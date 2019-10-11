<?php
$this->load->view('driver/templates/profile_header.php');
?>
<section class="profile_pic_sec row">
   <div  class="profile_login_cont earn-contain">
      <div class="share_det_title">
         <h2><span><?php 
						if($this->lang->line('driver_revenue_summary') != '') echo stripslashes($this->lang->line('driver_revenue_summary')); else  echo 'REVENUE SUMMARY';
						?>  : <?php echo get_time_to_string("j M Y",MongoEPOCH($bill_details['bill_from'])) . ' - ' . get_time_to_string("j M Y",MongoEPOCH($bill_details['bill_to'])); ?></span></h2>
      </div>
      <div class="profile_ac_form summary-earn-form">
		<?php if (!empty($rideList)) { ?>
         <table id="example" class="table table-striped table-bordered" cellspacing="0" width="100%">
            <thead>
               <tr>
                  <th><?php 
						if($this->lang->line('driver_ride_id') != '') echo stripslashes($this->lang->line('driver_ride_id')); else  echo 'Ride ID';
						?>  </th>
                  <th><?php 
						if($this->lang->line('driver_payment_date') != '') echo stripslashes($this->lang->line('driver_payment_date')); else  echo 'Date';
						?></th>
                  <th><?php 
						if($this->lang->line('dash_total_fare') != '') echo stripslashes($this->lang->line('dash_total_fare')); else  echo 'Total Fare';
						?></th>
                  <th><?php 
						if($this->lang->line('driver_earnings_tips') != '') echo stripslashes($this->lang->line('driver_earnings_tips')); else  echo 'Tips';
						?></th>
                  <th><?php 
						if($this->lang->line('dash_coupon_amount') != '') echo stripslashes($this->lang->line('dash_coupon_amount')); else  echo 'Coupon Amount';
						?></th>
                  <th><?php 
						if($this->lang->line('dash_amount_site') != '') echo stripslashes($this->lang->line('dash_amount_site')); else  echo 'Amount in Site';
						?></th>
                  <th><?php 
						if($this->lang->line('dash_amount_driver') != '') echo stripslashes($this->lang->line('dash_amount_driver')); else  echo 'Amount in Driver';
						?></th>
                  <th><?php 
						if($this->lang->line('dash_site_earnings') != '') echo stripslashes($this->lang->line('dash_site_earnings')); else  echo 'Site Earnings';
						?></th>
                  <th><?php 
						if($this->lang->line('dash_driver_earnings') != '') echo stripslashes($this->lang->line('dash_driver_earnings')); else  echo 'Driver Earnings';
						?></th>
               </tr>
            </thead>
            <tbody>
				<?php
				$i = 0; 
				$total_grand_fare = 0;
				$total_coupon_discount = 0;
				$total_amount_in_site = 0;
				$total_amount_in_driver = 0;
				$total_site_earnings = 0;
				$total_driver_earnings = 0;
				$total_driver_tips = 0;
				foreach ($rideList as $ride) {
					$i++;
					$amount_in_site = 0;
					$amount_in_driver = 0;
					$site_earnings = 0;
					$driver_earnings = 0;
					$pay_type = '';
					$tips_amount = 0;

					$amount_in_site = $ride['total']['wallet_usage'];
					if(isset($ride['total']['tips_amount'])) $tips_amount = $ride['total']['tips_amount'];
					if(isset($ride['amount_detail']['amount_in_site'])) $amount_in_site = $ride['amount_detail']['amount_in_site'];
					if(isset($ride['amount_detail']['amount_in_driver'])) $amount_in_driver = $ride['amount_detail']['amount_in_driver'];
					
					
					if (isset($ride['pay_summary']['type'])) $pay_type = $ride['pay_summary']['type'];
					if ($pay_type == '') $pay_type = 'FREE';
					
					$driver_earnings = $ride['driver_revenue'] + $tips_amount;
					?>
				   <tr  id="<?php echo $ride['ride_id']; ?>"  data-pickup="<?php echo $ride['booking_information']['pickup']['location']; ?>" data-drop="<?php echo $ride['booking_information']['drop']['location']; ?>" data-paytype="<?php echo get_language_value_for_keyword($pay_type,$this->data['langCode']); ?>" onclick="show_ride_info_popup('<?php echo $ride['ride_id']; ?>');">
				   
					  <td><?php echo $ride['ride_id']; ?></td>
					  <td><?php echo get_time_to_string("d-m-Y h:i A", MongoEPOCH($ride['booking_information']['pickup_date'])); ?></td>
					  <td><?php echo number_format($ride['total']['grand_fare'], 2); ?></td>
					  <td><?php  echo number_format($tips_amount, 2); ?></td>
					  <td><?php echo number_format($ride['total']['coupon_discount'], 2); ?></td>
					  <td><?php echo number_format($amount_in_site, 2); ?></td>
					  <td><?php echo number_format($amount_in_driver, 2); ?></td>
					  <td><?php echo number_format($ride['amount_commission'], 2); ?></td>
					  <td><?php echo number_format($driver_earnings, 2); ?></td>
				   </tr>
			   
					<?php
					$total_grand_fare+=$ride['total']['grand_fare'];
					$total_coupon_discount+=$ride['total']['coupon_discount'];
					$total_amount_in_site+=$amount_in_site;
					$total_amount_in_driver+=$amount_in_driver;
					$total_site_earnings+=$ride['amount_commission'];
					$total_driver_earnings+=$driver_earnings;
					if(isset($ride['total']['tips_amount'])) $total_driver_tips+=$ride['total']['tips_amount'];
                } ?>
			   
			   <tr class="last-total">
					<td colspan="2"><?php 
						if($this->lang->line('driver_trip_summary') != '') echo stripslashes($this->lang->line('driver_trip_summary')); else  echo 'Trip Summery';
						?> :</td>
					<td><?php echo $dcurrencySymbol.' '.number_format($total_grand_fare, 2); ?></td>
					<td><?php echo $dcurrencySymbol.' '.number_format($total_driver_tips, 2); ?></td>
					<td><?php echo $dcurrencySymbol.' '.number_format($total_coupon_discount, 2); ?></td>
					<td><?php echo $dcurrencySymbol.' '.number_format($total_amount_in_site, 2); ?></td>
					<td><?php echo $dcurrencySymbol.' '.number_format($total_amount_in_driver, 2); ?></td>
					<td><?php echo $dcurrencySymbol.' '.number_format($total_site_earnings, 2); ?></td>
					<td><?php echo $dcurrencySymbol.' '.number_format($total_driver_earnings, 2); ?></td>
				</tr>
			   
            </tbody>
         </table>
		 <?php } else {  ?>
				<div class="col-lg-12 col-md-6 col-sm-5 col-xs-12 back-one-step">
					<div class="back-earn" style="margin-bottom: 2%;">
					   <a href="driver/payments/display_payments"><button><?php 
						if($this->lang->line('driver_back_to_earnings') != '') echo stripslashes($this->lang->line('driver_back_to_earnings')); else  echo 'BACK TO EARNINGS';
						?> </button></a>
					</div>
				</div>
			  <h3 style="color: #a2a2a2;">***** <?php 
				if($this->lang->line('dash_no_trips_between_dates') != '') echo stripslashes($this->lang->line('dash_no_trips_between_dates')); else  echo 'No trips between this dates';
				?> ***** </h3>
		 <?php } ?>
      </div>
	  
		<?php if(!empty($bill_details) && !empty($rideList)){ ?>
		<div class="owner-detail">
			<div class="col-lg-6 col-md-6 col-sm-5 col-xs-12 back-one-step">
				<div class="back-earn">
				   <a href="driver/payments/display_payments"><button<?php 
						if($this->lang->line('driver_back_to_earnings') != '') echo stripslashes($this->lang->line('driver_back_to_earnings')); else  echo 'BACK TO EARNINGS';
						?></button></a>
				</div>
			</div>
			<div class="col-lg-5 col-md-5 col-sm-7 col-xs-12">
				<table class="table table-striped table-bordered table-own" cellspacing="0" width="100%">
				   <thead>
					  <tr>
						 <th><?php 
						if($this->lang->line('dash_site_Details') != '') echo stripslashes($this->lang->line('dash_site_Details')); else  echo 'Details';
						?></th>
						 <th><?php 
						if($this->lang->line('dash_site_owner') != '') echo stripslashes($this->lang->line('dash_site_owner')); else  echo 'Site Owner';
						?></th>
						 <th><?php 
						if($this->lang->line('driver_ride_driver') != '') echo stripslashes($this->lang->line('driver_ride_driver')); else  echo 'Driver';
						?></th>
					  </tr>
				   </thead>
				   <tbody>
					  <tr>
						 <td class="left-pay"><?php 
						if($this->lang->line('dash_payment_received') != '') echo stripslashes($this->lang->line('dash_payment_received')); else  echo 'Payment Received';
						?> </td>
						  <?php $payment_in_site = $total_coupon_discount + $total_amount_in_site; ?>
						 <td><?php echo $dcurrencySymbol.' '.number_format($payment_in_site, 2); ?></td>
						 <td><?php echo $dcurrencySymbol.' '.number_format($total_amount_in_driver, 2); ?></td>
					  </tr>
					  <tr>
						 <td class="left-pay"><?php 
						if($this->lang->line('dash_earnings') != '') echo stripslashes($this->lang->line('dash_earnings')); else  echo 'Earnings';
						?> </td>
						 <td><?php echo $dcurrencySymbol.' '.number_format($total_site_earnings, 2); ?></td>
						 <td><?php echo $dcurrencySymbol.' '.number_format($total_driver_earnings, 2); ?></td>
					  </tr>
					  <tr>
						 <td class="left-pay"><?php 
						if($this->lang->line('dash_payment_due') != '') echo stripslashes($this->lang->line('dash_payment_due')); else  echo 'Payment Due';
						?> </td>
						 <td><?php echo $dcurrencySymbol.' '.number_format($bill_details['site_pay_amount'], 2); ?></td>
						 <td><?php echo $dcurrencySymbol.' '.number_format($bill_details['driver_pay_amount'], 2); ?></td>
					  </tr>
					  <tr>
						 <td class="left-pay"><?php 
						if($this->lang->line('driver_status') != '') echo stripslashes($this->lang->line('driver_status')); else  echo 'Status';
						?></td>
						 <td style="font-weight:bold;">
							<?php if (isset($bill_details['site_need_to_pay'])) { ?>
							<?php if ($bill_details['site_need_to_pay']=='Yes') { ?>
								<?php if ($bill_details['site_paid']=='No') { ?>
									<p style="color:red;"><?php 
						if($this->lang->line('driver_pending_status') != '') echo stripslashes($this->lang->line('driver_pending_status')); else  echo 'Pending';
						?> </p>
								<?php }else{ ?>
									<p style="color:green;"><?php 
						if($this->lang->line('driver_paid_status') != '') echo stripslashes($this->lang->line('driver_paid_status')); else  echo 'Paid';
						?></p>
								<?php } ?>
							<?php }else{  echo '--'; } ?>
							<?php } ?>
						 </td>
						 <td style="font-weight:bold;">
							<?php if (isset($bill_details['driver_need_to_payment'])) { ?>
							<?php if ($bill_details['driver_need_to_payment']=='Yes') { ?>
							<?php if ($bill_details['driver_paid']=='No') { ?>
								<p style="color:red;"> <?php  if($this->lang->line('driver_pending_status') != '') echo stripslashes($this->lang->line('driver_pending_status')); else  echo 'Pending';
						?></p>
							<?php }else{ ?>
								<p style="color:green;"><?php 
						if($this->lang->line('driver_paid_status') != '') echo stripslashes($this->lang->line('driver_paid_status')); else  echo 'Paid';
						?></p>
							<?php } ?>
						<?php }else{ echo '--'; } ?>
						<?php } ?>
						 </td>
					  </tr>
				   </tbody>
				</table>
			</div>
		</div>
		<?php } ?>
	  
	  
   </div>
</section>


<!----------PAY POPUP-------------->

<div class="modal fade section-pay" id="payment-sec" role="dialog">
    <div class="modal-dialog">
    
      <!-- Modal content-->
      <div class="modal-content">
        <div class="modal-header">
          
        </div>
        <div class="modal-body">
          <div class="col-lg-6 col-md-6 col-sm-6 col-xs-12 pick-left">
				<h1><?php 
						if($this->lang->line('driver_pickup') != '') echo stripslashes($this->lang->line('driver_pickup')); else  echo 'PICKUP';
						?></h1>
				<img class="pcik-img" src="images/site/pick.png">
				<span id="pickup_addr"></span>
			</div>
			<div class="col-lg-6 col-md-6 col-sm-6 col-xs-12 pick-right">
				<h1><?php 
						if($this->lang->line('driver_payment_drop') != '') echo stripslashes($this->lang->line('driver_payment_drop')); else  echo 'DROP';
						?> </h1>
				<img class="pcik-img" src="images/site/drop.png">
				<span id="drop_addr"></span>
			</div>
			<div class="pay-foot">
				<p class="pay-foot-inner">
					<span><?php 
						if($this->lang->line('driver_payment_by') != '') echo stripslashes($this->lang->line('driver_payment_by')); else  echo 'Payment By';
						?>:</span>
					<span id="paytype"> <?php 
						if($this->lang->line('driver_cash') != '') echo stripslashes($this->lang->line('driver_cash')); else  echo 'Cash';
						?></span>
				</p>
			</div>
        </div>
		
		<span class="top-close" data-dismiss="modal"><img src="images/site/close-pop.png"></span>
        
      </div>
      
    </div>
  </div>


<!----------END PAY POPUP-------------->

<style>
	.left-pay {
		font-weight:bold;
	}
</style>

<script>
function show_ride_info_popup(rowNo){
	$('#pickup_addr').html($('#'+rowNo).attr('data-pickup'));
	$('#drop_addr').html($('#'+rowNo).attr('data-drop'));
	$('#paytype').html($('#'+rowNo).attr('data-paytype'));
	$('#payment-sec').modal('show');
}
</script>

<?php
$this->load->view('driver/templates/footer.php');
?>