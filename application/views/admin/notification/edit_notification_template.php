<?php
$this->load->view(ADMIN_ENC_URL.'/templates/header.php');
$templateDetails = $templateDetails->row(); 
?>


<script>
	$(document).ready(function(){
		$('#notification_type').click(function(){ 
			$('#notification_type').change(function(){ 
				if($('#notification_type').val() == 'notification'){
					$('.noti-mail').attr("style", "display: none !important");
					$('.noti-msg').attr("style", "display: none !important");
					$('.noti-notify').attr("style", "display: block !important");
				} else if($('#notification_type').val() == 'sms'){ 
					$('.noti-msg').attr("style", "display: block !important");
					$('.noti-notify').attr("style", "display: none !important");
					$('.noti-mail').attr("style", "display: none !important");
				} else {
					$('.noti-mail').attr("style", "display: block !important");
					$('.noti-msg').attr("style", "display: none !important");
					$('.noti-notify').attr("style", "display: none !important");
				}
			});
		});
	
	});
</script>


<?php 
if($templateDetails->notification_type == 'notification'){ ?>
<style>
.noti-notify {
	display:block !important;
}

.noti-mail {
	display:none !important;
}
.noti-msg {
	display:none !important;
}
</style>
<?php } else if($templateDetails->notification_type == 'email'){  ?>
<style>
.noti-mail {
	display:block !important;
}
.noti-notify {
	display:none !important;
}
.noti-msg {
	display:none !important;
}
</style>
<?php } else {  ?>
<style>
.noti-mail {
	display:none !important;
}
.noti-notify {
	display:none !important;
}
.noti-msg {
	display:block !important;
}
</style>
<?php } ?>

<div id="content" class="add-subpage-cms">
	<div class="grid_container">
		<div class="grid_12">
			<div class="widget_wrap">
				<div class="widget_top">
					<span class="h_icon list"></span>
					<h6><?php echo $heading;?></h6>
				</div>
				<div class="widget_content">
					<?php 
						$attributes = array('class' => 'form_container left_label', 'id' => 'commentForm','enctype' => 'multipart/form-data');
						echo form_open(ADMIN_ENC_URL.'/notification/insertEditNotificationTemplate',$attributes) 
					?>
	 					<ul class="inner-subpage">
						
							<li>
								<div class="form_grid_12">
									<label class="field_title"><?php if ($this->lang->line('admin_notification_notify_type') != '') echo stripslashes($this->lang->line('admin_notification_notify_type')); else echo 'Notify Type'; ?><span class="req">*</span></label>
									<div class="form_input">
										<select name="notification_type" id="notification_type"  class="required" style="height: 31px; width: 51%;">
											<option value="email" <?php if(isset($templateDetails->notification_type)) if($templateDetails->notification_type == 'email') echo 'selected="selected"';?>><?php if ($this->lang->line('admin_notification_e_Mail') != '') echo stripslashes($this->lang->line('admin_notification_e_Mail')); else echo 'E-Mail'; ?></option>
											<option value="notification" <?php if(isset($templateDetails->notification_type)) if($templateDetails->notification_type == 'notification') echo 'selected="selected"';?>><?php if ($this->lang->line('admin_notification_notification') != '') echo stripslashes($this->lang->line('admin_notification_notification')); else echo 'Notification'; ?></option>
											<option value="sms" <?php if(isset($templateDetails->notification_type)) if($templateDetails->notification_type == 'sms') echo 'selected="selected"';?>><?php echo  get_language_value_for_keyword('Sms',$this->data['langCode']);?></option>
										</select>
									</div> 
								</div>
							</li>
							
							<li>
								<div class="form_grid_12">
									<label class="field_title"><?php if ($this->lang->line('admin_notification_template_title') != '') echo stripslashes($this->lang->line('admin_notification_template_title')); else echo 'Template Title'; ?><span class="req">*</span></label>
									<div class="form_input">
										<input name="message[title]" style=" width:51%;" value="<?php if(isset($templateDetails->message['title'])) echo $templateDetails->message['title']; ?>" id="news_title" value="" type="text"  class="required tipTop" title="<?php if ($this->lang->line('admin_newsletter_template_name') != '') echo stripslashes($this->lang->line('admin_newsletter_template_name')); else echo 'Please enter the email templete name'; ?>"/>
									</div> 
								</div>
							</li>
							
							<li class="noti-mail">
								<div class="form_grid_12">
									<label class="field_title"><?php if ($this->lang->line('admin_notification_email_subject') != '') echo stripslashes($this->lang->line('admin_notification_email_subject')); else echo 'Email Subject'; ?> <span class="req">*</span></label>
									<div  style="float: none;" class="form_input">
										<input name="message[subject]" value="<?php if(isset($templateDetails->message['subject'])) echo $templateDetails->message['subject']; ?>" style=" width:51%;" id="news_subject" type="text"  class="required tipTop" title="<?php if ($this->lang->line('admin_newsletter_template_subject') != '') echo stripslashes($this->lang->line('admin_newsletter_template_subject')); else echo 'Please enter the email templete subject'; ?>"/>
									</div>
								</div>
							</li>
							
                           
							
							<input name="sender[name]" id="sender_name" type="hidden"  value="<?php echo $this->data['title'];?>" class="required tipTop" title="<?php if ($this->lang->line('admin_payment_gateway_sender_name') != '') echo stripslashes($this->lang->line('admin_payment_gateway_sender_name')); else echo 'Please enter the sender name'; ?>"/>
							<input name="sender[email]" id="sender_email" type="hidden"  value="<?php echo $this->config->item('email');?>" class="required tipTop" />
							
                           
								
                            <li class="noti-mail">
								<div class="form_grid_12">
									<label class="field_title"><?php if ($this->lang->line('admin_notification_email_description') != '') echo stripslashes($this->lang->line('admin_notification_email_description')); else echo 'Email Description'; ?></label>
									<div style="float: none;" class="form_input">
										<textarea name="message[mail_description]" style=" width:51%;" class="tipTop mceEditor required" title="<?php if ($this->lang->line('admin_newsletter_template_description') != '') echo stripslashes($this->lang->line('admin_newsletter_template_description')); else echo 'Please enter the email templete description'; ?>"  ><?php if(isset($templateDetails->message['mail_description'])) echo $templateDetails->message['mail_description']; ?></textarea>
									</div>
								</div>
							</li>
							
							<li class="noti-notify">
								<div class="form_grid_12">
									<label class="field_title"><?php if ($this->lang->line('admin_notification_notification_description') != '') echo stripslashes($this->lang->line('admin_notification_notification_description')); else echo 'Notification Description'; ?><span class="req">*</span></label>
									<div style="float: none;" class="form_input">
										<textarea name="message[msg_description]" style=" width:51%;" class="tipTop required" title="<?php if ($this->lang->line('admin_notification_template_description') != '') echo stripslashes($this->lang->line('admin_notification_template_description')); else echo 'Please enter the Notification template description'; ?>"><?php if(isset($templateDetails->message['msg_description'])) echo $templateDetails->message['msg_description']; ?></textarea>
									</div>
								</div>
							</li>
							
							<li class="noti-notify">
								<div class="form_grid_12">
									<label class="field_title"><?php if ($this->lang->line('admin_notification_notification_image') != '') echo stripslashes($this->lang->line('admin_notification_notification_image')); else echo 'Notification Image'; ?>  </label>
									<div class="form_input">
										<input name="image" value="<?php if(isset($templateDetails->message['image'])) echo $templateDetails->message['image']; ?>" class="tipTop" type="file" title="<?php if ($this->lang->line('admin_payment_gateway_notification_image') != '') echo stripslashes($this->lang->line('admin_payment_gateway_notification_image')); else echo 'Please choose the notification image'; ?>"/>
										
										<input name="old_image" value="<?php if(isset($templateDetails->message['image'])) echo $templateDetails->message['image']; ?>" type="hidden"/>
										<?php if(isset($templateDetails->message['image'])) if($templateDetails->message['image'] != '') { ?>
										<br/>
											<img src="images/notification/<?php echo $templateDetails->message['image']; ?>"  width="150"/>
										<?php } ?>
									</div>
								</div>
							</li>
							
							
							<li class="noti-msg">
								<div class="form_grid_12">
									<label class="field_title"><?php if ($this->lang->line('admin_message_text') != '') echo stripslashes($this->lang->line('admin_message_text')); else echo 'Message Text'; ?></label>
									<div class="form_input">
										<textarea name="message[sms_description]" style=" width:51%;" class="tipTop required" title="" maxlength="140" ><?php if(isset($templateDetails->message['sms_description'])) echo $templateDetails->message['sms_description']; ?></textarea>
									</div>
								</div>
							</li>
							
                            <input type="hidden" name="status" id="status" />
							<input type="hidden" name="_id" value="<?php echo (string)$templateDetails->_id;?>"/>
							
							
							
						</ul>
						<ul class="last-btn-submit">
						   <li>
								<div class="form_grid_12">
									<div class="form_input">
										<button type="submit" class="btn_small btn_blue" ><span><?php if ($this->lang->line('admin_common_submit') != '') echo stripslashes($this->lang->line('admin_common_submit')); else echo 'Submit'; ?></span></button>
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
<?php 
$this->load->view(ADMIN_ENC_URL.'/templates/footer.php');
?>