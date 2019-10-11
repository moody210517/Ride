<?php
$this->load->view(OPERATOR_NAME.'/templates/header.php');
extract($privileges);
$minDate=strtotime('2015-01-01');
?>
<link rel="stylesheet" type="text/css" media="all" href="plugins/daterangepicker/css/daterangepicker.css" />
<script type="text/javascript" src="plugins/daterangepicker/js/moment.js"></script>
<script type="text/javascript" src="plugins/daterangepicker/js/daterangepicker.js"></script>
<script>
	$(function () {
		$("#rideFromdate").datepicker({maxDate: '<?php echo date('m/d/Y'); ?>'});
		$("#rideFromdate").datepicker("option", "showAnim", "clip");
		$("#rideTodate").datepicker({  minDate: $("#rideFromdate").val(),maxDate: '<?php echo date('m/d/Y'); ?>' });
		$("#rideFromdate").change(function(){
			$( "#rideTodate" ).datepicker( "option", "minDate", $("#rideFromdate").val() );
			$( "#rideTodate" ).datepicker( "option", "maxDate", <?php echo date('m/d/Y'); ?> );
			$("#rideTodate").datepicker("option", "showAnim", "clip");  // drop,fold,slide,bounce,slideDown,blind
		});
		
	});
	
	$(function (){
		$("#rideLocation , #rideFromdate , #rideTodate").change(function(){
			$("#export_rides").text('<?php if ($this->lang->line('admin_rides_filtered_rides') != '') echo stripslashes($this->lang->line('admin_rides_filtered_rides')); else echo 'Export Filtered Rides'; ?>');
		});
		$("#export_rides").click(function(event){
			event.preventDefault();
			var query_strings = "<?php echo $_SERVER["QUERY_STRING"]; ?>";
			window.location.href = "<?php echo base_url().OPERATOR_NAME; ?>/trip/display_trips?" + query_strings + "&export=excel&export_type=limited";
		});
        
        
		$("#export_all_rides").click(function(event){
			event.preventDefault();
			var query_strings = "<?php echo $_SERVER["QUERY_STRING"]; ?>";
			window.location.href = "<?php echo base_url().OPERATOR_NAME; ?>/trip/display_trips?" + query_strings + "&export=excel&export_type=all";
		});
		
	});
	function submit_ride_filter(){
		get_field_values();
		window.location.href = "<?php echo base_url().OPERATOR_NAME; ?>/trip/display_trips?act=<?php if(isset($_GET['act']) && $_GET['act'] != ''){ echo $_GET['act'];} ?>&from="+$filter_from+'&to='+$filter_to+'&location='+$rideLocation;
	}
</script>
<script>
$(document).ready(function(){
   $('.vehicle_category').css("display","none");
   $(".vehicle_category").attr("disabled", true);
   $vehicle_category='';
	 $locations_id = '';
   <?php  if(isset($_GET['vehicle_category'])) {?>
	$vehicle_category = "<?php echo $_GET['vehicle_category']; ?>";
    <?php }?>    
		<?php  if(isset($type) && $type == 'driver_location' &&  isset($_GET['locations_id'])) {?>
	$locations_id = "<?php echo $_GET['locations_id']; ?>";
    <?php }?>
	if($vehicle_category != ''){
		$('.vehicle_category').css("display","inline");
		$('#filtervalue').css("display","none");
		$(".vehicle_category").attr("disabled", false);
	}
	if($locations_id != ''){
		$('.vehicle_category').attr("disabled", true);
		$('#filtervalue').css("display","none");
		$('#locations_id').css("display","inline");
	}
	$("#filtertype").change(function(){
		$filter_val = $(this).val();
		$('#filtervalue').val('');
		$('.vehicle_category').css("display","none");
		$('#filtervalue').css("display","inline");
        $(".vehicle_category").attr("disabled", true);
				$('#locations_id').css('display','none');
		if($filter_val == 'vehicle_type'){
			$('.vehicle_category').css("display","inline");
			$('#filtervalue').css("display","none");
            $('.vehicle_category').prop("disabled", false);
		}
		if($filter_val == 'driver_location'){ 
            $(".vehicle_category").attr("disabled", true);
            $('.vehicle_category').css("display","none");
			$('#filtervalue').css("display","none");
			$('#locations_id').css('display','inline');
		}
	});
	
});
</script>
<div id="content" class="just-booked-img">
    <div class="grid_container">
		<div class="grid_12">
			<div class="">
				<div class="widget_content">
					<span class="clear"></span>						
					<div class="">
						<div class=" filter_wrap">
							<div class="widget_top filter_widget">								
								<h6><?php if($this->lang->line('admin_ride_manual_filter') != '') echo stripslashes($this->lang->line('admin_ride_manual_filter')); else echo 'Manual Filter'; ?></h6>
									
								<div class="btn_30_light" style="width: 80%;">
										<?php
										$attributes = array('class' => '', 'id' => 'ride_filter_form','method'=>'GET','autocomplete'=>'off','enctype' => 'multipart/form-data');
										echo form_open(OPERATOR_NAME.'/trip/display_trips', $attributes)
										?>
										<input type="hidden" value="<?php if(isset($_GET['act']) && $_GET['act'] != ''){ echo $_GET['act'];} ?>" name="act">
										<select class="form-control" id="filtertype" name="type" >
											<option value="" data-val="">
											<?php if ($this->lang->line('admin_drivers_select_filter_type') != '') echo stripslashes($this->lang->line('admin_drivers_select_filter_type')); else echo 'Select Filter Type'; ?></option>
											<option value="driver_name" data-val="driver_name" <?php if(isset($type)){if($type=='driver_name'){ echo 'selected="selected"'; } }?>>
											<?php if ($this->lang->line('admin_drivers_driver_name') != '') echo stripslashes($this->lang->line('admin_drivers_driver_name')); else echo 'Driver Name'; ?></option>
											<option value="driver_email" data-val="driver_email" <?php if(isset($type)){if($type=='driver_email'){ echo 'selected="selected"'; } }?>><?php if ($this->lang->line('admin_drivers_change_password_driver_email') != '') echo stripslashes($this->lang->line('admin_drivers_change_password_driver_email')); else echo 'Driver Email'; ?></option>
											<option value="user_name" data-val="user_name" <?php if(isset($type)){if($type=='user_name'){ echo 'selected="selected"'; } }?>>
											<?php if ($this->lang->line('admin_users_users_list_user_name') != '') echo stripslashes($this->lang->line('admin_users_users_list_user_name')); else echo 'User Name'; ?></option>
											<option value="user_email" data-val="user_email" <?php if(isset($type)){if($type=='user_email'){ echo 'selected="selected"'; } }?>><?php if ($this->lang->line('admin_users_users_list_user_email') != '') echo stripslashes($this->lang->line('admin_users_users_list_user_email')); else echo 'User Email'; ?></option>
											<option value="driver_location" data-val="location" <?php if(isset($type)){if($type=='driver_location'){ echo 'selected="selected"'; } }?>><?php if ($this->lang->line('admin_location_and_fare_location_location') != '') echo stripslashes($this->lang->line('admin_location_and_fare_location_location')); else echo 'Location'; ?></option>
											<option value="vehicle_type" data-val="vehicle_type" <?php if(isset($type)){if($type=='vehicle_type'){ echo 'selected="selected"'; } }?>><?php if ($this->lang->line('admin_drivers_vehicle_type') != '') echo stripslashes($this->lang->line('admin_drivers_vehicle_type')); else echo 'Vehicle Type'; ?></option>
                                            <option value="ride_id" data-val="ride_id" <?php if(isset($type)){if($type=='ride_id'){ echo 'selected="selected"'; } }?>><?php if ($this->lang->line('admin_rides_ride_id') != '') echo stripslashes($this->lang->line('admin_rides_ride_id')); else echo 'Ride Id'; ?></option>
										</select>
									<?php /* <select class="chzn-select" name='location' id='rideLocation'>
									<option value=''><?php if($this->lang->line('admin_ride_location_filter') != '') echo stripslashes($this->lang->line('admin_ride_location_filter')); else echo 'Select location to filter'; ?>...</option>
									<?php foreach($locationLists->result() as $loc){
									if(isset($_GET['location']) && $_GET['location'] != ''){
										if($_GET['location'] == (string)$loc->_id ){
											echo '<option selected value="'.(string)$loc->_id.'">'.$loc->city.'</option>';
										}else{
											echo '<option value="'.(string)$loc->_id.'">'.$loc->city.'</option>';
										}	
									}else{
										echo '<option value="'.(string)$loc->_id.'">'.$loc->city.'</option>';
									}
									
									} ?>
									</select> */ ?>
									<select name="locations_id" class='locationsList' id="locations_id" style="display:none; width:200px !important;">
											<option value=""><?php if ($this->lang->line('admin_driver_filter_choose_loc') != '') echo stripslashes($this->lang->line('admin_driver_filter_choose_loc')); else echo 'Choose location'; ?>...</option>
										<?php 
											$loc_id = '';
											if(isset($_GET['locations_id']) && $_GET['locations_id']!=''){
												$loc_id = $_GET['locations_id'];
											}
											foreach($locationsList->result() as $loc){
												if($loc_id != '' && $loc_id == (string)$loc->_id){
													echo "<option selected value=".(string)$loc->_id.">".$loc->city."</option>";
												}else{
													echo "<option value=".(string)$loc->_id.">".$loc->city."</option>";
												}
												
											}
										?>
									</select>
									<input name="value" id="filtervalue" type="text"  class="tipTop" title="<?php if ($this->lang->line('driver_enter_keyword') != '') echo stripslashes($this->lang->line('driver_enter_keyword')); else echo 'Please enter keyword'; ?>" value="<?php if(isset($value)) echo $value; ?>" placeholder="<?php if ($this->lang->line('driver_enter_keyword') != '') echo stripslashes($this->lang->line('driver_enter_keyword')); else echo 'Please enter keyword'; ?>" />
									<select name="vehicle_category" class='vehicle_category' style="display:none">
									<?php 
										$veh_cat = '';
										if(isset($_GET['vehicle_category']) && $_GET['vehicle_category']!=''){
											$veh_cat = $_GET['vehicle_category'];
										}
										foreach($cabCats as $cat){
											if($veh_cat != '' && $veh_cat == (string)$cat->_id){
												echo "<option selected value=".(string)$cat->_id.">".$cat->name."</option>";
											}else{
												echo "<option value=".(string)$cat->_id.">".$cat->name."</option>";
											}
											
										}
									?>
									</select>
									<input name="date_range" id="rideFromdate" type="text"  class="tipTop monthYearPicker" title="<?php if ($this->lang->line('admin_ride_pls_starting_ride') != '') echo stripslashes($this->lang->line('admin_ride_pls_starting_ride')); else echo 'Please select the Starting Date'; ?>" readonly="readonly" value="<?php if(isset($_GET['date_range']))echo $_GET['date_range']; ?>" placeholder="<?php if ($this->lang->line('admin_ride_starting_ride') != '') echo stripslashes($this->lang->line('admin_ride_starting_ride')); else echo 'Starting Date'; ?>"/>
														
									<input name="dateto" id="rideTodate" type="text"  class="tipTop monthYearPicker" title="<?php if ($this->lang->line('admin_ride_pls_ending_ride') != '') echo stripslashes($this->lang->line('admin_ride_pls_ending_ride')); else echo 'Please select the Ending Date'; ?>" readonly="readonly" value="<?php if(isset($_GET['dateto']))echo $_GET['dateto']; ?>"  placeholder="<?php if ($this->lang->line('admin_ride_ending_ride') != '') echo stripslashes($this->lang->line('admin_ride_ending_ride')); else echo 'Ending Date'; ?>"/>
									
									<button type="submit" class="tipTop filterbtn"  original-title="<?php if ($this->lang->line('driver_enter_keyword_filter') != '') echo stripslashes($this->lang->line('driver_enter_keyword_filter')); else echo 'Select filter type and enter keyword to filter'; ?>">
										<span class="icon search"></span><span class="btn_link"><?php if ($this->lang->line('admin_rides_filter_rides') != '') echo stripslashes($this->lang->line('admin_rides_filter_rides')); else echo 'Filter Rides'; ?></span>
									</button>

									<?php if(isset($filter) && $filter!=""){ ?>
									<a href="<?php echo current_url(); ?>?act=<?php if(isset($_GET['act']) && $_GET['act'] != ''){ echo $_GET['act'];} ?>"class="tipTop" original-title="<?php if ($this->lang->line('admin_notification_remove_filter') != '') echo stripslashes($this->lang->line('admin_notification_remove_filter')); else echo 'Remove Filter'; ?>" style="margin: 0 5px -8px 7px;height: 26px;">
										<span class="icon delete_co" style="margin-top: 8px;"></span>
									</a>
									<?php } ?>
									</form>
								</div>
							</div>
						</div>
					</div>
				</div>
			</div>
		</div>
        <?php
        $attributes = array('id' => 'display_form');
        echo form_open(OPERATOR_NAME.'/trip/change_rides_status_global', $attributes)
        ?>
        <div class="grid_12">
            <div class="widget_wrap">
                <div class="widget_top">
                    <span class="h_icon blocks_images"></span>
                    <h6><?php echo $heading ?></h6>
					<?php if ($paginationLink != '') { ?>
					<a style="color:#fff" class="p_edit tipTop export_report" id="export_rides"><?php if ($this->lang->line('admin_ride_export') != '') echo stripslashes($this->lang->line('admin_ride_export')); else echo 'Export'; ?> <?php if(isset($_GET['act']) && $_GET['act'] != ''){ echo $disp_status = get_language_value_for_keyword(ucfirst(strtolower($_GET['act'])),$this->data['langCode']);} ?> <?php if ($this->lang->line('admin_ride_rides') != '') echo stripslashes($this->lang->line('admin_ride_rides')); else echo 'Rides'; ?> <?php if ($this->lang->line('admin_export_in_this_page') != '') echo stripslashes($this->lang->line('admin_export_in_this_page')); else echo 'In This Page'; ?></a>
					<?php } ?>
                    
                    <a style="color:#fff" class="p_edit tipTop export_report" id="export_all_rides"><?php if ($this->lang->line('admin_ride_export') != '') echo stripslashes($this->lang->line('admin_ride_export')); else echo 'Export'; ?> <?php if ($this->lang->line('common_all') != '') echo stripslashes($this->lang->line('common_all')); else echo 'All'; ?>  <?php if(isset($_GET['act']) && $_GET['act'] != ''){ echo $disp_status = get_language_value_for_keyword(ucfirst(strtolower($_GET['act'])),$this->data['langCode']);} ?> <?php if ($this->lang->line('admin_ride_rides') != '') echo stripslashes($this->lang->line('admin_ride_rides')); else echo 'Rides'; ?></a>
                </div>
                <div class="widget_content">
                  <?php
					$actionRide = $this->input->get('act');
					
					if($actionRide=='' || $actionRide=='Booked') {
					    if ($paginationLink != '') {
                           echo $paginationLink;
							$tble = 'alljustbk_tbl';
						} else {
							$tble = 'justbk_tbl';
						}
					
					} else if($actionRide=='OnRide') {
						if ($paginationLink != '') {
                           echo $paginationLink;
                        $tble = 'allonride_tbl';
						} else {
							$tble = 'onride_tbl';
						}
					} else if($actionRide=='Completed'){
						if ($paginationLink != '') {
                           echo $paginationLink;
                        $tble = 'allcompleted_tbl';
						} else {
							$tble = 'completed_tbl';
						}
					} else if($actionRide=='Expired') {
					
						if ($paginationLink != '') {
                           echo $paginationLink;
                        $tble = 'allexpired_tbl';
						} else {
							$tble = 'expired_tbl';
						}
					
					} else if($actionRide=='Cancelled' || $actionRide=='riderCancelled' || $actionRide=='driverCancelled'){
						if ($paginationLink != '') {
                           echo $paginationLink;
							$tble = 'allcancelled_tbl';
						} else {
							$tble = 'cancelled_tbl';
						}
					}else if($actionRide=='total'){
						$tble = 'total_rides_tbl';
					}
                    
                    ?>

                    <table class="display display_tbl" id="<?php echo $tble; ?>">
                        <thead>
                            <tr>
                                <th class="center tip_top" title="<?php if ($this->lang->line('dash_click_sort') != '') echo   stripslashes($this->lang->line('dash_click_sort')); else echo 'Click to sort'; ?>">
									<?php if ($this->lang->line('admin_s_no') != '') echo stripslashes($this->lang->line('admin_s_no')); else echo 'S.No'; ?>
                                </th>
                                <th class="tip_top" title="<?php if ($this->lang->line('dash_click_sort') != '') echo   stripslashes($this->lang->line('dash_click_sort')); else echo 'Click to sort'; ?>">
                                    <?php if ($this->lang->line('admin_rides_ride_id') != '') echo stripslashes($this->lang->line('admin_rides_ride_id')); else echo 'Ride Id'; ?>
                                </th>
                                <th class="tip_top" title="<?php if ($this->lang->line('dash_click_sort') != '') echo   stripslashes($this->lang->line('dash_click_sort')); else echo 'Click to sort'; ?>">
                                    <?php if ($this->lang->line('admin_rides_booked_date') != '') echo stripslashes($this->lang->line('admin_rides_booked_date')); else echo 'Booked Date'; ?>
                                </th>
                                <th class="tip_top" title="<?php if ($this->lang->line('dash_click_sort') != '') echo   stripslashes($this->lang->line('dash_click_sort')); else echo 'Click to sort'; ?>">
                                    <?php if ($this->lang->line('admin_rides_user') != '') echo stripslashes($this->lang->line('admin_rides_user')); else echo 'User'; ?>
                                </th>
                                <th <?php if($actionRide=='total'){ ?> class="tip_top" title="<?php if ($this->lang->line('dash_click_sort') != '') echo   stripslashes($this->lang->line('dash_click_sort')); else echo 'Click to sort'; ?>" <?php } ?>>
                                    <?php if ($this->lang->line('admin_subadmin_status') != '') echo stripslashes($this->lang->line('admin_subadmin_status')); else echo 'Status'; ?>
                                </th>
                                <?php
                                $actionRide = $this->input->get('act');
                                if ($actionRide == 'Completed') {
                                    ?>
                                    <th class="tip_top" title="<?php if ($this->lang->line('dash_click_sort') != '') echo   stripslashes($this->lang->line('dash_click_sort')); else echo 'Click to sort'; ?>">
                                        <?php if ($this->lang->line('admin_rides_rider_ratings') != '') echo stripslashes($this->lang->line('admin_rides_rider_ratings')); else echo 'Rider Ratings'; ?>
                                    </th>
                                    <th class="tip_top" title="<?php if ($this->lang->line('dash_click_sort') != '') echo   stripslashes($this->lang->line('dash_click_sort')); else echo 'Click to sort'; ?>">
                                        <?php if ($this->lang->line('admin_rides_driver_ratings') != '') echo stripslashes($this->lang->line('admin_rides_driver_ratings')); else echo 'Driver Ratings'; ?>
                                    </th>
                                    <?php
                                }
                                ?>
                                <?php
                                if ($actionRide=='' || $actionRide == 'Booked') {
                                    ?>
							    <?php if ($allPrev == '1' || in_array('2', $rides)) { ?>
                                    <th class="tip_top">
                                        <?php if ($this->lang->line('admin_rides_assign_cab') != '') echo stripslashes($this->lang->line('admin_rides_assign_cab')); else echo 'Assign Cab'; ?>
                                    </th>
                                    <?php
									}
                                }
                                ?>
								<?php
                                if ($actionRide != 'Expired') {
                                    ?>
								
								 <th class="tip_top" >
                                     <?php if ($this->lang->line('admin_rides_track_ride') != '') echo stripslashes($this->lang->line('admin_rides_track_ride')); else echo 'Track Ride'; ?>
                                 </th>
								 <?php } ?>
								
                                    <th>
                                        <?php if ($this->lang->line('admin_subadmin_action') != '') echo stripslashes($this->lang->line('admin_subadmin_action')); else echo 'Action'; ?>
                                    </th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php
                            if ($ridesList->num_rows() > 0) {
                                $i = $offsetVal + 1;
                                foreach ($ridesList->result() as $row) {
                                    ?>
                                    <tr>
                                        <td class="center tr_select "><?php echo $i; ?></td>
                                        <td class="center"><?php if (isset($row->ride_id)) echo $row->ride_id; ?></td>
                                        <td class="center"><?php $bookDateSec = MongoEPOCH($row->booking_information['booking_date']); if (isset($row->booking_information['booking_date'])) echo get_time_to_string('Y-m-d h:i A', $bookDateSec); ?></td>
                                        <td class="center"><?php if ($isDemo) { ?><?php echo $dEmail; ?><?php } else { ?><?php if (isset($row->user['email'])) echo $row->user['email']; ?><?php } ?></td>
                                        <td class="center"><?php if (isset($row->ride_status)) echo get_language_value_for_keyword($row->ride_status,$this->data['langCode']); ?></td>

                                        <?php if ($actionRide == 'Completed') { ?>
                                            <td class="center"><?php if (isset($row->rider_review_status)) { if ($row->rider_review_status == 'Yes') { ?> <?php echo $row->ratings['rider']['avg_rating']; ?> <?php } else { echo get_language_value_for_keyword('Not rated yet',$this->data['langCode']); } } else { echo get_language_value_for_keyword('Not rated yet',$this->data['langCode']); } ?> </td>
                                            <td class="center"> <?php if (isset($row->driver_review_status)) { if ($row->driver_review_status == 'Yes') { ?> <?php echo $row->ratings['driver']['avg_rating']; ?> <?php } else {  echo get_language_value_for_keyword('Not rated yet',$this->data['langCode']); } } else {  echo get_language_value_for_keyword('Not rated yet',$this->data['langCode']); } ?></td>
                                        <?php } ?>

                                        <?php
                                        if ($actionRide=='' || $actionRide == 'Booked') {
                                            if ($row->ride_status == 'Booked') {
                                                ?>
										    <?php if ($allPrev == '1' || in_array('2', $rides)) { ?>
                                                <td class="center">
                                                    <ul class="action_list ">
                                                        <li style="width:100%;">
                                                            <a class="p_car tipTop" href="<?php echo OPERATOR_NAME;?>/trip/available_drivers_list/<?php echo $row->_id; ?>" title="<?php if ($this->lang->line('admin_rides_assign_cab') != '') echo   stripslashes($this->lang->line('admin_rides_assign_cab')); else echo 'Assign Cab'; ?>">
                                                                <?php if ($this->lang->line('admin_rides_assign_cab') != '') echo stripslashes($this->lang->line('admin_rides_assign_cab')); else echo 'Assign Cab'; ?>
                                                            </a>
                                                        </li>
                                                    </ul>
                                                </td>
                                                <?php
												}
                                            }
                                        }
                                        ?>
										
										<?php
                                        if ($actionRide != 'Expired') {
                                        ?>
										
										 <td class="center">
											<ul class="action_list">
												<li style="width:100%;">
													<a class="p_car tipTop" href="track?rideId=<?php echo $row->ride_id; ?>" title="<?php if ($this->lang->line('admin_ride_track_this_ride') != '') echo   stripslashes($this->lang->line('admin_ride_track_this_ride')); else echo 'Track this ride'; ?>" target="_blank">
														<?php if ($this->lang->line('admin_rides_track_ride') != '') echo stripslashes($this->lang->line('admin_rides_track_ride')); else echo 'Track Ride'; ?>
													</a>
												</li>
											</ul>
                                        </td>
                                        <?php } ?>

                                            <td class="center">
                                                <ul class="action_list">
                                                    <li style="width:100%;">
                                                        <a class="p_edit tipTop" href="<?php echo OPERATOR_NAME;?>/trip/view_trip/<?php echo $row->_id; ?>?act=<?php echo $this->input->get('act'); ?>" title="<?php if ($this->lang->line('admin_rides_view_details') != '') echo   stripslashes($this->lang->line('admin_rides_view_details')); else echo 'View Details'; ?>">
                                                            <?php if ($this->lang->line('admin_rides_view_details') != '') echo stripslashes($this->lang->line('admin_rides_view_details')); else echo 'View Details'; ?>
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
                                <th class="center">
                                    <?php if ($this->lang->line('admin_s_no') != '') echo stripslashes($this->lang->line('admin_s_no')); else echo 'S.No'; ?>
                                </th>
                                <th class="tip_top">
                                    <?php if ($this->lang->line('admin_rides_ride_id') != '') echo stripslashes($this->lang->line('admin_rides_ride_id')); else echo 'Ride Id'; ?>
                                </th>
                                <th class="tip_top">
                                    <?php if ($this->lang->line('admin_rides_booked_date') != '') echo stripslashes($this->lang->line('admin_rides_booked_date')); else echo 'Booked Date'; ?>
                                </th>
                                <th class="tip_top">
                                    <?php if ($this->lang->line('admin_rides_user') != '') echo stripslashes($this->lang->line('admin_rides_user')); else echo 'User'; ?>
                                </th>
                                <th class="tip_top">
                                    <?php if ($this->lang->line('admin_subadmin_status') != '') echo stripslashes($this->lang->line('admin_subadmin_status')); else echo 'Status'; ?>

                                </th>
                                <?php
                                $actionRide = $this->input->get('act');

                                if ($actionRide == 'Completed') {
                                    ?>
                                    <th class="tip_top">
                                        <?php if ($this->lang->line('admin_rides_rider_ratings') != '') echo stripslashes($this->lang->line('admin_rides_rider_ratings')); else echo 'Rider Ratings'; ?>
                                    </th>
                                    <th class="tip_top" >
                                        <?php if ($this->lang->line('admin_rides_driver_ratings') != '') echo stripslashes($this->lang->line('admin_rides_driver_ratings')); else echo 'Driver Ratings'; ?>
                                    </th>
                                    <?php
                                }
                                ?>

                                <?php
                                if ($actionRide=='' || $actionRide == 'Booked') {
                                    ?>
								 <?php if ($allPrev == '1' || in_array('2', $rides)) { ?>
                                    <th class="tip_top">
                                        <?php if ($this->lang->line('admin_rides_assign_cab') != '') echo stripslashes($this->lang->line('admin_rides_assign_cab')); else echo 'Assign Cab'; ?>
                                    </th>
								<?php
                                }
                                ?>
                                    <?php
                                }
                                ?>
								
								<?php
                                if ($actionRide != 'Expired') {
                                    ?>
								
								<th class="tip_top">
                                    <?php if ($this->lang->line('admin_rides_track_ride') != '') echo stripslashes($this->lang->line('admin_rides_track_ride')); else echo 'Track Ride'; ?>
                                </th>
								<?php } ?>
								
                                    <th>
                                        <?php if ($this->lang->line('admin_subadmin_action') != '') echo stripslashes($this->lang->line('admin_subadmin_action')); else echo 'Action'; ?>
                                    </th>
                            </tr>
                        </tfoot>
                    </table>
                    <?php
                    if ($paginationLink != '') {
                        echo $paginationLink;
                    }
                    ?>
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
 <script>
	$(function () {
		$("#billFromdate").datepicker({  maxDate: '<?php echo date('m/d/Y'); ?>' });
		$("#billFromdate").datepicker("option", "showAnim", "clip");  // drop,fold,slide,bounce,slideDown,blind
		
		$("#billTodate").datepicker({  maxDate: '<?php echo date('m/d/Y'); ?>' });
		$("#billTodate").datepicker("option", "showAnim", "clip");  // drop,fold,slide,bounce,slideDown,blind
	});
</script>

<style>
    .filter_widget input, .filter_widget select {
        border: 1px solid #d8d8d8;
        font-family: "OpenSansRegular";
        font-size: 12px;
        padding: 5px 2px;
        width: 160px;
    }
    
                
    #date_range {	
        width:150px !important;
    }

    #locationFilter {
        font-size: 14px;
        height: 33px;
        width: 185px;
    }
    
    #export_all_rides {
        height: auto !important;
        font-size: 13px !important;
    }
    
</style>

<?php 
$this->load->view(OPERATOR_NAME.'/templates/footer.php');
?>