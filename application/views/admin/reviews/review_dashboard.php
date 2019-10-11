<?php
$this->load->view(ADMIN_ENC_URL.'/templates/header.php');
extract($privileges);
?>
<?php 
if ($this->lang->line('admin_below_one') != '') 
	$admin_below_one=stripslashes($this->lang->line('admin_below_one')); 
else
	$admin_below_one='Below 1 Star';
if ($this->lang->line('admin_below_two') != '') 
	$admin_below_two=stripslashes($this->lang->line('admin_below_two')); 
else
	$admin_below_two='1-2 star';
if ($this->lang->line('admin_below_three') != '') 
	$admin_below_three=stripslashes($this->lang->line('admin_below_three')); 
else
	$admin_below_three='2-3 star';
if ($this->lang->line('admin_below_four') != '') 
	$admin_below_four=stripslashes($this->lang->line('admin_below_four')); 
else
	$admin_below_four='3-4 star';
if ($this->lang->line('admin_above_four') != '') 
	$admin_above_four=stripslashes($this->lang->line('admin_above_four')); 
else
	$admin_above_four='Above 4 Star';
	

?>
<div id="content" style="clear:both;">
	<div class="grid_container">
		<div class="grid_6">
			<div class="widget_wrap">
				<div class="widget_top">
					<span class="h_icon list"></span>
					<h6><?php if ($this->lang->line('admin_users_rating') != '') echo stripslashes($this->lang->line('admin_users_rating')); else echo 'User Ratings'; ?></h6>
				</div>
				<div class="widget_content">
					<div id='userRatingContainer' class="chart_container"></div>
				</div>
			</div>
		</div>
		<div class="grid_6">
			<div class="widget_wrap">
				<div class="widget_top">
					<span class="h_icon list"></span>
					<h6><?php if ($this->lang->line('admin_driver_rating') != '') echo stripslashes($this->lang->line('admin_driver_rating')); else echo 'Driver Ratings'; ?></h6>
				</div>
				<div class="widget_content">
					<div id="driverRatingContainer" class="chart_container"></div>
				</div>
			</div>
		</div>
	</div>
	<span class="clear"></span>
	<div class="grid_container">
		<div class="grid_6">
			<div class="widget_wrap">
				<div class="widget_top">
					<span class="h_icon graph"></span>
					<h6><?php if ($this->lang->line('admin_top_three_user') != '') echo stripslashes($this->lang->line('admin_top_three_user')); else echo 'Top 3 Users'; ?></h6>
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
									 <?php if ($this->lang->line('admin_users_users_list_thumbnail') != '') echo stripslashes($this->lang->line('admin_users_users_list_thumbnail')); else echo 'Thumbnail'; ?>
								</th>
								<th>
									<?php if ($this->lang->line('admin_rating') != '') echo stripslashes($this->lang->line('admin_rating')); else echo 'Ratings'; ?>
								
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
									<div class="widget_thumb">
										<?php if (isset($user['image']) && $user['image'] != ''){?>
											<img width="40px" height="40px" src="<?php echo base_url().USER_PROFILE_THUMB.$user['image'];?>" />
										<?php }else {?>
											<img width="40px" height="40px" src="<?php echo base_url().USER_PROFILE_THUMB_DEFAULT;?>" />
										<?php }?>
									</div>
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
					<span class="h_icon graph"></span>
					<h6><?php if ($this->lang->line('admin_top_three_driver') != '') echo stripslashes($this->lang->line('admin_top_three_driver')); else echo 'Top 3 Drivers'; ?></h6>
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
									 <?php if ($this->lang->line('admin_users_users_list_thumbnail') != '') echo stripslashes($this->lang->line('admin_users_users_list_thumbnail')); else echo 'Thumbnail'; ?>
								</th>
								<th>
									<?php if ($this->lang->line('admin_rating') != '') echo stripslashes($this->lang->line('admin_rating')); else echo 'Ratings'; ?>
								
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
									<div class="widget_thumb">
										<?php if (isset($data['image']) && $data['image'] != ''){?>
											<img width="40px" height="40px" src="<?php echo base_url().USER_PROFILE_THUMB.$data['image'];?>" />
										<?php }else {?>
											<img width="40px" height="40px" src="<?php echo base_url().USER_PROFILE_THUMB_DEFAULT;?>" />
										<?php }?>
									</div>
								</td>
								<td>
									<div class="star str" id="star-pos<?php echo $data['_id'] ?>"  data-star="<?php echo $data['avg_review'] ?>" style="width: 200px;float:none;"></div>
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
		
	</div>
	<span class="clear"></span>
	<div class="grid_container">
		<div class="grid_6">
			<div class="widget_wrap">
				<div class="widget_top">
					<span class="h_icon graph"></span>
					<h6><?php if ($this->lang->line('admin_users_rating') != '') echo stripslashes($this->lang->line('admin_users_rating')); else echo 'User Ratings'; ?></h6>
				</div>
				<div class="widget_content">
					<div class="stat_block">
						<?php if(!empty($user_review)) {?>
						<table>
                            <thead>
                                <tr style="border-top: #ccc 1px solid;">
                                <td style="background: -moz-linear-gradient(top, #eeeeee 0%, #cccccc 100%);border-left: #ccc 1px solid; padding: 15px 12px;"><b><?php if ($this->lang->line('admin_dash_review_title') != '') echo stripslashes($this->lang->line('admin_dash_review_title')); else echo 'Review Title'; ?></b></td>
                                <td style="background: -moz-linear-gradient(top, #eeeeee 0%, #cccccc 100%); padding: 15px 12px;"><b><?php if ($this->lang->line('admin_dash_avg_ratings') != '') echo stripslashes($this->lang->line('admin_dash_avg_ratings')); else echo 'Avg Ratings'; ?></b></td>
                                <td style="background: -moz-linear-gradient(top, #eeeeee 0%, #cccccc 100%);border-right: #ccc 1px solid; padding: 15px 12px;"><b><?php if ($this->lang->line('admin_dash_total_ratings') != '') echo stripslashes($this->lang->line('admin_dash_total_ratings')); else echo 'Total Ratings'; ?></b></td>
                                </tr>
                            </thead>
							<tbody>
								<?php $u=0; foreach ($user_review as $data)  { $u++; ?>
								<tr>
									<td>
										<?php 
											
										  echo $data['title'];
										
										?> 
										
									</td>
									<td>
										<?php
										$user_rating = 0;
										if(isset($data['avg_ratting'])){
											if($data['avg_ratting']>0){
												$user_rating = $data['avg_ratting'];
											}
										}
										?>
										<div class="star str" id="star-posu<?php  echo $u; ?>"  data-star="<?php echo $user_rating; ?>" style="width: 200px;float:none;"></div>
										
									</td>
									<td>
									<?php 
										
									  echo $data['total_count'];
									
									?> 
										
									</td>
									
								</tr>
								<?php }?>
								
							</tbody>
						</table>
					 <?php }?>
					</div>
				</div>
			</div>
		</div>
		<div class="grid_6">
			<div class="widget_wrap">
				<div class="widget_top">
					<span class="h_icon graph"></span>
					<h6><?php if ($this->lang->line('admin_driver_rating') != '') echo stripslashes($this->lang->line('admin_driver_rating')); else echo 'Driver Ratings'; ?></h6>
				</div>
				<div class="widget_content">
					<div class="stat_block">
						<?php if(!empty($driver_review)) { ?>
						<table>
                            <thead>
                                <tr style="border-top: #ccc 1px solid;">
                                <td style="background: -moz-linear-gradient(top, #eeeeee 0%, #cccccc 100%);border-left: #ccc 1px solid; padding: 15px 12px;"><b><?php if ($this->lang->line('admin_dash_review_title') != '') echo stripslashes($this->lang->line('admin_dash_review_title')); else echo 'Review Title'; ?></b></td>
                                <td style="background: -moz-linear-gradient(top, #eeeeee 0%, #cccccc 100%); padding: 15px 12px;"><b><?php if ($this->lang->line('admin_dash_avg_ratings') != '') echo stripslashes($this->lang->line('admin_dash_avg_ratings')); else echo 'Avg Ratings'; ?></b></td>
                                <td style="background: -moz-linear-gradient(top, #eeeeee 0%, #cccccc 100%);border-right: #ccc 1px solid; padding: 15px 12px;"><b><?php if ($this->lang->line('admin_dash_total_ratings') != '') echo stripslashes($this->lang->line('admin_dash_total_ratings')); else echo 'Total Ratings'; ?></b></td>
                                </tr>
                            </thead>
							<tbody>
								<?php $d=0; foreach ($driver_review as $data)  { $d++; ?>
								<tr>
									<td>
										<?php 
											
											echo $data['title'];
										
										?> 
										
									</td>
									<td>
										<?php
										$driver_rating = 0;
										if(isset($data['avg_ratting'])){
											if($data['avg_ratting']>0){
												$driver_rating = $data['avg_ratting'];
											}
										}
										?>
										<div class="star str" id="star-posd<?php  echo $d; ?>"  data-star="<?php echo $driver_rating; ?>" style="width: 200px;float:none;"></div>
										
									</td>
									<td>
									<?php 
										
									  echo $data['total_count'];
									
									?> 
										
									</td>
									
								</tr>
								<?php }?>
								
							</tbody>
						</table>
					 <?php }?>
					</div>
				</div>
			</div>
		</div>
		
	</div>
</div>
</div>

<link rel="stylesheet" href="plugins/jqwidgets-master/jqwidgets/styles/jqx.base.css" type="text/css" />
<script type="text/javascript" src="plugins/jqwidgets-master/jqwidgets/jqxcore.js"></script>
<script type="text/javascript" src="plugins/jqwidgets-master/jqwidgets/jqxdata.js"></script>
<script type="text/javascript" src="plugins/jqwidgets-master/jqwidgets/jqxdraw.js"></script>
<script type="text/javascript" src="plugins/jqwidgets-master/jqwidgets/jqxchart.core.js"></script>


<script type="text/javascript">
	$(document).ready(function () {
	
		//$.jqx._jqxChart.prototype.colorSchemes.push({ name: 'myScheme', colors: ['#6680C6', '#E35912', '#FBB300', '#E30F74', '#78D22D'] });
		$.jqx._jqxChart.prototype.colorSchemes.push({ name: 'myScheme', colors: ['#5B69BC', '#10C469', '#FFBD4A', '#35B8E0', '#F05050'] });
		
		// prepare chart data for user ratings
		var  userRatingData = [
				{Star:'<?php echo $admin_below_one; ?> ',value: <?php echo $user_one_star; ?>},
				{Star:'<?php echo $admin_below_two; ?> ',value:<?php echo $user_two_star; ?>}, 
				{Star:'<?php echo $admin_below_three; ?> ',value:<?php echo $user_three_star; ?>},
				{Star:'<?php echo $admin_below_four; ?> ',value:<?php echo $user_four_star; ?>},
				{Star:'<?php echo $admin_above_four; ?> ',value:<?php echo $user_five_star; ?>}
			];
			console.log(userRatingData);
		// prepare jqxChart settings for user
		var userSettings = {
			title: "<?php if ($this->lang->line('admin_users_rating') != '') echo stripslashes($this->lang->line('admin_users_rating')); else echo 'User Ratings';?>",
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
									return parseFloat(value) + ' <?php if($this->lang->line('admin_review_chart_user'))echo stripslashes($this->lang->line('admin_review_chart_user'));else echo 'Users' ;?>';
								},
							}]
				}]
		};
		$('#userRatingContainer').jqxChart(userSettings);
		
		
		// prepare chart data for driver ratings
		var  driverRatingData = [
				{Star:'<?php echo $admin_below_one; ?> ',value: <?php echo $driver_one_star; ?>},
				{Star:'<?php echo $admin_below_two; ?> ',value:<?php echo $driver_two_star; ?>}, 
				{Star:'<?php echo $admin_below_three; ?> ',value:<?php echo $driver_three_star; ?>},
				{Star:'<?php echo $admin_below_four; ?> ',value:<?php echo $driver_four_star; ?>},
				{Star:'<?php echo $admin_above_four; ?> ',value:<?php echo $driver_five_star; ?>}
			];
		// prepare jqxChart settings for driver
		var driverSettings = {
			title: "<?php if ($this->lang->line('admin_driver_rating') != '') echo stripslashes($this->lang->line('admin_driver_rating')); else echo 'Driver Ratings'; ?>",
			description: "-----",
			enableAnimations: true,
			showLegend: true,
			showBorderLine: true,
			//legendLayout: { left: 700, top: 160, width: 300, height: 200, flow: 'vertical' },
			legendPosition: { left: 700, top: 160, width: 300, height: 200, flow: 'vertical'  },
			padding: { left: 5, top: 5, right: 5, bottom: 5 },
			titlePadding: { left: 0, top: 0, right: 0, bottom: 10 },
			source: driverRatingData,
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
									return parseFloat(value) + " <?php if ($this->lang->line('admin_review_chart_drivers') != '') echo stripslashes($this->lang->line('admin_review_chart_drivers')); else echo 'Drivers'; ?>";
								},
							}]
				}]
		};
		$('#driverRatingContainer').jqxChart(driverSettings);
	});
</script>


<style>
.starRatings-count {
	float: right;
    margin-right: 73%;
    margin-top: -16px;
}

#tab2 h2 {
    border: 1px solid grey;
    border-radius: 8px;
	background-color: #a7a9ac;
}
.str,.star{
	width:100px !important;
}
.chart_container{
	width: 100%;
	height: 340px;
	margin:3% auto;
}
.grid_container .grid_6{
	margin-bottom:20px;
}

</style>
<?php 
$this->load->view(ADMIN_ENC_URL.'/templates/footer.php');
?>