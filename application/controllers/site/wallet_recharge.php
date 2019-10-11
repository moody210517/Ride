<?php

if (!defined('BASEPATH'))
    exit('No direct script access allowed');

/**
 * 
 * User related functions
 * @author Casperon
 *
 * */
class Wallet_recharge extends MY_Controller {

    public $mobdata = array();

    function __construct() {
        parent::__construct();
        $this->load->helper(array('cookie', 'date', 'form', 'email'));
        $this->load->library(array('encrypt', 'form_validation'));
        $this->load->model('wallet_model');
		
		if($this->checkLogin('U') == ''){
			$this->setErrorMessage('error', 'Sorry, You must login first');
            redirect('rider/login');
		}
    }

    /**
     * 
     * Loading Add wallet money payment option
     *
     * */
    public function add_pay_wallet_payment_form() {
        $user_id = $this->checkLogin('U');
        $total_amount = $this->input->post('total_amount');
        $transaction_id = time();
        $pay_date = date("Y-m-d H:i:s");

        if ($user_id == '' || $total_amount == '') {
			
			$this->setErrorMessage('error', 'Sorry, recharge amount should not be empty', 'driver_recharge_amount_empty');
            redirect('rider/my-money');

        }
		
		if($total_amount < $this->config->item('wal_recharge_min_amount') || $total_amount > $this->config->item('wal_recharge_max_amount')) {
			
			$this->setErrorMessage('error', 'Recharge amount should be between the limit', 'ride_amount_between_limit');
			redirect('rider/my-money');
		}

        $chckUser = $this->wallet_model->get_selected_fields(USERS, array('_id' => MongoID($user_id)), array('_id'));
        if ($chckUser->num_rows() == 0) {
            $this->setErrorMessage('error', 'Sorry, User records not available', 'driver_user_record_not_avail');
            redirect('rider/my-money');
        }

        $paydataArr = array('user_id' => $user_id, 'total_amount' => $total_amount, 'transaction_id' => $transaction_id, 'pay_date' => $pay_date, 'pay_status' => 'Pending', 'payment_host' => 'web');
        $this->wallet_model->simple_insert(WALLET_RECHARGE, $paydataArr);

        $trans_details = $this->wallet_model->get_all_details(WALLET_RECHARGE, array('transaction_id' => $transaction_id, 'user_id' => $user_id));
        $this->data['trans_details'] = $trans_details;
        $this->data['rider_info'] = $this->user_model->get_all_details(USERS, array('_id' => MongoID($this->checkLogin('U'))));
		
		$this->data['heading'] = 'Wallet Recharge - Payment Form';
		
		$this->data['sideMenu'] = 'wallet';
		
        if ($this->data['auto_charge'] == 'Yes') {
            $this->load->view('site/wallet_recharge/stripe_payment.php', $this->data);
        } else {
            $this->load->view('site/wallet_recharge/wallet_payment_option.php', $this->data);
        }
    }

    /**
     * 
     * Payment Process using credit card
     *
     * */
    public function user_wal_PaymentCard() {
        $user_id = $this->input->post('user_id');
        $total_amount = $this->input->post('total_amount');
        $transaction_id = $this->input->post('transaction_id');
        $currency = $this->config->item('currency_code');
        #echo '<pre>'; print_r($_POST); die;
        if ($transaction_id != '' && $total_amount != '' && $user_id != '') {

            $userDetail = $this->wallet_model->get_selected_fields(USERS, array('_id' => MongoID($user_id)), array('user_name', 'email', 'phone_number'));
			
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


            $response = $transaction->authorizeAndCapture();
            #echo '<pre>'; print_r($response); echo '<pre>'; print_r($_REQUEST); die;		

            $pay_txn_id = $response->transaction_id;
            if ($pay_txn_id == '') {
                $pay_txn_id = $response->description;
            }

            if ($response->approved) {
                redirect('rider/wallet-recharge/success/' . $user_id . '/' . $transaction_id . '/credit-card/' . $pay_txn_id);
            } else {
                redirect('rider/wallet-recharge/failed/Error ' . $response->response_reason_text . '?mobileId=' . $transaction_id . '&user_id=' . $user_id);
            }
        } else {
            redirect('rider/wallet-recharge/pay-cancel?mobileId=' . $transaction_id . '&user_id=' . $user_id);
        }
    }

    /**
     * 
     * Payment Process using Paypal
     *
     * */
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


            if ($this->lang->line('driver_wallet_recharge') != '')
                $driver_wallet_recharge = str_replace('{SITENAME}', $this->config->item('email_title'), stripslashes($this->lang->line('driver_wallet_recharge')));
            else
                $driver_wallet_recharge = $this->config->item('email_title') . " wallet recharge";

            $item_name = $driver_wallet_recharge . '  : ' . $transaction_id;

			$totalAmount = $amount;
            $original_currency = 'USD';
			if($currency != $original_currency){
				$currencyval = $this->wallet_model->get_currency_value(round($amount, 2), $currency, $original_currency);
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

            $this->paypal_class->add_field('return', base_url() . 'rider/wallet-recharge/success/' . $user_id . '/' . $transaction_id . '/paypal'); // Return URL

            $this->paypal_class->add_field('cancel_return', base_url() . 'rider/wallet-recharge/failed/cancelled?user_id=' . $user_id); // Cancel URL

            $this->paypal_class->add_field('notify_url', base_url() . 'paypal-payment-ipn'); // Notify url

            $this->paypal_class->add_field('custom', 'WalletRecharge|' . $user_id . '|' . $transaction_id); // Custom Values			

            $this->paypal_class->add_field('item_name', $item_name); // Product Name

            $this->paypal_class->add_field('user_id', $user_id);

            $this->paypal_class->add_field('quantity', $quantity); // Quantity

            $this->paypal_class->add_field('amount', $totalAmount); // Price

            $this->paypal_class->submit_paypal_post();
        } else {
            redirect('rider/wallet-recharge/pay-cancel?mobileId=' . $transaction_id . '&user_id=' . $user_id);
        }
    }

    /**
     * 
     * Complete the payment process through stripe pay and auto payment
     *
     * */
    public function stripe_payment_process() {

        $user_id = $this->input->post('user_id');
        $total_amount = $this->input->post('total_amount');
        $transaction_id = $this->input->post('transaction_id');
        $email = $this->input->post('stripeEmail');
		
		
		/* Convert ride currency value to default currency  */
			
			/* $paymentCurr = 'USD';
			$currency = $this->data['dcurrencyCode'];
			if($currency != 'USD'){
				$get_dcurrency=$this->app_model->get_currency_value($total_amount,$currency,$paymentCurr);
				$total_amount = $get_dcurrency['CurrencyVal'];
			} */
			
		/************************************************/

        $getUsrCond = array('_id' => MongoID($user_id));
        $get_user_info = $this->wallet_model->get_selected_fields(USERS, $getUsrCond, array('email', 'stripe_customer_id'));
        if ($email == '') {
            $email = $get_user_info->row()->email;
        }

        $stripe_customer_id = '';
        if (isset($get_user_info->row()->stripe_customer_id)) {
            $stripe_customer_id = $get_user_info->row()->stripe_customer_id;
        }

        if ($transaction_id == '') {
            $transaction_id = time();
            $pay_date = date("Y-m-d H:i:s");
            $paydataArr = array('user_id' => $user_id, 'total_amount' => $total_amount, 'transaction_id' => $transaction_id, 'pay_date' => $pay_date, 'pay_status' => 'Pending', 'payment_host' => 'web');
            $this->wallet_model->simple_insert(WALLET_RECHARGE, $paydataArr);
        }

        require_once('./stripe/lib/Stripe.php');

        $stripe_settings = $this->data['stripe_settings'];
        $secret_key = $stripe_settings['settings']['secret_key'];
        $publishable_key = $stripe_settings['settings']['publishable_key'];

        $stripe = array(
            "secret_key" => $secret_key,
            "publishable_key" => $publishable_key
        );


        if ($this->lang->line('driver_money_wallet_recharge') != '')
            $product_description = str_replace('{SITENAME}', $this->config->item('email_title'), stripslashes($this->lang->line('driver_money_wallet_recharge')));
        else
            $product_description = ucfirst($this->config->item('email_title')) . ' money - Wallet Recharge ';


        #echo '<pre>'; print_r($_POST);die;
        Stripe::setApiKey($secret_key);
        $token = $this->input->post('stripeToken');
		
		$currency = $this->data['dcurrencyCode'];
		$amounts = $this->get_stripe_currency_smallest_unit($total_amount,$currency);
	   
        try {
            // Create a Customer

            $customer_id = $stripe_customer_id;
            if ($customer_id == '') {
                $customer = Stripe_Customer::create(array(
                            "card" => $token,
                            "description" => $product_description,
                            "email" => $email)
                );
                $customer_id = $customer->id;
            }

            $this->wallet_model->update_details(USERS, array('stripe_customer_id' => $customer_id), $getUsrCond);


            // Charge the Customer instead of the card
            $charge = Stripe_Charge::create(array(
                        "amount" => $amounts, # amount in cents, again
                        "currency" => $currency,
                        "customer" => $customer_id,
                        "description" => $product_description)
            );

            redirect(base_url() . 'rider/wallet-recharge/success/' . $user_id . '/' . $transaction_id . '/stripe?stripeTxnId=' . $charge['id']);
        } catch (Exception $e) {
            $error = $e->getMessage();
			$this->wallet_model->update_details(USERS, array('stripe_customer_id' => ''), $getUsrCond);
            redirect(base_url() . 'rider/wallet-recharge/failed/' . $error . '?user_id=' . $user_id);
        }
    }

    /**
     * 
     * Loading success payment
     *
     * */
    public function pay_success() {
        $user_id = $this->uri->segment(4);
        $transaction_id = $this->uri->segment(5);
        $payment_type = $this->uri->segment(6);
        $trans_id = $this->uri->segment(7);

        
        if ($payment_type == 'paypal') {
            $trans_id = $_REQUEST['txn_id'];
            $payment_status = $_REQUEST['payment_status'];
        }
		$payment_status = 'Completed';
        if ($payment_type == 'stripe') {
            $trans_id = $this->input->get('stripeTxnId');
        }



        $this->data['rider_info'] = $rider_info = $this->user_model->get_all_details(USERS, array('_id' => MongoID($user_id)));

        if ($payment_status == 'Completed') {
            $checkRecharge = $this->wallet_model->get_all_details(WALLET_RECHARGE, array('transaction_id' => floatval($transaction_id)));
            if ($checkRecharge->num_rows() == 1) {
                if ($checkRecharge->row()->pay_status == 'Pending') {

                    /**    update wallet * */
                    $total_amount = $checkRecharge->row()->total_amount;

                    /* Update the recharge amount to user wallet */
                    $this->wallet_model->update_wallet((string) $user_id, 'CREDIT', floatval($total_amount));
                    $currentWallet = $this->wallet_model->get_selected_fields(WALLET, array('user_id' => MongoID($user_id)), array('total'));
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


                    $this->wallet_model->simple_push(WALLET, array('user_id' => MongoID($user_id)), array('transactions' => $initialAmt));
                    $this->wallet_model->commonDelete(WALLET_RECHARGE, array('transaction_id' => floatval($transaction_id)));
                    $this->load->model('mail_model');
                    $this->mail_model->wallet_recharge_successfull_notification($initialAmt, $rider_info, $txn_time, $transaction_id);

                    $this->data['trans_id'] = $trans_id;
                }
            }
            $this->data['payOption'] = 'wallet recharge';
            $this->load->view('site/wallet_recharge/success.php', $this->data);
        } else {
            redirect('rider/wallet-recharge/failed/Error?user_id=' . $user_id);
        }
    }

    /**
     * 
     * Loading failed payment
     */
    public function pay_failed() {
        $this->data['errors'] = $this->uri->segment(4);
        $this->data['payOption'] = 'wallet recharge';
        if ($this->checkLogin('U') != '') {
            $this->data['rider_info'] = $this->user_model->get_all_details(USERS, array('_id' => MongoID($this->checkLogin('U'))));
            $this->load->view('site/wallet_recharge/failed.php', $this->data);
        } else {
            redirect('rider/login');
        }
    }

    /**
     * 
     * Loading cancelled payment by rider
     */
    public function payment_cancel() {

        if ($this->lang->line('driver_recharge_cancelled_by_you') != '')
            $this->data['errors'] = stripslashes($this->lang->line('driver_recharge_cancelled_by_you'));
        else
            $this->data['errors'] = 'Recharge Cancelled By You';
        $this->data['payOption'] = 'wallet recharge';
        $this->data['rider_info'] = $this->user_model->get_all_details(USERS, array('_id' => MongoID($this->checkLogin('U'))));
        $this->load->view('site/wallet_recharge/failed.php', $this->data);
    }

    public function payment_return() {
        $this->data['msg'] = $this->uri->segment(4);
        $this->data['rider_info'] = $this->user_model->get_all_details(USERS, array('_id' => MongoID($this->checkLogin('U'))));
        $this->load->view('site/wallet_recharge/payment_return.php', $this->data);
    }

}
