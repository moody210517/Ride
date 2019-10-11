<?php
$this->load->view(ADMIN_ENC_URL.'/templates/header.php');  
?> 

<div id="content" class="booking_trip_sec">
		<div class="grid_container">
			<div class="grid_12">
				<div class="widget_wrap">
					<div class="widget_top">
						<span class="h_icon list"></span>
						<h6><?php if ($this->lang->line('operator_booking_trip') != '') echo stripslashes($this->lang->line('operator_booking_trip')); else echo 'Booking a trip'; ?></h6>
                        <div id="widget_tab"></div>
					</div>
					<div class="widget_content">
						<form id="book_trip" method="POST" class="form_container left_label" action="<?php echo ADMIN_ENC_URL;?>/rides/confirm_trip">
						
					<div class="base_booking_trip">
						<div class="left_base_trip">
						<div class="booking_inner_trip">
						<p class="first_pickup">
							<input id="pickup_location" name="pickup_location" placeholder="<?php if ($this->lang->line('operator_pickup_location') != '') echo stripslashes($this->lang->line('operator_pickup_location')); else echo 'Pickup Location'; ?>" type="text"  class="large required" />
						</p>
						<p class="first_drop">
							<input id="drop_location" placeholder="<?php if ($this->lang->line('operator_drop_location') != '') echo stripslashes($this->lang->line('operator_drop_location')); else echo 'Drop Location'; ?>" focusout="estimate_fare();" name="drop_location" type="text"  class="large required" />
						</p>
						<p class="side_img_tag"></p>
						</div>
						<ul class="trip_type_sec">
                                <li class="catagory_type_sec">
									<div class="form_grid_12">
										<label class="field_title" id="ltrip_type"><?php if ($this->lang->line('operator_trip_type') != '') echo stripslashes($this->lang->line('operator_trip_type')); else echo 'Trip type'; ?><span class="req">*</span></label>
										<div class="form_input">
											<div class="rideLater_rideNow">
												<input type="checkbox"  name="trip_type" id="trip_type" class="rideLater_rideNow" checked="checked" />
											</div>
										</div>
									</div>
								</li>	
								
								<li>
									<div class="form_grid_12">
										<div class="form_input">
											<select style="font-size: 14px;padding:5px;width: 51%;" id="category" name="category" class="large required" onChange="get_nearest_drivers();">
												<option value=""> <?php if ($this->lang->line('operator_select_category') != '') echo stripslashes($this->lang->line('operator_select_category')); else echo 'Select Category'; ?></option>
											</select>
											<img src="images/indicator.gif"  id="catLoader" style="display:none;"/>
										</div>
									</div>
								</li>
                                
                                <li id="pickup_details">
									<div class="form_grid_12">
										<div class="form_input">
											<input placeholder="<?php if ($this->lang->line('operator_pickup_datetime') != '') echo stripslashes($this->lang->line('operator_pickup_datetime')); else echo 'Pickup Date and Time'; ?>" name="pickup_date_time" id="pickup_date_time" readonly="readonly" type="text"  class="large required"  />
										</div>
									</div>
								</li>	
									
								<li <?php  if($is_user && isset($user_info->row()->user_name)) echo 'style="display:none;"';?>>
									<div class="form_grid_12">
										<div class="form_input">
											<input placeholder="<?php if ($this->lang->line('operator_name') != '') echo stripslashes($this->lang->line('operator_name')); else echo 'Name'; ?>"  id="user_name" name="user_name" type="text"  class="large required" value="<?php if($is_user && isset($user_info->row()->user_name)) echo $user_info->row()->user_name; ?>"/>
										</div>
									</div>
								</li>
								<li <?php if($is_user && isset($user_info->row()->email)) echo 'style="display:none;"';?>>
									<div class="form_grid_12">
										<div class="form_input">
											<input placeholder="<?php if ($this->lang->line('operator_email') != '') echo stripslashes($this->lang->line('operator_email')); else echo 'Email'; ?>" id="user_email" name="user_email" type="text"  class="large required email" value="<?php if($is_user &&  isset($user_info->row()->email)) echo $user_info->row()->email; ?>" />
										</div>
									</div>
								</li>
									
								<li class="listing_phone_num" <?php if($is_user && isset($user_info->row()->phone_number)) echo 'style="display:none;"';?>>
									<div class="form_grid_12">
										<div class="form_input">
											<select name="dail_code" id="country_code"  class="required chzn-select11 small tipTop mCC" style="" title="<?php if ($this->lang->line('select_mobile_country_code') != '') echo stripslashes($this->lang->line('select_mobile_country_code')); else echo 'Please select mobile country code'; ?>">
											<?php foreach ($countryList as $country) { ?>
												<option value="<?php echo $country->dial_code; ?>" <?php if($country->dial_code==$d_country_code){
												if($country->cca3==$d_country_cca3){ echo "selected='selected'"; } }?> label="<?php echo $country->name.' ('.$country->dial_code.')'; ?>"><?php echo $country->dial_code; ?></option>
											<?php } ?>
											</select>
											
											<input placeholder="<?php if ($this->lang->line('operator_mobile_number') != '') echo stripslashes($this->lang->line('operator_mobile_number')); else echo 'Mobile Number'; ?>" id="mobile_number" name="mobile_number" type="text"  class="small required phoneNumber" value="<?php if($is_user && isset($user_info->row()->phone_number)) echo $user_info->row()->phone_number; ?>" />
										</div>
									</div>
								</li>
                                
                                
						</ul>
						</div>
						
						
						<div class="rite_base_trip">
							<ul class="trip_type_sec">
								
								<li>
									<div class="form_grid_12">
										<div class="form_input">
											<div id="map" style="height:380px"></div>
										</div>
									</div>
								</li>
                                
							</ul>
							
							
							
							
						</div>
						
						<ul class="estimate_sec_btn">
								<li>
									<div class="form_grid_12">
										<div class="form_input">
											<input type="hidden" name="pickup_lon" value="0" id="pickup_lon"/>
											<input type="hidden" name="pickup_lat" value="0" id="pickup_lat"/>
											<input type="hidden" name="drop_lon" value="0" id="drop_lon"/>
											<input type="hidden" name="drop_lat" value="0" id="drop_lat"/>
											
											<span id="pickupErr" class="error" style="font-weight:bold;"></span>
											<button type="button" class="btn_small" onclick="return estimate_fare();" style="border: 1px solid #000;" ><span><?php if ($this->lang->line('dash_operator_estimate_fare') != '') echo stripslashes($this->lang->line('dash_operator_estimate_fare')); else echo 'Estimate Fare'; ?> <img id="fareLoader" src="images/indicator.gif"></span></button>
											<button type="submit" class="btn_small btn_blue" ><span><?php if ($this->lang->line('operator_submit') != '') echo stripslashes($this->lang->line('operator_submit')); else echo 'Submit'; ?></span></button>
										</div>
									</div>
								</li>
							</ul>
					</div>
						</form>
					</div>
				</div>
			</div>
		</div>
		<span class="clear"></span>
</div>
</div>


 <div id="fare_estimate" style="display:none;">
	<h3><?php if ($this->lang->line('dash_operator_fare_estimation') != '') echo stripslashes($this->lang->line('dash_operator_fare_estimation')); else echo 'Fare Estimation'; ?></h3>
   <div class="widget_content">
	<ul>
		<li class="fare_list">
			<div class="form_grid_12 fareInner" style="margin-top: 22px !important;">
				<label class="field_title"><?php if ($this->lang->line('admin_settings_minimum_amount') != '') echo stripslashes($this->lang->line('admin_settings_minimum_amount')); else echo 'Minimum Amount'; ?></label>
				<div class="form_input">
					<span id="min_amount">0</span>
				</div>
			</div>
		</li>
		<li class="fare_list">
			<div class="form_grid_12 fareInner">
				<label class="field_title"><?php if ($this->lang->line('admin_settings_maximum_amount') != '') echo stripslashes($this->lang->line('admin_settings_maximum_amount')); else echo 'Maximum Amount'; ?></label>
				<div class="form_input">
					<span id="max_amount">0</span>
				</div>
			</div>
		</li>
		
		<li class="fare_list">
			<div class="form_grid_12 fareInner">
				<label class="field_title"><?php if ($this->lang->line('dash_operator_approx_distance') != '') echo stripslashes($this->lang->line('dash_operator_approx_distance')); else echo 'Approximate Distance'; ?></label>
				<div class="form_input">
					<span id="distance">0</span>
				</div>
			</div>
		</li>
		
		<li class="fare_list">
			<div class="form_grid_12 fareInner">
				<label class="field_title"><?php if ($this->lang->line('dash_operator_approx_travel_time') != '') echo stripslashes($this->lang->line('dash_operator_approx_travel_time')); else echo 'Approximate Travel Time'; ?></label>
				<div class="form_input">
					<span id="att">0</span>
				</div>
			</div>
		</li>
		<li class="fare_list errNote">
			<div class="form_grid_12">
				<label class="field_title"></label>
				<div class="form_input">
					<span id="note"></span>
				</div>
			</div>
		</li>
	</ul>
</div>
</div>


<input type="hidden" value="<?php echo $this->config->item('latitude'); ?>"  id="site_latitude" />
<input type="hidden" value="<?php echo $this->config->item('longitude'); ?>"  id="site_longitude" />


<style>
	.fare_list {
		display:none;
		background-color: #B8C5CC !important;
		background:none;
	}
	
	.fareInner {
		width:70% !important;
		margin-top: 15px !important;
	}
	
	.fareInner .form_input{
		float:right !important;
	}
	
	.errNote .form_grid_12{
		margin-top: 20px !important;
	}
	
	#fare_estimate {
		border-bottom: medium solid;
		color: #06c;
	}
	
	#fareLoader {
		display:none;
	}
    
    .fareNote {
        color: #06c;
        padding-left: 0 !important;
    }
    
</style>

<?php 
$map_language = "";
if($langCode!=''){
	$map_language = '&language='.$langCode;
}
?>

<script src="https://maps.googleapis.com/maps/api/js?v=3.exp&sensor=false&libraries=places<?php echo $google_maps_api_key; ?><?php echo $map_language; ?>"></script>


<script>
	function estimate_fare(){
		var category = $('#category').val();
		var pickup_lon = $('#pickup_lon').val();
		var pickup_lat = $('#pickup_lat').val();
		var drop_lon = $('#drop_lon').val();
		var drop_lat = $('#drop_lat').val();
		var pickup_date_time = $('#pickup_date_time').val();
		
        var trip_type = '0';
        if($('#trip_type').is(':checked')){
            var trip_type = '1';
        } 
        
		
		
		if(pickup_lon != '0' && pickup_lat != '0'){
			var pickup_latlang = pickup_lat+','+pickup_lon;
		} else {
			alert("<?php if ($this->lang->line('operator_pls_enter_picup_loc') != '') echo stripslashes($this->lang->line('operator_pls_enter_picup_loc')); else echo 'Please enter pickup location'; ?>"); return false;
		}
		
		if(drop_lon != '0' && drop_lat != '0'){
			var drop_latlang = drop_lat+','+drop_lon;
		} else {
			alert("<?php if ($this->lang->line('operator_pls_enter_drop_loc') != '') echo stripslashes($this->lang->line('operator_pls_enter_drop_loc')); else echo 'Please enter drop location'; ?>"); return false;
		}
        
		if(category == ''){
			alert("<?php if ($this->lang->line('operator_pls_choose_category') != '') echo stripslashes($this->lang->line('operator_pls_choose_category')); else echo 'Please choose category'; ?>"); return false;
		}
        
		if(trip_type == '1' && pickup_date_time == ''){
			alert("<?php if ($this->lang->line('operator_enter_pickup_date_time') != '') echo stripslashes($this->lang->line('operator_enter_pickup_date_time')); else echo 'Please enter pickup date and time'; ?>"); return false;
		}
        
        
		$('#fareLoader').css('display','inline-block');
		if(pickup_latlang != '' && drop_latlang != ''){
			$.ajax({
			  type: "POST",
			  url: "<?php echo ADMIN_ENC_URL;?>/rides/ajax_estimate_fare",
			  data: {'pickup_latlang':pickup_latlang,'drop_latlang':drop_latlang,'category':category,'trip_type':trip_type,'pickup_date_time':pickup_date_time},
			  success: function (result) {
				if(result.status == '1'){
					$('.fare_list').css('display','block');
					$('#note').css('color','#000');
					$('#min_amount').html(result.response.eta.min_amount);
					$('#max_amount').html(result.response.eta.max_amount);
					$('#att').html(result.response.eta.att);
					$('#distance').html(result.response.eta.distance);
					$('#note').html('<b><p class="fareNote" style="padding-left: 0 !important;">'+result.response.eta.peak_time+' </p><p class="fareNote" style="padding-left: 0 !important;">'+result.response.eta.night_charge+'</p></b><?php if ($this->lang->line('operator_eta_note') != '') echo stripslashes($this->lang->line('operator_eta_note')); else echo 'Note : This is an approximate estimate. actual cost and travel time may be different.'; ?>');
					$('#fareLoader').css('display','none');
					
					$('#fare_estimate').modal();

				} else {
					$('.fare_list').css('display','none');
					$('.errNote').css('display','block');
					$('#note').html(result.response);
					$('#note').css('color','red');
					$('#fareLoader').css('display','none');
					$('#fare_estimate').modal();
				}
			  },
			  dataType: "json"
			});
		}
	}
</script>

<link rel="stylesheet" type="text/css" media="all" href="plugins/timepicker/jquery-ui-timepicker-addon.css" />
<link rel="stylesheet" type="text/css" media="all" href="plugins/timepicker/jquery-ui-timepicker-addon.min.css" />
<script type="text/javascript" src="plugins/timepicker/jquery-ui-timepicker-addon.js"></script>
<script type="text/javascript" src="plugins/timepicker/jquery-ui-sliderAccess.js"></script>
<style>
    .ui-datepicker .ui-datepicker-buttonpane { background-image: none; margin: .7em 0 0 0; padding:0 .2em; border-left: 0; border-right: 0; border-bottom: 0; margin: 32px 0 0; }
</style>
<?php 
$am_uppercase = 'AM';
$pm_uppercase = 'PM';
if($langCode != 'en') {
    $languagPath = 'lg_files/datetime_lang_'.$langCode.'.php';
    if (file_exists($languagPath)){
        include($languagPath);
        if(isset($dateTimeKeys['am_uppercase'])) $am_uppercase = $dateTimeKeys['am_uppercase'];
        if(isset($dateTimeKeys['pm_uppercase'])) $pm_uppercase = $dateTimeKeys['pm_uppercase'];
    }
}
?>
<script>
$("#pickup_date_time").datetimepicker({
	dateFormat: "yy-mm-dd",
	changeYear: true,
	changeMonth: true,
	//minDate: new Date('<?php echo get_time_to_string('Y-m-d\TH:i:s.\0\0\0\Z',strtotime('+1 hour'));?>'),
	minDate: new Date(((new Date).getTime() + 1 * 60 * 60 * 1000)),
    maxDate: '<?php echo date('Y-m-d h:i',(strtotime('+6 days'))); ?>',
	controlType: 'select',
	oneLine: true,
	timeFormat: "hh:mm TT",
	touchonly: true,
    amNames: ['<?php echo $am_uppercase; ?>', 'A'],
	pmNames: ['<?php echo $pm_uppercase; ?>', 'P'],
	currentText: '<?php if ($this->lang->line('datetimepicker_now') != '') echo stripslashes($this->lang->line('datetimepicker_now')); else echo 'Now'; ?>',
	closeText: '<?php if ($this->lang->line('datetimepicker_done') != '') echo stripslashes($this->lang->line('datetimepicker_done')); else echo 'Done'; ?>',
	timeText: '<?php if ($this->lang->line('datetimepicker_time') != '') echo stripslashes($this->lang->line('datetimepicker_time')); else echo 'Time'; ?>',
});
</script>


<script>
$(document).ready(function() {
	$('.rideLater_rideNow :checkbox').iphoneStyle({ 
		checkedLabel: "<?php if ($this->lang->line('operator_ride_later') != '') echo stripslashes($this->lang->line('operator_ride_later')); else echo 'Later'; ?>", 
		uncheckedLabel: "<?php if ($this->lang->line('operator_ride_now') != '') echo stripslashes($this->lang->line('operator_ride_now')); else echo 'Now'; ?>" ,
		onChange: function(elem, value) {
			if($(elem)[0].checked==false){
				$("#pickup_details").hide();
				$('#pickup_date_time').removeClass('required');
			}else{
				$("#pickup_details").show();
				$('#pickup_date_time').addClass('required');
			}
		}
	});	
	
	$('#user_email').on('blur',function(){
		if($(this).val() != ''){
			$.ajax({
				url: "<?php echo ADMIN_ENC_URL;?>/rides/check_email_exists",
				data: {
					'email': $(this).val()
				},
				type: "POST",
				dataType : "json",
				success:function(data){
					if(data.phone_number !=false && data.country_code != false){
						$("#mobile_number").val(data.phone_number);
						$("#country_code").val(data.country_code);
					}
					
				}
			});
		}
	});
	$('#mobile_number').on('blur',function(){
		if($(this).val() != ''){
			$.ajax({
				url: "<?php echo ADMIN_ENC_URL;?>/rides/check_phone_number_exists",
				data: {
					'mobile_number': $(this).val(),
					'country_code': $("#country_code").val()
				},
				type: "POST",
				dataType : "json",
				success:function(data){
					if(data.email !=false){
						$("#user_email").val(data.email);
					}
					
				}
			});
		}
	});
});
</script>
<script>
	$('#book_trip').validate();
</script>


<div class="modal fade" id="search_driver" role="dialog" aria-labelledby="myModalLabel" style="display:none;">
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

.datepicker table tr td.day:hover, .datepicker table tr td.day.focused {
    background: #ccc none repeat scroll 0 0;
    cursor: pointer;

}
input{
border-radius:0px !important;
}
li.listing_phone_num .form_input select {
    width: 43% !important;
}
li.listing_phone_num .form_input input#mobile_number {
    width: 56% !important;
}
</style>

<script>
    
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
        var input = document.getElementById('pickup_location');
        var autocomplete = new google.maps.places.Autocomplete(input);
        google.maps.event.addListener(autocomplete, 'place_changed', function () {
            var place = autocomplete.getPlace();
			/************    category update by location     ************/
            $('#pickupErr').html('');
			var pickup_lat = place.geometry.location.lat(); 
			var pickup_lon = place.geometry.location.lng();
			$('#pickup_lat').val(pickup_lat);
			$('#pickup_lon').val(pickup_lon);
			$('#catLoader').css('display','inline-block');
			$('#category').html('<option value="Select Category"></option>');
			$.ajax({
				url: '<?php echo ADMIN_ENC_URL;?>/rides/get_category_from_location',
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
						$('#pickupErr').html('<span style="color:red; font-size: 13px;  margin-left: 1%;"><?php if ($this->lang->line('coomon_service_not_available_location') != '') echo stripslashes($this->lang->line('coomon_service_not_available_location')); else echo 'Sorry! service is not available in this location'; ?></>');
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
                    // This marker is 20 pixels wide by 32 pixels high.
                    scaledSize: new google.maps.Size(30, 30),
                     origin: new google.maps.Point(0, 0),
                // The anchor for this image is the base of the flagpole at (0, 32).
                    anchor: new google.maps.Point(0, 0)
                   
            };
            
            $('#catLoader').show();
            $.ajax({
				url: '<?php echo ADMIN_ENC_URL;?>/rides/get_nearest_drivers_from_ajax',
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
    
</script>

<?php
$this->load->view(ADMIN_ENC_URL.'/templates/footer.php');
?>