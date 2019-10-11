<?php
if (!defined('BASEPATH'))
    exit('No direct script access allowed');

/**
 * 
 * Site Android & Ios language conversion page related functions
 * @author Casperon
 *
 * */
class Lang_converter extends MY_Controller {
	
	function __construct() {
        parent::__construct();
        $this->load->helper(array('cookie', 'date', 'form', 'email'));
        $this->load->library(array('encrypt', 'form_validation'));
        $this->load->model('user_model');
    }
	
	function index(){
        $this->data['heading'] = $this->config->item('email_title').' - Language Conversion';
		
		$appCode = $this->input->get('App');
		$lang_result = array();
		if($appCode != ''){
			if($appCode == 'AU' || $appCode == 'AP' || $appCode == 'IU' || $appCode == 'IP'){   
				if($appCode == 'AU' || $appCode == 'AP'){
					$langFilePath = FCPATH.'app_langs/android-'.$appCode.'.xml';
				} else {
					$langFilePath = FCPATH.'app_langs/ios-'.$appCode.'.xml';
				} 
				if(file_exists($langFilePath)){
					$fileCnt = file_get_contents($langFilePath); 
					if($appCode == 'AU' || $appCode == 'AP' || $appCode == 'IU' || $appCode == 'IP'){
						header("Content-Type: text/plain");
						$parse = xml_parser_create();
						xml_parse_into_struct($parse, $fileCnt, $lang_vals, $index);
						xml_parser_free($parse);
						foreach($lang_vals as $lang){
							if(isset($lang['attributes']['NAME'])){
								if($lang['attributes']['NAME'] != ''){
									$lang_key = $lang['attributes']['NAME'];
									$lang_result[$lang_key] = $lang['value'];
								}
							}
						}
					}
					/*  else if($appCode == 'IU' || $appCode == 'IP'){
						$ioslangArr = @explode(';',$fileCnt);
						$ioslangArr = array_unique($ioslangArr);
						$i=0;
						foreach($ioslangArr as $iosLang){  $i++;
							if($iosLang != ''){
								$iosLang = str_replace(array('"','”','“'),'',$iosLang);
								$iosLangArr = @explode('=',trim($iosLang));
								$lkey = trim(url_title($iosLangArr[0],'_',TRUE)); 
								if(isset($iosLangArr[1])) $lval = trim($iosLangArr[1]); else $lval='';
								
								if($lval != '' && $lkey != ''){
									$lang_result[$lkey] = $lval;
								}
							}
						} 
					} */
					
				} else {
					$this->setErrorMessage('error', 'Language key file not found');
					redirect('convert-lang'); 
				}
			} else {
				$this->setErrorMessage('error', 'Invalid Language Key');
				redirect('convert-lang');
			}	
		} 
		$this->data['lang_result'] = $lang_result;
		
		header("Content-Type: text/html");
        $this->load->view('site/lang/lang_converter', $this->data);
	}
	
	/**
	* This function downloads the language .xml file for android
	***/
	
	public function download_android_xml_lang(){
		$langArr = $_POST['key']; 
		$app_type = $this->input->post('app_type');
		$file_name = '';
		if($app_type == 'AU'){
			$file_name = strtolower($this->config->item('email_title')).'-user-app-lang-'.time().'.xml';
		} else if($app_type == 'AP'){
			$file_name = strtolower($this->config->item('email_title')).'-provider-app-lang-'.time().'.xml';
		}
		
		if($file_name != ''){
			$xmlCnt = '<resources>';
			foreach($langArr as $key => $lang){
				if($lang != ''){
					$xmlCnt.='<string name="'.$key.'">'.$lang.'</string>';
				}
			}
			$xmlCnt.= '</resources>';
			header("Content-Type: text/xml encoding='utf-8'");
			header('Content-Disposition: attachment; filename="'.basename($file_name).'"');
			header('Content-Transfer-Encoding: binary');
			header('Connection: close');
			echo $xmlCnt; die;
		} else {
			$this->setErrorMessage('error', 'App language type is not defined');
			redirect('convert-lang');
		}
	}
	
	/**
	* This function downloads the language .rtf file for IOS
	***/
	
	public function download_ios_rtf_lang(){ #echo $this->input->post('app_type').'<pre>'; print_r($_POST); die;
		$langArr = $_POST['key']; 
		$app_type = $this->input->post('app_type');
		$file_name = '';
		if($app_type == 'IU'){
			$file_name = strtolower($this->config->item('email_title')).'-user-app-ios-lang-'.time().'.xml';
		} else if($app_type == 'IP'){
			$file_name = strtolower($this->config->item('email_title')).'-provider-app-ios-lang-'.time().'.xml';
		}
		
		if($file_name != ''){
			$xmlCnt = '<resources>';
			foreach($langArr as $key => $lang){
				if($lang != ''){
					$xmlCnt.='<string name="'.$key.'">'.$lang.'</string>';
				}
			}
			$xmlCnt.= '</resources>';
			header("Content-Type: text/xml encoding='utf-8'");
			header('Content-Disposition: attachment; filename="'.basename($file_name).'"');
			header('Content-Transfer-Encoding: binary');
			header('Connection: close');
			echo $xmlCnt; die;
		} else {
			$this->setErrorMessage('error', 'App language type is not defined');
			redirect('convert-lang');
		}
	}

}


/* End of file landing.php */
/* Location: ./application/controllers/site/landing.php */