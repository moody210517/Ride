<?php if (!defined('BASEPATH')) exit('No direct script access allowed');

/**
*
*	Brand
* 
*	@package		CI
*	@subpackage		Controller
*	@author			Casperon
*
**/

class Brand extends MY_Controller {

    function __construct() {
        parent::__construct();
        $this->load->helper(array('cookie', 'date', 'form'));
        $this->load->library(array('encrypt', 'form_validation'));
        $this->load->model('brand_model');
		$this->load->model(array('operators_model'));
        if ($this->checkLogin('C') != '') {
			$this->data['company_id'] = MongoID($this->session->userdata(APP_NAME.'_session_company_id'));
			$company_id = $this->checkLogin('C');
			$chkCompany = $this->app_model->get_selected_fields(COMPANY,array('_id' => MongoID($company_id)),array('status'));
			$chkstatus = TRUE;
			$errMsg = '';
			if($chkCompany->num_rows() == 1){
				if($chkCompany->row()->status == 'Inactive'){
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
				$collection = COMPANY;
				
				$condition = array('_id' => MongoID($this->checkLogin('C')));
				$this->app_model->update_details($collection, $newdata, $condition);
				$companydata = array(
							APP_NAME.'_session_company_id' => '',
							APP_NAME.'_session_company_name' => '',
							APP_NAME.'_session_company_email' => ''
							
						   
						);
						
				$this->session->unset_userdata($companydata);
				$this->setErrorMessage('error', $errMsg);
				redirect(COMPANY_NAME);
			}
			
		}
    }


    public function index() {
        if ($this->checkLogin('C') == '') {
            redirect(COMPANY_NAME);
        } else {
            redirect(COMPANY_NAME.'/brand/display_brand_list');
        }
    }

    /**
		* 
		* To list the email template in admin panel
		*
		* @return HTML to show the list of email templates
		*
		**/	
    public function display_brand_list() {
        if ($this->checkLogin('C') == '') {
            redirect(COMPANY_NAME);
        } else {
		    if ($this->lang->line('admin_make_and_model_marker_list_brand_list') != '') 
		    $this->data['heading']= stripslashes($this->lang->line('admin_make_and_model_marker_list_brand_list')); 
		    else  $this->data['heading'] = 'Brand List';
            $condition = array();
            $this->data['brandList'] = $this->brand_model->get_all_details(BRAND, $condition);
            $this->load->view(COMPANY_NAME.'/brand/display_brand_list', $this->data);
        }
    }

	  /**
		* 
		* To load the add new brand form
		*
		* @return the HTML to show the add brand form page
		*
		**/	
    public function add_brand_form() {
        if ($this->checkLogin('C') == '') {
            redirect(COMPANY_NAME);
        } else {
		    if ($this->lang->line('admin_make_and_model_add_new_maker_add_new_brand') != '') 
		    $this->data['heading']= stripslashes($this->lang->line('admin_make_and_model_add_new_maker_add_new_brand')); 
		    else  $this->data['heading'] = 'Add New Brand';
            $this->load->view(COMPANY_NAME.'/brand/add_brand', $this->data);
        }
    }

		/**
		* 
		* To insert brand informations into database
		* 
		* @param string $brand_id is brand and MongoDB\BSON\ObjectId
		* @param string $brand_name is brand name and MongoDB\BSON\ObjectId
		* @redirect HTTP request to display brands list page
		*
		**/	
    public function insertBrand() {
        if ($this->checkLogin('C') == '') {
            redirect(COMPANY_NAME);
        } else {
            $brand_id = $this->input->post('brand_id');
            $brand_name = $this->input->post('brand_name');
            $condition = array('brand_name' => $brand_name);

            $duplicate_name = $this->brand_model->get_all_details(BRAND, $condition);
            if ($duplicate_name->num_rows() > 0) {
                $this->setErrorMessage('error', 'Brand name already exists','admin_driver_brand_name_exist');
                redirect(COMPANY_NAME.'/brand/add_brand_form/' . $brand_id);
            }

            $excludeArr = array("status", "brand_logo");

            if ($this->input->post('status') != '') {
                $brand_status = 'Active';
            } else {
                $brand_status = 'Inactive';
            }

            $seourlBase = $seourl = url_title($brand_name, '-', TRUE);
            $seourl_check = '0';
            $duplicate_url = $this->brand_model->get_all_details(BRAND, array('seourl' => $seourl));
            if ($duplicate_url->num_rows() > 0) {
                $seourl = $seourlBase . '-' . $duplicate_url->num_rows();
            } else {
                $seourl_check = '1';
            }
            $urlCount = $duplicate_url->num_rows();
            while ($seourl_check == '0') {
                $urlCount++;
                $duplicate_url = $this->brand_model->get_all_details(BRAND, array('seourl' => $seourl));
                if ($duplicate_url->num_rows() > 0) {
                    $seourl = $seourlBase . '-' . $urlCount;
                } else {
                    $seourl_check = '1';
                }
            }

            $inputArr = array(
                'brand_name' => $brand_name,
                'seourl' => $seourl,
                'status' => $brand_status,
                'created' => date('Y-m-d H:i:s')
            );

            if ($_FILES['brand_logo']['name'] != '') {
                $config['encrypt_name'] = TRUE;
                $config['overwrite'] = FALSE;
                $config['allowed_types'] = 'jpg|jpeg|gif|png|bmp';
                $config['max_size'] = 2000;
                $config['upload_path'] = './images/brand/';
                $this->load->library('upload', $config);
                if ($this->upload->do_upload('brand_logo')) {
                    $logoDetails = $this->upload->data();
                    $ImageName = $logoDetails['file_name'];
                    @copy('./images/brand/' . $ImageName, './images/brand/thumbnail/' . $ImageName);
                    $this->imageResizeWithSpace(75, 42, $ImageName, './images/brand/thumbnail/');
                } else {
                    $logoDetails = $this->upload->display_errors();
                    $this->setErrorMessage('error', $logoDetails);
                    redirect(COMPANY_NAME.'/brand/add_brand_form/' . $brand_id);
                }
                $brand_data = array('brand_logo' => $ImageName);
            } else {
                $brand_data = array();
            }

            $dataArr = array_merge($inputArr, $brand_data);

            $this->brand_model->simple_insert(BRAND, $dataArr);
            $this->setErrorMessage('success', 'Brand added successfully','admin_driver_brand_added_success');
            redirect(COMPANY_NAME.'/brand/display_brand_list');
        }
    }

    /**
		* 
		* To load brand edit form
		* 
		* @param string $brand_id is brand and MongoDB\BSON\ObjectId
		* @redirect HTTP request to edit brand page
		*
		**/	
    public function edit_brand_form() {
        if ($this->checkLogin('C') == '') {
            redirect(COMPANY_NAME);
        } else {
		    if ($this->lang->line('admin_make_and_model_model_edit_brand') != '') 
		    $this->data['heading']= stripslashes($this->lang->line('admin_make_and_model_model_edit_brand')); 
		    else  $this->data['heading'] = 'Edit Brand';
            $brand_id = $this->uri->segment(4, 0);
            $condition = array('_id' => MongoID($brand_id));
            $this->data['brand_details'] = $this->brand_model->get_all_details(BRAND, $condition);
            if ($this->data['brand_details']->num_rows() == 1) {
                $this->load->view(COMPANY_NAME.'/brand/edit_brand', $this->data);
            } else {
                redirect(COMPANY_NAME);
            }
        }
    }

		/**
		* 
		* To edit specific brand information
		* 
		* @param string $brand_id is brand and MongoDB\BSON\ObjectId
		* @param string $brand_name is brand name and MongoDB\BSON\ObjectId
		* @redirect HTTP request to display brands list page
		*
		**/	
    public function EditBrand() {
        if ($this->checkLogin('C') == '') {
            redirect(COMPANY_NAME);
        } else {
            $brand_id = $this->input->post('brand_id');
            $brand_name = $this->input->post('brand_name');

            $condition = array('brand_name' => $brand_name, 'id !=' => $brand_id);
            $duplicate_name = $this->brand_model->get_all_details(BRAND, $condition);
            if ($duplicate_name->num_rows() > 0) {
                $this->setErrorMessage('error', 'Brand name already exists','admin_driver_brand_name_already');
                redirect(COMPANY_NAME.'/brand/edit_brand_form/' . $brand_id);
            }

            $condition = array('_id' => MongoID($brand_id));
            $excludeArr = array("status");
            if ($this->input->post('status') != '') {
                $brand_status = 'Active';
            } else {
                $brand_status = 'Inactive';
            }

            $seourlBase = $seourl = url_title($brand_name, '-', TRUE);
            $seourl_check = '0';
            $duplicate_url = $this->brand_model->get_all_details(BRAND, array('seourl' => $seourl));
            if ($duplicate_url->num_rows() > 0) {
                $seourl = $seourlBase . '-' . $duplicate_url->num_rows();
            } else {
                $seourl_check = '1';
            }
            $urlCount = $duplicate_url->num_rows();
            while ($seourl_check == '0') {
                $urlCount++;
                $duplicate_url = $this->brand_model->get_all_details(BRAND, array('seourl' => $seourl));
                if ($duplicate_url->num_rows() > 0) {
                    $seourl = $seourlBase . '-' . $urlCount;
                } else {
                    $seourl_check = '1';
                }
            }

            $inputArr = array(
                'brand_name' => $brand_name,
                'seourl' => $seourl,
                'status' => $brand_status
            );

            if ($_FILES['brand_logo']['name'] != '') {
                $config['encrypt_name'] = TRUE;
                $config['overwrite'] = FALSE;
                $config['allowed_types'] = 'jpg|jpeg|gif|png|bmp';
                $config['max_size'] = 2000;
                $config['upload_path'] = './images/brand/';
                $this->load->library('upload', $config);
                if ($this->upload->do_upload('brand_logo')) {
                    $logoDetails = $this->upload->data();
                    $ImageName = $logoDetails['file_name'];
                    @copy('./images/brand/' . $ImageName, './images/brand/thumbnail/' . $ImageName);
                    $this->imageResizeWithSpace(75, 42, $ImageName, './images/brand/thumbnail/');
                } else {
                    $logoDetails = $this->upload->display_errors();
                    $this->setErrorMessage('error', $logoDetails);
                    redirect(COMPANY_NAME.'/brand/add_brand_form/' . $brand_id);
                }
                $brand_data = array('brand_logo' => $ImageName);
            } else {
                $brand_data = array();
            }


            $dataArr = array_merge($inputArr, $brand_data);

            $this->brand_model->update_details(BRAND, $dataArr, $condition);			
			
            $modelArr = array('brand' => (string)$brand_id,'brand_name' => (string)$brand_name);
			$model_condition = array('brand' => (string)$brand_id);
			$this->brand_model->update_details(MODELS, $modelArr, $model_condition);
			
            $this->setErrorMessage('success', 'Brand Updated successfully','admin_brand_update_success');
            redirect(COMPANY_NAME.'/brand/display_brand_list');
        }
    }

		/**
		* 
		* To delete specific brand information from database
		* 
		* @param string $brand_id is brand and MongoDB\BSON\ObjectId
		* @redirect HTTP request to display brands list page
		*
		**/	
    public function delete_brand() {
        if ($this->checkLogin('C') == '') {
            redirect(COMPANY_NAME);
        } else {
            $brand_id = $this->uri->segment(4, 0);
            $condition = array('_id' => MongoID($brand_id));
            $this->brand_model->commonDelete(BRAND, $condition);
            $this->setErrorMessage('success', 'Brand deleted successfully','admin_brand_deleted_success');
            redirect(COMPANY_NAME.'/brand/display_brand_list');
        }
    }

    /**
    *
    * This function change the status of the brands
    *
    * */
    public function change_brand_status() {
        if ($this->checkLogin('C') == '') {
            redirect(COMPANY_NAME);
        } else {
            $mode = $this->uri->segment(4, 0);
            $brand_id = $this->uri->segment(5, 0);
            $status = ($mode == '0') ? 'Inactive' : 'Active';
            $newdata = array('status' => $status);
            $condition = array('_id' => MongoID($brand_id));
            $this->brand_model->update_details(BRAND, $newdata, $condition);
            $this->setErrorMessage('success', 'Brand Status Changed Successfully','admin_brand_status_change');
            redirect(COMPANY_NAME.'/brand/display_brand_list');
        }
    }

		/**
		* 
		* To change the brand status
		* @redirect HTTP request to display brands list page
		*
		**/	
    public function change_brand_status_global() {
        if (count($_POST['checkbox_id']) > 0 && $_POST['statusMode'] != '') {
            $this->brand_model->activeInactiveCommon(BRAND, '_id');
            if (strtolower($_POST['statusMode']) == 'delete') {
                $this->setErrorMessage('success', 'Brand records deleted successfully','admin_brand_records_deleted');
            } else {
                $this->setErrorMessage('success', 'Brand records status changed successfully','admin_brand_records_staus_change');
            }
            redirect(COMPANY_NAME.'/brand/display_brand_list');
        }
    }

    /**
    *
    * To check the logo propotion
    *
    * */
    public function ajax_check_brand_logo() {

        $tmp_name = $_FILES['brand_logo']['tmp_name'];
        $image_info = getimagesize($tmp_name);
        $image_width = $image_info[0];
        $image_height = $image_info[1];
        if ($image_width >= 75 && $image_height >= 42) {
            echo 'Success';
        } else {
            echo 'Error';
        }
    }

		/**
		* 
		* To display specific brand information
		* @redirect HTTP request to display brands list page
		*
		**/	
    public function display_model_list() {
        if ($this->checkLogin('C') == '') {
            redirect(COMPANY_NAME);
        } else {
		    if ($this->lang->line('admin_menu_model_list') != '') 
		    $this->data['heading']= stripslashes($this->lang->line('admin_menu_model_list')); 
		    else  $this->data['heading'] = 'Model List';
            $condition = array();
            $this->data['modelList'] = $modelList = $this->brand_model->get_all_details(MODELS, $condition);
            $avail_vehicles = array();
            if ($modelList->num_rows() > 0) {
                $vehicleTypes = $this->brand_model->get_all_vehicles(VEHICLES, array());
                if ($vehicleTypes->num_rows() > 0) {
                    foreach ($vehicleTypes->result() as $vehicle) {
                        $avail_vehicles[(string) $vehicle->_id] = $vehicle->vehicle_type;
                    }
                }
            }
            $this->data['availableVehicles'] = $avail_vehicles;
            $this->load->view(COMPANY_NAME.'/brand/display_model_list', $this->data);
        }
    }

		
		/**
		* 
		* To add/edit the specific model
		*
		* @param string $model_id is model id and MongoDB\BSON\ObjectId
		* @display HTML to show the add edit model list page
		**/	
    public function add_edit_model() {
        if ($this->checkLogin('C') == '') {
            redirect(COMPANY_NAME);
        } else {
            $model_id = $this->uri->segment(4, 0);

            $this->data['brandList'] = $this->brand_model->get_all_details(BRAND, array(), array('brand_name' => 'ASC'));
            $this->data['typeList'] = $this->brand_model->get_all_details(VEHICLES, array(), array('vehicle_type' => 'ASC'));

            $form_mode = FALSE;
			if ($this->lang->line('admin_menu_add_new_model') != '') 
		    $heading = stripslashes($this->lang->line('admin_menu_add_new_model')); 
		    else  $heading = 'Add New Model';
            if ($model_id != '') {
                $condition = array('_id' => MongoID($model_id));
                $this->data['modeldetails'] = $this->brand_model->get_all_details(MODELS, $condition);
                if ($this->data['modeldetails']->num_rows() != 1) {
                    redirect(COMPANY_NAME.'/brand/display_model_list');
                }
                $form_mode = TRUE;
				if ($this->lang->line('admin_make_and_model_edit_model') != '') 
		        $heading = stripslashes($this->lang->line('admin_make_and_model_edit_model')); 
		        else  $heading = 'Edit Model';
            }
            $this->data['form_mode'] = $form_mode;
            $this->data['heading'] = $heading;
            $this->load->view(COMPANY_NAME.'/brand/add_edit_model', $this->data);
        }
    }

		/**
		* 
		* To load the add/Edit Model form
		* 
		* @param string $model_id is model id and MongoDB\BSON\ObjectId
		* @redirect HTTP request to display add eit model page
		*
		**/	
   public function insertEditModel() {
		if ($this->checkLogin('C') == '') {
            redirect(COMPANY_NAME);
        } else {
            $model_id = $this->input->post('model_id');
            $brand = $this->input->post('brand');
            $name = $this->input->post('name');
            $type = $this->input->post('type');

            $brandInfo = $this->brand_model->get_all_details(BRAND, array('_id' => MongoID($brand)));
            $typeInfo = $this->brand_model->get_all_details(VEHICLES, array('_id' => MongoID($type)));

            $isDuplicate = FALSE;
            if ($model_id == '') {
                $condition = array('name' => $name, 'brand' => $brand);
                $duplicate_name = $this->brand_model->get_selected_fields(MODELS, $condition, array('brand'));
                if ($duplicate_name->num_rows() > 0)
                    $isDuplicate = TRUE;
            }else {
                $condition = array('name' => $name, 'brand' => $brand);
                $duplicate_name = $this->brand_model->get_selected_fields(MODELS, $condition, array('brand'));
                if ($duplicate_name->num_rows() > 1)
                    $isDuplicate = TRUE;
            }

            if ($isDuplicate) {
                $this->setErrorMessage('error', 'This Model already exists','admin_brand_model_already_exist');
                redirect(COMPANY_NAME.'/brand/add_edit_model/' . $model_id);
            }
            if ($this->input->post('status') == 'on') {
                $status = 'Active';
            } else {
                $status = 'Inactive';
            }

            $dataArr = array('name' => $name,
                'brand' => $brand,
                'brand_name' => $brandInfo->row()->brand_name,
                'type' => $type,
                'type_name' => $typeInfo->row()->vehicle_type,
				'year_of_model' => $this->input->post('year_of_model'),
                'status' => $status
            );
            if ($model_id == '') {
                $this->brand_model->simple_insert(MODELS, $dataArr);
                $this->setErrorMessage('success', 'Model added successfully','admin_brand_model_added_success');
            } else {
                $condition = array('_id' => MongoID($model_id));
                $this->brand_model->update_details(MODELS, $dataArr, $condition);
                $this->setErrorMessage('success', 'Model updated successfully','admin_model_updated_successfully');
            }
            redirect(COMPANY_NAME.'/brand/display_model_list');
        }
    }

		/**
		* 
		* To delete the record of particular model from database
		* 
		* @param string $model_id is model id and MongoDB\BSON\ObjectId
		* @display HTML to list the display model list page
		*
		**/	
    public function delete_model() {
        if ($this->checkLogin('C') == '') {
            redirect(COMPANY_NAME);
        } else {
            $model_id = $this->uri->segment(4, 0);
            $condition = array('_id' => MongoID($model_id));
            $this->brand_model->commonDelete(MODELS, $condition);
            $this->setErrorMessage('success', 'Model deleted successfully','admin_brand_model_deleted_success');
            redirect(COMPANY_NAME.'/brand/display_model_list');
        }
    }
		
		/**
		* To change the status of particular testimonial
		*
		* @param string $status mode Active/InActive
		* @param string $model_id is model id and MongoDB\BSON\ObjectId
		* @redirect http request to show the model list page
		*
		**/
    public function change_model_status() {
        if ($this->checkLogin('C') == '') {
            redirect(COMPANY_NAME);
        } else {
            $mode = $this->uri->segment(4, 0);
            $model_id = $this->uri->segment(5, 0);
            $status = ($mode == '0') ? 'Inactive' : 'Active';
            $newdata = array('status' => $status);
            $condition = array('_id' => MongoID($model_id));
            $this->brand_model->update_details(MODELS, $newdata, $condition);
            $this->setErrorMessage('success', 'Model Status Changed Successfully','admin_brand_model_status_change');
            redirect(COMPANY_NAME.'/brand/display_model_list');
        }
    }

		/**
		* To change the status of model globally
		*
		* @redirect http request to show the model list page
		*
		**/
    public function change_model_status_global() {
        if (count($_POST['checkbox_id']) > 0 && $_POST['statusMode'] != '') {
            $this->brand_model->activeInactiveCommon(MODELS, '_id');
            if (strtolower($_POST['statusMode']) == 'delete') {
                $this->setErrorMessage('success', 'Model records deleted successfully','admin_brand_record_delete');
            } else {
                $this->setErrorMessage('success', 'Model records status changed successfully','admin_brand_record_status_change');
            }
            redirect(COMPANY_NAME.'/brand/display_model_list');
        }
    }

}

/* End of file brand.php */
/* Location: ./application/controllers/COMPANY_NAME/brand.php */