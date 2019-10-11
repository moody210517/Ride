<?php
$this->load->view('driver/templates/header');
?>
<section class="rider_login_sec row">
   <div class="rider_login_cont">
      <h2><?php if ($this->lang->line('dash_driver_forgot_password') != '') echo stripslashes($this->lang->line('dash_driver_forgot_password')); else echo 'FORGOT PASSWORD';?></h2>
   </div>
   <div class="rider_login_cont">
      <div class="col-lg-12 col-md-12 col-sm-12 col-xs-12 no_padding">
         <div  class="col-lg-7 col-md-7 col-sm-7 col-xs-12 left no_padding">
           
			 <?php 
				$formArr = array('id' => 'driver_forget_pass_form','method' => 'post','enctype' => 'multipart/form-data','autocomplete' => 'off');
				echo form_open('driver/profile/driver_forgot_password',$formArr);
			 ?>
               <div class="eml frm_detail">
                  <label><?php if ($this->lang->line('dash_driver_email') != '') echo stripslashes($this->lang->line('dash_driver_email')); else echo 'Email';?> </label>
                  <input type="email" class="required email" name="email" id="email" placeholder="<?php if ($this->lang->line('dash_driver_enter_email') != '') echo stripslashes($this->lang->line('dash_driver_enter_email')); else echo 'Enter Your Email';?>">
				  <button class="login securityCheck"><?php if ($this->lang->line('dash_driver_submit') != '') echo stripslashes($this->lang->line('dash_driver_submit')); else echo 'SUBMIT';?> <i class="fa fa-long-arrow-right" aria-hidden="true"></i></i> </button>
				  <a href="driver/login" class="bck_login"><?php if ($this->lang->line('dash_driver_back_to_login') != '') echo stripslashes($this->lang->line('dash_driver_back_to_login')); else echo 'Back to login';?></a>
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
		$("#driver_forget_pass_form").validate({
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
