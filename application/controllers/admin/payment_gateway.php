<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

/**
*
*	Payment gateway
* 
*	@package		CI
*	@subpackage		Controller
*	@author			Casperon
*
**/

class Payment_gateway extends MY_Controller {

	function __construct(){
        parent::__construct();
		$this->load->helper(array('cookie','date','form'));
		$this->load->library(array('encrypt','form_validation'));		
		$this->load->model('payment_gateway_model');
		$this->load->model('admin_model');
		
		if ($this->checkPrivileges('payment_gateway',$this->privStatus) == FALSE){
			$this->setErrorMessage('error','You have no privilege for this managment','admin_payment_gate_no_privilage');
			redirect(ADMIN_ENC_URL.'/dashboard/admin_dashboard');
		}
		
		$c_fun = $this->uri->segment(3);
		$restricted_function = array('insertEditGateway','change_payment_gateway_status_global','pay_by_cash_status','use_wallet_amount_status');
		if(in_array($c_fun,$restricted_function) && $this->data['isDemo']===TRUE){
			$this->setErrorMessage('error','You are in demo version. you can\'t do this action.','admin_common_demo_version');
			redirect($_SERVER['HTTP_REFERER']); die;
		}
    }
    
	/**
	*
	* Initiate to display the list of payment gateway
	* 	
	* @return http request to show the payment gateway list
	*	
	**/
   	public function index(){	
		if ($this->checkLogin('A') == ''){
			redirect(ADMIN_ENC_URL);
		} else {
			redirect(ADMIN_ENC_URL.'/payment_gateway/display_payment_gateway_list');
		}
	}
	
	
	/**
	*
	* Display the payment gateway lists
	* 	
	* @return HTML to show the payment gateway list
	*	
	**/
	public function display_payment_gateway_list(){
		if ($this->checkLogin('A') == ''){
			redirect(ADMIN_ENC_URL);
		}else {
		    if ($this->lang->line('admin_menu_payment_gateway') != '') 
		    $this->data['heading']= stripslashes($this->lang->line('admin_menu_payment_gateway')); 
		    else  $this->data['heading'] = 'Payment Gateway';
			$condition = array();
			$this->data['gatewayLists'] = $this->payment_gateway_model->get_all_details(PAYMENT_GATEWAY,$condition);
			$this->load->view(ADMIN_ENC_URL.'/payment_gateway/display_payment_gateway',$this->data);
		}
	}
	
	/**
	* 
	* Display to edit the payment gateway form 
	*
	* @param string $gateway_id Gateway MongoDB\BSON\ObjectId
	* @return HTML to show the payment gateway lists
	*
	**/	
	public function edit_gateway_form(){
		if ($this->checkLogin('A') == ''){
			redirect(ADMIN_ENC_URL);
		}else {
		    if ($this->lang->line('admin_edit_payment_gateway_settings') != '') 
		    $this->data['heading']= stripslashes($this->lang->line('admin_edit_payment_gateway_settings')); 
		    else  $this->data['heading'] = 'Edit Gateway Settings';
			$gateway_id = $this->uri->segment(4,0);
			$condition = array('_id'=>MongoID($gateway_id));
			$this->data['gateway_details'] = $this->payment_gateway_model->get_all_details(PAYMENT_GATEWAY,$condition);
			if ($this->data['gateway_details']->num_rows() == 1){
				$this->load->view(ADMIN_ENC_URL.'/payment_gateway/edit_gateway',$this->data);
			}else {
				redirect(ADMIN_ENC_URL);
			}
		}
	}
	
	/**
	* 
	* To add or update the payment gateway mode and empty all the stripe customer id for user,when change the mode
	* 
	* @param string $gateway_id Gateway MongoDB\BSON\ObjectId
	* @param string $mode Mode sandbox/live
	* @return HTTP redirect to show the payment gateway list
	*
	**/	
	public function insertEditGateway(){
		if ($this->checkLogin('A') == ''){
			redirect(ADMIN_ENC_URL);
		}else {
				$gateway_id = $this->input->post('gateway_id');
				$mode = $this->input->post('mode');
				$gatewaySettings = array();
				if ($mode == ''){
					$gatewaySettings['mode'] = 'sandbox';
				}else {
					$gatewaySettings['mode'] = 'live';
				}
				
				$condition = array('_id'=>MongoID($gateway_id));
				$getGateWayName = $this->payment_gateway_model->get_selected_fields(PAYMENT_GATEWAY,$condition,array('gateway_name'));
				
				
				$excludeArr = array("gateway_id","mode");
				foreach ($this->input->post() as $key => $val){
					if (!in_array($key, $excludeArr)){
						$gatewaySettings[$key] = $val;
					}
				}
				if($getGateWayName->row()->gateway_name == 'Stripe'){
					if($gatewaySettings['mode'] != $this->data['stripe_settings']['settings']['mode'] || $gatewaySettings['secret_key'] != $this->data['stripe_settings']['settings']['secret_key'] || $gatewaySettings['publishable_key'] != $this->data['stripe_settings']['settings']['publishable_key']){
						$userdataArr = array('stripe_customer_id' =>''); 
						$this->payment_gateway_model->update_details(USERS,$userdataArr,array());
					}
				}
				$dataArr = array('settings' => $gatewaySettings);
				$condition = array('_id'=>MongoID($gateway_id));
				if ($gateway_id == ''){
					$this->setErrorMessage('success','Payment gateway updated successfully','admin_payment_gate_update_success');
				}else {
					$this->payment_gateway_model->update_details(PAYMENT_GATEWAY,$dataArr,$condition);
					$this->payment_gateway_model->savePaymentSettings();
					$this->setErrorMessage('success','Payment gateway updated successfully','admin_payment_gate_update_success');
				}
				redirect(ADMIN_ENC_URL.'/payment_gateway/display_payment_gateway_list');
		}
	}
	
	/**
	* 
	* To add or update the payment gateway mode and empty all the stripe customer id for user,when change the mode
	* 
	* @param string $gateway_id Gateway MongoDB\BSON\ObjectId
	* @param string $mode Mode sandbox/live
	* @return HTTP redirect to show the payment gateway list
	*
	**/	
	public function change_gateway_status(){
		if ($this->checkLogin('A') == ''){
			redirect(ADMIN_ENC_URL);
		}else {
				$mode = $this->uri->segment(4,0);
				$gateway_id = $this->uri->segment(5,0);
				$status = ($mode == '0')?'Disable':'Enable';
				$newdata = array('status' => $status);				
				$condition = array('_id'=>MongoID($gateway_id));
				$this->payment_gateway_model->update_details(PAYMENT_GATEWAY,$newdata,$condition);
				$this->payment_gateway_model->savePaymentSettings();
				$payArr = $this->payment_gateway_model->get_all_details(PAYMENT_GATEWAY,array("status"=>"Enable"));
				if($payArr->num_rows()==0){
					$condition = array('admin_id'=>'1');
					$newdata = array('pay_by_cash' => "Enable");
					$this->payment_gateway_model->update_details(ADMIN,$newdata,$condition);
					$this->admin_model->saveAdminSettings();
					
					$this->mail_model->payment_alert_notification();
				}
				sleep(5);
				$this->setErrorMessage('success','Payment Gateway Status Changed Successfully','admin_payment_gate_status_successs');
				redirect(ADMIN_ENC_URL.'/payment_gateway/display_payment_gateway_list');
		}
	}
	
	/**
	 * Bulk change of paymeny gayment status
	 *  
	 * @return HTTP redirect to show the payment gateway list
	 */
	public function change_payment_gateway_status_global(){
			if(count($_POST['checkbox_id']) > 0 &&  $_POST['statusMode'] != ''){ 
				$this->payment_gateway_model->activeInactiveCommon(PAYMENT_GATEWAY,'_id');
				$this->payment_gateway_model->savePaymentSettings();
				$payArr = $this->payment_gateway_model->get_all_details(PAYMENT_GATEWAY,array("status"=>"Enable"));
				
				if($payArr->num_rows()==0){
					$condition = array('admin_id'=>'1');
					$newdata = array('pay_by_cash' => "Enable");
					sleep(5);
					$this->payment_gateway_model->update_details(ADMIN,$newdata,$condition);
					$this->admin_model->saveAdminSettings();
					
					$this->mail_model->payment_alert_notification();
					
				}
				$this->setErrorMessage('success','Payment gateway records status changed successfully','admin_payment_gate_record_changed');
				redirect(ADMIN_ENC_URL.'/payment_gateway/display_payment_gateway_list');
			} else {
				$this->setErrorMessage('error','Payment gateway records failed to update','admin_payment_gate_record_failed_update');
				redirect(ADMIN_ENC_URL.'/payment_gateway/display_payment_gateway_list');
			}
	}
	
	/**
	 * gateway for pay by cash change the status
	 * 
	 * @param string $mode Mode Disable/Enable
	 * 
	 * @return HTTP redirect to show the payment gateway list
	 */
	public function pay_by_cash_status(){
		if ($this->checkLogin('A') == ''){
			redirect(ADMIN_ENC_URL);
		}else {
			$mode = $this->uri->segment(4,0);
			$condition = array('admin_id'=>'1');
			$status = ($mode == '0')?'Disable':'Enable';
			$newdata = array('pay_by_cash' => $status);
			sleep(5);
			$payArr = $this->payment_gateway_model->get_all_details(PAYMENT_GATEWAY,array("status"=>"Enable"));
			if($payArr->num_rows()>0){
				$this->payment_gateway_model->update_details(ADMIN,$newdata,$condition);
				$this->admin_model->saveAdminSettings();
				$this->setErrorMessage('success','Payment Gateway Status Changed Successfully','admin_payment_gate_status_successs');
			}else{
				$this->setErrorMessage('error','Payment gateway records failed to update','admin_payment_gate_record_failed_update');
			}
			redirect(ADMIN_ENC_URL.'/payment_gateway/display_payment_gateway_list');
		}
	}
	/**
	 * gateway for pay by wallet change the status
	 * 
	 * @param string $mode Mode Disable/Enable
	 * 
	 * @return HTTP redirect to show the payment gateway list
	 */
	public function use_wallet_amount_status(){
		if ($this->checkLogin('A') == ''){
			redirect(ADMIN_ENC_URL);
		}else {
			$mode = $this->uri->segment(4,0);
			$condition = array('admin_id'=>'1');
			$status = ($mode == '0')?'Disable':'Enable';
			$newdata = array('use_wallet_amount' => $status);
			sleep(5);
			$this->payment_gateway_model->update_details(ADMIN,$newdata,$condition);
			$this->admin_model->saveAdminSettings();
			$this->setErrorMessage('success','Payment Gateway Status Changed Successfully','admin_payment_gate_status_successs');
			redirect(ADMIN_ENC_URL.'/payment_gateway/display_payment_gateway_list');
		}
	}
	
}

/* End of file payment_gateway.php */
/* Location: ./application/controllers/admin/payment_gateway.php */