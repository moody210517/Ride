<?php 
$this->load->view(ADMIN_ENC_URL.'/templates/header.php');
extract($privileges);
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



	$d_distance_unit_code = get_language_value_for_keyword('km',$this->data['langCode']);
	$d_distance_unit = get_language_value_for_keyword('Kilometer',$this->data['langCode']);
if($d_distance_unit=="mi"){
	$d_distance_unit_code = get_language_value_for_keyword('mi',$this->data['langCode']);
	$d_distance_unit = get_language_value_for_keyword('Miles',$this->data['langCode']);
}

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
		$("#export_mileage").click(function(event){
			event.preventDefault();
			get_field_values();
			window.location.href = "<?php echo base_url().ADMIN_ENC_URL; ?>/mileage/export_mileage_report?type="+$type+'&vehicle_category='+$vehicle_category+'&date_range='+$date_range+'&dateto='+$dateto+'&filtervalue='+$value;
		});
	});
	function get_field_values(){
		$type = $("#filtertype").val();
		$vehicle_category = $(".vehicle_category").val();
		$date_range = $("#rideFromdate").val();
		$dateto = $("#rideTodate").val();
		$value = $("#filtervalue").val();
	}
</script>
<script>
$(document).ready(function(){
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
        $("#country").attr("disabled", true);
	}
    if($country != ''){
		$('#country').css("display","inline");
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

<?php 
$freeRoaming = number_format($total_mileage_info['free_distance'],2,'.','');
$freeRoamingT = $total_mileage_info['free_duration'];
$customerPickup = number_format($total_mileage_info['pickup_distance'],2,'.','');
$customerPickupT = $total_mileage_info['pickup_duration'];
$customerDrop = number_format($total_mileage_info['drop_distance'],2,'.','');
$customerDropT = $total_mileage_info['drop_duration'];

$totalMileage = number_format($total_mileage_info['free_distance']+$total_mileage_info['pickup_distance']+$total_mileage_info['drop_distance'],2,'.','');
$totalMileageT = $freeRoamingT+$customerPickupT+$customerDropT;

$ar = array($totalMileage,$freeRoaming,$customerPickup,$customerDrop);


$maxVal = max($ar);

if($maxVal<200){
	$maxVal = 200;
}


?>

<link rel="stylesheet" href="plugins/jqwidgets-master/jqwidgets/styles/jqx.base.css" type="text/css" />
<script type="text/javascript" src="plugins/jqwidgets-master/scripts/demos.js"></script>
<script type="text/javascript" src="plugins/jqwidgets-master/jqwidgets/jqxcore.js"></script>
<script type="text/javascript" src="plugins/jqwidgets-master/jqwidgets/jqxdata.js"></script>
<script type="text/javascript" src="plugins/jqwidgets-master/jqwidgets/jqxdraw.js"></script>
<script type="text/javascript" src="plugins/jqwidgets-master/jqwidgets/jqxgauge.js"></script>
<script type="text/javascript">
$(document).ready(function () {
	var labels = { visible: true, position: 'outside' };
	
	var maxVal = <?php echo $maxVal; ?>;
	
	var ranG = [{ startValue: 0, endValue:  50, style: { fill: '#e2e2e2', stroke: '#e2e2e2' }, startDistance: '5%', endDistance: '5%' },
				{ startValue:  50, endValue:  90, style: { fill: '#f6de54', stroke: '#f6de54' }, startDistance: '5%', endDistance: '5%'},
				{ startValue:  90, endValue:  150, style: { fill: '#db5016', stroke: '#db5016' }, startDistance: '5%', endDistance: '5%'},
				{ startValue:  150, endValue:  maxVal, style: { fill: '#d02841', stroke: '#d02841' }, startDistance: '5%', endDistance: '5%'}];
	  
	//Create jqxGauge for free-roaming
	var totalMileage = <?php echo $totalMileage; ?>;
	$('#totalMileage').jqxGauge({
		ranges: ranG,
		cap: { radius: 0.04 },
		caption: { offset: [0, -25], value: totalMileage+' <?php echo $d_distance_unit_code; ?>', position: 'bottom' },
		value: 0,
		style: { stroke: '#ffffff', 'stroke-width': '1px', fill: '#ffffff' },
		animationDuration: 1500,
		colorScheme: 'scheme06',
		labels: labels,
		niceInterval: true,
		max: maxVal,
		ticksMinor: { interval: 5, size: '5%' },
		ticksMajor: { interval: 10, size: '10%' }
	});
	
	$('#totalMileage').jqxGauge({ width: 200, height: 200 });
    $('#totalMileage').jqxGauge('setValue', totalMileage);
	
	//Create jqxGauge for free-roaming
	var freeRoaming = <?php echo $freeRoaming; ?>;
	$('#free-roaming').jqxGauge({
		ranges: ranG,
		cap: { radius: 0.04 },
		caption: { offset: [0, -25], value: freeRoaming+' <?php echo $d_distance_unit_code; ?>', position: 'bottom' },
		value: 0,
		style: { stroke: '#ffffff', 'stroke-width': '1px', fill: '#ffffff' },
		animationDuration: 1500,
		colorScheme: 'scheme06',
		labels: labels,
		niceInterval: true,
		max: maxVal,
		ticksMinor: { interval: 5, size: '5%' },
		ticksMajor: { interval: 10, size: '10%' }
	});
	
	$('#free-roaming').jqxGauge({ width: 200, height: 200 });
    $('#free-roaming').jqxGauge('setValue', freeRoaming);
	
	//Create jqxGauge for customer-pickup
	var customerPickup = <?php echo $customerPickup; ?>;
	$('#customer-pickup').jqxGauge({
		ranges: ranG,
		cap: { radius: 0.04 },
		caption: { offset: [0, -25], value: customerPickup+' <?php echo $d_distance_unit_code; ?>', position: 'bottom' },
		value: 0,
		style: { stroke: '#ffffff', 'stroke-width': '1px', fill: '#ffffff' },
		animationDuration: 1500,
		colorScheme: 'scheme06',
		labels: labels,
		niceInterval: true,
		max: maxVal,
		ticksMinor: { interval: 5, size: '5%' },
		ticksMajor: { interval: 10, size: '10%' }
	});
	
	$('#customer-pickup').jqxGauge({ width: 200, height: 200 });
    $('#customer-pickup').jqxGauge('setValue', customerPickup);
	
	//Create jqxGauge for customer-drop
	var customerDrop = <?php echo $customerDrop; ?>;
	$('#customer-drop').jqxGauge({
		ranges: ranG,
		cap: { radius: 0.04 },
		caption: { offset: [0, -25], value: customerDrop+' <?php echo $d_distance_unit_code; ?>', position: 'bottom' },
		value: 0,
		style: { stroke: '#ffffff', 'stroke-width': '1px', fill: '#ffffff' },
		animationDuration: 1500,
		colorScheme: 'scheme06',
		labels: labels,
		niceInterval: true,
		max: maxVal,
		ticksMinor: { interval: 5, size: '5%' },
		ticksMajor: { interval: 10, size: '10%' }
	});
	
	$('#customer-drop').jqxGauge({ width: 200, height: 200 });
    $('#customer-drop').jqxGauge('setValue', customerDrop);
	
});
</script>

<style>
.gaugeD{
	margin:10% auto;
}
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
.gaugeC .grid_3 {
    margin: 2% 1%;
}
</style>

<div id="content" class="milage_base_sec">

    <div class="grid_container">	
		<div class="grid_12">
			<div class="">
					<div class="widget_content">
						<span class="clear"></span>						
						<div class="">
							<div class=" filter_wrap">
								<div class="widget_top filter_widget">
								
									<h6><?php if ($this->lang->line('admin_drivers_mileage_filter') != '') echo stripslashes($this->lang->line('admin_drivers_mileage_filter')); else echo 'Mileage Filter'; ?></h6>
									<div class="btn_30_light">	
									<?php
									$attributes = array('class' => 'form_container left_label', 'id' => 'filter_form','method'=>'GET','autocomplete'=>'off','enctype' => 'multipart/form-data');
									echo form_open(ADMIN_ENC_URL.'/mileage/display_mileage_list', $attributes)
									?>
										
										<select class="form-control" id="filtertype" name="type" >
											<option value="" data-val=""><?php if ($this->lang->line('admin_drivers_select_filter_type') != '') echo stripslashes($this->lang->line('admin_drivers_select_filter_type')); else echo 'Select Filter Type'; ?></option>
											<option value="driver_name" data-val="driver_name" <?php if(isset($type)){if($type=='driver_name'){ echo 'selected="selected"'; } }?>>
											<?php if ($this->lang->line('admin_drivers_driver_name') != '') echo stripslashes($this->lang->line('admin_drivers_driver_name')); else echo 'Driver Name'; ?></option>
											
											<option value="driver_location" data-val="location" <?php if(isset($type)){if($type=='driver_location'){ echo 'selected="selected"'; } }?>><?php if ($this->lang->line('admin_location_and_fare_location_location') != '') echo stripslashes($this->lang->line('admin_location_and_fare_location_location')); else echo 'Location'; ?></option>
											<option value="vehicle_type" data-val="vehicle_type" <?php if(isset($type)){if($type=='vehicle_type'){ echo 'selected="selected"'; } }?>><?php if ($this->lang->line('admin_drivers_car_type') != '') echo stripslashes($this->lang->line('admin_drivers_car_type')); else echo 'Car Types'; ?></option>
										</select>
                           
										<input name="value" id="filtervalue" type="text"  class="tipTop" title="<?php if ($this->lang->line('driver_enter_keyword') != '') echo stripslashes($this->lang->line('driver_enter_keyword')); else echo 'Please enter keyword'; ?>" value="<?php if(isset($value)) echo $value; ?>" placeholder="<?php if ($this->lang->line('driver_enter_keyword') != '') echo stripslashes($this->lang->line('driver_enter_keyword')); else echo 'Please enter keyword'; ?>" />
										<?php # echo '<pre>'; print_r($cabCats); die;?>
										<select name="vehicle_category" class='vehicle_category' style="display:none">
											<option value="" hidden="hidden"><?php if ($this->lang->line('admin_mileage_car_type') != '') echo stripslashes($this->lang->line('admin_mileage_car_type')); else echo 'Choose Car Type'; ?></option>
										<?php 
											$veh_cat = '';
											if(isset($_GET['vehicle_category']) && $_GET['vehicle_category']!=''){
												$veh_cat = $_GET['vehicle_category'];
											}
											foreach($cabCats as $cat){
												$cat_name = $cat->name;
												if(isset($cat->name_languages[$langCode ]) && $cat->name_languages[$langCode ] != '') $cat_name = $cat->name_languages[$langCode ];
												
												if($veh_cat != '' && $veh_cat == $cat->name){
													echo "<option selected value=".$cat->name.">".$cat_name."</option>";
												}else{
													echo "<option value=".$cat->name.">".$cat_name."</option>";
												}
												
											}
										?>
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
                                        <input name="date_range" id="rideFromdate" type="text"  class="tipTop monthYearPicker" title="<?php if ($this->lang->line('admin_ride_pls_starting_ride') != '') echo stripslashes($this->lang->line('admin_ride_pls_starting_ride')); else echo 'Please select the Starting Date'; ?>" readonly="readonly" value="<?php if(isset($_GET['date_range']))echo $_GET['date_range']; ?>" placeholder="<?php if ($this->lang->line('admin_ride_starting_ride') != '') echo stripslashes($this->lang->line('admin_ride_starting_ride')); else echo 'Starting Date'; ?>"/>
														
										<input name="dateto" id="rideTodate" type="text"  class="tipTop monthYearPicker" title="<?php if ($this->lang->line('admin_ride_pls_ending_ride') != '') echo stripslashes($this->lang->line('admin_ride_pls_ending_ride')); else echo 'Please select the Ending Date'; ?>" readonly="readonly" value="<?php if(isset($_GET['dateto']))echo $_GET['dateto']; ?>"  placeholder="<?php if ($this->lang->line('admin_ride_ending_ride') != '') echo stripslashes($this->lang->line('admin_ride_ending_ride')); else echo 'Ending Date'; ?>"/>
								
										<button type="submit" class="tipTop filterbtn"  original-title="<?php if ($this->lang->line('driver_enter_keyword_filter') != '') echo stripslashes($this->lang->line('driver_enter_keyword_filter')); else echo 'Select filter type and enter keyword to filter'; ?>">
											<span class="icon search"></span><span class="btn_link"><?php if ($this->lang->line('admin_drivers_filter') != '') echo stripslashes($this->lang->line('admin_drivers_filter')); else echo 'Filter'; ?></span>
										</button>
										<?php if(isset($filter) && $filter!=""){ ?>
										<a href="<?php echo ADMIN_ENC_URL;?>/mileage/display_mileage_list" class="tipTop filterbtn" original-title="<?php if ($this->lang->line('admin_notification_remove_filter') != '') echo stripslashes($this->lang->line('admin_notification_remove_filter')); else echo 'Remove Filter'; ?>">
											<span class="icon delete_co"></span>
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
		<div class="grid_12 gaugeC">
			<div class="grid_3">
				<div class="widget_wrap" >
					<div class="widget_content">
						<p class="gTitle"><?php if($this->lang->line('admin_rides_mileage_view_total_mileage'))echo($this->lang->line('admin_rides_mileage_view_total_mileage'));else echo'Total Mileage'; ?></p>
						<div id="totalMileage" class="gaugeD"></div>
						<p class="gDuration"><?php if($this->lang->line('admin_drivers_mileage_view_duration'))echo($this->lang->line('admin_drivers_mileage_view_duration'));else echo'Duration'; ?> : <span><?php echo convertToHoursMins($totalMileageT);?></span><p>
					</div>
				</div>
			</div>
			<div class="grid_3">
				<div class="widget_wrap" >
					<div class="widget_content">
						<p class="gTitle"><?php if($this->lang->line('admin_rides_mileage_view_free_roaming'))echo($this->lang->line('admin_rides_mileage_view_free_roaming'));else echo'Free Roaming	'; ?></p>
						<div id="free-roaming" class="gaugeD"></div>
						<p class="gDuration"><?php if($this->lang->line('admin_drivers_mileage_view_duration'))echo($this->lang->line('admin_drivers_mileage_view_duration'));else echo'Duration'; ?> : <span><?php echo convertToHoursMins($freeRoamingT);?></span></p>
					</div>
				</div>
			</div>
			<div class="grid_3">
				<div class="widget_wrap">
					<div class="widget_content">
						<p class="gTitle"><?php if($this->lang->line('admin_rides_mileage_approach_trip'))echo($this->lang->line('admin_rides_mileage_approach_trip'));else echo'Approaching Trip'; ?></p>
						<div id="customer-pickup" class="gaugeD"></div>
						<p class="gDuration"><?php if($this->lang->line('admin_drivers_mileage_view_duration'))echo($this->lang->line('admin_drivers_mileage_view_duration'));else echo'Duration'; ?> : <span><?php echo convertToHoursMins($customerPickupT);?> </span></p>
					</div>
				</div>
			</div>
			<div class="grid_3">
				<div class="widget_wrap">
					<div class="widget_content">
						<p class="gTitle"><?php if($this->lang->line('admin_rides_mileage_on_trip'))echo($this->lang->line('admin_rides_mileage_on_trip'));else echo'On Trip'; ?></p>
						<div id="customer-drop" class="gaugeD"></div>
						<p class="gDuration"><?php if($this->lang->line('admin_drivers_mileage_view_duration'))echo($this->lang->line('admin_drivers_mileage_view_duration'));else echo'Duration'; ?>  : <span><?php echo convertToHoursMins($customerDropT); ?></span></p>
					</div>
				</div>
			</div>
		</div>
       
        <div class="grid_12">
            <div class="widget_wrap">
                <div class="widget_top">
                    <span class="h_icon blocks_images"></span>
                    <h6><?php  if ($this->lang->line('admin_menu_mileage_list') != '') echo stripslashes($this->lang->line('admin_menu_mileage_list')); else echo $heading;  ?></h6>
									<?php	if($mileage_data != ''){?>
					<a style="color:#fff" class="p_edit tipTop export_report" id="export_mileage"><?php if ($this->lang->line('admin_drivers_mileage_export') != '') echo stripslashes($this->lang->line('admin_drivers_mileage_export')); else echo 'Export'; ?></a> <?php } ?>
                </div>
                <div class="widget_content">
     
                    <table class="display" id="mileage_data">
                        <thead>
                            <tr>
                                
                               <th class="tip_top" title="<?php if ($this->lang->line('dash_click_sort') != '') echo stripslashes($this->lang->line('dash_click_sort')); else echo 'Click to sort'; ?>">
                                    <?php if ($this->lang->line('admin_car_types_name') != '') echo stripslashes($this->lang->line('admin_car_types_name')); else echo 'Name'; ?>
                                </th>
                                <th class="tip_top" title="<?php if ($this->lang->line('dash_click_sort') != '') echo stripslashes($this->lang->line('dash_click_sort')); else echo 'Click to sort'; ?>">
											<?php if ($this->lang->line('admin_drivers_mileage_category') != '') echo stripslashes($this->lang->line('admin_drivers_mileage_category')); else echo 'Category'; ?>
                                </th>
                                <th class="tip_top" title="<?php if ($this->lang->line('dash_click_sort') != '') echo stripslashes($this->lang->line('dash_click_sort')); else echo 'Click to sort'; ?>">
                                 <?php if ($this->lang->line('admin_drivers_mileage_total_distance') != '') echo stripslashes($this->lang->line('admin_drivers_mileage_total_distance')); else echo 'Total Distance'; ?> (<?php if ($this->lang->line('admin_drivers_mileage_total_distance') != '') echo stripslashes($this->lang->line('admin_drivers_mileage_kilometer')); else echo $d_distance_unit; ?>) 
                                </th>
                                <th class="tip_top" title="<?php if ($this->lang->line('dash_click_sort') != '') echo stripslashes($this->lang->line('dash_click_sort')); else echo 'Click to sort'; ?>" width="150px">
                                 <?php if ($this->lang->line('admin_drivers_mileage_total_duration') != '') echo stripslashes($this->lang->line('admin_drivers_mileage_total_duration')); else echo 'Total Duration'; ?>  (<?php if ($this->lang->line('mileage_total_duration_metric') != '') echo stripslashes($this->lang->line('mileage_total_duration_metric')); else echo 'Hour'; ?>)
											</th>
                                <th class="tip_top" title="<?php if ($this->lang->line('dash_click_sort') != '') echo stripslashes($this->lang->line('dash_click_sort')); else echo 'Click to sort'; ?>">
                                 <?php if ($this->lang->line('admin_drivers_mileage_free_roaming_distance') != '') echo stripslashes($this->lang->line('admin_drivers_mileage_free_roaming_distance')); else echo 'Free Roaming Distance'; ?>  
											(<?php if ($this->lang->line('admin_drivers_mileage_total_distance') != '') echo stripslashes($this->lang->line('admin_drivers_mileage_kilometer')); else echo $d_distance_unit; ?>)
                                </th>
                                <th class="tip_top" title="<?php if ($this->lang->line('dash_click_sort') != '') echo stripslashes($this->lang->line('dash_click_sort')); else echo 'Click to sort'; ?>">
                                 <?php if ($this->lang->line('admin_drivers_mileage_free_roaming_duration') != '') echo stripslashes($this->lang->line('admin_drivers_mileage_free_roaming_duration')); else echo 'Free Roaming Duration'; ?>  (<?php if ($this->lang->line('mileage_total_duration_metric') != '') echo stripslashes($this->lang->line('mileage_total_duration_metric')); else echo 'Hour'; ?>)
                                </th>
                                <th>
											<?php if ($this->lang->line('admin_drivers_mileage_pickup_distance') != '') echo stripslashes($this->lang->line('admin_drivers_mileage_pickup_distance')); else echo 'Approaching Distance'; ?> 
                                    (<?php if ($this->lang->line('admin_drivers_mileage_total_distance') != '') echo stripslashes($this->lang->line('admin_drivers_mileage_kilometer')); else echo $d_distance_unit; ?>)
                                </th>
                                <th class="tip_top" title="<?php if ($this->lang->line('dash_click_sort') != '') echo stripslashes($this->lang->line('dash_click_sort')); else echo 'Click to sort'; ?>">
                                 <?php if ($this->lang->line('admin_drivers_mileage_pickup_duration') != '') echo stripslashes($this->lang->line('admin_drivers_mileage_pickup_duration')); else echo 'Approaching Duration'; ?> 	(<?php if ($this->lang->line('mileage_total_duration_metric') != '') echo stripslashes($this->lang->line('mileage_total_duration_metric')); else echo 'Hour'; ?>)									
                                </th>
                                <th class="tip_top" title="<?php if ($this->lang->line('dash_click_sort') != '') echo stripslashes($this->lang->line('dash_click_sort')); else echo 'Click to sort'; ?>">
											<?php if ($this->lang->line('admin_drivers_mileage_trip_distance') != '') echo stripslashes($this->lang->line('admin_drivers_mileage_trip_distance')); else echo 'Trip Distance'; ?> 
											 (<?php if ($this->lang->line('admin_drivers_mileage_total_distance') != '') echo stripslashes($this->lang->line('admin_drivers_mileage_kilometer')); else echo $d_distance_unit; ?>)
                                </th>
                                <th class="tip_top" title="<?php if ($this->lang->line('dash_click_sort') != '') echo stripslashes($this->lang->line('dash_click_sort')); else echo 'Click to sort'; ?>">
                                 <?php if ($this->lang->line('admin_drivers_mileage_trip_duration') != '') echo stripslashes($this->lang->line('admin_drivers_mileage_trip_duration')); else echo 'Trip Duration'; ?> (<?php if ($this->lang->line('mileage_total_duration_metric') != '') echo stripslashes($this->lang->line('mileage_total_duration_metric')); else echo 'Hour'; ?>)
											
                                </th>
                                <th>
                                    <?php if ($this->lang->line('admin_subadmin_action') != '') echo stripslashes($this->lang->line('admin_subadmin_action')); else echo 'Action'; ?>
                                </th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php
                           
                                foreach ($mileage_data as $key=>$row) {
									
                                    ?>
                                    <tr style="border-bottom: 1px solid #dddddd !important;">
                                       
                                        <td class="center tip_top" title="<?php echo $row['email']; ?>">
                                            <?php echo $row['name']; ?>
											  
                                        </td>
										<td class="center">
                                            <?php echo $row['catgeory_name']; ?>
											  
                                        </td>
										<td class="center">
                                            <?php echo number_format(($row['free_distance']+$row['pickup_distance']+$row['drop_distance']),2); ?>
                                        </td>
										<td class="center">
                                         <?php 
										 
										  $total_duration=$row['free_duration']+$row['pickup_duration']+$row['drop_duration']; 
										  
										  echo $total_duration=convertToHoursMins($total_duration);
										 ?>
											  
                                        </td>
										<td class="center">
                                            <?php echo number_format($row['free_distance'],2); ?>
											  
                                        </td>
										<td class="center">
										<?php 
										
										 echo $free_duration=convertToHoursMins($row['free_duration']);
										?>
											  
                                        </td>
										<td class="center">
                                            <?php echo number_format($row['pickup_distance'],2); ?>
											  
                                        </td>
										<td class="center">
                                            <?php 
											
											echo $pickup_duration=convertToHoursMins($row['pickup_duration']);
											?>
											  
                                        </td>
										<td class="center">
                                            <?php echo number_format($row['drop_distance'],2); ?>
											  
                                        </td>
										<td class="center">
                                            <?php 											
											echo $drop_duration=convertToHoursMins($row['drop_duration']);
											?>
											  
                                        </td>
										

                                        
                                        <td class="center action-icons-wrap" style="width:140px;">
											<?php
												$urlVal='';
												if($end_date!='' && $start_date!=''){
													$enc_fromdate=$start_date;
													$enc_todate=$end_date;
													$urlVal='?&date_from='.$enc_fromdate.'&date_to='.$enc_todate;
												}
											?>
                                            <span><a class="action-icons c-suspend" href="<?php echo ADMIN_ENC_URL;?>/mileage/view_driver_mileage/<?php echo $key; ?><?php echo $urlVal; ?>" title="<?php if ($this->lang->line('admin_common_view') != '') echo stripslashes($this->lang->line('admin_common_view')); else echo 'View'; ?>"></a></span>
                                            
                                        </td>
                                    </tr>
                                    <?php
                                }
                            
                            ?>
                        </tbody>
                        <tfoot>
                            <tr>
                                
                                <th>
                                    <?php if ($this->lang->line('admin_car_types_name') != '') echo stripslashes($this->lang->line('admin_car_types_name')); else echo 'Name'; ?>
                                </th>
                                <th>
                                   <?php if ($this->lang->line('admin_drivers_mileage_category') != '') echo stripslashes($this->lang->line('admin_drivers_mileage_category')); else echo 'Category'; ?>
                                </th>
                                <th>
                                    <?php if ($this->lang->line('admin_drivers_mileage_total_distance') != '') echo stripslashes($this->lang->line('admin_drivers_mileage_total_distance')); else echo 'Total Distance'; ?>   (<?php if ($this->lang->line('admin_drivers_mileage_total_distance') != '') echo stripslashes($this->lang->line('admin_drivers_mileage_kilometer')); else echo $d_distance_unit; ?>)
                                </th>
                                <th>
                                    <?php if ($this->lang->line('admin_drivers_mileage_total_duration') != '') echo stripslashes($this->lang->line('admin_drivers_mileage_total_duration')); else echo 'Total Duration'; ?> (<?php if ($this->lang->line('mileage_total_duration_metric') != '') echo stripslashes($this->lang->line('mileage_total_duration_metric')); else echo 'Hour'; ?>)
                                </th>
                                <th>
                                    <?php if ($this->lang->line('admin_drivers_mileage_free_roaming_distance') != '') echo stripslashes($this->lang->line('admin_drivers_mileage_free_roaming_distance')); else echo 'Free Roaming Distance'; ?>  (<?php if ($this->lang->line('admin_drivers_mileage_total_distance') != '') echo stripslashes($this->lang->line('admin_drivers_mileage_kilometer')); else echo $d_distance_unit; ?>)
                                </th>
                                <th>
                                    <?php if ($this->lang->line('admin_drivers_mileage_free_roaming_duration') != '') echo stripslashes($this->lang->line('admin_drivers_mileage_free_roaming_duration')); else echo 'Free Roaming Duration'; ?> (<?php if ($this->lang->line('mileage_total_duration_metric') != '') echo stripslashes($this->lang->line('mileage_total_duration_metric')); else echo 'Hour'; ?>)
                                </th>
                                <th>
                                   <?php if ($this->lang->line('admin_drivers_mileage_pickup_distance') != '') echo stripslashes($this->lang->line('admin_drivers_mileage_pickup_distance')); else echo 'Approaching Distance'; ?> (<?php if ($this->lang->line('admin_drivers_mileage_total_distance') != '') echo stripslashes($this->lang->line('admin_drivers_mileage_kilometer')); else echo $d_distance_unit; ?>)
                                </th>
                                <th>
                                    <?php if ($this->lang->line('admin_drivers_mileage_pickup_duration') != '') echo stripslashes($this->lang->line('admin_drivers_mileage_pickup_duration')); else echo 'Approaching Duration'; ?> (<?php if ($this->lang->line('mileage_total_duration_metric') != '') echo stripslashes($this->lang->line('mileage_total_duration_metric')); else echo 'Hour'; ?>)
                                </th>
                                <th>
                                   <?php if ($this->lang->line('admin_drivers_mileage_trip_distance') != '') echo stripslashes($this->lang->line('admin_drivers_mileage_trip_distance')); else echo 'Trip Distance'; ?>  (<?php if ($this->lang->line('admin_drivers_mileage_total_distance') != '') echo stripslashes($this->lang->line('admin_drivers_mileage_kilometer')); else echo $d_distance_unit; ?>)
                                </th>
                                <th>
                                   <?php if ($this->lang->line('admin_drivers_mileage_trip_duration') != '') echo stripslashes($this->lang->line('admin_drivers_mileage_trip_duration')); else echo 'Trip Duration'; ?> (<?php if ($this->lang->line('mileage_total_duration_metric') != '') echo stripslashes($this->lang->line('mileage_total_duration_metric')); else echo 'Hour'; ?>)
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
		<style>
		.lct{
			text-transform: lowercase;
		}
		</style>
        
    </div>
    <span class="clear"></span>
</div>
</div>
<?php
$this->load->view(ADMIN_ENC_URL.'/templates/footer.php');
?>