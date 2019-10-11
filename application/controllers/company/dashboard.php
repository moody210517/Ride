<?php if (!defined('BASEPATH'))    exit('No direct script access allowed');
/**
*
*	dashboard
* 
*	@package		CI
*	@subpackage		Controller
*	@author			Casperon
*
**/
class Dashboard extends MY_Controller {

    function __construct() {
        parent::__construct();
        $this->load->helper(array('cookie', 'date', 'form'));
        $this->load->library(array('encrypt', 'form_validation'));
        $this->load->model('dashboard_model');
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
    /**
	* 
	* Displays the dashboard
	*
	* @return HTTP REDIRECT, dashboard page
	*
	**/
    public function index() {
        if ($this->checkLogin('C') == '') {
            redirect(COMPANY_NAME);
        } else {
            redirect(COMPANY_NAME.'/dashboard/user_dashboard');
        }
    }

    /**
	* 
	* Displays the Rides statics for the Driver and Riders under the company
	*
	* @return HTTP REDIRECT, dashboard page
	*
	**/
    public function user_dashboard() {
        if ($this->checkLogin('C') == '') {
            redirect(COMPANY_NAME);
        } else {
			$company_id=$this->data['company_id'];
            $condition = array('company_id'=>$company_id);
            $totalRides = $this->dashboard_model->get_all_counts(RIDES, $condition);
            $totalDrivers = $this->dashboard_model->get_all_counts(DRIVERS, array('company_id'=>$company_id));
            $condition = array('status' => 'Active','company_id'=>$company_id);
            $activeDrivers = $this->dashboard_model->get_all_counts(DRIVERS, $condition);
           
            $condition = array('ride_status' => 'Completed','company_id'=>$company_id);
            $completedRides = $this->dashboard_model->get_all_counts(RIDES, $condition);
            $condition = array('ride_status' => 'Booked','company_id'=>$company_id);
            $upcommingRides = $this->dashboard_model->get_all_counts(RIDES, $condition);
            $onRides = $this->dashboard_model->get_on_rides_company($company_id);
            $condition = array('ride_status' => 'Cancelled', 'cancelled.primary.by' => 'User','company_id'=>$company_id);
            $riderDeniedRides = $this->dashboard_model->get_all_counts(RIDES, $condition);
            $condition = array('ride_status' => 'Cancelled', 'cancelled.primary.by' => 'Driver','company_id'=>$company_id);
            $driverDeniedRides = $this->dashboard_model->get_all_counts(RIDES, $condition);
            $todayRides = 0;
            $todayDrivers = 0;
            $monthRides = 0;
            $monthDrivers = 0;
            $yearRides = 0;
            $yearDrivers = 0;
            $todayDriversArr = $this->dashboard_model->get_this_today_drivers_company($company_id);
            $monthDriversArr = $this->dashboard_model->get_this_month_drivers_company($company_id);
            $monthRidesArr = $this->dashboard_model->get_this_month_rides_company($company_id);
            if (!empty($todayDriversArr['result'])) {
                $todayDrivers = count($todayDriversArr['result']);
            }
			if (!empty($monthDriversArr['result'])) {
                $monthDrivers = count($monthDriversArr['result']);
            }
            if (!empty($monthRidesArr['result'])) {
                $monthRides = count($monthRidesArr['result']);
            }
            $yearDriversArr = $this->dashboard_model->get_this_year_drivers_company($company_id);
            $yearRidesArr = $this->dashboard_model->get_this_year_rides_company($company_id);
            $todayRidesArr = $this->dashboard_model->get_this_today_rides_company($company_id);
            if (!empty($yearDriversArr['result'])) {
                $yearDrivers = count($yearDriversArr['result']);
            }
			if (!empty($todayRidesArr['result'])) {
                $todayRides = count($todayRidesArr['result']);
            }
            if (!empty($yearRidesArr['result'])) {
                $yearRides = count($yearRidesArr['result']);
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
            $this->load->view(COMPANY_NAME.'/settings/dashboard', $this->data);
         
        }
    } 
    
   

    
}
/* End of file dashboard.php */
/* Location: ./application/controllers/company/dashboard.php */

