<div id="content" class="admin-settings">
		<div class="grid_container">
			<div class="grid_12">
				<div class="widget_wrap">
					<div class="widget_top">
						<span class="h_icon list"></span>
						<h6><?php if ($this->lang->line('admin_menu_change_password') != '') echo stripslashes($this->lang->line('admin_menu_change_password')); else echo 'Change Password'; ?></h6>
					</div>
	
					<div class="widget_content chenge-pass-base">
					<?php 
						$attributes = array('class' => 'form_container left_label', 'id' => 'regitstraion_form');
						echo form_open(COMPANY_NAME.'/login/change_password',$attributes) 
					?>
	 						<ul class="leftsec-contsec">
								<li>
								<div class="form_grid_12">
									<label class="field_title"><?php if ($this->lang->line('admin_change_password_current_password') != '') echo stripslashes($this->lang->line('admin_change_password_current_password')); else echo 'Current Password'; ?> <span class="req">*</span></label>
									<div class="form_input">
										<input name="password" id="password" type="password"  class="required large tipTop" title="<?php if ($this->lang->line('dash_enter_current_password') != '') echo stripslashes($this->lang->line('dash_enter_current_password')); else echo 'Please enter the current password'; ?>"/>
									</div>
								</div>
								</li>
								<li>
								<div class="form_grid_12">
									<label class="field_title"><?php if ($this->lang->line('admin_change_password_new_password') != '') echo stripslashes($this->lang->line('admin_change_password_new_password')); else echo 'New Password'; ?> <span class="req">*</span></label>
									<div class="form_input">
										<input name="new_password" id="new_password" type="password"  class="required large tipTop rangelength" title="<?php if ($this->lang->line('dash_enter_new_password') != '') echo stripslashes($this->lang->line('dash_enter_new_password')); else echo 'Please enter a new password'; ?>"/>
									</div>
								</div>
								</li>
								<li>
								<div class="form_grid_12">
									<label class="field_title"><?php if ($this->lang->line('admin_change_password_retype_password') != '') echo stripslashes($this->lang->line('admin_change_password_retype_password')); else echo 'Retype Password'; ?> <span class="req">*</span></label>
									<div class="form_input">
										<input name="confirm_password" id="confirm_password" type="password"  class="required large tipTop rangelength" title="<?php if ($this->lang->line('dash_reenter_password_again') != '') echo stripslashes($this->lang->line('dash_reenter_password_again')); else echo 'Please re-enter your new password again'; ?>" equalto="#new_password"/>
									</div>
								</div>
								</li>
								</ul>
								<ul class="admin-pass">
													<li class="change-pass">
								
								<div class="form_grid_12">
									<div class="form_input">
										<button type="submit" class="btn_small btn_blue"><span><?php if ($this->lang->line('admin_change_password_change') != '') echo stripslashes($this->lang->line('admin_change_password_change')); else echo 'Change'; ?></span></button>
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