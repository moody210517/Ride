<?php if (!defined('BASEPATH'))    exit('No direct script access allowed');
/**
*
*   operator panel dashboard
* 
*   @package    CI
*   @subpackage Controller
*   @author Casperon
*
**/
class Signup extends MY_Controller {

    function __construct() {
        parent::__construct();
        $this->load->helper(array('cookie', 'date', 'form'));
        $this->load->library(array('encrypt', 'form_validation'));
        $this->load->model('dashboard_model');
		$this->load->model('app_model');
		        
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
	


	public function register(){
	
		$email = $this->input->post('email');
		if($email != null  && $email != ""){
			
			$dail_code = $this->input->post('dail_code');
			$mobile_number = $this->input->post('mobile_number');
			$operator_location = (string)$this->input->post('operator_location');	
			$returnUrl = '/operator/signup';
			$condition=array('email' => $email);					
			$duplicate_email = $this->app_model->get_all_details(OPERATORS,$condition);
			if ($duplicate_email->num_rows() > 0){
					$this->setErrorMessage('error','Email address already exists','driver_email_already');
					redirect($returnUrl);
			}		
		
			$condition = array('dail_code' => $dail_code,'mobile_number' => $mobile_number);
			
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
	
			$this->app_model->commonInsertUpdate(OPERATORS,'insert',$excludeArr,$dataArr);
			$this->setErrorMessage('success','Operator added successfully','operators_added_successfully');
			
			$last_insert_id = $this->mongo_db->insert_id();
			$this->mail_model->send_operator_register_confirmation_mail($last_insert_id,$password);
			redirect('operator/login');
			
		}else{
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
			
			$this->load->view('operator/signup',$this->data);
		}

			
	
	}

	

	public function login() {

        if ($this->lang->line('dash_driver_login') != '')
            $dash_driver_login = stripslashes($this->lang->line('dash_driver_login'));
        else
            $dash_driver_login = 'Driver Login';
		$this->data['heading'] = $dash_driver_login;
				
        if ($this->checkLogin('O') == '') {
            $this->load->view('driver/templates/login.php', $this->data);
        } else {
            redirect('driver/dashboard');
        }
	}
	






	/**
	* 
	* Displays the dashboard
	*
	* @return HTTP REDIRECT, dashboard page
	*
	**/
   
   

}
/* End of file dashboard.php */
/* Location: ./application/controllers/operator/dashboard.php */