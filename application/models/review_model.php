<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');
/**
* 
* This model contains all db functions related to Review management
* @author Casperon
*
**/
 
class Review_model extends My_Model{
	public function __construct(){
		parent::__construct();
	}
	public function get_avg_review_count($start_star='',$end_star=''){
		
	   $matchArr=array('$match' => array('avg_review' =>array('$gte'=>(string)$start_star,'$lte'=>(string)$end_star)));
						
	   $groupArr=array('$group' => array(
										'_id' =>'$_id',
										'totaluser'=>array('$sum'=>1)
									)
						);						
	   $option = array(array('$project' => array(
										'avg_review' =>1
         							)
								),
								$matchArr,
								$groupArr
								
							);
		
		$res = $this->mongo_db->aggregate(USERS,$option);
		return $res;
	}
	public function get_avg_review_count_driver($start_star='',$end_star=''){
		
	   $matchArr=array('$match' => array('avg_review' =>array('$gte'=>(string)$start_star,'$lte'=>(string)$end_star)));
						
	   $groupArr=array('$group' => array(
										'_id' =>'$_id',
										'totaldriver'=>array('$sum'=>1)
									)
						);						
	   $option = array(array('$project' => array(
										'avg_review' =>1
         							)
								),
								$matchArr,
								$groupArr
								
							);
		
		$res = $this->mongo_db->aggregate(DRIVERS,$option);
		return $res;
	}
	public function top_review_user() {
	      
		  $option = array(
                array(
                    '$project' => array(
                        'avg_review' => 1,
                        'user_name' => 1,
                        'email' => 1,
                        'image' => 1,
                        'status' => 1,
						'no_of_rides'=>1
                        
                    )
                ),
                array(
                    '$match' => array(
                        'status' =>'Active'
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
		 
		
        $res = $this->mongo_db->aggregate(USERS, $option);
        return $res;
    }
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
                    '$match' => array(
                        'status' =>'Active'
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
	public function get_avg_reasonwise_review_user() {
	    $option = array(
                array(
                    '$project' => array(
                        'ride_id' => 1,
                        'booking_information.booking_date' => 1,
                        'driver.id' => 1,
						'ratings'=>1,
                        'ride_status' => 1,
						'rider_review_status'=>1
                    )
                ),
                array(
                    '$match' => array(
                        'booking_information.booking_date' => array('$gte' => MongoDATE(strtotime(date('Y-01-d 00:00:00'))), '$lte' => MongoDATE(strtotime(date('Y-m-d H:i:s')))),
                        'ride_status' =>'Completed',
                        'rider_review_status' =>'Yes'
                    )
                )
            );
         
        $res = $this->mongo_db->aggregate(RIDES, $option);
        return $res;
	
	}
	public function get_avg_reasonwise_review_driver() {
	    $option = array(
                array(
                    '$project' => array(
                        'ride_id' => 1,
                        'booking_information.booking_date' => 1,
                        'driver.id' => 1,
						'ratings'=>1,
                        'ride_status' => 1,
						'driver_review_status'=>1
                    )
                ),
                array(
                    '$match' => array(
                        'booking_information.booking_date' => array('$gte' => MongoDATE(strtotime(date('Y-01-d 00:00:00'))), '$lte' => MongoDATE(strtotime(date('Y-m-d H:i:s')))),
                        'ride_status' =>'Completed',
                        'driver_review_status' =>'Yes'
                    )
                )
            );
         
        $res = $this->mongo_db->aggregate(RIDES, $option);
        return $res;
	
	}


}	