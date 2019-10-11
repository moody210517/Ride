<?php
$profilePic = base_url() . USER_PROFILE_IMAGE_DEFAULT;
if (isset($rider_info->row()->image) && $rider_info->row()->image != '') {
    $profilePic = base_url() . USER_PROFILE_THUMB . $rider_info->row()->image;
}
$findpage = $this->uri->segment(2);
?>

	<div class="profile_det">
         <div class="profile_pic">
            <div class="profile_pic_contt">
               <img src="<?php echo $profilePic; ?>" class="profile_pic_img" />
               <?php if($findpage != 'profile'){ ?>
			   <a href="rider/profile"><img src="images/site/edit_img.png" class="profile_edit_img" /></a>
			   <?php } ?>
            </div>
            <label><?php echo $rider_info->row()->user_name; ?></label>
         </div>
         <ul>
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
