<?php if (!defined('BASEPATH'))  exit('No direct script access allowed');
/**
*
*	login management for company panel
* 
*	@package		CI
*	@subpackage		Controller
*	@author			Casperon
*
**/
class login extends MY_Controller {

    function __construct() {
        parent::__construct();
        $this->load->helper(array('cookie', 'date', 'form'));
        $this->load->library(array('encrypt', 'form_validation'));
        $this->load->model('company_model');
		
		$c_fun = $this->uri->segment(3);
		if ($this->checkLogin('C') != '') {
			$this->data['company_id'] = MongoID($this->session->userdata(APP_NAME.'_session_company_id'));
			$company_id = $this->checkLogin('C');
			$chkCompany = $this->app_model->get_selected_fields(COMPANY,array('_id' => MongoID($company_id)),array('status'));
			$chkstatus = TRUE;
			$errMsg = '';
			if($chkCompany->num_rows() == 1){
				if($chkCompany->row()->status == 'Inactive'){
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
				$collection = COMPANY;
				
				$condition = array('_id' => MongoID($this->checkLogin('C')));
				$this->app_model->update_details($collection, $newdata, $condition);
				$companydata = array(
							APP_NAME.'_session_company_id' => '',
							APP_NAME.'_session_company_name' => '',
							APP_NAME.'_session_company_email' => ''
							
						   
						);
						
				$this->session->unset_userdata($companydata);
				$this->setErrorMessage('error', $errMsg);
				redirect(COMPANY_NAME);
			}
			
		}
		      
		
    }
	/**
	* 
	* its redirect to company dashboard page
	*
	* @return HTTP REDIRECT,company dashboard page
	*
	**/	
    public function index() {
	    if ($this->lang->line('admin_menu_dashboard') != '') 
		$this->data['heading']= stripslashes($this->lang->line('admin_menu_dashboard')); 
		else  $this->data['heading'] = 'Dashboard';
	
        if ($this->checkLogin('C') == '') {
            $this->check_admin_session();
        }
        if ($this->checkLogin('C') == '') {
            $this->load->view(COMPANY_NAME.'/templates/login.php', $this->data);
        } else {
            redirect(COMPANY_NAME.'/dashboard');
        }
    }
	/**
	* 
	* it login into the company panel
	*
	* @param string $company_email  company email
	* @param string $company_password  company password
	* @return HTTP REDIRECT,company dashboard page
	*
	**/	
    public function user_login() {
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
        $this->form_validation->set_rules('admin_name', $form_validation_username, 'required');
        $this->form_validation->set_rules('admin_password', $form_validation_password, 'required');
        if ($this->form_validation->run() === FALSE) {
            $this->load->view(COMPANY_NAME.'/templates/login.php', $this->data);
        } else {
            $email = trim($this->input->post('admin_name'));
            $pwd = md5($this->input->post('admin_password'));
            $collection = COMPANY;
            $condition = array('email' => $email, 'password' => $pwd,'status' => 'Active');
            $query = $this->company_model->get_all_details($collection, $condition);

            if ($query->num_rows() == 1) {
				
                
                $companydata = array(
                    APP_NAME.'_session_company_id' => (string)$query->row()->_id,
                    APP_NAME.'_session_company_name' => $query->row()->company_name,
                    APP_NAME.'_session_company_email' => $query->row()->email
             
                );

                $this->session->set_userdata($companydata);
                $newdata = array(
                    'last_login_date' => date("Y-m-d H:i:s"),
                    'last_login_ip' => $this->input->ip_address()
                );
                $condition = array('_id' => $query->row()->_id);
                $this->company_model->update_details($collection, $newdata, $condition);
                $this->setErrorMessage('success', 'Logged in Successfully','admin_adminlogin_logged');
                redirect(COMPANY_NAME.'/dashboard/user_dashboard');
            } else {
                $this->setErrorMessage('error', 'Invalid Login Details','admin_adminlogin_invalid_login');
            }
            redirect(COMPANY_NAME);
        }
    }
	/**
	* 
	* logout from the company panel
	*
	* @return HTTP REDIRECT,company login page
	*
	**/	
    public function user_logout() {
        $newdata = array(
            'last_logout_date' => date("Y-m-d H:i:s")
        );
        $collection = COMPANY;
       
        $condition = array('_id' => MongoID($this->checkLogin('C')));
        $this->company_model->update_details($collection, $newdata, $condition);
        $admindata = array(
            APP_NAME.'_session_company_id' => '',
            APP_NAME.'_session_company_name' => '',
            APP_NAME.'_session_compnay_email' => ''
          
        );
        $this->session->unset_userdata($admindata);
        $this->setErrorMessage('success', 'Successfully logout from your account','admin_adminlogin_logout_account');
        redirect(COMPANY_NAME);
    }
	/**
	* 
	* company forgot password page
	*
	* @return HTML,company forgot password page
	*
	**/	
    public function forgot_password_form() {
        if ($this->checkLogin('C') == '') {
             $this->load->view(COMPANY_NAME.'/templates/forgot_password.php', $this->data);
        } else {
            redirect(COMPANY_NAME.'/dashboard');
        }
    }
	/**
	* 
	* company forgot password 
	*
	* @param string $email  company email
	* @return HTTP REDIRECT,company forgot password page
	*
	**/	
    public function forgot_password() {
		if ($this->lang->line('form_validation_email') != ''){
			$form_validation_email = stripslashes($this->lang->line('form_validation_email'));
		}else{
			$form_validation_email = 'Email';
		}
        $this->form_validation->set_rules('email', $form_validation_email, 'required|valid_email');
        if ($this->form_validation->run() === FALSE) {
        
            $this->load->view(COMPANY_NAME.'/templates/forgot_password.php', $this->data);
        } else {
            $email = $this->input->post('email');
            $collection = COMPANY;
            $condition = array('email' => $email);
            $adminVal = $this->company_model->get_all_details($collection, $condition);
            if ($adminVal->num_rows() == 1) {
               	$reset_id = md5(time());
                $reset_data = array('reset_id' => $reset_id);
                $condition = array('email' => $email);
                $this->company_model->update_details($collection, $reset_data, $condition);
								
				$reset_url = base_url().COMPANY_NAME.'/login/reset_password_form/'.$reset_id;
                $this->send_pwd($reset_url, $adminVal);
                $this->setErrorMessage('success', 'Check your mail to reset password.','admin_adminlogin_reset_password');
                redirect(COMPANY_NAME);
            } else {
                $this->setErrorMessage('error', 'Email id not matched in our records','admin_adminlogin_email_not_matched');
                redirect(COMPANY_NAME.'/login/forgot_password_form');
            }
            redirect(COMPANY_NAME);
        }
    }
	/**
	* 
	* check the company session 
	*
	* @return CALLBACK,company dashboard page
	*
	**/
    public function check_admin_session() {
        $admin_session = $this->input->cookie(APP_NAME.'_admin_session', FALSE);
        if ($admin_session != '') {
            $admin_id = $this->encrypt->decode($admin_session);
            $mode = $admin_session[APP_NAME.'_session_admin_mode'];
            $condition = array('admin_id' => $admin_id);
            $query = $this->company_model->get_all_details($mode, $condition);
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
                $this->company_model->update_details(ADMIN, $newdata, $condition);
            }
        }
    }
	/**
	* 
	* send forgot password mail
	*
	* @param string $reset_url  password reset link
    * @param string $query  company query result from company
	* @return HTTP REDIRECT,company forgot password page
	*
	**/
    public function send_pwd($reset_url = '', $query) {
        $newsid = '1';
        $template_values = $this->company_model->get_newsletter_template_details($newsid);
        $subject = 'From: ' . $this->config->item('email_title') . ' - ' . $template_values->message['subject'];
        $adminnewstemplateArr = array('email_title' => $this->config->item('email_title'), 'logo' => $this->config->item('logo_image'), 'footer_content' => $this->config->item('footer_content'), 'meta_title' => $this->config->item('meta_title'));
        extract($adminnewstemplateArr);
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
            'subject_message' => 'Password Reset',
            'body_messages' => $message
        );
        $email_send_to_common = $this->company_model->common_email_send($email_values);
    }
	/**
	* 
	* Displays the company change password
	*
   	* @return HTML, admin change password page
	*
	**/
    public function change_password_form() {
	    if ($this->lang->line('admin_menu_change_password') != '') 
		$this->data['heading']= stripslashes($this->lang->line('admin_menu_change_password')); 
		else  $this->data['heading'] = 'Change Password';
        if ($this->checkLogin('C') == '') {
            redirect(COMPANY_NAME);
        } else {
        
            $this->load->view(COMPANY_NAME.'/templates/header.php', $this->data);
            $this->load->view(COMPANY_NAME.'/settings/changepassword.php', $this->data);
            $this->load->view(COMPANY_NAME.'/templates/footer.php', $this->data);
        }
    }
	/**
	* 
	* Change company password
	*
    * @param string $new_password  company new password
    * @param string $confirm_password  company confirm new password
    * @return HTTP REDIRECT,company change password page
	*
	**/
    public function change_password() {
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
            $this->load->view(COMPANY_NAME.'/templates/header.php', $this->data);
            $this->load->view(COMPANY_NAME.'/settings/changepassword.php', $this->data);
            $this->load->view(COMPANY_NAME.'/templates/footer.php', $this->data);
        } else {
            $name = $this->session->userdata(APP_NAME.'_session_company_email');
			
            $pwd = md5($this->input->post('password'));
            $collection = COMPANY;
            $condition = array('email' => $name, 'password' => $pwd,'status' => 'Active');
			
            $query = $this->company_model->get_all_details($collection, $condition);
			
            if ($query->num_rows() == 1) {
                $new_pwd = $this->input->post('new_password');
                $newdata = array('password' => md5($new_pwd));
                $condition = array('_id' => $query->row()->_id);
				
                $this->company_model->update_details(COMPANY, $newdata, $condition);
                $this->setErrorMessage('success', 'Password changed successfully','admin_adminlogin_password_changed_successfully');
                redirect(COMPANY_NAME.'/login/change_password_form');
            } else {
                $this->setErrorMessage('error', 'Invalid current password','admin_adminlogin_invalid_current_password');
            }
            redirect(COMPANY_NAME.'/login/change_password_form');
        }
    }
	/**
	* 
	* Company profile setting page
	*
    * @param string $company_id  Company MongoDB\BSON\ObjectId
    * @return HTML, Company profile setting page
	*
	**/
	public function profile_setting() {
        if ($this->checkLogin('C') == '') {
            $this->setErrorMessage('error', 'You must login first','company_must_login');
            redirect(COMPANY_NAME);
        }else{
			if ($this->lang->line('dash_operator_edit_profile_settings') != '') 
					$this->data['heading']= stripslashes($this->lang->line('dash_operator_edit_profile_settings')); 
			else  
				$this->data['heading'] = 'Edit profile setting';
			$company_id = $this->session->userdata(APP_NAME.'_session_company_id'); 
            $condition = array('_id' => MongoID($company_id));
            $this->data['companydetail'] = $this->company_model->get_all_details(COMPANY, $condition);
            $form_mode = TRUE;
            $this->data['form_mode'] = $form_mode;
            $this->load->view(COMPANY_NAME.'/settings/edit_profile', $this->data);
      }
	}
	/**
	* 
	* insert or update company profile
	*
    * @param string $company_id  Company MongoDB\BSON\ObjectId
    * @param string $CompanyName  Company Name
    * @param string $phonenumber  Company Phone Number
    * @param text $address  Company address
    * @param string $city  Company City
    * @param string $state  Company state
    * @param string $country  Company country
    * @param string $zipcode  Company zipcode
    * @param string $username  Company username
    * @param string $password  Company password
    * @param string $password_confirm  Company password confirmation
    * @param string $email  Company email
    * @return HTTP REDIRECT, Company profile setting page
	*
	**/
	public function insertEditcompanyprofile(){  
		if ($this->checkLogin('C') == ''){
			redirect(COMPANY_NAME);
		}else {   
			
			$operators_id = $this->input->post('operators_id');
            $CompanyName = $this->input->post('CompanyName');
            $phonenumber = $this->input->post('phonenumber');
            $address	 = $this->input->post('address');
            $city    = $this->input->post('city');
            $state    = $this->input->post('state');
            $country   = $this->input->post('country');
            $zipcode    = $this->input->post('zipcode');
           
            $username = $this->input->post('username');
            $password = $this->input->post('password');
            $password_confirm = $this->input->post('password_confirm');
            $email = $this->input->post('email');


            $isDuplicatephone = FALSE;
            $isDuplicateName = FALSE;
            $isDuplicateEmail = FALSE;
            if ($operators_id == '') {
                $condition = array('company_name' => $CompanyName);
                $duplicate_username = $this->company_model->get_selected_fields(COMPANY, $condition, array('company_name'));
                if ($duplicate_username->num_rows() > 0)
                    $isDuplicateName = TRUE;
					
                $condition = array('email' => $email);
                $duplicate_email = $this->company_model->get_selected_fields(COMPANY, $condition, array('email'));
                if ($duplicate_email->num_rows() > 0)
                    $isDuplicateEmail = TRUE;
			}else{ 
				$condition = array('email' => $email,'_id' => array('$ne' => MongoID($operators_id)));
                $duplicate_email = $this->company_model->get_selected_fields(COMPANY, $condition, array('email'));
                if ($duplicate_email->num_rows() > 0)
                    $isDuplicateEmail = TRUE;
				$condition = array('company_name' => $CompanyName,'_id' => array('$ne' => MongoID($operators_id)));
                $duplicate_username = $this->company_model->get_selected_fields(COMPANY, $condition, array('company_name'));
				if ($duplicate_username->num_rows() > 0)
                    $isDuplicateName = TRUE;
				$condition = array('phonenumber' => $phonenumber,'_id' => array('$ne' => MongoID($operators_id)));
                $duplicate_phonenumber = $this->company_model->get_selected_fields(COMPANY, $condition, array('company_name'));
				if ($duplicate_phonenumber->num_rows() > 0) 
				$isDuplicatephone = TRUE;
			}
               

            if ($isDuplicateName) { 
                $this->setErrorMessage('error', 'This company name already exist','company_name_already');
				redirect(COMPANY_NAME.'/login/profile_setting');
            }
            if ($isDuplicateEmail) { 
                $this->setErrorMessage('error', 'This Email address already exist','email_already_exist');
				redirect(COMPANY_NAME.'/login/profile_setting');
            }
			if ($isDuplicatephone) { 
				$this->setErrorMessage('error', 'This mobile number already exist','phone_number_exist');
				redirect(COMPANY_NAME.'/login/profile_setting');
		    }
			
			
            

           $dataArr = array('company_name' => $CompanyName,
								'phonenumber'=>$phonenumber,
								'email' => $email,
								'modified' => date('Y-m-d H:i:s'),
								'locality'=>array('address' => $address,
												'city' => $city,
												 'state'=>$state,
												 'country'=>$country,
												 'zipcode'=>$zipcode)   
                );
			
			
            if ($operators_id == '') {
                $this->company_model->simple_insert(COMPANY, $dataArr);
                $this->setErrorMessage('success', 'Company profile added successfully','company_profile_added');
            } else {
				unset($dataArr['password']);
                $condition = array('_id' => MongoID($operators_id));
               $ress=$this->company_model->update_details(COMPANY, $dataArr, $condition);

                $this->setErrorMessage('success', 'Company profile updated successfully','company_profile_updated');
            }  
            $last_insert_id = $this->mongo_db->insert_id();
            
            redirect(COMPANY_NAME.'/login/profile_setting');
		}
	}
	/**
	* 
	* company reset password form
	*
    * @param string $reset_id  company password edit reset id
	* @return HTML, company reset settings
	*
	**/
    public function reset_password_form() {
        if ($this->checkLogin('C') == '') {
			$reset_id = $this->uri->segment(4);			
			$condition = array('reset_id' => $reset_id);
            $check_admin = $this->company_model->get_selected_fields(COMPANY, $condition, array('email'));
			$this->data['admin_type']='';
            if ($check_admin->num_rows() == 0) {
				
				$this->setErrorMessage('error', 'This link has been removed.','admin_adminlogin_link_has_removed');
				redirect(COMPANY_NAME);
            }else{
				$this->data['reset_id'] = $reset_id;
				$this->load->view(COMPANY_NAME.'/templates/reset_password.php', $this->data);
			}
			
        } else {
            redirect(COMPANY_NAME.'/dashboard');
        }
    }
	/**
	* 
	* company reset password
	*
    * @param string $new_password  company new password
    * @param string $confirm_password  company confirm password
    * @param string $reset_id  company password reset id
	* @return HTTP REDIRECT, company reset password
	*
	**/
    public function reset_password() {
		$reset_id = $this->input->post('reset_id');
		$new_password = $this->input->post('new_password');
		$confirm_password = $this->input->post('confirm_password');
		
        if ($confirm_password===$new_password) {
             $condition = array('reset_id' => $reset_id);
            $query = $this->company_model->get_all_details(COMPANY, $condition);
            if ($query->num_rows() == 1) {
			    $collection=COMPANY;
                $new_pwd = $this->input->post('new_password');
                $newdata = array('reset_id'=>'','password' => md5($new_pwd));
                $condition = array('_id' => $query->row()->_id);
                $this->company_model->update_details($collection, $newdata, $condition);
                $this->setErrorMessage('success', 'Password changed successfully','admin_adminlogin_password_changed_successfully');
                redirect(COMPANY_NAME.'/login/user_login');
            }else{
				$this->setErrorMessage('error', 'Please try again.','admin_adminlogin_please_try_again');
				redirect(COMPANY_NAME.'/login/reset_password_form/'.$reset_id);
			}
        }else{
			$this->setErrorMessage('error', 'Password doesnot matched.','admin_adminlogin_password_not_matched');
			redirect(COMPANY_NAME.'/login/reset_password_form/'.$reset_id);
		}
    }
}
/* End of file login.php */
/* Location: ./application/controllers/company/login.php */