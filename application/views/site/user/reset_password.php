<?php
$this->load->view('site/templates/header');
?>
<section class="rider_login_sec row">
   <div class="rider_login_cont">
      <h2><?php if ($this->lang->line('user_reset_password') != '') echo stripslashes($this->lang->line('user_reset_password')); else echo 'RESET PASSWORD'; ?></h2>
   </div>
   <div class="rider_login_cont">
      <div class="col-lg-12 col-md-12 col-sm-12 col-xs-12 no_padding">
         <div  class="col-lg-7 col-md-7 col-sm-7 col-xs-12 left no_padding">
           
			 <?php 
				$formArr = array('id' => 'driver_forget_pass_form','method' => 'post','enctype' => 'multipart/form-data','autocomplete' => 'off');
				echo form_open('site/user/update_reset_password',$formArr);
			 ?>
               <div class="eml frm_detail">
                  <label> <?php if ($this->lang->line('user_new_password_upper') != '') echo stripslashes($this->lang->line('user_new_password_upper')); else echo 'New Password '; ?> </label>
                  
				  <?php 
					if ($this->lang->line('rider_signup_password_placeholder') != '') $placeholder = stripslashes($this->lang->line('rider_signup_password_placeholder')); else $placeholder =  'At least 6 characters ';
					$input_data = array(
									'name' => 'new_password',
									'type' => 'password',
									'id' => 'new_password',
									'class' => 'required',
									'placeholder' => $placeholder,
									'minlength' => '6'
					);
					echo form_input($input_data);
				  ?>
				  
				  
               </div>
			   
			   <div class="eml frm_detail">
				  <label> <?php if ($this->lang->line('user_retype_password') != '') echo stripslashes($this->lang->line('user_retype_password')); else echo 'Retype Password'; ?></label>
                  
				  
				  <?php 
					if ($this->lang->line('rider_signup_password_placeholder') != '') $placeholder = stripslashes($this->lang->line('rider_signup_password_placeholder')); else $placeholder =  'At least 6 characters ';
					$input_data = array(
									'name' => 'confirm_password',
									'id' => 'confirm_password',
									'type' => 'password',
									'class' => 'required',
									'placeholder' => $placeholder,
									'minlength' => '6',
									'equalTo' => '#new_password'
					);
					echo form_input($input_data);
				  ?>
				  
				  <button class="login"><?php if ($this->lang->line('user_submit_upper') != '') echo stripslashes($this->lang->line('user_submit_upper')); else echo 'SUBMIT'; ?> <i class="fa fa-long-arrow-right" aria-hidden="true"></i></i> </button>
				  <a href="rider/login" class="bck_login" ><?php if ($this->lang->line('user_back_to_login') != '') echo stripslashes($this->lang->line('user_back_to_login')); else echo 'Back to login'; ?></a>
               </div>
            </form>
         </div>
      </div>
   </div>
</section>


<style>
.rider_login_cont .left.no_padding {
    border-right: none !important;
}
.rider_login_cont .login {
    margin-top: 30px;
}
.bck_login,.bck_login:hover {
	color:#e74c3c;
	margin-left: 10%;
}
</style>

<script>
	$(document).ready(function () {
		$("#driver_forget_pass_form").validate();
	});
</script>
<?php
$this->load->view('site/templates/footer');
?>
