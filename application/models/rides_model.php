<?php

if (!defined('BASEPATH'))
    exit('No direct script access allowed');

/**
 * 
 * This model contains all db functions related to Rides management
 * @author Casperon
 *
 * */
class Rides_model extends My_Model {

    public function __construct() {
        parent::__construct();
    }

    /**
     * 
     * This functions selects rides list 
     * */
    public function get_rides_total($ride_actions = '', $driver_id = '',$company_id='', $filter_array = array(),$filter_condition = array()) {
        $this->mongo_db->select('*');
        if ($ride_actions == 'Booked' || $ride_actions == '') {
            $where_clause = array('ride_status' => 'Booked');
        } else if ($ride_actions == 'OnRide') {
            $where_clause = array(
                '$or' => array(
                    array("ride_status" => 'Onride'),
                    array("ride_status" => 'Confirmed'),
                    array("ride_status" => 'Arrived'),
                )
            );
        } else if ($ride_actions == 'Completed') {
            $where_clause = array(
                '$or' => array(
                    array("ride_status" => 'Completed'),
                    array("ride_status" => 'Finished'),
                )
            );
        } else if ($ride_actions == 'Cancelled') {
            $where_clause = array('ride_status' => 'Cancelled');
        } else if ($ride_actions == 'riderCancelled') {
            $where_clause = array('ride_status' => 'Cancelled', 'cancelled.primary.by' => 'User');
        } else if ($ride_actions == 'driverCancelled') {
            $where_clause = array('ride_status' => 'Cancelled', 'cancelled.primary.by' => 'Driver');
		} else if ($ride_actions == 'Expired') {
            $where_clause = array('ride_status' => 'Expired');
        } else if ($ride_actions == 'total') {
            $where_clause = array();
        }
        if ($driver_id != '') {
            $where_clause['driver.id'] = $driver_id;
        }
		if($company_id != '') {
			$where_clause['company_id'] = $company_id;
		}
		/* Filter Rides*/
		if(!empty($filter_array))
			extract($filter_array);
			
/* 		if(isset($location) && !empty($location)){
			$where_clause['location.id'] = $location;
		} */	
		
		$type = array_keys($filter_condition);
		$value = array_values($filter_condition);
		if(!empty($filter_condition)){
			$where_clause[$type[0]] = $value[0];
		}
		
		if(isset($to) && !empty($to) && isset($from) && !empty($from)){
			$from_date = $from.' 00:00:00';
			$to_date = $to.' 23:59:59';
           
			$where_clause['booking_information.est_pickup_date'] = array('$lte' => MongoDATE(strtotime($to_date)),'$gte' => MongoDATE(strtotime($from_date)));
		}else if(isset($from) && !empty($from)){
			$from_date = $from.' 00:00:00';
			$where_clause['booking_information.est_pickup_date'] = array('$gte' => MongoDATE(strtotime($from_date)));
		}
		/* Filter Rides*/
		#echo "<pre>";print_r($where_clause);die;
        $this->mongo_db->where($where_clause, TRUE);
        $res = $this->mongo_db->get(RIDES);
        return $res;
    }

    /**
     * 
     * This functions selects rides list 
     * */
    public function get_rides_list($ride_actions = '', $limit = FALSE, $offset = FALSE, $driver_id = '', $filter_array = array(),$company_id ='', $filter_condition = array()) {
		$this->mongo_db->select('*');
        if ($ride_actions == 'Booked' || $ride_actions == '') {
            $where_clause = array('ride_status' => 'Booked');
        } else if ($ride_actions == 'OnRide') {
            $where_clause = array(
                '$or' => array(
                    array("ride_status" => 'Onride'),
                    array("ride_status" => 'Confirmed'),
                    array("ride_status" => 'Arrived'),
                    array("ride_status" => 'Finished'),
                )
            );
        } else if ($ride_actions == 'Completed') {
            $where_clause = array(
                '$or' => array(
                    array("ride_status" => 'Completed'),
                #array("ride_status"=>'Finished'),
                )
            );
        } else if ($ride_actions == 'Cancelled') {
            $where_clause = array('ride_status' => 'Cancelled');
        } else if ($ride_actions == 'riderCancelled') {
            $where_clause = array('ride_status' => 'Cancelled', 'cancelled.primary.by' => 'User');
        } else if ($ride_actions == 'driverCancelled') {
            $where_clause = array('ride_status' => 'Cancelled', 'cancelled.primary.by' => 'Driver');
		} else if ($ride_actions == 'Expired') {
            $where_clause = array('ride_status' => 'Expired');
        } else if ($ride_actions == 'total') {
            $where_clause = array();
        }

        if ($driver_id != '') {
            $where_clause['driver.id'] = $driver_id;
        }
		if($company_id != '' && $ride_actions != 'Booked') {
			$where_clause['company_id'] = $company_id;
		}
		/* Filter Rides*/
		if(!empty($filter_array))
			extract($filter_array);
			
 		if(isset($location) && $location!=''){
			$where_clause['location.id'] = $location;
		}
		
		$type = array_keys($filter_condition);
		$value = array_values($filter_condition);
        
		if(!empty($filter_condition)){
			$where_clause[$type[0]] = $value[0];
		}
		
		if(isset($to) && !empty($to) && isset($from) && !empty($from)){
			$from_date = $from.' 00:00:00';
			$to_date = $to.' 23:59:59';
			$where_clause['booking_information.est_pickup_date'] = array('$lte' => MongoDATE(strtotime($to_date)),'$gte' => MongoDATE(strtotime($from_date)));
		}else if(isset($from) && !empty($from)){
			$from_date = $from.' 00:00:00';
			$where_clause['booking_information.est_pickup_date'] = array('$gte' => MongoDATE(strtotime($from_date)));
		}
		
        $this->mongo_db->where($where_clause, TRUE);
		$this->mongo_db->order_by(array('booking_information.booking_date' => "DESC"));
        if ($limit !== FALSE && is_numeric($limit) && $offset !== FALSE && is_numeric($offset)) {
            $this->mongo_db->limit($limit);
            $this->mongo_db->offset($offset);
        } 
        $res = $this->mongo_db->get(RIDES);
        


        return $res;
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
			$this->mongo_db->order_by(array('ride_id' => -1));
            $res = $this->mongo_db->get(RIDES);
            return $res;
        }
    }

    /**
     * 
     * This functions selects driver's rides list 
     * */
    public function get_driver_rides_list($ride_actions = '', $driver_id = '') {
        $this->mongo_db->select('*');
        if ($driver_id != '') {
            $this->mongo_db->where(array('driver.id' => $driver_id));
        }
        if ($ride_actions == 'Booked' || $ride_actions == '') {
            $this->mongo_db->where(array('ride_status' => 'Booked'));
        } else if ($ride_actions == 'OnRide') {
            $this->mongo_db->where_or(array('ride_status' => 'Onride', 'ride_status' => 'Confirmed', 'ride_status' => 'Arrived'));
        } else if ($ride_actions == 'Completed') {
            $this->mongo_db->where_or(array('ride_status' => 'Completed', 'ride_status' => 'Finished'));
        } else if ($ride_actions == 'Cancelled') {
            $this->mongo_db->where(array('ride_status' => 'Cancelled'));
        }
		$this->mongo_db->order_by(array('ride_id' => -1));
        $res = $this->mongo_db->get(RIDES);
        return $res;
    }
	
	
	/**
	* Get Unfilled Rides
	**/
	public function get_unfilled_rides($coordinates = array(),$matchArr = array()){
		$option = array(
								array(
									'$geoNear'=>array("near"=>array("type"=>"Point",
														"coordinates"=>$coordinates
														),
									"spherical"=> true,
									"maxDistance"=>intval(1000),
									"includeLocs"=>'location',
									"distanceField"=>"distance",
									"distanceMultiplier"=>0.001,
									"num"=>intval(100000)
									),
								),
								array(
									'$project' => array(
										'pickup_address' =>1,
										'user_id' =>1,
										'location' =>1,
										'category' =>1,
										'ride_time' =>1,
										'location_id' =>1
									)
								)
					);
		
		if(!empty($matchArr)){
			$option[] = $matchArr;
		}
		$res = $this->mongo_db->aggregate(RIDE_STATISTICS,$option);
		return $res;
	}
	
    public function get_top_rider(){
	 
	    $matchArr=array('$match' => array('ride_status' =>array('$eq'=>'Completed'),
										'pay_status' =>array('$eq'=>'Paid')
									 )
					   );
						
	    $groupArr=array('$group' => array('_id' =>'$user.id',
										'totalTrips'=>array('$sum'=>1),
										 'user'=>array('$first'=>'$user')
									)
					    );
	
		$option = array(array('$project' => array(
										'ride_id' =>1,
										'commission_percent' =>1,
										'driver' =>1,
										'user' =>1,
										'total' =>1,
										'booking_information' =>1,
										'ride_status' =>1,
										'pay_status' =>1,
										'summary' =>1,
										'pay_summary' =>1,
										'history' =>1,
										'driver_revenue' =>1,
										'amount_commission' =>1,
										'amount_detail' =>1
									)
								),
								$matchArr,
								$groupArr,
								array(
							   '$sort'=>array(
									'totalTrips'=>-1
								 )
							    )
							   ,
							   array('$limit'=>3)
						);
		#echo "<pre>";print_r($option);
		$res = $this->mongo_db->aggregate(RIDES,$option);
		return $res;
	}
	
	public function get_top_revenue(){
	 
	    $matchArr=array('$match' => array('ride_status' =>array('$eq'=>'Completed'),
										'pay_status' =>array('$eq'=>'Paid')
									 )
					   );
						
	    $groupArr=array('$group' => array('_id' =>'$user.id',
										'totalTrips'=>array('$sum'=>1),
										 'totalRevenue'=>array('$sum'=>'$total.grand_fare'),
										 'user'=>array('$first'=>'$user')
									)
					    );
	
		$option = array(array('$project' => array(
										'ride_id' =>1,
										'commission_percent' =>1,
										'driver' =>1,
										'user' =>1,
										'total' =>1,
										'booking_information' =>1,
										'ride_status' =>1,
										'pay_status' =>1,
										'summary' =>1,
										'pay_summary' =>1,
										'history' =>1,
										'driver_revenue' =>1,
										'amount_commission' =>1,
										'amount_detail' =>1
										)
								),
								$matchArr,
								$groupArr,
								array(
							   '$sort'=>array(
									'totalRevenue'=>-1
								 )
							    )
							   ,
							   array('$limit'=>3)
						);
		#echo "<pre>";print_r($option);
		$res = $this->mongo_db->aggregate(RIDES,$option);
		return $res;
	}

}
