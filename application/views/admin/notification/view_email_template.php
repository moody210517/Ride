<?php
$this->load->view(ADMIN_ENC_URL.'/templates/header.php');
?>
<div id="content">
	<div class="grid_container">
		<div class="grid_12">
			<div class="widget_wrap">
				<div class="widget_top">
					<span class="h_icon list"></span>
					<h6><?php echo $heading;?></h6>
				</div>
				<div class="widget_content chenge-pass-base">
					<?php 
						$attributes = array('class' => 'form_container left_label');
						echo form_open(ADMIN_ENC_URL,$attributes) 
					?>
						<ul class="leftsec-contsec">
						<li>
							<div class="form_grid_12">
								<label class="field_title"><?php if ($this->lang->line('admin_templates_template_name') != '') echo stripslashes($this->lang->line('admin_templates_template_name')); else echo 'Template Name'; ?></label>
								<div class="form_input">
									<?php echo $template_details->row()->message['title'];?>
								</div>
							</div>
						</li>
						<li>
							<div class="form_grid_12">
								<label class="field_title"><?php if ($this->lang->line('admin_notification_notification_type') != '') echo stripslashes($this->lang->line('admin_notification_notification_type')); else echo 'Notification Type'; ?></label>
								<div class="form_input">
									<?php echo get_language_value_for_keyword(ucfirst($template_details->row()->notification_type),$this->data['langCode']);?>
								</div>
							</div>
						</li>
						<?php if($template_details->row()->notification_type ==='email'){ ?>
							
							<li>
								<div class="form_grid_12">
									<label class="field_title"><?php if ($this->lang->line('admin_common_submit') != '') echo stripslashes($this->lang->line('admin_common_submit')); else echo 'Subject'; ?> </label>
									<div class="form_input">
										<?php echo $template_details->row()->message['subject'];?>
									</div>
								</div>
							</li>
							
							<li>
								<div class="form_grid_12">
									<label class="field_title"><?php if ($this->lang->line('admin_templates_sender_name') != '') echo stripslashes($this->lang->line('admin_templates_sender_name')); else echo 'Sender Name'; ?></label>
									<div class="form_input">
										<?php echo $template_details->row()->sender['name'];?>
									</div>
								</div>
							</li>
							
							<li>
								<div class="form_grid_12">
									<label class="field_title"><?php if ($this->lang->line('admin_templates_sender_email_address') != '') echo stripslashes($this->lang->line('admin_templates_sender_email_address')); else echo 'Sender Email Address'; ?> </label>
									<div class="form_input">
										<?php echo $template_details->row()->sender['email'];?>
									</div>
								</div>
							</li>
							
							<li>
								<div class="form_grid_12">
									<label class="field_title"><?php if ($this->lang->line('admin_notification_mail_description') != '') echo stripslashes($this->lang->line('admin_notification_mail_description')); else echo 'Mail Description'; ?></label>
									<div class="form_input">
									<?php echo $template_details->row()->message['mail_description'];?>
									</div>
								</div>
							</li>
	 							
							<li>
								<div class="form_grid_12">
									<div class="form_input">
										<a href="<?php echo ADMIN_ENC_URL;?>/notification/display_notification_templates" class="tipLeft" title="<?php if ($this->lang->line('admin_newsletter_template_list') != '') echo stripslashes($this->lang->line('admin_newsletter_template_list')); else echo 'Go to email templates list'; ?>">
											<span class="badge_style b_done"><?php if ($this->lang->line('admin_common_back') != '') echo stripslashes($this->lang->line('admin_common_back')); else echo 'Back'; ?></span>
										</a>
									</div>
								</div>
							</li>
							<?php } else  if($template_details->row()->notification_type ==='notification'){ ?>
							 <li>
								<div class="form_grid_12">
									<label class="field_title"><?php if ($this->lang->line('admin_notification_notification_description') != '') echo stripslashes($this->lang->line('admin_notification_notification_description')); else echo 'Notification Description'; ?></label>
									<div class="form_input">
										<?php echo $template_details->row()->message['msg_description'];?>
									</div>
								</div>
							</li>
							<?php if(isset($template_details->row()->message['image'])){ ?>
							<li>
							<div class="form_grid_12">
								<label class="field_title"><?php if ($this->lang->line('admin_notification_notification_image') != '') echo stripslashes($this->lang->line('admin_notification_notification_image')); else echo 'Notification Image'; ?></label>
								<div class="form_input">
									
									
					<img src="<?php echo base_url()."images/notification/".$template_details->row()->message['image']; ?>" name="notify_image" style="height:150px;width:150px;" >
									
									</div>
								</div>
							</li>
							<?php } ?>
							<?php } else { ?>
								 <li>
								<div class="form_grid_12">
									<label class="field_title"><?php if ($this->lang->line('admin_message_text') != '') echo stripslashes($this->lang->line('admin_message_text')); else echo 'Message Text'; ?></label>
									<div class="form_input">
											<?php echo $template_details->row()->message['sms_description'];?>
									</div>
								</div>
							</li>
							<?php } ?>
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