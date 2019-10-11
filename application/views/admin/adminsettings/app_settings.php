<?php
$this->load->view(ADMIN_ENC_URL.'/templates/header.php'); 
?>
<style>

div#tab4 ul li:nth-child(5) label, div#tab4 ul li:nth-child(7) label, div#tab4 ul li:nth-child(9) label, div#tab4 ul li:nth-child(11) label {
    display: block;
}


</style>	

<div id="content" class="base-app-top">
    <div class="grid_container">
        <div class="grid_12">
            <div class="widget_wrap">
                <div class="widget_wrap tabby">
                    <div class="widget_top"> 
                        <span class="h_icon list"></span>
                        <h6><?php echo $heading; ?></h6>
                    </div>
                    <div class="widget_content">
                        <?php
                        $attributes = array('class' => 'form_container left_label', 'id' => 'app_settings_form','enctype' => 'multipart/form-data');
                        echo form_open(ADMIN_ENC_URL.'/adminlogin/admin_global_settings', $attributes)
                        ?>
                        <input type="hidden" name="form_mode" value="app"/>
                        <div id="tab4" class="base-appsec">
                            <ul class="left-contsec-app">

                                <li>
                                    <h3 class="head_social"><?php if ($this->lang->line('admin_settings_website_app_mode') != '') echo stripslashes($this->lang->line('admin_settings_website_app_mode')); else echo 'Website & App Mode'; ?></h3>
                                </li>
								
									<li>
										<div class="form_grid_12">
											<label class="field_title"><?php if ($this->lang->line('admin_common_status') != '') echo stripslashes($this->lang->line('admin_common_status')); else echo 'Status'; ?></label>
											<div class="form_input">
												<div class="prod_dev">
													<input type="checkbox"  name="site_mode"  id="prod_dev" class="prod_dev" <?php if (isset($admin_settings->row()->site_mode)){if ($admin_settings->row()->site_mode == 'production'){ echo 'checked="checked"'; }}  ?> />
												</div>
											</div>
										</div>
									</li>

                                <li>
                                    <h3 class="head_social"><?php if ($this->lang->line('admin_settings_payment_billing') != '') echo stripslashes($this->lang->line('admin_settings_payment_billing')); else echo 'Payment/ Billing'; ?></h3>
                                </li>

                                <li>
                                    <div class="form_grid_12">
                                        <label class="field_title"><?php if ($this->lang->line('admin_settings_billing_cycle') != '') echo stripslashes($this->lang->line('admin_settings_billing_cycle')); else echo 'Billing cycle'; ?> <span class="req">*</span></label>
                                        <div class="form_input">
                                            <input name="billing_cycle" id="billing_cycle" type="text" value="<?php if (isset($admin_settings->row()->billing_cycle)) echo htmlentities($admin_settings->row()->billing_cycle); ?>"  class="large tipTop required number positiveNumber currencyT minfloatingNumber" title="<?php if ($this->lang->line('admin_setting_no_days_billing') != '') echo stripslashes($this->lang->line('admin_setting_no_days_billing')); else echo 'No of days for Billing'; ?>"/>
                                            <?php if ($this->lang->line('admin_settings_days') != '') echo stripslashes($this->lang->line('admin_settings_days')); else echo 'days'; ?>
                                        </div>
                                    </div>
                                </li>

                                <li>
                                    <h3 class="head_social"><?php if ($this->lang->line('admin_settings_invite_and_earnings') != '') echo stripslashes($this->lang->line('admin_settings_invite_and_earnings')); else echo 'Invite and Earnings'; ?></h3>
                                </li>

                                <li class="curr-base-sec">
                                    <div class="form_grid_12">
                                        <label class="field_title"><?php if ($this->lang->line('admin_settings_welcome_amount') != '') echo stripslashes($this->lang->line('admin_settings_welcome_amount')); else echo 'Welcome Amount'; ?> <span class="req">*</span></label>
                                        <div class="form_input">
                                            <span class="curr-symbol"><?php echo $dcurrencySymbol . ' '; ?></span><input name="welcome_amount" id="welcome_amount" type="text" value="<?php if (isset($admin_settings->row()->welcome_amount)) echo htmlentities($admin_settings->row()->welcome_amount); ?>"  class="large tipTop required number positiveNumber currencyT minfloatingNumber" title="<?php if ($this->lang->line('admin_setting_welcome_amount') != '') echo stripslashes($this->lang->line('admin_setting_welcome_amount')); else echo 'Welcome amount'; ?>"/>
                                        </div>
                                    </div>
                                </li>

                                <li class="curr-base-sec">
                                    <div class="form_grid_12">
                                        <label class="field_title"><?php if ($this->lang->line('admin_settings_amount_per_referal') != '') echo stripslashes($this->lang->line('admin_settings_amount_per_referal')); else echo 'Amount per Referral'; ?> <span class="req">*</span></label>
                                        <div class="form_input">
                                            <span class="curr-symbol"><?php echo $dcurrencySymbol . ' '; ?></span><input name="referal_amount" id="referal_amount" type="text" value="<?php if (isset($admin_settings->row()->referal_amount)) echo htmlentities($admin_settings->row()->referal_amount); ?>"  class="large tipTop required number positiveNumber currencyT minfloatingNumber" title="<?php if ($this->lang->line('admin_settings_amount_per_referal') != '') echo stripslashes($this->lang->line('admin_settings_amount_per_referal')); else echo 'Amount per Referral'; ?>"/>
                                        </div>
                                    </div>
                                </li>

                                <li>
                                    <div class="form_grid_12">
                                        <label class="field_title"><?php if ($this->lang->line('admin_settings_referal_credit') != '') echo stripslashes($this->lang->line('admin_settings_referal_credit')); else echo 'Referal Credit'; ?> <span class="req">*</span></label>
                                        <div class="form_input">
                                            <select name="referal_credit" id="referal_credit">
                                                <option value="instant" <?php
                                                if (isset($admin_settings->row()->referal_credit)) {
                                                    if ($admin_settings->row()->referal_credit == "instant") {
                                                        echo "selected='selected'";
                                                    }
                                                }
                                                ?>><?php if ($this->lang->line('admin_settings_instant') != '') echo stripslashes($this->lang->line('admin_settings_instant')); else echo 'Instant'; ?></option>
                                                <option value="on_first_ride" <?php
                                                if (isset($admin_settings->row()->referal_credit)) {
                                                    if ($admin_settings->row()->referal_credit == "on_first_ride") {
                                                        echo "selected='selected'";
                                                    }
                                                }
                                                ?>><?php if ($this->lang->line('admin_settings_on_first_ride') != '') echo stripslashes($this->lang->line('admin_settings_on_first_ride')); else echo 'On First Ride'; ?></option>
                                            </select>
                                        </div>
                                    </div>
                                </li>

                                <li>
                                    <h3 class="head_social"><?php if ($this->lang->line('admin_settings_app_settings') != '') echo stripslashes($this->lang->line('admin_settings_app_settings')); else echo 'App Settings'; ?></h3>
                                </li>

                                <li>
                                    <div class="form_grid_12">
                                        <label class="field_title"><?php if ($this->lang->line('admin_settings_driver_timeout') != '') echo stripslashes($this->lang->line('admin_settings_driver_timeout')); else echo 'Driver Timeout'; ?> <span class="req">*</span></label>
                                        <div class="form_input">
                                            <input name="respond_timeout" id="respond_timeout" type="text" value="<?php if (isset($admin_settings->row()->respond_timeout)) echo htmlentities($admin_settings->row()->respond_timeout); ?>"  class="large tipTop required number positiveNumber currencyT minfloatingNumber" title="<?php if ($this->lang->line('admin_setting_maximum_driver_respond_request') != '') echo stripslashes($this->lang->line('admin_setting_maximum_driver_respond_request')); else echo 'Maximum time for driver to respond for a request'; ?>"/>
                                            (<?php if ($this->lang->line('admin_settings_seconds') != '') echo stripslashes($this->lang->line('admin_settings_seconds')); else echo 'Seconds'; ?>)
                                        </div>
                                    </div>
                                </li>
								
								<li>
                                    <div class="form_grid_12">
                                        <label class="field_title"><?php if ($this->lang->line('admin_settings_user_timeout') != '') echo stripslashes($this->lang->line('admin_settings_user_timeout')); else echo 'User Timeout'; ?> <span class="req">*</span></label>
                                        <div class="form_input">
                                            <input type="text" class="large tipTop required number positiveNumber currencyT minfloatingNumber"  value="<?php if (isset($admin_settings->row()->user_timeout)) echo htmlentities($admin_settings->row()->user_timeout); ?>" id="user_timeout" name="user_timeout" original-title="<?php if ($this->lang->line('admin_setting_maximum_driver_respond_payment') != '') echo stripslashes($this->lang->line('admin_setting_maximum_driver_respond_payment')); else echo 'Maximum time for user to respond for a payment'; ?>">
                                            (<?php if ($this->lang->line('admin_settings_seconds') != '') echo stripslashes($this->lang->line('admin_settings_seconds')); else echo 'Seconds'; ?>)
                                        </div>
                                    </div>
                                </li>
								
                                <li>
                                    <div class="form_grid_12">
                                        <label class="field_title"><?php if ($this->lang->line('admin_settings_map_searching_radius') != '') echo stripslashes($this->lang->line('admin_settings_map_searching_radius')); else echo 'Map Searching Radius'; ?> <span class="req">*</span></label>
                                        <div class="form_input">
                                            <input name="map_searching_radius" id="map_searching_radius" type="text" value="<?php if (isset($admin_settings->row()->map_searching_radius)) echo htmlentities($admin_settings->row()->map_searching_radius); ?>"  class="large tipTop required number positiveNumber currencyT minfloatingNumber" title="<?php if ($this->lang->line('admin_setting_maximum_search_radius_map') != '') echo stripslashes($this->lang->line('admin_setting_maximum_search_radius_map')); else echo 'Maximum search radius in map.'; ?>"/>
                                            (<?php if ($this->lang->line('admin_settings_meters') != '') echo stripslashes($this->lang->line('admin_settings_meters')); else echo 'Meters'; ?>)
                                        </div>
                                    </div>
                                </li>

                                <li>
                                    <h3 class="head_social"><?php if ($this->lang->line('admin_settings_twilio_sms_api') != '') echo stripslashes($this->lang->line('admin_settings_twilio_sms_api')); else echo 'Twilio SMS API'; ?></h3>
                                </li>

                                <li class="net-account-type">
                                    <div class="form_grid_12">
                                        <label class="field_title"><?php if ($this->lang->line('admin_settings_account_type') != '') echo stripslashes($this->lang->line('admin_settings_account_type')); else echo 'Account Type'; ?></label>


                                        <div class="form_input">

                                            <?php
                                            if (isset($admin_settings->row()->twilio_account_type)) {
                                                $twilio_account_type = $admin_settings->row()->twilio_account_type;
                                            } else {
                                                $twilio_account_type = '';
                                            }
                                            ?>

                                            <p class="left-input-sel"><input name="twilio_account_type" id="twilio_account_live" type="radio" <?php if ($twilio_account_type == "prod") { echo "checked"; } ?> value="prod"  class="small tipTop" title="<?php if ($this->lang->line('admin_setting_select_twilio_account_type') != '') echo stripslashes($this->lang->line('admin_setting_select_twilio_account_type')); else echo 'Please select the twilio account type'; ?>"/>
                                            <label><?php if ($this->lang->line('admin_settings_live') != '') echo stripslashes($this->lang->line('admin_settings_live')); else echo 'LIVE'; ?></label></p>

                                            <p class="left-input-sel"><input name="twilio_account_type" <?php if ($twilio_account_type == "sandbox") { echo "checked"; } ?>  id="twilio_account_test" type="radio" value="sandbox"  class="small tipTop" title="<?php if ($this->lang->line('admin_setting_select_twilio_account_type') != '') echo stripslashes($this->lang->line('admin_setting_select_twilio_account_type')); else echo 'Please select the twilio account type'; ?>"/>
                                            <label><?php if ($this->lang->line('admin_settings_test') != '') echo stripslashes($this->lang->line('admin_settings_test')); else echo 'TEST'; ?></label></p>

                                        </div>


                                    </div>
                                </li>


                                <li>
                                    <div class="form_grid_12">
                                        <label class="field_title"><?php if ($this->lang->line('admin_settings_account_sid') != '') echo stripslashes($this->lang->line('admin_settings_account_sid')); else echo 'Account SID'; ?></label>
                                        <div class="form_input">
                                            <input name="twilio_account_sid" id="twilio_account_sid" type="text" value="<?php if (isset($admin_settings->row()->twilio_account_sid)) echo $admin_settings->row()->twilio_account_sid; ?>"  class="large tipTop " title="<?php if ($this->lang->line('admin_setting_enter_twilio_account_id') != '') echo stripslashes($this->lang->line('admin_setting_enter_twilio_account_id')); else echo 'Please enter the twilio account sid'; ?>"/>
                                        </div>
                                    </div>
                                </li>

                                <li>
                                    <div class="form_grid_12">
                                        <label class="field_title"><?php if ($this->lang->line('admin_settings_auth_token') != '') echo stripslashes($this->lang->line('admin_settings_auth_token')); else echo 'Auth Token'; ?></label>
                                        <div class="form_input">
                                            <input name="twilio_auth_token" id="twilio_auth_token" type="text" value="<?php if (isset($admin_settings->row()->twilio_auth_token)) echo $admin_settings->row()->twilio_auth_token; ?>"  class="large tipTop " title="<?php if ($this->lang->line('admin_setting_enter_twilio_auth_token') != '') echo stripslashes($this->lang->line('admin_setting_enter_twilio_auth_token')); else echo 'Please enter the twilio auth token'; ?>"/>
                                        </div>
                                    </div>
                                </li>

                                <li>
                                    <div class="form_grid_12">
                                        <label class="field_title"><?php if ($this->lang->line('admin_settings_number') != '') echo stripslashes($this->lang->line('admin_settings_number')); else echo 'Number'; ?></label>
                                        <div class="form_input">
                                            <input name="twilio_number" id="twilio_number" type="text" value="<?php if (isset($admin_settings->row()->twilio_number)) echo $admin_settings->row()->twilio_number; ?>"  class="large tipTop " title="<?php if ($this->lang->line('admin_setting_enter_twilio_number') != '') echo stripslashes($this->lang->line('admin_setting_enter_twilio_number')); else echo 'Please enter the twilio number'; ?>"/>
                                        </div>
                                    </div>
                                </li>
							
							
							
							
                                <li>
                                    <h3 class="head_social"><?php if ($this->lang->line('admin_settings_wallet_recharge_amount_settings') != '') echo stripslashes($this->lang->line('admin_settings_wallet_recharge_amount_settings')); else echo 'Wallet Recharge Amount Settings'; ?></h3>
                                </li>

                                <li class="curr-base-sec">
                                    <div class="form_grid_12">
                                        <label class="field_title"><?php if ($this->lang->line('admin_settings_minimum_amount') != '') echo stripslashes($this->lang->line('admin_settings_minimum_amount')); else echo 'Minimum Amount'; ?></label>
                                        <div class="form_input">
                                            <span class="curr-symbol"><?php echo $dcurrencySymbol; ?></span>
                                            <input name="wal_recharge_min_amount" id="wal_recharge_min_amount" type="text" value="<?php if (isset($admin_settings->row()->wal_recharge_min_amount)) echo $admin_settings->row()->wal_recharge_min_amount; ?>"   lesserThan="#wal_recharge_max_amount" class="large tipTop number positiveNumber currencyT minfloatingNumber" title="<?php if ($this->lang->line('admin_setting_enter_minimum_wallet_recharge_amount') != '') echo stripslashes($this->lang->line('admin_setting_enter_minimum_wallet_recharge_amount')); else echo 'Please enter minimum wallet recharge amount'; ?>"/>
                                        </div>
                                    </div>
                                </li>

                                <li class="curr-base-sec">
                                    <div class="form_grid_12">
                                        <label class="field_title"><?php if ($this->lang->line('admin_settings_maximum_amount') != '') echo stripslashes($this->lang->line('admin_settings_maximum_amount')); else echo 'Maximum Amount'; ?></label>
                                        <div class="form_input">
                                            <span class="curr-symbol"><?php echo $dcurrencySymbol; ?></span>
                                            <input name="wal_recharge_max_amount" id="wal_recharge_max_amount" type="text" value="<?php if (isset($admin_settings->row()->wal_recharge_max_amount)) echo $admin_settings->row()->wal_recharge_max_amount; ?>"  greaterThan="#wal_recharge_min_amount" class="large tipTop number positiveNumber currencyT greaterThan minfloatingNumber" title="<?php if ($this->lang->line('admin_setting_enter_maximum_wallet_recharge_amount') != '') echo stripslashes($this->lang->line('admin_setting_enter_maximum_wallet_recharge_amount')); else echo 'Please enter maximum wallet recharge amount'; ?>"/>
                                        </div>
                                    </div>
                                </li>
                                
							</ul>
							
							<ul class="rite-contsec-app">
								<li>
                                    <h3 class="head_social"><?php if ($this->lang->line('admin_settings_googlemap_places') != '') echo stripslashes($this->lang->line('admin_settings_googlemap_places')); else echo 'Google Map Places Search API Key'; ?></h3>
                                </li>
                                <li>
                                    <div class="form_grid_12">
                                        <label class="field_title"><?php if ($this->lang->line('admin_settings_website') != '') echo stripslashes($this->lang->line('admin_settings_website')); else echo 'Website'; ?></label>
                                        <div class="form_input">
                                           
                                            <input name="google_maps_api_key" id="google_maps_api_key" type="text" value="<?php if (isset($admin_settings->row()->google_maps_api_key)) echo $admin_settings->row()->google_maps_api_key; ?>"  class="large tipTop" title="<?php if ($this->lang->line('admin_setting_google_map_api_key') != '') echo stripslashes($this->lang->line('admin_setting_google_map_api_key')); else echo 'Please enter the Google Map Api key'; ?>"/>
                                        
                                     
                                        </div>
                                    </div>
                                </li>
								<?php /*
                                <li>
                                    <div class="form_grid_12">
                                        <label class="field_title"><?php if ($this->lang->line('admin_settings_android') != '') echo stripslashes($this->lang->line('admin_settings_android')); else echo 'Android'; ?></label>
                                        <div class="form_input">
                                           
                                           <input name="google_maps_api_key_android" id="google_maps_api_key_android" type="text" value="<?php if (isset($admin_settings->row()->google_maps_api_key_android)) echo $admin_settings->row()->google_maps_api_key_android; ?>"  class="large tipTop" title="<?php if ($this->lang->line('admin_setting_google_map_api_key') != '') echo stripslashes($this->lang->line('admin_setting_google_map_api_key')); else echo 'Please enter the Google Map Api key'; ?>"/>
                                        
                                     
                                        </div>
                                    </div>
                                </li>
                                <li>
                                    <div class="form_grid_12">
                                        <label class="field_title"><?php if ($this->lang->line('admin_settings_ios') != '') echo stripslashes($this->lang->line('admin_settings_ios')); else echo 'Ios'; ?></label>
                                        <div class="form_input">
                                           
                                           <input name="google_maps_api_key_ios" id="google_maps_api_key_ios" type="text" value="<?php if (isset($admin_settings->row()->google_maps_api_key_ios)) echo $admin_settings->row()->google_maps_api_key_ios; ?>"  class="large tipTop" title="<?php if ($this->lang->line('admin_setting_google_map_api_key') != '') echo stripslashes($this->lang->line('admin_setting_google_map_api_key')); else echo 'Please enter the Google Map Api key'; ?>"/>
                                        
                                     
                                        </div>
                                    </div>
                                </li>
								*/ ?>
                                <li>
                                    <h3 class="head_social"><?php if ($this->lang->line('admin_settings_pushnotification') != '') echo stripslashes($this->lang->line('admin_settings_pushnotification')); else echo 'Push Notification'; ?></h3>
                                </li>
								
								
								
                                <li>
                                    <h3 class="head_social"><?php if ($this->lang->line('admin_settings_android') != '') echo stripslashes($this->lang->line('admin_settings_android')); else echo 'Android Key'; ?></h3>
                                </li>
                                <li>
                                    <div class="form_grid_12">
                                        <label class="field_title"><?php if ($this->lang->line('admin_settings_android_user') != '') echo stripslashes($this->lang->line('admin_settings_android_user')); else echo 'Android User'; ?></label>
                                        <div class="form_input">
                                           
                                           <input name="push_android_user" id="push_android_user" type="text" value="<?php if (isset($admin_settings->row()->push_android_user)) echo $admin_settings->row()->push_android_user; ?>"  class="large tipTop" />
                                        
                                     
                                        </div>
                                    </div>
                              </li>
                              <li>
                                    <div class="form_grid_12">
                                        <label class="field_title"><?php if ($this->lang->line('admin_settings_android_driver') != '') echo stripslashes($this->lang->line('admin_settings_android_driver')); else echo 'Android Driver'; ?></label>
                                        <div class="form_input">
                                           
                                           <input name="push_android_driver" id="push_android_driver" type="text" value="<?php if (isset($admin_settings->row()->push_android_driver)) echo $admin_settings->row()->push_android_driver; ?>"  class="large tipTop" />
                                        
                                     
                                        </div>
                                    </div>
                              </li>
                              <li>
                                    <h3 class="head_social"><?php if ($this->lang->line('admin_settings_ios_pem') != '') echo stripslashes($this->lang->line('admin_settings_ios_pem')); else echo 'Ios Pem File'; ?></h3>
                              </li>
                              <li>
                                    <div class="form_grid_12">
                                        <label class="field_title">Server Key</label>
                                        <div class="form_input">
                                           <input name="push_ios_key" id="push_ios_key" type="text" value="<?php if (isset($admin_settings->row()->push_ios_key)) echo $admin_settings->row()->push_ios_key; ?>"  class="large tipTop" />
                                        </div>
                                    </div>
                              </li>
                              <li>
                                <h3 class="head_social"><?php if ($this->lang->line('admin_settings_ios_pem_development') != '') echo stripslashes($this->lang->line('admin_settings_ios_pem_development')); else echo 'Development'; ?></h3>
                              </li>
                              <li>
                                    <div class="form_grid_12">
                                        <label class="field_title"><?php if ($this->lang->line('ios_user_pem') != '') echo stripslashes($this->lang->line('ios_user_pem')); else echo 'Ios User'; ?></label>
                                        <div class="form_input">
                                            <input name="ios_user_dev" id="ios_user_dev" type="file"  class="large tipTop" value="<?php if(isset($admin_settings->row()->ios_user_dev)) echo $admin_settings->row()->ios_user_dev; ?>"/>
                                        <?php if(isset($admin_settings->row()->ios_user_dev) && $admin_settings->row()->ios_user_dev != '') {?>
                                         <a href="certificates/<?php echo $admin_settings->row()->ios_user_dev; ?>" target="_blank" ><?php echo $admin_settings->row()->ios_user_dev; ?></a>
                                        <?php }?>
                                        </div>
                                        
                                      
                                       
                                    </div>
                                </li>
                                <li>
                                    <div class="form_grid_12">
                                        <label class="field_title"><?php if ($this->lang->line('ios_driver_pem') != '') echo stripslashes($this->lang->line('ios_driver_pem')); else echo 'Ios Driver'; ?></label>
                                        <div class="form_input">
                                            <input name="ios_driver_dev" id="ios_driver_dev" type="file"  class="large tipTop" value="<?php  if(isset($admin_settings->row()->ios_driver_dev) && $admin_settings->row()->ios_driver_dev != '') echo $admin_settings->row()->ios_driver_dev; ?>"/>
                                              <?php if(isset($admin_settings->row()->ios_driver_dev) && $admin_settings->row()->ios_driver_dev != '') {?>
                                              <a href="certificates/<?php echo $admin_settings->row()->ios_driver_dev; ?>" target="_blank" ><?php echo $admin_settings->row()->ios_driver_dev; ?></a>
                                           <?php }?>
                                        </div>
                                      
                                    </div>
                               </li>
                               <li>
                                <h3 class="head_social"><?php if ($this->lang->line('admin_settings_ios_pem_production') != '') echo stripslashes($this->lang->line('admin_settings_ios_pem_production')); else echo 'Production'; ?></h3>
                              </li>
                              <li>
                                    <div class="form_grid_12">
                                        <label class="field_title"><?php if ($this->lang->line('ios_user_pem') != '') echo stripslashes($this->lang->line('ios_user_pem')); else echo 'Ios User'; ?></label>
                                        <div class="form_input">
                                            <input name="ios_user_prod" id="ios_user_prod" type="file"  class="large tipTop" value="<?php if(isset($admin_settings->row()->ios_user_prod)  && $admin_settings->row()->ios_user_prod !='' )  echo $admin_settings->row()->ios_user_prod; ?>"/>
                                              <?php if(isset($admin_settings->row()->ios_user_prod)  && $admin_settings->row()->ios_user_prod !='' ) {?>
                                               <a href="certificates/<?php echo $admin_settings->row()->ios_user_prod; ?>" target="_blank" ><?php echo $admin_settings->row()->ios_user_prod; ?></a>
                                             <?php }?>
                                        </div>
                                       
                                    </div>
                                </li>
                                <li>
                                    <div class="form_grid_12">
                                        <label class="field_title"><?php if ($this->lang->line('ios_driver_pem') != '') echo stripslashes($this->lang->line('ios_driver_pem')); else echo 'Ios Driver'; ?></label>
                                        <div class="form_input">
                                            <input name="ios_driver_prod" id="ios_driver_prod" type="file"  class="large tipTop"value="<?php if(isset($admin_settings->row()->ios_driver_prod) && $admin_settings->row()->ios_driver_prod!='') echo $admin_settings->row()->ios_driver_prod; ?>" />
                                              <?php if(isset($admin_settings->row()->ios_driver_prod) && $admin_settings->row()->ios_driver_prod!='') {?>
                                                <a href="certificates/<?php echo $admin_settings->row()->ios_driver_prod; ?>" target="_blank" ><?php echo $admin_settings->row()->ios_driver_prod; ?></a>
                                             <?php }?>
                                        </div>
                                      
                                    </div>
                               </li>
                                <!--<li>
                                    <h3 class="head_social"><?php if ($this->lang->line('admin_settings_about_us') != '') echo stripslashes($this->lang->line('admin_settings_about_us')); else echo 'About us'; ?></h3>
                                </li>
                               <li>
                                    <div class="form_grid_12">
                                        <label class="field_title"><?php if ($this->lang->line('admin_settings_about_us_user') != '') echo stripslashes($this->lang->line('admin_settings_about_us_user')); else echo 'User App - About us'; ?></label>
                                        <div class="form_input">
                                            <textarea name="about_us" class="" cols="70" rows="5" ><?php if(isset($admin_settings->row()->about_us)) echo $admin_settings->row()->about_us; ?></textarea>
                                        </div>
                                    </div>
                                </li>
                               <li>
                                    <div class="form_grid_12">
                                        <label class="field_title"><?php if ($this->lang->line('admin_settings_about_us_driver') != '') echo stripslashes($this->lang->line('admin_settings_about_us_driver')); else echo 'Driver App - About us'; ?></label>
                                        <div class="form_input">
                                            <textarea name="about_us_driver" class="" cols="70" rows="5" ><?php if(isset($admin_settings->row()->about_us_driver)) echo $admin_settings->row()->about_us_driver; ?></textarea>
                                        </div>
                                    </div>
                                </li>-->
                                <li>
                                    <div class="form_grid_12">
                                        <label class="field_title">User App Version Control</label>
                                        <div class="form_input">
                                            <input name="user_version_control" id="user_version_control" type="text" value="<?php if (isset($admin_settings->row()->user_version_control)) echo htmlentities($admin_settings->row()->user_version_control); ?>"  class="large tipTop "/>
                                        </div>
                                    </div>
                                </li>
                                <li>
                                    <div class="form_grid_12">
                                        <label class="field_title">Driver App Version Control</label>
                                        <div class="form_input">
                                            <input name="driver_version_control" id="driver_version_control" type="text" value="<?php if (isset($admin_settings->row()->driver_version_control)) echo htmlentities($admin_settings->row()->driver_version_control); ?>"  class="large tipTop "/>
                                        </div>
                                    </div>
                                </li>

																<li>
                                    <h3 class="head_social"><?php if ($this->lang->line('admin_appsettings_link') != '') echo stripslashes($this->lang->line('admin_appsettings_link')); else echo 'Link'; ?></h3>
                                </li>
								<li>
									<h3 class="head_social"><?php if ($this->lang->line('admin_appsettings_playstore_link') != '') echo stripslashes($this->lang->line('admin_appsettings_playstore_link')); else echo 'Playstore link'; ?></h3>
								</li>

                                <li>
                                    <div class="form_grid_12">
                                        <label class="field_title"><?php if ($this->lang->line('admin_cancelled_by_user') != '') echo stripslashes($this->lang->line('admin_cancelled_by_user')); else echo 'User'; ?></label>
                                        <div class="form_input">
                                            <input name="user_playstore_link" id="user_playstore_link" type="text" value="<?php if (isset($admin_settings->row()->user_playstore_link)) echo htmlentities($admin_settings->row()->user_playstore_link); ?>"  class="large tipTop url"/>
                                        </div>
                                    </div>
                                </li>
																
								<li>
                                    <div class="form_grid_12">
                                        <label class="field_title"><?php if ($this->lang->line('admin_cancelled_by_driver') != '') echo stripslashes($this->lang->line('admin_cancelled_by_driver')); else echo 'Driver'; ?></label>
                                        <div class="form_input">
                                            <input name="driver_playstore_link" id="driver_playstore_link" type="text" value="<?php if (isset($admin_settings->row()->driver_playstore_link)) echo htmlentities($admin_settings->row()->driver_playstore_link); ?>"  class="large tipTop url"/>
                                        </div>
                                    </div>
                                </li>
																
								<li>
									<h3 class="head_social"><?php if ($this->lang->line('admin_appsettings_appstore_link') != '') echo stripslashes($this->lang->line('admin_appsettings_appstore_link')); else echo 'Appstore Link'; ?></h3>
								</li>

                                <li>
                                    <div class="form_grid_12">
                                        <label class="field_title"><?php if ($this->lang->line('admin_cancelled_by_user') != '') echo stripslashes($this->lang->line('admin_cancelled_by_user')); else echo 'User'; ?></label>
                                        <div class="form_input">
                                            <input name="user_appstore_link" id="user_appstore_link" type="text" value="<?php if (isset($admin_settings->row()->user_appstore_link)) echo htmlentities($admin_settings->row()->user_appstore_link); ?>"  class="large tipTop url"/>
                                        </div>
                                    </div>
                                </li>
																
								<li>
                                    <div class="form_grid_12">
                                        <label class="field_title"><?php if ($this->lang->line('admin_cancelled_by_driver') != '') echo stripslashes($this->lang->line('admin_cancelled_by_driver')); else echo 'Driver'; ?></label>
                                        <div class="form_input">
                                            <input name="driver_appstore_link" id="driver_appstore_link" type="text" value="<?php if (isset($admin_settings->row()->driver_appstore_link)) echo htmlentities($admin_settings->row()->driver_appstore_link); ?>"  class="large tipTop url"/>
                                        </div>
                                    </div>
                                </li>
								
								<li>
                                  <h3 class="field_title"><?php if ($this->lang->line('admin_ride_settings') != '') echo stripslashes($this->lang->line('admin_ride_settings')); else echo 'Ride Settings'; ?></h3>
                                </li>
								<li>
                                    <div class="form_grid_12">
                                        <label class="field_title" for="respond_timeout"><?php if ($this->lang->line('admin_driv_multi_cat_optn') != '') echo stripslashes($this->lang->line('admin_driv_multi_cat_optn')); else echo 'Multiple category ride option'; ?>?<span class="req">*</span></label>
                                        <div class="form_input">
                                            <select name="multi_category_option" id="multi_category_option">
                                                <option <?php
                                                if (isset($admin_settings->row()->multi_category_option)) {
                                                    if ($admin_settings->row()->multi_category_option == "OFF") {
                                                        echo "selected='selected'";
                                                    }
                                                }
                                                ?>><?php if ($this->lang->line('admin_driv_ride_off') != '') echo stripslashes($this->lang->line('admin_driv_ride_off')); else echo 'OFF'; ?></option>
												<option <?php
                                                if (isset($admin_settings->row()->multi_category_option)) {
                                                    if ($admin_settings->row()->multi_category_option == "ON") {
                                                        echo "selected='selected'";
                                                    }
                                                }
                                                ?>><?php if ($this->lang->line('admin_driv_ride_on') != '') echo stripslashes($this->lang->line('admin_driv_ride_on')); else echo 'ON'; ?></option>
                                            </select>
                                        </div>
                                    </div>
                                </li>
								
								<li>
                                    <div class="form_grid_12">
                                        <label class="field_title" for="respond_timeout"><?php if ($this->lang->line('dash_toll_park_charge') != '') echo stripslashes($this->lang->line('dash_toll_park_charge')); else echo 'Toll & Parking Charges'; ?>?<span class="req">*</span></label>
                                        <div class="form_input">
                                            <select name="toll_parking_status" id="toll_parking_status">
                                                <option <?php
                                                if (isset($admin_settings->row()->toll_parking_status)) {
                                                    if ($admin_settings->row()->toll_parking_status == "OFF") {
                                                        echo "selected='selected'";
                                                    }
                                                }
                                                ?>><?php if ($this->lang->line('admin_driv_ride_off') != '') echo stripslashes($this->lang->line('admin_driv_ride_off')); else echo 'OFF'; ?></option>
												<option <?php
                                                if (isset($admin_settings->row()->toll_parking_status)) {
                                                    if ($admin_settings->row()->toll_parking_status == "ON") {
                                                        echo "selected='selected'";
                                                    }
                                                }
                                                ?>><?php if ($this->lang->line('admin_driv_ride_on') != '') echo stripslashes($this->lang->line('admin_driv_ride_on')); else echo 'ON'; ?></option>
                                            </select>
                                        </div>
                                    </div>
                                </li>
								
								<li>
                                    <div class="form_grid_12">
                                        <label class="field_title" for="respond_timeout"><?php if ($this->lang->line('gender_preference_status') != '') echo stripslashes($this->lang->line('gender_preference_status')); else echo 'Trip gender preference request'; ?>?<span class="req">*</span></label>
                                        <div class="form_input">
                                            <select name="gender_perf_status" id="gender_perf_status">
                                                <option <?php
                                                if (isset($admin_settings->row()->gender_perf_status)) {
                                                    if ($admin_settings->row()->gender_perf_status == "OFF") {
                                                        echo "selected='selected'";
                                                    }
                                                }
                                                ?>><?php if ($this->lang->line('admin_driv_ride_off') != '') echo stripslashes($this->lang->line('admin_driv_ride_off')); else echo 'OFF'; ?></option>
												<option <?php
                                                if (isset($admin_settings->row()->gender_perf_status  )) {
                                                    if ($admin_settings->row()->gender_perf_status  == "ON") {
                                                        echo "selected='selected'";
                                                    }
                                                }
                                                ?>><?php if ($this->lang->line('admin_driv_ride_on') != '') echo stripslashes($this->lang->line('admin_driv_ride_on')); else echo 'ON'; ?></option>
                                            </select>
                                        </div>
                                    </div>
                                </li>
																
                            </ul>
                            <ul class="last-sec-button">
                                <li>
                                    <div class="form_grid_12">
                                        <div class="form_input">
                                            <button type="submit" class="btn_small btn_blue" ><span><?php if ($this->lang->line('admin_settings_submit') != '') echo stripslashes($this->lang->line('admin_settings_submit')); else echo 'Submit'; ?></span></button>
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
    </div>
    <span class="clear"></span> 
</div>
</div>

<script type="text/javascript">
    $(document).ready(function () {
        if ($("#publish1").attr("checked") == "checked") {
            $("#d_mode1").attr("checked", false);
            $("#d_mode2").attr("checked", false);
            $("#d_mode3").attr("checked", true);
            $("#d_mode4").attr("checked", false);
            $("li#dev_mode").hide();
        }
        $("#publish1").click(function () {
            $("#d_mode1").attr("checked", false);
            $("#d_mode2").attr("checked", false);
            $("#d_mode3").attr("checked", false);
            $("#d_mode4").attr("checked", false);
            $("li#dev_mode").hide();
        });
    });
</script>

<?php
$this->load->view(ADMIN_ENC_URL.'/templates/footer.php');
?>