<?php 
$this->load->view(COMPANY_NAME.'/templates/header.php');


$v_docx = 0; $d_docx = 0;
foreach($docx_list->result() as $docx) if($docx->category == 'Driver') $d_docx++; else $v_docx++;
?>


<script>
function custom_validate_form(){
    $.validator.setDefaults({ ignore: ":hidden:not(select)" });
	if($("#editdriver_form").valid()){
		var uEmail = $('#email').val();
		var uCC = $('#country_codeM').val();
		var uPN = $('#mobile_number').val();
		var driver_id = $('#driver_id').val();
		$('#form_loader').css('display','inline-block');
		$.ajax({
			type:'post',
			url:'admin/drivers/check_driver_duplicate',
			data:{'email':uEmail,'dial_code':uCC,'phone_number':uPN,'driver_id':driver_id},
			dataType: 'json',
			success:function(res){
				$('#form_loader').css('display','none');
				if(res.status == '1'){ 
					$('#editdriver_form').submit();
				} else {
					$('#user_duplicate_Err').html(res.response);
				}
			}
		});
	}
}

</script>

<div id="content" class="add-operator-sec driver-edit drives_first">
    <div class="grid_container">
        <div class="grid_12">
            <div class="widget_wrap">
                <div class="widget_top">
                    <span class="h_icon list"></span>
                    <h6><?php if ($this->lang->line('admin_drivers_edit_driver') != '') echo stripslashes($this->lang->line('admin_drivers_edit_driver')); else echo 'Edit Driver'; ?></h6>
                </div>
                <div class="widget_content">
                    <?php
                    $attributes = array('class' => 'form_container left_label', 'id' => 'editdriver_form', 'enctype' => 'multipart/form-data');
                    echo form_open_multipart(COMPANY_NAME.'/drivers/insertEdit_driver', $attributes);

                    $driver_details = $driver_details->row();
                    ?>
                    <ul class="operator-sec-bar">
                        <li>
                            <div class="form_grid_12">
                                <h3><?php if ($this->lang->line('admin_drivers_drivers_location') != '') echo stripslashes($this->lang->line('admin_drivers_drivers_location')); else echo 'Driver Location'; ?></h3>
                            </div>
                        </li>
                        <li>
                            <div class="form_grid_12">
                                <label class="field_title"><?php if ($this->lang->line('admin_location_and_fare_location_location') != '') echo stripslashes($this->lang->line('admin_location_and_fare_location_location')); else echo 'Location'; ?> <span class="req">*</span></label>
                                <div class="form_input">
                                    <select name="driver_location" id="driver_location"  class="required chzn-select" style="height: 31px; width: 51%;">
                                        <option value=""><?php if ($this->lang->line('select_driver_loc') != '') echo stripslashes($this->lang->line('select_driver_loc')); else echo 'Select driver location'; ?></option>
                                        <?php
										$curMultiCat = array();
                                        if ($locationList->num_rows() > 0) {
                                            foreach ($locationList->result() as $loclist) {
                                                if (isset($loclist->avail_category)) {
                                                    $category_list = @implode($loclist->avail_category, ',');
                                                } else {
                                                    $category_list = '';
                                                }
												
												$selct = '';
												if (isset($driver_details->driver_location)) {
													if ($driver_details->driver_location == $loclist->_id) {
														$selct = 'selected="selected"'; 
														$avail_category = $loclist->avail_category;
														
														$drCat = (string)$driver_details->category;
														if(isset($loclist->fare[$drCat]['additional_category'])){
															$curMultiCat = $loclist->fare[$drCat]['additional_category']; 
														}
													}
												}
												
												
                                                ?>
                                                <option value="<?php echo $loclist->_id; ?>" data-category="<?php echo $category_list; ?>" <?php   echo $selct;  ?>>
                                                    <?php echo $loclist->city; ?>
                                                </option>
                                                <?php
                                            }
                                        }
                                        ?>
                                    </select>
                                </div>
                            </div>
                        </li>

                        <li>
                            <div class="form_grid_12">
                                <h3><?php if ($this->lang->line('admin_drivers_driver_category') != '') echo stripslashes($this->lang->line('admin_drivers_driver_category')); else echo 'Driver Category'; ?></h3>
                            </div>
                        </li>
                        <li>
                            <div class="form_grid_12">
                                <label class="field_title"><?php if ($this->lang->line('admin_location_and_fare_category') != '') echo stripslashes($this->lang->line('admin_location_and_fare_category')); else echo 'Category'; ?> <span class="req">*</span></label>
                                <div class="form_input">
                                    <select class="chzn-select required Validname" name="category" id="category"  class="required" style="height: 31px; width: 51%;">
                                        <option value="" data-vehicle=''><?php if ($this->lang->line('admin_drivers_choose_driver_category') != '') echo stripslashes($this->lang->line('admin_drivers_choose_driver_category')); else echo 'Choose driver category'; ?></option>
                                        <?php
                                        if ($categoryList->num_rows() > 0) {
                                            foreach ($categoryList->result() as $category) {
                                                if (isset($category->vehicle_type)) {
                                                    $vehicle_type = @implode($category->vehicle_type, ',');
                                                } else {
                                                    $vehicle_type = '';
                                                }
												
												$category_name = $category->name;
												if(isset($category->name_languages[$langCode ]) && $category->name_languages[$langCode ] != '') $category_name = $category->name_languages[$langCode ];
                                                ?>
                                                <option value="<?php echo $category->_id; ?>" data-vehicle="<?php echo $vehicle_type; ?>" <?php if (isset($driver_details->category)) if ($driver_details->category == $category->_id) echo 'selected="selected"' ?>>
                                                    <?php echo $category_name; ?>
                                                </option>
                                                <?php
                                            }
                                        }
                                        ?>
                                    </select>
                                </div>
                            </div>
                        </li>
						
						<?php  
							$showChk = 'none';
							$adShow = 'block';
							if(isset($driver_details->multi_car_status) && $driver_details->multi_car_status == 'ON') $showChk = 'block'; 
							if($multiCategoryOption == 'OFF' || count($curMultiCat) == 0){
								$curMultiCat = array();
								$showChk = 'none';
								$adShow = 'none';
							} 
						?>
						<li id="additional_cat_li_chk" style="display:<?php echo $adShow; ?>">
                            <div class="form_grid_12">
                                <label class="field_title" for="multi_car_status"><?php if ($this->lang->line('accept_additional_category') != '') echo stripslashes($this->lang->line('accept_additional_category')); else echo 'Accept Rides From Additional Categories'; ?>?</label>
								<input type="checkbox" name="multi_car_status" id="multi_car_status" <?php if (isset($driver_details->multi_car_status)) if($driver_details->multi_car_status == 'ON') echo 'checked="checked"'; ?> value="ON" />
                            </div>
                        </li>
						
						
						<?php 
							$additonal_cars = array();
							if(isset($driver_details->additional_category)) 
							$additonal_cars = $driver_details->additional_category;
							#echo '<pre>'; print_r($curMultiCat); die;
						
						?>
						
						<li id="additional_cat_li" style="display:<?php echo $showChk; ?>">
                            <div class="form_grid_12">
                                <label class="field_title" for="additional_category"><?php if ($this->lang->line('admin_additional_category') != '') echo stripslashes($this->lang->line('admin_additional_category')); else echo 'Additional Categories'; ?></label>
                                <div class="form_input">
                                    <select name="additional_category[]" id="additional_category" multiple="multiple" tabindex="1" class="chzn-select" style="height: 31px; width: 51%;">
                                        <?php 
                                        if ($categoryList->num_rows() > 0) {
                                            foreach ($categoryList->result() as $category) {
												if(in_array((string)$category->_id,$curMultiCat)){
													if (isset($category->vehicle_type)) {
														$vehicle_type = @implode($category->vehicle_type, ',');
													} else {
														$vehicle_type = '';
													}
													$category_name = $category->name;
													if(isset($category->name_languages[$langCode ]) && $category->name_languages[$langCode ] != '') $category_name = $category->name_languages[$langCode ];
													?>
													<option value="<?php echo $category->_id; ?>" data-additional-cat="<?php echo @implode(',',$category->additional_category); ?>" data-vehicle="<?php echo $vehicle_type; ?>" <?php if (isset($driver_details->category)) if(in_array((string)$category->_id,$additonal_cars)) { echo 'selected="selected"'; } ?>>
														<?php echo $category_name; ?>
													</option>
													<?php
												}
                                            }
                                        }
                                        ?>
                                    </select>
                                </div>
                            </div>
                        </li>

                        <li>
                            <div class="form_grid_12">
                                <h3><?php if ($this->lang->line('admin_drivers_login_details') != '') echo stripslashes($this->lang->line('admin_drivers_login_details')); else echo 'Login Details'; ?></h3>
                            </div>
                        </li>

                        <li>
                            <div class="form_grid_12">
                                <label class="field_title"><?php if ($this->lang->line('admin_drivers_driver_name') != '') echo stripslashes($this->lang->line('admin_drivers_driver_name')); else echo 'Driver Name'; ?> <span class="req">*</span></label>
                                <div class="form_input">
                                    <input name="driver_name" id="driver_name" type="text"  class="required large tipTop alphanumeric" title="<?php if ($this->lang->line('driver_upload_enter_driver_fullname') != '') echo stripslashes($this->lang->line('driver_upload_enter_driver_fullname')); else echo 'Please enter the driver fullname'; ?>" value="<?php if (isset($driver_details->driver_name)) echo $driver_details->driver_name; ?>" />
                                </div>
                            </div>
                        </li>
                        <li>
                            <div class="form_grid_12">
                                <label class="field_title"><?php if ($this->lang->line('admin_drivers_email_address') != '') echo stripslashes($this->lang->line('admin_drivers_email_address')); else echo 'Email Address'; ?> <span class="req">*</span></label>
                                <div class="form_input">
                                    <input name="email" id="email" type="text"  class="required large tipTop email" title="<?php if ($this->lang->line('driver_enter_driver_email_address') != '') echo stripslashes($this->lang->line('driver_enter_driver_email_address')); else echo 'Please enter the driver email address'; ?>" value="<?php
                                    if ($isDemo) {
                                        echo $dEmail;
                                    } else {
                                        if (isset($driver_details->email))
                                            echo $driver_details->email;
                                    }
                                    ?>" readonly onkeypress="javascript: alert('You can not change email');" />
                                </div>
                            </div>
                        </li>


                        <li>
                            <div class="form_grid_12">
                                <h3><?php if ($this->lang->line('admin_drivers_address_details') != '') echo stripslashes($this->lang->line('admin_drivers_address_details')); else echo 'Address Details'; ?></h3>
                            </div>
                        </li>

                        <li>
                            <div class="form_grid_12">
                                <label class="field_title"><?php if ($this->lang->line('admin_drivers_address') != '') echo stripslashes($this->lang->line('admin_drivers_address')); else echo 'Address'; ?><span class="req">*</span></label>
                                <div class="form_input">
                                    <textarea name="address" id="address"  class="required large tipTop" title="<?php if ($this->lang->line('driver_enter_driver_address') != '') echo stripslashes($this->lang->line('driver_enter_driver_address')); else echo 'Please enter the driver address'; ?>" style="width: 372px;"><?php if (isset($driver_details->address['address'])) echo $driver_details->address['address']; ?></textarea>
                                </div>
                            </div>
                        </li>

                        <li>
                            <div class="form_grid_12">
                                <label class="field_title"><?php if ($this->lang->line('admin_drivers_country') != '') echo stripslashes($this->lang->line('admin_drivers_country')); else echo 'Country'; ?><span class="req">*</span></label>
                                <div class="form_input">
                                    <select name="county" id="county"  class="required chzn-select" style="height: 31px; width: 51%;">
                                        <option value=""><?php if ($this->lang->line('select_country') != '') echo stripslashes($this->lang->line('select_country')); else echo 'Select country'; ?></option>
                                        <?php foreach ($countryList as $country) { ?>
                                            <option value="<?php echo $country->name; ?>" data-dialCode="<?php echo $country->dial_code; ?>" <?php if ($driver_details->address['county'] == $country->name) echo 'selected="selected"' ?>><?php echo $country->name; ?></option>
                                        <?php } ?>
                                    </select>
                                </div>
                            </div>
                        </li>

                        <li>
                            <div class="form_grid_12">
                                <label class="field_title"><?php if ($this->lang->line('admin_drivers_state_province_region') != '') echo stripslashes($this->lang->line('admin_drivers_state_province_region')); else echo 'State / Province / Region'; ?><span class="req">*</span></label>
                                <div class="form_input">
                                    <input name="state" id="state" type="text"  class="required large tipTop" title="<?php if ($this->lang->line('driver_enter_driver_state') != '') echo stripslashes($this->lang->line('driver_enter_driver_state')); else echo 'Please enter the state'; ?>" value="<?php if (isset($driver_details->address['state'])) echo $driver_details->address['state']; ?>"/>
                                </div>
                            </div>
                        </li>

                        <li>
                            <div class="form_grid_12">
                                <label class="field_title"><?php if ($this->lang->line('admin_drivers_city') != '') echo stripslashes($this->lang->line('admin_drivers_city')); else echo 'City'; ?><span class="req">*</span></label>
                                <div class="form_input">
                                    <input name="city" id="city" type="text"  class="required large tipTop" title="<?php if ($this->lang->line('location_enter_the_city') != '') echo stripslashes($this->lang->line('location_enter_the_city')); else echo 'Please enter the city'; ?>" value="<?php if (isset($driver_details->address['city'])) echo $driver_details->address['city']; ?>"/>
                                </div>
                            </div>
                        </li>




                        <li>
                            <div class="form_grid_12">
                                <label class="field_title"><?php if ($this->lang->line('admin_drivers_postal_code') != '') echo stripslashes($this->lang->line('admin_drivers_postal_code')); else echo 'Postal Code'; ?><span class="req">*</span></label>
                                <div class="form_input">
                                    <input name="postal_code" id="postal_code" type="text"   maxlength="10"class="required large tipTop" title="<?php if ($this->lang->line('driver_enter_postal_code') != '') echo stripslashes($this->lang->line('driver_enter_postal_code')); else echo 'Please enter the postal code'; ?>" value="<?php if (isset($driver_details->address['postal_code'])) echo $driver_details->address['postal_code']; ?>"/>
                                </div>
                            </div>
                        </li>
                    
                        <li>
                            <div class="form_grid_12">
                                <label class="field_title"><?php if ($this->lang->line('admin_drivers_mobile_number') != '') echo stripslashes($this->lang->line('admin_drivers_mobile_number')); else echo 'Mobile Number'; ?><span class="req">*</span></label>
                                <div class="form_input">
								
									<select name="dail_code" id="country_codeM"  class="required chzn-select small tipTop mCC" style="" title="<?php if ($this->lang->line('select_mobile_country_code') != '') echo stripslashes($this->lang->line('select_mobile_country_code')); else echo 'Please select mobile country code'; ?>">
										<?php foreach ($countryList as $country) { ?>
											<option value="<?php echo $country->dial_code; ?>" <?php if($country->dial_code==$driver_details->dail_code){ echo "selected='selected'"; } ?>><?php echo $country->cca3.' ('.$country->dial_code.')'; ?></option>
										<?php } ?>
									</select>
									
									
									
                                    <input name="mobile_number" placeholder="Mobile Number.." id="mobile_number" type="text"  class="required large tipTop phoneNumber" title="<?php if ($this->lang->line('driver_enter_mobile_number') != '') echo stripslashes($this->lang->line('driver_enter_mobile_number')); else echo 'Please enter the mobile number'; ?>" maxlength="20" style="width: 38.5% !important;" value="<?php if (isset($driver_details->mobile_number)) echo $driver_details->mobile_number; ?>"/>
                                </div>
                            </div>
                        </li>

                        <li>
                            <div class="form_grid_12">
                                <h3><?php if ($this->lang->line('admin_drivers_identity') != '') echo stripslashes($this->lang->line('admin_drivers_identity')); else echo 'Identity'; ?></h3>
                            </div>
                        </li>

                        <li>
                            <div class="form_grid_12">
                                <label class="field_title"><?php if ($this->lang->line('admin_drivers_driver_image') != '') echo stripslashes($this->lang->line('admin_drivers_driver_image')); else echo 'Driver Image'; ?></label>
                                <div class="form_input">
                                    <input name="thumbnail" id="thumbnail" type="file"  class="large tipTop" title="<?php if ($this->lang->line('driver_select_driver_image') != '') echo stripslashes($this->lang->line('driver_select_driver_image')); else echo 'Please select driver image'; ?>" value="<?php if (isset($driver_details->driver_name)) echo $driver_details->driver_name; ?>"/>

                                    <br/>

                                    <?php if (isset($driver_details->image) != '') { ?>
                                        <img width="15%" src="<?php echo base_url() . USER_PROFILE_THUMB . $driver_details->image; ?>" />
                                    <?php } else { ?>
                                        <img width="15%" src="<?php echo base_url() . USER_PROFILE_THUMB_DEFAULT; ?>" />
                                    <?php } ?>


                                </div>
                            </div>
                        </li>
						
						<li>
                            <div class="form_grid_12">
                                <label class="field_title"><?php if ($this->lang->line('admin_drivers_gender') != '') echo stripslashes($this->lang->line('admin_drivers_gender')); else echo 'Gender'; ?></label>
                                <div class="form_input">
                                    <select class="required"  name="gender" id="gender" style="height: 31px;  width: 30% !important;">
                                        <option value="" hidden="hidden"><?php if ($this->lang->line('admin_select_gender') != '') echo stripslashes($this->lang->line('admin_select_gender')); else echo 'Select gender'; ?></option>
										<option value="male" <?php if(isset($driver_details->gender)  && $driver_details->gender == 'male') { echo "selected"; } ?>><?php if ($this->lang->line('admin_gender_male') != '') echo stripslashes($this->lang->line('admin_gender_male')); else echo 'Male'; ?></option>
										<option value="female" <?php if(isset($driver_details->gender)  && $driver_details->gender == 'female') { echo "selected"; } ?>><?php if ($this->lang->line('admin_gender_female') != '') echo stripslashes($this->lang->line('admin_gender_female')); else echo 'Female'; ?></option>
                                    </select>
                                </div>
                            </div>
                        </li>
                         </ul>
					<ul class="operator-log-rite">
					      <li>
                            <div class="form_grid_12">
                                <h3><?php if ($this->lang->line('admin_drivers_vehicle_information') != '') echo stripslashes($this->lang->line('admin_drivers_vehicle_information')); else echo 'Vehicle Information'; ?></h3>
                            </div>
                        </li>


                        <?php if ($vehicle_types->num_rows() > 0) { ?>
                            <li>
                                <div class="form_grid_12">
                                    <label class="field_title"><?php if ($this->lang->line('admin_drivers_vehicle_type') != '') echo stripslashes($this->lang->line('admin_drivers_vehicle_type')); else echo 'Vehicle Type'; ?><span class="req">*</span></label>
                                    <div class="form_input">
                                        <select class="required"  name="vehicle_type" id="vehicle_type" style="height: 31px; width: 51%;">
                                            <option value=""><?php if ($this->lang->line('dash_please_choose_vehicle_type') != '') echo stripslashes($this->lang->line('dash_please_choose_vehicle_type')); else echo 'Please choose vehicle type'; ?>... </option>
                                            <?php foreach ($vehicle_types->result() as $vehicles) { ?>
                                                <option value="<?php echo $vehicles->_id; ?>" <?php if ($driver_details->vehicle_type == $vehicles->_id) echo 'selected="selected"'; ?>><?php echo $vehicles->vehicle_type; ?></option>
                                            <?php } ?>
                                        </select>
                                    </div>
                                </div>
                            </li>
                        <?php } ?>
					     <li>
                            <div class="form_grid_12">
                                <label class="field_title"><?php if ($this->lang->line('admin_drivers_vehicle_maker') != '') echo stripslashes($this->lang->line('admin_drivers_vehicle_maker')); else echo 'Vehicle Maker'; ?><span class="req">*</span></label>
                                <div class="form_input">
                                    <select class="required"  name="vehicle_maker" id="vehicle_maker" style="height: 31px;  width: 51%;">
                                        <?php if ($brandList->num_rows() > 0) { ?>
                                            <?php foreach ($brandList->result() as $brand) { ?>
                                                <option value="<?php echo $brand->_id; ?>" <?php
                                                if (isset($driver_details->vehicle_maker)) {
                                                    if ($driver_details->vehicle_maker == $brand->_id)
                                                        echo 'selected="selected"';
                                                }
                                                ?>><?php echo $brand->brand_name; ?></option>
                                                    <?php } ?>
                                                <?php } ?>
                                    </select>
                                </div>
                            </div>
                        </li>
                        <li>
                            <div class="form_grid_12">
                                <label class="field_title"><?php if ($this->lang->line('admin_drivers_vehicle_model') != '') echo stripslashes($this->lang->line('admin_drivers_vehicle_model')); else echo 'Vehicle Model'; ?><span class="req">*</span></label>
                                <div class="form_input">
                                    <select class="required"  name="vehicle_model" id="vehicle_model" style="height: 31px;  width: 51%;">
                                        <option value="" data-years=""><?php if ($this->lang->line('dash_please_choose_vehicle_model') != '') echo stripslashes($this->lang->line('dash_please_choose_vehicle_model')); else echo 'Please choose vehicle model'; ?>...</option>
                                        <?php 
											$sldmodelYrs=array(); 
											if ($modelList->num_rows() > 0) { ?>
                                            <?php foreach ($modelList->result() as $model) { 
												
												$modelYears = '';
												if(isset($model->year_of_model))$modelYears = @implode(',',$model->year_of_model);
												if ($driver_details->vehicle_model == $model->_id){
													if(isset($model->year_of_model)) $sldmodelYrs = $model->year_of_model;
												}
											
											
											?>
                                                <option value="<?php echo $model->_id; ?>" data-years="<?php echo $modelYears; ?>" data-vmodel="<?php echo $model->brand . '_' . $model->type; ?>" <?php
                                                if (isset($driver_details->vehicle_model)) {
                                                    if ($driver_details->vehicle_model == $model->_id)
                                                        echo 'selected="selected"';
                                                }
                                                ?>><?php echo $model->name; ?></option>
                                                    <?php } ?>
                                                <?php } ?>
                                    </select>
                                </div>
                            </div>
                        </li>
						<li>
                            <div class="form_grid_12">
                                <label class="field_title"><?php if ($this->lang->line('admin_drivers_year_of_model') != '') echo stripslashes($this->lang->line('admin_drivers_year_of_model')); else echo 'Year Of Model'; ?><span class="req">*</span></label>
                                <div class="form_input">
                                    <select class="required"  name="vehicle_model_year" id="vehicle_model_year" style="height: 31px;  width: 51%;">
                                        <option value=""><?php if ($this->lang->line('dash_please_choose_year_of_model') != '') echo stripslashes($this->lang->line('dash_please_choose_year_of_model')); else echo 'Please choose year of model'; ?>...</option>
                                        <?php 
											if (count($sldmodelYrs) > 0) { ?>
                                            <?php foreach ($sldmodelYrs as $modelyr) { 			
											?>
                                                <option value="<?php echo $modelyr; ?>" <?php 
												if(isset($driver_details->vehicle_model_year)){ 
												if($driver_details->vehicle_model_year == $modelyr) echo 'selected="selected"';}
												?>><?php echo $modelyr; ?></option>
                                            <?php } ?>
                                        <?php } ?>
                                    </select>
                                </div>
                            </div>
                        </li>

                        <li>
                            <div class="form_grid_12">
                                <label class="field_title"><?php if ($this->lang->line('admin_drivers_vehicle_number') != '') echo stripslashes($this->lang->line('admin_drivers_vehicle_number')); else echo 'Vehicle Number'; ?><span class="req">*</span></label>
                                <div class="form_input">
                                    <input name="vehicle_number" id="vehicle_number" type="text"  class="required large tipTop  Vehicle_Number_Chk" title="<?php if ($this->lang->line('driver_enter_vechile_number') != '') echo stripslashes($this->lang->line('driver_enter_vechile_number')); else echo 'Please enter vehicle number'; ?>" value="<?php if (isset($driver_details->vehicle_number)) echo $driver_details->vehicle_number; ?>"/>
                                    <label class="error_chk" id="vehicle_number_exist"></label>
                                </div>
                            </div>
                        </li>

                        <?php if ($docx_list->num_rows() > 0  && $d_docx > 0) { ?>

                            <li>
                                <div class="form_grid_12">
                                    <h3><?php if ($this->lang->line('admin_drivers_driver_documents') != '') echo stripslashes($this->lang->line('admin_drivers_driver_documents')); else echo 'Driver Documents'; ?></h3>
                                </div>
                            </li>

                            <?php
                            $doc = 0;
                            foreach ($docx_list->result() as $docx) {
                                if ($docx->category == 'Driver') {
                                    $docx_uniq = 'docx-' . $docx->_id;

                                    $docxValues = '';
                                    $expiryValue = '';
                                    $fileName = '';
                                    if (isset($driver_details->documents) && isset($driver_details->documents['driver'][(string) $docx->_id])) {

                                        if (!isset($driver_details->documents['driver'][(string) $docx->_id]['typeName'])) {
                                            continue;
                                        }

                                        #$typeName = $driver_details->documents['driver'][(string) $docx->_id]['typeName'];
								$typeName = $docx->name;
                                        $fileName = $driver_details->documents['driver'][(string) $docx->_id]['fileName'];
                                        $docxValues = $typeName . '|:|' . $fileName . '|:|' . (string) $docx->_id . '|:|Old-docx';
                                        $expiryValue = $driver_details->documents['driver'][(string) $docx->_id]['expiryDate'];
                                    }
                                    ?>

                                    <li>
                                        <div class="form_grid_12">
                                            <label class="field_title"><?php
                                                echo $docx->name;
                                                if ($docx->hasReq == 'Yes') {
                                                    echo '<span class="req">*</span>';
                                                }
                                                ?> </label>
                                            <div class="form_input">
                                                <input name="<?php echo $docx_uniq; ?>" id="<?php echo $docx_uniq; ?>" data-docx="<?php echo $docx->name; ?>" data-docx_id="<?php echo $docx->_id; ?>" type="file"  value="" class="large tipTop <?php
                                                if ($docx->hasReq == 'Yes' && $fileName == '') {
                                                    echo 'required';
                                                }
                                                ?> docx" title="<?php if ($this->lang->line('admin_please_select') != '') echo stripslashes($this->lang->line('admin_please_select')); else echo 'Please select'; ?> <?php echo strtolower($docx->name); ?>"/>
                                                <input type="hidden" name="driver_docx[]" value="<?php echo $docxValues; ?>" id="<?php echo $docx_uniq; ?>-Hid" />
                                                <input type="hidden" name="driver_docx_expiry[]" value="<?php echo $docx->hasExp; ?>" />
                                                <span id="<?php echo $docx_uniq; ?>-Err" style="color:red;"></span>
                                                <span id="<?php echo $docx_uniq; ?>-Succ" style="color:green;"></span>
                                                <?php
                                                if ($fileName != '') {
                                                    ?>
                                                    <a href="drivers_documents/<?php echo $fileName; ?>" target="_blank" id="<?php echo $docx_uniq; ?>-View"> <?php if ($this->lang->line('admin_drivers_view_documents') != '') echo stripslashes($this->lang->line('admin_drivers_view_documents')); else echo 'View Document '; ?></a>
                                                <?php } else { ?>
                                                    <a href="drivers_documents/<?php echo $fileName; ?>" target="_blank" id="<?php echo $docx_uniq; ?>-View"></a>
                                                <?php } ?>
                                            </div>

                                            <?php if ($docx->hasExp == 'Yes') { ?>
                                                <label class="field_title"></label>
                                                <div class="form_input">
                                                    <div class="expiry_box">
                                                        <b><?php if ($this->lang->line('admin_drivers_expiry_date') != '') echo stripslashes($this->lang->line('admin_drivers_expiry_date')); else echo 'Expiry Date'; ?> : </b>
                                                        <input type="text"  id="expiry-<?php echo $docx_uniq; ?>" class="required" name="driver-<?php echo url_title($docx->name); ?>"  value="<?php echo $expiryValue; ?>"/> 
                                                    </div>
                                                </div>

                                                <script>
                                                    $(function () {
                                                        var mdate = new Date('<?php echo date("F d,Y H:i:s"); ?>');
                                                        $("#expiry-<?php echo $docx_uniq; ?>").datepicker();
                                                        $("#expiry-<?php echo $docx_uniq; ?>").datepicker("option", "changeMonth", "true");
                                                        $("#expiry-<?php echo $docx_uniq; ?>").datepicker("option", "changeYear", "true");
                                                        $("#expiry-<?php echo $docx_uniq; ?>").datepicker("option", "minDate", mdate);
                                                        $("#expiry-<?php echo $docx_uniq; ?>").datepicker("option", "showAnim", "clip");
                                                        // drop,fold,slide,bounce,slideDown,blind
                                                    });
                                                </script>

                                            <?php } ?>

                                        </div>
                                    </li>
                                    <?php
                                    $doc++;
                                }
                            }
                        }
                        ?>

                        <?php
                        if ($docx_list->num_rows() > 0 && $v_docx > 0) {
                            ?>
                            <li>
                                <div class="form_grid_12">
                                    <h3><?php if ($this->lang->line('admin_drivers_vehicle_documents') != '') echo stripslashes($this->lang->line('admin_drivers_vehicle_documents')); else echo 'Vehicle Documents'; ?></h3>
                                </div>
                            </li>
                            <?php
                            $doc = 0;
                            foreach ($docx_list->result() as $docx) {
                                if ($docx->category == 'Vehicle') {
                                    $docx_uniq = 'docx-' . $docx->_id;

                                    $docxValues = '';
                                    $expiryValue = '';
                                    $fileName = '';
                                    if (isset($driver_details->documents)) {
                                        $did = (string) $docx->_id;
                                        if (!isset($driver_details->documents['vehicle'][$did]['typeName'])) {
                                            #continue;
                                        }
                                        if(isset($driver_details->documents['vehicle'][$did]['typeName'])&& isset($driver_details->documents['vehicle'][$did]['fileName']) && isset($driver_details->documents['vehicle'][$did]['expiryDate']))
                                        {
                                        #$typeName = $driver_details->documents['vehicle'][$did]['typeName'];
								$typeName = $docx->name;
                                        $fileName = $driver_details->documents['vehicle'][$did]['fileName'];
                                        $docxValues = $typeName . '|:|' . $fileName . '|:|' . $did . '|:|Old-docx';
                                        $expiryValue = $driver_details->documents['vehicle'][$did]['expiryDate'];
                                        }
                                    }
                                    ?>

                                    <li>
                                        <div class="form_grid_12">
                                            <label class="field_title"><?php
                                                echo $docx->name;
                                                if ($docx->hasReq == 'Yes') {
                                                    echo '<span class="req">*</span>';
                                                }
                                                ?> </label>
                                            <div class="form_input">
                                                <input name="<?php echo $docx_uniq; ?>" id="<?php echo $docx_uniq; ?>" data-docx="<?php echo $docx->name; ?>" data-docx_id="<?php echo $docx->_id; ?>" type="file"  value="" class="large tipTop <?php
                                                if ($docx->hasReq == 'Yes' && $fileName == '') {
                                                    echo 'required';
                                                }
                                                ?> docx" title="<?php if ($this->lang->line('admin_please_select') != '') echo stripslashes($this->lang->line('admin_please_select')); else echo 'Please select'; ?><?php echo strtolower($docx->name); ?>"/>
                                                <input type="hidden" name="vehicle_docx[]" value="<?php echo $docxValues; ?>" id="<?php echo $docx_uniq; ?>-Hid" />
                                                <input type="hidden" name="vehicle_docx_expiry[]" value="<?php echo $docx->hasExp; ?>" />
                                                <span id="<?php echo $docx_uniq; ?>-Err" style="color:red;"></span>
                                                <span id="<?php echo $docx_uniq; ?>-Succ" style="color:green;"></span>
                                                <?php
                                                if ($fileName != '') {
                                                    ?>
                                                    <a href="drivers_documents/<?php echo $fileName; ?>" target="_blank" id="<?php echo $docx_uniq; ?>-View"> <?php if ($this->lang->line('admin_drivers_view_documents') != '') echo stripslashes($this->lang->line('admin_drivers_view_documents')); else echo 'View Document'; ?> </a>
                                                <?php } else { ?>
                                                    <a href="drivers_documents/<?php echo $fileName; ?>" target="_blank" id="<?php echo $docx_uniq; ?>-View"></a>
                                                <?php } ?>
                                            </div>

                                            <?php if ($docx->hasExp == 'Yes') { ?>
                                                <label class="field_title"></label>
                                                <div class="form_input">
                                                    <div class="expiry_box">
                                                        <b><?php if ($this->lang->line('admin_drivers_expiry_date') != '') echo stripslashes($this->lang->line('admin_drivers_expiry_date')); else echo 'Expiry Date'; ?> : </b>
                                                        <input type="text"  id="expiry-<?php echo $docx_uniq; ?>" class="required" name="vehicle-<?php echo url_title($docx->name); ?>"  value="<?php echo $expiryValue; ?>"/> 
                                                    </div>
                                                </div>

                                                <script>
                                                    $(function () {
                                                        var mdate = new Date('<?php echo date("F d,Y H:i:s"); ?>');
                                                        $("#expiry-<?php echo $docx_uniq; ?>").datepicker();
                                                        $("#expiry-<?php echo $docx_uniq; ?>").datepicker("option", "changeMonth", "true");
                                                        $("#expiry-<?php echo $docx_uniq; ?>").datepicker("option", "changeYear", "true");
                                                        $("#expiry-<?php echo $docx_uniq; ?>").datepicker("option", "minDate", mdate);
                                                        $("#expiry-<?php echo $docx_uniq; ?>").datepicker("option", "showAnim", "clip");
                                                        // drop,fold,slide,bounce,slideDown,blind
                                                    });
                                                </script>

                                            <?php } ?>

                                        </div>
                                    </li>
                                    <?php
                                    $doc++;
                                }
                            }
                        }
                        ?>	

                        <input name="driver_id" id="driver_id" type="hidden" value="<?php echo $driver_details->_id; ?>" />

                        <li>
                            <div class="form_grid_12">
                                <label class="field_title"><?php if ($this->lang->line('admin_subadmin_status') != '') echo stripslashes($this->lang->line('admin_subadmin_status')); else echo 'Status'; ?> </label>
                                <div class="form_input">
                                    <div class="active_inactive">
                                        <input type="checkbox"  name="status" id="active_inactive_active" class="active_inactive" <?php
                                        if (isset($driver_details->status)) {
                                            if ($driver_details->status == 'Active') {
                                                echo 'checked="checked"';
                                            }
                                        } else {
                                            echo 'checked="checked"';
                                        }
                                        ?>/>
                                    </div>
                                </div>
                            </div>
                        </li>
						</ul>
						<ul class="last-btn-submit">
                        <li>
                            <div class="form_grid_12">
                                <div class="form_input">
									<button type="button" onclick="custom_validate_form();" class="btn_small btn_blue" ><span><?php if ($this->lang->line('admin_common_submit') != '') echo stripslashes($this->lang->line('admin_common_submit')); else echo 'Submit'; ?></span>
									</button><img id="form_loader" src="images/indicator.gif" style="display:none;"/>
									<p class="error" id="user_duplicate_Err" style="margin-top: 12px;"></p>
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

<style>

    .expiry_box {
        background: none repeat scroll 0 0 gainsboro;
        border: 1px solid grey;
        border-radius: 5px;
        margin-top: 2%;
        padding: 1%;
        width: 23%;
    }


    .expiry_box input {
        width:50% !important;
        border-radius: 5px;
        border: 1px solid grey !important;
    }

</style>


<script>

var view_uploaded_document =  "<?php if ($this->lang->line('dash_view_uploaded_document') != '') echo stripslashes($this->lang->line('dash_view_uploaded_document')); else echo 'View Uploaded Document'; ?>";

var dash_please_choose_year_of_model =  "<?php if ($this->lang->line('dash_please_choose_year_of_model') != '') echo stripslashes($this->lang->line('dash_please_choose_year_of_model')); else echo 'Please choose year of model'; ?>";

$(document).ready(function () {
        var catoptions = $("#category").html();
        $("#driver_location").change(function (e) {
            var category_list = $("#driver_location :selected").attr('data-category');
            $("#category").html(catoptions);
            if (category_list == "") {
                $("#category").html('<option value=""><?php if ($this->lang->line('admin_drivers_choose_driver_category') != '') echo stripslashes($this->lang->line('admin_drivers_choose_driver_category')); else echo 'Choose driver category'; ?></option>');
                return;
            } else {
				$('#multi_car_status').prop('checked', false);
				$('#additional_cat_li_chk').css('display','none');
				$('#additional_cat_li').css('display','none');
				
                var vArr = category_list.split(",");
                $("#category option").each(function (e) {
                    var optval = $(this).val();
                    if (optval != '') {
                        if ($.inArray(optval, vArr) == -1) {
                            $('#category option[value="' + optval + '"]').remove();
                        }
                    }
                });
            }
        });
		
		$(document).on('click','#multi_car_status', function (e) {
			if($('#multi_car_status').prop('checked')){ 
				$('#additional_cat_li').show();
			} else {
				$('#additional_category').val('');
				$('#additional_cat_li').hide();
			}
		});
		
        var options = $("#vehicle_type").html();
        $("#category").change(function (e) {
            var vehicle_types = $("#category :selected").attr('data-vehicle');
            
			var cur_cat = $("#category").val();
			var location_id = $("#driver_location").val();		
            $("#vehicle_type").html(options);
			
			$('#additional_cat_li_chk').css('display','none');
			$('#additional_cat_li').css('display','none');
			
			if (cur_cat == "" || location_id == '') {
				$('#additional_category').html('');
            } else {			
				if($('#multi_car_status').prop('checked')){
					$('#additional_cat_li').css('display','block');
				}
				
				$.ajax({
				  type: "POST",
				  url: 'admin/drivers/ajax_get_additional_category_list',
				  data: {'cur_cat':cur_cat,'location_id':location_id},
				  success: function(res){
					$('#additional_cat_li_chk').css('display','block');
					$('#additional_category').html(res.response);
					$('#additional_category').trigger('liszt:updated');
					if(res.status == 'error'){
						$('#multi_car_status').prop('checked', false);
						$('#additional_cat_li_chk').css('display','none');
						$('#additional_cat_li').css('display','none');
					}
				  },
				  dataType: 'JSON'
				});
            }
			
            if (vehicle_types == "") {
				$("#vehicle_type").html(' <option value=""><?php if ($this->lang->line('driver_choose_vehicle_type') != '') echo stripslashes($this->lang->line('driver_choose_vehicle_type')); else echo 'Please choose vehicle type'; ?></option>');
                return;
            } else {
                var vArr = vehicle_types.split(",");
                $("#vehicle_type option").each(function (e) {
                    var optval = $(this).val();
                    if (optval != '') {
                        if ($.inArray(optval, vArr) == -1) {
                            $('#vehicle_type option[value="' + optval + '"]').remove();
                        }
                    }
                });
            }
        });

        // vehicle model
        var vehicleoptions = $("#vehicle_model").html();
        $("#vehicle_maker").change(function (e) {
            var maker = $("#vehicle_maker :selected").val();
            $("#vehicle_model").html(vehicleoptions);
            if (maker == "") {
                return;
            } else {
                var type = $("#vehicle_type :selected").val();
                $("#vehicle_model").html(vehicleoptions);
                if (type == "") {
                    return;
                } else {
                    var models = maker + '_' + type;
                    updatemodelList(models);
                }
            }
        });
        $("#vehicle_type").change(function (e) {
            var type = $("#vehicle_type :selected").val();
            $("#vehicle_model").html(vehicleoptions);
            if (type == "") {
                return;
            } else {
                var maker = $("#vehicle_maker :selected").val();
                $("#vehicle_model").html(vehicleoptions);
                if (maker == "") {
                    return;
                } else {
                    var models = maker + '_' + type;
                    updatemodelList(models);
                }
            }
        });
		
		$("#vehicle_model").change(function (e) {
		var modelYrs = $("#vehicle_model :selected").attr('data-years'); 
		var option = '<option value="" data-years="">'+dash_please_choose_year_of_model+'</option>';
		if(modelYrs != ''){
			var modelYrsArr = modelYrs.split(',');
			for(var yr=0; yr < modelYrsArr.length; yr++){
				option = option+'<option>'+modelYrsArr[yr]+'</option>';
			}
		}
		$("#vehicle_model_year").html(option);
		});
    });
    function updatemodelList(model) {
        $("#vehicle_model option").each(function (e) {
            var vmodel = $(this).attr("data-vmodel");
            if (vmodel != '') {
                if (model != vmodel) {
                    $('#vehicle_model option[data-vmodel="' + vmodel + '"]').remove();
                }
            }
        });
    }
    $(document).ready(function () {
        $("#county").change(function (e) {
            var dail_code = $(this).find(':selected').attr('data-dialCode');  
			$('#country_codeM').val(dail_code).trigger('liszt:updated');
        });

        $(".docx").change(function (e) {
            e.preventDefault();
            var docxId = $(this).attr('id');
            var docxType = $(this).attr('data-docx');
            var docxTypeId = $(this).attr('data-docx_id');
            $("#" + docxId + "-Err").html('<img src="images/indicator.gif" />');
            var formData = new FormData($(this).parents('form')[0]);
            $.ajax({
                url: 'admin/drivers/ajax_document_upload?docx_name=' + docxId,
                type: 'POST',
                xhr: function () {
                    var myXhr = $.ajaxSettings.xhr();
                    return myXhr;
                },
                success: function (data) {
                    if (data.err_msg == 'Success') {
                        $("#" + docxId + "-Hid").val(docxType + '|:|' + data.docx_name + '|:|' + docxTypeId);
                        $("#" + docxId + "-Err").html('');
                        $("#" + docxId + "-View").attr('href', 'drivers_documents_temp/' + data.docx_name);
                        $("#" + docxId + "-View").html(view_uploaded_document);
                        //$("#"+docxId+"-Succ").html('Success');
                    } else {
                        $("#" + docxId).val('');
                        $("#" + docxId + "-Hid").val('');
                        $("#" + docxId + "-Succ").html('');
                        $("#" + docxId + "-Err").html(data.err_msg);
                    }
                },
                data: formData,
                cache: false,
                contentType: false,
                processData: false,
                dataType: "json"
            });
            return false;
        });
    });
</script>


<style>
.chzn-container-multi{
	width: 100% !important;
}
#additional_cat_li_chk {
	margin-top:3%;
}
#additional_cat_li_chk label{
	width: 55% !important;
}
#multi_car_status {
	width: 20px !important;
}
.chzn-choices li{
	height: 25px !important;
	line-height: 21px !important;
	width: 49% !important;
}
.chzn-results li{
	height: 25px !important;
	line-height: 21px !important;
}

.operator-log-rite {
    min-height: 1370px !important;
}
</style>

<?php
$this->load->view(COMPANY_NAME.'/templates/footer.php');
?>