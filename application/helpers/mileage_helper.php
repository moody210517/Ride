<?php  if ( ! defined('BASEPATH')) exit('No direct script access allowed');


	/**
	*
	*	This function updates the mileage info
	*	@Param String $driver_id
	*	@Param String $start_time
	*	@Param String $type
	*	@Param String $distance
	*	@Param String $distance_unit
	*	@Param String $ride_id
	*
	**/
	
	if (!function_exists('update_mileage_system')){
		function update_mileage_system($driver_id='',$start_time='',$type='',$distance='',$distance_unit='',$ride_id='',$drop_time=''){
			$ci =& get_instance();
			$checkMileage = $ci->app_model->get_selected_fields(DRIVERS_MILEAGE, array('driver_id' => MongoID($driver_id)),array('_id'));
			$checkDriver = $ci->app_model->get_selected_fields(DRIVERS, array('_id' => MongoID($driver_id)),array('_id','total_mileage','total_duration','roaming_mileage','roaming_duration','pickup_mileage','pickup_duration','drop_mileage','drop_duration'));
			$end_time=time();
			if($drop_time!='' && $drop_time > 0) {
				$end_time=$drop_time;
			}
			if($distance_unit == 'mi'){
				$distance = round(($distance / 1.609344),2);
			}
		
			$total_mileage=$distance;
			if(isset($checkDriver->row()->total_mileage)) {
				
				$total_mileage=$distance+$checkDriver->row()->total_mileage;
			}
			$total_duration_min=floor(($end_time-$start_time)/60);
			
			$total_duration=floor(($end_time-$start_time)/60);
			if(isset($checkDriver->row()->total_duration)) {
				$total_duration=$total_duration_min+$checkDriver->row()->total_duration;
			}
			$update_totalArr=array('total_mileage'=>floatval($total_mileage),'total_duration'=>floatval($total_duration));
			$ci->app_model->update_details(DRIVERS, $update_totalArr,array('_id' => MongoID($driver_id)));
			/*** update total mileage ***/
			switch($type) {
				case 'free-roaming':
					$roaming_mileage=$distance;
					if(isset($checkDriver->row()->roaming_mileage)) {
						$roaming_mileage=$distance+$checkDriver->row()->roaming_mileage;
					}
					$roaming_duration=floor(($end_time-$start_time)/60);
					if(isset($checkDriver->row()->roaming_duration)) {
						$roaming_duration=$roaming_duration+$checkDriver->row()->roaming_duration;
					}
					$update_roaming_Arr=array('roaming_mileage'=>floatval($roaming_mileage),'roaming_duration'=>floatval($roaming_duration));
					$ci->app_model->update_details(DRIVERS,$update_roaming_Arr,array('_id' => MongoID($driver_id)));
				break;
				case 'customer-pickup':
					$pickup_mileage=$distance;
					if(isset($checkDriver->row()->pickup_mileage)) {
						$pickup_mileage=$distance+$checkDriver->row()->pickup_mileage;
					}
					$pickup_duration=floor(($end_time-$start_time)/60);
					if(isset($checkDriver->row()->pickup_duration)) {
						$pickup_duration=$pickup_duration+$checkDriver->row()->pickup_duration;
					}
					$update_pickup_Arr=array('pickup_mileage'=>floatval($pickup_mileage),'pickup_duration'=>floatval($pickup_duration));
					$ci->app_model->update_details(DRIVERS,$update_pickup_Arr,array('_id' => MongoID($driver_id)));
				break;
				case 'customer-drop':
					$drop_mileage=$distance;
					if(isset($checkDriver->row()->drop_mileage)) {
						$drop_mileage=$distance+$checkDriver->row()->drop_mileage;
					}
					$drop_duration=floor(($end_time-$start_time)/60);
					if(isset($checkDriver->row()->drop_duration)) {
						$drop_duration=$drop_duration+$checkDriver->row()->drop_duration;
					}
					$update_drop_Arr=array('drop_mileage'=>floatval($drop_mileage),'drop_duration'=>floatval($drop_duration));
					$ci->app_model->update_details(DRIVERS,$update_drop_Arr,array('_id' => MongoID($driver_id)));
				break;
			}
			if($checkMileage->num_rows() == 0) {
				$dataArr = array('driver_id' => MongoID($driver_id),
							  'created'=>MongoDATE(time()),
							  'mileage_data' => array(array(
							  'start_time' =>MongoDATE($start_time),
							  'end_time' =>MongoDATE($end_time),
							  'duration_min' =>floatval($total_duration_min),
							  'distance' =>$distance,
							  'type' =>$type,
							  'ride_id'=>$ride_id
							 )
							 ));
				$ci->app_model->simple_insert(DRIVERS_MILEAGE, $dataArr);
			} else {
				$dataArr=array('mileage_data' =>array(
							  'start_time' =>MongoDATE($start_time),
							  'end_time' =>MongoDATE($end_time),
							  'duration_min' =>floatval($total_duration_min),
							  'distance' =>$distance,
							  'type' =>$type,
							  'ride_id'=>$ride_id
							  )
							 );
				$ci->app_model->simple_push(DRIVERS_MILEAGE,array('driver_id' => MongoID($driver_id)),$dataArr);
			}
		}
	}
	
	/**
	*
	*	This function return the mileage report of drivers with start and end date
	*	@Param Array $driver_array
	*	@Param String $start_date
	*	@Param String $end_date
	*
	**/
	
	if (!function_exists('get_mileage_list')){
		function get_mileage_list($driver_array=array(),$start_date='',$end_date='',$company_id=''){
			$ci =& get_instance();
			$matchArr=array();
			$unwindArr=array();
			$groupArr=array();
			if(!empty($driver_array) && $start_date!='' && $end_date!='') {
			   $matchArr=array('$match' => array('driver_id' =>array('$in'=>$driver_array),
												'mileage_data.start_time'=>array('$gte'=> MongoDATE($start_date)),
												'mileage_data.end_time'=>array('$lte'=>MongoDATE($end_date))
											   )
							);	
				$unwindArr=array('$unwind' =>'$mileage_data');
				$groupArr=array(
									  '$group' => array(
											'_id' =>array('type'=>'$mileage_data.type',
														'driver_id'=>'$driver_id',
													),
										'total_distance'=>array('$sum'=>'$mileage_data.distance'),
										'total_duration'=>array('$sum'=>'$mileage_data.duration_min')
										)
									);
				$option = array(array('$project' => array(
											'driver_id' =>1,
											'mileage_data'=>1,
											'created' =>1
										)
									),
									$unwindArr,
									$matchArr,
									$groupArr
								);
				$res = $ci->mongo_db->aggregate(DRIVERS_MILEAGE,$option);
			} else if(!empty($driver_array)) {
				$matchArr=array('$match' => array('driver_id' =>array('$in'=>$driver_array)));	
				$unwindArr=array('$unwind' =>'$mileage_data');
				$groupArr=array(
									  '$group' => array(
											'_id' =>array('type'=>'$mileage_data.type',
														'driver_id'=>'$driver_id',
													),
										'total_distance'=>array('$sum'=>'$mileage_data.distance'),
										'total_duration'=>array('$sum'=>'$mileage_data.duration_min')
										)
									);
				$option = array(array('$project' => array(
											'driver_id' =>1,
											'mileage_data'=>1,
											'created' =>1
										)
									),
									$unwindArr,
									$matchArr,
									$groupArr
								);

				$res = $ci->mongo_db->aggregate(DRIVERS_MILEAGE,$option);
			}
			
			#echo "<pre>"; print_r($res); 

			$mileage_Record=array();
			if(!empty($res['result'])) {
				$aDr = array();
				foreach($res['result'] as $row) {
					$driver_id = (string)$row['_id']['driver_id'];
					if(!in_array($driver_id,$aDr)){
						$mileage_Record[$driver_id]['drop_distance']=0.0;
						$mileage_Record[$driver_id]['drop_duration']=0.0;
						$mileage_Record[$driver_id]['pickup_distance']=0.0;
						$mileage_Record[$driver_id]['pickup_duration']=0.0;
						$mileage_Record[$driver_id]['free_distance']=0.0;
						$mileage_Record[$driver_id]['free_duration']=0.0;
						$aDr[] = $driver_id;
					}
					
					if($row['_id']['type']=='customer-drop') {
						if(isset($row['total_distance']) && $row['total_distance']>0)  $mileage_Record[$driver_id]['drop_distance']= $mileage_Record[$driver_id]['drop_distance']+$row['total_distance'];
						
						if(isset($row['total_duration']) && $row['total_duration']>0)  $mileage_Record[$driver_id]['drop_duration']=$mileage_Record[$driver_id]['drop_duration']+$row['total_duration'];
					} else if($row['_id']['type']=='customer-pickup') {
						if(isset($row['total_distance']) && $row['total_distance']>0)  $mileage_Record[$driver_id]['pickup_distance']=$mileage_Record[$driver_id]['pickup_distance']+$row['total_distance'];
						
						if(isset($row['total_duration']) && $row['total_duration']>0)  $mileage_Record[$driver_id]['pickup_duration']=$mileage_Record[$driver_id]['pickup_duration']+$row['total_duration'];
					} else  {
						if(isset($row['total_distance']) && $row['total_distance']>0)  $mileage_Record[$driver_id]['free_distance']=$mileage_Record[$driver_id]['free_distance']+$row['total_distance'];
						
						if(isset($row['total_duration']) && $row['total_duration']>0)  $mileage_Record[$driver_id]['free_duration']=$mileage_Record[$driver_id]['free_duration']+$row['total_duration'];
					}
				}
			}
			return $mileage_Record;
		}
	}
	
	
	/**
	*
	*	This function return the mileage report of a particular driver/ride with start and end date
	*	@Param Array $driver_array
	*	@Param String $start_date
	*	@Param String $end_date
	*
	**/	
	if (!function_exists('view_mileage_list')){
		function view_mileage_list($driver_id,$start_date,$end_date,$ride_id){
			$ci =& get_instance();
			$matchArr=array();
			if($driver_id!='' && $start_date!='' && $end_date!='' && $ride_id!='') {
			   $matchArr=array('$match' => array('driver_id' =>array('$eq'=>MongoID($driver_id)),
												'mileage_data.ride_id'=>array('$eq'=>$ride_id),
												'mileage_data.start_time'=>array('$gte'=> MongoDATE($start_date)),
												'mileage_data.end_time'=>array('$lte'=>MongoDATE($end_date))
											   )
							);
			} else if($driver_id!='' &&  $start_date!='' && $end_date!='') {
				$matchArr=array('$match' => array('driver_id' =>array('$eq'=>MongoID($driver_id)),
												'mileage_data.start_time'=>array('$gte'=> MongoDATE($start_date)),
												'mileage_data.end_time'=>array('$lte'=>MongoDATE($end_date))
											   )
							);
			} else if($driver_id!='' && $ride_id!='') {
				$matchArr=array('$match' => array('driver_id' =>array('$eq'=>MongoID($driver_id)),
												'mileage_data.ride_id'=>array('$eq'=>$ride_id)
											   )
							);
			} else if($driver_id!='') {
				$matchArr=array('$match' => array('driver_id' =>array('$eq'=>MongoID($driver_id))));
			}

			$unwindArr=array('$unwind' =>'$mileage_data');
			$option = array(array('$project' => array(
											'driver_id' =>1,
											'mileage_data'=>1,
											'created' =>1
										)
									),
									$unwindArr,
									$matchArr,
									array(
										'$sort' => array(
											'mileage_data.start_time' =>1
										)
									)
								);
			$res = $ci->mongo_db->aggregate(DRIVERS_MILEAGE,$option);
			return $res;
		}
	}
	
	
	/**
	*
	*	This function return the mileage report of drivers with start and end date
	*	@Param String $start_date
	*	@Param String $end_date
	*
	**/
	
	if (!function_exists('get_total_mileage_list')){
		function get_total_mileage_list($driver_array=array(),$start_date='',$end_date='',$company_id=''){
			$ci =& get_instance();
			$res = array();
			
			$matchArr=array();
			$unwindArr=array();
			$groupArr=array();
			if(!empty($driver_array) && $start_date!='' && $end_date!='') {
			   $matchArr=array('$match' => array('driver_id' =>array('$in'=>$driver_array),
												'mileage_data.start_time'=>array('$gte'=> MongoDATE($start_date)),
												'mileage_data.end_time'=>array('$lte'=>MongoDATE($end_date))
															
											   )
							
							);	
				$unwindArr=array('$unwind' =>'$mileage_data');
				$groupArr=array(
									  '$group' => array(
											'_id' =>array('type'=>'$mileage_data.type',
														'driver_id'=>'$driver_id',
													),
										'total_distance'=>array('$sum'=>'$mileage_data.distance'),
										'total_duration'=>array('$sum'=>'$mileage_data.duration_min'),
											
											
										)
									);
				$option = array(array('$project' => array(
											'driver_id' =>1,
											'mileage_data'=>1,
											'created' =>1
										)
									),
									$unwindArr,
									$matchArr,
									$groupArr
								);
				$res = $ci->mongo_db->aggregate(DRIVERS_MILEAGE,$option);
			} else if(!empty($driver_array)) {
				$matchArr=array('$match' => array('driver_id' =>array('$in'=>$driver_array)));	
				$unwindArr=array('$unwind' =>'$mileage_data');
				$groupArr=array(
									  '$group' => array(
											'_id' =>array('type'=>'$mileage_data.type',
														'driver_id'=>'$driver_id',
													),
										'total_distance'=>array('$sum'=>'$mileage_data.distance'),
										'total_duration'=>array('$sum'=>'$mileage_data.duration_min')
										)
									);
				$option = array(array('$project' => array(
											'driver_id' =>1,
											'mileage_data'=>1,
											'created' =>1
										)
									),
									$unwindArr,
									$matchArr,
									$groupArr
								);

				$res = $ci->mongo_db->aggregate(DRIVERS_MILEAGE,$option);
			}else if($company_id!='' && empty($driver_array)){
				$res = array();
			} else {
					$unwindArr=array('$unwind' =>'$mileage_data');
					$groupArr=array(
										  '$group' => array(
											'_id' =>array('type'=>'$mileage_data.type'),
											'total_distance'=>array('$sum'=>'$mileage_data.distance'),
											'total_duration'=>array('$sum'=>'$mileage_data.duration_min')
											)
										);
					$option = array(array('$project' => array(
												'mileage_data'=>1,
												'created' =>1
											)
										),
										$unwindArr,
										$groupArr
									);
					$res = $ci->mongo_db->aggregate(DRIVERS_MILEAGE,$option);
			
			}
			
			$mileage_Record=array();
			$mileage_Record['drop_distance']=0.0;
			$mileage_Record['drop_duration']=0.0;
			$mileage_Record['pickup_distance']=0.0;
			$mileage_Record['pickup_duration']=0.0;
			$mileage_Record['free_distance']=0.0;
			$mileage_Record['free_duration']=0.0;
			if(!empty($res)) {
				if(!empty($res['result'])) {
					foreach($res['result'] as $row) {
						if($row['_id']['type']=='customer-drop') {
							if(isset($row['total_distance']) && $row['total_distance']>0)  
								$mileage_Record['drop_distance'] = $mileage_Record['drop_distance']+$row['total_distance'];
							
							if(isset($row['total_duration']) && $row['total_duration']>0)  
								$mileage_Record['drop_duration'] = $mileage_Record['drop_duration']+$row['total_duration'];
						} else if($row['_id']['type']=='customer-pickup') {
							if(isset($row['total_distance']) && $row['total_distance']>0)  
								$mileage_Record['pickup_distance']=$mileage_Record['pickup_distance']+$row['total_distance'];
							
							if(isset($row['total_duration']) && $row['total_duration']>0)  
								$mileage_Record['pickup_duration']=$mileage_Record['pickup_duration']+$row['total_duration'];
						} else if($row['_id']['type']=='free-roaming') {
							if(isset($row['total_distance']) && $row['total_distance']>0)  
								$mileage_Record['free_distance'] = $mileage_Record['free_distance']+$row['total_distance'];
							
							if(isset($row['total_duration']) && $row['total_duration']>0)  
								$mileage_Record['free_duration'] = $mileage_Record['free_duration']+$row['total_duration'];
						}
					}
				}
			}
			return $mileage_Record;
		}
	}
	

	/**
	*
	* convert minutes to hrs
	*
	**/
	if ( ! function_exists('convertToHoursMins')){
		function convertToHoursMins($time,$format = '%02d: %02d: 00') {
			$hours = floor($time / 60);
			$minutes = ($time % 60);
			return sprintf($format, $hours, $minutes);
		}
	}
	

/* End of file mileage_helper.php */
/* Location: ./application/helpers/mileage_helper.php */