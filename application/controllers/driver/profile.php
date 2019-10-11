<?php

if (!defined('BASEPATH'))
    exit('No direct script access allowed');

/**
 * 
 * This controller contains the functions related to driver management and login, forgot password
 * @author Casperon
 *
 * */
class Profile extends MY_Controller {

    function __construct() {
        parent::__construct();
        $this->load->helper(array('cookie', 'date', 'form'));
        $this->load->library(array('encrypt', 'form_validation'));
        $this->load->model('driversettings_model');
        $this->load->model('driver_model');
        $this->load->model('mail_model');
        $this->load->model('app_model');
    }

    /**
     * 
     * This function check the driver login session and load the templates
     * If session exists then load the dashboard
     * Otherwise load the login form
     * */
    public function index() {
	
		  if ($this->lang->line('dash_driver_login') != '')
            $dash_driver_login = stripslashes($this->lang->line('dash_driver_login'));
        else
            $dash_driver_login = 'Driver Login';

        $this->data['heading'] = $dash_driver_login;
        if ($this->checkLogin('D') == '') {
            $this->check_driver_session();
        }
        if ($this->checkLogin('D') == '') {
            $this->load->view('driver/templates/login.php', $this->data);
            #$this->load->view('driver/templates/driver_index.php',$this->data);
        } else {
            redirect('driver/dashboard');
        }
    }

    /**
     * 
     * This function loads the drivers login  form
     * 
     */
    public function login_form() {

        if ($this->lang->line('dash_driver_login') != '')
            $dash_driver_login = stripslashes($this->lang->line('dash_driver_login'));
        else
            $dash_driver_login = 'Driver Login';

        $this->data['heading'] = $dash_driver_login;
        if ($this->checkLogin('D') == '') {
            $this->load->view('driver/templates/login.php', $this->data);
        } else {
            redirect('driver/dashboard');
        }
    }

    /**
     * 
     * This function loads the drivers login  form
     * 
     */
    public function register_index_form() {

       if ($this->lang->line('dash_driver_registration_form') != '')
            $dash_driver_registration_form = stripslashes($this->lang->line('dash_driver_registration_form'));
        else
            $dash_driver_registration_form = 'Driver Registration Form';

        $this->data['heading'] = $dash_driver_registration_form;
        if ($this->checkLogin('D') == '') {
			$condition = array('status' => 'Active');
			$this->data['locationList'] = $locationList = $this->driver_model->get_all_details(LOCATIONS,$condition,array('city' => 1));
            $this->load->view('driver/templates/register_index.php', $this->data);
        } else {
            redirect('driver/dashboard');
        }
    }


    /**
     * 
     * This function inserts the drivers data in temp storage and redirects to progress form 
     * 
     */
    public function temp_register() { 

		$posted_details = $_POST;
		
		$driver_name = $this->input->post('driver_name');
		$driver_location = $this->input->post('driver_location');
		$category = $this->input->post('category');
		$email = strtolower($this->input->post('email'));
		$mobile_number = $this->input->post('mobile_number');
		if($email != '' && $driver_location != '' && $category != '' && $mobile_number != '' && count($posted_details) >= 9 ){
		
			$checkEmail = $this->driver_model->check_driver_exist(array('email' => $email));
			if ($checkEmail->num_rows() >= 1) {
				$this->setErrorMessage('error', 'This email already exist, please register with different email address.', 'dash_email_already_exist');
				redirect('driver/signup');
			}
			
			 $driver_data = array('category' => MongoID($category),
				'driver_name' => $driver_name,
				'driver_location' => $driver_location,
				'email' => strtolower($email),
				'password' => md5($this->input->post('password')),
				'dail_code' => (string) $this->session->userdata(APP_NAME.'otp_country_code'),
				'mobile_number' => (string) $this->session->userdata(APP_NAME.'otp_phone_number'),
			);
            
			
			$this->driver_model->simple_insert(TEMP_DRIVERS, $driver_data);
			$driver_id = $this->mongo_db->insert_id(); //echo"<pre>";print_r($driver_id);die;
			redirect('driver/signup/progress?q='.base64_encode($driver_id));
		} else {
			$this->setErrorMessage('error', 'Some of the fields are missing', 'dash_some_fields_missing');
            redirect('driver/signup');
		}
    }
	
	
	/**
     * 
     * This function opens the driver's next registration form
     * 
     */
    public function signup_progress_form() { 

        if ($this->lang->line('dash_driver_registration_form') != '') $dash_driver_registration_form = stripslashes($this->lang->line('dash_driver_registration_form'));  else $dash_driver_registration_form = 'Driver Registration Form';


        $this->data['heading'] = $dash_driver_registration_form;
       
		
		$temp_driver_id = base64_decode($this->input->get('q'));

		
		if($temp_driver_id != ''){
			$chk_driver_data = $this->driver_model->get_all_details(TEMP_DRIVERS, array('_id' => MongoID($temp_driver_id)));//echo"<pre>";print_r($chk_driver_data);die;
			if($chk_driver_data->num_rows() > 0){
				$driver_location = $chk_driver_data->row()->driver_location;
				$category = (string)$chk_driver_data->row()->category;
			} else {
				$this->setErrorMessage('error', 'Invalid registration request','driver_invalid_registration');
				redirect('driver/signup');
			}
		} else {
			$this->setErrorMessage('error', 'Some of the fields are missing', 'dash_some_fields_missing');
            redirect('driver/signup');
		}
		
        $get_locationId = array();
        if ($driver_location != '') {
            $get_locationId = $this->driver_model->get_all_details(LOCATIONS, array('status' => 'Active', '_id' =>MongoID($driver_location)))->row();
        }
       
        $get_vehicle_catId = array();
        if ($category != '') {
            $get_vehicle_catId = $this->driver_model->get_all_details(CATEGORY, array('status' => 'Active', '_id' => MongoID($category)))->row();
        }
		
		$catList = $this->driver_model->get_all_details(CATEGORY, array('status' => 'Active'));
		$additonalCats = array();
		$additonalCatsId = array();
		$langCode = $this->data['langCode'];
		foreach($catList->result() as $cats){
			if(isset($get_locationId->fare[$category]['additional_category'])){
				if(in_array((string)$cats->_id,$get_locationId->fare[$category]['additional_category'])){
					$category_name = $cats->name;
					if(isset($cats->name_languages[$langCode ]) && $cats->name_languages[$langCode ] != ''){
						$category_name = $cats->name_languages[$langCode];
					}
					$additonalCats[]= $category_name;
					$additonalCatsId[]= (string)$cats->_id;
				}
			}
		}
		$this->data['additional_category'] = $additonalCats;
		$this->data['additonalCatsId'] = $additonalCatsId;
           
        $this->data['vehicle_types'] = $this->driver_model->get_vehicles_list_by_category($get_vehicle_catId->vehicle_type); 
        $this->data['docx_list'] = $this->driver_model->get_all_details(DOCUMENTS, array('status' => 'Active'));
        $this->data['brandList'] = $this->driver_model->get_all_details(BRAND, array('status' => 'Active'), array('brand_name' => 'ASC'));
        $this->data['modelList'] = $this->driver_model->get_all_details(MODELS, array('status' => 'Active'), array('name' => 'ASC'));
        $this->data['locationDetail'] = $get_locationId;
		$this->data['driver_data'] = $chk_driver_data;
        //echo"<pre>";print_r($chk_driver_data);
        $this->load->view('driver/templates/register', $this->data);
    }

    /**
     * 
     * This function inserts the new driver to database
     * 
     */
    public function register() {	
		
        /**
         * clear the temp folders
         */
       #echo '<pre>'; print_r($_POST); die;
	   

        $dir = getcwd() .DIRECTORY_SEPARATOR."drivers_documents_temp"; //dir absolute path
        $interval = strtotime('-24 hours'); //files older than 24hours
        foreach (glob($dir . "*.*") as $file) {
            if (filemtime($file) <= $interval) {
                unlink($file);
            }
        }
		
		
		
		$temp_driver_id = base64_decode($this->input->post('temp_driver_id'));
		
		if($temp_driver_id != ''){
			$temp_dr_cond = array('_id' => MongoID($temp_driver_id));
			$chk_driver_data = $this->driver_model->get_all_details(TEMP_DRIVERS,$temp_dr_cond);
			if($chk_driver_data->num_rows() == 0){
				$this->setErrorMessage('error', 'Invalid registration request','driver_invalid_registration');
				redirect('driver/signup');
			}
		} else {
			$this->setErrorMessage('error', 'Some of the fields are missing', 'dash_some_fields_missing');
            redirect('driver/signup');
		}

        
        $vehicle_number = $this->input->post('vehicle_number');

		$status = 'Active';
		
		if ($this->input->post('multi_car_status') == 'on') {
            $multi_car_status = 'ON';
        } else {
            $multi_car_status = 'OFF';
        }
		$additional_category = @explode(',',$this->input->post('additional_category'));
		if($multi_car_status == 'OFF') $additional_category = array();

        $excludeArr = array("confirm_password", "new_password", "address", "county", "state", "city", "postal_code", "driver_docx", "driver_docx_expiry", "vehicle_docx", "vehicle_docx_expiry", "vehicle_type", "mobile_number", "dail_code", "termsCondition", "email","temp_driver_id","init_box_mobile_number","vehicle_number","multi_car_status","additional_category",'ac');

        $addressArr['address'] = array('address' => $this->input->post('address'),
            'county' => $this->input->post('county'),
            'state' => $this->input->post('state'),
            'city' => $this->input->post('city'),
            'postal_code' => $this->input->post('postal_code')
        );
        
         if ($this->input->post('ac') == 'on') {
            $ac = 'Yes';
        } else {
            $ac = 'No';
        }

        $image_data = array();
		if(isset($_FILES['thumbnail']['name']) && $_FILES['thumbnail']['name'] != ''){
			$config['overwrite'] = FALSE;
			$config['encrypt_name'] = TRUE;
			$config['allowed_types'] = 'jpg|jpeg|gif|png';
			$config['max_size'] = 2000;
			$config['upload_path'] = './images/users';
			$this->load->library('upload', $config);
			if ($this->upload->do_upload('thumbnail')) {
				$logoDetails = $this->upload->data();
				$this->ImageResizeWithCrop(300, 300, $logoDetails['file_name'], './images/users/');
				@copy('./images/users/' . $logoDetails['file_name'], './images/users/thumb/' . $logoDetails['file_name']);
				$this->ImageResizeWithCrop(150, 150, $logoDetails['file_name'], './images/users/thumb/');
				$profile_image = $logoDetails['file_name'];
				$image_data['image'] = $logoDetails['file_name'];
			} else {
					$logoDetails = $this->upload->display_errors();
					$this->setErrorMessage('error', $logoDetails);
					echo "<script>window.history.go(-1);</script>";
					exit;
			}
		}

        /*         * *
         *
         * document section 
         */
        $documents = array();
        $dr_documentArr = $this->input->post('driver_docx');  #echo '<pre>'; print_r($dr_documentArr); die;
        $dr_expiryArr = $this->input->post('driver_docx_expiry');
        for ($i = 0; $i < count($dr_documentArr); $i++) {
            $fileArr = @explode('|:|', $dr_documentArr[$i]);
            $fileArr = array_filter($fileArr);
            if (count($fileArr) > 0) {
                $docxName = $fileArr[0];
                $fileName = $fileArr[1];
                $fileTypeId = MongoID($fileArr[2]);
                if ($docxName != '' && $fileName != '' && count($fileArr) == 3) {
                    @copy('./drivers_documents_temp/' . $fileName, './drivers_documents/' . $fileName);
                }
                if ($dr_expiryArr[$i] == 'Yes') {
                    $expiryDate = $this->input->post('driver-' . url_title($docxName));
                    $excludeArr[] = url_title('driver-' . $docxName);
                } else {
                    $expiryDate = '';
                }
                if (count($fileArr) > 0) {
                    $documents['driver'][(string) $fileTypeId] = array('typeName' => $docxName, 'fileName' => $fileName, 'expiryDate' => $expiryDate, 'verify_status' => 'No');
                }
            }
        }


        $veh_documentArr = $this->input->post('vehicle_docx');  #echo '<pre>'; print_r($veh_documentArr); die;
        $veh_expiryArr = $this->input->post('vehicle_docx_expiry');
        for ($i = 0; $i < count($veh_documentArr); $i++) {
            $fileArr = @explode('|:|', $veh_documentArr[$i]);
            $docxName = $fileArr[0];
            $fileName = $fileArr[1];
            $fileTypeId = MongoID($fileArr[2]);
            if ($docxName != '' && $fileName != '' && count($fileArr) == 3) {
                @copy('./drivers_documents_temp/' . $fileName, './drivers_documents/' . $fileName);
            }
            if ($veh_expiryArr[$i] == 'Yes') {
                $expiryDate = $this->input->post('vehicle-' . url_title($docxName));
                $excludeArr[] = 'vehicle-' . url_title($docxName);
            } else {
                $expiryDate = '';
            }
			if($docxName != '' && $fileTypeId != '' && $fileName != ''){
				$documents['vehicle'][(string) $fileTypeId] = array('typeName' => $docxName, 'fileName' => $fileName, 'expiryDate' => $expiryDate, 'verify_status' => 'No');
			}
        }
		
		$temp_driver_data = array();
		foreach($chk_driver_data->row() as $key => $drivers){
			if($key != '_id'){
				if($key == 'category') {
					$temp_driver_data[$key] = $drivers;
				} else {
					$temp_driver_data[$key] = (string)$drivers;
				}
			}
		}
		
		
        $driver_data = array('created' => date('Y-m-d H:i:s'),
            'vehicle_type' => MongoID($this->input->post('vehicle_type')),
            'status' => $status,
            'no_of_rides' => 0,
            'availability' => 'No',
            'mode' => 'Available',
            'vehicle_number' => (string) $vehicle_number,
            'verify_status' => 'No',
			'multi_car_status' =>  $multi_car_status,
			'additional_category' => $additional_category,
            'ac' => $ac,
        );
		
		#echo '<pre>'; print_r($temp_driver_data); die;
		
		$driver_location = $temp_driver_data['driver_location'];

         if($driver_location == ''){
               $cond=array('_id'=> MongoID($driver_location));
               $get_loc_commison = $this->driver_model->get_selected_fields(LOCATIONS,$cond,array('site_commission'));
               if(isset($get_loc_commison->row()->site_commission)){ 
                  $driver_data['driver_commission'] = floatval($get_loc_commison->row()->site_commission);
               }
		   }


        $dataArr = array_merge($temp_driver_data, $driver_data, $image_data, $addressArr, array('documents' => $documents));  #echo '<pre>'; print_r($dataArr); die;

        $condition = array();
        $this->driver_model->commonInsertUpdate(DRIVERS, 'insert', $excludeArr, $dataArr, $condition);
        $last_insert_id = $this->mongo_db->insert_id();
		
		
        $fields = array(
            'username' => (string) $last_insert_id,
            'password' => md5((string) $last_insert_id)
        );
        $url = $this->data['soc_url'] . 'create-user.php';
        $this->load->library('curl');
        $output = $this->curl->simple_post($url, $fields);

        /* Update Stats Starts */
        $current_date = MongoDATE(strtotime(date("Y-m-d 00:00:00")));
        $field = array('driver.hour_' . date('H') => 1, 'driver.count' => 1);
        $this->driver_model->update_stats(array('day_hour' => $current_date), $field, 1);
        /* Update Stats End */

        $this->mail_model->send_driver_register_confirmation_mail((string)$last_insert_id);
		
		
		/************  Remove temp driver data ********************/
		$this->driver_model->commonDelete(TEMP_DRIVERS,$temp_dr_cond);


        $this->setErrorMessage('success', 'You have registered successfully', 'dash_you_have_registered_successfully');
        redirect('');
    }

    /**
     * 
     * This function sends driver registration confirmation mail
     * 
     */
    public function send_driver_register_confirmation_mail() {
        
    }

    /**
     * 
     * This function validate the driver login form
     * If details are correct then load the dashboard
     * Otherwise load the login form and show the error message
     */
    public function driver_login() {
		if ($this->lang->line('form_validation_username') != ''){
			$form_validation_username = stripslashes($this->lang->line('form_validation_username'));
		}else{
			$form_validation_username = 'Username';
		}
		if ($this->lang->line('form_validation_password') != ''){
			$form_validation_password = stripslashes($this->lang->line('form_validation_password'));
		}else{
			$form_validation_password = 'Password';
		}
        $this->form_validation->set_rules('driver_name', $form_validation_username, 'required');
        $this->form_validation->set_rules('driver_password', $form_validation_password, 'required');
        if ($this->form_validation->run() === FALSE) {
            $this->load->view('driver/templates/login.php', $this->data);
        } else {
            $name = strtolower($this->input->post('driver_name'));
            $pwd = md5($this->input->post('driver_password'));
            $collection = DRIVERS;
            $condition = array('email' => $name, 'password' => $pwd, 'status' => 'Active');
            $query = $this->driversettings_model->get_all_details($collection, $condition);

            if ($query->num_rows() == 1) {
                $driverdata = array(
                    APP_NAME.'_session_driver_id' => (string) $query->row()->_id,
                    APP_NAME.'_session_driver_name' => $query->row()->driver_name,
                    APP_NAME.'_session_driver_email' => $query->row()->email,
                    APP_NAME.'_session_driver_mode' => $collection
                );

                $this->session->set_userdata($driverdata);
                $newdata = array(
                    'last_login_date' => date("Y-m-d H:i:s"),
                    'last_login_ip' => $this->input->ip_address()
                );
                $condition = array('driver_id' => $query->row()->driver_id);
                $this->driversettings_model->update_details($collection, $newdata, $condition);
                $this->setErrorMessage('success', 'Login Success', 'dash_login_success');
                redirect('driver/dashboard');
            } else {
                $this->setErrorMessage('error', 'Invalid Login Details', 'dash_invalid_login_details');
            }
            redirect('driver');
        }
    }

    /**
     * 
     * This function remove all driver details from session and cookie and load the login form
     */
    public function driver_logout() {
        $newdata = array(
            'last_logout_date' => date("Y-m-d H:i:s")
        );
        $collection = DRIVERS;
        $condition = array('driver_id' => $this->checkLogin('D'));
        $this->driversettings_model->update_details($collection, $newdata, $condition);
        $driverdata = array(
            APP_NAME.'_session_driver_id' => '',
            APP_NAME.'_session_driver_name' => '',
            APP_NAME.'_session_driver_email' => '',
            APP_NAME.'_session_driver_mode' => '',
            APP_NAME.'_session_driver_privileges' => ''
        );
        $this->session->unset_userdata($driverdata);
        $this->setErrorMessage('success', 'Successfully logout from your account', 'dash_successfully_logout_your_account');
        redirect('driver');
    }

    /**
     * 
     * This function loads the forgot password form
     */
    public function driver_forgot_password_form() {
        if ($this->checkLogin('D') == '') {
            $this->load->view('driver/templates/forgot_password.php', $this->data);
        } else {
            redirect('driver/dashboard');
        }
    }

    /**
     * 
     * This function validate the forgot password form
     * If email is correct then generate new password and send it to the email given
     */
    public function driver_forgot_password() {
		if ($this->lang->line('form_validation_email') != ''){
			$form_validation_email = stripslashes($this->lang->line('form_validation_email'));
		}else{
			$form_validation_email = 'Email';
		}
        $this->form_validation->set_rules('email', $form_validation_email, 'required|valid_email');
        if ($this->form_validation->run() === FALSE) {
            $this->load->view('driver/templates/forgot_password.php', $this->data);
        } else {
            $email = $this->input->post('email');
            $collection = DRIVERS;
            $condition = array('email' => $email);
            $driverVal = $this->driversettings_model->get_all_details($collection, $condition);
            if ($driverVal->num_rows() == 1) {
                $new_pwd = $this->get_rand_str('6') . time();
                $newdata = array('reset_id' => $new_pwd);
                $condition = array('email' => $email);
                $this->driversettings_model->update_details($collection, $newdata, $condition);
                $this->send_driver_pwd($new_pwd, $driverVal);
                $this->setErrorMessage('success', 'Password reset link has been sent to your email address','dash_password_reset_sent_email_address');
                redirect('driver');
            } else {
                $this->setErrorMessage('error', 'Email id not matched in our records', 'dash_email_matched_records');
                redirect('driver/reset-password');
            }
            redirect('driver');
        }
    }

    /**
     * 
     * This function check the driver details in browser cookie
     */
    public function check_driver_session() {
        $driver_session = $this->input->cookie(APP_NAME.'_driver_session', FALSE);
        if ($driver_session != '') {
            $driver_id = $this->encrypt->decode($driver_session);
            $mode = $driver_session[APP_NAME.'_session_driver_mode'];
            $condition = array('driver_id' => $driver_id);
            $query = $this->driversettings_model->get_all_details($mode, $condition);
            if ($query->num_rows() == 1) {
                $priv = unserialize($query->row()->privileges);
                $driverdata = array(
                    APP_NAME.'_session_driver_id' => $query->row()->driver_id,
                    APP_NAME.'_session_driver_name' => $query->row()->driver_name,
                    APP_NAME.'_session_driver_email' => $query->row()->email,
                    APP_NAME.'_session_driver_mode' => $mode,
                    APP_NAME.'_session_driver_privileges' => $priv
                );
                $this->session->set_userdata($driverdata);
                $newdata = array(
                    'last_login_date' => date("Y-m-d H:i:s"),
                    'last_login_ip' => $this->input->ip_address()
                );
                $condition = array('driver_id' => $query->row()->driver_id);
                $this->driversettings_model->update_details(driver, $newdata, $condition);
            }
        }
    }

    /**
     * 
     * This function send the new password to driver email
     */
    public function send_driver_pwd($pwd = '', $query) {
        $newsid = '10';
        $reset_url = base_url() . 'driver/reset-password-form/' . $pwd;
        $user_name = $query->row()->driver_name;
        #echo $this->data['langCode'];
        $template_values = $this->driversettings_model->get_email_template($newsid,$this->data['langCode']);
           
        $subject = 'From: ' . $this->config->item('email_title') . ' - ' . $template_values['subject'];
      
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
            'to_mail_id' => $query->row()->email,
            'subject_message' => $subject,
            'body_messages' => stripcslashes($message)
        );
        #var_dump($email_values);
        #exit;
        $email_send_to_common = $this->driversettings_model->common_email_send($email_values);
    }

    /**
     * 
     * This function loads the reset password form
     */
    function reset_password_form() {

        if ($this->lang->line('dash_reset_password') != '')
            $dash_reset_password = stripslashes($this->lang->line('dash_reset_password'));
        else
            $dash_reset_password = 'Reset Password';


        $reset_id = $this->uri->segment(3);
        $condition = array('reset_id' => $reset_id);
        $driverVal = $this->driversettings_model->get_selected_fields(DRIVERS, $condition, array('_id'));
        if ($driverVal->num_rows() == 1) {
            $this->data['heading'] = $dash_reset_password;
            $this->data['reset_id'] = $reset_id;
            $this->load->view('driver/templates/reset_password.php', $this->data);
        } else {
            $this->setErrorMessage('error', 'Invalid reset password link', 'dash_invalid_reset_password');
            redirect('driver');
        }
    }

    /**
     * 
     * This function updates the reset password
     */
    function update_reset_password() {
        if ($this->checkLogin('D') != '') {
            redirect('driver');
        }
        $reset_id = $this->input->post('reset_id');
        $pwd = $this->input->post('new_password');
        $condition = array('reset_id' => $reset_id); 
        $driverVal = $this->driversettings_model->update_details(DRIVERS, array('password' => md5($pwd), 'reset_id' => ''), $condition);
        $this->setErrorMessage('success', 'Password changed successfully', 'dash_password_changed_successfully');
        redirect('driver');
    }

    /**
     * 
     * This function loads the change password form
     */
    public function change_password_form() {

        if ($this->lang->line('dash_change_password') != '')
            $dash_change_password = stripslashes($this->lang->line('dash_change_password'));
        else
            $dash_change_password = 'Change Password';

        $this->data['heading'] = $dash_change_password;
		$this->data['sideMenu'] = 'password';
        if ($this->checkLogin('D') == '') {
            redirect('driver');
        } else {
            $this->load->view('driver/driversettings/changepassword.php', $this->data);
        }
    }

    /**
     * 
     * This function validate the change password form
     * If details are correct then change the driver password
     */
    public function change_password() {
        if ($this->checkLogin('D') == '') {
            redirect('driver');
        }
		
		 $pwd = md5($this->input->post('password'));
		 $new_pwd = $this->input->post('new_password');
		
        if ($pwd == '' && $new_pwd  == '') {
			$this->setErrorMessage('error', 'Please enter all the required fields');
            redirect('driver/profile/change_password_form');
        } else {
            $driver_id = $this->session->userdata(APP_NAME.'_session_driver_id');
           
            $condition = array('_id' => MongoID($driver_id), 'password' => $pwd);
            $query = $this->driversettings_model->get_all_details(DRIVERS, $condition);
            if ($query->num_rows() == 1) {
                $newdata = array('password' => md5($new_pwd));
                $condition = array('_id' => MongoID($driver_id));
                $this->driversettings_model->update_details(DRIVERS, $newdata, $condition);
                $this->setErrorMessage('success', 'Password changed successfully', 'dash_password_changed_successfully');
                redirect('driver/profile/change_password_form');
            } else {
                $this->setErrorMessage('error', 'Invalid current password', 'dash_invalid_current_password');
                redirect('driver/profile/change_password_form');
            }
            redirect('driver/profile/edit_profile_form');
        }
    }

    /**
     * 
     * This function loads the driver profile view
     */
    public function view_profile() {
        if ($this->checkLogin('D') == '') {
            $this->setErrorMessage('error', 'You must login first', 'dash_you_must_login_first');
            redirect('driver');
        }
        $driver_id = $this->session->userdata(APP_NAME.'_session_driver_id');
		 if ($this->lang->line('dash_your_profile') != '')
            $heading = stripslashes($this->lang->line('dash_your_profile'));
        else
            $heading = 'Your Profile';
        $this->data['heading'] = $heading ;
        $condition = array('_id' => MongoID($driver_id));
        $this->data['driver_details'] = $driver_details = $this->driver_model->get_all_details(DRIVERS, $condition);
        if ($driver_details->num_rows() == 0) {
            $this->setErrorMessage('error', 'No record found for this driver', 'dash_no_record_found_this_driver');
            redirect('driver');
        }
		
		$cond = array('_id' => $driver_details->row()->vehicle_type);
        $this->data['vehicle_types'] = $this->driver_model->get_all_details(VEHICLES, $cond,array('vehicle_type' => 'ASC'));
        $this->data['docx_list'] = $this->driver_model->get_all_details(DOCUMENTS, array('status' => 'Active'));
		
		$cond = array('_id' => $driver_details->row()->category);
        $this->data['categoryList'] = $this->driver_model->get_all_details(CATEGORY, $cond);
		$cond = array('_id' => MongoID($driver_details->row()->vehicle_maker));
        $this->data['brandList'] = $this->driver_model->get_all_details(BRAND, $cond);
		$cond = array('_id' => MongoID($driver_details->row()->vehicle_model));
        $this->data['modelList'] = $this->driver_model->get_all_details(MODELS, $cond);
		
		$cond = array('_id' => MongoID($driver_details->row()->driver_location));
        $this->data['locationList'] = $locationList = $this->driver_model->get_all_details(LOCATIONS,$cond);
		$this->data['sideMenu'] = 'profile';
        $this->load->view('driver/driversettings/view_profile', $this->data);
    }


	/**
     * 
     * This function validates the driver settings form
     */
    public function edit_profile_form() {

        if ($this->lang->line('dash_edit_driver') != '')
            $dash_edit_driver = stripslashes($this->lang->line('dash_edit_driver'));
        else
            $dash_edit_driver = 'Edit Driver';

        if ($this->checkLogin('D') == '') {
            $this->setErrorMessage('error', 'You must login first', 'dash_you_must_login_first');
            redirect('driver');
        }
        $driver_id = $this->session->userdata(APP_NAME.'_session_driver_id');
        $this->data['heading'] = $dash_edit_driver;
        $condition = array('_id' => MongoID($driver_id));
        $this->data['driver_details'] = $driver_details = $this->driver_model->get_all_details(DRIVERS, $condition);
        if ($driver_details->num_rows() == 0) {
            $this->setErrorMessage('error', 'No record found for this driver', 'dash_no_record_found_this_driver');
            redirect('driver');
        }
		
		$locId = (string)$driver_details->row()->driver_location;
		$catId = (string)$driver_details->row()->category;
        $getLoc = $this->driver_model->get_all_details(LOCATIONS, array('status' => 'Active', '_id' =>MongoID($locId)))->row();
       
		$catList = $this->driver_model->get_all_details(CATEGORY, array('status' => 'Active'));
		$additonalCats = array();
		$additonalCatsId = array();
		$langCode = $this->data['langCode'];
		foreach($catList->result() as $cats){
			if(isset($getLoc->fare[$catId]['additional_category'])){
				if(in_array((string)$cats->_id,$getLoc->fare[$catId]['additional_category'])){
					$category_name = $cats->name;
					if(isset($cats->name_languages[$langCode ]) && $cats->name_languages[$langCode ] != ''){
						$category_name = $cats->name_languages[$langCode];
					}
					$additonalCats[]= $category_name;
					$additonalCatsId[]= (string)$cats->_id;
				}
			}
		}
		$this->data['additional_category'] = $additonalCats;
		$this->data['additonalCatsId'] = $additonalCatsId;
		
        $this->data['vehicle_types'] = $this->driver_model->get_all_details(VEHICLES, array('status' => 'Active'),array('vehicle_type' => 'ASC'));
        $this->data['docx_list'] = $this->driver_model->get_all_details(DOCUMENTS, array('status' => 'Active'));
        $this->data['categoryList'] = $this->driver_model->get_all_details(CATEGORY, array('status' => 'Active'), array('name' => 'ASC'));
        $this->data['brandList'] = $this->driver_model->get_all_details(BRAND, array('status' => 'Active'), array('brand_name' => 'ASC'));
        $this->data['modelList'] = $this->driver_model->get_all_details(MODELS, array('status' => 'Active'), array('name' => 'ASC'));
        $this->data['locationList'] = $this->driver_model->get_all_details(LOCATIONS, array('status' => 'Active'), array('city' => 'ASC'));
		$this->data['sideMenu'] = 'profile';
        //echo'<pre>';print_r($this->data);die;
        $this->load->view('driver/driversettings/edit_profile', $this->data);
    }

    /**
     *
     * This function uploads the documents via ajax for driver add & edit 
     *
     * */
    public function ajax_document_upload() {
        $docx_name = $this->input->get('docx_name');
        $docResult = array();

        $path = "drivers_documents_temp/";
        $imgRnew = @explode('.', $_FILES[$docx_name]["name"]);
        $NewImg = url_title($imgRnew[0], '-', TRUE) . '-' . time();
        $fileName = urlencode($NewImg);

        $extension = $imgRnew[count($imgRnew) - 1];

        $max_file_size = 2097152;
        $allowed = array("image/jpeg", "image/jpg", "image/png", "image/gif", "application/pdf");
        $file_type = $_FILES[$docx_name]['type'];
        $file_size = $_FILES[$docx_name]['size'];
        $filetmpName = $fileName . '.' . $extension;


        /*         * *   Language   ** */
        if ($this->lang->line('dash_file_could_not_be_uploaded') != '')
            $dash_file_could_not_be_uploaded = stripslashes($this->lang->line('dash_file_could_not_be_uploaded'));
        else
            $dash_file_could_not_be_uploaded = 'File could not be uploaded';

        if ($this->lang->line('dash_file_too_large') != '')
            $dash_file_too_large = stripslashes($this->lang->line('dash_file_too_large'));
        else
            $dash_file_too_large = 'File too large. File must be less than 2 megabytes.';

        if ($this->lang->line('dash_invalid_file_type') != '')
            $dash_invalid_file_type = stripslashes($this->lang->line('dash_invalid_file_type'));
        else
            $dash_invalid_file_type = 'Invalid file type. Only PDF, JPG, GIF and PNG types are accepted.';
        /*         * *************** */

        if ($filetmpName != '') {
            if (in_array($file_type, $allowed)) {
                if ($_FILES[$docx_name]["size"] < $max_file_size) {
                    $uploadsdocx = move_uploaded_file($_FILES[$docx_name]["tmp_name"], $path . $filetmpName);
                    if ($uploadsdocx == true) {
                        $docResult['docx_name'] = $filetmpName;
                        $docResult['err_msg'] = 'Success';
                    } else {
                        $docResult['err_msg'] = $dash_file_could_not_be_uploaded;
                    }
                } else {
                    $docResult['err_msg'] = $dash_file_too_large;
                }
            } else {
                $docResult['err_msg'] = $dash_invalid_file_type;
            }
        }
        echo json_encode($docResult);
    }

    /**
     *
     * This function Inserts & Edits the drivers
     *
     * */
    public function insertEdit_driver() {
        if ($this->checkLogin('D') == '') {
            $this->setErrorMessage('error', 'You must login first', 'dash_you_must_login_first');
            redirect('driver');
        }
        //echo '<pre>'; print_r($_POST); die;
        /**
         * clear the temp folders
         */
        $driver_id = $this->input->post('driver_id');

        $dir = getcwd() .DIRECTORY_SEPARATOR."drivers_documents_temp"; //dir absolute path
        $interval = strtotime('-24 hours'); //files older than 24hours
        foreach (glob($dir . "*.*") as $file) {
            if (filemtime($file) <= $interval) {
                unlink($file);
            }
        }

		$status = 'Active';
        $vehicle_number = $this->input->post('vehicle_number');
		
		if ($this->input->post('multi_car_status') == 'on') {
            $multi_car_status = 'ON';
        } else {
            $multi_car_status = 'OFF';
        }
		$additional_category = @explode(',',$this->input->post('additional_category'));
		if($multi_car_status == 'OFF') $additional_category = array();

        $excludeArr = array("driver_id", "confirm_password", "new_password", "address", "county", "state", "city", "postal_code", "driver_docx", "driver_docx_expiry", "vehicle_docx", "vehicle_docx_expiry", "vehicle_type", "mobile_number", "dail_code",'email','vehicle_number',"multi_car_status","additional_category");

        $addressArr['address'] = array('address' => $this->input->post('address'),
            'county' => $this->input->post('county'),
            'state' => $this->input->post('state'),
            'city' => $this->input->post('city'),
            'postal_code' => $this->input->post('postal_code')
        );

        $image_data = array();
		if(isset($_FILES['thumbnail']['name']) && $_FILES['thumbnail']['name'] != ''){
			$config['overwrite'] = FALSE;
			$config['encrypt_name'] = TRUE;
			$config['allowed_types'] = 'jpg|jpeg|gif|png';
			$config['max_size'] = 2000;
			$config['upload_path'] = './images/users';
			$this->load->library('upload', $config);
			if ($this->upload->do_upload('thumbnail')) {
				$logoDetails = $this->upload->data();
				$this->ImageResizeWithCrop(300, 300, $logoDetails['file_name'], './images/users/');
				@copy('./images/users/' . $logoDetails['file_name'], './images/users/thumb/' . $logoDetails['file_name']);
				$this->ImageResizeWithCrop(150, 150, $logoDetails['file_name'], './images/users/thumb/');
				$profile_image = $logoDetails['file_name'];
				$image_data['image'] = $logoDetails['file_name'];
			} else {
					$logoDetails = $this->upload->display_errors();
					$this->setErrorMessage('error', $logoDetails);
					echo "<script>window.history.go(-1);</script>";
					exit;
			}
		}
				

        /*         * *
         *
         * document section 
         */
        $documents = array();
        $dr_documentArr = $this->input->post('driver_docx'); # echo '<pre>'; print_r($dr_documentArr); die;
        $dr_expiryArr = $this->input->post('driver_docx_expiry');
        for ($i = 0; $i < count($dr_documentArr); $i++) {
            $fileArr = @explode('|:|', $dr_documentArr[$i]);
            $fileArr = array_filter($fileArr);
            if (count($fileArr) > 0) {
                $docxName = $fileArr[0];
                $fileName = $fileArr[1];
                $fileTypeId = MongoID($fileArr[2]);
                if ($docxName != '' && $fileName != '' && count($fileArr) == 3) {
                    @copy('./drivers_documents_temp/' . $fileName, './drivers_documents/' . $fileName);
                }
                if ($dr_expiryArr[$i] == 'Yes') {
                    $expiryDate = $this->input->post('driver-' . url_title($docxName));
                    $excludeArr[] = url_title('driver-' . $docxName);
                } else {
                    $expiryDate = '';
                }
                if (count($fileArr) > 0) {
                    if ($driver_id == '') {
                        $documents['driver'][(string) $fileTypeId] = array('typeName' => $docxName, 'fileName' => $fileName, 'expiryDate' => $expiryDate, 'verify_status' => 'No');
                    } else {
                        $documents['driver'][(string) $fileTypeId] = array('typeName' => $docxName, 'fileName' => $fileName, 'expiryDate' => $expiryDate);
                    }
                }
            }
        }


        $veh_documentArr = $this->input->post('vehicle_docx');  #echo '<pre>'; print_r($veh_documentArr); die;
        $veh_expiryArr = $this->input->post('vehicle_docx_expiry');
        for ($i = 0; $i < count($veh_documentArr); $i++) {
            $fileArr = @explode('|:|', $veh_documentArr[$i]);
            $docxName = $fileArr[0];
            $fileName = $fileArr[1];
            $fileTypeId = MongoID($fileArr[2]);
            if ($docxName != '' && $fileName != '' && count($fileArr) == 3) {
                @copy('./drivers_documents_temp/' . $fileName, './drivers_documents/' . $fileName);
            }
            if ($veh_expiryArr[$i] == 'Yes') {
                $expiryDate = $this->input->post('vehicle-' . url_title($docxName));
                $excludeArr[] = 'vehicle-' . url_title($docxName);
            } else {
                $expiryDate = '';
            }
			if($docxName != '' && $fileTypeId != '' && $fileName != ''){
				if ($driver_id == '') {
					$documents['vehicle'][(string) $fileTypeId] = array('typeName' => $docxName, 'fileName' => $fileName, 'expiryDate' => $expiryDate, 'verify_status' => 'No');
				} else {
					$documents['vehicle'][(string) $fileTypeId] = array('typeName' => $docxName, 'fileName' => $fileName, 'expiryDate' => $expiryDate);
				}
			}
        }

        $driver_data = array(
            'password' => md5($this->input->post('new_password')),
            'vehicle_type' => MongoID($this->input->post('vehicle_type')),
            'status' => $status,
            'no_of_rides' => '0',
            'availability' => 'No',
            'mode' => 'Available',
            #'dail_code' => (string) $this->input->post('dail_code'),
            #'mobile_number' => (string) $this->input->post('mobile_number'),
            'category' => MongoID($this->input->post('category')),
            'vehicle_number' => (string) $vehicle_number,
			'multi_car_status' =>  $multi_car_status,
			'additional_category' => $additional_category
        );

        if ($driver_id != '') {
            unset($driver_data['no_of_rides']);
            unset($driver_data['availability']);
            unset($driver_data['password']);
            unset($driver_data['mode']);
			unset($driver_data['status']);
        }

        $dataArr = array_merge($driver_data, $image_data, $addressArr, array('documents' => $documents));  #echo '<pre>'; print_r($dataArr); die;
        if ($driver_id == '') {
            $condition = array();
            $this->driver_model->commonInsertUpdate(DRIVERS, 'insert', $excludeArr, $dataArr, $condition);
            /* Update Stats Starts */
            $current_date = MongoDATE(strtotime(date("Y-m-d 00:00:00")));
            $field = array('driver.hour_' . date('H') => 1, 'driver.count' => 1);
            $this->driver_model->update_stats(array('day_hour' => $current_date), $field, 1);
            /* Update Stats End */
            $this->setErrorMessage('success', 'Your details added successfully', 'dash_your_details_added');
        } else {
            $excludeArr[] = 'promo_code';
            $condition = array('_id' => MongoID($driver_id));
            $this->driver_model->commonInsertUpdate(DRIVERS, 'update', $excludeArr, $dataArr, $condition);
			$new_uploaded_document=$this->input->post('new_uploaded_document');
            if($new_uploaded_document!=''&& $new_uploaded_document==1)
			{
				$condition = array('_id' => MongoID($driver_id));
				$this->driver_model->update_details(DRIVERS,array('verify_status'=>'No'), $condition);
			}
            $this->setErrorMessage('success', 'Your details updated successfully', 'dash_your_details_updated');
        }
        redirect('driver/profile/edit_profile_form');
    }

    /**
     *
     * This function loads the add/Edit Banking Informations form
     *
     * */
    public function banking() {
        if ($this->checkLogin('D') == '') {
            redirect('driver');
        } else {

            if ($this->lang->line('dash_add_banking_details') != '')
                $dash_add_banking_details = stripslashes($this->lang->line('dash_add_banking_details'));
            else
                $dash_add_banking_details = 'Add Banking Details';

            if ($this->lang->line('dash_edit_banking_details') != '')
                $dash_edit_banking_details = stripslashes($this->lang->line('dash_edit_banking_details'));
            else
                $dash_edit_banking_details = 'Add Banking Details';

            $driver_id = $this->checkLogin('D');
            $form_mode = FALSE;
            $heading = $dash_add_banking_details;
            if ($driver_id != '') {
                $condition = array('_id' => MongoID($driver_id));
                $this->data['driver_details'] = $driver_details = $this->driver_model->get_all_details(DRIVERS, $condition);
                $form_mode = TRUE;
                $heading = $dash_edit_banking_details;
            }
			
            $this->data['form_mode'] = $form_mode;
            
            $heading = stripslashes("Not Allowed, Contact Operator");

            $this->data['heading'] = $heading;

            $this->data['sideMenu'] = 'banking';
            $this->load->view('driver/driversettings/banking', $this->data);
        }
    }

    /**
     *
     * This function inserts / edit the  banking informations
     * */
    public function insertEditBanking() {
        if ($this->checkLogin('D') == '') {
            redirect('driver');
        } 
        $driver_id = $this->input->post('driver_id');
		$acc_holder_name = $this->input->post('acc_holder_name');
        $acc_holder_address = $this->input->post('acc_holder_address');
        $acc_number = $this->input->post('acc_number');
        $bank_name = $this->input->post('bank_name');
        $branch_name = $this->input->post('branch_name');
        $branch_address = $this->input->post('branch_address');
        $swift_code = $this->input->post('swift_code');
        $routing_number = $this->input->post('routing_number');
        $postedValues = $_POST;
        unset($postedValues['driver_id']);
		$dataArr = array('banking' =>array('acc_holder_name'=>$acc_holder_name,
										 'acc_holder_address'=>$acc_holder_address,
										 'acc_number'=>$acc_number,
										 'bank_name'=>$bank_name,
										 'branch_name'=>$branch_name,
										 'branch_address'=>$branch_address,
										 'swift_code'=>$swift_code,
										 'routing_number'=>$routing_number
		));
        $condition = array('_id' => MongoID($driver_id));
        $this->driver_model->update_details(DRIVERS, $dataArr, $condition);
        $this->setErrorMessage('success', 'Your banking details updated successfully', 'dash_your_banking_details_updated');
        redirect('driver/profile/banking');
    }

    /**
     *
     * This function loads the form to change email 
     *
     * */
    public function change_email_form() {
        if ($this->checkLogin('D') == '') {
            redirect('driver');
        } else {

            if ($this->lang->line('dash_change_email_address') != '')
                $dash_change_email_address = stripslashes($this->lang->line('dash_change_email_address'));
            else
                $dash_change_email_address = 'Change Email Address';
			$this->data['sideMenu'] = 'email';
            $this->data['heading'] = $dash_change_email_address;
            $this->load->view('driver/driversettings/change_email', $this->data);
        }
    }

    /**
     *
     * This function loads the form to change email 
     *
     * */
    public function change_email() {
        if ($this->checkLogin('D') == '') {
            redirect('driver');
        }
		#echo '<pre>'; print_r($_POST); die;
        $driver_id = $this->checkLogin('D');
        $email = $this->input->post('email');
        $new_email = $this->input->post('new_email');
		if($email==strtolower($new_email)) {
		    $this->setErrorMessage('error', 'Please enter a different mail id', 'dash_same_mail_id');
			 redirect('driver/profile/change_email_form');
		}
        $condition = array('_id' => MongoID($driver_id), 'email' => $email);
        $query = $this->driversettings_model->get_all_details(DRIVERS, $condition);
        if ($query->num_rows() == 1) {
			$condition = array( 'email' => strtolower($new_email));
			
			$Chkquery = $this->driversettings_model->get_all_details(DRIVERS, $condition);
			  if ($Chkquery->num_rows() == 0) {
					$newdata = array('email' => $new_email);
					$condition = array('_id' => MongoID($driver_id));
					$this->driversettings_model->update_details(DRIVERS, $newdata, $condition);
					$this->setErrorMessage('success', 'Email address changed successfully', 'dash_email_address_changed');
					redirect('driver/profile/view_profile');
				} else {
					$this->setErrorMessage('error', 'Email address already exist', 'dash_email_already_exist');
				}
        } else {
            $this->setErrorMessage('error', 'Invalid current email', 'dash_invalid_current_email');
        }
        redirect('driver/profile/change_email_form');
    }

    /**
     *
     * This function loads the form to change email 
     *
     * */
    public function change_mobile_form() {
        if ($this->checkLogin('D') == '') {
            redirect('driver');
        } else {
			
			$condition = array('_id' => MongoID($this->checkLogin('D')));
			$this->data['driverData'] = $this->driversettings_model->get_selected_fields(DRIVERS, $condition,array('dail_code','mobile_number'));
			
            if ($this->lang->line('dash_change_mobile_number') != '')
                $dash_change_mobile_number = stripslashes($this->lang->line('dash_change_mobile_number'));
            else
                $dash_change_mobile_number = 'Change Mobile Number';
			
            $this->data['heading'] = $dash_change_mobile_number;
			$this->data['sideMenu'] = 'mobile';
            $this->load->view('driver/driversettings/change_mobile', $this->data);
        }
    }

    /**
     * This function chage the mobile number for driver
     *
     * */
    function change_mobile() {
        if ($this->checkLogin('D') == '') {
            $this->setErrorMessage('error', 'You must login first', 'dash_you_must_login_first');
            redirect('driver');
        }
		#echo '<pre>'; print_r($_POST); die;
        $driver_id = $this->checkLogin('D');
        $otp = $this->input->post('mobile_otp');
        $mobile_number = $this->input->post('mobile_number');
        $dail_code = $this->input->post('dail_code');
        if ($otp == $this->session->userdata(APP_NAME.'sms_otp')) {
			if($mobile_number == $this->session->userdata(APP_NAME.'otp_phone_number')){
				$condition = array('_id' => MongoID($driver_id));
				$newdata = array('dail_code' => (string) $dail_code, 'mobile_number' => (string) $mobile_number);
				$this->user_model->update_details(DRIVERS, $newdata, $condition);
				$this->setErrorMessage('success', 'Your mobile number changed successfully', 'dash_your_mobile_number_changed');
				redirect('driver/profile/view_profile');
			} else {
				$this->setErrorMessage('error', 'Your otp mobile number does not match', 'dash_otp_mobile_note_match');
				redirect('driver/profile/change_mobile_form');
			}
        } else {
            $this->setErrorMessage('error', 'Your one time password is not match', 'dash_otp_not_be_match');
            redirect('driver/profile/change_mobile_form');
        }
        redirect('driver/profile/change_mobile_form');
    }

    /**
     * 
     * This function set the Sidebar Hide show 
     */
    public function check_set_sidebar_session($id) {
        $driverdata = array('session_sidebar_id' => $id);
        $this->session->set_userdata($driverdata);
    }
	
	/**
	*
	*	This function is checks the driver is exist or not by email or mobile number
	*	@Param email
	*
	**/
    public function ajax_check_driver_email_exist(){
		$returnArr['status']='1';
		$returnArr['response']='';
		
		$email = $this->input->post('email');
		if($email  != ''){
			if ($this->lang->line('driver_already_exist') != '')
                $already_exist = stripslashes($this->lang->line('driver_already_exist'));
            else
                $already_exist = 'already exist';
		
			$chkDriver = $this->user_model->get_selected_fields(DRIVERS,array('email' => $email),array('_id'));
			if($chkDriver->num_rows() > 0){
				$returnArr['status']='0';
				$returnArr['response']='<b>'.$email.' </b> '.$already_exist;
			}
		}
		$json_encode_new = json_encode($returnArr);
        echo $json_encode_new; 
	}
	
	
	/*
	***** This function return available categories in specified location ******
	*/
	public function available_categories(){
		
		$returnArr['status']=0;
		$dataArr='';
		try{
			$location_id=$this->input->post('locId');
			$location_details=$this->driver_model->get_all_details(LOCATIONS,array('_id'=>MongoID($location_id)));
			if($location_details->num_rows() >0){
			
			if ($this->lang->line('driver_choose_catagory') != '')
				
                 if ($this->lang->line('site_user_cab_type_upper') != '') 
                    $driver_choose_catagory = stripslashes($this->lang->line('site_user_cab_type_upper')); 
                else 
                    $driver_choose_catagory = 'CAB TYPE';
				
				$avail_category=$location_details->row()->avail_category;
				$langCode = $this->data['langCode'];
				if(isset($avail_category)&& !empty($avail_category)){
					$returnArr['status']=1;	
					$dataArr.='<option value="" hidden="hidden">'.$driver_choose_catagory.'</option>';
					foreach($avail_category as $cat){
						$cat_details=$this->driver_model->get_all_details(CATEGORY,array('_id'=>MongoID($cat),'status'=>'Active'));
						if(isset($cat_details->row()->vehicle_type) && !empty($cat_details->row()->vehicle_type)){
							$category_name = $cat_details->row()->name;
							if(isset($cat_details->row()->name_languages[$langCode]) && $cat_details->row()->name_languages[$langCode] != '') $category_name = $cat_details->row()->name_languages[$langCode];
							
							if($cat_details->row()->name!='') {
								$dataArr.='<option value="'.(string)$cat_details->row()->_id.'">'.$category_name.'</option>';
							}
						}
					}
					$returnArr['message']=$dataArr;
				}else{
					if ($this->lang->line('dash_no_available_category') != '')
					$dash_no_available_category = stripslashes($this->lang->line('dash_no_available_category'));
					else
					$dash_no_available_category = 'No Category Available In Your Location';
					$returnArr['message']= $dash_no_available_category;
				}
			
			}else{
				if ($this->lang->line('rides_location_not_avail') != '')
				$rides_location_not_avail = stripslashes($this->lang->line('rides_location_not_avail'));
				else
				$rides_location_not_avail = 'Location is not available';
				$returnArr['message']=$rides_location_not_avail;
			}
			
		}catch(MongoException $me){
			if ($this->lang->line('error_in_connnection') != '')
            $error_in_connnection = stripslashes($this->lang->line('error_in_connnection'));
			else
            $error_in_connnection = 'Error in connection';
			$returnArr['message']=$error_in_connnection;
		}
		header("Content-type:text/plain");
		echo json_encode($returnArr);
		
		
	}
	
	public function ajax_valid_image() {
        $path = $_FILES['thumbnail']['name'];
        $ext = pathinfo($path, PATHINFO_EXTENSION); 
        $allowTypes = array('jpg','jpeg','gif','png');
        if(in_array(strtolower($ext),$allowTypes)){
            echo 'Success'; 
        } else {
            echo 'File_Ext_Err';
        }
    }

}

/* End of file profile.php */
/* Location: ./application/controllers/driver/profile.php */