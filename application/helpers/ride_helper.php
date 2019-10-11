<?php  if ( ! defined('BASEPATH')) exit('No direct script access allowed');


	/* Saving ride details for future stats */
	
	if (!function_exists('save_ride_details_for_stats')){
		function save_ride_details_for_stats($ride_id) {
			$ci =& get_instance();
			$checkRide = $ci->app_model->get_selected_fields(RIDES, array('ride_id' => $ride_id),array('booking_information','user'));
			if($checkRide->num_rows() > 0 ){
				$dataArr = array(
									'user_id' => $checkRide->row()->user['id'],
									'location_id' => $checkRide->row()->location['id'],
									'location'=>$checkRide->row()->booking_information['pickup']['latlong'],'pickup_address'=> trim(
															preg_replace( "/\r|\n/", "", $checkRide->row()->booking_information['pickup']['location'] )
															),
									'category' => $checkRide->row()->booking_information['service_id'],
									'ride_time' => $checkRide->row()->booking_information['est_pickup_date']
								);
				$ci->app_model->simple_insert(RIDE_STATISTICS,$dataArr);
			}
		}
	}
	
	/* Saving ride details for future stats */
	
	if ( ! function_exists('check_surge')){
		function check_surge($location_id,$category) {
			$change_txt = "";
			$ci =& get_instance();
			$checkLoc = $ci->app_model->get_all_details(LOCATIONS, array('_id' => MongoID($location_id)));
			if($checkLoc->num_rows() > 0 ){
				$surgeX = 0;
				$night_charge = 0;
				
				$night_charge_val = 0;
				$peak_charge_val = 0;
				
				if(isset($checkLoc->row()->fare[$category]['night_charge'])){
					$night_charge = $checkLoc->row()->fare[$category]['night_charge'];
					$surgeX = $checkLoc->row()->fare[$category]['night_charge'];
				}
				
				$peak_time_charge = 0;
				if(isset($checkLoc->row()->fare[$category]['peak_time_charge'])){
					$peak_time_charge = $checkLoc->row()->fare[$category]['peak_time_charge'];
					$surgeX = $checkLoc->row()->fare[$category]['peak_time_charge'];
				}
				
				if($peak_time_charge>0 && $night_charge>0){
					$surgeX = $peak_time_charge+$night_charge;
				}
				
				
				$pickup_datetime = time();
				$pickup_date = date('Y-m-d');
				if ($checkLoc->row()->night_charge == 'Yes') {
					$time1 = strtotime($pickup_date . ' ' . $checkLoc->row()->night_time_frame['from']);
					$time2 = strtotime($pickup_date . ' ' . $checkLoc->row()->night_time_frame['to']);
					$nc = FALSE;
					if ($time1 > $time2) {
						if (date('a', $pickup_datetime) == 'pm') {
							if (($time1 <= $pickup_datetime) && (strtotime('+1 day', $time2) >= $pickup_datetime)) {
								$nc = TRUE;
							}
						} else {
							if ((strtotime('-1 day', $time1) <= $pickup_datetime) && ($time2 >= $pickup_datetime)) {
								$nc = TRUE;
							}
						}
					} else if ($time1 < $time2) {
						if (($time1 <= $pickup_datetime) && ($time2 >= $pickup_datetime)) {
							$nc = TRUE;
						}
					}
					if ($nc) {
						if($night_charge>0){
							$change_txt = $ci->format_string('Night time charges', 'night_time_charge').' '.$night_charge.'X '. $ci->format_string('will be applied', 'will_be_applied');
							$night_charge_val = $night_charge;
						}
					}
				}
				if ($checkLoc->row()->peak_time == 'Yes') {
					$time1 = strtotime($pickup_date . ' ' . $checkLoc->row()->peak_time_frame['from']);
					$time2 = strtotime($pickup_date . ' ' . $checkLoc->row()->peak_time_frame['to']);
					$ptc = FALSE;
					if ($time1 > $time2) {
						if (date('a', $pickup_datetime) == 'pm') {
							if (($time1 <= $pickup_datetime) && (strtotime('+1 day', $time2) >= $pickup_datetime)) {
								$ptc = TRUE;
							}
						} else {
							if ((strtotime('-1 day', $time1) <= $pickup_datetime) && ($time2 >= $pickup_datetime)) {
								$ptc = TRUE;
							}
						}
					} else if ($time1 < $time2) {
						if (($time1 <= $pickup_datetime) && ($time2 >= $pickup_datetime)) {
							$ptc = TRUE;
						}
					}
					if ($ptc) {
						if($peak_time_charge>0){
							$change_txt = $ci->format_string('Peak time charges', 'peak_time_charges').' '.$peak_time_charge.'X '. $ci->format_string('will be applied', 'will_be_applied');
							$peak_charge_val = $peak_time_charge;
						}
					}
				}
				
				if($night_charge_val>0 && $peak_charge_val>0){					
					$change_txt = $ci->format_string('Peak and Night time charges', 'peak_and_night_time_charges').' '.($night_charge+$peak_time_charge).'X '. $ci->format_string('will be applied', 'will_be_applied');
				}
				
				
			}
			return $change_txt;
		}
	}
	
	
	/**
	*
	*	This function make a booking process for a user
	*	Param @bookingInfo as Array
	*	Holds all the booking information
	*
	**/	
	if ( ! function_exists('book_a_ride')){
		function book_a_ride($bookingInfo) {
			$ci =& get_instance();			
			
			$returnArr['status'] = '0';
			$returnArr['response'] = '';
			$acceptance = 'No';
			
			try {
				if(array_key_exists("user_id",$bookingInfo)) $user_id =  trim($bookingInfo['user_id']); else $user_id = "";
				if(array_key_exists("pickup",$bookingInfo)) $pickup =  trim($bookingInfo['pickup']); else $pickup = "";
				if(array_key_exists("pickup_lat",$bookingInfo)) $pickup_lat =  trim($bookingInfo['pickup_lat']); else $pickup_lat = "";
				if(array_key_exists("pickup_lon",$bookingInfo)) $pickup_lon =  trim($bookingInfo['pickup_lon']); else $pickup_lon = "";
				if(array_key_exists("category",$bookingInfo)) $category =  trim($bookingInfo['category']); else $category = "";
				if(array_key_exists("type",$bookingInfo)) $type =  trim($bookingInfo['type']); else $type = "";
				if(array_key_exists("pickup_date",$bookingInfo)) $pickup_date =  trim($bookingInfo['pickup_date']); else $pickup_date = "";
				if(array_key_exists("pickup_time",$bookingInfo)) $pickup_time =  trim($bookingInfo['pickup_time']); else $pickup_time = "";
				if(array_key_exists("code",$bookingInfo)) $code =  trim($bookingInfo['code']); else $code = "";
				if(array_key_exists("try",$bookingInfo)) $try =  trim($bookingInfo['try']); else $try = 1;
				if(array_key_exists("ride_id",$bookingInfo)) $ride_id =  trim($bookingInfo['ride_id']); else $ride_id = "";
				
				if(array_key_exists("platform",$bookingInfo)) $platform =  trim($bookingInfo['platform']); else $platform = "unknown";
				
				if(array_key_exists("drop_loc",$bookingInfo)) $drop_loc =  trim($bookingInfo['drop_loc']); else $drop_loc = "";
				if(array_key_exists("drop_lat",$bookingInfo)) $drop_lat =  trim($bookingInfo['drop_lat']); else $drop_lat = "";
				if(array_key_exists("drop_lon",$bookingInfo)) $drop_lon =  trim($bookingInfo['drop_lon']); else $drop_lon = "";
				if($drop_loc==''){
					$drop_lat = 0;
					$drop_lon = 0;
				}
				
				$genderPref = FALSE;
                $gender_pref = "";
				if(array_key_exists("gender_pref",$bookingInfo)) $gender_pref =  trim($bookingInfo['gender_pref']);
				if($gender_pref=="Yes"){
					if(array_key_exists("gender",$bookingInfo)) $gender =  trim($bookingInfo['gender']); else $gender = "";
					if($gender!=""){
						$genderPref = $gender;
					}
				}
				
				if(array_key_exists("booking_source",$bookingInfo)) $booking_source =  trim($bookingInfo['booking_source']); else $booking_source = "";
				if(array_key_exists("booked_by",$bookingInfo)) $booked_by =  MongoID($bookingInfo['booked_by']); else $booked_by = "";
				
				$riderlocArr = array('lat' => (string) $pickup_lat, 'lon' => (string) $pickup_lon);

				if(array_key_exists("share",$bookingInfo)) $share =  trim($bookingInfo['share']); else $share = ""; #	(Yes/No)
				if(array_key_exists("no_of_seat",$bookingInfo)) $no_of_seat=intval($bookingInfo['no_of_seat']); else $no_of_seat=0; #	(1/2)
				if($share=="") $share = "No";
				if($share=="Yes"){
					$type == 0;
				}
				
				if ($try > 1) {
					$limit = 10 * $try;
				} else {
					$limit = 10;
				}
				if ($type == 1) {
					$ride_type = 'Later';
					$pickup_datetime = $pickup_date . ' ' . $pickup_time;
					$pickup_timestamp = strtotime($pickup_datetime);
				} else {
					$ride_type = 'Now';
					$pickup_timestamp = time();
				}
				
				$after_one_hour = strtotime('+1 hour', time());
				if( $type == 0 || ($type ==1 && ($pickup_timestamp >= $after_one_hour)) ){					

					$acceptance = 'No';
					if ($ride_id != '') {
						$checkRide = $ci->app_model->get_all_details(RIDES, array('ride_id' => $ride_id));
						if ($checkRide->num_rows() == 1) {
							$requested_drivers = $checkRide->row()->requested_drivers;
							if ($checkRide->row()->ride_status == 'Confirmed' || $checkRide->row()->ride_status == 'Arrived' || $checkRide->row()->ride_status == 'Onride' || $checkRide->row()->ride_status == 'Finished') {
								$acceptance = 'Yes';
								$driver_id = $checkRide->row()->driver['id'];
								$mindurationtext = '';
								if (isset($checkRide->row()->driver['est_eta'])) {
									$mindurationtext = $checkRide->row()->driver['est_eta'] . '';
								}
								$lat_lon = @explode(',', $checkRide->row()->driver['lat_lon']);
								$driver_lat = $lat_lon[0];
								$driver_lon = $lat_lon[1];
							} else {
								if($checkRide->row()->ride_status == 'Booked'){
									/* Saving Unaccepted Ride for future reference */
									save_ride_details_for_stats($ride_id);
									/* Saving Unaccepted Ride for future reference */
									$ci->app_model->commonDelete(RIDES, array('ride_id' => $ride_id));
								}
							}
						}
					}

					if ($acceptance == 'No') {
						
						if ($user_id!="" && $pickup!="" && $pickup_lat!="" && $pickup_lon!="" && $category!="") {
							$checkUser = $ci->app_model->get_selected_fields(USERS, array('_id' => MongoID($user_id)), array('email', 'user_name', 'country_code', 'phone_number', 'push_type'));
							if ($checkUser->num_rows() == 1) {
								if ($checkUser->row()->push_type != '' || $platform=='website') {
									$coordinates = array(floatval($pickup_lon), floatval($pickup_lat));
									$location = $ci->app_model->find_location(floatval($pickup_lon), floatval($pickup_lat));
									
									$droploc_Check = TRUE;
									if($drop_loc!=''){
										$droploc_Check = FALSE;
										$droploc = $ci->app_model->find_location(floatval($drop_lon), floatval($drop_lat));
										if (!empty($droploc['result'])) {
											$droploc_Check = TRUE;
										}
									}
									if (!empty($location['result']) && $droploc_Check == TRUE) {
										if($share=="Yes"){
											$has_pool_service = 0;
											$pooling = 0;
											if($ci->data['share_pooling_status'] != ''){
												$pooling = $ci->data['share_pooling_status'];
											}								
											if($pooling==1){
												if(array_key_exists("share_pooling",$location['result'][0])){
													$share_pooling = $location['result'][0]['share_pooling'];
													if($share_pooling=="Enable"){
														$pool_categories = $location['result'][0]['pool_categories'];
														if(!empty($pool_categories)){
															$has_pool_service = 1;
														}										
													}
												}
											}
										}
										$loc_category = $location['result'][0]['fare'];
										if (($share=="Yes" && $has_pool_service == 1) || (array_key_exists($category,$loc_category))){
											$location_id = $location['result'][0]['_id'];
											
											$serviceArr = array();
											if($share=="Yes"){
												$serviceArr = array("service_type"=>(string)$ci->config->item('pooling_name'),
																					"service_id"=>(string)POOL_ID
																			);
											}else{
												$categoryResult = $ci->app_model->get_selected_fields(CATEGORY, array('_id' => MongoID($category)), array('name'));
												if($categoryResult->num_rows() > 0){
													$serviceArr = array("service_type"=>(string)$categoryResult->row()->name,
																						"service_id"=>(string)$categoryResult->row()->_id
																				);
												}
											}
											
											if(!empty($serviceArr) && ($share=="No" || ($share=="Yes" && $no_of_seat > 0))){    
												$requested_drivers = array();
												if($category == POOL_ID){  
													$categoryID = array();
													foreach($location['result'][0]['pool_categories'] as $pool_cat){
														$categoryID[] = MongoID($pool_cat);
                                                      
													}
												}else{ 
													$categoryID = $category;
												}
												if($category == POOL_ID){      
													$category_drivers = $ci->app_model->get_nearest_pool_driver($coordinates, $categoryID, $limit,'',$requested_drivers,"",$location_id,$genderPref);
												}
                                                
												if (empty($category_drivers['result'])) {  
													$category_drivers = $ci->app_model->get_nearest_driver($coordinates, $categoryID, $limit,'',$requested_drivers,"",$location_id,'',$genderPref);
													if (empty($category_drivers['result'])) {
														$category_drivers = $ci->app_model->get_nearest_driver($coordinates, $categoryID, $limit * 2,'',$requested_drivers,"",$location_id,'',$genderPref);
													}
												}
												
												if(!empty($category_drivers['result']) || $type == 1){
													$push_drivers = array();
													foreach ($category_drivers['result'] as $driver) {
														if (isset($driver['push_notification'])) {
															$d_id=(string)$driver['_id'];
															array_push($requested_drivers,$d_id);
															if ($driver['push_notification']['type'] == 'ANDROID') {
																if (isset($driver['push_notification']['key']) && $driver['push_notification']['key'] != '') {
																	$k = $driver['push_notification']['key'];
																	$push_drivers[$k] = array('id' => $driver['_id'],
																											'driver_loc' =>  $driver['loc'],
																											'messaging_status' => $driver['messaging_status'],
																											'distance' => $driver['distance'],
																											'device_type' => 'ANDROID'
																									);
																}
															}
															if ($driver['push_notification']['type'] == 'IOS') {
																if (isset($driver['push_notification']['key']) && $driver['push_notification']['key'] != '') {
																	$k = $driver['push_notification']['key'];
																	$push_drivers[$k] = array('id' => $driver['_id'],
																												'driver_loc' =>  $driver['loc'],
																												'messaging_status' => $driver['messaging_status'],
																												'distance' => $driver['distance'],
																												'device_type' => 'IOS'
																										);
																}
															}
														}
													}
													
													$checkCode = $ci->app_model->get_all_details(PROMOCODE, array('promo_code' => $code));
													$code_used = 'No';
													$coupon_type = '';
													$coupon_amount = '';
													if ($checkCode->num_rows() > 0) {
														$code_used = 'Yes';
														$coupon_type = $checkCode->row()->code_type;
														$coupon_amount = $checkCode->row()->promo_value;
													}
													$site_commission = 0;
													if (isset($location['result'][0]['site_commission'])) {
														if ($location['result'][0]['site_commission'] > 0) {
															$site_commission = $location['result'][0]['site_commission'];
														}
													}
													
													#$currencyCode=$location['result'][0]['currency'];
													$currencyCode = $ci->data['dcurrencyCode'];
													
													$distance_unit = $ci->data['d_distance_unit'];
													if(isset($location['result'][0]['distance_unit'])){
														$distance_unit = $location['result'][0]['distance_unit'];
													}
													if($site_commission>100) $site_commission = 100;
													$ride_id = $ci->app_model->get_ride_id();
													$requested_drivers_final = array_unique($requested_drivers);
													
													if($genderPref!=FALSE) $driver_preference = $genderPref; else $driver_preference = "";
													
													$bookingRecord = array('ride_id' => (string) $ride_id,
																								'type' => $ride_type,
																								'booking_ref' =>$platform,
																								'currency' => $currencyCode,
																								'commission_percent' => $site_commission,
																								'location' => array('id' => (string) $location_id,
																									'name' => $location['result'][0]['city']
																								),
																								'user' => array('id' => (string) $checkUser->row()->_id,
																									'name' => $checkUser->row()->user_name,
																									'email' => $checkUser->row()->email,
																									'phone' => $checkUser->row()->country_code . $checkUser->row()->phone_number
																								),
																								'driver' => array('id' => '',
																									'name' => '',
																									'email' => '',
																									'phone' => ''
																								),
																								'total' => array('fare' => '',
																									'distance' => '',
																									'ride_time' => '',
																									'wait_time' => ''
																								),
																								'fare_breakup' => array('min_km' => '',
																									'min_time' => '',
																									'min_fare' => '',
																									'per_km' => '',
																									'per_minute' => '',
																									'wait_per_minute' => '',
																									'peak_time_charge' => '',
																									'night_charge' => '',
																									'distance_unit' => $distance_unit,
																									'duration_unit' => 'min',
																								),
																								'tax_breakup' => array('service_tax' => ''),
																								'booking_information' => array(
																										'service_type' => $serviceArr["service_type"],
																										'service_id' => (string) $serviceArr["service_id"],
																										'booking_date' => MongoDATE(time()),
																										'pickup_date' => '',
																										'actual_pickup_date' => MongoDATE($pickup_timestamp),
																										'est_pickup_date' => MongoDATE($pickup_timestamp),
																										'booking_email' => $checkUser->row()->email,
																										'pickup' => array('location' => $pickup,
																																'latlong' => array('lon' => floatval($pickup_lon),
																																						'lat' => floatval($pickup_lat)
																																						)
																															),
																										'drop' => array('location' => (string)$drop_loc,
																															'latlong' => array('lon' => floatval($drop_lon),
																																					'lat' => floatval($drop_lat)
																																					)
																														)
																								),
																								'ride_status' => 'Booked',
																								'coupon_used' => $code_used,
																								'coupon' => array('code' => $code,
																																'type' => $coupon_type,
																																'amount' => floatval($coupon_amount)
																															),
																								'requested_drivers'=>$requested_drivers_final,
																								'driver_preference'=>$driver_preference,
																								'booking_source'=>$booking_source,
																								'booked_by'=>$booked_by,
																							);
                                                                                            
                                        #echo "<pre>";print_R($bookingRecord->result());die;
                                                    
													$pooling_response = array();
													if($share=="Yes"){
														$pool_id = $ride_id;
														
														$from = $pickup_lat . ',' . $pickup_lon;
														$to = $drop_lat . ',' . $drop_lon;
                                                        $url = 'https://maps.googleapis.com/maps/api/directions/json?origin=' . $from . '&destination=' . $to . '&alternatives=false&sensor=false&mode=driving'.$ci->data['google_maps_api_key'];
														$gmap = file_get_contents($url);
														$map_values = json_decode($gmap);
														$routes = $map_values->routes;
														if(!empty($routes)){
															usort($routes, create_function('$a,$b', 'return intval($a->legs[0]->distance->value) - intval($b->legs[0]->distance->value);'));
															$pickup = (string) $routes[0]->legs[0]->start_address;
															$drop = (string) $routes[0]->legs[0]->end_address;															
															$min_distance = $routes[0]->legs[0]->distance->text;
															if (preg_match('/km/',$min_distance)){
																$return_distance = 'km';
															}else if (preg_match('/mi/',$min_distance)){
																$return_distance = 'mi';
															}else if (preg_match('/m/',$min_distance)){
																$return_distance = 'm';
															} else {
																$return_distance = 'km';
															}
															
															$mindistance = floatval(str_replace(',','',$min_distance));
															if($distance_unit!=$return_distance){
																if($distance_unit=='km' && $return_distance=='mi'){
																	$mindistance = $mindistance * 1.60934;
																} else if($distance_unit=='mi' && $return_distance=='km'){
																	$mindistance = $mindistance * 0.621371;
																} else if($distance_unit=='km' && $return_distance=='m'){
																	$mindistance = $mindistance / 1000;
																} else if($distance_unit=='mi' && $return_distance=='m'){
																	$mindistance = $mindistance * 0.00062137;
																}
															}
															$mindistance = floatval(round($mindistance,2));
															$minduration = round(($routes[0]->legs[0]->duration->value) / 60);
															$ci->load->helper('pool_helper');
															$poolFareResponse = get_pool_fare($mindistance,$minduration,$location_id);                                                          
															if($poolFareResponse["status"]=="1"){
																$est_amount = 0;
																$tax_amount = 0;
																if($no_of_seat==1){
																	$est_amount = $poolFareResponse["passenger"];
																}else if($no_of_seat==2){
																	$est_amount = $poolFareResponse["co_passenger"];
																}
																if($est_amount > 0){																	
																	$tax_amount = ($est_amount*0.01*$poolFareResponse["tax_percent"]);
																}
																$pool_fare = array("est"=>(string)number_format($est_amount, 2,'.',''),
																							"tax"=>(string)number_format($tax_amount, 2,'.',''),
																							"tax_percent"=>(string)$poolFareResponse["tax_percent"],
																							"base_fare"=>(string)$poolFareResponse["base_fare"],
																							"single_percent"=>(string)$poolFareResponse["single_percent"],
																							"double_percent"=>(string)$poolFareResponse["double_percent"],
																							"passanger"=>(string)number_format($poolFareResponse["passenger"], 2,'.',''),
																							"co_passanger"=>(string)number_format($poolFareResponse["co_passenger"], 2,'.',''),
																						);
																$pooling_with = array();
																$co_rider = array();
																$pool_type = 0;
															}
																
															$poolRecord = array("pool_ride"=>(string)"Yes",
																							"pool_id"=>(string)$pool_id,
																							"no_of_seat"=>(string)$no_of_seat,
																							"pool_fare"=>$pool_fare,
																							"pooling_with"=>$pooling_with,
																							"co_rider"=>$co_rider,
																							"pool_type"=>(string)$pool_type,
																						);
															$bookingRecord = array_merge($bookingRecord,$poolRecord);
														
															$pooling_response = array("share_ride"=>"Yes",
																											"pool_fare"=>(string)number_format($est_amount, 2,'.',''),
																											"currency"=>(string)$currencyCode
																										);
                                                                                                        
														}
													}
													
													$ci->app_model->simple_insert(RIDES, $bookingRecord);
													$last_insert_id = $ci->mongo_db->insert_id();
													
													if ($type == 0) {
														$response_time = $ci->config->item('respond_timeout');
														$options = array($ride_id, $response_time, $pickup,$drop_loc,(string)time());
														if (!empty($push_drivers)) {
															foreach ($push_drivers as $keys => $value) {
																$driver_id = $value['id']; 
																$driver_Msg = $ci->format_string("Request for pickup user","request_pickup_user", '', 'driver', (string)$driver_id);
																
																$reqHisArr =array('driver_id'=> MongoID($driver_id),
																							'driver_loc'=> $value['loc'],
																							'requested_time'=>MongoDATE(time()),
																							'distance'=> $value['distance'],
																							'ride_id'=> (string)$ride_id,
																							'status'=>'sent',
																							'device_type' => $value['device_type'],
																							'messaging_status' => $value['messaging_status']
																						 );
																$ci->app_model->simple_insert(RIDE_REQ_HISTORY, $reqHisArr);
																$ack_id = $ci->mongo_db->insert_id();
																$options[6]= (string) $ack_id;
																
																$ci->sendPushNotification($keys, $driver_Msg , 'ride_request', $value['device_type'], $options, 'DRIVER');
																$condition = array('_id' => MongoID($driver_id));
																$ci->mongo_db->where($condition)->inc('req_received', 1)->update(DRIVERS);
															}
														}
														
														$message = $ci->format_string("Searching for a driver", "searching_for_a_driver");
													}else{
														$message = $ci->format_string("Your booking has been accepted, Our driver information will be notified before 30 minutes of your booking time.", "your_booking_has_been_accepted");
													}
													if (isset($response_time)) {
														if ($response_time <= 0) {
															$response_time = 10;
														}
													} else {
														$response_time = 10;
													}
													if (empty($riderlocArr)) {
														$riderlocArr = json_decode("{}");
													}
													
													$final_response_time = ($response_time*3) +10;
													$retry_time = $response_time + 4;

													$returnArr['status'] = '1';
													$returnArr['response'] = array('type' => (string) $type, 
																									'response_time' => (string) $final_response_time,
																									'retry_time' => (string)$retry_time,
																									'ride_id' => (string) $ride_id, 
																									'message' => $message, 
																									'rider_location' => $riderlocArr
																								);
													if($share=="Yes"){
														$returnArr['response'] = array_merge($returnArr['response'],$pooling_response);
													}
													create_and_save_travel_path_in_map($ride_id);
												}else{
													$returnArr['response'] = $ci->format_string('No cabs available nearby', 'cabs_not_available_nearby');
												}
												
											}else{
												if ($share=="Yes" && $no_of_seat <= 0){
													$returnArr['response'] = $ci->format_string('Choose a number of seating', 'choose_number_of_seatings');
												}else{
													$returnArr['response'] = $ci->format_string('Sorry ! We do not provide services in your city yet.', 'service_unavailable_in_your_city');
												}
												
											}
											
											
										}else{
											$returnArr['response'] = $ci->format_string('Sorry ! We do not provide services in your city yet.', 'service_unavailable_in_your_city');
										}
									} else {
										$returnArr['response'] = $ci->format_string('Sorry ! We do not provide services in your city yet.', 'service_unavailable_in_your_city');
									}
								} else {
									$returnArr['response'] = $ci->format_string('Cannot recognize your device', 'cannot_recognise_device');
								}
							} else {
								$returnArr['response'] = $ci->format_string("Invalid User", "invalid_user");
							}
							
						} else {
							$returnArr['response'] = $ci->format_string("Some Parameters Missing", "some_parameters_missing");
						}
					} else {
						$returnArr['status'] = '1';
						$returnArr['acceptance'] = $acceptance;

						$checkDriver = $ci->app_model->get_selected_fields(DRIVERS, array('_id' => MongoID($driver_id)), array('_id', 'driver_name', 'image', 'avg_review', 'email', 'dail_code', 'mobile_number', 'vehicle_number', 'vehicle_model'));
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
						$vehicleInfo = $ci->app_model->get_selected_fields(MODELS, array('_id' => MongoID($checkDriver->row()->vehicle_model)), array('_id', 'name', 'brand_name'));
						$vehicle_model = '';
						if ($vehicleInfo->num_rows() > 0) {
							$vehicle_model = $vehicleInfo->row()->name;
						}

						$driver_profile = array('driver_id' => (string) $checkDriver->row()->_id,
							'driver_name' => (string) $checkDriver->row()->driver_name,
							'driver_email' => (string) $checkDriver->row()->email,
							'driver_image' => (string) base_url() . $driver_image,
							'driver_review' => (string) floatval($driver_review),
							'driver_lat' => floatval($driver_lat),
							'driver_lon' => floatval($driver_lon),
							'min_pickup_duration' => $mindurationtext,
							'ride_id' => (string) $ride_id,
							'phone_number' => (string) $checkDriver->row()->dail_code . $checkDriver->row()->mobile_number,
							'vehicle_number' => (string) $checkDriver->row()->vehicle_number,
							'vehicle_model' => (string) $vehicle_model
						);
						/* Preparing driver information to share with user -- End */

						if (empty($driver_profile)) {
							$driver_profile = json_decode("{}");
						}
						if (empty($riderlocArr)) {
							$riderlocArr = json_decode("{}");
						}
						$returnArr['response'] = array('type' => (string) $type, 
																				'ride_id' => (string) $ride_id,
																				'message' => $ci->format_string('ride confirmed', 'ride_confirmed'),
																				'driver_profile' => $driver_profile,
																				'rider_location' => $riderlocArr
																			);
						if(isset($checkRide->row()->pool_ride)){
							if($checkRide->row()->pool_ride=="Yes"){
								$returnArr['response']['currency'] = (string)$checkRide->row()->currency;
								$returnArr['response']['share_ride'] = 'Yes';
								$returnArr['response']['pool_fare'] = (string)$checkRide->row()->pool_fare["est"];
							}
						}
					}
					
				}else{
					$returnArr['response'] = $ci->format_string("You can book ride only after one hour from now", "after_one_from_now");
				}
			} catch (MongoException $ex) {
				$returnArr['response'] = $ci->format_string("Error in connection", "error_in_connection");
			}
			$returnArr['acceptance'] = $acceptance;
			
			if($platform == 'website' || $platform == 'operator'){
				return $returnArr;
			}

			$json_encode = json_encode($returnArr, JSON_PRETTY_PRINT);
			echo $ci->cleanString($json_encode);
		}
	}
		
	/**
	*
	*	This function accept a booking by the driver
	*	Param @bookingInfo as Array
	*	Holds all the acceptance information Eg. Driver Id, ride id, latitude, longtitude and distance
	*
	**/	
	if ( ! function_exists('request_retry')){
		function request_retry($retryInfo= array()) {
			$ci =& get_instance();
			
			$returnArr['status'] = '0';
			$returnArr['response'] = '';
			$returnArr['ride_view'] = 'stay';
			
			try {
				if(array_key_exists("user_id",$retryInfo)) $user_id =  trim($retryInfo['user_id']); else $user_id = "";
				if(array_key_exists("ride_id",$retryInfo)) $ride_id =  trim($retryInfo['ride_id']); else $ride_id = "";
				
				if($user_id!='' && $ride_id!=''){
					$checkUser = $ci->app_model->get_selected_fields(USERS, array('_id' => MongoID($user_id)), array('email'));
					if ($checkUser->num_rows() == 1) {
						$checkRide = $ci->app_model->get_all_details(RIDES, array('user.id' => $user_id,'ride_id' => $ride_id));
						if ($checkRide->num_rows() == 1) {
							if ($checkRide->row()->ride_status != 'Booked') {
								$returnArr['acceptance'] = 'Yes';
								
								$driver_id = $checkRide->row()->driver['id'];
								$mindurationtext = '';
								if (isset($checkRide->row()->driver['est_eta'])) {
									$mindurationtext = $checkRide->row()->driver['est_eta'] . '';
								}
								$lat_lon = @explode(',', $checkRide->row()->driver['lat_lon']);
								$driver_lat = $lat_lon[0];
								$driver_lon = $lat_lon[1];

								$checkDriver = $ci->app_model->get_selected_fields(DRIVERS, array('_id' => MongoID($driver_id)), array('_id', 'driver_name', 'image', 'avg_review', 'email', 'dail_code', 'mobile_number', 'vehicle_number', 'vehicle_model'));
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
								$vehicleInfo = $ci->app_model->get_selected_fields(MODELS, array('_id' => MongoID($checkDriver->row()->vehicle_model)), array('_id', 'name', 'brand_name'));
								$vehicle_model = '';
								if ($vehicleInfo->num_rows() > 0) {
									$vehicle_model = $vehicleInfo->row()->name;
								}

								$driver_profile = array('driver_id' => (string) $checkDriver->row()->_id,
									'driver_name' => (string) $checkDriver->row()->driver_name,
									'driver_email' => (string) $checkDriver->row()->email,
									'driver_image' => (string) base_url() . $driver_image,
									'driver_review' => (string) floatval($driver_review),
									'driver_lat' => floatval($driver_lat),
									'driver_lon' => floatval($driver_lon),
									'min_pickup_duration' => $mindurationtext,
									'ride_id' => (string) $ride_id,
									'phone_number' => (string) $checkDriver->row()->dail_code . $checkDriver->row()->mobile_number,
									'vehicle_number' => (string) $checkDriver->row()->vehicle_number,
									'vehicle_model' => (string) $vehicle_model
								);
								/* Preparing driver information to share with user -- End */
								if (empty($driver_profile)) {
									$driver_profile = json_decode("{}");
								}
								if (empty($riderlocArr)) {
									$riderlocArr = json_decode("{}");
								}
								$returnArr['response'] = array('type' => (string)0, 
																				'ride_id' => (string) $ride_id, 
																				'message' => $ci->format_string('ride confirmed', 'ride_confirmed'), 
																				'driver_profile' => $driver_profile, 
																				'rider_location' => $riderlocArr
																			);
							}else{
								$limit = 10;
								$pickup_location = $checkRide->row()->booking_information['pickup']['location'];
								$pickup_lat = $checkRide->row()->booking_information['pickup']['latlong']['lat'];
								$pickup_lon = $checkRide->row()->booking_information['pickup']['latlong']['lon'];
								
								$drop_location = $checkRide->row()->booking_information['drop']['location'];
								
								$category = $checkRide->row()->booking_information['service_id'];
								
								$location_id = $checkRide->row()->location['id'];
										
								$coordinates = array(floatval($pickup_lon), floatval($pickup_lat));
								$requested_drivers = array();
								if(isset($checkRide->row()->requested_driver)){
									$requested_drivers = $checkRide->row()->requested_driver;
								}
								
								$location = $ci->app_model->find_location(floatval($pickup_lon), floatval($pickup_lat));
								if (!empty($location['result'])) {
									$requested_drivers = array();
									if($category == POOL_ID){
										$categoryID = array();
										foreach($location['result'][0]['pool_categories'] as $pool_cat){
											$categoryID[] = MongoID($pool_cat);
										}
									}else{
										$categoryID = $category;
									}
									if($category == POOL_ID){
										$category_drivers = $ci->app_model->get_nearest_pool_driver($coordinates, $categoryID, $limit,'',$requested_drivers,"",$location_id);
									}
									if(empty($category_drivers['result'])){
										$category_drivers = $ci->app_model->get_nearest_driver($coordinates, $categoryID, $limit,'',$requested_drivers,"",$location_id);
										if (empty($category_drivers['result'])) {
											$category_drivers = $ci->app_model->get_nearest_driver($coordinates, $categoryID, $limit * 2,'',$requested_drivers,"",$location_id);
										}
									}
								}
								
								$push_drivers = array();
								foreach ($category_drivers['result'] as $driver) {
									if (isset($driver['push_notification'])) {
										$d_id=(string)$driver['_id'];
										array_push($requested_drivers,$d_id);
										if ($driver['push_notification']['type'] == 'ANDROID') {
											if (isset($driver['push_notification']['key']) && $driver['push_notification']['key'] != '') {
												$k = $driver['push_notification']['key'];
												$push_drivers[$k] = array('id' => $driver['_id'],
																						'driver_loc' =>  $driver['loc'],
																						'messaging_status' => $driver['messaging_status'],
																						'distance' => $driver['distance'],
																						'device_type' => 'ANDROID'
																				);
											}
										}
										if ($driver['push_notification']['type'] == 'IOS') {
											if (isset($driver['push_notification']['key']) && $driver['push_notification']['key'] != '') {
												$k = $driver['push_notification']['key'];
												$push_drivers[$k] = array('id' => $driver['_id'],
																						'driver_loc' =>  $driver['loc'],
																						'messaging_status' => $driver['messaging_status'],
																						'distance' => $driver['distance'],
																						'device_type' => 'IOS'
																				);
											}
										}
									}
								}
								
								$response_time = $ci->config->item('respond_timeout');
								$options = array($ride_id, $response_time, $pickup_location, $drop_location,(string)time());
								if (!empty($push_drivers)) {
									foreach ($push_drivers as $keys => $value) {
										$driver_id = $value['id']; 
										$driver_Msg = $ci->format_string("Request for pickup user","request_pickup_user", '', 'driver', (string)$driver_id);
										
										$reqHisArr =array('driver_id'=> MongoID($driver_id),
																	'driver_loc'=> $value['loc'],
																	'requested_time'=>MongoDATE(time()),
																	'distance'=> $value['distance'],
																	'ride_id'=> (string)$ride_id,
																	'status'=>'sent',
																	'device_type' => $value['device_type'],
																	'messaging_status' => $value['messaging_status']
																 );
										$ci->app_model->simple_insert(RIDE_REQ_HISTORY, $reqHisArr);
										$ack_id = $ci->mongo_db->insert_id();
										$options[6]= (string) $ack_id;
										
										$ci->sendPushNotification($keys, $driver_Msg , 'ride_request', $value['device_type'], $options, 'DRIVER');
										$condition = array('_id' => MongoID($driver_id));
										$ci->mongo_db->where($condition)->inc('req_received', 1)->update(DRIVERS);
									}
								}
								
								$requested_drivers_final = array_unique($requested_drivers);
								$ci->app_model->update_details(RIDES, array("requested_drivers"=>$requested_drivers_final), array('ride_id' => $ride_id));
								$returnArr['response'] = $ci->format_string("Searching for a driver", "searching_for_a_driver");
							}
							
							$returnArr['status'] = '1';
						}else{
							$returnArr['response'] = $ci->format_string("Invalid Ride", "invalid_ride");
						}
					}else{
						$returnArr['response'] = $ci->format_string("Driver not found", "driver_not_found");
					}
				}else{
					$returnArr['response'] = $ci->format_string("Some Parameters Missing", "some_parameters_missing");
				}
			} catch (MongoException $ex) {				
				$returnArr['response'] = $ci->format_string("Error in connection", "error_in_connection");
			}

			$json_encode = json_encode($returnArr, JSON_PRETTY_PRINT);
			echo $ci->cleanString($json_encode);
		}
	}
	
	
	/**
	*
	*	This function delete a booking request by user
	*	Param @deleteInfo as Array
	*	Returns all the acceptance information Eg. Driver Id, ride id, latitude, longtitude and distance
	*
	**/	
	if ( ! function_exists('request_delete')){
		function request_delete($deleteInfo= array()) {
			$ci =& get_instance();
			
			$returnArr['status'] = '0';
			$returnArr['response'] = '';
			$returnArr['acceptance'] = 'No';
			
			try {
				if(array_key_exists("user_id",$deleteInfo)) $user_id =  trim($deleteInfo['user_id']); else $user_id = "";
				if(array_key_exists("ride_id",$deleteInfo)) $ride_id =  trim($deleteInfo['ride_id']); else $ride_id = "";
				
				if(array_key_exists("mode",$deleteInfo)) $mode =  trim($deleteInfo['mode']); else $mode = "";
				
				if($user_id!='' && $ride_id!=''){
					$checkUser = $ci->app_model->get_selected_fields(USERS, array('_id' => MongoID($user_id)), array('email'));
					if ($checkUser->num_rows() == 1) {
						$checkRide = $ci->app_model->get_all_details(RIDES, array('user.id' => $user_id,'ride_id' => $ride_id));
						if ($checkRide->num_rows() == 1) {
							$ride_status = $checkRide->row()->ride_status;
							$driver_id = (string)$checkRide->row()->driver["id"];
							$hasDrivers = array("Confirmed","Arrived","Onride","Finished","Completed","Cancelled");
							if (in_array($ride_status,$hasDrivers) && $driver_id!="") {
								$returnArr['acceptance'] = 'Yes';
								$mindurationtext = '';
								if (isset($checkRide->row()->driver['est_eta'])) {
									$mindurationtext = $checkRide->row()->driver['est_eta'] . '';
								}
								$lat_lon = @explode(',', $checkRide->row()->driver['lat_lon']);
								$driver_lat = $lat_lon[0];
								$driver_lon = $lat_lon[1];

								$checkDriver = $ci->app_model->get_selected_fields(DRIVERS, array('_id' => MongoID($driver_id)), array('_id', 'driver_name', 'image', 'avg_review', 'email', 'dail_code', 'mobile_number', 'vehicle_number', 'vehicle_model'));
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
								$vehicleInfo = $ci->app_model->get_selected_fields(MODELS, array('_id' => MongoID($checkDriver->row()->vehicle_model)), array('_id', 'name', 'brand_name'));
								$vehicle_model = '';
								if ($vehicleInfo->num_rows() > 0) {
									$vehicle_model = $vehicleInfo->row()->name;
								}

								$driver_profile = array('driver_id' => (string) $checkDriver->row()->_id,
									'driver_name' => (string) $checkDriver->row()->driver_name,
									'driver_email' => (string) $checkDriver->row()->email,
									'driver_image' => (string) base_url() . $driver_image,
									'driver_review' => (string) floatval($driver_review),
									'driver_lat' => floatval($driver_lat),
									'driver_lon' => floatval($driver_lon),
									'min_pickup_duration' => $mindurationtext,
									'ride_id' => (string) $ride_id,
									'phone_number' => (string) $checkDriver->row()->dail_code . $checkDriver->row()->mobile_number,
									'vehicle_number' => (string) $checkDriver->row()->vehicle_number,
									'vehicle_model' => (string) $vehicle_model
								);
								/* Preparing driver information to share with user -- End */
								if (empty($driver_profile)) {
									$driver_profile = json_decode("{}");
								}
								if (empty($riderlocArr)) {
									$riderlocArr = json_decode("{}");
								}
								$returnArr['response'] = array('type' => (string)0, 
																			'ride_id' => (string) $ride_id, 
																			'message' => $ci->format_string('ride confirmed', 'ride_confirmed'), 
																			'driver_profile' => $driver_profile, 
																			'rider_location' => $riderlocArr
																		);
							}else{
								
								
								
								/* Saving Unaccepted Ride for future reference */
								save_ride_details_for_stats($ride_id);
								/* Saving Unaccepted Ride for future reference */
								
								$rideArr = $checkRide->result_array();
								$dataArr = $rideArr[0]; unset($dataArr['_id']);
								$ci->app_model->simple_insert(MISSED_RIDES,$dataArr);
								
								$ci->app_model->commonDelete(RIDES, array('ride_id' => $ride_id));
								$returnArr['response'] = $ci->format_string('Ride request cancelled', 'ride_request_cancelled');
								
								if($mode=="auto"){
									$returnArr['response'] = $ci->format_string('No cabs available nearby', 'cabs_not_available_nearby');
								}
								
							}
							$returnArr['status'] = '1';
						}else{
							$returnArr['response'] = $ci->format_string("This ride is unavailable", "ride_unavailable");
						}
					}else{
						$returnArr['response'] = $ci->format_string("Invalid User", "invalid_user");
					}
				}else{
					$returnArr['response'] = $ci->format_string("Some Parameters Missing", "some_parameters_missing");
				}
			} catch (MongoException $ex) {				
				$returnArr['response'] = $ci->format_string("Error in connection", "error_in_connection");
			}

			$json_encode = json_encode($returnArr, JSON_PRETTY_PRINT);
			echo $ci->cleanString($json_encode);
		}
	}
	
	/**
	*
	*	This function accept a booking by the driver
	*	Param @bookingInfo as Array
	*	Holds all the acceptance information Eg. Driver Id, ride id, latitude, longtitude and distance
	*
	**/	
	if ( ! function_exists('accepting_ride')){
		function accepting_ride($acceptanceInfo= array()) {
			$ci =& get_instance();
			
			$returnArr['status'] = '0';
			$returnArr['response'] = '';
			$returnArr['ride_view'] = 'stay';
			
			try {
				if(array_key_exists("driver_id",$acceptanceInfo)) $driver_id =  trim($acceptanceInfo['driver_id']); else $driver_id = "";
				if(array_key_exists("ride_id",$acceptanceInfo)) $ride_id =  trim($acceptanceInfo['ride_id']); else $ride_id = "";
				if(array_key_exists("driver_lat",$acceptanceInfo)) $driver_lat =  trim($acceptanceInfo['driver_lat']); else $driver_lat = "";
				if(array_key_exists("driver_lon",$acceptanceInfo)) $driver_lon =  trim($acceptanceInfo['driver_lon']); else $driver_lon = "";
				if(array_key_exists("distance",$acceptanceInfo)) $distance =  trim($acceptanceInfo['distance']); else $distance = 0;
				if(array_key_exists("ack_id",$acceptanceInfo)) $ack_id =  trim($acceptanceInfo['ack_id']); else $ack_id = '';
				
				if ($driver_id!="" && $ride_id!="" && $driver_lat!="" && $driver_lon!="" && $distance!="") {
					$checkDriver = $ci->app_model->get_selected_fields(DRIVERS, array('_id' => MongoID($driver_id)), array('_id', 'driver_name', 'image', 'avg_review', 'email', 'dail_code', 'mobile_number', 'vehicle_number', 'vehicle_model', 'driver_commission','company_id', 'operator_id','last_online_time','ride_type','mode','duty_ride'));
					if ($checkDriver->num_rows() == 1) {
						$company_id = '';
						if(isset($checkDriver->row()->company_id) && $checkDriver->row()->company_id != ''){
							$company_id = $checkDriver->row()->company_id;
						}
						$operator_id = '';
						if(isset($checkDriver->row()->operator_id) && $checkDriver->row()->operator_id != ''){
							$operator_id = $checkDriver->row()->operator_id;
						}
						$checkRide = $ci->app_model->get_all_details(RIDES, array('ride_id' => $ride_id));
						if ($checkRide->num_rows() == 1) {
							$isGo = FALSE;
							if ($checkDriver->row()->ride_type== 'Normal' && $checkDriver->row()->mode== 'Available'){
									$isGo = TRUE;
							}
							if ($checkDriver->row()->ride_type== 'Share'){
									$isGo = TRUE;
							}
							if ($checkRide->row()->ride_status == 'Booked' || $checkRide->row()->driver['id'] == $driver_id) {
								if ($checkRide->row()->ride_status == 'Booked') {
									$userVal = $ci->app_model->get_selected_fields(USERS, array('_id' => MongoID($checkRide->row()->user['id'])), array('_id', 'user_name', 'email', 'image', 'avg_review', 'phone_number', 'country_code', 'push_type', 'push_notification_key'));
									if ($userVal->num_rows() > 0) {
										$service_id = $checkRide->row()->booking_information['service_id'];
										/* Update the ride information with fare and driver details -- Start */
										$pickup_lon = $checkRide->row()->booking_information['pickup']['latlong']['lon'];
										$pickup_lat = $checkRide->row()->booking_information['pickup']['latlong']['lat'];
										$from = $driver_lat . ',' . $driver_lon;
										$to = $pickup_lat . ',' . $pickup_lon;

										$urls = 'https://maps.googleapis.com/maps/api/directions/json?origin=' . $from . '&destination=' . $to . '&alternatives=true&sensor=false&mode=driving'.$ci->data['google_maps_api_key'];
										$gmap = file_get_contents($urls);
										$map_values = json_decode($gmap);
										$routes = $map_values->routes;
										if(!empty($routes)){
											usort($routes, create_function('$a,$b', 'return intval($a->legs[0]->distance->value) - intval($b->legs[0]->distance->value);'));
											
											$distance_unit = $ci->data['d_distance_unit'];
											$duration_unit = 'min';
											if(isset($checkRide->row()->fare_breakup)){
												if($checkRide->row()->fare_breakup['distance_unit']!=''){
													$distance_unit = $checkRide->row()->fare_breakup['distance_unit'];
													$duration_unit = $checkRide->row()->fare_breakup['duration_unit'];
												} 
											}

											$mindistance = 1;
											$minduration = 1;
											$mindurationtext = '';
											if (!empty($routes[0])) {
												#$mindistance = ($routes[0]->legs[0]->distance->value) / 1000;
												$min_distance = $routes[0]->legs[0]->distance->text;
												if (preg_match('/km/',$min_distance)){
													$return_distance = 'km';
												}else if (preg_match('/mi/',$min_distance)){
													$return_distance = 'mi';
												}else if (preg_match('/m/',$min_distance)){
													$return_distance = 'm';
												} else {
													$return_distance = 'km';
												}
												
												$mindistance = floatval(str_replace(',','',$min_distance));
												if($distance_unit!=$return_distance){
													if($distance_unit=='km' && $return_distance=='mi'){
														$mindistance = $mindistance * 1.60934;
													} else if($distance_unit=='mi' && $return_distance=='km'){
														$mindistance = $mindistance * 0.621371;
													} else if($distance_unit=='km' && $return_distance=='m'){
														$mindistance = $mindistance / 1000;
													} else if($distance_unit=='mi' && $return_distance=='m'){
														$mindistance = $mindistance * 0.00062137;
													}
												}
												$mindistance = floatval(round($mindistance,2));
										
										
												$minduration = ($routes[0]->legs[0]->duration->value) / 60;
												$est_pickup_time = (time()) + $routes[0]->legs[0]->duration->value;
												#$est_pickup_time=MongoEPOCH($checkRide->row()->booking_information['est_pickup_date'])+$routes[0]->legs[0]->duration->value;
												$mindurationtext = $routes[0]->legs[0]->duration->text;
											}

											$fareDetails = $ci->app_model->get_all_details(LOCATIONS, array('_id' => MongoID($checkRide->row()->location['id'])));
											if ($fareDetails->num_rows() > 0) {
												$service_tax = 0.00;
												if (isset($fareDetails->row()->service_tax)) {
													if ($fareDetails->row()->service_tax > 0) {
														$service_tax = $fareDetails->row()->service_tax;
													}
												}
												if (isset($fareDetails->row()->fare[$service_id]) && $service_id!=POOL_ID) {
													$peak_time = '';
													$night_charge = '';
													$peak_time_amount = '';
													$night_charge_amount = '';
													$min_amount = 0.00;
													$max_amount = 0.00;
													$pickup_datetime = MongoEPOCH($checkRide->row()->booking_information['est_pickup_date']);
													$pickup_date = date('Y-m-d', MongoEPOCH($checkRide->row()->booking_information['est_pickup_date']));

													if ($fareDetails->row()->peak_time == 'Yes') {
														$time1 = strtotime($pickup_date . ' ' . $fareDetails->row()->peak_time_frame['from']);
														$time2 = strtotime($pickup_date . ' ' . $fareDetails->row()->peak_time_frame['to']);
														$ptc = FALSE;
														if ($time1 > $time2) {
															if (date('A', $pickup_datetime) == 'PM') {
																if (($time1 <= $pickup_datetime) && (strtotime('+1 day', $time2) >= $pickup_datetime)) {
																	$ptc = TRUE;
																}
															} else {
																if ((strtotime('-1 day', $time1) <= $pickup_datetime) && ($time2 >= $pickup_datetime)) {
																	$ptc = TRUE;
																}
															}
														} else if ($time1 < $time2) {
															if (($time1 <= $pickup_datetime) && ($time2 >= $pickup_datetime)) {
																$ptc = TRUE;
															}
														}
														if ($ptc) {
															$peak_time_amount = $fareDetails->row()->fare[$service_id]['peak_time_charge'];
														}
													}
													if ($fareDetails->row()->night_charge == 'Yes') {
														$time1 = strtotime($pickup_date . ' ' . $fareDetails->row()->night_time_frame['from']);
														$time2 = strtotime($pickup_date . ' ' . $fareDetails->row()->night_time_frame['to']);
														$nc = FALSE;
														if ($time1 > $time2) {
															if (date('A', $pickup_datetime) == 'PM') {
																if (($time1 <= $pickup_datetime) && (strtotime('+1 day', $time2) >= $pickup_datetime)) {
																	$nc = TRUE;
																}
															} else {
																if ((strtotime('-1 day', $time1) <= $pickup_datetime) && ($time2 >= $pickup_datetime)) {
																	$nc = TRUE;
																}
															}
														} else if ($time1 < $time2) {
															if (($time1 <= $pickup_datetime) && ($time2 >= $pickup_datetime)) {
																$nc = TRUE;
															}
														}
														if ($nc) {
															$night_charge_amount = $fareDetails->row()->fare[$service_id]['night_charge'];
														}
													}
													$fare_breakup = array('min_km' => (string) $fareDetails->row()->fare[$service_id]['min_km'],
														'min_time' => (string) $fareDetails->row()->fare[$service_id]['min_time'],
														'min_fare' => (string) $fareDetails->row()->fare[$service_id]['min_fare'],
														'per_km' => (string) $fareDetails->row()->fare[$service_id]['per_km'],
														'per_minute' => (string) $fareDetails->row()->fare[$service_id]['per_minute'],
														'wait_per_minute' => (string) $fareDetails->row()->fare[$service_id]['wait_per_minute'],
														'peak_time_charge' => (string) $peak_time_amount,
														'night_charge' => (string) $night_charge_amount,
														'distance_unit' => (string) $distance_unit,
														'duration_unit' => (string) $duration_unit
													);
												}
											}
											$vehicleInfo = $ci->app_model->get_selected_fields(MODELS, array('_id' => MongoID($checkDriver->row()->vehicle_model)), array('_id', 'name', 'brand_name'));
											$vehicle_model = '';
											if ($vehicleInfo->num_rows() > 0) {
												$vehicle_model = $vehicleInfo->row()->name;
												#$vehicle_model=$vehicleInfo->row()->brand_name.' '.$vehicleInfo->row()->name;
											}
											$driverInfo = array('id' => (string) $checkDriver->row()->_id,
												'name' => (string) $checkDriver->row()->driver_name,
												'email' => (string) $checkDriver->row()->email,
												'phone' => (string) $checkDriver->row()->dail_code . $checkDriver->row()->mobile_number,
												'vehicle_model' => (string) $vehicle_model,
												'vehicle_no' => (string) $checkDriver->row()->vehicle_number,
												'lat_lon' => (string) $driver_lat . ',' . $driver_lon,
												'est_eta' => (string) $mindurationtext
											);
											$history = array('booking_time' => $checkRide->row()->booking_information['booking_date'],
												'estimate_pickup_time' => MongoDATE($est_pickup_time),
												'driver_assigned' => MongoDATE(time())
											);
											$driver_commission = $checkRide->row()->commission_percent;
											if (isset($checkDriver->row()->driver_commission)) {
												$driver_commission = $checkDriver->row()->driver_commission;
											}
											$curr_duty_ride = $ride_id;
											if(isset($checkDriver->row()->duty_ride)){
												if($checkDriver->row()->duty_ride!="") $curr_duty_ride = $checkDriver->row()->duty_ride;
											}
											
											if($driver_commission>100) $driver_commission = 100;

											$rideDetails = array('ride_status' => 'Confirmed',
												'commission_percent' => floatval($driver_commission),
												'driver' => $driverInfo,
												'company_id'=>$company_id,
												'operator_id'=>$operator_id,
												'tax_breakup' => array('service_tax' => $service_tax),
												'booking_information.est_pickup_date' => MongoDATE($est_pickup_time),
												'history' => $history
											);
											$active_trips = 0;
											if($service_id!=POOL_ID){
												$active_trips = 1;
												$rideDetails['fare_breakup'] = $fare_breakup;
											}else if($service_id==POOL_ID){
												$pooling_with = array(); $co_rider = array(); $pool_type = 0;
												
												$checkAvailRide = $ci->app_model->get_driver_active_trips($driver_id,$curr_duty_ride,"Share");
												if($checkAvailRide->num_rows()>0){
													$active_trips = intval($checkAvailRide->num_rows());
												}
												if($active_trips>=1){
													$pool_type = 1;
													$active_trips ++;
													$pooling_with = array("name"=>$checkAvailRide->row()->user["name"],
																					"id"=>$checkAvailRide->row()->user["id"]
																				);
													$co_rider = array("name"=>$checkAvailRide->row()->user["name"],
																					"id"=>$checkAvailRide->row()->user["id"]
																				);
													$ext_pooling_with = array("name"=>$checkRide->row()->user["name"],
																					"id"=>$checkRide->row()->user["id"]
																				);
													$ext_co_rider = array("name"=>$checkRide->row()->user["name"],
																					"id"=>$checkRide->row()->user["id"]
																				);
													if(isset($checkAvailRide->row()->ride_id)){
														$ext_ride_id = (string)$checkAvailRide->row()->ride_id;
														if(isset($checkAvailRide->row()->pooling_with)){
															$ext_pooling_with = $ext_pooling_with;
														}
														if(isset($checkAvailRide->row()->co_rider)){
															$ext_co_rider_val = $checkAvailRide->row()->co_rider;
															$ext_co_rider_val[] = $ext_co_rider;
															$ext_co_rider = $ext_co_rider_val;
														}
														$ext_ride_Arr = array("pooling_with"=>$ext_pooling_with,"co_ride"=>$ext_co_rider);
														$ci->app_model->update_details(RIDES, $ext_ride_Arr, array('ride_id' => $ext_ride_id));
														
														/*	Sending the notification regarding the matching	*/
														$ext_user_id = $checkAvailRide->row()->user["id"];
														if($ext_user_id!=""){
															$extUserVal = $ci->app_model->get_selected_fields(USERS, array('_id' => MongoID($ext_user_id)), array('_id','push_type','push_notification_key'));
															if ($extUserVal->num_rows() > 0) {
																if (isset($extUserVal->row()->push_type)) {
																	if ($extUserVal->row()->push_type != '') {
																		$message = $ci->format_string('Ride has been matched', 'ride_matched','','user',(string)$extUserVal->row()->_id);						
																		$optionsFExt = array('ride_id' => $ext_ride_id);
																		if ($extUserVal->row()->push_type == 'ANDROID') {
																			if (isset($extUserVal->row()->push_notification_key['gcm_id'])) {
																				if ($extUserVal->row()->push_notification_key['gcm_id'] != '') {
																					$ci->sendPushNotification($extUserVal->row()->push_notification_key['gcm_id'], $message, 'track_reload', 'ANDROID', $optionsFExt, 'USER');
																				}
																			}
																		}
																		if ($extUserVal->row()->push_type == 'IOS') {
																			if (isset($extUserVal->row()->push_notification_key['ios_token'])) {
																				if ($extUserVal->row()->push_notification_key['ios_token'] != '') {
																					$ci->sendPushNotification($extUserVal->row()->push_notification_key['ios_token'], $message, 'track_reload', 'IOS', $optionsFExt, 'USER');
																				}
																			}
																		}
																	}
																}
															}
														}
														/*	Sending the notification regarding the matching	*/
                                                        $ci->sms_model->sms_on_driver_accept($driverInfo,$ride_id);
													}
												}else{
													$active_trips = 1;
												}
												
												$rideDetails['pooling_with'] = $pooling_with;
												$rideDetails['co_rider'] = $co_rider;
												$rideDetails['pool_type'] = (string)$pool_type;
												$rideDetails['pool_id'] = (string)$curr_duty_ride;
											}
											$driverSRls = array("ride_type"=>"Normal",
																		"duty_ride"=>(string)$ride_id,
																		"share_drop"=>array()
																	);
											if(isset($checkRide->row()->pool_ride)){
												if($checkRide->row()->pool_ride=="Yes"){
													$share_drop = $checkRide->row()->booking_information['drop']['latlong'];
													$driverSRls = array("ride_type"=>"Share",
																				"duty_ride"=>(string)$curr_duty_ride,
																				"share_drop"=>$share_drop
																			);
												}
											}
											/* echo '<pre>'; print_r($ext_ride_Arr); 
											echo '<pre>'; print_r($driverSRls); 
											echo '<pre>'; print_r($rideDetails);  die; */
											$checkBooked = $ci->app_model->get_selected_fields(RIDES, array('ride_id' => $ride_id, 'ride_status' => 'Booked'), array('ride_id', 'ride_status'));
											$checkAvailable = $ci->app_model->get_selected_fields(DRIVERS, array('_id' => MongoID($driver_id)), array('mode','duty_ride','ride_type'));
											$availablity = false;
											if ($checkAvailable->row()->mode == 'Available') {
												$availablity = true;
											}else{
												$hasPool_ride = false;
												if(isset($checkAvailable->row()->duty_ride)){
													if ($checkAvailable->row()->duty_ride != '' && $checkAvailable->row()->ride_type == 'Share') {
														$hasPool_ride = true;
													}
												}
											}
											
											
											if ($checkBooked->num_rows() > 0 && ($availablity === true || $hasPool_ride == true)) {
												$ci->app_model->update_details(RIDES, $rideDetails, array('ride_id' => $ride_id));
												/* Update the ride information with fare and driver details -- End */

												
												/* Update the driver status to Booked */
												$driverSRls['mode'] = "Booked";
												$driverSRls['active_trips'] = intval($active_trips);
												$ci->app_model->update_details(DRIVERS, $driverSRls, array('_id' => MongoID($driver_id)));

												/* Update the no of rides  */
												$ci->app_model->update_user_rides_count('no_of_rides', $userVal->row()->_id);
												//$ci->app_model->update_driver_rides_count('no_of_rides', $driver_id);

												/* Update Stats Starts */
												$current_date = MongoDATE(strtotime(date("Y-m-d 00:00:00")));
												$field = array('ride_booked.hour_' . date('H') => 1, 'ride_booked.count' => 1);
												$ci->app_model->update_stats(array('day_hour' => $current_date), $field, 1);
												/* Update Stats End */


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
												$driver_profile = array('driver_id' => (string) $checkDriver->row()->_id,
													'driver_name' => (string) $checkDriver->row()->driver_name,
													'driver_email' => (string) $checkDriver->row()->email,
													'driver_image' => (string) base_url() . $driver_image,
													'driver_review' => (string) floatval($driver_review),
													'driver_lat' => floatval($driver_lat),
													'driver_lon' => floatval($driver_lon),
													'min_pickup_duration' => $mindurationtext,
													'ride_id' => $ride_id,
													'phone_number' => (string) $checkDriver->row()->dail_code . $checkDriver->row()->mobile_number,
													'vehicle_number' => (string) $checkDriver->row()->vehicle_number,
													'vehicle_model' => (string) $vehicle_model,
													'pickup_location' => (string) $checkRide->row()->booking_information['pickup']['location'],
													'pickup_lat' => (string) $pickup_lat,
													'pickup_lon' => (string) $pickup_lon
												);
												/* Preparing driver information to share with user -- End */


												/* Preparing user information to share with driver -- Start */
												if ($userVal->row()->image == '') {
													$user_image = USER_PROFILE_IMAGE_DEFAULT;
												} else {
													$user_image = USER_PROFILE_IMAGE . $userVal->row()->image;
												}
												$user_review = 0;
												if (isset($userVal->row()->avg_review)) {
													$user_review = $userVal->row()->avg_review;
												}
												$user_profile = array('user_id' => (string)$userVal->row()->_id,
													'user_name' => $userVal->row()->user_name,
													'user_email' => $userVal->row()->email,
													'phone_number' => (string) $userVal->row()->country_code . $userVal->row()->phone_number,
													'user_image' => base_url() . $user_image,
													'user_review' => floatval($user_review),
													'ride_id' => $ride_id,
													'pickup_location' => $checkRide->row()->booking_information['pickup']['location'],
													'pickup_lat' => $pickup_lat,
													'pickup_lon' => $pickup_lon,
													'pickup_time' => date("h:i A jS M, Y", MongoEPOCH($checkRide->row()->booking_information['est_pickup_date']))
												);
												/* Preparing user information to share with driver -- End */

												/* Sending notification to user regarding booking confirmation -- Start */
												# Push notification
												if (isset($userVal->row()->push_type)) {
													if ($userVal->row()->push_type != '') {
														$message = $ci->format_string('Your ride request confirmed', 'ride_request_confirmed','','user',(string)$userVal->row()->_id);
																	
														$options = $driver_profile;
														//print_r($options);
														if ($userVal->row()->push_type == 'ANDROID') {
															if (isset($userVal->row()->push_notification_key['gcm_id'])) {
																if ($userVal->row()->push_notification_key['gcm_id'] != '') {
																	$ci->sendPushNotification($userVal->row()->push_notification_key['gcm_id'], $message, 'ride_confirmed', 'ANDROID', $driver_profile, 'USER');
																}
															}
														}
														if ($userVal->row()->push_type == 'IOS') {
															if (isset($userVal->row()->push_notification_key['ios_token'])) {
																if ($userVal->row()->push_notification_key['ios_token'] != '') {
																	$ci->sendPushNotification($userVal->row()->push_notification_key['ios_token'], $message, 'ride_confirmed', 'IOS', $driver_profile, 'USER');
																}
															}
														}
													}
												}
												/* Sending notification to user regarding booking confirmation -- End */
												
												$drop_location = 0;
												$drop_loc = '';$drop_lat = '';$drop_lon = '';
												if($checkRide->row()->booking_information['drop']['location']!=''){
													$drop_location = 1;
													$drop_loc = $checkRide->row()->booking_information['drop']['location'];
													$drop_lat = $checkRide->row()->booking_information['drop']['latlong']['lat'];
													$drop_lon = $checkRide->row()->booking_information['drop']['latlong']['lon'];
												}
												$user_profile['drop_location'] = (string)$drop_location;
												$user_profile['drop_loc'] = (string)$drop_loc;
												$user_profile['drop_lat'] = (string)$drop_lat;
												$user_profile['drop_lon'] = (string)$drop_lon;
												
												
												if ($ride_id != '') {
													$checkInfo = $ci->app_model->get_all_details(TRACKING, array('ride_id' => $ride_id));
												
													$latlng = $driver_lat . ',' . $driver_lon;
													$gmap = file_get_contents("https://maps.googleapis.com/maps/api/geocode/json?latlng=" . $latlng . "&sensor=false".$ci->data['google_maps_api_key']);
													$mapValues = json_decode($gmap)->results;
													if(!empty($mapValues)){
														$formatted_address = $mapValues[0]->formatted_address;
														$cuurentLoc = array('timestamp' => MongoDATE(time()),
															'locality' => (string) $formatted_address,
															'location' => array('lat' => floatval($driver_lat), 'lon' => floatval($driver_lon))
														);
														
														if ($checkInfo->num_rows() > 0) {
															$ci->app_model->simple_push(TRACKING, array('ride_id' => (string) $ride_id), array('steps' => $cuurentLoc));
														} else {
															$ci->app_model->simple_insert(TRACKING, array('ride_id' => (string) $ride_id));
															$ci->app_model->simple_push(TRACKING, array('ride_id' => (string) $ride_id), array('steps' => $cuurentLoc));
														}
													}
												}
												
												/** req history update  **/
												if($ack_id != ''){
													$dataArr = array('status' => 'accepted','accepted_time' => MongoDATE(time()));
													$ci->app_model->update_details(RIDE_REQ_HISTORY,$dataArr,array('_id' => MongoID($ack_id)));
												}
												/*******************/
												
												if (empty($user_profile)) {
													$user_profile = json_decode("{}");
												}
                                                save_driver_stats($driver_id,'Confirmed');
												$returnArr['status'] = '1';
												$returnArr['response'] = array('user_profile' => $user_profile, 'message' => $ci->format_string("Ride Accepted", "ride_accepted"));
												
												if(isset($checkDriver->row()->last_online_time)){
													$dataArr = array('last_accept_time' => MongoDATE(time()));
													$ci->app_model->update_details(DRIVERS, $dataArr, array('_id' => MongoID($driver_id)));
                                                    $last_online_time=MongoEPOCH($checkDriver->row()->last_online_time);
													update_mileage_system($driver_id,$last_online_time,'free-roaming',$distance,$ci->data['d_distance_unit'],$ride_id);
												}
												
											} else {
												$returnArr['ride_view'] = 'home';
												$returnArr['response'] = $ci->format_string('you are too late, this ride is booked.', 'you_are_too_late_to_book_this_ride');
											}
										} else {
											$returnArr['ride_view'] = 'home';
											$returnArr['response'] = $ci->format_string('Sorry ! We can not fetch information', 'cannot_fetch_location_information_in_map');								
										}
									}else{
										$returnArr['ride_view'] = 'home';
										$returnArr['response'] = $ci->format_string('You cannot accept this ride.', 'you_cannot_accept_this_ride');
									}
								}else{
									$ride_status = $checkRide->row()->ride_status;
									if($ride_status=="Cancelled"){
										$returnArr['ride_view'] = 'home';
										$returnArr['response'] = $ci->format_string('Already this ride has been cancelled', 'already_ride_cancelled');
									}else if($checkRide->row()->driver['id'] == $driver_id){
										$returnArr['ride_view'] = 'detail';
										 $returnArr['response'] = $ci->format_string('Ride Accepted', 'ride_accepted');
									}else{
										$returnArr['ride_view'] = 'home';
										$returnArr['response'] = $ci->format_string('You cannot accept this ride.', 'you_cannot_accept_this_ride');
									}
								}
							} else {
								$returnArr['ride_view'] = 'home';
								$returnArr['response'] = $ci->format_string('you are too late, this ride is booked.', 'you_are_too_late_to_book_this_ride');
							}
						} else {
							$returnArr['ride_view'] = 'home';
							$returnArr['response'] = $ci->format_string("This ride is unavailable", "ride_unavailable");
						}
					} else {
						$returnArr['response'] = $ci->format_string("Driver not found", "driver_not_found");
					}
				}else{
					$returnArr['response'] = $ci->format_string("Some Parameters Missing", "some_parameters_missing");
				}				
			} catch (MongoException $ex) {
				$returnArr['response'] = $ci->format_string("Error in connection", "error_in_connection");
			}

			$json_encode = json_encode($returnArr, JSON_PRETTY_PRINT);
			echo $ci->cleanString($json_encode);
		}
	}
	
	/**
	*
	*	This function will update the pickup location reached state
	*	Param @locationInfo as Array
	*	Holds all the location arrived information Eg. Driver Id, ride id, latitude, longtitude
	*
	**/	
	if ( ! function_exists('pickup_location_arrived')){
		function pickup_location_arrived($locationInfo= array()) {
			$ci =& get_instance();
			
			$returnArr['status'] = '0';
			$returnArr['response'] = '';
			$returnArr['ride_view'] = 'stay';
			
			try {
				if(array_key_exists("driver_id",$locationInfo)) $driver_id =  trim($locationInfo['driver_id']); else $driver_id = "";
				if(array_key_exists("ride_id",$locationInfo)) $ride_id =  trim($locationInfo['ride_id']); else $ride_id = "";
				if(array_key_exists("driver_lat",$locationInfo)) $driver_lat =  trim($locationInfo['driver_lat']); else $driver_lat = "";
				if(array_key_exists("driver_lon",$locationInfo)) $driver_lon =  trim($locationInfo['driver_lon']); else $driver_lon = "";
				
				if ($driver_id!="" && $ride_id!="" && $driver_lat!="" && $driver_lon!="") {
					$checkDriver = $ci->app_model->get_selected_fields(DRIVERS, array('_id' => MongoID($driver_id)), array('_id', 'driver_name', 'image', 'avg_review', 'email', 'dail_code', 'mobile_number', 'vehicle_number', 'vehicle_model', 'driver_commission','company_id', 'operator_id','last_online_time','ride_type','mode'));
					if ($checkDriver->num_rows() == 1) {
						$checkRide = $ci->app_model->get_all_details(RIDES, array('ride_id' => $ride_id));
						if ($checkRide->num_rows() == 1) {
							if ($checkRide->row()->ride_status == 'Confirmed') {
								/* Update the ride information */
								$rideDetails = array('ride_status' => 'Arrived',
									'booking_information.arrived_location' => array('lon' => floatval($driver_lon), 'lat' => floatval($driver_lat)),
									'history.arrived_time' => MongoDATE(time())
								);
								$ci->app_model->update_details(RIDES, $rideDetails, array('ride_id' => $ride_id));
								
								$driver_lat = 0;
								$driver_lon = 0;
								if(isset($checkDriver->row()->loc)){
									if(is_array($checkDriver->row()->loc)){
										$driver_lat = floatval($checkDriver->row()->loc['lat']);
										$driver_lon = floatval($checkDriver->row()->loc['lon']);
									}
								}
								
								/* Notification to user about driver reached his location */
								$user_id = $checkRide->row()->user['id'];
								$userVal = $ci->app_model->get_selected_fields(USERS, array('_id' => MongoID($user_id)), array('_id', 'user_name', 'email', 'image', 'avg_review', 'phone_number', 'country_code', 'push_type', 'push_notification_key'));
								if (isset($userVal->row()->push_type)) {
									if ($userVal->row()->push_type != '') {
										$message = $ci->format_string('Driver arrived on your place', 'driver_arrived', '', 'user', (string)$userVal->row()->_id);
										
										$options = array('ride_id' => (string) $ride_id, 'user_id' => (string) $user_id, 'driver_lat' => (string) $driver_lat, 'driver_lon' => (string) $driver_lon);
										if ($userVal->row()->push_type == 'ANDROID') {
											if (isset($userVal->row()->push_notification_key['gcm_id'])) {
												if ($userVal->row()->push_notification_key['gcm_id'] != '') {
													$ci->sendPushNotification(array($userVal->row()->push_notification_key['gcm_id']), $message, 'cab_arrived', 'ANDROID', $options, 'USER');
												}
											}
										}
										if ($userVal->row()->push_type == 'IOS') {
											if (isset($userVal->row()->push_notification_key['ios_token'])) {
												if ($userVal->row()->push_notification_key['ios_token'] != '') {
													$ci->sendPushNotification(array($userVal->row()->push_notification_key['ios_token']), $message, 'cab_arrived', 'IOS', $options, 'USER');
												}
											}
										}
									}
								}
								$ci->sms_model->sms_on_driver_arraival($ride_id);
                                save_driver_stats($driver_id,'Arrived');
								$returnArr['status'] = '1';
								get_trip_information($driver_id); exit;
							} else {
								if($checkRide->row()->ride_status == 'Arrived'){
									$returnArr['status'] = '1';
									get_trip_information($driver_id); exit;
								}else{
									$returnArr['ride_view'] = 'detail';
									$returnArr['response'] = $ci->format_string('Ride Cancelled', 'ride_cancelled');
								}
							}
						} else {
							$returnArr['ride_view'] = 'home';
							$returnArr['response'] = $ci->format_string("This ride is unavailable", "ride_unavailable");
						}
					} else {
						$returnArr['response'] = $ci->format_string("Driver not found", "driver_not_found");
					}
				}else{
					$returnArr['response'] = $ci->format_string("Some Parameters Missing", "some_parameters_missing");
				}				
			} catch (MongoException $ex) {
				$returnArr['response'] = $ci->format_string("Error in connection", "error_in_connection");
			}

			$json_encode = json_encode($returnArr, JSON_PRETTY_PRINT);
			echo $ci->cleanString($json_encode);
		}
	}
		
	/**
	*
	*	This function will update the begin trip details
	*	Param @locationInfo as Array
	*	Holds all the location arrived information Eg. Driver Id, ride id, latitude, longtitude
	*
	**/	
	if ( ! function_exists('begin_the_trip')){
		function begin_the_trip($beginInfo= array()) {
			$ci =& get_instance();
			
			$returnArr['status'] = '0';
			$returnArr['response'] = '';
			$returnArr['ride_view'] = 'stay';
			
			try {
				if(array_key_exists("driver_id",$beginInfo)) $driver_id =  trim($beginInfo['driver_id']); else $driver_id = "";
				if(array_key_exists("ride_id",$beginInfo)) $ride_id =  trim($beginInfo['ride_id']); else $ride_id = "";
				if(array_key_exists("pickup_lat",$beginInfo)) $pickup_lat =  trim($beginInfo['pickup_lat']); else $pickup_lat = "";
				if(array_key_exists("pickup_lon",$beginInfo)) $pickup_lon =  trim($beginInfo['pickup_lon']); else $pickup_lon = "";
				
				if(array_key_exists("drop_lat",$beginInfo)) $drop_lat =  trim($beginInfo['drop_lat']); else $drop_lat = "";
				if(array_key_exists("drop_lon",$beginInfo)) $drop_lon =  trim($beginInfo['drop_lon']); else $drop_lon = "";
				
				if(array_key_exists("distance",$beginInfo)) $distance =  floatval($beginInfo['distance']); else $distance = 0;
				
				if(array_key_exists("no_of_seat",$beginInfo)) $no_of_seat =  floatval($beginInfo['no_of_seat']); else $no_of_seat = "";				
				
				if ($driver_id!="" && $ride_id!="" && $pickup_lat!="" && $pickup_lon!="" && $drop_lat!="" && $drop_lon!="" && $distance>=0){
					$checkDriver = $ci->app_model->get_selected_fields(DRIVERS, array('_id' => MongoID($driver_id)), array('email','last_accept_time'));
					if ($checkDriver->num_rows() == 1) { 
						$checkRide = $ci->app_model->get_all_details(RIDES, array('ride_id' => $ride_id));
						if ($checkRide->num_rows() == 1) {
							$service_id = $checkRide->row()->booking_information['service_id'];
							$doBegin = TRUE;
							if ($service_id==POOL_ID) {
								$doBegin = FALSE;
								if($no_of_seat==1 || $no_of_seat==2) $doBegin = TRUE;
							}
							if($doBegin == FALSE){
								$returnArr['response'] = $ci->format_string("Confirm number of seats", "confirm_seats");
							}else{
								if ($checkRide->row()->ride_status == 'Arrived') {
									$latlng = $pickup_lat . ',' . $pickup_lon;
									$gmap = file_get_contents("https://maps.googleapis.com/maps/api/geocode/json?latlng=" . $latlng . "&sensor=false".$ci->data['google_maps_api_key']);
									$map_result = json_decode($gmap);
									$mapValues = $map_result->results;
									
									$drop_latlng = $drop_lat . ',' . $drop_lon;
									$urldrop = "https://maps.googleapis.com/maps/api/geocode/json?latlng=" . $drop_latlng . "&sensor=false".$ci->data['google_maps_api_key'];
									$gmap_drop = file_get_contents($urldrop);
									$drop_result = json_decode($gmap_drop);
									$mapValues_drop = $drop_result->results;
									
									if(!empty($mapValues) && !empty($mapValues_drop)){
										$formatted_address = $mapValues[0]->formatted_address;
										$drop_address = $mapValues_drop[0]->formatted_address;
										
										/* Update the ride information */
										$curr_time = time();
										
										
										
										$rideDetails = array('ride_status' => 'Onride',
											'booking_information.pickup_date' => MongoDATE($curr_time),
											'booking_information.pickup.location' => (string) $formatted_address,
											'booking_information.pickup.latlong' => array('lon' => floatval($pickup_lon),
												'lat' => floatval($pickup_lat)
											),
											'booking_information.drop.location' => (string) $drop_address,
											'booking_information.drop.latlong' => array('lon' => floatval($drop_lon),
												'lat' => floatval($drop_lat)
											),
											'history.begin_ride' => MongoDATE($curr_time)
										);
										if ($service_id==POOL_ID) {
											$est_amount = 0;
											if($no_of_seat==1){
												$est_amount = $checkRide->row()->pool_fare["passanger"];
											}else if($no_of_seat==2){
												$est_amount = $checkRide->row()->pool_fare["co_passanger"];
											}
											#$rideDetails["pool_fare.est"] = (string)$est_amount;
											$rideDetails["no_of_seat"] = (string)$no_of_seat;
										}
										$ci->app_model->update_details(RIDES, $rideDetails, array('ride_id' => $ride_id));
										
										/* Notification to user about begin trip  */
										$user_id = $checkRide->row()->user['id'];
										$userVal = $ci->app_model->get_selected_fields(USERS, array('_id' => MongoID($user_id)), array('_id', 'user_name', 'email', 'image', 'avg_review', 'phone_number', 'country_code', 'push_type', 'push_notification_key'));
										if (isset($userVal->row()->push_type)) {
											if ($userVal->row()->push_type != '') {
												$message = $ci->format_string("Your trip has been started", "your_trip_has_been_started", '', 'user', (string)$userVal->row()->_id);
												$options = array('ride_id' => (string) $ride_id, 'user_id' => (string) $user_id, 'drop_lat' => (string) $drop_lat, 'drop_lon' => (string) $drop_lon, 'pickup_lat' => (string) $pickup_lat, 'pickup_lon' => (string) $pickup_lon,'drop_address'=>(string) $drop_address);
												if ($userVal->row()->push_type == 'ANDROID') {
													if (isset($userVal->row()->push_notification_key['gcm_id'])) {
														if ($userVal->row()->push_notification_key['gcm_id'] != '') {
															$ci->sendPushNotification(array($userVal->row()->push_notification_key['gcm_id']), $message, 'trip_begin', 'ANDROID', $options, 'USER');
														}
													}
												}
												if ($userVal->row()->push_type == 'IOS') {
													if (isset($userVal->row()->push_notification_key['ios_token'])) {
														if ($userVal->row()->push_notification_key['ios_token'] != '') {
															$ci->sendPushNotification(array($userVal->row()->push_notification_key['ios_token']), $message, 'trip_begin', 'IOS', $options, 'USER');
														}
													}
												}
											}
										}
										
										if(isset($checkDriver->row()->last_accept_time)){
											$dataArr = array('last_begin_time' => MongoDATE(time()));
											$ci->app_model->update_details(DRIVERS, $dataArr, array('_id' => MongoID($driver_id)));
                                            $last_accept_time=MongoEPOCH($checkDriver->row()->last_accept_time);
											update_mileage_system($driver_id,$last_accept_time,'customer-pickup',$distance,$ci->data['d_distance_unit'],$ride_id);
										}
										save_driver_stats($driver_id,'Onride');
										get_trip_information($driver_id); exit;
										
									}else{
										$returnArr['response'] = $ci->format_string('Sorry ! We can not fetch information', 'cannot_fetch_location_information_in_map');
									}
								} else {
									if($checkRide->row()->ride_status == 'Onride'){
										/* $returnArr['ride_view'] = 'next';
										$returnArr['response'] = $ci->format_string('Already Ride Started', 'already_ride_started'); */
										get_trip_information($driver_id); exit;
									}else{
										/* $returnArr['ride_view'] = 'detail';
										$returnArr['response'] = $ci->format_string('Ride Cancelled', 'ride_cancelled'); */
										get_trip_information($driver_id); exit;
									}
								}
							}
						} else {
							$returnArr['response'] = $ci->format_string("Invalid Ride", "invalid_ride");
						}
					} else {
						$returnArr['response'] = $ci->format_string("Driver not found", "driver_not_found");
					}					
				}else{
					$returnArr['response'] = $ci->format_string("Some Parameters Missing", "some_parameters_missing");
				}				
			} catch (MongoException $ex) {
				$returnArr['response'] = $ci->format_string("Error in connection", "error_in_connection");
			}
			$json_encode = json_encode($returnArr, JSON_PRETTY_PRINT);
			echo $ci->cleanString($json_encode);
		}
	}
	
	/**
	*
	*	This function will update the begin trip details
	*	Param @locationInfo as Array
	*	Holds all the location arrived information Eg. Driver Id, ride id, latitude, longtitude
	*
	**/	
	if ( ! function_exists('finish_the_trip')){
		function finish_the_trip($endInfo= array()) {
			$ci =& get_instance();
			
			$sour = "";
			
			$returnArr['status'] = '0';
			$returnArr['response'] = '';
			$returnArr['ride_view'] = 'stay';
			
			try {				
				
				if(array_key_exists("sour",$endInfo)) $sour =  trim($endInfo['sour']); else $sour = "";
				
				
				if(array_key_exists("driver_id",$endInfo)) $driver_id =  trim($endInfo['driver_id']); else $driver_id = "";
				if(array_key_exists("ride_id",$endInfo)) $ride_id =  trim($endInfo['ride_id']); else $ride_id = "";
				if(array_key_exists("drop_lat",$endInfo)) $drop_lat =  trim($endInfo['drop_lat']); else $drop_lat = "";
				if(array_key_exists("drop_lon",$endInfo)) $drop_lon =  trim($endInfo['drop_lon']); else $drop_lon = "";
				
				if(array_key_exists("distance",$endInfo)) $distance =  floatval($endInfo['distance']); else $distance = "";
				$device_distance = $distance;
				
				if(array_key_exists("wait_time_frame",$endInfo)) $wait_time_frame =  trim($endInfo['wait_time_frame']); else $wait_time_frame = "";
								
				if(array_key_exists("travel_history",$endInfo)) $travel_history =  trim($endInfo['travel_history']); else $travel_history = "";
				# string lat;log;time,lat;lon;time,...
				
				if(array_key_exists("parking_charge",$endInfo)) $pC =  trim($endInfo['parking_charge']); else $pC = "";
				if(array_key_exists("toll_charge",$endInfo)) $tC =  trim($endInfo['toll_charge']); else $tC = "";
				
				$parking_charge = 0; $toll_charge = 0;
				if(is_numeric($pC)) $parking_charge = round($pC,2);
				if(is_numeric($tC)) $toll_charge = round($tC,2);
				
				$wait_time = 0;
                if ($wait_time_frame != '') {
                    $wt = @explode(':', $wait_time_frame);
                    $h = 0; $m = 0; $s = 0;
					if(isset($wt[0])) $h = intval($wt[0]);
                    if(isset($wt[1])) $m = intval($wt[1]);
                    if(isset($wt[2])) $s = intval($wt[2]);
					 
                    if ($h > 0) {
                        $wait_time = $h * 60;
                    }
                    if ($m > 0) {
                        $wait_time = $wait_time + ($m);
                    }
					if ($s > 0) {
                        $wait_time = $wait_time + 1;
                    }
                }
				if(!is_numeric($wait_time)){
					$wait_time = 0;
				}
				
				if ($driver_id!="" && $ride_id!="" && $drop_lat!="" && $drop_lon!=""){
					$checkDriver = $ci->app_model->get_selected_fields(DRIVERS, array('_id' => MongoID($driver_id)), array('email','last_begin_time','push_notification'));
					if ($checkDriver->num_rows() == 1) {
						$checkRide = $ci->app_model->get_all_details(RIDES, array('ride_id' => $ride_id));
						if ($checkRide->num_rows() == 1) {
							
							$pickup_time = MongoEPOCH($checkRide->row()->booking_information['pickup_date']);
							$drop_time = time();
							$interval  = abs($drop_time - $pickup_time);
							$ride_time_min = abs($interval / 60);
							$ride_end_time = $drop_time; // Trip Timestamp
							$ride_wait_time = abs($wait_time*60);	// in Seconds
							$ride_total_time_min = ceil($ride_time_min - $wait_time);
							if($ride_total_time_min<1) $ride_total_time_min = 1;
							$duration = $ride_total_time_min;
							
							$distance_unit = $ci->data['d_distance_unit'];
							if(isset($checkRide->row()->fare_breakup['distance_unit'])){
								$distance_unit = $checkRide->row()->fare_breakup['distance_unit'];
							}
							
							
							
							$math_ext_distance = 0;
							if($travel_history!="") $math_ext_distance = get_distance_from_latlong($travel_history,$ride_id);
							$distance_unit = $ci->data['d_distance_unit'];
							if(isset($checkRide->row()->fare_breakup['distance_unit'])){
								$distance_unit = $checkRide->row()->fare_breakup['distance_unit'];
							}
							
							$math_ext_distance = floatval($math_ext_distance);
							if($sour != "w"){
								if($math_ext_distance>=0){
									$googleDistance = 0;
									
                                    $pickup_lat=$checkRide->row()->booking_information["pickup"]["latlong"]["lat"];
                                    $pickup_lon=$checkRide->row()->booking_information["pickup"]["latlong"]["lon"];
									$pickuplatlng = $pickup_lat.','.$pickup_lon;
									$drlatlng = $drop_lat.','.$drop_lon;
									
									$gURL = 'https://maps.googleapis.com/maps/api/directions/json?origin='.$pickuplatlng.'&destination='.$drlatlng. '&alternatives=true&sensor=false&mode=driving'.$ci->data['google_maps_api_key'];
									$gmap = file_get_contents($gURL); 
									$map_values = json_decode($gmap);
									$routes = $map_values->routes;
									if(!empty($routes)){
										usort($routes, create_function('$a,$b', 'return intval($a->legs[0]->distance->value) - intval($b->legs[0]->distance->value);'));
										$min_distance = $routes[0]->legs[0]->distance->text;
										$min_duration = round($routes[0]->legs[0]->duration->value/60);
										if (preg_match('/km/',$min_distance)){
											$return_distance = 'km';
										}else if (preg_match('/mi/',$min_distance)){
											$return_distance = 'mi';
										}else if (preg_match('/m/',$min_distance)){
											$return_distance = 'm';
										} else {
											$return_distance = 'km';
										}
										
										$apxdistance = floatval(str_replace(',','',$min_distance));
										if($distance_unit!=$return_distance){
											if($distance_unit=='km' && $return_distance=='mi'){
												$apxdistance = $apxdistance * 1.60934;
											} else if($distance_unit=='mi' && $return_distance=='km'){
												$apxdistance = $apxdistance * 0.621371;
											} else if($distance_unit=='km' && $return_distance=='m'){
												$apxdistance = $apxdistance / 1000;
											} else if($distance_unit=='mi' && $return_distance=='m'){
												$apxdistance = $apxdistance * 0.00062137;
											}
										}
										$googleDistance = floatval(round($apxdistance,2));
									}
									$distance = $math_ext_distance;
									$timeDiff = FALSE;
									if(($duration > ($min_duration*1.5)) || (($duration*1.5) < $min_duration)){
										$timeDiff = TRUE;
									}
									if($timeDiff==TRUE){
										if($duration < $min_duration){
											if($math_ext_distance>0){
												$distance = $math_ext_distance;
											}else{
												$distance = $googleDistance;
											}
										}else{
											if($googleDistance>0){
												if($math_ext_distance>0){
													$distance = $math_ext_distance;
												}else{
													$distance = $googleDistance;
												}
											}
										}
									}else{
										if($googleDistance>0){
											if($math_ext_distance>0){
												$distance = $math_ext_distance;
												/* if((($googleDistance*1.5) <= $math_ext_distance) || ($googleDistance >= ($math_ext_distance*1.5))){
													$distance = $googleDistance;
												} */
											}else{
												$distance = $googleDistance;
											}
										}
									}
								}
							}							
							$distanceKM = $math_ext_distance;
							
							if($distance_unit == 'mi'){
								$distance = round(($distance / 1.609344),2);
							}
							if($checkRide->row()->ride_status=='Onride'){
								$currency = $checkRide->row()->currency;
								$grand_fare = 0;
								$total_fare = 0;
								$free_ride_time = 0;
								$total_base_fare = 0;
								$total_distance_charge = 0;
								$total_ride_charge = 0;
								$total_waiting_charge = 0;
								$total_peak_time_charge = 0;
								$total_night_time_charge = 0;
								$total_tax = 0;
								$coupon_discount = 0;
								
								$latlng = $drop_lat . ',' . $drop_lon;
								$gmap = file_get_contents("https://maps.googleapis.com/maps/api/geocode/json?latlng=" . $latlng . "&sensor=false".$ci->data['google_maps_api_key']);
								$map_values = json_decode($gmap);
								$mapValues = $map_values->results;
								if(!empty($mapValues)){									
									$dropping_address = $mapValues[0]->formatted_address;
									if(($pickup_time+$ride_wait_time)<=($ride_end_time+100)){
										
										$trip_type = "Normal";
										if(isset($checkRide->row()->pool_ride)){
											if($checkRide->row()->pool_ride=="Yes"){
												$trip_type = "Share";
											}
										}
										
										if($trip_type == "Normal"){
											$total_base_fare = $checkRide->row()->fare_breakup['min_fare'];
											$min_time = $ride_total_time_min - $checkRide->row()->fare_breakup['min_time'];
											if ($min_time > 0) {
												$total_ride_charge = ($ride_total_time_min - $checkRide->row()->fare_breakup['min_time']) * $checkRide->row()->fare_breakup['per_minute'];
											}
											$min_distance = $distance - $checkRide->row()->fare_breakup['min_km'];
											if ($min_distance > 0) {
												$total_distance_charge = ($distance - $checkRide->row()->fare_breakup['min_km']) * $checkRide->row()->fare_breakup['per_km'];
											}
											if ($wait_time > 0) {
												$total_waiting_charge = $wait_time * $checkRide->row()->fare_breakup['wait_per_minute'];
											}
											$total_fare = $total_base_fare + $total_distance_charge + $total_ride_charge + $total_waiting_charge;
											$grand_fare = $total_fare;
											
											if ($checkRide->row()->fare_breakup['peak_time_charge'] != '') {
												if($checkRide->row()->fare_breakup['peak_time_charge']>0){
													$total_peak_time_charge = $total_fare * $checkRide->row()->fare_breakup['peak_time_charge'];
													$grand_fare =$total_peak_time_charge;
												}
											}
											if ($checkRide->row()->fare_breakup['night_charge'] != '') {
												if($checkRide->row()->fare_breakup['night_charge']>0){
													$total_night_time_charge = $total_fare * $checkRide->row()->fare_breakup['night_charge'];
													if($total_peak_time_charge==0){
														$grand_fare = $total_night_time_charge;
													}else{
														$grand_fare = $grand_fare + $total_night_time_charge;
													}
												}
											}
											
											if($grand_fare != $total_fare){
												$grand_fare = $total_peak_time_charge + $total_night_time_charge;
											}else{
												$grand_fare = $total_fare;
											}
											if($total_peak_time_charge>0 && $total_night_time_charge>0){
												$total_surge = $total_peak_time_charge + $total_night_time_charge;
												$surge_val = $checkRide->row()->fare_breakup['peak_time_charge'] + $checkRide->row()->fare_breakup['night_charge'];
												$unit_surge = ($total_surge-$total_fare) / $surge_val;
												$total_peak_time_charge = $unit_surge * $checkRide->row()->fare_breakup['peak_time_charge'];
												$total_night_time_charge = $unit_surge * $checkRide->row()->fare_breakup['night_charge'];
											}else{
												if($total_peak_time_charge>0){
													$total_peak_time_charge = $grand_fare - $total_fare;
												}
												if($total_night_time_charge>0){
													$total_night_time_charge = $grand_fare - $total_fare;
												}
											}
										}else if($trip_type == "Share"){
											$no_of_seat = 1; $tax = 0;
											$tax_percent = 0;
											if(isset($checkRide->row()->pool_fare)){
												if(!empty($checkRide->row()->pool_fare)){
													$tax_percent = $checkRide->row()->pool_fare["tax_percent"];
												}
											}
											if(isset($checkRide->row()->no_of_seat)){
												if($checkRide->row()->no_of_seat!=""){
													$no_of_seat = $checkRide->row()->no_of_seat;
												}
											}
											if($no_of_seat==1){
												if(isset($checkRide->row()->pool_fare)){
													if(!empty($checkRide->row()->pool_fare)){
														$grand_fare = $checkRide->row()->pool_fare["passanger"];
														$final_tax_deduction = (($checkRide->row()->pool_fare["base_fare"]*0.01*$checkRide->row()->pool_fare["single_percent"])*0.01*$tax_percent);														
														$tax = $final_tax_deduction;
													}
												}
											}
											if($no_of_seat==2){
												if(isset($checkRide->row()->pool_fare)){
													if(!empty($checkRide->row()->pool_fare)){
														$grand_fare = $checkRide->row()->pool_fare["co_passanger"];
														$final_tax_deduction = ((($checkRide->row()->pool_fare["base_fare"]*0.01*$checkRide->row()->pool_fare["single_percent"])+((($checkRide->row()->pool_fare["base_fare"]*0.01*$checkRide->row()->pool_fare["single_percent"]))*0.01*$checkRide->row()->pool_fare["double_percent"]))*0.01*$tax_percent);														
														$tax = $final_tax_deduction;
													}
												}
											}											
											$total_fare = $grand_fare - $tax;
											$grand_fare = $total_fare;
										}
										
										/*	Normal trip fare calculation end	*/
										
										$user_id=$checkRide->row()->user['id'];
										
										if ($checkRide->row()->coupon_used == 'Yes') {
											$coupon_valid='No';
											$checkCode = $ci->app_model->get_all_details(PROMOCODE, array('promo_code' => $checkRide->row()->coupon['code']));
												if ($checkCode->row()->status == 'Active') {
													$valid_from = strtotime($checkCode->row()->validity['valid_from'] . ' 00:00:00');
													$valid_to = strtotime($checkCode->row()->validity['valid_to'] . ' 23:59:59');
													$date_time = MongoEPOCH($checkRide->row()->booking_information['est_pickup_date']);
													if (($valid_from <= $date_time) && ($valid_to >= $date_time)) {
														if ($checkCode->row()->usage_allowed > $checkCode->row()->no_of_usage) {
															 $coupon_usage = array();
															if (isset($checkCode->row()->usage)) {
																$coupon_usage = $checkCode->row()->usage;
															}
															$user_id=$checkRide->row()->user['id'];
															$usage = $ci->app_model->check_user_usage($coupon_usage, $user_id);
															if ($usage < $checkCode->row()->user_usage) {
																$coupon_valid='Yes';
															}
														}
													}
												}
											
											 if($coupon_valid=='No') {
												$coupon_update=array('coupon_used' =>'No',
														'coupon' => array('code' =>'',
															'type' => '',
															'amount' =>''
														));
											   $ci->app_model->update_details(RIDES, $coupon_update, array('ride_id' => $ride_id));
											 }
										}
														
										if ($checkRide->row()->coupon_used == 'Yes') {
											if ($checkRide->row()->coupon['type'] == 'Percent') {
												$coupon_discount = ($grand_fare * 0.01) * $checkRide->row()->coupon['amount'];
											} else if ($checkRide->row()->coupon['type'] == 'Flat') {
												if ($checkRide->row()->coupon['amount'] <= $grand_fare) {
													$coupon_discount = $checkRide->row()->coupon['amount'];
												} else if ($checkRide->row()->coupon['amount'] > $grand_fare) {
													$coupon_discount = $grand_fare;
												}
											}
											$grand_fare = $grand_fare - $coupon_discount;
											if ($grand_fare < 0) {
												$grand_fare = 0;
											}
											$coupon_condition = array('promo_code' => $checkRide->row()->coupon['code']);
											$ci->mongo_db->where($coupon_condition)->inc('no_of_usage', 1)->update(PROMOCODE);
											/* Update the coupon usage details */
											if ($checkRide->row()->coupon_used == 'Yes') {
												$usage = array("user_id" => (string) $user_id, "ride_id" => $ride_id);
												$promo_code = (string) $checkRide->row()->coupon['code'];
												$ci->app_model->simple_push(PROMOCODE, array('promo_code' => $promo_code), array('usage' => $usage));
											}
										}
										
										if ($checkRide->row()->tax_breakup['service_tax'] != '') {
											$total_tax = $grand_fare * 0.01 * $checkRide->row()->tax_breakup['service_tax'];
											$grand_fare = $grand_fare + $total_tax;
										}
										$grand_fare = $grand_fare + $parking_charge  +  $toll_charge;
										$original_grand_fare=$grand_fare;
										$total_fare = array('base_fare' => round($total_base_fare, 2),
																	'distance' => round($total_distance_charge, 2),
																	'free_ride_time' => round($free_ride_time, 2),
																	'ride_time' => round($total_ride_charge, 2),
																	'wait_time' => round($total_waiting_charge, 2),
																	'peak_time_charge' => round($total_peak_time_charge, 2),
																	'night_time_charge' => round($total_night_time_charge, 2),
																	'total_fare' => round($total_fare, 2),
																	'coupon_discount' => round($coupon_discount, 2),
																	 'parking_charge' => round($parking_charge, 2),
																	'toll_charge' => round($toll_charge, 2),
																	'service_tax' => round($total_tax, 2),
																	'grand_fare' => round($grand_fare),
																	'original_grand_fare' =>floatval($original_grand_fare),
																	'wallet_usage' => 0,
																	'paid_amount' => 0
																);
										$summary = array('ride_distance' => round($distance, 2),
																	'device_distance' => round($device_distance, 2),
																	'math_distance' => round($math_ext_distance, 2),
																	'ride_duration' => round(ceil($ride_total_time_min), 2),
																	'waiting_duration' => round(ceil($wait_time), 2)
																);
										
										$need_payment = 'YES';
										$ride_status = 'Finished';
										$pay_status = 'Pending';
										$isFree = 'NO';
										if ($grand_fare <= 0) {
											$need_payment = 'NO';
											$ride_status = 'Completed';
											$pay_status = 'Paid';
											$isFree = 'Yes';
										}	
										$mins = $ci->format_string('mins', 'mins');
										
										$min_short = $ci->format_string('min', 'min_short');
										$mins_short = $ci->format_string('mins', 'mins_short');
										if($ride_total_time_min>1){
											$ride_time_unit = $mins_short;
										}else{
											$ride_time_unit = $min_short;
										}
										if($wait_time>1){
											$wait_time_unit = $mins_short;
										}else{
											$wait_time_unit = $min_short;
										}
										
										$distance_unit = $ci->data['d_distance_unit'];
										if(isset($checkRide->row()->fare_breakup['distance_unit'])){
											$distance_unit = $checkRide->row()->fare_breakup['distance_unit'];
										}
										if($distance_unit == 'km'){
											$disp_distance_unit = $ci->format_string('km', 'km');
										}else if($distance_unit == 'mi'){
											$disp_distance_unit = $ci->format_string('mi', 'mi');
										}
										$fare_details = array('currency' => $currency,
											'ride_fare' => floatval(round($grand_fare, 2)),
											'ride_distance' => floatval(round($distance, 2)) . '  ' . $disp_distance_unit,
											'ride_duration' => round(ceil($ride_total_time_min), 2) . '  ' . $ride_time_unit,
											'waiting_duration' => round(ceil($wait_time), 2) . '  ' . $wait_time_unit,
											'need_payment' => $need_payment
										);


										$amount_commission = 0;
										$driver_revenue = 0;

										$total_grand_fare = $coupon_discount + $grand_fare;
										$total_grand_fare_without_tax = $total_grand_fare - ($total_tax + $parking_charge + $toll_charge);
										$admin_commission_percent = $checkRide->row()->commission_percent;
										$amount_commission = (($total_grand_fare_without_tax * 0.01) * $admin_commission_percent)+$total_tax;
										$driver_revenue = $total_grand_fare - $amount_commission;
										
										/* Update the ride information */
										$rideDetails = array('ride_status' => (string)$ride_status,
											'pay_status' => (string)$pay_status,
											'amount_commission' => floatval(round($amount_commission, 2)),
											'driver_revenue' => floatval(round($driver_revenue, 2)),
											'booking_information.drop_date' => MongoDATE(time()),
											'booking_information.drop.location' => (string) $dropping_address,
											'booking_information.drop.latlong' => array('lon' => floatval($drop_lon),
												'lat' => floatval($drop_lat)
											),
											'history.end_ride' => MongoDATE(time()),
											'total' => $total_fare,
											'summary' => $summary,
											'drs' => "0",
											'urs' => "0"
										);
										#echo "<pre>"; print_r($rideDetails); die;
										$ci->app_model->update_details(RIDES, $rideDetails, array('ride_id' => $ride_id));
										$ci->app_model->simple_insert(PAYMENTS, array('ride_id' => (string) $ride_id, 'total' => round($grand_fare, 2), 'transactions' => array()));
										/* update the driver completed count */
										$ci->app_model->update_driver_rides_count('no_of_rides', $driver_id);
                                        save_driver_stats($driver_id,'Finished');
										/* First ride money credit for referrer */
										$sortArr = array('ride_id' => -1);
                                       
										$firstRide = $ci->app_model->get_selected_fields(RIDES, array('user.id' => $checkRide->row()->user['id']), array('_id','ride_id'),$sortArr,1,0);
                                       
										if ($firstRide->num_rows() == 1) {
											$get_referVal=$ci->app_model->get_all_details(REFER_HISTORY,array('history'=>array('$all'=>array(array('$elemMatch'=>array('reference_id'=>$checkRide->row()->user['id'],'used'=>'false'))))));
											if ($get_referVal->num_rows() > 0) {
												$referer_user_id = (string)$get_referVal->row()->user_id;
												$condition = array('history.reference_id' => $checkRide->row()->user['id'],
													'user_id' => MongoID($get_referVal->row()->user_id));
													
												$trans_amount = 0.00;
												if (is_array($get_referVal->row()->history)) {
													foreach ($get_referVal->row()->history as $key => $value) {
														if ($value['reference_id'] == $checkRide->row()->user['id']) {
															$trans_amount = $value['amount_earns'];
														}
													}
												}
                                                
                                                $referrDataArr = array('history.$.used' => 'true','history.$.amount_earns' => floatval($trans_amount));
                                                $ci->app_model->update_details(REFER_HISTORY, $referrDataArr, $condition);
                                                
                                                if($trans_amount > 0){

                                                    $ci->app_model->update_wallet($referer_user_id, 'CREDIT', floatval($trans_amount));
                                                    $walletDetail = $ci->app_model->get_selected_fields(WALLET, array('user_id' => MongoID($referer_user_id)), array('total'));
                                                    $avail_amount = 0;
                                                    if (isset($walletDetail->row()->total)) {
                                                        $avail_amount = $walletDetail->row()->total;
                                                    }
                                                    $trans_id = time() . rand(0, 2578);
                                                    $walletArr = array('type' => 'CREDIT',
                                                        'credit_type' => 'referral',
                                                        'ref_id' => (string) $checkRide->row()->user['id'],
                                                        'trans_amount' => floatval($trans_amount),
                                                        'avail_amount' => floatval($avail_amount),
                                                        'trans_date' => MongoDATE(time()),
                                                        'trans_id' => $trans_id
                                                    );
                                                    $ci->app_model->simple_push(WALLET, array('user_id' => MongoID($referer_user_id)), array('transactions' => $walletArr));
                                                }
											}
										}
										
										$makeInvoice = 'No';
										/*	Making the automatic payment process start	*/
										#$ci->auto_payment_deduct($ride_id);
										$crideNew = $ci->app_model->get_all_details(RIDES, array('ride_id' => $ride_id));
										if($crideNew->num_rows()>0){
											if($crideNew->row()->ride_status == "Completed"){
												$need_payment = 'NO';
												$makeInvoice = 'Yes';
											}
										}
										/*	Making the automatic payment process end	*/
										
										/* Sending notification to user regarding booking confirmation -- Start */
										$userVal = $ci->app_model->get_selected_fields(USERS, array('_id' => MongoID($checkRide->row()->user['id'])), array('_id', 'user_name', 'email', 'image', 'avg_review', 'phone_number', 'country_code', 'push_type', 'push_notification_key'));
										if (isset($userVal->row()->push_type)) {
											if ($userVal->row()->push_type != '') {
												if ($need_payment == 'NO') {
													$user_id = $checkRide->row()->user['id'];
													$message = $ci->format_string('Ride Completed', 'ride_completed', '', 'user', (string)$userVal->row()->_id);
													$options = array('ride_id' => (string) $ride_id, 'user_id' => (string) $user_id);
													if ($userVal->row()->push_type == 'ANDROID') {
														if (isset($userVal->row()->push_notification_key['gcm_id'])) {
															if ($userVal->row()->push_notification_key['gcm_id'] != '') {
																$ci->sendPushNotification($userVal->row()->push_notification_key['gcm_id'], $message, 'payment_paid', 'ANDROID', $options, 'USER');
															}
														}
													}
													if ($userVal->row()->push_type == 'IOS') {
														if (isset($userVal->row()->push_notification_key['ios_token'])) {
															if ($userVal->row()->push_notification_key['ios_token'] != '') {
																$ci->sendPushNotification($userVal->row()->push_notification_key['ios_token'], $message, 'payment_paid', 'IOS', $options, 'USER');
															}
														}
													}
												}else{
													$message = $ci->format_string('Ride Completed', 'ride_completed', '', 'user', (string)$userVal->row()->_id);
													$options = array('ride_id' => (string) $ride_id);
													if ($userVal->row()->push_type == 'ANDROID') {
														if (isset($userVal->row()->push_notification_key['gcm_id'])) {
															if ($userVal->row()->push_notification_key['gcm_id'] != '') {
																$ci->sendPushNotification($userVal->row()->push_notification_key['gcm_id'], $message, 'make_payment', 'ANDROID', $options, 'USER');
															}
														}
													}
													if ($userVal->row()->push_type == 'IOS') {
														if (isset($userVal->row()->push_notification_key['ios_token'])) {
															if ($userVal->row()->push_notification_key['ios_token'] != '') {
																$ci->sendPushNotification($userVal->row()->push_notification_key['ios_token'], $message, 'make_payment', 'IOS', $options, 'USER');
															}
														}
													}
												}
											}
										}
										
										if ($need_payment == 'NO' && $isFree == 'Yes') {
											$pay_summary = array('type' => 'FREE');
											$paymentInfo = array('pay_summary' => $pay_summary);
											$ci->app_model->update_details(RIDES, $paymentInfo, array('ride_id' => $ride_id));
											/* Update Stats Starts */
											$current_date = MongoDATE(strtotime(date("Y-m-d 00:00:00")));
											$field = array('ride_completed.hour_' . date('H') => 1, 'ride_completed.count' => 1);
											$ci->app_model->update_stats(array('day_hour' => $current_date), $field, 1);
											/* Update Stats End */
											$avail_data = array('mode' => 'Available', 'availability' => 'Yes');
											$ci->app_model->update_details(DRIVERS, $avail_data, array('_id' => MongoID($driver_id)));
											$trans_id = time() . rand(0, 2578);
											$transactionArr = array('type' => 'Coupon',
												'amount' => floatval($grand_fare),
												'trans_id' => $trans_id,
												'trans_date' => MongoDATE(time())
											);
											$ci->app_model->simple_push(PAYMENTS, array('ride_id' => $ride_id), array('transactions' => $transactionArr));
											$makeInvoice = 'Yes';
										}
										if (empty($fare_details)) {
											$fare_details = json_decode("{}");
										}else{
											$fare_details['need_payment'] = $need_payment;
										}
										create_and_save_travel_path_in_map($ride_id);
										if($makeInvoice == 'Yes'){
											$ci->app_model->update_ride_amounts($ride_id);
											#	make and sending invoice to the rider 	#
											$fields = array(
												'ride_id' => (string) $ride_id
											);
											$url = base_url().'prepare-invoice';
											$ci->load->library('curl');
											$output = $ci->curl->simple_post($url, $fields);
										}
										
										if(isset($checkDriver->row()->last_begin_time)){
											$dataArr = array('last_online_time' => MongoDATE(time()));
											$ci->app_model->update_details(DRIVERS, $dataArr, array('_id' => MongoID($driver_id)));
                                            $last_begin_time=MongoEPOCH($checkDriver->row()->last_begin_time);
											update_mileage_system($driver_id,$last_begin_time,'customer-drop',$distanceKM,$ci->data['d_distance_unit'],$ride_id);
										}
										
										$receive_cash = 'Disable';
										if ($ci->config->item('pay_by_cash') != '' && $ci->config->item('pay_by_cash') != 'Disable') {
											$receive_cash = 'Enable';
										}
										
										$req_payment = 'Enable';
										$payArr = $ci->app_model->get_all_details(PAYMENT_GATEWAY,array("status"=>"Enable"));
										if($payArr->num_rows()==0){
											$req_payment = 'Disable';
										}
										
										
										$ratting_content = '1';
										$returnArr['status'] = '1';
										if($sour == "w"){
											/********* Send push notification driver if ride ends from admin ********/
                                            if (isset($checkDriver->row()->push_notification)) {
                                                if ($checkDriver->row()->push_notification != '') {
                                                    $message = $ci->format_string('Your Trip has been completed', 'your_trip_has_been_completed', '', 'driver', (string)$driver_id);
                                                    $options = array('ride_id' => (string) $ride_id, 'driver_id' => $driver_id);
                                                    if (isset($checkDriver->row()->push_notification['type'])) {
                                                        if ($checkDriver->row()->push_notification['type'] == 'ANDROID') {
                                                            if (isset($checkDriver->row()->push_notification['key'])) {
                                                                if ($checkDriver->row()->push_notification['key'] != '') {
                                                                    $ci->sendPushNotification($checkDriver->row()->push_notification['key'], $message, 'ride_completed', 'ANDROID', $options, 'DRIVER');
                                                                }
                                                            }
                                                        }
                                                        if ($checkDriver->row()->push_notification['type'] == 'IOS') {
                                                            if (isset($checkDriver->row()->push_notification['key'])) {
                                                                if ($checkDriver->row()->push_notification['key'] != '') {
                                                                    $ci->sendPushNotification($checkDriver->row()->push_notification['key'], $message, 'ride_completed', 'IOS', $options, 'DRIVER');
                                                                }
                                                            }
                                                        }
                                                    }
                                                }
                                            }
											$returnArr['response'] = $ci->format_string("Ride Completed", "manual_ride_completed");
											return $returnArr;
										}else{
											get_trip_information($driver_id); exit;
										}
										
									}else{
										$returnArr['response'] = $ci->format_string("Entered inputs are incorrect", "invalid_trip_end_inputs");
									}
								}else{
									$returnArr['response'] = $ci->format_string('Sorry ! We can not fetch information', 'cannot_fetch_location_information_in_map');
								}
							} else {
								if($sour == "w"){
									$returnArr['status'] = '1';
									$returnArr['response'] = $ci->format_string("Ride Completed", "manual_ride_completed");
									return $returnArr;
								}else{
									get_trip_information($driver_id); exit;
								}
							}
						} else {
							$returnArr['response'] = $ci->format_string("Invalid Ride", "invalid_ride");
						}
					} else {
						$returnArr['response'] = $ci->format_string("Driver not found", "driver_not_found");
					}
				}else{
					$returnArr['response'] = $ci->format_string("Some Parameters Missing", "some_parameters_missing");
				}				
			} catch (MongoException $ex) {
				$returnArr['response'] = $ci->format_string("Error in connection", "error_in_connection");
			}
			
			if($sour == "w"){
				return $returnArr;
			}else{
				$json_encode = json_encode($returnArr, JSON_PRETTY_PRINT);
				echo $ci->cleanString($json_encode);
			}
			
		}
	}
		
	
	/**
	*
	* This Function returns the trip information to drivers
	*
	**/
	if ( ! function_exists('get_trip_information')){
		function get_trip_information($driver_id= "") {
			$ci =& get_instance();
			
			$responseArr['status'] = '0';
			$responseArr['ride_view'] = 'home';
			$responseArr['response'] = '';
			try {
				if($driver_id!="") $driver_id = $ci->input->post('driver_id');
				
				
				if ($driver_id != '') {
					$checkDriver = $ci->app_model->get_all_details(DRIVERS, array('_id' => MongoID($driver_id)));
					if ($checkDriver->num_rows() == 1) {
						
						$ride_type = "Normal";	#(Normal / Share)
						if(isset($checkDriver->row()->ride_type)){
							if($checkDriver->row()->ride_type!=""){
								$ride_type = $checkDriver->row()->ride_type;
							}
						}
						
						
						if($ride_type!=""){
							
							$duty_ride = "";
							if(isset($checkDriver->row()->duty_ride)){
								if($checkDriver->row()->duty_ride!=""){
									$duty_ride = $checkDriver->row()->duty_ride;
								}
							}
							
							$mode_bre = "";
							if(isset($checkDriver->row()->mode)){
								if($checkDriver->row()->mode!=""){
									$mode_bre = $checkDriver->row()->mode;
								}
							}
							
							if($duty_ride!="" || $mode_bre=="Booked"){
								$checkRide = $ci->app_model->get_driver_active_trips($driver_id,$duty_ride,$ride_type);								
								if($checkRide->num_rows() >0) {
									$ridesArr = array();
									$locArr = array();
									foreach($checkRide->result() as $rides){
										$ride_info = array();
										$user_id = $rides->user['id'];
										
										$userVal = $ci->app_model->get_selected_fields(USERS, array('_id' => MongoID($user_id)), array('_id', 'user_name', 'email', 'image', 'avg_review', 'phone_number', 'country_code'));
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
											
											$pickup_time = date("h:i A jS M, Y",MongoEPOCH($rides->booking_information['est_pickup_date']));
											if($rides->ride_status=="Onride" || $rides->ride_status=="Finished" || $rides->ride_status=="Completed")
											$pickup_time = date("h:i A jS M, Y", MongoEPOCH($rides->booking_information['pickup_date']));
											
											$ride_info = array('user_id' => (string)$userVal->row()->_id,
												'user_name' => (string)$userVal->row()->user_name,
												'user_email' => (string)$userVal->row()->email,
												'phone_number' => (string) $userVal->row()->country_code . $userVal->row()->phone_number,
												'user_image' => (string)base_url() . $user_image,
												'user_review' => (string)floatval($user_review),
												'pickup_location' => (string)$rides->booking_information['pickup']['location'],
												'pickup_lat' => (string)$rides->booking_information['pickup']['latlong']['lat'],
												'pickup_lon' => (string)$rides->booking_information['pickup']['latlong']['lon'],
												'pickup_time' => $pickup_time,
												'drop_location' => "0",
												'drop_loc' => "",
												'drop_lat' => "",
												'drop_lon' => ""
											);
											
											if(array_key_exists("drop",$rides->booking_information)){
												if($rides->booking_information['drop']['location']!=""){
													$ride_info["drop_location"] = (string)1;
													$ride_info["drop_loc"] = (string)$rides->booking_information['drop']['location'];
													$ride_info["drop_lat"] = (string)$rides->booking_information['drop']['latlong']['lat'];
													$ride_info["drop_lon"] = (string)$rides->booking_information['drop']['latlong']['lon'];
												}
											}
										}
										
										if(isset($rides->pool_ride)){
											if($rides->pool_ride=="Yes"){
												$ride_info["no_of_seat"] = (string)$rides->no_of_seat;
												$ride_info["max_no_of_seat"] = (string)2;
												
												$lA = array_reverse($rides->booking_information['pickup']['latlong']);
												$lA['txt'] = "Pickup ".$rides->user["name"];
												$locArr[] = $lA;
												if(array_key_exists("drop",$rides->booking_information)){
													if($rides->booking_information['drop']['location']!=""){
														$lA = array_reverse($rides->booking_information['drop']['latlong']);
														$lA['txt'] = "Drop ".$rides->user["name"];
														$locArr[] = $lA;
													}
												}
											}else{
												$lA = array_reverse($rides->booking_information['pickup']['latlong']);
												$lA['txt'] = "Pickup ".$rides->user["name"];
												$locArr[] = $lA;
												if(array_key_exists("drop",$rides->booking_information)){
													if($rides->booking_information['drop']['location']!=""){
														$lA = array_reverse($rides->booking_information['drop']['latlong']);
														$lA['txt'] = "Drop ".$rides->user["name"];
														$locArr[] = $lA;
													}
												}
											}
										}else{											
											if($rides->ride_status=="Confirmed"){
												if(isset($checkDriver->row()->loc)){
													if($checkDriver->row()->loc!=""){
														$lA = array_reverse($checkDriver->row()->loc);
													}
												}
												$lA['txt'] = "Your Location";
												$locArr[] = $lA;
												$lA = array_reverse($rides->booking_information['pickup']['latlong']);
												$lA['txt'] = "Pickup ".$rides->user["name"];
												$locArr[] = $lA;
											}else if($rides->ride_status=="Arrived"){
												$locArr = array();
												$lA = array_reverse($rides->booking_information['pickup']['latlong']);
												$lA['txt'] = "Pickup ".$rides->user["name"];
												$locArr[] = $lA;
												if(array_key_exists("drop",$rides->booking_information)){
													if($rides->booking_information['drop']['location']!=""){
														$lA = array_reverse($rides->booking_information['drop']['latlong']);
														$lA['txt'] = "Drop ".$rides->user["name"];
														$locArr[] = $lA;
													}
												}
											}else{
												$lA = array_reverse($rides->booking_information['pickup']['latlong']);
												$lA['txt'] = "Pickup ".$rides->user["name"];
												$locArr[] = $lA;
												if(array_key_exists("drop",$rides->booking_information)){
													if($rides->booking_information['drop']['location']!=""){
														$lA = array_reverse($rides->booking_information['drop']['latlong']);
														$lA['txt'] = "Drop ".$rides->user["name"];
														$locArr[] = $lA;
													}
												}
											}											
										}
										$trip_type = "Normal";
										if(isset($rides->pool_ride)){
											if($rides->pool_ride=="Yes"){
												$trip_type = "Share";
											}
										}
										if(isset($rides->pool_id) && $rides->pool_id!=""){
												$trip_type = "Share";
										}
										
										$btn_group = 0;
										$ride_status = $rides->ride_status;
										
										$ride_status_case = $rides->ride_status;										
										if($rides->ride_status=="Completed"){
											if(isset($rides->drs) && $rides->drs=="0"){
												$ride_status_case = "Ratting";
											}
										}
										
										switch($ride_status_case){
											case "Booked": $btn_group = 1; break;		# No Buttons
											case "Confirmed": $btn_group = 2; break;	# Arrived and Cancelled
											case "Arrived": $btn_group = 3; break;		# Begin and Cancelled
											case "Onride": $btn_group = 4; break;		# End
											case "Finished": $btn_group = 5; break;		# Payment
											case "Completed": $btn_group = 6; break;	# No Buttons
											case "Cancelled": $btn_group = 7; break;	# No Buttons
											case "Ratting": $btn_group = 8; break;		# Ratting
										}
										
										$invoice_src = '';
										if($ride_status == "Completed"){
											$invoice_path = 'trip_invoice/'.$ride_id.'_large.jpg'; 
											if(file_exists($invoice_path)) {
												$invoice_src = base_url().$invoice_path;
											}
										}
										
										$fare_summary = array();
										if($ride_status == "Finished" || $ride_status == "Completed"){
											$fare_summary = array();
											if($trip_type == "Normal"){
												if (isset($rides->total['base_fare'])) {
													if ($rides->total['base_fare'] >= 0) {
														$fare_summary[] = array("title"=>(string)$ci->format_string("Base fare", "fare_summary_base_fare"),
																							"value"=>(string)round($rides->total['base_fare'],2)
																							);
													}
												}
												if (isset($rides->total['peak_time_charge'])) {
													if ($rides->total['peak_time_charge'] > 0) {
														$fare_summary[] = array("title"=>(string)$ci->format_string("Peak time fare", "fare_summary_peak_time_fare").' ('.floatval($rides->fare_breakup['peak_time_charge']).'X)',
																							"value"=>(string)round($rides->total['peak_time_charge'],2)
																							);
													}
												}
												if (isset($rides->total['night_time_charge'])) {
													if ($rides->total['night_time_charge'] > 0) {
														$fare_summary[] = array("title"=>(string)$ci->format_string("Night time fare", "fare_summary_night_time_fare").' ('.floatval($rides->fare_breakup['night_charge']).'X)',
																							"value"=>(string)round($rides->total['night_time_charge'],2)
																							);
													}
												}
											}
											if (isset($rides->total['total_fare'])) {
												if ($rides->total['total_fare'] >= 0) {
													$fare_summary[] = array("title"=>(string)$ci->format_string("Subtotal", "fare_summary_total"),
																						"value"=>(string)round($rides->total['total_fare'],2)
																						);
												}
											}
											
											if (isset($rides->total['coupon_discount'])) {
												if ($rides->total['coupon_discount'] > 0) {
													$fare_summary[] = array("title"=>(string)$ci->format_string("Discount amount", "fare_summary_coupon_discount"),
																						"value"=>(string)round($rides->total['coupon_discount'],2)
																						);
												}
											}
											
											if (isset($rides->total['service_tax'])) {
												if ($rides->total['service_tax'] > 0) {
													$fare_summary[] = array("title"=>(string)$ci->format_string("Service tax", "fare_summary_service_tax"),
																						"value"=>(string)round($rides->total['service_tax'],2)
																						);
												}
											}
																						
											if (isset($rides->total['grand_fare'])) {
												if ($rides->total['grand_fare'] >= 0) {
													$fare_summary[] = array("title"=>(string)$ci->format_string("Grand Total", "fare_summary_grand_fare"),
																						"value"=>(string)round($rides->total['grand_fare'],2)
																						);
												}
											}
											
											if (isset($rides->total['tips_amount'])) {
												if ($rides->total['tips_amount'] > 0) {
													$fare_summary[] = array("title"=>(string)$ci->format_string("Tips amount", "fare_summary_tips"),
																						"value"=>(string)round($rides->total['tips_amount'],2)
																						);
												}
											}
											if (isset($rides->total['wallet_usage'])) {
												if ($rides->total['wallet_usage'] > 0) {
													$fare_summary[] = array("title"=>(string)$ci->format_string("Wallet used amount", "fare_summary_wallet_used"),
																						"value"=>(string)round($rides->total['wallet_usage'],2)
																						);
												}
											}
											
											if (isset($rides->total['paid_amount'])) {
												if ($rides->total['paid_amount'] > 0) {
													$fare_summary[] = array("title"=>(string)$ci->format_string("Paid Amount", "fare_summary_paid_amount"),
																						"value"=>(string)round($rides->total['paid_amount'],2)
																						);
												}
											}
											
											
											$distance_unit = $ci->data['d_distance_unit'];
											if(isset($rides->fare_breakup['distance_unit'])){
												$distance_unit = strtolower($rides->fare_breakup['distance_unit']);
											}
                                            
                                            
                                            $display_distance_unit = $ci->format_string($distance_unit, $distance_unit);
                                            
                                            
											$summaryArr = array();
											$min_short = $ci->format_string('min', 'min_short');
											$mins_short = $ci->format_string('mins', 'mins_short');
											
											if (isset($rides->summary)) {
												if (is_array($rides->summary)) {
													foreach ($rides->summary as $key => $values) {
														if($key=="ride_distance"){
															$summaryArr[] = array("title"=>(string)$ci->format_string("Trip Distance", "trip_summary_trip_distance"),
																						"value"=>(string) $values,
																						"unit"=>(string) $display_distance_unit
																						);
														}else if($key=="ride_duration"){
															if($values<=1){
																$unit = $min_short;
															}else{
																$unit = $mins_short;
															}
															$summaryArr[] = array("title"=>(string)$ci->format_string("Trip Duration", "trip_summary_trip_duration"),
																						"value"=>(string) $values,
																						"unit"=>(string) $unit
																						);
														}else if($key=="waiting_duration"){
															if($values>0){
																if($values<=1){
																	$unit = $min_short;
																}else{
																	$unit = $mins_short;
																}
																$summaryArr[] = array("title"=>(string)$ci->format_string("Waiting Duration", "trip_summary_waiting_duration"),
																							"value"=>(string) $values,
																							"unit"=>(string) $unit
																							);
															}
														}                                 
													}
												}
											}
																	
											$need_payment = "1";
											$receive_cash = "0";
											$req_payment = "0";
											if ($ci->config->item('pay_by_cash') != '' && $ci->config->item('pay_by_cash') != 'Disable') {
												$receive_cash = "1";
											}
											
											$payArr = $ci->app_model->get_all_details(PAYMENT_GATEWAY,array("status"=>"Enable"));
											if($payArr->num_rows()>0){
												$req_payment = "1";
											}
											$pay_status = '';
											if (isset($rides->pay_status)) {
												$pay_status = $rides->pay_status;
												if($pay_status == 'Paid'){
													$need_payment = "0";
												}
											}
											$payable_amount = 0;$grand_fare = 0;$total_paid = 0;$wallet_usage = 0;$tips_amount = 0;
											if (isset($rides->total['grand_fare'])) {
												$grand_fare = $rides->total['grand_fare'];
											}
											if (isset($rides->total['tips_amount'])) {
												$tips_amount = $rides->total['tips_amount'];
											}
											if (isset($rides->total['wallet_usage'])) {
												$wallet_usage = $rides->total['wallet_usage'];
											}
											if (isset($rides->total['paid_amount'])) {
												$total_paid = $rides->total['paid_amount'];
											}
											$payable_amount = ($grand_fare+$tips_amount)-($wallet_usage+$total_paid);
											
											$payment_timeout = $ci->data['user_timeout'];
											$fare_info["need_payment"] = $need_payment;
											$fare_info["receive_cash"] = $receive_cash;
											$fare_info["req_payment"] = $req_payment;
											$fare_info["payment_timeout"] = $payment_timeout;
											$fare_info["total_payable_amount"] = number_format($payable_amount,2);
											$fare_info["trip_summary"] = $summaryArr;
										}
										
										$car_icon = base_url().ICON_MAP_CAR_IMAGE;
										if($trip_type!="Share"){
											$cat_id = ""; 
											if(isset($rides->booking_information['service_id'])) $cat_id = $rides->booking_information['service_id'];
											if($cat_id!=""){
												$categoryInfo = $ci->app_model->get_selected_fields(CATEGORY, array('_id' => MongoID($cat_id)), array('_id','icon_car_image'));
												if ($categoryInfo->num_rows() > 0) {
													if(isset($categoryInfo->row()->icon_car_image)){
														$car_icon = base_url() . ICON_IMAGE . $categoryInfo->row()->icon_car_image;
													}											
												}
											}
										}else{
											if ($ci->config->item('pool_map_car_image')!=""){
												$car_icon = base_url().ICON_IMAGE.$ci->config->item('pool_map_car_image');
											}
										}
										$rArr = array_merge(array("ride_id"=>(string)$rides->ride_id,
																				"currency" => $rides->currency,
																				"ride_status"=>(string)$ride_status,
																				"btn_group"=>(string)$btn_group,
																				"car_icon"=>(string)$car_icon,
																				"invoice_src"=>(string)$invoice_src
																		),$ride_info
																);
										
										$fare_info["fare_summary"] = $fare_summary;
										$rArr = array_merge($rArr,$fare_info);
										
										$ridesArr[] = $rArr;
									}
									
									if($ride_type=="Share"){
										$active_ride = $duty_ride;
										if(!empty($ridesArr)){
											$active_ride = $ridesArr[0]['ride_id'];
											
											if(count($ridesArr)>1){
												if($ridesArr[1]['ride_status']=='Finished' || $ridesArr[1]['btn_group']=='8'){
													$active_ride = $ridesArr[1]['ride_id'];
												}
											}
											
											
										}										
									}else{
										$active_ride = $duty_ride;
									}
									
									$toll_parking_status = '0';
									if($ci->config->item('toll_parking_status') == 'ON') $toll_parking_status = '1';
									
									$responseArr['status'] = '1';
									$responseArr['ride_view'] = 'stay';
									$responseArr['response'] = array('ride_type'=>(string)$ride_type,
																					'duty_id'=>(string)$duty_ride,
																					'active_ride'=>(string)$active_ride,
																					'rides'=>$ridesArr,
																					'map_locations'=>$locArr,
																					'toll_parking_status' => (string) $toll_parking_status,
																					
																				);
								}else{
									$responseArr['response'] = $ci->format_string('Currently no rides are available','currently_no_rides_are_available');
								}
							}else{
								$responseArr['response'] = $ci->format_string('Currently no rides are available','currently_no_rides_are_available');
							}
						}else{
							$responseArr['response'] = $ci->format_string('Currently no rides are available','currently_no_rides_are_available');
						}
					} else {
						$responseArr['response'] = $ci->format_string('Authentication Failed','authentication_failed');
					}
				} else {
					$responseArr['response'] = $ci->format_string("Some Parameters are missing","some_parameters_missing");
				}
			} catch (MongoException $ex) {
				$responseArr['response'] = $ci->format_string('Error in connection','error_in_connection');
			}
			$json_encode = json_encode($responseArr, JSON_PRETTY_PRINT);
			echo $ci->cleanString($json_encode);
		}
	}
	
	/**
	*
	* This Function returns the trip information to drivers
	*
	**/
	if ( ! function_exists('update_driver_avail_rides')){
		function update_driver_avail_rides($driver_id= "") {
			$ci =& get_instance();
			
			try {
				if($driver_id!="") $driver_id = $ci->input->post('driver_id');
				if ($driver_id != '') {
					$checkDriver = $ci->app_model->get_all_details(DRIVERS, array('_id' => MongoID($driver_id)));
					if ($checkDriver->num_rows() == 1) {
						$active_trips = 0;
						if(isset($checkDriver->row()->ride_type)){
							$curr_duty_ride = "";
							if(isset($checkDriver->row()->duty_ride)){
								$curr_duty_ride = $checkDriver->row()->duty_ride;
							}
							$ride_type = $checkDriver->row()->ride_type;
							$checkAvailRide = $ci->app_model->get_driver_active_trips($driver_id,$curr_duty_ride,$ride_type);
							if($checkAvailRide->num_rows()>0){
								$active_trips = intval($checkAvailRide->num_rows());
							}
						}
						$ci->app_model->update_details(DRIVERS, array("active_trips"=>intval($active_trips)), array('_id' => MongoID($driver_id)));
					}
				}
			} catch (MongoException $ex) { }
		}
	}
	
	
	/**
	*
	*	This function accept a booking by the driver
	*	Param @bookingInfo as Array
	*	Holds all the acceptance information Eg. Driver Id, ride id, latitude, longtitude and distance
	*
	**/	
	if ( ! function_exists('assign_ride')){
		function assign_ride($assignInfo= array()) {
			$ci =& get_instance();
			
			$returnArr['status'] = '0';
			$returnArr['response'] = '';
			$returnArr['ride_view'] = 'stay';
			
			try {
				if(array_key_exists("driver_id",$assignInfo)) $driver_id =  trim($assignInfo['driver_id']); else $driver_id = "";
				if(array_key_exists("ride_id",$assignInfo)) $ride_id =  trim($assignInfo['ride_id']); else $ride_id = "";
				
				 $ref="auto"; if(array_key_exists("ref",$assignInfo)) $ref="manual";
				 
				
				if ($driver_id!="" && $ride_id!="") {
					$checkDriver = $ci->app_model->get_all_details(DRIVERS, array('_id' => MongoID($driver_id)));
					if ($checkDriver->num_rows() == 1) {
						$driver_lat = '';
						if(isset($checkDriver->row()->loc) && $checkDriver->row()->loc['lat'] != ''){
							$driver_lat = $checkDriver->row()->loc['lat'];
						}
						$driver_lon = '';
						if(isset($checkDriver->row()->loc) && $checkDriver->row()->loc['lon'] != ''){
							$driver_lon = $checkDriver->row()->loc['lon'];
						}
						$distance = 0;
						
						
						$company_id = '';
						if(isset($checkDriver->row()->company_id) && $checkDriver->row()->company_id != ''){
							$company_id = $checkDriver->row()->company_id;
						}
						$operator_id = '';
						if(isset($checkDriver->row()->operator_id) && $checkDriver->row()->operator_id != ''){
							$operator_id = $checkDriver->row()->operator_id;
						}
						$checkRide = $ci->app_model->get_all_details(RIDES, array('ride_id' => $ride_id));
						if ($checkRide->num_rows() == 1) {
							$isGo = FALSE;
							if ($checkDriver->row()->ride_type== 'Normal' && $checkDriver->row()->mode== 'Available'){
									$isGo = TRUE;
							}
							if ($checkDriver->row()->ride_type== 'Share'){
									$isGo = TRUE;
							}
							if ($checkRide->row()->ride_status == 'Booked' || $checkRide->row()->driver['id'] == $driver_id) {
								if ($checkRide->row()->ride_status == 'Booked') {
									$userVal = $ci->app_model->get_selected_fields(USERS, array('_id' => MongoID($checkRide->row()->user['id'])), array('_id', 'user_name', 'email', 'image', 'avg_review', 'phone_number', 'country_code', 'push_type', 'push_notification_key'));
									if ($userVal->num_rows() > 0) {
										$service_id = $checkRide->row()->booking_information['service_id'];
										/* Update the ride information with fare and driver details -- Start */
										$pickup_lon = $checkRide->row()->booking_information['pickup']['latlong']['lon'];
										$pickup_lat = $checkRide->row()->booking_information['pickup']['latlong']['lat'];
										$from = $driver_lat . ',' . $driver_lon;
										$to = $pickup_lat . ',' . $pickup_lon;

										$urls = 'https://maps.googleapis.com/maps/api/directions/json?origin=' . $from . '&destination=' . $to . '&alternatives=true&sensor=false&mode=driving'.$ci->data['google_maps_api_key'];
										$gmap = file_get_contents($urls);
										$map_values = json_decode($gmap);
										$routes = $map_values->routes;
										if(!empty($routes)){
											usort($routes, create_function('$a,$b', 'return intval($a->legs[0]->distance->value) - intval($b->legs[0]->distance->value);'));
											
											$distance_unit = $ci->data['d_distance_unit'];
											$duration_unit = 'min';
											if(isset($checkRide->row()->fare_breakup)){
												if($checkRide->row()->fare_breakup['distance_unit']!=''){
													$distance_unit = $checkRide->row()->fare_breakup['distance_unit'];
													$duration_unit = $checkRide->row()->fare_breakup['duration_unit'];
												} 
											}

											$mindistance = 1;
											$minduration = 1;
											$mindurationtext = '';
											if (!empty($routes[0])) {
												#$mindistance = ($routes[0]->legs[0]->distance->value) / 1000;
												$min_distance = $routes[0]->legs[0]->distance->text;
												if (preg_match('/km/',$min_distance)){
													$return_distance = 'km';
												}else if (preg_match('/mi/',$min_distance)){
													$return_distance = 'mi';
												}else if (preg_match('/m/',$min_distance)){
													$return_distance = 'm';
												} else {
													$return_distance = 'km';
												}
												
												$mindistance = floatval(str_replace(',','',$min_distance));
												if($distance_unit!=$return_distance){
													if($distance_unit=='km' && $return_distance=='mi'){
														$mindistance = $mindistance * 1.60934;
													} else if($distance_unit=='mi' && $return_distance=='km'){
														$mindistance = $mindistance * 0.621371;
													} else if($distance_unit=='km' && $return_distance=='m'){
														$mindistance = MongoEPOCH($mindistance) ;
													} else if($distance_unit=='mi' && $return_distance=='m'){
														$mindistance = $mindistance * 0.00062137;
													}
												}
												$mindistance = floatval(round($mindistance,2));
										
										
												$minduration = ($routes[0]->legs[0]->duration->value) / 60;
												$est_pickup_time = (time()) + $routes[0]->legs[0]->duration->value;
												$mindurationtext = $routes[0]->legs[0]->duration->text;
											}

											$fareDetails = $ci->app_model->get_all_details(LOCATIONS, array('_id' => MongoID($checkRide->row()->location['id'])));
											if ($fareDetails->num_rows() > 0) {
												$service_tax = 0.00;
												if (isset($fareDetails->row()->service_tax)) {
													if ($fareDetails->row()->service_tax > 0) {
														$service_tax = $fareDetails->row()->service_tax;
													}
												}
												if (isset($fareDetails->row()->fare[$service_id]) && $service_id!=POOL_ID) {
													$peak_time = '';
													$night_charge = '';
													$peak_time_amount = '';
													$night_charge_amount = '';
													$min_amount = 0.00;
													$max_amount = 0.00;
													$pickup_datetime = MongoEPOCH($checkRide->row()->booking_information['est_pickup_date']);
													$pickup_date = date('Y-m-d',MongoEPOCH($checkRide->row()->booking_information['est_pickup_date']));

													if ($fareDetails->row()->peak_time == 'Yes') {
														$time1 = strtotime($pickup_date . ' ' . $fareDetails->row()->peak_time_frame['from']);
														$time2 = strtotime($pickup_date . ' ' . $fareDetails->row()->peak_time_frame['to']);
														$ptc = FALSE;
														if ($time1 > $time2) {
															if (date('A', $pickup_datetime) == 'PM') {
																if (($time1 <= $pickup_datetime) && (strtotime('+1 day', $time2) >= $pickup_datetime)) {
																	$ptc = TRUE;
																}
															} else {
																if ((strtotime('-1 day', $time1) <= $pickup_datetime) && ($time2 >= $pickup_datetime)) {
																	$ptc = TRUE;
																}
															}
														} else if ($time1 < $time2) {
															if (($time1 <= $pickup_datetime) && ($time2 >= $pickup_datetime)) {
																$ptc = TRUE;
															}
														}
														if ($ptc) {
															$peak_time_amount = $fareDetails->row()->fare[$service_id]['peak_time_charge'];
														}
													}
													if ($fareDetails->row()->night_charge == 'Yes') {
														$time1 = strtotime($pickup_date . ' ' . $fareDetails->row()->night_time_frame['from']);
														$time2 = strtotime($pickup_date . ' ' . $fareDetails->row()->night_time_frame['to']);
														$nc = FALSE;
														if ($time1 > $time2) {
															if (date('A', $pickup_datetime) == 'PM') {
																if (($time1 <= $pickup_datetime) && (strtotime('+1 day', $time2) >= $pickup_datetime)) {
																	$nc = TRUE;
																}
															} else {
																if ((strtotime('-1 day', $time1) <= $pickup_datetime) && ($time2 >= $pickup_datetime)) {
																	$nc = TRUE;
																}
															}
														} else if ($time1 < $time2) {
															if (($time1 <= $pickup_datetime) && ($time2 >= $pickup_datetime)) {
																$nc = TRUE;
															}
														}
														if ($nc) {
															$night_charge_amount = $fareDetails->row()->fare[$service_id]['night_charge'];
														}
													}
													$fare_breakup = array('min_km' => (string) $fareDetails->row()->fare[$service_id]['min_km'],
														'min_time' => (string) $fareDetails->row()->fare[$service_id]['min_time'],
														'min_fare' => (string) $fareDetails->row()->fare[$service_id]['min_fare'],
														'per_km' => (string) $fareDetails->row()->fare[$service_id]['per_km'],
														'per_minute' => (string) $fareDetails->row()->fare[$service_id]['per_minute'],
														'wait_per_minute' => (string) $fareDetails->row()->fare[$service_id]['wait_per_minute'],
														'peak_time_charge' => (string) $peak_time_amount,
														'night_charge' => (string) $night_charge_amount,
														'distance_unit' => (string) $distance_unit,
														'duration_unit' => (string) $duration_unit
													);
												}
											}
											$vehicleInfo = $ci->app_model->get_selected_fields(MODELS, array('_id' => MongoID($checkDriver->row()->vehicle_model)), array('_id', 'name', 'brand_name'));
											$vehicle_model = '';
											if ($vehicleInfo->num_rows() > 0) {
												$vehicle_model = $vehicleInfo->row()->name;
												#$vehicle_model=$vehicleInfo->row()->brand_name.' '.$vehicleInfo->row()->name;
											}
											$driverInfo = array('id' => (string) $checkDriver->row()->_id,
												'name' => (string) $checkDriver->row()->driver_name,
												'email' => (string) $checkDriver->row()->email,
												'phone' => (string) $checkDriver->row()->dail_code . $checkDriver->row()->mobile_number,
												'vehicle_model' => (string) $vehicle_model,
												'vehicle_no' => (string) $checkDriver->row()->vehicle_number,
												'lat_lon' => (string) $driver_lat . ',' . $driver_lon,
												'est_eta' => (string) $mindurationtext
											);
											$history = array('booking_time' => $checkRide->row()->booking_information['booking_date'],
												'estimate_pickup_time' => MongoDATE($est_pickup_time),
												'driver_assigned' => MongoDATE(time())
											);
											$driver_commission = $checkRide->row()->commission_percent;
											if (isset($checkDriver->row()->driver_commission)) {
												$driver_commission = $checkDriver->row()->driver_commission;
											}
											$curr_duty_ride = $ride_id;
											if(isset($checkDriver->row()->duty_ride) && $checkDriver->row()->duty_ride!=''){
												$curr_duty_ride = $checkDriver->row()->duty_ride;
											}
											
											if($driver_commission>100) $driver_commission = 100;
											$rideDetails = array('ride_status' => 'Confirmed',
												'commission_percent' => floatval($driver_commission),
												'driver' => $driverInfo,
												'company_id'=>$company_id,
												'operator_id'=>$operator_id,
												'tax_breakup' => array('service_tax' => $service_tax),
												'booking_information.est_pickup_date' => MongoDATE($est_pickup_time),
												'history' => $history
											);
											$active_trips = 0;
											if($service_id!=POOL_ID){
												$active_trips = 1;
												$rideDetails['fare_breakup'] = $fare_breakup;
											}else if($service_id==POOL_ID){
												$pooling_with = array(); $co_rider = array(); $pool_type = 0;
												
												$checkAvailRide = $ci->app_model->get_driver_active_trips($driver_id,$curr_duty_ride,"Share");
												if($checkAvailRide->num_rows()>0){
													$active_trips = intval($checkAvailRide->num_rows());
												}
												if($active_trips>=1){
													$pool_type = 1;
													$active_trips ++;
													$pooling_with = array("name"=>$checkAvailRide->row()->user["name"],
																					"id"=>$checkAvailRide->row()->user["id"]
																				);
													$co_rider = array("name"=>$checkAvailRide->row()->user["name"],
																					"id"=>$checkAvailRide->row()->user["id"]
																				);
													$ext_pooling_with = array("name"=>$checkRide->row()->user["name"],
																					"id"=>$checkRide->row()->user["id"]
																				);
													$ext_co_rider = array("name"=>$checkRide->row()->user["name"],
																					"id"=>$checkRide->row()->user["id"]
																				);
													if(isset($checkAvailRide->row()->ride_id)){
														$ext_ride_id = (string)$checkAvailRide->row()->ride_id;
														if(isset($checkAvailRide->row()->pooling_with)){
															$ext_pooling_with = $ext_pooling_with;
														}
														if(isset($checkAvailRide->row()->co_rider)){
															$ext_co_rider_val = $checkAvailRide->row()->co_rider;
															$ext_co_rider_val[] = $ext_co_rider;
															$ext_co_rider = $ext_co_rider_val;
														}
														$ext_ride_Arr = array("pooling_with"=>$ext_pooling_with,"co_ride"=>$ext_co_rider);
														$ci->app_model->update_details(RIDES, $ext_ride_Arr, array('ride_id' => $ext_ride_id));
														
														/*	Sending the notification regarding the matching	*/
														$ext_user_id = $checkAvailRide->row()->user["id"];
														if($ext_user_id!=""){
															$extUserVal = $ci->app_model->get_selected_fields(USERS, array('_id' => MongoID($ext_user_id)), array('_id','push_type','push_notification_key'));
															if ($extUserVal->num_rows() > 0) {
																if (isset($extUserVal->row()->push_type)) {
																	if ($extUserVal->row()->push_type != '') {
																		$message = $ci->format_string('Ride has been matched', 'ride_matched','','user',(string)$extUserVal->row()->_id);						
																		$optionsFExt = array('ride_id' => $ext_ride_id);
																		if ($extUserVal->row()->push_type == 'ANDROID') {
																			if (isset($extUserVal->row()->push_notification_key['gcm_id'])) {
																				if ($extUserVal->row()->push_notification_key['gcm_id'] != '') {
																					$ci->sendPushNotification($extUserVal->row()->push_notification_key['gcm_id'], $message, 'track_reload', 'ANDROID', $optionsFExt, 'USER');
																				}
																			}
																		}
																		if ($extUserVal->row()->push_type == 'IOS') {
																			if (isset($extUserVal->row()->push_notification_key['ios_token'])) {
																				if ($extUserVal->row()->push_notification_key['ios_token'] != '') {
																					$ci->sendPushNotification($extUserVal->row()->push_notification_key['ios_token'], $message, 'track_reload', 'IOS', $optionsFExt, 'USER');
																				}
																			}
																		}
																	}
																}
															}
														}
														/*	Sending the notification regarding the matching	*/
													}
												}else{
													$active_trips = 1;
												}
												
												$rideDetails['pooling_with'] = $pooling_with;
												$rideDetails['co_rider'] = $co_rider;
												$rideDetails['pool_type'] = (string)$pool_type;
												$rideDetails['pool_id'] = (string)$curr_duty_ride;
											}
											$driverSRls = array("ride_type"=>"Normal",
																		"duty_ride"=>(string)$ride_id,
																		"share_drop"=>array()
																	);
											if(isset($checkRide->row()->pool_ride)){
												if($checkRide->row()->pool_ride=="Yes"){
													$share_drop = $checkRide->row()->booking_information['drop']['latlong'];
													$driverSRls = array("ride_type"=>"Share",
																				"duty_ride"=>(string)$curr_duty_ride,
																				"share_drop"=>$share_drop
																			);
												}
											}
											/* echo '<pre>'; print_r($ext_ride_Arr); 
											echo '<pre>'; print_r($driverSRls); 
											echo '<pre>'; print_r($rideDetails);  die; */
											$checkBooked = $ci->app_model->get_selected_fields(RIDES, array('ride_id' => $ride_id, 'ride_status' => 'Booked'), array('ride_id', 'ride_status'));
											$checkAvailable = $ci->app_model->get_selected_fields(DRIVERS, array('_id' => MongoID($driver_id)), array('mode','duty_ride','ride_type'));
											$availablity = false;
											if ($checkAvailable->row()->mode == 'Available') {
												$availablity = true;
											}else{
												$hasPool_ride = false;
												if(isset($checkAvailable->row()->duty_ride)){
													if ($checkAvailable->row()->duty_ride != '' && $checkAvailable->row()->ride_type == 'Share') {
														$hasPool_ride = true;
													}
												}
											}
																						
											if ($checkBooked->num_rows() > 0 && ($availablity === true || $hasPool_ride == true)) {
												$ci->app_model->update_details(RIDES, $rideDetails, array('ride_id' => $ride_id));
												/* Update the ride information with fare and driver details -- End */

												
												/* Update the driver status to Booked */
												$driverSRls['mode'] = "Booked";
												$driverSRls['active_trips'] = intval($active_trips);
												$ci->app_model->update_details(DRIVERS, $driverSRls, array('_id' => MongoID($driver_id)));

												/* Update the no of rides  */
												$ci->app_model->update_user_rides_count('no_of_rides', $userVal->row()->_id);
												//$ci->app_model->update_driver_rides_count('no_of_rides', $driver_id);

												/* Update Stats Starts */
												$current_date = MongoDATE(strtotime(date("Y-m-d 00:00:00")));
												$field = array('ride_booked.hour_' . date('H') => 1, 'ride_booked.count' => 1);
												$ci->app_model->update_stats(array('day_hour' => $current_date), $field, 1);
												/* Update Stats End */


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
												$driver_profile = array('driver_id' => (string) $checkDriver->row()->_id,
													'driver_name' => (string) $checkDriver->row()->driver_name,
													'driver_email' => (string) $checkDriver->row()->email,
													'driver_image' => (string) base_url() . $driver_image,
													'driver_review' => (string) floatval($driver_review),
													'driver_lat' => floatval($driver_lat),
													'driver_lon' => floatval($driver_lon),
													'min_pickup_duration' => $mindurationtext,
													'ride_id' => $ride_id,
													'phone_number' => (string) $checkDriver->row()->dail_code . $checkDriver->row()->mobile_number,
													'vehicle_number' => (string) $checkDriver->row()->vehicle_number,
													'vehicle_model' => (string) $vehicle_model,
													'pickup_location' => (string) $checkRide->row()->booking_information['pickup']['location'],
													'pickup_lat' => (string) $pickup_lat,
													'pickup_lon' => (string) $pickup_lon
												);
												/* Preparing driver information to share with user -- End */


												/* Preparing user information to share with driver -- Start */
												if ($userVal->row()->image == '') {
													$user_image = USER_PROFILE_IMAGE_DEFAULT;
												} else {
													$user_image = USER_PROFILE_IMAGE . $userVal->row()->image;
												}
												$user_review = 0;
												if (isset($userVal->row()->avg_review)) {
													$user_review = $userVal->row()->avg_review;
												}
												$user_profile = array('user_name' => $userVal->row()->user_name,
													'user_email' => $userVal->row()->email,
													'phone_number' => (string) $userVal->row()->country_code . $userVal->row()->phone_number,
													'user_image' => base_url() . $user_image,
													'user_review' => floatval($user_review),
													'ride_id' => $ride_id,
													'pickup_location' => $checkRide->row()->booking_information['pickup']['location'],
													'pickup_lat' => $pickup_lat,
													'pickup_lon' => $pickup_lon,
													'pickup_time' => get_time_to_string("h:i A jS M, Y",  MongoEPOCH($checkRide->row()->booking_information['actual_pickup_date']))
												);
												/* Preparing user information to share with driver -- End */

												/* Sending notification to user regarding booking confirmation -- Start */
												# Push notification
												if (isset($userVal->row()->push_type)) {
													if ($userVal->row()->push_type != '') {
														$message = $ci->format_string('Your ride request confirmed', 'ride_request_confirmed','','user',(string)$userVal->row()->_id);						
														$options = $driver_profile;
														
														$action  = "ride_confirmed";
														if($ref=="manual") $action  = "ride_later_confirmed";
														
														if ($userVal->row()->push_type == 'ANDROID') {
															if (isset($userVal->row()->push_notification_key['gcm_id'])) {
																if ($userVal->row()->push_notification_key['gcm_id'] != '') {
																	$ci->sendPushNotification($userVal->row()->push_notification_key['gcm_id'], $message, $action, 'ANDROID', $driver_profile, 'USER');
																}
															}
														}
														if ($userVal->row()->push_type == 'IOS') {
															if (isset($userVal->row()->push_notification_key['ios_token'])) {
																if ($userVal->row()->push_notification_key['ios_token'] != '') {
																	$ci->sendPushNotification($userVal->row()->push_notification_key['ios_token'], $message, $action, 'IOS', $driver_profile, 'USER');
																}
															}
														}
													}
												}
												/* Sending notification to user regarding booking confirmation -- End */
												
												$drop_location = 0;
												$drop_loc = '';$drop_lat = '';$drop_lon = '';
												if($checkRide->row()->booking_information['drop']['location']!=''){
													$drop_location = 1;
													$drop_loc = $checkRide->row()->booking_information['drop']['location'];
													$drop_lat = $checkRide->row()->booking_information['drop']['latlong']['lat'];
													$drop_lon = $checkRide->row()->booking_information['drop']['latlong']['lon'];
												}
												$user_profile['drop_location'] = (string)$drop_location;
												$user_profile['drop_loc'] = (string)$drop_loc;
												$user_profile['drop_lat'] = (string)$drop_lat;
												$user_profile['drop_lon'] = (string)$drop_lon;
												
												
												if ($ride_id != '') {
													$checkInfo = $ci->app_model->get_all_details(TRACKING, array('ride_id' => $ride_id));
												
													$latlng = $driver_lat . ',' . $driver_lon;
													$gmap = file_get_contents("https://maps.googleapis.com/maps/api/geocode/json?latlng=" . $latlng . "&sensor=false".$ci->data['google_maps_api_key']);
													$mapValues = json_decode($gmap)->results;
													if(!empty($mapValues)){
														$formatted_address = $mapValues[0]->formatted_address;
														$cuurentLoc = array('timestamp' => MongoDATE(time()),
															'locality' => (string) $formatted_address,
															'location' => array('lat' => floatval($driver_lat), 'lon' => floatval($driver_lon))
														);
														
														if ($checkInfo->num_rows() > 0) {
															$ci->app_model->simple_push(TRACKING, array('ride_id' => (string) $ride_id), array('steps' => $cuurentLoc));
														} else {
															$ci->app_model->simple_insert(TRACKING, array('ride_id' => (string) $ride_id));
															$ci->app_model->simple_push(TRACKING, array('ride_id' => (string) $ride_id), array('steps' => $cuurentLoc));
														}
													}
												}
												
												
												if (empty($user_profile)) {
													$user_profile = json_decode("{}");
												}
												
												if(isset($checkDriver->row()->push_notification['type']) && $checkDriver->row()->push_notification['type']!=''){
													$driver_id = (string)$checkDriver->row()->_id;
													$message = $ci->format_string('You have a new trip', 'u_have_new_trip', '', 'driver', (string)$driver_id);
													if($checkDriver->row()->push_notification['type']=='ANDROID'){
														$condition=array('_id'=>MongoID($driver_id));
														$ci->mongo_db->where($condition)->inc('req_received',1)->update(DRIVERS);
														$ci->sendPushNotification(array($checkDriver->row()->push_notification['key']),$message,'new_trip','ANDROID',$user_profile,'DRIVER');
													}
													if($checkDriver->row()->push_notification['type']=='IOS'){
														$condition=array('_id'=>MongoID($driver_id));
														$ci->mongo_db->where($condition)->inc('req_received',1)->update(DRIVERS);
														$ci->sendPushNotification(array($checkDriver->row()->push_notification['key']),$message,'new_trip','IOS',$user_profile,'DRIVER');
													}
												}
												
												

												$returnArr['status'] = '1';
												$returnArr['response'] = array('user_profile' => $user_profile, 
																							'message' => $ci->format_string("Ride Accepted", "ride_accepted")
																					);
												
												if(isset($checkDriver->row()->last_online_time)){
													$dataArr = array('last_accept_time' => MongoDATE(time()));
													$ci->app_model->update_details(DRIVERS, $dataArr, array('_id' => MongoID($driver_id)));
                                                    $last_online_time=MongoEPOCH($checkDriver->row()->last_online_time);
													update_mileage_system($driver_id,$last_online_time,'free-roaming',$distance,$ci->data['d_distance_unit'],$ride_id);
												}
												
											} else {
												$returnArr['ride_view'] = 'home';
												$returnArr['response'] = $ci->format_string('you are too late, this ride is booked.', 'you_are_too_late_to_book_this_ride');
											}
										} else {
											$returnArr['ride_view'] = 'home';
											$returnArr['response'] = $ci->format_string('Sorry ! We can not fetch information', 'cannot_fetch_location_information_in_map');								
										}
									}else{
										$returnArr['ride_view'] = 'home';
										$returnArr['response'] = $ci->format_string('You cannot accept this ride.', 'you_cannot_accept_this_ride');
									}
								}else{
									$ride_status = $checkRide->row()->ride_status;
									if($ride_status=="Cancelled"){
										$returnArr['ride_view'] = 'home';
										$returnArr['response'] = $ci->format_string('Already this ride has been cancelled', 'already_ride_cancelled');
									}else if($checkRide->row()->driver['id'] == $driver_id){
										$returnArr['ride_view'] = 'detail';
										 $returnArr['response'] = $ci->format_string('Ride Accepted', 'ride_accepted');
									}else{
										$returnArr['ride_view'] = 'home';
										$returnArr['response'] = $ci->format_string('You cannot accept this ride.', 'you_cannot_accept_this_ride');
									}
								}
							} else {
								$returnArr['ride_view'] = 'home';
								$returnArr['response'] = $ci->format_string('you are too late, this ride is booked.', 'you_are_too_late_to_book_this_ride');
							}
						} else {
							$returnArr['ride_view'] = 'home';
							$returnArr['response'] = $ci->format_string("This ride is unavailable", "ride_unavailable");
						}
					} else {
						$returnArr['response'] = $ci->format_string("Driver not found", "driver_not_found");
					}
				}else{
					$returnArr['response'] = $ci->format_string("Some Parameters Missing", "some_parameters_missing");
				}				
			} catch (MongoException $ex) {
				$returnArr['response'] = $ci->format_string("Error in connection", "error_in_connection");
			}
			return $returnArr;
		}
	}

	if (!function_exists('get_referer_name')){
		function get_referer_name($user_id){
			$ci =& get_instance();
			$checkuser = $ci->app_model->get_selected_fields(USERS, array('_id' => MongoID($user_id)),array('user_name'));
			if(isset($checkuser->row()->user_name)){
				return $checkuser->row()->user_name;
			}else{
				return '';
			}
		}
	}
	
	
	if ( ! function_exists('create_and_save_travel_path_in_map')){
		function create_and_save_travel_path_in_map($ride_id) {
            /* for invoice map*/
            $ci =& get_instance();                        
            
            $ride_info = $ci->user_model->get_all_details(RIDES, array('ride_id' => (string)$ride_id));
            if($ride_info->num_rows() == 1){
				$ride_info=$ride_info->row();
				if(!empty($ride_info->booking_information['drop']['latlong']['lat'])&&!empty($ride_info->booking_information['drop']['latlong']['lon'])){
					$drop_lat=$ride_info->booking_information['drop']['latlong']['lat'];
					$drop_lon=$ride_info->booking_information['drop']['latlong']['lon'];
				}else{
					$drop_lat=0;
					$drop_lon=0;
				}
				if(!empty($ride_info->booking_information['pickup']['latlong']['lat'])&&!empty($ride_info->booking_information['pickup']['latlong']['lon'])){
					$pickup_lat=$ride_info->booking_information['pickup']['latlong']['lat'];
					$pickup_lon=$ride_info->booking_information['pickup']['latlong']['lon'];
				}else{
					$pickup_lat=0;
					$pickup_lon=0;
				}
				  
				$centered_lat=($pickup_lat+$drop_lat)/2.0;
				$centered_lon=($pickup_lon+$drop_lon)/2.0;
				  
				$path = '';
				$path .= '|'.$pickup_lat.','.$pickup_lon;
				$pathArr[] = array($pickup_lat,$pickup_lon);
				$ride_begin_time = "";
				if(isset($ride_info->history['begin_ride'])){
					$ride_begin_time = $ride_info->history['begin_ride'];
				}
				$ride_end_time = "";
				if(isset($ride_info->history['end_ride'])){
					$ride_end_time = $ride_info->history['end_ride'];
				}
				$tracking_values= $ci->user_model->get_all_details(TRAVEL_HISTORY,array('ride_id' => $ride_id));
				if(isset($tracking_values->row()->history_end) && !empty($tracking_values->row()->history_end)){
					$chkPath = array();
					foreach($tracking_values->row()->history_end as $track){
						$latlong = $track['lat'].'-'.$track['lon'];
						if(!in_array($latlong,$chkPath)){
							$chkPath[] = $latlong;
							$pathArr[] = array($track['lat'],$track['lon']);
							$path.='|'.$track['lat'].','.$track['lon'];
						}
					}
				}
				
				$live_url= base_url();
				if($_SERVER['HTTP_HOST']=="192.168.1.251" || $_SERVER['HTTP_HOST']=="localhost"){
					$live_url= 'http://project.dectar.com/cabilydemo/';
				}
				
				$dropPath = '';
				if($ride_info->ride_status == 'Completed' || $ride_info->ride_status == 'Finished'){
					$dropPath = "&markers=icon:".$live_url."images/drop_marker.png|".$drop_lat.",".$drop_lon;
					$path .= '|'.$drop_lat.','.$drop_lon;
					$pathArr[] = array($drop_lat,$drop_lon);
				} else {
					$centered_lat= $pickup_lat;
					$centered_lon= $pickup_lon;
				}
				
				
				if(count($pathArr) > 190){
					require_once(APPPATH.'/third_party/LatLongEncoder/class.polylineEncoder.php');
					$polyEncoder = new PolylineEncoder();
					$encodedPath = $polyEncoder->encode($pathArr); 
					if(isset($encodedPath->points)) $path = '|enc:'.$encodedPath->points;
				}
				
				/* Fetching image from google static map  here */	
				
				$url="https://maps.googleapis.com/maps/api/staticmap?center=".$centered_lat.",".$centered_lon."&zoom=auto&size=300x113&sensor=false&markers=icon:".$live_url."images/pickup_marker.png|".$pickup_lat.",".$pickup_lon.$dropPath."&path=color:0x003F87ff|weight:2".$path."&key=".$ci->config->item('google_maps_api_key'); 
				
				
				$img = base64_encode(file_get_contents($url));
				$imgPath = 'trip_invoice/';
				$imgName = $ride_info->ride_id. "_small.jpg";
				$uploadPath = $imgPath . $imgName;
				
				$imageFormat = array('data:image/jpeg;base64', 'data:image/png;base64', 'data:image/jpg;base64', 'data:image/gif;base64');
				$img = str_replace($imageFormat, '', $img);
				$data = base64_decode($img);
				$image = @imagecreatefromstring($data);
				
				if ($image !== false) {
					@imagejpeg($image, $uploadPath, 100);
					@imagedestroy($image);
				}
				
				$url="https://maps.googleapis.com/maps/api/staticmap?center=".$centered_lat.",".$centered_lon."&zoom=auto&size=640x242&sensor=false&markers=icon:".$live_url."images/pickup_marker.png|".$pickup_lat.",".$pickup_lon.$dropPath."&path=color:0x003F87ff|weight:2".$path."&key=".$ci->config->item('google_maps_api_key'); ;

				$img = base64_encode(file_get_contents($url));
				$imgPath = 'trip_invoice/';
				$imgName = $ride_info->ride_id. "_large.jpg";
				$uploadPath = $imgPath . $imgName;
				
				$imageFormat = array('data:image/jpeg;base64', 'data:image/png;base64', 'data:image/jpg;base64', 'data:image/gif;base64');
				$img = str_replace($imageFormat, '', $img);
				$data = base64_decode($img);
				$image = @imagecreatefromstring($data);
					
				if ($image !== false) {
					@imagejpeg($image, $uploadPath, 100);
					@imagedestroy($image);
					$ci->user_model->update_details(RIDES,array('map_created' => 'Yes'), array('ride_id' => $ride_id));
				} else {
					$ci->user_model->update_details(RIDES,array('map_created' => 'No'), array('ride_id' => $ride_id));
				}
			}
		}
	}
		


/* End of file ride_helper.php */
/* Location: ./application/helpers/ride_helper.php */