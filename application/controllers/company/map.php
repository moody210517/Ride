<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');
/**
*
*	Map
* 
*	@package		CI
*	@subpackage		Controller
*	@author			Casperon
*
**/
class Map extends MY_Controller {
	function __construct(){
        parent::__construct();
		$this->load->helper(array('cookie','date','form'));
		$this->load->library(array('encrypt','form_validation'));		
		$this->load->model(array('map_model'));
	    if ($this->checkLogin('C') != '') {
			$this->data['company_id'] = MongoID($this->session->userdata(APP_NAME.'_session_company_id'));
			$company_id = $this->checkLogin('C');
			$chkCompany = $this->app_model->get_selected_fields(COMPANY,array('_id' => MongoID($company_id)),array('status'));
			$chkstatus = TRUE;
			$errMsg = '';
			if($chkCompany->num_rows() == 1){
				if($chkCompany->row()->status == 'Inactive'){
					$chkstatus = FALSE;
						if ($this->lang->line('operator_inactive_message') != '') 
								$errMsg= stripslashes($this->lang->line('operator_inactive_message')); 
						else  $errMsg = 'Your account is temporarily deactivated, Please contact admin';
						
				}
			} else {
				$chkstatus = FALSE;
				if ($this->lang->line('account_not_found') != '') 
						$errMsg= stripslashes($this->lang->line('account_not_found')); 
				else  $errMsg = 'Your account details not found';
				
			}
			if(!$chkstatus){
				 $newdata = array(
					'last_logout_date' => date("Y-m-d H:i:s")
				);
				$collection = COMPANY;
				
				$condition = array('_id' => MongoID($this->checkLogin('C')));
				$this->app_model->update_details($collection, $newdata, $condition);
				$companydata = array(
							APP_NAME.'_session_company_id' => '',
							APP_NAME.'_session_company_name' => '',
							APP_NAME.'_session_company_email' => ''
							
						   
						);
						
				$this->session->unset_userdata($companydata);
				$this->setErrorMessage('error', $errMsg);
				redirect(COMPANY_NAME);
			}
			
		}
		
    }
    
   /**
    *
    * To redirect to available drivers on map page
    * 	
    * @Initiate HTML to available drivers on map page
    *	
    **/	
   	public function index(){
		if ($this->checkLogin('C') == ''){
			redirect(COMPANY_NAME);
		}else {
			redirect(COMPANY_NAME.'/map/map_avail_drivers');
		}
	}
    
   /**
    *
    * To load the available drivers on map
    *
    * @param string $address is location address
    * @Initiate HTML to available drivers on map page
    *	
    **/	
   	public function map_avail_drivers(){
		if ($this->checkLogin('C') == ''){
			redirect(COMPANY_NAME);
		}else{
			$center=$this->config->item('latitude').','.$this->config->item('longitude');
			$coordinates=array(floatval($this->config->item('longitude')),floatval($this->config->item('latitude')));
			$company_id=$this->data['company_id'];
			$condition=array('status'=>'Active');
			$category = $this->map_model->get_selected_fields(CATEGORY,$condition,array('name','image','name_languages'));
			$availCategory=array();
			$langCode = $this->data['langCode'];
			if($category->num_rows()>0){
				foreach($category->result() as $cat){
					$category_name = $cat->name;
					
					if(isset($cat->name_languages[$langCode ]) && $cat->name_languages[$langCode ] != '') $category_name = $cat->name_languages[$langCode ];
					$availCategory[(string)$cat->_id]=$category_name;
				}
			}
			
			$address=$this->input->get('location');
			if($address!=''){
				$address = str_replace(" ", "+", $address);
				$json = file_get_contents("https://maps.google.com/maps/api/geocode/json?address=$address&sensor=false".$this->data['google_maps_api_key']);
				$jsonArr = json_decode($json);
				$lat = $jsonArr->{'results'}[0]->{'geometry'}->{'location'}->{'lat'};
				$lang = $jsonArr->{'results'}[0]->{'geometry'}->{'location'}->{'lng'};
				$location=array($lat,$lang);
				$coordinates=array_reverse($location);
				$center=@implode($location,',');
			}
            if(!empty($coordinates) && $coordinates[0]!='') {
				$driverList = $this->map_model->get_nearest_driver_company($coordinates,$company_id); 
			} else {
               $this->setErrorMessage('error', 'No location Found','admin_no_location');
               redirect(COMPANY_NAME.'/map/map_avail_drivers');
            }
            
			$this->load->library('googlemaps');

			$config['center'] =$center;
			$config['zoom'] = '16';
			$config['minzoom'] = '3';
			$config['maxzoom'] = '24';
			$config['places'] = TRUE;
			$config['cluster'] = FALSE;
			$config['language'] = $this->data['langCode'];
			$config['placesAutocompleteInputID'] = 'location';
			$config['placesAutocompleteBoundsMap'] = TRUE;
			$this->googlemaps->initialize($config);
			$avail = 0;
			$unavail = 0;
			$onride = 0;
			if(!empty($driverList['result'])){
				foreach($driverList['result'] as $driver){
              
					$loc=array_reverse($driver['loc']);
					$latlong=@implode($loc,',');
					$marker = array();
					$marker['position'] = $latlong;
					$current=time()-300;
               
                   if(isset($driver['last_active_time'])) {
                    $last_active_time=MongoEPOCH($driver['last_active_time']);
                   }
                   if($driver['availability']=='Yes' && $driver['mode'] == 'Available' & isset($driver['last_active_time']) && $last_active_time > $current){
						$avail++;
						$marker['icon'] = base_url().'images/pin-available.png';
					} else if($driver['availability']=='Yes' && $driver['mode'] == 'Booked'){
						$onride++;
						$marker['icon'] = base_url().'images/pin-yellow.png';
					} else {
						$unavail++;
						$marker['icon'] = base_url().'images/pin-unavailable.png';
					}
					$marker['icon_scaledSize'] = '25,25';
					$catDis = "";
					if(array_key_exists((string)$driver['category'],$availCategory)){
						$catDis = $availCategory[(string)$driver['category']];
					}
					$marker['infowindow_content'] ="<div style='width:150px !important;height:50Px!important;'>".$driver['driver_name'].'<br/>'.$catDis."</div>";
					$this->googlemaps->add_marker($marker);
				}
			}
			$this->data['map'] = $this->googlemaps->create_map();
		    $this->data['online_drivers'] = $avail;
			$this->data['offline_drivers'] = $unavail;
			$this->data['onride_drivers'] = $onride;
			$this->data['address'] = urldecode($address);
            
            if ($this->lang->line('admin_menu_map_view') != '') 
		    $title= stripslashes($this->lang->line('admin_menu_map_view')); 
		    else  $title = 'Map View';
			$this->data['heading'] = $title;
			$this->load->view(COMPANY_NAME.'/map/availbale_drivers',$this->data);
		}
	}
    
    /**
	*
	* To load the available users on map
    *
	* @param string $address is location address
	* @Initiate HTML to available users on map page
    *	
    **/	
   	public function map_avail_users(){
		if ($this->checkLogin('A') == ''){
			redirect('admin');
		}else{
			$center=$this->config->item('latitude').','.$this->config->item('longitude');
			$coordinates=array(floatval($this->config->item('longitude')),floatval($this->config->item('latitude')));
			
			
			
			$address=$this->input->get('location');
			if($address!=''){
				$address = str_replace(" ", "+", $address);
				$json = file_get_contents("https://maps.google.com/maps/api/geocode/json?address=$address&sensor=false".$this->data['google_maps_api_key']);
				$jsonArr = json_decode($json);
				$lat = $jsonArr->{'results'}[0]->{'geometry'}->{'location'}->{'lat'};
				$lang = $jsonArr->{'results'}[0]->{'geometry'}->{'location'}->{'lng'};
				$location=array($lat,$lang);
				$coordinates=array_reverse($location);
				$center=@implode($location,',');
			}
		    if(!empty($coordinates) & $coordinates[0]!='') {
			$userList = $this->map_model->get_nearest_user($coordinates); 
			} else {
              $this->setErrorMessage('error', 'No location Found','admin_no_location');
              redirect('admin/map/map_avail_users');
            }
            
			$this->load->library('googlemaps');

			$config['center'] =$center;
			$config['zoom'] = '16';
			$config['minzoom'] = '3';
			$config['maxzoom'] = '24';
			$config['places'] = TRUE;
			$config['cluster'] = FALSE;
			$config['language'] = $this->data['langCode'];
			$config['placesAutocompleteInputID'] = 'location';
			$config['placesAutocompleteBoundsMap'] = TRUE;
			$this->googlemaps->initialize($config);
			
			if(!empty($userList['result'])){
				foreach($userList['result'] as $user){
					$loc=array_reverse($user['loc']);
					$latlong=@implode($loc,',');
					$marker = array();
					$marker['position'] = $latlong;
					$marker['icon'] = base_url().'images/user.png';
					$marker['icon_scaledSize'] = '25,25';
					$marker['infowindow_content'] ="<div style='width:200px !important;height:50Px!important;'>".$user['user_name'].'<br/>'.$user['email']."</div>";
					$this->googlemaps->add_marker($marker);
				}
			}
			$this->data['map'] = $this->googlemaps->create_map();
		
			$this->data['address'] = urldecode($address);
			if ($this->lang->line('admin_menu_map_view') != '') 
		    $title= stripslashes($this->lang->line('admin_menu_map_view')); 
		    else  $title = 'Map View';
			$this->data['heading'] = $title;
			$this->load->view('admin/map/availbale_users',$this->data);
		}
	}
	
}


/* End of file map.php */
/* Location: ./application/controllers/company/map.php */