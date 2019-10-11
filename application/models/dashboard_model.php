<?php

if (!defined('BASEPATH'))
    exit('No direct script access allowed');

/**
 * 
 * This model contains all db functions related to admin management
 * @author Casperon
 *
 * */
class Dashboard_model extends My_Model {

    public function __construct() {
        parent::__construct();
    }

    /**
     *
     * This function return the total number of on rides
     *
     * */
    public function get_on_rides($driver_id = '', $operator_id = '') {
        if ($driver_id != '') {
            $where_clause = array(
                '$or' => array(
                    array("ride_status" => 'Confirmed'),
                    array("ride_status" => 'Arrived'),
                    array("ride_status" => 'Onride'),
                    array("ride_status" => 'Finished'),
                ),
                'driver.id' => $driver_id
            );
        } else {
            $where_clause = array(
                '$or' => array(
                    array("ride_status" => 'Confirmed'),
                    array("ride_status" => 'Arrived'),
                    array("ride_status" => 'Onride'),
                    array("ride_status" => 'Finished')
                )
            );
        }
        $this->mongo_db->where($where_clause, TRUE);
        if($operator_id != ''){
                $this->mongo_db->where(array('operator_id' => $operator_id));
        }						   
        $res = $this->mongo_db->count(RIDES);
        return $res;
    }
	
	public function get_on_rides_company($company_id = '') {
			$where_clause = array(
			'$or' => array(
				array("ride_status" => 'Confirmed'),
				array("ride_status" => 'Arrived'),
				array("ride_status" => 'Onride'),
				array("ride_status" => 'Finished'),
			)
		);
        $this->mongo_db->where($where_clause, TRUE);
		if($company_id != ''){
				$this->mongo_db->where(array('company_id' => $company_id));
		}						   
        $res = $this->mongo_db->count(RIDES);
        return $res;
    }

    /**
     *
     * This function return the total earnings
     *
     * */
    public function get_total_earnings($driver_id = '') {
        if ($driver_id != '') {
            $option = array(
                array(
                    '$project' => array(
                        'ride_status' => 1,
                        'total' => 1,
                        'driver.id' => 1
                    )
                ),
                array(
                    '$match' => array(
                        'ride_status' => array('$eq' => 'Completed'),
                        'driver.id' => $driver_id
                    )
                ),
                array(
                    '$group' => array(
                        '_id' => '$ride_status',
                        'ride_status' => ['$last' => '$ride_status'],
                        'totalAmount' => array('$sum' => '$total.grand_fare')
                    )
                )
            );
        } else {
            $option = array(
                array(
                    '$project' => array(
                        'ride_status' => 1,
                        'total' => 1
                    )
                ),
                array(
                    '$match' => array(
                        'ride_status' => array('$eq' => 'Completed')
                    )
                ),
                array(
                    '$group' => array(
                        '_id' => '$ride_status',
                        'ride_status' => ['$last' => '$ride_status'],
                        'totalAmount' => array('$sum' => '$total.grand_fare')
                    )
                )
            );
        }
        $res = $this->mongo_db->aggregate(RIDES, $option);
        $totalAmount = 0;
        if (!empty($res)) {
            if (isset($res['result'][0]['totalAmount'])) {
                $totalAmount = $res['result'][0]['totalAmount'];
            }
        }
        return $totalAmount;
    }
    
    

    /**
     *
     * This function return the current month details
     *
     * */
    public function get_this_month_drivers($driver_id = '', $operator_id = '') {
        if ($driver_id != '') {
            $option = array(
                array(
                    '$project' => array(
                        'driver_name' => 1,
                        'created' => 1
                    )
                ),
                array(
                    '$match' => array(
                        'created' => array('$gte' => date('Y-m-01 00:00:00'), '$lte' => date('Y-m-d H:i:s')),
                        '_id' => MongoID($driver_id)
                    )
                )
            );
            $this->mongo_db->where();
        } else if($operator_id != ''){
            $option = array(
                array(
                    '$project' => array(
                        'driver_name' => 1,
                        'created' => 1,
												'operator_id' => 1
                    )
                ),
                array(
                    '$match' => array(
                        'created' => array('$gte' => date('Y-m-01 00:00:00'), '$lte' => date('Y-m-d H:i:s')),
												'operator_id' => $operator_id
                    )
                )
            );
        } else {
            $option = array(
                array(
                    '$project' => array(
                        'driver_name' => 1,
                        'created' => 1
                    )
                ),
                array(
                    '$match' => array(
                        'created' => array('$gte' => date('Y-m-01 00:00:00'), '$lte' => date('Y-m-d H:i:s'))
                    )
                )
            );
        }
        $res = $this->mongo_db->aggregate(DRIVERS, $option);
        return $res;
    }
	
	public function get_this_month_drivers_company($company_id = '') {
      if($company_id != ''){
            $option = array(
                array(
                    '$project' => array(
                        'driver_name' => 1,
                        'created' => 1,
						'company_id' => 1
                    )
                ),
                array(
                    '$match' => array(
                        'created' => array('$gte' => date('Y-m-01 00:00:00'), '$lte' => date('Y-m-d H:i:s')),
												'company_id' => $company_id
                    )
                )
            );
        } else {
            $option = array(
                array(
                    '$project' => array(
                        'driver_name' => 1,
                        'created' => 1
                    )
                ),
                array(
                    '$match' => array(
                        'created' => array('$gte' => date('Y-m-01 00:00:00'), '$lte' => date('Y-m-d H:i:s'))
                    )
                )
            );
        }
        $res = $this->mongo_db->aggregate(DRIVERS, $option);
        return $res;
    }
	public function get_this_today_drivers_company($company_id = '') {
      if($company_id != ''){
            $option = array(
                array(
                    '$project' => array(
                        'driver_name' => 1,
                        'created' => 1,
						'company_id' => 1
                    )
                ),
                array(
                    '$match' => array(
                        'created' => array('$gte' => date('Y-m-d 00:00:00'), '$lte' => date('Y-m-d H:i:s')),
												'company_id' => $company_id
                    )
                )
            );
        } else {
            $option = array(
                array(
                    '$project' => array(
                        'driver_name' => 1,
                        'created' => 1
                    )
                ),
                array(
                    '$match' => array(
                        'created' => array('$gte' => date('Y-m-01 00:00:00'), '$lte' => date('Y-m-d H:i:s'))
                    )
                )
            );
        }
        $res = $this->mongo_db->aggregate(DRIVERS, $option);
        return $res;
    }
	
	public function get_this_year_drivers_company($company_id = '') {
      if($company_id != ''){
            $option = array(
                array(
                    '$project' => array(
                        'driver_name' => 1,
                        'created' => 1,
						'company_id' => 1
                    )
                ),
                array(
                    '$match' => array(
                        'created' => array('$gte' => date('Y-01-01 00:00:00'), '$lte' => date('Y-m-d H:i:s')),
												'company_id' => $company_id
                    )
                )
            );
        } else {
            $option = array(
                array(
                    '$project' => array(
                        'driver_name' => 1,
                        'created' => 1
                    )
                ),
                array(
                    '$match' => array(
                        'created' => array('$gte' => date('Y-m-01 00:00:00'), '$lte' => date('Y-m-d H:i:s'))
                    )
                )
            );
        }
        $res = $this->mongo_db->aggregate(DRIVERS, $option);
        return $res;
    }
	
    /**
     *
     * This function return the current month details
     *
     * */
    public function get_this_month_rides($driver_id = '', $operator_id = '') {
	
		$groupArr = array(
						'$group' => array(
							'_id' =>'$driver.id',
							'ride_count'=>array('$sum'=>1)
						)
					);
		
        if ($driver_id != '') {
            $option = array(
                array(
                    '$project' => array(
                        'ride_id' => 1,
                        'history.end_ride' => 1,
                        'driver.id' => 1
                    )
                ),
                array(
                    '$match' => array(
                        'history.end_ride' => array('$gte' => MongoDATE(strtotime(date('Y-m-01 00:00:00'))), '$lte' => MongoDATE(strtotime(date('Y-m-d H:i:s')))),
                        'driver.id' => $driver_id
                    )
                ),
				$groupArr
            );
        } else if($operator_id != ''){
            $option = array(
                array(
                    '$project' => array(
                        'ride_id' => 1,
                        'history.end_ride' => 1,
						'booked_by' => 1,
						'operator_id' => 1
                    )
                ),
                array(
                    '$match' => array(
                        'history.end_ride' => array('$gte' => MongoDATE(strtotime(date('Y-m-01 00:00:00'))), '$lte' => MongoDATE(strtotime(date('Y-m-d H:i:s')))),
						'operator_id' =>MongoID($operator_id)
                    )
                )
            );
        } else {
            $option = array(
                array(
                    '$project' => array(
                        'ride_id' => 1,
                        'history.end_ride' => 1
                    )
                ),
                array(
                    '$match' => array(
                        'history.end_ride' => array('$gte' => MongoDATE(strtotime(date('Y-m-01 00:00:00'))), '$lte' => MongoDATE(strtotime(date('Y-m-d H:i:s'))))
                    )
                ),
				$groupArr
            );
        }
        $res = $this->mongo_db->aggregate(RIDES, $option);
        return $res;
    }
    public function get_this_month_rides_company($company_id = '') {
       
		$option = array(
			array(
				'$project' => array(
					'ride_id' => 1,
					'company_id' => 1,
					'history.end_ride' => 1,
					'booked_by' => 1
				)
			),
			array(
				'$match' => array(
					'history.end_ride' => array('$gte' => MongoDATE(strtotime(date('Y-m-01 00:00:00'))), '$lte' => MongoDATE(strtotime(date('Y-m-d H:i:s')))),
					'company_id' => $company_id
				)
			)
		);
        
        $res = $this->mongo_db->aggregate(RIDES, $option);
        return $res;
    }
    
	
	public function get_today_rides($driver_id = '',$operator_id = '',$ride_status='') {
	
		$groupArr=array(
								'$group' => array(
									'_id' =>'$driver.id',
									'ride_count'=>array('$sum'=>1)
								)
							);
	
        if ($driver_id != '') {
            $option = array(
                array(
                    '$project' => array(
                        'ride_id' => 1,
                        'history' => 1,
                        'driver.id' => 1,
						'ride_status' => 1
                    )
                ),
                array(
                    '$match' => array(
                        
                        'driver.id' => $driver_id
                    )
                ),
				$groupArr
            );
			if($ride_status != ''){
				$option[1]['$match']['ride_status'] = $ride_status;
                if($ride_status=='Completed') {
                    $option[1]['$match']['history.end_ride'] = array('$gte' => MongoDATE(strtotime(date('Y-m-d 00:00:00'))), '$lte' => MongoDATE(strtotime(date('Y-m-d H:i:s'))));
                } else if($ride_status=='Cancelled') {
                    $option[1]['$match']['history.cancelled_time'] = array('$gte' => MongoDATE(strtotime(date('Y-m-d 00:00:00'))), '$lte' => MongoDATE(strtotime(date('Y-m-d H:i:s'))));
                } 
                
				#echo '<pre>'; print_r($option); die;
			} else {
                    $option[1]['$match']['history.booking_time'] = array('$gte' => MongoDATE(strtotime(date('Y-m-d 00:00:00'))), '$lte' => MongoDATE(strtotime(date('Y-m-d H:i:s'))));
            }
        } else if($operator_id != ''){
            $option = array(
                array(
                    '$project' => array(
                        'ride_id' => 1,
                        'history' => 1,
                        'booked_by' => 1,
                        'operator_id' => 1
                    )
                ),
                array(
                    '$match' => array(
                       'history.end_ride' => array('$gte' => MongoDATE(strtotime(date('Y-m-d 00:00:00'))), '$lte' => MongoDATE(strtotime(date('Y-m-d H:i:s')))), 
                      
						'operator_id' =>MongoID($operator_id)
                    )
                )
            ); #echo '<pre>'; print_r($option); die;
        } else {
            $option = array(
                array(
                    '$project' => array(
                        'ride_id' => 1,
                        'history' => 1
                    )
                ),
                array(
                    '$match' => array(
                       'history.end_ride' => array('$gte' => MongoDATE(strtotime(date('Y-m-d 00:00:00'))), '$lte' => MongoDATE(strtotime(date('Y-m-d H:i:s'))))
                    )
                ),
				$groupArr
            );
        }
        $res = $this->mongo_db->aggregate(RIDES, $option);
        return $res;
    }
	
	
	public function get_today_drivers($operator_id = '') {
        if ($operator_id != '') {
            $option = array(
                array(
                    '$project' => array(
                        'driver_name' => 1,
                        'created' => 1,
												'operator_id' => 1
                    )
                ),
                array(
                    '$match' => array(
                        'created' => array('$gte' => date('Y-m-d 00:00:00'), '$lte' => date('Y-m-d H:i:s')),
                        'operator_id' => $operator_id
                    )
                )
            );
        } else {
            $option = array(
                array(
                    '$project' => array(
                        'driver_name' => 1,
                        'created' => 1
                    )
                ),
                array(
                    '$match' => array(
                        'created' => array('$gte' => date('Y-m-d 00:00:00'), '$lte' => date('Y-m-d H:i:s'))
                    )
                )
            );
        }
        $res = $this->mongo_db->aggregate(DRIVERS, $option);
        return $res;
    }

    /**
     *
     * This function return the current year details
     *
     * */
    public function get_this_year_drivers($driver_id = '', $operator_id = '') {
        if ($driver_id != '') {
            $option = array(
                array(
                    '$project' => array(
                        'driver_name' => 1,
                        'created' => 1
                    )
                ),
                array(
                    '$match' => array(
                        'created' => array('$gte' => date('Y-01-01 00:00:00'), '$lte' => date('Y-m-d H:i:s')),
                        '_id' => MongoID($driver_id)
                    )
                )
            );
        } else if($operator_id != ''){
            $option = array(
                array(
                    '$project' => array(
                        'driver_name' => 1,
                        'created' => 1,
												'operator_id' => 1
                    )
                ),
                array(
                    '$match' => array(
                        'created' => array('$gte' => date('Y-01-01 00:00:00'), '$lte' => date('Y-m-d H:i:s')),
												'operator_id' => $operator_id
                    )
                )
            );
        } else {
            $option = array(
                array(
                    '$project' => array(
                        'driver_name' => 1,
                        'created' => 1
                    )
                ),
                array(
                    '$match' => array(
                        'created' => array('$gte' => date('Y-01-01 00:00:00'), '$lte' => date('Y-m-d H:i:s'))
                    )
                )
            );
        }
        $res = $this->mongo_db->aggregate(DRIVERS, $option);
        return $res;
    }

   
   
	/**
     *
     * This function return the current year details
     *
     * */
    public function get_this_year_rides($driver_id = '', $operator_id = '') {
        
		$groupArr=array(
									'$group' => array(
										'_id' =>'$driver.id',
										'ride_count'=>array('$sum'=>1)
									)
								);
								
		if ($driver_id != '') {
            $option = array(
                array(
                    '$project' => array(
                        'ride_id' => 1,
                        'history.end_ride' => 1,
                        'driver.id' => 1
                    )
                ),
                array(
                    '$match' => array(
                        'history.end_ride' => array('$gte' => MongoDATE(strtotime(date('Y-01-01 00:00:00'))), '$lte' => MongoDATE(strtotime(date('Y-m-d H:i:s')))),
                        'driver.id' => $driver_id
                    )
                ),
				$groupArr
            );
        } else if($operator_id != ''){
            $option = array(
                array(
                    '$project' => array(
                        'ride_id' => 1,
                        'history.end_ride' => 1,
						'operator_id' => 1
                    )
                ),
                array(
                    '$match' => array(
                        'history.end_ride' => array('$gte' => MongoDATE(strtotime(date('Y-01-01 00:00:00'))), '$lte' => MongoDATE(strtotime(date('Y-m-d H:i:s')))),
					    'operator_id' =>MongoID($operator_id)
                    )
                )
            );
        } else {
            $option = array(
                array(
                    '$project' => array(
                        'ride_id' => 1,
                        'history.end_ride' => 1
                    )
                ),
                array(
                    '$match' => array(
                        'history.end_ride' => array('$gte' => MongoDATE(strtotime(date('Y-01-01 00:00:00'))), '$lte' => MongoDATE(strtotime(date('Y-m-d H:i:s'))))
                    )
                ),
				$groupArr
            );
        }
        $res = $this->mongo_db->aggregate(RIDES, $option);
        return $res;
    }
	
	public function get_this_year_rides_company($company_id = '') {
		$option = array(
			array(
				'$project' => array(
					'ride_id' => 1,
					'company_id' => 1,
					'history.end_ride' => 1
				)
			),
			array(
				'$match' => array(
					'history.end_ride' => array('$gte' => MongoDATE(strtotime(date('Y-01-01 00:00:00'))), '$lte' => MongoDATE(strtotime(date('Y-m-d H:i:s')))),
					'company_id'=>$company_id
				)
			)
		);
        
        $res = $this->mongo_db->aggregate(RIDES, $option);
        return $res;
    }
	public function get_this_today_rides_company($company_id = '') {
		$option = array(
			array(
				'$project' => array(
					'ride_id' => 1,
					'company_id' => 1,
					'history.end_ride' => 1
				)
			),
			array(
				'$match' => array(
					'history.end_ride' => array('$gte' => MongoDATE(strtotime(date('Y-m-d 00:00:00'))), '$lte' => MongoDATE(strtotime(date('Y-m-d H:i:s')))),
					'company_id'=>$company_id
				)
			)
		);
        
        $res = $this->mongo_db->aggregate(RIDES, $option);
        return $res;
    }

    /**
     *
     * This function return the monthly earnings
     *
     * */
    public function get_monthly_earnings($fromdate = '', $todate = '', $driver_id = '') {
        if ($driver_id != '') {
            $option = array(
                array(
                    '$project' => array(
                        'ride_status' => 1,
                        'total' => 1,
                        'amount_commission' => 1,
                        'commission_percent' => 1,
						'driver_revenue' => 1,
                        'driver.id' => 1,
                        'pay_status' => 1,
                        'history' => 1
                    )
                ),
                array(
                    '$match' => array(
                        'ride_status' => array('$eq' => 'Completed'),
                        'pay_status' =>array('$eq'=>'Paid'),
                        'history.end_ride' => array('$gte' => MongoDATE($fromdate), '$lte' => MongoDATE($todate)),
                        'driver.id' => $driver_id
                    )
                ),
                array(
                    '$group' => array(
                        '_id' => '$ride_status',
                        #'ride_status' => ['$last' => '$ride_status'],
                        'totalAmount' => array('$sum' => '$driver_revenue'),
                        'driver_Earnings' => array('$sum' => '$driver_revenue'),
                        'site_Earnings' => array('$sum' => '$amount_commission')
                    )
                )
            );
        } else {
            $option = array(
                array(
                    '$project' => array(
                        'ride_status' => 1,
                        'total' => 1,
                        'driver_revenue' => 1,
                        'amount_commission' => 1,
                        'pay_status' => 1,
                        'history' => 1
                    )
                ),
                array(
                    '$match' => array(
                        'ride_status' => array('$eq' => 'Completed'),
                        'pay_status' =>array('$eq'=>'Paid'),
                        'history.end_ride' => array('$gte' => MongoDATE($fromdate), '$lte' => MongoDATE($todate))
                    )
                ),
                array(
                    '$group' => array(
                        '_id' => '$ride_status',
                       # 'ride_status' => ['$last' => '$ride_status'],
                        'totalAmount' => array('$sum' => '$total.grand_fare'),
                        'driver_Earnings' => array('$sum' => '$driver_revenue'),
                        'site_Earnings' => array('$sum' => '$amount_commission')
                    )
                )
            );
        }
        #echo '<pre>'; print_r($option); die;
        $res = $this->mongo_db->aggregate(RIDES, $option);
        $resultArr = array('totalAmount' => 0, 'driver_Earnings' => 0, 'site_Earnings' => 0);
        if (!empty($res)) {
            if (isset($res['result'][0]['totalAmount'])) {
				if ($driver_id != '') {
					$resultArr = array('totalAmount' => $res['result'][0]['totalAmount'],
												'driver_Earnings' => $res['result'][0]['driver_Earnings'], 
												'site_Earnings' => $res['result'][0]['site_Earnings']
											);
				}else{
					$resultArr = array('totalAmount' => $res['result'][0]['driver_Earnings'] + $res['result'][0]['site_Earnings'],
												'driver_Earnings' => $res['result'][0]['driver_Earnings'], 
												'site_Earnings' => $res['result'][0]['site_Earnings']
											);
				}
            }
        }
        return $resultArr;
    }
	
	
	/**
	*
	* This function return the total amount in site
	*
	* */
	public function get_current_wallet_balance() {
		$option = array(array('$project' => array('wallet_amount' => 1,
																	'status' => 1
																)
								),
								array('$group' => array('_id' => '$email',
																	'count'=> array('$sum'=>1),
																	'totalAmount' => array('$sum'=>'$wallet_amount')
															)
								)
						);
		$res = $this->mongo_db->aggregate(USERS, $option);
		$totalWallet = 0;
		if (!empty($res)) {
			if (isset($res['result'][0]['totalAmount'])) {
				$totalWallet = round($res['result'][0]['totalAmount'],2);
			}
		}
		return $totalWallet;
	}
		/**
	*
	* This function return the todat total  in operator
	*
	* */
	    public function get_all_today_records($table,$condition){
        $date=date('Y-m-d'); 
        $from_date = $date.' 00:00:00';
        $to_date = $date.' 23:59:59';
        
        $this->mongo_db->select('_id');
        
        if($table == RIDES)
            $condition['booking_information.booking_date'] = array('$lte' => MongoDATE(strtotime($to_date)),'$gte' => MongoDATE(strtotime($from_date)));
        else
            $condition['created'] = array('$lte' => $to_date,'$gte' => $from_date );
      
        $this->mongo_db->where($condition, TRUE);    
        $todayRidesdata = $this->mongo_db->get($table);
        return $todayRidesdata->num_rows();
    }

}