<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

class Generate_invoice extends MY_Controller {
	function __construct(){
        parent::__construct();
		$this->load->helper(array('cookie','date','form'));
		$this->load->library(array('encrypt','form_validation'));		
		$this->load->model(array('user_model'));
		$this->load->model(array('mail_model'));
		$this->load->model(array('app_model'));
    }
	public function index(){
		$checkRide = $this->app_model->get_all_details(RIDES,array('ride_status'=>'Completed'));
		if($checkRide->num_rows() > 0){
			foreach($checkRide->result() as $ride){
				$this->mail_model->generate_invoice_mail($ride->ride_id,'','No');
			}
		}
	}
}

/* End of file generate_invoice.php */
/* Location: ./application/controllers/generate_invoice.php */