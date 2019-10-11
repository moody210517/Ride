<?php
$this->load->view('site/templates/common_header');
if (!isset($sideMenu)) $sideMenu = '';

$profilePic = base_url() . USER_PROFILE_IMAGE_DEFAULT;
if (isset($rider_info->row()->image) && $rider_info->row()->image != '') {
    $profilePic = base_url() . USER_PROFILE_THUMB . $rider_info->row()->image;
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
                        <img src="<?php echo $profilePic; ?>" width="20" height="20" /><?php echo $rider_info->row()->user_name; ?>
                        <span class="caret"></span></button>
                        <ul class="dropdown-menu inner">
                            <li <?php if ($sideMenu == 'bookride') { ?>class="active"<?php } ?>><a href="rider/booking"><?php if ($this->lang->line('rider_book_ride') != '') echo stripslashes($this->lang->line('rider_book_ride')); else echo 'Book Ride'; ?></a></li>
							<li <?php if ($sideMenu == 'rides') { ?>class="active"<?php } ?>><a  href="rider/my-rides"><?php if ($this->lang->line('rider_profile_my_rides') != '') echo stripslashes($this->lang->line('rider_profile_my_rides')); else echo 'My Rides'; ?></a></li>
							<li <?php if ($sideMenu == 'profile') { ?>class="active"<?php } ?>><a href="rider/profile"><?php if ($this->lang->line('profile_setting') != '') echo stripslashes($this->lang->line('profile_setting')); else echo 'Profile'; ?></a></li>
							<li <?php if ($sideMenu == 'password') { ?>class="active"<?php } ?>><a href="rider/change-password-form"><?php if ($this->lang->line('Change_setting') != '') echo stripslashes($this->lang->line('Change_setting')); else echo 'Change Password'; ?></a></li>
							<li <?php if ($sideMenu == 'ratecard') { ?>class="active"<?php } ?>><a href="rider/rate-card"><?php if ($this->lang->line('rider_profile_rate_card') != '') echo stripslashes($this->lang->line('rider_profile_rate_card')); else echo 'Rate Card'; ?></a></li>
							<li <?php if ($sideMenu == 'wallet') { ?>class="active"<?php } ?>><a href="rider/my-money"><?php echo $siteTitle; ?> <?php if ($this->lang->line('user_money_ucfirst') != '') echo stripslashes($this->lang->line('user_money_ucfirst')); else echo 'Money'; ?></a></li>
							<li <?php if ($sideMenu == 'share_earnings') { ?>class="active"<?php } ?>><a href="rider/share-and-earnings"><?php if ($this->lang->line('rider_profile_share_earnings_invite') != '') echo stripslashes($this->lang->line('rider_profile_share_earnings_invite')); else echo 'Invite & Earn'; ?></a></li>
							<li <?php if ($sideMenu == 'emergency') { ?>class="active"<?php } ?>><a href="rider/emergency-contact"><?php if ($this->lang->line('rider_profile_emergency_contact') != '') echo stripslashes($this->lang->line('rider_profile_emergency_contact')); else echo 'Emergency Contact'; ?></a></li>
							<li <?php if ($sideMenu == 'fav_locations') { ?>class="active"<?php } ?>><a href="rider/fav-location"><?php if ($this->lang->line('user_favourite_locations') != '') echo stripslashes($this->lang->line('user_favourite_locations')); else echo 'Favourite Locations'; ?></a></li>
							<li><a href="rider/logout"><?php if ($this->lang->line('rider_profile_logout') != '') echo stripslashes($this->lang->line('rider_profile_logout')); else echo 'Logout'; ?></a></li>
                        </ul>
                     </div>
                  </li>
               </ul>
            </div>
         </div>
      </div>
   </div>
</section>