<?php

if (!defined('BASEPATH'))
    exit('No direct script access allowed');

/**
 * 
 * This controller contains the functions related to Operator management
 * @author Casperon
 *
 * */
class Settings extends MY_Controller {

    function __construct() {
        parent::__construct();
        $this->load->helper(array('cookie', 'date', 'form'));
        $this->load->library(array('encrypt', 'form_validation'));
        $this->load->model('app_model');
		
		$c_fun = $this->uri->segment(3);
		/**************    check operator state      **************/
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
				
				$condition = array('_id' => $this->checkLogin('O'));
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
		/***************************************************************/
		
    }

    /**
     * 
     * This function check the operator login session and load the templates
     * If session exists then load the dashboard
     * Otherwise load the login form
     * */
    public function index() {
				if ($this->lang->line('admin_menu_dashboard') != '') 
						$this->data['heading']= stripslashes($this->lang->line('admin_menu_dashboard')); 
				else  $this->data['heading'] = 'Dashboard';
	
        if ($this->checkLogin('O') == '') {
            $this->check_operator_session();
        }
        if ($this->checkLogin('O') == '') {
            $this->load->view(OPERATOR_NAME.'/templates/login.php', $this->data);
        } else {
            redirect(OPERATOR_NAME.'/dashboard');
        }
    }

    /**
     * 
     * This function validate the operator login form
     * If details are correct then load the dashboard
     * Otherwise load the login form and show the error message
     */
    public function login() {
				if ($this->lang->line('form_validation_username') != ''){
						$form_validation_username = stripslashes($this->lang->line('form_validation_username'));
				}else{
						$form_validation_username = 'Username';
				}
				if ($this->lang->line('form_validation_password') != ''){
						$form_validation_password = stripslashes($this->lang->line('form_validation_password'));
				}else{
						$form_validation_password = 'Password';
				}
        $this->form_validation->set_rules('operator_name', $form_validation_username, 'required');
        $this->form_validation->set_rules('operator_password', $form_validation_password, 'required');
        if ($this->form_validation->run() === FALSE) {
            $this->load->view(OPERATOR_NAME.'/templates/login.php', $this->data);
        } else {
            $email = trim($this->input->post('operator_name'));
            $pwd = md5($this->input->post('operator_password'));
            $collection = OPERATORS;
            
            $condition = array('email' => $email, 'password' => $pwd,'status' => 'Active');
            $query = $this->app_model->get_all_details($collection, $condition);

            if ($query->num_rows() == 1) {			
                print_r($query->row());
                #exit;
                $operatordata = array(
                    APP_NAME.'_session_operator_id' => (string)$query->row()->_id,
                    APP_NAME.'_session_operator_name' => $query->row()->operator_name,
                    APP_NAME.'_session_operator_email' => $query->row()->email,
                    APP_NAME.'_session_operator_location' => (string)$query->row()->operator_location
                   
                );
                echo "<pre>";
                $this->session->set_userdata($operatordata);
                print_r($this->session->all_userdata());
                #exit;
                $newdata = array(
                    'last_login_date' => date("Y-m-d H:i:s"),
                    'last_login_ip' => $this->input->ip_address()
                );
                $condition = array('_id' => $query->row()->_id);
                $this->app_model->update_details($collection, $newdata, $condition);
                $this->setErrorMessage('success', 'Logged in Successfully','admin_adminlogin_logged');
                redirect(OPERATOR_NAME.'/dashboard/display_dashboard');
            } else {
                $this->setErrorMessage('error', 'Invalid Login Details','admin_adminlogin_invalid_login');
            }
            redirect(OPERATOR_NAME);
        }
    }

		/**
		 * 
		 * This function remove all operator details from session and cookie and load the login form
		 */
    public function logout() {
        $newdata = array(
            'last_logout_date' => date("Y-m-d H:i:s")
        );
        $collection = OPERATORS;
        
        $condition = array('_id' => $this->checkLogin('O'));
        $this->app_model->update_details($collection, $newdata, $condition);
        $operatordata = array(
                    APP_NAME.'_session_operator_id' => '',
                    APP_NAME.'_session_operator_name' => '',
                    APP_NAME.'_session_operator_email' => '',
                    APP_NAME.'_session_vendor_location' =>''
                   
                );
        $this->session->unset_userdata($operatordata);
        $this->setErrorMessage('success', 'Successfully logout from your account','admin_adminlogin_logout_account');
        redirect(OPERATOR_NAME);
    }

    /**
     * 
     * This function loads the forgot password form
     */
    public function forgot_password_form() {
        if ($this->checkLogin('V') == '') {
            $this->load->view(OPERATOR_NAME.'/templates/forgot_password.php', $this->data);
        } else {
            redirect(OPERATOR_NAME.'/dashboard');
        }
    }

    /**
     * 
     * This function validate the forgot password form
     * If email is correct then generate new password and send it to the email given
     */
    public function forgot_password() {
				if ($this->lang->line('form_validation_email') != ''){
					$form_validation_email = stripslashes($this->lang->line('form_validation_email'));
				}else{
					$form_validation_email = 'Email';
				}
        $this->form_validation->set_rules('email', $form_validation_email, 'required|valid_email');
        if ($this->form_validation->run() === FALSE) {        
            $this->load->view(OPERATOR_NAME . '/templates/forgot_password.php', $this->data);
        } else {
            $email = trim($this->input->post('email'));
            $collection = OPERATORS;
            
            $condition = array('email' => $email);
            $operatorVal = $this->app_model->get_all_details($collection, $condition);
            if ($operatorVal->num_rows() == 1) {
								$reset_id = md5(time());
                $reset_data = array('reset_id' => $reset_id);
                $condition = array('email' => $email);
                $this->app_model->update_details($collection, $reset_data, $condition);
								
								$reset_url = base_url().OPERATOR_NAME .'/settings/reset_password_form/'.$reset_id;
                $this->send_operator_pwd($reset_url, $operatorVal);
                $this->setErrorMessage('success', 'Check your mail to reset password.','admin_adminlogin_reset_password');
                redirect(OPERATOR_NAME);
            } else {
                $this->setErrorMessage('error', 'Email id not matched in our records','admin_adminlogin_email_not_matched');
                redirect(OPERATOR_NAME.'/settings/forgot_password_form');
            }
            redirect(OPERATOR_NAME);
        }
    }

		/**
		 * 
		 * This function check the operator details in browser cookie
		 */
		public function check_operator_session() {
        $admin_session = $this->input->cookie(APP_NAME.'_operator_session', FALSE);
        if ($admin_session != '') {
            $admin_id = $this->encrypt->decode($admin_session);
            $mode = $admin_session[APP_NAME.'_session_admin_mode'];
            $condition = array('admin_id' => $admin_id);
            $query = $this->app_model->get_all_details($mode, $condition);
            if ($query->num_rows() == 1) {
                $privileges = $query->row()->privileges;
								if(is_array($privileges)){
										$priv =$privileges;
								} else {
										$priv = @unserialize($query->row()->privileges);
								}
                $admindata = array(
                    APP_NAME.'_session_admin_id' => $query->row()->admin_id,
                    APP_NAME.'_session_admin_name' => $query->row()->admin_name,
                    APP_NAME.'_session_admin_email' => $query->row()->email,
                    APP_NAME.'_session_admin_mode' => $mode,
                    APP_NAME.'_session_admin_privileges' => $priv
                );
                $this->session->set_userdata($admindata);
                $newdata = array(
                    'last_login_date' => date("Y-m-d H:i:s"),
                    'last_login_ip' => $this->input->ip_address()
                );
                $condition = array('admin_id' => $query->row()->admin_id);
                $this->app_model->update_details(ADMIN, $newdata, $condition);
            }
        }
    }

    /**
     * 
     * This function send the new password to operator email
     */
    public function send_operator_pwd($reset_url = '', $query) {
        $newsid = '1';
        $template_values = $this->app_model->get_newsletter_template_details($newsid);
        $subject = 'From: ' . $this->config->item('email_title') . ' - ' . $template_values->message['subject'];
        $opertornewstemplateArr = array(
																		'email_title' => $this->config->item('email_title'),
																		'logo' => $this->config->item('logo_image'),
																		'footer_content' => $this->config->item('footer_content'),
																		'meta_title' => $this->config->item('meta_title')
																);
        extract($opertornewstemplateArr);
        $message = '<!DOCTYPE HTML>
					<html>
					<head>
					<meta http-equiv="Content-Type" content="text/html; charset=utf-8">
					<meta name="viewport" content="width=device-width"/>
					<title>' . $template_values->message['subject'] . '</title>
					<body>';
						include('./newsletter/template' . $newsid . '.php');
						$message .= '</body>
					</html>';

				$sender_email = $this->config->item('site_contact_mail');
				$sender_name = $this->config->item('email_title');
        
        $email_values = array('mail_type' => 'html',
            'from_mail_id' => $sender_email,
            'mail_name' => $sender_name,
            'to_mail_id' => $query->row()->email,
            'subject_message' => $subject,
            'body_messages' => $message
        );
		
        $email_send_to_common = $this->app_model->common_email_send($email_values);
    }

		/**
		 * 
		 * This function loads the change password form
		 */
		public function change_password() {
				if ($this->lang->line('admin_menu_change_password') != '') 
						$this->data['heading']= stripslashes($this->lang->line('admin_menu_change_password')); 
				else  $this->data['heading'] = 'Change Password';
				if ($this->checkLogin('O') == '') {
						redirect(OPERATOR_NAME);
				} else {				
						$this->load->view(OPERATOR_NAME . '/templates/header.php', $this->data);
						$this->load->view(OPERATOR_NAME . '/settings/changepassword.php', $this->data);
						$this->load->view(OPERATOR_NAME . '/templates/footer.php', $this->data);
				}
		}

    /**
     * 
     * This function validate the change password form
     * If details are correct then change the operator password
     */
    public function change_password_operator() {
				if ($this->lang->line('form_validation_password') != ''){
						$form_validation_password = stripslashes($this->lang->line('form_validation_password'));
				}else{
						$form_validation_password = 'Password';
				}
				if ($this->lang->line('form_validation_new_password') != ''){
						$form_validation_new_password = stripslashes($this->lang->line('form_validation_new_password'));
				}else{
						$form_validation_new_password = 'New Password';
				}
				if ($this->lang->line('form_validation_confirm_password') != ''){
						$form_validation_confirm_password = stripslashes($this->lang->line('form_validation_confirm_password'));
				}else{
						$form_validation_confirm_password = 'Retype Password';
				}
        $this->form_validation->set_rules('password', $form_validation_password, 'required');
        $this->form_validation->set_rules('new_password', $form_validation_new_password, 'required');
        $this->form_validation->set_rules('confirm_password', $form_validation_confirm_password, 'required');
        if ($this->form_validation->run() === FALSE) {
            $this->load->view(OPERATOR_NAME.'/templates/header.php', $this->data);
            $this->load->view(OPERATOR_NAME.'/settings/changepassword.php', $this->data);
            $this->load->view(OPERATOR_NAME.'/templates/footer.php', $this->data);
        } else {
            $email = $this->session->userdata(APP_NAME.'_session_operator_email');
            $pwd = md5($this->input->post('password'));
            $collection = OPERATORS;
            
            $condition = array('email' => $email, 'password' => $pwd, 'status' => 'Active');
            $query = $this->app_model->get_all_details($collection, $condition);
            if ($query->num_rows() == 1) {
                $new_pwd = $this->input->post('new_password');
                $newdata = array('password' => md5($new_pwd));
                $condition = array('_id' => $query->row()->_id);
                $this->app_model->update_details($collection, $newdata, $condition);
                $this->setErrorMessage('success', 'Password changed successfully','admin_adminlogin_password_changed_successfully');
                redirect(OPERATOR_NAME.'/settings/change_password');
            } else {
                $this->setErrorMessage('error', 'Invalid current password','admin_adminlogin_invalid_current_password');
            }
            redirect(OPERATOR_NAME.'/settings/change_password');
        }
    }

    /**
     * 
     * This function loads the forgot password form
     */
    public function reset_password_form() {
				if ($this->checkLogin('V') == '') {
						$reset_id = $this->uri->segment(4);			
						$condition = array('reset_id' => $reset_id);
            $check_operator = $this->app_model->get_selected_fields(OPERATORS, $condition, array('email'));			
            if ($check_operator->num_rows() == 0) {							
								$this->setErrorMessage('error', 'This link has been removed.','admin_adminlogin_link_has_removed');
								redirect(OPERATOR_NAME);
            } else {			
								$this->data['reset_id'] = $reset_id;
								$this->load->view(OPERATOR_NAME.'/templates/reset_password.php', $this->data);
						}			
        } else {
            redirect(OPERATOR_NAME.'/dashboard');
        }
    }
	
    /**
     * 
     * This function reset the new password
     */
    public function reset_password() {
				$reset_id = $this->input->post('reset_id');
				$new_password = $this->input->post('new_password');
				$confirm_password = $this->input->post('confirm_password');
				
				if ($confirm_password === $new_password) {
						$collection = OPERATORS;
						$condition = array('reset_id' => $reset_id);
						$query = $this->app_model->get_all_details($collection, $condition);
						if ($query->num_rows() == 1) {
								$new_pwd = $this->input->post('new_password');
								$newdata = array('reset_id'=>'','password' => md5($new_pwd));
								$condition = array('_id' => $query->row()->_id);
								$this->app_model->update_details($collection, $newdata, $condition);
								$this->setErrorMessage('success', 'Password changed successfully','admin_adminlogin_password_changed_successfully');
								redirect(OPERATOR_NAME);
						}	else {
								$this->setErrorMessage('error', 'Please try again.','admin_adminlogin_please_try_again');
								redirect(OPERATOR_NAME.'/settings/reset_password_form/'.$reset_id);
						}
				}else{
						$this->setErrorMessage('error', 'Password does not matched.','admin_adminlogin_password_not_matched');
						redirect(OPERATOR_NAME.'/settings/reset_password_form/'.$reset_id);
				}
		}
	
    
		/**
		*
		* This function loads the add/Edit operators form
		*
		**/
		public function edit_profile_form(){
				if ($this->checkLogin('O') == ''){
						redirect(OPERATOR_NAME);
				}else {
						$operator_id = $this->checkLogin('O');
						$form_mode=TRUE;
						if ($this->lang->line('dash_operator_edit_profile_settings') != '') 
								$heading = stripslashes($this->lang->line('dash_operator_edit_profile_settings')); 
						else  $heading = 'Edit profile setting';
						
						$this->data['locationList'] = $this->app_model->get_all_details(LOCATIONS, array('status' => 'Active'), array('city' => 'ASC'));
						$condition = array('_id' => MongoID($operator_id));
						$this->data['operator_details'] = $this->app_model->get_all_details(OPERATORS,$condition);
						$this->data['form_mode'] = $form_mode;
						$this->data['heading'] = $heading;
						$this->load->view(OPERATOR_NAME.'/settings/edit_profile',$this->data);
				}
		}
    
    
		function update_profile(){
				if ($this->checkLogin('O') == ''){
						redirect(OPERATOR_NAME);
				}else {
						$operator_id = $this->input->post('operator_id');
						$email = trim($this->input->post('email'));
						$mobile_number = $this->input->post('mobile_number');
						$dail_code = $this->input->post('dail_code');

						$returnUrl = OPERATOR_NAME.'/settings/edit_profile_form';

						$condition=array('email'=>$email);
						$duplicate_email = $this->app_model->get_all_details(OPERATORS,$condition);
						if ($duplicate_email->num_rows() > 0){
								$opId = (string)$duplicate_email->row()->_id;
								if($opId==$operator_id){
									
								}else{
									$this->setErrorMessage('error','Email address already exists','driver_email_already');
									redirect($returnUrl);
								}
						}		

						$condition=array('dail_code'=>$dail_code,'mobile_number'=>$mobile_number);
						$duplicate_phone = $this->app_model->get_all_details(OPERATORS,$condition);
						if ($duplicate_phone->num_rows() > 0){
								$opId = (string)$duplicate_phone->row()->_id;
								if($opId==$operator_id){
									
								}else{
                                    
									$this->setErrorMessage('error','Mobile number already exists','operators_mobile_already_exists');
									redirect($returnUrl);
								}
						}	

						$excludeArr = array("status", "operator_id", "mobile_number", "dail_code", "operator_location", "address", "country", "state", "city", "postal_code");
						
						$addressArr['address'] = array('address' => $this->input->post('address'),
																'country' => $this->input->post('country'),
																'state' => $this->input->post('state'),
																'city' => $this->input->post('city'),
																'postal_code' => $this->input->post('postal_code')
														);

						$operator_data = array(
								'dail_code' => (string) $this->input->post('dail_code'),
								'mobile_number' => (string) $this->input->post('mobile_number')
							);
							
						$dataArr = array_merge($operator_data,$addressArr);
								
						$condition=array('_id'=>MongoID($operator_id));
						$this->app_model->commonInsertUpdate(OPERATORS,'update',$excludeArr,$dataArr,$condition);
						$this->setErrorMessage('success','Operator details updated successfully', 'operators_det_update_successfully');
						
						redirect($returnUrl);
				}
		}
    
    
	
	

}


/* End of file settings.php */
/* Location: ./application/controllers/operator/settings.php */