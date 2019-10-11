<?php
$this->load->view(ADMIN_ENC_URL.'/templates/header.php');
extract($privileges);
?>


<?php 
$freeRoaming = number_format($total_mileage_info['free_distance'],2,'.','');
$freeRoamingT = $total_mileage_info['free_duration'];
$customerPickup = number_format($total_mileage_info['pickup_distance'],2,'.','');
$customerPickupT = $total_mileage_info['pickup_duration'];
$customerDrop = number_format($total_mileage_info['drop_distance'],2,'.','');
$customerDropT = $total_mileage_info['drop_duration'];

$ar = array($freeRoaming,$customerPickup,$customerDrop);


$maxVal = max($ar);

if($maxVal<500){
	$maxVal = 500;
}


?>

<div id="content">
	<div class="grid_container">
	
		<div class="grid_12">
			<div class="grid_4">
				<div class="widget_wrap" >
					<div class="widget_top">
						<span class="h_icon list"></span>
						<h6>Free Roaming</h6>
					</div>
					<div class="widget_content">
						<div id="free-roaming" class="gaugeD"></div>
						<h3><center>Duration : <?php echo get_time_to_string($freeRoamingT); ?></center></h3>
						<p>Drivers are travelled without any trip</p>
					</div>
				</div>
			</div>
			<div class="grid_4">
				<div class="widget_wrap">
					<div class="widget_top">
						<span class="h_icon list"></span>
						<h6>Approaching Trip</h6>
					</div>
					<div class="widget_content">
						<div id="customer-pickup" class="gaugeD"></div>
						<h3>Duration : 00:28:15 Hrs</h3>
						<p>Drivers are travelled without any trip</p>
					</div>
				</div>
			</div>
			<div class="grid_4">
				<div class="widget_wrap">
					<div class="widget_top">
						<span class="h_icon list"></span>
						<h6>On Trip</h6>
					</div>
					<div class="widget_content">
						<div id="customer-drop" class="gaugeD"></div>
						<h3>Duration : 00:28:15 Hrs</h3>
						<p>Drivers are travelled without any trip</p>
					</div>
				</div>
			</div>
		</div>
	</div>
    <span class="clear"></span>
</div>


<link rel="stylesheet" href="plugins/jqwidgets-master/jqwidgets/styles/jqx.base.css" type="text/css" />
<script type="text/javascript" src="plugins/jqwidgets-master/scripts/demos.js"></script>
<script type="text/javascript" src="plugins/jqwidgets-master/jqwidgets/jqxcore.js"></script>
<script type="text/javascript" src="plugins/jqwidgets-master/jqwidgets/jqxdata.js"></script>
<script type="text/javascript" src="plugins/jqwidgets-master/jqwidgets/jqxdraw.js"></script>
<script type="text/javascript" src="plugins/jqwidgets-master/jqwidgets/jqxgauge.js"></script>
<script type="text/javascript">
$(document).ready(function () {
	var labels = { visible: true, position: 'inside' };
	
	var maxVal = <?php echo $maxVal; ?>;
	
	var ranG = [{ startValue: 0, endValue:  30, style: { fill: '#e2e2e2', stroke: '#e2e2e2' }, startDistance: '5%', endDistance: '5%' },
				{ startValue:  30, endValue:  60, style: { fill: '#f6de54', stroke: '#f6de54' }, startDistance: '5%', endDistance: '5%'},
				{ startValue:  60, endValue:  90, style: { fill: '#db5016', stroke: '#db5016' }, startDistance: '5%', endDistance: '5%'},
				{ startValue:  90, endValue:  maxVal, style: { fill: '#d02841', stroke: '#d02841' }, startDistance: '5%', endDistance: '5%'}];
	  
	//Create jqxGauge for free-roaming
	var freeRoaming = <?php echo $freeRoaming; ?>;
	$('#free-roaming').jqxGauge({
		ranges: ranG,
		cap: { radius: 0.04 },
		caption: { offset: [0, -25], value: freeRoaming+' KM', position: 'bottom' },
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
	
	$('#free-roaming').jqxGauge({ width: 300, height: 300 });
    $('#free-roaming').jqxGauge('setValue', freeRoaming);
	
	//Create jqxGauge for customer-pickup
	var customerPickup = <?php echo $customerPickup; ?>;
	$('#customer-pickup').jqxGauge({
		ranges: ranG,
		cap: { radius: 0.04 },
		caption: { offset: [0, -25], value: customerPickup+' KM', position: 'bottom' },
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
	
	$('#customer-pickup').jqxGauge({ width: 300, height: 300 });
    $('#customer-pickup').jqxGauge('setValue', customerPickup);
	
	//Create jqxGauge for customer-drop
	var customerDrop = <?php echo $customerDrop; ?>;
	$('#customer-drop').jqxGauge({
		ranges: ranG,
		cap: { radius: 0.04 },
		caption: { offset: [0, -25], value: customerDrop+' KM', position: 'bottom' },
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
	
	$('#customer-drop').jqxGauge({ width: 300, height: 300 });
    $('#customer-drop').jqxGauge('setValue', customerDrop);
	
});
</script>

<style>
.gaugeD{
	margin:10% auto;
}
</style>

<?php
$this->load->view(ADMIN_ENC_URL.'/templates/footer.php');
?>