<?php
$this->load->view(ADMIN_ENC_URL.'/templates/header.php');
?>
<div id="content" class="add-user-list">
		<div class="grid_container">
				<div class="grid_12">
						<div class="widget_wrap">
								<div class="widget_top">
										<span class="h_icon list"></span>
										<h6><?php if ($this->lang->line('admin_rides_add_new_user') != '') echo stripslashes($this->lang->line('admin_rides_add_new_user')); else echo 'Add New User'; ?></h6>
								</div>
								<div class="widget_content">
										<?php 
										$attributes = array('class' => 'form_container left_label', 'id' => 'adduser_form', 'enctype' => 'multipart/form-data');
										echo form_open_multipart(ADMIN_ENC_URL.'/users/insertEditUser',$attributes) 
										?>
										<ul class="base-add-users">
												<li>
														<div class="form_grid_12">
																<label class="field_title"><?php if ($this->lang->line('admin_users_users_list_user_name') != '') echo stripslashes($this->lang->line('admin_users_users_list_user_name')); else echo 'User Name'; ?> <span class="req">*</span></label>
																<div class="form_input">
																		<input name="user_name" id="user_name" type="text"  class="required large tipTop alphanumeric" title="<?php if ($this->lang->line('user_enter_username') != '') echo stripslashes($this->lang->line('user_enter_username')); else echo 'Please enter the username'; ?>" value="<?php echo $this->input->post("user_name"); ?>"/>
																</div>
														</div>
												</li>
												<li>
														<div class="form_grid_12">
																<label class="field_title"><?php if ($this->lang->line('admin_users_email_address') != '') echo stripslashes($this->lang->line('admin_users_email_address')); else echo 'Email Address'; ?> <span class="req">*</span></label>
																<div class="form_input">
																		<input name="email" id="email" type="text"  class="required large tipTop email" title="<?php if ($this->lang->line('user_enter_user_email_address') != '') echo stripslashes($this->lang->line('user_enter_user_email_address')); else echo 'Please enter the user email address'; ?>"/>
																</div>
														</div>
												</li>
												<li>
														<div class="form_grid_12">
																<label class="field_title"><?php if ($this->lang->line('admin_drivers_mobile_number') != '') echo stripslashes($this->lang->line('admin_drivers_mobile_number')); else echo 'Mobile Number'; ?><span class="req">*</span></label>
																<div class="form_input">
																		<select name="country_code" id="country_codeM"  class="required chzn-select small tipTop mCC" style="" title="<?php if ($this->lang->line('select_mobile_country_code') != '') echo stripslashes($this->lang->line('select_mobile_country_code')); else echo 'Please select mobile country code'; ?>">
																			<?php foreach ($countryList as $country) { ?>
																				<option value="<?php echo $country->dial_code; ?>" <?php if($country->dial_code==$d_country_code){ echo "selected='selected'"; } ?>><?php echo $country->dial_code; ?></option>
																			<?php } ?>
																		</select>
																		<input name="phone_number" placeholder="<?php if ($this->lang->line('admin_drivers_mobile_number') != '') echo stripslashes($this->lang->line('admin_drivers_mobile_number')); else echo 'Mobile Number'; ?>" id="phone_number" type="text"  class="required medium tipTop phoneNumber" maxlength="20" title="<?php if ($this->lang->line('driver_enter_mobile_number') != '') echo stripslashes($this->lang->line('driver_enter_mobile_number')); else echo 'Please enter the mobile number'; ?>" style="vertical-align: top;"/>
																</div>
														</div>
												</li>
												<li>
														<div class="form_grid_12">
																<label class="field_title"><?php if ($this->lang->line('admin_users_users_list_user_image') != '') echo stripslashes($this->lang->line('admin_users_users_list_user_image')); else echo 'User Image'; ?></label>
																<div class="form_input">
																		<input name="thumbnail" id="thumbnail" type="file"  class="large tipTop" title="<?php if ($this->lang->line('user_select_user_image') != '') echo stripslashes($this->lang->line('user_select_user_image')); else echo 'Please select user image'; ?>"/>
																</div>
														</div>
												</li>
												<li>
														<div class="form_grid_12">
																<label class="field_title"><?php if ($this->lang->line('admin_common_status') != '') echo stripslashes($this->lang->line('admin_common_status')); else echo 'Status'; ?></label>
																<div class="form_input">
																		<div class="active_inactive">
																				<input type="checkbox"  name="status" checked="checked" id="active_inactive_active" class="active_inactive"/>
																		</div>
																</div>
														</div>
												</li>
												<input type="hidden" name="group" value="User" />
												<input type="hidden" name="user_id" value="" id="user_id" />
												
												
										</ul>
										
										<ul class="last-btn-submit">
											<li>
														<div class="form_grid_12">
																<div class="form_input">
																		<button type="button" onclick="custom_validate_form();" class="btn_small btn_blue" ><span><?php if ($this->lang->line('admin_common_submit') != '') echo stripslashes($this->lang->line('admin_common_submit')); else echo 'Submit'; ?></span>
																		</button><img id="form_loader" src="images/indicator.gif" style="display:none;"/>
																		<p class="error" id="user_duplicate_Err" style="margin-top: 12px;"></p>
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
<script>
/* $(function () {
	$.validator.setDefaults({ ignore: ":hidden:not(select)" });
	$("#adduser_form").validate();
}); */

$(document).ready(function(){
    $('#email,#phone_number').keypress(function(){
        $('#user_duplicate_Err').html('');
    });
});


function custom_validate_form(){
	if($("#adduser_form").valid()){
		var uEmail = $('#email').val();
		var uCC = $('#country_codeM').val();
		var uPN = $('#phone_number').val();
		var user_id = $('#user_id').val();
		$('#form_loader').css('display','inline-block');
		$.ajax({
			type:'post',
			url:'<?php echo ADMIN_ENC_URL;?>/users/check_user_duplicate',
			data:{'email':uEmail,'dial_code':uCC,'phone_number':uPN,'user_id':user_id},
			dataType: 'json',
			success:function(res){
				$('#form_loader').css('display','none');
				if(res.status == '1'){ 
					$('#adduser_form').submit();
				} else {
					$('#user_duplicate_Err').html(res.response);
				}
			}
		});
	}
}

</script>
<?php 
$this->load->view(ADMIN_ENC_URL.'/templates/footer.php');
?>