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
				<div class="widget_content">
					<?php 
						$attributes = array('class' => 'form_container left_label');
						echo form_open(ADMIN_ENC_URL,$attributes) 
					?>
						<ul>
							<li>
								<div class="form_grid_12">
									<label class="field_title"><?php if ($this->lang->line('admin_templates_template_name') != '') echo stripslashes($this->lang->line('admin_templates_template_name')); else echo 'Template Name'; ?></label>
									<div class="form_input">
										<?php echo $template_details->row()->template_name;?>
									</div>
								</div>
							</li>
							
							<li>
								<div class="form_grid_12">
									<label class="field_title"><?php if ($this->lang->line('admin_sms_template_message') != '') echo stripslashes($this->lang->line('admin_sms_template_message')); else echo 'Message Description'; ?></label>
									<div class="form_input">
										<?php echo $template_details->row()->description;?>
									</div>
								</div>
							</li>
							
							
						
							<li>
								<div class="form_grid_12">
									<div class="form_input">
										<a href="<?php echo ADMIN_ENC_URL;?>/templates/display_sms_templates" class="tipLeft" title="<?php if ($this->lang->line('admin_sms_template_list') != '') echo stripslashes($this->lang->line('admin_sms_template_list')); else echo 'Go to SMS templates list'; ?>">
											<span class="badge_style b_done"><?php if ($this->lang->line('admin_common_back') != '') echo stripslashes($this->lang->line('admin_common_back')); else echo 'Back'; ?></span>
										</a>
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
<?php 
$this->load->view(ADMIN_ENC_URL.'/templates/footer.php');
?>