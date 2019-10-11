<?php
$this->load->view(ADMIN_ENC_URL.'/templates/header.php');
extract($privileges);
?> 

<style>
.yellowbox {
    background: #f7941d none repeat scroll 0 0 !important;
    border: 1px solid #f7941d;
}
.center_driver_mode {
    margin-top: 20px;
    background: none !important;
     width: 98.0%;
}
#btn_find{
	    padding: 4px 15px !important;
}
#location {
    clear: both;
    height: 31px;
    margin: 9px 0 9px 9px;
    width: 42%;
}
#btn_find {
    background-color: #e84c3d ;
    border:1px solid #e84c3d ;
    box-shadow: none;
    color: #fff;
    cursor: pointer;
    display: inline-block;
    font-size: 14px;
    font-weight: 400;
    height: auto;
    line-height: 1.42857 !important;
    margin-bottom: 0;
    padding: 4px 25px;
    text-align: center;
    text-shadow: none;
    vertical-align: top;
    white-space: nowrap;
	margin-top:10px;
}
.pad-box .block_label small{
	 padding-top: 30px;
	 padding-bottom: 25px;
}
.pad-box .redbox:nth-last-child(3n+1) {
	margin-left:0px;
}
.filter_unfilled_div{
	background: white !important;
    box-shadow: none;
    margin-bottom: 10px;
    box-shadow: 2px 2px 20px #ddd;
    background: white;
    padding: 8px;
}
</style>
<link rel="stylesheet" type="text/css" media="all" href="plugins/daterangepicker/css/daterangepicker.css" />
<script type="text/javascript" src="plugins/daterangepicker/js/moment.js"></script>
<script type="text/javascript" src="plugins/daterangepicker/js/daterangepicker.js"></script>
 <script>
	$(function () {
		$("#rideFromdate").datepicker({  maxDate: '<?php echo date('m/d/Y'); ?>' });
		$("#rideFromdate").datepicker("option", "showAnim", "clip");
		$("#rideTodate").datepicker({  minDate: $("#rideFromdate").val(),maxDate: '<?php echo date('m/d/Y'); ?>' });
		$("#rideFromdate").change(function(){
			$( "#rideTodate" ).datepicker( "option", "minDate", $("#rideFromdate").val() );
			$( "#rideTodate" ).datepicker( "option", "maxDate", <?php echo date('m/d/Y'); ?> );
			$("#rideTodate").datepicker("option", "showAnim", "clip");  // drop,fold,slide,bounce,slideDown,blind
		});
		
	});
</script>
<div id="content">
    <div class="grid_container">		
        <div class="grid_12" >
            <div class="widget_wrap">
                <div class="widget_top">
                    <span class="h_icon graph"><span style="display: none;"></span><canvas width="16" height="16"></canvas></span>
                    <h6><?php if ($this->lang->line('admin_dashboard_ride_dashboard') != '') echo stripslashes($this->lang->line('admin_dashboard_ride_dashboard')); else echo 'Ride Dashboard'; ?></h6>
                </div>
                <div class="widget_content ride-dashboard-page">
                   
                        <div class="social_activities cabily_dash">								
                            <div class="activities_s site_statistics">
                                <div class="block_label">
								<small><?php if ($this->lang->line('admin_dashboard_total_rides') != '') echo stripslashes($this->lang->line('admin_dashboard_total_rides')); else echo 'Total Rides'; ?></small>
                                    <span><?php if (isset($totalRides)) echo $totalRides; ?></span>
                                    
                                    <i class="user_icon_font fa fa-car"></i>
									<a href="<?php echo ADMIN_ENC_URL;?>/rides/display_rides?act=total" class="small-box-footer"><?php if ($this->lang->line('panel_more_info') != '') echo stripslashes($this->lang->line('panel_more_info')); else echo 'More info'; ?> <i class="fa fa-arrow-circle-right"></i></a>
                                </div>
                            </div>								
                            <div class="activities_s site_statistics" style="margin-left: 20px !important;">
                                <div class="block_label">
								 <small><?php if ($this->lang->line('admin_dashboard_upcomming_rides') != '') echo stripslashes($this->lang->line('admin_dashboard_upcomming_rides')); else echo 'Upcoming Rides'; ?></small>
                                    <span><?php if (isset($upcommingRides)) echo $upcommingRides; ?></span>
                                   
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
                                    <span><?php if (isset($riderDeniedRides)) echo $riderDeniedRides; ?></span>
                                    
                                    <i class="user_icon_font fa fa-car"></i>
									<a href="<?php echo ADMIN_ENC_URL;?>/rides/display_rides?act=riderCancelled" class="small-box-footer"><?php if ($this->lang->line('panel_more_info') != '') echo stripslashes($this->lang->line('panel_more_info')); else echo 'More info'; ?><i class="fa fa-arrow-circle-right"></i></a>
                                </div>
                            </div>							
                            <div class="activities_s site_statistics">
                                <div class="block_label">
								<small><?php if ($this->lang->line('admin_dashboard_driver_denied') != '') echo stripslashes($this->lang->line('admin_dashboard_driver_denied')); else echo 'Driver Denied'; ?></small>
                                    <span><?php if (isset($driverDeniedRides)) echo $driverDeniedRides; ?></span>
                                    
                                    <i class="user_icon_font fa fa-car"></i>
									<a href="<?php echo ADMIN_ENC_URL;?>/rides/display_rides?act=driverCancelled" class="small-box-footer"><?php if ($this->lang->line('panel_more_info') != '') echo stripslashes($this->lang->line('panel_more_info')); else echo 'More info'; ?><i class="fa fa-arrow-circle-right"></i></a>
                                </div>
                            </div>						
                            <div class="activities_s site_statistics">
                                <div class="block_label">
								<small><?php if ($this->lang->line('admin_dashboard_completed_rides') != '') echo stripslashes($this->lang->line('admin_dashboard_completed_rides')); else echo 'Completed Rides'; ?></small>
                                    <span><?php if (isset($completedRides)) echo $completedRides; ?></span>
                                    
                                    <i class="user_icon_font fa fa-car"></i>
									<a href="<?php echo ADMIN_ENC_URL;?>/rides/display_rides?act=Completed" class="small-box-footer"><?php if ($this->lang->line('panel_more_info') != '') echo stripslashes($this->lang->line('panel_more_info')); else echo 'More info'; ?> <i class="fa fa-arrow-circle-right"></i></a>
                                </div>
                            </div>								
                        </div>
                    
                </div>
            </div>
        </div>
    </div>
	
	<div class="grid_container">
		<h4 class="filter_unfilled_h4" style="margin: 12px;width: 60%;"><b><?php if ($this->lang->line('rides_map_unfilled_rides') != '') echo stripslashes($this->lang->line('rides_map_unfilled_rides')); else echo 'Unfilled Rides'; ?> : </b></h4>
		<div class="grid_12">
			<div class="widget_wrap filter_widget_wrap">
				<div class="widget_content">
					<?php
					$attributes = array('class' => '', 'id' => 'ride_dashboard','method'=>'POST','autocomplete'=>'off','enctype' => 'multipart/form-data');
					echo form_open('', $attributes)
					?>
						<div class="grid_12 M0 filter_unfilled_div">
							<input name="location" id="location" type="text"  class="form-control" value="<?php if(isset($address)){ echo $address; } ?>" autocomplete="off" placeholder="<?php if ($this->lang->line('admin_enter_location') != '') echo stripslashes($this->lang->line('admin_enter_location')); else echo 'Enter location'; ?>" />
							<input name="date_from" id="rideFromdate" style="padding:6px;" type="text"  class="tipTop monthYearPicker" title="<?php if ($this->lang->line('admin_ride_pls_starting_ride') != '') echo stripslashes($this->lang->line('admin_ride_pls_starting_ride')); else echo 'Please select the Starting Date'; ?>" readonly="readonly" value="<?php if(isset($date_from))echo $date_from; ?>" placeholder="<?php if ($this->lang->line('admin_ride_starting_ride') != '') echo stripslashes($this->lang->line('admin_ride_starting_ride')); else echo 'Starting Date'; ?>"/>
									
							<input name="date_to" id="rideTodate" style="padding:6px;" type="text"  class="tipTop monthYearPicker" title="<?php if ($this->lang->line('admin_ride_pls_ending_ride') != '') echo stripslashes($this->lang->line('admin_ride_pls_ending_ride')); else echo 'Please select the Ending Date'; ?>" readonly="readonly" value="<?php if(isset($date_to))echo $date_to; ?>"  placeholder="<?php if ($this->lang->line('admin_ride_ending_ride') != '') echo stripslashes($this->lang->line('admin_ride_ending_ride')); else echo 'Ending Date'; ?>"/>
							<button type="submit" class="btn" id="btn_find" ><?php if ($this->lang->line('admin_map_find') != '') echo stripslashes($this->lang->line('admin_map_find')); else echo 'Find'; ?></button>
				
								<a href= "<?php echo ADMIN_ENC_URL;?>/rides/ride_dashboard" class="btn"  id="btn_find" original-title="<?php if ($this->lang->line('admin_notification_remove_filter') != '') echo stripslashes($this->lang->line('admin_notification_remove_filter')); else echo 'Remove Filter'; ?>">
											<span class="icon delete_co" >Remove Filter</span>
										</a>
							
										
						</div>
						<div class="grid_12 M0">
							<div class="widget_wrap">
							   <div class="widget_content">
									
										<div class="social_activities pad-box ride-dashboard-socialactivity cabily_dash">	
												<?php if($address != '') {?>
												<h2 class="filter_location_name" style="border-bottom: medium solid; margin-bottom: 12px;width: 90%;font-size:14px !important;"><b><?php if ($this->lang->line('rides_map_unfilled_rides_near') != '') echo stripslashes($this->lang->line('rides_map_unfilled_rides_near')); else echo 'Unfilled Rides Near'; ?> : </b><?php echo $address; ?></h2>
												<?php } ?>							
												<div class="activities_s site_statistics">
													<div class="block_label">
														<span><?php if (isset($unfilled_rides)) echo $unfilled_rides; ?></span>
													 <small><?php if ($this->lang->line('rides_map_unfilled_rides') != '') echo stripslashes($this->lang->line('rides_map_unfilled_rides')); else echo 'Unfilled Rides'; ?></small>
														<i class="user_icon_font fa fa-car"></i>
													</div>
												</div>
												<?php 
												if(!empty($categories)){
													foreach($categories as $cat){
														if($cat['name']!=""){
													?>
													<div class="activities_s site_statistics">
														<div class="block_label">
															<small><?php if (isset($cat['name'])) echo $cat['name']; ?></small>	
															<span><?php if (isset($cat['count'])) echo $cat['count']; ?></span>
															<i class="user_icon_font fa fa-car"></i>
														</div>
													</div>
													<?php 
														}
													}
												} 
												?>
											
										</div>
									
								</div>
							</div>
						</div>
						<div class="grid_12 M0" style="margin-top:20px;">
							<?php echo $mapContent['js']; ?>
							<?php echo $mapContent['html']; ?>
						</div>
						
					</form>
				</div>
			</div>
		</div>
	</div>
	
</div>

</div>
<?php
$this->load->view(ADMIN_ENC_URL.'/templates/footer.php');
?>