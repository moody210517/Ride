<?php if (!defined('BASEPATH'))    exit('No direct script access allowed');
/**
*
*	admin panel login and settings
* 
*	@package		CI
*	@subpackage		Controller
*	@author			Casperon
*
**/
class Adminlogin extends MY_Controller {

    function __construct() {
        parent::__construct();
        $this->load->helper(array('cookie', 'date', 'form'));
        $this->load->library(array('encrypt', 'form_validation'));
		$this->load->model('admin_model');
		$this->load->model('app_model');
		
		$c_fun = $this->uri->segment(3);
		$restricted_function = array('save_smtp_settings','admin_global_settings','change_admin_password');
		if(in_array($c_fun,$restricted_function) && $this->data['isDemo']===TRUE){
			$this->setErrorMessage('error','You are in demo version. you can\'t do this action.','admin_template_demo_version');
			redirect($_SERVER['HTTP_REFERER']); die;
		}      		
    }

    /**
	* 
	* its redirect to admin dashboard page
	*
	* @return HTTP REDIRECT,admin dashboard page
	*
	**/	
    public function index() {
	    if ($this->lang->line('admin_menu_dashboard') != '') 
		$this->data['heading']= stripslashes($this->lang->line('admin_menu_dashboard')); 
		else  $this->data['heading'] = 'Dashboard';
	
        if ($this->checkLogin('A') == '') {
            $this->check_admin_session();
        }
        if ($this->checkLogin('A') == '') {
            $this->load->view(ADMIN_ENC_URL.'/templates/login.php', $this->data);
        } else {
            redirect(ADMIN_ENC_URL.'/dashboard');
        }
    }
	/**
	* 
	* it login into the admin panel
	*
	* @param string $admin_name  admin name
	* @param string $admin_password  admin password
	* @return HTTP REDIRECT,admin dashboard page
	*
	**/	
    public function admin_login() {

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
			$this->load->view(ADMIN_ENC_URL.'/templates/login.php', $this->data);
			
        } else {
			
            $name = $this->input->post('admin_name');
			$pwd = md5($this->input->post('admin_password'));
			
			
            $collection = SUBADMIN;
            if ($name == $this->config->item('admin_name')) {
                $collection = ADMIN;
            }
			$condition = array('admin_name' => $name, 'admin_password' => $pwd, 'is_verified' => 'Yes', 'status' => 'Active');
			
					
			echo "ok";
			$query = $this->admin_model->get_all_details($collection, $condition);		
			echo "sss";

						
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
                    APP_NAME.'_session_admin_mode' => $collection,
                    APP_NAME.'_session_admin_privileges' => json_encode($priv)
                );

                $this->session->set_userdata($admindata);
                $newdata = array(
                    'last_login_date' => date("Y-m-d H:i:s"),
                    'last_login_ip' => $this->input->ip_address()
                );
                $condition = array('admin_id' => $query->row()->admin_id);
                $this->admin_model->update_details($collection, $newdata, $condition);
                $this->setErrorMessage('success', 'Logged in Successfully','admin_adminlogin_logged');
				redirect(ADMIN_ENC_URL.'/dashboard/admin_dashboard');
				
            } else {
				$this->setErrorMessage('error', 'Invalid Login Details','admin_adminlogin_invalid_login');
				
			}
			

            redirect(ADMIN_ENC_URL);
        }
    }
	/**
	* 
	* logout from the admin panel
	*
	* @return HTTP REDIRECT,admin login page
	*
	**/	
    public function admin_logout() {
        $newdata = array(
            'last_logout_date' => date("Y-m-d H:i:s")
        );
        $collection = SUBADMIN;
        if ($this->session->userdata(APP_NAME.'_session_admin_name') == $this->config->item('admin_name')) {
            $collection = ADMIN;
        }
        $condition = array('admin_id' => $this->checkLogin('A'));
        $this->admin_model->update_details($collection, $newdata, $condition);
        $admindata = array(
            APP_NAME.'_session_admin_id' => '',
            APP_NAME.'_session_admin_name' => '',
            APP_NAME.'_session_admin_email' => '',
            APP_NAME.'_session_admin_mode' => '',
            APP_NAME.'_session_admin_privileges' => ''
        );
        $this->session->unset_userdata($admindata);
        $this->setErrorMessage('success', 'Successfully logout from your account','admin_adminlogin_logout_account');
        redirect(ADMIN_ENC_URL);
    }
	/**
	* 
	* admin forgot password page
	*
	* @return HTML,admin forgot password page
	*
	**/	
    public function admin_forgot_password_form() {
        if ($this->checkLogin('A') == '') {
				$this->load->view(ADMIN_ENC_URL.'/templates/forgot_password.php', $this->data);
        } else {
            redirect(ADMIN_ENC_URL.'/dashboard');
        }
    }
	/**
	* 
	* admin forgot password 
	*
	* @param string $email  admin email
	* @return HTTP REDIRECT,admin forgot password page
	*
	**/	
    public function admin_forgot_password() {
		if ($this->lang->line('form_validation_email') != ''){
			$form_validation_email = stripslashes($this->lang->line('form_validation_email'));
		}else{
			$form_validation_email = 'Email';
		}
        $this->form_validation->set_rules('email', $form_validation_email, 'required|valid_email');
        if ($this->form_validation->run() === FALSE) {
        
            $this->load->view(ADMIN_ENC_URL.'/templates/forgot_password.php', $this->data);
        } else {
            $email = $this->input->post('email');
            $collection = SUBADMIN;
            if ($email == $this->config->item('email')) {
                $collection = ADMIN;
            }
            $condition = array('email' => $email);
            $adminVal = $this->admin_model->get_all_details($collection, $condition);
            if ($adminVal->num_rows() == 1) {
                $reset_id = md5(time());
                $reset_data = array('reset_id' => $reset_id);
                $condition = array('email' => $email);
                $this->admin_model->update_details($collection, $reset_data, $condition);
								
				$reset_url = base_url().ADMIN_ENC_URL.'/adminlogin/admin_reset_password_form/'.$reset_id;
                $this->send_admin_pwd($reset_url, $adminVal);
                $this->setErrorMessage('success', 'Check your mail to reset password.','admin_adminlogin_reset_password');
                redirect(ADMIN_ENC_URL);
            } else {
                $this->setErrorMessage('error', 'Email id not matched in our records','admin_adminlogin_email_not_matched');
                redirect(ADMIN_ENC_URL.'/adminlogin/admin_forgot_password_form');
            }
            redirect(ADMIN_ENC_URL);
        }
    }
	/**
	* 
	* check the admin session 
	*
	* @return CALLBACK,admin dashboard page
	*
	**/
    public function check_admin_session() {
        $admin_session = $this->input->cookie(APP_NAME.'_admin_session', FALSE);
        if ($admin_session != '') {
            $admin_id = $this->encrypt->decode($admin_session);
            $mode = $admin_session[APP_NAME.'_session_admin_mode'];
            $condition = array('admin_id' => $admin_id);
            $query = $this->admin_model->get_all_details($mode, $condition);
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
                $this->admin_model->update_details(ADMIN, $newdata, $condition);
            }
        }
    }
	/**
	* 
	* send forgot password mail
	*
	* @param string $reset_url  password reset link
    * @param string $query  admin query result from admin
	* @return HTTP REDIRECT,admin forgot password page
	*
	**/
    public function send_admin_pwd($reset_url = '', $query) {
        $newsid = '1';
        $template_values = $this->admin_model->get_newsletter_template_details($newsid);
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
        $email_send_to_common = $this->admin_model->common_email_send($email_values);
    }

    /**
	* 
	* Displays the admin change password
	*
    * @return HTML, admin change password page
	*
	**/
    public function change_admin_password_form() {
	    if ($this->lang->line('admin_menu_change_password') != '') 
		$this->data['heading']= stripslashes($this->lang->line('admin_menu_change_password')); 
		else  $this->data['heading'] = 'Change Password';
        if ($this->checkLogin('A') == '') {
            redirect(ADMIN_ENC_URL);
        } else {
        
            $this->load->view(ADMIN_ENC_URL.'/templates/header.php', $this->data);
            $this->load->view(ADMIN_ENC_URL.'/adminsettings/changepassword.php', $this->data);
            $this->load->view(ADMIN_ENC_URL.'/templates/footer.php', $this->data);
        }
    }

    /**
	* 
	* Change admin password
	*
    * @param string $new_password  admin new password
    * @param string $confirm_password  admin confirm new password
    * @return HTTP REDIRECT,admin change password page
	*
	**/
    public function change_admin_password() {
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
            $this->load->view(ADMIN_ENC_URL.'/templates/header.php', $this->data);
            $this->load->view(ADMIN_ENC_URL.'/adminsettings/changepassword.php', $this->data);
            $this->load->view(ADMIN_ENC_URL.'/templates/footer.php', $this->data);
        } else {
            $name = $this->session->userdata(APP_NAME.'_session_admin_name');
            $pwd = md5($this->input->post('password'));
            $collection = SUBADMIN;
            if ($name == $this->config->item('admin_name')) {
                $collection = ADMIN;
            }
            $condition = array('admin_name' => $name, 'admin_password' => $pwd, 'is_verified' => 'Yes', 'status' => 'Active');
            $query = $this->admin_model->get_all_details($collection, $condition);
            if ($query->num_rows() == 1) {
                $new_pwd = $this->input->post('new_password');
                $newdata = array('admin_password' => md5($new_pwd));
                $condition = array('_id' => $query->row()->_id);
                $this->admin_model->update_details($collection, $newdata, $condition);
                $this->setErrorMessage('success', 'Password changed successfully','admin_adminlogin_password_changed_successfully');
                redirect(ADMIN_ENC_URL.'/adminlogin/change_admin_password_form');
            } else {
                $this->setErrorMessage('error', 'Invalid current password','admin_adminlogin_invalid_current_password');
            }
            redirect(ADMIN_ENC_URL.'/adminlogin/change_admin_password_form');
        }
    }

    /**
	* 
	* Its displays admin users list
	*
	* @return HTML, admin users list page
	*
	**/
    public function display_admin_list() {
        if ($this->checkLogin('A') == '') {
            redirect(ADMIN_ENC_URL);
        } else {
            if ($this->checkPrivileges('admin', '0') == TRUE) {
			    if ($this->lang->line('admin_admin_users_list') != '') 
		        $this->data['heading']= stripslashes($this->lang->line('admin_admin_users_list')); 
		        else  $this->data['heading'] = 'Admin Users List';
                $condition = array();
                $this->data['admin_users'] = $this->admin_model->get_all_details(ADMIN, $condition);
                $this->load->view(ADMIN_ENC_URL.'/adminsettings/display_admin', $this->data);
            } else {
                redirect(ADMIN_ENC_URL);
            }
        }
    }

    /**
	* 
	* Change admin status
	*
    * @param string $mode  0 or 1 will denotes the admin status
    * @param string $banner_id  admin MongoDB\BSON\ObjectId
	* @return HTTP REDIRECT, admin user list page
	*
	**/
    public function change_admin_status() {
        if ($this->checkLogin('A') == '') {
            redirect(ADMIN_ENC_URL);
        } else {
            if ($this->checkPrivileges('admin', '2') == TRUE) {
                $mode = $this->uri->segment(4, 0);
                $adminid = $this->uri->segment(5, 0);
                $status = ($mode == '0') ? 'Inactive' : 'Active';
                $newdata = array('status' => $status);
                $condition = array('id' => $adminid);
                $this->admin_model->update_details(ADMIN, $newdata, $condition);
                $this->setErrorMessage('success', 'Admin User Status Changed Successfully','admin_adminlogin_admin_user_status_successfully');
                redirect(ADMIN_ENC_URL.'/adminlogin/display_admin_list');
            } else {
                redirect(ADMIN_ENC_URL);
            }
        }
    }

    /**
	* 
	* Displays admin settings
	*
	* @return HTML, admin settings page
	*
	**/
    public function admin_global_settings_form() {
        if ($this->checkLogin('A') == '') {
            redirect(ADMIN_ENC_URL);
        } else {
            if ($this->checkPrivileges('admin', '2') == TRUE) {
			    if ($this->lang->line('admin_settings_admin_settings') != '') 
		        $this->data['heading']= stripslashes($this->lang->line('admin_settings_admin_settings')); 
		        else  $this->data['heading'] = 'Admin Settings';
                $this->data['admin_settings'] = $result = $this->admin_model->getAdminSettings();
                $this->load->view(ADMIN_ENC_URL.'/adminsettings/edit_admin_settings', $this->data);
            } else {
                redirect(ADMIN_ENC_URL);
            }
        }
    }

    /**
	* 
	* Updates admin settings
	*
    * @param string $form_mode  admin form mode denotes which tab form is submitted
    * @param string $admin_name  admin login name
    * @param string $email  admin email
    * @param string $site_mode  site mode
	* @return HTTP REDIRECT, admin settings page
	*
	**/ 
    public function admin_global_settings() {    
        $form_mode = $this->input->post('form_mode');
        $selFields = array('id', 'admin_id', 'email');
        if ($form_mode == 'main_settings') {
            $dataArr = array('modified' => date("Y-m-d H:i:s"));
            $admin_name = $this->input->post('admin_name');
            $email = $this->input->post('email');
            $condition = array('admin_name' => $admin_name, 'admin_id !=' => '1');
            $duplicate_admin = $this->admin_model->get_selected_fields(ADMIN, $condition, $selFields);
            if ($duplicate_admin->num_rows() > 0) {
                $this->setErrorMessage('error', 'Admin name already exists','admin_adminlogin_admin_name_already_exist');
                redirect(ADMIN_ENC_URL.'/adminlogin/admin_global_settings_form');
            } else {
                $condition = array('admin_name' => $admin_name);
                $duplicate_sub_admin = $this->admin_model->get_selected_fields(SUBADMIN, $condition, $selFields);
                if ($duplicate_sub_admin->num_rows() > 0) {
                    $this->setErrorMessage('error', 'Sub Admin name exists','admin_adminlogin_sub_admin_name_exists');
                    redirect(ADMIN_ENC_URL.'/adminlogin/admin_global_settings_form');
                } else {
                    $condition = array('email' => $email, 'admin_id !=' => '1');
                    $duplicate_admin_mail = $this->admin_model->get_selected_fields(ADMIN, $condition, $selFields);
                    if ($duplicate_admin_mail->num_rows() > 0) {
                        $this->setErrorMessage('error', 'Admin email already exists','Admin email already exists');
                        redirect(ADMIN_ENC_URL.'/adminlogin/admin_global_settings_form');
                    } else {
                        $condition = array('email' => $email);
                        $duplicate_mail = $this->admin_model->get_selected_fields(SUBADMIN, $condition, $selFields);
                        if ($duplicate_mail->num_rows() > 0) {
                            $this->setErrorMessage('error', 'Sub Admin email exists','admin_adminlogin_subadminadmin_email_already_exists');
                            redirect(ADMIN_ENC_URL.'/adminlogin/admin_global_settings_form');
                        }
                    }
                }
            }



            $dataArr = array();
            $config['encrypt_name'] = TRUE;
            $config['overwrite'] = FALSE;
            $config['allowed_types'] = 'jpg|jpeg|gif|png|ico';
            $config['max_size'] = 2000;
            $config['upload_path'] = './images/logo';
            $this->load->library('upload', $config);
            if ($this->upload->do_upload('logo_image')) {
                $logoDetails = $this->upload->data();
                $dataArr['logo_image'] = $logoDetails['file_name'];
            }

            if ($this->upload->do_upload('favicon_image')) {
                $faviconDetails = $this->upload->data();
                $dataArr['favicon_image'] = $faviconDetails['file_name'];
            }
            $condition = array('admin_id' => '1');
            
            if($this->input->post('customer_service_number') != ''){
                $dataArr['customer_service_number'] = (string)$this->input->post('customer_service_number');
            }
            
            //echo '<pre>'; print_r($dataArr); die;
            
            $excludeArr = array('form_mode', 'logo_image', 'favicon_image','site_mode','customer_service_number');
            $this->admin_model->commonInsertUpdate(ADMIN, 'update', $excludeArr, $dataArr, $condition);

            $this->admin_model->saveAdminSettings();
            $this->session->set_userdata(APP_NAME.'_session_admin_name', $admin_name);
            $this->setErrorMessage('success', 'Admin details updated successfully','admin_adminlogin_admin_detail_update');
            redirect(ADMIN_ENC_URL.'/adminlogin/admin_global_settings_form');
        } else { 
            $excludeArr = array('seo');
            $dataArr = array();
            
            
            $condition = array('admin_id' => '1');
            if ($form_mode == 'social') {
               $dataArr = array('facebook_app_id' => $this->input->post('facebook_app_id'),'facebook_app_id_android' => (string)$this->input->post('facebook_app_id_android'));
            }
			if ($form_mode == 'seo') {
             $seoArr =  array(
                'meta_title' => $this->input->post('meta_title'),
                'meta_keyword' => $this->input->post('meta_keyword'),
                'meta_description' => $this->input->post('meta_description'),
                'google_verification_code' => $this->input->post('google_verification_code',FALSE),
                'google_verification' => $this->input->post('google_verification')
               );
            $dataArr['seo'] = $seoArr;
            }
			
			$site_mode = $this->input->post('site_mode');  
            
			
			if($form_mode == 'pool'){
				$excludeArr = array('share_pooling','pool_cat_image','pool_icon_normal','pool_icon_active','pool_map_car_image');
				
				$share_pooling = $this->input->post('share_pooling');  
				if($share_pooling == 'on'){
					$share_pooling  = '1';
				} else {
					$share_pooling  = '0';
				}
				$dataArr['share_pooling'] = $share_pooling;				
				
				if ($_FILES['pool_cat_image']['name'] != '') {
					$pconfig01['encrypt_name'] = TRUE;
					$pconfig01['overwrite'] = FALSE;
					$pconfig01['allowed_types'] = 'jpg|jpeg|gif|png|bmp';
					$pconfig01['max_size'] = 2000;
					$pconfig01['upload_path'] = './images/category/';
					$this->load->library('upload', $pconfig01);
					$this->load->initialize($pconfig01);
					if ($this->upload->do_upload('pool_cat_image')) {
						$imageDetails = $this->upload->data();
						$pool_cat_image = $imageDetails['file_name'];
					} else {
						$imageDetails = $this->upload->display_errors();
						$this->setErrorMessage('error', $imageDetails);
						redirect(ADMIN_ENC_URL.'/adminlogin/admin_pool_settings');
					}
					$dataArr['pool_cat_image'] = $pool_cat_image;
				}
				if ($_FILES['pool_icon_normal']['name'] != '') {					
					$pconfig1['encrypt_name'] = TRUE;
					$pconfig1['overwrite'] = FALSE;
					$pconfig1['allowed_types'] = 'jpg|jpeg|gif|png|bmp';
					$pconfig1['max_size'] = 2000;
					$pconfig1['upload_path'] = './images/icons/';
					$this->load->library('upload', $pconfig1);
					$this->load->initialize($pconfig1);
					$image_info = getimagesize($_FILES["pool_icon_normal"]["tmp_name"]);
					$image_width = $image_info[0];
					$image_height = $image_info[1];
					if($image_width == 150 && $image_height == 150){
						if ($this->upload->do_upload('pool_icon_normal')) {
							$imageDetails = $this->upload->data();
							$pool_icon_normal = $imageDetails['file_name'];
						} else {
							$imageDetails = $this->upload->display_errors();
							echo $imageDetails; die;
							$this->setErrorMessage('error', $imageDetails);
							redirect(ADMIN_ENC_URL.'/adminlogin/admin_pool_settings');
						}
					}else{
						$this->setErrorMessage('error',"Image size should be 150 X 150 Pixels",'admin_driver_image_size');
						redirect(ADMIN_ENC_URL.'/adminlogin/admin_pool_settings');
					}				   
					$dataArr['pool_icon_normal'] = $pool_icon_normal;
				}
				if ($_FILES['pool_icon_active']['name'] != '') {
					$pconfig2['encrypt_name'] = TRUE;
					$pconfig2['overwrite'] = FALSE;
					$pconfig2['allowed_types'] = 'jpg|jpeg|gif|png|bmp';
					$pconfig2['max_size'] = 2000;
					$pconfig2['upload_path'] = './images/icons/';
					$this->load->library('upload', $pconfig2);
					$this->load->initialize($pconfig2);
					$image_info = getimagesize($_FILES["pool_icon_active"]["tmp_name"]);
					$image_width = $image_info[0];
					$image_height = $image_info[1];
					if($image_width == 150 && $image_height == 150){
						if ($this->upload->do_upload('pool_icon_active')) {
							$imageDetails = $this->upload->data();
							$pool_icon_active = $imageDetails['file_name'];
						} else {
							$imageDetails = $this->upload->display_errors();
							$this->setErrorMessage('error', $imageDetails);
							redirect(ADMIN_ENC_URL.'/adminlogin/admin_pool_settings');
						}
					}else{
						$this->setErrorMessage('error',"Image size should be 150 X 150 Pixels",'admin_driver_image_size');
						redirect(ADMIN_ENC_URL.'/adminlogin/admin_pool_settings');
					}				   
					$dataArr['pool_icon_active'] = $pool_icon_active;
				}
				if ($_FILES['pool_map_car_image']['name'] != '') {
					$pconfig3['encrypt_name'] = TRUE;
					$pconfig3['overwrite'] = FALSE;
					$pconfig3['allowed_types'] = 'jpg|jpeg|gif|png|bmp';
					$pconfig3['max_size'] = 2000;
					$pconfig3['upload_path'] = './images/icons/';
					$this->load->library('upload', $pconfig3);
					$this->load->initialize($pconfig3);
					$image_info = getimagesize($_FILES["pool_map_car_image"]["tmp_name"]);
					$image_width = $image_info[0];
					$image_height = $image_info[1];
					if($image_width == 70 && $image_height == 70){
						if ($this->upload->do_upload('pool_map_car_image')) {
							$imageDetails = $this->upload->data();
							$pool_map_car_image = $imageDetails['file_name'];
						} else {
							$imageDetails = $this->upload->display_errors();
							$this->setErrorMessage('error', $imageDetails);
							redirect(ADMIN_ENC_URL.'/adminlogin/admin_pool_settings');
						}
					}else{
						$this->setErrorMessage('error',"Image size should be 70 X 70 Pixels",'admin_driver_image_size_pixel');
						redirect(ADMIN_ENC_URL.'/adminlogin/admin_pool_settings');
					}				   
					$dataArr['pool_map_car_image'] = $pool_map_car_image;
				}
			}else{
                
                
                if ($form_mode == 'app') {
                    if($site_mode == 'on'){
                        $site_mode  = 'production';
                    } else {
                            $site_mode  = 'development';
                    }
                    $dataArr['site_mode'] = $site_mode;
                }
				$config['overwrite'] = FALSE;
				$config['encrypt_name'] = TRUE;
				$config['allowed_types'] = 'jpg|jpeg|gif|png|ico|';
				$config['max_size'] = 2000;
				$config['upload_path'] = './images/logo';
				$this->load->library('upload', $config);
				if(isset($_FILES['facebook_image'])){
					if ($_FILES['facebook_image']['name'] != '') {
						if ($this->upload->do_upload('facebook_image')) {
								$logoDetails = $this->upload->data();
								$dataArr['facebook_image'] = $logoDetails['file_name'];
						}else{
							$logoDetails = $this->upload->display_errors();
							$this->setErrorMessage('error', $logoDetails);
							echo "<script>window.history.go(-1);</script>";
							exit;
						  }
					}
				}
				$config['overwrite'] = FALSE;
				$config['encrypt_name'] = TRUE;
				$config['allowed_types'] = 'pem';
				$config['max_size'] = 2000;
				$config['upload_path'] = './certificates';
			   
				$this->upload->initialize($config);
				$this->load->library('upload', $config);
			   
			   if(isset($_FILES['ios_user_dev'])){
					if ($_FILES['ios_user_dev']['name'] != '') {
						if ($this->upload->do_upload('ios_user_dev')) {
							$logoDetails = $this->upload->data();
							$dataArr['ios_user_dev'] = $logoDetails['file_name'];
						}else{
							$logoDetails = $this->upload->display_errors();
							$this->setErrorMessage('error', $logoDetails);
							echo "<script>window.history.go(-1);</script>";
							exit;
						}
					}
				}
			  
				if(isset($_FILES['ios_driver_dev'])){
					if ($_FILES['ios_driver_dev']['name'] != '') {
						if ($this->upload->do_upload('ios_driver_dev')) {
							$logoDetails = $this->upload->data();
							$dataArr['ios_driver_dev'] = $logoDetails['file_name'];
						}else{
							$logoDetails = $this->upload->display_errors();
							$this->setErrorMessage('error', $logoDetails);
							echo "<script>window.history.go(-1);</script>";
							exit;
						}
					}
				}
				
				if(isset($_FILES['ios_driver_prod'])){
					if ($_FILES['ios_driver_prod']['name'] != '') {
						if ($this->upload->do_upload('ios_driver_prod')) {
							$logoDetails = $this->upload->data();
							$dataArr['ios_driver_prod'] = $logoDetails['file_name'];
						}else{
							$logoDetails = $this->upload->display_errors();
							$this->setErrorMessage('error', $logoDetails);
							echo "<script>window.history.go(-1);</script>";
							exit;
						}
					}
				}
				if(isset($_FILES['ios_user_prod'])){
					if ($_FILES['ios_user_prod']['name'] != '') {
						if ($this->upload->do_upload('ios_user_prod')) {
							$logoDetails = $this->upload->data();
							$dataArr['ios_user_prod'] = $logoDetails['file_name'];
						}else{
							$logoDetails = $this->upload->display_errors();
							$this->setErrorMessage('error', $logoDetails);
							echo "<script>window.history.go(-1);</script>";
							exit;
						}
					}
				}
				
				if($form_mode == 'app'){
					$excludeArr = array('seo','wal_recharge_min_amount','wal_recharge_max_amount');
					$dataArr['wal_recharge_min_amount'] = intval($this->input->post('wal_recharge_min_amount'));
					$dataArr['wal_recharge_max_amount'] = intval($this->input->post('wal_recharge_max_amount'));
				}
			}
            
            $this->admin_model->commonInsertUpdate(ADMIN, 'update', $excludeArr, $dataArr, $condition);
            $this->admin_model->saveAdminSettings();
            $this->setErrorMessage('success', 'Admin details updated successfully','admin_adminlogin_admin_detail_update');
			if($form_mode == 'app'){
				redirect(ADMIN_ENC_URL.'/adminlogin/admin_app_settings');
			} else if($form_mode == 'pool'){
				redirect(ADMIN_ENC_URL.'/adminlogin/admin_pool_settings');
			} else {
				redirect(ADMIN_ENC_URL.'/adminlogin/admin_global_settings_form');
			}
        }
    }
	

    /**
	* 
	* Check admin side bar session
	*
    * @param string $id  admin session id
	* @return HTTP REDIRECT, admin side bar
	*
	**/
    public function check_set_sidebar_session($id) {
        $admindata = array('session_sidebar_id' => $id);
        $this->session->set_userdata($admindata);
    }

    /**
	* 
	* Admin smtp settings page
	*
	* @return HTML, admin SMTP settings 
	*
	**/
    public function admin_smtp_settings() {
        if ($this->checkLogin('A') == '') {
            redirect(ADMIN_ENC_URL);
        } else {
            if ($this->checkPrivileges('admin', '2') == TRUE) {
			    if ($this->lang->line('admin_settings_smtp_settings') != '') 
		        $this->data['heading']= stripslashes($this->lang->line('admin_settings_smtp_settings')); 
		        else  $this->data['heading'] = 'SMTP Settings';
                $this->data['admin_settings'] = $result = $this->admin_model->get_selected_fields(ADMIN, array(), array('smtp'));
                $this->load->view(ADMIN_ENC_URL.'/adminsettings/smtp_settings', $this->data);
            } else {
                redirect(ADMIN_ENC_URL);
            }
        }
    }

    /**
	* 
	* Admin currency settings page
	*
	* @return HTML, admin currency settings 
	*
	**/
    public function admin_currency_settings() {
        if ($this->checkLogin('A') == '') {
            redirect(ADMIN_ENC_URL);
        } else {
            if ($this->checkPrivileges('admin', '2') == TRUE) {
			    if ($this->lang->line('admin_settings_currency_setting') != '') 
		        $this->data['heading']= stripslashes($this->lang->line('admin_settings_currency_setting')); 
		        else  $this->data['heading'] = 'Currency Settings';
                $this->load->view(ADMIN_ENC_URL.'/adminsettings/currency_settings', $this->data);
            } else {
                redirect(ADMIN_ENC_URL);
            }
        }
    }

    /**
	* 
	* Admin country settings page
	*
	* @return HTML, admin country settings 
	*
	**/
    public function admin_country_settings() {
        if ($this->checkLogin('A') == '') {
            redirect(ADMIN_ENC_URL);
        } else {
            if ($this->checkPrivileges('admin', '2') == TRUE) {
				if ($this->lang->line('admin_settings_currency_setting') != '') 
		        $this->data['heading']= stripslashes($this->lang->line('admin_settings_currency_setting')); 
		        else  $this->data['heading'] = 'Currency Settings';
                $this->load->view(ADMIN_ENC_URL.'/adminsettings/country_settings', $this->data);
            } else {
                redirect(ADMIN_ENC_URL);
            }
        }
    }

   /**
	* 
	* Update and write SMTP settings
	*
	* @return HTTP REDIRECT, admin SMTP settings
	*
	**/
    public function save_smtp_settings() {
        if ($this->checkLogin('A') == '') {
            redirect(ADMIN_ENC_URL);
        } else {
            if ($this->checkPrivileges('admin', '2') == TRUE) {
                $condition = array('admin_id' => '1');
                $this->admin_model->commonInsertUpdate(ADMIN, 'update', array(), array(), $condition);
                $smtp_settings_val = $this->input->post("smtp");
                $config = '<?php ';
                foreach ($smtp_settings_val as $key => $val) {
                    $value = addslashes($val);
                    $config .= "\n\$config['$key'] = '$value'; ";
                }
                $config .= "\n ?>";
                $file = 'commonsettings/dectar_smtp_settings.php';
				 file_put_contents($file, $config);
                $this->setErrorMessage('success', 'SMTP settings updated successfully','admin_adminlogin_smtp_settings_updated');
                redirect(ADMIN_ENC_URL.'/adminlogin/admin_smtp_settings');
            } else {
                redirect(ADMIN_ENC_URL);
            }
        }
    }

   /**
	* 
	* Update and write currency settings
	*
	* @return HTTP REDIRECT, admin currency settings
	*
	**/
    public function save_currency_settings() {
        if ($this->checkLogin('A') == '') {
            redirect(ADMIN_ENC_URL);
        } else {
            if ($this->checkPrivileges('admin', '2') == TRUE) {
                $condition = array('admin_id' => '1');
                $this->admin_model->commonInsertUpdate(ADMIN, 'update', array(), array(), $condition);
                $currency_settings_val = $this->input->post("currency");
                $config = '<?php ';
                foreach ($currency_settings_val as $key => $val) {
                    $value = addslashes($val);
                    $config .= "\n\$config['$key'] = '$value'; ";
                }
                $config .= "\n ?>";
                $file = 'commonsettings/dectar_currency_settings.php';
                file_put_contents($file, $config);
                $this->setErrorMessage('success', 'Currency settings updated successfully','admin_adminlogin_currency_setting_updated');
                redirect(ADMIN_ENC_URL.'/adminlogin/admin_currency_settings');
            } else {
                redirect(ADMIN_ENC_URL);
            }
        }
    }

    /**
	* 
	* Update and write country settings
	*
	* @return HTTP REDIRECT, admin country settings
	*
	**/
    public function save_country_settings() {
        if ($this->checkLogin('A') == '') {
            redirect(ADMIN_ENC_URL);
        } else {
            if ($this->checkPrivileges('admin', '2') == TRUE) {
                $condition = array('admin_id' => '1');
                $this->admin_model->commonInsertUpdate(ADMIN, 'update', array(), array(), $condition);
                $countryId = $this->input->post("countryId");
                $config = '<?php ';
                foreach ($this->data['countryList'] as $country) {
                    if ($countryId == $country->_id) {
                        $countryName = addslashes($country->name);
                        $config .= "\n\$config['countryId'] = '$country->_id'; ";
                        $config .= "\n\$config['countryName'] = '$countryName'; ";
                        $config .= "\n\$config['countryCode'] = '$country->cca3'; ";
                        $config .= "\n\$config['dialCode'] = '$country->dial_code'; ";
                    }
                }
                $config .= "\n ?>";
                $file = 'commonsettings/dectar_country_settings.php';
                file_put_contents($file, $config);
                $this->setErrorMessage('success', 'Country settings updated successfully','admin_adminlogin_country_setting_updated');
                redirect(ADMIN_ENC_URL.'/adminlogin/admin_country_settings');
            } else {
                redirect(ADMIN_ENC_URL);
            }
        }
    }
	
	
   /**
	* 
	* Admin reset password form
	*
    * @param string $reset_id  admin password edit reset id
	* @return HTML, admin reset settings
	*
	**/
    public function admin_reset_password_form() {
        if ($this->checkLogin('A') == '') {
			$reset_id = $this->uri->segment(4);			
			$condition = array('reset_id' => $reset_id);
            $check_admin = $this->admin_model->get_selected_fields(ADMIN, $condition, array('email'));
			$this->data['admin_type']='';
            if ($check_admin->num_rows() == 0) {
				$check_admin = $this->admin_model->get_selected_fields(SUBADMIN, $condition, array('email'));
				if ($check_admin->num_rows() == 0) {
					$this->setErrorMessage('error', 'This link has been removed.','admin_adminlogin_link_has_removed');
				}else{
					$this->data['admin_type'] = SUBADMIN;
				}
            }else{
				$this->data['admin_type'] = ADMIN;
			}
			if($this->data['admin_type']==''){
				redirect(ADMIN_ENC_URL);
			}else{
				$this->data['reset_id'] = $reset_id;
				$this->load->view(ADMIN_ENC_URL.'/templates/reset_password.php', $this->data);
			}
        } else {
            redirect(ADMIN_ENC_URL.'/dashboard');
        }
    }
	
    /**
	* 
	* Admin reset password
	*
    * @param string $new_password  admin new password
    * @param string $confirm_password  admin confirm password
    * @param string $reset_id  admin password reset id
	* @return HTTP REDIRECT, admin reset password
	*
	**/
    public function reset_password() {
		$reset_id = $this->input->post('reset_id');
		$new_password = $this->input->post('new_password');
		$confirm_password = $this->input->post('confirm_password');
		
        if ($confirm_password===$new_password) {
            $collection = $this->input->post('type');
            $condition = array('reset_id' => $reset_id);
            $query = $this->admin_model->get_all_details($collection, $condition);
            if ($query->num_rows() == 1) {
                $new_pwd = $this->input->post('new_password');
                $newdata = array('reset_id'=>'','admin_password' => md5($new_pwd));
                $condition = array('admin_id' => $query->row()->admin_id);
                $this->admin_model->update_details($collection, $newdata, $condition);
                $this->setErrorMessage('success', 'Password changed successfully','admin_adminlogin_password_changed_successfully');
                redirect(ADMIN_ENC_URL.'/adminlogin/admin_login');
            }else{
				$this->setErrorMessage('error', 'Please try again.','admin_adminlogin_please_try_again');
				redirect(ADMIN_ENC_URL.'/adminlogin/admin_reset_password_form/'.$reset_id);
			}
        }else{
			$this->setErrorMessage('error', 'Password doesnot matched.','admin_adminlogin_password_not_matched');
			redirect(ADMIN_ENC_URL.'/adminlogin/admin_reset_password_form/'.$reset_id);
		}
    }
	
	
	/**
	* 
	* Admin site settings
	*
	* @return HTML, admin site settings page
	*
	**/
	public function admin_site_settings() {
        if ($this->checkLogin('A') == '') {
            redirect(ADMIN_ENC_URL);
        } else {
            if ($this->checkPrivileges('admin', '2') == TRUE) {
			    if ($this->lang->line('admin_menu_menu_settings') != '') 
		        $this->data['heading']= stripslashes($this->lang->line('admin_menu_menu_settings')); 
		        else  $this->data['heading'] = 'Site Settings';
				$form_mode = TRUE;
				$this->data['form_mode'] = $form_mode;
				$this->data['footerMenuLists'] = $this->data['topMenuLists'] = $this->data['allPagesArr'] = array();
				
				$top_menu_added = $this->admin_model->get_selected_fields(MENU,array('name'=>'top_menu'),array('added_pages','add_home_navigation'));
				
				$this->data['header_home'] = 'no';
				if($top_menu_added->num_rows()>0){
					$this->data['topMenuLists'] = $top_menu_added->row()->added_pages;
					$this->data['header_home'] = $top_menu_added->row()->add_home_navigation;
				}
				
				$footer_menu_added = $this->admin_model->get_selected_fields(MENU,array('name'=>'footer_menu'),array('added_pages','add_home_navigation'));
				$this->data['footer_home'] = 'no';
				if($footer_menu_added->num_rows()>0){
					$this->data['footerMenuLists'] = $footer_menu_added->row()->added_pages;
					$this->data['footer_home'] = $footer_menu_added->row()->add_home_navigation;
				}
				
				$condition = array('status' => 'Publish');
				$selectArr = array('_id','page_name');
				$sortArr = array('page_name' => 'Asc');
				$avail_pages =  $this->admin_model->get_selected_fields(CMS,$condition,$selectArr,$sortArr);
				
				if($avail_pages->num_rows()>0){
					foreach($avail_pages->result_array() as $page){
					$this->data['allPagesArr'][] = array('_id' => (string)$page['_id'],'name' => (string)$page['page_name']);
					
					}
				}
			
				$this->load->view(ADMIN_ENC_URL.'/adminsettings/menu_settings', $this->data);
				
            } else {
                redirect(ADMIN_ENC_URL);
            }
        }
    }
	
    
    /**
	* 
	* Insert menu
	*
    * @param string $added_top_menu  site top menu
    * @param string $header_home  site header menu
    * @param string $footer_menu  site footer menu
    * @param string $footer_home_checked  site footer menu yes/no
    * @param string $reset_id  admin password reset id
	* @return HTTP REDIRECT, admin reset password
	*
	**/
	public function insertMenu(){
		
		
		$top_menu = $this->input->post('added_top_menu');
		$header_home_checked = $this->input->post('header_home');
		$addedTopMenu = explode(',',$top_menu);
		$home_nav = 'no';
		if($header_home_checked !=''){
			$home_nav = 'yes';
		}
		$checkTop = $this->admin_model->get_selected_fields(MENU,array('name'=>'top_menu'),array('_id'));
		if($checkTop->num_rows()>0){
			$this->admin_model->update_details(MENU,array('added_pages'=>$addedTopMenu,'add_home_navigation'=>$home_nav),array('name'=>'top_menu'));
		}else{
			$this->admin_model->simple_insert(MENU,array('name'=>'top_menu','added_pages'=>$addedTopMenu,'add_home_navigation'=>$home_nav));
		}
		
		$footer_menu = $this->input->post('added_footer_menu');
		 $footer_home_checked = $this->input->post('footer_home');
		$addedFooterMenu = explode(',',$footer_menu);
		$home_nav = 'no';
		if($footer_home_checked !=''){
			$home_nav = 'yes';
		}
		$checkFooter = $this->admin_model->get_selected_fields(MENU,array('name'=>'footer_menu'),array('_id'));
		if($checkFooter->num_rows()>0){
			$this->admin_model->update_details(MENU,array('added_pages'=>$addedFooterMenu,'add_home_navigation'=>$home_nav),array('name'=>'footer_menu'));
		}else{
			$this->admin_model->simple_insert(MENU,array('name'=>'footer_menu','added_pages'=>$addedFooterMenu,'add_home_navigation'=>$home_nav));
		}
		
		$this->setErrorMessage('success', 'Menu has been updated..','admin_adminlogin_menu_updated');
		redirect(ADMIN_ENC_URL.'/adminlogin/admin_site_settings/');
	}
	
	
	/**
	* 
	* Admin app settings
	*
	* @return HTML, admin app settings page
	*
	**/
    public function admin_app_settings() {
        if ($this->checkLogin('A') == '') {
            redirect(ADMIN_ENC_URL);
        } else {
            if ($this->checkPrivileges('admin', '2') == TRUE) {
			    if ($this->lang->line('admin_menu_app_settings') != '') 
		        $this->data['heading']= stripslashes($this->lang->line('admin_menu_app_settings')); 
		        else  $this->data['heading'] = 'App Settings';

                $this->data['admin_settings'] = $result = $this->admin_model->getAdminSettings();
                $this->load->view(ADMIN_ENC_URL.'/adminsettings/app_settings', $this->data);
            } else {
                redirect(ADMIN_ENC_URL);
            }
        }
    }
	
	/**
	* 
	* Write distance unit
	*
    * @param string $du  site distance unit
	* @return HTML, admin app settings
	*
	**/
    public function save_distance_unit() {
        if ($this->checkLogin('A') == '') {
            redirect(ADMIN_ENC_URL);
        } else {
            if ($this->checkPrivileges('admin', '2') == TRUE) {
                $du = $this->input->post("du");
				
				if($du=="mi"){
					$short_du = "mi";
					$long_du = "Miles";
				}else{
					$short_du = "km";
					$long_du = "Kilometer";
				}
				
                $config = '<?php ';
				$config .= "\n\$config['short_du'] = '$short_du'; ";
				$config .= "\n\$config['long_du'] = '$long_du'; ";
                $config .= "\n ?>";
                $file = 'commonsettings/dectar_du_settings.php';
                file_put_contents($file, $config);
                $this->setErrorMessage('success', 'Distance Unit updated successfully','admin_adminlogin_distance_unit_setting_updated');
                redirect(ADMIN_ENC_URL.'/adminlogin/admin_app_settings');
            } else {
                redirect(ADMIN_ENC_URL);
            }
        }
    }
	
	
	/**
	* 
	* Admin pool settings
	*
    * @param string $du  site distance unit
	* @return HTML, admin pool settings
	*
	**/
    public function admin_pool_settings() {
        if ($this->checkLogin('A') == '') {
            redirect(ADMIN_ENC_URL);
        } else {
            if ($this->checkPrivileges('admin', '2') == TRUE) {
			    if ($this->lang->line('admin_menu_pool_settings') != '') 
		        $this->data['heading']= stripslashes($this->lang->line('admin_menu_pool_settings')); 
		        else  $this->data['heading'] = 'Share Pool Settings';

                $this->data['admin_settings'] = $result = $this->admin_model->getAdminSettings();
                $this->load->view(ADMIN_ENC_URL.'/adminsettings/pool_settings', $this->data);
            } else {
                redirect(ADMIN_ENC_URL);
            }
        }
    }
	
	

}

/* End of file adminlogin.php */
/* Location: ./application/controllers/admin/adminlogin.php */