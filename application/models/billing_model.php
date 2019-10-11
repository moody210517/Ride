<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');
/**
* 
* This model contains all db functions related to banner management
* @author Casperon
*
**/

class Billing_model extends My_Model{
	public function __construct(){
        parent::__construct();
    }
	
	/**
	*
	* 	This function return need to make billing or not
	*	String $billing_cycle
	*	String $last_billing_date
	*
	**/
	public function check_billing_cycle($billing_cycle,$last_billing_date){
		$s = FALSE;
		if($last_billing_date==''){
			$last_billing_date=date("Y-m-d");
		}else{
			$last_billing_date = $last_billing_date;
		}
		$today_date=date("Y-m-d");
		
		$last_billing_date;
		$today_date;
		
		$today_unix = strtotime($today_date);
		$last_billing_unix = strtotime($last_billing_date);
		
		if($today_unix>$last_billing_unix){
			$days = ($today_unix- $last_billing_unix)/24/3600; 
		}else{
			$days = 0;
		}
		
		/* 
		$datetime1 = new DateTime();
		$datetime2 = new DateTime($last_billing_date);
		$interval = $datetime1->diff($datetime2);
		$days = intval($interval->d);
		*/
		
 		if($billing_cycle == 1){
			if($days > $billing_cycle){
				$s = TRUE;
			}
		} else {
			if($days > $billing_cycle){
				$s = TRUE;
			}
		}
		
		return $s;
	}
	
	/**
	*
	* 	This function generate billing
	*	String $billing_cycle
	*	String $last_billing_date
	*
	**/
	public function generate_billing($billing_cycle,$last_billing_date){
		$today = (string)date('Y-m-d');
		$adSettings = $this->get_selected_fields(ADMIN, array('admin_id' =>'1'),array('billing_date'));
		$cBill = '1';
		if($adSettings->num_rows()>0){
			if(isset($adSettings->row()->billing_date)){
				if($adSettings->row()->billing_date == $today){
					$cBill = '0';
				}
			}
		}
		
		$this->update_details(ADMIN, array('billing_date'=>$today), array('admin_id' =>'1'));
		
		
		if($cBill=='1'){
			$fields = array();
			$url = base_url().'generate-billing';
			$this->load->library('curl');
			$output = $this->curl->simple_post($url, $fields);
		}
	    
	}
	
}
?>