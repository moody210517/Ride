<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');
/*
 *
 * This model contains all db functions related to user management
 * @author Casperon
 *
 */
class Dynamic_driver extends My_Model{
	public function __construct(){
        parent::__construct();
    }

	/**
	 * Given a $centre (latitude, longitude) co-ordinates and a
	 * distance $radius (miles), returns a random point (latitude,longtitude)
	 * which is within $radius miles of $centre.
	 *
	 * @param  array $centre Numeric array of floats. First element is 
	 *                       latitude, second is longitude.
	 * @param  float $radius The radius (in miles).
	 * @return array         Numeric array of floats (lat/lng). First 
	 *                       element is latitude, second is longitude.
	 */
	 function generate_random_point( $centre, $category,$radius=1 ){
		/* $geo_data = array('loc'=>array('lon'=>floatval (0),'lat'=>floatval (0)));
		$this->update_details(DRIVERS,$geo_data,array());
		return; */
		$totalDrivers = $this->get_selected_fields(DRIVERS,array("created_by"=>'Auto'),array('_id'))->count();
		$offset = rand(0,$totalDrivers);
		$condition=array("created_by"=>'Auto','availability'=>'Yes','mode'=>'Available','category'=>MongoID($category));
		$selectDrivers = $this->get_all_details(DRIVERS,$condition,array(),5,($offset-1));
		$selectDrivers->num_rows();
		if($selectDrivers->num_rows()<=5){
			$condition=array("created_by"=>'Auto','availability'=>'Yes','mode'=>'Available');
			$selectDrivers = $this->get_all_details(DRIVERS,$condition,array(),5,1);
			foreach($selectDrivers->result() as $driver){
				$driver_id = (string)$driver->_id;
				$this->update_details(DRIVERS,array('category'=>MongoID($category)),array('_id'=>MongoID($driver_id)));				
			}
			$condition=array("created_by"=>'Auto','availability'=>'Yes','mode'=>'Available','category'=>MongoID($category));
			$selectDrivers = $this->get_all_details(DRIVERS,$condition,array(),5,1);
		}
		#echo $selectDrivers->num_rows();
		if($selectDrivers->num_rows()>0){
			foreach($selectDrivers->result() as $driver){
					$driver_id = (string)$driver->_id;
					$radius_earth = 3959; //miles

					//Pick random distance within $distance;
					$distance = lcg_value()*$radius;

					//Convert degrees to radians.
					$centre_rads = array_map( 'deg2rad', $centre );

					//First suppose our point is the north pole.
					//Find a random point $distance miles away
					$lat_rads = (pi()/2) -  $distance/$radius_earth;
					$lng_rads = lcg_value()*2*pi();


					//($lat_rads,$lng_rads) is a point on the circle which is
					//$distance miles from the north pole. Convert to Cartesian
					$x1 = cos( $lat_rads ) * sin( $lng_rads );
					$y1 = cos( $lat_rads ) * cos( $lng_rads );
					$z1 = sin( $lat_rads );


					//Rotate that sphere so that the north pole is now at $centre.

					//Rotate in x axis by $rot = (pi()/2) - $centre_rads[0];
					$rot = (pi()/2) - $centre_rads[0];
					$x2 = $x1;
					$y2 = $y1 * cos( $rot ) + $z1 * sin( $rot );
					$z2 = -$y1 * sin( $rot ) + $z1 * cos( $rot );

					//Rotate in z axis by $rot = $centre_rads[1]
					$rot = $centre_rads[1];
					$x3 = $x2 * cos( $rot ) + $y2 * sin( $rot );
					$y3 = -$x2 * sin( $rot ) + $y2 * cos( $rot );
					$z3 = $z2;


					//Finally convert this point to polar co-ords
					$lng_rads = atan2( $x3, $y3 );
					$lat_rads = asin( $z3 );
					$latlong = array_map( 'rad2deg', array( $lat_rads, $lng_rads ) );
					$geo_data = array('loc'=>array('lon'=>floatval ($latlong[1]),'lat'=>floatval ($latlong[0])));
					$this->update_details(DRIVERS,$geo_data,array('_id'=>MongoID($driver_id)));
							
					#echo '<pre>'; print_r($geo_data);
			 }
		 }
	 }
}