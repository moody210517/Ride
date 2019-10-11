<?php  if ( ! defined('BASEPATH')) exit('No direct script access allowed');
	
if (!function_exists('MongoDATE')){
	function MongoDATE($timestamp=NULL) {
		if (class_exists('MongoDB\BSON\UTCDateTime')){
			if(isset($timestamp) && $timestamp!=NULL){
				$timestamp = $timestamp*1000;
				return new MongoDB\BSON\UTCDateTime($timestamp);
			}
			return new MongoDB\BSON\UTCDateTime();
		}
		return NULL;
	}
}
if (!function_exists('MongoID')){
	function MongoID($objID=NULL) {
        
		if (class_exists('MongoDB\BSON\ObjectId')){
           
			if(isset($objID) && $objID!=NULL){
				return new MongoDB\BSON\ObjectId($objID);
			}
		}
		return NULL;
	}
}
if (!function_exists('MongoEPOCH')){
	function MongoEPOCH($obj=NULL) {
		if(isset($obj) && $obj!=NULL){
			$timestamp = (string)$obj/1000;
			return $timestamp;
		}
		return NULL;
	}
}