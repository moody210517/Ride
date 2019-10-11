<?php

if (!defined('BASEPATH'))
    exit('No direct script access allowed');

/**
 * 
 * This controller contains the functions related to Drivers at the admin end
 * @author Casperon
 *
 * */
class Insert_drivers extends MY_Controller {

    function __construct() {
        parent::__construct();
        $this->load->helper(array('cookie', 'date', 'form'));
        $this->load->library(array('encrypt', 'form_validation'));
        $this->load->model(array('driver_model'));
        $this->load->model(array('app_model'));
    }
	
	
    /**
     *
     * This function Displays the Driver Category List
     *
     * */
    public function get_drivers_category() {
        $returnArr['status'] = '0';
		
		$categoryArr = array();
		$condition = array();
        $categoryList = $this->driver_model->get_all_details(CATEGORY, $condition);
		if($categoryList->num_rows()>0){
			foreach($categoryList->result() as $row){
				$categoryArr[] = array('id'=> (string)$row->_id,'name'=>$row->name);
			}
		}
		
		if(!empty($categoryArr)){
			$returnArr['status'] = '1';
		}
		$returnArr['response'] = $categoryArr;
		
		echo json_encode($returnArr);
    }

    /**
     *
     * This function Inserts & Edits the drivers
     *
     * */
    public function addNewDriver() {
		$returnArr['status'] = '0';
		$returnArr['response'] = '';
		$returnArr['password'] = '';
		
		$email = strtolower($this->input->post('email'));
		$latitude = $this->input->post('latitude');
		$longitude = $this->input->post('longitude');
		$txtPlaces = $this->input->post('txtPlaces');
		$categorytype = $this->input->post('catname');
		
		if($email!='' && $latitude!='' && $longitude!='' && $txtPlaces!='' && $categorytype!=''){
			$orgpassword = $this->app_model->get_random_string(8);
			$password = md5($orgpassword);
			$checkEmail = $this->driver_model->check_driver_exist(array('email' => $email));
			if($checkEmail->num_rows() >= 1){
				$returnArr['response'] = 'This Email id already registered with us.';
			}
			if($returnArr['response']==''){
				$url = "https://maps.google.com/maps/api/geocode/json?address=".$latitude.",".$longitude."&sensor=false".$this->data['google_maps_api_key'];
				$json = file_get_contents($url);
				$jsonArr = json_decode($json);
				$newAddress = $jsonArr->{'results'}[0]->{'address_components'};
				
				#echo "<pre>"; print_r($newAddress);
				$addressArr['street'] = ''; $addressArr['street1'] = ''; $addressArr['area'] = ''; $addressArr['location'] = ''; $addressArr['city'] = ''; 
				$addressArr['state'] = ''; $addressArr['country'] = ''; $addressArr['country_code'] = ''; $addressArr['zip'] = ''; 
				foreach($newAddress as $nA){
					if($nA->{'types'}[0] == 'route')$addressArr['street'] = $nA->{'long_name'};
					if($nA->{'types'}[0] == 'sublocality_level_2')$addressArr['street1'] = $nA->{'long_name'};
					if($nA->{'types'}[0] == 'sublocality_level_1')$addressArr['area'] = $nA->{'long_name'};
					if($nA->{'types'}[0] == 'locality')$addressArr['location'] = $nA->{'long_name'};
					if($nA->{'types'}[0] == 'administrative_area_level_2')$addressArr['city'] = $nA->{'long_name'};
					if($nA->{'types'}[0] == 'administrative_area_level_1')$addressArr['state'] = $nA->{'long_name'};
					if($nA->{'types'}[0] == 'country')$addressArr['country'] = $nA->{'long_name'};
					if($nA->{'types'}[0] == 'country')$addressArr['country_code'] = $nA->{'short_name'};
					if($nA->{'types'}[0] == 'postal_code')$addressArr['zip'] = $nA->{'long_name'};
				}
				
				if(!array_key_exists('city',$addressArr)){
					if($addressArr['state']!=""){
						$addressArr['city'] = $addressArr['state'];
					}else if($addressArr['country']!=""){
						$addressArr['city'] = $addressArr['country'];
					}
				}
				
				$driver_location = '';
				$location = $this->app_model->find_location(floatval($longitude), floatval($latitude));
				if(empty($location['result'])){
						$location = $this->driver_model->get_all_details(LOCATIONS,array());
						if($location->num_rows()>0){
							$driver_location = (string)$location->row()->_id;
						}
				}else{
					$driver_location = (string)$location['result'][0]['_id'];
				}
				
				$category = array('category'=>MongoID($categorytype));
				$sampleDriver = $this->driver_model->get_all_details(DRIVERS,$category,array(),1,0);
							
				$category=(string)$sampleDriver->row()->category;
				$vehicle_type=(string)$sampleDriver->row()->vehicle_type;
				$vehicle_maker=(string)$sampleDriver->row()->vehicle_maker;
				$vehicle_model=(string)$sampleDriver->row()->vehicle_model;
																								
				$driver_array = array(
												"driver_location"=>(string)$driver_location,
												"category"=>MongoID($category),
												"driver_name"=>$email,
												"password"=>$password,
												"vehicle_maker"=>$vehicle_maker,
												"vehicle_model"=>$vehicle_model,
												"vehicle_number"=>"TN ".time(),
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
													"address"=>(string)$addressArr['location'],
													"county"=>(string)$addressArr['country'],
													"state"=>(string)$addressArr['state'],
													"city"=>(string)$addressArr['city'],
													"postal_code"=>(string)$addressArr['zip'],
												),
												"documents"=>array(),
												"created_by"=>'Other'
											) ;
				#echo '<pre>'; print_r($driver_array);  die;
				
				$this->driver_model->simple_insert(DRIVERS,$driver_array);
				$last_insert_id = $this->mongo_db->insert_id();
				$fields = array(
					'username' => (string) $last_insert_id,
					'password' => md5((string) $last_insert_id)
				);
				$url = $this->data['soc_url'] . 'create-user.php';
				$this->load->library('curl');
				$output = $this->curl->simple_post($url, $fields);
		
				$returnArr['status'] = '1';
				$returnArr['password'] =(string)$orgpassword;
				$returnArr['response'] = "Your driver account registered successfully.";
				/* Update Stats Starts*/
				$current_date = MongoDATE(strtotime(date("Y-m-d 00:00:00")));
				$field=array('driver.hour_'.date('H')=>1,'driver.count'=>1);
				$this->driver_model->update_stats(array('day_hour'=>$current_date),$field,1);
				/* Update Stats End*/
				
				$this->mail_model->send_driver_register_confirmation_mail((string)$last_insert_id);
			}
		}else{
			$returnArr['response'] = 'Some Informations are missing.';
		}
		echo json_encode($returnArr);
    }

}

/* End of file insert_drivers.php */
/* Location: ./application/controllers/insert_drivers.php */