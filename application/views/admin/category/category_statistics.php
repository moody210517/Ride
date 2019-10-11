<?php
$this->load->view(ADMIN_ENC_URL.'/templates/header.php');
?>

<div id="content" style="clear:both;">
	<div class="grid_container">
	
		<div class="grid_12">
            <div class="widget_wrap">
                <div class="widget_top">
                    <span class="h_icon graph"><span style="display: none;"><span style="display: none;"></span><canvas width="16" height="16"></canvas></span><canvas width="16" height="16"></canvas></span>
                    <h6><?php if ($this->lang->line('admin_car_types_dashboard') != '') echo stripslashes($this->lang->line('admin_car_types_dashboard')); else echo 'Car Types Dashboard'; ?></h6>
                </div>
                <div class="widget_content">
           
                        <div class="social_activities pad-box ride-dashboard-socialactivity cabily_dash">								
                            <a class="activities_s site_statistics">
                                <div class="block_label">
								<small><?php if ($this->lang->line('admin_total_categories') != '') echo stripslashes($this->lang->line('admin_total_categories')); else echo 'Total Categories '; ?></small>
                                    <span><?php echo number_format($categoryList->num_rows(),0); ?></span>
                                    
									<i class="user_icon_font fa fa-users"></i>
                                </div>
                            </a>								
                            <a class="activities_s site_statistics">
                                <div class="block_label">
								<small><?php if ($this->lang->line('admin_active_categories') != '') echo stripslashes($this->lang->line('admin_active_categories')); else echo 'Active Categories'; ?></small>
                                    <span><?php echo number_format($activeCats,0); ?></span>
                                    
									<i class="user_icon_font fa fa-users"></i>
                                </div>
                            </a>								
                            <a class="activities_s site_statistics" style="margin-right:0px;">
                                <div class="block_label">
								 <small><?php if ($this->lang->line('admin_inactive_categories') != '') echo stripslashes($this->lang->line('admin_inactive_categories')); else echo 'Inactive Categories'; ?></small>
                                    <span><?php echo number_format(($categoryList->num_rows()-$activeCats),0); ?></span>
                                   <i class="user_icon_font fa fa-users"></i>
                                </div>
                            </a>								
                           						
                        </div>

                </div>
            </div>
        </div>
	
	    <div class="grid_6">
			<div class="widget_wrap">
				<div class="widget_top">
					<span class="h_icon graph"></span>
					<h6><?php if ($this->lang->line('admin_driver_under_category') != '') echo stripslashes($this->lang->line('admin_driver_under_category')); else echo 'Drivers under car category'; ?></h6>
				</div>
				<div class="widget_content">
					<div id="cat_drivers_stats" class="chart_container" ></div>
				</div>
			</div>
		</div>
		
	    <div class="grid_6">
			<div class="widget_wrap">
				<div class="widget_top">
					<span class="h_icon graph"></span>
					<h6><?php if ($this->lang->line('admin_rides_statistics_categories') != '') echo stripslashes($this->lang->line('admin_rides_statistics_categories')); else echo 'Category wise ride statistics'; ?></h6>
				</div>
				<div class="widget_content">
					<div id="cat_ride_stats" class="chart_container" ></div>
				</div>
			</div>
		</div>
	</div>
	<span class="clear"></span>
</div>
</div>
<link rel="stylesheet" href="plugins/jqwidgets-master/jqwidgets/styles/jqx.base.css" type="text/css" />
<script type="text/javascript" src="plugins/jqwidgets-master/jqwidgets/jqxcore.js"></script>
<script type="text/javascript" src="plugins/jqwidgets-master/jqwidgets/jqxdata.js"></script>
<script type="text/javascript" src="plugins/jqwidgets-master/jqwidgets/jqxdraw.js"></script>
<script type="text/javascript" src="plugins/jqwidgets-master/jqwidgets/jqxchart.core.js"></script>


<script type="text/javascript">
	$(document).ready(function () {
	
		$.jqx._jqxChart.prototype.colorSchemes.push({ name: 'myScheme', colors: ['#6680C6', '#E35912', '#FBB300', '#E30F74', '#78D22D'] });
		
		// prepare chart data for user ratings
		var  catDriverData = <?php echo json_encode($categoryDriversList); ?>
		// prepare jqxChart settings for user
		var cat_drivers_Settings = {
			title: " <?php if ($this->lang->line('admin_total_drivers') != '') echo stripslashes($this->lang->line('admin_total_drivers')); else echo 'Total Drivers'; ?> <?php echo ' : '.$driversList->num_rows(); ?> ",
			description: "----------",
			enableAnimations: true,
			showLegend: true,
			showBorderLine: true,
			//legendLayout: { left: 700, top: 160, width: 300, height: 200, flow: 'vertical' },
			legendPosition: { left: 700, top: 160, width: 300, height: 200, flow: 'vertical'  },
			padding: { left: 5, top: 5, right: 5, bottom: 5 },
			titlePadding: { left: 0, top: 0, right: 0, bottom: 10 },
			source: catDriverData,
			colorScheme: 'myScheme',
			seriesGroups:
				[{
					type: 'pie',
					showLabels: true,
					series:[{ 
								dataField: 'drivers_count',
								displayText: 'cat_name',
								labelRadius: 140,
								initialAngle: 15,
								radius: 100,
								centerOffset: 0,
								formatFunction: function (value) {
									if (isNaN(value)) return value;
									return parseFloat(value) + '';
								},
							}]
				}]
		};
		$('#cat_drivers_stats').jqxChart(cat_drivers_Settings);
		
		 // prepare chart data for user ratings
		var  catRideData = <?php echo json_encode($categoryRidesList); ?>
		// prepare jqxChart settings for user
		var  catRideSettings = {
			title: "<?php if ($this->lang->line('admin_total_rides') != '') echo stripslashes($this->lang->line('admin_total_rides')); else echo 'Total Rides'; ?> <?php echo ' : '.$ridesList->num_rows(); ?>",
			description: "-------",
			enableAnimations: true,
			showLegend: true,
			showBorderLine: true,
			//legendLayout: { left: 700, top: 160, width: 300, height: 200, flow: 'vertical' },
			legendPosition: { left: 700, top: 160, width: 300, height: 200, flow: 'vertical'  },
			padding: { left: 5, top: 5, right: 5, bottom: 5 },
			titlePadding: { left: 0, top: 0, right: 0, bottom: 10 },
			source: catRideData,
			colorScheme: 'myScheme',
			seriesGroups:
				[{
					type: 'pie',
					showLabels: true,
					series:[{ 
								dataField: 'rides_count',
								displayText: 'cat_name',
								labelRadius: 140,
								initialAngle: 15,
								radius: 100,
								centerOffset: 0,
								formatFunction: function (value) {
									if (isNaN(value)) return value;
									return parseFloat(value) + '';
								},
							}]
				}]
		};
		$('#cat_ride_stats').jqxChart(catRideSettings); 
	});
</script>

<style>
.stat_block {
  height:145px !important;
}
.rider_block tr {
  height:35px !important;
}
.chart_container{
	width: 100%;
	height: 340px;
	margin:3% auto;
}
.activities_s{
	width: 32.3%;
	margin:10px 16px 10px 0;
}
.block_label small{
	 padding-top: 30px;
}
</style>
<?php 
$this->load->view(ADMIN_ENC_URL.'/templates/footer.php');
?>