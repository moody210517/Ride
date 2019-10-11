<?php

if (!defined('BASEPATH'))
    exit('No direct script access allowed');
/*
 *
 * This model contains all db functions related to mail sending
 * @author Casperon
 *
 */

class Mail_model extends My_Model {

    public function __construct() {
        parent::__construct();
    }

    /**
     * 
     * This function send the email to registred user regarding registration 
     * 	@Param String $user_id
     * */
    public function send_user_registration_mail($user_id = '') {
        if ($user_id != '') {
            $checkUser = $this->get_selected_fields(USERS, array('_id' => MongoID($user_id)), array('email', 'user_name', 'unique_code'));
            if ($checkUser->num_rows() > 0) {
                $newsid = '3';
				#$template_values = $this->get_newsletter_template_details($newsid);
				$template_values = $this->get_email_template($newsid);
				$adminnewstemplateArr = array('mail_emailTitle' => $this->config->item('email_title'), 
											'mail_logo' => $this->config->item('logo_image'), 
											'mail_footerContent' => $this->config->item('footer_content'), 
											'mail_metaTitle' => $this->config->item('meta_title'), 
											'mail_contactMail' => $this->config->item('site_contact_mail'),
											'mail_referalCode' => $checkUser->row()->unique_code
											);
				extract($adminnewstemplateArr);
                $subject = $this->config->item('email_title') . ' - ' . $template_values['subject'];
               
                $message = '<!DOCTYPE HTML>
					<html>
					<head>
					<meta http-equiv="Content-Type" content="text/html; charset=utf-8">
					<meta name="viewport" content="width=device-width"/>
					<title>' . $template_values['subject'] . '</title>
					<body>';
					include($template_values['templateurl']);
					$message .= '</body>
					</html>';
				 
				 $sender_name = $this->config->item('email_title');
                 $sender_email = $this->config->item('site_contact_mail');
              
				  $email_values = array('mail_type' => 'html',
                    'from_mail_id' => $sender_email,
                    'mail_name' => $sender_name,
                    'to_mail_id' => $checkUser->row()->email,
                    'subject_message' => $template_values['subject'],
                    'body_messages' => $message
                );
                $email_send_to_common = $this->common_email_send($email_values);
            }
        }
    }

    /**
    * 
    * This function send the email to  drivers for registration confirmation
    * 	@Param String $user_id
    * */
    public function send_driver_register_confirmation_mail($driver_id = '') {
        if ($driver_id != '') {
            $checkUser = $this->get_selected_fields(DRIVERS, array('_id' => MongoID($driver_id)), array('email', 'driver_name'));
            if ($checkUser->num_rows() > 0) {
                $newsid = '8';
                $template_values = $this->get_email_template($newsid);
                $subject = $this->config->item('email_title') . ' - ' . $template_values['subject'];
                $mailtemplateValues = array('mail_emailTitle' => $this->config->item('email_title'),
                    'mail_logo' => $this->config->item('logo_image'),
                    'mail_footerContent' => $this->config->item('footer_content'),
                    'mail_metaTitle' => $this->config->item('meta_title'),
                    'mail_contactMail' => $this->config->item('site_contact_mail')
                );
                extract($mailtemplateValues);
                $message = '<!DOCTYPE HTML>
					<html>
					<head>
					<meta http-equiv="Content-Type" content="text/html; charset=utf-8">
					<meta name="viewport" content="width=device-width"/>
					<title>' . $subject . '</title>
					<body>';
                include($template_values['templateurl']);
                $message .= '</body>
					</html>';
					
					$sender_email = $this->config->item('site_contact_mail');
                    $sender_name = $this->config->item('email_title');
                
                $email_values = array('mail_type' => 'html',
                    'from_mail_id' => $sender_email,
                    'mail_name' => $sender_name,
                    'to_mail_id' => $checkUser->row()->email,
                    'subject_message' => $subject,
                    'body_messages' => $message
                );
                $email_send_to_common = $this->common_email_send($email_values);
            }
        }
    }
	
	//start company email notification
    public function send_company_registration_mail($user_id = '',$password,$company_name='') {  
        if ($user_id != '') {
            $checkUser = $this->get_selected_fields(COMPANY,array('_id' => MongoID($user_id)), array('email', 'user_name', 'address'));
            if ($checkUser->num_rows() > 0) { 
                $newsid = '18';
				$template_values = $this->get_email_template($newsid);
				$adminnewstemplateArr = array('mail_emailTitle' => $this->config->item('email_title'), 
											'mail_logo' => $this->config->item('logo_image'), 
											'mail_footerContent' => $this->config->item('footer_content'), 
											'mail_metaTitle' => $this->config->item('meta_title'), 
											'mail_contactMail' => $this->config->item('site_contact_mail'),
											 'mail_email' => $checkUser->row()->email,
											 'mail_password'=>$password
											);
				extract($adminnewstemplateArr);
                $subject = $this->config->item('email_title') . ' - ' . $template_values['subject'];
               
                $message = '<!DOCTYPE HTML>
					<html>
					<head>
					<meta http-equiv="Content-Type" content="text/html; charset=utf-8">
					<meta name="viewport" content="width=device-width"/>
					<title>' . $template_values['subject'] . '</title>
					<body>';
					include($template_values['templateurl']);
					$message .= '</body>
					</html>';
				 
				 $sender_name = $template_values['sender_name'];
                 $sender_email = $template_values['sender_email'];
                if ($template_values['sender_name'] == '' && $template_values['sender_email'] == '') {
                    $sender_email = $this->config->item('site_contact_mail');
                    $sender_name = $this->config->item('email_title');
                }
				
                $email_values = array('mail_type' => 'html',
                    'from_mail_id' => $sender_email,
                    'mail_name' => $sender_name,
                    'to_mail_id' => $checkUser->row()->email,
                    'subject_message' => $template_values['subject'],
                    'body_messages' => $message
                );
				
                $email_send_to_common = $this->common_email_send($email_values);
            }
        }
    }

    /**
     * 
     * This function send the email invoice
     * 	@Param String $ride_id
     * 	@Param String $email
     * */
    public function send_invoice_mail($ride_id = '', $email = '', $langcode = '') {
        if ($ride_id != '') {
            $ride_info = $this->user_model->get_all_details(RIDES, array('ride_id' => $ride_id));						 
            if ($ride_info->num_rows() == 1) {
                if ($email == '') {
                    $email = $ride_info->row()->booking_information['booking_email'];
                }
                if ($ride_info->row()->summary['ride_distance'] > $ride_info->row()->fare_breakup['min_km']) {
                        $after_min_distance = $ride_info->row()->summary['ride_distance'] - $ride_info->row()->fare_breakup['min_km'];
                } else {
                        $after_min_distance = 0;
                }
                if ($ride_info->row()->summary['ride_duration'] > $ride_info->row()->fare_breakup['min_time']) {
                        $after_min_duration = $ride_info->row()->summary['ride_duration'] - $ride_info->row()->fare_breakup['min_time'];
                } else {
                        $after_min_duration = 0;
                }	
                                        
                if (isset($ride_info->row()->total['tips_amount'])) {
                    if ($ride_info->row()->total['tips_amount'] > 0) {                               
                            $tips_amount = number_format($ride_info->row()->total['tips_amount'],2);     
                    }
                } else {
                    $tips_amount = 0.00;
                }
                if(isset($ride_info->row()->fare_breakup['peak_time_charge'])){
                        if ($ride_info->row()->fare_breakup['peak_time_charge'] != '') { 
                                $peak_time_charge_def = number_format($ride_info->row()->fare_breakup['peak_time_charge'],2);
                                $peak_time_charge = number_format($ride_info->row()->total['peak_time_charge'],2);
                        } else {
                                $peak_time_charge_def = 0;
                                $peak_time_charge = 0.00;
                        }
                } else {
                        $peak_time_charge_def = 0;
                        $peak_time_charge = 0.00;
                }
                
                if(isset($ride_info->row()->fare_breakup['night_charge'])){
                        if ($ride_info->row()->fare_breakup['night_charge'] != '') { 
                                $night_charge_def = number_format($ride_info->row()->fare_breakup['night_charge'],2);
                                $night_charge =  number_format($ride_info->row()->total['night_time_charge'],2);
                        } else {
                                $night_charge_def = 0 ;
                                $night_charge =0.00;
                        }
                } else {
                        $night_charge_def = 0 ;
                        $night_charge =0.00;
                }
                
                if(isset($ride_info->row()->fare_breakup['wait_per_minute'])){
                        if ($ride_info->row()->fare_breakup['wait_per_minute'] != '') { 
                                $wait_time_def = number_format($ride_info->row()->fare_breakup['wait_per_minute'],2);
                                $wait_time =  number_format($ride_info->row()->total['wait_time'],2);
                        } else {
                                $wait_time_def = 0 ;
                                $wait_time =0.00;
                        }
                } else {
                        $wait_time_def = 0 ;
                        $wait_time =0.00;
                }
                
                if(isset($ride_info->row()->total['coupon_discount'])){
                        if ($ride_info->row()->total['coupon_discount'] > 0) { 
                                $coupon_discount = number_format($ride_info->row()->total['coupon_discount'],2);
                        } else {
                                $coupon_discount = 0.00;
                        }						
                } else {
                      $coupon_discount = 0.00;
                }
				$original_grand_fare=0.00;
				if(isset($ride_info->row()->total['original_grand_fare'])) {
					$original_grand_fare=$ride_info->row()->total['original_grand_fare'];
				}
                
                $shareType = 'normal';
                if(isset($ride_info->row()->pool_ride)){
                    $shareType = 'pool';
                }
				$invoice_name = 'invoice_template';
				if($shareType == 'pool'){
					$invoice_name = 'share_pool_invoice_template';
				}
				$templateurl_pdf=FCPATH.'invoice'.DIRECTORY_SEPARATOR.$invoice_name.'.php';
				$user_id=$ride_info->row()->user['id'];
                $user_info = $this->user_model->get_selected_fields(USERS, array('_id' =>MongoID($user_id)),array('_id','lang_code'));
				$langcode = $this->app_language;
				if(isset($user_info->row()->lang_code) && $user_info->row()->lang_code!='') {
					$langcode = $user_info->row()->lang_code;
				}
				//$langcode='en';
                $template_values = $this->get_invoice_template($langcode,$shareType);
                $subject = $this->config->item('email_title') . ' ' . $template_values['subject'] . ' : #' . $ride_id;
                
				$pickup_location = $ride_info->row()->booking_information['pickup']['location'];
				$drop_location = $ride_info->row()->booking_information['drop']['location'];
				$pickup_time = date("H:i", MongoEPOCH($ride_info->row()->booking_information['drop_date']));
				$drop_time = date("H:i", MongoEPOCH($ride_info->row()->booking_information['drop_date']));
				$driver_image = USER_PROFILE_IMAGE_DEFAULT;
				$driver_id = $ride_info->row()->driver['id'];
				
				$checkDriver = $this->get_selected_fields(DRIVERS, array('_id' => MongoID($driver_id)), array('_id', 'driver_name', 'image', 'avg_review'));
				if (isset($checkDriver->row()->image)) {
					if ($checkDriver->row()->image != '') {
						$driver_image = USER_PROFILE_IMAGE . $checkDriver->row()->image;
					}
				}
				
				$parking_charge = 0;
                if(isset($ride_info->row()->total['parking_charge'])){
                    $parking_charge = $ride_info->row()->total['parking_charge'];
                }
                
                $toll_charge = 0;
                if(isset($ride_info->row()->total['toll_charge'])){
                    $toll_charge = $ride_info->row()->total['toll_charge'];
                }
                $toll_and_parking_charge = $parking_charge+$toll_charge;
				
				$ride_hours_mins=convertToHoursMins($ride_info->row()->summary['ride_duration']);
				$sub_total=$ride_info->row()->total['total_fare']+$peak_time_charge+$night_charge;
				$d_distance_unit_code = get_language_value_for_keyword($this->data['d_distance_unit'],$langcode);
				$d_distance_unit = get_language_value_for_keyword($this->data['d_distance_unit_name'],$langcode);
                $mailtemplateValues = array('email_title' => $this->config->item('email_title'),
										'logo_image' => $this->config->item('logo_image'),
										'ride_id' => $ride_info->row()->ride_id,
										'pickup_date' => date("d-m-Y", MongoEPOCH($ride_info->row()->booking_information['pickup_date'])),
										'booking_date' => date("d M, Y, h:i A", MongoEPOCH($ride_info->row()->booking_information['booking_date'])),
										'user_name' => $ride_info->row()->user['name'],
										'grand_fare' => number_format($ride_info->row()->total['grand_fare'],2),
                                        'total_fare' => number_format($ride_info->row()->total['total_fare'],2),
                                        'sub_total' => number_format($sub_total,2),
										'tips_amount' => $tips_amount,
										'ride_distance' => $ride_info->row()->summary['ride_distance'],
										'ride_duration' => $ride_info->row()->summary['ride_duration'],
										'ride_hours_mins' => $ride_hours_mins,
										'wallet_usage' => number_format($ride_info->row()->total['wallet_usage'],2),
										'paid_amount' => number_format($ride_info->row()->total['paid_amount'],2),
										'fare_breakup_km' => $ride_info->row()->fare_breakup['min_km'],
										'fare_breakup_time' => $ride_info->row()->fare_breakup['min_time'],
										'fare_breakup_per_km' => $ride_info->row()->fare_breakup['per_km'],
										'fare_breakup_per_min' => $ride_info->row()->fare_breakup['per_minute'],
										'fare_breakup_fare' => number_format($ride_info->row()->fare_breakup['min_fare'],2),
										'base_fare' => number_format($ride_info->row()->total['base_fare'],2),
										'service_tax' => number_format($ride_info->row()->total['service_tax'],2),
										'distance' => number_format($ride_info->row()->total['distance'],2),
										'ride_time' => number_format($ride_info->row()->total['ride_time'],2),
										'location' => $ride_info->row()->location['name'],
										'service_type' => $ride_info->row()->booking_information['service_type'],
										'booking_email' => $ride_info->row()->booking_information['booking_email'],
										'ride_id' => $ride_info->row()->ride_id,
										'rcurrencySymbol' => $this->data['dcurrencySymbol'],
										'ride_distance_unit' =>$d_distance_unit_code,
										'ride_distance_unit_name' =>$d_distance_unit,
										'footer_content' => $this->config->item('footer_content'),
										'meta_title' => $this->config->item('meta_title'),
										'site_contact_mail' => $this->config->item('site_contact_mail'),
										'site_name_capital' => $this->config->item('site_name_capital'),
										'after_min_duration' => $after_min_duration,
										'after_min_distance' => $after_min_distance,
										'coupon_discount' => $coupon_discount,
										'night_charge' => number_format($night_charge,2),
										'night_charge_def' => $night_charge_def,
										'peak_time_charge_def' => $peak_time_charge_def,
										'peak_time_charge' => number_format($peak_time_charge,2),
										'wait_time_def' => $wait_time_def,
										'wait_time' => $wait_time,
										'pickup_location' => $pickup_location,
										'drop_location' => $drop_location,
										'pickup_time' => $pickup_time,
										'drop_time' => $drop_time,
										'driver_name' => $ride_info->row()->driver['name'],
										'driver_image' => base_url().$driver_image,
										'vehicle_number' =>$ride_info->row()->driver['vehicle_no'],
										'service_tax_per' =>$ride_info->row()->tax_breakup['service_tax'],
										'round_off' =>number_format(($ride_info->row()->total['grand_fare']-$original_grand_fare),2),
										'parking_charge' => number_format($parking_charge,2),
                                        'toll_charge' => number_format($toll_charge,2),
                                        'toll_and_parking_charge' => number_format($toll_and_parking_charge,2),
                );
                extract($mailtemplateValues);
                $message = '<!DOCTYPE HTML>
								<html>
								<head>
								<meta http-equiv="Content-Type" content="text/html; charset=utf-8">
								<meta name="viewport" content="width=device-width"/>
								<title>' . $subject . '</title>
								<body>';
                include($template_values['templateurl']);
                $message .= '</body>
								</html>';
               
				$email_values = array('mail_type' => 'html',
                    'from_mail_id' => $this->config->item('site_contact_mail'),
                    'mail_name' => $this->config->item('email_title'),
                    'to_mail_id' => $email,
                    'subject_message' => $subject,
                    'body_messages' => $message
                );
                $email_send_to_common = $this->common_email_send($email_values);
               
            }
        }
    }

    /**
     * 
     * This function send the email invoice
     * 	@Param String $ride_id
     * 	@Param String $email
     * */
    public function send_invoice($ride_id = '', $email = '') {
        if ($ride_id != '') {
            $ride_info = $this->user_model->get_all_details(RIDES, array('ride_id' => $ride_id));
            if ($ride_info->num_rows() == 1) {
                $shareType = 'normal';
                if(isset($ride_info->row()->pool_ride)){
                    $shareType = 'pool';
                }
				$user_id=$ride_info->row()->user['id'];
				$user_info = $this->user_model->get_selected_fields(USERS, array('_id' =>MongoID($user_id)),array('_id','lang_code'));
				$langcode = $this->app_language;
				if(isset($user_info->row()->lang_code) && $user_info->row()->lang_code!='') {
					$langcode = $user_info->row()->lang_code;
				}
				if ($email == '') {
                    $email = $ride_info->row()->booking_information['booking_email'];
                }
                if ($ride_info->row()->summary['ride_distance'] > $ride_info->row()->fare_breakup['min_km']) {
                        $after_min_distance = $ride_info->row()->summary['ride_distance'] - $ride_info->row()->fare_breakup['min_km'];
                } else {
                        $after_min_distance = 0;
                }
                if ($ride_info->row()->summary['ride_duration'] > $ride_info->row()->fare_breakup['min_time']) {
                        $after_min_duration = $ride_info->row()->summary['ride_duration'] - $ride_info->row()->fare_breakup['min_time'];
                } else {
                        $after_min_duration = 0;
                }
                                        
                if (isset($ride_info->row()->total['tips_amount'])) {
                    if ($ride_info->row()->total['tips_amount'] > 0) {                               
                            $tips_amount = number_format($ride_info->row()->total['tips_amount'],2);     
                    }
                } else {
                    $tips_amount = 0.00;
                }
                if(isset($ride_info->row()->fare_breakup['peak_time_charge'])){
                        if ($ride_info->row()->fare_breakup['peak_time_charge'] != '') { 
                                $peak_time_charge_def = number_format($ride_info->row()->fare_breakup['peak_time_charge'],2);
                                $peak_time_charge = number_format($ride_info->row()->total['peak_time_charge'],2);
                        } else {
                                $peak_time_charge_def = 0;
                                $peak_time_charge = 0.00;
                        }
                } else {
                        $peak_time_charge_def = 0;
                        $peak_time_charge = 0.00;
                }
                
                if(isset($ride_info->row()->fare_breakup['night_charge'])){
                        if ($ride_info->row()->fare_breakup['night_charge'] != '') { 
                                $night_charge_def = number_format($ride_info->row()->fare_breakup['night_charge'],2);
                                $night_charge =  number_format($ride_info->row()->total['night_time_charge'],2);
                        } else {
                                $night_charge_def = 0 ;
                                $night_charge =0.00;
                        }
                } else {
                        $night_charge_def = 0 ;
                        $night_charge =0.00;
                }
                
                if(isset($ride_info->row()->fare_breakup['wait_per_minute'])){
                        if ($ride_info->row()->fare_breakup['wait_per_minute'] != '') { 
                                $wait_time_def = number_format($ride_info->row()->fare_breakup['wait_per_minute'],2);
                                $wait_time =  number_format($ride_info->row()->total['wait_time'],2);
                        } else {
                                $wait_time_def = 0 ;
                                $wait_time =0.00;
                        }
                } else {
                        $wait_time_def = 0 ;
                        $wait_time =0.00;
                }
                
                if(isset($ride_info->row()->total['coupon_discount'])){
                        if ($ride_info->row()->total['coupon_discount'] > 0) { 
                                $coupon_discount = number_format($ride_info->row()->total['coupon_discount'],2);
                        } else {
                                $coupon_discount = 0;
                        }						
                } else {
                      $coupon_discount = 0.00;
                }
                $original_grand_fare=0.00;
				if(isset($ride_info->row()->total['original_grand_fare'])) {
					$original_grand_fare=$ride_info->row()->total['original_grand_fare'];
				}
                $shareType = 'normal';
                if(isset($ride_info->row()->pool_ride)){
                    $shareType = 'pool';
                }
                $template_values = $this->get_invoice_template($langcode,$shareType);
				$pickup_location = $ride_info->row()->booking_information['pickup']['location'];
				$drop_location = $ride_info->row()->booking_information['drop']['location'];
				$pickup_time = date("H:i", MongoEPOCH($ride_info->row()->booking_information['drop_date']));
				$drop_time = date("H:i", MongoEPOCH($ride_info->row()->booking_information['drop_date']));
				$driver_image = USER_PROFILE_IMAGE_DEFAULT;
				$driver_id = $ride_info->row()->driver['id'];
				
				$checkDriver = $this->get_selected_fields(DRIVERS, array('_id' => MongoID($driver_id)), array('_id', 'driver_name', 'image', 'avg_review'));
				if (isset($checkDriver->row()->image)) {
					if ($checkDriver->row()->image != '') {
						$driver_image = USER_PROFILE_IMAGE . $checkDriver->row()->image;
					}
				}
				
				$parking_charge = 0;
                if(isset($ride_info->row()->total['parking_charge'])){
                    $parking_charge = $ride_info->row()->total['parking_charge'];
                }
                
                $toll_charge = 0;
                if(isset($ride_info->row()->total['toll_charge'])){
                    $toll_charge = $ride_info->row()->total['toll_charge'];
                }
                
                $toll_and_parking_charge = $parking_charge+$toll_charge;
				
				$ride_hours_mins=convertToHoursMins($ride_info->row()->summary['ride_duration']);
				$sub_total=$ride_info->row()->total['total_fare']+$peak_time_charge+$night_charge;
				$d_distance_unit_code = get_language_value_for_keyword($this->data['d_distance_unit'],$langcode);
				$d_distance_unit = get_language_value_for_keyword($this->data['d_distance_unit_name'],$langcode);				
                $subject = $this->config->item('email_title') . ' ' . $template_values['subject'] . ' : #' . $ride_id;
                $mailtemplateValues = array('email_title' => $this->config->item('email_title'),
                    'logo_image' => $this->config->item('logo_image'),
										'ride_id' => $ride_info->row()->ride_id,
										'pickup_date' => date("d-m-Y", MongoEPOCH($ride_info->row()->booking_information['pickup_date'])),
										'booking_date' => date("d M, Y, h:i A", MongoEPOCH($ride_info->row()->booking_information['booking_date'])),
										'user_name' => $ride_info->row()->user['name'],
										'grand_fare' => number_format($ride_info->row()->total['grand_fare'],2),
                                        'total_fare' => number_format($ride_info->row()->total['total_fare'],2),
										'sub_total' => number_format($sub_total,2),
										'tips_amount' => $tips_amount,
										'ride_distance' => $ride_info->row()->summary['ride_distance'],
										'ride_duration' => $ride_info->row()->summary['ride_duration'],
										'ride_hours_mins' => $ride_hours_mins,
										'wallet_usage' => number_format($ride_info->row()->total['wallet_usage'],2),
										'paid_amount' => number_format($ride_info->row()->total['paid_amount'],2),
										'fare_breakup_km' => $ride_info->row()->fare_breakup['min_km'],
										'fare_breakup_time' => $ride_info->row()->fare_breakup['min_time'],
										'fare_breakup_per_km' => $ride_info->row()->fare_breakup['per_km'],
										'fare_breakup_per_min' => $ride_info->row()->fare_breakup['per_minute'],
										'fare_breakup_fare' => number_format($ride_info->row()->fare_breakup['min_fare'],2),
										'base_fare' => number_format($ride_info->row()->total['base_fare'],2),
										'service_tax' => number_format($ride_info->row()->total['service_tax'],2),
										'distance' => number_format($ride_info->row()->total['distance'],2),
										'ride_time' => number_format($ride_info->row()->total['ride_time'],2),
										'location' => $ride_info->row()->location['name'],
										'service_type' => $ride_info->row()->booking_information['service_type'],
										'booking_email' => $ride_info->row()->booking_information['booking_email'],
										'ride_id' => $ride_info->row()->ride_id,
										'rcurrencySymbol' => $this->data['dcurrencySymbol'],
										'ride_distance_unit' => $d_distance_unit_code,
										'ride_distance_unit_name' => $d_distance_unit,
										'footer_content' => $this->config->item('footer_content'),
										'meta_title' => $this->config->item('meta_title'),
										'site_contact_mail' => $this->config->item('site_contact_mail'),
										'site_name_capital' => $this->config->item('site_name_capital'),
										'after_min_duration' => $after_min_duration,
										'after_min_distance' => $after_min_distance,
										'coupon_discount' => $coupon_discount,
										'night_charge' => number_format($night_charge,2),
										'night_charge_def' => $night_charge_def,
										'peak_time_charge_def' => $peak_time_charge_def,
										'peak_time_charge' => number_format($peak_time_charge,2),
										'wait_time_def' => $wait_time_def,
										'wait_time' => $wait_time,
										'pickup_location' => $pickup_location,
										'drop_location' => $drop_location,
										'pickup_time' => $pickup_time,
										'drop_time' => $drop_time,
										'driver_name' => $ride_info->row()->driver['name'],
										'driver_image' => base_url().$driver_image,
										'vehicle_number' =>$ride_info->row()->driver['vehicle_no'],
										'service_tax_per' =>$ride_info->row()->tax_breakup['service_tax'],
										'round_off' =>number_format(($ride_info->row()->total['grand_fare']-$original_grand_fare),2),
										'toll_charge' => number_format($toll_charge,2),
                                        'parking_charge' => number_format($parking_charge,2),
                                        'toll_and_parking_charge' => number_format($toll_and_parking_charge,2),
                );
                extract($mailtemplateValues);
                $message = '<!DOCTYPE HTML>
								<html>
								<head>
								<meta http-equiv="Content-Type" content="text/html; charset=utf-8">
								<meta name="viewport" content="width=device-width"/>
								<title>' . $subject . '</title>
								<body>';
                include($template_values['templateurl']);
                $message .= '</body>
								</html>';
								                
                $email_values = array('mail_type' => 'html',
                    'from_mail_id' => $this->config->item('site_contact_mail'),
                    'mail_name' => $this->config->item('email_title'),
                    'to_mail_id' => $email,
                    'subject_message' => $subject,
                    'body_messages' => $message
                );
                $email_send_to_common = $this->common_email_send($email_values);
                
            }
        }
    }
  

    function wallet_recharge_successfull_notification($pay_details, $rider_info, $txn_time, $recharge_id) {
        $newsid = '9';
        $template_values = $this->get_email_template($newsid);
        $dcurrencySymbol = $this->data['dcurrencySymbol'];
        $user_name = $rider_info->row()->user_name;
        $amount = $dcurrencySymbol . $pay_details['trans_amount'];
        $txn_id = $pay_details['trans_id'];
        $txn_date = date('M d-Y h:i a', $txn_time);
        $txn_method = $pay_details['ref_id'];
        $wallet_amount = $dcurrencySymbol . $pay_details['avail_amount'];

        if ($txn_id == '') {
            $txn_id = $recharge_id;
        }

        $subject = $this->config->item('email_title') . ' - ' . $template_values['subject'];
        $mailtemplateValues = array('mail_emailTitle' => $this->config->item('email_title'),
            'mail_logo' => $this->config->item('logo_image'),
            'mail_footerContent' => $this->config->item('footer_content'),
            'mail_metaTitle' => $this->config->item('meta_title'),
            'mail_contactMail' => $this->config->item('site_contact_mail')
        );
        extract($mailtemplateValues);
        $message = '<!DOCTYPE HTML>
			<html>
			<head>
			<meta http-equiv="Content-Type" content="text/html; charset=utf-8">
			<meta name="viewport" content="width=device-width"/>
			<title>' . $subject . '</title>
			<body>';
        include($template_values['templateurl']);
        $message .= '</body>
			</html>';
		 $sender_email = $this->config->item('site_contact_mail');
         $sender_name = $this->config->item('email_title');
        
        $email_values = array('mail_type' => 'html',
            'from_mail_id' => $sender_email,
            'mail_name' => $sender_name,
            'to_mail_id' => $rider_info->row()->email,
            'subject_message' => $subject,
            'body_messages' => $message
        );
        $email_send_to_common = $this->common_email_send($email_values);
    }
 
	/**
     *
     * Get Notification / EMAIL templates details
     * @param Interger $news_id
     *
     * */
    public function notification_email_template_info($news_id = '') {
        $this->mongo_db->select();
        if ($news_id != '') {
            $this->mongo_db->where(array('news_id' => (int) $news_id));
        }
        $res = $this->mongo_db->get(NOTIFICATION_TEMPLATES);
        return $res->row();
    }
		
		/**
     * 
     * This function send the email invoice
     * 	@Param String $ride_id
     * 	@Param String $email
     * */
    public function view_invoice($ride_id = '', $email = '', $langcode = '') {
			$message ='';
        if ($ride_id != '') {
            $ride_info = $this->user_model->get_all_details(RIDES, array('ride_id' => $ride_id));
            if ($ride_info->num_rows() == 1) {
                if ($email == '') {
                    $email = $ride_info->row()->booking_information['booking_email'];
                }
				if ($ride_info->row()->summary['ride_distance'] > $ride_info->row()->fare_breakup['min_km']) {
					$after_min_distance = $ride_info->row()->summary['ride_distance'] - $ride_info->row()->fare_breakup['min_km'];
				} else {
					$after_min_distance = 0;
				}
				if ($ride_info->row()->summary['ride_duration'] > $ride_info->row()->fare_breakup['min_time']) {
					$after_min_duration = $ride_info->row()->summary['ride_duration'] - $ride_info->row()->fare_breakup['min_time'];
				} else {
					$after_min_duration = 0;
				}	
										
				if (isset($ride_info->row()->total['tips_amount'])) {
						if ($ride_info->row()->total['tips_amount'] > 0) {                               
								$tips_amount = number_format($ride_info->row()->total['tips_amount'],2);     
						}
				} else {
						$tips_amount = 0.00;
				}
				if(isset($ride_info->row()->fare_breakup['peak_time_charge'])){
						if ($ride_info->row()->fare_breakup['peak_time_charge'] != '') { 
								$peak_time_charge_def = number_format($ride_info->row()->fare_breakup['peak_time_charge'],2);
								$peak_time_charge = number_format($ride_info->row()->total['peak_time_charge'],2);
						} else {
								$peak_time_charge_def = 0;
								$peak_time_charge = 0.00;
						}
				} else {
						$peak_time_charge_def = 0;
						$peak_time_charge = 0.00;
				}
								
                if(isset($ride_info->row()->fare_breakup['night_charge'])){
						if ($ride_info->row()->fare_breakup['night_charge'] != '') { 
								$night_charge_def = number_format($ride_info->row()->fare_breakup['night_charge'],2);
								$night_charge =  number_format($ride_info->row()->total['night_time_charge'],2);
						} else {
								$night_charge_def = 0 ;
								$night_charge =0.00;
						}
				} else {
						$night_charge_def = 0 ;
						$night_charge =0.00;
				}
				
				if(isset($ride_info->row()->fare_breakup['wait_per_minute'])){
						if ($ride_info->row()->fare_breakup['wait_per_minute'] != '') { 
								$wait_time_def = number_format($ride_info->row()->fare_breakup['wait_per_minute'],2);
								$wait_time =  number_format($ride_info->row()->total['wait_time'],2);
						} else {
								$wait_time_def = 0 ;
								$wait_time =0.00;
						}
				} else {
						$wait_time_def = 0 ;
						$wait_time =0.00;
				}
								
				if(isset($ride_info->row()->total['coupon_discount'])){
						if ($ride_info->row()->total['coupon_discount'] > 0) { 
								$coupon_discount = number_format($ride_info->row()->total['coupon_discount'],2);
						} else {
								$coupon_discount = 0;
						}						
				} else {
					  $coupon_discount = 0.00;
				}
				$original_grand_fare=0.00;
				if(isset($ride_info->row()->total['original_grand_fare'])) {
					$original_grand_fare=$ride_info->row()->total['original_grand_fare'];
				}
				
				
				$shareType = 'normal';
				if(isset($ride_info->row()->pool_ride)){
					$shareType = 'pool';
				}
				
				$min_fare = 0;
				if(isset($ride_info->row()->fare_breakup['min_fare'])){
					$min_fare = floatval($ride_info->row()->fare_breakup['min_fare']);
				}
				$user_id=$ride_info->row()->user['id'];
				$user_info = $this->user_model->get_selected_fields(USERS, array('_id' =>MongoID($user_id)),array('_id','lang_code'));
				$langcode = $this->app_language;
				if(isset($user_info->row()->lang_code) && $user_info->row()->lang_code!='') {
					$langcode = $user_info->row()->lang_code;
				}
				$template_values = $this->get_invoice_template($langcode,$shareType);
				
				$subject = $this->config->item('email_title') . ' invoice for ride : ' . $ride_id;
				
				$pickup_location = $ride_info->row()->booking_information['pickup']['location'];
				$drop_location = $ride_info->row()->booking_information['drop']['location'];
				$pickup_time = date("H:i", MongoEPOCH($ride_info->row()->booking_information['drop_date']));
				$drop_time = date("H:i", MongoEPOCH($ride_info->row()->booking_information['drop_date']));
				$driver_image = USER_PROFILE_IMAGE_DEFAULT;
				$driver_id = $ride_info->row()->driver['id'];
				
				$checkDriver = $this->get_selected_fields(DRIVERS, array('_id' => MongoID($driver_id)), array('_id', 'driver_name', 'image', 'avg_review'));
				if (isset($checkDriver->row()->image)) {
					if ($checkDriver->row()->image != '') {
						$driver_image = USER_PROFILE_IMAGE . $checkDriver->row()->image;
					}
				}
				$ride_hours_mins=convertToHoursMins($ride_info->row()->summary['ride_duration']);
				$sub_total=$ride_info->row()->total['total_fare']+$peak_time_charge+$night_charge;
				$d_distance_unit_code = get_language_value_for_keyword($this->data['d_distance_unit'],$langcode);
				$d_distance_unit = get_language_value_for_keyword($this->data['d_distance_unit_name'],$langcode);
				
				 $parking_charge = 0;
                if(isset($ride_info->row()->total['parking_charge'])){
                    $parking_charge = $ride_info->row()->total['parking_charge'];
                }
                $toll_charge = 0;
                if(isset($ride_info->row()->total['toll_charge'])){
                    $toll_charge = $ride_info->row()->total['toll_charge'];
                }
                $toll_and_parking_charge = $parking_charge+$toll_charge;
								
				$mailtemplateValues = array('email_title' => $this->config->item('email_title'),
						'logo_image' => $this->config->item('logo_image'),
						'ride_id' => $ride_info->row()->ride_id,
						'pickup_date' => date("d-m-Y", MongoEPOCH($ride_info->row()->booking_information['pickup_date'])),
						'booking_date' => date("d M, Y, h:i A", MongoEPOCH($ride_info->row()->booking_information['booking_date'])),
						'user_name' => $ride_info->row()->user['name'],
						'grand_fare' => number_format($ride_info->row()->total['grand_fare'],2),
						'total_fare' => number_format($ride_info->row()->total['total_fare'],2),
						'sub_total' => number_format($sub_total,2),
						'tips_amount' => $tips_amount,
						'ride_distance' => $ride_info->row()->summary['ride_distance'],
						'ride_duration' => convertToHoursMins($ride_info->row()->summary['ride_duration']),
						'ride_hours_mins' =>$ride_hours_mins,
						'wallet_usage' => number_format($ride_info->row()->total['wallet_usage'],2),
						'paid_amount' => number_format($ride_info->row()->total['paid_amount'],2),
						'fare_breakup_km' => $ride_info->row()->fare_breakup['min_km'],
						'fare_breakup_time' => $ride_info->row()->fare_breakup['min_time'],
						'fare_breakup_per_km' => $ride_info->row()->fare_breakup['per_km'],
						'fare_breakup_per_min' => $ride_info->row()->fare_breakup['per_minute'],
						'fare_breakup_fare' => number_format($min_fare,2),
						'base_fare' => number_format($ride_info->row()->total['base_fare'],2),
						'service_tax' => number_format($ride_info->row()->total['service_tax'],2),
						'distance' => number_format($ride_info->row()->total['distance'],2),
						'ride_time' => number_format($ride_info->row()->total['ride_time'],2),
						'location' => $ride_info->row()->location['name'],
						'service_type' => $ride_info->row()->booking_information['service_type'],
						'booking_email' => $ride_info->row()->booking_information['booking_email'],
						'ride_id' => $ride_info->row()->ride_id,
						'rcurrencySymbol' => $this->data['dcurrencySymbol'],
						'ride_distance_unit' => $d_distance_unit_code,
						'ride_distance_unit_name' => $d_distance_unit,
						'footer_content' => $this->config->item('footer_content'),
						'meta_title' => $this->config->item('meta_title'),
						'site_contact_mail' => $this->config->item('site_contact_mail'),
						'site_name_capital' => $this->config->item('site_name_capital'),
						'after_min_duration' => $after_min_duration,
						'after_min_distance' => $after_min_distance,
						'coupon_discount' => $coupon_discount,
						'night_charge' => number_format($night_charge,2),
						'night_charge_def' => $night_charge_def,
						'peak_time_charge_def' => $peak_time_charge_def,
						'peak_time_charge' => number_format($peak_time_charge,2),
						'wait_time_def' => $wait_time_def,
						'wait_time' => $wait_time,
						'pickup_location' => $pickup_location,
						'drop_location' => $drop_location,
						'pickup_time' => $pickup_time,
						'drop_time' => $drop_time,
						'driver_name' => $ride_info->row()->driver['name'],
						'driver_image' => base_url().$driver_image,
						'vehicle_number' =>$ride_info->row()->driver['vehicle_no'],
						'service_tax_per' =>$ride_info->row()->tax_breakup['service_tax'],
						'round_off' =>number_format(($ride_info->row()->total['grand_fare']-$original_grand_fare),2),
						'parking_charge' => number_format($parking_charge,2),
						'toll_charge' => number_format($toll_charge,2),
						'toll_and_parking_charge' => number_format($toll_and_parking_charge,2)
					);
					extract($mailtemplateValues);
					$message = '<!DOCTYPE HTML>
								<html>
								<head>
								<meta http-equiv="Content-Type" content="text/html; charset=utf-8">
								<meta name="viewport" content="width=device-width"/>
								<title>' . $subject . '</title>
								<body>';
                include($template_values['templateurl']);
                $message .= '</body>
								</html>';
               
            }
        }
				return stripcslashes($message);
    }
		
		/**
    * 
    * This function send the email to operators for registration confirmation
    * 	@Param String $operator_id
    * */
    public function send_operator_register_confirmation_mail($operator_id = '',$password) {
        if ($operator_id != '') {
            $checkOperator = $this->get_selected_fields(OPERATORS, array('_id' => MongoID($operator_id)), array('email', 'operator_name'));
            if ($checkOperator->num_rows() > 0) {
                $newsid = '15';
                $template_values = $this->get_email_template($newsid);
                $subject = $this->config->item('email_title') . ' - ' . $template_values['subject'];
                $mailtemplateValues = array('mail_emailTitle' => $this->config->item('email_title'),
                    'mail_logo' => $this->config->item('logo_image'),
                    'mail_footerContent' => $this->config->item('footer_content'),
                    'mail_metaTitle' => $this->config->item('meta_title'),
                    'mail_contactMail' => $this->config->item('site_contact_mail'),
                    'password' => $password,
										'operatorName' => OPERATOR_NAME
                );
                extract($mailtemplateValues);
                $message = '<!DOCTYPE HTML>
								<html>
								<head>
								<meta http-equiv="Content-Type" content="text/html; charset=utf-8">
								<meta name="viewport" content="width=device-width"/>
								<title>' . $subject . '</title>
								<body>';
											include($template_values['templateurl']);
											$message .= '</body>
								</html>';
					
								$sender_email = $this->config->item('site_contact_mail');
								$sender_name = $this->config->item('email_title');
                
                $email_values = array('mail_type' => 'html',
                    'from_mail_id' => $sender_email,
                    'mail_name' => $sender_name,
                    'to_mail_id' => $checkOperator->row()->email,
                    'subject_message' => $subject,
                    'body_messages' => $message
                );
                $email_send_to_common = $this->common_email_send($email_values);
            }
        }
    }
		
		/**
    * 
    * This function send the email to user for registration confirmation
    * 	@Param String $user_id
    * */
    public function send_user_register_confirmation_mail($user_id = '',$password) {
        if ($user_id != '') {
            $checkUser = $this->get_selected_fields(USERS, array('_id' => MongoID($user_id)), array('email', 'user_name', 'unique_code'));
            if ($checkUser->num_rows() > 0) {
                $newsid = '17';
                $template_values = $this->get_email_template($newsid);
                $subject = $this->config->item('email_title') . ' - ' . $template_values['subject'];
                $mailtemplateValues = array('mail_emailTitle' => $this->config->item('email_title'),
                    'mail_logo' => $this->config->item('logo_image'),
                    'mail_footerContent' => $this->config->item('footer_content'),
                    'mail_metaTitle' => $this->config->item('meta_title'),
                    'mail_contactMail' => $this->config->item('site_contact_mail'),
                    'password' => $password,
                    'mail_referalCode' => $checkUser->row()->unique_code
                );
                extract($mailtemplateValues);
                $message = '<!DOCTYPE HTML>
								<html>
								<head>
								<meta http-equiv="Content-Type" content="text/html; charset=utf-8">
								<meta name="viewport" content="width=device-width"/>
								<title>' . $subject . '</title>
								<body>';
											include($template_values['templateurl']);
											$message .= '</body>
								</html>';
					
								$sender_email = $this->config->item('site_contact_mail');
								$sender_name = $this->config->item('email_title');
                
                $email_values = array('mail_type' => 'html',
                    'from_mail_id' => $sender_email,
                    'mail_name' => $sender_name,
                    'to_mail_id' => $checkUser->row()->email,
                    'subject_message' => $subject,
                    'body_messages' => $message
                );
                $email_send_to_common = $this->common_email_send($email_values);
            }
        }
    }
		
		
		public function send_operator_booking_confirmation_mail($bookingArr= array()){

				$newsid = '16';
				$template_values = $this->get_email_template($newsid);
				
				$user_name = $bookingArr['name'];;
				$to_mail = $bookingArr['email'];
				$ride_id = $bookingArr['ride_id'];
				$location = $bookingArr['location'];
			
				$subject = $this->config->item('email_title') . ' - ' . $template_values['subject'];
				$mailtemplateValues = array('mail_emailTitle' => $this->config->item('email_title'),
							'mail_logo' => $this->config->item('logo_image'),
							'mail_footerContent' => $this->config->item('footer_content'),
							'mail_metaTitle' => $this->config->item('meta_title'),
							'mail_contactMail' => $this->config->item('site_contact_mail')
				); 
				extract($mailtemplateValues);
				$message = '<!DOCTYPE HTML>
				<html>
				<head>
				<meta http-equiv="Content-Type" content="text/html; charset=utf-8">
				<meta name="viewport" content="width=device-width"/>
				<title>' . $subject . '</title>
				<body>';
					include($template_values['templateurl']);
					$message .= '</body>
				</html>';
				$sender_name = $template_values['sender_name'];
				$sender_email = $template_values['sender_email'];
				if ($template_values['sender_name'] == '' && $template_values['sender_email'] == '') {
						$sender_email = $this->config->item('site_contact_mail');
						$sender_name = $this->config->item('email_title');
				}
				$email_values = array('mail_type' => 'html',
						'from_mail_id' => $sender_email,
						'mail_name' => $sender_name,
						'to_mail_id' => $to_mail,
						'subject_message' => $subject,
						'body_messages' => trim($message)
				);   
			  $email_send_to_common = $this->common_email_send($email_values);
		}
		
	public function customer_service_rider_register_notification_mail($rider_id = '',$user_password=''	) {

        if ($rider_id != '') {
            $checkRider = $this->get_selected_fields(USERS, array('_id' => MongoID($rider_id)), array('email', 'user_name','unique_code'));

            if ($checkRider->num_rows() > 0) {
                $newsid = '3';
                $template_values = $this->get_email_template($newsid);
                $subject = $template_values['subject'];
                $mailtemplateValues = array(
                    'user_name' => $checkRider->row()->user_name,
                    'mail_emailTitle' => $this->config->item('email_title'),
                    'mail_logo' => $this->config->item('logo_image'),
                    'mail_footerContent' => $this->config->item('footer_content'),
                    'mail_metaTitle' => $this->config->item('meta_title'),
                    'mail_contactMail' => $this->config->item('site_contact_mail'),
                    'mail_referalCode' => $checkRider->row()->unique_code
                );
                extract($mailtemplateValues);
                $message = '<!DOCTYPE HTML>
					<html>
					<head>
					<meta http-equiv="Content-Type" content="text/html; charset=utf-8">
					<meta name="viewport" content="width=device-width"/>
					<title>' . $subject . '</title>
					<body>';
                include('./newsletter/template' . $newsid . '.php');
                $message .= '</body>
					</html>';

                if ($template_values['sender_name'] == '' && $template_values['sender_email'] == '') {
                    $sender_email = $this->config->item('site_contact_mail');
                    $sender_name = $this->config->item('email_title');
                } else {
                    $sender_name = $template_values['sender_name'];
                    $sender_email = $template_values['sender_email'];
                }
				
	
				
                $email_values = array('mail_type' => 'html',
                    'from_mail_id' => $sender_email,
                    'mail_name' => $sender_name,
                    'to_mail_id' => $checkRider->row()->email,
                    'subject_message' => $subject,
                    'body_messages' => $message
                );
                $email_send_to_common = $this->common_email_send($email_values);
            }
        }
    }
	
	public function send_report_to_admin($mailArr=array()) {
		extract($mailArr);
		$newsid = '20';
		$template_values = $this->get_email_template($newsid);
		$subject = $template_values['subject'];
		$mailtemplateValues = array(
			'mail_emailTitle' => $this->config->item('email_title'),
			'mail_logo' => $this->config->item('logo_image'),
			'mail_footerContent' => $this->config->item('footer_content'),
			'mail_metaTitle' => $this->config->item('meta_title'),
			'mail_contactMail' => $this->config->item('site_contact_mail')
		);
		extract($mailtemplateValues);
		$message = '<!DOCTYPE HTML>
			<html>
			<head>
			<meta http-equiv="Content-Type" content="text/html; charset=utf-8">
			<meta name="viewport" content="width=device-width"/>
			<title>' . $subject . '</title>
			<body>';
		include('./newsletter/template' . $newsid . '.php');
		$message .= '</body>
			</html>';

		if ($template_values['sender_name'] == '' && $template_values['sender_email'] == '') {
			$sender_email = $this->config->item('site_contact_mail');
			$sender_name = $this->config->item('email_title');
		} else {
			$sender_name = $template_values['sender_name'];
			$sender_email = $template_values['sender_email'];
		}
		
		$email_values = array('mail_type' => 'html',
			'from_mail_id' => $reporter_email,
			'mail_name' => $reporter_name,
			'to_mail_id' => $this->config->item('site_contact_mail'),
			'subject_message' => $subject,
			'body_messages' => $message
		);
		$email_send_to_common = $this->common_email_send($email_values);
    }
	
	public function reply_to_report_from_admin($mailArr=array()) {
		extract($mailArr);
		$newsid = '21';
		$template_values = $this->get_email_template($newsid);
		#$subject = $template_values['subject'];
		$mailtemplateValues = array(
			'mail_emailTitle' => $this->config->item('email_title'),
			'mail_logo' => $this->config->item('logo_image'),
			'mail_footerContent' => $this->config->item('footer_content'),
			'mail_metaTitle' => $this->config->item('meta_title'),
			'mail_contactMail' => $this->config->item('site_contact_mail')
		);
		extract($mailtemplateValues);
		$message = '<!DOCTYPE HTML>
			<html>
			<head>
			<meta http-equiv="Content-Type" content="text/html; charset=utf-8">
			<meta name="viewport" content="width=device-width"/>
			<title>' . $subject . '</title>
			<body>';
		include('./newsletter/template' . $newsid . '.php');
		$message .= '</body>
			</html>';

		if ($template_values['sender_name'] == '' && $template_values['sender_email'] == '') {
			$sender_email = $this->config->item('site_contact_mail');
			$sender_name = $this->config->item('email_title');
		} else {
			$sender_name = $template_values['sender_name'];
			$sender_email = $template_values['sender_email'];
		}
		
		$email_values = array('mail_type' => 'html',
			'from_mail_id' => $sender_email,
			'mail_name' => $sender_name,
			'to_mail_id' => $reporter_email,
			'subject_message' => $subject,
			'body_messages' => $message
		);
		$email_send_to_common = $this->common_email_send($email_values);
    }
	
	
	public function payment_alert_notification() {
		$newsid = '23';
		$template_values = $this->get_email_template($newsid);
		$subject = $template_values['subject'];
		$mailtemplateValues = array(
			'mail_emailTitle' => $this->config->item('email_title'),
			'mail_logo' => $this->config->item('logo_image'),
			'mail_footerContent' => $this->config->item('footer_content'),
			'mail_metaTitle' => $this->config->item('meta_title'),
			'mail_contactMail' => $this->config->item('site_contact_mail')
		);
		extract($mailtemplateValues);
		$message = '<!DOCTYPE HTML>
			<html>
			<head>
			<meta http-equiv="Content-Type" content="text/html; charset=utf-8">
			<meta name="viewport" content="width=device-width"/>
			<title>' . $subject . '</title>
			<body>';
		include('./newsletter/template' . $newsid . '.php');
		$message .= '</body>
			</html>';

		if ($template_values['sender_name'] == '' && $template_values['sender_email'] == '') {
			$sender_email = $this->config->item('site_contact_mail');
			$sender_name = $this->config->item('email_title');
		} else {
			$sender_name = $template_values['sender_name'];
			$sender_email = $template_values['sender_email'];
		}
		
		$to_mail_id = $this->config->item('email');
		
		$email_values = array('mail_type' => 'html',
			'from_mail_id' => $sender_email,
			'mail_name' => $sender_name,
			'to_mail_id' => $to_mail_id,
			'subject_message' => $subject,
			'body_messages' => $message
		);
		$email_send_to_common = $this->common_email_send($email_values);
    }
	
	/**
    * 
    * This function send the email to unregister drivers for registration confirmation
    * 	@Param String $user_id
    * */
    public function send_unregister_driver_register_confirmation_mail($user_id = '') {
        if ($user_id != '') {
            $checkUser = $this->get_selected_fields(TEMP_DRIVERS, array('_id' => MongoID((string)$user_id)), array('email', 'driver_name'));
			# echo 'ruban'; echo '<pre>';  print_r($checkUser->row()); die;
            if ($checkUser->num_rows() > 0) {
                $newsid = '24';
                $template_values = $this->get_email_template($newsid);
                $subject = $this->config->item('email_title') . ' - ' . $template_values['subject'];
                $mailtemplateValues = array('mail_emailTitle' => $this->config->item('email_title'),
                    'mail_logo' => $this->config->item('logo_image'),
                    'mail_footerContent' => $this->config->item('footer_content'),
                    'mail_metaTitle' => $this->config->item('meta_title'),
                    'mail_contactMail' => $this->config->item('site_contact_mail')
                );
				$driver_id=base64_encode($user_id);
                extract($mailtemplateValues);
                $message = '<!DOCTYPE HTML>
					<html>
					<head>
					<meta http-equiv="Content-Type" content="text/html; charset=utf-8">
					<meta name="viewport" content="width=device-width"/>
					<title>' . $subject . '</title>
					<body>';
                include($template_values['templateurl']);
                $message .= '</body>
					</html>';
					$sender_email = $this->config->item('site_contact_mail');
                    $sender_name = $this->config->item('email_title');
                
                $email_values = array('mail_type' => 'html',
                    'from_mail_id' => $sender_email,
                    'mail_name' => $sender_name,
                    'to_mail_id' => $checkUser->row()->email,
                    'subject_message' => $subject,
                    'body_messages' => $message
                );
				# echo '<pre>'; print_r($email_values ); die;
                $email_send_to_common = $this->common_email_send($email_values);
				 #echo $email_send_to_common; die;
            }
        }
		
    }
}
