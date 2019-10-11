<?php
error_reporting(0);
if (!defined('BASEPATH'))
    exit('No direct script access allowed');

class MY_Input extends CI_Input {


	public function __construct() {
		$this->_POST_RAW = $_POST; //clone raw post data 
		parent::__construct(); 
	}

	public function post($index = null, $xss_clean = TRUE) {
		if(!$xss_clean){
			if(is_array($this->_POST_RAW[$index])){
				$orgVals = $this->_POST_RAW[$index];
				$keyVals = array_keys($orgVals);
				$post_data = array();
				foreach($keyVals as $keys){
					$post_data["$index"][$keys] = $orgVals[$keys];
				}
				return $post_data;
			}else{
					return $this->_POST_RAW[$index];
			}
			
		}
		return parent::post($index, $xss_clean); 
	}

}

/* End of file MY_Input.php */
/* Location: ./application/core/MY_Input.php */