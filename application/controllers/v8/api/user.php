<?php if (!defined('BASEPATH')) exit('No direct script access allowed');

/**
*  
* User related functions
* @author Casperon
*
**/

class User extends MY_Controller {

    function __construct() {
        parent::__construct();
        $this->load->helper(array('cookie', 'date', 'form', 'email','ride_helper'));
        $this->load->library(array('encrypt', 'form_validation'));
        $this->load->model(array('user_model','app_model','dynamic_driver'));
        $returnArr = array();

		/* Authentication Begin */
        $headers = $this->input->request_headers();
		header('Content-type:application/json;charset=utf-8');
		if (array_key_exists("Authkey", $headers)) $auth_key = $headers['Authkey']; else $auth_key = "";
		if(stripos($auth_key,APP_NAME) === false) {
			$cf_fun= $this->router->fetch_method();
			$apply_function = array('check_account','check_social_login','register_user','social_Login','login_user','proceed_payment');
			if(!in_array($cf_fun,$apply_function)){
				show_404();
			}
		}
		
        if (array_key_exists("Apptype", $headers)) $this->Apptype = $headers['Apptype'];
        if (array_key_exists("Userid", $headers)) $this->Userid = $headers['Userid'];
        if (array_key_exists("Apptoken", $headers)) $this->Token = $headers['Apptoken'];
        try {
            if ($this->Userid != "" && $this->Token != "" && $this->Apptype != "") {
                $deadChk = $this->app_model->get_selected_fields(USERS, array('_id' => MongoID($this->Userid)), array('push_type', 'push_notification_key','status'));
                if ($deadChk->num_rows() > 0) {
					$storedToken = '';
                    if (strtolower($deadChk->row()->push_type) == "ios") {
                        $storedToken = $deadChk->row()->push_notification_key["ios_token"];
                    }
                    if (strtolower($deadChk->row()->push_type) == "android") {
                        $storedToken = $deadChk->row()->push_notification_key["gcm_id"];
                    }
					$c_fun= $this->router->fetch_method();
					$apply_function = array('login_user','social_Login','logout_user');
					if(!in_array($c_fun,$apply_function)){
						if(strtolower($deadChk->row()->status)!="active"){
							$is_out_message = $this->format_string('Your account has been modified, please login to again.', 'is_out_message');
							echo json_encode(array("is_out" => "Yes","message" => $is_out_message));die;
						}
						if($storedToken!=''){
							if ($storedToken != $this->Token) {
								echo json_encode(array("is_dead" => "Yes"));
								die;
							}
						}
					}
                }else{
					$is_out_message = $this->format_string('Your account has been modified, please login to again.', 'is_out_message');
					echo json_encode(array("is_out" => "Yes","message" => $is_out_message));die;
				}
            }
        } catch (MongoException $ex) {
            
        }
		/* Authentication End */
    }
	

	/**
	*
	* This function creates a new account for user
	*
	**/
    public function check_account() {
        $returnArr['status'] = '0';
        $returnArr['message'] = '';
        try {
            $email = $this->input->post('email');
            $password = $this->input->post('password');
            $user_name = $this->input->post('user_name');
            $country_code = $this->input->post('country_code');
            $phone_number = $this->input->post('phone_number');
            $referal_code = $this->input->post('referal_code');
            $gcm_id = $this->input->post('gcm_id');
            $deviceToken = $this->input->post('deviceToken');

            if (is_array($this->input->post())) {
                $chkValues = count(array_filter($this->input->post()));
            } else {
                $chkValues = 0;
            }

            if ($chkValues >= 5) {
                if (valid_email($email)) {
                    $checkEmail = $this->user_model->check_user_exist(array('email' => $email));
                    if ($checkEmail->num_rows() >= 1) {
                        if ($checkEmail->row()->status != "Active") {
                            $returnArr['message'] = $this->format_string("Your account is currenty unavailable", "account_currently_unavailbale");
                        } else {
                            $returnArr['message'] = $this->format_string('Email address already exists', 'email_already_exist');
                        }
                    } else {
                        $condition = array('country_code' => $country_code, 'phone_number' => $phone_number);
                        $chekMobile = $this->user_model->get_selected_fields(USERS, $condition, array('_id'));
                        if ($chekMobile->num_rows() == 0) {
                            $cStatus = FALSE;
                            if ($referal_code != '') {
                                $chekCode = $this->user_model->get_selected_fields(USERS, array('unique_code' => $referal_code), array('_id'));
                                if ($chekCode->num_rows() > 0) {
                                    $cStatus = TRUE;
                                }
                            } else {
                                $cStatus = TRUE;
                            }
                            if ($cStatus) {
                                $key = '';
                                if ($gcm_id != "") {
                                    $key = $gcm_id;
                                } else if ($deviceToken != "") {
                                    $key = $deviceToken;
                                }
                                $otp_string = $this->user_model->get_random_string(6);
                                $otp_status = "development";
                                if ($this->config->item('twilio_account_type') == 'prod') {
                                    $otp_status = "production";
                                    $this->sms_model->opt_for_registration($country_code, $phone_number, $otp_string,$this->app_language);
                                }
                                $returnArr['message'] = $this->format_string('Success', 'success');
                                $returnArr['user_name'] = $user_name;
                                $returnArr['email'] = $email;
                                $returnArr['country_code'] = $country_code;
                                $returnArr['phone_number'] = $phone_number;
                                $returnArr['referal_code'] = $referal_code;
                                $returnArr['key'] = $key;
                                $returnArr['otp_status'] = (string) $otp_status;
                                $returnArr['otp'] = (string) $otp_string;
                                $returnArr['status'] = '1';
                            } else {
                                $returnArr['message'] = $this->format_string('Invalid referral code', 'invalid_referral_code');
                            }
                        } else {
                            $returnArr['message'] = $this->format_string('This mobile number already registered', 'mobile_number_already_registered');
                        }
                    }
                } else {
                    $returnArr['message'] = $this->format_string("Invalid Email address", "invalid_email_address");
                }
            } else {
                $returnArr['message'] = $this->format_string("Some Parameters are missing", "some_parameters_missing");
            }
        } catch (MongoException $ex) {
            $returnArr['response'] = $this->format_string("Error in connection", "error_in_connection");
        }
        $json_encode = json_encode($returnArr, JSON_PRETTY_PRINT);
        echo $this->cleanString($json_encode);
    }

	/**
	*
	* This function creates a new account for user
	*
	**/
    public function check_social_login() {
        $returnArr['status'] = '0';
        $returnArr['message'] = '';
        try {
            $media_id = $this->input->post('media_id');
            $gcm_id = $this->input->post('gcm_id');
            $deviceToken = $this->input->post('deviceToken');
			$email = $this->input->post('email');
 
            if ($media_id != "") {
                $condition = array('media_id' => $media_id);
                $checkUser = $this->user_model->get_all_details(USERS, $condition);
				
				if($checkUser->num_rows() == 0 && $email != ''){
					$condition = array('email' => $email);
					$checkUser = $this->user_model->get_all_details(USERS, $condition);
				}
		
                if ($checkUser->num_rows() == 1) {
                    if ($checkUser->row()->status == "Active") {
                        $push_data = array();
                        $key = '';
                        if ($gcm_id != "") {
                            $key = $gcm_id;
                            $push_data = array('push_notification_key.gcm_id' => $gcm_id, 'push_type' => 'ANDROID');
                            $push_update_data = array('push_notification_key.gcm_id' => '', 'push_type' => '');
                        } else if ($deviceToken != "") {
                            $key = $deviceToken;
                            $push_data = array('push_notification_key.ios_token' => $deviceToken, 'push_type' => 'IOS');
                            $push_update_data = array('push_notification_key.ios_token' => '', 'push_type' => '');
                        }
                        if (!empty($push_data)) {
                            $this->user_model->update_details(USERS, $push_update_data, $push_data);
                            $this->user_model->update_details(USERS, $push_data, array('_id' => MongoID($checkUser->row()->_id)));
                        }

                        $returnArr['status'] = '1';
                        $returnArr['message'] = $this->format_string('You are Logged In successfully', 'you_logged_in');
                        $userVal = $this->user_model->get_selected_fields(USERS, array('_id' => MongoID($checkUser->row()->_id)), array('email', 'image', 'user_name', 'country_code', 'phone_number', 'referral_code', 'push_notification_key.gcm_id','unique_code'));
                        if ($userVal->row()->image == '') {
                            $user_image = USER_PROFILE_IMAGE_DEFAULT;
                        } else {
                            $user_image = USER_PROFILE_IMAGE . $userVal->row()->image;
                        }
                        $returnArr['user_image'] = base_url() . $user_image;
                        $returnArr['user_id'] = (string) $checkUser->row()->_id;
                        $returnArr['user_name'] = $userVal->row()->user_name;
                        $returnArr['email'] = $userVal->row()->email;
                        $returnArr['country_code'] = $userVal->row()->country_code;
                        $returnArr['phone_number'] = $userVal->row()->phone_number;
                        $returnArr['sec_key'] = md5((string) $checkUser->row()->_id);
						$returnArr['referal_code'] = $userVal->row()->unique_code;
                        $returnArr['key'] = $key;

                        $walletDetail = $this->user_model->get_selected_fields(WALLET, array('user_id' => MongoID($checkUser->row()->_id)), array('total'));
                        $avail_amount = 0;
                        if (isset($walletDetail->row()->total)) {
                            $avail_amount = $walletDetail->row()->total;
                        }
                        
                        $wallet_amount = round($avail_amount,2);
                        
                        $returnArr['wallet_amount'] = (string) number_format($wallet_amount,2);
                        $returnArr['currency'] = (string) $this->data['dcurrencyCode'];
						
						
						$is_alive_other = "No";
						if ($checkUser->row()->push_type != '') {
							if ($checkUser->row()->push_type == "ANDROID") {
								$existingKey = $checkUser->row()->push_notification_key["gcm_id"];
							}
							if ($checkUser->row()->push_type == "IOS") {
								$existingKey = $checkUser->row()->push_notification_key["ios_token"];
							}
							if ($existingKey != $key) {
								$is_alive_other = "Yes";
							}
						}
						$returnArr['is_alive_other'] = (string) $is_alive_other;
						
						$categoryResult = $this->app_model->get_selected_fields(CATEGORY, array('status' => 'Active'), array('name'));
						$category = '';
						if ($categoryResult->num_rows() > 0) {
							$category = $categoryResult->row()->_id;
						}
						$returnArr['category'] = (string) $category;
                    } else {
                        if ($checkUser->row()->status == "Deleted") {
                            $returnArr['message'] = $this->format_string("Your account is currently unavailable", "account_currently_unavailbale");
                        } else {
                            $returnArr['message'] = $this->format_string("Your account has been inactivated", "your_account_inactivated");
                        }
                    }
                } else {
                    $returnArr['status'] = '2';
                    $returnArr['message'] = $this->format_string("Continue Signup Process", "continue_signup_process");
                }
            } else {
                $returnArr['message'] = $this->format_string("Authentication Failed", "authentication_failed");
            }
        } catch (MongoException $ex) {
            $returnArr['response'] = $this->format_string("Error in connection", "error_in_connection");
        }
        $json_encode = json_encode($returnArr, JSON_PRETTY_PRINT);
        echo $this->cleanString($json_encode);
    }

	/**
	*
	* This function creates a new account for user
	*
	**/
    public function register_user() {
        $returnArr['status'] = '0';
        $returnArr['message'] = '';
        try {
            $email = strtolower($this->input->post('email'));
            $password = $this->input->post('password');
            $user_name = $this->input->post('user_name');
            $country_code = $this->input->post('country_code');
            $phone_number = $this->input->post('phone_number');
            $referal_code = $this->input->post('referal_code');
            $gcm_id = $this->input->post('gcm_id');
            $deviceToken = $this->input->post('deviceToken');
			$latitude = $this->input->post('lat');
			$longitude = $this->input->post('lon');

            if (is_array($this->input->post())) {
                $chkValues = count(array_filter($this->input->post()));
            } else {
                $chkValues = 0;
            }

            if ($chkValues >= 5) {
                if (valid_email($email)) {
                    $checkEmail = $this->user_model->check_user_exist(array('email' => $email));
                    if ($checkEmail->num_rows() >= 1) {
                        $returnArr['message'] = $this->format_string('Email address already exists', 'email_already_exist');
                    } else {
                        $cStatus = FALSE;
                        if ($referal_code != '') {
                            $chekCode = $this->user_model->get_selected_fields(USERS, array('unique_code' => $referal_code), array('_id'));
                            if ($chekCode->num_rows() > 0) {
                                $cStatus = TRUE;
                            }
                        } else {
                            $cStatus = TRUE;
                        }
                        if ($cStatus) {
                            $verification_code = $this->get_rand_str('10');
                            $unique_code = $this->app_model->get_unique_id($user_name);
                            $user_data = array('user_name' => $user_name,
                                'user_type' => 'Normal',
                                'unique_code' => $unique_code,
                                'email' => $email,
                                'password' => md5($password),
                                'image' => '',
                                'status' => 'Active',
                                'country_code' => $country_code,
                                'phone_number' => $phone_number,
                                'referral_code' => $referal_code,
                                'verification_code' => array("email" => $verification_code),
                                'created' => date("Y-m-d H:i:s")
                            );
                            $this->user_model->insert_user($user_data);
                            $last_insert_id = $this->mongo_db->insert_id();
                            if ($last_insert_id != '') {
                                $push_data = array();
                                $key = '';
                                if ($gcm_id != "") {
                                    $key = $gcm_id;
                                    $push_data = array('push_notification_key.gcm_id' => $gcm_id, 'push_type' => 'ANDROID');
                                    $push_update_data = array('push_notification_key.gcm_id' => '', 'push_type' => '');
                                } else if ($deviceToken != "") {
                                    $key = $deviceToken;
                                    $push_data = array('push_notification_key.ios_token' => $deviceToken, 'push_type' => 'IOS');
                                    $push_update_data = array('push_notification_key.ios_token' => '', 'push_type' => '');
                                }
                                if (!empty($push_data)) {
                                    $this->user_model->update_details(USERS, $push_update_data, $push_data);
                                    $this->user_model->update_details(USERS, $push_data, array('_id' => MongoID($last_insert_id)));
                                }

                                $returnArr['message'] = $this->format_string('Successfully registered', 'successfully_registered');
                                $userVal = $this->user_model->get_selected_fields(USERS, array('_id' => MongoID($last_insert_id)), array('image', 'password'));
                                if ($userVal->row()->image == '') {
                                    $user_image = USER_PROFILE_IMAGE_DEFAULT;
                                } else {
                                    $user_image = USER_PROFILE_IMAGE . $userVal->row()->image;
                                }
                                $returnArr['user_image'] = base_url() . $user_image;
                                $returnArr['user_id'] = (string) $last_insert_id;
                                $returnArr['user_name'] = $user_name;
                                $returnArr['email'] = $email;
                                $returnArr['country_code'] = $country_code;
                                $returnArr['phone_number'] = $phone_number;
                                $returnArr['referal_code'] = $unique_code;
                                $returnArr['sec_key'] = md5((string) $last_insert_id);
                                $returnArr['key'] = $key;
                                $returnArr['status'] = '1';

                                $fields = array(
                                    'username' => (string) $last_insert_id,
                                    'password' => md5((string) $last_insert_id)
                                );
															  $url = $this->data['soc_url'] . 'create-user.php';
															  $this->load->library('curl');
															  $output = $this->curl->simple_post($url, $fields);
								$location = $this->app_model->find_location(floatval($longitude), floatval($latitude));
					
								if(!empty($location['result'])) {
									$final_cat_list = $location['result'][0]['avail_category'];
								}

								$selected_Category='';
								if(!empty($final_cat_list)) {
									$selected_Category=$final_cat_list[0];
								}
                                $returnArr['currency'] = (string) $this->data['dcurrencyCode'];
                                $returnArr['category'] = (string) $selected_Category;

                                /* Insert Referal and wallet collection */
                                $this->user_model->simple_insert(REFER_HISTORY, array('user_id' => MongoID($last_insert_id)));
                                $this->user_model->simple_insert(WALLET, array('user_id' => MongoID($last_insert_id), 'total' => floatval(0)));
                                /* Update the welcome amount to the registered user wallet */
                                if($this->config->item('welcome_amount') > 0){
                                    $trans_id = time() . rand(0, 2578);
                                    $initialAmt = array('type' => 'CREDIT',
                                        'credit_type' => 'welcome',
                                        'ref_id' => '',
                                        'trans_amount' => floatval($this->config->item('welcome_amount')),
                                        'avail_amount' => floatval($this->config->item('welcome_amount')),
                                        'trans_date' => MongoDATE(time()),
                                        'trans_id' => $trans_id
                                    );
                                    $this->user_model->simple_push(WALLET, array('user_id' => MongoID($last_insert_id)), array('transactions' => $initialAmt));
                                    $this->user_model->update_wallet((string) $last_insert_id, 'CREDIT', floatval($this->config->item('welcome_amount')));
                                }
                                
                                /* Update the referer history */
                                if ($referal_code != '' && $this->config->item('referal_amount') > 0) {
                                    $refererVal = $this->user_model->get_selected_fields(USERS, array('unique_code' => $referal_code), array('email','referral_count'));
                                    if ($refererVal->num_rows() > 0) {
									
										$referral_count = 1;
										if(isset($refererVal->row()->referral_count)) $referral_count = $refererVal->row()->referral_count+1;
										$userCond = array('_id' => MongoID($refererVal->row()->_id));
										$this->user_model->update_details(USERS, array('referral_count' => floatval($referral_count)), $userCond);
									
                                        $ref_status = 'true';
                                        $amount_earns = floatval($this->config->item('referal_amount'));
                                        if ($this->config->item('referal_credit') == 'on_first_ride') {
                                            $ref_status = 'false';
                                            #$amount_earns = floatval(0);
                                        }
                                        $refArr = array('reference_id' => (string) $last_insert_id,
                                            'reference_mail' => (string) $email,
                                            'amount_earns' => $amount_earns,
                                            'reference_date' => MongoDATE(time()),
                                            'used' => $ref_status
                                        );
                                        $this->user_model->simple_push(REFER_HISTORY, array('user_id' => MongoID($refererVal->row()->_id)), array('history' => $refArr));
                                        if ($this->config->item('referal_credit') == 'instant') {
                                            $this->user_model->update_wallet((string) $refererVal->row()->_id, 'CREDIT', floatval($this->config->item('referal_amount')));
                                            $walletDetail = $this->user_model->get_selected_fields(WALLET, array('user_id' => MongoID($refererVal->row()->_id)), array('total'));
                                            $avail_amount = 0;
                                            if (isset($walletDetail->row()->total)) {
                                                $avail_amount = $walletDetail->row()->total;
                                            }
                                            $trans_id = time() . rand(0, 2578);
                                            $walletArr = array('type' => 'CREDIT',
                                                'credit_type' => 'referral',
                                                'ref_id' => (string) $last_insert_id,
                                                'trans_amount' => floatval($this->config->item('referal_amount')),
                                                'avail_amount' => floatval($avail_amount),
                                                'trans_date' => MongoDATE(time()),
                                                'trans_id' => $trans_id
                                            );
                                            $this->user_model->simple_push(WALLET, array('user_id' => MongoID($refererVal->row()->_id)), array('transactions' => $walletArr));
                                        }
                                    }
                                }

                                /* Update Stats Starts */
                                $current_date = MongoDATE(strtotime(date("Y-m-d 00:00:00")));
                                $field = array('user.hour_' . date('H') => 1, 'user.count' => 1);
                                $this->app_model->update_stats(array('day_hour' => $current_date), $field, 1);
                                /* Update Stats End */
                                $walletDetail = $this->user_model->get_selected_fields(WALLET, array('user_id' => MongoID($last_insert_id)), array('total'));
                                $avail_amount = 0;
                                if (isset($walletDetail->row()->total)) {
                                    $avail_amount = $walletDetail->row()->total;
                                }
                                
                                $wallet_amount = round($avail_amount,2);
                                
                                $returnArr['wallet_amount'] = (string) number_format($wallet_amount,2);

                                /* Sending Mail notification about registration */
                                $this->mail_model->send_user_registration_mail($last_insert_id);
                            } else {
                                $returnArr['message'] = $this->format_string('Registration Failure', 'registration_failed');
                            }
                        } else {
                            $returnArr['message'] = $this->format_string('Invalid referral code', 'invalid_referral_code');
                        }
                    }
                } else {
                    $returnArr['message'] = $this->format_string("Invalid Email address", "invalid_email_address");
                }
            } else {
                $returnArr['message'] = $this->format_string("Some Parameters are missing", "some_parameters_missing");
            }
        } catch (MongoException $ex) {
            $returnArr['response'] = $this->format_string("Error in connection", "error_in_connection");
        }
        $json_encode = json_encode($returnArr, JSON_PRETTY_PRINT);
        echo $this->cleanString($json_encode);
    }

	/**
	*
	* Social Media Login and Register
	*
	**/
    public function social_login() {
        $returnArr['status'] = '0';
        $returnArr['message'] = '';
        try {
            $email = strtolower($this->input->post('email'));
            $user_name = $this->input->post('user_name');
            $country_code = $this->input->post('country_code');
            $phone_number = $this->input->post('phone_number');
            $referal_code = $this->input->post('referal_code');
            $gcm_id = $this->input->post('gcm_id');
            $deviceToken = $this->input->post('deviceToken');
            $media = $this->input->post('media');
            $media_id = $this->input->post('media_id');
            $password = $this->input->post('password');
			$latitude = $this->input->post('lat');
			$longitude = $this->input->post('lon');

            #$password = $this->user_model->get_random_string(6);

            if (is_array($this->input->post())) {
                $chkValues = count(array_filter($this->input->post()));
            } else {
                $chkValues = 0;
            }

            if ($chkValues >= 6) {
                if (valid_email($email)) {
                    $checkEmail = $this->user_model->check_user_exist(array('email' => $email));
                    if ($checkEmail->num_rows() >= 1) {
                        $push_data = array();
                        $key = '';
                        if ($gcm_id != "") {
                            $key = $gcm_id;
                            $push_data = array('push_notification_key.gcm_id' => $gcm_id, 'push_type' => 'ANDROID');
                            $push_update_data = array('push_notification_key.gcm_id' => '', 'push_type' => '');
                        } else if ($deviceToken != "") {
                            $key = $deviceToken;
                            $push_data = array('push_notification_key.ios_token' => $deviceToken, 'push_type' => 'IOS');
                            $push_update_data = array('push_notification_key.ios_token' => '', 'push_type' => '');
                        }
						
						$is_alive_other = "No";
						$checkUser = $this->user_model->get_selected_fields(USERS, array('email' => $email), array('push_type','push_notification_key'));
						if ($checkUser->num_rows() == 1) {
							if (isset($checkUser->row()->push_type)) {
								if ($checkUser->row()->push_type != '') {
									if ($checkUser->row()->push_type == "ANDROID") {
										$existingKey = $checkUser->row()->push_notification_key["gcm_id"];
									}
									if ($checkUser->row()->push_type == "IOS") {
										$existingKey = $checkUser->row()->push_notification_key["ios_token"];
									}
									if ($existingKey != $key) {
										$is_alive_other = "Yes";
									}
								}
							}
						}
						$returnArr['is_alive_other'] = (string) $is_alive_other;
						
						
                        if (!empty($push_data)) {
                            $this->user_model->update_details(USERS, $push_update_data, $push_data);
                            $this->user_model->update_details(USERS, $push_data, array('_id' => MongoID($checkEmail->row()->_id)));
                        }

                        $returnArr['status'] = '1';
                        $returnArr['message'] = $this->format_string('You are Logged In successfully', 'you_logged_in');
                        $userVal = $this->user_model->get_selected_fields(USERS, array('_id' => MongoID($checkEmail->row()->_id)), array('email', 'image', 'user_name', 'country_code', 'phone_number', 'referral_code', 'push_notification_key.gcm_id','unique_code'));
                        if ($userVal->row()->image == '') {
                            $user_image = USER_PROFILE_IMAGE_DEFAULT;
                        } else {
                            $user_image = USER_PROFILE_IMAGE . $userVal->row()->image;
                        }
                        $unique_code = $userVal->row()->unique_code;
                        $returnArr['user_image'] = base_url() . $user_image;
                        $returnArr['user_id'] = (string) $checkEmail->row()->_id;
                        $returnArr['user_name'] = $userVal->row()->user_name;
                        $returnArr['email'] = $userVal->row()->email;
                        $returnArr['country_code'] = $userVal->row()->country_code;
                        $returnArr['phone_number'] = $userVal->row()->phone_number;
                        $returnArr['referal_code'] = $unique_code;
                        $returnArr['sec_key'] = md5((string) $checkEmail->row()->_id);
                        $returnArr['key'] = $key;
                        $location = $this->app_model->find_location(floatval($longitude), floatval($latitude));
					
						if(!empty($location['result'])) {
							$final_cat_list = $location['result'][0]['avail_category'];
						}

						$selected_Category='';
						if(!empty($final_cat_list)) {
							$selected_Category=$final_cat_list[0];
						}
                        $returnArr['currency'] = (string) $this->data['dcurrencyCode'];
                        $returnArr['category'] = (string) $selected_Category;

                        $walletDetail = $this->user_model->get_selected_fields(WALLET, array('user_id' => MongoID($checkEmail->row()->_id)), array('total'));
                        $avail_amount = 0;
                        if (isset($walletDetail->row()->total)) {
                            $avail_amount = $walletDetail->row()->total;
                        }
                        $wallet_amount = round($avail_amount,2);
                        $returnArr['wallet_amount'] = (string) number_format($wallet_amount,2);
                    } else {
                        $cStatus = FALSE;
                        if ($referal_code != '') {
                            $chekCode = $this->user_model->get_selected_fields(USERS, array('unique_code' => $referal_code), array('_id'));
                            if ($chekCode->num_rows() > 0) {
                                $cStatus = TRUE;
                            }
                        } else {
                            $cStatus = TRUE;
                        }
                        if ($cStatus) {
                            $user_image = '';

                            if (isset($_FILES['photo'])) {
                                if ($_FILES['photo']['size'] > 0) {
                                    $data = file_get_contents($_FILES['photo']['tmp_name']);
                                    $image = imagecreatefromstring($data);
                                    $imgname = md5(time() . rand(10, 99999999) . time()) . ".jpg";
                                    $savePath = USER_PROFILE_IMAGE . $imgname;
                                    imagejpeg($image, $savePath, 99);

                                    $option = $this->getImageShape(250, 250, $savePath);
                                    $resizeObj = new Resizeimage($savePath);
                                    $resizeObj->resizeImage(75, 75, $option);
                                    $resizeObj->saveImage(USER_PROFILE_THUMB . $imgname, 100);

                                    $this->ImageCompress(USER_PROFILE_IMAGE . $imgname);
                                    $this->ImageCompress(USER_PROFILE_THUMB . $imgname);
                                    $user_image = $imgname;
                                }
                            }

                            $verification_code = $this->get_rand_str('10');
                            $unique_code = $this->app_model->get_unique_id($user_name);
                            $user_data = array('user_name' => $user_name,
                                'user_type' => $media,
                                'media_id' => (string) $media_id,
                                'unique_code' => $unique_code,
                                'email' => $email,
                                'password' => md5($password),
                                'image' => $user_image,
                                'status' => 'Active',
                                'country_code' => $country_code,
                                'phone_number' => $phone_number,
                                'referral_code' => $referal_code,
                                'verification_code' => array("email" => $verification_code),
                                'created' => date("Y-m-d H:i:s")
                            );
                            $this->user_model->insert_user($user_data);
                            $last_insert_id = $this->mongo_db->insert_id();
                            if ($last_insert_id != '') {
                                $push_data = array();
                                $key = '';
                                if ($gcm_id != "") {
                                    $key = $gcm_id;
                                    $push_data = array('push_notification_key.gcm_id' => $gcm_id, 'push_type' => 'ANDROID');
                                    $push_update_data = array('push_notification_key.gcm_id' => '', 'push_type' => '');
                                } else if ($deviceToken != "") {
                                    $key = $deviceToken;
                                    $push_data = array('push_notification_key.ios_token' => $deviceToken, 'push_type' => 'IOS');
                                    $push_update_data = array('push_notification_key.ios_token' => '', 'push_type' => '');
                                }
                                if (!empty($push_data)) {
                                    $this->user_model->update_details(USERS, $push_update_data, $push_data);
                                    $this->user_model->update_details(USERS, $push_data, array('_id' => MongoID($last_insert_id)));
                                }

                                /* Insert Referal and wallet collection */
                                $this->user_model->simple_insert(REFER_HISTORY, array('user_id' => MongoID($last_insert_id)));
                                $this->user_model->simple_insert(WALLET, array('user_id' => MongoID($last_insert_id), 'total' => floatval(0)));
                                /* Update the welcome amount to the registered user wallet */
                                if($this->config->item('welcome_amount') > 0){
                                    $trans_id = time() . rand(0, 2578);
                                    $initialAmt = array('type' => 'CREDIT',
                                        'credit_type' => 'welcome',
                                        'ref_id' => '',
                                        'trans_amount' => floatval($this->config->item('welcome_amount')),
                                        'avail_amount' => floatval($this->config->item('welcome_amount')),
                                        'trans_date' => MongoDATE(time()),
                                        'trans_id' => $trans_id
                                    );
                                    $this->user_model->simple_push(WALLET, array('user_id' => MongoID($last_insert_id)), array('transactions' => $initialAmt));
                                    $this->user_model->update_wallet((string) $last_insert_id, 'CREDIT', floatval($this->config->item('welcome_amount')));
                                }
                                /* Update the referer history */
                                if ($referal_code != '' && $this->config->item('referal_amount') > 0) {
                                    $refererVal = $this->user_model->get_selected_fields(USERS, array('unique_code' => $referal_code), array('email','referral_count'));
                                    if ($refererVal->num_rows() > 0) {
									
										$referral_count = 1;
										if(isset($refererVal->row()->referral_count)) $referral_count = $refererVal->row()->referral_count+1;
										$userCond = array('_id' => MongoID($refererVal->row()->_id));
										$this->user_model->update_details(USERS, array('referral_count' => floatval($referral_count)), $userCond);
									
                                        $ref_status = 'true';
                                        $amount_earns = floatval($this->config->item('referal_amount'));
                                        if ($this->config->item('referal_credit') == 'on_first_ride') {
                                            $ref_status = 'false';
                                            #$amount_earns = floatval(0);
                                        }
                                        $refArr = array('reference_id' => (string) $last_insert_id,
                                            'reference_mail' => (string) $email,
                                            'amount_earns' => $amount_earns,
                                            'reference_date' => MongoDATE(time()),
                                            'used' => $ref_status
                                        );
                                        $this->user_model->simple_push(REFER_HISTORY, array('user_id' => MongoID($refererVal->row()->_id)), array('history' => $refArr));
                                        if ($this->config->item('referal_credit') == 'instant') {
                                            $this->user_model->update_wallet((string) $refererVal->row()->_id, 'CREDIT', floatval($this->config->item('referal_amount')));
                                            $walletDetail = $this->user_model->get_selected_fields(WALLET, array('user_id' => MongoID($refererVal->row()->_id)), array('total'));
                                            $avail_amount = 0;
                                            if (isset($walletDetail->row()->total)) {
                                                $avail_amount = $walletDetail->row()->total;
                                            }
                                            $walletArr = array('type' => 'CREDIT',
                                                'credit_type' => 'referral',
                                                'ref_id' => (string) $last_insert_id,
                                                'trans_amount' => floatval($this->config->item('referal_amount')),
                                                'avail_amount' => floatval($avail_amount),
                                                'trans_date' => MongoDATE(time())
                                            );
                                            $this->user_model->simple_push(WALLET, array('user_id' => MongoID($refererVal->row()->_id)), array('transactions' => $walletArr));
                                        }
                                    }
                                }

                                /* Update Stats Starts */
                                $current_date = MongoDATE(strtotime(date("Y-m-d 00:00:00")));
                                $field = array('user.hour_' . date('H') => 1, 'user.count' => 1);
                                $this->app_model->update_stats(array('day_hour' => $current_date), $field, 1);
                                /* Update Stats End */


                                $walletDetail = $this->user_model->get_selected_fields(WALLET, array('user_id' => MongoID($last_insert_id)), array('total'));
                                $avail_amount = 0;
                                if (isset($walletDetail->row()->total)) {
                                    $avail_amount = $walletDetail->row()->total;
                                }
                                
                                $wallet_amount = round($avail_amount,2);
                                
                                $returnArr['wallet_amount'] = (string) number_format($wallet_amount,2);

                                /* Sending Mail notification about registration */
                                $this->mail_model->send_user_registration_mail($last_insert_id);

                                $returnArr['message'] = $this->format_string('Successfully registered', 'successfully_registered');
                                $userVal = $this->user_model->get_selected_fields(USERS, array('_id' => MongoID($last_insert_id)), array('image'));
                                if ($userVal->row()->image == '') {
                                    $user_image = USER_PROFILE_IMAGE_DEFAULT;
                                } else {
                                    $user_image = USER_PROFILE_IMAGE . $userVal->row()->image;
                                }
                                $returnArr['user_image'] = base_url() . $user_image;
                                $returnArr['user_id'] = (string) $last_insert_id;
                                $returnArr['user_name'] = $user_name;
                                $returnArr['email'] = $email;
                                $returnArr['country_code'] = $country_code;
                                $returnArr['phone_number'] = $phone_number;
                                $returnArr['referal_code'] = $unique_code;
                                $returnArr['key'] = $key;
                                $returnArr['status'] = '1';

                                $fields = array(
                                    'username' => $last_insert_id,
                                    'password' => md5($last_insert_id)
                                );
                                $url = $this->data['soc_url'] . 'create-user.php';
                                $this->load->library('curl');
                                $output = $this->curl->simple_post($url, $fields);

                                $categoryResult = $this->app_model->get_selected_fields(CATEGORY, array('status' => 'Active'), array('name'));
                                $category = '';
                                if ($categoryResult->num_rows() > 0) {
                                    $category = $categoryResult->row()->_id;
                                }
                                $returnArr['currency'] = (string) $this->data['dcurrencyCode'];
                                $returnArr['category'] = (string) $category;
                            } else {
                                $returnArr['message'] = $this->format_string('Registration Failure', 'registration_failed');
                            }
                        } else {
                            $returnArr['message'] = $this->format_string('Invalid referral code', 'invalid_referral_code');
                        }
                    }
                } else {
                    $returnArr['message'] = $this->format_string("Invalid Email address", "invalid_email_address");
                }
            } else {
                $returnArr['message'] = $this->format_string("Some Parameters are missing", "some_parameters_missing");
            }
        } catch (MongoException $ex) {
            $returnArr['response'] = $this->format_string("Error in connection", "error_in_connection");
        }
        $json_encode = json_encode($returnArr, JSON_PRETTY_PRINT);
        echo $this->cleanString($json_encode);
    }

	/**
	*
	* Login User 
	*
	**/
    public function login_user() {
        $returnArr['status'] = '0';
        $returnArr['message'] = '';
        try {
            $email = strtolower($this->input->post('email'));
            $password = $this->input->post('password');
            $gcm_id = $this->input->post('gcm_id');
            $deviceToken = $this->input->post('deviceToken');
			$latitude = $this->input->post('lat');
			$longitude = $this->input->post('lon');

            if (is_array($this->input->post())) {
                $chkValues = count(array_filter($this->input->post()));
            } else {
                $chkValues = 0;
            }

            if ($chkValues >= 2) {
                if (valid_email($email)) {
					$checkAccount=$this->user_model->get_selected_fields(USERS, array('email' =>$email),array('email'));
					if($checkAccount->num_rows() == 1)  {
						$checkUser = $this->user_model->get_selected_fields(USERS, array('email' => $email, 'password' => md5($password)), array('email', 'user_name', 'phone_number', 'status','push_type','push_notification_key'));
						if ($checkUser->num_rows() == 1) {
							if ($checkUser->row()->status == "Active") {
								$push_data = array();
								$key = '';
								if ($gcm_id != "") {
									$key = $gcm_id;
									$push_data = array('push_notification_key.gcm_id' => $gcm_id, 'push_type' => 'ANDROID');
									$push_update_data = array('push_notification_key.gcm_id' => '', 'push_type' => '');
								}
								if ($deviceToken != "") {
									$key = $deviceToken;
									$push_data = array('push_notification_key.ios_token' => $deviceToken, 'push_type' => 'IOS');
									$push_update_data = array('push_notification_key.ios_token' => '', 'push_type' => '');
								}
								/* if($key==""){
								  $this->user_model->update_details(USERS,array('push_type'=>''),array('_id'=>MongoID($checkUser->row()->_id)));
								  } */
								  
								
								$is_alive_other = "No";
								if (isset($checkUser->row()->push_type)) {
									if ($checkUser->row()->push_type != '') {
										if ($checkUser->row()->push_type == "ANDROID") {
											$existingKey = $checkUser->row()->push_notification_key["gcm_id"];
										}
										if ($checkUser->row()->push_type == "IOS") {
											$existingKey = $checkUser->row()->push_notification_key["ios_token"];
										}
										if ($existingKey != $key) {
											$is_alive_other = "Yes";
										}
									}
								}
								$returnArr['is_alive_other'] = (string) $is_alive_other;
								
								
								if (!empty($push_data)) {
									$this->user_model->update_details(USERS, $push_update_data, $push_data);
									$this->user_model->update_details(USERS, $push_data, array('_id' => MongoID($checkUser->row()->_id)));
								}


								$returnArr['status'] = '1';
								$returnArr['message'] = $this->format_string('You are Logged In successfully', 'you_logged_in');
								$userVal = $this->user_model->get_selected_fields(USERS, array('_id' => MongoID($checkUser->row()->_id)), array('email', 'image', 'user_name', 'country_code', 'phone_number', 'referral_code', 'push_notification_key.gcm_id', 'password'));
								if ($userVal->row()->image == '') {
									$user_image = USER_PROFILE_IMAGE_DEFAULT;
								} else {
									$user_image = USER_PROFILE_IMAGE . $userVal->row()->image;
								}
								$returnArr['user_image'] = base_url() . $user_image;
								$returnArr['user_id'] = (string) $checkUser->row()->_id;
								$returnArr['user_name'] = $userVal->row()->user_name;
								$returnArr['email'] = $userVal->row()->email;
								$returnArr['country_code'] = $userVal->row()->country_code;
								$returnArr['phone_number'] = $userVal->row()->phone_number;
								$returnArr['referal_code'] = $userVal->row()->referral_code;
								$returnArr['sec_key'] = md5((string) $checkUser->row()->_id);
								$returnArr['key'] = $key;
								
								if(isset($checkUser->row()->lang_code)){
										$returnArr['lang_code'] = $checkUser->row()->lang_code;
								}else{
										$returnArr['lang_code'] = $this->temp_lang;
								}	

								$walletDetail = $this->user_model->get_selected_fields(WALLET, array('user_id' => MongoID($checkUser->row()->_id)), array('total'));
								$avail_amount = 0;
								if (isset($walletDetail->row()->total)) {
									$avail_amount = $walletDetail->row()->total;
								}
								
                                $wallet_amount = round($avail_amount,2);
                                $returnArr['wallet_amount'] = (string) number_format($wallet_amount,2);
                                
								$returnArr['currency'] = (string) $this->data['dcurrencyCode'];
								$location = $this->app_model->find_location(floatval($longitude), floatval($latitude));
					
								if(!empty($location['result'])) {
									$final_cat_list = $location['result'][0]['avail_category'];
								}

								$selected_Category='';
								if(!empty($final_cat_list)) {
									$selected_Category=$final_cat_list[0];
								}
								
								$returnArr['category'] = (string) $selected_Category;
							} else {
								if ($checkUser->row()->status == "Deleted") {
									$returnArr['message'] = $this->format_string("Your account is currently unavailable", "account_currently_unavailbale");
								} else {
									$returnArr['message'] = $this->format_string("Your account has been inactivated", "your_account_inactivated");
								}
							}
						} else {
							$returnArr['message'] = $this->format_string('Please check the email and password and try again', 'please_check_email_and_password');
						}
				 }else {
						$returnArr['message'] = $this->format_string('Your account does not exist', 'account_not_exists');
				 }
                } else {
                    $returnArr['message'] = $this->format_string("Invalid Email address", "invalid_email_address");
                }
            } else {
                $returnArr['message'] = $this->format_string("Some Parameters are missing", "some_parameters_missing");
            }
        } catch (MongoException $ex) {
            $returnArr['response'] = $this->format_string("Error in connection", "error_in_connection");
        }
        $json_encode = json_encode($returnArr, JSON_PRETTY_PRINT);
        echo $this->cleanString($json_encode);
    }

	/**
	*
	* Logout Driver 
	*
	**/
    public function logout_user() {
        $returnArr['status'] = '0';
        $returnArr['response'] = '';
        try {
            $user_id = $this->input->post('user_id');
            $device = $this->input->post('device');

            if ($user_id != '' && $device != '') {
                $condition = array('_id' => MongoID($user_id));
                $checkUser = $this->app_model->get_selected_fields(USERS, $condition, array('push_notification_key', 'push_type'));
                if ($checkUser->num_rows() == 1) {
                    if ($device == 'IOS' || $device == 'ANDROID') {
                        if ($device == 'ANDROID') {
                            $push_update_data = array('push_notification_key.gcm_id' => '', 'push_type' => '');
                        } else if ($device == 'IOS') {
                            $push_update_data = array('push_notification_key.ios_token' => '', 'push_type' => '');
                        } else {
                            $push_update_data = array('push_notification_key.gcm_id' => '', 'push_notification_key.ios_token' => '', 'push_type' => '');
                        }
                        $this->app_model->update_details(USERS, $push_update_data, $condition);
                        $returnArr['status'] = '1';
                        $returnArr['response'] = $this->format_string("You are logged out", "you_are_logged_out");
                    } else {
                        $returnArr['response'] = $this->format_string('Invalid inputs', 'invalid_input');
                    }
                } else {
                    $returnArr['response'] = $this->format_string("Invalid User", "invalid_user");
                }
            } else {
                $returnArr['response'] = $this->format_string("Some Parameters are missing", "some_parameters_missing");
            }
        } catch (MongoException $ex) {
            $returnArr['response'] = $this->format_string("Error in connection", "error_in_connection");
        }
        $json_encode = json_encode($returnArr, JSON_PRETTY_PRINT);
        echo $this->cleanString($json_encode);
    }

	/**
	*
	* Forgot Password
	*
	**/
    public function findAccount() {
        $returnArr['status'] = '0';
        $returnArr['message'] = '';
        try {
            $email = $this->input->post('email');
            if (is_array($this->input->post())) {
                $chkValues = count(array_filter($this->input->post()));
            } else {
                $chkValues = 0;
            }

            if ($chkValues >= 1) {
                if (valid_email($email)) {
                    $checkUser = $this->user_model->get_selected_fields(USERS, array('email' => $email), array('email', 'user_name', 'phone_number'));
                    if ($checkUser->num_rows() == 1) {
                        $verification_code = $this->get_rand_str('10');
                        $user_data = array('verification_code.forgot' => $verification_code);
                        $this->user_model->update_details(USERS, $user_data, array('email' => $email));
                        $returnArr['status'] = '1';
                        $returnArr['message'] = $this->format_string('Kindly check your email', 'check_your_email');
                    } else {
                        $returnArr['message'] = $this->format_string('Please enter the correct email and try again', 'enter_correct_email');
                    }
                } else {
                    $returnArr['message'] = $this->format_string("Invalid Email address", "invalid_email_address");
                }
            } else {
                $returnArr['message'] = $this->format_string("Some Parameters are missing", "some_parameters_missing");
            }
        } catch (MongoException $ex) {
            $returnArr['response'] = $this->format_string("Error in connection", "error_in_connection");
        }
        $json_encode = json_encode($returnArr, JSON_PRETTY_PRINT);
        echo $this->cleanString($json_encode);
    }

	/**
	*
	* Update user Location
	*
	**/
    public function update_user_location() {
        $returnArr['status'] = '0';
        $returnArr['message'] = '';
        try {
            $user_id = $this->input->post('user_id');
            $latitude = $this->input->post('latitude');
            $longitude = $this->input->post('longitude');

            if (is_array($this->input->post())) {
                $chkValues = count(array_filter($this->input->post()));
            } else {
                $chkValues = 0;
            }

            if ($chkValues >= 3) {
                $checkUser = $this->user_model->get_selected_fields(USERS, array('_id' => MongoID($user_id)), array('email'));
                if ($checkUser->num_rows() == 1) {
                    $geo_data = array('geo' => array(floatval($longitude), floatval($latitude)));
                    $checkGeo = $this->user_model->get_selected_fields(USER_LOCATION, array('user_id' => MongoID($user_id)), array('user_id'));
                    $geo_data_user = array('loc' => array('lon' => floatval($longitude), 'lat' => floatval($latitude)),'last_active_time'=>MongoDATE(time()));
                    $this->user_model->update_details(USERS, $geo_data_user, array('_id' => MongoID($user_id)));
                    if ($checkGeo->num_rows() > 0) {
                        $this->user_model->update_details(USER_LOCATION, $geo_data, array('user_id' => MongoID($user_id)));
                    } else {
                        $newGeo = array('user_id' => MongoID($user_id), 'geo' => array(floatval($longitude), floatval($latitude)));
                        $this->user_model->simple_insert(USER_LOCATION, $newGeo);
                    }
                    $walletDetail = $this->user_model->get_selected_fields(WALLET, array('user_id' => MongoID($user_id)), array('total'));
                    $avail_amount = 0;
                    if (isset($walletDetail->row()->total)) {
                        $avail_amount = $walletDetail->row()->total;
                    }

                    $category_id = '';
                    $location = $this->app_model->find_location(floatval($longitude), floatval($latitude));
                    if (!empty($location['result'])) {
                        if (array_key_exists('avail_category', $location['result'][0]) && array_key_exists('fare', $location['result'][0])) {
                            if (!empty($location['result'][0]['avail_category']) && !empty($location['result'][0]['fare'])) {
								$cat_avail = $location['result'][0]['avail_category'];
								$cat_fare = array_keys($location['result'][0]['fare']);
								$final_cat_list = array_intersect($cat_avail,$cat_fare);
								$final_cat_list = array_values($final_cat_list);
                                $category_id = $final_cat_list[0];
								#$category_id = $location['result'][0]['avail_category'][0];
                            }
                        }
                    }					
					$returnArr['ongoing_trips'] = 'No';
					$ongoing_trips = $this->app_model->get_ongoing_rides($user_id);
					if($ongoing_trips>0){
						$returnArr['ongoing_trips'] = 'Yes';
					}
					
					
                    $returnArr['category_id'] = (string) $category_id;

                    $returnArr['status'] = '1';
                    $returnArr['message'] = $this->format_string('Geo Location Updated', 'geo_location_updated');
                    $returnArr['currency'] = (string) $this->data['dcurrencyCode'];
                    
                    $wallet_amount = round($avail_amount,2);
                    $returnArr['wallet_amount'] = (string) number_format($wallet_amount,2);
                    
                } else {
                    $returnArr['message'] = $this->format_string("Invalid User", "invalid_user");
                }
            } else {
                $returnArr['message'] = $this->format_string("Some Parameters Missing", "some_parameters_missing");
            }
        } catch (MongoException $ex) {
            $returnArr['response'] = $this->format_string("Error in connection", "error_in_connection");
        }
        $json_encode = json_encode($returnArr, JSON_PRETTY_PRINT);
        echo $this->cleanString($json_encode);
    }

	/**
	*
	* This Function return the location list
	*
	**/
    public function get_location_list() {
        $returnArr['status'] = '0';
        $returnArr['response'] = '';
        try {
            $locationsVal = $this->app_model->get_selected_fields(LOCATIONS, array('status' => 'Active'), array('city', 'avail_category','fare'), array('city' => 1));
            if ($locationsVal->num_rows() > 0) {
                $locationsArr = array();
                foreach ($locationsVal->result() as $row) {
					$final_cat_list = array();
					if (isset($row->avail_category) && isset($row->fare)) {
						if (!empty($row->avail_category) && !empty($row->fare)) {
							$cat_avail = $row->avail_category;
							$cat_fare = array_keys($row->fare);
							$final_cat_list = array_intersect($cat_avail,$cat_fare);
						}
					}
                    $categoryResult = $this->app_model->get_available_category(CATEGORY, $final_cat_list);
                    $categoryArr = array();
                    if ($categoryResult->num_rows() > 0) {
                        foreach ($categoryResult->result() as $row1) {
                            $categoryArr[] = array('id' => (string) $row1->_id,
                                'category' => (string) $row1->name
                            );
                        }
                    }
					if(!empty($categoryArr)){
						$locationsArr[] = array('id' => (string) $row->_id,
							'city' => (string) $row->city
						);
					}
                }
                if (empty($locationsArr)) {
                    $locationsArr = json_decode("{}");
                }

                $returnArr['status'] = '1';
                $returnArr['response'] = array('locations' => $locationsArr);
            } else {
                $returnArr['response'] = $this->format_string("Some Parameters Missing", "some_parameters_missing");
            }
        } catch (MongoException $ex) {
            $returnArr['response'] = $this->format_string("Error in connection", "error_in_connection");
        }
        $json_encode = json_encode($returnArr, JSON_PRETTY_PRINT);
        echo $this->cleanString($json_encode);
    }

	/**
	*
	* This Function return the category list
	*
	**/
    public function get_category_list() {
        $returnArr['status'] = '0';
        $returnArr['response'] = '';
        try {
            $location_id = (string) $this->input->post('location_id');

            if ($location_id != '') {
                $locationsVal = $this->app_model->get_selected_fields(LOCATIONS, array('_id' => MongoID($location_id)), array('city', 'avail_category','fare'));
                if ($locationsVal->num_rows() > 0) {
					$final_cat_list = array();
					if (isset($locationsVal->row()->avail_category) && isset($locationsVal->row()->fare)) {
						if (!empty($locationsVal->row()->avail_category) && !empty($locationsVal->row()->fare)) {
							$cat_avail = $locationsVal->row()->avail_category;
							$cat_fare = array_keys($locationsVal->row()->fare);
							$final_cat_list = array_intersect($cat_avail,$cat_fare);
						}
					}
                    $categoryResult = $this->app_model->get_available_category(CATEGORY, $final_cat_list);
                    $categoryArr = array();
                    if ($categoryResult->num_rows() > 0) {
                        foreach ($categoryResult->result() as $row) {
							$cat_name = $row->name;
							if(isset($row->name_languages)){
								$langKey = $this->data['sms_lang_code'];
								$arrVal = $row->name_languages;
								if(array_key_exists($langKey,$arrVal)){
									if($row->name_languages[$langKey]!=""){
										$cat_name = $row->name_languages[$langKey];
									}
								}
							}
                            $categoryArr[] = array('id' => (string) $row->_id,
                                'category' => (string) $cat_name
                            );
                        }
                    }
					if (empty($categoryArr)) {
                       $returnArr['response']  = $this->format_string("No categories available in this location","ratecard_no_category");
                    } else {
						$returnArr['status'] = '1';
						$returnArr['response'] = array('category' => $categoryArr);
					}
                } else {
                    $returnArr['response'] = $this->format_string("Records not available", "no_records_found");
                }
            } else {
                $returnArr['response'] = $this->format_string("Some Parameters Missing", "some_parameters_missing");
            }
        } catch (MongoException $ex) {
            $returnArr['response'] = $this->format_string("Error in connection", "error_in_connection");
        }
        $json_encode = json_encode($returnArr, JSON_PRETTY_PRINT);
        echo $this->cleanString($json_encode);
    }

	/**
	*
	* This Function return the rate card
	*
	**/
    public function get_rate_card() {
        $returnArr['status'] = '0';
        $returnArr['response'] = '';
        try {
            $location_id = (string) $this->input->post('location_id');
            $category_id = (string) $this->input->post('category_id');
			
			
            $mins = $this->format_string('mins', 'mins');
            $per_min = $this->format_string('per min', 'per_min');
			$per = $this->format_string('per', 'per');
			
            $first = $this->format_string('First', 'first');
            $after = $this->format_string('After', 'after');
            $service_tax = $this->format_string('Service Tax', 'service_tax',FALSE);
            $night_time_charges = $this->format_string('Night time charges', 'night_time_charges');
            $service_tax_payable = $this->format_string('Service tax is payable in addition to ride fare.', 'service_tax_payable');
            $night_time_charges_may_applicable = $this->format_string('Night time charges may be applicable during the late night hours and will be conveyed during the booking.', 'night_time_charges_may_applicable');
            $peak_time_charges_may_applicable = $this->format_string('Peak time charges may be applicable during high demand hours and will be conveyed during the booking.', 'peak_time_charges_may_applicable');
            $sur_charges_may_applicable_append = $this->format_string('This enables us to make more cabs available to you.', 'sur_charges_may_applicable_append');
            $peak_time_charges = $this->format_string('Peak time charges', 'peak_time_charges');
            $mins = $this->format_string('min', 'min_short');
			$mins_short = $this->format_string('mins', 'mins_short');
            $mins_ride_times_free = ' '.$this->format_string(' ride time is FREE! Wait time is chargeable.', 'mins_ride_times_free_text');
            $ride_time_charges = $this->format_string('Ride time charges', 'ride_time_charges',FALSE);

            if ($location_id != '') {
                $locationsVal = $this->app_model->get_selected_fields(LOCATIONS, array('_id' => MongoID($location_id)), array('currency', 'fare', 'peak_time', 'night_charge', 'service_tax','distance_unit'),array('city' => 1));
				
				$distance_unit = $this->data['d_distance_unit'];
				if(isset($locationsVal->row()->distance_unit)){
					if($locationsVal->row()->distance_unit != ''){
						$distance_unit = $locationsVal->row()->distance_unit;
					} 
				}
				if($distance_unit == 'km'){
					$disp_distance_unit = $this->format_string('km', 'km');
				}else if($distance_unit == 'mi'){
					$disp_distance_unit = $this->format_string('mi', 'mi');
				}
				
				
                if ($locationsVal->num_rows() > 0) {
                    $ratecardArr = array();
                    if (isset($locationsVal->row()->fare[$category_id])) {
                        $standard_rate = array(array('title' => $first . ' ' . $locationsVal->row()->fare[$category_id]['min_km'] . ' ' . $disp_distance_unit,
                                'fare' => (string)$locationsVal->row()->fare[$category_id]['min_fare'],
                                'sub_title' => '',
								'has_unit' => ""
                            ),
                            array('title' => $after . ' ' . $locationsVal->row()->fare[$category_id]['min_km'] . ' ' . $disp_distance_unit,
								'fare' => (string)$locationsVal->row()->fare[$category_id]['per_km'].' '.$per.' '.$disp_distance_unit,
                                'sub_title' => '',
								'has_unit' => ""
                            )
                        );
                        if($locationsVal->row()->fare[$category_id]['min_time'] >1){
								$wait_unit = $mins_short;
						}else{
								$wait_unit = $mins;
					     }
                        $extra_charges = array(array('title' => $ride_time_charges,
                                'fare' => (string)$locationsVal->row()->fare[$category_id]['per_minute'] . ' ' . $per_min,
                                'sub_title' => $first . ' ' . $locationsVal->row()->fare[$category_id]['min_time'] . ' ' . $wait_unit.''.$mins_ride_times_free,
								'has_unit' => ""
                            )
                        );
                        if (isset($locationsVal->row()->peak_time)) {
                            if ($locationsVal->row()->peak_time == 'Yes') {
                                $extra_charges[] = array('title' => $peak_time_charges,
                                    'fare' => '',
                                    'sub_title' => $peak_time_charges_may_applicable.' '.$sur_charges_may_applicable_append,
									'has_unit' => ""
                                );
                            }
                        }
                        if (isset($locationsVal->row()->night_charge)) {
                            if ($locationsVal->row()->night_charge == 'Yes') {
                                $extra_charges[] = array('title' => $night_time_charges,
                                    'fare' => '',
                                    'sub_title' => $night_time_charges_may_applicable.' '.$sur_charges_may_applicable_append,
									'has_unit' => ""
                                );
                            }
                        }
                        if (isset($locationsVal->row()->service_tax)) {
                            if ($locationsVal->row()->service_tax > 0) {
                                $extra_charges[] = array('title' => $service_tax,
                                    'fare' => (string)$locationsVal->row()->service_tax,
                                    'sub_title' => $service_tax_payable,
                                    'has_unit' => "%"
                                );
                            }
                        }
                        $ratecardArr = array('currency' => $this->data['dcurrencyCode'],
                            'standard_rate' => $standard_rate,
                            'extra_charges' => $extra_charges,
                        );
                    }

					
					if (empty($ratecardArr)) {
                       $returnArr['response']  = $this->format_string("Fare details are not updated for this category","fare_details_not_updated");
                    } else {
						$returnArr['status'] = '1';
						$returnArr['response'] = array('ratecard' => $ratecardArr);
					}
					
                } else {
                    $returnArr['response'] = $this->format_string("Records not available", "no_records_found");
                }
            } else {
                $returnArr['response'] = $this->format_string("Some Parameters Missing", "some_parameters_missing");
            }
        } catch (MongoException $ex) {
            $returnArr['response'] = $this->format_string("Error in connection", "error_in_connection");
        }
        $json_encode = json_encode($returnArr, JSON_PRETTY_PRINT);
        echo $this->cleanString($json_encode);
    }

	/**
	*
	* This Function return the invites page info
	*
	**/
    public function get_invites() {
        $returnArr['status'] = '0';
        $returnArr['response'] = '';
        try {
            $user_id = (string) $this->input->post('user_id');

            if ($user_id != '') {
                $userVal = $this->app_model->get_selected_fields(USERS, array('_id' => MongoID($user_id)), array('city', 'unique_code'));
                if ($userVal->num_rows() > 0) {
                    if ($this->config->item('referal_credit') == 'instant') {
                        $your_earn = 'Friend Joins';
						$your_earn_condition = "instant";
                    } else if ($this->config->item('referal_credit') == 'on_first_ride') {
                        $your_earn = 'Friend Rides';
						$your_earn_condition = "on_first_ride";
                    }
					
					$welcome_amount = floatval($this->config->item('welcome_amount'));
					
					$subject = $this->config->item('email_title')." ".$this->format_string('app invitation','app_invitation');
					if($welcome_amount>0){
						$message = $this->format_string("I have an {SITENAME} coupon worth {CURRENCY_CODE}{WELCOME_AMOUNT} for you. Sign up with my referral code {UNIQUE_CODE} for more info visit :", "share_and_earnings_describtion",FALSE).' '.base_url();
											
						$message = str_replace('{SITENAME}',$this->config->item('email_title'),$message);
						$message = str_replace('{WELCOME_AMOUNT}',$welcome_amount,$message);
						$message = str_replace('{UNIQUE_CODE}',$userVal->row()->unique_code,$message);
						$message = str_replace('{CURRENCY_CODE}',$this->data['dcurrencyCode'],$message);
					}else{
						$message = $this->format_string("I have been using the {SITENAME} app to book cabs quickly and travel safely around the city. I want you to try this app and enjoy the  {SITENAME} experience. Sign up with my referral code {UNIQUE_CODE}. for more info visit :", "share_describtion",FALSE).' '.base_url();
											
						$message = str_replace('{SITENAME}',$this->config->item('email_title'),$message);
						$message = str_replace('{UNIQUE_CODE}',$userVal->row()->unique_code,$message);
					}
					
                    $detailsArr = array('friends_earn_amount' => floatval($welcome_amount),
                        'your_earn' => $your_earn,
                        'your_earn_condition' => $your_earn_condition,
                        'your_earn_amount' => floatval($this->config->item('referal_amount')),
                        'referral_code' => $userVal->row()->unique_code,
                        'currency' => $this->data['dcurrencyCode'],
                        'subject' => (string)$subject,
                        'message' => (string)$message,
                        'url'=>base_url().'rider/signup/'.$this->app_language.'/'.time().'?ref=' . base64_encode($userVal->row()->unique_code)
                    );
                    if (empty($detailsArr)) {
                        $detailsArr = json_decode("{}");
                    }
                    $returnArr['status'] = '1';
                    $returnArr['response'] = array('details' => $detailsArr);
                } else {
                    $returnArr['response'] = $this->format_string("Invalid User", "invalid_user");
                }
            } else {
                $returnArr['response'] = $this->format_string("Some Parameters Missing", "some_parameters_missing");
            }
        } catch (MongoException $ex) {
            $returnArr['response'] = $this->format_string("Error in connection", "error_in_connection");
        }
        $json_encode = json_encode($returnArr, JSON_PRETTY_PRINT);
        echo $this->cleanString($json_encode);
    }

	/**
	*
	* This Function return the invites page info
	*
	**/
    public function get_earnings_list() {
        $returnArr['status'] = '0';
        $returnArr['response'] = '';
        try {
            $user_id = (string) $this->input->post('user_id');

            if ($user_id != '') {
                $userVal = $this->app_model->get_selected_fields(USERS, array('_id' => MongoID($user_id)), array('city', 'unique_code'));
                if ($userVal->num_rows() > 0) {
                    $earningsArr = array();
                    $wallet_amount = 0;
                    $walletAmt = $this->app_model->get_selected_fields(WALLET, array('user_id' => MongoID($user_id)), array('total'));
                    if ($walletAmt->num_rows() > 0) {
                        if (isset($walletAmt->row()->total)) {
                            $wallet_amount = $walletAmt->row()->total;
                        }
                    }
                    $referralArr = $this->app_model->get_all_details(REFER_HISTORY, array('user_id' => MongoID($user_id)));
                    if ($referralArr->num_rows() > 0) {
                        if (isset($referralArr->row()->history)) {
                            foreach ($referralArr->row()->history as $earn) {
                                if ($earn['used'] == 'true') {
                                    $amount = $earn['amount_earns'];
                                } else if ($earn['used'] == 'false') {
                                    $amount = 'joined';
                                }
                                $earningsArr = array('emil' => $earn['reference_mail'],
                                    'amount' => $amount
                                );
                            }
                        }
                    }
                    if (empty($earningsArr)) {
                        $earningsArr = json_decode("{}");
                    }
                    if (empty($wallet_amount)) {
                        $wallet_amount = json_decode("{}");
                    }
                    $returnArr['status'] = '1';
                    $returnArr['response'] = array('currency' => $this->data['dcurrencyCode'], 'wallet_amount' => round($wallet_amount,2), 'earnings' => $earningsArr);
                } else {
                    $returnArr['response'] = $this->format_string("Invalid User", "invalid_user");
                }
            } else {
                $returnArr['response'] = $this->format_string("Some Parameters Missing", "some_parameters_missing");
            }
        } catch (MongoException $ex) {
            $returnArr['response'] = $this->format_string("Error in connection", "error_in_connection");
        }
        $json_encode = json_encode($returnArr, JSON_PRETTY_PRINT);
        echo $this->cleanString($json_encode);
    }

	/**
	*
	* This Function return the money/wallet page details
	*
	**/
    public function get_money_page() {
        $returnArr['status'] = '0';
        $returnArr['response'] = '';
        try {
            $user_id = (string) $this->input->post('user_id');

            if ($user_id != '') {
                $userVal = $this->app_model->get_selected_fields(USERS, array('_id' => MongoID($user_id)), array('city', 'unique_code', 'stripe_customer_id'));
                if ($userVal->num_rows() > 0) {
                    $current_balance = 0;
                    $walletAmt = $this->app_model->get_selected_fields(WALLET, array('user_id' => MongoID($user_id)), array('total'));
                    if ($walletAmt->num_rows() > 0) {
                        if (isset($walletAmt->row()->total)) {
                            $current_balance = $walletAmt->row()->total;
                        }
                    }
                    $wallet_min_amount = floatval($this->config->item('wal_recharge_min_amount'));
                    $wallet_max_amount = floatval($this->config->item('wal_recharge_max_amount'));
                    $wallet_middle_amount = floatval(($this->config->item('wal_recharge_max_amount') + $this->config->item('wal_recharge_min_amount')) / 2);


                    if ($wallet_max_amount != '' && $wallet_max_amount != '') {
                        $wallet_money = array('min_amount' => (string)number_format($wallet_min_amount,2),
                                              'middle_amount' => (string)number_format(round($wallet_middle_amount),2),
                                              'max_amount' => (string) number_format($wallet_max_amount,2)
                                        );
                    } else {
                        $wallet_money = array();
                    }

                    $stripe_customer_id = '';
                    if (isset($userVal->row()->stripe_customer_id)) {
                        $stripe_customer_id = $userVal->row()->stripe_customer_id;
                    }

                    $auto_charge_status = '0';
                    if ($this->data['auto_charge'] == 'Yes' && $stripe_customer_id != '') {
                        $auto_charge_status = '1';
                    }

                    $returnArr['auto_charge_status'] = $auto_charge_status;
                    $current_balance = round($current_balance,2);
                    $returnArr['status'] = '1';
                    $returnArr['response'] = array('currency' => $this->data['dcurrencyCode'],
                                                   'current_balance' => number_format($current_balance,2),
                                                   'recharge_boundary' => $wallet_money
                    );
                } else {
                    $returnArr['response'] = $this->format_string("Invalid User", "invalid_user");
                }
            } else {
                $returnArr['response'] = $this->format_string("Some Parameters Missing", "some_parameters_missing");
            }
            #echo '<pre>'; print_r($returnArr); die;
        } catch (MongoException $ex) {
            $returnArr['response'] = $this->format_string("Error in connection", "error_in_connection");
        }
        $json_encode = json_encode($returnArr, JSON_PRETTY_PRINT);
        echo $this->cleanString($json_encode);
    }

	/**
	*
	* This Function return the transaction list
	*
	**/
    public function get_transaction_list() {
        $returnArr['status'] = '0';
        $returnArr['response'] = '';
        try {
            $user_id = (string) $this->input->post('user_id');
            $type = (string) $this->input->post('type');


            if ($user_id != '') {
                $userVal = $this->app_model->get_selected_fields(USERS, array('_id' => MongoID($user_id)), array());
                if ($userVal->num_rows() > 0) {
                    $checkList = $this->app_model->user_transaction($user_id, $type);
                    $transArr = array();
                    $total_amount = 0;
                    $total_transaction = 0;

                    if (count($checkList['result']) > 0) {
                         $total_amount = $checkList['result'][0]['total'];
                        if (count($checkList['result']) >0) {
                            $transactions = array_reverse($checkList['result']);
                            foreach ($transactions as $trans) {
                                $title = '';
                                if ($trans['transactions']['type'] == 'CREDIT') {
                                    if ($trans['transactions']['credit_type'] == 'welcome') {
                                        $title = $this->format_string("Welcome bonus", "welcome_bonus");
                                    } else if ($trans['transactions']['credit_type'] == 'referral') {
                                        $refVal = $this->app_model->get_selected_fields(USERS, array('_id' => MongoID($trans['transactions']['ref_id'])), array('user_name'));
                                        $title = $this->format_string("Referral reward", "referral_reward");
                                        if ($refVal->num_rows() > 0) {
                                            if (isset($refVal->row()->user_name)) {
                                                $title.=' : ' . $refVal->row()->user_name;
                                            }
                                        }
                                    }else{
										$title = $this->format_string("Recharge", "recharge");
									}
                                } else if ($trans['transactions']['type'] == 'DEBIT') {
                                    if ($trans['transactions']['debit_type'] == 'payment') {
                                        $title = $this->format_string("Booking for", "booking_for").' #' . $trans['transactions']['ref_id'];
                                    }
                                }
                                $transArr[] = array('type' => (string) $trans['transactions']['type'],
                                    'trans_amount' => (string) number_format($trans['transactions']['trans_amount'],2),
                                    'title' => (string) $title,
                                    'trans_date' => (string) get_time_to_string("jS M, Y", MongoEPOCH($trans['transactions']['trans_date'])),
                                    'balance_amount' => (string) number_format($trans['transactions']['avail_amount'],2)
                                );
                            }
                            $total_transaction = count($checkList['result']);
                        }
                    }
                    $returnArr['status'] = '1';
                    $returnArr['response'] = array('currency' => $this->data['dcurrencyCode'],
                        'total_amount' => $total_amount,
                        'total_transaction' => $total_transaction,
                        'trans' => $transArr
                    );
                } else {
                    $returnArr['response'] = $this->format_string("Invalid User", "invalid_user");
                }
            } else {
                $returnArr['response'] = $this->format_string("Some Parameters Missing", "some_parameters_missing");
            }
        } catch (MongoException $ex) {
            $returnArr['response'] = $this->format_string("Error in connection", "error_in_connection");
        }
        $json_encode = json_encode($returnArr, JSON_PRETTY_PRINT);
        echo $this->cleanString($json_encode);
    }
	
	/**
	*
	* This Function applying the tips amount for driver
	*
	**/
    public function apply_tips_amount() {
        $responseArr['status'] = '0';
        $responseArr['response'] =[];
        try {
            $ride_id = $this->input->post('ride_id');
            $tips_amount = $this->input->post('tips_amount');
            if ($ride_id != '' && $tips_amount != '') {
                $cond = array('ride_id' => $ride_id);
                $rideInfo = $this->app_model->get_selected_fields(RIDES, $cond, array('total','pay_status'));
                if ($rideInfo->num_rows() > 0) {
					if($rideInfo->row()->pay_status == 'Pending' || $rideInfo->row()->pay_status == 'Processing'){
					  $dataArr = array('total.tips_amount' => floatval($tips_amount));
						$this->app_model->update_details(RIDES, $dataArr, $cond);
						$responseArr['response']['tips_amount'] = (string) number_format($tips_amount, 2);
						$responseArr['response']['total'] = (string) number_format(($rideInfo->row()->total['grand_fare']+$tips_amount), 2);
						$responseArr['response']['tip_status'] = '1';
						$responseArr['response']['msg'] = $this->format_string('tips added successfully','tips_added');
						$responseArr['status'] = '1';
				    } else {
						$responseArr['response'] = $this->format_string('You Can\'t apply tips amount right now.','cant_apply_tips');
					}
                  } else {
                    $responseArr['response'] = $this->format_string('Records not available.','no_records_found');
                }
            } else {
                $responseArr['response'] = $this->format_string("Some Parameters are missing","some_parameters_missing");
            }
        } catch (MongoException $ex) {
            $responseArr['response'] = $this->format_string('Error in connection','error_in_connection');
        }
		$json_encode = json_encode($responseArr, JSON_PRETTY_PRINT);
        echo $this->cleanString($json_encode);
    }

	/**
	*
	* This Function applying the tips amount for driver
	*
	**/
    public function remove_tips_amount() {
        $responseArr['status'] = '0';
        $responseArr['response'] = [];
         
        try {
            $ride_id = $this->input->post('ride_id');
            if ($ride_id != '') {
                $cond = array('ride_id' => $ride_id);
                $rideInfo = $this->app_model->get_selected_fields(RIDES, $cond, array('total'));
               
                if ($rideInfo->num_rows() > 0) {
                    $dataArr = array('total.tips_amount' => floatval(0));
                    $this->app_model->update_details(RIDES, $dataArr, $cond);
                     //print_R($rideInfo->result());die;
                     $responseArr['response']['tips_amount'] = 0.00;
                     $responseArr['response']['total'] = (string) number_format($rideInfo->row()->total['grand_fare'], 2);
                    $responseArr['response']['tip_status'] =0;

                    $responseArr['response']['msg'] = $this->format_string('tips removed successfully','tips_removed');
                    $responseArr['status'] = '1'; 
                   
                } else {
                    $responseArr['response'] = $this->format_string('Records not available.','no_records_found');
                }
            } else {
                $responseArr['response'] = $this->format_string("Some Parameters are missing","some_parameters_missing");
            }
        } catch (MongoException $ex) {
            $responseArr['response'] = $this->format_string('Error in connection','error_in_connection');
        }
		$json_encode = json_encode($responseArr, JSON_PRETTY_PRINT);
        echo $this->cleanString($json_encode);
    }
	
	/**
	*
	* This Function return the fare breakup details of a particular ride
	*
	**/
    public function get_fare_breakup() {
        $responseArr['status'] = '0';
        $responseArr['response'] = '';
        try {
            $ride_id = $this->input->post('ride_id');
            $user_id = $this->input->post('user_id');
            if ($user_id != '' && $ride_id != '') {
                $cond = array('user.id' => $user_id,'ride_id' => $ride_id);
                $rideInfo = $this->app_model->get_all_details(RIDES, $cond);
			if ($rideInfo->num_rows() > 0) {
				if ($rideInfo->row()->ride_status =='Finished') {	#	Finished
					$locationArr = array();
					$driverinfoArr = array();
					$fareArr = array();
					
					$tips_amount = 0.00;
					if(isset($rideInfo->row()->total['tips_amount'])){
						$tips_amount = $rideInfo->row()->total['tips_amount'];
					}
					
					$driverInfo = $this->app_model->get_selected_fields(DRIVERS, array('_id'=>MongoID($rideInfo->row()->driver['id'])),array('image','avg_review'));
					$driver_image = USER_PROFILE_IMAGE_DEFAULT;
					if (isset($driverInfo->row()->image)) {
						if ($driverInfo->row()->image != '') {
							$driver_image = USER_PROFILE_IMAGE . $driverInfo->row()->image;
						}
					}
					$driver_ratting = 0;
					if (isset($driverInfo->row()->avg_review)) {
						if ($driverInfo->row()->avg_review != '') {
							$driver_ratting = $driverInfo->row()->avg_review;
						}
					}
					
					$locationArr = array ( 'pickup_lat'=>(string)$rideInfo->row()->booking_information['pickup']['latlong']['lat'],
					'pickup_lon'=>(string)$rideInfo->row()->booking_information['pickup']['latlong']['lon'],
					'drop_long'=>(string)$rideInfo->row()->booking_information['drop']['latlong']['lat'],
					'drop_lon'=>(string)$rideInfo->row()->booking_information['drop']['latlong']['lon']
				);
					$driverinfoArr = array ( 'name'=>(string)$rideInfo->row()->driver['name'],
														'image'=>(string) base_url().$driver_image,
														'ratting'=>(string) $driver_ratting,
														'contact_number'=>(string)$rideInfo->row()->driver['phone'],
														'cab_no'=>(string)$rideInfo->row()->driver['vehicle_no'],
														'cab_model'=>(string)$rideInfo->row()->driver['vehicle_model']
													);
					
					$userVal = $this->app_model->get_selected_fields(USERS, array('_id' => MongoID($user_id)), array('stripe_customer_id'));
					$having_card = 'No';
					if ($userVal->num_rows() > 0) {
                        if (isset($userVal->row()->stripe_customer_id)) {
                            $stripe_customer_id = $userVal->row()->stripe_customer_id;
                            if ($stripe_customer_id != '') {
								### Check the customer id is in merchant account	###
								$have_con_cards = $this->get_stripe_card_details($stripe_customer_id);
								if($have_con_cards['error_status']=='1' && count($have_con_cards['result']) > 0){
									$having_card = 'Yes';
								}
                            }
                        }
					}					
					$stripe_connected = 'No';
					if($this->data['auto_charge'] == 'Yes'){
						if($having_card == 'Yes'){
							$stripe_connected = 'Yes';
						}
					}
					$user_timeout = $this->data['user_timeout'];

					
					$distance_unit = $this->data['d_distance_unit'];
					if(isset($rideInfo->row()->fare_breakup['distance_unit'])){
						$distance_unit = $rideInfo->row()->fare_breakup['distance_unit'];
					}
					$disp_distance_unit = $distance_km;
					if($distance_unit == 'km') $disp_distance_unit = $this->format_string('km', 'km');
					if($distance_unit == 'mi') $disp_distance_unit = $this->format_string('mi', 'mi');
						
					$invoice_src = '';
					if ($rideInfo->row()->ride_status == 'Completed') {
						$invoice_path = 'trip_invoice/'.$ride_id.'_path.jpg'; 
						if(file_exists($invoice_path)) {
							$invoice_src = base_url().$invoice_path;
						}
					}
					
					$min_short = $this->format_string('min', 'min_short');
					$mins_short = $this->format_string('mins', 'mins_short');
					$ride_duration_unit = $min_short;
					if($rideInfo->row()->summary['ride_duration']>1){
						$ride_duration_unit = $mins_short;
					}
					
                    $sub_total = $rideInfo->row()->total['total_fare'] + $rideInfo->row()->total['peak_time_charge'] + $rideInfo->row()->total['night_time_charge'];
                    $grand_fare=$rideInfo->row()->total['grand_fare']+$tips_amount;
                    
					$fareArr = array ('cab_type'=>(string)$rideInfo->row()->booking_information['service_type'],
						'trip_date'=>(string) date("d-m-Y",MongoEPOCH($rideInfo->row()->booking_information['pickup_date'])),
						'base_fare'=>(string)number_format($rideInfo->row()->total['base_fare'],2),
						'ride_duration'=>(string)$rideInfo->row()->summary['ride_duration'],
						'ride_duration_unit'=>(string)$ride_duration_unit,
						'time_fare'=>(string)number_format($rideInfo->row()->total['ride_time'],2),
						'ride_distance'=>(string)$rideInfo->row()->summary['ride_distance'],
						'distance_fare'=>(string)number_format($rideInfo->row()->total['distance'],2),
						'tax_amount'=>(string)number_format($rideInfo->row()->total['service_tax'],2),
						'tip_amount'=>(string)number_format($tips_amount,2),
						'coupon_amount'=>(string)number_format($rideInfo->row()->total['coupon_discount'],2),
						'sub_total'=>(string)number_format($sub_total,2),
						'total'=>(string)number_format($grand_fare,2),
						'wallet_usage'=>(string) number_format($rideInfo->row()->total['wallet_usage'],2),
						'stripe_connected'=>(string)$stripe_connected,
						'payment_timeout'=>(string)$user_timeout,
						'distance_unit'=>(string)$disp_distance_unit,
						'invoice_src' => $invoice_src
					);
					
					$currency = $this->data['dcurrencyCode'];
					if(isset($rideInfo->row()->currency)){
						$currency = $rideInfo->row()->currency;
					}
					
					if(empty($locationArr)){
						$locationArr = json_decode("{}");
					}
					if(empty($driverinfoArr)){
						$driverinfoArr = json_decode("{}");
					}
					if(empty($fareArr)){
						$fareArr = json_decode("{}");
					}
					$responseArr['status'] = '1';
					$responseArr['response'] = array('currency'=>$currency,'location'=>$locationArr,'driverinfo'=>$driverinfoArr,'fare'=>$fareArr);
				}else{
					$responseArr['response'] = $this->format_string('You cannot make the payment for this trip now.','cannot_make_payment_now');
				}
                } else {
                    $responseArr['response'] = $this->format_string('Records not available.','no_records_found');
                }
            } else {
                $responseArr['response'] = $this->format_string("Some Parameters are missing","some_parameters_missing");
            }
        } catch (MongoException $ex) {
            $responseArr['response'] = $this->format_string('Error in connection','error_in_connection');
        }
		$json_encode = json_encode($responseArr, JSON_PRETTY_PRINT);
        echo $this->cleanString($json_encode);
    }
	
	/**
	*
	* Share user track the river location after booking confirmed
	*
	**/
    public function share_trip_status() {
        $ride_id = $this->input->post('ride_id');
        $mobile_no = $this->input->post('mobile_no');
        if ($ride_id == '') {
            $ride_id = $this->input->get('ride_id');
        }
        if ($mobile_no == '') {
            $mobile_no = $this->input->get('mobile_no');
        }
        $returnArr['status'] = '0';
        $returnArr['response'] = '';
        if ($ride_id != '' && $mobile_no != '') {
            $checkRide = $this->app_model->get_selected_fields(RIDES, array('ride_id' => $ride_id), array('ride_id', 'ride_status', 'booking_information', 'driver', 'coupon_used', 'coupon', 'cancelled', 'user'));
            if ($checkRide->num_rows() == 1) {

                $tracking_records = $this->app_model->get_all_details(TRACKING, array('ride_id' => $ride_id));
                $tracking = array();
                if ($tracking_records->num_rows() >= 0) {
                    $allStages = $tracking_records->row()->steps;
                    $user_id = $checkRide->row()->user['id'];
                    $user_name = 'unknown';
                    if ($user_id != '') {
                        $checkUser = $this->app_model->get_selected_fields(USERS, array('_id' => MongoID($user_id)), array('user_name'));
                        $user_name = $checkUser->row()->user_name;
                    }
                    $location = $allStages[count($allStages) - 1]['locality'];

                    /*                     * *****     send sms to particular user  ******* */
                    $this->sms_model->send_sms_share_driver_tracking_location($mobile_no, $location, $user_name, $ride_id);
                    $returnArr['status'] = '1';
                    $msg = $this->format_string('Your ride has been successfully shared with', 'ride_successfully_shared_with');
                    $returnArr['response'] = $msg . ' ' . $mobile_no;
                } else {
                    $returnArr['response'] = $this->format_string('Tracking records not available for this ride', 'trackings_records_not_found');
                }
            } else {
                $returnArr['response'] = $this->format_string('Records not available', 'no_records_found');
            }
        } else {
            $returnArr['response'] = $this->format_string('Some Parameters are missing', 'some_parameters_missing');
        }
        $json_encode = json_encode($returnArr, JSON_PRETTY_PRINT);
        echo $this->cleanString($json_encode);
    }
	
	/**
	*
	* This Function return the transaction list
	*
	**/
    public function get_payment_list() {
        $returnArr['status'] = '0';
        $returnArr['response'] = '';
        try {
            $user_id = (string) $this->input->post('user_id');
            $ride_id = (string) $this->input->post('ride_id');


            if ($user_id != '') {
                $userVal = $this->app_model->get_selected_fields(USERS, array('_id' => MongoID($user_id)), array('stripe_customer_id'));
                if ($userVal->num_rows() > 0) {
                    $checkRide = $this->app_model->get_all_details(RIDES, array('ride_id' => $ride_id, 'user.id' => $user_id));
                    if ($checkRide->num_rows() == 1) {
                        $having_card = 'No';
                        if (isset($userVal->row()->stripe_customer_id)) {
                            $stripe_customer_id = $userVal->row()->stripe_customer_id;
                            if ($stripe_customer_id != '') {
								$have_con_cards = $this->get_stripe_card_details($stripe_customer_id);
								if($have_con_cards['error_status']=='1' && count($have_con_cards['result']) > 0){
									$having_card = 'Yes';
								}
                            }
                        }
						
						$pay_by_cash_req = 'No';
						if(isset($checkRide->row()->pay_by_cash)){
							$pay_by_cash_req = $checkRide->row()->pay_by_cash;
						}

                        $pay_amount = $checkRide->row()->total['grand_fare'];
                        $walletDetail = $this->user_model->get_selected_fields(WALLET, array('user_id' => MongoID($user_id)), array('total'));
                        $avail_amount = 0;
                        if (isset($walletDetail->row()->total)) {
                            $avail_amount = floatval($walletDetail->row()->total);
                        }

                        $paymentArr = array();
                        $pay_by_cash = 'Disable';
                        $use_wallet_amount = 'Disable';
						if ($this->config->item('pay_by_cash') != '' && $this->config->item('pay_by_cash') != 'Disable') {
							if($pay_by_cash_req=='No'){
								$pay_by_cash = $this->format_string('Pay by Cash', 'pay_by_cash');
								$paymentArr[] = array('name' => $pay_by_cash, 'code' => 'cash');
							}
                        }
                        if (0 < $avail_amount) {
                            $avail_amount = number_format($avail_amount,2);
                            if ($this->config->item('use_wallet_amount') != '' && $this->config->item('use_wallet_amount') != 'Disable') {
                                $user_my_wallet = $this->format_string('Use my wallet/money', 'user_my_wallet');
                                $paymentArr[] = array('name' => $user_my_wallet . ' (' . $this->data['dcurrencySymbol'] . $avail_amount . ')', 'code' => 'wallet');
                            }
                        }
                        $getPaymentgatway = $this->app_model->get_all_details(PAYMENT_GATEWAY, array('status' => 'Enable'));
						
                        if ($this->data['auto_charge'] == "Yes") {
							if($having_card == 'Yes') $gateway_number = 'auto_detect'; else $gateway_number = 3;
							$pay_by_card = $this->format_string('Pay by Card', 'pay_by_card');
							$paymentArr[] = array('name' => $pay_by_card, 'code' => (string)$gateway_number);
						} else {
							if ($getPaymentgatway->num_rows() > 0) {
								foreach ($getPaymentgatway->result() as $row) {
									$paymentArr[] = array('name' => $row->gateway_name, 'code' => (string)$row->gateway_number);
								}
							}
						}
						
						$userVal = $this->app_model->get_selected_fields(USERS, array('_id' => MongoID($user_id)), array('stripe_customer_id'));
						$having_card = 'No';
						if ($userVal->num_rows() > 0) {
							if (isset($userVal->row()->stripe_customer_id)) {
								$stripe_customer_id = $userVal->row()->stripe_customer_id;
								if ($stripe_customer_id != '') {
									$having_card = 'Yes';
								}
							}
						}
						$stripe_connected = 'No';
						if($this->data['auto_charge'] == 'Yes'){
							if($having_card == 'Yes'){
								$stripe_connected = 'Yes';
							}
						}
						$user_timeout = $this->data['user_timeout'];

						
                        if (empty($paymentArr)) {
                            $paymentArr = json_decode("{}");
                        }
                        $returnArr['status'] = '1';
                        $returnArr['response'] = array('payment' => $paymentArr,
														'stripe_connected'=>(string)$stripe_connected,
														'payment_timeout'=>(string)$user_timeout
													);
                    } else {
                        $returnArr['response'] = $this->format_string('Invalid Ride', 'invalid_ride');
                    }
                } else {
                    $returnArr['response'] = $this->format_string("Invalid User", "invalid_user");
                }
            } else {
                $returnArr['response'] = $this->format_string("Some Parameters Missing", "some_parameters_missing");
            }
        } catch (MongoException $ex) {
            $returnArr['response'] = $this->format_string("Error in connection", "error_in_connection");
        }
        $json_encode = json_encode($returnArr, JSON_PRETTY_PRINT);
        echo $this->cleanString($json_encode);
    }

	/**
	*
	* This Function process the wallet usage for payment
	*
	**/
    public function payment_by_wallet() {
        $returnArr['status'] = '0';
        $returnArr['response'] = '';
        try {
            $user_id = (string) $this->input->post('user_id');
            $ride_id = (string) $this->input->post('ride_id');

            if ($user_id != '') {
                $userVal = $this->app_model->get_selected_fields(USERS, array('_id' => MongoID($user_id)), array());
                if ($userVal->num_rows() > 0) {
                    $checkRide = $this->app_model->get_all_details(RIDES, array('ride_id' => $ride_id, 'user.id' => $user_id));
                    if ($checkRide->num_rows() == 1) {
                        $walletVal = $this->app_model->get_selected_fields(WALLET, array('user_id' => MongoID($user_id)), array('total'));
                        if ($walletVal->num_rows() == 1) {
                            $wallet_amount = 0.00;
                            $ride_charge = 0.00;
                            if (isset($walletVal->row()->total)) {
                                $wallet_amount = round($walletVal->row()->total,2);
                            }
                            if (isset($checkRide->row()->total['grand_fare'])) {
                                $ride_charge = floatval($checkRide->row()->total['grand_fare']);
                            }
                            $tips_amt = 0.00;
                            if (isset($checkRide->row()->total['tips_amount'])) {
                                if ($checkRide->row()->total['tips_amount'] > 0) {
                                    $tips_amt = $checkRide->row()->total['tips_amount'];
                                }
                            }
                            $ride_charge = $ride_charge + $tips_amt;

                            if ($wallet_amount > 0 && $ride_charge > 0) {
                                if ($ride_charge <= $wallet_amount) {
                                    $pay_summary = array('type' => 'Wallet');
                                    $paymentInfo = array('ride_status' => 'Completed',
                                        'pay_status' => 'Paid',
                                        'history.wallet_usage_time' => MongoDATE(time()),
                                        'total.wallet_usage' => $ride_charge,
                                        'pay_summary' => $pay_summary
                                    );
                                    /* Update the user wallet */
                                    $currentWallet = $this->app_model->get_selected_fields(WALLET, array('user_id' => MongoID($user_id)), array('total'));
                                    $avail_amount = 0.00;
                                    if ($currentWallet->num_rows() > 0) {
                                        if (isset($currentWallet->row()->total)) {
                                            $avail_amount = floatval($currentWallet->row()->total);
                                        }
                                    }
                                    if ($avail_amount > 0) {
                                        $this->app_model->update_wallet((string) $user_id, 'DEBIT', floatval(round(($avail_amount - $ride_charge),2)));
                                    }
                                    $currentWallet = $this->app_model->get_selected_fields(WALLET, array('user_id' => MongoID($user_id)), array('total'));
                                    $avail_amount = 0.00;
                                    if ($currentWallet->num_rows() > 0) {
                                        if (isset($currentWallet->row()->total)) {
                                            $avail_amount = floatval($currentWallet->row()->total);
                                        }
                                    }
                                    $walletArr = array('type' => 'DEBIT',
                                        'debit_type' => 'payment',
                                        'ref_id' => $ride_id,
                                        'trans_amount' => floatval($ride_charge),
                                        'avail_amount' => floatval(round($avail_amount,2)),
                                        'trans_date' => MongoDATE(time())
                                    );
                                    $this->app_model->simple_push(WALLET, array('user_id' => MongoID($user_id)), array('transactions' => $walletArr));
                                    $transactionArr = array('type' => 'wallet',
                                        'amount' => floatval($ride_charge),
                                        'trans_date' => MongoDATE(time())
                                    );
                                    $this->app_model->simple_push(PAYMENTS, array('ride_id' => $ride_id), array('transactions' => $transactionArr));
                                    $this->app_model->update_details(RIDES, $paymentInfo, array('ride_id' => $ride_id));
                                    $avail_data = array('mode' => 'Available');
                                    $this->app_model->update_details(DRIVERS, $avail_data, array('_id' => MongoID($checkRide->row()->driver['id'])));

                                    $driver_id = $checkRide->row()->driver['id'];
                                    $driverVal = $this->app_model->get_selected_fields(DRIVERS, array('_id' => MongoID($driver_id)), array('_id', 'push_notification'));

                                    /* Update Stats Starts */
                                    $current_date = MongoDATE(strtotime(date("Y-m-d 00:00:00")));
                                    $field = array('ride_completed.hour_' . date('H') => 1, 'ride_completed.count' => 1);
                                    $this->app_model->update_stats(array('day_hour' => $current_date), $field, 1);
                                    /* Update Stats End */

                                    if ($driverVal->num_rows() > 0) {
                                        if (isset($driverVal->row()->push_notification)) {
                                            if ($driverVal->row()->push_notification != '') {
                                                $message = $this->format_string("payment successfully completed", "payment_completed", '', 'driver', (string)$driver_id);
                                                $options = array('ride_id' => (string) $ride_id, 'driver_id' => $driver_id);
                                                if (isset($driverVal->row()->push_notification['type'])) {
                                                    if ($driverVal->row()->push_notification['type'] == 'ANDROID') {
                                                        if (isset($driverVal->row()->push_notification['key'])) {
                                                            if ($driverVal->row()->push_notification['key'] != '') {
                                                                $this->sendPushNotification($driverVal->row()->push_notification['key'], $message, 'payment_paid', 'ANDROID', $options, 'DRIVER');
                                                            }
                                                        }
                                                    }
                                                    if ($driverVal->row()->push_notification['type'] == 'IOS') {
                                                        if (isset($driverVal->row()->push_notification['key'])) {
                                                            if ($driverVal->row()->push_notification['key'] != '') {
                                                                $this->sendPushNotification($driverVal->row()->push_notification['key'], $message, 'payment_paid', 'IOS', $options, 'DRIVER');
                                                            }
                                                        }
                                                    }
                                                }
                                            }
                                        }
                                    }
									$this->app_model->update_ride_amounts($ride_id);
									$fields = array(
										'ride_id' => (string) $ride_id
									);
									$url = base_url().'prepare-invoice';
									$this->load->library('curl');
									$output = $this->curl->simple_post($url, $fields);

									
									$curWalletVal = $this->app_model->get_selected_fields(WALLET, array('user_id' => MongoID($user_id)), array('total'));
									$currency = $this->data['dcurrencyCode'];
									$wallet_amount = 0.00;
									if ($curWalletVal->num_rows() == 1) {
										if (isset($curWalletVal->row()->total)) {
											$wallet_amount = number_format($curWalletVal->row()->total,2,'.','');
										}
									}
							
                                    $returnArr['status'] = '1';
                                    $returnArr['wallet_amount'] = (string)$wallet_amount;
                                    $returnArr['currency'] = (string)$currency;
                                    $returnArr['response'] = $this->format_string('payment successfully completed', 'payment_completed');
							
                                } else if ($ride_charge > $wallet_amount) {

                                    $pay_summary = array('type' => 'Wallet');
                                    $paymentInfo = array('pay_status' => 'Processing',
                                        'history.wallet_usage_time' => MongoDATE(time()),
                                        'total.wallet_usage' => $wallet_amount,
                                        'pay_summary' => $pay_summary
                                    );
                                    /* Update the user wallet */
                                    $currentWallet = $this->app_model->get_selected_fields(WALLET, array('user_id' => MongoID($user_id)), array('total'));
                                    $avail_amount = 0.00;
                                    if ($currentWallet->num_rows() > 0) {
                                        if (isset($currentWallet->row()->total)) {
                                            $avail_amount = floatval($currentWallet->row()->total);
                                        }
                                    }
                                    if ($avail_amount > 0) {
                                        $this->app_model->update_wallet((string) $user_id, 'DEBIT', floatval(round(($avail_amount - $wallet_amount),2)));
                                    }
                                    $currentWallet = $this->app_model->get_selected_fields(WALLET, array('user_id' => MongoID($user_id)), array('total'));
                                    $avail_amount = 0.00;
                                    if ($currentWallet->num_rows() > 0) {
                                        if (isset($currentWallet->row()->total)) {
                                            $avail_amount = floatval($currentWallet->row()->total);
                                        }
                                    }
                                    $trans_id = time() . rand(0, 2578);
                                    $walletArr = array('type' => 'DEBIT',
                                        'debit_type' => 'payment',
                                        'ref_id' => $ride_id,
                                        'trans_amount' => floatval($wallet_amount),
                                        'avail_amount' => floatval($avail_amount),
                                        'trans_date' => MongoDATE(time()),
                                        'trans_id' => $trans_id
                                    );
                                    $this->app_model->simple_push(WALLET, array('user_id' => MongoID($user_id)), array('transactions' => $walletArr));
                                    $transactionArr = array('type' => 'wallet',
                                        'amount' => floatval($wallet_amount),
                                        'trans_id' => $trans_id,
                                        'trans_date' => MongoDATE(time())
                                    );
                                    $this->app_model->simple_push(PAYMENTS, array('ride_id' => $ride_id), array('transactions' => $transactionArr));
                                    $this->app_model->update_details(RIDES, $paymentInfo, array('ride_id' => $ride_id));

                                    $unbill_amount = $ride_charge - $wallet_amount;
                                    
									
									$curWalletVal = $this->app_model->get_selected_fields(WALLET, array('user_id' => MongoID($user_id)), array('total'));
									$currency = $this->data['dcurrencyCode'];
									$wallet_amount = 0.00;
									if ($curWalletVal->num_rows() == 1) {
										if (isset($curWalletVal->row()->total)) {
											$wallet_amount = number_format($curWalletVal->row()->total,2,'.','');
										}
									}
									
									$returnArr['status'] = '2';
                                    $returnArr['used_amount'] = (string) $wallet_amount;
                                    $returnArr['unbill_amount'] = (string) $unbill_amount;
                                    $returnArr['wallet_amount'] = (string)$wallet_amount;
                                    $returnArr['currency'] = (string)$currency;
                                    $returnArr['response'] = $this->format_string('Wallet amount used successfully', 'wallet_used_successfully');
                                }
                            } else {
                                $returnArr['response'] = $this->format_string("Wallet Empty", "wallet_empty");
                            }
                        } else {
                            $returnArr['response'] = $this->format_string("Wallet Empty", "wallet_empty");
                        }
                    } else {
                        $returnArr['response'] = $this->format_string('Invalid Ride', 'invalid_ride');
                    }
                } else {
                    $returnArr['response'] = $this->format_string("Invalid User", "invalid_user");
                }
            } else {
                $returnArr['response'] = $this->format_string("Some Parameters Missing", "some_parameters_missing");
            }
        } catch (MongoException $ex) {
            $returnArr['response'] = $this->format_string("Error in connection", "error_in_connection");
        }
        $json_encode = json_encode($returnArr, JSON_PRETTY_PRINT);
        echo $this->cleanString($json_encode);
    }

	/**
	*
	* This Function process the wallet usage for payment
	*
	**/
    public function payment_by_cash() {
        $returnArr['status'] = '0';
        $returnArr['response'] = '';
        try {
            $user_id = (string) $this->input->post('user_id');
            $ride_id = (string) $this->input->post('ride_id');


            if ($user_id != '' && $ride_id != '') {
                $userVal = $this->app_model->get_selected_fields(USERS, array('_id' => MongoID($user_id)), array());
                if ($userVal->num_rows() > 0) {
                    $checkRide = $this->app_model->get_all_details(RIDES, array('ride_id' => $ride_id, 'user.id' => $user_id));
                    if ($checkRide->num_rows() == 1) {

                        $driver_id = $checkRide->row()->driver['id'];
                        $driverVal = $this->app_model->get_selected_fields(DRIVERS, array('_id' => MongoID($driver_id)), array('_id', 'push_notification'));

                        if (isset($driverVal->row()->push_notification)) {
                            if ($driverVal->row()->push_notification != '') {
                                $message = $this->format_string("rider wants to pay by cash", "rider_want_pay_by_cash", '', 'driver', (string)$driver_id);
                                $amount_to_receive = 0.00;
                                $tips_amt = 0.00;
                                if (isset($checkRide->row()->total['tips_amount'])) {
                                    if ($checkRide->row()->total['tips_amount'] > 0) {
                                        $tips_amt = $checkRide->row()->total['tips_amount'];
                                    }
                                }
                                #$amount_to_receive = $amount_to_receive + $tips_amt;
                                if (isset($checkRide->row()->total)) {
                                    if (isset($checkRide->row()->total['grand_fare']) && isset($checkRide->row()->total['wallet_usage'])) {
                                        $amount_to_receive = ($checkRide->row()->total['grand_fare'] + $tips_amt) - $checkRide->row()->total['wallet_usage'];
										
										$amount_to_receive = round($amount_to_receive,2);
                                    }
                                }
								

                                $currency = (string) $checkRide->row()->currency;
                                $options = array('ride_id' => (string) $ride_id, 'driver_id' => $driver_id, 'amount' => (string) $amount_to_receive, 'currency' => $currency);
								
								
                                if (isset($driverVal->row()->push_notification['type'])) {
                                    if ($driverVal->row()->push_notification['type'] == 'ANDROID') {
                                        if (isset($driverVal->row()->push_notification['key'])) {
                                            if ($driverVal->row()->push_notification['key'] != '') {
                                                $this->sendPushNotification($driverVal->row()->push_notification['key'], $message, 'receive_cash', 'ANDROID', $options, 'DRIVER');
                                            }
                                        }
                                    }
                                    if ($driverVal->row()->push_notification['type'] == 'IOS') {
                                        if (isset($driverVal->row()->push_notification['key'])) {
                                            if ($driverVal->row()->push_notification['key'] != '') {
                                                $this->sendPushNotification($driverVal->row()->push_notification['key'], $message, 'receive_cash', 'IOS', $options, 'DRIVER');
                                            }
                                        }
                                    }
									$payArr = array('pay_by_cash'=>'Yes');
									$this->app_model->update_details(RIDES, $payArr, array('ride_id' => $ride_id));
                                }
                            }
                        }

                        $returnArr['status'] = '1';
                        $returnArr['response'] = $this->format_string('Pay your bill by cash', 'pay_bill_by_cash');
                    } else {
                        $returnArr['response'] = $this->format_string('Invalid Ride', 'invalid_ride');
                    }
                } else {
                    $returnArr['response'] = $this->format_string("Invalid User", "invalid_user");
                }
            } else {
                $returnArr['response'] = $this->format_string("Some Parameters Missing", "some_parameters_missing");
            }
        } catch (MongoException $ex) {
            $returnArr['response'] = $this->format_string("Error in connection", "error_in_connection");
        }
        $json_encode = json_encode($returnArr, JSON_PRETTY_PRINT);
        echo $this->cleanString($json_encode);
    }

	/**
	*
	* This Function process the strip auto payment deduct
	*
	**/
    public function payment_by_auto_charge() {
        $returnArr['status'] = '0';
        $returnArr['response'] = '';
        try {
            $user_id = (string) $this->input->post('user_id');
            $ride_id = (string) $this->input->post('ride_id');

            if ($user_id != '' && $ride_id != '') {
                $userVal = $this->app_model->get_selected_fields(USERS, array('_id' => MongoID($user_id)), array());
                if ($userVal->num_rows() > 0) {
                    $checkRide = $this->app_model->get_selected_fields(RIDES, array('ride_id' => $ride_id, 'user.id' => $user_id), array('total'));
                    #echo '<pre>'; print_r($checkRide->row()); die;
                    if ($checkRide->num_rows() == 1) {
                        $grand_fare = $checkRide->row()->total['grand_fare'];
                        $paid_amount = $checkRide->row()->total['paid_amount'];
                        $wallet_amount = $checkRide->row()->total['wallet_usage'];

                        $tips_amt = 0.00;
						if (isset($checkRide->row()->total['tips_amount'])) {
							if ($checkRide->row()->total['tips_amount'] > 0) {
								$tips_amt = $checkRide->row()->total['tips_amount'];
							}
						}
						$grand_fare = $grand_fare + $tips_amt;
						
						$pay_amount = $grand_fare - ($paid_amount + $wallet_amount);

                        if ($pay_amount > 0) {
                            // Stripe Payment Process Starts here (Auto charge)
                            $paymentData = array('user_id' => $user_id, 'ride_id' => $ride_id, 'total_amount' => $pay_amount);
                            $pay_response = $this->common_auto_stripe_payment_process($paymentData);
                        } else {
                            $pay_response['status'] = '1';
                            $pay_response['msg'] = $this->format_string('This ride has been paid already', 'ride_has_been_paid_already');
                        }
                        $returnArr['status'] = $pay_response['status'];
                        $returnArr['response'] = $pay_response['msg'];
                    } else {
                        $returnArr['response'] = $this->format_string('Invalid Ride', 'invalid_ride');
                    }
                } else {
                    $returnArr['response'] = $this->format_string("Invalid User", "invalid_user");
                }
            } else {
                $returnArr['response'] = $this->format_string("Some Parameters Missing", "some_parameters_missing");
            }
        } catch (MongoException $ex) {
            $returnArr['response'] = $this->format_string("Error in connection", "error_in_connection");
        }
        $json_encode = json_encode($returnArr, JSON_PRETTY_PRINT);
        echo $this->cleanString($json_encode);
    }

	/**
	*
	* Setting values for payment
	*
	**/
    public function payment_by_gateway() {
        $returnArr['status'] = '0';
        $returnArr['response'] = '';
        try {
            $user_id = (string) $this->input->post('user_id');
            $ride_id = (string) $this->input->post('ride_id');
            $payment = (string) $this->input->post('gateway');

            if ($payment != '' && $ride_id != '' && $user_id != '') {
                $checkRide = $this->app_model->get_all_details(RIDES, array('ride_id' => $ride_id, 'user.id' => $user_id));
                if ($checkRide->num_rows() == 1) {
                    $driver_id = $checkRide->row()->driver['id'];
                    $paymentVal = $this->app_model->get_all_details(PAYMENT_GATEWAY, array('status' => 'Enable', 'gateway_number' => $payment));
                    if ($paymentVal->num_rows() > 0) {
                        $payment_name = $paymentVal->row()->gateway_name;
                        $pay_amount = 0.00;
                        if (isset($checkRide->row()->total)) {
                            if (isset($checkRide->row()->total['grand_fare']) && isset($checkRide->row()->total['wallet_usage'])) {
                                $pay_amount = round(($checkRide->row()->total['grand_fare'] - $checkRide->row()->total['wallet_usage']), 2);
                            }
                        }
						
						$tips_amt = 0.00;
						if (isset($checkRide->row()->total['tips_amount'])) {
							if ($checkRide->row()->total['tips_amount'] > 0) {
								$tips_amt = $checkRide->row()->total['tips_amount'];
							}
						}
						
                        $payArr = array('user_id' => $user_id,
                            'driver_id' => $driver_id,
                            'ride_id' => $ride_id,
                            'payment_id' => $payment,
                            'payment' => $payment_name,
                            'amount' => $pay_amount,
							'tips_amount' => $tips_amt,
                            'dateAdded' => MongoDATE(time())
                        );
                        $this->app_model->simple_insert(MOBILE_PAYMENT, $payArr);
                        $mobile_id = $this->mongo_db->insert_id();
                        $returnArr['status'] = '1';
                        $returnArr['response'] = $this->format_string('Payment Initiated', 'payment_initiated');
                        $returnArr['mobile_id'] = (string) $mobile_id;
                    } else {
                        $returnArr['response'] = $this->format_string('Payment method currently unavailable', 'payment_method_unavailable');
                    }
                } else {
                    $returnArr['response'] = $this->format_string('Authentication Failed', 'authentication_failed');
                }
            }else{
				$returnArr['response'] = $this->format_string("Some Parameters Missing", "some_parameters_missing");
			}
        } catch (MongoException $ex) {
            $returnArr['response'] = $this->format_string("Error in connection", "error_in_connection");
        }
        $json_encode = json_encode($returnArr, JSON_PRETTY_PRINT);
        echo $this->cleanString($json_encode);
    }

	/**
	*
	* Routing the payment process
	*
	**/
    public function proceed_payment() {
        $mobile_id = (string) $this->input->get('mobileId');
        if ($mobile_id != '') {
            $checkPayment = $this->app_model->get_all_details(MOBILE_PAYMENT, array('_id' => MongoID($mobile_id)));
            if ($checkPayment->num_rows() == 1) {
                $payment_id = $checkPayment->row()->payment_id;
                switch ($payment_id) {
                    case '1':
                        redirect(base_url() . 'v8/api/payment/authorizedotNet?mobileId=' . $mobile_id);
                        break;
                    case '2':
                        redirect(base_url() . 'v8/api/payment/paypal?mobileId=' . $mobile_id);
                        break;
                    case '3':
                        redirect(base_url() . 'v8/api/payment/stripe?mobileId=' . $mobile_id);
                        break;
                }
            }
        }
    }

	/**
	*
	* Mail Invoice
	*
	**/
    public function mail_invoice() {
        $returnArr['status'] = '0';
        $returnArr['response'] = '';
        try {
            $ride_id = $this->input->post('ride_id');
            $email = $this->input->post('email');
            if ($ride_id != '' && $email != '') {
                $this->mail_model->send_invoice($ride_id, $email);
                $returnArr['status'] = '1';
                $returnArr['response'] = $this->format_string('Mail sent', 'mail_sent');
            } else {
                $returnArr['response'] = $this->format_string('Mail not sent', 'mail_not_sent');
            }
        } catch (MongoException $ex) {
            $returnArr['response'] = $this->format_string("Error in connection", "error_in_connection");
        }
        $json_encode = json_encode($returnArr, JSON_PRETTY_PRINT);
        echo $this->cleanString($json_encode);
    }
	
    
}

/* End of file user.php */
/* Location: ./application/controllers/v8/api/user.php */