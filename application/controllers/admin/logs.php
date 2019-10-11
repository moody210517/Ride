<?php

if (!defined('BASEPATH'))
    exit('No direct script access allowed');

/**
*
*	Logs
* 
*	@package		CI
*	@subpackage		Controller
*	@author			Casperon
*
**/
class Logs extends MY_Controller {

    function __construct() {
        parent::__construct();
        $this->load->helper(array('cookie', 'date', 'form'));
        $this->load->library(array('encrypt', 'form_validation'));
        if ($this->checkPrivileges('Logs', $this->privStatus) == FALSE) {
            redirect(ADMIN_ENC_URL);
        }
    }

	/**
	*
	* Display the logs list for any action done in admin panel
	* 	
	* @return HTML to show the log list
	*	
	**/
    public function display_logs() {
        if ($this->checkLogin('A') == '') {
            redirect(ADMIN_ENC_URL);
        } else {
		    if ($this->lang->line('admin_view_logs') != '') 
		    $this->data['heading']= stripslashes($this->lang->line('admin_view_logs')); 
		    else  $this->data['heading'] = 'View Logs';
			
			$logfilesList = glob("log/logs/*.txt");
            $this->data['logfilesList'] = $logfilesList;
			$this->data['filedir'] = $logfilesList[count($logfilesList)-1];
			
            $this->load->view(ADMIN_ENC_URL.'/logs/display_logs', $this->data);
        }
    }

}

/* End of file Logs.php */
/* Location: ./application/controllers/admin/Logs.php */