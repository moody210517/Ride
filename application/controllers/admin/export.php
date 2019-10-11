<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');
/**
*
*	Export user and driver information in excel format
* 
*	@package	CI
*	@subpackage	Controller
*	@author	Casperon
*
**/

class Export extends MY_Controller {

	function __construct(){
        parent::__construct();
		$this->load->helper(array('cookie','date','form','csv','download'));
		$this->load->library(array('encrypt','form_validation'));		
		$this->load->model(array('export_model'));	
		if ($this->checkPrivileges('export',$this->privStatus) == FALSE){
			redirect(ADMIN_ENC_URL);
		}
    }
	/**
	* 
	* it export the user list as excel format 
	*
	* @param string $type  User filter type
	* @param string $value  User filter value
	* @return EXCEL,export the user list as excel
	*
	**/
	public function userlist(){
		$filterArr=array();
		
		if((isset($_POST['type']) && isset($_POST['value'])) && ($_POST['type']!='' && $_POST['value']!='')){
			if(isset($_POST['type']) && $_POST['type']!=''){
				$this->data['type']=$_POST['type'];
			}
			if(isset($_POST['value']) && $_POST['value']!=''){
				$this->data['value']=$_POST['value'];
				$filter_val= $this->data['value'];
			}
			if($this->data['type']!='location'){
				$filterArr=array($this->data['type']=>$filter_val);
			}else{
				$filterArr=array('address.street'=>$filter_val,'address.city'=>$filter_val,'address.state'=>$filter_val,'address.country'=>$filter_val,'address.zip_code'=>$filter_val);
			}
		}
		$selectedFileds=array("user_name","email","image","country_code","phone_number","referral_code","created");
		$usersList = $this->export_model->get_selected_fields(USERS,array(),$selectedFileds,'','','',$filterArr);
		
		if($usersList->num_rows()==0){
			$this->setErrorMessage('error','No Data found to export');
			echo "<script>window.history.go(-1)</script>";exit();
		 }else{
			$dataArr=array();
			$fields=array("user_name","email","image","country_code","phone_number","referral_code","created");
			$dataArr[]=$fields;
			
			if($usersList->num_rows()>0){
				foreach($usersList->result() as $row){
					if($row->image!=''){
						$image=base_url().USER_PROFILE_IMAGE.$row->image;
					}else{
						$image=base_url().USER_PROFILE_IMAGE_DEFAULT;
					}
					$dataArr[] = array('user_name'=>$row->user_name,
													'email'=>$row->email,
													'image'=>$image,
													'country_code'=>$row->country_code,
													'phone'=>$row->phone_number,
													'referral_code'=>$row->referral_code,
													'created'=>$row->created,
												);
				}
			}
			$fileName='user_list_'.time().'.csv';
			array_to_csv($dataArr,$fileName); 
			die;
		 }
	}
	/**
	* 
	* it export the driver list as excel format 
	*
	* @return EXCEL,export the driver list as excel
	*
	**/
	public function driverlist(){
		
		$selectedFileds=array("driver_name","email","image","dail_code","mobile_number","created");
		$driverList = $this->export_model->get_selected_fields(DRIVERS,array(),$selectedFileds);
		
		if($driverList->num_rows()==0){
			$this->setErrorMessage('error','No Data found to export');
			echo "<script>window.history.go(-1)</script>";exit();
		 }else{
			$dataArr=array();
			$fields=array("driver_name","email","image","country_code","phone_number","created");
			$dataArr[]=$fields;
			
			if($driverList->num_rows()>0){
				foreach($driverList->result() as $row){
					if($row->image!=''){
						$image=base_url().USER_PROFILE_IMAGE.$row->image;
					}else{
						$image=base_url().USER_PROFILE_IMAGE_DEFAULT;
					}
					$dataArr[] = array('driver_name'=>$row->driver_name,
													'email'=>$row->email,
													'image'=>$image,
													'country_code'=>$row->dail_code,
													'phone'=>$row->mobile_number,
													'created'=>$row->created,
												);
				}
			}
			$fileName='drivers_list_'.time().'.csv';
			array_to_csv($dataArr,$fileName); 
			die;
		 }
	}
  
}
/* End of file export.php */
/* Location: ./application/controllers/admin/export.php */