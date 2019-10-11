<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');
/**
*
*	Reviews options Management for admin 
* 
*	@package	CI
*	@subpackage	Controller
*	@author	Casperon
*
**/
class Reviews extends MY_Controller {

	function __construct(){
        parent::__construct();
		$this->load->helper(array('cookie','date','form'));
		$this->load->library(array('encrypt','form_validation'));		
		$this->load->model('review_model');
		if ($this->checkPrivileges('reviews',$this->privStatus) == FALSE){
			redirect(ADMIN_ENC_URL);
		}
		$c_fun = $this->uri->segment(3);
        $restricted_function = array('delete_reviews', 'change_reviews_status_global');
        if (in_array($c_fun, $restricted_function) && $this->data['isDemo'] === TRUE) {
            $this->setErrorMessage('error', 'You are in demo version. you can\'t do this action.','admin_template_demo_version');
            redirect($_SERVER['HTTP_REFERER']);
            die;
        }
		
    }

	
	/**
	* 
	* Displays the review options list
	*
	* @return HTML, review options list page
	*
	**/
	public function display_reviews_options_list(){
		if ($this->checkLogin('A') == ''){
			redirect(ADMIN_ENC_URL);
		}else {
		    if ($this->lang->line('admin_review_display_reviews_options') != '') 
		    $this->data['heading']= stripslashes($this->lang->line('admin_review_display_reviews_options')); 
		    else  $this->data['heading'] = 'Display Reviews Options';
			$condition = array();
			$this->data['reviewsList'] = $this->review_model->get_all_details(REVIEW_OPTIONS,$condition);
			$this->load->view(ADMIN_ENC_URL.'/reviews/display_review_options',$this->data);
		}
	}
	
    /**
	* 
	* Displays the review options dashboard
	*
	* @return HTML, review options dashboard page
	*
	**/
	public function review_dashboard(){
		if ($this->checkLogin('A') == ''){
			redirect(ADMIN_ENC_URL);
		}else {
				$condition = array();
			    $this->data['reviewsList'] = $this->review_model->get_all_details(REVIEW_OPTIONS,$condition);
				if ($this->lang->line('admin_review_dashboard') != '') 
				$this->data['heading']= stripslashes($this->lang->line('admin_review_dashboard')); 
				else  $this->data['heading'] = 'Review dashboard';
				$user_option=array();
				$driver_option=array();
				foreach($this->data['reviewsList']->result_array() as $review) {
					$option_name = $review['option_name'];
				    if($review['option_holder']=='rider') {
						$user_option[]= $option_name;
					}
					if($review['option_holder']=='driver') {
						$driver_option[]=$option_name;
					}
				}
				$one_star_count=0;
				$two_star_count=0;
				$three_star_count=0;
				$four_star_count=0;
				$five_star_count=0;
			    $user_one=$this->review_model->get_avg_review_count(0,1);
				$user_two=$this->review_model->get_avg_review_count(1,2);
				$user_three=$this->review_model->get_avg_review_count(2,3);
				$user_four=$this->review_model->get_avg_review_count(3,4);
				$user_five=$this->review_model->get_avg_review_count(4,5);
				
				if(!empty($user_one['result'])) {
				    $one_star_count=count($user_one['result']);
				
				}
				if(!empty($user_two['result'])) {
				   $two_star_count=count($user_two['result']);
				}
				if(!empty($user_three['result'])) {
				   $three_star_count=count($user_three['result']);
				}
				if(!empty($user_four['result'])) {
				   $four_star_count=count($user_four['result']);
				}
				if(!empty($user_five['result'])) {
				   $five_star_count=count($user_five['result']);
				}
				$top_user=$this->review_model->top_review_user();
				$user_avg_review=array();
				$user_reason=$this->review_model->get_avg_reasonwise_review_user();
				if(!empty($user_reason['result'])) {
					
					foreach($user_reason['result'] as $data) {
						if(isset($data['ratings']['rider']['ratings']) && !empty($data['ratings']['rider']['ratings'])) {
							foreach($data['ratings']['rider']['ratings'] as $values) {																
								$user_avg_review[$values['option_id']]['rating'][]=$values['rating'];
								$user_avg_review[$values['option_id']]['title'][]=$values['option_title'];
							}
						}
					}
				}
				$final_user_review=array();
				$reviewdOptionsRr = array();
			    if(!empty($user_avg_review)) {
				   foreach($user_avg_review as $key=>$ratng) {
						$ratting_title=$ratng['title'][0];
						$reviewdOptionsRr[] = $ratting_title;
						$avg_ratting=array_sum($ratng['rating'])/count($ratng['rating']);
						$final_user_review[]=array('title'=>$ratting_title,'avg_ratting'=>$avg_ratting,'total_count'=>count($ratng['rating']));
						
				   }
				}
				
				foreach($user_option as $title){
					if(!in_array($title,$reviewdOptionsRr)){
						$final_user_review[] = array('title'=>$title,'avg_ratting'=>0,'total_count'=>0);
					}
				}
				
				$this->data['top_user']=$top_user;
				$this->data['user_review']=$final_user_review;
				$this->data['user_one_star']=$one_star_count;
				$this->data['user_two_star']=$two_star_count;
				$this->data['user_three_star']=$three_star_count;
				$this->data['user_four_star']=$four_star_count;
				$this->data['user_five_star']=$five_star_count;
				
				
				$one_star_count_driver=0;
				$two_star_count_driver=0;
				$three_star_count_driver=0;
				$four_star_count_driver=0;
				$five_star_count_driver=0;
			    $driver_one=$this->review_model->get_avg_review_count_driver(0,1);
				$driver_two=$this->review_model->get_avg_review_count_driver(1,2);
				$driver_three=$this->review_model->get_avg_review_count_driver(2,3);
				$driver_four=$this->review_model->get_avg_review_count_driver(3,4);
				$driver_five=$this->review_model->get_avg_review_count_driver(4,5);
				
				if(!empty($driver_one['result'])) {
				   $one_star_count_driver=count($driver_one['result']);
				}
				if(!empty($driver_two['result'])) {
				   $two_star_count_driver=count($driver_two['result']);
				}
				if(!empty($driver_three['result'])) {
				   $three_star_count_driver=count($driver_three['result']);
				}
				if(!empty($driver_four['result'])) {
				   $four_star_count_driver=count($driver_four['result']);
				}
				if(!empty($driver_five['result'])) {
				   $five_star_count_driver=count($driver_five['result']);
				}
				$top_driver=$this->review_model->top_review_driver();
				$driver_avg_review=array();
				$driver_reason=$this->review_model->get_avg_reasonwise_review_driver();
				if(!empty($driver_reason['result'])) {
					
					foreach($driver_reason['result'] as $data) {
						if(isset($data['ratings']['driver']['ratings']) && !empty($data['ratings']['driver']['ratings'])) {
							foreach($data['ratings']['driver']['ratings'] as $values) {								
								$driver_avg_review[$values['option_id']]['rating'][]=$values['rating'];
								$driver_avg_review[$values['option_id']]['title'][]=$values['option_title'];
							}
						}
					}
				}
				
				$final_driver_review=array();
				$reviewdOptionsDr = array();
			    if(!empty($driver_avg_review)) {
				   foreach($driver_avg_review as $key=>$ratng) {
						$avg_ratting=array_sum($ratng['rating'])/count($ratng['rating']);
					    $ratting_title=$ratng['title'][0];
						$reviewdOptionsDr[] = $ratting_title;
						$final_driver_review[]=array('title'=>$ratting_title,'avg_ratting'=>$avg_ratting,'total_count'=>count($ratng['rating']));
				   }
				}
				
				
				
				foreach($driver_option as $title){
					if(!in_array($title,$reviewdOptionsDr)){
						$final_driver_review[] = array('title'=>$title,'avg_ratting'=>0,'total_count'=>0);
					}
				}
			
				$this->data['top_driver']=$top_driver;
				$this->data['driver_review']=$final_driver_review;
				$this->data['driver_one_star']=$one_star_count_driver;
				$this->data['driver_two_star']=$two_star_count_driver;
				$this->data['driver_three_star']=$three_star_count_driver;
				$this->data['driver_four_star']=$four_star_count_driver;
				$this->data['driver_five_star']=$five_star_count_driver;
				
			    $this->load->view(ADMIN_ENC_URL.'/reviews/review_dashboard',$this->data);
		}
	}
		
	
	/**
	* 
	* Its displays add review options page
	*
	* @return HTML, review options page
	*
	**/
	public function add_review_option_form(){
		if ($this->checkLogin('A') == ''){
			redirect(ADMIN_ENC_URL);
		}else {
		    if ($this->lang->line('admin_review_display_reviews_options') != '') 
		    $this->data['heading']= stripslashes($this->lang->line('admin_review_display_reviews_options')); 
		    else  $this->data['heading'] = 'Add New Reviews Option';
			$condition = array();
			$this->load->view(ADMIN_ENC_URL.'/reviews/add_review_options',$this->data);
		}
	}
	
	
	
	
	/**
	* 
	* Edit review option
	*
    * @param string $reviews_id  review option MongoDB\BSON\ObjectId
	* @return HTML, review option edit page
	*
	**/
	public function edit_review_option_form(){
		if ($this->checkLogin('A') == ''){
			redirect(ADMIN_ENC_URL);
		}else {
			$reviews_id = $this->uri->segment(4);
			if($reviews_id!=''){
				$condition = array('_id' => MongoID($reviews_id));
				$this->data['reviewsdetails'] = $this->review_model->get_all_details(REVIEW_OPTIONS,$condition);
				if ($this->data['reviewsdetails']->num_rows() != 1){
					redirect(ADMIN_ENC_URL.'/reviews/display_reviews_options_list');
				}
				$heading='Edit Review Option';
			} else {
				redirect(ADMIN_ENC_URL);
			}
			$this->data['heading'] = $heading;
			$this->load->view(ADMIN_ENC_URL.'/reviews/edit_review_options',$this->data);
		}
	}
	
	/**
	* 
	* Insert/update review option informations
	*
    * @param string $option_id  review option MongoDB\BSON\ObjectId
    * @param string $option_name  review option name
    * @param string $option_holder  review option holder driver/rider
    * @param string $isdefault  review option isdefault status on/off
    * @param string $status  review option status
	* @return HTTP REDIRECT, review option list page
	*
	**/
	public function insertEditReviews_options(){
		if ($this->checkLogin('A') == ''){
			redirect(ADMIN_ENC_URL);
		}else {
			$option_id = $this->input->post('option_id');
			$option_name = $this->input->post('option_name');
			$option_holder =trim($this->input->post('option_holder'));
			
			$option_number = array(); 
			if($option_id == ''){
				
				$getMaxCount = $this->review_model->get_selected_fields(REVIEW_OPTIONS,array(),array('option_id'),array('option_id'=>'DESC'));
                
				$option_id_temp = $getMaxCount->row()->option_id+1;
				$option_number = array('option_id'=>floatval($option_id_temp));
			}
			$chkcondition=array('option_name'=>$option_name,'option_holder'=> $option_holder);
			$reviewOptionCheck = $this->review_model->get_all_details(REVIEW_OPTIONS,$chkcondition);
		
			if(($reviewOptionCheck->num_rows() > 0 && $option_id =='') || ($option_id != '' && $reviewOptionCheck->num_rows() > 1)) {
				$this->setErrorMessage('error','Reviews option is already exist, Please try with another title','admin_review_option_exist');
				redirect($_SERVER['HTTP_REFERER']);
			}
			
			$excludeArr = array("option_id","status");
			
			if ($this->input->post('status') == 'on'){
				$reviews_status = 'Active';
			}else{
				$reviews_status = 'Inactive';
			}
			$reviews_dataArr = array('status' => $reviews_status);
			$reviews_data = array_merge($reviews_dataArr,$option_number);
			$condition = array();
			if ($option_id == ''){
				$this->review_model->commonInsertUpdate(REVIEW_OPTIONS,'insert',$excludeArr,$reviews_data,$condition);
				$this->setErrorMessage('success','Reviews added successfully');
			}else {
				$condition = array('_id' => MongoID($option_id));
				$this->review_model->commonInsertUpdate(REVIEW_OPTIONS,'update',$excludeArr,$reviews_data,$condition);
				$this->setErrorMessage('success','Reviews updated successfully');
			}
			redirect(ADMIN_ENC_URL.'/reviews/display_reviews_options_list');
		}
	}
	
	
	/**
	* 
	* Change review option status
	*
    * @param string $mode  review option status mode 0/1
    * @param string $reviews_id  review option MongoDB\BSON\ObjectId
	* @return HTTP REDIRECT, review option list page
	*
	**/
	public function change_reviews_status(){
		if ($this->checkLogin('A') == ''){
			redirect(ADMIN_ENC_URL);
		}else {
			$mode = $this->uri->segment(4,0);
			$reviews_id = $this->uri->segment(5,0);
			$status = ($mode == '0')?'Inactive':'Active';
			$newdata = array('status' => $status);
			$condition = array('_id' => MongoID($reviews_id));
			$this->review_model->update_details(REVIEW_OPTIONS,$newdata,$condition);
			$this->setErrorMessage('success','Reviews Option Status Changed Successfully','admin_review_option_status_change');
			redirect(ADMIN_ENC_URL.'/reviews/display_reviews_options_list');
		}
	}
	
	/**
	* 
	* Delete review option
	*
    * @param string $reviews_id  review option MongoDB\BSON\ObjectId
	* @return HTTP REDIRECT, review option list page
	*
	**/
	public function delete_reviews(){
		if ($this->checkLogin('A') == ''){
			redirect(ADMIN_ENC_URL);
		}else {	
			$reviews_id = $this->uri->segment(4,0);
			$condition = array('_id' => MongoID($reviews_id));
			$this->review_model->commonDelete(REVIEW_OPTIONS,$condition);
			$this->setErrorMessage('success','Reviews deleted successfully','admin_review_delete_success');
			redirect(ADMIN_ENC_URL.'/reviews/display_reviews_options_list'); 
		}
	}
	
		
	/**
	* 
	* Change multiple review option status
	*
    * @param string $statusMode  active/inactive/delete will denotes the review option state activity
    * @param string $checkbox_id  review option id's
	* @return HTTP REDIRECT, review option list page
	*
	**/
	public function change_reviews_status_global(){
		if(count($_POST['checkbox_id']) > 0 &&  $_POST['statusMode'] != ''){
			$this->user_model->activeInactiveCommon(REVIEW_OPTIONS,'_id');
			if (strtolower($_POST['statusMode']) == 'delete'){
				$this->setErrorMessage('success','Reviews records deleted successfully','admin_review_records_delete');
			}else {
				$this->setErrorMessage('success','Reviews records status changed successfully','admin_review_records_status_change');
			}
			redirect(ADMIN_ENC_URL.'/reviews/display_reviews_options_list');
		}
	}
	
	
	/**
	* 
	* Displays the reviews list
	*
    * @param string $reviewType  review option type from query string user/driver
	* @return HTTP REDIRECT, review option list page
	*
	**/
	public function display_reviews_list(){
		if ($this->checkLogin('A') == ''){
			redirect(ADMIN_ENC_URL);
		}else {	
			$reviewType = $this->input->get('q');
			if($reviewType == 'driver' || $reviewType == 'rider'){
				$this->setErrorMessage('success','Reviews deleted successfully','admin_review_delete_successfully');
				redirect(ADMIN_ENC_URL.'/reviews/display_reviews_options_list'); 
			} else {
				$this->setErrorMessage('error','Invalid link','admin_review_invalid_link');
				redirect($_SERVER['HTTP_REFERER']);
			}
		}
	}

	/**
	* 
	* Displays the user reviews list
	*
    * @param string $user_id  review option type from query string user
	* @return HTTP REDIRECT, user list page
	*
	**/
	public function view_user_reviews(){
		if ($this->checkLogin('A') == ''){
			redirect(ADMIN_ENC_URL);
		}else {	
			$user_id = $this->uri->segment(4);
			if($user_id != ''){
				$get_review_options = $this->review_model->get_all_details(REVIEW_OPTIONS,array('option_holder' => 'rider')); 
				$reviewsList = array();
				$getCond = array('user.id' => $user_id,'rider_review_status' => 'Yes');
				$get_ratings = $this->review_model->get_selected_fields(RIDES,$getCond,array('ratings.rider','rider_review_status'));
				if($get_ratings->num_rows() > 0){
					$usersTotalRates = 0; $commonNumTotal = 0; 
					foreach($get_review_options->result() as $options){
						$tot_no_of_Rates  = 0; $totalRates = 0; $reviewStatus = 'No'; 
						foreach($get_ratings->result() as $ratings){ 
							if(isset($ratings->rider_review_status)){
								if($ratings->rider_review_status == 'Yes'){
									$reviewStatus = $ratings->rider_review_status;
									foreach($ratings->ratings['rider']['ratings'] as $rateOptions){  
										if($options->option_id == $rateOptions['option_id']){ 
											$commonNumTotal++; $tot_no_of_Rates++;
											$totalRates = $totalRates + $rateOptions['rating'];
											$usersTotalRates = $usersTotalRates + $rateOptions['rating'];
										}
									}
								}
							}
						}
                        if($totalRates > 0) {
                            $avgRates = ($totalRates/$tot_no_of_Rates);
                        } else {
                           $avgRates=0.00;
                        }
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
					
					$commonAvgRates = $usersTotalRates/$commonNumTotal;
					$summaryRateArr = array('totalRates' => $usersTotalRates,'commonNumTotal' => $get_ratings->num_rows(),'commonAvgRates' => $commonAvgRates);
					$this->data['reviewsSummary'] = $summaryRateArr; 
					$this->data['reviewsList'] = $reviewsList;
					if ($this->lang->line('admin_user_rating_summary') != ''){
						$heading = stripslashes($this->lang->line('admin_user_rating_summary')); 
					}else{
						$heading = 'Users Ratings Summary';
					}
					$this->data['heading'] = $heading;
					$this->load->view(ADMIN_ENC_URL.'/reviews/view_review_summary',$this->data);
				} else {
					$this->setErrorMessage('error','No ratings found this user','admin_review_no_rating_found_user');
					redirect($_SERVER['HTTP_REFERER']);
				}
				
			} else {
				$this->setErrorMessage('error','Invalid link','admin_review_invalid_link');
				redirect($_SERVER['HTTP_REFERER']);
			}
		}
	}
	
	/**
	* 
	* Displays the driver reviews list
	*
    * @param string $user_id  review option type from query string driver
	* @return HTTP REDIRECT, driver list page
	*
	**/
	public function view_driver_reviews(){
		if ($this->checkLogin('A') == ''){
			redirect(ADMIN_ENC_URL);
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
					
					$commonAvgRates = $usersTotalRates/$commonNumTotal;
					$summaryRateArr = array('totalRates' => $usersTotalRates,'commonNumTotal' => $get_ratings->num_rows(),'commonAvgRates' => $commonAvgRates);
					$this->data['reviewsSummary'] = $summaryRateArr; 
					$this->data['reviewsList'] = $reviewsList;
					if ($this->lang->line('admin_driver_rating_summary') != ''){
						$heading = stripslashes($this->lang->line('admin_driver_rating_summary')); 
					}else{
						$heading = 'Driver Ratings Summary';
					}
					$this->data['heading'] = $heading;
					$this->load->view(ADMIN_ENC_URL.'/reviews/view_review_summary',$this->data);
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
    
    /**
	* 
	* Edit review language
	*
    * @param string $review_id  review MongoDB\BSON\ObjectId
	* @return HTML, edit review language page
	*
	**/
    public function edit_language_review(){
		if ($this->checkLogin('A') == '') {
            redirect(ADMIN_ENC_URL);
        } else {
            $review_id = $this->uri->segment(4, 0);
            if ($review_id != '') {
                $condition = array('_id' => MongoID($review_id));
                $this->data['reviewdetails'] = $reviewdetails = $this->review_model->get_all_details(REVIEW_OPTIONS, $condition);
                $this->data['languagesList'] = $this->review_model->get_all_details(LANGUAGES, array('status' => 'Active'));
                if ($this->data['reviewdetails']->num_rows() != 1) {
                    redirect(ADMIN_ENC_URL.'/reviews/display_reviews_options_list');
                }
            }
			
			if ($this->lang->line('edit_review_lanaguage') != '') 
			$heading = stripslashes($this->lang->line('edit_review_lanaguage')); 
			else  $heading = 'Edit Review Setting language';
			 
			 $this->data['heading'] = $heading;
            $this->load->view(ADMIN_ENC_URL.'/reviews/edit_category_language_form', $this->data);
        }
	}
    
    /**
	* 
	* Update review language
	*
    * @param string $review_id  review MongoDB\BSON\ObjectId
    * @param array $option_name_languages  review option name languages
	* @return HTTP REDIRECT, review option list page
	*
	**/
    public function update_language_content(){
		if ($this->checkLogin('A') == '') {
            redirect(ADMIN_ENC_URL);
        }  
		$language_content = $this->input->post('option_name_languages');  
		$category_id = $this->input->post('category_id');
		$updCond = array('_id' => MongoID($category_id ));
		$dataArr = array('option_name_languages' => $language_content);  
		$this->review_model->update_details(REVIEW_OPTIONS,$dataArr ,$updCond);
		$this->setErrorMessage('success', 'Language content updated successfully','language_content_updated_successfully');
        redirect(ADMIN_ENC_URL.'/reviews/display_reviews_options_list');
	}
	
	
}

/* End of file reviews.php */
/* Location: ./application/controllers/admin/reviews.php */