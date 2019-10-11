<?php
$this->load->view(COMPANY_NAME.'/templates/header.php');

?> 

<style>


.stat_block{
	padding: 15px 0;
	background:#ffffff;
}
.data_widget{
	margin: 20px 0;
}
.stat_chart .chart_label{
	text-align: right;
}
div#content {
    min-height: inherit;
}


</style>

<div id="content" style="clear:both;">
    <div class="grid_container">		
		
        <div class="grid_12" >
            <div class="widget_wrap">
                <!--<div class="widget_top">
                    <span class="h_icon graph"><span style="display: none;"></span><canvas width="16" height="16"></canvas></span>
                    <h6><?php if ($this->lang->line('admin_dashboard_ride_statistics') != '') echo stripslashes($this->lang->line('admin_dashboard_ride_statistics')); else echo 'Ride Statistics'; ?></h6>
                </div>-->
                <div class="widget_content">
                    
                        <div class="social_activities cabily_dash">	
							<h6><?php if ($this->lang->line('admin_dashboard_ride_statistics') != '') echo stripslashes($this->lang->line('admin_dashboard_ride_statistics')); else echo 'Ride Statistics'; ?></h6>
                            <div class="activities_s site_statistics">
                                <div class="block_label">
                                    
                                    <small><?php if ($this->lang->line('admin_dashboard_total_rides') != '') echo stripslashes($this->lang->line('admin_dashboard_total_rides')); else echo 'Total Rides'; ?></small>
									<span><?php if (isset($totalRides)) echo $totalRides; ?></span>
                                    <i class="user_icon_font fa fa-car"></i>
									<a href="<?php echo COMPANY_NAME; ?>/rides/display_rides?act=total" class="small-box-footer"><?php if ($this->lang->line('panel_more_info') != '') echo stripslashes($this->lang->line('panel_more_info')); else echo 'More info'; ?> <i class="fa fa-arrow-circle-right"></i></a>
                                </div>
                            </div>								
                             <div class="activities_s site_statistics">
                                <div class="block_label">
                                    
                                    <small><?php if ($this->lang->line('admin_dashboard_completed_rides') != '') echo stripslashes($this->lang->line('admin_dashboard_completed_rides')); else echo 'Completed Rides'; ?></small>
									<span><?php if (isset($completedRides)) echo $completedRides; ?></span>
                                    <i class="user_icon_font fa fa-car"></i>
									<a href="<?php echo COMPANY_NAME; ?>/rides/display_rides?act=Completed" class="small-box-footer"><?php if ($this->lang->line('panel_more_info') != '') echo stripslashes($this->lang->line('panel_more_info')); else echo 'More info'; ?> <i class="fa fa-arrow-circle-right"></i></a>
                                </div>
                            </div>							
                            <div class="activities_s site_statistics">
                                <div class="block_label">
                                    
                                    <small><?php if ($this->lang->line('admin_dashboard_on_rides') != '') echo stripslashes($this->lang->line('admin_dashboard_on_rides')); else echo 'On Rides'; ?></small>
									<span><?php if (isset($onRides)) echo $onRides; ?></span>
                                    <i class="user_icon_font fa fa-car"></i>
									<a href="<?php echo COMPANY_NAME; ?>/rides/display_rides?act=OnRide" class="small-box-footer"><?php if ($this->lang->line('panel_more_info') != '') echo stripslashes($this->lang->line('panel_more_info')); else echo 'More info'; ?> <i class="fa fa-arrow-circle-right"></i></a>
                                </div>
                            </div>								
                            <div class="activities_s site_statistics">
                                <div class="block_label">
                                    
                                    <small><?php if ($this->lang->line('admin_dashboard_rider_denied') != '') echo stripslashes($this->lang->line('admin_dashboard_rider_denied')); else echo 'Rider Denied'; ?></small>
									<span><?php if (isset($riderDeniedRides)) echo $riderDeniedRides; ?></span>
                                    <i class="user_icon_font fa fa-car"></i>
									<a href="<?php echo COMPANY_NAME; ?>/rides/display_rides?act=riderCancelled" class="small-box-footer"><?php if ($this->lang->line('panel_more_info') != '') echo stripslashes($this->lang->line('panel_more_info')); else echo 'More info'; ?> <i class="fa fa-arrow-circle-right"></i></a>
                                </div>
                            </div>								
                            <div class="activities_s site_statistics">
                                <div class="block_label">
                                    
                                    <small><?php if ($this->lang->line('admin_dashboard_driver_denied') != '') echo stripslashes($this->lang->line('admin_dashboard_driver_denied')); else echo 'Driver Denied'; ?></small>
									<span><?php if (isset($driverDeniedRides)) echo $driverDeniedRides; ?></span>
                                    <i class="user_icon_font fa fa-car"></i>
									<a href="<?php echo COMPANY_NAME; ?>/rides/display_rides?act=driverCancelled" class="small-box-footer"><?php if ($this->lang->line('panel_more_info') != '') echo stripslashes($this->lang->line('panel_more_info')); else echo 'More info'; ?> <i class="fa fa-arrow-circle-right"></i></a>
                                </div>
                            </div>								
                            							
                        </div>
                   
                </div>
            </div>
        </div>

        <div class="grid_6" style="margin-top: 20px;">
            <div class="widget_wrap">
                <div class="widget_top">
                    <span class="h_icon graph"></span>
                    <h6><?php if ($this->lang->line('admin_dashboard_rides') != '') echo stripslashes($this->lang->line('admin_dashboard_rides')); else echo 'Rides'; ?></h6>
                </div>
                <div class="widget_content">
                    <div class="stat_block">
                        <div class="stat_chart">							
                            <h4><?php if ($this->lang->line('admin_dashboard_rides_count') != '') echo stripslashes($this->lang->line('admin_dashboard_rides_count')); else echo 'Rides Count'; ?> : <?php if (isset($totalRides)) echo $totalRides; ?></h4>
                            <table>
                                <tbody>
                                    <tr>
                                        <td>
                                            <?php if ($this->lang->line('admin_dashboard_today') != '') echo stripslashes($this->lang->line('admin_dashboard_today')); else echo 'Today'; ?>
                                        </td>
                                        <td>
                                            <?php if (isset($todayRides)) echo $todayRides; ?>
                                        </td>
                                        <?php /* <td class="min_chart">
                                          <span class="bar">20,30,50,200,250,280,350</span>
                                          </td> */ ?>
                                    </tr>
                                    <tr>
                                        <td>
                                            <?php if ($this->lang->line('admin_dashboard_this_month') != '') echo stripslashes($this->lang->line('admin_dashboard_this_month')); else echo 'This Month'; ?>
                                        </td>
                                        <td>
                                            <?php if (isset($monthRides)) echo $monthRides; ?>
                                        </td>
                                        <?php /* <td class="min_chart">
                                          <span class="line">20,30,50,200,250,280,350</span>
                                          </td> */ ?>
                                    </tr>
                                    <tr>
                                        <td>
                                            <?php if ($this->lang->line('admin_dashboard_this_year') != '') echo stripslashes($this->lang->line('admin_dashboard_this_year')); else echo 'This Year'; ?>
                                        </td>
                                        <td>
                                            <?php if (isset($yearRides)) echo $yearRides; ?>
                                        </td>
                                        <?php /* <td class="min_chart">
                                          <span class="line">20,30,50,200,250,280,350</span>
                                          </td> */ ?>
                                    </tr>
                                </tbody>
                            </table>
                            <div class="pie_chart">
                                <?php
                                $completedRidesPercent = 0.00;
                                if (isset($totalRides) && isset($completedRides)) {
                                    if ($totalRides > 0) {
                                        $completedRidesPercent = ($completedRides * 100) / $totalRides;
                                    }
                                }
								
                                ?>
                                <span class="inner_circle"><?php echo round($completedRidesPercent, 1) . '%'; ?></span>
                                <span class="pie"><?php if (isset($completedRides)) echo $completedRides; ?>/<?php if (isset($totalRides)) echo $totalRides; ?></span>
                            </div>
                            <div class="chart_label">
                                <ul>
                                    <li><span class="new_visits"></span><?php if ($this->lang->line('admin_dashboard_completed_rides') != '') echo stripslashes($this->lang->line('admin_dashboard_completed_rides')); else echo 'Completed Rides'; ?>: <?php if (isset($completedRides)) echo $completedRides; ?></li>
                                    <li><span class="unique_visits"></span><?php if ($this->lang->line('admin_dashboard_total_rides') != '') echo stripslashes($this->lang->line('admin_dashboard_total_rides')); else echo 'Total Rides'; ?>: <?php if (isset($totalRides)) echo $totalRides; ?></li>
                                </ul>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <div class="grid_6" style="margin-top: 20px;">
            <div class="widget_wrap">
                <div class="widget_top">
                    <span class="h_icon graph"></span>
                    <h6><?php if ($this->lang->line('admin_dashboard_drivers') != '') echo stripslashes($this->lang->line('admin_dashboard_drivers')); else echo 'Drivers'; ?></h6>
                </div>
                <div class="widget_content">
                    <div class="stat_block">	
                        <div class="stat_chart">
                            <h4><?php if ($this->lang->line('admin_dashboard_drivers_count') != '') echo stripslashes($this->lang->line('admin_dashboard_drivers_count')); else echo 'Drivers Count'; ?> : <?php echo $totalDrivers; ?></h4>
                            <table>
                                <tbody>
                                    <tr>
                                        <td>
                                            <?php if ($this->lang->line('admin_dashboard_today') != '') echo stripslashes($this->lang->line('admin_dashboard_today')); else echo 'Today'; ?>
                                        </td>
                                        <td>
                                            <?php if (isset($todayDrivers)) echo $todayDrivers; ?>
                                        </td>
                                        <?php /* <td class="min_chart">
                                          <span class="bar">20,30,50,200,250,280,350</span>
                                          </td> */ ?>
                                    </tr>
                                    <tr>
                                        <td>
                                            <?php if ($this->lang->line('admin_dashboard_this_month') != '') echo stripslashes($this->lang->line('admin_dashboard_this_month')); else echo 'This Month'; ?>
                                        </td>
                                        <td>
                                            <?php if (isset($monthDrivers)) echo $monthDrivers; ?>
                                        </td>
                                        <?php /* <td class="min_chart">
                                          <span class="line">20,30,50,200,250,280,350</span>
                                          </td> */ ?>
                                    </tr>
                                    <tr>
                                        <td>
                                            <?php if ($this->lang->line('admin_dashboard_this_year') != '') echo stripslashes($this->lang->line('admin_dashboard_this_year')); else echo 'This Year'; ?>
                                        </td>
                                        <td>
                                            <?php if (isset($yearDrivers)) echo $yearDrivers; ?>
                                        </td>
                                        <?php /* <td class="min_chart">
                                          <span class="line">20,30,50,200,250,280,350</span>
                                          </td> */ ?>
                                    </tr>
                                </tbody>
                            </table>
							<div class="pie_chart">
                                <?php
                                $activeDriversPercent = 0.00;
                                if (isset($activeDrivers)) {
                                    if ($totalDrivers > 0) {
                                        $activeDriversPercent = ($activeDrivers * 100) / $totalDrivers;
                                    }
                                }
                                ?>
                                <span class="inner_circle"><?php echo round($activeDriversPercent, 1) . '%'; ?></span>
                                <span class="pie"><?php if (isset($activeDrivers)) echo $activeDrivers; ?>/<?php if (isset($totalDrivers)) echo $totalDrivers; ?></span>
                            </div>
                            <div class="chart_label">
                                <ul>
                                    <li><span class="new_visits"></span><?php if ($this->lang->line('admin_dashboard_active_drivers') != '') echo stripslashes($this->lang->line('admin_dashboard_active_drivers')); else echo 'Active Drivers'; ?>: <?php if (isset($activeDrivers)) echo $activeDrivers; ?></li>
                                    <li><span class="unique_visits"></span><?php if ($this->lang->line('admin_dashboard_total_drivers') != '') echo stripslashes($this->lang->line('admin_dashboard_total_drivers')); else echo 'Total Drivers'; ?>: <?php if (isset($totalDrivers)) echo $totalDrivers; ?></li>
                                </ul>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        
    </div>
    <span class="clear"></span>
</div>
</div>
<?php
$this->load->view(COMPANY_NAME.'/templates/footer.php');
?>