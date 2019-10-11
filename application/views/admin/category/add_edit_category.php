<?php
$this->load->view(ADMIN_ENC_URL.'/templates/header.php');
?>
	<div id="content" class="add-operator-sec add-edit-cat">
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
			$attributes = array('class' => 'form_container left_label', 'id' => 'addeditcategory_form','method'=>'POST','enctype' => 'multipart/form-data');
			echo form_open(ADMIN_ENC_URL.'/category/insertEditCategory', $attributes)
			?>
				<div>
			<ul class="operator-sec-bar">
				<li>
					<div class="form_grid_12">
						<label class="field_title"><?php if ($this->lang->line('admin_drivers_category') != '') echo stripslashes($this->lang->line('admin_drivers_category')); else echo 'Category'; ?> <span class="req">*</span></label>
						<div class="form_input">
							<input name="name" id="name" type="text"  class="large tipTop required" title="<?php if ($this->lang->line('cartype_enter_category_name') != '') echo stripslashes($this->lang->line('cartype_enter_category_name')); else echo 'Please enter category name'; ?>" value="<?php if($form_mode){ echo $categorydetails->row()->name; } ?>"/>
						</div>
					</div>
				</li>
							
					<?php /* <li>
						<div class="form_grid_12">
							<label class="field_title">Seating Capacity <span class="req">*</span></label>
							<div class="form_input">
								<ul>
									<li>
										Min Seats
										<select name="minSeat" id="minSeat" class="small tipTop required lesserThan" data-min="maxSeat">
											<option value="">Select Minimum Seat</option>
											<?php for($i=1;$i<=12;$i++){ ?>
											<option value="<?php echo $i; ?>" <?php if($form_mode) if(isset($categorydetails->row()->seating_capacity)) if($categorydetails->row()->seating_capacity['min']==$i) echo "selected"; ?>>
											<?php echo $i; ?>
											</option>
											<?php } ?>
										</select>
									</li>
									<li>
										Max Seats
										<select name="maxSeat" id="maxSeat" class="small tipTop required greaterThan" data-max="minSeat">
											<option value="">Select Maximum Seat</option>
											<?php for($i=1;$i<=15;$i++){ ?>
											<option value="<?php echo $i; ?>" <?php if($form_mode) if(isset($categorydetails->row()->seating_capacity)) if($categorydetails->row()->seating_capacity['max']==$i) echo "selected"; ?>>
											<?php echo $i; ?>
											</option>
											<?php } ?>
										</select>
									</li>
								</ul>
							</div>
						</div>
					</li> */ ?>
									
					<li>
						<div class="form_grid_12">
							<label class="field_title"><?php if ($this->lang->line('admin_drivers_image') != '') echo stripslashes($this->lang->line('admin_drivers_image')); else echo 'Image'; ?> </label>
							<div class="form_input">
								<input name="image" id="image" type="file"  class="large tipTop" title="<?php if ($this->lang->line('cartype_upload_image') != '') echo stripslashes($this->lang->line('cartype_upload_image')); else echo 'Please upload Image'; ?>"/>
							</div>
						</div>
					</li>
				
					<?php 
					if($form_mode){
						if(isset($categorydetails->row()->image)){
						
						if(isset($categorydetails->row()->image)){
							if($categorydetails->row()->image!=""){
								$image=CATEGORY_IMAGE.$categorydetails->row()->image;
							}else{
								$image=CATEGORY_IMAGE_DEFAULT;
							}
						}else{
							$image=CATEGORY_IMAGE_DEFAULT;
						}
					?>
					
					<li>
						<div class="form_grid_12">
							<label class="field_title">&nbsp; </label>
							<div class="form_input">
								<img src="<?php echo base_url().$image; ?>" alt="<?php echo $categorydetails->row()->name;?>" width="100" />
							</div>
						</div>
					</li>
									
					<?php 
						}
					}
					?>
					
					<li>
						<div class="form_grid_12">
							<label class="field_title"><?php if ($this->lang->line('admin_drivers_icon_default') != '') echo stripslashes($this->lang->line('admin_drivers_icon_default')); else echo 'Icon Default'; ?> </label>
							<div class="form_input">
								<input name="icon_normal" id="icon_normal" type="file"  class="large tipTop" title="<?php if ($this->lang->line('cartype_upload_icon') != '') echo stripslashes($this->lang->line('cartype_upload_icon')); else echo 'Please upload Icon'; ?>"/>
								<p><?php if ($this->lang->line('admin_drivers_standard_size') != '') echo stripslashes($this->lang->line('admin_drivers_standard_size')); else echo 'Standard Size 150x150 px'; ?></p>
							</div>
						</div>
					</li>
					<?php 
					if($form_mode){
						if(isset($categorydetails->row()->icon_normal)){
						
						if(isset($categorydetails->row()->icon_normal)){
							if($categorydetails->row()->icon_normal!=""){
								$icon_normal=ICON_IMAGE.$categorydetails->row()->icon_normal;
							}else{
								$icon_normal=ICON_IMAGE_DEFAULT;
							}
						}else{
							$icon_normal=ICON_IMAGE_DEFAULT;
						}
					?>
					
					<li>
						<div class="form_grid_12">
							<label class="field_title">&nbsp; </label>
							<div class="form_input">
								<img src="<?php echo base_url().$icon_normal; ?>" alt="<?php echo $categorydetails->row()->icon_normal;?>" width="40" />
							</div>
						</div>
					</li>
					
					<?php 
						}
					}
					?>
				
				</ul>
				<ul class="operator-log-rite">
				
					<li>
						<div class="form_grid_12">
							<label class="field_title"><?php if ($this->lang->line('admin_drivers_icon_active') != '') echo stripslashes($this->lang->line('admin_drivers_icon_active')); else echo 'Icon Active'; ?> </label>
							<div class="form_input">
								<input name="icon_active" id="icon_active" type="file"  class="large tipTop" title="<?php if ($this->lang->line('cartype_upload_active_icon') != '') echo stripslashes($this->lang->line('cartype_upload_active_icon')); else echo 'Please upload Active Icon'; ?>"/>
								<p><?php if ($this->lang->line('admin_drivers_standard_size') != '') echo stripslashes($this->lang->line('admin_drivers_standard_size')); else echo 'Standard Size 150x150 px'; ?></p>
							</div>
						</div>
					</li>
					
				<?php 
				if($form_mode){
					if(isset($categorydetails->row()->icon_active)){
					
					if(isset($categorydetails->row()->icon_active)){
						if($categorydetails->row()->icon_active!=""){
							$icon_active=ICON_IMAGE.$categorydetails->row()->icon_active;
						}else{
							$icon_active=ICON_IMAGE_ACTIVE;
						}
					}else{
						$icon_active=ICON_IMAGE_ACTIVE;
					}
				?>
				
				<li>
					<div class="form_grid_12">
						<label class="field_title">&nbsp; </label>
						<div class="form_input">
							<img src="<?php echo base_url().$icon_active; ?>" alt="<?php echo $categorydetails->row()->icon_active;?>" width="40" />
						</div>
					</div>
				</li>
				
				<?php 
					}
				}
				?>
				<li>
						<div class="form_grid_12">
							<label class="field_title"><?php if ($this->lang->line('admin_drivers_map_car_image') != '') echo stripslashes($this->lang->line('admin_drivers_map_car_image')); else echo 'Map Car Image'; ?> </label>
							<div class="form_input">
								<input name="icon_car_image" id="icon_car_image" type="file"  class="large tipTop" title="<?php if ($this->lang->line('cartype_upload_icon') != '') echo stripslashes($this->lang->line('cartype_upload_icon')); else echo 'Please upload Icon'; ?>"/>
								<p><?php if ($this->lang->line('admin_map_icon_standard_sizes') != '') echo stripslashes($this->lang->line('admin_map_icon_standard_sizes')); else echo 'Standard Size 70x70 px'; ?></p>
							</div>
						</div>
					</li>
					<?php 
					
					if($form_mode){
						
						if(isset($categorydetails->row()->icon_car_image)){
						if(isset($categorydetails->row()->icon_car_image)){
							if($categorydetails->row()->icon_car_image!=""){
								$icon_car_image=ICON_IMAGE.$categorydetails->row()->icon_car_image;
							}else{
								$icon_car_image=ICON_MAP_CAR_IMAGE;
							}
						}else{
							$icon_car_image=ICON_MAP_CAR_IMAGE;
						}
						
					?>
					
					<li>
						<div class="form_grid_12">
							<label class="field_title">&nbsp; </label>
							<div class="form_input">
								<img src="<?php echo base_url().$icon_car_image; ?>" alt="map car image" width="70" />
							</div>
						</div>
					</li>
					
					<?php 
					}
					}
					?>
			
				<?php /*
				<li>
					<div class="form_grid_12">
						<label class="field_title">Default <span class="req">*</span></label>
						<div class="form_input">
							<div class="yes_no">
								<input type="checkbox"  name="isdefault"  id="yes_no_yes" class="yes_no" <?php if($form_mode){ if ($categorydetails->row()->isdefault == 'Yes'){echo 'checked="checked"';} }else{ echo 'checked="checked"'; }?>/>
							</div>
						</div>
					</div>
				</li>
				*/ ?>
			
				
				<li>
					<div class="form_grid_12">
						<label class="field_title"><?php if ($this->lang->line('admin_common_status') != '') echo stripslashes($this->lang->line('admin_common_status')); else echo 'Status'; ?> </label>
						<div class="form_input">
							<div class="active_inactive">
								<input type="checkbox"  name="status" id="active_inactive_active" class="active_inactive" <?php if($form_mode){ if ($categorydetails->row()->status == 'Active'){echo 'checked="checked"';} }else{echo 'checked="checked"';} ?>/>
							</div>
						</div>
					</div>
				</li>
				
				
									
								</ul>
								<ul class="last-btn-submit">
								<li>
					<div class="form_grid_12">
						<div class="form_input">
							<input type="hidden" name="category_id" id="category_id" value="<?php if($form_mode){ echo $categorydetails->row()->_id; } ?>"  />
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
<script type="text/javascript">
	/* $.validator.addMethod("greaterThan", function( value, element) {
		var elementID=$('.greaterThan').attr('id');
		var dataMax=$('#'+elementID).attr("data-max");
		var max = $("#"+dataMax).val();
		var result =parseInt(value) > parseInt(max)
		if (!result) {
			return false;
		}else{
			return true;
		}
	},"Max must be greater than Min");
	$.validator.addMethod("lesserThan", function( value, element) {
		var elementID=$('.lesserThan').attr('id');
		var dataMin=$('#'+elementID).attr("data-min");
		var min = $("#"+dataMin).val();
		var result =parseInt(value) < parseInt(min)
		if (!result) {
			return false;
		}else{
			return true;
		}
	},"Min must be lesser than Max"); */
 </script>
<?php 
$this->load->view(ADMIN_ENC_URL.'/templates/footer.php');
?>