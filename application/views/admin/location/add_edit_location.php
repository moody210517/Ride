<?php
$this->load->view(ADMIN_ENC_URL.'/templates/header.php');
?>
<?php 
$share_pooling = "0";
if($share_pooling_status=="1"){
	if($form_mode){
		if(isset($locationdetails->row()->share_pooling)){ 
			if ($locationdetails->row()->share_pooling == 'Enable'){
				$share_pooling = "1";
			} 
		} 
	}
}

//print_R($share_pooling);die;
?>
<!-- Script for timepicker -->	
<script type="text/javascript" src="js/timepicker/jquery.timepicker.js"></script>
<script type="text/javascript" src="js/timepicker/bootstrap-datepicker.js"></script>
<script type="text/javascript" src="js/timepicker/site.js"></script>
<script type="text/javascript" src="js/timepicker/jquery.timepicker.min.js"></script>
<!-- Script for timepicker -->	

<!-- css for timepicker -->	
<link rel="stylesheet" type="text/css" href="css/timepicker/bootstrap-datepicker.css" />
<link rel="stylesheet" type="text/css" href="css/timepicker/site.css" />
<link rel="stylesheet" type="text/css" href="css/timepicker/jquery.timepicker.css" />

<script>
// This example displays an address form, using the autocomplete feature
// of the Google Places API to help users fill in the information.

var placeSearch, autocomplete;
var componentForm = {
  street_number: 'short_name',
  route: 'long_name',
  locality: 'long_name',
  administrative_area_level_1: 'short_name',
  country: 'long_name',
  postal_code: 'short_name'
};

function initAutocomplete() {
  // Create the autocomplete object, restricting the search to geographical
  // location types.
  var options = {
	  types: [] //'geocode','address','establishment'
	};
  autocomplete = new google.maps.places.Autocomplete((document.getElementById('city')),options);

  // When the user selects an address from the dropdown, populate the address
  // fields in the form.
  autocomplete.addListener('place_changed',function() {
    $('#location_string').val('');
  });
  
  //autocomplete.addListener('place_changed', fillInAddress);
}

function fillInAddress() {
  // Get the place details from the autocomplete object.
  var place = autocomplete.getPlace();

  for (var component in componentForm) {
    document.getElementById(component).value = '';
    document.getElementById(component).disabled = false;
  }

  // Get each component of the address from the place details
  // and fill the corresponding field on the form.
  for (var i = 0; i < place.address_components.length; i++) {
    var addressType = place.address_components[i].types[0];
    if (componentForm[addressType]) {
      var val = place.address_components[i][componentForm[addressType]];
      document.getElementById(addressType).value = val;
    }
  }
}

// Bias the autocomplete object to the user's geographical location,
// as supplied by the browser's 'navigator.geolocation' object.
function geolocate() {
  if (navigator.geolocation) {
    navigator.geolocation.getCurrentPosition(function(position) {
      var geolocation = {
        lat: position.coords.latitude,
        lng: position.coords.longitude
      };
      var circle = new google.maps.Circle({
        center: geolocation,
        radius: position.coords.accuracy
      });
      autocomplete.setBounds(circle.getBounds());
    });
  }
}
</script>
<script src="https://maps.googleapis.com/maps/api/js?key=<?php echo $this->config->item('google_maps_api_key');?>&signed_in=true&libraries=places&callback=initAutocomplete"
async defer></script>



<div id="content" class="add-location menu-set extra_menus" >
		<div class="grid_container">
			<div class="grid_12">
				<div class="widget_wrap">
					<div class="widget_top">
						<span class="h_icon list"></span>
						<h6><?php echo $heading; ?></h6>
					</div>
					<div class="widget_content">
					<?php 
						$attributes = array('class' => 'form_container left_label', 'id' => 'addeditlocation_form');
						echo form_open(ADMIN_ENC_URL.'/location/insertEditLocation',$attributes) 
					?> 		
	 						<ul class="left-edit-location rite-menu-sec">
                                <?php /* <li>
									<div class="form_grid_12">
										<label class="field_title">Country Name <span class="req">*</span></label>
										<div class="form_input">	
											<input name="countryDisp" id="countryDisp" disabled="disabled" type="text"  class="large required tipTop" value="<?php echo $this->config->item('countryName'); ?>"/>
											<input name="country" id="country" type="hidden" value="<?php echo $this->config->item('countryId'); ?>"/>
										</div>
									</div>
								</li> */ ?>
								
                                <li>
									<div class="form_grid_12">
										<label class="field_title"><?php if ($this->lang->line('admin_location_and_fare_city') != '') echo stripslashes($this->lang->line('admin_location_and_fare_city')); else echo 'City'; ?> <span class="req">*</span></label>
										<div class="form_input">
										<input name="city" id="city" type="text"  class="large required tipTop" title="<?php if ($this->lang->line('location_enter_the_city') != '') echo stripslashes($this->lang->line('location_enter_the_city')); else echo 'Please enter the city'; ?>" value="<?php if($form_mode){ echo $locationdetails->row()->city; } ?>" onFocus="geolocate()" placeholder="<?php if ($this->lang->line('admin_enter_location') != '') echo stripslashes($this->lang->line('admin_enter_location')); else echo 'Enter location'; ?>"/>
										</div>
									</div>
								</li>
								
                                <?php 
								$categoryArr='';
								if($form_mode){
									if(isset($locationdetails->row()->avail_category)){
										$categoryArr=$locationdetails->row()->avail_category;
									}else{
										$categoryArr='';
									}
								}
								if(!is_array($categoryArr))$categoryArr=array();
								
								$poolcategoryArr='';
								if($form_mode){
									if(isset($locationdetails->row()->pool_categories)){
										$poolcategoryArr=$locationdetails->row()->pool_categories;
									}else{
										$poolcategoryArr='';
									}
								}
								if(!is_array($poolcategoryArr))$poolcategoryArr=array();
								
								?>
								
								<li>
									<div class="form_grid_12">
										<label class="field_title"><?php if ($this->lang->line('admin_location_and_fare_availabe_category') != '') echo stripslashes($this->lang->line('admin_location_and_fare_availabe_category')); else echo 'Available Category'; ?><span class="req">*</span></label>
										<div class="form_input">
										<?php if($categoryList->num_rows()>0){ ?>
										<select class="chzn-select required Validname" multiple="multiple" id="category" name="category[]"  data-placeholder="<?php if ($this->lang->line('admin_availabe_category') != '') echo stripslashes($this->lang->line('admin_availabe_category')); else echo 'Choose available category'; ?>">
											<?php foreach($categoryList->result() as $row){ 
												if(!in_array($row->_id,$categoryArr)){
												$category_name = $row->name;
												if(isset($row->name_languages[$langCode ]) && $row->name_languages[$langCode ] != '') $category_name = $row->name_languages[$langCode ];
												?>
											<option value="<?php echo $row->_id; ?>" data-name="<?php echo $category_name; ?>"><?php echo $category_name; ?></option>
											<?php }
											} ?>
											<?php foreach($categoryArr as $cat_id){
											      foreach($categoryList->result() as $row){ 
												  if($row->_id==$cat_id){
												  
												  $category_name = $row->name;
												if(isset($row->name_languages[$langCode ]) && $row->name_languages[$langCode ] != '') $category_name = $row->name_languages[$langCode ];
												  
											?>
											<option value="<?php echo $row->_id; ?>" <?php if (in_array($row->_id,$categoryArr)){echo 'selected="selected"';}  ?> data-name="<?php echo $category_name; ?>"><?php echo $category_name; ?></option>
											<?php 
											}
											}
											} ?>
										</select>
										<?php }else{ ?>
								
                                            <p class="error"><?php if ($this->lang->line('admin_location_and_fare_check_category_list') != '') echo stripslashes($this->lang->line('admin_location_and_fare_check_category_list')); else echo 'Kindly check category list. There is no category.'; ?></p>
                                        
                                
										<?php } ?>
										</div>
									</div>
								</li>
								
                                <li>
									<div class="form_grid_12">
										<label class="field_title"><?php if ($this->lang->line('admin_location_and_fare_peak_time_surcharge') != '') echo stripslashes($this->lang->line('admin_location_and_fare_peak_time_surcharge')); else echo 'Peak Time Surcharge'; ?></label>
										<div class="form_input">
											<div class="peakYes_peakNo">
												<input type="checkbox"  name="peak_time" id="peak_time" class="peakYes_peakNo" <?php if($form_mode){ if(isset($locationdetails->row()->peak_time)){ if ($locationdetails->row()->peak_time == 'Yes'){echo 'checked="checked"'; }}else{echo 'checked="checked"';}}else{echo 'checked="checked"';} ?>/>
											</div>
										</div>
									</div>
								</li>
                                <li id="peak_time_frame" style="<?php if($form_mode){ if(isset($locationdetails->row()->peak_time)){ if ($locationdetails->row()->peak_time == 'No'){echo 'display:none'; }}}?>">
									<div class="form_grid_12">
										<label class="field_title"><?php if ($this->lang->line('admin_location_and_fare_peak_time_surcharge') != '') echo stripslashes($this->lang->line('admin_location_and_fare_peak_time_surcharge')); else echo 'Peak Time Surcharge'; ?><span class="req">*</span></label>
										<div class="form_input">
											<div class="peak_time_frame">
												<?php if ($this->lang->line('admin_location_and_edit_from') != '') echo stripslashes($this->lang->line('admin_location_and_edit_from')); else echo 'From'; ?>
												<input id="peak_time_frame_from" name="peak_time_frame[from]" title="<?php if ($this->lang->line('location_select_peak_time_from') != '') echo stripslashes($this->lang->line('location_select_peak_time_from')); else echo 'Select the Peak time from'; ?>" type="text" class="small required peak_time_input" value="<?php if($form_mode) if(isset($locationdetails->row()->peak_time_frame)){ echo $locationdetails->row()->peak_time_frame['from']; } ?>" /> 												
												<?php if ($this->lang->line('admin_location_and_edit_to') != '') echo stripslashes($this->lang->line('admin_location_and_edit_to')); else echo 'TO'; ?>
												<input id="peak_time_frame_to" name="peak_time_frame[to]" title="<?php if ($this->lang->line('location_select_peak_time_to') != '') echo  stripslashes($this->lang->line('location_select_peak_time_to')); else echo 'Select the Peak time to'; ?>" type="text" class="small required peak_time_input" value="<?php if($form_mode) if(isset($locationdetails->row()->peak_time_frame)){ echo $locationdetails->row()->peak_time_frame['to']; } ?>" />
											</div>
										</div>
									</div>
								</li>
                                <li>
									<div class="form_grid_12">
										<label class="field_title"><?php if ($this->lang->line('admin_location_and_fare_night_charge') != '') echo stripslashes($this->lang->line('admin_location_and_fare_night_charge')); else echo 'Night charges'; ?></label>
										<div class="form_input">
											<div class="nightYes_nightNo">
												<input type="checkbox"  name="night_charge" id="night_charge" class="nightYes_nightNo" <?php if($form_mode){ if(isset($locationdetails->row()->night_charge)){ if ($locationdetails->row()->night_charge == 'Yes'){echo 'checked="checked"'; }}else{echo 'checked="checked"';}}else{echo 'checked="checked"';} ?>/>
											</div>
										</div>
									</div>
								</li>
                                <li id="night_time_frame" style="<?php if($form_mode){ if(isset($locationdetails->row()->night_charge)){ if ($locationdetails->row()->night_charge == 'No'){echo 'display:none'; }}}?>">
									<div class="form_grid_12">
										<label class="field_title"><?php if ($this->lang->line('admin_location_and_fare_night_charges_timing') != '') echo stripslashes($this->lang->line('admin_location_and_fare_night_charges_timing')); else echo 'Night charges timing'; ?><span class="req">*</span></label>
										<div class="form_input">
											<div class="night_time_frame">
												<?php if ($this->lang->line('admin_location_and_edit_from') != '') echo stripslashes($this->lang->line('admin_location_and_edit_from')); else echo 'From'; ?>
												<input id="night_time_frame_from" name="night_time_frame[from]" title="<?php if ($this->lang->line('location_select_night_time_from') != '') echo  stripslashes($this->lang->line('location_select_night_time_from')); else echo 'Select the Night time from'; ?>" type="text" class="small required night_time_input" value="<?php if($form_mode) if(isset($locationdetails->row()->night_time_frame)){ echo $locationdetails->row()->night_time_frame['from']; } ?>" /> 												
												<?php if ($this->lang->line('admin_location_and_edit_to') != '') echo stripslashes($this->lang->line('admin_location_and_edit_to')); else echo 'To'; ?>
												<input id="night_time_frame_to" name="night_time_frame[to]" title="<?php if ($this->lang->line('location_select_night_time_to') != '') echo  stripslashes($this->lang->line('location_select_night_time_to')); else echo 'Select the Night time to'; ?>" type="text" class="small required night_time_input" value="<?php if($form_mode) if(isset($locationdetails->row()->night_time_frame)){ echo $locationdetails->row()->night_time_frame['to']; } ?>" />
											</div>
										</div>
									</div>
								</li>
								
								
							</ul>
							
							
							<ul class="rite-edit-location">	
								
								
								<li>
									<div class="form_grid_12">
										<label class="field_title"><?php if ($this->lang->line('admin_location_and_fare_service_tax') != '') echo stripslashes($this->lang->line('admin_location_and_fare_service_tax')); else echo 'Service Tax'; ?> <span class="req">*</span></label>
										<div class="form_input symbol_sec">
											<input style="margin-right:5px;float:left" name="service_tax" id="service_tax" type="text"  class="large required number tipTop positiveNumber currencyT minfloatingNumber" title="<?php if ($this->lang->line('location_enter_service_tax') != '') echo stripslashes($this->lang->line('location_enter_service_tax')); else echo 'Please enter Service tax'; ?>" value="<?php if($form_mode)if(isset($locationdetails->row()->service_tax)){ echo $locationdetails->row()->service_tax; } ?>"/> 
											<span style="width:30px;float:left;line-height:36px;" class="extra-info">(%)</span>
										</div>
									</div>
								</li>
								
								<li>
									<div class="form_grid_12 symbol_sec_2">
										<label class="field_title"><?php if ($this->lang->line('admin_location_and_fare_commission_to_site') != '') echo stripslashes($this->lang->line('admin_location_and_fare_commission_to_site')); else echo 'Commission to site'; ?> <span class="req">*</span></label>
										<div class="form_input">
											<input style="margin-right:5px;float:left" name="site_commission" id="site_commission" type="text"  class="large required number tipTop positiveNumber currencyT minfloatingNumber" title="<?php if ($this->lang->line('please_enter_commission_percent_site') != '') echo stripslashes($this->lang->line('please_enter_commission_percent_site')); else echo 'Please enter Commission Percent to site'; ?>" value="<?php if($form_mode)if(isset($locationdetails->row()->site_commission)){ echo $locationdetails->row()->site_commission; } ?>"/> 
											<span style="width:75px;float:left;line-height:36px;" class="extra-info">(% <?php if ($this->lang->line('admin_location_and_edit_ride_percent') != '') echo stripslashes($this->lang->line('admin_location_and_edit_ride_percent')); else echo 'of ride'; ?>)</span>
										</div>
									</div>
								</li>
								
								<?php if($share_pooling_status=="1"){ ?>
								<li>
									<div class="form_grid_12">
										<label class="field_title"><?php if ($this->lang->line('admin_pool_settings_enable_share_pooling') != '') echo stripslashes($this->lang->line('admin_pool_settings_enable_share_pooling')); else echo 'Enable Share Pooling'; ?> </label>
										<div class="form_input">
											<div class="pool_yes_no">
												<input type="checkbox"  name="share_pooling" id="pool_yes_no" class="pool_yes_no" <?php if($form_mode){ if(isset($locationdetails->row()->share_pooling)){ if ($locationdetails->row()->share_pooling == 'Enable'){echo 'checked="checked"';} } }  ?> <?php if($form_mode){ echo 'data-fmode="edit"'; }else{ echo 'data-fmode="add"'; }  ?>/>
											</div>
										</div>
									</div>
								</li>
								<li class="pool_values_wrapper">
									<div class="form_grid_12">
										<label class="field_title"><?php if ($this->lang->line('admin_categories_under_pool') != '') echo stripslashes($this->lang->line('admin_categories_under_pool')); else echo 'Categories under pool'; ?> <span class="req">*</span></label>
										<div class="form_input">
											<select class="chzn-select pool_values Validname <?php if($share_pooling == "1"){ ?>required<?php } ?>" multiple="multiple" id="pool_category" name="pool_category[]"  data-placeholder="<?php if ($this->lang->line('admin_choose_pooling_category') != '') echo stripslashes($this->lang->line('admin_choose_pooling_category')); else echo 'Choose Pooling category'; ?>">	
											<?php 
											if(!empty($poolcategoryArr)){
												foreach($categoryList->result() as $row){
													if(in_array($row->_id,$poolcategoryArr) && in_array($row->_id,$categoryArr)){
														$selected = 'selected="selected"';
													}else if(in_array($row->_id,$poolcategoryArr) || in_array($row->_id,$categoryArr)){
														$selected = "";
													}else{
														$selected = "-1";
													}
													if($selected != "-1"){
														$category_name = $row->name;
														if(isset($row->name_languages[$langCode ]) && $row->name_languages[$langCode ] != '') $category_name = $row->name_languages[$langCode ];
												?>
													<option value="<?php echo $row->_id; ?>" <?php echo $selected;  ?>><?php echo $category_name; ?></option>
												<?php 
													}
												}
											}else{
											foreach($categoryList->result() as $row){
												if(in_array($row->_id,$categoryArr)){
													$category_name = $row->name;
													if(isset($row->name_languages[$langCode ]) && $row->name_languages[$langCode ] != '') $category_name = $row->name_languages[$langCode ];
											?>
											<option value="<?php echo $row->_id; ?>" ><?php echo $category_name; ?></option>
											<?php 
												}
											}
											}
											?>
											</select>
									</div>
									</div>
								</li>
								<li class="pool_values_wrapper">
									<div class="form_grid_12">
										<label class="field_title"><?php if ($this->lang->line('admin_pool_fare_percentage_for_rider') != '') echo stripslashes($this->lang->line('admin_pool_fare_percentage_for_rider')); else echo 'Fare percentage for rider'; ?> <span class="req">*</span></label>
										<div class="form_input">
											 <input name="passenger" id="passenger" type="text" value="<?php if($form_mode)if(isset($locationdetails->row()->pool_fare) && !empty($locationdetails->row()->pool_fare)){ echo $locationdetails->row()->pool_fare["passenger"]; } ?>" class="large tipTop pool_values max <?php if($share_pooling == "1"){ ?>required<?php } ?>" max="100" title="<?php if ($this->lang->line('admin_enter_the_percentage_for_single_seat') != '') echo stripslashes($this->lang->line('admin_enter_the_percentage_for_single_seat')); else echo 'Enter the fare percentage for single seat'; ?>" placeholder="<?php if ($this->lang->line('admin_pool_fare_percentage_for_rider') != '') echo stripslashes($this->lang->line('admin_pool_fare_percentage_for_rider')); else echo 'Fare percentage for rider'; ?>" />
										</div>
									</div>
								</li>
								<li class="pool_values_wrapper">
									<div class="form_grid_12">
										<label class="field_title"><?php if ($this->lang->line('admin_pool_additional_fare_percentage_copassenger') != '') echo stripslashes($this->lang->line('admin_pool_additional_fare_percentage_copassenger')); else echo 'Additional fare percentage per co-passenger'; ?> <span class="req">*</span></label>
										<div class="form_input">
											 <input name="co_passenger" id="co_passenger" type="text" value="<?php if($form_mode)if(isset($locationdetails->row()->pool_fare)  && !empty($locationdetails->row()->pool_fare)){ echo $locationdetails->row()->pool_fare["co_passenger"]; } ?>" class="large tipTop pool_values max <?php if($share_pooling == "1"){ ?>required<?php } ?>" max="100" title="<?php if ($this->lang->line('admin_enter_the_fare_percentage_for_double_seat') != '') echo stripslashes($this->lang->line('admin_enter_the_fare_percentage_for_double_seat')); else echo 'Enter the fare percentage for double seat'; ?>" placeholder="<?php if ($this->lang->line('admin_pool_additional_fare_percentage_copassenger') != '') echo stripslashes($this->lang->line('admin_pool_additional_fare_percentage_copassenger')); else echo 'Additional fare percentage per co-passenger'; ?>" />
										</div>
									</div>
								</li>
								<li class="pool_values_wrapper">
									<div class="form_grid_12">
										<label class="field_title"><?php if ($this->lang->line('admin_pool_map_searching_radius') != '') echo stripslashes($this->lang->line('admin_pool_map_searching_radius')); else echo 'Pool map searching radius'; ?> <span class="req">*</span></label>
										<div class="form_input">
											 <input name="pool_map_search_radius" id="pool_map_search_radius" type="text" value="<?php if($form_mode)if(isset($locationdetails->row()->pool_map_search_radius)){ echo $locationdetails->row()->pool_map_search_radius; } ?>" class="large tipTop pool_values <?php if($share_pooling == "1"){ ?>required<?php } ?>" title="<?php if ($this->lang->line('admin_enter_the_searching_radius_for_pooling_service') != '') echo stripslashes($this->lang->line('admin_enter_the_searching_radius_for_pooling_service')); else echo 'Enter the searching radius for pooling service'; ?>" placeholder="<?php if ($this->lang->line('admin_pool_map_searching_radius') != '') echo stripslashes($this->lang->line('admin_pool_map_searching_radius')); else echo 'Pool map searching radius'; ?>" />
										</div>
									</div>
								</li>									
								<?php } ?>
								
								
								<li>
									<div class="form_grid_12">
										<label class="field_title"><?php if ($this->lang->line('admin_subadmin_status') != '') echo stripslashes($this->lang->line('admin_subadmin_status')); else echo 'Status'; ?></label>
										<div class="form_input">
											<div class="active_inactive">
												<input type="checkbox"  name="status" id="active_inactive_active" class="active_inactive" <?php if($form_mode){ if ($locationdetails->row()->status == 'Active'){echo 'checked="checked"';} }else{echo 'checked="checked"';} ?>/>
											</div>
										</div>
									</div>
								</li>
								<input type="hidden" name="location_id" value="<?php if($form_mode){ echo $locationdetails->row()->_id; } ?>"/>
                                
                                
                                <input type="hidden" name="location_string" id="location_string" value="<?php if($form_mode && isset($locationdetails->row()->location_string)){ echo $locationdetails->row()->location_string; } ?>"/>
								
							</ul>
							
							<ul class="last-btn-submit">
								<li>
									<div class="form_grid_12">
										<div class="form_input">
											<input type="hidden" val="" name="available_category">
											<button type="submit" class="btn_small btn_blue" ><span><?php if ($this->lang->line('admin_subadmin_submit') != '') echo stripslashes($this->lang->line('admin_subadmin_submit')); else echo 'Submit'; ?></span></button>
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
<?php $checkbox_lan=get_language_array_for_keyword($this->data['langCode']);?>
<script>
$(document).ready(function() {
	$.validator.setDefaults({ ignore: ":hidden:not(select)" });
	$("#addeditlocation_form").validate();
	/* Javascript function closure for Getting Selected order of Available Category  at signup*/
	var categoryArr = <?php echo json_encode($categoryArr); ?>;
	$("input[name='available_category']").val(categoryArr);
	$("input[name='selected_category']").val();
	var selectedVal=[];
	var thisValue=[];
	var finalArray=[];	
	
	 $("#category").change(function(){  
			thisValue=$(this).val();
			var thisData=$(this).find("option:selected").data('name');
			if(thisValue !=null){
				var i;
				for(i=0; i < thisValue.length; i++){
					if($.inArray(thisValue[i],selectedVal) == -1){
						selectedVal.push(thisValue[i]);
						$("input[name='available_category']").val(selectedVal);
					}else if(thisValue.length <= selectedVal.length){
						finalArray=[];	
						for(i=0; i < selectedVal.length; i++){
							if($.inArray(selectedVal[i],thisValue) != -1){
								finalArray.push(selectedVal[i]);
							}
							$("input[name='available_category']").val(finalArray);
						}
						selectedVal=finalArray;
					}
				}
			}else{
				$("input[name='available_category']").val("");
				finalArray,thisValue,selectedVal=[];
			}
			updatePoolCategory($(this));
	});
	/* Ending of Available Category Closure*/
	
	$('.peakYes_peakNo :checkbox').iphoneStyle({
		checkedLabel: '<?php echo $checkbox_lan['verify_status_yes_ucfirst']; ?>', 
		uncheckedLabel: '<?php echo $checkbox_lan['verify_status_no_ucfirst']; ?>' ,
		onChange: function(elem, value) {
			if($(elem)[0].checked==false){
				$("#peak_time_frame").hide();
				$('.peak_time_input').removeClass('required');
			}else{
				$("#peak_time_frame").show();
				$('.peak_time_input').addClass('required');
			}
		}
	});
	$('.nightYes_nightNo :checkbox').iphoneStyle({ 
		checkedLabel: '<?php echo $checkbox_lan['verify_status_yes_ucfirst']; ?>', 
		uncheckedLabel: '<?php echo $checkbox_lan['verify_status_no_ucfirst']; ?>',
		onChange: function(elem, value) {
			if($(elem)[0].checked==false){
				$("#night_time_frame").hide();
				$('.night_time_input').removeClass('required');
			}else{
				$("#night_time_frame").show();
				$('.night_time_input').addClass('required');
			}
		}
	});
	
	
	$('.pool_yes_no :checkbox').iphoneStyle({
		checkedLabel: '<?php echo $checkbox_lan['verify_status_yes_ucfirst']; ?>', 
		uncheckedLabel: '<?php echo $checkbox_lan['verify_status_no_ucfirst']; ?>' ,
		onChange: function(elem, value) {
			
			var fmode = $("#pool_yes_no").attr('data-fmode');
			
			$(".chzn-drop").css("width","200px")
			
			
			
			if($(elem)[0].checked==false){
				$(".pool_values_wrapper").hide();
				$('.pool_values').removeClass('required');
			}else{
				$(".pool_values_wrapper").show();
				$('.pool_values').addClass('required');
			}
		}
	});
	
	$('#peak_time_frame_from').timepicker({ 'timeFormat': 'h:i A' });
	$('#peak_time_frame_to').timepicker({ 'timeFormat': 'h:i A' });
	
	
	 
				
	$('#night_time_frame_from').timepicker({ 'timeFormat': 'h:i A' });
	$('#night_time_frame_to').timepicker({ 'timeFormat': 'h:i A' });
	
	$('input.peak_time_input').bind('copy paste cut keypress', function (e) {
       e.preventDefault();
    });
	$('input.night_time_input').bind('copy paste cut keypress', function (e) {
       e.preventDefault();
    });
	
});
$.validator.setDefaults({ ignore: ":hidden:not(select)" });


function updatePoolCategory(catObj){
	$("#pool_category").html("");
    $("#category").trigger("liszt:updated");
	$('#category').find("option:selected").each(function(){ 
		$('#pool_category').append('<option value="'+$(this).val()+'">'+$(this).data('name')+'</option>');
	})
	$("#pool_category").trigger("liszt:updated");
}

<?php if($share_pooling == "0"){ ?>
	$(".pool_values_wrapper").hide();
	$('.pool_values').removeClass('required');
<?php } ?>		
				
</script>

<style>
.chzn-container {
	display: block;
	width: 50% !important;
}
.chzn-container-multi .chzn-choices .search-field {
	width: 100%;
}
.chzn-container-multi .chzn-choices .search-field .default {
	float: left;
	width: 100% !important;
}
<?php if($share_pooling == "0"){ ?>
.pool_values_wrapper { 
	display:none;
}
<?php } ?>
</style>

<?php 
$this->load->view(ADMIN_ENC_URL.'/templates/footer.php');
?>