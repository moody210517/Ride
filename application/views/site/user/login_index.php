<?php
$this->load->view('site/templates/header');
?>

<section class="rider_login_sec row log-base-sec">
   <div class="container login-center">
      <div class="col-lg-12 col-md-12 col-sm-12 col-xs-12 base-log">
         <div class="login-base">
            <h1><?php if ($this->lang->line('user_register_login') != '') echo stripslashes($this->lang->line('user_register_login')); else echo 'Log in'; ?></h1>
            
            
            <div class="col-lg-4 col-md-4 col-sm-12 col-xs-12 left-log">
               <h3><?php if ($this->lang->line('dash_driver') != '') echo stripslashes($this->lang->line('dash_driver')); else echo 'Driver'; ?></h3>
               <p>
                  <?php if ($this->lang->line('find_everything_u_need_track_success_on_the_road') != '') echo stripslashes($this->lang->line('find_everything_u_need_track_success_on_the_road')); else echo 'Find everything you need to track your success on the road.'; ?>
               </p>
               <p class="driver-login">
                  <a href="driver/login">
                  <?php if ($this->lang->line('dash_driver_login') != '') echo stripslashes($this->lang->line('dash_driver_login')); else echo 'Driver Login'; ?>
                  </a>
               </p>
               <p class="driver-sigin">
                  <span><?php if ($this->lang->line('rider_dont_have_account') != '') echo stripslashes($this->lang->line('rider_dont_have_account')); else echo 'Don\'t have an account?'; ?></span>
                  <a href="driver/signup"><?php if ($this->lang->line('dash_register') != '') echo stripslashes($this->lang->line('dash_register')); else echo 'Register'; ?></a>
               </p>
            </div>


            <div class="col-lg-4 col-md-4 col-sm-12 col-xs-12 left-log">
               <h3><?php if ($this->lang->line('admin_common_rider') != '') echo stripslashes($this->lang->line('admin_common_rider')); else echo 'Rider'; ?></h3>
               <p>
                  <?php if ($this->lang->line('manage_ur_payment_option_history_more') != '') echo stripslashes($this->lang->line('manage_ur_payment_option_history_more')); else echo 'Manage your payment options, review trip history, and more.'; ?>
               </p>
               <p class="driver-login">
                  <a href="rider/login">
                  <?php if ($this->lang->line('driver_rider_login') != '') echo stripslashes($this->lang->line('driver_rider_login')); else echo 'Rider Login'; ?>
                  </a>
               </p>
               <p class="driver-sigin">
                  <span><?php if ($this->lang->line('rider_dont_have_account') != '') echo stripslashes($this->lang->line('rider_dont_have_account')); else echo 'Don\'t have an account?'; ?></span>
                  <a href="rider/signup"><?php if ($this->lang->line('dash_register') != '') echo stripslashes($this->lang->line('dash_register')); else echo 'Register'; ?></a>
               </p>
            </div>


            
            <div class="col-lg-4 col-md-4 col-sm-12 col-xs-12 left-log rite-log">
               <h3><?php if ($this->lang->line('admin_common_operator') != '') echo stripslashes($this->lang->line('admin_common_operator')); else echo 'Operator'; ?></h3>
               <p>
                  <?php if ($this->lang->line('manage_ur_payment_option_history_more') != '') echo stripslashes($this->lang->line('manage_ur_payment_option_history_more')); else echo 'Login as operator.'; ?>
               </p>
               <p class="driver-login">
                  <a href="operator/login">
                  <?php if ($this->lang->line('driver_rider_login') != '') echo stripslashes($this->lang->line('driver_rider_login')); else echo 'Operator Login'; ?>
                  </a>
               </p>
               <p class="driver-sigin">
                  <span><?php if ($this->lang->line('rider_dont_have_account') != '') echo stripslashes($this->lang->line('rider_dont_have_account')); else echo 'Don\'t have an account?'; ?></span>
                  <a href="operator/signup"><?php if ($this->lang->line('dash_register') != '') echo stripslashes($this->lang->line('dash_register')); else echo 'Register'; ?></a>
               </p>
            </div>



         </div>
      </div>
   </div>
</section>

<?php
$this->load->view('site/templates/footer');
?>