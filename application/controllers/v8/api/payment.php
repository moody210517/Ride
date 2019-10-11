<?php if (!defined('BASEPATH')) exit('No direct script access allowed');

/**
* 
* User Trip Payment webview related functions
* @author Casperon
*
**/

class Payment extends MY_Controller {

    public $mobdata = array();

    function __construct() {
        parent::__construct();
        $this->load->helper(array('cookie', 'date', 'form', 'email'));
        $this->load->library(array('encrypt', 'form_validation'));
        $this->load->model('app_model');
        $this->load->model('rides_model');


        if (isset($_GET['mobileId']) && isset($_POST['mobileId'])) {
            $this->load->view('v6/mobile/error.php', $this->mobdata);
            die;
        } else {
             if (isset($_GET['mobileId'])) {
                 $mobileId = $_GET['mobileId'];
             } else if(isset($_POST['mobileId'])){
                 $mobileId = $_POST['mobileId'];
             }
            $mobileData = $this->app_model->get_all_details(MOBILE_PAYMENT, array('_id' => MongoID($mobileId)));
            if ($mobileData->num_rows() == 0) {
                $this->load->view('v6/mobile/error.php', $this->mobdata);
                die;
            } else {
                $this->mobdata['mobileId'] = (string) $mobileData->row()->_id;
                $this->mobdata['user_id'] = $mobileData->row()->user_id;
                $this->mobdata['driver_id'] = $mobileData->row()->driver_id;
                $this->mobdata['ride_id'] = $mobileData->row()->ride_id;
                $this->mobdata['payment'] = $mobileData->row()->payment;
                $this->mobdata['payment_id'] = $mobileData->row()->payment_id;
                $this->mobdata['total_amount'] = $mobileData->row()->amount;
                $this->mobdata['tips_amount'] = $mobileData->row()->tips_amount;
				
				$checkRideV = $this->app_model->get_all_details(RIDES, array('ride_id' => $this->mobdata['ride_id']));
				if($checkRideV->num_rows()>0){
					$currency = $this->data['dcurrencyCode'];                       
					$currency_status=$this->get_stripe_provide_currency($currency);            
					if($currency_status) {
						if(isset($checkRideV->row()->currency)) $currency = $checkRideV->row()->currency;         
					} else {
						$original_currency = 'USD';  
						if($currency != $original_currency){               
							$currency='USD';
						}
					}
				}
				$this->mobdata['pay_currency'] = $currency;
			
			
            }
        }
        
        
        $lang_code = $this->input->get('lang');
		if($lang_code!=""){
			$this->load->helper('lg_helper');
			change_web_language($lang_code);
		}
    }

    /**
     * 
     * Loading Credit Card Payment Form
     *
     * */
    public function authorizedotNet() {
        $this->load->view('v8/webview/trip/credit_card_payment.php', $this->mobdata);
    }

    /**
     * 
     * Payment Process using credit card
     *
     * */
    public function userPaymentCard() {
        $user_id = $this->mobdata['user_id'];
        $ride_id = $this->mobdata['ride_id'];

        $checkRide = $this->app_model->get_all_details(RIDES, array('ride_id' => $ride_id, 'user.id' => $user_id));
        if ($checkRide->num_rows() == 1) {
            $this->app_model->update_details(RIDES, array('pay_status' => 'Processing'), array('ride_id' => $ride_id));

            $tips_amt = 0.00;
            if (isset($checkRide->row()->total['tips_amount'])) {
                if ($checkRide->row()->total['tips_amount'] > 0) {
                    $tips_amt = $checkRide->row()->total['tips_amount'];
                }
            }

            $amount = $this->mobdata['total_amount'] + $tips_amt;
			
			if(isset($checkRide->row()->currency)){
				$currency = $checkRide->row()->currency;
			}
			$original_currency = 'USD';
			if($currency != $original_currency){
				$currencyval = $this->app_model->get_currency_value(round($amount, 2), $currency, $original_currency);
				if (!empty($currencyval)) {
					$amount = $currencyval['CurrencyVal'];
				}
			}
			
            //Authorize.net Intergration
            $Auth_Details = $this->data['authorize_net_settings'];
            $Auth_Setting_Details = $Auth_Details['settings'];

            define("AUTHORIZENET_API_LOGIN_ID", $Auth_Setting_Details['Login_ID']);    // Add your API LOGIN ID
            define("AUTHORIZENET_TRANSACTION_KEY", $Auth_Setting_Details['Transaction_Key']); // Add your API transaction key
            define("API_MODE", $Auth_Setting_Details['mode']);


            if (API_MODE == 'sandbox') {
                define("AUTHORIZENET_SANDBOX", true); // Set to false to test against production
            } else {
                define("AUTHORIZENET_SANDBOX", false);
            }
            define("TEST_REQUEST", "FALSE");
            require_once './authorize/AuthorizeNet.php';

            $transaction = new AuthorizeNetAIM;
            $transaction->setSandbox(AUTHORIZENET_SANDBOX);
            $transaction->setFields(array('amount' => $amount,
                'card_num' => $this->input->post('cardNumber'),
                'exp_date' => $this->input->post('CCExpDay') . '/' . $this->input->post('CCExpMnth'),
                'first_name' => $checkRide->row()->user['name'],
                'last_name' => '',
                'address' => '',
                'city' => '',
                'state' => '',
                'country' => '',
                'phone' => $checkRide->row()->user['phone'],
                'email' => $checkRide->row()->user['email'],
                'card_code' => $this->input->post('creditCardIdentifier')
                    )
            );

            #echo '<pre>'; print_r($transaction);die;			
            $response = $transaction->authorizeAndCapture();

            if ($response->approved) {
                redirect('v8/webview/trip/success/' . $user_id . '/' . $ride_id . '/credit-card/' . $response->transaction_id . '?mobileId=' . $this->mobdata['mobileId']);
            } else {
                redirect('v8/webview/trip/failed/' . $response->response_reason_text . '?mobileId=' . $this->mobdata['mobileId']);
            }
        } else {
            redirect('v8/webview/trip/Cancel?mobileId=' . $this->mobdata['mobileId']);
        }
    }

    /**
     * 
     * Payment Process using Paypal
     *
     * */
    public function paypal() {
        $user_id = $this->mobdata['user_id'];
        $ride_id = $this->mobdata['ride_id'];

        $checkRide = $this->app_model->get_all_details(RIDES, array('ride_id' => $ride_id, 'user.id' => $user_id));
        if ($checkRide->num_rows() == 1) {
            $this->app_model->update_details(RIDES, array('pay_status' => 'Processing'), array('ride_id' => $ride_id));

            $tips_amt = 0.00;
            if (isset($checkRide->row()->total['tips_amount'])) {
                if ($checkRide->row()->total['tips_amount'] > 0) {
                    $tips_amt = $checkRide->row()->total['tips_amount'];
                }
            }

            $amount = $this->mobdata['total_amount'] + $tips_amt;

            /* Paypal integration start */
            $this->load->library('paypal_class');

            define("PAYPAL_API", $this->config->item('payment_1'));
            $Paypal_Details = unserialize(PAYPAL_API);
            $Paypal_Setting_Details = $Paypal_Details['settings'];

            $paypalmode = $Paypal_Setting_Details['mode'];
            $paypalEmail = $Paypal_Setting_Details['merchant_email'];

            $item_name = $this->config->item('email_title') . ' payment ride : ' . $ride_id;
			
			$totalAmount = $amount;
            $currency = $checkRide->row()->currency;
            $original_currency = 'USD';
			if($currency != $original_currency){
				$currencyval = $this->app_model->get_currency_value(round($amount, 2), $currency, $original_currency);
				if (!empty($currencyval)) {
					$totalAmount = $currencyval['CurrencyVal'];
				}
			}

            $quantity = 1;

            if ($paypalmode == 'sandbox') {
                $this->paypal_class->paypal_url = 'https://www.sandbox.paypal.com/cgi-bin/webscr';   // testing paypal url
            } else {
                $this->paypal_class->paypal_url = 'https://www.paypal.com/cgi-bin/webscr';     // paypal url
            }
            $this->paypal_class->add_field('currency_code', $original_currency);

            $this->paypal_class->add_field('business', $paypalEmail); // Business Email

            $this->paypal_class->add_field('return', base_url() . 'v8/webview/trip/success/' . $user_id . '/' . $ride_id . '/paypal/?mobileId=' . $this->mobdata['mobileId']); // Return URL

            $this->paypal_class->add_field('cancel_return', base_url() . 'v8/webview/trip/cancelled?mobileId=' . $this->mobdata['mobileId']); // Cancel URL

            #$this->paypal_class->add_field('notify_url', base_url() . 'v8/webview/trip/ipn'); // Notify url

            $this->paypal_class->add_field('custom', 'RidePayment|' . $user_id . '|' . $ride_id); // Custom Values			

            $this->paypal_class->add_field('item_name', $item_name); // Product Name

            $this->paypal_class->add_field('user_id', $user_id);

            $this->paypal_class->add_field('quantity', $quantity); // Quantity

            $this->paypal_class->add_field('amount', $totalAmount); // Price

            $this->paypal_class->submit_paypal_post();
        } else {
            redirect('v8/webview/trip/Cancel?mobileId=' . $this->mobdata['mobileId']);
        }
    }

    /**
     * 
     * Loads the stripe pay form for manual payment
     *
     * */
    public function stripe() {
        $this->mobdata['auto_charge'] = $this->data['auto_charge'];
        $this->mobdata['stripe_settings'] = $this->data['stripe_settings'];
		$this->load->view('v8/webview/trip/stripe_payment_card.php', $this->mobdata);
    }

    /**
     * 
     * Complete the payment process through stripe pay and manual payment
     *
     * */
    public function stripe_payment_process() {

        $user_id = $this->input->post('user_id');
        $total_amount = $this->input->post('total_amount');
        $ride_id = $this->input->post('transaction_id');
        $email = $this->input->post('stripeEmail');

        $checkRide = $this->app_model->get_all_details(RIDES, array('ride_id' => $ride_id, 'user.id' => $user_id));
        $this->app_model->update_details(RIDES, array('pay_status' => 'Processing'), array('ride_id' => $ride_id));
		
		$cardInfo = 'Yes';
		if($this->input->post('stripeToken') == ''){
			$cardArr = array();
			if($this->input->post('card_number') != '') $cardArr['card_number'] = $this->input->post('card_number');			
			if($this->input->post('exp_month') != '') $cardArr['exp_month'] = $this->input->post('exp_month');			
			if($this->input->post('exp_year') != '') $cardArr['exp_year'] = $this->input->post('exp_year');
			if($this->input->post('cvc_number') != '') $cardArr['cvc_number'] = $this->input->post('cvc_number'); 
			if(count($cardArr) == 4){
				$cardInfo = 'Yes';
			} else {
				$cardInfo = 'No';
			}
		}
		
		 $getUsrCond = array('_id' => MongoID($user_id));

        if ($checkRide->num_rows() == 1 && $cardInfo == 'Yes') {

            $tips_amt = 0.00;
            if (isset($checkRide->row()->total['tips_amount'])) {
                if ($checkRide->row()->total['tips_amount'] > 0) {
                    $tips_amt = $checkRide->row()->total['tips_amount'];
                }
            }

            $total_amount = $total_amount + $tips_amt;
            $get_user_info = $this->app_model->get_selected_fields(USERS, $getUsrCond, array('email', 'stripe_customer_id'));
            if ($email == '') {
                $email = $get_user_info->row()->email;
            }

            $stripe_customer_id = '';
            $auto_pay_status = 'No';

            require_once('./stripe/lib/Stripe.php');

            $stripe_settings = $this->data['stripe_settings'];
            $secret_key = $stripe_settings['settings']['secret_key'];
            $publishable_key = $stripe_settings['settings']['publishable_key'];

            $stripe = array(
                "secret_key" => $secret_key,
                "publishable_key" => $publishable_key
            );

			$product_description = ucfirst($this->config->item('email_title')) . ' - Ride Charge #'.$ride_id;
			
			$currency = $this->data['dcurrencyCode'];                       
            $currency_status=$this->get_stripe_provide_currency($currency);            
            if($currency_status) {                               
               if(isset($checkRide->row()->currency))
                $currency = $checkRide->row()->currency;         
                $amounts = $this->get_stripe_currency_smallest_unit($total_amount,$currency);		
            } else {                     
                $original_currency = 'USD';  
                    if($currency != $original_currency){        
                    $currencyval = $this->app_model->get_currency_value(round($total_amount, 2), $currency, $original_currency);
                    if (!empty($currencyval)) {      
                    $amounts = round($currencyval['CurrencyVal']*100);         
                    }                    
                    $currency='USD';    
                 }                
            }
			
			#echo '<pre>'; print_r($_POST);die;
            Stripe::setApiKey($secret_key);
            $token = $this->input->post('stripeToken');

            try {
                // Create a Customer
				
				if($token == '' && !empty($cardArr)){
					$getToken = Stripe_Token::create(
						array(
							"card" => array(
								"number" => $cardArr['card_number'],
								"exp_month" => $cardArr['exp_month'],
								"exp_year" => $cardArr['exp_year'],
								"cvc" => ($cardArr['cvc_number'])
							)
						)
					);
					$token = $getToken['id'];
				}
				

                $customer_id = $stripe_customer_id;
                if ($customer_id == '') {
                    $customer = Stripe_Customer::create(array(
                                "card" => $token,
                                "description" => $product_description,
                                "email" => $email)
                    );
                    $customer_id = $customer->id;
                }


                $this->app_model->update_details(USERS, array('stripe_customer_id' => $customer_id), $getUsrCond);


                // Charge the Customer instead of the card
                $charge = Stripe_Charge::create(array(
                            "amount" => $amounts, # amount in cents, again
                            "currency" => $currency,
                            "customer" => $customer_id,
                            "description" => $product_description)
                );
                redirect('v8/webview/trip/success/' . $user_id . '/' . $ride_id . '/stripe/' . $charge['id'] . '?mobileId=' . $this->mobdata['mobileId']);
            } catch (Exception $e) {
                $error = $e->getMessage();
                if ($error == '') {
                    $error = 'Payment Failed';
                }
				$this->app_model->update_details(USERS, array('stripe_customer_id' => ''), $getUsrCond);
                redirect('v8/webview/trip/failed/' . $error . '?mobileId=' . $this->mobdata['mobileId']);
            }
        } else {
            redirect('v8/webview/trip/Cancel?mobileId=' . $this->mobdata['mobileId']);
        }
    }

    /**
     * 
     * Loading success payment
     *
     * */
    public function success() {
        $user_id = $this->uri->segment(5);
        $ride_id = $this->uri->segment(6);
        $payment_type = $this->uri->segment(7);
        $trans_id = $this->uri->segment(8);
        $payment_status = 'Completed';

        if ($payment_type == 'paypal') {
            $trans_id = $_REQUEST['txn_id'];
        }

        if ($payment_status == 'Completed') {
            $checkRide = $this->app_model->get_all_details(RIDES, array('ride_id' => $ride_id));
            if ($checkRide->num_rows() == 1) {
                if ($checkRide->row()->pay_status == 'Pending' || $checkRide->row()->pay_status == 'Processing') {
                    $paid_amount = 0.00;
                    if (isset($checkRide->row()->total)) {
                        if (isset($checkRide->row()->total['grand_fare']) && isset($checkRide->row()->total['wallet_usage'])) {
                            $paid_amount = round(($checkRide->row()->total['grand_fare'] - $checkRide->row()->total['wallet_usage']), 2);
                        }
                    }
                    $pay_summary = 'Gateway';
                    if (isset($checkRide->row()->pay_summary['type'])) {
                        if ($checkRide->row()->pay_summary['type'] != '') {
                            if ($checkRide->row()->pay_summary['type'] != 'Gateway') {
                                $pay_summary = $checkRide->row()->pay_summary['type'] . '_Gateway';
                            }
                        } else {
                            $pay_summary = 'Gateway';
                        }
                    }
					
					   $tips_amt = 0.00; 
                  if (isset($checkRide->row()->total['tips_amount']) && $checkRide->row()->total['tips_amount'] > 0) {
                     $tips_amt = $checkRide->row()->total['tips_amount'];
                  }
                  $paid_amount_with_tips = $paid_amount + $tips_amt;
					
					
					
					
                    $pay_summary = array('type' => $pay_summary);
                    $paymentInfo = array('ride_status' => 'Completed',
                        'pay_status' => 'Paid',
                        'history.pay_by_gateway_time' => MongoDATE(time()),
                        'total.paid_amount' => round(floatval($paid_amount), 2),
                        'total.paid_amount_with_tips' => round(floatval($paid_amount_with_tips), 2),
                        'pay_summary' => $pay_summary
                    );
                    $this->app_model->update_details(RIDES, $paymentInfo, array('ride_id' => $ride_id));
                    /* Update Stats Starts */
                    $current_date = MongoDATE(strtotime(date("Y-m-d 00:00:00")));
                    $field = array('ride_completed.hour_' . date('H') => 1, 'ride_completed.count' => 1);
                    $this->app_model->update_stats(array('day_hour' => $current_date), $field, 1);
                    /* Update Stats End */

                    $avail_data = array('mode' => 'Available', 'availability' => 'Yes');
                    $driver_id = $checkRide->row()->driver['id'];
                    $this->app_model->update_details(DRIVERS, $avail_data, array('_id' => MongoID($driver_id)));
                    $transactionArr = array('type' => 'Card',
                        'amount' => floatval($paid_amount),
                        'trans_id' => $trans_id,
                        'trans_date' => MongoDATE(time())
                    );
                    $this->app_model->simple_push(PAYMENTS, array('ride_id' => $ride_id), array('transactions' => $transactionArr));
                    $driverVal = $this->app_model->get_selected_fields(DRIVERS, array('_id' => MongoID($driver_id)), array('_id', 'push_notification'));
                    if ($driverVal->num_rows() > 0) {
                        if (isset($driverVal->row()->push_notification)) {
                            if ($driverVal->row()->push_notification != '') {
                                $message = $this->format_string("payment successfully completed", "payment_completed", '', 'driver', (string)$driver_id);
                                $options = array('ride_id' => (string) $ride_id, 'driver_id' => $driver_id);
                                if (isset($driverVal->row()->push_notification['type'])) {
                                    if ($driverVal->row()->push_notification['type'] == 'ANDROID') {
                                        if (isset($driverVal->row()->push_notification['key'])) {
                                            if ($driverVal->row()->push_notification['key'] != '') {
                                                $this->sendPushNotification($driverVal->row()->push_notification['key'], $message, 'payment_paid', 'ANDROID', $options, 'DRIVER');
                                            }
                                        }
                                    }
                                    if ($driverVal->row()->push_notification['type'] == 'IOS') {
                                        if (isset($driverVal->row()->push_notification['key'])) {
                                            if ($driverVal->row()->push_notification['key'] != '') {
                                                $this->sendPushNotification($driverVal->row()->push_notification['key'], $message, 'payment_paid', 'IOS', $options, 'DRIVER');
                                            }
                                        }
                                    }
                                }
                            }
                        }
                    }
					$this->app_model->update_ride_amounts($ride_id);
					$fields = array(
						'ride_id' => (string) $ride_id
					);
					$url = base_url().'prepare-invoice';
					$this->load->library('curl');
					$output = $this->curl->simple_post($url, $fields);
                }
            }
            $this->mobdata['payOption'] = 'ride payment';
            $this->load->view('v8/webview/success.php', $this->mobdata);
        } else {
            redirect('v8/webview/trip/failed/paymentfailed?mobileId=' . $this->mobdata['mobileId']);
        }
    }

	/**
	* 
	* Loading failed payment
	*
	**/
    public function failed() {
        $this->mobdata['errors'] = $this->uri->segment(5);
        $this->load->view('v8/webview/failed.php', $this->mobdata);
    }

    /**
    * 
    * Connecting back to mobile application
	*
    **/
    public function returns() {
        $this->mobdata['msg'] = $this->uri->segment(5);
        $this->clearPayData();
        $this->load->view('v8/webview/payment_return.php', $this->mobdata);
    }

	/**
	* 
	* Delete Payment Records
	*
	**/
    public function clearPayData() {
        $this->app_model->commonDelete(MOBILE_PAYMENT, array('_id' => MongoID($this->mobdata['mobileId'])));
    }

}

/* End of file payment.php */
/* Location: ./application/controllers/v8/api/payment.php */