<?php    
	$this->load->view('site/templates/profile_header'); 
?>

<section class="profile_pic_sec row">
   <div  class="profile_login_cont">
     
		<!--------------  Load Profile Side Bar ------------------------>
		<?php    
			$this->load->view('site/templates/profile_sidebar'); 
		?>

      <div class="share_detail">
         <div class="share_det_title">
            <h2><?php if ($this->lang->line('rider_book_ride') != '') echo stripslashes($this->lang->line('rider_book_ride')); else echo 'Book Ride'; ?></h2>
         </div>
         <section class="priceing booking">
            <div class="col-lg-6 col-md-6 ">
               <div class="priceing_form booking_form">
					<?php 
						$formArr = array('id' => 'booking_form','method' => 'post');
						echo form_open('site/rider/booking_ride',$formArr);
					?>
					    <div class="estimate_form">
							 <p class="estimate_input_border booking_border"></p>
							 <p>
								<?php 
									if ($this->lang->line('book_pickup_location') != '') $placeholder = stripslashes($this->lang->line('book_pickup_location')); else $placeholder = 'Pickup Location';
									
                                    
													$pickup_location = '';
													if(isset($booking_data['pickup_location'])) $pickup_location = $booking_data['pickup_location'];
                                    
									$input_data = array(
													'name' => 'pickup_location',
													'id' => 'pickup_location',
													'type' => 'text',
													'placeholder' => $placeholder,
                                                    'value' => $pickup_location
									);
									echo form_input($input_data);
								?>
							 </p>
							 <p>
								<?php 
									if ($this->lang->line('rider_drop_address') != '') $placeholder = stripslashes($this->lang->line('rider_drop_address')); else $placeholder = 'Drop Location';
									
                                    $drop_location = '';
                                    if(isset($booking_data['drop_location'])) $drop_location = $booking_data['drop_location'];
                                    
									$input_data = array(
													'name' => 'drop_location',
													'id' => 'drop_location',
													'type' => 'text',
													'placeholder' => $placeholder,
                           'value' => $drop_location
									);
									echo form_input($input_data);
								?>
								<span><input type="button" value="" onclick="draw_pickup_drop_direction();" /></span>
							 </p>
							 <p>
								<?php 
									if ($this->lang->line('rider_select_ride_type') != '') $ride_type = stripslashes($this->lang->line('rider_select_ride_type')); else $ride_type = 'Select ride type';
                                    
                                    if ($this->lang->line('rider_now') != '') $now = stripslashes($this->lang->line('rider_now')); else $now = 'Ride Now';
									
									if ($this->lang->line('rider_later') != '') $schedule = stripslashes($this->lang->line('rider_later')); else $schedule = 'Ride Later';
									
									$drop_options = array(
														'"hidden="hidden' => $ride_type,
                                                        '0' => $now,
														'1' => $schedule
									);
									
									
									$input_data = 'id="type" onchange="servicetype();"';
									echo form_dropdown('type',$drop_options,'0',$input_data);
									
								?>
							 
							 </p>
							 <p id="datetimepicker" style="display:none;">
							 
								<?php 
									if ($this->lang->line('rider_pickup_date_time') != '') $placeholder = stripslashes($this->lang->line('rider_pickup_date_time')); else $placeholder = 'Pick up Date & Time';
									
									$input_data = array(
													'name' => 'pickup_date_time',
													'id' => 'pickup_date_time',
													'type' => 'text',
													'placeholder' => $placeholder,
													'class' => 'required',
                                                    'autocomplete' => 'off',
                                                    'readonly' => 'readonly'
									);
									echo form_input($input_data);
								?>
							 
							 </p>
							 <p>
								<?php 
									$input_data = 'id="category" class="required" onChange="get_nearest_drivers();"';
									echo form_dropdown('category',$cat_drop_options,'',$input_data);
								?>
								<img src="images/indicator.gif"  id="catLoader" style="display:none;"/><br/>
							 </p>
							 <p id="apply_promo_box">
							 
								<?php 
									if ($this->lang->line('rider_coupon_code') != '') $placeholder = stripslashes($this->lang->line('rider_coupon_code')); else $placeholder = 'Enter Promo code here';
									
									$input_data = array(
													'name' => 'code',
													'id' => 'coupon_code',
													'type' => 'text',
													'placeholder' => $placeholder
									);
									echo form_input($input_data);
								?>
								<span><input type="button" value="" onclick="validate_coupon_code('apply');"></span>
							 </p>
							 <p id="applied_promo_box" style="display:none;">
								
							 </p>
							 <img src="images/indicator.gif" id="promoLoader" style="display:none;" /><p id="coupon_codeErr"></p>
							 <p>
							 <div class="sign_up">
								
								
								<?php 
                                
                                    $pickup_lon = 0; $pickup_lat = 0; $drop_lon = 0; $drop_lat = 0;
                                    
                                    if(isset($booking_data['pickup_lon'])) $pickup_lon = $booking_data['pickup_lon'];
                                    
                                    if(isset($booking_data['pickup_lat'])) $pickup_lat = $booking_data['pickup_lat'];
                                    
                                    
                                    if(isset($booking_data['drop_lon'])) $drop_lon = $booking_data['drop_lon'];
                                    if(isset($booking_data['drop_lat'])) $drop_lat = $booking_data['drop_lat'];
									
                                    
									$input_data = array(
													'name' => 'pickup_lon',
													'id' => 'pickup_lon',
													'type' => 'hidden',
													'value' => $pickup_lon
									);
									echo form_input($input_data);
                                    
                                    
									
									$input_data = array(
													'name' => 'pickup_lat',
													'id' => 'pickup_lat',
													'type' => 'hidden',
													'value' => $pickup_lat
									);
									echo form_input($input_data);
                                    
                                    
									$input_data = array(
													'name' => 'ride_type',
													'id' => 'ride_type',
													'type' => 'hidden'
									);
									echo form_input($input_data);
									
									$input_data = array(
													'name' => 'drop_lon',
													'id' => 'drop_lon',
													'type' => 'hidden',
													'value' => $drop_lon
									);
									echo form_input($input_data);
									
									$input_data = array(
													'name' => 'drop_lat',
													'id' => 'drop_lat',
													'type' => 'hidden',
													'value' => $drop_lat
									);
									echo form_input($input_data);
									
									
								?>
								<span id="autocompleteErr" class="error" style="font-weight:bold;"></span>	
								<button type="button" id="booking" class="securityCheck" style="background-repeat:repeat !important" onclick="validate_booking_form();" >
                                <?php if ($this->lang->line('dash_user_book_now_upper') != '') echo stripslashes($this->lang->line('dash_user_book_now_upper')); else echo 'BOOK NOW'; ?> <img src="images/indicator.gif" style="display:none;" id="booking_loader" />
                                </button>
							 </div>
							 </p>
						</div>
					</form>
               </div>
            </div>
            <div class="col-lg-6 col-md-6 no_padding right">
               <div class="estimate_map book_map" >
					<div id="map" style="height:405px;"></div>
               </div>
            </div>
         </section>
      </div>
   </div>
</section> 
<input type="hidden" value="<?php echo $this->config->item('latitude'); ?>"  id="site_latitude" />
<input type="hidden" value="<?php echo $this->config->item('longitude'); ?>"  id="site_longitude" />

<input type="hidden" value="<?php echo (string)$rider_info->row()->_id;?>"  id="user_id" />
<input type="hidden" value="<?php echo APP_NAME; ?>"  id="Authkey" />
<input type="hidden" value="<?php echo $langCode; ?>"  id="Langkey" />

<?php 
$map_language = "";
if($langCode!=''){
	$map_language = '&language='.$langCode;
}
?>

<script src="https://maps.googleapis.com/maps/api/js?v=3.exp&sensor=false&libraries=places<?php echo $google_maps_api_key; ?><?php echo $map_language; ?>"></script>



<div class="modal fade" id="search_driver" role="dialog" aria-labelledby="myModalLabel">
    <div class="modal-dialog" role="document">
        <div class="modal-content favourite_container">
           
            <div class="modal-body" style="height: 135px;">
                <div class="col-lg-12 text-left sign_driver_text" style="margin-top: 5%;">
                    <div id="progressBar"><div></div></div>
                    <p><?php if ($this->lang->line('rider_searching_for_driver') != '') echo stripslashes($this->lang->line('rider_searching_for_driver')); else echo 'Searching for driver'; ?>...</p>
				</div>
            </div> 
        </div>
    </div>
</div>


<div class="modal fade" id="booking_error_popup" role="dialog" aria-labelledby="myModalLabel">
    <div class="modal-dialog" role="document">
        <div class="modal-content favourite_container">
		  <a class = "modalCloseImg simplemodal-close" id = 'close_data'> </a>
            <div class="modal-body" style="height: 155px;">
                <div class="col-lg-12 text-left sign_driver_text" style="margin-top: 6%;">
                    <p id="booking_error_popup_lbl"></p>
				</div>
            </div> 
        </div>
    </div>
</div>



<style>
.sign_driver_text {
    text-align: center;
}

#progressBar {
  width: 90%;
  margin: 10px auto;
  height: 22px;
  background-color: #0A5F44;
}

#progressBar div {
  height: 100%;
  text-align: right;
  padding: 0 10px;
  line-height: 22px; /* same as #progressBar height if we want text middle aligned */
  width: 0;
  background-color: #CBEA00;
  box-sizing: border-box;
}
#booking_error_popup_lbl{
	color: #fb4f4f;
	font-size: 20px;
	font-weight: bold;
	text-align: center;
	vertical-align: middle;
}
a.modalCloseImg.simplemodal-close {
    background: url(<?php echo $base_url; ?>images/x.png) no-repeat; 
    width: 25px;
    height: 29px;
    display: inline;
    z-index: 3200;
    position: absolute;
    top: -9px;
    right: -16px;
    cursor: pointer;
}


</style>

<script>
    
   
    
    
    <?php 
    $loadDefaultMap = TRUE;
    if(($pickup_lon != '' && $pickup_lon != 0) && ($pickup_lat != '' && $pickup_lat != 0) &&  ($drop_lon != '' && $drop_lon != 0) &&  ($drop_lat != '' && $drop_lat != 0)){ 
    $loadDefaultMap = FALSE;
    ?>
    
    draw_pickup_drop_direction();
   
   <?php } ?>
    
	$(function () {
		
		$('#pickup_date_time').datetimepicker({
			autoclose: true,
			startDate: '<?php echo date('Y-m-d H:i:s',time()+3600); ?>',
            endDate: '<?php echo date('Y-m-d H:i:s',(strtotime('+6 days'))); ?>'
		});
	});
    
    var start_icon = 'images/pickup_marker.png';
    var end_icon = 'images/drop_marker.png';
    var geocoder;
    var map;
    var markersArray = [];
	
	function initialize() {
	      var pickup_location = document.getElementById('pickup_location');
		   var drop_location = document.getElementById('drop_location');
	      if(pickup_location == drop_location){
			alert('ss');
	         }
        var input = document.getElementById('pickup_location');
        var autocomplete = new google.maps.places.Autocomplete(input);
        google.maps.event.addListener(autocomplete, 'place_changed', function () {
            var place = autocomplete.getPlace();
			/************    category update by location     ************/
            $('#autocompleteErr').html('');
			var pickup_lat = place.geometry.location.lat(); 
			var pickup_lon = place.geometry.location.lng();
			$('#pickup_lat').val(pickup_lat);
			$('#pickup_lon').val(pickup_lon);
			$('#catLoader').css('display','inline-block');
			$('#category').html('<option value="Select Category"></option>');
			$.ajax({
				url: 'site/rider/get_category_from_location',
				data: {"pickup_lat":pickup_lat,"pickup_lon":pickup_lon},
				type: 'POST',
				dataType: 'json',
				success: function(data) {   
					$('#catLoader').css('display','none');
					if(data.status == '1'){ 
						$('#category').html(data.response);
                        $('#pickup_location').css('border-color','#dcdede');
					} else if(data.status == '0'){
                        $('#pickup_lat').val('');
                        $('#pickup_lon').val('');
						$('#autocompleteErr').html('<span style="color:red; font-size: 13px;  margin-left: 1%;"><?php if ($this->lang->line('coomon_service_not_available_location') != '') echo stripslashes($this->lang->line('coomon_service_not_available_location')); else echo 'Sorry! service is not available in this location'; ?></>');
                  $('#pickup_location').css('border-color','red');
						$('#category').html(data.response);						
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
					var directionsDisplay = new google.maps.DirectionsRenderer;
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
				
			/**********************************************************/
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
					var directionsDisplay = new google.maps.DirectionsRenderer;
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
				
			/**********************************************************/
			get_nearest_drivers();
        });
		
        <?php if($loadDefaultMap){ ?>
		/******************          Default Load Map       ****************/
		var site_latitude = parseFloat($('#site_latitude').val());
		var site_longitude = parseFloat($('#site_longitude').val());
		if(site_longitude != '' && site_latitude != ''){ 
				map = new google.maps.Map(document.getElementById('map'), {
					zoom: 14,
					center: {lat: site_latitude, lng: site_longitude}
				});
		}
        <?php } ?>
    }
    google.maps.event.addDomListener(window, 'load', initialize); 
	
	
	 function calculateAndDisplayRoute(directionsService, directionsDisplay) { 
        
                
        directionsDisplay.setMap(map);
        directionsDisplay.setOptions( { suppressMarkers: true } );
        
        directionsService.route({
          origin: document.getElementById('pickup_location').value,
          destination: document.getElementById('drop_location').value,
          travelMode: 'DRIVING'
        }, function(response, status) {
          if (status === 'OK') {
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
			var directionsDisplay = new google.maps.DirectionsRenderer;
			directionsDisplay.setMap(map);
			calculateAndDisplayRoute(directionsService, directionsDisplay);
		} else {		
		   
			if(drop_lat == '' && drop_lon == '') $('#drop_location').css('border-color','red'); 
			if(pickup_lat == '' && pickup_lon == '') $('#pickup_location').css('border-color','red');
		}
	}
    
    function get_nearest_drivers(){
        var cat_id = $('#category').val();
        var pickup_lat = $('#pickup_lat').val();
        var pickup_lon = $('#pickup_lon').val();
        
        if(cat_id != '' && pickup_lat != '' && pickup_lon != ''){
            
            var car_icon = $('#category').find(':selected').attr('data-car'); 
            
            var car_icon_img = {
                url: '<?php echo base_url(); ?>'+car_icon,
                scaledSize: new google.maps.Size(30, 30),
                origin: new google.maps.Point(0, 0),
                anchor: new google.maps.Point(0, 0)
            };
            
            $('#catLoader').show();
            $.ajax({
				url: 'site/user/get_nearest_drivers_from_ajax',
				data: {"pickup_lat":pickup_lat,"pickup_lon":pickup_lon,"cat_id":cat_id},
				type: 'POST',
				dataType: 'json',
				success: function(data) { 
                    if (markersArray) {
                        for (i in markersArray) markersArray[i].setMap(null);
                    }
					$('#catLoader').css('display','none');
					if(data.status == '1'){ 
                        var drivers = data.response;
                        for(i in drivers){
                            var lat = parseFloat(drivers[i].lat);
                            var lon = parseFloat(drivers[i].lon);
                            var markerPos = {
                                lat: lat,
                                lng: lon
                            };
                            
                            var marker = new google.maps.Marker({
                                position: markerPos,
                                map: map,
                                icon: car_icon_img,
                            });
                            markersArray.push(marker);
                        }                       
						$('#catLoader').hide();
					}   
				}
			});
        }
    }
	 
	$("#close_data").click(function(){
	$("#booking_error_popup").hide();
	location.reload();
  });
    
</script>




<?php  
	$this->load->view('site/templates/footer'); 
?> 