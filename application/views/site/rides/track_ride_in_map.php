<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
    <head>
        <meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
        <meta name="viewport" content="width=device-width, initial-scale=1.0" />
        <?php if ($sideMenu == 'share_code') { ?>
            <?php /* <meta property="og:site_name" content="<?php echo $this->config->item('email_title'); ?>"/> */ ?>
            <meta property="og:type" content="website"/>
            <meta property="og:url" content="<?php echo $_SERVER['HTTP_HOST'] . $_SERVER['REQUEST_URI']; ?>"/>
            <meta property="og:title" content="Signup with my code."/>
            <meta property="og:image" content="<?php echo base_url() . 'images/site/track/share.png'; ?>"/>
            <meta property="og:image:width" content="100" />
            <meta property="og:image:height" content="100" />
            <meta property="og:description" content="<?php echo $shareDesc; ?>"/>
        <?php } ?>
        <meta name="Title" content="<?php if ($meta_title == '') { echo $this->config->item('meta_title'); } else { echo $meta_title; } ?>" />  
        <base href="<?php echo base_url(); ?>" />
        <?php
        if ($this->config->item('google_verification')) {
            echo stripslashes($this->config->item('google_verification'));
        }
        if ($heading == '') { ?>    
            <title><?php echo $title; ?></title>
        <?php } else { ?>
            <title><?php echo $heading; ?></title>
        <?php } ?>

        <meta name="Title" content="<?php if ($meta_title == '') { echo $this->config->item('meta_title'); } else { echo $meta_title; } ?>" />
        <meta name="keywords" content="<?php if ($meta_keyword == '') { echo $this->config->item('meta_keyword'); } else { echo $meta_keyword; } ?>" />
        <meta name="description" content="<?php if ($meta_description == '') { echo $this->config->item('meta_description'); } else { echo $meta_description; } ?>" />
		<?php
		if (isset($meta_abstraction)){
		  if ($meta_abstraction == '') {
			  echo "<!-- " . $this->config->item('meta_abstraction') . " --><cmt>";
		  } else {
			  echo "<!-- " . $meta_abstraction . " --><cmt>";
		  }
		}
		?>
        <link rel="shortcut icon" type="image/x-icon" href="<?php echo base_url() . 'images/logo/' . $this->config->item('favicon_image'); ?>">    

		 <link rel="stylesheet" href="css/site/bootstrap.min.css"type="text/css" />
		<link rel="stylesheet" href="css/site/track/style.css">
		<script type="text/javascript" src="js/site/jquery-3.2.1.min.js"></script>
		<script type='text/javascript' src='js/strophe.js'></script>
       <script type='text/javascript' src='js/echobot.js'></script>
		<script type="text/javascript" src="js/site/bootstrap.min.js"></script>
		
		<?php $this->load->view('site/rides/chat.php'); ?>
		
	</head>
	<body>
		<div class="full-width mobile-section">
		<div class="full-width cab-bottom">
		<a href="<?php echo base_url(); ?>" class="product-logo-wrap" target="_blank"><img src="images/logo/<?php echo $this->config->item('logo_image'); ?>" alt="<?php echo $this->config->item('email_title'); ?>"></a>
		<a href="rider/login" target="_blank" class="sign-btn">
		<?php       if ($this->lang->line('signin') != '')
                        echo stripslashes($this->lang->line('signin'));
                    else
                        echo 'Sign In';
         ?>
		<?php echo $this->config->item('email_title'); ?></a>
		</div>
		</div>
		<section>
		<div class="full-width map-main">
			<div id="dvMap" style="width:100%;height:100%;"></div>
			<div class="cab-arrive">
				<div class="full-width cab-top">
					<div class="cab-top-left">
						<h2><?php if(isset($ride_info->row()->user['name'])) echo $ride_info->row()->user['name']?></h2>
						<span id='action_txt'><?php echo $track_msg; ?></span>
					</div>
					<!--<div class="cab-top-right">
						<h3>5</h3>
						min
					</div>-->
				</div>
				<div class="full-width cab-middle">
				<?php
					$driverStatus = FALSE;
					if(isset($ride_info->row()->driver)){ 
						if($ride_info->row()->driver['id'] != ''){
							$driverStatus = TRUE;
						}
					}
					if($driverStatus){
				?>
				
					<div class="customer-profile">
						<?php 
							$driver_image = USER_PROFILE_IMAGE_DEFAULT;
							if (isset($driver_info->image)) {
								if ($driver_info->image != '') {
									$driver_image = USER_PROFILE_IMAGE . $driver_info->image;
								}
							}
						?>
						<img src="<?php echo $driver_image; ?>">
						<div class="customer-rating">
							<span><?php if(isset($driver_info->avg_review)) echo number_format($driver_info->avg_review,1); else echo '0.0';?></span>
							<div class="star-raing"><img src="images/site/track/star.png"></div>
						</div>
					</div>
					<div class="car-type">
						<h4><?php echo $ride_info->row()->driver['name']?></h4>
						<h5>
						<span class="car-type-detail"><?php
						if(isset($ride_info->row()->pool_ride) && $ride_info->row()->pool_ride=="Yes"){
							$disp_cat = (string)$ride_info->row()->booking_information['service_type'];
						}else{
							if(isset($ride_info->row()->booking_information['service_type'])) {
								$lng_cat_name =  get_category_name_by_lang($ride_info->row()->booking_information['service_id'],$this->data['langCode']);
								if($lng_cat_name != '') $disp_cat =  $lng_cat_name; else $disp_cat =  $ride_info->row()->booking_information['service_type'];
							} else {
								$disp_cat =  get_language_value_for_keyword('Not Available',$this->data['langCode']); 
							}
						}
						echo $disp_cat;
						?></span>
						<span class="car-number-detail"><?php if(isset($ride_info->row()->driver['vehicle_no'])) echo $ride_info->row()->driver['vehicle_no']?></span>
						</h5>
					</div>
				<?php } else { ?>
				<div class="customer-profile cab-top-left"> <span><?php       if ($this->lang->line('driver_not_assigned') != '')
                        echo stripslashes($this->lang->line('driver_not_assigned'));
                    else
                        echo 'Driver not yet assigned';
				?> </span>
				</div>
				<?php } ?>
				</div>
				<div class="full-width cab-bottom">
				<a href="<?php echo base_url(); ?>" class="product-logo-wrap-desk" target="_blank"><img src="images/logo/<?php echo $this->config->item('logo_image'); ?>" alt="<?php echo $this->config->item('email_title'); ?>"></a>
				<a href="rider/login" target="_blank" class="sign-btn">
				<?php       if ($this->lang->line('signin') != '')
                        echo stripslashes($this->lang->line('signin'));
                    else
                        echo 'Sign In';
                ?>
				<?php echo $this->config->item('email_title'); ?></a>
				
				
				</div>
			</div>
		</div>
		

		
</section>

<script src="https://maps.googleapis.com/maps/api/js?libraries=geometry&key=<?php echo $this->config->item('google_maps_api_key'); ?>&language=<?php echo $this->data['langCode']; ?>"></script>
<script>
    var map;
    var directionsDisplay;
    var hit_count=0;
    
    var map_marker;
    var d=50;
    var step=50;
    var apiKey='<?php echo $this->config->item('google_maps_api_key'); ?>';
    var polylines = [];
    var snappedCoordinates = [];
    var lat = '13.1539174';
    var lng = '80.2275385';
	var m = '';
    var lineCoordinatesArray = new google.maps.MVCArray();
    var realtimeArray = new google.maps.MVCArray();
    var distanceArray = new google.maps.MVCArray();
    var  service = new google.maps.DirectionsService(),lineCoordinatesPath;
    var car = "M17.402,0H5.643C2.526,0,0,3.467,0,6.584v34.804c0,3.116,2.526,5.644,5.643,5.644h11.759c3.116,0,5.644-2.527,5.644-5.644 V6.584C23.044,3.467,20.518,0,17.402,0z M22.057,14.188v11.665l-2.729,0.351v-4.806L22.057,14.188z M20.625,10.773 c-1.016,3.9-2.219,8.51-2.219,8.51H4.638l-2.222-8.51C2.417,10.773,11.3,7.755,20.625,10.773z M3.748,21.713v4.492l-2.73-0.349 V14.502L3.748,21.713z M1.018,37.938V27.579l2.73,0.343v8.196L1.018,37.938z M2.575,40.882l2.218-3.336h13.771l2.219,3.336H2.575z M19.328,35.805v-7.872l2.729-0.355v10.048L19.328,35.805z";
	var icon = {
	  path: car,
	  scale: .7,
	  strokeColor: 'white',
	  strokeWeight: .10,
	  fillOpacity: 1,
	  rotation:0,
	  fillColor: 'blue',
	  offset: '50%',
	  // rotation: parseInt(heading[i]),
	  anchor: new google.maps.Point(10,25) // orig 10,50 back of car, 10,0 front of car, 10,25 center of car
	};	
  
   function initialize() {
      console.log("Google Maps Initialized")
      map = new google.maps.Map(document.getElementById('dvMap'), {
        zoom: 15,
        center: {lat: parseFloat(lat), lng : parseFloat(lng), alt: 0}
      });

    
	  
	  var trafficLayer = new google.maps.TrafficLayer();
	  
      //trafficLayer.setMap(map);
    }

    // moves the marker and center of map
    


    function pushCoordToArray(latIn, lngIn) {
      lineCoordinatesArray.push(new google.maps.LatLng(latIn, lngIn));
    }
    

 google.maps.event.addDomListener(window, 'load', initialize());  

</script>
<script>
var icons = {
			  start: new google.maps.MarkerImage(
			   // URL
			   '<?php echo base_url(); ?>images/pickup_marker.png',
			   // (width,height)
			   new google.maps.Size( 44, 32 ),
			   // The origin point (x,y)
			   new google.maps.Point( 0, 0 ),
			   // The anchor point (x,y)
			   new google.maps.Point( 22, 32 )
			  ),
			  end: new google.maps.MarkerImage(
			   // URL
			   '<?php echo base_url(); ?>images/drop_marker.png',
			   // (width,height)
			   new google.maps.Size( 44, 32 ),
			   // The origin point (x,y)
			   new google.maps.Point( 0, 0 ),
			   // The anchor point (x,y)
			   new google.maps.Point( 22, 32 )
			  )
			 };
function makeMarker( position, icon, title ) {
	 new google.maps.Marker({
	  position: position,
	  map: map,
	  icon: icon,
	  title: title
	 });			 
}				 
function onMessage(msg) {
    var to = msg.getAttribute('to');
    var from = msg.getAttribute('from');
    var type = msg.getAttribute('type');
    var elems = msg.getElementsByTagName('body');

    if (type == "chat" && elems.length > 0) {
	var body = elems[0];
    var Vals = Strophe.getText(body);
	
	//Vals = Vals.replace(new RegExp('&quot;', 'g'), '"');
	sample=decodeURIComponent(Vals);
	
	if (/^[\],:{}\s]*$/.test(sample.replace(/\\["\\\/bfnrtu]/g, '@').replace(/"[^"\\\n\r]*"|true|false|null|-?\d+(?:\.\d*)?(?:[eE][+\-]?\d+)?/g, ']').replace(/(?:^|:|,)(?:\s*\[)+/g, ''))) {		
		realtimedata=JSON.parse(sample);
	}else{
		sample = Vals.replace(new RegExp('&quot;', 'g'), '"');
		realtimedata=JSON.parse(sample);
	}

	
	if(realtimedata.key9!='' && typeof realtimedata.key9 != 'undefined') {
		ride_id = realtimedata.key9;
	} else if(realtimedata.key1!='' && typeof realtimedata.key1 != 'undefined') {
		ride_id = realtimedata.key1;
	} else if(realtimedata.ride_id!='' && typeof realtimedata.ride_id != 'undefined'){
	   ride_id = realtimedata.ride_id;
	}
	
	action = realtimedata.action;
	
	action_msg= realtimedata.message;
	
	current_ride_id='<?php echo $_GET['rideId']; ?>';
	if(ride_id==current_ride_id){
		switch(action) {
			case 'ride_confirmed':
				   console.log('ok');
				   
				   
					map_marker = new google.maps.Marker({
						position: {lat: parseFloat(realtimedata.key6), lng: parseFloat(realtimedata.key7)},
						map: map,
						icon: icon
					});
					map_marker.setPosition({lat: parseFloat(realtimedata.key6), lng : parseFloat(realtimedata.key6), alt: 0});
					map.setCenter({lat: parseFloat(realtimedata.key6), lng : parseFloat(realtimedata.key6), alt: 0})
					map_marker.setMap(map);
					map.setZoom(18);
					var pointA = new google.maps.LatLng(realtimedata.key6,realtimedata.key7),
					pointB = new google.maps.LatLng(realtimedata.key14,realtimedata.key15),
					// Instantiate a directions service.
					directionsService = new google.maps.DirectionsService,
					directionsDisplay = new google.maps.DirectionsRenderer({
						map: map,
						suppressMarkers: true
						
					});
					//console.log(map);
				// get route from A to B
				
				 
				directionsService.route({
					origin: pointB,
					destination: pointA,
					avoidTolls: true,
					avoidHighways: false,
					travelMode: google.maps.TravelMode.DRIVING
				}, function (response, status) {
					if (status == google.maps.DirectionsStatus.OK) {
						directionsDisplay.setDirections(response);
						var leg = response.routes[ 0 ].legs[ 0 ];
						
						  makeMarker( leg.start_location, icons.start, "title" );
						  makeMarker( leg.end_location, icons.end, 'title' );
					} else {
						window.alert('Directions request failed due to ' + status);
					}
				});
			   
			
		break;
		
		case 'trip_begin':
				   console.log('ok');
				   initialize();
				   
				   map_marker = new google.maps.Marker({
						position: {lat: parseFloat(realtimedata.key5), lng: parseFloat(realtimedata.key6)},
						map: map,
						icon: icon
					});
					map_marker.setPosition({lat: parseFloat(realtimedata.key5), lng : parseFloat(realtimedata.key6), alt: 0});
					map.setCenter({lat: parseFloat(realtimedata.key5), lng : parseFloat(realtimedata.key6), alt: 0})
					map_marker.setMap(map);
					map.setZoom(18);
				   var pointA = new google.maps.LatLng(realtimedata.key5,realtimedata.key6),
					pointB = new google.maps.LatLng(realtimedata.key3,realtimedata.key4),
					
					// Instantiate a directions service.
					directionsService = new google.maps.DirectionsService,
					directionsDisplay = new google.maps.DirectionsRenderer({
						map: map,
						suppressMarkers: true
						
					});
					console.log(map);
				// get route from A to B
				
				//directionsDisplay.setMap(null);
				 
				directionsService.route({
					origin: pointA,
					destination: pointB,
					avoidTolls: true,
					avoidHighways: false,
					travelMode: google.maps.TravelMode.DRIVING
				}, function (response, status) {
					if (status == google.maps.DirectionsStatus.OK) {
						directionsDisplay.setDirections(response);
						var leg = response.routes[ 0 ].legs[ 0 ];
						
						makeMarker( leg.start_location, icons.start, "title" );
						makeMarker( leg.end_location, icons.end, 'title' );
					} else {
						window.alert('Directions request failed due to ' + status);
					}
				});
				
			
		break;
		case 'payment_paid':
			 location.reload();
			 break;
	    case 'make_payment':
			 location.reload();
			 break;
		default:
			console.log('ok');
           
		}
		if(action=="ride_confirmed" || action=="cab_arrived" ||  action=="trip_begin" || action=="payment_paid" || action=="make_payment") {
		
			$('#action_txt').html(action_msg);
		}
		is_being_picked_up="<?php if($this->lang->line('is_being_picked')!=''){ echo stripslashes($this->lang->line('is_being_picked')); }else { echo "is being picked up";} ?>";
		is_en_route="<?php if($this->lang->line('is_en_route')!=''){ echo stripslashes($this->lang->line('is_en_route')); }else { echo "is en route";} ?>";
		has_arrived="<?php if($this->lang->line('has_arrived')!=''){ echo stripslashes($this->lang->line('has_arrived')); }else { echo "has arrived";} ?>";
		switch(action) { 
		  case 'ride_confirmed':
				$('#action_txt').html(is_being_picked_up);
				break;
		   case 'cab_arrived':
				$('#action_txt').html(is_being_picked_up);
				break;
		  case 'trip_begin':
				$('#action_txt').html(is_en_route);
				break;
		  case 'payment_paid':
				$('#action_txt').html(has_arrived);
				break;
		  case 'make_payment':
				$('#action_txt').html(has_arrived);
				break;

		}
			
		
		if(action=='driver_loc') {
			lat = realtimedata.latitude;
			lng = realtimedata.longitude;
			
			var pt={
			lat :parseFloat(lat),
			lng :parseFloat(lng),
			time : new Date(),
			}
			redraw(pt);
		}
	}
	var reply = $msg({to: from, from: to, type: 'chat'}).cnode(Strophe.copyElement(body));
	connection.send(reply.tree());
    }
    return true;
}


var pts=[];
var glb_previndx=-1;
var snaproadarray="";
var snappedCoordinates=[];
var animationdata=[];
var redraw = function(pt){	
	pt.lat=parseFloat(pt.lat);
	pt.lng=parseFloat(pt.lng);
	
	if(pts.length>=1){
		var ln = pts.length;
		var lstptindx= ln-1;
		var d= getDistanceFromLatLonInmeter(pts[lstptindx].lat,pts[lstptindx].lng,pt.lat,pt.lng);
		if(d<3){
			return false;
		}
	}
	pts.push(pt);
	
	if(pts.length==1) {
		map_marker.setPosition({lat: parseFloat(pt.lat), lng : parseFloat(pt.lng), alt: 0});
		map.setCenter({lat: parseFloat(pt.lat), lng : parseFloat(pt.lng), alt: 0})
		map.setZoom(18);
		snaproadarray=pt.lat+","+pt.lng;
	}else{
		if(pts.length>=2){
			var ln = pts.length;
			var lstptindx= ln-1;
			var previndx= glb_previndx==-1?ln-2:glb_previndx;
			var d= getDistanceFromLatLonInmeter(pts[previndx].lat,pts[previndx].lng,pts[lstptindx].lat,pts[lstptindx].lng);
			if(d>10){
				glb_previndx=lstptindx;
				snaproadarray+="|"+pt.lat+","+pt.lng;
				var limitedValues= snaproadarray.split('|').reverse().slice(0,100).reverse();
				snaproadarray=pt.lat+","+pt.lng;
				$.get('https://roads.googleapis.com/v1/snapToRoads', {
					interpolate: true,
					key: apiKey,
					path: limitedValues.join('|')
				  }, function(data) {
						drawSnappedPolyline(data);
						for(var ind =0;ind<data.snappedPoints.length;ind++){
							snappedCoordinates.push(
								{
																
									lat:data.snappedPoints[ind].location.latitude,
									lng:data.snappedPoints[ind].location.longitude
								}
							)
						}
				  });
			}else{
				glb_previndx=previndx;
				if(d>2){
					snaproadarray+="|"+pt.lat+","+pt.lng;
				}
			}
		}
	}
}
function drawSnappedPolyline(data) {
  var snappedPolyline = new google.maps.Polyline({
    path: snappedCoordinates,
    strokeColor: 'black',
    strokeWeight: 3
  });
	k=data.snappedPoints.length;
	lat=data.snappedPoints[k-1].location.latitude;
	lng=data.snappedPoints[k-1].location.longitude;
	var lastPosn = map_marker.getPosition();
	map_marker.setPosition({lat: parseFloat(lat), lng : parseFloat(lng), alt: 0});
	map.setZoom(15);
	map.setCenter({lat: parseFloat(lat), lng : parseFloat(lng), alt: 0});
	var p=new google.maps.LatLng(parseFloat(lat), parseFloat(lng));
	var heading = google.maps.geometry.spherical.computeHeading(lastPosn, p);
	icon.rotation = heading;
	map_marker.setIcon(icon);
}


function getDistanceFromLatLonInmeter(lat1,lon1,lat2,lon2) {
  var R = 6371; // Radius of the earth in km
  var dLat = deg2rad(lat2-lat1);  // deg2rad below
  var dLon = deg2rad(lon2-lon1); 
  var a = Math.sin(dLat/2)*Math.sin(dLat/2)+Math.cos(deg2rad(lat1))*Math.cos(deg2rad(lat2))*Math.sin(dLon/2)*Math.sin(dLon/2); 
  var c = 2 * Math.atan2(Math.sqrt(a), Math.sqrt(1-a)); 
  var d = R * c * 1000; // Distance in m
  return d;
}

function deg2rad(deg) {
  return deg * (Math.PI/180)
}


</script>
<script>
<?php if($ride_info->row()->ride_status=='Booked')  { ?>
map_marker = new google.maps.Marker({
						position: {lat:<?php if(isset($ride_info->row()->booking_information['pickup']['latlong']['lat'])) echo $ride_info->row()->booking_information['pickup']['latlong']['lat']; else '0';?>, lng: <?php if(isset($ride_info->row()->booking_information['pickup']['latlong']['lon'])) echo $ride_info->row()->booking_information['pickup']['latlong']['lon']; else '0';?>},
						map: map,
						icon: icons.start
});
map.setCenter({lat:<?php if(isset($ride_info->row()->booking_information['pickup']['latlong']['lat'])) echo $ride_info->row()->booking_information['pickup']['latlong']['lat']; else '0';?>, lng: <?php if(isset($ride_info->row()->booking_information['pickup']['latlong']['lon'])) echo $ride_info->row()->booking_information['pickup']['latlong']['lon']; else '0';?>});
map_marker.setMap(map);
<?php } ?>
<?php if($ride_info->row()->ride_status=='Confirmed')  { ?>
var pointA = new google.maps.LatLng(<?php if(isset($ride_info->row()->booking_information['pickup']['latlong']['lat'])) echo $ride_info->row()->booking_information['pickup']['latlong']['lat']; else '0';?>,<?php if(isset($ride_info->row()->booking_information['pickup']['latlong']['lon'])) echo $ride_info->row()->booking_information['pickup']['latlong']['lon']; else '0';?>),pointB = new google.maps.LatLng(<?php if(isset($driver_info->loc['lat'])) echo $driver_info->loc['lat']; else '0';?>,<?php if(isset($driver_info->loc['lon'])) echo $driver_info->loc['lon']; else '0';?>),
				// Instantiate a directions service.
				directionsService = new google.maps.DirectionsService,
				directionsDisplay = new google.maps.DirectionsRenderer({
					map: map,
				    suppressMarkers: true
					
});
map_marker = new google.maps.Marker({
						position: {lat: <?php if(isset($driver_info->loc['lat'])) echo $driver_info->loc['lat']; else '0';?>, lng: <?php if(isset($driver_info->loc['lon'])) echo $driver_info->loc['lon']; else '0';?>},
						map: map,
						icon: icon
});
map_marker.setPosition({lat: <?php if(isset($driver_info->loc['lat'])) echo $driver_info->loc['lat']; else '0';?>, lng: <?php if(isset($driver_info->loc['lon'])) echo $driver_info->loc['lon']; else '0';?>});
map.setCenter({lat: <?php if(isset($driver_info->loc['lat'])) echo $driver_info->loc['lat']; else '0';?>, lng: <?php if(isset($driver_info->loc['lon'])) echo $driver_info->loc['lon']; else '0';?>})
map_marker.setMap(map);
map.setZoom(18);
directionsService.route({
origin: pointA,
destination: pointB,
avoidTolls: true,
avoidHighways: false,
travelMode: google.maps.TravelMode.DRIVING
}, function (response, status) {
if (status == google.maps.DirectionsStatus.OK) {
	directionsDisplay.setDirections(response);
	var leg = response.routes[ 0 ].legs[ 0 ];

	  makeMarker( leg.start_location, icons.start, "title" );
	  makeMarker( leg.end_location, icons.end, 'title' );
} else {
	window.alert('Directions request failed due to ' + status);
}
});
<?php } ?>
<?php if($ride_info->row()->ride_status=='Arrived')  { ?>
var pointA = new google.maps.LatLng(<?php if(isset($ride_info->row()->booking_information['pickup']['latlong']['lat'])) echo $ride_info->row()->booking_information['pickup']['latlong']['lat']; else '0';?>,<?php if(isset($ride_info->row()->booking_information['pickup']['latlong']['lon'])) echo $ride_info->row()->booking_information['pickup']['latlong']['lon']; else '0';?>),pointB = new google.maps.LatLng(<?php if(isset($driver_info->loc['lat'])) echo $driver_info->loc['lat']; else '0';?>,<?php if(isset($driver_info->loc['lon'])) echo $driver_info->loc['lon']; else '0';?>),
				// Instantiate a directions service.
				directionsService = new google.maps.DirectionsService,
				directionsDisplay = new google.maps.DirectionsRenderer({
					map: map,
				    suppressMarkers: true
					
});

map_marker = new google.maps.Marker({
						position: {lat: <?php if(isset($driver_info->loc['lat'])) echo $driver_info->loc['lat']; else '0';?>, lng: <?php if(isset($driver_info->loc['lon'])) echo $driver_info->loc['lon']; else '0';?>},
						map: map,
						icon: icon
});
map_marker.setPosition({lat: <?php if(isset($driver_info->loc['lat'])) echo $driver_info->loc['lat']; else '0';?>, lng: <?php if(isset($driver_info->loc['lon'])) echo $driver_info->loc['lon']; else '0';?>});
map.setCenter({lat: <?php if(isset($driver_info->loc['lat'])) echo $driver_info->loc['lat']; else '0';?>, lng: <?php if(isset($driver_info->loc['lon'])) echo $driver_info->loc['lon']; else '0';?>})
map_marker.setMap(map);
map.setZoom(18);
directionsService.route({
origin: pointA,
destination: pointB,
avoidTolls: true,
avoidHighways: false,
travelMode: google.maps.TravelMode.DRIVING
}, function (response, status) {
if (status == google.maps.DirectionsStatus.OK) {
	directionsDisplay.setDirections(response);
	var leg = response.routes[ 0 ].legs[ 0 ];
	
	  makeMarker( leg.start_location, icons.start, "title" );
	  makeMarker( leg.end_location, icons.end, 'title' );
} else {
	window.alert('Directions request failed due to ' + status);
}
});
<?php } ?>
<?php if($ride_info->row()->ride_status=='Onride')  { ?>
var pointA = new google.maps.LatLng(<?php if(isset($ride_info->row()->booking_information['pickup']['latlong']['lat'])) echo $ride_info->row()->booking_information['pickup']['latlong']['lat']; else '0';?>,<?php if(isset($ride_info->row()->booking_information['pickup']['latlong']['lon'])) echo $ride_info->row()->booking_information['pickup']['latlong']['lon']; else '0';?>),pointB = new google.maps.LatLng(<?php if(isset($ride_info->row()->booking_information['drop']['latlong']['lat'])) echo $ride_info->row()->booking_information['drop']['latlong']['lat']; else '0';?>,<?php if(isset($ride_info->row()->booking_information['drop']['latlong']['lon'])) echo $ride_info->row()->booking_information['drop']['latlong']['lon']; else '0';?>),
				// Instantiate a directions service.
directionsService = new google.maps.DirectionsService,
directionsDisplay = new google.maps.DirectionsRenderer({
	map: map,
	suppressMarkers: true
					
});
map_marker = new google.maps.Marker({
						position: {lat: <?php if(isset($driver_info->loc['lat'])) echo $driver_info->loc['lat']; else '0';?>, lng: <?php if(isset($driver_info->loc['lon'])) echo $driver_info->loc['lon']; else '0';?>},
						map: map,
						icon: icon
});
map_marker.setPosition({lat: <?php if(isset($driver_info->loc['lat'])) echo $driver_info->loc['lat']; else '0';?>, lng: <?php if(isset($driver_info->loc['lon'])) echo $driver_info->loc['lon']; else '0';?>});
map.setCenter({lat: <?php if(isset($driver_info->loc['lat'])) echo $driver_info->loc['lat']; else '0';?>, lng: <?php if(isset($driver_info->loc['lon'])) echo $driver_info->loc['lon']; else '0';?>})
map.setZoom(18);
map_marker.setMap(map);
directionsService.route({
origin: pointA,
destination: pointB,
avoidTolls: true,
avoidHighways: false,
travelMode: google.maps.TravelMode.DRIVING
}, function (response, status) {
if (status == google.maps.DirectionsStatus.OK) {
	directionsDisplay.setDirections(response);
	var leg = response.routes[ 0 ].legs[ 0 ];
	  makeMarker( leg.start_location, icons.start, "title" );
	  makeMarker( leg.end_location, icons.end, 'title' );
} else {
	window.alert('Directions request failed due to ' + status);
}
});
<?php } ?>
<?php if($ride_info->row()->ride_status=='Finished' || $ride_info->row()->ride_status=='Completed')  { ?>
var pointA = new google.maps.LatLng(<?php if(isset($ride_info->row()->booking_information['pickup']['latlong']['lat'])) echo $ride_info->row()->booking_information['pickup']['latlong']['lat']; else '0';?>,<?php if(isset($ride_info->row()->booking_information['pickup']['latlong']['lon'])) echo $ride_info->row()->booking_information['pickup']['latlong']['lon']; else '0';?>),pointB = new google.maps.LatLng(<?php if(isset($ride_info->row()->booking_information['drop']['latlong']['lat'])) echo $ride_info->row()->booking_information['drop']['latlong']['lat']; else '0';?>,<?php if(isset($ride_info->row()->booking_information['drop']['latlong']['lon'])) echo $ride_info->row()->booking_information['drop']['latlong']['lon']; else '0';?>);
map.setCenter({lat:<?php if(isset($ride_info->row()->booking_information['pickup']['latlong']['lat'])) echo $ride_info->row()->booking_information['pickup']['latlong']['lat']; else '0';?>, lng:<?php if(isset($ride_info->row()->booking_information['pickup']['latlong']['lon'])) echo $ride_info->row()->booking_information['pickup']['latlong']['lon']; else '0';?>});
var polylineCoordinates = [
		<?php foreach($tracking_record as $data){ ?>
			new google.maps.LatLng(<?php echo $data['lat'] ?>,<?php echo $data['lon'] ?>),
		<?php }?>
        
  ];
var marker = new google.maps.Marker({
          position:{lat:<?php if(isset($ride_info->row()->booking_information['pickup']['latlong']['lat'])) echo $ride_info->row()->booking_information['pickup']['latlong']['lat']; else '0';?>, lng:<?php if(isset($ride_info->row()->booking_information['pickup']['latlong']['lon'])) echo $ride_info->row()->booking_information['pickup']['latlong']['lon']; else '0';?>},
          icon:'<?php echo base_url(); ?>images/pickup_marker.png',
          draggable: false,
          map: map
 });
 var marker = new google.maps.Marker({
          position:{lat:<?php if(isset($ride_info->row()->booking_information['drop']['latlong']['lat'])) echo $ride_info->row()->booking_information['drop']['latlong']['lat']; else '0';?>, lng:<?php if(isset($ride_info->row()->booking_information['drop']['latlong']['lon'])) echo $ride_info->row()->booking_information['drop']['latlong']['lon']; else '0';?>},
          icon:'<?php echo base_url(); ?>images/drop_marker.png',
          draggable: false,
          map: map
 });
 
var polyline = new google.maps.Polyline({
  path: polylineCoordinates,
  strokeColor: '#3393FF'
 
});

polyline.setMap(map);
<?php } ?>
</script>
</body>
</html>