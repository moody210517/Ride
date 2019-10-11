
						<table class="custom-table">
							<thead>
								<tr>
									<th>Sl No</th>
									<th>Ride Id</th>
									<th>Date</th>
									<th>Payment Type</th>
									<th>Total Fare</th>
									<th>Coupon Amount</th>
									<th>Amount in Site</th>
									<th>Amount in Driver</th>
									<th>Site Earnings</th>
									<th>Driver Earnings</th>
								</tr>
							</thead>
								<?php $i=0; ?>
								<?php
								$total_grand_fare=0;
								$total_coupon_discount=0;
								$total_amount_in_site=0;
								$total_amount_in_driver=0;
								$total_site_earnings=0;
								$total_driver_earnings=0;
								?>							
							<tbody>
								<?php foreach($rideList as $ride){ $i++; ?>
								<?php 
								$amount_in_site=0;
								$amount_in_driver=0;
								$site_earnings=0;
								$driver_earnings=0;
								$pay_type='';
								
								$amount_in_site=$ride['total']['wallet_usage'];
								if(isset($ride['pay_summary']['type'])){
									$pay_type=$ride['pay_summary']['type'];
								}
								if($pay_type==''){
									$pay_type='FREE';
								}
								$siteArry=array('Gateway','Wallet_Gateway');
								$driverArry=array('Cash','Wallet_Cash');
								if(in_array($pay_type,$siteArry)){
									$amount_in_site=$ride['total']['paid_amount'];
								}
								if(in_array($pay_type,$driverArry)){
									$amount_in_driver=$ride['total']['paid_amount'];
								}
								
								?>
								<tr>
									<td><?php echo $i; ?></td>
									<td><?php echo $ride['ride_id']; ?></td>
									<td><?php echo get_time_to_string("d-m-Y h:i A",MongoEPOCH($ride['booking_information']['pickup_date'])); ?></td>
									<td><?php echo $pay_type; ?></td>
									<td>
										<span class="amt-right"><?php echo number_format($ride['total']['grand_fare'],2); ?></span>
										<span class="admin-currency">&nbsp;<?php #echo $dcurrencySymbol; ?>&nbsp;</span>
									</td>
									<td>
										<span class="amt-right"><?php echo number_format($ride['total']['coupon_discount'],2); ?></span>
									</td>
									<td>
										<span class="amt-right"><?php echo number_format($amount_in_site,2); ?></span>
									</td>
									<td>
										<span class="amt-right"><?php echo number_format($amount_in_driver,2); ?></span>
									</td>
									<td>
										<span class="amt-right"><?php echo number_format($ride['amount_commission'],2); ?></span>
									</td>
									<td>
										<span class="amt-right"><?php echo number_format($ride['driver_revenue'],2); ?></span>
									</td>
								</tr>
								<?php 
								$total_grand_fare+=$ride['total']['grand_fare'];
								$total_coupon_discount+=$ride['total']['coupon_discount'];
								$total_amount_in_site+=$amount_in_site;
								$total_amount_in_driver+=$amount_in_driver;
								$total_site_earnings+=$ride['amount_commission'];
								$total_driver_earnings+=$ride['driver_revenue'];
								?>
								<?php } ?>
								</tbody>
								<tfoot>
									<tr>
										<th colspan='4'> Trip Summary: </th>
										<th>
											<span class="amt-right"><?php echo number_format($total_grand_fare,2); ?></span>
											<span class="admin-currency">&nbsp;<?php echo $dcurrencySymbol; ?>&nbsp;</span>
										</th>
										<th>
											<span class="amt-right"><?php echo number_format($total_coupon_discount,2); ?></span>
											<span class="admin-currency">&nbsp;<?php echo $dcurrencySymbol; ?>&nbsp;</span>
										</th>
										<th>
											<span class="amt-right"><?php echo number_format($total_amount_in_site,2); ?></span>
											<span class="admin-currency">&nbsp;<?php echo $dcurrencySymbol; ?>&nbsp;</span>
										</th>
										<th>
											<span class="amt-right"><?php echo number_format($total_amount_in_driver,2); ?></span>
											<span class="admin-currency">&nbsp;<?php echo $dcurrencySymbol; ?>&nbsp;</span>
										</th>
										<th>
											<span class="amt-right"><?php echo number_format($total_site_earnings,2); ?></span>
											<span class="admin-currency">&nbsp;<?php echo $dcurrencySymbol; ?>&nbsp;</span>
										</th>
										<th>
											<span class="amt-right"><?php echo number_format($total_driver_earnings,2); ?></span>
											<span class="admin-currency">&nbsp;<?php echo $dcurrencySymbol; ?>&nbsp;</span>
										</th>
									</tr>
								</tfoot>
						</table>