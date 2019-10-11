<?php if (!defined('BASEPATH')) exit('No direct script access allowed');
/*
*
* This model contains all db functions related to mobile user application 
* @author Casperon
*
*/

class App_model extends My_Model {

    public function __construct() {
        parent::__construct();
    }

    /**
     *
     * This function return the location if the the given coordinates will be inside the location boundary
     * 	Array $coordinates
     *
     * */
	public function find_location($longitude, $latitude,$app = 'No') {
		if($app=='No'){
			$option = array("loc" => array('$geoIntersects' => array('$geometry' => array("type"=>"Point",
																																		"coordinates"=>array($longitude,$latitude)
																																		)
																									)
															)
									);
		}else{
			$option = array("status"=>"Active",
									"loc" => array('$geoIntersects' => array('$geometry' => array("type"=>"Point",
																																		"coordinates"=>array($longitude,$latitude)
																																		)
																									)
															)
									);			
		}
        
     
        $this->mongo_db->where($option);
        $res = $this->mongo_db->get(LOCATIONS);
        
		$return['result'] = array();
		if($res->num_rows()>0){
			$return['result'] = $res->result_array();
		}
        return $return;
    }

    /**
     *
     * This function return the location if the the given coordinates will be inside the location boundary
     * 	Array $coordinates
     *
     * */
    public function location_exist($longitude, $latitude) {
		$option = array("loc" => array('$geoIntersects' => array('$geometry' => array("type"=>"Point","coordinates"=>array($longitude,$latitude)))));
        $this->mongo_db->where($option);
        $res = $this->mongo_db->get(LOCATIONS);
		
		$return['result'] = array();
		if($res->num_rows()>0){
			$return['result'] = $res->result_array();
		}
        return $return;
    }

    /**
     *
     * This function return the location  whether if availabel or else return false 
     * 	Array $southwest
     * 	Array $northeast
     *
     * */
    public function find_location_within_boundary($southwest = array(), $northeast = array()) {
        $option = array("location" => array('$geoWithin' => array('$box' => array($southwest, $northeast))));
        $this->mongo_db->select(array('city', 'location', 'bounds'));
        $this->mongo_db->where($option);
        $res = $this->mongo_db->get(LOCATIONS);
        return $res;
    }

    /**
     *
     * This function return the drivers list in a selected category
     * 	Array $coordinates
     * 	String $category
     * 	Number $limit
     *
     * */
    public function get_nearest_driver($coordinates = array(), $category, $limit=10,$distance_unit='',$requested_drivers= array(),$global="",$location = "",$reqType='share',$genderPref=FALSE) {
   
		$map_searching_radius = $this->config->item('map_searching_radius');
		if($map_searching_radius<1000){
			$map_searching_radius = 1000;
		}
		if($global=="global"){
			$map_searching_radius = 100000;
		}
		
		$distanceMultiplier = 0.001;
		if($distance_unit == 'km'){
			$distanceMultiplier = 0.001;
		} else if($distance_unit == 'mi'){
			$distanceMultiplier = 0.000621371;
		} else if($distance_unit == 'm'){
			$distanceMultiplier = 1;
		}
		#print_R($category);die;
        if(!is_array($requested_drivers)&& empty($requested_drivers)){
			$requested_drivers=array();
		}		
		
		$admin_muti_vehicle_status = $this->config->item('multi_category_option');
		if($admin_muti_vehicle_status == ''){
			$admin_muti_vehicle_status = 'OFF';
		} 
		
        $matchArr =    array('availability' => array('$eq' => 'Yes'),
										'status' => array('$eq' => 'Active'),
										'verify_status' => array('$eq' => 'Yes'),
										'last_active_time' => array('$gte' => MongoDATE(time()-1800)),
										#'_id'=>array('$nin'=>$requested_drivers)
									);
		if($genderPref!=FALSE && $genderPref!=""){
			$matchArr["gender"] = array('$eq' =>strtolower($genderPref));
		}
		
      
		if(is_array($category) && $reqType == 'share'){  
			if($location!=""){ 
				$this->mongo_db->select(array('pool_map_search_radius'));
				$this->mongo_db->where(array('_id' => MongoID($location)));
				$res = $this->mongo_db->get(LOCATIONS);
				if($res->num_rows()>0){
					if(isset($res->row()->pool_map_search_radius)){
						if($res->row()->pool_map_search_radius>=1000){
							$map_searching_radius = $res->row()->pool_map_search_radius;
						}
					}
				}
			}
			$matchArr["category"] = array('$in' =>$category);
			$matchArr["active_trips"] = array('$lt' =>intval(2));
			$matchArr['$or'] = array(array('mode' => array('$eq' =>'Booked'),'ride_type'=>array('$exists' =>true,'$eq' => 'Share')),
												array('mode' => array('$eq' =>'Available'),'ride_type' => array('$exists' =>true,'$eq' =>"Normal")),
												array('mode' => array('$eq' =>'Available'),'ride_type' => array('$exists' =>true,'$eq' =>"Share")),
												array('mode' => array('$eq' =>'Available'),'ride_type' => array('$exists' =>true,'$eq' =>"")),
												array('mode' => array('$eq' =>'Available'),'ride_type'=>array('$exists' =>false))
											);
		}else{
            if(is_array($category) && $reqType == 'all'){
                $matchArr["category"] = array('$in' =>$category);
            } else {
				if(is_array($category)){
					$aCatArr = array();
					foreach($category as $key => $val){
						$aCatArr[] = (string) $val; 
					} 
					$matchArr['$or'] = array(array('category'=>array('$in' =>$category)),
																		array('multi_car_status' => array('$eq' =>'ON'),
																			  'additional_category' => array('$eq' =>$aCatArr)
																		)
																);
				} else {
					$matchArr['$or'] = array(array('category'=>array('$eq' => MongoID($category))),
													array('multi_car_status' => array('$eq' =>'ON'),
														  'additional_category' => array('$eq' =>$category)
													)
											);
				}
            }
			$matchArr["active_trips"] = array('$lt' =>intval(1));
			$matchArr["mode"] = array('$eq' => 'Available');
		}
		#echo '<pre>'; print_r($matchArr); die;
		
        $option = array(
            array(
                '$geoNear' => array("near" => array("type" => "Point",
                        "coordinates" => $coordinates
                    ),
                    "spherical" => true,
                    "maxDistance" => intval($map_searching_radius),
                    "includeLocs" => 'loc',
                    "distanceField" => "distance",
                    "distanceMultiplier" => $distanceMultiplier,
                    'num' => (string)$limit
                ),
            ),
            array(
                '$project' => array(
                    'category' => 1,
                    'driver_name' => 1,
                    'loc' => 1,
                    'availability' => 1,
                    'status' => 1,
                    'mode' => 1,
                    'push_notification' => 1,
                    'no_of_rides' => 1,
                    'avg_review' => 1,
                    'total_review' => 1,
                    'distance' => 1,
                    'verify_status' => 1,
                    'last_active_time' => 1,
                    'active_trips' => 1,
                    'ride_type' => 1,
					'gender' => 1,
					'multi_car_status' => 1,
					'additional_category' => 1,
					'messaging_status' => 1
                )
            ),
			array('$match' => $matchArr),
            array(
                '$sort' => array(
                    'distance'=>1,
                    'last_active_time' => -1
                )
            )
        );
		
		#print_r($option);
		
        $res = $this->mongo_db->aggregate(DRIVERS, $option);
        return $res;
    }

    /**
     *
     * This function get the category in a location
     * @param String $collection
     * @param Array $condition
     *
     * */
    public function get_available_category($collection = '', $condition = array()) {
		
		if(!is_array($condition)){
			$condition = (array)$condition;
			$condition = array_unique(array_filter($condition));
		}
		
        $data = array();
        $k = 0;
        foreach ($condition as $key => $value) {
            $data[$k] = MongoID($value);
            $k++;
        }
        $this->mongo_db->select(array('name', 'image', 'vehicle_type', 'icon_normal', 'icon_active','icon_car_image','name_languages'));
        $this->mongo_db->where_in('_id', $data);
		$this->mongo_db->where(array('status' => 'Active'));
        $res = $this->mongo_db->get($collection);
        return $res;
    }

    /**
     *
     * This function get the vehicles type in a category
     * @param Array $condition
     *
     * */
    public function get_available_vehicles($condition = array()) {
        $data = array();
        $k = 0;
        foreach ($condition as $key => $value) {
            $data[$k] = MongoID($value);
            $k++;
        }
        
        $this->mongo_db->select(array('vehicle_type'));
        $this->mongo_db->where(array('status' => 'Active'));
        $this->mongo_db->where_in('_id', $data);
        $res = $this->mongo_db->get(VEHICLES);
        return $res;
    }

    /**
     *
     * This function check the coupon code usage for the given user
     * @param Array $mainarray
     * @param String $user_id
     *
     * */
    public function check_user_usage($mainarray = array(), $user_id) {
        foreach ($mainarray as $mainarraykey => $mainarray) {
            if (isset($countArr[$mainarray['user_id']])) {
                $countArr[$mainarray['user_id']] = $countArr[$mainarray['user_id']] + 1;
            } else {
                $countArr[$mainarray['user_id']] = 1;
            }
        }
        if (isset($countArr[$user_id])) {
            return $countArr[$user_id];
        } else {
            return 0;
        }
    }

    /**
     *
     * This function updates the number of rides in user collection
     * @param String $field (no_of_rides/cancelled_rides/completed_rides)
     * @param String $user_id
     *
     * */
    public function update_user_rides_count($field = '', $user_id = '') {
        if ($user_id != '' && $field != '') {
            $condition = array('_id' => MongoID($user_id));
            $this->mongo_db->where($condition)->inc($field, 1)->update(USERS);
        }
    }

    /**
     *
     * This function updates the number of rides in driver collection
     * @param String $field (no_of_rides/cancelled_rides/completed_rides)
     * @param String $driver_id
     *
     * */
    public function update_driver_rides_count($field = '', $driver_id = '') {
        if ($driver_id != '' && $field != '') {
            $condition = array('_id' => MongoID($driver_id));
            $this->mongo_db->where($condition)->inc($field, 1)->update(DRIVERS);
            /* $qur='db.dectar_drivers.update({"driver_name":"Suresh Kumar"},{"$inc":{"cancelled_rides":1}})'; */
        }
    }

    /**
     *
     * This function return the ride list
     * @param String $type (all/upcoming/completed)
     * @param String $user_id
     * @param Array $fieldArr
     *
     * */
    public function get_ride_list($user_id = '', $type = '', $fieldArr = array()) {
        if ($user_id != '' && $type != '') {
            $this->mongo_db->select($fieldArr);

            switch ($type) {
                case 'all':
                    $where_clause = array("user.id" => $user_id);
                    break;
                case 'upcoming':
                    $where_clause = array(
                        '$or' => array(
                            array("ride_status" => 'Booked'),
                            array("ride_status" => 'Confirmed'),
                        ),
                        "user.id" => $user_id
                    );
                    break;
                case 'completed':
                    $where_clause = array(
                        '$or' => array(
                            #array("ride_status"=>'Finished')
                            array("ride_status" => 'Completed')
                        ),
                        "user.id" => $user_id
                    );
                    #$this->mongo_db->or_where(array('ride_status'=>'Completed', 'ride_status'=>'Cancelled','ride_status'=>'Confirmed', 'ride_status'=>'Arrived','ride_status'=>'Onride', 'ride_status'=>'Finished'));
                    break;
                default:
                    $where_clause = array("user.id" => $user_id);
                    break;
            }
            $this->mongo_db->where($where_clause, TRUE);
            $this->mongo_db->order_by(array('_id' => 'DESC'));
            $res = $this->mongo_db->get(RIDES);
            return $res;
        }
    }

    /**
     *
     * This function return the ride list for drivers
     * @param String $type (all/onride/completed)
     * @param String $driver_id
     * @param Array $fieldArr
     *
     * */
    public function get_ride_list_for_driver($driver_id = '', $type = '', $fieldArr = array()) {
        if ($driver_id != '' && $type != '') {
            $this->mongo_db->select($fieldArr);

            switch ($type) {
                case 'all':
                    $where_clause = array("driver.id" => $driver_id);
                    break;
                case 'onride':
                    $where_clause = array(
                        '$or' => array(
                            array("ride_status" => 'Onride'),
                            array("ride_status" => 'Confirmed'),
                            array("ride_status" => 'Arrived'),
                        ),
                        "driver.id" => $driver_id
                    );
                    break;
                case 'completed':
                    $where_clause = array(
                        '$or' => array(
                            array("ride_status" => 'Completed'),
                            array("ride_status" => 'Finished')
                        ),
                        "driver.id" => $driver_id
                    );
                    break;
                default:
                    $where_clause = array("driver.id" => $driver_id);
                    break;
            }
            $this->mongo_db->where($where_clause, TRUE);
            $this->mongo_db->order_by(array('_id' => 'DESC'));
            $res = $this->mongo_db->get(RIDES);
            return $res;
        }
    }

    /**
     *
     * This function return the transaction list
     * @param String $type (all/credit/debit)
     * @param String $user_id
     * @param Array $fieldArr
     *
     * */
    public function get_transaction_lists($user_id = '', $type = '', $fieldArr = array()) {
        if ($user_id != '' && $type != '') {
            $this->mongo_db->select($fieldArr);
            switch ($type) {
                case 'all':
                    $where_clause = array("user_id" => MongoID($user_id));
                    break;
                case 'credit':
                    $where_clause = array("transactions.$.type" => 'CREDIT', "user_id" => MongoID($user_id));
                    break;
                case 'debit':
                    $where_clause = array("transactions.$.type" => 'DEBIT', "user_id" => MongoID($user_id));
                    break;
                default:
                    $where_clause = array("user_id" => MongoID($user_id));
                    break;
            }
            $this->mongo_db->where($where_clause, TRUE);
            $res = $this->mongo_db->get(WALLET);
            return $res;
        }
    }
	 /**
     *
     * This function return the transaction list
     * @param String $type (all/credit/debit)
     * @param String $user_id
     * @param Array $fieldArr
     *
     * */
	public function user_transaction($user_id, $trans_type) {
		$matchArr=array('$match' => array('user_id' =>MongoID($user_id)));
		$unwindArr=array('$unwind' =>'$transactions');
	    if($trans_type=='debit') {
			 $matchArrsend=array('$match' => array('transactions.type' =>'DEBIT'));
			 $option = array(array('$project' => array(
											'user_id' =>1,
											'transactions'=>1,
											'total' =>1
											
										)
									),
									$matchArr,
									$unwindArr,
									$matchArrsend
							);
	   } else if($trans_type=='credit') {
			 $matchArrsend=array('$match' => array('transactions.type' =>'CREDIT'));
			 $option = array(array('$project' => array(
											'user_id' =>1,
											'transactions'=>1,
											'total' =>1
											
										)
									),
									$matchArr,
									$unwindArr,
									$matchArrsend
							);
	   } else {
	     $option = array(array('$project' => array(
										'user_id' =>1,
										'transactions'=>1,
										'total' =>1
										
									)
								),
								$matchArr,
								$unwindArr
							);
	   }
	    $res = $this->mongo_db->aggregate(WALLET, $option);
        return $res;
    }

    /**
     *
     * This function return the referer document which is matched 
     *
     * */
    public function check_ref_val($ref_id = '') {
        
        $option = array(
            array(
                '$project' => array(
                    'transactions' => 1
                )
            ),
            array(
                '$match' => array(
                    'transactions.ref_id' => array('$eq' => $ref_id)
                )
            ),
            /* array(
              '$unwind'=>"$transactions"
              ), */
            array(
                '$group' => array("_id" => array("transactions.ref_id" => $ref_id),
                    "values" => array('$addToSet' => '$transactions')
                )
            ),
                
        );
        $res = $this->mongo_db->aggregate(WALLET, $option);
        return $res;
    }

    /**
     *
     * This function return the pending(uncompleted) ride list for drivers
     *
     * */
    public function get_uncompleted_trips($driver_id = '', $fieldArr = array()) {
        if ($driver_id != '') {
            $where_clause = array(
                '$or' => array(
                    array("ride_status" => 'Confirmed'),
                    array("ride_status" => 'Arrived'),
                    array("ride_status" => 'Onride'),
                    array("ride_status" => 'Finished'),
                ),
                "driver.id" => $driver_id
            );
            $this->mongo_db->select($fieldArr);
            $this->mongo_db->where($where_clause, TRUE);
            $this->mongo_db->order_by(array('_id' => 'DESC'));
            $res = $this->mongo_db->get(RIDES);
            return $res;
        }
    }

    /**
     *
     * This function return the ride list
     * @param String $start_time
     * @param String $end_time
     *
     * */
    public function get_ride_later_list($start_time, $end_time) {
        if ($start_time != '' && $end_time != '') {
            $this->mongo_db->select(array('ride_id', 'type', 'ride_status', 'user'));
            $this->mongo_db->where(array("type" => 'Later', "ride_status" => 'Booked'));
            $this->mongo_db->where_between("booking_information.est_pickup_date", MongoDATE($start_time), MongoDATE($end_time));
            $this->mongo_db->order_by(array('_id' => 'ASC'));
            $res = $this->mongo_db->get(RIDES);
            return $res;
        }
    }

    /**
     *
     * This function return the billing details for drivers
     * @param String $bill_from
     * @param String $bill_to
     *
     * */
    public function get_billing_rides($bill_from, $bill_to, $driver_id) {
        if ($bill_from != '' && $bill_to != '' && $driver_id != '') {
            $this->mongo_db->select(array('ride_id', 'driver_revenue', 'booking_information'));
            $this->mongo_db->where(array("driver.id" => $driver_id));
            $this->mongo_db->where_between("history.end_ride", MongoDATE($bill_from), MongoDATE($bill_to));
            $this->mongo_db->order_by(array('_id' => 'DESC'));
            $res = $this->mongo_db->get(RIDES);
            return $res;
        }
    }
	
	/*	application version update	*/
	
	/**
    *
    * This function return the today trips and earnings
    *
    * */
    public function get_today_rides($driver_id = '') {
		$option = array(
			array(
				'$project' => array(
					'ride_id' => 1,
					'booking_information' => 1,
					'driver' => 1,
					'driver_revenue' => 1,
					'pay_status' => 1,
					'ride_status' => 1,
					'total' => 1,
					'summary' =>1
				)
			),
			array(
				'$match' => array(
					'booking_information.booking_date' => array('$gte' => MongoDATE(strtotime(date('Y-m-d 00:00:00'))), '$lte' => MongoDATE(strtotime(date('Y-m-d H:i:s')))),
					'driver.id' => $driver_id,
					'ride_status' => 'Completed',
					'pay_status' => 'Paid'
				)
			),
			array(
				'$group' => array(
					'_id' =>'$driver.id',
					'totalTrips'=>array('$sum'=>1),
					'driverAmount'=>array('$sum'=>'$driver_revenue'),
					'freeTime'=>array('$sum'=>'$total.free_ride_time'),
					'tripTime'=>array('$sum'=>'$total.ride_time'),
					'waitTime'=>array('$sum'=>'$total.wait_time'),
					'ridetime'=>array('$sum'=>'$summary.ride_duration')
				)
			)
		);
        $res = $this->mongo_db->aggregate(RIDES, $option);
        return $res;
    }
	/**
    *
    * This function return the today tips amount
    *
    * */
    public function get_today_tips($driver_id = '') {
		$option = array(
								array('$project' => array('ride_id' => 1,
																'booking_information' => 1,
																'driver' => 1,
																'pay_status' => 1,
																'ride_status' => 1,
																'total' => 1
															)
								),
								array('$match' => array('booking_information.booking_date' => array('$gte' => MongoDATE(strtotime(date('Y-m-d 00:00:00'))),
																															'$lte' => MongoDATE(strtotime(date('Y-m-d H:i:s')))
																														),
																	'driver.id' => $driver_id,
																	'ride_status' => 'Completed',
																	'pay_status' => 'Paid',
																	#'total.tips_amount' =>array('$gt'=> floatval(0))
																)
								),
								array('$group' => array('_id' =>'$driver.id',
																'totalTrips'=>array('$sum'=>1),
																'tipsAmount'=>array('$sum'=>'$total.tips_amount')
														)
										)
							);
        $res = $this->mongo_db->aggregate(RIDES, $option);
        return $res;
     }
	 /**
     *
     * This function return the ride list
     * @param String $start_time
     * @param String $end_time
     *
     * */
    public function get_expired_ride_list($start_time) {
        if ($start_time != '') {
            $this->mongo_db->select(array('ride_id', 'type', 'ride_status', 'user'));
            $this->mongo_db->where(array("ride_status" => 'Booked'));
            $this->mongo_db->where(array('booking_information.est_pickup_date'=>array('$lt'=>MongoDATE($start_time))));
            $this->mongo_db->order_by(array('_id' => 'ASC'));
            $res = $this->mongo_db->get(RIDES);
            return $res;
        }
    }

	/**
	*
	* This function return the pending(uncompleted) ride list for user
	*
	* */
    public function get_ongoing_rides($user_id = '', $fieldArr = array()) {
		$trip_cout = 0;
        if ($user_id != '') {
            $where_clause = array(
                '$or' => array(
                    array("ride_status" => 'Onride'),
                    array("ride_status" => 'Finished'),
                ),
                "user.id" => $user_id
            );
            $this->mongo_db->select($fieldArr);
            $this->mongo_db->where($where_clause, TRUE);
            $this->mongo_db->order_by(array('_id' => 'DESC'));
            $res = $this->mongo_db->get(RIDES);
			$trip_cout = $res->num_rows();
        }
		return $trip_cout;
    }
	
	/**
	*
	* This function return the drivers list for pool service
	* 	Array $coordinates
	* 	String $category
	* 	Number $limit
	*
	* */
    public function get_nearest_pool_driver($coordinates = array(), $category, $limit=10,$distance_unit='',$requested_drivers= array(),$global="",$location = "",$genderPref=FALSE) {
    
 
		$map_searching_radius = $this->config->item('map_searching_radius');
		if($map_searching_radius<1000){
			$map_searching_radius = 1000;
		}
		if($global=="global"){
			$map_searching_radius = 100000;
		}
		
		$distanceMultiplier = 0.001;
		if($distance_unit == 'km'){
			$distanceMultiplier = 0.001;
		} else if($distance_unit == 'mi'){
			$distanceMultiplier = 0.000621371;
		} else if($distance_unit == 'm'){
			$distanceMultiplier = 1;
		}
		
        if(!is_array($requested_drivers)&& empty($requested_drivers)){
			$requested_drivers=array();
		}		
		
        $matchArr =    array('availability' => array('$eq' => 'Yes'),
										'status' => array('$eq' => 'Active'),
										'verify_status' => array('$eq' => 'Yes'),
										'active_trips' => array('$lt' =>intval(2)),
										'last_active_time' => array('$gte' => MongoDATE(time()-1800)),
										#'_id'=>array('$nin'=>$requested_drivers)
									);
		if($genderPref!=FALSE && $genderPref!=""){
			$matchArr["gender"] = array('$eq' =>strtolower($genderPref));
		}
		
		if(is_array($category) ){
			
			if($location!=""){
				$this->mongo_db->select(array('pool_map_search_radius'));
				$this->mongo_db->where(array('_id' => MongoID($location)));
				$res = $this->mongo_db->get(LOCATIONS);
				if($res->num_rows()>0){
					if(isset($res->row()->pool_map_search_radius)){
						if($res->row()->pool_map_search_radius>=1000){
							$map_searching_radius = $res->row()->pool_map_search_radius;
						}
					}
				}
			}
				
			$matchArr["category"] = array('$in' =>$category);
			$matchArr['$or'] = array(array('mode' => array('$eq' =>'Booked'),'ride_type'=>array('$exists' =>true,'$eq' => 'Share')),
												array('mode' => array('$eq' =>'Available'),'ride_type' => array('$exists' =>true,'$eq' =>"Normal")),
												array('mode' => array('$eq' =>'Available'),'ride_type' => array('$exists' =>true,'$eq' =>"Share")),
												array('mode' => array('$eq' =>'Available'),'ride_type' => array('$exists' =>true,'$eq' =>"")),
												array('mode' => array('$eq' =>'Available'),'ride_type'=>array('$exists' =>false))
										);
		}else{
			$matchArr["category"] = array('$eq' => MongoID($category));
			$matchArr["mode"] = array('$eq' => 'Available');
		}
		
		
        $option = array(
            array(
                '$geoNear' => array("near" => array("type" => "Point",
                        "coordinates" => $coordinates
                    ),
                    "spherical" => true,
                    "maxDistance" => intval($map_searching_radius),
                    "includeLocs" => 'loc',
                    "distanceField" => "distance",
                    "distanceMultiplier" => $distanceMultiplier,
                    'num' => (string)$limit
                ),
            ),
            array(
                '$project' => array(
                    'category' => 1,
                    'driver_name' => 1,
                    'loc' => 1,
                    'availability' => 1,
                    'status' => 1,
                    'mode' => 1,
                    'push_notification' => 1,
                    'no_of_rides' => 1,
                    'avg_review' => 1,
                    'total_review' => 1,
                    'distance' => 1,
                    'verify_status' => 1,
                    'last_active_time' => 1,
                    'ride_type' => 1,
                    'active_trips' => 1,
					'gender' => 1,
					'messaging_status' => 1
                )
            ),
			array('$match' => $matchArr),
            array(
                '$sort' => array(
                    'last_active_time' => -1
                )
            )
        );
        $res = $this->mongo_db->aggregate(DRIVERS, $option);
        return $res;
    }
	
	/**
    *
    * This functions returns all the collection details using @param 
    * @param String $collection
    * @param Array $sortArr
    * @param Array $condition
    * @param Numeric $limit
    * @param Numeric $offset
    * @param Array $likearr
    *
    * */
    public function get_driver_active_trips($driver_id="",$ride_id="",$ride_type="Normal") {
        if ($driver_id !="" && $ride_id !="") {
			$this->mongo_db->select();
			if($ride_type=="Normal"){
				$condition = array('ride_id' => $ride_id, 
											'driver.id' => $driver_id,
											'$or' => array(array("ride_status" => 'Confirmed'),
																array("ride_status" => 'Arrived'),
																array("ride_status" => 'Onride'),
																array("ride_status" => 'Finished'),
																array("ride_status" => 'Completed','drs'=>'0'),
															)
									);
				$this->mongo_db->where($condition,TRUE);
			}else{
				$condition = array('pool_id' => $ride_id, 
											'driver.id' => $driver_id,
											'$or' => array(array("ride_status" => 'Confirmed'),
																array("ride_status" => 'Arrived'),
																array("ride_status" => 'Onride'),
																array("ride_status" => 'Finished'),
																array("ride_status" => 'Completed','drs'=>'0'),
															)
									);
				$this->mongo_db->where($condition,TRUE);
			}
            $res = $this->mongo_db->get(RIDES);
        }else if($driver_id !=""){
			$condition = array('driver.id' => $driver_id,
										'$or' => array(array("ride_status" => 'Confirmed'),
															array("ride_status" => 'Arrived'),
															array("ride_status" => 'Onride'),
															array("ride_status" => 'Finished'),
															array("ride_status" => 'Completed','drs'=>'0'),
														)
								);
			$this->mongo_db->select();
			$this->mongo_db->where($condition,TRUE);
            $res = $this->mongo_db->get(RIDES);
		}
        return $res;
    }
	public function get_user_active_trips($pool_id='') {
		$condition = array('pool_id' => $pool_id, 
							'$or' => array(array("ride_status" => 'Confirmed'),
											array("ride_status" => 'Arrived'),
											array("ride_status" => 'Onride'),
											array("ride_status" => 'Finished')
											
										)
							);
		$this->mongo_db->where($condition,TRUE);
		$res = $this->mongo_db->get(RIDES);
		return $res;
	}


}
