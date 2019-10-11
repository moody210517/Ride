<?php 
$this->load->view('site/templates/header');
$ref_code='';
if($this->input->get('ref') != '')
 $ref_code=base64_decode($this->input->get('ref'));
if (is_file('google-login-mats/index.php')){
	require_once 'google-login-mats/index.php';
} 

$fb_login = 'No';
if ($this->config->item('facebook_app_id') != '' && $this->config->item('facebook_app_secret') != '') $fb_login = 'Yes'; 

$google_login = 'No';
if ($this->config->item('google_client_id') != '' && $this->config->item('google_redirect_url') != '' && $this->config->item('google_client_secret') != '') $google_login = 'Yes'; 
?>


<section class="rider-register">
   <div class="container">
      <div class="row">
         <div class="col-md-4 col-lg-4"></div>
         <div class="col-md-4 col-lg-4 register_form_detail">
            <div class="signup_to_ride_contant">
               <h1><?php if ($this->lang->line('rider_signup_signup_to_ride') != '') echo stripslashes($this->lang->line('rider_signup_signup_to_ride')); else echo 'SIGN UP TO RIDE'; ?></h1>
               <p><?php if ($this->lang->line('rider_singup_welcome_to_dectar') != '') echo str_replace('{SITENAME}', $this->config->item('email_title'), stripslashes($this->lang->line('rider_singup_welcome_to_dectar'))); else echo "Welcome to " . $this->config->item('email_title') . ", the easiest way to get around at the tap of a button.";  ?>
               </p>
               <h5><?php if ($this->lang->line('rider_signup_create_your_account') != '') echo stripslashes($this->lang->line('rider_signup_create_your_account')); else echo 'Create your account and get moving in minutes.'; ?></h5>
			   
			   
			   
            </div>
			
			<?php if($fb_login == 'Yes' || $google_login == 'Yes'){ ?>
				<div class="signup_to_ride_contant">
					<h4><?php if ($this->lang->line('signup_with') != '') echo stripslashes($this->lang->line('signup_with')); else echo 'Signup with'; ?></h4>
					
					
					<?php if($fb_login == 'Yes'){ ?>
					  <a href="<?php echo base_url().'facebook/user.php'; ?>">
						 <div class="fb social reg_social">
							<i class="fa fa-facebook-square" aria-hidden="true"></i><span> <?php if ($this->lang->line('signup_with_facebook') != '') echo stripslashes($this->lang->line('signup_with_facebook')); else echo 'Signup with Facebook'; ?></span>
						 </div>
					  </a>
					<?php } ?>
					  
					<?php if($google_login == 'Yes') { ?>
						<a href="<?php echo $authUrl; ?>">
						<div class="gp social reg_social">
							<i class="fa fa-google" aria-hidden="true"></i> <span><?php if ($this->lang->line('signup_with_google') != '') echo stripslashes($this->lang->line('signup_with_google')); else echo 'Signup with Google'; ?></span>
						</div>
					  </a>
					<?php } ?>  
				</div>
			<?php } ?> 
			
			 <?php 
			 $formArr = array('id' => 'rider_register_form','method' => 'post','enctype' => 'multipart/form-data');
			 echo form_open('site/user/register_rider',$formArr);
			 ?>
				<div class="signup_to_ride_form">
				   <div class="col form_head">
					  <h2><?php if ($this->lang->line('rider_profile_account') != '') echo stripslashes($this->lang->line('rider_profile_account')); else echo 'Account'; ?></h2>
					  <p><span>*</span><?php if ($this->lang->line('user_register_required') != '') echo stripslashes($this->lang->line('user_register_required')); else echo 'Required'; ?></p>
				   </div>
				   <div class="col">
					  <p><span>*</span><?php if ($this->lang->line('rider_email') != '') echo stripslashes($this->lang->line('rider_email')); else echo 'EMAIL'; ?></p>
					  
					  <?php 
					 
					  if ($this->lang->line('user_name_placeholder') != '') $placeholder = stripslashes($this->lang->line('user_name_placeholder')); else echo 'name@email.com'; 
						$input_data = array(
										'name' => 'email',
										'type' => 'email',
										'id' => 'email',
										'class' => 'required email',
										'placeholder' => $placeholder,
										'onblur' => 'checkUser_email();'
						);
						echo form_input($input_data);
					  ?>
					  
					  
					  <img id="emailLoader" src="images/indicator.gif" style="display:none;">
					  <span class="error" id="mailErr" style="color: #de5130 !important;"></span>
				   </div>
				   <div class="col">
					  <p><span>*</span><?php if ($this->lang->line('rider_password') != '') echo stripslashes($this->lang->line('rider_password')); else echo 'PASSWORD'; ?></p>
					  
					  
					  <?php 
						if ($this->lang->line('rider_signup_password_placeholder') != '') $placeholder = stripslashes($this->lang->line('rider_signup_password_placeholder')); else $placeholder =  'At least 6 characters ';
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
				   <div class="col">
					  <p><span>*</span><?php if ($this->lang->line('rider_confirm_password') != '') echo stripslashes($this->lang->line('rider_confirm_password')); else echo 'CONFIRM PASSWORD'; ?></p>
					  
					  <?php 
						if ($this->lang->line('rider_signup_password_placeholder') != '') $placeholder = stripslashes($this->lang->line('rider_signup_password_placeholder')); else $placeholder =  'At least 6 characters ';
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
				   <div class="col form_head">
					  <h2><?php if ($this->lang->line('rider_profile_profile') != '') echo stripslashes($this->lang->line('rider_profile_profile')); else echo 'Profile'; ?></h2>
				   </div>
				   <div class="col">
					  <p><span>*</span><?php if ($this->lang->line('rider_signup_name') != '') echo stripslashes($this->lang->line('rider_signup_name')); else echo 'NAME'; ?></p>
					  
					  <?php 
						if ($this->lang->line('rider_signup_name_placeholder') != '') $placeholder = stripslashes($this->lang->line('rider_signup_name_placeholder')); else $placeholder =  'Full Name';
						
						$input_data = array(
										'name' => 'user_name',
										'id' => 'user_name',
										'type' => 'text',
										'class' => 'required onlyalphabets',
										'placeholder' => $placeholder,
                                        'minlength' => '2',
                                        'maxlength' => '30'
						);
						echo form_input($input_data);
					  ?>
					  
				   </div>
				   <div class="col r_phone_num">
					  <p><span>*</span><?php if ($this->lang->line('driver_mobile') != '') echo stripslashes($this->lang->line('driver_mobile')); else echo 'Mobile'; ?></p>
					  
					  
					  <?php //$d_country_cca3
						$drop_options = array();
						foreach ($countryList as $country) {  
							if($country->dial_code != ''){ 
								$optkey = $country->dial_code.'" data-cCode="'.$country->cca3;
								if($d_country_cca3 == $country->cca3) $optkey.= '" selected=selected';
								$drop_options[$optkey]=$country->cca3.' ('.$country->dial_code.')';
							}
						}
						
						$input_data = 'class="country_code required chzn-select" id="country_code" style="width: 37%;"';
						
						echo form_dropdown('dail_code',$drop_options,$d_country_code,$input_data);
						
						if ($this->lang->line('admin_site_earnings_Phone') != '') $placeholder = stripslashes($this->lang->line('admin_site_earnings_Phone')); else $placeholder = 'Phone';
						
						$input_data = array(
										'name' => 'mobile_number',
										'id' => 'mobile_number',
										'type' => 'text',
										'class' => 'p_number required number phoneNumber',
										'placeholder' => $placeholder,
										'style' => 'width: 60%;'
						);
						echo form_input($input_data);
					  ?>
					  
					  
				   </div>
				   <div class="col">
					  <input type="button" value="<?php  if ($this->lang->line('send_otp') != '') echo stripslashes($this->lang->line('send_otp')); else echo 'Send OTP'; ?>" class="otp_btn" id="otp_send_btn" onclick="sendOtp();" />
					  <img src="images/indicator.gif" id="sms_loader">
				   </div>

				   <div class="col">
						<span id="otpSuccess"></span>
						<span id="temp_otp"></span>
						<span id="otpNumErr"></span>
						
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
						?>
						
				   </div>
				   
				   
				   <div class="col" id="otp_container" style="display:none;">
						<p><?php if ($this->lang->line('rider_profile_enter_otp') != '') echo stripslashes($this->lang->line('rider_profile_enter_otp')); else echo 'Enter OTP'; ?> : </p>
						 
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
						 
						<input type="button" class="otp_btn" onclick="verifyOtp();" value="<?php if ($this->lang->line('verify_otp') != '') echo stripslashes($this->lang->line('verify_otp')); else echo 'Verify OTP'; ?>" style="margin-top: 12px;" />	
					</div>
				   
				   <div class="col">
					  <p><?php if ($this->lang->line('rider_signup_referral_code') != '') echo stripslashes($this->lang->line('rider_signup_referral_code')); else echo 'REFERRAL CODE ( Optional )'; ?></p>
					  
					  <?php 
						if ($this->lang->line('rider_signup_referral_code_placeholder') != '') $placeholder = stripslashes($this->lang->line('rider_signup_referral_code_placeholder')); else $placeholder =  'Enter Referral Code If You Have';
						
						$input_data = array(
										'name' => 'referal_code',
										'id' => 'referal_code',
										'type' => 'text',
										'value' =>base64_decode($this->input->get('ref')),
										'placeholder' => $placeholder
						);
						echo form_input($input_data);
					  ?>
					  
					   <p id="invalid_code" class="error invalid_code"></p>
				   </div>
				   <div class="col">
					  <input type="submit" value="<?php if ($this->lang->line('rider_signup_referral_create_account') != '') echo stripslashes($this->lang->line('rider_signup_referral_create_account')); else echo 'Create Account'; ?>" class="acc_creat securityCheck">
				   </div>
				   <div class="col note">
					  <p><?php if ($this->lang->line('rider_signup_please_fill') != '') echo stripslashes($this->lang->line('rider_signup_please_fill')); else echo 'Please fill out all required'; ?> (<span>*</span>) <?php if ($this->lang->line('rider_signup_fields') != '') echo stripslashes($this->lang->line('rider_signup_fields')); else echo 'fields'; ?> .</p>
				   </div>
				   <div class="col term_condition">
					  <p><?php if ($this->lang->line('rider_signup_by_clicking') != '') echo stripslashes($this->lang->line('rider_signup_by_clicking')); else echo 'By clicking “Create Account” , you agree to'; ?> <?php if ($this->lang->line('home_cabilys') != '') $welcome_cabilys = str_replace('{SITENAME}', $this->config->item('email_title'), stripslashes($this->lang->line('home_cabilys'))); else $welcome_cabilys = $this->config->item('email_title') . '\'s'; ?> <?php echo $welcome_cabilys; ?> <br> <a href="pages/terms-and-conditions"> <?php if ($this->lang->line('rider_signup_terms_condition') != '') echo stripslashes($this->lang->line('rider_signup_terms_condition')); else echo 'Terms and Conditions'; ?></a> <?php if ($this->lang->line('rider_signup_and') != '') echo stripslashes($this->lang->line('rider_signup_and')); else echo 'and'; ?> <a href="pages/privacy-and-policy"><?php if ($this->lang->line('rider_signup_privacy_policy') != '') echo stripslashes($this->lang->line('rider_signup_privacy_policy')); else echo 'Privacy Policy.'; ?></a></p>
					  <p><?php if ($this->lang->line('user_already_have_account') != '') echo stripslashes($this->lang->line('user_already_have_account')); else echo 'Already have a rider account'; ?>? <a href="rider/login"> <?php if ($this->lang->line('user_register_login') != '') echo stripslashes($this->lang->line('user_register_login')); else echo 'Log In'; ?></a></p>
				   </div>
				</div>
			</form>
         </div>
         <div class="col-md-4 col-lg-4"></div>
      </div>
   </div>
</section>
<input type="hidden" id="referral_state" value="No" />



  <script>
	
	$(document).ready(function () {
		$("#rider_register_form").validate({
			submitHandler: function(form) {
				otp_phone_number=$("#otp_phone_number").val();
				otp_country_code=$("#otp_country_code").val();
				phone_code = $('#country_code').val();
				otp_phone = $('#mobile_number').val();
				isNumberExists=$("#isNumberExists").val();
				if(isNumberExists=='true'){
					$('#otpSuccess').hide();
					$('#temp_otp').hide();
					$('#otp_container').hide();
					$('#otpNumErr').html('<span style="color:#de5130;"><?php
													if ($this->lang->line('this_mobile_number_exist') != '')
														echo stripslashes($this->lang->line('this_mobile_number_exist'));
													else
														echo 'This mobile number already exist';
													?> !!!</span>');
				} else if(otp_phone_number !=otp_phone || otp_country_code !=phone_code){
					 sendOtp();
				} else{
					$.ajax({
						type: 'POST',
						url: 'site/sms_twilio/check_is_valid_otp_fields',
						dataType: "json",
						data: {"otp_phone":otp_phone_number,"phone_code":otp_country_code},
						success: function (response) {
							if(response=='success'){
								
								
								if($('#referal_code').val() != '' && $('#referral_state').val() == 'No'){ 
									checkReferralCode(); 
								} else {
									$(".securityCheck").attr("disabled", true);
									form.submit();
								}
								
							}else if(response =='exist'){
								$('#otpSuccess').hide();
								$('#temp_otp').hide();
								$('#otp_container').hide();
								$('#otpNumErr').html('<span style="color:#de5130;"><?php
													if ($this->lang->line('this_mobile_number_exist') != '')
														echo stripslashes($this->lang->line('this_mobile_number_exist'));
													else
														echo 'This mobile number already exists';
													?> !!!</span>');
							} else {
								  $('#otpNumErr').html('<span style="color:#de5130;"><?php
													if ($this->lang->line('verify_otp') != '')
														echo stripslashes($this->lang->line('verify_otp'));
													else
														echo 'Verify OTP';
													?> !!!</span>');
							}
						}
					});
				}
			}
		});
			
			
		$("input[name='mobile_number']").blur(function(){
		
		   otp_phone_number=$("#otp_phone_number").val();
		   otp_country_code=$("#otp_country_code").val();
		   phone_code = $('#country_code').val();
		   otp_phone = $('#mobile_number').val();
		
			if(otp_country_code !='' | otp_phone_number !=''){
				if(otp_phone_number!=otp_phone | otp_country_code!=phone_code)
				{
					$("#isNumberExists").val("false");
					$("#otpSuccess,#temp_otp,#otp_container").css('display','none');
					$("#otp_send_btn").val('<?php  if ($this->lang->line('send_otp') != '')
						echo stripslashes($this->lang->line('send_otp'));
					else
						echo 'Send OTP';
					?>');
					$("#mobile_otp").val('');
					$('#otpNumErr').html('');
				}
			}
		});
		
    });
   
    function sendOtp() {
		var phone_code = $('#country_code').val().trim();
        var otp_phone = $('#mobile_number').val().trim();
		if(phone_code!='' && otp_phone!='') {
			if(otp_phone.length <=5 || otp_phone.length >=20 || isNaN(otp_phone)) {
				console.log(isNaN(otp_phone));
				return false;
			}
		}
        if (phone_code == '') {
            $('#otpNumErr').html('<span style="color:#de5130; background: none repeat scroll 0 0 #fff; "><?php
                                            if ($this->lang->line('dash_country_code') != '')
                                                echo stripslashes($this->lang->line('dash_country_code'));
                                            else
                                                echo 'Please enter mobile country code';
                                            ?></span>');
        } else if (otp_phone == '') {
            $('#otpNumErr').html('<span style="color:#de5130; background: none repeat scroll 0 0 #fff; "><?php
                                            if ($this->lang->line('dash_enter_mobile_phone') != '')
                                                echo stripslashes($this->lang->line('dash_enter_mobile_phone'));
                                            else
                                                echo 'Please enter the mobile number';
                                            ?></span>');
        } else {
            $('#otpNumErr').html('');
            $('#sms_loader').css('display', 'inline-block');
            $.ajax({
                type: 'POST',
                url: 'site/sms_twilio/send_otp',
                dataType: "json",
                data: {"otp_phone": otp_phone, "phone_code": phone_code},
                success: function (response) {
					$("#otp_phone_number").val(otp_phone);
					$("#otp_country_code").val(phone_code);
                    if (response.status != '0') {
						$("#isNumberExists").val("false");
						if(response.status == '2'){
							$("#isNumberExists").val("true");
							$('#otpNumErr').html('<span class="error"><?php
												if ($this->lang->line('this_mobile_number_exist') != '')
													echo stripslashes($this->lang->line('this_mobile_number_exist'));
												else
													echo 'This mobile number already exist';
												?> !!!</span>');
						} else {
							$('#otp_container').css('display','block');
							$('#otp_send_btn').val('<?php
												if ($this->lang->line('dash_resend_otp') != '')
													echo stripslashes($this->lang->line('dash_resend_otp'));
												else
													echo 'Resend OTP';
												?>');
							$('#otpSuccess').html('<p  style="background: none repeat scroll 0 0 #fff;  color:green;"><?php
												if ($this->lang->line('dash_otp_sent_your_number') != '')
													echo stripslashes($this->lang->line('dash_otp_sent_your_number'));
												else
													echo 'OTP has been sent to your phone number';
												?></p>').css('display','block');;
							if (response.mode == 'sandbox') {
								$("#temp_otp").html('<p  style=" "><?php
												if ($this->lang->line('dash_otp_demo_response_msg') != '')
													echo stripslashes($this->lang->line('dash_otp_demo_response_msg'));
												else
													echo 'OTP is in demo mode, so please use this otp <b>';
												?> ' + response.otp + '</b></p>').css('display','block');;
							}
						}
                    } else {
                        $('#otpNumErr').html('<span style="color:#de5130; background: none repeat scroll 0 0 #fff; "><?php
                                            if ($this->lang->line('dash_otp_failed_response_msg') != '')
                                                echo stripslashes($this->lang->line('dash_otp_failed_response_msg'));
                                            else
                                                echo 'OTP failed to send, please try again';
                                            ?>.</span>');
                    }
                    $('#sms_loader').css('display', 'none');
                }
            });
        }
    }
    function reset_otp() {
        //$('#country_code').val('');
        $('#mobile_number').val('');
        $('#mobile_otp').val('');
        $('#otpSuccess').html('');
        $("#otp_send_btn").val('<?php  if ($this->lang->line('send_otp') != '')
                                    echo stripslashes($this->lang->line('send_otp'));
                                else
                                    echo 'Send OTP';
                                ?>');
        $("#otp_send_btn").attr("onclick", "sendOtp()");
    }

    function verifyOtp() {
		var otp_phone_number=$("#otp_phone_number").val();
		var otp_country_code=$("#otp_country_code").val();
        var phone_code = $('#country_code').val();
        var otp_phone = $('#mobile_number').val();
        $('#sms_loader').css('display', 'none');
		if (phone_code == '') {
			$('#otpNumErr').html('<span style="color:#de5130; background: none repeat scroll 0 0 #fff; "><?php
											if ($this->lang->line('dash_country_code') != '')
												echo stripslashes($this->lang->line('dash_country_code'));
											else
												echo 'Please enter mobile country code';
											?></span>');
			return false;
		} else if (otp_phone == '') {
			$('#otpNumErr').html('<span style="color:#de5130; background: none repeat scroll 0 0 #fff; "><?php
											if ($this->lang->line('dash_enter_mobile_phone') != '')
												echo stripslashes($this->lang->line('dash_enter_mobile_phone'));
											else
												echo 'Please enter the mobile number';
											?></span>');
			return false;
		} else if ($("#mobile_otp").val() == '') {
			$("#mobile_otp").css('border-color', '#de5130');
			return false;
		} else {
			var mobile_otp = $("#mobile_otp").val();
			$('#sms_loader').css('display', 'block');
			$.ajax({
				type: 'POST',
				url: 'site/sms_twilio/otp_verification',
				data: {"otp": mobile_otp,"otp_phone":otp_phone_number,"phone_code":otp_country_code},
				dataType:'json',
				success: function (response) {
					if (response.status == '1') { 
						$('#otpNumErr').html('');
						$('#temp_otp').html('');
						$('#otpSuccess').html('<?php
											if ($this->lang->line('dash_otp_verified_successfully') != '')
												echo stripslashes($this->lang->line('dash_otp_verified_successfully'));
											else
												echo 'OTP has been verified successfully';
											?>.').css('display','block');
						
						$('#mobile_otp').css('border-color','green');
						$('#otpSuccess').css('color','green');
						$('#otp_container').css('display','none');
						$("#otp_send_btn").val('<?php
											if ($this->lang->line('change_number') != '')
												echo stripslashes($this->lang->line('change_number'));
											else
												echo 'Change Number';
											?>');
						$("#otp_send_btn").attr("onclick", "reset_otp()");
					  
					} else {
						$('#otpNumErr').html('<span style="color:#de5130;"><?php
											if ($this->lang->line('dash_entered_wrong_otp') != '')
												echo stripslashes($this->lang->line('dash_entered_wrong_otp'));
											else
												echo 'You have entered wrong OTP';
											?></span>');
					}
				}
			});
			$('#sms_loader').css('display', 'none');
		}
    }
	
	function checkReferralCode(){
		
		referal_code = $('#referal_code').val();
		$('#invalid_code').show();
		$('#invalid_code').html('<img src="images/indicator.gif" />');
		if(referal_code != ''){
			$.ajax({
					type: 'POST',
					url: 'site/user/check_referral_code',
					data: {"referal_code":referal_code},
					success: function (response) {
						if(response=='error'){
							
							$('#invalid_code').html('<?php  if ($this->lang->line('driver_invalid_referral') != '')
								echo stripslashes($this->lang->line('driver_invalid_referral'));
							else
								echo 'Sorry,You have applied Invalid referral code';
							?>');
							$('#referal_code').val('');
							setTimeout(function(){ $('#invalid_code').hide(); }, 5000);
							return false;
						} else {
							$('#invalid_code').html('');
							$('#referral_state').val('Yes');
							$(".securityCheck").attr("disabled", true);
							$('#rider_register_form').submit();
						}
					}
			});
		} 
	}
</script>


<style>
	.chosen-container-single .chosen-single {
		background: none;
		border: 1px solid #e5e5e5;
		border-radius: 0;
		box-shadow: none;
		color: #444;
		display: block;
		height: 53px;
		line-height: 54px;
		overflow: hidden;
		padding: 0 0 0 4px;
		position: relative;
		text-decoration: none;
		white-space: nowrap;
	}
</style>

<?php 
$this->load->view('site/templates/footer');
?>
