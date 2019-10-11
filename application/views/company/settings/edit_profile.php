<?php
$this->load->view(COMPANY_NAME.'/templates/header.php');
?> 
<style>
label.error {
 float:right;
}
</style>
	<div id="content" class="admin-settings profile_set_panel edit-global-set">
		<div class="grid_container">
			<div class="grid_12">
				<div class="widget_wrap">
					<div class="widget_top">
						<span class="h_icon list"></span>
						<h6><?php echo $heading; ?></h6>
                        <div id="widget_tab">
            			</div>
					</div>  	
					<div class="widget_content chenge-pass-base">  
						<form id="add_edit_company" autocomplete="off" method="POST" action="<?php echo COMPANY_NAME; ?>/login/insertEditcompanyprofile" class="form_container left_label form_stylee" enctype="multipart/form-data">
							<ul class="left-contsec pro_editing">
								<li>
									<div class="form_grid_12">
										<label class="field_title" id="lemail"><?php if ($this->lang->line('admin_partner_email_address') != '') echo stripslashes($this->lang->line('admin_partner_email_address')); else echo 'Email Address'; ?><span class="req">*</span></label>
										<div class="form_input">
											<input id="email" name="email" type="text" value="<?php if(isset($companydetail->row()->email)){ echo $companydetail->row()->email; }else{echo "not available";} ?>" maxlength="150" class="large required email" />
											
										</div>
									</div>
								</li>
								
							<?php if(!$form_mode){ ?>
								
								<li>
									<div class="form_grid_12">
										<label class="field_title" id="lpassword"><?php if ($this->lang->line('admin_partner_password') != '') echo stripslashes($this->lang->line('admin_partner_password')); else echo 'Password'; ?><span class="req">*</span></label>
										<div class="form_input">
											<input id="password" name="password" type="password" value="<?php if($form_mode){ echo $companydetail->row()->email; } ?>" maxlength="150" class="large required"/>
										</div>
									</div>
								</li>
								<li>
                            <div class="form_grid_12">
                                <label class="field_title"><?php if ($this->lang->line('admin_partner_confirm_password') != '') echo stripslashes($this->lang->line('admin_partner_confirm_password')); else echo 'confirm Password'; ?><span class="req">*</span></label>
                                <div class="form_input">
                                    <input name="confirm_password" minlength="5" equalTo="#password" id="confirm_password" type="password"  class="required required large tipTop" title="Please re-type the above password"/>
                                </div>
                            </div>
                        </li>

							<?php } ?>

								<li>
									<div class="form_grid_12">
										<label class="field_title" id="lfirstname"><?php if ($this->lang->line('admin_partner_company_name') != '') echo stripslashes($this->lang->line('admin_partner_company_name')); else echo 'Company Name'; ?><span class="req">*</span></label>
										<div class="form_input">
											<input id="firstname" name="CompanyName" type="text" value="<?php if($form_mode){ echo $companydetail->row()->company_name; } ?>" maxlength="100" class="large required"/>
										</div>
									</div>
								</li>  
								

								
								<li>
									<div class="form_grid_12">
										<label class="field_title" id="llastname"><?php if ($this->lang->line('admin_partner_phone_number') != '') echo stripslashes($this->lang->line('admin_partner_phone_number')); else echo 'Phone Number'; ?><span class="req">*</span></label>
										<div class="form_input">
											<input id="lastname" name="phonenumber" type="text" value="<?php if($form_mode){ echo $companydetail->row()->phonenumber; } ?>"  class="large required number">
										</div>
									</div>
								</li>
								
								<li>
									<div class="form_grid_12">
										<label class="field_title" id="lusername"><address></address><?php if ($this->lang->line('admin_partner_address') != '') echo stripslashes($this->lang->line('admin_partner_address')); else echo 'Address'; ?><span class="req">*</span></label>
										<div class="form_input">
										<textarea rows="5" name="address" id="address" class="large required"   style="width:50%;"><?php if($form_mode){ if(isset($companydetail->row()->locality['address'])) echo $companydetail->row()->locality['address']; } ?></textarea>
										</div>
									</div>
								</li>
								
								</ul>
								
								<ul class="rite-contsec pro_editing">
								<li>
									<div class="form_grid_12">
										<label class="field_title"  id="lusername"><?php if ($this->lang->line('admin_partner_city') != '') echo stripslashes($this->lang->line('admin_partner_city')); else echo 'City'; ?><span class="req">*</span></label>
										<div class="form_input">
											  <input class="field large required" type="text" id="locality" name="city" value="<?php if($form_mode){ if(isset($companydetail->row()->locality['city'])) echo $companydetail->row()->locality['city']; } ?>"></input></td>
										</div>
									</div>
								</li>
								<li>
									<div class="form_grid_12">
										<label class="field_title" id="lusername"><?php if ($this->lang->line('admin_partner_state') != '') echo stripslashes($this->lang->line('admin_partner_state')); else echo 'State'; ?><span class="req">*</span></label>
										<div class="form_input">
											<td class="slimField">
											<input type="text" class="field large required" name="state" id="administrative_area_level_1" value="<?php if($form_mode){ if(isset($companydetail->row()->locality['state'])) echo $companydetail->row()->locality['state']; } ?>" ></input></td>
										</div>
									</div>
								</li>
										   

              			
						
							<li>
                            <div class="form_grid_12">
                                <label class="field_title"><?php if ($this->lang->line('admin_partner_country') != '') echo stripslashes($this->lang->line('admin_partner_country')); else echo 'Country'; ?><span class="req">*</span></label>
                                <div class="form_input">
                                    <select name="country" id="county"  class="required chzn-select" style="height: 31px; width: 51%;">
										<option value=""><?php if ($this->lang->line('admin_partner_please_select_country') != '') echo stripslashes($this->lang->line('admin_partner_please_select_country')); else echo 'Please select country...'; ?></option>
                                        <?php foreach ($countryList as $country) { ?>
                                            <option value="<?php echo $country->name; ?>"  <?php 
											if($form_mode && isset($companydetail->row()->locality['country'])){
											if($country->name == $companydetail->row()->locality['country']){ echo 'selected="selected"'; } }  ?>><?php echo $country->name; ?></option>
                                        <?php } ?>
                                    </select>
                                </div>
                            </div>
                        </li>	
								<li>
									<div class="form_grid_12">
										<label class="field_title" id="lusername"><?php if ($this->lang->line('admin_partner_zipcode') != '') echo stripslashes($this->lang->line('admin_partner_zipcode')); else echo 'zip code'; ?><span class="req">*</span></label>
										<div class="form_input">
											<td class="wideField">
											<input type="text"class="field required large" name="zipcode" id="postal_code"  value="<?php if($form_mode){ if(isset($companydetail->row()->locality['zipcode'])) echo $companydetail->row()->locality['zipcode']; } ?>"></input></td>
      </tr>
										</div>
									</div>
								</li>

								
								
								
							</ul>
							
							<ul class="admin-pass">
																	<li class="change-pass">
								<div class="form_grid_12">
									<div class="form_input">
										<input type="hidden" name="operators_id" id="operators_id" value="<?php if($form_mode){ echo $companydetail->row()->_id; } ?>"  />
										<button type="submit" class="btn_small btn_blue" ><span><?php if ($this->lang->line('admin_partner_submit') != '') echo stripslashes($this->lang->line('admin_partner_submit')); else echo 'Submit'; ?></span></button>
									</div>
								</div>
								</li>
								
						</form>
					</div>
				</div>
			</div>
		</div>
		<span class="clear"></span>
	</div>
	</div>
<script>
$.validator.setDefaults({ ignore: ":hidden:not(select)" });
$('#add_edit_company').validate();
</script>


<?php 
$this->load->view(COMPANY_NAME.'/templates/footer.php');
?>