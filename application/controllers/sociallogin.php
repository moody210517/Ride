<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

class Sociallogin extends MY_Controller {

	function __construct()
    {	
        parent::__construct();
		$this->load->model('user_model');
		$this->load->library('session');
    }
	public function index()
	{
		$this->googleLoginProcess();
	}
	
	/*  login, registration */

	function googleLoginProcess()
	{
	
		$getFileNameArray = explode('/',$profile_image_url);
	
		 $fileNameDetails = $getFileNameArray[7];
		 
		$url = $twConnectId->profile_image_url;
		$img = 'images/users/'.$fileNameDetails ;
		file_put_contents($img, file_get_contents($url));
		
		
		$url = $profile_image_url;
		$img = 'images/users/'.$fileNameDetails ;
		file_put_contents($img, file_get_contents($url));
				
		$google_login_details = array('social_login_name'=>$user_name,'social_login_unique_id'=>$user_id,'screen_name'=>$user_name,'social_image_name'=>$fileNameDetails);
		
		$_SESSION['social_login_name']=$user_name;
		$_SESSION['social_login_unique_id']=$user_id;
		$_SESSION['screen_name']=$user_name;
		$_SESSION['social_image_name']=$fileNameDetails;
		//redirect('signup');
		header( 'Location: '.$originalBasePath.'signup' );
		
	}
	
	function googleRedirect()
	{
		require_once 'google-login-mats/index.php';
		$user_name  = '';
		$email = '';
		if(isset($_GET['code'])){ 
			$gClient->authenticate($_GET['code']);
			$_SESSION['token'] = $gClient->getAccessToken();
			//header('Location: ' . filter_var($google_redirect_url, FILTER_SANITIZE_URL));
			return;
		}
		
		
		if(isset($_SESSION['token'])){ 
				$gClient->setAccessToken($_SESSION['token']);
		}
		
		
		if ($gClient->getAccessToken()){
			  //Get user details if user is logged in
			  $user 				= $google_oauthV2->userinfo->get(); 
			  $user_id 				= $user['id'];
			  $user_name 			= filter_var($user['name'], FILTER_SANITIZE_SPECIAL_CHARS);
			  $email 				= filter_var($user['email'], FILTER_SANITIZE_EMAIL);
			  $profile_url 			= filter_var($user['link'], FILTER_VALIDATE_URL);
			  $profile_image_url 	= filter_var($user['picture'], FILTER_VALIDATE_URL);
			  $personMarkup 		= "$email<div><img src='$profile_image_url?sz=50'></div>";
			
			  $_SESSION['token'] 	= $gClient->getAccessToken(); 
		} else {
			//get google login url
			$authUrl = $gClient->createAuthUrl();
		}
		
		$userImage = '';
		if($profile_image_url != ''){
			/*******  update user image  ******/
			$image_data = file_get_contents($profile_image_url);
			$url = base_url()."upload-fb-profile-pic";
			$data = array('image_data'=>base64_encode($image_data),'user_id'=>$user_id);
			$userImage = $user_id.'.jpg';
			$this->load->library('curl');
			$output = $this->curl->simple_post($url, $data);
		}
		 
		if($email != ''){
			$condition = array('email' => $email);
            $checkUser = $this->user_model->get_selected_fields(USERS, $condition, array('_id', 'user_name', 'unique_code', 'email','status'));
			
			if($checkUser->num_rows() > 0){
				if($checkUser->row()->status=='Active') {
					$userdata = array(
								APP_NAME.'_session_user_id' => (string) $checkUser->row()->_id,
								APP_NAME.'_session_user_name' => $checkUser->row()->user_name,
								APP_NAME.'_session_unique_code' => $checkUser->row()->unique_code,
								APP_NAME.'_session_user_email' => $checkUser->row()->email
							  );
					$this->session->set_userdata($userdata);
					$this->setErrorMessage('success','You are Logged In!','user_social_logged_in');
					redirect('rider/profile');
				} else {
					$userdata = array(
								APP_NAME.'_session_user_id' => "",
								APP_NAME.'_session_user_name' => "",
								APP_NAME.'_session_unique_code' => "",
								APP_NAME.'_session_user_email' => ""
							  );
					$this->session->set_userdata($userdata);
					$this->setErrorMessage('error', 'Your Account Is In-Active', 'driver_acccount_inactive');
					redirect('rider/login');
				}
			} else {				
				$google_login_details = array('social_login_name'=>$user_name,'social_login_unique_id'=>'','screen_name'=>$user_name,'social_image_name'=>$userImage,'social_email_name'=>$email,'loginUserType'=>'google','social_user_id'=>$user_id,'ref_code'=>$_SESSION['ref_code']);
				$social_login_name = $user_name;
				$this->session->set_userdata($google_login_details);
				$this->session->set_flashdata('user_type', "google");				
				redirect('rider/social-signup');
			}
		} else {
			redirect('');
		}
	}
	
	function facebookRedirect(){
		@session_start();
		$flashErr = $this->input->get('flashErr');
		if($flashErr  != ''){
			$this->setErrorMessage('error',$flashErr);
			redirect('');
		}
		$email=$_SESSION['email'];
		$fb_user_id = $_SESSION['fb_user_id']; 
		$userExist = FALSE;
		if($fb_user_id != ''){
			$condition = array('fb_user_id' => $fb_user_id);
			$checkUser = $this->user_model->get_selected_fields(USERS, $condition, array('_id', 'user_name', 'unique_code', 'email','status'));
			if($checkUser->num_rows() > 0) $userExist = TRUE;
		}
		
		if(!$userExist && $email != ''){
			$condition = array('email' => $email);
			$checkUser = $this->user_model->get_selected_fields(USERS, $condition, array('_id', 'user_name', 'unique_code', 'email','status'));
			if($checkUser->num_rows() > 0) $userExist = TRUE;
		}
		
		if( $userExist ){
			if($checkUser->row()->status=='Active') {
				if($email != '' && $email != $checkUser->row()->email){
					$this->user_model->update_details(USERS,array('email'=>$email),$condition);
				}
				$userdata = array(
							APP_NAME.'_session_user_id' => (string) $checkUser->row()->_id,
							APP_NAME.'_session_user_name' => $checkUser->row()->user_name,
							APP_NAME.'_session_unique_code' => $checkUser->row()->unique_code,
							APP_NAME.'_session_user_email' => $checkUser->row()->email
							);
				$this->session->set_userdata($userdata);
				$this->setErrorMessage('success','You are Logged In!','user_social_logged_in');
				redirect('rider/profile');
			} else {
				$this->setErrorMessage('error', 'Your Account Is In-Active', 'driver_acccount_inactive');
				redirect('rider/login');
			}
		} else {
			
			$google_login_details = array('social_login_name'=>$_SESSION['first_name'],'social_login_unique_id'=>'','screen_name'=>$_SESSION['first_name'],'social_image_name'=>$_SESSION['user_image'],'social_email_name'=>$_SESSION['email'],'loginUserType'=>'facebook','social_user_image'=>$_SESSION['user_image'],'social_user_id'=>$_SESSION['fb_user_id'],'ref_code'=>$_SESSION['ref_code']);
			
			$social_login_name = $_SESSION['first_name'];
			$this->session->set_userdata($google_login_details);
			$this->session->set_flashdata('user_type', "facebook");				
			redirect('rider/social-signup');
		}
	}
	
	
	

}