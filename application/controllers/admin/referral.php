<?php if (!defined('BASEPATH')) exit('No direct script access allowed');

/**
*
*   Referrals Management
* 
*   @package    CI
*   @subpackage Controller
*   @author Casperon
*
**/

class Referral extends MY_Controller {

    function __construct() {
        parent::__construct();
        $this->load->helper(array('cookie', 'date', 'form'));
        $this->load->library(array('encrypt', 'form_validation'));
        $this->load->model(array('user_model'));

        if ($this->checkPrivileges('referral', $this->privStatus) == FALSE) {
            redirect(ADMIN_ENC_URL);
        }
    }

    /**
    * 
    * Redirect to User Referrals lists
    *
    * @return HTTP REDIRECT User Referrals list
    *
    **/
    public function index() {
        if ($this->checkLogin('A') == '') {
            redirect(ADMIN_ENC_URL);
        } else {
            redirect(ADMIN_ENC_URL.'/referral/display_user_referrals');
        }
    }

    /**
    * 
    * Display User Referrals List
    *
    * @return HTML, User Referrals List
    *
    **/
    public function display_user_referrals() {
        if ($this->checkLogin('A') == '') {
            redirect(ADMIN_ENC_URL);
        } else {
		    if ($this->lang->line('admin_referral_history_users_referral_history') != '') 
		    $this->data['heading']= stripslashes($this->lang->line('admin_referral_history_users_referral_history')); 
		    else  $this->data['heading'] = 'Users Referral History';
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
               
                    $filterArr = array($this->data['type'] => $filter_val);
                 
            }
			$condition = array('referral_count' => array('$gt' => 0));
            $usersCount = $this->user_model->get_all_counts(USERS, $condition, $filterArr);
            if ($usersCount > 1000) {
                $searchPerPage = 500;
                $paginationNo = $this->uri->segment(4);
                if ($paginationNo == '') {
                    $paginationNo = 0;
                } else {
                    $paginationNo = $paginationNo;
                }
                $this->data['usersList'] = $this->user_model->get_all_details(USERS, $condition, array('created' => 'DESC'), $searchPerPage, $paginationNo, $filterArr);

                $searchbaseUrl = ADMIN_ENC_URL.'/referral/display_user_referrals/';
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
                $this->data['usersList'] = $this->user_model->get_all_details(USERS, $condition, '', '', '', $filterArr);
                $this->data['paginationLink'] = '';
				//echo"<pre>";print_R($this->data);die;
                $this->load->view(ADMIN_ENC_URL.'/referral/display_userlist', $this->data);
            }
        }
    }
	
    /**
    * 
    * Display Driver Referrals List
    *
    * @return HTML, Driver Referrals List
    *
    **/
	public function display_driver_referrals() {
        if ($this->checkLogin('A') == '') {
            $this->setErrorMessage('error', 'You must login first','admin_driver_login_first');
            redirect(ADMIN_ENC_URL);
        }
		if ($this->lang->line('driver_disp_driver_list') != '') 
		$this->data['heading']= stripslashes($this->lang->line('driver_disp_driver_list')); 
		else  $this->data['heading'] = 'Display Drivers List'; 
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
               
               $filterArr = array($this->data['type'] => $filter_val);
                 
            }
		$condition = array('referral_count' => array('$gt' => 0));
        $driversCount = $this->user_model->get_all_counts(DRIVERS, $condition,$filterArr);

        if ($driversCount > 1000) {
            $searchPerPage = 500;
            $paginationNo = $this->uri->segment(4);
            if ($paginationNo == '') {
                $paginationNo = 0;
            } else {
                $paginationNo = $paginationNo;
            }

            $this->data['driversList'] = $this->user_model->get_all_details(DRIVERS, $condition, array('created' => 'DESC'), $searchPerPage, $paginationNo,$filterArr);
            

            $searchbaseUrl = ADMIN_ENC_URL.'/referral/display_driver_referrals/';
            $config['num_links'] = 3;
            $config['display_pages'] = TRUE;
            $config['base_url'] = $searchbaseUrl;
            $config['total_rows'] = $driversCount;
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
        } else {
            $this->data['paginationLink'] = '';
            $this->data['driversList'] = $this->user_model->get_all_details(DRIVERS,  $condition, '', '', '', $filterArr);
        }
        $this->load->view(ADMIN_ENC_URL.'/referral/display_drivers_list', $this->data);
    }
	
    /**
    * 
    * Display Referral details
    *
    * @param string $refType  user/driver
    * @param string $mem_id  User/Driver MongoDB\BSON\ObjectId
    * @return HTML, Referral details
    *
    **/
	public function view_referral_details(){
		if ($this->checkLogin('A') == '') {
            redirect(ADMIN_ENC_URL);
		}
		$refType = $this->input->get('q');
		$mem_id = $this->uri->segment(4); 
		if ($this->lang->line('admin_referred_members_list') != ''){
			$referred_members_list = stripslashes($this->lang->line('admin_referred_members_list'));
		}else{
			$referred_members_list = 'Referred Members List';
		}
		
		if($refType == 'user'){
			$condition = array('user_id' => MongoID($mem_id));
			$getMember = $this->user_model->get_selected_fields(USERS,array('_id' => MongoID($mem_id)),array('user_name','email','unique_code'));
			$this->data['heading'] = $getMember->row()->user_name.' ( '.$getMember->row()->email.' ) - '.$referred_members_list;
		} else {
			$condition = array('driver_id' => MongoID($mem_id));
			$getMember = $this->user_model->get_selected_fields(DRIVERS,array('_id' => MongoID($mem_id)),array('driver_name','email','unique_code'));
			$this->data['heading'] = $getMember->row()->driver_name.' ( '.$getMember->row()->email.' ) - '.$referred_members_list;
		}
		$this->data['reff_member'] = $getMember;
		$this->data['referralsList'] = $referralsList = $this->user_model->get_all_details(REFER_HISTORY, $condition);

		$this->load->view(ADMIN_ENC_URL.'/referral/view_referrals_list', $this->data);
	}
}

/* End of file referral.php */
/* Location: ./application/controllers/admin/referral.php */