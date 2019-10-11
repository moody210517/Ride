<?php if (!defined('BASEPATH')) exit('No direct script access allowed');
/**
* 
* User related functions
* @author Casperon
*
* */
class Mail extends MY_Controller {

    function __construct() {
        parent::__construct();
        $this->load->model('mail_model');
       
    }
	
	/**
	* 
	* Send bulk emails to users and drivers
	* @author Casperon
	*
	* */
	public function send_bulk_emails_to_users_and_drivers(){	
				$template_id = $this->input->post('news_id');
                $template_values = $this->mail_model->notification_email_template_info($template_id);
                $subject = $this->config->item('email_title') . ' - ' . $template_values->message['subject'];
                $mailtemplateValues = array('mail_emailTitle' => $this->config->item('email_title'),
                    'mail_logo' => $this->config->item('logo_image'),
                    'mail_footerContent' => $this->config->item('footer_content'),
                    'mail_metaTitle' => $this->config->item('meta_title'),
                    'mail_contactMail' => $this->config->item('site_contact_mail'),
                );
                extract($mailtemplateValues);
                $message = '<!DOCTYPE HTML>
					<html>
					<head>
					<meta http-equiv="Content-Type" content="text/html; charset=utf-8">
					<meta name="viewport" content="width=device-width"/>
					<title>' . $subject . '</title>
					<body>';
                include('./newsletter/notify_template' . $template_id . '.php');
                $message .= '</body>
					</html>';

                    $sender_email = $this->config->item('site_contact_mail');
                    $sender_name = $this->config->item('email_title');
                
                $email_values = array('mail_type' => 'html',
                    'from_mail_id' => $sender_email,
                    'mail_name' => $sender_name,
                    'to_mail_id' => $this->input->post('to_mail_id'),
					'bcc_mail_id'=>$this->input->post('bcc_mail_id'),
                    'subject_message' => $subject,
                    'body_messages' => $message
				);
				$email_send_to_common = $this->mail_model->common_email_send($email_values);
           
	}

}


/* End of file mail.php */
/* Location: ./application/controllers/site/mail.php */