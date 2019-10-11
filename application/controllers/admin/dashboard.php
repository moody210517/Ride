<?php if (!defined('BASEPATH')) exit('No direct script access allowed');
/**
*
*	Dashboard Management for admin 
* 
*	@package	CI
*	@subpackage	Controller
*	@author	Casperon
*
**/
class Dashboard extends MY_Controller {

    function __construct() {
        parent::__construct();
        $this->load->helper(array('cookie', 'date', 'form'));
        $this->load->library(array('encrypt', 'form_validation'));
        $this->load->model('dashboard_model');
    }

    /**
	* 
	* Displays the dashboard
	*
	* @return HTTP REDIRECT, dashboard page
	*
	**/
    public function index() {
        if ($this->checkLogin('A') == '') {
            redirect(ADMIN_ENC_URL);
        } else {
            redirect(ADMIN_ENC_URL.'/dashboard/admin_dashboard');
        }
        
    }

    /**
	* 
	* Displays the dashboard
	*
	* @return HTML, dashboard page
	*
	**/
    public function admin_dashboard() {
        if ($this->checkLogin('A') == '') {
            redirect(ADMIN_ENC_URL);
        } else {
           
            $condition = array();
            $totalUsers = $this->dashboard_model->get_all_counts(USERS, $condition);

            $totalRides = $this->dashboard_model->get_all_counts(RIDES, $condition);

            $totalDrivers = $this->dashboard_model->get_all_counts(DRIVERS, $condition);

            $totalcouponCode = $this->dashboard_model->get_all_counts(PROMOCODE, $condition);

            $totalLocations = $this->dashboard_model->get_all_counts(LOCATIONS, $condition);

            $condition = array('status' => 'Active');
            $activeDrivers = $this->dashboard_model->get_all_counts(DRIVERS, $condition);
            
            
            $condition = array('ride_status' => 'Completed');
            $completedRides = $this->dashboard_model->get_all_counts(RIDES, $condition);

            $condition = array('ride_status' => 'Booked');
            $upcommingRides = $this->dashboard_model->get_all_counts(RIDES, $condition);

            $onRides = $this->dashboard_model->get_on_rides();
           
            $condition = array('ride_status' => 'Cancelled', 'cancelled.primary.by' => 'User');
            $riderDeniedRides = $this->dashboard_model->get_all_counts(RIDES, $condition);
 
            $condition = array('ride_status' => 'Cancelled', 'cancelled.primary.by' => 'Driver');
            $driverDeniedRides = $this->dashboard_model->get_all_counts(RIDES, $condition);
            

            $totalEarnings = $this->dashboard_model->get_total_earnings();
			
            $totalWallet = $this->dashboard_model->get_current_wallet_balance();

            
            $current_date = MongoDATE(strtotime(date("Y-m-d 00:00:00")));
            $todayList = $this->dashboard_model->get_all_details(STATISTICS, array('day_hour' => $current_date));

            $todayRides = 0;
            $todayDrivers = 0;
            $monthRides = 0;
            $monthDrivers = 0;
            $yearRides = 0;
            $yearDrivers = 0;
            if ($todayList->num_rows() > 0) {
                $todayListArr = (array) $todayList->result_array();
                if (array_key_exists('ride_booked', $todayListArr[0])) {
                    if (is_array($todayList->row()->ride_booked)) {
                        if ($todayList->row()->ride_booked['count'] > 0) {
                            $todayRides = $todayList->row()->ride_booked['count'];
                        }
                    }
                }
                if (array_key_exists('driver', $todayListArr[0])) {
                    if (is_array($todayList->row()->driver)) {
                        if ($todayList->row()->driver['count'] > 0) {
                            $todayDrivers = $todayList->row()->driver['count'];
                        }
                    }
                }
            }

            $monthDriversArr = $this->dashboard_model->get_this_month_drivers();
            $monthRidesArr = $this->dashboard_model->get_this_month_rides();
			$todayRidesArr = $this->dashboard_model->get_today_rides();
			
			 if (!empty($todayRidesArr['result'])) {
				$todayRides = $todayRidesArr['result'][0]['ride_count'];
            }

            if (!empty($monthDriversArr['result'])) {
                $monthDrivers = count($monthDriversArr['result']);
            }
            if (!empty($monthRidesArr['result'])) {
                $monthRides = $monthRidesArr['result'][0]['ride_count'];
            }

            $yearDriversArr = $this->dashboard_model->get_this_year_drivers();
            $yearRidesArr = $this->dashboard_model->get_this_year_rides();

            if (!empty($yearDriversArr['result'])) {
                $yearDrivers = count($yearDriversArr['result']);
            }
            if (!empty($yearRidesArr['result'])) {
                $yearRides =$yearRidesArr['result'][0]['ride_count'];
            }

            $monthEarnings = array();
            $drivermonthEarnings = array();
            $sitemonthEarnings = array();
			
            $monthEarningsNew = array();
            $drivermonthEarningsNew = array();
            $sitemonthEarningsNew = array();
            $monthArr[] = array();
            
            for ($m = 0; $m < 12; $m++) {
                if ($m == 0) {
                    $mStartDate = strtotime(date("Y-m-01 00:00:00"));
                    $mEndDate = strtotime(date("Y-m-31 23:59:59"));
                    $currMonth = date("Y-m-d");
                    $currMonthN = date("M Y");
                } else {
                    $mStartDate = strtotime(date("Y-m-01 00:00:00", strtotime("-" . $m . " month")));
                    $mEndDate = strtotime(date("Y-m-d 23:59:59", strtotime('last day of this month', $mStartDate)));
                    $currMonth = date("Y-m-31", $mStartDate);
                    $currMonthN = date("M Y", $mStartDate);
                }
                $thismonthearnings = $this->dashboard_model->get_monthly_earnings($mStartDate, $mEndDate);
				
				$monthArr[] = array("Month"=>get_time_to_string("M Y",strtotime($currMonthN)));
                
                if ($thismonthearnings['totalAmount'] >= 0) {
                    $monthEarnings[] = array($currMonth, $thismonthearnings['totalAmount']);
					$monthEarningsNew[] = array("Amount"=>$thismonthearnings['totalAmount']);
                }
                if ($thismonthearnings['driver_Earnings'] >= 0) {
                    $drivermonthEarnings[] = array($currMonth, $thismonthearnings['driver_Earnings']);
					$drivermonthEarningsNew[] = array("Amount"=>$thismonthearnings['driver_Earnings']);
                }
                if ($thismonthearnings['site_Earnings'] >= 0) {
                    $sitemonthEarnings[] = array($currMonth, $thismonthearnings['site_Earnings']);
					$sitemonthEarningsNew[] = array("Amount"=>$thismonthearnings['site_Earnings']);
                }
            }

            $monthEarnings = array_reverse($monthEarnings);
            $monthlyEarningsGraph = $monthEarnings;
            $sitemonthEarnings = array_reverse($sitemonthEarnings);
            $monthlySiteEarningsGraph = $sitemonthEarnings;
			
            $monthArr = array_reverse($monthArr);		
            $monthEarningsNew = array_reverse($monthEarningsNew);
            $monthlyEarningsGraphNew = $monthEarningsNew;			
            $drivermonthEarningsNew = array_reverse($drivermonthEarningsNew);
            $monthlyDriverEarningsGraphNew = $drivermonthEarningsNew;
            $sitemonthEarningsNew = array_reverse($sitemonthEarningsNew);
            $monthlySiteEarningsGraphNew = $sitemonthEarningsNew;

            
            $this->data['totalUsers'] = $totalUsers;
            $this->data['totalRides'] = $totalRides;
            $this->data['totalDrivers'] = $totalDrivers;
            $this->data['activeDrivers'] = $activeDrivers;
            $this->data['totalcouponCode'] = $totalcouponCode;
            $this->data['totalLocations'] = $totalLocations;

            $this->data['completedRides'] = $completedRides;
            $this->data['upcommingRides'] = $upcommingRides;
            $this->data['onRides'] = $onRides;
            $this->data['riderDeniedRides'] = $riderDeniedRides;
            $this->data['driverDeniedRides'] = $driverDeniedRides;

            $this->data['totalEarnings'] = $totalEarnings;
            $this->data['totalWallet'] = $totalWallet;

            $this->data['todayRides'] = $todayRides;
            $this->data['todayDrivers'] = $todayDrivers;
            $this->data['monthRides'] = $monthRides;
            $this->data['monthDrivers'] = $monthDrivers;
            $this->data['yearRides'] = $yearRides;
            $this->data['yearDrivers'] = $yearDrivers;


            $this->data['monthlyEarningsGraph'] = $monthlyEarningsGraph;
            $this->data['monthlySiteEarningsGraph'] = $monthlySiteEarningsGraph;
			
            $this->data['monthArr'] = $monthArr;
            $this->data['monthlyEarningsGraphNew'] = $monthlyEarningsGraphNew;
            $this->data['monthlySiteEarningsGraphNew'] = $monthlySiteEarningsGraphNew;
            $this->data['monthlyDriverEarningsGraphNew'] = $monthlyDriverEarningsGraphNew;
			

			if ($this->lang->line('admin_menu_dashboard') != '') 
		    $this->data['heading']= stripslashes($this->lang->line('admin_menu_dashboard')); 
		    else  $this->data['heading'] = 'Dashboard'; 
            $this->load->view(ADMIN_ENC_URL.'/adminsettings/dashboard', $this->data);
        }
    }

}


/* End of file dashboard.php */
/* Location: ./application/controllers/admin/dashboard.php */