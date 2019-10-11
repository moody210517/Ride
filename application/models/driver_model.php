<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

/**
*
* This model contains all db functions related to user management
* @author Casperon
*
**/
 
class Driver_model extends My_Model{
	public function __construct(){
        parent::__construct();
    }

	/**
	*
	* Check driver exist or not
	*
	**/
	public function check_driver_exist($condition = array()){ 
		$this->mongo_db->select();
		$this->mongo_db->where($condition);
		return $res = $this->mongo_db->get(DRIVERS);  
	}
	
	/**
	* 
	* This function selects the vehicles list by category using where IN
	*/
    public function get_vehicles_list_by_category($idsList=''){  
		$ids=array();
		foreach($idsList as $val){
			$ids[]=MongoID($val);
		}
		$idsList = array();
		$this->mongo_db->where_in('_id',$ids);
		$this->mongo_db->where(array('status' => 'Active'));
		$res = $this->mongo_db->get(VEHICLES); 
		return $res;
    }
	
	
	/**
	*
	* This function return the trip summary
	*	String $driver_id
	*
	**/
	public function get_trip_summary($driver_id = '',$start_date = '',$end_date = ''){
		
		if($start_date!='' && $end_date!=''){
			$matchArr=array(
										'$match' => array(
											'driver.id' =>array('$eq'=>$driver_id),
											'ride_status' =>array('$eq'=>'Completed'),
											'pay_status' =>array('$eq'=>'Paid'),
											'history.end_ride' =>array('$gte'=>MongoDATE($start_date),'$lte'=>MongoDATE($end_date))
										)
									);
		}else{
			$matchArr=array(
										'$match' => array(
											'driver.id' =>array('$eq'=>$driver_id),
											'ride_status' =>array('$eq'=>'Completed'),
											'pay_status' =>array('$eq'=>'Paid')
										)
									);		
		}
								
		$option = array(
								array(
									'$project' => array(
										'ride_id' =>1,
										'commission_percent' =>1,
										'driver' =>1,
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
								$matchArr
							);
		#echo "<pre>";print_r($option);
		$res = $this->mongo_db->aggregate(RIDES,$option);
		return $res;
	}
	
	/**
	*
	* This function return the total earnings
	*
	**/
	public function get_total_earnings($driver_id=''){
		$option = array(								
								array(
									'$project' => array(
										'ride_status' =>1,
										'driver' =>1,
										'total' =>1
									)
								),							
								array(
									'$match' => array(
										'ride_status' =>array('$eq'=>'Completed'),
										'driver.id' =>array('$eq'=>$driver_id)
									)
								),
								array(
									'$group' => array(
										'_id' =>'$ride_status',
										'ride_status'=>['$last'=>'$ride_status'],
										'totalAmount'=>array('$sum'=>'$total.grand_fare')
									)
								)
							);
		$res = $this->mongo_db->aggregate(RIDES,$option);
		$totalAmount=0;
		if(!empty($res)){ 
			if(isset($res['result'][0]['totalAmount'])){
				$totalAmount=$res['result'][0]['totalAmount'];
			}
		}
		return $totalAmount;
	}
  public function get_available_category($condition = array()) {
        $data = array();
        $k = 0;
        foreach ($condition as $key => $value) {
            $data[$k] = MongoID($value);
            $k++;
        }
        $this->mongo_db->select();
        $this->mongo_db->where_in('_id', $data);
        $res = $this->mongo_db->get(CATEGORY);
        return $res;
    }
	
	public function get_driver_last_ride_status($driver_id=''){
		$this->mongo_db->select(array('ride_id','ride_status'));
		$this->mongo_db->where(array('driver.id' => $driver_id));
		$this->mongo_db->order_by(array('ride_id' => 'DESC'));
		$this->mongo_db->limit(1);
		return $res = $this->mongo_db->get(RIDES);  
	}
	/**
	*
	* Retures the number drivers category wise
	**/
	public function get_drivers_categorywise() {
		$uCount = 0;
		
		$option = array(
			array(
				'$project' => array(
					'category' => 1
					
				)
			),
			
			array(
				'$group' => array(
					'_id' =>'$category',
					'drivercount'=>array('$sum'=>1),
				)
			)
		);		
        $res = $this->mongo_db->aggregate(DRIVERS, $option);
		
		return $res;
	}
	

	public function get_drivers_categorywisebyoperator($operator_id = '') {
		$uCount = 0;		
		$option = array(
			array(
				'$project' => array(
					'category' => 1,
					'operator_id' => 1								
				)
			),
			array(
				'$match' => array(
					'operator_id' =>array('$eq'=>$operator_id),					
				)
			),
			array(
				'$group' => array(
					'_id' =>'$category',
					'drivercount'=>array('$sum'=>1),
				)
			)
		);
		$this->mongo_db->where(array('operator.id' => $operator_id));
        $res = $this->mongo_db->aggregate(DRIVERS, $option);
		
		return $res;
	}
	


	/**
	*
	* Retures the number drivers category wise
	**/
	public function get_drivers_locationwise() {
		$uCount = 0;
		
		$option = array(
			array(
				'$project' => array(
					'driver_location' => 1
					
				)
			),
			array(
				'$group' => array(
					'_id' =>'$driver_location',
					'drivercount'=>array('$sum'=>1),
				)
			)
		);
        $res = $this->mongo_db->aggregate(DRIVERS, $option);		
		return $res;
	}
		
	public function get_drivers_locationwisebyoperator($operator_id = '') {
		$uCount = 0;
		
		$option = array(
			array(
				'$project' => array(
					'driver_location' => 1,
					'operator_id' => 1
					
				)
			),
			array(			
				'$match' => array(
					'operator_id' =>array('$eq'=>$operator_id),					
				)

			),	
			array(
				'$group' => array(
					'_id' =>'$driver_location',
					'drivercount'=>array('$sum'=>1),
				)
			)
		);
        $res = $this->mongo_db->aggregate(DRIVERS, $option);
		
		return $res;
	}
	


	/***Returns the top 3 rated driver */
	public function top_review_driver() {
	      
		  $option = array(
                array(
                    '$project' => array(
                        'avg_review' => 1,
                        'driver_name' => 1,
                        'email' => 1,
                        'image' => 1,
                        'status' => 1,
						'no_of_rides'=>1
                        
                    )
                ),
               array(
				   '$sort'=>array(
				   
					'avg_review'=>-1
				   )
			   )
			   ,
			   array('$limit'=>3
			   )
            );
		 
		
        $res = $this->mongo_db->aggregate(DRIVERS, $option);
        return $res;
    }
    
	/***Returns the bottom 3 rated driver */
	public function bottom_review_driver() {
	      
		  $option = array(
                array(
                    '$project' => array(
                        'avg_review' => 1,
                        'driver_name' => 1,
                        'email' => 1,
                        'image' => 1,
                        'status' => 1,
						'no_of_rides'=>1
                        
                    )
                ),
               array(
				   '$sort'=>array(
				   
					'avg_review'=>1
				   )
			   )
			   ,
			   array('$limit'=>3
			   )
            );
		 
		
        $res = $this->mongo_db->aggregate(DRIVERS, $option);
        return $res;
    }
	public function get_top_revenue(){
	 
	    $matchArr=array('$match' => array('ride_status' =>array('$eq'=>'Completed'),
										'pay_status' =>array('$eq'=>'Paid')
									 )
					   );
						
	    $groupArr=array('$group' => array('_id' =>'$driver.id',
										'totalTrips'=>array('$sum'=>1),
										 'totalRevenue'=>array('$sum'=>'$driver_revenue'),
										 'driver'=>array('$first'=>'$driver')
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
	public function get_top_rides(){
	 
	    $matchArr=array('$match' => array('ride_status' =>array('$eq'=>'Completed'),
										'pay_status' =>array('$eq'=>'Paid')
									 )
					   );
						
	    $groupArr=array('$group' => array('_id' =>'$driver.id',
										'totalTrips'=>array('$sum'=>1),
										 'totalRevenue'=>array('$sum'=>'$driver_revenue'),
										 'driver'=>array('$first'=>'$driver')
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
    public function billing_list($driver_id='',$limit='',$offset='') {
	   #echo $driver_id;
	   $matchArr = array('driver_id'=>array('$eq'=>$driver_id));
	   $option = array(
           array(
                '$project' => array(
                    'invoice_id'=>1,
                    'bill_from'=>1,
                    'driver_id'=>1,
                    'bill_to'=>1,
                    'driver_earnings'=>1,
                    'bill_date'=>1,
                    'total_rides'=>1,
                )
            ),
			array('$match' =>$matchArr),
            array(
                '$sort' => array(
                    'bill_date' =>-1
                )
			),
			array('$skip' =>(int)$offset),
			array('$limit' =>$limit)
			
        );
	
        $res = $this->mongo_db->aggregate(BILLINGS, $option);
		
        return $res;
    }
    public function get_online_duration($driver_id = '', $fromDate = '',$toDate='') {
        $option = array(
            array(
                '$project' => array(
                    'record_date' => 1,
                    'total_duration' => 1,
                    'driver_id' => 1
                )
            ),
            array(
                '$match' => array(
                    'record_date' => array('$gte' => MongoDATE($fromDate), '$lte' => MongoDATE($toDate)),
                    'driver_id' => MongoID($driver_id)
                )
            ),
            array('$group' => array('_id' =>'$driver_id',
                                'totalRecords'=>array('$sum'=>1),
                                'totalDuration'=>array('$sum'=>'$total_duration'),
                            )
            )
        );
        $res = $this->mongo_db->aggregate(DRIVERS_ONLINE_DURATION, $option);
        return $res;
    }
	
}