<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

class Connect_socket extends MY_Controller {
	function __construct(){
        parent::__construct();
		$this->load->helper(array('cookie','date','form'));
		$this->load->library(array('encrypt','form_validation'));		
		#$this->load->model(array('user_model'));
		error_reporting(-1);
    }
	public function index(){
		$condition = array();
		$usersList = $this->user_model->get_selected_fields(USERS,$condition,array('_id','password'));
		if($usersList->num_rows() > 0){
			foreach($usersList->result() as $user){
				$user_name = (string)$user->_id;
				$password = (string)$user->password;
				
				 $fields = array(
					'username' => $user_name,
					'password' => md5($user_name)
				);
				
				$url = $this->data['soc_url'].'create-user.php';
				$this->load->library('curl');
				$output = $this->curl->simple_post($url, $fields);
				
				
				var_dump($output);
				#return $result;
			}
		}
	}
	public function driver(){
		$condition = array();
		$usersList = $this->user_model->get_selected_fields(DRIVERS,$condition,array('_id','password'));
		if($usersList->num_rows() > 0){
			foreach($usersList->result() as $user){
				$user_name = (string)$user->_id;
				$password = (string)$user->password;
				
				 $fields = array(
					'username' => $user_name,
					'password' => md5($user_name)
				);
				
				$url = $this->data['soc_url'].'create-user.php';
				$this->load->library('curl');
				$output = $this->curl->simple_post($url, $fields);
								
				var_dump($output);
				#return $result;
			}
		}
	}
}

/* End of file generate_invoice.php */
/* Location: ./application/controllers/generate_invoice.php */