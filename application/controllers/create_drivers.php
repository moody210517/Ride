<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

/**
* 
* This controller contains the functions related to Create Multiple Drivers
* @author Casperon
*
**/

class Create_drivers extends MY_Controller {
	function __construct(){
        parent::__construct();
		$this->load->helper(array('cookie','date','form'));
		$this->load->library(array('encrypt','form_validation'));		
		$this->load->model(array('driver_model'));
    }
	
	public function index(){
		$limit=50;
		$totalDrivers = $this->driver_model->get_selected_fields(DRIVERS,array(),array('_id'))->count();
		$totalDrivers=intval($totalDrivers);
		if($totalDrivers<0){
			$totalDrivers=10;
		}
		$total_target=$totalDrivers+$limit;
		$i=0;
		for($d=$totalDrivers;$d<=$total_target;$d++){
			$i++;
			$siteName = preg_replace('/[^A-Za-z0-9\-\']/', '', $this->config->item('email_title'));
			$email= $siteName.'_driver'.$d.'@'.$siteName.'.com';
			$password = strtolower($siteName.'_driver'.$d);
			$email=strtolower($email);
			$checkEmail = $this->driver_model->check_driver_exist(array('email' => $email));
			if($checkEmail->num_rows() >= 1){
				continue;
			}
			if($i==$limit){
				die;
			}
			
			$totalDrivers = $this->driver_model->get_selected_fields(DRIVERS,array(),array('_id'))->count();
			$offset = rand(0,$totalDrivers);
			$sampleDriver = $this->driver_model->get_all_details(DRIVERS,array(),array(),1,($offset-1));
						
			$category=(string)$sampleDriver->row()->category;
			$vehicle_type=(string)$sampleDriver->row()->vehicle_type;
			$vehicle_maker=(string)$sampleDriver->row()->vehicle_maker;
			$vehicle_model=(string)$sampleDriver->row()->vehicle_model;
																							
			$driver_array = array(
                                "driver_location"=>"55c211a6cae2aac003000029",
                                "category"=>MongoID($category),
                                "driver_name"=>str_replace('_',' ',$password),
                                "password"=>md5($password),
                                "vehicle_maker"=>$vehicle_maker,
                                "vehicle_model"=>$vehicle_model,
                                "vehicle_number"=>"TN ".$d,
                                "status"=>"Active",
                                "verify_status"=>"Yes",
                                "created"=>date("Y-m-d H:i:s"),
                                "email"=>$email,
                                "vehicle_type"=>MongoID($vehicle_type),
                                "ac"=>"Yes",
                                "no_of_rides"=>floatval(0),
                                "availability"=>"Yes",
                                "mode"=>"Available",
                                "dail_code"=>'+91',
                                "mobile_number"=>(string)floatval(time()),
                                "address"=>array(
                                    "address"=>"address",
                                    "county"=>"India",
                                    "state"=>"State",
                                    "city"=>"City",
                                    "postal_code"=>(string)time(),
                                ),
                                "documents"=>array(),
                                "created_by"=>'Auto'
                            ) ;
			#echo '<pre>'; print_r($driver_array);  die;
			
			$this->driver_model->simple_insert(DRIVERS,$driver_array);
			echo '<br>'.$d;
			/* Update Stats Starts*/
			$current_date = MongoDATE(strtotime(date("Y-m-d 00:00:00")));
			$field=array('driver.hour_'.date('H')=>1,'driver.count'=>1);
			$this->driver_model->update_stats(array('day_hour'=>$current_date),$field,1);
			/* Update Stats End*/
		}
	}
	
}

/* End of file create_drivers.php */
/* Location: ./application/controllers/create_drivers.php */