<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');
/**
*
*	Paypal Payment
* 
*	@package	CI
*	@subpackage	Controller
*	@author	Casperon
*
**/

class Paypal_payment extends MY_Controller { 
	public $mobdata = array();
	function __construct(){
        parent::__construct();
		$this->load->helper(array('cookie','date','form','email'));
		$this->load->library(array('encrypt','form_validation'));		
		$this->load->model('app_model');
		$this->load->model('rides_model');	
    }
	
	/**
	* 
	* callback url for paypal ipn notification
	*
	* @param string $custom  Paypal callback request information
	* @return CALLBACK,payment success
	*
	**/
	public function ipnpayment(){
		$newcustom = @explode('|',$_REQUEST['custom']);
		
		if($newcustom[0]=='RidePayment'){
			$transId = $_REQUEST['txn_id'];
			$Pray_Email = $_REQUEST['payer_email'];
			
			$user_id = $newcustom[1];
			$ride_id = $newcustom[2];
			$checkRide = $this->app_model->get_all_details(RIDES,array('ride_id'=>$ride_id,'pay_status'=>'Pending'));
			if($checkRide->num_rows() == 1){
				$paid_amount=0.00;
				if(isset($checkRide->row()->total)){
					if(isset($checkRide->row()->total['grand_fare']) && isset($checkRide->row()->total['wallet_usage'])){
						$paid_amount=round(($checkRide->row()->total['grand_fare']-$checkRide->row()->total['wallet_usage']),2);
					}
				}
				$pay_summary='Gateway';
				if(isset($checkRide->row()->pay_summary)){
					if($checkRide->row()->pay_summary!=''){
						if($checkRide->row()->pay_summary!='Gateway'){
							$pay_summary=$checkRide->row()->pay_summary.'_Gateway';
						}
					}else{
						$pay_summary='Gateway';
					}
				}
				$pay_summary = array('type'=>$pay_summary);
				$paymentInfo = array('ride_status'=>'Completed',
														'pay_status'=>'Paid',
														'history.pay_by_gateway_time'=>MongoDATE(time()),
														'total.paid_amount'=>round(floatval($paid_amount),2),
														'pay_summary'=>$pay_summary
													);
				$this->app_model->update_details(RIDES,$paymentInfo,array('ride_id'=>$ride_id));
				$avail_data = array('mode'=>'Available','availability'=>'Yes');
				$this->app_model->update_details(DRIVERS,$avail_data,array('_id'=>MongoID($driver_id)));			
				$transactionArr = array('type'=>'Paypal',
														'amount'=>floatval($paid_amount),
														'trans_id'=>$trans_id,
														'trans_date'=>MongoDATE(time())
													);
				$this->app_model->simple_push(PAYMENTS,array('ride_id'=>$ride_id),array('transactions'=>$transactionArr));
				
				$driver_id=$checkRide->row()->driver['id'];
				$driverVal = $this->app_model->get_selected_fields(DRIVERS,array('_id'=>MongoID($driver_id)),array('_id','push_notification'));
				if($driverVal->num_rows()>0){
					if(isset($driverVal->row()->push_notification)){
						if($driverVal->row()->push_notification!=''){
							$message='payment completed';										
							$options=array('ride_id'=>(string)$ride_id,'driver_id'=>$driver_id);
							if(isset($driverVal->row()->push_notification['type'])){
								if($driverVal->row()->push_notification['type']=='ANDROID'){
									if(isset($driverVal->row()->push_notification['key'])){
										if($driverVal->row()->push_notification['key']!=''){
											$this->sendPushNotification($driverVal->row()->push_notification['key'],$message,'payment_paid','ANDROID',$options,'DRIVER');
										}
									}
								}
								if($driverVal->row()->push_notification['type']=='IOS'){
									if(isset($driverVal->row()->push_notification['key'])){
										if($driverVal->row()->push_notification['key']!=''){
											$this->sendPushNotification($driverVal->row()->push_notification['key'],$message,'payment_paid','IOS',$options,'DRIVER');
										}
									}
								}
							}
						}
					}
				}
			}
		}
	}

}
/* End of file paypal_payment.php */
/* Location: ./application/controllers/admin/paypal_payment.php */