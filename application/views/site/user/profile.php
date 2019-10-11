<?php
$this->load->view('site/templates/profile_header');
$profilePic = base_url() . USER_PROFILE_IMAGE_DEFAULT;
$hasPhoto = FALSE;
if (isset($rider_info->row()->image) && $rider_info->row()->image != '') {
    $profilePic = base_url() . USER_PROFILE_THUMB . $rider_info->row()->image;
    $hasPhoto = TRUE;
}
?>



<section class="profile_pic_sec row">
   <div  class="profile_login_cont">
      
	  <!-------Profile side bar ---->
		<?php
		$this->load->view('site/templates/profile_sidebar');
		?>
	  
      <div class="share_detail">
         <div class="share_det_title">
            <h2><?php if ($this->lang->line('user_dash_cabs_profile') != '') echo stripslashes($this->lang->line('user_dash_cabs_profile')); else echo 'PROFILE'; ?></h2>
         </div>
         <div class="profile_ac_inner_det">
            <div class="profile_ac_details profile_ac_title">
               <h2><?php if ($this->lang->line('rider_profile_account') != '') echo stripslashes($this->lang->line('rider_profile_account')); else echo 'Account'; ?></h2>
            </div>
            <div class="profile_ac_form">
				<?php 
				$formArr = array('id' => 'profile_update_form','method' => 'post','enctype' => 'multipart/form-data');
				echo form_open('site/rider/update_rider_profile',$formArr);
				
				?>
                  <div class="col-lg-6 col-md-6 no_padding left">
                     <label><span class="req">* </span><?php if ($this->lang->line('rider_email') != '') echo stripslashes($this->lang->line('rider_email')); else echo 'EMAIL'; ?></label>
                     <div class="input_div rider_profile_name">
						
						
					<?php 
						$input_data = array(
										'type' => 'text',
										'value' => $rider_info->row()->email,
										'readonly' => 'readonly'
						);
						echo form_input($input_data);
					?>
						
						
						
					</div>
                     <label><span class="req">* </span><?php if ($this->lang->line('dash_user_profile_mobile_number_cabs') != '') echo stripslashes($this->lang->line('dash_user_profile_mobile_number_cabs')); else echo 'MOBILE NUMBER'; ?> </label> 
                     <div class="input_div selc">
					 
					 
						<?php 
							$drop_options = array();
							foreach ($countryList as $country) {  
								if($country->dial_code != ''){ 
									$optkey = $country->dial_code.'" data-cCode="'.$country->cca3; 
									if($rider_info->row()->country_code == $country->dial_code) $optkey.= '" selected=selected';
									$drop_options[$optkey]= $country->dial_code;
								}
							}
							
							$input_data = 'class="required" id="country_code_exist" disabled';
							echo form_dropdown('country_code',$drop_options,$rider_info->row()->country_code,$input_data);
							if ($this->lang->line('admin_site_earnings_Phone') != '') $placeholder = stripslashes($this->lang->line('admin_site_earnings_Phone')); else $placeholder = 'Phone';
							
							$input_data = array(
											'name' => 'mNumber',
											'type' => 'text',
											'class' => 'required phoneNumber',
											'placeholder' => $placeholder,
											'readonly' => 'readonly alphanumeric',
											'value' => $rider_info->row()->phone_number
							);
							echo form_input($input_data);
						?>
						
						<a href="" data-toggle="modal" data-target="#edit_mobileNo_popup"><img src="images/site/edit_img.png" class="profile_mobile_edit_img"></a>
                     </div>
					 
                     <label><span class="req">* </span><?php if ($this->lang->line('rider_signup_name') != '') echo stripslashes($this->lang->line('rider_signup_name')); else echo 'NAME'; ?></label>
                     <div class="input_div">
						
						
						<?php 
							if ($this->lang->line('operator_name') != '') $placeholder = stripslashes($this->lang->line('operator_name')); else $placeholder =  'Name';
							$input_data = array(
											'name' => 'user_name',
											'type' => 'text',
											'id' => 'user_name',
											'class' => 'required onlyalphabets',
											'placeholder' => $placeholder,
											'value' => $rider_info->row()->user_name,
											'minlength' => '2',
											'maxlength' => '30'
							);
							echo form_input($input_data);
						?>
						
					</div>
					 
                     
                  </div>
                 
                  <div class="profile_ac_details profile_pic_title">
                     <h2><?php if ($this->lang->line('dash_user_profile_photo') != '') echo stripslashes($this->lang->line('dash_user_profile_photo')); else echo 'Profile Photo'; ?></h2>
                  </div>
                  <div class="col-lg-6 col-md-6">
                     <span class="profile_pic_cont" id="image-holder">
						<img src="<?php echo $profilePic; ?>" id="profile_thumb_img" />
					</span>
                    
                    <button class="wh_btn prv_btn" type="button" onclick="change_profile_photo();"> <?php 
                    if($hasPhoto){
                        if ($this->lang->line('dash_user_change_photo') != '') echo stripslashes($this->lang->line('dash_user_change_photo')); else echo 'Change Photo'; 
                    } else {
                        if ($this->lang->line('rider_add_photo') != '') echo stripslashes($this->lang->line('rider_add_photo')); else echo 'Add Photo';
                    }
                    ?> </button>
					
					 <p id="choosen_file_err" class="error"></p>
                  </div>
				  
                  <div class="col-lg-6 col-md-6 ">
                     <div class="profile_upload">
					 
						<?php 
							$input_data = array(
											'name' => 'image',
											'id' => 'image',
											'type' => 'file',
											'class' => 'inputfile inputfile-1 media_image'
							);
							echo form_input($input_data);
						?>
					 
						<button type="button" class="rd_btn add_money_btn profile_sbmt_btn"><?php if ($this->lang->line('dash_user_update_profile') != '') echo stripslashes($this->lang->line('dash_user_update_profile')); else echo 'Update Profile'; ?> </button>
                     </div>
                  </div>
               </form>
            </div>
         </div>
      </div>
   </div>
</section>


<div class="modal fade"  id="edit_mobileNo_popup">
	<div class="modal-dialog" role="document">
		<div class="modal-content">
			<div class="modal-header">
				<h5 class="modal-title"><?php if ($this->lang->line('dash_change_mobile_number') != '') echo stripslashes($this->lang->line('dash_change_mobile_number')); else echo 'Change Mobile Number'; ?></h5>
				<button type="button" class="close" data-dismiss="modal" aria-label="Close">
				  <span aria-hidden="true">&times;</span>
				</button>
			</div>
			<?php 
					$formArr = array('id' => 'change_mobile_form','method' => 'post','enctype' => 'multipart/form-data');
					echo form_open('site/rider/update_rider_profile',$formArr);
				?>
				<div class="modal-body">
					
					<div class="col r_phone_num change_phone_popup rider_login_cont">
					  
					  
					  <?php 
						$drop_options = array();
						foreach ($countryList as $country) {  
							if($country->dial_code != ''){ 
								$optkey = $country->dial_code.'" data-cCode="'.$country->cca3; 
								if($rider_info->row()->country_code == $country->dial_code) $optkey.= '" selected=selected';
								$drop_options[$optkey]=$country->cca3.' ('.$country->dial_code.')';
							}
						}
						
						$input_data = 'class="country_code required chzn-selects" id="country_code" style="width: 25%;"';
						
						echo form_dropdown('country_code',$drop_options,$rider_info->row()->country_code,$input_data);
						
						
						$input_data = array(
										'name' => 'mNumber',
										'type' => 'text',
										'class' => 'p_number required number phoneNumber mNumber',
										'placeholder' => '777-777-7777',
										'style' => 'width: 45%;',
										'id' => 'mobile_number'
						);
						echo form_input($input_data);
					?>
					  <input type="button" value="<?php if ($this->lang->line('rider_profile_send_otp') != '') echo stripslashes($this->lang->line('rider_profile_send_otp')); else echo 'Send OTP'; ?>" class="login otp_btn" id="otp_send_btn" onclick="sendOtp();" />
					  <img src="images/indicator.gif" id="sms_loader" />
						
						<div class="otp_verify_box" id="otpblock" style="display:none">
							
							
							<?php 
							if ($this->lang->line('rider_profile_enter_otp') != '') $placeholder = stripslashes($this->lang->line('rider_profile_enter_otp')); else $placeholder =  'Enter OTP';
							
							$input_data = array(
											'name' => 'otpNumber',
											'id' => 'otpNumber',
											'type' => 'text',
											'class' => 'otpNumber',
											'placeholder' => $placeholder,
											'style' => 'width: 33%;'
							);
							echo form_input($input_data);
						 ?>
							
							
							<a href="javascript:void(0)" id="otp_send_btn" class="login otp_btn" onclick="verifyOtp()"><?php if ($this->lang->line('verify_otp') != '') echo stripslashes($this->lang->line('verify_otp')); else echo 'Verify OTP'; ?></a>
						</div>
						
						<p id="otpSuccess" style="color:green; margin-top: 3%;" class="otp-note"></p>
						<p id="otpNumErr"  class="error otp-note"></p> 
						<p id="temp_otp" class="otp-note"></p>
						
						
						<?php 
						
							$input_data = array(
											'name' => 'changed_number',
											'id' => 'changed_mobile_number',
											'type' => 'hidden'
							);
							echo form_input($input_data);
							
							$input_data = array(
											'name' => 'countryCodeIfAlreadyExists',
											'type' => 'hidden',
											'value' => $rider_info->row()->country_code
							);
							echo form_input($input_data);
							
							$input_data = array(
											'name' => 'mobileNumberIfAlreadyExists',
											'type' => 'hidden',
											'value' => $rider_info->row()->phone_number
							);
							echo form_input($input_data);
							
							$input_data = array(
											'name' => 'isMobileNumberChanged',
											'type' => 'hidden'
							);
							echo form_input($input_data);
							
							$input_data = array(
											'name' => 'isEditing',
											'id' => 'isEditing',
											'type' => 'hidden'
							);
							echo form_input($input_data);
							
							$input_data = array(
											'name' => 'user_name',
											'type' => 'hidden',
											'value' => $rider_info->row()->user_name
							);
							echo form_input($input_data);
						?>
							
						<input type="hidden" id="riderId" value=" <?php echo $rider_info->row()->_id; ?>">
						<input type="hidden" name="otpVerified" id='otpVerified' value='false' />
						<input type="hidden" id="otp_mode" value="<?php echo $this->config->item('twilio_account_type'); ?>"/>
						<input type='hidden' id="otp_sent_status"/>
				   </div>
					
				</div>
				<div class="modal-footer">
					<button type="button" class="btn btn-primary change_mob_popup_submit"><?php if ($this->lang->line('admin_settings_submit') != '') echo stripslashes($this->lang->line('admin_settings_submit')); else echo 'Submit'; ?></button>
					<button type="button" class="btn btn-secondary" data-dismiss="modal"><?php if ($this->lang->line('admin_subadmin_close') != '') echo stripslashes($this->lang->line('admin_subadmin_close')); else echo 'Close'; ?></button>
				</div>
			</form>
		</div>
	</div>
</div>



<script>
	
	
    function sendOtp(otp_number) {
        var phone_code = $('#country_code').val().trim();
        var otp_phone = $('.mNumber').val().trim();
        var rider_id = $("#riderId").val();
				var otpVerified = $("#otpVerified").val();
		$("#otpNumber").val(''); 
		if(phone_code!='' && otp_phone!='') {
			if(otp_phone.length <=5 || otp_phone.length >=20 || isNaN(otp_phone)) {
				return false;
			}
		}
        $('#otpblock').css('display','block');
        
		if(otpVerified == 'true'){
			 $('#otpNumErr').html('OTP already verified successfully');
			 return false;
		}
		
        if (phone_code == '') {
            $('#otpNumErr').html('<p  class="error" style="background: none repeat scroll 0 0 #fff; margin: 10px 0 0;"><?php if ($this->lang->line('rider_profile_enter_mobile_code_number') != '') echo stripslashes($this->lang->line('rider_profile_enter_mobile_code_number')); else echo 'Please enter mobile code number'; ?></p>');
        } else if (otp_phone == '') {
            $('#otpNumErr').html('<p  class="error" style="background: none repeat scroll 0 0 #fff; margin: 10px 0 0;"><?php if ($this->lang->line('rider_profile_enter_phone_number') != '') echo stripslashes($this->lang->line('rider_profile_enter_phone_number')); else echo 'Please enter phone number'; ?></p>');
        } else {
			$('.otp_verify_box').show();
            $('#otpNumErr').html('');
			$('#otpSuccess').html('');  
			$('.otpNumber').css('border-color','#e8e8e8');  
            $('#sms_loader').css('display', 'inline-block');
            $.ajax({
                type: 'POST',
                url: 'site/sms_twilio/send_otp',
                dataType: "json",
                data: {"otp_phone": otp_phone, "phone_code": phone_code, "riderId": rider_id},
                success: function (response) {
					
                    if (response.status != '0') {
                       if(response.status == '2'){
							$("#isNumberExists").val("true");
							$('#otpblock').css('display','none');
							$('#otpNumErr').html('<p class="error"><?php
												if ($this->lang->line('this_mobile_number_exist') != '')
													echo stripslashes($this->lang->line('this_mobile_number_exist'));
												else
													echo 'This mobile number already exist';
												?> !!!</p>');
						}else if ($('#otp_mode').val() == 'sandbox') {
							$('#otp_sent_status').val('Yes');
							$('#otp_send_btn').text('<?php if ($this->lang->line('rider_profile_resend_otp') != '') echo stripslashes($this->lang->line('rider_profile_resend_otp')); else echo 'Resend OTP'; ?>');
							$("#temp_otp").html('<p  style=" margin: 10px 0 0;"><?php if ($this->lang->line('rider_profile_otp_is_in_demo_mode') != '') echo stripslashes($this->lang->line('rider_profile_otp_is_in_demo_mode')); else echo 'OTP is in demo mode, only the registed mobile number will receive OTP code, For other number use this'; ?>'+' ' + response.otp + '</p>');
							$(".enter_otp").css("display", 'block');
                        }else{
							$('#otp_sent_status').val('Yes');
							 $('#otpSuccess').html('<p  style="background: none repeat scroll 0 0 #fff; margin: 10px 0 0; color:green;"><?php if ($this->lang->line('rider_profile_otp_sent') != '') echo stripslashes($this->lang->line('rider_profile_otp_sent')); else echo 'OTP has been sent to your phone number'; ?></p>');
							  $(".enter_otp").css("display", 'block');
						}
                     
                    } else {
                        $('#otpNumErr').html('<p  class="error" style="background: none repeat scroll 0 0 #fff; margin: 10px 0 0;"><?php if ($this->lang->line('rider_profile_otp_failed') != '') echo stripslashes($this->lang->line('rider_profile_otp_failed')); else echo 'OTP failed to send, please try again.'; ?></p>');
                    }
                    $('#sms_loader').css('display', 'none');
                }
            });
        }

    }

    function verifyOtp() {

        var phone_code = $('#country_code').val();
        var otp_phone = $('.mNumber').val(); 
        var rider_id = $("#riderId").val();
        var phone_number = $('.mNumber').val();
        $('#sms_loader').css('display', 'none');
        if (phone_code == '') {
            $('#otpNumErr').html('<p  class="error" style="background: none repeat scroll 0 0 #fff; margin: 10px 0 0;"><?php if ($this->lang->line('rider_profile_enter_mobile_code_number') != '') echo stripslashes($this->lang->line('rider_profile_enter_mobile_code_number')); else echo 'Please enter mobile code number'; ?></p>');
            return false;
        } else if (otp_phone == '') {
            $('#otpNumErr').html('<p  class="error" style="background: none repeat scroll 0 0 #fff; margin: 10px 0 0;"><?php if ($this->lang->line('rider_profile_enter_phone_number') != '') echo stripslashes($this->lang->line('rider_profile_enter_phone_number')); else echo 'Please enter phone number'; ?></p>');
            return false;
        } else if ($("#otpNumber").val() == '') {
            $("#otpNumber").css('border-color', 'red');
			 $('#otpNumErr').html('');
            return false;
        } else {
            var otpNumber = $("#otpNumber").val();
            $('#sms_loader').css('display', 'block');
            $.ajax({
                type: 'POST',
                url: 'site/sms_twilio/otp_verification',
                data: {"otp": otpNumber, "riderId": rider_id},
                dataType: "json",
                success: function (response) {
                
                    if (response.status == '1') {
						 $("#isEditing").val('false')
                        $('#otpSuccess').html('<?php if ($this->lang->line('rider_profile_otp_verified') != '') echo stripslashes($this->lang->line('rider_profile_otp_verified')); else echo 'OTP has been verified successfully.'; ?>');
                        // $("#mobile_form").submit();
                        $("#otpVerified").val('true');
                        $('#otpNumErr,#firstVerify,#temp_otp').html("");
                        $("#changed_mobile_number").val(phone_number);
                        $("#otpblock").css('display','none');
						//$("#otp_send_btn").hide();
						$("input[name='isMobileNumberChanged']").val('changed');
                    } else {
                        $('#otpSuccess').html("");
                        $('#otpNumErr').html("<?php if ($this->lang->line('dash_entered_wrong_otp') != '') echo stripslashes($this->lang->line('dash_entered_wrong_otp')); else echo 'You have entered wrong OTP'; ?>");
                        $("#otpVerified").val('false');
						$('#otpNumErr').css('display', 'block');
                    }
                }
            });
            $('#sms_loader').css('display', 'none');
        }
    }
</script>
<script>
    $(document).ready(function () {
		
        $('.mNumber').keyup(function(e){
          if (/\D/g.test(this.value)) this.value = this.value.replace(/\D/g, '');
        });
        
		$("#change_mobile_form").validate();
		$("#profile_update_form").validate();
		
		$('.profile_sbmt_btn').click(function(){
			$chkentry = 0;
			$errchk = 0;
			$trimChk = 0;
			$('.pass').each(function(){
				var p = $(this).val();
				if(p != ''){
					if(p.trim() == ''){
						$(this).val('');
						$trimChk++;
					} else {
						$chkentry++;
					}
				} 
			});   
			if($trimChk > 0){
				alert('<?php if ($this->lang->line('site_user_password_space_err_alert') != '') echo stripslashes($this->lang->line('site_user_password_space_err_alert')); else echo 'Password strings are not valid. Password will not be updated.'; ?>');
				return false;
			}
			
			if($chkentry > 0){
				$('.pass').each(function(){ 
					if($(this).val() == '') {
						$errchk++;
						$(this).closest( "div" ).css('border-color','#de5130');
					} else {
						$(this).closest( "div" ).css('border-color','#e8e8e8');
					}
				});
				if($errchk == 0){
					if($("#profile_update_form").valid()){
						$('#profile_update_form').submit();
					}
				}
			} else {
				$('#profile_update_form').submit();
			}
		});
	  
		
		$('#mobile_number').keypress(function(){
			$('#otpblock').css('display','none');
			$('#otpVerified').val('false');
			$('#otp_sent_status').val('No');
			$('#temp_otp').html('');
		});
	
       
        $(".change_mob_popup_submit").click(function () {
			var mobile_number = $('#mobile_number').val(); 
			var otp_sent_status = $('#otp_sent_status').val();
            $otp_verified = $("#otpVerified").val();
			if(mobile_number == ''){
				sendOtp();
			} else  if ($otp_verified != 'true') {  
				if(otp_sent_status == 'Yes'){
					verifyOtp();
				} else {
					sendOtp();
				}
            } else { 
				$("#change_mobile_form").submit();
			}
		});
		
	    $(".media_image").change(function (e) { 
            e.preventDefault();
            if (typeof (FileReader) != "undefined") {
                var image_holder = $("#image-holder");
				if(typeof($(this)[0].files[0]) == 'undefined') {
					//image_holder.empty();
					$('.profile_pic_cont').html('<img src="<?php echo $profilePic; ?>" id="thumb-image" style="width:100px;height:100px;">');
					return false;
				}
                var reader = new FileReader();
                reader.onload = function (e) {
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
                                    "style": "width:100px;height:100px;",
                                }).appendTo(image_holder);
                                $('#choosen_file_err').html('');

                            } else {
								$('.media_image').val('');
                                $('#choosen_file_err').html("<?php if ($this->lang->line('user_upload_image_too_small') != '') echo stripslashes($this->lang->line('user_upload_image_too_small')); else echo 'Upload Image Too Small. Please Upload Image Size More than or Equalto 75 X 42 .'; ?>");
                            }
                        };
                    }  else {
						$('.media_image').val('');
                        $('#choosen_file_err').html("<?php if ($this->lang->line('user_please_select_an_image') != '') echo stripslashes($this->lang->line('user_please_select_an_image')); else echo 'Please Select an Image file'; ?>");
                    }
                }
                image_holder.show();
                reader.readAsDataURL($(this)[0].files[0]);
            }
        });
		
		
    });
	
	
</script>
<style>
select.country_code {
    color: #000;
}
.change_phone_popup .login {
    margin-left: 0;
    margin-top: 3%;
}
h2 {
	text-transform: uppercase!important;
}
</style>
<?php
$this->load->view('site/templates/footer');
?> 