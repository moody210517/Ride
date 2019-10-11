<?php
$this->load->view(OPERATOR_NAME.'/templates/header.php');

?>
<div id="content" style="clear:both;" class="display_driver_list">
	<div class="grid_container">
		<div class="grid_6">
			<div class="widget_wrap">
				<div class="widget_top">
					<span class="h_icon graph"></span>
					<h6><?php if ($this->lang->line('admin_drivers_drivers_registered') != '') echo stripslashes($this->lang->line('admin_drivers_drivers_registered')); else echo 'drivers registered in this site'; ?></h6>
				</div>
				<div class="widget_content">
					<div id="driveractInactContainer" class="chart_container" ></div>
				</div>
			</div>
		</div>
		<div class="grid_6">
			<div class="widget_wrap">
				<div class="widget_top">
					<span class="h_icon graph"></span>
					<h6><?php if ($this->lang->line('admin_drivers_dashboard_verified_drivers') != '') echo stripslashes($this->lang->line('admin_drivers_dashboard_verified_drivers')); else echo 'Verified drivers in this site'; ?></h6>
				</div>
				<div class="widget_content">
					<div id="driververifyContainer" class="chart_container" ></div>
				</div>
			</div>
		</div>
		<div class="grid_6">
			<div class="widget_wrap">
				<div class="widget_top">
					<span class="h_icon graph"></span>
					<h6><?php if ($this->lang->line('admin_drivers_dashboard_category_based_drivers') != '') echo stripslashes($this->lang->line('admin_drivers_dashboard_category_based_drivers')); else echo 'Category based drivers'; ?></h6>
				</div>
				<div class="widget_content">
					<div id="driversCategoryContainer" class="chart_container" ></div>
				</div>
			</div>
		</div>
		<div class="grid_6">
			<div class="widget_wrap">
				<div class="widget_top">
					<span class="h_icon graph"></span>
					<h6><?php if ($this->lang->line('admin_drivers_dashboard_location_based_drivers') != '') echo stripslashes($this->lang->line('admin_drivers_dashboard_location_based_drivers')); else echo 'Location based drivers'; ?></h6>
				</div>
				<div class="widget_content">
					<div id="driversLocationContainer" class="chart_container" ></div>
				</div>
			</div>
		</div>
		<div class="grid_6">
			<div class="widget_wrap">
				<div class="widget_top">
					<span class="h_icon graph"></span>
					<h6><?php if ($this->lang->line('admin_drivers_dashboard_highest_rated_drivers') != '') echo stripslashes($this->lang->line('admin_drivers_dashboard_highest_rated_drivers')); else echo 'Highest Rated Driver';?></h6>
				</div>
				<div class="widget_content">
					<div class="stat_block">
						<table class="wtbl_list">
						<thead>
							<tr>
								<th>
									 <?php if ($this->lang->line('admin_users_users_list_name') != '') echo stripslashes($this->lang->line('admin_users_users_list_name')); else echo 'Name'; ?>
								</th>
								<th>
									 <?php if ($this->lang->line('admin_users_users_list_email') != '') echo stripslashes($this->lang->line('admin_users_users_list_email')); else echo 'Email'; ?>
								</th>
								
								<th>
									<?php if ($this->lang->line('admin_users_rating') != '') echo stripslashes($this->lang->line('admin_users_rating')); else echo 'Ratings'; ?>
								
								</th>
								<th>
								  <?php if ($this->lang->line('admin_ride_count') != '') echo stripslashes($this->lang->line('admin_ride_count')); else echo 'Ride Count'; ?>
								</th>
							</tr>
						</thead>
						<tbody>
							<?php 
							if (count($top_driver['result']) > 0){
								foreach($top_driver['result'] as $data){
							?>
							<tr class="tr_even">
								<td>
									 <?php echo $data['driver_name'];?>
								</td>
								<td>
									 <?php echo $data['email'];?>
								</td>
								
								<td>
									<div class="star str" id="star-pos<?php echo $data['_id'] ?>"  data-star="<?php if(isset($data['avg_review']) && $data['avg_review'] !='' ){echo $data['avg_review']; }else{ echo "NA"; } ?>" style="width: 200px;float:none;"></div>
								</td>
								<td>
									 <?php echo $data['no_of_rides'];?>
								</td>
							</tr>
							<?php 
									}
							}else {
							?>
							<tr>
								<td colspan="5" align="center"><?php if ($this->lang->line('admin_menu_dashboard') != '') echo stripslashes($this->lang->line('admin_menu_dashboard')); else echo 'Dashboard'; ?><?php if ($this->lang->line('admin_users_no_available') != '') echo stripslashes($this->lang->line('admin_users_no_available')); else echo 'No Users Available'; ?></td>
							</tr>
							<?php }?>
						</tbody>
					</table>
						
					</div>
				</div>
			</div>
		</div>
		<div class="grid_6">
			<div class="widget_wrap">
				<div class="widget_top">
					<span class="h_icon graph"></span>
					<h6><?php if ($this->lang->line('admin_drivers_dashboard_lowest_rated_drivers') != '') echo stripslashes($this->lang->line('admin_drivers_dashboard_lowest_rated_drivers')); else echo 'Lowest Rated Driver'; ?></h6>
				</div>
				<div class="widget_content">
					<div class="stat_block">
						<table class="wtbl_list">
						<thead>
							<tr>
								<th>
									 <?php if ($this->lang->line('admin_users_users_list_name') != '') echo stripslashes($this->lang->line('admin_users_users_list_name')); else echo 'Name'; ?>
								</th>
								<th>
									 <?php if ($this->lang->line('admin_users_users_list_email') != '') echo stripslashes($this->lang->line('admin_users_users_list_email')); else echo 'Email'; ?>
								</th>
								
								<th>
									<?php if ($this->lang->line('admin_users_rating') != '') echo stripslashes($this->lang->line('admin_users_rating')); else echo 'Ratings'; ?>
								
								</th>
								<th>
								  <?php if ($this->lang->line('admin_ride_count') != '') echo stripslashes($this->lang->line('admin_ride_count')); else echo 'Ride Count'; ?>
								</th>
							</tr>
						</thead>
						<tbody>
							<?php 
							if (count($bottom_driver['result']) > 0){
								foreach($bottom_driver['result'] as $data){
							?>
							<tr class="tr_even">
								<td>
									 <?php echo $data['driver_name'];?>
								</td>
								<td>
									 <?php echo $data['email'];?>
								</td>
								
								<td>
								    <?php if(isset($data['avg_review'])) {
										
										$avg_review=$data['avg_review'];
									  } else {
										$avg_review=0;
									  }
									?>
									
									
									<div class="star str" id="star-pos-low<?php echo $data['_id'] ?>"  data-star="<?php echo $avg_review ?>" style="width: 200px;float:none;"></div>
								</td>
								<td>
									 <?php echo $data['no_of_rides'];?>
								</td>
							</tr>
							<?php 
									}
							}else {
							?>
							<tr>
								<td colspan="5" align="center"><?php if ($this->lang->line('admin_menu_dashboard') != '') echo stripslashes($this->lang->line('admin_menu_dashboard')); else echo 'Dashboard'; ?><?php if ($this->lang->line('admin_users_no_available') != '') echo stripslashes($this->lang->line('admin_users_no_available')); else echo 'No Users Available'; ?></td>
							</tr>
							<?php }?>
						</tbody>
					</table>
						
					</div>
				</div>
			</div>
		</div>
		
		<div class="grid_6">
			<div class="widget_wrap">
				<div class="widget_top">
					<span class="h_icon graph"></span>
					<h6><?php if ($this->lang->line('admin_drivers_dashboard_top_3rides_driver') != '') echo stripslashes($this->lang->line('admin_drivers_dashboard_top_3rides_driver')); else echo 'Top 3 Rides Drivers';?></h6>
				</div>
				<div class="widget_content">
					<div class="stat_block">
						<table class="wtbl_list">
						<thead>
							<tr>
								<th>
									 <?php if ($this->lang->line('admin_users_users_list_name') != '') echo stripslashes($this->lang->line('admin_users_users_list_name')); else echo 'Name'; ?>
								</th>
								<th>
									 <?php if ($this->lang->line('admin_users_users_list_email') != '') echo stripslashes($this->lang->line('admin_users_users_list_email')); else echo 'Email'; ?>
								</th>
																
								<th>
								  <?php if ($this->lang->line('admin_ride_count') != '') echo stripslashes($this->lang->line('admin_ride_count')); else echo 'Ride Count'; ?>
								</th>
							</tr>
						</thead>
						<tbody>
							<?php 
							if (count($top_rides['result']) > 0){
								foreach($top_rides['result'] as $data){
							?>
							<tr class="tr_even">
								<td>
									 <?php echo $data['driver']['name'];?>
								</td>
								<td>
									 <?php echo $data['driver']['email'];?>
								</td>
																
								<td>
									 <?php echo $data['totalTrips'];?>
								</td>
							</tr>
							<?php 
									}
							}else {
							?>
							<tr>
								<td colspan="5" align="center"><?php if ($this->lang->line('admin_menu_dashboard') != '') echo stripslashes($this->lang->line('admin_menu_dashboard')); else echo 'Dashboard'; ?><?php if ($this->lang->line('admin_users_no_available') != '') echo stripslashes($this->lang->line('admin_users_no_available')); else echo 'No Users Available'; ?></td>
							</tr>
							<?php }?>
						</tbody>
					</table>
						
					</div>
				</div>
			</div>
		</div>
				<div class="grid_6">
			<div class="widget_wrap">
				<div class="widget_top">
					<span class="h_icon graph"></span>
					<h6><?php  if ($this->lang->line('admin_drivers_dashboard_top_3revenue_drivers') != '') echo stripslashes($this->lang->line('admin_drivers_dashboard_top_3revenue_drivers')); else echo 'Top 3 Revenue Drivers';?></h6>
				</div>
				<div class="widget_content">
					<div class="stat_block">
						<table class="wtbl_list">
						<thead>
							<tr>
								<th>
									 <?php if ($this->lang->line('admin_users_users_list_name') != '') echo stripslashes($this->lang->line('admin_users_users_list_name')); else echo 'Name'; ?>
								</th>
								<th>
									 <?php if ($this->lang->line('admin_users_users_list_email') != '') echo stripslashes($this->lang->line('admin_users_users_list_email')); else echo 'Email'; ?>
								</th>
																
								<th>
								  <?php if ($this->lang->line('admin_revenue') != '') echo stripslashes($this->lang->line('admin_revenue')); else echo 'Revenue'; ?>
								</th>
							</tr>
						</thead>
						<tbody>
							<?php 
							if (count($top_revenue['result']) > 0){
								foreach($top_revenue['result'] as $data){
							?>
							<tr class="tr_even">
								<td>
									 <?php echo $data['driver']['name'];?>
								</td>
								<td>
									 <?php echo $data['driver']['email'];?>
								</td>
																
								<td>
								  <?php echo $dcurrencySymbol; ?> <?php echo $data['totalRevenue'];?>
								</td>
							</tr>
							<?php 
									}
							}else {
							?>
							<tr>
								<td colspan="5" align="center"><?php if ($this->lang->line('admin_menu_dashboard') != '') echo stripslashes($this->lang->line('admin_menu_dashboard')); else echo 'Dashboard'; ?><?php if ($this->lang->line('admin_users_no_available') != '') echo stripslashes($this->lang->line('admin_users_no_available')); else echo 'No Users Available'; ?></td>
							</tr>
							<?php }?>
						</tbody>
					</table>
						
					</div>
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
		var  driverStatusData = [
				{Star:'<?php if ($this->lang->line('admin_drivers_active_drivers') != '') echo stripslashes($this->lang->line('admin_drivers_active_drivers')); else echo 'Active Drivers'; ?> ',value: <?php echo $activeDrivers; ?>},
				{Star:'<?php if ($this->lang->line('admin_drivers_inactive_drivers') != '') echo stripslashes($this->lang->line('admin_drivers_inactive_drivers')); else echo 'Inactive Drivers'; ?> ',value:<?php echo $inactiveDrivers; ?>}
			];
		// prepare jqxChart settings for user
		var driverStatusSettings = {
			title: "<?php echo $totalDrivers;?> <?php if ($this->lang->line('admin_drivers_total_count') != '') echo stripslashes($this->lang->line('admin_drivers_total_count')); else echo 'Total Drivers'; ?>",
			description: "-----",
			enableAnimations: true,
			showLegend: true,
			showBorderLine: true,
			//legendLayout: { left: 700, top: 160, width: 300, height: 200, flow: 'vertical' },
			legendPosition: { left: 700, top: 160, width: 300, height: 200, flow: 'vertical'  },
			padding: { left: 5, top: 5, right: 5, bottom: 5 },
			titlePadding: { left: 0, top: 0, right: 0, bottom: 10 },
			source: driverStatusData,
			colorScheme: 'myScheme',
			seriesGroups:
				[{
					type: 'pie',
					showLabels: true,
					series:[{ 
								dataField: 'value',
								displayText: 'Star',
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
		$('#driveractInactContainer').jqxChart(driverStatusSettings);
		
		// prepare chart data for user ratings
		var  driverVerifyData = [
				{Star:'<?php if ($this->lang->line('admin_driver_dashboard_verified') != '') echo stripslashes($this->lang->line('admin_driver_dashboard_verified')); else echo 'Verified Drivers'; ?> ',value: <?php echo $verifiedDrivers; ?>},
				{Star:'<?php if ($this->lang->line('admin_driver_dashboard_un_verified') != '') echo stripslashes($this->lang->line('admin_driver_dashboard_un_verified')); else echo 'Un Verified Drivers'; ?> ',value:<?php echo $unverifiedDrivers; ?>}				
			];
		// prepare jqxChart settings for user
		var driverVerifySettings = {
			title: "",
			description: "",
			enableAnimations: true,
			showLegend: true,
			showBorderLine: true,
			//legendLayout: { left: 700, top: 160, width: 300, height: 200, flow: 'vertical' },
			legendPosition: { left: 700, top: 160, width: 300, height: 200, flow: 'vertical'  },
			padding: { left: 5, top: 5, right: 5, bottom: 5 },
			titlePadding: { left: 0, top: 0, right: 0, bottom: 10 },
			source: driverVerifyData,
			colorScheme: 'myScheme',
			seriesGroups:
				[{
					type: 'pie',
					showLabels: true,
					series:[{ 
								dataField: 'value',
								displayText: 'Star',
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
		$('#driververifyContainer').jqxChart(driverVerifySettings);
		
		// prepare chart data
		var driverCategoryData = <?php echo json_encode($category_driver); ?>

		// prepare jqxChart settings for drivers by category
		var driverCategorySettings = {
			title: "",
			description: "",
			showLegend: true,
			enableAnimations: true,
			padding: { left: 20, top: 5, right: 20, bottom: 5 },
			titlePadding: { left: 90, top: 0, right: 0, bottom: 10 },
			source: driverCategoryData,
			xAxis:
			{
				dataField: '<?php if ($this->lang->line('admin_graph_category') != '') echo stripslashes($this->lang->line('admin_graph_category')); else echo 'Category'; ?>',
				gridLines: { visible: false },
				flip: true,
				labels: {
					visible: true,
					angle : 90
				}
			},
			valueAxis:
			{
				flip: true,
				minValue: 0,
				description: '',
				axisSize: 'auto',
				tickMarksColor: '#888888',
				showGridLines: false,
				labels: {
					visible: true,
					/* formatFunction: function (value) {
						return parseInt(value / 1);
					} */
				}
			},
			colorScheme: 'myScheme',
			seriesGroups:
				[
					{
						type: 'column',
						orientation: 'horizontal',
						columnsGapPercent: 50,
						toolTipFormatSettings: { thousandsSeparator: ',' },
						series: [
								{ dataField: 'Drivers', displayText: '<?php if ($this->lang->line('admin_driver_dashboard_drivers') != '') echo stripslashes($this->lang->line('admin_driver_dashboard_drivers')); else echo 'Drivers'; ?>' }
							]
					}
				]
		};
		
		// setup the chart
		$('#driversCategoryContainer').jqxChart(driverCategorySettings);
		
		// prepare chart data
		var driverLocationData = <?php echo json_encode($location_driver); ?>

		// prepare jqxChart settings for drivers by category
		var driverLocationSettings = {
			title: "",
			description: "",
			showLegend: true,
			enableAnimations: true,
			padding: { left: 20, top: 5, right: 20, bottom: 5 },
			titlePadding: { left: 90, top: 0, right: 0, bottom: 10 },
			source: driverLocationData,
			xAxis:
			{
				dataField: '<?php if ($this->lang->line('admin_graph_location') != '') echo stripslashes($this->lang->line('admin_graph_location')); else echo 'Location'; ?>',
				gridLines: { visible: false },
				flip: true,
				labels: {
					visible: true,
					angle : 90
				}
			},
			valueAxis:
			{
				flip: true,
				minValue: 0,
				description: '',
				axisSize: 'auto',
				tickMarksColor: '#888888',
				showGridLines: false,
				labels: {
					visible: true,
					/* formatFunction: function (value) {
						return parseInt(value / 1);
					} */
				}
			},
			colorScheme: 'myScheme',
			seriesGroups:
				[
					{
						type: 'column',
						orientation: 'horizontal',
						columnsGapPercent: 50,
						toolTipFormatSettings: { thousandsSeparator: ',' },
						series: [
								{ dataField: 'Drivers', displayText: '<?php if ($this->lang->line('admin_driver_dashboard_drivers') != '') echo stripslashes($this->lang->line('admin_driver_dashboard_drivers')); else echo 'Drivers'; ?>' }
							]
					}
				]
		};
		
		$('#driversLocationContainer').jqxChart(driverLocationSettings);
	});
</script>






<style>
.stat_block {
  height:145px !important;
}
.driver_block tr {
  height:35px !important;
}
.chart_container{
	width: 100%;
	height: 340px;
	margin:3% auto;
}
</style>


<?php 
$this->load->view(OPERATOR_NAME.'/templates/footer.php');
?>