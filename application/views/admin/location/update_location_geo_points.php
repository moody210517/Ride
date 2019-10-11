<?php 
$this->load->view(ADMIN_ENC_URL.'/templates/header.php');
?>

<style>
#map 
{ 
height: 800px; 
}

#errorBox {
    color: red;
    font-size: 14px;
    font-weight: bold;
}
</style>
<script src="js/map/jsts.min.js"></script>
<div id="content" class="admin-settings">
		<div class="grid_container">
			<div class="grid_12">
				<div class="widget_wrap">
					<div class="widget_top">
						<span class="h_icon list"></span>
						<h6><?php echo $heading; ?></h6>
					</div>
					<div class="widget_content chenge-pass-base">
					<?php 
						$attributes = array('class' => 'form_container left_label', 'id' => 'addeditlocation_form');
						echo form_open(ADMIN_ENC_URL.'/location/updateLocationBoundary',$attributes) 
					?> 		
	 						<ul class="leftsec-contsec">
                                <li>
									<div class="form_grid_12">
										<div id="map" style="height:500px"></div>
									</div>
								</li>
								
							</ul>	
							<ul class="last-btn-submit">	
							<input type="hidden" name="boundayVal" id="boundayVal" value=""/>
								<input type="hidden" name="location_id" value="<?php if($form_mode){ echo $locationdetails->row()->_id; } ?>"/>
								<li>
									<div class="form_grid_12">
										<div class="form_input">
                                            <p id="errorBox" class="error_cls"></p> 
											<button type="button" onclick="validate_polylines();" class="btn_small btn_blue" ><span><?php if ($this->lang->line('admin_common_update') != '') echo stripslashes($this->lang->line('admin_common_update')); else echo 'Update'; ?></span></button>
										</div>
									</div>
								</li>
							</ul>
						</form>
					</div>
				</div>
			</div>
		</div>
		<span class="clear"></span>
</div>
</div>
<input type="hidden" id="poly_intersection_count" value="0" />

<?php

$oldcoordinatesArr = array();
if(isset($locationdetails->row()->loc)){
	$oldcoordinatesArr = $locationdetails->row()->loc['coordinates'][0];
	
	$noco = array();
	foreach($oldcoordinatesArr as $key=>$val){
		$noco[] = array_reverse($val);
	}
	$oldcoordinatesArr = $noco;
	#print_r($oldcoordinatesArr); die;
	unset($oldcoordinatesArr[count($oldcoordinatesArr)-1]);
}
$map_radius = 10;
if(isset($locationdetails->row()->map_searching_radius)){
	$map_radius = intval($locationdetails->row()->map_searching_radius);
}
$map_language = "";
if($langCode!=''){
	$map_language = '&language='.$langCode;
}

?>
<script src="https://maps.googleapis.com/maps/api/js?sensor=false&libraries=drawing&key=<?php echo $this->config->item('google_maps_api_key');?><?php echo $map_language; ?>"></script>
<script type="text/javascript">
var Lat = <?php echo $locationdetails->row()->location['lat']; ?>;
var Long = <?php echo $locationdetails->row()->location['lng']; ?>;

var lat_longs = new Array();
var markers = new Array();
var drawingManager;

function initialize() {
	var myLatlng = new google.maps.LatLng(Lat, Long);
	var map_options = {
            zoom: 10,
            center: myLatlng,
            mapTypeId: google.maps.MapTypeId.ROADMAP
            };
    map = new google.maps.Map( document.getElementById('map'), map_options );
    
    
     var infowindow = new google.maps.InfoWindow({
      size: new google.maps.Size(150, 50)
    });
    
    <?php if(!empty($other_loc_lists)){ 
        foreach($other_loc_lists as $loc_row){
        $loc_coordinates = json_encode($loc_row['coordinates']);
        $disp_status = get_language_value_for_keyword($loc_row['status'],$this->data['langCode']);
        if($loc_row['status'] == 'Active'){
            $disp_status = '<span style=\'color:green;\'>'.$disp_status.'</span>';
        } else {
            $disp_status = '<span style=\'color:red;\'>'.$disp_status.'</span>';
        }
    ?>
    
    var otherLocPolyLine = new google.maps.Polygon({
      paths: <?php echo $loc_coordinates; ?>,
      strokeColor: '#3399FF',
      strokeOpacity: 0.8,
      strokeWeight: 2,
      fillColor: '#3399FF',
      fillOpacity: 0.35,
      draggable: false,
    });
    otherLocPolyLine.setMap(map);
    //overlayClickListener(otherLocPolyLine);
    otherLocPolyLine.addListener('click', function(event) {
        var contentString = "<b><?php echo $loc_row['location_name']; ?></b><br/><b><?php if ($this->lang->line('admin_subadmin_status') != '') echo stripslashes($this->lang->line('admin_subadmin_status')); else echo 'Status'; ?> : <?php echo $disp_status; ?></b><br/>";
        infowindow.setContent(contentString);
        infowindow.setPosition(event.latLng);
        infowindow.open(map);
    });
    <?php 
       }
    } ?>
	
}

function overlayClickListener(overlay) {
    google.maps.event.addListener(overlay, "mouseup", function(event){
        $('#boundayVal').val(overlay.getPath().getArray());
    });
}
function getPoints() {
    $('#boundayVal').val(google.maps.getPath().getArray());
}

function create_polygon(coordinates) {
    var icon = {
        path: google.maps.SymbolPath.CIRCLE,
        //path: "M -1 -1 L 1 -1 L 1 1 L -1 1 z",
        strokeColor: "#3399FF",
        strokeOpacity: 0,
        fillColor: "#FFFFFF",
        fillOpacity: 1,
        scale: 3
    };

    var polygon = new google.maps.Polygon({
        map: map,
        paths: coordinates,
        strokeColor: "#3399FF",
        strokeOpacity: 0.8,
        strokeWeight: 2,
        fillColor: "#3399FF",
        fillOpacity: 0.5,
        zIndex: -1
    });

    var marker_options = {
        map: map,
        icon: icon,
        flat: true,
        draggable: true,
        raiseOnDrag: false
    };
    
    for (var i=0; i<coordinates.length; i++){
        marker_options.position = coordinates[i];
        var point = new google.maps.Marker(marker_options);
        
        google.maps.event.addListener(point, "drag", update_polygon_closure(polygon, i));
		
		google.maps.event.addListener(point, "mouseup", function(event){
			$('#boundayVal').val(polygon.getPath().getArray());
            
            var intersection_count = 0;
            /* <?php if(!empty($other_loc_lists)){ 
                foreach($other_loc_lists as $loc_row){
                $loc_coordinates = json_encode($loc_row['coordinates']);
            ?>
            
            var otherLocPolyLine = new google.maps.Polygon({
              paths: <?php echo $loc_coordinates; ?>,
              strokeColor: '#FF0000',
              strokeOpacity: 0.8,
              strokeWeight: 3,
              fillColor: '#FF0000',
              fillOpacity: 0.35,
              draggable: false,
            });
            
            var geometryFactory = new jsts.geom.GeometryFactory();
            var polygon1 = createJstsPolygon(geometryFactory, polygon);
            var polygon2 = createJstsPolygon(geometryFactory, otherLocPolyLine);
            var intersection = polygon1.intersection(polygon2); 
            
            var coords = intersection.getCoordinates().map(function (coord) {
                return { lat: coord.x, lng: coord.y };
            });
            if(coords.length > 0){
                intersection_count++;
            }
            <?php 
               } 
            } ?> */
            
            
            $('#poly_intersection_count').val(intersection_count);
            if(intersection_count == 0){
                $('#errorBox').html('');
            } else { 
                if(intersection_count > 0){ 
                    $('#errorBox').html('<?php if ($this->lang->line('loc_border_overalpping') != '') echo stripslashes($this->lang->line('loc_border_overalpping')); else echo 'Drawn location border is overlapping with other locations'; ?>');
                } 
            }
            
		});
    }
    
    function update_polygon_closure(polygon, i){
		$('#boundayVal').val(polygon.getPath().getArray());
        return function(event){
           polygon.getPath().setAt(i, event.latLng); 
        }
    }
	
};

initialize();

var corners = <?php make_coordinates($locationdetails->row()->location['lat'],$locationdetails->row()->location['lng'],'',$oldcoordinatesArr,$map_radius); ?>;


//var corners = [[13.077639,80.249119],[13.095864,80.24929],[13.108069,80.267143],[13.096199,80.282593],[13.07697,80.282421],[13.06861,80.266972]];
var coordinates = [];

for (var i=0; i<corners.length; i++){
	var position = new google.maps.LatLng(corners[i][0], corners[i][1]);
	coordinates.push(position);
}

create_polygon(coordinates);
var Loc_coordinates = coordinates;
</script>
<?php
$coordinatesArr = array();
function make_coordinates($latpoint,$lngpoint,$t='',$oldcoordinatesArr,$map_radius=10){
	for($i=1;$i<=16;$i++){
		$coordinatesArr[] = get_gps_distance($latpoint,$lngpoint,$map_radius,22.5*$i,$t);
	}
	if(!empty($oldcoordinatesArr)){
		$coordinatesArr = $oldcoordinatesArr;
	}
	echo json_encode($coordinatesArr);
}

function get_gps_distance($lat1,$long1,$d,$angle,$type=''){
    # Earth Radious in KM
    $R = 6378.14;

    # Degree to Radian
    $latitude1 = $lat1 * (M_PI/180);
    $longitude1 = $long1 * (M_PI/180);
    $brng = $angle * (M_PI/180);

    $latitude2 = asin(sin($latitude1)*cos($d/$R) + cos($latitude1)*sin($d/$R)*cos($brng));
    $longitude2 = $longitude1 + atan2(sin($brng)*sin($d/$R)*cos($latitude1),cos($d/$R)-sin($latitude1)*sin($latitude2));

    # back to degrees
    $latitude2 = $latitude2 * (180/M_PI);
    $longitude2 = $longitude2 * (180/M_PI);

    # 6 decimal for Leaflet and other system compatibility
   $lat2 = round ($latitude2,6);
   $long2 = round ($longitude2,6);

   // Push in array and get back
   $tab[0] = $lat2;
   $tab[1] = $long2;
   if($type==''){
	return $tab;
   }else{
	return @implode('|',$tab);
   }
}
?>


<script>



function validate_polylines(){
    if($('#addeditlocation_form').valid()){
        var intersection_count = $('#poly_intersection_count').val();
        if(intersection_count > 0){
            $('#errorBox').html('<?php if ($this->lang->line('loc_border_overalpping') != '') echo stripslashes($this->lang->line('loc_border_overalpping')); else echo 'Drawn location border is overlapping with other locations'; ?>');
        } else {
            $('#errorBox').html('');
            $('#addeditlocation_form').submit();
        }
    } 
}

function createJstsPolygon(geometryFactory, polygon) {
  var path = polygon.getPath();
  var coordinates = path.getArray().map(function name(coord) {
    return new jsts.geom.Coordinate(coord.lat(), coord.lng());
  });
  if(coordinates[0].compareTo(coordinates[coordinates.length-1]) != 0) 
      coordinates.push(coordinates[0]);
  var shell = geometryFactory.createLinearRing(coordinates);
  return geometryFactory.createPolygon(shell);
}


/* check_polygon_borders(Loc_coordinates); */

function check_polygon_borders(coordinates){ 
    
    var polygon = new google.maps.Polygon({
      paths: coordinates,
      strokeColor: '#3399FF',
      strokeOpacity: 0.8,
      strokeWeight: 3,
      fillColor: '#3399FF',
      fillOpacity: 0.35,
      draggable: false,
    }); console.log(coordinates);
    
    var intersection_count = 0;
    <?php if(!empty($other_loc_lists)){ 
        foreach($other_loc_lists as $loc_row){
        $loc_coordinates = json_encode($loc_row['coordinates']);
    ?>
    
    var otherLocPolyLine = new google.maps.Polygon({
      paths: <?php echo $loc_coordinates; ?>,
      strokeColor: '#FF0000',
      strokeOpacity: 0.8,
      strokeWeight: 3,
      fillColor: '#FF0000',
      fillOpacity: 0.35,
      draggable: false,
    });
    
    var geometryFactory = new jsts.geom.GeometryFactory();
    var polygon1 = createJstsPolygon(geometryFactory, polygon);
    var polygon2 = createJstsPolygon(geometryFactory, otherLocPolyLine);
    var intersection = polygon1.intersection(polygon2); 
    
    var coords = intersection.getCoordinates().map(function (coord) {
        return { lat: coord.x, lng: coord.y };
    });
    if(coords.length > 0){
        intersection_count++;
    }
    <?php 
       } 
    } ?>
    
    
    $('#poly_intersection_count').val(intersection_count);
    if(intersection_count == 0){
        $('#errorBox').html('');
    } else { 
        if(intersection_count > 0){ 
            $('#errorBox').html('<?php if ($this->lang->line('loc_border_overalpping') != '') echo stripslashes($this->lang->line('loc_border_overalpping')); else echo 'Drawn location border is overlapping with other locations'; ?>');
        } 
    }
    
}


            
</script>

<?php 
$this->load->view(ADMIN_ENC_URL.'/templates/footer.php');
?>
