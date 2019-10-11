<?php
$profilePic = base_url() . USER_PROFILE_IMAGE_DEFAULT;
if (isset($driver_info->row()->image) && $driver_info->row()->image != '') {
    $profilePic = base_url() . USER_PROFILE_THUMB . $driver_info->row()->image;
}
$findpage = $this->uri->segment(3);
?>

	<div class="profile_det">
        <div class="profile_pic">
            <div class="profile_pic_contt">
				<a  <?php if($findpage != 'view_profile'){ ?> href="driver/profile/view_profile" <?php } ?>>
					<img src="<?php echo $profilePic; ?>" class="profile_pic_img" />
				</a>
               <?php if($findpage != 'edit_profile_form'){ ?>
			   <a href="driver/profile/edit_profile_form"><img src="images/site/edit_img.png" class="profile_edit_img" /></a>
			   <?php } ?>
            </div>
            <label><?php echo $driver_info->row()->driver_name; ?></label>
        </div>
        <ul>
            <li <?php if ($sideMenu == 'dashboard') { ?>class="active"<?php } ?>><a href="driver/dashboard/driver_dashboard"><?php
                        if ($this->lang->line('driver_dash') != '')
                            echo stripslashes($this->lang->line('driver_dash'));
                        else
                            echo 'Dashboard';
                        ?> </a></li>
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
                        ?></a></li>
			<li><a href="driver/logout"><?php
                        if ($this->lang->line('driver_logout') != '')
                            echo stripslashes($this->lang->line('driver_logout'));
                        else
                            echo 'Logout';
                        ?></a></li>
        </ul>
	</div>
