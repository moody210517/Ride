<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');
 /**
*
*	Testimonials
* 
*	@package		CI
*	@subpackage		Controller
*	@author			Casperon
*
**/

class Testimonials extends MY_Controller {

	function __construct(){
        parent::__construct();
		$this->load->helper(array('cookie','date','form'));
		$this->load->library(array('encrypt','form_validation'));		
		$this->load->model('testimonials_model');
		if ($this->checkPrivileges('testimonials',$this->privStatus) == FALSE){
			redirect(ADMIN_ENC_URL);
		}
    }
    

	/**
	*
	* To redirect the testimonials list page
	* 	
	* @Initiate HTML to Redirect testimonials list page
	*	
	**/	
   	public function index(){	
		if ($this->checkLogin('A') == ''){
			redirect(ADMIN_ENC_URL);
		}else {
			redirect(ADMIN_ENC_URL.'/testimonials/display_testimonials_list');
		}
	}
	
	/**
	* 
	* To display the testimonial list in admin panel
	*
	* @display HTML to show the testimonial list
	*
	**/	
	public function display_testimonials_list(){
		if ($this->checkLogin('A') == ''){
			redirect(ADMIN_ENC_URL);
		}else {
			if ($this->lang->line('admin_menu_display_testimonials') != '') 
		    $this->data['heading']= stripslashes($this->lang->line('admin_menu_display_testimonials')); 
		    else  $this->data['heading'] = 'Testimonials List';
			$condition = array();
			$sortArr = array('name'=>'ASC');
			$this->data['testimonialsList'] = $this->testimonials_model->get_all_details(TESTIMONIALS,$condition);
			$this->load->view(ADMIN_ENC_URL.'/testimonials/display_testimonials',$this->data);
		}
	}
	
	/**
	* 
	* To add/edit the testimonial fields 
	*
	* @param string $testimonials_id is Testimonial id and MongoDB\BSON\ObjectId
	* @display HTML to show the add edit testimonial list
	**/	
	public function add_edit_testimonials_form(){
		if ($this->checkLogin('A') == ''){
			redirect(ADMIN_ENC_URL);
		}else {
			$testimonials_id = $this->uri->segment(4,0);
			$form_mode=FALSE;
			
			if ($this->lang->line('admin_menu_add_new_testimonial') != '') 
		    $this->data['heading']= stripslashes($this->lang->line('admin_menu_add_new_testimonial')); 
		    else  $this->data['heading'] = 'Add New Testimonial';
			
			if($testimonials_id!=''){
				$condition = array('_id' => MongoID($testimonials_id));
				$this->data['testimonialsdetails'] = $this->testimonials_model->get_all_details(TESTIMONIALS,$condition);
				if ($this->data['testimonialsdetails']->num_rows() != 1){
					redirect(ADMIN_ENC_URL.'/testimonials/display_testimonials_list');
				}
				$form_mode=TRUE;
				
				if ($this->lang->line('admin_menu_edit_testimonial') != '') 
				$this->data['heading']= stripslashes($this->lang->line('admin_menu_edit_testimonial')); 
				else  $this->data['heading'] = 'Edit Testimonial';
			}
			$this->data['form_mode'] = $form_mode;
			$this->load->view(ADMIN_ENC_URL.'/testimonials/add_edit_testimonials',$this->data);
		}
	}
	
	/**
	* 
	* To show testimonial information to add/edit a testimonial
	*
	* @param string $testimonials_id is Testimonial id and MongoDB\BSON\ObjectId
	* @redirect HTML to show the list of testimonials page
	*
	**/	
	public function insertEditTestimonials(){
		if ($this->checkLogin('A') == ''){
			redirect(ADMIN_ENC_URL);
		}else {
			$testimonials_id = $this->input->post('testimonials_id');
			$name =trim($this->input->post('name'));
			
			$excludeArr = array("testimonials_id","status");
			
			if ($this->input->post('status') == 'on'){
				$testimonials_status = 'Active';
			}else{
				$testimonials_status = 'Inactive';
			}
			$testimonials_data = array('status' => $testimonials_status);
			$condition = array();
			if ($testimonials_id == ''){
				$testimonials_data['created_date'] = MongoDATE(time());
				$this->testimonials_model->commonInsertUpdate(TESTIMONIALS,'insert',$excludeArr,$testimonials_data,$condition);
				$this->setErrorMessage('success','Testimonial added successfully','admin_testimonials_added_successfully');
			} else {
				$condition = array('_id' => MongoID($testimonials_id));
				$this->testimonials_model->commonInsertUpdate(TESTIMONIALS,'update',$excludeArr,$testimonials_data,$condition);
				$this->setErrorMessage('success','Testimonials updated successfully','admin_testimonials_edited_successfully');
			}
			redirect(ADMIN_ENC_URL.'/testimonials/display_testimonials_list');
		}
	}
	/**
	* 
	* To view the specific testimonial
	*
	* @param string $testimonials_id is Template and MongoDB\BSON\ObjectId
	* @display the HTML to view the testimonial 
	*
	**/	
	public function view_testimonials(){
		if ($this->checkLogin('A') == ''){
			redirect(ADMIN_ENC_URL);
		}else {
			
			
			if ($this->lang->line('admin_view_testimonial') != '') 
		    $this->data['heading']= stripslashes($this->lang->line('admin_view_testimonial')); 
		    else  $this->data['heading'] = 'View Testimonial';
			
			
			$testimonials_id = $this->uri->segment(4,0);
			$condition = array('_id' => MongoID($testimonials_id));
			$this->data['testimonials_details'] = $testimonials_details=$this->testimonials_model->get_all_details(TESTIMONIALS,$condition);
			if ($this->data['testimonials_details']->num_rows() == 1){
				$this->load->view(ADMIN_ENC_URL.'/testimonials/view_testimonials',$this->data);
			}else {
				redirect(ADMIN_ENC_URL.'/testimonials/display_testimonials_list');
			}
		}
	}
	
	/**
	* 
	*	To change the status of particular testimonial
	
	* @param string $status Mode Active/InActive
	* @param string $testimonials_id Testimonial and MongoDB\BSON\ObjectId
	* @redirect http request to show the testimonial list
	*
	**/
	public function change_testimonials_status(){
		if ($this->checkLogin('A') == ''){
			redirect(ADMIN_ENC_URL);
		}else {
			$mode = $this->uri->segment(4,0);
			$testimonials_id = $this->uri->segment(5,0);
			$status = ($mode == '0')?'Inactive':'Active';
			$newdata = array('status' => $status);
			$condition = array('_id' => MongoID($testimonials_id));
			$this->testimonials_model->update_details(TESTIMONIALS,$newdata,$condition);
			$this->setErrorMessage('success','Testimonial Status Changed Successfully','admin_testimonial_status_changed');
			redirect(ADMIN_ENC_URL.'/testimonials/display_testimonials_list');
		}
	}
	
	/**
	* 
	* To delete the record of particular testimonial
	* 
	* @param string $testimonials_id is Testimonial id and MongoDB\BSON\ObjectId
	* @display HTML to list the particular testimonial
	*
	**/	
	public function delete_testimonials(){
		if ($this->checkLogin('A') == ''){
			redirect(ADMIN_ENC_URL);
		}else {		
			$testimonials_id = $this->uri->segment(4,0);
			$condition = array('_id' => MongoID($testimonials_id));
			$this->testimonials_model->commonDelete(TESTIMONIALS,$condition);
			$this->setErrorMessage('success','Testimonial deleted successfully','admin_testimonial_deleted');
			redirect(ADMIN_ENC_URL.'/testimonials/display_testimonials_list');
		}
	}
	
		
	/**
	* To change the status of the testimonial globally
	*
	* @redirect http request to show the testimonial list page
	*
	**/
	public function change_testimonials_status_global(){
		if(count($_POST['checkbox_id']) > 0 &&  $_POST['statusMode'] != ''){
			
			$this->user_model->activeInactiveCommon(TESTIMONIALS,'_id');
			if (strtolower($_POST['statusMode']) == 'delete'){
				$this->setErrorMessage('success','Testimonial records deleted successfully','admin_testimonials_deleted');
			}else {
				$this->setErrorMessage('success','Testimonial records status changed successfully','admin_testimonials_status_changed');
			}
			redirect(ADMIN_ENC_URL.'/testimonials/display_testimonials_list');
		}
	}
}

/* End of file testimonials.php */
/* Location: ./application/controllers/admin/testimonials.php */