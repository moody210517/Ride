<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

class Common extends MY_Controller {
	function __construct(){
        parent::__construct();
		$this->load->helper(array('cookie','date','form'));
		$this->load->library(array('encrypt','form_validation'));		
		$this->load->model(array('user_model'));
		$this->load->model(array('mail_model'));
		$this->load->model(array('app_model'));
    }
	
		/**
    * 
    * Check EMAIL does exists or not
    *
    */
    public function check_email_exists() {
		$returnArr['status'] = 0;
		$email = $this->input->post('email');
		$user_type = $this->input->post('user_type');
		
		$collection = USERS;
		if($user_type == 'driver'){
			$collection = DRIVERS;
		}
		$checkUser = $this->user_model->get_selected_fields($collection,array('email'=> $email),array('_id'));
		if($checkUser->num_rows()>0){
			$returnArr['status'] = 1;
		}
		echo json_encode($returnArr);
	}
	
	/**
    * 
    * Check PHONE NUMBER does exists or not
    *
    */
    public function check_phone_number_exists() {
		$returnArr['status'] = 0;
		$phone_number = $this->input->post('phone_number');
		$user_type = $this->input->post('user_type');
		
		$collection = USERS;
		$condition = array('phone_number' => $phone_number);
		if($user_type == 'driver'){
			$condition = array('mobile_number' => $phone_number);
			$collection = DRIVERS;
		}
		
		$checkUser = $this->user_model->get_selected_fields($collection,$condition,array('_id'));
		if($checkUser->num_rows()>0){
			$returnArr['status'] = 1;
		}
		echo json_encode($returnArr);
    }
	
	
}

/* End of file common.php */
/* Location: ./application/controllers/common.php */