

<style>


.left_label ul li .form_input{
	float: left;
    margin-left: 0;
}
.form_container ul li{
	width: 100%;
	display: inline-block;
	padding:10px 15px 0px 10px;
	border-bottom:none;
	background:none;
}
.form_container input[type="text"], input[type="email"], .form_container input[type="password"], .form_container textarea{
	padding: 8px 2px;
}
.left_label li.change-pass .form_input {
    margin-bottom: 15px;
    margin-left: 0;
}
.btn_blue{
	background-color: #383f52;
    border-color: #383f52;
	border-radius: 3px;
    box-shadow: none;
	cursor: pointer;
    display: inline-block;
    font-size: 14px;
    font-weight: 400;
    line-height: 1.42857 !important;
    margin-bottom: 0;
    padding: 6px 12px;
    text-align: center;
    vertical-align: middle;
    white-space: nowrap;
	color: #fff;
	height:auto;
	text-shadow: none;
	
}
.form_container ul li

.form_container ul li:last-child {
    background-image: none;
    border-bottom: medium none;
    border-top: 1px solid #f4f4f4;
    margin-top: 15px;
    width: 98%;
}


</style>

<div id="content" class="admin-settings password_chage">
		<div class="grid_container">
			<div class="grid_12">
				<div class="widget_wrap">
					<!--<div class="widget_top">
						<span class="h_icon list"></span>
						
					</div>-->
					<div class="widget_content chenge-pass-base cabily_pass">
					<div class="top_wrap_over">
					<h6><?php if ($this->lang->line('admin_menu_change_password') != '') echo stripslashes($this->lang->line('admin_menu_change_password')); else echo 'Change Password'; ?></h6>
					<?php 
						$attributes = array('class' => 'form_container left_label', 'id' => 'regitstraion_form');
						echo form_open(ADMIN_ENC_URL.'/adminlogin/change_admin_password',$attributes) 
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
										<input name="new_password" id="new_password" type="password"  class="required large tipTop" title="<?php if ($this->lang->line('dash_enter_new_password') != '') echo stripslashes($this->lang->line('dash_enter_new_password')); else echo 'Please enter a new password'; ?>"/>
									</div>
								</div>
								</li>
								<li>
								<div class="form_grid_12">
									<label class="field_title"><?php if ($this->lang->line('admin_change_password_retype_password') != '') echo stripslashes($this->lang->line('admin_change_password_retype_password')); else echo 'Retype Password'; ?> <span class="req">*</span></label>
									<div class="form_input">
										<input name="confirm_password" id="confirm_password" type="password"  class="required large tipTop" title="<?php if ($this->lang->line('dash_reenter_password_again') != '') echo stripslashes($this->lang->line('dash_reenter_password_again')); else echo 'Please re-enter your new password again'; ?>"/>
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
		</div>
		<span class="clear"></span>
	</div>
</div>