<?php if (!defined('BASEPATH')) exit('No direct script access allowed');

/**
* 
* This controller contains the functions related to Drivers at the app end
* @author Casperon
*
* */
class Driver extends MY_Controller {

    function __construct() {
        parent::__construct();
        $this->load->helper(array('cookie', 'date', 'form', 'email'));
        $this->load->library(array('encrypt', 'form_validation'));
        $this->load->model(array('driver_model'));
        $this->load->model(array('app_model'));
        $this->load->model(array('revenue_model'));
		
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
					$apply_function = array('login','logout','update_driver_location');
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
     * Login Driver 
     *
     * */
    public function login() {
        $returnArr['status'] = '0';
        $returnArr['response'] = '';
        try {
            $email = $this->input->post('email');
            $password = $this->input->post('password');
            $gcm_id = $this->input->post('gcm_id');
            $deviceToken = (string) $this->input->post('deviceToken');

            if (is_array($this->input->post())) {
                $chkValues = count(array_filter($this->input->post()));
            } else {
                $chkValues = 0;
            }

            if ($email != '' && $password != '') {
                if (valid_email($email)) {
					$checkAccount=$this->driver_model->get_selected_fields(DRIVERS, array('email' =>strtolower($email)),array('email'));
					if($checkAccount->num_rows() == 1) {
						$checkDriver = $this->driver_model->get_selected_fields(DRIVERS, array('email' => strtolower($email), 'password' => md5($password)), array('email', 'user_name', 'phone_number','push_notification','status'));
						if ($checkDriver->num_rows() == 1) {
							if ($checkDriver->row()->status == 'Active') {
								$push_data = array();
								$key = '';
								if ($gcm_id != "") {
									$key = $gcm_id;
									$push_data = array('push_notification.key' => $gcm_id, 'push_notification.type' => 'ANDROID');
								} else if ($deviceToken != "") {
									$key = $deviceToken;
									$push_data = array('push_notification.key' => $deviceToken, 'push_notification.type' => 'IOS');
								}
								
								$is_alive_other = "No";
								if (isset($checkDriver->row()->push_notification)) {
									if ($checkDriver->row()->push_notification['type'] != '') {
										if ($checkDriver->row()->push_notification['type'] == "ANDROID") {
											$existingKey = $checkDriver->row()->push_notification["key"];
										}
										if ($checkDriver->row()->push_notification['type'] == "IOS") {
											$existingKey = $checkDriver->row()->push_notification["key"];
										}
										if ($existingKey != $key) {
											$is_alive_other = "Yes";
										}
									}
								}
								$returnArr['is_alive_other'] = (string) $is_alive_other;
								
								if (!empty($push_data)) {
									$this->driver_model->update_details(DRIVERS, array('push_notification.key' => '', 'push_notification.type' => ''), $push_data);
									$this->driver_model->update_details(DRIVERS, $push_data, array('_id' => MongoID($checkDriver->row()->_id)));
								}
								
								$returnArr['status'] = '1';
								$returnArr['response'] = $this->format_string('You are Logged In successfully', 'you_logged_in');
								$driverVal = $this->driver_model->get_selected_fields(DRIVERS, array('_id' => MongoID($checkDriver->row()->_id)), array('email', 'image', 'driver_name', 'push_notification', 'vehicle_number', 'vehicle_model', 'password','category','avg_review'));
								if (isset($driverVal->row()->image)) {
									if ($driverVal->row()->image == '') {
										$driver_image = USER_PROFILE_IMAGE_DEFAULT;
									} else {
										$driver_image = USER_PROFILE_IMAGE . $driverVal->row()->image;
									}
								} else {
									$driver_image = USER_PROFILE_IMAGE_DEFAULT;
								}
								$modelVal = $this->driver_model->get_selected_fields(MODELS, array('_id' => MongoID($driverVal->row()->vehicle_model)), array('name', 'brand_name'));
								$vehicle_model = '';
								if ($modelVal->num_rows() > 0) {
									if (isset($modelVal->row()->name)) {
										$vehicle_model = $modelVal->row()->name;
									}
								}
								$categoryInfo = $this->driver_model->get_selected_fields(CATEGORY, array('_id' => MongoID($driverVal->row()->category)), array('_id', 'name', 'brand_name', 'icon_car_image','name_languages'));
								$driver_category = '';
								$category_icon = base_url().ICON_MAP_CAR_IMAGE;
								if ($categoryInfo->num_rows() > 0) {
									$driver_category = $categoryInfo->row()->name;
									if(isset($categoryInfo->row()->name_languages)){
										$langKey = $this->data['sms_lang_code'];
										$arrVal = $categoryInfo->row()->name_languages;
										if(array_key_exists($langKey,$arrVal)){
											if($categoryInfo->row()->name_languages[$langKey]!=""){
												$driver_category = $categoryInfo->row()->name_languages[$langKey];
											}
										}
									}
									if(isset($categoryInfo->row()->icon_car_image)){
										$category_icon = base_url() . ICON_IMAGE . $categoryInfo->row()->icon_car_image;
									}
								}
								$driver_review = 0;
								if (isset($driverVal->row()->avg_review)) {
									$driver_review = $driverVal->row()->avg_review;
								}
								$returnArr['driver_image'] = (string) base_url() . $driver_image;
								$returnArr['driver_id'] = (string) $checkDriver->row()->_id;
								$returnArr['driver_name'] = (string) $driverVal->row()->driver_name;
								$returnArr['sec_key'] = md5((string) $driverVal->row()->_id);
								$returnArr['email'] = (string) $driverVal->row()->email;
								$returnArr['vehicle_number'] = (string) $driverVal->row()->vehicle_number;
								$returnArr['driver_category'] = (string) $driver_category;
								$returnArr['driver_review'] = (string) $driver_review;
								$returnArr['vehicle_model'] = (string) $vehicle_model;
								$returnArr['key'] = (string) $key;
							} else {
								$returnArr['response'] = $this->format_string('Your account have not been activated yet', 'driver_account_not_activated');
							} 
						}else {
							$returnArr['response'] = $this->format_string('Please check the email and password and try again', 'please_check_email_and_password');
						}
				 } else {
					$returnArr['response'] = $this->format_string('Your account does not exist', 'account_not_exists');
				 }
                } else {
                    $returnArr['response'] = $this->format_string("Invalid Email address", "invalid_email_address");
                }
            } else {
                if ($gcm_id == "" && ($email != '' && $password != '')) {
                    $returnArr['response'] = $this->format_string("Cannot recognize your device", "cannot_recognise_device");
                } else {
                    $returnArr['response'] = $this->format_string("Some Parameters are missing", "some_parameters_missing");
                }
            }
        } catch (MongoException $ex) {
            $returnArr['response'] = $this->format_string('Error in connection', 'error_in_connection');
        }
        $json_encode = json_encode($returnArr, JSON_PRETTY_PRINT);
        echo $this->cleanString($json_encode);
    }

    /**
     *
     * Logout Driver 
     *
     * */
    public function logout() {
        $returnArr['status'] = '0';
        $returnArr['response'] = '';
        try {
            $driver_id = $this->input->post('driver_id');
            $device = $this->input->post('device');

            if ($driver_id != '' && $device != '') {
                $checkDriver = $this->driver_model->get_selected_fields(DRIVERS, array('_id' => MongoID($driver_id)), array('push_notification'));
                if ($checkDriver->num_rows() == 1) {
                    if ($device == 'IOS' || $device == 'ANDROID') {
                        $condition = array('_id' => MongoID($driver_id));
                        $this->driver_model->update_details(DRIVERS, array('availability' => 'No', 'push_notification.key' => '', 'push_notification.type' => ''), $condition);
                        $returnArr['status'] = '1';
                        $returnArr['response'] = $this->format_string('You are logged out', 'you_are_logged_out');
                    } else {
                        $returnArr['response'] = $this->format_string('Invalid inputs', 'invalid_input');
                    }
                } else {
                    $returnArr['response'] = $this->format_string("Driver not found", "driver_not_found");
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
     * Update driver availablity
     *
     * */
    public function update_driver_availablity() {
        $returnArr['status'] = '0';
        $returnArr['response'] = '';
        try {
            $driver_id = $this->input->post('driver_id');
            $availability = $this->input->post('availability');
            $distance = floatval($this->input->post('distance'));

            if (is_array($this->input->post())) {
                $chkValues = count(array_filter($this->input->post()));
            } else {
                $chkValues = 0;
            }
            if ($chkValues >= 2) {
                $checkDriver = $this->driver_model->get_selected_fields(DRIVERS, array('_id' => MongoID($driver_id)), array('_id','last_online_time','driver_name','vehicle_number','availability'));
                if ($checkDriver->num_rows() == 1) {
                    $this->app_model->update_drivers_online_duration($checkDriver,$availability);
					if($availability == 'Yes') {
						if($checkDriver->row()->availability != 'Yes'){  #If driver is in already online means no need to update
                            $dataArr = array('last_online_time' => MongoDATE(time()));
                            $this->driver_model->update_details(DRIVERS, $dataArr, array('_id' => MongoID($driver_id)));
                            $dataArr=array('driver_id'=>$driver_id,
                                           'activity_time'=>MongoDATE(time()),
                                           'activity'=>'online'
                            );
                            $this->driver_model->simple_insert(DRIVERS_ACTIVITY,$dataArr);
                        }
					} else {
						if(isset($checkDriver->row()->last_online_time)){
                            $last_online_time=MongoEPOCH($checkDriver->row()->last_online_time);
							update_mileage_system($driver_id,$last_online_time,'free-roaming',$distance,$this->data['d_distance_unit']);
						}
                        $dataArr=array('driver_id'=>$driver_id,
                                       'activity_time'=>MongoDATE(time()),
                                       'activity'=>'offline'
                            );
                        $this->driver_model->simple_insert(DRIVERS_ACTIVITY,$dataArr);
					}
                    $avail_data = array('availability' => $availability, 'last_active_time' => MongoDATE(time()));
                    $this->driver_model->update_details(DRIVERS, $avail_data, array('_id' => MongoID($driver_id)));
					$driver_name = ""; $vehicle_number = "";
					if(isset($checkDriver->row()->driver_name)) $driver_name = $checkDriver->row()->driver_name;
					if(isset($checkDriver->row()->vehicle_number)) $vehicle_number = $checkDriver->row()->vehicle_number;					
                    $returnArr['status'] = '1';
                    $returnArr['driver_name'] = $driver_name;
                    $returnArr['vehicle_number'] = $vehicle_number;
                    $returnArr['response'] = $this->format_string('Availability Updated', 'availability_updated');
                } else {
                    $returnArr['response'] = $this->format_string("Driver not found", "driver_not_found");
                }
            } else {
                $returnArr['response'] = $this->format_string("Some Parameters Missing", "some_parameters_missing");
            }
        } catch (MongoException $ex) {
            $returnArr['response'] = $this->format_string('Error in connection', 'error_in_connection');
        }
        $json_encode = json_encode($returnArr, JSON_PRETTY_PRINT);
        echo $this->cleanString($json_encode);
    }

    /**
     *
     * Update driver Mode
     *
     * */
    public function update_driver_mode() {
        $returnArr['status'] = '0';
        $returnArr['response'] = '';
        try {
            $driver_id = $this->input->post('driver_id');
            $type = $this->input->post('type');
            if ($type == '') {
                $type = 'Available';
            }

            if (is_array($this->input->post())) {
                $chkValues = count(array_filter($this->input->post()));
            } else {
                $chkValues = 0;
            }
            if (isset($_GET['dev'])) {
                if ($_GET['dev'] == 'jj') {
                    $avail_data = array('mode' => $type);
                    $this->driver_model->update_details(DRIVERS, $avail_data, array());
                }
            }
            if ($chkValues >= 2) {
                $checkDriver = $this->driver_model->get_selected_fields(DRIVERS, array('_id' => MongoID($driver_id)), array('_id'));
                if ($checkDriver->num_rows() == 1) {
                    $avail_data = array('mode' => $type);
                    $this->driver_model->update_details(DRIVERS, $avail_data, array('_id' => MongoID($driver_id)));
                    $this->driver_model->update_details(DRIVERS, $avail_data, array());
                    $returnArr['status'] = '1';
                    $returnArr['response'] = $this->format_string('Mode Updated', 'mode_updated');
                } else {
                    $returnArr['response'] = $this->format_string("Driver not found", "driver_not_found");
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
     * This Function return the rider informations
     *
     * */
    public function get_rider_information() {
        $returnArr['status'] = '0';
        $returnArr['response'] = '';
        try {
            $driver_id = $this->input->post('driver_id');
            $ride_id = $this->input->post('ride_id');

            if (is_array($this->input->post())) {
                $chkValues = count(array_filter($this->input->post()));
            } else {
                $chkValues = 0;
            }

            if ($chkValues >= 2) {
                $checkDriver = $this->app_model->get_selected_fields(DRIVERS, array('_id' => MongoID($driver_id)), array('email'));
                if ($checkDriver->num_rows() == 1) {
                    $checkRide = $this->app_model->get_selected_fields(RIDES, array('ride_id' => $ride_id), array('ride_id', 'ride_status', 'booking_information', 'user.id', 'driver.id'));
                    if ($checkRide->num_rows() == 1) {
                        $user_id = $checkRide->row()->user['id'];
                        $checkUser = $this->app_model->get_selected_fields(USERS, array('_id' => MongoID($user_id)), array('email', 'user_name', 'country_code', 'phone_number', 'image', 'avg_review'));
                        $infoArr = array();
                        if ($checkUser->num_rows() == 1) {
                            if ($checkUser->row()->image == '') {
                                $user_image = USER_PROFILE_IMAGE_DEFAULT;
                            } else {
                                $user_image = USER_PROFILE_IMAGE . $checkUser->row()->image;
                            }
                            $user_review = 0;
                            if (isset($checkUser->row()->avg_review)) {
                                $user_review = $checkUser->row()->avg_review;
                            }
                            $infoArr = array('user_name' => $checkUser->row()->user_name,
                                'user_id' => (string) $checkUser->row()->_id,
                                'user_email' => $checkUser->row()->email,
                                'user_phone' => $checkUser->row()->country_code . '' . $checkUser->row()->phone_number,
                                'user_image' => base_url() . $user_image,
                                'user_review' => floatval($user_review),
                                'ride_id' => $ride_id
                            );
                        }

                        if (empty($infoArr)) {
                            $infoArr = json_decode("{}");
                        }
                        $returnArr['status'] = '1';
                        $returnArr['response'] = array('information' => $infoArr);
                    } else {
                        $returnArr['response'] = $this->format_string("Invalid Ride", "invalid_ride");
                    }
                } else {
                    $returnArr['response'] = $this->format_string("Driver not found", "driver_not_found");
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
     * This Function returns the driver rides list
     *
     * */
    public function driver_all_ride_list() {
        $returnArr['status'] = '0';
        $returnArr['response'] = '';
        try {
            $driver_id = (string) $this->input->post('driver_id');
            $type = (string) $this->input->post('trip_type');
            if ($type == '')
                $type = 'all';

            if ($driver_id != '') {
                $driverVal = $this->app_model->get_selected_fields(DRIVERS, array('_id' => MongoID($driver_id)), array('city', 'avail_category'));
                if ($driverVal->num_rows() > 0) {
                    $checkRide = $this->app_model->get_ride_list_for_driver($driver_id, $type, array('booking_information', 'ride_id', 'ride_status'));
                    $rideArr = array();
                    if ($checkRide->num_rows() > 0) {
                        foreach ($checkRide->result() as $ride) {
                            $group = 'all';
                            if ($ride->ride_status == 'Onride' || $ride->ride_status == 'Confirmed' || $ride->ride_status == 'Arrived') {
                                $group = 'onride';
                            } else if ($ride->ride_status == 'Completed' || $ride->ride_status == 'Finished') {
                                $group = 'completed';
                            }
							$ride_id = $ride->ride_id;
							$invoice_src = '';
							$invoice_path = 'trip_invoice/'.$ride_id.'_small.jpg'; 
							if(file_exists($invoice_path)) { $invoice_src = base_url().$invoice_path; }
							
                            $rideArr[] = array('ride_id' => (string) $ride_id,
                                'ride_time' => get_time_to_string("h:i A", MongoEPOCH($ride->booking_information['booking_date'])),
                                'ride_date' => get_time_to_string("jS M, Y", MongoEPOCH($ride->booking_information['booking_date'])),
                                'pickup' => $ride->booking_information['pickup']['location'],
                                'group' => $group,
                                'datetime' => get_time_to_string("d-m-Y", MongoEPOCH($ride->booking_information['booking_date'])),
								'invoice_src' => $invoice_src
                            );
                        }
                    }

                    if (empty($rideArr)) {
                        $rideArr = json_decode("{}");
                    }
                    $total_rides = intval($checkRide->num_rows());
                    $returnArr['status'] = '1';
                    $returnArr['response'] = array('total_rides' => (string) $total_rides, 'rides' => $rideArr);
                } else {
                    $returnArr['response'] = $this->format_string("Driver not found", "driver_not_found");
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
     * This Function return the drivers particular ride details
     *
     * */
    public function view_driver_ride_information() {
        $returnArr['status'] = '0';
        $returnArr['response'] = '';
        try {
            $driver_id = (string) $this->input->post('driver_id');
            $ride_id = (string) $this->input->post('ride_id');

            if ($driver_id != '') {
                $driverVal = $this->app_model->get_selected_fields(DRIVERS, array('_id' => MongoID($driver_id)), array('_id'));
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
                            $userVal = $this->driver_model->get_selected_fields(USERS, array('_id' => MongoID($checkRide->row()->user['id'])), array('_id', 'user_name', 'email', 'image', 'avg_review', 'phone_number', 'country_code', 'push_type', 'push_notification_key'));
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
							$drop_date_time = get_time_to_string("h:i A",  MongoEPOCH($checkRide->row()->booking_information['drop_date'])).' '.$this->format_string('on','on').' '. get_time_to_string("jS M, Y", MongoEPOCH($checkRide->row()->booking_information['drop_date']));
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
                        /* if (isset($checkRide->row()->total['service_tax'])) {
                            if ($checkRide->row()->total['service_tax'] >= 0) {
                                $driver_earning[] = array("title"=>(string)$this->format_string("Service Tax", "service_tax",FALSE),
                                                                    "value"=>(string)number_format($checkRide->row()->total['service_tax'],2,'.',''),
                                                                    'positive'=>'2'
                                                                    );
                            }
						} */
                        if (isset($checkRide->row()->amount_commission)) {
                            if ($checkRide->row()->amount_commission >= 0) {
                                $site_commission=$checkRide->row()->amount_commission-$checkRide->row()->total['service_tax'];
                                $driver_earning[] = array("title"=>$this->config->item('email_title')." ".$this->format_string("Fee", "service_fee",FALSE),
                                                                    "value"=>(string)number_format($site_commission,2,'.',''),
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
						$payArr = $this->driver_model->get_all_details(PAYMENT_GATEWAY,array("status"=>"Enable"));
						if($payArr->num_rows()==0){
							$req_payment = 'Disable';
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
                                                    'vehicle_no'=>$vehicle_no ,
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
        $json_encode = json_encode($returnArr, JSON_PRETTY_PRINT);
        echo $this->cleanString($json_encode);
    }

   
    /**
     *
     * This Function sends the request to riders about payment
     *
     * */
    public function requesting_payment() {
        $returnArr['status'] = '0';
        $returnArr['response'] = '';
        try {
            $driver_id = (string) $this->input->post('driver_id');
            $ride_id = (string) $this->input->post('ride_id');


            if ($driver_id != '') {
                $driverChek = $this->app_model->get_all_details(DRIVERS, array('_id' => MongoID($driver_id)), array());
                if ($driverChek->num_rows() > 0) {
                    $checkRide = $this->app_model->get_all_details(RIDES, array('ride_id' => $ride_id, 'driver.id' => $driver_id));
                    if ($checkRide->num_rows() == 1) {
                        $user_id = $checkRide->row()->user['id'];
                        $userVal = $this->driver_model->get_selected_fields(USERS, array('_id' => MongoID($user_id)), array('_id', 'user_name', 'email', 'image', 'avg_review', 'phone_number', 'country_code', 'push_type', 'push_notification_key'));
                        if (isset($userVal->row()->push_type)) {
                            if ($userVal->row()->push_type != '') {

                                $tip_status = '0';
                                $tips_amount = '0.00';
                                if (isset($checkRide->row()->total['tips_amount'])) {
                                    if ($checkRide->row()->total['tips_amount'] > 0) {
                                        $tip_status = '0';
                                        $tips_amount = (string) $checkRide->row()->total['tips_amount'];
                                    }
                                }
								
								/* Preparing driver information to share with user -- Start */
								$driver_image = USER_PROFILE_IMAGE_DEFAULT;
								if (isset($driverChek->row()->image)) {
									if ($driverChek->row()->image != '') {
										$driver_image = USER_PROFILE_IMAGE . $driverChek->row()->image;
									}
								}
								$driver_review = 0;
								if (isset($driverChek->row()->avg_review)) {
									$driver_review = $driverChek->row()->avg_review;
								}
								$driver_name = '';
								if (isset($driverChek->row()->driver_name)) {
									$driver_name = $driverChek->row()->driver_name;
								}
								$driver_lat = '';$driver_long = '';
								if (isset($driverChek->row()->loc)) {
									$driver_lat = $driverChek->row()->loc['lat'];
									$driver_long = $driverChek->row()->loc['lon'];
								}
								$user_name = $userVal->row()->user_name;
								$user_lat = '';$user_long = '';
								$userLocation = $this->app_model->get_all_details(USER_LOCATION, array('user_id' => MongoID($user_id)));
								if($userLocation->num_rows()>0){
									if(isset($userLocation->row()->geo)){
										$latlong = $userLocation->row()->geo;
										$user_lat = $latlong[1];
										$user_long = $latlong[0];
									}
								}
								$subtotal = 0; $coupon = 0; $service_tax = 0;$total = 0;
								if (isset($checkRide->row()->total['total_fare'])) {
                                    if ($checkRide->row()->total['total_fare'] > 0) {
                                        $subtotal = $checkRide->row()->total['total_fare'];
                                    }
                                }
								if (isset($checkRide->row()->total['coupon_discount'])) {
                                    if ($checkRide->row()->total['coupon_discount'] > 0) {
                                        $coupon = $checkRide->row()->total['coupon_discount'];
                                    }
                                }
								if (isset($checkRide->row()->total['service_tax'])) {
                                    if ($checkRide->row()->total['service_tax'] > 0) {
                                        $service_tax = $checkRide->row()->total['service_tax'];
                                    }
                                }
								if (isset($checkRide->row()->total['grand_fare'])) {
                                    if ($checkRide->row()->total['grand_fare'] > 0) {
                                        $total = $checkRide->row()->total['grand_fare'];
                                    }
                                }
									

                               $message = $this->format_string("your payment is pending", "your_payment_is_pending", '', 'user', (string)$userVal->row()->_id);
                                $currency = $checkRide->row()->currency;
                                $mins = $this->format_string('mins', 'mins');
                                
								$distance_unit = $this->data['d_distance_unit'];
								if(isset($checkRide->row()->fare_breakup['distance_unit'])){
									$distance_unit = $checkRide->row()->fare_breakup['distance_unit'];
								}
								if($distance_unit == 'km'){
									$distance_km = $this->format_string('km', 'km');
								}
                                $options = array('currency' => (string) $currency,
                                    'ride_fare' => (string) $checkRide->row()->total['grand_fare'],
                                    'ride_distance' => (string) $checkRide->row()->summary['ride_distance'] .' ' .$distance_km,
                                    'ride_duration' => (string) $checkRide->row()->summary['ride_duration'] .' '.$mins,
                                    'waiting_duration' => (string) $checkRide->row()->summary['waiting_duration'] .' '. $mins,
                                    'ride_id' => (string) $ride_id,
                                    'user_id' => (string) $user_id,
                                    'tip_status' => (string)$tip_status,
                                    'tips_amount' => (string)$tips_amount,
                                    'driver_name' => (string)$driver_name,
                                    'driver_image' => (string)base_url().$driver_image,
                                    'driver_review' => (string)$driver_review,
                                    'driver_lat' => (string)$driver_lat,
                                    'driver_long' => (string)$driver_long,
                                    'user_name' => (string)$user_name,
                                    'user_lat' => (string)$user_lat,
                                    'user_long' => (string)$user_long,
                                    'subtotal' => (string)$subtotal,
                                    'coupon' => (string)$coupon,
                                    'service_tax' => (string)$service_tax,
                                    'total' => (string)$total,
                                    'base_fare' => (string) $checkRide->row()->total['base_fare']
                                );
								
								
                                if ($userVal->row()->push_type == 'ANDROID') {
                                    if (isset($userVal->row()->push_notification_key['gcm_id'])) {
                                        if ($userVal->row()->push_notification_key['gcm_id'] != '') {
                                            $this->sendPushNotification(array($userVal->row()->push_notification_key['gcm_id']), $message, 'requesting_payment', 'ANDROID', $options, 'USER');
                                        }
                                    }
                                }
                                if ($userVal->row()->push_type == 'IOS') {
                                    if (isset($userVal->row()->push_notification_key['ios_token'])) {
                                        if ($userVal->row()->push_notification_key['ios_token'] != '') {
                                            $this->sendPushNotification(array($userVal->row()->push_notification_key['ios_token']), $message, 'requesting_payment', 'IOS', $options, 'USER');
                                        }
                                    }
                                }
                            }
                        }

                        $returnArr['status'] = '1';
                        $returnArr['response'] = $this->format_string('request sent', 'request_sent');
                    } else {
                        $returnArr['response'] = $this->format_string("Invalid Ride", "invalid_ride");
                    }
                } else {
                    $returnArr['response'] = $this->format_string("Driver not found", "driver_not_found");
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
	* This Function accepting the cash and update the ride payment status
	*
	**/
    public function cash_payment_received() {
        $returnArr['status'] = '0';
        $returnArr['response'] = '';
        try {
            $driver_id = (string) $this->input->post('driver_id');
            $ride_id = (string) $this->input->post('ride_id');
            $amount = $this->input->post('amount');


            if ($driver_id != '' && $ride_id != '' && $amount != '') {
                $driverChek = $this->app_model->get_selected_fields(DRIVERS, array('_id' => MongoID($driver_id)), array());
                if ($driverChek->num_rows() > 0) {
                    $checkRide = $this->app_model->get_all_details(RIDES, array('ride_id' => $ride_id, 'driver.id' => $driver_id));
                    if ($checkRide->num_rows() == 1) {
                        $paid_amount = 0.00;
                        $tips_amount = 0.00;
						
						
						if (isset($checkRide->row()->total['tips_amount'])) {
							$tips_amount = $checkRide->row()->total['tips_amount'];
						}
						
                        if (isset($checkRide->row()->total)) {
                            if (isset($checkRide->row()->total['grand_fare']) && isset($checkRide->row()->total['wallet_usage'])) {
                                $paid_amount = ($checkRide->row()->total['grand_fare']+ $tips_amount) - $checkRide->row()->total['wallet_usage'];
								$paid_amount = round($paid_amount,2);
                            }
                        }
                        $pay_summary = 'Cash';
                        if (isset($checkRide->row()->pay_summary)) {
                            if ($checkRide->row()->pay_summary != '') {
                                if ($checkRide->row()->pay_summary != 'Cash') {
									if($checkRide->row()->pay_summary['type']!="Cash"){
										$pay_summary = $checkRide->row()->pay_summary['type'] . '_Cash';
									}
                                }
                            } else {
                                $pay_summary = 'Cash';
                            }
                        }
                        $pay_summary = array('type' => $pay_summary);
                        $paymentInfo = array('ride_status' => 'Completed',
                            'pay_status' => 'Paid',
                            'history.pay_by_cash_time' => MongoDATE(time()),
                            'total.paid_amount' => round(floatval($paid_amount), 2),
                            'pay_summary' => $pay_summary
                        );
						
						if($checkRide->row()->pay_status !="Paid"){
							$this->app_model->update_details(RIDES, $paymentInfo, array('ride_id' => $ride_id));
							/* Update Stats Starts */
							$current_date = MongoDATE(strtotime(date("Y-m-d 00:00:00")));
							$field = array('ride_completed.hour_' . date('H') => 1, 'ride_completed.count' => 1);
							$this->app_model->update_stats(array('day_hour' => $current_date), $field, 1);
							/* Update Stats End */
							$avail_data = array('mode' => 'Available', 'availability' => 'Yes');
							$this->app_model->update_details(DRIVERS, $avail_data, array('_id' => MongoID($driver_id)));
							$trans_id = time() . rand(0, 2578);
							$transactionArr = array('type' => 'cash',
								'amount' => floatval($paid_amount),
								'trans_id' => $trans_id,
								'trans_date' => MongoDATE(time())
							);
							$this->app_model->simple_push(PAYMENTS, array('ride_id' => $ride_id), array('transactions' => $transactionArr));

							$user_id = $checkRide->row()->user['id'];
							$userVal = $this->driver_model->get_selected_fields(USERS, array('_id' => MongoID($user_id)), array('_id', 'user_name', 'email', 'image', 'avg_review', 'phone_number', 'country_code', 'push_type', 'push_notification_key'));
							if (isset($userVal->row()->push_type)) {
								if ($userVal->row()->push_type != '') {
									$message = $this->format_string("your billing amount paid successfully", "your_billing_amount_paid", '', 'user', (string)$userVal->row()->_id);
									$options = array('ride_id' => (string) $ride_id, 'user_id' => (string) $user_id);
									if ($userVal->row()->push_type == 'ANDROID') {
										if (isset($userVal->row()->push_notification_key['gcm_id'])) {
											if ($userVal->row()->push_notification_key['gcm_id'] != '') {
												$this->sendPushNotification(array($userVal->row()->push_notification_key['gcm_id']), $message, 'payment_paid', 'ANDROID', $options, 'USER');
											}
										}
									}
									if ($userVal->row()->push_type == 'IOS') {
										if (isset($userVal->row()->push_notification_key['ios_token'])) {
											if ($userVal->row()->push_notification_key['ios_token'] != '') {
												$this->sendPushNotification(array($userVal->row()->push_notification_key['ios_token']), $message, 'payment_paid', 'IOS', $options, 'USER');
											}
										}
									}
								}
							}
							
							#	make and sending invoice to the rider 	#
							$this->app_model->update_ride_amounts($ride_id);
							$fields = array(
								'ride_id' => (string) $ride_id
							);
							$url = base_url().'prepare-invoice';
							$this->load->library('curl');
							$output = $this->curl->simple_post($url, $fields);
							
						}

                        $returnArr['status'] = '1';
                        $returnArr['response'] = $this->format_string('amount received', 'amount_received');
                    } else {
                        $returnArr['response'] = $this->format_string("Invalid Ride", "invalid_ride");
                    }
                } else {
                    $returnArr['response'] = $this->format_string("Driver not found", "driver_not_found");
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
     * This function returns the banking detail of the driver
     *
     * */
    public function get_banking_details() {
        $returnArr['status'] = '0';
        $returnArr['response'] = '';
        try {
            $driver_id = $this->input->post('driver_id');

            if ($driver_id != '') {
                $checkDriver = $this->driver_model->get_selected_fields(DRIVERS, array('_id' => MongoID($driver_id)), array('_id', 'banking'));
                if ($checkDriver->num_rows() == 1) {
                    $bankingArr = array("acc_holder_name" => (string) '',
                        "acc_holder_address" => (string) '',
                        "acc_number" => (string) '',
                        "bank_name" => (string) '',
                        "branch_name" => (string) '',
                        "branch_address" => (string) '',
                        "swift_code" => (string) '',
                        "routing_number" => (string) ''
                    );
                    if (isset($checkDriver->row()->banking)) {
                        if (is_array($checkDriver->row()->banking)) {
                            if (!empty($checkDriver->row()->banking)) {
                                $bankingArr = $checkDriver->row()->banking;
                            }
                        }
                    }
                    if (empty($bankingArr)) {
                        $bankingArr = json_decode("{}");
                    }
                    $returnArr['status'] = '1';
                    $returnArr['response'] = array('banking' => $bankingArr);
                } else {
                    $returnArr['response'] = $this->format_string("Driver not found", "driver_not_found");
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
     * This function save and return the banking detail of the driver
     *
     * */
    public function save_banking_details() {
        $returnArr['status'] = '0';
        $returnArr['response'] = '';
        try {
            $driver_id = $this->input->post('driver_id');

            if (is_array($this->input->post())) {
                $chkValues = count(array_filter($this->input->post()));
            } else {
                $chkValues = 0;
            }

            if ($driver_id != '' && $chkValues >= 6) {
                $checkDriver = $this->driver_model->get_selected_fields(DRIVERS, array('_id' => MongoID($driver_id)), array('_id', 'banking'));
                if ($checkDriver->num_rows() == 1) {

                    $banking = array("acc_holder_name" => trim($this->input->post('acc_holder_name')),
                        "acc_holder_address" => trim($this->input->post('acc_holder_address')),
                        "acc_number" => trim($this->input->post('acc_number')),
                        "bank_name" => trim($this->input->post('bank_name')),
                        "branch_name" => trim($this->input->post('branch_name')),
                        "branch_address" => trim($this->input->post('branch_address')),
                        "swift_code" => trim($this->input->post('swift_code')),
                        "routing_number" => trim($this->input->post('routing_number'))
                    );
                    $dataArr = array('banking' => $banking);
                    $this->driver_model->update_details(DRIVERS, $dataArr, array('_id' => MongoID($driver_id)));

                    $checkDriver = $this->driver_model->get_selected_fields(DRIVERS, array('_id' => MongoID($driver_id)), array('_id', 'banking'));
                    $bankingArr = array();
                    if (isset($checkDriver->row()->banking)) {
                        if (is_array($checkDriver->row()->banking)) {
                            $bankingArr = $checkDriver->row()->banking;
                        }
                    }
                    if (empty($bankingArr)) {
                        $bankingArr = json_decode("{}");
                    }
                    $returnArr['status'] = '1';
                    $returnArr['response'] = array('banking' => $bankingArr);
                } else {
                    $returnArr['response'] = $this->format_string("Driver not found", "driver_not_found");
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
     * This Function returns the driver payment list
     *
     * */
    public function driver_all_payment_list() {
        $returnArr['status'] = '0';
        $returnArr['response'] = '';
        try {
            $driver_id = (string) $this->input->post('driver_id');
            if ($driver_id != '') {
                $driverVal = $this->app_model->get_selected_fields(DRIVERS, array('_id' => MongoID($driver_id)), array('city', 'avail_category'));
                if ($driverVal->num_rows() > 0) {
                    $paymentArr = array();
                    $billing_next="+".$this->data['billing_cycle']." day";
                    $billing_period=date("F d",strtotime("+1 day",strtotime($this->data['last_billing_date'])))." - ".date("F d",strtotime($billing_next,strtotime($this->data['last_billing_date'])));
                    $billing_from  = date('Y-m-d',strtotime("+1 day",strtotime($this->data['last_billing_date'])));
                    $billing_to = date('Y-m-d');
                    $day_earning=array();
                    $fromdate=strtotime($billing_from." 00:00:00");
                    $todate=strtotime($billing_to." 23:59:59");
                    $rideSummary = $this->revenue_model->get_driver_summary($fromdate,$todate,$driver_id);
                    $net_fare=0;
                    $net_array=array();
                    $trips_array=array();
                    $ratting_array=array();
                    if(!empty($rideSummary['result'])) {
                        $driver_earnings=$rideSummary['result'][0]['driver_earnings'];
                        $total_trips=$rideSummary['result'][0]['totalTrips'];
                        $paymentArr[] = array(
                                'amount' => (string)$driver_earnings,
                                'total_rides' => (string)$total_trips,
                                'pay_duration_from' =>date("Y-m-d",strtotime("+1 day",strtotime($this->data['last_billing_date']))),
                                "billing_period"=>date("M d",strtotime("+1 day",strtotime($this->data['last_billing_date'])))." - ".date("M d",strtotime($billing_next,strtotime($this->data['last_billing_date']))),
                                'pay_duration_to' =>date("Y-m-d",strtotime($billing_next,strtotime($this->data['last_billing_date']))),
                                
                               
                         );
                    }
                    $billingDetails = $this->app_model->get_all_details(BILLINGS, array('driver_id' => $driver_id), array('bill_date' => 'DESC'));
                    if ($billingDetails->num_rows() > 0) {
                        foreach ($billingDetails->result() as $bill) {
                            $paymentArr[] = array('pay_duration_from' => (string) get_time_to_string("Y-m-d", MongoEPOCH($bill->bill_from)),
                                "billing_period"=>get_time_to_string("M d", MongoEPOCH($bill->bill_from))." - ".get_time_to_string("M d", MongoEPOCH($bill->bill_to)),
                                'pay_duration_to' => (string) get_time_to_string("Y-m-d", MongoEPOCH($bill->bill_to)),
                                'amount' => (string) $bill->driver_earnings,
                                'total_rides' => (string) $bill->total_rides
                            );
                        }
                    }
                    if (empty($paymentArr)) {
                        $paymentArr = json_decode("{}");
                    }
                    $total_payments=count($paymentArr);
                    $returnArr['status'] = '1';
                    $returnArr['response'] = array('total_payments' => (string) $total_payments, 'payments' => $paymentArr, 'currency' => (string) $this->data['dcurrencyCode']);
                } else {
                    $returnArr['response'] = $this->format_string("Driver not found", "driver_not_found");
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
     * This Function returns the driver payment summary
     *
     * */
    public function view_driver_payment_information() {
        $returnArr['status'] = '0';
        $returnArr['response'] = '';
        try {
            $driver_id = (string) $this->input->post('driver_id');
            $invoice_id = (string) $this->input->post('pay_id');

            if ($driver_id != '' && $invoice_id != '') {
                $driverVal = $this->app_model->get_selected_fields(DRIVERS, array('_id' => MongoID($driver_id)), array('city', 'avail_category'));
                if ($driverVal->num_rows() > 0) {
                    $total_payments = 0;
                    $paymentArr = array();
                    $listsArr = array();

                    $billingDetails = $this->app_model->get_all_details(BILLINGS, array('invoice_id' => floatval($invoice_id)));
                    if ($billingDetails->num_rows() > 0) {
                        $paymentArr[] = array('pay_id' => (string) $billingDetails->row()->invoice_id,
                            'pay_duration_from' => (string) get_time_to_string("d-m-Y", ((string)$billingDetails->row()->bill_from)),
                            'pay_duration_to' => (string) get_time_to_string("d-m-Y", MongoEPOCH($billingDetails->row()->bill_to)),
                            'amount' => (string) $billingDetails->row()->driver_earnings,
                            'pay_date' => (string) get_time_to_string("d-m-Y", MongoEPOCH($billingDetails->row()->bill_date))
                        );
                        $bill_from=MongoEPOCH($billingDetails->row()->bill_from);
                        $bill_to=MongoEPOCH($billingDetails->row()->bill_to);
                        $ridesVal = $this->app_model->get_billing_rides($bill_from,$bill_to,$billingDetails->row()->driver_id);
                        if ($ridesVal->num_rows() > 0) {
                            $total_payments = $ridesVal->num_rows();
                            foreach ($ridesVal->result() as $rides) {
                                $listsArr[] = array('ride_id' => (string) $rides->ride_id,
                                    'amount' => (string) $rides->driver_revenue,
                                    'ride_date' => (string) date("d-m-Y", MongoEPOCH($rides->booking_information['pickup_date']))
                                );
                            }
                        }
                    }
					
                    if (empty($paymentArr)) {
                        $paymentArr = json_decode("{}");
                    }if (empty($listsArr)) {
                        $listsArr = json_decode("{}");
                    }
                    $returnArr['status'] = '1';
                    $returnArr['response'] = array('total_payments' => (string) $total_payments, 
																'payments' => $paymentArr, 
																'listsArr' => $listsArr, 
																'currency' => (string) $this->data['dcurrencyCode']
															);
                } else {
                    $returnArr['response'] = $this->format_string("Driver not found", "driver_not_found");
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
     * This Function complete the free trip 
     *
     * */
    public function trip_completed() {
        $returnArr['status'] = '0';
        $returnArr['response'] = '';
        try {
            $driver_id = (string) $this->input->post('driver_id');
            $ride_id = (string) $this->input->post('ride_id');


            if ($driver_id != '' && $ride_id != '') {
                $driverChek = $this->app_model->get_selected_fields(DRIVERS, array('_id' => MongoID($driver_id)), array());
                if ($driverChek->num_rows() > 0) {
                    $checkRide = $this->app_model->get_all_details(RIDES, array('ride_id' => $ride_id, 'driver.id' => $driver_id));
                    if ($checkRide->num_rows() == 1) {
                        $paid_amount = 0.00;
                        $pay_summary = array('type' => 'FREE');
                        $paymentInfo = array('ride_status' => 'Completed',
                            'pay_status' => 'Paid',
                            'history.pay_by_coupon_time' => MongoDATE(time()),
                            'total.paid_amount' => round(floatval($paid_amount), 2),
                            'pay_summary' => $pay_summary
                        );
                        $this->app_model->update_details(RIDES, $paymentInfo, array('ride_id' => $ride_id));

                        /* Update Stats Starts */
                        $current_date = MongoDATE(strtotime(date("Y-m-d 00:00:00")));
                        $field = array('ride_completed.hour_' . date('H') => 1, 'ride_completed.count' => 1);
                        $this->app_model->update_stats(array('day_hour' => $current_date), $field, 1);
                        /* Update Stats End */

                        $avail_data = array('mode' => 'Available', 'availability' => 'Yes');
                        $this->app_model->update_details(DRIVERS, $avail_data, array('_id' => MongoID($driver_id)));
                        $trans_id = time() . rand(0, 2578);
                        $transactionArr = array('type' => 'coupon',
                            'amount' => floatval($paid_amount),
                            'trans_id' => $trans_id,
                            'trans_date' => MongoDATE(time())
                        );
                        $this->app_model->simple_push(PAYMENTS, array('ride_id' => $ride_id), array('transactions' => $transactionArr));

                        $user_id = $checkRide->row()->user['id'];
                        $userVal = $this->driver_model->get_selected_fields(USERS, array('_id' => MongoID($user_id)), array('_id', 'user_name', 'email', 'image', 'avg_review', 'phone_number', 'country_code', 'push_type', 'push_notification_key'));
                        if (isset($userVal->row()->push_type)) {
                            if ($userVal->row()->push_type != '') {
                                $message = $this->format_string("your billing amount paid successfully", "your_billing_amount_paid");
                                $options = array('ride_id' => (string) $ride_id, 'user_id' => (string) $user_id);
                                if ($userVal->row()->push_type == 'ANDROID') {
                                    if (isset($userVal->row()->push_notification_key['gcm_id'])) {
                                        if ($userVal->row()->push_notification_key['gcm_id'] != '') {
                                            $this->sendPushNotification(array($userVal->row()->push_notification_key['gcm_id']), $message, 'payment_paid', 'ANDROID', $options, 'USER');
                                        }
                                    }
                                }
                                if ($userVal->row()->push_type == 'IOS') {
                                    if (isset($userVal->row()->push_notification_key['ios_token'])) {
                                        if ($userVal->row()->push_notification_key['ios_token'] != '') {
                                            $this->sendPushNotification(array($userVal->row()->push_notification_key['ios_token']), $message, 'payment_paid', 'IOS', $options, 'USER');
                                        }
                                    }
                                }
                            }
                        }
						#	make and sending invoice to the rider 	#
						$this->app_model->update_ride_amounts($ride_id);
						$fields = array(
							'ride_id' => (string) $ride_id
						);
						$url = base_url().'prepare-invoice';
						$this->load->library('curl');
						$output = $this->curl->simple_post($url, $fields);
						
						
                        $returnArr['status'] = '1';
                        $returnArr['response'] = $this->format_string('Ride Completed', 'ride_completed');
                    } else {
                        $returnArr['response'] = $this->format_string("Invalid Ride", "invalid_ride");
                    }
                } else {
                    $returnArr['response'] = $this->format_string("Driver not found", "driver_not_found");
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
	* This Function returns the trip payment process
	*
	**/
    public function check_trip_payment_status() {
        $responseArr['status'] = '0';
        $responseArr['response'] = '';
        try {
            $driver_id = $this->input->post('driver_id');
            $ride_id = $this->input->post('ride_id');
            if ($driver_id != '' && $ride_id != '') {
                $checkDriver = $this->app_model->get_selected_fields(DRIVERS, array('_id' => MongoID($driver_id)), array());
                if ($checkDriver->num_rows() == 1) {
					$checkRide = $this->app_model->get_selected_fields(RIDES, array('ride_id' => $ride_id, 'driver.id' => $driver_id), array('ride_id', 'ride_status','pay_status', 'booking_information','driver_review_status'));
                    if ($checkRide->num_rows() == 1) {
						$trip_waiting = 'Yes';
						$ratting_submited = 'No';
						if($checkRide->row()->ride_status=='Completed'){
							$trip_waiting = 'No';
						}
						if($checkRide->row()->ride_status=='Finished'){
							$trip_waiting = 'Yes';
						}
						
						if($trip_waiting == 'Yes'){
							if(isset($checkRide->row()->driver_review_status)){
								if($checkRide->row()->driver_review_status=='Yes'){
									$ratting_submited = 'Yes';
								}
							}
						}
						if($ratting_submited == 'Yes'){
							$ratting_pending = 'No';
						}else{
							$ratting_pending = 'Yes';
						}
						
						$responseArr['status'] = '1';
						$responseArr['response'] = array('trip_waiting'=>(string)$trip_waiting,
														'ratting_pending'=>(string)$ratting_pending,
														);
					}else{
						$responseArr['response'] = $this->format_string('Invalid Ride', 'invalid_ride');
					}
                } else {
                    $responseArr['response'] = $this->format_string('Authentication Failed','authentication_failed');
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
	* Deduct the automatic payment for a trip while end the trip
	*
	**/
    public function auto_payment_deduct($ride_id=''){
		$rideinfoUpdated=$this->app_model->get_all_details(RIDES, array('ride_id' => $ride_id));
		$bayMethod ='';
		if($rideinfoUpdated->num_rows() ==1){
			$user_id=$rideinfoUpdated->row()->user['id'];
			$wallet_amount=$this->app_model->get_all_details(WALLET,array('user_id'=>MongoID($user_id)));
			$total_grand_fare = $rideinfoUpdated->row()->total['grand_fare'];
			if($wallet_amount->num_rows() >0){
				if($total_grand_fare <= $wallet_amount->row()->total){
					$bayMethod = 'wallet';
				}else{
					$bayMethod = 'stripe';
				}
			} else {
				$bayMethod = 'stripe';
			}
			$is_completed= 'No';
			if($bayMethod == 'wallet'){
				$bal_walletamount=($wallet_amount->row()->total-$total_grand_fare);
				$walletamount=array('total'=>floatval($bal_walletamount));
				$this->app_model->update_details(WALLET,$walletamount,array('user_id'=>MongoID($user_id)));
				$txn_time = time() . rand(0, 2578);
				$initialAmt = array('type' => 'DEBIT',
								   'debit_type' => 'payment',
								   'ref_id' => $ride_id,
								   'trans_amount' => floatval($total_grand_fare),
								   'avail_amount' => floatval($bal_walletamount),
								   'trans_date' => MongoDATE(time()),
								   'trans_id' => $txn_time
								);
				$this->app_model->simple_push(WALLET, array('user_id' => MongoID($user_id)), array('transactions' => $initialAmt));
				$is_completed= 'Yes';
			}else if($bayMethod == 'stripe'){
				$stripe_settings = $this->data['stripe_settings'];
				if($stripe_settings['status'] == 'Enable'){
					$getUsrCond = array('_id' => MongoID($user_id));
					$get_user_info = $this->app_model->get_selected_fields(USERS, $getUsrCond, array('email', 'stripe_customer_id')); 
					$email = $get_user_info->row()->email;
					$stripe_customer_id = '';
					$auto_pay_status = 'No';
					if (isset($get_user_info->row()->stripe_customer_id)) {
						$stripe_customer_id = $get_user_info->row()->stripe_customer_id;
						if ($stripe_customer_id != '') {
							$auto_pay_status = 'Yes';
						}
					}
					
					if($auto_pay_status == 'Yes'){
						require_once('./stripe/lib/Stripe.php');

						$stripe_settings = $this->data['stripe_settings'];
						$secret_key = $stripe_settings['settings']['secret_key'];
						$publishable_key = $stripe_settings['settings']['publishable_key'];

						$stripe = array(
							"secret_key" => $secret_key,
							"publishable_key" => $publishable_key
						);
						$description = ucfirst($this->config->item('email_title')) . ' - trip payment';
						
						
						$currency = $this->data['dcurrencyCode'];
						if(isset($rideinfoUpdated->row()->currency)) $currency = $rideinfoUpdated->row()->currency;
						$amounts = $this->get_stripe_currency_smallest_unit($total_grand_fare,$currency);
						
						Stripe::setApiKey($secret_key);
						
						
						try {
							if ($stripe_customer_id!='') {
								// Charge the Customer instead of the card
								$charge = Stripe_Charge::create(array(
											"amount" => $amounts, # amount in cents, again
											"currency" => $currency,
											"customer" => $stripe_customer_id,
											"description" => $description)
								);

								$paymentData=array('user_id' => $user_id, 
													'ride_id' => $ride_id, 
													'payType' => 'stripe', 
													'stripeTxnId' => $charge['id']
												);
								$is_completed= 'Yes';
								$strip_txnid=$charge['id'];
							}
						} catch (Exception $e) {
							$error = $e->getMessage();
						}
					}
					
				}
			}
			
			
			if($is_completed == 'Yes'){
				###	Update into the ride and driver collection ###
				if ($rideinfoUpdated->row()->pay_status == 'Pending' || $rideinfoUpdated->row()->pay_status == 'Processing') {
					if (isset($rideinfoUpdated->row()->total)) {
                        if (isset($rideinfoUpdated->row()->total['grand_fare'])) {
                            $paid_amount = round($rideinfoUpdated->row()->total['grand_fare'], 2);
                        }
                    }
                    if($bayMethod=='stripe'){
						$pay_summary = 'Gateway';
						$trans_id=$strip_txnid;
						$type='Card';
                    } else if($bayMethod == 'wallet'){
						$pay_summary = 'Wallet';
						$trans_id  =$txn_time; 
						$type='wallet';
                    }
                    $pay_summary = array('type' => $pay_summary);
                    $paymentInfo = array('ride_status' => 'Completed',
                        'pay_status' => 'Paid',
                        'total.paid_amount' => round(floatval($paid_amount), 2),
                        'pay_summary' => $pay_summary
                    );
					if($bayMethod=='stripe'){
						$paymentInfo['history.pay_by_gateway_time'] = MongoDATE(time());
                    } else if($bayMethod == 'wallet'){
						$paymentInfo['history.wallet_usage_time'] = MongoDATE(time());
                    }
                    $this->app_model->update_details(RIDES, $paymentInfo, array('ride_id' => $ride_id));
                    /* Update Stats Starts */
                    $current_date = MongoDATE(strtotime(date("Y-m-d 00:00:00")));
                    $field = array('ride_completed.hour_' . date('H') => 1, 'ride_completed.count' => 1);
                    $this->app_model->update_stats(array('day_hour' => $current_date), $field, 1);
                    /* Update Stats End */
                    $avail_data = array('mode' => 'Available', 'availability' => 'Yes');
                    $driver_id = $rideinfoUpdated->row()->driver['id'];
                    $this->app_model->update_details(DRIVERS, $avail_data, array('_id' => MongoID($driver_id)));
                    $transactionArr = array('type' => $type,
                        'amount' => floatval($paid_amount),
                        'trans_id' => $trans_id,
                        'trans_date' => MongoDATE(time())
                    );
                    $this->app_model->simple_push(PAYMENTS, array('ride_id' => $ride_id), array('transactions' => $transactionArr));
				}
     
			}
        
		}   
	}
	
	
	
	/**
	*
	* This Function returns the trip information to drivers
	*
	**/
    public function get_trip_information() {		
        $responseArr['status'] = '0';
        $responseArr['response'] = '';
        try {
            $driver_id = $this->input->post('driver_id');
            $ride_id = $this->input->post('ride_id');
            if ($driver_id != '') {
				$this->load->helper('ride_helper');
				get_trip_information($driver_id); exit;
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
	*	This function will update the information for rides request acknowledgement
	*
	**/	
	public function ack_ride_request() {
        $returnArr['status'] = '0';
        $returnArr['response'] = '';
		
		try {
			$ack_id = (string)$this->input->post('ack_id');
			if($ack_id !=''){
				/** req history update  **/
				$dataArr = array('status' => 'received','received_time' => MongoDATE(time()));
				$this->app_model->update_details(RIDE_REQ_HISTORY,$dataArr,array('_id' => MongoID($ack_id)));
				/*******************/
				$returnArr['status'] = '1';
				$returnArr['response'] = $this->format_string("Request Acknowledged", "request_acknowledged");
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
	public function deny_ride_request() {
        $returnArr['status'] = '0';
        $returnArr['response'] = '';
		
		try {
			$ack_id = (string)$this->input->post('ack_id');
			if($ack_id != ''){
				/** req history update  **/
				$dataArr = array('status' => 'declined','declined_time' => MongoDATE(time()));
				$this->app_model->update_details(RIDE_REQ_HISTORY,$dataArr,array('_id' => MongoID($ack_id)));
				
				$returnArr['status'] = '1';
				$returnArr['response'] = $this->format_string("Request Denied Successfully", "request_denied_successfully");
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
	*	This function will update the driver location and send the location to user by notifications
	*
	**/	
	public function driver_update_ride_location() {
		$returnArr['status'] = '0';
		$returnArr['response'] = '';
		
		try {
			$driver_id = (string)$this->input->post('driver_id');
			$ride_id = (string)$this->input->post('ride_id');
			$lat = (string)$this->input->post('lat');
			$lon = (string)$this->input->post('lon');
			$bearing = (string)$this->input->post('bearing');
			
			if($driver_id!='' && $ride_id!='' && $lat!='' && $lon!=''){
				$checkDriver = $this->app_model->get_selected_fields(DRIVERS, array('_id' => MongoID($driver_id)), array('email'));
                if ($checkDriver->num_rows() == 1) {
                    $checkRide = $this->app_model->get_selected_fields(RIDES, array('ride_id' => $ride_id), array('ride_id', 'ride_status','user'));
                    if ($checkRide->num_rows() == 1) {
						$ride_location[] = array('lat' => $lat,
																		'lon' => $lon,
																		'update_time' => MongoDATE(time())
																		);
						$checkRideHistory = $this->app_model->get_selected_fields(RIDE_HISTORY, array('ride_id' => $ride_id), array('values'));
						if($checkRideHistory->num_rows()>0){
							if(!empty($travel_historyArr)){
								$this->app_model->simple_push(RIDE_HISTORY,array('ride_id' => $ride_id),array('values' => $rowVal));
							}
						}else{
							if(!empty($travel_historyArr)){
								$this->app_model->simple_insert(RIDE_HISTORY,array('ride_id' => $ride_id,'values' => $travel_historyArr));
							}
						}
						/* Notification to user about driver current ride location */
						$user_id = $checkRide->row()->user['id'];
						$userVal = $this->app_model->get_selected_fields(USERS, array('_id' => MongoID($user_id)), array('_id', 'user_name', 'email', 'image', 'avg_review', 'phone_number', 'country_code', 'push_type', 'push_notification_key'));
						if (isset($userVal->row()->push_type)) {
							if ($userVal->row()->push_type != '') {
								$message = $this->format_string('Driver current ride location', 'driver_curr_ride_loc', '', 'user', (string)$userVal->row()->_id);
								$options = array("action"=>"driver_loc",'ride_id' => (string) $ride_id, 'latitude' => (string) $lat, 'longitude' => (string) $lon,'bearing' => (string) $bearing);
								if ($userVal->row()->push_type == 'ANDROID') {
									if (isset($userVal->row()->push_notification_key['gcm_id'])) {
										if ($userVal->row()->push_notification_key['gcm_id'] != '') {
											$this->sendPushNotification(array($userVal->row()->push_notification_key['gcm_id']), $message, 'driver_loc', 'ANDROID', $options, 'USER');
										}
									}
								}
								if ($userVal->row()->push_type == 'IOS') {
									if (isset($userVal->row()->push_notification_key['ios_token'])) {
										if ($userVal->row()->push_notification_key['ios_token'] != '') {
											$this->sendPushNotification(array($userVal->row()->push_notification_key['ios_token']), $message, 'driver_loc', 'IOS', $options, 'USER');
										}
									}
								}
							}
						}
						$returnArr['status'] = '1';
						$returnArr['response'] = $this->format_string("Updated Successfully", "updated_successfully");
					}else{
						$returnArr['response'] = $this->format_string("Invalid Ride", "invalid_ride");
					}
				}else{
					$returnArr['response'] = $this->format_string("Driver not found", "driver_not_found");
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
     * This Function returns the driver dashboard
     *
     * */
    public function driver_dashboard() {
        $responseArr['status'] = '0';
        $responseArr['response'] = '';
        try {
            $driver_id = $this->input->post('driver_id');
            $driver_lat = $this->input->post('driver_lat');
            $driver_lon = $this->input->post('driver_lon');
            if ($driver_id != '' && $driver_lat != '' && $driver_lon != '') {
                $checkDriver = $this->app_model->get_selected_fields(DRIVERS, array('_id' => MongoID($driver_id)), array('driver_name', 'image', 'avg_review', 'email', 'dail_code', 'mobile_number', 'vehicle_number', 'vehicle_model', 'driver_commission', 'loc', 'category','availability','mode','driver_location','multi_car_status','additional_category'));
                if ($checkDriver->num_rows() == 1) {
                    $driver_image = USER_PROFILE_IMAGE_DEFAULT;
                    if (isset($checkDriver->row()->image)) {
                        if ($checkDriver->row()->image != '') {
                            $driver_image = USER_PROFILE_IMAGE . $checkDriver->row()->image;
                        }
                    }
                    $driver_review = 0;
                    if (isset($checkDriver->row()->avg_review)) {
                        $driver_review = $checkDriver->row()->avg_review;
                    }
                    $availability = 'No';
                    if (isset($checkDriver->row()->availability)) {
                        $availability = $checkDriver->row()->availability;
                    }														
					$availability_string = 'Yes';
					$ride_status_string = 'No';
					if ($checkDriver->row()->mode == 'Available') {
                        $availability_string = 'Yes';
                    } else if ($checkDriver->row()->mode == 'Booked') {
                        $checkPending = $this->app_model->get_uncompleted_trips($driver_id, array('ride_id', 'ride_status', 'pay_status'));
                        if ($checkPending->num_rows() > 0) {
							if ($checkPending->row()->ride_status == 'Onride') {
								$ride_status_string = 'Yes';
							}
                            $availability_string = 'No';
                        } else {
                            $availability_string = 'Yes';
                        }
                    }
                    /* $driver_lat = $checkDriver->row()->loc['lat'];
                    $driver_lon = $checkDriver->row()->loc['lon']; */
                    $vehicleInfo = $this->driver_model->get_selected_fields(MODELS, array('_id' => MongoID($checkDriver->row()->vehicle_model)), array('_id', 'name', 'brand_name'));
                    $vehicle_model = '';
                    if ($vehicleInfo->num_rows() > 0) {
                        $vehicle_model = $vehicleInfo->row()->name;
                    }
                    $categoryInfo = $this->driver_model->get_selected_fields(CATEGORY, array('_id' => MongoID($checkDriver->row()->category)), array('_id', 'name', 'brand_name', 'icon_car_image','name_languages'));
                    $driver_category = '';
					$category_icon = base_url().ICON_MAP_CAR_IMAGE;
                    if ($categoryInfo->num_rows() > 0) {
                        $driver_category = $categoryInfo->row()->name;
						if(isset($categoryInfo->row()->name_languages)){
							$langKey = $this->data['sms_lang_code'];
							$arrVal = $categoryInfo->row()->name_languages;
							if(array_key_exists($langKey,$arrVal)){
								if($categoryInfo->row()->name_languages[$langKey]!=""){
									$driver_category = $categoryInfo->row()->name_languages[$langKey];
								}
							}
						}
						if(isset($categoryInfo->row()->icon_car_image)){
							$category_icon = base_url() . ICON_IMAGE . $categoryInfo->row()->icon_car_image;
						}
                        
                    }

                    $last_trip = array();
                    $checkTrip = $this->app_model->get_all_details(RIDES, array('driver.id' => $driver_id, 'ride_status' => "Completed", "pay_status" => "Paid"), array("_id" => "DESC"));
                    if ($checkTrip->num_rows() > 0) {
                        $last_trip = array("ride_time" => get_time_to_string("h:i A", MongoEPOCH($checkTrip->row()->booking_information['drop_date'])),
                            "ride_date" => get_time_to_string("jS M, Y", MongoEPOCH($checkTrip->row()->booking_information['drop_date'])),
                            "earnings" => (string) number_format($checkTrip->row()->driver_revenue, 2),
                            "currency" => (string) $this->data['dcurrencyCode']
                        );
                    }

                    $today_earnings = array();
                    $checkRide = $this->app_model->get_today_rides($driver_id);
                    if (!empty($checkRide['result'])) {
                        $online_hours = $checkRide['result'][0]['freeTime'] + $checkRide['result'][0]['tripTime'] + $checkRide['result'][0]['waitTime'];
                        $online_hours_txt = '0 hours';
                        if ($online_hours > 0) {
                            if ($online_hours >= 60) {
								$online_hours_in_hrs = ($online_hours / 60);
                                $online_hours_txt = round($online_hours_in_hrs,2) . ' hours';
                            } else {
                                $online_hours_txt = $online_hours . ' minutes';
                            }
                        }
                        $mins = $this->format_string('min', 'min_short');
			            $mins_short = $this->format_string('mins', 'mins_short');
                        if($checkRide['result'][0]['ridetime'] >1){
                                $min_unit = $mins_short;
                        }else{
                                $min_unit = $mins;
                        }
                        $trip = $this->format_string('trip', 'trip_singular');
			            $trips = $this->format_string('trips', 'trip_plural');
                        if($checkRide['result'][0]['totalTrips'] >1) {
                           $trip_unit = $trips;
                        } else {
                           $trip_unit = $trip;
                        }
                       
                        $today_earnings = array("online_hours" => (string) $checkRide['result'][0]['ridetime'].' '.$min_unit,
                            "trips" => (string) $checkRide['result'][0]['totalTrips'],
                            "earnings" => (string) number_format($checkRide['result'][0]['driverAmount'], 2),
                            "currency" => (string) $this->data['dcurrencyCode'],
                            "trip_unit" => (string) $trip_unit
                        );
                    }
                    $today_tips = array();
                    $todayTips = $this->app_model->get_today_tips($driver_id);
                    if (!empty($todayTips['result'])) {
                        $today_tips = array("trips" => (string) $todayTips['result'][0]['totalTrips'],
                            "tips" => (string) number_format($todayTips['result'][0]['tipsAmount'], 2),
                            "currency" => (string) $this->data['dcurrencyCode']
                        );
                    }
					
					if(empty($last_trip)){
						$last_trip = json_decode("{}");
					}
					if(empty($today_earnings)){
						$today_earnings = json_decode("{}");
					}
					if(empty($today_tips)){
						$today_tips = json_decode("{}");
					}
					
					$go_online_status = "0";
					$go_online_string = $this->format_string('Currently you can\'t able to service in this location','driver_cannot_service_in_this_location');
					$driver_location = $checkDriver->row()->driver_location;
					$service_location = $this->app_model->find_location(floatval($driver_lon),floatval($driver_lat));
					if (!empty($service_location['result'])) {
						$service_location_arr = array_column($service_location['result'],'_id');
						$sArr = array();
						foreach($service_location_arr as $locs){
							$sArr[] = (string)$locs;
						}
						if(in_array($driver_location,$sArr)){
							$go_online_status = "1";
							$go_online_string = "";
							/* $service_location_id = (string)$service_location['result'][0]['_id'];
							if($service_location_id==$driver_location){
								$go_online_status = "1";
								$go_online_string = "";
							} */
						}
					}
					
					$multi_car_status = '-1';
					$additional_category = $driver_category;
					$additionalCats = 0;
					if($this->data['multiCategoryOption'] == 'ON'){
						$multi_car_status = '0';
						if(isset($checkDriver->row()->multi_car_status) && $checkDriver->row()->multi_car_status == 'ON'){
						    $multi_car_status = '1';
							if(isset($checkDriver->row()->additional_category) && count($checkDriver->row()->additional_category) > 0){
								$categoryList = $this->app_model->get_selected_fields(CATEGORY, array('status' => 'Active'), array('_id', 'name','name_languages'));
								foreach($categoryList->result() as $adCats){
									$adCatsId = (string)$adCats->_id;
									if(in_array($adCatsId,$checkDriver->row()->additional_category)){
										$category_name = $adCats->name;
										if(isset($adCats->name_languages[$langCode]) && $adCats->name_languages[$langCode] != ''){
											$category_name = $adCats->name_languages[$langCode];
										}
										$additional_category = $additional_category.'/'.$category_name;
										$additionalCats++;
									}
								}
							}
						}						
					}
					$multi_car_string = 'Accept rides for '.$additional_category;
					if($additionalCats == 0){
						$multi_car_status = '-1';
					}
					
                    $driver_dashboard = array("currency" => (string) $this->data['dcurrencyCode'],
						'driver_id' => (string) $checkDriver->row()->_id,
                        'availability' => (string)$availability,
						'driver_status' => (string) $checkDriver->row()->availability,
                        'driver_name' => (string) $checkDriver->row()->driver_name,
                        'driver_email' => (string) $checkDriver->row()->email,
                        'driver_image' => (string) base_url() . $driver_image,
                        'driver_review' => (string) floatval($driver_review),
                        'driver_lat' => (string) floatval($driver_lat),
                        'driver_lon' => (string) floatval($driver_lon),
                        'phone_number' => (string) $checkDriver->row()->dail_code . $checkDriver->row()->mobile_number,
                        'driver_category' => (string) $driver_category,
                        'vehicle_number' => (string) $checkDriver->row()->vehicle_number,
                        'vehicle_model' => (string) $vehicle_model,
                        'last_trip' => $last_trip,
                        'today_earnings' => $today_earnings,
                        'today_tips' => $today_tips,
						'availability_string' => (string)$availability_string,
						'ride_status_string' => (string)$ride_status_string,
						'category_icon' => (string)$category_icon,
						'go_online_status' => (string)$go_online_status,
						'go_online_string' => (string)$go_online_string,
						'multi_car_status' => (string)$multi_car_status,
						'multi_car_string' => $multi_car_string
                    );

                    $responseArr['status'] = '1';
                    $responseArr['response'] = $driver_dashboard;
                } else {
                    $responseArr['response'] = $this->format_string('Authentication Failed','authentication_failed');
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
    * This function changes the driver password
    *
    * */
    public function change_password() {
        $responseArr['status'] = '0';
        $responseArr['response'] = '';
        try {
            $driver_id = $this->input->post('driver_id');
            $password = $this->input->post('password');
            $new_password = (string) $this->input->post('new_password');

            if ($driver_id != '' && $password != '' && $new_password != '') {
                $checkDriver = $this->app_model->get_selected_fields(DRIVERS, array('_id' => MongoID($driver_id)), array('password'));
                if ($checkDriver->num_rows() == 1) {
                    if (strlen($new_password) >= 6) {
                        if ($checkDriver->row()->password == md5($password)) {
                            $condition = array('_id' => MongoID($driver_id));
                            $dataArr = array('password' => md5($new_password));
                            $this->app_model->update_details(DRIVERS, $dataArr, $condition);
                            $responseArr['status'] = '1';
                            $responseArr['response'] = $this->format_string('Password changed successfully.','password_changed');
                        } else {
                            $responseArr['response'] = $this->format_string('Your current password is not matching.','password_not_matching');
                        }
                    } else {
                        $responseArr['response'] = $this->format_string('Password should be at least 6 characters.','password_should_be_6_characters');
                    }
                } else {
                    $responseArr['response'] = $this->format_string('Authentication Failed','authentication_failed');
                }
            } else {
                $responseArr['response'] = $this->format_string("Some Parameters are missing.","some_parameters_missing");
            }
        } catch (MongoException $ex) {
            $responseArr['response'] = $this->format_string('Error in connection','error_in_connection');
        }
        $json_encode = json_encode($responseArr, JSON_PRETTY_PRINT);
        echo $this->cleanString($json_encode);
    }
	
	 /**
    *
    * This function forgot driver password request
    *
    * */
	public function forgot_password() {
        $responseArr['status'] = '0';
        $responseArr['response'] = '';
        try {
            $email = $this->input->post('email');
            if ($email != '') {
                $checkDriver = $this->app_model->get_selected_fields(DRIVERS, array('email' => $email), array('password', 'driver_name', 'email'));
                if ($checkDriver->num_rows() == 1) {
                    $new_pwd = $this->get_rand_str('6') . time();
                    $newdata = array('reset_id' => $new_pwd);
                    $condition = array('email' => $email);
                    $this->app_model->update_details(DRIVERS, $newdata, $condition);
                    $this->send_driver_pwd($new_pwd, $checkDriver);
                    $responseArr['status'] = '1';
                    $responseArr['response'] = $this->format_string('Password reset link has been sent to your email address.','password_reset_link_sent');
                } else {
                    $responseArr['response'] = $this->format_string('Email id does not match our records','record_not_have');
                }
            } else {
                $responseArr['response'] = $this->format_string("Some Parameters are missing.","some_parameters_missing");
            }
        } catch (MongoException $ex) {
            $responseArr['response'] = $this->format_string('Error in connection','error_in_connection');
        }
        $json_encode = json_encode($responseArr, JSON_PRETTY_PRINT);
        echo $this->cleanString($json_encode);
	}
	
	 /**
    *
    * This function send the new password to driver email
    *
    * */
    public function send_driver_pwd($pwd = '', $query) {
        $newsid = '10';
        $reset_url = base_url() . 'driver/reset-password-form/' . $pwd;
        $user_name = $query->row()->driver_name;
		$template_values = $this->app_model->get_email_template($newsid,$this->data['langCode']);
        $subject = 'From: ' . $this->config->item('email_title') . ' - ' . $template_values['subject'];
        $drivernewstemplateArr = array('email_title' => $this->config->item('email_title'), 'mail_emailTitle' => $this->config->item('email_title'), 'mail_logo' => $this->config->item('logo_image'), 'mail_footerContent' => $this->config->item('footer_content'), 'mail_metaTitle' => $this->config->item('meta_title'), 'mail_contactMail' => $this->config->item('site_contact_mail'));
        extract($drivernewstemplateArr);
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
        $email_send_to_common = $this->app_model->common_email_send($email_values);
    }
	
	
	/*
	* This function change the driver's multi car status
	*/
	public function change_driver_multicar_status(){
		$returnArr['status'] = '0';
        $returnArr['response'] = '';	
		try {
			$driver_id = $this->input->post('driver_id');
			$mode = $this->input->post('mode');
			if($driver_id != '' && $mode != ''){
				if(in_array(strtolower($mode),array('on','off'))){
					$checkDriver = $this->app_model->get_selected_fields(DRIVERS,array('_id'=>MongoID($driver_id)),array('multi_car_status','additional_category','category'));
					if($checkDriver->num_rows() > 0){
						$this->app_model->update_details(DRIVERS,array('multi_car_status'=>strtoupper($mode)),array('_id'=>MongoID($driver_id)));
						$category = '';
						if(isset($checkDriver->row()->category)) $category = $checkDriver->row()->category;
						$getCat = $this->app_model->get_selected_fields(CATEGORY, array('_id' => MongoID($category)), array('_id', 'name','name_languages'));
						
						$langCode = $this->data['langCode'];
						$category_name = $getCat->row()->name;
						if(isset($getCat->row()->name_languages[$langCode]) && $getCat->row()->name_languages[$langCode] != ''){
							$category_name = $getCat->row()->name_languages[$langCode];
						}
						$additional_category = $category_name;
						if($mode == 'on'){
							if(isset($checkDriver->row()->additional_category) && count($checkDriver->row()->additional_category) > 0){
								$categoryList = $this->app_model->get_selected_fields(CATEGORY, array('status' => 'Active'), array('_id', 'name','name_languages'));
								foreach($categoryList->result() as $adCats){
									$adCatsId = (string)$adCats->_id;
									if(in_array($adCatsId,$checkDriver->row()->additional_category)){
										$category_name = $adCats->name;
										if(isset($adCats->name_languages[$langCode]) && $adCats->name_languages[$langCode] != ''){
											$category_name = $adCats->name_languages[$langCode];
										}
										$additional_category = $additional_category.'/'.$category_name;
									}
								}
							}
						}
						$multi_car_string = 'Accept rides for '.$additional_category;
						$returnArr['status'] = '1';
						$returnArr['response'] = $multi_car_string;
					} else {
						$returnArr['response'] = $this->format_string('No records found');
					}
				}else{
					$returnArr['response'] = $this->format_string('Invalid request');
				}
			}else{
				$returnArr['response'] = $this->format_string('Some parameters are missing');
			}
		}catch (MongoException $ex) {
            $returnArr['response'] = $this->format_string('Error in connection');
		}
	    $json_encode = json_encode($returnArr, JSON_PRETTY_PRINT);
        echo $this->cleanString($json_encode);
	}
    public function ride_list_view() {
        $returnArr['status'] = '0';
        $returnArr['response'] = '';
        try {
            $driver_id = (string) $this->input->post('driver_id');
      
            if ($driver_id != '') {
                $driverVal = $this->app_model->get_selected_fields(DRIVERS, array('_id' => MongoID($driver_id)), array('_id'));
                if ($driverVal->num_rows() > 0) {
                    $checkRide = $this->app_model->get_all_details(RIDES,array('driver.id'=>$driver_id));
                    $rideArr = array();
                    foreach($checkRide->result() as $data) {
                        $ride_id=$data->ride_id;
                        $fareArr = array();
                        $summaryArr = array();
						$min_short = $this->format_string('min', 'min_short');
						$mins_short = $this->format_string('mins', 'mins_short');
                        if (isset($data->summary)) {
                            if (is_array($data->summary)) {
                                foreach ($data->summary as $key => $values) {
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
                        if (isset($data->total)) {
                            if (is_array($data->total)) {
                                $total_bill = 0.00;
                                $tips_amount = 0.00;
                                $coupon_discount = 0.00;
                                $grand_bill = 0.00;
                                $total_paid = 0.00;
                                $wallet_usage = 0.00;
                                if (isset($data->total['total_fare'])) {
                                    $total_bill = $data->total['total_fare'];
                                }

                                if (isset($data->total['tips_amount'])) {
                                    $tips_amount = $data->total['tips_amount'];
                                }

                                $tips_status = '0';
                                if ($tips_amount > 0) {
                                    $tips_status = '1';
                                }


                                if (isset($data->total['coupon_discount'])) {
                                    $coupon_discount = $data->total['coupon_discount'];
                                }
                                if (isset($data->total['grand_fare'])) {
                                    $grand_bill = $data->total['grand_fare'];
                                }
                                if (isset($data->total['paid_amount'])) {
                                    $total_paid = $data->total['paid_amount'];
                                }
                                if (isset($data->total['wallet_usage'])) {
                                    $wallet_usage = $data->total['wallet_usage'];
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
                        if (isset($data->pay_status)) {
                            $pay_status = $data->pay_status;
							if($pay_status == 'Paid'){
								$disp_pay_status = $this->format_string("Paid", "paid");
							}else {
								$pay_status == 'Pending';
								$disp_pay_status = $this->format_string("Pending", "pending");
							}
                        }


                        $doAction = 0;
                        if ($data->ride_status == 'Booked' || $data->ride_status == 'Confirmed' || $data->ride_status == 'Cancelled' || $data->ride_status == 'Arrived') {
                            $doAction = 1;
                            if ($data->ride_status == 'Cancelled') {
								$doAction = 0;
                            }
                        }
                        $iscontinue = 'NO';
                        if ($data->ride_status == 'Confirmed' || $data->ride_status == 'Arrived' || $data->ride_status == 'Onride') {
                            if ($data->ride_status == 'Confirmed') {
                                $iscontinue = 'arrived';
                            }
                            if ($data->ride_status == 'Arrived') {
                                $iscontinue = 'begin';
                            }
                            if ($data->ride_status == 'Onride') {
                                $iscontinue = 'end';
                            }
                        }
						
                        $user_profile = array();
                        if ($iscontinue != 'NO' || $iscontinue == 'NO') {
                            $userVal = $this->driver_model->get_selected_fields(USERS, array('_id' => MongoID($data->user['id'])), array('_id', 'user_name', 'email', 'image', 'avg_review', 'phone_number', 'country_code', 'push_type', 'push_notification_key'));
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
								if($data->booking_information['drop']['location']!=''){
									$drop_location = 1;
									$drop_loc = $data->booking_information['drop']['location'];
									$drop_lat = $data->booking_information['drop']['latlong']['lat'];
									$drop_lon = $data->booking_information['drop']['latlong']['lon'];
								}
								
								$ride_date = get_time_to_string("M d, Y",MongoEPOCH($data->booking_information['est_pickup_date']));
								$pickup_date = '';
								$drop_date = '';
								if ($data->ride_status == 'Booked' || $data->ride_status == 'Confirmed' || $data->ride_status == 'Cancelled' || $data->ride_status == 'Arrived' || $data->ride_status == 'Onride') {
									$pickup_date = get_time_to_string("h:i A", MongoEPOCH($data->booking_information['est_pickup_date']));
								} else {
									$pickup_date = get_time_to_string("h:i A", MongoEPOCH($data->history['begin_ride']));
									$drop_date = get_time_to_string("h:i A", MongoEPOCH($data->history['end_ride']));
								}
								
                                $user_profile = array('user_name' => $userVal->row()->user_name,
                                    'user_id' => (string)$userVal->row()->_id,
                                    'user_email' => $userVal->row()->email,
                                    'phone_number' => (string) $userVal->row()->country_code . $userVal->row()->phone_number,
                                    'user_image' => base_url() . $user_image,
                                    'user_review' => floatval($user_review),
                                    'ride_id' => $ride_id,
                                    'pickup_location' => $data->booking_information['pickup']['location'],
                                    'pickup_lat' => $data->booking_information['pickup']['latlong']['lat'],
                                    'pickup_lon' => $data->booking_information['pickup']['latlong']['lon'],
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
                        if ($data->booking_information['drop']['location']!='') {
                            $dropArr = $data->booking_information['drop'];
                        }
						if (empty($dropArr)) {
							$dropArr = json_decode("{}");
						}
						$distance_unit = $this->data['d_distance_unit'];
						if(isset($data->fare_breakup['distance_unit'])){
							$distance_unit = $data->fare_breakup['distance_unit'];
						}						
						$disp_distance_unit = $distance_unit;
						if($distance_unit == 'km') $disp_distance_unit = $this->format_string('km', 'km');
						if($distance_unit == 'mi') $disp_distance_unit = $this->format_string('mi', 'mi');
						
						if(!empty($summaryArr)){
							$summaryArr['currency'] = (string)$data->currency;
							$summaryArr['ride_fare'] = (string) floatval(round($grand_bill, 2));
						}
						
						$invoice_path = 'trip_invoice/'.$ride_id.'_large.jpg'; 
						if(file_exists($invoice_path)) {
							$invoice_src = base_url().$invoice_path;
						} else {
							$invoice_src = '';
						}
						
						$drop_date_time = '';
						if(isset($data->booking_information['drop_date'])){
							$drop_date_time = get_time_to_string("h:i A", MongoEPOCH($data->booking_information['drop_date'])).' '.$this->format_string('on','on').' '. get_time_to_string("jS M, Y", MongoEPOCH($data->booking_information['drop_date']));
						}
                        $disp_status = '';
                        if ($data->ride_status == 'Booked') {
                            $disp_status = $this->format_string("Booked", "booked");
                        } else if ($data->ride_status == 'Confirmed') {
                            $disp_status = $this->format_string("Accepted", "accepted");
                        } else if ($data->ride_status == 'Cancelled') {
                            $disp_status = $this->format_string("Cancelled", "cancelled");
                        } else if ($data->ride_status == 'Completed') {
                            $disp_status = $this->format_string("Completed", "completed");
                        } else if ($data->ride_status == 'Finished') {
                            $disp_status = $this->format_string("Awaiting Payment", "await_payment");
                        } else if ($data->ride_status == 'Arrived' || $data->ride_status == 'Onride') {
                            $disp_status = $this->format_string("On Ride", "on_ride");
                        }
						
						$trip_type = "Normal";
						if(isset($data->pool_ride)){
							if($data->pool_ride=="Yes"){
								$trip_type = "Share";
							}
						}
						$driver_revenue=0;
						if(isset($data->driver_revenue)){
                            $driver_revenue=$data->driver_revenue+$data->total['tips_amount'];
						}
						$payment_method='';
                        if(isset($data->pay_summary['type']) && $data->pay_summary['type']!='') {
                         $payment_method=$data->pay_summary['type'];
                         $payment_method=str_replace("_"," & ",$payment_method);
                        }
                        $vehicle_no='';
                        if(isset($data->driver['vehicle_no']) && $data->driver['vehicle_no']!='') {
                            $vehicle_no=$data->driver['vehicle_no'];
                        }
                        $passenger_Arr = array();
                        if (isset($data->total['grand_fare'])) {
                            if ($data->total['grand_fare'] >= 0) {
                                $passenger_Arr[] = array("title"=>(string)$this->format_string("Passenger Paid", "passenger_paid"),
                                                         "value"=>(string)number_format($data->total['grand_fare'],2,'.',''),
                                                          'positive'=>'0'
                                                         );
                            }
						}
                       
                        if(isset($data->pay_summary['type']) && $data->pay_summary['type']!='') {
                            if (strpos($data->pay_summary['type'], '_') !== false) {
                               $payment_Arr=@explode('_',$data->pay_summary['type']);  
                               if($payment_Arr[1]=='Cash') {
                                
                                    if (isset($data->total['paid_amount'])) {
                                        if ($data->total['paid_amount'] > 0) {
                                            $passenger_Arr[] = array("title"=>(string)$this->format_string("Cash Received", "cash_received"),
                                                                                "value"=>(string)number_format($data->total['paid_amount'],2,'.',''),
                                                                                'positive'=>'0'
                                                                                );
                                        }
                                    }
                              }
                               if($payment_Arr[0]=='W' || $payment_Arr[0]=='Wallet') {
                                    if (isset($data->total['wallet_usage'])) {
                                        if ($data->total['wallet_usage'] > 0) {
                                            $passenger_Arr[] = array("title"=>(string)$this->format_string("Wallet used", "fare_summary_wallet_used"),
                                                                                "value"=>(string)number_format($data->total['wallet_usage'],2,'.',''),
                                                                                'positive'=>'0'
                                                                                );
                                        }
                                    }
                               }
                               
                              if($payment_Arr[1]=='Gateway') {
                                    if (isset($data->total['paid_amount'])) {
                                        if ($data->total['paid_amount'] > 0) {
                                            $passenger_Arr[] = array("title"=>(string)$this->format_string("Card Payment", "card_payment"),
                                                                                "value"=>(string)number_format($data->total['paid_amount'],2,'.',''),
                                                                                'positive'=>'0'
                                                                                );
                                        }
                                    }
                              }
                             
                            } else {
                                  if($data->pay_summary['type']=='Wallet') {
                                    if (isset($data->total['wallet_usage'])) {
                                        if ($data->total['wallet_usage'] > 0) {
                                            $passenger_Arr[] = array("title"=>(string)$this->format_string("Wallet used", "fare_summary_wallet_used"),
                                                                                "value"=>(string)number_format($data->total['wallet_usage'],2,'.',''),
                                                                                'positive'=>'0'
                                                                                );
                                        }
                                    }
                                }
                               if($data->pay_summary['type']=='Cash') {
                                
                                    if (isset($data->total['paid_amount'])) {
                                        if ($data->total['paid_amount'] > 0) {
                                            $passenger_Arr[] = array("title"=>(string)$this->format_string("Cash Received", "cash_received"),
                                                                                "value"=>(string)number_format($data->total['paid_amount'],2,'.',''),
                                                                                'positive'=>'0'
                                                                                );
                                        }
                                    }
                              }
                              if($data->pay_summary['type']=='Gateway') {
                                    if (isset($data->total['paid_amount'])) {
                                        if ($data->total['paid_amount'] > 0) {
                                            $passenger_Arr[] = array("title"=>(string)$this->format_string("Card Payment", "card_payment"),
                                                                                "value"=>(string)number_format($data->total['paid_amount'],2,'.',''),
                                                                                'positive'=>'0'
                                                                                );
                                        }
                                    }
                              }
                            }
                            if (isset($data->total['coupon_discount'])) {
                                if ($data->total['coupon_discount'] > 0) {
                                    $passenger_Arr[] = array("title"=>(string)$this->format_string("Discount amount", "fare_summary_coupon_discount"),
                                                                        "value"=>(string)number_format($data->total['coupon_discount'],2,'.',''),
                                                                        'positive'=>'2'
                                                                        );
                                }
                            }
                        
                        }
                        $driver_earning = array();
                        if (isset($data->total['grand_fare'])) {
                            if ($data->total['grand_fare'] >= 0) {
                                $driver_earning[] = array("title"=>(string)$this->format_string("Trip Fare", "trip_fare"),
                                                         "value"=>(string)number_format($data->total['grand_fare'],2,'.',''),
                                                          'positive'=>'0'
                                                         );
                            }
						}
                        /* if (isset($data->total['service_tax'])) {
                            if ($data->total['service_tax'] >= 0) {
                                $driver_earning[] = array("title"=>(string)$this->format_string("Service Tax", "service_tax",FALSE),
                                                                    "value"=>(string)number_format($data->total['service_tax'],2,'.',''),
                                                                    'positive'=>'2'
                                                                    );
                            }
						} */
                        if (isset($data->amount_commission)) {
                            if ($data->amount_commission >= 0) {
                                $driver_earning[] = array("title"=>$this->config->item('email_title')." ".$this->format_string("Fee", "service_fee",FALSE),
                                                                    "value"=>(string)number_format($data->amount_commission,2,'.',''),
                                                                    'positive'=>'2'
                                                                    );
                            }
						}
                        if (isset($data->total['coupon_discount'])) {
							if ($data->total['coupon_discount'] > 0) {
								$driver_earning[] = array("title"=>(string)$this->format_string("Discount amount", "fare_summary_coupon_discount"),
																	"value"=>(string)number_format($data->total['coupon_discount'],2,'.',''),
                                                                    'positive'=>'1'
																	);
							}
						}
                        
                        if (isset($data->total['tips_amount'])) {
							if ($data->total['tips_amount'] > 0) {
								$driver_earning[] = array("title"=>(string)$this->format_string("Tips amount", "fare_summary_tips"),
																	"value"=>(string)number_format($data->total['tips_amount'],2,'.',''),
                                                                    'positive'=>'1'
																	);
							}
						}
                        if(isset($data->driver_revenue)){
                            $driver_revenue=$data->driver_revenue+$data->total['tips_amount'];
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
						$payArr = $this->driver_model->get_all_details(PAYMENT_GATEWAY,array("status"=>"Enable"));
						if($payArr->num_rows()==0){
							$req_payment = 'Disable';
						}
                        $rideArr[]=array('receive_cash' => $receive_cash,
                                                    'req_payment' => $req_payment, 
                                                    'currency' => $data->currency,
                                                    'cab_type' => $data->booking_information['service_type'],
                                                    'trip_type' => $trip_type,
                                                    'ride_id' => $data->ride_id,
                                                    'ride_status' => $data->ride_status,
                                                    'disp_status' => $disp_status,
                                                    'do_cancel_action' => (string) $doAction,
                                                    'pay_status' => $pay_status,
                                                    'disp_pay_status' => $disp_pay_status,
                                                    'pickup' => $data->booking_information['pickup'],
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
                                                    'user_id' => (string)$data->user['id'],
                                                    'driver_revenue' => $driver_revenue,
                                                    'payment_method'=>$payment_method,
                                                    'vehicle_no'=>$vehicle_no ,
                                                    'user_profile' => $user_profile
												);
                    }

                    if (empty($rideArr)) {
                        $rideArr = json_decode("{}");
                    }
                    $total_rides = intval($checkRide->num_rows());
                    $returnArr['status'] = '1';
                    $returnArr['response'] = array('total_rides' => (string) $total_rides, 'rides' => $rideArr);
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
	
	
	
	
}

/* End of file driver.php */
/* Location: ./application/controllers/v8/api/driver.php */