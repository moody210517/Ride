<?php
$this->load->view(ADMIN_ENC_URL.'/templates/header.php');
extract($privileges);
?>

<div id="content" style="clear:both;">
	<div class="grid_container">
	    <div class="grid_6">
			<div class="widget_wrap">
				<div class="widget_top">
					<span class="h_icon graph"></span>
					<h6><?php if ($this->lang->line('admin_users_users_list_users_registered_in_site') != '') echo stripslashes($this->lang->line('admin_users_users_list_users_registered_in_site')); else echo 'users registered in this site'; ?></h6>
				</div>
				<div class="widget_content">
					<div id="useractInactContainer" class="chart_container" ></div>
				</div>
			</div>
		</div>
		
	    <div class="grid_6">
			<div class="widget_wrap">
				<div class="widget_top">
					<span class="h_icon graph"></span>
					<h6><?php if ($this->lang->line('admin_users_completed_riders') != '') echo stripslashes($this->lang->line('admin_users_completed_riders')); else echo 'Users who made successful rides'; ?></h6>
				</div>
				<div class="widget_content">
					<div id="userRidUnRContainer" class="chart_container" ></div>
				</div>
			</div>
		</div>

		<div class="grid_6">
			<div class="widget_wrap">
				<div class="widget_top">
					<span class="h_icon image_1"></span>
					<h6><?php if ($this->lang->line('admin_users_recent_users') != '') echo stripslashes($this->lang->line('admin_users_recent_users')); else echo 'Recent Users'; ?></h6>
				</div>
				<div class="widget_content">
					<div class="stat_block">
						<table class="wtbl_list rider_block">
							<thead>
								<tr>
									<th>
										 <?php if ($this->lang->line('admin_users_users_list_user_name') != '') echo stripslashes($this->lang->line('admin_users_users_list_user_name')); else echo 'User Name'; ?>
									</th>
									<th>
										 <?php if ($this->lang->line('admin_users_users_list_email') != '') echo stripslashes($this->lang->line('admin_users_users_list_email')); else echo 'Email'; ?>
									</th>
									<th>
										<?php if ($this->lang->line('admin_commons_status') != '') echo stripslashes($this->lang->line('admin_commons_status')); else echo 'Status'; ?> 
									</th>
								</tr>
							</thead>
							<tbody>
								<?php 
								if ($recentusersList->num_rows() > 0){
									foreach($recentusersList->result() as $user){
								?>
								<tr class="tr_even">
									<td>
										 <?php echo $user->user_name;?>
									</td>
									<td>
										 <?php echo $user->email;?>
									</td>
									<td>
										<?php
										$disp_status = get_language_value_for_keyword($user->status,$this->data['langCode']);
										?>
										<?php echo $disp_status;?>
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
						<span class="h_icon image_1"></span>
						<h6><?php if ($this->lang->line('admin_top_three_ratings') != '') echo stripslashes($this->lang->line('admin_top_three_ratings')); else echo 'Top 3 Ratings'; ?></h6>
					</div>
					<div class="widget_content">
						<div class="stat_block">
							<table class="wtbl_list rider_block">
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
								if (count($top_user['result']) > 0){
									foreach($top_user['result'] as $user){
								?>
								<tr class="tr_even">
									<td>
										 <?php echo $user['user_name'];?>
									</td>
									<td>
										 <?php echo $user['email'];?>
									</td>
									
									<td>
										<div class="star str" id="star-pos<?php echo $user['_id'] ?>"  data-star="<?php echo $user['avg_review'] ?>" style="width: 200px;float:none;"></div>
									</td>
									<td>
									 <?php echo $user['no_of_rides']; ?>
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
						<span class="h_icon image_1"></span>
						<h6><?php if ($this->lang->line('admin_top_three_rider') != '') echo stripslashes($this->lang->line('admin_top_three_rider')); else echo 'Top 3 Rider'; ?></h6>
					</div>
					<div class="widget_content">
						<div class="stat_block">
							<table class="wtbl_list rider_block">
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
								if (count($top_rider['result']) > 0){
									
									foreach($top_rider['result'] as $data){
								?>
								<tr class="tr_even">
									<td>
										 <?php echo $data['user']['name'];?>
									</td>
									<td>
										 <?php echo $data['user']['email'];?>
									</td>
									<td>
									 <?php echo $data['totalTrips']; ?>
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
						<span class="h_icon image_1"></span>
						<h6><?php if ($this->lang->line('admin_top_three_reveneue') != '') echo stripslashes($this->lang->line('admin_top_three_reveneue')); else echo 'Top 3 Revenue'; ?></h6>
					</div>
					<div class="widget_content">
						<div class="stat_block">
							<table class="wtbl_list rider_block">
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
											 <?php echo $data['user']['name'];?>
										</td>
										<td>
											 <?php echo $data['user']['email'];?>
										</td>
										<td>
										 <?php echo $dcurrencySymbol; ?> <?php echo number_format($data['totalRevenue'],2); ?>
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
		var  userRatingData = [
				{Star:'<?php if ($this->lang->line('admin_users_users_list_active_users') != '') echo stripslashes($this->lang->line('admin_users_users_list_active_users')); else echo 'Active Users'; ?> ',value: <?php echo $totalActiveUser; ?>},
				{Star:'<?php if ($this->lang->line('admin_users_users_list_inactive_users') != '') echo stripslashes($this->lang->line('admin_users_users_list_inactive_users')); else echo 'Inactive Users'; ?> ',value:<?php echo $totalInactiveUser; ?>}, 
				{Star:'<?php if ($this->lang->line('admin_users_users_list_deleted_users') != '') echo stripslashes($this->lang->line('admin_users_users_list_deleted_users')); else echo 'Deleted Users'; ?> ',value:<?php echo $totalDeletedUser; ?>}, 
			];
		// prepare jqxChart settings for user
		var userSettings = {
			title: "<?php echo $totalUsersList;?> <?php if ($this->lang->line('admin_users_total_count') != '') echo stripslashes($this->lang->line('admin_users_total_count')); else echo 'Total Users'; ?>",
			description: "-----",
			enableAnimations: true,
			showLegend: true,
			showBorderLine: true,
			//legendLayout: { left: 700, top: 160, width: 300, height: 200, flow: 'vertical' },
			legendPosition: { left: 700, top: 160, width: 300, height: 200, flow: 'vertical'  },
			padding: { left: 5, top: 5, right: 5, bottom: 5 },
			titlePadding: { left: 0, top: 0, right: 0, bottom: 10 },
			source: userRatingData,
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
		$('#useractInactContainer').jqxChart(userSettings);
		
		// prepare chart data for user ratings
		var  userRatingData = [
				{Star:'<?php if ($this->lang->line('admin_productive_users') != '') echo stripslashes($this->lang->line('admin_productive_users')); else echo 'Productive Users'; ?> ',value: <?php echo $totalRidedUser; ?>},
				{Star:'<?php if ($this->lang->line('admin_non_productive_users') != '') echo stripslashes($this->lang->line('admin_non_productive_users')); else echo 'Non-Productive Users'; ?> ',value:<?php echo $totalunRidedUser; ?>}
			];
		// prepare jqxChart settings for user
		var userSettings = {
			title: "",
			description: "",
			enableAnimations: true,
			showLegend: true,
			showBorderLine: true,
			//legendLayout: { left: 700, top: 160, width: 300, height: 200, flow: 'vertical' },
			legendPosition: { left: 700, top: 160, width: 300, height: 200, flow: 'vertical'  },
			padding: { left: 5, top: 5, right: 5, bottom: 5 },
			titlePadding: { left: 0, top: 0, right: 0, bottom: 10 },
			source: userRatingData,
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
		$('#userRidUnRContainer').jqxChart(userSettings);
	});
</script>

<style>
.stat_block {
  height:auto !important;
}
.rider_block tr {
  height:35px !important;
}
.chart_container{
	width: 100%;
	height: 340px;
	margin:3% auto;
}
.grid_container .grid_6{
	margin-bottom:15px;
}
</style>
<?php 
$this->load->view(ADMIN_ENC_URL.'/templates/footer.php');
?>