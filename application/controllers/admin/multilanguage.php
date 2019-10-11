<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

/**
*
*   Language Management
* 
*   @package    CI
*   @subpackage Controller
*   @author Casperon
*
**/

class Multilanguage extends MY_Controller {

    function __construct() {
        parent::__construct();
        $this->load->helper('file');
        $this->load->helper('language');
        $this->load->helper(array('cookie', 'date', 'form'));
        $this->load->library(array('encrypt', 'form_validation'));
        $this->load->model('admin_model');
        $this->load->model('multilanguage_model');
        $this->load->helper('directory');
        if ($this->checkPrivileges('multilang', $this->privStatus) == FALSE) {
            redirect(ADMIN_ENC_URL);
        }
    }

    
    function index() {
        $this->display_language_list();
    }

    /**
    * 
    * Display Language List
    *
    * @return HTML, Language List
    *
    **/
    function display_language_list() {

        if ($this->checkLogin('A') == '') {
            redirect(ADMIN_ENC_URL);
        } else {
		    if ($this->lang->line('admin_multilanguage_multi_language_management') != '') 
		    $this->data['heading']= stripslashes($this->lang->line('admin_multilanguage_multi_language_management')); 
		    else  $this->data['heading'] = 'Multi Language Management';
            $this->data['admin_settings'] = $result = $this->admin_model->getAdminSettings();
            $this->data['language_list'] = $result = $this->multilanguage_model->get_language_list();
            $this->load->view(ADMIN_ENC_URL.'/multilanguage/language_list', $this->data);
        }
    }

    /**
    * 
    * Display Add new language page
    * 
    * @return HTML, Add new language page
    *
    **/
    public function add_new_lg() {
        if ($this->checkLogin('A') == '') {
            show_404();
        } else {
		    if ($this->lang->line('admin_multilanguage_add_new_language') != '') 
		    $this->data['heading']= stripslashes($this->lang->line('admin_multilanguage_add_new_language')); 
		    else  $this->data['heading'] = 'Add New Language';
            $this->load->view(ADMIN_ENC_URL.'/multilanguage/add_new_lg', $this->data);
        }
    }

    /**
    * 
    * Dispaly Edit language Content Page 
    * 
    * @param string $selectedLanguage language code
    * @return HTML, Edit language Content Page
    *
    **/
    function edit_language() {
        if ($this->checkLogin('A') == '') {
            redirect(ADMIN_ENC_URL);
        } else {

            $file_name_prefix = 'file';
            $file_number = $this->uri->segment(5);

            $selectedLanguage = $this->uri->segment('4');
            $languagDirectory = APPPATH . 'language/' . $selectedLanguage;

            $get_english_lang_count = directory_map(APPPATH . "language/en/");

            $filePath = APPPATH . "language/" . $selectedLanguage . "/" . $file_name_prefix . $file_number . "_lang.php";
            if (!is_dir($languagDirectory)) {

                mkdir($languagDirectory, 0777);

                if (!is_file($filePath)) {

                    mkdir($languagDirectory, 0777);
                    file_put_contents($filePath, '');
                }
            }
            
            if (is_file($filePath)) {
                $this->lang->load($file_name_prefix . $file_number, $selectedLanguage);
            }

            $filePath = APPPATH . "language/en/" . $file_name_prefix . $file_number . "_lang.php";
            $fileValues = file_get_contents($filePath);

            $fileKeyValues_explode1 = @explode("\$lang['", $fileValues);
            $language_file_keys = array();
            foreach ($fileKeyValues_explode1 as $fileKeyValues2) {
                $fileKeyValues_explode2 = @explode("']", $fileKeyValues2);
                $language_file_keys[] = $fileKeyValues_explode2[0];
            }
            

            
            $fileValues_explode1 = @explode("']='", $fileValues);
            $language_file_values = array();

            foreach ($fileValues_explode1 as $fileValues2) {
                $fileValues_explode2 = @explode("';", $fileValues2);
                $language_file_values[] = $fileValues_explode2[0];
            }
            

            $this->data['file_key_values'] = $language_file_keys;
            $this->data['file_lang_values'] = $language_file_values;
            $this->data['selectedLanguage'] = $selectedLanguage;
		           if ($this->lang->line('admin_multilanguage_edit_language') != '') 
		          $this->data['heading']= stripslashes($this->lang->line('admin_multilanguage_edit_language')); 
		         else  $this->data['heading'] = 'Edit Language';
            $this->data['file_name_prefix'] = $file_name_prefix;
            $this->data['get_total_files'] = count($get_english_lang_count);
            $this->data['current_file_no'] = $file_number;
            $this->data['admin_settings'] = $result = $this->admin_model->getAdminSettings();
            $this->load->view(ADMIN_ENC_URL.'/multilanguage/language_edit', $this->data);
        }
    }

    /**
    * 
    * Update language Content
    * 
    * @param string $getLanguageKeyDetails  language keys
    * @param string $getLanguageContentDetails language values
    * @param string $selectedLanguage language code
    * @param string $file_name_prefix language File prefix
    * @param string $current_file_no language File no
    * @return HTML, Update language Content Page
    *
    **/
    function languageAddEditValues() {

        $getLanguageKeyDetails = $this->input->post('languageKeys');
        $getLanguageContentDetails = $this->input->post('language_vals');
        $selectedLanguage = $this->input->post('selectedLanguage');
        $file_name_prefix = $this->input->post('file_name_prefix');
        $current_file_no = $this->input->post('current_file_no');

        $loopItem = 0;
        $config = '<?php';
        foreach ($getLanguageKeyDetails as $key_val) {
            $language_file_values = addslashes($getLanguageContentDetails[$loopItem]);
            $config .= "\n\$lang['$key_val']='$language_file_values'; ";
            $loopItem = $loopItem + 1;
        }

        $config .= ' ?>';

        $languagDirectory = APPPATH . "language/" . $selectedLanguage;
        if (!is_dir($languagDirectory)) {
            mkdir($languagDirectory, 0777);
        }


        $filePath = APPPATH . "language/" . $selectedLanguage . "/" . $file_name_prefix . $current_file_no . "_lang.php";
        file_put_contents($filePath, $config);

        $get_folder_files = directory_map(APPPATH . "language/" . $selectedLanguage);



        


        $filePath = APPPATH . "language/" . $selectedLanguage . "/" . $selectedLanguage . "_lang.php";

        if (!is_file($filePath)) {
            mkdir($languagDirectory, 0777);
            file_put_contents($filePath, '');
        }
        file_put_contents($filePath, '');

        foreach ($get_folder_files as $file_name_dtls) {
            if ($file_name_dtls != $selectedLanguage . "_lang.php") {
                $open_file_to_append = APPPATH . "language/" . $selectedLanguage . "/" . $file_name_dtls;
                $handle = fopen($filePath, 'a');
                $data = file_get_contents($open_file_to_append);
                fwrite($handle, $data);
            }
        }
        
        $get_en_folder_files = directory_map(APPPATH . "language/en");

        $filePath = APPPATH . "language/en/en_lang.php";

        if (!is_file($filePath)) {
            mkdir($languagDirectory, 0777);
            file_put_contents($filePath, '');
        }
        file_put_contents($filePath, '');
        
        foreach ($get_en_folder_files as $file_name_dtls) {
            if ($file_name_dtls != "en_lang.php") {
                $open_file_to_append = APPPATH . "language/en/" . $file_name_dtls;
                $handle = fopen($filePath, 'a');
                $data = file_get_contents($open_file_to_append);
                fwrite($handle, $data);
            }
        }
        
				$this->setErrorMessage('success','Language content updated successfully','language_content_updated');
        redirect(ADMIN_ENC_URL.'/multilanguage/edit_language/' . $selectedLanguage . "/" . $current_file_no);
    }

    /**
    * 
    * Delete Language
    * 
    * @param string $languageId  language MongoDB\BSON\ObjectId
    * @return HTTP REDIRECT to Language List
    *
    **/
    function delete_language() {
        $languageId = $this->uri->segment('4');
        if ($languageId != '') {
            $languageDetails = $this->multilanguage_model->get_all_details(LANGUAGES, array('_id' => MongoID($languageId)));
            if ($languageDetails->num_rows() > 0) {
                if ($languageDetails->row()->default_language == 'Yes') {
                    $this->setErrorMessage('error', " You cannot remove the default language.",'admin_multilanguage_remove_default');
                } else {
                    $delete_language = $this->multilanguage_model->delete_language($languageId);
                    $this->setErrorMessage('success', " Language deleted successfully",'admin_multilanguage_delete_success');
                }
            }
            redirect(ADMIN_ENC_URL.'/multilanguage/display_language_list');
        } else {
            redirect(ADMIN_ENC_URL.'/multilanguage/display_language_list');
        }
    }

    /**
    * 
    * Change the status of multiple Languages
    * @param string $statusMode  Active/Inactive
    * @param string $checkbox_id  language MongoDB\BSON\ObjectId array
    * @return HTTP REDIRECT to Referer page  
    *
    **/
    function change_multi_language_details() {
        $statusMode = $this->input->post('statusMode');
        $checkbox_id = $this->input->post('checkbox_id');
        $checkboxId = array();
        foreach ($checkbox_id as $cid) {
            if ($cid != 'on' && $cid != 'off') {
                $checkboxId[] = MongoID($cid);
            }
        }
        if ($statusMode != '' && !empty($checkboxId)) {
            $change_language_status = $this->multilanguage_model->change_language_status($statusMode, $checkboxId);
            $this->setErrorMessage('success', " Language settings changed successfully",'admin_multilanguage_setting_change_success');
            redirect(ADMIN_ENC_URL.'/multilanguage/display_language_list');
        } else {
            redirect(ADMIN_ENC_URL);
        }
    }

    /**
    * 
    * Change the status of the language 
    * 
    * @param string $current_status Active/Inactive
    * @param string $languageId language MongoDB\BSON\ObjectId
    * @return HTTP REDIRECT to Language List  
    *
    **/
    function change_language_status() {
        $current_status = $this->uri->segment('4');
        $languageId = $this->uri->segment('5');
        if ($current_status != '' && $languageId != '') {
            $languageDetails = $this->multilanguage_model->get_all_details(LANGUAGES, array('_id' => MongoID($languageId)));
            if ($languageDetails->num_rows() > 0) {
                if ($languageDetails->row()->default_language == 'Yes') {
                    $this->setErrorMessage('error', " You cannot change the default language status.",'admin_multilanguage_defalut_language');
                } else {
                    $change_language_details = $this->multilanguage_model->change_language_details($current_status, $languageId);
                    $this->setErrorMessage('success', " Language settings changed successfully",'admin_multilanguage_language_setting_change');
                }
            }
            redirect(ADMIN_ENC_URL.'/multilanguage/display_language_list');
        } else {
            redirect(ADMIN_ENC_URL.'/multilanguage/display_language_list');
        }
    }

    /**
    * 
    * Change default language 
    * 
    * @param string $mode 0/1
    * @param string $languageId language MongoDB\BSON\ObjectId
    * @return HTTP REDIRECT to Language List  
    *
    **/
    public function change_language_default() {
        if ($this->checkLogin('A') == '') {
            redirect(ADMIN_ENC_URL);
        } else {
            $mode = $this->uri->segment(4, 0);
            $language_id = $this->uri->segment(5, 0);
            $status = ($mode == '0') ? 'No' : 'Yes';

            $languageDetails = $this->multilanguage_model->get_all_details(LANGUAGES, array('_id' => MongoID($language_id)));
            if ($languageDetails->num_rows() > 0) {
                if ($languageDetails->row()->default_language == 'Yes' && $status == 'No') {
                    $this->setErrorMessage('error', 'There should be atleast one default language.','admin_multilanguage_atleast_default_language');
                } else {
                    if ($languageDetails->row()->status == 'Inactive') {
                        $this->setErrorMessage('error', 'Default language should be in active status.','admin_multilanguage_default_language_active_status');
                    } else {
                        $filePath = APPPATH . "language/" . $languageDetails->row()->lang_code . "/" . $languageDetails->row()->lang_code . "_lang.php";
                        if (!is_file($filePath)) {
                            $this->setErrorMessage('error', 'This language is cannot make as defaul currently.','admin_multilanguage_cannot_default_currently');
                        } else {
                            $this->multilanguage_model->update_details(LANGUAGES, array('default_language' => 'No'), array('default_language' => 'Yes'));
                            $newdata = array('default_language' => "Yes");
                            $condition = array('_id' => MongoID($language_id));
                            $this->multilanguage_model->update_details(LANGUAGES, $newdata, $condition);
                            $getLanguage = $this->multilanguage_model->get_all_details(LANGUAGES, $condition);
                            
                            if (isset($getLanguage->row()->default_language)) {
                                if ($getLanguage->row()->default_language == 'Yes') {
                                    $file = 'commonsettings/dectar_lang_settings.php';
                                    if (!is_file($file)) {
                                        mkdir($languagDirectory, 0777);
                                        file_put_contents($file, '');
                                    }
                                    $lonCode = $getLanguage->row()->lang_code;
                                    $lonName = $getLanguage->row()->name;
                                    $config = '<?php ';
                                    $config .= "\n\$config['default_lang_code'] = '$lonCode'; ";
                                    $config .= "\n\$config['default_lang_name'] = '$lonName'; ";
                                    $config .= "\n ?>";
                                    file_put_contents($file, $config);
                                }
                            }
                            $this->setErrorMessage('success', 'This language changed As default','admin_multilanguage_language_default');
                        }
                    }
                }
            }
            redirect(ADMIN_ENC_URL.'/multilanguage/display_language_list');
        }
    }

    /**
    * 
    * Insert new Language 
    * 
    * @param string $lname  Language name
    * @param string $lang_code  Language code
    * @return HTTP REDIRCT Language List
    *
    **/ 
    public function add_lg_process() {
        if ($this->checkLogin('A') == '') {
            show_404();
        } else {
            $lname = $this->input->post('name');
            $lcode = $this->input->post('lang_code');
            $duplicateName = $this->multilanguage_model->get_all_details(LANGUAGES, array('name' => $lname));
            if ($duplicateName->num_rows() > 0) {
                $this->setErrorMessage('error', 'Language name already exists','admin_multilanguage_language_name_already_exist');
                echo "<script>window.history.go(-1);</script>";
                exit();
            } else {
                $duplicateCode = $this->multilanguage_model->get_all_details(LANGUAGES, array('lang_code' => $lcode));
                if ($duplicateCode->num_rows() > 0) {
                    $this->setErrorMessage('error', 'Language code already exists','admin_multilanguage_language_code_already_exist');
                    echo "<script>window.history.go(-1);</script>";
                    exit();
                } else {
                    $dataArr = array('default_language' => 'No');
                    if ($lcode == 'en') {
                        $dataArr = array('default_language' => 'Yes');
                        $file = 'commonsettings/dectar_lang_settings.php';
                        if (!is_file($file)) {
                            mkdir($languagDirectory, 0777);
                            file_put_contents($file, '');
                        }
                        $config = '<?php ';
                        $config .= "\n\$config['default_lang_code'] = '$lcode'; ";
                        $config .= "\n\$config['default_lang_name'] = '$lname'; ";
                        $config .= "\n ?>";
                        file_put_contents($file, $config);
                    }
                    $this->multilanguage_model->commonInsertUpdate(LANGUAGES, 'insert', array(), $dataArr);
                    $this->setErrorMessage('success', 'Language added successfully','language_added_success');
                    redirect(ADMIN_ENC_URL.'/multilanguage/display_language_list');
                }
            }
        }
    }

    /**
    * 
    * Display Add Mobile language Content page
    * 
    * @param string $selectedLang  Language Code
    * @return HTML, Mobile language Content
    *
    **/
    public function mobile_edit_language() {
        if ($this->checkLogin('A') == '') {
            redirect(ADMIN_ENC_URL);
        } else {
            $this->data['selectedLang'] = $selectedLang = $this->uri->segment('4');
            $this->data['admin_settings'] = $this->admin_model->getAdminSettings();
            $this->data['language_list'] = $this->multilanguage_model->get_language_list();
            $this->data['language_list_db'] = $this->multilanguage_model->get_all_details(MOBILE_LANGUAGES, array('language_code' => $selectedLang));
			
			if($selectedLang==""){
				$this->data['language_list_db'] = array();
			}
            $condition = array('language_code' => 'en');
            $langData = $this->multilanguage_model->get_selected_fields(MOBILE_LANGUAGES, $condition);
			$this->data['language_key_values'] = $this->loadLanguageFromJSON();
			
		    if ($this->lang->line('admin_edit_language_for_mobiles') != '') {
				$this->data['heading']= stripslashes($this->lang->line('admin_edit_language_for_mobiles')); 
			}else{
				$this->data['heading'] = 'Edit Languages for Mobiles';
			}
			
            $this->load->view(ADMIN_ENC_URL.'/multilanguage/mobile_language_list', $this->data);
        }
    }

    /**
    * 
    * Add/Edit Mobile language Content
    * 
    * @param string $getLanguageKeyDetails Language content Keys
    * @param string $getLanguageContentDetails Language content Values
    * @param string $selectedLang  Language Code
    * @return HTTP REDIRECT, Add/Edit Mobile language Content
    *
    **/
    public function add_edit_mobile_language() {
        if ($this->checkLogin('A') == '') {
            redirect(ADMIN_ENC_URL);
        } else {
            $getLanguageKeyDetails = $this->input->post('languageKeys');
            $getLanguageContentDetails = $this->input->post('language_vals');
            $selectedLang = $this->input->post('selectedLang');
            $langArr = array();
            $excludeArray = array('languageKeys', 'language_vals', 'selectedLang');
            $loopItem = 0;
            foreach ($getLanguageKeyDetails as $key_val) {
                $langArr[$key_val] = $getLanguageContentDetails[$loopItem];
                $loopItem = $loopItem + 1;
            }
            $finalArray = array('key_values' => $langArr);
            $condition = array('language_code' => $selectedLang);
            $checkLangExists = $this->multilanguage_model->get_selected_fields(MOBILE_LANGUAGES, $condition);

            if ($checkLangExists->num_rows() == 0) {
                $finalArray['language_code'] = $selectedLang;
                $this->multilanguage_model->commonInsertUpdate(MOBILE_LANGUAGES, 'insert', $excludeArray, $finalArray);
				$this->setErrorMessage('success','Language key value updated','language_key_value_added');
            } else {
                $this->multilanguage_model->commonInsertUpdate(MOBILE_LANGUAGES, 'update', $excludeArray, $finalArray, $condition);
				$this->setErrorMessage('success','Language key value updated','language_key_value_added');
            }
			redirect(ADMIN_ENC_URL.'/multilanguage/mobile_edit_language/'.$selectedLang);
        }
    }


	/**
    * 
    * Display Add/Edit language Keywords page
    * 
    * @param string $selectedLang  Language Code
    * @return HTML,  Add/Edit language Keywords page
    *
    **/
    public function keyword_edit_language() {
        if ($this->checkLogin('A') == '') {
            redirect(ADMIN_ENC_URL);
        } else {
            $this->data['selectedLang'] = $selectedLang = $this->uri->segment('4');
            $this->data['language_list'] = $this->multilanguage_model->get_language_list();
            $this->data['language_list_db'] = $this->multilanguage_model->get_all_details(MULTI_LANGUAGES, array('language_code' => $selectedLang,'type'=>"keyword"));
            $this->data['language_key_values'] = get_language_files_for_keywords();
			
		    if ($this->lang->line('admin_edit_language_for_keywords') != '') {
				$this->data['heading']= stripslashes($this->lang->line('admin_edit_language_for_keywords')); 
			}else{
				$this->data['heading'] = 'Edit Languages for Keywords';
			}
			
            $this->load->view(ADMIN_ENC_URL.'/multilanguage/keyword_language_list', $this->data);
        }
    }
	
	/**
    * 
    * Display Add/Edit validation content page
    * 
    * @param string $selectedLang  Language Code
    * @return HTML,  Add/Edit validation content page
    *
    **/
    public function validation_edit_language() {
        if ($this->checkLogin('A') == '') {
            redirect(ADMIN_ENC_URL);
        } else {
			$this->data['selectedLang'] = $selectedLang = $this->uri->segment('4');
			$this->data['language_list'] = $this->multilanguage_model->get_language_list();
			$this->data['language_list_db'] = $this->multilanguage_model->get_all_details(MULTI_LANGUAGES, array('language_code' => $selectedLang,'type'=>"validation"));
			$this->data['language_key_values'] = get_language_files_for_validation();
			
		    if ($this->lang->line('admin_edit_language_for_validation') != '') {
				$this->data['heading']= stripslashes($this->lang->line('admin_edit_language_for_validation')); 
			}else{
				$this->data['heading'] = 'Edit Languages for Validation Messages';
			}
			
            $this->load->view(ADMIN_ENC_URL.'/multilanguage/validation_language_list', $this->data);
        }
    }

    /**
    * 
    * Add/Edit language Keywords/validation
    * 
    * @param string $selectedLang  Language Code
    * @param string $type keyword/validation
    * @param string $getLanguageKeyDetails  Language keys for Keywords/validation
    * @param string $getLanguageContentDetails  Language content for Keywords/validation
    * @return HTTP REDIRECT  Add/Edit language Keywords/validation
    *
    **/
    public function add_edit_keyval_language() {
        if ($this->checkLogin('A') == '') {
            redirect(ADMIN_ENC_URL);
        } else {
            $getLanguageKeyDetails = $this->input->post('languageKeys');
            $getLanguageContentDetails = $this->input->post('language_vals');
            $selectedLang = $this->input->post('selectedLang');
            $type = $this->input->post('type');
            $langArr = array();
            $excludeArray = array('languageKeys', 'language_vals', 'selectedLang');
            $loopItem = 0;
            foreach ($getLanguageKeyDetails as $key_val) {
                $langArr[$key_val] = $getLanguageContentDetails[$loopItem];
                $loopItem = $loopItem + 1;
            }
            $finalArray = array('key_values' => $langArr);
            $condition = array('language_code' => $selectedLang,'type' => (string)$type);
            $checkLangExists = $this->multilanguage_model->get_selected_fields(MULTI_LANGUAGES, $condition);

            if ($checkLangExists->num_rows() == 0) {
               $finalArray['language_code'] = $selectedLang;
				$this->setErrorMessage('success','Language key value updated','language_key_value_added');
               $this->multilanguage_model->commonInsertUpdate(MULTI_LANGUAGES, 'insert', $excludeArray, $finalArray);
            } else {
				$this->setErrorMessage('success','Language key value updated','language_key_value_added');
               $this->multilanguage_model->commonInsertUpdate(MULTI_LANGUAGES, 'update', $excludeArray, $finalArray, $condition);
            }
			if($type=="keyword"){
				redirect(ADMIN_ENC_URL.'/multilanguage/keyword_edit_language/');
			}else{
				redirect(ADMIN_ENC_URL.'/multilanguage/validation_edit_language/');
			}
            
        }
    }
	
	/**
    * 
    * Display Edit datetime language page
    * 
    * @param string $selectedLang  Language Code
    * @return HTML,  Edit  datetime language page
    *
    **/
    public function datetime_edit_language() {
        if ($this->checkLogin('A') == '') {
            redirect(ADMIN_ENC_URL);
        } else {
			$this->data['selectedLang'] = $selectedLang = (string)$this->uri->segment(4);
			$this->data['language_list'] = $this->multilanguage_model->get_language_list();			
			
			$language_list_sel = array();
			if($selectedLang!="") $language_list_sel = get_language_keys_for_datetime($selectedLang);
			$this->data['language_list_sel'] = $language_list_sel;
			$this->data['language_key_values'] = get_language_keys_for_datetime();
			
		    if ($this->lang->line('admin_edit_language_for_datetime') != '') {
				$this->data['heading']= stripslashes($this->lang->line('admin_edit_language_for_datetime')); 
			}else{
				$this->data['heading'] = 'Edit Languages for Date & Time';
			}
			
            $this->load->view(ADMIN_ENC_URL.'/multilanguage/datetime_language_list', $this->data);
        }
    }
	
	/**
    * 
    * Update datetime language
    * 
    * @param string $selectedLang  Language Code
    * @param string $getLanguageKeyDetails  Language keys for datetime
    * @param string $getLanguageContentDetails  Language content for datetime
    * @return HTTP REDIRECT edit datetime language
    *
    **/
	public function add_edit_datetime_language() {
        if ($this->checkLogin('A') == '') {
            redirect(ADMIN_ENC_URL);
        } else {
            $getLanguageKeyDetails = $this->input->post('languageKeys');
            $getLanguageContentDetails = $this->input->post('language_vals');
            $selectedLang = $this->input->post('selectedLang');
            $type = $this->input->post('type');
            $langArr = array();
            $loopItem = 0;
			$newStrValFPK = "";
			
			/* echo "<pre>"; print_r($getLanguageKeyDetails); 
			echo "<pre>"; print_r($getLanguageContentDetails);  */
			
            foreach ($getLanguageKeyDetails as $key_val) {
				if($key_val!=""){
					$newStrValFPK .= "\n";
					$newStrValFPK .= '$dateTimeKeys["'.$key_val.'"]="'.$getLanguageContentDetails[$key_val].'";';
				}
                $langArr[$key_val] = $getLanguageContentDetails[$loopItem];
                $loopItem = $loopItem + 1;
            }
			
			if(!empty($langArr)){
				$newStrVal = json_encode($langArr);
				$newStrValFP = "<?php ";
				$newStrValFP .= "$newStrValFPK";
				$newStrValFP .= "\n?>";
				
				$file_name = "datetime_lang";
				if($selectedLang!="" && $selectedLang!="en"){
					$file_name = "datetime_lang_".$selectedLang;
					$languagPath = 'lg_files/'.$file_name.'.php';
					file_put_contents($languagPath, $newStrValFP);
				}
			}
			$this->setErrorMessage('success','Language key value updated','language_key_value_added');
			redirect(ADMIN_ENC_URL.'/multilanguage/datetime_edit_language/'.$selectedLang);            
        }
    }
}

/* End of file multilanguage.php */
/* Location: ./application/controllers/admin/multilanguage.php */