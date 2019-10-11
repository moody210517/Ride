<?php
$this->load->view(ADMIN_ENC_URL.'/templates/header.php');
?>

<?php 
$share_pooling ="0"; 
if (isset($admin_settings->row()->share_pooling)){
	if ($admin_settings->row()->share_pooling == '1'){ 
		$share_pooling ="1"; 
	}
}  
?>


<div id="content" class="base-app-top">
    <div class="grid_container">
        <div class="grid_12">
            <div class="widget_wrap">
                <div class="widget_wrap tabby">
                    <div class="widget_top"> 
                        <span class="h_icon list"></span>
                        <h6><?php echo $heading; ?></h6>
                    </div>
		
                    <div class="widget_content">
                        <?php
                        $attributes = array('class' => 'form_container left_label', 'id' => 'pool_settings_form','enctype' => 'multipart/form-data');
                        echo form_open(ADMIN_ENC_URL.'/adminlogin/admin_global_settings', $attributes)
                        ?>
                        <input type="hidden" name="form_mode" value="pool"/>
                        <div id="tab45" class="base-appsec">
                            <ul class="left_promo_base" style=" min-height: 200px;">

                                <li>
                                    <h3 style="padding:0px;"><?php if ($this->lang->line('admin_menu_pool_settings') != '') echo stripslashes($this->lang->line('admin_menu_pool_settings')); else echo 'Share Pool Settings'; ?></h3>
                                </li>
									
									<li>
										<div class="form_grid_12">
											<label class="field_title"><?php if ($this->lang->line('admin_pool_settings_enable_share_pooling') != '') echo stripslashes($this->lang->line('admin_pool_settings_enable_share_pooling')); else echo 'Enable Share Pooling'; ?></label>
											<div class="form_input">
												<div class="gPoolyes_no">
													<input type="checkbox" name="share_pooling"  id="yes_no" class="gPoolyes_no" <?php if ($share_pooling == '1'){ echo 'checked="checked"'; }  ?> />
												</div>
											</div>
										</div>
									</li>
									
									<li class="pool_values_wrapper" <?php  if ($share_pooling == '0'){ echo "style='display:none'"; }?>>
										<div class="form_grid_12">
											<label class="field_title"><?php if ($this->lang->line('admin_pool_settings_share_pool_name') != '') echo stripslashes($this->lang->line('admin_pool_settings_share_pool_name')); else echo 'Share pool name'; ?> <span class="req">*</span></label>
											<div class="form_input">
												<input name="pooling_name" id="pooling_name" type="text" value="<?php echo $this->config->item('pooling_name'); ?>" class="large tipTop pool_values <?php if ($share_pooling == '1'){ echo 'required'; }  ?>" title="<?php if ($this->lang->line('admin_pool_enter_sharepool_name') != '') echo stripslashes($this->lang->line('admin_pool_enter_sharepool_name')); else echo 'Enter the Share Pool name'; ?>"/>
										</div>
										</div>
									</li>
									
									<li class="pool_values_wrapper" <?php  if ($share_pooling == '0'){ echo "style='display:none'"; }?>>
										<div class="form_grid_12">
											<label class="field_title"><?php if ($this->lang->line('admin_drivers_image') != '') echo stripslashes($this->lang->line('admin_drivers_image')); else echo 'Image'; ?></label>
											<div class="form_input">
												<input name="pool_cat_image" id="pool_cat_image" type="file" class="large tipTop pool_valuesF" title="<?php if ($this->lang->line('cartype_upload_image') != '') echo stripslashes($this->lang->line('cartype_upload_image')); else echo 'Please upload Image'; ?>"/>
											</div>
										</div>
									</li>
									<?php 
									if($share_pooling=="1"){
										$pool_cat_image=CATEGORY_IMAGE_DEFAULT;
										if ($this->config->item('pool_cat_image')!=""){
											$pool_cat_image=CATEGORY_IMAGE.$this->config->item('pool_cat_image');
										}
									?>
									<li class="pool_values_wrapper" <?php  if ($share_pooling == '0'){ echo "style='display:none'"; }?>>
										<div class="form_grid_12">
											<label class="field_title" >&nbsp; </label>
											<div class="form_input">
												<img src="<?php echo base_url().$pool_cat_image; ?>" alt="<?php echo $this->config->item('pool_cat_image'); ?>" width="100" />
											</div>
										</div>
									</li>
									<?php 
									}
									?>	
									
									<li class="pool_values_wrapper" <?php  if ($share_pooling == '0'){ echo "style='display:none'"; }?>>
										<div class="form_grid_12">
											<label class="field_title"><?php if ($this->lang->line('admin_drivers_icon_default') != '') echo stripslashes($this->lang->line('admin_drivers_icon_default')); else echo 'Icon Default'; ?> </label>
											<div class="form_input">
												<input name="pool_icon_normal" id="pool_icon_normal" type="file" class="large tipTop pool_valuesF" title="<?php if ($this->lang->line('cartype_upload_icon') != '') echo stripslashes($this->lang->line('cartype_upload_icon')); else echo 'Please upload Icon'; ?>"/>
												<p><?php if ($this->lang->line('admin_drivers_standard_size') != '') echo stripslashes($this->lang->line('admin_drivers_standard_size')); else echo 'Standard Size 150x150 px'; ?></p>
											</div>
										</div>
									</li>
									<?php 
									if($share_pooling=="1"){
										$pool_icon_normal=ICON_IMAGE_DEFAULT;
										if ($this->config->item('pool_icon_normal')!=""){
											$pool_icon_normal=ICON_IMAGE.$this->config->item('pool_icon_normal');
										}
									?>
									<li class="pool_values_wrapper" <?php  if ($share_pooling == '0'){ echo "style='display:none'"; }?>>
										<div class="form_grid_12">
											<label class="field_title" >&nbsp; </label>
											<div class="form_input">
												<img src="<?php echo base_url().$pool_icon_normal; ?>" alt="<?php echo $this->config->item('pool_icon_normal'); ?>" width="40" />
											</div>
										</div>
									</li>
									<?php 
									}
									?>									
									
									<li class="pool_values_wrapper" <?php  if ($share_pooling == '0'){ echo "style='display:none'"; }?>>
										<div class="form_grid_12">
											<label class="field_title"><?php if ($this->lang->line('admin_drivers_icon_active') != '') echo stripslashes($this->lang->line('admin_drivers_icon_active')); else echo 'Icon Active'; ?></label>
											<div class="form_input">
												<input name="pool_icon_active" id="pool_icon_active" type="file" class="large tipTop pool_valuesF" title="<?php if ($this->lang->line('cartype_upload_active_icon') != '') echo stripslashes($this->lang->line('cartype_upload_active_icon')); else echo 'Please upload Active Icon'; ?>"/>
												<p><?php if ($this->lang->line('admin_drivers_standard_size') != '') echo stripslashes($this->lang->line('admin_drivers_standard_size')); else echo 'Standard Size 150x150 px'; ?></p>
											</div>
										</div>
									</li>
									
									<?php 
									if($share_pooling=="1"){
										$pool_icon_active=ICON_IMAGE_DEFAULT;
										if ($this->config->item('pool_icon_active')!=""){
											$pool_icon_active=ICON_IMAGE.$this->config->item('pool_icon_active');
										}
									?>
									<li class="pool_values_wrapper" <?php  if ($share_pooling == '0'){ echo "style='display:none'"; }?>>
										<div class="form_grid_12">
											<label class="field_title">&nbsp; </label>
											<div class="form_input">
												<img src="<?php echo base_url().$pool_icon_active; ?>" alt="<?php echo $this->config->item('pool_icon_active'); ?>" width="40" />
											</div>
										</div>
									</li>
									<?php 
									}
									?>
									
									<li class="pool_values_wrapper" <?php  if ($share_pooling == '0'){ echo "style='display:none'"; }?>>
										<div class="form_grid_12">
											<label class="field_title"><?php if ($this->lang->line('admin_drivers_map_car_image') != '') echo stripslashes($this->lang->line('admin_drivers_map_car_image')); else echo 'Map Car Image'; ?> </label>
											<div class="form_input">
												<input name="pool_map_car_image" id="pool_map_car_image" type="file" class="large tipTop pool_valuesF" title="<?php if ($this->lang->line('cartype_upload_icon') != '') echo stripslashes($this->lang->line('cartype_upload_icon')); else echo 'Please upload Icon'; ?>"/>
												<p><?php if ($this->lang->line('admin_map_icon_standard_sizes') != '') echo stripslashes($this->lang->line('admin_map_icon_standard_sizes')); else echo 'Standard Size 70x70 px'; ?></p>
											</div>
										</div>
									</li>
									<?php 
									if($share_pooling=="1"){
										$pool_map_car_image=ICON_MAP_CAR_IMAGE;
										if ($this->config->item('pool_map_car_image')!=""){
											$pool_map_car_image=ICON_IMAGE.$this->config->item('pool_map_car_image');
										}
									?>
									<li class="pool_values_wrapper" <?php  if ($share_pooling == '0'){ echo "style='display:none'"; }?>>
										<div class="form_grid_12">
											<label class="field_title">&nbsp; </label>
											<div class="form_input">
												<img src="<?php echo base_url().$pool_map_car_image; ?>" alt="<?php echo $this->config->item('pool_map_car_image'); ?>" width="70" />
											</div>
										</div>
									</li>
									<?php 
									}
									?>
									
									
                            </ul>
                            <ul>
                                <li>
                                    <div class="form_grid_12">
                                        <div class="form_input">
                                            <button type="submit" class="btn_small btn_blue"><span><?php if ($this->lang->line('admin_settings_submit') != '') echo stripslashes($this->lang->line('admin_settings_submit')); else echo 'Submit'; ?></span></button>
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
    </div>
    <span class="clear"></span> 
</div>
</div>

<?php $checkbox_lan=get_language_array_for_keyword($this->data['langCode']);?>
<script type="text/javascript">
    $(document).ready(function () {
		$("#pool_settings_form").validate();
    });
		
		
	$('.gPoolyes_no :checkbox').iphoneStyle({
		checkedLabel: '<?php echo $checkbox_lan['verify_status_yes_ucfirst']; ?>', 
		uncheckedLabel: '<?php echo $checkbox_lan['verify_status_no_ucfirst']; ?>' ,
		onChange: function(elem, value) {
			if($(elem)[0].checked==false){
				$(".pool_values_wrapper").hide();
				$('.pool_values').removeClass('required');
			}else{
				$(".pool_values_wrapper").show();
				$('.pool_values').addClass('required');
			}
		}
	});
</script>

<?php if ($share_pooling == '0'){ ?>
<style>
.pool_values_wrapper { 
	display:none;
}
</style>
<?php }  ?>

<?php
$this->load->view(ADMIN_ENC_URL.'/templates/footer.php');
?>