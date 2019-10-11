<?php

/**
 *
 * @since       2013-03-24
 * @author      Dan Mandle http://dan.mandle.me
 * @see 		http://facstaff.unca.edu/mcmcclur/GoogleMaps/EncodePolyline/
 *
 *
 ** DON'T BE A DICK PUBLIC LICENSE
 *
 *** http://www.dbad-license.org
 *** Version 1, December 2009
 *
 ** Copyright (C) 2013 Dan Mandle http://dan.mandle.me
 *
 * Everyone is permitted to copy and distribute verbatim or modified
 * copies of this license document, and changing it is allowed as long
 * as the name is changed.
 *
 ** DON'T BE A DICK PUBLIC LICENSE
 ** TERMS AND CONDITIONS FOR COPYING, DISTRIBUTION AND MODIFICATION
 *
 *  1. Do whatever you like with the original work, just don't be a dick.
 *
 *     Being a dick includes - but is not limited to - the following instances:
 *
 *	   1a. Outright copyright infringement - Don't just copy this and change the name.  
 *	   1b. Selling the unmodified original with no work done what-so-ever, that's REALLY being a dick.  
 *	   1c. Modifying the original work to contain hidden harmful content. That would make you a PROPER dick.  
 *
 *  2. If you become rich through modifications, related works/services, or supporting the original work,
 *     share the love. Only a dick would make loads off this work and not buy the original works 
 *     creator(s) a pint.
 * 
 *  3. Code is provided with no warranty. Using somebody else's code and bitching when it goes wrong makes 
 *     you a DONKEY dick. Fix the problem yourself. A non-dick would submit the fix back.
 *
 */

require_once(dirname(__FILE__).'/class.polylineEncoder.php');

class PolylineUtilities extends PolylineEncoder{
	
	public function stitchPolylines($existingPolyline, $newPolyline, $lastCoords = NULL, $newPolylineFromOrigin = FALSE){
		// existingPolyline: string
		// newPolyline: string
		// optional lastCoords: string OR array(lat,lon)
		// newPolylineFromOrigin: bool if new polyline is from origin or starting from lastCoords

		if(!$newPolylineFromOrigin){
			if(isset($lastCoords)){
				if(!is_array($lastCoords)){
					$lastCoords[0] = explode(',', lastCoords);
				}
				else{
					$lastCoords[0] = $lastCoords;	
				}
			}
			else{
				$lastCoords[0] = array_pop(decodePolyline($existingPolyline));
			}
			$lastCoordsEncoded = $this->encode($lastCoords);
			$stichedPolyline = $existingPolyline.str_replace($lastCoordsEncoded, '', $newPolyline);
		}
		else{
			if(isset($lastCoords)){
				if(!is_array($lastCoords)){
					$lastCoords[0] = explode(',', lastCoords);
				}
				else{
					$lastCoords[0] = $lastCoords;	
				}
			}
			else{
				$lastCoords[0] = array_pop(decodePolyline($existingPolyline));
			}
			$firstCoords[0] = array_shift(decodePolyline($newPolyline));

			$lastCoordsEncoded = $this->encode($lastCoords);
			$firstCoordsEncoded = $this->encode($firstCoords);
			$lastAndFirstEncoded = $this->encode(array($lastCoords, $firstCoords));

			$stitchedPolyline = $existingPolyline
								. str_replace($lastCoordsEncoded, '', $lastAndFirstEncoded)
								. str_replace($firstCoordsEncoded, '', $newPolyline);
		}
		return $stitchedPolyline;
	}

	public function decode($polyline){
		require_once(dirname(__FILE__).'/decodePolylineToArray.php');
		return decodePolylineToArray($polyline);
	}

}