<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');
 /**
* 
* This model contains all db functions related to map management
* @author Casperon
*
**/

class Map_model extends My_Model{

	public function __construct(){
		parent::__construct();
	}
	
	/**
	* Check driver exist or not
	**/
	public function get_nearest_driver($coordinates = array(), $operator_loc = '',$radius){
		
		if($operator_loc != ''){
				$matchCond = array(
												//'availability' =>array('$eq'=>'Yes'),
												'status' =>array('$eq'=>'Active'),
												'driver_location' => array('$eq' => $operator_loc)
										 );
		} else {
				$matchCond = array(
												//'availability' =>array('$eq'=>'Yes'),
												'status' =>array('$eq'=>'Active')
										 );
		}
        
        if(!empty($coordinates) && $coordinates[0]!='') {
                $limit=100000;
				if($radius!=''){
				$max_distance=$radius;
				}
				else{
				$max_distance=50000;
				}
                $option = array(
								array(
									'$geoNear'=>array("near"=>array("type"=>"Point",
														"coordinates"=>$coordinates
														),
									"spherical"=> true,
									"maxDistance"=>intval($max_distance),
									"includeLocs"=>'loc',
									"distanceField"=>"distance",
									"distanceMultiplier"=>0.001,
									'num' => intval($limit)
									),
								),
								array(
									'$project' => array(
										'category' =>1,
										'driver_name' =>1,
										'loc' =>1,
										'availability' =>1,
										'status' =>1,
										'distance' =>1,
                                        'mode'=>1,
                                        'last_active_time' => 1,
                                        'operator_id' => 1,
                                        'driver_location' => 1,
                                        'dail_code' => 1,
                                        'mobile_number' =>1,
										 'email' =>1
									)
								),
								array(
									'$match' => $matchCond
								)
				);
        }else {
            $option = array(
								
								array(
									'$project' => array(
										'category' =>1,
										'driver_name' =>1,
										'loc' =>1,
										'availability' =>1,
										'status' =>1,
										'distance' =>1,
                                        'mode'=>1,
                                        'last_active_time' => 1,
                                        'operator_id' => 1,
                                        'driver_location' => 1,
                                        'dail_code' => 1,
                                        'mobile_number' =>1,
										'email' =>1
									)
								),
								array(
									'$match' => $matchCond
								)
				);
        }
		//print_r($option);
		
		

		try{
			$res = $this->mongo_db->aggregate(DRIVERS,$option);
		}catch( Exception  $e){
			//echo $e;
		}
		
		

		return $res;
	}
	public function get_nearest_driver_company($coordinates = array(),$company_id=''){
		/* $option=array('$geoNear'=>array("near"=>array("type"=>"Point",
														"coordinates"=>$coordinates
														),
								"spherical"=> true,
								"maxDistance"=>5000000,
								"includeLocs"=>'loc',
								"distanceField"=>"distance",
								"distanceMultiplier"=>0.001,
								'num' => 10
								)); */
		
		$matchCond = array(
								'company_id' =>array('$eq'=>$company_id),
								'status' =>array('$eq'=>'Active')
							);
		
	
		$option = array(
								array(
									'$geoNear'=>array("near"=>array("type"=>"Point",
														"coordinates"=>$coordinates
														),
									"spherical"=> true,
									//"maxDistance"=>50000,
									"includeLocs"=>'loc',
									"distanceField"=>"distance",
									"distanceMultiplier"=>0.001,
									"num"=>100000
									
									),
								),
								array(
									'$project' => array(
										'category' =>1,
										'driver_name' =>1,
										'loc' =>1,
										'availability' =>1,
										'status' =>1,
										'distance' =>1,
                                        'mode'=>1,
                                        'last_active_time' => 1,
										'operator_id' => 1,
										'driver_location' => 1,
										'company_id' => 1
									)
								),
								array(
									'$match' => $matchCond
								)
							);
							
		$res = $this->mongo_db->aggregate(DRIVERS,$option);
		return $res;
	}
    public function get_nearest_user($coordinates = array()){
		/* $option=array('$geoNear'=>array("near"=>array("type"=>"Point",
														"coordinates"=>$coordinates
														),
								"spherical"=> true,
								"maxDistance"=>5000000,
								"includeLocs"=>'loc',
								"distanceField"=>"distance",
								"distanceMultiplier"=>0.001,
								'num' => 10
								)); */
		
		$option = array(	
								array(
									'$geoNear'=>array("near"=>array("type"=>"Point",
														"coordinates"=>$coordinates
														),
									"spherical"=> true,
									//"maxDistance"=>50000,
									"includeLocs"=>'loc',
									"distanceField"=>"distance",
									"distanceMultiplier"=>0.001,
									"num"=>100000
									
									),
								),
								array(
									'$project' => array(
										'user_name' =>1,
										'loc' =>1,
									    'status' =>1,
									    'email' =>1,
									    'phone_number' =>1,
									    'distance' =>1
										
									)
								),
								array(
									'$match' => array(
										//'availability' =>array('$eq'=>'Yes'),
										'status' =>array('$eq'=>'Active')
									)
								)
							);
							
		$res = $this->mongo_db->aggregate(USERS,$option);
		return $res;
	}
	
}

?>