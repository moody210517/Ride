<?php
$this->load->view('driver/templates/profile_header.php');


$hasPhoto = FALSE;
$profilePic = base_url() . USER_PROFILE_IMAGE_DEFAULT;
if (isset($driver_info->row()->image) && $driver_info->row()->image != '') {
    $profilePic = base_url() . USER_PROFILE_THUMB . $driver_info->row()->image;
    $hasPhoto = TRUE;
}

$v_docx = 0; $d_docx = 0;
foreach($docx_list->result() as $docx) if($docx->category == 'Driver') $d_docx++; else $v_docx++;
?>
<section class="profile_pic_sec row edit_form_profile">
   <div  class="profile_login_cont">
		
		<?php
			$this->load->view('driver/templates/profile_sidebar.php');
		?>
		
	   <div class="share_detail">
		   <div class="share_det_title">
			  <h2><span><?php if($this->lang->line('driver_edit_profile') != '') echo stripslashes($this->lang->line('driver_edit_profile')); else  echo 'EDIT PROFILE'; ?> </span></h2>
		   </div>
		     <?php
			$attributes = array('class' => 'form_container left_label', 'id' => 'driver_form', 'enctype' => 'multipart/form-data');
			echo form_open_multipart('driver/profile/insertEdit_driver', $attributes);

			$driver_details = $driver_details->row();
            ?>
			   <div class="profile_ac_inner_det">
				  <div class="inner_full editprofile">
					 <p class="form_sub_title"><?php if($this->lang->line('driver_account_information') != '') echo stripslashes($this->lang->line('driver_account_information')); else  echo 'Account Information'; ?>  </p>
					 <div class="col">
						<li>
						   <p><?php if($this->lang->line('driver_city') != '') echo stripslashes($this->lang->line('driver_city')); else  echo 'CITY'; ?> </p>
						  
							
							<?php 
								if ($this->lang->line('site_user_city_upper') != '') $city = stripslashes($this->lang->line('site_user_city_upper')); else $city = 'CITY';
							
								$driver_loc_name = '';
								foreach ($locationList->result() as $loclist) {  
									if ($driver_details->driver_location == $loclist->_id){
										$driver_loc_name = $loclist->city; break;
									}
								}
                                
                                $input_data = array(
												'id' => 'driver_location',
												'type' => 'text',
												'class' => '',
												'value' => $driver_loc_name,
                                                'disabled' => 'disabled'
								);
								echo form_input($input_data);
								
							?>
							
						  
						</li>
						<li>
						   <p><?php if($this->lang->line('driver_cab_type') != '') echo stripslashes($this->lang->line('driver_cab_type')); else  echo 'CAB TYPE'; ?>  <span class="req">*</span></p>
							
							
							<?php 
								if ($this->lang->line('site_user_cab_type_upper') != '') $cab_type = stripslashes($this->lang->line('site_user_cab_type_upper')); else $cab_type = 'CAB TYPE';
							
								$drop_options = array('' => $cab_type);
								foreach ($categoryList->result() as $category) {
									$vehicle_type = '';
									if (isset($category->vehicle_type)) {
										$vehicle_type = @implode($category->vehicle_type, ',');
									}
									$category_name = $category->name;
									if(isset($category->name_languages[$langCode]) && $category->name_languages[$langCode] != '') $category_name = $category->name_languages[$langCode];
									
									$optkey = (string)$category->_id.'" data-vehicle="'.$vehicle_type; 
									if ($driver_details->category == $category->_id){
										$optkey.= '" selected=selected';
									}
									$drop_options[$optkey] = $category_name;
								}
								
								$input_data = 'class="required" id="category"';
								
								echo form_dropdown('category',$drop_options,$driver_details->category,$input_data);
                                
                                
								
							?>
							
						</li>
					 </div>
					 <div class="col">
						<li>
						    <p><?php if($this->lang->line('dash_driver_name') != '') echo stripslashes($this->lang->line('dash_driver_name')); else  echo 'Driver Name'; ?> <span class="req">*</span></p>
							
							<?php 
								if ($this->lang->line('rider_signup_name_placeholder') != '') $placeholder = stripslashes($this->lang->line('rider_signup_name_placeholder')); else $placeholder =  'Full Name';
								
								$driver_name = '';
								if (isset($driver_details->driver_name)) $driver_name = $driver_details->driver_name; 
								
								$input_data = array(
												'name' => 'driver_name',
												'id' => 'driver_name',
												'type' => 'text',
												'class' => 'required onlyalphabets',
												'placeholder' => $placeholder,
												'value' => $driver_name,
                                                'minlength' => '2',
                                                 'maxlength' => '30'
								);
								echo form_input($input_data);
							?>
							
						</li>
						
						<li>
						   <p><?php if($this->lang->line('dash_email_address') != '') echo stripslashes($this->lang->line('dash_email_address')); else  echo 'Email Address'; ?></p>
						     
							<?php 			
								if ($this->lang->line('cms_email') != '') $placeholder = stripslashes($this->lang->line('cms_email')); else $placeholder = 'Email';
								
								$email = ''; if (isset($driver_details->email)) $email = $driver_details->email; 
								
								$input_data = array(
												'type' => 'text',
												'class' => 'required email',
												'disabled' => 'disabled',
												'value' => $email
								);
								echo form_input($input_data);
							?>
							<a href="driver/profile/change_email_form"  target="_blank">
							<img src="images/site/edit_img.png" class="profile_mobile_edit_img"></a>
						</li>
					 </div>
					 
					 
					<div class="col">
					
						<li>
						   <p><?php if($this->lang->line('dash_driver_image') != '') echo stripslashes($this->lang->line('dash_driver_image')); else  echo 'Driver Image'; ?></p>						
						   <div id="image-holder" class="profile_pic_cont">
								<img class="driver_prof_img_edit" src="<?php echo $profilePic; ?>">
							</div>
							
							<?php 			
								$input_data = array(
												'name' => 'thumbnail',
												'id' => 'image',
												'type' => 'file',
												'class' => 'inputfile inputfile-1 media_image'
								);
								echo form_input($input_data);
							?>

							<button class="wh_btn prv_btn" type="button" onclick="change_profile_photo();"> <?php 
								if($hasPhoto){
									if ($this->lang->line('dash_user_change_photo') != '') echo stripslashes($this->lang->line('dash_user_change_photo')); else echo 'Change Photo'; 
								} else {
									if ($this->lang->line('rider_add_photo') != '') echo stripslashes($this->lang->line('rider_add_photo')); else echo 'Add Photo';
								}
								?> </button>
						   <p id="choosen_file_err" class="error"></p>
						</li>
					
						<li>
							<?php 
								if ($this->lang->line('admin_drivers_gender') != '') $gender_title = stripslashes($this->lang->line('admin_drivers_gender')); else $gender_title = 'Gender';
										
								if ($this->lang->line('admin_gender_male') != '') $male = stripslashes($this->lang->line('admin_gender_male')); else $male = 'Male';
										
								if ($this->lang->line('admin_gender_female') != '') $female = stripslashes($this->lang->line('admin_gender_female')); else $female = 'Female';

								$drop_options = array('"hidden=hidden'=>$gender_title);
								
								$optkey = 'male';
								if($driver_details->gender == 'male'){
									$optkey.= '" selected=selected';
								}
								$drop_options[$optkey] = $male;

								$optkey = 'female';
								if($driver_details->gender == 'female'){
									$optkey.= '" selected=selected';
								}
								$drop_options[$optkey] = $female;								
								$input_data = 'class="required" id="gender"'; ?>

							<p><?php echo $gender_title;?></p>
							<?php echo form_dropdown('gender',$drop_options,'',$input_data); ?>
						</li>
						
						
						
						
						
					</div>
						   
					
					 <p class="form_sub_title secondary_form" id="address_info"><?php if($this->lang->line('driver_address_information') != '') echo stripslashes($this->lang->line('driver_address_information')); else  echo 'Address Information'; ?>  </p>
					 <div class="col">
						<li>
						   <p><?php if($this->lang->line('dash_address') != '') echo stripslashes($this->lang->line('dash_address')); else  echo 'Address'; ?> <span class="req">*</span></p>
						   
						    <?php 
								if ($this->lang->line('driver_your_address') != '') $placeholder =  stripslashes($this->lang->line('driver_your_address')); else $placeholder = 'Your Address'; 
								
								$address = '';
								if (isset($driver_details->address['address'])) $address = $driver_details->address['address'];
								
								$input_data = array('name' => 'address',
													'type' => 'text',
													'id' => 'address',
													'class' => 'required',
													'placeholder' => $placeholder,
													'value' => $address
								);
								echo form_input($input_data);
							?>
						</li>
						<li>
						   <p><?php if($this->lang->line('dash_country') != '') echo stripslashes($this->lang->line('dash_country')); else  echo 'Country'; ?> <span class="req">*</span></p>
							 
							<?php 
								$drop_options = array();
								foreach ($countryList as $country) {  
									$optkey = $country->name.'" data-dialCode="'.$country->dial_code; 
									if(isset($driver_details->address['county']) && $country->name == $driver_details->address['county']){
										$optkey.= '" selected=selected';
									}
									$drop_options[$optkey]=$country->name;
									
								}
								$input_data = 'class="required chzn-select" id="county"';
								
								echo form_dropdown('county',$drop_options,'',$input_data);
								
							?>
							 
						</li>
					 </div>
					 <div class="col">
						<li>
						    <p><?php 
						if($this->lang->line('dash_state_province_region') != '') echo stripslashes($this->lang->line('dash_state_province_region')); else  echo 'State / Province / Region';
						?> <span class="req">*</span></p>
						
						<?php 
							if ($this->lang->line('driver_state_province_region_your') != '') $placeholder =  stripslashes($this->lang->line('driver_state_province_region_your')); else $placeholder = 'Your State / Province / Region'; 
							
							$state = ''; if(isset($driver_details->address['state'])) $state = $driver_details->address['state'];
							
							$input_data = array('name' => 'state',
												'type' => 'text',
												'id' => 'state',
												'class' => 'required',
												'placeholder' => $placeholder,
												'value' => $state,
							);
							echo form_input($input_data);
						?>
						
						</li>
						<li>
						   <p><?php 
						if($this->lang->line('dash_city') != '') echo stripslashes($this->lang->line('dash_city')); else  echo 'City';
						?> <span class="req">*</span></p>
						
						
							<?php 
								if ($this->lang->line('driver_your_city') != '') $placeholder =  stripslashes($this->lang->line('driver_your_city')); else $placeholder = 'Your City'; 
								
								$city = ''; if (isset($driver_details->address['city'])) $city = $driver_details->address['city'];
								
								$input_data = array('name' => 'city',
													'type' => 'text',
													'id' => 'city',
													'class' => 'required',
													'placeholder' => $placeholder,
													'value' => $city
								);
								echo form_input($input_data);
							?>
						
						</li>
					 </div>
					 <div class="col">
						<li>
						    <p><?php if ($this->lang->line('user_postal_code') != '') echo stripslashes($this->lang->line('user_postal_code')); else echo 'Postal Code'; ?> <span class="req">*</span></p>
						    
							
							<?php 
								$postal_code = '';
								if (isset($driver_details->address['postal_code'])) $postal_code = $driver_details->address['postal_code'];
								
								$input_data = array('name' => 'postal_code',
													'type' => 'text',
													'id' => 'postal_code',
													'class' => 'required',
													'placeholder' => '000 111',
													'maxlength' => '10',
													'value' => $postal_code
								);
								echo form_input($input_data);
							?>
							
						</li>
						
						<li>
						   <p><?php 
						if($this->lang->line('dash_mobile_number') != '') echo stripslashes($this->lang->line('dash_mobile_number')); else  echo 'Mobile Number';
						?> </p>
						
							<?php 
								
								$mobile_number = '';
								if (isset($driver_details->dail_code)) $mobile_number = $driver_details->dail_code; 
								if (isset($driver_details->mobile_number)) $mobile_number.=$driver_details->mobile_number; 
								
								$input_data = array(
													'type' => 'text',
													'disabled' => 'disabled',
													'value' => $mobile_number
								);
								echo form_input($input_data);
							?>
							
							<a href="driver/profile/change_mobile_form" target="_blank"><img src="images/site/edit_img.png" class="profile_mobile_edit_img"></a>
						</li>
						
						
						
					 </div>
					 
					
					 <p class="form_sub_title secondary_form veh_info"><?php 
						if($this->lang->line('dash_vehicle_information') != '') echo stripslashes($this->lang->line('dash_vehicle_information')); else  echo 'Vehicle Information';
						?></p>
					 <div class="col">
						<li>
						    <p><?php 
						if($this->lang->line('dash_vehicle_type') != '') echo stripslashes($this->lang->line('dash_vehicle_type')); else  echo 'Vehicle Type';
						?> <span class="req">*</span></p>
						
							<?php 
								
								if ($this->lang->line('driver_choose_vehicle_type') != '') $choose = stripslashes($this->lang->line('driver_choose_vehicle_type')); else $choose = 'Please choose vehicle type... ';
							
								$drop_options = array('' => $choose);
								foreach ($vehicle_types->result() as $vehicles) {  
									$drop_options[(string)$vehicles->_id]=$vehicles->vehicle_type;
								}
								$input_data = 'class="required" id="vehicle_type"';
								
								echo form_dropdown('vehicle_type',$drop_options,(string)$driver_details->vehicle_type,$input_data);
								
							?>
							
						</li>
						<li>
						   <p><?php 
						if($this->lang->line('dash_vehicle_maker') != '') echo stripslashes($this->lang->line('dash_vehicle_maker')); else  echo 'Vehicle Maker';
						?> <span class="req">*</span></p>
						
							<?php 
								
								if ($this->lang->line('driver_choose_vehicle_maker') != '') $choose = stripslashes($this->lang->line('driver_choose_vehicle_maker')); else $choose = 'Please choose vehicle maker...';
							
								$drop_options = array('' => $choose);
								foreach ($brandList->result() as $brand) {  
									$drop_options[(string)$brand->_id]=$brand->brand_name;
								}
								$input_data = 'class="required" id="vehicle_maker"';
								
								echo form_dropdown('vehicle_maker',$drop_options,(string)$driver_details->vehicle_maker,$input_data);
								
							?>
						
						</li>
					 </div>
					 <div class="col">
						<li>
						   <p><?php 
						if($this->lang->line('dash_vehicle_model') != '') echo stripslashes($this->lang->line('dash_vehicle_model')); else  echo 'Vehicle Model';
						?> <span class="req">*</span></p>
						
							<?php 
								
								if ($this->lang->line('driver_choose_vehicle_model') != '') $choose = stripslashes($this->lang->line('driver_choose_vehicle_model')); else $choose = 'Please choose vehicle model...';
							
								$drop_options = array('' => $choose);
								$sldmodelYrs = array();
								foreach ($modelList->result() as $model) {  
									$modelYears = '';
									if(isset($model->year_of_model))$modelYears = @implode(',',$model->year_of_model);
									
									$optkey = (string)$model->_id.'" data-years="'.$modelYears.'" data-vmodel="'.$model->brand . '_' . $model->type;
									
									if($driver_details->vehicle_model == $model->_id){
										$optkey.= '" selected=selected';
										$sldmodelYrs = $model->year_of_model;
									}
									
									$drop_options[$optkey]=$model->name;
								}
								$input_data = 'class="required" id="vehicle_model"';
								echo form_dropdown('vehicle_model',$drop_options,(string)$driver_details->vehicle_model,$input_data);
								
							?>
						
							
						</li>
						<li>
						   <p><?php 
						if($this->lang->line('dash_year_of_model') != '') echo stripslashes($this->lang->line('dash_year_of_model')); else  echo 'Year Of Model';
						?> <span class="req">*</span></p>
						
							<?php 
								if ($this->lang->line('dash_please_choose_year_of_model') != '') $choose = stripslashes($this->lang->line('dash_please_choose_year_of_model')); else $choose = 'Please choose year of model';
							
								$drop_options = array('' => $choose.'...');
								foreach ($sldmodelYrs as $modelyr) {  
									$drop_options[$modelyr]=$modelyr;
								}
								$input_data = 'class="required" id="vehicle_model_year"';
								
								echo form_dropdown('vehicle_model_year',$drop_options,$driver_details->vehicle_model_year,$input_data);
							?>
						
							
						</li>
					 </div>
					 <div class="col">
						<li>
						   <p><?php 
						if($this->lang->line('dash_vehicle_number') != '') echo stripslashes($this->lang->line('dash_vehicle_number')); else  echo 'Vehicle Number';
						?> <span class="req">*</span></p>
						
							<?php 
								$vehicle_number = '';
								if(isset($driver_details->vehicle_number)) $vehicle_number = $driver_details->vehicle_number;
								$input_data = array(
												'id' => 'vehicle_number',
												'name' => 'vehicle_number',
												'type' => 'text',
												'class' => 'required Vehicle_Number_Chk',
												'placeholder' => 'ABC 00 0000',
												'value' => $vehicle_number
								);
								echo form_input($input_data);
							?>
                            <p class="error_chk" id="vehicle_number_exist"></p>
						</li>						
					 </div>
					
					  <?php if ($docx_list->num_rows() > 0  && $d_docx > 0) { ?>
					 <p class="form_sub_title secondary_form docu_info"><?php 
						if($this->lang->line('dash_driver_documents') != '') echo stripslashes($this->lang->line('dash_driver_documents')); else  echo 'Driver Documents';
						?></p>
						<?php
                            $doc = 0;
                            foreach ($docx_list->result() as $docx) {
                                if ($docx->category == 'Driver') {
                                    $docx_uniq = 'docx-' . $docx->_id;

                                    $docxValues = '';
                                    $expiryValue = '';
                                    $fileName = '';
                                    if (isset($driver_details->documents) && isset($driver_details->documents['driver'][(string) $docx->_id])) {
                                        $did = (string) $docx->_id;
                                        if (!isset($driver_details->documents['driver'][$did]['typeName'])) {
                                            continue;
                                        }
                                        #$typeName = $driver_details->documents['driver'][(string) $docx->_id]['typeName'];
										$typeName = $docx->name;
                                        $fileName = $driver_details->documents['driver'][(string) $docx->_id]['fileName'];
                                        $docxValues = $typeName . '|:|' . $fileName . '|:|' . (string) $docx->_id . '|:|Old-docx';
                                        $expiryValue = $driver_details->documents['driver'][(string) $docx->_id]['expiryDate'];
                                    }
                                    ?>
									<div class="col">
										<li>
										   <p>
												<span class="l_label">
													<?php
														echo $docx->name;
														if ($docx->hasReq == 'Yes')  echo ' <span class="req">*</span>';
														?>
												</span>
												
											</p>
											
											<?php 
												$hasReq = '';
												if ($docx->hasReq == 'Yes' && $fileName == '') $hasReq = 'required';
												$input_data = array(
																'id' => $docx_uniq,
																'name' => $docx_uniq,
																'type' => 'file',
																'class' => 'inputfile inputfile-1 docx '.$hasReq,
																'data-docx' => $docx->name,
																'data-docx_id' => (string)$docx->_id
												);
												echo form_input($input_data);
											?>
										   
										   
										   
											<label for="<?php echo $docx_uniq; ?>" style="display:<?php if ($fileName != '') echo 'none'; ?>;" id="<?php echo $docx_uniq; ?>-lbl">&nbsp;</label>
											
											<?php 
												$input_data = array(
																'id' => $docx_uniq.'-Hid',
																'name' => 'driver_docx[]',
																'type' => 'hidden',
																'value' => $docxValues
												);
												echo form_input($input_data);
												
												$input_data = array(
																'name' => 'driver_docx_expiry[]',
																'type' => 'hidden',
																'value' => $docx->hasExp
												);
												echo form_input($input_data);
											?>
											
											<span id="<?php echo $docx_uniq; ?>-Err" style="color:red;"></span>
											<span id="<?php echo $docx_uniq; ?>-Succ" style="color:green;"></span>
											
											
											<p class="image_doc" id="<?php echo $docx_uniq; ?>-pbox" style="display:<?php if ($fileName == '') echo 'none'; ?>;">
												<span class="r_label"><a target="_blank" href="drivers_documents_temp/<?php echo $fileName; ?>"><?php if($this->lang->line('dash_view_document') != '') echo stripslashes($this->lang->line('dash_view_document')); else  echo 'View Document'; ?></a></span><button onclick="toggle_upload_option('<?php echo $docx_uniq; ?>');" type="button"><?php if($this->lang->line('driver_change_document') != '') echo stripslashes($this->lang->line('driver_change_document')); else  echo 'Change Document'; ?></button>
											</p>
										</li>
										
										 <?php if ($docx->hasExp == 'Yes') { ?>
										<li>
										   <p><?php 
												if($this->lang->line('dash_expiry_date') != '') echo stripslashes($this->lang->line('dash_expiry_date')); else  echo 'Expiry Date';
												?> <span class="req">*</span></p>
											
											<?php 
												$input_data = array(
																'id' => 'expiry-'.$docx_uniq,
																'name' => 'driver-'.url_title($docx->name),
																'type' => 'text',
																'class' => 'required',
																'value' => $expiryValue
												);
												echo form_input($input_data);
											?>
											
										   <img src="images/site/date_img.png"/>
										</li>
									
										<script>
											$(function () {		
												var mdate = new Date('<?php echo date("F d,Y H:i:s"); ?>');
												$("#expiry-<?php echo $docx_uniq; ?>").datetimepicker({
													minView: 2,
													format: 'mm/dd/yyyy',
													autoclose: true,
													startDate: mdate
												});
											});
										</script>
									 </div>
									 <?php } ?>
                                    <?php
                                    $doc++;
                                }
                            }
                        }
                        ?>
					 
					 <?php if ($docx_list->num_rows() > 0 && $v_docx > 0) { ?>
					 <p class="form_sub_title secondary_form docu_info"><?php 
						if($this->lang->line('dash_vehicle_documents') != '') echo stripslashes($this->lang->line('dash_vehicle_documents')); else  echo 'Vehicle Documents';
						?></p>
						<?php
                            $doc = 0;
                            foreach ($docx_list->result() as $docx) {
                                if ($docx->category == 'Vehicle') {
                                    $docx_uniq = 'docx-' . $docx->_id;

                                    $docxValues = '';
                                    $expiryValue = '';
                                    $fileName = '';
                                    if (isset($driver_details->documents) && isset($driver_details->documents['vehicle'][(string) $docx->_id])) {
                                        $did = (string) $docx->_id;
                                        if (!isset($driver_details->documents['vehicle'][$did]['typeName'])) {
                                            continue;
                                        }
										$typeName = $docx->name;
                                        $fileName = $driver_details->documents['vehicle'][(string) $docx->_id]['fileName'];
                                        $docxValues = $typeName . '|:|' . $fileName . '|:|' . (string) $docx->_id . '|:|Old-docx';
                                        $expiryValue = $driver_details->documents['vehicle'][(string) $docx->_id]['expiryDate'];
                                    }
                                    ?>
									<div class="col">
										<li>
										   <p>
												<span class="l_label">
													<?php
														echo $docx->name;
														if ($docx->hasReq == 'Yes')  echo ' <span class="req">*</span>';
														?>
												</span>
											</p>
											
											
											<?php 
												$hasReq = '';
												if ($docx->hasReq == 'Yes' && $fileName == '') $hasReq = 'required';
												$input_data = array(
																'id' => $docx_uniq,
																'name' => $docx_uniq,
																'type' => 'file',
																'class' => 'inputfile inputfile-1 docx '.$hasReq,
																'data-docx' => $docx->name,
																'data-docx_id' => (string)$docx->_id
												);
												echo form_input($input_data);
											?>
										   
										   
											<label for="<?php echo $docx_uniq; ?>" style="display:<?php if ($fileName != '') echo 'none'; ?>;" id="<?php echo $docx_uniq; ?>-lbl">&nbsp;</label>
											
											<?php 
												$input_data = array(
																'id' => $docx_uniq.'-Hid',
																'name' => 'vehicle_docx[]',
																'type' => 'hidden',
																'value' => $docxValues
												);
												echo form_input($input_data);
												
												$input_data = array(
																'name' => 'vehicle_docx_expiry[]',
																'type' => 'hidden',
																'value' => $docx->hasExp
												);
												echo form_input($input_data);
											?>
											
											<span id="<?php echo $docx_uniq; ?>-Err" style="color:red;"></span>
											<span id="<?php echo $docx_uniq; ?>-Succ" style="color:green;"></span>
											
											<p class="image_doc" id="<?php echo $docx_uniq; ?>-pbox" style="display:<?php if ($fileName == '') echo 'none'; ?>;">
												<span class="r_label"><a target="_blank" href="drivers_documents_temp/<?php echo $fileName; ?>"><?php if($this->lang->line('dash_view_document') != '') echo stripslashes($this->lang->line('dash_view_document')); else  echo 'View Document'; ?></a></span><button onclick="toggle_upload_option('<?php echo $docx_uniq; ?>');" type="button"><?php if($this->lang->line('driver_change_document') != '') echo stripslashes($this->lang->line('driver_change_document')); else  echo 'Change Document'; ?></button>
											</p>
											
										</li>
										
										 <?php if ($docx->hasExp == 'Yes') { ?>
										<li>
										   <p><?php if($this->lang->line('dash_expiry_date') != '') echo stripslashes($this->lang->line('dash_expiry_date')); else  echo 'Expiry Date'; ?> <span class="req">*</span></p>
											
											<?php 
												$input_data = array(
																'id' => 'expiry-'.$docx_uniq,
																'name' => 'vehicle-'.url_title($docx->name),
																'type' => 'text',
																'class' => 'required',
																'value' => $expiryValue
												);
												echo form_input($input_data);
											?>
											
										   <img src="images/site/date_img.png"/>
										</li>
									
										<script>
											$(function () {		
												var mdate = new Date('<?php echo date("F d,Y H:i:s"); ?>');
												$("#expiry-<?php echo $docx_uniq; ?>").datetimepicker({
													minView: 2,
													format: 'mm/dd/yyyy',
													autoclose: true,
													startDate: mdate
												});
											});
										</script>
									 </div>
									 <?php } ?>
                                    <?php
                                    $doc++;
                                }
                            }
                        }
                        ?>
						
						<?php if(count($additional_category) > 0 && $multiCategoryOption == 'ON'){ ?>
						<div class="col docu_sub">
							<label class="form_sub_title secondary_form docu_info" for="multi_car_status"><?php if ($this->lang->line('accept_additional_category') != '') echo stripslashes($this->lang->line('accept_additional_category')); else echo 'Accept Rides From Additional Categories'; ?> <span style="color:grey; font-size:12px;">( <?php echo @implode(',',$additional_category)?> )</span>?</label>
							<input type="checkbox" name="multi_car_status" id="multi_car_status" <?php if(isset($driver_details->multi_car_status) && $driver_details->multi_car_status == 'ON') echo 'checked="checked"'; ?>  />
						</div>
						<?php } else { ?>
							<input type="hidden" name="multi_car_status" value="OFF" />
						<?php } ?>
						<input type="hidden" name="additional_category" value="<?php echo @implode(',',$additonalCatsId)?>">
						
						
					 <div class="col docu_sub">
						<li>
						   <input type="submit" value="<?php 
												if($this->lang->line('driver_save_information') != '') echo stripslashes($this->lang->line('driver_save_information')); else  echo 'Save Information';
												?>">
						</li>
					 </div>
					 
					 <input name="driver_id" type="hidden" value="<?php echo $driver_details->_id; ?>" />
					 
				  </div>
			   </div>
			   <input type="hidden" name="new_uploaded_document" id="new_uploaded_document" />

		   </form>
		</div>
	</div>
</section>


<style>
.profile_mobile_edit_img {
    right: 4px !important;
    top: 34px  !important;
}
</style>



<script>
	$('#driver_form').validate();
    $(document).ready(function () {
        var options = $("#vehicle_type").html();
        $("#category").change(function (e) {
            var vehicle_types = $("#category :selected").attr('data-vehicle');
            $("#vehicle_type").html(options); 
            if (vehicle_types == "") {
				$("#vehicle_type").html('<option value=""><?php 
				if($this->lang->line('dash_please_choose_vehicle_type') != '') echo stripslashes($this->lang->line('dash_please_choose_vehicle_type')); else  echo 'Please choose vehicle type';
				?>... </option>');
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
			var option = '<option value=""><?php 
						if($this->lang->line('dash_please_choose_year_of_model') != '') echo stripslashes($this->lang->line('dash_please_choose_year_of_model')); else  echo 'Please choose year of model';
						?>...</option>';
			if(modelYrs != ''){
				var modelYrsArr = modelYrs.split(',');
				for(var yr=0; yr < modelYrsArr.length; yr++){
					option = option+'<option>'+modelYrsArr[yr]+'</option>';
				}
			}
			$("#vehicle_model_year").html(option);
		});
		
		
		 $("#image").change(function (e) { 
            e.preventDefault();
            if (typeof (FileReader) != "undefined") {
                var image_holder = $("#image-holder");
                var reader = new FileReader();
                if(typeof($(this)[0].files[0]) == 'undefined') {
					//image_holder.empty();
					$('.profile_pic_cont').html('<img src="<?php echo $profilePic; ?>" class="driver_prof_img_edit">');
					return false;
				}
                reader.onload = function (e) {
                    var fname = $("#image").val();  
                    fname = fname.replace("fakepath",""); 
                    fname = fname.replace("C:\\",""); 
                    fname = fname.replace("\\",""); 
                    var res = e.target.result; 
                    var ext = res.substring(11, 14);
                    extensions = ['jpg', 'jpe', 'gif', 'png', 'bmp'];
                    if ($.inArray(ext, extensions) !== -1) {
                        var image = new Image();
                        image.src = e.target.result;
	
                        image.onload = function () {
                            if (this.width >= 75 && this.height >= 42) {
                                 image_holder.empty();
                                $("<img />", {
                                    "src": e.target.result,
                                    "id": "thumb-image",
                                    "class": "driver_prof_img_edit",
									"style": "width:100px;height:100px;",
                                }).appendTo(image_holder);
                                $('#choosen_file_err').html('');
                                $('.editprofile_pic_label').html(fname);
                            } else {
								$('#image').val('');
                                $('#choosen_file_err').html("<?php if ($this->lang->line('user_upload_image_too_small') != '') echo stripslashes($this->lang->line('user_upload_image_too_small')); else echo 'Upload Image Too Small. Please Upload Image Size More than or Equalto 75 X 42 .'; ?>");
                            }
                        };
                    }  else {
						$('#image').val('');
                        $('#choosen_file_err').html("<?php if ($this->lang->line('user_please_select_an_image') != '') echo stripslashes($this->lang->line('user_please_select_an_image')); else echo 'Please Select an Image file'; ?>");
                    }
                }
                image_holder.show();
                reader.readAsDataURL($(this)[0].files[0]);
            }
        });
		
    });


    function updatemodelList(model) {
		$("#vehicle_model").val('');
        $("#vehicle_model option").each(function (e) {
            var vmodel = $(this).attr("data-vmodel");
            if (vmodel != '') {
                if (model != vmodel) {
                    $('#vehicle_model option[data-vmodel="' + vmodel + '"]').remove();
                }
            }
        });
		$("#vehicle_model_year").val('');
    }
    $(document).ready(function () {
        $("#county").change(function (e) {
            var dail_code = $(this).find(':selected').attr('data-dialCode'); //.data('dialCode'); 
            $('#country_code').val(dail_code);
        });

        $(".docx").change(function (e) {
		e.preventDefault();
		var docxId = $(this).attr('id');
		var docxType = $(this).attr('data-docx');
		var docxTypeId = $(this).attr('data-docx_id');
		$("#" + docxId + "-Err").html('<img src="images/indicator.gif" />');
		var formData = new FormData($(this).parents('form')[0]);
		$.ajax({
			url: 'driver/profile/ajax_document_upload?docx_name=' + docxId,
			type: 'POST',
			xhr: function () {
				var myXhr = $.ajaxSettings.xhr();
				return myXhr;
			},
			success: function (data) {
				if (data.err_msg == 'Success') {
					$("#" + docxId + "-Hid").val(docxType + '|:|' + data.docx_name + '|:|' + docxTypeId);
					$("#" + docxId + "-Err").html('');
					
					$("#"+docxId+"-pbox").html('<span class="r_label"><a target="_blank" href="drivers_documents_temp/'+ data.docx_name+'"><?php if($this->lang->line('dash_view_document') != '') echo stripslashes($this->lang->line('dash_view_document')); else  echo 'View Document'; ?></a></span><button onclick="toggle_upload_option(\''+docxId+'\');" type="button"><?php if($this->lang->line('driver_change_document') != '') echo stripslashes($this->lang->line('driver_change_document')); else  echo 'Change Document'; ?></button>');
					$("#"+docxId+"-lbl").hide();
					$("#"+docxId+"-pbox").show();
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
	
	
function toggle_upload_option(docxId){ 
	$("#"+docxId+"-lbl").show();
	$("#"+docxId+"-pbox").hide();
}

</script>
<style>
p.image_doc span {
    width: 45%;
	padding: 3%;
	text-transform: none;
}
p.image_doc button {
	padding: 3%;
}
.editprofile_pic_label {
    color: #000 !important;
    font-size: 14px !important;
    font-weight: normal !important;
}
.inner_full.editprofile .col li select { 
    padding: 9px 14px;
}
#multi_car_status {
	width: 30px;
}
</style>

<?php
$this->load->view('driver/templates/footer.php');
?>