<?php
$this->load->view(ADMIN_ENC_URL.'/templates/header.php');
?>
<div id="content" class="admin-settings view_user_set">
	<div class="grid_container">
		<div class="grid_12">
			<div class="widget_wrap">
				<div class="widget_top">
					<span class="h_icon list"></span>
					<h6><?php echo $heading; ?></h6>
					
				</div>
				<div class="widget_content chenge-pass-base">
					<?php 
						$attributes = array('class' => 'form_container left_label');
						echo form_open(ADMIN_ENC_URL,$attributes) 
					?>
					<div id="tab1">
	 					<ul class="leftsec-contsec">
	 						
							<li>
								<div class="form_grid_12">
									<label class="field_title"><?php if ($this->lang->line('admin_testimonials_title') != '') echo stripslashes($this->lang->line('admin_testimonials_title')); else echo 'Title'; ?></label>
									<div class="form_input">
										<?php echo $testimonials_details->row()->title;?>
									</div>
								</div>
							</li>
								
	 						<li>
								<div class="form_grid_12">
									<label class="field_title"><?php if ($this->lang->line('admin_testimonial_description') != '') echo stripslashes($this->lang->line('admin_testimonial_description')); else echo 'Description'; ?></label>
									<div class="form_input">
										<?php echo $testimonials_details->row()->description;?>
										
									</div>
								</div>
							</li>
						</ul>
						
						<ul class="admin-pass back_view_user">
							<li class="change-pass">
								<div class="form_grid_12">
									<div class="form_input">
										<a href="<?php echo ADMIN_ENC_URL;?>/testimonials/display_testimonials_list" class="tipLeft" title="<?php if ($this->lang->line('admin_testimonial_go_back') != '') echo stripslashes($this->lang->line('admin_testimonial_go_back')); else echo 'Go to testimonials list'; ?>"><span class="badge_style b_done"><?php if ($this->lang->line('admin_location_and_fare_back') != '') echo stripslashes($this->lang->line('admin_location_and_fare_back')); else echo 'Back'; ?></span></a>
									</div>
								</div>
							</li>
						</ul>
					</div>
					
				</div>
			</div>
		</div>
		<span class="clear"></span>
	</div>
</div>
</div>
<?php 
$this->load->view(ADMIN_ENC_URL.'/templates/footer.php');
?>