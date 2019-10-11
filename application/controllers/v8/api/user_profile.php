<?php

if (!defined('BASEPATH'))
    exit('No direct script access allowed');

/**
 * 
 * This controller contains the functions related to favourite locations 
 * @author Casperon
 *
 * */
class User_profile extends MY_Controller {

    function __construct() {
        parent::__construct();
        $this->load->helper(array('cookie', 'date', 'form', 'email'));
        $this->load->library(array('encrypt', 'form_validation', 'twilio'));
        $this->load->model(array('user_model'));
		
		
		/* Authentication Begin */
        $headers = $this->input->request_headers();
		header('Content-type:application/json;charset=utf-8');
		if (array_key_exists("Authkey", $headers)) $auth_key = $headers['Authkey']; else $auth_key = "";
		if(stripos($auth_key,APP_NAME) === false) {
			$cf_fun= $this->router->fetch_method();
			$apply_function = array('user_reset_password','resend_otp');
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
					$apply_function = array('login_user','social_Login');
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
	*	This function will returns the user profile information
	*
	**/	
	public function get_user_profile() {
		$returnArr['status'] = '0';
		$returnArr['response'] = '';
		try{
			$user_id = $this->input->post('user_id');
			if($user_id!=""){
				$userInfo = $this->app_model->get_all_details(USERS, array('_id' => MongoID($user_id)));
				if($userInfo->num_rows() > 0 ){
					$name = ""; $country_code = ""; $phone_number = ""; $email = ""; 
					$image = USER_PROFILE_IMAGE_DEFAULT; $ratting = "0";
					$lang = "en"; if($this->config->item('default_lang_code')!="") $lang = $this->config->item('default_lang_code');
					
					if (isset($userInfo->row()->user_name) && ($userInfo->row()->user_name!="")) $name = $userInfo->row()->user_name;
					if (isset($userInfo->row()->country_code) && ($userInfo->row()->country_code!="")) $country_code = $userInfo->row()->country_code;
					if (isset($userInfo->row()->phone_number) && ($userInfo->row()->phone_number!="")) $phone_number = $userInfo->row()->phone_number;
					if (isset($userInfo->row()->email) && ($userInfo->row()->email!="")) $email = $userInfo->row()->email;
					if (isset($userInfo->row()->avg_review) && ($userInfo->row()->avg_review!="")) $ratting = $userInfo->row()->avg_review;
					if (isset($userInfo->row()->lang_code) && ($userInfo->row()->lang_code!="")) $lang = $userInfo->row()->lang_code;
					
					if (isset($userInfo->row()->image) && ($userInfo->row()->image!=""))  $image = USER_PROFILE_IMAGE . $userInfo->row()->image;
					
					$user_info  = array("name"=>(string)$name,
												"country_code"=>(string)$country_code,
												"phone_number"=>(string)$phone_number,
												"email"=>(string)$email,
												"image"=>(string)base_url().$image,
												"ratting"=>(string)$ratting,
												"lang"=>(string)$lang,
											);
										
					if(empty($user_info)){
						$returnArr['response'] = $this->format_string("Something went wrong, please try again later!", "something_went_wrong");
					}else{
						$returnArr['status'] = '1';
						$returnArr['response'] = $user_info;
					}
				}else{
					$returnArr['response'] = $this->format_string("Invalid User", "invalid_user");
				}
			} else {
				$returnArr['response'] = $this->format_string("Some Parameters Missing", "some_parameters_missing");
			}
		}catch(Exception $e){
			$returnArr['response'] = $this->format_string("Error in Connection", "error_in_connection");
		}
		$json_encode = json_encode($returnArr, JSON_PRETTY_PRINT);
        echo $this->cleanString($json_encode);
	}

    /**
     *
     * This function add the location to favourite list
     *
     * */
    public function add_favourite_location() { 
        $responseArr['status'] = '0';
        $responseArr['message'] = '';

        $title = trim($this->input->post('title'));
        $address = trim($this->input->post('address'));
        $user_id = $this->input->post('user_id');
        $longitude = $this->input->post('longitude');
        $latitude = $this->input->post('latitude');
        $loc_key = 'lat_' . str_replace('.', '-', $longitude) . 'lon_' . str_replace('.', '-', $latitude);


        if (is_array($this->input->post())) {
            $chkValues = count(array_filter($this->input->post()));
        } else {
            $chkValues = 0;
        }

        if ($chkValues >= 5) {
            $fav_condition = array('user_id' => MongoID($user_id));
            $checkUserInFav = $this->user_model->get_all_details(FAVOURITE, $fav_condition);
			$titleExist = FALSE;
			if ($checkUserInFav->num_rows() > 0) {
				if (isset($checkUserInFav->row()->fav_location)) {
					$fLoc = $checkUserInFav->row()->fav_location;
					$fvLoc = in_array($title,array_column($fLoc,"title"));
					if($fvLoc) $titleExist = TRUE;
				}
			}
            if (isset($checkUserInFav->row()->fav_location[$loc_key]) || $titleExist == TRUE) {
				if($titleExist){
					$responseArr['message'] = $this->format_string('Location name already exist in your favourite list', 'location_name_already_exist_in_favourite');
				}else{
					$responseArr['message'] = $this->format_string('Location already exist in your favourite list', 'location_already_exist_in_favourite');
				}
            } else {
                if ($checkUserInFav->num_rows() == 0) {
                    $dataArr = array('user_id' => MongoID($user_id),
                        'fav_location' => array($loc_key => array('title' => $title,
                                'address' => $address,
                                'geo' => array('longitude' => floatval($longitude),
                                    'latitude' => floatval($latitude)
                                )
                            )
                        )
                    );
                    $this->user_model->simple_insert(FAVOURITE, $dataArr);
                    $responseArr['status'] = '1';
                    $responseArr['loc_key'] = $loc_key;
                    $responseArr['message'] = $this->format_string('Location added to favourite', 'location_added_to_favourite');
                } else {
                    $dataArr = array('fav_location.' . $loc_key => array('title' => $title,
                            'address' => $address,
                            'geo' => array('longitude' => floatval($longitude),
                                'latitude' => floatval($latitude)
                            )
                        )
                    );
                    $this->user_model->set_to_field(FAVOURITE, $fav_condition, $dataArr);
                    $responseArr['status'] = '1';
					$responseArr['loc_key'] = $loc_key;
                    $responseArr['message'] = $this->format_string('Location added to favourite', 'location_added_to_favourite');
                }
            }
        } else {
            $responseArr['message'] = $this->format_string('Some Parameters are missing', 'some_parameters_missing');
        }

        $json_encode = json_encode($responseArr, JSON_PRETTY_PRINT);
        echo $this->cleanString($json_encode);
    }

    /**
     *
     * This function edit the location from favourite list
     *
     * */
    public function edit_favourite_location() {
        $responseArr['status'] = '0';
        $responseArr['message'] = '';

        $title = trim($this->input->post('title'));
        $address = trim($this->input->post('address'));
        $user_id = $this->input->post('user_id');
        $longitude = $this->input->post('longitude');
        $latitude = $this->input->post('latitude');
        $loc_key = $this->input->post('location_key');
		
		$loc_key_new = 'lat_' . str_replace('.', '-', $longitude) . 'lon_' . str_replace('.', '-', $latitude);

        if (is_array($this->input->post())) {
            $chkValues = count(array_filter($this->input->post()));
        } else {
            $chkValues = 0;
        }

        if ($chkValues >= 5) {
            $fav_condition = array('user_id' => MongoID($user_id));
            $checkUserInFav = $this->user_model->get_all_details(FAVOURITE, $fav_condition);
			$titleExist = FALSE;
			if ($checkUserInFav->num_rows() > 0) {
				if (isset($checkUserInFav->row()->fav_location)) {
                    $fLocation=array();
					$fLoc = $checkUserInFav->row()->fav_location;
                    foreach($fLoc as $key=>$Loc) {
                        if($key!=$loc_key) {
                            $fLocation[]=$Loc;
                        }
                    }
                    
					$fvLoc = in_array($title,array_column($fLocation,"title"));
					if($fvLoc) $titleExist = TRUE;
                    
				}
			}
				
			$fLs = array();
			if ($checkUserInFav->num_rows() > 0) {
				if (isset($checkUserInFav->row()->fav_location)) {
					$fLoc = $checkUserInFav->row()->fav_location;
					foreach($fLoc as $key=>$value){
						$fLs = array($key=>$value['title']);
					}
				}
			}
			
            if (!isset($checkUserInFav->row()->fav_location[$loc_key]) || $titleExist == TRUE) {
                $responseArr['status'] = '0';
				if($titleExist){
					$responseArr['message'] = $this->format_string('Location name already exist in your favourite list', 'location_name_already_exist_in_favourite');
				}else{
					$responseArr['message'] = $this->format_string('No records found for this location', 'no_records_found_for_location');
				}                
            } else {
				$titleExist = FALSE;
				if($key = array_search($title,$fLs)){
					if($loc_key!=$key) $titleExist = TRUE;
				}
				if($titleExist){
					$responseArr['message'] = $this->format_string('Location name already exist in your favourite list', 'location_name_already_exist_in_favourite');
				}else{
					$dataArr = array('fav_location.' . $loc_key_new => array('title' => $title,
							'address' => $address,
							'geo' => array('longitude' => floatval($longitude),
								'latitude' => floatval($latitude)
							)
						)
					);
					$this->user_model->remove_favorite_location($fav_condition, 'fav_location.' . $loc_key);
					$this->user_model->set_to_field(FAVOURITE, $fav_condition, $dataArr);

					$responseArr['status'] = '1';
					$responseArr['message'] = $this->format_string('Updated successfully', 'updated_successfully');
				}
                
            }
        } else {
            $responseArr['message'] = $this->format_string('Some Parameters are missing', 'some_parameters_missing');
        }
        $json_encode = json_encode($responseArr, JSON_PRETTY_PRINT);
        echo $this->cleanString($json_encode);
    }

    /**
     *
     * This function remove the location from favourite list
     *
     * */
    public function remove_favourite_location() {
        $responseArr['status'] = '0';
        $responseArr['message'] = '';
        $loc_key = $this->input->post('location_key');
        $user_id = $this->input->post('user_id');


        if (is_array($this->input->post())) {
            $chkValues = count(array_filter($this->input->post()));
        } else {
            $chkValues = 0;
        }

        if ($chkValues >= 2) {
            $fav_condition = array('user_id' => MongoID($user_id));
            $checkUserInFav = $this->user_model->get_all_details(FAVOURITE, $fav_condition);

            if (!isset($checkUserInFav->row()->fav_location[$loc_key])) {
                $responseArr['status'] = '0';
                $responseArr['message'] = $this->format_string('No records found for this location', 'no_records_found_for_location');
            } else {
                $this->user_model->remove_favorite_location($fav_condition, 'fav_location.' . $loc_key);
                $responseArr['status'] = '1';
                $responseArr['message'] = $this->format_string('Location removed successfully', 'location_removed_successfully');
            }
        } else {
            $responseArr['message'] = $this->format_string('Some Parameters are missing', 'some_parameters_missing');
        }
        $json_encode = json_encode($responseArr, JSON_PRETTY_PRINT);
        echo $this->cleanString($json_encode);
    }

    /**
     *
     * This function displays the all favourite locations
     *
     * */
    public function display_favourite_location() {
        $responseArr['status'] = '0';
        $responseArr['response'] = '';
        $user_id = $this->input->post('user_id');

        if ($user_id != '') {
            $fav_condition = array('user_id' => MongoID($user_id));
            $checkUserInFav = $this->user_model->get_all_details(FAVOURITE, $fav_condition);
            if ($checkUserInFav->num_rows() == 0) {
                $responseArr['response'] = $this->format_string('No records found for in your favourite list', 'no_records_found_in_your_favourite_list');
            } else {
                if (isset($checkUserInFav->row()->fav_location)) {
                    $favLocations = $checkUserInFav->row()->fav_location;
                } else {
                    $favLocations = array();
                }
                $favLocatArr = array();
                foreach ($favLocations as $key => $val) {
                    $favLocatArr[] = array('location_key' => $key,
                        'title' => $val['title'],
                        'address' => $val['address'],
                        'longitude' => $val['geo']['longitude'],
                        'latitude' => $val['geo']['latitude'],
                    );
                }
                $totalFavLoc = count($favLocations);
                if ($totalFavLoc > 0) {
                    if (empty($favLocatArr)) {
                        $favLocatArr = json_decode("{}");
                    }
                    $responseArr['status'] = '1';
                    $responseArr['response'] = array('locations' => $favLocatArr, 'total_count' => $totalFavLoc);
                } else {
                    $responseArr['response'] = $this->format_string('No records found for in your favourite list', 'no_records_found_in_your_favourite_list');
                }
            }
        } else {
            $responseArr['response'] = $this->format_string('Some Parameters are missing', 'some_parameters_missing');
        }
        $json_encode = json_encode($responseArr, JSON_PRETTY_PRINT);
        echo $this->cleanString($json_encode);
    }

    /**
     *
     * This function changes the users name 
     * */
    public function change_user_name() {
        $responseArr['status'] = '0';
        $responseArr['response'] = '';
        $user_id = $this->input->post('user_id');
        $user_name = $this->input->post('user_name');
        if ($user_id != '' && $user_name != '') {
            $condition = array('_id' => MongoID($user_id));
            $checkUser = $this->user_model->get_selected_fields(USERS, $condition, array('_id'));
            if ($checkUser->num_rows() == 1) {
                $dataArr = array('user_name' => $user_name);
                $this->user_model->update_details(USERS, $dataArr, $condition);
                $responseArr['status'] = '1';
                $responseArr['response'] = $this->format_string('User name changed successfully', 'username_changed_successfully');
                $responseArr['user_name'] = $user_name;
            } else {
                $responseArr['response'] = $this->format_string('Invalid request', 'invalid_request');
            }
        } else {
            $responseArr['response'] = $this->format_string('Some Parameters are missing', 'some_parameters_missing');
        }

        $json_encode = json_encode($responseArr, JSON_PRETTY_PRINT);
        echo $this->cleanString($json_encode);
    }

    /**
     *
     * This function changes the users mobile 
     * */
    public function change_user_mobile_number() {
        $responseArr['status'] = '0';
        $responseArr['response'] = '';
        $user_id = $this->input->post('user_id');
        $country_code = $this->input->post('country_code');
        $phone_number = $this->input->post('phone_number');
        $otp = (string) $this->input->post('otp');

        if (is_array($this->input->post())) {
            $chkValues = count(array_filter($this->input->post()));
        } else {
            $chkValues = 0;
        }
        if ($chkValues >= 3) {
            $phcondition = array('phone_number' => $phone_number, 'country_code' => $country_code);
            $checkphUser = $this->user_model->get_selected_fields(USERS, $phcondition, array('_id'));
            if ($checkphUser->num_rows() == 0) {
                $condition = array('_id' => MongoID($user_id));
                $checkUser = $this->user_model->get_selected_fields(USERS, $condition, array('_id', 'user_name', 'email', 'country_code', 'phone_number','emergency_contact'));
                if ($checkUser->num_rows() == 1) {
					$em_mobile = ""; $em_mobile_code = "";
					if(isset($checkUser->row()->emergency_contact)){
						$em_mobile = (string)$checkUser->row()->emergency_contact['em_mobile'];
						$em_mobile_code = (string)$checkUser->row()->emergency_contact['em_mobile_code'];
					}
					
					$proC = TRUE;		
					if($country_code==$em_mobile_code || $phone_number==$em_mobile){
						if($phone_number==$em_mobile){
							if($country_code==$em_mobile_code){
								$proC = FALSE;
							}
						}
					}
					if($proC == TRUE){
						if ($otp != '') {
							$dataArr = array('country_code' => $country_code, 'phone_number' => $phone_number);
							$this->user_model->update_details(USERS, $dataArr, $condition);
						}

						if ($otp == '') {
							/*                         * ****  mobile otp section  start**** */
							$phone_code = $country_code;
							if (substr($phone_code, 0, 1) == '+') {
								$phone_code = $phone_code;
							} else {
								$phone_code = '+' . $phone_code;
							}
							$otp_number = rand(10000, 99999);
							$from = $this->config->item('twilio_number');
							$to = $phone_code . $phone_number;
							$user_name = $checkUser->row()->user_name;
							$user_email = $checkUser->row()->email;
							$dear = $this->format_string('Dear', 'dear');
							$your = $this->format_string('your', 'your');
							$one_time_password_is = $this->format_string('one time password is', 'one_time_password_is');
							
							$smsInfo = array("otp_number"=>$otp_number,
										"user_name"=>$user_name,
										"phone_code"=>$phone_code,
										"phone_number"=>$phone_number
									);
							$this->sms_model->passanger_change_mobile_otp($smsInfo);
							
							$this->user_model->update_details(USERS, array('mobile_otp' => $otp_number), $condition);

							$responseArr['otp'] = $otp_number;
							if ($this->config->item('twilio_account_type') == 'sandbox') {
								$otp_status = 'development';
							} else {
								$otp_status = 'production';
							}
							$responseArr['otp_status'] = $otp_status;
						}
						if ($otp == '') {
							$responseArr['country_code'] = (string) $country_code;
							$responseArr['phone_number'] = (string) $phone_number;
							$responseArr['response'] = $this->format_string('otp sent successfully', 'otp_sent');
						} else {
							$responseArr['response'] = $this->format_string('User mobile number changed successfully', 'user_mobile_number_changed');
							$responseArr['country_code'] = (string) $checkUser->row()->country_code;
							$responseArr['phone_number'] = (string) $checkUser->row()->phone_number;
						}
						$responseArr['status'] = '1';
					}else{
						$responseArr['response'] = $this->format_string('This number has been already added in your emergency contact, so you cannot update this number', 'already_in_emergency_contact');
					}
                } else {
                    $responseArr['response'] = $this->format_string('Invalid request', 'invalid_request');
                }
            } else {
                $responseArr['response'] = $this->format_string('This mobile number already registered', 'mobile_number_already_registered');
            }
        } else {
            $responseArr['response'] = $this->format_string('Some Parameters are missing', 'some_parameters_missing');
        }

        $json_encode = json_encode($responseArr, JSON_PRETTY_PRINT);
        echo $this->cleanString($json_encode);
    }
	
	/**
	*
	*	This function will upload the user profile picture
	*
	**/	
	public function change_profile_image() {
		$returnArr['status'] = '0';
		$returnArr['response'] = '';
		$returnArr['image_url'] = '';
		try{
			$user_id = $this->input->post('user_id');
			if($user_id!=""){
				$userInfo = $this->app_model->get_selected_fields(USERS, array('_id' => MongoID($user_id)),array('image'));
				if($userInfo->num_rows() > 0 ){
					$config['overwrite'] = FALSE;
					$config['encrypt_name'] = TRUE;
					$config['allowed_types'] = 'jpg|jpeg|gif|png';
					$config['max_size'] = 2000;
					$config['upload_path'] = './images/users';
					$this->load->library('upload', $config);
					
					if (!$this->upload->do_upload('user_image')){
						$returnArr['response'] = $this->format_string("Error in updating profile picture", "profile_picture_updated_error");
						#$returnArr['response'] = (string)$this->upload->display_errors();
					}else{
						$imgDetails = $this->upload->data();
						$ImageName = $imgDetails['file_name'];
						
						$this->ImageResizeWithCrop(600, 600, $ImageName, './images/users/');
						@copy('./images/users/' . $ImageName, './images/users/thumb/' . $ImageName);
						$this->ImageResizeWithCrop(210, 210, $ImageName, './images/users/thumb/');
					
						$returnArr['image_url'] = base_url().USER_PROFILE_THUMB.$ImageName;	
						
						$condition =  array('_id' => MongoID($user_id));
						$this->app_model->update_details(USERS, array('image' => $ImageName,'modified' => date("Y-m-d H:i:s")), $condition);
						
						$returnArr['response'] = $this->format_string("Profile picture updated successfully", "profile_picture_updated_success");
						$returnArr['status'] = '1';
					}
				}else{
					$returnArr['response'] = $this->format_string("Invalid User", "invalid_user");
				}
			} else {
				$returnArr['response'] = $this->format_string("Some Parameters Missing", "some_parameters_missing");
			}
		}catch(Exception $e){
			$returnArr['response'] = $this->format_string("Error in Connection", "error_in_connection");
		}
		$json_encode = json_encode($returnArr, JSON_PRETTY_PRINT);
        echo $this->cleanString($json_encode);
	}

    /**
     *
     * This function changes the users password
     * */
    public function send_otp_email($otpCode = '', $user_name = '', $user_email = '') {
        $newsid = '4';
        $template_values = $this->user_model->get_email_template($newsid,$this->data['langCode']);
		$adminnewstemplateArr = array('email_title' => $this->config->item('email_title'), 'logo' => $this->config->item('logo_image'), 'footer_content' => $this->config->item('footer_content'), 'meta_title' => $this->config->item('meta_title'), 'site_contact_mail' => $this->config->item('site_contact_mail'));
		extract($adminnewstemplateArr);
        $subject = $template_values['subject'];
        $adminnewstemplateArr = array('email_title' => $this->config->item('email_title'), 'logo' => $this->config->item('logo_image'), 'footer_content' => $this->config->item('footer_content'), 'meta_title' => $this->config->item('meta_title'), 'site_contact_mail' => $this->config->item('site_contact_mail'));
        extract($adminnewstemplateArr);
        $message = '<!DOCTYPE HTML>
			<html>
			<head>
			<meta http-equiv="Content-Type" content="text/html; charset=utf-8">
			<meta name="viewport" content="width=device-width"/>
			<title>' . $template_values['subject'] . '</title>
			<body>';
        include($template_values['templateurl']);
        $message .= '</body>
			</html>';
		$sender_email = $this->config->item('site_contact_mail');
        $sender_name = $this->config->item('email_title');
        
        $email_values = array('mail_type' => 'html',
            'from_mail_id' => $sender_email,
            'mail_name' => $sender_name,
            'to_mail_id' => $user_email,
            'subject_message' => $subject,
            'body_messages' => $message
        );
        $email_send_to_common = $this->user_model->common_email_send($email_values);
    }

    /**
     *
     * This function changes the users password
     * */
    public function change_user_password() {
        $responseArr['status'] = '0';
        $responseArr['response'] = '';
        $user_id = $this->input->post('user_id');
        $password = $this->input->post('password');
        $new_password = (string) $this->input->post('new_password');

        if (is_array($this->input->post())) {
            $chkValues = count(array_filter($this->input->post()));
        } else {
            $chkValues = 0;
        }
        if ($chkValues >= 3) {
            if (strlen($new_password) >= 6) {
                $condition = array('_id' => MongoID($user_id), 'password' => md5($password));
                $checkUser = $this->user_model->get_selected_fields(USERS, $condition, array('_id'));
                if ($checkUser->num_rows() == 1) {
                    $condition = array('_id' => MongoID($user_id));
                    $dataArr = array('password' => md5($new_password));
                    $this->user_model->update_details(USERS, $dataArr, $condition);
                    $responseArr['status'] = '1';
                    $responseArr['response'] = $this->format_string('User password changed successfully', 'password_changed');
                } else {
                    $responseArr['response'] = $this->format_string('Your current password is not matching', 'password_not_matching');
                }
            } else {
                $responseArr['response'] = $this->format_string('Password should be atleast 6 characters', 'password_should_be_6_characters');
            }
        } else {
            $responseArr['response'] = $this->format_string('Some Parameters are missing', 'some_parameters_missing');
        }

        $json_encode = json_encode($responseArr, JSON_PRETTY_PRINT);
        echo $this->cleanString($json_encode);
    }

    public function emergency_contact_add_edit() {
        $responseArr['status'] = '0';
        $responseArr['response'] = '';
        $user_id = $this->input->post('user_id');
        $em_name = $this->input->post('em_name');
        $em_email = $this->input->post('em_email');
        $em_mobile = $this->input->post('em_mobile');
        $em_mobile_code = $this->input->post('em_mobile_code');

        if (is_array($this->input->post())) {
            $chkValues = count(array_filter($this->input->post()));
        } else {
            $chkValues = 0;
        }
        if ($chkValues >= 5) {
            $condition = array('_id' => MongoID($user_id));
            $checkUser = $this->user_model->get_selected_fields(USERS, $condition, array('_id', 'user_name', 'email', 'phone_number', 'emergency_contact','country_code'));

            $email_verify_status = 'No';
            $mobile_verify_status = 'No';
			if(isset($checkUser->row()->emergency_contact)){
				if (array_key_exists('em_email',$checkUser->row()->emergency_contact)) {
					if (isset($checkUser->row()->emergency_contact['verification']['email']))
						$email_verify_status = $checkUser->row()->emergency_contact['verification']['email'];
					if (isset($checkUser->row()->emergency_contact['verification']['mobile']))
						$mobile_verify_status = $checkUser->row()->emergency_contact['verification']['mobile'];
					if ($checkUser->row()->emergency_contact['em_email'] != $em_email) {
						$email_verify_status = 'No';
					}
					if ($checkUser->row()->emergency_contact['em_mobile'] != $em_mobile) {
						$mobile_verify_status = 'No';
					}
				}
			}

            $vfyArr = array('email' => $email_verify_status, 'mobile' => $mobile_verify_status);

            if ($checkUser->num_rows() == 1) {
                if ($checkUser->row()->email != $em_email && ($checkUser->row()->phone_number != $em_mobile || $checkUser->row()->country_code != $em_mobile_code)) {

                    $em_dataArr = array('emergency_contact' => array('em_name' => $em_name, 'em_email' => $em_email, 'em_mobile' => $em_mobile, 'em_mobile_code' => $em_mobile_code), 'verification' => $vfyArr);
                    if (isset($checkUser->row()->emergency_contact)) {
                        if (is_array($checkUser->row()->emergency_contact)) {
                            if (!empty($checkUser->row()->emergency_contact)) {
                                $em_dataArr = array('emergency_contact.em_name' => $em_name, 'emergency_contact.em_email' => $em_email, 'emergency_contact.em_mobile' => $em_mobile, 'emergency_contact.em_mobile_code' => $em_mobile_code, 'emergency_contact.verification' => $vfyArr);
                            }
                        }
                    }

                    $em_dataMailArr = array('em_name' => $em_name, 'em_email' => $em_email, 'em_mobile' => $em_mobile, 'em_mobile_code' => $em_mobile_code);


                    if (isset($checkUser->row()->emergency_contact['em_email'])) {
                        $olderEmail = $checkUser->row()->emergency_contact['em_email'];
                    } else {
                        $olderEmail = '';
                    }
					if (isset($checkUser->row()->emergency_contact['em_mobile'])) {
                            $olderMobile = $checkUser->row()->emergency_contact['em_mobile'];
                    } else {
                            $olderMobile = '';
                    }

                    $this->user_model->update_details(USERS, $em_dataArr, $condition);

                    if (isset($checkUser->row()->emergency_contact)) {
                        if ($olderEmail == $em_email && $olderMobile==$em_mobile) {
                            $responseArr['response'] = $this->format_string('Emergency contact updated successfully', 'emergency_contact_updated');
                        } else {
                            $this->emergency_contact_verification_request($checkUser, $em_dataMailArr);
                            $responseArr['response'] = $this->format_string('Emergency contact added successfully', 'emergency_contact_added');
                        }
                    } else {
                        $this->emergency_contact_verification_request($checkUser, $em_dataMailArr);
                        $responseArr['response'] = $this->format_string('Emergency contact added successfully', 'emergency_contact_added');
                    }


                    $responseArr['status'] = '1';
                    $responseArr['response'] = $this->format_string('Emergency contact added successfully', 'emergency_contact_added');
                } else {
                    $responseArr['response'] = $this->format_string('Sorry, You can not add your details', 'you_cannot_add_your_details');
                }
            } else {
                $responseArr['response'] = $this->format_string('This user does not exist', 'user_not_exists');
            }
        } else {
            $responseArr['response'] = $this->format_string('Some Parameters are missing', 'some_parameters_missing');
        }
        $json_encode = json_encode($responseArr, JSON_PRETTY_PRINT);
        echo $this->cleanString($json_encode);
    }

    public function emergency_contact_verification_request($user_info, $contactArr) {
        /* ---------------SMS--------------------- */
        $phone_code = $contactArr['em_mobile_code'];
        $phone_number = $contactArr['em_mobile'];
        if (substr($phone_code, 0, 1) == '+') {
            $phone_code = $phone_code;
        } else {
            $phone_code = '+' . $phone_code;
        }
        $otp_number = rand(10000, 99999);
        $from = $this->config->item('twilio_number');
        $to = $phone_code . $phone_number;
        $em_user_name = $contactArr['em_name'];
        $em_user_email = $contactArr['em_email'];
        $user_name = $user_info->row()->user_name;
        $user_id = $user_info->row()->_id;
		
		$smsInfo = array("em_user_name"=>$em_user_name,
										"user_name"=>$user_name,
										"otp_number"=>$otp_number,
										"phone_code"=>$phone_code,
										"phone_number"=>$phone_number
									);
        $this->sms_model->emergency_contact_update($smsInfo);

        $condition = array('_id' => MongoID($user_id));
        $this->user_model->update_details(USERS, array('emergency_contact.mobile_otp' => $otp_number), $condition);

        $responseArr['otp'] = $otp_number;
        if ($this->config->item('twilio_account_type') == 'sandbox') {
            $otp_status = 'development';
        } else {
            $otp_status = 'production';
        }
        $responseArr['otp_status'] = $otp_status;

        /* --------------------Email-------------------- */
        $newsid = '5';
        $confirm_link = base_url() . 'emergency-contact/confirm?c=' . md5($otp_number) . '&u=' . $user_id;
        $template_values = $this->user_model->get_email_template($newsid,$this->data['langCode']);
		$adminnewstemplateArr = array('email_title' => $this->config->item('email_title'), 'logo' => $this->config->item('logo_image'), 'footer_content' => $this->config->item('footer_content'), 'meta_title' => $this->config->item('meta_title'), 'site_contact_mail' => $this->config->item('site_contact_mail'));
		extract($adminnewstemplateArr);
        $message = '<!DOCTYPE HTML>
			<html>
			<head>
			<meta http-equiv="Content-Type" content="text/html; charset=utf-8">
			<meta name="viewport" content="width=device-width"/>
			<title>' . $template_values['subject'] . '</title>
			<body>';
        include($template_values['templateurl']);
        $message .= '</body>
			</html>';
		$sender_email = $this->config->item('site_contact_mail');
        $sender_name = $this->config->item('email_title');
        
        $email_values = array('mail_type' => 'html',
            'from_mail_id' => $sender_email,
            'mail_name' => $sender_name,
            'to_mail_id' => $em_user_email,
            'subject_message' => $template_values['subject'],
            'body_messages' => $message
        );
        $email_send_to_common = $this->user_model->common_email_send($email_values);
        return $responseArr;
    }

    public function emergency_contact_view() {
        $responseArr['status'] = '0';
        $responseArr['response'] = '';
        $user_id = $this->input->post('user_id');

        if ($user_id != '') {
            $condition = array('_id' => MongoID($user_id));
            $checkUser = $this->user_model->get_selected_fields(USERS, $condition, array('_id', 'user_name', 'emergency_contact'));
            if ($checkUser->num_rows() == 1) {
                if (isset($checkUser->row()->emergency_contact)) {
                    if (count($checkUser->row()->emergency_contact) > 0) {
                        $emgaArr = $checkUser->row()->emergency_contact;

                        if (isset($emgaArr['verification'])) {
                            $vefifyStatus = array('mobile' => $emgaArr['verification']['mobile'],
                                'email' => $emgaArr['verification']['email']
                            );
                        } else {
                            $vefifyStatus = array('mobile' => 'No',
                                'email' => 'No'
                            );
                        }

                        $emergency_contact = array('name' => $emgaArr['em_name'],
                            'email' => $emgaArr['em_email'],
                            'code' => $emgaArr['em_mobile_code'],
                            'mobile' => $emgaArr['em_mobile'],
                            'verification_status' => $vefifyStatus);

                        if (empty($emergency_contact)) {
                            $emergency_contact = json_decode("{}");
                        }
                        $responseArr['emergency_contact'] = $emergency_contact;

                        $responseArr['status'] = '1';
                    } else {
                        $responseArr['response'] = $this->format_string('Emergency contact is not available', 'emergency_contact_unavailable');
                    }
                } else {
                    $responseArr['response'] = $this->format_string('Sorry, You have not set emergency contact yet', 'you_have_not_added_emergency_contacty_yet');
                }
            } else {
                $responseArr['response'] = $this->format_string('This user does not exist', 'user_not_exists');
            }
        } else {
            $responseArr['response'] = $this->format_string('Some Parameters are missing', 'some_parameters_missing');
        }
        $json_encode = json_encode($responseArr, JSON_PRETTY_PRINT);
        echo $this->cleanString($json_encode);
    }

    public function emergency_contact_delete() {
        $responseArr['status'] = '0';
        $responseArr['response'] = '';
        $user_id = $this->input->post('user_id');

        if ($user_id != '') {
            $condition = array('_id' => MongoID($user_id));
            $checkUser = $this->user_model->get_selected_fields(USERS, $condition, array('_id', 'user_name', 'emergency_contact'));
            if ($checkUser->num_rows() == 1) {
                if (isset($checkUser->row()->emergency_contact)) {
                    $em_dataArr = array();
                    $this->user_model->update_details(USERS, array('emergency_contact' => $em_dataArr), $condition);
                    $responseArr['response'] = $this->format_string('Contact deleted successfully', 'contact_deleted');
                    $responseArr['status'] = '1';
                } else {
                    $responseArr['response'] = $this->format_string('Sorry, You have not set emergency contact yet', 'you_have_not_added_emergency_contacty_yet');
                }
            } else {
                $responseArr['response'] = $this->format_string('This user does not exist', 'user_not_exists');
            }
        } else {
            $responseArr['response'] = $this->format_string('Some Parameters are missing', 'some_parameters_missing');
        }
        $json_encode = json_encode($responseArr, JSON_PRETTY_PRINT);
        echo $this->cleanString($json_encode);
    }

    public function emergency_contact_alert() {
        $responseArr['status'] = '0';
        $responseArr['response'] = '';
        $user_id = $this->input->post('user_id');
        $latitude = $this->input->post('latitude');
        $longitude = $this->input->post('longitude');

        if (is_array($this->input->post())) {
            $chkValues = count(array_filter($this->input->post()));
        } else {
            $chkValues = 0;
        }

        if ($chkValues >= 3) {
            $condition = array('_id' => MongoID($user_id));
            $checkUser = $this->user_model->get_selected_fields(USERS, $condition, array('_id', 'user_name', 'emergency_contact'));
            if ($checkUser->num_rows() == 1) {
                if (isset($checkUser->row()->emergency_contact)) {
                    if (count($checkUser->row()->emergency_contact) > 0) {
                           
                        if($checkUser->row()->emergency_contact['verification']['email'] == 'Yes' && $checkUser->row()->emergency_contact['verification']['mobile'] == 'Yes'){
                        
                            $latlng = $latitude . ',' . $longitude;
                            $gmap = file_get_contents("https://maps.googleapis.com/maps/api/geocode/json?latlng=" . $latlng . "&sensor=false".$this->data['google_maps_api_key']);
                            $mapValues = json_decode($gmap)->results;
                            $formatted_address = $mapValues[0]->formatted_address;

                            $this->send_alert_notification_to_emergency_contact($checkUser->row()->user_name, $checkUser->row()->emergency_contact, $formatted_address);
                            $responseArr['response'] = $this->format_string('Alert notification sent successfully', 'alert_notification_sent');
                            $responseArr['status'] = '1';
                        } else {
                            $responseArr['response'] = $this->format_string('Your emergency contacts are not verified yet', 'your_emergency_contacts_not_verified');
                        }
                    } else {
                        $responseArr['response'] = $this->format_string('Emergency contact is not available', 'emergency_contact_unavailable');
                    }
                } else {
                    $responseArr['response'] = $this->format_string('Sorry, You have not set emergency contact yet', 'you_have_not_added_emergency_contacty_yet');
                }
            } else {
                $responseArr['response'] = $this->format_string('This user does not exist', 'user_not_exists');
            }
        } else {
            $responseArr['response'] = $this->format_string('Some Parameters are missing', 'some_parameters_missing');
        }
        $json_encode = json_encode($responseArr, JSON_PRETTY_PRINT);
        echo $this->cleanString($json_encode);
    }

    public function send_alert_notification_to_emergency_contact($user_name, $contactArr, $currentLocation = '') {
        /* ---------------SMS--------------------- */
        $phone_code = $contactArr['em_mobile_code'];
        $phone_number = $contactArr['em_mobile'];
        if (substr($phone_code, 0, 1) == '+') {
            $phone_code = $phone_code;
        } else {
            $phone_code = '+' . $phone_code;
        }

        $from = $this->config->item('twilio_number');
        $to = $phone_code . $phone_number;
        $em_user_name = $contactArr['em_name'];
        $em_user_email = $contactArr['em_email'];
        $user_name = $user_name;
		
		$smsInfo = array("em_user_name"=>$em_user_name,
										"user_name"=>$user_name,
										"phone_code"=>$phone_code,
										"phone_number"=>$phone_number
									);
        $this->sms_model->emergency_alert($smsInfo);
		
		$messagearr=array();
		if ($this->config->item('twilio_account_type') == 'sandbox') {
			$count =120;
		} else {
			$count =160;
		}
		
	    $messagearr=$this->sms_chunk_split($message,$count);
		
		foreach($messagearr as $meschunk) {
		 
		  $response = $this->sms_model->send_common_sms($from, $to, $meschunk);

		}

        if ($this->config->item('twilio_account_type') == 'sandbox') {
            $otp_status = 'development';
        } else {
            $otp_status = 'production';
        }
        $responseArr['otp_status'] = $otp_status;

        /* --------------------Email-------------------- */
        $newsid = '6';
		$template_values = $this->user_model->get_email_template($newsid,$this->data['langCode']);
		$adminnewstemplateArr = array('email_title' => $this->config->item('email_title'), 'logo' => $this->config->item('logo_image'), 'footer_content' => $this->config->item('footer_content'), 'meta_title' => $this->config->item('meta_title'), 'site_contact_mail' => $this->config->item('site_contact_mail'));
		extract($adminnewstemplateArr);
		
        $message = '<!DOCTYPE HTML>
			<html>
			<head>
			<meta http-equiv="Content-Type" content="text/html; charset=utf-8">
			<meta name="viewport" content="width=device-width"/>
			<title>' . $template_values['subject'] . '</title>
			<body>';
        include($template_values['templateurl']);
        $message .= '</body>
			</html>';
		$sender_email = $this->config->item('site_contact_mail');
        $sender_name = $this->config->item('email_title');
        
        $email_values = array('mail_type' => 'html',
            'from_mail_id' => $sender_email,
            'mail_name' => $sender_name,
            'to_mail_id' => $em_user_email,
            'subject_message' => $template_values['subject'],
            'body_messages' => $message
        );
        $email_send_to_common = $this->user_model->common_email_send($email_values);
        return $responseArr;
    }

    /**
     * 
     * This function validate the forgot password form
     * If email is correct then generate new password and send it to the email given
     */
    public function forgot_password_initiate() {
        $email = $this->input->post('email');
        $responseArr['status'] = '0';
        $responseArr['response'] = '';
        if ($email != '') {
            $condition = array('email' => $email);
            $riderVal = $this->user_model->get_all_details(USERS, $condition);
            if ($riderVal->num_rows() == 1) {
                $new_pwd = $this->get_rand_str('4');
                $newdata = array('reset_id' => $new_pwd);
                $condition = array('email' => $email);
                $this->user_model->update_details(USERS, $newdata, $condition);
                $resturn_res = $this->send_rider_reset_pwd_verification_code($new_pwd, $riderVal);
                $responseArr['status'] = '1';
                $responseArr = array_merge($responseArr, $resturn_res);
                $responseArr['response'] = $this->format_string('Verification code has been sent to you', 'verification_code_sent');
            } else {
                $responseArr['response'] = $this->format_string('Email id not matched in our records', 'email_not_matched');
            }
        } else {
            $responseArr['response'] = $this->format_string('Some Parameters are missing', 'some_parameters_missing');
        }
        $json_encode = json_encode($responseArr, JSON_PRETTY_PRINT);
        echo $this->cleanString($json_encode);
    }

    /**
     * 
     * This function send the new password to driver email
     */
    public function send_rider_reset_pwd_verification_code($pwd = '', $query) {

        $user_name = $query->row()->user_name;
        /* ---------------SMS--------------------- */
        $phone_code = $query->row()->country_code;
        $phone_number = $query->row()->phone_number;
        if (substr($phone_code, 0, 1) == '+') {
            $phone_code = $phone_code;
        } else {
            $phone_code = '+' . $phone_code;
        }

        $from = $this->config->item('twilio_number');
        $to = $phone_code . $phone_number;
		
		$smsInfo = array("code"=>$pwd,
										"user_name"=>$user_name,
										"phone_code"=>$phone_code,
										"phone_number"=>$phone_number
									);
        $this->sms_model->passanger_reset_password_otp($smsInfo);

        if ($this->config->item('twilio_account_type') == 'sandbox') {
            $otp_status = 'development';
        } else {
            $otp_status = 'production';
        }
        $responseArr['sms_status'] = $otp_status;
        $responseArr['verification_code'] = $pwd;
        $responseArr['email_address'] = $query->row()->email;


        /* ---------------EMAIL--------------------- */

        $newsid = '11';
        $verificationCode = $pwd;
		$template_values = $this->user_model->get_email_template($newsid,$this->data['langCode']);
        $subject = 'From: ' . $this->config->item('email_title') . ' - ' . $template_values['subject'];
        $ridernewstemplateArr = array('email_title' => $this->config->item('email_title'), 'mail_emailTitle' => $this->config->item('email_title'), 'mail_logo' => $this->config->item('logo_image'), 'mail_footerContent' => $this->config->item('footer_content'), 'mail_metaTitle' => $this->config->item('meta_title'), 'mail_contactMail' => $this->config->item('site_contact_mail'));
        extract($ridernewstemplateArr);
        $message = '<!DOCTYPE HTML>
			<html>
			<head>
			<meta http-equiv="Content-Type" content="text/html; charset=utf-8">
			<meta name="viewport" content="width=device-width"/>
			<title>' . $template_values['subject'] . '</title>
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
        return $responseArr;
    }

    /**
     * 
     * This function updates the reset password
     */
    function reset_password() {
        $email = $this->input->post('email');
        $pwd = $this->input->post('password');
        $responseArr['status'] = '0';
        $responseArr['response'] = '';
        if ($pwd != '' && $email != '') {
            $condition = array('email' => $email);
            $driverVal = $this->user_model->update_details(USERS, array('password' => md5($pwd), 'reset_id' => ''), $condition);
            $responseArr['status'] = '1';
            $responseArr['response'] = $this->format_string('Password changed successfully', 'password_changed');
        } else {
            $responseArr['response'] = $this->format_string('Some Parameters are missing', 'some_parameters_missing');
        }
        $json_encode = json_encode($responseArr, JSON_PRETTY_PRINT);
        echo $this->cleanString($json_encode);
    }
	
	
	/**
     * 
     * Add Favourite Driver
     */
	 
	  public function add_favourite_driver() {
        $returnArr['status'] = '0';
        $returnArr['response'] = '';
        try {
            $title = trim($this->input->post('title'));
            $desc = trim($this->input->post('description'));
            $user_id = $this->input->post('user_id');
			$driver_id = $this->input->post('driver_id');
			if(isset($title)&&isset($user_id)&&isset($driver_id)){
				if(!empty($title)&&!empty($user_id)&&!empty($driver_id)){
					$user_details=$this->user_action_model->get_selected_fields(USERS,array('_id'=>MongoID($user_id)),array('_id'));
					if($user_details->num_rows()>0){
						$driver_details=$this->user_action_model->get_selected_fields(DRIVERS,array('_id'=>MongoID($driver_id)),array('_id'));	
						if($driver_details->num_rows()>0){
							$fav_condition = array('user_id' => MongoID($user_id));
							$checkDriverFav = $this->user_action_model->get_all_details(FAVOURITE, $fav_condition);
						if (isset($checkDriverFav->row()->fav_driver[$driver_id])) {
							 $returnArr['response'] = $this->format_string('Driver already exist in your favourite list', 'driver_already_exist_in_favourite');
						}else{
							 if ($checkDriverFav->num_rows() == 0) {
								$dataArr = array('user_id' => MongoID($user_id),
								'fav_driver' => array($driver_id => array('title' => $title,
										'description' => $desc
								 )));
								$this->user_action_model->simple_insert(FAVOURITE, $dataArr);
								$returnArr['status'] = '1';
								$returnArr['response'] = $this->format_string('Driver added to favourite successfully!', 'driver_added_to_favourite');
							}else {
								$dataArr = array('fav_driver.' . $driver_id => array('title' => $title,
                                'description' => $desc));
								$this->user_action_model->set_to_field(FAVOURITE, $fav_condition, $dataArr);
								$returnArr['status'] = '1';
								$returnArr['response'] = $this->format_string('Driver added to favourite successfully!', 'driver_added_to_favourite');
						   }
						}
						}else{
							 $returnArr['response'] = $this->format_string("Invalid Driver", "invalid_driver");
						}
					}else{
						 $returnArr['response'] = $this->format_string("Invalid User", "invalid_user");
						}
				}  else {
                $returnArr['response'] = $this->format_string("Some Parameters are missing", "some_parameters_missing");
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
     * This function edit favourite driver added by user
     *
     * */
    public function edit_favourite_driver() {
        $returnArr['status'] = '0';
        $returnArr['response'] = '';
        try {
            $title = trim($this->input->post('title'));
            $desc = trim($this->input->post('description'));
            $user_id = $this->input->post('user_id');
			$driver_id = $this->input->post('driver_id');

           	if(isset($title)&&isset($user_id)&&isset($driver_id)){
				if(!empty($title)&&!empty($user_id)&&!empty($driver_id)){
					$user_details=$this->user_action_model->get_selected_fields(USERS,array('_id'=>MongoID($user_id)),array('_id'));
					if($user_details->num_rows()>0){
						$driver_details=$this->user_action_model->get_selected_fields(DRIVERS,array('_id'=>MongoID($driver_id)),array('_id'));	
						if($driver_details->num_rows()>0){
							$fav_condition = array('user_id' => MongoID($user_id));
							$checkDriverFav = $this->user_action_model->get_all_details(FAVOURITE, $fav_condition);
						if (isset($checkDriverFav->row()->fav_driver[$driver_id])) {
							$dataArr = array('fav_driver.' . $driver_id => array('title' => $title,
                                'description' => $desc));
								$this->user_action_model->set_to_field(FAVOURITE, $fav_condition, $dataArr);
								$returnArr['status'] = '1';
								$returnArr['response'] = $this->format_string('Favorite driver edited successfully!', 'favorite_driver_updated');
						}else{
							
							$returnArr['response'] = $this->format_string('Driver not found in your favorite drivers list', 'driver_not_found_in_favorite');
						}
						}else{
							 $returnArr['response'] = $this->format_string("Invalid Driver", "invalid_driver");
						}
					}else{
						 $returnArr['response'] = $this->format_string("Invalid User", "invalid_user");
						}
				}  else {
                $returnArr['response'] = $this->format_string("Some Parameters are missing", "some_parameters_missing");
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
     * This function remove the favorite driver from user's favorite driver list
     *
     * */
    public function remove_favourite_driver() {
        $returnArr['status'] = '0';
        $returnArr['response'] = '';
        try {
            $driver_id = $this->input->post('driver_id');
            $user_id = $this->input->post('user_id');
			if(isset($user_id)&&isset($driver_id)){
				if(!empty($user_id)&&!empty($driver_id)){
					$user_details=$this->user_action_model->get_selected_fields(USERS,array('_id'=>MongoID($user_id)),array('_id'));
					if($user_details->num_rows()>0){
						$driver_details=$this->user_action_model->get_selected_fields(DRIVERS,array('_id'=>MongoID($driver_id)),array('_id'));	
						if($driver_details->num_rows()>0){
						$fav_condition = array('user_id' => MongoID($user_id));
						$checkDriverFav = $this->user_action_model->get_all_details(FAVOURITE, $fav_condition);
						if (isset($checkDriverFav->row()->fav_driver[$driver_id])) {
						$this->user_action_model->remove_favorite_driver($fav_condition, 'fav_driver.' . $driver_id);
						$returnArr['status'] = '1';
						$returnArr['response'] = $this->format_string('Driver unfavored successfully!', 'driver_unfavored_successfully');
							
						}else{
							
							$returnArr['response'] = $this->format_string('Driver not found in your favorite drivers list', 'driver_not_found_in_favorite');
						}
						}else{
							 $returnArr['response'] = $this->format_string("Invalid Driver", "invalid_driver");
						}
					}else{
						 $returnArr['response'] = $this->format_string("Invalid User", "invalid_user");
						}
				}  else {
				$returnArr['response'] = $this->format_string("Some Parameters are missing", "some_parameters_missing");
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
     * This function displays the all favourite drivers added by user
     *
     * */
    public function display_favourite_driver() {
        $returnArr['status'] = '0';
        $returnArr['response'] = '';
        try {
			$user_id = $this->input->post('user_id');
			$page = $this->input->post('page');
			$perPage = $this->input->post('perPage');
			if ($perPage <= 0) {
                $perPage = 20;
            }

			if(isset($user_id)&&!empty($user_id)){
					$user_details=$this->user_action_model->get_selected_fields(USERS,array('_id'=>MongoID($user_id)),array('_id'));
					if($user_details->num_rows()>0){
						$fav_condition = array('user_id' => MongoID($user_id));
						$checkDriverFav = $this->user_action_model->get_all_details(FAVOURITE, $fav_condition);
						if ($checkDriverFav->num_rows() == 0) {
						$returnArr['response'] = $this->format_string('No records found for in your favourite list', 'no_records_found_in_your_favourite_list');
						} else {
							
							if ($page <= 0) {
								$offset = 0;
								$current_page = 1;
							} else {
								$current_page = $page;
								$offset = ($page * $perPage) - $perPage;
							}
							
							if (isset($checkDriverFav->row()->fav_driver)) {
								$favDrivers = $checkDriverFav->row()->fav_driver;
							} else {
								$favDrivers = array();
							}
							$favDriverArr =$favDriverArray= array();
							foreach ($favDrivers as $key=>$val) {
								$favDriverArr[] = array('driver_id' => $key,
									'title' => $val['title'],
									'description' => $val['description']
								);
							}
							$favDriverArr=array_slice($favDriverArr,$offset,$perPage);
							if (empty($favDriverArr)) {
								$favDriverArr = json_decode("{}");
							}
							$totalFavDriver = count($favDrivers);
							if ($totalFavDriver > 0) {
								$returnArr['status'] = '1';
								$returnArr['response'] = array('drivers' => $favDriverArr,'current_page' => (string) $current_page, 'perPage' => (string) $perPage, 'total_count' => $totalFavDriver);
							} else {
								$returnArr['response'] = $this->format_string('No records found for in your favourite list', 'no_records_found_in_your_favourite_list');
							}
						}
						
					}else{
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
	* ReSend OTP
	*
	**/
    public function resend_otp() {
        $type = $this->input->post('type'); #(register/forgot/change)
        $mobile_number = $this->input->post('mobile_number');
        $dail_code = $this->input->post('dail_code');
		
		$email = (string)trim($this->input->post('email'));
		$user_id = (string)trim($this->input->post('user_id'));
		
        $returnArr['status'] = '0';
        $returnArr['response'] = '';
        if ($type!== ''  && (($type == 'register' && $dail_code != '' && $mobile_number != '') || ($type == 'forgot' && $email != '') || ($type == 'change' && $dail_code != '' && $mobile_number != '' && $user_id != ''))) {
			if($type == 'register'){
				$otp_string = $this->user_model->get_random_string(6);
				$otp_status = "development";
				if ($this->config->item('twilio_account_type') == 'prod') {
					$otp_status = "production";
					$this->sms_model->opt_for_registration($dail_code, $mobile_number, $otp_string,$this->app_language);
				}
				$returnArr['status'] = '1';
				$msg = $this->format_string('OTP has been resent to this number', 'otp_resent_to_this_number');
				$returnArr['response'] = $msg . ' : ' . $dail_code.$mobile_number;
				$returnArr['otp'] = (string)$otp_string;
				$returnArr['otp_status'] = (string) $otp_status;		
			}else if($type == 'forgot'){
				$condition = array('email' => $email);
				$riderVal = $this->user_model->get_all_details(USERS, $condition);
				if ($riderVal->num_rows() == 1) {
					$new_pwd = $this->get_rand_str('4');
					$newdata = array('reset_id' => $new_pwd);
					$condition = array('email' => $email);
					$this->user_model->update_details(USERS, $newdata, $condition);
					$resturn_res = $this->send_rider_reset_pwd_verification_code($new_pwd, $riderVal);
					$returnArr['status'] = '1';
					$returnArr = array_merge($returnArr, $resturn_res);
					$returnArr['response'] = $this->format_string('Verification code has been sent to you', 'verification_code_sent');
				} else {
					$returnArr['response'] = $this->format_string('Email id not matched in our records', 'email_not_matched');
				}
			}else if($type == 'change'){
				$condition = array('_id' => MongoID($user_id));
				$checkUser = $this->user_model->get_all_details(USERS, $condition);
				if ($checkUser->num_rows() == 1) {
					$em_mobile = ""; $em_mobile_code = "";
					if(isset($checkUser->row()->emergency_contact)){
						$em_mobile = (string)$checkUser->row()->emergency_contact['em_mobile'];
						$em_mobile_code = (string)$checkUser->row()->emergency_contact['em_mobile_code'];
					}
					
					$proC = TRUE;		
					if($dail_code==$em_mobile_code || $mobile_number==$em_mobile){
						if($mobile_number==$em_mobile){
							if($dail_code==$em_mobile_code){
								$proC = FALSE;
							}
						}
					}
					if($proC == TRUE){
						$phone_code = $dail_code;
						if (substr($phone_code, 0, 1) == '+') {
							$phone_code = $phone_code;
						} else {
							$phone_code = '+' . $phone_code;
						}
						$otp_number = rand(10000, 99999);
						$from = $this->config->item('twilio_number');
						$to = $phone_code . $mobile_number;
						$user_name = $checkUser->row()->user_name;
						$user_email = $checkUser->row()->email;
						$dear = $this->format_string('Dear', 'dear');
						$your = $this->format_string('your', 'your');
						$one_time_password_is = $this->format_string('one time password is', 'one_time_password_is');
					
						$smsInfo = array("otp_number"=>$otp_number,
									"user_name"=>$user_name,
									"phone_code"=>$phone_code,
									"phone_number"=>$mobile_number
								);
						$this->sms_model->passanger_change_mobile_otp($smsInfo);
						
						$this->user_model->update_details(USERS, array('mobile_otp' => $otp_number), $condition);

						$returnArr['otp'] = $otp_number;
						if ($this->config->item('twilio_account_type') == 'sandbox') {
							$otp_status = 'development';
						} else {
							$otp_status = 'production';
						}
						$returnArr['otp_status'] = $otp_status;
						$returnArr['status'] = '1';
						$returnArr['country_code'] = (string) $dail_code;
						$returnArr['phone_number'] = (string) $mobile_number;
						$returnArr['response'] = $this->format_string('otp sent successfully', 'otp_sent');
					}else{
						$returnArr['response'] = $this->format_string('This number has been already added in your emergency contact, so you cannot update this number', 'already_in_emergency_contact');
					}
				} else {
					$returnArr['response'] = $this->format_string('Email id not matched in our records', 'email_not_matched');
				}
			}else{
				$returnArr['response'] = $this->format_string("Something went wrong, please try again later!", "something_went_wrong");
			}
						
        } else {
            $returnArr['response'] = $this->format_string('Some Parameters are missing', 'some_parameters_missing');
        }
        $json_encode = json_encode($returnArr, JSON_PRETTY_PRINT);
        echo $this->cleanString($json_encode);
    }

}

/* End of file user_profile.php */
/* Location: ./application/controllers/v8/api/user_profile.php */