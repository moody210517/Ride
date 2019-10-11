<?php
$this->load->view(ADMIN_ENC_URL.'/templates/header.php');
?>
<script type="text/javascript">
$(document).ready(function(){


$("#cancelled_by").change(function(){
	var user_type = $(this).val();
	$.ajax({
	  type: 'POST',
	  data: { 'user_type': user_type },
	  url: '<?php echo ADMIN_ENC_URL;?>/rides/user_type_cancellation_reason',
	  dataType: "json",
	  success: function(data){
		$("#cancel_reason").empty();
		$(data).each(function(key,val){
			$("#cancel_reason").append($('<option></option>').val(val.id).html(val.reason)).trigger("liszt:updated");
		});
	  }
	});
});
	
});
</script>
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
					echo form_open(ADMIN_ENC_URL.'/rides/make_ride_cancelled', $attributes)
					?>
                        <div>
                        <ul>
						<?php if($rideFound == 'true'){ ?>
                        <li>
							<div class="form_grid_12">
							<label class="field_title"><?php if ($this->lang->line('admin_rides_ride_id') != '') echo stripslashes($this->lang->line('admin_rides_ride_id')); else echo 'Ride ID'; ?></label>
							<div class="form_input">
								<p><b><?php  if($ride_id != '') { echo $ride_id;} ?></b></p>
								<input name="ride_id" id="ride_id" type="hidden"  class="large tipTop required" title="<?php if ($this->lang->line('admin_ride_enter_ride_id') != '') echo stripslashes($this->lang->line('admin_ride_enter_ride_id')); else echo 'Please enter Ride ID'; ?>" readonly value="<?php  if($ride_id != '') { echo $ride_id;} ?>"/>
							</div>
							</div>
						</li>
						<li>
						<div class="form_grid_12">
						<label class="field_title"><?php if ($this->lang->line('admin_rides_current_ride_status') != '') echo stripslashes($this->lang->line('admin_rides_current_ride_status')); else echo 'Current Ride Status'; ?></label>
							<div class="form_input">
								<input name="current_ride_status" id="current_ride_status" type="text"  class="large tipTop required" title="<?php if ($this->lang->line('admin_ride_current_ride_status') != '') echo stripslashes($this->lang->line('admin_ride_current_ride_status')); else echo 'Current Ride Status'; ?>" disabled value="<?php echo get_language_value_for_keyword($ride_details->row()->ride_status,$this->data['langCode']); ?>"/>
							</div>
						</div>
						</li>
						<li>
						<div class="form_grid_12">
						<label class="field_title"><?php if ($this->lang->line('admin_rides_current_payment_status') != '') echo stripslashes($this->lang->line('admin_rides_current_payment_status')); else echo 'Current Payment Status'; ?></label>
							<div class="form_input">
								<input name="current_pay_status" id="current_pay_status" type="text"  class="large tipTop required" title="<?php if ($this->lang->line('admin_ride_current_payment_status') != '') echo stripslashes($this->lang->line('admin_ride_current_payment_status')); else echo 'Current Payment Status'; ?>" disabled value="<?php if(isset($ride_details->row()->pay_status))echo $ride_details->row()->pay_status; else echo get_language_value_for_keyword('Not Available',$this->data['langCode']); ?>"/>
							</div>
						</div>
						</li>
						<li>
						<div class="form_grid_12">
						<label class="field_title"><?php if ($this->lang->line('admin_rides_cancelled_by') != '') echo stripslashes($this->lang->line('admin_rides_cancelled_by')); else echo 'Cancelled By'; ?> <span class="req">*</span></label>
							<div class="form_input model_type">
								<select id="cancelled_by" class="chzn-select admin_cancel_ride_chosen required" name="cancelled_by"  style="width: 375px; display: none;" >
									<option value=""><?php if ($this->lang->line('admin_rides_select_cancelling_ride') != '') echo stripslashes($this->lang->line('admin_rides_select_cancelling_ride')); else echo 'Select who is Cancelling Ride?'; ?></option>
									<option value="driver"><?php if ($this->lang->line('admin_rides_driver') != '') echo stripslashes($this->lang->line('admin_rides_driver')); else echo 'Driver'; ?></option>
									<option value="user"><?php if ($this->lang->line('admin_rides_user') != '') echo stripslashes($this->lang->line('admin_rides_user')); else echo 'User'; ?></option>
								</select>
							</div>
							</div>
						</li>
						<li>
						<div class="form_grid_12">
						<label class="field_title"><?php if ($this->lang->line('admin_rides_cancellation_reason') != '') echo stripslashes($this->lang->line('admin_rides_cancellation_reason')); else echo 'Cancelling Reason'; ?> <span class="req">*</span></label>
							<div class="form_input model_type">
								<select id="cancel_reason" class="chzn-select admin_cancel_ride_chosen required" name="cancel_reason"  style="width: 375px; display: block;" >								
								<option value="" hidden="hidden"><?php if ($this->lang->line('select_an_option') != '') echo stripslashes($this->lang->line('select_an_option')); else echo 'Select an option'; ?></option>								
								</select>
							</div>
							</div>
						</li>
						<li>
							<div class="form_grid_12">
								<div class="form_input">
								<button type="submit" class="btn_small btn_blue" ><span><?php if ($this->lang->line('admin_rides_cancel_ride') != '') echo stripslashes($this->lang->line('admin_rides_cancel_ride')); else echo 'Cancel Ride'; ?></span></button>
								</div>
							</div>
						</li>
						<?php } ?>

                            </ul>
                        </div>

                    </form>
                </div>
            </div>
        </div>
    </div>
    <span class="clear"></span>
</div>

<?php
$this->load->view(ADMIN_ENC_URL.'/templates/footer.php');
?>