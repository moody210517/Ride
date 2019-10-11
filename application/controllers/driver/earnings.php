<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');
/**
* 
* This controller contains the functions related to driver management and login, forgot password
* @author Casperon
*
**/

class Earnings extends MY_Controller {
	function __construct(){
        parent::__construct();
		$this->load->helper(array('cookie','date','form'));
		$this->load->library(array('encrypt','form_validation'));		
		$this->load->model('driver_model');
    }
    
	 /**
    * 
    * This function loads the drivers earnings history
	*
    **/
	
	public function display_driver_earnings(){
		echo "Earnings";
	}

	
}
/* End of file earnings.php */
/* Location: ./application/controllers/driver/earnings.php */