<?php
$this->load->view(ADMIN_ENC_URL.'/templates/header.php');
?>
<div id="content" class="add-operator-sec view-opt">
		<div class="grid_container">
				<div class="grid_12">
						<div class="widget_wrap">
								<div class="widget_wrap tabby">
										<div class="widget_top"> 
												<span class="h_icon list"></span>
												<h6><?php if ($this->lang->line('admin_drivers_global_site_configuration') != '') echo stripslashes($this->lang->line('admin_drivers_global_site_configuration')); else echo 'Global Site Configuration'; ?></h6>
										</div>
										<div class="widget_content">
												<?php 
												$attributes = array('class' => 'form_container left_label ajaxsubmit', 'id' => 'settings_form', 'enctype' => 'multipart/form-data');
												echo form_open_multipart(ADMIN_ENC_URL.'/adminlogin/admin_global_settings',$attributes);						
												$operator_details = $operator_details->row();											
												?>
												<ul class="operator-sec-bar">													
														<li>
																<div class="form_grid_12">
																		<label class="field_title"><?php if ($this->lang->line('admin_operator_name') != '') echo stripslashes($this->lang->line('admin_operator_name')); else echo 'Operator Name'; ?></label>
																		<div class="form_input">
																				<p><?php echo $operator_details->operator_name;?></p>
																		</div>
																</div>
														</li>
														
														<li>
																<div class="form_grid_12">
																		<label class="field_title"><?php if ($this->lang->line('admin_drivers_email_address') != '') echo stripslashes($this->lang->line('admin_drivers_email_address')); else echo 'Email Address'; ?> </label>
																		<div class="form_input">
																				<p>											
																				<?php if($isDemo){ ?>
																				<?php echo $dEmail; ?>
																				<?php }  else{ ?>
																				<?php echo $operator_details->email;?>
																				<?php } ?>
																				</p>											
																		</div>
																</div>
														</li>
							
														
														<li>
																<h3><?php if ($this->lang->line('admin_drivers_address_details') != '') echo stripslashes($this->lang->line('admin_drivers_address_details')); else echo 'Address Details'; ?></h3>
														</li>
														
														<li>
															<div class="form_grid_12">
																	<label class="field_title"><?php if ($this->lang->line('admin_drivers_address') != '') echo stripslashes($this->lang->line('admin_drivers_address')); else echo 'Address'; ?></label>
																	<div class="form_input">
																			<p><?php if(isset($operator_details->address['address'])) echo $operator_details->address['address']; ?></p>
																	</div>
															</div>
														</li>
														
														<li>
																<div class="form_grid_12">
																		<label class="field_title"><?php if ($this->lang->line('admin_drivers_country') != '') echo stripslashes($this->lang->line('admin_drivers_country')); else echo 'Country'; ?></label>
																		<div class="form_input">
																				<p><?php echo $operator_details->address['country']; ?></p>
																		</div>
																</div>
														</li>
														</ul>
														<ul class="operator-log-rite">
														<li>
																<div class="form_grid_12">
																		<label class="field_title"><?php if ($this->lang->line('admin_drivers_state_province_region') != '') echo stripslashes($this->lang->line('admin_drivers_state_province_region')); else echo 'State / Province / Region'; ?></label>
																		<div class="form_input">
																				<p><?php if(isset($operator_details->address['state'])) echo $operator_details->address['state']; ?></p>
																		</div>
																</div>
														</li>
														
														<li>
																<div class="form_grid_12">
																		<label class="field_title"><?php if ($this->lang->line('admin_drivers_city') != '') echo stripslashes($this->lang->line('admin_drivers_city')); else echo 'City'; ?></label>
																		<div class="form_input">
																				<p><?php if(isset($operator_details->address['city'])) echo $operator_details->address['city']; ?></p>
																		</div>
																</div>
														</li>

														<li>
																<div class="form_grid_12">
																		<label class="field_title"><?php if ($this->lang->line('admin_drivers_postal_code') != '') echo stripslashes($this->lang->line('admin_drivers_postal_code')); else echo 'Postal Code'; ?></label>
																		<div class="form_input">
																				<p><?php if(isset($operator_details->address['postal_code'])) echo $operator_details->address['postal_code']; ?></p>
																		</div>
																</div>
														</li>

														<li>
																<div class="form_grid_12">
																		<label class="field_title"><?php if ($this->lang->line('admin_drivers_mobile_number') != '') echo stripslashes($this->lang->line('admin_drivers_mobile_number')); else echo 'Mobile Number'; ?></label>
																		<div class="form_input">
																				<p><?php if(isset($operator_details->dail_code)) echo $operator_details->dail_code; ?>
																				<?php if(isset($operator_details->mobile_number)) echo $operator_details->mobile_number; ?></p>
																		</div>
																</div>
														</li>
														
														<li>
																<div class="form_grid_12">
																		<label class="field_title"><?php if ($this->lang->line('admin_subadmin_status') != '') echo stripslashes($this->lang->line('admin_subadmin_status')); else echo 'Status'; ?></label>
																		<div class="form_input">
																				<p><?php if(isset($operator_details->status)) echo get_language_value_for_keyword($operator_details->status,$this->data['langCode']); ?></p>
																		</div>
																</div>
														</li>
													
												</ul>
													
												<ul class="last-btn-submit">
														<li>
																<div class="form_grid_12">
																		<div class="form_input">
																				<a  href="<?php echo ADMIN_ENC_URL;?>/operators/display_operators_list" class="btn_small btn_blue" ><span><?php if ($this->lang->line('admin_operators_back_to_operator_list') != '') echo stripslashes($this->lang->line('admin_operators_back_to_operator_list')); else echo 'Back To Operators List'; ?></span></a>
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
</div>

<?php 
$this->load->view(ADMIN_ENC_URL.'/templates/footer.php');
?>
