<?php
$this->load->view('site/templates/header');

?>
<section class="rider_login_sec row">
   <div class="rider_login_cont">
      <h2><?php if ($this->lang->line('rider_forget_password') != '') echo stripslashes($this->lang->line('rider_forget_password')); else echo 'FORGOT PASSWORD'; ?></h2>
   </div>
   <div class="rider_login_cont">
      <div class="col-lg-12 col-md-12 col-sm-12 col-xs-12 no_padding">
         <div  class="col-lg-7 col-md-7 col-sm-7 col-xs-12 left no_padding">
           
			 <?php 
				$formArr = array('id' => 'user_forget_pass_form','method' => 'post','enctype' => 'multipart/form-data','autocomplete' => 'off');
				echo form_open('site/user/user_forgot_password',$formArr);
			 ?>
               <div class="eml frm_detail">
                  <label> <?php if ($this->lang->line('cms_email') != '') echo stripslashes($this->lang->line('cms_email')); else echo 'Email'; ?> </label>
                  
					<?php 
						
						if ($this->lang->line('login_enter_your_email') != '') $placeholder = stripslashes($this->lang->line('login_enter_your_email')); else $placeholder = 'Enter Your Email';
						
						$input_data = array(
										'name' => 'email',
										'type' => 'email',
										'id' => 'email',
										'class' => 'required email',
										'placeholder' => $placeholder
						);
						echo form_input($input_data);
					?>
				  
				  <button class="login securityCheck"><?php if ($this->lang->line('user_submit_upper') != '') echo stripslashes($this->lang->line('user_submit_upper')); else echo 'SUBMIT'; ?> <i class="fa fa-long-arrow-right" aria-hidden="true"></i></i> </button>
				  <a href="rider/login" class="bck_login" title="<?php if ($this->lang->line('user_click_to_go_back') != '') echo stripslashes($this->lang->line('user_click_to_go_back')); else echo 'Click to go back login page'; ?>"><?php if ($this->lang->line('user_back_to_login') != '') echo stripslashes($this->lang->line('user_back_to_login')); else echo 'Back to login'; ?></a>
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
		$("#user_forget_pass_form").validate({
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
