<?php
$this->load->view(ADMIN_ENC_URL.'/templates/header.php');
extract($privileges);
?>
<?php 

$d_distance_unit_code = get_language_value_for_keyword('km',$this->data['langCode']);
$d_distance_unit = get_language_value_for_keyword('Kilometer',$this->data['langCode']);
if($d_distance_unit=="mi"){
$d_distance_unit_code = get_language_value_for_keyword('mi',$this->data['langCode']);
$d_distance_unit = get_language_value_for_keyword('Miles',$this->data['langCode']);
}
?>
<script>
		
	$(function (){
		$("#export_mileage").click(function(event){
			event.preventDefault();
			get_field_values();
			window.location.href = "<?php echo ADMIN_ENC_URL;?>/mileage/view_driver_mileage_report/<?php echo $driver_id; ?>?date_from="+$date_from+'&date_to='+$date_to+'&ride_id='+$ride_id;
		});
	});
	
	function get_field_values(){
		$date_from = $("#date_from").val();
		$date_to = $("#date_to").val();
		$ride_id = $("#filtervalue").val();
	}
</script>


<div id="content" class="milage_sec">
    <div class="grid_container">
	
		<div class="grid_12">
			<div class="">
					<div class="widget_content">
						<span class="clear"></span>						
						<div class="">
							<div class=" filter_wrap">
								<div class="widget_top filter_widget">
								
									<h6><?php if($this->lang->line('admin_mileage_view_ride_filter'))echo stripslashes($this->lang->line('admin_mileage_view_ride_filter'));else echo'Ride Filter';?></h6>
									<div class="btn_30_light">
									<?php
									$attributes = array('class' => 'form_container left_label', 'id' => 'filter_form','method'=>'GET','autocomplete'=>'off','enctype' => 'multipart/form-data');
									echo form_open(ADMIN_ENC_URL.'/mileage/view_driver_mileage/'.$driver_id, $attributes)
									?>
									<input name="ride_id" id="filtervalue" type="text"  class="tipTop" title="<?php if($this->lang->line('admin_rides_mileage_view_enter_keyword'))echo stripslashes($this->lang->line('admin_rides_mileage_view_enter_keyword'));else echo'Please enter rides id';?>" value="<?php if(isset($ride_id)) echo $ride_id; ?>" placeholder="<?php  if ($this->lang->line('admin_milage_ride_id') != '') echo stripslashes($this->lang->line('admin_milage_ride_id')); else echo 'Ride Id';?>" />
									
									<input name="date_from" id="date_from" type="hidden" value="<?php if(isset($start_date)) echo $start_date; ?>" />
									
									<input name="date_to" id="date_to" type="hidden" value="<?php if(isset($end_date)) echo $end_date; ?>"  />
				
										<button type="submit" class="tipTop filterbtn"  original-title="<?php if ($this->lang->line('driver_enter_keyword_filter') != '') echo stripslashes($this->lang->line('driver_enter_keyword_filter')); else echo 'Select filter type and enter keyword to filter'; ?>">
											<span class="icon search"></span><span class="btn_link"><?php if ($this->lang->line('admin_drivers_filter') != '') echo stripslashes($this->lang->line('admin_drivers_filter')); else echo 'Filter'; ?></span>
										</button>
										<?php if(isset($ride_id) && $ride_id!=""){ 
										 
												$urlVal='';
												if($end_date!='' && $start_date!=''){
													$enc_fromdate=$start_date;
													$enc_todate=$end_date;
													$urlVal='?&date_from='.$enc_fromdate.'&date_to='.$enc_todate;
												}
										
										?>
										
										<a href="<?php echo ADMIN_ENC_URL;?>/mileage/view_driver_mileage/<?php echo $driver_id; ?><?php echo $urlVal; ?>" class="tipTop filterbtn" original-title="<?php if ($this->lang->line('driver_enter_view_all_users') != '') echo stripslashes($this->lang->line('driver_enter_view_all_users')); else echo 'View All Users'; ?>">
											<span class="icon delete_co"></span><span class="btn_link"><?php if ($this->lang->line('admin_drivers_remove_filter') != '') echo stripslashes($this->lang->line('admin_drivers_remove_filter')); else echo 'Remove Filter'; ?></span>
										</a>
										<?php } ?>
										</form>
									</div>
								</div>
							</div>
						</div>
					</div>
							        <div class="grid_12">
            <div class="widget_wrap">
                
                <div class="widget_content">
                    <div class="stat_block">
                        <div class="social_activities mileage-block">								
                            <a class="activities_s bluebox" href="javascript:void(0)">
                                <div class="block_label">
									<?php if ($this->lang->line('admin_drivers_mileage_total_distance') != '') echo stripslashes($this->lang->line('admin_drivers_mileage_total_distance')); else echo 'Total Distance'; ?>
                                    <span class="lct"><?php echo number_format($total_distance,2,'.','');?> <?php echo $d_distance_unit_code; ?></span>
									<br>
									<?php if ($this->lang->line('admin_drivers_mileage_total_duration') != '') echo stripslashes($this->lang->line('admin_drivers_mileage_total_duration')); else echo 'Total Duration'; ?>
                                    <span class="lct"><?php echo convertToHoursMins($total_duration);?> <?php if ($this->lang->line('admin_drivers_mileage_hrs') != '') echo stripslashes($this->lang->line('admin_drivers_mileage_hrs')); else echo 'Hrs'; ?></span>
                                    
                                </div>
                            </a>								
                           
							<a class="activities_s orangebox" href="javascript:void(0)">
                                <div class="block_label">
                                 <?php if ($this->lang->line('admin_drivers_mileage_total_roaming_distance') != '') echo stripslashes($this->lang->line('admin_drivers_mileage_total_roaming_distance')); else echo 'Total Free Roaming Distance'; ?>   
												<span class="lct"><?php echo number_format($tot_free_distance,2,'.','');?> <?php echo $d_distance_unit_code; ?></span>
									<br>
											<?php if ($this->lang->line('admin_drivers_mileage_total_roaming_duration') != '') echo stripslashes($this->lang->line('admin_drivers_mileage_total_roaming_duration')); else echo 'Total Free Roaming Duration'; ?>   
                                    <span class="lct"><?php echo convertToHoursMins($tot_free_duration);?> <?php if ($this->lang->line('admin_drivers_mileage_hrs') != '') echo stripslashes($this->lang->line('admin_drivers_mileage_hrs')); else echo 'Hrs'; ?></span>
                                </div>
                            </a>
						  <a class="activities_s greenbox" href="javascript:void(0)">
                                <div class="block_label">
                                 <?php if ($this->lang->line('admin_drivers_mileage_total_pickup_distance') != '') echo stripslashes($this->lang->line('admin_drivers_mileage_total_pickup_distance')); else echo 'Total Approaching Distance'; ?>   	
                                    <span class="lct"><?php echo number_format($tot_pick_distance,2,'.','');?> <?php echo $d_distance_unit_code; ?></span>
									<br>
											<?php if ($this->lang->line('admin_drivers_mileage_total_pickup_duration') != '') echo stripslashes($this->lang->line('admin_drivers_mileage_total_pickup_duration')); else echo 'Total Pickup Duration'; ?>   	
                                   <span class="lct"><?php echo convertToHoursMins($tot_pick_duration);?> <?php if ($this->lang->line('admin_drivers_mileage_hrs') != '') echo stripslashes($this->lang->line('admin_drivers_mileage_hrs')); else echo 'Hrs'; ?></span>
                                </div>
                          </a>
						  <a class="activities_s redbox" href="javascript:void(0)">
                                <div class="block_label">
											<?php if ($this->lang->line('admin_drivers_mileage_total_trip_distance') != '') echo stripslashes($this->lang->line('admin_drivers_mileage_total_trip_distance')); else echo 'Total Trip Distance'; ?>   	
                                    <span class="lct"><?php echo number_format($tot_drop_distance,2,'.','');?> <?php echo $d_distance_unit_code; ?></span>
									<br>
											<?php if ($this->lang->line('admin_drivers_mileage_total_trip_duration') != '') echo stripslashes($this->lang->line('admin_drivers_mileage_total_trip_duration')); else echo 'Total Trip Duration'; ?>   	
												<span class="lct"><?php echo convertToHoursMins($tot_drop_duration);?> <?php if ($this->lang->line('admin_drivers_mileage_hrs') != '') echo stripslashes($this->lang->line('admin_drivers_mileage_hrs')); else echo 'Hrs'; ?></span>
                                </div>
                          </a>
							
							
							
							
                           	
                        </div>
                    </div>
                </div>
            </div>
        </div>	
	
	<style>										
			.b_warn {
				background: orangered none repeat scroll 0 0;
				border: medium none red;
			}
			
			.filter_widget .btn_30_light {
				margin: -11px;
				width: 83%;
			}
			.activities_s{
			width: 23%;
			}
	</style>
			</div>
		</div>
	
        <?php
        $attributes = array('id' => 'display_form');
        echo form_open(ADMIN_ENC_URL.'/drivers/change_driver_status_global', $attributes)
        ?>
        <div class="grid_12">
            <div class="widget_wrap">
                <div class="widget_top">
                    <span class="h_icon blocks_images"></span>
                    <h6><?php echo $heading ?></h6>
					<a style="color:#fff" class="p_edit tipTop export_report" id="export_mileage"> <?php if ($this->lang->line('admin_drivers_mileage_export') != '') echo stripslashes($this->lang->line('admin_drivers_mileage_export')); else echo 'Export'; ?></a>

                </div>
                <div class="widget_content">
                    

                    <table class="display" id="mileage_view">
                        <thead>
                            <tr>
                               <th class="tip_top" title="<?php if ($this->lang->line('dash_click_sort') != '') echo stripslashes($this->lang->line('dash_click_sort')); else echo 'Click to sort'; ?>">
                                 <?php if ($this->lang->line('admin_drivers_mileage_view_sno') != '') echo stripslashes($this->lang->line('admin_drivers_mileage_view_sno')); else echo 'SNO'; ?>   	
											</th> 
                               <th class="tip_top" title="<?php if ($this->lang->line('dash_click_sort') != '') echo stripslashes($this->lang->line('dash_click_sort')); else echo 'Click to sort'; ?>">
                                    <?php if ($this->lang->line('admin_drivers_mileage_view_from_time') != '') echo stripslashes($this->lang->line('admin_drivers_mileage_view_from_time')); else echo 'From Time'; ?>   
											</th>
                                <th class="tip_top" title="<?php if ($this->lang->line('dash_click_sort') != '') echo stripslashes($this->lang->line('dash_click_sort')); else echo 'Click to sort'; ?>">
                                    <?php if ($this->lang->line('admin_drivers_mileage_view_to_time') != '') echo stripslashes($this->lang->line('admin_drivers_mileage_view_to_time')); else echo 'To Time'; ?>
												
                                </th>
                                <th class="tip_top" title="<?php if ($this->lang->line('dash_click_sort') != '') echo stripslashes($this->lang->line('dash_click_sort')); else echo 'Click to sort'; ?>">
                                   <?php if ($this->lang->line('admin_drivers_mileage_view_type') != '') echo stripslashes($this->lang->line('admin_drivers_mileage_view_type')); else echo 'Type'; ?>
											  
                                </th>
                                <th class="tip_top" title="<?php if ($this->lang->line('dash_click_sort') != '') echo stripslashes($this->lang->line('dash_click_sort')); else echo 'Click to sort'; ?>">
                                     <?php if ($this->lang->line('admin_drivers_mileage_view_duration') != '') echo stripslashes($this->lang->line('admin_drivers_mileage_view_duration')); else echo 'Duration'; ?> (<?php if ($this->lang->line('mileage_total_duration_metric') != '') echo stripslashes($this->lang->line('mileage_total_duration_metric')); else echo 'Hour'; ?>)
										  </th>
                                <th class="tip_top" title="<?php if ($this->lang->line('dash_click_sort') != '') echo stripslashes($this->lang->line('dash_click_sort')); else echo 'Click to sort'; ?>">
                                    <?php if ($this->lang->line('admin_drivers_mileage_view_distance') != '') echo stripslashes($this->lang->line('admin_drivers_mileage_view_distance')); else echo 'Distance'; ?>
												(<?php if ($this->lang->line('admin_drivers_mileage_kilometer') != '') echo stripslashes($this->lang->line('admin_drivers_mileage_kilometer')); else echo $d_distance_unit;?>)
                                </th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php
                                $sno=1;
                                foreach ($mileage_data['result'] as $key=>$row) {
								    
									
                                    ?>
                                    <tr style="border-bottom: 1px solid #dddddd !important;">
                                       
                                        <td class="center">
                                            <?php echo $sno; ?>
											  
                                        </td>
										<td class="center">
                                            <?php echo get_time_to_string('d-m-Y h:i:s A',MongoEPOCH($row['mileage_data']['start_time'])); ?>
											  
                                        </td>
										<td class="center">
                                            
											<?php echo get_time_to_string('d-m-Y h:i:s A',MongoEPOCH($row['mileage_data']['end_time'])); ?>
											  
                                        </td>
										<td class="center">
                                            <?php
											$mileage_data_type = "";
											if($row['mileage_data']['type']=="customer-pickup"){
												if($this->lang->line('admin_rides_mileage_approach_trip')) $mileage_data_type = ($this->lang->line('admin_rides_mileage_approach_trip'));else $mileage_data_type ='Approaching Trip';
											}
											if($row['mileage_data']['type']=="customer-drop"){
												if($this->lang->line('admin_rides_mileage_on_trip')) $mileage_data_type = ($this->lang->line('admin_rides_mileage_on_trip'));else $mileage_data_type = 'On Trip';
											}
											if($row['mileage_data']['type']=="free-roaming"){
												if($this->lang->line('admin_rides_mileage_view_free_roaming')) $mileage_data_type = ($this->lang->line('admin_rides_mileage_view_free_roaming'));else $mileage_data_type = 'Free Roaming';
											}
											echo $mileage_data_type;
											
											if(isset($row['mileage_data']['ride_id']) && $row['mileage_data']['ride_id']!='') {
												echo " (".$row['mileage_data']['ride_id'].")";											
											}
											?>
                                        </td>
										<td class="center">
                                            <?php echo convertToHoursMins($row['mileage_data']['duration_min']); ?>
											  
                                        </td>
										<td class="center">
                                           <?php echo number_format($row['mileage_data']['distance'],2,'.',''); ?>
                                        </td>
																				

                                        
                                       
                                    </tr>
                                    <?php
									$sno++;
                                }
                            
                            ?>
                        </tbody>
                        <tfoot>
                            <tr>
								<th>
                                   <?php if ($this->lang->line('admin_drivers_mileage_view_sno') != '') echo stripslashes($this->lang->line('admin_drivers_mileage_view_sno')); else echo 'SNO'; ?>
                                </th> 
                                <th>
                                    <?php if ($this->lang->line('admin_drivers_mileage_view_from_time') != '') echo stripslashes($this->lang->line('admin_drivers_mileage_view_from_time')); else echo 'From Time'; ?>   
                                </th>
                                <th>
                                    <?php if ($this->lang->line('admin_drivers_mileage_view_to_time') != '') echo stripslashes($this->lang->line('admin_drivers_mileage_view_to_time')); else echo 'To Time'; ?>
                                </th>
                                <th>
                                   <?php if ($this->lang->line('admin_drivers_mileage_view_type') != '') echo stripslashes($this->lang->line('admin_drivers_mileage_view_type')); else echo 'Type'; ?>
                                </th>
                                <th>
                                    <?php if ($this->lang->line('admin_drivers_mileage_view_duration') != '') echo stripslashes($this->lang->line('admin_drivers_mileage_view_duration')); else echo 'Duration'; ?> (<?php if ($this->lang->line('mileage_total_duration_metric') != '') echo stripslashes($this->lang->line('mileage_total_duration_metric')); else echo 'Hour'; ?>)
                                </th>
                                <th>
                                    <?php if ($this->lang->line('admin_drivers_mileage_view_distance') != '') echo stripslashes($this->lang->line('admin_drivers_mileage_view_distance')); else echo 'Distance'; ?> (<?php if ($this->lang->line('admin_drivers_mileage_kilometer') != '') echo stripslashes($this->lang->line('admin_drivers_mileage_kilometer')); else echo $d_distance_unit;?>)
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
<style>										
.b_warn {
	background: orangered none repeat scroll 0 0;
	border: medium none red;
}
.filter_widget .btn_30_light {
	margin: -11px;
	width: 83%;
}
.lct{
	text-transform: lowercase !important;
}
</style>
<?php
$this->load->view(ADMIN_ENC_URL.'/templates/footer.php');
?>