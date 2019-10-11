<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');
/** 
* 
* Remove Rides
* @author Casperon
*
**/
 
class Cls_rides extends MY_Controller {
	function __construct(){
        parent::__construct();
		$this->load->helper(array('cookie','date','form','email'));
		$this->load->library(array('encrypt','form_validation'));
		
    }
	
	public function index() {
		$start_time = time()-300;
		$this->mongo_db->select(array('ride_id', 'type', 'ride_status', 'user'));
		$this->mongo_db->where(array("ride_status" => 'Booked',"type" => 'Now',"booking_information.est_pickup_date"=>array('$lt'=>MongoDATE($start_time))));
		$this->mongo_db->order_by(array('_id' => 'ASC'));
		$res = $this->mongo_db->get(RIDES);
		
		#echo "<pre>"; print_r($res->result()); 
		
	}
	
	
	
}

/* End of file cls_rides.php */
/* Location: ./application/controllers/cron/cls_rides.php */