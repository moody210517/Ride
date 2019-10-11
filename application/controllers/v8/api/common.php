<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');
/** 
* 
* Common functions related to both user and driver application
* @author Casperon
*
**/
 
class Common extends MY_Controller {

	function __construct(){
        parent::__construct();
		$this->load->helper(array('cookie','date','form','email'));
		$this->load->library(array('encrypt','form_validation'));
		$this->load->model(array('app_model','user_action_model')); 
		$responseArr=array();
		
		/* Authentication Begin */
        $headers = $this->input->request_headers();
		header('Content-type:application/json;charset=utf-8');
		if (array_key_exists("Authkey", $headers)) $auth_key = $headers['Authkey']; else $auth_key = "";
		if(stripos($auth_key,APP_NAME) === false) {
			$cf_fun= $this->router->fetch_method();
			$apply_function = array('update_receive_mode','get_app_info');
			if(!in_array($cf_fun,$apply_function)){
				show_404();
			}
		}
		
		if(array_key_exists("Apptype",$headers)) $this->Apptype =$headers['Apptype'];
		if(array_key_exists("Userid",$headers)) $this->Userid =$headers['Userid'];
		if(array_key_exists("Driverid",$headers)) $this->Driverid =$headers['Driverid'];
		if(array_key_exists("Apptoken",$headers)) $this->Token =$headers['Apptoken'];
		try{
			if(($this->Userid!="" || $this->Driverid!="") && $this->Token!="" && $this->Apptype!=""){
				if($this->Driverid!=''){
					$deadChk = $this->app_model->get_selected_fields(DRIVERS, array('_id' => MongoID($this->Driverid)), array('push_notification','status'));
					if($deadChk->num_rows()>0){
						$storedToken ='';
						if(strtolower($deadChk->row()->push_notification['type']) == "ios"){
							$storedToken = $deadChk->row()->push_notification["key"];
						}
						if(strtolower($deadChk->row()->push_notification['type']) == "android"){
							$storedToken = $deadChk->row()->push_notification["key"];
						}
						$c_fun= $this->router->fetch_method();
						$apply_function = array('update_receive_mode','get_app_info');
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
				if($this->Userid!=''){
					$deadChk = $this->app_model->get_selected_fields(USERS, array('_id' => MongoID($this->Userid)), array('push_type', 'push_notification_key','status'));
					if($deadChk->num_rows()>0){
						$storedToken ='';
						if(strtolower($deadChk->row()->push_type) == "ios"){
							$storedToken = $deadChk->row()->push_notification_key["ios_token"];
						}
						if(strtolower($deadChk->row()->push_type) == "android"){
							$storedToken = $deadChk->row()->push_notification_key["gcm_id"];
						}
						if(strtolower($deadChk->row()->status)!="active"){
							$is_out_message = $this->format_string('Your account has been modified, please login to again.', 'is_out_message');
							echo json_encode(array("is_out" => "Yes","message" => $is_out_message));die;
						}
						if($storedToken!=''){
							if($storedToken != $this->Token){
								echo json_encode(array("is_dead"=>"Yes")); die;
							}
						}
					}else{
						$is_out_message = $this->format_string('Your account has been modified, please login to again.', 'is_out_message');
						echo json_encode(array("is_out" => "Yes","message" => $is_out_message));die;
					}
				}
			 }
		} catch (MongoException $ex) {}
		/*Authentication End*/
    }
	
	/**
	*
	*	This function will update the users/drivers current availablity
	*
	**/
	
	public function update_receive_mode() {
        $returnArr['status'] = '0';
        $returnArr['response'] = '';
		
		try {
			$usertype = (string)strtolower($this->input->post('user_type'));	# 	(user/driver)
			$id = (string)$this->input->post('id');
			$mode = (string)$this->input->post('mode'); #	(available/unavailable)
						
			if($usertype != '' && $id != '' && $mode != ''){
				$collection = '';
				if($usertype == "user"){
					$collection = USERS;
				}else if($usertype == "driver"){
					$collection = DRIVERS;
				}
				if($collection!=''){
					$userInfo = $this->app_model->get_selected_fields($collection, array('_id' => MongoID($id)), array('chat_status'));					
					if($userInfo->num_rows()==1){
						$dataArr =  array('messaging_status' => strtolower($mode));
						$condition =  array('_id' => MongoID($id));
						$this->app_model->update_details($collection, $dataArr, $condition);
						$returnArr['status'] = '1';
						$returnArr['response'] = $this->format_string("Status Updated Successfully",'status_update_success');
					}else{
						$returnArr['response'] = $this->format_string("Cannot find your identity",'cant_find_your_identity');
					}
				}else{
					$returnArr['response'] = $this->format_string("Cannot find your identity",'cant_find_your_identity');
				}
            } else {
                $returnArr['response'] = $this->format_string("Some Parameters are missing",'some_parameters_missing');
            }
		
		}catch (MongoException $ex) {
            $returnArr['response'] = $this->format_string('Error in connection','error_in_connection');
        }
        $json_encode = json_encode($returnArr, JSON_PRETTY_PRINT);
        echo $this->cleanString($json_encode);
    }
	
	/**
	*
	*	This function will return the information to app during launching
	*
	**/	
	public function get_app_info() {
        $returnArr['status'] = '0';
        $returnArr['response'] = '';
		
		try {
			$usertype = (string)strtolower($this->input->post('user_type'));	# 	(user/driver)
			$id = (string)$this->input->post('id');
			$latitude = $this->input->post('lat');
			$longitude = $this->input->post('lon');
			
			$server_mode = '0';
			if($_SERVER['HTTP_HOST']=="192.168.1.244"){
				$xmpp_host_url = '192.168.1.244';
				$xmpp_host_name = '192.168.1.244';
			}else{
				$server_mode = '1';
				if($_SERVER['HTTP_HOST']=="booktaxi.casperon.co"){
					$xmpp_host_url = 'ejabberd.casperon.co';
					$xmpp_host_name = 'ejabberd.casperon.co';
				}else if (is_file('xmpp-master/config.php')) {
					require_once('./xmpp-master/config.php');
					$xmpp_host_url = vhost_name;
					$xmpp_host_name = vhost_name;
				} 
			}
			
			$site_mode_string = $this->format_string("currently we are not able to service you, please try again later", "currently_we_are_not_able_to_service_you");

			$site_mode_status = (string)$this->config->item('site_mode');
			if($site_mode_status==""){
				$site_mode_status = "production";	#(development/production)
			}
			
			$defaultLanguage = "en";
			if($this->config->item('default_lang_code')!=""){
				$defaultLanguage = $this->config->item('default_lang_code');
			}
            if ($defaultLanguage == '') {
                $defaultLanguage = 'en';
            }
			$lang_code = $defaultLanguage;
			$location = $this->app_model->find_location(floatval($longitude), floatval($latitude));
					
			if(!empty($location['result'])) {
				$final_cat_list = $location['result'][0]['avail_category'];
			}

			$selected_Category='';
			if(!empty($final_cat_list)) {
				$selected_Category=$final_cat_list[0];
			}
			
			if($this->data['phone_masking_status'] != ''){
				$phone_masking_status = $this->data['phone_masking_status'];
			}else{
				$phone_masking_status = 'No';
			}
			$sms_char_length = '140';
			
			$pooling = "0";
			if($this->data['share_pooling_status'] != ''){
				$pooling = $this->data['share_pooling_status'];
			}
            
            $emergencyNumbers = array(array("title"=> $this->format_string('Police','em_police'),
                                            "number"=>"100"
                                          ),
                                      array("title"=> $this->format_string('Fire','em_fire'),
                                            "number"=>"101"
                                          ),
                                      array("title"=> $this->format_string('Ambulance','em_ambulance'),
                                            "number"=>"108"
                                          ) 
                                    );
			
			$infoArr =  array('site_contact_mail' => (string)$this->config->item('site_contact_mail'),
							'site_contact_address' => (string)$this->config->item('site_contact_address'),
							'customer_service_number' => (string)$this->config->item('customer_service_number'),
							'server_mode' => $server_mode,
							'site_mode' => $site_mode_status,
							'site_mode_string' => $site_mode_string,
							'site_url' => base_url(),
							'xmpp_host_url' => (string)$xmpp_host_url,
							'xmpp_host_name' => (string)$xmpp_host_name,
							'facebook_id' => (string)$this->config->item('facebook_app_id_android'),
							'google_plus_app_id' => (string)$this->config->item('google_client_id'),
							'driver_google_ios_key' => (string)$this->config->item('google_ios_key'),
							'driver_google_android_key' => (string)$this->config->item('google_android_key'),
							'driver_google_ios_server_key' => (string)$this->config->item('google_server_key'),
							'driver_google_android_server_key' => (string)$this->config->item('google_ios_key'),
							'user_google_ios_key' => (string)$this->config->item('google_ios_key'),
							'user_google_android_key' => (string)$this->config->item('google_android_key'),
							'user_google_ios_server_key' => (string)$this->config->item('google_server_key'),
							'user_google_android_server_key' => (string)$this->config->item('google_ios_key'),
                            'app_identity_name' => (string)APP_NAME,
							'about_content' => (string)$this->config->item('about_us'),
							'phone_masking_status' => (string)$phone_masking_status,
							'sms_char_length' => (string)$sms_char_length,
							'user_image' => (string)"",
							'user_name' => (string)"",
							'driver_image' => (string)"",
							'driver_name' => (string)"",
							'lang_code' => (string)$lang_code,
							'selected_Category' => (string)$selected_Category,
							'pooling' => (string)$pooling,
							'emergency_numbers' => $emergencyNumbers,
                            'user_version_control' => (string)$this->config->item('user_version_control'),
							'driver_version_control' => (string)$this->config->item('driver_version_control'),
                            'confirmed'=>"0",
                            'arrived'=>"0",
                            'onride'=>"0",
                            'finished'=>"0",
                            'completed'=>"0",
                            'cancelled'=>"0"
                        );	
             
							
			if($usertype != '' && $id != ''){
				$collection = '';
				if($usertype == "user"){
					$collection = USERS;
				}else if($usertype == "driver"){
					$collection = DRIVERS;
				}
				if($collection!=''){
					$userInfo = $this->app_model->get_selected_fields($collection, array('_id' => MongoID($id)), array('chat_status','lang_code'));					
					if($userInfo->num_rows()==1){
						if(isset($userInfo->row()->lang_code)){
							if($userInfo->row()->lang_code!=""){
								$infoArr['lang_code'] = $userInfo->row()->lang_code;
							}
						}						
					}
				}
				if($collection == USERS){
					$userVal = $this->app_model->get_selected_fields(USERS, array('_id' => MongoID($id)), array( 'image','user_name'));
					$user_image = USER_PROFILE_IMAGE_DEFAULT;
					if(isset($userVal->row()->image)){
						if ($userVal->row()->image != '') {
							$user_image = USER_PROFILE_IMAGE . $userVal->row()->image;
						}
					}
					if(isset($userVal->row()->user_name)){
						if ($userVal->row()->user_name != '') {
							$infoArr['user_name'] = $userVal->row()->user_name;
						}
					}
					$infoArr['user_image'] = base_url() . $user_image;
				}
				if($collection == DRIVERS){
					$driverVal = $this->app_model->get_selected_fields(DRIVERS, array('_id' => MongoID($id)), array( 'image','driver_name'));
					$driver_image = USER_PROFILE_IMAGE_DEFAULT;
					if(isset($driverVal->row()->image)){
						if ($driverVal->row()->image != '') {
							$driver_image = USER_PROFILE_IMAGE . $driverVal->row()->image;
						}
					}
					if(isset($driverVal->row()->driver_name)){
						if ($driverVal->row()->driver_name != '') {
							$infoArr['driver_name'] = $driverVal->row()->driver_name;
						}
					}
                    $Stats = $this->app_model->get_all_details(DRIVER_STATISTICS, array('driver_id' =>MongoID($id)));
                    if($Stats->num_rows() >0) {
                       $infoArr['confirmed'] = $Stats->row()->confirmed; 
                       $infoArr['arrived'] = $Stats->row()->arrived; 
                       $infoArr['onride'] = $Stats->row()->onride; 
                       $infoArr['finished'] = $Stats->row()->finished; 
                       $infoArr['completed'] = $Stats->row()->completed; 
                       $infoArr['completed'] = $Stats->row()->completed; 
                    }
					$infoArr['user_image'] = base_url() . $driver_image;
					$infoArr['driver_image'] = base_url() . $driver_image;
					$infoArr['about_content'] = (string)$this->config->item('about_us_driver');
				}
						
			}
			$returnArr['status'] = '1';
			$returnArr['response'] = array('info'=>$infoArr);
		
		}catch (MongoException $ex) {
            $returnArr['response'] = $this->format_string('Error in connection','error_in_connection');
        }
        $json_encode = json_encode($returnArr, JSON_PRETTY_PRINT);
        echo $this->cleanString($json_encode);
    }
	
	/**
	*
	*	This function will update the driver location and send the location to user by notifications
	*
	**/	
	public function update_primary_language() {
		$returnArr['status'] = '0';
		$returnArr['response'] = '';
		
		try {
			$id = (string)$this->input->post('id');
			$lang_code = (string)$this->input->post('lang_code');
			$user_type = (string)$this->input->post('user_type');  // Options : user/driver
			
			if($id !='' && $user_type != '' && $lang_code != ''){
				$chekLang = $this->app_model->get_selected_fields(LANGUAGES, array('lang_code' => (string)$lang_code), array('name'));
				if($chekLang->num_rows() == 1){
					$action = FALSE;
					if($user_type == 'user'){
						$chekUser = $this->app_model->get_selected_fields(USERS, array('_id' => MongoID($id)), array('_id'));
						if($chekUser ->num_rows() == 1){
							$this->app_model->update_details(USERS, array('lang_code' => $lang_code),array('_id' => MongoID($id)));
							$action = TRUE;
						}
					} else if($user_type == 'driver'){
						$chekDriver = $this->app_model->get_selected_fields(DRIVERS, array('_id' => MongoID($id)), array('_id'));
						if($chekDriver ->num_rows() == 1){
							$this->app_model->update_details(DRIVERS, array('lang_code' => $lang_code),array('_id' => MongoID($id)));
							$action = TRUE;
						}
					}
					if($action){
						$returnArr['status'] = '1';
						$returnArr['response'] = $this->format_string("Updated Successfully", "updated_successfully");
					}else{
						$returnArr['response'] = $this->format_string("Failed to update", "failed_to_update");
					}
				} else {
					$returnArr['response'] = $this->format_string("Invalid language code", "invalid_language_code");
				}
			}else{
				$returnArr['response'] = $this->format_string("Some Parameters Missing", "some_parameters_missing");
			}
		}catch (MongoException $ex) {
            $returnArr['response'] = $this->format_string('Error in connection', 'error_in_connection');
        }
        $json_encode = json_encode($returnArr, JSON_PRETTY_PRINT);
        echo $this->cleanString($json_encode);
    }
	
	/**
	*
	*	This function will update the information for denied rides
	*
	**/	
	public function send_reports() {
        $returnArr['status'] = '0';
        $returnArr['response'] = '';
		
		try {
			$id = (string)$this->input->post('id');
			$user_type = strtolower($this->input->post('user_type'));
			$subject = (string)$this->input->post('subject');
			$message = (string)$this->input->post('message');
			if($user_type != '' && $id != '' && $message != '' &&  $subject != '' && ($user_type == 'user' || $user_type == 'driver')){
				$collection = USERS;
				if($user_type == 'driver'){
					$collection = DRIVERS;
				}
				$checkReporter = $this->app_model->get_selected_fields($collection , array('_id' => MongoID($id)), array('email','user_name','driver_name','country_code','phone_number','dail_code','mobile_number'));
				if($user_type == 'driver'){
					$reporter_name = $checkReporter->row()->driver_name;
					$phone_number = $checkReporter->row()->dail_code.$checkReporter->row()->mobile_number;
				} else {
					$reporter_name =  $checkReporter->row()->user_name;
					$phone_number = $checkReporter->row()->country_code.$checkReporter->row()->phone_number;
				}
				$email = $checkReporter->row()->email;
				
				
                if ($checkReporter->num_rows() == 1) {
					$report_id = (string) time();
					$dataArr =  array('report_id' => $report_id,
														'reporter_id' => MongoID($id),
														'reporter_type' => (string)$user_type,
														'reporter_details' => array('name' => $reporter_name,'email' => $email,'phone_number' => $phone_number),
														'subject' => $subject,
														'message' => $message,
														'status' => 'open',
														'created_date' => MongoDATE(time())
					);
					$this->app_model->simple_insert(REPORTS,$dataArr);
					$mailArr = array('reporter_name' => $reporter_name,
													 'reporter_type' => ucfirst($user_type),
													 'report_subject' => $subject,
													 'report_message' => $message,
													 'reporter_email' => $email,
													 'report_id' => $report_id
										);
					$this->load->model('mail_model');
					$this->mail_model->send_report_to_admin($mailArr);
					$returnArr['status'] = '1';
					$report_details = array('report_id' => $report_id,'subject' => $subject,'message' => $message);
					$returnArr['response']  = array('message' => $this->format_string("Report has been submitted successfully", "report_sent_success"), 'report_details' => $report_details);
				}else{
					$returnArr['response'] = $this->format_string("Reporter details not found", "reporter_not_found");
				}
			}else{
				$returnArr['response'] = $this->format_string("Some Parameters Missing", "some_parameters_missing");
			}
		}catch (MongoException $ex) {
            $returnArr['response'] = $this->format_string("Error in connection", "error_in_connection");
        }
        $json_encode = json_encode($returnArr, JSON_PRETTY_PRINT);
        echo $this->cleanString($json_encode);
    }

	/**
	*
	*	This function will update the information for denied rides
	*
	**/	
	public function reports_list() {
        $returnArr['status'] = '0';
        $returnArr['response'] = '';
		
		try {
			$id = (string)$this->input->post('id');
			$user_type = strtolower($this->input->post('user_type'));
			
			
			if($user_type != '' && $id != ''  && ($user_type == 'user' || $user_type == 'driver')){
				$collection = USERS;
				if($user_type == 'driver'){
					$collection = DRIVERS;
				}
				$checkReporter = $this->app_model->get_selected_fields($collection , array('_id' => MongoID($id)), array('email','user_name','driver_name'));
				if($user_type == 'driver'){
					$reporter_name = $checkReporter->row()->driver_name;
				} else {
					$reporter_name =  $checkReporter->row()->user_name;
				}
				$email = $checkReporter->row()->email;
				
                if ($checkReporter->num_rows() == 1) {
					$reports_list = $this->app_model->get_all_details(REPORTS,array('reporter_id' => MongoID($id)));
					$reportsList = array();
					foreach($reports_list->result() as $reports){
						$reportsList[] = array('report_id' => (string)$reports->report_id,
																'report_status' => ucfirst($reports->status),
																'subject' => $reports->subject,
																'message' => $reports->message,
																'reported_date' => get_time_to_string('Y-m-d h:i A',MongoEPOCH($reports->created_date))
													);
					}
					$reports_count = (string)count($reportsList);
					if($reports_count > 0){
						$returnArr['status'] = '1';
						$returnArr['response']  = array('reports_count' => $reports_count,'reports_list' => $reportsList);
					} else {
						$returnArr['response'] = $this->format_string("You have not sent any reports yet", "have_not_sent_report");
					}
				}else{
					$returnArr['response'] = $this->format_string("Reporter details not found", "reporter_not_found");
				}
			}else{
				$returnArr['response'] = $this->format_string("Some Parameters Missing", "some_parameters_missing");
			}
		}catch (MongoException $ex) {
            $returnArr['response'] = $this->format_string("Error in connection", "error_in_connection");
        }
        $json_encode = json_encode($returnArr, JSON_PRETTY_PRINT);
        echo $this->cleanString($json_encode);
    }
	
	/**
	*
	*	Return the review options
	*
	**/
	public function get_review_options() {
        $responseArr['status'] = '0';
        try {
            $optionsFor = $this->input->post('optionsFor');
			$ride_id = $this->input->post('ride_id');
            if ($optionsFor != '' && $ride_id!='') {
				$ride_ratting_status = '0';
				$checkRide = $this->app_model->get_selected_fields(RIDES, array('ride_id' => $ride_id), array('driver_review_status','rider_review_status'));
				if ($checkRide->num_rows() == 1) {
					if($optionsFor=='rider'){
						if(isset($checkRide->row()->rider_review_status)){
							if($checkRide->row()->rider_review_status=='Yes'){
								$ride_ratting_status = '1';
							}
						}
					}
					if($optionsFor=='driver'){
						if(isset($checkRide->row()->driver_review_status)){
							if($checkRide->row()->driver_review_status=='Yes'){
								$ride_ratting_status = '1';
							}
						}
					}
				}
				
                $condition = array('option_holder' => $optionsFor, 'status' => 'Active');
                $optionsList = $this->app_model->get_all_details(REVIEW_OPTIONS, $condition);
                if ($optionsList->num_rows() > 0) {
                    $review_opt_arr = array();
                    foreach ($optionsList->result() as $options) {
						if(is_object($options->option_id)){
							$option_id = $options->option_id->value;
						}else{
							$option_id = $options->option_id;
						}
						$option_title = $options->option_name;
						if(isset($options->option_name_languages)){
							$langKey=$this->data['sms_lang_code'];
							$arrVal = $options->option_name_languages;
							if(array_key_exists($langKey,$arrVal)){
								if($options->option_name_languages[$langKey]!=""){
									$option_title = $options->option_name_languages[$langKey];
								}
							}
						}
                        $review_opt_arr[] = array('option_title' => $option_title, 'option_id' =>$option_id);
                    }
                    if (empty($review_opt_arr)) {
                        $review_opt_arr = json_decode("{}");
                    }

                    $responseArr['status'] = '1';
					$responseArr['ride_ratting_status'] = (string)$ride_ratting_status;
                    $responseArr['optionsFor'] = $optionsFor;
                    $responseArr['total'] = $optionsList->num_rows();
                    $responseArr['review_options'] = $review_opt_arr;
                } else {
                    $responseArr['response'] = $this->format_string('Review options not found', 'review_option_not_found');
                }
            } else {
                $responseArr['response'] = $this->format_string("Some Parameters are missing", "some_parameters_missing");
            }
        } catch (MongoException $ex) {
            $returnArr['response'] = $this->format_string("Error in connection", "error_in_connection");
        }
        $json_encode = json_encode($responseArr, JSON_PRETTY_PRINT);
        echo $this->cleanString($json_encode);
    }
	
	/**
	*
	*	Skip the ratings
	*
	**/
	public function skip_reviews() {
        $responseArr['status'] = '0';
        $responseArr['response'] = '';
        try {
            $skip_by = $this->input->post('skip_by');
			$ride_id = (string)$this->input->post('ride_id');
            if ($skip_by != '' && $ride_id!='') {
				$do_action = '1';
				$checkRide = $this->app_model->get_selected_fields(RIDES, array('ride_id' => $ride_id), array('drs','urs','driver.id'));
				if ($checkRide->num_rows() == 1) {
					if($skip_by=='user'){
						$uArr = array("urs"=>"2");
						if(isset($checkRide->row()->urs)){
							if($checkRide->row()->urs!='0'){
								$do_action = '0';
							}
						}
					}
                   
					if($skip_by=='driver'){
						$uArr = array("drs"=>"2");
						if(isset($checkRide->row()->drs)){
							if($checkRide->row()->drs!='0'){
								$do_action = '0';
							}
						}
					}
					$ride_info=array();
                    if($skip_by == 'driver'){
                        $driver_id=$checkRide->row()->driver['id'];
                        $ride_info=$this->ride_info($ride_id,$driver_id);
                    }
					if($do_action=="1"){
						$this->app_model->update_details(RIDES, $uArr,array('ride_id' => $ride_id));
						$responseArr['status'] = '1';
                        $responseArr['ride_info'] = $ride_info;
						$responseArr['response'] = $this->format_string('Review skipped', 'review_skipped');
					}else{
						$responseArr['response'] = $this->format_string('You cannot do this action', 'you_cannot_do_this_action');
					}
                } else {
                    $responseArr['response'] = $this->format_string('Records not available', 'no_records_found');
                }
            } else {
                $responseArr['response'] = $this->format_string("Some Parameters are missing", "some_parameters_missing");
            }
        } catch (MongoException $ex) {
            $returnArr['response'] = $this->format_string("Error in connection", "error_in_connection");
        }
        $json_encode = json_encode($responseArr, JSON_PRETTY_PRINT);
        echo $this->cleanString($json_encode);
    }

	/**
	*
	*	Submit the Reviews to the Trip (User/Driver)
	*
	**/
    public function submit_reviews() {
        $responseArr['status'] = '0';
        try {
            $ratingsFor = $this->input->post('ratingsFor');
            $ride_id = $this->input->post('ride_id');
            $ratingsArr = $this->input->post('ratings');
            $comments = (string) $this->input->post('comments');

            if ($ratingsFor != '' && $ride_id != '' && is_array($ratingsArr)) {
                if (count($ratingsArr) > 0 && ($ratingsFor == 'driver' || $ratingsFor == 'rider')) {
                    $rideCond = array('ride_id' => $ride_id);
                    $get_ride_info = $this->app_model->get_selected_fields(RIDES, $rideCond, array('user.id', 'driver.id', 'rider_review_status', 'driver_review_status'));

                    $driversRating = 0;
                    $ridersRating = 0;
                    if (isset($get_ride_info->row()->driver_review_status)) {
                        if ($ratingsFor == 'driver' && ($get_ride_info->row()->driver_review_status == 'Yes')) {
                            $driversRating = 1;
                        }
                    }
                    if (isset($get_ride_info->row()->rider_review_status)) {
                        if ($ratingsFor == 'rider' && ($get_ride_info->row()->rider_review_status == 'Yes')) {
                            $ridersRating = 1;
                        }
                    }

                    if (($ratingsFor == 'driver' && $driversRating == 0) || ($ratingsFor == 'rider' && $ridersRating == 0)) {

                        $user_id = $get_ride_info->row()->user['id'];
                        $driver_id = $get_ride_info->row()->driver['id'];

                        $ratingsArr = array_filter($ratingsArr);
                        $num_of_ratings = 0;
                        $totalRatings = 0;
                        $avg_rating = 0;
                        for ($i = 0; $i < count($ratingsArr); $i++) {
                            $totalRatings = $totalRatings + $ratingsArr[$i]['rating'];
                            $num_of_ratings++;
                        }
                        $avg_rating = number_format(($totalRatings / $num_of_ratings), 2);

                        $ride_dataArr = array('total_options' => $num_of_ratings,
                            'total_ratings' => $totalRatings,
                            'avg_rating' => number_format($avg_rating, 2),
                            'ratings' => $ratingsArr,
                            'comments' => $comments
                        );


                        if ($ratingsFor == 'driver') {
                            $this->app_model->set_to_field(RIDES, $rideCond, array('ratings.' . $ratingsFor => $ride_dataArr, 'driver_review_status' => 'Yes','urs' => '1'));
                        } else {
                            $this->app_model->set_to_field(RIDES, $rideCond, array('ratings.' . $ratingsFor => $ride_dataArr, 'rider_review_status' => 'Yes','drs' => '1'));
                        }



                        /*                         * *
                         *
                         * Update user rating records
                         */
                        if ($ratingsFor == 'rider') {
                            $userCond = array('_id' => MongoID($user_id));
                            $get_user_ratings = $this->app_model->get_selected_fields(USERS, $userCond, array('avg_review', 'total_review'));

                            $userRateDivider = 1;
                            if (isset($get_user_ratings->row()->avg_review)) {
                                $existUserAvgRat = $get_user_ratings->row()->avg_review;
                                $userRateDivider++;
                            } else {
                                $existUserAvgRat = 0;
                            }

                            if (isset($get_user_ratings->row()->total_review)) {
                                $existTotReview = $get_user_ratings->row()->total_review;
                            } else {
                                $existTotReview = 0;
                            }
                            $userAvgRatings = ($existUserAvgRat + $avg_rating) / $userRateDivider;
                            $userTotalReviews = $existTotReview + 1;

                            $this->app_model->update_details(USERS, array('avg_review' => number_format($userAvgRatings, 2), 'total_review' => $userTotalReviews), $userCond);
                        }


                        /*                         * *
                         *
                         * Update driver rating records
                         */
                        if ($ratingsFor == 'driver') {
                            $driverCond = array('_id' => MongoID($driver_id));
                            $get_driver_ratings = $this->app_model->get_selected_fields(DRIVERS, $driverCond, array('avg_review', 'total_review'));

                            $driverRateDivider = 1;
                            if (isset($get_driver_ratings->row()->avg_review)) {
                                $existDriverAvgRat = $get_driver_ratings->row()->avg_review;
                                if ($get_driver_ratings->row()->avg_review != '') {
                                    $driverRateDivider++;
                                }
                            } else {
                                $existDriverAvgRat = 0;
                            }

                            if (isset($get_driver_ratings->row()->total_review)) {
                                $existDriverTotReview = $get_driver_ratings->row()->total_review;
                            } else {
                                $existDriverTotReview = 0;
                            }
                            $driverAvgRatings = ($existDriverAvgRat + $avg_rating) / $driverRateDivider;
                            $driverTotalReviews = $existDriverTotReview + 1;

                            $this->app_model->update_details(DRIVERS, array('avg_review' => number_format($driverAvgRatings, 2), 'total_review' => $driverTotalReviews), $driverCond);
                        }


                        $ride_info=array();
                        if($ratingsFor == 'rider'){
                            $driver_id=$get_ride_info->row()->driver['id'];
                            $ride_info=$this->ride_info($ride_id,$driver_id);
                        }

                        $responseArr['status'] = '1';
                        $responseArr['ride_info'] = $ride_info;
                        $responseArr['response'] = $this->format_string('Your ratings submitted successfully', 'your_ratings_submitted');
                    } else {
                        $responseArr['response'] = $this->format_string('Already you have submitted your ratings for this ride.', 'already_you_submitted_ratings_for_this_ride');  # as a '.$ratingsFor;
                    }
                } else {
                    $responseArr['response'] = $this->format_string('Submitted ratings fields are not valid', 'submitted_ratings_field_invalid');
                }
            } else {
                $responseArr['response'] = $this->format_string("Some Parameters are missing", "some_parameters_missing");
            }
        } catch (MongoException $ex) {
            $returnArr['response'] = $this->format_string("Error in connection", "error_in_connection");
        }
        $json_encode = json_encode($responseArr, JSON_PRETTY_PRINT);
        echo $this->cleanString($json_encode);
    }
	
	/**
	*
	*	This function will return the information to app during launch app
	*
	**/	
	public function make_masking_call() {
	
        $returnArr['status'] = '0';
        $returnArr['response'] = '';
		
		try {
			$ride_id = (string)$this->input->post('ride_id');
			$user_type = (string)$this->input->post('user_type'); #(user/driver)
			$checkRide = $this->app_model->get_all_details(RIDES, array('ride_id' => $ride_id));
			if ($checkRide->num_rows() == 1) {
				$ride_status = $checkRide->row()->ride_status;
				$allowed_status = array('Confirmed','Arrived','Onride','Finished','Completed');
				if(in_array($ride_status,$allowed_status)){
					
					if($checkRide->row()->user['phone']){
						$passanger_number = $checkRide->row()->user['phone'];
					}
					if($checkRide->row()->driver['phone']){
						$driver_number = $checkRide->row()->driver['phone'];
					}
					
					if($user_type=='user'){
						$primary_call = $passanger_number;
						$secondary_call = $driver_number;
					}
					if($user_type=='driver'){
						$primary_call = $driver_number;
						$secondary_call = $passanger_number;
					}
					
					if($passanger_number!='' && $driver_number!=''){
						
						$twilio_mode        = $this->config->item('twilio_account_type');
						$twilio_account_sid = $this->config->item('twilio_account_sid');
						$twilio_auth_token  = $this->config->item('twilio_auth_token');
						$twilio_number      = '+'.$this->config->item('twilio_number');
						
						try{
							// this line loads the library 
							require(APPPATH.'/third_party/twilio/Services/Twilio.php'); 

							$account_sid = $twilio_account_sid; 
							$auth_token = $twilio_auth_token; 
							$client = new Services_Twilio($account_sid, $auth_token); 
							
							$url = base_url().'phmsk?callid='.$secondary_call;
							$client->account->calls->create($twilio_number, $primary_call, $url, array( 
							'Method' => 'GET',  
							'FallbackMethod' => 'GET',  
							'StatusCallbackMethod' => 'GET',    
							'Record' => 'false', 
							));
							
							$returnArr['status'] = '1';
							$returnArr['response'] = $this->format_string("Please wait, we will call you back", "wait_will_call");
						}catch(Exception $e){
							#$returnArr['response'] = $e->getMessage();
							$returnArr['response'] = $this->format_string("Number is unverified", "number_unverified");
						}
					}else{
						$returnArr['response'] = $this->format_string("Call not allowed", "call_not_allowed");
					}
					
				}else{
					$returnArr['response'] = $this->format_string("You cannot make a call now", "cannot_make_a_call_now");
				}
			}else{
				$returnArr['response'] = $this->format_string("You cannot make a call now", "cannot_make_a_call_now");
			}
		}catch (MongoException $ex) {
			$returnArr['response'] = $this->format_string("Error in connection", "error_in_connection");
        }
        $json_encode = json_encode($returnArr, JSON_PRETTY_PRINT);
        echo $this->cleanString($json_encode);
    }
	
	/**
	*
	*	This function will send the sms to user/driver
	*
	**/	
	public function send_sms() {
	
        $returnArr['status'] = '0';
        $returnArr['response'] = '';
		
		try {
			$ride_id = (string)$this->input->post('ride_id');
			$user_type = (string)$this->input->post('user_type'); #(user/driver)
			$sms_content = (string)$this->input->post('sms_content');
			
			$checkRide = $this->app_model->get_all_details(RIDES, array('ride_id' => $ride_id));
			if ($checkRide->num_rows() == 1) {
				$ride_status = $checkRide->row()->ride_status;
				$allowed_status = array('Confirmed','Arrived','Onride','Finished','Completed');
				if(in_array($ride_status,$allowed_status)){
					
					if($checkRide->row()->user['phone']){
						$passanger_number = $checkRide->row()->user['phone'];
					}
					if($checkRide->row()->driver['phone']){
						$driver_number = $checkRide->row()->driver['phone'];
					}
					
					$number_to_send_sms = "";
					if($user_type=='user'){
						$number_to_send_sms = $driver_number;
					}
					if($user_type=='driver'){
						$number_to_send_sms = $passanger_number;
					}
					
					if($number_to_send_sms!=''){
						$from = $this->config->item('twilio_number');
						$to = $number_to_send_sms;
						$message = $sms_content;
						$response = $this->twilio->sms($from, $to, $message); 
						$returnArr['status'] = '1';
						$returnArr['response'] = $this->format_string("SMS sent successfully", "sms_sent");
					}else{
						$returnArr['response'] = $this->format_string("You cannot send a sms now", "cannot_send_a_sms_now");
					}
				}else{
					$returnArr['response'] = $this->format_string("You cannot send a sms now", "cannot_send_a_sms_now");
				}
			}else{
				$returnArr['response'] = $this->format_string("You cannot send a sms now", "cannot_send_a_sms_now");
			}
		}catch (MongoException $ex) {
			$returnArr['response'] = $this->format_string("Error in connection", "error_in_connection");
        }
        $json_encode = json_encode($returnArr, JSON_PRETTY_PRINT);
        echo $this->cleanString($json_encode);
    }
	
	public function open_chat() {
        $returnArr['status'] = '0';
        $returnArr['response'] = '';
		
		try {
			$usertype = (string)strtolower($this->input->post('user_type'));	# 	(user/driver)
			$id = (string)$this->input->post('id');
			$ride_id = (string)$this->input->post('ride_id');
			
			$hostDetails1 = array();
			$hostDetails2 = array();
			$senderInfo = array();
			$receiverInfo = array();
			$chatInfo = array();
						
			if($usertype != '' && $id != '' && $ride_id != ''){
				$host1_collection = '';
				if($usertype == "user"){
					$host1_collection = USERS;
					$host2_collection = DRIVERS;
					$match_field = 'user.id';
					$host1_selectArr = array('user_name','image','messaging_status');
					$host2_selectArr = array('driver_name','image','messaging_status');
				}else if($usertype == "driver"){
					$host1_collection = DRIVERS;
					$host2_collection = USERS;
					$match_field = 'driver.id';
					$host1_selectArr = array('driver_name','image','messaging_status');
					$host2_selectArr = array('user_name','image','messaging_status');
				}
				
				if($host1_collection!=''){
					$hostInfo1 = $this->app_model->get_selected_fields($host1_collection, array('_id' => MongoID($id)), $host1_selectArr);			
					if($hostInfo1->num_rows()==1){
						$hostDetails1 = $hostInfo1->result_array();
						$jobInfo = $this->app_model->get_selected_fields(RIDES, array('ride_id' => (string)$ride_id,$match_field=>$id), array('ride_status','user','driver'));
						
						if($jobInfo->num_rows()==1){
							
							if($usertype=='user'){
								$host2_id = $jobInfo->row()->driver['id'];
								if($host2_id!=''){
									$hostInfo2 = $this->app_model->get_selected_fields($host2_collection, array('_id' => MongoID($host2_id)), $host2_selectArr);
									if($hostInfo2->num_rows()==1){
										$hostDetails2 = $hostInfo2->result_array();
									}
								}
							}else if($usertype=='driver'){
								$host2_id = $jobInfo->row()->user['id'];
								if($host2_id!=''){
									$hostInfo2 = $this->app_model->get_selected_fields($host2_collection, array('_id' => MongoID($host2_id)), $host2_selectArr);
									if($hostInfo2->num_rows()==1){
										$hostDetails2 = $hostInfo2->result_array();
									}
								}
							}
							
							$hostDetails1 = $hostInfo1->result_array();
							
							if($usertype=='user'){
								if(!empty($hostDetails1)){
									$sender_image = USER_PROFILE_THUMB_DEFAULT;
									if(isset($hostDetails1[0]['image'])){
										if($hostDetails1[0]['image']!=''){
											$sender_image = USER_PROFILE_THUMB . $hostDetails1[0]['image'];
										}
									}
									$senderInfo = array('name'=>(string)$hostDetails1[0]['user_name'],
														'id'=>(string)$hostDetails1[0]['_id'],
														'image'=>(string)base_url().$sender_image
														);
								}
								if(!empty($hostDetails2)){														
									$receiver_image = USER_PROFILE_THUMB_DEFAULT;
									if(isset($hostDetails2[0]['image'])){
										if($hostDetails2[0]['image']!=''){
											$receiver_image = USER_PROFILE_THUMB . $hostDetails2[0]['image'];
										}
									}									
									$receiverInfo = array('name'=>(string)$hostDetails2[0]['driver_name'],
														'id'=>(string)$hostDetails2[0]['_id'],
														'image'=>(string)base_url().$receiver_image
														);
								}
							}else if($usertype=='driver'){
								if(!empty($hostDetails1)){
									$sender_image = USER_PROFILE_THUMB_DEFAULT;
									if(isset($hostDetails1[0]['image'])){
										if($hostDetails1[0]['image']!=''){
											$sender_image = USER_PROFILE_THUMB . $hostDetails1[0]['image'];
										}
									}
									$senderInfo = array('name'=>(string)$hostDetails1[0]['driver_name'],
														'id'=>(string)$hostDetails1[0]['_id'],
														'image'=>(string)base_url().$sender_image
														);
								}
								if(!empty($hostDetails2)){														
									$receiver_image = USER_PROFILE_THUMB_DEFAULT;
									if(isset($hostDetails2[0]['image'])){
										if($hostDetails2[0]['image']!=''){
											$receiver_image = USER_PROFILE_THUMB . $hostDetails2[0]['image'];
										}
									}									
									$receiverInfo = array('name'=>(string)$hostDetails2[0]['user_name'],
														'id'=>(string)$hostDetails2[0]['_id'],
														'image'=>(string)base_url().$receiver_image
														);
								}							
							}
							
							$job_chat_status = 'closed';
							if(in_array($jobInfo->row()->ride_status , $this->open_chat_arr)){
								$job_chat_status = 'open';
							}
							
							$receiver_status = 'offline';
							if(isset($hostDetails2[0]['messaging_status'])){
								if($hostDetails2[0]['messaging_status'] == 'available'){
									$receiver_status = 'online';
								}
							}
							
							$receiver_online_status = $receiver_status;		# (online / offline)
							$messaging_status = $job_chat_status;				# (open / closed)
							
							$chatInfo = array('ride_id'=>(string)$ride_id,
											'receiver_status' => (string)$receiver_online_status,
											'chat_status' => (string)$messaging_status
											);
							
							if(empty($senderInfo)){
								$senderInfo = json_decode("{}");
							}
							if(empty($receiverInfo)){
								$receiverInfo = json_decode("{}");
							}
							if(empty($chatInfo)){
								$chatInfo = json_decode("{}");
							}
							
							$returnArr['status'] = '1';
							$returnArr['response'] = array('sender'=>$senderInfo,
															'receiver'=>$receiverInfo,
															'chat'=>$chatInfo
														);
						}else{
							$returnArr['response'] = $this->format_string("Cannot Continue to chat");
						}
					}else{
						$returnArr['response'] = $this->format_string("Cannot find your identity","cannot_find_identity");
					}
				}else{
					$returnArr['response'] = $this->format_string("Cannot find your identity","cannot_find_identity");
				}
            } else {
                $returnArr['response'] = $this->format_string("Some Parameters are missing","some_parameters_missing");
            }
		
		}catch (MongoException $ex) {
            $returnArr['response'] = $this->format_string('Error in connection','error_connection');
        }
        $json_encode = json_encode($returnArr, JSON_PRETTY_PRINT);
        echo $this->cleanString($json_encode);
    }
	
	
	public function push_chat_message(){
		$returnArr['status'] = '0';
		$returnArr['response'] = '';
		$id = $this->input->post('id');
		$user_type = $this->input->post('user_type');
		$message_type = $this->input->post('message_type');
		$message_content = trim($this->input->post('message_content'));
		$ride_id = $this->input->post('ride_id');
		try{
			if($user_type != '' && $id != '' && $ride_id != '' && $message_type != '' && $message_content != ''){
				if($user_type == 'user'){
					$collection = USERS;
					$invalid_user = $this->format_string("Invalid User", "invalid_user");
				}else{
					$collection = DRIVERS;
					$invalid_user = $this->format_string("Invalid Usher", "invalid_usher");
				}
				$userInfo = $this->app_model->get_selected_fields($collection, array('_id' => MongoID($id)),array('image','messaging_status','user_name','driver_name','avg_review','total_review'));
				if($userInfo->num_rows() > 0 ){
					$checkRide = $this->app_model->get_all_details(RIDES,array('ride_id' => $ride_id),array('user','driver'));
					if($checkRide->num_rows() > 0){
						$proceed_to_send = FALSE;
						if($user_type == 'user'){
							$userVal = $this->app_model->get_selected_fields(DRIVERS,array('_id' => MongoID($checkRide->row()->driver['id'])),array('push_notification','driver_name','image'));
							
							$push_values = array_filter($userVal->row()->push_notification);
							if(!empty($push_values)){
								$proceed_to_send = TRUE;
								$sender_name = $userInfo->row()->user_name;
								$sender_id = (string)$userInfo->row()->_id;
								$receiver_id = (string)$userVal->row()->_id;
								$receiver_name = $userVal->row()->driver_name;
							}
						}else{
							$userVal = $this->app_model->get_selected_fields(USERS,array('_id' => MongoID($checkRide->row()->user['id'])),array('push_type','push_notification_key','user_name','image'));
							$push_values = array_filter($userVal->row()->push_notification_key);
							if(!empty($push_values) && $userVal->row()->push_type != '' ){
								$proceed_to_send = TRUE;
								$sender_id = (string)$userInfo->row()->_id;
								$receiver_id = (string)$userVal->row()->_id;
								$sender_name = $userInfo->row()->driver_name;
								$receiver_name = $userVal->row()->user_name;
								
							}
						}
						if($proceed_to_send){
							if ($userVal->row()->image == '') {
								$receiver_image = USER_PROFILE_IMAGE_DEFAULT;
							} else {
								$receiver_image = USER_PROFILE_IMAGE . $userVal->row()->image;
							}
							if ($userInfo->row()->image == '') {
								$sender_image = USER_PROFILE_IMAGE_DEFAULT;
							} else {
								$sender_image = USER_PROFILE_IMAGE . $userInfo->row()->image;
							}
							
							$options = array(
								'chat_content' => $message_content,
								'sender_image' => base_url().$sender_image,
								'receiver_image' => base_url().$receiver_image,
								'receiver_name' => $receiver_name,
								'sender_name' => $sender_name,
								'sender_id' => $sender_id,
								'receiver_id' => $receiver_id,
								'message_type' => $message_type
							);
							
							if($message_type == 'text'){
								if($user_type=='user') {									
									$message = $this->format_string("New message from passenger", "message_to_user");
								} else if($user_type=='driver') {
									$message = $this->format_string("New message from driver", "message_to_driver");
								}
							}
							$message_type = $message_type;
							$action = 'chat_received';
							
							$sendResponse = $this->pushingNotificationSend($userVal, $message, $action, $options,$user_type);
							if($sendResponse == 'Yes'){
								$message_sent = 'Yes';
							}else{
								$message_sent = 'No';
							}
						}else{
							$message_sent = 'Yes';
						}
						
						$returnArr['status'] = '1';
						$returnArr['response'] = array(
							'online_status' => $userInfo->row()->messaging_status,
							'message_sent' => $message_sent,
						);
						
					}else{
						$returnArr['response'] = $this->format_string("This job is unavailable", "job_unavailalbe");
					}
				}else{
					$returnArr['response'] = $invalid_user;
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
	
	public function pushingNotificationSend($userVal = array(), $message = '', $action = '', $options = array(), $user_type = '' ) {
		$message_sent = 'No';
		if($user_type == 'user'){
			if (isset($userVal->row()->push_notification)) {
				if ($userVal->row()->push_notification != '') {
					if (isset($userVal->row()->push_notification['type'])) {
						if ($userVal->row()->push_notification['type'] == 'ANDROID') {
							if (isset($userVal->row()->push_notification['key'])) {
								if ($userVal->row()->push_notification['key'] != '') {
									$this->sendPushNotification($userVal->row()->push_notification['key'], $message, $action, 'ANDROID', $options, 'DRIVER');
									$message_sent = 'Yes';
								}
							}
						}
						if ($userVal->row()->push_notification['type'] == 'IOS') {
							if (isset($userVal->row()->push_notification['key'])) {
								if ($userVal->row()->push_notification['key'] != '') {
									$this->sendPushNotification($userVal->row()->push_notification['key'], $message, $action, 'IOS', $options, 'DRIVER');
									$message_sent = 'Yes';
								}
							}
						}
					}
				}
			}
		}else{
			if (isset($userVal->row()->push_type)) {
				if ($userVal->row()->push_type != '') {
					if ($userVal->row()->push_type == 'ANDROID') {
						if (isset($userVal->row()->push_notification_key['gcm_id'])) {
							if ($userVal->row()->push_notification_key['gcm_id'] != '') {
								$this->sendPushNotification($userVal->row()->push_notification_key['gcm_id'], $message, $action, 'ANDROID', $options, 'USER');
								$message_sent = 'Yes';
							}
						}
					}
					if ($userVal->row()->push_type == 'IOS') {
						if (isset($userVal->row()->push_notification_key['ios_token'])) {
							if ($userVal->row()->push_notification_key['ios_token'] != '') {
								$this->sendPushNotification($userVal->row()->push_notification_key['ios_token'], $message, $action, 'IOS', $options, 'USER');
								$message_sent = 'Yes';
							}
						}
					}
				}
			}
		}
		return $message_sent;	 
	}
    public function ride_info($ride_id,$driver_id) {
        $returnArr['status'] = '0';
        $returnArr['response'] = '';
        try {
            
            if ($driver_id != '') {
                $driverVal = $this->app_model->get_selected_fields(DRIVERS, array('_id' =>MongoID($driver_id)), array('_id'));
                if ($driverVal->num_rows() > 0) {
                    $checkRide = $this->app_model->get_all_details(RIDES, array('ride_id' => $ride_id, 'driver.id' => $driver_id));
                    if ($checkRide->num_rows() == 1) {
                        $fareArr = array();
                        $summaryArr = array();
						$min_short = $this->format_string('min', 'min_short');
						$mins_short = $this->format_string('mins', 'mins_short');
                        if (isset($checkRide->row()->summary)) {
                            if (is_array($checkRide->row()->summary)) {
                                foreach ($checkRide->row()->summary as $key => $values) {
									if($key=="ride_duration"){
										if($values<=1){
											$unit = $min_short;
										}else{
											$unit = $mins_short;
										}
										$summaryArr[$key] = (string) $values.' '.$unit;
									}else if($key=="waiting_duration"){
										if($values<=1){
											$unit = $min_short;
										}else{
											$unit = $mins_short;
										}
										$summaryArr[$key] = (string) $values.' '.$unit;
									}else{
										$summaryArr[$key] = (string) $values;
									}
                                    
                                }
                            }
                        }
                        if (isset($checkRide->row()->total)) {
                            if (is_array($checkRide->row()->total)) {
                                $total_bill = 0.00;
                                $tips_amount = 0.00;
                                $coupon_discount = 0.00;
                                $grand_bill = 0.00;
                                $total_paid = 0.00;
                                $wallet_usage = 0.00;
                                if (isset($checkRide->row()->total['total_fare'])) {
                                    $total_bill = $checkRide->row()->total['total_fare'];
                                }

                                if (isset($checkRide->row()->total['tips_amount'])) {
                                    $tips_amount = $checkRide->row()->total['tips_amount'];
                                }

                                $tips_status = '0';
                                if ($tips_amount > 0) {
                                    $tips_status = '1';
                                }


                                if (isset($checkRide->row()->total['coupon_discount'])) {
                                    $coupon_discount = $checkRide->row()->total['coupon_discount'];
                                }
                                if (isset($checkRide->row()->total['grand_fare'])) {
                                    $grand_bill = $checkRide->row()->total['grand_fare'];
                                }
                                if (isset($checkRide->row()->total['paid_amount'])) {
                                    $total_paid = $checkRide->row()->total['paid_amount'];
                                }
                                if (isset($checkRide->row()->total['wallet_usage'])) {
                                    $wallet_usage = $checkRide->row()->total['wallet_usage'];
                                }
                                $fareArr = array('total_bill' => (string) floatval(round($total_bill, 2)),
                                    'coupon_discount' => (string) floatval(round($coupon_discount, 2)),
                                    'grand_bill' => (string) floatval(round($grand_bill, 2)),
                                    'total_paid' => (string) floatval(round($total_paid, 2)),
                                    'wallet_usage' => (string) floatval(round($wallet_usage, 2))
                                );

                                $tipsArr = array('tips_status' => $tips_status,
                                    'tips_amount' => (string) floatval($tips_amount)
                                );
                            }
                        }

                        $pay_status = '';
                        $disp_pay_status = '';
                        if (isset($checkRide->row()->pay_status)) {
                            $pay_status = $checkRide->row()->pay_status;
							if($pay_status == 'Paid'){
								$disp_pay_status = $this->format_string("Paid", "paid");
							}else {
								$pay_status == 'Pending';
								$disp_pay_status = $this->format_string("Pending", "pending");
							}
                        }


                        $doAction = 0;
                        if ($checkRide->row()->ride_status == 'Booked' || $checkRide->row()->ride_status == 'Confirmed' || $checkRide->row()->ride_status == 'Cancelled' || $checkRide->row()->ride_status == 'Arrived') {
                            $doAction = 1;
                            if ($checkRide->row()->ride_status == 'Cancelled') {
								$doAction = 0;
                            }
                        }
                        $iscontinue = 'NO';
                        if ($checkRide->row()->ride_status == 'Confirmed' || $checkRide->row()->ride_status == 'Arrived' || $checkRide->row()->ride_status == 'Onride') {
                            if ($checkRide->row()->ride_status == 'Confirmed') {
                                $iscontinue = 'arrived';
                            }
                            if ($checkRide->row()->ride_status == 'Arrived') {
                                $iscontinue = 'begin';
                            }
                            if ($checkRide->row()->ride_status == 'Onride') {
                                $iscontinue = 'end';
                            }
                        }
						
                        $user_profile = array();
                        if ($iscontinue != 'NO' || $iscontinue == 'NO') {
                            $userVal = $this->app_model->get_selected_fields(USERS, array('_id' =>MongoID($checkRide->row()->user['id'])), array('_id', 'user_name', 'email', 'image', 'avg_review', 'phone_number', 'country_code', 'push_type', 'push_notification_key'));
                            if ($userVal->num_rows() > 0) {
                                if ($userVal->row()->image == '') {
                                    $user_image = USER_PROFILE_IMAGE_DEFAULT;
                                } else {
                                    $user_image = USER_PROFILE_IMAGE . $userVal->row()->image;
                                }
                                $user_review = 0;
                                if (isset($userVal->row()->avg_review)) {
                                    $user_review = $userVal->row()->avg_review;
                                }
								
								$drop_location = 0;
								$drop_loc = '';$drop_lat = '';$drop_lon = '';
								if($checkRide->row()->booking_information['drop']['location']!=''){
									$drop_location = 1;
									$drop_loc = $checkRide->row()->booking_information['drop']['location'];
									$drop_lat = $checkRide->row()->booking_information['drop']['latlong']['lat'];
									$drop_lon = $checkRide->row()->booking_information['drop']['latlong']['lon'];
								}
								
								$ride_date = get_time_to_string("M d, Y", MongoEPOCH($checkRide->row()->booking_information['est_pickup_date']));
								$pickup_date = '';
								$drop_date = '';
								if ($checkRide->row()->ride_status == 'Booked' || $checkRide->row()->ride_status == 'Confirmed' || $checkRide->row()->ride_status == 'Cancelled' || $checkRide->row()->ride_status == 'Arrived' || $checkRide->row()->ride_status == 'Onride') {
									$pickup_date = get_time_to_string("h:i A", MongoEPOCH($checkRide->row()->booking_information['est_pickup_date']));
								} else {
									$pickup_date = get_time_to_string("h:i A", MongoEPOCH($checkRide->row()->history['begin_ride']));
									$drop_date = get_time_to_string("h:i A", MongoEPOCH($checkRide->row()->history['end_ride']));
								}
								
                                $user_profile = array('user_name' => $userVal->row()->user_name,
                                    'user_id' => (string)$userVal->row()->_id,
                                    'user_email' => $userVal->row()->email,
                                    'phone_number' => (string) $userVal->row()->country_code . $userVal->row()->phone_number,
                                    'user_image' => base_url() . $user_image,
                                    'user_review' => floatval($user_review),
                                    'ride_id' => $ride_id,
                                    'pickup_location' => $checkRide->row()->booking_information['pickup']['location'],
                                    'pickup_lat' => $checkRide->row()->booking_information['pickup']['latlong']['lat'],
                                    'pickup_lon' => $checkRide->row()->booking_information['pickup']['latlong']['lon'],
                                    'pickup_time' => $pickup_date,
									'drop_location' => (string)$drop_location,
									'drop_loc' => (string)$drop_loc,
									'drop_lat' => (string)$drop_lat,
									'drop_lon' => (string)$drop_lon,
									'drop_time' => (string)$drop_date
                                );
                            }
                        }

                        $dropArr = array();
                        if ($checkRide->row()->booking_information['drop']['location']!='') {
                            $dropArr = $checkRide->row()->booking_information['drop'];
                        }
						if (empty($dropArr)) {
							$dropArr = json_decode("{}");
						}
						$distance_unit = $this->data['d_distance_unit'];
						if(isset($checkRide->row()->fare_breakup['distance_unit'])){
							$distance_unit = $checkRide->row()->fare_breakup['distance_unit'];
						}						
						$disp_distance_unit = $distance_unit;
						if($distance_unit == 'km') $disp_distance_unit = $this->format_string('km', 'km');
						if($distance_unit == 'mi') $disp_distance_unit = $this->format_string('mi', 'mi');
						
						if(!empty($summaryArr)){
							$summaryArr['currency'] = (string)$checkRide->row()->currency;
							$summaryArr['ride_fare'] = (string) floatval(round($grand_bill, 2));
						}
						
						$invoice_path = 'trip_invoice/'.$ride_id.'_large.jpg'; 
						if(file_exists($invoice_path)) {
							$invoice_src = base_url().$invoice_path;
						} else {
							$invoice_src = '';
						}
						
						$drop_date_time = '';
						if(isset($checkRide->row()->booking_information['drop_date'])){
							$drop_date_time = get_time_to_string("h:i A", MongoEPOCH($checkRide->row()->booking_information['drop_date'])).' '.$this->format_string('on','on').' '. get_time_to_string("jS M, Y", MongoEPOCH($checkRide->row()->booking_information['drop_date']));
						}
                        $disp_status = '';
                        if ($checkRide->row()->ride_status == 'Booked') {
                            $disp_status = $this->format_string("Booked", "booked");
                        } else if ($checkRide->row()->ride_status == 'Confirmed') {
                            $disp_status = $this->format_string("Accepted", "accepted");
                        } else if ($checkRide->row()->ride_status == 'Cancelled') {
                            $disp_status = $this->format_string("Cancelled", "cancelled");
                        } else if ($checkRide->row()->ride_status == 'Completed') {
                            $disp_status = $this->format_string("Completed", "completed");
                        } else if ($checkRide->row()->ride_status == 'Finished') {
                            $disp_status = $this->format_string("Awaiting Payment", "await_payment");
                        } else if ($checkRide->row()->ride_status == 'Arrived' || $checkRide->row()->ride_status == 'Onride') {
                            $disp_status = $this->format_string("On Ride", "on_ride");
                        }
						
						$trip_type = "Normal";
						if(isset($checkRide->row()->pool_ride)){
							if($checkRide->row()->pool_ride=="Yes"){
								$trip_type = "Share";
							}
						}
						$driver_revenue=0;
						if(isset($checkRide->row()->driver_revenue)){
                            $driver_revenue=$checkRide->row()->driver_revenue+$checkRide->row()->total['tips_amount'];
						}
						$payment_method='';
                        if(isset($checkRide->row()->pay_summary['type']) && $checkRide->row()->pay_summary['type']!='') {
                         $payment_method=$checkRide->row()->pay_summary['type'];
                         $payment_method=str_replace("_"," & ",$payment_method);
                        }
                        $vehicle_no='';
                        if(isset($checkRide->row()->driver['vehicle_no']) && $checkRide->row()->driver['vehicle_no']!='') {
                            $vehicle_no=$checkRide->row()->driver['vehicle_no'];
                        }
                        $passenger_Arr = array();
                        if (isset($checkRide->row()->total['grand_fare'])) {
                            if ($checkRide->row()->total['grand_fare'] >= 0) {
                                $passenger_Arr[] = array("title"=>(string)$this->format_string("Passenger Paid", "passenger_paid"),
                                                         "value"=>(string)number_format($checkRide->row()->total['grand_fare'],2,'.',''),
                                                          'positive'=>'0'
                                                         );
                            }
						}
                       
                        if(isset($checkRide->row()->pay_summary['type']) && $checkRide->row()->pay_summary['type']!='') {
                            if (strpos($checkRide->row()->pay_summary['type'], '_') !== false) {
                               $payment_Arr=@explode('_',$checkRide->row()->pay_summary['type']);  
                               if($payment_Arr[1]=='Cash') {
                                
                                    if (isset($checkRide->row()->total['paid_amount'])) {
                                        if ($checkRide->row()->total['paid_amount'] > 0) {
                                            $passenger_Arr[] = array("title"=>(string)$this->format_string("Cash Received", "cash_received"),
                                                                                "value"=>(string)number_format($checkRide->row()->total['paid_amount'],2,'.',''),
                                                                                'positive'=>'0'
                                                                                );
                                        }
                                    }
                              }
                               if($payment_Arr[0]=='W' || $payment_Arr[0]=='Wallet') {
                                    if (isset($checkRide->row()->total['wallet_usage'])) {
                                        if ($checkRide->row()->total['wallet_usage'] > 0) {
                                            $passenger_Arr[] = array("title"=>(string)$this->format_string("Wallet used", "fare_summary_wallet_used"),
                                                                                "value"=>(string)number_format($checkRide->row()->total['wallet_usage'],2,'.',''),
                                                                                'positive'=>'0'
                                                                                );
                                        }
                                    }
                               }
                               
                              if($payment_Arr[1]=='Gateway') {
                                    if (isset($checkRide->row()->total['paid_amount'])) {
                                        if ($checkRide->row()->total['paid_amount'] > 0) {
                                            $passenger_Arr[] = array("title"=>(string)$this->format_string("Card Payment", "card_payment"),
                                                                                "value"=>(string)number_format($checkRide->row()->total['paid_amount'],2,'.',''),
                                                                                'positive'=>'0'
                                                                                );
                                        }
                                    }
                              }
                             
                            } else {
                                  if($checkRide->row()->pay_summary['type']=='Wallet') {
                                    if (isset($checkRide->row()->total['wallet_usage'])) {
                                        if ($checkRide->row()->total['wallet_usage'] > 0) {
                                            $passenger_Arr[] = array("title"=>(string)$this->format_string("Wallet used", "fare_summary_wallet_used"),
                                                                                "value"=>(string)number_format($checkRide->row()->total['wallet_usage'],2,'.',''),
                                                                                'positive'=>'0'
                                                                                );
                                        }
                                    }
                                }
                               if($checkRide->row()->pay_summary['type']=='Cash') {
                                
                                    if (isset($checkRide->row()->total['paid_amount'])) {
                                        if ($checkRide->row()->total['paid_amount'] > 0) {
                                            $passenger_Arr[] = array("title"=>(string)$this->format_string("Cash Received", "cash_received"),
                                                                                "value"=>(string)number_format($checkRide->row()->total['paid_amount'],2,'.',''),
                                                                                'positive'=>'0'
                                                                                );
                                        }
                                    }
                              }
                              if($checkRide->row()->pay_summary['type']=='Gateway') {
                                    if (isset($checkRide->row()->total['paid_amount'])) {
                                        if ($checkRide->row()->total['paid_amount'] > 0) {
                                            $passenger_Arr[] = array("title"=>(string)$this->format_string("Card Payment", "card_payment"),
                                                                                "value"=>(string)number_format($checkRide->row()->total['paid_amount'],2,'.',''),
                                                                                'positive'=>'0'
                                                                                );
                                        }
                                    }
                              }
                            }
                            if (isset($checkRide->row()->total['coupon_discount'])) {
                                if ($checkRide->row()->total['coupon_discount'] > 0) {
                                    $passenger_Arr[] = array("title"=>(string)$this->format_string("Discount amount", "fare_summary_coupon_discount"),
                                                                        "value"=>(string)number_format($checkRide->row()->total['coupon_discount'],2,'.',''),
                                                                        'positive'=>'2'
                                                                        );
                                }
                            }
                        
                        }
                        $driver_earning = array();
                        if (isset($checkRide->row()->total['grand_fare'])) {
                            if ($checkRide->row()->total['grand_fare'] >= 0) {
                                $driver_earning[] = array("title"=>(string)$this->format_string("Trip Fare", "trip_fare"),
                                                         "value"=>(string)number_format($checkRide->row()->total['grand_fare'],2,'.',''),
                                                          'positive'=>'0'
                                                         );
                            }
						}
                       /*  if (isset($checkRide->row()->total['service_tax'])) {
                            if ($checkRide->row()->total['service_tax'] >= 0) {
                                $driver_earning[] = array("title"=>(string)$this->format_string("Service Tax", "service_tax",FALSE),
                                                                    "value"=>(string)number_format($checkRide->row()->total['service_tax'],2,'.',''),
                                                                    'positive'=>'2'
                                                                    );
                            }
						} */
                        if (isset($checkRide->row()->amount_commission)) {
                            if ($checkRide->row()->amount_commission >= 0) {
                                $driver_earning[] = array("title"=>$this->config->item('email_title')." ".$this->format_string("Fee", "service_fee",FALSE),
                                                                    "value"=>(string)number_format($checkRide->row()->amount_commission,2,'.',''),
                                                                    'positive'=>'2'
                                                                    );
                            }
						}
                        if (isset($checkRide->row()->total['coupon_discount'])) {
							if ($checkRide->row()->total['coupon_discount'] > 0) {
								$driver_earning[] = array("title"=>(string)$this->format_string("Discount amount", "fare_summary_coupon_discount"),
																	"value"=>(string)number_format($checkRide->row()->total['coupon_discount'],2,'.',''),
                                                                    'positive'=>'1'
																	);
							}
						}
                        
                        if (isset($checkRide->row()->total['tips_amount'])) {
							if ($checkRide->row()->total['tips_amount'] > 0) {
								$driver_earning[] = array("title"=>(string)$this->format_string("Tips amount", "fare_summary_tips"),
																	"value"=>(string)number_format($checkRide->row()->total['tips_amount'],2,'.',''),
                                                                    'positive'=>'1'
																	);
							}
						}
                        if(isset($checkRide->row()->driver_revenue)){
                            $driver_revenue=$checkRide->row()->driver_revenue+$checkRide->row()->total['tips_amount'];
                            if ($driver_revenue > 0) {
                                    $driver_earning[] = array("title"=>(string)$this->format_string("Total Earning", "total_earning",FALSE),
                                                                        "value"=>(string)number_format($driver_revenue,2,'.',''),
                                                                        'positive'=>'0'
                                                                        );
                                
                            }
						}
                        
						
						$receive_cash = 'Disable';
						if ($this->config->item('pay_by_cash') != '' && $this->config->item('pay_by_cash') != 'Disable') {
							$receive_cash = 'Enable';
						}
						
						$req_payment = 'Enable';
						$payArr = $this->app_model->get_all_details(PAYMENT_GATEWAY,array("status"=>"Enable"));
						if($payArr->num_rows()==0){
							$req_payment = 'Disable';
						}

                        if (empty($responseArr)) {
                            $responseArr = json_decode("{}");
                        }
                        $returnArr['status'] = '1';
                        $returnArr['response'] = array('receive_cash' => $receive_cash,
                                                    'req_payment' => $req_payment, 
                                                    'currency' => $checkRide->row()->currency,
                                                    'cab_type' => $checkRide->row()->booking_information['service_type'],
                                                    'trip_type' => $trip_type,
                                                    'ride_id' => $checkRide->row()->ride_id,
                                                    'ride_status' => $checkRide->row()->ride_status,
                                                    'disp_status' => $disp_status,
                                                    'do_cancel_action' => (string) $doAction,
                                                    'pay_status' => $pay_status,
                                                    'disp_pay_status' => $disp_pay_status,
                                                    'pickup' => $checkRide->row()->booking_information['pickup'],
                                                    'drop' => $dropArr,
                                                    'ride_date' => (string)$ride_date,
                                                    'pickup_date' =>$pickup_date,
                                                    'drop_date' => $drop_date,
                                                    'summary' => $summaryArr,
                                                    'passenger_fare' => $passenger_Arr,
                                                    'driver_earning' => $driver_earning,
                                                    'fare' => $fareArr,
                                                    'tips' => $tipsArr,
                                                    'continue_ride' => $iscontinue,
                                                    'distance_unit' => $disp_distance_unit,
                                                    'invoice_src' => $invoice_src,
                                                    'user_id' => (string)$checkRide->row()->user['id'],
                                                    'driver_revenue' => $driver_revenue,
                                                    'payment_method'=>$payment_method,
                                                    'vehicle_no'=>$vehicle_no,   
                                                    'user_profile' => $user_profile
													);
                    } else {
                        $returnArr['response'] = $this->format_string("Ride not found", "ride_not_found");
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
        return $returnArr;
    }
	
	
}

/* End of file common.php */
/* Location: ./application/controllers/v8/api/common.php */