<?php
$this->load->view(ADMIN_ENC_URL.'/templates/header.php'); 
extract($privileges);
$minDate=date('Y-m-d',strtotime('-365 days'));
//$this->load->helper('common_helper');
?>
<?php
 $dialcode=array();
 foreach ($countryList as $country) {
     if ($country->dial_code != '') {
      $dialcode[]=str_replace(' ', '', $country->dial_code); 
     }
 }
 
   asort($dialcode);
   $dialcode=array_unique($dialcode);
   
   $driver_mode = $this->input->get('dmode');
    if($driver_mode == '') $driver_mode = 'all';

    $driver_avail = $this->input->get('davail');
    if($driver_avail == '') $driver_avail = 'all';

    $dverify = $this->input->get('dverify');
    if($dverify == '') $dverify = 'all';

    $dstatus = $this->input->get('dstatus');
    if($dstatus == '') $dstatus = 'all';
    
    $datefrom = '';
    $dateto = '';
    if($this->input->get('datefrom') != '') $datefrom = $this->input->get('datefrom');
    if($this->input->get('dateto') != '') $dateto = $this->input->get('dateto');
    
?>

<script>
$(document).ready(function(){
    $("#export_report").click(function(event){  
		event.preventDefault();
		var query_strings = "<?php echo $_SERVER["QUERY_STRING"]; ?>";
		window.location.href = "<?php echo ADMIN_ENC_URL;?>/drivers/display_drivers_list?" + query_strings + "&export=excel";
	});
    
    $("#export_report_all").click(function(event){
		event.preventDefault();
		var query_strings = "<?php echo $_SERVER["QUERY_STRING"]; ?>";
		window.location.href = "<?php echo ADMIN_ENC_URL;?>/drivers/display_drivers_list?" + query_strings + "&export=all";
	});

   $('.vehicle_category').css("display","none");
   $(".vehicle_category").attr("disabled", true);
   $('#country').css("display","none");
   $("#country").attr("disabled", true);
   $vehicle_category='';
   $country='';
	 $locations_id = '';
   <?php  if(isset($_GET['vehicle_category'])) {?>
	$vehicle_category = "<?php echo $_GET['vehicle_category']; ?>";
    <?php }?>
    <?php  if(isset($_GET['country'])) {?>
	$country = "<?php echo $_GET['country']; ?>";
    <?php }?>
		<?php  if(isset($type) && $type == 'driver_location' &&  isset($_GET['locations_id'])) {?>
	$locations_id = "<?php echo $_GET['locations_id']; ?>";
    <?php }?>
	if($vehicle_category != ''){
		$('.vehicle_category').css("display","inline");
		$('#filtervalue').css("display","none");
		$(".vehicle_category").attr("disabled", false);
        $("#country").attr("disabled", true);
	}
    if($country != ''){
		$('#country').css("display","inline");
		$("#country").attr("disabled", false);
        $('.vehicle_category').attr("disabled", true);
		
	}
	if($locations_id != ''){
		$('#country').css("display","none");
		$('.vehicle_category').attr("disabled", true);
		$('#filtervalue').css("display","none");
		$('#locations_id').css("display","inline");
	}
	$("#filtertype").change(function(){
		$filter_val = $(this).val();
        $('#filtervalue').val('');
		$('.vehicle_category').css("display","none");
		$('#filtervalue').css("display","inline");
        $('#country').css("display","none");
        $("#country").attr("disabled", true);
        $(".vehicle_category").attr("disabled", true);
				$('#locations_id').css('display','none');
		if($filter_val == 'vehicle_type'){
			$('.vehicle_category').css("display","inline");
			$('#filtervalue').css("display","none");
            $('#country').css("display","none");
            $('.vehicle_category').prop("disabled", false);
            $("#country").attr("disabled", true);
		}
        if($filter_val == 'mobile_number'){
			$('#country').css("display","inline");
			$('#country').prop("disabled", false);
            $(".vehicle_category").attr("disabled", true);
            $('.vehicle_category').css("display","none");
		}
		if($filter_val == 'driver_location'){ 
			$('#country').css("display","none");
			$('#country').prop("disabled", false);
            $(".vehicle_category").attr("disabled", true);
            $('.vehicle_category').css("display","none");
			$('#filtervalue').css("display","none");
			$('#locations_id').css('display','inline');
		}
	});
	
});
</script>
<div id="content" class="first-disply-list">
    <div class="grid_container">
	
		<div class="grid_12">
			<div class="">
					<div class="widget_content">
						<span class="clear"></span>						
						<div class="">
							<div class=" filter_wrap">
								<div class="widget_top filter_widget display_filter_wp">
									
									<?php
									$attributes = array('class' => 'form_container left_label', 'id' => 'filter_form','method'=>'GET','autocomplete'=>'off','enctype' => 'multipart/form-data','style' => 'width: 100% !important;');
									echo form_open(ADMIN_ENC_URL.'/drivers/display_drivers_list', $attributes)
									?>
                                    <div class="btn_30_light">
                                        <div class="filterwrap">
                                            <span class="title">Sortby</span>
                                            <select class="form-control" id="sortby" name="sortby"  style="width:150px;">
                                                <option value="" data-val=""><?php if ($this->lang->line('admin_driver_select_sort_type') != '') echo stripslashes($this->lang->line('admin_driver_select_sort_type')); else echo 'Select Sort Type'; ?></option></option>
                                                <option value="doj_asc" <?php if(isset($sortby)){if($sortby=='doj_asc'){ echo 'selected="selected"'; } }?>><?php if ($this->lang->line('admin_user_by_join_date') != '') echo stripslashes($this->lang->line('admin_user_by_join_date')); else echo 'By Joining Date'; ?></option>
                                                <option value="doj_desc" <?php if(isset($sortby)){if($sortby=='doj_desc'){ echo 'selected="selected"'; } }?>><?php if ($this->lang->line('admin_user_by_recently_joined') != '') echo stripslashes($this->lang->line('admin_user_by_recently_joined')); else echo 'By Recently Joined'; ?></option>
                                                <option value="rides_asc" <?php if(isset($sortby)){if($sortby=='rides_asc'){ echo 'selected="selected"'; } }?>><?php if ($this->lang->line('admin_user_by_least_rides') != '') echo stripslashes($this->lang->line('admin_user_by_least_rides')); else echo 'By Least Rides'; ?></option>
                                                <option value="rides_desc" <?php if(isset($sortby)){if($sortby=='rides_desc'){ echo 'selected="selected"'; } }?>><?php if ($this->lang->line('admin_user_by_maximum_rides') != '') echo stripslashes($this->lang->line('admin_user_by_maximum_rides')); else echo 'By Maximum Rides'; ?></option>
                                            </select>
                                        </div>
                                        
                                        <div class="filterwrap"> 
											<span class="title">Availablity</span>
											<select class="form-control element" id="davail" name="davail">
												<option value="on" <?php if($driver_avail=='on'){ echo 'selected="selected"'; } ?>><?php if ($this->lang->line('admin_drivers_online') != '') echo stripslashes($this->lang->line('admin_drivers_online')); else echo 'Online'; ?></option>
												<option value="off" <?php if($driver_avail=='off'){ echo 'selected="selected"'; } ?>><?php if ($this->lang->line('admin_drivers_offline') != '') echo stripslashes($this->lang->line('admin_drivers_offline')); else echo 'Offline'; ?></option>
												<option value="all" <?php if($driver_avail=='all'){ echo 'selected="selected"'; } ?>><?php if ($this->lang->line('admin_drivers_all') != '') echo stripslashes($this->lang->line('admin_drivers_all')); else echo 'All'; ?></option>
											</select>
										</div>
                                        
                                         <div class="filterwrap">
											<span class="title">Mode</span>
											<select class="form-control" id="dmode" name="dmode">
												<option value="available" <?php if($driver_mode=='available'){ echo 'selected="selected"'; } ?>>Waiting for Trip</option>
												<option value="booked" <?php if($driver_mode=='booked'){ echo 'selected="selected"'; } ?>><?php if ($this->lang->line('admin_drivers_onride') != '') echo stripslashes($this->lang->line('admin_drivers_onride')); else echo 'On Ride'; ?></option>
												<option value="all" <?php if($driver_mode=='all'){ echo 'selected="selected"'; } ?>><?php if ($this->lang->line('admin_drivers_all') != '') echo stripslashes($this->lang->line('admin_drivers_all')); else echo 'All'; ?></option>
											</select>
										</div>
                                        
                                        <div class="filterwrap">
											<span class="title">Verified Status</span>
											<select class="form-control" id="dverify" name="dverify" >
												<option value="verified" <?php if($dverify=='verified'){ echo 'selected="selected"'; } ?>><?php if ($this->lang->line('admin_drivers_verified') != '') echo stripslashes($this->lang->line('admin_drivers_verified')); else echo 'Verified'; ?></option>
												<option value="unverified" <?php if($dverify=='unverified'){ echo 'selected="selected"'; } ?>><?php if ($this->lang->line('admin_drivers_unverified') != '') echo stripslashes($this->lang->line('admin_drivers_unverified')); else echo 'Un Verified'; ?></option>
												<option value="all" <?php if($dverify=='all'){ echo 'selected="selected"'; } ?>><?php if ($this->lang->line('admin_drivers_all') != '') echo stripslashes($this->lang->line('admin_drivers_all')); else echo 'All'; ?></option>
											</select>
										</div>
                                        
                                        <div class="filterwrap">
											<span class="title">Status</span>
											<select class="form-control" id="dstatus" name="dstatus" >
												<option value="active" <?php if($dstatus=='active'){ echo 'selected="selected"'; } ?>><?php if ($this->lang->line('admin_subadmin_active') != '') echo stripslashes($this->lang->line('admin_subadmin_active')); else echo 'Active'; ?></option>
												<option value="inactive" <?php if($dstatus=='inactive'){ echo 'selected="selected"'; } ?>><?php if ($this->lang->line('admin_subadmin_inactive') != '') echo stripslashes($this->lang->line('admin_subadmin_inactive')); else echo 'Inactive'; ?></option>
												<option value="all" <?php if($dstatus=='all'){ echo 'selected="selected"'; } ?>><?php if ($this->lang->line('admin_drivers_all') != '') echo stripslashes($this->lang->line('admin_drivers_all')); else echo 'All'; ?></option>
											</select>
										</div>
                                    </div>
                                        
                                    <div class="btn_30_light second-row">
                                        <div class="filterwrap filter-type-sec" >
                                            <span class="title">Filter Type</span>    
                                            <select class="form-control" id="filtertype" name="type">
                                                <option value="" data-val="">
                                                <?php if ($this->lang->line('admin_drivers_select_filter_type') != '') echo stripslashes($this->lang->line('admin_drivers_select_filter_type')); else echo 'Select Filter Type'; ?></option>
                                                <option value="driver_name" data-val="driver_name" <?php if(isset($type)){if($type=='driver_name'){ echo 'selected="selected"'; } }?>>
                                                <?php if ($this->lang->line('admin_drivers_driver_name') != '') echo stripslashes($this->lang->line('admin_drivers_driver_name')); else echo 'Driver Name'; ?></option>
                                                <option value="email" data-val="email" <?php if(isset($type)){if($type=='email'){ echo 'selected="selected"'; } }?>><?php if ($this->lang->line('admin_drivers_change_password_driver_email') != '') echo stripslashes($this->lang->line('admin_drivers_change_password_driver_email')); else echo 'Driver Email'; ?></option>
                                                <option value="mobile_number" data-val="mobile_number" <?php if(isset($type)){if($type=='mobile_number'){ echo 'selected="selected"'; } }?>>
                                                <?php if ($this->lang->line('admin_drivers_phone_number') != '') echo stripslashes($this->lang->line('admin_drivers_phone_number')); else echo 'Driver PhoneNumber'; ?></option>
                                                <option value="driver_location" data-val="location" <?php if(isset($type)){if($type=='driver_location'){ echo 'selected="selected"'; } }?>><?php if ($this->lang->line('admin_location_and_fare_location_location') != '') echo stripslashes($this->lang->line('admin_location_and_fare_location_location')); else echo 'Location'; ?></option>
                                                <option value="vehicle_type" data-val="vehicle_type" <?php if(isset($type)){if($type=='vehicle_type'){ echo 'selected="selected"'; } }?>><?php if ($this->lang->line('admin_drivers_vehicle_type') != '') echo stripslashes($this->lang->line('admin_drivers_vehicle_type')); else echo 'Vehicle Type'; ?></option>
                                            </select>
                                        </div>
                                        
                                        <div class="filterwrap filter-loc-section">
                                            <select name="country" id="country"  class=" form-control" title="<?php if ($this->lang->line('please_enter_country') != '') echo stripslashes($this->lang->line('please_enter_country')); else echo 'Please choose your country';?>" style="display:none;">
                                                <?php 
                                                $country = '';
                                                    if(isset($_GET['country']) && $_GET['country']!=''){
                                                        $country = $_GET['country'];
                                                    }
                                                     
                                                foreach ($dialcode as $row) {
                                                   // if($country != '' && $country == $row){
                                                    if(empty($country)){
                                                    if($d_country_code==$row){
                                                            echo "<option selected value=".$row.">".$row."</option>";
                                                        }else{
                                                            echo "<option value=".$row.">".$row."</option>";
                                                        }
                                                    }
                                                    if(!empty($country)){
                                                    if($country==$row){
                                                            echo "<option selected value=".$row.">".$row."</option>";
                                                        }else{
                                                            echo "<option value=".$row.">".$row."</option>";
                                                        }
                                                    }
                                                } ?>
                                            </select>
                                                <select name="locations_id" class='locationsList' id="locations_id" style="display:none; width:320px !important;">
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
                                                            if($veh_cat != '' && $veh_cat == $cat->name){
                                                                echo "<option selected value=".$cat->name.">".$cat->name."</option>";
                                                            }else{
                                                                echo "<option value=".$cat->name.">".$cat->name."</option>";
                                                            }
                                                            
                                                        }
                                                    ?>
                                            </select>
                                        </div>
                                        
                                        <div class="filterwrap date_betweens" style="width:40% !important;">
											<span class="title">Date Between</span>
											<input name="datefrom" id="datefrom"  class="element" type="text" tabindex="1" class="tipTop monthYearPicker" title="Please select the Starting Date" readonly="readonly" value="<?php echo $datefrom; ?>" placeholder="Starting Date" />
											<input name="dateto" id="dateto" type="text" tabindex="2" class="tipTop monthYearPicker" title="Please select the Ending Date" readonly="readonly" value="<?php echo $dateto; ?>"  placeholder="Ending Date"  /> 
										</div>
								
                                        <div class="filterwrap f-submint">
                                            <button type="submit" class="tipTop filterbtn"  original-title="<?php if ($this->lang->line('driver_enter_keyword_filter') != '') echo stripslashes($this->lang->line('driver_enter_keyword_filter')); else echo 'Select filter type and enter keyword to filter'; ?>">
                                                <span class="icon search"></span><span class="btn_link"><?php if ($this->lang->line('admin_drivers_filter') != '') echo stripslashes($this->lang->line('admin_drivers_filter')); else echo 'Filter'; ?></span>
                                            </button>
                                            
                                            <?php if(isset($filter) && $filter!=""){ ?>
                                            <a href="<?php echo ADMIN_ENC_URL;?>/drivers/display_drivers_list"class="tipTop filterbtn filter_admin" original-title="<?php if ($this->lang->line('admin_notification_remove_filter') != '') echo stripslashes($this->lang->line('admin_notification_remove_filter')); else echo 'Remove Filter'; ?>">
                                                <span class="icon delete_co"></span>
                                            </a>
                                            <?php } ?>
                                        </div>
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
        echo form_open(ADMIN_ENC_URL.'/drivers/change_driver_status_global', $attributes)
        ?>
        <div class="grid_12">
            <div class="widget_wrap">
                <div class="widget_top">
                    <span class="h_icon blocks_images"></span>
                    <h6><?php echo $heading ?></h6>
                    <div style="float: right;line-height:40px;padding:0px 10px;height:39px;">
                        <?php if ($allPrev == '1' || in_array('2', $driver)) { ?>
                            <div class="btn_30_light" style="height: 29px;">
                                <a href="javascript:void(0)" onclick="return checkBoxValidationAdmin('Active', '<?php echo $subAdminMail; ?>');" class="tipTop" title="<?php if ($this->lang->line('driver_select_active_records') != '') echo stripslashes($this->lang->line('driver_select_active_records')); else echo 'Select any checkbox and click here to active records'; ?>">
                                    <!-- <span class="icon accept_co"></span> -->
                                    <span class="btn_link act-btn"><?php if ($this->lang->line('admin_subadmin_active') != '') echo stripslashes($this->lang->line('admin_subadmin_active')); else echo 'Active'; ?></span>
                                </a>
                            </div>
                            <div class="btn_30_light" style="height: 29px;">
                                <a href="javascript:void(0)" onclick="return checkBoxValidationAdmin('Inactive', '<?php echo $subAdminMail; ?>');" class="tipTop" title="<?php if ($this->lang->line('driver_select_inactive_records') != '') echo stripslashes($this->lang->line('driver_select_inactive_records')); else echo 'Select any checkbox and click here to inactive records'; ?>">
                                    <!-- <span class="icon delete_co"></span> -->
                                    <span class="btn_link inact-btn"><?php if ($this->lang->line('admin_subadmin_inactive') != '') echo stripslashes($this->lang->line('admin_subadmin_inactive')); else echo 'Inactive'; ?></span>
                                </a>
                            </div>
                            <?php
                        }
                        if ($allPrev == '1' || in_array('3', $user)) {
                            ?>
                            <div class="btn_30_light" style="height: 29px;">
                                <a href="javascript:void(0)" onclick="return checkBoxValidationAdmin('Delete', '<?php echo $subAdminMail; ?>');" class="tipTop" title="<?php if ($this->lang->line('driver_select_delete_records') != '') echo stripslashes($this->lang->line('driver_select_delete_records')); else echo 'Select any checkbox and click here to delete records'; ?>">
                                   <!-- <span class="icon cross_co del-btn"></span>-->
                                   <span class="btn_link"><?php if ($this->lang->line('admin_subadmin_delete') != '') echo stripslashes($this->lang->line('admin_subadmin_delete')); else echo 'Delete'; ?></span>
                                </a>
                            </div>
                        <?php } ?>
                        
                        <?php if ($allPrev == '1') { ?>
						<?php if($driversList->num_rows() > 0){ ?>
                            <div class="btn_30_light" style="height: 29px;">
                                <a style="color:#fff;height: 25px !important;font-size: 12px !important;" class="p_edit tipTop export_report" id="export_report_all"><?php if ($this->lang->line('admin_export') != '') echo stripslashes($this->lang->line('admin_export')); else echo 'Export'; ?> All</a>
                            </div>
                            
                            <div class="btn_30_light" style="height: 29px;">
                                <a style="color:#fff;height: 25px !important;font-size: 12px !important;" class="p_edit tipTop export_report" id="export_report"><?php if ($this->lang->line('admin_export') != '') echo stripslashes($this->lang->line('admin_export')); else echo 'Export'; ?></a>
						<?php } ?>
                            </div>
						<?php } ?>
                    </div>
                </div>
                <div class="widget_content">
                    <?php
                    if ($paginationLink != '') {
                        echo $paginationLink;
                        $tble = 'alldriverListTbl';
                    } else {
                        $tble = 'driverListTbl';
                    }
                    ?>

                    <table class="display" id="<?php echo $tble; ?>" width='100%' style="overflow:scrolll">
                        <thead>
                            <tr>
                                <th class="center">
                                    <input name="checkbox_id[]" type="checkbox" value="on" class="checkall">
                                </th>
                                <th class="tip_top" title="<?php if ($this->lang->line('dash_click_sort') != '') echo stripslashes($this->lang->line('dash_click_sort')); else echo 'Click to sort'; ?>">
                                    <?php if ($this->lang->line('admin_car_types_name') != '') echo stripslashes($this->lang->line('admin_car_types_name')); else echo 'Name'; ?>
                                </th>
                                <th>
                                     <?php if ($this->lang->line('admin_driver_list_ride_stats') != '') echo stripslashes($this->lang->line('admin_driver_list_ride_stats')); else echo 'Ride Stats'; ?>
                                </th>
                                <th class="tip_top" title="<?php if ($this->lang->line('dash_click_sort') != '') echo stripslashes($this->lang->line('dash_click_sort')); else echo 'Click to sort'; ?>">
                                     <?php if ($this->lang->line('admin_driver_list_avg_count') != '') echo stripslashes($this->lang->line('admin_driver_list_avg_count')); else echo 'Avg(Count)'; ?>
                                </th>
                                <th class="tip_top" title="<?php if ($this->lang->line('dash_click_sort') != '') echo stripslashes($this->lang->line('dash_click_sort')); else echo 'Click to sort'; ?>">
                                     <?php if ($this->lang->line('admin_driver_list_doj') != '') echo stripslashes($this->lang->line('admin_driver_list_doj')); else echo 'DOJ'; ?>
                                </th>
                                <th>
                                    <?php if ($this->lang->line('admin_driver_list_avail') != '') echo stripslashes($this->lang->line('admin_driver_list_avail')); else echo 'AVAIL'; ?>
                                </th>
                                <th class="tip_top" title="<?php if ($this->lang->line('dash_click_sort') != '') echo stripslashes($this->lang->line('dash_click_sort')); else echo 'Click to sort'; ?>">
                                    <?php if ($this->lang->line('admin_drivers_mode') != '') echo stripslashes($this->lang->line('admin_drivers_mode')); else echo 'Mode'; ?>
                                </th>
                                <th class="tip_top" title="<?php if ($this->lang->line('dash_click_sort') != '') echo stripslashes($this->lang->line('dash_click_sort')); else echo 'Click to sort'; ?>">
                                    <?php if ($this->lang->line('admin_drivers_verified') != '') echo stripslashes($this->lang->line('admin_drivers_verified')); else echo 'Verified'; ?>
                                </th>
                                <th class="tip_top" title="<?php if ($this->lang->line('dash_click_sort') != '') echo stripslashes($this->lang->line('dash_click_sort')); else echo 'Click to sort'; ?>">
                                    <?php if ($this->lang->line('admin_subadmin_status') != '') echo stripslashes($this->lang->line('admin_subadmin_status')); else echo 'Status'; ?>
                                </th>
                                <th>
                                    <?php if ($this->lang->line('admin_subadmin_action') != '') echo stripslashes($this->lang->line('admin_subadmin_action')); else echo 'Action'; ?>
                                </th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php
                            if ($driversList->num_rows() > 0) {
                                foreach ($driversList->result() as $row) {
                                    ?>
                                    <tr style="border-bottom: 1px solid #dddddd !important;">
                                        <td class="center tr_select ">
                                            <input name="checkbox_id[]" type="checkbox" value="<?php echo $row->_id; ?>">
                                        </td>
                                        <td class="center">
                                            <?php echo $row->driver_name; ?>
											<?php 
												if ($this->lang->line('not_available') != '') 
													$cTy=stripslashes($this->lang->line('not_available'));
												else
													$cTy='N/A';
												
												if(isset($row->category)){
													$catsId = (string)$row->category; 
													if(array_key_exists($catsId,$cabCats)){
														$cTy = $cabCats[$catsId]->name;
													}
												}
												echo '<br/><br/><span style="color:gray;">'.$cTy.'</span>'; 
											?>
                                        </td>

                                        <td class="center">
                                            <?php
                                            if (isset($row->req_received)) {
                                                $req_received = $row->req_received;
                                            } else {
                                                $req_received = '0';
                                            }
                                            if (isset($row->no_of_rides)) {
                                                $no_of_rides = $row->no_of_rides;
                                            }else {
                                                $no_of_rides = '0';
                                            }
                                            if (isset($row->cancelled_rides)) {
                                                $cancelled_rides = $row->cancelled_rides;
                                            } else {
                                                $cancelled_rides = '0';
                                            }
                                            ?>
											<?php if ($this->lang->line('admin_driver_list_req_rcvd') != '') echo stripslashes($this->lang->line('admin_driver_list_req_rcvd')); else echo 'Req Rcvd'; ?> : <?php echo $req_received."<br/><br/>"; ?>
											<?php if ($this->lang->line('admin_driver_list_cmpl') != '') echo stripslashes($this->lang->line('admin_driver_list_cmpl')); else echo 'CMPL'; ?> : <?php echo $no_of_rides."<br/><br/>"; ?>
											<?php if ($this->lang->line('admin_driver_list_cxld') != '') echo stripslashes($this->lang->line('admin_driver_list_cxld')); else echo 'CXLD'; ?> : <?php echo $cancelled_rides; ?>
                                        </td>

                                        <td class="center" style="20px;">
                                            <?php if (isset($row->avg_review)) { ?>
                                                <?php if ($row->avg_review != '') { ?>
                                                    <a href="<?php echo ADMIN_ENC_URL;?>/reviews/view_driver_reviews/<?php echo $row->_id; ?>" style="color:blue;" ><?php echo number_format($row->avg_review, 2) . ' (' . $row->total_review . ')'; ?></a>
                                                <?php } else { ?>
                                                    0 (0)
                                                    <?php
                                                }
                                            } else {
                                                ?>
                                                0 (0)
                                            <?php } ?>
                                        </td>

                                        <td class="center">
                                            <?php
                                            if (isset($row->created)) {
                                                echo get_time_to_string('Y-m-d', strtotime($row->created));
                                            }
                                            ?>
                                        </td>


                                        <td class="center">
                                            <?php
                                          
                                            $current=time()-300;
                                            if(isset($driver['last_active_time'])) {
                                                $last_active_time=MongoEPOCH($row->last_active_time);
                                            }
                                            if (isset($row->availability)) {
                                                if ($row->availability == 'Yes' && isset($row->last_active_time) && $last_active_time > $current) {
                                                    ?>
                                                    <img src="images/status-online.png" />
													<span style="display:none;">1</span>
                                                    <?php
                                                } else {
                                                    ?>
                                                    <img src="images/status-offline.png" />
													<span style="display:none;">0</span>
                                                    <?php
                                                }
                                            } else {
                                                ?>
                                                <img src="images/status-offline.png" />
												<span style="display:none;">0</span>
                                                <?php
                                            }
                                            ?>
                                        </td>
                                        <td class="center">
												<?php
												$disp_mode = get_language_value_for_keyword($row->mode,$this->data['langCode']);
											if (isset($row->mode)) {
													if($row->mode == 'Booked'){
													$mode = '0';
												?>
												   <a title="<?php if ($this->lang->line('common_click_to_make_available') != '') echo stripslashes($this->lang->line('common_click_to_make_available')); else echo 'Click to make available'; ?>" class="tip_top" href="javascript:confirm_status('<?php echo ADMIN_ENC_URL;?>/drivers/change_driver_mode_status/<?php echo $mode; ?>/<?php echo $row->_id; ?>');">
                                                        <span class="badge_style b_done b_warn"><?php echo $disp_mode; ?></span>
                                                    </a>
												<?php
												} else  {
													echo $disp_mode;
												}
											} 
                                            ?>
                                        </td>
									
                                        <td class="center">
                                            <?php
											
											if(isset($row->verify_status)){
												if($row->verify_status==''){
													$verify_status = get_language_value_for_keyword('No',$this->data['langCode']);;
												}else{
													$verify_status = get_language_value_for_keyword($row->verify_status,$this->data['langCode']);;
												}
											}else{
												$verify_status = get_language_value_for_keyword('No',$this->data['langCode']);;
											}
                                            if ($allPrev == '1' || in_array('2', $driver)) {
                                                if (isset($row->verify_status) && $row->verify_status == 'Yes') {
                                                    $mode = 0;
													$verify_status = get_language_value_for_keyword($row->verify_status,$this->data['langCode']);;
                                                } else {
                                                    $mode = 1;
                                                }
                                                if ($mode == '0') {
                                                    ?>
                                                    <a title="<?php if ($this->lang->line('common_click_to_unverify') != '') echo stripslashes($this->lang->line('common_click_to_unverify')); else echo 'Click to Unverify'; ?>" class="tip_top" href="javascript:confirm_status('<?php echo ADMIN_ENC_URL;?>/drivers/change_driver_vrification_status/<?php echo $mode; ?>/<?php echo $row->_id; ?>');">
                                                        <span class="badge_style b_done"><?php echo $verify_status; ?></span>
                                                    </a>
                                                    <?php
                                                } else if ($mode == '1') {
                                                    ?>
                                                    <a title="<?php if ($this->lang->line('common_click_to_verify') != '') echo stripslashes($this->lang->line('common_click_to_verify')); else echo 'Click to verify'; ?>" class="tip_top" href="javascript:confirm_status('<?php echo ADMIN_ENC_URL;?>/drivers/change_driver_vrification_status/<?php echo $mode; ?>/<?php echo $row->_id; ?>')">
                                                        <span class="badge_style"><?php echo $verify_status; ?></span>
                                                    </a>
                                                <?php } else {
                                                    ?>
                                                    <span class="badge_style"><?php echo $verify_status; ?></span>
                                                    <?php
                                                }
                                            } else {
                                                ?>
                                                <span class="badge_style b_done"><?php echo $verify_status; ?></span>
                                            <?php } ?>
                                        </td>

                                        <td class="center">
                                            <?php
											$disp_status = get_language_value_for_keyword($row->status,$this->data['langCode']);
                                            if ($allPrev == '1' || in_array('2', $driver)) {
                                                if ($row->status == 'Active') {
                                                    $mode = 0;
                                                } elseif ($row->status == 'Inactive') {
                                                    $mode = 1;
                                                } else {
                                                    $mode = 2;
                                                }
                                                if ($mode == '0') {
                                                    ?>
                                                    <a title="<?php if ($this->lang->line('common_click_to_inactive') != '') echo stripslashes($this->lang->line('common_click_to_inactive')); else echo 'Click to inactive'; ?>" class="tip_top" href="javascript:confirm_status('<?php echo ADMIN_ENC_URL;?>/drivers/change_driver_status/<?php echo $mode; ?>/<?php echo $row->_id; ?>');">
                                                        <span class="badge_style b_done"><?php echo $disp_status; ?></span>
                                                    </a>
                                                    <?php
                                                } else if ($mode == '1') {
                                                    ?>
                                                    <a title="<?php if ($this->lang->line('common_click_to_active') != '') echo stripslashes($this->lang->line('common_click_to_active')); else echo 'Click to active'; ?>" class="tip_top" href="javascript:confirm_status('<?php echo ADMIN_ENC_URL;?>/drivers/change_driver_status/<?php echo $mode; ?>/<?php echo $row->_id; ?>')">
                                                        <span class="badge_style"><?php echo $disp_status; ?></span>
                                                    </a>
                                                <?php } else {
                                                    ?>
                                                    <span class="badge_style"><?php echo $disp_status; ?></span>
                                                    <?php
                                                }
                                            } else {
                                                ?>
                                                <span class="badge_style b_done"><?php echo $disp_status; ?></span>
                                            <?php } ?>
                                        </td>
                                        <td class="center action-icons-wrap">
                                            <?php if ($allPrev == '1' || in_array('2', $driver)) { ?>
                                                <span><a class="action-icons c-bank" href="<?php echo ADMIN_ENC_URL;?>/drivers/banking/<?php echo $row->_id; ?>" title="<?php if ($this->lang->line('driver_connect_banking') != '') echo stripslashes($this->lang->line('driver_connect_banking')); else echo 'Connect Banking'; ?>"><?php if ($this->lang->line('driver_connect_banking') != '') echo stripslashes($this->lang->line('driver_connect_banking')); else echo 'Connect Banking'; ?></a></span>
                                                <span><a class="action-icons c-key" href="<?php echo ADMIN_ENC_URL;?>/drivers/change_password_form/<?php echo $row->_id; ?>" title="<?php if ($this->lang->line('driver_change_password') != '') echo stripslashes($this->lang->line('driver_change_password')); else echo 'Change Password'; ?>"><?php if ($this->lang->line('admin_common_edit') != '') echo stripslashes($this->lang->line('admin_common_edit')); else echo 'Edit'; ?></a></span>
                                                <span><a class="action-icons c-edit" href="<?php echo ADMIN_ENC_URL;?>/drivers/edit_driver_form/<?php echo $row->_id; ?>" title="<?php if ($this->lang->line('admin_common_edit') != '') echo stripslashes($this->lang->line('admin_common_edit')); else echo 'Edit'; ?>"><?php if ($this->lang->line('admin_common_edit') != '') echo stripslashes($this->lang->line('admin_common_edit')); else echo 'Edit'; ?></a></span>
                                            <?php } ?>
                                            <span><a class="action-icons c-suspend" href="<?php echo ADMIN_ENC_URL;?>/drivers/view_driver/<?php echo $row->_id; ?>" title="<?php if ($this->lang->line('admin_common_view') != '') echo stripslashes($this->lang->line('admin_common_view')); else echo 'View'; ?>"></a></span>
                                            <?php if ($allPrev == '1' || in_array('3', $driver)) { ?>	
                                                <?php if ($row->status != 'Deleted') { ?>
                                                    <span><a class="action-icons c-delete" href="javascript:confirm_delete('<?php echo ADMIN_ENC_URL;?>/drivers/delete_driver/<?php echo $row->_id; ?>')" title="<?php if ($this->lang->line('admin_common_delete') != '') echo stripslashes($this->lang->line('admin_common_delete')); else echo 'Delete'; ?>"></a></span>
                                                <?php } ?>
                                            <?php } ?>
                                        </td>
                                    </tr>
                                    <?php
                                }
                            }
                            ?>
                        </tbody>
                        <tfoot>
                            <tr>
                                <th class="center">
                                    <input name="checkbox_id[]" type="checkbox" value="on" class="checkall">
                                </th>
                                <th>
                                    <?php if ($this->lang->line('admin_car_types_name') != '') echo stripslashes($this->lang->line('admin_car_types_name')); else echo 'Name'; ?>
                                </th>
                                <th>
                                    <?php if ($this->lang->line('admin_driver_list_ride_stats') != '') echo stripslashes($this->lang->line('admin_driver_list_ride_stats')); else echo 'Ride Stats'; ?>
                                </th>
                                <th>
                                    <?php if ($this->lang->line('admin_driver_list_avg_count') != '') echo stripslashes($this->lang->line('admin_driver_list_avg_count')); else echo 'Avg(Count)'; ?>
                                </th>
                                <th>
                                     <?php if ($this->lang->line('admin_driver_list_doj') != '') echo stripslashes($this->lang->line('admin_driver_list_doj')); else echo 'DOJ'; ?>
                                </th>
                                <th>
                                     <?php if ($this->lang->line('admin_driver_list_avail') != '') echo stripslashes($this->lang->line('admin_driver_list_avail')); else echo 'AVAIL'; ?>
                                </th>
                                <th>
                                    <?php if ($this->lang->line('admin_drivers_mode') != '') echo stripslashes($this->lang->line('admin_drivers_mode')); else echo 'Mode'; ?>
                                </th>
                                <th>
                                    <?php if ($this->lang->line('admin_drivers_verified') != '') echo stripslashes($this->lang->line('admin_drivers_verified')); else echo 'Verified'; ?>
                                </th>
                                <th>
                                    <?php if ($this->lang->line('admin_subadmin_status') != '') echo stripslashes($this->lang->line('admin_subadmin_status')); else echo 'Status'; ?>
                                </th>
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
	<style>										
			.b_warn {
				background: orangered none repeat scroll 0 0;
				border: medium none red;
			}
			
			.filter_widget .btn_30_light {
				margin: -11px;
				width: 83%;
			}
            
            .filterwrap.f-submint {
                width: auto;
                margin-top: 20px;
            }            
            
            .filterwrap.filter-loc-section {
                width: 29%;
                 margin-top: 20px;
            }
            
            .filterwrap.filter-type-sec select {
                width: 100% !important;
            }
            
            .filterwrap.f-submint {
                margin-left: 20px;
            }
            
            .btn_30_light.second-row {
                width: 100% !important;
                margin-top: 0 !important;
            }
            
            .filterwrap.filter-loc-section #locations_id {
                width: 70% !important;
            }
            
            .filterwrap.filter-type-sec {
                width: 15%;
            }
            
            .filterwrap.filter-loc-section #filtervalue {
                width: auto !important;
            }
            
            .filterwrap {
                width: 15%;
                overflow: hidden;
                padding-right: 10px;
                float: left;
            }
            
            .btn_30_light span {
                display: inline-block;
                float: left;
                width:100%;
            }
            
            .filterwrap .title {
                color: #fff;
                padding: 5px;
                height: 20px;
            }
            
            .export_report {
                background: #be3b0a none repeat scroll 0 0 !important;
                float: right;
                margin-right: 15px;                
                border-radius: 3px !important;
                color:#ffffff !important; 
            }
            
            .widget_top.filter_widget {
                background: #4f5973 !important;
                height: auto !important;
                padding: 11px 8px;
            }
            
	</style>
    <script>
        var mdate = new Date('<?php echo date("Y-m-d H:i:s",strtotime($minDate)); ?>');
        var cformat = 'yy-mm-dd';
        $(function () {
            $("#datefrom").datepicker({  
                maxDate: '<?php echo date('Y-m-d'); ?>',
                dateFormat: cformat,
                minDate:mdate,
                onSelect: function(dateStr){
                    $("#dateto").datepicker("destroy");
                    //$("#dateto").val(dateStr);
                    $("#dateto").datepicker({ dateFormat: cformat,minDate: new Date(dateStr),maxDate: '<?php echo date('Y-m-d'); ?>'})
                }
            });
            
            $("#dateto").datepicker({  
                maxDate: '<?php echo date('Y-m-d'); ?>',
                dateFormat: cformat,
                minDate:mdate,
                onSelect: function(dateStr){
                    $("#datefrom").datepicker("destroy");
                    //$("#datefrom").val(dateStr);
                    $("#datefrom").datepicker({ dateFormat: cformat,maxDate: new Date(dateStr)})
                }
            });
        });

    </script>
<?php
$this->load->view(ADMIN_ENC_URL.'/templates/footer.php');
?>