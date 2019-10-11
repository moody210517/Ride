<?php
$this->load->view(ADMIN_ENC_URL.'/templates/header.php');
?>
	<div id="content" class="add-operator-sec driver-bank">
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
						$attributes = array('class' => 'form_container left_label', 'id' => 'addeditdriverbank_form','method'=>'POST','autocomplete'=>'off','enctype' => 'multipart/form-data');
						echo form_open(ADMIN_ENC_URL.'/drivers/insertEditDriverBanking', $attributes)
						?>
							<input type="hidden" name="driver_id" id="driver_id" value="<?php if($form_mode){ echo $driver_details->row()->_id; } ?>"  />
							<div>
								<ul class="operator-sec-bar">
									<li>
										<div class="form_grid_12">
											<label class="field_title"><?php if ($this->lang->line('admin_drivers_account_holder_name') != '') echo stripslashes($this->lang->line('admin_drivers_account_holder_name')); else echo 'Account holder name'; ?> <span class="req">*</span></label>
											<div class="form_input">
												<input name="acc_holder_name" id="acc_holder_name" type="text"  class="large tipTop required" title="<?php if ($this->lang->line('dash_enter_account_holder_name') != '') echo stripslashes($this->lang->line('dash_enter_account_holder_name')); else echo 'Please enter account holder name'; ?>" value="<?php if($form_mode){ if(isset($driver_details->row()->banking['acc_holder_name'])) echo htmlentities($driver_details->row()->banking['acc_holder_name']); } ?>"/>
											</div>
										</div>
									</li>
									<li>
										<div class="form_grid_12">
											<label class="field_title"><?php if ($this->lang->line('admin_drivers_account_holder_address') != '') echo stripslashes($this->lang->line('admin_drivers_account_holder_address')); else echo 'Account holder address'; ?> <span class="req">*</span></label>
											<div class="form_input">
												<textarea name="acc_holder_address" id="acc_holder_address" type="text"  class="large tipTop required" title="<?php if ($this->lang->line('dash_enter_account_holder_address') != '') echo stripslashes($this->lang->line('dash_enter_account_holder_address')); else echo 'Please enter account holder address'; ?>" style="width:50%;"><?php if($form_mode){ if(isset($driver_details->row()->banking['acc_holder_address'])) echo htmlentities($driver_details->row()->banking['acc_holder_address']); } ?></textarea>
											</div>
										</div>
									</li>
									<li>
										<div class="form_grid_12">
											<label class="field_title"><?php if ($this->lang->line('admin_drivers_account_number') != '') echo stripslashes($this->lang->line('admin_drivers_account_number')); else echo 'Account number'; ?> <span class="req">*</span></label>
											<div class="form_input">
												<input name="acc_number" id="acc_number" type="text"  class="large tipTop required" title="<?php if ($this->lang->line('dash_enter_account_number') != '') echo stripslashes($this->lang->line('dash_enter_account_number')); else echo 'Please enter Account number'; ?>" value="<?php if($form_mode){ if(isset($driver_details->row()->banking['acc_number'])) echo htmlentities($driver_details->row()->banking['acc_number']); } ?>"/>
											</div>
										</div>
									</li>
									<li>
										<div class="form_grid_12">
											<label class="field_title"><?php if ($this->lang->line('admin_drivers_bank_name') != '') echo stripslashes($this->lang->line('admin_drivers_bank_name')); else echo 'Bank Name'; ?> <span class="req">*</span></label>
											<div class="form_input">
												<input name="bank_name" id="bank_name" type="text"  class="large tipTop required" title="<?php if ($this->lang->line('dash_enter_bank_name') != '') echo stripslashes($this->lang->line('dash_enter_bank_name')); else echo 'Please enter bank name'; ?>" value="<?php if($form_mode){ if(isset($driver_details->row()->banking['bank_name'])) echo htmlentities($driver_details->row()->banking['bank_name']); } ?>"/>
											</div>
										</div>
									</li>
									</ul>
									<ul class="operator-log-rite">
									
									<li>
										<div class="form_grid_12">
											<label class="field_title"><?php if ($this->lang->line('admin_drivers_branch_name') != '') echo stripslashes($this->lang->line('admin_drivers_branch_name')); else echo 'Branch Name'; ?> <span class="req">*</span></label>
											<div class="form_input">
												<input name="branch_name" id="branch_name" type="text"  class="large tipTop required" title="<?php if ($this->lang->line('dash__enter_branch_name') != '') echo stripslashes($this->lang->line('dash__enter_branch_name')); else echo 'Please enter Branch Name'; ?>" value="<?php if($form_mode){ if(isset($driver_details->row()->banking['branch_name'])) echo htmlentities($driver_details->row()->banking['branch_name']); } ?>"/>
											</div>
										</div>
									</li>
									<li>
										<div class="form_grid_12">
											<label class="field_title"><?php if ($this->lang->line('admin_drivers_branch_address') != '') echo stripslashes($this->lang->line('admin_drivers_branch_address')); else echo 'Branch address'; ?>  <span class="req">*</span></label>
											<div class="form_input">
												<textarea name="branch_address" id="branch_address" type="text"  class="large tipTop required" title="<?php if ($this->lang->line('dash_enter_branch_address') != '') echo stripslashes($this->lang->line('dash_enter_branch_address')); else echo 'Please enter Branch address'; ?>" style="width:50%;"><?php if($form_mode){ if(isset($driver_details->row()->banking['branch_address'])) echo htmlentities($driver_details->row()->banking['branch_address']); } ?></textarea>
											</div>
										</div>
									</li>		
									<li>
										<div class="form_grid_12">
											<label class="field_title"><?php if ($this->lang->line('admin_drivers_swift') != '') echo stripslashes($this->lang->line('admin_drivers_swift')); else echo 'Swift'; ?>/ <?php if ($this->lang->line('admin_ifsc_code') != '') echo stripslashes($this->lang->line('admin_ifsc_code')); else echo 'ifsc code'; ?></label>
											<div class="form_input">
												<input name="swift_code" id="swift_code" type="text"  class="large tipTop" title="<?php if ($this->lang->line('dash_enter_swift_code') != '') echo stripslashes($this->lang->line('dash_enter_swift_code')); else echo 'Please enter Swift Code'; ?>" value="<?php if($form_mode){ if(isset($driver_details->row()->banking['swift_code'])) echo htmlentities($driver_details->row()->banking['swift_code']); } ?>"/>
											</div>
										</div>
									</li>		
									<li>
										<div class="form_grid_12">
											<label class="field_title"><?php if ($this->lang->line('admin_drivers_routing_number') != '') echo stripslashes($this->lang->line('admin_drivers_routing_number')); else echo 'Routing Number'; ?></label>
											<div class="form_input">
												<input name="routing_number" id="routing_number" type="text"  class="large tipTop" title="<?php if ($this->lang->line('dash_enter_routing_number') != '') echo stripslashes($this->lang->line('dash_enter_routing_number')); else echo 'Please enter Routing Number'; ?>" value="<?php if($form_mode){ if(isset($driver_details->row()->banking['routing_number'])) echo htmlentities($driver_details->row()->banking['routing_number']); } ?>"/>
											</div>
										</div>
									</li>									
									
									
									
								</ul>
								<ul class="last-btn-submit">
								<li>
										<div class="form_grid_12">
											<div class="form_input">
												<button type="submit" class="btn_small btn_blue" ><span><?php if ($this->lang->line('admin_common_submit') != '') echo stripslashes($this->lang->line('admin_common_submit')); else echo 'Submit'; ?></span></button>
											</div>
										</div>
									</li>
								</ul>
						   </div>
					   
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