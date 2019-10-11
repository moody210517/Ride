<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');
/**
*
*	Promocode Management for admin 
* 
*	@package	CI
*	@subpackage	Controller
*	@author	Casperon
*
**/
class Promocode extends MY_Controller { 

	function __construct(){
        parent::__construct();
		$this->load->helper(array('cookie','date','form'));
		$this->load->library(array('encrypt','form_validation'));		
		$this->load->model('promocode_model');
		if ($this->checkPrivileges('promocode',$this->privStatus) == FALSE){
			redirect(ADMIN_ENC_URL);
		}
    }
    
    /**
	* 
	* Displays the promocode list
	*
	* @return HTTP REDIRECT, promocode list page
	*
	**/
   	public function index(){	
		if ($this->checkLogin('A') == ''){
			redirect(ADMIN_ENC_URL);
		} else {
			redirect(ADMIN_ENC_URL.'/promocode/display_promocode');
		}
	}
	
	/**
	* 
	* Displays the promocode list
	*
	* @return HTML, promocode list page
	*
	**/
	public function display_promocode(){
		if ($this->checkLogin('A') == ''){
			redirect(ADMIN_ENC_URL);
		}else {
		    if ($this->lang->line('admin_menu_coupon_code_list') != '') 
		    $this->data['heading']= stripslashes($this->lang->line('admin_menu_coupon_code_list')); 
		    else  $this->data['heading'] = 'Coupon Codes List';
			$condition = array();
			$this->data['promocodeList'] = $this->promocode_model->get_all_details(PROMOCODE,$condition);
			$this->load->view(ADMIN_ENC_URL.'/promocode/display_promocode',$this->data);
		}
	}
	
	/**
	* 
	* Its displays add promocode page
	*
	* @return HTML, promocode page
	*
	**/
	public function add_promocode_form(){
		if ($this->checkLogin('A') == ''){
			redirect(ADMIN_ENC_URL);
		}else {
		    if ($this->lang->line('admin_promocode_add_new_coupon_code') != '') 
		    $this->data['heading']= stripslashes($this->lang->line('admin_promocode_add_new_coupon_code')); 
		    else  $this->data['heading'] = 'Add New Coupon Code';
			//$this->data['heading'] = 'Add New Coupon Code';
			$this->data['code'] = $this->get_rand_str(10);
			$pChk = $this->promocode_model->get_selected_fields(PROMOCODE,array('promo_code'=>$this->data['code']),array('promo_code'));
			while($pChk->num_rows()>0){
				$this->data['code'] = $this->get_rand_str(10);
				$pChk = $this->promocode_model->get_selected_fields(PROMOCODE,array('promo_code'=>$this->data['code']),array('promo_code'));
			}
			$this->load->view(ADMIN_ENC_URL.'/promocode/add_promocode',$this->data);
		}
	}
	
	/**
	* 
	* Insert/update promocode informations
	*
    * @param string $promo_id  promocode MongoDB\BSON\ObjectId
    * @param string $promo_code  promo code
    * @param string $promo_value  promocode value
    * @param string $price_type  promocode price type on/off
    * @param string $status  promocode status
	* @return HTTP REDIRECT, promocode list page
	*
	**/
	public function insertEditPromoCode(){
		if ($this->checkLogin('A') == ''){
			redirect(ADMIN_ENC_URL);
		}else {
			$promo_code = strtoupper($this->input->post('promo_code'));
			$promo_id = $this->input->post('promo_id');
			$promo_value = $this->input->post('promo_value');
			if ($promo_id == ''){
				$condition = array('promo_code' => $promo_code);
				$duplicate_code = $this->promocode_model->get_selected_fields(PROMOCODE,$condition,array('promo_code'));  
			}else {
				$condition = array('promo_code' => $promo_code);
				$duplicate_code = $this->promocode_model->check_code_exist($condition,$promo_id); 
			} 
			
			if ($duplicate_code->num_rows() > 0){
				$this->setErrorMessage('error','This coupon already exists','admin_coupon_already_exist');
				redirect(ADMIN_ENC_URL.'/promocode/display_promocode');
			}
			
			$promocode_data = array();
			
			if($this->input->post('price_type')=='on'){
				$price_type='Flat';
			}else{
				$price_type='Percent';
				if($promo_value>100){
					$promo_value = 100;
				}
			}
			
			$promocode_data['code_type']	=	$price_type;
			$promocode_data['promo_value']	=	$promo_value;
			if($this->input->post('status')=='on'){
				$status='Active';
			}else{
				$status='Inactive';
			}
			$promocode_data['status']	=	$status;
			
			$excludeArr = array("promo_id","price_type","status","promo_value");

			$inputArr=array();
			if ($promo_id == ''){
				$inputArr = array('created'	=>date("Y-m-d H:i:s"),'no_of_usage'=>intval(0),'promo_users'=>'');
			}
			$excludeArr[] = 'promo_code';
			$dataArr = array_merge($inputArr,$promocode_data);
			if ($promo_id == ''){
				$condition = array();
				$dataArr['promo_code'] = (string) $promo_code;
				$this->promocode_model->commonInsertUpdate(PROMOCODE,'insert',$excludeArr,$dataArr,$condition);
				$this->setErrorMessage('success','Coupon Code added successfully','admin_coupon_code_add');
			}else{
				$condition = array('_id' => MongoID($promo_id));
				$this->promocode_model->commonInsertUpdate(PROMOCODE,'update',$excludeArr,$dataArr,$condition);
				$this->setErrorMessage('success','Coupon Code updated successfully','admin_coupon_code_update');
			}
			redirect(ADMIN_ENC_URL.'/promocode/display_promocode');
		}
	}
	
	/**
	* 
	* Its displays edit promocode page
	*
    * @param string $promo_id  promocode MongoDB\BSON\ObjectId
	* @return HTML, promocode page
	*
	**/
	public function edit_promocode_form(){
		if ($this->checkLogin('A') == ''){
			redirect(ADMIN_ENC_URL);
		}else {
		    if ($this->lang->line('admin_promocode_edit_coupon_code') != '') 
		    $this->data['heading']= stripslashes($this->lang->line('admin_promocode_edit_coupon_code')); 
		    else  $this->data['heading'] = 'Edit Coupon Code';
			$promo_id = $this->uri->segment(4,0);
			$condition = array('_id' => MongoID($promo_id));
			$this->data['promocode_details'] = $this->promocode_model->get_all_details(PROMOCODE,$condition);
			if ($this->data['promocode_details']->num_rows() == 1){
				$this->load->view(ADMIN_ENC_URL.'/promocode/edit_promocode',$this->data);
			}else {
				redirect(ADMIN_ENC_URL);
			}
		}
	}
	
	/**
	* 
	* Change promocode status
	*
    * @param string $promo_id  promocode Mongo Id
    * @param string $mode  promocode status mode 0/1
	* @return HTTP REDIRECT, promocode list page
	*
	**/
	public function change_promocode_status(){
		if ($this->checkLogin('A') == ''){
			redirect(ADMIN_ENC_URL);
		}else {
			$mode = $this->uri->segment(4,0);
			$promo_id = $this->uri->segment(5,0);
			$status = ($mode == '0')?'Inactive':'Active';
			$newdata = array('status' => $status);
			$condition = array('_id' => MongoID($promo_id));
			$this->promocode_model->update_details(PROMOCODE,$newdata,$condition);
			$this->setErrorMessage('success','Coupon Code Status Changed Successfully','admin_coupon_code_status_change');
			redirect(ADMIN_ENC_URL.'/promocode/display_promocode');
		}
	}
	
	/**
	* 
	* Delete promocode
	*
    * @param string $promo_id  promocode MongoDB\BSON\ObjectId
	* @return HTTP REDIRECT, promocode list page
	*
	**/
	public function delete_promocode(){
		if ($this->checkLogin('A') == ''){
			redirect(ADMIN_ENC_URL);
		}else {
			$promo_id = $this->uri->segment(4,0);
			$condition = array('_id' => MongoID($promo_id));
			$this->promocode_model->commonDelete(PROMOCODE,$condition);
			$this->setErrorMessage('success','Coupon Code deleted successfully','admin_coupon_code_delete_success');
			redirect(ADMIN_ENC_URL.'/promocode/display_promocode');
		}
	}
	
	/**
	* 
	* Change multiple promocode status
	*
    * @param string $statusMode  active/inactive/delete will denotes the promocode state activity
    * @param string $checkbox_id  promocode id's
	* @return HTTP REDIRECT, promocode list page
	*
	**/
	public function change_promocode_status_global(){
		if(count($_POST['checkbox_id']) > 0 &&  $_POST['statusMode'] != ''){
			$this->promocode_model->activeInactiveCommon(PROMOCODE,'_id');
			if (strtolower($_POST['statusMode']) == 'delete'){
				$this->setErrorMessage('success','Coupon Code deleted successfully','admin_coupon_code_delete_success');
			}else {
				$this->setErrorMessage('success','Coupon Code status changed successfully','admin_coupon_code_status_change');
			}
			redirect(ADMIN_ENC_URL.'/promocode/display_promocode');
		}
	}
}

/* End of file promocode.php */
/* Location: ./application/controllers/admin/promocode.php */