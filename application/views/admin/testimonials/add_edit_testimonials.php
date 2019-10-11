<?php
$this->load->view(ADMIN_ENC_URL.'/templates/header.php');
?>
	
<div id="content" class="add-new-docum">
	<div class="grid_container">
		<div class="grid_12">
			<div class="widget_wrap">
				<div class="widget_top">
					<span class="h_icon list"></span>
					<h6><?php echo $heading; ?></h6>
				</div>
				<div class="widget_content">
				<?php 
					$attributes = array('class' => 'form_container left_label', 'id' => 'addEditvehicle_form');
					echo form_open(ADMIN_ENC_URL.'/testimonials/insertEditTestimonials',$attributes) 
				?> 		
				<div class="base-appsec">
						<ul class="docum-base">							
							<li>
								<div class="form_grid_12">
									<label class="field_title"><?php if ($this->lang->line('admin_testimonials_title') != '') echo stripslashes($this->lang->line('admin_testimonials_title')); else echo 'Title'; ?> <span class="req">*</span></label>
									<div class="form_input">
									<input name="title" id="title" type="text"  class="large required tipTop" value="<?php if($form_mode){ echo $testimonialsdetails->row()->title; } ?>"/>
									</div>
								</div>
							</li>							
							<li>
								<div class="form_grid_12">
									<label class="field_title"><?php if ($this->lang->line('admin_testimonial_description') != '') echo stripslashes($this->lang->line('admin_testimonial_description')); else echo 'Description'; ?> <span class="req">*</span></label>
									<div class="form_input">
									<textarea name="description" id="description" type="text"  class="large required tipTop" minlength="10" maxlength="1000" ><?php if($form_mode){ echo $testimonialsdetails->row()->description; } ?></textarea>
									</div>
								</div>
							</li>							
							
							<li>
								<div class="form_grid_12">
									<label class="field_title"><?php if ($this->lang->line('admin_common_status') != '') echo stripslashes($this->lang->line('admin_common_status')); else echo 'Status'; ?></label>
									<div class="form_input">
										<div class="active_inactive">
											<input type="checkbox"  name="status" id="active_inactive_active" class="active_inactive" <?php if($form_mode){ if ($testimonialsdetails->row()->status == 'Active'){echo 'checked="checked"';} }else{echo 'checked="checked"';} ?>/>
										</div>
									</div>
								</div>
							</li>
							
						</ul>
						
						</div>
						
						<ul class="last-btn-submit">
							<li>
								<div class="form_grid_12">
									<div class="form_input">
										<input type="hidden" name="testimonials_id" value="<?php if($form_mode){ echo $testimonialsdetails->row()->_id; } ?>"/>
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