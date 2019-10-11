<?php
$this->load->view(ADMIN_ENC_URL.'/templates/header.php');
extract($privileges);
?> 
<?php if (!empty($monthlyEarningsGraphNew)) { ?>
<link rel="stylesheet" href="plugins/jqwidgets-master/jqwidgets/styles/jqx.base.css" type="text/css" />
<script type="text/javascript" src="plugins/jqwidgets-master/jqwidgets/jqxcore.js"></script>
<script type="text/javascript" src="plugins/jqwidgets-master/jqwidgets/jqxdata.js"></script>
<script type="text/javascript" src="plugins/jqwidgets-master/jqwidgets/jqxdraw.js"></script>
<script type="text/javascript" src="plugins/jqwidgets-master/jqwidgets/jqxchart.core.js"></script>
<script type="text/javascript" src="plugins/jqwidgets-master/jqwidgets/jqxchart.rangeselector.js"></script>
<script type="text/javascript">
$(document).ready(function () {
	
	// prepare chart data as an array
	var monthVal = jQuery.parseJSON('<?php echo json_encode($monthArr); ?>');
	var totalEarningsaNew = jQuery.parseJSON('<?php echo json_encode($monthlyEarningsGraphNew); ?>');
	var driverEarningsaNew = jQuery.parseJSON('<?php echo json_encode($monthlyDriverEarningsGraphNew); ?>');
	var siteearningsaNew = jQuery.parseJSON('<?php echo json_encode($monthlySiteEarningsGraphNew); ?>');
	

	// prepare jqxChart settings
	var settings = {
		title: "",
		description: "",
		enableAnimations: true,
		animationDuration: 2500,
		showLegend: true,
		padding: { left: 5, top: 20, right: 30, bottom: 5 },
		titlePadding: { left: 90, top: 0, right: 0, bottom: 10 },
		source: monthVal,
		enableCrosshairs: true,
		xAxis: { 
			dataField: 'Month', 
			displayText: '<?php if ($this->lang->line('this_month') != '') echo stripslashes($this->lang->line('this_month')); else echo 'This Month'; ?>', 
			gridLines: { visible: true },
			rangeSelector: {
                            serieType: 'area',
                            padding: { /*left: 0, right: 0,*/ top: 20, bottom: 0 },
                            backgroundColor: 'white',
                            size: 110,
                            gridLines: {visible: false},
                        }
		},
		colorScheme: 'scheme01',
		valueAxis: { visible: true, title: { text: '<?php if ($this->lang->line('amount_in') != '') echo stripslashes($this->lang->line('amount_in')); else echo 'Amount in'; ?> <?php echo $dcurrencySymbol; ?>' } },
		seriesGroups:
			[
				{
					type: 'stackedarea',
					source: totalEarningsaNew,
					series: [
						  { dataField: 'Amount', displayText: '<?php echo get_language_value_for_keyword("Total",$this->data['langCode']); ?>' }
					]
				},
				{
					type: 'stackedline',
					source: driverEarningsaNew,
					series: [
							{ dataField: 'Amount', displayText: '<?php echo get_language_value_for_keyword("Driver",$this->data['langCode']); ?>' }
					]
				},
				{
					type: 'stackedline',
					source: siteearningsaNew,
					series: [
							{ dataField: 'Amount', displayText: '<?php echo get_language_value_for_keyword("Site",$this->data['langCode']); ?>' }
					]
				}
			]
	};

	// setup the chart
	$('#siteEarnings').jqxChart(settings);
});
</script>


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


</style>
	
	
<?php } ?>
<div id="content" style="clear:both;">
    <div class="grid_container">		
        <div class="grid_12">
            <div class="widget_wrap">
               <!-- <div class="widget_top">
                    <span class="h_icon graph"><span style="display: none;"></span><canvas width="16" height="16"></canvas></span>
                    
                </div>-->
                <div class="widget_content">
                   
                        <div class="social_activities cabily_dash">	
						<h6><?php if ($this->lang->line('admin_dashboard_site_statistics') != '') echo stripslashes($this->lang->line('admin_dashboard_site_statistics')); else echo 'Site Statistics'; ?></h6>
                            <div class="activities_s site_statistics" >
                                <div class="block_label">   
									<small><?php if ($this->lang->line('admin_dashboard_site_user') != '') echo stripslashes($this->lang->line('admin_dashboard_site_user')); else echo 'Users'; ?></small>								
                                    <span><?php if (isset($totalUsers)) echo number_format($totalUsers,0); ?></span>
									
									<i class="user_icon_font fa fa-user-plus"></i>
									<a href="<?php echo ADMIN_ENC_URL;?>/users/display_user_list" class="small-box-footer"><?php if ($this->lang->line('panel_more_info') != '') echo stripslashes($this->lang->line('panel_more_info')); else echo 'More info'; ?> <i class="fa fa-arrow-circle-right"></i></a>
                                </div>
                            </div>	
							
                            <div class="activities_s site_statistics">
                                <div class="block_label">
								<small><?php if ($this->lang->line('admin_menu_drivers') != '') echo stripslashes($this->lang->line('admin_menu_drivers')); else echo 'Drivers'; ?></small>
									<span><?php if (isset($totalDrivers)) echo number_format($totalDrivers,0); ?></span>
									
                                    <i class="user_icon_font fa fa-address-card"></i>
									<a href="<?php echo ADMIN_ENC_URL;?>/drivers/display_drivers_list" class="small-box-footer"><?php if ($this->lang->line('panel_more_info') != '') echo stripslashes($this->lang->line('panel_more_info')); else echo 'More info'; ?> <i class="fa fa-arrow-circle-right"></i></a>
                                </div>
                            </div>
							
                            <div class="activities_s site_statistics">
                                <div class="block_label">
								 <small><?php if ($this->lang->line('admin_dashboard_coupon_codes') != '') echo stripslashes($this->lang->line('admin_dashboard_coupon_codes')); else echo 'Coupon Codes'; ?></small>
                                    <span><?php if (isset($totalcouponCode)) echo $totalcouponCode; ?></span>
                                   
                                    <i class="user_icon_font fa fa-ticket"></i>
									<a href="<?php echo ADMIN_ENC_URL;?>/promocode/display_promocode" class="small-box-footer"><?php if ($this->lang->line('panel_more_info') != '') echo stripslashes($this->lang->line('panel_more_info')); else echo 'More info'; ?> <i class="fa fa-arrow-circle-right"></i></a>
                                </div>	
                            </div>
							
                            <div class="activities_s site_statistics">
                                <div class="block_label">
								<small><?php if ($this->lang->line('admin_dashboard_locations') != '') echo stripslashes($this->lang->line('admin_dashboard_locations')); else echo 'Locations'; ?></small>
                                    <span><?php if (isset($totalLocations)) echo number_format($totalLocations,0); ?></span>
                                    
									<i class="user_icon_font fa fa-location-arrow"></i>
									<a href="<?php echo ADMIN_ENC_URL;?>/location/display_location_list" class="small-box-footer"><?php if ($this->lang->line('panel_more_info') != '') echo stripslashes($this->lang->line('panel_more_info')); else echo 'More info'; ?> <i class="fa fa-arrow-circle-right"></i></a>
                                </div>	
                            </div>
							
                            <div class="activities_s site_statistics">
                                <div class="block_label">
								<small><?php if ($this->lang->line('admin_dashboard_total_earnings') != '') echo stripslashes($this->lang->line('admin_dashboard_total_earnings')); else echo 'Total Earnings'; ?> </small>
									<span> <p class="curr_one"><?php echo $dcurrencySymbol; ?> </p><?php if (isset($totalEarnings)) echo number_format($totalEarnings,0); ?></span>                                  
                                    
                                    <i class="user_icon_font fa fa-money"></i>
									<a href="<?php echo ADMIN_ENC_URL;?>/revenue/display_site_revenue" class="small-box-footer"><?php if ($this->lang->line('panel_more_info') != '') echo stripslashes($this->lang->line('panel_more_info')); else echo 'More info'; ?> <i class="fa fa-arrow-circle-right"></i></a>
                                </div>	
                            </div>
							
                            <div class="activities_s site_statistics">
                                <div class="block_label">
								<small><?php if ($this->lang->line('admin_dashboard_current_wallet_amount') != '') echo stripslashes($this->lang->line('admin_dashboard_current_wallet_amount')); else echo 'Current Wallet Balance'; ?> </small>
									<span> <p class="curr_one"><?php echo $dcurrencySymbol; ?> </p><?php if (isset($totalWallet)) echo number_format($totalWallet,0); ?></span>                                  
                                    
                                    <i class="user_icon_font fa fa-money"></i>
									<a class="small-box-footer"></a>
                                </div>	
                            </div>

							
                        </div>
                    
                </div>
            </div>
        </div>			
        <div class="grid_12" style="margin-top: 20px;">
            <div class="widget_wrap">
               <!-- <div class="widget_top">
                    <span class="h_icon graph"><span style="display: none;"></span><canvas width="16" height="16"></canvas></span>
                    
                </div>-->
                <div class="widget_content">
                    
                        <div class="social_activities second-tab cabily_dash">
<h6><?php if ($this->lang->line('admin_dashboard_ride_statistics') != '') echo stripslashes($this->lang->line('admin_dashboard_ride_statistics')); else echo 'Ride Statistics'; ?></h6>						
                            <div class="activities_s site_statistics" >
                                <div class="block_label">
								<small><?php if ($this->lang->line('admin_dashboard_total_rides') != '') echo stripslashes($this->lang->line('admin_dashboard_total_rides')); else echo 'Total Rides'; ?></small> 
                                    <span><?php if (isset($totalRides)) echo number_format($totalRides,0); ?></span>
                                    
									<i class="user_icon_font fa fa-car"></i>
									<a href="<?php echo ADMIN_ENC_URL;?>/rides/display_rides?act=total" class="small-box-footer"><?php if ($this->lang->line('panel_more_info') != '') echo stripslashes($this->lang->line('panel_more_info')); else echo 'More info'; ?> <i class="fa fa-arrow-circle-right"></i></a>	
                                </div>
                            </div>								
                            <div class="activities_s site_statistics">
                                <div class="block_label">
								<small><?php if ($this->lang->line('admin_dashboard_upcomming_rides') != '') echo stripslashes($this->lang->line('admin_dashboard_upcomming_rides')); else echo 'Upcoming Rides'; ?></small> 
                                    <span><?php if (isset($upcommingRides)) echo number_format($upcommingRides,0); ?></span>
                                                       
									<i class="user_icon_font fa fa-car"></i>
									<a href="<?php echo ADMIN_ENC_URL;?>/rides/display_rides?act=Booked" class="small-box-footer"><?php if ($this->lang->line('panel_more_info') != '') echo stripslashes($this->lang->line('panel_more_info')); else echo 'More info'; ?> <i class="fa fa-arrow-circle-right"></i></a>
                                </div>
                            </div>								
                            <div class="activities_s site_statistics">
                                <div class="block_label">
								<small><?php if ($this->lang->line('admin_dashboard_on_rides') != '') echo stripslashes($this->lang->line('admin_dashboard_on_rides')); else echo 'On Rides'; ?></small>
                                    <span><?php if (isset($onRides)) echo $onRides; ?></span>
                                    
                                    <i class="user_icon_font fa fa-car"></i>
									<a href="<?php echo ADMIN_ENC_URL;?>/rides/display_rides?act=OnRide" class="small-box-footer"><?php if ($this->lang->line('panel_more_info') != '') echo stripslashes($this->lang->line('panel_more_info')); else echo 'More info'; ?> <i class="fa fa-arrow-circle-right"></i></a>
                                </div>
                            </div>								
                            <div class="activities_s site_statistics">
                                <div class="block_label">
								<small><?php if ($this->lang->line('admin_dashboard_rider_denied') != '') echo stripslashes($this->lang->line('admin_dashboard_rider_denied')); else echo 'Rider Denied'; ?></small>
                                    <span><?php if (isset($riderDeniedRides)) echo number_format($riderDeniedRides,0); ?></span>
                                    
									<i class="user_icon_font fa fa-car"></i>
									<a href="<?php echo ADMIN_ENC_URL;?>/rides/display_rides?act=riderCancelled" class="small-box-footer"><?php if ($this->lang->line('panel_more_info') != '') echo stripslashes($this->lang->line('panel_more_info')); else echo 'More info'; ?> <i class="fa fa-arrow-circle-right"></i></a>
                                </div>
                            </div>								
                            <div class="activities_s site_statistics">
                                <div class="block_label">
								<small><?php if ($this->lang->line('admin_dashboard_driver_denied') != '') echo stripslashes($this->lang->line('admin_dashboard_driver_denied')); else echo 'Driver Denied'; ?></small>
                                    <span><?php if (isset($driverDeniedRides)) echo number_format($driverDeniedRides,0); ?></span>
                                    
                                    <i class="user_icon_font fa fa-car"></i>
									<a href="<?php echo ADMIN_ENC_URL;?>/rides/display_rides?act=driverCancelled" class="small-box-footer"><?php if ($this->lang->line('panel_more_info') != '') echo stripslashes($this->lang->line('panel_more_info')); else echo 'More info'; ?> <i class="fa fa-arrow-circle-right"></i></a>
                                </div>
                            </div>								
                            <div class="activities_s site_statistics">
                                <div class="block_label">
								<small><?php if ($this->lang->line('admin_dashboard_completed_rides') != '') echo stripslashes($this->lang->line('admin_dashboard_completed_rides')); else echo 'Completed Rides'; ?></small>
                                    <span><?php if (isset($completedRides)) echo number_format($completedRides,0); ?></span>
                                    
                                    <i class="user_icon_font fa fa-car"></i>
									<a href="<?php echo ADMIN_ENC_URL;?>/rides/display_rides?act=Completed" class="small-box-footer"><?php if ($this->lang->line('panel_more_info') != '') echo stripslashes($this->lang->line('panel_more_info')); else echo 'More info'; ?> <i class="fa fa-arrow-circle-right"></i></a>
                                </div>
                            </div>								
                        </div>
                    
                </div>
            </div>
        </div>

        <div class="grid_6" style="margin-top: 20px;">
            <div class="widget_wrap">
                <!--<div class="widget_top">
                    <span class="h_icon graph"></span>
                    
                </div>-->
                <div class="widget_content">
                    <div class="stat_block cabily_rides">
					<h6><?php if ($this->lang->line('admin_dashboard_rides') != '') echo stripslashes($this->lang->line('admin_dashboard_rides')); else echo 'Rides'; ?></h6>
                        <div class="stat_chart">							
                            <h4><?php if ($this->lang->line('admin_dashboard_rides_count') != '') echo stripslashes($this->lang->line('admin_dashboard_rides_count')); else echo 'Rides Count'; ?> : <?php if (isset($totalRides)) echo $totalRides; ?></h4>
                            <table>
                                <tbody>
                                    <tr>
                                        <td>
                                            <?php if ($this->lang->line('admin_dashboard_today') != '') echo stripslashes($this->lang->line('admin_dashboard_today')); else echo 'Today'; ?>
                                        </td>
                                        <td>
                                            <?php if (isset($todayRides)) echo number_format($todayRides,0); ?>
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
                                            <?php if (isset($monthRides)) echo number_format($monthRides,0); ?>
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
                                            <?php if (isset($yearRides)) echo number_format($yearRides,0); ?>
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
                                <span class="pie"><?php if (isset($completedRides)) echo number_format($completedRides,0); ?>/<?php if (isset($totalRides)) echo $totalRides; ?></span>
                            </div>
                            <div class="chart_label">
                                <ul>
                                    <li><span class="new_visits"></span><?php if ($this->lang->line('admin_dashboard_completed_rides') != '') echo stripslashes($this->lang->line('admin_dashboard_completed_rides')); else echo 'Completed Rides'; ?>: <?php if (isset($completedRides)) echo number_format($completedRides,0); ?></li>
                                    <li><span class="unique_visits"></span><?php if ($this->lang->line('admin_dashboard_total_rides') != '') echo stripslashes($this->lang->line('admin_dashboard_total_rides')); else echo 'Total Rides'; ?>: <?php if (isset($totalRides)) echo  number_format($totalRides,0); ?></li>
                                </ul>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <div class="grid_6" style="margin-top: 20px;">
            <div class="widget_wrap">
                <!--<div class="widget_top">
                    <span class="h_icon graph"></span>
                    
                </div>-->
                <div class="widget_content">
                    <div class="stat_block cabily_rides">	
					<h6><?php if ($this->lang->line('admin_dashboard_drivers') != '') echo stripslashes($this->lang->line('admin_dashboard_drivers')); else echo 'Drivers'; ?></h6>
                        <div class="stat_chart">
                            <h4><?php if ($this->lang->line('admin_dashboard_drivers_count') != '') echo stripslashes($this->lang->line('admin_dashboard_drivers_count')); else echo 'Drivers Count'; ?> : <?php echo number_format($totalDrivers,0); ?></h4>
                            <table>
                                <tbody>
                                    <tr>
                                        <td>
                                            <?php if ($this->lang->line('admin_dashboard_today') != '') echo stripslashes($this->lang->line('admin_dashboard_today')); else echo 'Today'; ?>
                                        </td>
                                        <td>
                                            <?php if (isset($todayDrivers)) echo number_format($todayDrivers,0); ?>
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
                                            <?php if (isset($monthDrivers)) echo number_format($monthDrivers,0); ?>
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
                                            <?php if (isset($yearDrivers)) echo number_format($yearDrivers,0); ?>
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
                                    <li><span class="new_visits"></span><?php if ($this->lang->line('admin_dashboard_active_drivers') != '') echo stripslashes($this->lang->line('admin_dashboard_active_drivers')); else echo 'Active Drivers'; ?>: <?php if (isset($activeDrivers)) echo number_format($activeDrivers,0); ?></li>
                                    <li><span class="unique_visits"></span><?php if ($this->lang->line('admin_dashboard_total_drivers') != '') echo stripslashes($this->lang->line('admin_dashboard_total_drivers')); else echo 'Total Drivers'; ?>: <?php if (isset($totalDrivers)) echo number_format($totalDrivers,0); ?></li>
                                </ul>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <span class="clear"></span>
        <?php if (!empty($monthlyEarningsGraph)) { ?>
            <div class="grid_12">
                <div class="widget_wrap">
                   <!-- <div class="widget_top">
                        <span class="h_icon graph"></span>
                        
                    </div>-->
                    <div class="widget_content">
					<div class="stat_block cabily_rides">
					<h6><?php if ($this->lang->line('admin_dashboard_earnings') != '') echo stripslashes($this->lang->line('admin_dashboard_earnings')); else echo 'Earnings'; ?></h6>
                        <div class="data_widget black_g chart_wrap">
							<div id='siteEarnings' style="width: 100%; height: 400px; position: relative; left: 0px; top: 0px;"></div>
                        </div>
					</div>	
                    </div>
                </div>
            </div>
            <span class="clear"></span>
        <?php } ?>
    </div>
    <span class="clear"></span>
</div>

</div>
<?php
$this->load->view(ADMIN_ENC_URL.'/templates/footer.php');
?>