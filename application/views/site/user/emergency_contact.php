<?php
    $this->load->view('site/templates/profile_header');
?> 

<?php 
    $findpage = $this->uri->segment(2);

    $contact_details = array();

    if (isset($rider_info->row()->emergency_contact)) {
        if (isset($rider_info->row()->emergency_contact['em_email'])) {
            $contact_details = $rider_info->row()->emergency_contact;
        }
    }

    $mobile_verification = 'No';

    if ($this->lang->line('emergency_email_and_mobile_verify') != ''){
        $notVerified = stripslashes($this->lang->line('emergency_email_and_mobile_verify'));
    }else{
        $notVerified = "Mobile and Email";
    }
    if (isset($contact_details['verification']['mobile'])) {
        if ($contact_details['verification']['mobile'] == 'Yes') {
            $mobile_verification = 'Yes';
            if ($this->lang->line('emergency_email_verify') != ''){
                $notVerified = stripslashes($this->lang->line('emergency_email_verify'));
            }else{
                $notVerified = "Email";
            }
        }
    }

    $email_verification = 'No';
    if (isset($contact_details['verification']['email'])) {
        if ($contact_details['verification']['email'] == 'Yes') {
            $email_verification = 'Yes';
            if ($this->lang->line('emergency_mobile_verify') != ''){
                $notVerified = stripslashes($this->lang->line('emergency_mobile_verify'));
            }else{
                $notVerified = "Mobile";
            }
        }
    }
?> 

<style>
.chosen-container-single .chosen-single {
    background: none;;
    border: none;
    border-radius: 0;
    box-shadow: none;
	
	background-image: url("images/site/dropdown.png");
    background-position: 90% center;
    background-repeat: no-repeat;
	
}
#em_mobile_code_chosen {
	width: 120px !important;
}
#em_mobile {
	border-left: 1px solid #e8e8e8;
}
</style>

<section class="profile_pic_sec row">
    <div  class="profile_login_cont emergency_contact_loc">
        <?php $this->load->view('site/templates/profile_sidebar'); ?>
        <div class="share_detail">
            <div class="share_det_title">
                <h2><?php if ($this->lang->line('user_emergency_contact') != '') echo stripslashes($this->lang->line('user_emergency_contact')); else echo 'EMERGENCY CONTACT!'; ?></h2>
            </div>
            <div class="profile_ac_inner_det">
                <div class="rate_title">
                    <h2><?php if ($this->lang->line('site_user_enter_your_emergency_contact') != '') echo stripslashes($this->lang->line('site_user_enter_your_emergency_contact')); else echo 'Enter your emergency contact'; ?></h2> 
                    <?php if(!empty($contact_details)){ ?>
                        <a href="site/rider/delete_emergency_contact"  class=" btn1 sign_in_driver"><button><?php if ($this->lang->line('rides_remove_contact') != '') echo stripslashes($this->lang->line('rides_remove_contact')); else echo 'REMOVE CONTACT'; ?></button></a>
                    <?php } ?>
                </div>
                <div class="profile_ac_form">
                    <form action="site/rider/update_emergency_contact" name="em_contact_form" id="em_contact_form" method="POST">
                        <div class="col-lg-6 col-md-6 no_padding left">
                            <label><span class="req">*</span> <?php if ($this->lang->line('rider_signup_name') != '') echo stripslashes($this->lang->line('rider_signup_name')); else echo 'NAME'; ?></label>
                            <div class="input_div">
							
							
							<?php 
								if ($this->lang->line('user_enter_name') != '') $placeholder = stripslashes($this->lang->line('user_enter_name')); else $placeholder =  'Enter Name';
								
								$em_name = ''; 
								if (isset($contact_details['em_name'])) $em_name = $contact_details['em_name'];
								
								$input_data = array(
												'name' => 'em_name',
												'id' => 'em_name',
												'type' => 'text',
												'class' => 'required em_inputs alphanumeric',
												'placeholder' => $placeholder,
												'value' => $em_name
								);
								echo form_input($input_data);
							?>
							
							</div>
                            <label><span class="req">*</span> <?php if ($this->lang->line('dash_user_profile_mobile_number_cabs') != '') echo stripslashes($this->lang->line('dash_user_profile_mobile_number_cabs')); else echo 'MOBILE NUMBER'; ?></label>
                            <div class="input_div selc">
							
								<?php 
								if (isset($contact_details['em_mobile_code'])) {
									$chekDailCode = $contact_details['em_mobile_code'];
								} else {
									$chekDailCode = $rider_info->row()->country_code;
								}
								$drop_options = array();
								foreach ($countryList as $country) {  
									if($country->dial_code != '' && $country->cca3 !='' ){ 
										$drop_options[$country->cca3.$country->dial_code]= $country->cca3.$country->dial_code;
									}
								}
								
								$input_data = 'class="required chzn-select" id="em_mobile_code"';
								
								echo form_dropdown('em_mobile_code',$drop_options,$chekDailCode,$input_data);
								
								
								$em_mobile = '';
								if (isset($contact_details['em_mobile'])) $em_mobile = $contact_details['em_mobile'];
								
								if ($this->lang->line('rider_profile_mobile_number') != '') $placeholder = stripslashes($this->lang->line('rider_profile_mobile_number')); else $placeholder = 'Mobile Number';
								
								$input_data = array(
												'name' => 'em_mobile',
												'id' => 'em_mobile',
												'type' => 'text',
												'class' => 'required number phoneNumber',
												'placeholder' => $placeholder,
												'value' => $em_mobile
								);
								echo form_input($input_data);
							?>
							
                              
                            </div>
                        </div>
                        <div class="col-lg-6 col-md-6 no_padding right">
                            <label><span class="req">*</span> <?php if ($this->lang->line('rider_email') != '') echo stripslashes($this->lang->line('rider_email')); else echo 'EMAIL'; ?></label>
                            <div class="input_div">
                                
								<?php 
								
									if ($this->lang->line('rider_email_address') != '') $placeholder = stripslashes($this->lang->line('rider_email_address')); else $placeholder = 'Email Address';
									
									$em_email = '';
									if(isset($contact_details['em_email'])) $em_email = $contact_details['em_email'];
									
									$input_data = array(
													'name' => 'em_email',
													'type' => 'email',
													'id' => 'em_email',
													'class' => 'required em_inputs',
													'placeholder' => $placeholder,
													'value' => $em_email
									);
									echo form_input($input_data);
								?>
								
                              
                            </div>
                            <button class="rd_btn Emr_btn"><?php if ($this->lang->line('rides_update_contact') != '') echo stripslashes($this->lang->line('rides_update_contact')); else echo 'UPDATE CONTACT'; ?></button>
                            <!--  -->
                        </div>
                    </form>
                </div>
				<?php if (isset($contact_details['em_email']) && $contact_details['em_email'] != '' && isset($contact_details['em_mobile']) && $contact_details['em_mobile'] != ''){ ?>
					<div class="emr_span">
						<p style="text-transform:uppercase;"><?php if ($this->lang->line('site_user_email_verification_status') != '') echo stripslashes($this->lang->line('site_user_email_verification_status')); else echo 'Email Verification Status'; ?> <?php if ($email_verification == 'Yes') { ?>
						<a title="<?php if ($this->lang->line('site_user_email_id_verified') != '') echo stripslashes($this->lang->line('site_user_email_id_verified')); else echo 'Email Id Verified'; ?>" class="c-active">&emsp;</a>
						<?php } else { ?>
						<a title="<?php if ($this->lang->line('site_user_email_id_still_not_verified') != '') echo stripslashes($this->lang->line('site_user_email_id_still_not_verified')); else echo 'Email Id Still Not Verified'; ?>" class="c-inactive">&emsp;</a>
						<?php }?>
						</p>
						<p style="text-transform:uppercase;"><?php if ($this->lang->line('site_user_mobile_verification_status') != '') echo stripslashes($this->lang->line('site_user_mobile_verification_status')); else echo 'Mobile Verification Status'; ?> <?php if ($mobile_verification == 'Yes') { ?>
						<a title="<?php if ($this->lang->line('site_user_mobile_number_verified') != '') echo stripslashes($this->lang->line('site_user_mobile_number_verified')); else echo 'Mobile Number Verified'; ?>" class="c-active">&emsp;</a>
						<?php } else { ?>
						<a title="<?php if ($this->lang->line('site_user_mobile_number_still_not_verified') != '') echo stripslashes($this->lang->line('site_user_mobile_number_still_not_verified')); else echo 'Mobile Number Still Not Verified'; ?>" class="c-inactive">&emsp;</a>
						<?php } ?>
						</p>
					</div>
				<?php } ?>
            </div>
        </div>
    </div>
</section>


<script src="js/site/jquery.confirm.js"></script>

<script>
    $(document).ready(function () {
        $("#em_contact_form").validate();
    });


    $("#alert_confirmation").click(function () {
        $.confirm({
            text: "<?php if ($this->lang->line('user_are_you_sure_send_emergency') != '') echo stripslashes($this->lang->line('user_are_you_sure_send_emergency')); else echo 'Are you sure do you want to send emergency alert to this person?'; ?>",
            confirm: function () {
                //alert("You just confirmed.");
                window.location.href = "rider/emergency-alert";
            },
            cancel: function () {
                //alert("You cancelled.");
            }
        });
    });

</script>

<?php
$this->load->view('site/templates/footer');
?> 