<?php

if (!defined('BASEPATH'))
    exit('No direct script access allowed');

/**
 * 
 * This controller contains the functions related to rides management 
 * @author Casperon
 *
 * */
class Rides extends MY_Controller {

    function __construct() {
        parent::__construct();
        $this->load->helper(array('cookie', 'date', 'form'));
        $this->load->library(array('encrypt', 'form_validation'));
        $this->load->model('rides_model');
    }

    /**
     * 
     * This function loads the rides list page
     *
     * */
    public function index() {
        if ($this->checkLogin('D') == '') {
            redirect('driver');
        } else {
            redirect('driver/rides/display_rides_list');
        }
    }

    /**
     * 
     * This function loads the rides list page
     *
     * */
    public function display_rides() {
        if ($this->checkLogin('D') == '') {
            redirect('driver');
        } else {
            $driver_id = $this->checkLogin('D');
			$condition = array('driver.id' => $this->checkLogin('D'),'$or' => array(
                    array("ride_status" => 'Completed'),
                    array("ride_status" => 'Cancelled')
                 
                ));
            $this->data['ridesList'] = $this->rides_model->get_all_details(RIDES, $condition); 
			if ($this->lang->line('rider_profile_my_rides') != '')
                $dash_my_rides = stripslashes($this->lang->line('rider_profile_my_rides'));
            else
            $dash_my_rides = 'My Rides';			
			$this->data['heading'] = $dash_my_rides;
			$this->data['sideMenu'] = 'rides';
            $this->load->view('driver/rides/display_rides', $this->data);
		
        }
    }

    /**
     * 
     * This function loads the rides view page
     *
     * */
    public function view_ride_details() {
        if ($this->checkLogin('D') == '') {
            redirect('driver');
        } else {

            if ($this->lang->line('dash_view_rides') != '')
                $dash_view_rides = stripslashes($this->lang->line('dash_view_rides'));
            else
                $dash_view_rides = 'View Rides';

            $this->data['heading'] = $dash_view_rides;
            $ride_id = $this->uri->segment(4, 0);
			$condition = array('driver.id' => $this->checkLogin('D'), 'ride_id' => $ride_id);
            $this->data['rides_details'] = $rides_details = $this->rides_model->get_all_details(RIDES, $condition);
			$this->data['sideMenu'] = 'rides';
            if ($this->data['rides_details']->num_rows() == 1) {
			
				/*----------------- ratings-----------------------------------*/
				$this->data['get_ratings'] = 'No'; 
				if(($rides_details->row()->ride_status == 'Finished' || $rides_details->row()->ride_status == 'Completed') && !isset($rides_details->row()->rider_review_status)){
					$condition = array('option_holder' => 'rider', 'status' => 'Active');
					$this->data['rating_options'] = $rating_options = $this->user_model->get_all_details(REVIEW_OPTIONS, $condition);
					$this->data['get_ratings'] = 'Yes';  
				}
				
				/*----------------- user details-----------------------------------*/
				$user_id = $rides_details->row()->user['id'];
				$condition = array('_id' => MongoID($user_id));
				 $this->data['user_details'] = $user_details = $this->rides_model->get_all_details(USERS, $condition);
				 
				/*----------------------map details--------------------------*/
				if (isset($rides_details->row()->booking_information['pickup']['latlong'])) {
					$latlong = @implode(array_reverse($rides_details->row()->booking_information['pickup']['latlong']), ',');
				} else {
					$latlong = '';
				}
				$config['center'] = $latlong;
				$config['zoom'] = '18';
                $config['draggable'] = false;
                $config['disableMapTypeControl'] = true;
                $config['fullscreenControl'] = false;
				$config['language'] = $this->data['langCode'];
				$this->googlemaps->initialize($config);
				$marker = array();
				$marker['position'] = $latlong;
				$this->googlemaps->add_marker($marker);
				$this->data['map'] = $this->googlemaps->create_map();				
				 
                $this->load->view('driver/rides/view_rides', $this->data);
            } else {
                $this->setErrorMessage('error', 'No records found', 'driver_no_records_found');
                redirect('driver/rides/display_rides');
            }
        }
    }

   
	/**
     * 
     * This function loads the rides view page
     *
     * */
    public function on_ride_details() {
        if ($this->checkLogin('D') == '') {
            redirect('driver');
        } else {
			$driver_id = $this->checkLogin('D');
			$action = $this->input->get('act');
            $condition = array('driver.id' => $driver_id,'ride_status' => array('$in' => array('Confirmed','Arrived','Onride','Finished')));
            $this->data['rides_details'] = $rides_details = $this->rides_model->get_selected_fields(RIDES, $condition,array('ride_id'));
	
            if ($this->data['rides_details']->num_rows() > 0) {
                $ride_id = $rides_details->row()->ride_id;
                
                
                if ($this->lang->line('dash_view_rides') != '')
                    $dash_view_rides = stripslashes($this->lang->line('dash_view_rides'));
                else
                    $dash_view_rides = 'View Rides';

                $this->data['heading'] = $dash_view_rides;
                $condition = array('driver.id' => $this->checkLogin('D'), 'ride_id' => $ride_id);
                $this->data['rides_details'] = $rides_details = $this->rides_model->get_all_details(RIDES, $condition);
                $this->data['sideMenu'] = 'rides';
                
                if ($this->data['rides_details']->num_rows() == 0) {
                    $this->setErrorMessage('error', 'No records found', 'driver_no_records_found');
                     redirect('driver/dashboard/driver_dashboard');
                }
                
                /*----------------- ratings-----------------------------------*/
                $this->data['get_ratings'] = 'No'; 
                if(($rides_details->row()->ride_status == 'Finished' || $rides_details->row()->ride_status == 'Completed') && !isset($rides_details->row()->driver_review_status)){
                    $condition = array('option_holder' => 'rider', 'status' => 'Active');
                    $this->data['rating_options'] = $rating_options = $this->user_model->get_all_details(REVIEW_OPTIONS, $condition);
                    $this->data['get_ratings'] = 'Yes';  
                }
                
                /*----------------- user details-----------------------------------*/
                $user_id = $rides_details->row()->user['id'];
                $condition = array('_id' => MongoID($user_id));
                 $this->data['user_details'] = $user_details = $this->rides_model->get_all_details(USERS, $condition);
                 
                /*----------------------map details--------------------------*/
                if (isset($rides_details->row()->booking_information['pickup']['latlong'])) {
                    $latlong = @implode(array_reverse($rides_details->row()->booking_information['pickup']['latlong']), ',');
                } else {
                    $latlong = '';
                }
                $config['center'] = $latlong;
                $config['zoom'] = '18';
                $config['draggable'] = false;
                $config['disableMapTypeControl'] = true;
                $config['fullscreenControl'] = false;
                $config['language'] = $this->data['langCode'];
                $this->googlemaps->initialize($config);
                $marker = array();
                $marker['position'] = $latlong;
                $this->googlemaps->add_marker($marker);
                $this->data['map'] = $this->googlemaps->create_map();
                
				if($action == 'details'){
					$this->load->view('driver/rides/view_rides', $this->data);
				} else {
					redirect('track?rideId='.$ride_id);
				}
            } else {
                $this->setErrorMessage('error', 'No records found', 'driver_no_records_found');
                redirect('driver/rides/display_rides');
            }
        }
    }
	
	/**
	* 
	* This function will save the users ratings in db
	* 
	**/
   public function submit_reviews() {
		#echo '<pre>';  print_r($_POST); die;
        $responseArr['status'] = '0';
        try {
            $ratingsFor = 'rider';
            $ride_id = $this->input->post('ride_id');
            $ratingsArr = $this->input->post('reviews');
            $comments = (string) $this->input->post('comments');

            if ($ride_id != '' && is_array($ratingsArr)) {
                if (count($ratingsArr) > 0) {
                    $rideCond = array('ride_id' => $ride_id);
                    $get_ride_info = $this->user_model->get_selected_fields(RIDES, $rideCond, array('user.id', 'driver.id', 'rider_review_status', 'driver_review_status'));

                    $driversRating = 0;
                    $ridersRating = 0;
                    if (isset($get_ride_info->row()->driver_review_status)) {
                        if ($ratingsFor == 'driver' && ($get_ride_info->row()->driver_review_status == 'Yes')) {
                            $driversRating = 1;
                        }
                    }
                    if (isset($get_ride_info->row()->rider_review_status)) {
                        if ($ratingsFor == 'rider' && ($get_ride_info->row()->rider_review_status == 'Yes')) {
                            $ridersRating = 1;
                        }
                    }

                    if (($ratingsFor == 'driver' && $driversRating == 0) || ($ratingsFor == 'rider' && $ridersRating == 0)) {

                        $user_id = $get_ride_info->row()->user['id'];
                        $driver_id = $get_ride_info->row()->driver['id'];

                        $ratingsArr = array_filter($ratingsArr);
                        $num_of_ratings = 0;
                        $totalRatings = 0;
                        $avg_rating = 0;
                        for ($i = 0; $i < count($ratingsArr); $i++) {
                            $totalRatings = $totalRatings + $ratingsArr[$i]['rating'];
                            $num_of_ratings++;
                        }
                        $avg_rating = number_format(($totalRatings / $num_of_ratings), 2);

                        $ride_dataArr = array('total_options' => $num_of_ratings,
                            'total_ratings' => $totalRatings,
                            'avg_rating' => number_format($avg_rating, 2),
                            'ratings' => $ratingsArr,
                            'comments' => $comments
                        );

						 $this->user_model->set_to_field(RIDES, $rideCond, array('ratings.' . $ratingsFor => $ride_dataArr, 'rider_review_status' => 'Yes'));



                       if ($ratingsFor == 'rider') {
                            $userCond = array('_id' => MongoID($user_id));
                            $get_user_ratings = $this->user_model->get_selected_fields(USERS, $userCond, array('avg_review', 'total_review'));
                            $userRateDivider = 1;
                            if (isset($get_user_ratings->row()->avg_review)) {
                                $existUserAvgRat = $get_user_ratings->row()->avg_review;
                                $userRateDivider++;
                            } else {
                                $existUserAvgRat = 0;
                            }
                            if (isset($get_user_ratings->row()->total_review)) {
                                $existTotReview = $get_user_ratings->row()->total_review;
                            } else {
                                $existTotReview = 0;
                            }
                            $userAvgRatings = ($existUserAvgRat + $avg_rating) / $userRateDivider;
                            $userTotalReviews = $existTotReview + 1;
                            $this->user_model->update_details(USERS, array('avg_review' => number_format($userAvgRatings, 2), 'total_review' => $userTotalReviews), $userCond);
                        }


                        $responseArr['status'] = '1';
                        $responseArr['response'] = $this->format_string('Your ratings submitted successfully', 'your_ratings_submitted');
                    } else {
                        $responseArr['response'] = $this->format_string('Already you have submitted your ratings for this ride.', 'already_you_submitted_ratings_for_this_ride');  # as a '.$ratingsFor;
                    }
                } else {
                    $responseArr['response'] = $this->format_string('Submitted ratings fields are not valid', 'submitted_ratings_field_invalid');
                }
            } else {
                $responseArr['response'] = $this->format_string("Some Parameters are missing", "some_parameters_missing");
            }
        } catch (MongoException $ex) {
            $returnArr['response'] = $this->format_string("Error in connection", "error_in_connection");
        }
		if( $responseArr['status'] == '1'){
			$this->setErrorMessage('success', 'Thanks, Your ratings are submitted successfully');
		} else {
			$this->setErrorMessage('error',$returnArr['response']);
		}
		redirect('driver/rides/view_ride_details/'.$ride_id);
    }
	
	
}

/* End of file rides.php */
/* Location: ./application/controllers/driver/rides.php */