<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');
/**
* 
* This controller contains the functions related to driver management and login, forgot password
* @author Casperon
*
**/

class Login extends MY_Controller {
	function __construct(){
        parent::__construct();
		$this->load->helper(array('cookie','date','form'));
		$this->load->library(array('encrypt','form_validation'));		
		$this->load->model('driversettings_model');
		$this->load->model('driver_model');
    }
    
	
}