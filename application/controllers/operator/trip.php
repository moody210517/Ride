<?php if (!defined('BASEPATH')) exit('No direct script access allowed');
/**
*
*   Rides Management in operator panel
* 
*   @package    CI
*   @subpackage Controller
*   @author Casperon
*
**/
class Trip extends MY_Controller {

    function __construct() {
        parent::__construct();
        $this->load->helper(array('cookie', 'date', 'form','ride_helper','distcalc_helper'));
        $this->load->library(array('encrypt', 'form_validation'));
        $this->load->model(array('app_model', 'operators_model','rides_model','dashboard_model'));
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
				
				$condition = array('_id' => $this->checkLogin('O'));
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
    * Redirect to operator's ride List
    *
	* @param string $per_page  per page pagination per page
	* @param string $act action filter
	* @param string $list rides list
    * @return HTTP REDIRECT operator's ride List
    *
    **/
	public function display_trips(){
		if ($this->checkLogin('O') == ''){
				redirect(OPERATOR_NAME);
		}else {
            $ride_act = '';
            $from = '';
            $to = '';
            $location = '';
			$filter = "";
			$filterKV = array();
			if (isset($_GET['date_range'])) {
				$from = $this->input->get('date_range');
				$filter = "filter";
				$filterKV[] = "date_range=".$from;
			}
			if (isset($_GET['dateto'])) {
				$to = $this->input->get('dateto');
				$filterKV[] = "dateto=".$to;
			}
				
			
			$filter_array = array('from' => $from, 'to' => $to);
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
				if ($this->lang->line('dash_rider_cancelled_rides_list') != '')  $this->data['heading']= stripslashes($this->lang->line('dash_rider_cancelled_rides_list'));  else  $this->data['heading'] = 'Rider Cancelled Rides List';
            } else if ($ride_act == 'driverCancelled') {
				if ($this->lang->line('dash_driver_cancelled_rides_list') != '')  $this->data['heading']= stripslashes($this->lang->line('dash_driver_cancelled_rides_list'));  else  $this->data['heading'] = 'Driver Cancelled Rides List';
			} else if ($ride_act == 'Expired') {
				if ($this->lang->line('heading_expired_ride_list') != '')  $this->data['heading']= stripslashes($this->lang->line('heading_expired_ride_list'));  else  $this->data['heading'] = 'Expired Rides List';
            } else {
				if ($this->lang->line('dash_all_rides_list') != '')  $this->data['heading']= stripslashes($this->lang->line('dash_all_rides_list'));  else  $this->data['heading'] = 'All Rides List';
            }
            
            $type = $this->input->get('type');
            $value = $this->input->get('value');
            $this->data['type'] = $type;
            $filterCondition = array();
		    $operators_id =  MongoID($this->checkLogin('O'));
          	$filterCondition = array('operator_id' => $operators_id);
            if($type == 'driver_name'){							
                if($this->input->get('value') != ''){								
                    $filter = "filter";
                    $this->data['value'] = $value = $this->input->get('value');
                    $filter_condition = array('driver.name'=>array('$regex'=> $value,'$options'=> 'i'));
                }
            } else if($type == 'driver_email'){							
                if($this->input->get('value') != ''){
                    $filter = "filter";
                    $this->data['value'] = $value = $this->input->get('value');
                    $filter_condition = array('driver.email'=>array('$regex'=> $value,'$options'=> 'i'));
                }
            } else if($type == 'user_name'){							
                if($this->input->get('value') != ''){
                    $filter = "filter";
                    $this->data['value'] =$value = $this->input->get('value');
                    $filter_condition = array('user.name'=>array('$regex'=> $value,'$options'=> 'i'));
                }
            } else if($type == 'user_email'){							
                if($this->input->get('value') != ''){
                    $filter = "filter";
                    $this->data['value'] = $value = $this->input->get('value');
                    $filter_condition = array('user.email'=>array('$regex'=> $value,'$options'=> 'i'));
                }
            } else if($type == 'driver_location'){						
                if($this->input->get('locations_id') != ''){
                    $filter = "filter";
                    $this->data['locations_id'] = $locations_id = $this->input->get('locations_id');
                    $filter_condition = array('location.id'=>$locations_id);
                }
            } else if($type == 'vehicle_type'){
                if($this->input->get('vehicle_category') != ''){
                    $filter = "filter";
                    $this->data['vehicle_category'] = $vehicle_category = $this->input->get('vehicle_category');
                    $filter_condition = array('booking_information.service_id'=>$vehicle_category);
                }
            } else if($type == 'ride_id'){  
                if($this->input->get('value') != ''){
                    $filter = "filter";   
                    $this->data['value'] = $ride_id = $this->input->get('value');  
                    $filter_condition = array('ride_id'=>$ride_id);
                }
            }
			$this->data['filter'] = $filter;
           
			$rides_total = $this->operators_model->get_operators_rides_total($operators_id,$ride_act); 
            $export_type = $this->input->get('export_type');
            if ($rides_total->num_rows() > 500 && $export_type != 'all') {
                $limit = 500;

             $this->data['ridesList'] = $ridesList = $this->operators_model->get_operators_rides($operators_id,$limit,$offsetVal,$ride_act); 
				
				
				$fv = "";
				if(!empty($filterKV)){
					$fv = @implode("&",$filterKV);
				}
				
				$searchbaseUrl = OPERATOR_NAME.'/trip/display_trips?act=' . $ride_act.'&'.$fv.'&type='.$type.'&value='.$value;
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
               $this->data['ridesList'] = $ridesList = $this->operators_model->get_operators_rides($operators_id,'','',$ride_act); 
            }
			//print_R($this->data['ridesList']->result());die;
            $this->data['offsetVal'] = $offsetVal;
       
            $cabCats = $this->rides_model->get_selected_fields(CATEGORY, array(), array('_id', 'name'))->result();
            $cabsTypeArr = array();
            foreach ($cabCats as $cab) {
                    $cabId = (string) $cab->_id;
                    $cabsTypeArr[$cabId] = $cab;
            }
            $this->data['cabCats'] = $cabsTypeArr;
    
            $this->data['locationsList'] = $this->operators_model->get_selected_fields(LOCATIONS, array('status' => 'Active'),array('city','_id'),array('city' => 1));
   
            if(isset($_GET['export']) && $_GET['export'] == 'excel'){
                    $this->load->helper('export_helper');
                    export_rides_list($ridesList,$ride_act);
            }
				$this->load->view(OPERATOR_NAME.'/trip/display_trips',$this->data);
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
	public function view_trip(){
		if ($this->checkLogin('O') == ''){
				redirect(OPERATOR_NAME);
		}else {
				if($this->lang->line('operator_view_trip') != '')
				{
						$operator_view_trip = stripslashes($this->lang->line('operator_view_trip'));
				}
				else  { 
						$operator_view_trip = 'View Trip';
				}
				$this->data['heading'] = $operator_view_trip;
				$rides_id = $this->uri->segment(4,0);
				try{
						$condition = array('_id' => MongoID($rides_id));
						$this->data['rides_details'] = $rides_details = $this->app_model->get_all_details(RIDES,$condition);
						if ($this->data['rides_details']->num_rows() == 1){
								$this->load->view(OPERATOR_NAME.'/trip/view_trip',$this->data);
						}else {
								$this->setErrorMessage('error','No records found','driver_no_records_found');
								redirect(OPERATOR_NAME.'/trip/display_trips');
						}
				}catch(MongoException $e){
						$this->setErrorMessage('error','No records found','driver_no_records_found');
						redirect(OPERATOR_NAME.'/trip/display_trips');
				}
		}
	}
	
    /**
    * 
    * operator panel booking page
    *
    * @return HTML,operator panel booking page
    *
    **/ 
    public function init_booking_form() {
		if ($this->checkLogin('O') == '') {
				redirect(OPERATOR_NAME);
		} else {
				if($this->lang->line('operator_choose_customers') != ''){
						$operator_choose_customers = stripslashes($this->lang->line('operator_choose_customers'));
				} else  { 
						$operator_choose_customers = 'Choose customer';
				}
				$this->data['heading'] = $operator_choose_customers;
				$pay_mode = 'cash';
				if($this->input->get('pay_mode')){
					$pay_mode = $this->input->get('pay_mode');
				}
			
				$customer_type = 'new';
				if($this->input->get('customer_type')){
						$customer_type = $this->input->get('customer_type');
				}
		
				$this->data['client_id'] = '';
			
				$this->load->view(OPERATOR_NAME.'/trip/init_booking', $this->data);
		}
    }
	/**
    * 
    * operator panel booking new ride page
    *
    * @param string $customer_type  New/old customer
    * @param string $user_id  user id MongoDB\BSON\ObjectId
    * @return HTML,operator panel booking new ride page
    *
    **/ 
    public function book_trip() { 
        if ($this->checkLogin('O') == '') {
            redirect(OPERATOR_NAME);
        } else { 
				$customer_type = $this->input->post('customer_type');
				$user_id = $this->input->post('user_id');
	
				$redirectTags = '?customer_type='.$customer_type.'&user_id='.$user_id;
	
				if($customer_type  == 'existing' && $user_id == ''){
						$this->setErrorMessage('error','Please choose customer.','operator_book_choose_cus');
						redirect(OPERATOR_NAME.'/trip/init_booking_form'.$redirectTags);
				}
				$user_info = array();
				$is_user = FALSE;
				if($this->input->post('customer_type') == 'existing' && $user_id != ''){
						$user_info = $this->app_model->get_selected_fields(USERS, array('_id' => MongoID($user_id)),array('user_name','email','country_code','phone_number'));
						$is_user = TRUE;
				}
				$this->data['categoryList'] = $this->app_model->get_all_details(CATEGORY, array('status' => 'Active'));
				if($this->lang->line('operator_book_new_trip') != '')
				{
						$operator_book_new_trip = stripslashes($this->lang->line('operator_book_new_trip'));
				}
				else  { 
						$operator_book_new_trip = 'Booking a new trip';
				}
            $this->data['heading'] = $operator_book_new_trip;
			$this->data['user_info'] = $user_info;
			$this->data['is_user'] = $is_user;
            $this->load->view(OPERATOR_NAME.'/trip/booking', $this->data);
        }
    }
	/**
    * 
    * cancellation reason list
    *
    * @param string $cancelled_by  driver/user/operator
    * @return JSON,cancellation reason list
    *
    **/ 
	public function get_cancellation_reason(){
		$retArr['status'] = '0';
		$cancelled_by = strtolower($this->input->post('cancelled_by'));
		if ($this->lang->line('dash_operator_choose_cancellation_reason') != '') $dash_operator_choose_cancellation_reason= stripslashes($this->lang->line('dash_operator_choose_cancellation_reason')); else $dash_operator_choose_cancellation_reason= 'Choose cancellation reason';
		
		$resArr = '<option>'.$dash_operator_choose_cancellation_reason.'</option>';
		if($cancelled_by != ''){
				$reasonlist = $this->app_model->get_all_details(CANCELLATION_REASON, array('type' => $cancelled_by,'status' => 'Active'));
				if($reasonlist->num_rows() > 0){
						$retArr['status'] = '1';
						foreach($reasonlist->result() as $reasons){
								$resArr.= '<option value="'.(string)$reasons->_id.'">'.$reasons->reason.'</option>';
						}
				}
		}
		$retArr['reasons'] = $resArr ;
		echo json_encode($retArr); exit;
	}
	/**
    * 
    * cancel a ride from operator panel
    *
    * @param string $user_id  user id MongoDB\BSON\ObjectId
    * @param string $ride_id  ride id
    * @param string $reason  reason of cancellation
    * @param string $cancelled_by  cancelled by driver/user/operator
    * @return HTTP REDIRECT , operator ride list page
    *
    **/ 
    public function cancelling_ride_old() {
		if ($this->checkLogin('O') == '') {
			redirect(OPERATOR_NAME);
		}
        $returnArr['status'] = '0';
        $returnArr['response'] = '';
        try {
            $user_id = $this->input->post('user_id');
            $ride_id = $this->input->post('ride_id');
            $reason = $this->input->post('reason');
			$cancelled_by = $this->input->post('cancelled_by');

            if (is_array($this->input->post())) {
                $chkValues = count(array_filter($this->input->post()));
            } else {
                $chkValues = 0;
            }

            if ($chkValues >= 2) {
				$checkRide = $this->app_model->get_selected_fields(RIDES, array('ride_id' => $ride_id), array('ride_id', 'ride_status', 'booking_information', 'driver.id','user.id', 'coupon_used', 'coupon', 'cancelled'));
				 if ($checkRide->num_rows() == 1) {
					$Operator_id = $this->checkLogin('O');
					$user_id = $checkRide->row()->user['id'];
					$doAction = 0;
					if ($checkRide->row()->ride_status == 'Booked' || $checkRide->row()->ride_status == 'Confirmed' || $checkRide->row()->ride_status == 'Cancelled' || $checkRide->row()->ride_status == 'Arrived') {
						$doAction = 1;
						if ($checkRide->row()->ride_status == 'Cancelled') {
								if ($checkRide->row()->cancelled['primary']['by'] == $cancelled_by) {
										$doAction = 0;
								}
								if (isset($checkRide->row()->cancelled['secondary']['by'])) {
										if ($checkRide->row()->cancelled['secondary']['by'] == $cancelled_by) {
												$doAction = 0;
										}
								}
						}
					}
					if ($doAction == 1) {
						$reasonVal = $this->app_model->get_selected_fields(CANCELLATION_REASON, array('_id' => MongoID($reason)), array('reason'));
						if ($reasonVal->num_rows() > 0) {
							$reason_id = (string) $reasonVal->row()->_id;
							$reason_text = (string) $reasonVal->row()->reason;

							$isPrimary = 'No';
							if ($checkRide->row()->ride_status != 'Cancelled') {
								$rideDetails = array('ride_status' => 'Cancelled',
											'cancelled' => array('primary' => array('by' => $cancelled_by,
											'id' => $Operator_id,
											'reason' => $reason_id,
											'text' => $reason_text,
											'cancelled_by' => 'operator'
										)
									),
									'history.cancelled_time' => MongoDATE(time())
								);
								$isPrimary = 'Yes';
							} else if ($checkRide->row()->ride_status == 'Cancelled') {
									$rideDetails = array('cancelled.secondary' => array('by' => $cancelled_by,
											'id' => $Operator_id,
											'reason' => $reason_id,
											'text' => $reason_text,
											'cancelled_source' => 'operator'
										),
										'history.secondary_cancelled_time' => MongoDATE(time())
									);
							}
							$this->app_model->update_details(RIDES, $rideDetails, array('ride_id' => $ride_id));
						    if ($isPrimary == 'Yes') {
								if ($checkRide->row()->coupon_used == 'Yes') {
									$usage = array("user_id" => (string) $checkRide->row()->user['id'], "ride_id" => $ride_id);
									$promo_code = (string) $checkRide->row()->coupon['code'];
									$this->app_model->simple_pull(PROMOCODE, array('promo_code' => $promo_code), array('usage' => $usage));
								}
								if ($checkRide->row()->driver['id'] != '') {
										$driver_id = $checkRide->row()->driver['id'];
										$this->app_model->update_details(DRIVERS, array('mode' => 'Available'), array('_id' => MongoID($driver_id)));
								}
								$this->app_model->update_user_rides_count('cancelled_rides', $user_id);
								if ($checkRide->row()->driver['id'] != '') {
										$this->app_model->update_driver_rides_count('cancelled_rides', $driver_id);
								}
								$current_date = MongoDATE(strtotime(date("Y-m-d 00:00:00")));
								$field = array('ride_cancel.hour_' . date('H') => 1, 'ride_cancel.count' => 1);
								$this->app_model->update_stats(array('day_hour' => $current_date), $field, 1);
								

							if ($checkRide->row()->driver['id'] != '') {
										$driver_id = $driver_id;
										$driverVal = $this->app_model->get_selected_fields(DRIVERS, array('_id' => MongoID($driver_id)), array('_id', 'push_notification'));

									if (isset($driverVal->row()->push_notification)) {
										if ($driverVal->row()->push_notification != '') {
										$message = 'Operator cancelled this ride';
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
						}
						$returnArr['status'] = '1';
						$returnArr['response'] = 'Ride #'.$ride_id.' has been cancelled successfully';
					  } else {
							$returnArr['response'] = $this->format_string('You cannot do this action', 'you_cannot_do_this_action');
					  }
						} else {
								$returnArr['response'] = $this->format_string('You cannot do this actions', 'you_cannot_do_this_action');
						}
					} else {
							$returnArr['response'] = $this->format_string("This ride is not available", "ride_unavailable");
					}
	
			} else {
					$returnArr['response'] = $this->format_string("Some Parameters Missing", "some_parameters_missing");
			}
				} catch (MongoException $ex) {
						$returnArr['response'] = $this->format_string("Error in connection", "error_in_connnection");
				}
        
				if($returnArr['status'] == '1'){
						$this->setErrorMessage('success',$returnArr['response']);
				} else {
						$this->setErrorMessage('error',$returnArr['response']);
				} 
				redirect(OPERATOR_NAME.'/trip/display_trips');
    }
	/**
    * 
    * operator panel search ride page
    *
    * @param string $search_ride_id  ride id
    * @return HTML , operator panel search ride page
    *
    **/ 
	public function search_ride(){
		if ($this->checkLogin('O') == '') {
            redirect(OPERATOR_NAME);
         } else {
			if($this->lang->line('admin_search_ride') != '') 
		     $this->data['heading']= stripslashes($this->lang->line('admin_search_ride')); 
		     else  $this->data['heading'] = 'Search Ride';
			 
			 $this->data['ride_id'] = $ride_id = trim($this->input->get('search_ride_id'));
			 $this->data['rideFound'] = 'false';
			 if($ride_id != ''){
				$this->data['ride_details'] = $checkRide = $this->app_model->get_all_details(RIDES,array('ride_id'=>$ride_id));
				if($checkRide->num_rows()>0){
					$this->data['rideFound'] = 'true';
				} else {
					$this->setErrorMessage('error','Ride not found','error_message_ride_not_found');
					redirect(OPERATOR_NAME.'/trip/search_ride');
				}
			}
			if(isset($_GET['search_ride_id']) && $ride_id==""){
				$this->setErrorMessage('error','Please enter the valid ride id','error_message_ride_need');
				redirect(OPERATOR_NAME.'/trip/search_ride');
			}
			$this->load->view(OPERATOR_NAME.'/trip/search_ride',$this->data);
			
		}
	}
	/**
    * 
    * operator panel cancel ride page
    *
    * @param string $ride_id  ride id
    * @return HTML , operator panel cancel ride page
    *
    **/ 
	public function cancelling_ride_form(){
		if ($this->checkLogin('O') == '') {
            redirect(OPERATOR_NAME);
         } else {
		    if ($this->lang->line('dash_ride_cancelling_ride') != '') 
		    $this->data['heading']= stripslashes($this->lang->line('dash_ride_cancelling_ride')); 
		    else  $this->data['heading'] = 'Cancelling Ride';
			$this->data['ride_id'] = $ride_id = trim($this->input->get('ride_id'));
			$this->data['rideFound'] = 'false';
			$this->data['ride_details'] = $checkRide = $this->app_model->get_all_details(RIDES,array('ride_id'=>$ride_id));
			if($checkRide->num_rows()>0){
				$this->data['rideFound'] = 'true';
				$this->load->view(OPERATOR_NAME.'/trip/cancelling_ride',$this->data);
			}else{
				$this->setErrorMessage('error','Ride not found','error_message_ride_not_found');
				redirect(OPERATOR_NAME.'/trip/search_ride');
			}
		}
	}
	/**
    * 
    * cancellation reason list user wise
    *
    * @param string $user_type  user/driver
    * @return JSON , cancellation reason list
    *
    **/ 
	public function user_type_cancellation_reason(){
		$user_type = $this->input->post('user_type');
		$condition = array('type'=>$user_type);
		$cancel_reason = $this->app_model->get_all_details(CANCELLATION_REASON,$condition);
		$i = 0;
		if ($this->lang->line('select_an_option') != '') $reason1 = stripslashes($this->lang->line('select_an_option')); else $reason1 = 'Select an option';
		$returnArr[$i]['id'] = "";
		$returnArr[$i]['reason'] = $reason1;
		$langCode = $this->data['langCode'];
		if($user_type!=""){
			$i++;
			foreach($cancel_reason->result_array() as $reason){
				$returnArr[$i]= $reason;
				
					
				if($i > 0){
					$cancel_reason = $reason['reason'];
					if(isset($reason['name_languages'][$langCode]) && $reason['name_languages'][$langCode] != '' )$cancel_reason = $reason['name_languages'][$langCode];
					$returnArr[$i]['reason'] = $cancel_reason;
				}
				
				$returnArr[$i]['id'] = (string)$returnArr[$i]['_id'];
				unset($returnArr[$i]['_id']);
				$i++;
			}
		}
		echo json_encode($returnArr);
	}
	/**
    * 
    * operator panel end ride page
    *
    * @param string $ride_id  ride id
    * @return HTML, operator panel end ride page
    *
    **/ 
	public function end_ride_form(){
		if ($this->checkLogin('O') == '') {
            redirect(OPERATOR_NAME);
         } 
		$this->data['ride_id'] = $ride_id = $this->input->get('ride_id');
		$condition = array('ride_id' => $ride_id); 
		$this->data['ride_details'] = $checkRide = $this->app_model->get_selected_fields(RIDES,$condition,array('driver','booking_information'));
		if($checkRide->num_rows() == 1){
		
			 if ($this->lang->line('admin_end_ride') != '') 
		   $heading = stripslashes($this->lang->line('admin_end_ride')); 
		    else  $heading = 'End Ride';
		
			$this->data['heading'] = $heading.' #'.$ride_id;
			$this->load->view(OPERATOR_NAME.'/trip/ending_ride',$this->data);
		} else {
			$this->setErrorMessage('error','Ride not found','error_message_ride_not_found');
			redirect(OPERATOR_NAME.'/trip/search_ride?ride_id='.$ride_id);
		}
	}
	/**
    * 
    * operator panel end ride 
    *
    * @param string $ride_id  ride id
    * @param string $distance  ride distance
    * @param string $drop_time  ride drop time
    * @return HTTP REDIRECT, operator panel search ride page
    *
    **/ 
	public function ending_ride(){
		if ($this->checkLogin('O') == '') {
            redirect(OPERATOR_NAME);
         } 
		 $postedVals = $_POST;
		 $ride_id = $this->input->post('ride_id');
		 $distance = $this->input->post('distance');
		 $drop_time = strtotime($this->input->post('drop_time'));
		 $condition = array('ride_id' => $this->input->post('ride_id')); 
		 $checkRide = $this->app_model->get_selected_fields(RIDES,$condition,array('driver'));
		 if($checkRide->num_rows() == 1){
			$postedVals['driver_id'] = $checkRide->row()->driver['id'];
			$driver_id = $checkRide->row()->driver['id'];
			$checkDriver = $this->app_model->get_selected_fields(DRIVERS, array('_id' => MongoID($driver_id)), array('email','last_begin_time'));
			if(isset($checkDriver->row()->last_begin_time)){
				$dataArr = array('last_online_time' => MongoDATE(time()));
				$this->app_model->update_details(DRIVERS, $dataArr, array('_id' => MongoID($driver_id)));
                $last_begin_time=MongoEPOCH($checkDriver->row()->last_begin_time);
				update_mileage_system($driver_id,$last_begin_time,'customer-drop',$distance,$this->data['d_distance_unit'],$ride_id,$drop_time);
			}
			$result = $this->make_end_ride();

			if($result['status'] == '1'){
				$this->setErrorMessage('success','Ride has been ended successfully','admin_ride_has_ended_successfully');
			} else {
				$this->setErrorMessage('error',$result['response']);
			}
		 } else {
			$this->setErrorMessage('error','Ride not found','error_message_ride_not_found');
		 }
		 redirect(OPERATOR_NAME.'/trip/search_ride?ride_id='.$ride_id);
	}
	/**
    * 
    * operator panel end ride call back function
    *
    * @param string $ride_id  ride id
    * @param string $driver_id  driver id MongoDB\BSON\ObjectId
    * @param string $drop_lat  drop location latitude
    * @param string $drop_lon  drop location longitude
    * @param string $distance  distance in Km
    * @param string $wait_time  wait time frame in H:M:S
    * @return ARRAY, callback to end ride function
    *
    **/ 
    public function make_end_ride() {
		$driver_id = $this->input->post('driver_id');
		$ride_id = $this->input->post('ride_id');
		$drop_lat = $this->input->post('drop_lat');
		$drop_lon = $this->input->post('drop_lon');

		$interrupted = (string) $this->input->post('interrupted');
		$drop_loc = $this->input->post('drop_loc');
		$drop_time = $this->input->post('drop_time');

		$distance = $this->input->post('distance'); 
		$device_distance = $this->input->post('distance'); 
		$wait_time_frame = $this->input->post('wait_time'); 
		
		if(is_array($wait_time_frame)){
			$wait_time_frame = @implode(':',$wait_time_frame);
		}
		
		$travel_history = $this->input->post('travel_history'); 		
				
		$endTripArr = array("driver_id"=>$driver_id,
										"sour"=>'w',
										"ride_id"=>$ride_id,
										"drop_lat"=>$drop_lat,
										"drop_lon"=>$drop_lon,
										"distance"=>$distance,
										"wait_time_frame"=>$wait_time_frame,
										"travel_history"=>$travel_history
								);
		$finishRes = finish_the_trip($endTripArr);
		return $finishRes;
    }
	/**
    * 
    * Ride Cancellation from operator panel
    *
    * @param string $ride_id  ride id
    * @param string $cancelled_by User/driver
    * @param string $cancel_reason cancellation reason  
    * @return HTTP REDIRECT,Ride list page
    *
    **/
	public function cancelling_ride(){
		if ($this->checkLogin('O') == '') {
            redirect(OPERATOR_NAME);
         } else{
			$cancelled_by = $this->input->post('cancelled_by');
			$cancel_reason = $this->input->post('reason'); 
			if($cancelled_by != '' && $cancel_reason !=''){
				$ride_id = $this->input->post('ride_id');
				$checkRide = $this->app_model->get_all_details(RIDES,array('ride_id'=>$ride_id));
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
						$reasonVal = $this->app_model->get_selected_fields(CANCELLATION_REASON, array('_id' => MongoID($cancel_reason)), array('reason'));
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
								redirect(OPERATOR_NAME.'/trip/display_trips');
							}
							
							$isPrimary = 'No';
							
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
							$this->app_model->update_details(RIDES, $rideDetails, array('ride_id' => $ride_id));

							if ($checkRide->row()->driver['id'] != '') {
								
								$avail_data = array('mode' => 'Available', 'availability' => 'Yes');
								$driver_id = $checkRide->row()->driver['id'];
								$this->app_model->update_details(DRIVERS,$avail_data,array('_id'=>MongoID($driver_id)));
							}else{
								$driver_id = "";
							}
							$current_date = MongoDATE(strtotime(date("Y-m-d 00:00:00")));
							$field = array('ride_cancel.hour_' . date('H') => 1, 'ride_cancel.count' => 1);
							$this->app_model->update_stats(array('day_hour' => $current_date), $field, 1);
							if ($cancelled_by == 'user' && $isPrimary == 'Yes') {
								
								if ($checkRide->row()->coupon_used == 'Yes') {
									$usage = array("user_id" => (string) $user_id, "ride_id" => $ride_id);
									$promo_code = (string) $checkRide->row()->coupon['code'];
									$this->app_model->simple_pull(PROMOCODE, array('promo_code' => $promo_code), array('usage' => $usage));
								}
							}
							
							$this->app_model->update_user_rides_count('cancelled_rides', (string) $user_id);
							if ($driver_id != '') {
								$this->app_model->update_driver_rides_count('cancelled_rides', (string) $driver_id);
							}
							
							if($driver_id!=""){
								$driverVal = $this->app_model->get_selected_fields(DRIVERS, array('_id' => MongoID($driver_id)), array('_id', 'push_notification'));

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
								$userVal = $this->app_model->get_selected_fields(USERS, array('_id' => MongoID($user_id)), array('_id', 'push_notification_key','push_type'));
								
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
							redirect(OPERATOR_NAME.'/trip/display_trips');
						} else {
							$this->setErrorMessage('success', 'Invalid Reason','error_message_ride_invalid_reason');
							redirect(OPERATOR_NAME.'/trip/display_trips');
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
						redirect(OPERATOR_NAME.'/trip/display_trips');
					}
				}else{
				   $this->setErrorMessage('error', 'Ride Not Found','error_message_ride_not_found');
				   redirect(OPERATOR_NAME.'/trip/display_trips');
				}
			}else{
			   $this->setErrorMessage('error', 'Some Parameters are missing','error_message_ride_parameter_missing');
			   redirect(OPERATOR_NAME.'/trip/display_trips');
			}
		}
	}
	/**
    * 
    * Ride booking from operator panel
    *
    * @param string $category  car category id
    * @param string $pickup_location ride pick-up location
    * @param string $pickup_lon ride pick-up longitude
    * @param string $pickup_lat ride pick-up latitude
    * @param string $drop_location ride drop location
    * @param string $drop_lon ride drop location longitude
    * @param string $drop_lat ride drop location latitude
    * @param string $trip_type ride type now/later
    * @param string $pickup_date ride pick-up date for later rides
    * @param string $pickup_time ride pick-up time
    * @param string $dail_code user dial code
    * @param string $mobile_number user mobile number
    * @param string $user_name user name
    * @param string $user_email user email
    * @return HTTP REDIRECT,available driver list/ride list /page
    *
    **/
    public function confirm_trip() {
        if ($this->checkLogin('O') == '') {
            redirect(OPERATOR_NAME);
        } else { 
		
			$category = $this->input->post('category');
			$pickup = $this->input->post('pickup_location');
			$pickup_lon = $this->input->post('pickup_lon');
			$pickup_lat = $this->input->post('pickup_lat');
			$drop = $this->input->post('drop_location');
			$drop_lon = $this->input->post('drop_lon');
			$drop_lat = $this->input->post('drop_lat');
			$trip_type = $this->input->post('trip_type');
			$pickup_date = $this->input->post('pickup_date');
			$pickup_time = $this->input->post('pickup_time');
			$country_code = $this->input->post('dail_code');
			$phone_number = $this->input->post('mobile_number');	

			$user_name = $this->input->post('user_name');
			$user_email = $this->input->post('user_email');
			
			$pickup_date_time = $this->input->post('pickup_date_time');

			$operators_id = $this->checkLogin('O');
			$code = '';
			$try = intval(0);
			$ride_id = '';
			
			$pickup_address = str_replace(" ", "+", $pickup);
			
		
			$pickup_address_arr = file_get_contents("https://maps.google.com/maps/api/geocode/json?address=$pickup_address&sensor=false".$this->data['google_maps_api_key']);
			$pickup_address_arr = json_decode($pickup_address_arr);
			
			$drop_address = str_replace(" ", "+", $drop);
			$drop_address_arr = file_get_contents("https://maps.google.com/maps/api/geocode/json?address=$drop_address&sensor=false".$this->data['google_maps_api_key']);
			$drop_address_arr = json_decode($drop_address_arr);
		
			if($pickup_lat != '' && $pickup_lon != '' && $drop_lon != '' && $drop_lat != '' && !empty($pickup_address_arr->results) && !empty($drop_address_arr->results)){
				$riderlocArr = array('lat' => (string) $pickup_lat, 'lon' => (string) $pickup_lon);
		 
				if ($trip_type == 'on') {
						$ride_type = 'Later';
						$pickup_date = date("Y-m-d",strtotime($pickup_date));
						$pickup_time = date("h:i A",strtotime($pickup_time));
				} else {
						$ride_type = 'Now';
						$pickup_date = date("Y-m-d");
						$pickup_time = date("h:i A");
						$pickup_date_time = $pickup_date . ' ' . $pickup_time;
				}

				$pickup_datetime = $pickup_date_time;
				$pickup_timestamp = strtotime($pickup_datetime);
				$after_one_hour = strtotime('+1 hour', time());
				if($trip_type =='' || ($trip_type =='on' && ($pickup_timestamp > $after_one_hour)) ){
					$userSelectArr = array('email', 'user_name', 'country_code', 'phone_number','status');
					$userExist = FALSE;
					$checkbyEmail = $this->app_model->get_selected_fields(USERS, array('email' => $user_email), $userSelectArr);
					if ($checkbyEmail->num_rows() == 1) {
							$userExist = TRUE;
							$userDetails = $checkbyEmail->row();
					}
					$checkbyMobile = $this->app_model->get_selected_fields(USERS, array('country_code' => $country_code,'phone_number' => $phone_number), $userSelectArr);
					if ($checkbyMobile->num_rows() == 1) {
							$userExist = TRUE;
							$userDetails = $checkbyMobile->row();
					}
	
				
			$userArr= array();
			if($userExist){
	
					if($userDetails->status == 'Deleted'){
							$this->setErrorMessage('error', 'You can not book rides for deleted users.');
							redirect(OPERATOR_NAME.'/trip/init_booking_form');
					}
					
		
					$user_id = (string)$userDetails->_id;
					$user_name = $userDetails->user_name;
					$user_email = $userDetails->email;
					$country_code = $userDetails->country_code;
					$phone_number = $userDetails->phone_number;
			}else{
					
					$userArr = array("user_name"=>$user_name,"user_email"=>$user_email,"country_code"=>$country_code,"phone_number"=>$phone_number);
					$createUser = $this->register_user($userArr);
					if($createUser['status'] == '1'){
							$user_id = $createUser['response'];
					}else{
							$this->setErrorMessage('error', $createUser['response']);
							redirect(OPERATOR_NAME.'/trip/book_trip');
					}
			}
			if ($user_id !=  '') {
					$checkUser = $this->app_model->get_selected_fields(USERS, array('_id' => MongoID($user_id)), array('email', 'user_name', 'country_code', 'phone_number', 'push_type','brain_profile_id','status'));
					if ($checkUser->num_rows() == 1 && $checkUser->row()->status == 'Active') {
	
							$coordinates = array(floatval($pickup_lon), floatval($pickup_lat));
							$location = $this->app_model->find_location(floatval($pickup_lon), floatval($pickup_lat));
							if (!empty($location['result'])) {
									$condition = array('status' => 'Active');
									$categoryResult = $this->app_model->get_selected_fields(CATEGORY, array('_id' => MongoID($category)), array('name'));
									if ($categoryResult->num_rows() > 0) {
											$checkCode = $this->app_model->get_all_details(PROMOCODE, array('promo_code' => $code));
											$code_used = 'No';
											$coupon_type = '';
											$coupon_amount = '';
											if ($checkCode->num_rows() > 0) {
													$code_used = 'Yes';
													$coupon_type = $checkCode->row()->code_type;
													$coupon_amount = $checkCode->row()->promo_value;
											}
											$site_commission = 0;
											if (isset($location['result'][0]['site_commission'])) {
													if ($location['result'][0]['site_commission'] > 0) {
															$site_commission = $location['result'][0]['site_commission'];
													}
											}
					
				
											$currencyCode = $this->data['dcurrencyCode'];
											$currencySymbol = $this->data['dcurrencySymbol'];
				
											if(isset($location['result'][0]['currency'])){
													if($location['result'][0]['currency'] != ''){
															$currencyCode  = $location['result'][0]['currency'];
															$currencySymbol  = $location['result'][0]['currency_symbol'];
													}
											}
				
											$distance_unit = $this->data['d_distance_unit'];
											if(isset($location['result'][0]['distance_unit'])){
													$distance_unit = $location['result'][0]['distance_unit'];
											}
				

											$booked_ride_id= $ride_id = $this->app_model->get_ride_id();
											$timeZone = $this->data['current_app_timezone'];
											$bookingInfo = array('ride_id' => (string) $ride_id,
												'type' => $ride_type,
												'booking_source'=>'Operators',
												'booked_by'=>$operators_id,
                                                'operator_id'=>MongoID($operators_id),
												'currency' => $currencyCode,
												'currency_symbol' => $currencySymbol,
												'timezone' => $timeZone,
												'commission_percent' => $site_commission,
												'location' => array('id' => (string) $location['result'][0]['_id'],
													'name' => $location['result'][0]['city']
												),
												'user' => array('id' => (string) $checkUser->row()->_id,
													'name' => $checkUser->row()->user_name,
													'email' => $checkUser->row()->email,
													'phone' => $checkUser->row()->country_code . $checkUser->row()->phone_number
												),
												'driver' => array('id' => '',
													'name' => '',
													'email' => '',
													'phone' => ''
												),
												'total' => array('fare' => '',
													'distance' => '',
													'ride_time' => '',
													'wait_time' => ''
												),
												'fare_breakup' => array('min_km' => '',
													'min_time' => '',
													'min_fare' => '',
													'per_km' => '',
													'per_minute' => '',
													'wait_per_minute' => '',
													'peak_time_charge' => '',
													'night_charge' => '',
													'distance_unit' => (string)$distance_unit,
													'duration_unit' => 'min',
												),
												'tax_breakup' => array('service_tax' => ''),
												'booking_information' => array('service_type' => $categoryResult->row()->name,
													'service_id' => (string) $categoryResult->row()->_id,
													'booking_date' => MongoDATE(time()),
													'pickup_date' => '',
													'actual_pickup_date' => MongoDATE(strtotime($pickup_datetime)),
													'est_pickup_date' => MongoDATE(strtotime($pickup_datetime)),
													'booking_email' => $checkUser->row()->email,
													'pickup' => array('location' => $pickup,
														'latlong' => array('lon' => floatval($pickup_lon),
															'lat' => floatval($pickup_lat))
													),
													'drop' => array('location' => $drop,
														'latlong' => array('lon' => floatval($drop_lon),
															'lat' => floatval($drop_lat)
														)
													)
												),
												'ride_status' => 'Booked',
												'coupon_used' => $code_used,
												'coupon' => array('code' => $code,
													'type' => $coupon_type,
													'amount' => floatval($coupon_amount)
												)
											);
											
					
											$this->app_model->simple_insert(RIDES, $bookingInfo);
											$ride_id = $this->mongo_db->insert_id();
				
											$bookingArr = array('email' => $checkUser->row()->email,
																'ride_id' => $booked_ride_id,
																'name' => $checkUser->row()->user_name,
																'location' => $pickup,
																'phone_code' => $checkUser->row()->country_code,
																'phone_number' => $checkUser->row()->phone_number
											);
											
											
											$this->load->model('mail_model'); 
											$this->load->model('sms_model');
											$this->mail_model->send_operator_booking_confirmation_mail($bookingArr);
											$this->sms_model->send_operator_booking_confirmation_sms($bookingArr);
										
											$this->setErrorMessage('success', 'Trip has been booked','operator_trip_booked');
											if($ride_type == 'Now'){
													redirect(OPERATOR_NAME.'/trip/available_drivers_list/'.$ride_id);
											}else{
													redirect(OPERATOR_NAME.'/trip/view_trip/'.$ride_id);
											}
									}
							} else {
									$this->setErrorMessage('error', 'Some of the fields are missing','driver_some_missing');
							}
					} else {
							if($checkUser->num_rows() > 0){
									if($checkUser->row()->status == 'Inactive'){
											$this->setErrorMessage('error', "Your account has been deactivated", "your_account_inactivated");
									} else  if($checkUser->row()->status == 'Deleted'){
											$this->setErrorMessage('error', "Your account is suspended permanently", "account_suspended_permanently");
									}  else  if($checkUser->row()->status == 'Blocked'){
											$this->setErrorMessage('error', "Your account is temporarily blocked, Please contact support team for more details", "account_temporarily_blocked");
									}
									redirect(OPERATOR_NAME.'/trip/init_booking_form');
							}
					}
			}
			$this->setErrorMessage('error', 'You cannot book at this time.','operator_cannot_book_at_this_time');
			redirect(OPERATOR_NAME.'/trip/init_booking_form');
			} else {
					$this->setErrorMessage('error', 'You cannot book at this time.','operator_cannot_book_at_this_time');
					redirect(OPERATOR_NAME.'/trip/init_booking_form');
				 }
			 } else {
				$this->setErrorMessage('error', 'Your selected locations are invalid','operator_invalid_location');
				redirect(OPERATOR_NAME.'/trip/init_booking_form');
			 }
		}
	}
	/**
    * 
    * register a new user from operator panel 
    *
    * @param string $country_code user country code
    * @param string $phone_number user mobile number
    * @param string $user_name user name
    * @param string $user_email user email
    * @return ARRAY,its a call back function operator booking function
    *
    **/
	public function register_user($userDetailsArr = array()){
		$returnArr['status'] = '0';
		$returnArr['response'] = '';
		$last_insert_id = FALSE;
		if(!empty($userDetailsArr)){
			$email = strtolower($userDetailsArr['user_email']);
			$user_name = $userDetailsArr['user_name'];
			$country_code = $userDetailsArr['country_code'];
			$phone_number = $userDetailsArr['phone_number'];
			
			$password = $this->get_rand_str('6');
			$referal_code = '';
			$checkEmail = $this->app_model->get_selected_fields(USERS,array('email' => $email),array('_id'));
			if($checkEmail->num_rows() < 1 ){
				$checkPhone = $this->app_model->get_selected_fields(USERS,array('phone_number' => $phone_number,'country_code' => $country_code),array('_id'));
				if($checkPhone->num_rows() < 1 ){
					$verification_code=$this->get_rand_str('10');
					$unique_code=$this->app_model->get_unique_id($user_name);
					$user_data = array('user_name' => $user_name,
						'user_type' => 'Guest',
						'guest_id' => (string)$this->checkLogin('O'),
						'unique_code' => $unique_code,
						'email' => $email,
						'password' => md5($password),
						'image' => '',
						'status' => 'Active',
						'country_code' => $country_code,
						'phone_number' => $phone_number,
						'referral_code' => $referal_code,
						'verification_code' => array("email"=>$verification_code),
						'created' => date("Y-m-d H:i:s")
					   );
					$this->app_model->simple_insert(USERS,$user_data);
					$last_insert_id = $this->mongo_db->insert_id();
					if($last_insert_id != ''){
						$fields = array(
							'username' => (string)$last_insert_id,
							'password' => md5((string)$last_insert_id)
						);						
						$url = $this->data['soc_url'].'create-user.php';
						$this->load->library('curl');
						$output = $this->curl->simple_post($url, $fields);
						
						$this->app_model->simple_insert(REFER_HISTORY,array('user_id'=>MongoID($last_insert_id)));
						$this->app_model->simple_insert(WALLET,array('user_id'=>MongoID($last_insert_id),'total'=>floatval(0)));
						
                        if($this->config->item('welcome_amount') > 0){
                            $trans_id=time().rand(0,2578);
                            $initialAmt = array('type'=>'CREDIT',
                                'credit_type'=>'welcome',
                                'ref_id'=>'',
                                'trans_amount'=>floatval($this->config->item('welcome_amount')),
                                'avail_amount'=>floatval($this->config->item('welcome_amount')),
                                'trans_date'=>MongoDATE(time()),
                                'trans_id'=>$trans_id
                            );
                            $this->app_model->simple_push(WALLET,array('user_id'=>MongoID($last_insert_id)),array('transactions'=>$initialAmt));
                            $this->app_model->update_wallet((string)$last_insert_id,'CREDIT',floatval($this->config->item('welcome_amount')));
                        }
						
						$current_date = MongoDATE(strtotime(date("Y-m-d 00:00:00")));
						$field=array('user.hour_'.date('H')=>1,'user.count'=>1);
						$this->app_model->update_stats(array('day_hour'=>$current_date),$field,1);
						
						$this->mail_model->customer_service_rider_register_notification_mail($last_insert_id,$password);	
						$returnArr['status'] = '1';
						$returnArr['response'] = $last_insert_id;
					}
				}else{
					if($this->lang->line('operator_phone_exists') != ''){
					$operator_phone_exists = stripslashes($this->lang->line('operator_phone_exists'));
					}else  { $operator_phone_exists = 'Phone number already exists';}
					$returnArr['response'] = $operator_phone_exists;
				}
			}else{
				if($this->lang->line('operator_email_exists') != ''){
				$operator_email_exists = stripslashes($this->lang->line('operator_email_exists'));
				}else  { $operator_email_exists = 'Email already exists';}
				$returnArr['response'] = $operator_email_exists;
			}
		}
		return $returnArr;
	}
	/**
    * 
    * available driver list page
    *
    * @param string $rides_id ride id
    * @return HTML,available driver list page
    *
    **/	
	public function available_drivers_list(){
		if ($this->checkLogin('O') == ''){
				redirect(OPERATOR_NAME);
		}else {
			if ($this->lang->line('dash_ride_avaialble_drivers') != '')
					$operator_driver_avail = stripslashes($this->lang->line('dash_ride_avaialble_drivers'));
			else $operator_driver_avail = 'Available Drivers';
			$this->data['heading'] = $operator_driver_avail;
			$rides_id = $this->uri->segment(4,0);
			$condition = array('_id' => MongoID($rides_id));
			$this->data['rides_details'] = $rides_details = $this->app_model->get_all_details(RIDES,$condition);
			if ($this->data['rides_details']->num_rows() == 1){
					$pickup_lat = $this->data['rides_details']->row()->booking_information['pickup']['latlong']['lat'];
					$pickup_lon = $this->data['rides_details']->row()->booking_information['pickup']['latlong']['lon'];
					$category =  $this->data['rides_details']->row()->booking_information['service_id'];
					$coordinates=array(floatval($pickup_lon),floatval($pickup_lat));
					
					$distance_unit = $this->data['d_distance_unit'];
					if(isset($rides_details->row()->fare_breakup['distance_unit'])){
						$distance_unit = $rides_details->row()->fare_breakup['distance_unit'];
					}
		
					$this->data['distance_unit'] = $distance_unit;
					
					$location_id = $rides_details->row()->location['id'];
					
					$category_drivers = $this->app_model->get_nearest_driver($coordinates,(string)$category,10,$distance_unit,'','global',$location_id);
					
					$this->data['driversList'] = $driversList = $category_drivers['result'];
					
					$this->load->view(OPERATOR_NAME.'/trip/available_drivers',$this->data);
			}else {
					$this->setErrorMessage('error','No records found','driver_no_records_found');
					redirect(OPERATOR_NAME.'/trip/display_trips');
			}
		}
	}
	/**
    * 
    * Assign a driver to a ride
    *
    * @param string $ride_id  Ride id
    * @param string $driver_id  Driver MongoDB\BSON\ObjectId
    * @return HTTP REDIRECT ,available rides list page
    *
    **/ 
	public function assign_driver(){
		if ($this->checkLogin('O') == ''){
				redirect(OPERATOR_NAME);
		}else {
			$operator_id = $this->checkLogin('O');
			$ride_id = $this->uri->segment(4,0);			
			$driver_id = $this->uri->segment(5,0);
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
				redirect(OPERATOR_NAME.'/trip/available_drivers_list/'.$rides_details->row()->_id);
			}else {
				$this->setErrorMessage('error','No records found','driver_no_records_found');
				redirect(OPERATOR_NAME.'/dashboard');
			}
		}
	}
	/**
    * 
    * email duplication check callback function
    *
    * @param string $email  user email 
    * @return JSON ,call back to new user registration function
    *
    **/ 
	public function check_email_exists(){
		if ($this->checkLogin('O') != ''){
			$email = $this->input->post('email');
			$emailArr = $this->app_model->get_selected_fields(USERS,array('email' => $email),array('country_code','phone_number'));
			if($emailArr->num_rows() > 0 ){
					echo json_encode(array('country_code' => $emailArr->row()->country_code,'phone_number' => $emailArr->row()->phone_number));
			}
		}	
	}
    public function ride_dashboard() {
		
        if ($this->checkLogin('O') == '') {
            redirect(OPERATOR_NAME);
        } else {
            $operator_id = MongoID($this->checkLogin('O'));
            if ($this->lang->line('rides_dashboard') != '') 
		     $this->data['heading']= stripslashes($this->lang->line('rides_dashboard')); 
		     else  $this->data['heading'] = 'Rides Dashboard';
            			
            $condition = array('ride_status' => 'Completed','operator_id' => $operator_id);
            $this->data['completedRides'] = $this->rides_model->get_all_counts(RIDES, $condition);

            $condition = array('ride_status' => 'Booked','operator_id' => $operator_id);
            $this->data['upcommingRides'] = $this->rides_model->get_all_counts(RIDES, $condition);

            $this->data['onRides'] = $this->dashboard_model->get_on_rides('',$operator_id);

            $condition = array('ride_status' => 'Cancelled', 'cancelled.primary.by' => 'User','operator_id' => $operator_id);
            $this->data['riderDeniedRides'] = $this->rides_model->get_all_counts(RIDES, $condition);

            $condition = array('ride_status' => 'Cancelled', 'cancelled.primary.by' => 'Driver','operator_id' => $operator_id);
            $this->data['driverDeniedRides'] = $this->rides_model->get_all_counts(RIDES, $condition);
			
			$this->data['totalRides'] = $this->rides_model->get_all_counts(RIDES,array('operator_id' => $operator_id));
			
			$this->data['unfilledRides'] = $this->rides_model->get_all_counts(RIDE_STATISTICS,array('location_id'=>MongoID($this->session->userdata(APP_NAME.'_session_operator_location'))));
				$center=$this->config->item('latitude').','.$this->config->item('longitude');
				$coordinates=array(floatval($this->config->item('longitude')),floatval($this->config->item('latitude')));
				
				$condition=array('status'=>'Active');
				$category = $this->rides_model->get_selected_fields(CATEGORY,$condition,array('name','image','name_languages'));
				$availCategory=array();
				$langCode = $this->data['langCode'];
				if($category->num_rows()>0){
					foreach($category->result() as $cat){
					
						$category_name = $cat->name;
						if(isset($cat->name_languages[$langCode ]) && $cat->name_languages[$langCode ] != '') $category_name = $cat->name_languages[$langCode ];
					
						$availCategory[(string)$cat->_id]=$category_name;
					}
				}
				
				$address=$this->input->post('location');
				$date_from=$this->input->post('date_from');
				$date_to=$this->input->post('date_to');
				if($address!=''){
					$address = str_replace(" ", "+", $address);
					$json =file_get_contents("https://maps.google.com/maps/api/geocode/json?address=$address&sensor=false".$this->data['google_maps_api_key']);
					$jsonArr = json_decode($json);
					$lat = $jsonArr->{'results'}[0]->{'geometry'}->{'location'}->{'lat'};
					$lang = $jsonArr->{'results'}[0]->{'geometry'}->{'location'}->{'lng'};
					$location=array($lat,$lang);
					$coordinates=array_reverse($location);
					$center=@implode($location,',');
				}
				
				$matchArr = array(
					'$match' => array(
                    'location_id'=>MongoID($this->session->userdata(APP_NAME.'_session_operator_location'))
                    ));
				if(($date_from !=''&& $date_to !='')){
					$matchArr = array(
					'$match' => array('ride_time' => array('$gte' => MongoDATE(strtotime($date_from)),'$lte' => MongoDATE(strtotime($date_to.' 23:59:59'))),
                    'location_id'=>MongoID($this->session->userdata(APP_NAME.'_session_operator_location'))
                    ));
				}else if($date_from != ''){
					$matchArr = array(
					'$match' => array('ride_time' => array('$gte' => MongoDATE(strtotime($date_from.' 00:00:00'))),
                    'location_id'=>MongoID($this->session->userdata(APP_NAME.'_session_operator_location'))
                    )
                    
                    );
				}
				if(!empty($coordinates) & $coordinates[0]!='') {
				$rideDetails = $this->rides_model->get_unfilled_rides($coordinates,$matchArr); 
				}else{
				 $this->setErrorMessage('error', 'No location Found','admin_no_location');
                  redirect(OPERATOR_NAME.'/trip/ride_dashboard');
				}
				$this->load->library('googlemaps');

				$config['center'] =$center;
				$config['zoom'] = '16';
				$config['minzoom'] = '3';
				$config['maxzoom'] = '24';
				$config['places'] = TRUE;
				$config['cluster'] = FALSE;
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
						
						$unfilled++;
						
					}
				}
				
					$unfilled_rides = 0;
					if(!empty($catArr)){
						foreach($catArr as $cAR){
							if($cAR["name"]!=""){
								$unfilled_rides = $unfilled_rides + $cAR["count"];
							}
						}
					}

			$this->data['mapContent'] = $this->googlemaps->create_map();
			$this->data['unfilled_rides'] = $unfilled_rides;
			$this->data['address'] = urldecode($address);
			$this->data['date_from'] = urldecode($date_from);
			$this->data['date_to'] = urldecode($date_to);
			$this->data['categories'] = $catArr;
					
			$this->load->view(OPERATOR_NAME.'/trip/ride_dashboard', $this->data);
        }
    }
	/**
    * 
    * mobile number duplication check callback function
    *
    * @param string $mobile_number  user mobile number 
    * @param string $country_code  user country code 
    * @return JSON ,call back to new user registration function
    *
    **/ 
	public function check_phone_number_exists(){
			if ($this->checkLogin('O') != ''){
					$phone_number = $this->input->post('mobile_number');
					$country_code = $this->input->post('country_code');
					$phone_numberArr = $this->app_model->get_selected_fields(USERS,array('phone_number' => $phone_number,'country_code' => $country_code),array('email'));
					if($phone_numberArr->num_rows() > 0 ){
							echo json_encode(array('email' => $phone_numberArr->row()->email));
					}
			}				
	}
	/**
    * 
    * user auto search ajax call
    *
    * @param string $keyword  user name
    * @return HTML ,user search result html
    *
    **/ 
	public function ajax_user_autosearch(){
			$returnmsg = '';
			$keyword = $this->input->post('keyword');
			$cond = array('user_name' => array('$regex'=>$keyword, '$options'=>'i'));
			$usersList = $this->app_model->get_selected_fields(USERS,$cond,array('user_name','email','_id'));
			foreach($usersList->result() as $users){
					$returnmsg.='<li id="'.(string)$users->_id.'" onclick="select_user(\''.(string)$users->_id.'\');">'.$users->user_name.' ('.$users->email.')</li>';
			}
			echo $returnmsg; exit;
	}
	/**
    * 
    * ride estimation calculation
    *
    * @param string $category  ride car category
    * @param string $pickup_latlang  pickup location lat/lon
    * @param string $drop_latlang  drop location lat/lon
    * @return JSON ,ride estimated fare list
    *
    **/ 	
	function ajax_estimate_fare(){ 
		$returnArr['status'] = '0';
		$returnArr['response'] = '';
		$category = $this->input->post('category');
		$from = $this->input->post('pickup_latlang');
		$to = $this->input->post('drop_latlang');
		$pickupArr = @explode(',',$from);
		$pickup_lon = $pickupArr[1];
		$pickup_lat = $pickupArr[0];
        
        $dropArr = @explode(',',$to);
		$drop_lon = $dropArr[1];
		$drop_lat = $dropArr[0];
		
        
        $pickup_date_time = $this->input->post('pickup_date_time');
        
        $type = intval($this->input->post('trip_type'));
        $pickup_date = ''; $pickup_time = ''; 
        if($type == 1 && $pickup_date_time != ''){
            $pickup_date = date('Y-m-d',strtotime($pickup_date_time));
            $pickup_time = date('H:i:s',strtotime($pickup_date_time));
        }
        
        $fareReqArr = array('pickup_lon' => $pickup_lon,
                            'pickup_lat' => $pickup_lat,
                            'drop_lat' => $drop_lat,
                            'drop_lon' => $drop_lon,
                            'category' => $category,
                            'type' => $type,
                            'pickup_date' => $pickup_date,
                            'pickup_time' => $pickup_time
                            
        );
        
		$this->load->helper('pool_helper');
        get_estimate($fareReqArr,'operator');
				
	}
	/**
    * 
    * get available category for the location
    *
    * @param string $pickup_lat  pickup location lat
    * @param string $pickup_lon  pickup location lon
    * @return JSON ,available category list
    *
    **/ 
	public function get_category_from_location(){
	
		$returnArr['status']='0';
		$pickup_lat = $this->input->post('pickup_lat');
		$pickup_lon = $this->input->post('pickup_lon');
		
		if ($this->lang->line('operator_select_category') != '') $select_category =  stripslashes($this->lang->line('operator_select_category')); else $select_category = 'Select Category'; 
		
		$location = $this->app_model->find_location(floatval($pickup_lon), floatval($pickup_lat));
		$catsStr = '<option value="">'.$select_category.'</option>';
		if (!empty($location['result'])) {
			$availCats = $location['result'][0]['avail_category'];
			$catsList = array();
			foreach($availCats as $cid){
				$catsList[] = MongoID($cid);
			}
			$langCode = $this->data['langCode'];
			$getCats = $this->app_model->get_selected_fields(CATEGORY,array('_id' => array('$in' => $catsList)),array('_id','name','name_languages','icon_car_image'));
			foreach($getCats->result() as $cats){
				$category_name = $cats->name;
                $icon_car_image = ICON_MAP_CAR_IMAGE;
                if(isset($cats->icon_car_image) && $cats->icon_car_image != ''){
                    if(file_exists(ICON_IMAGE.$cats->icon_car_image)){
                        $icon_car_image = ICON_IMAGE.$cats->icon_car_image;
                    }
                } 
				if(isset($cats->name_languages[$langCode]) && $cats->name_languages[$langCode] != '') $category_name = $cats->name_languages[$langCode];
				$catsStr.='<option value="'.$cats->_id.'" data-car="'.$icon_car_image.'">'.$category_name.'</option>';
			}
			$returnArr['status']='1';
		}	
		$returnArr['response'] = $catsStr;
		$json_encode_new = json_encode($returnArr);
		echo $json_encode_new; 
	}
    
    /**
    * 
    * get available drivers for the category and location
    *
    * @param string $pickup_lat  pickup location lat
    * @param string $pickup_lon  pickup location lon
    * @param string $cat_id  category id
    * @return JSON ,available category list
    *
    **/ 
	public function get_nearest_drivers_from_ajax(){
	
		$returnArr['status']='0';
		$pickup_lat = $this->input->post('pickup_lat');
		$pickup_lon = $this->input->post('pickup_lon');
		$categoryID = $this->input->post('cat_id');
        $limit = 1000;
        $catIcons = array();
		
        $location = $this->app_model->find_location(floatval($pickup_lon), floatval($pickup_lat));
        if(isset($location['result'][0]['_id'])){
            $location_id = $location['result'][0]['_id']; 
            if($categoryID == ''){
                $categoryArr = $location['result'][0]['avail_category']; 
                $categoryID = array();
                foreach($categoryArr as $cats){
                    $categoryID[] = MongoID($cats);
                }
                $getCats = $this->app_model->get_selected_fields(CATEGORY,array('_id' => array('$in' => $categoryID)),array('icon_car_image'));
                foreach($getCats->result() as $cat_data){
                    $icon_car_image = ICON_MAP_CAR_IMAGE;
                    if(isset($cat_data->icon_car_image) && $cat_data->icon_car_image != ''){
                        if(file_exists(ICON_IMAGE.$cat_data->icon_car_image)){
                            $icon_car_image = ICON_IMAGE.$cat_data->icon_car_image;
                        }
                    }
                    $catIcons[(string)$cat_data->_id] = $icon_car_image;
                }
            }  
            $coordinates = array(floatval($pickup_lon), floatval($pickup_lat));
            $driversArr = array();
            $driverList = $this->app_model->get_nearest_driver($coordinates, $categoryID, $limit,"","","",$location_id,"all"); 
            if (!empty($driverList['result'])) {
                foreach ($driverList['result'] as $driver) {
                    $lat = $driver['loc']['lat'];
                    $lon = $driver['loc']['lon'];
                    $icon_img = '';
                    $cat_id = (string)$driver['category'];
                    if(isset($catIcons[$cat_id])){
                        $icon_img = $catIcons[$cat_id];
                    }
                    $driversArr[] = array('lat' => $lat,
                                        'lon' => $lon,
                                        'icon_img' => $icon_img
                                    );
                }
            }
            $returnArr['status']='1';
        }
        
		$returnArr['response'] = $driversArr;
		$json_encode_new = json_encode($returnArr);
		echo $json_encode_new; 
	}
    
    public function rides_grid_view() {  
        if ($this->checkLogin('O') == '') {
            redirect(OPERATOR_NAME);
        } else { 
		   $startDate = strtotime(date('Y-m-d H:i:s',strtotime('-24 hours'))); 
		   $cond = array('booking_information.booking_date' => array('$gte' =>  MongoDATE($startDate),'$lte'                => MongoDATE(time())),
                    'operator_id' =>MongoID($this->checkLogin('O'))
           ); 
			$this->data['ridesList'] = $ridesList = $this->rides_model->get_selected_fields(RIDES,$cond,array('user','driver','booking_information','ride_status','ride_id','type'),array('booking_information.booking_date'=>"DESC"));
			if ($this->lang->line('dash_view_rides') != '') 
		    $this->data['heading']= stripslashes($this->lang->line('dash_view_rides')); 
		    else  $this->data['heading'] = 'View Rides';
            $this->load->view(OPERATOR_NAME.'/trip/rides_grid_view', $this->data);
        }
    }
	
	function get_rides_ajax(){
		$returnArr['status'] = '0';
		$returnArr['response'] = '';
		$authkey = $this->input->post('authkey');
		if($authkey == APP_NAME){
			$startDate = strtotime(date('Y-m-d H:i:s',strtotime('-24 hours'))); 
	      $cond = array('booking_information.booking_date' => array('$gte' =>  MongoDATE($startDate),'$lte'                => MongoDATE(time())),
                    'operator_id' =>MongoID($this->checkLogin('O'))
           ); 
			$ridesList = $this->rides_model->get_selected_fields(RIDES,$cond,array('user','driver','booking_information','ride_status','ride_id','type'),array('booking_information.booking_date'=>"DESC"));
			$booked_rides = '';  $confimed_rides = '';  $on_rides = '';  $completed_rides = '';
			
			foreach($ridesList->result() as $rides){
				$driver_name = '--';
				if(isset($rides->driver['name'])){
					$driver_name = $rides->driver['name'];
				}
				
				$booked_date = '--';
				if(isset($rides->booking_information['booking_date'])){
					$booked_date = date('h:i A',MongoEPOCH($rides->booking_information['booking_date']));
				}
                $actual_pickup_date = '--';
				if(isset($rides->booking_information['actual_pickup_date'])){
					$actual_pickup_date = date('h:i A',MongoEPOCH($rides->booking_information['actual_pickup_date']));
				}
				if(isset($rides->type) && $rides->type =='Now'){
                $ridetype = 'Ride Now';
                
                }else{
                $ridetype = 'Ride Later';
                }
				if($rides->ride_status == 'Booked'){
					$booked_rides.= '<tr>
					<td>'.$rides->user['name'].'</td>
					<td>'.$rides->ride_id.'<span class="service_type">'.$rides->booking_information['service_type'].'</span><span class="rides_type">'.$ridetype.'</span></td>';
                    if($rides->type!='Now') {
                        $booked_rides.='<td><span class="rides_type">Booking Time-'.$booked_date.'</span><span class="rides_type">Pick-up Time-'.$actual_pickup_date.'</span></td>
                        </tr>';
                    } else {
                        $booked_rides.='<td><span class="rides_type">Pick-up Time-'.$booked_date.'</span></td>
                        </tr>';
                    }
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
/* End of file trip.php */
/* Location: ./application/controllers/operator/trip.php */