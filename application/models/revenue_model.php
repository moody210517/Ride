<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');
/*
 *
 * This model contains all db functions related to Revenue and commission management
 * @author Casperon
 *
 */
class Revenue_model extends My_Model{
	public function __construct(){
        parent::__construct();
    }
	
	/**
	*
	* This function return the rides details
	*	String $driver_id
	*
	**/
	public function get_ride_details($driver_id = '',$start_date = '',$end_date = '',$having = ''){
		
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
		if($having=='site'){
			$matchArr['$match']['pay_summary.type']=array('$in'=>array('Gateway','Wallet','Wallet_Gateway','FREE'));
			$groupArr=array(
									'$group' => array(
										'_id' =>'$driver.id',
										'tipsAmount'=>array('$sum'=>'$total.tips_amount'),
										'site_having'=>array('$sum'=>'$total.paid_amount')
									)
								);
		}else if($having=='driver'){
			$matchArr['$match']['pay_summary.type']=array('$in'=>array('Cash','Wallet_Cash'));
			$groupArr=array(
									'$group' => array(
										'_id' =>'$driver.id',
										'tipsAmount'=>array('$sum'=>'$total.tips_amount'),
										'driver_having'=>array('$sum'=>'$total.paid_amount'),
									)
								);
		}else{
			$groupArr=array(
									'$group' => array(
										'_id' =>'$driver.id',
										'totalTrips'=>array('$sum'=>1),
										'totalAmount'=>array('$sum'=>'$total.grand_fare'),
										'couponAmount'=>array('$sum'=>'$total.coupon_discount'),
										'tipsAmount'=>array('$sum'=>'$total.tips_amount'),
										'by_wallet'=>array('$sum'=>'$total.wallet_usage'),
										'site_earnings'=>array('$sum'=>'$amount_commission'),
										'driver_earnings'=>array('$sum'=>'$driver_revenue'),
										'amount_in_site'=>array('$sum'=>'$amount_detail.amount_in_site'),
										'amount_in_driver'=>array('$sum'=>'$amount_detail.amount_in_driver')
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
								$matchArr,
								$groupArr
							);
		#echo "<pre>";print_r($option);
		$res = $this->mongo_db->aggregate(RIDES,$option);
		return $res;
	}
	public function get_ride_details_company($driver_id = '',$start_date = '',$end_date = '',$company_id = ''){
		
		if($start_date!='' && $end_date!='' && $company_id!=''){
			$matchArr=array(
										'$match' => array(
											'driver.id' =>array('$eq'=>$driver_id),
											'company_id' =>array('$eq'=>$company_id),
											'ride_status' =>array('$eq'=>'Completed'),
											'pay_status' =>array('$eq'=>'Paid'),
											'history.end_ride' =>array('$gte'=>MongoDATE($start_date),'$lte'=>MongoDATE($end_date))
										)
									);
		}else if($company_id!=''){
			$matchArr=array(
										'$match' => array(
											'driver.id' =>array('$eq'=>$driver_id),
											'company_id' =>array('$eq'=>$company_id),
											'ride_status' =>array('$eq'=>'Completed'),
											'pay_status' =>array('$eq'=>'Paid')
										)
									);		
		} else {
			$matchArr=array(
										'$match' => array(
											'driver.id' =>array('$eq'=>$driver_id),
											'ride_status' =>array('$eq'=>'Completed'),
											'pay_status' =>array('$eq'=>'Paid')
										)
									);		
		}
		$groupArr=array('$group' => array(
										'_id' =>'$driver.id',
										'totalTrips'=>array('$sum'=>1),
										'totalAmount'=>array('$sum'=>'$total.grand_fare'),
										'couponAmount'=>array('$sum'=>'$total.coupon_discount'),
										'tipsAmount'=>array('$sum'=>'$total.tips_amount'),
										'by_wallet'=>array('$sum'=>'$total.wallet_usage'),
										'site_earnings'=>array('$sum'=>'$amount_commission'),
										'driver_earnings'=>array('$sum'=>'$driver_revenue'),
										'amount_in_site'=>array('$sum'=>'$amount_detail.amount_in_site'),
										'amount_in_driver'=>array('$sum'=>'$amount_detail.amount_in_driver')
									)
							);
		
		$option = array(
								array(
									'$project' => array(
										'ride_id' =>1,
										'commission_percent' =>1,
										'driver' =>1,
										'company_id' =>1,
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
								$groupArr
								
							);
		#echo "<pre>";print_r($option);
		$res = $this->mongo_db->aggregate(RIDES,$option);
		return $res;
	}
	
	/**
	*
	* This function return the rides details
	*	String $driver_id
	*
	**/
	public function get_ride_summary($start_date = '',$end_date = '',$having = '',$locationId=''){
		
		if($start_date!='' && $end_date!=''){
			$matchArr=array(
										'$match' => array(
											'ride_status' =>array('$eq'=>'Completed'),
											'pay_status' =>array('$eq'=>'Paid'),
											'history.end_ride' =>array('$gte'=>MongoDATE($start_date),'$lte'=>MongoDATE($end_date))
										)
									);
		}else{
			$matchArr=array(
										'$match' => array(
											'ride_status' =>array('$eq'=>'Completed'),
											'pay_status' =>array('$eq'=>'Paid')
										)
									);		
		}
		
		$groupArr=array(
									'$group' => array(
										'_id' =>'$ride_status',
										'totalTrips'=>array('$sum'=>1),
										'totalAmount'=>array('$sum'=>'$total.grand_fare'),
										'couponAmount'=>array('$sum'=>'$total.coupon_discount'),
										'by_wallet'=>array('$sum'=>'$total.wallet_usage'),
										'site_earnings'=>array('$sum'=>'$amount_commission'),
										'driver_earnings'=>array('$sum'=>'$driver_revenue')
									)
								);
		if($locationId!=''&& $locationId!='all') {
			
			$matchArr['$match']['location.id'] = $locationId;
		}
		$option = array(
								array(
									'$project' => array(
										'ride_id' =>1,
										'location.id' =>1,
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
										'amount_commission' =>1
									)
								),
								$matchArr,
								$groupArr
							);
		#echo '<pre>'; print_r($option); die;
		$res = $this->mongo_db->aggregate(RIDES,$option);
		return $res;
	}
	public function get_ride_summary_company($start_date = '',$end_date = '',$company_id='',$locationId=''){
		
		if($start_date!='' && $end_date!='' && $company_id!=''){
			$matchArr=array(
										'$match' => array(
											'ride_status' =>array('$eq'=>'Completed'),
											'company_id' =>array('$eq'=>$company_id),
											'pay_status' =>array('$eq'=>'Paid'),
											'history.end_ride' =>array('$gte'=>MongoDATE($start_date),'$lte'=>MongoDATE($end_date))
										)
									);
		}else if($company_id!=''){
			$matchArr=array(
										'$match' => array(
											'ride_status' =>array('$eq'=>'Completed'),
											'pay_status' =>array('$eq'=>'Paid'),
											'company_id' =>array('$eq'=>$company_id)
										)
									);		
		} else {
		
			$matchArr=array(
										'$match' => array(
											'ride_status' =>array('$eq'=>'Completed'),
											'pay_status' =>array('$eq'=>'Paid')
											
										)
									);		
		}
        if($locationId!=''&& $locationId!='all') {
			
			$matchArr['$match']['location.id'] = $locationId;
		}
		
		$groupArr=array(
									'$group' => array(
										'_id' =>'$ride_status',
										'totalTrips'=>array('$sum'=>1),
										'totalAmount'=>array('$sum'=>'$total.grand_fare'),
										'couponAmount'=>array('$sum'=>'$total.coupon_discount'),
										'by_wallet'=>array('$sum'=>'$total.wallet_usage'),
										'site_earnings'=>array('$sum'=>'$amount_commission'),
										'driver_earnings'=>array('$sum'=>'$driver_revenue')
									)
								);
		
		$option = array(
								array(
									'$project' => array(
										'ride_id' =>1,
										'company_id' =>1,
										'commission_percent' =>1,
										'driver' =>1,
                                        'location.id' =>1,
										'total' =>1,
										'booking_information' =>1,
										'ride_status' =>1,
										'pay_status' =>1,
										'summary' =>1,
										'pay_summary' =>1,
										'history' =>1,
										'driver_revenue' =>1,
										'amount_commission' =>1
									)
								),
								$matchArr,
								$groupArr
							);
		#echo "<pre>";print_r($option);
		$res = $this->mongo_db->aggregate(RIDES,$option);
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
    public function get_day_earning($start_date = '',$end_date = '',$driver_id=''){
		
		if($start_date!='' && $end_date!=''){
			$matchArr=array(
										'$match' => array(
											'ride_status' =>array('$eq'=>'Completed'),
											'pay_status' =>array('$eq'=>'Paid'),
											'driver.id' =>array('$eq'=>$driver_id),
											'history.end_ride' =>array('$gte'=>MongoDATE(strtotime($start_date)),'$lte'=>MongoDATE(strtotime($end_date)))
										)
									);
		}
		
		$groupArr=array(
									'$group' => array(
										'_id' =>'$ride_status',
										'totalTrips'=>array('$sum'=>1),
										'driver_earnings'=>array('$sum'=>'$driver_revenue')
									)
								);
		
		$option = array(
								array(
									'$project' => array(
										'ride_id' =>1,
										'location.id' =>1,
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
										'amount_commission' =>1
									)
								),
								$matchArr,
								$groupArr
							);
		$res = $this->mongo_db->aggregate(RIDES,$option);
		return $res;
	}
    
    public function get_ride_list($start_date = '',$end_date = '',$driver_id=''){
		if($start_date!='' && $end_date!=''){
			$matchArr=array(
                                        '$match' => array(
										     'driver_id' =>array('$eq'=>$driver_id),
											'activity_time' =>array('$gte'=>MongoDATE(strtotime($start_date)),'$lte'=>MongoDATE(strtotime($end_date)))
										)
						 );
		}
		
		$option = array(
                            array(
                                '$project' => array(
                                    'ride_id' =>1,
                                    'ride_type' =>1,
                                    'driver_id' =>1,
                                    'booking_date' =>1,
                                    'activity_time' =>1,
                                    'category' =>1,
                                    'ratting' =>1,
                                    'driver_earning' =>1,
                                    'payment_method' =>1,
                                    'activity' =>1,
                                   
                                )
                            ),
                            $matchArr
					);
		$res = $this->mongo_db->aggregate(DRIVERS_ACTIVITY,$option);
		return $res;
	}
     public function get_driver_summary($start_date = '',$end_date = '',$driver_id=''){
		
		if($start_date!='' && $end_date!='' && $driver_id!=''){
			$matchArr=array(
                            '$match' => array(
                                'driver.id' =>array('$eq'=>$driver_id),
                                'ride_status' =>array('$eq'=>'Completed'),
                                'pay_status' =>array('$eq'=>'Paid'),
                                'history.end_ride' =>array('$gte'=>MongoDATE($start_date),'$lte'=>MongoDATE($end_date))
                            )
						  );
		}
		$groupArr=array(
                        '$group' => array(
                            '_id' =>'$ride_status',
                            'totalTrips'=>array('$sum'=>1),
                            'totalAmount'=>array('$sum'=>'$total.grand_fare'),
                            'driver_earnings'=>array('$sum'=>'$driver_revenue'),
                            'total_hours'=>array('$sum'=>'$summary.ride_duration'),
                            'total_distance'=>array('$sum'=>'$summary.ride_distance'),
                            'total_time_earning'=>array('$sum'=>'$total.ride_time'),
                            'total_distance_earning'=>array('$sum'=>'$total.distance'),
                            'total_rattings'=>array('$sum'=>array('$toDouble'=>'$ratings.driver.avg_rating'))
                         )
					  );
                    
		$option = array(
								array(
									'$project' => array(
										'ride_id' =>1,
										'location.id' =>1,
										'commission_percent' =>1,
										'driver' =>1,
										'total' =>1,
										'ratings' =>1,
										'booking_information' =>1,
										'ride_status' =>1,
										'pay_status' =>1,
										'summary' =>1,
										'pay_summary' =>1,
										'history' =>1,
										'driver_revenue' =>1,
                                        'total_rattings' =>1,
										'amount_commission' =>1
									)
								),
								$matchArr,
								$groupArr
							);
		#echo '<pre>'; print_r($option); die;
		$res = $this->mongo_db->aggregate(RIDES,$option);
       # print_R($res);die;
		return $res;
	}
}

?>