<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');
#require_once('Cimongo_base.php');
/**
 * CodeIgniter MongoDB Library
 *
 * A library to interact with the NoSQL database MongoDB.
 * For more information see http://www.mongodb.org
 *
 * @package		CodeIgniter
 * @author		Alessandro Arnodo | a.arnodo@gmail.com | @vesparny
 * @copyright	Copyright (c) 2012, Alessandro Arnodo.
 * @license		http://www.opensource.org/licenses/mit-license.php
 * @link
 * @version		Version 1.1.0
 *
 */

/**
 * Cimongo_cursor
 *
 * Cursor object, that behaves much like the MongoCursor, but permits to generating query results like CI
 * @since v1.0.0
 */
class Cimongo_cursor extends Mongo_db
{
	/**
	 * @var MongoCursor $_cursor the MongoCursor returned by the query
	 * @since v1.0.0
	 */
	protected $_cursor=array();

	/**
	 * Construct a new Cimongo_extras
	 *
	 * @param MongoCursor $cursor the cursor returned by the query
	 * @since v1.0.0
	 */
	public function __construct($cursor=array()){
		 $this->_cursor	= $cursor;
        
	}

	
    
    public function result($as_object=TRUE){
		$result = array();
		try {
            if($as_object) {
              $result=$this->_cursor;
            } else {
              foreach ($this->_cursor as $doc){
                 $result[]=(array)$doc;
              }
            }
		} catch (MongoDB\Driver\Exception $e)
		{
			if(isset($this->debug) == TRUE && $this->debug == TRUE)
			{
				show_error("MongoDB query failed: {$e->getMessage()}", 500);
			}
			else
			{
				show_error("MongoDB query failed.", 500);
			}
		}
       
		return $result;

	}

	/**
	 * Check if cursor is iterable, but maybe this could be done better FIXME
	 *
	 * @since v1.1.0
	 */
	public function has_error(){
		try {
			$this->_cursor->next();
		}catch (Exception  $exception){
			return $this->_handle_exception($exception->getMessage(),$as_object);
		}
		return FALSE;

	}

	/**
	 * Returns query results as an array
	 *
	 * @since v1.0.0
	 */
	public function result_array(){
		return $this->result(FALSE);

	}

	/**
	 * Returns query results as an object
	 *
	 * @since v1.0.0
	 */
	public function result_object(){
		return $this->result();

	}

	/**
	 * Returns the number of the documents fetched
	 *
	 * @since v1.0.0
	 */
	public function num_rows(){
		return $this->count(TRUE);
	}

	/**
	 * Returns the document at the specified index as an object
	 *
	 * @since v1.0.0
	 */
	public function row($index=0, $class=NULL, $as_object=TRUE) {
        $res=array();
		if(count($this->_cursor) > 0) {
            $res=(object)$this->_cursor[0];
        }
        return $res;
	}

	/**
	 * Returns the document at the specified index as an array
	 *
	 * @since v1.0.0
	 */
	public function row_array($index=0, $class=NULL){
		return $this->row($index, NULL, FALSE);
	}

	/**
	 * Skip the specified number of documents
	 *
	 * @since v1.0.0
	 */
	public function skip($x = FALSE){
		if ($x !== FALSE && is_numeric($x) && $x >= 1){
			return $this->_cursor->skip((int)$x);
		}
		return $this->_cursor;
	}


	/**
	 * Limit results to the specified number
	 *
	 * @since v1.0.0
	 */
	public function limit($x = FALSE){
		if ($x !== FALSE && is_numeric($x) && $x >= 1)
		{
			return $this->_cursor->limit((int)$x);
		}
		return $this->_cursor;
	}

	/**
	 * Sort by the field
	 *
	 * @since v1.0.0
	 */
	public function sort($fields) {
		return $this->_cursor->sort($fields);
	}

	/**
	 * Count the results
	 *
	 * @since v1.0.0
	 */
	public function count($foundOnly = FALSE) {
		$count = array();
		try {
			$array = $this->_cursor;
            
            $count=count($array);
		}catch (MongoCursorException $exception){
			show_error($exception->getMessage(), 500);
		}catch (MongoConnectionException $exception){
			show_error($exception->getMessage(), 500);
		}
		catch (MongoCursorTimeoutException $exception){
			show_error($exception->getMessage(), 500);
		}
		return $count;
	}
    
  	/**
	 * Private method to convert an array into an object
	 *
	 * @since v1.0.0
	 */
	
}