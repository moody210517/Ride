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
<div class="" style="position:relative">
	<div id="left_bar" >
	
		<div class="logo">
			<img src="images/logo/<?php echo $logo;?>" alt="<?php echo $siteTitle;?>" width="90px" title="<?php echo $siteTitle;?>">
		</div>
	
	
		<div id="sidebar">
			<div id="secondary_nav">
				<ul id="sidenav" class="accordion_mnu collapsible">
					<li>
						<a href="<?php echo base_url().OPERATOR_NAME; ?>/dashboard/display_dashboard" <?php if ($currentUrl == 'dashboard') { echo 'class="active"'; } ?>>
							<span class="nav_icon computer_imac"></span> 
							<?php if ($this->lang->line('admin_menu_dashboard') != '') echo stripslashes($this->lang->line('admin_menu_dashboard')); else echo 'Dashboard'; ?>
						</a>
					</li>
					<!--<li>
						<h6 style="margin: 10px 0;padding-left:10px; font-size:13px; font-weight:bold;color:#333; text-transform:uppercase; "><?php if ($this->lang->line('admin_menu_managements') != '') echo stripslashes($this->lang->line('admin_menu_managements')); else echo 'Managements'; ?></h6>
					</li>-->

					<li>
						<a href="<?php echo $current_url; ?>" <?php if ($currentUrl == 'settings') { echo 'class="active"'; } ?>>
							<span class="nav_icon admin_user"></span><?php if ($this->lang->line('admin_menu_settings') != '') echo stripslashes($this->lang->line('admin_menu_settings')); else echo 'Settings'; ?><span class="up_down_arrow">&nbsp;</span>
						</a>
						<ul class="acitem" <?php if ($currentUrl == 'settings') { echo 'style="display: block;"'; } else { echo 'style="display: none;"'; } ?>>
							<li>
								<a href="<?php echo OPERATOR_NAME; ?>/settings/edit_profile_form" <?php if ($currentPage == 'edit_profile_form') { echo 'class="active"';}?>>
									<span class="list-icon">&nbsp;</span><?php if ($this->lang->line('dash_operator_profile_settings') != '') echo stripslashes($this->lang->line('dash_operator_profile_settings')); else echo 'Profile Settings'; ?>
								</a>
							</li>
							<li>
								<a href="<?php echo OPERATOR_NAME; ?>/settings/change_password" <?php if ($currentPage == 'change_password') { echo 'class="active"'; } ?>>
									<span class="list-icon">&nbsp;</span><?php if ($this->lang->line('admin_menu_change_password') != '') echo stripslashes($this->lang->line('admin_menu_change_password')); else echo 'Change Password'; ?>
								</a>
							</li>
						</ul>
					</li>
								
					<li>
						<a href="<?php echo $current_url; ?>" <?php if ($currentUrl == 'map') { echo 'class="active"'; }?>>
							<span class="nav_icon marker map-new"></span> <?php if ($this->lang->line('admin_menu_map_view') != '') echo stripslashes($this->lang->line('admin_menu_map_view')); else echo 'Map View'; ?><span class="up_down_arrow">&nbsp;</span>
						</a>
						<ul class="acitem" <?php if ($currentUrl == 'map') { echo 'style="display: block;"'; } else { echo 'style="display: none;"'; } ?>>
							<li>
								<a href="<?php echo OPERATOR_NAME; ?>/map/map_avail_drivers" <?php if ($currentPage == 'map_avail_drivers') { echo 'class="active"'; } ?>>
									<span class="list-icon">&nbsp;</span><?php if ($this->lang->line('admin_menu_view_available_drivers') != '') echo stripslashes($this->lang->line('admin_menu_view_available_drivers')); else echo 'View available drivers';?>
								</a>
							</li>
							<li>
								<a href="<?php echo OPERATOR_NAME; ?>/map/map_avail_users" <?php if ($currentPage == 'map_avail_users') { echo 'class="active"'; } ?>>
									<span class="list-icon">&nbsp;</span><?php if ($this->lang->line('admin_menu_view_available_users') != '') echo stripslashes($this->lang->line('admin_menu_view_available_users')); else echo 'View available users'; ?>
								</a>
							</li>
						</ul>
					</li>
					
					<li>
						<a href="<?php echo $current_url; ?>" <?php if (($currentUrl == 'drivers' || $currentPage == 'view_driver_reviews') && ($currentPage != 'add_edit_category_types' && $currentPage != 'add_edit_category' && $currentPage != 'display_drivers_category' && $currentPage != 'edit_language_category')) { echo 'class="active"'; } ?>>
							<span class="nav_icon users"></span> <?php if ($this->lang->line('admin_menu_drivers') != '') echo stripslashes($this->lang->line('admin_menu_drivers')); else echo 'Drivers'; ?><span class="up_down_arrow">&nbsp;</span>
						</a>
						<ul class="acitem" <?php if (($currentUrl == 'drivers' || $currentPage == 'view_driver_reviews') && ($currentPage != 'add_edit_category_types' && $currentPage != 'add_edit_category' && $currentPage != 'display_drivers_category' && $currentPage != 'edit_language_category')) { echo 'style="display: block;"'; } else { echo 'style="display: none;"'; }?>>
							<li>
								<a href="<?php echo OPERATOR_NAME; ?>/drivers/display_driver_dashboard" <?php if ($currentPage == 'display_driver_dashboard') { echo 'class="active"';}?>>
									<span class="list-icon">&nbsp;</span><?php if ($this->lang->line('admin_menu_drivers_dashboard') != '') echo stripslashes($this->lang->line('admin_menu_drivers_dashboard')); else echo 'Drivers Dashboard'; ?>
								</a>
							</li>
							<li>
								<a href="<?php echo OPERATOR_NAME; ?>/drivers/display_drivers_list" <?php if ($currentPage == 'display_drivers_list' || $currentPage == 'edit_driver_form' || $currentPage == 'change_password_form' || $currentPage == 'view_driver' || $currentPage == 'banking' || $currentPage == 'view_driver_reviews' || $currentPage == 'display_rides') { echo 'class="active"'; } ?>>
									<span class="list-icon">&nbsp;</span><?php if ($this->lang->line('admin_menu_drivers_list') != '') echo stripslashes($this->lang->line('admin_menu_drivers_list')); else echo 'Drivers List'; ?>
								</a>
							</li>
						 
							<li>
								<a href="<?php echo OPERATOR_NAME; ?>/drivers/add_driver_form" <?php if ($currentPage == 'add_driver_form') { echo 'class="active"'; }?>>
									<span class="list-icon">&nbsp;</span><?php if ($this->lang->line('admin_menu_add_driver') != '') echo stripslashes($this->lang->line('admin_menu_add_driver')); else echo 'Add Driver'; ?>
								</a>
							</li>
						</ul>
					</li>
								
								
										  
                    <li>
                        <a href="<?php echo $current_url; ?>" <?php
                        if ($currentUrl == 'rides') {
                            echo 'class="active"';
                        }
                        ?>>
                            <span class="nav_icon car-icon"></span> <?php if ($this->lang->line('admin_menu_rides') != '') echo stripslashes($this->lang->line('admin_menu_rides')); else echo 'Rides'; ?><span class="up_down_arrow">&nbsp;</span>
                        </a>
                        <ul class="acitem" <?php
                        $ride_action='';
                        if ($currentUrl == 'trip') {
                            $ride_action=$this->input->get('act');
                            echo 'style="display: block;"';
                        } else {
                            echo 'style="display: none;"';
                        }
                        ?>>
							<li>
                                <a href="<?php echo OPERATOR_NAME;?>/trip/ride_dashboard" <?php
                                if ($currentPage == 'ride_dashboard' || $currentPage == 'map_unfilled_rides') {
                                    echo 'class="active"';
                                }
                                ?>>
                                    <span class="list-icon">&nbsp;</span><?php if ($this->lang->line('rides_dashboard') != '') echo stripslashes($this->lang->line('rides_dashboard')); else echo 'Rides Dashboard'; ?>
                                </a>
                            </li>
							
							<li>
                                <a href="<?php echo OPERATOR_NAME;?>/trip/rides_grid_view" <?php
                                if ($currentPage == 'rides_grid_view') {
                                    echo 'class="active"';
                                }
                                ?>>
                                    <span class="list-icon">&nbsp;</span><?php if ($this->lang->line('rides_grid_view') != '') echo stripslashes($this->lang->line('rides_grid_view')); else echo 'Rides Grid View'; ?>
                                </a>
                            </li>
							
                            <li>
                                <a href="<?php echo OPERATOR_NAME;?>/trip/display_trips?act=Booked" <?php
                                if ($ride_action == 'Booked') {
                                    echo 'class="active"';
                                }
                                ?>>
                                    <span class="list-icon">&nbsp;</span><?php if ($this->lang->line('admin_menu_just_booked') != '') echo stripslashes($this->lang->line('admin_menu_just_booked')); else echo 'Just Booked'; ?>
                                </a>
                            </li>
                            <li>
                                <a href="<?php echo OPERATOR_NAME;?>/trip/display_trips?act=OnRide" <?php
                                if ($ride_action == 'OnRide') {
                                    echo 'class="active"';
                                }
                                ?>>
                                    <span class="list-icon">&nbsp;</span><?php if ($this->lang->line('admin_menu_on_rides') != '') echo stripslashes($this->lang->line('admin_menu_on_rides')); else echo 'On Rides'; ?>
                                </a>
                            </li>
                            <li>
                                <a href="<?php echo OPERATOR_NAME;?>/trip/display_trips?act=Completed" <?php
                                if ($ride_action == 'Completed') {
                                    echo 'class="active"';
                                }
                                ?>>
                                    <span class="list-icon">&nbsp;</span><?php if ($this->lang->line('admin_menu_completed_rides') != '') echo stripslashes($this->lang->line('admin_menu_completed_rides')); else echo 'Completed Rides'; ?>
                                </a>
                            </li>
                            <li>
                                <a href="<?php echo OPERATOR_NAME;?>/trip/display_trips?act=Cancelled" <?php
                                if ($ride_action == 'Cancelled' || $ride_action == 'riderCancelled' || $ride_action == 'driverCancelled') {
                                    echo 'class="active"';
                                }
                                ?>>
                                    <span class="list-icon">&nbsp;</span><?php if ($this->lang->line('admin_menu_cancelled_rides') != '') echo stripslashes($this->lang->line('admin_menu_cancelled_rides')); else echo 'Cancelled Rides'; ?>
                                </a>
                            </li>
							
							<li>
                                <a href="<?php echo OPERATOR_NAME;?>/trip/display_trips?act=Expired" <?php
                                if ($ride_action == 'Expired') {
                                    echo 'class="active"';
                                }
                                ?>>
                                    <span class="list-icon">&nbsp;</span><?php if ($this->lang->line('admin_menu_expired_rides') != '') echo stripslashes($this->lang->line('admin_menu_expired_rides')); else echo 'Expired Rides'; ?>
                                </a>
                            </li>
							
							<li>
                                <a href="<?php echo OPERATOR_NAME;?>/trip/search_ride" <?php
                                if ($currentPage == 'search_ride' || $currentPage == 'cancelling_ride' || $currentPage == 'end_ride_form') {
                                    echo 'class="active"';
                                }
                                ?>>
                                    <span class="list-icon">&nbsp;</span><?php if ($this->lang->line('admin_search_ride') != '') echo stripslashes($this->lang->line('admin_search_ride')); else echo 'Search Ride'; ?>
                                </a>
                            </li>
							<li>
                                <a href="<?php echo OPERATOR_NAME;?>/trip/init_booking_form" <?php
                                if ($currentPage == 'init_booking_form' || $currentPage == 'book_trip') {
                                    echo 'class="active"';
                                }
                                ?>>
                                    <span class="list-icon">&nbsp;</span><?php if ($this->lang->line('rider_book_ride') != '') echo stripslashes($this->lang->line('rider_book_ride')); else echo 'Book Ride'; ?>
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
                    </li>
                
					<li>
						<a href="<?php echo $current_url; ?>" <?php if ($currentUrl == 'notification' || $currentPage == 'display_notification_user_list') { echo 'class="active"'; } ?>>
						<span class="nav_icon users"></span> <?php if ($this->lang->line('admin_menu_notification') != '') echo stripslashes($this->lang->line('admin_menu_notification')); else echo 'Notification'; ?><span class="up_down_arrow">&nbsp;</span>
						</a>
						<ul class="acitem" <?php if ($currentUrl == 'notification' || $currentPage == 'display_notification_user_list' || $currentPage == 'display_notification_driver_list') { echo 'style="display: block;"'; } else { echo 'style="display: none;"'; } ?>>
								
							<li>
								<a href="<?php echo OPERATOR_NAME; ?>/notification/display_notification_driver_list" <?php if ($currentPage == 'display_notification_driver_list') { echo 'class="active"'; } ?>>
								<span class="list-icon"></span> <?php if ($this->lang->line('admin_menu_drivers') != '') echo stripslashes($this->lang->line('admin_menu_drivers')); else echo 'Drivers'; ?>
								</a>
							</li>
							<li>
								<a href="<?php echo OPERATOR_NAME; ?>/notification/display_notification_user_list" <?php if ($currentPage == 'display_notification_user_list') { echo 'class="active"'; } ?>>
								<span class="list-icon"></span> <?php if ($this->lang->line('admin_menu_users') != '') echo stripslashes($this->lang->line('admin_menu_users')); else echo 'Users'; ?>
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
                                <a href="<?php echo OPERATOR_NAME; ?>/brand/display_brand_list" <?php
                                if ($currentPage == 'display_brand_list') {
                                    echo 'class="active"';
                                }
                                ?>>
                                    <span class="list-icon">&nbsp;</span><?php if ($this->lang->line('admin_menu_makers_list') != '') echo stripslashes($this->lang->line('admin_menu_makers_list')); else echo 'Makers List'; ?>
                                </a>
                            </li>
                            
                                <li>
                                    <a href="<?php echo OPERATOR_NAME; ?>/brand/add_brand_form" <?php
                                    if ($currentPage == 'add_brand_form' || $currentPage == 'edit_brand_form') {
                                        echo 'class="active"';
                                    }
                                    ?>>
                                        <span class="list-icon">&nbsp;</span><?php if ($this->lang->line('admin_menu_add_new_maker') != '') echo stripslashes($this->lang->line('admin_menu_add_new_maker')); else echo 'Add New Maker'; ?>
                                    </a>
                                </li>							
                            <li>
                                <a href="<?php echo OPERATOR_NAME; ?>/brand/display_model_list" <?php
                                if ($currentPage == 'display_model_list') {
                                    echo 'class="active"';
                                }
                                ?>>
                                    <span class="list-icon">&nbsp;</span><?php if ($this->lang->line('admin_menu_model_list') != '') echo stripslashes($this->lang->line('admin_menu_model_list')); else echo 'Model List'; ?>
                                </a>
                            </li>
							<li>
								<a href="<?php echo OPERATOR_NAME; ?>/brand/add_edit_model" <?php
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


