<?php if (!defined('BASEPATH')) exit('No direct script access allowed');

/**
* 
* This controller contains the functions related to Drivers at the app end
* @author Casperon
*
* */
class Driver_registration extends MY_Controller {

    function __construct() {
        parent::__construct();
        $this->load->helper(array('cookie', 'date', 'form', 'email'));
        $this->load->library(array('encrypt', 'form_validation'));
        $this->load->model(array('driver_model'));
        $this->load->model(array('app_model'));
		
		/* Authentication Begin */
        $headers = $this->input->request_headers();
		header('Content-type:application/json;charset=utf-8');
		if (array_key_exists("Authkey", $headers)) $auth_key = $headers['Authkey']; else $auth_key = "";
		if(stripos($auth_key,APP_NAME) === false) {
			$cf_fun= $this->router->fetch_method();
			$apply_function = array('login','logout','update_driver_location');
			if(!in_array($cf_fun,$apply_function)){
				show_404();
			}
		}
		
        if (array_key_exists("Apptype", $headers))
            $this->Apptype = $headers['Apptype'];
        if (array_key_exists("Driverid", $headers))
            $this->Driverid = $headers['Driverid'];
        if (array_key_exists("Apptoken", $headers))
            $this->Token = $headers['Apptoken'];
        try {
            if ($this->Driverid != "" && $this->Token != "" && $this->Apptype != "") {
                $deadChk = $this->app_model->get_selected_fields(DRIVERS, array('_id' => MongoID($this->Driverid)), array('push_notification','status'));
                if ($deadChk->num_rows() > 0) {
					$storedToken ='';
                    if (strtolower($deadChk->row()->push_notification['type']) == "ios") {
                        $storedToken = $deadChk->row()->push_notification["key"];
                    }
                    if (strtolower($deadChk->row()->push_notification['type']) == "android") {
                        $storedToken = $deadChk->row()->push_notification["key"];
                    }
					
					$c_fun= (string)$this->router->fetch_method();
					$apply_function = array();
					if(!in_array($c_fun,$apply_function)){
						if(strtolower($deadChk->row()->status)!="active"){
							$is_out_message = $this->format_string('Your account has been modified, please login to again.', 'is_out_message');
							echo json_encode(array("is_out" => "Yes","message" => $is_out_message));die;
						}
						if($storedToken!=''){
							if ($storedToken != $this->Token) {
								echo json_encode(array("is_dead" => "Yes"));die;
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
	* This function returns the list of locations
	*
	**/
    public function init() {
        $returnArr['status'] = '0';
        $returnArr['response'] = '';
        try {
			$locationsArr = array(); $countriesArr = array();
			$locationsVal = $this->app_model->get_selected_fields(LOCATIONS, array('status' => 'Active'), array('city', 'avail_category','fare'), array('city' => 1));
			$langCode = $this->data['langCode'];
            if ($locationsVal->num_rows() > 0) {
                foreach ($locationsVal->result() as $row) {
					$final_cat_list = array();
					if (isset($row->avail_category) && isset($row->fare)) {
						if (!empty($row->avail_category) && !empty($row->fare)) {
							$cat_avail = $row->avail_category;
							$cat_fare = array_keys($row->fare);
							$final_cat_list = array_intersect($cat_avail,$cat_fare);
						}
					}
                    $catsList = $this->app_model->get_available_category(CATEGORY, $final_cat_list);
                    $categoryArr = array();
                    if ($catsList->num_rows() > 0) {
                        foreach ($catsList->result() as $row1) {
							
							$catId =  (string) $row1->_id;
							$catName = $row1->name;
							if(isset($row1->name_languages[$langCode ]) && $row1->name_languages[$langCode ] != ''){
								$catName = $row1->name_languages[$langCode];
							}
							
							$additonalCats = array();
							if($this->data['multiCategoryOption'] == 'ON'){
								foreach($catsList->result() as $cats){
									if(isset($row->fare[$catId]['additional_category'])){
										if(in_array((string)$cats->_id,$row->fare[$catId]['additional_category'])){
											$category_name = $cats->name;
											if(isset($cats->name_languages[$langCode ]) && $cats->name_languages[$langCode ] != ''){
												$category_name = $cats->name_languages[$langCode];
											}
											$additonalCats[]= $category_name;
										}
									}
								}
							}
							
                            $categoryArr[] = array('id' => $catId,
                                'category' => (string) $catName,
								'additional_category' => $additonalCats
                            );
                        }
                    }
					if(!empty($categoryArr)){
						$locationsArr[] = array('id' => (string) $row->_id,
							'city' => (string) $row->city,
							'category' => $categoryArr
						);
					}
				}
			}			
			$countriesVal = $this->app_model->get_selected_fields(COUNTRY, array('status' => 'Active'), array('name', 'dial_code'), array('name' => 'ASC'));
			if ($countriesVal->num_rows() > 0) {
				foreach ($countriesVal->result() as $row) {
					$countriesArr[] = array('id' => (string) $row->_id,
											'name' => (string) $row->name,
											'dial_code' => (string) $row->dial_code
										);
				}
			}				
			$returnArr['status'] = '1'; 
			$returnArr['response'] = array('locations' => $locationsArr,
										'countries' => $countriesArr
									);
            
        } catch (MongoException $ex) {
            $returnArr['response'] = $this->format_string("Error in connection", "error_in_connection");
        }
        $json_encode = json_encode($returnArr, JSON_PRETTY_PRINT);
        echo $this->cleanString($json_encode);
    }
	/**
	*
	* This function returns the list of makers, Models, Vehicle Types
	*
	**/
    public function get_vehicle_details() {
        $returnArr['status'] = '0';
        $returnArr['response'] = '';
        try {
			$vehicle_detailsArr = array();
			$vehicleTypes = $this->app_model->get_all_details(VEHICLES, array('status' => 'Active'));
			$catsList = $this->app_model->get_all_details(CATEGORY,array('status' => 'Active'));
			$modelsList = $this->app_model->get_all_details(MODELS,array('status' => 'Active'));
			$makerList = $this->app_model->get_all_details(BRAND,array('status' => 'Active'));
			$makersArr = array();
			foreach($makerList->result() as $maker){
				if(isset($maker->brand_name)) {
					$maker_id = (string)$maker->_id;
					$modelsArr = array();
					foreach($modelsList->result() as $models){
						if(isset($models->brand) && $models->brand == $maker_id) {
							$years = array(); if(isset($models->year_of_model)) $years = $models->year_of_model;
							$modelsArr[] = array('id' => (string)$models->_id,
															'name' => $models->name,
															'years' => $years
													);
						}
					}
					$makersArr[$maker_id] = array('name' => $maker->brand_name,
																   'models' => $modelsArr
															);
				}
			}
			foreach($vehicleTypes->result() as $vehicles){
				$vehicle_id = (string)$vehicles->_id; 
				$catIds = array();
				foreach($catsList->result() as $cats){
					if(isset($cats->vehicle_type) && in_array($vehicle_id,$cats->vehicle_type)){
						$catIds [] = (string)$cats->_id;
					}
				}
				$vehMakers = array();
				$makerIds = array();
				foreach($modelsList->result() as $models){
					if(isset($models->type) && $vehicle_id == $models->type && isset($models->brand) && $models->brand != ''){
						$maker_id = $models->brand;
						if(isset($makersArr[$maker_id]) && !empty($makersArr[$maker_id]) && !in_array($maker_id,$makerIds)){
							$vehMakers[] =  array(
															'id' => $models->brand,
															'name' => $makersArr[$maker_id]['name'],
															'models' => $makersArr[$maker_id]['models']
															
							);
							$makerIds[] = $maker_id;
						}
					}
				}
				$vehicle_detailsArr[] = array("id"=> $vehicle_id,
											"name"=> $vehicles->vehicle_type,
											"cat_id"=> $catIds ,
											"makers"=> $vehMakers
										);  
			}	
			$returnArr['status'] = '1'; 
			$returnArr['response'] = array('vehicles' => $vehicle_detailsArr);
        } catch (MongoException $ex) {
            $returnArr['response'] = $this->format_string("Error in connection", "error_in_connection");
        }
        $json_encode = json_encode($returnArr, JSON_PRETTY_PRINT);
        echo $this->cleanString($json_encode);
    }
	
	/**
	*
	* This function checks the mobile number is already available or not in driver accounts
	*
	**/
    public function check_mobile() {
        $returnArr['status'] = '0';
        $returnArr['response'] = '';
        try {
            $dail_code = $this->input->post('dail_code');
            $mobile_number = $this->input->post('mobile_number');
            if ($dail_code != "" && $mobile_number != "") {
				$condition = array('dail_code' => $dail_code, 'mobile_number' => $mobile_number);
				$chekMobile = $this->user_model->get_selected_fields(DRIVERS, $condition, array('_id'));
				if ($chekMobile->num_rows() == 0) {
					$otp_string = $this->user_model->get_random_string(6);
					$otp_status = "development";
					if ($this->config->item('twilio_account_type') == 'prod') {
						$otp_status = "production";
						$this->sms_model->opt_for_registration($dail_code, $mobile_number, $otp_string,$this->app_language);
					}
					$returnArr['response'] = $this->format_string('Verification code sent to given number', 'verification_code_sent');
					$returnArr['dail_code'] = $dail_code;
					$returnArr['mobile_number'] = $mobile_number;
					$returnArr['otp_status'] = (string) $otp_status;
					$returnArr['otp'] = (string) $otp_string;
					$returnArr['otp_resend_timer'] = (string) 100;
					$returnArr['status'] = '1';
				} else {
					$returnArr['response'] = $this->format_string('This mobile number already registered', 'mobile_number_already_registered');
				}
            } else {
                $returnArr['response'] = $this->format_string("Enter dailcode and mobile number", "enter_dial_code_and_mobile_number");
            }
        } catch (MongoException $ex) {
            $returnArr['response'] = $this->format_string("Error in connection", "error_in_connection");
        }
        $json_encode = json_encode($returnArr, JSON_PRETTY_PRINT);
        echo $this->cleanString($json_encode);
    }
	
	/**
	*
	* This function checks the email is already available or not in driver accounts
	*
	**/
    public function check_email() {
        $log_content = date("Y-m-d H:i:s")." - Email - ".json_encode($this->input->post()).PHP_EOL;
		file_put_contents('./logss.txt', $log_content, FILE_APPEND);
        $returnArr['status'] = '0';
        $returnArr['response'] = '';
        try {
            $email = strtolower($this->input->post('email'));
            if ($email != "") {
                if (valid_email($email)) {
                    $checkEmail = $this->app_model->get_selected_fields(DRIVERS,array('email' =>$email),array("_id","status"));
                    if ($checkEmail->num_rows() >= 1) {
                        if ($checkEmail->row()->status != "Active") {
                            $returnArr['response'] = $this->format_string("Your account is currenty unavailable", "account_currently_unavailbale");
                        } else {
                            $returnArr['response'] = $this->format_string('Email address already exists', 'email_already_exist');
                        }
                    } else {
						$returnArr['response'] = $this->format_string('Continue to registration', 'continue_to_registration');
						$returnArr['status'] = '1';
                    }
                } else {
                    $returnArr['response'] = $this->format_string("Invalid Email address", "invalid_email_address");
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
	* This function returns the document list
	*
	**/
    public function upload_picture() {
        $returnArr['status'] = '0';
        $returnArr['response'] = '';
        try {
			$max_size = 2; #in MB
			$config['overwrite'] = FALSE;
            $config['encrypt_name'] = TRUE;
            $config['allowed_types'] = 'jpg|jpeg|gif|png|bmp';
            $config['max_size'] = $max_size*1024;
            $config['upload_path'] = './drivers_documents_temp';
            $this->load->library('upload', $config);
			
			if(isset($_FILES["picture"]) && ($_FILES["picture"]["size"]>0)){
				if($_FILES["picture"]["size"]<=($max_size*1024*1000)){
					if ($this->upload->do_upload('picture')) {
						$documentDetails = $this->upload->data();
						$document_name = $documentDetails['file_name'];
						$returnArr['picture_name'] = $document_name;
						$returnArr['picture_path'] = base_url()."drivers_documents_temp/".$document_name;
						$returnArr['response'] = 'Profile picture uploaded';
						$returnArr['status'] = '1';
					} else {
						$documentDetails = $this->upload->display_errors();
						$returnArr['response'] = strip_tags($documentDetails);						
					}
				}else{
					$returnArr['response'] = $this->format_string('Maximum picture size', 'maximum_picture_size').' '.$max_size.' '.$this->format_string('MB', 'size_mb');
				}
			}else{
				$returnArr['response'] = $this->format_string('Picture size is empty, upload valid one', 'picture_size_empty');
			}
        } catch (MongoException $ex) {
            $returnArr['response'] = $this->format_string("Error in connection", "error_in_connection");
        }
        $json_encode = json_encode($returnArr, JSON_PRETTY_PRINT);
        echo $this->cleanString($json_encode);
    }
	
	/**
	*
	* This function checks the vehicle number is available or not
	*
	**/
    public function check_vehicle_number() {
        $returnArr['status'] = '0';
        $returnArr['response'] = '';
        try {
            $vehicle_no = $this->input->post('vehicle_no');
            if ($vehicle_no != "") {
				$checkVehicle = $this->app_model->get_selected_fields(DRIVERS,array('vehicle_number' =>(string)$vehicle_no),array("_id","status"));
				if ($checkVehicle->num_rows()>=0) {
					$returnArr['status'] = '1';
					$returnArr['response'] = $this->format_string('Vehicle number is validated', 'vehicle_number_validated');
				} else {
					$returnArr['response'] = $this->format_string('Vehicle number is already registered', 'vehicle_already_registered');
				}
            } else {
                $returnArr['response'] = $this->format_string("Enter vehicle number", "enter_vehicle_number");
            }
        } catch (MongoException $ex) {
            $returnArr['response'] = $this->format_string("Error in connection", "error_in_connection");
        }
        $json_encode = json_encode($returnArr, JSON_PRETTY_PRINT);
        echo $this->cleanString($json_encode);
    }
    
	/**
	*
	* This function returns the document list
	*
	**/
    public function get_document_list() {
        $returnArr['status'] = '0';
        $returnArr['response'] = '';
        try {
			$documentsArr = array();
			$getDocuments = $this->app_model->get_all_details(DOCUMENTS,array('status' =>"Active"));
			if($getDocuments->num_rows()>0){
				foreach($getDocuments->result() as $doc){
					$documentsArr[] = array("id"=>(string)$doc->_id,
														"name"=>(string)$doc->name,
														"category"=>(string)$doc->category,
														"is_req"=>(string)$doc->hasReq,
														"is_exp"=>(string)$doc->hasExp
													);
				}
			}
			$returnArr['status'] = '1';
			$returnArr['response'] = array("documents"=>$documentsArr);
        } catch (MongoException $ex) {
            $returnArr['response'] = $this->format_string("Error in connection", "error_in_connection");
        }
        $json_encode = json_encode($returnArr, JSON_PRETTY_PRINT);
        echo $this->cleanString($json_encode);
    }
	
	
	/**
	*
	* This function returns the document list
	*
	**/
    public function upload_document() {
        $returnArr['status'] = '0';
        $returnArr['response'] = '';
        try {
			$max_size = 2; #in MB
			$config['overwrite'] = FALSE;
            $config['encrypt_name'] = TRUE;
            $config['allowed_types'] = 'jpg|jpeg|gif|png|bmp|pdf';
            $config['max_size'] = $max_size*1024;
            $config['upload_path'] = './drivers_documents_temp';
            $this->load->library('upload', $config);
			
			if(isset($_FILES["document"]) && ($_FILES["document"]["size"]>0)){
				if($_FILES["document"]["size"]<=($max_size*1024*1000)){
					if ($this->upload->do_upload('document')) {
						$documentDetails = $this->upload->data();
						$document_name = $documentDetails['file_name'];
						$returnArr['document_name'] = $document_name;
						$returnArr['document_path'] = base_url()."drivers_documents_temp/".$document_name;
						$returnArr['response'] = 'Uploaded';
						$returnArr['status'] = '1';
					} else {
						$documentDetails = $this->upload->display_errors();
						$returnArr['response'] = strip_tags($documentDetails);						
					}
				}else{
					$returnArr['response'] = $this->format_string('Maximum document size', 'maximum_document').' '.$max_size.' '.$this->format_string('MB', 'size_mb');
				}
			}else{
				$returnArr['response'] = $this->format_string('Document size is empty, upload valid one', 'document_size_empty');
			}
        } catch (MongoException $ex) {
            $returnArr['response'] = $this->format_string("Error in connection", "error_in_connection");
        }
        $json_encode = json_encode($returnArr, JSON_PRETTY_PRINT);
        echo $this->cleanString($json_encode);
    }
	
	/**
	*
	* This function creates the driver account
	*
	**/
    public function do_register() {
        $returnArr['status'] = '0';
        $returnArr['response'] = '';
        try {			
			$location = (string)$this->input->post("location");
			$category = (string)$this->input->post("category");
			$dail_code = (string)$this->input->post("dail_code");
			$mobile_number = (string)$this->input->post("mobile_number");
			$email = strtolower((string)$this->input->post("email"));
			$driver_name = (string)$this->input->post("driver_name");
			$gender = (string) strtolower($this->input->post("gender"));
			$password = (string)$this->input->post("password");
			
			$vehicle_number = (string)$this->input->post("vehicle_number");
			$vehicle_type = (string)$this->input->post("vehicle_type");
			$vehicle_maker = (string)$this->input->post("vehicle_maker");
			$vehicle_model = (string)$this->input->post("vehicle_model");
			$vehicle_model_year = (string)$this->input->post("vehicle_model_year");
			
			$gcm_id = (string)$this->input->post("gcm_id");
			$deviceToken = (string)$this->input->post("deviceToken");
			$lat = $this->input->post("lat");
			$lon = $this->input->post("lon");
			
			$document = $this->input->post("documents");
		
            $address = (string)$this->input->post("address");
            $city = (string)$this->input->post("city");
            $state = (string)$this->input->post("state");
            $country = (string)$this->input->post("country");
            $postal_code = (string)$this->input->post("postal_code");
			
			$accept_other_category = (string) strtolower($this->input->post("accept_other_category"));
			                        
            if($vehicle_number != '' && $vehicle_type != '' && $vehicle_maker != '' && $vehicle_model != '' && $vehicle_model_year != '' && $location != '' && $category != '' && $gender != ''){
				
				if($address != '' && $city != '' && $state != '' && $country != '' && $postal_code != ''){
					if ($dail_code != "" && $mobile_number != "") {
						$condition = array('dail_code' => $dail_code, 'mobile_number' => $mobile_number);
						$chekMobile = $this->user_model->get_selected_fields(DRIVERS, $condition, array('_id'));
						if ($chekMobile->num_rows() == 0) {
							if ($email != "") {
								if (valid_email($email)) {
									$checkEmail = $this->app_model->get_selected_fields(DRIVERS,array('email' =>$email),array("_id","status"));
									if ($checkEmail->num_rows() ==0) {
										if ($vehicle_number != "") {
											$checkVehicle = $this->app_model->get_selected_fields(DRIVERS,array('vehicle_number' =>(string)$vehicle_number),array("_id"));
											if ($checkVehicle->num_rows() == 0) {
												$driver_commission = 0;
												$cond=array('_id'=> MongoID($location));
												$get_loc_commison = $this->driver_model->get_selected_fields(LOCATIONS,$cond,array('site_commission'));
												if(isset($get_loc_commison->row()->site_commission)){ 
													$driver_commission = $get_loc_commison->row()->site_commission;
												}
												$push_dataI = $push_data = array("key"=>"","type"=>"");
												$key = '';
												if ($gcm_id != "") {
													$key = $gcm_id;
													$push_dataI = array('key' => $gcm_id, 'type' => 'ANDROID');
													$push_data = array('push_notification.key' => $gcm_id, 'push_notification.type' => 'ANDROID');
												}
												if ($deviceToken != "") {
													$key = $deviceToken;
													$push_dataI = array('key' => $deviceToken, 'type' => 'IOS');
													$push_data = array('push_notification.key' => $deviceToken, 'push_notification.type' => 'IOS');
												}
													
												$documentsArr = array();
												if(!empty($document)){
													foreach($document as $id=>$doc){
														$docx_details = $this->driver_model->get_all_details(DOCUMENTS, array('_id' =>MongoID($id)));
														if($docx_details->num_rows()>0){
															$docType = strtolower($docx_details->row()->category);
															$fileName = ''; $expiryDate = '';
															if(isset($doc["expiry_date"]) && $doc["expiry_date"]!="") $expiryDate = $doc["expiry_date"];
															if(isset($doc["file_name"]) && $doc["file_name"]!="") $fileName = $doc["file_name"];
															if($fileName!="" && file_exists('drivers_documents_temp/'.$fileName)){
																@copy('./drivers_documents_temp/' . $fileName, './drivers_documents/' . $fileName);
																$documentsArr[(string)$docType][(string)$id] = array('typeName' => $docx_details->row()->name,
																																			'fileName' =>(string)$fileName,
																																			'expiryDate' =>(string)$expiryDate,
																																			'verify_status' =>'No'
																																	);
															}
														}
													}
												} 
												
												$multi_car_status = 'OFF';
												$additonalCats = array();
												if($accept_other_category == 'on' && $this->data['multiCategoryOption'] == 'ON'){
													$multi_car_status = 'ON';
													$locInfo = $this->app_model->get_selected_fields(LOCATIONS,array('_id' => MongoID($location)),array('fare')); 
													$catsList = $this->app_model->get_selected_fields(CATEGORY,array('status' => 'Active'),array('_id'));
													foreach($catsList->result() as $cats){
														if(isset($locInfo->row()->fare[$category]['additional_category'])){
															if(in_array((string)$cats->_id,$locInfo->row()->fare[$category]['additional_category'])){
																$additonalCats[]= (string)$cats->_id;
															}
														}
													}
												}
												
												$driverArr =array(
														"driver_location"=>(string)$location,
														"category"=>MongoID($category),
														"driver_name"=>(string)$driver_name,
														"email"=>(string)$email,
														"dail_code"=>(string)$dail_code,
														"mobile_number"=>(string)$mobile_number,
														"password"=>(string)md5($password),
														"verify_status"=>"No",
														"status"=>"Active",
														"availability"=>"No",
														"mode"=>"Available",
														"gender" => $gender,
														"vehicle_type"=>(string)$vehicle_type,
														"vehicle_maker"=>(string)$vehicle_maker,
														"vehicle_model"=>(string)$vehicle_model,
														"vehicle_model_year"=>(string)$vehicle_model_year,
														"vehicle_number"=>(string)$vehicle_number,
														"driver_commission"=>floatval($driver_commission),
														"created"=>date('Y-m-d H:i:s'),
														"welcome_mail"=>(string)0,
														"no_of_rides"=>0,
														"documents"=>$documentsArr,
														"push_notification"=>$push_dataI,
														"loc"=>array("lon"=>floatval($lon),"lat"=>floatval($lat)),
														"address"=>array("address"=>$address,
																				"city"=>$city,
																				"state"=>$state,
																				"county"=>$country,
																				"postal_code"=>$postal_code
																		),
														'multi_car_status' => $multi_car_status,
														'additional_category' => $additonalCats
													);
													#echo '<pre>'; print_r($driverArr); die;
													if (!empty($push_data)) {
														$this->driver_model->update_details(DRIVERS, array('push_notification.key' => '', 'push_notification.type' => ''), $push_data);
													}
													$this->app_model->simple_insert(DRIVERS, $driverArr);
													$driver_id = $this->mongo_db->insert_id();
													
													$fields = array('username' => (string)$driver_id,'password' => md5((string)$driver_id));
													$url = $this->data['soc_url'] . 'create-user.php';
													$this->load->library('curl');
													$output = $this->curl->simple_post($url, $fields);
	
													/* Update Stats Starts */
													$current_date = MongoDATE(strtotime(date("Y-m-d 00:00:00")));
													$field = array('driver.hour_' . date('H') => 1, 'driver.count' => 1);
													$this->app_model->update_stats(array('day_hour' => $current_date), $field, 1);
													/* Update Stats End */
													
													$driver_image = USER_PROFILE_IMAGE_DEFAULT;
													$returnArr['status'] = '1';
													$returnArr['response'] = $this->format_string('Your account has been registered successfully', 'your_account_registered');
													$returnArr['action'] = "login";	#<dashboard/login>
													$returnArr['is_alive_other'] = "No";
													$returnArr['driver_image'] = (string) base_url() . $driver_image;
													$returnArr['driver_id'] = (string) $driver_id;
													$returnArr['driver_name'] = (string) $driver_name;
													$returnArr['sec_key'] = md5((string) $driver_id);
													$returnArr['email'] = (string) $email;
													$returnArr['vehicle_number'] = (string) $vehicle_number;
													$returnArr['vehicle_model'] = (string) $vehicle_model;
													$returnArr['key'] = (string) $key;													
													$this->mail_model->send_driver_register_confirmation_mail((string)$driver_id);
											} else {
												$returnArr['response'] = $this->format_string('Vehicle number is already registered', 'vehicle_already_registered');
											}
										} else {
											$returnArr['response'] = $this->format_string("Enter vehicle number", "enter_vehicle_number");
										}
									} else {
										$returnArr['response'] = $this->format_string('Email address already exists', 'email_already_exist');
									}
								} else {
									$returnArr['response'] = $this->format_string("Invalid Email address", "invalid_email_address");
								}
							} else {
								$returnArr['response'] = $this->format_string("Enter email address", "enter_email_address");
							}
						}else{
							$returnArr['response'] = $this->format_string('This mobile number already registered', 'mobile_number_already_registered');
						}
					}else{
						$returnArr['response'] = $this->format_string("Enter dialcode and mobile number", "enter_dial_code_and_mobile_number");
					}
				}else{
					$returnArr['response'] = $this->format_string("Address informations is missing", "address_missing");
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
	
	
	
}

/* End of file driver_registration.php */
/* Location: ./application/controllers/v8/api/driver_registration.php */