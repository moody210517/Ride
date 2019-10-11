<?php if (!defined('BASEPATH')) exit('No direct script access allowed');
/**
*
*	Category Management for admin 
* 
*	@package	CI
*	@subpackage	Controller
*	@author	Casperon
*
**/
class Category extends MY_Controller {
	

	function __construct() {
        parent::__construct();
        $this->load->helper(array('cookie', 'date', 'form'));
        $this->load->library(array('encrypt', 'form_validation'));
        $this->load->model(array('driver_model'));

        if ($this->checkPrivileges('category', $this->privStatus) == FALSE) {
            redirect(ADMIN_ENC_URL);
        }

        $c_fun = $this->uri->segment(3);
        $restricted_function = array( 'delete_category', 'change_category_status_global','insertEditCategory','insertEditTypes');
        if (in_array($c_fun, $restricted_function) && $this->data['isDemo'] === TRUE) {
            $this->setErrorMessage('error', 'You are in demo version. you can\'t do this action.','admin_template_demo_version');
            redirect($_SERVER['HTTP_REFERER']);
            die;
        }
    }
	
    /**
	* 
	* Displays the category list
	*
	* @return HTML, category list page
	*
	**/
    public function display_drivers_category() {
        if ($this->checkLogin('A') == '') {
            redirect(ADMIN_ENC_URL);
        } else {
		   			
			 if ($this->lang->line('admin_menu_car_types') != '') 
			 $this->data['heading']=stripslashes($this->lang->line('admin_menu_car_types'));
			 else  $this->data['heading'] ='Car Types';
			 
            $condition = array();
            $this->data['categoryList'] = $this->driver_model->get_all_details(CATEGORY, $condition);
            $this->load->view(ADMIN_ENC_URL.'/category/display_drivers_category', $this->data);
        }
    }


	
    /**
	* 
	* Displays the add/edit category page
	*
    * @param string $category_id  category MongoDB\BSON\ObjectId
	* @return HTML, category add/edit page
	*
	**/
    public function add_edit_category() {
        if ($this->checkLogin('A') == '') {
            redirect(ADMIN_ENC_URL);
        } else {
            $category_id = $this->uri->segment(4, 0);
            $form_mode = FALSE;
			
           if ($this->lang->line('admin_add_new_car_type') != '') $this->data['heading'] =  stripslashes($this->lang->line('admin_add_new_car_type')); else $this->data['heading'] =  'Add new car type';
            
            if ($category_id != '') {
                $condition = array('_id' => MongoID($category_id));
                $this->data['categorydetails'] = $this->driver_model->get_all_details(CATEGORY, $condition);
                if ($this->data['categorydetails']->num_rows() != 1) {
                    redirect(ADMIN_ENC_URL.'/category/display_drivers_category');
                }
                $form_mode = TRUE;
				if ($this->lang->line('driver_edit_category') != '') 
				$heading= stripslashes($this->lang->line('driver_edit_category')); else  $heading = 'Edit Category'; 
            }
            $this->data['form_mode'] = $form_mode;
            
            $this->load->view(ADMIN_ENC_URL.'/category/add_edit_category', $this->data);
        }
    }

    /**
	* 
	* Insert/update category informations
	*
    * @param string $category_id  category MongoDB\BSON\ObjectId
    * @param string $name  category name
    * @param string $status  category status
    * @param string $isdefault  category isdefault status on/off
    * @argument file $image  category image
    * @argument file $icon_normal  category icon normal image
    * @argument file $icon_active  category active icon image
    * @argument file $icon_car_image  category map car icon image
	* @return HTTP REDIRECT, category list page
	*
	**/
    public function insertEditCategory() {
        if ($this->checkLogin('A') == '') {
            redirect(ADMIN_ENC_URL);
        } else {
            $category_id = $this->input->post('category_id');
            $name = $this->input->post('name');

            if ($category_id == '') {
                $condition = array('name' => $name);
                $duplicate_name = $this->driver_model->get_selected_fields(CATEGORY, $condition, array('name'));
                if ($duplicate_name->num_rows() > 0)
                    $isDuplicate = TRUE;
            }else {
                $condition = array('name' => $name);
                $duplicate_name = $this->driver_model->get_selected_fields(CATEGORY, $condition, array('name'));
                if ($duplicate_name->num_rows() > 1)
                    $isDuplicate = TRUE;
            }

            if ($isDuplicate) {
                $this->setErrorMessage('error', 'Category already exists','admin_driver_category_already_exist');
                redirect(ADMIN_ENC_URL.'/category/add_edit_category/' . $category_id);
            }

            $excludeArr = array("status", "image");

            if ($this->input->post('status') != '') {
                $status = 'Active';
            } else {
                $status = 'Inactive';
            }
            if ($this->input->post('isdefault') != '' || $this->input->post('isdefault') == 'on') {
                $isdefault = 'Yes';
                $this->driver_model->update_details(CATEGORY, array('isdefault' => 'No'), array());
            } else {
                $isdefault = 'No';
            }

            $inputArr = array('name' => $name,
                'status' => $status,
                'isdefault' => $isdefault,
                'created' => date('Y-m-d H:i:s')
            );

            if ($_FILES['image']['name'] != '') {
                $config['encrypt_name'] = TRUE;
                $config['overwrite'] = FALSE;
                $config['allowed_types'] = 'jpg|jpeg|gif|png|bmp';
                $config['max_size'] = 2000;
                $config['upload_path'] = './images/category/';
                $this->load->library('upload', $config);
                $this->load->initialize($config);
                if ($this->upload->do_upload('image')) {
                    $imageDetails = $this->upload->data();
                    $category_image = $imageDetails['file_name'];
                } else {
                    $imageDetails = $this->upload->display_errors();
                    $this->setErrorMessage('error', $imageDetails);
                    redirect(ADMIN_ENC_URL.'/category/add_edit_category/' . $category_id);
                }
                $vehicle_data = array('image' => $category_image);
            } else {
                $vehicle_data = array();
            }
            if ($_FILES['icon_normal']['name'] != '') {
                $config1['encrypt_name'] = TRUE;
                $config1['overwrite'] = FALSE;
                $config1['allowed_types'] = 'jpg|jpeg|gif|png|bmp';
                $config1['max_size'] = 2000;
                $config1['upload_path'] = './images/icons/';
                $this->load->library('upload', $config1);
				$this->load->initialize($config1);
				$image_info = getimagesize($_FILES["icon_normal"]["tmp_name"]);
				$image_width = $image_info[0];
				$image_height = $image_info[1];
				if($image_width == 150 && $image_height == 150){
					 if ($this->upload->do_upload('icon_normal')) {
                    $imageDetails = $this->upload->data();
                    $icon_normal = $imageDetails['file_name'];
					} else {
						$imageDetails = $this->upload->display_errors();
						$this->setErrorMessage('error', $imageDetails);
						redirect(ADMIN_ENC_URL.'/category/add_edit_category/' . $category_id);
					}
				}else{
					$this->setErrorMessage('error',"Image size should be 150 X 150 Pixels",'admin_driver_image_size');
					redirect(ADMIN_ENC_URL.'/category/add_edit_category/' . $category_id);
				}
               
                $vehicle_data['icon_normal'] = $icon_normal;
            }
            if ($_FILES['icon_active']['name'] != '') {
                $config2['encrypt_name'] = TRUE;
                $config2['overwrite'] = FALSE;
                $config2['allowed_types'] = 'jpg|jpeg|gif|png|bmp';
                $config2['max_size'] = 2000;
                $config2['upload_path'] = './images/icons/';
                $this->load->library('upload', $config2);
				$this->load->initialize($config2);
				$image_info = getimagesize($_FILES["icon_active"]["tmp_name"]);
				$image_width = $image_info[0];
				$image_height = $image_info[1];
				if($image_width == 150 && $image_height == 150){
					 if ($this->upload->do_upload('icon_active')) {
                    $imageDetails = $this->upload->data();
                    $icon_active = $imageDetails['file_name'];
					} else {
						$imageDetails = $this->upload->display_errors();
						$this->setErrorMessage('error', $imageDetails);
						redirect(ADMIN_ENC_URL.'/category/add_edit_category/' . $category_id);
					}
				}else{
					$this->setErrorMessage('error',"Image size should be 150 X 150 Pixels",'admin_driver_image_size');
					redirect(ADMIN_ENC_URL.'/category/add_edit_category/' . $category_id);
				}
               
                $vehicle_data['icon_active'] = $icon_active;
            }
			if ($_FILES['icon_car_image']['name'] != '') {
                $config3['encrypt_name'] = TRUE;
                $config3['overwrite'] = FALSE;
                $config3['allowed_types'] = 'jpg|jpeg|gif|png|bmp';
                $config3['max_size'] = 2000;
                $config3['upload_path'] = './images/icons/';
                $this->load->library('upload', $config3);
				$this->load->initialize($config3);
				$image_info = getimagesize($_FILES["icon_car_image"]["tmp_name"]);
				$image_width = $image_info[0];
				$image_height = $image_info[1];
				if($image_width == 70 && $image_height == 70){
					 if ($this->upload->do_upload('icon_car_image')) {
                    $imageDetails = $this->upload->data();
                    $icon_car_image = $imageDetails['file_name'];
					} else {
						$imageDetails = $this->upload->display_errors();
						$this->setErrorMessage('error', $imageDetails);
						redirect(ADMIN_ENC_URL.'/category/add_edit_category/' . $category_id);
					}
				}else{
					$this->setErrorMessage('error',"Image size should be 70 X 70 Pixels",'admin_driver_image_size_pixel');
					redirect(ADMIN_ENC_URL.'/category/add_edit_category/' . $category_id);
				}
               
                $vehicle_data['icon_car_image'] = $icon_car_image;
            }

            $dataArr = array_merge($inputArr, $vehicle_data);

            if ($category_id == '') {
                $this->driver_model->simple_insert(CATEGORY, $dataArr);
                $this->setErrorMessage('success', 'Category added successfully','admin_driver_category_added_successfully');
            } else {
                $condition = array('_id' => MongoID($category_id));
                $this->driver_model->update_details(CATEGORY, $dataArr, $condition);
                $this->setErrorMessage('success', 'Category updated successfully','admin_driver_category_updated_successfully');
            }
            redirect(ADMIN_ENC_URL.'/category/display_drivers_category');
        }
    }

    /**
	* 
	* Change category status
	*
    * @param string $category_id  category Mongo Id
    * @param string $mode  category status mode 0/1
	* @return HTTP REDIRECT, category list page
	*
	**/
    public function change_category_status() {
        if ($this->checkLogin('A') == '') {
            redirect(ADMIN_ENC_URL);
        } else {
            $mode = $this->uri->segment(4, 0);
            $category_id = $this->uri->segment(5, 0);
            $status = ($mode == '0') ? 'Inactive' : 'Active';
            $newdata = array('status' => $status);
            $condition = array('_id' => MongoID($category_id));
            $this->driver_model->update_details(CATEGORY, $newdata, $condition);
            $this->setErrorMessage('success', 'Category Status Changed Successfully','admin_driver_category_status_change');
            redirect(ADMIN_ENC_URL.'/category/display_drivers_category');
        }
    }

    /**
	* 
	* Delete category
	*
    * @param string $category_id  category MongoDB\BSON\ObjectId
	* @return HTTP REDIRECT, category list page
	*
	**/
    public function delete_category() {
        if ($this->checkLogin('A') == '') {
            redirect(ADMIN_ENC_URL);
        } else {
            $category_id = $this->uri->segment(4, 0);
            $condition = array('_id' => MongoID($category_id));
            $this->driver_model->commonDelete(CATEGORY, $condition);
			$this->user_model->simple_pull(LOCATIONS, array(), 'avail_category',$category_id);
			$this->setErrorMessage('success', 'Category deleted successfully','admin_driver_category_deleted_success');
            redirect(ADMIN_ENC_URL.'/category/display_drivers_category');
        }
    }

    /**
	* 
	* Change multiple category status
	*
    * @param string $statusMode  active/inactive/delete will denotes the category state activity
    * @param string $checkbox_id  category id's
	* @return HTTP REDIRECT, category list page
	*
	**/
    public function change_category_status_global() {
        if (count($_POST['checkbox_id']) > 0 && $_POST['statusMode'] != '') {
            $this->driver_model->activeInactiveCommon(CATEGORY, '_id');
            if (strtolower($_POST['statusMode']) == 'delete') {
                $this->setErrorMessage('success', 'Category records deleted successfully','admin_driver_category_records_deleted_success');
            } else {
                $this->setErrorMessage('success', 'Category records status changed successfully','admin_driver_category_records_status_change');
            }
            redirect(ADMIN_ENC_URL.'/category/display_drivers_category');
        }
    }

    /**
	* 
	* Displays the add/edit vehicle type page
	*
    * @param string $category_id  category MongoDB\BSON\ObjectId
	* @return HTML, vehicle type page
	*
	**/
    public function add_edit_category_types() {
        if ($this->checkLogin('A') == '') {
            redirect(ADMIN_ENC_URL);
        } else {
            $category_id = $this->uri->segment(4, 0);
            if ($category_id != '') {
                $condition = array('_id' => MongoID($category_id));
                $this->data['categorydetails'] = $this->driver_model->get_all_details(CATEGORY, $condition);
                $this->data['vehicle_types'] = $this->driver_model->get_all_details(VEHICLES, array('status' => 'Active'));
                if ($this->data['categorydetails']->num_rows() != 1) {
                    redirect(ADMIN_ENC_URL.'/category/display_drivers_category');
                }
            }
				if ($this->lang->line('admin_error_msg_vehicle_type_category') != '') 
		          $this->data['heading']= stripslashes($this->lang->line('admin_error_msg_vehicle_type_category')); 
		          else  $this->data['heading'] = 'Vehicle types under category';
            $this->load->view(ADMIN_ENC_URL.'/category/add_edit_category_types', $this->data);
        }
    }

    /**
	* 
	* Insert/update vehicle informations
	*
    * @param string $category_id  category MongoDB\BSON\ObjectId
    * @param string $vehicle_type  vehicle type
    * @param string $max_seating  vehicle maximum seating capacity
    * @argument file $icon  vehicle icon image
	* @return HTTP REDIRECT, vehicle list page
	*
	**/
    public function insertEditTypes() {
        if ($this->checkLogin('A') == '') {
            redirect(ADMIN_ENC_URL);
        } else {
            $category_id = $this->input->post('category_id');
            if ($category_id != '' && $this->input->post('vehicle_type') != NULL) {
                $vehicle_type = $this->input->post('vehicle_type');
                $dataArr = array('vehicle_type' => $vehicle_type);
                $condition = array('_id' => MongoID($category_id));
                $this->driver_model->update_details(CATEGORY, $dataArr, $condition);
                $this->setErrorMessage('success', 'Category\'s types updated successfully','admin_driver_category_update_sccess');
                redirect(ADMIN_ENC_URL.'/category/display_drivers_category');
            } else {
                $this->setErrorMessage('error', 'Category\'s types cannot be updated','admin_driver_category_not_update_sccess');
                redirect(ADMIN_ENC_URL.'/category/add_edit_category_types/' . $category_id);
            }
        }
    }
	
	/**
	* 
	* Edit category name language
	*
    * @param string $category_id  category MongoDB\BSON\ObjectId
	* @return HTML, category name language edit page
	*
	**/
	public function edit_language_category(){
		if ($this->checkLogin('A') == '') {
            redirect(ADMIN_ENC_URL);
        } else {
            $category_id = $this->uri->segment(4, 0);
            if ($category_id != '') {
                $condition = array('_id' => MongoID($category_id));
                $this->data['categorydetails'] = $categorydetails = $this->driver_model->get_all_details(CATEGORY, $condition);
                $this->data['languagesList'] = $this->driver_model->get_all_details(LANGUAGES, array('status' => 'Active'));
                if ($this->data['categorydetails']->num_rows() != 1) {
                    redirect(ADMIN_ENC_URL.'/category/display_drivers_category');
                }
            }
			
			if ($this->lang->line('edit_category_language') != '') 
			$heading = stripslashes($this->lang->line('edit_category_language')); 
			else  $heading = 'Edit category language';
			 
			 $this->data['heading'] = $heading;
            $this->load->view(ADMIN_ENC_URL.'/category/edit_category_language_form', $this->data);
        }
	}
	
    /**
	* 
	* Update category name language content
	*
    * @param string $category_id  category MongoDB\BSON\ObjectId
    * @argument array $name_languages[language_code]  category name languages
	* @return HTTP REDIRECT, category name language edit page
	*
	**/
	public function update_language_content(){
		if ($this->checkLogin('A') == '') {
            redirect(ADMIN_ENC_URL);
        }  
		$language_content = $this->input->post('name_languages');  
		$category_id = $this->input->post('category_id');
		$updCond = array('_id' => MongoID($category_id ));
		$dataArr = array('name_languages' => $language_content);
		$this->driver_model->update_details(CATEGORY,$dataArr ,$updCond);
		$this->setErrorMessage('success', 'Language content updated successfully','language_content_updated_successfully');
        redirect(ADMIN_ENC_URL.'/category/display_drivers_category');
	}
	
	/**
	* 
	* Displays category statistics page
	*
	* @return HTML, category statistics page
	*
	**/
    public function category_statistics() {
        if ($this->checkLogin('A') == '') {
            redirect(ADMIN_ENC_URL);
        } else {
		   			
			 if ($this->lang->line('admin_menu_car_types') != '') 
			 $this->data['heading']=stripslashes($this->lang->line('admin_menu_car_types'));
			 else  $this->data['heading'] ='Car Types';
			 
            $condition = array();
            $this->data['categoryList'] = $categoryList = $this->driver_model->get_all_details(CATEGORY, $condition);
			$condition = array('status' => 'Active');
			$this->data['driversList'] = $driversList = $this->driver_model->get_selected_fields(DRIVERS,$condition,array('category'));
			$condition = array('ride_status' => 'Completed');
			$this->data['ridesList'] = $ridesList = $this->driver_model->get_selected_fields(RIDES,$condition,array('booking_information.service_id'));
			
			
			$categoryDriversList = array();
			$categoryRidesList = array();
			$activeCats = 0;
			foreach($categoryList->result() as $cats){
				if($cats->status == 'Active') $activeCats++;
				$cat_id = (string)$cats->_id;
				$cat_name = $cats->name;
				if(isset($cats->name_languages[$this->data['langCode']]) && $cats->name_languages[$this->data['langCode']] != '') $cat_name = $cats->name_languages[$this->data['langCode']];
				$categoryDriversList[$cat_id]['cat_name'] = $cat_name;
				$categoryRidesList[$cat_id]['cat_name'] = $cat_name;
				$categoryDriversList[$cat_id]['drivers_count'] = 0;
				$categoryRidesList[$cat_id]['rides_count'] = 0;
			}
			$driverOthers = 0;
			foreach($driversList->result() as $drivers){
				$category_id = (string)$drivers->category;
				if(isset($categoryDriversList[$category_id])){
					$drivers_count = $categoryDriversList[$category_id]['drivers_count'];
					$categoryDriversList[$category_id]['drivers_count'] = $drivers_count + 1;
				} else {
					$driverOthers++;
				}
			}
			 if ($this->lang->line('admin_cat_stats_others') != '') $admin_cat_stats_others = stripslashes($this->lang->line('admin_cat_stats_others')); else $admin_cat_stats_others = 'Other';
			if($driverOthers > 0){ 
				$categoryDriversList['others']['cat_name'] = $admin_cat_stats_others;
				$categoryDriversList['others']['drivers_count'] = $driverOthers;
			}
			
			$ridesOthers = 0;
			foreach($ridesList->result() as $rides){
				$category_id = (string)$rides->booking_information['service_id'];
				if(isset($categoryRidesList[$category_id])){
					$rides_count = $categoryRidesList[$category_id]['rides_count'];
					$categoryRidesList[$category_id]['rides_count'] = $rides_count + 1;
				} else {
					$ridesOthers++;
				}
			}
			if($ridesOthers > 0){
				$categoryRidesList['others']['cat_name'] = $admin_cat_stats_others;
				$categoryRidesList['others']['rides_count'] = $ridesOthers;
			}
			
			
			$categoryDriversList = array_values($categoryDriversList);
			$categoryRidesList = array_values($categoryRidesList); 
			$this->data['categoryDriversList'] = $categoryDriversList; 
			$this->data['categoryRidesList'] = $categoryRidesList; 
			$this->data['activeCats'] = $activeCats; 
            $this->load->view(ADMIN_ENC_URL.'/category/category_statistics', $this->data);
        }
    }
	

}

/* End of file category.php */
/* Location: ./application/controllers/admin/category.php */	
	