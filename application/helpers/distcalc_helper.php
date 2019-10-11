<?php  if ( ! defined('BASEPATH')) exit('No direct script access allowed');


if ( ! function_exists('get_distance_from_latlong')) {
	function get_distance_from_latlong($travel_history="",$ride_id="") {
		$ci =& get_instance();
		$math_ext_distance = 0;
		if($travel_history!=""){
			$travel_history = trim($travel_history,',');
			$travel_historyArr = array();
			$travelRecords = @explode(',',$travel_history);
			$lat = ""; 
			$long = ""; 
			if(count($travelRecords)>1){
				for( $i = 0; $i < count($travelRecords); $i++){
					$splitedHis = @explode(';',$travelRecords[$i]);
					if(isset($splitedHis[0])) $lat = $splitedHis[0];
					if(isset($splitedHis[1])) $long = $splitedHis[1];
					if(is_valid_lat_long($lat,$long)){
						$travel_historyArr[] = array('lat' => $lat,
													 'lon' => $long,
													 'update_time' =>MongoDATE(strtotime($splitedHis[2]))
													);
					}
				}
			}
			if(!empty($travel_historyArr)){
				$getRideHIstoryVal = $ci->app_model->get_all_details(TRAVEL_HISTORY,array('ride_id' => (string)$ride_id));
				if($getRideHIstoryVal->num_rows()>0){
					$ci->app_model->update_details(TRAVEL_HISTORY,array('history_end' => $travel_historyArr),array('ride_id' => $ride_id));
				}else{
					$ci->app_model->simple_insert(TRAVEL_HISTORY,array('ride_id' => $ride_id,'history_end' => $travel_historyArr));
				}
			}
			$dis_val_arr = array();
			$val1 = array();
			$val2 = array();
			$getRideHIstory = $ci->app_model->get_all_details(TRAVEL_HISTORY,array('ride_id' => $ride_id));
			if($getRideHIstory->num_rows()>0){
				foreach ($getRideHIstory->result() as $key => $data) {
					$hisMid = array();
					$hisEnd = array();
					if(isset($data->history)){
						$hisMid = $data->history;
					}
					if(isset($data->history_end)){
						$hisEnd = $data->history_end;
					}
					$hisFinal = $hisEnd;
					if(count($hisEnd) > count($hisMid)){
						$hisFinal = $hisEnd;
					}else{
						$hisFinal = $hisMid;
					}
					foreach($hisFinal as $value) {
						if(count($val1)==0){
							$val1[0] = $value['lat'];
							$val1[1] = $value['lon']; 
							$val2[0] = $value['lat'];
							$val2[1] = $value['lon'];
							continue;
						}else{
							$val1[0] = $val2[0];
							$val1[1] = $val2[1]; 
						}
						$val2[0] = $value['lat'];
						$val2[1] = $value['lon'];
						$dis_val_arr[] = round(cal_distance($val1[0], $val1[1], $val2[0], $val2[1]),3);
					}
				}
			}
			$math_ext_distance = array_sum($dis_val_arr);
			if (!is_numeric($lat)){
				$math_ext_distance = 0.00;
			}
		}
		return $math_ext_distance;
	}
}

if ( ! function_exists('is_valid_lat_long')) {
	function is_valid_lat_long($lat,$long){
		if ($lat!="" && $long!="") {
			if (is_numeric($lat) && is_numeric($long)) {
				if ($lat!=0 && $long!=0) {
					return true;
				}
			}
		}
		return false;
	}
}

if ( ! function_exists('cal_distance')) {
	function cal_distance($latitudeFrom=0.00, $longitudeFrom=0.00, $latitudeTo=0.00, $longitudeTo=0.00, $earthRadius = 3959){
		// convert from degrees to radians
		$latFrom = deg2rad($latitudeFrom);
		$lonFrom = deg2rad($longitudeFrom);
		$latTo = deg2rad($latitudeTo);
		$lonTo = deg2rad($longitudeTo);

		$latDelta = $latTo - $latFrom;
		$lonDelta = $lonTo - $lonFrom;

		$angle = 2 * asin(sqrt(pow(sin($latDelta / 2), 2) + cos($latFrom) * cos($latTo) * pow(sin($lonDelta / 2), 2)));
		return $angle * $earthRadius * 1.609344;
	}
	if ( ! function_exists('find_postal_code')){
        function find_postal_code($lat='',$lon=''){ 
			$ci =& get_instance(); 
			$postal_code = '';
			if($lat != '' && $lon != ''){
				$latlng = $lat . ',' . $lon;
				$gmap = file_get_contents("https://maps.googleapis.com/maps/api/geocode/json?latlng=" . $latlng . "&sensor=false".$ci->data['google_maps_api_key']);
				$mapValues = json_decode($gmap)->results;
				
				if(isset($mapValues[0]->address_components)){
					$addrComponents = $mapValues[0]->address_components;
					foreach($addrComponents as $addr){
						if(isset($addr->types[0]) && $addr->types[0] == 'postal_code'){
							$postal_code = $addr->long_name; 
						}
					}
				}
			}
			return $postal_code;
        }
    }
}
	


/* End of file distcalc_helper.php */
/* Location: ./application/helpers/distcalc_helper.php */