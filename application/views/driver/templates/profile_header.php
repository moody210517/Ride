<?php
$this->load->view('driver/templates/common_header');
if (!isset($sideMenu)) $sideMenu = '';

$profilePic = base_url() . USER_PROFILE_IMAGE_DEFAULT;
if (isset($driver_info->row()->image) && $driver_info->row()->image != '') {
    $profilePic = base_url() . USER_PROFILE_THUMB . $driver_info->row()->image;
}
$findpage = $this->uri->segment(2);

?> 
</head>
<body>
   <!-------------header----------->
   
<section class="header inner_header">
   <div class="container-fluid profile_login_cont">
      <div class="row">
         <div class="col-md-4 col-lg-4">
         </div>
         <div class="col-md-4 col-lg-4">
            <div class="logo"><a href="<?php echo base_url(); ?>"><img src="images/logo/<?php echo $this->config->item('logo_image'); ?>" alt="<?php echo $this->config->item('email_title'); ?>" title="<?php echo $this->config->item('email_title'); ?>"></a></div>
         </div>
         <div class="col-md-4 col-lg-4">
            <div class="reg_col">
               <ul>
                  <li class="get_app">
                     <div class="dropdown">
                        <button class="btn btn-primary dropdown-toggle" type="button" data-toggle="dropdown">
                        <img src="<?php echo $profilePic; ?>" width="20" height="20" /><?php echo $driver_info->row()->driver_name; ?>
                        <span class="caret"></span></button>
                        <ul class="dropdown-menu inner">
                             <li <?php if ($sideMenu == 'dashboard') { ?>class="active"<?php } ?>><a href="driver/dashboard/driver_dashboard"><?php
										if ($this->lang->line('driver_dash') != '')
											echo stripslashes($this->lang->line('driver_dash'));
										else
											echo 'Dashboard';
										?></a></li>
							<li <?php if ($sideMenu == 'profile') { ?>class="active"<?php } ?>><a href="driver/profile/view_profile"><?php
										if ($this->lang->line('rider_profile_profile') != '')
											echo stripslashes($this->lang->line('rider_profile_profile'));
										else
											echo 'Profile';
										?></a></li>
							<li <?php if ($sideMenu == 'email') { ?>class="active"<?php } ?>><a href="driver/profile/change_email_form"><?php
										if ($this->lang->line('driver_change_mail') != '')
											echo stripslashes($this->lang->line('driver_change_mail'));
										else
											echo 'Change Email';
										?></a></li>
							<li <?php if ($sideMenu == 'mobile') { ?>class="active"<?php } ?>><a href="driver/profile/change_mobile_form"><?php
										if ($this->lang->line('driver_change_mob') != '')
											echo stripslashes($this->lang->line('driver_change_mob'));
										else
											echo 'Change Mobile';
										?></a></li>
							<li <?php if ($sideMenu == 'password') { ?>class="active"<?php } ?>><a href="driver/profile/change_password_form"><?php
										if ($this->lang->line('driver_change_pwd') != '')
											echo stripslashes($this->lang->line('driver_change_pwd'));
										else
											echo 'Change Password';
										?></a></li>
							<li <?php if ($sideMenu == 'banking') { ?>class="active"<?php } ?>><a href="driver/profile/banking"><?php
										if ($this->lang->line('driver_banking') != '')
											echo stripslashes($this->lang->line('driver_banking'));
										else
											echo 'Banking';
										?></a></li>
							<li <?php if ($sideMenu == 'rides') { ?>class="active"<?php } ?>><a href="driver/rides/display_rides"><?php
                        if ($this->lang->line('admin_menu_rides') != '')
                            echo stripslashes($this->lang->line('admin_menu_rides'));
                        else
                            echo 'Rides';
                        ?></a></li>
							<li <?php if ($sideMenu == 'earnings') { ?>class="active"<?php } ?>><a href="driver/payments/display_payments"><?php
										if ($this->lang->line('driver_earn') != '')
											echo stripslashes($this->lang->line('driver_earn'));
										else
											echo 'Earnings';
										?>
									</a></a></li>
							<li><a href="driver/logout"><?php
                        if ($this->lang->line('driver_logout') != '')
                            echo stripslashes($this->lang->line('driver_logout'));
                        else
                            echo 'Logout';
                        ?></a></li>
                        </ul>
                     </div>
                  </li>
               </ul>
            </div>
         </div>
      </div>
   </div>
</section>