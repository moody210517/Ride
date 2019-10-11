<?php if (!defined('BASEPATH'))    exit('No direct script access allowed');
/**
*
*   operator panel dashboard
* 
*   @package    CI
*   @subpackage Controller
*   @author Casperon
*
**/
class Dashboard extends MY_Controller {

    function __construct() {
        parent::__construct();
        $this->load->helper(array('cookie', 'date', 'form'));
        $this->load->library(array('encrypt', 'form_validation'));
        $this->load->model('dashboard_model');
        $this->load->model('app_model');
        
		if($this->checkLogin('O') != ''){
            
			$operator_id = $this->checkLogin('O');
			$chkOperator = $this->app_model->get_selected_fields(OPERATORS,array('_id' => MongoID($operator_id)),array('status'));
			$chkstatus = TRUE;
			$errMsg = '';
			if($chkOperator->num_rows() == 1){
				if($chkOperator->row()->status == 'Inactive'){
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
				$collection = OPERATORS;
				
				$condition = array('_id' =>MongoID($this->checkLogin('O')));
				$this->app_model->update_details($collection, $newdata, $condition);
				$operatordata = array(
							APP_NAME.'_session_operator_id' => '',
							APP_NAME.'_session_operator_name' => '',
							APP_NAME.'_session_operator_email' => '',
							APP_NAME.'_session_vendor_location' =>''
						   
						);
				$this->session->unset_userdata($operatordata);
				$this->setErrorMessage('error', $errMsg);
				redirect(OPERATOR_NAME);
			}
		}
		
    }
	/**
	* 
	* Displays the dashboard
	*
	* @return HTTP REDIRECT, dashboard page
	*
	**/
   
    public function index() {
        if ($this->checkLogin('O') == '') {
            redirect(OPERATOR_NAME);
        } else {
            redirect(OPERATOR_NAME.'/dashboard/display_dashboard');
        }
    }
	/**
	* 
	* Displays the Rides statics for the Driver and Riders under the operator
	*
	* @return HTTP REDIRECT, dashboard page
	*
	**/
    public function display_dashboard() {
        if ($this->checkLogin('O') == '') {
            redirect(OPERATOR_NAME);
        } else {
         
			$operator_id = MongoID($this->checkLogin('O'));
            $condition = array();
						
			$this->mongo_db->where(array('operator_id' => $operator_id));			
            $totalRides = $this->dashboard_model->get_all_counts(RIDES, $condition);
						
			$this->mongo_db->where(array('driver_location' => (string)$this->session->userdata(APP_NAME.'_session_operator_location'),'operator_id' => $operator_id));
            $totalDrivers = $this->dashboard_model->get_all_counts(DRIVERS, $condition);
						
            $condition = array('status' => 'Active');
			$this->mongo_db->where(array('driver_location' => (string)$this->session->userdata(APP_NAME.'_session_operator_location'),'operator_id' => $operator_id));
            $activeDrivers = $this->dashboard_model->get_all_counts(DRIVERS, $condition);
         
            $condition = array('ride_status' => 'Completed');
			$this->mongo_db->where(array('operator_id' => $operator_id));
            $completedRides = $this->dashboard_model->get_all_counts(RIDES, $condition);

            $condition = array('ride_status' => 'Booked');
			$this->mongo_db->where(array('operator_id' => $operator_id));
            $upcommingRides = $this->dashboard_model->get_all_counts(RIDES, $condition);

            $onRides = $this->dashboard_model->get_on_rides('', $operator_id);

            $condition = array('ride_status' => 'Cancelled', 'cancelled.primary.by' => 'User');
			$this->mongo_db->where(array('operator_id' => $operator_id));
            $riderDeniedRides = $this->dashboard_model->get_all_counts(RIDES, $condition);

            $condition = array('ride_status' => 'Cancelled', 'cancelled.primary.by' => 'Driver');
			$this->mongo_db->where(array('operator_id' => $operator_id));
            $driverDeniedRides = $this->dashboard_model->get_all_counts(RIDES, $condition);

            $todayRides = 0;
            $todayDrivers = 0;
            $monthRides = 0;
            $monthDrivers = 0;
            $yearRides = 0;
            $yearDrivers = 0;
						
			$todayRidesArr = $this->dashboard_model->get_today_rides('', $operator_id);
            $monthRidesArr = $this->dashboard_model->get_this_month_rides('', $operator_id);
			$yearRidesArr = $this->dashboard_model->get_this_year_rides('', $operator_id);
						
			if (!empty($todayRidesArr['result'])) {
                $todayRides = count($todayRidesArr['result']);
            }
            if (!empty($monthRidesArr['result'])) {
                $monthRides = count($monthRidesArr['result']);
            }
			if (!empty($yearRidesArr['result'])) {
                $yearRides = count($yearRidesArr['result']);
            }
	
            $todayDriversArr = $this->dashboard_model->get_today_drivers($operator_id);
			$monthDriversArr = $this->dashboard_model->get_this_month_drivers('', $operator_id);
			$yearDriversArr = $this->dashboard_model->get_this_year_drivers('', $operator_id);
						
			if (!empty($todayDriversArr['result'])) {
                $todayDrivers = count($todayDriversArr['result']);
            }
            if (!empty($monthDriversArr['result'])) {
                $monthDrivers = count($monthDriversArr['result']);
            }
			if (!empty($yearDriversArr['result'])) {
                $yearDrivers = count($yearDriversArr['result']);
            }
            $this->data['totalRides'] = $totalRides;
            $this->data['totalDrivers'] = $totalDrivers;
            $this->data['activeDrivers'] = $activeDrivers;
            $this->data['completedRides'] = $completedRides;
            $this->data['upcommingRides'] = $upcommingRides;
            $this->data['onRides'] = $onRides;
            $this->data['riderDeniedRides'] = $riderDeniedRides;
            $this->data['driverDeniedRides'] = $driverDeniedRides;
            $this->data['todayRides'] = $todayRides;
            $this->data['todayDrivers'] = $todayDrivers;
            $this->data['monthRides'] = $monthRides;
            $this->data['monthDrivers'] = $monthDrivers;
            $this->data['yearRides'] = $yearRides;
            $this->data['yearDrivers'] = $yearDrivers;
			if ($this->lang->line('admin_menu_dashboard') != '') 
				$this->data['heading']= stripslashes($this->lang->line('admin_menu_dashboard')); 
			else  $this->data['heading'] = 'Dashboard';
				$this->load->view(OPERATOR_NAME.'/settings/dashboard', $this->data);
			
		}
	}

}
/* End of file dashboard.php */
/* Location: ./application/controllers/operator/dashboard.php */