<?php
$this->load->view('site/templates/common_header');

$v_docx = 0; $d_docx = 0;
foreach($docx_list->result() as $docx) if($docx->category == 'Driver') $d_docx++; else $v_docx++;

//print_R($docx_list->result());die;
?>


<section class="profile_pic_sec row">
	<div  class="profile_login_cont">

		<div class="share_detail dirver_reg_form">
			<div class="share_det_title">
			   <h2><?php if ($this->lang->line('driver_complete_registration') != '') echo stripslashes($this->lang->line('driver_complete_registration')); else echo 'COMPLETE REGISTRATION';
						?></h2>
			</div>
			
			<?php 
				$formArr = array('id' => 'driver_register_form','enctype' => 'multipart/form-data');
				echo form_open('site/app_driver/register',$formArr);
			?>
			
				<div class="profile_ac_inner_det">
				   <div class="inner_full editprofile">
					  <p class="form_sub_title secondary_form driver_register_form_detail"><?php if ($this->lang->line('driver_address_details') != '') echo stripslashes($this->lang->line('driver_address_details')); else echo 'Address Details';
						?></p>
					  <div class="col">
						 <li>
							<p><?php if ($this->lang->line('cms_address') != '') echo stripslashes($this->lang->line('cms_address')); else echo 'Address';
								?> <span class="req">*</span></p>
								
							<?php 
								if ($this->lang->line('driver_your_address') != '') $placeholder =  stripslashes($this->lang->line('driver_your_address')); else $placeholder = 'Your Address'; 
								$input_data = array('name' => 'address',
													'type' => 'text',
													'id' => 'address',
													'class' => 'required',
													'placeholder' => $placeholder
								);
								echo form_input($input_data);
							?>
								
						 </li>
						 <li>
							<p><?php if ($this->lang->line('driver_country') != '') echo stripslashes($this->lang->line('driver_country')); else echo 'Country'; ?> <span class="req">*</span></p>
                            
                            <option value=""><?php if ($this->lang->line('select_country') != '') $select_country = stripslashes($this->lang->line('select_country')); else $select_country = 'Select country'; ?></option>
							
							<?php 
								$drop_options = array();
                                $drop_options[''] = $select_country;
								foreach ($countryList as $country) {  
									$optkey = $country->name.'" data-dialCode="'.$country->dial_code; 
									if($country->dial_code == $driver_data->row()->dail_code){
										$optkey = $optkey.'" selected=selected';
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
							<p><?php if ($this->lang->line('driver_state_province_region') != '') echo stripslashes($this->lang->line('driver_state_province_region')); else echo 'State / Province / Region'; ?> <span class="req">*</span></p>
							
							<?php 
								if ($this->lang->line('driver_state_province_region_your') != '') $placeholder =  stripslashes($this->lang->line('driver_state_province_region_your')); else $placeholder = 'Your State / Province / Region'; 
								$input_data = array('name' => 'state',
													'type' => 'text',
													'id' => 'state',
													'class' => 'required',
													'placeholder' => $placeholder
								);
								echo form_input($input_data);
							?>
							
						 </li>
						 <li>
							<p><?php if ($this->lang->line('cms_city') != '') echo stripslashes($this->lang->line('cms_city')); else echo 'City'; ?> <span class="req">*</span></p>
							
							<?php 
								if ($this->lang->line('driver_your_city') != '') $placeholder =  stripslashes($this->lang->line('driver_your_city')); else $placeholder = 'Your City'; 
								
								$city = ''; if (isset($locationDetail->city)) $city = $locationDetail->city;
								
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
								
								$input_data = array('name' => 'postal_code',
													'type' => 'text',
													'id' => 'postal_code',
													'class' => 'required',
													'placeholder' => '000 111',
													'maxlength' => '10'
								);
								echo form_input($input_data);
							?>
							
						 </li>
					  </div>
					 
					  <p class="form_sub_title secondary_form veh_info"><?php if ($this->lang->line('driver_identity') != '')  echo stripslashes($this->lang->line('driver_identity')); else echo 'Identity'; ?></p>
					  <div class="col">
						 <li>
							<p><?php if ($this->lang->line('driver_your_profile_image') != '') echo stripslashes($this->lang->line('driver_your_profile_image')); else echo 'Your Profile Image'; ?></p>
							
							<?php 
								
								$input_data = array('name' => 'thumbnail',
													'type' => 'file',
													'id' => 'thumbnail',
													'value' => $city
								);
								echo form_input($input_data);
							?>
							
							<label class="editprofile_pic_label">&nbsp;</label>
							<p id="profile_image_failure" style="color:red"></p>
						 </li>
						
					  </div>
					  
					  
					  <p class="form_sub_title secondary_form veh_info"><?php if ($this->lang->line('driver_vehicle_info') != '') echo stripslashes($this->lang->line('driver_vehicle_info')); else echo 'Vehicle Information'; ?></p>
					  <div class="col">
						 <li>
							<p><?php if ($this->lang->line('driver_vehicle_type') != '') echo stripslashes($this->lang->line('driver_vehicle_type')); else echo 'Vehicle Type'; ?> <span class="req">*</span></p>
							
							<?php 
								
								if ($this->lang->line('driver_choose_vehicle_type') != '') $choose = stripslashes($this->lang->line('driver_choose_vehicle_type')); else $choose = 'Please choose vehicle type... ';
							
								$drop_options = array('' => $choose);
								foreach ($vehicle_types->result() as $vehicles) {  
									$drop_options[(string)$vehicles->_id]=$vehicles->vehicle_type;
								}
								$input_data = 'class="required" id="vehicle_type"';
								
								echo form_dropdown('vehicle_type',$drop_options,'',$input_data);
								
							?>
							
						 </li>
						 <li>
							<p><?php if ($this->lang->line('driver_vehicle_maker') != '') echo stripslashes($this->lang->line('driver_vehicle_maker')); else echo 'Vehicle Maker'; ?> <span class="req">*</span></p>
							
							<?php 
								
								if ($this->lang->line('driver_choose_vehicle_maker') != '') $choose = stripslashes($this->lang->line('driver_choose_vehicle_maker')); else $choose = 'Please choose vehicle maker...';
							
								$drop_options = array('' => $choose);
								foreach ($brandList->result() as $brand) {  
									$drop_options[(string)$brand->_id]=$brand->brand_name;
								}
								$input_data = 'class="required" id="vehicle_maker"';
								
								echo form_dropdown('vehicle_maker',$drop_options,'',$input_data);
								
							?>
							
						 </li>
					  </div>
					  <div class="col">
						 <li>
							<p><?php if ($this->lang->line('dash_vehicle_model') != '') echo stripslashes($this->lang->line('dash_vehicle_model')); else echo 'Vehicle Model'; ?> <span class="req">*</span></p>
							
							<?php 
								
								if ($this->lang->line('driver_choose_vehicle_model') != '') $choose = stripslashes($this->lang->line('driver_choose_vehicle_model')); else $choose = 'Please choose vehicle model...';
							
								$drop_options = array('' => $choose);
								$syr = 0;
								foreach ($modelList->result() as $model) {  
									$modelYears = '';
									if(isset($model->year_of_model))$modelYears = @implode(',',$model->year_of_model);
									if($syr == 0)$sldmodelYrs = $model->year_of_model;
									$syr++;
									$optkey = (string)$model->_id.'" data-years="'.$modelYears.'" data-vmodel="'.$model->brand . '_' . $model->type;
									$drop_options[$optkey]=$model->name;
								}
								$input_data = 'class="required" id="vehicle_model"';
								echo form_dropdown('vehicle_model',$drop_options,'',$input_data);
								
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
								
								echo form_dropdown('vehicle_model_year',$drop_options,'',$input_data);
							?>
							
							
						 </li>
					  </div>
					  <div class="col">
						 <li>
							<p><?php if ($this->lang->line('driver_vehicle_number') != '') echo stripslashes($this->lang->line('driver_vehicle_number')); else echo 'Vehicle Number'; ?> <span class="req">*</span></p>
							
                             <?php if ($this->lang->line('driver_enter_vehicle_number') != '') $driver_enter_vehicle_number = stripslashes($this->lang->line('driver_enter_vehicle_number')); else $driver_enter_vehicle_number = 'Enter your vehicle number'; ?>
                            
							<?php 
								$input_data = array(
												'id' => 'vehicle_number',
												'name' => 'vehicle_number',
												'type' => 'text',
												'class' => 'required Vehicle_Number_Check alphanumeric',
												'placeholder' => $driver_enter_vehicle_number,
                                                'minlength' => '2',
                                                'maxlength' => '20'
								);
								echo form_input($input_data);
							?>
							<p class="error_chk error" id="vehicle_number_exist" style="color:red!important;"></p>
						 </li>						
					  </div>
					  
					  
					  <?php if ($docx_list->num_rows() > 0 && $d_docx > 0) { ?>
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
												
												
												<label for="<?php echo $docx_uniq; ?>" id="<?php echo $docx_uniq; ?>-lbl">&nbsp;</label>
												
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
												
												<p class="image_doc" id="<?php echo $docx_uniq; ?>-pbox"></p>
												
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
																	'value' => $expiryValue,
                                                                    'autocomplete' => 'off'
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
											   
												<label for="<?php echo $docx_uniq; ?>" id="<?php echo $docx_uniq; ?>-lbl">&nbsp;</label>
												
												
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
												
												<p class="image_doc" id="<?php echo $docx_uniq; ?>-pbox"></p>
												
											</li>
											
											 <?php if ($docx->hasExp == 'Yes') { ?>
											<li>
											   <p><?php 
													if($this->lang->line('dash_expiry_date') != '') echo stripslashes($this->lang->line('dash_expiry_date')); else  echo 'Expiry Date';
													?> <span class="req">*</span></p>
													
													
												<?php 
													$input_data = array(
																	'id' => 'expiry-'.$docx_uniq,
																	'name' => 'vehicle-'.url_title($docx->name),
																	'type' => 'text',
																	'class' => 'required',
																	'value' => $expiryValue,
                                                                    'autocomplete' => 'off'
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
								<input type="checkbox" name="multi_car_status" id="multi_car_status" />
							</div>
							<?php } else { ?>
								<input type="hidden" name="multi_car_status" value="OFF" />
							<?php } ?>
							<input type="hidden" name="additional_category" value="<?php echo @implode(',',$additonalCatsId)?>">
							
						 <div class="col docu_sub">
							<li>
								
								<?php 
									$input_data = array(
													'name' => 'temp_driver_id',
													'type' => 'hidden',
													'value' => $this->input->get('q')
									);
									echo form_input($input_data);
								?>
								
							   <input type="button" class='next' onclick="submit_register_form();" value="<?php if($this->lang->line('driver_save_information') != '') echo stripslashes($this->lang->line('driver_save_information')); else  echo 'Save Information'; ?>" id="btnSave">
							</li>
						 </div>
				   </div>
				</div>
			</form>
		</div>
	</div>
</section>
<input type="hidden" id="uploadProgress" value="0" >
<?php  if ($this->lang->line('dash_invalid_file_type') != '')
            $dash_invalid_file_type = stripslashes($this->lang->line('dash_invalid_file_type'));
        else
            $dash_invalid_file_type = 'Invalid file type. Only PDF, JPG, GIF and PNG types are accepted.'; ?>

<script>


	$(document).ready(function () {
		
		$('#btnSave').click(function(){
            if($("#driver_register_form").valid()) {
                
                $("#driver_register_form").submit();
                $(':input[type="submit"]').prop('disabled', true);
                
            }
        
        });
		
       	var catoptions = $("#category").html();
		$("#driver_location").change(function (e) {
			var category_list = $("#driver_location :selected").attr('data-category');
			$("#category").html(catoptions);
			if (category_list == "") {
				return;
			} else {
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

		var options = $("#vehicle_type").html();
		$("#category").change(function (e) {
			var vehicle_types = $("#category :selected").attr('data-vehicle');
			$("#vehicle_type").html(options);
			if (vehicle_types == "") {
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
		$('#vehicle_model option:not(:first)').remove().end();
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
    
    function submit_register_form(){ 
            $('.errDocx').each(function(){
                if($(this).html() != '<img src="images/indicator.gif">') $(this).html('');
            });
            if($('#driver_register_form').valid()){ 
                    // $(".securityCheck").attr("disabled", true);
                if(Number($('#uploadProgress').val()) > 0) {
                    alert('Please wait... '+$('#uploadProgress').val()+' File(s) uploading in progress');
                } else {
                    $('#btnSave').prop('disabled', true);
                    $('#driver_register_form').submit(); 
                }
            } 
     }

 $(document).ready(function () {
 
    $(".Vehicle_Number_Check").blur(function() {
        $('#btnSave').prop('disabled', true);
        var vehicle_number = $(this).val();
        var driver_id = $("#driver_id").val();
        if (vehicle_number != null && vehicle_number != '') {
            $('#vehicle_number_exist').html(checking_number + '....');
            $.ajax({
                type: 'post',
                url: 'site/cms/check_number',
                data: {
                    'vehicle_number': vehicle_number,
                    'driver_id': driver_id
                },
                dataType: 'json',
                success: function(res) {
                    $('#vehicle_number_exist').show();
                    if (res.status == '1') {
                        $('#vehicle_number_exist').css('color','red');
                        $('#vehicle_number_exist').html(res.message);
                        $(".Vehicle_Number_Check").val('');
                        $('#btnSave').prop('disabled', false);
                        return false;
                    } else {
                        $('#vehicle_number_exist').html('');
                        $('#vehicle_number_exist').hide();
                        $('#btnSave').prop('disabled', false);
                    }
                }
            });
        }
    });
	
	//$(".docx").change(function (e) { 
    $(document).on("change",".docx",function(e) { 
		e.preventDefault();
		var docxId = $(this).attr('id');
		var docxType = $(this).attr('data-docx');
		var docxTypeId = $(this).attr('data-docx_id');
		$("#" + docxId + "-Err").html('<img src="images/indicator.gif" />');
        var progCount = Number($('#uploadProgress').val())+1;
		$('#uploadProgress').val(progCount);
		var formData = new FormData($(this).parents('form')[0]);
        $('#btnSave').prop('disabled', true);
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
					
					$("#"+docxId+"-pbox").html('<button onclick="toggle_upload_option(\''+docxId+'\');" type="button"><?php if($this->lang->line('driver_change_document') != '') echo stripslashes($this->lang->line('driver_change_document')); else  echo 'Change Document'; ?></button>');
					$("#"+docxId+"-lbl").hide();
					$("#"+docxId+"-pbox").show();
                    $('#btnSave').prop('disabled', false);
                    $("#" + docxId).next('.error').remove();
				} else {
					$("#" + docxId).val('');
					$("#" + docxId + "-Hid").val('');
					$("#" + docxId + "-Succ").html('');
					$("#" + docxId + "-Err").html(data.err_msg);
                    $("#" + docxId).next('.error').remove();
                    $('#btnSave').prop('disabled', false);
				}
                var progCount = Number($('#uploadProgress').val())-1;
				$('#uploadProgress').val(progCount);
			},
			data: formData,
			cache: false,
			contentType: false,
			processData: false,
			dataType: "json"
		});
		return false;
	});
	 $("#thumbnail").change(function (e) {
            e.preventDefault();
			invalid_file='<?php echo $dash_invalid_file_type; ?>';
            var formData = new FormData($(this).parents('form')[0]);
            $(':input[type="submit"]').prop('disabled', true);
            $.ajax({
                beforeSend: function ()
                {
                    $("#loadedImg").css("display", "block");
                   // document.getElementById("loadedImg").src = 'images/loader64.gif';
                },
                url: 'driver/profile/ajax_valid_image',
                type: 'POST',
                xhr: function () {
                    var myXhr = $.ajaxSettings.xhr();
                    return myXhr;
                },
                success: function (data) {
                    if(data=='Success') {
						$('#profile_image_success').html('');
						$('#profile_image_failure').html('');
                        $(':input[type="submit"]').prop('disabled', false);
					} else {
						$("#thumbnail").val('');
						$('#profile_image_failure').html(invalid_file);
                        $(':input[type="submit"]').prop('disabled', false);
					}
                },
                data: formData,
                cache: false,
                contentType: false,
                processData: false
            });
            return false;
       });
});


function toggle_upload_option(docxId){ 
	$("#"+docxId+"-lbl").show();
	$("#"+docxId+"-pbox").hide();
    $("#"+docxId).val('');
}
$.validator.setDefaults({ ignore: ":hidden:not(select)" });
$('#driver_register_form').validate();
</script>
<style>
.editprofile .inputfile + label {
    padding: 10px !important;
	margin-top: 7px;
}

p.image_doc span {
    width: 45%;
	padding: 3%;
}
p.image_doc button {
	padding: 3%;
}
.driver_prof_img_edit{
	display:none;
}

#multi_car_status {
	width: 30px;
}
#btnSave{
    display: inline-block;
    vertical-align: middle;
    background-color: #e74c3c;
    color: #fff;
    font-size: 17px;
}
</style>
<script>
	$(".chzn-select").chosen();
</script>