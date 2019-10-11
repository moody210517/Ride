<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

class Demo extends MY_Controller {
	function __construct(){
        parent::__construct();
		$this->load->helper(array('cookie','date','form'));
		$this->load->library(array('encrypt','form_validation'));		
		$this->load->model(array('user_model'));
		$this->load->model(array('mail_model'));
		$this->load->model(array('app_model'));
		echo '<pre>';
    }
	public function index(){
		echo -8+10;
		echo phpinfo();
	}
	public function cls(){
		echo 'Sorry ! go and do you vealai.';
		/* $this->user_model->commonDelete(RIDES,array());
		$this->user_model->commonDelete(PAYMENTS,array());
		$this->user_model->commonDelete(WALLET,array());
		$this->user_model->commonDelete(WALLET_RECHARGE,array());
		$this->user_model->commonDelete(REFER_HISTORY,array());
		$this->user_model->commonDelete(USERS,array());
		$this->user_model->commonDelete(USER_LOCATION,array());
		$this->user_model->commonDelete(STATISTICS,array());
		$this->user_model->commonDelete(FAVOURITE,array());
		$this->user_model->commonDelete(PROMOCODE,array());
		$this->user_model->commonDelete(DRIVERS,array()); 
		$this->user_model->commonDelete(TRANSACTION,array());   
		$this->user_model->commonDelete(BILLINGS,array());
		$this->user_model->commonDelete(RIDE_STATISTICS,array());
		$this->user_model->commonDelete(RIDE_HISTORY,array());
		$this->user_model->commonDelete(TRAVEL_HISTORY,array());
		$this->user_model->commonDelete(TRACKING,array());
		$this->user_model->commonDelete(DRIVERS_MILEAGE,array());
		$this->user_model->commonDelete(REPORTS,array());
		$this->user_model->commonDelete(PAYMENT_TRANSACTION,array());
		
		
		$this->user_model->commonDelete(LOCATIONS,array());
		$this->user_model->commonDelete(BANNER,array());
		$this->user_model->commonDelete(VEHICLES,array());
		$this->user_model->commonDelete(BRAND,array());
		$this->user_model->commonDelete(MODELS,array());
		$this->user_model->commonDelete(CANCELLATION_REASON,array());
		$this->user_model->commonDelete(REVIEW_OPTIONS,array());
		$this->user_model->commonDelete(REFER_HISTORY,array()); */
	}
	public function currency_api(){
		#$headers = array('Host:'.urlencode(base_url()).'','Authkey:CASPERON-API-SERVICES','AuthSource: CABILY');
		$headers = array('Authkey:CASPERON-API-SERVICES','AuthSource: CABILY');
		$url = "http://192.168.1.76/cabily-api/v1/api/currency/converter?amount=150&from=INR&to=USD";
		$ch = curl_init();
		curl_setopt($ch, CURLOPT_URL, $url);
		curl_setopt($ch, CURLOPT_POST, true);
		curl_setopt($ch, CURLOPT_HTTPHEADER, $headers);
		curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
		curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, 0);
		curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
		$result = curl_exec($ch);
		if ($result === FALSE) {
			die('Curl failed: ' . curl_error($ch));
		}
		curl_close($ch);

		var_dump($result);
	}
    
    function inv(){
        $fields = array(
            'ride_id' => (string) $ride_id
        );
        $url = base_url().'prepare-invoice';
        $this->load->library('curl');
        $output = $this->curl->simple_post($url, $fields);
    }
    
    
    function rv(){
        $rides = $this->app_model->get_selected_fields(RIDES,array('commission_percent' => array('$gt' => 100)),array('_id','ride_id'));
        echo '<pre>'; print_r($rides->result());
        foreach($rides->result() as $rd){
            $this->app_model->update_details(RIDES,array('commission_percent' => floatval(100),'driver_revenue' => floatval(0)),array('ride_id' => $rd->ride_id));
        }
    }
    
    public function update_driver_onlineMOde(){ 
		 $this->user_model->update_details(DRIVERS,array('availability' => 'Yes','mode' => 'Available','last_active_time'=>MongoDATE(time()),'loc' => array('lon' => floatval('80.254201'),'lat' => floatval('13.058977'))),array('email' => 'AROCKIASAMY@casperon.in'));
         echo 'done';
         
         $this->user_model->update_details(DRIVERS,array('availability' => 'Yes','mode' => 'Available','last_active_time'=>MongoDATE(time()),'loc' => array('lon' => floatval('80.213521'),'lat' => floatval('13.058977'))),array('email' => 'sureshkumar@casperon.in'));
         echo 'done';
        
        $this->user_model->update_details(DRIVERS,array('availability' => 'Yes','mode' => 'Available','last_active_time'=>MongoDATE(time()),'loc' => array('lon' => floatval('80.216628'),'lat' => floatval('13.004261'))),array('email' => 'demoios@casperon.in'));
         echo 'done';
        
        $this->user_model->update_details(DRIVERS,array('availability' => 'Yes','mode' => 'Available','last_active_time'=>MongoDATE(time()),'loc' => array('lon' => floatval('80.210397'),'lat' => floatval('13.034689'))),array('email' => 'cabilyteam@teamtweaks.com'));
         echo 'done';
         
         $this->user_model->update_details(DRIVERS,array('availability' => 'Yes','mode' => 'Available','last_active_time'=>MongoDATE(time()),'loc' => array('lon' => floatval('80.233692'),'lat' => floatval('13.040503'))),array('email' => 'suresh@gmail.com'));
         echo 'done';
    
        $this->user_model->update_details(DRIVERS,array('availability' => 'Yes','mode' => 'Available','last_active_time'=>MongoDATE(time()),'loc' => array('lon' => floatval('80.251479'),'lat' => floatval('13.050529'))),array('email' => 'suresh@casperon.in'));
        echo 'done';
        
        
	}
	
	function cMap(){
		$this->load->helper('ride_helper');
		create_and_save_travel_path_in_map('995115');
	}
	
}

/* End of file demo.php */
/* Location: ./application/controllers/demo.php */