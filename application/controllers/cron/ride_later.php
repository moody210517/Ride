<?php

if (!defined('BASEPATH'))
    exit('No direct script access allowed');

/**
 * 
 * ride later related functions
 * @author Casperon
 *
 * */
class Ride_later extends MY_Controller {

    function __construct() {
        parent::__construct();
        $this->load->helper(array('cookie', 'date', 'form', 'email','ride_helper'));
        $this->load->library(array('encrypt', 'form_validation'));
        $this->load->model('user_model');
        $this->load->model('app_model');
        $returnArr = array();
    }

    /*
     *
     * Select the rides which are to be un allocated
     *
     */

    public function get_later_rides() {
        $start_time = time();
        $end_time = $start_time + 1800;
        
        $later_rides = $this->app_model->get_ride_later_list($start_time, $end_time);
        $expired_rides = $this->app_model->get_expired_ride_list($start_time);
        
        if ($expired_rides->num_rows() > 0) {
            foreach ($expired_rides->result() as $rides) {
                $rid = $rides->ride_id;
				/* Saving Unaccepted Ride for future reference */
				save_ride_details_for_stats($rid);
				/* Saving Unaccepted Ride for future reference */
                $email = $rides->user['email'];
                $user_id =$rides->user['id'];
                $this->mail_to_user($rid,$email,$user_id);
            }
        }
        if ($later_rides->num_rows() > 0) {
            foreach ($later_rides->result() as $rides) {
                $rid = $rides->ride_id;
                $this->booking_ride_later_request($rid);
            }
        }
    }
    
    /**
     *
     * This Function used for mail to user regrading Ride Expired
     *
     * */
    public function mail_to_user($rid,$email,$user_id)
    {
		/* Update the ride information */
		$rideDetails = array('ride_status' => 'Expired', 'booking_information.expired_date' => MongoDATE(time()));
		$this->app_model->update_details(RIDES, $rideDetails, array('ride_id' => $rid));
		$newsid = '13';
		$query = $this->app_model->get_all_details(USERS, array('_id' => MongoID($user_id)));
		$user_name = $query->row()->user_name;
   
		$template_values = $this->user_model->get_email_template($newsid,$this->data['langCode']);
		$subject = 'From: ' . $this->config->item('email_title') . ' - ' . $template_values['subject'];
		$ridernewstemplateArr = array('email_title' => $this->config->item('email_title'), 'mail_emailTitle' => $this->config->item('email_title'), 'mail_logo' => $this->config->item('logo_image'), 'mail_footerContent' => $this->config->item('footer_content'), 'mail_metaTitle' => $this->config->item('meta_title'), 'mail_contactMail' => $this->config->item('site_contact_mail'));
		extract($ridernewstemplateArr);
		$ride_id=$rid;
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
		   'to_mail_id' => $query->row()->email,
		   'subject_message' =>'Your '.$this->config->item('email_title').' ride has been expired',
		   'body_messages' => $message
		);
	  
		$email_send_to_common = $this->user_model->common_email_send($email_values);
    
    }


    /**
     *
     * This Function used for booking a ride later request
     *
     * */
    public function booking_ride_later_request($ride_id = '') {
        $limit = 10;
        if ($ride_id != '') {
            $checkRide = $this->app_model->get_all_details(RIDES, array('ride_id' => $ride_id));
            if ($checkRide->num_rows() == 1) {
                $checkUser = $this->app_model->get_selected_fields(USERS, array('_id' => MongoID($checkRide->row()->user['id'])), array('email', 'user_name', 'country_code', 'phone_number', 'push_type'));
                if ($checkUser->row()->push_type != '') {
                    $pickup_lon = $checkRide->row()->booking_information['pickup']['latlong']['lon'];
                    $pickup_lat = $checkRide->row()->booking_information['pickup']['latlong']['lat'];
                    $coordinates = array(floatval($pickup_lon), floatval($pickup_lat));
                    $location = $this->app_model->find_location(floatval($pickup_lon), floatval($pickup_lat));
                    if (!empty($location['result'])) {
                        $condition = array('status' => 'Active');
                        $category = $checkRide->row()->booking_information['service_id'];
                        $categoryResult = $this->app_model->get_selected_fields(CATEGORY, array('_id' => MongoID($category)), array('name'));
                        if ($categoryResult->num_rows() > 0) {
                            $category_drivers = $this->app_model->get_nearest_driver($coordinates, (string) $category, $limit);

                            if (empty($category_drivers['result'])) {
                                $category_drivers = $this->app_model->get_nearest_driver($coordinates, (string) $category, $limit * 2);
                            }
							
							$requested_drivers = array();
							if(isset($checkRide->row()->requested_driver)){
								$requested_drivers = $checkRide->row()->requested_driver;
							}
							
							$push_drivers = array();
                            foreach ($category_drivers['result'] as $driver) {
								$d_id=(string)$driver['_id'];
								array_push($requested_drivers,$d_id);
                                if (isset($driver['push_notification'])) {
                                    if ($driver['push_notification']['type'] == 'ANDROID') {
                                        if (isset($driver['push_notification']['key']) && $driver['push_notification']['key'] != '') {
											$k = $driver['push_notification']['key'];
											$push_drivers[$k] = array('id' => $driver['_id'],
																					'driver_loc' =>  $driver['loc'],
																					'messaging_status' => $driver['messaging_status'],
																					'distance' => $driver['distance'],
																					'device_type' => 'ANDROID'
																			);
                                        }
                                    }
                                    if ($driver['push_notification']['type'] == 'IOS') {
                                        if (isset($driver['push_notification']['key']) && $driver['push_notification']['key'] != '') {
                                            $k = $driver['push_notification']['key'];
											$push_drivers[$k] = array('id' => $driver['_id'],
																					'driver_loc' =>  $driver['loc'],
																					'messaging_status' => $driver['messaging_status'],
																					'distance' => $driver['distance'],
																					'device_type' => 'IOS'
																			);
                                        }
                                    }
                                }
                            }


                            if ($checkRide->row()->type == 'Later') {
                                $pickup = $checkRide->row()->booking_information['pickup']['location'];
                                $drop_loc = $checkRide->row()->booking_information['drop']['location'];
                                $response_time = $this->config->item('respond_timeout');
                                $options = array($ride_id, $response_time, $pickup,$drop_loc,(string)time());
								
								
								if (!empty($push_drivers)) {
									foreach ($push_drivers as $keys => $value) {
										$driver_id = $value['id']; 
										$driver_Msg = $this->format_string("Request for pickup user","request_pickup_user", '', 'driver', (string)$driver_id);
										
										$reqHisArr =array('driver_id'=> MongoID($driver_id),
																	'driver_loc'=> $value['loc'],
																	'requested_time'=>MongoDATE(time()),
																	'distance'=> $value['distance'],
																	'ride_id'=> (string)$ride_id,
																	'status'=>'sent',
																	'device_type' => $value['device_type'],
																	'messaging_status' => $value['messaging_status']
																 );
										$this->app_model->simple_insert(RIDE_REQ_HISTORY, $reqHisArr);
										$ack_id = $this->mongo_db->insert_id();
										$options[6]= (string) $ack_id;
										
										$this->sendPushNotification($keys, $driver_Msg , 'ride_request', $value['device_type'], $options, 'DRIVER');
										$condition = array('_id' => MongoID($driver_id));
										$this->mongo_db->where($condition)->inc('req_received', 1)->update(DRIVERS);
									}
								}
								$requested_drivers_final = array_unique($requested_drivers);
								$this->app_model->update_details(RIDES, array("requested_drivers"=>$requested_drivers_final), array('ride_id' => $ride_id));
								
                            }
                        }
                    }
                }
            }
        }
    }

}

/* End of file ride_later.php */
/* Location: ./application/controllers/mobile/ride_later.php */