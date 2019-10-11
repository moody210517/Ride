<?php  if ( ! defined('BASEPATH')) exit('No direct script access allowed');


	/* Saving ride details for future stats */
	
	if (!function_exists('save_driver_stats')){
		function save_driver_stats($driver_id,$ride_status,$prev_status='') {
			$ci =& get_instance();
			$checkStats = $ci->app_model->get_selected_fields(DRIVER_STATISTICS, array('driver_id' =>MongoID($driver_id)),array('_id'));
			if($checkStats->num_rows()==0){
				$dataArr = array(
									'driver_id' =>MongoID($driver_id),
									'confirmed'=>floatval(1),
									'arrived'=>floatval(0),
									'onride'=>floatval(0),
									'finished'=>floatval(0),
									'completed'=>floatval(0),
									'cancelled'=>floatval(0),
                                    'modified'=>MongoDATE(time())
									
								);
				$ci->app_model->simple_insert(DRIVER_STATISTICS,$dataArr);
			} else {
                $condition=array('driver_id'=>MongoID($driver_id));
                switch ($ride_status) {
                    case 'Confirmed':
                        $ci->mongo_db->where($condition)->inc('confirmed', 1)->update(DRIVER_STATISTICS);
                        break;
                    case 'Arrived':
                        
                        $ci->mongo_db->where($condition)->inc('confirmed', -1)->update(DRIVER_STATISTICS);
                        
                        $ci->mongo_db->where($condition)->inc('arrived', 1)->update(DRIVER_STATISTICS);
                        break;
                    case 'Onride':
                        $ci->mongo_db->where($condition)->inc('arrived', -1)->update(DRIVER_STATISTICS);
                        $ci->mongo_db->where($condition)->inc('onride', 1)->update(DRIVER_STATISTICS);
                        break;
                   case 'Finished':
                        $ci->mongo_db->where($condition)->inc('onride', -1)->update(DRIVER_STATISTICS);
                        $ci->mongo_db->where($condition)->inc('finished', 1)->update(DRIVER_STATISTICS);
                        break;
                   case 'Completed':
                        $ci->mongo_db->where($condition)->inc('finished', -1)->update(DRIVER_STATISTICS);
                        $ci->mongo_db->where($condition)->inc('completed', 1)->update(DRIVER_STATISTICS);
                        break;
                   case 'Cancelled':
                        if($prev_status=='Confirmed') {
                            $ci->mongo_db->where($condition)->inc('confirmed', -1)->update(DRIVER_STATISTICS);
                            $ci->mongo_db->where($condition)->inc('cancelled', 1)->update(DRIVER_STATISTICS);
                        } else if($prev_status=='Arrived') {
                            $ci->mongo_db->where($condition)->inc('confirmed', -1)->update(DRIVER_STATISTICS);
                            $ci->mongo_db->where($condition)->inc('arrived', -1)->update(DRIVER_STATISTICS);
                            $ci->mongo_db->where($condition)->inc('cancelled', 1)->update(DRIVER_STATISTICS);
                        }
                        break;
                }
            }
		}
	}
	
	
	
		
		


/* End of file ride_helper.php */
/* Location: ./application/helpers/ride_helper.php */