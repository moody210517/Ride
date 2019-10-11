<?php
 if (!defined('BASEPATH'))  exit('No direct script access allowed');
/**
*
*	User 
* 
*	@package	CI
*	@subpackage	Controller
*	@author	Casperon
*
**/
class Rider extends MY_Controller {

    public function __construct() {
        parent::__construct();
        $this->load->helper(array('cookie', 'date', 'form', 'email','ride_helper'));
        $this->load->library(array('encrypt', 'form_validation'));
        $this->load->model('user_model');



        if ($this->checkLogin('U') != '') {
            $this->data['rider_info'] =$chech_deivers= $this->user_model->get_all_details(USERS, array('_id' => MongoID($this->checkLogin('U')),'status' => array('$ne' => 'Deleted')));
           
			if($chech_deivers->num_rows()==0)
            {
                redirect('rider/logout');
            }
            $rider_info = $this->data['rider_info']->row();
        } else {
            $this->setErrorMessage('error', 'You must login first', 'driver_login_first');
            $nextUrl = current_url();
            redirect('rider/login?action=' . $nextUrl);
        }
    }
	/**
	* 
	* its redirect to the user profile page
	*
	* @return HTTP REDIRECT,user profile page
	*
	**/	
    public function index() {
        if ($this->checkLogin('U') == '') {
            $this->setErrorMessage('error', 'You must login first', 'driver_login_first');
            redirect('rider/login');
        } else {
            redirect('rider/profile');
        }
    }
	/**
	* 
	* its display the profile page
	*
	* @return HTML,profile page
	*
	**/	
    public function profile_view() {
		if ($this->lang->line('rider_profile_profile') != '') {
			$this->data['heading']= stripslashes($this->lang->line('rider_profile_profile')); 
		}else{
			$this->data['heading'] = "Profile";
		}
		$this->data['sideMenu'] = 'profile';
        $this->load->view('site/user/profile', $this->data);
    }
    /**
	* 
	* its display the user ride list page
	*
	* @param string $pg  pagination
	* @param string $list  ride status
	* @return HTML,user ride list page
	*
	**/	
    function display_my_rides() {

        $this->data['sideMenu'] = 'rides';
        $limit = 10;
        $offset = 0;
        if (isset($_SERVER['HTTPS'])) {
            $protocol = ($_SERVER['HTTPS'] && $_SERVER['HTTPS'] != "off") ? "https://" : "http://";
        } else {
            $protocol = 'http://';
        }
        $currentURL = $protocol . $_SERVER['HTTP_HOST'] . $_SERVER['REQUEST_URI'];

        if (substr_count($currentURL, '?pg=') == 0) {
            $curUrl = @explode('&pg=', $currentURL);
        } else {
            $curUrl = @explode('?pg=', $currentURL);
        }
        $currentPage = 1;
        $npage = intval($this->input->get('pg'));
        if ($npage > 0) {
            $currentPage = $npage;
        }

        if ($npage != 0) {
            $paginationVal = $this->input->get('pg') * $limit;
            $offset = $paginationVal;
        }

        $newPage = $currentPage + 1;
        if (substr_count($curUrl[0], '?') >= 1) {
            $qry_str = $curUrl[0] . '&pg=' . $newPage;
        } else {
            $qry_str = $curUrl[0] . '?pg=' . $newPage;
        }

        $user_id = $this->checkLogin('U');
        $list = (empty($_GET['list'])) ? 'all' : $_GET['list'];
        switch ($list) {
            case 'all':
                $list = 'all';
                break;
            case 'upcoming':
                $list = 'upcoming';
                break;
            case 'cancelled':
                $list = 'cancelled';
                break;
            case 'onride':
                $list = 'onride';
                break;
            case 'completed':
                $list = 'completed';
                break;
            default:
                $list = 'all';
                break;
        }
        $this->data['findpage'] = $list;
        $getFields = array('_id', 'ride_status', 'ride_id', 'booking_information', 'ride_status', 'pay_status','driver');
        $this->data['ridesList'] = $ridesList = $this->user_model->get_ride_list($user_id, $list, $getFields, $limit, $offset);
        
        if ($ridesList->num_rows() > 0) {
            $paginationDisplay = '<a title="' . $newPage . '" class="scrolling-btn-more" href="' . $qry_str . '" style="display: none;">See More List</a>';
        } else {
            $paginationDisplay = '<a title="' . $newPage . '" class="scrolling-btn-more" style="display: none;">No More List</a>';
        }
        $this->data['paginationDisplay'] = $paginationDisplay;
        if ($this->lang->line('rider_profile_my_rides') != '')
            $driver_my_rides = stripslashes($this->lang->line('rider_profile_my_rides'));
        else
            $driver_my_rides = 'My Rides';
        $this->data['heading'] = $driver_my_rides;
        $this->load->view('site/rides/display_rides', $this->data);
    }
	/**
	* 
	* Its used to view the ride detail information page
	*
	* @param string $ride_id  ride id
	* @return HTML,user ride detail information page
	*
	**/	
    function view_ride_details() {
        $this->data['sideMenu'] = 'rides';
        $ride_id = $this->uri->segment(3);
		
        if ($this->lang->line('rider_profile_my_ride_detail') != ''){
			$this->data['heading'] = stripslashes($this->lang->line('rider_profile_my_ride_detail'));
		}else{
			$this->data['heading'] = "My Ride Details";
		}
		$driver_assigned = FALSE;
        $condition = array('user.id' => $this->checkLogin('U'), 'ride_id' => $ride_id);
        $this->data['rides_details'] = $rides_details = $this->user_model->get_all_details(RIDES, $condition); 
		if(isset($rides_details->row()->driver['id']) && $rides_details->row()->driver['id'] != ''){
			$condition = array('_id' => MongoID($rides_details->row()->driver['id']));
			$this->data['driver_details'] = $driver_details = $this->user_model->get_selected_fields(DRIVERS, $condition,array('driver_name','image','avg_review')); 
            $driver_assigned = TRUE;
		}
        $this->data['driver_assigned'] = $driver_assigned;
        
		$favcondition = array('user_id' => MongoID($this->checkLogin('U')));
		$this->data['favouriteList'] = $favouriteList = $this->user_model->get_all_details(FAVOURITE, $favcondition);   
		
		$this->data['get_ratings'] = 'No'; 
		if(($rides_details->row()->ride_status == 'Finished' || $rides_details->row()->ride_status == 'Completed') && !isset($rides_details->row()->driver_review_status)){
			$condition = array('option_holder' => 'driver', 'status' => 'Active');
			$this->data['rating_options'] = $rating_options = $this->user_model->get_all_details(REVIEW_OPTIONS, $condition);
			$this->data['get_ratings'] = 'Yes';  
		}
        
        if ($rides_details->num_rows() == 1) {

			if (isset($rides_details->row()->booking_information['pickup']['latlong'])) {
                $latlong = @implode(array_reverse($rides_details->row()->booking_information['pickup']['latlong']), ',');
            } else {
                $latlong = '';
            }
            $config['center'] = $latlong;
            $config['zoom'] = '18';
            $config['draggable'] = false;
            $config['disableMapTypeControl'] = true;
            $config['fullscreenControl'] = false;
			$config['language'] = $this->data['langCode'];
            $this->googlemaps->initialize($config);
            $marker = array();
            $marker['position'] = $latlong;
            $this->googlemaps->add_marker($marker);
            $this->data['map'] = $this->googlemaps->create_map();

            $this->load->view('site/rides/view_rides', $this->data);
        } else {
            $this->setErrorMessage('error', 'This ride is no longer available', 'driver_no_longer_avail');
            redirect('rider/my-rides');
        }
    }
	/**
	* 
	* Its used to view the user emergency contact details page
	*
	* @return HTML,user emergency contact details page
	*
	**/
    function emergency_contact() {

        if ($this->lang->line('driver_emergency_contact_details') != '')
            $driver_emergency_contact_details = stripslashes($this->lang->line('driver_emergency_contact_details'));
        else
            $driver_emergency_contact_details = "Emergency Contact Details";
        $this->data['sideMenu'] = 'emergency';
        $this->data['heading'] = $driver_emergency_contact_details;
        $this->load->view('site/user/emergency_contact', $this->data);
    }
	/**
	* 
	* Its used to update user emergency contact
	*
	* @param string $em_name  emergency contact user name
	* @param string $em_email  emergency contact email
	* @param string $em_mobile  emergency contact mobile number
	* @param string $em_mobile_code  emergency contact mobile number
	* @return HTTP REDIRECT,user emergency contact page
	*
	**/
    function update_emergency_contact() {

        $user_id = $this->checkLogin('U');
        $em_name = $this->input->post('em_name');
        $em_email = $this->input->post('em_email');
        $em_mobile = $this->input->post('em_mobile');
        $em_mobile_code = $this->input->post('em_mobile_code');

        $condition = array('_id' => MongoID($user_id));
        $rider_info = $this->data['rider_info'];


        $email_verify_status = 'No';
        $mobile_verify_status = 'No';
        if ($rider_info->row()->emergency_contact['em_email']) {
            if (isset($rider_info->row()->emergency_contact['verification']['email']))
                $email_verify_status = $rider_info->row()->emergency_contact['verification']['email'];
            if (isset($rider_info->row()->emergency_contact['verification']['mobile']))
                $mobile_verify_status = $rider_info->row()->emergency_contact['verification']['mobile'];
            if ($rider_info->row()->emergency_contact['em_email'] != $em_email) {
                $email_verify_status = 'No';
            }
            if ($rider_info->row()->emergency_contact['em_mobile'] != $em_mobile) {
                $mobile_verify_status = 'No';
            }
        }

        $vfyArr = array('email' => $email_verify_status, 'mobile' => $mobile_verify_status);

        if ($rider_info->num_rows() == 1) {
            if ($rider_info->row()->email != $em_email && ($rider_info->row()->phone_number != $em_mobile || $rider_info->row()->country_code != $em_mobile_code)) {
            
                $em_dataArr = array('emergency_contact.em_name' => $em_name, 'emergency_contact.em_email' => $em_email, 'emergency_contact.em_mobile' => $em_mobile, 'emergency_contact.em_mobile_code' => $em_mobile_code, 'emergency_contact.verification' => $vfyArr);

                $em_dataMailArr = array('em_name' => $em_name, 'em_email' => $em_email, 'em_mobile' => $em_mobile, 'em_mobile_code' => $em_mobile_code, 'verification' => $vfyArr);

                if (!isset($rider_info->row()->emergency_contact['em_name'])) {
                    $em_dataArr = array('emergency_contact' => $em_dataMailArr);
                }

                if (isset($rider_info->row()->emergency_contact['em_email'])) {
                    $olderEmail = $rider_info->row()->emergency_contact['em_email'];
                }
				 if (isset($rider_info->row()->emergency_contact['em_mobile'])) {
                    $olderMobile = $rider_info->row()->emergency_contact['em_mobile'];
                }

                $this->user_model->update_details(USERS, $em_dataArr, $condition);


                if (isset($rider_info->row()->emergency_contact)) {
			      
                    if ($olderEmail == $em_email && $olderMobile==$em_mobile) {
                        $this->setErrorMessage('success', 'Emergency contact updated successfully', 'driver_emergency_contact_updated');
                    } else {
						$this->emergency_contact_verification_request($rider_info, $em_dataMailArr);
                        $this->setErrorMessage('success', 'Emergency contact added successfully', 'driver_emergency_contact_added');
                    }
                } else {
					$this->emergency_contact_verification_request($rider_info, $em_dataMailArr);
                    $this->setErrorMessage('success', 'Emergency contact added successfully', 'driver_emergency_contact_added');
                }
            } else {
                $this->setErrorMessage('error', 'Sorry, You can not add your own contact details', 'driver_cannot_add_own');
            }
        } else {
            $this->setErrorMessage('error', 'Sorry, Your records not found', 'driver_your_record_not_found');
        }
        redirect('rider/emergency-contact');
    }
		
		
		
		
		 /**
     * 
     * This function loads the change password form
     */
    public function change_password_form() {

        if ($this->lang->line('dash_change_password') != '')
            $dash_change_password = stripslashes($this->lang->line('dash_change_password'));
        else
            $dash_change_password = 'Change Password';

        $this->data['heading'] = $dash_change_password;
		$this->data['sideMenu'] = 'password';
        if ($this->checkLogin('U') == '') {
            redirect('rider');
        } else {
            $this->load->view('site/user/changepassword.php', $this->data);
        }
    }

    /**
     * 
     * This function validate the change password form
     * If details are correct then change the driver password
     */
    public function change_password() {
        if ($this->checkLogin('U') == '') {
            redirect('rider');
        }
		
		 $pwd = md5($this->input->post('password'));
		 $new_pwd = $this->input->post('new_password');
		
        if ($pwd == '' && $new_pwd  == '') {
			$this->setErrorMessage('error', 'Please enter all the required fields');
            redirect('site/rider/change_password_form');
        } else {
            $user_id = $this->checkLogin('U');
           
            $condition = array('_id' => MongoID($user_id), 'password' => $pwd);
            $query = $this->user_model->get_all_details(USERS, $condition);
            if ($query->num_rows() == 1) {
                $newdata = array('password' => md5($new_pwd));
                $condition = array('_id' => MongoID($user_id));
                $this->user_model->update_details(USERS, $newdata, $condition);
                $this->setErrorMessage('success', 'Password changed successfully', 'dash_password_changed_successfully');
                redirect('site/rider/change_password_form');
            } else {
                $this->setErrorMessage('error', 'Invalid current password', 'dash_invalid_current_password');
                redirect('site/rider/change_password_form');
            }
            redirect('site/rider/profile_view');
        }
    }

	/**
	* 
	* Its used to send mail and message to emergency contact person
	*
	* @param string $user_info  user record object
	* @param string $contactArr  contact array
	* @return ARRAY,callback to update emergency contact page
	*
	**/
    public function emergency_contact_verification_request($user_info, $contactArr) {
        $phone_code = $contactArr['em_mobile_code'];
        $phone_number = $contactArr['em_mobile'];
        if (substr($phone_code, 0, 1) == '+') {
            $phone_code = $phone_code;
        } else {
            $phone_code = '+' . $phone_code;
        }
        $otp_number = rand(10000, 99999);
        $from = $this->config->item('twilio_number');
        $to = $phone_code . $phone_number;
        $em_user_name = $contactArr['em_name'];
        $em_user_email = $contactArr['em_email'];
        $user_name = $user_info->row()->user_name;
        $user_id = $user_info->row()->_id;

		$smsInfo = array("em_user_name"=>$em_user_name,
										"user_name"=>$user_name,
										"otp_number"=>$otp_number,
										"phone_code"=>$phone_code,
										"phone_number"=>$phone_number
									);
        $this->sms_model->emergency_contact_update($smsInfo);

        $condition = array('_id' => MongoID($user_id));
        $this->user_model->update_details(USERS, array('emergency_contact.mobile_otp' => $otp_number), $condition);

        $responseArr['otp'] = $otp_number;
        if ($this->config->item('twilio_account_type') == 'sandbox') {
            $otp_status = 'development';
        } else {
            $otp_status = 'production';
        }
        $responseArr['otp_status'] = $otp_status;
        $newsid = '5';
        $confirm_link = base_url() . 'emergency-contact/confirm?c=' . md5($otp_number) . '&u=' . $user_id;
        $template_values = $this->user_model->get_email_template($newsid,$this->data['langCode']);
		$adminnewstemplateArr = array('email_title' => $this->config->item('email_title'), 'logo' => $this->config->item('logo_image'), 'footer_content' => $this->config->item('footer_content'), 'meta_title' => $this->config->item('meta_title'), 'site_contact_mail' => $this->config->item('site_contact_mail'));
		extract($adminnewstemplateArr);
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
			
		$sender_email = $this->config->item('site_contact_mail');
        $sender_name = $this->config->item('email_title');
        
        $email_values = array('mail_type' => 'html',
            'from_mail_id' => $sender_email,
            'mail_name' => $sender_name,
            'to_mail_id' => $em_user_email,
            'subject_message' => $template_values['subject'],
            'body_messages' => $message
        );
        $email_send_to_common = $this->user_model->common_email_send($email_values);
        return $responseArr;
    }
	/**
	* 
	* Its used to send emergency mail to emergency contact person
	*
	* @return HTTP REDIRECT,callback emergency contact page
	*
	**/
    public function emergency_alert_notification() {

        $user_id = $this->checkLogin('U');
        $latitude = 13.057215;
        $longitude = 80.253157;


        if ($latitude != '' && $longitude != '') {
            $condition = array('_id' => MongoID($user_id));
            $checkUser = $this->user_model->get_selected_fields(USERS, $condition, array('_id', 'user_name', 'emergency_contact'));
            if ($checkUser->num_rows() == 1) {
                if (isset($checkUser->row()->emergency_contact)) {
                    if (count($checkUser->row()->emergency_contact) > 0) {

                        $latlng = $latitude . ',' . $longitude;
                       # $gmap = file_get_contents("http://maps.googleapis.com/maps/api/geocode/json?latlng=" . $latlng . "&sensor=false");
                        #$mapValues = json_decode($gmap)->results;
                        #$formatted_address = $mapValues[0]->formatted_address;
				    $formatted_address = 'N/A - ( GPS Not Detected )';

                        $this->send_alert_notification_to_emergency_contact($checkUser->row()->user_name, $checkUser->row()->emergency_contact, $formatted_address);
                        $this->setErrorMessage('success', 'Alert notification sent successfully', 'driver_alert_sent_success');
                    } else {
                        $this->setErrorMessage('error', 'Emergency contact is not available', 'driver_emergency_contact_na');
                    }
                } else {
                    $this->setErrorMessage('error', 'Sorry, You have not set emergency contact yet', 'driver_you_have_not_set_emergency_contact');
                }
            } else {
                $this->setErrorMessage('error', 'This user does not exist', 'driver_user_not_exist');
            }
        } else {
            $this->setErrorMessage('error', 'Not able to find your current location for send with alert notification.', 'driver_not_able_to_find_location');
        }
        redirect('rider/emergency-contact');
    }

    public function send_alert_notification_to_emergency_contact($user_name, $contactArr, $currentLocation = '') {
        /* ---------------SMS--------------------- */
        $phone_code = $contactArr['em_mobile_code'];
        $phone_number = $contactArr['em_mobile'];
        if (substr($phone_code, 0, 1) == '+') {
            $phone_code = $phone_code;
        } else {
            $phone_code = '+' . $phone_code;
        }

        $from = $this->config->item('twilio_number');
        $to = $phone_code . $phone_number;
        $em_user_name = $contactArr['em_name'];
        $em_user_email = $contactArr['em_email'];
        $user_name = $user_name;
		
		$smsInfo = array("em_user_name"=>$em_user_name,
										"user_name"=>$user_name,
										"phone_code"=>$phone_code,
										"phone_number"=>$phone_number
									);
        $this->sms_model->emergency_alert($smsInfo);


        if ($this->config->item('twilio_account_type') == 'sandbox') {
            $otp_status = 'development';
        } else {
            $otp_status = 'production';
        }
        $responseArr['otp_status'] = $otp_status;

        /* --------------------Email-------------------- */
        $newsid = '6';
        $template_values = $this->user_model->get_email_template($newsid,$this->data['langCode']);
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
		$sender_email = $this->config->item('site_contact_mail');
        $sender_name = $this->config->item('email_title');
        
        $email_values = array('mail_type' => 'html',
            'from_mail_id' => $sender_email,
            'mail_name' => $sender_name,
            'to_mail_id' => $em_user_email,
            'subject_message' =>$template_values['subject'],
            'body_messages' => $message
        );
        $email_send_to_common = $this->user_model->common_email_send($email_values);
        return $responseArr;
    }

    /*     * *
     *
     * This function loads the riders rate card
     *
     */

    public function display_rate_card() {
        $this->data['sideMenu'] = 'ratecard';

        if ($this->lang->line('driver_rate_card') != '')
            $heading = str_replace('{SITENAME}', $this->config->item('email_title'), stripslashes($this->lang->line('driver_rate_card')));
        else
            $heading = $this->config->item('email_title') . " Rate Card";

        $this->data['heading'] = $heading;

        $this->data['locationsList'] = $locationsList = $this->user_model->get_selected_fields(LOCATIONS, array('status' => 'Active','fare' => array('$exists' => 1),'avail_category' => array('$exists' => 1)), array('city', '_id', 'avail_category','fare'),array('city' => 1));
        
        $getcatList = $this->user_model->get_selected_fields(CATEGORY,array(),array('_id'));
        $cat_idsArr = array();
        foreach($getcatList->result() as $cats){
            $cat_idsArr[] = (string)$cats->_id;
        }
        
        $locationsArr = array();
        foreach($locationsList->result() as $location){
            if(!empty($location->fare) && !empty($location->avail_category)){
               
                $faredCats = array();
                $cat_fare = array_keys($location->fare);
                $faredCats = array_intersect($location->avail_category,$cat_fare);
                $chckCats = array_intersect($faredCats,$cat_idsArr);
                
                
                if(count($faredCats) == count($chckCats)){
                    $locationsArr[] = $location;
                }
            }
        }
        
        $this->data['locationsArr'] = $locationsArr;
      

        if ($this->input->get('loc') != '') {
            $location_id = (string) $this->input->get('loc');
        } else {
            $location_id  = '';
        }

        if ($this->input->get('cat') != '') {
            $category_id = (string) $this->input->get('cat');
        } else {
            $category_id = '';
        }
        if($location_id != ''){
            $this->data['categoryOptions'] = $this->get_rate_card_city_categories_ajax('cntl',$location_id);
        } else {
            $this->data['categoryOptions'] = '';
        }
        
        $this->load->view('site/user/rate_card', $this->data); 

    }
    /* * *
     *
     * This function loads the dectar money page
     *
     * * */
    function display_money_page() {   
        $user_id = $this->checkLogin('U');
        if ($this->lang->line('driver_sitename_money') != ''){
			$disp_msg = str_replace('{SITENAME}', $this->config->item('email_title'), stripslashes(trim($this->lang->line('driver_sitename_money'))));
		} else{
			$disp_msg = $this->config->item('email_title') . ' Money';
		}
        $this->data['heading'] = $disp_msg;
        $this->data['wallet_balance'] = $this->user_model->get_selected_fields(WALLET, array('user_id' => MongoID($user_id)), array('total'))->row()->total;
        $this->data['sideMenu'] = 'wallet';
        $this->load->view('site/user/my_money', $this->data);

    }
    /* * *
     *
     * This function loads the dectar money transactions history page
     *
     */

    function display_transaction_list() {


        if ($this->lang->line('driver_sitename_money_transaction') != '')
            $heading = str_replace('{SITENAME}', $this->config->item('email_title'), stripslashes($this->lang->line('driver_sitename_money_transaction')));
        else
            $heading = ucfirst($this->config->item('email_title')) . " Money Transactions";



        $this->data['sideMenu'] = 'wallet';
        $this->data['heading'] = $heading;
        $user_id = $this->checkLogin('U');
        $this->data['txn_type'] = $transType = $this->input->get('q');
       
		
		
		if ($transType == 'credit') {
            $transType_cond='CREDIT';
        } else if ($transType == 'debit') {
            $transType_cond='DEBIT'; 
        } else {
            $transType_cond='';
        }
		
        #echo '<pre>'; print_r($condition); die;
        #$this->data['wallet_history'] = $wallet_history = $this->user_model->get_all_details(WALLET, $condition);
        
		#print_r($transType_cond);die;
		$wallet_history = $this->user_model->user_transaction($user_id, $transType_cond);
        #echo '<pre>'; print_r($wallet_history['result']); die;
		$ref_id = 0;
		if(isset($wallet_history['result'][0]['transactions'])){
			$transactionsList = $wallet_history['result'][0]['transactions'];
			foreach($transactionsList as $referral){
				if(isset($wallet_history['result'][0]['transactions'][$ref_id]['credit_type'])){
					if($wallet_history['result'][0]['transactions'][$ref_id]['credit_type'] == 'referral'){
						$userName = $this->user_model->get_selected_fields(USERS,array('_id' => MongoID($referral['ref_id'])),array('user_name'));
						if(isset($userName->row()->user_name)){
							$wallet_history['result'][0]['transactions'][$ref_id]['ref_user_name'] = $userName->row()->user_name;
						}
					}
				}
				$ref_id++;
			}
		}
		
		
        $this->data['wallet_history'] =$wallet_history['result'];
		
        $this->load->view('site/user/display_transaction_list', $this->data);
    }

   
    function display_share_earnings() {

        if ($this->lang->line('driver_share_earnings') != '')
            $driver_share_earnings = stripslashes($this->lang->line('driver_share_earnings'));
        else
            $driver_share_earnings = 'Share and Earnings';


        if ($this->lang->line('driver_sign_up_with_my_code') != '')
            $driver_sign_up_with_my_code = stripslashes($this->lang->line('driver_sign_up_with_my_code'));
        else
            $driver_sign_up_with_my_code = 'Sign up with my code';

        if ($this->lang->line('driver_to_get') != '')
            $driver_to_get = stripslashes($this->lang->line('driver_to_get'));
        else
            $driver_to_get = 'to get';

        if ($this->lang->line('driver_bonus_amount_on') != '')
            $driver_bonus_amount_on = str_replace('{SITENAME}', $this->config->item('email_title'), stripslashes($this->lang->line('driver_bonus_amount_on')));
        else
            $driver_bonus_amount_on = "bonus amount on " . $this->config->item('email_title');

        $this->data['sideMenu'] = 'share_earnings';
        $this->data['heading'] = $driver_share_earnings;
        $shareDesc = $driver_sign_up_with_my_code .' '. $this->data['rider_info']->row()->unique_code . " " . $driver_to_get . "   " . $this->data['dcurrencyCode'] . " " . number_format($this->config->item('welcome_amount'), 2) . " " . $driver_bonus_amount_on;
		
		#echo  $shareDesc; die;
		
        $this->data['shareDesc'] = $shareDesc;
        $this->load->view('site/user/display_share_earnings', $this->data);
    }

    function display_earnings() { 
        if ($this->lang->line('driver_emergency_contact_details') != '')
            $driver_emergency_contact_details = stripslashes($this->lang->line('driver_emergency_contact_details'));
        else
            $driver_emergency_contact_details = 'Emergency Contact Details';

        $this->data['heading'] = $driver_emergency_contact_details;
        $this->load->view('site/user/display_earnings', $this->data);
    }

    function update_rider_profile() {
        #echo '<pre>'; print_r($_POST); die;
        $this->data['rider_info'] = $this->user_model->get_all_details(USERS, array('_id' => MongoID(trim($this->checkLogin('U')))));
        $rider_info = $this->data['rider_info']->row();
		$image_name = '';
            if ($_FILES['image']['name'] != '') {
                $config['overwrite'] = false;
                $config['encrypt_name'] = TRUE;
                $config['allowed_types'] = 'jpg|jpeg|gif|png|bmp';
                $config['max_size'] = 2000;
                $config['upload_path'] = 'images/users';
                $this->load->library('upload', $config);
                
                if ($this->upload->do_upload('image')) {
                    $imageData = $this->upload->data();
                    $this->ImageResizeWithCrop(600, 600, $imageData['file_name'], './images/users/');
                    @copy('./images/users/' . $imageData['file_name'], './images/users/thumb/' . $imageData['file_name']);
                    $this->ImageResizeWithCrop(150, 150, $imageData['file_name'], './images/users/thumb/');
                    $notificationImageName = $imageData['file_name'];
                    $profile_pic_path = $notificationImageName;
                } else {
                    $error = array('error' => $this->upload->display_errors());
                    $this->setErrorMessage('error', $this->upload->display_errors());
                    redirect('rider/profile');
                }
            } else {
                $profile_pic_path = $rider_info->image;
            }
            $otpVerified = $this->input->post('otpVerified');
            $mobileInput = $this->input->post('mNumber');
            $mNumber = $this->input->post('changed_number');
            $isMobChanged = $this->input->post('isMobileNumberChanged');
            //  print_r("<pre>".$otpVerified);
            $mobile = $rider_info->phone_number;
            $country_code = $rider_info->country_code;
            if ($isMobChanged == 'changed' && $otpVerified != 'true') {
                $this->setErrorMessage('error', 'Verify Your OTP, Try Again!', 'driver_verify_your_otp');
				redirect('site/rider/profile_view');
            } else if ($mNumber != '' && ($mNumber != $rider_info->phone_number || $this->input->post('country_code') != $rider_info->country_code)) {
                if ($otpVerified == 'true') {
					if($this->session->userdata(APP_NAME.'otp_phone_number') != $this->input->post('changed_number') || $this->session->userdata(APP_NAME.'otp_country_code')!=$this->input->post('country_code')){
						$this->setErrorMessage('error', 'Entered and Verified numbers are mismatched', 'number_otp_mismatched');
						redirect('site/rider/profile_view');
					}else{
						$mobile = $this->session->userdata(APP_NAME.'otp_phone_number'); #$this->input->post('changed_number');
						$country_code = $this->session->userdata(APP_NAME.'otp_country_code'); #$this->input->post('country_code');
					}
                } else {
                    $this->setErrorMessage('error', 'Verify Your OTP, Try Again!', 'driver_verify_your_otp');
					redirect('site/rider/profile_view');
                }
            }
			
			$user_name = trim($this->input->post('user_name'));
			$stringOldPassword = trim($this->input->post('old_password'));
            $stringPassword = trim($this->input->post('password'));
            $stringConfirmPassword = trim($this->input->post('confirm_password'));
		    $password = $rider_info->password;
			if ($stringPassword != '' && $stringOldPassword !='') {
			 if((md5($stringOldPassword) == $rider_info->password)){
				if ($stringPassword === $stringConfirmPassword) {
				   $password = md5($stringPassword);
				} else {
                     $this->setErrorMessage('error', 'Your password doesn\'t Match', 'driver_pwd_doesnt_match');
				   redirect('site/rider/profile_view');
                  }
			}else {
                    $this->setErrorMessage('error', 'Your old password doesn\'t Match', 'driver_old_pwd_doesnt_match');
				  redirect('site/rider/profile_view');	
             }	
                
            }
		   $mId = MongoID($this->checkLogin('U'));
            $condition = array('_id' => $mId);
            $updating = $this->user_model->update_details(USERS, array('email' => $rider_info->email, 'password' => $password, 'country_code' => $country_code, 'phone_number' => $mobile, 'image' => $profile_pic_path,'user_name' => $user_name,'modified' => date("Y-m-d H:i:s")), $condition);
            if ($updating) {
                $this->setErrorMessage('success', 'Profile Updated Successfully!','profile_updated');
            }
            redirect('rider/profile');
        
    }

    /**
    * This functions loads the language settings form
    * */

    public function language_settings_form() {

        if ($this->lang->line('driver_set_your_lang_pref') != '')
            $driver_set_your_lang_pref = stripslashes($this->lang->line('driver_set_your_lang_pref'));
        else
            $driver_set_your_lang_pref = 'Set Your Language Preference';


        $this->data['sideMenu'] = 'language_settings';
        $this->data['heading'] = $driver_set_your_lang_pref;
        $this->data['languageList'] = $languageList = $this->user_model->get_all_details(LANGUAGES, array('status' => 'Active'));
        $this->load->view('site/user/language_settings', $this->data);
    }
	
	/**
    * This functions loads the language settings form
    * */
	function display_fav_locations() {
        if ($this->lang->line('user_favourite_locations') != '')
            $user_favourite_locations = stripslashes($this->lang->line('user_favourite_locations'));
        else
            $user_favourite_locations = 'Favourite Locations';
		$favcondition = array('user_id' => MongoID($this->checkLogin('U')));
		$this->data['favouriteList'] = $favouriteList = $this->user_model->get_all_details(FAVOURITE, $favcondition); 
		#echo '<pre>'; print_r($favouriteList->result()); die;
		$this->data['sideMenu'] = 'fav_locations';		
        $this->data['heading'] = $user_favourite_locations;
        $this->load->view('site/user/fav_locations', $this->data);
    }
    /**
    * This functions loads the ride booking form
    * */
    public function booking_ride_form() {
		$this->data['inputArr'] = $_GET;
        $this->data['sideMenu'] = 'bookride';
        if ($this->lang->line('book_ride_now') != '')
           $book_ride_now = stripslashes($this->lang->line('book_ride_now'));
        else
            $book_ride_now = 'Book your ride now';
            
        if ($this->lang->line('rider_category_type') != '') $cType = stripslashes($this->lang->line('rider_category_type')); else $cType = 'Category Type';
            
        $this->data['heading'] = $book_ride_now;
        $this->data['vehicleTypes'] = $this->user_model->get_all_details(CATEGORY, array('status' => 'Active'));
        
        $this->data['booking_data'] = $booking_data = $this->session->userdata(APP_NAME.'_session_tmp_booking_data');
        $this->session->set_userdata(APP_NAME.'_session_tmp_booking_data',array());
        
        $drop_options = array('' => $cType);
        if(isset($booking_data['pickup_lat']) && $booking_data['pickup_lat'] != '' && isset($booking_data['pickup_lon']) && $booking_data['pickup_lon'] != ''){
            $drop_options = $this->get_category_from_location($booking_data['pickup_lat'],$booking_data['pickup_lon'],'ctlr');
        }
        
        $this->data['cat_drop_options'] = $drop_options; 
        
        $this->load->view('site/rides/booking_ride', $this->data);
    }
	
	/**
     *
     * This Function used for booking a ride
     *
     * */
    public function booking_ride() {
		
		/*** language ***/
		if ($this->lang->line('no_cabs_available') != '') $no_cabs_available = stripslashes($this->lang->line('no_cabs_available')); else $no_cabs_available = 'No cabs available nearby';
		if ($this->lang->line('sorry_dont') != '') $sorry_dont = stripslashes($this->lang->line('sorry_dont')); else $sorry_dont = 'Sorry ! We do not provide services in your city yet.';
		if ($this->lang->line('invalid_user') != '') $invalid_user = stripslashes($this->lang->line('invalid_user')); else $invalid_user = 'Invalid User';
		if ($this->lang->line('please_provide_a_valid_informations') != '') $please_provide_a_valid_informations = stripslashes($this->lang->line('please_provide_a_valid_informations')); else $please_provide_a_valid_informations = 'The given informations are incorrect, please provide a valid informations';
		if ($this->lang->line('after_one_from_now') != '') $after_one_from_now = stripslashes($this->lang->line('after_one_from_now'));
		else $after_one_from_now = 'You can book ride only after one hour from now'; 
		if ($this->lang->line('error_in_connection') != '') $error_in_connection = stripslashes($this->lang->line('error_in_connection')); else $error_in_connection = 'Error in connection';
		if ($this->lang->line('ride_booked_successfully') != '') $ride_booked_success = stripslashes($this->lang->line('ride_booked_successfully')); else $ride_booked_success = 'Your ride has been booked successfully';                

		/*** language ***/
		
		
		$returnArr['status'] = '0';
		$returnArr['response'] = '';
        try {
            $user_id = $this->checkLogin('U');
            $pickup = $this->input->post('pickup_location');
            $pickup_lat = $this->input->post('pickup_lat');
            $pickup_lon = $this->input->post('pickup_lon');
            $category = $this->input->post('category');
            $type = $this->input->post('type');
            $pickup_datetime = $this->input->post('pickup_date_time');
            $code = $this->input->post('code');
            $try = intval($this->input->post('try'));
            $ride_id = (string) $this->input->post('ride_id');

            $drop_loc = trim((string)$this->input->post('drop_location'));
            $drop_lat = $this->input->post('drop_lat');
            $drop_lon = $this->input->post('drop_lon');
            
            if($drop_loc==''){
				$drop_lat = 0;
				$drop_lon = 0;
            }			
			
            $riderlocArr = array('lat' => (string) $pickup_lat, 'lon' => (string) $pickup_lon);
            if ($type == 1) {
                $ride_type = 'Later';
                $pickup_datetime = strtotime($pickup_datetime);
                $pickup_date = date('Y-m-d',$pickup_datetime);
                $pickup_time = date('h:i A',$pickup_datetime );
            } else {
                $ride_type = 'Now';
                $pickup_datetime = time(); 
                $pickup_date = date('Y-m-d');
                $pickup_time = date('h:i A');
            }
            $pickup_timestamp = $pickup_datetime;
            $after_one_hour = strtotime('+1 hour', time());
            if( $type == 0 || ($type ==1 && ($pickup_timestamp > $after_one_hour)) ){

				if (is_array($this->input->post())) {
					$chkValues = count(array_filter($this->input->post()));
				} else {
					$chkValues = 0;
				}

				$bookingInfo = array("user_id"=>$user_id,
									"pickup"=>$pickup,
									"pickup_lat"=>$pickup_lat,
									"pickup_lon"=>$pickup_lon,
									"category"=>$category,
									"type"=>$type,
									"pickup_date"=>$pickup_date,
									"pickup_time"=>$pickup_time,
									"code"=>$code,
									"try"=>$try,
									"ride_id"=>$ride_id,
									"platform"=>"website",
									"drop_loc"=>$drop_loc,
									"drop_lat"=>$drop_lat,
									"drop_lon"=>$drop_lon
								);
				$returnArr = book_a_ride($bookingInfo);
				if($returnArr['status'] == '1'){
					if($type ==1) $this->setErrorMessage('success', $returnArr['response']['message']); 
					$returnArr['status'] = '1';
					$returnArr['response'] = $returnArr['response'];
				}else{
					$returnArr['response'] = $returnArr['response'];
				}
           }else{
				$returnArr['response'] = $after_one_from_now;
			}
        } catch (MongoException $ex) {
            $returnArr['response'] = $error_in_connection;
        }
		echo json_encode($returnArr); die;
    }  
    
    
    /***
    * This function will check that ride is confirmed are not if not then send the request to drivers
    **/
    
    function ride_request_retry(){
        $ride_id = $this->input->post('ride_id');
		$user_id = $this->checkLogin('U');
		$retryReq = array("user_id"=>$user_id,"ride_id"=>$ride_id);
		request_retry($retryReq); die;
    }
    
    /***
    * This function will check that ride is confirmed are not if not then remove the ride record from database
    **/
    
    function ride_request_delete(){
        $ride_id = $this->input->post('ride_id');
		$user_id = $this->checkLogin('U');
		$deleteReq = array("user_id"=>$user_id,"ride_id"=>$ride_id,"mode"=>"auto");
		request_delete($deleteReq); die;
    }
	
	
    /***
    * This function will check that ride is confirmed are not 
    **/
    
    function check_ride_acceptance_status_ajax(){
        $ride_id = $this->input->post('ride_id');
        $returnArr['status']='0';
        if($ride_id != ''){
            $checkRide = $this->app_model->get_selected_fields(RIDES,array('ride_id' => $ride_id),array('ride_status'));
            if(isset($checkRide->row()->ride_status) && $checkRide->row()->ride_status != 'Booked'){
                $returnArr['status']='1';
            }
        }
        echo json_encode($returnArr);
    }
		
	/* 
    * This function  gets the categories of pickup point ( location wise )
    */
    public function get_category_from_location($pickup_lat='0',$pickup_lon = '0',$caller='ajax'){
    
        
        $returnArr['status']='0';
        if($caller == 'ajax'){
            $pickup_lat = $this->input->post('pickup_lat');
            $pickup_lon = $this->input->post('pickup_lon');
        }
        
        
        if ($this->lang->line('operator_select_category') != '') $select_category =  stripslashes($this->lang->line('operator_select_category')); else $select_category = 'Select Category'; 
        
        $location = $this->app_model->find_location(floatval($pickup_lon), floatval($pickup_lat));
        $catsStr = '<option value="">'.$select_category.'</option>';
        $catsArr[''] =  $select_category;
        
        if (!empty($location['result'])) {
            $availCats = $location['result'][0]['avail_category'];
            $catsList = array();
            foreach($availCats as $cid){
                $catsList[] = MongoID($cid);
            }
            $getCats = $this->app_model->get_selected_fields(CATEGORY,array('_id' => array('$in' => $catsList)),array('_id','name','name_languages','icon_car_image'));
            $langCode = $this->data['langCode'];
            foreach($getCats->result() as $cats){
                $category_name = $cats->name;
                
                $icon_car_image = ICON_MAP_CAR_IMAGE;
                if(isset($cats->icon_car_image) && $cats->icon_car_image != ''){
                    if(file_exists(ICON_IMAGE.$cats->icon_car_image)){
                        $icon_car_image = ICON_IMAGE.$cats->icon_car_image;
                    }
                } 
                
                if(isset($cats->name_languages[$langCode]) && $cats->name_languages[$langCode] != '') $category_name = $cats->name_languages[$langCode];
                $catsStr.='<option value="'.$cats->_id.'" data-car="'.$icon_car_image.'">'.$category_name.'</option>';
                $catsArr[(string)$cats->_id] = $category_name;
            }
            $returnArr['status']='1';
        }	
        $returnArr['response'] = $catsStr;
        $json_encode_new = json_encode($returnArr);
        
        if($caller == 'ajax'){
            echo $json_encode_new; 
        } else {
            return $catsArr;
        }
        
    }
		
	/**
	* 
	* This function updates the emergency contact page
	* 
	**/
    function delete_emergency_contact() {
        $user_id = $this->checkLogin('U');
		$rider_info = $this->data['rider_info'];
        if ($rider_info->num_rows() == 1) {
			$condition = array('_id' => MongoID($user_id));
			$em_dataArr = array();
			$this->user_model->update_details(USERS, array('emergency_contact' => $em_dataArr), $condition);
			$this->setErrorMessage('success', 'Emergency contact removed successfully', 'user_emergency_contact_removed');
        } else {
            $this->setErrorMessage('error', 'Sorry, Your records not found', 'driver_your_record_not_found');
        }
        redirect('rider/emergency-contact');
    }
	
	
	/**
	* 
	* This function will save the users ratings in db
	* 
	**/
   public function submit_reviews() {
		#echo '<pre>';  print_r($_POST); die;
        $responseArr['status'] = '0';
        try {
            $ratingsFor = 'driver';
            $ride_id = $this->input->post('ride_id');
            $ratingsArr = $this->input->post('reviews');
            $comments = (string) $this->input->post('comments');

            if ($ride_id != '' && is_array($ratingsArr)) {
                if (count($ratingsArr) > 0) {
                    $rideCond = array('ride_id' => $ride_id);
                    $get_ride_info = $this->user_model->get_selected_fields(RIDES, $rideCond, array('user.id', 'driver.id', 'rider_review_status', 'driver_review_status'));

                    $driversRating = 0;
                    $ridersRating = 0;
                    if (isset($get_ride_info->row()->driver_review_status)) {
                        if ($ratingsFor == 'driver' && ($get_ride_info->row()->driver_review_status == 'Yes')) {
                            $driversRating = 1;
                        }
                    }
                    if (isset($get_ride_info->row()->rider_review_status)) {
                        if ($ratingsFor == 'rider' && ($get_ride_info->row()->rider_review_status == 'Yes')) {
                            $ridersRating = 1;
                        }
                    }

                    if (($ratingsFor == 'driver' && $driversRating == 0) || ($ratingsFor == 'rider' && $ridersRating == 0)) {

                        $user_id = $get_ride_info->row()->user['id'];
                        $driver_id = $get_ride_info->row()->driver['id'];

                        $ratingsArr = array_filter($ratingsArr);
                        $num_of_ratings = 0;
                        $totalRatings = 0;
                        $avg_rating = 0;
                        for ($i = 0; $i < count($ratingsArr); $i++) {
                            $totalRatings = $totalRatings + $ratingsArr[$i]['rating'];
                            $num_of_ratings++;
                        }
                        $avg_rating = number_format(($totalRatings / $num_of_ratings), 2);

                        $ride_dataArr = array('total_options' => $num_of_ratings,
                            'total_ratings' => $totalRatings,
                            'avg_rating' => number_format($avg_rating, 2),
                            'ratings' => $ratingsArr,
                            'comments' => $comments
                        );

						$this->user_model->set_to_field(RIDES, $rideCond, array('ratings.' . $ratingsFor => $ride_dataArr, 'driver_review_status' => 'Yes'));



                        /*                         * *
                         *
                         * Update driver rating records
                         */
                        if ($ratingsFor == 'driver') {
                            $driverCond = array('_id' => MongoID($driver_id));
                            $get_driver_ratings = $this->user_model->get_selected_fields(DRIVERS, $driverCond, array('avg_review', 'total_review'));

                            $driverRateDivider = 1;
                            if (isset($get_driver_ratings->row()->avg_review)) {
                                $existDriverAvgRat = $get_driver_ratings->row()->avg_review;
                                if ($get_driver_ratings->row()->avg_review != '') {
                                    $driverRateDivider++;
                                }
                            } else {
                                $existDriverAvgRat = 0;
                            }

                            if (isset($get_driver_ratings->row()->total_review)) {
                                $existDriverTotReview = $get_driver_ratings->row()->total_review;
                            } else {
                                $existDriverTotReview = 0;
                            }
                            $driverAvgRatings = ($existDriverAvgRat + $avg_rating) / $driverRateDivider;
                            $driverTotalReviews = $existDriverTotReview + 1;

                            $this->user_model->update_details(DRIVERS, array('avg_review' => number_format($driverAvgRatings, 2), 'total_review' => $driverTotalReviews), $driverCond);
                        }


                        $responseArr['status'] = '1';
                        $responseArr['response'] = $this->format_string('Your ratings submitted successfully', 'your_ratings_submitted');
                    } else {
                        $responseArr['response'] = $this->format_string('Already you have submitted your ratings for this ride.', 'already_you_submitted_ratings_for_this_ride');  # as a '.$ratingsFor;
                    }
                } else {
                    $responseArr['response'] = $this->format_string('Submitted ratings fields are not valid', 'submitted_ratings_field_invalid');
                }
            } else {
                $responseArr['response'] = $this->format_string("Some Parameters are missing", "some_parameters_missing");
            }
        } catch (MongoException $ex) {
            $returnArr['response'] = $this->format_string("Error in connection", "error_in_connection");
        }
		if( $responseArr['status'] == '1'){
			$this->setErrorMessage('success', 'Thanks, Your ratings are submitted successfully',"rider_submitted_ratings");
		} else {
			$this->setErrorMessage('error',$returnArr['response']);
		}
		redirect('rider/view-ride/'.$ride_id);
    }
	
	
	 /**
     *
     * This function add the location to favourite list
     *
     * */
    public function add_favourite_location() { 
        $responseArr['status'] = '0';
        $responseArr['message'] = '';

        $title = trim($this->input->post('title'));
        $address = trim($this->input->post('address'));
        $user_id = $this->input->post('user_id');
        $longitude = $this->input->post('longitude');
        $latitude = $this->input->post('latitude');
        $loc_key = 'lat_' . str_replace('.', '-', $longitude) . 'lon_' . str_replace('.', '-', $latitude);


        if (is_array($this->input->post())) {
            $chkValues = count(array_filter($this->input->post()));
        } else {
            $chkValues = 0;
        }

        if ($chkValues >=5) {
            $fav_condition = array('user_id' => MongoID($user_id));
            $checkUserInFav = $this->user_model->get_all_details(FAVOURITE, $fav_condition);
			$titleExist = FALSE;
			if ($checkUserInFav->num_rows() > 0) {
				if (isset($checkUserInFav->row()->fav_location)) {
					$fLoc = $checkUserInFav->row()->fav_location;
					$fvLoc = in_array($title,array_column($fLoc,"title"));
					if($fvLoc) $titleExist = TRUE;
				}
			}
            if (isset($checkUserInFav->row()->fav_location[$loc_key]) || $titleExist == TRUE) {
				if($titleExist){
                
                     if ($this->lang->line('rider_location_name_exst') != '')
                        $responseArr['message'] = stripslashes($this->lang->line('rider_location_name_exst'));
                      else
                        $responseArr['message'] = 'Location name already exist in your favourite list';  
                       
				}else{
					
                     if ($this->lang->line('rider_location_exst') != '')
                        $responseArr['message'] = stripslashes($this->lang->line('rider_location_exst'));
                      else
                        $responseArr['message'] = 'Location already exist in your favourite list'; 
                    
				}
            } else {
                if ($checkUserInFav->num_rows() == 0) {
                    $dataArr = array('user_id' => MongoID($user_id),
                        'fav_location' => array($loc_key => array('title' => $title,
                                'address' => $address,
                                'geo' => array('longitude' => floatval($longitude),
                                    'latitude' => floatval($latitude)
                                )
                            )
                        )
                    );
                    $this->user_model->simple_insert(FAVOURITE, $dataArr);
                    $responseArr['status'] = '1';
                    $responseArr['message'] = $this->format_string('Location added to favourite', 'location_added_to_favourite');
                } else {
                    $dataArr = array('fav_location.' . $loc_key => array('title' => $title,
                            'address' => $address,
                            'geo' => array('longitude' => floatval($longitude),
                                'latitude' => floatval($latitude)
                            )
                        )
                    );
                    $this->user_model->set_to_field(FAVOURITE, $fav_condition, $dataArr);
                    $responseArr['status'] = '1';
                    $responseArr['message'] = $this->format_string('Location added to favourite', 'location_added_to_favourite');
                }
            }
        } else {
			 if ($this->lang->line('some_parameters_missing') != '')
                $some_parameters_missing = stripslashes($this->lang->line('some_parameters_missing'));
              else
                $some_parameters_missing = 'Some Parameters are missing';  
				$responseArr['message'] = $some_parameters_missing;
        }

        $json_encode = json_encode($responseArr, JSON_PRETTY_PRINT);
        echo $this->cleanString($json_encode);
    }

	
	
	 /**
     *
     * This function edit the location from favourite list
     *
     * */
    public function edit_favourite_location() {
        $responseArr['status'] = '0';
        $responseArr['message'] = '';

        $title = trim($this->input->post('title'));
        $address = trim($this->input->post('address'));
        $user_id = $this->input->post('user_id');
        $longitude = $this->input->post('longitude');
        $latitude = $this->input->post('latitude');
        $loc_key = $this->input->post('location_key');
		
		$loc_key_new = 'lat_' . str_replace('.', '-', $longitude) . 'lon_' . str_replace('.', '-', $latitude);

        if (is_array($this->input->post())) {
            $chkValues = count(array_filter($this->input->post()));
        } else {
            $chkValues = 0;
        }

        if ($chkValues >= 5) {
        
            $fav_condition = array('user_id' => MongoID($user_id));
            $checkUserInFav = $this->user_model->get_all_details(FAVOURITE, $fav_condition);
			$titleExist = FALSE;
			if ($checkUserInFav->num_rows() > 0) {
				if (isset($checkUserInFav->row()->fav_location)) {
					$fLoc = $checkUserInFav->row()->fav_location;
					//$fvLoc = in_array($title,array_column($fLoc,"title"));
                    foreach($fLoc as $key => $val){
                        if(isset($val['title']) && $title == $val['title'] && $key != $loc_key){
                            $titleExist = TRUE; break;
                        }
                    }
					
				}
			}
            
            
        
            $fav_condition = array('user_id' => MongoID($user_id));
            $checkUserInFav = $this->user_model->get_all_details(FAVOURITE, $fav_condition);
			$fLs = array();
			if ($checkUserInFav->num_rows() > 0) {
				if (isset($checkUserInFav->row()->fav_location)) {
					$fLoc = $checkUserInFav->row()->fav_location;
					foreach($fLoc as $key=>$value){
						$fLs = array($key=>$value['title']);
					}
				}
			}
			
            if (!isset($checkUserInFav->row()->fav_location[$loc_key])) {
                $responseArr['status'] = '0';
                $responseArr['message'] = $this->format_string('No records found for this location', 'no_records_found_for_location');
            } else {
				
				if($titleExist){
					$responseArr['message'] = $this->format_string('Location name already exist in your favourite list', 'location_name_already_exist_in_favourite');
				}else{
					$dataArr = array('fav_location.' . $loc_key_new => array('title' => $title,
							'address' => $address,
							'geo' => array('longitude' => floatval($longitude),
								'latitude' => floatval($latitude)
							)
						)
					);
					$this->user_model->remove_favorite_location($fav_condition, 'fav_location.' . $loc_key);
					$this->user_model->set_to_field(FAVOURITE, $fav_condition, $dataArr);

					$responseArr['status'] = '1';
					$responseArr['message'] = $this->format_string('Updated successfully', 'updated_successfully');
				}
                
            }
        } else {
            $responseArr['message'] = $this->format_string('Some Parameters are missing', 'some_parameters_missing');
        }
        $json_encode = json_encode($responseArr, JSON_PRETTY_PRINT);
        echo $this->cleanString($json_encode);
    }
	
	 /**
     *
     * This function remove the location from favourite list
     *
     * */
    public function remove_favourite_location() {
        $responseArr['status'] = '0';
        $responseArr['message'] = '';
        $loc_key = $this->input->post('location_key');
        $user_id = $this->input->post('user_id');


        if (is_array($this->input->post())) {
            $chkValues = count(array_filter($this->input->post()));
        } else {
            $chkValues = 0;
        }

        if ($chkValues >= 2) {
            $fav_condition = array('user_id' => MongoID($user_id));
            $checkUserInFav = $this->user_model->get_all_details(FAVOURITE, $fav_condition);

            if (!isset($checkUserInFav->row()->fav_location[$loc_key])) {
                $responseArr['status'] = '0';
                $responseArr['message'] = $this->format_string('No records found for this location', 'no_records_found_for_location');
            } else {
                $this->user_model->remove_favorite_location($fav_condition, 'fav_location.' . $loc_key);
                $responseArr['status'] = '1';
                $responseArr['message'] = $this->format_string('Location removed successfully', 'location_removed_successfully');
            }
        } else {
            $responseArr['message'] = $this->format_string('Some Parameters are missing', 'some_parameters_missing');
        }
        $json_encode = json_encode($responseArr, JSON_PRETTY_PRINT);
        echo $this->cleanString($json_encode);
    }
    
    function get_rate_card_city_categories_ajax($calfrom='view',$location_id=''){
        if($calfrom == 'view'){
            $location_id = $this->input->post('location_id');
        }
        $responseArr['status'] = '0';
        if ($this->lang->line('ride_select_car_type') != '') $ride_select_car_type = stripslashes($this->lang->line('ride_select_car_type')); else $ride_select_car_type = 'Select car type';
        $response = '<option value="">'.$ride_select_car_type.'....</option>';
        if ($location_id != '') {
            $locationsVal = $this->user_model->get_selected_fields(LOCATIONS, array('_id' => MongoID($location_id)), array('currency', 'fare', 'peak_time', 'night_charge', 'service_tax', 'avail_category'));

            if (isset($locationsVal->row()->avail_category) && !empty($locationsVal->row()->avail_category)){ 
                $avail_cat = array();
                $fare_cat = array();
                if (isset($locationsVal->row()->fare)) {
                    if (!empty($locationsVal->row()->fare)) {
                        $fare_cat = array_keys($locationsVal->row()->fare);
                    }
                } 
                $avail_cat = $locationsVal->row()->avail_category;
                $final_cat = array_intersect($fare_cat,$avail_cat);
                $catCond = array('_id', $final_cat, array('status' => 'Active'));
                $RatecategoryList = $categoryList = $this->user_model->get_selected_fields_where_in(CATEGORY, $catCond, array('_id', 'name','name_languages'));
                $langCode = $this->data['langCode'];
                if($RatecategoryList->num_rows() > 0){
                    foreach ($RatecategoryList->result() as $categories) {
                        $category_name = $categories->name;
                        if(isset($categories->name_languages[$langCode ]) && $categories->name_languages[$langCode ] != '') $category_name = $categories->name_languages[$langCode ];
                    
                        $response.= '<option value="'.(string) $categories->_id.'">'.$category_name.'</option>';
                    }
                    $responseArr['status'] = '1';                    
                }
            }
        }
        if($calfrom == 'view'){
            $responseArr['response'] = $response;
            echo json_encode($responseArr, JSON_PRETTY_PRINT);
        } else {
            return $response;
        }
    }    
    
    function get_rate_card_ajax(){
        $location_id = $this->input->post('location_id');
        $category_id = $this->input->post('category_id');
        $responseArr['status'] = '0';
        $responseArr['response'] = '';
        if ($location_id != '' && $category_id != '') {
            $locationsVal = $this->user_model->get_selected_fields(LOCATIONS, array('_id' => MongoID($location_id)), array('currency', 'fare', 'peak_time', 'night_charge', 'service_tax', 'avail_category'));

            if (isset($locationsVal->row()->avail_category)) {
                if (!empty($locationsVal->row()->avail_category)) {
					$avail_cat = array();
					$fare_cat = array();
					if (isset($locationsVal->row()->fare)) {
						if (!empty($locationsVal->row()->fare)) {
							$fare_cat = array_keys($locationsVal->row()->fare);
						}
					}
					$avail_cat = $locationsVal->row()->avail_category;
					$final_cat = array_intersect($fare_cat,$avail_cat);
                    $catCond = array('_id', $final_cat, array('status' => 'Active'));
                    $this->data['RatecategoryList'] = $categoryList = $this->user_model->get_selected_fields_where_in(CATEGORY, $catCond, array('_id', 'name','name_languages'))->result();
                }
            }

            #echo '<pre>'; print_r($locationsVal->row()); die;

            if ($locationsVal->num_rows() > 0) {
               
                
               if (isset($locationsVal->row()->fare[$category_id])) {
                  if ($this->lang->line('driver_first') != '')
                        $first = stripslashes($this->lang->line('driver_first'));
                    else
                        $first = 'First';

                    if ($this->lang->line('driver_after') != '')
                        $after = stripslashes($this->lang->line('driver_after'));
                    else
                        $after = 'After';

                    if ($this->lang->line('driver_ride_time_charges') != '')
                        $ride_time_charges = stripslashes($this->lang->line('driver_ride_time_charges'));
                    else
                        $ride_time_charges = 'Ride time charges';

                    if ($this->lang->line('driver_ride_time_is_free') != '')
                        $driver_ride_time_is_free = stripslashes($this->lang->line('driver_ride_time_is_free'));
                    else
                        $driver_ride_time_is_free = 'ride time is FREE! Wait time is chargeable.';

                    if ($this->lang->line('driver_wait_time_charges') != '')
                        $driver_wait_time_charges = stripslashes($this->lang->line('driver_wait_time_charges'));
                    else
                        $driver_wait_time_charges = 'Waiting time charges';


                    if ($this->lang->line('driver_peak_time_charge') != '')
                        $driver_peak_time_charge = stripslashes($this->lang->line('driver_peak_time_charge'));
                    else
                        $driver_peak_time_charge = 'Peak time charges';


                    if ($this->lang->line('driver_may_applicable_during_high_demand') != '')
                        $driver_may_applicable_during_high_demand = stripslashes($this->lang->line('driver_may_applicable_during_high_demand'));
                    else
                        $driver_may_applicable_during_high_demand = 'Peak time charges may be applicable during hign demand hours and will be conveyed during the booking. This enables us to make more cabs available to you.';


                    if ($this->lang->line('driver_night_time_charges') != '')
                        $driver_night_time_charges = stripslashes($this->lang->line('driver_night_time_charges'));
                    else
                        $driver_night_time_charges = 'Night time charges';


                    if ($this->lang->line('driver_night_time_charges_may_apply') != '')
                        $driver_night_time_charges_may_apply = stripslashes($this->lang->line('driver_night_time_charges_may_apply'));
                    else
                        $driver_night_time_charges_may_apply = 'Night time charges may be applicable during the late night hours and will be conveyed during the booking. This enables us to make more cabs available to you.';


                    if ($this->lang->line('driver_service_tax') != '')
                        $driver_service_tax = stripslashes($this->lang->line('driver_service_tax'));
                    else
                        $driver_service_tax = 'Service Tax';


                    if ($this->lang->line('driver_service_tax_is_payable') != '')
                        $driver_service_tax_is_payable = stripslashes($this->lang->line('driver_service_tax_is_payable'));
                    else
                        $driver_service_tax_is_payable = 'Service tax is payable in addition to ride fare.';
												
										if ($this->lang->line('ride_per') != '')
                        $ride_per = stripslashes($this->lang->line('ride_per'));
                    else
                        $ride_per = 'per';	
                   
                   if ($this->lang->line('ride_per_min') != '')
                        $ride_per_min = stripslashes($this->lang->line('ride_per_min'));
                    else
                        $ride_per_min = 'per min';	

                    
                    if($locationsVal->row()->fare[$category_id]['min_time'] > 1){
                        if ($this->lang->line('rides_mins_lower') != '')$min_time_mins = stripslashes($this->lang->line('rides_mins_lower'));else $min_time_mins = 'mins'; 
                    } else {
                        if ($this->lang->line('rides_min_lower') != '')$min_time_mins = stripslashes($this->lang->line('rides_min_lower'));else $min_time_mins = 'min'; 
                    }
                    
                    $d_distance_unit = $this->data['d_distance_unit'];
                    $dcurrencySymbol = $this->data['dcurrencySymbol'];
												
                    $standard_rate = array(array('title' => $first .' '. $locationsVal->row()->fare[$category_id]['min_km'] .' '. $d_distance_unit,
                            'fare' => $locationsVal->row()->fare[$category_id]['min_fare'],
                            'sub_title' => ''
                        ),
                        array('title' => $after .' '. $locationsVal->row()->fare[$category_id]['min_km'] .' '. $d_distance_unit,
                            'fare' => $locationsVal->row()->fare[$category_id]['per_km'] .' '. $ride_per.' '.$d_distance_unit,
                            'sub_title' => ''
                        )
                    ); 
                    $extra_charges = array(array('title' => $ride_time_charges,
                            'fare' => $locationsVal->row()->fare[$category_id]['per_minute'] .' ' .$ride_per_min,
                            'sub_title' => $first .' '. $locationsVal->row()->fare[$category_id]['min_time'] . " " .$min_time_mins." ".$driver_ride_time_is_free
                        )
                    );


                    if (isset($locationsVal->row()->service_tax)) {
                        if ($locationsVal->row()->service_tax > 0) {
                            $extra_charges[] = array('title' => $driver_wait_time_charges,
                                'fare' => $locationsVal->row()->fare[$category_id]['wait_per_minute'].' '. $ride_per_min,
                                'sub_title' => ''
                            );
                        }
                    }

                    if (isset($locationsVal->row()->peak_time)) {
                        if ($locationsVal->row()->peak_time == 'Yes') {
                            $extra_charges[] = array('title' => $driver_peak_time_charge,
                                'fare' => $locationsVal->row()->fare[$category_id]['peak_time_charge'] . ' x ',
                                'sub_title' => $driver_may_applicable_during_high_demand,
                                'currency_state' => 'No',
                                'percentage_state' => 'No'
                            );
                        }
                    }
                    if (isset($locationsVal->row()->night_charge)) {
                        if ($locationsVal->row()->night_charge == 'Yes') {
                            $extra_charges[] = array('title' => $driver_night_time_charges,
                                'fare' => $locationsVal->row()->fare[$category_id]['night_charge'] . ' x ',
                                'sub_title' => $driver_night_time_charges_may_apply,
                                'currency_state' => 'No',
                                'percentage_state' => 'No'
                            );
                        }
                    }
                    if (isset($locationsVal->row()->service_tax)) {
                        if ($locationsVal->row()->service_tax > 0) {
                            $extra_charges[] = array('title' => $driver_service_tax,
                                'fare' => $locationsVal->row()->service_tax,
                                'sub_title' => $driver_service_tax_is_payable,
                                'currency_state' => 'No',
                                'percentage_state' => 'Yes'
                            );
                        }
                    }



                    $ratecardArr = array('currency' => $locationsVal->row()->currency,
                        'standard_rate' => $standard_rate,
                        'extra_charges' => $extra_charges,
                        'location_id' => $location_id,
                        'category_id' => $category_id
                    );
                    
                    
                    if ($this->lang->line('user_standard_rate') != '') $standard_rate_title =  stripslashes($this->lang->line('user_standard_rate')); else $standard_rate_title =  'STANDARD RATE';
                    
                    if ($this->lang->line('user_rate_information_na') != '') $user_rate_information_na = stripslashes($this->lang->line('user_rate_information_na')); else $user_rate_information_na = 'Rate Information not available';
                    
                    if ($this->lang->line('user_extra_charges') != '') $user_extra_charges = stripslashes($this->lang->line('user_extra_charges')); else $user_extra_charges = 'EXTRA CHARGES';
                    
                    
                    $response = '';
                    $response.= '
                    <div class="rate_title">
                        <h2>'.$standard_rate_title.'</h2>
                    </div>
                    <div class="col-lg-12 col-md-12 col-sm-12 col-xs-12 no_padding rate_detail">';
             
                    if (count($standard_rate) > 0) { 
                    for ($s = 0; $s < count($standard_rate); $s++) {
                    
                    if($d_distance_unit == "kms"){
                       if ($this->lang->line('ride_kms') != '') $d_distance_unit1=stripslashes($this->lang->line('ride_kms'));else $d_distance_unit1= 'kms'; 
                    }else if($d_distance_unit == "km"){
                      if ($this->lang->line('ride_km') != '') $d_distance_unit1=stripslashes($this->lang->line('ride_km'));
                        else
                        $d_distance_unit1= 'km'; 
                    }
            


                    $response.= '<div class="inner_full">
                        <div class="inner_left">'.str_replace("km",$d_distance_unit1,$standard_rate[$s]['title']).' '.$standard_rate[$s]['sub_title'].'</div>
                        <div class="inner_right">
                           '.$dcurrencySymbol.' '. str_replace("km",$d_distance_unit1,$standard_rate[$s]['fare']).'
                        </div>
                     </div>';

                    } } else { 
                    $response.= '<div class="inner_full">
                        <div class="inner_left">
                           '.$user_rate_information_na.'
                        </div>
                    </div>';
                    }

                  $response.= '</div>
                    <div class="rate_title">
                         <h2>'.$user_extra_charges.'</h2>
                    </div>
                    <div class="col-lg-12 col-md-12 col-sm-12 col-xs-12 no_padding rate_detail">';
                    
                    if (count($extra_charges) > 0) { 
                        for ($exr = 0; $exr < count($extra_charges); $exr++) {
                      
                   
                        $response.= '<div class="inner_full">
                        <div class="inner_left">
                           '.$extra_charges[$exr]['title'].'<br>
                           <span>'.$extra_charges[$exr]['sub_title'].'</span>
                        </div>
                        <div class="inner_right">';
                            
                           if(isset($extra_charges[$exr]['currency_state']) && $extra_charges[$exr]['currency_state'] == 'No' && $extra_charges[$exr]['percentage_state'] == 'No') {
                              $response.= $extra_charges[$exr]['fare']; 
                           } else if(isset($extra_charges[$exr]['currency_state']) && $extra_charges[$exr]['currency_state'] == 'No' && $extra_charges[$exr]['percentage_state'] == 'Yes')  {
                             $response.= $extra_charges[$exr]['fare']."%"; 
                           }
                            else {
                              $response.= $dcurrencySymbol .' '. $extra_charges[$exr]['fare'];
                           } 
                        $response.= '</div>
                    </div>';

                
                    } } else {
                        $response.= '<div class="inner_full">
                          <div class="inner_left">
                            '.$user_rate_information_na.'
                          </div>
                        </div>';
                    }
                    $response.= '</div>';
                    
                    $responseArr['status'] = '1';                    
                    $responseArr['response'] = $response;
                    
                    
                } else {					
					if ($this->lang->line('driver_car_type_not_avail') != '') $responseArr['response'] =  stripslashes($this->lang->line('driver_car_type_not_avail')); else $responseArr['response'] =  'Sorry, Car type is not available for this location, choose another car type.';
                }
            } else {
				if ($this->lang->line('driver_location_not_found_for_rate_card') != '') $responseArr['response'] =  stripslashes($this->lang->line('driver_location_not_found_for_rate_card')); else $responseArr['response'] =  'Sorry, No location found for rate card calculation';
            }
        } else {
			if ($this->lang->line('driver_location_not_found_for_rate_card') != '') $responseArr['response'] =  stripslashes($this->lang->line('driver_location_not_found_for_rate_card')); else $responseArr['response'] =  'Sorry, No location found for rate card calculation';
        }
        echo json_encode($responseArr);
    }

}
