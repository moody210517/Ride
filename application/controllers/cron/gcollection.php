<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');
/** 
* 
* This Controller will used to remove the unused images from users, driver
* @author Casperon
*
**/
 
class Gcollection extends MY_Controller {
	function __construct(){
        parent::__construct();
		$this->load->helper(array('cookie','date','form','email'));
		$this->load->library(array('encrypt','form_validation'));
		
    }
	
	public function garbgaecollection() {
		
		$user_data=$this->app_model->get_selected_fields(USERS,array(),array('image'));
		$image_array=array();
		foreach($user_data->result() as $data) {
			if(isset($data->image) && $data->image!='') {
				$image_array[]=$data->image;
			}
		}
		
		$driver_data=$this->app_model->get_selected_fields(DRIVERS,array(),array('image','vehicle_image','documents'));
		$image_array2=array();
		
		$driver_document=array();
		foreach($driver_data->result() as $data) {
			if(isset($data->image) && $data->image!='') {
				$image_array2[]=$data->image;
			}
				
			
			if(isset($data->documents) && !empty($data->documents)) {
				foreach($data->documents as $key=>$record) {
					foreach($record as $file_name) {
						
						if($file_name['fileName']!='') {
							$driver_document[]=$file_name['fileName'];
						}
					}
					
				}
			}
		}
		$image_array3=array('default.jpg');
		$newarray=array();
		$newarray=array_merge($image_array,$image_array2,$image_array3);
		
		/** profile pictures removal unwwanted **/
		$dir = getcwd() . "/images/users/"; //dir absolute path
        foreach (glob($dir . "*.*") as $file) {
		  if(is_file($file) && !in_array(end(explode("/", $file)),$newarray))
			unlink($file); 
        }
		
		$dir = getcwd() . "/images/users/thumb/"; //dir absolute path
        foreach (glob($dir . "*.*") as $file) {
		  if(is_file($file) && !in_array(end(explode("/", $file)),$newarray))
			unlink($file); 
        }
		/** profile pictures removal unwwanted **/
		/** unwanted driver documents removal less than 24 hrs **/
		$dir = getcwd() . "/drivers_documents_temp/"; //dir absolute path
        $interval = strtotime('-24 hours'); //files older than 24hours
        foreach (glob($dir . "*.*") as $file) {
            if (filemtime($file) <= $interval) {
                unlink($file);
            }
        }
		/** unwanted driver documents removal less than 24 hrs **/
		$dir = getcwd() . "/drivers_documents/"; //dir absolute path
        
        foreach (glob($dir . "*.*") as $file) {
           if(is_file($file) && !in_array(end(explode("/", $file)), $driver_document))
			unlink($file); 
        }
		
	}
	
	
	
}

/* End of file gcollection.php */
/* Location: ./application/controllers/cron/gcollection.php */