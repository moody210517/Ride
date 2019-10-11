<?php 
$this->load->view('driver/templates/profile_header.php');

?>

<link rel="stylesheet" href="plugins/jqwidgets-master/jqwidgets/styles/jqx.base.css" type="text/css" />
<script type="text/javascript" src="plugins/jqwidgets-master/jqwidgets/jqxcore.js"></script>
<script type="text/javascript" src="plugins/jqwidgets-master/jqwidgets/jqxdata.js"></script>
<script type="text/javascript" src="plugins/jqwidgets-master/jqwidgets/jqxdraw.js"></script>
<script type="text/javascript" src="plugins/jqwidgets-master/jqwidgets/jqxchart.core.js"></script>
 <script type="text/javascript" src="plugins/jqwidgets-master/jqwidgets/jqxchart.waterfall.js"></script>


<script type="text/javascript">
	$(document).ready(function () {
		// prepare chart data
	   var sampleData = <?php echo json_encode($weeklyEarningsGraph); ?>;
			
		// prepare jqxChart settings
		var settings = {
			title: "",
			description: "<?php if ($this->lang->line('driver_earnings_last_seven') != '')
								echo stripslashes($this->lang->line('driver_earnings_last_seven'));
									else  echo 'Last 7 days earnings statistics';
										?>",
			showLegend: true,
			enableAnimations: true,
			padding: { left: 20, top: 5, right: 20, bottom: 5 },
			titlePadding: { left: 60, top: 0, right: 0, bottom: 10 },
			source: sampleData,
			xAxis:
			{
				dataField: '<?php if ($this->lang->line('dash_days') != '') echo stripslashes($this->lang->line('dash_days')); else  echo 'Day'; ?>',
				displayText: '<?php if ($this->lang->line('dash_day') != '') echo stripslashes($this->lang->line('dash_day')); else  echo 'Day'; ?>',
				gridLines: { visible: false },
				flip: false
			},
			valueAxis:
			{
				flip: false,
				labels: {
					visible: true,
					formatFunction: function (value) {
						<?php if($weekly_total_earnings > 0) { ?>return parseInt(value); <?php } ?>
					}
				}
			},
			colorScheme: 'scheme01',
			seriesGroups:
				[
					{ 
						type: 'column',
						orientation: 'vertical',
						columnsGapPercent: 50,
						toolTipFormatSettings: { thousandsSeparator: ',' },
						series: [
								{ dataField: 'Earnings', displayText: '<?php if ($this->lang->line('dash_earnings') != '')
								echo stripslashes($this->lang->line('dash_earnings'));
									else  echo 'Earnings';
										?>  (<?php echo $dcurrencySymbol;?>)' }
							]
					}
				]
		};
		// setup the chart
		$('#last_week_earnings').jqxChart(settings);
	});
</script>
	
	<?php if (isset($yearRides) && $yearRides > 0){ ?>
	<script type="text/javascript">
        $(document).ready(function () {
            // prepare chart data as an array
			
				$.jqx._jqxChart.prototype.colorSchemes.push({ name: 'myScheme', colors: ['#ff8acc', '#35b8e0', '#5b69bc'] });
			
			 var RideData = [{'period_name' : "<?php if ($this->lang->line('dash_today') != '') echo stripslashes($this->lang->line('dash_today')); else echo 'Today'; ?>",'rides_count' : <?php if(isset($todayRides)) echo $todayRides; ?>},{'period_name' : "<?php if ($this->lang->line('dash_this_month') != '') echo stripslashes($this->lang->line('dash_this_month')); else echo 'This Month'; ?>",'rides_count' : <?php if(isset($monthRides)) echo $monthRides; ?>},{'period_name' : "<?php if ($this->lang->line('dash_this_year') != '') echo stripslashes($this->lang->line('dash_this_year')); else echo 'This Year'; ?>",'rides_count' : <?php if(isset($yearRides)) echo $yearRides; ?>}];
           
            // prepare jqxChart settings
            var RideSettings = {
                title: "",
                description: "<?php if ($this->lang->line('driver_ride_statistics') != '') echo stripslashes($this->lang->line('driver_ride_statistics')); else  echo 'Ride statistics'; ?>",
                enableAnimations: true,
                showLegend: false,
                showBorderLine: true,
				legendPosition: { left: 100, top: 80, width: 50, height: 50 },
                padding: { left: 2, top: 2, right: 2, bottom: 2 },
                titlePadding: { left: 0, top: 0, right: 0, bottom: 10 },
                source: RideData,
                colorScheme: 'myScheme',
                seriesGroups:
                    [
                        {
                            type: 'donut',
                            showLabels: true,
                            series:
                                [
                                    { 
                                        dataField: 'rides_count',
                                        displayText: 'period_name',
                                        labelRadius: 70,
                                        initialAngle: 15,
                                        radius: 110,
                                        innerRadius: 40,
                                        centerOffset: 0,
                                        formatSettings: { sufix :'	<?php if ($this->lang->line('driver_graph_view_Rides') != '')
                                    echo stripslashes($this->lang->line('driver_graph_view_Rides'));
                                        else  echo 'Rides';
                                            ?>',thousandsSeparator : ',' }
                                    }
                                ]
                        }
                    ]
            };
            // setup the chart
            $('#ride_summary').jqxChart(RideSettings);
        });
    </script>
	<?php } ?>
	
	 <script type="text/javascript">
        $(document).ready(function () {
            
			var data = <?php echo json_encode($MonthlyEarningsGraph); ?>; 
			
			 // convert raw data to differences
            for (var i = data.length - 1; i > 0; i--)
               data[i].Earnings -= data[i - 1].Earnings;
            // prepare jqxChart settings
            var settings = {
                title: "<?php if ($this->lang->line('driver_your_earnings_between') != '')
                                    echo stripslashes($this->lang->line('driver_your_earnings_between'));
                                        else  echo 'Your Earnings between';
                                            ?> <?php echo get_time_to_string('M Y',strtotime('-11 month'));?> <?php if ($this->lang->line('driver_earnings_and') != '')
                                    echo stripslashes($this->lang->line('driver_earnings_and'));
                                        else  echo 'and';
                                            ?>   <?php echo get_time_to_string('M Y',time());?>",
                description: "<?php if ($this->lang->line('driver_monthly_earnings_statistics') != '')
                                    echo stripslashes($this->lang->line('driver_monthly_earnings_statistics'));
                                        else  echo 'Monthly earnings variation statistics';
                                            ?>",
                enableAnimations: true,
                showLegend: false,
                padding: { left: 10, top: 5, right: 10, bottom: 5 },
                titlePadding: { left: 90, top: 0, right: 0, bottom: 10 },
                source: data,
                colorScheme: 'scheme06',
                xAxis:
                {
                    type: 'basic',
                    dataField: 'Month',
                    displayText: '<?php if ($this->lang->line('driver_graph_month') != '')
                                    echo stripslashes($this->lang->line('driver_graph_month'));
                                        else  echo 'Month';
                                            ?>',
                    labels: { angle: 0 }
                },
                valueAxis:
                {
                    title: {text: '<?php if ($this->lang->line('dash_earnings') != '')
                                    echo stripslashes($this->lang->line('dash_earnings'));
                                        else  echo 'Earnings';
                                            ?> <br>'},
                    unitInterval: 5000,
                    labels:
                    {
                        formatFunction: function (value) {
                            return value;
                        }
                    }
                },
                seriesGroups:
                    [
                        {
                            type: 'waterfall',
                            series:
                            [
                                {
                                    dataField: 'Earnings',
                                    summary: 'summary',
                                    displayText: '<?php if ($this->lang->line('dash_earnings') != '')
                                    echo stripslashes($this->lang->line('dash_earnings'));
                                        else  echo 'Earnings';
                                            ?>',
                                    colorFunction: function (value, itemIndex, serie, group) {
                                        if (itemIndex == data.length - 1)
                                            return '#383f52'; // total 
                                        return (value < 0) ? '#D30E2F' /* red */ : '#24A037' /*green*/;
                                    }
                                }
                            ]
                        }
                    ]
            };
			
            // setup the chart
            $('#earnings_summary').jqxChart(settings);
        });
    </script>



<section class="profile_pic_sec row">
   <div  class="profile_login_cont">
	   
	   <!----------------  Load profile side bar --------------------------------->
	   <?php
		$this->load->view('driver/templates/profile_sidebar.php');
		?> 
	   
	   <div class="share_detail">
		   <div class="share_det_title">
			  <h2><span>  <?php
                            if ($this->lang->line('driver_dashboard') != '')
                                echo stripslashes($this->lang->line('driver_dashboard'));
                            else
                                echo 'Dashboard';
                            ?> </span></h2>
		   </div>
		   <div class="profile_ac_inner_det">
			  <div class="dashboard">
				 <div class="ride_col">
					<div class="totalrides">
					   <h2> <?php
                                if ($this->lang->line('dash_total_rides') != '')
                                    echo stripslashes($this->lang->line('dash_total_rides'));
                                else
                                    echo 'Total Rides';
                                ?></h2>
					   <li class="count">
						  <div class="count_num"><?php if (isset($totalRides)) echo $totalRides; ?></div>
					   </li>
					   <li class="ride_count">
						  <div class="ride_count_detail">
							 <h6><?php if (isset($todayRides)) echo $todayRides; ?></h6>
							 <p> <?php
                                if ($this->lang->line('driver_rides_today') != '')
                                    echo stripslashes($this->lang->line('driver_rides_today'));
                                else
                                    echo 'Rides today';
                                ?></p>
						  </div>
					   </li>
					</div>
					 <div class="upcoming">
					   <h2> <?php
                                if ($this->lang->line('dash_cancelled') != '')
                                    echo stripslashes($this->lang->line('dash_cancelled'));
                                else
                                    echo 'Cancelled';
                                ?></h2>
					   <li class="count">
						  <div class="count_num"><?php if (isset($cancelledRides)) echo $cancelledRides; ?></div>
					   </li>
					   <li class="upcome_ride_count">
						  <div class="upcome_ride_count_detail">
							 <h6><?php if (isset($todayCancelledRides)) echo $todayCancelledRides; ?></h6>
							 <p>  <?php
                                if ($this->lang->line('driver_rides_today') != '')
                                    echo stripslashes($this->lang->line('driver_rides_today'));
                                else
                                    echo 'Rides today';
                                ?></p>
						  </div>
					   </li>
					</div> 
					<div class="completed">
					   <h2><?php
                                if ($this->lang->line('driver_rides_completed') != '')
                                    echo stripslashes($this->lang->line('driver_rides_completed'));
                                else
                                    echo 'Completed';
                                ?></h2>
					   <li class="completed_count count">
						  <div class="completed_count_num count_num"><?php if (isset($completedRides)) echo $completedRides; ?></div>
					   </li>
					   <li class="completed_ride_count">
						  <div class="completed_ride_count_detail">
							 <h6><?php if (isset($todayCompletedRides)) echo $todayCompletedRides; ?></h6>
							 <p> <?php
                                if ($this->lang->line('driver_rides_today') != '')
                                    echo stripslashes($this->lang->line('driver_rides_today'));
                                else
                                    echo 'Rides today';
                                ?></p>
						  </div>
					   </li>
					</div>
					<div class="onride">
					   <h2><?php
                                if ($this->lang->line('driver_on_ride') != '')
                                    echo stripslashes($this->lang->line('driver_on_ride'));
                                else
                                    echo 'On Ride';
                                ?></h2>
					   <li class="onride_count count">
						  <div class="onride_car_icon"><img src="images/site/onride_car_icon.png"></div>
					   </li>
					   <li class="onride_ride_count">
						  <div class="onride_ride_count_detail">
							 <div class="track"><a href="driver/rides/on_ride_details?act=track"><?php
                                if ($this->lang->line('driver_ride_track') != '')
                                    echo stripslashes($this->lang->line('driver_ride_track'));
                                else
                                    echo 'Track';
                                ?></a></div>
							 <div class="detail"><a href="driver/rides/on_ride_details?act=details"><?php
                                if ($this->lang->line('dash_details') != '')
                                    echo stripslashes($this->lang->line('dash_details'));
                                else
                                    echo 'Details';
                                ?></a></div>
						  </div>
					   </li>
					</div>
				 </div>
				 <div class="earning_col">
					<div class="last_weak_earning">
					   <h2><?php
                                if ($this->lang->line('driver_last_week_earnings') != '')
                                    echo stripslashes($this->lang->line('driver_last_week_earnings'));
                                else
                                    echo 'Last Week Earnings';
                                ?></h2>
					   <div class="last_weak_earn" >
							<div id="last_week_earnings"  style="width:400px; height:275px;"></div>
					   </div>
					</div>
					<div class="ride_summary">
					   <h2><?php
                                if ($this->lang->line('driver_ride_summary') != '')
                                    echo stripslashes($this->lang->line('driver_ride_summary'));
                                else
                                    echo 'Ride Summary';
                                ?></h2>
					   <div class="ride_summary_detail">
						  <div id="ride_summary" style="width: 300px; height: 275px;"></div>
					   </div>
					   <div class="summary_detail">
						  <ul>
							 <li class="today">    <?php
                                            if ($this->lang->line('dash_today') != '')
                                                echo stripslashes($this->lang->line('dash_today'));
                                            else
                                                echo 'Today';
                                            ?></li>
							 <li class="thismonth"><?php
                                            if ($this->lang->line('dash_this_month') != '')
                                                echo stripslashes($this->lang->line('dash_this_month'));
                                            else
                                                echo 'This Month';
                                            ?></li>
							 <li class="thisyear"> <?php
                                            if ($this->lang->line('dash_this_year') != '')
                                                echo stripslashes($this->lang->line('dash_this_year'));
                                            else
                                                echo 'This Year';
                                            ?></li>
						  </ul>
					   </div>
					</div>
					<div class="total_earning">
					   <h2><?php if ($this->lang->line('dash_earnings') != '')
                                    echo stripslashes($this->lang->line('dash_earnings'));
                                        else  echo 'Earnings';
                                            ?> </h2>
					   <div class="total_earning_detail">
							<div id="earnings_summary" style="width:740px; height: 425px;"></div>
					   </div>
					</div>
				 </div>
			  </div>
		   </div> 
		 </div>
	</div>
</section>


<?php
$this->load->view('driver/templates/footer.php');
?>