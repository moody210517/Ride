<?php
$this->load->view('driver/templates/header');
?>
<section class="rider_login_sec row">
   <div class="rider_login_cont">
      <h2><?php if ($this->lang->line('user_register_login') != '') echo stripslashes($this->lang->line('user_register_login')); else echo 'Log In'; ?></h2>
   </div>
   <div class="rider_login_cont">
      <div class="col-lg-12 col-md-12 col-sm-12 col-xs-12 no_padding">
         <div  class="col-lg-7 col-md-7 col-sm-7 col-xs-12 left no_padding">
           
			 <?php 
				$formArr = array('id' => 'driver_login_form','method' => 'post','enctype' => 'multipart/form-data','autocomplete' => 'off');
				echo form_open('driver/profile/driver_login',$formArr);
			 ?>
               <div class="eml frm_detail">
                  <label> <?php if ($this->lang->line('dash_email') != '') echo stripslashes($this->lang->line('dash_email')); else echo 'Email'; ?> </label>
                  
				  <?php 
					if ($this->lang->line('login_enter_your_email') != '') $placeholder =  stripslashes($this->lang->line('login_enter_your_email')); else $placeholder = 'Enter Your Email'; 
					$input_data = array('name' => 'driver_name',
										'type' => 'email',
										'id' => 'driver_name',
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
					$input_data = array('name' => 'driver_password',
										'type' => 'password',
										'id' => 'driver_password',
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
                  <a href="driver/reset-password"><?php if ($this->lang->line('rider_forget_password') != '') echo stripslashes($this->lang->line('rider_forget_password')); else echo 'Forgot Password'; ?>?</a>
               </div>
			   <input type='hidden' value="<?php echo $this->input->get('action'); ?>" name="next_url"/>
            </form>
         </div>
         <div  class="col-lg-5 col-md-5 col-sm-5 col-xs-12 right no_padding">
            <div class="right">
               <p><?php if ($this->lang->line('rider_dont_have_account') != '') echo stripslashes($this->lang->line('rider_dont_have_account')); else echo 'Don\'t have an account?'; ?></p>
               <div class="no_padding">
                    <a href="driver/signup">
						<button class="register"> <?php if ($this->lang->line('dash_register') != '') echo stripslashes($this->lang->line('dash_register')); else echo 'Register'; ?><i class="fa fa-long-arrow-right" aria-hidden="true"></i></button>
				   </a>
               </div>
            </div>
         </div>
      </div>
   </div>
</section>

<script>
	$(document).ready(function () {
		$("#driver_login_form").validate({
			submitHandler: function(form) {
			    $(".securityCheck").attr("disabled", true);
			    form.submit();
			},
		});
	});
</script>
<?php
$this->load->view('driver/templates/footer');
?>
