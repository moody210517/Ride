<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');
 /**
* 
* This model contains all db functions related to documents management
* @author Casperon
*
**/

class Documents_model extends My_Model{
	public function __construct(){
		parent::__construct(); 	
	}
	/**
	*
	* This functions returns types od document category
	*
	**/
	public function get_documents_type(){
		$pipeline = array(
								array(
									'$group' => array(
										'_id' =>'$category',
										'category'=>['$last'=>'$category']
									),
								),
								array(
									'$sort' => array(
										'category' =>1,
									)
								)
							);
		$res = $this->mongo_db->aggregate(DOCUMENTS,$pipeline);
		return $res;
	}
	
}

?>