<?php
$this->load->view(OPERATOR_NAME.'/templates/header.php');
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
<div id="content" class="admin-settings">
    <div class="grid_container">
        <div class="grid_12">
            <div class="widget_wrap">
                <div class="widget_top">
                    <span class="h_icon list"></span>
                    <h6><?php echo $heading; ?></h6>
                    <div id="widget_tab">
                    </div>
                </div>
				<div class="widget_content chenge-pass-base">
                    <form class="form_container left_label" action="<?php echo OPERATOR_NAME; ?>/trip/search_ride" id="admin_search_ride_form" method="get" enctype="multipart/form-data">
                        <div>
                        <ul class="leftsec-contsec">
						<li>
							<div class="form_grid_12">
							<label class="field_title"><?php if ($this->lang->line('operator_ride_id') != '') echo stripslashes($this->lang->line('operator_ride_id')); else echo 'Ride ID'; ?><span class="req">*</span></label>
							
							<div class="form_input">
								<input name="search_ride_id" id="search_ride_id" type="text"  class="large tipTop required" title="<?php if ($this->lang->line('admin_ride_enter_ride_id') != '') echo stripslashes($this->lang->line('admin_ride_enter_ride_id')); else echo 'Please enter Ride ID'; ?>" value="<?php  if(isset($ride_id)) { echo $ride_id;} ?>"/>
								<button type="submit" class="btn_small btn_blue" ><span><?php if ($this->lang->line('admin_rides_search') != '') echo stripslashes($this->lang->line('admin_rides_search')); else echo 'Search'; ?> </span></button>
							</div>
							</div>
						</li>
						
						

                            </ul>
                        </div>

                    </form>
					
					 <form class="form_container left_label" action="<?php echo OPERATOR_NAME; ?>/trip/make_ride_cancelled" id="admin_cancelling_ride_form" method="post" enctype="multipart/form-data">
                        <div>
                        <ul class="leftsec-contsec" style="margin-top: 15px;">
						<?php if($rideFound == 'true'){ ?>
                        
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
								<input name="current_pay_status" id="current_pay_status" type="text"  class="large tipTop required" title="<?php if ($this->lang->line('admin_ride_current_payment_status') != '') echo stripslashes($this->lang->line('admin_ride_current_payment_status')); else echo 'Current Payment Status'; ?>" disabled value="<?php if(isset($ride_details->row()->pay_status))echo get_language_value_for_keyword($ride_details->row()->pay_status,$this->data['langCode']); else echo get_language_value_for_keyword('Not Available',$this->data['langCode']); ?>"/>
							</div>
						</div>
						</li>
						
						<li>
						<div class="form_grid_12">
								<label class="field_title"></label>
							<div class="form_input">
								<a class="btn_small btn_blue" target="_blank" href="track?rideId=<?php echo $ride_id; ?>" title="<?php if ($this->lang->line('admin_ride_track_this_ride') != '') echo stripslashes($this->lang->line('admin_ride_track_this_ride')); else echo 'Track this ride'; ?>"><?php if ($this->lang->line('admin_rides_track_ride') != '') echo stripslashes($this->lang->line('admin_rides_track_ride')); else echo 'Track Ride'; ?></a>
								<a class="btn_small btn_blue" target="_blank" href="<?php echo OPERATOR_NAME; ?>/trip/view_trip/<?php echo $ride_details->row()->_id; ?>" title="<?php if ($this->lang->line('admin_referral_history_view_details') != '') echo stripslashes($this->lang->line('admin_referral_history_view_details')); else echo 'View Details'; ?>"><?php if ($this->lang->line('admin_rides_view_ride') != '') echo stripslashes($this->lang->line('admin_rides_view_ride')); else echo 'View Ride'; ?></a>
								
								<?php
								$ride_status = $ride_details->row()->ride_status;
								$cancellArr = array('Booked','Confirmed','Arrived');
								if(in_array($ride_status,$cancellArr)){  ?>
									<a class="btn_small btn_blue" style="background:#be3b0a  none repeat scroll 0 0 !important;" href="<?php echo OPERATOR_NAME; ?>/trip/cancelling_ride_form?ride_id=<?php echo $ride_details->row()->ride_id; ?>" title="<?php if ($this->lang->line('admin_menu_cancel_ride') != '') echo stripslashes($this->lang->line('admin_menu_cancel_ride')); else echo 'Cancel Ride'; ?>">
									<span class="icon delete_co"></span>
									<?php if ($this->lang->line('admin_menu_cancel_ride') != '') echo stripslashes($this->lang->line('admin_menu_cancel_ride')); else echo 'Cancel Ride'; ?></a>
								<?php } ?>
								<?php 
								$completeArr = array('Onride');
								if(in_array($ride_status,$completeArr)){  ?>
									<a class="btn_small btn_blue" style="background:#0f78b9   none repeat scroll 0 0 !important;" href="<?php echo OPERATOR_NAME; ?>/trip/end_ride_form?ride_id=<?php echo $ride_details->row()->ride_id; ?>" title="<?php if ($this->lang->line('admin_end_ride') != '') echo stripslashes($this->lang->line('admin_end_ride')); else echo 'End Ride'; ?>">
									<span class="icon delete_co"></span>
									<?php if ($this->lang->line('admin_end_ride') != '') echo stripslashes($this->lang->line('admin_end_ride')); else echo 'End Ride'; ?></a>
								<?php } ?>
								
							</div>
						</div>
						</li>
						
						
						
						<?php }  ?>

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

<style>
.ride_actions {
	padding-top: 10px;
	height: 28px;
	background:green none repeat scroll 0 0 !important
}
</style>
<script>

$(document).ready(function() {
	$("#admin_search_ride_form").validate();
});
</script>

<?php
$this->load->view(OPERATOR_NAME.'/templates/footer.php');
?>