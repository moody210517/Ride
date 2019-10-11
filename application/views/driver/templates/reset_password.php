<?php
$this->load->view('driver/templates/header');
?>
<section class="rider_login_sec row">
   <div class="rider_login_cont">
      <h2>RESET PASSWORD</h2>
   </div>
   <div class="rider_login_cont">
      <div class="col-lg-12 col-md-12 col-sm-12 col-xs-12 no_padding">
         <div  class="col-lg-7 col-md-7 col-sm-7 col-xs-12 left no_padding">
           
			 <?php 
				$formArr = array('id' => 'driver_forget_pass_form','method' => 'post','enctype' => 'multipart/form-data','autocomplete' => 'off');
				echo form_open('driver/profile/update_reset_password',$formArr);
			 ?>
               <div class="eml frm_detail">
                  <label> New Password </label>
                  <input  class="required" name="new_password" id="new_password" type="password" placeholder="Please enter a new password" minlength="6" />
               </div>
			   
			   <input type="hidden" name="reset_id" value="<?php echo $reset_id; ?>" />
			   
			   <div class="eml frm_detail">
				  <label> Retype Password</label>
                  <input class="required" name="confirm_password" id="confirm_password" type="password" placeholder="Please re-enter password" equalto="#new_password" minlength="6" />
				  <button class="login">SUBMIT <i class="fa fa-long-arrow-right" aria-hidden="true"></i></i> </button>
				  <a href="driver/login" class="bck_login">Back to login</a>
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
$this->load->view('driver/templates/footer');
?>
