<?php  if ( ! defined('BASEPATH')) exit('No direct script access allowed');

/**
*
*	Saving ride details for future stats 
*
**/
if ( ! function_exists('get_estimate')){
	function get_estimate($fareReqArr=array(),$callFor='') {
		$ci =& get_instance();
		$returnArr['status'] = '0';
		$returnArr['response'] = '';
		try {
			$user_id = trim($ci->input->post('user_id'));
			$pickup = trim($ci->input->post('pickup'));
			$drop = trim($ci->input->post('drop'));
			$pickup_lat = trim($ci->input->post('pickup_lat'));
			$pickup_lon = trim($ci->input->post('pickup_lon'));
			$drop_lat = trim($ci->input->post('drop_lat'));
			$drop_lon = trim($ci->input->post('drop_lon'));
			$category = trim($ci->input->post('category'));
			$type = intval($ci->input->post('type'));
			$pickup_date = trim($ci->input->post('pickup_date'));
			$pickup_time = trim($ci->input->post('pickup_time'));
            

            $isApp = TRUE;
            if($callFor == 'operator' && is_array($fareReqArr) && !empty($fareReqArr)){
                @extract($fareReqArr);
                $isApp = FALSE;
            }
            
			if (($user_id !="" || $callFor == 'operator') && $pickup_lat !="" && $pickup_lon !="" && $drop_lat !="" && $drop_lon !="" && $category !="") {
				
                $authChk = FALSE;
                
                if($callFor == 'operator'){
                    $authChk = TRUE;
                } else if($user_id != ''){
                    $checkUser = $ci->app_model->get_selected_fields(USERS, array('_id' => MongoID($user_id)), array('email'));
                    if ($checkUser->num_rows() == 1) {
                        $authChk = TRUE;
                    }
                }
                
				if ($authChk) {
					$coordinates = array(floatval($pickup_lon), floatval($pickup_lat));
					$location = $ci->app_model->find_location(floatval($pickup_lon), floatval($pickup_lat));
					if (!empty($location['result'])) {
						if (!empty($location['result'][0]['fare'][$category]) || $category == POOL_ID){
							$location_id = $location['result'][0]['_id'];
							
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
											$pool_map_search_radius = $location['result'][0]['pool_map_search_radius'];
											$has_pool_service = 1;
										}										
									}
								}
							}
															
							if($category != POOL_ID){
								$catCheck = MongoID($category);
								$condition = array('status' => 'Active','_id' => $catCheck);
								$categoryResult = $ci->app_model->get_selected_fields(CATEGORY, $condition, array('name','name_languages'));
								$availCategory = array();
								$etaArr = array();
								$rateCard = array();
									
								$distance_unit = $ci->data['d_distance_unit'];
								if(isset($location['result'][0]['distance_unit'])){
									if($location['result'][0]['distance_unit'] != ''){
										$distance_unit = $location['result'][0]['distance_unit'];
									} 
								}
								if($category == POOL_ID){
									$categoryID = array();
									foreach($location['result'][0]['pool_categories'] as $pool_cat){
										$categoryID[] = MongoID($pool_cat);
									}
								}else{
									$categoryID = $category;
								}
								if ($categoryResult->num_rows() > 0 || $category == POOL_ID) {
									
									$category_drivers = $ci->app_model->get_nearest_driver($coordinates, $categoryID, 1,"","","",$location_id);
									
									$from = $pickup_lat . ',' . $pickup_lon;
									$to = $drop_lat . ',' . $drop_lon;

									$gmap = file_get_contents('https://maps.googleapis.com/maps/api/directions/json?origin=' . $from . '&destination=' . $to . '&alternatives=true&sensor=false&mode=driving'.$ci->data['google_maps_api_key']);
									$map_values = json_decode($gmap);
									$routes = $map_values->routes;
									if(!empty($routes)){
										usort($routes, create_function('$a,$b', 'return intval($a->legs[0]->distance->value) - intval($b->legs[0]->distance->value);'));


										if($pickup=="") $pickup = (string) $routes[0]->legs[0]->start_address;
										if($drop=="") $drop = (string) $routes[0]->legs[0]->end_address;

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
									
										$minduration = round(($routes[0]->legs[0]->duration->value) / 60);
										$mindurationtext = $routes[0]->legs[0]->duration->text;
										$mins = $ci->format_string('mins', 'mins');
                                        
                                        
                                        $peak_time_txt = $ci->format_string('Peak time surcharge', 'peak_time_surcharge');
                                        $night_time_txt = $ci->format_string('Night time charge', 'night_time_charge');
                                        
                                        if(!$isApp){
                                            if ($ci->lang->line('rides_mins_lower') != '') $mins = stripslashes($ci->lang->line('rides_mins_lower')); else $mins = 'mins';
                                            
                                            if($distance_unit == 'km'){
												
                                                
                                                if ($ci->lang->line('rides_km_lower') != '') $disp_distance_unit = stripslashes($ci->lang->line('rides_km_lower')); else $disp_distance_unit = 'km';
                                                
											}else if($distance_unit == 'mi'){
												
                                                if ($ci->lang->line('rides_mi_lower') != '') $disp_distance_unit = stripslashes($ci->lang->line('rides_mi_lower')); else $disp_distance_unit = 'mi';
                                                
											}
                                            
                                            
                                             if ($ci->lang->line('rides_peak_surcharge') != '') $peak_time_txt = stripslashes($ci->lang->line('rides_peak_surcharge')); else $peak_time_txt = 'Peak time surcharge';
                                             
                                             if ($ci->lang->line('dash_night_time_charge') != '') $night_time_txt = stripslashes($ci->lang->line('dash_night_time_charge')); else $night_time_txt = 'Night time charge';
                                            
                                        }
                                        
                                        
										$mindurationtext = $minduration.' '.$mins;
                                        
                                        $mindistancetext = $mindistance.' '.$disp_distance_unit;
										
										$distance = 0;
										if(isset($category_drivers['result'][0]['distance'])) $distance = $category_drivers['result'][0]['distance'];
										$nbddtext = $ci->app_model->calculateETA($distance) . ' ' . $mins;

										$peak_time = '';
										$night_charge = '';
										$peak_time_amount = 1;
										$night_charge_amount = 1;
										$min_amount = 0.00;
										$max_amount = 0.00;

										if ($type == 1) {
											$pickup_datetime = strtotime($pickup_date . ' ' . $pickup_time);
										} else {
											$pickup_datetime = time();
											$pickup_date = date('Y-m-d');
										}
										
										if($category != POOL_ID){
											if ($location['result'][0]['peak_time'] == 'Yes') {
												$time1 = strtotime($pickup_date . ' ' . $location['result'][0]['peak_time_frame']['from']);
												$time2 = strtotime($pickup_date . ' ' . $location['result'][0]['peak_time_frame']['to']);
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
													$peak_time_amount = $location['result'][0]['fare'][$category]['peak_time_charge'];
													$peak_time = $peak_time_txt.' '. $location['result'][0]['fare'][$category]['peak_time_charge'] . 'X';
												}
											}
											if ($location['result'][0]['night_charge'] == 'Yes') {
												$time1 = strtotime($pickup_date . ' ' . $location['result'][0]['night_time_frame']['from']);
												$time2 = strtotime($pickup_date . ' ' . $location['result'][0]['night_time_frame']['to']);
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
													$night_charge_amount = $location['result'][0]['fare'][$category]['night_charge'];
													$night_charge = $night_time_txt.' '. $location['result'][0]['fare'][$category]['night_charge'] . 'X';
												}
											}
											$min_amount = floatval($location['result'][0]['fare'][$category]['min_fare']);
											if (floatval($location['result'][0]['fare'][$category]['min_time']) < floatval($minduration)) {
												$ride_fare = 0;
												$ride_time = floatval($minduration) - floatval($location['result'][0]['fare'][$category]['min_time']);
												$ride_fare = $ride_time * floatval($location['result'][0]['fare'][$category]['per_minute']);
												$min_amount = $min_amount + $ride_fare;
											}
											if (floatval($location['result'][0]['fare'][$category]['min_km']) < floatval($mindistance)) {
												$after_fare = 0;
												$ride_time = floatval($mindistance) - floatval($location['result'][0]['fare'][$category]['min_km']);
												$after_fare = $ride_time * floatval($location['result'][0]['fare'][$category]['per_km']);
												$min_amount = $min_amount + $after_fare;
											}
											
											/**  updated **/
											if($nc && $ptc){
												$night_surge = $min_amount * $night_charge_amount;
												$peak_surge = $min_amount * $peak_time_amount;
												$min_amount = $night_surge + $peak_surge;
											} else {
												$min_amount = $min_amount * $night_charge_amount;
												$min_amount = $min_amount * $peak_time_amount;
											} 
											/**********/
											
											$service_tax = 0;
											if (isset($location['result'][0]['service_tax'])) {
												if ($location['result'][0]['service_tax'] > 0) {
													$service_tax = $location['result'][0]['service_tax'];
												}
											}
                    
											$min_amount = $min_amount + ($min_amount*0.01*$service_tax); 

											$max_amount = $min_amount + ($min_amount*0.01*30);
											$est_amount = $min_amount + ($min_amount*0.01*15);
											
											$note_heading = $ci->format_string('Note', 'note_heading');
											$note_approximate_estimate = $ci->format_string('This is an approximate estimate. Actual cost and travel time may be different.', 'note_approximate_estimate');
											
											
											$cat_name = $categoryResult->row()->name;
											if(isset($categoryResult->row()->name_languages)){
												$langKey = $ci->data['sms_lang_code'];
												$arrVal = $categoryResult->row()->name_languages;
												if(array_key_exists($langKey,$arrVal)){
													if($categoryResult->row()->name_languages[$langKey]!=""){
														$cat_name = $categoryResult->row()->name_languages[$langKey];
													}
												}
											}
											
											$etaArr = array('catrgory_id' => (string) $categoryResult->row()->_id,
												'catrgory_name' => $cat_name,
												'pickup' => (string) $pickup,
												'drop' => (string) $drop,
												'min_amount' => number_format($min_amount, 2),
												'max_amount' => number_format($max_amount, 2),
												'est_amount' => number_format($est_amount, 2),
												'att' => (string) $mindurationtext,
												'nbdd' => (string) $nbddtext,
												'peak_time' => (string) $peak_time,
												'night_charge' => (string) $night_charge,
												'note' => $note_heading.' : '.$note_approximate_estimate,
                                                'distance' => (string) $mindistancetext
											);
											$rateCard['note'] = $note_heading.' : '.$note_approximate_estimate;
											
											$distance_unit = $ci->data['d_distance_unit'];
											if(isset($location['result'][0]['distance_unit'])){
												if($location['result'][0]['distance_unit'] != ''){
													$distance_unit = $location['result'][0]['distance_unit'];
												} 
											}
											if($distance_unit == 'km'){
												$disp_distance_unit = $ci->format_string('km', 'km');
											}else if($distance_unit == 'mi'){
												$disp_distance_unit = $ci->format_string('mi', 'mi');
											}
											$min_short = $ci->format_string('min', 'min_short');		
											$mins_short = $ci->format_string('mins', 'mins_short');						
											if($location['result'][0]['fare'][$category]['min_time']>1){
												$min_time_unit = $mins_short;
											}else{
												$min_time_unit = $min_short;
											}
											$first = $ci->format_string('First', 'first');
											$after = $ci->format_string('After', 'after');
											$ride_time_rate_post = $ci->format_string('Ride time rate post', 'ride_time_rate_post');
											
											$fare = array();
											$fare['min_fare'] = array('amount' => (string) $location['result'][0]['fare'][$category]['min_fare'],
												'text' => $first . ' ' . $location['result'][0]['fare'][$category]['min_km'] . ' ' . $disp_distance_unit);
											$fare['after_fare'] = array('amount' => (string) $location['result'][0]['fare'][$category]['per_km'] . '/' . $disp_distance_unit,
												'text' => $after . ' ' . $location['result'][0]['fare'][$category]['min_km'] . ' ' . $disp_distance_unit);
											$fare['other_fare'] = array('amount' => (string) $location['result'][0]['fare'][$category]['per_minute'] . '/' . $min_short,
												'text' => $ride_time_rate_post . ' ' . $location['result'][0]['fare'][$category]['min_time'] . ' ' . $min_time_unit);
											$rateCard['farebreakup'] = $fare;
										}
									
												
										if (empty($etaArr)) {
											$etaArr = json_decode("{}");
										}
										if (empty($rateCard)) {
											$rateCard = json_decode("{}");
										} 
										
										
										$returnArr['status'] = '1';
										$returnArr['response'] = array('currency' => (string) $ci->data['dcurrencyCode'], 
																		'eta' => $etaArr, 
																		'ratecard' => $rateCard
																	);
									}else{
										$returnArr['response'] = $ci->format_string('Sorry ! We can not fetch information', 'cannot_fetch_location_information_in_map');
									}
								}else{
									$returnArr['response'] = $ci->format_string('Service category not found','service_category_not_found');
								}
							}
							
							if($category == POOL_ID || $has_pool_service == 1){
								$loc_pool_categories = $location['result'][0]['pool_categories'];
								if(!is_array($loc_pool_categories)) $loc_pool_categories = array();								
								if(($category != POOL_ID && in_array($category,$loc_pool_categories)) || $category == POOL_ID){
									$hasRoute = TRUE;
									$responseArr = array('currency' => (string) $ci->data['dcurrencyCode']);																
									if($has_pool_service == 1 && $type != 1){
										$is_pool_service = 1;
										$poolEtaResponse  = get_pool_estimate("Ap");
										if(!empty($poolEtaResponse)){
											if(array_key_exists("no_route_avail",$poolEtaResponse)) $hasRoute = FALSE;
											$responseArr['has_pool_service'] = (string)1;
											$responseArr = array_merge($responseArr,$poolEtaResponse);
										}
									}else{
										$responseArr['has_pool_service'] = (string)0;
									}
									if($hasRoute == TRUE){
										if($category == POOL_ID){
											$responseArr['is_pool_service'] = (string)1;
										}else{
											$responseArr['is_pool_service'] = (string)0;
										}
										$returnArr['status'] = '1';
										if(!is_array($returnArr['response'])) $returnArr['response']= array();
										$returnArr['response'] = array_merge($returnArr['response'],$responseArr);
										if($category == POOL_ID && empty($poolEtaResponse)){
											$returnArr['status'] = '2';
											$returnArr['response'] = $ci->format_string('No driver available right now.', 'no_driver_available_right_now');
										}
									}else{
										$returnArr['status'] = '2';
										$returnArr['response'] = $ci->format_string('Please try with some other drop location', 'no_route_found');
									}
								}
							}
						} else {
							$returnArr['response'] = $ci->format_string('This type of cab service is not available in your location.', 'this_type_of_cab_not_available_location');
						}
					} else {
						$returnArr['response'] = $ci->format_string('Sorry ! We do not provide services in your city yet.', 'service_unavailable_in_your_city');
					}
				} else {
					$returnArr['response'] = $ci->format_string("Invalid User", "invalid_user");
				}
			} else {
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
*	Saving ride details for future stats 
*
**/
if ( ! function_exists('get_pool_estimate')){
	function get_pool_estimate($sR="") {
		$ci =& get_instance();
		$poolEst = array();
		try {
			$user_id = trim($ci->input->post('user_id'));
			$pickup = trim($ci->input->post('pickup'));
			$drop = trim($ci->input->post('drop'));
			$pickup_lat = trim($ci->input->post('pickup_lat'));
			$pickup_lon = trim($ci->input->post('pickup_lon'));
			$drop_lat = trim($ci->input->post('drop_lat'));
			$drop_lon = trim($ci->input->post('drop_lon'));
			$category = trim($ci->input->post('category'));
			$type = intval($ci->input->post('type'));
			$pickup_date = trim($ci->input->post('pickup_date'));
			$pickup_time = trim($ci->input->post('pickup_time'));

			if (is_array($ci->input->post())) {
				$chkValues = count(array_filter($ci->input->post()));
			} else {
				$chkValues = 0;
			}

			if ($pickup !="" && $drop !="" && $pickup_lat !="" && $pickup_lon !="" && $drop_lat !="" && $drop_lon !="" && $category !="") {
				$coordinates = array(floatval($pickup_lon), floatval($pickup_lat));
				$location = $ci->app_model->find_location(floatval($pickup_lon), floatval($pickup_lat));
				if (!empty($location['result'])) {
					$location_id = $location['result'][0]['_id'];						
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
									$pool_map_search_radius = $location['result'][0]['pool_map_search_radius'];
									$has_pool_service = 1;
								}										
							}
						}
					}
					
					if($has_pool_service == 1){
						
						$distance_unit = $ci->data['d_distance_unit'];
						if(isset($location['result'][0]['distance_unit'])){
							if($location['result'][0]['distance_unit'] != ''){
								$distance_unit = $location['result'][0]['distance_unit'];
							} 
						}
						
						$categoryID = array();
						foreach($location['result'][0]['pool_categories'] as $pool_cat){
							$categoryID[] = MongoID($pool_cat);
						}
						
						if($category == POOL_ID){
							$category_drivers = $ci->app_model->get_nearest_pool_driver($coordinates, $categoryID, 1,'',"","",$location_id);
						}
						if (empty($category_drivers['result'])) {
							$category_drivers = $ci->app_model->get_nearest_driver($coordinates, $categoryID, 1,"","","",$location_id);
						}
						
						
						if (!empty($category_drivers['result'])) {
							$from = $pickup_lat . ',' . $pickup_lon;
							$to = $drop_lat . ',' . $drop_lon;
							$gmap = file_get_contents('https://maps.googleapis.com/maps/api/directions/json?origin=' . $from . '&destination=' . $to . '&alternatives=true&sensor=false&mode=driving'.$ci->data['google_maps_api_key']);
							$map_values = json_decode($gmap);
							$routes = $map_values->routes;
							if(!empty($routes)){
								usort($routes, create_function('$a,$b', 'return intval($a->legs[0]->distance->value) - intval($b->legs[0]->distance->value);'));
								
								if($pickup=="") $pickup = (string) $routes[0]->legs[0]->start_address;
								if($drop=="") $drop = (string) $routes[0]->legs[0]->end_address;
								
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
								$mindurationtext = $routes[0]->legs[0]->duration->text;
								$mins = $ci->format_string('mins', 'mins');
								$mindurationtext = $minduration.' '.$mins;
								
								$distance = $category_drivers['result'][0]['distance'];
								$nbddtext = $ci->app_model->calculateETA($distance) . ' ' . $mins;
								
								$peak_time = ''; $night_charge = ''; $peak_time_amount = 1; $night_charge_amount = 1;
								
								if ($type = 1) {
									$pickup_datetime = strtotime($pickup_date . ' ' . $pickup_time);
								} else {
									$pickup_datetime = time();
									$pickup_date = date('Y-m-d');
								}
								
								$est_amount = 0;
								$poolFareResponse = get_pool_fare($mindistance,$minduration,$location_id,$sR);								
								if($poolFareResponse["status"]=="1"){
									$note_heading = $ci->format_string('Note', 'note_heading');
									$note_approximate_estimate = $ci->format_string('This is an approximate estimate. Actual cost and travel time may be different.', 'note_approximate_estimate');
									
									$poolData = array('catrgory_id'=>(string)POOL_ID,
																'catrgory_name'=>(string)$ci->config->item('pooling_name'),
																'pickup' => (string) $pickup,
																'drop' => (string) $drop,
																'est_amount' => number_format($poolFareResponse["est_fare"], 2),
																'att' => (string) $mindurationtext,
																'nbdd' => (string) $nbddtext,
																'note' => $note_heading.' : '.$note_approximate_estimate
															);
									$poolRateCard = array(array("seat"=>"1",
																						"cost"=>(string)number_format($poolFareResponse["passenger"], 2)
																					),
																			array("seat"=>"2",
																						"cost"=>(string)number_format($poolFareResponse["co_passenger"], 2)
																					)
																		);
																
									$poolEst =  array("pool_eta"=>$poolData,
															"pool_ratecard"=>$poolRateCard,
														);
								}else{
									if($sR=="Ap") $poolEst["no_route_avail"]="1";
								}
							}else{
								if($sR=="Ap") $poolEst["no_route_avail"]="1";
							}
						}
					}
				}
			}
		} catch (MongoException $ex) { }		
		return $poolEst; 
	}
}
	
/**
*
*	Saving ride details for future stats 
*
**/
if ( ! function_exists('get_pool_fare')){
	function get_pool_fare($cPdistance,$cPduration,$cPlocation,$pfA="") {
		$ci =& get_instance();
		$poolFare = array("status"=>"0","est_fare"=>0.00,"passenger"=>0.00,"co_passenger"=>0.00);
        
		try {
			if ($cPdistance !="" && $cPduration !="" && $cPlocation !=""){
				$pCondition = array("_id"=>MongoID($cPlocation),'status' => 'Active');
				$locationsVal = $ci->app_model->get_all_details(LOCATIONS, $pCondition);
               
				if ($locationsVal->num_rows() > 0) {
					$catFAr =  array();
					$est_datetime = time();
					$est_date = date("Y-m-d");
					$pool_categoriesV = $locationsVal->row()->pool_categories; if(!is_array($pool_categoriesV)) $pool_categoriesV= array();
					$ptc = FALSE; $ntc = FALSE;
					if ( isset($locationsVal->row()->peak_time) && $locationsVal->row()->peak_time == 'Yes') {
						$time1 = strtotime($est_date . ' ' . $locationsVal->row()->peak_time_frame['from']);
						$time2 = strtotime($est_date . ' ' . $locationsVal->row()->peak_time_frame['to']);
						$ptc = FALSE;
						if ($time1 > $time2) {
							if (date('a', $est_datetime) == 'PM') {
								if (($time1 <= $est_datetime) && (strtotime('+1 day', $time2) >= $est_datetime)) {
									$ptc = TRUE;
								}
							} else {
								if ((strtotime('-1 day', $time1) <= $est_datetime) && ($time2 >= $est_datetime)) {
									$ptc = TRUE;
								}
							}
						} else if ($time1 < $time2) {
							if (($time1 <= $est_datetime) && ($time2 >= $est_datetime)) {
								$ptc = TRUE;
							}
						}
					}
					if ( isset($locationsVal->row()->night_charge) && $locationsVal->row()->night_charge == 'Yes') {
						$time1 = strtotime($est_date . ' ' . $locationsVal->row()->night_time_frame['from']);
						$time2 = strtotime($est_date . ' ' . $locationsVal->row()->night_time_frame['to']);
						$ntc = FALSE;
						if ($time1 > $time2) {
							if (date('a', $est_datetime) == 'PM') {
								if (($time1 <= $est_datetime) && (strtotime('+1 day', $time2) >= $est_datetime)) {
									$ntc = TRUE;
								}
							} else {
								if ((strtotime('-1 day', $time1) <= $est_datetime) && ($time2 >= $est_datetime)) {
									$ntc = TRUE;
								}
							}
						} else if ($time1 < $time2) {
							if (($time1 <= $est_datetime) && ($time2 >= $est_datetime)) {
								$ntc = TRUE;
							}
						}
					}
					$service_tax = 0.00;
					if (isset($locationsVal->row()->service_tax)) {
						if ($locationsVal->row()->service_tax > 0) {
							$service_tax = $locationsVal->row()->service_tax;
						}
					}
					foreach($locationsVal->row()->fare as $category=>$cFare){
						if(in_array($category,$pool_categoriesV)){
							
							#echo $category.'-';
							$cEFare = 0;
							/*	Start Calculate the fare for category	*/
							$cEFare = floatval($locationsVal->row()->fare[$category]['min_fare']);
							
							if (floatval($locationsVal->row()->fare[$category]['min_time']) < floatval($cPduration)) {
								$ride_fare = 0;
								$ride_time = floatval($cPduration) - floatval($locationsVal->row()->fare[$category]['min_time']);
								$ride_fare = $ride_time * floatval($locationsVal->row()->fare[$category]['per_minute']);
								$cEFare = $cEFare + $ride_fare;
							}
							
							if (floatval($locationsVal->row()->fare[$category]['min_km']) < floatval($cPdistance)) {
								$after_fare = 0;
								$ride_time = floatval($cPdistance) - floatval($locationsVal->row()->fare[$category]['min_km']);
								$after_fare = $ride_time * floatval($locationsVal->row()->fare[$category]['per_km']);
								$cEFare = $cEFare + $after_fare;
							}
							if ($ptc) {
								$cEFare = $cEFare * $locationsVal->row()->fare[$category]['peak_time_charge'];
							}
							if ($ntc) {
								$cEFare = $cEFare * $locationsVal->row()->fare[$category]['night_charge'];
							}
							/*	End Calculate the fare for this category	*/
							$catFAr[] = $cEFare;
						}
					}
					#var_dump($catFAr);
					$pFare = min($catFAr);		#	Get the minimum category fare from the array
                      
					if($pFare>0){
						if(isset($locationsVal->row()->pool_fare)){
							if(is_array($locationsVal->row()->pool_fare)){
								$pool_fare = $locationsVal->row()->pool_fare;
								$min_cat_fare = number_format($pFare,2,'.','');
								$passengerFare = 0;
								$est_fare_fl = 0;
								$single_percent = 0;$double_percent = 0;
								if(isset($pool_fare["passenger"]) && $pool_fare["passenger"]>0){
									$single_percent = $pool_fare["passenger"];
									$est_fare_fl = ($min_cat_fare*0.01*$pool_fare["passenger"]);
									$service_tax_amount = number_format(($est_fare_fl*0.01*$service_tax),2,'.','');
									$est_fare = number_format(($est_fare_fl+$service_tax_amount),2,'.','');
									$passengerFare = $est_fare;
								}
								if(isset($pool_fare["co_passenger"]) && $pool_fare["co_passenger"]>0){
									$double_percent = $pool_fare["co_passenger"];
									$co_passengerFare = $est_fare_fl + ($est_fare_fl*0.01*$pool_fare["co_passenger"]);
									$service_tax_amount = number_format(($co_passengerFare*0.01*$service_tax),2,'.','');
									$co_passengerFare = number_format(($co_passengerFare+$service_tax_amount),2,'.','');
								}
								$base_fare = $min_cat_fare;
								$poolFare = array("status"=>"1",
															"est_fare"=>$est_fare,
															"tax"=>$service_tax_amount,
															"tax_percent"=>$service_tax,
															"base_fare"=>$base_fare,
															"single_percent"=>$single_percent,
															"double_percent"=>$double_percent,
															"passenger"=>$passengerFare,
															"co_passenger"=>$co_passengerFare
														);
                                                      
							}
						}
					}
				}
			}
		} catch (MongoException $ex) {
			return $poolFare;
		}
		return $poolFare;
	}
}

/* End of file pool_helper.php */
/* Location: ./application/helpers/pool_helper.php */