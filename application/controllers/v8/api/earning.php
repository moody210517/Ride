<?php if (!defined('BASEPATH')) exit('No direct script access allowed');

/**
* 
* This controller contains the functions related to Drivers at the app end
* @author Casperon
*
* */
class Earning extends MY_Controller {

    function __construct() {
        parent::__construct();
        $this->load->helper(array('cookie', 'date', 'form', 'email','mileage_helper'));
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
			$apply_function = array();
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
     * This Function returns the driver rides list
     *
     * */
    public function billing_list() {
        $returnArr['status'] = '0';
        $returnArr['response'] = '';
        try {
            $driver_id = (string) $this->input->post('driver_id');
            $offset = $this->input->post('offset');
            if ($driver_id != '') {
                $driverVal = $this->app_model->get_selected_fields(DRIVERS, array('_id' => MongoID($driver_id)), array('city', 'avail_category'));
                if ($driverVal->num_rows() > 0) {
                   
                    if($offset > 0)  {
                        $limit=1;
                        $offset=$offset-1;
                        $billingDetails = $this->driver_model->billing_list($driver_id,$limit,$offset);
                        if(count($billingDetails['result']) > 0) {
                            
                            
                            $billing_period=date('M d',MongoEPOCH($billingDetails['result'][0]['bill_from']))." - ".date('M d',MongoEPOCH($billingDetails['result'][0]['bill_to']));
                            $day_earning=array();
                            $date  = date('Y-m-d',MongoEPOCH($billingDetails['result'][0]['bill_from']));
                            $end_date = date('Y-m-d',MongoEPOCH($billingDetails['result'][0]['bill_to']));
                            $total_rides=0;
                            $total_earning=0;
                            while (strtotime($date) <= strtotime($end_date)) {
                                $fromdate=$date." 00:00:00";
                                $todate=$date." 23:59:59";
                                $rideSummary = $this->revenue_model->get_day_earning($fromdate,$todate,$driver_id);
                                
                                if(!empty($rideSummary['result'])) {
                                   $driver_earning=$rideSummary['result'][0]['driver_earnings'];
                                   $total_earning+=$rideSummary['result'][0]['driver_earnings'];
                                   $total_rides+=$rideSummary['result'][0]['totalTrips'];
                                   $day_earning[]= array('date'=>$date,
                                   'chart_date'=>date('d M',strtotime($date)),
                                   'amount'=>$driver_earning
                                   );
                                } else {
                                   $driver_earning=0;
                                   $day_earning[]= array('date'=>$date,
                                   'chart_date'=>date('d M',strtotime($date)),
                                   'amount'=>$driver_earning
                                   ); 
                                }
                                $date = date ("Y-m-d", strtotime("+1 day", strtotime($date)));
                            }
                            $sortArr = array('record_date' => 'DESC');
                            $onlineHours = $this->driver_model->get_online_duration($driver_id,MongoEPOCH($billingDetails['result'][0]['bill_from']),MongoEPOCH($billingDetails['result'][0]['bill_to']));
                            $duration="0 hrs 0 Mins";
                            if(isset($onlineHours['result'][0]['totalDuration'])){
                                $duration = convertToHoursMins($onlineHours['result'][0]['totalDuration'],'%02d:%02d');
                            }
                            $returnArr['status'] = '1';
                            $returnArr['response'] = array('billing_period' =>(string)$billing_period, 
                                                           'amount' =>$total_earning,
                                                           'total_rides' =>$total_rides,
                                                           'total_onlinehours' =>$duration,
                                                           'day_earning' =>$day_earning,
                                                           'currency' =>$this->data['dcurrencyCode']
                                                        );
                        } else {
                           $returnArr['response'] = $this->format_string("No earnings Available", "no_earnings_available"); 
                        }
                    } else {
                        $billing_next="+".$this->data['billing_cycle']." day";
                        $billing_period=date("M d",strtotime("+1 day",strtotime($this->data['last_billing_date'])))." - ".date("M d",strtotime($billing_next,strtotime($this->data['last_billing_date'])));
						$day_earning=array();
						$date  = date('Y-m-d',strtotime("+1 day",strtotime($this->data['last_billing_date'])));
						$end_date = date('Y-m-d');
						$total_rides=0;
						$total_earning=0;
                        $fdate=$date." 00:00:00";
						while (strtotime($date) <= strtotime($end_date)) {
							$fromdate=$date." 00:00:00";
							$todate=$date." 23:59:59";
							$rideSummary = $this->revenue_model->get_day_earning($fromdate,$todate,$driver_id);
							
							if(!empty($rideSummary['result'])) {
							   $driver_earning=$rideSummary['result'][0]['driver_earnings'];
							   $total_earning+=$rideSummary['result'][0]['driver_earnings'];
							   $total_rides+=$rideSummary['result'][0]['totalTrips'];
							   $day_earning[]= array('date'=>$date,
							   'chart_date'=>date('d M',strtotime($date)),
							   'amount'=>$driver_earning
							   );
							} else {
							   $driver_earning=0;
							   $day_earning[]= array('date'=>$date,
							   'chart_date'=>date('d M',strtotime($date)),
							   'amount'=>$driver_earning
							   ); 
							}
							$date = date ("Y-m-d", strtotime("+1 day", strtotime($date)));
						}
						$sortArr = array('record_date' => 'DESC');
						$bill_from=strtotime(date('Y-m-d',strtotime("+1 day",strtotime($this->data['last_billing_date'])))." 00:00:00");
						$bill_to=strtotime(date('Y-m-d')." 23:59:59");
						$onlineHours = $this->driver_model->get_online_duration($driver_id,$bill_from,$bill_to);
						$duration="0 hrs 0 Mins";
						
						$curDuration=0;
						$condition = array('driver_id'=>MongoID($driver_id)); 
						
						$remain_online_hours = $this->driver_model->get_all_details(DRIVERS_ONLINE_DURATION,$condition,array('record_id' => 'DESC'),1,0);
						if(isset($remain_online_hours->row()->history)){
							$lastRec = end($remain_online_hours->row()->history);
							if(isset($lastRec['in_time'])&&($lastRec['out_time']=='')){
								$inTime = MongoEPOCH($lastRec['in_time']);
								if(strtotime($fdate) > $inTime) $inTime = strtotime($fdate);
								$curDuration = round((time() - $inTime)/60);
							}
						}
						if($curDuration > 0) $duration = convertToHoursMins($curDuration,'%02d hrs:%02d mins');
						if(isset($onlineHours['result'][0]['totalDuration'])){
							$totDur = $curDuration + $onlineHours['result'][0]['totalDuration'];
							$duration = convertToHoursMins($totDur,'%02d hrs:%02d mins');
						}
						
						$returnArr['status'] = '1';
						$returnArr['response'] = array('billing_period' =>(string)$billing_period, 
													   'amount' =>$total_earning,
													   'total_rides' =>$total_rides,
													   'total_onlinehours' =>$duration,
													   'day_earning' =>$day_earning,
													   'currency' =>$this->data['dcurrencyCode']
													);
						  
						
                      
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
    public function ride_list() {
        $returnArr['status'] = '0';
        $returnArr['response'] = '';
        try {
            $driver_id = (string) $this->input->post('driver_id');
            $date = $this->input->post('date');
            $date_to = $this->input->post('date_to');
            if ($driver_id != '' && $date!='') {
                $driverVal = $this->app_model->get_selected_fields(DRIVERS, array('_id' => MongoID($driver_id)), array('city', 'avail_category'));
                if ($driverVal->num_rows() > 0) {
                        $fromdate=$date." 00:00:00";
                        $todate=$date." 23:59:59";
                        if($date_to!='') {
                            $todate=$date_to." 23:59:59";
                        }
                        $rideSummary = $this->revenue_model->get_ride_list($fromdate,$todate,$driver_id);
                        $activityArr=array();
                        if(!empty($rideSummary['result'])) {
                          foreach($rideSummary['result'] as $data) {
                            if($data['activity']=='ride') {
                                $pay_type=str_replace("_"," & ",$data['payment_method']);
                                $activityArr[]=array('ride_id'=>$data['ride_id'],
                                                     'ride_type'=>$data['ride_type'],
                                                     'activity'=>ucfirst($data['activity']),
                                                     'driver_earning'=>number_format($data['driver_earning'],2,'.',''),
                                                     'payment_type'=>$pay_type,
                                                     'ratting'=>$data['ratting'],
                                                     'category'=>$data['category'],
                                                     'activity_time'=>date('F d, Y h:i a',MongoEPOCH($data['activity_time']))
                                                    );
                            } else {
                                $activityArr[]=array('activity'=>ucfirst($data['activity']),
                                                     'activity_time'=>date('F d, Y h:i a',MongoEPOCH($data['activity_time']))
                                                    );
                            }
                          }
                        } 
                        $returnArr['status'] = '1';
                        $billing_period=date('M d',strtotime($fromdate));
                        if($date_to!='') {
                           $billing_period=date('M d',strtotime($fromdate))." - ".date('M d',strtotime($todate)); 
                        }
                        
                        $returnArr['response'] = array('activity_list'=>$activityArr,
                                                       'currency' =>$this->data['dcurrencyCode'],
                                                       'date' =>$billing_period
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
    
    public function ride_summary() { 
        $returnArr['status'] = '0';
        $returnArr['response'] = '';
        try {
            $driver_id = (string) $this->input->post('driver_id');
            $offset = $this->input->post('offset');
            if ($driver_id != '') {
                $driverVal = $this->app_model->get_selected_fields(DRIVERS, array('_id' => MongoID($driver_id)), array('city', 'avail_category'));
                if ($driverVal->num_rows() > 0) {
                   
                    if($offset > 0)  {
                        $limit=1;
                        $offset=$offset-1;
                        $billingDetails = $this->driver_model->billing_list($driver_id,$limit,$offset);
                        if(count($billingDetails['result']) > 0) {
                            $billing_period=date('M d',MongoEPOCH($billingDetails['result'][0]['bill_from']))." - ".date('M d',MongoEPOCH($billingDetails['result'][0]['bill_to']));
                            $day_earning=array();
                            $fromdate=strtotime(date('Y-m-d',MongoEPOCH($billingDetails['result'][0]['bill_from']))." 00:00:00");
                            $todate=strtotime(date('Y-m-d',MongoEPOCH($billingDetails['result'][0]['bill_to']))." 23:59:59");
                            $rideSummary = $this->revenue_model->get_driver_summary($fromdate,$todate,$driver_id);
                            $net_fare=0;
                            $net_array=array();
                            $trips_array=array();
                            $ratting_array=array();
                           
                            if(!empty($rideSummary['result'])) {
                               /* echo "<pre>";
                               print_r($rideSummary['result']); */
                               $net_fare=$rideSummary['result'][0]['driver_earnings'];
                               $total_trips=$rideSummary['result'][0]['totalTrips'];
                               $total_distance=$rideSummary['result'][0]['total_distance'];
                               $total_distance_earning=$rideSummary['result'][0]['total_distance_earning'];
                               $total_time_earning=$rideSummary['result'][0]['total_time_earning'];
                               $total_ride_mins=$rideSummary['result'][0]['total_hours'];
                               $per_hour_earning=round((($rideSummary['result'][0]['total_time_earning']/$rideSummary['result'][0]['total_hours'])*60),2);
                               $per_trip=round(($net_fare/$total_trips),2);
                               $distance_unit = $this->data['d_distance_unit'];
                               $per_trip_distance=$total_distance." ".$distance_unit;
                               $per_distance=0;
                               if($total_distance > 0) {
                                $per_distance=round(($total_distance_earning/$total_distance),2);
                               }
                               $total_trips=$rideSummary['result'][0]['totalTrips'];
                               $total_trips_hour=convertToHoursMins($rideSummary['result'][0]['total_hours'],'%02d hrs:%02d mins');
                               $sortArr = array('record_date' => 'DESC');
                               $onlineHours = $this->driver_model->get_online_duration($driver_id,MongoEPOCH($billingDetails['result'][0]['bill_from']),MongoEPOCH($billingDetails['result'][0]['bill_to']));
                                $total_online_duration="0 hrs 0 Mins";
                                if(isset($onlineHours['result'][0]['totalDuration'])){
                                    $total_online_duration = convertToHoursMins($onlineHours['result'][0]['totalDuration'],'%02d hrs:%02d mins');
                                }
                                $ratting_avg=$rideSummary['result'][0]['total_rattings']/$total_trips;
                                
                                
                                
                                $net_array[] = array("title"=>(string)$this->format_string("Net Fare", "net_fare",FALSE),
                                                     "value"=>(string)number_format($net_fare,2,'.',''),
                                                     "currency"=>'1'
                                                     );
                                $net_array[] = array("title"=>(string)$this->format_string("Per Trip", "per_trip",FALSE),
                                                     "value"=>(string)number_format($per_trip,2,'.',''),
                                                     "currency"=>'1'
                                                     );
                                $net_array[] = array("title"=>(string)$this->format_string("Per Hour on trip", "per_hour_ontrip",FALSE),
                                                     "value"=>(string)number_format($per_hour_earning,2,'.',''),
                                                     "currency"=>'1'
                                                     );
                                 $net_array[] = array("title"=>(string)$this->format_string("Per Distance on trip", "per_distance_ontrip",FALSE),
                                                     "value"=>(string)number_format($per_distance,2,'.',''),
                                                     "currency"=>'1'
                                                     );
                                $trips_array[]=array("title"=>(string)$this->format_string("Trips", "trips",FALSE),
                                                     "value"=>(string)$total_trips,
                                                     "currency"=>'0'
                                                     );
                                $trips_array[]=array("title"=>(string)$this->format_string("Time Online", "time_online",FALSE),
                                                     "value"=>(string)$total_online_duration,
                                                     "currency"=>'0'
                                                     );
                                $trips_array[]=array("title"=>(string)$this->format_string("Trip Hour", "trip_hour",FALSE),
                                                     "value"=>(string)$total_trips_hour,
                                                     "currency"=>'0'
                                                     );
                                $trips_array[]=array("title"=>(string)$this->format_string("Trip Distance", "trip_distance",FALSE),
                                                     "value"=>(string)$per_trip_distance,
                                                     "currency"=>'0'
                                                     );
                                $ratting_array[]=array("title"=>(string)$this->format_string("Ratings", "rattings",FALSE),
                                                     "value"=> round($ratting_avg, 2),
                                                     "currency"=>'0'
                                                     );
                            } 
                            
                            $returnArr['status'] = '1';
                            $returnArr['response'] = array('net_breakup' =>$net_array, 
                                                           'trip_breakup'=>$trips_array,
                                                           'ratting_stats'=>$ratting_array,
                                                           'billing_period'=>$billing_period,
                                                           'billing_from'=>date('Y-m-d',MongoEPOCH($billingDetails['result'][0]['bill_from'])),
                                                           'billing_to'=>date('Y-m-d',MongoEPOCH($billingDetails['result'][0]['bill_to'])),
                                                           'net_fare'=>number_format($net_fare,2,'.',''),
                                                           'currency' =>$this->data['dcurrencyCode']
                                                        );
                      } else {
                            $returnArr['response'] = $this->format_string("No earnings Available", "no_earnings_available"); 
                      }
                 } else {
                            $billing_period=date("M d",strtotime("+1 day",strtotime($this->data['last_billing_date'])))." - ".date("M d");
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
                               
                               $net_fare=$rideSummary['result'][0]['driver_earnings'];
                               $total_trips=$rideSummary['result'][0]['totalTrips'];
                               $total_distance=$rideSummary['result'][0]['total_distance'];
                               $total_distance_earning=$rideSummary['result'][0]['total_distance_earning'];
                               $total_time_earning=$rideSummary['result'][0]['total_time_earning'];
                               $total_ride_mins=$rideSummary['result'][0]['total_hours'];
                               $per_hour_earning=round((($rideSummary['result'][0]['total_time_earning']/$rideSummary['result'][0]['total_hours'])*60),2);
                               $per_trip=round(($net_fare/$total_trips),2);
                               $distance_unit = $this->data['d_distance_unit'];
                               $per_trip_distance=$total_distance." ".$distance_unit;
                               $per_distance=0;
                               if($total_distance > 0) {
                                $per_distance=round(($total_distance_earning/$total_distance),2);
                               }
                               $total_trips=$rideSummary['result'][0]['totalTrips'];
                               $total_trips_hour=convertToHoursMins($rideSummary['result'][0]['total_hours'],'%02d hrs:%02d mins');
                               $sortArr = array('record_date' => 'DESC');
                               $bill_from=$fromdate;
                               $bill_to=$todate;
                               $onlineHours = $this->driver_model->get_online_duration($driver_id,$bill_from,$bill_to);
                                $total_online_duration="0 hrs 0 Mins";
                                if(isset($onlineHours['result'][0]['totalDuration'])){
                                    $total_online_duration = convertToHoursMins($onlineHours['result'][0]['totalDuration'],'%02d hrs:%02d mins');
                                }
                                $ratting_avg=$rideSummary['result'][0]['total_rattings']/$total_trips;
                                
                                
                                $net_array[] = array("title"=>(string)$this->format_string("Net Fare", "net_fare",FALSE),
                                                     "value"=>(string)number_format($net_fare,2,'.',''),
                                                     "currency"=>'1'
                                                     );
                                $net_array[] = array("title"=>(string)$this->format_string("Per Trip", "per_trip",FALSE),
                                                     "value"=>(string)number_format($per_trip,2,'.',''),
                                                     "currency"=>'1'
                                                     );
                                $net_array[] = array("title"=>(string)$this->format_string("Per Hour on trip", "per_hour_ontrip",FALSE),
                                                     "value"=>(string)number_format($per_hour_earning,2,'.',''),
                                                     "currency"=>'1'
                                                     );
                                 $net_array[] = array("title"=>(string)$this->format_string("Per Distance on trip", "per_distance_ontrip",FALSE),
                                                     "value"=>(string)number_format($per_distance,2,'.',''),
                                                     "currency"=>'1'
                                                     );
                                $trips_array[]=array("title"=>(string)$this->format_string("Trips", "trips",FALSE),
                                                     "value"=>(string)$total_trips,
                                                     "currency"=>'0'
                                                     );
                                $trips_array[]=array("title"=>(string)$this->format_string("Time Online", "time_online",FALSE),
                                                     "value"=>(string)$total_online_duration,
                                                     "currency"=>'0'
                                                     );
                                $trips_array[]=array("title"=>(string)$this->format_string("Trip Hour", "trip_hour",FALSE),
                                                     "value"=>(string)$total_trips_hour,
                                                     "currency"=>'0'
                                                     );
                                $trips_array[]=array("title"=>(string)$this->format_string("Trip Distance", "trip_distance",FALSE),
                                                     "value"=>(string)$per_trip_distance,
                                                     "currency"=>'0'
                                                     );
                                $ratting_array[]=array("title"=>(string)$this->format_string("Ratings", "rattings",FALSE),
                                                     "value"=> round($ratting_avg, 2),
                                                     "currency"=>'0'
                                                     );
                            } 
                            
                            $returnArr['status'] = '1';
                            $returnArr['response'] = array('net_breakup' =>$net_array, 
                                                           'trip_breakup'=>$trips_array,
                                                           'ratting_stats'=>$ratting_array,
                                                           'billing_period'=>$billing_period,
                                                           'billing_from'=>$billing_from,
                                                           'billing_to'=>$billing_to,
                                                           'net_fare'=>number_format($net_fare,2,'.',''),
                                                           'currency' =>$this->data['dcurrencyCode']
                                                        );
                     
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
    
	
}
/* End of file driver.php */
/* Location: ./application/controllers/v9/api/earning.php */