<?php
 
if (!defined('BASEPATH'))
    exit('No direct script access allowed');

/**
 * 
 * Site landing page related functions
 * @author Casperon
 *
 * */
class Landing extends MY_Controller {

    function __construct() {
        parent::__construct();
        $this->load->helper(array('cookie', 'date', 'form', 'email'));
        $this->load->library(array('encrypt', 'form_validation'));
        $this->load->model('user_model');
        $returnArr = array();
        
    }

    public function index() {  
        
        if ($this->lang->line('home_welcome') != '')
            $home_welcome =  stripslashes($this->lang->line('home_welcome'));
        else  $home_welcome =  'Welcome';
        
        $this->data['banner']=$this->user_model->get_all_details(BANNER, array('status' => 'Publish'));
        $this->data['heading'] = $home_welcome.' - '.$this->config->item('email_title');
		$this->data['landing_details']=$this->user_model->get_all_details(LANDING_CONTENT);
		
		$this->data['testimonials_details']=$this->user_model->get_all_details(TESTIMONIALS,array('status' => 'Active'));
		
        $this->load->view('site/landing/landing', $this->data);
    }

    public function changeLangage() {
        $choosenlonCode = $this->input->get('q');
        if ($choosenlonCode != '') {

            /* Load selected lang library */

            $defaultLanguage = $this->config->item('default_lang_code');
            if ($defaultLanguage == '') {
                $defaultLanguage = 'en';
            }

            $selectedLanguage = $choosenlonCode;
            $filePath = APPPATH . "language/" . $selectedLanguage . "/" . $selectedLanguage . "_lang.php";
            if ($selectedLanguage != '') {
                if (!(is_file($filePath))) {
                    $this->lang->load($defaultLanguage, $defaultLanguage);
                } else {
                    $this->lang->load($selectedLanguage, $selectedLanguage);
                }
            } else {
                $this->lang->load($defaultLanguage, $defaultLanguage);
            }


            $selectedLang = $this->user_model->get_all_details(LANGUAGES, array('lang_code' => $choosenlonCode));
            if ($selectedLang->num_rows() > 0) {
                $languageArr = array(APP_NAME.'langCode' => $selectedLang->row()->lang_code, APP_NAME.'langName' => $selectedLang->row()->name);
                $this->session->set_userdata($languageArr);
                $this->setErrorMessage('success', 'Language changed successfully', 'driver_lang_changed_success');
            } else {
                $this->setErrorMessage('error', 'Language not changed. Please try again', 'driver_lang_not_changed_success');
            }
        } else {
            $this->setErrorMessage('error', 'Language not changed. Please try again', 'driver_lang_not_changed_success');
        }
        redirect('');
    }
	
	
    public function load_image(){		
        $req_uri = $this->input->server('REQUEST_URI');
		$sName = $this->input->server('SCRIPT_NAME');
		$sName = str_replace("index.php","",$sName);
		$dirs = str_replace($sName,"",$req_uri);
        $img_path = $req_uri;
        $img_arr = explode('/', $img_path);
        $img_ext = explode('.', $img_arr[count($img_arr)-1]);
        $img_ext_arr = array('jpg','jpeg','png','gif');
		$file =basename($req_uri);
		$actualPath = str_replace($file,"",$dirs);
		
		if(file_exists('./'.$actualPath.$file)){
			if(in_array(strtolower($img_ext[1]),$img_ext_arr)){
				$fp = fopen('./'.$actualPath.$file, 'rb');
				header("Content-Type: image/png");
				header("Content-Length: " . filesize('./'.$actualPath.$file));
				fpassthru($fp);
				exit;
			}else{
				echo 'Request is not a valid image';
				$file =basename($req_uri);
				unlink('./'.$actualPath.$file);
				exit;
			}
		}else{
			echo 'Request file not found';
		}
    }
	
	/**
	* This function will calculate and returns the estimated fare as json response in ajax
	*
	*******/
	
	public function ajax_fare_estimate(){
		$returnArr['status']='0';
		$pickup_lat = $this->input->post('pickup_lat');
		$pickup_lon = $this->input->post('pickup_lon');
		
		$location = $this->app_model->find_location(floatval($pickup_lon), floatval($pickup_lat));
		$cat_fare = array();
		if (!empty($location['result'])) {
			$availCats = $location['result'][0]['avail_category'];
			$catsList = array();
			foreach($availCats as $cid){
				$catsList[] = MongoID($cid);
			}
			$getCats = $this->app_model->get_selected_fields(CATEGORY,array('_id' => array('$in' => $catsList),'status' => 'Active'),array('_id','name','name_languages'));
			$i = 0;
            $langCode = $this->data['langCode'];
			foreach($getCats->result() as $cats){
				$cat_id = (string) $cats->_id;
				if(isset($location['result'][0]['fare'][$cat_id])){
                
                    $category_name = $cats->name;
					if(isset($cats->name_languages[$langCode]) && $cats->name_languages[$langCode] != '') $category_name = $cats->name_languages[$langCode];
                 $service_tax = 0;
											if (isset($location['result'][0]['service_tax'])) {
												if ($location['result'][0]['service_tax'] > 0) {
													$service_tax = $location['result'][0]['service_tax'];
												}
											}
					$cat_fare[$i]['cat_name'] = $category_name;
					$cat_fare[$i]['service_tax'] = $service_tax;
					$cat_fare[$i]['fare'] = $location['result'][0]['fare'][$cat_id];
					$i++;
				}
			}
			$returnArr['status']='1';
		}	
		$returnArr['response'] = $cat_fare;
		$json_encode_new = json_encode($returnArr);
		echo $json_encode_new; 
	}

}

/* End of file landing.php */
/* Location: ./application/controllers/site/landing.php */