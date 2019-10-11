<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');
/** 
* 
* This controller is used to clear the records related to logs and ejabbered 
* @author Casperon
*
**/
 
class Cls_records extends MY_Controller {
	function __construct(){
        parent::__construct();
    }
	
	public function index() {
        include_once('./xmpp-master/config.php');
		$db_connec = @mysqli_connect(database_host,database_user,database_password,database_name); 
        if($db_connec) mysqli_query($db_connec,"TRUNCATE TABLE `archive`");
        file_put_contents("./xmpp-master/log.txt", "");
	}
}

/* End of file cls_records.php */
/* Location: ./application/controllers/cron/cls_records.php */