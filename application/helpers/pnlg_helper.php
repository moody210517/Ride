<?php  if ( ! defined('BASEPATH')) exit('No direct script access allowed');

/**
*
* Returns the language of the particular driver or user
*
**/
if ( ! function_exists('get_user_language')) {
	function get_user_language($user_type = '', $id = '') {
		$ci =& get_instance();
		if($user_type != '' && $id != ''){
			if($user_type = 'user'){
				$collection = USERS;
			} else if($user_type = 'driver'){
				$collection = DRIVERS;
			}
			$selFields = array("lang_code");
			$condition = array('_id' => MongoID($id));
			$user_details = $ci->app_model->get_selected_fields($collection,$condition,$selFields);
			return $user_details;
		}
	}
}


/* End of file pnlg_helper.php */
/* Location: ./application/helpers/pnlg_helper.php */