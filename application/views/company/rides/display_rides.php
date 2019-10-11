<?php
$this->load->view(COMPANY_NAME.'/templates/header.php');
$minDate=strtotime('2015-01-01');
#print_r($rides);
?>
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
	
	$(function (){
		$("#rideLocation , #rideFromdate , #rideTodate").change(function(){
			$("#export_rides").text('<?php if ($this->lang->line('admin_rides_filtered_rides') != '') echo stripslashes($this->lang->line('admin_rides_filtered_rides')); else echo 'Export Filtered Rides'; ?>');
		});
		$("#export_rides").click(function(event){
			event.preventDefault();
			get_field_values();
			window.location.href = "<?php echo base_url(); ?><?php echo COMPANY_NAME; ?>/rides/display_report_rides?action=<?php if(isset($_GET['act']) && $_GET['act'] != ''){ echo $_GET['act'];} ?>&from="+$filter_from+'&to='+$filter_to+'&location='+$rideLocation;
		});
		
	});
	function submit_ride_filter(){
		get_field_values();
		window.location.href = "<?php echo base_url(); ?><?php echo COMPANY_NAME; ?>/rides/display_rides?act=<?php if(isset($_GET['act']) && $_GET['act'] != ''){ echo $_GET['act'];} ?>&from="+$filter_from+'&to='+$filter_to+'&location='+$rideLocation;
	}
	function get_field_values(){
		$filter_from = btoa($("#rideFromdate").val());
		$filter_to = btoa($("#rideTodate").val());
		$rideLocation = $("#rideLocation").val();
	}
</script>
<div id="content" class="rides_disply_company">
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
									<select class="chzn-select" name='location' id='rideLocation'>
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
									</select>
									<input name="date_range" id="rideFromdate" type="text"  class="tipTop monthYearPicker" title="<?php if ($this->lang->line('admin_ride_pls_starting_ride') != '') echo stripslashes($this->lang->line('admin_ride_pls_starting_ride')); else echo 'Please select the Starting Date'; ?>" readonly="readonly" value="<?php if(isset($_GET['from']))echo base64_decode($_GET['from']); ?>" placeholder="<?php if ($this->lang->line('admin_ride_starting_ride') != '') echo stripslashes($this->lang->line('admin_ride_starting_ride')); else echo 'Starting Date'; ?>"/>
														
									<input name="dateto" id="rideTodate" type="text"  class="tipTop monthYearPicker" title="<?php if ($this->lang->line('admin_ride_pls_ending_ride') != '') echo stripslashes($this->lang->line('admin_ride_pls_ending_ride')); else echo 'Please select the Ending Date'; ?>" readonly="readonly" value="<?php if(isset($_GET['to']))echo base64_decode($_GET['to']); ?>"  placeholder="<?php if ($this->lang->line('admin_ride_ending_ride') != '') echo stripslashes($this->lang->line('admin_ride_ending_ride')); else echo 'Ending Date'; ?>"/>
									
									<a href="javascript:void(0)" class="filter_btn_company"onclick="submit_ride_filter()" style="margin: 0 5px -8px 7px;height: 26px;">
										<span class="icon search" style="margin-top: 8px;"></span>
										<span class="btn_link" style="line-height: 26px;height: 27px;"><?php if ($this->lang->line('admin_rides_filter_rides') != '') echo stripslashes($this->lang->line('admin_rides_filter_rides')); else echo 'Filter Rides'; ?></span>
									</a>
									<?php if(isset($filter) && $filter!=""){ ?>
									<a href="<?php echo current_url(); ?>?act=<?php if(isset($_GET['act']) && $_GET['act'] != ''){ echo $_GET['act'];} ?>"class="tipTop" original-title="<?php if ($this->lang->line('admin_notification_remove_filter') != '') echo stripslashes($this->lang->line('admin_notification_remove_filter')); else echo 'Remove Filter'; ?>" style="margin: 0 5px -8px 7px;height: 26px;">
										<span class="icon delete_co" style="margin-top: 8px;"></span>
									</a>
									<?php } ?>
								</div>
							</div>
						</div>
					</div>
				</div>
			</div>
		</div>
        <?php
        $attributes = array('id' => 'display_form');
        echo form_open(COMPANY_NAME.'/rides/change_rides_status_global', $attributes)
        ?>
        <div class="grid_12">
            <div class="widget_wrap">
                <div class="widget_top">
                    <span class="h_icon blocks_images"></span>
                    <h6><?php echo $heading ?></h6>
					
                </div>
                <div class="widget_content">
                    <?php
					$tble='';
					$actionRide = $this->input->get('act');
					if($actionRide=='Booked') {
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
					
					} else if($actionRide=='Cancelled'){
						if ($paginationLink != '') {
                           echo $paginationLink;
                        $tble = 'allcancelled_tbl';
						} else {
							$tble = 'cancelled_tbl';
						}
					} else if($actionRide=='total'){
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
                                <th class="tip_top" title="<?php if ($this->lang->line('dash_click_sort') != '') echo   stripslashes($this->lang->line('dash_click_sort')); else echo 'Click to sort'; ?>">
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
                        </thead>
                        <tbody>
                            <?php
                            if ($ridesList->num_rows() > 0) {
                                $i = $offsetVal + 1;
                                foreach ($ridesList->result() as $row) {
                                    ?>
                                    <tr>
                                        <td class="center tr_select ">
                                            <?php echo $i; ?>
                                        </td>
                                        <td class="center">
                                            <?php if (isset($row->ride_id)) echo $row->ride_id; ?>
                                        </td>
                                        <td class="center">
                                            <?php
                                            $bookDateSec = MongoEPOCH($row->booking_information['booking_date']);

                                            if (isset($row->booking_information['booking_date']))
                                                echo get_time_to_string('Y-m-d h:i A', $bookDateSec);
                                            ?>
                                        </td>
                                        <td class="center">
                                            <?php if ($isDemo) { ?>
                                                <?php echo $dEmail; ?>
                                            <?php } else { ?>
                                                <?php if (isset($row->user['email'])) echo $row->user['email']; ?>
                                            <?php } ?>
                                        </td>
                                        <td class="center">
                                            <?php if (isset($row->ride_status)) echo get_language_value_for_keyword($row->ride_status,$this->data['langCode']); ?>
                                        </td>

                                        <?php if ($actionRide == 'Completed') { ?>
                                            <td class="center">
                                                <?php
                                                if (isset($row->rider_review_status)) {
                                                    if ($row->rider_review_status == 'Yes') {
                                                        ?>
                                                        <?php echo $row->ratings['rider']['avg_rating']; ?>
                                                        <?php
                                                    } else {
                                                        echo get_language_value_for_keyword('Not rated yet',$this->data['langCode']);
                                                    }
                                                } else {
                                                    echo get_language_value_for_keyword('Not rated yet',$this->data['langCode']);
                                                }
                                                ?>
                                            </td>

                                            <td class="center">
                                                <?php
                                                if (isset($row->driver_review_status)) {
                                                    if ($row->driver_review_status == 'Yes') {
                                                        ?>
                                                        <?php echo $row->ratings['driver']['avg_rating']; ?>
                                                        <?php
                                                    } else {
                                                        echo get_language_value_for_keyword('Not rated yet',$this->data['langCode']);
                                                    }
                                                } else {
                                                    echo get_language_value_for_keyword('Not rated yet',$this->data['langCode']);
                                                }
                                                ?>
                                            </td>


                                        <?php } ?>

                            
										
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
                                                        <a class="p_edit tipTop" href="<?php echo COMPANY_NAME; ?>/rides/view_ride_details/<?php echo $row->_id; ?>?act=<?php echo $this->input->get('act'); ?>" title="<?php if ($this->lang->line('admin_rides_view_details') != '') echo   stripslashes($this->lang->line('admin_rides_view_details')); else echo 'View Details'; ?>">
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
                                <th>
                                    <?php if ($this->lang->line('admin_s_no') != '') echo stripslashes($this->lang->line('admin_s_no')); else echo 'S.No'; ?>
                                </th>
                                <th>
                                    <?php if ($this->lang->line('admin_rides_ride_id') != '') echo stripslashes($this->lang->line('admin_rides_ride_id')); else echo 'Ride Id'; ?>
                                </th>
                                <th>
                                    <?php if ($this->lang->line('admin_rides_booked_date') != '') echo stripslashes($this->lang->line('admin_rides_booked_date')); else echo 'Booked Date'; ?>
                                </th>
                                <th>
                                    <?php if ($this->lang->line('admin_rides_user') != '') echo stripslashes($this->lang->line('admin_rides_user')); else echo 'User'; ?>
                                </th>
                                <th>
                                    <?php if ($this->lang->line('admin_subadmin_status') != '') echo stripslashes($this->lang->line('admin_subadmin_status')); else echo 'Status'; ?>

                                </th>
                                <?php
                                $actionRide = $this->input->get('act');

                                if ($actionRide == 'Completed') {
                                    ?>
                                    <th>
                                        <?php if ($this->lang->line('admin_rides_rider_ratings') != '') echo stripslashes($this->lang->line('admin_rides_rider_ratings')); else echo 'Rider Ratings'; ?>
                                    </th>
                                    <th>
                                        <?php if ($this->lang->line('admin_rides_driver_ratings') != '') echo stripslashes($this->lang->line('admin_rides_driver_ratings')); else echo 'Driver Ratings'; ?>
                                    </th>
                                    <?php
                                }
                                ?>

                                
								
								<?php
                                if ($actionRide != 'Expired') {
                                    ?>
								
								<th>
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
			
</style>

<?php
$this->load->view(COMPANY_NAME.'/templates/footer.php');
?>