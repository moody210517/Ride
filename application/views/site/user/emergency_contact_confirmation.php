<?php
$this->load->view('site/templates/header');
?>  


<section class="rider_login_sec row">
   <div class="rider_login_cont">
      <div class="col-lg-12 col-md-12 col-sm-12 col-xs-12 no_padding">
			<div class="em-confirm-container" >
				<div class="col-lg-12 driver_detail_center text-center">
					<div class="driver_form_start">
						<h2 > <?php if ($this->lang->line('user_emergency_contact_confirmation') != '') echo stripslashes($this->lang->line('user_emergency_contact_confirmation')); else echo 'Emergency Contact Confirmation'; ?></h2>
						<p class="vsuccess"><?php if ($this->lang->line('user_your_email_verification_success') != '') echo stripslashes($this->lang->line('user_your_email_verification_success')); else echo 'Your Email Verification Success !'; ?></p>
						<h4><?php if ($this->lang->line('user_confirm_your_mobile') != '') echo stripslashes($this->lang->line('user_confirm_your_mobile')); else echo 'Please Confirm Your Mobile Number'; ?></h4>
					</div>
					<?php 
					$formArr = array('id' => 'confirm_emergency_contact_form','method' => 'post');
					echo form_open('site/user/confirm_emergency_contact',$formArr);
					?>
					<div class="col-lg-12 nopadd driver_sign_up_form text-center">
						<div class="v-innerBox">
							<label><?php if ($this->lang->line('user_enter_your_mobile_otp') != '') echo stripslashes($this->lang->line('user_enter_your_mobile_otp')); else echo 'Enter your mobile OTP'; ?></label>
							<input type="text" name="em_mobile_otp" id="em_mobile_otp" placeholder="<?php if ($this->lang->line('user_enter_mobile_otp') != '') echo stripslashes($this->lang->line('user_enter_mobile_otp')); else echo 'enter mobile otp'; ?>" /> <br/>
							<input type="hidden" name="user_id" value="<?php echo $user_details->row()->_id; ?>" /> 
							<span id="otpErrMsg" style="color:red;"></span></br>
							<?php
							if ($this->config->item('twilio_account_type') == 'sandbox') {
								?>
								<span style="color:grey;"><?php if ($this->lang->line('rider_profile_otp_is_in_demo_mode') != '') echo stripslashes($this->lang->line('rider_profile_otp_is_in_demo_mode')); else echo 'OTP is in demo mode, only the registered mobile number will receive OTP code, For other number use this';
									?><b> <?php echo $otp_number; ?> </b></span></br>
								<?php
							}
							?>
							<span class="v-btn">
								<input type="button" value="<?php if ($this->lang->line('cms_submit') != '') echo stripslashes($this->lang->line('cms_submit')); else echo 'SUBMIT'; ?>" onclick="otpValidations();" class="blue-btn" />
							</span>
						</div>
					</div>
				</form>
				</div>
			</div>
		</div>
   </div>
</section>


<script>
<?php if ($this->lang->line('user_emergency_enter_received_otp') != '') $otpErrMsg = stripslashes($this->lang->line('user_emergency_enter_received_otp')); else $otpErrMsg = 'Please enter the otp you have received'; ?>
function otpValidations(){ 
	var em_mobile_otp = $('#em_mobile_otp').val();
	if(em_mobile_otp != ''){
		$('#confirm_emergency_contact_form').submit();
	} else {
		$('#otpErrMsg').html('<?php echo $otpErrMsg ?>.');
	}
}
</script>
<?php
$this->load->view('site/templates/footer');
?> 