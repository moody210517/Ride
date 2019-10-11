<?php if (!defined('BASEPATH')) exit('No direct script access allowed');
/**
 *
 *	Notification
 * 
 *	@package		CI
 *	@subpackage		Controller
 *	@author			Casperon
 *
 * */
class Notification extends MY_Controller {

    function __construct() {
        parent::__construct();
        $this->load->helper(array('cookie', 'date', 'form'));
        $this->load->library(array('encrypt', 'form_validation','curl'));
        $this->load->model('notification_model', 'notify_model');
		$this->load->model(array('operators_model'));
		if($this->checkLogin('O') != ''){
			$operator_id = $this->checkLogin('O');
			$chkOperator = $this->app_model->get_selected_fields(OPERATORS,array('_id' => MongoID($operator_id)),array('status'));
			$chkstatus = TRUE;
			$errMsg = '';
			if($chkOperator->num_rows() == 1){
				if($chkOperator->row()->status == 'Inactive'){
					$chkstatus = FALSE;
						if ($this->lang->line('operator_inactive_message') != '') 
								$errMsg= stripslashes($this->lang->line('operator_inactive_message')); 
						else  $errMsg = 'Your account is temporarily deactivated, Please contact admin';
						
				}
			} else {
				$chkstatus = FALSE;
				if ($this->lang->line('account_not_found') != '') 
						$errMsg= stripslashes($this->lang->line('account_not_found')); 
				else  $errMsg = 'Your account details not found';
				
			}
			if(!$chkstatus){
				 $newdata = array(
					'last_logout_date' => date("Y-m-d H:i:s")
				);
				$collection = OPERATORS;
				
				$condition = array('_id' =>MongoID($this->checkLogin('O')));
				$this->app_model->update_details($collection, $newdata, $condition);
				$operatordata = array(
							APP_NAME.'_session_operator_id' => '',
							APP_NAME.'_session_operator_name' => '',
							APP_NAME.'_session_operator_email' => '',
							APP_NAME.'_session_vendor_location' =>''
						   
						);
				$this->session->unset_userdata($operatordata);
				$this->setErrorMessage('error', $errMsg);
				redirect(OPERATOR_NAME);
			}
		}
		
    }

	/**
	*
	* To redirect the user list page
	* 	
	* @Initiate HTML to Redirect Driver notification list page
	*	
	* */
    public function index() {
        if ($this->checkLogin('O') == '') {
            redirect(OPERATOR_NAME);
        } else {
            redirect(OPERATOR_NAME.'/notification/display_notification_driver_list');
        }
    }
		
	/**
	* 
	* To list the users in admin panel to send a notification
	*
	* @param string $type is filter type 
	* @param string $value for keywords to filter
	* @display HTML to show the Users notification list page
	*
	**/
    public function display_notification_user_list() {
		if ($this->checkLogin('O') == '') {
            redirect(OPERATOR_NAME);
        } else {
			$condition=array('status' => 'Active');
				if ($this->lang->line('admin_notification_send_notification') != '') 
						$this->data['heading']= stripslashes($this->lang->line('admin_notification_send_notification')); 
		    else  $this->data['heading'] = 'Send Notification';
			
            $filterArr = array();
            if ((isset($_GET['type']) && isset($_GET['value'])) && ($_GET['type'] != '' && $_GET['value'] != '')) {
                if (isset($_GET['type']) && $_GET['type'] != '') {
                    $this->data['type'] = $_GET['type'];
                }
                if (isset($_GET['value']) && $_GET['value'] != '') {
                    $this->data['value'] = $_GET['value'];
                    $filter_val = $this->data['value'];
                }
                $this->data['filter'] = 'filter';
								if($_GET['type'] == 'phone_number') {
										$condition["phone_number"] = $filter_val;
										$condition["country_code"] = $_GET['country'];
								} else {
										$filterArr = array($this->data['type'] => $filter_val);
								}   
            }
						$this->data['template_details']=$this->notify_model->get_all_details(NOTIFICATION_TEMPLATES,array());
			
						$usersCount = $this->notify_model->get_all_counts(USERS, $condition, $filterArr);
            if ($usersCount > 1000) {
                $searchPerPage = 500;
                $paginationNo = $this->uri->segment(4);
                if ($paginationNo == '') {
                    $paginationNo = 0;
                } else {
                    $paginationNo = $paginationNo;
                }

                $this->data['usersList'] = $this->notify_model->get_all_details(USERS, $condition, array('created' => 'DESC'), $searchPerPage, $paginationNo, $filterArr);

                $searchbaseUrl = OPERATOR_NAME . '/notification/display_notification_user_list';
                $config['num_links'] = 3;
                $config['display_pages'] = TRUE;
                $config['base_url'] = $searchbaseUrl;
                $config['total_rows'] = $usersCount;
                $config["per_page"] = $searchPerPage;
                $config["uri_segment"] = 4;
                $config['first_link'] = '';
                $config['last_link'] = '';
                $config['full_tag_open'] = '<ul class="tsc_pagination tsc_paginationA tsc_paginationA01">';
                $config['full_tag_close'] = '</ul>';
                if ($this->lang->line('pagination_prev_lbl') != '') $config['prev_link'] =stripslashes($this->lang->line('pagination_prev_lbl'));  else  $config['prev_link'] ='Prev';
                $config['prev_tag_open'] = '<li>';
                $config['prev_tag_close'] = '</li>';
                if ($this->lang->line('pagination_next_lbl') != '') $config['next_link'] =stripslashes($this->lang->line('pagination_next_lbl'));  else  $config['next_link'] ='Next';
                $config['next_tag_open'] = '<li>';
                $config['next_tag_close'] = '</li>';
                $config['cur_tag_open'] = '<li class="current"><a href="javascript:void(0);" style="cursor:default;">';
                $config['cur_tag_close'] = '</a></li>';
                $config['num_tag_open'] = '<li>';
                $config['num_tag_close'] = '</li>';
                $config['first_tag_open'] = '<li>';
                $config['first_tag_close'] = '</li>';
                $config['last_tag_open'] = '<li>';
                $config['last_tag_close'] = '</li>';
                if ($this->lang->line('pagination_first_lbl') != '') $config['first_link'] =stripslashes($this->lang->line('pagination_first_lbl'));  else  $config['first_link'] ='First';
                if ($this->lang->line('pagination_last_lbl') != '') $config['last_link'] = stripslashes($this->lang->line('pagination_last_lbl'));  else  $config['last_link'] ='Last';
                $this->pagination->initialize($config);
                $paginationLink = $this->pagination->create_links();
                $this->data['paginationLink'] = $paginationLink;


                $this->load->view(OPERATOR_NAME . '/notification/display_notification_userlist', $this->data);
            } else {

								$this->data['usersList'] = $this->notify_model->get_all_details(USERS, $condition, '', '', '', $filterArr);

                $this->data['paginationLink'] = '';
                $this->load->view(OPERATOR_NAME . '/notification/display_notification_userlist', $this->data);
            }
        }
    }
	
		/**
		* 
		* To list the users in admin panel to send a notification
		*
		* @param string $type is filter type 
		* @param string $value for keywords to filter
		* @display HTML to show the Users notification list page
		*
		**/

		public function display_notification_driver_list() {
 
        if ($this->checkLogin('O') == '') {
            redirect(OPERATOR_NAME);
        } else {
            
            if ($this->lang->line('admin_notification_send_notification') != '') {
                    $this->data['heading']= stripslashes($this->lang->line('admin_notification_send_notification')); 
            }else{
                    $this->data['heading'] = 'Send Notification';
            }
            
            $condition = array('status' => 'Active');
            $filterArr = array();
            
            $f_type = $this->input->get('type');
            $value = $this->input->get('value');
            $location_id = $this->input->get('location_id');
            $vehicle_category = $this->input->get('vehicle_category');
            if($f_type != '' && ($value != '' || $location_id != '' || $vehicle_category != '')){
                $this->data['type'] = $f_type;
                $this->data['value'] = $value;
                $this->data['filter'] = 'filter';
				$filterCondition = array();
                $filter_val = $value;
                if($f_type == 'mobile_number'){
                    $filterArr = array('mobile_number' => $filter_val,'dail_code' => $this->input->get('country'));
                } else if($f_type == 'vehicle_type'){
                    $condition = array('category' => MongoID($vehicle_category),'status' => 'Active');
                } else if($f_type == 'driver_location'){
                    $filterArr = array('driver_location' => $location_id);
                } else {
                    $filterArr = array($this->data['type'] => $filter_val);
                }
            }
                        
                        
                        
            $locationList = $this->notify_model->get_selected_fields(LOCATIONS, array(), array('_id', 'city'),array('city' => 'ASC'));
            
            $this->data['locationList'] = $locationList;
            
            $cabCats = $this->notify_model->get_selected_fields(CATEGORY, array(), array('_id', 'name','name_languages'))->result();
            $cabsTypeArr = array();
            foreach ($cabCats as $cab) {
                $cabId = (string) $cab->_id;
                $cabsTypeArr[$cabId] = $cab;
            }
            $this->data['cabCats'] = $cabsTypeArr;

            if ((isset($_GET['type']) && isset($_GET['value'])) && ($_GET['type'] != '' && $_GET['value'] != '')) {
                $this->data['type'] = $_GET['type'];
                $this->data['value'] = $_GET['value'];
                $filter_val = $this->data['value'];
                $this->data['filter'] = 'filter';
            } 
			$this->data['template_details']=$this->notify_model->get_all_details(NOTIFICATION_TEMPLATES,array());
			
			$this->mongo_db->where(array('driver_location'=>(string)$this->session->userdata(APP_NAME.'_session_operator_location')));
            $driversCount = $this->notify_model->get_all_counts(DRIVERS, $condition, $filterArr);
            if ($driversCount > 1000) {

                $searchPerPage = 500;
                $paginationNo = $this->uri->segment(4);
                if ($paginationNo == '') {
                    $paginationNo = 0;
                } else {
                    $paginationNo = $paginationNo;
                }
								
				$this->mongo_db->where(array('driver_location'=>(string)$this->session->userdata(APP_NAME.'_session_operator_location')));
                $this->data['driversList'] = $this->notify_model->get_all_details(DRIVERS, $condition, array('created' => 'DESC'), $searchPerPage, $paginationNo, $filterArr);

                $searchbaseUrl = OPERATOR_NAME.'/notification/display_notification_driver_list';
                $config['num_links'] = 3;
                $config['display_pages'] = TRUE;
                $config['base_url'] = $searchbaseUrl;
                $config['total_rows'] = $driversCount;
                $config["per_page"] = $searchPerPage;
                $config["uri_segment"] = 4;
                $config['first_link'] = '';
                $config['last_link'] = '';
                $config['full_tag_open'] = '<ul class="tsc_pagination tsc_paginationA tsc_paginationA01">';
                $config['full_tag_close'] = '</ul>';
                if ($this->lang->line('pagination_prev_lbl') != '') $config['prev_link'] =stripslashes($this->lang->line('pagination_prev_lbl'));  else  $config['prev_link'] ='Prev';
                $config['prev_tag_open'] = '<li>';
                $config['prev_tag_close'] = '</li>';
                if ($this->lang->line('pagination_next_lbl') != '') $config['next_link'] =stripslashes($this->lang->line('pagination_next_lbl'));  else  $config['next_link'] ='Next';
                $config['next_tag_open'] = '<li>';
                $config['next_tag_close'] = '</li>';
                $config['cur_tag_open'] = '<li class="current"><a href="javascript:void(0);" style="cursor:default;">';
                $config['cur_tag_close'] = '</a></li>';
                $config['num_tag_open'] = '<li>';
                $config['num_tag_close'] = '</li>';
                $config['first_tag_open'] = '<li>';
                $config['first_tag_close'] = '</li>';
                $config['last_tag_open'] = '<li>';
                $config['last_tag_close'] = '</li>';
                if ($this->lang->line('pagination_first_lbl') != '') $config['first_link'] =stripslashes($this->lang->line('pagination_first_lbl'));  else  $config['first_link'] ='First';
                if ($this->lang->line('pagination_last_lbl') != '') $config['last_link'] = stripslashes($this->lang->line('pagination_last_lbl'));  else  $config['last_link'] ='Last';
                $this->pagination->initialize($config);
                $paginationLink = $this->pagination->create_links();
                $this->data['paginationLink'] = $paginationLink;


                $this->load->view(OPERATOR_NAME.'/notification/display_notification_driverlist', $this->data);
            } else {
						
								$this->mongo_db->where(array('driver_location'=>(string)$this->session->userdata(APP_NAME.'_session_operator_location')));
                $this->data['driversList'] = $this->notify_model->get_all_details(DRIVERS, $condition, '', '', '', $filterArr);
                $this->data['paginationLink'] = '';
                $this->load->view(OPERATOR_NAME.'/notification/display_notification_driverlist', $this->data);
            }
        }
    }
	
	
		/**
		* 
		* To send notification to all users that have been checked
		*
		* @param string $userIds is user MongoDB\BSON\ObjectId
		* @param string $notification_id is Notification id and MongoDB\BSON\ObjectId
		* @param string $user_type is type of user and MongoDB\BSON\ObjectId
		* @redirect HTML to show Users notification list page
		*
		**/	
	 
    public function send_notification_to_device() {
				if($this->checkLogin('O') == ''){
						redirect(OPERATOR_NAME);
				}
				$userIds = $this->input->post('userIds');
				$notification_id = $this->input->post('notification_id');
				$user_type = $this->input->post('user_type');
				if($user_type == ''){
						$user_type = 'user';
				}
				$redirect_url=OPERATOR_NAME.'/notification/display_notification_'.$user_type.'_list';
		 
				if(!empty($notification_id)){
						if(!empty($userIds)){
								if($notification_id == ''){
										$this->setErrorMessage('error','Please select notification template','admin_notification_select_notification_template');
										redirect(OPERATOR_NAME.'/notification/display_notification_user_list');
								}
		
								$getTemplateDetails = $this->notify_model->get_all_details(NOTIFICATION_TEMPLATES,array('news_id' => (int)$notification_id));
		
								if($getTemplateDetails->num_rows() <= 0){
										$this->setErrorMessage('error','Notification records not available','admin_notification_records_not_available');
										redirect(OPERATOR_NAME . '/notification/display_notification_user_list');
								}
		
								$title = $getTemplateDetails->row()->message['title'];
								$description = $getTemplateDetails->row()->message['msg_description'];
								$image_name = '';
								if(isset($getTemplateDetails->row()->message['image'])){
										$image_name = base_url() . 'images/notification/' . $getTemplateDetails->row()->message['image'];
								}

								$u_id = @explode(',', $userIds);
								$u_id = array_filter(array_unique($u_id));

								$usersList = $this->notify_model->get_selected_fields_where_in(USERS, array('_id', $u_id, ''), array('push_notification_key', 'push_type'));
								
								$options = array('title' => $title,
										'description' => $description,
										'image_name' => $image_name
								);

								$android_user = array();
								$apple_user = array();
								foreach ($usersList->result_array() as $user) {
										if (array_key_exists('push_type', $user)) {
												if ($user['push_type'] == 'ANDROID') {
														if (isset($user['push_notification_key']['gcm_id'])) {
																if ($user['push_notification_key']['gcm_id'] != '') {
																		$android_user[] = $user['push_notification_key']['gcm_id'];
																}
														}
												}
												if ($user['push_type'] == 'IOS') {
														if (isset($user['push_notification_key']['ios_token'])) {
																if ($user['push_notification_key']['ios_token'] != '') {
																		$apple_user[] = $user['push_notification_key']['ios_token'];
																}
														}
												}
										}
								}
								$message = $title;
								if (!empty($android_user)) {
										$this->sendPushNotification($android_user, $message, 'ads', 'ANDROID', $options, 'USER');
								}
								if (!empty($apple_user)) {
										$this->sendPushNotification($apple_user, $message, 'ads', 'IOS', $options, 'USER');
								}
								$this->setErrorMessage('success', 'Notifications Sent.','admin_notification_sent');
								redirect(OPERATOR_NAME.'/notification/display_notification_user_list');
		
						}else{
								if($user_type=="driver"){
									$this->setErrorMessage('error', 'Please select one or more driver\'s','admin_notification_select_drivers');
								}else{
									$this->setErrorMessage('error', 'Please select one or more user\'s','admin_notification_select_user');
								}
								redirect($redirect_url);
						}
				}else{
						$this->setErrorMessage('error', 'Please select Notification template','admin_notification_select_notification_template');
						redirect($redirect_url);
				}
    }

		/**
		* 
		* To send notification to all drivers that have been checked
		*
		* @param string $userIds is driver MongoDB\BSON\ObjectId
		* @param string $notification_id is Notification id and MongoDB\BSON\ObjectId
		* @param string $user_type is type of user and MongoDB\BSON\ObjectId
		* @redirect HTML to show driver notification list page
		*
		**/	
	
		public function send_notification_to_device_driver() {
				if($this->checkLogin('O') == ''){
						redirect(OPERATOR_NAME);
				}
				$userIds = $this->input->post('userIds');
				$notification_id = $this->input->post('notification_id');
				$user_type = $this->input->post('user_type');
		
				if($user_type == ''){
						$user_type = 'driver';
				}		
		
				$redirect_url=OPERATOR_NAME.'/notification/display_notification_'.$user_type.'_list';
				if(!empty($notification_id)){
						if(!empty($userIds)){
		
								if($notification_id == ''){
										$this->setErrorMessage('error','Please select notification template','admin_notification_select_notification_template');
										redirect(OPERATOR_NAME.'/notification/display_notification_driver_list');
								}
		
								$getTemplateDetails = $this->notify_model->get_all_details(NOTIFICATION_TEMPLATES,array('news_id' => (int)$notification_id));
		
								if($getTemplateDetails->num_rows() <= 0){
										$this->setErrorMessage('error','Notification records not available','admin_notification_records_not_available');
										redirect(OPERATOR_NAME.'/notification/display_notification_driver_list');
								}
		
								$title = $getTemplateDetails->row()->message['title'];
								$description = $getTemplateDetails->row()->message['msg_description'];
								$image_name = '';
								if(isset($getTemplateDetails->row()->message['image'])){
										$image_name = base_url() . 'images/notification/' . $getTemplateDetails->row()->message['image'];
								}
		
								$u_id = @explode(',', $userIds);
								$u_id = array_filter(array_unique($u_id));
								$usersList = $this->notify_model->get_selected_fields_where_in(DRIVERS, array('_id', $u_id, ''), array('push_notification'));
								
								$options = array('title' => $title,
										'description' => $description,
										'image_name' => $image_name
								);

								$android_driver = array();
								$apple_driver = array();


								foreach ($usersList->result_array() as $user) {
										if (array_key_exists('push_notification', $user)) {
												if ($user['push_notification']['type'] == 'ANDROID') {

														if (isset($user['push_notification']['key'])) {

																if ($user['push_notification']['key'] != '') {
																		$android_driver[] = $user['push_notification']['key'];
																}
														}
												}
												if ($user['push_notification']['type'] == 'IOS') {
														if (isset($user['push_notification']['key'])) {
																if ($user['push_notification']['key'] != '') {
																		$apple_driver[] = $user['push_notification']['key'];
																}
														}
												}
										}
								}

								$message = $title;
								if (!empty($android_driver)) {
										$this->sendPushNotification($android_driver, $message, 'ads', 'ANDROID', $options, 'DRIVER');
								}
								if (!empty($apple_driver)) {
										$this->sendPushNotification($apple_driver, $message, 'ads', 'IOS', $options, 'DRIVER');
								}
								$this->setErrorMessage('success', 'Notifications Sent.','admin_notification_sent');
								redirect(OPERATOR_NAME.'/notification/display_notification_driver_list');
    
						}else{
								if($user_type=="driver"){
										$this->setErrorMessage('error', 'Please select one or more driver\'s','admin_notification_select_drivers');
								}else{
										$this->setErrorMessage('error', 'Please select one or more user\'s','admin_notification_select_user');
								}
								redirect($redirect_url);
						}
				}else{
						$this->setErrorMessage('error', 'Please select Notification template','admin_notification_select_notification_template');
						redirect($redirect_url);
				}
	}

		/**
		* 
		* To send bulk email to Drivers or Users
		*
		* @param string $userIds is user/driver MongoDB\BSON\ObjectId
		* @param string $email_id is email id and MongoDB\BSON\ObjectId
		* @param string $user_type is type of user and MongoDB\BSON\ObjectId
		* @display HTML to show users notification list page
		*
		**/	
	public function send_email_to_users(){
		if($this->checkLogin('O') == ''){
				redirect(OPERATOR_NAME);
		}

		$userIds = $this->input->post('userIds');
		$email_id = $this->input->post('email_id');
		$user_type = $this->input->post('user_type');
		$redirect_url=OPERATOR_NAME.'/notification/display_notification_'.$user_type.'_list';
		if(!empty($email_id)){
				if(!empty($userIds)){
						$fields=$email_list=array();
						$fields['news_id']=$email_id;
						if($user_type=='user'){
								$collection = USERS;
						}else{
								$collection = DRIVERS;
						}
						if($collection!=''){
				
								$u_id = @explode(',', $userIds);
								$u_id = array_filter(array_unique($u_id));
								$usersList = $this->notify_model->get_selected_fields_where_in($collection, array('_id', $u_id, ''), array('email'));
		
		
								foreach($usersList->result_array() as $user){
										$email_list[]=$user['email'];
								}
		
								$total_email = count($email_list); 
								$num_of_email_to_send=50;
								$ne = ceil($total_email/$num_of_email_to_send);
								$max=0;
								$url = base_url() . 'send-bulk-emails';
								for($i=0;$i<$ne;$i++){
										$to="";
										$sliced_array=array_slice($email_list,$max,$num_of_email_to_send);
										$bcc=implode(",",$sliced_array);
										$fields['to_mail_id']=$to;
										$fields['bcc_mail_id']=$bcc;
										$max=($i+1)*$num_of_email_to_send;
										$output = $this->curl->simple_post($url, $fields);
								}
								$this->setErrorMessage('success', 'Successfully Emails Sent!','admin_notification_successfully_email_sent');
								redirect($redirect_url);	
						}
		
				}else{
						if($user_type=="driver"){
								$this->setErrorMessage('error', 'Please select one or more driver\'s','admin_notification_select_drivers');
						}else{
								$this->setErrorMessage('error', 'Please select one or more user\'s','admin_notification_select_user');
						}
						redirect($redirect_url);
				}
		}else{
				$this->setErrorMessage('error', 'Please select email template','admin_notification_select_email_template');
				redirect($redirect_url);
		}		
	}
		
		
		/**
		* 
		* To Send bulk sms to Drivers or Users
		*
		* @param string $userIds is user/driver MongoDB\BSON\ObjectId
		* @param string $user_type is type of user and MongoDB\BSON\ObjectId
		* @param string $sms_id is type of sms id
		* @return string error or success message to list particular template
		**/
	public function send_sms_to_device(){
		 if ($this->checkLogin('O') == ''){
			redirect(OPERATOR_NAME);
		}
		
		$userIds = $this->input->post('userIds');
		$sms_id = $this->input->post('sms_id');
		$user_type = $this->input->post('user_type');
		$redirect_url= OPERATOR_NAME.'/notification/display_notification_'.$user_type.'_list';
		if(!empty($sms_id)){
		if(!empty($userIds)){
			$u_id = @explode(',', $userIds);
			$u_id = array_filter(array_unique($u_id));
		
			if($user_type=='user'){
				$membersList = $this->notify_model->get_selected_fields_where_in(USERS, array('_id', $u_id, ''), array('country_code','phone_number'));
			}else{
				$membersList = $this->notify_model->get_selected_fields_where_in(DRIVERS, array('_id', $u_id, ''), array('dail_code','mobile_number'));
			}
			if($membersList->num_rows() > 0){
				$this->load->model('sms_model');
				$getSms_info = $this->notify_model->get_all_details(NOTIFICATION_TEMPLATES,array('news_id' => (int)$sms_id));
				$msg_content = '';
				if(isset($getSms_info->row()->message['sms_description'])) $msg_content = $getSms_info->row()->message['sms_description'];
				if($msg_content != ''){
					foreach($membersList->result() as $user){
						if($user_type == 'user'){
							$phone_number =$user->country_code.$user->phone_number;
						} else {
							$phone_number =$user->dail_code.$user->mobile_number;
						}
						$from = $this->config->item('twilio_number');
						$this->sms_model->send_common_sms($from,$phone_number,$msg_content);
					}
					
					$this->setErrorMessage('success', 'SMS Sent Successfully','sms_sent_successfully');
					redirect($redirect_url);	
				} else {
					$this->setErrorMessage('error', 'SMS details not found','sms_sms_details_not_found');
					redirect($redirect_url);
				}
			} else {
				$this->setErrorMessage('error', $user_type.' not found');
				redirect($redirect_url);	
			}	
		}else{
			if($user_type=="driver"){
				$this->setErrorMessage('error', 'Please select one or more drivers','admin_notification_select_drivers');
			}else{
				$this->setErrorMessage('error', 'Please select one or more users','admin_notification_select_user');
			}
			redirect($redirect_url);
		 }
		}else{
			$this->setErrorMessage('error', 'Please select sms template first','please_select_sms_template_first');
			redirect($redirect_url);
		}
	}

	

}


/* End of file notification.php */
/* Location: ./application/controllers/operator/notification.php */