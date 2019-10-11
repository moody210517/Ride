<?php
$this->load->view('site/templates/common_header');
?>

<style>
    section.ddriver_reg_banner {
        background-image: none;
    }
</style>

<section class="ddriver_reg_banner">
	<div class="driver_reg_form">
		<div class="container">
			<div class="row">
				
				<div class="col-md-6 col-lg-6">
					<div class="driver_register_form">
						<h1 class="form_head"><?php
                            if ($this->lang->line('driver_sign_up_to_drive') != '')
                                echo stripslashes($this->lang->line('driver_sign_up_to_drive'));
                            else
                                echo 'SIGN UP TO DRIVE';
                            ?></h1>
						
						
						
						<?php 
						$formArr = array('id' => 'driver_register_form','class' => 'msform');
						echo form_open('site/app_driver/driver_register_demo',$formArr);
						?>
						
							<!-- fieldsets -->
							<fieldset id="first_set">
								<div class="f_row">
								   <div class="d_fullname">
										<?php 
											if ($this->lang->line('rider_signup_name_placeholder') != '') $placeholder = stripslashes($this->lang->line('rider_signup_name_placeholder')); else $placeholder =  'Full Name';
											
											$input_data = array(
															'name' => 'driver_name',
															'id' => 'driver_name',
															'type' => 'text',
															'class' => 'required onlyalphabets',
															'placeholder' => $placeholder,
                                                            'minlength' => '2',
                                                            'maxlength' => '30'
											);
											echo form_input($input_data);
										?>
								   
								   </div>
								</div>
								<div class="f_row">
								   <div class="d_email">
										
										<?php 
											
											if ($this->lang->line('cms_email') != '') $placeholder = stripslashes($this->lang->line('cms_email')); else $placeholder = 'Email';
											
											$input_data = array(
															'name' => 'email',
															'type' => 'email',
															'id' => 'email',
															'class' => 'required email',
															'placeholder' => $placeholder
											);
											echo form_input($input_data);
										?>
									  <span id="email_exist" class="error"></span>
								   </div>
								</div>
								<div class="f_row init_box_mob" style="width: 35%; float: left; display: inline-block;">
								
									<?php 
										$drop_options = array();
										foreach ($countryList as $country) {  
											if($country->dial_code != ''){ 
												$optkey = $country->dial_code.'" data-cCode="'.$country->cca3; 
												if($d_country_cca3 == $country->dial_cca3) $optkey.= '" selected=selected';
												$drop_options[$optkey]=$country->cca3.' ('.$country->dial_code.')';
											}
										}
										
										$input_data = 'class="required chzn-select" id="init_box_country_code" style="height: 43px"';
										
										echo form_dropdown('',$drop_options,$d_country_code,$input_data);
										
									?>
								</div>
								<div class="f_row init_box_mob" style="width: 65%; float: left; display: inline-block;">
									<div class="d_phone_num">
										<?php 
											
											if ($this->lang->line('admin_site_earnings_Phone') != '') $placeholder = stripslashes($this->lang->line('admin_site_earnings_Phone')); else $placeholder = 'Phone';
											
											$input_data = array(
															'id' => 'init_box_mobile_number',
															'name' => 'init_box_mobile_number',
															'type' => 'tel',
															'class' => 'required number phoneNumber',
															'placeholder' => $placeholder,
															'style' => 'width:100%',
											);
											echo form_input($input_data);
										?>
									</div>
								</div>
								<span id="init_box_otpNumErr"></span>
								<div class="f_row">
								   <div class="d_password">
								   
										<?php 
											if ($this->lang->line('driver_signup_password_placeholder') != '') $placeholder = stripslashes($this->lang->line('driver_signup_password_placeholder')); else $placeholder =  'Password (At least 6 characters)';
											$input_data = array(
															'name' => 'password',
															'type' => 'password',
															'id' => 'password',
															'class' => 'required',
															'placeholder' => $placeholder,
															'minlength' => '6'
											);
											echo form_input($input_data);
										?>
								   </div>
								</div>
								<div class="f_row">
								   <div class="d_password">
									    
										<?php 
											if ($this->lang->line('driver_signup_confirm_password_placeholder') != '') $placeholder = stripslashes($this->lang->line('driver_signup_confirm_password_placeholder')); else $placeholder =  'Confirm Password (At least 6 characters)';
											$input_data = array(
															'name' => 'confirm_password',
															'id' => 'confirm_password',
															'type' => 'password',
															'class' => 'required',
															'placeholder' => $placeholder,
															'minlength' => '6',
															'equalTo' => '#password'
											);
											echo form_input($input_data);
										?>
									  
								   </div>
								</div>
								
								<input type="button" name="next" class="next action-button" value="<?php if ($this->lang->line('site_data_table_next_ucfirst') != '') echo stripslashes($this->lang->line('site_data_table_next_ucfirst')); else echo 'Next';?>" />
								<img src="images/indicator.gif" id="init_box_sms_loader" style="display:none;">
							</fieldset>
						
							<fieldset id="second_set">
							
								
								
								<div class="f_row" style="display:none" id="mobile_container">
									<div class="">
									
										<?php 
											$drop_options = array();
											foreach ($countryList as $country) {  
												if($country->dial_code != ''){ 
													$optkey = $country->dial_code.'" data-cCode="'.$country->cca3; 
													if($d_country_code == $country->dial_code) $optkey.= '" selected=selected';
													$drop_options[$optkey]=$country->cca3.' ('.$country->dial_code.')';
												}
											}
											
											$input_data = 'class="required chzn-selects" id="country_code"';
											
											echo form_dropdown('dail_code',$drop_options,$d_country_code,$input_data);
											
											
											if ($this->lang->line('admin_site_earnings_Phone') != '') $placeholder = stripslashes($this->lang->line('admin_site_earnings_Phone')); else $placeholder = 'Phone';
											
											$input_data = array(
															'name' => 'mobile_number',
															'id' => 'mobile_number',
															'type' => 'tel',
															'class' => 'required number phoneNumber',
															'placeholder' => $placeholder
											);
											echo form_input($input_data);
									    ?>
									</div>
								</div>
								
								<div class="f_row">
									<input type="button" id="change_phone" class="otpbtns" value="<?php if ($this->lang->line('driver_otp_change_number') != '') echo stripslashes($this->lang->line('driver_otp_change_number')); else echo 'Change Number'; ?>" onclick="change_mobile_number();">
								</div>
									
								<div class="f_row">	
									<input type="button" id="otp_send_btn" class="otpbtns dr_otp_btn" data-otptype="RO" value="Resend OTP" onclick="sendOtp();">
								</div>
								
								
								<div class="f_row">
									
									<?php 
										if ($this->lang->line('rider_profile_enter_otp') != '') $placeholder = stripslashes($this->lang->line('rider_profile_enter_otp')); else $placeholder =  'Enter OTP';
										
										$input_data = array(
														'name' => 'mobile_otp',
														'id' => 'mobile_otp',
														'type' => 'text',
														'class' => 'required',
														'placeholder' => $placeholder
										);
										echo form_input($input_data);
									?>
								</div>
								
								<div class="f_row">
								    <p id="otpSuccess" class="otpSuccess"></p>
									<p id="temp_otp"></p>
									<p id="otpNumErr" class="error"></p>
									<p id="formErr" class="error"></p>
									
									
									<?php 
									$input_data = array(
													'id' => 'otp_mode',
													'type' => 'hidden',
													'value' => $this->config->item('twilio_account_type')
									);
									echo form_input($input_data);
									
									$input_data = array(
													'id' => 'otp_phone_number',
													'type' => 'hidden'
									);
									echo form_input($input_data);
									
									$input_data = array(
													'id' => 'otp_country_code',
													'type' => 'hidden'
									);
									echo form_input($input_data);
									
									$input_data = array(
													'id' => 'isNumberExists',
													'type' => 'hidden'
									);
									echo form_input($input_data);
									
									$input_data = array(
													'id' => 'otp_verify_status',
													'type' => 'hidden',
													'value' => 'No'
									);
									echo form_input($input_data);
								?>
									
									
									<img src="images/indicator.gif" id="sms_loader" style="display:none;">
								</div>
								
								<div class="f_row"></div>
								
								
								<input type="button" class="otpbtns previous action-button" value="<?php if ($this->lang->line('verify_otp') != '') echo stripslashes($this->lang->line('verify_otp')); else echo 'Verify OTP'; ?>" onclick="verifyOtp();" id="verify_btn" style="display:none;">
								
								<input type="button" class="submit action-button securityCheck" value="<?php if ($this->lang->line('verify_otp') != '') echo stripslashes($this->lang->line('verify_otp')); else echo 'Verify OTP'; ?>" onclick="submit_register_form();" />
							</fieldset>
						</form>
					</div>
				</div>
			</div>
		</div>
	</div>
</section>



<script type="text/javascript" src="js/site/jquery.easing.min.js"></script>

<script>
	$(document).ready(function(){
	
		
		$(".next").click(function(){ 
			if($('#driver_register_form').valid()){
				if($('#email_exist').html() == ''){
					sendOtp('init_box_');
				}
			} 
		});
		
		$('#driver_location').change(function(){ 
			var locId = $('#driver_location').find(':selected').attr('data-catId'); 
            $('#catBox').hide('slow');
			if(locId != '' && locId != 'undefined'){
				$.ajax({
				    type: "POST",
				    url: 'site/app_driver/available_categories',
				    data: {'locId':locId},
				    dataType: 'json',
				    success: function(res) {
						if(res.status == '1'){ 
                            $('#catBox').show('slow');
							$('#category').html(res.message);
						} else {
							alert(res.message);
						}
					}
				});
			}
		});
		
		$('#email').focusout(function(e){ 		
			e.preventDefault();
			var email = $(this).val();			
			if(email != ''){
				var email_check = isEmail(email);
				if(email_check){
					$.ajax({
						type: "POST",
						url: 'site/app_driver/ajax_check_driver_email_exist',
						data: {'email':email},
						dataType: 'json',
						success: function(res) {
							if(res.status == '1'){
								$('#email_exist').html('');								
							}else{
								$('#email_exist').html(res.response);
							}
						}
					});
				}
			}	
		});
		
		
		
		$('#mobile_number').keypress(function(){
			change_mobile_number();
		});
		
	});
	
	function isEmail(email) {
		var regex = /^([a-zA-Z0-9_.+-])+\@(([a-zA-Z0-9-])+\.)+([a-zA-Z0-9]{2,4})+$/;
		return regex.test(email);
	}
	function change_mobile_number(){
		$('#mobile_otp').val('');	
		$('#otp_verify_status').val('No');	
		$('#mobile_container, #otp_send_btn').show();
		$('#resend_box, #change_phone, #resend_box, #change_phone, #otpSuccess, #temp_otp, #mobile_otp').hide();
		$('#verify_btn').prop( "disabled", true );	
		$('#otp_send_btn').val('<?php  if ($this->lang->line('send_otp') != '')
			echo stripslashes($this->lang->line('send_otp'));
		else
			echo 'Send OTP';
		?>');
        $('#otp_send_btn').attr('data-otptype','SO');
        $('.securityCheck').val('<?php if ($this->lang->line('verify_otp') != '') echo stripslashes($this->lang->line('verify_otp')); else echo 'Verify OTP'; ?>');
        $('.securityCheck').hide();
	}
	
	function submit_register_form(){
		$('#formErr').html('').show();
		
        var otpState = $('#otp_send_btn').attr('data-otptype');
        if(otpState == 'SO'){
            $('#formErr').html('<?php  if ($this->lang->line('driver_otp_verification_process_pending') != '') echo stripslashes($this->lang->line('driver_otp_verification_process_pending')); else echo 'OTP verification process is pending'; ?>');
        }
        
		if($('#driver_register_form').valid()){
            verifyOtp();
            var otp_verify_status = $('#otp_verify_status').val();
			if(otp_verify_status == 'Yes'){ 
				$('#driver_register_form').submit(); 
			} else {
				//$('#formErr').html('<?php  if ($this->lang->line('driver_otp_verification_process_pending') != '') echo stripslashes($this->lang->line('driver_otp_verification_process_pending')); else echo 'OTP verification process is pending'; ?>');
				setTimeout(function() {
					$('#formErr').hide();
				}, 2500);
			}
		}
	}
</script>



<script>
	
	
   
    function sendOtp(e) {
        if(e == null || e == 'undefined') e = '';
		var phone_code = $('#'+e+'country_code').val();
        var otp_phone = $('#'+e+'mobile_number').val();
		var rtn = 0;
        
        var otp_send_type = $('#otp_send_btn').attr('data-otptype'); 
		if(e == '' && otp_send_type == 'SO'){
            if(!$('#driver_register_form').valid()){
                return false;
            }
		}
		
		
		if (phone_code == '') {
			if(e != 'init_box_'){
				$('#'+e+'otpNumErr').html('<?php
				if ($this->lang->line('dash_country_code') != '')
					echo stripslashes($this->lang->line('dash_country_code'));
				else
					echo 'Please enter mobile country code';
				?>');
			}
			rtn++;
		} else if (otp_phone == '') {
			if(e != 'init_box_'){
				$('#'+e+'otpNumErr').html('<?php
				if ($this->lang->line('dash_enter_mobile_phone') != '')
					echo stripslashes($this->lang->line('dash_enter_mobile_phone'));
				else
					echo 'Please enter the mobile number';
				?>');
			}
			rtn++;
		}
		
		if(Number(rtn) > 0){
			return false;
		}
		$('#'+e+'otpNumErr').html('');
		$('#'+e+'sms_loader').css('display', 'inline-block');
		$.ajax({
			type: 'POST',
			url: 'driver/sms_twilio/send_otp',
			dataType: "json",
			data: {"otp_phone": otp_phone, "phone_code": phone_code},
			success: function (response) {
				$("#otp_phone_number").val(otp_phone);
				$("#otp_country_code").val(phone_code);
				if (response.status != '0') {
					$("#isNumberExists").val("false");
					if(response.status == '2'){
						$('#otpSuccess').hide();
						$('#temp_otp').hide();
						$("#isNumberExists").val("true");
						$('#'+e+'otpNumErr').html('<span class="error"><?php
						if ($this->lang->line('this_mobile_number_exist') != '')
							echo stripslashes($this->lang->line('this_mobile_number_exist'));
						else
							echo 'This mobile number already exist';
						?> !!!');
					} else {
						$('#otp_send_btn').val('<?php
						if ($this->lang->line('dash_resend_otp') != '')
							echo stripslashes($this->lang->line('dash_resend_otp'));
						else
							echo 'Resend OTP';
						?>');
                        $('#otp_send_btn').attr('data-otptype','RO');
						$('#otpSuccess').html('<?php
						if ($this->lang->line('dash_otp_sent_your_number') != '')
							echo stripslashes($this->lang->line('dash_otp_sent_your_number'));
						else
							echo 'OTP has been sent to your phone number';
						?>').show();
						if (response.mode == 'sandbox') {
                            $('#mobile_otp').val(response.otp);
							$("#temp_otp").html('<?php
							if ($this->lang->line('dash_otp_demo_response_msg') != '')
								echo stripslashes($this->lang->line('dash_otp_demo_response_msg'));
							else
								echo 'OTP is in demo mode, so please use this otp <b>';
							?> ' + response.otp + '</b>').show();
						}
						
						
						//  move to next stage----------------------------
						if(e == 'init_box_'){
							var current_fs, next_fs, previous_fs;
							var left, opacity, scale; 
							var animating; 
							if(animating) return false;
							animating = true;
							current_fs = $('#first_set');
							next_fs = $('#second_set');
							
							next_fs.show(); 
							current_fs.animate({opacity: 0}, {
								step: function(now, mx) {
									scale = 1 - (1 - now) * 0.2;
									left = (now * 50)+"%";
									opacity = 1 - now;
									current_fs.css({'transform': 'scale('+scale+')'});
									next_fs.css({'left': left, 'opacity': opacity});
								}, 
								duration: 800, 
								complete: function(){
									current_fs.hide();
									animating = false;
								}, 
								easing: 'easeInOutBack'
							});
							$('#mobile_number').val(otp_phone);
							$('#country_code').val(phone_code);
							var changetxt = $('#change_phone').val() +' ('+phone_code+otp_phone+')';
							$('#change_phone').val(changetxt);
							$('#reg_sub_title').html('<?php  if ($this->lang->line('driver_otp_verification_sec') != '') echo stripslashes($this->lang->line('driver_otp_verification_sec')); else echo 'Verify your mobile number'; ?>');
						}
						//------------------------------------------------
						$('#mobile_otp').show();
						$('#otp_verify_status').val('No');	
						$('#verify_btn').prop( "disabled", false );	
						$('.securityCheck').show();
					}
				} else {
					$('#'+e+'otpNumErr').html('<?php
					if ($this->lang->line('dash_otp_failed_response_msg') != '')
						echo stripslashes($this->lang->line('dash_otp_failed_response_msg'));
					else
						echo 'OTP failed to send, please try again';
					?>.');
				}
				$('#'+e+'sms_loader').css('display', 'none');
			}
		});
    }
    
    
    function verifyOtp(e) {
        if(e == null || e == 'undefined') e = '';
		var otp_phone_number=$("#otp_phone_number").val();
		var  otp_country_code=$("#otp_country_code").val();
        var phone_code = $('#country_code').val();
        var otp_phone = $('#mobile_number').val();
        $('#'+e+'sms_loader').hide();
		if (phone_code == '') {
			$('#'+e+'otpNumErr').html('<?php
			if ($this->lang->line('dash_country_code') != '')
				echo stripslashes($this->lang->line('dash_country_code'));
			else
				echo 'Please enter mobile country code';
			?>');
			return false;
		} else if (otp_phone == '') {
			$('#'+e+'otpNumErr').html('<?php
			if ($this->lang->line('dash_enter_mobile_phone') != '')
				echo stripslashes($this->lang->line('dash_enter_mobile_phone'));
			else
				echo 'Please enter the mobile number';
			?>');
			return false;
		} else if ($("#mobile_otp").val() == '') {
			$("#mobile_otp").css('border-color', '#de5130');
			return false;
		} else {
			var mobile_otp = $("#mobile_otp").val();
			$('#'+e+'sms_loader').show();
			$.ajax({
				type: 'POST',
				url: 'driver/sms_twilio/otp_verification',
				data: {"otp": mobile_otp,"otp_phone":otp_phone_number,"phone_code":otp_country_code},
				dataType:'json',
				success: function (response) {
					if (response.status == '1') { 
						$('#'+e+'otpNumErr').html('');
						$('#temp_otp').html('');
						$('#otpSuccess').html('<?php
							if ($this->lang->line('dash_otp_verified_successfully') != '')
								echo stripslashes($this->lang->line('dash_otp_verified_successfully'));
							else
								echo 'OTP has been verified successfully';
							?>').show();
						
						
						var changetxt = '<?php if ($this->lang->line('driver_otp_change_number') != '') echo stripslashes($this->lang->line('driver_otp_change_number')); else echo 'Change Number'; ?> ('+otp_country_code+otp_phone_number+')';
						$('#change_phone').val(changetxt);
						
						$('#mobile_otp').css('border-color','#e7e7e7');
						$('#otp_send_btn, #mobile_otp, #mobile_container').hide();
						$('#change_phone').show();
						$('#verify_btn').prop( "disabled", true );
						$('#otp_verify_status').val('Yes');	
                        $('.securityCheck').val('<?php if ($this->lang->line('dash_submit') != '') echo stripslashes($this->lang->line('dash_submit')); else echo 'Submit'; ?>');
						
					  
					} else {
						$('#'+e+'otpNumErr').html('<?php
							if ($this->lang->line('dash_entered_wrong_otp') != '')
								echo stripslashes($this->lang->line('dash_entered_wrong_otp'));
							else
								echo 'You have entered wrong OTP';
							?>');
					}
					$('#'+e+'sms_loader').hide();
				}
			});
		}
    }
</script>

<script>
	$(".chzn-select").chosen();
</script>

