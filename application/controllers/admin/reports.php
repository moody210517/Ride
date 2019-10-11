<?php if (!defined('BASEPATH')) exit('No direct script access allowed');

/**
*
*	Reports
* 
*	@package		CI
*	@subpackage		Controller
*	@author			Casperon
*
**/

class Reports extends MY_Controller {

    function __construct() {
        parent::__construct();
        $this->load->helper(array('cookie', 'date', 'form'));
        $this->load->library(array('encrypt', 'form_validation'));
        if ($this->checkPrivileges('reports', $this->privStatus) == FALSE) {
            redirect(ADMIN_ENC_URL);
        }
     }

	/**
	*
	* Initiate to display the list of ride report for user
	* 	
	* @return http request to show the report list page
	*	
	**/
    public function index() {
        if ($this->checkLogin('A') == '') {
            redirect(ADMIN_ENC_URL);
        } else {
            redirect(ADMIN_ENC_URL.'/reports/display_reports_list');
        }
    }

	/**
	*
	* Display the list of report for makes user or driver
	* 	
	* @return http request to show the report list page
	*	
	**/
    public function display_reports_list() {
        if ($this->checkLogin('A') == '') {
            redirect(ADMIN_ENC_URL);
        } else {
		    if ($this->lang->line('admin_reports_list') != '') 
		    $this->data['heading']= stripslashes($this->lang->line('admin_reports_list')); 
		    else  $this->data['heading'] = 'Display Reports/Feedbacks List';
			 	$sortArr  = array('created_date' => 'DESC');
            $condition = array();
            $this->data['reportsList'] = $this->user_model->get_all_details(REPORTS, $condition,$sortArr);
            $this->load->view(ADMIN_ENC_URL.'/reports/display_reports_list', $this->data);
        }
    }
	
	/**
	*
	* Display the particular report for makes user or driver
	* 	
	* @return HTML to show the list reports in admin 
	*	
	**/
    public function view_reports_details() {
        if ($this->checkLogin('A') == '') {
            redirect(ADMIN_ENC_URL);
        } else {
			$report_id = $this->uri->segment(4);
		    if ($this->lang->line('admin_reports_view_reports_details') != '') 
		    $this->data['heading']= stripslashes($this->lang->line('admin_reports_view_reports_details')); 
		    else  $this->data['heading'] = 'View reports/feedbacks details';
            $condition = array('_id' => MongoID($report_id));
            $this->data['report_details'] = $this->user_model->get_all_details(REPORTS, $condition);
            $this->load->view(ADMIN_ENC_URL.'/reports/view_reports', $this->data);
        }
    }

	/**
	* 
	* Delete the record for particular reports
	* 
	* @param string $reports_id  Report MongoDB\BSON\ObjectId
	* @return http redirect show the list of ride reports
	*
	**/
    public function delete_reports() {
        if ($this->checkLogin('A') == '') {
            redirect(ADMIN_ENC_URL);
        } else {
            $reports_id = $this->uri->segment(4, 0);
            $condition = array('_id' => MongoID($reports_id));
            $this->user_model->commonDelete(REPORTS, $condition);
            $this->setErrorMessage('success', 'Reports deleted successfully','admin_reports_deleted_success');
            redirect(ADMIN_ENC_URL.'/reports/display_reports_list');
        }
    }

	/**
	* 
	* To change the particular report status
	* 
	* @param string $status status open/closed
	* @param string $reports_id report MongoDB\BSON\ObjectId
	* @return http redirect to show the report lists page
	*
	**/
    public function change_report_status() {
        if ($this->checkLogin('A') == '') {
            redirect(ADMIN_ENC_URL);
        } else {
            $mode = $this->uri->segment(4, 0);
            $reports_id = $this->uri->segment(5, 0);
            $status = ($mode == '0') ? 'open' : 'closed';
            $newdata = array('status' => $status);
            $condition = array('_id' => MongoID($reports_id));
            $this->user_model->update_details(REPORTS, $newdata, $condition);
            $this->setErrorMessage('success', 'Reports Status Changed Successfully','admin_reports_status_changed');
            redirect(ADMIN_ENC_URL.'/reports/display_reports_list');
        }
    }
	
	/**
	* 
	* replay the mail and description for rised report
	* 
	* @param string $_id id MongoDB\BSON\ObjectId
	* @param string $report_id Report
	* @param string $reply_message Description for sending the mail
	* @param string $reporter_name report name
	* @param string $subject subject of the mail
	* @param string $reported_on create a date for replay
	* @return http redirect to show the feedback for the particular report
	*
	**/
    public function reply_reports() {
        if ($this->checkLogin('A') == '') {
            redirect(ADMIN_ENC_URL);
        } else {
            $id = $this->input->post('id');
            $report_id = $this->input->post('report_id');
			$reply_message = $this->input->post('reply_message',FALSE);
			$reporter_email = $this->input->post('reporter_email');
			$reporter_name = $this->input->post('reporter_name');
			$subject = $this->input->post('subject');
			$reported_on = $this->input->post('reported_on');
			if($report_id != '' && $reply_message != ''){
				$newdata = array('status' => 'closed','reply_message' => $reply_message,'reply_date' => MongoDATE(time()));
				$condition = array('report_id' => (string)$report_id);
				$this->user_model->update_details(REPORTS, $newdata, $condition);
				$mailArr = array('reporter_email' => $reporter_email,
								'reporter_name' => $reporter_name,
								'reply_message' => $reply_message,
								'subject' => 'Re : '.$subject,
								'reported_date' => $reported_on,
								'report_id' => $report_id
				);
				$this->mail_model->reply_to_report_from_admin($mailArr);
				$this->setErrorMessage('success', 'Reports Status Changed Successfully','admin_reports_status_changed');
			}else{
				$this->setErrorMessage('error', 'Enter reply message to close this report','admin_enter_reply_message');
			}
            redirect(ADMIN_ENC_URL.'/reports/view_reports_details/'.$id);
        }
    }
	
	
    /**
     * 
     * This function change the report status, delete the report record
     */
    public function change_report_status_global() {
        if (count($_POST['checkbox_id']) > 0 && $_POST['statusMode'] != '') {
            $this->user_model->activeInactiveCommon(REPORTS, '_id',TRUE);
            if (strtolower($_POST['statusMode']) == 'delete') {
                 $this->setErrorMessage('success', 'Reports deleted successfully','admin_reports_deleted_success');
            } else {
                 $this->setErrorMessage('success', 'Reports Status Changed Successfully','admin_reports_status_changed');
            }
            redirect(ADMIN_ENC_URL.'/reports/display_reports_list');
        }
    }

}

/* End of file reports.php */
/* Location: ./application/controllers/admin/reports.php */