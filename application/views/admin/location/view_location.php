<?php
$this->load->view(ADMIN_ENC_URL.'/templates/header.php');
?>
<style>
#map 
{ 
height: 500px; 
}
</style>
<script type='text/javascript'>
$(function(){
	$('#map_tab').bind('click',function() {
            var w = $('#tab3').width();
            var h = $('#tab3').height();
            $('#map_canvas').css({ width: w, height: h });
			var center = map.getCenter();
           google.maps.event.trigger(map, 'resize');
		   map.setCenter(center); 
	});
});
</script>
<link href="css/admin_custom.css" rel="stylesheet" type="text/css" media="screen">
<div id="content" class="admin-settings">
	<div class="grid_container">
		<div class="grid_12">
			<div class="widget_wrap">
				<div class="widget_top">
					<span class="h_icon list"></span>
					<h6><?php echo $heading; ?></h6>
					<div id="widget_tab">
						<ul>
							<li><a href="#tab1" class="active_tab"><?php if ($this->lang->line('admin_location_and_fare_location_details') != '') echo stripslashes($this->lang->line('admin_location_and_fare_location_details')); else echo 'Location Details'; ?></a></li>
							<li><a href="#tab2"><?php if ($this->lang->line('admin_location_and_fare_fare_details') != '') echo stripslashes($this->lang->line('admin_location_and_fare_fare_details')); else echo 'Fare Details'; ?></a></li>
							<li><a href="#tab3" id='map_tab'><?php if ($this->lang->line('admin_location_and_fare_view_map') != '') echo stripslashes($this->lang->line('admin_location_and_fare_view_map')); else echo 'View Map'; ?></a></li>
						</ul>
					</div>
				</div>
				<div class="widget_content chenge-pass-base">
					<?php 
						$attributes = array('class' => 'form_container left_label');
						echo form_open(ADMIN_ENC_URL,$attributes) 
					?>
					<div id="tab1">
	 					<ul class="leftsec-contsec">	 						
							<li>
								<div class="form_grid_12">
									<label class="field_title"><?php if ($this->lang->line('admin_location_and_fare_location_location') != '') echo stripslashes($this->lang->line('admin_location_and_fare_location_location')); else echo 'Location'; ?></label>
									<div class="form_input">
										<?php if(isset($location_details->row()->city)){echo $location_details->row()->city; }else{ echo 'Not available'; } ?>
									</div>
								</div>
							</li>						
							<li>
								<div class="form_grid_12">
									<label class="field_title"><?php if ($this->lang->line('admin_drivers_country') != '') echo stripslashes($this->lang->line('admin_drivers_country')); else echo 'Country'; ?></label>
									<div class="form_input">
										<?php if(isset($location_details->row()->country)){echo $location_details->row()->country['name']; }else{ echo 'Not available'; } ?>
									</div>
								</div>
							</li>					
							<li>
								<div class="form_grid_12">
									<label class="field_title"><?php if ($this->lang->line('admin_location_and_fare_commission_to_site') != '') echo stripslashes($this->lang->line('admin_location_and_fare_commission_to_site')); else echo 'Commission to site'; ?></label>
									<div class="form_input">
										<?php if(isset($location_details->row()->site_commission)){echo $location_details->row()->site_commission; }else{ echo 'Not available'; } ?> %
									</div>
								</div>
							</li>						
							<li>
								<div class="form_grid_12">
									<label class="field_title"><?php if ($this->lang->line('admin_subadmin_status') != '') echo stripslashes($this->lang->line('admin_subadmin_status')); else echo 'Status'; ?></label>
									<div class="form_input">
										<?php if(isset($location_details->row()->status)){ echo  
                                        get_language_value_for_keyword($location_details->row()->status,$this->data['langCode']);
                                        
                                        }else{ echo get_language_value_for_keyword('Not Available',$this->data['langCode']); } ?>
									</div>
								</div>
							</li>					
							<li>
								<div class="form_grid_12">
									<label class="field_title"><?php if ($this->lang->line('admin_location_and_fare_location_available_category') != '') echo stripslashes($this->lang->line('admin_location_and_fare_location_available_category')); else echo 'Available category'; ?></label>
									<div class="form_input">
										<?php if(isset($availableCategory)){echo @implode($availableCategory,', '); }else{ echo 'Not available'; } ?>
									</div>
								</div>
							</li>
						</ul>
					</div>
					<div id="tab2">
						<?php if(!empty($availableCategory)){ ?>
						<ul class="leftsec-contsec">
						<table class="custom-table">
							<thead>
								<tr>
									<th><?php if ($this->lang->line('admin_location_and_fare_category') != '') echo stripslashes($this->lang->line('admin_location_and_fare_category')); else echo 'Category'; ?></th>
									<th><?php if ($this->lang->line('admin_location_and_fare_minimum_bill') != '') echo stripslashes($this->lang->line('admin_location_and_fare_minimum_bill')); else echo 'Minimum Bill'; ?></th>
									<th><?php if ($this->lang->line('admin_location_and_fare_after_minimum_bill') != '') echo stripslashes($this->lang->line('admin_location_and_fare_after_minimum_bill')); else echo 'After Minimum Bill'; ?></th>
									<th><?php if ($this->lang->line('admin_location_and_fare_waiting_charge') != '') echo stripslashes($this->lang->line('admin_location_and_fare_waiting_charge')); else echo 'Waiting Charge'; ?></th>
									<th><?php if ($this->lang->line('admin_location_and_fare_extra_charges') != '') echo stripslashes($this->lang->line('admin_location_and_fare_extra_charges')); else echo 'Extra charges'; ?></th>
								</tr>
							</thead>
							<tbody>
								<?php foreach($availableCategory as $key=>$row){ ?>
								<tr>
									<td><?php echo $row; ?></td>
									<td>
									<?php echo $dcurrencySymbol.' '; ?> <?php if(isset($location_details->row()->fare[$key]['min_fare'])){echo $location_details->row()->fare[$key]['min_fare']; } ?> 
                                    <?php if(isset($location_details->row()->fare[$key]['admin_for_first'])){echo $location_details->row()->fare[$key]['admin_for_first']; } ?> <?php if ($this->lang->line('admin_for_first') != '') echo stripslashes($this->lang->line('admin_for_first')); else echo 'for first'; ?>
                                    <?php if(isset($location_details->row()->fare[$key]['min_km'])){echo $location_details->row()->fare[$key]['min_km']; } ?> 
                                    <?php echo get_language_value_for_keyword($d_distance_unit,$this->data['langCode']); ?>
									</td>
									<td>
									<?php echo $dcurrencySymbol.' '; ?> <?php if(isset($location_details->row()->fare[$key]['per_km'])){echo $location_details->row()->fare[$key]['per_km']; } ?> <?php if ($this->lang->line('ride_per') != '') echo stripslashes($this->lang->line('ride_per')); else echo 'Per'; ?>  <?php echo get_language_value_for_keyword($d_distance_unit,$this->data['langCode']); ?>
                                    
									<br/>
									<?php echo $dcurrencySymbol.' '; ?> <?php if(isset($location_details->row()->fare[$key]['per_minute'])){echo $location_details->row()->fare[$key]['per_minute']; } ?> 
                                    <?php if ($this->lang->line('admin_location_and_fare_fare_per_minitue') != '') echo stripslashes($this->lang->line('admin_location_and_fare_fare_per_minitue')); else echo 'Fare per min (Ride time charges)'; ?>
                                    
									</td>
									<td>
									<?php echo $dcurrencySymbol.' '; ?><?php if(isset($location_details->row()->fare[$key]['wait_per_minute'])){ if($location_details->row()->fare[$key]['wait_per_minute']>0){ echo $location_details->row()->fare[$key]['wait_per_minute'];}else{ echo "N/A"; } } ?>  
                                    <?php if ($this->lang->line('ride_per_min') != '') echo stripslashes($this->lang->line('ride_per_min')); else echo 'Per Min'; ?> 
                                    
									<br/>
									</td>
									<td>
									<?php if(isset($location_details->row()->peak_time)){if($location_details->row()->peak_time=='Yes'){ ?> <?php if(isset($location_details->row()->fare[$key]['peak_time_charge'])){ if($location_details->row()->fare[$key]['peak_time_charge']>0){ echo $location_details->row()->fare[$key]['peak_time_charge'].'X';}else{ echo "N/A"; } }}}else{ echo 'N/A'; } ?> ( <?php if ($this->lang->line('admin_location_and_fare_peak_time_surcharge') != '') echo stripslashes($this->lang->line('admin_location_and_fare_peak_time_surcharge')); else echo 'Peak Time Surcharge'; ?> )
                                    
                                    
									<br/>
									<?php  if(isset($location_details->row()->night_charge)){if($location_details->row()->night_charge=='Yes'){ ?> <?php if(isset($location_details->row()->fare[$key]['night_charge'])){ if($location_details->row()->fare[$key]['night_charge']>0){ echo $location_details->row()->fare[$key]['night_charge'].'X';}else{ echo "N/A"; } } }else{ echo 'N/A'; }}else{ echo 'N/A'; } ?> (<?php if ($this->lang->line('admin_location_and_fare_night_charge') != '') echo stripslashes($this->lang->line('admin_location_and_fare_night_charge')); else echo 'Night charges'; ?>)
                                    
                                    
									</td>
								</tr>
								<?php } ?>
							</tbody>
						</table>
						</ul>
						<?php }else{ ?>
							<?php if ($this->lang->line('admin_location_and_fare_informations_are_not_availble') != '') echo stripslashes($this->lang->line('admin_location_and_fare_informations_are_not_availble')); else echo 'informations are not available'; ?>
						<?php } ?>
					</div>
					<div id="tab3">
					<ul class="leftsec-contsec">
						<?php #echo $map['js']; ?>
						<?php #echo $map['html']; ?>
						<div id='map' style="width:100%"></div>
					</ul>	
					</div>
					
						<ul class="last-btn-submit">
							<li>
								<div class="form_grid_12">
									<div class="form_input">
										<a href="<?php echo ADMIN_ENC_URL;?>/location/display_location_list" class="tipLeft" title="<?php if ($this->lang->line('admin_go_to_location') != '') echo stripslashes($this->lang->line('admin_go_to_location')); else echo 'Go to location list'; ?>"><span class="badge_style b_done btn-theme"><?php if ($this->lang->line('admin_location_and_fare_back') != '') echo stripslashes($this->lang->line('admin_location_and_fare_back')); else echo 'Back'; ?></span></a>
									</div>
								</div>
							</li>
						</ul>
				</div>
			</div>
		</div>
		<span class="clear"></span>
	</div>
</div>
</div>
<?php
$oldcoordinatesArr = array();
if(isset($location_details->row()->loc)){
	$oldcoordinatesArr = $location_details->row()->loc['coordinates'][0];
	
	$noco = array();
	foreach($oldcoordinatesArr as $key=>$val){
		$noco[] = array_reverse($val);
	}
	$oldcoordinatesArr = $noco;
	#print_r($oldcoordinatesArr); die;
	unset($oldcoordinatesArr[count($oldcoordinatesArr)-1]);
}
$map_radius = 10;
if(isset($location_details->row()->map_searching_radius)){
	$map_radius = intval($location_details->row()->map_searching_radius);
}
$map_language = "";
if($langCode!=''){
	$map_language = '&language='.$langCode;
}
?>
<script src="https://maps.googleapis.com/maps/api/js?sensor=false&libraries=drawing&key=<?php echo $this->config->item('google_maps_api_key');?><?php echo $map_language; ?>"></script>
<script type="text/javascript">
var Lat = <?php echo $location_details->row()->location['lat']; ?>;
var Long = <?php echo $location_details->row()->location['lng']; ?>;
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
        draggable: false,
        raiseOnDrag: false
    };
    
    for (var i=0; i<coordinates.length; i++){
        marker_options.position = coordinates[i];
        var point = new google.maps.Marker(marker_options);
    }
};

initialize();

var corners = <?php make_coordinates($location_details->row()->location['lat'],$location_details->row()->location['lng'],'',$oldcoordinatesArr,$map_radius); ?>;


//var corners = [[13.077639,80.249119],[13.095864,80.24929],[13.108069,80.267143],[13.096199,80.282593],[13.07697,80.282421],[13.06861,80.266972]];
var coordinates = [];

for (var i=0; i<corners.length; i++){
	var position = new google.maps.LatLng(corners[i][0], corners[i][1]);
	coordinates.push(position);
}
create_polygon(coordinates);
</script>
<?php
$coordinatesArr = array();
function make_coordinates($latpoint,$lngpoint,$t='',$oldcoordinatesArr,$map_radius=10){
	for($i=1;$i<=8;$i++){
		$coordinatesArr[] = get_gps_distance($latpoint,$lngpoint,$map_radius,45*$i,$t);
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
<?php 
$this->load->view(ADMIN_ENC_URL.'/templates/footer.php');
?>