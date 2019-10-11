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
						$attributes = array('class' => 'form_container left_label');
						echo form_open(ADMIN_ENC_URL.'/location/display_country_list',$attributes) 
					?> 		
	 						<ul>								
                                <li>
									<div class="form_grid_12">
										<label class="field_title"><?php if ($this->lang->line('admin_location_and_fare_name') != '') echo stripslashes($this->lang->line('admin_location_and_fare_name')); else echo 'Name'; ?></label>
										<div class="form_input">
										<?php if(isset($countrydetails->row()->name)){ echo $countrydetails->row()->name; } ?>
										</div>
									</div>
								</li>							
                                <li>
									<div class="form_grid_12">
										<label class="field_title"><?php if ($this->lang->line('admin_location_and_fare_iso_code') != '') echo stripslashes($this->lang->line('admin_location_and_fare_iso_code')); else echo 'ISO Code'; ?> 2</label>
										<div class="form_input">
										<?php if(isset($countrydetails->row()->cca2)){ echo $countrydetails->row()->cca2; } ?>
										</div>
									</div>
								</li>							
                                <li>
									<div class="form_grid_12">
										<label class="field_title"><?php if ($this->lang->line('admin_location_and_fare_iso_code') != '') echo stripslashes($this->lang->line('admin_location_and_fare_iso_code')); else echo 'ISO Code'; ?> 3</label>
										<div class="form_input">
										<?php if(isset($countrydetails->row()->cca3)){ echo $countrydetails->row()->cca3; } ?>
										</div>
									</div>
								</li>								
                                <li>
									<div class="form_grid_12">
										<label class="field_title"><?php if ($this->lang->line('admin_location_and_fare_dial_code') != '') echo stripslashes($this->lang->line('admin_location_and_fare_dial_code')); else echo 'Dial Code'; ?></label>
										<div class="form_input">
										<?php if(isset($countrydetails->row()->dial_code)){ echo $countrydetails->row()->dial_code; } ?>
										</div>
									</div>
								</li>						
                                <li>
									<div class="form_grid_12">
										<label class="field_title"><?php if ($this->lang->line('admin_location_and_fare_currency_code') != '') echo stripslashes($this->lang->line('admin_location_and_fare_currency_code')); else echo 'Currency Code'; ?></label>
										<div class="form_input">
										<?php if(isset($countrydetails->row()->currency_code)){ echo $countrydetails->row()->currency_code; } ?>
										</div>
									</div>
								</li>					
                                <li>
									<div class="form_grid_12">
										<label class="field_title"><?php if ($this->lang->line('admin_location_and_fare_currency_symbol') != '') echo stripslashes($this->lang->line('admin_location_and_fare_currency_symbol')); else echo 'Currency Symbol'; ?></label>
										<div class="form_input">
										<?php if(isset($countrydetails->row()->currency_symbol)){ echo $countrydetails->row()->currency_symbol; } ?>
										</div>
									</div>
								</li>					
                                <li>
									<div class="form_grid_12">
										<label class="field_title"><?php if ($this->lang->line('admin_location_and_fare_currency_name') != '') echo stripslashes($this->lang->line('admin_location_and_fare_currency_name')); else echo 'Currency Name'; ?></label>
										<div class="form_input">
										<?php if(isset($countrydetails->row()->currency_name)){ echo $countrydetails->row()->currency_name; } ?>
										</div>
									</div>
								</li>
								
								
								<li>
									<div class="form_grid_12">
										<label class="field_title"><?php if ($this->lang->line('admin_common_status') != '') echo stripslashes($this->lang->line('admin_common_status')); else echo 'Status'; ?></label>
										<div class="form_input">
											<div class="active_inactive">
												<?php if(isset($countrydetails->row()->status)){ echo $countrydetails->row()->status; } ?>
											</div>
										</div>
									</div>
								</li>
								<li>
									<div class="form_grid_12">
										<div class="form_input">
											<a class="tipLeft" href="<?php echo ADMIN_ENC_URL;?>/location/display_country_list" original-title="Go to Country list">
												<span class="badge_style b_done"><?php if ($this->lang->line('admin_common_back') != '') echo stripslashes($this->lang->line('admin_common_back')); else echo 'Back'; ?></span>
											</a>
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