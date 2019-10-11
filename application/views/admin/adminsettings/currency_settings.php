<?php
$this->load->view(ADMIN_ENC_URL.'/templates/header.php');
 if (is_file('./commonsettings/dectar_currency_settings.php')){
	include('commonsettings/dectar_currency_settings.php');
}
?>
<div id="content">
	<div class="grid_container">
		<div class="grid_12">
			<div class="widget_wrap">
				<div class="widget_top">
					<span class="h_icon list"></span>
					<h6><?php echo $heading;?></h6>
				</div>
				<div class="widget_content">
					<?php 
						$attributes = array('class' => 'form_container left_label', 'id' => 'regitstraion_form');
						echo form_open(ADMIN_ENC_URL.'/adminlogin/save_currency_settings',$attributes) 
					?>
	 					<ul>
							<li>
								<div class="form_grid_12">
									<label class="field_title"><?php if ($this->lang->line('admin_settings_currency_name') != '') echo stripslashes($this->lang->line('admin_settings_currency_name')); else echo 'Currency Name'; ?></label>
									<div class="form_input">
										<input name="currency[currency_name]" value="<?php echo $this->config->item('currency_name');?>" id="currency_name" type="text"  class="large tipTop" title="<?php if ($this->lang->line('admin_setting_currency_name') != '') echo stripslashes($this->lang->line('admin_setting_currency_name')); else echo 'Please enter the currency name'; ?>"/>
									</div>
                                </div>
							</li>
                                    
                            <li>
								<div class="form_grid_12">
									<label class="field_title"><?php if ($this->lang->line('admin_settings_currency_code') != '') echo stripslashes($this->lang->line('admin_settings_currency_code')); else echo 'Currency Code'; ?></label>
									<div class="form_input">
										<input name="currency[currency_code]" value="<?php echo $this->config->item('currency_code');?>" id="currency_code" type="text"  class="large tipTop" title="<?php if ($this->lang->line('admin_setting_currency_code') != '') echo stripslashes($this->lang->line('admin_setting_currency_code')); else echo 'Please enter the currency code'; ?>"/>
									</div>
								</div>
                            </li>
                                    
                            <li>
								 <div class="form_grid_12">
									 <label class="field_title"><?php if ($this->lang->line('admin_settings_currency_symbol') != '') echo stripslashes($this->lang->line('admin_settings_currency_symbol')); else echo 'Currency Symbol'; ?></label>
									 <div class="form_input">
										<input name="currency[currency_symbol]" value="<?php echo $this->config->item('currency_symbol');?>" id="currency_symbol" type="text"  class="large tipTop" title="<?php if ($this->lang->line('admin_setting_currency_symbol') != '') echo stripslashes($this->lang->line('admin_setting_currency_symbol')); else echo 'Please enter the currency symbol'; ?> "/>
									 </div>
								 </div>
                            </li>
	
							<li>
								<div class="form_grid_12">
									<div class="form_input">
										<button type="submit" class="btn_small btn_blue" ><span><?php if ($this->lang->line('admin_common_save') != '') echo stripslashes($this->lang->line('admin_common_save')); else echo 'Save'; ?></span></button>
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