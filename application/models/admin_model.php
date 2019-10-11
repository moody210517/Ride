<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');
/**
* 
* This model contains all db functions related to driver management
* @author Casperon
*
**/
class Admin_model extends My_Model
{
	public function __construct() 
	{
		parent::__construct();
	}
	
	public function add_edit_subdriver($dataArr='',$condition=''){
		if ($condition['id'] != ''){
			$this->db->where($condition);
			$this->db->update(ADMIN,$dataArr);
		}else {
			$this->db->insert(ADMIN,$dataArr);
		}
	}
	
	/**
    * 
    * This function save the driver details in a file
    */
   public function saveAdminSettings(){
		$getAdminSettingsDetails = $this->getAdminSettings();
		$config = '<?php ';
		foreach($getAdminSettingsDetails->row() as $key => $val){
			if($key!='admin_password' && $key!='privileges' && $key!='smtp' && $key!='country' && $key!='countryId' && $key!='currency'){
				if(is_array($val)){
					foreach($val as $ikey => $ival){
						$value = addslashes($ival);
						$config .= "\n\$config['$ikey'] = '$value'; ";
					}
				}else{				
					$value = addslashes($val);
					$config .= "\n\$config['$key'] = '$value'; ";
				}
			}
		}
		$config .= "\n\$config['base_url'] = '".base_url()."';\n ";
		$config .= ' ?>';
		$file = 'commonsettings/dectar_admin_settings.php';
		file_put_contents($file, $config);
   }
}