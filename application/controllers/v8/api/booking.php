<?php if (!defined('BASEPATH')) exit('No direct script access allowed');

/**
* 
* Bookings related functions
* @author Casperon
*
**/

class Booking extends MY_Controller {

    function __construct(){
		parent::__construct();
		$this->load->helper(array('cookie','date','form','email','ride_helper','pool_helper','distcalc_helper','statistics_helper'));
		$this->load->library(array('encrypt','form_validation'));
		$returnArr=array();
		
		/* Authentication Begin */
        $headers = $this->input->request_headers();
		header('Content-type:application/json;charset=utf-8');
		if (array_key_exists("Authkey", $headers)) $auth_key = $headers['Authkey']; else $auth_key = "";
		if(stripos($auth_key,APP_NAME) === false) {
			$cf_fun= $this->router->fetch_method();
			$apply_function = array();
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
							} else {
                                if($this->Apptype=='ios') $dType= "IOS"; else $dType= "ANDROID";
                                $push_data = array('push_notification.key' => $this->Token, 'push_notification.type' => $dType);
                                $this->app_model->update_details(DRIVERS, $push_data, array('_id' => MongoID($this->Driverid)));
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
		} catch (MongoException $ex) {
			
		}
		/*	Authentication End	*/
    }
	
	
	/**
	*
	* This function used for driver to update driver current location
	*
	**/
    public function update_driver_location() {
		$returnArr['status'] = '0';
        $returnArr['response'] = '';
        try {
            $driver_id = $this->input->post('driver_id');
            $latitude = $this->input->post('latitude');
            $longitude = $this->input->post('longitude');

            $c_ride_id = $this->input->post('ride_id');

            if (is_array($this->input->post())) {
                $chkValues = count(array_filter($this->input->post()));
            } else {
                $chkValues = 0;
            }

            if ($chkValues >= 3) {
                $checkDriver = $this->app_model->get_selected_fields(DRIVERS, array('_id' => MongoID($driver_id)), array('_id', 'mode', 'availability','verify_status','driver_location','ride_type','duty_ride'));
                if ($checkDriver->num_rows() == 1) {
                    $geo_data = array('loc' => array('lon' => floatval($longitude), 'lat' => floatval($latitude)),'last_active_time'=>MongoDATE(time()));
                    $this->app_model->update_details(DRIVERS, $geo_data, array('_id' => MongoID($driver_id)));
					
					update_driver_avail_rides($driver_id);

                    $ride_id = '';
                    $verify_status = 'No';
					$errorMsg = $this->format_string("You do not have a verified account, Contact us for more information", "account_not_verified", TRUE);					
					if(isset($checkDriver->row()->verify_status)){
						if ($checkDriver->row()->verify_status == 'Yes') {
							$verify_status = 'Yes';
							$errorMsg = '';
						}
					}
                    $Location_Arr=array('driver_id'=> MongoID($driver_id),
                                       'lat'=>floatval($latitude),
                                       'lon'=>floatval($longitude),
                                       'updated_time'=>MongoDATE(time()),
                                       );
                    $this->app_model->simple_insert(ONLINE_DRIVERS,$Location_Arr);
					$ride_type = "Normal";	#(Normal / Share)
					if(isset($checkDriver->row()->ride_type)){
						if($checkDriver->row()->ride_type!=""){
							$ride_type = $checkDriver->row()->ride_type;
						}
					}
					$duty_ride = "";
					if(isset($checkDriver->row()->duty_ride)){
						if($checkDriver->row()->duty_ride!=""){
							$duty_ride = $checkDriver->row()->duty_ride;
						}
					}
                    if ($checkDriver->row()->mode == 'Available' && $ride_type == "Normal") {
                        $availability_string = 'Available';
						$checkPending = $this->app_model->get_driver_active_trips($driver_id, $duty_ride,$ride_type);
                        if ($checkPending->num_rows() > 0) {
							$availability_string = 'Unavailable';
							$ride_id = $checkPending->row()->ride_id;
                            $avail_data = array('ride_type'=>'Normal','duty_ride'=>(string)$ride_id, 'last_active_time' => MongoDATE(time()));
                            $this->app_model->update_details(DRIVERS, $avail_data, array('_id' => MongoID($driver_id)));			
						}
                    } else if ($checkDriver->row()->mode == 'Booked' || ($checkDriver->row()->mode == 'Available' && $ride_type == "Share")) {
                        $checkPending = $this->app_model->get_driver_active_trips($driver_id, $duty_ride,$ride_type);
                        if ($checkPending->num_rows() > 0) {
                            $availability_string = 'Unavailable';
                            $ride_id = $checkPending->row()->ride_id;
							$errorMsg = $this->format_string("You have a pending trip / transaction. Please tap to view. Without resolving this you will not get ride requests", "pending_trip_cant_ride_request", TRUE);
                        } else {
                            $availability_string = 'Available';
                            $avail_data = array('ride_type'=>'','duty_ride'=>'','mode'=>'Available','availability' => 'Yes', 'last_active_time' => MongoDATE(time()));
                            $this->app_model->update_details(DRIVERS, $avail_data, array('_id' => MongoID($driver_id)));
                        }
                    }else{
						$availability_string = 'Available';
					}
					
					/* if ($checkDriver->row()->mode == 'Available' && $verify_status == 'No' && $ride_id == '') {
						$verify_status = 'No';
						$errorMsg = $this->format_string('Currently you can\'t able to service in this location','driver_cannot_service_in_this_location');
						$driver_location = $checkDriver->row()->driver_location;
						$service_location = $this->app_model->find_location(floatval($longitude),floatval($latitude));
						if (!empty($service_location['result'])) {
							$service_location_arr = array_column($service_location['result'],'_id');
							$sArr = array();
							foreach($service_location_arr as $locs){
								$sArr[] = (string)$locs;
							}
							if (in_array($driver_location,$sArr)){
								$verify_status = 'Yes';
								$errorMsg = "";
								$service_location_id = (string)$service_location['result'][0]['_id'];
								if($service_location_id==$driver_location){
									$verify_status = 'Yes';
									$errorMsg = "";
								}
							}
						}
					} */

                    if ($c_ride_id != '' && $c_ride_id != NULL) {
						$checkRide = $this->app_model->get_selected_fields(RIDES, array('ride_id' => $c_ride_id), array( 'ride_status'));
						if($checkRide->num_rows()>0){
							if($checkRide->row()->ride_status!="Completed"){
								$checkInfo = $this->app_model->get_all_details(TRACKING, array('ride_id' => $c_ride_id));
							
								$latlng = $latitude . ',' . $longitude;
								/* $gmap = file_get_contents("https://maps.googleapis.com/maps/api/geocode/json?latlng=" . $latlng . "&sensor=false".$this->data['google_maps_api_key']);
								$mapValues = json_decode($gmap)->results;
								if(!empty($mapValues)){
									$formatted_address = $mapValues[0]->formatted_address;
								} */
								$formatted_address = "";
									$cuurentLoc = array('timestamp' => MongoDATE(time()),
										'locality' => (string) $formatted_address,
										'location' => array('lat' => floatval($latitude), 'lon' => floatval($longitude))
									);
									
									if ($checkInfo->num_rows() > 0) {
										$this->app_model->simple_push(TRACKING, array('ride_id' => (string) $c_ride_id), array('steps' => $cuurentLoc));
									} else {
										$this->app_model->simple_insert(TRACKING, array('ride_id' => (string) $c_ride_id));
										$this->app_model->simple_push(TRACKING, array('ride_id' => (string) $c_ride_id), array('steps' => $cuurentLoc));
									}
							}
						}
                    }
					
                    $returnArr['status'] = '1';
                    $returnArr['response'] = array('message' => $this->format_string('Geo Location Updated', 'geo_location_updated'), 
												'availability' => $availability_string,
												'ride_id' => $ride_id,
												'verify_status' => $verify_status,
												'errorMsg' => $errorMsg
											);
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
	* This Function return the drivers information for user map view
	*
	**/
    public function get_drivers() {
        $limit = 1000;
        $returnArr['status'] = '0';
        $returnArr['response'] = '';
        try {
            $user_id = trim($this->input->post('user_id'));
            $latitude = $this->input->post('lat');
            $longitude = $this->input->post('lon');
            $category = $this->input->post('category');
			$gender_pref = trim(strtolower($this->input->post('gender_pref'))); #(male/female/)
			
			$genderPref = FALSE;
			if(strtolower($gender_pref)=="male" || strtolower($gender_pref)=="female"){
				$genderPref = $gender_pref;
			}

            if (is_array($this->input->post())) {
                $chkValues = count(array_filter($this->input->post()));
            } else {
                $chkValues = 0;
            }
           
            if ($chkValues >= 3) {
                $checkUser = $this->app_model->get_selected_fields(USERS, array('_id' => MongoID($user_id)), array('email','xmpp_connected'));
                $geo_data_user = array('loc' => array('lon' => floatval($longitude), 'lat' => floatval($latitude)),'last_active_time'=>MongoDATE(time()));
                $this->user_model->update_details(USERS, $geo_data_user, array('_id' => MongoID($user_id)));
                if ($checkUser->num_rows() == 1) {
                    $xmpp_connected = 'No';
                     if(isset($checkUser->row()->xmpp_connected)){
                        $xmpp_connected = $checkUser->row()->xmpp_connected;
                     }
                     if($xmpp_connected != 'Yes'){
                        /************  connect users with xmpp ************/
                        $fields = array(
                                    'username' => (string) $user_id,
                                    'password' => md5((string) $user_id)
                                 );
                        $url = $this->data['soc_url'] . 'create-user.php';
                        $this->load->library('curl');
                        $output = $this->curl->simple_post($url, $fields);
                        
                        $this->user_model->update_details(USERS,array('xmpp_connected' => 'Yes'),array('_id' => MongoID($user_id)));
                     }
                   
                    $coordinates = array(floatval($longitude), floatval($latitude));
					
                    $location = $this->app_model->find_location(floatval($longitude), floatval($latitude),"Yes");
                    							
                    if (!empty($location['result'])) {
                        
						$location_id = $location['result'][0]['_id'];
                        $condition = array('status' => 'Active');						
						/*
							Make the final category list
						*/
						$final_cat_list = $location['result'][0]['avail_category'];
						if (array_key_exists('avail_category', $location['result'][0]) && array_key_exists('fare', $location['result'][0])) {
                            if (!empty($location['result'][0]['avail_category']) && !empty($location['result'][0]['fare'])) {
								$cat_avail = $location['result'][0]['avail_category'];
								$cat_fare = array_keys($location['result'][0]['fare']);
								$final_cat_list = array_intersect($cat_avail,$cat_fare);
                            }
                        }
						
                        $categoryResult = $this->app_model->get_available_category(CATEGORY, $final_cat_list);
                       
                        if($category == '' && isset($categoryResult->row()->_id)){
                            $category = (string)$categoryResult->row()->_id;
                        }
                        
                        $availCategory = array();
                        $categoryArr = array();
                        $rateCard = array();
                        $vehicle_type = '';
						$finalcategoryArr = array(); // result array
                        if ($categoryResult->num_rows() > 0) {
							$has_pool_service = 0;
							$pooling = 0;
							if($this->data['share_pooling_status'] != ''){
								$pooling = $this->data['share_pooling_status'];
							}
                           
							$hasPoolDr=FALSE;
							if($pooling==1){
								if(array_key_exists("share_pooling",$location['result'][0])){
									$share_pooling = $location['result'][0]['share_pooling'];
                                  
									if($share_pooling=="Enable"){
										$pool_categories = $location['result'][0]['pool_categories'];
                                        
										if(!empty($pool_categories)){
											$pool_map_search_radius = $location['result'][0]['pool_map_search_radius'];
											$pool_fare = $location['result'][0]['pool_fare'];
											$has_pool_service = 1;
											
											
											$categoryID = array();
											foreach($pool_categories as $pool_cat){
												if($pool_cat!="") $categoryID[] = MongoID($pool_cat);
											}
											
											$category_drivers = $this->app_model->get_nearest_pool_driver($coordinates, $categoryID, 1,'',"","",$location_id,$genderPref);
											// print_R($category_drivers['result']);die;
                                            if (empty($category_drivers['result'])) {
												$category_drivers = $this->app_model->get_nearest_driver($coordinates, $categoryID, 1,"","","",$location_id,'',$genderPref);
											}
								            
											$mins = $this->format_string('min', 'min_short');
											$mins_short = $this->format_string('mins', 'mins_short');
											$no_cabs = $this->format_string('no cabs', 'no_cabs');
											if (!empty($category_drivers['result'])) {
												$hasPoolDr=TRUE;
												$distance = $category_drivers['result'][0]['distance'];
												$eta_time = $this->app_model->calculateETA($distance);
												if($eta_time>1){
													$eta_unit = $mins_short;
												}else{
													$eta_unit = $mins;
												}
												$eta = $this->app_model->calculateETA($distance) . ' ' . $mins;
											} else {
												$eta_time = "";
												$eta_unit = "";
												$eta = $no_cabs;
											}
											
											if($hasPoolDr==TRUE){
												$pool_icon_normal=ICON_IMAGE_DEFAULT;
												if ($this->config->item('pool_icon_normal')!=""){
													$pool_icon_normal=ICON_IMAGE.$this->config->item('pool_icon_normal');
												}
												$pool_icon_active=ICON_IMAGE_DEFAULT;
												if ($this->config->item('pool_icon_active')!=""){
													$pool_icon_active=ICON_IMAGE.$this->config->item('pool_icon_active');
												}
												$pool_map_car_image=ICON_MAP_CAR_IMAGE;
												if ($this->config->item('pool_map_car_image')!=""){
													$pool_map_car_image=ICON_IMAGE.$this->config->item('pool_map_car_image');
												}
												$finalcategoryArr[] = array('id' => (string) POOL_ID,
																						'name' => (string)$this->config->item('pooling_name'),
																						'eta' => (string) $eta,
																						'eta_time' => (string) $eta_time,
																						'eta_unit' => (string) $eta_unit,
																						'icon_normal' => (string) base_url() . $pool_icon_normal,
																						'icon_active' => (string) base_url() . $pool_icon_active,
																						'icon_car_image' => (string) base_url() . $pool_map_car_image,
																						'is_pool_type' => (string) 1,
																						'has_pool_option' => (string) 1
																					);
											}
										}										
									}
								}
							}
                            
                            
                            foreach ($categoryResult->result() as $cat) {
									

                                $cat_name = $cat->name;
								if(isset($cat->name_languages)){
									$langKey = $this->data['sms_lang_code'];
									$arrVal = $cat->name_languages;
									if(array_key_exists($langKey,$arrVal)){
										if($cat->name_languages[$langKey]!=""){
											$cat_name = $cat->name_languages[$langKey];
										}
									}
								}
                                
                                $availCategory[(string) $cat->_id] = $cat_name;
                                
                                $category_drivers = $this->app_model->get_nearest_driver($coordinates, (string) $cat->_id, $limit,"","","",$location_id,'',$genderPref);
								 
								#$mins = $this->format_string('mins', 'mins');
								$mins = $this->format_string('min', 'min_short');
								$mins_short = $this->format_string('mins', 'mins_short');
								$no_cabs = $this->format_string('no cabs', 'no_cabs');
                                if (!empty($category_drivers['result'])) {
                                    $distance = $category_drivers['result'][0]['distance'];
									$eta_time = $this->app_model->calculateETA($distance);
									if($eta_time>1){
										$eta_unit = $mins_short;
									}else{
										$eta_unit = $mins;
									}
                                    $eta = $this->app_model->calculateETA($distance) . ' ' . $mins;
                                } else {
									$eta_time = "";
									$eta_unit = "";
                                    $eta = $no_cabs;
                                }
                                $avail_vehicles = array();
                                if ((string) $cat->_id == $category) {
									if(isset($cat->vehicle_type)) $avail_vehicles = $cat->vehicle_type;
                                }
                                
                                $icon_normal = base_url() . ICON_IMAGE_DEFAULT;
                                $icon_active = base_url() . ICON_IMAGE_ACTIVE;
								$icon_car_image = base_url().ICON_MAP_CAR_IMAGE;
                                if (isset($cat->icon_normal)) {
                                    if ($cat->icon_normal != '') {
                                        $icon_normal = base_url() . ICON_IMAGE . $cat->icon_normal;
                                    }
                                }
                                if (isset($cat->icon_active)) {
                                    if ($cat->icon_active != '') {
                                        $icon_active = base_url() . ICON_IMAGE . $cat->icon_active;
                                    }
                                }
								if (isset($cat->icon_car_image)) {
                                    if ($cat->icon_car_image != '') {
                                        $icon_car_image = base_url() . ICON_IMAGE . $cat->icon_car_image;
                                    }
                                }
								
								
								$is_pool_type = 0;
								$has_pool_option = 0;
								
								if($has_pool_service == 1){
									if(in_array((string) $cat->_id,$pool_categories) && $hasPoolDr==TRUE){
										$has_pool_option = 1;
									}
								}
									$categoryArr[] = array('id' => (string) $cat->_id,
											'name' => $cat_name,
											'eta' => (string) $eta,
											'eta_time' => (string) $eta_time,
											'eta_unit' => (string) $eta_unit,
											'icon_normal' => (string) $icon_normal,
											'icon_active' => (string) $icon_active,
											'icon_car_image' => (string) $icon_car_image,
											'is_pool_type' => (string) $is_pool_type,
											'has_pool_option' => (string) $has_pool_option
									);
                            }
							foreach($final_cat_list as $val){ // loop
							   $key = array_search($val, array_column($categoryArr, 'id'));
							   $finalcategoryArr[$categoryArr[$key]['id']] =$categoryArr[$key];
							}
                            
							$finalcategoryArr=array_values($finalcategoryArr);
                            $vehicleResult = $this->app_model->get_available_vehicles($avail_vehicles);
                            
                            if ($vehicleResult->num_rows() > 0) {
                                $vehicleArr = (array) $vehicleResult->result_array();
                                $vehicle_type = implode(',', array_map(function($n) {
                                            return $n['vehicle_type'];
                                        }, $vehicleArr));
                            }
                            if (isset($availCategory[(string) $category])) {
								$note_heading = $this->format_string('Note', 'note_heading');
								$note_peak_time = $this->format_string('Peak time charges may apply. Service tax extra.', 'note_peak_time',FALSE);
                                
                                
                                $rateCard['category'] = $availCategory[(string) $category];
                                
                                
                                $rateCard['vehicletypes'] = $vehicle_type;
                                $rateCard['note'] = $note_heading.' : '.$note_peak_time;
                                $fare = array();
								
								$distance_unit = $this->data['d_distance_unit'];
								if(isset($location['result'][0]['distance_unit'])){
									if($location['result'][0]['distance_unit'] != ''){
										$distance_unit = $location['result'][0]['distance_unit'];
									} 
								}
								$disp_distance_unit = $distance_unit;
								if($distance_unit == 'km') $disp_distance_unit = $this->format_string('km', 'km');
								if($distance_unit == 'mi') $disp_distance_unit = $this->format_string('mi', 'mi');
								
								$min = $this->format_string('min', 'min');								
								$first = $this->format_string('First', 'first');
								$after = $this->format_string('After', 'after');
								$ride_time_rate_post = $this->format_string('Ride time rate post ', 'ride_time_rate_post');
								
                                if (isset($location['result'][0]['fare'])) {
                                    if (array_key_exists($category, $location['result'][0]['fare'])) {
										if($location['result'][0]['fare'][$category]['min_time']>1){
											$min_time_unit = $mins_short;
										}else{
											$min_time_unit = $mins;
										}
                                        $fare['min_fare'] = array('amount' => (string) $location['result'][0]['fare'][$category]['min_fare'],
                                            'text' => $first . ' ' . $location['result'][0]['fare'][$category]['min_km'] .' '. $disp_distance_unit);
                                        $fare['after_fare'] = array('amount' => (string) $location['result'][0]['fare'][$category]['per_km']  . '/' . $disp_distance_unit,
                                            'text' => $after . ' ' . $location['result'][0]['fare'][$category]['min_km'] . ' ' . $disp_distance_unit);
                                        $fare['other_fare'] = array('amount' => (string) $location['result'][0]['fare'][$category]['per_minute'] . '/' . $mins,
                                            'text' => $ride_time_rate_post . ' ' . $location['result'][0]['fare'][$category]['min_time'] . ' ' . $min_time_unit);
                                    }
                                }
                                $rateCard['farebreakup'] = $fare;
                            }
                        }
						
						if($category == POOL_ID){
							$pool_service_text = $this->format_string("Share your ride and save on fares. Book up to 2 seats every ride.", 'share_your_ride_and_get_reduce_fare');
							$categoryID = array();
							foreach($location['result'][0]['pool_categories'] as $pool_cat){
								$categoryID[] = MongoID($pool_cat);
							}
						}else{
							$pool_service_text = "";
							$categoryID = $category;
						}
						$driversArr = array();
						$driverList = $this->app_model->get_nearest_driver($coordinates, $categoryID, $limit,"","","",$location_id,'',$genderPref);
                        if (!empty($driverList['result'])) {
                            foreach ($driverList['result'] as $driver) {
                                $lat = $driver['loc']['lat'];
                                $lon = $driver['loc']['lon'];
                                $driversArr[] = array('lat' => $lat,
                                    'lon' => $lon
                                );
                            }
                        }

                        if (empty($rateCard)) {
                            $rateCard = json_decode("{}");
                        }

                        if (empty($finalcategoryArr)) {
                            $finalcategoryArr = json_decode("{}");
                        } 
						if (empty($rateCard)) {
                            $rateCard = json_decode("{}");
                        }
						
						$location_id = $location['result'][0]["_id"];
						$surge = check_surge($location_id,$category);
						if (empty($driversArr)) {
							$surge = "";
						}
						if (empty($driversArr)) {
                            $driversArr = json_decode("{}");
                        }
						
						$curWalletVal = $this->app_model->get_selected_fields(WALLET, array('user_id' => MongoID($user_id)), array('total'));
						$wallet_amount = 0;
						if ($curWalletVal->num_rows() == 1) {
							if (isset($curWalletVal->row()->total)) {
								$wallet_amount = $curWalletVal->row()->total;
							}
						}
						
						$gender_perf_status = '0';
						if($this->config->item('gender_perf_status') == 'ON') $gender_perf_status = '1';
                        
                        $returnArr['status'] = '1';						
                        $returnArr['response'] = array('currency' => (string) $this->data['dcurrencyCode'], 
                                                    'wallet_amount' => (string)number_format($wallet_amount,2), 
                                                    'has_pool_service' => (string)$has_pool_service, 
                                                    'category' => $finalcategoryArr, 
                                                    'drivers' => $driversArr, 
                                                    'ratecard' => $rateCard,
                                                    'pool_service_text' => (string)$pool_service_text,
                                                    'surge' => (string)$surge,
                                                    'selected_category' => (string) $category,
													'selected_gender' => (string) $gender_pref,
													'gender_perf_status' => (string) $gender_perf_status
                                                );
                    } else {
                        $returnArr['response'] = $this->format_string('Sorry ! We do not provide services in your city yet.', 'service_unavailable_in_your_city');
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
	* This Function return the eta information for a ride
	*
	**/
    public function get_estimate() {
		get_estimate();
    }
	
	/**
	*
	* This Function used for booking a ride
	*
	**/
    public function make_booking() {
		book_a_ride($this->input->post());
    }
	
	/**
	*
	*	This function will send the request to nearby drivers while send retry request
	*
	**/
	public function retry_ride_request() {
        $returnArr['status'] = '0';
        $returnArr['response'] = '';
        $returnArr['acceptance'] = 'No';
		
		try {
			request_retry($this->input->post()); exit;
		}catch (MongoException $ex) {
            $returnArr['response'] = $this->format_string("Error in connection", "error_in_connection");
        }
		
        $json_encode = json_encode($returnArr, JSON_PRETTY_PRINT);
        echo $this->cleanString($json_encode);
    }
	
	/**
	*
	* This function used for driver will accepting the users requesting for ride
	*
	**/
    public function accept_ride_request() {
		accepting_ride($this->input->post());
    }
	
	/**
	*
	* This function used for driver to update the pickup location reached state
	*
	**/
    public function location_arrived() {
		pickup_location_arrived($this->input->post());
    }
		
	/**
	*
	* This function used for driver to update the pickup location reached state
	*
	**/
    public function begin_ride() {
		begin_the_trip($this->input->post());
    }
	
	/**
	*
	* This function used for end the trip
	*
	**/
    public function end_ride() {
		finish_the_trip($this->input->post());
    }
	
	
	/**
	*
	* This Function returns the tracking trip information to users
	*
	**/
    public function get_track_information_user() {		
        $ride_id = $this->input->post('ride_id');
        if ($ride_id == '') {
            $ride_id = $this->input->get('ride_id');
        }
        $returnArr['status'] = '0';
        if ($ride_id != '') {
            $checkRide = $this->app_model->get_all_details(RIDES, array('ride_id' => $ride_id));
            if ($checkRide->num_rows() == 1) {
                $driver_id = $checkRide->row()->driver['id'];
                if ($driver_id != '') {
					$map_locations = array();
					
                    $lat_lon = @explode(',', $checkRide->row()->driver['lat_lon']);
                    $driver_lat = $lat_lon[0];
                    $driver_lon = $lat_lon[1];

                    /** *********   find estimated duration   ********** */
                    $pickupLocArr = $checkRide->row()->booking_information['pickup']['latlong'];

                    $from = $driver_lat . ',' . $driver_lon;
                    $to = $pickupLocArr['lat'] . ',' . $pickupLocArr['lon'];
					
					$mindurationtext = 'N/A';

                    $gmap = file_get_contents('https://maps.googleapis.com/maps/api/directions/json?origin=' . $from . '&destination=' . $to . '&alternatives=true&sensor=false&mode=driving'.$this->data['google_maps_api_key']);
                    $map_values = json_decode($gmap);
                    $routes = $map_values->routes;
					if(!empty($routes)){
						usort($routes, create_function('$a,$b', 'return intval($a->legs[0]->distance->value) - intval($b->legs[0]->distance->value);'));
						$mindurationtext = $routes[0]->legs[0]->duration->text;
					}


                    $checkDriver = $this->app_model->get_selected_fields(DRIVERS, array('_id' => MongoID($driver_id)), array('_id', 'driver_name', 'image', 'avg_review', 'email', 'dail_code', 'mobile_number', 'vehicle_number', 'vehicle_model'));
                    /* Preparing driver information to share with user -- Start */
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
                    $vehicleInfo = $this->app_model->get_selected_fields(MODELS, array('_id' => MongoID($checkDriver->row()->vehicle_model)), array('_id', 'name', 'brand_name'));
                    $vehicle_model = '';
                    if ($vehicleInfo->num_rows() > 0) {
                        $vehicle_model = $vehicleInfo->row()->name;
                    }
					
					
					$pickup_arr = array();
					if($checkRide->row()->booking_information['pickup']['location']!=''){
						$pickup_arr = $checkRide->row()->booking_information['pickup'];
						$map_locations[] = array_reverse($checkRide->row()->booking_information['pickup']['latlong']);
					}
					if (empty($pickup_arr)) {
						$pickup_arr = json_decode("{}");
					}
					
					$drop_arr = array();
					if($checkRide->row()->booking_information['drop']['location']!=''){
						$drop_arr = $checkRide->row()->booking_information['drop'];
						$map_locations[] = array_reverse($checkRide->row()->booking_information['drop']['latlong']);
					}
					if (empty($drop_arr)) {
						$drop_arr = json_decode("{}");
					}
					
					$cab_type = "";
					$map_car = ICON_MAP_CAR_IMAGE;
					$cab_image = CATEGORY_IMAGE_DEFAULT;
					$cab_type = $checkRide->row()->booking_information['service_type'];
					$cab_type_id =  $checkRide->row()->booking_information['service_id'];
					
					if($cab_type_id==POOL_ID){
						$cab_type = (string)$this->config->item('pooling_name');
						if ($this->config->item('pool_cat_image')!=""){
							$cab_image=CATEGORY_IMAGE.$this->config->item('pool_cat_image');
						}
						if ($this->config->item('pool_map_car_image')!=""){
							$map_car=ICON_IMAGE.$this->config->item('pool_map_car_image');
						}
					}else{
						$categoryInfo = $this->app_model->get_selected_fields(CATEGORY, array('_id' => MongoID($cab_type_id)), array('_id', 'name', 'image','name_languages','icon_car_image'));
						if ($categoryInfo->num_rows() > 0) {
							$cab_type = $categoryInfo->row()->name;
							if(isset($categoryInfo->row()->image)){
								$cab_image = CATEGORY_IMAGE.$categoryInfo->row()->image;
							}
							if(isset($categoryInfo->row()->icon_car_image) && $categoryInfo->row()->icon_car_image!=""){
								$map_car = ICON_IMAGE.$categoryInfo->row()->icon_car_image;
							}
							if(isset($categoryInfo->row()->name_languages)){
								$langKey = $this->data['sms_lang_code'];
								$arrVal = $categoryInfo->row()->name_languages;
								if(array_key_exists($langKey,$arrVal)){
									if($categoryInfo->row()->name_languages[$langKey]!=""){
										$cab_type = $categoryInfo->row()->name_languages[$langKey];
									}
								}
							}
						}
					}


                    $driver_profile = array('driver_id' => (string) $checkDriver->row()->_id,
                        'driver_name' => (string) $checkDriver->row()->driver_name,
                        'driver_email' => (string) $checkDriver->row()->email,
                        'driver_image' => (string) base_url() . $driver_image,
                        'driver_review' => (string) floatval($driver_review),
                        'driver_lat' => (string) floatval($driver_lat),
                        'driver_lon' => (string) floatval($driver_lon),
                        'cab_type' => (string) $cab_type,
                        'cab_image' => (string) base_url().$cab_image,
                        'map_car' => (string) base_url().$map_car,
                        'rider_lat' => (string) floatval($checkRide->row()->booking_information['pickup']['latlong']['lat']),
                        'rider_lon' => (string) floatval($checkRide->row()->booking_information['pickup']['latlong']['lon']),
                        'min_pickup_duration' => $mindurationtext,
                        'ride_id' => (string) $ride_id,
                        'phone_number' => (string) $checkDriver->row()->dail_code . $checkDriver->row()->mobile_number,
                        'vehicle_number' => (string) $checkDriver->row()->vehicle_number,
                        'vehicle_model' => (string) $vehicle_model,
                        'ride_status' => (string) $checkRide->row()->ride_status,
                        'pickup' => $pickup_arr,
                        'drop' => $drop_arr
                    );
                    /* Preparing driver information to share with user -- End */
                } else {
                    $driver_profile = array();
                }

                /* get driver current location and path */
                $tracking = array();
                if (empty($driver_profile)) {
                    $driver_profile = json_decode("{}");
                }
                if (empty($tracking)) {
                    $tracking = json_decode("{}");
                }
				
				$ride_type = "Normal";	#(Normal / Share)
				if(isset($checkRide->row()->pool_ride)){
					if($checkRide->row()->pool_ride=="Yes"){
						$ride_type = "Share";
					}
				}
				
				
				
                $returnArr['status'] = '1';
                $returnArr['response'] = array('ride_id' => (string) $ride_id,
															'ride_type' => $ride_type,
															'driver_profile' => $driver_profile,
															'tracking_details' => $tracking,
															'map_locations' => $map_locations
														);
				if($ride_type == "Share"){
					$has_co_rider = "0";
					$returnArr['response']['co_rider_name'] = "";
					$checkAvailRide = $this->app_model->get_user_active_trips($checkRide->row()->pool_id);
					
					if(isset($checkRide->row()->pooling_with)){
						if(!empty($checkRide->row()->pooling_with) && $checkAvailRide->num_rows()>1){
							$has_co_rider = "1";
							$returnArr['response']['co_rider_name'] = $checkRide->row()->pooling_with["name"];
						}
					}
					$returnArr['response']['has_co_rider'] = $has_co_rider;
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
	* This Function returns the tracking trip information to drivers
	*
	**/
    public function get_track_information_driver() {
        $returnArr['status'] = '0';
        $returnArr['response'] = '';
        try {
            $driver_id = $this->input->post('driver_id');
            $ride_id = $this->input->post('ride_id');
            if ($driver_id != '') {
				get_trip_information($driver_id); exit;
            } else {
                $returnArr['response'] = $this->format_string("Some Parameters are missing","some_parameters_missing");
            }
        } catch (MongoException $ex) {
            $returnArr['response'] = $this->format_string('Error in connection','error_in_connection');
        }
        $json_encode = json_encode($returnArr, JSON_PRETTY_PRINT);
        echo $this->cleanString($json_encode);
    }
	
	/**
	*
	* This Function return the ride cancellation reson for users/driver 
	*
	**/
    public function get_cancellation_reasons() {
        $returnArr['status'] = '0';
        $returnArr['response'] = '';
        try {
            $user_type = $this->input->post('user_type');	#(user/driver)
            $id = $this->input->post('id');	
            $ride_id = $this->input->post('ride_id');

            if ($user_type!="" && $id!="" && $ride_id!="") {
				if($user_type == "user"){
					$auth_collection = USERS;
					$reason_list = "user";
					$ride_auth = "user.id";
					$selectArr = array('user_name','image','messaging_status');
				}else if($user_type == "driver"){
					$auth_collection = DRIVERS;
					$reason_list = "driver";
					$ride_auth = "driver.id";
					$selectArr = array('driver_name','image','messaging_status');
				}
				
                $checkUser = $this->app_model->get_selected_fields($auth_collection, array('_id' => MongoID($id)), $selectArr);
                if ($checkUser->num_rows() == 1) {
                    $checkRide = $this->app_model->get_selected_fields(RIDES, array('ride_id' => $ride_id,$ride_auth => (string)$id), array('ride_id', 'ride_status','cancelled'));
                    if ($checkRide->num_rows() == 1) {
						if ($checkRide->row()->ride_status != 'Cancelled') {
							$reasonVal = $this->app_model->get_selected_fields(CANCELLATION_REASON, array('status' => 'Active', 'type' => (string)$reason_list), array('reason','name_languages'));
							if ($reasonVal->num_rows() > 0) {
								$reasonArr = array();
								foreach ($reasonVal->result() as $row) {
									$reason = $row->reason;
									if(isset($row->name_languages)){
										$langKey = $this->data['sms_lang_code'];
										$arrVal = $row->name_languages;
										if(array_key_exists($langKey,$arrVal)){
											if($row->name_languages[$langKey]!=""){
												$reason = $row->name_languages[$langKey];
											}
										}
									}
									$reasonArr[] = array('id' => (string) $row->_id,
										'reason' => (string) $reason
									);
								}
								if (empty($reasonArr)) {
									$reasonArr = json_decode("{}");
								}
								$returnArr['status'] = '1';
								$returnArr['response'] = array('reason' => $reasonArr);
							} else {
								$returnArr['response'] = $this->format_string('No reasons available to cancelling ride', 'no_reasons_available_to_cancel_ride');
							}
						}else{
							$returnArr['response'] = $this->format_string('Already this ride has been cancelled', 'already_ride_cancelled');
						}
					}else{
						$returnArr['response'] = $this->format_string("This ride is unavailable", "ride_unavailable");
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
	* This Function used to cancelling a ride by a user/driver
	*
	**/
    public function cancelling_ride() {
        $returnArr['status'] = '0';
        $returnArr['response'] = '';
        try {
			$user_type = $this->input->post('user_type');	#(user/driver)
            $id = $this->input->post('id');
            $ride_id = $this->input->post('ride_id');
            $reason = $this->input->post('reason');

            if ($user_type!="" && $id!="" && $ride_id!="" && $reason!="") {
				if($user_type == "user"){
					$auth_collection = USERS;
					$reason_list = "user";
					$ride_auth = "user.id";
					$selectArr = array('user_name','image','messaging_status');
				}else if($user_type == "driver"){
					$auth_collection = DRIVERS;
					$reason_list = "driver";
					$ride_auth = "driver.id";
					$selectArr = array('driver_name','image','messaging_status','duty_ride');
				}
				$checkUser = $this->app_model->get_selected_fields($auth_collection, array('_id' => MongoID($id)), $selectArr);
                if ($checkUser->num_rows() == 1) {
                    $checkRide = $this->app_model->get_all_details(RIDES, array('ride_id' => $ride_id,$ride_auth => (string)$id));
                    if ($checkRide->num_rows() == 1) {
						if($user_type == "user"){
							$user_id = $id;
							$doAction = 0;
							if ($checkRide->row()->ride_status == 'Booked' || $checkRide->row()->ride_status == 'Confirmed' || $checkRide->row()->ride_status == 'Arrived') {
								$doAction = 1;
							}
							if ($doAction == 1) {
								$reasonVal = $this->app_model->get_selected_fields(CANCELLATION_REASON, array('_id' => MongoID($reason)), array('reason'));
								if ($reasonVal->num_rows() > 0) {
									$reason_id = (string) $reasonVal->row()->_id;
									$reason_text = (string) $reasonVal->row()->reason;

									$isPrimary = 'No';
									if ($checkRide->row()->ride_status != 'Cancelled') {
										$rideDetails = array('ride_status' => 'Cancelled',
											'cancelled' => array('primary' => array('by' => 'User',
													'id' => $user_id,
													'reason' => $reason_id,
													'text' => $reason_text
												)
											),
											'history.cancelled_time' => MongoDATE(time())
										);
										$isPrimary = 'Yes';
									} else if ($checkRide->row()->ride_status == 'Cancelled') {
										$rideDetails = array('cancelled.secondary' => array('by' => 'User',
												'id' => $user_id,
												'reason' => $reason_id,
												'text' => $reason_text
											),
											'history.secondary_cancelled_time' => MongoDATE(time())
										);
									}
									$ride_type = "";	#(Normal / Share)

									if ($isPrimary == 'Yes') {
										/* Update the coupon usage details */
										if ($checkRide->row()->coupon_used == 'Yes') {
											$usage = array("user_id" => (string) $checkUser->row()->_id, "ride_id" => $ride_id);
											$promo_code = (string) $checkRide->row()->coupon['code'];
											$this->app_model->simple_pull(PROMOCODE, array('promo_code' => $promo_code), array('usage' => $usage));
										}
										if ($checkRide->row()->driver['id'] != '') {
											/* Update the driver status to Available */
											$driver_id = $checkRide->row()->driver['id'];
											$checkDrRide = $this->app_model->get_driver_active_trips($driver_id,'','');
											if($checkDrRide->num_rows() ==0) {
												$this->app_model->update_details(DRIVERS, array('mode' => 'Available'), array('_id' => MongoID($driver_id)));
											}
										}

										/* Update the no of cancellation under this reason  */
										$this->app_model->update_user_rides_count('cancelled_rides', $user_id);
										if ($checkRide->row()->driver['id'] != '') {
											$this->app_model->update_driver_rides_count('cancelled_rides', $driver_id);
										}

										/* Update Stats Starts */
										$current_date = MongoDATE(strtotime(date("Y-m-d 00:00:00")));
										$field = array('ride_cancel.hour_' . date('H') => 1, 'ride_cancel.count' => 1);
										$this->app_model->update_stats(array('day_hour' => $current_date), $field, 1);
										/* Update Stats End */

										if ($checkRide->row()->driver['id'] != '') {
											$driver_id = $driver_id;
											$driverVal = $this->app_model->get_selected_fields(DRIVERS, array('_id' => MongoID($driver_id)), array('_id', 'push_notification','duty_ride'));

											if (isset($driverVal->row()->push_notification)) {
												if ($driverVal->row()->push_notification != '') {
													$message = $this->format_string("rider cancelled this ride","rider_cancelled_ride", '', 'driver', (string)$driverVal->row()->_id);													
													$options = array('ride_id' => (string) $ride_id, 'driver_id' => $driver_id);
													if (isset($driverVal->row()->push_notification['type'])) {
														if ($driverVal->row()->push_notification['type'] == 'ANDROID') {
															if (isset($driverVal->row()->push_notification['key'])) {
																if ($driverVal->row()->push_notification['key'] != '') {
																	$this->sendPushNotification($driverVal->row()->push_notification['key'], $message, 'ride_cancelled', 'ANDROID', $options, 'DRIVER');
																}
															}
														}
														if ($driverVal->row()->push_notification['type'] == 'IOS') {
															if (isset($driverVal->row()->push_notification['key'])) {
																if ($driverVal->row()->push_notification['key'] != '') {
																	$this->sendPushNotification($driverVal->row()->push_notification['key'], $message, 'ride_cancelled', 'IOS', $options, 'DRIVER');
																}
															}
														}
													}
												}
											}
																																	
											
											if(isset($checkRide->row()->pool_ride)){
												if($checkRide->row()->pool_ride=="Yes"){
													$ride_type = "Share";
												}
											}
											
											if($ride_type=="Share"){
												$curr_duty_ride = "";
												if(isset($driverVal->row()->duty_ride)){
													if($driverVal->row()->duty_ride!="") $curr_duty_ride = $driverVal->row()->duty_ride;
												}
												$checkAvailRide = $this->app_model->get_driver_active_trips($driver_id,$curr_duty_ride,"Share");
												$active_trips = 0;
												if($checkAvailRide->num_rows()>0){
													$active_trips = intval($checkAvailRide->num_rows());
												}
												if($active_trips>=1){
													$cUser = $checkRide->row()->user["id"];
													foreach($checkAvailRide->result() as $passanger){
														$ext_user_id = $passanger->user["id"];
														$curRide_id = $passanger->ride_id;
														if($ext_user_id!="" && $ext_user_id!=$cUser){
															$rSetPoolData = array('co_rider' => array(),'pooling_with' => array());
															$this->app_model->update_details(RIDES, $rSetPoolData, array('ride_id' => $curRide_id));
															$extUserVal = $this->app_model->get_selected_fields(USERS, array('_id' => MongoID($ext_user_id)), array('_id','push_type','push_notification_key'));
															if ($extUserVal->num_rows() > 0) {
																if (isset($extUserVal->row()->push_type)) {
																	if ($extUserVal->row()->push_type != '') {
																		$message = $this->format_string('Your trip information has been updated', 'trip_info_updated','','user',(string)$extUserVal->row()->_id);						
																		$optionsFExt = array('ride_id' => $curRide_id);
																		if ($extUserVal->row()->push_type == 'ANDROID') {
																			if (isset($extUserVal->row()->push_notification_key['gcm_id'])) {
																				if ($extUserVal->row()->push_notification_key['gcm_id'] != '') {
																					$this->sendPushNotification($extUserVal->row()->push_notification_key['gcm_id'], $message, 'track_reload', 'ANDROID', $optionsFExt, 'USER');
																				}
																			}
																		}
																		if ($extUserVal->row()->push_type == 'IOS') {
																			if (isset($extUserVal->row()->push_notification_key['ios_token'])) {
																				if ($extUserVal->row()->push_notification_key['ios_token'] != '') {
																					$this->sendPushNotification($extUserVal->row()->push_notification_key['ios_token'], $message, 'track_reload', 'IOS', $optionsFExt, 'USER');
																				}
																			}
																		}
																	}
																}
															}
														}
													}
												}
											}
											
											
										}
									}
									
									/* Update the ride information */			
									if($ride_type=="Share"){
										$rideDetails["co_rider"] = array();
										$rideDetails["pooling_with"] = array();
									}
									$this->app_model->update_details(RIDES, $rideDetails, array('ride_id' => $ride_id));

									$returnArr['status'] = '1';
									$returnArr['response'] = array('ride_id' => (string) $ride_id, 
																				'message' => $this->format_string('Ride Cancelled', 'ride_cancelled')
																			);
								} else {
									$returnArr['response'] = $this->format_string("You cannot do this action", "you_cannot_do_this_action");
								}
							} else {
								$returnArr['response'] = $this->format_string('You cannot do this action', 'you_cannot_do_this_action');
							}
						}else if($user_type == "driver"){
							$driver_id = $id;
							$doAction = 0;
							if ($checkRide->row()->ride_status == 'Confirmed' || $checkRide->row()->ride_status == 'Arrived') {
								$doAction = 1;
							}
							$doAction = 1;
							if ($doAction == 1) {
								$reasonVal = $this->app_model->get_selected_fields(CANCELLATION_REASON, array('_id' => MongoID($reason)), array('reason'));
								if ($reasonVal->num_rows() > 0) {
									$reason_id = (string) $reasonVal->row()->_id;
									$reason_text = (string) $reasonVal->row()->reason;

									$isPrimary = 'No';
									if ($checkRide->row()->ride_status != 'Cancelled') {
										$rideDetails = array('ride_status' => 'Cancelled',
											'cancelled' => array('primary' => array('by' => 'Driver',
													'id' => $driver_id,
													'reason' => $reason_id,
													'text' => $reason_text
												)
											),
											'history.cancelled_time' => MongoDATE(time())
										);
										$isPrimary = 'Yes';
									} else if ($checkRide->row()->ride_status == 'Cancelled') {
										$rideDetails = array('cancelled.secondary' => array('by' => 'Driver',
												'id' => $driver_id,
												'reason' => $reason_id,
												'text' => $reason_text
											),
											'history.secondary_cancelled_time' => MongoDATE(time())
										);
									}
									$ride_type = "";	#(Normal / Share)

									if ($isPrimary == 'Yes') {
										/* Update the coupon usage details */
										if ($checkRide->row()->coupon_used == 'Yes') {
											$usage = array("user_id" => (string) $checkRide->row()->user['id'], "ride_id" => $ride_id);
											$promo_code = (string) $checkRide->row()->coupon['code'];
											$this->app_model->simple_pull(PROMOCODE, array('promo_code' => $promo_code), array('usage' => $usage));
										}
										/* Update the driver status to Available */
										$driver_id = $checkRide->row()->driver['id'];
										
										$checkDrRide = $this->app_model->get_driver_active_trips($driver_id,'','');
										if($checkDrRide->num_rows() ==0) {
											$this->app_model->update_details(DRIVERS, array('mode' => 'Available'), array('_id' => MongoID($driver_id)));
										}

										/* Update the no of cancellation under this reason  */
										$this->app_model->update_user_rides_count('cancelled_rides', $checkRide->row()->user['id']);
										$this->app_model->update_driver_rides_count('cancelled_rides', $driver_id);


										/* Push Notification to driver regarding cancelling ride */
										$userVal = $this->app_model->get_selected_fields(USERS, array('_id' => MongoID($checkRide->row()->user['id'])), array('_id', 'user_name', 'email', 'image', 'avg_review', 'phone_number', 'country_code', 'push_type', 'push_notification_key'));
										if (isset($userVal->row()->push_type)) {
											if ($userVal->row()->push_type != '') {
												$message = $this->format_string('Your ride cancelled', 'your_ride_cancelled', '', 'user', (string)$userVal->row()->_id);
												
												$options = array('ride_id' => (string) $ride_id);
												if ($userVal->row()->push_type == 'ANDROID') {
													if (isset($userVal->row()->push_notification_key['gcm_id'])) {
														if ($userVal->row()->push_notification_key['gcm_id'] != '') {
															$this->sendPushNotification(array($userVal->row()->push_notification_key['gcm_id']), $message, 'ride_cancelled', 'ANDROID', $options, 'USER');
														}
													}
												}
												if ($userVal->row()->push_type == 'IOS') {
													if (isset($userVal->row()->push_notification_key['ios_token'])) {
														if ($userVal->row()->push_notification_key['ios_token'] != '') {
															$this->sendPushNotification(array($userVal->row()->push_notification_key['ios_token']), $message, 'ride_cancelled', 'IOS', $options, 'USER');
														}
													}
												}
											}
										}
										
										if(isset($checkRide->row()->pool_ride)){
											if($checkRide->row()->pool_ride=="Yes"){
												$ride_type = "Share";
											}
										}
										
										if($ride_type=="Share"){
											
												
											$curr_duty_ride = "";
											if(isset($checkUser->row()->duty_ride)){
												if($checkUser->row()->duty_ride!="") $curr_duty_ride = $checkUser->row()->duty_ride;
											}
											$checkAvailRide = $this->app_model->get_driver_active_trips($driver_id,$curr_duty_ride,"Share");
											$active_trips = 0;
											if($checkAvailRide->num_rows()>0){
												$active_trips = intval($checkAvailRide->num_rows());
											}
											if($active_trips>=1){
												$cUser = $checkRide->row()->user["id"];
												foreach($checkAvailRide->result() as $passanger){
													$ext_user_id = $passanger->user["id"];
													$curRide_id = $passanger->ride_id;
													if($ext_user_id!="" && $ext_user_id!=$cUser){
														$rSetPoolData = array('co_rider' => array(),'pooling_with' => array());
														$this->app_model->update_details(RIDES, $rSetPoolData, array('ride_id' => $curRide_id));
														$extUserVal = $this->app_model->get_selected_fields(USERS, array('_id' => MongoID($ext_user_id)), array('_id','push_type','push_notification_key'));
														if ($extUserVal->num_rows() > 0) {
															if (isset($extUserVal->row()->push_type)) {
																if ($extUserVal->row()->push_type != '') {
																	$message = $this->format_string('Your trip information has been updated', 'trip_info_updated','','user',(string)$extUserVal->row()->_id);						
																	$optionsFExt = array('ride_id' => $curRide_id);
																	if ($extUserVal->row()->push_type == 'ANDROID') {
																		if (isset($extUserVal->row()->push_notification_key['gcm_id'])) {
																			if ($extUserVal->row()->push_notification_key['gcm_id'] != '') {
																				$this->sendPushNotification($extUserVal->row()->push_notification_key['gcm_id'], $message, 'track_reload', 'ANDROID', $optionsFExt, 'USER');
																			}
																		}
																	}
																	if ($extUserVal->row()->push_type == 'IOS') {
																		if (isset($extUserVal->row()->push_notification_key['ios_token'])) {
																			if ($extUserVal->row()->push_notification_key['ios_token'] != '') {
																				$this->sendPushNotification($extUserVal->row()->push_notification_key['ios_token'], $message, 'track_reload', 'IOS', $optionsFExt, 'USER');
																			}
																		}
																	}
																}
															}
														}
													}
												}
											}
										}
										
										/* Update the ride information */	
										if($ride_type=="Share"){
											$rideDetails["co_rider"] = array();
											$rideDetails["pooling_with"] = array();
										}
										$this->app_model->update_details(RIDES, $rideDetails, array('ride_id' => $ride_id));
										
										
										/* Update Stats Starts */
										$current_date = MongoDATE(strtotime(date("Y-m-d 00:00:00")));
										$field = array('ride_cancel.hour_' . date('H') => 1, 'ride_cancel.count' => 1);
										$this->app_model->update_stats(array('day_hour' => $current_date), $field, 1);
										/* Update Stats End */
									}
									
									$returnArr['status'] = '1';
									$returnArr['response'] = array('ride_id' => (string) $ride_id, 'message' => $this->format_string('Ride Cancelled', 'ride_cancelled','','driver',(string)$driver_id));
								} else {
									$returnArr['status'] = '2';
									$returnArr['response'] = $this->format_string("You cannot do this action", "you_cannot_do_this_action");
								}
							} else {							
								$returnArr['status'] = '3';
								$returnArr['response'] = $this->format_string('Already this ride has been cancelled', 'already_ride_cancelled');
							}
						}else{
							$returnArr['response'] = $this->format_string("You cannot do this action", "you_cannot_do_this_action");
						}
					}else{
						$returnArr['response'] = $this->format_string("This ride is unavailable", "ride_unavailable");
					}
                } else {
                    $returnArr['response'] = $this->format_string("Invalid User", "invalid_user");
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
	* Get Trip List
	*
	* Accept User id and authenticate that with database values
	*
	* @param user_id as string	id of the user
	* @param type as string	filter value
	* @return json which having the list of trips
	*
	**/
	public function user_trip_list() {
        $returnArr['status'] = '0';
        $returnArr['response'] = '';
        try {
            $user_id = (string) $this->input->post('user_id');
            $type = (string) $this->input->post('type');
            if ($type == '') $type = 'all';

            if ($user_id != '') {
                $userVal = $this->app_model->get_selected_fields(USERS, array('_id' => MongoID($user_id)), array('city', 'avail_category'));
                if ($userVal->num_rows() > 0) {
                    $checkRide = $this->app_model->get_ride_list($user_id, $type, array('booking_information', 'ride_id', 'ride_status'));
                    $rideArr = array();
                    if ($checkRide->num_rows() > 0) {
                        foreach ($checkRide->result() as $ride) {
                            $group = 'all';
                            if ($ride->ride_status == 'Booked' || $ride->ride_status == 'Confirmed' || $ride->ride_status == 'Arrived') {
                                $group = 'upcoming';
                            } else if ($ride->ride_status == 'Completed' || $ride->ride_status == 'Finished') {
                                $group = 'completed';
                            }
                            $disp_status = '';
                            if ($ride->ride_status == 'Booked') {
                                $disp_status = $this->format_string("Booked", "booked");
                            } else if ($ride->ride_status == 'Confirmed') {
                                $disp_status = $this->format_string("Accepted", "accepted");
                            } else if ($ride->ride_status == 'Cancelled') {
                                $disp_status = $this->format_string("Cancelled", "cancelled");
                            } else if ($ride->ride_status == 'Completed') {
                                $disp_status = $this->format_string("Completed", "completed");
                            } else if ($ride->ride_status == 'Finished') {
                                $disp_status = $this->format_string("Awaiting Payment", "await_payment");
                            } else if ( $ride->ride_status == 'Onride') {
                                $disp_status = $this->format_string("On Ride", "on_ride");
                            } else if ($ride->ride_status == 'Arrived' ) {
                                $disp_status = $this->format_string("Arrived", "arrived");
                            }
							
							$ride_id = $ride->ride_id;
							
							$invoice_src = '';
							$invoice_path = 'trip_invoice/'.$ride_id.'_small.jpg'; 
							if(file_exists($invoice_path)) { $invoice_src = base_url().$invoice_path; }
							
							if ($ride->ride_status != 'Expired') {
								$rideArr[] = array('ride_id' => (string)$ride_id,
									'ride_time' => get_time_to_string("h:i A", MongoEPOCH($ride->booking_information['booking_date'])),
									'ride_date' => get_time_to_string("jS M, Y", MongoEPOCH($ride->booking_information['booking_date'])),
									'pickup' => $ride->booking_information['pickup']['location'],
									'ride_status' => (string) $ride->ride_status,
									'display_status' => (string) $disp_status,
									'group' => $group,
									'datetime' => get_time_to_string("d-m-Y", MongoEPOCH($ride->booking_information['booking_date'])),
									'invoice_src' => $invoice_src
								);
							}
                        }
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
	
	/**
	* Get Trip Information
	*
	* Accept User id and authenticate that with database values
	*
	* @param user_id as string	id of the user
	* @param ride_id as string	id of the tide
	* @return json which having the trip information
	*
	**/
	public function user_trip_view() {
        $returnArr['status'] = '0';
        $returnArr['response'] = '';
        try {
            $user_id = (string) $this->input->post('user_id');
            $ride_id = (string) $this->input->post('ride_id');

            if ($user_id != '') {
                $userVal = $this->app_model->get_selected_fields(USERS, array('_id' => MongoID($user_id)), array('city', 'avail_category'));
                if ($userVal->num_rows() > 0) {
                    $checkRide = $this->app_model->get_all_details(RIDES, array('ride_id' => $ride_id, 'user.id' => $user_id));
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
						$distance_unit = $this->data['d_distance_unit'];
						if(isset($checkRide->row()->fare_breakup['distance_unit'])){
							$distance_unit = $checkRide->row()->fare_breakup['distance_unit'];
						}
						$disp_distance_unit = $distance_unit;
						if($distance_unit == 'km') $disp_distance_unit = $this->format_string('km', 'km');
						if($distance_unit == 'mi') $disp_distance_unit = $this->format_string('mi', 'mi');
						
						$invoice_src = '';
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
						
						
                        $disp_status = '';
                        if ($checkRide->row()->ride_status == 'Booked') {
                           $disp_status = $this->format_string("Booked", "booked");
                        } else if ($checkRide->row()->ride_status == 'Confirmed') {
                            $disp_status = $this->format_string("Accepted", "accepted");
                        } else if ($checkRide->row()->ride_status == 'Cancelled') {
                            $disp_status = $this->format_string("Cancelled", "cancelled");
                        } else if ($checkRide->row()->ride_status == 'Completed') {
							$disp_status = $this->format_string("Completed", "completed");
							$invoice_path = 'trip_invoice/'.$ride_id.'_large.jpg'; 
							if(file_exists($invoice_path)) { $invoice_src = base_url().$invoice_path; }
                        } else if ($checkRide->row()->ride_status == 'Finished') {
                            $disp_status = $this->format_string("Awaiting Payment", "await_payment");
                        } else if ($checkRide->row()->ride_status == 'Arrived' || $checkRide->row()->ride_status == 'Onride') {
                            $disp_status = $this->format_string("On Ride", "on_ride");
                        }

                        $isFav = 0;
                        $favLocId = "";
                        $longitude = $checkRide->row()->booking_information['pickup']['latlong']['lon'];
                        $latitude = $checkRide->row()->booking_information['pickup']['latlong']['lat'];
                        $loc_key = 'lat_' . str_replace('.', '-', $longitude) . 'lon_' . str_replace('.', '-', $latitude);
                        $fav_condition = array('user_id' => MongoID($user_id));
                        $checkUserInFav = $this->app_model->get_all_details(FAVOURITE, $fav_condition);
                        if ($checkUserInFav->num_rows() > 0) {
                            if (isset($checkUserInFav->row()->fav_location)) {
                                if (array_key_exists($loc_key, $checkUserInFav->row()->fav_location)) {
                                    $isFav = 1;
                                    $favLocId = $loc_key;
                                }
                            }
                        }


                        $doTrack = 0;
                        if (($checkRide->row()->driver['id'] != '') && ($checkRide->row()->ride_status == 'Confirmed' || $checkRide->row()->ride_status == 'Arrived' || $checkRide->row()->ride_status == 'Onride')) {
                            $doTrack = 1;
                        }
                        $do_fav = 0;
                        if($checkRide->row()->ride_status == 'Finished' || $checkRide->row()->ride_status == 'Completed'){
                            $do_fav = 1;
                        }
                        $doAction = 0;
                        if ($checkRide->row()->ride_status == 'Booked' || $checkRide->row()->ride_status == 'Confirmed' || $checkRide->row()->ride_status == 'Cancelled' || $checkRide->row()->ride_status == 'Arrived') {
                            $doAction = 1;
                            if ($checkRide->row()->ride_status == 'Cancelled') {
                                $doAction = 0;
                            }
                        }

						$ride_date = get_time_to_string("M d, Y", MongoEPOCH($checkRide->row()->booking_information['est_pickup_date']));
                        $pickup_date = '';
                        $drop_date = '';
                        if ($checkRide->row()->ride_status == 'Booked' || $checkRide->row()->ride_status == 'Confirmed' || $checkRide->row()->ride_status == 'Cancelled' || $checkRide->row()->ride_status == 'Arrived' || $checkRide->row()->ride_status == 'Expired') {
                            $pickup_date = get_time_to_string("h:i A", MongoEPOCH($checkRide->row()->booking_information['est_pickup_date']));
                        } else  if($checkRide->row()->ride_status == 'Onride'){
                            $pickup_date = get_time_to_string("h:i A", MongoEPOCH($checkRide->row()->history['begin_ride']));
                        }else {
                            $pickup_date = get_time_to_string("h:i A", MongoEPOCH($checkRide->row()->history['begin_ride']));
                            $drop_date = get_time_to_string("h:i A", MongoEPOCH($checkRide->row()->history['end_ride']));
                        }
						
						$drop_arr = array();
						if(array_key_exists("drop",$checkRide->row()->booking_information)){
							if(array_key_exists("location",$checkRide->row()->booking_information['drop'])){
								if($checkRide->row()->booking_information['drop']['location']!=''){
									$drop_arr = $checkRide->row()->booking_information['drop'];
								}
							}
						}
                        if (empty($drop_arr)) {
                            $drop_arr = json_decode("{}");
                        }
						
						$fare_summary = array();
						
						$trip_type = "Normal";
						if(isset($checkRide->row()->pool_ride)){
							if($checkRide->row()->pool_ride=="Yes"){
								$trip_type = "Share";
							}
						}
						
						if($trip_type == "Normal"){
							if (isset($checkRide->row()->total['base_fare'])) {
								if ($checkRide->row()->total['base_fare'] >= 0) {
									$fare_summary[] = array("title"=>(string)$this->format_string("Base fare", "fare_summary_base_fare"),
																		"value"=>(string)number_format($checkRide->row()->total['base_fare'],2,'.','')
																		);
								}
							}
							if (isset($checkRide->row()->total['peak_time_charge'])) {
								if ($checkRide->row()->total['peak_time_charge'] > 0) {
									$fare_summary[] = array("title"=>(string)$this->format_string("Peak time fare", "fare_summary_peak_time_fare").' ('.floatval($checkRide->row()->fare_breakup['peak_time_charge']).'X)',
                                                            "value"=>(string)number_format($checkRide->row()->total['peak_time_charge'],2,'.','')
                                                            );
								}
							}
							if (isset($checkRide->row()->total['night_time_charge'])) {
								if ($checkRide->row()->total['night_time_charge'] > 0) {
									$fare_summary[] = array("title"=>(string)$this->format_string("Night time fare", "fare_summary_night_time_fare").' ('.floatval($checkRide->row()->fare_breakup['night_charge']).'X)',
																		"value"=>(string)number_format($checkRide->row()->total['night_time_charge'],2,'.','')
																		);
								}
							}
						}
						if (isset($checkRide->row()->total['total_fare'])) {
							if ($checkRide->row()->total['total_fare'] >= 0) {
								$fare_summary[] = array("title"=>(string)$this->format_string("Trip Fare", "fare_summary_trip_fare"),
																	"value"=>(string)number_format($checkRide->row()->total['total_fare'],2,'.','')
																	);
							}
						}
						
						if (isset($checkRide->row()->total['coupon_discount'])) {
							if ($checkRide->row()->total['coupon_discount'] > 0) {
								$fare_summary[] = array("title"=>(string)$this->format_string("Discount amount", "fare_summary_coupon_discount"),
																	"value"=>(string)number_format($checkRide->row()->total['coupon_discount'],2,'.','')
																	);
							}
						}
						
						if (isset($checkRide->row()->total['service_tax'])) {
							if ($checkRide->row()->total['service_tax'] >= 0) {
								$fare_summary[] = array("title"=>(string)$this->format_string("Service Tax", "fare_summary_service_tax"),
																	"value"=>(string)number_format($checkRide->row()->total['service_tax'],2,'.','')
																	);
							}
						}
						if (isset($checkRide->row()->total['grand_fare'])) {
							if ($checkRide->row()->total['grand_fare'] >= 0) {
								$fare_summary[] = array("title"=>(string)$this->format_string("Sub Total", "fare_summary_sub_total"),
																	"value"=>(string)number_format($checkRide->row()->total['grand_fare'],2,'.','')
																	);
							}
						}
						
						if (isset($checkRide->row()->total['tips_amount'])) {
							if ($checkRide->row()->total['tips_amount'] > 0) {
								$fare_summary[] = array("title"=>(string)$this->format_string("Tips amount", "fare_summary_tips"),
																	"value"=>(string)number_format($checkRide->row()->total['tips_amount'],2,'.','')
																	);
							}
						}
						if (isset($checkRide->row()->total['wallet_usage'])) {
							if ($checkRide->row()->total['wallet_usage'] > 0) {
								$fare_summary[] = array("title"=>(string)$this->format_string("Wallet used", "fare_summary_wallet_used"),
																	"value"=>(string)number_format($checkRide->row()->total['wallet_usage'],2,'.','')
																	);
							}
						}
						
						if (isset($checkRide->row()->total['paid_amount'])) {
							if ($checkRide->row()->total['paid_amount'] > 0) {
								$fare_summary[] = array("title"=>(string)$this->format_string("Paid Amount", "fare_summary_paid_amount"),
																	"value"=>(string)number_format($checkRide->row()->total['paid_amount'],2,'.','')
																	);
							}
						}
						
						$fin_tips_amount = 0;$fin_total_paid = 0;$fin_wallet_usage = 0;$fin_grand_bill = 0;
						$payable_amount = 0;
						 if (isset($checkRide->row()->total['tips_amount'])) {
							if ($checkRide->row()->total['tips_amount'] > 0) {
								$fin_tips_amount = $checkRide->row()->total['tips_amount'];
							}
						}
						if (isset($checkRide->row()->total['grand_fare'])) {
							$fin_grand_bill = $checkRide->row()->total['grand_fare'];
						}
						if (isset($checkRide->row()->total['wallet_usage'])) {
							$fin_wallet_usage = $checkRide->row()->total['wallet_usage'];
						}
						if (isset($checkRide->row()->total['paid_amount'])) {
							$fin_total_paid = $checkRide->row()->total['paid_amount'];
						}
                        if (isset($checkRide->row()->total['paid_amount_with_tips'])) {
							$fin_total_paid_with_tips = $checkRide->row()->total['paid_amount_with_tips'];
						}
                        
                        if($fin_tips_amount > 0 && $checkRide->row()->pay_summary['type']=='Gateway'){
                           $payable_amount =  $fin_grand_bill + $fin_tips_amount - $fin_wallet_usage - $fin_total_paid_with_tips;
                        } else {
                           $payable_amount =  $fin_grand_bill  + $fin_tips_amount - $fin_wallet_usage - $fin_total_paid;
                        }
                       
					    if ($payable_amount > 0) {
							$fare_summary[] = array("title"=>(string)$this->format_string("Payable Amount", "fare_summary_payable_amount"),
																"value"=>(string)number_format($payable_amount,2,'.','')
																);
						}
						
                        if (empty($fare_summary)) {
                            $fare_summary = json_decode("{}");
                        }
						
						
						$cab_type = $checkRide->row()->booking_information['service_type'];
						$cab_type_id = $checkRide->row()->booking_information['service_id'];
						if($cab_type_id==POOL_ID){
							$cab_type = (string)$this->config->item('pooling_name');
						}else{
							$categoryInfo = $this->app_model->get_selected_fields(CATEGORY, array('_id' => MongoID($checkRide->row()->booking_information['service_id'])), array('_id', 'name', 'brand_name', 'icon_car_image','name_languages'));
							if ($categoryInfo->num_rows() > 0) {
								$cab_type = $categoryInfo->row()->name;
								if(isset($categoryInfo->row()->name_languages)){
									$langKey = $this->data['sms_lang_code'];
									$arrVal = $categoryInfo->row()->name_languages;
									if(array_key_exists($langKey,$arrVal)){
										if($categoryInfo->row()->name_languages[$langKey]!=""){
											$cab_type = $categoryInfo->row()->name_languages[$langKey];
										}
									}
								}							
							}
						}
                        
                        $cancellation_info = array();
                        /* if($checkRide->row()->ride_status == 'Cancelled'){
                            
                            $cancelled_by = '';
                            if(isset($checkRide->row()->cancelled)){
                                $cancelled_by = get_language_value_for_keyword(strtolower($checkRide->row()->cancelled['primary']['by']),$this->data['langCode']); 
                            }
                            
                            $cancel_reason = $checkRide->row()->cancelled['primary']['text']; 
                            if(isset($rides_details->cancelled['primary']['reason']) && $rides_details->cancelled['primary']['reason'] != '' ){
                                $cancel_reason = get_cancellation_reason_by_lang($checkRide->row()->cancelled['primary']['reason'],$langCode);
                            }
                            
                            
                            $cancellation_info = array(
                                array('title' => (string)$this->format_string("Cancelled By", "cancelled_by"),
                                'value' => $cancelled_by
                                ),
                                array('title' => (string)$this->format_string("Cancellation Reason", "cancellation_reason"),
                                'value' => $cancel_reason
                                ),
                            );
                        } */
						
						$trip_cost = $fin_grand_bill;


                        $responseArr = array('currency' => $checkRide->row()->currency,
                            'cab_type' => (string)$cab_type,
                            'trip_type' => (string)$trip_type,
                            'ride_id' => $checkRide->row()->ride_id,
                            'ride_status' => $checkRide->row()->ride_status,
                            'disp_status' => (string) $disp_status,
                            'do_cancel_action' => (string) $doAction,
                            'do_track_action' => (string) $doTrack,
                            'do_fav' => (string) $do_fav,
                            'is_fav_location' => (string) $isFav,
                            'fav_location_id' => (string) $favLocId,
                            'pay_status' => $pay_status,
                            'disp_pay_status' => $disp_pay_status,
                            'pickup' => $checkRide->row()->booking_information['pickup'],
                            'drop' => $drop_arr,
                            'ride_date' => (string) $ride_date,
                            'pickup_date' => (string) $pickup_date,
                            'drop_date' => (string) $drop_date,
                            'summary' => $summaryArr,
                            'fare_summary' => $fare_summary,
                            'distance_unit' => $disp_distance_unit,
							'invoice_src' => $invoice_src,
                            'cancellation_info' => $cancellation_info,
                            'trip_cost' => (string)number_format($trip_cost,2)
                        );
                        if (empty($responseArr)) {
                            $responseArr = json_decode("{}");
                        }
                        $returnArr['status'] = '1';
                        $returnArr['response'] = array('details' => $responseArr);
                    } else {
                        $returnArr['response'] = $this->format_string("Records not available", "no_records_found ");
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
	* This Function check whether the coupon is valid or not
	*
	**/
    public function apply_coupon_code() {
        $returnArr['status'] = '0';
        $returnArr['response'] = '';
        try {
            $user_id = $this->input->post('user_id');
            $code = $this->input->post('code');
            $pickup_date = $this->input->post('pickup_date');
			
            $lat = $this->input->post('lat');
            $long = $this->input->post('long');
            $category = $this->input->post('category');

            if (is_array($this->input->post())) {
                $chkValues = count(array_filter($this->input->post()));
            } else {
                $chkValues = 0;
            }

            if ($chkValues >= 3) {
                $checkUser = $this->app_model->get_selected_fields(USERS, array('_id' => MongoID($user_id)), array('email'));
                if ($checkUser->num_rows() == 1) {
                    $checkCode = $this->app_model->get_all_details(PROMOCODE, array('promo_code' => $code));
                    if ($checkCode->num_rows() > 0) {
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
                                    $usage = $this->app_model->check_user_usage($coupon_usage, $user_id);
                                    if ($usage < $checkCode->row()->user_usage) {
										$discount_amount = $checkCode->row()->promo_value;
										$discount_type = $checkCode->row()->code_type;
										$currencyCode = $this->data['dcurrencyCode'];
                                        $returnArr['status'] = '1';
                                        $returnArr['response'] = array('code' => (string) $code, 
																		'discount_amount' => (string) $discount_amount,
																		'discount_type' => (string) $discount_type,
																		'currency_code' => (string) $currencyCode,
																		'message' => $this->format_string('Coupon code applied.', 'coupon_applied'));
                                    } else {
                                        $returnArr['response'] = $this->format_string('Maximum no used in your account', 'maximum_not_used_in_your_account');
                                    }
                                } else {
                                    $returnArr['response'] = $this->format_string('Coupon Expired', 'coupon_expired');
                                }
                            } else {
                                $returnArr['response'] = $this->format_string('Coupon Expired', 'coupon_expired');
                            }
                        } else {
                            $returnArr['response'] = $this->format_string('Unavailable Coupon', 'coupon_unavailable');
                        }
                    } else {
                        $returnArr['response'] = $this->format_string('Invalid Coupon', 'nvalid_coupon');
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
	* This Function used for delete a ride
	*
	**/
    public function delete_ride() {
        $returnArr['status'] = '0';
        $returnArr['response'] = '';
        $returnArr['acceptance'] = 'No';
        try {
            $user_id = $this->input->post('user_id');
            $ride_id = $this->input->post('ride_id');
			$mode = $this->input->post('mode');
			$deleteInfo = array("user_id"=>$user_id,"ride_id"=>$ride_id,"mode"=>$mode);
			request_delete($deleteInfo); die;
            
        } catch (MongoException $ex) {
            $returnArr['response'] = $this->format_string("Error in connection", "error_in_connection");
        }
        $json_encode = json_encode($returnArr, JSON_PRETTY_PRINT);
        echo $this->cleanString($json_encode);
    }
	
	
	/**
	* Get Trip Information
	*
	* Accept User id and authenticate that with database values
	*
	* @param id as string	id of the user/driver
	* @param user_type as string	id of the user/driver
	* @param ride_id as string	id of the tide
	* @return json which having the trip information
	*
	**/
	public function common_trip_details() {
        $returnArr['status'] = '0';
        $returnArr['response'] = '';
        try {
            $id = (string) $this->input->post('id');
            $ride_id = (string) $this->input->post('ride_id');
			$type = (string) $this->input->post('type');    #options user/driver
 
            if ($id != '' &&  $ride_id != '' && $type != '') {
				$authChk = FALSE;
				$cond = array('ride_id' => $ride_id);
				if($type == 'user'){
					$userVal = $this->app_model->get_selected_fields(USERS, array('_id' => MongoID($id)), array());
					if ($userVal->num_rows() > 0)  $authChk = TRUE;
					$cond['user.id'] = $id;
				} else if($type == 'driver'){
					$driverVal = $this->app_model->get_selected_fields(DRIVERS, array('_id' => MongoID($id)), array());
					if ($driverVal->num_rows() > 0)  $authChk = TRUE;
					$cond['driver.id'] = $id;
				}
                if ($authChk) {
                    $checkRide = $this->app_model->get_all_details(RIDES, $cond);
                    if ($checkRide->num_rows() == 1) {
                        $fareArr = array(); $summaryArr = array();
						$invoice_src = ''; $pay_status = ''; $disp_pay_status = '';
						$min_short = $this->format_string('min', 'min_short');
						$mins_short = $this->format_string('mins', 'mins_short');
						$distance_unit = $this->data['d_distance_unit'];
						if(isset($checkRide->row()->fare_breakup['distance_unit'])){
							$distance_unit = $checkRide->row()->fare_breakup['distance_unit'];
						}
						$disp_distance_unit = $distance_unit;
						if($distance_unit == 'km') $disp_distance_unit = $this->format_string('km', 'km');
						if($distance_unit == 'mi') $disp_distance_unit = $this->format_string('mi', 'mi');
                        if (isset($checkRide->row()->summary)) {
                            if (is_array($checkRide->row()->summary)) {
								$summary = $checkRide->row()->summary;
								if(array_key_exists("ride_duration",$summary)){
									$values = $summary["ride_duration"];
									if($values<=1){
										$unit = $min_short;
									}else{
										$unit = $mins_short;
									}
									$summaryArr[] = array("title"=>$this->format_string("Trip Duration", "trip_summary_trip_duration",FALSE),
																		"value"=>$values.' '.$unit
																);
								}
								if(array_key_exists("waiting_duration",$summary)){
									$values = $summary["waiting_duration"];
									if($values<=1){
										$unit = $min_short;
									}else{
										$unit = $mins_short;
									}
									$summaryArr[] = array("title"=>$this->format_string("Waiting Duration", "trip_summary_waiting_duration",FALSE),
																		"value"=>$values.' '.$unit
																);
								}
								if(array_key_exists("ride_distance",$summary)){
									$values = $summary["ride_distance"];
									$summaryArr[] = array("title"=>$this->format_string("Trip Distance", "trip_summary_trip_distance",FALSE),
																		"value"=>$values.' '.$disp_distance_unit
																);
								}
                            }
                        }
						
						$ride_status = $checkRide->row()->ride_status;
						
                        if (isset($checkRide->row()->pay_status)) {
                            $pay_status = $checkRide->row()->pay_status;
							if($pay_status == 'Paid'){
								$disp_pay_status = $this->format_string("Paid", "paid");
							}else {
								$pay_status == 'Pending';
								$disp_pay_status = $this->format_string("Pending", "pending");
							}
                        }
						
						
                        $disp_status = '';
                        if ($ride_status == 'Booked') {
                           $disp_status = $this->format_string("Booked", "booked");
                        } else if ($ride_status == 'Confirmed') {
                            $disp_status = $this->format_string("Accepted", "accepted");
                        } else if ($ride_status == 'Cancelled') {
                            $disp_status = $this->format_string("Cancelled", "cancelled");
                        } else if ($ride_status == 'Completed') {
							$disp_status = $this->format_string("Completed", "completed");
                        } else if ($ride_status == 'Finished') {
                            $disp_status = $this->format_string("Awaiting Payment", "await_payment");
                        } else if ($ride_status == 'Arrived' || $ride_status == 'Onride') {
                            $disp_status = $this->format_string("On Ride", "on_ride");
                        }
						
						$invoice_path = 'trip_invoice/'.$ride_id.'_large.jpg'; 
						if(file_exists($invoice_path)) { $invoice_src = base_url().$invoice_path; }

                        $isFav = 0; $favLocId = ""; $do_fav = 0;
						if($type == 'user'){
							$longitude = $checkRide->row()->booking_information['pickup']['latlong']['lon'];
							$latitude = $checkRide->row()->booking_information['pickup']['latlong']['lat'];
							$loc_key = 'lat_' . str_replace('.', '-', $longitude) . 'lon_' . str_replace('.', '-', $latitude);
							$fav_condition = array('user_id' => MongoID($id));
							$checkUserInFav = $this->app_model->get_all_details(FAVOURITE, $fav_condition);
							if ($checkUserInFav->num_rows() > 0) {
								if (isset($checkUserInFav->row()->fav_location)) {
									if (array_key_exists($loc_key, $checkUserInFav->row()->fav_location)) {
										$isFav = 1;
										$favLocId = $loc_key;
									}
								}
							}
							if($ride_status == 'Finished' || $ride_status == 'Completed'){
								$do_fav = 1;
							}
						}


                        $doTrack = 0;
                        if (($checkRide->row()->driver['id'] != '') && ($ride_status == 'Confirmed' || $ride_status == 'Arrived' || $ride_status == 'Onride')) {
                            $doTrack = 1;
                        }
                        
                        $doCnlAction = 0;
                        if ($ride_status == 'Booked' || $ride_status == 'Confirmed' || $ride_status == 'Cancelled' || $ride_status == 'Arrived') {
                            $doCnlAction = 1;
                            if ($ride_status == 'Cancelled') {
                                $doCnlAction = 0;
                            }
                        }

						$ride_date = get_time_to_string("M d, Y", MongoEPOCH($checkRide->row()->booking_information['est_pickup_date']));
												
						$fare_summary = array();
						
						$trip_type = "Normal";
						if(isset($checkRide->row()->pool_ride)){
							if($checkRide->row()->pool_ride=="Yes"){
								$trip_type = "Share";
							}
						}
						
						if($trip_type == "Normal"){
							if (isset($checkRide->row()->total['base_fare'])) {
								if ($checkRide->row()->total['base_fare'] >= 0) {
									$fare_summary[] = array("title"=>(string)$this->format_string("Base fare", "fare_summary_base_fare"),
																		"value"=>(string)number_format($checkRide->row()->total['base_fare'],2,'.','')
																		);
								}
							}
							if (isset($checkRide->row()->total['peak_time_charge'])) {
								if ($checkRide->row()->total['peak_time_charge'] > 0) {
									$fare_summary[] = array("title"=>(string)$this->format_string("Peak time fare", "fare_summary_peak_time_fare").' ('.floatval($checkRide->row()->fare_breakup['peak_time_charge']).'X)',
                                                            "value"=>(string)number_format($checkRide->row()->total['peak_time_charge'],2,'.','')
                                                            );
								}
							}
							if (isset($checkRide->row()->total['night_time_charge'])) {
								if ($checkRide->row()->total['night_time_charge'] > 0) {
									$fare_summary[] = array("title"=>(string)$this->format_string("Night time fare", "fare_summary_night_time_fare").' ('.floatval($checkRide->row()->fare_breakup['night_charge']).'X)',
																		"value"=>(string)number_format($checkRide->row()->total['night_time_charge'],2,'.','')
																		);
								}
							}
						}
						if (isset($checkRide->row()->total['total_fare'])) {
							if ($checkRide->row()->total['total_fare'] >= 0) {
								$fare_summary[] = array("title"=>(string)$this->format_string("Trip Fare", "fare_summary_trip_fare"),
																	"value"=>(string)number_format($checkRide->row()->total['total_fare'],2,'.','')
																	);
							}
						}
						
						if (isset($checkRide->row()->total['coupon_discount'])) {
							if ($checkRide->row()->total['coupon_discount'] > 0) {
								$fare_summary[] = array("title"=>(string)$this->format_string("Discount amount", "fare_summary_coupon_discount"),
																	"value"=>(string)number_format($checkRide->row()->total['coupon_discount'],2,'.','')
																	);
							}
						}
						
						if (isset($checkRide->row()->total['parking_charge'])) {
							if ($checkRide->row()->total['parking_charge'] > 0) {
								$fare_summary[] = array("title"=>(string)$this->format_string("Parking Charge", "fare_summary_parking_charge"),
														"value"=>(string)number_format($checkRide->row()->total['parking_charge'],2,'.','')
																	);
							}
						}
						if (isset($checkRide->row()->total['toll_charge'])) {
							if ($checkRide->row()->total['toll_charge'] > 0) {
								$fare_summary[] = array("title"=>(string)$this->format_string("Toll Charge", "fare_summary_toll_charge"),
														"value"=>(string)number_format($checkRide->row()->total['toll_charge'],2,'.','')
																	);
							}
						}
						
						if (isset($checkRide->row()->total['service_tax'])) {
							if ($checkRide->row()->total['service_tax'] >= 0) {
								$fare_summary[] = array("title"=>(string)$this->format_string("Service Tax", "fare_summary_service_tax"),
																	"value"=>(string)number_format($checkRide->row()->total['service_tax'],2,'.','')
																	);
							}
						}
						if (isset($checkRide->row()->total['grand_fare'])) {
							if ($checkRide->row()->total['grand_fare'] >= 0) {
								$fare_summary[] = array("title"=>(string)$this->format_string("Sub Total", "fare_summary_sub_total"),
																	"value"=>(string)number_format($checkRide->row()->total['grand_fare'],2,'.','')
																	);
							}
						}
						
						if (isset($checkRide->row()->total['tips_amount'])) {
							if ($checkRide->row()->total['tips_amount'] > 0) {
								$fare_summary[] = array("title"=>(string)$this->format_string("Tips amount", "fare_summary_tips"),
																	"value"=>(string)number_format($checkRide->row()->total['tips_amount'],2,'.','')
																	);
							}
						}
						if (isset($checkRide->row()->total['wallet_usage'])) {
							if ($checkRide->row()->total['wallet_usage'] > 0) {
								$fare_summary[] = array("title"=>(string)$this->format_string("Wallet used", "fare_summary_wallet_used"),
																	"value"=>(string)number_format($checkRide->row()->total['wallet_usage'],2,'.','')
																	);
							}
						}
						
						if (isset($checkRide->row()->total['paid_amount'])) {
							if ($checkRide->row()->total['paid_amount'] > 0) {
								$fare_summary[] = array("title"=>(string)$this->format_string("Paid Amount", "fare_summary_paid_amount"),
																	"value"=>(string)number_format($checkRide->row()->total['paid_amount'],2,'.','')
																	);
							}
						}
						
						$fin_tips_amount = 0;$fin_total_paid = 0;$fin_wallet_usage = 0;$fin_grand_bill = 0;
						$payable_amount = 0; $fin_total_paid_with_tips = 0;
						 if (isset($checkRide->row()->total['tips_amount'])) {
							if ($checkRide->row()->total['tips_amount'] > 0) {
								$fin_tips_amount = $checkRide->row()->total['tips_amount'];
							}
						}
						if (isset($checkRide->row()->total['grand_fare'])) {
							$fin_grand_bill = $checkRide->row()->total['grand_fare'];
						}
						if (isset($checkRide->row()->total['wallet_usage'])) {
							$fin_wallet_usage = $checkRide->row()->total['wallet_usage'];
						}
						if (isset($checkRide->row()->total['paid_amount'])) {
							$fin_total_paid = $checkRide->row()->total['paid_amount'];
						}
                        if (isset($checkRide->row()->total['paid_amount_with_tips'])) {
							$fin_total_paid_with_tips = $checkRide->row()->total['paid_amount_with_tips'];
						}
                        
                         $payMode = $checkRide->row()->pay_summary['type'];
                        if($fin_tips_amount > 0 && ($payMode=='Gateway' || $payMode == 'Wallet_Gateway')){
                           $payable_amount =  $fin_grand_bill + $fin_tips_amount - $fin_wallet_usage - $fin_total_paid_with_tips;
                        } else {
                           $payable_amount =  $fin_grand_bill  + $fin_tips_amount - $fin_wallet_usage - $fin_total_paid;
                        }
                       
					    $payableTitle = (string)$this->format_string("Payable Amount", "fare_summary_payable_amount");
					   if($type == 'driver'){
							$payableTitle = (string)$this->format_string("Pending Amount", "fare_summary_pending_amount");
					   }
					   
					    if ($payable_amount > 0) {
							$fare_summary[] = array("title"=> $payableTitle,
																"value"=>(string)number_format($payable_amount,2,'.','')
																);
						}
						
                        if (empty($fare_summary)) {
                            $fare_summary = json_decode("{}");
                        }
						$cab_type = $checkRide->row()->booking_information['service_type'];
						$cab_type_id = $checkRide->row()->booking_information['service_id'];
						if($cab_type_id==POOL_ID){
							$cab_type = (string)$this->config->item('pooling_name');
						}else{
							$categoryInfo = $this->app_model->get_selected_fields(CATEGORY, array('_id' => MongoID($checkRide->row()->booking_information['service_id'])), array('_id', 'name', 'brand_name', 'icon_car_image','name_languages'));
							if ($categoryInfo->num_rows() > 0) {
								$cab_type = $categoryInfo->row()->name;
								if(isset($categoryInfo->row()->name_languages)){
									$langKey = $this->data['sms_lang_code'];
									$arrVal = $categoryInfo->row()->name_languages;
									if(array_key_exists($langKey,$arrVal)){
										if($categoryInfo->row()->name_languages[$langKey]!=""){
											$cab_type = $categoryInfo->row()->name_languages[$langKey];
										}
									}
								}							
							}
						}
                        			
						$trip_cost = $fin_grand_bill;						
						$locations = array();
						
						if(isset($checkRide->row()->booking_information['pickup'])){
							if(isset($checkRide->row()->history['begin_ride'])) $pickup_date = get_time_to_string("h:i A", MongoEPOCH($checkRide->row()->history['begin_ride'])); else $pickup_date = "";
							$locations[] = array("address"=>(string)$checkRide->row()->booking_information['pickup']['location'],
														"lat"=>(string)$checkRide->row()->booking_information['pickup']['latlong']['lat'],
														"lon"=>(string)$checkRide->row()->booking_information['pickup']['latlong']['lon'],
														"type"=>"pickup",
														"time"=>(string)$pickup_date
													);
						}
						if(isset($checkRide->row()->booking_information['drop']) && $checkRide->row()->booking_information['drop']['location']!=""){
							if(isset($checkRide->row()->history['end_ride'])) $drop_date = get_time_to_string("h:i A", MongoEPOCH($checkRide->row()->history['end_ride'])); else $drop_date = "";
							$locations[] = array("address"=>(string)$checkRide->row()->booking_information['drop']['location'],
														"lat"=>(string)$checkRide->row()->booking_information['drop']['latlong']['lat'],
														"lon"=>(string)$checkRide->row()->booking_information['drop']['latlong']['lon'],
														"type"=>"drop",
														"time"=>(string)$drop_date
													);
						}
												
                        $responseArr = array('currency' => $checkRide->row()->currency,
                            'cab_type' => (string)$cab_type,
                            'trip_type' => (string)$trip_type,
                            'ride_id' => $checkRide->row()->ride_id,
                            'ride_status' => $ride_status,
                            'disp_status' => (string) $disp_status,
                            'do_cancel_action' => (string) $doCnlAction,
                            'pay_status' => $pay_status,
                            'disp_pay_status' => $disp_pay_status,
                            'ride_date' => (string) $ride_date,
                            'distance_unit' => $disp_distance_unit,
							'invoice_src' => $invoice_src,
                            'trip_cost' => (string)number_format($trip_cost,2),
                            'locations' => $locations,
                            'summary' => $summaryArr,
                            'fare_summary' => $fare_summary
                        );
						$type = 'user';
						if($type == 'user'){
							$userActions = array( 
									'do_track_action' => (string) $doTrack,
									'do_fav' => (string) $do_fav,
									'is_fav_location' => (string) $isFav,
									'fav_location_id' => (string) $favLocId
							);
							$responseArr['user_actions'] = $userActions;
						}
						$type = 'driver';
						if($type == 'driver'){
							$iscontinue = 'NO';
							if ($ride_status == 'Confirmed' || $ride_status == 'Arrived' || $ride_status == 'Onride') {
								if ($ride_status == 'Confirmed')  $iscontinue = 'arrived';
								if ($ride_status == 'Arrived') $iscontinue = 'begin';
								if ($ride_status == 'Onride') $iscontinue = 'end';
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
							
							$driverActions = array( 
									'continue_ride' => $iscontinue,
									'receive_cash' => $receive_cash,
									'req_payment' => $req_payment, 
							);
							$responseArr['driver_actions'] = $driverActions;
						}						
						
                        if (empty($responseArr)) {
                            $responseArr = json_decode("{}");
                        }
                        $returnArr['status'] = '1';
                        $returnArr['response'] = array('details' => $responseArr);
                    } else {
                        $returnArr['response'] = $this->format_string("Records not available", "no_records_found ");
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

}

/* End of file booking.php */
/* Location: ./application/controllers/v8/api/booking.php */