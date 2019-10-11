<?php
if (!defined('BASEPATH'))
    exit('No direct script access allowed');

/**
 * 
 * This controller contains the functions related to driver dashboard
 * @author Casperon
 *
 * */
class Dashboard extends MY_Controller {

    function __construct() {
        parent::__construct();
        $this->load->helper(array('cookie', 'date', 'form'));
        $this->load->library(array('encrypt', 'form_validation'));
        $this->load->model('dashboard_model');
    }

    /**
     * 
     * This function loads the driver Dashboard 
     *
     */
    public function index() {
        if ($this->checkLogin('D') == '') {
            redirect('driver');
        } else {
            redirect('driver/dashboard/driver_dashboard');
        }
    }

    /**
     * 
     * This function loads the driver Dashboard 
     * */
    public function driver_dashboard() {
        if ($this->checkLogin('D') == '') {
            redirect('driver');
        } else {
            /* Total Site statistics */
            $driver_id = $this->checkLogin('D');
            $condition = array('driver.id' => $driver_id);


            $totalRides = $this->dashboard_model->get_all_counts(RIDES, $condition);
			#echo '<pre>'; print_r($totalRides); die;

            /* Ride Statistics Informations */
            $condition = array('ride_status' => 'Completed', 'driver.id' => $driver_id);
            $completedRides = $this->dashboard_model->get_all_counts(RIDES, $condition);
			
			$condition = array('ride_status' => 'Cancelled', 'driver.id' => $driver_id);
            $cancelledRides = $this->dashboard_model->get_all_counts(RIDES, $condition);

            $condition = array('ride_status' => 'Confirmed', 'driver.id' => $driver_id);
            $upcommingRides = $this->dashboard_model->get_all_counts(RIDES, $condition);

            $condition = array('ride_status' => 'Cancelled', 'cancelled.primary.by' => 'User', 'driver.id' => $driver_id);
            $riderDeniedRides = $this->dashboard_model->get_all_counts(RIDES, $condition);

            $condition = array('ride_status' => 'Cancelled', 'cancelled.primary.by' => 'Driver', 'driver.id' => $driver_id);
            $driverDeniedRides = $this->dashboard_model->get_all_counts(RIDES, $condition);


            $totalEarnings = $this->dashboard_model->get_total_earnings($driver_id);

          
            $todayRides = 0;
            $monthRides = 0;
            $yearRides = 0;
			$todayUpcomingRides = 0;
			$todayCompletedRides = 0;
            $todayCancelledRides = 0;

            $monthRidesArr = $this->dashboard_model->get_this_month_rides($driver_id);
            $todayRidesArr = $this->dashboard_model->get_today_rides($driver_id);
			
			$todayUpComingRidesArr = $this->dashboard_model->get_today_rides($driver_id,'','Confirmed');
			$todayCompletedRidesArr = $this->dashboard_model->get_today_rides($driver_id,'','Completed');
			$todayCancelledRidesArr = $this->dashboard_model->get_today_rides($driver_id,'','Cancelled');
            
            if (!empty($todayRidesArr['result'])) {
				$todayRides = $todayRidesArr['result'][0]['ride_count'];
            }

			if (!empty($todayUpComingRidesArr['result'])) {
				$todayUpcomingRides = $todayUpComingRidesArr['result'][0]['ride_count'];
            }

			if (!empty($todayCompletedRidesArr['result'])) {
				$todayCompletedRides = $todayCompletedRidesArr['result'][0]['ride_count'];
            }
			if (!empty($todayCancelledRidesArr['result'])) {
				$todayCancelledRides = $todayCancelledRidesArr['result'][0]['ride_count'];
            }
            
            if (!empty($monthRidesArr['result'])) {
                $monthRides = $monthRidesArr['result'][0]['ride_count'];
            }
            

            $yearRidesArr = $this->dashboard_model->get_this_year_rides($driver_id); #echo '<pre>'; print_r($yearRidesArr); die;

            if (!empty($yearRidesArr['result'])) {
                $yearRides =$yearRidesArr['result'][0]['ride_count'];
            }


			$weekly_total_earnings = 0;
            $weekEarnings = array();
            for ($m = 0; $m < 7; $m++) {
                if ($m == 0) {
                    $mStartDate = strtotime(date("Y-m-d 00:00:00"));
                    $mEndDate = strtotime(date("Y-m-d 23:59:59"));
                    $currMonth = date("Y-m-d 00:00:00");
                } else {
                    $mStartDate = strtotime(date("Y-m-d 00:00:00", strtotime("-" . $m . " day", time())));
                    $mEndDate = strtotime(date("Y-m-d 23:59:59", strtotime("-" . $m . " day", time())));
                    $currMonth = date("Y-m-d 00:00:00", $mStartDate);
                } 
                
                $currMonth = get_time_to_string('M d',strtotime($currMonth));
                
                $thisweekEarnings = $this->dashboard_model->get_monthly_earnings($mStartDate, $mEndDate, $driver_id);
				$weekly_total_earnings = $weekly_total_earnings + $thisweekEarnings['totalAmount'];
                $weekEarnings[] = array('Day' => (string) $currMonth, 'Earnings' => (int) $thisweekEarnings['totalAmount']);
				
				/* $weekly_total_earnings = $weekly_total_earnings + $m;
				$weekEarnings[] = array('Day' => (string) $currMonth, 'Earnings' => (int) $m); */
            }
            $weekEarnings = array_reverse($weekEarnings);
            $weeklyEarningsGraph = $weekEarnings;
			
			#echo "<pre>"; print_r($weekEarnings); die; 
			
			 $MonthlyEarnings = array();
			 $MonthlyTotalEarnings = 0;
			 $total_driver_revenue = 0;
			 $total_driver_revenue_summary = 0;
			 $m = 11;
            while($m>=0){
                if ($m == 0) {
                    $mStartDate = strtotime(date("Y-m-01 00:00:00"));
                    $mEndDate = strtotime(date("Y-m-31 23:59:59"));
                    $currMonth = date("Y-m-d");
                    $currMonthN = date("M y");
                } else {
                    $mStartDate = strtotime(date("Y-m-01 00:00:00", strtotime("-" . $m . " month")));
                    $mEndDate = strtotime(date("Y-m-d 23:59:59", strtotime('last day of this month', $mStartDate)));
                    $currMonth = date("Y-m-31", $mStartDate);
                    $currMonthN = date("M y", $mStartDate);
                }
                
                #echo date("Y-m-d",$mStartDate).'----'.date("Y-m-31",$mEndDate).'</br>';
                $currMonthN = get_time_to_string("M",strtotime($currMonthN));
                
                $thismonthearnings = $this->dashboard_model->get_monthly_earnings($mStartDate, $mEndDate,$driver_id);
				
				#echo "<pre>"; print_r($thismonthearnings);
				
				$MonthlyTotalEarnings = $MonthlyTotalEarnings + $thismonthearnings['totalAmount'];
				$total_driver_revenue = $thismonthearnings['driver_Earnings'];
				$total_driver_revenue_summary = $total_driver_revenue_summary + $total_driver_revenue;
				$MonthlyEarnings[] = array('Month' => (string) $currMonthN, 'Earnings' => (int) $total_driver_revenue);
				$m--;
            }
            
           
            if ($this->lang->line('dr_dash_total') != '')
                $totalLang = stripslashes($this->lang->line('dr_dash_total'));
            else
                $totalLang = 'Total';
            
			$MonthlyEarnings[]  = array('Month' => $totalLang, 'Earnings' => (int)$total_driver_revenue_summary);


			#echo "<pre>"; print_r($MonthlyEarnings); die; 


            /* Calculating the rides informations for graph */
         
            $this->data['totalRides'] = $totalRides;
            $this->data['completedRides'] = $completedRides;
            $this->data['upcommingRides'] = $upcommingRides;
            $this->data['cancelledRides'] = $cancelledRides;

            $this->data['weekly_total_earnings'] = $weekly_total_earnings;
			$this->data['totalEarnings'] = $totalEarnings;

            $this->data['todayRides'] = $todayRides;
			$this->data['todayUpcomingRides'] = $todayUpcomingRides;
			$this->data['todayCompletedRides'] = $todayCompletedRides;
			$this->data['todayCancelledRides'] = $todayCancelledRides;
			
            $this->data['monthRides'] = $monthRides;
            $this->data['yearRides'] = $yearRides;

            $this->data['weeklyEarningsGraph'] = $weeklyEarningsGraph;
			$this->data['MonthlyEarningsGraph'] = $MonthlyEarnings;
			if ($this->lang->line('driver_dashboard') != '')
                $dashboard = stripslashes($this->lang->line('driver_dashboard'));
            else
                $dashboard = 'Dashboard';
            $this->data['heading'] = $dashboard;
			$this->data['sideMenu'] = 'dashboard';
            $this->load->view('driver/driversettings/dashboard', $this->data);
            /* Assign dashboard values to view end */
        }
    }

}
