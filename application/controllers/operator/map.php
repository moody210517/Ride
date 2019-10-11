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
		$this->load->model(array('app_model'));
		
		if($this->checkLogin('O') != ''){
			$operator_id = $this->checkLogin('O');
			$chkOperator = $this->app_model->get_selected_fields(OPERATORS,array('_id' => MongoID($operator_id)),array('status'));
			$chkstatus = TRUE;
			$errMsg = '';
			if($chkOperator->num_rows() == 1){
				if($chkOperator->row()->status == 'Inactive'){
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
				$collection = OPERATORS;
				
				$condition = array('_id' =>MongoID($this->checkLogin('O')));
				$this->app_model->update_details($collection, $newdata, $condition);
				$operatordata = array(
							APP_NAME.'_session_operator_id' => '',
							APP_NAME.'_session_operator_name' => '',
							APP_NAME.'_session_operator_email' => '',
							APP_NAME.'_session_vendor_location' =>''
						   
						);
				$this->session->unset_userdata($operatordata);
				$this->setErrorMessage('error', $errMsg);
				redirect(OPERATOR_NAME);
			}
		}
	
    }
    
   /**
    *
    * To redirect to available drivers on map page
    * 	
    * @return HTTP to redirect available drivers on map page
    *	
    **/	
   	public function index(){
				if ($this->checkLogin('O') == ''){
						redirect(OPERATOR_NAME);
				}else {
						redirect(OPERATOR_NAME.'/map/map_avail_drivers');
				}
	}
		
   /**
    *
    * To load the available drivers on map in particular location
    *
    * @param string $address is location address
    * @return HTML to show available drivers on map page
    *	
    **/	
   	public function map_avail_drivers(){
		
			if ($this->checkLogin('O') == ''){
				
					redirect(OPERATOR_NAME);
			}else{
				
					$operator_id = (string)$this->checkLogin('O');
					$operator_loc = (string)$this->session->userdata(APP_NAME.'_session_operator_location');
					$condition = array('_id' => MongoID($this->session->userdata(APP_NAME.'_session_operator_location')));
					
					$location = $this->map_model->get_selected_fields(LOCATIONS,$condition,array('_id','location'));
					

					$coordinates=array(floatval($location->row()->location['lng']),floatval($location->row()->location['lat']));
				
					$location=array($location->row()->location['lat'],$location->row()->location['lng']);
					$center=@implode($location,',');
				
					$condition=array('status'=>'Active');
					$category = $this->map_model->get_selected_fields(CATEGORY,$condition,array('name','image','name_languages'));
					$availCategory=array();
					$langCode = $this->data['langCode'];
					if($category->num_rows()>0){
							foreach($category->result() as $cat){
									$category_name = $cat->name;
									if(isset($cat->name_languages[$langCode]) && $cat->name_languages[$langCode] != '') $category_name = $cat->name_languages[$langCode];
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
							
					}
					if(!empty($coordinates) & $coordinates[0]!='') {												
						
						$driverList = $this->map_model->get_nearest_driver($coordinates,$operator_loc,''); 
					} else {
						 $this->setErrorMessage('error', 'No location Found','admin_no_location');
						 redirect(OPERATOR_NAME.'/dashboard/display_dashboard');
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
			$total_drivers = 0;
            $markerSData = array();
			if(!empty($driverList['result'])){
				foreach($driverList['result'] as $driver){
                 
                   if(isset($driver['loc'])) {
					$loc=array_reverse($driver['loc']);
					$latlong=@implode($loc,',');
					
					$marker['position'] = $latlong;
                    
                    
                    $current=time()-600;
                    $total_drivers++;
                    if(isset($driver['last_active_time'])) {
                        $last_active_time=MongoEPOCH($driver['last_active_time']);
                    }
                    if($driver['loc']['lat']!=0 && $driver['loc']['lon']!=0) {
                       if($driver['availability']=='Yes' && $driver['mode'] == 'Available' && isset($driver['last_active_time']) && $last_active_time > $current){
                                $avail++;
                                $marker['icon'] = base_url().'images/pin-available.png';
                                $marker_icon = base_url().'images/pin-available.png';
                            } else if($driver['availability']=='Yes' && $driver['mode'] == 'Booked'){
                                $onride++;
                                $marker['icon'] = base_url().'images/pin-yellow.png';
                                $marker_icon = base_url().'images/pin-yellow.png';
                            } else {
                                $unavail++;
                                $marker['icon'] = base_url().'images/pin-unavailable.png';
                                $marker_icon = base_url().'images/pin-unavailable.png';
                            }
                            $marker['icon_scaledSize'] = '25,25';
                            $catDis = "";
                            if(array_key_exists((string)$driver['category'],$availCategory)){
                                $catDis = $availCategory[(string)$driver['category']];
                            }
                            $marker['id']=(string)$driver['_id'];
                            $marker['infowindow_content'] ="<div style='width:150px !important;height:50Px!important;'>".htmlentities(trim($driver['driver_name'])).'<br/>'.htmlentities(trim($catDis))."</div>";
                            $infowindow_content="<div style='width:150px !important;height:50Px!important;'>".htmlentities(trim($driver['driver_name'])).'<br/>'.htmlentities(trim($catDis)).'<br>'.$driver['mobile_number']."</div>";
                            
                                $markerSData[]=array('lat'=>$driver['loc']['lat'],
                                                 'lon'=>$driver['loc']['lon'],
                                                 'icon'=>$marker_icon,
                                                 'icon_scaledSize'=>'25,25',
                                                 'id'=>(string)$driver['_id'],
                                                 'infowindow_content'=>$infowindow_content
                                                );
                        
                        $this->googlemaps->add_marker($marker);
                    }
                 }
			  }
			}
            $this->data['driverList'] = $driverList['result'];
            $this->data['marker'] = $markerSData;
            $this->data['center'] = $center;
			$this->data['mapContent'] = $this->googlemaps->create_map();
            $this->data['total_drivers'] = $total_drivers;
            $this->data['online_drivers'] = $avail;
			$this->data['offline_drivers'] = $unavail;
			$this->data['onride_drivers'] = $onride;
			$this->data['address'] = urldecode($address);
            $this->data['location_detail']=$this->app_model->get_all_details(LOCATIONS,array('status'=>'Active'));
			if ($this->lang->line('admin_menu_map_view') != '') 
		    $title= stripslashes($this->lang->line('admin_menu_map_view')); 
		    else  $title = 'Map View';
					$this->data['heading'] = $title;
					$this->load->view(OPERATOR_NAME.'/map/availbale_drivers',$this->data);
			}
	}
    
   /**
	*
	* To load the available users on map in particular location
    *
	* @param string $address is location address
	* @return HTML to shows available users on map page
    *	
    **/
   	public function map_avail_users(){
			if ($this->checkLogin('O') == ''){
					redirect(OPERATOR_NAME);
			}else{
					
					$condition = array('_id' => MongoID($this->session->userdata(APP_NAME.'_session_operator_location')));
				
					$location = $this->map_model->get_selected_fields(LOCATIONS,$condition,array('_id','location'));
				
					$coordinates=array(floatval($location->row()->location['lng']),floatval($location->row()->location['lat']));
				
					$location=array($location->row()->location['lat'],$location->row()->location['lng']);
					$center=@implode($location,',');
				
					$condition=array('status'=>'Active');
					$category = $this->map_model->get_selected_fields(CATEGORY,$condition,array('name','image'));
					$availCategory=array();
					if($category->num_rows()>0){
							foreach($category->result() as $cat){
									$availCategory[(string)$cat->_id]=$cat->name;
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
							
					}
					
					if(!empty($coordinates) & $coordinates[0]!='') {
							$userList = $this->map_model->get_nearest_user($coordinates); 
					} else {
							$this->setErrorMessage('error', 'No location Found','admin_no_location');
							redirect(OPERATOR_NAME . '/dashboard/display_dashboard');
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
					$this->load->view(OPERATOR_NAME . '/map/availbale_users',$this->data);
			}
	}
    
    
       public function map_online_driver() {
        #error_reporting(E_ALL);
        #ini_set('display_errors', 'on');
        #error_reporting(-1);
        $start_time = time();
        $end_time = $start_time-7200;
        $driver_id=$this->uri->segment(4,0);
        $drivers_info = $this->map_model->get_all_details(ONLINE_DRIVERS,array('updated_time'=>array('$lt'=>MongoDATE(time()),'$gte'=>MongoDATE($end_time)),'driver_id'=>MongoID($driver_id)));
        $this->data['drivers_info']=$drivers_info;
        #echo "<pre>";
        #print_r($drivers_info->result());
        #exit;
        $this->load->view(OPERATOR_NAME.'/map/online_tracking',$this->data);
    }
	
	
}


/* End of file map.php */
/* Location: ./application/controllers/operator/map.php */