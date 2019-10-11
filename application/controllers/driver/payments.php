<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');
/**
* 
* This controller contains the functions related to driver Payments Summaries
* @author Casperon
*
**/

class Payments extends MY_Controller {
	function __construct(){
        parent::__construct();
		$this->load->helper(array('cookie','date','form'));
		$this->load->library(array('encrypt','form_validation'));		
		$this->load->model('driver_model');
		if ($this->checkLogin('D') == ''){
			redirect('driver');
		}
    }
    
	 /**
    * 
    * This function loads the drivers earnings history
	*
    **/
	public function display_payments(){
		$driver_id = $this->checkLogin('D');
		$billings = $this->driver_model->get_all_details(BILLINGS,array('driver_id'=>$driver_id),array('bill_id'=>'DESC'));
		
		if ($this->lang->line('dash_drivers_payments') != '')
			$dash_drivers_payments = stripslashes($this->lang->line('dash_drivers_payments'));
		else
			$dash_drivers_payments = 'Drivers Payments';
		
		$this->data['sideMenu'] ='earnings';
		$this->data['billings'] = $billings;
		$this->data['heading'] = $dash_drivers_payments;
		$this->load->view('driver/payments/display_payments',$this->data);
	}
	/**
    * 
    * This function loads the drivers earnings history
	*
    **/
	public function payment_summary($invoice_id=""){
		$driver_id = $this->checkLogin('D');
		$billDetails = $this->driver_model->get_all_details(BILLINGS,array('invoice_id'=>floatval($invoice_id),'driver_id'=>$driver_id));
		if($billDetails->num_rows()>0){
			$fromdate=MongoEPOCH($billDetails->row()->bill_from);
			$todate=MongoEPOCH($billDetails->row()->bill_to);
				
			if($fromdate!='' && $todate!=''){
				$fdate=$fromdate;
				$tdate=$todate;
			}
			$rideSummary = $this->driver_model->get_trip_summary($driver_id,$fdate,$tdate);
			$rideList=array();
			if(!empty($rideSummary['result'])){
				$rideList=$rideSummary['result'];
			}			
			
			$billingsList = $this->driver_model->get_all_details(TRANSACTION,array('bill_period_from'=>MongoDATE($fromdate),'bill_period_to'=>MongoDATE($todate)));
				
			$billingARR=array();
			if($billingsList->num_rows()>0){
				$bill_id = $billingsList->row()->bill_id;
				$billingArr = $this->driver_model->get_all_details(BILLINGS,array('bill_id'=>$bill_id,'driver_id'=>$driver_id))->result_array();
				$billingARR = $billingArr[0];
			}
				
			$this->data['rideList']=$rideList;
											
			$this->data['bill_details']=$billingARR;
			

			if ($this->lang->line('dash_bill_details') != '')
				$dash_bill_details = stripslashes($this->lang->line('dash_bill_details'));
			else
				$dash_bill_details = 'Bill Details';
			
			$this->data['sideMenu'] ='earnings';
			$this->data['heading'] = $dash_bill_details;
			$this->load->view('driver/payments/payment_details',$this->data);
		}else{
			$this->setErrorMessage('error','We were unable to retrieve your statements at this time. Please try again later.','driver_unable_to_retrieve');
			redirect('driver/payments/display_payments','dash_unable_to_retrieve_your_statements');
		}
	}

	
}
/* End of file payments.php */
/* Location: ./application/controllers/driver/payments.php */