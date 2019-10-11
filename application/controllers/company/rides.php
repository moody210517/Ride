<?php if (!defined('BASEPATH')) exit('No direct script access allowed');
/**
*
*   Rides Management
* 
*   @package    CI
*   @subpackage Controller
*   @author Casperon
*
**/

class Rides extends MY_Controller {

    function __construct() {
        parent::__construct();
		$this->load->helper(array('cookie', 'date', 'form','ride_helper','distcalc_helper'));
        $this->load->library(array('encrypt', 'form_validation','excel'));
        $this->load->model('rides_model');
        $this->load->model('app_model');
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
    * Redirect to Company's ride List
    *
    * @return HTTP REDIRECT Company's ride List
    *
    **/
    public function index() {
        if ($this->checkLogin('C') == '') {
            redirect(COMPANY_NAME);
        } else {
            redirect(COMPANY_NAME.'/rides/display_rides');
        }
    }

    /**
    * 
    * Display Company Rides List
    *
    * @param string $company_id  company MongoDB\BSON\ObjectId
    * @return HTML, Company Rides List
    *
    **/
    public function display_rides() {

        if ($this->checkLogin('C') == '') {
            redirect(COMPANY_NAME);
        } else {
			$this->data['locationLists'] = $this->rides_model->get_selected_fields(LOCATIONS,array(),array('_id','city'),array('city' => 1));
            $ride_act = '';
            $from = '';
            $to = '';
            $location = '';
			$filter = "";
			$company_id=$this->data['company_id'];
			$filter_array=array();
			if (isset($_GET['from'])) {
				$from = $this->input->get('from');
				$filter = "filter";
			}
			if (isset($_GET['to'])) {
				$to = $this->input->get('to');
			}
			if (isset($_GET['location'])) {
				$location = $this->input->get('location');
				$filter = "filter";
			}
			$this->data['filter'] = $filter;
			$filter_array = array('from' => base64_decode($from), 'to' => base64_decode($to) , 'location' => $location);
            if (isset($_GET['act'])) {
                $ride_act = $this->input->get('act');
            }
            $offsetVal = 0;
            if (isset($_GET['per_page'])) {
                $offsetVal = $this->input->get('per_page');
            }
            if ($ride_act == 'Booked') {
			if ($this->lang->line('dash_just_booked') != '') 
		           $this->data['heading']= stripslashes($this->lang->line('dash_just_booked')); 
		      else  $this->data['heading'] = 'Just Booked , Not Yet Started Rides';
            } else if ($ride_act == 'OnRide') {
				if ($this->lang->line('dash_on_rides_list') != '') 
		            $this->data['heading']= stripslashes($this->lang->line('dash_on_rides_list')); 
		       else  $this->data['heading'] = 'On Rides List';
            } else if ($ride_act == 'Completed') {
				if ($this->lang->line('dash_completed_rides_list') != '') 
					$this->data['heading']= stripslashes($this->lang->line('dash_completed_rides_list')); 
		        else  $this->data['heading'] = 'Completed Rides List';
            } else if ($ride_act == 'Cancelled') {
				if ($this->lang->line('dash_cancelled_rides_list') != '') 
					$this->data['heading']= stripslashes($this->lang->line('dash_cancelled_rides_list')); 
		        else  $this->data['heading'] = 'Cancelled Rides List';
            } else if ($ride_act == 'riderCancelled') {
				if ($this->lang->line('dash_rider_cancelled_rides_list') != '') 
		              $this->data['heading']= stripslashes($this->lang->line('dash_rider_cancelled_rides_list')); 
		              else  $this->data['heading'] = 'Rider Cancelled Rides List';
            } else if ($ride_act == 'driverCancelled') {
				 if ($this->lang->line('dash_driver_cancelled_rides_list') != '') 
		              $this->data['heading']= stripslashes($this->lang->line('dash_driver_cancelled_rides_list')); 
		              else  $this->data['heading'] = 'Driver Cancelled Rides List';
			} else if ($ride_act == 'Expired') {
			             if ($this->lang->line('heading_expired_ride_list') != '') 
		              $this->data['heading']= stripslashes($this->lang->line('heading_expired_ride_list')); 
		              else  $this->data['heading'] = 'Expired Rides List';
            } else {
				if ($this->lang->line('dash_all_rides_list') != '') 
		              $this->data['heading']= stripslashes($this->lang->line('dash_all_rides_list')); 
		              else  $this->data['heading'] = 'All Rides List';
            }
			
            $rides_total = $ridesList = $this->rides_model->get_rides_total($ride_act,'',$company_id);

            if ($rides_total->num_rows() > 100) {
                $limit = 50;

                $this->data['ridesList'] = $ridesList = $this->rides_model->get_rides_list($ride_act, $limit, $offsetVal, '', $filter_array,$company_id);

                $searchbaseUrl = COMPANY_NAME.'/rides/display_rides?act=' . $ride_act;
                $config['num_links'] = 3;
                $config['display_pages'] = TRUE;
                $config['page_query_string'] = TRUE;
                $config['base_url'] = $searchbaseUrl;
                $config['total_rows'] = $rides_total->num_rows();
                $config["per_page"] = $limit;
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
                $this->data['ridesList'] = $ridesList = $this->rides_model->get_rides_list($ride_act, FALSE,FALSE,'',$filter_array,$company_id);
            }
			
            $this->data['offsetVal'] = $offsetVal;

            #echo '<pre>'; print_r($ridesList->result()); die;
            $this->load->view(COMPANY_NAME.'/rides/display_rides', $this->data);
        }
    }

    /**
    * 
    * Display a Ride detail
    *
    * @param string $rides_id  ride MongoDB\BSON\ObjectId
    * @return HTML,page
    *
    **/ 
    public function view_ride_details() {
        if ($this->checkLogin('C') == '') {
            redirect(COMPANY_NAME);
        } else {
		    if ($this->lang->line('dash_view_rides') != '') 
		    $this->data['heading']= stripslashes($this->lang->line('dash_view_rides')); 
		    else  $this->data['heading'] = 'View Rides';
            $rides_id = $this->uri->segment(4, 0);
            $condition = array('_id' => MongoID($rides_id));
            $this->data['rides_details'] = $rides_details = $this->rides_model->get_all_details(RIDES, $condition);
            if ($this->data['rides_details']->num_rows() == 1) {
                $this->load->view(COMPANY_NAME.'/rides/view_rides', $this->data);
            } else {
                $this->setErrorMessage('error', 'No records found','admin_driver_no_records_found');
                redirect(COMPANY_NAME.'/rides/display_rides');
            }
        }
    }

    /**
    * 
    * Delete a ride
    *
    * @param string $ride_id  Ride MongoDB\BSON\ObjectId
    * @return HTTP REDIRECT , Company ride list
    *
    **/ 
    public function delete_rides() {
        if ($this->checkLogin('C') == '') {
            redirect(COMPANY_NAME);
        } else {
            $this->setErrorMessage('error', 'This service is not available','driver_service_not_avail');
            redirect(COMPANY_NAME.'/rides/display_rides');
            /* $rides_id = $this->uri->segment(4,0);
              $condition = array('_id' => MongoID($rides_id));
              $this->rides_model->commonDelete(RIDES,$condition);
              $this->setErrorMessage('success','Rides deleted successfully');
              redirect('admin/rides/display_rides'); */
        }
    }

    /**
    * 
    * Change the status of multiple rides
    * 
    * @return HTTP REDIRECT to Company Ride Lists  
    *
    **/
    public function change_rides_status_global() {
        if (count($_POST['checkbox_id']) > 0 && $_POST['statusMode'] != '') {
            if (strtolower($_POST['statusMode']) == 'delete') {
                $this->setErrorMessage('error', 'This service is not available','driver_service_not_avail');
                redirect(COMPANY_NAME.'/rides/display_rides');
            }
            $this->user_model->activeInactiveCommon(RIDES, '_id');
            if (strtolower($_POST['statusMode']) == 'delete') {
                $this->setErrorMessage('success', 'Rides records deleted successfully','driver_rides_records_deleted');
            } else {
                $this->setErrorMessage('success', 'Rides records status changed successfully','driver_rides_status_changed');
            }
            redirect(COMPANY_NAME.'/rides/display_rides');
        }
    }

    /**
    * 
    * Display available drivers
    *
    * @param string $ride_id  Ride MongoDB\BSON\ObjectId
    * @return HTML , available drivers list
    *
    **/ 
    public function available_drivers_list() {
        if ($this->checkLogin('C') == '') {
            redirect(COMPANY_NAME);
        } else {
		    if ($this->lang->line('dash_ride_avaialble_drivers') != '') 
		    $this->data['heading']= stripslashes($this->lang->line('dash_ride_avaialble_drivers')); 
		    else  $this->data['heading'] = 'Available Drivers';
            $rides_id = $this->uri->segment(4, 0);
            $condition = array('_id' => MongoID($rides_id));
            $this->data['rides_details'] = $rides_details = $this->rides_model->get_all_details(RIDES, $condition);
            if ($this->data['rides_details']->num_rows() == 1) {
                $pickup_lat = $this->data['rides_details']->row()->booking_information['pickup']['latlong']['lat'];
                $pickup_lon = $this->data['rides_details']->row()->booking_information['pickup']['latlong']['lon'];
                $category = $this->data['rides_details']->row()->booking_information['service_id'];
                $coordinates = array(floatval($pickup_lon), floatval($pickup_lat));
				
				
				$distance_unit = $this->data['d_distance_unit'];
				if(isset($rides_details->row()->fare_breakup['distance_unit'])){
					$distance_unit = $rides_details->row()->fare_breakup['distance_unit'];
				}
				
				$this->data['distance_unit'] = $distance_unit;
				
                $category_drivers = $this->app_model->get_nearest_driver($coordinates, (string) $category, 10,$distance_unit);
                $this->data['driversList'] = $driversList = $category_drivers['result'];

                $this->load->view(COMPANY_NAME.'/rides/available_drivers', $this->data);
            } else {
                $this->setErrorMessage('error', 'No records found');
                redirect(COMPANY_NAME.'/rides/display_rides?act=Booked');
            }
        }
    }

    /**
    * 
    * Assign a driver to a ride
    *
    * @param string $ride_id  Ride id
    * @param string $driver_id  Driver MongoDB\BSON\ObjectId
    * @return HTTP REDIRECT ,rides list page
    *
    **/ 
    public function assign_driver() {
        if ($this->checkLogin('C') == '') {
            redirect(COMPANY_NAME);
        } else {
            $ride_id = $this->uri->segment(4);
            $driver_id = $this->uri->segment(5);			
			$condition = array('ride_id' => $ride_id);
			$rides_details = $this->app_model->get_all_details(RIDES,$condition);
			if ($rides_details->num_rows() == 1){
				$assignArr = array("driver_id"=>$driver_id,"ride_id"=>$ride_id,"ref"=>"Manual");
				$returnArr = assign_ride($assignArr);
				if($returnArr['status']=='1'){
					$this->setErrorMessage('success', 'Cab assigned successfully.','error_message_cab_assigned_success');
				}else{
					$this->setErrorMessage('error', $returnArr['response']);
				}
				redirect(COMPANY_NAME.'/rides/available_drivers_list/' . $rides_details->row()->_id);
			}else {
				$this->setErrorMessage('error','No records found','driver_no_records_found');
				redirect(COMPANY_NAME.'/rides/display_rides?act=Booked');
			}
        }
    }
	
	/**
    * 
    * Display Rides Cancellation page
    *
    * @return HTML ,Rides Cancellation page
    *
    **/ 
	public function cancel_ride(){
		if ($this->checkLogin('C') == '') {
            redirect(COMPANY_NAME);
         } else {
            
			if($this->lang->line('admin_menu_cancel_ride') != '') 
		     $this->data['heading']= stripslashes($this->lang->line('admin_menu_cancel_ride')); 
		     else  $this->data['heading'] = 'Cancel Ride';
			$this->data['form_mode'] = FALSE;
			$this->data['rideFound'] = 'false';
			$this->load->view(COMPANY_NAME.'/rides/cancelling_ride',$this->data);
			
		}
	}
	
	/**
    * 
    * Display Ride Cancellation page
    *
    * @param string $ride_id  Ride id
    * @return HTML ,Ride Cancellation page
    *
    **/
	public function cancelling_ride(){
		if ($this->checkLogin('C') == '') {
            redirect(COMPANY_NAME);
         } else {
		    if ($this->lang->line('dash_ride_cancelling_ride') != '') 
		    $this->data['heading']= stripslashes($this->lang->line('dash_ride_cancelling_ride')); 
		    else  $this->data['heading'] = 'Cancelling Ride';
			$this->data['ride_id'] = $ride_id = $this->input->post('search_ride_id');
			$this->data['rideFound'] = 'false';
			$this->data['ride_details'] = $checkRide = $this->rides_model->get_all_details(RIDES,array('ride_id'=>$ride_id));
			if($checkRide->num_rows()>0){
				$this->data['rideFound'] = 'true';
				$this->load->view(COMPANY_NAME.'/rides/cancelling_ride',$this->data);
			}else{
			
				$this->setErrorMessage('error','Ride not found','error_message_ride_not_found');
				redirect(COMPANY_NAME.'/rides/cancel_ride');
			}
			
			
		}
	}
	
	/**
    * 
    * Ride detail Page
    *
    * @param string $ride_id  ride MongoDB\BSON\ObjectId
    * @return HTTP REDIRECT,ride detail page
    *
    **/ 
	public function view_ride(){
		if ($this->checkLogin('C') == '') {
            redirect(COMPANY_NAME);
         } else{
		     if ($this->lang->line('admin_menu_cancel_ride') != '') 
		     $this->data['heading']= stripslashes($this->lang->line('admin_menu_cancel_ride')); 
		     else  $this->data['heading'] = 'Cancel Ride';
			 $checkRide = $this->rides_model->get_selected_fields(RIDES,array('ride_id'=>$ride_id),array('_id'));
				if($checkRide->num_rows()>0){
					$ride_id = (string)$checkRide->row()->_id;
					 redirect(COMPANY_NAME.'/rides/view_ride_details/'.$ride_id);
				}else{
				   $this->setErrorMessage('error', 'No records found','admin_driver_no_records_found');
				   redirect(COMPANY_NAME.'/rides/cancel_ride');
			    }
			 
		 }
	}
	
    /**
    * 
    * Ride Cancellation
    *
    * @param string $ride_id  ride id
    * @param string $cancelled_by User/driver
    * @param string $cancel_reason cancellation reason  
    * @return HTTP REDIRECT,Ride Cancellation page
    *
    **/
	public function make_ride_cancelled(){
		if ($this->checkLogin('C') == '') {
            redirect(COMPANY_NAME);
         } else{
			$cancelled_by = $this->input->post('cancelled_by');
			$cancel_reason = $this->input->post('cancel_reason');
			if($cancelled_by != '' && $cancel_reason !==''){
				$ride_id = $this->input->post('ride_id');
				$checkRide = $this->rides_model->get_all_details(RIDES,array('ride_id'=>$ride_id));
				if($checkRide->num_rows()>0){
					$doAction = 0;
					$ride_status = $checkRide->row()->ride_status;
					if ($ride_status == 'Booked' || $ride_status == 'Confirmed' || $ride_status == 'Arrived') {
						$doAction = 1;
						if ($ride_status == 'Cancelled') {
							$doAction = 0;
						}
					}
							
					if ($doAction == 1) {
						$reasonVal = $this->rides_model->get_selected_fields(CANCELLATION_REASON, array('_id' => MongoID($cancel_reason)), array('reason'));
						if ($reasonVal->num_rows() > 0) {
							$reason_id = (string) $reasonVal->row()->_id;
							$reason_text = (string) $reasonVal->row()->reason;

							if($cancelled_by == 'driver'){
								$cancell_id = $checkRide->row()->driver['id'];
							}else{
								$cancell_id = $checkRide->row()->user['id'];
							}
							$user_id = $checkRide->row()->user['id'];
							
							if($reason_id==""){
								$this->setErrorMessage('success', 'Invalid Reason','error_message_ride_invalid_reason');
								redirect(COMPANY_NAME.'/rides/cancel_ride');
							}
							
							$isPrimary = 'No';
							/* Update the ride information */
							if ($checkRide->row()->ride_status != 'Cancelled') {
								$rideDetails = array('ride_status' => 'Cancelled',
									'cancelled' => array('primary' => array('by' => ucfirst($cancelled_by),
											'id' => $cancell_id,
											'reason' => $reason_id,
											'text' => $reason_text
										)
									),
									'history.cancelled_time' => MongoDATE(time())
								);
								$isPrimary = 'Yes';
							} else if ($checkRide->row()->ride_status == 'Cancelled') {
								$rideDetails = array('cancelled.secondary' => array('by' => ucfirst($cancelled_by),
										'id' => $cancell_id,
										'reason' => $reason_id,
										'text' => $reason_text
									),
									'history.secondary_cancelled_time' => MongoDATE(time())
								);
							}
							$this->rides_model->update_details(RIDES, $rideDetails, array('ride_id' => $ride_id));

							if ($checkRide->row()->driver['id'] != '') {
								/* Update the driver status to Available */
								$avail_data = array('mode' => 'Available', 'availability' => 'Yes');
								$driver_id = $checkRide->row()->driver['id'];
								$this->rides_model->update_details(DRIVERS,$avail_data,array('_id'=>MongoID($driver_id)));
							}else{
								$driver_id = "";
							}
									
							/* Update Stats Starts */
							$current_date = MongoDATE(strtotime(date("Y-m-d 00:00:00")));
							$field = array('ride_cancel.hour_' . date('H') => 1, 'ride_cancel.count' => 1);
							$this->rides_model->update_stats(array('day_hour' => $current_date), $field, 1);
							/* Update Stats End */
									

							if ($cancelled_by == 'user' && $isPrimary == 'Yes') {
								/* Update the coupon usage details */
								if ($checkRide->row()->coupon_used == 'Yes') {
									$usage = array("user_id" => (string) $user_id, "ride_id" => $ride_id);
									$promo_code = (string) $checkRide->row()->coupon['code'];
									$this->rides_model->simple_pull(PROMOCODE, array('promo_code' => $promo_code), array('usage' => $usage));
								}
							}
							
							/* Update the no of cancellation under this reason  */
							$this->app_model->update_user_rides_count('cancelled_rides', (string) $user_id);
							if ($driver_id != '') {
								$this->app_model->update_driver_rides_count('cancelled_rides', (string) $driver_id);
							}
							
							if($driver_id!=""){
								$driverVal = $this->rides_model->get_selected_fields(DRIVERS, array('_id' => MongoID($driver_id)), array('_id', 'push_notification'));

								if (isset($driverVal->row()->push_notification)) {
									if ($driverVal->row()->push_notification != '') {
										if($cancelled_by == 'driver'){
											$message = $this->format_string('Your ride cancelled', 'your_ride_cancelled', '', 'driver', (string)$driver_id);
										}else{
											$message = $this->format_string('Rider cancelled this ride', 'rider_cancelled_ride', '', 'driver', (string)$driver_id);
										}
										$options = array('ride_id' => (string) $ride_id, 'driver_id' => $driver_id);
										if (isset($driverVal->row()->push_notification['type'])) {
											if ($driverVal->row()->push_notification['type'] == 'ANDROID') {
												if (isset($driverVal->row()->push_notification['key'])) {
													if ($driverVal->row()->push_notification['key'] != '') {
														$this->sendPushNotification($driverVal->row()->push_notification['key'], $message, 'ride_cancelled', 'ANDROID', $options, 'DRIVER');
													}
												}
											}
											if ($driverVal->row()->push_notification['type'] == 'IOS') {
												if (isset($driverVal->row()->push_notification['key'])) {
													if ($driverVal->row()->push_notification['key'] != '') {
														$this->sendPushNotification($driverVal->row()->push_notification['key'], $message, 'ride_cancelled', 'IOS', $options, 'DRIVER');
													}
												}
											}
										}
									}
								}
							}
							
							if($user_id!=""){
								$userVal = $this->rides_model->get_selected_fields(USERS, array('_id' => MongoID($user_id)), array('_id', 'push_notification_key','push_type'));
								
								if (isset($userVal->row()->push_type)) {
									if ($userVal->row()->push_type != '') {
										if($cancelled_by == 'driver'){
											$message = $this->format_string('Driver cancelled this ride', 'driver_cancelled_ride', '', 'user', (string)$user_id);
										}else{
											$message = $this->format_string('Your ride cancelled', 'your_ride_cancelled', '', 'user', (string)$user_id);
										}
										$options = array('ride_id' => (string) $ride_id);
										if ($userVal->row()->push_type == 'ANDROID') {
											if (isset($userVal->row()->push_notification_key['gcm_id'])) {
												if ($userVal->row()->push_notification_key['gcm_id'] != '') {
													$this->sendPushNotification(array($userVal->row()->push_notification_key['gcm_id']), $message, 'ride_cancelled', 'ANDROID', $options, 'USER');
												}
											}
										}
										if ($userVal->row()->push_type == 'IOS') {
											if (isset($userVal->row()->push_notification_key['ios_token'])) {
												if ($userVal->row()->push_notification_key['ios_token'] != '') {
													$this->sendPushNotification(array($userVal->row()->push_notification_key['ios_token']), $message, 'ride_cancelled', 'IOS', $options, 'USER');
												}
											}
										}
									}
								}
							}
							$this->setErrorMessage('success', 'Ride has been cancelled Successfully!','error_message_ride_has_cancelled_success');
							redirect(COMPANY_NAME.'/rides/cancel_ride');
						} else {
							$this->setErrorMessage('success', 'Invalid Reason','error_message_ride_invalid_reason');
							redirect(COMPANY_NAME.'/rides/cancel_ride');
						}
					} else {
						if ($checkRide->row()->ride_status == 'Cancelled') {
							$this->setErrorMessage('error', 'This trip has been already cancelled','error_message_ride_already_cancelled');
						}
						if ($checkRide->row()->ride_status == 'Onride') {
							$this->setErrorMessage('error', 'This trip is in progress, you cannot able to cancel this trip','error_message_ride_is_in_progress');
						}
						if ($checkRide->row()->ride_status == 'Finished' || $checkRide->row()->ride_status == 'Completed') {
							$this->setErrorMessage('error', 'This trip has been finished, you cannot able to cancel this trip','error_message_ride_already_finished');
						}
						redirect(COMPANY_NAME.'/rides/cancel_ride');
					}
				}else{
				   $this->setErrorMessage('error', 'Ride Not Found','error_message_ride_not_found');
				   redirect(COMPANY_NAME.'/rides/cancel_ride');
				}
			}else{
			   $this->setErrorMessage('error', 'Some Parameters are missing','error_message_ride_parameter_missing');
			   redirect(COMPANY_NAME.'/rides/cancel_ride');
			}
		}
	}
	
	/**
    * 
    * User Cancellation Reason
    *
    * @param string $user_type user type
    * @return JSON,user cancellation list
    *
    **/
	public function user_type_cancellation_reason(){
		$user_type = $this->input->post('user_type');
		$condition = array('type'=>$user_type);
		$cancel_reason = $this->rides_model->get_all_details(CANCELLATION_REASON,$condition);
		$i = 0;
		foreach($cancel_reason->result_array() as $reason){
			$returnArr[$i]= $reason;
			$returnArr[$i]['id'] = (string)$returnArr[$i]['_id'];
			unset($returnArr[$i]['_id']);
			$i++;
		}
		echo json_encode($returnArr);
	}
	
    /**
    * 
    * Ride list Excel Export
    *
    * @param string $action  ride status
    * @param string $from  ride from date
    * @param string $to  ride to date
    * @param string $location  ride location
    * @return EXCELSHEET,ride excel report
    *
    **/
	public function display_report_rides(){
		if ($this->checkLogin('C') == '') {
            redirect(COMPANY_NAME);
		} else {
			$where_clause = array();
			$ride_actions = $this->input->get('action');
			$from = $this->input->get('from');
			$to = $this->input->get('to');
			$location = $this->input->get('location');
			if ($ride_actions == 'Booked') {
				$where_clause = array('ride_status' => 'Booked');
			} else if ($ride_actions == 'OnRide') {
				$where_clause = array(
					'$or' => array(
									array("ride_status" => 'Onride'),
									array("ride_status" => 'Confirmed'),
									array("ride_status" => 'Arrived'),
									array("ride_status" => 'Finished'),
								)
									);
			} else if ($ride_actions == 'Completed') {
				$where_clause = array("ride_status" => 'Completed');
			} else if ($ride_actions == 'Cancelled') {
				$where_clause = array('ride_status' => 'Cancelled');
			}else if($ride_actions == 'Expired'){
			  $where_clause = array('ride_status' => 'Expired');
			}
			if(isset($location) && !empty($location)){
				$where_clause['location.id'] = $location;
			}	
			$from_date = base64_decode($from).' 00:00:00';
			$to_date = base64_decode($to).' 23:59:59';
			
			if(isset($to) && !empty($to) && isset($from) && !empty($from)){
				$where_clause['booking_information.est_pickup_date'] = array('$lte' => MongoDATE(strtotime($to_date)),'$gte' => MongoDATE(strtotime($from_date)));
			}else if(isset($from) && !empty($from)){
				$where_clause['booking_information.est_pickup_date'] = array('$gte' => MongoDATE(strtotime($from_date)));
			}
			
			
			$limit = 10000;
			$rideDetails  = $this->rides_model->get_all_details(RIDES,$where_clause);
			#echo "<pre>";print_r($rideDetails->result());die;
			$rideArray = $rideDetails->result_array();
			$no_of_rows = count($rideDetails->result_array());
			$no_of_sheets = floor($no_of_rows/$limit);
			if($no_of_rows%$limit > 0){
				$no_of_sheets++;
			}
			$ride_dis_mi = 'Ride Distance ('.$this->data['d_distance_unit'].')';
			$headers_array = array('Ride ID','Type','Booking Date','Ride Date','Ride Status','Username','User Email','Driver Name','Driver Email','Car Type','Pickup Location','Drop Location','Total Fare (USD)','Coupon Used (USD)','Wallet Used (USD)','Total Fare Paid (USD)','Service Tax (USD)','Tips Amount (USD)','Pay Status',$ride_dis_mi,'Ride Duration (mins)','Paid By','Amount in Site','Amount in Driver','Site Revenue','Driver Revenue');
			
			if($ride_actions == 'Cancelled' ){
				array_push($headers_array,"Cancelled By","Cancellation Reason");
			}
			
			#$this->excel->getActiveSheet()->fromArray($headers_array);
			
			$next_limit = 0;
			for($i=0; $i<$no_of_sheets; $i++){
				$this->excel->setActiveSheetIndex($i);
				$current_limit = $next_limit;
				/* Setting Header Name */
				$headerLetter = 'A';
				foreach($headers_array as $key => $val){
				$this->excel->getActiveSheet()->setCellValue($headerLetter++."1", $val);
				}
				$this->excel->getActiveSheet()->getStyle('A1:'.$headerLetter.'1')->getFont()->setBold(true);
				$this->excel->getActiveSheet()->getStyle('A1:'.$headerLetter.'1')->getFont()->setSize(12);
				
				/* Setting Header Name --- Ends here */
				
				$m = $i+1;
				$next_limit = $m*$limit;
				$row = 2;
				foreach($rideArray as $key => $val){
					if($key >= $current_limit && $key < $next_limit){
						$contentLetter = 'A';
						#echo 	"\n".$row;
						#echo "\n".$contentLetter++.$row;
						$ride_id = (string)$rideArray[$key]['ride_id'];
						$this->excel->getActiveSheet()->setCellValue($contentLetter.$row, $ride_id);
						$contentLetter++;
						
						$type = (string)$rideArray[$key]['type'];
						$this->excel->getActiveSheet()->setCellValue($contentLetter.$row, $type);
						$contentLetter++;
						
						if(isset($rideArray[$key]['booking_information']['booking_date']) && $rideArray[$key]['booking_information']['booking_date'] != ''){
							$booking_date = date('Y-m-d H:i:s',MongoEPOCH($rideArray[$key]['booking_information']['booking_date']));
						}else{
							$booking_date = 'NA';
						}
						$this->excel->getActiveSheet()->setCellValue($contentLetter.$row, $booking_date);
						$contentLetter++;
						
						if(isset($rideArray[$key]['booking_information']['pickup_date']) && $rideArray[$key]['booking_information']['pickup_date'] != ''){
							$pickup_date = date('Y-m-d H:i:s',MongoEPOCH($rideArray[$key]['booking_information']['pickup_date']));
						}else{
							$pickup_date = 'NA';
						}
						$this->excel->getActiveSheet()->setCellValue($contentLetter.$row, $pickup_date);
						$contentLetter++;
						
						$ride_status = $rideArray[$key]['ride_status'];
						$this->excel->getActiveSheet()->setCellValue($contentLetter.$row, $ride_status);
						$contentLetter++;
						
						$user_name = $rideArray[$key]['user']['name'];
						$this->excel->getActiveSheet()->setCellValue($contentLetter.$row, $user_name);
						$contentLetter++;
						
						$useremail = $rideArray[$key]['user']['email'];
						$this->excel->getActiveSheet()->setCellValue($contentLetter.$row, $useremail);
						$contentLetter++;
						
						if(isset($rideArray[$key]['driver']['name']) && $rideArray[$key]['driver']['name'] != ''){
							$driver_name = $rideArray[$key]['driver']['name'];
						}else{
							$driver_name = 'NA';
						}
						$this->excel->getActiveSheet()->setCellValue($contentLetter.$row, $driver_name);
						$contentLetter++;
						
						if(isset($rideArray[$key]['driver']['email']) && $rideArray[$key]['driver']['email'] != ''){
							$driver_email = $rideArray[$key]['driver']['email'];
						}else{
							$driver_email = 'NA';
						}
						$this->excel->getActiveSheet()->setCellValue($contentLetter.$row, $driver_email);
						$contentLetter++;
						
						$service_type = $rideArray[$key]['booking_information']['service_type'];
						$this->excel->getActiveSheet()->setCellValue($contentLetter.$row, $service_type);
						$contentLetter++;
						
						if(isset($rideArray[$key]['booking_information']['pickup']['location']) && $rideArray[$key]['booking_information']['pickup']['location'] != ''){
							$pickup_location = $rideArray[$key]['booking_information']['pickup']['location'];
						}else{
							$pickup_location = 'NA';
						}
						$this->excel->getActiveSheet()->setCellValue($contentLetter.$row, $pickup_location);
						$contentLetter++;
						
						if(isset($rideArray[$key]['booking_information']['drop']['location']) && $rideArray[$key]['booking_information']['drop']['location'] != ''){
							$drop_location = $rideArray[$key]['booking_information']['drop']['location'];
						}else{
							$drop_location = 'NA';
						}
						$this->excel->getActiveSheet()->setCellValue($contentLetter.$row, $drop_location);
						$contentLetter++;
						
						
						if(isset($rideArray[$key]['total']['grand_fare']) && $rideArray[$key]['total']['grand_fare'] != ''){
							$grand_fare = $rideArray[$key]['total']['grand_fare'];
						}else{
							$grand_fare = 0;
						}
						$this->excel->getActiveSheet()->setCellValue($contentLetter.$row, $grand_fare);
						$contentLetter++;
						
						if(isset($rideArray[$key]['total']['coupon_discount']) && $rideArray[$key]['total']['coupon_discount'] != ''){
							$coupon_discount = $rideArray[$key]['total']['coupon_discount'];
						}else{
							$coupon_discount = 0;
						}
						$this->excel->getActiveSheet()->setCellValue($contentLetter.$row, $coupon_discount);
						$contentLetter++;
						
						if(isset($rideArray[$key]['total']['wallet_usage']) && $rideArray[$key]['total']['wallet_usage'] != ''){
							$wallet_usage = $rideArray[$key]['total']['wallet_usage'];
						}else{
							$wallet_usage = 0;
						}
						$this->excel->getActiveSheet()->setCellValue($contentLetter.$row, $wallet_usage);
						$contentLetter++;
						
						
						if(isset($rideArray[$key]['total']['paid_amount']) && $rideArray[$key]['total']['paid_amount'] != ''){
							$paid_amount = $rideArray[$key]['total']['paid_amount'];
						}else{
							$paid_amount = 0;
						}
						$this->excel->getActiveSheet()->setCellValue($contentLetter.$row, $paid_amount);
						$contentLetter++;
						
						if(isset($rideArray[$key]['total']['service_tax']) && $rideArray[$key]['total']['service_tax'] != ''){
							$service_tax = $rideArray[$key]['total']['service_tax'];
						}else{
							$service_tax = 0;
						}
						$this->excel->getActiveSheet()->setCellValue($contentLetter.$row, $service_tax);
						$contentLetter++;
						
						if(isset($rideArray[$key]['total']['tips_amount']) && $rideArray[$key]['total']['tips_amount'] != ''){
							$tips_amount = $rideArray[$key]['total']['tips_amount'];
						}else{
							$tips_amount = 0;
						}
						$this->excel->getActiveSheet()->setCellValue($contentLetter.$row, $tips_amount);
						$contentLetter++;
						
						
						if(isset($rideArray[$key]['pay_status']) && $rideArray[$key]['pay_status'] != ''){
							$pay_status = $rideArray[$key]['pay_status'];
						}else{
							$pay_status = 'NA';
						}
						$this->excel->getActiveSheet()->setCellValue($contentLetter.$row, $pay_status);
						$contentLetter++;
						
						if(isset($rideArray[$key]['summary']['ride_distance']) && $rideArray[$key]['summary']['ride_distance'] != ''){
							$ride_distance = $rideArray[$key]['summary']['ride_distance'];
						}else{
							$ride_distance = '0';
						}
						$this->excel->getActiveSheet()->setCellValue($contentLetter.$row, $ride_distance);
						$contentLetter++;
						
						if(isset($rideArray[$key]['summary']['ride_duration']) && $rideArray[$key]['summary']['ride_duration'] != ''){
							$ride_duration = $rideArray[$key]['summary']['ride_duration'];
						}else{
							$ride_duration = '0';
						}
						$this->excel->getActiveSheet()->setCellValue($contentLetter.$row, $ride_duration);
						$contentLetter++;
						
						if(isset($rideArray[$key]['pay_summary']['type']) && $rideArray[$key]['pay_summary']['type'] != ''){
							$pay_summary = $rideArray[$key]['pay_summary']['type'];
						}else{
							$pay_summary = 'NA';
						}
						$this->excel->getActiveSheet()->setCellValue($contentLetter.$row, $pay_summary);
						$contentLetter++;
						
						if(isset($rideArray[$key]['amount_detail']['amount_in_site']) && $rideArray[$key]['amount_detail']['amount_in_site'] != ''){
							$amount_in_site = $rideArray[$key]['amount_detail']['amount_in_site'];
						}else{
							$amount_in_site = 0;
						}
						$this->excel->getActiveSheet()->setCellValue($contentLetter.$row, $amount_in_site);
						$contentLetter++;
						
						if(isset($rideArray[$key]['amount_detail']['amount_in_driver']) && $rideArray[$key]['amount_detail']['amount_in_driver'] != ''){
							$amount_in_driver = $rideArray[$key]['amount_detail']['amount_in_driver'];
						}else{
							$amount_in_driver = 0;
						}
						$this->excel->getActiveSheet()->setCellValue($contentLetter.$row, $amount_in_driver);
						$contentLetter++;
						
						if(isset($rideArray[$key]['amount_commission']) && $rideArray[$key]['amount_commission'] != ''){
							$amount_commission = $rideArray[$key]['amount_commission'];
						}else{
							$amount_commission = 0;
						}
						$this->excel->getActiveSheet()->setCellValue($contentLetter.$row, $amount_commission);
						$contentLetter++;
						
						if(isset($rideArray[$key]['driver_revenue']) && $rideArray[$key]['driver_revenue'] != ''){
							$driver_revenue = $rideArray[$key]['driver_revenue'];
						}else{
							$driver_revenue = 0;
						}
						$this->excel->getActiveSheet()->setCellValue($contentLetter.$row, $driver_revenue);
						$contentLetter++;
						
						if($ride_actions == 'Cancelled' ){
							if(isset($rideArray[$key]['cancelled']['primary']['by']) && $rideArray[$key]['cancelled']['primary']['by'] != ''){
								$cancelled_by = $rideArray[$key]['cancelled']['primary']['by'];
							}else{
								$cancelled_by = 0;
							}
							$this->excel->getActiveSheet()->setCellValue($contentLetter.$row, $cancelled_by);
							$contentLetter++;
							
							if(isset($rideArray[$key]['cancelled']['primary']['text']) && $rideArray[$key]['cancelled']['primary']['text'] != ''){
								$cancelled_reason = $rideArray[$key]['cancelled']['primary']['text'];
							}else{
								$cancelled_reason = 0;
							}
							$this->excel->getActiveSheet()->setCellValue($contentLetter.$row, $cancelled_reason);
							$contentLetter++;
							
						}
						
						$row = $row +1;;
					}
					
					
				}
				
			/* Creating Multiple Sheets*/
			$this->excel->getActiveSheet()->setTitle('sheet'.$i);
			$this->excel->createSheet();
			
			}
			
				$filename='Ride Report '.date("Y-m-d").'.xls'; //save our workbook as this file name
				header('Content-Type: application/vnd.ms-excel'); //mime type
				header('Content-Disposition: attachment;filename="'.$filename.'"'); //tell browser what's the file name
				header('Cache-Control: max-age=0'); //no cache
							 
				//save it to Excel5 format (excel 2003 .XLS file), change this to 'Excel2007' (and adjust the filename extension, also the header mime type)
				//if you want to save it as .XLSX Excel 2007 format
				$objWriter = PHPExcel_IOFactory::createWriter($this->excel, 'Excel5');  
				//force user to download the Excel file without writing it to server's HD
				$objWriter->save('php://output');
		}
	}
	
	/**
    * 
    * Ride dashboard page
    *
    * @return HTML,ride dashboard page
    *
    **/ 
    public function ride_dashboard() {
        if ($this->checkLogin('C') == '') {
            redirect(COMPANY_NAME);
        } else {
            if ($this->lang->line('rides_dashboard') != '') 
		     $this->data['heading']= stripslashes($this->lang->line('rides_dashboard')); 
		     else  $this->data['heading'] = 'Rides Dashboard';
            
			/* Ride Statistics Informations */
            $condition = array('ride_status' => 'Completed');
            $this->data['completedRides'] = $this->rides_model->get_all_counts(RIDES, $condition);

            $condition = array('ride_status' => 'Booked');
            $this->data['upcommingRides'] = $this->rides_model->get_all_counts(RIDES, $condition);

            $this->data['onRides'] = $this->dashboard_model->get_on_rides();

            $condition = array('ride_status' => 'Cancelled', 'cancelled.primary.by' => 'User');
            $this->data['riderDeniedRides'] = $this->rides_model->get_all_counts(RIDES, $condition);

            $condition = array('ride_status' => 'Cancelled', 'cancelled.primary.by' => 'Driver');
            $this->data['driverDeniedRides'] = $this->rides_model->get_all_counts(RIDES, $condition);
			
			$this->data['totalRides'] = $this->rides_model->get_all_counts(RIDES);
			
			$this->data['unfilledRides'] = $this->rides_model->get_all_counts(RIDE_STATISTICS);
			
			
			/* Unfilled Rides Map */
				
				$center=$this->config->item('latitude').','.$this->config->item('longitude');
				$coordinates=array(floatval($this->config->item('longitude')),floatval($this->config->item('latitude')));
				
				$condition=array('status'=>'Active');
				$category = $this->rides_model->get_selected_fields(CATEGORY,$condition,array('name','image'));
				$availCategory=array();
				if($category->num_rows()>0){
					foreach($category->result() as $cat){
						$availCategory[(string)$cat->_id]=$cat->name;
					}
				}
				
				$address=$this->input->post('location');
				$date_from=$this->input->post('date_from');
				$date_to=$this->input->post('date_to');
				/*Get latitude and longitude for an address*/
				if($address!=''){
					$address = str_replace(" ", "+", $address);
					$google_map_api='AIzaSyC5YIg8-Yk_zqjzWpFyZrgYuzzjTCBJV7k';
					$json = file_get_contents("http://maps.google.com/maps/api/geocode/json?address=$address&sensor=false");
					$jsonArr = json_decode($json);
					$lat = $jsonArr->{'results'}[0]->{'geometry'}->{'location'}->{'lat'};
					$lang = $jsonArr->{'results'}[0]->{'geometry'}->{'location'}->{'lng'};
					/*Get latitude and longitude for an address*/
					$location=array($lat,$lang);
					$coordinates=array_reverse($location);
					$center=@implode($location,',');
				}
				
				$matchArr = array();
				if(($date_from !=''&& $date_to !='')){
					$matchArr = array(
					'$match' => array('ride_time' => array('$gte' => MongoDATE(strtotime($date_from)),'$lte' => MongoDATE(strtotime($date_to.' 23:59:59')))));
				}else if($date_from != ''){
					$matchArr = array(
					'$match' => array('ride_time' => array('$gte' => MongoDATE(strtotime($date_from.' 00:00:00')))));
				}
				if(!empty($coordinates) & $coordinates[0]!='') {
				$rideDetails = $this->rides_model->get_unfilled_rides($coordinates,$matchArr); 
				}else{
				 $this->setErrorMessage('error', 'No location Found','admin_no_location');
                  redirect(COMPANY_NAME.'/rides/ride_dashboard');
				}
				$this->load->library('googlemaps');

				$config['center'] =$center;
				#$config['zoom'] = 'auto';
				$config['places'] = TRUE;
				$config['cluster'] = TRUE;
				$config['language'] = $this->data['langCode'];
				$config['placesAutocompleteInputID'] = 'location';
				$config['placesAutocompleteBoundsMap'] = TRUE;
				$this->googlemaps->initialize($config);
				$avail = 0;
				$unfilled = 0;
				$onride = 0;
				$catArr = array();
				
				if(!empty($rideDetails['result'])){
					foreach($rideDetails['result'] as $ride){
						$loc=array_reverse($ride['location']);
						$latlong=@implode($loc,',');
						$marker = array();
						$marker['position'] = $latlong;
						$current=time()-300;
				   
						$user_name = '';
						$checkUser = $this->rides_model->get_selected_fields(USERS,array('_id' => MongoID($ride['user_id'])),array('user_name','phone_number','country_code','email'));
						if($checkUser->num_rows() > 0 ){
							$user_name = $checkUser->row()->user_name;
							$phone_number = $checkUser->row()->phone_number;
							$country_code = $checkUser->row()->country_code;
							$email = $checkUser->row()->email;
						}
							 
							
						$unfilled++;
						$marker['icon'] = base_url().'images/pin-unavailable.png';
						
						$marker['icon_scaledSize'] = '25,25';
						$catS = "";
						if(array_key_exists((string)$ride['category'],$availCategory)){
							$catS = $availCategory[(string)$ride['category']];
						}
						
						$marker['infowindow_content'] ="<div style='width:200px !important;height:auto!important;'><span style='font-weight:700;font-size:13px;color:#FF5733;display:block;padding-bottom:5px;'>".$user_name."</span><span style='font-weight:400;font-size:13px;color:#999933;display:block;padding-bottom:5px;;'>".$country_code.$phone_number."</span><span style='font-weight:400;font-size:13px;color:#999933;display:block;padding-bottom:5px;;'>".$email."</span>".trim(preg_replace( "/\r|\n/", "", $ride['pickup_address'])).'<span style="font-weight:700;font-size:13px;color:green;display:block;padding:5px 0;">'.$catS."</span><span style='font-weight:700;font-size:13px;color:#999933;display:block;padding-bottom:5px;'>".date('Y-m-d h:i:s a',MongoEPOCH($ride['ride_time']))."</span></div>";
						
						$this->googlemaps->add_marker($marker);
					
						
						if(array_key_exists((string)$ride['category'],$catArr)){
							$catArr[(string)$ride['category']]['count'] = $catArr[(string)$ride['category']]['count']+1;
						}else{
							$catArr[(string)$ride['category']]['count'] = 1;
							$catArr[(string)$ride['category']]['name'] = $catS;
						}
						
					}
				}

					$this->data['map'] = $this->googlemaps->create_map();
					$this->data['unfilled_rides'] = $unfilled;
					$this->data['address'] = urldecode($address);
					$this->data['date_from'] = urldecode($date_from);
					$this->data['date_to'] = urldecode($date_to);
					$this->data['categories'] = $catArr;
					
					#$this->load->view('admin/rides/map_unfilled_rides',$this->data);
				
			/* Unfilled Rides Map */
			
			$this->load->view(COMPANY_NAME.'/rides/ride_dashboard', $this->data);
        }
    }

    /**
    * 
    * Display driver reviews
    *
    * @param string $driver_id  driver MongoDB\BSON\ObjectId
    * @return HTTP REDIRECT, Display driver reviews
    *
    **/
	public function view_driver_reviews(){
		if ($this->checkLogin('C') == '') {
            redirect(COMPANY_NAME);
        }else {	
			$driver_id = $this->uri->segment(4);
			if($driver_id != ''){
				
				$get_review_options = $this->review_model->get_all_details(REVIEW_OPTIONS,array('option_holder' => 'driver')); 
				$reviewsList = array();
				$getCond = array('driver.id' => $driver_id,'driver_review_status' => 'Yes');
				$get_ratings = $this->review_model->get_selected_fields(RIDES,$getCond,array('ratings.driver','driver_review_status'));   #echo '<pre>'; print_r($get_ratings->result()); die;
				if($get_ratings->num_rows() > 0){
					$usersTotalRates = 0; $commonNumTotal = 0; 
					foreach($get_review_options->result() as $options){
						$tot_no_of_Rates  = 0; $totalRates = 0; $reviewStatus = 'No'; 
						foreach($get_ratings->result() as $ratings){ 
							if(isset($ratings->driver_review_status)){
								if($ratings->driver_review_status == 'Yes'){
									$reviewStatus = $ratings->driver_review_status;
									foreach($ratings->ratings['driver']['ratings'] as $rateOptions){  
										if($options->option_id == $rateOptions['option_id']){ 
											$commonNumTotal++; $tot_no_of_Rates++;
											$totalRates = $totalRates + $rateOptions['rating'];
											$usersTotalRates = $usersTotalRates + $rateOptions['rating'];
										}
									}
								}
							}
						}
						$avgRates = $totalRates;
						if($tot_no_of_Rates>0) $avgRates = $totalRates/$tot_no_of_Rates;
						
						$option_name = $options->option_name;
						if(isset($options->option_name_languages)){
							$langKey=$this->data['langCode'];
							$arrVal = $options->option_name_languages;
							if(array_key_exists($langKey,$arrVal)){
								if($options->option_name_languages[$langKey]!=""){
									$option_name = $options->option_name_languages[$langKey];
								}
							}
						}
						
						$rateArr = array('review_post_status' => $reviewStatus,
													 'no_of_rates' => $tot_no_of_Rates,
													 'IndtotalRates' => $totalRates,
													 'avg_rates' => $avgRates,
													 'option_holder' => $options->option_holder,
													 'option_name' => $option_name, 
													 'status' => $options->status,
													 'option_id' => $options->option_id
													 );
						$reviewsList[] = $rateArr;
					}
					#echo $usersTotalRates.'---'.$commonNumTotal;
					
					$commonAvgRates = $usersTotalRates/$commonNumTotal;
					$summaryRateArr = array('totalRates' => $usersTotalRates,'commonNumTotal' => $get_ratings->num_rows(),'commonAvgRates' => $commonAvgRates);
					$this->data['reviewsSummary'] = $summaryRateArr; 
					$this->data['reviewsList'] = $reviewsList;
					#echo '<pre>'; print_r($summaryRateArr); echo '<pre>'; print_r($reviewsList); die;
					if ($this->lang->line('admin_driver_rating_summary') != ''){
						$heading = stripslashes($this->lang->line('admin_driver_rating_summary')); 
					}else{
						$heading = 'Driver Ratings Summary';
					}
					$this->data['heading'] = $heading;
					$this->load->view(COMPANY_NAME.'/rides/view_review_summary',$this->data);
				} else {
					$this->setErrorMessage('error','No ratings found for this driver','admin_review_no_rating_found_driver');
					redirect($_SERVER['HTTP_REFERER']);
				}
				
			} else {
				$this->setErrorMessage('error','Invalid link','admin_review_invalid_link');
				redirect($_SERVER['HTTP_REFERER']);
			}
		}
	}
    
    
    public function rides_grid_view() {  
        if ($this->checkLogin('C') == '') {
            redirect(COMPANY_NAME);
        } else { 
		   $startDate = strtotime(date('Y-m-d H:i:s',strtotime('-24 hours'))); 
		   $cond = array('booking_information.booking_date' => array('$gte' =>  MongoDATE($startDate),'$lte'                => MongoDATE(time())),
                    'company_id' =>MongoID($this->checkLogin('C'))
            ); 
			$this->data['ridesList'] = $ridesList = $this->rides_model->get_selected_fields(RIDES,$cond,array('user','driver','booking_information','ride_status','ride_id','type'),array('booking_information.booking_date'=>"DESC"));
			if ($this->lang->line('dash_view_rides') != '') 
		    $this->data['heading']= stripslashes($this->lang->line('dash_view_rides')); 
		    else  $this->data['heading'] = 'View Rides';
            $this->load->view(COMPANY_NAME.'/rides/rides_grid_view', $this->data);
        }
    }
	
	function get_rides_ajax(){
		$returnArr['status'] = '0';
		$returnArr['response'] = '';
		$authkey = $this->input->post('authkey');
		if($authkey == APP_NAME){
			$startDate = strtotime(date('Y-m-d H:i:s',strtotime('-24 hours'))); 
	        $cond = array('booking_information.booking_date' => array('$gte' =>  MongoDATE($startDate),'$lte'                => MongoDATE(time())),
                    'company_id' =>MongoID($this->checkLogin('C'))
            );
			$ridesList = $this->rides_model->get_selected_fields(RIDES,$cond,array('user','driver','booking_information','ride_status','ride_id','type'),array('booking_information.booking_date'=>"DESC"));
			$booked_rides = '';  $confimed_rides = '';  $on_rides = '';  $completed_rides = '';
			
			foreach($ridesList->result() as $rides){
				$driver_name = '--';
				if(isset($rides->driver['name'])){
					$driver_name = $rides->driver['name'];
				}
				
				$booked_date = '--';
				if(isset($rides->booking_information['service_type'])){
					$booked_date = date('h:i A',MongoEPOCH($rides->booking_information['booking_date']));
				}
				if(isset($rides->type) && $rides->type =='Now'){
                $ridetype = 'Ride Now';
                
                }else{
                $ridetype = 'Ride Later';
                }
				if($rides->ride_status == 'Booked'){
					$booked_rides.= '<tr>
					<td>'.$rides->user['name'].'</td>
					<td>'.$rides->ride_id.'<span class="service_type">'.$rides->booking_information['service_type'].'</span><span class="rides_type">'.$ridetype.'</span></td>
					<td>'.$booked_date.'</td>
					</tr>';
				}
				if($rides->ride_status == 'Confirmed' || $rides->ride_status == 'Accepted'){
					$confimed_rides.= '<tr>
					<td>'.$rides->user['name'].'</td>
					<td>'.$driver_name.'</td>
					<td>'.$rides->ride_id.'<span class="service_type">'.$rides->booking_information['service_type'].'</span><span class="rides_type">'.$ridetype.'</span></td>
					<td>'.$booked_date.'</td>
					</tr>';
				}
				if($rides->ride_status == 'Arrived' || $rides->ride_status == 'Onride'){
					if($rides->ride_status == 'Arrived') $class_name="tr-yellow"; if($rides->ride_status == 'Onride') $class_name="tr-blue";
					$on_rides.= '<tr class="'.$class_name.'">
					<td>'.$rides->user['name'].'</td>
					<td>'.$driver_name.'</td>
					<td>'.$rides->ride_id.'<span class="service_type">'.$rides->booking_information['service_type'].'</span><span class="rides_type">'.$ridetype.'</span></td>
					<td>'.$booked_date.'</td>
					</tr>';
				}
				
				if($rides->ride_status == 'Completed' || $rides->ride_status == 'Cancelled'){
					if($rides->ride_status == 'Completed') $class_name="tr-green"; if($rides->ride_status == 'Cancelled') $class_name="tr-red";
					$completed_rides.= '<tr class="'.$class_name.'">
					<td>'.$rides->user['name'].'</td>
					<td>'.$driver_name.'</td>
					<td>'.$rides->ride_id.'<span class="service_type">'.$rides->booking_information['service_type'].'</span><span class="rides_type">'.$ridetype.'</span></td>
					<td>'.$booked_date.'</td>
					</tr>';
				}
			}
			$returnArr['status'] = '1';
			$returnArr['booked_rides'] = $booked_rides;
			$returnArr['confimed_rides'] = $confimed_rides;
			$returnArr['on_rides'] = $on_rides;
			$returnArr['completed_rides'] = $completed_rides;
		} else {
			$returnArr['response'] = 'Authentication Failed';
		}
		echo json_encode($returnArr);
	}
}
/* End of file rides.php */
/* Location: ./application/controllers/company/rides.php */