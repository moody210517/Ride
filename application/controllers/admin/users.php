<?php if (!defined('BASEPATH')) exit('No direct script access allowed');

/**
*
*   Users Management
* 
*   @package    CI
*   @subpackage Controller
*   @author Casperon
*
**/

class Users extends MY_Controller {

    function __construct() {
        parent::__construct();
        $this->load->helper(array('cookie', 'date', 'form','ride_helper'));
        $this->load->library(array('encrypt', 'form_validation'));
        $this->load->model(array('user_model','review_model','rides_model'));

        if ($this->checkPrivileges('user', $this->privStatus) == FALSE) {
            redirect(ADMIN_ENC_URL);
        }
    }

    /**
    * 
    * Redirect to Users List
    *
    * @return HTTP REDIRECT Users List
    *
    **/
    public function index() {
        if ($this->checkLogin('A') == '') {
            redirect(ADMIN_ENC_URL);
        } else {
            redirect(ADMIN_ENC_URL.'/users/display_user_list');
        }
    }

    /**
    * 
    * Display Users List
    *
    * @return HTML, Users List
    *
    **/
    public function display_user_list() {
        if ($this->checkLogin('A') == '') {
            redirect(ADMIN_ENC_URL);
        } else {
			if ($this->lang->line('admin_menu_users_list') != '') 
		    $this->data['heading']= stripslashes($this->lang->line('admin_menu_users_list')); 
		    else  $this->data['heading'] = 'Users List';
			
			$condition = array('status' => array('$ne' => 'Deleted'));
			
			$user_type = $this->input->get('user_type');
			if($user_type == 'deleted'){
				$condition = array('status' => 'Deleted');
			}
			
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
            if ((isset($_GET['type']) && isset($_GET['value'])) && ($_GET['type'] != '' && $_GET['value'] != '')) {
                if (isset($_GET['type']) && $_GET['type'] != '') {
                    $this->data['type'] = $_GET['type'];
                }
                if (isset($_GET['value']) && $_GET['value'] != '') {
                    $this->data['value'] = $_GET['value'];
                    $filter_val = $this->data['value'];
                }
				$this->data['filter'] = 'filter';
				if($_GET['type'] == 'phone_number') {
					#$filterArr = array("phone_number" => urldecode($filter_val),"country_code" => urldecode($_GET['country']));
					$condition["phone_number"] = urldecode($filter_val);
					$condition["country_code"] = "+".trim(urldecode($_GET['country']));
				} else {
					$filterArr = array($this->data['type'] => urldecode($filter_val));
				}   
            }
			
            $usersCount = $this->user_model->get_all_counts(USERS, $condition, $filterArr);
            if ($usersCount > 1000) {
                $searchPerPage = 500;
                $paginationNo = $this->uri->segment(4);
                if ($paginationNo == '') {
                    $paginationNo = 0;
                } else {
                    $paginationNo = $paginationNo;
                }

                $this->data['usersList'] = $this->user_model->get_all_details(USERS, $condition,$sortArr, $searchPerPage, $paginationNo, $filterArr);
				if($user_type == 'deleted'){
				
					$searchbaseUrl = ADMIN_ENC_URL.'/users/display_user_list?user_type=deleted';
					
				} else {
					$searchbaseUrl = ADMIN_ENC_URL.'/users/display_user_list';
				}
				
                $config['num_links'] = 3;
                $config['display_pages'] = TRUE;
                $config['base_url'] = $searchbaseUrl;
                $config['total_rows'] = $usersCount;
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

                $this->load->view(ADMIN_ENC_URL.'/users/display_userlist', $this->data);
            } else {
               
			   
                $this->data['usersList'] = $this->user_model->get_all_details(USERS, $condition, $sortArr, '', '', $filterArr);

                #$this->data['usersList'] = $this->user_model->get_user_details($this->data['usersList'],USER_LOCATION,'_id','user_id');

                $this->data['paginationLink'] = '';
                $this->load->view(ADMIN_ENC_URL.'/users/display_userlist', $this->data);
            }
        }
    }

    /**
    * 
    * Display User Dashboard
    *
    * @return HTML, User Dashboard
    *
    **/
    public function display_user_dashboard() {
        if ($this->checkLogin('A') == '') {
            redirect(ADMIN_ENC_URL);
        } else {
		    if ($this->lang->line('admin_users_users_dashboard') != '') 
		    $this->data['heading']= stripslashes($this->lang->line('admin_users_users_dashboard')); 
		    else  $this->data['heading'] = 'Users Dashboard';
			
            $this->data['totalUsersList'] = $this->user_model->get_all_counts(USERS, array());
            $this->data['totalActiveUser'] = $this->user_model->get_all_counts(USERS, array('status' => 'Active'));
            $this->data['totalInactiveUser'] = $this->user_model->get_all_counts(USERS, array('status' => 'Inactive'));
            $this->data['totalDeletedUser'] = $this->user_model->get_all_counts(USERS, array('status' => 'Deleted'));
			
			$overAllUsers = $this->user_model->get_selected_fields(USERS, array(),array('status'));
			
			$userIDS = array();
			$k = 0;
			if($overAllUsers->num_rows()>0){
				foreach ($overAllUsers->result() as $roww) {
					if($roww->status !="Deleted"){
						$userIDS[$k] = (string)$roww->_id;
						$k++;
					}
				}
			}
			
            $this->data['totalRidedUser'] = $this->user_model->get_unrided_user($userIDS);
            $this->data['totalunRidedUser'] = ($this->data['totalUsersList'] -$this->data['totalDeletedUser']) - $this->data['totalRidedUser'];
			
            $selectedFileds = array('user_name', 'email', 'image', 'status');
            $this->data['recentusersList'] = $this->user_model->get_selected_fields(USERS, array(), $selectedFileds, array('_id' => 'DESC'), 3, 0);
			$top_user=$this->review_model->top_review_user();
			$top_rider=$this->rides_model->get_top_rider();
			$top_revenue=$this->rides_model->get_top_revenue();
			$this->data['top_user']=$top_user;
			$this->data['top_rider']=$top_rider;
			$this->data['top_revenue']=$top_revenue;
            $this->load->view(ADMIN_ENC_URL.'/users/display_user_dashboard', $this->data);
        }
    }

    /**
    * 
    * Updates a User
    * 
    * @param string $user_id user MongoDB\BSON\ObjectId
    * @return HTTP REDIRCT Users List
    *
    **/ 
    function update_user_details() {
        if ($this->checkLogin('A') == '') {
            $this->setErrorMessage('error', 'You must login first','admin_driver_login_first');
            redirect(ADMIN_ENC_URL);
        }

        $user_id = $this->input->post('user_id');

        $config['overwrite'] = FALSE;
		$config['encrypt_name'] = TRUE;
        $config['allowed_types'] = 'jpg|jpeg|gif|png';
        $config['max_size'] = 2000;
        $config['upload_path'] = './images/users';
        $this->load->library('upload', $config);
        if ($this->upload->do_upload('thumbnail')) {
            $logoDetails = $this->upload->data();
            $this->ImageResizeWithCrop(600, 600, $logoDetails['file_name'], './images/users/');
            @copy('./images/users/' . $logoDetails['file_name'], './images/users/thumb/' . $logoDetails['file_name']);
            $this->ImageResizeWithCrop(210, 210, $logoDetails['file_name'], './images/users/thumb/');
            $profile_image = $logoDetails['file_name'];
            $inputArr['image'] = $logoDetails['file_name'];
        } else {
            $logoDetails = $this->upload->display_errors();
            $this->setErrorMessage('error', $logoDetails);
            redirect(ADMIN_ENC_URL.'/users/edit_user_form/' . $user_id);
        }
        $datestring = "%Y-%m-%d H:i:s";
        $time = time();
        $inputArr['modified'] = date('Y-m-d H:i:s');
        $condition = array('_id' => MongoID($user_id));
        $this->user_model->update_details(USERS, $inputArr, $condition);
        $this->setErrorMessage('success', 'User updated successfully','admin_user_updated_success');
        redirect(ADMIN_ENC_URL.'/users/display_user_list');
    }

    /**
    * 
    * Insert/Update a user
    * 
    * @param string $user_id user MongoDB\BSON\ObjectId
    * @param string $user_name user name
    * @param string $email user email
    * @param string $country_code user country code
    * @param string $phone_number user phone number
    * @return HTTP REDIRCT View user
    *
    **/
    public function insertEditUser() {
        if ($this->checkLogin('A') == '') {
            redirect(ADMIN_ENC_URL);
        } else { #echo '<pre>'; print_r($_POST); die;
            $user_id = $this->input->post('user_id');
            $user_name = $this->input->post('user_name');
            $email = trim($this->input->post('email'));
			
			$country_code = $this->input->post('country_code');
			$phone_number = $this->input->post('phone_number');
			
            if ($user_id == '') {
				$condition = array('email' => $email);
				$duplicate_mail = $this->user_model->get_all_details(USERS, $condition);
				if ($duplicate_mail->num_rows() > 0) {
					$this->setErrorMessage('error', 'User email already exists','admin_user_email_already_exist');
					echo "<script>window.history.go(-1);</script>";exit;
				}	
				$condition = array('country_code' => $country_code,'phone_number' => $phone_number);
				$duplicate_mobile = $this->user_model->get_all_details(USERS, $condition);
				if ($duplicate_mobile->num_rows() > 0) {
					$this->setErrorMessage('error', 'User mobile number already exists','admin_user_mobile_already_exist');
					echo "<script>window.history.go(-1);</script>";exit;
				}
            }else{
				$condition = array('country_code' => $country_code,'phone_number' => $phone_number,'_id' => array('$ne' => MongoID($user_id)));
				$duplicate_mobile = $this->user_model->get_all_details(USERS, $condition);
				if ($duplicate_mobile->num_rows() > 0) {
					$this->setErrorMessage('error', 'User mobile number already exists','admin_user_mobile_already_exist');
					echo "<script>window.history.go(-1);</script>";exit;
				}
				$condition = array('email' => $email,'_id' => array('$ne' => MongoID($user_id)));
				$duplicate_mail = $this->user_model->get_all_details(USERS, $condition);
				if ($duplicate_mail->num_rows() > 0) {
					$this->setErrorMessage('error', 'User email already exists','admin_user_email_already_exist');
					echo "<script>window.history.go(-1);</script>";exit;
				}	
			}
            $excludeArr = array("user_id", "thumbnail", "group", "status", "phone_number", "country_code");
			$inputArr = array();
			if ($this->input->post('status') == 'on') {
				$user_status = 'Active';
			} else {
				$user_status = 'Inactive';
			}
									
			$datestring = "%Y-%m-%d";
			$time = time();
			$config['encrypt_name'] = TRUE;
			$config['overwrite'] = FALSE;
			$config['allowed_types'] = 'jpg|jpeg|gif|png';
			$config['max_size'] = 2000;
			$config['upload_path'] = './images/users';
			$this->load->library('upload', $config);
						
			if ($_FILES['thumbnail']['name'] != '') {
				if ($this->upload->do_upload('thumbnail')) {
					$logoDetails = $this->upload->data();
					$this->ImageResizeWithCrop(600, 600, $logoDetails['file_name'], './images/users/');
					@copy('./images/users/' . $logoDetails['file_name'], './images/users/thumb/' . $logoDetails['file_name']);
					$this->ImageResizeWithCrop(210, 210, $logoDetails['file_name'], './images/users/thumb/');
					$profile_image = $logoDetails['file_name'];
					$inputArr['image'] = $logoDetails['file_name'];
				}else{
					$logoDetails = $this->upload->display_errors();
					$this->setErrorMessage('error',$logoDetails);
					echo "<script>window.history.go(-1);</script>";exit;
				}
			}
			if ($user_id == '') {
				$password = $this->get_rand_str(12);
				$unique_code = $this->user_model->get_unique_id($user_name);
				$user_data = array(
										'user_type' => 'Admin',
										'country_code' => (string)$country_code,
										'phone_number' => (string)$phone_number,
										'unique_code' => $unique_code,
										'password' => md5($password),
										'created' => date("Y-m-d H:i:s"), 
										'status' => $user_status
				);
			} else {
				$user_data = array('modified' => date("Y-m-d H:i:s"),
													'country_code' => (string)$country_code,
													'phone_number' => (string)$phone_number,
													'status' => $user_status
												);
			}
			$dataArr = array_merge($inputArr, $user_data);
			if ($user_id == '') {
				$this->user_model->commonInsertUpdate(USERS, 'insert', $excludeArr, $dataArr, array());
				$this->setErrorMessage('success', 'User added successfully','admin_user_added_sucess');
				$user_id = (string)$this->mongo_db->insert_id();
				$last_insert_id = $user_id;
				
				
				$fields = array(
								'username' => (string) $last_insert_id,
								'password' => md5((string) $last_insert_id)
							);
				$url = $this->data['soc_url'] . 'create-user.php';
				$this->load->library('curl');
				$output = $this->curl->simple_post($url, $fields);				
				
				
				$this->user_model->simple_insert(REFER_HISTORY, array('user_id' => MongoID($last_insert_id)));
				$this->user_model->simple_insert(WALLET, array('user_id' => MongoID($last_insert_id), 'total' => floatval(0)));
				
                if($this->config->item('welcome_amount') > 0){
                    $trans_id = time() . rand(0, 2578);
                    $initialAmt = array('type' => 'CREDIT',
                        'credit_type' => 'welcome',
                        'ref_id' => '',
                        'trans_amount' => floatval($this->config->item('welcome_amount')),
                        'avail_amount' => floatval($this->config->item('welcome_amount')),
                        'trans_date' => MongoDATE(time()),
                        'trans_id' => $trans_id
                    );
                    $this->user_model->simple_push(WALLET, array('user_id' => MongoID($last_insert_id)), array('transactions' => $initialAmt));
                    $this->user_model->update_wallet((string) $last_insert_id, 'CREDIT', floatval($this->config->item('welcome_amount')));
				}
				
				$current_date = MongoDATE(strtotime(date("Y-m-d 00:00:00")));
				$field = array('user.hour_' . date('H') => 1, 'user.count' => 1);
				$this->app_model->update_stats(array('day_hour' => $current_date), $field, 1);
				
				$this->mail_model->send_user_register_confirmation_mail($user_id,$password);
			} else {
				$condition = array('_id' => MongoID($user_id));
				$this->user_model->commonInsertUpdate(USERS, 'update', $excludeArr, $dataArr, $condition);
				$this->setErrorMessage('success', 'User updated successfully','admin_user_updated_sucess');						
			}
			redirect(ADMIN_ENC_URL.'/users/view_user/'.$user_id);
        }
    }

    /**
	* 
	* Display Edit user page
	* 
	* @param string $user_id  user MongoDB\BSON\ObjectId
	* @return HTML, Edit user page 
	*
	**/	
    public function edit_user_form() {
        if ($this->checkLogin('A') == '') {
            redirect(ADMIN_ENC_URL);
        } else {
		    if ($this->lang->line('admin_users_edit_users') != '') 
		    $this->data['heading']= stripslashes($this->lang->line('admin_users_edit_users')); 
		    else  $this->data['heading'] = 'Edit User';
            $user_id = $this->uri->segment(4, 0);
            $condition = array('_id' => MongoID($user_id));
            $this->data['user_details'] = $this->user_model->get_all_details(USERS, $condition);
            if ($this->data['user_details']->num_rows() == 1) {
                $this->load->view(ADMIN_ENC_URL.'/users/edit_user', $this->data);
            } else {
                redirect(ADMIN_ENC_URL);
            }
        }
    }

    /**
	* 
	* Change the status of a user
	* 
	* @param string $mode 0/1 
	* @param string $user_id  user MongoDB\BSON\ObjectId
	* @return HTTP REDIRECT to Users List  
	*
	**/
    public function change_user_status() {
        if ($this->checkLogin('A') == '') {
            redirect(ADMIN_ENC_URL);
        } else {
            $mode = $this->uri->segment(4, 0);
            $user_id = $this->uri->segment(5, 0);
            $status = ($mode == '0') ? 'Inactive' : 'Active';
            $newdata = array('status' => $status);
            $condition = array('_id' => MongoID($user_id));
            $this->user_model->update_details(USERS, $newdata, $condition);
            $this->setErrorMessage('success', 'User Status Changed Successfully','admin_user_status_changed_success');
            redirect(ADMIN_ENC_URL.'/users/display_user_list');
        }
    }

    /**
	* 
	* View user
	* 
	* @param string $user_id  user MongoDB\BSON\ObjectId
	* @return HTML, View user
	*
	**/
    public function view_user() {
        if ($this->checkLogin('A') == '') {
            redirect(ADMIN_ENC_URL);
        } else {
		    if ($this->lang->line('admin_users_view_users') != '') 
		    $this->data['heading']= stripslashes($this->lang->line('admin_users_view_users')); 
		    else  $this->data['heading'] = 'View User';
			
            $user_id = $this->uri->segment(4, 0);
            $condition = array('_id' => MongoID($user_id));
            $this->data['user_details'] = $this->user_model->get_all_details(USERS, $condition);
            $userLocation = $this->user_model->get_all_details(USER_LOCATION, array('user_id' => MongoID($user_id)));
            $latlong = @implode(array_reverse($userLocation->row()->geo), ',');
			
			if($latlong != ''){
				$config['center'] = $latlong;
				$config['zoom'] = 'auto';
				$config['language'] = $this->data['langCode'];
				$this->googlemaps->initialize($config);
				$marker = array();
				$marker['position'] = $latlong;
				$this->googlemaps->add_marker($marker);
				$this->data['map'] = $this->googlemaps->create_map();
			} else {
				$this->data['map'] = '';
			}


            if ($this->data['user_details']->num_rows() == 1) {
                $this->load->view(ADMIN_ENC_URL.'/users/view_user', $this->data);
            } else {
                redirect(ADMIN_ENC_URL);
            }
        }
    }

    /**
	* 
	* Deletes a user
	* 
	* @param string $user_id  user MongoDB\BSON\ObjectId
	* @return HTTP REDIRECT to Users List 
	*
	**/
    public function delete_user() {
        if ($this->checkLogin('A') == '') {
            redirect(ADMIN_ENC_URL);
        } else {
            $user_id = $this->uri->segment(4, 0);
            $condition = array('_id' => MongoID($user_id));

            $user_details = $this->user_model->get_all_details(USERS, $condition);
            $this->user_model->update_details(USERS, array('status' => 'Deleted'), $condition);

            $this->setErrorMessage('success', 'User deleted successfully','admin_user_delete_success');
            redirect(ADMIN_ENC_URL.'/users/display_user_list');
        }
    }

    /**
	* 
	* Change the status of multiple Users
	* 
	* @return HTTP REDIRECT to Users List
	*
	**/
    public function change_user_status_global() {
        if (count($_POST['checkbox_id']) > 0 && $_POST['statusMode'] != '') {
            $this->user_model->activeInactiveCommon(USERS, '_id',FALSE);
            if (strtolower($_POST['statusMode']) == 'delete') {
                $this->setErrorMessage('success', 'User records deleted successfully','admin_user_records_delete');
            } else {
                $this->setErrorMessage('success', 'User records status changed successfully','admin_user_records_status_change');
            }
            redirect(ADMIN_ENC_URL.'/users/display_user_list');
        }
    }
    
    /**
    * 
    * Notification Users List
    *
    * @return HTML, Notification Users List
    *
    **/
    public function display_notification_user_list() {
        if ($this->checkLogin('A') == '') {
            redirect(ADMIN_ENC_URL);
        } else {
		    if ($this->lang->line('admin_menu_users_list') != '') 
		    $this->data['heading']= stripslashes($this->lang->line('admin_menu_users_list')); 
		    else  $this->data['heading'] = 'Users List';
            $filterArr = array();
            if ((isset($_GET['type']) && isset($_GET['value'])) && ($_GET['type'] != '' && $_GET['value'] != '')) {
                if (isset($_GET['type']) && $_GET['type'] != '') {
                    $this->data['type'] = $_GET['type'];
                }
                if (isset($_GET['value']) && $_GET['value'] != '') {
                    $this->data['value'] = $_GET['value'];
                    $filter_val = $this->data['value'];
                }
                $this->data['filter'] = 'filter';
                if ($this->data['type'] != 'location') {
                    $filterArr = array($this->data['type'] => $filter_val);
                } else {
                    $filterArr = array('address.street' => $filter_val, 'address.city' => $filter_val, 'address.state' => $filter_val, 'address.country' => $filter_val, 'address.zip_code' => $filter_val);
                }
            }
            $usersCount = $this->user_model->get_all_counts(USERS, array(), $filterArr);
            if ($usersCount > 1000) {
                $searchPerPage = 500;
                $paginationNo = $this->uri->segment(4);
                if ($paginationNo == '') {
                    $paginationNo = 0;
                } else {
                    $paginationNo = $paginationNo;
                }

                $this->data['usersList'] = $this->user_model->get_all_details(USERS, array(), array('created' => 'DESC'), $searchPerPage, $paginationNo, $filterArr);

                $searchbaseUrl = ADMIN_ENC_URL.'/users/display_user_list/';
                $config['num_links'] = 3;
                $config['display_pages'] = TRUE;
                $config['base_url'] = $searchbaseUrl;
                $config['total_rows'] = $usersCount;
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

                $this->load->view(ADMIN_ENC_URL.'/notification/display_notification_userlist', $this->data);
            } else {
                $condition = array();
                $this->data['usersList'] = $this->user_model->get_all_details(USERS, $condition, '', '', '', $filterArr);

                #$this->data['usersList'] = $this->user_model->get_user_details($this->data['usersList'],USER_LOCATION,'_id','user_id');

                $this->data['paginationLink'] = '';
                $this->load->view(ADMIN_ENC_URL.'/notification/display_notification_userlist', $this->data);
            }
        }
    }

	/**
	* 
	* Deletes a user permanently
	* 
	* @param string $user_id user MongoDB\BSON\ObjectId
	* @return HTTP REDIRECT to deleted user list
	*
	**/
	public function delete_user_permanently() {
        if ($this->checkLogin('A') == '') {
            redirect(ADMIN_ENC_URL);
        } else {
            $user_id = $this->uri->segment(4, 0);
            $condition = array('_id' => MongoID($user_id));

            $this->user_model->commonDelete(USERS,$condition);

            $this->setErrorMessage('success', 'User permanently deleted from system','admin_user_permanently_deleted');
            redirect(ADMIN_ENC_URL.'/users/display_user_list?user_type=deleted');
        }
    }
    
    
    /**
    * 
    * Display Change Password page
    * 
    * @param string $user_id user MongoDB\BSON\ObjectId
    * @return HTML, Change Password page 
    *
    **/ 
    public function change_password_form() {
        if ($this->checkLogin('A') == '') {
            redirect(ADMIN_ENC_URL);
        }
        $user_id = $this->uri->segment(4);
	 
	 if ($this->lang->line('admin_menu_users_list') != '') 
	 $this->data['heading']= stripslashes($this->lang->line('admin_change_user_password')); 
	 else  $this->data['heading'] = 'Change User Password';
	 
        $condition = array('_id' => MongoID($user_id));
        $this->data['user_details'] = $user_details = $this->user_model->get_all_details(USERS, $condition);
        $this->load->view(ADMIN_ENC_URL.'/users/change_password', $this->data);
    }
    
	
    /**
    * 
    * Update user Password
    * 
    * @param string $user_id user MongoDB\BSON\ObjectId
    * @param string $password user password
    * @return HTTP REDIRECT to Users List 
    *
    **/ 
	public function change_password() {
		 if ($this->checkLogin('A') == '' || $this->input->post('new_password') == '') {
		     redirect(ADMIN_ENC_URL);
		 }
		 $password = $this->input->post('new_password');
		 $user_id = $this->input->post('user_id');
		 $dataArr = array('password' => md5($this->input->post('new_password')));
		 $condition = array('_id' => MongoID($user_id));
		 $this->user_model->update_details(USERS, $dataArr, $condition);

		 
		 $userinfo = $this->user_model->get_all_details(USERS, $condition);
		 $this->send_user_pwd($password, $userinfo);

		 $this->setErrorMessage('success', 'User password changed and sent to user successfully','admin_user_password_changed_successfully');
		 redirect(ADMIN_ENC_URL.'/users/display_user_list');
	}
	
	/**
    * 
    * Email user new Password
    * 
    * @param string $userinfo user details
    * @param string $pwd user password
    * @return HTTP REDIRECT to Users List 
    *
    **/
	public function send_user_pwd($pwd = '', $userinfo) {
		$default_lang=$this->config->item('default_lang_code');
		 $driver_name = $userinfo->row()->user_name;
		 $newsid = '2';
		 $template_values = $this->user_model->get_email_template($newsid,$default_lang);
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
		     'to_mail_id' => $userinfo->row()->email,
		     'subject_message' => $template_values['subject'],
		     'body_messages' => $message
		);
		$email_send_to_common = $this->user_model->common_email_send($email_values);
	}

	/**
    * 
    * Display User Wallet detail
    *
    * @param string $user_id User MongoDB\BSON\ObjectId
    * @return HTML, User Wallet detail
    *
    **/
	public function view_wallet() {
			if ($this->checkLogin('A') == '') {
				redirect(ADMIN_ENC_URL);
			} else {
			 if ($this->lang->line('admin_menu_users_list') != '') 
		    $this->data['heading']= stripslashes($this->lang->line('admin_menu_users_list')); 
		    else  $this->data['heading'] = 'Users List';
			 $user_id = $this->uri->segment(4, 0);
			 $transType_cond='';
			$wallet_history = $this->user_model->user_transaction($user_id, $transType_cond);
			
			$this->data['user_info'] =  $this->user_model->get_selected_fields(USERS,array('_id' => MongoID($user_id)),array('user_name','email'));
			
			 $ref_id = 0;
			 if(isset($wallet_history['result'][0]['transactions'])){
				$transactionsList = $wallet_history['result'][0]['transactions'];
				foreach($transactionsList as $referral){
				   if(isset($wallet_history['result'][0]['transactions'][$ref_id]['credit_type'])){
					  if($wallet_history['result'][0]['transactions'][$ref_id]['credit_type'] == 'referral'){
						 $userName = $this->user_model->get_selected_fields(USERS,array('_id' => MongoID($referral['ref_id'])),array('user_name'));
						 if(isset($userName->row()->user_name)){
							$wallet_history['result'][0]['transactions'][$ref_id]['ref_user_name'] = $userName->row()->user_name;
						 }
					  }
				   }
				   $ref_id++;
				}
			 }
			$this->data['wallet_history'] =$wallet_history['result'];
			$this->load->view(ADMIN_ENC_URL.'/users/view_wallet', $this->data);
       }
	}
	 
	/**
    * 
    * Update User Wallet Amount
    *
    * @param string $user_id User MongoDB\BSON\ObjectId
    * @param string $total_amount amount
    * @return HTML, User Wallet detail
    *
    **/
	function add_money_to_user(){
			if ($this->checkLogin('A') == '') {
				redirect(ADMIN_ENC_URL);
			} 
			
			$total_amount = $this->input->post('trans_amount');
			$user_id  =  $this->input->post('user_id');
			if($total_amount  != '' && $user_id  != ''){
				
				$this->user_model->update_wallet((string) $user_id, 'CREDIT', floatval($total_amount));
				$currentWallet = $this->user_model->get_selected_fields(WALLET, array('user_id' => MongoID($user_id)), array('total'));
				$user_info = $this->user_model->get_selected_fields(USERS, array('_id' => MongoID($user_id)), array('user_name','country_code','phone_number','email'));
				$avail_amount = 0.00;
				if ($currentWallet->num_rows() > 0) {
					if (isset($currentWallet->row()->total)) {
						$avail_amount = floatval($currentWallet->row()->total);
					}
				}
				$txn_time = time();
				$initialAmt = array('type' => 'CREDIT',
					'credit_type' => 'recharge',
					'ref_id' => 'admin',
					'trans_amount' => floatval($total_amount),
					'avail_amount' => floatval($avail_amount),
					'trans_date' => MongoDATE($txn_time),
					'trans_id' => $txn_time
				);
				$this->user_model->simple_push(WALLET, array('user_id' => MongoID($user_id)), array('transactions' => $initialAmt));
				$this->load->model('mail_model');
				$this->load->model('sms_model');
				$this->mail_model->wallet_recharge_successfull_notification($initialAmt, $user_info, $txn_time, $txn_time);
				$user_name  = $user_info->row()->user_name;
				$country_code  = $user_info->row()->country_code;
				$phone_number  = $user_info->row()->phone_number;
				
				$this->sms_model->send_wallet_money_credit_sms($country_code,$phone_number,$user_name,$total_amount,$avail_amount);
				
				$this->setErrorMessage('success', 'Money has been added successfully','admin_user_money_added');
		} else {
			$this->setErrorMessage('error', 'Please enter valid amount','admin_user_money_invalid');
		}
		redirect(ADMIN_ENC_URL.'/users/view_wallet/'.$user_id);
	}
	
	/**
    * 
    * Display Add New User page
    *
    * @return HTML, Add New User page
    *
    **/

	function add_user_form(){
		if ($this->checkLogin('A') == '') {
			redirect(ADMIN_ENC_URL);
		} 
		if ($this->lang->line('admin_rides_add_new_user') != '') 
				$this->data['heading']= stripslashes($this->lang->line('admin_rides_add_new_user')); 
		else  $this->data['heading'] = 'Add New User';
		$this->load->view(ADMIN_ENC_URL.'/users/add_user', $this->data);
	}


	/**
    * 
    * Checks User email and phone number Duplication
    *
    * @param string $user_id User MongoDB\BSON\ObjectId
	* @param string $email email
	* @param string $dial_code dial code
	* @param string $phone_number phone number
    * @return JSON contains the user exsistance
    *
    **/

	function check_user_duplicate(){ 
		$returnArr['status'] = '0';
		$email = $this->input->post('email');
		$user_id = (string)$this->input->post('user_id');
		$dial_code = $this->input->post('dial_code');
		$phone_number = $this->input->post('phone_number');
		$emailCond = array('email' => $email);
		
        $emCheck = TRUE;
        $em_mailCheck = TRUE;
        $em_mobCheck = TRUE;  
        $emErr = '';
        
        if($user_id != ''){
            $user_info = $this->user_model->get_selected_fields(USERS,array('_id' => MongoID($user_id)),array('emergency_contact'));
            if($user_info->num_rows() == 1){
                if(isset($user_info->row()->emergency_contact['em_email']) && $user_info->row()->emergency_contact['em_email'] == $email){
                    $emCheck = FALSE;
                    $em_mailCheck = FALSE;
                    
                    if ($this->lang->line('admin_user_emergency_email_same') != '') 
                        $emErr = stripslashes($this->lang->line('admin_user_emergency_email_same')); 
                    else  $emErr = 'User email address and emergency contact email address should not be same';
                    
                }
                
                if(isset($user_info->row()->emergency_contact['em_mobile']) && $user_info->row()->emergency_contact['em_mobile'] == $phone_number && isset($user_info->row()->emergency_contact['em_mobile_code']) && $user_info->row()->emergency_contact['em_mobile_code'] == $dial_code){
                    $emCheck = FALSE;
                    $em_mobCheck = FALSE;
                    if ($this->lang->line('admin_user_emergency_phone_same') != '') 
                        $emErr = stripslashes($this->lang->line('admin_user_emergency_phone_same')); 
                    else  $emErr = 'User mobile and emergency contact mobile numbers should not be same';
                }
                
                if($em_mailCheck == FALSE && $em_mobCheck == FALSE){
                     if ($this->lang->line('admin_user_emergency_details_same') != '') 
                        $emErr = stripslashes($this->lang->line('admin_user_emergency_details_same')); 
                    else  $emErr = 'User details and emergency contact details should not be same';
                }
            }
        }
        
        if($emCheck == TRUE){
        
            if($user_id != ''){
                $emailCond = array('email'=>$email,'_id' =>array('$ne'=>MongoID($user_id)));
            } 
            $chekEmail = $this->user_model->get_selected_fields(USERS,$emailCond,array('_id','email'));
            $emailExist = 'No';
            if($chekEmail->num_rows() > 0){
                $emailExist = 'Yes';
            } 
            $PhoneCond = array('country_code' => $dial_code,'phone_number' => $phone_number);
            if($user_id != ''){
                $PhoneCond = array('country_code' => $dial_code,'phone_number' => $phone_number,'_id' => array('$ne' => MongoID($user_id)));
            }
            $chekPhone = $this->user_model->get_selected_fields(USERS,$PhoneCond,array('_id'));
            $phoneExist = 'No';
            if($chekPhone->num_rows() > 0){
                $phoneExist = 'Yes';
            }
            
            
            if ($this->lang->line('admin_user_user_details_already_exists') != '') 
                $response = stripslashes($this->lang->line('admin_user_user_details_already_exists')); 
            else  $response = 'User details already exists';
            
            if($emailExist == 'No' && $phoneExist == 'No'){
                $returnArr['status'] = '1';
            } else {
                if($emailExist == 'Yes' && $phoneExist == 'Yes'){
                    if ($this->lang->line('admin_user_email_and_phone_already_exist') != '') 
                        $response = stripslashes($this->lang->line('admin_user_email_and_phone_already_exist')); 
                    else  $response = 'User email and mobile number already exists';
                }  else if($emailExist == 'Yes'){
                    if ($this->lang->line('admin_user_email_already_exist') != '') 
                        $response = stripslashes($this->lang->line('admin_user_email_already_exist')); 
                    else  $response = 'User email already exists';
                }  else if($phoneExist == 'Yes'){
                    if ($this->lang->line('admin_user_mobile_already_exist') != '') 
                        $response = stripslashes($this->lang->line('admin_user_mobile_already_exist')); 
                    else  $response = 'User mobile number already exists';
                }
            }
        } else {
            $response = $emErr;
        }
		$returnArr['response'] = $response;
		echo json_encode($returnArr); exit;
	}

}

/* End of file users.php */
/* Location: ./application/controllers/admin/users.php */