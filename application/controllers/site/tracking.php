<?php if (!defined('BASEPATH')) exit('No direct script access allowed');

/**
* 
* Tracking related functions
* @author Casperon
*
* */
class Tracking extends MY_Controller {

	function __construct() {
		parent::__construct();
		$this->load->helper(array('cookie', 'date', 'form', 'email'));
		$this->load->library(array('encrypt', 'form_validation'));
		$this->load->model(array('user_model','app_model'));
	}
	
	/**
	*
	* This function  tracks the ride details
	*
	* */
	public function track_ride_map_details() {
		if(isset($_POST['rideId']) || isset($_GET['rideId'])){
			$ride_id = $this->input->post('rideId');
			if ($ride_id == '') {
				$ride_id = $this->input->get('rideId');
			}
		} else if(isset($_POST['q']) || isset($_GET['q'])){
			$ride_id = $this->input->post('q');
			if ($ride_id == '') {
				$ride_id = $this->input->get('q');
			}
		}
		
		if ($ride_id == '') {
			$this->setErrorMessage('error','Invalid ride id','tracking_invalid_id');
			redirect('');
		}
		$ride_info = $this->user_model->get_all_details(RIDES,array('ride_id' => $ride_id));
		if($ride_info->num_rows() == 0){
			$this->setErrorMessage('error','No records found','admin_drivers_no_record_found');
			redirect('');
		}
		if ($this->lang->line('is_being_picked') != '') {
			$is_being_picked= stripslashes($this->lang->line('is_being_picked')); 
		}else{
			$is_being_picked = "is being picked up";
		}
		if ($this->lang->line('is_en_route') != '') {
			$is_en_route= stripslashes($this->lang->line('is_en_route')); 
		}else{
			$is_en_route = "is en route";
		}
		if ($this->lang->line('has_arrived') != '') {
			$has_arrived= stripslashes($this->lang->line('has_arrived')); 
		}else{
			$has_arrived = "has arrived";
		}
		if ($this->lang->line('has_finished') != '') {
			$has_finished = stripslashes($this->lang->line('has_finished')); 
		}else{
			$has_finished = "has finished";
		}
        
        if ($this->lang->line('has_completed') != '') {
			$has_completed = stripslashes($this->lang->line('has_completed')); 
		}else{
			$has_completed = "has completed";
		}
        
		if ($this->lang->line('track_ride') != '') {
			$ride_txt= stripslashes($this->lang->line('track_ride')); 
		}else{
			$ride_txt = "Ride";
		}
		
		$msg='';
		
		switch($ride_info->row()->ride_status) {
		  case 'Confirmed':
		   $msg=$is_being_picked;
		   break;
		  case 'Arrived':
		   $msg=$has_arrived;
		   break;
		  case 'Onride':
		   $msg=$is_en_route;
		  break;
		  case 'Finished':
		   $msg=$has_finished;
		  break;
		  case 'Completed':
		
		   $msg=$has_completed;
		  break;
		  default:
		   $msg=$ride_txt." ".get_language_value_for_keyword($ride_info->row()->ride_status,$this->data['langCode']);
		   
		}
		
		$driver_info = array();
		if(isset($ride_info->row()->driver['id'])){
			if($ride_info->row()->driver['id'] != ''){
				$driver_info = $this->user_model->get_selected_fields(DRIVERS,array('_id' => MongoID($ride_info->row()->driver['id'])),array('avg_review','image','loc'))->row();
			}
		}

		$this->data['driver_info'] = $driver_info;
		$this->data['track_msg'] = $msg;
		$this->data['ride_info'] = $ride_info;
		
		$tracking_values= $this->user_model->get_all_details(TRAVEL_HISTORY,array('ride_id' => $ride_id));
		
		$tracking_array=array();
		if(isset($tracking_values->row()->history_end) && !empty($tracking_values->row()->history_end)){
				
			foreach($tracking_values->row()->history_end as $track){
				$ride_begin_time=MongoEPOCH($ride_begin_time);
				$ride_end_time=MongoEPOCH($ride_end_time);
				$update_time=MongoEPOCH($track['update_time']);
				if(isset($ride_begin_time) && $ride_begin_time != ""){
					if($ride_begin_time <= $update_time){
						if($ride_end_time != ""){
							if($ride_end_time >= $update_time){
							
								
								$tracking_array[]=array('lat'=>$track['lat'],'lon'=>$track['lon']);
							}
						}else{
							$tracking_array[]=array('lat'=>$track['lat'],'lon'=>$track['lon']);
						}
					}
				}else{
					$tracking_array[]=array('lat'=>$track['lat'],'lon'=>$track['lon']);
				}
			}
		}
		
		
		if ($this->lang->line('track_ride_heading') != '') 
			$heading = stripslashes($this->lang->line('track_ride_heading')); 
		else  
			$heading = 'Track Ride';
		
		$this->data['heading'] = $heading.' - '.$this->config->item('email_title');
		$this->data['tracking_record']=$tracking_array;
		$this->load->view('site/rides/track_ride_in_map', $this->data);
	}

}

/* End of file tracking.php */
/* Location: ./application/controllers/site/tracking.php */