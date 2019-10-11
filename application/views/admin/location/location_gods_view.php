<!DOCTYPE html PUBLIC"-//W3C//DTD XHTML 1.0 Strict//EN"
"http://www.w3.org/TR/xhtml1/DTD/xhtml1-strict.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
<meta http-equiv="content-type" content="text/html;charset=utf-8" />
<meta name="viewport" content="width=1200" /> 
<title><?php echo $heading.' - '.$title;?></title>
<link rel="shortcut icon" type="image/x-icon" href="<?php echo base_url();?>images/logo/<?php echo $favicon;?>">


<base href="<?php echo base_url(); ?>">

    <link rel="stylesheet" href="css/site/bootstrap.min.css"type="text/css" />
    <link rel="stylesheet" href="css/site/track/style.css">
    <script type="text/javascript" src="js/site/jquery-3.2.1.min.js"></script>
    <script type="text/javascript" src="js/site/bootstrap.min.js"></script>

<style>
#map 
{ 
height: 500px; 
}
.serach_area_input{
    width: 100%;
    height: 35px;
}
.box {
    border:solid 1px #000;
    margin-right:5px;
    padding-right: 10px;
}
.box-p {
    width: 50%;
    float: left;
}
label {
	cursor:pointer;
}
</style>
</head>


<?php 

$map_language = "";
if($langCode!=''){
	$map_language = '&language='.$langCode;
}

$map_center_lat = $this->config->item('latitude');
$map_center_lng = $this->config->item('longitude');
$map_zoom = 6;

$serach_lat = $this->input->get('lat');
$serach_lng = $this->input->get('lng');
$user_type=$this->input->get('user_type');
$user_array=explode(',',$user_type);
$search_area = $this->input->get('search_area');
$serachStatus = FALSE;
if($serach_lat != '' && $serach_lng != '' && $search_area != ''){
    $map_center_lat = $serach_lat;
    $map_center_lng = $serach_lng;
    $map_zoom = 12;
    $serachStatus = TRUE;
}
?>

<body>
<section>
    <div class="full-width map-main">
        <div id="map" style="width:100%;height:100%;"></div>
        <div class="cab-arrive" style="width: 35%; top: 10px;">
            <div class="full-width cab-top">
                <div class="cab-top-left">
                    <h2><a href="<?php echo base_url(); ?>" class="product-logo-wrap-desk" target="_blank"><img src="images/logo/<?php echo $this->config->item('logo_image'); ?>" alt="<?php echo $this->config->item('email_title'); ?>"></a></h2>
                </div>
            </div>
            <div class="full-width cab-middle">
                <p class="box-p">
                <input type="checkbox" name="user_type" id="user_filter" value="user" class="box user_type" <?php if(in_array('user',$user_array)) { echo "checked='checked'";} ?> />
				<label for="user_filter">
                    <?php if ($this->lang->line('admin_menu_users') != '') 
                        echo stripslashes($this->lang->line('admin_menu_users')); 
                    else 
                        echo 'Users'; ?>
				</label>
                </p>
                <p class="box-p">
               
				<input type="checkbox" name="user_type" value="driver" id="driver_filter" class="box user_type" <?php if(in_array('driver',$user_array)) { echo "checked='checked'";} ?>/>
				<label for="driver_filter">
                    <?php if ($this->lang->line('admin_dashboard_drivers') != '') 
                        echo stripslashes($this->lang->line('admin_dashboard_drivers')); 
                    else 
                        echo 'Drivers'; ?>
				</label>
						
                </p>
                
            </div>
           
            
            <div class="full-width cab-bottom">
                    <input type="text" value="<?php echo $search_area; ?>" name="search_area" id="search_area" class="serach_area_input" placeholder="<?php if ($this->lang->line('admin_location_search') != '') echo stripslashes($this->lang->line('admin_location_search')); else echo 'Search locations'; ?>..." />
                    <input type="hidden" name="lat" id="lat" value="<?php if(isset($serach_lat)) echo $serach_lat; ?>"/>
                    <input type="hidden" name="lng" id="lng" value="<?php if(isset($serach_lng)) echo $serach_lng; ?>"/>
                    <input type="hidden" name="search_area" id="search_area" value="<?php if(isset($search_area)) echo $search_area; ?>"/>
                    <a href="<?php echo ADMIN_ENC_URL;?>/location/gods_view" class="search_remove">x</a>
                </form>
			</div>
            
        </div>
    </div>
</section>



<script src="https://maps.googleapis.com/maps/api/js?sensor=false&libraries=drawing,places&key=<?php echo $this->config->item('google_maps_api_key');?><?php echo $map_language; ?>"></script>
<script type="text/javascript">
var Lat = <?php echo $map_center_lat; ?>;
var Long = <?php echo $map_center_lng; ?>;
var lat_longs = new Array();
var markers = new Array();
var drawingManager;
var search_area;
var zoomLevel;
var lat;
var lng; 
var user_marker = {
		url: '<?php echo base_url(); ?>images/user_marker.png',
		// This marker is 20 pixels wide by 32 pixels high.
		scaledSize: new google.maps.Size(25, 32),
		 origin: new google.maps.Point(0, 0),
    // The anchor for this image is the base of the flagpole at (0, 32).
		anchor: new google.maps.Point(0, 0)
	   
};
var driver_marker = {
url: '<?php echo base_url(); ?>images/driver_marker.png',
// This marker is 20 pixels wide by 32 pixels high.
scaledSize: new google.maps.Size(25, 32),
 origin: new google.maps.Point(0, 0),
// The anchor for this image is the base of the flagpole at (0, 32).
anchor: new google.maps.Point(0, 0)

};
function initialize() {
	
	var myLatlng = new google.maps.LatLng(Lat, Long);
	var map_options = {
            zoom: <?php echo $map_zoom; ?>,
			styles: [{"elementType":"geometry","stylers":[{"color":"#f5f5f5"}]},{"elementType":"labels.icon","stylers":[{"visibility":"off"}]},{"elementType":"labels.text.fill","stylers":[{"color":"#616161"}]},{"elementType":"labels.text.stroke","stylers":[{"color":"#f5f5f5"}]},{"featureType":"administrative.land_parcel","elementType":"labels.text.fill","stylers":[{"color":"#bdbdbd"}]},{"featureType":"poi","elementType":"geometry","stylers":[{"color":"#eeeeee"}]},{"featureType":"poi","elementType":"labels.text.fill","stylers":[{"color":"#757575"}]},{"featureType":"poi.park","elementType":"geometry","stylers":[{"color":"#e5e5e5"}]},{"featureType":"poi.park","elementType":"labels.text.fill","stylers":[{"color":"#9e9e9e"}]},{"featureType":"road","elementType":"geometry","stylers":[{"color":"#ffffff"}]},{"featureType":"road.arterial","elementType":"labels.text.fill","stylers":[{"color":"#757575"}]},{"featureType":"road.highway","elementType":"geometry","stylers":[{"color":"#dadada"}]},{"featureType":"road.highway","elementType":"labels.text.fill","stylers":[{"color":"#616161"}]},{"featureType":"road.local","elementType":"labels.text.fill","stylers":[{"color":"#9e9e9e"}]},{"featureType":"transit.line","elementType":"geometry","stylers":[{"color":"#e5e5e5"}]},{"featureType":"transit.station","elementType":"geometry","stylers":[{"color":"#eeeeee"}]},{"featureType":"water","elementType":"geometry","stylers":[{"color":"#c9c9c9"}]},{"featureType":"water","elementType":"labels.text.fill","stylers":[{"color":"#9e9e9e"}]}],
            center: myLatlng,
            mapTypeId: google.maps.MapTypeId.ROADMAP,
            mapTypeControl: true,
            mapTypeControlOptions: {
                  style: google.maps.MapTypeControlStyle.HORIZONTAL_BAR,
                  position: google.maps.ControlPosition.TOP_CENTER
           },
        };
    map = new google.maps.Map( document.getElementById('map'), map_options );
    
    var infowindow = new google.maps.InfoWindow({
      size: new google.maps.Size(150, 50)
    });
    
    <?php if(!empty($locationsCoordsList)){ 
        foreach($locationsCoordsList as $loc_row){
        $loc_coordinates = json_encode($loc_row['coordinates']);
    ?>
    
    var locPolyLine = new google.maps.Polygon({
      paths: <?php echo $loc_coordinates; ?>,
      strokeColor: '#3399FF',
      strokeOpacity: 0.8,
      strokeWeight: 3,
      fillColor: '#3399FF',
      fillOpacity: 0.25,
      draggable: false,
      zIndex: -1
    });
    locPolyLine.setMap(map); 
   
	
    locPolyLine.addListener('click', function(event) {
        var contentString = "<b><span style='color:green;'><?php echo $loc_row['city_name']; ?></span></b><p><a href='<?php echo ADMIN_ENC_URL;?>/location/update_location_geo_points/<?php echo $loc_row['location_id']; ?>' target='_blank'><?php if ($this->lang->line('admin_location_and_fare_update_boundary') != '') echo stripslashes($this->lang->line('admin_location_and_fare_update_boundary')); else echo 'Update boundary'; ?></a></p>";
        infowindow.setContent(contentString);
        infowindow.setPosition(event.latLng);
        infowindow.open(map);
    });
    
    
   
    
    <?php 
       }
    } ?>
    
    
    var autocomplete = new google.maps.places.Autocomplete((document.getElementById('search_area')), {});

    google.maps.event.addListener(autocomplete, 'place_changed', function() {
        var place = autocomplete.getPlace();
        var search_area = $('#search_area').val();
        var lat = place.geometry.location.lat();
        var lng = place.geometry.location.lng(); 
        if(lat != '' && lng != '' && search_area != ''){
            $('#lat').val(lat); 
            $('#lng').val(lng);
            $('#search_area').val(search_area);
            			
			user_type='';
			$('input[name="user_type"]:checked').each(function() {
				user_type+=this.value+',';
			});
			
			window.location.href="<?php echo ADMIN_ENC_URL;?>/location/gods_view?user_type="+user_type+"&search_area="+search_area+"&lat="+lat+"&lng="+lng;
        }
    });
    
    <?php if($serachStatus){ ?>
    var marker = new google.maps.Marker({
      position: {lat: <?php echo $serach_lat; ?>, lng: <?php echo $serach_lng; ?>},
      map: map,
      title: '<?php echo $search_area; ?>'
    });
    
    marker.addListener('click', function(event) {
        var contentString = "<b><span><?php echo $search_area; ?></span></b>";
        infowindow.setContent(contentString);
        infowindow.setPosition(event.latLng);
        infowindow.open(map);
    });
    
    <?php } ?>
		
	<?php 
		if(isset($usersList)) {
			foreach($usersList->result() as $data) {
	?>
		<?php  if(isset($data->loc['lat'])&& isset($data->loc['lon']) && $data->loc['lat']!='' && $data->loc['lon']!='')  {?>
		var marker = new google.maps.Marker({
		  position: {lat: <?php echo $data->loc['lat']; ?>, lng: <?php echo $data->loc['lon']; ?>},
		  map: map,
		  icon:user_marker,
		  title: '<?php echo $data->user_name; ?>'
		});
		
		
	<?php } }} ?>
	<?php 
			if(isset($driverList)) {
			foreach($driverList->result() as $data) {
			
	?>
		<?php  if(isset($data->loc['lat']) && isset($data->loc['lon']) && $data->loc['lat']!='' && $data->loc['lon']!='')  {?>
		var marker = new google.maps.Marker({
		  position: {lat: <?php echo $data->loc['lat']; ?>, lng: <?php echo $data->loc['lon']; ?>},
		  map: map,
		  icon:driver_marker,
		  title: '<?php echo $data->user_name; ?>'
		});
		
		
	<?php  }}} ?>
	
}

initialize();
google.maps.event.addListener(map, 'zoom_changed', function() {
    zoomLevel = map.getZoom();
	console.log(zoomLevel);
	
	//alert(zoomLevel);
    //this is where you will do your icon height and width change.     
});
</script>


<script>
	$(document).ready(function() {
    //set initial state.
    

		$('.user_type').change(function() {
			
			user_type='';
			$('input[name="user_type"]:checked').each(function() {
				user_type+=this.value+',';
			});
			search_area=$('#search_area').val();
			lat=$('#lat').val();
			lng=$('#lng').val();
			window.location.href="<?php echo ADMIN_ENC_URL;?>/location/gods_view?user_type="+user_type+"&search_area="+search_area+"&lat="+lat+"&lng="+lng;
			
			        
		});
	});
</script>

<style>
<?php if($serachStatus){ ?>
        .serach_area_input {
            width: 95%;
        }
        .search_remove {
            margin-left: 3px;
            color: #fff;
            font-weight:bold;
        }
    
<?php } else { ?>
    .search_remove {
        display:none;
    }
<?php } ?>
</style>
    </body>
</html>
