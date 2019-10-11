<?php if (!defined('BASEPATH')) exit('No direct script access allowed');

/**
* 
* This controller contains the functions related to Web View Driver Signup process
* @author Casperon
*
* */
class App_driver extends MY_Controller {

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
     * This function loads the drivers login  form
     * 
     */
    public function register_form() { 
		
		$lang_code = $this->input->get('lang');
		if($lang_code!=""){
			$this->load->helper('lg_helper');
			change_web_language($lang_code);
		}
		
		$this->data['heading'] = 'Driver Registration';
		$location = $this->driver_model->get_all_details(LOCATIONS, array('status' => 'Active'), array('city' => 'ASC'));
		$this->data['locationList'] = $location;
		if($this->data['isDemo']){
				$this->load->view('driver/forapp/driver_index_demo.php', $this->data);
		}else{
			$this->load->view('driver/forapp/driver_index.php', $this->data);
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
				redirect('app/driver/signup');
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
			$driver_id = $this->mongo_db->insert_id(); 
			redirect('app/driver/signup/progress?q='.base64_encode($driver_id));
		} else {
			$this->setErrorMessage('error', 'Some of the fields are missing', 'dash_some_fields_missing');
            redirect('app/driver/signup');
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
			$chk_driver_data = $this->driver_model->get_all_details(TEMP_DRIVERS, array('_id' => MongoID($temp_driver_id)));
			if($chk_driver_data->num_rows() > 0){
				$driver_location = $chk_driver_data->row()->driver_location;
				$category = (string)$chk_driver_data->row()->category;
			} else {
				$this->setErrorMessage('error', 'Invalid registration request','driver_invalid_registration');
				redirect('app/driver/signup');
			}
		} else {
			$this->setErrorMessage('error', 'Some of the fields are missing', 'dash_some_fields_missing');
            redirect('app/driver/signup');
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
        $this->load->view('driver/forapp/register', $this->data);
    }
	
	
	
	public function check_email() {
        $email = $this->input->post('email');
     

        $returnArr['status'] = '0';
        $returnArr['message'] = '';

        $Driverdata = $this->driver_model->get_all_details(DRIVERS,array('email'=>$email));
        if ($Driverdata->num_rows() > 0) {
            $returnArr['status'] = '1';

            if ($this->lang->line('driver_is_already_exist') != '')
                $driver_is_already_exist = stripslashes($this->lang->line('driver_is_already_exist'));
            else
                $driver_is_already_exist = 'is already exist';
            $returnArr['message'] = '<b>' . $email . '</b> ' . $driver_is_already_exist . '.';
        }

        $json_encode = json_encode($returnArr);
        echo $this->cleanString($json_encode);
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
       # echo '<pre>'; print_r($_POST); die;

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
				redirect('app/driver/signup');
			}
		} else {
			$this->setErrorMessage('error', 'Some of the fields are missing', 'dash_some_fields_missing');
            redirect('app/driver/signup');
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
		
        $excludeArr = array("confirm_password", "new_password", "address", "county", "state", "city", "postal_code", "driver_docx", "driver_docx_expiry", "vehicle_docx", "vehicle_docx_expiry", "vehicle_type", "mobile_number", "dail_code", "termsCondition", "email","temp_driver_id","vehicle_number","multi_car_status","additional_category",'ac');

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
		if ($this->input->post('ac') == 'on') {
            $ac = 'Yes';
        } else {
            $ac = 'No';
        }
		
        $driver_data = array('created' => date('Y-m-d H:i:s'),
            'vehicle_type' => MongoID($this->input->post('vehicle_type')),
            'status' => $status,
            'ac' => $ac,
            'no_of_rides' => 0,
            'availability' => 'No',
            'mode' => 'Available',
            'vehicle_number' => (string) $vehicle_number,
            'verify_status' => 'No',
			'multi_car_status' =>  $multi_car_status,
			'additional_category' => $additional_category
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

        redirect('v6/app/driver/signup/success');
    }
	
	/*
	***** Function loads when registration is success ******
	*/
	public function success(){
			$this->data['heading']='Login Success';
			$this->load->view('driver/forapp/success.php',$this->data);
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
	
	
	/**
	* 
	* This function inserts the drivers data in temp storage and redirects to progress form 
	* 
	**/
    public function driver_register_demo() {
		$driver_name = $this->input->post('driver_name');
		$email = strtolower($this->input->post('email'));
		$dail_code = $this->input->post('dail_code');
		$mobile_number = $this->input->post('mobile_number');
		$password = $this->input->post('password');
       
		if($driver_name != '' && $email != '' &&  $dail_code != '' && $mobile_number != '' && $password != ''){
		
			$checkEmail = $this->driver_model->check_driver_exist(array('email' => $email));
			if ($checkEmail->num_rows() >= 1) {
				$this->setErrorMessage('error', 'This email already exist, please register with different email address.', 'dash_email_already_exist');
				redirect('app/driver/signup');
			}
			
			$totalDrivers = $this->driver_model->get_selected_fields(DRIVERS,array(),array('_id'))->count();
			$offset = rand(0,$totalDrivers);
			$sampleDriver = $this->driver_model->get_all_details(DRIVERS,array(),array(),1,($offset-1));
						
			$driver_location=(string)$sampleDriver->row()->driver_location;
			$category=(string)$sampleDriver->row()->category;
			$vehicle_type=(string)$sampleDriver->row()->vehicle_type;
			$vehicle_maker=(string)$sampleDriver->row()->vehicle_maker;
			$vehicle_model=(string)$sampleDriver->row()->vehicle_model;
			$vehicle_model_year=$sampleDriver->row()->vehicle_model_year;
			
			
			$vehicle_number = strtoupper($this->get_rand_str(2))." ".str_pad(rand(0, pow(10, 2)-1), 2, '0', STR_PAD_LEFT)." ".strtoupper($this->get_rand_str(2))." ".str_pad(rand(0, pow(10, 4)-1), 4, '0', STR_PAD_LEFT);
			$addressArr = array('address' => "",'county' => "",'state' => "",'city' => "",'postal_code' => "");			 
			$driver_data = array("driver_location"=>$driver_location,
										"category"=>MongoID($category),
										"driver_name"=>$driver_name,
										"password"=>md5($password),
										"verify_status"=>"Yes",
										"vehicle_maker"=>(string)$vehicle_maker,
										"vehicle_model"=>(string)$vehicle_model,
										"vehicle_model_year"=>$vehicle_model_year,
										"vehicle_number"=>(string)$vehicle_number,
										"status"=>"Active",
										"created"=>date('Y-m-d H:i:s'),
										"email"=>$email,
										"vehicle_type"=>MongoID($vehicle_type),
										"ac"=>"Yes",
										"no_of_rides"=>floatval(0),
										"availability"=>"Yes",
										"mode"=>"Available",
										"dail_code"=>(string) $this->session->userdata(APP_NAME.'otp_country_code'),
										"mobile_number"=>(string) $this->session->userdata(APP_NAME.'otp_phone_number'),
										"driver_commission"=>floatval(5),
										"address"=>$addressArr,
								);			
			#echo "<pre>"; print_r($driver_data); die;
			$this->driver_model->simple_insert(DRIVERS, $driver_data);
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
			
			redirect('v6/app/driver/signup/success');
		} else {
			$this->setErrorMessage('error', 'Some of the fields are missing', 'dash_some_fields_missing');
            redirect('app/driver/signup');
		}
    }
	

}

/* End of file app_driver.php */
/* Location: ./application/controllers/site/app_driver.php */