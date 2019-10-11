<?php
$this->load->view('admin/templates/header.php');
extract($privileges);
?>
<div id="content">
		<div class="grid_container">
			<?php 
				$attributes = array('id' => 'display_form');
				echo form_open('admin/rides/display_rides',$attributes) 
			?>
			<div class="grid_12">
				<div class="widget_wrap">
					<div class="widget_top">
						<span class="h_icon blocks_images"></span>
						<h6><?php echo $heading?></h6>
					</div>
					<div class="widget_content">
						
						<table class="display display_tbl" id="avail_drivers">
						<thead>
						<tr>
							<th class="center" title="<?php if ($this->lang->line('dash_click_sort') != '') echo stripslashes($this->lang->line('dash_click_sort')); else echo 'Click to sort'; ?>">
								<?php if ($this->lang->line('admin_s_no') != '') echo stripslashes($this->lang->line('admin_s_no')); else echo 'S.No'; ?>
							</th>
							<th class="tip_top" title="<?php if ($this->lang->line('dash_click_sort') != '') echo stripslashes($this->lang->line('dash_click_sort')); else echo 'Click to sort'; ?>">
								 <?php if ($this->lang->line('admin_rides_driver_id') != '') echo stripslashes($this->lang->line('admin_rides_driver_id')); else echo 'Driver Id'; ?>
							</th>
							<th class="tip_top" title="<?php if ($this->lang->line('dash_click_sort') != '') echo stripslashes($this->lang->line('dash_click_sort')); else echo 'Click to sort'; ?>">
								 <?php if ($this->lang->line('admin_rides_name') != '') echo stripslashes($this->lang->line('admin_rides_name')); else echo 'Name'; ?>
							</th>
							<th class="tip_top" title="<?php if ($this->lang->line('dash_click_sort') != '') echo stripslashes($this->lang->line('dash_click_sort')); else echo 'Click to sort'; ?>">
								<?php if ($this->lang->line('admin_rides_rating') != '') echo stripslashes($this->lang->line('admin_rides_rating')); else echo 'Ratings'; ?>
							</th>
							<th class="tip_top" title="<?php if ($this->lang->line('dash_click_sort') != '') echo   stripslashes($this->lang->line('dash_click_sort')); else echo 'Click to sort'; ?>">
								<?php if ($this->lang->line('admin_rides_total_rides') != '') echo stripslashes($this->lang->line('admin_rides_total_rides')); else echo 'Total rides'; ?>
							</th>
							<th class="tip_top" title="<?php if ($this->lang->line('dash_click_sort') != '') echo stripslashes($this->lang->line('dash_click_sort')); else echo 'Click to sort'; ?>">
								<?php if ($this->lang->line('admin_rides_distance') != '') echo stripslashes($this->lang->line('admin_rides_distance')); else echo 'Distance'; ?>
							</th>
							<th>
								<?php if ($this->lang->line('admin_subadmin_action') != '') echo stripslashes($this->lang->line('admin_subadmin_action')); else echo 'Action'; ?> 
							</th>
						</tr>
						</thead>
						<tbody>
						<?php 
						if (count($driversList) > 0){ $i = 1;
							foreach ($driversList as $row){
						?>
						<tr>
							<td class="center tr_select ">
								<?php echo $i; ?>
							</td>							
							<td class="center">
								<?php  if(isset($row['_id'])) echo $row['_id']; ?>
							</td>
							<td class="center">
								<?php  if(isset($row['driver_name'])) echo $row['driver_name']; ?>
							</td>							
							<td class="center">
								<?php  if(isset($row['avg_review'])){ echo $row['avg_review']; }else{ echo 'N/A'; } ?>
							</td>
							
							<td class="center">
								<?php  if(isset($row['no_of_rides'])) echo $row['no_of_rides']; ?>
							</td>	
							
							<td class="center">
								<?php if (isset($row['distance'])) echo round($row['distance'], 2); ?> <?php echo $distance_unit; ?>
							</td>
							
							<td class="center">
								<ul class="action_list">
									<li style="width:100%;">
										<a class="p_car tipTop" href="admin/rides/assign_driver/<?php echo $rides_details->row()->ride_id;?>/<?php echo $row['_id'];?>" title="<?php if ($this->lang->line('admin_rides_assign_cab') != '') echo stripslashes($this->lang->line('admin_rides_assign_cab')); else echo 'Assign Cab'; ?>">
											
                                            <?php if ($this->lang->line('admin_send_req') != '') echo stripslashes($this->lang->line('admin_send_req')); else echo 'Send req'; ?>
										</a>
									</li>
								</ul>
							</td>
						</tr>
						<?php 
							$i++;
							}
						}
						?>
						</tbody>
						<tfoot>
						<tr>
							<th>
								<?php if ($this->lang->line('admin_s_no') != '') echo stripslashes($this->lang->line('admin_s_no')); else echo 'S.No'; ?>
							</th>
							<th>
								<?php if ($this->lang->line('admin_rides_driver_id') != '') echo stripslashes($this->lang->line('admin_rides_driver_id')); else echo 'Driver Id'; ?>
							</th>
							<th>
								<?php if ($this->lang->line('admin_rides_name') != '') echo stripslashes($this->lang->line('admin_rides_name')); else echo 'Name'; ?>
							</th>
							<th>
								<?php if ($this->lang->line('admin_rides_rating') != '') echo stripslashes($this->lang->line('admin_rides_rating')); else echo 'Ratings'; ?>
							</th>
							<th>
								<?php if ($this->lang->line('admin_rides_total_rides') != '') echo stripslashes($this->lang->line('admin_rides_total_rides')); else echo 'Total rides'; ?>
							</th>
							<th>
								<?php if ($this->lang->line('admin_rides_distance') != '') echo stripslashes($this->lang->line('admin_rides_distance')); else echo 'Distance'; ?>
							</th>
							<th>
								 <?php if ($this->lang->line('admin_subadmin_action') != '') echo stripslashes($this->lang->line('admin_subadmin_action')); else echo 'Action'; ?>
							</th>
						</tr>
						</tfoot>
						</table>
						
					</div>
				</div>
			</div>
			<input type="hidden" name="statusMode" id="statusMode"/>
			<input type="hidden" name="SubAdminEmail" id="SubAdminEmail"/>
		</form>	
			
		</div>
		<span class="clear"></span>
	</div>
</div>


<?php 
$this->load->view('admin/templates/footer.php');
?>