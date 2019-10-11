<?php 
$currentUrl = $this->uri->segment(2, 0);
$currentPage = $this->uri->segment(3, 0);
if ($currentUrl == '') {
    $currentUrl = 'dashboard';
}
if ($currentPage == '') {
    $currentPage = 'dashboard';
}
extract($privileges);
$current_url = $_SERVER['REQUEST_URI'];
?>

<div class="" style="position:relative">

<div id="left_bar" >

	<div class="logo">
				<img src="images/logo/<?php echo $logo;?>" alt="<?php echo $siteTitle;?>" width="90px" title="<?php echo $siteTitle;?>">
			</div>
			
    <div id="sidebar">
	
		
        <div id="secondary_nav" class="cabily_sidenav">
		
			
			
            <ul id="sidenav" class="accordion_mnu collapsible">
                <li>
                    <a href="<?php echo base_url().ADMIN_ENC_URL; ?>/dashboard/admin_dashboard" <?php
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
                <?php
                if ($this->session->userdata(APP_NAME.'_session_admin_mode') == 'admin' || $allPrev == '1') {
                    ?>
                    <li>
                        <a href="<?php echo $current_url; ?>" <?php
                        if ($currentUrl == 'adminlogin') {
                            echo 'class="active"';
                        }
                        ?>>
                            <span class="nav_icon admin_user"></span> <?php if ($this->lang->line('admin_menu_admin') != '') echo stripslashes($this->lang->line('admin_menu_admin')); else echo 'Admin'; ?><span class="up_down_arrow">&nbsp;</span>
                        </a>
                        <ul class="acitem" <?php
                        if ($currentUrl == 'adminlogin') {
                            echo 'style="display: block;"';
                        } else {
                            echo 'style="display: none;"';
                        }
                        ?>>			
                            <li>
                                <a href="<?php echo ADMIN_ENC_URL;?>/adminlogin/change_admin_password_form" <?php
                                if ($currentPage == 'change_admin_password_form') {
                                    echo 'class="active"';
                                }
                                ?>>
                                    <span class="list-icon">&nbsp;</span><?php if ($this->lang->line('admin_menu_change_password') != '') echo stripslashes($this->lang->line('admin_menu_change_password')); else echo 'Change Password'; ?>
                                </a>
                            </li>
                            <li>
                                <a href="<?php echo ADMIN_ENC_URL;?>/adminlogin/admin_global_settings_form" <?php
                                if ($currentPage == 'admin_global_settings_form') {
                                    echo 'class="active"';
                                }
                                ?>>
                                    <span class="list-icon">&nbsp;</span><?php if ($this->lang->line('admin_menu_settings') != '') echo stripslashes($this->lang->line('admin_menu_settings')); else echo 'Settings'; ?>
                                </a>
                            </li>
                            <li>
                                <a href="<?php echo ADMIN_ENC_URL;?>/adminlogin/admin_smtp_settings" <?php
                                if ($currentPage == 'admin_smtp_settings') {
                                    echo 'class="active"';
                                }
                                ?>>
                                    <span class="list-icon">&nbsp;</span><?php if ($this->lang->line('admin_menu_smtp_settings') != '') echo stripslashes($this->lang->line('admin_menu_smtp_settings')); else echo 'SMTP Settings'; ?>
                                </a>
                            </li>
														<li>
                                <a href="<?php echo ADMIN_ENC_URL;?>/adminlogin/admin_site_settings" <?php
                                if ($currentPage == 'admin_site_settings') {
                                    echo 'class="active"';
                                }
                                ?>>
                                    <span class="list-icon">&nbsp;</span><?php if ($this->lang->line('admin_menu_menu_settings') != '') echo stripslashes($this->lang->line('admin_menu_menu_settings')); else echo 'Menu Settings'; ?>
                                </a>
                            </li>
							
							<li>
                                <a href="<?php echo ADMIN_ENC_URL;?>/adminlogin/admin_app_settings" <?php
                                if ($currentPage == 'admin_app_settings') {
                                    echo 'class="active"';
                                }
                                ?>>
                                    <span class="list-icon">&nbsp;</span><?php if ($this->lang->line('admin_menu_app_settings') != '') echo stripslashes($this->lang->line('admin_menu_app_settings')); else echo 'App Settings'; ?>
                                </a>
                            </li>
							
							<li>
                                <a href="<?php echo ADMIN_ENC_URL;?>/adminlogin/admin_pool_settings" <?php if ($currentPage == 'admin_pool_settings') { echo 'class="active"'; } ?>>
                                    <span class="list-icon">&nbsp;</span><?php if ($this->lang->line('admin_menu_pool_settings') != '') echo stripslashes($this->lang->line('admin_menu_pool_settings')); else echo 'Share Pool Settings'; ?>
                                </a>
                            </li>
							
                            <?php if ($this->config->item('currency_name') == '' || $this->config->item('currency_code') == '' || $this->config->item('currency_symbol') == '') { ?>
                                <li>
                                    <a href="<?php echo ADMIN_ENC_URL;?>/adminlogin/admin_currency_settings" <?php
                                    if ($currentPage == 'admin_currency_settings') {
                                        echo 'class="active"';
                                    }
                                    ?>>
                                        <span class="list-icon">&nbsp;</span><?php if ($this->lang->line('currency_setting') != '') echo stripslashes($this->lang->line('admin_menu_admin')); else echo 'Currency Settings'; ?>
                                    </a>
                                </li>
                            <?php } ?>
                            <?php if ($this->config->item('countryId') == '' || $this->config->item('countryName') == '') { ?>
                                <li>
                                    <a href="<?php echo ADMIN_ENC_URL;?>/adminlogin/admin_country_settings" <?php
                                    if ($currentPage == 'admin_country_settings') {
                                        echo 'class="active"';
                                    }
                                    ?>>
                                        <span class="list-icon">&nbsp;</span><?php if ($this->lang->line('currency_setting') != '') echo stripslashes($this->lang->line('currency_setting')); else echo 'Country Settings'; ?>
                                    </a>
                                </li>
                            <?php } ?>
                        </ul>
                    </li>

                    <li><a href="<?php echo $current_url; ?>" <?php
                        if ($currentUrl == 'subadmin') {
                            echo 'class="active"';
                        }
                        ?>><span class="nav_icon user"></span><?php if ($this->lang->line('admin_menu_subadmin') != '') echo stripslashes($this->lang->line('admin_menu_subadmin')); else echo 'Subadmin'; ?> 
                            <span class="up_down_arrow">&nbsp;</span></a>
                        <ul class="acitem" <?php
                        if ($currentUrl == 'subadmin') {
                            echo 'style="display: block;"';
                        } else {
                            echo 'style="display: none;"';
                        }
                        ?>>
                            <li>
                                <a href="<?php echo ADMIN_ENC_URL;?>/subadmin/display_sub_admin" <?php
                                if ($currentPage == 'display_sub_admin') {
                                    echo 'class="active"';
                                }
                                ?>><span class="list-icon">&nbsp;</span><?php if ($this->lang->line('admin_menu_subadmin_list') != '') echo stripslashes($this->lang->line('admin_menu_subadmin_list')); else echo 'Subadmin List'; ?>
                                </a>
                            </li>

                            <li>
                                <a href="<?php echo ADMIN_ENC_URL;?>/subadmin/add_sub_admin_form" <?php
                                if ($currentPage == 'add_sub_admin_form') {
                                    echo 'class="active"';
                                }
                                ?>>
                                    <span class="list-icon">&nbsp;</span><?php if ($this->lang->line('admin_menu_add_new_subadmin') != '') echo stripslashes($this->lang->line('admin_menu_add_new_subadmin')); else echo 'Add New Subadmin'; ?>
                                </a>
                            </li>
                        </ul>
                    </li>

                <?php } else { ?>				

                    <li>
                        <a href="<?php echo $current_url; ?>" <?php
                        if ($currentUrl == 'adminlogin') {
                            echo 'class="active"';
                        }
                        ?>>
                            <span class="nav_icon admin_user"></span><?php if ($this->lang->line('admin_menu_admin') != '') echo stripslashes($this->lang->line('admin_menu_admin')); else echo 'Admin'; ?><span class="up_down_arrow">&nbsp;</span>
                        </a>
                        <ul class="acitem" <?php
                        if ($currentUrl == 'adminlogin') {
                            echo 'style="display: block;"';
                        } else {
                            echo 'style="display: none;"';
                        }
                        ?>>
                            <li>
                                <a href="<?php echo ADMIN_ENC_URL;?>/adminlogin/change_admin_password_form" <?php
                                if ($currentPage == 'change_admin_password_form') {
                                    echo 'class="active"';
                                }
                                ?>>
                                    <span class="list-icon">&nbsp;</span><?php if ($this->lang->line('admin_menu_change_password') != '') echo stripslashes($this->lang->line('admin_menu_change_password')); else echo 'Change Password'; ?>
                                </a>
                            </li>
                            <?php /*
                              <li>
                              <a href="admin/adminlogin/admin_global_settings_form" <?php if($currentPage=='admin_global_settings_form'){ echo 'class="active"';} ?>>
                              <span class="list-icon">&nbsp;</span>Settings
                              </a>
                              </li>
                              <li>
                              <a href="admin/adminlogin/admin_smtp_settings" <?php if($currentPage=='admin_smtp_settings'){ echo 'class="active"';} ?>>
                              <span class="list-icon">&nbsp;</span>SMTP Settings
                              </a>
                              </li>

                              <?php if($this->config->item('currency_name') == '' || $this->config->item('currency_code') == '' || $this->config->item('currency_symbol') == ''){ ?>
                              <li>
                              <a href="admin/adminlogin/admin_currency_settings" <?php if($currentPage=='admin_smtp_settings'){ echo 'class="active"';} ?>>
                              <span class="list-icon">&nbsp;</span>Currency Settings
                              </a>
                              </li>
                              <?php } ?>
                              <?php if($this->config->item('countryId') == '' || $this->config->item('countryName') == ''){ ?>
                              <li>
                              <a href="admin/adminlogin/admin_country_settings" <?php if($currentPage=='admin_country_settings'){ echo 'class="active"';} ?>>
                              <span class="list-icon">&nbsp;</span>Country Settings
                              </a>
                              </li>
                              <?php } ?>
                             */ ?>
                        </ul>
                    </li>

                    <li style="display:none;">  <a href="<?php echo $current_url; ?>" <?php
                        if ($currentUrl == 'subadmin') {
                            echo 'class="active"';
                        }
                        ?>><span class="nav_icon user"></span><?php if ($this->lang->line('admin_menu_subadmin') != '') echo stripslashes($this->lang->line('admin_menu_subadmin')); else echo 'Subadmin'; ?> 
                            <span class="up_down_arrow">&nbsp;</span></a>
                        <ul class="acitem" <?php
                        if ($currentUrl == 'subadmin') {
                            echo 'style="display: block;"';
                        } else {
                            echo 'style="display: none;"';
                        }
                        ?>>
                            <li><a href="<?php echo ADMIN_ENC_URL;?>/subadmin/display_sub_admin" <?php
                                if ($currentPage == 'display_sub_admin') {
                                    echo 'class="active"';
                                }
                                ?>><span class="list-icon">&nbsp;</span><?php if ($this->lang->line('admin_menu_subadmin_list') != '') echo stripslashes($this->lang->line('admin_menu_subadmin_list')); else echo 'Subadmin List'; ?></a></li>
                            <li><a href="<?php echo ADMIN_ENC_URL;?>/subadmin/add_sub_admin_form" <?php
                                if ($currentPage == 'add_sub_admin_form') {
                                    echo 'class="active"';
                                }
                                ?>><span class="list-icon">&nbsp;</span><?php if ($this->lang->line('admin_menu_add_new_subadmin') != '') echo stripslashes($this->lang->line('admin_menu_add_new_subadmin')); else echo 'Add New Subadmin'; ?></a></li>
                        </ul>
                    </li>

                <?php } ?>	
				
				<?php if ((isset($operators) && is_array($operators)) && in_array('0', $operators) || $allPrev == '1') { ?>
					<li>
							<a href="<?php echo $current_url; ?>" <?php if ($currentUrl == 'operators') { echo 'class="active"'; } ?>>
									<span class="nav_icon headphones"></span><?php if ($this->lang->line('admin_menu_operators') != '') echo stripslashes($this->lang->line('admin_menu_operators')); else echo 'Operators'; ?> <span class="up_down_arrow">&nbsp;</span>
							</a>
							<ul class="acitem" <?php if ($currentUrl == 'operators') { echo 'style="display: block;"'; } else { echo 'style="display: none;"'; } ?>>
									<li>
											<a href="<?php echo ADMIN_ENC_URL;?>/operators/display_operators_list" <?php if ($currentPage == 'display_operators_list') {
											echo 'class="active"'; } ?>>
													<span class="list-icon">&nbsp;</span><?php if ($this->lang->line('admin_menu_operators_list') != '') echo stripslashes($this->lang->line('admin_menu_operators_list')); else echo 'Operators list'; ?>
											</a>
									</li>
									<?php 
										if ($allPrev == '1' || in_array('1', $operators)) {?>
									<li>
											<a href="<?php echo ADMIN_ENC_URL;?>/operators/add_edit_operator_form" <?php if ($currentPage == 'add_edit_operator_form') {
											echo 'class="active"'; } ?>>
													<span class="list-icon">&nbsp;</span><?php if ($this->lang->line('admin_menu_add_operator') != '') echo stripslashes($this->lang->line('admin_menu_add_operator')); else echo 'Add Operator'; ?>
											</a>
									</li>
									<?php } ?>
							</ul>
					</li>
				<?php } ?>
				<?php if ((isset($company) && is_array($company)) && in_array('0', $company) || $allPrev == '1') { ?>
				 <li>
                        <a href="<?php echo $current_url; ?>" <?php if ($currentUrl == 'company') { echo 'class="active"'; } ?>>
                            <span class="nav_icon apartment_building"></span><?php if ($this->lang->line('admin_company') != '') echo stripslashes($this->lang->line('admin_company')); else echo 'Company'; ?> <span class="up_down_arrow">&nbsp;</span>
                        </a>
                        <ul class="acitem" <?php if ($currentUrl == 'company') { echo 'style="display: block;"'; } else { echo 'style="display: none;"'; } ?>>
                            <li>
                                <a href="<?php echo ADMIN_ENC_URL;?>/company/display_companylist" <?php if ($currentPage == 'display_companylist'|| $currentPage == 'change_password_form' || $currentPage == 'edit_company'||$currentPage=='view_company'||$currentPage=='banking') { echo 'class="active"'; } ?>>
                                    <span class="list-icon">&nbsp;</span> 
									<?php if ($this->lang->line('admin_company_list') != '') echo stripslashes($this->lang->line('admin_company_list')); else echo 'Company list'; ?>
                                </a>
                            </li>
                            <?php if ($allPrev == '1' || in_array('1', $operators)) { ?>
                            <li>
                                <a href="<?php echo ADMIN_ENC_URL;?>/company/add_edit_company" <?php if ($currentPage == 'add_edit_company') { echo 'class="active"'; } ?>>
                                    <span class="list-icon">&nbsp;</span><?php if ($this->lang->line('admin_add_company') != '') echo stripslashes($this->lang->line('admin_add_company')); else echo 'Add Company'; ?>
                                </a>
                            </li>
                            <?php } ?>
                        </ul>
                  </li>
                 <?php } ?>

                <?php if ((isset($map) && is_array($map)) && in_array('0', $map) || $allPrev == '1') { ?>
                    <li>
                        <a href="<?php echo $current_url; ?>" <?php
                        if ($currentUrl == 'map') {
                            echo 'class="active"';
                        }
                        ?>>
                            <span class="nav_icon marker"></span><?php if ($this->lang->line('admin_menu_map_view') != '') echo stripslashes($this->lang->line('admin_menu_map_view')); else echo 'Map View'; ?> <span class="up_down_arrow">&nbsp;</span>
                        </a>
                        <ul class="acitem" <?php
                        if ($currentUrl == 'map') {
                            echo 'style="display: block;"';
                        } else {
                            echo 'style="display: none;"';
                        }
                        ?>>
                            <li>
                                <a href="<?php echo ADMIN_ENC_URL;?>/map/map_avail_drivers" <?php
                                if ($currentPage == 'map_avail_drivers') {
                                    echo 'class="active"';
                                }
                                ?>>
                                    <span class="list-icon">&nbsp;</span><?php if ($this->lang->line('admin_menu_view_available_drivers') != '') echo stripslashes($this->lang->line('admin_menu_view_available_drivers')); else echo 'View available drivers'; ?>
                                </a>
                            </li>
                             <li>
                                <a href="<?php echo ADMIN_ENC_URL;?>/map/map_avail_users" <?php
                                if ($currentPage == 'map_avail_users') {
                                    echo 'class="active"';
                                }
                                ?>>
                                    <span class="list-icon">&nbsp;</span><?php if ($this->lang->line('admin_menu_view_available_users') != '') echo stripslashes($this->lang->line('admin_menu_view_available_users')); else echo 'View available users'; ?>
                                </a>
                            </li>
                        </ul>
                    </li>
                <?php } ?>		


                <?php
                if ((isset($location) && is_array($location)) && in_array('0', $location) || $allPrev == '1') {
                    if ($this->config->item('countryId') != '' || $this->config->item('countryName') != '') {
                        ?>
                        <li>
                            <a href="<?php echo $current_url; ?>" <?php
                            if ($currentUrl == 'location') {
                                echo 'class="active"';
                            }
                            ?>>
                                <span class="nav_icon globe"></span> <?php if ($this->lang->line('admin_menu_location_fare') != '') echo stripslashes($this->lang->line('admin_menu_location_fare')); else echo 'Location & Fare'; ?><span class="up_down_arrow">&nbsp;</span>
                            </a>
                            <ul class="acitem" <?php
                            if ($currentUrl == 'location') {
                                echo 'style="display: block;"';
                            } else {
                                echo 'style="display: none;"';
                            }
                            ?>>
                                <li>
                                    <a href="<?php echo ADMIN_ENC_URL;?>/location/display_location_list" <?php
                                    if ($currentPage == 'display_location_list' || $currentPage == 'location_fare') {
                                        echo 'class="active"';
                                    }
                                    ?>>
                                        <span class="list-icon">&nbsp;</span><?php if ($this->lang->line('admin_menu_location_list') != '') echo stripslashes($this->lang->line('admin_menu_location_list')); else echo 'Location List'; ?>
                                    </a>
                                </li>
                                <?php if ($allPrev == '1' || in_array('1', $location)) { ?>
                                    <li>
                                        <a href="<?php echo ADMIN_ENC_URL;?>/location/add_edit_location" <?php
                                        if ($currentPage == 'add_edit_location') {
                                            echo 'class="active"';
                                        }
                                        ?>>
                                            <span class="list-icon">&nbsp;</span><?php if ($this->lang->line('admin_menu_add_location') != '') echo stripslashes($this->lang->line('admin_menu_add_location')); else echo 'Add Location'; ?>
                                        </a>
                                    </li>
                                    <?php /*
                                      <li>
                                      <a href="admin/location/display_country_list" <?php if($currentPage=='display_country_list' || $currentPage=='add_edit_country' || $currentPage=='view_country'){ echo 'class="active"';} ?>>
                                      <span class="list-icon">&nbsp;</span>Country List
                                      </a>
                                      </li>
                                     */ ?>
                                <?php } ?>
                            </ul>
                        </li>  
                        <?php
                    }
                }
                ?>
								
								        <?php if ((isset($user) && is_array($user)) && in_array('0', $user) || $allPrev == '1') { ?>
                    <li>
                        <a href="<?php echo $current_url; ?>" <?php
                        if ($currentUrl == 'users' || $currentPage == 'view_user_reviews') {
                            echo 'class="active"';
                        }
                        ?>>
                            <span class="nav_icon users_2"></span> <?php if ($this->lang->line('admin_menu_users') != '') echo stripslashes($this->lang->line('admin_menu_users')); else echo 'Users'; ?><span class="up_down_arrow">&nbsp;</span>
                        </a>
                        <ul class="acitem" <?php
                        if ($currentUrl == 'users' || $currentPage == 'view_user_reviews') {
                            echo 'style="display: block;"';
                        } else {
                            echo 'style="display: none;"';
                        }
                        ?>>
                            <li>
                                <a href="<?php echo ADMIN_ENC_URL;?>/users/display_user_dashboard" <?php
                                if ($currentPage == 'display_user_dashboard') {
                                    echo 'class="active"';
                                }
                                ?>>
                                    <span class="list-icon">&nbsp;</span><?php if ($this->lang->line('admin_menu_dashboard') != '') echo stripslashes($this->lang->line('admin_menu_dashboard')); else echo 'Dashboard'; ?>
                                </a>
                            </li>
                            <li>
                                <a href="<?php echo ADMIN_ENC_URL;?>/users/display_user_list" <?php
                                if (($currentPage == 'display_user_list' || $currentPage == 'view_user_reviews') && $this->input->get('user_type') != 'deleted') {
                                    echo 'class="active"';
                                }
                                ?>>
                                    <span class="list-icon">&nbsp;</span><?php if ($this->lang->line('admin_menu_users_list') != '') echo stripslashes($this->lang->line('admin_menu_users_list')); else echo 'Users List'; ?>
                                </a>
                            </li>
							        <li>
                                <a href="<?php echo ADMIN_ENC_URL;?>/users/display_user_list?user_type=deleted" <?php
                                if (($currentPage == 'display_user_list' || $currentPage == 'view_user_reviews') && $this->input->get('user_type') == 'deleted') {
                                    echo 'class="active"';
                                }
                                ?>>
                                    <span class="list-icon">&nbsp;</span><?php if ($this->lang->line('admin_menu_deleted_users_list') != '') echo stripslashes($this->lang->line('admin_menu_deleted_users_list')); else echo 'Deleted Users List'; ?>
                                </a>
                            </li>
														<?php if ($allPrev == '1' || in_array('1', $user)) { ?>
                                <li>
                                    <a href="<?php echo ADMIN_ENC_URL;?>/users/add_user_form" <?php
                                    if ($currentPage == 'add_user_form') {
                                        echo 'class="active"';
                                    }
                                    ?>>
                                        <span class="list-icon">&nbsp;</span><?php if ($this->lang->line('admin_menu_add_user') != '') echo stripslashes($this->lang->line('admin_menu_add_user')); else echo 'Add User'; ?>
                                    </a>
                                </li>
														<?php } ?>
                        </ul>
                    </li>
                <?php } ?>
                <?php if ((isset($driver) && is_array($driver)) && in_array('0', $driver) || $allPrev == '1') { ?>
                    <li>
                        <a href="<?php echo $current_url; ?>" <?php
                        if (($currentUrl == 'drivers' || $currentPage == 'view_driver_reviews') && ($currentPage != 'add_edit_category_types' && $currentPage != 'add_edit_category' && $currentPage != 'display_drivers_category' && $currentPage != 'edit_language_category')) {
                            echo 'class="active"';
                        }
                        ?>>
                            <span class="nav_icon admin_user"></span> <?php if ($this->lang->line('admin_menu_drivers') != '') echo stripslashes($this->lang->line('admin_menu_drivers')); else echo 'Drivers'; ?><span class="up_down_arrow">&nbsp;</span>
                        </a>
                        <ul class="acitem" <?php
                        if (($currentUrl == 'drivers' || $currentPage == 'view_driver_reviews') && ($currentPage != 'add_edit_category_types' && $currentPage != 'add_edit_category' && $currentPage != 'display_drivers_category' && $currentPage != 'edit_language_category')) {
                            echo 'style="display: block;"';
                        } else {
                            echo 'style="display: none;"';
                        }
                        ?>>
                            <li>
                                <a href="<?php echo ADMIN_ENC_URL;?>/drivers/display_driver_dashboard" <?php
                                if ($currentPage == 'display_driver_dashboard') {
                                    echo 'class="active"';
                                }
                                ?>>
                                    <span class="list-icon">&nbsp;</span><?php if ($this->lang->line('admin_menu_drivers_dashboard') != '') echo stripslashes($this->lang->line('admin_menu_drivers_dashboard')); else echo 'Drivers Dashboard'; ?>
                                </a>
                            </li>
                            <li>
                                <a href="<?php echo ADMIN_ENC_URL;?>/drivers/display_drivers_list" <?php
                                if ($currentPage == 'display_drivers_list' || $currentPage == 'edit_driver_form' || $currentPage == 'change_password_form' || $currentPage == 'view_driver' || $currentPage == 'banking' || $currentPage == 'view_driver_reviews') {
                                    echo 'class="active"';
                                }
                                ?>>
                                    <span class="list-icon">&nbsp;</span><?php if ($this->lang->line('admin_menu_drivers_list') != '') echo stripslashes($this->lang->line('admin_menu_drivers_list')); else echo 'Drivers List'; ?>
                                </a>
                            </li>
                            <?php if ($allPrev == '1' || in_array('1', $driver)) { ?>
                                <li>
                                    <a href="<?php echo ADMIN_ENC_URL;?>/drivers/add_driver_form" <?php
                                    if ($currentPage == 'add_driver_form') {
                                        echo 'class="active"';
                                    }
                                    ?>>
                                        <span class="list-icon">&nbsp;</span><?php if ($this->lang->line('admin_menu_add_driver') != '') echo stripslashes($this->lang->line('admin_menu_add_driver')); else echo 'Add Driver'; ?>
                                    </a>
                                </li>
                                <li>
                                    <a href="<?php echo ADMIN_ENC_URL;?>/drivers/display_unregister_drivers_list" <?php
                                    if ($currentPage == 'display_unregister_drivers_list' || $currentPage == 'view_unregisterdriver') {
                                        echo 'class="active"';
                                    }
                                    ?>>
                                        <span class="list-icon">&nbsp;</span><?php if ($this->lang->line('admin_unregister_drivers') != '') echo stripslashes($this->lang->line('admin_unregister_drivers')); else echo 'UnRegistered Drivers'; ?>
                                    </a>
                                </li>
                            <?php } ?>
						</ul>
                    </li>
                <?php } ?>


								   <?php
                if ((isset($rides) && is_array($rides)) && in_array('0', $rides) || $allPrev == '1') {
                    $ride_action = $this->input->get('act');
                    ?>
                    <li>
                        <a href="<?php echo $current_url; ?>" <?php
                        if ($currentUrl == 'rides') {
                            echo 'class="active"';
                        }
                        ?>>
                            <span class="nav_icon car-icon"></span> <?php if ($this->lang->line('admin_menu_rides') != '') echo stripslashes($this->lang->line('admin_menu_rides')); else echo 'Rides'; ?><span class="up_down_arrow">&nbsp;</span>
                        </a>
                        <ul class="acitem" <?php
                        if ($currentUrl == 'rides') {
                            echo 'style="display: block;"';
                        } else {
                            echo 'style="display: none;"';
                        }
                        ?>>
							<li>
                                <a href="<?php echo ADMIN_ENC_URL;?>/rides/ride_dashboard" <?php
                                if ($currentPage == 'ride_dashboard' || $currentPage == 'map_unfilled_rides') {
                                    echo 'class="active"';
                                }
                                ?>>
                                    <span class="list-icon">&nbsp;</span><?php if ($this->lang->line('rides_dashboard') != '') echo stripslashes($this->lang->line('rides_dashboard')); else echo 'Rides Dashboard'; ?>
                                </a>
                            </li>
							
							<li>
                                <a href="<?php echo ADMIN_ENC_URL;?>/rides/rides_grid_view" <?php
                                if ($currentPage == 'rides_grid_view') {
                                    echo 'class="active"';
                                }
                                ?>>
                                    <span class="list-icon">&nbsp;</span><?php if ($this->lang->line('rides_grid_view') != '') echo stripslashes($this->lang->line('rides_grid_view')); else echo 'Rides Grid View'; ?>
                                </a>
                            </li>
							
                            <li>
                                <a href="<?php echo ADMIN_ENC_URL;?>/rides/display_rides?act=Booked" <?php
                                if ($ride_action == 'Booked') {
                                    echo 'class="active"';
                                }
                                ?>>
                                    <span class="list-icon">&nbsp;</span><?php if ($this->lang->line('admin_menu_just_booked') != '') echo stripslashes($this->lang->line('admin_menu_just_booked')); else echo 'Just Booked'; ?>
                                </a>
                            </li>
                            <li>
                                <a href="<?php echo ADMIN_ENC_URL;?>/rides/display_rides?act=OnRide" <?php
                                if ($ride_action == 'OnRide') {
                                    echo 'class="active"';
                                }
                                ?>>
                                    <span class="list-icon">&nbsp;</span><?php if ($this->lang->line('admin_menu_on_rides') != '') echo stripslashes($this->lang->line('admin_menu_on_rides')); else echo 'On Rides'; ?>
                                </a>
                            </li>
                            <li>
                                <a href="<?php echo ADMIN_ENC_URL;?>/rides/display_rides?act=Completed" <?php
                                if ($ride_action == 'Completed') {
                                    echo 'class="active"';
                                }
                                ?>>
                                    <span class="list-icon">&nbsp;</span><?php if ($this->lang->line('admin_menu_completed_rides') != '') echo stripslashes($this->lang->line('admin_menu_completed_rides')); else echo 'Completed Rides'; ?>
                                </a>
                            </li>
                            <li>
                                <a href="<?php echo ADMIN_ENC_URL;?>/rides/display_rides?act=Cancelled" <?php
                                if ($ride_action == 'Cancelled' || $ride_action == 'riderCancelled' || $ride_action == 'driverCancelled') {
                                    echo 'class="active"';
                                }
                                ?>>
                                    <span class="list-icon">&nbsp;</span><?php if ($this->lang->line('admin_menu_cancelled_rides') != '') echo stripslashes($this->lang->line('admin_menu_cancelled_rides')); else echo 'Cancelled Rides'; ?>
                                </a>
                            </li>
							
							<li>
                                <a href="<?php echo ADMIN_ENC_URL;?>/rides/display_rides?act=Expired" <?php
                                if ($ride_action == 'Expired') {
                                    echo 'class="active"';
                                }
                                ?>>
                                    <span class="list-icon">&nbsp;</span><?php if ($this->lang->line('admin_menu_expired_rides') != '') echo stripslashes($this->lang->line('admin_menu_expired_rides')); else echo 'Expired Rides'; ?>
                                </a>
                            </li>
							
							<li>
                                <a href="<?php echo ADMIN_ENC_URL;?>/rides/search_ride" <?php
                                if ($currentPage == 'search_ride' || $currentPage == 'cancelling_ride' || $currentPage == 'end_ride_form') {
                                    echo 'class="active"';
                                }
                                ?>>
                                    <span class="list-icon">&nbsp;</span><?php if ($this->lang->line('admin_search_ride') != '') echo stripslashes($this->lang->line('admin_search_ride')); else echo 'Search Ride'; ?>
                                </a>
                            </li>
							<li>
                                <a href="<?php echo ADMIN_ENC_URL;?>/rides/init_booking_form" <?php
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
                <?php } ?>

				
				  <?php if ((isset($category) && is_array($category)) && in_array('0', $category) || $allPrev == '1') { ?>
                    <li>
                        <a href="<?php echo $current_url; ?>" <?php
                        if (($currentUrl == 'category')) {
                            echo 'class="active"';
                        }
                        ?>>
                            <span class="nav_icon record"></span> <?php if ($this->lang->line('admin_menu_car_types') != '') echo stripslashes($this->lang->line('admin_menu_car_types')); else echo 'vehicle category'; ?><span class="up_down_arrow">&nbsp;</span>
                        </a>
                        <ul class="acitem" <?php
							 if ($currentUrl == 'category') {
								echo 'style="display: block;"';
							} else {
								echo 'style="display: none;"';
							}
							?>>
                           
							<li>
								<a href="<?php echo ADMIN_ENC_URL;?>/category/category_statistics" <?php
								if ($currentPage == 'category_statistics') {
									echo 'class="active"';
								}
								?>>
									<span class="list-icon"></span> <?php if ($this->lang->line('admin_car_types_statistics') != '') echo stripslashes($this->lang->line('admin_car_types_statistics')); else echo 'Statistics'; ?>
								</a>
							</li>
						   
							<li>
								<a href="<?php echo ADMIN_ENC_URL;?>/category/display_drivers_category" <?php
								if (($currentPage == 'display_drivers_category' || $currentPage == 'add_edit_category_types' || $currentPage == 'add_edit_category' || $currentPage == 'edit_language_category')) {
									echo 'class="active"';
								}
								?>>
									<span class="list-icon"></span> <?php if ($this->lang->line('admin_display_car_types') != '') echo stripslashes($this->lang->line('admin_display_car_types')); else echo 'vehicle category List'; ?>
								</a>
							</li>
							
                        </ul>
                    </li>
                <?php } ?> 
				
				
                <?php if ((isset($vehicle) && is_array($vehicle)) && in_array('0', $vehicle) || $allPrev == '1') { ?>
                    <li>
                        <a href="<?php echo $current_url; ?>" <?php
                        if ($currentUrl == 'vehicle') {
                            echo 'class="active"';
                        }
                        ?>>
                            <span class="nav_icon application_put_co"></span><?php if ($this->lang->line('admin_menu_vehicles') != '') echo stripslashes($this->lang->line('admin_menu_vehicles')); else echo 'Vehicles'; ?> <span class="up_down_arrow">&nbsp;</span>
                        </a>
                        <ul class="acitem" <?php
                        if ($currentUrl == 'vehicle') {
                            echo 'style="display: block;"';
                        } else {
                            echo 'style="display: none;"';
                        }
                        ?>>
                            <li>
                                <a href="<?php echo ADMIN_ENC_URL;?>/vehicle/display_vehicle_list" <?php
                                if ($currentPage == 'display_vehicle_list') {
                                    echo 'class="active"';
                                }
                                ?>>
                                    <span class="list-icon">&nbsp;</span><?php if ($this->lang->line('admin_menu_vehicle_type_list') != '') echo stripslashes($this->lang->line('admin_menu_vehicle_type_list')); else echo 'Vehicle Type List'; ?>
                                </a>
                            </li>
                            <?php if ($allPrev == '1' || in_array('1', $vehicle)) { ?>
                                <li>
                                    <a href="<?php echo ADMIN_ENC_URL;?>/vehicle/add_edit_vehicle_type_form" <?php
                                    if ($currentPage == 'add_edit_vehicle_type_form') {
                                        echo 'class="active"';
                                    }
                                    ?>>
                                        <span class="list-icon">&nbsp;</span><?php if ($this->lang->line('admin_menu_add_new_vehicle_type') != '') echo stripslashes($this->lang->line('admin_menu_add_new_vehicle_type')); else echo 'Add New Vehicle Type'; ?>
                                    </a>
                                </li>
                            <?php } ?>
                        </ul>
                    </li>
                <?php } ?>
				
                <?php if ((isset($brand) && is_array($brand)) && in_array('0', $brand) || $allPrev == '1') { ?>
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
                                <a href="<?php echo ADMIN_ENC_URL;?>/brand/display_brand_list" <?php
                                if ($currentPage == 'display_brand_list') {
                                    echo 'class="active"';
                                }
                                ?>>
                                    <span class="list-icon">&nbsp;</span><?php if ($this->lang->line('admin_menu_makers_list') != '') echo stripslashes($this->lang->line('admin_menu_makers_list')); else echo 'Makers List'; ?>
                                </a>
                            </li>
                            <?php if ($allPrev == '1' || in_array('1', $brand)) { ?>
                                <li>
                                    <a href="<?php echo ADMIN_ENC_URL;?>/brand/add_brand_form" <?php
                                    if ($currentPage == 'add_brand_form') {
                                        echo 'class="active"';
                                    }
                                    ?>>
                                        <span class="list-icon">&nbsp;</span><?php if ($this->lang->line('admin_menu_add_new_maker') != '') echo stripslashes($this->lang->line('admin_menu_add_new_maker')); else echo 'Add New Maker'; ?>
                                    </a>
                                </li>
                            <?php } ?>							
                            <li>
                                <a href="<?php echo ADMIN_ENC_URL;?>/brand/display_model_list" <?php
                                if ($currentPage == 'display_model_list') {
                                    echo 'class="active"';
                                }
                                ?>>
                                    <span class="list-icon">&nbsp;</span><?php if ($this->lang->line('admin_menu_model_list') != '') echo stripslashes($this->lang->line('admin_menu_model_list')); else echo 'Model List'; ?>
                                </a>
                            </li>
                            <?php if ($allPrev == '1' || in_array('1', $brand)) { ?>
                                <li>
                                    <a href="<?php echo ADMIN_ENC_URL;?>/brand/add_edit_model" <?php
                                    if ($currentPage == 'add_edit_model') {
                                        echo 'class="active"';
                                    }
                                    ?>>
                                        <span class="list-icon">&nbsp;</span><?php if ($this->lang->line('admin_menu_add_new_model') != '') echo stripslashes($this->lang->line('admin_menu_add_new_model')); else echo 'Add New Model'; ?>
                                    </a>
                                </li>
                            <?php } ?>
                        </ul>
                    </li>
                <?php } ?> 


        

                <?php if ((isset($revenue) && is_array($revenue)) && in_array('0', $revenue) || $allPrev == '1') { ?>
                    <li>
                        <a href="<?php echo ADMIN_ENC_URL;?>/revenue/display_site_revenue" <?php
                        if ($currentUrl == 'revenue') {
                            echo 'class="active"';
                        }
                        ?>>
                            <span class="nav_icon money"></span><?php if ($this->lang->line('admin_menu_site_earnings') != '') echo stripslashes($this->lang->line('admin_menu_site_earnings')); else echo 'Site Earnings'; ?> 
                        </a>
                    </li>
                <?php } ?>
				
				

                <?php if ((isset($mileage) && is_array($mileage)) && in_array('0', $mileage) || $allPrev == '1') { ?>
                    <li>
                        <a href="<?php echo ADMIN_ENC_URL;?>/mileage/display_mileage_list" <?php if ($currentUrl == 'mileage') { echo 'class="active"'; } ?>>
                            <span class="nav_icon car"></span> <?php if ($this->lang->line('admin_menu_mileage') != '') echo stripslashes($this->lang->line('admin_menu_mileage')); else echo 'Mileage'; ?>
                        </a>
                    </li>
                <?php } ?>
				

             




				
				 <?php if ((isset($reviews) && is_array($reviews)) && in_array('0', $reviews) || $allPrev == '1') { ?>
                    <li>
                        <a href="<?php echo $current_url; ?>" <?php
                        if ($currentUrl == 'reviews'  && ($currentPage != 'view_user_reviews' && $currentPage != 'view_driver_reviews')) {
                            echo 'class="active"';
                        }
                        ?>>
                            <span class="nav_icon feed_sl"></span><?php if ($this->lang->line('admin_menu_review') != '') echo stripslashes($this->lang->line('admin_menu_review')); else echo 'Review'; ?><span class="up_down_arrow">&nbsp;</span>
                        </a>
                        <ul class="acitem" <?php
                        if ($currentUrl == 'reviews'  && ($currentPage != 'view_user_reviews' && $currentPage != 'view_driver_reviews')) {
                            echo 'style="display: block;"';
                        } else {
                            echo 'style="display: none;"';
                        }
                        ?>>
                            <li>
                                <a href="<?php echo ADMIN_ENC_URL;?>/reviews/review_dashboard" <?php
                                if ($currentPage == 'review_dashboard') {
                                    echo 'class="active"';
                                }
                                ?>>
                                    <span class="list-icon">&nbsp;</span><?php if ($this->lang->line('admin_statistics') != '') echo stripslashes($this->lang->line('admin_statistics')); else echo 'Statistics'; ?>
                                </a>
                            </li>
                           
                                <li>
                                    <a href="<?php echo ADMIN_ENC_URL;?>/reviews/display_reviews_options_list" <?php
                                    if ($currentPage == 'display_reviews_options_list') {
                                        echo 'class="active"';
                                    }
                                    ?>>
                                        <span class="list-icon">&nbsp;</span><?php if ($this->lang->line('admin_menu_settings') != '') echo stripslashes($this->lang->line('admin_menu_settings')); else echo 'Settings'; ?>
                                    </a>
                                </li>
                           
                        </ul>
                    </li>
                <?php } ?>

                 <?php if ((isset($documents) && is_array($documents)) && in_array('0', $documents) || $allPrev == '1') { ?>
                    <li>
                        <a href="<?php echo $current_url; ?>" <?php
                        if ($currentUrl == 'documents') {
                            echo 'class="active"';
                        }
                        ?>>
                            <span class="nav_icon documents"></span> <?php if ($this->lang->line('admin_menu_documents') != '') echo stripslashes($this->lang->line('admin_menu_documents')); else echo 'Documents'; ?><span class="up_down_arrow">&nbsp;</span>
                        </a>
                        <ul class="acitem" <?php
                        if ($currentUrl == 'documents') {
                            echo 'style="display: block;"';
                        } else {
                            echo 'style="display: none;"';
                        }
                        ?>>
                            <li>
                                <a href="<?php echo ADMIN_ENC_URL;?>/documents/display_documents_list" <?php
                                if ($currentPage == 'display_documents_list') {
                                    echo 'class="active"';
                                }
                                ?>>
                                    <span class="list-icon">&nbsp;</span><?php if ($this->lang->line('admin_menu_documents_list') != '') echo stripslashes($this->lang->line('admin_menu_documents_list')); else echo 'Documents List'; ?>
                                </a>
                            </li>
                            <?php if ($allPrev == '1' || in_array('1', $documents)) { ?>
                                <li>
                                    <a href="<?php echo ADMIN_ENC_URL;?>/documents/add_edit_document_form" <?php
                                    if ($currentPage == 'add_edit_document_form') {
                                        echo 'class="active"';
                                    }
                                    ?>>
                                        <span class="list-icon">&nbsp;</span><?php if ($this->lang->line('admin_menu_add_new_documents') != '') echo stripslashes($this->lang->line('admin_menu_add_new_documents')); else echo 'Add New Documents'; ?>
                                    </a>
                                </li>
                            <?php } ?>
                        </ul>
                    </li>
                <?php } ?>				

                <?php if ((isset($promocode) && is_array($promocode)) && in_array('0', $promocode) || $allPrev == '1') { ?>
                    <li>
                        <a href="<?php echo $current_url; ?>" <?php
                        if ($currentUrl == 'promocode') {
                            echo 'class="active"';
                        }
                        ?>>
                            <span class="nav_icon bestseller_sl"></span> <?php if ($this->lang->line('admin_menu_coupon_codes') != '') echo stripslashes($this->lang->line('admin_menu_coupon_codes')); else echo 'Coupon Codes'; ?><span class="up_down_arrow">&nbsp;</span>
                        </a>
                        <ul class="acitem" <?php
                        if ($currentUrl == 'promocode') {
                            echo 'style="display: block;"';
                        } else {
                            echo 'style="display: none;"';
                        }
                        ?>>
                            <li>
                                <a href="<?php echo ADMIN_ENC_URL;?>/promocode/promocode" <?php
                                if ($currentPage == 'display_promocodes') {
                                    echo 'class="active"';
                                }
                                ?>>
                                    <span class="list-icon">&nbsp;</span><?php if ($this->lang->line('admin_menu_coupon_code_list') != '') echo stripslashes($this->lang->line('admin_menu_coupon_code_list')); else echo 'Coupon code List'; ?>
                                </a>
                            </li>
                            <?php if ($allPrev == '1' || in_array('1', $promocode)) { ?>
                                <li>
                                    <a href="<?php echo ADMIN_ENC_URL;?>/promocode/add_promocode_form" <?php
                                    if ($currentPage == 'add_promocode_form') {
                                        echo 'class="active"';
                                    }
                                    ?>>
                                        <span class="list-icon">&nbsp;</span><?php if ($this->lang->line('admin_menu_add_coupon_code') != '') echo stripslashes($this->lang->line('admin_menu_add_coupon_code')); else echo 'Add Coupon code'; ?>
                                    </a>
                                </li>
                            <?php } ?>
                        </ul>
                    </li>
                <?php } ?>


                <?php if ((isset($cancellation) && is_array($cancellation)) && in_array('0', $cancellation) || $allPrev == '1') { ?>
                    <li>
                        <a href="<?php echo $current_url; ?>" <?php
                        if ($currentUrl == 'cancellation') {
                            echo 'class="active"';
                        }
                        ?>>
                            <span class="nav_icon pencil"></span> <?php if ($this->lang->line('admin_menu_cancellation') != '') echo stripslashes($this->lang->line('admin_menu_cancellation')); else echo 'Cancellation'; ?><span class="up_down_arrow">&nbsp;</span>
                        </a>
                        <ul class="acitem" <?php
                        if ($currentUrl == 'cancellation') {
                            echo 'style="display: block;"';
                        } else {
                            echo 'style="display: none;"';
                        }
                        ?>>
						<?php 
							$reason_for = '';
							if($this->uri->segment(5, 0) !='' && $currentUrl == 'cancellation'){
								if($this->uri->segment(5, 0) == 'user'){
									$reason_for = 'user';
								}else if($this->uri->segment(5, 0) == 'driver'){
									$reason_for = 'driver';
								}
							}
						?>
							<?php if ($allPrev == '1' || in_array('0', $cancellation)) { ?>
                            <li>
                                <a href="<?php echo ADMIN_ENC_URL;?>/cancellation/user_cancellation_types" <?php
                                if ($currentPage == 'user_cancellation_types' || $reason_for == 'user') {
                                    echo 'class="active"';
                                }
                                ?>>
                                    <span class="list-icon">&nbsp;</span><?php if ($this->lang->line('admin_menu_user_cancellation_reasons') != '') echo stripslashes($this->lang->line('admin_menu_user_cancellation_reasons')); else echo 'User Cancellation Reasons'; ?>
                                </a>
                            </li>
                                <li>
                                    <a href="<?php echo ADMIN_ENC_URL;?>/cancellation/driver_cancellation_types" <?php
                                    if ($currentPage == 'driver_cancellation_types' || $reason_for == 'driver') {
                                        echo 'class="active"';
                                    }
                                    ?>>
                                        <span class="list-icon">&nbsp;</span><?php if ($this->lang->line('admin_menu_driver_cancellation_reasons') != '') echo stripslashes($this->lang->line('admin_menu_driver_cancellation_reasons')); else echo 'Driver Cancellation Reasons'; ?>
                                    </a>
                                </li>
                            <?php } ?>
                        </ul>
                    </li>
                <?php } ?>


                <?php if ((isset($banner) && is_array($banner)) && in_array('0', $banner) || $allPrev == '1') { ?>
                    <li>
                        <a href="<?php echo $current_url; ?>" <?php
                        if ($currentUrl == 'banner') {
                            echo 'class="active"';
                        }
                        ?>>
                            <span class="nav_icon ipad"></span><?php if ($this->lang->line('admin_menu_banners') != '') echo stripslashes($this->lang->line('admin_menu_banners')); else echo 'Banners'; ?><span class="up_down_arrow">&nbsp;</span>
                        </a>
                        <ul class="acitem" <?php
                        if ($currentUrl == 'banner') {
                            echo 'style="display: block;"';
                        } else {
                            echo 'style="display: none;"';
                        }
                        ?>>
                            <li>
                                <a href="<?php echo ADMIN_ENC_URL;?>/banner/display_banner" <?php
                                if ($currentPage == 'display_banner') {
                                    echo 'class="active"';
                                }
                                ?>>
                                    <span class="list-icon">&nbsp;</span><?php if ($this->lang->line('admin_menu_banners_list') != '') echo stripslashes($this->lang->line('admin_menu_banners_list')); else echo 'Banners List'; ?>
                                </a>
                            </li>
                            <?php if ($allPrev == '1' || in_array('1', $banner)) { ?>
                                <li>
                                    <a href="<?php echo ADMIN_ENC_URL;?>/banner/add_banner_form" <?php
                                    if ($currentPage == 'add_banner_form') {
                                        echo 'class="active"';
                                    }
                                    ?>>
                                        <span class="list-icon">&nbsp;</span><?php if ($this->lang->line('admin_menu_add_banner') != '') echo stripslashes($this->lang->line('admin_menu_add_banner')); else echo 'Add Banner'; ?>
                                    </a>
                                </li>
                            <?php } ?>
                        </ul>
                    </li>
                <?php } ?> 

                <?php if ((isset($cms) && is_array($cms)) && in_array('0', $cms) || $allPrev == '1') { ?>
                    <li>
                        <a href="<?php echo $current_url; ?>" <?php
                        if ($currentUrl == 'cms') {
                            echo 'class="active"';
                        }
                        ?>>
                            <span class="nav_icon documents"></span> <?php if ($this->lang->line('admin_menu_pages') != '') echo stripslashes($this->lang->line('admin_menu_pages')); else echo 'Pages'; ?><span class="up_down_arrow">&nbsp;</span>
                        </a>
                        <ul class="acitem" <?php
                        if ($currentUrl == 'cms') {
                            echo 'style="display: block;"';
                        } else {
                            echo 'style="display: none;"';
                        }
                        ?>>
                            <li>
                                <a href="<?php echo ADMIN_ENC_URL;?>/cms/display_cms" <?php
                                if ($currentPage == 'display_cms') {
                                    echo 'class="active"';
                                }
                                ?>><span class="list-icon">&nbsp;</span><?php if ($this->lang->line('admin_menu_list_of_pages') != '') echo stripslashes($this->lang->line('admin_menu_list_of_pages')); else echo 'List of pages'; ?></a>
                            </li>
                            <?php if ($allPrev == '1' || in_array('1', $cms)) { ?>
                                <li>
                                    <a href="<?php echo ADMIN_ENC_URL;?>/cms/add_cms_form" <?php
                                    if ($currentPage == 'add_cms_form') {
                                        echo 'class="active"';
                                    }
                                    ?>><span class="list-icon">&nbsp;</span><?php if ($this->lang->line('admin_menu_add_main_page') != '') echo stripslashes($this->lang->line('admin_menu_add_main_page')); else echo 'Add Main Page'; ?></a>
                                </li>
                                <li>
                                    <a href="<?php echo ADMIN_ENC_URL;?>/cms/add_subpage_form" <?php
                                    if ($currentPage == 'add_subpage_form') {
                                        echo 'class="active"';
                                    }
                                    ?>><span class="list-icon">&nbsp;</span><?php if ($this->lang->line('admin_menu_add_sub_page') != '') echo stripslashes($this->lang->line('admin_menu_add_sub_page')); else echo 'Add Sub Page'; ?></a>
                                </li>
								<li>
                                    <a href="<?php echo ADMIN_ENC_URL;?>/cms/add_landing_page_form" <?php
                                    if ($currentPage == 'add_landing_page_form') {
                                        echo 'class="active"';
                                    }
                                    ?>><span class="list-icon">&nbsp;</span><?php if ($this->lang->line('admin_menu_landing_page') != '') echo stripslashes($this->lang->line('admin_menu_landing_page')); else echo 'Landing Page'; ?></a>
                                </li>
                            <?php } ?>
                        </ul>
                    </li>

                <?php } ?> 

                <?php if ((isset($templates) && is_array($templates)) && in_array('0', $templates) || $allPrev == '1') { ?>                
                    <li>
                        <a href="<?php echo $current_url; ?>" <?php
                        if ($currentUrl == 'templates') {
                            echo 'class="active"';
                        }
                        ?>>
                            <span class="nav_icon mail"></span><?php if ($this->lang->line('admin_menu_templates') != '') echo stripslashes($this->lang->line('admin_menu_templates')); else echo 'Templates'; ?><span class="up_down_arrow">&nbsp;</span>
                        </a>
                        <ul class="acitem" <?php
                        if ($currentUrl == 'templates') {
                            echo 'style="display: block;"';
                        } else {
                            echo 'style="display: none;"';
                        }
                        ?>>						
                            <li>
                                <a href="<?php echo ADMIN_ENC_URL;?>/templates/display_email_template" <?php
                                if ($currentPage == 'display_email_template') {
                                    echo 'class="active"';
                                }
                                ?>>
                                    <span class="list-icon">&nbsp;</span><?php if ($this->lang->line('admin_menu_email_template_list') != '') echo stripslashes($this->lang->line('admin_menu_email_template_list')); else echo 'Email Template List'; ?>
                                </a>
                            </li>
                            <?php if ($allPrev == '1' || in_array('1', $templates)) { ?>
                                <li>
                                    <a href="<?php echo ADMIN_ENC_URL;?>/templates/add_email_template" <?php
                                    if ($currentPage == 'add_email_template') {
                                        echo 'class="active"';
                                    }
                                    ?>>
                                        <span class="list-icon">&nbsp;</span><?php if ($this->lang->line('admin_menu_add_email_template') != '') echo stripslashes($this->lang->line('admin_menu_add_email_template')); else echo 'Add Email Template'; ?>
                                    </a>
                                </li>
                            <?php } ?>
                            <li>
                                  <a href="<?php echo ADMIN_ENC_URL;?>/templates/sms_template_list" <?php if($currentPage=='sms_template_list' || $currentPage=='add_edit_sms_templates_form' || $currentPage=='view_sms_template'){ echo 'class="active"';} ?>>
                                  <span class="list-icon">&nbsp;</span><?php if ($this->lang->line('admin_menu_sms_template') != '') echo stripslashes($this->lang->line('admin_menu_sms_template')); else echo 'SMS Templates List'; ?>
                                  </a>
							</li>
														
							<?php if ($allPrev == '1') { ?>
                                <li>
                                    <a href="<?php echo ADMIN_ENC_URL;?>/templates/invoice_template" <?php
                                    if ($currentPage == 'invoice_template') {
                                        echo 'class="active"';
                                    }
                                    ?>>
                                        <span class="list-icon">&nbsp;</span><?php if($this->lang->line('invoice_template_lang')!='') echo $this->lang->line('invoice_template_lang'); else { ?>Invoice Template<?php }?>
                                    </a>
                                </li>
                            <?php } ?>
                            
                            
                            <?php if ($allPrev == '1' && $this->config->item('share_pooling') == '1') { ?>
                                <li>
                                    <a href="<?php echo ADMIN_ENC_URL;?>/templates/share_pool_invoice_template" <?php
                                    if ($currentPage == 'share_pool_invoice_template') {
                                        echo 'class="active"';
                                    }
                                    ?>>
                                        <span class="list-icon">&nbsp;</span><?php if($this->lang->line('admin_share_pool_invoice')!='') echo $this->lang->line('admin_share_pool_invoice'); else { ?>Share Pool Invoice<?php }?>
                                    </a>
                                </li>
                            <?php } ?>

                            <li>
                                <a href="<?php echo ADMIN_ENC_URL;?>/templates/display_subscribers_list" <?php
                                if ($currentPage == 'display_subscribers_list') {
                                    echo 'class="active"';
                                }
                                ?>>
                                    <span class="list-icon">&nbsp;</span><?php if ($this->lang->line('admin_menu_news_subscribers_list') != '') echo stripslashes($this->lang->line('admin_menu_news_subscribers_list')); else echo 'News Subscribers List'; ?>
                                </a>
                            </li>

                        </ul>
                    </li>
                <?php } ?>


                <?php /* if ((isset($currency) && is_array($currency)) && in_array('0', $currency) || $allPrev == '1'){ ?>
                  <li>
                  <a href="<?php echo $current_url; ?>" <?php if($currentUrl=='currency'){ echo 'class="active"';} ?>>
                  <span class="nav_icon money"></span> Currency<span class="up_down_arrow">&nbsp;</span>
                  </a>
                  <ul class="acitem" <?php if($currentUrl=='currency'){ echo 'style="display: block;"';}else{ echo 'style="display: none;"';} ?>>
                  <li>
                  <a href="admin/currency/display_currency_list" <?php if($currentPage=='display_currency_list' || $currentPage=='location_fare'){ echo 'class="active"';} ?>>
                  <span class="list-icon">&nbsp;</span>Currency List
                  </a>
                  </li>
                  <?php if ($allPrev == '1' || in_array('1', $location)){?>
                  <li>
                  <a href="admin/currency/add_edit_currency" <?php if($currentPage=='add_edit_currency'){ echo 'class="active"';} ?>>
                  <span class="list-icon">&nbsp;</span>Add Currency
                  </a>
                  </li>
                  <?php }?>
                  </ul>
                  </li>
                  <?php } */ ?>



                <?php if ((isset($payment_gateway) && is_array($payment_gateway)) && in_array('0', $payment_gateway) || $allPrev == '1') { ?>
                    <li>
                        <a href="<?php echo ADMIN_ENC_URL;?>/payment_gateway/display_payment_gateway_list" <?php
                        if ($currentPage == 'display_payment_gateway_list' || $currentPage == 'edit_gateway_form') {
                            echo 'class="active"';
                        }
                        ?>>
                            <span class="nav_icon shopping_cart_2">&nbsp;</span><?php if ($this->lang->line('admin_menu_payment_gateway') != '') echo stripslashes($this->lang->line('admin_menu_payment_gateway')); else echo 'Payment Gateway'; ?>
                        </a>
                    </li>
                <?php } ?>

                <?php if ((isset($notification) && is_array($notification)) && in_array('0', $notification) || $allPrev == '1') { ?>
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
                                <a href="<?php echo ADMIN_ENC_URL;?>/notification/display_notification_user_list" <?php
                                if ($currentPage == 'display_notification_user_list') {
                                    echo 'class="active"';
                                }
                                ?>>
                                    <span class="list-icon">&nbsp;</span><?php if ($this->lang->line('admin_menu_users') != '') echo stripslashes($this->lang->line('admin_menu_users')); else echo 'Users'; ?>
                                </a>
                            </li>
                            <li>
                                <a href="<?php echo ADMIN_ENC_URL;?>/notification/display_notification_driver_list" <?php
                                if ($currentPage == 'display_notification_driver_list') {
                                    echo 'class="active"';
                                }
                                ?>>
                                    <span class="list-icon">&nbsp;</span><?php if ($this->lang->line('admin_menu_drivers') != '') echo stripslashes($this->lang->line('admin_menu_drivers')); else echo 'Drivers'; ?>
                                </a>
                            </li>
							<li>
                                <a href="<?php echo ADMIN_ENC_URL;?>/notification/display_notification_templates" <?php
                                if ($currentPage == 'display_notification_templates'||$currentPage == 'edit_notification_template'||$currentPage == 'view_notification_template') {
                                    echo 'class="active"';
                                }
                                ?> >
                                    <span class="list-icon">&nbsp;</span><?php if ($this->lang->line('admin_menu_notification_templates') != '') echo stripslashes($this->lang->line('admin_menu_notification_templates')); else echo 'Notification Templates'; ?>
                                </a>
                            </li>

                        </ul>
                    </li>
                <?php } ?>


                <?php if ((isset($multilang) && is_array($multilang)) && in_array('0', $multilang) || $allPrev == '1') { ?>
                    <li>
                        <a href="<?php echo $current_url; ?>" <?php
                        if ($currentUrl == 'multilanguage' || $currentPage == 'display_language_list') {
                            echo 'class="active"';
                        }
                        ?>>
                            <span class="nav_icon cog_3"></span><?php if ($this->lang->line('admin_menu_language_management') != '') echo stripslashes($this->lang->line('admin_menu_language_management')); else echo 'Language Management'; ?> <span class="up_down_arrow">&nbsp;</span>
                        </a>
                        <ul class="acitem" <?php
                        if ($currentUrl == 'multilanguage' || $currentPage == 'display_language_list' || $currentPage == 'mobile_display_language_list') {
                            echo 'style="display: block;"';
                        } else {
                            echo 'style="display: none;"';
                        }
                        ?>>
                            <li>
                                <a href="<?php echo ADMIN_ENC_URL;?>/multilanguage/display_language_list" <?php
                                if ($currentPage == 'display_language_list') {
                                    echo 'class="active"';
                                }
                                ?>>
                                    <span class="list-icon">&nbsp;</span><?php if ($this->lang->line('admin_menu_website') != '') echo stripslashes($this->lang->line('admin_menu_website')); else echo 'Website'; ?>
                                </a>
                            </li>
                            <li>
                                <a href="<?php echo ADMIN_ENC_URL;?>/multilanguage/mobile_edit_language" <?php
                                if ($currentPage == 'mobile_edit_language') {
                                    echo 'class="active"';
                                }
                                ?>>
                                    <span class="list-icon">&nbsp;</span><?php if ($this->lang->line('admin_menu_mobile') != '') echo stripslashes($this->lang->line('admin_menu_mobile')); else echo 'Mobile'; ?>
                                </a>
                            </li>
                            <li>
                                <a href="<?php echo ADMIN_ENC_URL;?>/multilanguage/keyword_edit_language" <?php
                                if ($currentPage == 'keyword_edit_language') {
                                    echo 'class="active"';
                                }
                                ?>>
                                    <span class="list-icon">&nbsp;</span><?php if ($this->lang->line('admin_menu_lang_keywords') != '') echo stripslashes($this->lang->line('admin_menu_lang_keywords')); else echo 'Keywords'; ?>
                                </a>
                            </li>
                            <li>
                                <a href="<?php echo ADMIN_ENC_URL;?>/multilanguage/validation_edit_language" <?php
                                if ($currentPage == 'validation_edit_language') {
                                    echo 'class="active"';
                                }
                                ?>>
                                    <span class="list-icon">&nbsp;</span><?php if ($this->lang->line('admin_menu_lang_validation') != '') echo stripslashes($this->lang->line('admin_menu_lang_validation')); else echo 'Validation'; ?>
                                </a>
                            </li>
							
                            <li>
                                <a href="<?php echo ADMIN_ENC_URL;?>/multilanguage/datetime_edit_language" <?php if ($currentPage == 'datetime_edit_language') { echo 'class="active"'; } ?>>
                                    <span class="list-icon">&nbsp;</span><?php if ($this->lang->line('admin_menu_lang_datetime') != '') echo stripslashes($this->lang->line('admin_menu_lang_datetime')); else echo 'Date & Time'; ?>
                                </a>
                            </li>
							

                        </ul>
                    </li>
                <?php } ?>				
				
				<?php
                if ((isset($referral) && is_array($referral)) && in_array('0', $referral) || $allPrev == '1') {
                    $ride_action = $this->input->get('act');
                    ?>
                    <li>
                        <a href="<?php echo ADMIN_ENC_URL;?>/referral/display_user_referrals" <?php
                        if ($currentUrl == 'referral') {
                            echo 'class="active"';
                        }
                        ?>>
                            <span class="nav_icon money_2"></span><?php if ($this->lang->line('admin_menu_referral_history') != '') echo stripslashes($this->lang->line('admin_menu_referral_history')); else echo 'Referral History'; ?>
                        </a>
                    </li>
                <?php } ?>
			 
			 <?php
                if ((isset($reports) && is_array($reports)) && in_array('0', $reports) || $allPrev == '1') {
                    ?>
                   <li>
                        <a href="<?php echo ADMIN_ENC_URL;?>/reports/display_reports_list" <?php
                        if ($currentUrl == 'reports') {
                            echo 'class="active"';
                        }
                        ?>>
                            <span class="nav_icon mail"></span><?php if ($this->lang->line('admin_reports') != '') echo stripslashes($this->lang->line('admin_reports')); else echo 'Reports/Feedbacks'; ?>
                        </a>
                    </li>
                <?php } ?>
				
				
				<?php if ((isset($testimonials) && is_array($testimonials)) && in_array('0', $testimonials) || $allPrev == '1') { ?>
					<li>
						
						
						
						 <a href="<?php echo $current_url; ?>" <?php
                        if ($currentUrl == 'testimonials' || $currentPage == 'display_testimonials_list') {
                            echo 'class="active"';
                        }
                        ?>>
                            <span class="nav_icon documents"></span>
							<?php if ($this->lang->line('admin_menu_testimonials') != '') echo stripslashes($this->lang->line('admin_menu_testimonials')); else echo 'Testimonials'; ?>
							<span class="up_down_arrow">&nbsp;</span>
                        </a>
						
						

						<ul class="acitem" <?php
						if ($currentUrl == 'testimonials' || $currentPage == 'display_testimonials_list' || $currentPage == 'edit_testimonials') { echo 'style="display: block;"'; } else { echo 'style="display: none;"'; } ?>>
							<li>
								<a href="<?php echo ADMIN_ENC_URL;?>/testimonials/display_testimonials_list" <?php if ($currentPage == 'display_testimonials_list' || $currentPage == 'view_testimonials') { echo 'class="active"'; } ?>>
									<span class="list-icon">&nbsp;</span><?php if ($this->lang->line('admin_menu_display_testimonials') != '') echo stripslashes($this->lang->line('admin_menu_display_testimonials')); else echo 'Display Testimonials'; ?>
								</a>
							</li>
							<li>
								<a href="<?php echo ADMIN_ENC_URL;?>/testimonials/add_edit_testimonials_form" <?php if ($currentPage == 'add_edit_testimonials_form') { echo 'class="active"'; } ?>>
									<span class="list-icon">&nbsp;</span><?php if ($this->lang->line('admin_menu_add_testimonials') != '') echo stripslashes($this->lang->line('admin_menu_add_testimonials')); else echo 'Add Testimonials'; ?>
								</a>
							</li>
						</ul>
					</li>
				<?php } ?>
				
				
				<?php
                /* if ((isset($logs) && is_array($logs)) && in_array('0', $logs) || $allPrev == '1') {
                    ?>
                   <li>
                        <a href="admin/logs/display_logs" <?php
                        if ($currentUrl == 'logs') {
                            echo 'class="active"';
                        }
                        ?>>
                            <span class="nav_icon mail"></span><?php if ($this->lang->line('admin_logs') != '') echo stripslashes($this->lang->line('admin_logs')); else echo 'Logs'; ?>
                        </a>
                    </li>
                <?php } */ ?>

              

            </ul>
        </div>
    </div>
</div>


