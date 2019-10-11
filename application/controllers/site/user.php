<?php
//print_R($this->checkLogin('U'));die;
if (!defined('BASEPATH'))
	exit('No direct script access allowed');

/**
 * 
 * User related functions
 * @author Casperon
 *
 * */
class User extends MY_Controller {

	function __construct() {
		parent::__construct();
		$this->load->helper(array('cookie', 'date', 'form', 'email'));
		$this->load->library(array('encrypt', 'form_validation'));
		$this->load->model('user_model');
		$this->load->model('app_model');
		$this->load->model('mail_model');
		
		
		
		
	}

	/**
	 * 
	 * This function loads the login index form
	 * 
	 */
	public function login_index_form() {
		if ($this->lang->line('driver_login_ucfirst') != '')
			$Login = stripslashes($this->lang->line('driver_login_ucfirst'));
		else
			$Login = 'Login';

		$this->data['heading'] = $this->config->item('email_title') . ' ' . $Login;
		$this->load->view('site/user/login_index.php', $this->data);
	}

	/**
	 * 
	 * This function loads the login index form
	 * 
	 */
	public function signup_index_form() {
		if ($this->lang->line('driver_login_ucfirst') != '')
			$Login = stripslashes($this->lang->line('driver_login_ucfirst'));
		else
			$Login = 'Login';

		$this->data['heading'] = $this->config->item('email_title') . ' ' . $Login;
		$this->load->view('site/user/signup_index.php', $this->data);
	}

	/**
	 * 
	 * This function loads the riders login  form
	 * 
	 */
	public function login_form() {
		if ($this->checkLogin('U') == '') {
			if ($this->lang->line('driver_rider_login') != '')
				$driver_rider_login = stripslashes($this->lang->line('driver_rider_login'));
			else
				$driver_rider_login = 'Rider Login';

			$this->data['heading'] = $driver_rider_login;
			

			/**             * *** Stay signed in process *** */
			if ($this->checkLogin('U') == '') {
				$UserCookieData = $this->input->cookie(APP_NAME."_NewUser");
				if ($UserCookieData != '') {
					$condition = array('_id' => MongoID($UserCookieData));
					$checkUser = $this->user_model->get_selected_fields(USERS, $condition, array('_id', 'user_name', 'unique_code', 'email'));
					if ($checkUser->num_rows() == 1) {
						$userdata = array(
							APP_NAME.'_session_user_id' => (string) $checkUser->row()->_id,
							APP_NAME.'_session_user_name' => $checkUser->row()->user_name,
							APP_NAME.'_session_unique_code' => $checkUser->row()->unique_code,
							APP_NAME.'_session_user_email' => $checkUser->row()->email
						);
						$this->session->set_userdata($userdata);
						redirect('rider');
					}
				}
			}

			$this->load->view('site/user/login.php', $this->data);
		} else {
			redirect('rider');
		}
	}

	/**
	 * 
	 * This function loads the riders register  form
	 * 
	 */
	public function rigister_form() {
		if ($this->lang->line('driver_sign_up_to_ride') != '')
			$driver_sign_up_to_ride = stripslashes($this->lang->line('driver_sign_up_to_ride'));
		else
			$driver_sign_up_to_ride = 'Sign up to ride';

		$this->data['heading'] = $driver_sign_up_to_ride;
		if ($this->checkLogin('U') == '') {
			$ref_code = (empty($_GET['ref'])) ? '' : $_GET['ref'];
				
			if ($ref_code != '') {
				$ref_code = base64_decode($ref_code);
				$this->data['referrer_info'] = $this->user_model->get_all_details(USERS, array('unique_code' => $ref_code));
				if ($this->data['referrer_info']->num_rows() > 0) {
					$this->data['sideMenu'] = 'share_code';

					if ($this->lang->line('driver_sign_up_with_my_code') != '') $driver_sign_up_with_my_code = stripslashes($this->lang->line('driver_sign_up_with_my_code')); else $driver_sign_up_with_my_code = 'Sign up with my code';

					$unique_code =" " . $this->data['referrer_info']->row()->unique_code;

					if ($this->lang->line('driver_to_get') != '') $driver_to_get = stripslashes($this->lang->line('driver_to_get')); else $driver_to_get = 'to get';

					if ($this->lang->line('driver_bonus_amount_on') != '') $driver_bonus_amount_on = str_replace('{SITENAME}', $this->config->item('email_title'), stripslashes($this->lang->line('driver_bonus_amount_on'))); else $driver_bonus_amount_on = "bonus amount on " . $this->config->item('email_title');
					
					$welcome_amount = floatval($this->config->item('welcome_amount'));
					if($welcome_amount>0){
						$shareDesc = $driver_sign_up_with_my_code .' '. $unique_code . " " . $driver_to_get . " " . $this->data['dcurrencyCode'] . " " . number_format($welcome_amount, 2) . " " . $driver_bonus_amount_on;
					}else{
						$shareDesc = $driver_sign_up_with_my_code .' '. $unique_code;
					}
					
					$this->data['shareDesc'] = $shareDesc;
				}
			}
			//echo"<pre>";print_r($this->data);

			$this->load->view('site/user/register.php', $this->data);
		} else {
			redirect('rider');
		}
	}

	/**
	 * 
	 * This function login the riders into site
	 * 
	 */
	public function rider_login() {
		if ($this->checkLogin('U') != '') {
			$this->setErrorMessage('error', 'Some one is already logged in', 'driver_someone_already_logged_in');
		}
		
		if ($this->lang->line('form_validation_email_address') != ''){
			$form_validation_email_address = stripslashes($this->lang->line('form_validation_email_address'));
		}else{
			$form_validation_email_address = 'Email Address';
		}
		if ($this->lang->line('form_validation_password') != ''){
			$form_validation_password = stripslashes($this->lang->line('form_validation_password'));
		}else{
			$form_validation_password = 'Password';
		}
            
		
		$this->form_validation->set_rules('emailAddr', $form_validation_email_address, 'required');
		$this->form_validation->set_rules('password', $form_validation_password, 'required');
		$next = $this->input->post('next_url');
	
		if ($this->form_validation->run() === FALSE) {
			$this->setErrorMessage('error', 'Email and password fields required', 'driver_email_password_fields_required');
			if ($next != '') {
				redirect('rider/login?action=' . urlencode($next));
			} else {
				redirect('rider/login');
			}
		} else {
			$email = strtolower($this->input->post('emailAddr'));
			$pwd = md5($this->input->post('password'));
			$stay_signed_in = $this->input->post('stay_signed_in');
			$condition = array('email' => $email, 'password' => $pwd);
			$checkUser = $this->user_model->get_selected_fields(USERS, $condition, array('_id'));

			$user_id = (string) $checkUser->row()->_id;

			if ($checkUser->num_rows() == 1) {
				$condition = array('email' => $email, 'password' => $pwd, 'status' => 'Active');
				$checkUser = $this->user_model->get_selected_fields(USERS, $condition, array('_id', 'user_name', 'unique_code', 'email'));
				if ($checkUser->num_rows() == 1) {
					$userdata = array(
						APP_NAME.'_session_user_id' => (string) $checkUser->row()->_id,
						APP_NAME.'_session_user_name' => $checkUser->row()->user_name,
						APP_NAME.'_session_unique_code' => $checkUser->row()->unique_code,
						APP_NAME.'_session_user_email' => $checkUser->row()->email
							//'userType'=>$checkUser->row()->user_type
					);

					$this->session->set_userdata($userdata);
					if ($stay_signed_in == "yes") {
						$CookieVal = array('name' => APP_NAME.'_NewUser', 'value' => (string) $checkUser->row()->_id, 'expire' => 3600 * 24 * 7);
						$this->input->set_cookie($CookieVal);
					}
					$datestring = "%Y-%m-%d %h:%i:%s";
					$time = time();
					$newdata = array(
						'last_login_date' => mdate($datestring, $time),
						'last_login_ip' => $this->input->ip_address()
					);

					$condition = array('_id' => MongoID($user_id));
					$this->user_model->update_details(USERS, array('last_login_info' => $newdata), $condition);

					$this->setErrorMessage('success', 'You are Logged In!', 'driver_you_logged_in');

					if ($next != '') {
						redirect($next);
					} else {
						redirect('rider/booking');
					}
				} else {
					$this->setErrorMessage('error', 'Your Account Is In-Active', 'driver_acccount_inactive');
					if ($next != '') {
						redirect('rider/login?action=' . urlencode($next));
					} else {
						redirect('rider/login');
					}
				}
			} else {
				$this->setErrorMessage('error', 'Invalid login details', 'driver_invalid_login');
				if ($next != '') {
					redirect('rider/login?action=' . urlencode($next));
				} else {
					redirect('rider/login');
				}
			}
		}
	}

	/**
	 * 
	 * clear all the user session
	 * 
	 */
	public function logout_rider() {
		try{
					//echo $this->input->cookie('Shopsy_NewUser');die;
				$datestring = "%Y-%m-%d %h:%i:%s";
				$time = time();

				$last_logout_date = mdate($datestring, $time);

				$condition = array('_id' => MongoID($this->checkLogin('U')));
				$this->user_model->update_details(USERS, array('last_login_info.last_logout_date' => $last_logout_date), $condition);
				$userdata = array(
					APP_NAME.'_session_user_id' => '',
					APP_NAME.'_session_user_name' => '',
					APP_NAME.'_session_unique_code' => '',
					APP_NAME.'_session_user_email' => ''
				);
				$_SESSION['email'] = '';
				$_SESSION['first_name'] = '';
				$_SESSION['last_name'] = '';

				unset($_SESSION['email']);
				unset($_SESSION['first_name']);
				unset($_SESSION['last_name']);
				$this->session->unset_userdata($userdata);
				@session_start();
				unset($_SESSION['token']);
				delete_cookie(APP_NAME."_NewUser");
				$this->setErrorMessage('success', 'Successfully loggedout from your account', 'driver_successfully_loggedout');
				redirect('');
		}
		catch(MongoException $me){
				redirect('');
		}
		
	}

	/**
	 * 
	 * This function send the new password to driver email
	 */
	public function send_rider_reset_pwd_link($pwd = '', $query) {
		$newsid = '10';
		$reset_url = base_url() . 'rider/reset-password-form/' . $pwd;
		$user_name = $query->row()->user_name;
		$template_values = $this->user_model->get_email_template($newsid,$this->data['langCode']);
		$subject = 'From: ' . $this->config->item('email_title') . ' - ' . $template_values['subject'];
		$ridernewstemplateArr = array('email_title' => $this->config->item('email_title'), 'mail_emailTitle' => $this->config->item('email_title'), 'mail_logo' => $this->config->item('logo_image'), 'mail_footerContent' => $this->config->item('footer_content'), 'mail_metaTitle' => $this->config->item('meta_title'), 'mail_contactMail' => $this->config->item('site_contact_mail'));
		extract($ridernewstemplateArr);
		$message = '<!DOCTYPE HTML>
			<html>
			<head>
			<meta http-equiv="Content-Type" content="text/html; charset=utf-8">
			<meta name="viewport" content="width=device-width"/>
			<title>' . $subject . '</title>
			<body>';
		include($template_values['templateurl']);
		$message .= '</body>
			</html>';
			$sender_email = $this->config->item('site_contact_mail');
			$sender_name = $this->config->item('email_title');
		
		$email_values = array('mail_type' => 'html',
			'from_mail_id' => $sender_email,
			'mail_name' => $sender_name,
			'to_mail_id' => $query->row()->email,
			'subject_message' => 'Password Reset',
			'body_messages' => $message
		);
		#var_dump($email_values);
		$email_send_to_common = $this->user_model->common_email_send($email_values);
	}

	/**
	 * 
	 * This function loads the reset password form
	 */
	function reset_password_form() {
		$reset_id = $this->uri->segment(3);
		$condition = array('reset_id' => $reset_id);
		$riderVal = $this->user_model->get_selected_fields(USERS, $condition, array('_id'));
		if ($riderVal->num_rows() == 1) {
			if ($this->lang->line('driver_reset_password') != '')
				$driver_reset_password = stripslashes($this->lang->line('driver_reset_password'));
			else
				$driver_reset_password = 'Reset Password';
			$this->data['heading'] = $driver_reset_password;
			$this->data['reset_id'] = $reset_id;
			$this->load->view('site/user/reset_password.php', $this->data);
		} else {
			$this->setErrorMessage('error', 'Invalid reset password link', 'driver_invalid_reset_pwd_link');
			redirect('rider/login');
		}
	}

	/**
	 * 
	 * This function updates the reset password
	 */
	function update_reset_password() {
		if ($this->checkLogin('U') != '') {
			redirect('rider');
		}
		$reset_id = $this->input->post('reset_id');
		$pwd = $this->input->post('new_password');
		$condition = array('reset_id' => $reset_id);
		$driverVal = $this->user_model->update_details(USERS, array('password' => md5($pwd), 'reset_id' => '','modified' => date("Y-m-d H:i:s")), $condition);
		$this->setErrorMessage('success', 'Password changed successfully', 'driver_pwd_changed_successfully');
		redirect('rider/login');
	}

	/**
	 * 
	 * Forgot password view page
	 * 
	 */
	public function forgot_password_form() {
		if ($this->checkLogin('U') != '') {
			$this->setErrorMessage('error', 'Some one is already logged in', 'driver_someone_already_logged_in');
			redirect('rider');
		}
		if ($this->lang->line('driver_site_forgot_pwd') != '')
			$heading = str_replace('{SITENAME}', $this->config->item('email_title'), stripslashes($this->lang->line('driver_site_forgot_pwd')));
		else
			$heading = $this->config->item('email_title') . ' - Forgot Password';
		$this->data['heading'] = $heading;
		$this->load->view('site/user/forgot_password.php', $this->data);
	}

	/**
	 * 
	 * This function validate the forgot password form
	 * If email is correct then generate new password and send it to the email given
	 */
	public function user_forgot_password() {
		if ($this->lang->line('form_validation_email') != ''){
			$form_validation_email = stripslashes($this->lang->line('form_validation_email'));
		}else{
			$form_validation_email = 'Email';
		}
		$this->form_validation->set_rules('email', $form_validation_email, 'required|valid_email');
		if ($this->form_validation->run() === FALSE) { 
			$this->load->view('site/user/reset_password.php', $this->data);
		} else {
			$email = $this->input->post('email');
			$collection = USERS;

			$condition = array('email' => $email);
			$riderVal = $this->user_model->get_all_details($collection, $condition);
			if ($riderVal->num_rows() == 1) {
				$new_pwd = $this->get_rand_str('6') . time();
				$newdata = array('reset_id' => $new_pwd);
				$condition = array('email' => $email);
				$this->user_model->update_details($collection, $newdata, $condition);
				$this->send_rider_reset_pwd_link($new_pwd, $riderVal);
				$this->setErrorMessage('success', 'Reset password link has been sent to your email address', 'driver_reset_password_link');
				redirect('rider/login');
			} else {
				$this->setErrorMessage('error', 'Email id not matched in our records', 'driver_emaild_not_matched');
				redirect('rider/reset-password');
			}
			redirect('rider/login');
		}
	}

	/**
	 *
	 * This function creates a new account for user
	 *
	 * */
	public function register_rider() {  
		if ($this->checkLogin('U') != '') {
			$this->setErrorMessage('error', 'Some one is already logged in', 'driver_someone_already_logged_in');
		}
		$email = strtolower($this->input->post('email'));
		$user_type = $this->input->post('user_type');
		$fb_user_id = $this->input->post('fb_user_id');
		$password = $this->input->post('password');
		$user_name = $this->input->post('user_name');
		$country_code = $this->session->userdata(APP_NAME.'otp_country_code');
		$phone_number = $this->session->userdata(APP_NAME.'otp_phone_number');
		$referal_code = $this->input->post('referal_code');
		$gcm_id = $this->input->post('gcm_id');
		$deviceToken = $this->input->post('deviceToken');
		
		
		if(empty($user_type))
		$user_type='Normal';
	
		if(empty($fb_user_id))
		$fb_user_id='';
		
		/* user_image will be coming on social login post */
		$user_image=$this->input->post('user_image');
		if(!empty($user_image))
		{
		$image=$user_image;	
		}else{
		$image='';
		}
		
		if (is_array($this->input->post())) {
			$chkValues = count(array_filter($this->input->post()));
		} else {
			$chkValues = 0;
		}

		if ($chkValues >= 6) {
			if (valid_email($email)) {
				$checkEmail = $this->user_model->check_user_exist(array('email' => $email));
				if ($checkEmail->num_rows() >= 1) {
					$this->setErrorMessage('error', 'Email address already exists', 'driver_email_already');
					redirect('rider/signup');
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
							'user_type' => $user_type,
							'unique_code' => $unique_code,
							'email' => $email,
							'password' => md5($password),
							'fb_user_id'=>$fb_user_id,
							'media_id'=>$fb_user_id,
							'image' => $image,
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

							$fields = array(
								'username' => (string) $last_insert_id,
								'password' => md5((string) $last_insert_id)
							);
							$url = $this->data['soc_url'] . 'create-user.php';
							$this->load->library('curl');
							$output = $this->curl->simple_post($url, $fields);

							$push_data = array();
							$key = '';

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

							/* Sending Mail notification about registration */
							$this->mail_model->send_user_registration_mail($last_insert_id);
							$this->setErrorMessage('success', 'Successfully registered', 'driver_successfully_registered');
							
							
							/***********************  Social Auto Login Process *********************/
							$register_media = $this->input->post('register_media');
							if($register_media == 'social' && $last_insert_id != ''){
								$condition = array('_id' => MongoID($last_insert_id));
								$checkUser = $this->user_model->get_selected_fields(USERS, $condition, array('_id', 'user_name', 'unique_code', 'email'));
								$userdata = array(
											APP_NAME.'_session_user_id' => (string) $checkUser->row()->_id,
											APP_NAME.'_session_user_name' => $checkUser->row()->user_name,
											APP_NAME.'_session_unique_code' => $checkUser->row()->unique_code,
											APP_NAME.'_session_user_email' => $checkUser->row()->email
											);
								$this->session->set_userdata($userdata);
								$this->setErrorMessage('success','You are Logged In!','user_social_logged_in');
								
								$social_userdata = array(
											'social_login_name' => '',
											'social_email_name' => '',
											'social_image_name' => '',
											'social_user_id' =>'',
											'loginUserType' => '',
											'ref_code' => ''
											);
								unset($_SESSION['ref_code']);
								$this->session->set_userdata($social_userdata);
								redirect('rider/profile');
							}
							/********************************************************************************/
							redirect('');
						} else {
							$this->setErrorMessage('error', 'Sorry,Registration failed. Please try again', 'driver_registration_failed');
						}
					} else {
						$this->setErrorMessage('error', 'Sorry,You have applied Invalid referral code', 'driver_invalid_referral');
					}
				}
			} else {
				$this->setErrorMessage('error', 'Invalid email address', 'driver_invalid_email');
			}
		} else {
			$this->setErrorMessage('error', 'Please fill all the required fields', 'driver_fill_all_fields');
		}

		redirect('rider/signup');
	}

	public function load_invoice() {
		$ride_id = $this->uri->segment(2);
		if ($ride_id != '') {
			$ride_info = $this->user_model->get_all_details(RIDES, array('ride_id' => $ride_id));
			if ($ride_info->num_rows() == 1) {
				/* $this->load->helper(array('ride_helper'));
				create_and_save_travel_path_in_map($ride_id); */
				$this->data['heading'] = $this->config->item('email_title') . ' Invoice';
				$currency_code = $ride_info->row()->currency;
				$ride_distance_unit = $this->data['d_distance_unit'];
				if(isset($ride_info->row()->fare_breakup)){
					if(array_key_exists('distance_unit',$ride_info->row()->fare_breakup)){
						$ride_distance_unit = $ride_info->row()->fare_breakup['distance_unit'];
					}
				}
				
				$currencyVal = $this->user_model->get_all_details(CURRENCY, array('code' => $currency_code));
				if ($currencyVal->num_rows() > 0) {
					$this->data['rcurrencyCode'] = $currencyVal->row()->code;
					$this->data['rcurrencySymbol'] = $currencyVal->row()->symbol;
					$this->data['rcurrencyName'] = $currencyVal->row()->name;
				}
				$invoiceData = $this->mail_model->view_invoice($ride_id);
				$this->data['invoiceData'] = $invoiceData;
				
				$this->data['ride_info'] = $ride_info;
				$this->data['ride_distance_unit'] = $ride_distance_unit;
				$this->load->view('site/user/invoice.php', $this->data);
			} else {
				$this->load->view('site/user/no_invoice.php', $this->data);
			}
		} else {
			$this->load->view('site/user/no_invoice.php', $this->data);
		}
	}

	/**
	 *
	 * This function subscribes the user
	 *
	 * */
	public function email_subscription() {
		$result_msg['msg'] = 'Error';	
		
		if ($this->lang->line('user_email_subscription_error') != '')
			$dis_msg = stripslashes($this->lang->line('user_email_subscription_error'));
		else
			$dis_msg = 'Sorry, Please try again';
		$result_msg['dis_msg'] = $dis_msg;
		
		$subscriber_name = @explode('@',$this->input->post('subscriber_email'));
		
		$subscriber_name = $subscriber_name[0];
		
		$subscriber_email = $this->input->post('subscriber_email');
		if ($subscriber_email != '' && $subscriber_name != '') {
			$chkSuscriber = $this->user_model->get_selected_fields(NEWSLETTER_SUBSCRIBER, array('subscriber_email' => $subscriber_email), array('_id'));
			if ($chkSuscriber->num_rows() == 0) {
				$randStr = $this->get_rand_str('10');

				$dataArr = array('subscriber_name' => $subscriber_name, 'subscriber_email' => $subscriber_email, 'dateSubscribed' => date('Y-m-d H:i:s'), 'verification_code' => $randStr, 'verify_status' => 'No');
				$this->user_model->simple_insert(NEWSLETTER_SUBSCRIBER, $dataArr);

				$maxidd = $this->user_model->get_last_insert_id();

				$cfmurl = base_url() . 'site/user/confirm_subscription/' . $maxidd . '/' . $randStr;

				$newsid = '7';
				$template_values = $this->user_model->get_email_template($newsid,$this->data['langCode']);
				$subject = 'Newsletter Confirmation From : ' . $this->config->item('email_title');
				$adminnewstemplateArr = array('email_title' => $this->config->item('email_title'), 'logo' => $this->config->item('logo_image'), 'footer_content' => $this->config->item('footer_content'), 'meta_title' => $this->config->item('meta_title'), 'site_contact_mail' => $this->config->item('site_contact_mail'));
				extract($adminnewstemplateArr);
				$header = '';
				$message = '';

				$header .="Content-Type: text/plain; charset=ISO-8859-1\r\n";

				$message .= '<!DOCTYPE HTML>
				<html>
				<head>
				<meta http-equiv="Content-Type" content="text/html; charset=utf-8">
				<meta name="viewport" content="width=device-width"/><body>';
				include($template_values['templateurl']);
				$message .= '</body>
				</html>';
				$sender_email = $this->config->item('site_contact_mail');
				$sender_name = $this->config->item('email_title');
				

				$email_values = array('mail_type' => 'html',
					'from_mail_id' => $sender_email,
					'mail_name' => $sender_name,
					'to_mail_id' => $subscriber_email,
					'subject_message' => $template_values['subject'],
					'body_messages' => $message
				);
				$email_send_to_common = $this->user_model->common_email_send($email_values);

				$result_msg['msg'] = 'Success';
				if ($this->lang->line('user_email_subscription_success') != '')
					$dis_msg = stripslashes($this->lang->line('user_email_subscription_success'));
				else
					$dis_msg = 'Thanks for subscribing with us';
				$result_msg['dis_msg'] = $dis_msg;
			} else {
				$result_msg['msg'] = 'Exist';
				if ($this->lang->line('user_email_subscription_exist') != '')
					$dis_msg = stripslashes($this->lang->line('user_email_subscription_exist'));
				else
					$dis_msg = 'You have already subscribed';
				$result_msg['dis_msg'] = $dis_msg;
			}
		}
		echo json_encode($result_msg);
	}

	/**
	 *
	 * This function  confirms subscribers email address
	 *
	 * */
	public function confirm_subscription() {
		$subscriber_id = $this->uri->segment(4);
		$verification_code = $this->uri->segment(5);
		if ($verification_code != '' && $subscriber_id != '') {
			$condition = array('_id' => MongoID($subscriber_id), 'verification_code' => $verification_code);
			$chkSuscriber = $this->user_model->get_selected_fields(NEWSLETTER_SUBSCRIBER, $condition, array('_id', 'verify_status'));
			if ($chkSuscriber->num_rows() == 0) {
				$this->setErrorMessage('error', 'Sorry, This is not a valid confirmation link', 'driver_invalid_confirmation_link');
				redirect();
			} else {
				if ($chkSuscriber->row()->verify_status == 'Yes') {
					$this->setErrorMessage('success', 'You have already confirmed your subscription', 'driver_confirmed_your_subscription');
					redirect();
				} else {
					$dataArr = array('verify_status' => 'Yes');
					$this->user_model->update_details(NEWSLETTER_SUBSCRIBER, $dataArr, $condition);
					$this->setErrorMessage('success', 'Thanks, You have successfully confirmed your subscription', 'driver_successfully_confirmed');
					redirect();
				}
			}
		} else {
			$this->setErrorMessage('error', 'Sorry, This is not a valid confirmation link', 'driver_invalid_confirmation_link');
			redirect();
		}
	}

	/**
	 *
	 * This function  confirms emergency email and mobile
	 *
	 * */
	public function confirm_emergency_contact_form() {
		$user_id = $this->input->get('u');
		$otp_encr = $this->input->get('c');
		if ($user_id != '') {
			$condition = array('_id' => MongoID($user_id));
			$getUser = $this->user_model->get_selected_fields(USERS, $condition, array('_id', 'user_name', 'emergency_contact'));
			if ($getUser->num_rows() == 0) {
				$this->setErrorMessage('error', 'This user details does not exist', 'driver_user_detail_not_exist');
				redirect('');
			} else {
			
				if (isset($getUser->row()->emergency_contact['verification']['mobile'] ) && $getUser->row()->emergency_contact['verification']['mobile'] == 'Yes' && isset($getUser->row()->emergency_contact['verification']['email'] ) && $getUser->row()->emergency_contact['verification']['email'] == 'Yes') {
					$this->setErrorMessage('success', 'Already your verification has been done.', 'driver_verification_done');
					redirect('');
				}
                
				$em_mobile_otp = $getUser->row()->emergency_contact['mobile_otp'];
				if (md5($em_mobile_otp) == $otp_encr) {
					$emDataArr = array('emergency_contact.verification.email' => 'Yes');
					$this->user_model->update_details(USERS, $emDataArr, $condition);
                    
                    if ((isset($getUser->row()->emergency_contact['verification']['mobile']) && $getUser->row()->emergency_contact['verification']['mobile'] == 'No') || !isset($getUser->row()->emergency_contact['verification']['mobile'])) {
                        $this->data['otp_number'] = $em_mobile_otp;
                        $this->data['user_details'] = $getUser;
                        $this->load->view('site/user/emergency_contact_confirmation', $this->data);
                    } else {
                        $this->setErrorMessage('success', 'Emergency contact details verified successfully', 'em_verification_completed');
                        redirect('');
                    }
				} else {
					$this->setErrorMessage('error', 'User authentication failed, Link is not a valid link', 'driver_user_auth_failed');
					redirect('');
				}
			}
		} else {
			$this->setErrorMessage('error', 'Invalid link', 'driver_invalid_link');
			redirect('');
		}
	}

	/**
	 *
	 * This function  confirms emergency email and mobile
	 *
	 * */
	public function confirm_emergency_contact() {
		$user_id = $this->input->post('user_id');
		$emGiven_otp = trim($this->input->post('em_mobile_otp'));
		if ($user_id != '') {
			$condition = array('_id' => MongoID($user_id));
			$getUser = $this->user_model->get_selected_fields(USERS, $condition, array('_id', 'user_name', 'emergency_contact'));
			if ($getUser->num_rows() == 0) {
				$this->setErrorMessage('error', 'This user details does not exist', 'driver_user_detail_not_exist');
				redirect($_SERVER['HTTP_REFERER']);
			} else {
				$em_mobile_otp = $getUser->row()->emergency_contact['mobile_otp'];
				
				if (isset($getUser->row()->emergency_contact['verification']['mobile'] ) && $getUser->row()->emergency_contact['verification']['mobile'] == 'Yes' && isset($getUser->row()->emergency_contact['verification']['email'] ) && $getUser->row()->emergency_contact['verification']['email'] == 'Yes') {
					$this->setErrorMessage('success', 'Already your verification has been done.', 'driver_verification_done');
					redirect('');
				}
				
				if ($em_mobile_otp == $emGiven_otp) {
					$emDataArr = array('emergency_contact.verification.mobile' => 'Yes');
					$this->user_model->update_details(USERS, $emDataArr, $condition);
					$this->setErrorMessage('success', 'Thanks, Your verification has been completed successfully', 'driver_verification_completed');
					redirect('');
				} else {
					$this->setErrorMessage('error', 'Sorry, OTP does not match', 'driver_otp_not_match');
					redirect($_SERVER['HTTP_REFERER']);
				}
			}
		} else {
			$this->setErrorMessage('error', 'Invalid link', 'driver_invalid_link');
			redirect($_SERVER['HTTP_REFERER']);
		}
	}

	/**
	 *
	 * This function  tracks the ride details
	 *
	 * */
	public function track_ride_location_details() {
		if(isset($_POST['rideId']) || isset($_GET['rideId'])){
			$ride_id = $this->input->post('rideId');
			if ($ride_id == '') {
				$ride_id = $this->input->get('rideId');
			}
		} else if(isset($_POST['q']) || isset($_GET['q'])){
			$ride_id = $this->input->post('q');
			if ($ride_id == '') {
				$ride_id = $this->input->get('q');
			}
		}
		
		if ($ride_id != '') {
			$checkRide = $this->app_model->get_selected_fields(RIDES, array('ride_id' => $ride_id), array('ride_id', 'user', 'ride_status', 'booking_information', 'driver', 'coupon_used', 'coupon', 'cancelled', 'history', 'summary', 'pay_status', 'pay_summary', 'cancelled'));
			if ($checkRide->num_rows() == 1) {
				$driver_id = $checkRide->row()->driver['id'];
				$user_id = $checkRide->row()->user['id'];
				$user_details = $this->app_model->get_selected_fields(USERS, array('_id' => MongoID($user_id)), array('_id', 'user_name', 'image', 'email', 'country_code', 'phone_number'))->row();
				$driver_profile = array();
				if ($driver_id != '') {
					$lat_lon = @explode(',', $checkRide->row()->driver['lat_lon']);
					$driver_lat = '';
					$driver_lon = '';
					if (isset($lat_lon[0])) {
						$driver_lat = $lat_lon[0];
					}


					if (isset($lat_lon[1])) {
						$driver_lon = $lat_lon[1];
					}

					$checkDriver = $this->app_model->get_selected_fields(DRIVERS, array('_id' => MongoID($driver_id)), array('_id', 'driver_name', 'image', 'avg_review', 'email', 'dail_code', 'mobile_number', 'vehicle_number', 'vehicle_model'));
					/* Preparing driver information to share with user -- Start */

					$driver_review = 0;
					if (isset($checkDriver->row()->avg_review)) {
						$driver_review = $checkDriver->row()->avg_review;
					}
					$vehicleInfo = $this->app_model->get_selected_fields(MODELS, array('_id' => MongoID($checkDriver->row()->vehicle_model)), array('_id', 'name', 'brand_name'));
					$vehicle_model = '';
					if ($vehicleInfo->num_rows() > 0) {
						$vehicle_model = $vehicleInfo->row()->name;
					}

					$driverImg = '';
					if (isset($checkDriver->row()->image)) {
						$driverImg = $checkDriver->row()->image;
					}

					$driver_profile = array('driver_id' => (string) $checkDriver->row()->_id,
						'driver_name' => (string) $checkDriver->row()->driver_name,
						'driver_email' => (string) $checkDriver->row()->email,
						'driver_image' => $driverImg,
						'driver_review' => (string) floatval($driver_review),
						'driver_lat' => (string) floatval($driver_lat),
						'driver_lon' => (string) floatval($driver_lon),
						'rider_lat' => (string) floatval($checkRide->row()->booking_information['pickup']['latlong']['lat']),
						'rider_lon' => (string) floatval($checkRide->row()->booking_information['pickup']['latlong']['lon']),
						'ride_id' => (string) $ride_id,
						'phone_number' => (string) $checkDriver->row()->dail_code . ' ' . $checkDriver->row()->mobile_number,
						'vehicle_number' => (string) $checkDriver->row()->vehicle_number,
						'vehicle_model' => (string) $vehicle_model
					);
					/* Preparing driver information to share with user -- End */
				}

				/* get driver current location and path */
				$tracking_records = $this->app_model->get_all_details(TRACKING, array('ride_id' => $ride_id));

				$tracking = array();
				if (isset($tracking_records->row()->steps)) {
					$allStages = $tracking_records->row()->steps;
					for ($i = 0; $i < count($allStages); $i++) {
						$lastTime = MongoEPOCH($allStages[$i]['timestamp']);
						$tracking[] = array('on_time' => $lastTime,
							'locality' => $allStages[$i]['locality'],
							'location' => $allStages[$i]['location']
						);
					}
				}
			} else {
				$this->setErrorMessage('error', 'No records found', 'driver_no_records_found');
				redirect('');
			}
		} else {
			$this->setErrorMessage('error', 'Ride id is empty', 'driver_ride_id_empty');
			redirect('');
		}

		if ($this->lang->line('driver_track_the_ride') != '')
			$driver_track_the_ride = stripslashes($this->lang->line('driver_track_the_ride'));
		else
			$driver_track_the_ride = 'Track the ride';
		$this->data['heading'] = $driver_track_the_ride . '  #' . $ride_id;
		$this->data['driver_details'] = $driver_profile;
		$this->data['user_details'] = $user_details;
		$this->data['tracking_details'] = $tracking;
		$this->data['booking_information'] = $checkRide->row()->booking_information;
		$this->data['ride_details'] = $checkRide->row();

		#echo '<pre>'; print_r($tracking); echo '<pre>'; print_r($checkRide->row()->booking_information); echo '<pre>'; print_r($driver_profile); die;

		$this->load->view('site/rides/track_ride', $this->data);
	}
	
	/* This function uploading facebook image of an user to appropriate folder */
	/* This will be called from root/facebook/user.php */
	public function upload_fb_profile_pic(){
		
	    $user_id= $this->input->post('user_id');
		$decoded_image_data=base64_decode($this->input->post('image_data'));
		$path_to_upload=USER_PROFILE_IMAGE.$user_id.'.jpg';
		if(!file_exists($path_to_upload)){
				/* uploading user image */
			$image = @imagecreatefromstring($decoded_image_data);
			$newwidth=210;
			$newheight=210;
			$resized_image = @imagecreatetruecolor($newwidth, $newheight);
			$width = imagesx($image);
			$height = imagesy($image);
			 @imagecopyresized($resized_image, $image, 0, 0, 0, 0, $newwidth, $newheight, $width, $height);
			@imagejpeg($resized_image, $path_to_upload, 100); 
			/* end uploading user image */
			/************************/
			/* uploading user Thumb image */
			$newwidth=100;
			$newheight=100;
			$thumb_image = @imagecreatetruecolor($newwidth, $newheight);
			$width = imagesx($image);
			$height = imagesy($image);
			 @imagecopyresized($thumb_image, $image, 0, 0, 0, 0, $newwidth, $newheight, $width, $height);
			
			if ($thumb_image !== false) {
				$thumpUploadPath = USER_PROFILE_THUMB . $user_id.'.jpg';
				@imagejpeg($thumb_image, $thumpUploadPath, 100);
			}
							
			/* end uploading user Thumb image */
			@imagedestroy($image);
			@imagedestroy($thumb_image);
		}
	}
	
	public function social_rigister_form() { 
        $this->data['heading'] = 'Sign up to ride';
        if ($this->checkLogin('U') == '') {
            $ref_code = (empty($_GET['ref'])) ? '' : $_GET['ref'];

            if ($ref_code != '') {
                $ref_code = base64_decode($ref_code);
                $this->data['referrer_info'] = $this->user_model->get_all_details(USERS, array('unique_code' => $ref_code));
                if ($this->data['referrer_info']->num_rows() > 0) {
                    $this->data['sideMenu'] = 'share_code';
                    $shareDesc = "Sign up with my code " . $this->data['referrer_info']->row()->unique_code . " to get " . $this->data['dcurrencyCode'] . " " . number_format($this->config->item('welcome_amount'), 2) . " bonus amount on " . $this->config->item('email_title');
                    $this->data['shareDesc'] = $shareDesc;
                }
            }

            $this->load->view('site/user/social_register.php', $this->data);
        } else {
            redirect('rider');
        }
    }
	public function check_referral_code(){
		$status='error';
		$referal_code = $this->input->post('referal_code');
		$refererVal = $this->user_model->get_selected_fields(USERS, array('unique_code' => $referal_code), array('email','referral_count'));
		if($refererVal->num_rows()==1){
			$status='success';			
		}	
      
        echo $status; 
	}
    public function ajax_coupon_validation(){
		$returnArr['status']=0;
		$code = $this->input->post('code');
		$pickup_date = $this->input->post('pickup_date');
		if($pickup_date == ''){
			$pickup_date = date('Y-m-d H:i:s');
		}else{
			$pickup_date = $this->input->post('pickup_date'). ':00';
		}
        $checkCode = $this->app_model->get_all_details(PROMOCODE, array('promo_code' => (string)$code));
			if($checkCode->num_rows() > 0){
				if ($checkCode->row()->status == 'Active') {
					$valid_from = strtotime($checkCode->row()->validity['valid_from'] . ' 00:00:00');
					$valid_to = strtotime($checkCode->row()->validity['valid_to'] . ' 23:59:59');
					$date_time = strtotime($pickup_date);
					if (($valid_from <= $date_time) && ($valid_to >= $date_time)) {
						if ($checkCode->row()->usage_allowed > $checkCode->row()->no_of_usage) {
							
							$coupon_usage = array();
							if (isset($checkCode->row()->usage)) {
								$coupon_usage = $checkCode->row()->usage;
							}
                            $user_id = $this->checkLogin('U');
							$usage = $this->app_model->check_user_usage($coupon_usage, $user_id);
							if ($usage < $checkCode->row()->user_usage) {
								$returnArr['status'] = 1;
								if($checkCode->row()->code_type == 'Flat'){
									$returnArr['promo_value'] = $checkCode->row()->promo_value;
									$returnArr['code_type'] = $this->data['dcurrencySymbol'];
								}else if($checkCode->row()->code_type == 'Percent'){
									$returnArr['promo_value'] = $checkCode->row()->promo_value;
									$returnArr['code_type'] = '%';
								}
							}
						}
					}	
				}					
			}	
        $json_encode_new = json_encode($returnArr);
        echo $json_encode_new; 
	}
	
	/**
	*
	*	This function is checks the user is exist or not by email or mobile number
	*	@Param email
	*	@Param dial_code
	*	@Param phone_number
	*	@Param mode (add/edit)
	*
	**/
    public function check_user_duplicate(){
		$returnArr['status']='0';
		
		$email = $this->input->post('email');
		$dial_code = $this->input->post('dial_code');
		$phone_number = $this->input->post('phone_number');
		$mode = $this->input->post('mode');
		
		
        $json_encode_new = json_encode($returnArr);
        echo $json_encode_new; 
	}
	
	/**
	*
	*	This function is checks the user is exist or not by email or mobile number
	*	@Param email
	*
	**/
    public function ajax_check_user_mail_exist(){
		$returnArr['status']='1';
		$returnArr['response']='';
			if ($this->lang->line('driver_login_ucfirst') != '')
			$Login = stripslashes($this->lang->line('driver_login_ucfirst'));
		else
			$Login = 'Login';

		$email = $this->input->post('email');
		if ($this->lang->line('user_already_exist') != '')
			$exist = stripslashes($this->lang->line('user_already_exist'));
		else
			$exist = 'already exist';
			
		if($email  != ''){
			$chkUser = $this->user_model->get_selected_fields(USERS,array('email' => $email),array('_id'));
			if($chkUser->num_rows() > 0){
				$returnArr['status']='0';
				$returnArr['response']='<b>'.$email.' </b>'.$exist.'';
			}
		}
        $json_encode_new = json_encode($returnArr);
        echo $json_encode_new; 
	}
    
    
    
    /**
	*
	*	This function is checks the user's login and proceeds to book ride
	*	@Param posted values
	*
	**/
    public function proceed_to_booking(){
		$pickup_location = $this->input->post('pickup_location');
		$drop_location = $this->input->post('drop_location');
		$pickup_lon = $this->input->post('pickup_lon');
		$pickup_lat = $this->input->post('pickup_lat');
		$drop_lon = $this->input->post('drop_lon');
		$drop_lat = $this->input->post('drop_lat');
        
        $booking_info = array('pickup_location' => $this->input->post('pickup_location'),
                              'drop_location' => $this->input->post('drop_location'),
                              'pickup_lon' => $this->input->post('pickup_lon'),
                              'pickup_lat' => $this->input->post('pickup_lat'),
                              'drop_lon' => $this->input->post('drop_lon'),
                              'drop_lat' => $this->input->post('drop_lat')
        
        );
        $this->session->set_userdata(array(APP_NAME.'_session_tmp_booking_data' => $booking_info));
        
        if($this->checkLogin('U') == ''){
            redirect('rider/login?action='.base_url().'rider/booking');
        } else {
            redirect('rider/booking');
        }
	}
    
    /**
    * 
    * get available drivers for the category and location
    *
    * @param string $pickup_lat  pickup location lat
    * @param string $pickup_lon  pickup location lon
    * @param string $cat_id  category id
    * @return JSON ,available category list
    *
    **/ 
	public function get_nearest_drivers_from_ajax(){
	
		$returnArr['status']='0';
		$pickup_lat = $this->input->post('pickup_lat');
		$pickup_lon = $this->input->post('pickup_lon');
		$categoryID = $this->input->post('cat_id');
        $limit = 1000;
        $catIcons = array();
		
        $location = $this->app_model->find_location(floatval($pickup_lon), floatval($pickup_lat));
        if(isset($location['result'][0]['_id'])){
            $location_id = $location['result'][0]['_id']; 
            if($categoryID == ''){
                $categoryArr = $location['result'][0]['avail_category']; 
                $categoryID = array();
                foreach($categoryArr as $cats){
                    $categoryID[] = MongoID($cats);
                }
                $getCats = $this->app_model->get_selected_fields(CATEGORY,array('_id' => array('$in' => $categoryID)),array('icon_car_image'));
                foreach($getCats->result() as $cat_data){
                    $icon_car_image = ICON_MAP_CAR_IMAGE;
                    if(isset($cat_data->icon_car_image) && $cat_data->icon_car_image != ''){
                        if(file_exists(ICON_IMAGE.$cat_data->icon_car_image)){
                            $icon_car_image = ICON_IMAGE.$cat_data->icon_car_image;
                        }
                    }
                    $catIcons[(string)$cat_data->_id] = $icon_car_image;
                }
            }  
            $coordinates = array(floatval($pickup_lon), floatval($pickup_lat));
            $driversArr = array();
            $driverList = $this->app_model->get_nearest_driver($coordinates, $categoryID, $limit,"","","",$location_id,"all"); 
            if (!empty($driverList['result'])) {
                foreach ($driverList['result'] as $driver) {
                    $lat = $driver['loc']['lat'];
                    $lon = $driver['loc']['lon'];
                    $icon_img = '';
                    $cat_id = (string)$driver['category'];
                    if(isset($catIcons[$cat_id])){
                        $icon_img = $catIcons[$cat_id];
                    }
                    $driversArr[] = array('lat' => $lat,
                                        'lon' => $lon,
                                        'icon_img' => $icon_img
                                    );
                }
            }
            $returnArr['status']='1';
        }
        
		$returnArr['response'] = $driversArr;
		$json_encode_new = json_encode($returnArr);
		echo $json_encode_new; 
	}
    
}

/* End of file user.php */
/* Location: ./application/controllers/site/user.php */