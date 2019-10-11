<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');
/**
*
*	vehicle Management for admin 
* 
*	@package	CI
*	@subpackage	Controller
*	@author	Casperon
*
**/

class Vehicle extends MY_Controller {

	function __construct(){
		parent::__construct();
		$this->load->helper(array('cookie','date','form'));
		$this->load->library(array('encrypt','form_validation'));
		$this->load->model('vehicle_model');
		if ($this->checkPrivileges('vehicle',$this->privStatus) == FALSE){
			redirect(ADMIN_ENC_URL);
		}
	}
	/**
	* 
	* it displays the vehicle list page
	*
	* @return HTTP REDIRECT,vehicle list page
	*
	**/
	public function index(){
		if ($this->checkLogin('A') == ''){
			redirect(ADMIN_ENC_URL);
		}else {
			redirect(ADMIN_ENC_URL.'/vehicle/display_vehicle_list');
		}
	}
	/**
	* 
	* it displays the vehicle list page
	*
	* @return HTML ,vehicle list page
	*
	**/
	public function display_vehicle_list(){
		if ($this->checkLogin('A') == ''){
			redirect(ADMIN_ENC_URL);
		}else {
		    if ($this->lang->line('admin_drivers_vehicle_list') != '') 
		    $this->data['heading']= stripslashes($this->lang->line('admin_drivers_vehicle_list')); 
		    else  $this->data['heading'] = 'Vehicle List';
			$condition = array();
			$this->data['vehicleList'] = $this->vehicle_model->get_all_details(VEHICLES,$condition);
			$this->load->view(ADMIN_ENC_URL.'/vehicle/display_vehicle_list',$this->data);
		}
	}
	/**
	* 
	* it display the add or edit vehicle page
	*
	* @param string $vehicle_id  Vehicle MongoDB\BSON\ObjectId
	* @return HTML,add or edit vehicle page
	*
	**/
	public function add_edit_vehicle_type_form(){
		if ($this->checkLogin('A') == ''){
			redirect(ADMIN_ENC_URL);
		}else {
			$vehicle_id = $this->uri->segment(4,0);
			$form_mode=FALSE;
			if ($this->lang->line('admin_drivers_vehicle_list') != '') 
		    $heading = stripslashes($this->lang->line('admin_drivers_vehicle_list')); 
		    else  $heading = 'Add New Vehicle';
			if($vehicle_id!=''){
				$condition = array('_id' => MongoID($vehicle_id));
				$this->data['vehicledetails'] = $this->vehicle_model->get_all_details(VEHICLES,$condition);
				if ($this->data['vehicledetails']->num_rows() != 1){
					redirect(ADMIN_ENC_URL.'/vehicle/display_vehicle_list');
				}
				$form_mode=TRUE;
				if ($this->lang->line('admin_menu_edit_vehicle_type') != '') 
		        $heading = stripslashes($this->lang->line('admin_menu_edit_vehicle_type')); 
		        else  $heading = 'Edit Vehicle';
			}
			$this->data['form_mode'] = $form_mode;
			$this->data['heading'] = $heading;
			$this->load->view(ADMIN_ENC_URL.'/vehicle/add_edit_vehicle_type',$this->data);
		}
	}
	/**
	* 
	* it insert or edit the vehicle
	*
	* @param string $vehicle_id  Vehicle MongoDB\BSON\ObjectId
	* @param string $vehicle_type  Vehicle Type
	* @return HTTP REDIRECT,vehicle list page
	*
	**/
	public function insertEditVehicle(){
		
		if ($this->checkLogin('A') == ''){
			redirect(ADMIN_ENC_URL);
		}else {
			$vehicle_id = $this->input->post('vehicle_id');
			$vehicle_type = $this->input->post('vehicle_type');
			if($vehicle_id==''){
				$condition=array('vehicle_type'=>$vehicle_type);
			}else{
				$condition=array('vehicle_type'=>$vehicle_type,'_id'=>array('$ne'=>MongoID($vehicle_id)));
			}
			
			$duplicate_name = $this->vehicle_model->get_all_details(VEHICLES,$condition);
			if ($duplicate_name->num_rows() > 0){
				$this->setErrorMessage('error','This type already exists','admin_vehicle_type_exist');
				redirect(ADMIN_ENC_URL.'/vehicle/add_edit_vehicle_type_form/'.$vehicle_id);
			}			

			$excludeArr = array("status","icon");
				
			$max_seating=$this->input->post('max_seating');
			if ($this->input->post('status') != ''){
				$status = 'Active';
			}else {
				$status = 'Inactive';
			}			
				
			$inputArr = array('vehicle_type' => $vehicle_type,
				'max_seating' => intval($max_seating),
				'status' => $status,
				'created' => date('Y-m-d H:i:s')
			);
				
				
			if($_FILES['icon']['name'] !=''){				
				$config['encrypt_name'] = TRUE;
				$config['overwrite'] = FALSE;
				$config['allowed_types'] = 'jpg|jpeg|gif|png|bmp';
				$config['max_size'] = 2000;
				$config['upload_path'] = './images/vehicle/';
				$this->load->library('upload', $config);
				$image_info = getimagesize($_FILES["icon"]["tmp_name"]);
				$image_width = $image_info[0];
				$image_height = $image_info[1];
				if($image_width >=70 && $image_height >=40){
					if ( $this->upload->do_upload('icon')){
					$iconDetails = $this->upload->data();
					$vehicleIcon = $iconDetails['file_name'];
					$this->imageResizeWithSpace(70, 40, $vehicleIcon, './images/vehicle/');
					}else{
						$iconDetails = $this->upload->display_errors();
						$this->setErrorMessage('error',$iconDetails);
						redirect(ADMIN_ENC_URL.'/vehicle/add_edit_vehicle_type_form/'.$vehicle_id);
					}
				}else{
						$this->setErrorMessage('error',"Image size should be more than 70x40 Pixels",'admin_vehicle_image_size');
						redirect(ADMIN_ENC_URL.'/vehicle/add_edit_vehicle_type_form/'.$vehicle_id);
					
				}
				
			
				
				$vehicle_data = array( 'icon' => $vehicleIcon);
			}else{
				$vehicle_data = array();
			}
			$dataArr = array_merge($inputArr,$vehicle_data);
			
			if($vehicle_id==''){
				$this->vehicle_model->simple_insert(VEHICLES,$dataArr);
				$this->setErrorMessage('success','Vehicle type added successfully','admin_vehicle_type_added');
			}else{
				$condition=array('_id'=>MongoID($vehicle_id));
				$this->vehicle_model->update_details(VEHICLES,$dataArr,$condition);
				$this->setErrorMessage('success','Vehicle type updated successfully','admin_vehicle_type_updated');
			}
			redirect(ADMIN_ENC_URL.'/vehicle/display_vehicle_list');
		}
	}
	/**
	* 
	* it delete the vehicle 
	*
	* @param string $vehicle_id  Vehicle MongoDB\BSON\ObjectId
	* @return HTTP REDIRECT,vehicle list page
	*
	**/
	public function delete_vehicle(){
		if ($this->checkLogin('A') == ''){
			redirect(ADMIN_ENC_URL);
		}else {
			$vehicle_id = $this->uri->segment(4,0);
			$condition = array('_id' => MongoID($vehicle_id));
			$this->vehicle_model->commonDelete(VEHICLES,$condition);
			$this->setErrorMessage('success','Vehicle deleted successfully','admin_vehicle_deleted_success');
			redirect(ADMIN_ENC_URL.'/vehicle/display_vehicle_list');
		}
	}
	/**
	* 
	* it change the vehicle status 
	*
	* @param string $vehicle_id  Vehicle MongoDB\BSON\ObjectId
	* @param string $mode  Active/Inactive
	* @return HTTP REDIRECT,vehicle list page
	*
	**/
	public function change_vehicle_status(){
		if ($this->checkLogin('A') == ''){
			redirect(ADMIN_ENC_URL);
		}else {
			$mode = $this->uri->segment(4,0);
			$vehicle_id = $this->uri->segment(5,0);
			$status = ($mode == '0')?'Inactive':'Active';
			$newdata = array('status' => $status);
			$condition = array('_id' => MongoID($vehicle_id));
			$this->vehicle_model->update_details(VEHICLES,$newdata,$condition);
			$this->setErrorMessage('success','Vehicle Status Changed Successfully','admin_vehicle_status_change');
			redirect(ADMIN_ENC_URL.'/vehicle/display_vehicle_list');
		}
	}
	/**
	* 
	* it change the vehicle status bulk
	*
	* @param string $checkbox_id  Vehicle MongoDB\BSON\ObjectId ARRAY[]
	* @param string $statusMode  Active/Inactive  ARRAY[]
	* @return HTTP REDIRECT,vehicle list page
	*
	**/
	public function change_vehicle_status_global(){
		if(count($_POST['checkbox_id']) > 0 &&  $_POST['statusMode'] != ''){
			$this->vehicle_model->activeInactiveCommon(VEHICLES,'_id');
			if (strtolower($_POST['statusMode']) == 'delete'){
				$this->setErrorMessage('success','Vehicle records deleted successfully','admin_vehicle_record_delete');
			}else {
				$this->setErrorMessage('success','Vehicle records status changed successfully','admin_vehicle_record_status_change');
			}
			redirect(ADMIN_ENC_URL.'/vehicle/display_vehicle_list');
		}
	}
	/**
	* 
	* it check the image size
	*
	* @param file $icon  Vehicle icon ARRAY[]
	* @return STRING,success or error message
	*
	**/
	public function ajax_check_icon(){
		list($w, $h) = getimagesize($_FILES["icon"]["tmp_name"]);
		if($w >=70 && $h >= 40){
			echo 'Success';
		} else {
			echo 'Error';
		}
	}
	
}

/* End of file vehicle.php */
/* Location: ./application/controllers/admin/vehicle.php */