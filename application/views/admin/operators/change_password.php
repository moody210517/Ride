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
						$attributes = array('class' => 'form_container left_label', 'id' => 'regitstraion_form', 'enctype' => 'multipart/form-data');
						echo form_open_multipart(ADMIN_ENC_URL.'/operators/change_password',$attributes);
						
						$operator_details = $operator_details->row();  
					?>
	 						<ul class="leftsec-contsec">
	 							<li>
								<div class="form_grid_12">
									<label class="field_title"><?php if ($this->lang->line('admin_operator_email') != '') echo stripslashes($this->lang->line('admin_operator_email')); else echo 'Operator Email'; ?> <span class="req">*</span></label>
									<div class="form_input">
										<input name="operator_email" id="operator_email" type="text"  class="required large tipTop"  value="<?php if(isset($operator_details->email)) echo $operator_details->email; ?>" disabled="disabled" />
									</div>
								</div>
								</li>
								
								<li>
								<div class="form_grid_12">
									<label class="field_title"><?php if ($this->lang->line('admin_drivers_new_password') != '') echo stripslashes($this->lang->line('admin_drivers_new_password')); else echo 'New Password'; ?> <span class="req">*</span></label>
									<div class="form_input">
										<input name="new_password" id="new_password" type="password"  class="required large tipTop" title="<?php if ($this->lang->line('dash_enter_new_password') != '') echo stripslashes($this->lang->line('dash_enter_new_password')); else echo 'Please enter a new password'; ?>"/>
									</div>
								</div>
								</li>
								<li>
								<div class="form_grid_12">
									<label class="field_title"><?php if ($this->lang->line('form_validation_confirm_password') != '') echo stripslashes($this->lang->line('form_validation_confirm_password')); else echo 'Retype Password'; ?> <span class="req">*</span></label>
									<div class="form_input">
										<input name="confirm_password" id="confirm_password" type="password"  class="required large tipTop" title="<?php if ($this->lang->line('dash_reenter_password_again') != '') echo stripslashes($this->lang->line('dash_reenter_password_again')); else echo 'Please enter a new password'; ?>"/>
									</div>
								</div>
								</li>
								<input name="operator_id" type="hidden" value="<?php echo $operator_details->_id; ?>" />
								
							</ul>
							<ul class="admin-pass">
							<li class="change-pass">
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