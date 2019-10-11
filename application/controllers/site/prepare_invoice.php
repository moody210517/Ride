<?php if (!defined('BASEPATH')) exit('No direct script access allowed');

/**
* 
* invoice related functions
* @author Casperon
*
*/
class Prepare_invoice extends MY_Controller {

    function __construct() {
        parent::__construct();
        $this->load->helper(array('cookie', 'date', 'form', 'email'));
        $this->load->library(array('encrypt', 'form_validation'));
        $this->load->model('app_model');
		$this->load->model('mail_model');
    }

    /**
    * 
    * This function is used for create and send invoice to the rider
    * 
    **/
    public function make_and_send() {
        $ride_id = (string)$this->input->post('ride_id');
		if($ride_id!=''){
			$this->mail_model->send_invoice_mail($ride_id);
        }
    }

}

/*End of file prepare_invoice.php */
/* Location: ./application/controllers/site/prepare_invoice.php */