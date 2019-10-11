<?php if (!defined('BASEPATH')) exit('No direct script access allowed'); 

#error_reporting(E_ALL);
#ini_set('display_errors', 'on');
error_reporting(0); 
/**
* 
* This controller contains the common functions
* @author Casperon
*
**/
date_default_timezone_set('Asia/Kolkata');

class MY_Controller extends CI_Controller {
	
    public $privStatus;
    public $data = array();
    public $loadedLang;
    public $app_language = 'en';

    Public $Apptype = '';
    Public $Userid = '';
    Public $Driverid = '';
    Public $Token = '';
		
	
    Public $temp_lang = '';
    Public $tempLang = '';

    function __construct() {
        parent::__construct();
        ob_start();
        ob_clean();
		
        $this->load->helper(array('url', 'cookie', 'directory', 'text','lg_helper','mileage_helper','mongodb_helper'));
        $this->load->library('mongo_db');
        $this->load->library(array('pagination', 'session', 'googlemaps'));
        $this->load->library('resizeimage', FALSE);
        $this->load->library('currencyget');
        $this->output->set_header('Cache-Control: no-store, no-cache, must-revalidate, post-check=0, pre-check=0');
        $this->output->set_header('Pragma: no-cache');
        $this->output->set_header('Content-Type: text/html; charset=utf-8');

        $this->load->model(array('user_model','mail_model','sms_model','billing_model','app_model'));
		
		$site_timezone_str = $this->config->item('site_timezone');
		if($site_timezone_str!=""){
			$timezone = @explode(",",$site_timezone_str);
			if(isset($timezone[0]) && $timezone[0]!="") date_default_timezone_set($timezone[0]);
		}
        

        ini_set("mongo.native_long", 0);
        ini_set("mongo.long_as_object", 1);
		
        $uriMethod = $this->uri->segment(3, 0);
        if (substr($uriMethod, 0, 7) == 'display' || substr($uriMethod, 0, 4) == 'view' || $uriMethod == '0') {
            $this->privStatus = '0';
        } else if (substr($uriMethod, 0, 3) == 'add' || substr($uriMethod, 0, 6) == 'insert') {
            $this->privStatus = '1';
        } else if (substr($uriMethod, 0, 4) == 'edit' || substr($uriMethod, 0, 6) == 'insert' || substr($uriMethod, 0, 6) == 'change') {
            $this->privStatus = '2';
        } else if (substr($uriMethod, 0, 6) == 'delete') {
            $this->privStatus = '3';
        } else {
            $this->privStatus = '0';
        }

        $this->data['title'] = $this->config->item('meta_title');
        $this->data['heading'] = '';

        $this->data['flash_data'] = $this->session->flashdata('sErrMSG');
        $this->data['flash_data_type'] = $this->session->flashdata('sErrMSGType');
        $this->data['flash_data_key'] = $this->session->flashdata('sErrMSGKey');

        $this->data['adminPrevArr'] = $this->config->item('adminPrev');
        $this->data['adminEmail'] = $this->config->item('email');
        $this->data['privileges'] = (array)json_decode($this->session->userdata(APP_NAME.'_session_admin_privileges'));
        $this->data['subAdminMail'] = $this->session->userdata(APP_NAME.'_session_admin_email');
        $this->data['allPrev'] = '0';
        $this->data['logo'] = $this->config->item('logo_image');
        $this->data['favicon'] = $this->config->item('favicon_image');
        $this->data['footer'] = $this->config->item('footer_content');
        $this->data['siteContactMail'] = $this->config->item('site_contact_mail');
        $this->data['siteContactNumber'] = $this->config->item('site_contact_number');
        $this->data['siteTitle'] = $this->config->item('email_title');
        $this->data['meta_title'] = $this->config->item('meta_title');
        $this->data['meta_keyword'] = $this->config->item('meta_keyword');
        $this->data['meta_description'] = $this->config->item('meta_description');
		if($this->data['title']=="") $this->data['title'] = $this->config->item('email_title');

        $this->data['billing_cycle'] = intval($this->config->item('billing_cycle'));
        $this->data['last_billing_date'] = $this->config->item('last_billing_date');
		$this->data['multiCategoryOption'] = $this->config->item('multi_category_option');
		
		
		/***************      App download links    ****************/
		$this->data['user_playstore_link'] = $this->config->item('user_playstore_link');
		$this->data['driver_playstore_link'] = $this->config->item('driver_playstore_link');
		
		$this->data['user_appstore_link'] = $this->config->item('user_appstore_link');
		$this->data['driver_appstore_link'] = $this->config->item('driver_appstore_link');
		
		
        $this->data['map_searching_radius'] = $this->config->item('map_searching_radius');
		if($this->data['map_searching_radius'] < 10000){
			$this->data['map_searching_radius'] = 10000;
		}
		
        $this->data['user_timeout'] = intval($this->config->item('user_timeout'));
		if($this->data['user_timeout'] < 0){
			$this->data['user_timeout'] = 60;
		}
		
		$this->data['share_pooling_status'] = "0";
		if($this->config->item('share_pooling') !=""){
			$this->data['share_pooling_status'] = $this->config->item('share_pooling');
		}
        
		$google_maps_api_key = $this->config->item('google_maps_api_key');
		$this->data['google_maps_api_key'] = '';
		if($google_maps_api_key!=''){
			$this->data['google_maps_api_key'] = '&key='.trim($google_maps_api_key);
		}

        $hasBIlling = $this->billing_model->check_billing_cycle($this->data['billing_cycle'], $this->data['last_billing_date']);

        $this->data['billing_job'] = 'No';
		if ($hasBIlling === TRUE) {
			$this->data['billing_job'] = 'Yes';
        }
			if($this->uri->segment(1) == 'admin' && $this->uri->segment(2) == 'api'){
			header('Access-Control-Allow-Origin: *');
			header('Access-Control-Allow-Headers: Content-Type,Authkey,AuthToken');
		}
        $this->data['sideMenu'] = '';
        $this->data['sidebar_id'] = $this->session->userdata(APP_NAME.'session_sidebar_id');
        if ($this->session->userdata(APP_NAME.'_session_admin_name') == $this->config->item('admin_name')) {
            $this->data['allPrev'] = '1';
        }


        $this->data['datestring'] = "%Y-%m-%d %H:%i:%s";
        if ($this->checkLogin('U') != '') {
            $this->data['common_user_id'] = $this->checkLogin('U');
        } elseif ($this->checkLogin('T') != '') {
            $this->data['common_user_id'] = $this->checkLogin('T');
        } else {
            $temp_id = substr(number_format(time() * rand(), 0, '', ''), 0, 6);
            $this->session->set_userdata(APP_NAME.'_session_temp_id', $temp_id);
            $this->data['common_user_id'] = $temp_id;
        }

        $this->data['dEmail'] = $dEmail = "xxxx@yyy.zzz";
        $this->data['dMobile'] = $dEmail = "99xxxxxxx456";
        
		$this->data['isDemo'] = $isDemo = FALSE;

        $this->data['countryList'] = $this->user_model->get_all_details(COUNTRY, array('status' => 'Active'), array('name' => 1))->result();
        $this->data['dialCodeList'] = $this->user_model->get_all_details(COUNTRY, array('status' => 'Active'), array('dial_code' => 1))->result();

        /**
        * Currency Values
        **/
        $this->data['dcurrencyName'] = $dcurrencyName = $this->config->item('currency_name');
        $this->data['dcurrencyCode'] = $dcurrencyCode = $this->config->item('currency_code');
        $this->data['dcurrencySymbol'] = $dcurrencySymbol = $this->config->item('currency_symbol');
		
		/**
		*
		*	Default distance units 
		*
		**/
		$this->data['d_distance_unit'] = (string)$this->config->item('short_du'); # 'km';
		$this->data['d_distance_unit_name'] = (string)$this->config->item('long_du'); # 'Kilometer';
		$this->data['d_country_code'] = (string)$this->config->item('dialCode'); #'+91'; 
        $this->data['d_country_cca3'] = (string)$this->config->item('countryCode'); #'IND';

		/**
		* Payment Settings
		**/
        $this->data['authorize_net_settings'] = unserialize($this->config->item('payment_0'));
        $this->data['paypal_settings'] = unserialize($this->config->item('payment_1'));
        $this->data['stripe_settings'] = $stripe_settings = unserialize($this->config->item('payment_2')); 

        $auto_card_pay = 'No';
        if (isset($stripe_settings['status'])) {
            if ($stripe_settings['status'] == 'Enable' && $stripe_settings['settings']['secret_key'] != '' && $stripe_settings['settings']['publishable_key'] != '') {
                $auto_card_pay = 'Yes';
            }
        }
        $this->data['auto_charge'] = $auto_card_pay;

		if($_SERVER['HTTP_HOST']=="192.168.1.244"){
			$this->data['soc_url'] = 'http://192.168.1.244/xmpp-master/';
		}else if($_SERVER['HTTP_HOST']=="booktaxi.casperon.co"){
			$this->data['soc_url'] = 'https://ejabberd.casperon.co/xmpp-master/';
		}else{
			$this->data['soc_url'] = base_url().'xmpp-master/';
		}
        $this->data['website_tracking']='trackuser';
		
		$this->data['phone_masking_status'] = "No";

        /* Multi language script loader start */
        $headers = $this->input->request_headers();
        if (array_key_exists("Isapplication", $headers)) {
			$this->output->set_header('Content-Type: text/json; charset=utf-8');
            if (array_key_exists("Applanguage", $headers)) {
                $this->app_language = $headers["Applanguage"];
                $dLangDataDB = $this->languageDataFromDb();
                $LangDataJSON = $this->loadLanguageFromJSON();
				
				$this->mailLang = 'en';
                if ($dLangDataDB->num_rows() > 0) {
					$this->mailLang = $dLangDataDB->row()->language_code;
					if(isset($dLangDataDB->row()->key_values)){
						$tr = $dLangDataDB->row()->key_values;
						//print_r($tr);
						$this->loadedLang = $tr;
					}
                } else {
                    $this->loadedLang = $LangDataJSON;
                }
				if(empty($this->loadedLang)){
					$this->loadedLang = $LangDataJSON;
				}
				$this->temp_lang =$this->mailLang;
            }
        } else {
            $this->languageConfiguration();
			$this->temp_lang = $this->data['langCode'];
			$this->mailLang = $this->data['langCode'];
        }
        /* Multi language script loader end */
		$this->data['langCode'] = $this->data['sms_lang_code'] = $this->temp_lang;
		
		/* Dynamic Header and Footer Menu */
		$page_url = $this->uri->segment(1);
		$allowPages = array('rider','pages','driver','login','signup','emergency-contact');
		if($page_url == '' || in_array($page_url,$allowPages)){
			$lang = $this->data['dLangCode'];
			if($this->data['langCode'] != $this->data['dLangCode']) $lang = $this->data['langCode'];
			
			$header_menu = $this->user_model->get_selected_fields(MENU,array('name'=>'top_menu'),array('added_pages','add_home_navigation'));
			if($header_menu->num_rows()>0){
				$this->data['header_home'] = 'no';
				if(isset($header_menu->row()->add_home_navigation) && $header_menu->row()->add_home_navigation == 'yes'){
					$this->data['header_home'] = 'yes';
				}
				$hmenu_details = array();
				if(isset($header_menu->row()->added_pages) && !empty($header_menu->row()->added_pages)){
					$i=0;
					foreach($header_menu->row()->added_pages as $header_page){
						if($header_page!=''){
							$detail = $this->user_model->get_all_details(CMS,array('_id'=>MongoID($header_page),'status'=>'Publish'));
							if($detail->num_rows() > 0){
								$seourl = $detail->row()->seourl;
								$hmenu_details[$i] = array('name'=>$detail->row()->page_name,'url'=>$seourl);
								if(isset($detail->row()->$lang) && !empty($detail->row()->$lang)){
									$detail = $detail->row()->$lang;
									$hmenu_details[$i] = array('name'=>$detail['page_name'],'url'=>$seourl);
								}
								$i++;
							}
						}
					}
				}
				$this->data['header_menu'] = $hmenu_details;
			}
			
			$footer_menu = $this->user_model->get_selected_fields(MENU,array('name'=>'footer_menu'),array('added_pages','add_home_navigation'));
			if($footer_menu->num_rows()>0){
				$this->data['footer_home'] = 'no';
				if(isset($footer_menu->row()->add_home_navigation)){
					if($footer_menu->row()->add_home_navigation == 'yes'){
						$this->data['footer_home'] = 'yes';
					}	
				}
				$fmenu_details = array();
				if(isset($footer_menu->row()->added_pages) && !empty($footer_menu->row()->added_pages)){
					$i=0;
					foreach($footer_menu->row()->added_pages as $footer_page){
						if($footer_page!=''){
							$detail = $this->user_model->get_all_details(CMS,array('_id'=>MongoID($footer_page),'status'=>'Publish'));
							$seourl = ""; if(isset($detail->row()->seourl)) $seourl = $detail->row()->seourl;	
							if($seourl!=""){
								$fmenu_details[$i] = array('name'=>$detail->row()->page_name,'url'=>$seourl);
								if(isset($detail->row()->$lang)){
									if(!empty($detail->row()->$lang)){
										$detail = $detail->row()->$lang;
										$fmenu_details[$i] = array('name'=>$detail['page_name'],'url'=>$seourl);
									}
								}
								$i++;
							}
						}
					}
				}
				$this->data['footer_menu'] = $fmenu_details;
			}
		}
		/* Dynamic Header and Footer Menu --------- Ends Here */
		
		if($page_url == 'driver' && $this->checkLogin('D') != ''){
			$driver_id = $this->checkLogin('D');
			$this->data['driver_info'] = $this->user_model->get_all_details(DRIVERS,array('_id' => MongoID($driver_id)));
		}
    }

    /**
     * 
     * This function return the session value based on param
     * @param $type
     */
    public function checkLogin($type = '') {
        if ($type == 'A') {
            return $this->session->userdata(APP_NAME.'_session_admin_id');
        } else if ($type == 'N') {
            return $this->session->userdata(APP_NAME.'_session_admin_name');
        } else if ($type == 'M') {
            return $this->session->userdata(APP_NAME.'_session_admin_email');
        } else if ($type == 'P') {
            return $this->session->userdata(APP_NAME.'_session_admin_privileges');
        } else if ($type == 'U') {
            return $this->session->userdata(APP_NAME.'_session_user_id');
        } else if ($type == 'D') {
            return $this->session->userdata(APP_NAME.'_session_driver_id');
        } else if ($type == 'T') {
            return $this->session->userdata(APP_NAME.'_session_temp_id');
        } else if ($type == 'O') {
            return $this->session->userdata(APP_NAME.'_session_operator_id');
        }  else if ($type == 'C') {
            return $this->session->userdata(APP_NAME.'_session_company_id');
        }
    }

    /**
     * 
     * This function set the error message and type in session
     * @param string $type
     * @param string $msg
     */
    public function setErrorMessage($type = '', $msg = '', $langKey = '') {
		$org_msg = strip_tags($msg);
        if ($langKey != '') {
            if ($this->lang->line($langKey) != '') {
                $msg = stripslashes($this->lang->line($langKey));
            }
        }
        $msg = base64_encode($msg);
        ($type == 'success') ? $msgVal = 'message-green' : $msgVal = 'message-red';
		
		if($type == 'success'){
			if ($this->lang->line('admin_success') != ''){
				$keyVal = stripslashes($this->lang->line('admin_success'));
			}else{
				$keyVal = 'Success';
			}
		}else{
			if ($this->lang->line('admin_error') != ''){
				$keyVal = stripslashes($this->lang->line('admin_error'));
			}else{
				$keyVal = 'Error';
			}
		}
		
        $this->session->set_flashdata('sErrMSGKey', base64_encode($keyVal));
        $this->session->set_flashdata('sErrMSGType', $msgVal);
        $this->session->set_flashdata('sErrMSG', $msg);
	   
	   	if($this->uri->segment(1) == ADMIN_ENC_URL){
			$this->write_into_log_file($type,$org_msg);
		}
    }

    /**
     * 
     * This function check the admin privileges
     * @param String $name	->	Management Name
     * @param Integer $right	->	0 for view, 1 for add, 2 for edit, 3 delete
     */
    public function checkPrivileges($name = '', $right = '') {
        $prev = '0';
        $privileges = (array)json_decode($this->session->userdata(APP_NAME.'_session_admin_privileges'));
        @extract($privileges);
        $userName = $this->session->userdata(APP_NAME.'_session_admin_name');
        $adminName = $this->config->item('admin_name');
        if ($userName == $adminName) {
            $prev = '1';
        }
        if (isset(${$name}) && is_array(${$name}) && in_array($right, ${$name})) {
            $prev = '1';
        }
        if ($prev == '1') {
            return TRUE;
        } else {
            return FALSE;
        }
    }

    /**
     * 
     * Generate random string
     * @param Integer $length
     *
     * */
    public function get_rand_str($length = '6') {
        return substr(str_shuffle("0123456789abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ"), 0, $length);
    }
	
	/**
     * 
     * Generate random string
     * @param Integer $length
     *
     * */
	public function sms_chunk_split($msg,$count=120) {
	   $msg = preg_replace('/[\r\n]+/', ' ', $msg);
	   $chunks = wordwrap($msg, $count, '\n');
	   return explode('\n', $chunks);
	}

    /**
     * 
     * Clean string 
     * @param String $orig_text
     *
     * */
    public function cleanString($orig_text) {
        $text = $orig_text;
        // Single letters
        $text = preg_replace("/[∂άαáàâãªä]/u", "a", $text);
        $text = preg_replace("/[∆лДΛдАÁÀÂÃÄ]/u", "A", $text);
        $text = preg_replace("/[ЂЪЬБъь]/u", "b", $text);
        $text = preg_replace("/[βвВ]/u", "B", $text);
        $text = preg_replace("/[çς©с]/u", "c", $text);
        $text = preg_replace("/[ÇС]/u", "C", $text);
        $text = preg_replace("/[δ]/u", "d", $text);
        $text = preg_replace("/[éèêëέëèεе℮ёєэЭ]/u", "e", $text);
        $text = preg_replace("/[ÉÈÊË€ξЄ€Е∑]/u", "E", $text);
        $text = preg_replace("/[₣]/u", "F", $text);
        $text = preg_replace("/[НнЊњ]/u", "H", $text);
        $text = preg_replace("/[ђћЋ]/u", "h", $text);
        $text = preg_replace("/[ÍÌÎÏ]/u", "I", $text);
        $text = preg_replace("/[íìîïιίϊі]/u", "i", $text);
        $text = preg_replace("/[Јј]/u", "j", $text);
        $text = preg_replace("/[ΚЌК]/u", 'K', $text);
        $text = preg_replace("/[ќк]/u", 'k', $text);
        $text = preg_replace("/[ℓ∟]/u", 'l', $text);
        $text = preg_replace("/[Мм]/u", "M", $text);
        $text = preg_replace("/[ñηήηπⁿ]/u", "n", $text);
        $text = preg_replace("/[Ñ∏пПИЙийΝЛ]/u", "N", $text);
        $text = preg_replace("/[óòôõºöοФσόо]/u", "o", $text);
        $text = preg_replace("/[ÓÒÔÕÖθΩθОΩ]/u", "O", $text);
        $text = preg_replace("/[ρφрРф]/u", "p", $text);
        $text = preg_replace("/[®яЯ]/u", "R", $text);
        $text = preg_replace("/[ГЃгѓ]/u", "r", $text);
        $text = preg_replace("/[Ѕ]/u", "S", $text);
        $text = preg_replace("/[ѕ]/u", "s", $text);
        $text = preg_replace("/[Тт]/u", "T", $text);
        $text = preg_replace("/[τ†‡]/u", "t", $text);
        $text = preg_replace("/[úùûüџμΰµυϋύ]/u", "u", $text);
        $text = preg_replace("/[√]/u", "v", $text);
        $text = preg_replace("/[ÚÙÛÜЏЦц]/u", "U", $text);
        $text = preg_replace("/[Ψψωώẅẃẁщш]/u", "w", $text);
        $text = preg_replace("/[ẀẄẂШЩ]/u", "W", $text);
        $text = preg_replace("/[ΧχЖХж]/u", "x", $text);
        $text = preg_replace("/[ỲΫ¥]/u", "Y", $text);
        $text = preg_replace("/[ỳγўЎУуч]/u", "y", $text);
        $text = preg_replace("/[ζ]/u", "Z", $text);

        // Punctuation
        $text = preg_replace("/[‚‚]/u", ",", $text);
        $text = preg_replace("/[`‛′’‘]/u", "'", $text);
        $text = preg_replace("/[″“”«»„]/u", '"', $text);
        $text = preg_replace("/[—–―−–‾⌐─↔→←]/u", '-', $text);
        $text = preg_replace("/[  ]/u", ' ', $text);

        $text = str_replace("…", "...", $text);
        $text = str_replace("≠", "!=", $text);
        $text = str_replace("≤", "<=", $text);
        $text = str_replace("≥", ">=", $text);
        $text = preg_replace("/[‗≈≡]/u", "=", $text);


        // Exciting combinations    
        $text = str_replace("ыЫ", "bl", $text);
        $text = str_replace("℅", "c/o", $text);
        $text = str_replace("₧", "Pts", $text);
        $text = str_replace("™", "tm", $text);
        $text = str_replace("№", "No", $text);
        $text = str_replace("Ч", "4", $text);
        $text = str_replace("‰", "%", $text);
        $text = preg_replace("/[∙•]/u", "*", $text);
        $text = str_replace("‹", "<", $text);
        $text = str_replace("›", ">", $text);
        $text = str_replace("‼", "!!", $text);
        $text = str_replace("⁄", "/", $text);
        $text = str_replace("∕", "/", $text);
        $text = str_replace("⅞", "7/8", $text);
        $text = str_replace("⅝", "5/8", $text);
        $text = str_replace("⅜", "3/8", $text);
        $text = str_replace("⅛", "1/8", $text);
        $text = preg_replace("/[‰]/u", "%", $text);
        $text = preg_replace("/[Љљ]/u", "Ab", $text);
        $text = preg_replace("/[Юю]/u", "IO", $text);
        $text = preg_replace("/[ﬁﬂ]/u", "fi", $text);
        $text = preg_replace("/[зЗ]/u", "3", $text);
        $text = str_replace("£", "(pounds)", $text);
        $text = str_replace("₤", "(lira)", $text);
        $text = preg_replace("/[‰]/u", "%", $text);
        $text = preg_replace("/[↨↕↓↑│]/u", "|", $text);
        $text = preg_replace("/[∞∩∫⌂⌠⌡]/u", "", $text);


        //2) Translation CP1252.
        $trans = get_html_translation_table(HTML_ENTITIES);
        $trans['f'] = '&fnof;';    // Latin Small Letter F With Hook
        $trans['-'] = array(
            '&hellip;', // Horizontal Ellipsis
            '&tilde;', // Small Tilde
            '&ndash;'       // Dash
        );
        $trans["+"] = '&dagger;';    // Dagger
        $trans['#'] = '&Dagger;';    // Double Dagger         
        $trans['M'] = '&permil;';    // Per Mille Sign
        $trans['S'] = '&Scaron;';    // Latin Capital Letter S With Caron        
        $trans['OE'] = '&OElig;';    // Latin Capital Ligature OE
        $trans["'"] = array(
            '&lsquo;', // Left Single Quotation Mark
            '&rsquo;', // Right Single Quotation Mark
            '&rsaquo;', // Single Right-Pointing Angle Quotation Mark
            '&sbquo;', // Single Low-9 Quotation Mark
            '&circ;', // Modifier Letter Circumflex Accent
            '&lsaquo;'  // Single Left-Pointing Angle Quotation Mark
        );

        $trans['"'] = array(
            '&ldquo;', // Left Double Quotation Mark
            '&rdquo;', // Right Double Quotation Mark
            '&bdquo;', // Double Low-9 Quotation Mark
        );

        $trans['*'] = '&bull;';    // Bullet
        $trans['n'] = '&ndash;';    // En Dash
        $trans['m'] = '&mdash;';    // Em Dash        
        $trans['tm'] = '&trade;';    // Trade Mark Sign
        $trans['s'] = '&scaron;';    // Latin Small Letter S With Caron
        $trans['oe'] = '&oelig;';    // Latin Small Ligature OE
        $trans['Y'] = '&Yuml;';    // Latin Capital Letter Y With Diaeresis
        $trans['euro'] = '&euro;';    // euro currency symbol
        ksort($trans);

        foreach ($trans as $k => $v) {
            $text = str_replace($v, $k, $text);
        }

        // 3) remove <p>, <br/> ...
        $text = strip_tags($text);

        // 4) &amp; => & &quot; => '
        $text = html_entity_decode($text);


        // transliterate
        // if (function_exists('iconv')) {
        // $text = iconv('utf-8', 'us-ascii//TRANSLIT', $text);
        // }
        // remove non ascii characters
        #$text =  preg_replace('/[\x00-\x1F\x80-\xFF]/', '', $text);      


        return $text;
    }

    /**
     * This function send the notification for mobile app
     * @param Array $regIds
     * @param String $message 
     * @param string $action 
     * @param string $type 
     * @param Array $urlval 
     * */
    public function sendPushNotification($regIds, $message = '', $action = '', $type = '', $urlval = array(), $app = '') {
        if ($message != '') {
            $msg = array();
            $msg ['message'] = $message;
            $msg ['action'] = $action;
            $i = 1;
			if($msg['action']!="driver_loc"){
				foreach ($urlval as $vals) {
					$msg['key' . $i] = (string) $vals;
					$i++;
				}
			}
			if($msg['action']=="driver_loc"){
				$msg ['latitude'] = (string)$urlval['latitude'];
				$msg ['longitude'] = (string)$urlval['longitude'];
				$msg ['bearing'] = (string)$urlval['bearing'];
				$msg ['ride_id'] = (string)$urlval['ride_id'];
			}
         
            if (is_array($regIds)) {
                $regIds = $regIds;
            } else {
                $regIds = array($regIds);
            }
			 
              if (!empty($regIds) && ($type == 'ANDROID' || $type == 'IOS')) {
                $send_message_web = json_encode($msg);
				$send_message = urlencode(json_encode($msg)); 
				if($msg['action']=="ride_confirmed" || $msg['action']=="cab_arrived" ||  $msg['action']=="trip_begin" || $msg['action']=="payment_paid" || $msg['action']=="make_payment") {
					
					   $fields = array(
											'username' =>$this->data['website_tracking'],
											'message' => (string) $send_message_web
										);
						$url = $this->data['soc_url'] . 'sendMessage.php';
						$this->load->library('curl');
						$output = $this->curl->simple_post($url, $fields);
				
				}
                if (!empty($regIds)) {
                    if ($app == 'DRIVER') {
                        $collection = DRIVERS;
                        $checkField = 'push_notification.key';
                    } else if ($app == 'USER') {
                        $collection = USERS;
                        if ($type == 'ANDROID') {
                            $checkField = 'push_notification_key.gcm_id';
                        } else if ($type == 'IOS') {
                            $checkField = 'push_notification_key.ios_token';
                        }
                    }

                    $usersList = $this->user_model->get_user_ids_from_device($collection, $regIds, $checkField);
                    if ($usersList->num_rows() > 0) {
                        foreach ($usersList->result() as $ids) {
                            
							$token = '';
							if ($app == 'DRIVER') {
								$token = $ids->push_notification['key'];
							} else if ($app == 'USER') {
								if ($type == 'ANDROID') {
									$token = $ids->push_notification_key['gcm_id'];
								} else if ($type == 'IOS') {
									$token = $ids->push_notification_key['ios_token'];
								}
							}
                            if($type == 'ANDROID'){
                                if($msg['action']=="ride_request"  || $msg['action']=="new_trip"){
                                    $username = (string) $ids->_id;
                                    if ($username != '') {
                                        $fields = array(
                                            'username' => $username,
                                            'message' => (string) $send_message
                                        );
                                        $url = $this->data['soc_url'] . 'sendMessage.php';
                                        $this->load->library('curl');
                                        $output = $this->curl->simple_post($url, $fields);
                                        $this->sendPushNotificationToGCMOrg($regIds, $msg, $app);
                                    }
                                }
                            }
							if(isset($ids->messaging_status)){
								if($ids->messaging_status == 'available' && ($type == 'IOS' || $type == 'ANDROID')){
									$idss = array_search($token, $regIds);
									if($idss !== false) {
										unset($regIds[$idss]);
									}
									$username = (string) $ids->_id;
									if ($username != '') {
										$fields = array(
											'username' => $username,
											'message' => (string) $send_message
										);
										$url = $this->data['soc_url'] . 'sendMessage.php';
										$this->load->library('curl');
										$output = $this->curl->simple_post($url, $fields);
									}
								}else if($ids->messaging_status == 'available'){
									$idss = array_search($token, $regIds);
									if($idss !== false) {
										unset($regIds[$idss]);
									}
									$username = (string) $ids->_id;
									if ($username != '') {
										$fields = array(
											'username' => $username,
											'message' => (string) $send_message
										);
										$url = $this->data['soc_url'] . 'sendMessage.php';
										$this->load->library('curl');
										$output = $this->curl->simple_post($url, $fields);
									}
								}
							}else{
								$idss = array_search($token, $regIds);
								if($idss !== false) {
									unset($regIds[$idss]);
								}
								$username = (string) $ids->_id;
								if ($username != '') {
									$fields = array(
										'username' => $username,
										'message' => (string) $send_message
									);
									$url = $this->data['soc_url'] . 'sendMessage.php';
									$this->load->library('curl');
									$output = $this->curl->simple_post($url, $fields);
								}
							}
                        }
                    }
                }
            }
			
			if (!empty($regIds) && $type == 'ANDROID') {
				$this->sendPushNotificationToGCMOrg($regIds, $msg, $app);
                
			}
			if (!empty($regIds) && $type == 'IOS') {
                
				#$this->push_notification($regIds, $msg, $app);
				$this->push_notification_apns($regIds, $msg, $app);
               
			}
        }
    }

    /**
     * This function send the notification for Anriod app
     * @param string $registration_ids
     * @param string $message 
     * */
    public function sendPushNotificationToGCMOrg($registration_ids, $message, $app) {

        //Google cloud messaging GCM-API url
        #$url = 'https://android.googleapis.com/gcm/send';
        $url = 'https://fcm.googleapis.com/fcm/send';
        if (!is_array($registration_ids)) {
            $registration_ids = array($registration_ids);
        }
        if (!is_array($message)) {
            $message = array("message" => $message);
        }

        $fields = array(
            'registration_ids' => $registration_ids,
            'data' => $message,
        );
        
        // Google Cloud Messaging GCM API Key
        if ($app == 'DRIVER') {
            $google_key = $this->config->item('push_android_driver');
        } else if ($app == 'USER') {
            $google_key = $this->config->item('push_android_user');
        }
        define("GOOGLE_API_KEY", $google_key);
        $headers = array(
            'Authorization: key=' . GOOGLE_API_KEY,
            'Content-Type: application/json'
        );
        $ch = curl_init();
        curl_setopt($ch, CURLOPT_URL, $url);
        curl_setopt($ch, CURLOPT_POST, true);
        curl_setopt($ch, CURLOPT_HTTPHEADER, $headers);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, 0);
        curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
        curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode($fields));
        $result = curl_exec($ch);
       
        if ($result === FALSE) {
            die('Curl failed: ' . curl_error($ch));
        }
        curl_close($ch);
       
        return $result;
    }

    /**
     * This function send the notification for IOS app
     * @param string $deviceId
     * @param string $message 
     * */
    public function push_notification($deviceId, $message, $app) {
    
        $this->load->library('apns');
        if (is_array($deviceId) && !empty($deviceId)) {
            $this->apns->send_push_message($deviceId, $message, $app);
        } else {
            $this->apns->send_push_message(array($deviceId), $message, $app);
        }

        
    }
    public function push_notification_apns($deviceId,$message, $app) {
        $url = "https://fcm.googleapis.com/fcm/send";
        if (is_array($deviceId) && !empty($deviceId)) {
            $device_token=$deviceId;
        } else {
           $device_token=array($deviceId);
        }
        $title=$this->config->item('email_title');
        $fcm_leacy_server_key = $this->config->item('push_ios_key');
        #$fcm_leacy_server_key = 'AIzaSyDOQt3gnuEUiq01-0pYXs0MQZpmezo_RY0';
        $notification = array('title' =>$title,'text' =>$message['message'], 'sound' => 'default', 'badge' => '0');
        $message_array=array();
        $message_array = array("message" => json_encode($message));
        $arrayToSend = array('registration_ids'=>$device_token,'notification' => $notification,'priority'=>'high','data' => $message_array);
        
        if($message['action']=='ride_request'){
            $arrayToSend['time_to_live']=30;
        }
        $json = json_encode($arrayToSend);
        $headers = array();
        $headers[] = 'Content-Type: application/json';
        $headers[] = 'Authorization: key='. $fcm_leacy_server_key;
        $ch = curl_init();
        curl_setopt($ch, CURLOPT_URL, $url);
        curl_setopt($ch, CURLOPT_CUSTOMREQUEST,"POST");
        curl_setopt($ch, CURLOPT_POSTFIELDS, $json);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, TRUE);
        curl_setopt($ch, CURLOPT_HTTPHEADER,$headers);
        $resultss = curl_exec($ch);

        if ($resultss === FALSE) {
            die('FCM Send Error: ' . curl_error($ch));
        }
        curl_close($ch);
        return $resultss;
    }

    /**
     *
     * Resize the image
     * @param int target_width
     * @param int target_height
     * @param string image_name
     * @param string target_path
     *
     * */
    public function imageResizeWithSpace($box_w, $box_h, $userImage, $savepath) {
        $thumb_file = $savepath . $userImage;
        list($w, $h, $type, $attr) = getimagesize($thumb_file);
        $size = getimagesize($thumb_file);
        switch ($size["mime"]) {
            case "image/jpeg":
                $img = imagecreatefromjpeg($thumb_file); //jpeg file
                break;
            case "image/gif":
                $img = imagecreatefromgif($thumb_file); //gif file
                break;
            case "image/png":
                $img = imagecreatefrompng($thumb_file); //png file
                break;
            default:
                $im = false;
                break;
        }
        $new = imagecreatetruecolor($box_w, $box_h);
        if ($new === false) {
            //creation failed -- probably not enough memory
            return null;
        }
        $fill = imagecolorallocate($new, 255, 255, 255);
        imagefill($new, 0, 0, $fill);

        //compute resize ratio
        $hratio = $box_h / imagesy($img);
        $wratio = $box_w / imagesx($img);
        $ratio = min($hratio, $wratio);

        if ($ratio > 1.0)
            $ratio = 1.0;

        //compute sizes
        $sy = floor(imagesy($img) * $ratio);
        $sx = floor(imagesx($img) * $ratio);

        $m_y = floor(($box_h - $sy) / 2);
        $m_x = floor(($box_w - $sx) / 2);

        if (!imagecopyresampled($new, $img, $m_x, $m_y, //dest x, y (margins)
                        0, 0, //src x, y (0,0 means top left)
                        $sx, $sy, //dest w, h (resample to this size (computed above)
                        imagesx($img), imagesy($img)) //src w, h (the full size of the original)
        ) {
            //copy failed
            imagedestroy($new);
            return null;
        }
        if (isset($i))
            imagedestroy($i);
        imagejpeg($new, $thumb_file, 99);
    }

    /**
     * Image resize
     * @param int $width
     * @param int $height
     * @param string $targetImage Name
     * @param string $savepath 
     * */
    public function ImageResizeWithCrop($width, $height, $thumbImage, $savePath) {
        $thumb_file = $savePath . $thumbImage;
        $newimgPath = base_url() . substr($savePath, 2) . $thumbImage;
        /* Get original image x y */
        list($w, $h) = getimagesize($thumb_file);
        $size = getimagesize($thumb_file);
        /* calculate new image size with ratio */
        $ratio = max($width / $w, $height / $h);
        $h = ceil($height / $ratio);
        $x = ($w - $width / $ratio) / 2;
        $w = ceil($width / $ratio);
        /* new file name */
        $path = $savePath . $thumbImage;
        /* read binary data from image file */

        $imgString = file_get_contents($newimgPath);
        /* create image from string */
        $image = imagecreatefromstring($imgString);
        $tmp = imagecreatetruecolor($width, $height);
        imagecopyresampled($tmp, $image, 0, 0, $x, 0, $width, $height, $w, $h);

        /* Save image */
        switch ($size["mime"]) {
            case 'image/jpeg':
                imagejpeg($tmp, $path, 100);
                break;
            case 'image/png':
                imagepng($tmp, $path, 0);
                break;
            case 'image/gif':
                imagegif($tmp, $path);
                break;
            default:
                exit;
                break;
        }
        return $path;
        /* cleanup memory */
        imagedestroy($image);
        imagedestroy($tmp);
    }

    /**
     * Image Compress
     * @param int $quality
     * @param string $source_url 
     * @param string $destination_url 
     * */
    public function ImageCompress($source_url, $destination_url = '', $quality = 70) {
        $info = getimagesize($source_url);
        $savePath = $source_url;

        if ($info['mime'] == 'image/jpeg')
            $image = imagecreatefromjpeg($savePath);
        elseif ($info['mime'] == 'image/gif')
            $image = imagecreatefromgif($savePath);
        elseif ($info['mime'] == 'image/png')
            $image = imagecreatefrompng($savePath);
        ### Saving Image
        imagejpeg($image, $savePath, $quality);
    }

    /**
     * Get Image resolution type
     * @param string $destination_url 
     * */
    public function getImageShape($width, $height, $target_file) {
        list($w, $h) = getimagesize($target_file);
        if ($w == $width && $h == $height) {
            $option = "exact";
        } else if ($w == $h) {
            $option = "exact";
        } else if ($w > $h) {
            $option = "landscape";
        } else if ($w < $h) {
            $option = "portrait";
        } else {
            $option = "crop";
        }
        return $option;
    }

    /**
     * 
     * Complete the payment process through stripe pay and auto payment for ride booking
     *
     * */
    public function common_auto_stripe_payment_process($payData = array()) {
        $user_id = $payData['user_id'];
        $total_amount = $payData['total_amount'];
        $ride_id = $payData['ride_id'];
        $email = $this->input->post('stripeEmail');
		
        $checkRide = $this->app_model->get_all_details(RIDES, array('ride_id' => $ride_id, 'user.id' => $user_id));
        $this->app_model->update_details(RIDES, array('pay_status' => 'Processing'), array('ride_id' => $ride_id));

        if ($checkRide->num_rows() == 1) {
            $getUsrCond = array('_id' => MongoID($user_id));
            $get_user_info = $this->app_model->get_selected_fields(USERS, $getUsrCond, array('email', 'stripe_customer_id'));
            if ($email == '') {
                $email = $get_user_info->row()->email;
            }

            $stripe_customer_id = '';
            if (isset($get_user_info->row()->stripe_customer_id)) {
                $stripe_customer_id = $get_user_info->row()->stripe_customer_id;
                if ($stripe_customer_id != '') {
					$chkCard = $this->get_stripe_card_details($stripe_customer_id);
					if($chkCard['error_status'] == '0'){
						$this->app_model->update_details(USERS, array('stripe_customer_id' => ''), $getUsrCond);
					}
                }
            }

            require_once('./stripe/lib/Stripe.php');

            $stripe_settings = $this->data['stripe_settings'];
            $secret_key = $stripe_settings['settings']['secret_key'];
            $publishable_key = $stripe_settings['settings']['publishable_key'];

            $stripe = array(
                "secret_key" => $secret_key,
                "publishable_key" => $publishable_key
            );

		  $product_description = ucfirst($this->config->item('email_title')) . ' - Ride Charge #'.$ride_id;
		  
	        $currency = $this->data['dcurrencyCode'];
            $currency_status=$this->get_stripe_provide_currency($currency);
            if($currency_status) {
				$amounts = $this->get_stripe_currency_smallest_unit($total_amount,$currency);
            } else {
                $original_currency = 'USD';
                if($currency != $original_currency){
                    $currencyval = $this->app_model->get_currency_value(round($total_amount, 2), $currency, $original_currency);
                    if (!empty($currencyval)) { 
                    $amounts = round($currencyval['CurrencyVal']*100);}
                    $currency='USD';
                }
            }
            Stripe::setApiKey($secret_key);
            $token = $this->input->post('stripeToken');
            try {
                // Create a Customer

                $customer_id = $stripe_customer_id;
                if ($customer_id == '' && $token!="") {
                    $customer = Stripe_Customer::create(array(
                                "card" => $token,
                                "description" => $product_description,
                                "email" => $email)
                    );
                    $customer_id = $customer->id;
                }
				if($customer_id!=""){
					$this->app_model->update_details(USERS, array('stripe_customer_id' => $customer_id), $getUsrCond);
					// Charge the Customer instead of the card
					$charge = Stripe_Charge::create(array(
								"amount" => $amounts, # amount in cents, again
								"currency" => $currency,
								"customer" => $customer_id,
								"description" => $product_description)
					);


					$paymentData = array('user_id' => $user_id, 'ride_id' => $ride_id, 'payType' => 'stripe', 'stripeTxnId' => $charge['id']);
					$sendSucc = $this->auto_pay_success($paymentData);


					$returnArr['status'] = '1';
					$returnArr['msg'] = 'Transaction Successful';
				}else{
					$returnArr['status'] = '0';
					$returnArr['msg'] = 'Transaction Failed';
				}
                return $returnArr;
            } catch (Exception $e) {
                $error = $e->getMessage();
                if ($error == '') {
                    $error = 'Payment Failed';
                }
				#$this->app_model->update_details(USERS, array('stripe_customer_id' => ''), $getUsrCond);
				
                /** *************  respond back to app ************ */
                $returnArr['status'] = '0';
                $returnArr['msg'] = 'Transaction Failed, : ' . $error;
                return $returnArr;
            }
        } else {
            $returnArr['status'] = '0';
            $returnArr['msg'] = 'Transaction Failed , Invalid User';
            return $returnArr;
        }
    }

    /**
     * 
     * Loading success payment
     *
     * */
    public function auto_pay_success($paymentData) {
        $this->load->model('mail_model');
        $user_id = $paymentData['user_id'];
        $ride_id = $paymentData['ride_id'];
        $payment_type = $paymentData['payType'];
        $trans_id = $paymentData['stripeTxnId'];
        $payment_status = 'Completed';

        if ($payment_status == 'Completed') {
            $checkRide = $this->app_model->get_all_details(RIDES, array('ride_id' => $ride_id));
            if ($checkRide->num_rows() == 1) {
                if ($checkRide->row()->pay_status == 'Pending' || $checkRide->row()->pay_status == 'Processing') {
                    $paid_amount = 0.00;
                    if (isset($checkRide->row()->total)) {
                        if (isset($checkRide->row()->total['grand_fare']) && isset($checkRide->row()->total['wallet_usage'])) {
                            $paid_amount = round(($checkRide->row()->total['grand_fare'] - $checkRide->row()->total['wallet_usage']), 2);
                        }
                    }
                    $pay_summary = 'Gateway';
                    if (isset($checkRide->row()->pay_summary['type'])) {
                        if ($checkRide->row()->pay_summary['type'] != '') {
                            if ($checkRide->row()->pay_summary['type'] != 'Gateway') {
                                $pay_summary = $checkRide->row()->pay_summary['type'] . '_Gateway';
                            }
                        } else {
                            $pay_summary = 'Gateway';
                        }
                    }
					
					$tips_amt = 0.00; 
					if (isset($checkRide->row()->total['tips_amount']) && $checkRide->row()->total['tips_amount'] > 0) {
						$tips_amt = $checkRide->row()->total['tips_amount'];
					}
					$paid_amount_with_tips = $paid_amount + $tips_amt;
					
					
                    $pay_summary = array('type' => $pay_summary);
                    $paymentInfo = array('ride_status' => 'Completed',
                        'pay_status' => 'Paid',
                        'history.pay_by_gateway_time' => MongoDATE(time()),
                        'total.paid_amount' => round(floatval($paid_amount), 2),
                        'total.paid_amount_with_tips' => round(floatval($paid_amount_with_tips), 2),
                        'pay_summary' => $pay_summary
                    );
                    $this->app_model->update_details(RIDES, $paymentInfo, array('ride_id' => $ride_id));
                    /* Update Stats Starts */
                    $current_date = MongoDATE(strtotime(date("Y-m-d 00:00:00")));
                    $field = array('ride_completed.hour_' . date('H') => 1, 'ride_completed.count' => 1);
                    $this->app_model->update_stats(array('day_hour' => $current_date), $field, 1);
                    /* Update Stats End */

                    $avail_data = array('mode' => 'Available', 'availability' => 'Yes');
                    $driver_id = $checkRide->row()->driver['id'];
                    $this->app_model->update_details(DRIVERS, $avail_data, array('_id' => MongoID($driver_id)));
                    $transactionArr = array('type' => 'Card',
                        'amount' => floatval($paid_amount),
                        'trans_id' => $trans_id,
                        'trans_date' => MongoDATE(time())
                    );
                    $this->app_model->simple_push(PAYMENTS, array('ride_id' => $ride_id), array('transactions' => $transactionArr));
                    $driverVal = $this->app_model->get_selected_fields(DRIVERS, array('_id' => MongoID($driver_id)), array('_id', 'push_notification'));
                    if ($driverVal->num_rows() > 0) {
                        if (isset($driverVal->row()->push_notification)) {
                            if ($driverVal->row()->push_notification != '') {
                                $message=$this->format_string("Payment completed", "payment_completed");
                                $options = array('ride_id' => (string) $ride_id, 'driver_id' => $driver_id);
                                if (isset($driverVal->row()->push_notification['type'])) {
                                    if ($driverVal->row()->push_notification['type'] == 'ANDROID') {
                                        if (isset($driverVal->row()->push_notification['key'])) {
                                            if ($driverVal->row()->push_notification['key'] != '') {
                                                $this->sendPushNotification($driverVal->row()->push_notification['key'], $message, 'payment_paid', 'ANDROID', $options, 'DRIVER');
                                            }
                                        }
                                    }
                                    if ($driverVal->row()->push_notification['type'] == 'IOS') {
                                        if (isset($driverVal->row()->push_notification['key'])) {
                                            if ($driverVal->row()->push_notification['key'] != '') {
                                                $this->sendPushNotification($driverVal->row()->push_notification['key'], $message, 'payment_paid', 'IOS', $options, 'DRIVER');
                                            }
                                        }
                                    }
                                }
                            }
                        }
                    }
					$this->app_model->update_ride_amounts($ride_id);
					$fields = array(
						'ride_id' => (string) $ride_id
					);
					$url = base_url().'prepare-invoice';
					$this->load->library('curl');
					$output = $this->curl->simple_post($url, $fields);
                }
            }
            return "Success";
        } else {
            return "Error";
        }
    }

	/**
	* This function format the string
	* @param string $string
	**/
    public function format_string($string='',$langKey='',$txtForm=TRUE,$user_type='',$user_id=''){
		$this->tempLang = array();
		$returnString = $string;
		if($user_type != '' && $user_id != ''){
			$collection = '';
			if($user_type == 'user'){
				$collection = USERS;
			} else if ($user_type == 'driver'){
				$collection = DRIVERS;
			}
			if($collection != ''){
				$condition = array('_id' => MongoID($user_id));
				$selFields = array('lang_code');
				$userDetails = $this->app_model->get_selected_fields($collection, $condition, $selFields);								
				if($userDetails->num_rows() == 1){
					if(isset($userDetails->row()->lang_code) && $userDetails->row()->lang_code != ''){
						$dLangDataDB = $this->languageDataFromDb($userDetails->row()->lang_code);
						if ($dLangDataDB->num_rows() > 0) {
							if(isset($dLangDataDB->row()->key_values)){
								$tr = $dLangDataDB->row()->key_values;
								$this->tempLang = $tr;
								#$this->app_language = $userDetails->row()->lang_code;						
							}			
						}
					}
				}
			}
		}
		if (is_array($this->tempLang) && !empty($this->tempLang)) {
			if (array_key_exists($langKey,$this->tempLang)) {
				$str = ""; if(isset($this->tempLang[$langKey])) $str = $this->tempLang[$langKey];
				if($str!=''){
					if($this->app_language!='en'){
						$returnString = $str;
					}else{
						$returnString = (string)$str;
					}
				}else{
					$returnString = (string)$string;
				}
			} else {
				$returnString = (string)$string;
			}
		} else if (is_array($this->loadedLang) && !empty($this->loadedLang)) {
			if (array_key_exists($langKey,$this->loadedLang)) {
				$str = $this->loadedLang[$langKey];
				if($str!=''){
					if($this->app_language!='en'){
						$returnString = $str;
					}else{
						$returnString = (string)$str;
					}
				}
			} else {
				$returnString = (string)$string;
			}
		} else {
			$returnString = (string)$string;
		}
		if($txtForm==TRUE && $this->app_language=='en'){
			$returnString = (string) ucfirst(strtolower($returnString));
		}
		return $returnString;
	}

    public function languageConfiguration() {

        if ($this->uri->segment(1) != ADMIN_ENC_URL) {
            $defaultLanguage = $this->config->item('default_lang_code');
            $defaultLanguageName = $this->config->item('default_lang_name');
            if ($defaultLanguage == '') {
                $defaultLanguage = 'en';
                $defaultLanguageName = 'English';
            }
			$currentUrl = $this->uri->segment(1,0); 
			$currentPage = $this->uri->segment(2,0);
			$this->data['mlang'] = $mlang = $this->uri->segment(3,0);
            if (($this->session->userdata(APP_NAME.'langCode') != false && $this->session->userdata(APP_NAME.'langCode') != '') || ( isset($mlang) && $mlang != '' && $currentUrl == 'rider' && $currentPage == 'signup')) {
                $selectedLanguage = $this->session->userdata(APP_NAME.'langCode');
				if(isset($mlang) && $mlang != ''){
                    if($currentUrl == 'rider' && $currentPage == 'signup'){
                        $selectedLanguage = $mlang;
						$this->data['sideMenu'] = 'share_code';
                    }
                }
				
                $filePath = APPPATH . "language/" . $selectedLanguage . "/" . $selectedLanguage . "_lang.php";
				
				$pickLanguage = $this->user_model->get_all_details(LANGUAGES, array('lang_code' => $selectedLanguage));
				if($pickLanguage->num_rows()>0){
					$selectedLanguage = $pickLanguage->row()->lang_code;
					$selectedLanguageName = $pickLanguage->row()->name;
				}
				
                if (!(is_file($filePath))) {
                    $this->lang->load($defaultLanguage, $defaultLanguage);
					$selctedLang = array('langCode' => $defaultLanguage, 'langName' => $defaultLanguageName);
                } else {  
                    $this->lang->load($selectedLanguage, $selectedLanguage);
					$selctedLang = array('langCode' => $selectedLanguage, 'langName' => $selectedLanguageName);
                }
            } else {
                $selctedLang = array('langCode' => $defaultLanguage, 'langName' => $defaultLanguageName);
                $this->lang->load($defaultLanguage, $defaultLanguage);
            }
            $this->data['dLangCode'] = $defaultLanguage;
            $this->data['dLangName'] = $defaultLanguageName;
            $this->data['langCode'] = $selctedLang['langCode'];
            $this->data['langName'] = $selctedLang['langName'];
            $this->data['languageList'] = $languageList = $this->user_model->get_all_details(LANGUAGES, array('status' => 'Active'));
        }else if ($this->uri->segment(1) == ADMIN_ENC_URL) {
            $defaultLanguage = $this->config->item('default_lang_code');
            $defaultLanguageName = $this->config->item('default_lang_name');
            if ($defaultLanguage == '') {
                $defaultLanguage = 'en';
                $defaultLanguageName = 'English';
            }
			
			if ($this->session->userdata(APP_NAME.'admin_langCode') != false && $this->session->userdata(APP_NAME.'admin_langCode') != '') {
                $selectedLanguage = $this->session->userdata(APP_NAME.'admin_langCode');
                $filePath = APPPATH . "language/" . $selectedLanguage . "/" . $selectedLanguage . "_lang.php";
                $selctedLang = array('admin_langCode' => $this->session->userdata(APP_NAME.'admin_langCode'),
                    'langName' => $this->session->userdata(APP_NAME.'langName'));
                if (!(is_file($filePath))) {
                    $this->lang->load($defaultLanguage, $defaultLanguage);
                } else {  
                    $this->lang->load($selectedLanguage, $selectedLanguage);
                }
            } else {
                $selctedLang = array('admin_langCode' => $defaultLanguage, 'langName' => $defaultLanguageName);
                $this->lang->load($defaultLanguage, $defaultLanguage);
            }
			
			$this->data['langCode'] = $defaultLanguage;
		}else{
			$this->data['langCode'] = 'en';
		}
    }
	
    /* This function load data from the collection according to the default Language */

    public function languageDataFromDb($langPS="") {
		#echo $this->app_language;
		if($langPS==""){
			$langPS = $this->app_language;
		}
        $condition = array('language_code' => $langPS);
        $defaultLangDataFromDB = $this->user_model->get_all_details(MOBILE_LANGUAGES, $condition);

        if ($defaultLangDataFromDB->num_rows() < 1) {
            $condition = array('language_code' => 'en');
            $defaultLangDataFromDB = $this->user_model->get_all_details(MOBILE_LANGUAGES, $condition);
        }
        return $defaultLangDataFromDB;
    }

    /* This function load data from /web_service_content/response.json and convert all keys and values as singe Array */

    public function loadLanguageFromJSON() {
        $languagDirectory = 'web_service_content/';
        $get_english_lang_count = directory_map($languagDirectory);
        $json_content = @file_get_contents($languagDirectory . $get_english_lang_count[0]);
        $decoded = json_decode($json_content, TRUE);
        return $decoded;
    }
	
	
	/**  This function returns the currencies smallest units for stripe  **/
	public function get_stripe_currency_smallest_unit($amt=0.00,$currency='USD'){
		$zero_decimal_currencies = array('BIF','DJF','JPY','KRW','PYG','VND','XAF','XPF','CLP','GNF','KMF','MGA','RWF','VUV','XOF','bif','djf','jpy','krw','pyg','vnd','xaf','xpf','clp','gnf','kmf','mga','rwf','vuv','xof');
		if(in_array($currency,$zero_decimal_currencies)){
			return $amt;
		} else {
			return $amt * 100;
		}
	}
	
	/**
    *
    * retrieve saved cards details in stripe account 
    *
    * */

	function get_stripe_card_details($customerId = ''){ 
		$resultVals = array(); 
		$errStatus = '1';
		if($customerId != ''){
			require_once('./stripe/lib/Stripe.php');
			$stripe_settings = $this->data['stripe_settings'];
			$secret_key = $stripe_settings['settings']['secret_key'];
			$publishable_key = $stripe_settings['settings']['publishable_key']; 
			Stripe::setApiKey($secret_key);
			try {
				$card_output = Stripe_Customer::retrieve($customerId)->sources->all(array('limit'=>100, 'object' => 'card'));
				foreach($card_output['data'] as $cards){ 
					$cardType = 'card';
					if($cards['funding'] != '' && $cards['funding'] != 'unknown') $cardType = $cards['funding'].' card';
					$resultVals[] = array('card_number' => (string)'**** **** **** '.$cards['last4'],
										  'exp_month' => (string)$cards['exp_month'],
										  'exp_year' => (string)$cards['exp_year'],
										  'card_type' => (string)$cards['brand'].' '.$cardType,
										  'customer_id' => (string)$cards['customer'],
										  'card_id' => (string)$cards['id']
										  );
				}
				if(!empty($resultVals)){
					return $response = array('error_status' => '1','result' => $resultVals);
				}else{
					return $response = array('error_status' => '0','result' => 'No cards found');
				}
			} catch (Exception $e) {
                $resultVals = $e->getMessage();
                if ($resultVals == '') {
                    $resultVals = 'Network error please try again';
                }
                $errStatus = '0';
            }
			return $response = array('error_status' => $errStatus,'result' => $resultVals);
		} else {
			return $response = array('error_status' => '0','result' => 'Customer id is empty');
		}
	}
	
	
	function cal_distance_from_positions($latitudeFrom=0.00, $longitudeFrom=0.00, $latitudeTo=0.00, $longitudeTo=0.00, $earthRadius = 3959){
		// convert from degrees to radians
		$latFrom = deg2rad($latitudeFrom);
		$lonFrom = deg2rad($longitudeFrom);
		$latTo = deg2rad($latitudeTo);
		$lonTo = deg2rad($longitudeTo);

		$latDelta = $latTo - $latFrom;
		$lonDelta = $lonTo - $lonFrom;

		$angle = 2 * asin(sqrt(pow(sin($latDelta / 2), 2) +
		cos($latFrom) * cos($latTo) * pow(sin($lonDelta / 2), 2)));
		return $angle * $earthRadius * 1.609344;
	}
	 /**  This function returns the support currencies for stripe**/	
	public function get_stripe_provide_currency($currency){       
		 $currency_code=strtolower($currency);		
		  $stripe_currency = array('usd','aed','afn','all','amd','ang','aoa','ars','aud','awg','azn','bam','bbd','bdt','bgn','bif','bmd','bnd','bob','brl','bsd','bwp','bzd','cad','cdf','chf','clp','cny','cop','crc','cve','czk','djf','dkk','dop','dzd','egp','etb','eur','fjd','fkp','gbp','gel','gip','gmd','gnf','gtq','gyd','hkd','hnl','hrk','htg','huf','idr','ils','inr','isk','jmd','jpy','kes','kgs','khr','kmf','krw','kyd','kzt','lak','lbp','lkr','lrd','lsl','ltl','mad','mdl','mga','mkd','mnt','mop','mro','mur','mvr','mwk','mxn','myr','mzn','nad','ngn','nio','nok','npr','nzd','pab','pen','pgk','php','pkr','pln','pyg','qar','ron','rsd','rub','rwf','sar','sbd','scr','sek','sgd','shp','sll','sos','srd','std','svc','szl','thb','tjs','top','try','ttd','twd','tzs','uah','ugx','uyu','uzs','vnd','vuv','wst','xaf','xcd','xof','xpf','yer','zar','zmw','eek','lvl','vef');		
		  if(in_array($currency_code,$stripe_currency)){		
			return true;		
		  } else {    
		   return false;        
		  }
	}
	
		
	/***
	*  This function updates the log file
	***/
	public function write_into_log_file($action_type,$msg=''){
		$IPaddress = $this->input->ip_address(); 
		$admin_name = $this->checkLogin('N');
		@require_once("./log/log_methods.php"); 
		$segmentArr = $this->uri->segment_array();
		$management_name = strtoupper(str_replace('_',' ',$segmentArr[2]));
		$class_name = $segmentArr[2];
		$function_name = ''; if(isset($segmentArr[3]))  $function_name = $segmentArr[3];
		$optional_value = ''; if(isset($segmentArr[4])) $optional_value = $segmentArr[4];
		$message  = ''; 
		if(isset($methodsArr[$function_name]))  $message = $methodsArr[$function_name]; 
		if($function_name ==  'change_password'){
			if(isset($methodsArr[$function_name.'_'.$class_name]))  $message = $methodsArr[$function_name.'_'.$class_name];
		} 
		if($function_name ==  'update_language_content'){
			$message = $management_name.' '.$message;
		} 
		$log_time = date('Y-m-d h:i:s A'); 
		if(substr($function_name,0,10)  == 'insertEdit'){
			$getLastURL= str_replace(base_url(),'',$_SERVER['HTTP_REFERER']);
			$last_uriArr =array_filter(@explode('/',$getLastURL)); 
			if(count($last_uriArr) >= 4){
				$message = str_replace('Added/Edited','Edited',$message); 
			} else {
				$message = str_replace('Added/Edited','Added',$message); 
			}
		}
		if(substr($function_name,-13)  == 'status_global'){
			if($_POST['statusMode'] == 'Delete')
				$message = str_replace('Modified/Deleted','Deleted',$message);
			else 
				$message = str_replace('Modified/Deleted','Modified',$message);
		}
		if($action_type == 'success') $action_status = 'SUCCESS'; else $action_status = 'FAILED';
		$errMsg = '';
		if($action_type == 'error'){
			$errMsg  = ",[ERROR:$msg]";
		}
		$message = ucfirst(strtolower($message));
		if($message != ''){
			$log_content = "[$log_time][$management_name][$message][$action_status][Admin Name:$admin_name]".$errMsg."[IP:$IPaddress],".PHP_EOL;
			file_put_contents('./log/logs/'.date("Y-m-d").'.txt', $log_content, FILE_APPEND); #die;
		}
	}
    
}


?>