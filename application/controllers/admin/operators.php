<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');
/**
*
*	Operator Management for admin 
* 
*	@package	CI
*	@subpackage	Controller
*	@author	Casperon
*
**/
class Operators extends MY_Controller {
	function __construct(){
		parent::__construct();
		$this->load->helper(array('cookie','date','form','export_helper'));
		$this->load->library(array('encrypt','form_validation'));
		$this->load->model(array('app_model', 'mail_model', 'sms_model'));
		if ($this->checkPrivileges('operators',$this->privStatus) == FALSE){
				redirect(ADMIN_ENC_URL);
		}
		$c_fun = $this->uri->segment(3);
		$restricted_function = array( 'delete_operator', 'change_operator_status_global','change_password');
		if (in_array($c_fun, $restricted_function) && $this->data['isDemo'] === TRUE) {
			$this->setErrorMessage('error', 'You are in demo version. you can\'t do this action.','admin_template_demo_version');
			redirect($_SERVER['HTTP_REFERER']);
			die;
		}
	}

	/**
	* 
	* it display the operator list
	*
	* @return HTTP REDIRECT, operator list page
	*
	**/	
	public function index(){
			if ($this->checkLogin('A') == ''){
					redirect(ADMIN_ENC_URL);
			}else {
					redirect(ADMIN_ENC_URL.'/operators/display_operators_list');
			}
	}

	/**
	* 
	* it display the operator list
	*
	* @return HTML, operator list page
	*
	**/	
	public function display_operators_list(){
        if ($this->checkLogin('A') == ''){
                redirect(ADMIN_ENC_URL);
        }else {
                if ($this->lang->line('admin_menu_operators_list') != '')
                        $this->data['heading'] = stripslashes($this->lang->line('admin_menu_operators_list'));
                else $this->data['heading'] = 'Operators list';
                
                $filterArr = array();
                    if (isset($_GET['type']) && $_GET['type'] != '' && isset($_GET['value']) && $_GET['value'] != ''){
                        if (isset($_GET['type']) && $_GET['type'] != '') {
                            $this->data['type'] = $_GET['type'];
                        }
                    
                        if (isset($_GET['value']) && $_GET['value'] != '') {
                            $this->data['value'] = $_GET['value'];
                            $filter_val = $this->data['value'];
                        }
                        
                        $this->data['filter'] = 'filter';
                            if(isset($_GET['type']) &&  $_GET['type'] == 'mobile_number') {
                                $filterArr = array($this->data['type'] => $filter_val,'dail_code' => $_GET['country']);
                            }else if(isset($_GET['type'])){ 
                                $filterArr = array($this->data['type'] => $filter_val);
                            } 
                    }             
                    $operatorsCount = $this->app_model->get_all_counts(OPERATORS, array(),$filterArr);
                    
                    $this->data['operatorsList'] = $operatorsList = $this->app_model->get_all_details(OPERATORS, '', '', '', '', $filterArr);
                    $this->data['locationList'] = $locationList = $this->app_model->get_all_details(LOCATIONS,array()); 
                    if(isset($_GET['export']) && ($_GET['export'] == 'excel') && $operatorsList->num_rows() > 0){  
                            $this->load->helper('export_helper');
                            export_operators_list($operatorsList->result(),$this->data);
                    }
                $this->load->view(ADMIN_ENC_URL.'/operators/display_operators_list',$this->data);
        }
	}

	/**
	* 
	* it add or edit the operator information page
	*
	* @param string $operator_id  Operator MongoDB\BSON\ObjectId
	* @return HTML ,Add/edit operator information page
	*
	**/	
	public function add_edit_operator_form(){
			if ($this->checkLogin('A') == ''){
					redirect(ADMIN_ENC_URL);
			}else {
					$operator_id = $this->uri->segment(4,0);
					$form_mode=FALSE;
					if ($this->lang->line('admin_menu_add_operator') != '')
							$heading = stripslashes($this->lang->line('admin_menu_add_operator'));
					else $heading = 'Add Operator';
					$this->data['operatorsList'] = $this->app_model->get_selected_fields(OPERATORS,array('status' => 'Active'),array('_id','operator_name','email'));
					$this->data['locationList'] = $this->app_model->get_all_details(LOCATIONS, array('status' => 'Active'), array('city' => 'ASC'));
					if($operator_id != ''){
							$condition = array('_id' => MongoID($operator_id));
							$this->data['operator_details'] = $this->app_model->get_all_details(OPERATORS,$condition);
							if ($this->data['operator_details']->num_rows() != 1){
									redirect(ADMIN_ENC_URL.'/operators/display_operators_list');
							}
							$form_mode=TRUE;
							if ($this->lang->line('admin_menu_edit_operator') != '')
									$heading = stripslashes($this->lang->line('admin_menu_edit_operator'));
							else $heading = 'Edit Operator';
					}
					$this->data['form_mode'] = $form_mode;
					$this->data['heading'] = $heading;
					$this->load->view(ADMIN_ENC_URL.'/operators/add_edit_operator',$this->data);
			}
	}
	/**
	* 
	* it insert or update the operator information
	*
	* @param string $operator_id  Operator MongoDB\BSON\ObjectId
	* @param string $email  Operator email
	* @param string $dail_code  Operator country code
	* @param string $mobile_number  Operator Mobile Number
	* @param string $operator_location  Operator Location
	* @return HTML ,operator list page
	*
	**/

	public function insertEditOperators(){ 
		
			if ($this->checkLogin('A') == ''){
					redirect(ADMIN_ENC_URL);
			}else {
					$operator_id = $this->input->post('operator_id');						
					$email = $this->input->post('email');
					$dail_code = $this->input->post('dail_code');
					$mobile_number = $this->input->post('mobile_number');
					$operator_location = (string)$this->input->post('operator_location');
			
					$returnUrl = ADMIN_ENC_URL.'/operators/add_edit_operator_form';
					if($operator_id != ''){
							$returnUrl =  ADMIN_ENC_URL.'/operators/add_edit_operator_form/'.$operator_id;
					}
				
					if($operator_id==''){
							$condition=array('email' => $email);
					}else{
							$condition=array('email' => $email, '_id' => array('$ne' => MongoID($operator_id)));
					}
					$duplicate_email = $this->app_model->get_all_details(OPERATORS,$condition);
					if ($duplicate_email->num_rows() > 0){
							$this->setErrorMessage('error','Email address already exists','driver_email_already');
							redirect($returnUrl);
					}		
				
					if($operator_id == ''){
						$condition = array('dail_code' => $dail_code,'mobile_number' => $mobile_number);
					}else{
						$condition = array('dail_code' => $dail_code, 'mobile_number' => $mobile_number, '_id' => array('$ne' => MongoID($operator_id)));
					}
					$duplicate_phone = $this->app_model->get_all_details(OPERATORS,$condition);
					if ($duplicate_phone->num_rows() > 0){
							$this->setErrorMessage('error','Mobile number already exists','operators_mobile_already_exists');
							redirect($returnUrl);
					}			

					$excludeArr = array("status", "operator_id", "mobile_number", "dail_code", "operator_location", "address", "country", "state", "city", "postal_code");
				
					$addressArr['address'] = array('address' => $this->input->post('address'),
																			'country' => $this->input->post('country'),
																			'state' => $this->input->post('state'),
																			'city' => $this->input->post('city'),
																			'postal_code' => $this->input->post('postal_code')
																	);
			
					$password = $this->get_rand_str();
				
					if ($this->input->post('status') != ''){
							$status = 'Active';
					} else {
							$status = 'Inactive';
					}	
				
					$operator_data = array(
											'status' => $status,
											'created' => date('Y-m-d H:i:s'),
											'password' => md5($password),
											'dail_code' => (string) $this->input->post('dail_code'),
											'mobile_number' => (string) $this->input->post('mobile_number')
										);
					
				  $operator_data['operator_location'] = MongoID($operator_location);		
										
				  $dataArr = array_merge($operator_data,$addressArr);
				  
					if($operator_id == ''){
							$this->app_model->commonInsertUpdate(OPERATORS,'insert',$excludeArr,$dataArr);
							$this->setErrorMessage('success','Operator added successfully','operators_added_successfully');
							
							$last_insert_id = $this->mongo_db->insert_id();
							$this->mail_model->send_operator_register_confirmation_mail($last_insert_id,$password);
				
					}	else	{
							$condition=array('_id'=>MongoID($operator_id));
							unset($dataArr['password']);
							unset($dataArr['created']);
							$this->app_model->commonInsertUpdate(OPERATORS,'update',$excludeArr,$dataArr,$condition);
							$this->setErrorMessage('success','Operator details updated successfully','operators_det_update_successfully');
					}
					redirect(ADMIN_ENC_URL.'/operators/display_operators_list');
			}
	}
	/**
	* 
	* it delete the operator information
	*
	* @param string $operator_id  Operator MongoDB\BSON\ObjectId
	* @return HTTP REDIRECT ,operator list page
	*
	**/
	public function delete_operator(){
		if ($this->checkLogin('A') == ''){
			redirect(ADMIN_ENC_URL);
		} else {
			$operator_id = $this->uri->segment(4,0);
			$condition = array('_id' => MongoID($operator_id));
			$operator_info = $this->app_model->get_all_details(OPERATORS,$condition);
			$this->app_model->commonDelete(OPERATORS, $condition);
			$this->setErrorMessage('success', 'Operator deleted successfully','admin_operator_deleted_changed');
			redirect(ADMIN_ENC_URL.'/operators/display_operators_list');
		}
	}
	/**
	* 
	* it change the operator status 
	*
	* @param string $operator_id  Operator MongoDB\BSON\ObjectId
	* @param string $mode  Active/Inactive
	* @return HTTP REDIRECT ,operator list page
	*
	**/
	public function change_operator_status(){
			if ($this->checkLogin('A') == ''){
					redirect(ADMIN_ENC_URL);
			}else {
					$mode = $this->uri->segment(4,0);
					$operator_id = $this->uri->segment(5,0);
					$status = ($mode == '0')?'Inactive':'Active';
					$newdata = array('status' => $status);
					$condition = array('_id' => MongoID($operator_id));
					$this->app_model->update_details(OPERATORS,$newdata,$condition);
					$this->setErrorMessage('success', 'Operator Status Changed Successfully','admin_opertor_status_change');
					redirect(ADMIN_ENC_URL.'/operators/display_operators_list');
			}
	}
	/**
	* 
	* it change the operator status bulk
	*
	* @param string $checkbox_id  Operator MongoDB\BSON\ObjectId ARRAY[]
	* @param string $statusMode  Active/Inactive  ARRAY[]
	* @return HTTP REDIRECT ,operator list page
	*
	**/
	public function change_operator_status_global(){
			if (count($_POST['checkbox_id']) > 0 && $_POST['statusMode'] != '') {
		$this->app_model->activeInactiveCommon(OPERATORS, '_id');
		if (strtolower($_POST['statusMode']) == 'delete') {
			$this->setErrorMessage('success', 'Operator records deleted successfully','admin_operator_record_delete');
		} else {
			$this->setErrorMessage('success', 'Operator status changed successfully','admin_opertor_status_change');
		}
		redirect(ADMIN_ENC_URL.'/operators/display_operators_list');
	}
	}
	
	/**
	* 
	* it display the operator information details
	*
	* @param string $operatorId  Operator MongoDB\BSON\ObjectId
	* @return HTML ,operator information page
	*
	**/
	public function view_operator() {
		if ($this->checkLogin('A') == '') {
			$this->setErrorMessage('error', 'You must login first','admin_driver_login_first');
			redirect(ADMIN_ENC_URL);
		}
		$operatorId = $this->uri->segment(4);
		if ($this->lang->line('operator_view_details') != '') 
				$this->data['heading']= stripslashes($this->lang->line('operator_view_details')); 
		else  $this->data['heading'] = 'View Operator Details';
		$condition = array('_id' => MongoID($operatorId));
		$this->data['operator_details'] = $operator_details = $this->app_model->get_all_details(OPERATORS, $condition);

		if ($operator_details->num_rows() == 0) {
			$this->setErrorMessage('error', 'No records found','admin_driver_no_records_found');
			redirect(ADMIN_ENC_URL.'/operators/display_operators_list');
		}

		$this->load->view(ADMIN_ENC_URL.'/operators/view_operator', $this->data);
	}
	/**
	* 
	* it display the operator password change page
	*
	* @param string $operatorId  Operator MongoDB\BSON\ObjectId
	* @return HTML ,operator password change page
	*
	**/
	public function change_password_form() {
		if ($this->checkLogin('A') == '') {
			redirect(ADMIN_ENC_URL);
		}
		$operatorId = $this->uri->segment(4);
		if ($this->lang->line('operator_change_password') != '') 
				$this->data['heading']= stripslashes($this->lang->line('operator_change_password')); 
		else  $this->data['heading'] = 'Change Operator Password';
		$condition = array('_id' => MongoID($operatorId));
		$this->data['operator_details'] = $operator_details = $this->app_model->get_all_details(OPERATORS, $condition);
		$this->load->view(ADMIN_ENC_URL.'/operators/change_password', $this->data);
	}
	/**
	* 
	* it change the operator password information
	*
	* @param string $operatorId  Operator MongoDB\BSON\ObjectId
	* @param string $new_password  Operator Password
	* @return HTTP REDIRECT ,operator list page
	*
	**/
	public function change_password() {
		if ($this->checkLogin('A') == '' || $this->input->post('new_password') == '') {
			redirect(ADMIN_ENC_URL);
		}
		$password = $this->input->post('new_password');
		$operatorId = $this->input->post('operator_id');
		$dataArr = array('password' => md5($this->input->post('new_password')));
		$condition = array('_id' => MongoID($operatorId));
		$operator_details = $this->app_model->update_details(OPERATORS, $dataArr, $condition);
		$operatorinfo = $this->app_model->get_all_details(OPERATORS, $condition);
		$this->send_operator_pwd($password, $operatorinfo);
		$this->setErrorMessage('success', 'Operator password changed and sent to operator successfully','admin_operator_password_changed');
		redirect(ADMIN_ENC_URL.'/operators/display_operators_list');
	}
	/**
	* 
	* it send mail about operator password change from admin
	*
	* @param string $pwd  Operator password
	* @param object $operatorinfo  Operator query result object
	* @return CALLBACK ,change the operator password information function
	*
	**/

	public function send_operator_pwd($pwd = '', $operatorinfo) {
		$default_lang=$this->config->item('default_lang_code');
		$driver_name = $operatorinfo->row()->operator_name;
		$newsid = '2';
		$template_values = $this->app_model->get_email_template($newsid,$default_lang);
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
			'to_mail_id' => $operatorinfo->row()->email,
			'subject_message' => $template_values['subject'],
			'body_messages' => $message
		);
		$email_send_to_common = $this->app_model->common_email_send($email_values);
	}
}

/* End of file operators.php */
/* Location: ./application/controllers/admin/operators.php */