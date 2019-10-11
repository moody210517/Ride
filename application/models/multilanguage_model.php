<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

/**
 * 
 * This model contains all db functions related to user management
 * @author Casperon
 *
 */
class Multilanguage_model extends My_Model
{
	public function __construct() 
	{
		parent::__construct();
	}
	
	/**
    * 
    * Getting Users details
    * @param String $condition
	*
    **/
   public function get_language_list(){
		$this->mongo_db->select();
		$this->mongo_db->order_by(array('name' => 'asc'));
		$res = $this->mongo_db->get(LANGUAGES);
		return $res;
   }
   /**
    * 
    * Change language status
    * @param String $Mode
    * @param String $condition
	*
    **/
    public function change_language_status($statusMode='',$checkbox_id=array()){
		#$condArr = array('lang_code'=> array('$ne' => 'en'));
		$condArr = array();
   		if($statusMode == 'Active' || $statusMode == 'Inactive'){			
			$data = array('status' => $statusMode);
			$this->mongo_db->where($condArr);
			$this->mongo_db->where_in('_id', $checkbox_id);
			$this->mongo_db->set($data);
			$this->mongo_db->update_all(LANGUAGES); 
			}else if($statusMode == 'Delete') {
				$this->mongo_db->where_in('_id', $checkbox_id);
				$this->mongo_db->where($condArr);
				$this->mongo_db->delete_all(LANGUAGES); 
			}
			return 1;
		
   }
   /**
    * 
    * To delete LANGUAGES
    * @param Integer $ID
    */
   public function delete_language($languageId = ''){   
		$updateCond = array('_id' => MongoID($languageId),'lang_code'=> array('$ne' => 'en'));
		$this->mongo_db->where($updateCond);
		$this->mongo_db->delete(LANGUAGES);
		return 1;
   }   
   /**
    * 
    * To change Language Details
    * @param String $Current status
    * @param Integer $ID
    */
    public function change_language_details($current_status = '',$languageId=''){
    	if($current_status ==  'Active'){
			$new_status = 'Inactive';
		}else if($current_status == 'Inactive'){	
			$new_status = 'Active';
		}else{		 
			$new_status = 'Active';
		}
		$updateCond = array('_id' => MongoID($languageId));
		$updateData = array('status' => $new_status);		 
		$this->mongo_db->where($updateCond);
		$this->mongo_db->set($updateData);
		$this->mongo_db->update_all(LANGUAGES); 
		return 1;   	
   }
   
  
}