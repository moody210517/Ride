<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');
/**
*
*	Location Management for admin 
* 
*	@package	CI
*	@subpackage	Controller
*	@author	Casperon
*
**/

class Location extends MY_Controller {

	function __construct(){
        parent::__construct();
		$this->load->helper(array('cookie','date','form'));
		$this->load->library(array('encrypt','form_validation'));		
		$this->load->model('location_model');
		if ($this->checkPrivileges('location',$this->privStatus) == FALSE){
			redirect(ADMIN_ENC_URL);
		}
		
		$c_fun = $this->uri->segment(3);
		$restricted_function = array('delete_location','change_location_status','change_location_status_global','updateLocationBoundary','insertEditLocation','insertCopyLocation');
		if(in_array($c_fun,$restricted_function) && $this->data['isDemo']===TRUE){
			$this->setErrorMessage('error','You are in demo version. you can\'t do this action.','admin_template_demo_version');
			redirect($_SERVER['HTTP_REFERER']); die;
		}
    }
    
    /**
	* 
	* its redirect to the location list page
	*
	* @return HTTP REDIRECT,to the location list page
	*
	**/
   	public function index(){	
		if ($this->checkLogin('A') == ''){
			redirect(ADMIN_ENC_URL);
		}else {
			redirect(ADMIN_ENC_URL.'/location/display_location_list');
		}
	}
	/**
	* 
	* its display the location list page
	*
	* @return HTML,location list page
	*
	**/
	public function display_location_list(){
		if ($this->checkLogin('A') == ''){
			redirect(ADMIN_ENC_URL);
		}else {
		    if ($this->lang->line('admin_menu_location_list') != '') 
		    $this->data['heading']= stripslashes($this->lang->line('admin_menu_location_list')); 
		    else  $this->data['heading'] = 'Location List';
			$condition = array();
			$sortArr = array('city'=>'ASC');
			$this->data['locationList'] = $this->location_model->get_all_details(LOCATIONS,$condition);
			$this->load->view(ADMIN_ENC_URL.'/location/display_location',$this->data);
		}
	}
	/**
	* 
	* its add or edit the location
	*
	* @param string $location_id  Location MongoDB\BSON\ObjectId
	* @return HTML,location add or edit page
	*
	**/
	public function add_edit_location(){
		if ($this->checkLogin('A') == ''){
			redirect(ADMIN_ENC_URL);
		}else {
			$location_id = $this->uri->segment(4,0);
			$form_mode=FALSE;
			if ($this->lang->line('admin_location_and_fare_add_new_location') != '') {
				$heading= stripslashes($this->lang->line('admin_location_and_fare_add_new_location')); 
			}else{
				$heading = 'Add New Location';
			}
			if($location_id!=''){
				$condition = array('_id' => MongoID($location_id));
				$this->data['locationdetails'] = $this->location_model->get_all_details(LOCATIONS,$condition);
				if ($this->data['locationdetails']->num_rows() != 1){
					redirect(ADMIN_ENC_URL.'/location/display_location_list');
				}
				$form_mode=TRUE;
				
				if ($this->lang->line('admin_location_edit_location') != '') {
					$heading= stripslashes($this->lang->line('admin_location_edit_location')); 
				}else{
					$heading = 'Edit Location';
				}
			}
			$this->data['categoryList'] = $this->location_model->get_all_details(CATEGORY,array('status' => 'Active'),array('name'=>'ASC'));
			$this->data['form_mode'] = $form_mode;
			
			$this->data['heading'] = $heading;
			$this->load->view(ADMIN_ENC_URL.'/location/add_edit_location',$this->data);
		}
	}
	/**
	* 
	* its copy the location detail
	*
	* @param string $location_id  Location MongoDB\BSON\ObjectId
	* @return HTML,copy location page
	*
	**/
	public function copy_location(){
		if ($this->checkLogin('A') == ''){
			redirect(ADMIN_ENC_URL);
		}else {
			$location_id = $this->uri->segment(4,0);
			$form_mode=FALSE;
			if ($this->lang->line('admin_location_and_fare_add_new_location') != '') 
		    $this->data['heading']= stripslashes($this->lang->line('admin_location_and_fare_add_new_location')); 
		    else  $this->data['heading'] = 'Add New Location';
			if($location_id!=''){
				$condition = array('_id' => MongoID($location_id));
				$this->data['locationdetails'] = $this->location_model->get_all_details(LOCATIONS,$condition);
				if ($this->data['locationdetails']->num_rows() != 1){
					redirect(ADMIN_ENC_URL.'/location/display_location_list');
				}
				$form_mode=TRUE;
				if ($this->lang->line('admin_location_edit_location') != '') {
					$heading= stripslashes($this->lang->line('admin_location_edit_location')); 
				}else{
					$heading = 'Edit Location';
				}
			}
			$this->data['categoryList'] = $this->location_model->get_all_details(CATEGORY,array('status' => 'Active'),array('name'=>'ASC'));
			$this->data['form_mode'] = $form_mode;
			
			$this->data['heading'] = $heading;
			$this->load->view(ADMIN_ENC_URL.'/location/copy_location',$this->data);
		}
	}
	/**
	* 
	* its insert or edit location 
	*
	* @param string $location_id  Location MongoDB\BSON\ObjectId
	* @param string $city  City
	* @param string $location_string  Location address
	* @param string $available_category  available category in comma separator
	* @return HTTP REDIRECT,geo location boundary page
	*
	**/
	public function insertEditLocation(){
		if ($this->checkLogin('A') == ''){
			redirect(ADMIN_ENC_URL);
		}else {       
			$location_id = $this->input->post('location_id');			
			$city = $this->input->post('city');
            $location_string = $this->input->post('location_string');
			$category = @explode(",",$this->input->post('available_category'));
			if($location_string != ''){
                $address = str_replace(" ", "+", $location_string);
            } else {
                $address = str_replace(" ", "+", $city);
                $location_string = $city;
            }
            
            $json = file_get_contents("https://maps.google.com/maps/api/geocode/json?address=$address&sensor=false".$this->data['google_maps_api_key']);
			
			$jsonArr = json_decode($json);
			$newAddress = $jsonArr->{'results'}[0]->{'address_components'};
			
		
			foreach($newAddress as $nA){
				if($nA->{'types'}[0] == 'route')$addressArr['street'] = $nA->{'long_name'};
				if($nA->{'types'}[0] == 'sublocality_level_2')$addressArr['street1'] = $nA->{'long_name'};
				if($nA->{'types'}[0] == 'sublocality_level_1')$addressArr['area'] = $nA->{'long_name'};
				if($nA->{'types'}[0] == 'locality')$addressArr['location'] = $nA->{'long_name'};
				if($nA->{'types'}[0] == 'administrative_area_level_2')$addressArr['city'] = $nA->{'long_name'};
               if(!isset($addressArr['city']) || (isset($addressArr['city']) && $addressArr['city'] == '')){
					if($nA->{'types'}[0] == 'colloquial_area')$addressArr['city'] = $nA->{'long_name'};
				}
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
            
            if ($location_id != ''){
                $condition = array('_id' => MongoID($location_id));
                $city_check = $this->location_model->get_all_details(LOCATIONS,$condition);
                $location_lng=$city_check->row()->location['lng'];
                $location_lat=$city_check->row()->location['lat'];
                $location_city=$city_check->row()->city;
                if($location_city!=$addressArr['city']) {
                  $field = array('loc');
                  $this->mongo_db->where($condition)->unset_field($field)->update(LOCATIONS);
                }
            }
            
			$condition = array('cca2' => (string)$addressArr['country_code']);
			$countryList = $this->user_model->get_all_details(COUNTRY,$condition);
			
			
			if($countryList->num_rows()>0){				
				$country_name=$addressArr['country'];
				$country_code=$addressArr['country_code'];
				
				$country_id=(string)$countryList->row()->_id;
				$country_currency=$this->data['dcurrencyCode'];
			}else{
				$this->setErrorMessage('error','Unknown country, Please try again','admin_location_unknown_country');
				redirect(ADMIN_ENC_URL.'/location/add_edit_location/'.$location_id);
			}
			
			
			$lat = $jsonArr->{'results'}[0]->{'geometry'}->{'location'}->{'lat'};
			$lang = $jsonArr->{'results'}[0]->{'geometry'}->{'location'}->{'lng'};
			$northeast_lat = $jsonArr->{'results'}[0]->{'geometry'}->{'bounds'}->{'northeast'}->{'lat'};
			$northeast_lng = $jsonArr->{'results'}[0]->{'geometry'}->{'bounds'}->{'northeast'}->{'lng'};
			$southwest_lat = $jsonArr->{'results'}[0]->{'geometry'}->{'bounds'}->{'southwest'}->{'lat'};
			$southwest_lng = $jsonArr->{'results'}[0]->{'geometry'}->{'bounds'}->{'southwest'}->{'lng'};
			
			$location=array('lng'=>floatval($lang),'lat'=>floatval($lat));
			$bounds=array('southwest'=>array('lng'=>floatval($southwest_lng),'lat'=>floatval($southwest_lat)),'northeast'=>array('lng'=>floatval($northeast_lng),'lat'=>floatval($northeast_lat)));
			
			$cityName = $addressArr['city'];
			if ($location_id == ''){
				$condition = array('city' => $cityName);
			} else {
				$condition = array('_id' => array('$ne' => MongoID($location_id)),'city' => $cityName);
			} 
			$duplicate_name = $this->location_model->get_all_details(LOCATIONS,$condition);
			if ($duplicate_name->num_rows() > 0){
				$this->setErrorMessage('error','Location already exists','admin_location_already_exist');
				redirect(ADMIN_ENC_URL.'/location/add_edit_location/'.$location_id);
			}
			$excludeArr = array("location_id","status","country","city","peak_time","night_charge","category","available_category","share_pooling","pool_map_search_radius","passenger","co_passenger","pool_category","location_string");
			
			
			if ($this->input->post('status') == 'on'){
				$location_status = 'Active';
			}else{
				$location_status = 'Inactive';
			}
			if ($this->input->post('peak_time') == 'on'){
				$peak_time_status = 'Yes';
			}else{
				$peak_time_status = 'No';
			}
			if ($this->input->post('night_charge') == 'on'){
				$night_charge_status = 'Yes';
			}else{
				$night_charge_status = 'No';
			}
			$avail_category=$category;
			$country=array('id'=>MongoID($country_id),'name'=>$country_name,'code'=>$country_code);
			
			if ($this->input->post('share_pooling') == 'on'){
				$location_share_pooling = 'Enable';
			}else{
				$location_share_pooling = 'Disable';
			}
			$pool_map_search_radius = "";
			$pool_categories = array();
			$pool_fare = array();
			
			if($location_share_pooling == 'Enable'){
				$pool_categories = $this->input->post("pool_category");
				$pool_categories = array_values(array_unique(array_filter($pool_categories)));
				if(!empty($pool_categories)){
					$pool_map_search_radius = $this->input->post("pool_map_search_radius");
					$passenger = $this->input->post("passenger");
					$co_passenger = $this->input->post("co_passenger");
					$pool_fare = array("passenger"=>(string)$passenger,"co_passenger"=>(string)$co_passenger);
				}
			}
			if(empty($pool_categories)){
				$location_share_pooling = 'Disable';
			}
			
			
			$location_data = array('country' => $country,
                                    'city' => $cityName,
                                    'location' => $location,
                                    'bounds' => $bounds,
                                    'currency' => $country_currency,
                                    'avail_category' => $avail_category,
                                    'peak_time' => $peak_time_status,
                                    'night_charge' => $night_charge_status,
                                    'status' => $location_status,
                                    'share_pooling' => $location_share_pooling,
                                    'pool_categories' => $pool_categories,
                                    'pool_map_search_radius' => (string)$pool_map_search_radius,
                                    'pool_fare' => $pool_fare,
                                    'location_string' => (string)$location_string
                                );
												
			
			$condition = array();
			if ($location_id == ''){
				$this->location_model->commonInsertUpdate(LOCATIONS,'insert',$excludeArr,$location_data,$condition);
				$location_id = $this->mongo_db->insert_id();
				$this->setErrorMessage('success','Location added successfully','admin_location_added_success');
			}else {
				$condition = array('_id' => MongoID($location_id));
				$this->location_model->commonInsertUpdate(LOCATIONS,'update',$excludeArr,$location_data,$condition);
				$this->setErrorMessage('success','Location updated successfully','admin_location_updated_success');
			}
			redirect(ADMIN_ENC_URL.'/location/update_location_geo_points/'.$location_id);
		}
	}
	/**
	* 
	* its copy the location
	*
	* @param string $location_id  Location MongoDB\BSON\ObjectId
	* @param string $copy_location_id  City MongoDB\BSON\ObjectId
	* @param string $city  City
	* @param string $category  category
	* @return HTTP REDIRECT,geo location boundary page
	*
	**/
	public function insertCopyLocation(){
		if ($this->checkLogin('A') == ''){
			redirect(ADMIN_ENC_URL);
		}else {
			$location_id = $this->input->post('location_id');			
			$copy_location_id = $this->input->post('copy_location_id');			
			$city = $this->input->post('city');
			$category = $this->input->post('category');
			$address = str_replace(" ", "+", $city);
			$google_map_api='AIzaSyC5YIg8-Yk_zqjzWpFyZrgYuzzjTCBJV7k';
			$json = file_get_contents("https://maps.google.com/maps/api/geocode/json?address=$address&sensor=false".$this->data['google_maps_api_key']);
			$jsonArr = json_decode($json);
			$newAddress = $jsonArr->{'results'}[0]->{'address_components'};
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
			$condition = array('cca2' => (string)$addressArr['country_code']);
			$countryList = $this->user_model->get_all_details(COUNTRY,$condition);
			if($countryList->num_rows()>0){				
				$country_name=$addressArr['country'];
				$country_code=$addressArr['country_code'];
				$country_id=(string)$countryList->row()->_id;
				$country_currency=$this->data['dcurrencyCode'];
			}else{
				$this->setErrorMessage('error','Unknown country, Please try again','admin_location_unknown_country');
				redirect(ADMIN_ENC_URL.'/location/add_edit_location/'.$location_id);
			}
			
			
			$lat = $jsonArr->{'results'}[0]->{'geometry'}->{'location'}->{'lat'};
			$lang = $jsonArr->{'results'}[0]->{'geometry'}->{'location'}->{'lng'};
			$northeast_lat = $jsonArr->{'results'}[0]->{'geometry'}->{'bounds'}->{'northeast'}->{'lat'};
			$northeast_lng = $jsonArr->{'results'}[0]->{'geometry'}->{'bounds'}->{'northeast'}->{'lng'};
			$southwest_lat = $jsonArr->{'results'}[0]->{'geometry'}->{'bounds'}->{'southwest'}->{'lat'};
			$southwest_lng = $jsonArr->{'results'}[0]->{'geometry'}->{'bounds'}->{'southwest'}->{'lng'};
			$location=array('lng'=>floatval($lang),'lat'=>floatval($lat));
			$bounds=array('southwest'=>array('lng'=>floatval($southwest_lng),'lat'=>floatval($southwest_lat)),'northeast'=>array('lng'=>floatval($northeast_lng),'lat'=>floatval($northeast_lat)));
					
			if ($location_id == ''){
				$condition = array('location' => $location);
				$duplicate_name = $this->location_model->get_all_details(LOCATIONS,$condition);
				if ($duplicate_name->num_rows() > 0){
					$this->setErrorMessage('error','Location already exists','admin_location_already_exist');
					redirect(ADMIN_ENC_URL.'/location/add_edit_location/'.$location_id);
				}
			}
			$excludeArr = array("location_id","status","country","city","peak_time","night_charge","category");
			
			if ($this->input->post('status') == 'on'){
				$location_status = 'Active';
			}else{
				$location_status = 'Inactive';
			}
			if ($this->input->post('peak_time') == 'on'){
				$peak_time_status = 'Yes';
			}else{
				$peak_time_status = 'No';
			}
			if ($this->input->post('night_charge') == 'on'){
				$night_charge_status = 'Yes';
			}else{
				$night_charge_status = 'No';
			}
			$avail_category=$category;
			$fare = array();
			$source_location = $this->location_model->get_all_details(LOCATIONS,array('_id'=>MongoID($copy_location_id)));
			if($source_location->num_rows()>0){
				$source_fare = $source_location->row()->fare;
				foreach($avail_category as $key){
					if(array_key_exists($key,$source_fare)){
						$fare[$key] = $source_fare[$key];
					}else{
						$fare[$key] = array();
					}
				}
			}
			$country=array('id'=>MongoID($country_id),'name'=>$country_name,'code'=>$country_code);
			$location_data = array('country' => $country,
													'city' => $addressArr['city'],
													'location' => $location,
													'bounds' => $bounds,
													'currency' => $country_currency,
													'avail_category' => $avail_category,
													'peak_time' => $peak_time_status,
													'night_charge' => $night_charge_status,
													'status' => $location_status,
													'fare'=>$fare
												);
			$condition = array();
			if ($location_id == ''){
				$this->location_model->commonInsertUpdate(LOCATIONS,'insert',$excludeArr,$location_data,$condition);
				$location_id = $this->mongo_db->insert_id();
				$this->setErrorMessage('success','Location added successfully','admin_location_added_success');
			}else {
				$condition = array('_id' => MongoID($location_id));
				$this->location_model->commonInsertUpdate(LOCATIONS,'update',$excludeArr,$location_data,$condition);
				$this->setErrorMessage('success','Location updated successfully','admin_location_updated_success');
			}
			redirect(ADMIN_ENC_URL.'/location/update_location_geo_points/'.$location_id);
		}
	}
	/**
	* 
	* its view the location details
	*
	* @param string $location_id  Location MongoDB\BSON\ObjectId
	* @return HTML,location details page
	*
	**/
	public function view_location(){
		if ($this->checkLogin('A') == ''){
			redirect(ADMIN_ENC_URL);
		}else {
		    if ($this->lang->line('admin_location_and_fare_view_location') != '') 
		    $this->data['heading']= stripslashes($this->lang->line('admin_location_and_fare_view_location')); 
		    else  $this->data['heading'] = 'View Location';
			$location_id = $this->uri->segment(4,0);
			$condition = array('_id' => MongoID($location_id));
			$this->data['location_details'] = $location_details=$this->location_model->get_all_details(LOCATIONS,$condition);
			if ($this->data['location_details']->num_rows() == 1){
				$avail_category=$location_details->row()->avail_category;
				if(!is_array($avail_category)){
					$avail_category=array();
				}
				$availableCategory =  $this->location_model->get_available_services(CATEGORY,'_id',$avail_category);
				$categoryArr=array();
				$langCode = $this->data['langCode'];
				if($availableCategory->num_rows()>0){
					foreach($availableCategory->result() as $category){
					
						$category_name = $category->name;
						if(isset($category->name_languages[$langCode ]) && $category->name_languages[$langCode ] != '') $category_name = $category->name_languages[$langCode ];
					
						$categoryArr[(string)$category->_id]=$category_name;
					}
				}
				$this->data['availableCategory']=$categoryArr;
				$this->load->view(ADMIN_ENC_URL.'/location/view_location',$this->data);
			}else {
				redirect(ADMIN_ENC_URL);
			}
		}
	}
	/**
	* 
	* its change the location status 
	*
	* @param string $location_id  Location MongoDB\BSON\ObjectId
	* @param string $mode  active/inactive
	* @return HTTP REDIRECT,location list page 
	*
	**/
	public function change_location_status(){
		if ($this->checkLogin('A') == ''){
			redirect(ADMIN_ENC_URL);
		}else {
			$mode = $this->uri->segment(4,0);
			$location_id = $this->uri->segment(5,0);
			$status = ($mode == '0')?'Inactive':'Active';
			$newdata = array('status' => $status);
			$condition = array('_id' => MongoID($location_id));
			$this->location_model->update_details(LOCATIONS,$newdata,$condition);
			$this->setErrorMessage('success','Location Status Changed Successfully','admin_location_status_change');
			redirect(ADMIN_ENC_URL.'/location/display_location_list');
		}
	}
	/**
	* 
	* its delete the location details
	*
	* @param string $location_id  Location MongoDB\BSON\ObjectId
	* @return HTTP REDIRECT,location list page 
	*
	**/
	public function delete_location(){
		if ($this->checkLogin('A') == ''){
			redirect(ADMIN_ENC_URL);
		}else {
			$location_id = $this->uri->segment(4,0);
			$condition = array('_id' => MongoID($location_id));
			$this->location_model->commonDelete(LOCATIONS,$condition);
			$this->setErrorMessage('success','Location deleted successfully','admin_location_deleted_success');
			redirect(ADMIN_ENC_URL.'/location/display_location_list');
		}
	}
	/**
	* 
	* its change the location status bulk 
	*
	* @param string $checkbox_id  Location Id MongoDB\BSON\ObjectId ARRAY[]
	* @param string $statusMode  Active/Inactive
	* @return HTTP REDIRECT,location list page 
	*
	**/
	public function change_location_status_global(){
		if(count($_POST['checkbox_id']) > 0 &&  $_POST['statusMode'] != ''){
			$this->user_model->activeInactiveCommon(LOCATIONS,'_id');
			if (strtolower($_POST['statusMode']) == 'delete'){
				$this->setErrorMessage('success','Locations deleted successfully','admin_location_deleted_success');
			}else {
				$this->setErrorMessage('success','Locations status changed successfully','admin_location_status_change');
			}
			redirect(ADMIN_ENC_URL.'/location/display_location_list');
		}
	}
	/**
	* 
	* its displays the location fare page
	*
	* @param string $location_id  Location Id MongoDB\BSON\ObjectId
	* @return HTML,location fare page  
	*
	**/
	public function location_fare(){
		if ($this->checkLogin('A') == ''){
			redirect(ADMIN_ENC_URL);
		}else {
			$location_id = $this->uri->segment(4,0);
			$form_mode=FALSE;
			$categoryArr=array();
			if($location_id!=''){
				$condition = array('_id' => MongoID($location_id));
				$this->data['locationdetails'] = $this->location_model->get_all_details(LOCATIONS,$condition);
				$this->data['categorydetails'] = $this->location_model->get_all_details(CATEGORY,array());
				if ($this->data['locationdetails']->num_rows() != 1){
					redirect(ADMIN_ENC_URL.'/location/display_location_list');
				}
				$form_mode=TRUE;				
				if(isset($this->data['locationdetails']->row()->avail_category)){
					$categoryArr=$this->data['locationdetails']->row()->avail_category;
				}else{
					$categoryArr='';
				}
				if(!is_array($categoryArr))$categoryArr=array();
			}			
			$this->data['availableCategory'] = $categoryArr;
			$this->data['form_mode'] = $form_mode;
			$this->data['locationId'] = $location_id;
            if ($this->lang->line('admin_rides_fare_details') != '') 
		    $title= stripslashes($this->lang->line('admin_rides_fare_details')); 
		    else  $title = 'Fare Details';
			$this->data['heading'] = $this->data['locationdetails']->row()->city.'-'.$title;
			$this->load->view(ADMIN_ENC_URL.'/location/add_edit_fare',$this->data);
		}
	}
	/**
	* 
	* its update the fare details
	*
	* @param string $location_id  Location Id MongoDB\BSON\ObjectId
	* @param string $fare $_POST[] array 
	* @return HTTP REDIRECT,location fare page  
	*
	**/
	public function insertEditFare(){
		if ($this->checkLogin('A') == ''){
			redirect(ADMIN_ENC_URL);
		}else {
			$fare=$this->input->post();
			unset($fare['apply']);
			unset($fare['location_id']);
			$fareArr=array('fare'=>$fare);
			$location_id=$this->input->post('location_id');
			$condition=array('_id'=>MongoID($location_id));
			if($location_id!=''){
				$this->location_model->update_details(LOCATIONS,$fareArr,$condition);
				$this->setErrorMessage('success','Fare System updated successfully','admin_location_fare_system_update');
			}else{
				$this->setErrorMessage('error','Fare System updation failed. Please try again.','admin_location_fare_system_update_failed');
			}
			redirect(ADMIN_ENC_URL.'/location/location_fare/'.$location_id);
		}
	}
	/**
	* 
	* its displays country list
	*
	* @return HTML,country list page 
	*
	**/
	public function display_country_list(){
		if ($this->checkLogin('A') == ''){
			redirect(ADMIN_ENC_URL);
		}else {
		    if ($this->lang->line('admin_location_and_fare_country_list') != '') 
		    $this->data['heading']= stripslashes($this->lang->line('admin_location_and_fare_country_list')); 
		    else  $this->data['heading'] = 'Country List';
			$condition = array();
			$sortArr = array('name'=>'ASC');
			$this->data['countryList'] = $this->location_model->get_all_details(COUNTRY,$condition);
			$this->load->view(ADMIN_ENC_URL.'/location/display_country',$this->data);
		}
	}
	/**
	* 
	* its add or edits country page
	*
	* @param string $country_id  Country Id MongoDB\BSON\ObjectId
	* @return HTML,add or edit country page 
	*
	**/
	public function add_edit_country(){
		if ($this->checkLogin('A') == ''){
			redirect(ADMIN_ENC_URL);
		}else {
			$country_id = $this->uri->segment(4,0);
			$form_mode=FALSE;
			if ($this->lang->line('admin_location_and_fare_add_new_country') != '') 
		    $heading= stripslashes($this->lang->line('admin_location_and_fare_add_new_country')); 
		    else  $heading = 'Add New Country';
						
			if($country_id!=''){
				$condition = array('_id' => MongoID($country_id));
				$this->data['countrydetails'] = $this->location_model->get_all_details(COUNTRY,$condition);
				if ($this->data['countrydetails']->num_rows() != 1){
					redirect(ADMIN_ENC_URL.'/location/display_country');
				}
				$form_mode=TRUE;
				if ($this->lang->line('admin_location_and_fare_edit_country') != '') 
		        $heading= stripslashes($this->lang->line('admin_location_and_fare_edit_country')); 
		        else  $heading = 'Edit Country';
				
			}
			$condition = array('status'=>'Active');
			$sortArr = array('name'=>'ASC');
			$this->data['currencyList'] = $this->location_model->get_all_details(CURRENCY,$condition);
			$this->data['form_mode'] = $form_mode;
			$this->data['heading'] = $heading;
			$this->load->view(ADMIN_ENC_URL.'/location/add_edit_country',$this->data);
		}
	}
	/**
	* 
	* its insert or edits country page
	*
	* @param string $country_id  Country Id MongoDB\BSON\ObjectId
	* @param string $cca2  CCA2 2 digit country code
	* @param string $cca3  CCA3 3 digit country code
	* @param string $dial_code  Country code
	* @param string $name  Name of the country
	* @return HTTP REDIRECT,display country list page
	*
	**/
	public function insertEditCountry(){
		if ($this->checkLogin('A') == ''){
			redirect(ADMIN_ENC_URL);
		}else {
			$country_id = $this->input->post('country_id');
			$cca2 = $this->input->post('cca2');
			$cca3 = $this->input->post('cca3');
			$dial_code = $this->input->post('dial_code');
			$name = $this->input->post('name');
			if($country_id==''){
				$condition=array('name'=>$name,'cca2'=>$cca2,'cca3'=>$cca3,'dial_code'=>$dial_code);
				$primary_condition=array();
			}else{
				$condition=array('name'=>$name,'cca2'=>$cca2,'cca3'=>$cca3,'dial_code'=>$dial_code);
				$primary_condition=array();
			}
			$countryList = $this->location_model->chk_country_exist(COUNTRY,$condition,$primary_condition);
			$duplicateCountry=array();
			if($countryList->num_rows()>0){
				foreach($countryList->result() as $cnty){
					$duplicateCountry[]=(string)$cnty->_id;
				}
			}
			$isDuplicate=FALSE;
			if(!empty($duplicateCountry)){
				if(($key = array_search($country_id, $duplicateCountry)) !== false) {
					unset($duplicateCountry[$key]);
				}
				if(!empty($duplicateCountry)){
					$isDuplicate=TRUE;
				}
			}
			if($isDuplicate){
				$this->setErrorMessage('error','Country informations are already exist, Please try again','admin_location_country_inforamtion_exit');
				redirect(ADMIN_ENC_URL.'/location/add_edit_country/'.$country_id);
			}
			$excludeArr = array("country_id","status","currency","dial_code");
			
			if ($this->input->post('status') == 'on'){
				$currency_status = 'Active';
			}else{
				$currency_status = 'Inactive';
			}
			$currency_data = array('status' => $currency_status,'dial_code' => (string)$dial_code);
			
			$currency_code=$this->input->post('currency');
			$currencyList = $this->location_model->get_all_details(CURRENCY,array('code'=>$currency_code));
			
			if($currencyList->num_rows()>0){
				$currency_data['currency_code']=$currencyList->row()->code;
				$currency_data['currency_symbol']=$currencyList->row()->symbol;
				$currency_data['currency_name']=$currencyList->row()->name;
			}
			
			$condition = array();
			if ($country_id == ''){
				$this->location_model->commonInsertUpdate(COUNTRY,'insert',$excludeArr,$currency_data,$condition);
				$this->setErrorMessage('success','Country added successfully','admin_location_country_added_success');
			}else {
				$condition = array('_id' => MongoID($country_id));
				$this->location_model->commonInsertUpdate(COUNTRY,'update',$excludeArr,$currency_data,$condition);
				$this->setErrorMessage('success','Country updated successfully','admin_location_country_updated_success');
			}
			redirect(ADMIN_ENC_URL.'/location/display_country_list');
		}
	}
	/**
	* 
	* its view the country Detail information
	*
	* @param string $country_id  Country Id MongoDB\BSON\ObjectId
	* @return HTML,view country detail information page
	*
	**/
	public function view_country(){
		if ($this->checkLogin('A') == ''){
			redirect(ADMIN_ENC_URL);
		}else {
			if ($this->lang->line('admin_location_view_country') != '') 
			$heading= stripslashes($this->lang->line('admin_location_view_country')); 
			else  $heading = 'View Country';
			$this->data['heading'] = $heading;
			$country_id = $this->uri->segment(4,0);
			$condition = array('_id' => MongoID($country_id));
			$this->data['countrydetails'] = $countrydetails=$this->location_model->get_all_details(COUNTRY,$condition);
			if ($this->data['countrydetails']->num_rows() == 1){
				$this->load->view(ADMIN_ENC_URL.'/location/view_country',$this->data);
			}else {
				redirect(ADMIN_ENC_URL);
			}
		}
	}		
	/**
	* 
	* its delete the country details
	*
	* @param string $country_id  Country Id MongoDB\BSON\ObjectId
	* @return HTTP REDIRECT,display country information page
	*
	**/
	public function delete_country(){
		if ($this->checkLogin('A') == ''){
			redirect(ADMIN_ENC_URL);
		}else {
			$this->setErrorMessage('error','This service is not available','admin_location_service_not_available');
			redirect(ADMIN_ENC_URL.'/location/display_country_list');
			$country_id = $this->uri->segment(4,0);
			$condition = array('_id' => MongoID($country_id));
			$this->location_model->commonDelete(COUNTRY,$condition);
			$this->setErrorMessage('success','Country deleted successfully');
			redirect(ADMIN_ENC_URL.'/location/display_country_list'); 
		}
	}
	/**
	* 
	* its change country status information
	*
	* @param string $country_id  Country Id MongoDB\BSON\ObjectId
	* @param string $mode  Active or Inactive status
	* @return HTTP REDIRECT,display country information page
	*
	**/
	public function change_country_status(){
		if ($this->checkLogin('A') == ''){
			redirect(ADMIN_ENC_URL);
		}else {
			$mode = $this->uri->segment(4,0);
			$country_id = $this->uri->segment(5,0);
			$status = ($mode == '0')?'Inactive':'Active';
			$newdata = array('status' => $status);
			$condition = array('_id' => MongoID($country_id));
			$this->location_model->update_details(COUNTRY,$newdata,$condition);
			$this->setErrorMessage('success','Country Status Changed Successfully','admin_location_country_status_change');
			redirect(ADMIN_ENC_URL.'/location/display_country_list');
		}
	}
	/**
	* 
	* its change country status bulk 
	*
	* @param string $checkbox_id  Country Id MongoDB\BSON\ObjectId
	* @param string $statusMode  Active or Inactive status
	* @return HTTP REDIRECT,display country information page
	*
	**/
	public function change_country_status_global(){
		if(count($_POST['checkbox_id']) > 0 &&  $_POST['statusMode'] != ''){
			if (strtolower($_POST['statusMode']) == 'delete'){
				$this->setErrorMessage('error','This service is not available','admin_location_service_not_available');
				redirect(ADMIN_ENC_URL.'/location/display_country_list');		
			}
			$this->location_model->activeInactiveCommon(COUNTRY,'_id');
			if (strtolower($_POST['statusMode']) == 'delete'){
				$this->setErrorMessage('success','Country records deleted successfully','admin_location_country_records_deleted');
			}else {
				$this->setErrorMessage('success','Country records status changed successfully','admin_location_country_records_status_change');
			}
			redirect(ADMIN_ENC_URL.'/location/display_country_list');
		}
	}
	/**
	* 
	* its display location boundary page
	*
	* @param string $location_id  Location Id MongoDB\BSON\ObjectId
	* @return HTML,display location boundary page 
	*
	**/
	public function update_location_geo_points(){ 
		if ($this->checkLogin('A') == ''){
			redirect(ADMIN_ENC_URL);
		}else {
			$location_id = $this->uri->segment(4,0);
			$form_mode=TRUE;
			
		    if ($this->lang->line('admin_location_update_bounday_points') != '') 
		    $heading= stripslashes($this->lang->line('admin_location_update_bounday_points')); 
		    else  $heading = 'Update Map Boundary Points';
            
            
            $condition = array('_id' => array('$ne' => MongoID($location_id)));
            $locations_list = $this->location_model->get_all_details(LOCATIONS,$condition);
            
            $other_loc_lists = array();
            foreach($locations_list->result() as $location){
                if(isset($location->loc['coordinates'][0])){
                    $locLatLngs = $location->loc['coordinates'][0];
                    $coordsArr = array();
                    foreach($locLatLngs as $latlngs){
                        if(isset($latlngs[0]) && isset($latlngs[1])){
                            $coordsArr[] = array('lat' => $latlngs[1],'lng' => $latlngs[0]);
                        }
                    }
                    if(!empty($coordsArr)){
                        $other_loc_lists[] = array('coordinates' => $coordsArr,
                                                'location_name' => $location->city,
                                                'location_id' => (string)$location->_id,
                                                'status' => $location->status
                                          );
                    }
                }
            }
            
            #echo '<pre>'; print_r($other_loc_lists); die;
            $this->data['other_loc_lists'] = $other_loc_lists;
            
			
			if($location_id!=''){
				$condition = array('_id' => MongoID($location_id));
				$this->data['locationdetails'] = $this->location_model->get_all_details(LOCATIONS,$condition);
				if ($this->data['locationdetails']->num_rows() != 1){
					redirect(ADMIN_ENC_URL.'/location/display_location_list');
				}
			}
			$this->data['form_mode'] = $form_mode;
			$this->data['heading'] = $heading;
			
			$this->load->view(ADMIN_ENC_URL.'/location/update_location_geo_points',$this->data);
		}
	}
	/**
	* 
	* its updates the geo location boundary
	*
	* @param string $location_id  Location Id MongoDB\BSON\ObjectId
	* @param string $boundayVal  Boundary $_POST[] array
	* @return HTTP REDIRECT,location fare page
	*
	**/
	public function updateLocationBoundary(){
		if ($this->checkLogin('A') == ''){
			redirect(ADMIN_ENC_URL);
		}else {
			$location_id = $this->input->post('location_id');			
			$boundayVal = $this->input->post('boundayVal');
			
			$boundayVal = trim($boundayVal,'(');
			$boundayVal = trim($boundayVal,')');
			$bArr = @explode('),(',$boundayVal);
			$bcArr= array();
			foreach($bArr as $points){
				$bcArrTemp = @explode(', ',$points);
				$bcArr[] = array(floatval($bcArrTemp[1]),floatval($bcArrTemp[0]));
			}
			if(!empty($bcArr)){
				$bcArr[] = $bcArr[0];
			}
			
			$boundarydata = array('loc'=>array("type"=>"Polygon",'coordinates'=>array($bcArr)));
			$condition = array('_id' => MongoID($location_id));
			$this->location_model->update_details(LOCATIONS,$boundarydata,$condition);
			$this->setErrorMessage('success','Boundary updated Successfully','admin_location_boundary_updated');
		    redirect(ADMIN_ENC_URL.'/location/location_fare/'.$location_id);
		}
	}
    
    /**
	*
	* This function will show the locations polygons in single view
	*
	**/
    function gods_view(){
        if ($this->checkLogin('A') == ''){
			redirect(ADMIN_ENC_URL);
        } 
        
        $this->data['locationsList'] = $locationsList = $this->location_model->get_selected_fields(LOCATIONS,array(),array('loc','city','status'));
            
        $loc_coordinatesArr = array();
        foreach($locationsList->result() as $location){
            if(isset($location->loc['coordinates'][0])){
                $spLatLngs = $location->loc['coordinates'][0];
                $coordsArr = array();
                foreach($spLatLngs as $latlngs){
                    if(isset($latlngs[0]) && isset($latlngs[1])){
                        $coordsArr[] = array('lat' => $latlngs[1],'lng' => $latlngs[0]);
                    }
                }
                
                if(!empty($coordsArr)){
                    $loc_coordinatesArr[] = array('coordinates' => $coordsArr,
                                            'city_name' => $location->city,
                                            'status' => $location->status,
                                            'location_id' => (string)$location->_id
                                      );
                }
            }
        }
        $this->data['locationsCoordsList'] = $loc_coordinatesArr;
		$user_type=$this->input->get('user_type');
		
		if(isset($user_type) && $user_type!='') {
			$user_type_array=explode(',',$user_type);
			if(in_array('user',$user_type_array) && in_array('driver',$user_type_array)) {
				$usersList = $this->location_model->get_selected_fields(USERS,array(),array('loc','user_name','email','phone_number'));
				$driverList = $this->location_model->get_selected_fields(DRIVERS,array(),array('loc','driver_name','email'));
			} else if(in_array('user',$user_type_array)) {
				$usersList = $this->location_model->get_selected_fields(USERS,array(),array('loc','user_name','email','phone_number'));
			} else if(in_array('driver',$user_type_array)) {
				$driverList = $this->location_model->get_selected_fields(DRIVERS,array(),array('loc','driver_name','email'));
			}
		} else {
			$usersList = $this->location_model->get_selected_fields(USERS,array(),array('loc','user_name','email','phone_number'));
			$driverList = $this->location_model->get_selected_fields(DRIVERS,array(),array('loc','driver_name','email'));
		}
		$this->data['driverList'] = $driverList;
		$this->data['usersList'] = $usersList;
        if ($this->lang->line('admin_gods_view') != '') $this->data['heading'] =  stripslashes($this->lang->line('admin_gods_view')); else $this->data['heading'] = 'Locations god\'s view';
        
        $this->load->view(ADMIN_ENC_URL.'/location/location_gods_view.php',$this->data);
    }
    
    
}
/* End of file location.php */
/* Location: ./application/controllers/admin/location.php */