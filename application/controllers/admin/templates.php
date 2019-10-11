<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

/**
*
*	Templates
* 
*	@package		CI
*	@subpackage		Controller
*	@author			Casperon
*
**/

class Templates extends MY_Controller {
	public function __construct(){
        parent::__construct();
		$this->load->helper(array('cookie','date','form'));
		$this->load->library(array('encrypt','form_validation'));		
		$this->load->model(array('templates_model','admin_model'));
		
		if ($this->checkPrivileges('templates',$this->privStatus) == FALSE){
			redirect(ADMIN_ENC_URL);
		}
		$c_fun = $this->uri->segment(3);
		$restricted_function = array('insertEditEmailtemplate','delete_email_template');
		if(in_array($c_fun,$restricted_function) && $this->data['isDemo']===TRUE){
			$this->setErrorMessage('error','You are in demo version. you can\'t do this action.','admin_common_demo_version');
			redirect($_SERVER['HTTP_REFERER']); die;
		}
    }
    
	/**
	*
	* To redirect the email template page
	* 	
	* @Initiate HTML to Redirect email template page
	*	
	**/	
   	public function index(){	
		if ($this->checkLogin('A') == ''){
			redirect(ADMIN_ENC_URL);
		}else {
			redirect(ADMIN_ENC_URL.'/templates/display_email_template');
		}
	}
	
	/**
	* 
	* To list the email template in admin panel
	*
	* @return HTML to show the list of email templates
	*
	**/	
	public function display_email_template(){
		if ($this->checkLogin('A') == ''){
			redirect(ADMIN_ENC_URL);
		}else {
		    if ($this->lang->line('admin_menu_email_template_list') != '') 
		    $this->data['heading']= stripslashes($this->lang->line('admin_menu_email_template_list')); 
		    else  $this->data['heading'] = 'Email Template List';
			$condition = array();
			$this->data['templateList'] = $this->templates_model->get_all_details(NEWSLETTER,$condition,array('news_id'=>'asc'));
			$this->load->view(ADMIN_ENC_URL.'/newsletter/display_emailtemplates',$this->data);
		}
	}
	
	/**
	* 
	* To show email template form for create a new email template
	*
	* @return the HTML to show the email template form
	*
	**/	
	public function add_email_template(){
		if ($this->checkLogin('A') == ''){
			redirect(ADMIN_ENC_URL);
		}else {
		  if ($this->lang->line('admin_templates_add_email_template') != '') 
		    $this->data['heading']= stripslashes($this->lang->line('admin_templates_add_email_template')); 
		    else  $this->data['heading'] = 'Add Email Template';

		
			$this->load->view(ADMIN_ENC_URL.'/newsletter/add_email_template',$this->data);
		}
	}
	
	/**
	* 
	* To show edit the email template form
	*
	* @param string $email_id template MongoDB\BSON\ObjectId
	* @return HTML to show edit the template form
	*
	**/	
	public function edit_email_template_form(){
		if ($this->checkLogin('A') == ''){
			redirect(ADMIN_ENC_URL);
		}else {
		    if ($this->lang->line('admin_menu_edit_email_template') != '') 
		    $this->data['heading']= stripslashes($this->lang->line('admin_menu_edit_email_template')); 
		    else  $this->data['heading'] = 'Edit Email Template';
			$email_id = $this->uri->segment(4,0);
            $this->data['template_id']=$email_id;
            $this->data['language_code'] = $language_code = $this->uri->segment(5, 0);
            $condition = array('_id' => MongoID($email_id));
				$added_languages = $this->templates_model->get_selected_fields(NEWSLETTER, $condition, array('translated_languages'))->row();
                if (isset($added_languages->translated_languages)) {
                    $this->data['translated_languages'] = $added_languages->translated_languages;
                }
            $condition = array('_id' =>  MongoID($email_id));
            $this->data['langList'] = $this->templates_model->get_selected_fields(LANGUAGES, array(), array('name', 'lang_code'))->result();
			$this->data['template_details'] = $this->templates_model->get_all_details(NEWSLETTER,$condition);
			if ($this->data['template_details']->num_rows() == 1){
				$this->load->view(ADMIN_ENC_URL.'/newsletter/edit_email_template',$this->data);
			}else {
				redirect(ADMIN_ENC_URL);
			}
		}
	}
	
	/**
	* 
	* To add or update the email template fields
	* 
	* @param string $template_id Template id MongoDB\BSON\ObjectId
	* @param string $lang_code Language code
	* @param string $post_message Optional, for email template description.
	* @redirect HTTP request to show the list of email templates page
	*
	**/	
	public function insertEditEmailtemplate(){
		if ($this->checkLogin('A') == ''){
			redirect(ADMIN_ENC_URL);
		}else {
			$template_id = $this->input->post('_id');
			$lang_code = $this->input->post('lang_code');
        
			$excludeArr = array("_id","status");
			$etemplate_status = 'Active';
			$dataArr = array();
				
			$getTemplates=$this->templates_model->get_selected_fields(NEWSLETTER,array(),array('news_id'),array('news_id'=>'DESC'));
			if ($template_id == ''){
				$nid = $getTemplates->row()->news_id;
				$news_id = $nid+1;
				$dataArr = array(
                        'news_id'=>floatval($news_id),
                        'status' => $etemplate_status,
                        'created_date'=>date('Y-m-d H:i:s')
					);
			}else{
				$condition = array('_id' =>  MongoID($template_id));
				$template_contentOld=$this->templates_model->get_selected_fields(NEWSLETTER,$condition,array('message.description','news_id'));
				if($template_contentOld->num_rows()>0){
					$news_id=(string)$template_contentOld->row()->news_id;
				}
				
			}
			
			if($lang_code=='' || $lang_code=='0'){
				$post_message = $this->input->post('message',FALSE);
				$org_description_content = $post_message['message']['description'];
				$temp_description_content = str_replace("'.base_url().'", base_url(), $post_message['message']['description']);
				
				$dataArr['message']['title'] = $post_message['message']['title'];
				$dataArr['message']['subject'] = $post_message['message']['subject'];
				$dataArr['message']['description'] = $org_description_content;
				
				$file = 'newsletter/template'.$news_id.'.php';
				
			}else{
				$post_message = $this->input->post("$lang_code",FALSE);
				$org_description_content = $post_message["$lang_code"]['email_description'];
				$temp_description_content = str_replace("'.base_url().'", base_url(), $post_message["$lang_code"]['email_description']);
				
				$dataArr["$lang_code"]['email_title'] = $post_message["$lang_code"]['email_title'];
				$dataArr["$lang_code"]['email_subject'] = $post_message["$lang_code"]['email_subject'];
				$dataArr["$lang_code"]['email_description'] = $org_description_content;
				
				$file = 'newsletter/template'.$news_id.'_'.$lang_code.'.php';
			}
			
			if ($template_id == ''){
				$condition = array();
				$this->templates_model->commonInsertUpdate(NEWSLETTER,'insert',$excludeArr,$dataArr,$condition);
				
				$template_content_new = str_replace("{","'.",addslashes($temp_description_content));
				$temp_description_string = str_replace("}",".'",$template_content_new);
				
				$config = "<?php \$message .= '";
				$config .= "$temp_description_string";
				$config .= "';  ?>";
				file_put_contents($file, $config);
				$this->setErrorMessage('success','Email template added successfully','admin_template_email_added_success');
			}else {
				if($lang_code!='' && $lang_code!='0'){
					$condition = array('_id' =>  MongoID($template_id));
					$added_languages = $this->templates_model->get_selected_fields(NEWSLETTER, $condition, array('translated_languages'))->row();

					if (isset($added_languages->translated_languages)) {
						foreach ($added_languages->translated_languages as $added) {
							$translated[] = $added;
						}
						if (!in_array($lang_code, $translated)){
							$translated[] = $lang_code;
						}
						$translated_langs = array('translated_languages' => $translated);
						$dataArr = array_merge($dataArr, $translated_langs);
					}else {
						$translated[] = $lang_code;
						$translated_langs = array('translated_languages' => $translated);
						$dataArr = array_merge($dataArr, $translated_langs);
					}
					
					$dataArr = array_merge(array('news_id'=>floatval($news_id)),$dataArr);
					$this->templates_model->commonInsertUpdate(NEWSLETTER,'update',$excludeArr,$dataArr,$condition);
				
					$template_content_new = str_replace("{","'.",addslashes($temp_description_content));
					$temp_description_string = str_replace("}",".'",$template_content_new);
					
					$config = "<?php \$message .= '";
					$config .= "$temp_description_string";
					$config .= "';  ?>";
					file_put_contents($file, $config);
					$this->setErrorMessage('success','Email template updated successfully','admin_tempalte_email_update_success');
				}else{
					$condition = array('_id' =>  MongoID($template_id));
					$dataArr = array_merge(array('news_id'=>floatval($news_id)),$dataArr);
					$this->templates_model->commonInsertUpdate(NEWSLETTER,'update',$excludeArr,$dataArr,$condition);
					
					$template_content_new = str_replace("{","'.",addslashes($temp_description_content));
					$temp_description_string = str_replace("}",".'",$template_content_new);
				
					$config = "<?php \$message .= '";
					$config .= "$temp_description_string";
					$config .= "';  ?>";
					file_put_contents($file, $config);
					$this->setErrorMessage('success','Email template updated successfully','admin_tempalte_email_update_success');
				}
			}
			redirect(ADMIN_ENC_URL.'/templates/display_email_template');
		}
	}
	
	/**
	* 
	* To show the particular email template details
	* 
	* @param string $template_id Template id MongoDB\BSON\ObjectId
	* @return HTML to list the particular email template
	*
	**/	
	public function view_email_template(){
		if ($this->checkLogin('A') == ''){
			redirect(ADMIN_ENC_URL);
		}else {
		    if ($this->lang->line('admin_menu_view_email_temp') != '') 
		    $this->data['heading']= stripslashes($this->lang->line('admin_menu_view_email_temp')); 
		    else  $this->data['heading'] = 'View Email Template';
			$template_id = $this->uri->segment(4,0);
			$condition = array('_id' =>  MongoID($template_id));
			$this->data['template_details'] = $this->templates_model->get_all_details(NEWSLETTER,$condition);
			if ($this->data['template_details']->num_rows() == 1){
				$this->load->view(ADMIN_ENC_URL.'/newsletter/view_email_template',$this->data);
			}else {
				redirect(ADMIN_ENC_URL);
			}
		}
	}
    
	
	/**
	* 
	* Delete the record for particular email template
	* 
	* @param string $template_id Template id MongoDB\BSON\ObjectId
	* @return HTML to list the particular email template
	*
	**/	
	public function delete_email_template(){
		if ($this->checkLogin('A') == ''){
			redirect(ADMIN_ENC_URL);
		}else {
			$email_id = $this->uri->segment(4,0);
			$condition = array('_id' =>  MongoID($email_id));
			 $this->templates_model->commonDelete(NEWSLETTER,$condition);
			$this->setErrorMessage('success','Email template deleted successfully','admin_templete_email_delete_success');
			redirect(ADMIN_ENC_URL.'/templates/display_email_template');
		}
	}
    
   
	/**
	* 
	* To show the subscribers list in admin panel
	*
	* @return HTML to show the subscribers lists
	*
	**/		
	public function display_subscribers_list(){
		if ($this->checkLogin('A') == ''){
			redirect(ADMIN_ENC_URL);
		}else {
		    if ($this->lang->line('admin_templates_subscribers_list') != '') 
		    $this->data['heading']= stripslashes($this->lang->line('admin_templates_subscribers_list')); 
		    else  $this->data['heading'] = 'Subscribers List';
			$condition = array();
			$this->data['subscribersList'] = $this->templates_model->get_all_details(NEWSLETTER_SUBSCRIBER,$condition);
			$this->data['NewsList'] = $this->templates_model->get_all_details(NEWSLETTER,$condition);
			$this->load->view(ADMIN_ENC_URL.'/newsletter/display_subscribers',$this->data);
		}
	}
	
	/**
	* 
	* To change the status
	* 
	* @param string $status Mode Active/InActive
	* @param string $user_id User MongoDB\BSON\ObjectId
	* @return HTTP request to show the subscribers lists
	*
	**/	
	public function change_subscribers_status(){
		if ($this->checkLogin('A') == ''){
			redirect(ADMIN_ENC_URL);
		}else {
			$mode = $this->uri->segment(4,0);
			$user_id = $this->uri->segment(5,0);
			$status = ($mode == '0')?'InActive':'Active';
			$newdata = array('status' => $status);
			$condition = array('_id' => MongoID($user_id));
			$this->templates_model->update_details(NEWSLETTER_SUBSCRIBER,$newdata,$condition);
			$this->setErrorMessage('success','Subscribers Status Changed Successfully','admin_template_subcriber_status');
			redirect(ADMIN_ENC_URL.'/templates/display_subscribers_list');
		}
	}
	


	/**
	* 
	* Delete the record for particular subscriber
	* 
	* @param string $user_id User MongoDB\BSON\ObjectId
	* @redirect HTTP request to show the subscribers lists
	*
	**/
	public function delete_subscribers(){
		if ($this->checkLogin('A') == ''){
			redirect(ADMIN_ENC_URL);
		}else {
			$user_id = $this->uri->segment(4,0);
			$condition = array('_id' => MongoID($user_id));
			$this->templates_model->commonDelete(NEWSLETTER_SUBSCRIBER,$condition);
			$this->setErrorMessage('success','Subscribers deleted successfully','admin_template_subcriber_delate');
			redirect(ADMIN_ENC_URL.'/templates/display_subscribers_list');
		}
	}
	/**
	* 
	* To change the newsletter status bulk
	* 
	* @param string $statusMode Mail status mode
	* @param string $mail_contents mail body in html
	* @return HTTP redirect to the display subscriber page
	*
	**/
	
	public function change_newsletter_status_global(){
		if($this->input->post('statusMode')=='SendMail' &&  $this->input->post('mail_contents')!=''){
			if(count($_POST['checkbox_id']) > 0){
				$data =  $_POST['checkbox_id'];
				for ($i=0;$i<count($data);$i++){
					if($data[$i] == 'on'){
						unset($data[$i]);
					}
				}
				
				$SubscribEmail=$this->templates_model->send_mail_subcribers($data);
				
				$emailtemplate_id = $this->input->post('mail_contents');
				$condition1 = array('news_id' =>floatval($emailtemplate_id));
				$NewsTemplate= $this->templates_model->get_all_details(NEWSLETTER,$condition1);
				
				
				$this->templates_model->send_mail_subcribers_list($SubscribEmail, $NewsTemplate);
				$this->setErrorMessage('success'," Send Mail's successfully",'admin_common_mail_send_success');
				redirect(ADMIN_ENC_URL.'/templates/display_subscribers_list');
			}else{
				$this->setErrorMessage('error'," Email Not Send",'admin_common_mail_send_error');
				redirect(ADMIN_ENC_URL.'/templates/display_subscribers_list');
			}
		}else if($this->input->post('statusMode')=='SendMailAll' &&  $this->input->post('mail_contents')!=''){
			$conditionval = array();
			$SubscribEmail=$this->templates_model->get_newsletter_details(NEWSLETTER_SUBSCRIBER,$conditionval);
			$emailtemplate_id = $this->input->post('mail_contents');
			$condition1 = array('news_id' =>floatval($emailtemplate_id));
			$NewsTemplate= $this->templates_model->get_all_details(NEWSLETTER,$condition1);
			$this->templates_model->send_mail_subcribers_list($SubscribEmail, $NewsTemplate);
			$this->setErrorMessage('success',"Email sent successfully",'admin_common_mail_sendt_success');
			redirect(ADMIN_ENC_URL.'/templates/display_subscribers_list');
		}else{
			if(count($this->input->post('checkbox_id')) > 0 &&  $this->input->post('statusMode') != ''){
				$this->templates_model->activeInactiveCommon(NEWSLETTER_SUBSCRIBER,'_id');
				if (strtolower($this->input->post('statusMode')) == 'delete'){
					$this->setErrorMessage('success','Subscribers records deleted successfully','admin_template_subcrib_record_deleted');
				}else {
					$this->setErrorMessage('success','Subscribers records status changed successfully','admin_template_subcrib_status');
				}
				redirect(ADMIN_ENC_URL.'/templates/display_subscribers_list');
			}
		}
	}
	
	/**
	* 
	* To show invoice template page
	*
	* @param string $language_code Language code
	* @return HTML to show invoice template page
	*
	**/		
	
	public function invoice_template(){
		if ($this->checkLogin('A') == ''){
			redirect(ADMIN_ENC_URL);
		}else {
			 
			$this->data['language_code'] = $language_code = $this->uri->segment(4, 0);
			$condition = array();
			/* Sending all the languages that has been already translated */
			$added_languages = $this->templates_model->get_selected_fields(INVOICE, $condition, array('translated_languages'))->row();
			if (isset($added_languages->translated_languages)) {
				$this->data['translated_languages'] = $added_languages->translated_languages;
			}
			/* Sending all ---> Ends heree... */
			$condition = array();
			$this->data['langList'] = $this->templates_model->get_selected_fields(LANGUAGES, array(), array('name', 'lang_code'))->result();
			$this->data['template_details'] = $this->templates_model->get_all_details(INVOICE,$condition);
			
			
             if ($this->lang->line('invoice_template_lang') != '') {
				 $heading= stripslashes($this->lang->line('invoice_template_lang')); 
			 }else{
				$heading = 'Invoice Template';
			 }
			 $this->data['heading'] = $heading;
			 
			if ($this->data['template_details']->num_rows() == 1){
				$this->load->view(ADMIN_ENC_URL.'/newsletter/invoice_template',$this->data);
			}else {
				$this->load->view(ADMIN_ENC_URL.'/newsletter/invoice_template',$this->data);
			}				
		}	
	}
	
	/**
	* 
	* To add or update the invoice template
	* 
	* @param string $template_id Template id MongoDB\BSON\ObjectId
	* @param string $lang_code Language code
	* @param string $post_message Optional,for invoice template description
	* @redirect http request to show the invoice template
	*
	**/		
	public function insertEditInvoicetemplate(){
		if ($this->checkLogin('A') == ''){
			redirect(ADMIN_ENC_URL);
		}else {
        
			$template_id = $this->input->post('_id');
			$lang_code = $this->input->post('lang_code');
       
			$excludeArr = array("_id","status","lang_code");
			$etemplate_status = 'Active';
			$dataArr = array();

			if ($template_id == ''){
				$dataArr = array(
					'status' => $etemplate_status,
					'created_date'=>date('Y-m-d H:i:s')
					);
			} 
			
			if($lang_code=='' || $lang_code=='0'){
				$post_message = $this->input->post('message',FALSE);
				$org_description_content = $post_message['message']['description'];
				$temp_description_content = str_replace("'.base_url().'", base_url(), $post_message['message']['description']);
				
				$dataArr['message']['title'] = $post_message['message']['title'];
				$dataArr['message']['subject'] = $post_message['message']['subject'];
				$dataArr['message']['description'] = $org_description_content;
				
				$file = 'invoice/invoice_template.php';
				
			}else{
				$post_message = $this->input->post("$lang_code",FALSE);
				$org_description_content = $post_message["$lang_code"]['email_description'];
				$temp_description_content = str_replace("'.base_url().'", base_url(), $post_message["$lang_code"]['email_description']);
				
				$dataArr["$lang_code"]['email_title'] = $post_message["$lang_code"]['email_title'];
				$dataArr["$lang_code"]['email_subject'] = $post_message["$lang_code"]['email_subject'];
				$dataArr["$lang_code"]['email_description'] = $org_description_content;
				
				$file = 'invoice/invoice_template_'.$lang_code.'.php';
			}
			/**
			*  css style replacement section
			*/ 
				$i = 0;
				$css_cnt_arr = array();
				$tagname = 'style';
				$pattern = "#<\s*?$tagname\b[^>]*>(.*?)</$tagname\b[^>]*>#s";
				while(preg_match($pattern, $temp_description_content, $matches)){
					$css_cnt = $matches[0];
					$styleMasker = "[STYLE_MASK_".time()."_".$i."]";
					$temp_description_content = str_replace($css_cnt,$styleMasker,$temp_description_content);
					$i++;
					$css_cnt_arr[$styleMasker] = $css_cnt;
				}
			/**
			*  css style replacement section
			*/ 
			
			if ($template_id == ''){
				$condition = array();
				$this->templates_model->commonInsertUpdate(INVOICE,'insert',$excludeArr,$dataArr,$condition);
				
				$template_content_new = str_replace("{","'.",addslashes($temp_description_content));
				$temp_description_string = str_replace("}",".'",$template_content_new);
				
				/******************** CSS Style Code Replacement ********************/
                foreach($css_cnt_arr as $mask => $css ){
                    $css = addslashes($css);
                    $temp_description_string = str_replace($mask,$css,$temp_description_string); 
                }
                /******************** CSS Style Code Replacement ********************/
				
				$config = "<?php \$message .= '";
				$config .= "$temp_description_string";
				$config .= "';  ?>";
				
				file_put_contents($file, $config);
				$this->setErrorMessage('success','Email template added successfully','admin_template_email_added_success');
			}else {
				if($lang_code!='' && $lang_code!='0'){
					$condition = array('_id' =>  MongoID($template_id));
					$added_languages = $this->templates_model->get_selected_fields(INVOICE, $condition, array('translated_languages'))->row();

					if (isset($added_languages->translated_languages)) {
						foreach ($added_languages->translated_languages as $added) {
							$translated[] = $added;
						}
						if (!in_array($lang_code, $translated)){
							$translated[] = $lang_code;
						}	
						$translated_langs = array('translated_languages' => $translated);
						$dataArr = array_merge($dataArr, $translated_langs);
					}else {
						$translated[] = $lang_code;
						$translated_langs = array('translated_languages' => $translated);
						$dataArr = array_merge($dataArr, $translated_langs);
					}
					
					$this->templates_model->commonInsertUpdate(INVOICE,'update',$excludeArr,$dataArr,	$condition);
					
					$template_content_new = str_replace("{","'.",addslashes($temp_description_content));
					$temp_description_string = str_replace("}",".'",$template_content_new);
					
					/******************** CSS Style Code Replacement ********************/
						foreach($css_cnt_arr as $mask => $css ){
							$css = addslashes($css);
							$temp_description_string = str_replace($mask,$css,$temp_description_string); 
						}
					/******************** CSS Style Code Replacement ********************/
					
					$config = "<?php \$message .= '";
					$config .= "$temp_description_string";
					$config .= "';  ?>";
					file_put_contents($file, $config);
					$this->setErrorMessage('success','Email template updated successfully','admin_tempalte_email_update_success');
				}else{
					$condition = array('_id' =>  MongoID($template_id));
					
					$this->templates_model->commonInsertUpdate(INVOICE,'update',$excludeArr,$dataArr,$condition);
					
					$template_content_new = str_replace("{","'.",addslashes($temp_description_content));
					$temp_description_string = str_replace("}",".'",$template_content_new);
					
					/******************** CSS Style Code Replacement ********************/
					foreach($css_cnt_arr as $mask => $css ){
						$css = addslashes($css);
						$temp_description_string = str_replace($mask,$css,$temp_description_string); 
					}
					/******************** CSS Style Code Replacement ********************/
					$config = "<?php \$message .= '";
					$config .= "$temp_description_string";
					$config .= "';  ?>";
					file_put_contents($file, $config);
					$this->setErrorMessage('success','Email template updated successfully','admin_tempalte_email_update_success');
				}
			}
			redirect(ADMIN_ENC_URL.'/templates/invoice_template');
		}
	}
	
  	/**
	* 
	* To show the share pool invoice template page
	*
	* @return HTML to show the share pool invoice template page
	*
	**/	
    
    public function share_pool_invoice_template(){
		if ($this->checkLogin('A') == ''){
			redirect(ADMIN_ENC_URL);
		}else {
			 
			$this->data['language_code'] = $language_code = $this->uri->segment(4, 0);
			$condition = array();
			/* Sending all the languages that has been already translated */
			$added_languages = $this->templates_model->get_selected_fields(SHARE_POOL_INVOICE, $condition, array('translated_languages'))->row();
			if (isset($added_languages->translated_languages)) {
				$this->data['translated_languages'] = $added_languages->translated_languages;
			}
			/* Sending all ---> Ends heree..... */
			$condition = array();
			$this->data['langList'] = $this->templates_model->get_selected_fields(LANGUAGES, array(), array('name', 'lang_code'))->result();
			$this->data['template_details'] = $this->templates_model->get_all_details(SHARE_POOL_INVOICE,$condition);
			
			
             if ($this->lang->line('admin_share_pool_invoice_template_lang') != '') {
				 $heading= stripslashes($this->lang->line('admin_share_pool_invoice_template_lang')); 
			 }else{
				$heading = 'Share Pool Invoice Template';
			 }
			 $this->data['heading'] = $heading;
			 
			if ($this->data['template_details']->num_rows() == 1){
				$this->load->view(ADMIN_ENC_URL.'/newsletter/share_pool_invoice_template',$this->data);
			}else {
				$this->load->view(ADMIN_ENC_URL.'/newsletter/share_pool_invoice_template',$this->data);
			}				
		}	
	}
	
	/**
	* 
	* To add or update the share pool invoice template
	* 
	* @param string $template_id Template id MongoDB\BSON\ObjectId
	* @param string $lang_code Language code
	* @param string $post_message Optional,for share pool invoice template description.
	* @redirect HTML to show the share pool invoice template
	*
	**/	
	public function insertEditSharePoolInvoicetemplate(){
		if ($this->checkLogin('A') == ''){
			redirect(ADMIN_ENC_URL);
		}else {
        
			$template_id = $this->input->post('_id');
			$lang_code = $this->input->post('lang_code');
       
			$excludeArr = array("_id","status","lang_code");
			$etemplate_status = 'Active';
			$dataArr = array();

			if ($template_id == ''){
				$dataArr = array(
					'status' => $etemplate_status,
					'created_date'=>date('Y-m-d H:i:s')
					);
			} 
			
			if($lang_code=='' || $lang_code=='0'){
				$post_message = $this->input->post('message',FALSE);
				$org_description_content = $post_message['message']['description'];
				$temp_description_content = str_replace("'.base_url().'", base_url(), $post_message['message']['description']);
				
				$dataArr['message']['title'] = $post_message['message']['title'];
				$dataArr['message']['subject'] = $post_message['message']['subject'];
				$dataArr['message']['description'] = $org_description_content;
				
				$file = 'invoice/share_pool_invoice_template.php';
				
			}else{
				$post_message = $this->input->post("$lang_code",FALSE);
				$org_description_content = $post_message["$lang_code"]['email_description'];
				$temp_description_content = str_replace("'.base_url().'", base_url(), $post_message["$lang_code"]['email_description']);
				
				$dataArr["$lang_code"]['email_title'] = $post_message["$lang_code"]['email_title'];
				$dataArr["$lang_code"]['email_subject'] = $post_message["$lang_code"]['email_subject'];
				$dataArr["$lang_code"]['email_description'] = $org_description_content;
				
				$file = 'invoice/share_pool_invoice_template_'.$lang_code.'.php';
			}
			
			if ($template_id == ''){
				$condition = array();
				$this->templates_model->commonInsertUpdate(SHARE_POOL_INVOICE,'insert',$excludeArr,$dataArr,$condition);
				
				$template_content_new = str_replace("{","'.",addslashes($temp_description_content));
				$temp_description_string = str_replace("}",".'",$template_content_new);
				
				$config = "<?php \$message .= '";
				$config .= "$temp_description_string";
				$config .= "';  ?>";
				
				file_put_contents($file, $config);
				$this->setErrorMessage('success','Email template added successfully','admin_template_email_added_success');
			}else {
				if($lang_code!='' && $lang_code!='0'){
					$condition = array('_id' =>  MongoID($template_id));
					$added_languages = $this->templates_model->get_selected_fields(SHARE_POOL_INVOICE, $condition, array('translated_languages'))->row();

					if (isset($added_languages->translated_languages)) {
						foreach ($added_languages->translated_languages as $added) {
							$translated[] = $added;
						}
						if (!in_array($lang_code, $translated)){
							$translated[] = $lang_code;
						}	
						$translated_langs = array('translated_languages' => $translated);
						$dataArr = array_merge($dataArr, $translated_langs);
					}else {
						$translated[] = $lang_code;
						$translated_langs = array('translated_languages' => $translated);
						$dataArr = array_merge($dataArr, $translated_langs);
					}
					
					$this->templates_model->commonInsertUpdate(SHARE_POOL_INVOICE,'update',$excludeArr,$dataArr,	$condition);
					
					$template_content_new = str_replace("{","'.",addslashes($temp_description_content));
					$temp_description_string = str_replace("}",".'",$template_content_new);
					
					$config = "<?php \$message .= '";
					$config .= "$temp_description_string";
					$config .= "';  ?>";
					file_put_contents($file, $config);
					$this->setErrorMessage('success','Email template updated successfully','admin_tempalte_email_update_success');
				}else{
					$condition = array('_id' =>  MongoID($template_id));
					
					$this->templates_model->commonInsertUpdate(SHARE_POOL_INVOICE,'update',$excludeArr,$dataArr,$condition);
					
					$template_content_new = str_replace("{","'.",addslashes($temp_description_content));
					$temp_description_string = str_replace("}",".'",$template_content_new);
				
					$config = "<?php \$message .= '";
					$config .= "$temp_description_string";
					$config .= "';  ?>";
					file_put_contents($file, $config);
					$this->setErrorMessage('success','Email template updated successfully','admin_tempalte_email_update_success');
				}
			}
			redirect(ADMIN_ENC_URL.'/templates/share_pool_invoice_template');
		}
	}
	
	
	/**
	* 
	* To show the list of sms template
	*
	* @param string $selectedLang Language code
	* @return HTML to show the sms template list depends upon the selected language
	*
	**/	
    public function sms_template_list() {
        if ($this->checkLogin('A') == '') {
            redirect(ADMIN_ENC_URL);
        } else {
            $this->data['selectedLang'] = $selectedLang = $this->uri->segment('4');
            $this->data['admin_settings'] = $this->admin_model->getAdminSettings();
            $this->data['language_list'] = $this->templates_model->get_language_list();
            $this->data['language_list_db'] = $this->templates_model->get_all_details(SMS_TEMPLATE, array('language_code' => $selectedLang));
            $condition = array('language_code' => 'en');
            $langData = $this->templates_model->get_selected_fields(SMS_TEMPLATE, $condition);
			$this->data['language_key_values'] = get_language_files_for_SMS();
			
		    if ($this->lang->line('admin_sms_template_edit_heading') != '') {
				$this->data['heading']= stripslashes($this->lang->line('admin_sms_template_edit_heading')); 
			}else{
				$this->data['heading'] = 'Edit SMS Templates';
			}
			
            $this->load->view(ADMIN_ENC_URL.'/newsletter/sms_template_list', $this->data);
        }
    }
	
	/**
	* 
	* To update the sms template list
	*
	* @param string $getLanguageKey language keys
	* @param string $getLanguageContentDetails language values
	* @param string $selectedLang language code
	* @return HTML to show the sms template list
	*
	**/	
	 public function update_sms_template() {
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
            $checkLangExists = $this->templates_model->get_selected_fields(SMS_TEMPLATE, $condition);

            if ($checkLangExists->num_rows() == 0) {
                $finalArray['language_code'] = $selectedLang;
                $this->templates_model->commonInsertUpdate(SMS_TEMPLATE, 'insert', $excludeArray, $finalArray);
            } else {
                $this->templates_model->commonInsertUpdate(SMS_TEMPLATE, 'update', $excludeArray, $finalArray, $condition);
            }
			$this->setErrorMessage('success','SMS template updated successfully','admin_sms_template_updated');
            redirect(ADMIN_ENC_URL.'/templates/sms_template_list/'.$selectedLang);
        }
    }
	
	
}

/* End of file templates.php */
/* Location: ./application/controllers/admin/templates.php */

?>