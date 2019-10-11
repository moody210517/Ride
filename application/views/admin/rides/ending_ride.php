<?php
$this->load->view(ADMIN_ENC_URL.'/templates/header.php');
?>


<style type="text/css">
.model_type .error {
    float: right;
    margin-right: 30%;
}
.year-of-models .chzn-drop{
	width: 65px !important;
}
#year_of_model_chzn{
	width: 250px !important;
}
.default {
	width: 650px !important;
}
.track_ride, .view_details{
	padding: 7px 12px 7px 23px !important;
	color: #fff;
}
</style>
<div id="content">
    <div class="grid_container">
        <div class="grid_12">
            <div class="widget_wrap">
                <div class="widget_top">
                    <span class="h_icon list"></span>
                    <h6><?php echo $heading; ?></h6>
                    <div id="widget_tab">
                    </div>
                </div>
				
                <div class="widget_content">
					<?php
					$attributes = array('class' => 'form_container left_label', 'id' => 'admin_cancelling_ride_form','method'=>'POST','autocomplete'=>'off','enctype' => 'multipart/form-data');
					echo form_open(ADMIN_ENC_URL.'/rides/ending_ride', $attributes)
					?>
                        <div>
                        <ul>
						
							<li>
								<div class="form_grid_12">
								<label class="field_title"><?php if ($this->lang->line('operator_ride_id') != '') echo stripslashes($this->lang->line('operator_ride_id')); else echo 'Ride ID'; ?></label>
								<div class="form_input">
									<?php  echo $ride_id; ?>
								</div>
								</div>
							</li>
							<li>
								<div class="form_grid_12">
								<label class="field_title"><?php if ($this->lang->line('operator_drop_location') != '') echo stripslashes($this->lang->line('operator_drop_location')); else echo 'Drop Location'; ?><span class="req">*</span></label>
									<div class="form_input">
										<input name="drop_loc" id="drop_loc" type="text"  class="large tipTop required" title="<?php if ($this->lang->line('admin_enter_drop_location') != '') echo stripslashes($this->lang->line('admin_enter_drop_location')); else echo 'Enter the drop location'; ?>" placeholder="<?php if ($this->lang->line('admin_enter_drop_location') != '') echo stripslashes($this->lang->line('admin_enter_drop_location')); else echo 'Enter the drop location'; ?>" />
									</div>
								</div>
							</li>
							
							<li>
								<div class="form_grid_12">
								<label class="field_title"><?php if ($this->lang->line('admin_drop_time') != '') echo stripslashes($this->lang->line('admin_drop_time')); else echo 'Drop Time'; ?> <span class="req">*</span></label>
									<div class="form_input">
										<input name="drop_time" id="drop_time" readonly="readonly" placeholder="" type="text"  class="large tipTop required" title="<?php if ($this->lang->line('admin_enter_drop_time') != '') echo stripslashes($this->lang->line('admin_enter_drop_time')); else echo 'Enter the drop time'; ?>" />
									</div>
								</div>
							</li>
							
							<li>
								<div class="form_grid_12">
								<label class="field_title"><?php if ($this->lang->line('admin_total_distance') != '') echo stripslashes($this->lang->line('admin_total_distance')); else echo 'Total Distance'; ?> <span style="color:#c4c4c4;">( <?php echo $d_distance_unit = get_language_value_for_keyword('km',$this->data['langCode']); ?>) </span><span class="req">*</span></label>
									<div class="form_input">
										<input name="distance" id="distance" type="text"  maxlength="7" class="large tipTop required number positiveNumber minfloatingNumber" title="<?php if ($this->lang->line('admin_enter_distance_travelled') != '') echo stripslashes($this->lang->line('admin_enter_distance_travelled')); else echo 'Enter the distance travelled'; ?>" />
									</div>
								</div>
							</li>
						
							<li>
								<div class="form_grid_12">
								<label class="field_title"><?php if ($this->lang->line('admin_total_waiting_time') != '') echo stripslashes($this->lang->line('admin_total_waiting_time')); else echo 'Total Waiting Time'; ?></label>
									<div class="form_input">
										
										<select name="wait_time[0]" id="wait_time" class="large tipTop min_seconds" title="<?php if ($this->lang->line('admin_end_ride_hours') != '') echo stripslashes($this->lang->line('admin_end_ride_hours')); else echo 'Hours'; ?>">
											<?php for($i=0; $i < 24;$i++){ 
												$hr = sprintf('%02d', $i);
											?>
											<option value="<?php echo $hr; ?>"><?php echo $hr; ?></option>
											<?php } ?>
										</select>
										
										<select name="wait_time[1]" id="wait_time" class="large tipTop min_seconds" title="<?php if ($this->lang->line('admin_end_ride_minutes') != '') echo stripslashes($this->lang->line('admin_end_ride_minutes')); else echo 'Minutes'; ?>">
											<?php for($i=0; $i < 60;$i++){ 
												$min = sprintf('%02d', $i);
											?>
											<option value="<?php echo $min; ?>"><?php echo $min; ?></option>
											<?php } ?>
										</select>
										
										<select name="wait_time[2]" id="wait_time" class="large tipTop min_seconds" title="<?php if ($this->lang->line('admin_end_ride_seconds') != '') echo stripslashes($this->lang->line('admin_end_ride_seconds')); else echo 'Seconds'; ?>">
											<?php for($i=0; $i < 60;$i++){ 
												$sec = sprintf('%02d', $i);
											?>
											<option value="<?php echo $sec; ?>"><?php echo $sec; ?></option>
											<?php } ?>
										</select>
									</div>
								</div>
							</li>
							
							<li>
							<div class="form_grid_12">
								<label class="field_title"></label>
								<div class="form_input">
								<button type="submit" class="btn_small btn_blue" ><span><?php if ($this->lang->line('admin_end_ride') != '') echo stripslashes($this->lang->line('admin_end_ride')); else echo 'End Ride'; ?></span></button>
								</div>
							</div>
						</li>
						
						<input type="hidden" name="drop_lat" id="drop_lat" />
						<input type="hidden" name="drop_lon" id="drop_lon" />
						<input type="hidden" name="ride_id" id="ride_id" value="<?php echo $ride_id; ?>" />
						<input type="hidden" name="driver_id" id="driver_id" value="<?php echo $ride_details->row()->driver['id']; ?>" />
						<input type="hidden" name="interrupted" id="interrupted"  value="YES"/>

                            </ul>
                        </div>

                    </form>
                </div>
            </div>
        </div>
    </div>
    <span class="clear"></span>
</div>

<style>
.min_seconds {
	width:8%;
	height: 30px;
}
</style>



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
$("#drop_time").datetimepicker({
	dateFormat: "yy-mm-dd",
	changeYear: true,
	changeMonth: true,
	minDate: new Date('<?php echo date('Y-m-d H:i:s',MongoEPOCH($ride_details->row()->booking_information['pickup_date'])); ?>'),
	maxDate: new Date(),
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

// This example displays an address form, using the autocomplete feature
// of the Google Places API to help users fill in the information.

var placeSearch, autocomplete;

function initAutocomplete() {
  // Create the autocomplete object, restricting the search to geographical
  // location types.
  autocomplete = new google.maps.places.Autocomplete(
      /** @type {!HTMLInputElement} */(document.getElementById('drop_loc')),
      {types: ['geocode']});

		google.maps.event.addListener(autocomplete, 'place_changed', function () {
			var place = autocomplete.getPlace();
			$('#drop_lat').val(place.geometry.location.lat());
			$('#drop_lon').val(place.geometry.location.lng());
        });
}


</script>
<script src="https://maps.googleapis.com/maps/api/js?key=<?php echo $this->config->item('google_maps_api_key');?>&signed_in=true&libraries=places&callback=initAutocomplete"
async defer></script>

<?php
$this->load->view(ADMIN_ENC_URL.'/templates/footer.php');
?>