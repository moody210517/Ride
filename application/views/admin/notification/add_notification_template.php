<?php
$this->load->view(ADMIN_ENC_URL.'/templates/header.php');
?>

<script>
	$(document).ready(function(){
		$('#notification_type').click(function(){
			$('#notification_type').change(function(){
				if($('#notification_type').val() == 'notification'){
					$('.noti-mail').css('display','none');
					$('.noti-msg').css('display','none');
					$('.noti-notify').css('display','inline-block');
				} else if($('#notification_type').val() == 'sms'){
					$('.noti-msg').css('display','inline-block');
					$('.noti-notify').css('display','none');
					$('.noti-mail').css('display','none');
				} else {
					$('.noti-mail').css('display','inline-block');
					$('.noti-msg').css('display','none');
					$('.noti-notify').css('display','none');
				}
			});
		});
	
	});
</script>
<div id="content" class="base-app-top">
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
					<div class="base-appsec">
	 					<ul class="leftsec-contsec">
						
							<li>
								<div class="form_grid_12">
									<label class="field_title"><?php if ($this->lang->line('admin_notification_notify_type') != '') echo stripslashes($this->lang->line('admin_notification_notify_type')); else echo 'Notify Type'; ?><span class="req">*</span></label>
									<div class="form_input">
										<select name="notification_type" id="notification_type"  class="required" style="height: 31px; width: 51%;">
											<option value="email"><?php if ($this->lang->line('admin_notification_e_Mail') != '') echo stripslashes($this->lang->line('admin_notification_e_Mail')); else echo 'E-Mail'; ?></option>
											<option value="notification"><?php if ($this->lang->line('admin_notification_notification') != '') echo stripslashes($this->lang->line('admin_notification_notification')); else echo 'Notification'; ?></option>
											<option value="sms"><?php echo  get_language_value_for_keyword('Sms',$this->data['langCode']); ?></option>
										</select>
									</div> 
								</div>
							</li>
							
							<li>
								<div class="form_grid_12">
									<label class="field_title"><?php if ($this->lang->line('admin_notification_template_title') != '') echo stripslashes($this->lang->line('admin_notification_template_title')); else echo 'Template Title'; ?><span class="req">*</span></label>
									<div class="form_input">
										<input name="message[title]" style=" width:51%;" id="news_title" value="" type="text"  class="required tipTop" title="<?php if ($this->lang->line('admin_newsletter_template_title') != '') echo stripslashes($this->lang->line('admin_newsletter_template_title')); else echo 'Please enter the template name'; ?>"/>
									</div> 
								</div>
							</li>
							
							<li class="noti-mail">
								<div class="form_grid_12">
									<label class="field_title"><?php if ($this->lang->line('admin_notification_email_subject') != '') echo stripslashes($this->lang->line('admin_notification_email_subject')); else echo 'Email Subject'; ?> <span class="req">*</span></label>
									<div  class="form_input">
										<input name="message[subject]" style=" width:51%;" id="news_subject" type="text"  class="required tipTop" title="<?php if ($this->lang->line('admin_newsletter_template_sub') != '') echo stripslashes($this->lang->line('admin_newsletter_template_sub')); else echo 'Please enter the template subject'; ?>"/>
									</div>
								</div>
							</li>
							
                           
							
							<input name="sender[name]" id="sender_name" type="hidden"  value="<?php echo $this->data['title'];?>" class="required tipTop" title="<?php if ($this->lang->line('admin_payment_gateway_sender_name') != '') echo stripslashes($this->lang->line('admin_payment_gateway_sender_name')); else echo 'Please enter the sender name'; ?>"/>
							<input name="sender[email]" id="sender_email" type="hidden"  value="<?php echo $this->config->item('email');?>" class="required tipTop" />
							
                           
								
                            <li class="noti-mail">
								<div class="form_grid_12">
									<label class="field_title"><?php if ($this->lang->line('admin_notification_email_description') != '') echo stripslashes($this->lang->line('admin_notification_email_description')); else echo 'Email Description'; ?>  </label>
									<div class="form_input">
										<textarea name="message[mail_description]" style=" width:51%;" class="tipTop mceEditor required" title="<?php if ($this->lang->line('admin_newsletter_template_desc') != '') echo stripslashes($this->lang->line('admin_newsletter_template_desc')); else echo 'Please enter the  template description'; ?>"></textarea>
									</div>
								</div>
							</li>
							
							<li class="noti-notify" style="display:none;">
								<div class="form_grid_12">
									<label class="field_title"><?php if ($this->lang->line('admin_notification_notification_description') != '') echo stripslashes($this->lang->line('admin_notification_notification_description')); else echo 'Notification Description'; ?>  </label>
									<div class="form_input">
										<textarea name="message[msg_description]" style=" width:51%; required" class="tipTop" title="<?php if ($this->lang->line('admin_newsletter_template_desc') != '') echo stripslashes($this->lang->line('admin_newsletter_template_desc')); else echo 'Please enter the  template description'; ?>"></textarea>
									</div>
								</div>
							</li>
							
							<li class="noti-notify" style="display:none;">
								<div class="form_grid_12">
									<label class="field_title"><?php if ($this->lang->line('admin_notification_notification_image') != '') echo stripslashes($this->lang->line('admin_notification_notification_image')); else echo 'Notification Image'; ?>  </label>
									<div class="form_input">
										<input name="image" class="tipTop" type="file" title="<?php if ($this->lang->line('admin_payment_gateway_notification_image') != '') echo stripslashes($this->lang->line('admin_payment_gateway_notification_image')); else echo 'Please choose the notification image'; ?>"/>
									</div>
								</div>
							</li>
							
							<li class="noti-msg" style="display:none;">
								<div class="form_grid_12">
									<label class="field_title"><?php if ($this->lang->line('admin_message_text') != '') echo stripslashes($this->lang->line('admin_message_text')); else echo 'Message Text'; ?><span class="req">*</span></label>
									<div class="form_input">
										<textarea name="message[sms_description]" style=" width:51%; " class="tipTop required" title=""  maxlength="140"></textarea>
									</div>
								</div>
							</li>
							
                            <input type="hidden" name="status" id="status" />
							<input type="hidden" name="_id" value=""/>
							
							<li>
								<div class="form_grid_12">
									<div class="form_input">
										<button type="submit" class="btn_small btn_blue" ><span><?php if ($this->lang->line('admin_common_submit') != '') echo stripslashes($this->lang->line('admin_common_submit')); else echo 'Submit'; ?></span></button>
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
<?php 
$this->load->view(ADMIN_ENC_URL.'/templates/footer.php');
?>