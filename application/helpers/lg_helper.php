<?php  if ( ! defined('BASEPATH')) exit('No direct script access allowed');


/**
*
* Returns the language array for the keywords
*
**/
if ( ! function_exists('get_language_files_for_keywords'))
{
	function get_language_files_for_keywords() {
		$languagPath = 'lg_files/keywords.json';
		$json_content = @file_get_contents($languagPath);
		$decoded_values = json_decode($json_content, TRUE);
		return $decoded_values;
	}
}
/**
*
* Returns the language array for the validation
*
**/
if ( ! function_exists('get_language_files_for_validation'))
{
	function get_language_files_for_validation() {
		$languagPath = 'lg_files/validation.json';
		$json_content = @file_get_contents($languagPath);
		$decoded_values = json_decode($json_content, TRUE);
		return $decoded_values;
	}
}

/**
*
* Returns the language value for the keyword
*
**/
if ( ! function_exists('get_language_value_for_keyword'))
{
	function get_language_value_for_keyword($value="",$lang_code="",$converted_values="") {
		$ci =& get_instance();
		
		$languagPath = 'lg_files/keywords.json';
		$json_content = @file_get_contents($languagPath);
		$decoded_values = json_decode($json_content, TRUE);
		
        
        
        if($value == 'Ridercancelled') $value = 'Rider Cancelled';
        if($value == 'Drivercancelled') $value = 'Driver Cancelled';
        
		$lang_key = FALSE;
		if(!empty($decoded_values)){
			$lang_key = array_search($value,$decoded_values);
		}		
		
		if($lang_key){ 
			$language_list_db = $ci->app_model->get_all_details(MULTI_LANGUAGES, array('language_code' => $lang_code,'type'=>"keyword"));
			
			if($language_list_db->num_rows()>0){
				if(isset($language_list_db->row()->key_values)){
					if(!empty($language_list_db->row()->key_values)){
						if(array_key_exists($lang_key,$language_list_db->row()->key_values)){
							$converted_values = $language_list_db->row()->key_values[$lang_key];
						}
					}
				}
			}
		}
		
		
		if($converted_values==""){
			$converted_values = $value;
		}
		return $converted_values;
	}
}

/**
*
* Returns the language array for the keyword
*
**/
if ( ! function_exists('get_language_array_for_keyword'))
{
	function get_language_array_for_keyword($lang_code="",$converted_values="") {
		$ci =& get_instance();
		
		$languagPath = 'lg_files/keywords.json';
		$json_content = @file_get_contents($languagPath);
		$lang_arrayS = json_decode($json_content, TRUE);
		
		$converted_array = array();
		if($lang_code!=""){
			$language_list_db = $ci->app_model->get_all_details(MULTI_LANGUAGES, array('language_code' => $lang_code,'type'=>"keyword"));
			
			if($language_list_db->num_rows()>0){
				if(isset($language_list_db->row()->key_values)){
					if(!empty($language_list_db->row()->key_values)){
						$converted_array = $language_list_db->row()->key_values;
					}
				}
			}
		}
		
		
		if(!empty($converted_array)){
			$lang_arrayS = $converted_array;
		}
		return $lang_arrayS;
	}
}

/**
*
* Returns the language value for the validation
*
**/
if ( ! function_exists('get_language_value_for_validation'))
{
	function get_language_value_for_validation($lang_key=FALSE,$lang_code="",$converted_values="") {
		$ci =& get_instance();
		
		$languagPath = 'lg_files/validation.json';
		$json_content = @file_get_contents($languagPath);
		$decoded_values = json_decode($json_content, TRUE);
				
		if($lang_key){
			$language_list_db = $ci->app_model->get_all_details(MULTI_LANGUAGES, array('language_code' => $lang_code,'type'=>"validation"));
			
			if($language_list_db->num_rows()>0){
				if(isset($language_list_db->row()->key_values)){
					if(!empty($language_list_db->row()->key_values)){
						if(array_key_exists($lang_key,$language_list_db->row()->key_values)){
							$converted_values = $language_list_db->row()->key_values[$lang_key];
						}
					}
				}
			}
		}
		
		
		if($converted_values==""){
			$converted_values = $value;
		}
		return $converted_values;
	}
}

/**
*
* Returns the language array for the keyword
*
**/
if ( ! function_exists('get_language_array_for_validation'))
{
	function get_language_array_for_validation($lang_code="",$converted_values="") {
		$ci =& get_instance();
		
		$languagPath = 'lg_files/validation.json';
		$json_content = @file_get_contents($languagPath);
		$lang_arrayS = json_decode($json_content, TRUE);
		
		$converted_array = array();
		if($lang_code!=""){
			$language_list_db = $ci->app_model->get_all_details(MULTI_LANGUAGES, array('language_code' => $lang_code,'type'=>"validation"));
			
			if($language_list_db->num_rows()>0){
				if(isset($language_list_db->row()->key_values)){
					if(!empty($language_list_db->row()->key_values)){
						$converted_array = $language_list_db->row()->key_values;
					}
				}
			}
		}
		
		
		if(!empty($converted_array)){
			$lang_arrayS = $converted_array;
		}
		return $lang_arrayS;
	}
}


/**
*
* Returns the language array for the SMS templates
*
**/
if ( ! function_exists('get_language_files_for_SMS'))
{
	function get_language_files_for_SMS() {
		$languagPath = 'lg_files/sms_templates.json';
		$json_content = @file_get_contents($languagPath);
		$decoded_values = json_decode($json_content, TRUE);
		return $decoded_values;
	}
}

/**
*
* Returns the language value for the SMS templates
*
**/
if ( ! function_exists('get_sms_content')){
	function get_sms_content($sms_key="",$lang_code="",$converted_values="") {
		$ci =& get_instance();
		
		if($lang_code==""){
			$lang_code = $ci->data['sms_lang_code'];
		}
		
		$languagPath = 'lg_files/sms_templates.json';
		$json_content = @file_get_contents($languagPath);
		$decoded_values = json_decode($json_content, TRUE);
		
		$value = "";
		if(!empty($decoded_values)){
			if(array_key_exists($sms_key,$decoded_values)){
				$value = $decoded_values[$sms_key];
			}
		}		
		
		if($sms_key!=""){
			$language_list_db = $ci->app_model->get_all_details(SMS_TEMPLATE, array('language_code' => $lang_code));
			
			if($language_list_db->num_rows()>0){
				if(isset($language_list_db->row()->key_values)){
					if(!empty($language_list_db->row()->key_values)){
						if(array_key_exists($sms_key,$language_list_db->row()->key_values)){
							$converted_values = $language_list_db->row()->key_values[$sms_key];
						}
					}
				}
			}
		}
		
		if($converted_values==""){
			$converted_values = $value;
		}
		return $converted_values;
	}
}



/**
*
* Returns the language value for  category name
*
**/
if ( ! function_exists('get_category_name_by_lang')){
	function get_category_name_by_lang($category_id="",$lang_code="") { 
		$ci =& get_instance(); 
		if($lang_code=="") $lang_code = $ci->data['langCode'];
		if (preg_match('/^[a-f\d]{24}$/i', $category_id)){
			$get_cat = $ci->user_model->get_selected_fields(CATEGORY,array('_id' => MongoID($category_id)),array('name_languages','name')); 
			$category_name = ''; 
			if($get_cat->num_rows() == 1){
				$category_name = $get_cat->row()->name;
				if(isset($get_cat->row()->name_languages[$lang_code]) && $get_cat->row()->name_languages[$lang_code] != '') $category_name = $get_cat->row()->name_languages[$lang_code];
			}
		}else{
			$category_name = $ci->config->item('pooling_name');
		}
		return $category_name;
	}
}


/**
*
* Returns the language value for  cancellation reason
*
**/
if ( ! function_exists('get_cancellation_reason_by_lang')){
	function get_cancellation_reason_by_lang($reason_id="",$lang_code="") { 
		$ci =& get_instance(); 
		if($lang_code=="") $lang_code = $ci->data['langCode'];  
		$get_reason = $ci->user_model->get_selected_fields(CANCELLATION_REASON,array('_id' => MongoID($reason_id)),array('name_languages','reason')); 
		$cancel_reason = ''; 
		if($get_reason->num_rows() == 1){
			$cancel_reason = $get_reason->row()->reason;
			if(isset($get_reason->row()->name_languages[$lang_code]) && $get_reason->row()->name_languages[$lang_code] != '') $cancel_reason = $get_reason->row()->name_languages[$lang_code];
		}
		return $cancel_reason;
	}
}


/**
*
* Returns the language array for the validation
*
**/
if ( ! function_exists('get_language_keys_for_datetime'))
{
	function get_language_keys_for_datetime($lang="") {
		$file_name = "datetime_lang";
		if($lang!="" && $lang!="en") $file_name = "datetime_lang_".$lang;
		$languagPath = 'lg_files/'.$file_name.'.php';
		$dateTime_keys = array();
		$dateTimeKeys = array();
		if(file_exists($languagPath)){
			require($languagPath);
			$dateTime_keys = array_filter($dateTimeKeys);
		}
		return $dateTime_keys;
	}
}

/**
*
* Returns the language array for the SMS templates
*
**/
if ( ! function_exists('get_time_to_string'))
{
	function get_time_to_string($format = "",$timestamp = "") {
        if($timestamp == '') $timestamp = time();
		$returnVal = date($format,$timestamp);
		if($format!="" && $timestamp!=""){
			$returnValTemp = $returnVal;
			$returnValTemp = str_replace('-',' ',$returnValTemp);
			$returnValTemp = str_replace(',',' ',$returnValTemp);
			$strg = @explode(' ',$returnValTemp);
			
			$ci =& get_instance(); $lang_code = "";
			if($lang_code=="") $lang_code = $ci->data['langCode'];
			
			$engDTLA = get_language_keys_for_datetime();
			if($lang_code!="" && $lang_code!="en"){
				$curLDTLA = get_language_keys_for_datetime($lang_code);
				$fudKey = array();
				$lngKey = array();
				if(!empty($strg)){
					foreach($strg as $key=>$value){
						/* if (preg_match('/[^A-Za-z]/', $value)){
							$arr = preg_split('/(?<=[0-9])(?=[a-z]+)/i',$value);
							if(count($arr)>1) print_r($arr);
						} */
						$value = trim($value);
						if($value!=""){
							$keyRet = array_search($value,$engDTLA);
							if($keyRet){
								$lngKey[$keyRet] = $value;
								if(array_key_exists($keyRet,$curLDTLA)) {
									$fudKey[$keyRet] = $curLDTLA[$keyRet];
									$returnVal = str_replace($value,$curLDTLA[$keyRet],$returnVal);
								}else {
									$fudKey[$keyRet] = $value;
								}
							}
						}
					}
				}
			}
		}
		if(isset($curLDTLA) && !empty($curLDTLA)){
			if(stripos($returnVal,'st')) {		
				if(array_key_exists('suffix_st',$curLDTLA)) {
					$fudKey['st'] = $curLDTLA['suffix_st'];
					$returnVal = str_replace('st',$curLDTLA['suffix_st'],$returnVal);
				}
			}
			if(stripos($returnVal,'th')) {		
				if(array_key_exists('suffix_th',$curLDTLA)) {
					$fudKey['th'] = $curLDTLA['suffix_th'];
					$returnVal = str_replace('th',$curLDTLA['suffix_th'],$returnVal);
				}
			}
			if(stripos($returnVal,'rd')) {		
				if(array_key_exists('suffix_rd',$curLDTLA)) {
					$fudKey['rd'] = $curLDTLA['suffix_rd'];
					$returnVal = str_replace('rd',$curLDTLA['suffix_rd'],$returnVal);
				}
			}
			if(stripos($returnVal,'nd')) {		
				if(array_key_exists('suffix_nd',$curLDTLA)) {
					$fudKey['nd'] = $curLDTLA['suffix_nd'];
					$returnVal = str_replace('nd',$curLDTLA['suffix_nd'],$returnVal);
				}
			}
		}
		
		return $returnVal;
	}
}

/**
*
* Change the language as per the @arg request
*
**/
if ( ! function_exists('change_web_language'))
{
	function change_web_language($lang_code="") {
		$ci =& get_instance();
		
		if($lang_code!=""){ 
			$selectedLang = $ci->app_model->get_all_details(LANGUAGES, array('lang_code' => $lang_code));
			
			if($selectedLang->num_rows() > 0){  
                
                $defaultLanguage = $ci->config->item('default_lang_code');
                if ($defaultLanguage == '') {
                    $defaultLanguage = 'en';
                }
				
                $selectedLanguage = $lang_code;
                $filePath = APPPATH . "language/" . $selectedLanguage . "/" . $selectedLanguage . "_lang.php";
                if ($selectedLanguage != '') {
                    if (!(is_file($filePath))) {
                        $ci->lang->load($defaultLanguage, $defaultLanguage);
                    } else {
                        $ci->lang->load($selectedLanguage, $selectedLanguage);
                    }
                } else {
                    $ci->lang->load($defaultLanguage, $defaultLanguage);
                }
                
                $languageArr = array(APP_NAME.'langCode' => $selectedLang->row()->lang_code, APP_NAME.'langName' => $selectedLang->row()->name);
                $ci->session->set_userdata($languageArr);
                
			}
		}
	}
}


/* End of file lg_helper.php */
/* Location: ./application/helpers/lg_helper.php */