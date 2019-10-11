<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

/**
 * 
 * This model contains all db functions related to SMS and Email Template management
 * @author Casperon
 *
 */
class Templates_model extends My_Model
{
	public function __construct()
	{
		parent::__construct();
	}
	/**
    * 
    * Getting mail id of Subscriber
    * @param String $data
    */
	public function send_mail_subcribers($data,$field='_id'){
		$this->mongo_db->select();
		if($field=='_id'){
			$datanew=$data;
			$data=array();
			$k=0;
			foreach($datanew as $key=>$value){
				$newdata[$k]=MongoID($value);
				$k++;
			}
		}
		$this->mongo_db->where_in('_id', $newdata);
		$res = $this->mongo_db->get(NEWSLETTER_SUBSCRIBER);
		return $result = $res->result_array();
	}
	/**
    * 
    * Getting newsletter Details
    * @param Constant $tablename
    * @param String $Data
    */
	public function get_newsletter_details($collection='',$data=''){
		$query =  $this->mongo_db->get($collection,$data);
		return $result = $query->result_array();
	}
	/**
    * 
    * To send mail Subscriber list
    * @param String $valuesArr
    * @param Integer $NewsId
    */
	public function send_mail_subcribers_list($valuesArr,$NewsId){
	
		$newsid=$NewsId->row()->news_id;
		$template_values = $this->get_email_template($newsid);
		$adminnewstemplateArr = array('mail_emailTitle' => $this->config->item('email_title'), 
									'mail_logo' => $this->config->item('logo_image'), 
									'mail_footerContent' => $this->config->item('footer_content'), 
									'mail_metaTitle' => $this->config->item('meta_title'), 
									'mail_contactMail' => $this->config->item('site_contact_mail')
									);
		
		extract($adminnewstemplateArr);
		$subject = 'From: '.$this->config->item('email_title').' - '.$template_values['subject'];
  		$message = '<!DOCTYPE HTML>
			<html>
			<head>
			<meta http-equiv="Content-Type" content="text/html; charset=utf-8">
			<meta name="viewport" content="width=device-width"/>
			<title>'.$template_values['subject'].'</title><body>';
			include($template_values['templateurl']);	
		$message .= '</body>
			</html>';
		
		 $sender_email = $this->config->item('site_contact_mail');
         $sender_name = $this->config->item('email_title');
        
		$sender_array = array();
		foreach($valuesArr as $SubscriberEmail){	
			if($SubscriberEmail['subscriber_email']!=''){
				$sender_array[] = $SubscriberEmail['subscriber_email'];
			}
		}
		if(!empty($sender_array)){
			$email_values = array('mail_type'=>'html',
							'from_mail_id'=>$sender_email,
							'mail_name'=>$sender_name,
							'bcc_mail_id'=>$sender_array,
							'subject_message'=>$subject,
							'body_messages'=>$message
							);
			#echo '<pre>'; print_r($email_values); die;
			$email_send_to_common = $this->common_email_send($email_values);
		}
	}
	
	
	/**
    * 
    * Getting Users details
    * @param String $condition
	*
    **/
   public function get_language_list(){
		$this->mongo_db->select();
		$this->mongo_db->order_by(array('name' => 'asc'));
		$res = $this->mongo_db->get(LANGUAGES);
		return $res;
   }
	
	
	
}

?>