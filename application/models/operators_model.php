<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');
 /**
* 
* This model contains all db functions related to operators management
* @author Casperon
*
**/

class Operators_model extends My_Model{

		public function __construct(){
				parent::__construct();
		}
		
		/**
		* 
		* This functions selects rides list 
		**/
		public function get_rides_total($operators_id = ''){
				$this->mongo_db->select('*');
				if($operators_id != ''){
					$where_clause['booked_by'] = $operators_id;
				}
				$this->mongo_db->where($where_clause, TRUE);
				$res = $this->mongo_db->get(RIDES);
				return $res;
		}
		
		/**
		* 
		* This functions selects rides list 
		**/
		public function get_operators_rides_total($operators_id = '', $ride_actions = ''){
       
				 $this->mongo_db->select('*');
        if ($ride_actions == 'Booked' || $ride_actions == '') {
            $where_clause = array('ride_status' => 'Booked');
        } else if ($ride_actions == 'OnRide') {
            $where_clause = array(
                '$or' => array(
                    array("ride_status" => 'Onride'),
                    array("ride_status" => 'Confirmed'),
                    array("ride_status" => 'Arrived'),
                )
            );
        } else if ($ride_actions == 'Completed') {
            $where_clause = array(
                '$or' => array(
                    array("ride_status" => 'Completed'),
                    array("ride_status" => 'Finished'),
                )
            );
        } else if ($ride_actions == 'Cancelled') {
            $where_clause = array('ride_status' => 'Cancelled');
        } else if ($ride_actions == 'riderCancelled') {
            $where_clause = array('ride_status' => 'Cancelled', 'cancelled.primary.by' => 'User');
        } else if ($ride_actions == 'driverCancelled') {
            $where_clause = array('ride_status' => 'Cancelled', 'cancelled.primary.by' => 'Driver');
		} else if ($ride_actions == 'Expired') {
            $where_clause = array('ride_status' => 'Expired');
        } else if ($ride_actions == 'total') {
            $where_clause = array();
        }
        
		if($operators_id != ''){
        
						$where_clause['operator_id'] = MongoID($operators_id);
				}
		
		if(isset($to) && !empty($to) && isset($from) && !empty($from)){
			$from_date = $from.' 00:00:00';
			$to_date = $to.' 23:59:59';
           
			$where_clause['booking_information.est_pickup_date'] = array('$lte' => MongoDATE(strtotime($to_date)),'$gte' => MongoDATE(strtotime($from_date)));
		}else if(isset($from) && !empty($from)){
			$from_date = $from.' 00:00:00';
			$where_clause['booking_information.est_pickup_date'] = array('$gte' => MongoDATE(strtotime($from_date)));
		}
		/* Filter Rides*/
		#echo "<pre>";print_r($where_clause);die;
        $this->mongo_db->where($where_clause, TRUE);
        $res = $this->mongo_db->get(RIDES);
        return $res;
		}
		
		/**
		* 
		* This functions selects rides list 
		**/
		public function get_rides_list($operators_id='', $limit=FALSE,$offset=FALSE){
				$this->mongo_db->select('*');		
				if($operators_id != ''){
						$where_clause['booked_by'] = $operators_id;
				}		
				$this->mongo_db->where($where_clause, TRUE);
				$this->mongo_db->order_by(array('ride_id' => -1));
				if ($limit !== FALSE && is_numeric($limit) && $offset !== FALSE && is_numeric($offset)) {
						$this->mongo_db->limit($limit);
                        $this->mongo_db->offset($offset);
				}
				$res = $this->mongo_db->get(RIDES);  
				
				
				return $res;
		}
		
		/**
		* 
		* This functions selects rides list 
		**/
		public function get_operators_rides($operators_id='', $limit=FALSE,$offset=FALSE, $ride_actions = ''){
				$this->mongo_db->select('*');		
			$where_clause = array();		
				  if ($ride_actions == 'Booked') {
            $where_clause = array('ride_status' => 'Booked');
        } else if ($ride_actions == 'OnRide') {
            $where_clause = array(
                '$or' => array(
                    array("ride_status" => 'Onride'),
                    array("ride_status" => 'Confirmed'),
                    array("ride_status" => 'Arrived'),
                )
            );
        } else if ($ride_actions == 'Completed') {
            $where_clause = array(
                '$or' => array(
                    array("ride_status" => 'Completed'),
                    array("ride_status" => 'Finished'),
                )
            );
        } else if ($ride_actions == 'Cancelled') {
            $where_clause = array('ride_status' => 'Cancelled');
        } else if ($ride_actions == 'riderCancelled') {
            $where_clause = array('ride_status' => 'Cancelled', 'cancelled.primary.by' => 'User');
        } else if ($ride_actions == 'driverCancelled') {
            $where_clause = array('ride_status' => 'Cancelled', 'cancelled.primary.by' => 'Driver');
		} else if ($ride_actions == 'Expired') {
            $where_clause = array('ride_status' => 'Expired');
        } else if ($ride_actions == 'total') {
            $where_clause = array();
        }
        
				if($operators_id != ''){
						$where_clause['operator_id'] = MongoID($operators_id);
				}
				$this->mongo_db->where($where_clause, TRUE);
				$this->mongo_db->order_by(array('ride_id' => -1));
			  if ($limit !== FALSE && is_numeric($limit) && $offset !== FALSE && is_numeric($offset)) {
                    $this->mongo_db->limit($limit);
                    $this->mongo_db->offset($offset);
				}
               # echo "<pre>";print_r($where_clause);die;
			    $res = $this->mongo_db->get(RIDES);  
				#echo "<pre>";print_r($res->result());die;
				return $res;
		}
					/**
     *
     * This function return the later jobs for operator panel bid jobs
     * 	Array $coordinates
     * 	Number $limit
     *
     * */
		public function get_operator_scheduled_jobs($operator_info = array())
		{
 
		$operator_id = $operator_info;
		#echo'<pre>fff';print_R($operator_id);die;
		$rStatusArr = array(
							array('ride_status' => 'Booked')
					    );
		$bStatusArr = array(
							array('bid_status' => 'Accepted',
									'bid_transfer_type' => array('$exists' => false)
							),
							array('bid_status' => 'Accepted','bid_transfer_type' => 'Network','bid_operator_id' => MongoID($operator_id)),
							array('bid_status' => 'Recalled','bid_operator_id' => MongoID($operator_id)),
							array('booked_by' =>MongoID($operator_id),'bid_status' => array('$exists' => false))
						);
		$oprArr = array(
							array( 'bid_operator_id' => $operator_id),
							//array( 'tr_operator_id' => $operator_id),
							array('booked_by' =>MongoID($operator_id),'bid_status' => array('$exists' => false))
						);
        $matchArr = array(
						  '$and' => array(array('$or' => $rStatusArr),array('$or' => $bStatusArr),array('$or' => $oprArr))
					);
	#echo '<pre>'; print_r($matchArr); die;
        $fields = array('ride_status','type','declined_operators','booking_information','ride_id','bid_status','user','tr_operator_id','booking_ref','inbound_tranfer','driver');
        $this->mongo_db->select($fields);		
		$this->mongo_db->where($matchArr, TRUE);
		$this->mongo_db->order_by(array('booking_information.est_pickup_date' => "ASC"));
		$res = $this->mongo_db->get(RIDES);  
		#echo'<pre>';print_R($res->result());die;
		return $res;
    }
	
}

?>