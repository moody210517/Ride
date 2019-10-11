<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');
 /**
* 
* This model contains all db functions related to brand management
* @author Casperon
*
**/

class Brand_model extends My_Model{

	public function __construct(){
		parent::__construct();
	}
	
	/**
	* 
	* This function get the vehicle types (and return the id)
	* @param String $collection
	* @param Array $condition
	* 
	**/
	public function get_all_vehicles($collection='',$condition=array()){
		$this->mongo_db->select(array('_id','vehicle_type'));
		$this->mongo_db->where($condition);
		$res = $this->mongo_db->get($collection);
		return $res;
	}
	
}

?>