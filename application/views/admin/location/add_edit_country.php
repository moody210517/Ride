<?php
$this->load->view(ADMIN_ENC_URL.'/templates/header.php');
?>
<div id="content">
		<div class="grid_container">
			<div class="grid_12">
				<div class="widget_wrap">
					<div class="widget_top">
						<span class="h_icon list"></span>
						<h6><?php echo $heading; ?></h6>
					</div>
					<div class="widget_content">
					<?php 
						$attributes = array('class' => 'form_container left_label', 'id' => 'addeditcountry_form');
						echo form_open(ADMIN_ENC_URL.'/location/insertEditCountry',$attributes) 
					?> 		
	 						<ul>								
                                <li>
									<div class="form_grid_12">
										<label class="field_title"><?php if ($this->lang->line('admin_location_and_fare_name') != '') echo stripslashes($this->lang->line('admin_location_and_fare_name')); else echo 'Name'; ?> <span class="req">*</span></label>
										<div class="form_input">
										<input name="name" id="name" type="text"  class="large required tipTop" title="<?php if ($this->lang->line('admin_location_enter_name') != '') echo stripslashes($this->lang->line('admin_location_enter_name')); else echo 'Please enter the name'; ?>" value="<?php if($form_mode){ echo $countrydetails->row()->name; } ?>"/>
										</div>
									</div>
								</li>							
                                <li>
									<div class="form_grid_12">
										<label class="field_title"><?php if ($this->lang->line('admin_location_and_fare_iso_code') != '') echo stripslashes($this->lang->line('admin_location_and_fare_iso_code')); else echo 'ISO Code'; ?> 2 <span class="req">*</span></label>
										<div class="form_input">
										<input name="cca2" id="cca2" type="text"  class="large required tipTop" title="<?php if ($this->lang->line('admin_location_enter_iso_code') != '') echo stripslashes($this->lang->line('admin_location_enter_iso_code')); else echo 'Please enter the ISO Code 2'; ?>" value="<?php if($form_mode){ echo $countrydetails->row()->cca2; } ?>"/>
										</div>
									</div>
								</li>							
                                <li>
									<div class="form_grid_12">
										<label class="field_title"><?php if ($this->lang->line('admin_location_and_fare_iso_code') != '') echo stripslashes($this->lang->line('admin_location_and_fare_iso_code')); else echo 'ISO Code'; ?> 3 <span class="req">*</span></label>
										<div class="form_input">
										<input name="cca3" id="cca3" type="text"  class="large required tipTop" title="<?php if ($this->lang->line('admin_location_enter_iso_code_three') != '') echo stripslashes($this->lang->line('admin_location_enter_iso_code_three')); else echo 'Please enter the ISO Code 3'; ?>" value="<?php if($form_mode){ echo $countrydetails->row()->cca3; } ?>"/>
										</div>
									</div>
								</li>							
                                <li>
									<div class="form_grid_12">
										<label class="field_title"><?php if ($this->lang->line('admin_location_and_fare_dial_code') != '') echo stripslashes($this->lang->line('admin_location_and_fare_dial_code')); else echo 'Dial Code'; ?> <span class="req">*</span></label>
										<div class="form_input">
										<input name="dial_code" id="dial_code" type="text"  class="large required tipTop" title="<?php if ($this->lang->line('admin_location_enter_dial_code') != '') echo stripslashes($this->lang->line('admin_location_enter_dial_code')); else echo 'Please enter the Dial Code'; ?>" value="<?php if($form_mode){ echo $countrydetails->row()->dial_code; } ?>"/>
										</div>
									</div>
								</li>	
								
                                <li>
									<div class="form_grid_12">
										<label class="field_title"><?php if ($this->lang->line('admin_location_and_fare_currency') != '') echo stripslashes($this->lang->line('admin_location_and_fare_currency')); else echo 'Currency'; ?> <span class="req">*</span></label>
										<div class="form_input">
											<select name="currency" id="currency"  class="required chzn-select" style="width:200px;">
												<?php foreach($currencyList->result() as $currency){ ?>
												<option value="<?php echo $currency->code; ?>" <?php if($form_mode){ if ($countrydetails->row()->currency_code == $currency->code){echo 'selected="selected"';} } ?>><?php echo $currency->name; ?></option>
												<?php } ?>
											</select>
										</div>
									</div>
								</li>
								
								
								<li>
									<div class="form_grid_12">
										<label class="field_title"><?php if ($this->lang->line('admin_common_status') != '') echo stripslashes($this->lang->line('admin_common_status')); else echo 'Status'; ?></label>
										<div class="form_input">
											<div class="active_inactive">
												<input type="checkbox"  name="status" id="active_inactive_active" class="active_inactive" <?php if($form_mode){ if ($countrydetails->row()->status == 'Active'){echo 'checked="checked"';} }else{echo 'checked="checked"';} ?>/>
											</div>
										</div>
									</div>
								</li>
								<input type="hidden" name="country_id" value="<?php if($form_mode){ echo $countrydetails->row()->_id; } ?>"/>
								<li>
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
<?php 
$this->load->view(ADMIN_ENC_URL.'/templates/footer.php');
?>