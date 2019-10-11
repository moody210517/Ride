<?php
$this->load->view(ADMIN_ENC_URL.'/templates/header.php');    
?> 
<div id="content" class="add_company_base">
  <div class="grid_container">
			<div class="grid_12">
				<div class="widget_wrap">
					<div class="widget_top">
						<span class="h_icon list"></span>
						<h6><?php echo $heading; ?></h6>
                        <div id="widget_tab">
            			</div>
					</div>  	
					<div class="widget_content">  
						<?php
						$attributes = array('class' => 'form_container left_label form_stylee', 'id' => 'add_edit_company','method'=>'POST','autocomplete'=>'off','enctype' => 'multipart/form-data');
						echo form_open(ADMIN_ENC_URL.'/company/insertEditcompanyprofile', $attributes)
						?>
							<ul class="left-add-company">
								<li>
									<div class="form_grid_12">
										<label class="field_title" id="lemail"><?php if ($this->lang->line('admin_partner_email_address') != '') echo stripslashes($this->lang->line('admin_partner_email_address')); else echo 'Email Address'; ?><span class="req">*</span></label>
										<div class="form_input">
											<input id="email" name="email" type="text" value="<?php if($form_mode){ echo $companydetails->row()->email; } ?>" maxlength="150" class="large required email tip_top" title="<?php if ($this->lang->line('please_enter_email_tooltip') != '') echo stripslashes($this->lang->line('please_enter_email_tooltip')); else echo 'Please Enter Email Address'; ?>"  />
										</div>
									</div>
								</li>
								
							<?php if(!$form_mode){ ?>
								
								<li>
									<div class="form_grid_12">
										<label class="field_title" id="lpassword"><?php if ($this->lang->line('admin_partner_password') != '') echo stripslashes($this->lang->line('admin_partner_password')); else echo 'Password'; ?><span class="req">*</span></label>
										<div class="form_input">
											<input id="password" name="password" minlength="6"  type="password" value="<?php if($form_mode){ echo $companydetails->row()->password; } ?>" maxlength="20" class="large rangelength required tip_top" title="<?php if ($this->lang->line('company_please_enter_password_tooltip') != '') echo stripslashes($this->lang->line('company_please_enter_password_tooltip')); else echo 'Please Enter Password'; ?>"  />
										</div>
									</div>
								</li>
								<li>
                            <div class="form_grid_12">
                                <label class="field_title"><?php if ($this->lang->line('admin_partner_confirm_password') != '') echo stripslashes($this->lang->line('admin_partner_confirm_password')); else echo 'confirm Password'; ?> <span class="req">*</span></label>
                                <div class="form_input">
                                    <input name="confirm_password" minlength="6" equalTo="#password" id="confirm_password" type="password"  class="required required large tip_top" title="<?php if ($this->lang->line('company_please_enter_confirm_password_tooltip') != '') echo stripslashes($this->lang->line('company_please_enter_confirm_password_tooltip')); else echo 'Please Confirm Your Password'; ?>" />
                                </div>
                            </div>
                        </li>

							<?php } ?>

								<li>
									<div class="form_grid_12">
										<label class="field_title" id="lfirstname"><?php if ($this->lang->line('admin_partner_company_name') != '') echo stripslashes($this->lang->line('admin_partner_company_name')); else echo 'Company Name'; ?><span class="req">*</span></label>
										<div class="form_input">
											<input id="firstname" name="CompanyName" type="text" value="<?php if($form_mode){ echo $companydetails->row()->company_name; } ?>" minlength="5" maxlength="100" class="large required tip_top" title="<?php if ($this->lang->line('company_please_enter_company_name_tooltip') != '') echo stripslashes($this->lang->line('company_please_enter_company_name_tooltip')); else echo 'Please Enter Company Name'; ?>"  />
										</div>
									</div>
								</li>  
                                
                                
                                <li>
                                    <div class="form_grid_12">
                                        <label class="field_title"><?php if ($this->lang->line('admin_partner_country') != '') echo stripslashes($this->lang->line('admin_partner_country')); else echo 'Country'; ?><span class="req">*</span></label>
                                        <div class="form_input rerr">
                                            <select name="county" id="county"  class="required chzn-select tip_top" title="<?php if ($this->lang->line('company_please_choose_country_tooltip') != '') echo stripslashes($this->lang->line('company_please_choose_country_tooltip')); else echo 'Please Choose Country'; ?>" style="height: 31px; width: 100%;">
                                                <option value="" hidden="hidden"><?php if ($this->lang->line('admin_partner_please_select_country') != '') echo stripslashes($this->lang->line('admin_partner_please_select_country')); else echo 'Please select country...'; ?></option>
                                                <?php foreach ($countryList as $country) {   ?>
                                                    <option value="<?php echo $country->name; ?>" data-dialCode="<?php echo $country->dial_code; ?>" 
													<?php if($form_mode && isset($companydetails->row()->address['country'])){ if($country->name == $companydetails->row()->address['country']){ 
															echo 'selected="selected"'; 
														} 
													} ?>><?php echo $country->name; ?></option>
                                                <?php } ?>
                                            </select>
                                        </div>
                                    </div>
                                </li>	
								
								<li>
									<div class="form_grid_12">
										<label class="field_title"><?php if ($this->lang->line('admin_partner_phone_number') != '') echo stripslashes($this->lang->line('admin_partner_phone_number')); else echo 'Phone Number'; ?><span class="req">*</span></label>
										<div class="form_input">
                                            <select name="dail_code" id="country_codeM"  class="required chzn-select1 small tipTop mCC" style="" title="<?php if ($this->lang->line('select_mobile_country_code') != '') echo stripslashes($this->lang->line('select_mobile_country_code')); else echo 'Please select mobile country code'; ?>">
                                                <?php foreach ($countryList as $country) {  ?>
                                                    <option value="<?php echo $country->dial_code; ?>" <?php if($country->dial_code==$d_country_code){ echo "selected='selected'"; } ?>><?php echo $country->cca3.' ('.$country->dial_code.')'; ?></option>
                                                <?php  } ?>  
                                            </select>
                                        
											<input id="lastname" name="phonenumber" type="text" value="<?php if($form_mode){ echo $companydetails->row()->phonenumber; } ?>"  class="large required tip_top phoneNumber" maxlength='20' title="<?php if ($this->lang->line('company_please_enter_phone_number_tooltip') != '') echo stripslashes($this->lang->line('company_please_enter_phone_number_tooltip')); else echo 'Please Enter Phone Number'; ?>"  />
										</div>
									</div>
								</li>
							</ul>
								
							<ul class="rite-add-company">
                            <li>
									<div class="form_grid_12">
										<label class="field_title" id="lusername"><address></address><?php if ($this->lang->line('admin_partner_address') != '') echo stripslashes($this->lang->line('admin_partner_address')); else echo 'Address'; ?><span class="req">*</span></label>
										<div class="form_input">
											 <textarea name="address" id="address" class="large required tip_top" title="<?php if ($this->lang->line('company_please_enter_address_tooltip') != '') echo stripslashes($this->lang->line('company_please_enter_address_tooltip')); else echo 'Please Enter Address'; ?>"   style="width:50%;"> <?php if($form_mode){ if(isset($companydetails->row()->locality['address'])) echo $companydetails->row()->locality['address']; } ?></textarea>
										</div>
									</div>
								</li>
								
								<li>
									<div class="form_grid_12">
										<label class="field_title"  id="lusername"><?php if ($this->lang->line('admin_partner_city') != '') echo stripslashes($this->lang->line('admin_partner_city')); else echo 'City'; ?><span class="req">*</span></label>
										<div class="form_input">
											  <input class="field large required tip_top" title="<?php if ($this->lang->line('company_please_enter_city_tooltip') != '') echo stripslashes($this->lang->line('company_please_enter_city_tooltip')); else echo 'Please Enter City'; ?>" type="text" id="locality" name="city" value="<?php if($form_mode){ if(isset($companydetails->row()->locality['city'])) echo $companydetails->row()->locality['city']; } ?>"  />
										</div>
									</div>
								</li>
								<li>
									<div class="form_grid_12">
										<label class="field_title" id="lusername"><?php if ($this->lang->line('admin_partner_state') != '') echo stripslashes($this->lang->line('admin_partner_state')); else echo 'State'; ?><span class="req">*</span></label>
										<div class="form_input">
											<input type="text" class="field large required tip_top" title="<?php if ($this->lang->line('company_please_enter_state_tooltip') != '') echo stripslashes($this->lang->line('company_please_enter_state_tooltip')); else echo 'Please Enter State'; ?>" name="state" id="administrative_area_level_1" value="<?php if($form_mode){ if(isset($companydetails->row()->locality['state'])) echo $companydetails->row()->locality['state']; } ?>"  />
										</div>
									</div>
								</li>
							
								<li>
									<div class="form_grid_12">
										<label class="field_title" id="lusername"><?php if ($this->lang->line('admin_drivers_postal_code') != '') echo stripslashes($this->lang->line('admin_drivers_postal_code')); else echo 'Postal Code'; ?><span class="req">*</span></label>
										<div class="form_input">
											<input type="text"class="field required large tip_top" title="<?php if ($this->lang->line('company_please_enter_postal_code_tooltip') != '') echo stripslashes($this->lang->line('company_please_enter_postal_code_tooltip')); else echo 'Please Enter Postal Code'; ?>" name="zipcode" id="postal_code"  value="<?php if($form_mode){ if(isset($companydetails->row()->locality['zipcode'])) echo $companydetails->row()->locality['zipcode']; } ?>"  />
										</div>
									</div>
								</li>
                                
								<li>
									<div class="form_grid_12">
										<label class="field_title"><?php if ($this->lang->line('admin_partner_status') != '') echo stripslashes($this->lang->line('admin_partner_status')); else echo 'Status'; ?> </label>
										<div class="form_input">
											<div class="active_inactive">
												<input type="checkbox"  name="status" id="active_inactive_active" class="active_inactive tip_top" title="<?php if ($this->lang->line('company_please_select_status_tooltip') != '') echo stripslashes($this->lang->line('company_please_select_status_tooltip')); else echo 'Please Select The Status'; ?>" <?php if($form_mode){ if ($companydetails->row()->status == 'Active'){echo 'checked="checked"';} }else{echo 'checked="checked"';} ?>/>
											</div>
										</div>
									</div>
								</li>
							</ul>
							
							<ul class="last-btn-submit">
								<li>
								<div class="form_grid_12">
									<div class="form_input">
										<input type="hidden" name="operators_id" id="operators_id" value="<?php if($form_mode){ echo $companydetails->row()->_id; } ?>"  />
										<button type="submit" class="btn_small btn_blue" ><span><?php if ($this->lang->line('admin_partner_submit') != '') echo stripslashes($this->lang->line('admin_partner_submit')); else echo 'Submit'; ?></span></button>
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
$(document).ready(function() {
	$.validator.setDefaults({ ignore: ":hidden:not(select)" });
	$("#add_edit_company").validate();
	$('#add_edit_company').on('submit', function() {
	$(".chzn-select").siblings(".error").appendTo($(".chzn-select").parent());
	$(".error").css("margin-right","0");
	});
     
     $("#county").change(function (e) { 
            var dail_code = $(this).find(':selected').attr('data-dialCode');   
            $('#country_codeM').val(dail_code).trigger('liszt:updated');
    });
});
</script>

<?php 
$this->load->view(ADMIN_ENC_URL.'/templates/footer.php');
?>