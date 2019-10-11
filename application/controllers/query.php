<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

class Query extends MY_Controller {
	function __construct(){
        parent::__construct();
		$this->load->helper(array('cookie','date','form'));
		$this->load->library(array('encrypt','form_validation'));		
		$this->load->model(array('user_model'));
		$this->load->model(array('app_model'));
		$this->load->model(array('dashboard_model'));
    }
	public function geoindex(){
        $this->mongo_db->ensure_index(USERS,array('loc'=>'2dsphere'));
	}
    public function check_index(){
        $this->mongo_db->ensure_index(DRIVERS,array('email'=>'1'),array('unique'=>TRUE));
        echo "done";
	}
	public function v_ride(){
		$rideId = $this->uri->segment(3);
		$getRide=$this->user_model->get_all_details(RIDES, array('ride_id' =>$rideId));
		echo '<pre>'; print_r($getRide->result()); 
        
        echo $pickup_time = date("d-m-Y H:i", MongoEPOCH($getRide->row()->booking_information['drop_date']));
	}
    public function time_check() {
        echo date('a',time());
    }
    
    public function fcm_check() {
        $url = "https://fcm.googleapis.com/fcm/send";
        $token = array("cO2vfO5pvKw:APA91bHaC1FHQFlEJ7qmlUNPT93g-SwpBGBDBaxWOANOAfW2QlzwtKGvHpgXz5HrA5UidZ6DZyAnxpbSsa7sI4fqyUP_PJHmnEVIPo6bWE4A3racblv1xH3ULED0G4ye_Xwqgy4m5iUD");
        $serverKey = 'AIzaSyDOQt3gnuEUiq01-0pYXs0MQZpmezo_RY0';
        $serverKey = 'AAAARbw6MC8:APA91bHdKKnbfIp4_UmPt93nqYv-R-IC0XoQcjxxQxu4H1ZXWOv0R75CdM9oZfMjdtbS2lSMgNrhdFJktE_lnrLcU9dUySLMWinHW8CwaxdM0zRNPxL-UhMRCyZPKppGN3KVERbUuzVr';
        
        $title = "Title";
        $body = "sample messgae from fcm";
        $message=array('message'=>'Test PushNotification',
                        'action'=>'ads',
                        'key1'=>'Test PushNotification',
                        'key2'=>'sample'
                      );
        $notification = array('title' =>$title , 'text' =>$message['message'], 'sound' => 'default', 'badge' => '1');
        $message_array=array();
        $message_array = array("message" => json_encode($message));
        $arrayToSend = array('registration_ids'=>$token,'notification' => $notification,'priority'=>'high','data' => $message_array,'content_available'=>true);
        #'time_to_live'=>30
        $json = json_encode($arrayToSend);
        $headers = array();
        $headers[] = 'Content-Type: application/json';
        $headers[] = 'Authorization: key='. $serverKey;
        $ch = curl_init();
        curl_setopt($ch, CURLOPT_URL, $url);

        curl_setopt($ch, CURLOPT_CUSTOMREQUEST,

        "POST");
        curl_setopt($ch, CURLOPT_POSTFIELDS, $json);
        curl_setopt($ch, CURLOPT_HTTPHEADER,$headers);
        //Send the request
        $response = curl_exec($ch);
        print_r($response);
        //Close request
        if ($response === FALSE) {
            die('FCM Send Error: ' . curl_error($ch));
        }
        curl_close($ch);
    
    }
    
    public function sample() {
       
       $sortArr = array('ride_id' => -1);
	   $firstRide = $this->app_model->get_selected_fields(RIDES, array('ride_id'=>'96263233','ride_status' => array('$in' => array('Finished','Completed'))), array('_id','ride_id'),$sortArr,1,0);
       echo "<pre>";
       print_r($firstRide->num_rows());
       
    }
    public function update_location() {
        $latitude=13.0573121;
        $longitude=80.2509929;
        $geo_data = array('loc' => array('lon' => floatval($longitude), 'lat' => floatval($latitude)),'last_active_time'=>new MongoDB\BSON\UTCDateTime((time()*1000)));
        $this->app_model->update_details(DRIVERS, $geo_data, array());
        echo "done";
    }
   
    public function performance_check() {
     
        header('Content-type:application/json;charset=utf-8');
        echo "<pre>";
        $total_time_start=microtime(true);
        $update_start=microtime(true);
        $ride_detail = $this->app_model->update_details(RIDES,array('checking_status'=>'Completed'),array('ride_id'=>'627194'));
        $update_end=microtime(true);
        $update_time = $update_end - $update_start;
        $update="update processed=".$update_start."-".$update_end."=".$update_time." ms";
        echo $update;
        echo "<br>";
        
        
        $insert_start=microtime(true);
        $insert_array=array('name'=>'sample','sample'=>'arockiasamy');
        $ride_detail = $this->app_model->simple_insert('testing_insert',$insert_array);
        $insert_end=microtime(true);
        $insert_time = $insert_end - $insert_start;
        $insert="insert processed=".$insert_start."-".$insert_end."=".$insert_time." ms";
        echo $insert;
        echo "<br>";
        
        
        $query_start=microtime(true);
        $ride_detail = $this->app_model->get_all_details(RIDES,array(),array(),3000,0);
        $query_end=microtime(true);
        $query_time = $query_end - $query_start;
        $str="query processed=".$query_start."-".$query_end."=".$query_time." ms";
        echo $str;
        echo "<br>";
        $ride_list=array();
        $php_start=microtime(true);
		foreach($ride_detail->result() as $data) {
            $ride_list[]=$data;
		}
        $php_end=microtime(true);
        $php_time = $php_end - $php_start;
        $query="php processed=".$php_start."-".$php_end."=".$php_time." ms";
        echo $query;
        echo "<br>";
        $json_start=microtime(true);
        $json_encode = json_encode($ride_list, JSON_PRETTY_PRINT);
        $json_end=microtime(true);
        $json_time = $json_end - $json_start;
        $json="Json processed=".$json_start."-".$json_end."=".$json_time." ms";
        echo $json;
        echo "<br>";
        $tot_time=$query_time+$php_time+$json_time+$update_time+$insert_time;
        echo "<br>";
        echo "Total processing Time".$tot_time;
        echo "<br>";
        
        echo $this->cleanString($json_encode);
        $total_time_end=microtime(true);
        $overall_time=$total_time_end-$total_time_start;
        echo "<br>";
        echo "over processing Time".$overall_time;
       
    }
    public function get_near() {
        $pickup_lat=13.0573121;
        $pickup_lon=80.2509929;
        $map_searching_radius = 1000;
        $coordinates = array(floatval($pickup_lon), floatval($pickup_lat));
        $distance_unit='km';
        $distanceMultiplier = 0.001;
		if($distance_unit == 'km'){
			$distanceMultiplier = 0.001;
		} else if($distance_unit == 'mi'){
			$distanceMultiplier = 0.000621371;
		} else if($distance_unit == 'm'){
			$distanceMultiplier = 1;
		}
        $matchArr =    array('availability' => array('$eq' => 'Yes'),
										'status' => array('$eq' => 'Active'),
										'verify_status' => array('$eq' => 'Yes'),
										#'last_active_time' => array('$gte' => MongoDATE(time()-1800)),
										#'_id'=>array('$nin'=>$requested_drivers)
							);
        $limit=10;
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
					'additional_category' => 1
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
        echo "<pre>";
        print_r($res);
        exit;
    }
	public function h_ride(){
		$rideId = $this->uri->segment(3);
		$getRideHIstory = $this->user_model->get_all_details(TRAVEL_HISTORY,array('ride_id' => $rideId));
		echo '<pre>'; print_r($getRideHIstory->result()); die;
	}   
	public function j_ride() {
		$res_arr = array();
		$res2_arr = array();
		$res3_arr = array();
		$res4_arr = array();
		$val1 = array();
		$val2 = array();
		$rideId = $this->uri->segment(3);
		$getRideHIstory = $this->user_model->get_all_details(TRAVEL_HISTORY,array('ride_id' => $rideId));
		foreach ($getRideHIstory->result() as $key => $data) {
			foreach($data->history_end as $value) {
				$val1[]=array('lat'=>$value['lat'],'lon'=>$value['lon']); 
			}
		}
		$json_encode = json_encode($val1);
		echo $this->cleanString($json_encode);
	}
    public function get_driver_update() {
        $mobile_number=$this->uri->segment(3,0);
        $checkAccount=$this->app_model->get_all_details(DRIVERS, array('mobile_number' =>$mobile_number),array('email','last_active_time','last_dashboard','last_login','loc','version_code'));
         echo "<pre>";
        if($checkAccount->num_rows() >0 ){
            if(isset($checkAccount->row()->last_active_time)){
              echo date('d-m-Y h:i:s a',MongoEPOCH($checkAccount->row()->last_active_time));  
            }
           
            print_r($checkAccount->row()->loc);
            echo "<br>";  
            echo date('d-m-Y h:i:s a',MongoEPOCH($checkAccount->row()->last_dashboard));
            echo "<br>";  
            echo $checkAccount->row()->version_code; 
            echo "<br>";            
            echo date('d-m-Y h:i:s a',MongoEPOCH($checkAccount->row()->last_login));
            echo "<br>";
        }
       print_r($checkAccount->result());
          /*$checkAccount=$this->app_model->get_all_details(ONLINE_DRIVERS,array());
         echo "<pre>";
         print_r($checkAccount->result()); */
        
    }
	
	public function makers_model() {
		/* $data=file_get_contents(base_url().'vehicle_maker_models_years.json');
		$new_data=json_decode($data);
		
		foreach($new_data as $key=>$record) {
			foreach($record as $maker) {
					
					$maker_name=$maker->name;
					$insertArr['brand'] = '';
					$insertArr['brand_name'] = '';
					$sel_fields = array('_id','brand_name');
					$seourl = str_replace(' ','-',trim($maker_name));
					$condition = array('seourl' => $seourl);
					$checkMaker = $this->user_model->get_selected_fields(BRAND, $condition,$sel_fields);
					if($checkMaker->num_rows() == 1){
						$insertArr['brand'] = (string)$checkMaker->row()->_id;
						$insertArr['brand_name'] = $checkMaker->row()->brand_name;
					} else {
						$brand_data= array('brand_name' => ucfirst($maker_name), 'seourl' => $seourl,'status' => 'Active','created' => date('Y-m-d H:i:s'),'brand_logo' => '');
						$this->user_model->simple_insert(BRAND,$brand_data);
						$insertArr['brand'] = (string)$this->mongo_db->insert_id();
					    $insertArr['brand_name'] = ucfirst($maker_name);
					}
					$insertArr['type'] = '';
					$insertArr['type_name'] = '';
			
					
					$checkType = $this->user_model->get_selected_fields(VEHICLES,array(),array('_id','vehicle_type'));
					$vehicle_type=array();
					foreach($checkType->result() as $data) {
						if($data->vehicle_type!='') {
							$vehicle_type[]=array('type'=>(string)$data->_id,'type_name'=>$data->vehicle_type);
						}
					}
					
					$random_index=rand(0,count($vehicle_type));
					$insertArr['type']=$vehicle_type[$random_index]['type'];
					$insertArr['type_name']=$vehicle_type[$random_index]['type_name'];
					if($insertArr['type']=='' && $insertArr['type_name']=='') {
						$insertArr['type']=(string)$checkType->row()->_id;
						$insertArr['type_name']=$checkType->row()->vehicle_type;
					}
					
					//print_r($maker->models);
					foreach($maker->models as $models) {
						//print_r($models);
						$model_condition = array();
						$insertArr['name'] = '';
						$insertArr['name'] = $models->name;
						$sel_fields = array('_id','name');
						$condition = array('$or'=>array(array('name' => $models->name),array('name' => ucfirst($models->name)),array('name' => strtolower($models->name)),array('name' => strtoupper($models->name)),array('name' => ucwords($models->name))));
						$checkModels = $this->user_model->get_selected_fields(MODELS, $condition,$sel_fields);
						if($checkModels->num_rows() == 1){
							$model_condition = array('name' => $checkModels->row()->name);
							
						} else {
							$years_array=array();
							foreach($models->years as $year) {
								
								$years_array[]=$year->year;
							}
							//print_r($years_array);
							
							$insertArr['year_of_model']=$years_array;
							$insertArr['status']='Active';
							
							unset($insertArr['_id']);
							
							$this->user_model->simple_insert(MODELS,$insertArr);
							
						}
						
				  }
			}
		} */
		
	}
	public function remove_make_model() {
		/* $this->user_model->commonDelete(BRAND,array());
        $this->user_model->commonDelete(MODELS,array()); */
	}
	public function v_driver(){
		$driver_id=$this->uri->segment(3);
		$getInfo = $this->user_model->get_all_details(DRIVERS, array('_id' => MongoID(($driver_id))));
		if($getInfo->num_rows()>0){
			echo "<pre>"; print_r($getInfo->result());
		}else{
			echo "Invalid";
		}
	}
	public function v_user(){
		$driver_id=$this->uri->segment(3);
		$getInfo = $this->user_model->get_all_details(USERS, array('_id' => MongoID(($driver_id))));
		if($getInfo->num_rows()>0){
			echo "<pre>"; print_r($getInfo->result());
		}else{
			echo "Invalid";
		}
	}
    
    public function driver_activities() {
        #$this->user_model->commonDelete(DRIVERS_ACTIVITY,array()); #exit;
		#die;
        
        
        foreach($ride_info_detail->result() as  $record) {
			$chkActivity = $this->user_model->get_all_details(DRIVERS_ACTIVITY,array('ride_id'=>$record->ride_id));
			if($chkActivity->num_rows() == 0){
				$pay_type='';
				if (isset($record->pay_summary['type'])) {
					$pay_type = $record->pay_summary['type'];
				}
				$ride_type='Normal Ride';
				if(isset($record->ride_type) && $record->ride_type!='') {
					$ride_type=$record->ride_type;
				}
				if(isset($record->pool_ride) && $record->pool_ride=='Yes') {
					$ride_type = 'Share Ride';
				}
				$driver_ratting=0;
				if(isset($record->driver_review_status) && $record->driver_review_status=='Yes') {
					$driver_ratting=$record->ratings['driver']['avg_rating'];
				}
				
				$dataArr=array('ride_id'=>$record->ride_id,
							   'ride_type'=> ucfirst($ride_type),
							   'driver_id'=>$record->driver['id'],
							   'booking_date'=>MongoDATE($record->history['booking_time']->sec),
							   'end_date'=>MongoDATE($record->history['end_ride']->sec),
							   'activity_time'=>MongoDATE($record->history['end_ride']->sec),
							   'category'=>$record->booking_information['service_type'],
							   'ratting'=>(string)$driver_ratting,
							   'driver_earning'=>floatval($record->driver_revenue),
							   'payment_method'=>$pay_type,
							   'activity'=>'ride'
							  );
				
				echo $this->user_model->simple_insert(DRIVERS_ACTIVITY,$dataArr);
			}
         }
    }
    
    public function driver_activity() {
        #echo "ssss";
        #$this->user_model->commonDelete(DRIVERS_ACTIVITY,array()); exit;
		#die;
        ini_set('memory_limit', '-1');
        $ride_info_detail = $this->user_model->get_selected_fields(RIDES,array('ride_status'=>'Completed'),array('total','ride_status','pay_status','pay_summary','ride_id','pool_ride','driver','user','booking_information','history','driver_review_status','ratings','driver_revenue'));
        echo "<pre>";
        #print_r($ride_info_detail->result());
        #exit;
        
        foreach($ride_info_detail->result() as  $record) {
			$chkActivity = $this->user_model->get_all_details(DRIVERS_ACTIVITY,array('ride_id'=>$record->ride_id));
            echo $chkActivity->num_rows();
            
			if($chkActivity->num_rows() == 0){
				$pay_type='';
				if (isset($record->pay_summary['type'])) {
					$pay_type = $record->pay_summary['type'];
				}
				$ride_type='Normal';
				if(isset($record->ride_type) && $record->ride_type!='') {
					$ride_type=$record->ride_type;
				}
				if(isset($record->pool_ride) && $record->pool_ride=='Yes') {
					$ride_type = 'Share';
				}
				$driver_ratting=0;
				if(isset($record->driver_review_status) && $record->driver_review_status=='Yes') {
					$driver_ratting=$record->ratings['driver']['avg_rating'];
				}
				
				$dataArr=array('ride_id'=>$record->ride_id,
							   'ride_type'=> ucfirst($ride_type),
							   'driver_id'=>$record->driver['id'],
							   'booking_date'=>MongoDATE(MongoEPOCH($record->history['booking_time'])),
							   'end_date'=>MongoDATE(MongoEPOCH($record->history['end_ride'])),
							   'activity_time'=>MongoDATE(MongoEPOCH($record->history['end_ride'])),
							   'category'=>$record->booking_information['service_type'],
							   'ratting'=>(string)$driver_ratting,
							   'driver_earning'=>floatval($record->driver_revenue),
							   'payment_method'=>$pay_type,
							   'activity'=>'ride'
							  );
				
                
			  $this->user_model->simple_insert(DRIVERS_ACTIVITY,$dataArr);
              #exit;
			} else if($chkActivity->num_rows() > 1){
                $ride_id=$chkActivity->row()->ride_id;
                $this->user_model->commonDelete(DRIVERS_ACTIVITY,array('ride_id'=>$ride_id));
            }
         }
         echo "done";
    }
	
	public function gMap(){
		$ride_id=$this->uri->segment(3);
		$this->load->helper(array('ride_helper'));
		create_and_save_travel_path_in_map($ride_id);
	}
	
	public function u_wallet(){
		$userInfo = $this->user_model->get_all_details(USERS,array());
		if($userInfo->num_rows()>0){
			foreach($userInfo->result() as $row){
				$user_id = (string)$row->_id;
				$walletInfo = $this->app_model->get_all_details(WALLET, array('user_id' => MongoID($user_id)));
				if($walletInfo->num_rows() == 1){
					$amount = floatval($walletInfo->row()->total);
					if($amount<=0){
						$amount = 0;
					}
					$this->app_model->update_details(USERS,array('wallet_amount' =>floatval($amount)),array('_id' => MongoID($user_id)));
					
					echo $user_id." : ".$amount.'<br/>';
					
				}
			}
		}
	}
	public function invoice_check() {
		$this->mail_model->send_invoice('883483','arockiasamy@casperon.in');
		echo "done";
	}
	public function update_amount_ride() {
		$ride_detail = $this->app_model->get_all_details(RIDES, array('ride_status' =>'Completed'));
		foreach($ride_detail->result() as $data) {
			$original_grand_fare=$data->total['grand_fare'];
			$grand_fare=round($data->total['grand_fare']);
			$this->app_model->update_details(RIDES,array('total.original_grand_fare' =>floatval($original_grand_fare),'total.grand_fare'=>floatval($grand_fare)),array('ride_id' =>$data->ride_id));
		}
	}
    public function update_loc() {
    
        $ride_detail = $this->app_model->get_all_details(RIDE_STATISTICS, array());
		foreach($ride_detail->result() as $data) {
            $longitude=$data->location['lon'];
            $latitude=$data->location['lat'];
            $coordinates = array(floatval($longitude), floatval($latitude));
            $location = $this->app_model->find_location(floatval($longitude), floatval($latitude),"Yes");
            if (!empty($location['result'])) {
                $location_id=$location['result'][0]['_id'];
                $this->app_model->update_details(RIDE_STATISTICS,array('location_id'=>$location_id),array('_id' =>$data->_id));
            }
        }
        echo "updated";
    }
	public function invoice_mail() {
		$message=file_get_contents('http://192.168.1.251:8081/arockia/invoice/invoice.html');
		
		$invoicename = time(). '.pdf';
		$file_to_save = 'trip_invoice/' . $invoicename;
		include("./mpdf/mpdf.php");
		$mpdf=new mPDF($langcode); 
		//$stylesheet = file_get_contents('pdf.css');
		//$mpdf->WriteHTML($stylesheet,1);
		$mpdf->WriteHTML(stripcslashes($message));
		$mpdf->Output($file_to_save,'D');
		$attachments = $file_to_save;
	
	}
	
	public function refer_history() {
	
		$ride_detail = $this->app_model->get_all_details(REFER_HISTORY,array('history'=>array('$all'=>array(array('$elemMatch'=>array('reference_id'=>'581876efcae2aa4c1700002a','used'=>'true'))))));
		echo "<pre>";
		print_r($ride_detail->result());
	}
	
}

/* End of file query.php */
/* Location: ./application/controllers/query.php */