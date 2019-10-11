<?php if (!defined('BASEPATH')) exit('No direct script access allowed');

/**
*
*	Cms
* 
*	@package		CI
*	@subpackage		Controller
*	@author			Casperon
*
**/
class Cms extends MY_Controller {

    function __construct() {
        parent::__construct();
        $this->load->helper(array('cookie', 'date', 'form', 'email'));
        $this->load->library(array('encrypt', 'form_validation'));
        $this->load->model('cms_model');

        $this->data['loginCheck'] = $this->checkLogin('U');
		$lang = $this->data['dLangCode'];
		if($this->data['langCode'] != $this->data['dLangCode']){
			$lang = $this->data['langCode'];
		}
		
		$header_menu = $this->user_model->get_selected_fields(MENU,array('name'=>'top_menu'),array('added_pages','add_home_navigation'));
		if($header_menu->num_rows()>0){
			$this->data['header_home'] = 'no';
			if(isset($header_menu->row()->add_home_navigation)){
				if($header_menu->row()->add_home_navigation == 'yes'){
					$this->data['header_home'] = 'yes';
			}
				
			}
			$hmenu_details = array();
			if(isset($header_menu->row()->added_pages)){
				if(!empty($header_menu->row()->added_pages)){
					$i=0;
					foreach($header_menu->row()->added_pages as $header_page){
						if($header_page!=''){
							$detail = $this->user_model->get_all_details(CMS,array('_id'=>MongoID($header_page)));
							if($detail->num_rows() > 0){
								$seourl = $detail->row()->seourl;
								$hmenu_details[$i] = array('name'=>$detail->row()->page_name,'url'=>$seourl);
								if(isset($detail->row()->$lang)){
									if(!empty($detail->row()->$lang)){
										$detail = $detail->row()->$lang;
										$hmenu_details[$i] = array('name'=>$detail['page_name'],'url'=>$seourl);
									}
								}
								$i++;
							}
						}
					}
				}
				
			}
  
			$this->data['header_menu'] = $hmenu_details;
		}
		
		$footer_menu = $this->user_model->get_selected_fields(MENU,array('name'=>'footer_menu'),array('added_pages','add_home_navigation'));
		
		if($footer_menu->num_rows()>0){
			$this->data['footer_home'] = 'no';
			if(isset($header_menu->row()->add_home_navigation)){
				if($header_menu->row()->add_home_navigation == 'yes'){
					$this->data['footer_home'] = 'yes';
			}
				
			}
			$fmenu_details = array();
			if(isset($footer_menu->row()->added_pages)){
				if(!empty($footer_menu->row()->added_pages)){
					$i=0;
					foreach($footer_menu->row()->added_pages as $footer_page){
						if($footer_page!=''){
							$detail = $this->user_model->get_all_details(CMS,array('_id'=>MongoID($footer_page)));
							
							$seourl = "";
							if(isset($detail->row()->seourl)){
								$seourl = $detail->row()->seourl;	
							}
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
				
			}
			$this->data['footer_menu'] = $fmenu_details;
		}
		
  }

		/**
		 *
		 * To list the cms pages
		 * 	
		 * @Initiate HTML to Redirect display cms page
		 *	
		 **/	
    public function index() {
		
		$lang = $this->data['dLangCode'];
		if($this->data['langCode'] != $this->data['dLangCode']){
			$lang = $this->data['langCode'];
		}
		$header_menu = $this->cms_model->get_selected_fields(MENU,array('name'=>'top_menu'),array('added_pages','add_home_navigation'));
		if($header_menu->num_rows()>0){
			$this->data['header_home'] = 'no';
			if(isset($header_menu->row()->add_home_navigation)){
				if($header_menu->row()->add_home_navigation == 'yes'){
					$this->data['header_home'] = 'yes';
			}
				
			}
			$hmenu_details = array();
			if(isset($header_menu->row()->added_pages)){
				if(!empty($header_menu->row()->added_pages)){
					$i=0;
					foreach($header_menu->row()->added_pages as $header_page){
						if($header_page!=''){
							$detail = $this->cms_model->get_all_details(CMS,array('_id'=>MongoID($header_page)));
							$seourl = $detail->row()->seourl;
							$hmenu_details[$i] = array('name'=>$detail->row()->page_name,'url'=>$seourl);
							if(isset($detail->row()->$lang)){
								if(!empty($detail->row()->$lang)){
									$detail = $detail->row()->$lang;
									$hmenu_details[$i] = array('name'=>$detail['page_name'],'url'=>$seourl);
								}
							}
							$i++;
						}
					}
				}
				
			}
			$this->data['header_menu'] = $hmenu_details;
		}
		
		$footer_menu = $this->cms_model->get_selected_fields(MENU,array('name'=>'footer_menu'),array('added_pages','add_home_navigation'));
		
		if($footer_menu->num_rows()>0){
			$this->data['footer_home'] = 'no';
			if(isset($header_menu->row()->add_home_navigation)){
				if($header_menu->row()->add_home_navigation == 'yes'){
					$this->data['footer_home'] = 'yes';
			}
				
			}
			$fmenu_details = array();
			if(isset($footer_menu->row()->added_pages)){
				if(!empty($footer_menu->row()->added_pages)){
					$i=0;
					foreach($footer_menu->row()->added_pages as $footer_page){ 
						if($footer_page!=''){
							$detail = $this->cms_model->get_all_details(CMS,array('_id'=>MongoID($footer_page)));
							if($detail->num_rows() > 0){
								$seourl = $detail->row()->seourl;
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
				
			}
			$this->data['footer_menu'] = $fmenu_details;
		}

        $seourl = $this->uri->segment(2);
        $pageDetails = $this->cms_model->get_all_details(CMS, array('seourl' => $seourl, 'status' => 'Publish'));

        $def_lang = $this->config->item('default_lang_code');
        $user_lang = $this->session->userdata(APP_NAME.'langCode');
        $pageDetail = $pageDetails->result_array()[0];

        if ($user_lang != 'en') {
            if (isset($user_lang) ? !empty($user_lang) : false) {
                if (isset($pageDetails->result_array()[0][$user_lang]))
                    $pageDetail = $pageDetails->result_array()[0][$user_lang];
            }
        }

        if ($pageDetails->num_rows() == 0) {
            show_404();
        } else {

            if ($pageDetail['meta_title'] != '') {
                $this->data['heading'] = $pageDetail['meta_title'];
                $this->data['meta_title'] = $pageDetail['meta_title'];
            }
            if ($pageDetail['meta_tag'] != '') {
                $this->data['meta_keyword'] = $pageDetail['meta_tag'];
            }
            if ($pageDetail['meta_description'] != '') {
                $this->data['meta_description'] = $pageDetail['meta_description'];
            }
            if (isset($pageDetail['meta_abstraction']) && $pageDetail['meta_abstraction'] != '') {
                $this->data['meta_abstraction'] = $pageDetail['meta_abstraction'];
            }
            $this->data['heading'] = $pageDetail['meta_title'];
            $this->data['pageDetails'] = $pageDetails;
            $this->data['pageDetail'] = $pageDetail;

            $this->data['page_details'] = $pageDetail;

            if ($seourl == 'contact-us') {
                $this->load->view('site/cms/contact_us', $this->data);
            } else {
                $this->load->view('site/cms/display_cms', $this->data);
            }
        }
    }

		/**
		 * 
		 * To share the referral code
		 *
		 * @param string $user_id is User MongoDB\BSON\ObjectId
		 * @display HTML to show the share page
		 **/	
    public function share() {
        $user_id = $this->uri->segment(2);
        $this->data['rider_info'] = $this->user_model->get_all_details(USERS, array('_id' => MongoID($user_id)));
        if ($this->data['rider_info']->num_rows() > 0) {

            $this->data['sideMenu'] = 'share_code';
            if ($this->lang->line('driver_share_referral_code') != '')
                $driver_share_referral_code = stripslashes($this->lang->line('driver_share_referral_code'));
            else
                $driver_share_referral_code = 'Share referral code';
            $this->data['heading'] = $driver_share_referral_code;

            if ($this->lang->line('driver_sign_up_with_my_code') != '')
                $shareDesc = stripslashes($this->lang->line('driver_sign_up_with_my_code'));
            else
                $shareDesc = 'Sign up with my code';

            $shareDesc .=" " . $this->data['rider_info']->row()->unique_code . " ";

            if ($this->lang->line('driver_to_get') != '')
                $shareDesc .= stripslashes($this->lang->line('driver_to_get'));
            else
                $shareDesc .= 'to get';

            $shareDesc .=$this->data['dcurrencyCode'] . " " . number_format($this->config->item('welcome_amount'), 2);


            if ($this->lang->line('driver_bonus_amount_on') != '')
                $shareDesc .= str_replace('{SITENAME}', $this->config->item('email_title'), stripslashes($this->lang->line('driver_bonus_amount_on')));
            else
                $shareDesc .= "bonus amount on " . $this->config->item('email_title');


            //$shareDesc = $shareDe . " " . $this->data['rider_info']->row()->unique_code . " to get " . $this->data['dcurrencyCode'] . " " . number_format($this->config->item('welcome_amount'), 2) . " bonus amount on " . $this->config->item('email_title');
            $this->data['shareDesc'] = $shareDesc;
            $this->load->view('site/cms/share', $this->data);
        } else {
            $this->load->view('site/cms/share', $this->data);
        }
    }

		/**
		 * 
		 * To checks the vehicle number is already exist or not
		 *
		 * @param string $driver_id is driver MongoDB\BSON\ObjectId
		 * @param string $vehicle_number is vehicle number
		 **/	
    public function check_number() {
        $driver_id = $this->input->post('driver_id');
        $vehicle_number = $this->input->post('vehicle_number');

        $returnArr['status'] = '0';
        $returnArr['message'] = '';

        $result = $this->user_model->check_vehicle_number($vehicle_number, $driver_id);
        if ($result == 1) {
            $returnArr['status'] = '1';

            if ($this->lang->line('driver_is_already_exist') != '')
                $driver_is_already_exist = stripslashes($this->lang->line('driver_is_already_exist'));
            else
                $driver_is_already_exist = 'is already exist';
            $returnArr['message'] = '<b>' . $vehicle_number . '</b> ' . $driver_is_already_exist . '.';
        }

        $json_encode = json_encode($returnArr);
        echo $this->cleanString($json_encode);
    }
	
	function send_contact_mail(){ 
		$user_name = $this->input->post('user_name');
		$user_email = $this->input->post('user_email');
		$user_address = $this->input->post('user_address');
		$user_address1 = $this->input->post('user_address1');
		$city = $this->input->post('city');
		$state = $this->input->post('state');
		$zipcode = $this->input->post('zipcode');
		$mobile = $this->input->post('mobile');
		$dail_code = $this->input->post('dail_code');
		$message = $this->input->post('message');
		$adressInfo = $user_address;
		if($user_address1 != '')$adressInfo.=',<br/>'.$user_address1;
		$adressInfo.=',<br/>'.$city.' - '.$zipcode;
		$adressInfo.=',<br/>'.$state;
		$adressInfo.=',<br/>'.$mobile;
		$adressInfo.=',<br/>'.$dail_code;
		$adressInfo.=',<br/>'.$user_email;
		
		if($user_email != '' && $message != ''){
			$message = '<!DOCTYPE HTML>
				<html>
				<head>
				<meta http-equiv="Content-Type" content="text/html; charset=utf-8">
				<meta name="viewport" content="width=device-width"/>
				<title>'.$this->config->item('email_title').' - Contact Form</title>
				<body>
					<div style="border:solid 1px #dfdfdf; width:600px;padding: 12px; background: #F2F6F9;">
						<span style="float: left; width:100%;"><img src="'.base_url().'/images/logo/'.$this->config->item('logo_image').'" width="150"></span>
						<table style="width:600px; background: #fff;">
							<tbody>
								<tr>
									<td style="border:solid 1px #dfdfdf; padding: 10px;">Contact Person</td>
									<td style="border:solid 1px #dfdfdf; padding: 10px;">'.$user_name.'</td>
								</tr>
								<tr>
									<td style="border:solid 1px #dfdfdf; padding: 10px;">Address Details</td>
									<td style="border:solid 1px #dfdfdf; padding: 10px;">'.$adressInfo.'</td>
								</tr>
								<tr>
									<td style="border:solid 1px #dfdfdf; padding: 10px;" colspan="2"><b>Message : </b><br/>'.$message.'</td>
									
								</tr>
							</tbody>
						</table>
					</div>
				</body>
				</html>';
			$subject = 'Contact From Customer';
			
			$sender_name = $user_name;
			$sender_email = $user_email;
			$to_email = $this->config->item('site_contact_mail');
			
			$email_values = array('mail_type' => 'html',
				'from_mail_id' => $sender_email,
				'mail_name' => $sender_name,
				'to_mail_id' => $to_email,
				'subject_message' => $subject,
				'body_messages' => $message
			);
			$email_send_to_common = $this->user_model->common_email_send($email_values);
			$this->setErrorMessage('success','Thank you for contacting us!','contact_success');
		} else {
			$this->setErrorMessage('error','Please fill the required fields','contact_failure');
		}
		redirect('pages/contact-us');
	}

}

/*End of file cms.php */
/* Location: ./application/controllers/site/product.php */