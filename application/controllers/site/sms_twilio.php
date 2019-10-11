<?php

if (!defined('BASEPATH'))
    exit('No direct script access allowed');

/**
 * 
 * Sms related functions
 * @author Casperon
 *
 */
class Sms_twilio extends MY_Controller {

    function __construct() {
        parent::__construct();
        error_reporting(-1);
        $this->load->helper(array('cookie', 'date', 'form', 'email'));
        $this->load->library(array('encrypt', 'form_validation', 'twilio'));
        $this->data['loginCheck'] = $this->checkLogin('U');
        $this->load->model(array('user_model'));
        $this->load->model(array('sms_model'));
    }

    /**
     * 
     * Send Otp Ajax function 
     */
    function send_otp() {
		$returnArr['status'] = '0';
		$returnArr['otp'] = '0';
		$otp_phone = $this->input->post('otp_phone');
		$phone_code = $this->input->post('phone_code');
		$riderId = $this->input->post('riderId');
		$checkMob = $this->user_model->get_selected_fields(USERS,array('country_code' => $phone_code,'phone_number' => $otp_phone),array(),array('_id'));
		if($checkMob->num_rows() > 0){
			$returnArr['status'] = '2';
		} else {
		  if (substr($phone_code, 0, 1) == '+') {
            $phone_code = $phone_code;
			} else {
				$phone_code = '+' . $phone_code;
			}
			if ($otp_phone != '' && $phone_code != '') {
			 
				
				
				$otp_number = $this->user_model->get_random_string(6);
            
				if(isset($otp_phone)&&isset($phone_code)){
					$session_data=array(APP_NAME.'otp_phone_number'=>$otp_phone,APP_NAME.'otp_country_code'=>$phone_code,'isVerified'=>'false');
					$this->session->set_userdata($session_data);
				}
				$this->session->set_userdata(APP_NAME.'sms_otp', $otp_number);
				
		      $this->sms_model->opt_for_registration($phone_code, $otp_phone, $otp_number,$this->data['langCode']);
				if ($riderId != '') {
					try {
						$condition = array('_id' => MongoID(trim($riderId)));
						$this->user_model->update_details(USERS, array('mobile_otp' => $otp_number), $condition);
					} catch (MongoException $ex) {
					}
				}
			
				$mode = $this->config->item('twilio_account_type');
				$returnArr['mode'] = $mode;
				if( $mode == 'sandbox') $returnArr['otp'] = $otp_number;
				$returnArr['status'] = '1';
			}
		}
		echo json_encode($returnArr);
    }

   

    function otp_verification() {
		$returnArr['status'] = '0';
		$otp = $this->input->post('otp');
		if ((string)$otp == (string)$this->session->userdata(APP_NAME.'sms_otp')) {
			$this->session->set_userdata('isVerified','true');
			$returnArr['status'] = '1';
		}
		echo json_encode($returnArr);
    }
	function check_is_valid_otp_fields(){
	
			$otp_phone = $this->input->post('otp_phone');
			$phone_code = $this->input->post('phone_code');
			$stored_mobile_number=$this->session->userdata(APP_NAME.'otp_phone_number');
			$stored_country_code=$this->session->userdata(APP_NAME.'otp_country_code');
			$isVerified=$this->session->userdata('isVerified');
			
			if($stored_mobile_number==$otp_phone && $stored_country_code==$phone_code && $isVerified=='true'){
			echo json_encode('success');
			}else{
			echo json_encode('error');
			}
	}

}

?>