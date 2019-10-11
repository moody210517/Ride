<?php
$this->load->view(ADMIN_ENC_URL.'/templates/header.php');
extract($privileges);
?>
<script type='text/javascript'>
$(function(){
	$('#map_tab').bind('click',function() {
            var w = $('#tab2').width();
            var h = $('#tab2').height();
            $('#map_canvas').css({ width: w, height: h });
			var center = map.getCenter();
           google.maps.event.trigger(map, 'resize');
		   map.setCenter(center); 
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
				</div>
				<div class="widget_content chenge-pass-base">
					<?php 
						$attributes = array('class' => 'form_container left_label','id' => 'report_view_form','method' => 'post');
						echo form_open(ADMIN_ENC_URL.'/reports/reply_reports',$attributes) 
					?>
					<div id="tab1">
	 					<ul class="leftsec-contsec">
	 						
							<li>
								<div class="form_grid_12">
									<label class="field_title"><?php if ($this->lang->line('admin_reports_report_id') != '') echo stripslashes($this->lang->line('admin_reports_report_id')); else echo 'Report Id'; ?></label>
									<div class="form_input">
										#<?php echo $report_details->row()->report_id;?>
									</div>
								</div>
							</li>
							
							<li>
								<div class="form_grid_12">
									<label class="field_title"><?php if ($this->lang->line('admin_reports_date') != '') echo stripslashes($this->lang->line('admin_reports_date')); else echo 'Date'; ?></label>
									<div class="form_input">
										<?php echo get_time_to_string('Y-m-d h:i A',MongoEPOCH($report_details->row()->created_date));?>
									</div>
								</div>
							</li>
								
	 						<li>
								<div class="form_grid_12">
									<label class="field_title"><?php if ($this->lang->line('admin_reports_reporter_type') != '') echo stripslashes($this->lang->line('admin_reports_reporter_type')); else echo 'Reporter Type'; ?></label>
									<div class="form_input">
										
										<?php if(isset($report_details->row()->reporter_type)) echo get_language_value_for_keyword($report_details->row()->reporter_type,$this->data['langCode']);?>
									</div>
								</div>
							</li>
							
							
							<li>
								<div class="form_grid_12">
									<label class="field_title"><?php if ($this->lang->line('admin_reports_user_info') != '') echo stripslashes($this->lang->line('admin_reports_user_info')); else echo 'User Info'; ?></label>
									<div class="form_input">
										<?php 
												echo ucfirst($report_details->row()->reporter_details['name']).' ('.$report_details->row()->reporter_details['email'].' )';
										?>
									</div>
								</div>
							</li>
							
							
							<?php 
								
									if($report_details->row()->status == 'open'){
										if ($this->lang->line('admin_subadmin_open') != '') $disp_status =  stripslashes($this->lang->line('admin_subadmin_open')); else $disp_status =  'Open'; 
									} else {
										if ($this->lang->line('admin_subadmin_close') != '') $disp_status = stripslashes($this->lang->line('admin_subadmin_close')); else $disp_status = 'Close'; 
									}
							
							?>
							
							
							<li>
								<div class="form_grid_12">
									<label class="field_title"><?php if ($this->lang->line('admin_common_status') != '') echo stripslashes($this->lang->line('admin_common_status')); else echo 'Status'; ?></label>
									<div class="form_input">
										<?php  if($report_details->row()->status == 'open') echo '<b style="color:red;">'.$disp_status.'</b>'; else echo '<b style="color:green;">'.$disp_status.'</b>'; ?>
									</div>
								</div>
							</li>
							
							<li>
								<div class="form_grid_12">
									<label class="field_title"><?php if ($this->lang->line('admin_reports_subject') != '') echo stripslashes($this->lang->line('admin_reports_subject')); else echo 'Subject'; ?></label>
									<div class="form_input">
										<?php echo $report_details->row()->subject; ?>
									</div>
								</div>
							</li>
							
							<li>
								<div class="form_grid_12">
									<label class="field_title"><?php if ($this->lang->line('admin_reports_message') != '') echo stripslashes($this->lang->line('admin_reports_message')); else echo 'Message'; ?></label>
									<div class="form_input">
										<?php echo $report_details->row()->message; ?>
									</div>
								</div>
							</li>
							
							<?php if(isset($report_details->row()->reply_message)){ ?>
							<li>
								<div class="form_grid_12">
									<label class="field_title"><?php if ($this->lang->line('admin_reports_reply') != '') echo stripslashes($this->lang->line('admin_reports_reply')); else echo 'Reply'; ?> <?php if ($this->lang->line('admin_reports_message') != '') echo stripslashes($this->lang->line('admin_reports_message')); else echo 'Message'; ?></label>
									<div class="form_input">
										<?php echo $report_details->row()->reply_message; ?>
									</div>
								</div>
							</li>
							<?php } ?>
							
							
							
							<li>
								<div class="form_grid_12">
									<div class="form_input">
										<a href="<?php echo ADMIN_ENC_URL;?>/reports/display_reports_list" class="tipLeft" title="<?php if ($this->lang->line('admin_reports_goto_reports_feedbacks_list') != '') echo stripslashes($this->lang->line('admin_reports_goto_reports_feedbacks_list')); else echo 'Go to reports/feedbacks list'; ?>"><span class="badge_style b_done btn-theme"><?php if ($this->lang->line('admin_location_and_fare_back') != '') echo stripslashes($this->lang->line('admin_location_and_fare_back')); else echo 'Back'; ?></span></a>
										<?php if($report_details->row()->status == 'open' &&  ($allPrev == '1' || in_array('2', $reports) || in_array('1', $reports))){ ?>
											<a href="javascript:void(0);" class="tipLeft" title="<?php if ($this->lang->line('admin_reports_reply_this_report') != '') echo stripslashes($this->lang->line('admin_reports_reply_this_report')); else echo 'Reply to this report/feedback'; ?>" onclick="toggle_show_replyBox();"><span class="badge_style b_done"><?php if ($this->lang->line('admin_reports_reply') != '') echo stripslashes($this->lang->line('admin_reports_reply')); else echo 'Reply'; ?></span></a>
										<?php } ?>
									</div>
								</div>
							</li>
							<?php if($report_details->row()->status == 'open' &&  ($allPrev == '1' || in_array('2', $reports) || in_array('1', $reports))){ ?>
							<li class="rply" style="display:none;z-index: 9999;">
								<div class="form_grid_12">
									<div class="form_input">
										<textarea style="width: 70%;" placeholder="<?php if ($this->lang->line('admin_reports_enter_your_reply') != '') echo stripslashes($this->lang->line('admin_reports_enter_your_reply')); else echo 'Enter your reply'; ?>" name="reply_message" id="reply_message" class="required mceEditorsimples"></textarea>
									</div>
								</div>
							</li>
							<li class="rply" style="display:none;">
								<div class="form_grid_12">
									<div class="form_input">
										<button type="submit" class="btn_small btn_blue"  ><span><?php if ($this->lang->line('admin_reports_reply') != '') echo stripslashes($this->lang->line('admin_reports_reply')); else echo 'Reply'; ?></span></button>
									</div>
								</div>
							</li>
							<input type="hidden" value="<?php echo (string)$report_details->row()->_id; ?>" name="id" />
							<input type="hidden" value="<?php echo $report_details->row()->report_id; ?>" name="report_id" />
							<input type="hidden" value="<?php echo $report_details->row()->reporter_details['name']; ?>" name="reporter_name" />
							<input type="hidden" value="<?php echo $report_details->row()->reporter_details['email']; ?>" name="reporter_email" />
							<input type="hidden" value="<?php echo $report_details->row()->subject; ?>" name="subject" />
							<input type="hidden" value="<?php echo get_time_to_string('Y-m-d h:i A',MongoEPOCH($report_details->row()->created_date));?>" name="reported_on" />
							<?php } ?>
						</ul>
					</div>
					</form>
				</div>
			</div>
		</div>
		<span class="clear"></span>
	</div>
</div>
<input type="hidden" id="replybox_show" value="No" />
<script>
$('#report_view_form').validate();
function toggle_show_replyBox(){
	if($('#replybox_show').val() == 'No'){
		$('.rply').css('display','block');
		$('#replybox_show').val('Yes');
	} else {
		$('.rply').css('display','none');
		$('#replybox_show').val('No');
	}
}
</script>
<script type="text/javascript">
		tinyMCE.init({
			// General options
			mode : "specific_textareas",
			editor_selector : "mceEditorsimple",
			theme : "simple"
		
		});

</script>
<?php 
$this->load->view(ADMIN_ENC_URL.'/templates/footer.php');
?>