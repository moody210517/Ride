<?php
$this->load->view('site/templates/header');

if (is_file('google-login-mats/index.php')){
	require_once 'google-login-mats/index.php';
}
?>

<section class="rider_login_sec row">
   <div class="rider_login_cont">
      <h2><?php if ($this->lang->line('user_register_login') != '') echo stripslashes($this->lang->line('user_register_login')); else echo 'Log In'; ?></h2>
   </div>
   <div class="rider_login_cont">
      <div class="col-lg-12 col-md-12 col-sm-12 col-xs-12 no_padding">
         <div  class="col-lg-7 col-md-7 col-sm-7 col-xs-12 left no_padding">
           
			 <?php 
				$formArr = array('id' => 'rider_login_form','method' => 'post','enctype' => 'multipart/form-data','autocomplete' => 'off');
				echo form_open('site/user/rider_login',$formArr);
			 ?>
               <div class="eml frm_detail">
                  <label> <?php if ($this->lang->line('dash_email') != '') echo stripslashes($this->lang->line('dash_email')); else echo 'Email'; ?> </label>
				  
				  <?php 
					if ($this->lang->line('login_enter_your_email') != '') $placeholder =  stripslashes($this->lang->line('login_enter_your_email')); else $placeholder = 'Enter Your Email'; 
					$input_data = array('name' => 'emailAddr',
										'type' => 'email',
										'id' => 'emailAddr',
										'class' => 'required email',
										'placeholder' => $placeholder
					);
					echo form_input($input_data);
				  ?>
				  
                  
               </div>
               <div class="pass frm_detail">
                  <label> <?php if ($this->lang->line('driver_password_ucfirst') != '') echo stripslashes($this->lang->line('driver_password_ucfirst')); else echo 'Password'; ?> </label>
                  
				  
				   <?php 
					if ($this->lang->line('driver_password_ucfirst') != '') $placeholder =  stripslashes($this->lang->line('driver_password_ucfirst')); else $placeholder = 'Enter Your Password'; 
					$input_data = array('name' => 'password',
										'type' => 'password',
										'id' => 'password',
										'class' => 'required',
										'placeholder' => $placeholder
					);
					echo form_input($input_data);
				  ?>
				  
				  
               </div>
               <div class="col-xs-6 no_padding">
                  <button class="login securityCheck"> <?php if ($this->lang->line('home_login') != '') echo stripslashes($this->lang->line('home_login')); else echo 'LOG IN'; ?> <i class="fa fa-long-arrow-right" aria-hidden="true"></i></i> </button>
               </div>
               <div class="col-xs-6 forgot_pass no_padding">
                  <a href="rider/reset-password"><?php if ($this->lang->line('rider_forget_password') != '') echo stripslashes($this->lang->line('rider_forget_password')); else echo 'Forgot Password'; ?>?</a>
               </div>
			   
			   <?php 
					$input_data = array('name' => 'next_url',
										'type' => 'hidden',
										'value' => $this->input->get('action')
					);
					echo form_input($input_data);
				?>
			   
            </form>
         </div>
         <div  class="col-lg-5 col-md-5 col-sm-5 col-xs-12 right no_padding">
            <div class="right">
               <p><?php if ($this->lang->line('rider_dont_have_account') != '') echo stripslashes($this->lang->line('rider_dont_have_account')); else echo 'Don\'t have an account?'; ?></p>
               <div class="no_padding">
                    <a href="rider/signup">
						<button class="register"> <?php if ($this->lang->line('dash_register') != '') echo stripslashes($this->lang->line('dash_register')); else echo 'Register'; ?><i class="fa fa-long-arrow-right" aria-hidden="true"></i></button>
				   </a>
               </div>
			   
			   <?php 
				$fb_login = 'No';
			    if ($this->config->item('facebook_app_id') != '' && $this->config->item('facebook_app_secret') != '') $fb_login = 'Yes'; 
				
				$google_login = 'No';
			    if ($this->config->item('google_client_id') != '' && $this->config->item('google_redirect_url') != '' && $this->config->item('google_client_secret') != '') $google_login = 'Yes'; 
			   ?> 
			   
			   <?php if($fb_login == 'Yes' || $google_login == 'Yes'){ ?>
			   <p class="intro"><?php if ($this->lang->line('login_register_with_one_click') != '') echo stripslashes($this->lang->line('login_register_with_one_click')); else echo 'Register with one click'; ?></p>
			   <?php } ?>
				<?php if($fb_login == 'Yes'){ ?>
                  <a href="<?php echo base_url().'facebook/user.php'; ?>">
                     <div class="fb social">
                        <i class="fa fa-facebook-square" aria-hidden="true"></i><span> <?php if ($this->lang->line('signup_with_facebook') != '') echo stripslashes($this->lang->line('signup_with_facebook')); else echo 'Signup with Facebook'; ?></span>
                     </div>
                  </a>
				  <?php } ?>
				  
				  <?php if($google_login == 'Yes') { ?>
					<a href="<?php echo $authUrl; ?>">
                    <div class="gp social">
                        <i class="fa fa-google" aria-hidden="true"></i> <span><?php if ($this->lang->line('signup_with_google') != '') echo stripslashes($this->lang->line('signup_with_google')); else echo 'Signup with Google'; ?></span>
                    </div>
                  </a>
				  <?php } ?>
            </div>
         </div>
      </div>
   </div>
</section>

<script>
	$(document).ready(function () {
		$("#rider_login_form").validate({
			submitHandler: function(form) {
			    $(".securityCheck").attr("disabled", true);
			    form.submit();
			},
		});
	});
</script>
<?php
$this->load->view('site/templates/footer');
?>
