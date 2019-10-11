<?php
$this->load->view(ADMIN_ENC_URL.'/templates/header.php');
?>
<div id="content" class="admin-settings">
		<div class="grid_container">
			<div class="grid_12">
				<div class="widget_wrap">
					<div class="widget_top">
						<span class="h_icon list"></span>
						<h6><?php echo $heading; ?></h6>
					</div>
					<div class="widget_content chenge-pass-base">
					<?php 
						$attributes = array('class' => 'form_container left_label', 'id' => 'addeditcurrency_form');
						echo form_open(ADMIN_ENC_URL.'/reviews/insertEditReviews_options',$attributes) 
					?> 		
	 						<ul class="leftsec-contsec">								
                                <li>
									<div class="form_grid_12">
										<label class="field_title"><?php if ($this->lang->line('admin_review_review_option_for') != '') echo stripslashes($this->lang->line('admin_review_review_option_for')); else echo 'Review Option For'; ?>? <span class="req">*</span></label>
										<div class="form_input">
										<select name="option_holder" id="option_holder" class="required" style="width: 50%;">
											<option value=""><?php if ($this->lang->line('admin_review_choose_the_option_for') != '') echo stripslashes($this->lang->line('admin_review_choose_the_option_for')); else echo 'Choose the option for ?'; ?></option>
											<option value="driver" <?php if(isset($reviewsdetails->row()->option_name)) if($reviewsdetails->row()->option_holder == 'driver') echo 'selected';?>><?php if ($this->lang->line('admin_common_driver') != '') echo stripslashes($this->lang->line('admin_common_driver')); else echo 'Driver'; ?></option>
											<option value="rider" <?php  if(isset($reviewsdetails->row()->option_name)) if($reviewsdetails->row()->option_holder == 'rider') echo 'selected';?>>
											<?php if ($this->lang->line('admin_common_rider') != '') echo stripslashes($this->lang->line('admin_common_rider')); else echo 'Rider'; ?></option>
										</select>
										</div>
									</div>
								</li>							
                                
								<li>
									<div class="form_grid_12">
										<label class="field_title"><?php if ($this->lang->line('admin_review_review_option_name') != '') echo stripslashes($this->lang->line('admin_review_review_option_name')); else echo 'Review Option Name'; ?><span class="req">*</span></label>
										<div class="form_input">
										<input name="option_name" id="option_name" type="text"  class="large required tipTop" title="<?php if ($this->lang->line('admin_review_enter_review_option_name') != '') echo stripslashes($this->lang->line('admin_review_enter_review_option_name')); else echo 'Please enter review option name'; ?>" value="<?php if(isset($reviewsdetails->row()->option_name)) echo $reviewsdetails->row()->option_name;?>" />
										</div>
									</div>
								</li>			

								<li>
									<div class="form_grid_12">
										<label class="field_title">
										<?php if ($this->lang->line('admin_common_status') != '') echo stripslashes($this->lang->line('admin_common_status')); else echo 'Status'; ?></label>
										<div class="form_input">
											<div class="active_inactive">
												<input type="checkbox"   name="status" id="active_inactive_active" class="active_inactive" <?php   if(isset($reviewsdetails->row()->status)) if($reviewsdetails->row()->status == 'Active'){ echo 'checked="checked"';}  ?> />
											</div>
										</div>
									</div>
								</li>
								
								<input type="hidden" name="option_id"  value="<?php echo $reviewsdetails->row()->_id; ?>"/>
								
							</ul>
							
							<ul class="admin-pass">
							<li class="change-pass">
									<div class="form_grid_12">
										<div class="form_input">
											<button type="submit" class="btn_small btn_blue" ><span>
											<?php if ($this->lang->line('admin_common_submit') != '') echo stripslashes($this->lang->line('admin_common_submit')); else echo 'Submit'; ?></span></button>
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