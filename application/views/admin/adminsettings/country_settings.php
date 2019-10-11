<?php
$this->load->view(ADMIN_ENC_URL.'/templates/header.php');
 if (is_file('./commonsettings/dectar_country_settings.php')){
	include('commonsettings/dectar_country_settings.php');
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
						echo form_open(ADMIN_ENC_URL.'/adminlogin/save_country_settings',$attributes) 
					?>
	 					<ul>
							 <li>
									<div class="form_grid_12">
										<label class="field_title"><?php if ($this->lang->line('admin_settings_country_name') != '') echo stripslashes($this->lang->line('admin_settings_country_name')); else echo 'Country Name'; ?> <span class="req">*</span></label>
										<div class="form_input">
											<select name="countryId" id="countryId"  class="required" style="height: 31px; width: 51%;">
												<?php foreach($countryList as $country){ ?>
												<option value="<?php echo $country->_id; ?>" <?php if($this->config->item('countryId') == $country->_id){echo 'selected="selected"';}  ?>><?php echo $country->name; ?></option>
												<?php } ?>
											</select>
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