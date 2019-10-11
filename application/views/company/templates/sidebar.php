<?php 
$currentUrl = $this->uri->segment(2, 0);
$currentPage = $this->uri->segment(3, 0);
if ($currentUrl == '') {
    $currentUrl = 'dashboard';
}
if ($currentPage == '') {
    $currentPage = 'dashboard';
}

$current_url = $_SERVER['REQUEST_URI'];
?>
<div class="" >
<div id="left_bar" >

	<div class="logo">
				<img src="images/logo/<?php echo $logo;?>" alt="<?php echo $siteTitle;?>" width="90px" title="<?php echo $siteTitle;?>">
			</div>


    <div id="sidebar">
        <div id="secondary_nav">
            <ul id="sidenav" class="accordion_mnu collapsible">
                <li>
                    <a href="<?php echo base_url(); ?><?php echo COMPANY_NAME; ?>/dashboard/user_dashboard" <?php
                    if ($currentUrl == 'dashboard') {
                        echo 'class="active"';
                    }
                    ?>>
                        <span class="nav_icon computer_imac"></span> 
						<?php if ($this->lang->line('admin_menu_dashboard') != '') echo stripslashes($this->lang->line('admin_menu_dashboard')); else echo 'Dashboard'; ?>
                    </a>
                </li>
                <!--<li>
                    <h6 style="margin: 10px 0;padding-left:10px; font-size:13px; font-weight:bold;color:#333; text-transform:uppercase; "><?php if ($this->lang->line('admin_menu_managements') != '') echo stripslashes($this->lang->line('admin_menu_managements')); else echo 'Managements'; ?></h6>
                </li>-->
			

                    <li>
                        <a href="<?php echo $current_url; ?>" <?php
                        if ($currentUrl == 'adminlogin') {
                            echo 'class="active"';
                        }
                        ?>>
                            <span class="nav_icon admin_user"></span><?php if ($this->lang->line('company_settings') != '') echo stripslashes($this->lang->line('company_settings')); else echo 'Settings'; ?><span class="up_down_arrow">&nbsp;</span>
                        </a>
                        <ul class="acitem" <?php
                        if ($currentUrl == 'login') {
                            echo 'style="display: block;"';
                        } else {
                            echo 'style="display: none;"';
                        }
                        ?>>
                            <li>
                                <a href="<?php echo COMPANY_NAME; ?>/login/profile_setting" <?php
                                if ($currentPage == 'profile_setting') {
                                    echo 'class="active"';
                                }
                                ?>>
                                    <span class="list-icon">&nbsp;</span><?php if ($this->lang->line('profile_setting') != '') echo stripslashes($this->lang->line('profile_setting')); else echo 'Profile'; ?>
                                </a>
                            </li>
							<li>
                                <a href="<?php echo COMPANY_NAME; ?>/login/change_password_form" <?php
                                if ($currentPage == 'change_password_form') {
                                    echo 'class="active"';
                                }
                                ?>>
                                    <span class="list-icon">&nbsp;</span><?php if ($this->lang->line('admin_menu_change_password') != '') echo stripslashes($this->lang->line('admin_menu_change_password')); else echo 'Change Password'; ?>
                                </a>
                            </li>

                        </ul>
                    </li>
					<li>
                        <a href="<?php echo COMPANY_NAME; ?>/map/map_avail_drivers" <?php
                        if ($currentPage == 'map_avail_drivers') {
                            echo 'class="active"';
                        }
                        ?>>
                            <span class="nav_icon marker"></span><?php if ($this->lang->line('admin_menu_view_available_drivers') != '') echo stripslashes($this->lang->line('admin_menu_view_available_drivers')); else echo 'View available drivers'; ?>
                        </a>
                    </li>
        
                    <li>
                        <a href="<?php echo $current_url; ?>" <?php
                        if (($currentUrl == 'drivers' || $currentPage == 'view_driver_reviews') && ($currentPage != 'add_edit_category_types' && $currentPage != 'add_edit_category' && $currentPage != 'display_drivers_category' && $currentPage != 'edit_language_category')) {
                            echo 'class="active"';
                        }
                        ?>>
                            <span class="nav_icon users"></span> <?php if ($this->lang->line('admin_menu_drivers') != '') echo stripslashes($this->lang->line('admin_menu_drivers')); else echo 'Drivers'; ?><span class="up_down_arrow">&nbsp;</span>
                        </a>
                        <ul class="acitem" <?php
                        if (($currentUrl == 'drivers' || $currentPage == 'view_driver_reviews') && ($currentPage != 'add_edit_category_types' && $currentPage != 'add_edit_category' && $currentPage != 'display_drivers_category' && $currentPage != 'edit_language_category')) {
                            echo 'style="display: block;"';
                        } else {
                            echo 'style="display: none;"';
                        }
                        ?>>
                            <li>
                                <a href="<?php echo COMPANY_NAME; ?>/drivers/display_driver_dashboard" <?php
                                if ($currentPage == 'display_driver_dashboard') {
                                    echo 'class="active"';
                                }
                                ?>>
                                    <span class="list-icon">&nbsp;</span><?php if ($this->lang->line('admin_menu_drivers_dashboard') != '') echo stripslashes($this->lang->line('admin_menu_drivers_dashboard')); else echo 'Drivers Dashboard'; ?>
                                </a>
                            </li>
                            <li>
                                <a href="<?php echo COMPANY_NAME; ?>/drivers/display_drivers_list" <?php
                                if ($currentPage == 'display_drivers_list' || $currentPage == 'edit_driver_form' || $currentPage == 'change_password_form' || $currentPage == 'view_driver' || $currentPage == 'banking' || $currentPage == 'view_driver_reviews') {
                                    echo 'class="active"';
                                }
                                ?>>
                                    <span class="list-icon">&nbsp;</span><?php if ($this->lang->line('admin_menu_drivers_list') != '') echo stripslashes($this->lang->line('admin_menu_drivers_list')); else echo 'Drivers List'; ?>
                                </a>
                            </li>
                          
                                <li>
                                    <a href="<?php echo COMPANY_NAME; ?>/drivers/add_driver_form" <?php
                                    if ($currentPage == 'add_driver_form') {
                                        echo 'class="active"';
                                    }
                                    ?>>
                                        <span class="list-icon">&nbsp;</span><?php if ($this->lang->line('admin_menu_add_driver') != '') echo stripslashes($this->lang->line('admin_menu_add_driver')); else echo 'Add Driver'; ?>
                                    </a>
                                </li>
                          
						</ul>
                    </li>
					<li>
                        <a href="<?php echo COMPANY_NAME; ?>/mileage/display_mileage_list" <?php if ($currentUrl == 'mileage') { echo 'class="active"'; } ?>>
                            <span class="nav_icon car"></span> <?php if ($this->lang->line('admin_menu_mileage') != '') echo stripslashes($this->lang->line('admin_menu_mileage')); else echo 'Mileage'; ?>
                        </a>
                    </li>
               
                    <li>
                        <a href="<?php echo COMPANY_NAME; ?>/revenue/display_site_revenue" <?php
                        if ($currentUrl == 'revenue') {
                            echo 'class="active"';
                        }
                        ?>>
                            <span class="nav_icon money"></span><?php if ($this->lang->line('admin_menu_site_earnings') != '') echo stripslashes($this->lang->line('admin_menu_site_earnings')); else echo 'Site Earnings'; ?> 
                        </a>
                    </li>
					
              

                  <?php
            
                    $ride_action = $this->input->get('act');
                    ?>
                    <li>
                        <a href="<?php echo $current_url; ?>" <?php
                        if ($currentUrl == 'rides') {
                            echo 'class="active"';
                        }
                        ?>>
                            <span class="nav_icon car"></span> <?php if ($this->lang->line('admin_menu_rides') != '') echo stripslashes($this->lang->line('admin_menu_rides')); else echo 'Rides'; ?><span class="up_down_arrow">&nbsp;</span>
                        </a>
                        <ul class="acitem" <?php
                        if ($currentUrl == 'rides') {
                            echo 'style="display: block;"';
                        } else {
                            echo 'style="display: none;"';
                        }
                        ?>>
							
                            <?php /* <li>
                                <a href="<?php echo COMPANY_NAME; ?>/rides/display_rides?act=Booked" <?php
                                if ($ride_action == 'Booked') {
                                    echo 'class="active"';
                                }
                                ?>>
                                    <span class="list-icon">&nbsp;</span><?php if ($this->lang->line('admin_menu_just_booked') != '') echo stripslashes($this->lang->line('admin_menu_just_booked')); else echo 'Just Booked'; ?>
                                </a>
                            </li> */ ?>
                            
                            <li>
                                <a href="<?php echo COMPANY_NAME;?>/rides/rides_grid_view" <?php
                                if ($currentPage == 'rides_grid_view') {
                                    echo 'class="active"';
                                }
                                ?>>
                                    <span class="list-icon">&nbsp;</span><?php if ($this->lang->line('rides_grid_view') != '') echo stripslashes($this->lang->line('rides_grid_view')); else echo 'Rides Grid View'; ?>
                                </a>
                            </li>
                            <li>
                                <a href="<?php echo COMPANY_NAME; ?>/rides/display_rides?act=OnRide" <?php
                                if ($ride_action == 'OnRide') {
                                    echo 'class="active"';
                                }
                                ?>>
                                    <span class="list-icon">&nbsp;</span><?php if ($this->lang->line('admin_menu_on_rides') != '') echo stripslashes($this->lang->line('admin_menu_on_rides')); else echo 'On Rides'; ?>
                                </a>
                            </li>
                            <li>
                                <a href="<?php echo COMPANY_NAME; ?>/rides/display_rides?act=Completed" <?php
                                if ($ride_action == 'Completed') {
                                    echo 'class="active"';
                                }
                                ?>>
                                    <span class="list-icon">&nbsp;</span><?php if ($this->lang->line('admin_menu_completed_rides') != '') echo stripslashes($this->lang->line('admin_menu_completed_rides')); else echo 'Completed Rides'; ?>
                                </a>
                            </li>
                            <li>
                                <a href="<?php echo COMPANY_NAME; ?>/rides/display_rides?act=Cancelled" <?php
                                if ($ride_action == 'Cancelled' || $ride_action == 'riderCancelled' || $ride_action == 'driverCancelled') {
                                    echo 'class="active"';
                                }
                                ?>>
                                    <span class="list-icon">&nbsp;</span><?php if ($this->lang->line('admin_menu_cancelled_rides') != '') echo stripslashes($this->lang->line('admin_menu_cancelled_rides')); else echo 'Cancelled Rides'; ?>
                                </a>
                            </li>
							<!---<li>
                                    <a href="admin/rides/display_rides?act=All" <?php
                            if ($ride_action == 'All') {
                                echo 'class="active"';
                            }
                            ?>>
                                            <span class="list-icon">&nbsp;</span>All Rides
                                    </a>
                            </li> -->
                        </ul>
                          
                    <li>
                        <a href="<?php echo $current_url; ?>" <?php
                        if ($currentUrl == 'notification' || $currentPage == 'display_notification_user_list') {
                            echo 'class="active"';
                        }
                        ?>>
                            <span class="nav_icon users"></span> <?php if ($this->lang->line('admin_menu_notification') != '') echo stripslashes($this->lang->line('admin_menu_notification')); else echo 'Notification'; ?><span class="up_down_arrow">&nbsp;</span>
                        </a>
                        <ul class="acitem" <?php
                        if ($currentUrl == 'notification' || $currentPage == 'display_notification_user_list' || $currentPage == 'display_notification_driver_list') {
                            echo 'style="display: block;"';
                        } else {
                            echo 'style="display: none;"';
                        }
                        ?>>
                           
                            <li>
                                <a href="<?php echo COMPANY_NAME; ?>/notification/display_notification_driver_list" <?php
                                if ($currentPage == 'display_notification_driver_list') {
                                    echo 'class="active"';
                                }
                                ?>>
                                    <span class="list-icon">&nbsp;</span><?php if ($this->lang->line('admin_menu_drivers') != '') echo stripslashes($this->lang->line('admin_menu_drivers')); else echo 'Drivers'; ?>
                                </a>
                            </li>
						
                        </ul>
                    </li>
					
					
                    <li>
                        <a href="<?php echo $current_url; ?>" <?php
                        if ($currentUrl == 'brand') {
                            echo 'class="active"';
                        }
                        ?>>
                            <span class="nav_icon companies"></span> <?php if ($this->lang->line('admin_menu_make_and_model') != '') echo stripslashes($this->lang->line('admin_menu_make_and_model')); else echo 'Make and Model'; ?><span class="up_down_arrow">&nbsp;</span>
                        </a>
                        <ul class="acitem" <?php
                        if ($currentUrl == 'brand' || $currentPage == 'display_brand_list' || $currentPage == 'add_brand_form' || $currentPage == 'edit_brand_form') {
                            echo 'style="display: block;"';
                        } else {
                            echo 'style="display: none;"';
                        }
                        ?>>
                            <li>
                                <a href="<?php echo COMPANY_NAME; ?>/brand/display_brand_list" <?php
                                if ($currentPage == 'display_brand_list') {
                                    echo 'class="active"';
                                }
                                ?>>
                                    <span class="list-icon">&nbsp;</span><?php if ($this->lang->line('admin_menu_makers_list') != '') echo stripslashes($this->lang->line('admin_menu_makers_list')); else echo 'Makers List'; ?>
                                </a>
                            </li>
							<li>
								<a href="<?php echo COMPANY_NAME; ?>/brand/add_brand_form" <?php
								if ($currentPage == 'add_brand_form' || $currentPage == 'edit_brand_form') {
									echo 'class="active"';
								}
								?>>
									<span class="list-icon">&nbsp;</span><?php if ($this->lang->line('admin_menu_add_new_maker') != '') echo stripslashes($this->lang->line('admin_menu_add_new_maker')); else echo 'Add New Maker'; ?>
								</a>
							</li>							
                            <li>
                                <a href="<?php echo COMPANY_NAME; ?>/brand/display_model_list" <?php
                                if ($currentPage == 'display_model_list') {
                                    echo 'class="active"';
                                }
                                ?>>
                                    <span class="list-icon">&nbsp;</span><?php if ($this->lang->line('admin_menu_model_list') != '') echo stripslashes($this->lang->line('admin_menu_model_list')); else echo 'Model List'; ?>
                                </a>
                            </li>
							<li>
								<a href="<?php echo COMPANY_NAME; ?>/brand/add_edit_model" <?php
								if ($currentPage == 'add_edit_model') {
									echo 'class="active"';
								}
								?>>
									<span class="list-icon">&nbsp;</span><?php if ($this->lang->line('admin_menu_add_new_model') != '') echo stripslashes($this->lang->line('admin_menu_add_new_model')); else echo 'Add New Model'; ?>
								</a>
							</li>
                        </ul>
                    </li>
              
            </ul>
        </div>
    </div>
</div>
</div>


