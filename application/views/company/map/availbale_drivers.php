<?php
$this->load->view(COMPANY_NAME.'/templates/header.php');
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
#location {
    clear: both;
    height: 34px;
    margin: 0 10px 15px 0;
    width: 50%;
}
#btn_find {
    background-color: #e84c3d ;
    border:1px solid #e84c3d ;
    border-radius: 3px;
    box-shadow: none;
    color: #fff;
    cursor: pointer;
    display: inline-block;
    font-size: 14px;
    font-weight: 400;
    height: auto;
    line-height: 1.42857 !important;
    margin-bottom: 0;
    padding: 6px 25px;
    text-align: center;
    text-shadow: none;
    vertical-align: top;
    white-space: nowrap;
}
.activities_s{
	width: 31.9%;
}
.block_label small{
	 padding-top: 30px;
}
</style>
<div id="content">
		<div class="grid_container">
			<div class="grid_12">
				<div class="widget_wrap">
					<div class="widget_top">
						<span class="h_icon list"></span>
						<h6><?php if ($this->lang->line('admin_map_view_display_drivers') != '') echo stripslashes($this->lang->line('admin_map_view_display_drivers')); else echo 'Display available drivers in their location'; ?></h6>
                        <div id="widget_tab">
            			</div>
					</div>
					<div class="widget_content" style="padding: 20px 0;">
							<form>
								<div class="grid_12">
							<input name="location" id="location" type="text"  class="form-control" value="<?php if(isset($address)){ echo $address; } ?>" autocomplete="off" placeholder="<?php if ($this->lang->line('admin_enter_location') != '') echo stripslashes($this->lang->line('admin_enter_location')); else echo 'Enter location'; ?>"/>
								<button type="submit" class="btn" id="btn_find" ><?php if ($this->lang->line('admin_map_find') != '') echo stripslashes($this->lang->line('admin_map_find')); else echo 'Find'; ?></button>
								</div>
								
								<div class="grid_12">
									<?php echo $map['js']; ?>
									<?php echo $map['html']; ?>
								</div>
							</form>
					</div>
				</div>
			</div>
		</div>
		 <div class="grid_12 center_driver_mode">
				<div class="widget_wrap">
				   <div class="widget_content">
						
							<div class="social_activities cabily_dash">	
									<?php if($address != '') {?>
									<h2 style="border-bottom: medium solid; margin-bottom: 12px;width: 90%;"><b><?php if ($this->lang->line('admin_map_drivers_near') != '') echo stripslashes($this->lang->line('admin_map_drivers_near')); else echo 'Drivers Near'; ?> : </b><?php echo $address; ?></h2>
									<?php } ?>
									<h6></h6>
									<a class="activities_s site_statistics" href="javascript:void(0)">
										<div class="block_label">
											<span><?php if (isset($online_drivers)) echo $online_drivers; ?></span>
											<small><?php if ($this->lang->line('admin_map_online_drivers') != '') echo stripslashes($this->lang->line('admin_map_online_drivers')); else echo 'Online Drivers'; ?></small>
											<i class="user_icon_font fa fa-users"></i>
										</div>
									</a>								
									<a class="activities_s site_statistics" href="javascript:void(0)">
										<div class="block_label">
											<span><?php if (isset($offline_drivers)) echo $offline_drivers; ?></span>
										 <small><?php if ($this->lang->line('admin_map_offline_drivers') != '') echo stripslashes($this->lang->line('admin_map_offline_drivers')); else echo 'Offline Drivers'; ?>	</small>
										 <i class="user_icon_font fa fa-users"></i>
											
										</div>
									</a>							
									<div class="activities_s site_statistics">
										<div class="block_label">
											<span><?php if (isset($onride_drivers)) echo $onride_drivers; ?></span>
											<small><?php if ($this->lang->line('admin_map_on_ride_drivers') != '') echo stripslashes($this->lang->line('admin_map_on_ride_drivers')); else echo 'On Ride Drivers'; ?></small>
											<i class="user_icon_font fa fa-users"></i>
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