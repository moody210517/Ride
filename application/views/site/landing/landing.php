<?php  
$this->load->view('site/templates/header');
?>

<!--------------banner------------->
<section class="banner">
   <div id="myCarousel" class="carousel slide" data-ride="carousel">
      <!-- Indicators -->
      <ol class="carousel-indicators">
		<?php 
			 if($banner->num_rows() >0){
			$i = 0;
			foreach($banner->result() as $row){
		?>
         <li data-target="#myCarousel" data-slide-to="<?php echo $i; ?>" class="<?php if($i == 0) echo 'active'; ?>"></li>
		 <?php 
			$i++;
			}
		 } ?>
      </ol>
      <div class="carousel-inner">
			<?php      
				$thumbnails = array();
				$labels = array();
                if($banner->num_rows() >0){
					$i = 0;
					foreach($banner->result() as $row){
					$thumbnails[] = "'banner/thumbnail-$row->image'";
					$labels[] = "'$row->name'";
            ?>
			<div class="item <?php if($i == 0) echo 'active'; ?>">
				<img src="images/banner/<?php echo $row->image; ?>" style="width:100%" alt="<?php echo $row->banner_title; ?>" class="img-responsive">
				<div class="container">
				   <div class="carousel-caption">
					  <h1 class="wow bounceIn" data-wow-delay="0.1s"><?php if(isset($row->name)) echo $row->name; ?></h1>
					  <p class="wow zoomIn" data-wow-delay="0.5s"><?php if(isset($row->banner_title))  echo $row->banner_title; ?></p>
					  <p class="getup wow zoomIn" data-wow-delay="0.9s"><a class="btn btn-lg btn-primary" href="<?php echo base_url(); ?>#play_store_app" role="button"><?php if ($this->lang->line('home_get_app') != '')
                                    echo stripslashes($this->lang->line('home_get_app'));
                                        else  echo 'GET APP';
                                            ?></a></p>
					  <p class="booknow wow slideInUp" data-wow-delay="0.5s"><a href="rider/booking"><?php if ($this->lang->line('home_book_now') != '')
                                    echo stripslashes($this->lang->line('home_book_now'));
                                        else  echo 'BOOK NOW';
                                            ?> </a></p>
											
											
				   </div>
				</div>
			 </div>
			 <?php $i++;
				} 
			 } else { 
			 ?>
			<div class="item active">
				<img src="images/banner/default.jpg" class="img-responsive" style="width:100%" alt="First slide">
				<div class="container">
				   <div class="carousel-caption">
					  <?php /* <h1 class="wow bounceIn" data-wow-delay="0.1s"><?php echo $siteTitle; ?> <?php if ($this->lang->line('home_helps_you') != '')
                                    echo stripslashes($this->lang->line('home_helps_you'));
                                        else  echo 'helps you';
                                            ?></h1> */ ?>
					  <p class="wow zoomIn" data-wow-delay="0.5s"><?php if ($this->lang->line('home_order_taxi') != '')
                                    echo stripslashes($this->lang->line('home_order_taxi'));
                                        else  echo 'Order taxi online and enjoy comfortable trip';
                                            ?></p>
					  <p class="getup wow zoomIn" data-wow-delay="0.9s"><a class="btn btn-lg btn-primary" href="<?php echo base_url(); ?>#play_store_app" role="button"><?php if ($this->lang->line('home_get_app') != '')
                                    echo stripslashes($this->lang->line('home_get_app'));
                                        else  echo 'GET APP';
                                            ?></a></p>
					  <p class="booknow wow slideInUp" data-wow-delay="0.5s"><a href="rider/booking"><?php if ($this->lang->line('home_book_now') != '')
                                    echo stripslashes($this->lang->line('home_book_now'));
                                        else  echo 'BOOK NOW';
                                            ?> </a></p> 
				   </div>
				</div>
			 </div>
			<?php } ?>
			
      </div>
   </div>
   <a class="left carousel-control" href="#myCarousel" role="button" data-slide="prev">
   <span class="glyphicon glyphicon-chevron-left" aria-hidden="true"><img src="images/site/banner_left_areo.png" class="img-responsive" alt="image" /></span>
   <span class="sr-only"><?php if ($this->lang->line('home_data_previous') != '') echo stripslashes($this->lang->line('site_data_previous')); else  echo 'Previous'; ?></span>
   </a>
   <a class="right carousel-control" href="#myCarousel" role="button" data-slide="next">
   <span class="glyphicon glyphicon-chevron-right" aria-hidden="true"><img src="images/site/banner_right_areo.png" class="img-responsive" alt="image"  /></span>
   <span class="sr-only"><?php if ($this->lang->line('home_next_taxi') != '') echo stripslashes($this->lang->line('home_next_taxi')); else  echo 'Next'; ?></span>
   </a>
   </div>
</section>

<!--------------landing dynamic content------------->

<?php 
	$default_lang=$dLangCode;
	$selected_lang=$langCode;
	$landing_content='';
	$css='';
	if($landing_details->num_rows()>0){
		if(($default_lang == $selected_lang) && $default_lang=='en'){
			if(isset($landing_details->row()->landing_page_content)){
				$landing_content=$landing_details->row()->landing_page_content;
				$css=$landing_details->row()->css_descrip;
			}
		}else if($selected_lang=='en'){
			if(isset($landing_details->row()->landing_page_content)){
				$landing_content=$landing_details->row()->landing_page_content;
				$css=$landing_details->row()->css_descrip;
			}
		}else{
			if(isset($landing_details->row()->$selected_lang)){
				$cont = $landing_details->row()->$selected_lang;
				$landing_content=$cont['landing_page_content'];
				$css=$cont['css_descrip'];
			}
		}
	}
	echo stripslashes($landing_content); 
	echo $css;
	?>




<!--------------priceing------------->
<section class="priceing">
	<div class="container">
		<div class="row">
			<div class="col-md-4 col-lg-4 wow slideInLeft">
            <?php 
            $formArr = array('id' => 'est_to_book_ride_form','method' => 'post');
            echo form_open('site/user/proceed_to_booking',$formArr); ?>
				<div class="priceing_form">
					<div class="estimate_form">
						<h3><?php if ($this->lang->line('home_estimate_pricing') != '')
                                    echo stripslashes($this->lang->line('home_estimate_pricing'));
                                        else  echo 'Pricing';?></h3>
						<h2><?php if ($this->lang->line('home_estimate_fare') != '')
                                    echo stripslashes($this->lang->line('home_estimate_fare'));
                                        else  echo 'Get a fare estimate';?></h2>
						<p class="estimate_input_border"></p>
						<p>
							<?php 
								if ($this->lang->line('book_pickup_location') != '') $placeholder = stripslashes($this->lang->line('book_pickup_location')); else $placeholder = 'Pickup Location';
								
								$input_data = array(
												'name' => 'pickup_location',
												'id' => 'pickup_location',
												'type' => 'text',
												'placeholder' => $placeholder
								);
								echo form_input($input_data);
							?>
						</p>
						<p>
							<?php 
								if ($this->lang->line('rider_drop_address') != '') $placeholder = stripslashes($this->lang->line('rider_drop_address')); else $placeholder = 'Drop Location';
								
								$input_data = array(
												'name' => 'drop_location',
												'id' => 'drop_location',
												'type' => 'text',
												'placeholder' => $placeholder
								);
								echo form_input($input_data);
							?>
							<span><input type="button" value=""  onclick="draw_pickup_drop_direction();"></span>
						</p>
						<span id="autocompleteErr" class="error" style="font-weight:bold;"></span>
					</div>
					<div class="estimate_detail">
					</div>
					<p class="form_inst">* <?php if ($this->lang->line('home_peak_time_charges') != '')
                                    echo stripslashes($this->lang->line('home_peak_time_charges'));
                                        else  echo 'Peak time charges and night time charges may apply based on the booking time.';?> </p>
					<div class="sign_up">
                    
												<input type="hidden" name="pickup_lon" value="0" id="pickup_lon"/>
                        <input type="hidden" name="pickup_lat" value="0" id="pickup_lat"/>
                        <input type="hidden" name="drop_lon" value="0" id="drop_lon"/>
                        <input type="hidden" name="drop_lat" value="0" id="drop_lat"/>
                    
						<input type="button" value="<?php if($this->session->userdata(APP_NAME.'_session_user_id') != '')  if ($this->lang->line('rider_continue_booking') != '') echo stripslashes($this->lang->line('rider_continue_booking')); else  echo 'Continue Booking'; else if ($this->lang->line('home_sign_up_ride') != '') echo stripslashes($this->lang->line('home_sign_up_ride')); else  echo 'sign up to ride.';?>" onclick="validate_proceed_booking();" id="proceed_booking" />
					</div>
				</div>
            <?php echo form_close(); ?>
			</div>
			<div class="col-md-8 col-lg-8 wow slideInRight">
				<div class="estimate_map">
					<div id="map" style="width:750px; height:530px;"></div>
				</div>
			</div>
		</div>
	</div>
</section>



<?php if($testimonials_details->num_rows() > 0){ ?>
	<section id="carousel_1" class="home_testmonial wow bounceIn">    				
		<div class="container">
			<div class="row">
				<h2><?php if ($this->lang->line('home_passengers_about_us') != '')
                                    echo stripslashes($this->lang->line('home_passengers_about_us'));
                                        else  echo 'PASSENGERS ABOUT US';?> </h2>
				<h1><?php if ($this->lang->line('home_testimonials') != '')
                                    echo stripslashes($this->lang->line('home_testimonials'));
                                        else  echo 'Testimonials';?></h1>
				<div class="col-md-12">
					<div class="quote"><i class="test_quote"></i></div>
					<div class="carousel slide testimonial_contant" id="fade-quote-carousel" data-ride="carousel" data-interval="false">
					  <?php if($testimonials_details->num_rows() >= 2){ ?>
					  <!-- Carousel indicators -->
					  <ol class="carousel-indicators">
						<?php 
						$i = 0;
						foreach($testimonials_details->result() as $testimonials){ ?>
						<li data-target="#fade-quote-carousel" data-slide-to="<?php echo $i; ?>" class="<?php if($i == 0) echo 'active'; ?>"></li>
						<?php $i++; } ?>
					  </ol>
					  <?php } ?>
					  <!-- Carousel items -->
					  <div class="carousel-inner">
					  
						<?php 
						$j = 0;
						foreach($testimonials_details->result() as $testimonials){ ?>
					  
							<div class="<?php if($j == 0) echo 'active'; ?> item">
								<blockquote>
									<p><?php echo $testimonials->description; ?></p>
								</blockquote>
								<div class="testimonial_buy"><?php echo $testimonials->title; ?></div>
							</div>
						
						<?php $j++; } ?>
					   
					  </div>
					</div>
				</div>							
			</div>
		</div>
	</section>
<?php } ?>
<section class="news_letter_part wow bounce">
	<div class="container">
		<div class="row">
			<div class="col-md-1 col-lg-1"></div>
			<div class="col-md-10 col-lg-10">
				<div class="newsleter">
					<div class="newletter_contant">
						<span class="newsletter_icon"><img src="images/site/newsletter_icon.png" class="img-responsive"></span>
						<span class="newsletter_contant"><?php if ($this->lang->line('home_newsletter_content') != '')
                                    echo stripslashes($this->lang->line('home_newsletter_content'));
                                        else  echo 'Join our newsletter to receive updates and hear about new product releases';?> </span>
					</div>
					
					<div class="newsletter_btn">
						<ul>
							<li>
								<?php 
									if ($this->lang->line('home_enter_email') != '') $placeholder = stripslashes($this->lang->line('home_enter_email')); else $placeholder = 'Enter email';
									
									$input_data = array(
													'name' => 'subscriber_email',
													'id' => 'subscriber_email',
													'type' => 'email',
													'placeholder' => $placeholder
									);
									echo form_input($input_data);
								?>
							</li>
							<li>
								<input type="button" onclick="email_subscription();" value="<?php if ($this->lang->line('home_subscribe') != '')
                                    echo stripslashes($this->lang->line('home_subscribe'));
                                        else  echo 'SUBSCRIBE';?>">
								<img src="images/indicator.gif" class="img-responsive" id="subscripe_loader" style="display:none;">
							</li>
							<li><span id="subscribeMsg"></span></li>
						</ul>
					</div>
				</div>
			</div>
			<div class="col-md-1 col-lg-1"></div>
		</div>
	</div>
</section>

<?php 
$map_language = "";
if($langCode!=''){
	$map_language = '&language='.$langCode;
}
?>

<link type="text/css" rel="stylesheet" href="https://fonts.googleapis.com/css?family=Roboto:300,400,500">

<script src="https://maps.googleapis.com/maps/api/js?v=3.exp&sensor=false&libraries=places<?php echo $google_maps_api_key; ?><?php echo $map_language; ?>"></script>


<input type="hidden" value="<?php echo $this->config->item('latitude'); ?>"  id="site_latitude" />
<input type="hidden" value="<?php echo $this->config->item('longitude'); ?>"  id="site_longitude" />


<script>
	var category_fare_details = '';
    
    var start_icon = 'images/pickup_marker.png';
    var end_icon = 'images/drop_marker.png';
    var geocoder;
    var map;
    var markersArray = [];
    
	function initialize() {
        geocoder = new google.maps.Geocoder();
        
        var input = document.getElementById('pickup_location');
        var autocomplete = new google.maps.places.Autocomplete(input);
        google.maps.event.addListener(autocomplete, 'place_changed', function () {
            var place = autocomplete.getPlace();
			
			/************    category update by location     ************/
			var pickup_lat = place.geometry.location.lat(); 
			var pickup_lon = place.geometry.location.lng();
			$('#pickup_lat').val(pickup_lat);
			$('#pickup_lon').val(pickup_lon);
			$.ajax({
				url: 'site/landing/ajax_fare_estimate',
				data: {"pickup_lat":pickup_lat,"pickup_lon":pickup_lon},
				type: 'POST',
				dataType: 'json',
				success: function(data) {
					if(data.status == '1'){ 
						category_fare_details = data.response;
						$('#autocompleteErr').html('');
						$('#proceed_booking').prop('disabled', false);
					} else if(data.status == '0'){
						$('#autocompleteErr').html('<span style="color:red; font-size: 13px;  margin-left: 1%;"><?php if ($this->lang->line('coomon_service_not_available_location') != '') echo stripslashes($this->lang->line('coomon_service_not_available_location')); else echo 'Sorry! service is not available in this location'; ?></>');
						$('.estimate_detail').html('');
						$('#proceed_booking').prop('disabled', true);
						//$('#pickup_location').val('');
					}         
				}
			});
			/******************       Pickup point adjust map       *********************/
				var pickupLatLng = {
					lat: pickup_lat,
					lng: pickup_lon
				};

				map = new google.maps.Map(document.getElementById('map'), {
					zoom: 14,
					center: pickupLatLng
				});
				
				var drop_lat = parseFloat($('#drop_lat').val());
				var drop_lon = parseFloat($('#drop_lon').val());
			
				if(drop_lon != '' && drop_lat != ''){
					var directionsService = new google.maps.DirectionsService;
					var directionsDisplay = new google.maps.DirectionsRenderer({draggable: true,map: map});
					directionsDisplay.setMap(map);
                    
					calculateAndDisplayRoute(directionsService, directionsDisplay);
				} else {
					var start_icon = 'images/pickup_marker.png';
					var marker = new google.maps.Marker({
						position: pickupLatLng,
						map: map,
						icon: start_icon,
						title: 'Pickup Point'
					});
				}
				
				if($('#drop_location').val() != ''){
					directionsDisplay.addListener('directions_changed', function() {  
						computeTotalDistance_EstimateFare(directionsDisplay.getDirections(),category_fare_details);
					});
				}
				
			/**********************************************************/
            get_nearest_drivers();
        });

		var input = document.getElementById('drop_location');
        var drop_location = new google.maps.places.Autocomplete(input);
        google.maps.event.addListener(drop_location, 'place_changed', function () {
            var place = drop_location.getPlace();
			
			$('#drop_lat').val(place.geometry.location.lat());
			$('#drop_lon').val(place.geometry.location.lng());
			
			var pickup_lat = parseFloat($('#pickup_lat').val());
			var pickup_lon = parseFloat($('#pickup_lon').val());
			
			var drop_lat = parseFloat($('#drop_lat').val());
			var drop_lon = parseFloat($('#drop_lon').val());
            
            if((pickup_lat == drop_lat) && (pickup_lon == drop_lon)){
				alert('please try with some other drop location');
				$('#drop_location').css('border-color','red');
				$('#pickup_location').css('border-color','red');
				location.reload(); 
			}
			/******************       Drop point adjust map       *********************/

				var dropLatLng = {
					lat: drop_lat,
					lng: drop_lon
				};
				
				map = new google.maps.Map(document.getElementById('map'), {
					zoom: 14,
					center: dropLatLng
				});
				
				if(pickup_lat != '' && drop_lon != ''){
					var directionsService = new google.maps.DirectionsService;
					var directionsDisplay = new google.maps.DirectionsRenderer({draggable: true,map: map});
					directionsDisplay.setMap(map);
					calculateAndDisplayRoute(directionsService, directionsDisplay);
				} else {
					var end_icon = 'images/drop_marker.png';
					var marker = new google.maps.Marker({
						position: dropLatLng,
						map: map,
						icon: end_icon,
						title: 'Drop Point'
					});
				}
				
				if($('#pickup_location').val() != ''){
					directionsDisplay.addListener('directions_changed', function() {  
						computeTotalDistance_EstimateFare(directionsDisplay.getDirections(),category_fare_details);
					});
				}
				
			/**********************************************************/
			get_nearest_drivers();
        });
		
		
		
		/******************          Default Load Map       ****************/
		var site_latitude = parseFloat($('#site_latitude').val());
		var site_longitude = parseFloat($('#site_longitude').val());
		if(site_longitude != '' && site_latitude != ''){ 
				map = new google.maps.Map(document.getElementById('map'), {
					zoom: 14,
					center: {lat: site_latitude, lng: site_longitude}
				});
		}
    }
    google.maps.event.addDomListener(window, 'load', initialize); 
	
	
	 function calculateAndDisplayRoute(directionsService, directionsDisplay) { 
     
        
        directionsDisplay.setOptions( { suppressMarkers: true } );
        
       
        directionsService.route({
          origin: document.getElementById('pickup_location').value,
          destination: document.getElementById('drop_location').value,
          provideRouteAlternatives: true, 
          travelMode: 'DRIVING'
        }, function(response, status) {
          if (status === 'OK') { 
            
            /******* find and make shortest path as best route *****/
            var routeArr = response.routes;
            var routeDistArr = []; 
            for(var i=0; i < routeArr.length; i++){
                //alert(((routeArr[i].legs[0].distance.value)->sec)+' : '+(routeArr[i].legs[0].duration.value/60));
                routeDistArr[i] = routeArr[i].legs[0].distance.value;
            } 
            var min_dis = routeDistArr.indexOf(Math.min(...routeDistArr));
            response.routes[0] = routeArr[min_dis];
            /*******************************************************/
            
            directionsDisplay.setDirections(response);
            
            var _route = response.routes[0].legs[0];  
			var markerA = new google.maps.Marker({
				position: _route.start_location,
				map: map,
				icon: start_icon
			})
			var markerB = new google.maps.Marker({
				position: _route.end_location,
				map: map,
				icon: end_icon
			});
            
            /* markerA.setOptions({draggable: false});
            google.maps.event.addListener(markerA, "dragend", function(event) {

                var point = markerA.getPosition(); 
                $('#pickup_lon').val(point.lng());
                $('#pickup_lat').val(point.lat()); 
                
                geocodePosition(point,'pickup_location');
                draw_pickup_drop_direction();
            }); */
            
          } else {
            window.alert('<?php if ($this->lang->line('landing_Service_not_available') != '')
                                    echo stripslashes($this->lang->line('landing_Service_not_available'));
                                        else  echo 'Service not available';?>');
          }
        });
    }
	
    
        
	function draw_pickup_drop_direction(){
		var pickup_lat = parseFloat($('#pickup_lat').val());
		var pickup_lon = parseFloat($('#pickup_lon').val());
		
		var drop_lat = parseFloat($('#drop_lat').val());
		var drop_lon = parseFloat($('#drop_lon').val());
		
		if(pickup_lat != 0 && pickup_lon != 0 && drop_lat != '' && drop_lon != ''){
			var pickupLatLng = {
				lat: pickup_lat,
				lng: pickup_lon
			};

			map = new google.maps.Map(document.getElementById('map'), {
				zoom: 14,
				center: pickupLatLng
			});
            
            
			var directionsService = new google.maps.DirectionsService;
			var directionsDisplay = new google.maps.DirectionsRenderer({draggable: true,map: map});
			directionsDisplay.setMap(map);
            
            
			calculateAndDisplayRoute(directionsService, directionsDisplay);
			
			directionsDisplay.addListener('directions_changed', function() {  
				computeTotalDistance_EstimateFare(directionsDisplay.getDirections(),category_fare_details);
			});
			
		} else {	alert("Please enter the Location");	
			if(drop_lat == '' && drop_lon == '') $('#drop_location').css('border-color','red'); 
			
			if(pickup_lat == '' && pickup_lon == '') $('#pickup_location').css('border-color','red');
			
		}
	}
	
	function computeTotalDistance_EstimateFare(result,fare_details) {
		var total_distance = 0;
		var travel_time = 0;
		var myroute = result.routes[0];  
		
		$('#pickup_location').val(myroute.legs[0].start_address);
		$('#drop_location').val(myroute.legs[0].end_address);
		
		for (var i = 0; i < myroute.legs.length; i++) {
		  total_distance += myroute.legs[i].distance.value;
		  travel_time += myroute.legs[i].duration.value;
          distance_unit=myroute.legs[i].distance.text;
		}
        return_distance = 'km';
        if (distance_unit.toLowerCase().indexOf("km") >= 0) {
              return_distance = 'km';  
              
        } else if(distance_unit.toLowerCase().indexOf("mi") >= 0) {
            return_distance = 'mi';
        } else if(distance_unit.toLowerCase().indexOf("m") >= 0) {
            return_distance = 'm';
        }
		        
        travel_time = Math.round(Math.abs(Number(travel_time / 60)));
		total_distance = Math.round((Number(total_distance / 1000)*10))/10;
        distance_unit='<?php echo $d_distance_unit; ?>';
        if(distance_unit!=return_distance){
            if(distance_unit=='km' && return_distance=='mi'){
                total_distance = total_distance * 1.60934;
            } else if(distance_unit=='mi' && return_distance=='km'){
                total_distance = total_distance * 0.621371;
                console.log(total_distance);
            } else if(distance_unit=='km' && return_distance=='m'){
                total_distance = total_distance / 1000;
            } else if(distance_unit=='mi' && return_distance=='m'){
                total_distance = total_distance * 0.00062137;
            }
		}
		var fare_cnt = '';
		for (var i = 0; i < fare_details.length; i++) {
			
			var min_amount = Number(fare_details[i].fare.min_fare);
			var service_tax = Number(fare_details[i].service_tax);
			if (Number(fare_details[i].fare.min_time) < travel_time) {
				var ride_time = Number(travel_time - Number(fare_details[i].fare.min_time));
				var ride_fare = Number(ride_time * fare_details[i].fare.per_minute);
				min_amount = Number(min_amount + ride_fare);
			}
			if (Number(fare_details[i].fare.min_km) < total_distance) {
				var ride_time = Number(total_distance - Number(fare_details[i].fare.min_km));
				var after_fare = Number(ride_time * Number(fare_details[i].fare.per_km));
				var min_amount = Number(min_amount + after_fare);
			}
			var min_amount = min_amount + (min_amount*0.01*service_tax);
			var max_amount = min_amount + (min_amount*0.01*30);
			fare_cnt += '<div class=\"row1\"><li>'+fare_details[i].cat_name+'</li><li><?php echo $dcurrencySymbol; ?> '+Math.round(min_amount)+'-'+Math.round(max_amount)+'</li></div>';
		}
		if(fare_cnt != '') $('#autocompleteErr').html('');
		$('.estimate_detail').html(fare_cnt);
	}
    
    
    function geocodePosition(pos,ids) {
     
      geocoder.geocode({
        latLng: pos
      }, function(responses) {
        if (responses && responses.length > 0) { 
          $('#'+ids).val(responses[0].formatted_address);
        } 
      });
    }
    
     function get_nearest_drivers(){ 
        var cat_id = $('#category').val();
        var pickup_lat = $('#pickup_lat').val();
        var pickup_lon = $('#pickup_lon').val();
        
        
        if(pickup_lat != '' && pickup_lon != ''){
            
           
            $.ajax({
				url: 'site/user/get_nearest_drivers_from_ajax',
				data: {"pickup_lat":pickup_lat,"pickup_lon":pickup_lon,"cat_id":''},
				type: 'POST',
				dataType: 'json',
				success: function(data) { 
                    if (markersArray) {
                        for (i in markersArray) markersArray[i].setMap(null);
                    }
					if(data.status == '1'){ 
                        var drivers = data.response;
                        for(i in drivers){
                            var lat = parseFloat(drivers[i].lat);
                            var lon = parseFloat(drivers[i].lon);
                            var markerPos = {
                                lat: lat,
                                lng: lon
                            };
                            
                            var car_icon_img = {
                                    url: '<?php echo base_url(); ?>'+drivers[i].icon_img,
                                    scaledSize: new google.maps.Size(30, 30),
                                    origin: new google.maps.Point(0, 0),
                                    anchor: new google.maps.Point(0, 0)
                                   
                            }; 
                            
                            var marker = new google.maps.Marker({
                                position: markerPos,
                                map: map,
                                icon: car_icon_img,
                            });
                            markersArray.push(marker);
                        }                       
					}   
				}
			});
        }
    }
	



  $(document).ready(function(){
	new WOW().init();
  });
  </script>

<?php 
$this->load->view('site/templates/footer'); 
?>

<script>
	$(document).ready(function()
	{
		$('div#wrapper').click(function()
		{
			if($('div#wrapper').hasClass('toggled'))
			{
				$('body').css('overflow','hidden');
			}
			else{
				$('body').css('overflow','auto');
			}
		});
	});
</script>


<script>

$(document).ready(function(){
  var width = $(window).width();
  if (width < 767){
    $('#myCarousel').carousel({
    pause: true,
    interval: false,
  })
  }	
})
</script>

<script>

$(document).ready(function(){
	$(window).resize(function() {
  var width = $(window).width();
  if (width < 767){
    $('#myCarousel').carousel({
    pause: true,
    interval: false,
  })
  }	
	})
})
</script>


