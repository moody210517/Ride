<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');
/**
* 
* This model contains all db functions related to Payment gateway management
* @author Casperon
*
**/
 
class Payment_gateway_model extends My_Model{
	public function __construct(){
		parent::__construct();
	}
	/**
    * 
    * This function save the payment settings in a file
    */
   public function savePaymentSettings(){
		$getPaymentSettings = $this->get_all_details(PAYMENT_GATEWAY,array(),array('gateway_number'=>'ASC')); 
		#echo '<pre>'; print_r($getPaymentSettings->result()); die;
		$config = '<?php ';
		foreach($getPaymentSettings->result_array() as $key => $val){
			unset($val['_id']); 
			$gateway_number = intval($val['gateway_number'])-1;
			$value = serialize($val);
			$config .= "\n\$config['payment_$gateway_number'] = '$value'; ";
		}
		$config .= ' ?>';
		$file = 'commonsettings/dectar_payment_settings.php';
		file_put_contents($file, $config);
   }
}