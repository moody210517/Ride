<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');
/**
* 
* This model contains all db functions related to Currency management
* @author Casperon
*
**/
 
class Currency_model extends My_Model{
	public function __construct(){
		parent::__construct();
	}
	
	/**
	* 
	* This function check the currency
	* @param String $collection
	* @param Array $condition
	* @param Array $primary_condition
	* 
	**/
	public function chk_currency_exist($collection='',$condition=array(),$primary_condition=array()){
		$this->mongo_db->select(array('_id'));
		$this->mongo_db->where($primary_condition);
		$this->mongo_db->where_or($condition);
		$res = $this->mongo_db->get($collection);
		return $res;
	}
}	