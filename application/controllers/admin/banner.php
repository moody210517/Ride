<?php if (!defined('BASEPATH')) exit('No direct script access allowed');
/**
*
*	Banner Management for admin 
* 
*	@package	CI
*	@subpackage	Controller
*	@author	Casperon
*
**/
class Banner extends MY_Controller {

    function __construct() {
        parent::__construct();
        $this->load->helper(array('cookie', 'date', 'form'));
        $this->load->library(array('encrypt', 'form_validation'));
        $this->load->model('banner_model');
        if ($this->checkPrivileges('banner', $this->privStatus) == FALSE) {
            redirect(ADMIN_ENC_URL);
        }
    }

    /**
	* 
	* Its displays the banner list
	*
	* @return HTTP REDIRECT, banner list page
	*
	**/
    public function index() {
        if ($this->checkLogin('A') == '') {
            redirect(ADMIN_ENC_URL);
        } else {
            redirect(ADMIN_ENC_URL.'/banner/display_banner');
        }
    }
    
    /**
	* 
	* Its displays the banner list
	*
	* @return HTML, banner list page
	*
	**/
    public function display_banner() {
        if ($this->checkLogin('A') == '') {
            redirect(ADMIN_ENC_URL);
        } else {
		    if ($this->lang->line('admin_banner_banner_list') != '') 
		    $this->data['heading']= stripslashes($this->lang->line('admin_banner_banner_list')); 
		    else  $this->data['heading'] = 'Banner List';
            $condition = array();
            $this->data['bannerList'] = $this->banner_model->get_all_details(BANNER, $condition);
            $this->load->view(ADMIN_ENC_URL.'/banner/display_banner', $this->data);
        }
    }

    /**
	* 
	* Its displays the add banner page
	*
	* @return HTML, banner add page
	*
	**/
    public function add_banner_form() {
        if ($this->checkLogin('A') == '') {
            redirect(ADMIN_ENC_URL);
        } else {
		    if ($this->lang->line('admin_banner_add_new_banner') != '') 
		    $this->data['heading']= stripslashes($this->lang->line('admin_banner_add_new_banner')); 
		    else  $this->data['heading'] = 'Add New Banner';
            $this->load->view(ADMIN_ENC_URL.'/banner/add_banner', $this->data);
        }
    }

    /**
	* 
	* Add the banner information
	*
    * @param string $status  banner status checkbox value
    * @param file $banner_image  banner image
    * @param string $name  banner name
    * @param string $banner_title  banner title
	* @return HTTP REDIRECT, banner list page
	*
	**/
    
    public function insertBanner() {
        if ($this->checkLogin('A') == '') {
            redirect(ADMIN_ENC_URL);
        } else {

            $excludeArr = array("status", "banner_image");

            if ($this->input->post('status') != '') {
                $banner_status = 'Publish';
            } else {
                $banner_status = 'Unpublish';
            }

            $inputArr = array(
                'status' => $banner_status
            );

            $config['overwrite'] = FALSE;
            $config['encrypt_name'] = TRUE;
            $config['allowed_types'] = 'jpg|jpeg|gif|png|bmp';
            $config['max_size'] = 2000;
            $config['upload_path'] = './images/banner';
            $this->load->library('upload', $config);
            if ($this->upload->do_upload('banner_image')) {
                $bannerDetails = $this->upload->data();
                $ImageName = $bannerDetails['file_name'];
				
				$target_file='images/banner/'.$ImageName;
				$option=$this->getImageShape(200,112,$target_file);						
				$resizeObj = new Resizeimage($target_file);							
				$resizeObj -> resizeImage(200, 112, $option);
				$resizeObj -> saveImage('images/banner/thumbnail-'.$ImageName, 100);	
				
            } else {
                $bannerDetails = $this->upload->display_errors();
                $this->setErrorMessage('error', strip_tags($bannerDetails));
                redirect(ADMIN_ENC_URL.'/banner/add_banner_form');
            }
            $banner_data = array('image' => $ImageName);

            $dataArr = array_merge($inputArr, $banner_data);

            $this->banner_model->commonInsertUpdate(BANNER, 'insert', $excludeArr, $dataArr);
            $this->setErrorMessage('success', 'Banner added successfully','admin_banner_added_success');
            redirect(ADMIN_ENC_URL.'/banner/display_banner');
        }
    }

    /**
	* 
	* Change banner status
	*
    * @param string $mode  0 or 1 will denotes the banner status
    * @param string $banner_id  banner MongoDB\BSON\ObjectId
	* @return HTTP REDIRECT, banner list page
	*
	**/
    public function change_banner_status() {
        if ($this->checkLogin('A') == '') {
            redirect(ADMIN_ENC_URL);
        } else {
            $mode = $this->uri->segment(4, 0);
            $banner_id = $this->uri->segment(5, 0);
            $status = ($mode == '0') ? 'Unpublish' : 'Publish';
            $newdata = array('status' => $status);
            $condition = array('_id' => MongoID($banner_id));
            $this->banner_model->update_details(BANNER, $newdata, $condition);
            $this->setErrorMessage('success', 'Banner Status Changed Successfully','admin_banner_status_change');
            redirect(ADMIN_ENC_URL.'/banner/display_banner');
        }
    }

    /**
	* 
	* Change multiple banner status
	*
    * @param string $statusMode  active/inactive/delete will denotes the banner state activity
    * @param string $checkbox_id  banner id's
	* @return HTTP REDIRECT, banner list page
	*
	**/
    public function change_banner_status_global() {
        if (count($_POST['checkbox_id']) > 0 && $_POST['statusMode'] != '') {
            $this->banner_model->activeInactiveCommon(BANNER, '_id');
            if (strtolower($_POST['statusMode']) == 'delete') {
                $this->setErrorMessage('success', 'Banner records deleted successfully','admin_banner_records_delete');
            } else {
                $this->setErrorMessage('success', 'Banner records status changed successfully','admin_banner_records_status_change');
            }
            redirect(ADMIN_ENC_URL.'/banner/display_banner');
        }
    }

    /**
	* 
	* Delete banner
	*
    * @param string $banner_id  banner MongoDB\BSON\ObjectId
	* @return HTTP REDIRECT, banner list page
	*
	**/
    public function delete_banner() {
        if ($this->checkLogin('A') == '') {
            redirect(ADMIN_ENC_URL);
        } else {
            $banner_id = $this->uri->segment(4, 0);
            $condition = array('_id' => MongoID($banner_id));
            $this->banner_model->commonDelete(BANNER, $condition);
            $this->setErrorMessage('success', 'Banner deleted successfully','admin_banner_delete_success');
            redirect(ADMIN_ENC_URL.'/banner/display_banner');
        }
    }

    
    /**
	* 
	* Edit banner
	*
    * @param string $banner_id  banner MongoDB\BSON\ObjectId
	* @return HTML, banner edit page
	*
	**/
    
    public function edit_banner() {
        if ($this->checkLogin('A') == '') {
            redirect(ADMIN_ENC_URL);
        } else {
		    if ($this->lang->line('admin_banner_edit_banner') != '') 
		    $this->data['heading']= stripslashes($this->lang->line('admin_banner_edit_banner')); 
		    else  $this->data['heading'] = 'Edit Banner';
            $banner_id = $this->uri->segment(4, 0);
            $condition = array('_id' => MongoID($banner_id));
            $this->data['banner_details'] = $this->banner_model->get_all_details(BANNER, $condition);
            if ($this->data['banner_details']->num_rows() == 1) {
                $this->load->view(ADMIN_ENC_URL.'/banner/edit_banner', $this->data);
            } else {
                redirect(ADMIN_ENC_URL);
            }
        }
    }

    
    /**
	* 
	* Edit and update banner information
	*
    * @param string $banner_id  banner MongoDB\BSON\ObjectId
    * @param string $status  banner status checkbox value
    * @param file $banner_image  banner image
    * @param string $name  banner name
    * @param string $banner_title  banner title
	* @return HTTP REDIRECT, banner list page
	*
	**/
    public function editBanner() {
        if ($this->checkLogin('A') == '') {
            redirect(ADMIN_ENC_URL);
        } else {
            $bid = $this->input->post('banner_id');
            $excludeArr = array("status", "banner_image", "banner_id");

            if ($this->input->post('status') != '') {
                $banner_status = 'Publish';
            } else {
                $banner_status = 'Unpublish';
            }

            $inputArr = array(
                'status' => $banner_status
            );

            $config['overwrite'] = FALSE;
            $config['encrypt_name'] = TRUE;
            $config['allowed_types'] = 'jpg|jpeg|gif|png|bmp';
            $config['max_size'] = 2000;
            $config['upload_path'] = './images/banner';
            $this->load->library('upload', $config);
            $banner_data = array();
			
			if($_FILES["banner_image"]["size"]>0){
				if ($this->upload->do_upload('banner_image')) {
					$logoDetails = $this->upload->data();
					$ImageName = $logoDetails['file_name'];
					$target_file='images/banner/'.$ImageName;
					$option=$this->getImageShape(200,112,$target_file);						
					$resizeObj = new Resizeimage($target_file);							
					$resizeObj -> resizeImage(200, 112, $option);
					$resizeObj -> saveImage('images/banner/thumbnail-'.$ImageName, 100);
					$banner_data = array('image' => $ImageName);
				} else {
					$bannerDetails = $this->upload->display_errors();
					$this->setErrorMessage('error', strip_tags($bannerDetails));
					redirect(ADMIN_ENC_URL.'/banner/edit_banner/'.$bid);
				}
			}

            $dataArr = array_merge($inputArr, $banner_data);
            $condition = array('_id' => MongoID($bid));
            $this->banner_model->commonInsertUpdate(BANNER, 'update', $excludeArr, $dataArr, $condition);
            $this->setErrorMessage('success', 'Banner updated successfully','admin_banner_update_success');
            redirect(ADMIN_ENC_URL.'/banner/display_banner');
        }
    }

    /**
	* 
	* Check banner image size
	*
    * @argument file $banner_image  banner image
	* @return STRING, banner add/edit page
	*
	**/
    
    public function ajax_check_banner_image_size() {
    
        $path = $_FILES['banner_image']['name'];
        $ext = pathinfo($path, PATHINFO_EXTENSION); 
        
        $allowTypes = array('jpg','jpeg','gif','png');
        if(in_array(strtolower($ext),$allowTypes)){
            list($w, $h) = getimagesize($_FILES["banner_image"]["tmp_name"]);
            if ($w == 1346 && $h == 660) echo 'Success'; else echo 'Error';
        } else {
            echo 'File_Ext_Err';
        }
    }

}

/* End of file banner.php */
/* Location: ./application/controllers/admin/banner.php */