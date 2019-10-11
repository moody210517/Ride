<?php if (!defined('BASEPATH')) exit('No direct script access allowed');
/**
*
*	Mileage
* 
*	@package		CI
*	@subpackage		Controller
*	@author			Casperon
*
**/
class Mileage extends MY_Controller {

    function __construct() {
        parent::__construct();
        $this->load->helper(array('cookie', 'date', 'form'));
        $this->load->library(array('encrypt', 'form_validation','excel'));
        $this->load->model(array('mileage_model'));

        if ($this->checkPrivileges('mileage', $this->privStatus) == FALSE) {
            redirect(ADMIN_ENC_URL);
        }

        $c_fun = $this->uri->segment(3);
        $restricted_function = array();
        if (in_array($c_fun, $restricted_function) && $this->data['isDemo'] === TRUE) {
            $this->setErrorMessage('error', 'You are in demo version. you can\'t do this action.','admin_template_demo_version');
            redirect($_SERVER['HTTP_REFERER']);
            die;
        }
    }
		
	/**
	*
	* To redirect to mileage list page
	* 	
	* @Initiate HTML to Redirect mileage list page
	*	
	**/	
    public function index() {
        if ($this->checkLogin('A') == '') {
            redirect(ADMIN_ENC_URL);
        } else {
            redirect(ADMIN_ENC_URL.'/mileage/display_mileage_list');
        }
    }
	
	/**
	* 
	* To load the mileage list page
	*
	* @display HTML to show the mileage dashboard
	*
	**/	
    public function display_mileage_dashboard() {
		if ($this->checkLogin('A') == '') {
			$this->setErrorMessage('error', 'You must login first','admin_driver_login_first');
			redirect(ADMIN_ENC_URL);
		}
		
		if ($this->lang->line('admin_mileage_heading') != '') $this->data['heading']= stripslashes($this->lang->line('admin_mileage_heading'));  else  $this->data['heading'] = 'Mileage Dashboard';	
		
		
		$this->data['total_mileage_info'] = get_total_mileage_list();
		
		
		$this->load->view(ADMIN_ENC_URL.'/mileage/mileage_dashboard', $this->data);
	}
	
	/**
	* 
	* To show the mileage list page
	*
	* @display HTML to show the mileage list page
	*
	**/		
    public function display_mileage_list() {
		if ($this->checkLogin('A') == '') {
			$this->setErrorMessage('error', 'You must login first','admin_driver_login_first');
			redirect(ADMIN_ENC_URL);
		}
		if ($this->lang->line('admin_menu_mileage_list') != '') $this->data['heading']= stripslashes($this->lang->line('admin_menu_mileage_list'));  else  $this->data['heading'] = 'Mileage List';	
		
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

		$this->data['driversList'] = $this->mileage_model->get_all_details(DRIVERS,  $filterCondition, $sortArr, '', '', $filterArr);

		$driver_array=array();
		$driver_data=array();
		$langCode = $this->data['langCode'];
		if($this->data['driversList']->num_rows() > 0 ){
			foreach($this->data['driversList']->result() as $row){
				$categoryVal=$this->user_model->get_selected_fields(CATEGORY,array('_id'=>$row->category),array('name','email','_id','name_languages'));
				$driver_array[]=$row->_id;
				$cat_name = "";
				
				if($categoryVal->num_rows()>0) { 
					$cat_name = $categoryVal->row()->name;
					if(isset($categoryVal->row()->name_languages[$langCode ]) && $categoryVal->row()->name_languages[$langCode ] != '') $cat_name = $categoryVal->row()->name_languages[$langCode ];
				}
				
				$driver_data[(string)$row->_id]=array('name'=>$row->driver_name,'email'=>$row->email,'catgeory_name'=>$cat_name);
			}
		}


		$start_date='';
		$end_date='';
		$start_d='';
		$end_d='';
		if(isset($_GET['date_range']) && $_GET['date_range']!='') {
			$start_date = strtotime($_GET['date_range'] . ' 00:00:00');
			$start_d = $_GET['date_range'];
		}
		if(isset($_GET['dateto']) && $_GET['dateto']!='') {
			$end_date = strtotime($_GET['dateto'] . ' 23:59:59');
			$end_d = $_GET['dateto'];
		}
		$mileage_data=get_mileage_list($driver_array,$start_date,$end_date);
		$total_duration=0;
		$total_distance=0;
		$tot_free_distance=0;
		$tot_free_duration=0;
		$tot_pick_duration=0;
		$tot_pick_distance=0;
		$tot_drop_distance=0;
		$tot_drop_duration=0;
		$final_Arr=array();

		foreach($mileage_data as $key=>$data) {
			$final_Arr[$key]=array_merge($data,$driver_data[$key]);
			
			$free_duration = 00; $drop_duration = 00; $pickup_duration = 00;
			$drop_distance = 00; $pickup_distance = 00; $free_distance = 00;
			
			if(isset($data['free_duration'])){ $free_duration = $data['free_duration']; }
			if(isset($data['drop_duration'])){ $drop_duration = $data['drop_duration']; }
			if(isset($data['pickup_duration'])){ $pickup_duration = $data['pickup_duration']; }
			
			if(isset($data['drop_distance'])){ $drop_distance = $data['drop_distance']; }
			if(isset($data['pickup_distance'])){ $pickup_distance = $data['pickup_distance']; }
			if(isset($data['free_distance'])){ $free_distance = $data['free_distance']; }

			$total_duration+= $free_duration+$drop_duration+$pickup_duration;	
			$total_distance+= $drop_distance+$pickup_distance+$free_distance;

			$tot_free_distance+=$free_distance;
			$tot_pick_distance+=$pickup_distance;
			$tot_drop_distance+=$drop_distance;

			$tot_free_duration+=$free_duration;
			$tot_pick_duration+=$pickup_duration;
			$tot_drop_duration+=$drop_duration;
		}
		$this->data['total_duration']=$total_duration;
		$this->data['total_distance']=$total_distance;

		$this->data['tot_free_distance']=$tot_free_distance;
		$this->data['tot_pick_distance']=$tot_pick_distance;
		$this->data['tot_drop_distance']=$tot_drop_distance;

		$this->data['tot_free_duration']=$tot_free_duration;
		$this->data['tot_pick_duration']=$tot_pick_duration;
		$this->data['tot_drop_duration']=$tot_drop_duration;

		$cabCats = $this->mileage_model->get_selected_fields(CATEGORY, array(), array('_id', 'name','name_languages'))->result();
		$cabsTypeArr = array();
		foreach ($cabCats as $cab) {
			$cabId = (string) $cab->_id;
			$cabsTypeArr[$cabId] = $cab;
		}
		$this->data['cabCats'] = $cabsTypeArr;
		$this->data['mileage_data'] = $final_Arr;
		$this->data['start_date'] = $start_d;
		$this->data['end_date'] = $end_d;
		
		$this->data['total_mileage_info'] = get_total_mileage_list($driver_array,$start_date,$end_date);;
		
		$this->data['locationsList'] = $this->mileage_model->get_selected_fields(LOCATIONS, array('status' => 'Active'),array('city','_id'),array('city' => 'ASC'));

		$this->load->view(ADMIN_ENC_URL.'/mileage/display_mileage_list', $this->data);
    }
	
	/**
	*
	* Export mileage report
	*
	* @return .xls file
	**/	
    public function export_mileage_report() {
		if ($this->checkLogin('A') == '') {
			$this->setErrorMessage('error', 'You must login first','admin_driver_login_first');
			redirect(ADMIN_ENC_URL);
		}
		if ($this->lang->line('admin_menu_mileage_list') != '') 
			$this->data['heading']= stripslashes($this->lang->line('admin_menu_mileage_list')); 
		else  
			$this->data['heading'] = 'Mileage List';
		
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
        if (isset($_GET['type']) && (isset($_GET['value']) || isset($_GET['vehicle_category'])) && $_GET['type'] != '' && ($_GET['value'] != '' || $_GET['vehicle_category'] != '')) {
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
				$location=$this->user_model->get_all_details(LOCATIONS,'','','','',array('city'=>$_GET['value']));
				$filterArr = array($this->data['type'] => $location->row()->_id);
			} else if($_GET['type'] == 'mobile_number') {
				$filterArr = array($this->data['type'] => $filter_val,'dail_code' => $_GET['country']);
			}else{ 
				$filterArr = array($this->data['type'] => $filter_val);
			}
		}
			
		$this->data['driversList'] = $this->mileage_model->get_all_details(DRIVERS,  $filterCondition, $sortArr, '', '', $filterArr);
		$driver_array=array();
		$driver_data=array();
		if($this->data['driversList']->num_rows() > 0 ){
			foreach($this->data['driversList']->result() as $row){
				$categoryVal=$this->user_model->get_selected_fields(CATEGORY,array('_id'=>$row->category),array('name','_id'));
				
				$cat_name = '';
				if($categoryVal->num_rows() == 1){
					$cat_name = $categoryVal->row()->name;
				}
				$driver_array[]=$row->_id;
				$driver_data[(string)$row->_id]=array('name'=>$row->driver_name,'catgeory_name'=>$cat_name);
			}
		}
			
		$start_date='';
		$end_date='';
		if(isset($_GET['date_range']) && $_GET['date_range']!='') {
			$start_date = strtotime($_GET['date_range'] . ' 00:00:00');
			$start_d = $_GET['date_range'];
		}
		if(isset($_GET['dateto']) && $_GET['dateto']!='') {
			$end_date = strtotime($_GET['dateto'] . ' 23:59:59');
			$end_d = $_GET['dateto'];
		}
		$mileage_data=get_mileage_list($driver_array,$start_date,$end_date);
			
		$final_Arr=array();
		foreach($mileage_data as $key=>$data) {
			$final_Arr[$key]=array_merge($data,$driver_data[$key]);
		}
		$limit = 1000;
			
		$no_of_rows = count($final_Arr);
		$no_of_sheets = floor($no_of_rows/$limit);
		if($no_of_rows%$limit > 0){
			$no_of_sheets++;
		}
		$headers_array = array('Driver Name',
											'Category',
											'Total Distance '.$this->data['d_distance_unit_name'],
											'Total Duration [Min(s)]',
											'Free Roaming Distance '.$this->data['d_distance_unit_name'],
											'Free Roaming Duration [Min(s)]',
											'Pickup Distance '.$this->data['d_distance_unit_name'],
											'Pickup Duration [Min(s)]',
											'Drop Distance '.$this->data['d_distance_unit_name'],
											'Drop Duration [Min(s)]'
										);
		$next_limit = 0;
		for($i=0; $i<$no_of_sheets; $i++){
			$this->excel->setActiveSheetIndex($i);
			$current_limit = $next_limit;

			$headerLetter = 'A';
			foreach($headers_array as $key => $val){
				$this->excel->getActiveSheet()->setCellValue($headerLetter++."1", $val);
			}
			$this->excel->getActiveSheet()->getStyle('A1:'.$headerLetter.'1')->getFont()->setBold(true);
			$this->excel->getActiveSheet()->getStyle('A1:'.$headerLetter.'1')->getFont()->setSize(12);


			$m = $i+1;
			$next_limit = $m*$limit;
			$row = 2;
			$countflag = 0;
			$final_Arr=array_values($final_Arr);

			foreach($final_Arr as $key=>$val){
				if($key >= $current_limit && $key < $next_limit){
					$contentLetter = 'A';

					$driver_name = (string)$final_Arr[$key]['name'];
					$this->excel->getActiveSheet()->setCellValue($contentLetter.$row, $driver_name);
					$contentLetter++;

					$catgeory_name = (string)(string)$final_Arr[$key]['catgeory_name'];
					$this->excel->getActiveSheet()->setCellValue($contentLetter.$row, $catgeory_name);
					$contentLetter++;

					$total_distance = $final_Arr[$key]['free_distance']+$final_Arr[$key]['pickup_distance']+$final_Arr[$key]['drop_distance'];
					$this->excel->getActiveSheet()->setCellValue($contentLetter.$row, number_format($total_distance,2,'.',''));
					$contentLetter++;

					$total_duration = $final_Arr[$key]['free_duration']+$final_Arr[$key]['pickup_duration']+$final_Arr[$key]['drop_duration'];
					$this->excel->getActiveSheet()->setCellValue($contentLetter.$row, number_format($total_duration,2,'.',''));
					$contentLetter++;

					$free_distance = (string)(string)$final_Arr[$key]['free_distance'];
					$this->excel->getActiveSheet()->setCellValue($contentLetter.$row, number_format($free_distance,2,'.',''));
					$contentLetter++;

					$free_duration = (string)(string)$final_Arr[$key]['free_duration'];
					$this->excel->getActiveSheet()->setCellValue($contentLetter.$row, number_format($free_duration,2,'.',''));
					$contentLetter++;

					$pickup_distance = (string)(string)$final_Arr[$key]['pickup_distance'];
					$this->excel->getActiveSheet()->setCellValue($contentLetter.$row, number_format($pickup_distance,2,'.',''));
					$contentLetter++;

					$pickup_duration = (string)(string)$final_Arr[$key]['pickup_duration'];
					$this->excel->getActiveSheet()->setCellValue($contentLetter.$row, number_format($pickup_duration,2,'.',''));
					$contentLetter++;

					$drop_distance = (string)(string)$final_Arr[$key]['drop_distance'];
					$this->excel->getActiveSheet()->setCellValue($contentLetter.$row, number_format($drop_distance,2,'.',''));
					$contentLetter++;

					$drop_duration = (string)(string)$final_Arr[$key]['drop_duration'];
					$this->excel->getActiveSheet()->setCellValue($contentLetter.$row, number_format($drop_duration,2,'.',''));
					$contentLetter++;

					$countflag = $countflag +1;
					$row = $row +1;
				}
			}

			$this->excel->getActiveSheet()->setTitle('sheet'.$i);
			$this->excel->createSheet();
		}
			
		$filename='Mileage_report'.date("Y-m-d H:i:s").'.xls';
		header('Content-Type: application/vnd.ms-excel');
		header('Content-Disposition: attachment;filename="'.$filename.'"'); 
		header('Cache-Control: max-age=0');
		$objWriter = PHPExcel_IOFactory::createWriter($this->excel, 'Excel5');  
		$objWriter->save('php://output');
		exit;
    }
	
	/**
	*
	* To load the mileage detail of a particular driver
	*
	* @param string $driver_id is driver id and MongoDB\BSON\ObjectId
	* @return HTML contains the driver mileage
	**/
    public function view_driver_mileage() {
		if ($this->checkLogin('A') == '') {
			$this->setErrorMessage('error', 'You must login first','admin_driver_login_first');
			redirect(ADMIN_ENC_URL);
		}
		if ($this->lang->line('admin_menu_mileage_list') != '') 
			$this->data['heading']= stripslashes($this->lang->line('admin_menu_mileage_list')); 
		else  
			$this->data['heading'] = 'Mileage List';	
		
		
		$driver_id=$this->uri->segment(4,0);
		$start_date='';
		$end_date='';
		$start_d='';
		$end_d='';

		if(isset($_GET['date_from']) && $_GET['date_from']!='') {
			$start_date = strtotime($_GET['date_from'] . ' 00:00:00');
			$start_d =$_GET['date_from'] ;
		}
		if(isset($_GET['date_to']) && $_GET['date_to']!='') {
			$end_date = strtotime($_GET['date_to'] . ' 23:59:59');
			$end_d =$_GET['date_to'] ; 
		}

		$ride_id='';
		if(isset($_GET['ride_id']) && $_GET['ride_id']!='') {
			$ride_id=$_GET['ride_id'];
		}
		$total_duration=0;
		$total_distance=0;
		$tot_free_distance=0;
		$tot_free_duration=0;
		$tot_pick_duration=0;
		$tot_pick_distance=0;
		$tot_drop_distance=0;
		$tot_drop_duration=0;
		$mileage_data=view_mileage_list($driver_id,$start_date,$end_date,$ride_id);
		
		foreach($mileage_data['result'] as $row) {
			$total_duration+=$row['mileage_data']['duration_min'];	
			$total_distance+=$row['mileage_data']['distance'];
			if($row['mileage_data']['type']=='free-roaming'){
				$tot_free_distance+=$row['mileage_data']['distance'];
				$tot_free_duration+=$row['mileage_data']['duration_min'];
			} else if($row['mileage_data']['type']=='customer-pickup') {
				$tot_pick_distance+=$row['mileage_data']['distance'];
				$tot_pick_duration+=$row['mileage_data']['duration_min'];
			} else if($row['mileage_data']['type']=='customer-drop') {
				$tot_drop_distance+=$row['mileage_data']['distance'];
				$tot_drop_duration+=$row['mileage_data']['duration_min'];
			}
		}
		
		$this->data['total_duration']=$total_duration;
		$this->data['total_distance']=$total_distance;
		$this->data['tot_free_distance']=$tot_free_distance;
		$this->data['tot_pick_distance']=$tot_pick_distance;
		$this->data['tot_drop_distance']=$tot_drop_distance;
		$this->data['tot_free_duration']=$tot_free_duration;
		$this->data['tot_pick_duration']=$tot_pick_duration;
		$this->data['tot_drop_duration']=$tot_drop_duration;
		$this->data['mileage_data'] = $mileage_data;
		$this->data['start_date'] = $start_d;
		$this->data['end_date'] = $end_d;
		$this->data['ride_id'] = $ride_id;
		$this->data['driver_id'] = $driver_id;
		
		$driver_info = $this->mileage_model->get_selected_fields(DRIVERS,array('_id'=>MongoID($driver_id)),array('driver_name'));
		if($driver_info->num_rows()>0){
			$this->data['heading'] = $driver_info->row()->driver_name.' - '.$this->data['heading'];
		}
		

		$this->load->view(ADMIN_ENC_URL.'/mileage/view_driver_mileage', $this->data);
    }
		
	/**
	*
	* To load the mileage list
	*
	* @param string $driver_id is driver id and MongoDB\BSON\ObjectId
	**/
    public function view_driver_mileage_report() {
		if ($this->checkLogin('A') == '') {
			$this->setErrorMessage('error', 'You must login first','admin_driver_login_first');
			redirect(ADMIN_ENC_URL);
		}
		if ($this->lang->line('admin_menu_mileage_list') != '') 
			$this->data['heading']= stripslashes($this->lang->line('admin_menu_mileage_list')); 
		else  
			$this->data['heading'] = 'Mileage List';	
		
		$driver_id=$this->uri->segment(4,0);
		$start_date='';
		$end_date='';
		$start_d='';
		$end_d='';

		if(isset($_GET['date_from']) && $_GET['date_from']!='') {
			$start_date = strtotime($_GET['date_from'] . ' 00:00:00');
			$start_d =$_GET['date_from'] ;
		}
		if(isset($_GET['date_to']) && $_GET['date_to']!='') {
			$end_date = strtotime($_GET['date_to'] . ' 23:59:59');
			$end_d =$_GET['date_to'] ; 
		}

		$ride_id='';
		if(isset($_GET['ride_id']) && $_GET['ride_id']!='') {
			$ride_id=$_GET['ride_id'];
		}
		$mileage_data=view_mileage_list($driver_id,$start_date,$end_date,$ride_id);

		$limit = 1000;

		$no_of_rows = count($mileage_data['result']);
		$no_of_sheets = floor($no_of_rows/$limit);
		if($no_of_rows%$limit > 0){
			$no_of_sheets++;
		}
		$headers_array = array('S.No',
										'From Time',
										'To Time',
										'Type',
										'Duration [Min(s)]',
										'distance '.$this->data['d_distance_unit_name']
									);
		$next_limit = 0;
		for($i=0; $i<$no_of_sheets; $i++){
			$this->excel->setActiveSheetIndex($i);
			$current_limit = $next_limit;

			$headerLetter = 'A';
			foreach($headers_array as $key => $val){
				$this->excel->getActiveSheet()->setCellValue($headerLetter++."1", $val);
			}
			$this->excel->getActiveSheet()->getStyle('A1:'.$headerLetter.'1')->getFont()->setBold(true);
			$this->excel->getActiveSheet()->getStyle('A1:'.$headerLetter.'1')->getFont()->setSize(12);


			$m = $i+1;
			$next_limit = $m*$limit;
			$row = 2;
			$countflag = 0;
			$sno=1;
			foreach($mileage_data['result'] as $key=>$val){
				if($key >= $current_limit && $key < $next_limit){
					$contentLetter = 'A';

					$sno = (string)$sno;
					$this->excel->getActiveSheet()->setCellValue($contentLetter.$row, $sno);
					$contentLetter++;

					$from_time=date('d-m-Y h:i:s A',MongoEPOCH($mileage_data['result'][$key]['mileage_data']['start_time']));

					$this->excel->getActiveSheet()->setCellValue($contentLetter.$row, $from_time);
					$contentLetter++;

					$end_time=date('d-m-Y h:i:s A',MongoEPOCH($mileage_data['result'][$key]['mileage_data']['end_time']));

					$this->excel->getActiveSheet()->setCellValue($contentLetter.$row, $end_time);
					$contentLetter++;
					$ride_id='';
					if(isset($mileage_data['result'][$key]['mileage_data']['ride_id']) && $mileage_data['result'][$key]['mileage_data']['ride_id']!='') {
						$ride_id='('.$mileage_data['result'][$key]['mileage_data']['ride_id'].')';
					}

					$type = $mileage_data['result'][$key]['mileage_data']['type'].' '.$ride_id;
					$this->excel->getActiveSheet()->setCellValue($contentLetter.$row, $type);
					$contentLetter++;

					$duration_min = convertToHoursMins($mileage_data['result'][$key]['mileage_data']['duration_min']);
					$this->excel->getActiveSheet()->setCellValue($contentLetter.$row, $duration_min);
					$contentLetter++;

					$distance = (string)number_format($mileage_data['result'][$key]['mileage_data']['distance'],2,'.','').' '.$this->data['d_distance_unit'];
					
					$this->excel->getActiveSheet()->setCellValue($contentLetter.$row, $distance);
					$contentLetter++;

					$countflag = $countflag +1;
					$row = $row +1;
					$sno = $sno +1;
				}
			}	

			$this->excel->getActiveSheet()->setTitle('sheet'.$i);
			$this->excel->createSheet();
		}
			
		$filename='Mileage_report_view '.date("Y-m-d H: i:s").'.xls';
		header('Content-Type: application/vnd.ms-excel'); 
		header('Content-Disposition: attachment;filename="'.$filename.'"'); 
		header('Cache-Control: max-age=0');
		
		$objWriter = PHPExcel_IOFactory::createWriter($this->excel, 'Excel5');  
		$objWriter->save('php://output');
		exit;
    }
}

/* End of file mileage.php */
/* Location: ./application/controllers/admin/mileage.php */