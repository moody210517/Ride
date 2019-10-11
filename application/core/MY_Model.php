<?php 
if (!defined('BASEPATH')) exit('No direct script access allowed');

/**
* 
* This model contains all common db related functions
* @author Casperon
*
* */
class My_Model extends CI_Model {

    /**
    * 
    * This function connect the database and load the functions from CI_Model
    *
    * */
    public function __construct() {
        parent::__construct();
    }

    /**
    *
    * This functions returns all the collection details using @param 
    * @param String $collection
    * @param Array $sortArr
    * @param Array $condition
    * @param Numeric $limit
    * @param Numeric $offset
    * @param Array $likearr
    *
    * */
    public function get_all_details($collection, $condition = array(), $sortArr = array(), $limit = FALSE, $offset = FALSE, $likearr = array()) {

  
        $this->mongo_db->select();
        if (!empty($condition)) {
            $this->mongo_db->where($condition);
        }
        if (!empty($likearr)) {
            if (count($likearr) > 0) {
                foreach ($likearr as $key => $val) {
                    $this->mongo_db->or_like($key, $val);
                }
            } else {
                $this->mongo_db->like($key, $val);
            }
        }
        if ($sortArr != '' && is_array($sortArr) && !empty($sortArr)) {
            $this->mongo_db->order_by($sortArr);
        }
        if ($limit !== FALSE && is_numeric($limit) && $offset !== FALSE && is_numeric($offset)) {
            $this->mongo_db->limit($limit);
            $this->mongo_db->offset($offset);
        }


            
        $res = $this->mongo_db->get($collection);


        return $res;

        
    }

    /**
     *
     * This functions returns all the collection details using @param 
     * @param String $collection
     * @param Array $sortArr
     * @param Array $fields
     * @param Array $condition
     * @param Numeric $limit
     * @param Numeric $offset
     * @param Array $likearr
     *
     * */
    public function get_selected_fields($collection, $condition = array(), $fields = array(), $sortArr = array(), $limit = FALSE, $offset = FALSE, $likearr = array()) {
        $this->mongo_db->select($fields);
        if (!empty($condition)) {
            $this->mongo_db->where($condition);
        }
        if (!empty($likearr)) {
            if (count($likearr) > 0) {
                foreach ($likearr as $key => $val) {
                    $this->mongo_db->or_like($key, $val);
                }
            } else {
                $this->mongo_db->like($key, $val);
            }
        }
        if ($sortArr != '' && is_array($sortArr) && !empty($sortArr)) {
            $this->mongo_db->order_by($sortArr);
        }
        if ($limit !== FALSE && is_numeric($limit) && $offset !== FALSE && is_numeric($offset)) {
            $this->mongo_db->limit($limit);
            $this->mongo_db->offset($offset);
        } 
        $res = $this->mongo_db->get($collection);
        return $res;
    }

    /**
     * 
     * This function do all insert and edit operations
     * @param String $collection	   -->	Collection name
     * @param String $mode		   -->	Insert, Update
     * @param Array $excludeArr	   -->   To avoid post inputs
     * @param Array $dataArr         -->   Add additional inputs with posted inputs
     * @param Array $condition      -->  Applicable only for updates
     *
     * */
    public function commonInsertUpdate($collection = '', $mode = '', $excludeArr = '', $dataArr = '', $condition = '') {
        $inputArr = array();
        foreach ($this->input->post() as $key => $val) {
            if (!in_array($key, $excludeArr)) {
                if (is_numeric($val)) {
                    $inputArr[$key] = floatval($val);
                } else {
                    $inputArr[$key] = $val;
                }
            }
        }
        $finalArr = array_merge($inputArr, $dataArr);
		
        if ($mode == 'insert') {
            return $this->mongo_db->insert($collection, $finalArr);
        } else if ($mode == 'update') {
            $this->mongo_db->where($condition);
            $this->mongo_db->set($finalArr);
            return $this->mongo_db->update($collection);
        }
    }

    /**
     * 
     * Simple function for inserting data into a collection
     * @param String $collection
     * @param Array $data
     *
     * */
    public function simple_insert($collection = '', $data = '') {
        return $this->mongo_db->insert($collection, $data);
    }

	/**
	*
	* This functions updates the collection details using @param 
	* @param String $collection
	* @param Array $data
	* @param Array $condition
	*
	* */
    public function update_details($collection = '', $data = '', $condition = '') {
        if (!empty($collection)) {
            $this->mongo_db->where($condition);
            $this->mongo_db->set($data);
            return $this->mongo_db->update_all($collection);
        }
    }

    /**
     * 
     * This function deletes the document based upon the condition
     * @param String $collection
     * @param Array $condition
     * */
    public function commonDelete($collection = '', $condition = '') {
        $this->mongo_db->where($condition);
        return $this->mongo_db->delete_all($collection);
        
    }

    /**
     *
     * Common function for executing mongoDB query
     * @param String $Query	->	mongoDB Query
     *
     * */
    public function ExecuteQuery($Query) {
        $res = $this->mongo_db->command($Query);
        return $res;
    }

    /**
     *
     * Common function for get last inserted _id
     *
     * */
    public function get_last_insert_id() {
        $last_insert_id = $this->mongo_db->insert_id();
        return $last_insert_id;
    }

    /**
     *
     * Get newsletter templates details
     * @param Interger $news_id
     *
     * */
    public function get_newsletter_template_details($news_id = '') {
        $this->mongo_db->select();
        if ($news_id != '') {
            $this->mongo_db->where(array('news_id' => (int) $news_id));
        }
        $res = $this->mongo_db->get(NEWSLETTER);
        return $res->row();
    }

    /**
     * 
     * This function change the status of records and delete the records
     * @param String $collection
     * @param String $field
     * 
     * */
    public function activeInactiveCommon($collection = '', $field = '', $delete = TRUE) {
        $data = $_POST['checkbox_id'];
        $mode = $this->input->post('statusMode');
        for ($i = 0; $i <= count($data); $i++) {
            if ($data[$i] == 'on') {
                unset($data[$i]);
            }
        }
        if ($field == '_id') {
            $datanew = $data;
            $data = array();
            $k = 0;
            foreach ($datanew as $key => $value) {
                $data[$k] = MongoID($value);
                $k++;
            }
        }
        $newdata = array_values($data);
        $this->mongo_db->where_in($field, $newdata);
        if (strtolower($mode) == 'delete') {
            if ($delete === TRUE) {
                $this->mongo_db->delete_all($collection);
            } else if ($delete === FALSE) {
                $statusArr = array('status' => 'Deleted');
                $this->mongo_db->set($statusArr);
                $this->mongo_db->update_all($collection);
            }
        } else {
            $statusArr = array('status' => $mode);
            $this->mongo_db->set($statusArr);
            $this->mongo_db->update_all($collection);
        }
    }

    /**
     * 
     * Common select base on the where in conditions
     *
     * @param $condition = array('field','where_in Array');
     */
    public function get_selected_fields_where_in($collection, $conditionArr = array(), $fields = array(), $sortArr = array(), $limit = FALSE, $offset = FALSE, $likearr = array()) {
        $this->mongo_db->select($fields);

        if (!empty($conditionArr)) {
            $field = $conditionArr[0];
            $data = $conditionArr[1];
            $condition = $conditionArr[2];

            if (!empty($condition)) {
                $this->mongo_db->where($condition);
            }
            if ($field != '' && !empty($data)) {
                if ($field == '_id') {
                    $datanew = $data;
                    $data = array();
                    $k = 0;
                    foreach ($datanew as $key => $value) {
                        $data[$k] = MongoID($value);
                        $k++;
                    }
                }
                $newdata = array_values($data);
                $this->mongo_db->where_in($field, $newdata);
            }
        }

        if (!empty($likearr)) {
            if (count($likearr) > 0) {
                foreach ($likearr as $key => $val) {
                    $this->mongo_db->or_like($key, $val);
                }
            } else {
                $this->mongo_db->like($key, $val);
            }
        }
        if ($sortArr != '' && is_array($sortArr) && !empty($sortArr)) {
            $this->mongo_db->order_by($sortArr);
        }
        if ($limit !== FALSE && is_numeric($limit) && $offset !== FALSE && is_numeric($offset)) {
            $this->mongo_db->limit($limit);
            $this->mongo_db->offset($offset);
        } 
        $res = $this->mongo_db->get($collection);
        
        return $res;
    }

    /**
     * 
     * Common Email send funciton 
     * @param Array $eamil_vaues
     * @return 1
     *
     */
    public function common_email_send($eamil_vaues = array()) {
        $server_ip = $this->input->ip_address();
        $mail_id = 'set';
#echo '<pre>'; print_r($eamil_vaues); die;
        if ($mail_id != '') {
            if (is_file('./commonsettings/dectar_smtp_settings.php')) {
                include('commonsettings/dectar_smtp_settings.php');
            }
            // Set SMTP Configuration
            if ($config['smtp_user'] != '' && $config['smtp_pass'] != '') {
                $emailConfig = array(
                    'protocol' => 'smtp',
                    'smtp_host' => $config['smtp_host'],
                    'smtp_port' => $config['smtp_port'],
                    'smtp_user' => $config['smtp_user'],
                    'smtp_pass' => $config['smtp_pass'],
                    'auth' => true,
                );
            }

            // Set your email information
            $from = array('email' => $eamil_vaues['from_mail_id'], 'name' => $eamil_vaues['mail_name']);
            $to = $eamil_vaues['to_mail_id'];
            $subject = $eamil_vaues['subject_message'];
            $message = stripslashes($eamil_vaues['body_messages']);
#echo "<pre>"; echo $message; die;
            // Load CodeIgniter Email library   ##  iso-8859-1
            if ($config['smtp_user'] != '' && $config['smtp_pass'] != '') {
                $this->load->library('email', $emailConfig);
            } else {
                $headers = 'MIME-Version: 1.0' . "\r\n";
                $headers .= 'Content-type: text/html; charset=utf-8' . "\r\n";
                // Additional headers
                $headers .= 'From: ' . $eamil_vaues['mail_name'] . ' <' . $eamil_vaues['from_mail_id'] . '>' . "\r\n";
                if (array_key_exists('cc_mail_id', $eamil_vaues)) {
                    if ($eamil_vaues['cc_mail_id'] != '') {
                        $headers .= 'Cc: ' . $eamil_vaues['cc_mail_id'] . "\r\n";
                    }
                }
                if (array_key_exists('bcc_mail_id', $eamil_vaues)) {
                    if ($eamil_vaues['bcc_mail_id'] != '') {
                        $headers .= 'Bcc: ' . $eamil_vaues['bcc_mail_id'] . "\r\n";
                    }
                }

                // Mail it
                mail($eamil_vaues['to_mail_id'], trim(stripslashes($eamil_vaues['subject_message'])), trim(stripslashes($eamil_vaues['body_messages'])), $headers);
                return 1;
            }

            // Sometimes you have to set the new line character for better result
            $this->email->set_newline("\r\n");
            // Set email preferences
            $this->email->set_mailtype($eamil_vaues['mail_type']);
            $this->email->from($from['email'], $from['name']);
            $this->email->to($to);
            if (array_key_exists('cc_mail_id', $eamil_vaues)) {
                if ($eamil_vaues['cc_mail_id'] != '') {
                    $this->email->cc($eamil_vaues['cc_mail_id']);
                }
            }
            if (array_key_exists('bcc_mail_id', $eamil_vaues)) {
                if ($eamil_vaues['bcc_mail_id'] != '') {
                    $this->email->bcc($eamil_vaues['bcc_mail_id']);
                }
            }
            $this->email->subject($subject);
            $this->email->message($message);
            if (!empty($eamil_vaues['attachments'])) {
                foreach ($eamil_vaues['attachments'] as $attach) {
                    if ($attach != '') {
                        $this->email->attach($attach);
                    }
                }
            }
            // Ready to send email and check whether the email was successfully sent;

            if (!$this->email->send()) {
                $headers = 'MIME-Version: 1.0' . "\r\n";
                $headers .= 'Content-type: text/html; charset=utf-8' . "\r\n";
                // Additional headers
                $headers .= 'From: ' . $eamil_vaues['mail_name'] . ' <' . $eamil_vaues['from_mail_id'] . '>' . "\r\n";
                if (array_key_exists('cc_mail_id', $eamil_vaues)) {
                    if ($eamil_vaues['cc_mail_id'] != '') {
                        $headers .= 'Cc: ' . $eamil_vaues['cc_mail_id'] . "\r\n";
                    }
                }
                if (array_key_exists('bcc_mail_id', $eamil_vaues)) {
                    if ($eamil_vaues['bcc_mail_id'] != '') {
                        $headers .= 'Bcc: ' . $eamil_vaues['bcc_mail_id'] . "\r\n";
                    }
                }

                // Mail it
                mail($eamil_vaues['to_mail_id'], trim(stripslashes($eamil_vaues['subject_message'])), trim(stripslashes($eamil_vaues['body_messages'])), $headers);
                return 1;
            } else {
                // Show success notification or other things here
                //echo 'Success to send email';
                return 1;
            }
        } else {
            return 1;
        }
    }

    /**
     * 
     * This function return the admin settings details
     *
     * */
    public function getAdminSettings() {
        $this->mongo_db->select();
        $this->mongo_db->where(array('admin_id' => '1'));
        $result = $this->mongo_db->get(ADMIN);
        unset($result->row()->admin_password);
        return $result;
    }

    /**
     * 
     * This function return the count of particular records
     * @param String $collection
     * @param Array $condition
     * @param Array $filterarr
     *
     * */
    public function get_all_counts($collection = '', $condition = array(), $filterarr = array(), $limit = FALSE, $offset = FALSE) {
        if (!empty($condition)) {
            $this->mongo_db->where($condition);
        }
        if (!empty($filterarr)) {
            if (count($filterarr) > 0) {
                foreach ($filterarr as $key => $val) {
                    $this->mongo_db->or_like($key, $val);
                }
            } else {
                $this->mongo_db->like($key, $val);
            }
        }
        if ($limit !== FALSE && is_numeric($limit) && $offset !== FALSE && is_numeric($offset)) {
            $this->mongo_db->limit($limit);
            $this->mongo_db->offset($offset);
        }
        return $this->mongo_db->count($collection);
    }

    /**
     * 
     * This function push the data in to a field
     * @param String $collection
     * @param Array $condition
     * @param Array/String $pushdata
     *
     * */
    public function simple_push($collection = '', $condition = array(), $pushdata = array()) {
        if (!empty($condition)) {
            $this->mongo_db->where($condition);
        }
        $this->mongo_db->push($pushdata);
        return $this->mongo_db->update_all($collection);
    }

    /**
     * 
     * This function removes the data in a field
     * @param String $collection
     * @param Array $condition
     * @param Array/String $pushdata
     *
     * */
    public function simple_pull($collection = '', $condition = array(), $pulldata, $value = array()) {
        if (!empty($condition)) {
            $this->mongo_db->where($condition);
        }
        if (is_array($pulldata)) {
            foreach ($pulldata as $field => $value) {
                $this->mongo_db->pull($field, $value);
            }
        } elseif (is_string($pulldata)) {
            $this->mongo_db->pull($pulldata, $value);
        }
        return $this->mongo_db->update_all($collection);
    }

    /**
     * 
     * This function add to set data in a field
     * @param String $collection
     * @param Array $condition
     * @param Array $setdata
     *
     * */
    public function set_to_field($collection = '', $condition = array(), $setdata = array()) {
        if (!empty($condition)) {
            $this->mongo_db->where($condition);
        }
        if (is_array($setdata)) {
            $this->mongo_db->set($setdata);
        }
        return $this->mongo_db->update_all($collection);
    }

    /**
     * 
     * This function calculate the distance between two lat lon
     * @param String $lat1
     * @param String $lon1
     * @param String $lat2
     * @param String $lon2
     * @param String $unit (M=>Miles,K=>Kilometers,N=>Nautical Miles)
     *
     * */
    public function geoDistance($lat1, $lon1, $lat2, $lon2, $unit = 'K') {
        $theta = $lon1 - $lon2;
        $dist = sin(deg2rad($lat1)) * sin(deg2rad($lat2)) + cos(deg2rad($lat1)) * cos(deg2rad($lat2)) * cos(deg2rad($theta));
        $dist = acos($dist);
        $dist = rad2deg($dist);
        $miles = $dist * 60 * 1.1515;
        $unit = strtoupper($unit);

        if ($unit == "K") {
            return ($miles * 1.609344);
        } else if ($unit == "N") {
            return ($miles * 0.8684);
        } else {
            return $miles;
        }
    }

    /**
     * 
     * This function calculate the ETA (return in minutes)
     * @param String $distance km
     * @param String $speed in kmh
     *
     * */
    public function calculateETA($distance, $speed = 20) {
        $time = ($distance / $speed) * 60;
        if ($time > 0) {
            $eta = ceil($time);
            $eta = intval($eta);
        } else {
            $eta = 0;
        }
        return $eta;
    }

    /**
     * 
     * This function update the statistics information
     * @param Array $condition
     * @param String/Array $field
     * @param Numeric $value
     *
     * */
    public function update_stats($condition = '', $field, $value = 1) {
        $this->mongo_db->select(array('day_hour'));
        $this->mongo_db->where($condition);
        $res = $this->mongo_db->get(STATISTICS);
        if ($res->num_rows() > 0) {
            if (!empty($condition)) {
                $this->mongo_db->where($condition)->inc($field, $value)->update(STATISTICS);
            }
        } else {
            $this->mongo_db->insert(STATISTICS, $condition);
        }
    }

    /**
     * 
     * This function generate the ride id
     *
     * */
    public function get_ride_id() {
		$digits = 6;
		$ride_id = str_pad(rand(0, pow(10, $digits)-1), $digits, '0', STR_PAD_LEFT);
		
        $condition = array('ride_id' => $ride_id);

        $this->mongo_db->select(array('ride_id'));
        $this->mongo_db->where($condition);
        $res = $this->mongo_db->get(RIDES);
        if ($res->num_rows() > 0) {
            $check = 0;
            $ride_id = str_pad(rand(0, pow(10, $digits)-1), $digits, '0', STR_PAD_LEFT);
            while ($check == 0) {
                $condition = array('ride_id' => $ride_id);
                $duplicate_id = $this->get_all_details(RIDES, $condition);
                if ($duplicate_id->num_rows() > 0) {
                    $ride_id = str_pad(rand(0, pow(10, $digits)-1), $digits, '0', STR_PAD_LEFT);
                } else {
                    $check = 1;
                }
            }
        }
        return $ride_id;
    }

    /**
     * 
     * This function generate the random string
     *
     * */
    public function get_random_string($length = 6) {
        #$random_string = substr(str_shuffle("0123456789abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ"), 0, $length);
        $six_digit_random_number = mt_rand(100000, 999999);
        return $six_digit_random_number;
    }
    
    public function get_random_number($length = 6) {
        $six_digit_random_number = mt_rand(100000, 999999);
        return $six_digit_random_number;
    }

    /**
     * 
     * This function generate the unique id
     *
     * */
    public function get_unique_id($user_name = '', $length = 7) {
        if ($user_name == '') {
            $unique_code = substr(str_shuffle("0123456789abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ"), 0, $length);
        } else {
            $unique_code = preg_replace('/[^A-Za-z0-9\-\']/', '', $user_name);
            $unique_code.= time();
            $unique_code = substr($unique_code, 0, $length);
        }
        $condition = array('unique_code' => strtoupper($unique_code));

        $this->mongo_db->select(array('unique_code'));
        $this->mongo_db->where($condition);
        $res = $this->mongo_db->get(USERS);
        if ($res->num_rows() > 0) {
            $check = 0;
            if ($user_name == '') {
                $unique_code = substr(str_shuffle("0123456789abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ"), 0, $length);
            } else {
                $unique_code = preg_replace('/[^A-Za-z0-9\-\']/', '', $user_name);
                $unique_code.= time();
                $unique_code = substr($unique_code, 0, $length);
            }
            while ($check == 0) {
                $condition = array('unique_code' => strtoupper($unique_code));
                $duplicate_id = $this->get_all_details(USERS, $condition);
                if ($duplicate_id->num_rows() > 0) {
                    $unique_code = time() + rand(0000, 999999);
                } else {
                    $check = 1;
                }
            }
        }
        $unique_code = substr($unique_code, 0, $length);
        return strtoupper($unique_code);
    }

    /**
     * 
     * This function update the total wallet amount
     * @param String $user_id
     * @param Numeric $amount
     *
     * */
    public function update_wallet($user_id = '', $type = '', $amount = 0) {
		if($amount < 0){
			$amount = 0;
		}
        if ($user_id != '' && $amount >= 0) {
            if ($type == 'CREDIT') {
                $this->mongo_db->where(array('user_id' => MongoID($user_id)))->inc('total', $amount)->update(WALLET);
            } else if ($type == 'DEBIT') {
                $this->mongo_db->where(array('user_id' => MongoID($user_id)))->set(array('total' => $amount))->update(WALLET);
            }
        }
		if ($user_id!=""){
			$condition = array('user_id' => MongoID($user_id));
			$get_user_wallet = $this->get_selected_fields(WALLET, $condition,array("total"));
			$wallet_amount = 0.00;
			if ($get_user_wallet->num_rows() > 0) {
				if(isset($get_user_wallet->row()->total)) $wallet_amount = round($get_user_wallet->row()->total,2);
			}
			$this->mongo_db->where(array('_id' => MongoID($user_id)))->set(array('wallet_amount' =>floatval($wallet_amount)))->update(USERS);
		}
			
    }

    /**
     * 
     * This function gets the current currency conversion value
     * @param String $from
     * @param String $to
     * @param Numeric $value
     *
     * */
    public function get_currency_value($value = 1, $from, $to = 'USD') {
        $gCurrencyVal = floatval($this->currencyget->currency_conversion($value, $from, $to));
        $gCurrencyRev = floatval($this->currencyget->currency_conversion($value, $to, $from));
        if ($gCurrencyVal == 0) {
            $CurrencyVal = 1;
            $CurrencyRev = 1;
        } else {
            $CurrencyVal = $gCurrencyVal;
            $CurrencyRev = $gCurrencyRev;
        }
        $currencyValArr = array('CurrencyVal' => round($CurrencyVal, 2), 'CurrencyRev' => round($gCurrencyRev, 2));
        return $currencyValArr;
    }

    /**
    *
    * get ids from device 
    *
    */
    public function get_user_ids_from_device($collection, $data = array(), $field = '') {
        $this->mongo_db->select(array('_id','messaging_status',$field));
        $this->mongo_db->where_in($field, $data);
        $res = $this->mongo_db->get($collection);
        return $res;
    }
    /**
    *
    * get email template
    *
    */
    public function get_email_template($newsid,$langcode='') {
	
		if($langcode==''){
			$langcode = $this->mailLang;
		}
    
        $email_data=$this->get_newsletter_template_details($newsid);
		
		$sender_email = '';
		$sender_name = '';
       
        $data=array();
        if($langcode!='en')
        {
         $templateurl=FCPATH.DIRECTORY_SEPARATOR.'newsletter'.DIRECTORY_SEPARATOR.'template' . $newsid . '_'.$langcode.'.php';
        
         
          if(!file_exists($templateurl))
          {
            $subject=$email_data->message['subject'];
            if(isset($email_data->sender['name']))  $sender_name = $email_data->sender['name'];
            if(isset($email_data->sender['email'])) $sender_email = $email_data->sender['email'];
            $templateurl=FCPATH.DIRECTORY_SEPARATOR.'newsletter'.DIRECTORY_SEPARATOR.'template' . $newsid .'.php';
            
            $data=array("subject"=>$subject,"sender_name"=>$sender_name,"sender_email"=>$sender_email,"templateurl"=>$templateurl);
           
          }
          else{
             
             $lang_details = $email_data->$langcode;
             $subject= $lang_details['email_subject'];
              if(isset($email_data->sender['name'])) $sender_name =  $lang_details['sender_name'];
              if(isset($email_data->sender['email']))  $sender_email =  $lang_details['sender_email'];
             $templateurl=FCPATH.DIRECTORY_SEPARATOR.'newsletter'.DIRECTORY_SEPARATOR.'template' . $newsid . '_'.$langcode.'.php';
             $data=array("subject"=>$subject,"sender_name"=>$sender_name,"sender_email"=>$sender_email,"templateurl"=>$templateurl);
          }
        }
        else
        {
            $subject=$email_data->message['subject'];
             if(isset($email_data->sender['name'])) $sender_name = $email_data->sender['name'];
             if(isset($email_data->sender['email']))  $sender_email = $email_data->sender['email'];
            $templateurl=FCPATH.DIRECTORY_SEPARATOR.'newsletter'.DIRECTORY_SEPARATOR.'template' . $newsid .'.php';
            
            $data=array("subject"=>$subject,"sender_name"=>$sender_name,"sender_email"=>$sender_email,"templateurl"=>$templateurl);
        }
       
         return $data;
          
    }
		
    /**
    *
    * get invoice template
    *
    */
    public function get_invoice_template($langcode='',$shareType='normal') {
	
        if($langcode==''){
            $langcode = $this->mailLang;
        }

        $email_data=$this->get_invoice_template_details($shareType);
        
        $invoice_name = 'invoice_template';
        if($shareType == 'pool'){
            $invoice_name = 'share_pool_invoice_template';
        }
        
        $data=array();
        if($langcode!='en'){
        $templateurl=FCPATH.'invoice'.DIRECTORY_SEPARATOR.$invoice_name.'_'.$langcode.'.php';
    
            $sender_name =  $this->config->item('email_title');
            $sender_email =  $lang_details['site_contact_mail'];
            if(!file_exists($templateurl)){
                $subject=$email_data->message['subject'];
                $templateurl=FCPATH.'invoice'.DIRECTORY_SEPARATOR.$invoice_name.'.php';
        
                $data=array("subject"=>$subject,"sender_name"=>$sender_name,"sender_email"=>$sender_email,"templateurl"=>$templateurl);
       
            } else {
         
              $lang_details = $email_data->$langcode;
                $subject=  $lang_details['site_contact_mail'];
      
              $templateurl=FCPATH.'invoice'.DIRECTORY_SEPARATOR.$invoice_name.'_'.$langcode.'.php';
              $data=array("subject"=>$subject,"sender_name"=>$sender_name,"sender_email"=>$sender_email,"templateurl"=>$templateurl);
            }
        } else {
            $subject=$email_data->message['subject'];
            $sender_name = $this->config->item('email_title');
            $sender_email = $this->config->item('email');
            $templateurl=FCPATH.'invoice'.DIRECTORY_SEPARATOR.$invoice_name.'.php';
        
            $data=array("subject"=>$subject,"sender_name"=>$sender_name,"sender_email"=>$sender_email,"templateurl"=>$templateurl);
        }
   
      return $data;
          
    }
		
    /**
    * 
    * function used to update the ride amount in driver end and site end
    *
    */
    public function update_ride_amounts($ride_id = '') {
		if($ride_id != ''){
			$ride_info_detail = $this->get_selected_fields(RIDES,array('ride_id'=>$ride_id),array('total','ride_status','pay_status','pay_summary','ride_id','pool_ride','driver','user','booking_information','history','driver_review_status','ratings','driver_revenue'));
			if($ride_info_detail->num_rows()==1){
				$amount_in_site = 0;
				$amount_in_driver = 0;
				$pay_type = '';
				$tips_amount = 0;

				$amount_in_site = $ride_info_detail->row()->total['wallet_usage'];
				if (isset($ride_info_detail->row()->pay_summary['type'])) {
					$pay_type = $ride_info_detail->row()->pay_summary['type'];
				}
				
				if(isset($ride_info_detail->row()->total['tips_amount'])){
					$tips_amount = $ride_info_detail->row()->total['tips_amount'];
				}
				
				$total_amount = $ride_info_detail->row()->total['grand_fare'] + $tips_amount;
				
				if ($pay_type == '') {
					$pay_type = 'FREE';
				}
				$siteArray = array('Gateway', 'Wallet_Gateway','FREE','Wallet');
				$driverArray = array('Cash', 'Wallet_Cash');
				
				if (in_array($pay_type, $siteArray)) {
					$amount_in_site = $amount_in_site + $ride_info_detail->row()->total['paid_amount'];
					if($ride_info_detail->row()->total['grand_fare'] >= $ride_info_detail->row()->total['wallet_usage']){
						$amount_in_site = $amount_in_site + $tips_amount;
					}
				}
				$ride_type="Normal";
				if(isset($ride_info_detail->row()->pool_ride)){
					if($ride_info_detail->row()->pool_ride=="Yes"){
						$ride_type = "Share";
					}
				}
				$CI =& get_instance();
				$driver_id = $ride_info_detail->row()->driver['id'];
				$driverVal = $this->get_selected_fields(DRIVERS, array('_id' => MongoID($driver_id)), array('_id', 'push_notification','duty_ride'));							
				if($ride_type=="Share"){
					$curr_duty_ride = "";
					if(isset($driverVal->row()->duty_ride)){
						if($driverVal->row()->duty_ride!="") $curr_duty_ride = $driverVal->row()->duty_ride;
					}
					$checkAvailRide = $this->get_driver_active_trips($driver_id,$curr_duty_ride,"Share");
					$active_trips = 0;
					if($checkAvailRide->num_rows()>0){
						$active_trips = intval($checkAvailRide->num_rows());
					}
					if($active_trips>=1){
						$cUser = $ride_info_detail->row()->user["id"];
						foreach($checkAvailRide->result() as $passanger){
							$ext_user_id = $passanger->user["id"];
							$curRide_id = $passanger->ride_id;
							if($ext_user_id!="" && $ext_user_id!=$cUser){
								
								$extUserVal = $this->get_selected_fields(USERS, array('_id' => MongoID($ext_user_id)), array('_id','push_type','push_notification_key'));
								if ($extUserVal->num_rows() > 0) {
									if (isset($extUserVal->row()->push_type)) {
										if ($extUserVal->row()->push_type != '') {
											$message = $CI->format_string('Your trip information has been updated', 'trip_info_updated','','user',(string)$extUserVal->row()->_id);						
											$optionsFExt = array('ride_id' => $curRide_id);
											if ($extUserVal->row()->push_type == 'ANDROID') {
												if (isset($extUserVal->row()->push_notification_key['gcm_id'])) {
													if ($extUserVal->row()->push_notification_key['gcm_id'] != '') {
														$CI->sendPushNotification($extUserVal->row()->push_notification_key['gcm_id'], $message, 'track_reload', 'ANDROID', $optionsFExt, 'USER');
													}
												}
											}
											if ($extUserVal->row()->push_type == 'IOS') {
												if (isset($extUserVal->row()->push_notification_key['ios_token'])) {
													if ($extUserVal->row()->push_notification_key['ios_token'] != '') {
														$CI->sendPushNotification($extUserVal->row()->push_notification_key['ios_token'], $message, 'track_reload', 'IOS', $optionsFExt, 'USER');
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
				if(in_array($pay_type, $driverArray)) {
					$tot_fare = $ride_info_detail->row()->total['grand_fare']+ $tips_amount;
					$amount_in_driver = $ride_info_detail->row()->total['paid_amount'];
					if($tot_fare == $ride_info_detail->row()->total['paid_amount']){
						if($ride_info_detail->row()->total['grand_fare'] < $ride_info_detail->row()->total['paid_amount']){
							if($pay_type != 'Cash'){
								$amount_in_driver = $amount_in_driver + $tips_amount;
							}
						}
					}
				}
                $ride_type='Normal Ride';
				if(isset($record->ride_type) && $record->ride_type!='') {
					$ride_type=$record->ride_type;
				}
				if(isset($record->pool_ride) && $record->pool_ride=='Yes') {
					$ride_type = 'Share Ride';
				}
                $driver_ratting=0;
                if(isset($ride_info_detail->row()->driver_review_status) && $ride_info_detail->row()->driver_review_status=='Yes') {
                    $driver_ratting=$ride_info_detail->row()->ratings['driver']['avg_rating'];
                }
                $dataArr=array('ride_id'=>$ride_id,
                               'ride_type'=>$ride_type,
                               'booking_date'=>MongoDATE(MongoEPOCH($ride_info_detail->row()->history['booking_time'])),
                               'end_date'=>MongoDATE(MongoEPOCH($ride_info_detail->row()->history['end_ride'])),
                               'activity_time'=>MongoDATE(MongoEPOCH($ride_info_detail->row()->history['end_ride'])),
                               'category'=>$ride_info_detail->row()->booking_information['service_type'],
                               'ratting'=>(string)$driver_ratting,
                               'driver_earning'=>floatval($ride_info_detail->row()->driver_revenue),
                               'payment_method'=>$pay_type,
                               'driver_id'=>$ride_info_detail->row()->driver['id'],
                               'activity'=>'ride'
                              );
                $this->simple_insert(DRIVERS_ACTIVITY,$dataArr);
				$this->load->helper('statistics_helper');
				save_driver_stats($driver_id,'Completed');
				#if($total_amount == ($amount_in_site+$amount_in_driver)){}
				$update_arr = array('amount_detail'=>array('total_amount'=>$total_amount,
														'amount_in_site'=>$amount_in_site,
														'amount_in_driver'=>$amount_in_driver
														)
									);
				$this->update_details(RIDES,$update_arr,array('ride_id'=>$ride_id));
				
			}
		}
	}
	
	/**
	*
	* Get invoice templates details
	* @param Interger $news_id
	*
	**/
	public function get_invoice_template_details($shareType='normal') {
        $this->mongo_db->select();
        if($shareType == 'pool'){
            $res = $this->mongo_db->get(SHARE_POOL_INVOICE);
        } else {
            $res = $this->mongo_db->get(INVOICE);
        }
        return $res->row();
	}
    public function update_drivers_online_duration($checkDriver,$new_status) {
        $returnArr['status'] = '0';
        if($checkDriver->num_rows() == 1){
            $driver_id = $checkDriver->row()->_id;
            $todayStart = strtotime(date('Y-m-d 00:00:00')); 
            $LOT = $todayStart;
            if(isset($checkDriver->row()->last_online_time)) $LOT = MongoEPOCH($checkDriver->row()->last_online_time);
            $condition = array('driver_id' => $driver_id,'record_id' => (string)$todayStart);
            
            $current_status = 'No';
            if(isset($checkDriver->row()->availability)) $current_status = $checkDriver->row()->availability;
            
            $tdyDuration = 0;
            if(isset($checkDriver->row()->today_online_duration)) $tdyDuration = $checkDriver->row()->today_online_duration;
            
            if($current_status != $new_status){
                
                $cTime = time();
                if($new_status == 'No'){
                    $TDLOT = $LOT;
                    if(date('Y-m-d',$LOT) != date('Y-m-d')){
                        $TDLOT = date('Y-m-d 00:00:00');
                    }
                    $LDuration = round(($cTime - $TDLOT)/60);
                    $tdyDuration+= $LDuration;
                    $dataArr = array('today_online_duration' => floatval($tdyDuration));
                    $this->update_details(DRIVERS,$dataArr,array('_id' => $driver_id));
                }   
            
                $getToday = $this->get_all_details(DRIVERS_ONLINE_DURATION,$condition);
                
                $tdyHistory = array();
                $total_duration = 0;
                $duration = 0;
                $hact_key = 0;
                if(isset($getToday->row()->total_duration)) $total_duration = $getToday->row()->total_duration;
                if(isset($getToday->row()->hact_key)) $hact_key = $getToday->row()->hact_key;
                
                if(isset($getToday->row()->history)){
                    $tdyHistory = $getToday->row()->history;
                    if(isset($tdyHistory[$hact_key]['in_time']) && $new_status == 'No'){
                        $duration = round(($cTime - MongoEPOCH($tdyHistory[$hact_key]['in_time'])/60));
                        $total_duration+= $duration;
                    }
                }
                
                $dataArr = array('driver_id' => $driver_id,
                                 'record_date' => MongoDATE($todayStart),
                                 'record_id' => (string)$todayStart,
                                 'hact_key' => floatval($hact_key),
                                 'total_duration' => floatval(0),  // in minutes
                                 'history' => array(array('in_time' => MongoDATE($cTime),'out_time' => ''))
                            );
                
                if($new_status == 'Yes'){
                    if($getToday->num_rows() == 0){
                        $this->simple_insert(DRIVERS_ONLINE_DURATION,$dataArr);
                    } else {
                        $history = array('in_time' => MongoDATE($cTime),'out_time' => '');
                        $this->simple_push(DRIVERS_ONLINE_DURATION,$condition,array('history' => $history));
                        $this->update_details(DRIVERS_ONLINE_DURATION,array('hact_key' => floatval($hact_key+1)),$condition);
                    }
                } else if($new_status == 'No'){ 
                    if($getToday->num_rows() == 0){
                    
                        if(date('Y-m-d',$LOT) == date('Y-m-d',$todayStart)){
                            $duration = round(($cTime - $LOT)/60);
                            $dataArr['history'] = array(array('in_time' => MongoDATE($LOT),
                                                               'out_time' => MongoDATE($cTime),
                                                               'duration' => floatval($duration)
                                                        )
                                                    );
                            $dataArr['total_duration'] = floatval($duration);
                            $this->simple_insert(DRIVERS_ONLINE_DURATION,$dataArr); 
                        } else {
                            $datediff = $cTime - $LOT;
                            $btwDays = round($datediff / (60 * 60 * 24));
                            for($i=$btwDays; $i >= 0; $i--){
                                
                                $rowStartDate = strtotime(date('Y-m-d 00:00:00',strtotime("-$i days")));
                                $rowEndDate = strtotime(date('Y-m-d 23:59:59',strtotime("-$i days")));
                                $chkcond = array('driver_id' => $driver_id,'record_id' => (string)$rowStartDate);
                                $chkRecord = $this->get_all_details(DRIVERS_ONLINE_DURATION,$chkcond);
                                
                                $total_duration = 0;
                                $hact_key = 0;
                                if(isset($chkRecord->row()->total_duration)) $total_duration = $chkRecord->row()->total_duration;
                                if(isset($chkRecord->row()->hact_key)) $hact_key = $chkRecord->row()->hact_key;
                                $history = array();
                                $rowHistory = array();
                                if(isset($chkRecord->row()->history)){
                                    $rowHistory = $chkRecord->row()->history;
                                }
                                
                                $rowDataArr = array();
                                
                                if(date('Y-m-d',$LOT) == date('Y-m-d',strtotime("-$i days"))){  // row in last online time 
                                    $in_time = strtotime(date('Y-m-d H:i:s',$LOT));
                                    $out_time = strtotime(date('Y-m-d 23:59:59',$LOT));
                                    
                                    $duration = round(($out_time - $LOT)/60);
                                    if($chkRecord->num_rows() > 0){ 
                                        if(isset($rowHistory[$hact_key]['out_time']) && $rowHistory[$hact_key]['out_time'] == ''){
                                            $rowDataArr['history.'.$hact_key.'.out_time'] = MongoDATE($out_time);
                                        }
                                        $rowDataArr['history.'.$hact_key.'.duration'] = floatval($duration);
                                        $total_duration+= $duration;
                                    } else {
                                        $total_duration = $duration;
                                        $history = array(array('in_time' => MongoDATE($in_time),
                                                     'out_time' => MongoDATE($out_time),
                                                     'duration' => floatval($total_duration)
                                                )); 
                                    }
                                } else if(date('Y-m-d') == date('Y-m-d',strtotime("-$i days"))){  // row in today 
                                    $in_time = strtotime(date('Y-m-d 00:00:00'));
                                    $out_time = strtotime(date('Y-m-d H:i:s',$cTime));
                                   
                                    if($chkRecord->num_rows() > 0){ 
                                        if(isset($chkRecord->row()->history)){
                                            if(isset($rowHistory[$hact_key]['in_time']))
                                            $duration = round(($out_time - $rowHistory[$hact_key]['in_time'])/60);
                                            if(isset($rowHistory[$hact_key]['out_time']) && $rowHistory[$hact_key]['out_time'] == ''){
                                                $rowDataArr['history.'.$hact_key.'.out_time'] = MongoDATE($out_time);
                                                $rowDataArr['history.'.$hact_key.'.duration'] = floatval($duration);
                                            }
                                            $total_duration+= $duration ;
                                        }
                                    } else {
                                        $total_duration = round(($out_time - $in_time)/60);
                                        $history = array(array('in_time' => MongoDATE($in_time),
                                                     'out_time' => MongoDATE($out_time),
                                                     'duration' => floatval($total_duration)
                                                ));
                                    }
                                } else {    // row in middle days
                                
                                    $total_duration = 1440;
                                    $history = array(array('in_time' => MongoDATE($rowStartDate),
                                                     'out_time' => MongoDATE($rowEndDate),
                                                     'duration' => floatval($total_duration)
                                                    ));
                                    if($chkRecord->num_rows() > 0){
                                        $rowDataArr['history.'.$hact_key.'.in_time'] = MongoDATE($rowStartDate);
                                        $rowDataArr['history.'.$hact_key.'.out_time'] = MongoDATE($rowEndDate);
                                        $rowDataArr['history.'.$hact_key.'.duration'] = floatval($total_duration);
                                    }
                                }
                                
                                if($chkRecord->num_rows() == 0){
                                    $rowInputData = array('driver_id' => $driver_id,
                                                     'record_date' => MongoDATE($rowStartDate),
                                                     'record_id' => (string)$rowStartDate,
                                                     'hact_key' => floatval($hact_key),
                                                     'total_duration' => floatval($total_duration),  // in minutes
                                                     'history' => $history
                                                );
                                    $this->simple_insert(DRIVERS_ONLINE_DURATION,$rowInputData);
                                } else { 
                                    $rowDataArr['total_duration'] = floatval($total_duration);
                                    $rowDataArr['hact_key'] = floatval($hact_key+1);
                                    $this->update_details(DRIVERS_ONLINE_DURATION,$rowDataArr,$chkcond); 
                                    #echo '<pre>'; print_r($rowDataArr); die;
                                }
                            } 
                        } 
                    } else { 
                        if(isset($tdyHistory[$hact_key]['out_time']) && $tdyHistory[$hact_key]['out_time'] == ''){
                            $inputArr['history.'.$hact_key.'.out_time'] = MongoDATE($cTime);
                            $inputArr['history.'.$hact_key.'.duration'] = floatval($duration);
                            $inputArr['total_duration'] = floatval($total_duration); 
                            $this->update_details(DRIVERS_ONLINE_DURATION,$inputArr,$condition);
                        }
                    }
                }
                $returnArr['status'] = '1';
            }
        }
        return $returnArr['status'];
	}
	

}
?>