<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

/**
*
*	Subadmin
* 
*	@package		CI
*	@subpackage		Controller
*	@author			Casperon
*
**/
class Subadmin extends MY_Controller {

	function __construct(){
        parent::__construct();
		$this->load->helper(array('cookie','date','form','inflector'));
		$this->load->library(array('encrypt','form_validation'));		
		$this->load->model('subadmin_model');
			if ($this->checkPrivileges('subadmin',$this->privStatus) == FALSE){
				redirect(ADMIN_ENC_URL);
			}	
    }
    
	/**
	*
	* Display the subadmin list
	* 	
	* @return HTML to show the list of subadmin 
	*	
	**/	
	public function display_sub_admin(){
		if ($this->checkLogin('A') == ''){
			redirect(ADMIN_ENC_URL);
		}else {
		    if ($this->lang->line('admin_subadmin_subadmin_user_list') != '') 
		    $this->data['heading']= stripslashes($this->lang->line('admin_subadmin_subadmin_user_list')); 
		    else  $this->data['heading'] = 'Sub Admin Users List';
			$condition = array();
			$this->data['admin_users'] = $this->subadmin_model->get_all_details(SUBADMIN,$condition);
			$this->load->view(ADMIN_ENC_URL.'/subadmin/display_subadmin',$this->data);
		}
	}
	
	/**
	* 
	* To change the subadmin status
	* 
	* @param string $status Subadmin status Active/InActive
	* @param string $adminid Admin MongoDB\BSON\ObjectId
	* @return http redirect to show the sub admin list page
	*
	**/
	public function change_subadmin_status(){
		if ($this->checkLogin('A') == ''){
			redirect(ADMIN_ENC_URL);
		}else {
			$mode = $this->uri->segment(4,0);
			$adminid = $this->uri->segment(5,0);
			$status = ($mode == '0')?'Inactive':'Active';
			$newdata = array('status' => $status);
			$condition = array('_id' => MongoID($adminid));
			$this->subadmin_model->update_details(SUBADMIN,$newdata,$condition);
			$this->setErrorMessage('success','Sub Admin Status Changed Successfully','admin_subadmin_admin_status_change');
			redirect(ADMIN_ENC_URL.'/subadmin/display_sub_admin');
		}
	}
	
	/**
	* 
	* Display the add Subadmin form for create a new subadmin
	*
	* @return HTML to show Add subadmin form
	*
	**/	
	public function add_sub_admin_form(){
		if ($this->checkLogin('A') == ''){
			redirect(ADMIN_ENC_URL);
		}else {
		    if ($this->lang->line('admin_subadmin_add_subadmin') != '') 
		    $this->data['heading']= stripslashes($this->lang->line('admin_subadmin_add_subadmin')); 
		    else  $this->data['heading'] = 'Add Sub admin';
			$condition = array();
			$this->load->view(ADMIN_ENC_URL.'/subadmin/add_subadmin',$this->data);
		}
	}
	
	/**
	* 
	* To add or update for create new sub admin and set access for admin management
	* 
	* @param string $subadminid subadmin id 
	* @param string $admin_name name of subadmin 
	* @param string $admin_password subadmin password encrypted formatte
	* @param string $email subadmin email
	* @param string $privArr set previllage for access the admin management Array
	* @return HTTP redirect to show the subadmin list 
	*
	**/
	public function insertEditSubadmin(){
		if ($this->checkLogin('A') == ''){
			redirect(ADMIN_ENC_URL);
		}else {

			$subadminid = $this->input->post('subadminid');
			$admin_name = $this->input->post('admin_name');
			$admin_password = md5($this->input->post('admin_password'));
			$email = $this->input->post('email');
			if ($subadminid == ''){
				$condition = array('email' => $email);
				$duplicate_admin= $this->subadmin_model->get_all_details(ADMIN,$condition);
				if ($duplicate_admin->num_rows() > 0){
					$this->setErrorMessage('error','Admin email already exists','admin_adminlogin_admin_email_already_exists');
					redirect(ADMIN_ENC_URL.'/subadmin/add_sub_admin_form');
				}else {
					$duplicate_email = $this->subadmin_model->get_all_details(SUBADMIN,$condition);
					if ($duplicate_email->num_rows() > 0){
						$this->setErrorMessage('error','Sub Admin email already exists','admin_subadmin_email_already_exist');
						redirect(ADMIN_ENC_URL.'/subadmin/add_sub_admin_form');
					
					}else {
						$condition = array('admin_name' => $admin_name);
						$duplicate_adminname = $this->subadmin_model->get_all_details(ADMIN,$condition);
						if ($duplicate_adminname->num_rows() > 0){
							$this->setErrorMessage('error','Admin name already exists','admin_adminlogin_admin_name_already_exist');
							redirect(ADMIN_ENC_URL.'/subadmin/add_sub_admin_form');
						}else {
							$duplicate_name = $this->subadmin_model->get_all_details(SUBADMIN,$condition);
							if ($duplicate_name->num_rows() > 0){
								$this->setErrorMessage('error','Sub Admin name already exists','admin_subadmin_admin_name_already_exist');
								redirect(ADMIN_ENC_URL.'/subadmin/add_sub_admin_form');
							}
							
						}
					}
				}
			}
			$excludeArr = array("email","subadminid","admin_name","admin_password");
			$privArr = array();
			foreach ($this->input->post() as $key => $val){
				if (!in_array($key, $excludeArr)){
					$privArr[$key] = $val;
				}
			} 
			
			$inputArr = array('privileges' => $privArr);
			$datestring = "%Y-%m-%d";
			$time = time();
			if ($subadminid == ''){
				$admindata = array(
					'admin_id' => time(),
					'admin_name'	=>	$admin_name,
					'admin_password'	=>	$admin_password,
					'email'	=>	$email,
					'created'	=>	mdate($datestring,$time),
					'modified'	=>	mdate($datestring,$time),
					'admin_type'	=>	'sub',
					'is_verified'	=>	'Yes',
					'status'	=>	'Active'
				);
			} else {
				$admindata = array('modified' =>mdate($datestring,$time));
			}
			$dataArr = array_merge($admindata,$inputArr);
			if($subadminid != ''){
				$condition = array('_id' => MongoID($subadminid));
			} else {
				$condition = array();
			} 
			$this->subadmin_model->add_edit_subadmin($dataArr,$condition);
			if ($subadminid == ''){
				$this->setErrorMessage('success','Subadmin added successfully','admin_subadmin_added_successfully');
			}else {
				$this->setErrorMessage('success','Subadmin updated successfully','admin_subadmin_updated_successfully');
			}
			redirect(ADMIN_ENC_URL.'/subadmin/display_sub_admin');
		
		}
	}
	
	/**
	* 
	* Display subadmin form for create a  new  subadmin
	* 
	* @param string $subadminid subadmin id 
	* @param string $privArr set previllage for access the admin management Array
	* @return HTTP redirect to show the edit subadmin page 
	*
	**/
	public function edit_subadmin_form(){
		if ($this->checkLogin('A') == ''){
			redirect(ADMIN_ENC_URL);
		}else {
		    if ($this->lang->line('admin_subadmin_edit_subadmin') != '') 
		    $this->data['heading']= stripslashes($this->lang->line('admin_subadmin_edit_subadmin')); 
		    else  $this->data['heading'] = 'Edit Subadmin';
			
			$adminid = $this->uri->segment(4,0);
			$condition = array('_id' => MongoID($adminid));
			$this->data['admin_details'] = $this->subadmin_model->get_all_details(SUBADMIN,$condition);
			if ($this->data['admin_details']->num_rows() == 1){
				$privileges = $this->data['admin_details']->row()->privileges; 
				if(is_array($privileges)){
					$this->data['privArr'] = $privileges;
				} else {
					$this->data['privArr'] = @unserialize($this->data['admin_details']->row()->privileges);
				}
				if (!is_array($this->data['privArr'])){
					$this->data['privArr'] = array();
				}
				$this->load->view(ADMIN_ENC_URL.'/subadmin/edit_subadmin',$this->data);
			}else {
				redirect(ADMIN_ENC_URL);
			}
		}
	}
	
	/**
	* 
	* Display the particular subadmin in formation
	* 
	* @param $subadmin Subadmin MongoDB\BSON\ObjectId
	* @return HTML to show the subadmin information in view subadmin page
 	*
	**/
	public function view_subadmin(){
		if ($this->checkLogin('A') == ''){
			redirect(ADMIN_ENC_URL);
		}else {
		    if ($this->lang->line('admin_subadmin_view_subadmin') != '') 
		    $this->data['heading']= stripslashes($this->lang->line('admin_subadmin_view_subadmin')); 
		    else  $this->data['heading'] = 'View Subadmin';
			
			$adminid = $this->uri->segment(4,0);
			$condition = array('_id' => MongoID($adminid));
			$this->data['admin_details'] = $this->subadmin_model->get_all_details(SUBADMIN,$condition);
			if ($this->data['admin_details']->num_rows() == 1){
				$privileges = $this->data['admin_details']->row()->privileges; 
				if(is_array($privileges)){
					$this->data['privArr'] = $privileges;
				} else {
					$this->data['privArr'] = @unserialize($this->data['admin_details']->row()->privileges);
				}
				if (!is_array($this->data['privArr'])){
					$this->data['privArr'] = array();
				}
				$this->load->view(ADMIN_ENC_URL.'/subadmin/view_subadmin',$this->data);
			}else {
				redirect(ADMIN_ENC_URL);
			}
		}
	}
	
   /**
	* 
	* Delete the record for particular subadmin
	* 
	* @param string $subadmin_id  subadmin  MongoDB\BSON\ObjectId
	* @redirect HTML to show the display subadmin page 
	*
	**/
	public function delete_subadmin(){
		if ($this->checkLogin('A') == ''){
			redirect(ADMIN_ENC_URL);
		}else {
			$subadmin_id = $this->uri->segment(4,0);
			$condition = array('_id' => MongoID($subadmin_id));
			$this->subadmin_model->commonDelete(SUBADMIN,$condition);
			$this->setErrorMessage('success','Subadmin deleted successfully','admin_subadmin_deleted_successfully');
			redirect(ADMIN_ENC_URL.'/subadmin/display_sub_admin');
		}
	}
	
   /**
	* 
	* 
	* 
	* 
	*
	**/
	public function change_subadmin_status_global(){
		if(count($_POST['checkbox_id']) > 0 &&  $_POST['statusMode'] != ''){
			$this->subadmin_model->activeInactiveCommon(SUBADMIN,'_id');
			if (strtolower($_POST['statusMode']) == 'delete'){
				$this->setErrorMessage('success','Subadmin records deleted successfully','admin_subadmin_records_deleted_success');
			}else {
				$this->setErrorMessage('success','Subadmin records status changed successfully','admin_subadmin_records_status_changed');
			}
			redirect(ADMIN_ENC_URL.'/subadmin/display_sub_admin');
		}
	}
	
    /**
	 *	To update new password for subadmin 
	 *	 
	 * @param string $subadmin_id subadmin MongoDB\BSON\ObjectId
	 * @redirect HTML to show the change password page
     *
     * */
	public function change_subadmin_password() {
		if ($this->checkLogin('A') == '') {
			redirect(ADMIN_ENC_URL);
		}
		$subadmin_id = $this->uri->segment(4);

		if ($this->lang->line('admin_change_subadmin_password') != '') 
		$this->data['heading']= stripslashes($this->lang->line('admin_change_subadmin_password')); 
		else  $this->data['heading'] = 'Change Subadmin Password';

		$condition = array('_id' => MongoID($subadmin_id));
		$this->data['subadmin_details'] = $subadmin_details = $this->subadmin_model->get_all_details(SUBADMIN, $condition);
		$this->load->view(ADMIN_ENC_URL.'/subadmin/change_password', $this->data);
	}


	/**
	 *	To update new password for subadmin and password send throw the mail
	 *	 
	 * @param string $subadmin_id subadmin MongoDB\BSON\ObjectId
	 * @redirect HTML to show the list of subadmin page
     *
     * */
	public function change_password() { 
		if ($this->checkLogin('A') == '' || $this->input->post('new_password') == '') {
			redirect(ADMIN_ENC_URL);
		}
		$password = $this->input->post('new_password');
		$subadmin_id = $this->input->post('subadmin_id');
		$dataArr = array('admin_password' => md5($this->input->post('new_password')));
		$condition = array('_id' => MongoID($subadmin_id));
		$this->subadmin_model->update_details(SUBADMIN, $dataArr, $condition);

		$subadmininfo = $this->subadmin_model->get_all_details(SUBADMIN, $condition);
		$this->send_subadmin_pwd($password, $subadmininfo);

		$this->setErrorMessage('success', 'Subadmin password changed and sent to their email','admin_subadmin_password_changed');
		redirect(ADMIN_ENC_URL.'/subadmin/display_sub_admin');
	}

	/**
	 *	send password throw subadmin mail id
	 *
     * */
	public function send_subadmin_pwd($pwd = '', $subadmininfo) {
		$default_lang=$this->config->item('default_lang_code');
		$driver_name = $subadmininfo->row()->admin_name;
		$newsid = '22';
		$template_values = $this->user_model->get_email_template($newsid,$default_lang);
		$adminnewstemplateArr = array('email_title' => $this->config->item('email_title'), 
																'logo' => $this->config->item('logo_image'), 
																'footer_content' => $this->config->item('footer_content'), 
																'meta_title' => $this->config->item('meta_title'), 
																'site_contact_mail' => $this->config->item('site_contact_mail'));
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
												'to_mail_id' => $subadmininfo->row()->email,
												'subject_message' => $template_values['subject'],
												'body_messages' => $message
											);
		$email_send_to_common = $this->subadmin_model->common_email_send($email_values);
	}
	
	
	
}

/* End of file subadmin.php */
/* Location: ./application/controllers/admin/subadmin.php */
?>