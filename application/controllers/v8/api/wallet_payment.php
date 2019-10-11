<?php if (!defined('BASEPATH')) exit('No direct script access allowed');

/**
 * 
 * User Wallet Recharge related functions
 * @author Casperon
 *
 * */
class Wallet_payment extends MY_Controller {

    public $mobdata = array();

    function __construct() {
        parent::__construct();
        $this->load->helper(array('cookie', 'date', 'form', 'email'));
        $this->load->library(array('encrypt', 'form_validation'));
        $this->load->model('app_model');

        $this->mobdata['authorize_net_settings'] = $this->data['authorize_net_settings'];
        $this->mobdata['paypal_settings'] = $this->data['paypal_settings'];
        $this->mobdata['stripe_settings'] = $this->data['stripe_settings'];
        
        $lang_code = $this->input->get('lang');
		if($lang_code!=""){
			$this->load->helper('lg_helper');
			change_web_language($lang_code);
		}

    }

	/**
	* 
	* Loading Add wallet money payment option
	*
	**/
    public function add_pay_wallet_payment_form() {
        $user_id = $this->input->get('user_id');
        $total_amount = $this->input->get('total_amount');
        $transaction_id = time();
        $pay_date = date("Y-m-d H:i:s");

        if ($user_id == '' || $total_amount == '' || $total_amount < $this->config->item('wal_recharge_min_amount') || $total_amount > $this->config->item('wal_recharge_max_amount')) {
            $this->load->view('v8/webview/error.php', $this->mobdata);
            die;
        }

        $chckUser = $this->app_model->get_selected_fields(USERS, array('_id' => MongoID($user_id)), array('_id', 'stripe_customer_id'));
        if ($chckUser->num_rows() == 0) {
            $this->load->view('v8/webview/error.php', $this->mobdata);
            die;
        }

        $paydataArr = array('user_id' => $user_id, 'total_amount' => $total_amount, 'transaction_id' => $transaction_id, 'pay_date' => $pay_date, 'pay_status' => 'Pending');
        $this->app_model->simple_insert(WALLET_RECHARGE, $paydataArr);

        $trans_details = $this->app_model->get_all_details(WALLET_RECHARGE, array('transaction_id' => $transaction_id, 'user_id' => $user_id));
        $this->mobdata['trans_details'] = $trans_details;

        $this->mobdata['auto_charge'] = $this->data['auto_charge'];
        $this->mobdata['stripe_settings'] = $this->data['stripe_settings'];

        if ($this->data['auto_charge'] == 'Yes') {
			$this->load->view('v8/webview/wallet/stripe_payment_card.php', $this->mobdata);
        } else {
            $this->load->view('v8/webview/wallet/wallet_payment_option.php', $this->mobdata);
        }
    }

	/**
	* 
	* Payment Process using credit card
	*
	**/
    public function user_wal_PaymentCard() {
        $user_id = $this->input->post('user_id');
        $total_amount = $this->input->post('total_amount');
        $transaction_id = $this->input->post('transaction_id');
        $currency = $this->config->item('currency_code');

        if ($transaction_id != '' && $total_amount != '' && $user_id != '') {

            $userDetail = $this->app_model->get_selected_fields(USERS, array('_id' => MongoID($user_id)), array('user_name', 'email', 'phone_number'));

            $original_currency = 'USD';
			if($original_currency != $currency){
				$currencyval = $this->app_model->get_currency_value(round($total_amount, 2), $currency, $original_currency);
				if (!empty($currencyval)) {
					$total_amount = $currencyval['CurrencyVal'];
				}
			}
			
			$amount = $total_amount;
            define("AUTHORIZENET_API", $this->config->item('payment_0'));
            //Authorize.net Intergration
            $Auth_Details = unserialize(AUTHORIZENET_API);
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
                'first_name' => $userDetail->row()->user_name,
                'last_name' => '',
                'address' => '',
                'city' => '',
                'state' => '',
                'country' => '',
                'phone' => $userDetail->row()->phone_number,
                'email' => $userDetail->row()->email,
                'card_code' => $this->input->post('creditCardIdentifier')
                    )
            );

            #echo '<pre>'; print_r($transaction);die;			
            $response = $transaction->authorizeAndCapture();

            $pay_txn_id = $response->transaction_id;
            if ($pay_txn_id == '') {
                $pay_txn_id = $response->description;
            }

            if ($response->approved) {
                redirect('v8/webview/wallet/success/' . $user_id . '/' . $transaction_id . '/credit-card/' . $pay_txn_id);
            } else {
                redirect('v8/webview/wallet/failed/Error ' . $response->response_reason_text . '?mobileId=' . $transaction_id);
            }
        } else {
            redirect('v8/webview/wallet/pay-cancel?mobileId=' . $transaction_id);
        }
    }

	/**
	* 
	* Payment Process using Paypal
	*
	**/
    public function paypal_wal_payment_process() {
        $user_id = $this->input->post('user_id');
        $total_amount = $this->input->post('total_amount');
        $transaction_id = $this->input->post('transaction_id');
        $currency = $this->config->item('currency_code');

        if ($transaction_id != '' && $total_amount != '' && $user_id != '') {

            $amount = $total_amount;

            /* Paypal integration start */
            $this->load->library('paypal_class');

            define("PAYPAL_API", $this->config->item('payment_1'));
            $Paypal_Details = unserialize(PAYPAL_API);
            $Paypal_Setting_Details = $Paypal_Details['settings'];

            $paypalmode = $Paypal_Setting_Details['mode'];
            $paypalEmail = $Paypal_Setting_Details['merchant_email'];

            $item_name = $this->config->item('email_title') . ' wallet recharge : ' . $transaction_id;

            $original_grand_fare = 0;
            $original_currency = 'USD';
            $currencyval = $this->app_model->get_currency_value(round($amount, 2), $currency, $original_currency);
            if (!empty($currencyval)) {
                $original_grand_fare = $currencyval['CurrencyVal'];
            }
            $totalAmount = $original_grand_fare;

            $quantity = 1;

            if ($paypalmode == 'sandbox') {
                $this->paypal_class->paypal_url = 'https://www.sandbox.paypal.com/cgi-bin/webscr';   // testing paypal url
            } else {
                $this->paypal_class->paypal_url = 'https://www.paypal.com/cgi-bin/webscr';     // paypal url
            }
            $this->paypal_class->add_field('currency_code', $original_currency);

            $this->paypal_class->add_field('business', $paypalEmail); // Business Email

            $this->paypal_class->add_field('return', base_url() . 'v8/webview/wallet/success/' . $user_id . '/' . $transaction_id . '/paypal'); // Return URL

            $this->paypal_class->add_field('cancel_return', base_url() . 'v8/webview/wallet/failed/cancelled'); // Cancel URL

            #$this->paypal_class->add_field('notify_url', base_url() . 'v8/webview/wallet/paypal-payment-ipn'); // Notify url

            $this->paypal_class->add_field('custom', 'WalletRecharge|' . $user_id . '|' . $transaction_id); // Custom Values			

            $this->paypal_class->add_field('item_name', $item_name); // Product Name

            $this->paypal_class->add_field('user_id', $user_id);

            $this->paypal_class->add_field('quantity', $quantity); // Quantity

            $this->paypal_class->add_field('amount', $totalAmount); // Price

            $this->paypal_class->submit_paypal_post();
        } else {
            redirect('v8/webview/wallet/pay-cancel?mobileId=' . $transaction_id);
        }
    }

	/**
	* 
	* Complete the payment process through stripe pay and auto payment
	*
	**/
    public function stripe_payment_process() {
		$user_id = $this->input->post('user_id');
        $total_amount = $this->input->post('total_amount');
        $transaction_id = $this->input->post('transaction_id');
        $email = $this->input->post('stripeEmail');
		
        $stripe_customer_id = '';
		$stripe_customer_id = '';
		$auto_pay_status = 'No';
		$cardInfo = 'No';
		
        if($user_id != ''){
			$getUsrCond = array('_id' => MongoID($user_id));
			$get_user_info = $this->app_model->get_selected_fields(USERS, $getUsrCond, array('email', 'stripe_customer_id'));
			if(isset($get_user_info->row()->stripe_customer_id)) {
				$stripe_customer_id = $get_user_info->row()->stripe_customer_id;
			}
       
			if (isset($get_user_info->row()->stripe_customer_id)) {
				$stripe_customer_id = $get_user_info->row()->stripe_customer_id;
				if ($stripe_customer_id != '') {
					$chkCard = $this->get_stripe_card_details($stripe_customer_id);
					if($chkCard['error_status'] == '1'){
						$auto_pay_status = 'Yes';
						$cardInfo = 'Yes';
					} else {
						$this->app_model->update_details(USERS, array('stripe_customer_id' => ''), $getUsrCond);
					}
				}
			}
		}
      
	   $getUsrCond = array('_id' => MongoID($user_id));
		
		if($this->input->post('stripeToken') == '' && ($stripe_customer_id == '' || $cardInfo == 'No')){
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

        if ($user_id != '' && $cardInfo == 'Yes') {

            $getUsrCond = array('_id' => MongoID($user_id));
            $get_user_info = $this->app_model->get_selected_fields(USERS, $getUsrCond, array('email', 'stripe_customer_id'));
            if ($email == '') {
                $email = $get_user_info->row()->email;
            }

           

            if ($transaction_id == '') {
                $transaction_id = time();
                $pay_date = date("Y-m-d H:i:s");
                $paydataArr = array('user_id' => $user_id, 'total_amount' => $total_amount, 'transaction_id' => $transaction_id, 'pay_date' => $pay_date, 'pay_status' => 'Pending', 'payment_host' => 'web');
                $this->app_model->simple_insert(WALLET_RECHARGE, $paydataArr);
            }

            require_once('./stripe/lib/Stripe.php');

            $stripe_settings = $this->data['stripe_settings'];
            $secret_key = $stripe_settings['settings']['secret_key'];
            $publishable_key = $stripe_settings['settings']['publishable_key'];

            $stripe = array(
                "secret_key" => $secret_key,
                "publishable_key" => $publishable_key
            );

            $product_description = ucfirst($this->config->item('email_title')) . ' money - Wallet Recharge ';

            #echo '<pre>'; print_r($_POST);die;
            Stripe::setApiKey($secret_key);
            $token = $this->input->post('stripeToken');
			
			$currency = $this->data['dcurrencyCode'];
            $currency_status=$this->get_stripe_provide_currency($currency);
            if($currency_status) {
            $amounts = $this->get_stripe_currency_smallest_unit($total_amount,$currency);
            } else {
                $original_currency = 'USD';
                if($currency != $original_currency){
                    $currencyval = $this->app_model->get_currency_value(round($total_amount, 2), $currency, $original_currency);
                    if (!empty($currencyval)) { 
                    $amounts = round($currencyval['CurrencyVal']*100);}
                    $currency='USD';
                }
            }          
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
					$stripe_customer_id = '';
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
				
			    if($customer_id != ''){
					$this->app_model->update_details(USERS, array('stripe_customer_id' => $customer_id), $getUsrCond);
			    }


                // Charge the Customer instead of the card
                $charge = Stripe_Charge::create(array(
                            "amount" => $amounts, # amount in cents, again
                            "currency" => $currency,
                            "customer" => $customer_id,
                            "description" => $product_description)
                );

                if ($auto_pay_status == 'Yes') {

                    $paymentData = array('user_id' => $user_id, 'transaction_id' => $transaction_id, 'payType' => 'stripe', 'stripeTxnId' => $charge['id']);
                    $this->success('Auto', $paymentData);
                } else {
                    redirect(base_url() . 'v8/webview/wallet/success/' . $user_id . '/' . $transaction_id . '/stripe?stripeTxnId=' . $charge['id']);
                }
            } catch (Exception $e) {
                $error = $e->getMessage();
                if ($error == '') {
                    $error = 'Payment Cancelled';
                }
				$this->app_model->update_details(USERS, array('stripe_customer_id' => ''), $getUsrCond);
                if ($auto_pay_status == 'Yes') {

                    /*                     * *************  respond back to app ************ */
                    $returnArr['status'] = '0';
                    $message = $this->format_string('Transaction Failed', 'transaction_failed');
                    $returnArr['msg'] = $message . ', : ' . $error;
                    $json_encode = json_encode($returnArr);
                    echo $this->cleanString($json_encode);
                    die;
                } else {
                    redirect(base_url() . 'v8/webview/wallet/failed/' . $error . '?user_id=' . $user_id);
                }
            }
		} else {
			if($auto_pay_status == 'Yes'){
				$returnArr['status'] = '0';
				$message = $this->format_string('Invalid Payment Request');
				$returnArr['msg'] = $message;
				$json_encode = json_encode($returnArr);
				echo $this->cleanString($json_encode);
				die;
			} else {
				redirect(base_url() . 'v8/webview/wallet/failed/Invalid Payment Request?user_id=' . $user_id);
			}
        }
    }

	/**
	* 
	* Loading success payment
	*
	**/
    public function success($payMode = 'Manual', $paymentData = array()) {
        
        $user_id = $this->uri->segment(5);
        $transaction_id = $this->uri->segment(6);
        $payment_type = $this->uri->segment(7);
        $trans_id = $this->uri->segment(8);

        $payment_status = 'Completed';
        if ($payment_type == 'paypal') {
        
            $trans_id = $_REQUEST['txn_id'];
            
        }

        if ($payment_type == 'stripe') {
            $trans_id = $this->input->get('stripeTxnId');
        }

        if ($payMode == 'Auto') {
            $user_id = $paymentData['user_id'];
            $transaction_id = $paymentData['transaction_id'];
            $payment_type = $paymentData['payType'];
            $trans_id = $paymentData['stripeTxnId'];
        }


        $this->mobdata['trans_id'] = '';

        if ($payment_status == 'Completed') {
            $checkRecharge = $this->app_model->get_all_details(WALLET_RECHARGE, array('transaction_id' => floatval($transaction_id)));
            if ($checkRecharge->num_rows() == 1) {
                if ($checkRecharge->row()->pay_status == 'Pending') {

                    /**    update wallet * */
                    $total_amount = $checkRecharge->row()->total_amount;

                    /* Update the recharge amount to user wallet */
                    $this->app_model->update_wallet((string) $user_id, 'CREDIT', floatval($total_amount));
                    $currentWallet = $this->app_model->get_selected_fields(WALLET, array('user_id' => MongoID($user_id)), array('total'));
                    $avail_amount = 0.00;
                    if ($currentWallet->num_rows() > 0) {
                        if (isset($currentWallet->row()->total)) {
                            $avail_amount = floatval($currentWallet->row()->total);
                        }
                    }
                    $txn_time = time();
                    $initialAmt = array('type' => 'CREDIT',
                        'credit_type' => 'recharge',
                        'ref_id' => $payment_type,
                        'trans_amount' => floatval($total_amount),
                        'avail_amount' => floatval($avail_amount),
                        'trans_date' => MongoDATE($txn_time),
                        'trans_id' => $trans_id
                    );
                    $this->app_model->simple_push(WALLET, array('user_id' => MongoID($user_id)), array('transactions' => $initialAmt));
                    $this->app_model->commonDelete(WALLET_RECHARGE, array('transaction_id' => floatval($transaction_id)));
                    $rider_info = $this->app_model->get_selected_fields(USERS, array('_id' => MongoID($user_id)), array('user_name', 'email', 'phone_number'));

                    $this->load->model('mail_model');
                    $this->mail_model->wallet_recharge_successfull_notification($initialAmt, $rider_info, $txn_time, $transaction_id);
                    $this->mobdata['trans_id'] = $trans_id;
                }
            }
            $this->mobdata['payOption'] = 'wallet recharge';

            if ($payMode == 'Auto') {
                /*                 * *************  respond back to app ************ */
                $returnArr['status'] = '1';
                $returnArr['msg'] = $this->format_string('Transaction Successful','transaction_successs');
                $avail_amount = number_format($avail_amount,2);
                $returnArr['wallet_amount'] = $avail_amount;
                $json_encode = json_encode($returnArr);
                echo $this->cleanString($json_encode);
                die;
            } else {
                $this->load->view('v8/webview/success.php', $this->mobdata);
            }
        } else {
            /*             * *************  respond back to app ************ */
            if ($payMode == 'Auto') {
                $returnArr['status'] = '0';
                $returnArr['msg'] = $this->format_string('Transaction Failed', 'transaction_failed');
                $json_encode = json_encode($returnArr);
                echo $this->cleanString($json_encode);
                die;
            } else {
                redirect('v8/webview/wallet/failed/Error');
            }
        }
    }

	/**
	* 
	* Loading failed payment response
	*
	**/
    public function failed() {
        $this->mobdata['errors'] = $this->uri->segment(5);
        $this->mobdata['payOption'] = 'wallet recharge'; 
        $this->load->view('v8/webview/failed.php', $this->mobdata);
    }

	/**
	* 
	* Loading success payment response
	*
	**/
    public function returns() {
        $this->mobdata['msg'] = $this->uri->segment(5);
        $this->load->view('v8/webview/payment_return.php', $this->mobdata);
    }

}


/* End of file wallet_payment.php */
/* Location: ./application/controllers/v8/api/wallet_payment.php */
