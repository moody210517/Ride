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
	
		if ($this->checkPrivileges('map',$this->privStatus) == FALSE){
			redirect(ADMIN_ENC_URL);
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
		if ($this->checkLogin('A') == ''){
			redirect(ADMIN_ENC_URL);
		}else {
			redirect(ADMIN_ENC_URL.'/map/map_avail_drivers');
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
		   
		if ($this->checkLogin('A') == ''){
			redirect(ADMIN_ENC_URL);
		}else{
			$center=$this->config->item('latitude').','.$this->config->item('longitude');
            $coordinates=array(); #$coordinates=array(floatval($this->config->item('longitude')),floatval($this->config->item('latitude')));
			
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
			
			$location=$this->input->get('location');
			$city=$this->input->get('city');
			$radius=$this->input->get('radius');

			$address = '';
			if($city!=''){
				$address = urlencode($city);
			}
			if($location!=''){
				$address = urlencode($location);
			}
			if($address!=''){
				$urL = "https://maps.google.com/maps/api/geocode/json?address=$address&sensor=false".$this->data['google_maps_api_key'];
				$json = file_get_contents($urL);
				if($json!=false){
					$jsonArr = json_decode($json);
					$lat = $jsonArr->{'results'}[0]->{'geometry'}->{'location'}->{'lat'};
					$lang = $jsonArr->{'results'}[0]->{'geometry'}->{'location'}->{'lng'};
					$locationArr=array($lat,$lang);
					$coordinates=array_reverse($locationArr);
					$center=@implode($locationArr,',');
				}
			}
			if($radius==''){
                $driverList = $this->map_model->get_nearest_driver($coordinates,'',''); 
			}else{
				$operator_loc='';
				$driverList = $this->map_model->get_nearest_driver($coordinates,$operator_loc,$radius); 
			}
			$this->load->library('googlemaps');
			$config['center'] = $center;
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
			$this->load->view(ADMIN_ENC_URL.'/map/availbale_drivers',$this->data);
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
			redirect(ADMIN_ENC_URL);
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
			#echo '<pre>'; print_r($userList);die;
			} else {
              $this->setErrorMessage('error', 'No location Found','admin_no_location');
              redirect(ADMIN_ENC_URL.'/map/map_avail_users');
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
					$marker['icon'] = base_url().'images/user_marker.png';
					$marker['icon_scaledSize'] = '25,31';
					$marker['infowindow_content'] ="<div style='width:200px !important;height:50Px!important;'>".htmlentities(trim($user['user_name'])).'<br/>'.htmlentities(trim($user['email']))."</div>";
					$this->googlemaps->add_marker($marker);
				}
			}
			$this->data['mapContent'] = $this->googlemaps->create_map();
		
			$this->data['address'] = urldecode($address);
			if ($this->lang->line('admin_menu_map_view') != '') 
		    $title= stripslashes($this->lang->line('admin_menu_map_view')); 
		    else  $title = 'Map View';
			$this->data['heading'] = $title;
			$this->load->view(ADMIN_ENC_URL.'/map/availbale_users',$this->data);
		}
	}
  
   /**
	*
	* To view the estimated fare
	*
	* @Initiate HTML to available drivers on map page
	*	
	**/	
   	public function estimate_fare(){
		if ($this->checkLogin('A') == ''){
			redirect(ADMIN_ENC_URL);
		}else{
			echo $geoDistance = $this->map_model->geoDistance(13.061037,80.254521,13.022360,80.219498); echo '<br/>';
			$originlatlon='13.061037,80.254521';
			$destinationlatlon='13.022360,80.219498';
			$from = str_replace(' ','%20',$originlatlon);
			$to = str_replace(' ','%20',$destinationlatlon);

			$gmap=file_get_contents('https://maps.googleapis.com/maps/api/directions/json?origin='.$from.'&destination='.$to.'&alternatives=true&sensor=false&mode=driving'.$this->data['google_maps_api_key']);
			$routes=json_decode($gmap)->routes;
			usort($routes,create_function('$a,$b','return intval($a->legs[0]->distance->value) - intval($b->legs[0]->distance->value);'));
			echo $routes[0]->legs[0]->distance->text;echo '<br/>';
			echo $routes[0]->legs[0]->duration->text;echo '<br/>';
			$distance = preg_replace('/[^0-9.]+/i', '', $routes[0]->legs[0]->distance->text);
			echo $distance = (double) $distance;
			die;
			if ($this->lang->line('admin_menu_map_view') != '') 
		    $this->data['heading']= stripslashes($this->lang->line('admin_menu_map_view')); 
		    else  $this->data['heading'] = 'Map View';
			$this->load->view(ADMIN_ENC_URL.'/map/availbale_drivers',$this->data);
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
        $this->load->view(ADMIN_ENC_URL.'/map/online_tracking',$this->data);
    }
	
}

/* End of file map.php */
/* Location: ./application/controllers/admin/map.php */