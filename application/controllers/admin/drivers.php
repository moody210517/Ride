<?php if (!defined('BASEPATH')) exit('No direct script access allowed');
/**
*
*	Drivers
* 
*	@package		CI
*	@subpackage		Controller
*	@author			Casperon
*
**/
class Drivers extends MY_Controller {

    function __construct() {
        parent::__construct();
        $this->load->helper(array('cookie', 'date', 'form'));
        $this->load->library(array('encrypt', 'form_validation'));
        $this->load->model(array('driver_model','mail_model'));

        if ($this->checkPrivileges('driver', $this->privStatus) == FALSE) {
            redirect(ADMIN_ENC_URL);
        }

        $c_fun = $this->uri->segment(3);
        $restricted_function = array('delete_driver', 'change_driver_status_global');
        if (in_array($c_fun, $restricted_function) && $this->data['isDemo'] === TRUE) {
            $this->setErrorMessage('error', 'You are in demo version. you can\'t do this action.','admin_template_demo_version');
            redirect($_SERVER['HTTP_REFERER']);
            die;
        }
    }
	
	/**
	*
	* Initiate to display the driver dashboard page
	* 	
	* @return http request to the driver dashboard page
	*	
	**/	
	public function index() {
        if ($this->checkLogin('A') == '') {
            redirect(ADMIN_ENC_URL);
        } else {
            redirect(ADMIN_ENC_URL.'/drivers/display_driver_dashboard');
        }
    }
    
	/**
	*
	* Display the driver dashboard page
	* 	
	* @return HTML to show driver dashboard page
	*	
	**/	
    public function display_driver_dashboard() {
        if ($this->checkLogin('A') == '') {
            redirect(ADMIN_ENC_URL);
        } else {
		    if ($this->lang->line('admin_drivers_drivers_dashboard') != '') 
			$this->data['heading']= stripslashes($this->lang->line('admin_drivers_drivers_dashboard')); 
		    else  $this->data['heading'] = 'Drivers Dashboard';	
			
            $this->data['totalDrivers'] =$totalDrivers=$this->driver_model->get_all_counts(DRIVERS, array());
			
            $this->data['activeDrivers'] = $this->driver_model->get_all_counts(DRIVERS, array('status' => 'Active'));
            $this->data['inactiveDrivers'] = $this->driver_model->get_all_counts(DRIVERS, array('status' => 'Inactive'));
			
            $this->data['verifiedDrivers'] = $this->driver_model->get_all_counts(DRIVERS, array('verify_status' => 'Yes'));

            $this->data['unverifiedDrivers'] = $this->data['totalDrivers'] - $this->data['verifiedDrivers'];
			
			$catgeorywise_count=$this->driver_model->get_drivers_categorywise();
			$cat_count_array=array();
			if(!empty($catgeorywise_count['result'])) {
				foreach($catgeorywise_count['result'] as $data) {
					$cat_count_array[(string)$data['_id']]=$data['drivercount'];
				}
			}
			
			$category_driver=array();
			$categoryList= $this->driver_model->get_all_details(CATEGORY, array());
			$count_category_wise=0;
			$langCode = $this->data['langCode'];
			if ($this->lang->line('admin_graph_category') != '') $CategoryLbl = stripslashes($this->lang->line('admin_graph_category')); else $CategoryLbl = 'Category';
			foreach($categoryList->result() as $data) {
			
				$category_name = $data->name;
				if(isset($data->name_languages[$langCode ]) && $data->name_languages[$langCode ] != '') $category_name = $data->name_languages[$langCode ];
			
			   if(isset($cat_count_array[(string)$data->_id])) {
					$count_category_wise+=$cat_count_array[(string)$data->_id];
					$category_driver[]=array($CategoryLbl=>$category_name,'Drivers'=>$cat_count_array[(string)$data->_id]);
			   } else {
			        $category_driver[]=array($CategoryLbl=>$category_name,'Drivers'=>0);
			   }
			}
			$unknown_catgory=$totalDrivers-$count_category_wise;
			
			 if ($this->lang->line('admin_other_category') != '') 
			$othersCat = stripslashes($this->lang->line('admin_other_category')); 
		    else  $othersCat= 'Others';	
			
			
			if($unknown_catgory >0) {
			 $category_driver[]=array($CategoryLbl=> $othersCat,'Drivers'=>$unknown_catgory);
			}
			$this->data['category_driver']=$category_driver;
			
			$locationwise_count=$this->driver_model->get_drivers_locationwise();
			$loc_count_array=array();
			if(!empty($locationwise_count['result'])) {
				foreach($locationwise_count['result'] as $data) {
					$loc_count_array[(string)$data['_id']]=$data['drivercount'];
				}
			}
			
			$location_driver=array();
			$LocationList= $this->driver_model->get_all_details(LOCATIONS, array(),array('_id','city'));
			$count_location_wise=0;
			 
			if ($this->lang->line('admin_graph_location') != '') $LocationKey = stripslashes($this->lang->line('admin_graph_location')); else $LocationKey = 'Location';
			foreach($LocationList->result() as $data) {
			   if(isset($loc_count_array[(string)$data->_id])) {
					$count_location_wise+=$loc_count_array[(string)$data->_id];
					$location_driver[]=array($LocationKey=>$data->city,'Drivers'=>$loc_count_array[(string)$data->_id]);
			   } else {
			        $location_driver[]=array($LocationKey=>$data->city,'Drivers'=>0);
			   }
			}
			$unknown_location=$totalDrivers-$count_location_wise;
			
			if($unknown_location >0) {
				$location_driver[]=array($LocationKey=>'Other','Drivers'=>$unknown_location);
			}
			$this->data['location_driver']=$location_driver;
			$top_driver=$this->driver_model->top_review_driver();
			$bottom_driver=$this->driver_model->bottom_review_driver();
			$top_revenue=$this->driver_model->get_top_revenue();
			$top_rides=$this->driver_model->get_top_rides();
			$this->data['top_driver']=$top_driver;
			$this->data['bottom_driver']=$bottom_driver;
			$this->data['top_revenue']=$top_revenue;
			$this->data['top_rides']=$top_rides;
            $selectedFileds = array('driver_name', 'email', 'image', 'status');
            $this->data['recentdriversList'] = $this->driver_model->get_selected_fields(DRIVERS, array(), $selectedFileds, array('_id' => 'DESC'), 3, 0);
			
            $this->load->view(ADMIN_ENC_URL.'/drivers/display_drivers_dashboard', $this->data);
        }
    }

	/**
	* 
	* Display the list of drivers
	*
	* @return HTML to show the list of drivers
	*
	**/	
    public function display_drivers_list() {
        if ($this->checkLogin('A') == '') {
            $this->setErrorMessage('error', 'You must login first','admin_driver_login_first');
            redirect(ADMIN_ENC_URL);
        }
		if ($this->lang->line('driver_disp_driver_list') != '') 
		$this->data['heading']= stripslashes($this->lang->line('driver_disp_driver_list')); 
		else  $this->data['heading'] = 'Display Drivers List';	
		$sortArr  = array('created' => 'DESC');
		$sortby = '';
		if (isset($_GET['sortby'])) {
			$this->data['filter'] = 'filter';
			$sortby = $_GET['sortby'];
			if($sortby=="doj_asc"){
				$sortArr  = array('created' => 'ASC');
			}
			if($sortby=="doj_desc"){
				$sortArr  = array('created' => 'DESC');
			}
			if($sortby=="rides_asc"){
				$sortArr  = array('no_of_rides' => 'ASC');
			}
			if($sortby=="rides_desc"){
				$sortArr  = array('no_of_rides' => 'DESC');
			}
		}
		$this->data['sortby'] = $sortby;
		
        $filterArr = array();
		$filterCondition = array();
            if ((isset($_GET['type']) && $_GET['type'] != '') && ((isset($_GET['value']) && $_GET['value'] != '') || (isset($_GET['vehicle_category']) && $_GET['vehicle_category']) || (isset($_GET['locations_id']) && $_GET['locations_id'] != ''))) {
            if (isset($_GET['type']) && $_GET['type'] != '') {
                $this->data['type'] = $_GET['type'];
            }
            if (isset($_GET['value']) && $_GET['value'] != '') {
                $this->data['value'] = $_GET['value'];
                $filter_val = $this->data['value'];
            }
            $this->data['filter'] = 'filter';
            $filterCondition = array();
            if($_GET['type'] == 'vehicle_type'){
                $vehicle_category = trim($_GET['vehicle_category']);
                $categoryVal=$this->user_model->get_all_details(CATEGORY,'','','','',array('name'=>$vehicle_category));
                $filterCondition = array('category' => $categoryVal->row()->_id);
            } else if($_GET['type'] == 'driver_location') {
                $locations_id = $_GET['locations_id'];
                $filterCondition = array('driver_location' => $locations_id);
            } else if($_GET['type'] == 'mobile_number') {
                $filterArr = array($this->data['type'] => $filter_val,'dail_code' => $_GET['country']);
            }else{ 
                $filterArr = array($this->data['type'] => $filter_val);
            }                 
        }
        
        $davail = $this->input->get('davail');
        if($davail == 'on'){
            $filterCondition['availability'] = 'Yes';
        } else if($davail == 'off'){
            $filterCondition['availability'] = 'No';
        }
        
        $dverify = $this->input->get('dverify');
        if($dverify == 'verified'){
            $filterCondition['verify_status'] = 'Yes';
        } else if($dverify == 'unverified'){
            $filterCondition['verify_status'] = 'No';
        }
		
		$dmode = $this->input->get('dmode');
        if($dmode == 'available'){
            $filterCondition['mode'] = 'Available';
        } else if($dmode == 'booked'){
            $filterCondition['mode'] = 'Booked';
        }
        
        $dstatus = $this->input->get('dstatus');
        if($dstatus == 'active'){
            $filterCondition['status'] = 'Active';
        } else if($dstatus == 'inactive'){
			$filterCondition['status'] = 'Inactive';
		}
        //echo"<pre>";print_r($filterCondition);die;
        $date_to = ''; $date_from = '';
        if($this->input->get('datefrom') != ''){
            $date_from = date('Y-m-d 00:00:00',strtotime($this->input->get('datefrom')));
        }
        if($this->input->get('dateto') != ''){
            $date_to = date('Y-m-d 23:59:59',strtotime($this->input->get('dateto')));
        }
        
		if($date_from != '' &&  $date_to != ''){
			$filterCondition['created']  =  array('$gte' => $date_from,
												   '$lte' => $date_to
											 );
		}
		
		$driversCount = $this->user_model->get_all_counts(DRIVERS, array(),$filterArr);
        if ($driversCount > 1000) {
            $searchPerPage = 500;
            $paginationNo = $this->uri->segment(4);
            if ($paginationNo == '') {
                $paginationNo = 0;
            } else {
                $paginationNo = $paginationNo;
            }

            $this->data['driversList'] = $this->user_model->get_all_details(DRIVERS, $filterCondition, $sortArr, $searchPerPage, $paginationNo,$filterArr);

            $searchbaseUrl = ADMIN_ENC_URL.'/drivers/display_drivers_list/'; 
            $config['num_links'] = 3;
            $config['display_pages'] = TRUE;
            $config['base_url'] = $searchbaseUrl;
            $config['total_rows'] = $driversCount;
            $config["per_page"] = $searchPerPage;
            $config["uri_segment"] = 4;
            $config['first_link'] = '';
            $config['last_link'] = '';
            $config['full_tag_open'] = '<ul class="tsc_pagination tsc_paginationA tsc_paginationA01">';
            $config['full_tag_close'] = '</ul>';
            if ($this->lang->line('pagination_prev_lbl') != '') $config['prev_link'] =stripslashes($this->lang->line('pagination_prev_lbl'));  else  $config['prev_link'] ='Prev';
            $config['prev_tag_open'] = '<li>';
            $config['prev_tag_close'] = '</li>';
            if ($this->lang->line('pagination_next_lbl') != '') $config['next_link'] =stripslashes($this->lang->line('pagination_next_lbl'));  else  $config['next_link'] ='Next';
            $config['next_tag_open'] = '<li>';
            $config['next_tag_close'] = '</li>';
            $config['cur_tag_open'] = '<li class="current"><a href="javascript:void(0);" style="cursor:default;">';
            $config['cur_tag_close'] = '</a></li>';
            $config['num_tag_open'] = '<li>';
            $config['num_tag_close'] = '</li>';
            $config['first_tag_open'] = '<li>';
            $config['first_tag_close'] = '</li>';
            $config['last_tag_open'] = '<li>';
            $config['last_tag_close'] = '</li>';
            if ($this->lang->line('pagination_first_lbl') != '') $config['first_link'] =stripslashes($this->lang->line('pagination_first_lbl'));  else  $config['first_link'] ='First';
            if ($this->lang->line('pagination_last_lbl') != '') $config['last_link'] = stripslashes($this->lang->line('pagination_last_lbl'));  else  $config['last_link'] ='Last';
            $this->pagination->initialize($config);
            $paginationLink = $this->pagination->create_links();
            $this->data['paginationLink'] = $paginationLink;
        } else { 
            $this->data['paginationLink'] = '';
            $condition = array();
          
            $this->data['driversList'] = $driversList = $this->driver_model->get_all_details(DRIVERS,  $filterCondition, $sortArr, '', '', $filterArr);  
        }
		
		$cabCats = $this->driver_model->get_selected_fields(CATEGORY, array(), array('_id', 'name','name_languages'))->result();
        $cabsTypeArr = array();
		$langCode = $this->data['langCode'];
        foreach ($cabCats as $cab) {
            $cabId = (string) $cab->_id;
			$category_name = $cab->name;
			if(isset($cab->name_languages[$langCode ]) && $cab->name_languages[$langCode ] != '') $category_name = $cab->name_languages[$langCode ];
			
            $cabsTypeArr[$cabId] = $cab;
			$cabsTypeArr[$cabId]->name = $category_name;
        }
        $this->data['cabCats'] = $cabsTypeArr;
        $this->data['locationsList'] = $this->driver_model->get_selected_fields(LOCATIONS, array('status' => 'Active'),array('city','city_name','_id'),array('city_name' => 'ASC'));
        
        $CabMakers = $this->data['brandList'] = $this->driver_model->get_all_details(BRAND, array('status' => 'Active'), array('brand_name' => 'ASC'))->result();
       $cabMaker = array();
       $langCode = $this->data['langCode'];
       foreach ($CabMakers as $maker) {
            $cabmakeId = (string) $maker->_id;
			$maker_name = $maker->brand_name;
			if(isset($maker->name_languages[$langCode ]) && $maker->name_languages[$langCode ] != '') $maker_name = $maker->name_languages[$langCode ];
			
            $cabMaker[$cabmakeId] = $maker;
			$cabMaker[$cabmakeId]->brand_name = $maker_name;
        }
        $this->data['brand'] = $cabMaker;
       
        $CabModels = $this->data['modelList'] = $this->driver_model->get_all_details(MODELS, array('status' => 'Active'), array('name' => 'ASC'))->result();
        $cabModel = array();
       $langCode = $this->data['langCode'];
       foreach ($CabModels as $model) {
            $cabmodelId = (string) $model->_id;
			$model_name = $model->name;
			if(isset($model->name_languages[$langCode ]) && $model->name_languages[$langCode ] != '') $model_name = $model->name_languages[$langCode ];
			
            $cabModel[$cabmodelId] = $model;
			$cabModel[$cabmodelId]->name = $model_name;
        }
        $this->data['model'] = $cabModel;
         
        if(isset($_GET['export']) && ($_GET['export'] == 'excel' || $_GET['export'] == 'all') && $driversList->num_rows() > 0){  
            $this->load->helper('export_helper');
            export_drivers_list($driversList->result(),$this->data);
        }		
        $this->load->view(ADMIN_ENC_URL.'/drivers/display_drivers_list', $this->data);
    }







    public function display_unregister_drivers_list() {
        if ($this->checkLogin('A') == '') {
            $this->setErrorMessage('error', 'You must login first','admin_driver_login_first');
            redirect(ADMIN_ENC_URL);
        }
        if ($this->lang->line('admin_display_unregistered_drivers') != '') 
        $this->data['heading']= stripslashes($this->lang->line('admin_display_unregistered_drivers')); 
        else  $this->data['heading'] = 'Display Unregistered Drivers';  
        $sortArr  = array('created' => 'DESC');
        $sortby = '';
        if (isset($_GET['sortby'])) {
            $this->data['filter'] = 'filter';
            $sortby = $_GET['sortby'];
            if($sortby=="doj_asc"){
                $sortArr  = array('created' => 'ASC');
            }
            if($sortby=="doj_desc"){
                $sortArr  = array('created' => 'DESC');
            }
            if($sortby=="rides_asc"){
                $sortArr  = array('no_of_rides' => 'ASC');
            }
            if($sortby=="rides_desc"){
                $sortArr  = array('no_of_rides' => 'DESC');
            }
        }
        $this->data['sortby'] = $sortby;
        
        $filterArr = array();
        $filterCondition = array();
            if ((isset($_GET['type']) && $_GET['type'] != '') && ((isset($_GET['value']) && $_GET['value'] != '') || (isset($_GET['vehicle_category']) && $_GET['vehicle_category']) || (isset($_GET['locations_id']) && $_GET['locations_id'] != ''))) {
            if (isset($_GET['type']) && $_GET['type'] != '') {
                $this->data['type'] = $_GET['type'];
            }
            if (isset($_GET['value']) && $_GET['value'] != '') {
                $this->data['value'] = $_GET['value'];
                $filter_val = $this->data['value'];
				
            }
            $this->data['filter'] = 'filter';
            $filterCondition = array();
            if($_GET['type'] == 'vehicle_type'){
                $vehicle_category = trim($_GET['vehicle_category']);
                $categoryVal=$this->user_model->get_all_details(CATEGORY,'','','','',array('name'=>$vehicle_category));
                $filterCondition = array('category' => $categoryVal->row()->_id);
            } else if($_GET['type'] == 'driver_location') {
                $locations_id = $_GET['locations_id'];
                $filterCondition = array('driver_location' => $locations_id);
            } 
			
			else if($_GET['type'] == 'driver_name') {
                $name = $_GET['value'];
                $filterArr  = array('driver_name' => $name);
				
            } 
			else if($_GET['type'] == 'mobile_number') {
              
              $filterArr = array($this->data['type'] => $filter_val,'dail_code' => $_GET['country']);
           
            }else{ 
                $filterArr = array($this->data['type'] => $filter_val);
            }                 
        }
        
        $driversCount = $this->user_model->get_all_counts(TEMP_DRIVERS, array(),$filterArr);
        if ($driversCount > 1000) {
            $searchPerPage = 500;
            $paginationNo = $this->uri->segment(4);
            if ($paginationNo == '') {
                $paginationNo = 0;
            } else {
                $paginationNo = $paginationNo;
            }
            $this->data['driversList'] = $this->user_model->get_all_details(TEMP_DRIVERS, $filterCondition, $sortArr, $searchPerPage, $paginationNo,$filterArr);
//echo'<pre>';print_r( $this->data['driversList']);die;
            $searchbaseUrl = ADMIN_ENC_URL.'/drivers/display_unregister_drivers_list/'; 
            $config['num_links'] = 3;
            $config['display_pages'] = TRUE;
            $config['base_url'] = $searchbaseUrl;
            $config['total_rows'] = $driversCount;
            $config["per_page"] = $searchPerPage;
            $config["uri_segment"] = 4;
            $config['first_link'] = '';
            $config['last_link'] = '';
            $config['full_tag_open'] = '<ul class="tsc_pagination tsc_paginationA tsc_paginationA01">';
            $config['full_tag_close'] = '</ul>';
            if ($this->lang->line('pagination_prev_lbl') != '') $config['prev_link'] =stripslashes($this->lang->line('pagination_prev_lbl'));  else  $config['prev_link'] ='Prev';
            $config['prev_tag_open'] = '<li>';
            $config['prev_tag_close'] = '</li>';
            if ($this->lang->line('pagination_next_lbl') != '') $config['next_link'] =stripslashes($this->lang->line('pagination_next_lbl'));  else  $config['next_link'] ='Next';
            $config['next_tag_open'] = '<li>';
            $config['next_tag_close'] = '</li>';
            $config['cur_tag_open'] = '<li class="current"><a href="javascript:void(0);" style="cursor:default;">';
            $config['cur_tag_close'] = '</a></li>';
            $config['num_tag_open'] = '<li>';
            $config['num_tag_close'] = '</li>';
            $config['first_tag_open'] = '<li>';
            $config['first_tag_close'] = '</li>';
            $config['last_tag_open'] = '<li>';
            $config['last_tag_close'] = '</li>';
            if ($this->lang->line('pagination_first_lbl') != '') $config['first_link'] =stripslashes($this->lang->line('pagination_first_lbl'));  else  $config['first_link'] ='First';
            if ($this->lang->line('pagination_last_lbl') != '') $config['last_link'] = stripslashes($this->lang->line('pagination_last_lbl'));  else  $config['last_link'] ='Last';
            $this->pagination->initialize($config);
            $paginationLink = $this->pagination->create_links();
            $this->data['paginationLink'] = $paginationLink;
        } else {
            $this->data['paginationLink'] = '';
            $condition = array();
          //echo'<pre>';print_r($filterArr);die;
            $this->data['driversList'] = $this->driver_model->get_all_details(TEMP_DRIVERS,$filterCondition, $sortArr, '', '', $filterArr);//echo'<pre>';print_r($this->data['driversList']);die;
        }
        
        $cabCats = $this->driver_model->get_selected_fields(CATEGORY, array(), array('_id', 'name','name_languages'))->result();
        $cabsTypeArr = array();
        $langCode = $this->data['langCode'];
        foreach ($cabCats as $cab) {
            $cabId = (string) $cab->_id;
            $category_name = $cab->name;
            if(isset($cab->name_languages[$langCode ]) && $cab->name_languages[$langCode ] != '') $category_name = $cab->name_languages[$langCode ];
            
            $cabsTypeArr[$cabId] = $cab;
            $cabsTypeArr[$cabId]->name = $category_name;
        }
        $this->data['cabCats'] = $cabsTypeArr;
                
                $this->data['locationsList'] = $this->driver_model->get_selected_fields(LOCATIONS, array('status' => 'Active'),array('city','_id'));  
//echo'dfdfdfd';die;				
        $this->load->view(ADMIN_ENC_URL.'/drivers/display_unregistered_drivers', $this->data);
    }
	
	
	/**
	* 
	* Display the particular unregister driver in formation
	* 
	* @param $driverId unregisterDriver MongoDB\BSON\ObjectId
	* @return HTML to show the unregisterdriver information in view driver page
 	*
	**/	
    public function view_unregisterdriver() {
        if ($this->checkLogin('A') == '') {
            $this->setErrorMessage('error', 'You must login first','admin_driver_login_first');
            redirect(ADMIN_ENC_URL);
        }
        $driverId = $this->uri->segment(4);
		if ($this->lang->line('driver_view_details') != '') 
		$this->data['heading']= stripslashes($this->lang->line('driver_view_details')); 
		else  $this->data['heading'] = 'View Driver Details';
        $condition = array('_id' => MongoID($driverId));
        $this->data['driver_details'] = $driver_details = $this->driver_model->get_all_details(TEMP_DRIVERS, $condition);

        if ($driver_details->num_rows() == 0) {
            $this->setErrorMessage('error', 'No records found','admin_driver_no_records_found');
            redirect(ADMIN_ENC_URL.'/drivers/display_unregister_drivers_list');
        }
		
			// $veh_condition = array('_id' => MongoID($driver_details->row()->vehicle_type));
			// $this->data['vehicle_types'] = $vehicle_types = $this->driver_model->get_all_details(VEHICLES, $veh_condition);
			
			$cat_condition = array();
			if(isset($driver_details->row()->category) && $driver_details->row()->category != '') {
				$cat_condition = array('_id' => MongoID($driver_details->row()->category));
			}
			$this->data['driver_category'] = $driver_category = $this->driver_model->get_all_details(CATEGORY, $cat_condition);

			// $maker_condition = array('_id' => MongoID($driver_details->row()->vehicle_maker));
			// $this->data['vehicle_maker'] = $vehicle_maker = $this->driver_model->get_all_details(BRAND, $maker_condition);

			// $vehicle_model_model = array('_id' => MongoID($driver_details->row()->vehicle_model));
			// $this->data['vehicle_model'] = $vehicle_model = $this->driver_model->get_all_details(MODELS, $vehicle_model_model);
        $this->load->view(ADMIN_ENC_URL.'/drivers/view_unregistered_drivers', $this->data);
    }
	
	/*send email to unregister_driver*/
	  public function send_email_to_unregister_drivers() 
	{
		 $driver_id=$this->uri->segment(4);
		
		 $mail=$this->mail_model->send_unregister_driver_register_confirmation_mail($driver_id);
		 
		 $this->setErrorMessage('success', 'Email Send Successfully','Email_send_successfully');
		 redirect(ADMIN_ENC_URL.'/drivers/display_unregister_drivers_list');
	}
	
	  /**
	* 
	* Delete the record for particular driver
	* 
	* @param string $driver_id  Driver id MongoDB\BSON\ObjectId
	* @redirect HTML to show the drivers lists page
	*
	**/
    public function delete_unregiser_driver() {
        if ($this->checkLogin('A') == '') {
            redirect(ADMIN_ENC_URL);
        } else {
            $promo_id = $this->uri->segment(4, 0);
            $condition = array('_id' => MongoID($promo_id));
            $this->driver_model->commonDelete(TEMP_DRIVERS, $condition);
            $this->setErrorMessage('success', 'Driver deleted successfully','admin_driver_deleted_changed');
            redirect(ADMIN_ENC_URL.'/drivers/display_unregister_drivers_list');
        }
    }

	/**
	* 
	* Display the add driver form for create a new driver
	*
	* @return HTML to show Add driver form
	*
	**/	
    public function add_driver_form() {
        if ($this->checkLogin('A') == '') {
            $this->setErrorMessage('error', 'You must login first','admin_driver_login_first');
            redirect(ADMIN_ENC_URL);
        }
		if ($this->lang->line('driver_add_new_driver') != '') 
		$this->data['heading']= stripslashes($this->lang->line('driver_add_new_driver')); 
		else  $this->data['heading'] = 'Add New Driver';	
        $this->data['vehicle_types'] = $this->driver_model->get_all_details(VEHICLES, array('status' => 'Active'),array('vehicle_type' => 'ASC'));
        
		$this->data['docx_list'] = $this->driver_model->get_all_details(DOCUMENTS, array('status' => 'Active'));
        $this->data['categoryList'] = $this->driver_model->get_all_details(CATEGORY, array('status' => 'Active'), array('name' => 'ASC'));
        $this->data['brandList'] = $this->driver_model->get_all_details(BRAND, array('status' => 'Active'), array('brand_name' => 'ASC'));
        $this->data['modelList'] = $this->driver_model->get_all_details(MODELS, array('status' => 'Active'), array('name' => 'ASC'));
        $this->data['locationList'] = $this->driver_model->get_all_details(LOCATIONS, array('status' => 'Active'), array('city' => 'ASC'));
        $this->load->view(ADMIN_ENC_URL.'/drivers/add_driver', $this->data);
    }

 	
	/**
	* 
	* Display the edit driver form in admin panel
	*
	* @param string $driver_id Driver MongoDB\BSON\ObjectId
	* @return HTML to show edit the driver form
	*
	**/	
    public function edit_driver_form() {	
        if ($this->checkLogin('A') == '') {
            $this->setErrorMessage('error', 'You must login first','admin_driver_login_first');
            redirect(ADMIN_ENC_URL);
        }
        $driver_id = $this->uri->segment(4);
								 if ($this->lang->line('dash_edit_driver') != '') 
					 $this->data['heading']= stripslashes($this->lang->line('dash_edit_driver')); 
					else  $this->data['heading'] = 'Edit Driver';
        $condition = array('_id' => MongoID($driver_id));
        $this->data['driver_details'] = $driver_details = $this->driver_model->get_all_details(DRIVERS, $condition);
        if ($driver_details->num_rows() == 0) {
            $this->setErrorMessage('error', 'No record found for this driver','admin_driver_no_record_found');
            redirect(ADMIN_ENC_URL.'/drivers/display_drivers_list');
        }
        $this->data['vehicle_types'] = $this->driver_model->get_all_details(VEHICLES, array('status' => 'Active'),array('vehicle_type' => 'ASC'));
        $this->data['docx_list'] = $this->driver_model->get_all_details(DOCUMENTS, array('status' => 'Active'));
        $this->data['categoryList'] = $this->driver_model->get_all_details(CATEGORY, array('status' => 'Active'), array('name' => 'ASC'));
        $this->data['brandList'] = $this->driver_model->get_all_details(BRAND, array('status' => 'Active'), array('brand_name' => 'ASC'));
        $this->data['modelList'] = $this->driver_model->get_all_details(MODELS, array('status' => 'Active'), array('name' => 'ASC'));
        $this->data['locationList'] = $this->driver_model->get_all_details(LOCATIONS, array('status' => 'Active'), array('city' => 'ASC'));
        $this->load->view(ADMIN_ENC_URL.'/drivers/edit_driver', $this->data);
    }
		
	/**
	* 
	* To add or update the driver details and remove the document from temporary folder
	* 
	* @param string $driver_id Template id MongoDB\BSON\ObjectId
	* @param string $password  Password encrypt formate
	* @param string $vehicle_type vehicle Id MongoDB\BSON\ObjectId
	* @param string $status driver status Yes/No 
	* @param string $ac Ac Yes/No 
	* @param string $dail_code Dial code numeric 
	* @param string $mobile_number  Mobile number numeric
	* @param string $category  category id MongoDB\BSON\ObjectId
	* @param string $driver_docx  Driver document Array 
	* @param string $address  driver Address 
	* @param string $county  driver country
	* @param string $state  driver State
	* @param string $city  driver City
	* @param string $postal_code driver City
	* @return HTTP redirect to show the list of drivers
	*
	**/	
    public function insertEdit_driver() {
        if ($this->checkLogin('A') == '') {
            $this->setErrorMessage('error', 'You must login first','admin_driver_login_first');
            redirect(ADMIN_ENC_URL);
        }
        $driver_id = $this->input->post('driver_id');

        $dir = getcwd() .DIRECTORY_SEPARATOR."drivers_documents_temp"; 
        $interval = strtotime('-24 hours'); 
        foreach (glob($dir . "*.*") as $file) {
            if (filemtime($file) <= $interval) {
                unlink($file);
            }
        }

		if ($this->input->post('email') == '') {
            $this->setErrorMessage('error', 'Some of the fields are missing','admin_driver_field_missing');
			echo "<script>window.history.go(-1);</script>";exit;
        }
		$email = strtolower($this->input->post('email'));

        if ($driver_id == '') {
            $checkEmail = $this->driver_model->check_driver_exist(array('email' => $this->input->post('email')));
            if ($checkEmail->num_rows() >= 1) {
                $this->setErrorMessage('error', 'This email already exist, please register with different email address.','admin_driver_register_different_email');
				echo "<script>window.history.go(-1);</script>";exit;
            }
        }
		
		$old_number = '';
		$mobile_number = $this->input->post('mobile_number');
		if($driver_id != ''){
			$checkDriver = $this->driver_model->get_selected_fields(DRIVERS,array('_id'=>MongoID($driver_id)),array('_id','mobile_number'));
			$old_number = $checkDriver->row()->mobile_number;
		}
		if($old_number  != $mobile_number){
			$checkMobile = $this->driver_model->get_selected_fields(DRIVERS,array('mobile_number'=>$mobile_number),array('_id','mobile_number'));
			if ($checkMobile->num_rows() >= 1) {
				$this->setErrorMessage('error', 'This mobile number already exist, please register with different mobile number.','admin_driver_register_mobile_number_exist');
				echo "<script>window.history.go(-1);</script>";exit;
			}
		}
		
		if ($this->input->post('status') == 'on') {
            $status = 'Active';
        } else {
            $status = 'Inactive';
        }
        if ($this->input->post('ac') == 'on') {
            $ac = 'Yes';
        } else {
            $ac = 'No';
        }
		
		$additional_categories = array();
		$multi_car_status = 'OFF';
		if($this->input->post('multi_car_status') == 'ON'){
			$multi_car_status = 'ON';
			$additional_categories = $this->input->post('additional_category');
		}
		$additional_categories = array_filter($additional_categories);
		
        $vehicle_number = $this->input->post('vehicle_number');
        $excludeArr = array("driver_id", "confirm_password", "new_password", "address", "county", "state", "city", "postal_code", "driver_docx", "driver_docx_expiry", "vehicle_docx", "vehicle_docx_expiry", "vehicle_type", "mobile_number", "dail_code", 'ac', "email","vehicle_number","additional_category","multi_car_status");
		$addressArr['address'] = array('address' => $this->input->post('address'),
            'county' => $this->input->post('county'),
            'state' => $this->input->post('state'),
            'city' => $this->input->post('city'),
            'postal_code' => $this->input->post('postal_code')
        );

        $image_data = array();

        if ($_FILES['thumbnail']['name'] != '') {
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
		
        $documents = array();
        $dr_documentArr = $this->input->post('driver_docx');
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


        $veh_documentArr = $this->input->post('vehicle_docx');
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

        $driver_data = array('created' => date('Y-m-d H:i:s'),
            'password' => md5($this->input->post('password')),
            'email' => $email,
            'vehicle_type' => MongoID($this->input->post('vehicle_type')),
            'status' => $status,
            'ac' => $ac,
            'no_of_rides' => 0,
            'availability' => 'No',
            'mode' => 'Available',
            'dail_code' => (string) $this->input->post('dail_code'),
            'vehicle_number' => (string) $vehicle_number,
            'mobile_number' => (string) $this->input->post('mobile_number'),
            'category' => MongoID($this->input->post('category')),
			'multi_car_status' => $multi_car_status,
			'additional_category' => $additional_categories
        );
		
		if($this->input->post('driver_commission') == ''){
			$cond=array('_id'=> MongoID($this->input->post('driver_location')));
        	$get_loc_commison = $this->driver_model->get_selected_fields(LOCATIONS,$cond,array('site_commission'));
			if(isset($get_loc_commison->row()->site_commission)){ 
				$driver_data['driver_commission'] = floatval($get_loc_commison->row()->site_commission);
			}
		}
		
        if ($driver_id != '') {
            unset($driver_data['no_of_rides']);
            unset($driver_data['availability']);
            unset($driver_data['password']);
            unset($driver_data['mode']);
            unset($driver_data['created']);
            unset($driver_data['email']);
        }

        $dataArr = array_merge($driver_data, $image_data, $addressArr, array('documents' => $documents));
        if ($driver_id == '') {
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
			
			$post_data = array('driver_id' =>  (string)$last_insert_id );
			$url = base_url().'welcome-mail';
			$this->curl->simple_post($url, $post_data);
			
			$current_date = MongoDATE(strtotime(date("Y-m-d 00:00:00")));
            $field = array('driver.hour_' . date('H') => 1, 'driver.count' => 1);
            $this->driver_model->update_stats(array('day_hour' => $current_date), $field, 1);
            
            $this->setErrorMessage('success', 'Driver added successfully','admin_driver_added_success');
        } else {
            $excludeArr[] = 'promo_code';
            $condition = array('_id' => MongoID($driver_id));
            $this->driver_model->commonInsertUpdate(DRIVERS, 'update', $excludeArr, $dataArr, $condition);
            $this->setErrorMessage('success', 'Driver details updated successfully','admin_driver_updated_success');
        }
        redirect(ADMIN_ENC_URL.'/drivers/display_drivers_list');
    }

	/**
	* 
	* To Upload the driver image 
	*
	* @param string $user_id User MongoDB\BSON\ObjectId
	* @param string $docx_name driver document name
	* @return JSON contains the driver document throw the error/success messages 
	*
	**/
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
		if ($this->lang->line('file_not_uploaded') != '') 
			$file_not_uploaded= stripslashes($this->lang->line('file_not_uploaded')); 
		else  $file_not_uploaded= 'File could not be uploaded';
		if ($this->lang->line('file_is_too_large') != '') 
			$file_is_too_large= stripslashes($this->lang->line('file_is_too_large')); 
		else  $file_is_too_large= 'File too large. File must be less than 2 megabytes.';
		if ($this->lang->line('invalid_file_type') != '') 
			$invalid_file_type= stripslashes($this->lang->line('invalid_file_type')); 
		else  $invalid_file_type= 'Invalid file type. Only PDF, JPG, GIF and PNG types are accepted.';
        if ($filetmpName != '') {
            if (in_array($file_type, $allowed)) {
                if ($_FILES[$docx_name]["size"] < $max_file_size) {
                    $uploadsdocx = move_uploaded_file($_FILES[$docx_name]["tmp_name"], $path . $filetmpName);
                    if ($uploadsdocx == true) {
                        $docResult['docx_name'] = $filetmpName;
                        $docResult['err_msg'] = 'Success';
                    } else {
                        $docResult['err_msg'] = $file_not_uploaded;
                    }
                } else {
                    $docResult['err_msg'] = $file_is_too_large;
                }
            } else {
                $docResult['err_msg'] =$invalid_file_type;
            }
        }

        echo json_encode($docResult);
    }

	/**
	* 
	* To add or update the email template fields
	* 
	* @param string $status Mode Active/InActive
	* @param string $driver_id Driver MongoDB\BSON\ObjectId
	* @redirect HTTP request to show the driver list  
	*
	**/	
    public function change_driver_vrification_status() {
        if ($this->checkLogin('A') == '') {
            redirect(ADMIN_ENC_URL);
        } else {
            $mode = $this->uri->segment(4, 0);
            $driver_id = $this->uri->segment(5, 0);
            $status = ($mode == '0') ? 'No' : 'Yes';
            $newdata = array('verify_status' => $status);
            $condition = array('_id' => MongoID($driver_id));
            $this->driver_model->update_details(DRIVERS, $newdata, $condition);
            $this->setErrorMessage('success', 'Driver Verification Status Changed Successfully','admin_driver_verification_status');
            redirect(ADMIN_ENC_URL.'/drivers/display_drivers_list');
        }
    }
	
	/**
	* 
	* To change the status
	* 
	* @param string $status driver status Active/InActive
	* @param string $driver_id User MongoDB\BSON\ObjectId
	* @return http redirect to show the drivers list
	*
	**/
    public function change_driver_status() {
        if ($this->checkLogin('A') == '') {
            redirect(ADMIN_ENC_URL);
        } else {
            $mode = $this->uri->segment(4, 0);
            $driver_id = $this->uri->segment(5, 0);
            $status = ($mode == '0') ? 'Inactive' : 'Active';
            $newdata = array('status' => $status);
            $condition = array('_id' => MongoID($driver_id));
            $this->driver_model->update_details(DRIVERS, $newdata, $condition);
            $this->setErrorMessage('success', 'Driver Status Changed Successfully','admin_driver_status_changed');
            redirect(ADMIN_ENC_URL.'/drivers/display_drivers_list');
        }
    }

    /**
	* 
	* Delete the record for particular driver
	* 
	* @param string $driver_id  Driver id MongoDB\BSON\ObjectId
	* @redirect HTML to show the drivers lists page
	*
	**/
    public function delete_driver() {
        if ($this->checkLogin('A') == '') {
            redirect(ADMIN_ENC_URL);
        } else {
            $promo_id = $this->uri->segment(4, 0);
            $condition = array('_id' => MongoID($promo_id));
            $this->driver_model->commonDelete(DRIVERS, $condition);
            $this->setErrorMessage('success', 'Driver deleted successfully','admin_driver_deleted_changed');
            redirect(ADMIN_ENC_URL.'/drivers/display_drivers_list');
        }
    }

    /**
	* 
	* Bulk Delete of driver  
	* 
	* @return http redirect to show the drivers list page
	*
	**/
    public function change_driver_status_global() {
        if (count($_POST['checkbox_id']) > 0 && $_POST['statusMode'] != '') {
            $this->driver_model->activeInactiveCommon(DRIVERS, '_id');
            if (strtolower($_POST['statusMode']) == 'delete') {
                $this->setErrorMessage('success', 'Driver deleted successfully','admin_driver_deleted_changed');
            } else {
                $this->setErrorMessage('success', 'Driver status changed successfully','admin_driver_status_change');
            }
            redirect(ADMIN_ENC_URL.'/drivers/display_drivers_list');
        }
    }

	/**
	* 
	* Display the change password form for driver
	*
	* @param string $driverId Driver MongoDB\BSON\ObjectId
	* @return HTML to show change password form
	*
	**/	
    public function change_password_form() {
        if ($this->checkLogin('A') == '') {
            redirect(ADMIN_ENC_URL);
        }
        $driverId = $this->uri->segment(4);
			if ($this->lang->line('driver_change_password') != '') 
				$this->data['heading']= stripslashes($this->lang->line('driver_change_password')); 
			else  $this->data['heading'] = 'Change Driver Password';
        $condition = array('_id' => MongoID($driverId));
        $this->data['driver_details'] = $driver_details = $this->driver_model->get_all_details(DRIVERS, $condition);
        $this->load->view(ADMIN_ENC_URL.'/drivers/change_password', $this->data);
    }

    /**
	* To update new password for driver 
	*	 
    * @param string $driverId Driver MongoDB\BSON\ObjectId
    * @param string $new_password Driver new password in encrypted formate
    * @return HTTP redirect to show driver list page
    *
    **/
    public function change_password() {
        if ($this->checkLogin('A') == '' || $this->input->post('new_password') == '') {
            redirect(ADMIN_ENC_URL);
        }
        $password = $this->input->post('new_password');
        $driverId = $this->input->post('driver_id');
        $dataArr = array('password' => md5($this->input->post('new_password')));
        $condition = array('_id' => MongoID($driverId));
        $driver_details = $this->driver_model->update_details(DRIVERS, $dataArr, $condition);

        /*         * **  send password to driver through email **** */
        $driverinfo = $this->driver_model->get_all_details(DRIVERS, $condition);
        $this->send_driver_pwd($password, $driverinfo);

        $this->setErrorMessage('success', 'Driver password changed and sent to driver successfully','admin_driver_password_changed');
        redirect(ADMIN_ENC_URL.'/drivers/display_drivers_list');
    }

    /**
	* To update new password for driver 
	*	 
    * @param string $driverId Driver MongoDB\BSON\ObjectId
    * @param string $new_password Driver new password in encrypted formate
	*
    **/
    public function send_driver_pwd($pwd = '', $driverinfo) {
		$default_lang=$this->config->item('default_lang_code');
        $driver_name = $driverinfo->row()->driver_name;
        $newsid = '2';
        $template_values = $this->driver_model->get_email_template($newsid,$default_lang);
        $adminnewstemplateArr = array('email_title' => $this->config->item('email_title'), 'logo' => $this->config->item('logo_image'), 'footer_content' => $this->config->item('footer_content'), 'meta_title' => $this->config->item('meta_title'), 'site_contact_mail' => $this->config->item('site_contact_mail'));
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
            'to_mail_id' => $driverinfo->row()->email,
            'subject_message' => $template_values['subject'],
            'body_messages' => $message
        );
        $email_send_to_common = $this->driver_model->common_email_send($email_values);
    }

	/**
	* 
	* Display the particular driver in formation
	* 
	* @param $driverId Driver MongoDB\BSON\ObjectId
	* @return HTML to show the driver information in view driver page
 	*
	**/	
    public function view_driver() {
        if ($this->checkLogin('A') == '') {
            $this->setErrorMessage('error', 'You must login first','admin_driver_login_first');
            redirect(ADMIN_ENC_URL);
        }
        $driverId = $this->uri->segment(4);
		if ($this->lang->line('driver_view_details') != '') 
		$this->data['heading']= stripslashes($this->lang->line('driver_view_details')); 
		else  $this->data['heading'] = 'View Driver Details';
        $condition = array('_id' => MongoID($driverId));
        $this->data['driver_details'] = $driver_details = $this->driver_model->get_all_details(DRIVERS, $condition);

        if ($driver_details->num_rows() == 0) {
            $this->setErrorMessage('error', 'No records found','admin_driver_no_records_found');
            redirect(ADMIN_ENC_URL.'/drivers/display_drivers_list');
        }

        $veh_condition = array('_id' => MongoID($driver_details->row()->vehicle_type));
        $this->data['vehicle_types'] = $vehicle_types = $this->driver_model->get_all_details(VEHICLES, $veh_condition);

        $cat_condition = array('_id' => MongoID($driver_details->row()->category));
        $this->data['driver_category'] = $driver_category = $this->driver_model->get_all_details(CATEGORY, $cat_condition);

        $maker_condition = array('_id' => MongoID($driver_details->row()->vehicle_maker));
        $this->data['vehicle_maker'] = $vehicle_maker = $this->driver_model->get_all_details(BRAND, $maker_condition);

        $vehicle_model_model = array('_id' => MongoID($driver_details->row()->vehicle_model));
        $this->data['vehicle_model'] = $vehicle_model = $this->driver_model->get_all_details(MODELS, $vehicle_model_model);

        $this->load->view(ADMIN_ENC_URL.'/drivers/view_driver', $this->data);
    }

 	/**
	* 
	* To update Driver document status 
	*
	* @param string $driverId Driver MongoDB\BSON\ObjectId
	* @param string $docxId Driver Document id
	* @param string $docxType Driver Document type
	* @return string error/success message throw the edit driver document page
	*
	**/	
    public function document_verify_status_ajax() {
        if ($this->checkLogin('A') == '') {
            $this->setErrorMessage('error', 'You must login first','admin_driver_login_first');
            echo 'error';
            redirect(ADMIN_ENC_URL);
        }
        $driverId = $this->input->get('driverId');
        $docxId = $this->input->get('docxId');
        $docxType = $this->input->get('docxType');

        if ($this->input->get('docx_state') == 'Verify') {
            $docx_state = 'Yes';
        } else {
            $docx_state = 'No';
        }
        $dataArr = array("documents." . $docxType . "." . $docxId . ".verify_status" => $docx_state);
        $condition = array('_id' => MongoID($driverId));
        $this->data['driver_details'] = $driver_details = $this->driver_model->update_details(DRIVERS, $dataArr, $condition);
        echo 'Success';
    }

	/**
	* 
	* Display the banking form for driver update the banking information
	*
	* @param string $driver_id Driver MongoDB\BSON\ObjectId
	* @return HTML to show the Driver banking form   
	*
	**/	
    public function banking() {
        if ($this->checkLogin('A') == '') {
            redirect(ADMIN_ENC_URL);
        } else {
            $driver_id = $this->uri->segment(4, 0);
            $form_mode = FALSE;
		          	if ($this->lang->line('dash_add_banking_details') != '') 
		          $this->data['heading']= stripslashes($this->lang->line('dash_add_banking_details')); 
		          else  $this->data['heading'] = 'Add Banking Details';
            if ($driver_id != '') {
                $condition = array('_id' => MongoID($driver_id));
                $this->data['driver_details'] = $driver_details = $this->driver_model->get_all_details(DRIVERS, $condition);
                if ($this->data['driver_details']->num_rows() != 1) {
                    redirect(ADMIN_ENC_URL.'/drivers/display_drivers_list');
                }
                $form_mode = TRUE;
																if ($this->lang->line('dash_edit_banking_details') != '') 
		              $heading = stripslashes($this->lang->line('dash_edit_banking_details')); 
		              else  $heading = 'Edit Banking Details';
            }
            $this->data['form_mode'] = $form_mode;
            $this->data['heading'] = $heading;
            $this->load->view(ADMIN_ENC_URL.'/drivers/add_edit_banking', $this->data);
        }
    }

	/**
	* 
	* To add or update driver Banking account information
	* 
	* @param string $driver_id Driver MongoDB\BSON\ObjectId
	* @param string $acc_holder_name Driver account name
	* @param string $acc_holder_address Driver account address
	* @param string $acc_number Driver account number
	* @param string $bank_name Driver bank account name
	* @param string $branch_name Driver banking branch name
	* @param string $swift_code Driver account swift code
	* @param string $routing_number Driver account routing number
	* 
	* @return HTML redirect to show the add or update in particular driver banking page
	*
	**/	
    public function insertEditDriverBanking() {
        if ($this->checkLogin('A') == '') {
            redirect(ADMIN_ENC_URL);
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
        $this->setErrorMessage('success', 'Driver banking details updated successfully','admin_driver_banking_details_update');
        redirect(ADMIN_ENC_URL.'/drivers/banking/' . $driver_id);
    }
	
	 /**
	 *
	 * Change the driver mode from booked state to available state
	 *
     * @param string $driver_id Driver MongoDB\BSON\ObjectId
	 * @return HTTP redirect to show the list of drivers in driver list page
	 *
	 * */
    public function change_driver_mode_status() {
        if ($this->checkLogin('A') == '') {
            redirect(ADMIN_ENC_URL);
        } else {
            $driver_id = $this->uri->segment(5, 0);
		
		$dri_LastRide_satate = $this->driver_model->get_driver_last_ride_status($driver_id );
		$doAction = FALSE;
		if($dri_LastRide_satate->num_rows() == 0){
			$doAction = TRUE;
		}
		if(isset($dri_LastRide_satate->row()->ride_status) && ($dri_LastRide_satate->row()->ride_status == 'Completed' ||  $dri_LastRide_satate->row()->ride_status == 'Finished' || $dri_LastRide_satate->row()->ride_status == 'Cancelled') ){
			$doAction = TRUE;
		}
		
		if($doAction){
		      $newdata = array('mode' => 'Available');
		      $condition = array('_id' => MongoID($driver_id));
		      $this->driver_model->update_details(DRIVERS, $newdata, $condition);
		      $this->setErrorMessage('success', 'Driver Verification Status Changed Successfully','admin_driver_verification_status');
	       } else {
			    $this->setErrorMessage('error', 'Sorry! This driver is on ride,  You can not make him available right now','this_driver_on_ride_make_him_available_now');
	      }
             redirect(ADMIN_ENC_URL.'/drivers/display_drivers_list');
        }
    }
	
	
	 /**
	 *
	 * Avoid the duplicate information for driver 
	 *
	 * @param string $email Driver Email 
	 * @param string $driver_id Driver MongoDB\BSON\ObjectId 
	 * @param string $dial_code dial code  
	 * @param string $phone_number Driver phone number
	 * @return JSON contains throw error/success message
	 *
	 * */

	function check_driver_duplicate(){
		$returnArr['status'] = '0';
		$email = $this->input->post('email');
		$driver_id = (string)$this->input->post('driver_id');
		$dial_code = $this->input->post('dial_code');
		$phone_number = $this->input->post('phone_number');
		$emailCond = array('email' => $email);
		if($driver_id != ''){
			$emailCond = array('email'=>$email,'_id' =>array('$ne'=>MongoID($driver_id)));
		} 
		$chekEmail = $this->user_model->get_selected_fields(DRIVERS,$emailCond,array('_id','email'));
		$emailExist = 'No';
		if($chekEmail->num_rows() > 0){
			$emailExist = 'Yes';
		} 
		$PhoneCond = array('dail_code' => $dial_code,'mobile_number' => $phone_number);
		if($driver_id != ''){
			$PhoneCond = array('dail_code' => $dial_code,'mobile_number' => $phone_number,'_id' => array('$ne' => MongoID($driver_id)));
		}
		$chekPhone = $this->user_model->get_selected_fields(DRIVERS,$PhoneCond,array('_id'));
		$phoneExist = 'No';
		if($chekPhone->num_rows() > 0){
			$phoneExist = 'Yes';
		}
		
		
		if ($this->lang->line('admin_driver_details_already_exists') != '') 
			$response = stripslashes($this->lang->line('admin_driver_details_already_exists')); 
		else  $response = 'Driver details already exists';
		
		if($emailExist == 'No' && $phoneExist == 'No'){
			$returnArr['status'] = '1';
		} else {
			if($emailExist == 'Yes' && $phoneExist == 'Yes'){
				if ($this->lang->line('admin_driver_email_and_phone_already_exist') != '') 
					$response = stripslashes($this->lang->line('admin_driver_email_and_phone_already_exist')); 
				else  $response = 'Driver email and mobile number already exists';
			}  else if($emailExist == 'Yes'){
				if ($this->lang->line('admin_driver_email_already_exist') != '') 
					$response = stripslashes($this->lang->line('admin_driver_email_already_exist')); 
				else  $response = 'Driver email already exists';
			}  else if($phoneExist == 'Yes'){
				if ($this->lang->line('admin_driver_mobile_already_exist') != '') 
					$response = stripslashes($this->lang->line('admin_driver_mobile_already_exist')); 
				else  $response = 'Driver mobile number already exists';
			}
		}
		$returnArr['response'] = $response;
		echo json_encode($returnArr); exit;
	}
	
	public function ajax_get_additional_category_list(){
		$resArr['status'] = 'error';
		$resArr['response'] = '';  
		if($this->data['multiCategoryOption'] == 'ON'){
			$cur_cat = $this->input->post('cur_cat');
			$location_id = $this->input->post('location_id');
			$addionalVehicles = $this->driver_model->get_selected_fields(LOCATIONS, array('_id' => MongoID($location_id)),array('fare.'.$cur_cat.'.additional_category','_id'));
			$additional_vehicle_types = array();
			if(isset($addionalVehicles->row()->fare[$cur_cat]['additional_category'])){
				$additional_vehicle_types = $addionalVehicles->row()->fare[$cur_cat]['additional_category'];
			} 
			
			$addionalVehicles = $this->driver_model->get_selected_fields(CATEGORY, array('status' => 'Active'),array('name','_id'));
			
			$addionalVehiclesList = '';
			foreach($addionalVehicles->result() as $vehicles){
				$vehId = (string)$vehicles->_id;
				if(in_array($vehId,$additional_vehicle_types) && $vehId != $cur_cat){
					$addionalVehiclesList.='<option value="'.$vehId.'">'.$vehicles->name.'</option>';
				}
			}
			if($addionalVehiclesList != ''){
				$resArr['status'] = 'success';
				$resArr['response'] = $addionalVehiclesList;
			}
		}
		echo json_encode($resArr);
	}

}

/* End of file drivers.php */
/* Location: ./application/controllers/admin/drivers.php */