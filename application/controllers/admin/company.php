<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');
/**
*
*	Company Management for admin 
* 
*	@package	CI
*	@subpackage	Controller
*	@author	Casperon
*
**/
class Company extends MY_Controller {

	function __construct(){
        parent::__construct();
		$this->load->helper(array('cookie','date','form'));
		$this->load->library(array('encrypt','form_validation'));		
		$this->load->model('company_model');
        $this->load->model(array('driver_model'));
        $this->load->model('mail_model');

		
		if ($this->checkPrivileges('company',$this->privStatus) == FALSE){
			redirect(ADMIN_ENC_URL);
		}
		$c_fun = $this->uri->segment(3);
        $restricted_function = array( 'delete_companyprofile', 'change_company_status_global');
        if (in_array($c_fun, $restricted_function) && $this->data['isDemo'] === TRUE) {
            $this->setErrorMessage('error', 'You are in demo version. you can\'t do this action.','admin_template_demo_version');
            redirect($_SERVER['HTTP_REFERER']);
            die;
        }
    }
    /**
	* 
	* its display the company list
	*
	* @return HTML,	company list page
	*
	**/	
    public function display_companylist(){     
        if ($this->checkLogin('A') == ''){
            redirect(ADMIN_ENC_URL);
        }else {
            if ($this->lang->line('company_list') != '')
				$heading = stripslashes($this->lang->line('company_list'));
			else $heading = 'Companies List';
			$this->data['heading']=$heading;
            
                $filterArr = array();
                    if (isset($_GET['type']) && $_GET['type'] != '' && isset($_GET['value']) && $_GET['value'] != '' || isset($_GET['locations_id']) && $_GET['locations_id'] != '') { 
                        if (isset($_GET['type']) && $_GET['type'] != '') {
                            $this->data['type'] = $_GET['type'];
                        }
                    
                        if (isset($_GET['value']) && $_GET['value'] != '') {
                            $this->data['value'] = $_GET['value'];
                            $filter_val = $this->data['value'];
                        }
                        
                        $this->data['filter'] = 'filter';
                            if(isset($_GET['type']) &&  $_GET['type'] == 'mobile_number') { 
                                $filterArr = array('phonenumber' => $filter_val,'dail_code' => $_GET['country']);
                            }else if($_GET['type'] == 'location') {   
                                $locations_id = $_GET['locations_id'];
                                $filterArr = array("locality.city" => trim($locations_id));
                            }else if(isset($_GET['type'])){ 
                                $filterArr = array($this->data['type'] => $filter_val);
                            } 
                    } 
                    
                    $this->data['companylist'] = $companylist = $this->company_model->get_all_details(COMPANY, '',  '', '', '', $filterArr);  
                    if(isset($_GET['export']) && ($_GET['export'] == 'excel') && $companylist->num_rows() > 0){  
                        $this->load->helper('export_helper');
                        export_companys_list($companylist->result(),$this->data);
                    } 
            $this->data['locationsList'] = $this->driver_model->get_selected_fields(LOCATIONS, array('status' => 'Active'),array('city','city_name','_id'),array('city_name' => 'ASC'));
            $this->load->view(ADMIN_ENC_URL.'/company/display_companydetails',$this->data);
        }
    }
    /**
	* 
	* its display the add or edit company page
	*
	* @param string $company_id  Company MongoDB\BSON\ObjectId
	* @return HTML,	add or edit company page
	*
	**/	
	public function add_edit_company(){  
		if ($this->checkLogin('A') == ''){
			redirect(ADMIN_ENC_URL);
		}else {
			$company_id = $this->uri->segment(4, 0); 
            $form_mode = FALSE;
            
			if ($this->lang->line('admin_add_new_company') != '')
				$heading = stripslashes($this->lang->line('admin_add_new_company'));
			else $heading = 'Add New Company';
            if ($company_id != '') {
                $condition = array('_id' => MongoID($company_id));
                $this->data['companydetails'] = $companydetails = $this->company_model->get_all_details(COMPANY, $condition);  
                if ($this->data['companydetails']->num_rows() != 1) {
                    redirect(ADMIN_ENC_URL.'/company/display_companylist');
                }
                $form_mode = TRUE;
				if ($this->lang->line('edit_company') != '')
					$heading = stripslashes($this->lang->line('edit_company'));
			    else $heading = 'Edit Company';
              
            } 
            $this->data['form_mode'] = $form_mode;
            $this->data['heading'] = $heading;
            $this->load->view(ADMIN_ENC_URL.'/company/add_edit_company', $this->data);
		}
	}
    /**
	* 
	* its insert or edit company page
	*
	* @param string $company_id  Company MongoDB\BSON\ObjectId
	* @param string $CompanyName  Company Name
	* @param string $phonenumber  Company Phone Number
	* @param text $address  Company Address
	* @param string $city  Company city
	* @param string $state  Company state
	* @param string $country  Company country
	* @param string $zipcode  Company Zip code
	* @param string $username  Company user name
	* @param string $password  Company password
	* @param string $password_confirm  Company password confirmation
	* @param string $email  Company email
	* @return HTTP REDIRECT, company list page
	*
	**/	
	public function insertEditcompanyprofile(){  
		if ($this->checkLogin('A') == ''){
			redirect(ADMIN_ENC_URL);
		}else {   
			$operators_id = $this->input->post('operators_id');
            $CompanyName = trim($this->input->post('CompanyName'));
            $phonenumber = $this->input->post('phonenumber');
            $address	 = $this->input->post('address');
            $city    = $this->input->post('city');
            $state    = $this->input->post('state');
            $country   = $this->input->post('county');
            $dailcode   = $this->input->post('dail_code');
            $zipcode    = $this->input->post('zipcode');
            $username = $this->input->post('username');
            $password = $this->input->post('password');
            $password_confirm = $this->input->post('password_confirm');
            $email = trim(strtolower($this->input->post('email')));
            $isDuplicatephone = FALSE;
            $isDuplicateName = FALSE;
            $isDuplicateEmail = FALSE;
            if ($operators_id == '') {
                $condition = array('company_name' => $CompanyName);
                $duplicate_username = $this->company_model->get_selected_fields(COMPANY, $condition, array('company_name'));
                if ($duplicate_username->num_rows() > 0) $isDuplicateName = TRUE;
				$condition = array('email' => $email);
                $duplicate_email = $this->company_model->get_selected_fields(COMPANY, $condition, array('email'));
                if ($duplicate_email->num_rows() > 0) $isDuplicateEmail = TRUE;
				$condition = array('phonenumber' => $phonenumber);
                $duplicate_phonenumber = $this->company_model->get_selected_fields(COMPANY, $condition, array('company_name'));
				if ($duplicate_phonenumber->num_rows() > 0)  $isDuplicatephone = TRUE;
			}else{
				$condition = array('email' => $email,'_id' => array('$ne' => MongoID($operators_id)));
                $duplicate_email = $this->company_model->get_selected_fields(COMPANY, $condition, array('email'));
                if ($duplicate_email->num_rows() > 0) $isDuplicateEmail = TRUE;
				$condition = array('company_name' => $CompanyName,'_id' => array('$ne' => MongoID($operators_id)));
                $duplicate_username = $this->company_model->get_selected_fields(COMPANY, $condition, array('company_name'));
				if ($duplicate_username->num_rows() > 0) $isDuplicateName = TRUE;
				$condition = array('phonenumber' => $phonenumber,'_id' => array('$ne' => MongoID($operators_id)));
                $duplicate_phonenumber = $this->company_model->get_selected_fields(COMPANY, $condition, array('company_name'));
				if ($duplicate_phonenumber->num_rows() > 0)  $isDuplicatephone = TRUE;
			}
               

            if ($isDuplicateName) { 
                $this->setErrorMessage('error', 'This company name already exist','company_name_already');
				redirect(ADMIN_ENC_URL.'/company/display_companylist');
            }
            if ($isDuplicateEmail) { 
                $this->setErrorMessage('error', 'This Email address already exist','email_already_exist');
				redirect(ADMIN_ENC_URL.'/company/display_companylist');
            }
						
		    if ($isDuplicatephone) { 
				$this->setErrorMessage('error', 'This mobile number already exist','phone_number_exist');
				redirect(ADMIN_ENC_URL.'/company/display_companylist');
		    }
			
            if ($this->input->post('status') == 'on') {
                $status = 'Active';
            } else {
                $status = 'Inactive';
            }
		
			if ($operators_id == '') {
				$dataArr = array('company_name' => $CompanyName,
								'phonenumber'=>$phonenumber,
								'password' => md5($password),
								'email' => $email,
                                'dail_code' => (string)$dailcode,
								'status' => $status,
								'created' => date('Y-m-d H:i:s'),
								'locality'=>array('address' => $address,
										'city' => $city,
										 'state'=>$state,
										 'country'=>$country,
										 'zipcode'=>$zipcode)   
                );
			} else {
				$dataArr = array('company_name' => $CompanyName,
								'phonenumber'=>$phonenumber,
								'status' => $status,
								'email' => $email,
								'modified' => date('Y-m-d H:i:s'),
								'locality'=>array('address' => $address,
												'city' => $city,
												 'state'=>$state,
												 'country'=>$country,
												 'zipcode'=>$zipcode)   
                );
			}    
			if ($operators_id == '') {
                $this->company_model->simple_insert(COMPANY, $dataArr);
                $this->setErrorMessage('success', 'company profile added successfully','company_profile_added');
            } else {
				unset($dataArr['password']);
                $condition = array('_id' => MongoID($operators_id));
               $ress=$this->company_model->update_details(COMPANY,$dataArr,$condition);

                $this->setErrorMessage('success', 'company profile updated successfully','company_profile_updated');
            }  
            $last_insert_id = $this->mongo_db->insert_id();
            if ($operators_id == '') {
				$this->mail_model->send_company_registration_mail($last_insert_id,$password,$CompanyName);
			}
            redirect(ADMIN_ENC_URL.'/company/display_companylist');
		}
	}
    /**
	* 
	* it display company detail information
	*
	* @param string $company_id  Company MongoDB\BSON\ObjectId
	* @return HTML, company detail page
	*
	**/	
    public function view_company() { 
        if ($this->checkLogin('A') == '') {
            $this->setErrorMessage('error', 'You must login first');
            redirect(ADMIN_ENC_URL);
        }
        $companyId = $this->uri->segment(4);
		if ($this->lang->line('view_company') != '')
			$heading = stripslashes($this->lang->line('view_company'));
		else
			$heading = 'View Company Details';
		
        $this->data['heading'] = $heading;

        $condition = array('_id' => MongoID($companyId));
        $this->data['company_details'] = $company_details = $this->driver_model->get_all_details(COMPANY,$condition);

        if ($company_details->num_rows() == 0) {
            $this->setErrorMessage('error', 'No records found','driver_no_records_found');
            redirect(ADMIN_ENC_URL.'/company/display_companylist');
        }

        $this->load->view(ADMIN_ENC_URL.'/company/view_company',$this->data);
    }
	/**
	* 
	* it change the company login password change page
	*
	* @param string $company_id  Company MongoDB\BSON\ObjectId
	* @return HTTP REDIRECT, company list page
	*
	**/	
    public function change_password_form() {
        if ($this->checkLogin('A') == '') {
            redirect(ADMIN_ENC_URL);
        }
        $operators_id = $this->uri->segment(4);
        
		if ($this->lang->line('admin_menu_change_password') != '')
			$heading = stripslashes($this->lang->line('admin_menu_change_password'));
		else
			$heading = 'Change Password';
		$this->data['heading'] = $heading;
        $condition = array('_id' => MongoID($operators_id));
        $this->data['operatorsdetails'] = $operatorsdetails = $this->company_model->get_all_details(COMPANY, $condition);
		if($operatorsdetails->num_rows()>0){
			$this->load->view(ADMIN_ENC_URL.'/company/change_password',$this->data);
		}else{
			$this->setErrorMessage('error', 'No records found','driver_no_records_found');
			redirect(ADMIN_ENC_URL.'/company/display_companylist');
		}
    }
	/**
	* 
	* it change the company login password detail
	*
	* @param string $company_id  Company MongoDB\BSON\ObjectId
	* @param string $password  Company MongoDB\BSON\ObjectId
	* @return HTTP REDIRECT, company list page
	*
	**/	
    public function change_password() {
        if ($this->checkLogin('A') == '' || $this->input->post('password') == '') {
            redirect(ADMIN_ENC_URL);
        }
        $password = $this->input->post('password'); 
        $operators_id = $this->input->post('operators_id');
        $dataArr = array('password' => md5($this->input->post('password')));
        $condition = array('_id' => MongoID($operators_id));
        $operator_details = $this->company_model->update_details(COMPANY, $dataArr, $condition);
		$operatorinfo = $this->company_model->get_all_details(COMPANY, $condition);
        $this->company_password($password, $operatorinfo);
        $this->setErrorMessage('success', 'Password changed successfully','driver_pwd_changed_successfully');
        redirect(ADMIN_ENC_URL.'/company/display_companylist');
    }
	/**
	* 
	* it send mail about password change 
	*
	* @param string $pwd  Password 
	* @param object $operatorinfo  Operator query result object
	* @return CALLBACK, company change password
	*
	**/
    public function company_password($pwd = '', $operatorinfo) {
        $operator_name = $operatorinfo->row()->company_name;
        $newsid = '19';
        $template_values = $this->company_model->get_newsletter_template_details($newsid);
		$subject = $template_values->message['subject'];
        $adminnewstemplateArr = array('email_title' => $this->config->item('email_title'), 'logo' => $this->config->item('logo_image'), 'footer_content' => $this->config->item('footer_content'), 'meta_title' => $this->config->item('meta_title'), 'site_contact_mail' => $this->config->item('site_contact_mail'));
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
            'to_mail_id' => $operatorinfo->row()->email,
            'subject_message' => $subject,
            'body_messages' => $message
        );
		
        $email_send_to_common = $this->company_model->common_email_send($email_values);
    }
	/**
	* 
	* it delete the company profile
	*
	* @param string $company_id  Company MongoDB\BSON\ObjectId
	* @return HTTP REDIRECT,company list page
	*
	**/
    public function delete_companyprofile() { 
        if ($this->checkLogin('A') == '') {
            redirect(ADMIN_ENC_URL);
        } else {  
            $promo_id = $this->uri->segment(4, 0);  
            $condition = array('_id' => MongoID($promo_id));
            $this->company_model->commonDelete(COMPANY, $condition);
            $this->setErrorMessage('success', 'company profile deleted successfully','company_delete');
            redirect(ADMIN_ENC_URL.'/company/display_companylist');
        }
    }
	/**
	* 
	* it change the company status 
	*
	* @param string $company_id  Company MongoDB\BSON\ObjectId
	* @param string $mode  Status Active/Inactive
	* @return HTTP REDIRECT,company list page
	*
	**/
    public function change_company_status() {
        if ($this->checkLogin('A') == '') {
            redirect(ADMIN_ENC_URL);
        } else {
            $mode = $this->uri->segment(4,0);
            $driver_id = $this->uri->segment(5,0);
            $status = ($mode == '0') ? 'Inactive' : 'Active'; 
            $newdata = array('status' => $status);
            $condition = array('_id' => MongoID($driver_id));
            $ress=$this->company_model->update_details(COMPANY, $newdata, $condition);
            $this->setErrorMessage('success', 'Company Status Changed Successfully','company_status');
            redirect(ADMIN_ENC_URL.'/company/display_companylist');
        }
    }
	/**
	* 
	* it change the company status bulk
	*
	* @param string $checkbox_id  Company MongoDB\BSON\ObjectId ARRAY[]
	* @param string $statusMode  Status Active/Inactive ARRAY[]
	* @return HTTP REDIRECT,company list page
	*
	**/
     public function change_company_status_global() {
     
          if (count($_POST['checkbox_id']) > 0 && $_POST['statusMode'] != '') {
            $this->company_model->activeInactiveCommon(COMPANY,'_id');
            if (strtolower($_POST['statusMode']) == 'delete') {
                $this->setErrorMessage('success', 'company deleted successfully','company_deleted');
            } else {
                $this->setErrorMessage('success', 'company status changed successfully','company_status');
            }
            redirect(ADMIN_ENC_URL.'/company/display_companylist');
        }
        if ($this->checkLogin('A') == '') {
            redirect(ADMIN_ENC_URL);
        } else {
            $mode = $this->uri->segment(4, 0);
            $driver_id = $this->uri->segment(5, 0);
            $status = ($mode == '0') ? 'No' : 'Yes';
            $newdata = array('verify_status' => $status);
            $condition = array('_id' => MongoID($driver_id));
            $this->operators_model->update_details(COMPANY, $newdata, $condition);
            $this->setErrorMessage('success', 'company status changed successfully','company_status');
            redirect(ADMIN_ENC_URL.'/company/display_companylist');
        }
    }
	
	
}
/* End of file company.php */
/* Location: ./application/controllers/admin/company.php */