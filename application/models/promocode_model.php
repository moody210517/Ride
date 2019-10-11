<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

/**
 * 
 * This model contains all db functions related to promocode management
 * @author Casperon
 *
 */
class Promocode_model extends My_Model{
	public function __construct(){
        parent::__construct();
    }
	
	public function check_code_exist($condition,$promo_id){ 
		$this->mongo_db->select(array('_id')); 
		$this->mongo_db->where($condition);
		$this->mongo_db->where_ne('_id',MongoID($promo_id));
		$res = $this->mongo_db->get(PROMOCODE);		
		return $res;
	}
}