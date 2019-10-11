<?php 
$this->load->view('driver/templates/profile_header.php');
?>


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
	.otpbtns{
		text-transform: uppercase;
		top:0 !important;
	}
	
	.chosen-drop {
		width: 130px !important;
	}
	.active-result {
		width: 120px !important;
		float: left !important;
	}
	.chosen-search-input {
		width: 120px !important;
	}
	
	/* .chosen-container-single .chosen-single {
		height: 45px !important;
		line-height: 45px !important;
		border-right:none;
	}
	
	#country_code_chosen {
		width: 24% !important;
	} */
	
	
	
</style>


<section class="profile_pic_sec row">
   <div  class="profile_login_cont">
   
		<!--------------  Load Profile Side Bar ------------------------>
		<?php    
			$this->load->view('driver/templates/profile_sidebar'); 
		?>
   
   <div class="share_detail mobileno_change">
   <div class="share_det_title">
      <h2><?php echo $heading; ?></span></h2>
   </div>
   <div class="profile_ac_inner_det">
      <div class="inner_full editprofile">
         <p class="form_sub_title"><?php if ($this->lang->line('driver_mobile_number_information') != '') echo stripslashes($this->lang->line('driver_mobile_number_information'));else echo 'Mobile Number Information';
             ?></p>
         
			<?php 
				$attributes = array('class' => 'form_container left_label', 'id' => 'change_mobile_form');
				echo form_open('driver/profile/change_mobile',$attributes) 
			?>
			
			<div class="col">
			   <li>
				  <p><?php if ($this->lang->line('driver_current_mobile_number') != '') echo stripslashes($this->lang->line('driver_current_mobile_number'));else echo 'Current Mobile Number'; ?></p>
			 
				<?php 
					$dail_code = '';
					$mobile_number = '';
					if(isset($driverData->row()->dail_code)) $dail_code = $driverData->row()->dail_code; 
					if(isset($driverData->row()->mobile_number)) $mobile_number = $driverData->row()->mobile_number; 
					$input_data = array(
									'type' => 'text',
									'class' => 'required',
									'value' => $dail_code.$mobile_number,
									'disabled' => 'disabled'
					);
					echo form_input($input_data);
				?>
			 
			   </li>
			   <li><p>&nbsp;</p></li>
			</div>
			<div class="col">
			   <li>
				  <p><?php if ($this->lang->line('driver_new_mobile_number') != '') echo stripslashes($this->lang->line('driver_new_mobile_number'));else echo 'New Mobile Number';
             ?></p>
				  <div class="input_div selc">
					
					
					<?php 
						$drop_options = array();
						foreach ($countryList as $country) {  
							if($country->dial_code != ''){ 
								$optkey = $country->dial_code.'" data-cCode="'.$country->cca3; 
								if($dail_code == $country->dial_code) $optkey.= '" selected=selected';
								$drop_options[$optkey]=$country->cca3.' ('.$country->dial_code.')';
							}
						}
						
						$input_data = 'class="required chzn-select dail_code" id="country_code" style="width: 37%;"';
						
						echo form_dropdown('dail_code',$drop_options,$dail_code,$input_data);
						
						
						if ($this->lang->line('driver_new_mobile_number') != '') $placeholder = stripslashes($this->lang->line('driver_new_mobile_number')); else $placeholder = 'New Mobile Number';
						
						$input_data = array(
										'name' => 'mobile_number',
										'id' => 'mobile_number',
										'type' => 'text',
										'class' => 'required phoneNumber',
										'placeholder' => $placeholder
						);
						echo form_input($input_data);
					?>
					
					 <input type="button"  onclick="sendOtp();" id="otp_send_btn" class="otpbtns"  value="<?php if ($this->lang->line('dash_send_otp') != '')  echo stripslashes($this->lang->line('dash_send_otp')); else echo 'Send OTP'; ?>"  />
				  </div>
			   </li>
			   <li id="otp_container" style="display:none;">
			   <p>
				  <?php if ($this->lang->line('driver_confirm_otp') != '') echo stripslashes($this->lang->line('driver_confirm_otp'));else echo 'Confirm OTP';
					?>
				</p>
					<div class="input_div selc">
					
						<?php 
							if ($this->lang->line('dash_enter_otp') != '') $placeholder = stripslashes($this->lang->line('dash_enter_otp')); else $placeholder =  'Enter OTP';
							
							$input_data = array(
											'name' => 'mobile_otp',
											'id' => 'mobile_otp',
											'type' => 'text',
											'placeholder' => $placeholder,
											'style' => 'width: 100% !important;'
							);
							echo form_input($input_data);
						 ?>
					
						<input type="button"  onclick="verifyOtp();" class="otpbtns" value="<?php if ($this->lang->line('verify_otp') != '') echo stripslashes($this->lang->line('verify_otp'));else echo 'Verify OTP';
					?>"  />
				   </div>
			   </li>
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
			
			<div class="col secondary_form_btn acc_info">
			   <li>
				  <input type="submit" value="<?php if ($this->lang->line('driver_save_information') != '') echo stripslashes($this->lang->line('driver_save_information'));else echo 'Save Information';
					?>">
			   </li>
			   <li>
				  <p>&nbsp;</p>
			   </li>
			</div>
			
			<img src="images/indicator.gif" style="display:none;" id="sms_loader">
		 </form>
      </div>
   </div>
</section>


<script>
	
	$(document).ready(function () {
		$("#change_mobile_form").validate({
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
					$('#otpNumErr').html('<span style="color:#de5130;"><?php if ($this->lang->line('this_mobile_number_exist') != '') echo stripslashes($this->lang->line('this_mobile_number_exist')); else echo 'This mobile number already exist'; ?> !!!</span>');
				}else if(otp_phone_number !=otp_phone || otp_country_code !=phone_code){
					 sendOtp();
				}else{
					$.ajax({
						type: 'POST',
						url: 'driver/sms_twilio/check_is_valid_otp_fields',
						dataType: "json",
						data: {"otp_phone":otp_phone_number,"phone_code":otp_country_code},
						success: function (response) {
							if(response=='success'){
								form.submit();
							}else if(response =='exist'){
								$('#otpNumErr').html('<span style="color:#de5130;"><?php if ($this->lang->line('this_mobile_number_exist') != '') echo stripslashes($this->lang->line('this_mobile_number_exist')); else echo 'This mobile number already exist'; ?> !!!</span>');
							} else{
								  $('#otpNumErr').html('<span style="color:#de5130;"><?php if ($this->lang->line('verify_otp') != '') echo stripslashes($this->lang->line('verify_otp')); else echo 'Verify OTP'; ?> !!!</span>');
							}
						}
					});
				}
			}
		});
        
        $('#mobile_number').keyup(function(e){
          if (/\D/g.test(this.value)) this.value = this.value.replace(/\D/g, '');
        });
        
    });
   
    function sendOtp() {  
		var phone_code = $('#country_code').val().trim();
        var otp_phone = $('#mobile_number').val().trim();
        
       
		if(phone_code!='' && otp_phone!='') {
			if(otp_phone.length <=5 || otp_phone.length >=20 || isNaN($('#mobile_number').val())) {
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
							$('#otp_container').hide();
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
		var  otp_country_code=$("#otp_country_code").val();
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
				url: 'driver/sms_twilio/otp_verification',
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
											if ($this->lang->line('driver_otp_change') != '')
												echo stripslashes($this->lang->line('driver_otp_change'));
											else
												echo 'Change';
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
</script>


<?php
$this->load->view('driver/templates/footer.php');
?>