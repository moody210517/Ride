<?php
//print_R($ridesList->result());die;
//$this->load->view(OPERATOR_NAME.'/templates/common_header');
$this->load->view('site/templates/common_header');
?> 
<base href="<?php echo base_url(); ?>"></base>
<style>
    body {
        margin: 0px;
        padding: 0px;
    }
    
    table {
        border: 1px solid #ccc;
        border-collapse: collapse;
    }
    
    td {
        border: 1px solid #ccc;
        font-size: 11px;
        padding: 4px;
        text-align: left;
    }
    
    th {
        border: 1px solid #ccc;
        padding: 7px;
        font-size: 13px;
        text-align: left;
    }
    
    tr {
        border: 1px solid #ccc;
    }
    
    #col1 {
        width: 50%;
        float: left;
        border: 1px solid #000;
        border-right: none;
		height:400px;
		overflow:auto;
    }
    
    #col2 {
        width: 50%;
        float: left;
        border: 1px solid #000;
        border-right: none;
		height:400px;
		overflow:auto;
    }
    
    #col3 {
        width: 50%;
        float: left;
        border: 1px solid #000;
        border-right: none;
		height:400px;
		overflow:auto;
    }
    
    #col4 {
        width: 50%;
        float: left;
        border: 1px solid #000;
		height:400px;
		overflow:auto;
    }
	span.service_type {
		clear: both;
		float: left;
		width: 100%;
		font-size: 10px;
	}
	span.rides_type {
		clear: both;
		float: left;
		width: 100%;
		font-size: 10px;
	}
	.tbltitle {
		padding: 4px;
		font-size: 18px;
		text-align: center;
		height: 55px !important;
	}
.view-all {
    color: #fff;
    font-size: 14px;
    float: right;
    background: #062a40;
    padding: 10px 7px 7px 7px;
}
.view-all:hover {
	color:#fff;
	font-size:14px;
}
.back-options {
	width: 17%;
    margin-top: 10px;
    color: black;
    margin-left: 4px;
}
.back-options a {
	color: #fff;
    background: #0ad7af;
    padding: 10px;
    display: inline-block;
}
.tr-red{
	background:#fb4f4f;
	color:#fff;
}
.tr-green{
	background:#0ad7af;
	color:#000;
}
.tr-blue{
	background:#062a40;
	color:#fff;
}
.tr-yellow{
	background:#ffd34d;
	color:#000;
}
</style>

<body>
<p class="back-options">
	<a href="<?php echo base_url().OPERATOR_NAME; ?>" style="background-color:#00c0ef ;"><?php if ($this->lang->line('admin_back_to_operator') != '') echo stripslashes($this->lang->line('admin_back_to_operator')); else echo 'Back to Operator Panel'; ?></a>
</p>
    <div id="col1">
        <table width="100%">
			<thead>
				<tr width="100%">
					<td align="center" colspan="4" class="tbltitle" style="background-color: #a6e6b3;color:#000;"><?php if ($this->lang->line('admin_acpt_serch_details') != '') echo stripslashes($this->lang->line('admin_acpt_serch_details')); else echo 'Accepted booking, searching for drivers'; ?></td>
				</tr>
				<tr>
					<th width="20%"><?php if ($this->lang->line('driver_user') != '') echo stripslashes($this->lang->line('driver_user')); else echo 'User'; ?></th>
					<th width="20%"><?php if ($this->lang->line('admin_users_details') != '') echo stripslashes($this->lang->line('admin_users_details')); else echo 'Details'; ?></th>
					<th width="20%"><?php if ($this->lang->line('driver_view_time') != '') echo stripslashes($this->lang->line('driver_view_time')); else echo 'Time'; ?></th>
				</tr>
			</thead>
            <tbody id="booked_rides">
				<?php 
                #print_r($ridesList->result());
				foreach($ridesList->result() as $bRides){
					if($bRides->ride_status == 'Booked'){
				?>
				<tr>
					<td width="20%"><?php echo $bRides->user['name']; ?></td>
					<td width="20%"><?php echo $bRides->ride_id; ?><span class="service_type"><?php echo $bRides->booking_information['service_type']; ?><span><span class="rides_type"><?php
                    if(isset($bRides->type)&& $bRides->type == 'Now')
                    { echo 'Ride Now'; }
                    else{ echo 'Ride Later'; }
                    ?><span></td>
					<td width="20%">
                        <?php if($bRides->type != 'Now') {?>
                           <span class="service_type">Booking Time -<?php echo date('h:i A',MongoEPOCH($bRides->booking_information['booking_date'])); ?></span>
                            <span class="service_type">Pick-up Time -<?php echo date('h:i A',MongoEPOCH($bRides->booking_information['actual_pickup_date'])); ?></span>                            
                        <?php } else { ?>
                         <span class="service_type">Pick-up Time -<?php date('h:i A',MongoEPOCH($bRides->booking_information['booking_date'])); ?></span>      
                        <?php }?>
                     </td>
					
				</tr>
				<?php 
						}
					} 
				?>
			</tbody>

        </table>
    </div>
    <div id="col2">
        <table width="100%">
			<thead>
				<tr width="100%">
					<td align="center" colspan="4" class="tbltitle" style="background-color: #61e47a ;color:#000;"><?php if ($this->lang->line('admin_assgn_enrout_details') != '') echo stripslashes($this->lang->line('admin_assgn_enrout_details')); else echo 'Assigned driver, driver enroute to pickup passenger'; ?></td>
				</tr>
				<tr>
					<th width="20%"><?php if ($this->lang->line('driver_user') != '') echo stripslashes($this->lang->line('driver_user')); else echo 'User'; ?></th>
					<th width="20%"><?php if ($this->lang->line('dash_driver') != '') echo stripslashes($this->lang->line('dash_driver')); else echo 'Driver'; ?></th>
					<th width="20%"><?php if ($this->lang->line('admin_users_details') != '') echo stripslashes($this->lang->line('admin_users_details')); else echo 'Details'; ?></th>
					<th width="20%"><?php if ($this->lang->line('driver_view_time') != '') echo stripslashes($this->lang->line('driver_view_time')); else echo 'Time'; ?></th>
				</tr>
			</thead>
            <tbody id="confirmed_rides">
				<?php 
				foreach($ridesList->result() as $bRides){
					if($bRides->ride_status == 'Confirmed' || $bRides->ride_status == 'Accepted'){
				?>
				<tr>
					<td width="20%"><?php echo $bRides->user['name']; ?></td>
					<td width="20%"><?php echo $bRides->driver['name']; ?></td>
					<td width="20%"><?php echo $bRides->ride_id; ?><span class="service_type"><?php echo $bRides->booking_information['service_type']; ?><span><span class="rides_type"><?php
                    if(isset($bRides->type)&& $bRides->type == 'Now')
                    { echo 'Ride Now'; }
                    else{ echo 'Ride Later'; }
                    ?><span></td>
					<td width="20%"><?php echo date('h:i A',MongoEPOCH($bRides->booking_information['booking_date'])); ?></td>
				</tr>
				<?php 
						}
					} 
				?>
			</tbody>

        </table>
    </div>
    <div id="col3">
        <table width="100%">
			<thead>
				<tr width="100%">
					<td align="center" colspan="4" class="tbltitle" style="background-color: #05c52a;color:#000;"><?php if ($this->lang->line('admin_pick_onride_details') != '') echo stripslashes($this->lang->line('admin_pick_onride_details')); else echo 'Driver arrived at pickup location/Passenger picked up, on-ride'; ?></td>
				</tr>
				<tr>
					<th width="20%"><?php if ($this->lang->line('driver_user') != '') echo stripslashes($this->lang->line('driver_user')); else echo 'User'; ?></th>
					<th width="20%"><?php if ($this->lang->line('dash_driver') != '') echo stripslashes($this->lang->line('dash_driver')); else echo 'Driver'; ?></th>
					<th width="20%"><?php if ($this->lang->line('admin_users_details') != '') echo stripslashes($this->lang->line('admin_users_details')); else echo 'Details'; ?></th>
					<th width="20%"><?php if ($this->lang->line('driver_view_time') != '') echo stripslashes($this->lang->line('driver_view_time')); else echo 'Time'; ?></th>
				</tr>
			</thead>
			<tbody id="on_rides">
				<?php 
				foreach($ridesList->result() as $bRides){
					if($bRides->ride_status == 'Arrived' || $bRides->ride_status == 'Onride'){
				?>
				<tr class="<?php if($bRides->ride_status == 'Arrived'){ ?>tr-yellow<?php } ?><?php if($bRides->ride_status == 'Onride'){ ?>tr-blue<?php } ?>">
					<td width="20%"><?php echo $bRides->user['name']; ?></td>
					<td width="20%"><?php echo $bRides->driver['name']; ?></td>
					<td width="20%"><?php echo $bRides->ride_id; ?><span class="service_type"><?php echo $bRides->booking_information['service_type']; ?><span><span class="rides_type"><?php
                    
                    if(isset($bRides->type)&& $bRides->type == 'Now')
                    { echo 'Ride Now'; } else{ echo 'Ride Later'; }
                    
                    ?><span></td>
					<td width="20%"><?php echo date('h:i A',MongoEPOCH($bRides->booking_information['booking_date'])); ?></td>
				</tr>
				<?php 
						}
					} 
				?>
			</tbody>

        </table>
    </div>
    <div id="col4">
        <table width="100%">
			<thead>
				<tr width="100%">
					<td align="center" colspan="4" class="tbltitle" style="background-color: #109608;color:#fff;"><?php if ($this->lang->line('admin_cpltd_cancl_details') != '') echo stripslashes($this->lang->line('admin_cpltd_cancl_details')); else echo 'Trip Completed/Cancelled'; ?> <a href="<?php echo base_url().OPERATOR_NAME; ?>/trip/display_trips?act=Completed" target="_blank" class="view-all" > <?php if ($this->lang->line('admin_view_all_details') != '') echo stripslashes($this->lang->line('admin_view_all_details')); else echo 'View All'; ?></a></td>
				</tr>
				<tr>
					<th width="20%"><?php if ($this->lang->line('driver_user') != '') echo stripslashes($this->lang->line('driver_user')); else echo 'User'; ?></th>
					<th width="20%"><?php if ($this->lang->line('dash_driver') != '') echo stripslashes($this->lang->line('dash_driver')); else echo 'Driver'; ?></th>
					<th width="20%"><?php if ($this->lang->line('admin_users_details') != '') echo stripslashes($this->lang->line('admin_users_details')); else echo 'Details'; ?></th>
					<th width="20%"><?php if ($this->lang->line('driver_view_time') != '') echo stripslashes($this->lang->line('driver_view_time')); else echo 'Time'; ?></th>
				</tr>
			</thead>
			<tbody id="completed_rides">
				<?php 
				foreach($ridesList->result() as $bRides){
					if($bRides->ride_status == 'Completed' || $bRides->ride_status == 'Cancelled'){
				?>
				<tr class="<?php if($bRides->ride_status == 'Cancelled'){ ?>tr-red<?php } ?><?php if($bRides->ride_status == 'Completed'){ ?>tr-green<?php } ?>">
					<td width="20%"><?php echo $bRides->user['name']; ?></td>
					<td width="20%"><?php echo $bRides->driver['name']; ?></td>
					<td width="20%"><?php echo $bRides->ride_id; ?><span class="service_type"><?php echo $bRides->booking_information['service_type']; ?><span><span class="rides_type"><?php
                    
                    if(isset($bRides->type)&& $bRides->type == 'Now')
                    { echo 'Ride Now'; } else{ echo 'Ride Later'; }
                    
                    ?><span></td>
					<td width="20%"><?php echo date('h:i A',MongoEPOCH($bRides->booking_information['booking_date'])); ?></td>
				</tr>
				<?php 
						}
					} 
				?>
			</tbody>
        </table>
    </div>
    <script src="https://ajax.googleapis.com/ajax/libs/jquery/3.3.1/jquery.min.js"></script>
    <script>
        $(function() {
            var h1 = $('#col1').height();
            var h2 = $('#col2').height();
            var h3 = $('#col3').height();
			 var h4 = $('#col4').height();

            var maxH = Math.max(h1, h2, h3);
            $('div').height(maxH);

        });
		setInterval(function(){
			$.ajax({
				type: "POST",
				url: '<?php echo base_url().OPERATOR_NAME; ?>/trip/get_rides_ajax',
				data: {'authkey':'<?php echo APP_NAME; ?>'},
				dataType: 'json',
				success: function (data) {
					console.log(data);
					if(data.status == '1'){
						$('#booked_rides').html(data.booked_rides);
						$('#confirmed_rides').html(data.confimed_rides);
						$('#on_rides').html(data.on_rides);
						$('#completed_rides').html(data.completed_rides);
					}
				},
				error: function (data) {
					console.log('An error occurred.');
					console.log(data);
				},
			});
		}, 3000);
    </script>
</body>
</html>