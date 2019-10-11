<?php

if (!defined('BASEPATH'))
    exit('No direct script access allowed');
/*
 *
 * This model contains all db functions related to user management
 * @author Casperon
 *
 */

class User_model extends My_Model {

    public function __construct() {
        parent::__construct();
    }

    public function insert_user($user_data = array()) {
        if (!empty($user_data)) {
            $this->mongo_db->insert(USERS, $user_data);
        }
    }

    public function check_user_exist($condition = array()) {
        $this->mongo_db->select();
        $this->mongo_db->where($condition);
        return $res = $this->mongo_db->get(USERS);
    }

    public function get_user_details($origin, $refcollection, $primary, $reference) {
        if ($origin->num_rows() > 0) {
            $neworigin = $origin->result_array();
            foreach ($origin->result_array() as $key => $value) {
                $data = array($value[$primary]);
                $this->mongo_db->where_in($reference, $data);
                $res = $this->mongo_db->get($refcollection);
                if ($res->num_rows() > 0) {
                    $neworigin[$key]['geo'] = $res->row()->geo;
                } else {
                    $neworigin[$key]['geo'] = '';
                }
            }
        }
        return (object) $neworigin;
    }

    public function remove_favorite_location($condition = array(), $field = '') {
        $this->mongo_db->where($condition);
        $this->mongo_db->unset_field($field);
        $this->mongo_db->update_all(FAVOURITE);
    }

    public function get_current_location() {
        
    }

    /**
     *
     * This function return the ride list
     * @param String $type (all/upcoming/completed)
     * @param String $user_id
     * @param Array $fieldArr
     *
     * */
    public function get_ride_list($user_id = '', $type = '', $fieldArr = array(), $limit, $offset) {
        if ($user_id != '' && $type != '') {
            $this->mongo_db->select($fieldArr);

            switch ($type) {
                case 'all':
                    $where_clause = array("user.id" => $user_id);
                    break;
                case 'onride':
                    $where_clause = array(
                        '$or' => array(
                            array("ride_status" => 'Arrived'),
                            array("ride_status" => 'Onride'),
                            array("ride_status" => 'Finished'),
                        ),
                        "user.id" => $user_id
                    );
                    break;
                case 'upcoming':
                    $where_clause = array(
                        '$or' => array(
                            array("ride_status" => 'Booked'),
                            array("ride_status" => 'Confirmed'),
                        ),
                        "user.id" => $user_id
                    );
                    break;
                case 'cancelled':
                    $where_clause = array(
                        '$or' => array(
                            array("ride_status" => 'Cancelled'),
                        ),
                        "user.id" => $user_id
                    );
                    break;
                case 'completed':
                    $where_clause = array(
                        '$or' => array(
                            array("ride_status" => 'Completed')
                        ),
                        "user.id" => $user_id
                    );
                    break;
                default:
                    $where_clause = array("user.id" => $user_id);
                    break;
            }
            $this->mongo_db->where($where_clause, TRUE);
            $this->mongo_db->order_by(array('_id' => 'DESC'));
            $res = $this->mongo_db->get(RIDES, $limit, $offset);
            return $res;
        }
    }
	
	public function check_vehicle_number($vehicle_number="", $driver_id=""){
		$exist = 0;
		if($vehicle_number!=""){
			$this->mongo_db->select(array('_id')); 
			$this->mongo_db->where(array("vehicle_number"=>$vehicle_number));
			if($driver_id!=""){
				$this->mongo_db->where_ne('_id',MongoID($driver_id));
			}
			$res = $this->mongo_db->get(DRIVERS);		
			if($res->num_rows()>0){
				$exist = 1;
			}
		}
		return $exist;
	}
	
	public function user_transaction($user_id, $trans_type) {
   
      if($trans_type!='')
      {
        $option = array(
                array('$match' => array('user_id'=>MongoID($user_id))),
                array('$unwind'=>'$transactions'),
                array('$match' => array('transactions.type'=>$trans_type)),
                array('$group'=>array('_id'=>'$_id','transactions'=>array('$push'=>'$transactions'))));
              
       }
       else
       {
          $option = array(
      
                array('$match' => array('user_id'=>MongoID($user_id))),
                );
       }
        $res = $this->mongo_db->aggregate(WALLET, $option);
        
        return $res;
    }
	
	/**
	*
	* Retures the number of users who are all made a successful ride
	*
	**/
	public function get_unrided_user($user_ids=array()) {
		$uCount = 0;
		
		$option = array(
			array(
				'$project' => array(
					'ride_id' => 1,
					'ride_status' => 1,
					'pay_status' => 1,
					'user' => 1
				)
			),
			array(
				'$match' => array(
					'ride_status' => 'Completed',
					'pay_status' => 'Paid',
					'user.id' => array('$in'=>$user_ids)
				)
			),
			array(
				'$group' => array(
					'_id' =>'$user.id',
					'totalTrips'=>array('$sum'=>1),
				)
			)
		);
        $res = $this->mongo_db->aggregate(RIDES, $option);
		
		if(is_array($res)){
			if(!empty($res['result'])){
				$uCount = count($res['result']);
			}
		}
		
		return $uCount;
    }


}
