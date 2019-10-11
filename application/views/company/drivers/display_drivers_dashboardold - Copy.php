<?php
$this->load->view(COMPANY_NAME.'/templates/header.php');

?>
<div id="content" style="clear:both;" class="dashboard_drive">
	<div class="grid_container">
		<div class="grid_6">
			<div class="widget_wrap">
				<div class="widget_top">
					<span class="h_icon graph"></span>
					<h6><?php echo $heading; ?></h6>
				</div>
				<div class="widget_content">
					<div class="stat_block">
						<h4><?php echo $totalUsersList;?> <?php if ($this->lang->line('admin_drivers_drivers_registered') != '') echo stripslashes($this->lang->line('admin_drivers_drivers_registered')); else echo 'drivers registered in this site'; ?> </h4>
						<table>
							<tbody>
								<tr>
									<td>
										<?php if ($this->lang->line('admin_drivers_active_drivers') != '') echo stripslashes($this->lang->line('admin_drivers_active_drivers')); else echo 'Active Drivers'; ?>
									</td>
									<td>
										<?php echo $totalActiveUser;?>
									</td>
								</tr>
								<tr>
									<td>
										<?php if ($this->lang->line('admin_drivers_inactive_drivers') != '') echo stripslashes($this->lang->line('admin_drivers_inactive_drivers')); else echo 'Inactive Drivers'; ?>
									</td>
									<td>
										<?php echo $totalInactiveUser;?>
									</td>
								</tr>
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
					<h6><?php if ($this->lang->line('admin_drivers_recent_drivers') != '') echo stripslashes($this->lang->line('admin_drivers_recent_drivers')); else echo 'Recent Drivers'; ?></h6>
				</div>
				<div class="widget_content">
					<table class="wtbl_list">
						<thead>
							<tr>
								<th>
									 <?php if ($this->lang->line('admin_car_types_name') != '') echo stripslashes($this->lang->line('admin_car_types_name')); else echo 'Name'; ?>
								</th>
								<th>
									 <?php if ($this->lang->line('admin_users_users_list_email') != '') echo stripslashes($this->lang->line('admin_users_users_list_email')); else echo 'Email'; ?>
								</th>
								<th>
									 <?php if ($this->lang->line('admin_users_users_list_thumbnail') != '') echo stripslashes($this->lang->line('admin_users_users_list_thumbnail')); else echo 'Thumbnail'; ?>
								</th>
								<th>
									 <?php if ($this->lang->line('admin_subadmin_status') != '') echo stripslashes($this->lang->line('admin_subadmin_status')); else echo 'Status'; ?>
								</th>
							</tr>
						</thead>
						<tbody>
							<?php 
							if ($recentdriversList->num_rows() > 0){
								foreach($recentdriversList->result() as $drivers){
							?>
							<tr class="tr_even">
								<td>
									 <?php echo $drivers->driver_name;?>
								</td>
								<td>
									 <?php echo $drivers->email;?>
								</td>
								<td>
									<div class="widget_thumb">
										<?php 
										if(isset($drivers->image)){
											if ($drivers->image != ''){
										?>
											<img width="40px" height="40px" src="<?php echo base_url().USER_PROFILE_THUMB.$drivers->image;?>" />
										<?php }else {?>
											<img width="40px" height="40px" src="<?php echo base_url().USER_PROFILE_THUMB_DEFAULT;?>" />
										<?php 
											}
										}else {
										?>
											<img width="40px" height="40px" src="<?php echo base_url().USER_PROFILE_THUMB_DEFAULT;?>" />
										<?php }?>
									</div>
								</td>
								<td>
									<?php 
									$disp_status = get_language_value_for_keyword($drivers->status,$this->data['langCode']); ?>
									<?php if (strtolower($drivers->status) == 'active'){?>
										 <span class="badge_style b_done"><?php echo $disp_status;?></span>
									<?php }else {?>
										 <span class="badge_style b_active"><?php echo $disp_status;?></span>
									<?php }?>
								</td>
							</tr>
							<?php 
									}
							}else {
							?>
							<tr>
								<td colspan="5" align="center"><?php if ($this->lang->line('admin_menu_dashboard') != '') echo stripslashes($this->lang->line('admin_menu_dashboard')); else echo 'Dashboard'; ?>
								<?php if ($this->lang->line('admin_drivers_no_driver_available') != '') echo stripslashes($this->lang->line('admin_drivers_no_driver_available')); else echo 'No Drivers Available'; ?>
								</td>
							</tr>
							<?php }?>
						</tbody>
					</table>
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