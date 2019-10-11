<?php
$this->load->view(COMPANY_NAME.'/templates/header.php');

if($d_distance_unit=="km"){
	$d_distance_unit = get_language_value_for_keyword('km',$this->data['langCode']);
}else if($d_distance_unit=="mi"){
	$d_distance_unit = get_language_value_for_keyword('mi',$this->data['langCode']);
}
$rides_details = $rides_details->row();

?>
<div id="content" class="admin-settings edit-global-set parti_div rides_views">
		<div class="grid_container">
			<div class="grid_12">
				<div class="widget_wrap">
					<div class="widget_top">
						<span class="h_icon list"></span>
						<h6><?php echo $heading; ?></h6>
						<?php
                            if ($rides_details->ride_status == 'Booked' && ($allPrev == '1' || in_array('2', $rides))) {
                                ?>
                                <a href="<?php echo COMPANY_NAME;?>/rides/available_drivers_list/<?php echo $rides_details->_id; ?>" style="color:#fff" class="p_car p_edit tipTop export_report" title="<?php if ($this->lang->line('admin_rides_assign_cab') != '') echo   stripslashes($this->lang->line('admin_rides_assign_cab')); else echo 'Assign Cab'; ?>"><?php if ($this->lang->line('admin_rides_assign_cab') != '') echo stripslashes($this->lang->line('admin_rides_assign_cab')); else echo 'Assign Cab'; ?></a>
                                <?php
                            } 
                            ?>
                            
                            
						<div id="widget_tab">
			              <ul>
                            <?php if($rides_details->ride_status=="Completed"){ ?>
			                <li><a href="#tab1" class="active_tab"><?php if ($this->lang->line('admin_users_details') != '') echo stripslashes($this->lang->line('admin_users_details')); else echo 'Details'; ?></a></li> 
                            
							<li><a href="#tab2" class=""><?php if ($this->lang->line('admin_users_users_list_ratings') != '') echo stripslashes($this->lang->line('admin_users_users_list_ratings')); else echo 'Ratings'; ?></a></li>
							<?php } ?>
			              </ul>
			            </div>
						
					</div>
					<div class="widget_content drop_widget_sec">
					<?php
						$attributes = array('class' => 'form_container left_label booking-one');
						echo form_open(COMPANY_NAME.'/rides/display_rides_list',$attributes) 
					?> 		
	 				<?php if(isset($rides_details->currency)) $currency = $rides_details->currency; else  $currency='';
               #echo "<pre>";
               #print_r($rides_details);
               #exit;
               
               ?>
						<div id="tab1" class="first-section sec_first_padd">
						
						
							<div class="delivery_details_ride_new">
							
								<div class="view_ride_book_id">
									<label class="field_title">Ride Id</label>
									<div class="form_input booking_id_ride"><?php echo $rides_details->ride_id; ?></div> 
								</div>
												
								<div class="view_ride_book_id">
									<label class="field_title">Ride Type</label>
									<div class="form_input"><?php echo $rides_details->type; ?></div>
								</div>

								<div class="view_ride_book_id">
									<label class="field_title">Ride Status</label>
									<div class="form_input"><?php echo $rides_details->ride_status; ?></div> 
								</div>
								
								<div class="view_ride_book_id city_padding">
									<label class="field_title">City</label>
									<div class="form_input"><?php echo $rides_details->location['name']; ?></div>
								</div>
							</div>
						
						
						
						<div class="booking_view_ride ">
						<ul class="drop_locate_details drop_2 left-contsec booking">
							<li>
									<h2 class="padd_limit"><?php if ($this->lang->line('admin_rides_user_details') != '') echo stripslashes($this->lang->line('admin_rides_user_details')); else echo 'User Details'; ?></h2>
								 </li>
													
                                <li>
									<div class="form_grid_12">
										<label class="field_title"><?php if ($this->lang->line('admin_rides_name') != '') echo stripslashes($this->lang->line('admin_rides_name')); else echo 'Name'; ?></label>
										<div class="form_input">
										<?php if(isset($rides_details->user['name']))echo $rides_details->user['name']; else echo get_language_value_for_keyword('Not Available',$this->data['langCode']);  ?>
										</div>
									</div>
								</li> 

								<li>
									<div class="form_grid_12">
										<label class="field_title"><?php if ($this->lang->line('admin_rides_email') != '') echo stripslashes($this->lang->line('admin_rides_email')); else echo 'Email'; ?></label>
										<div class="form_input">
										<?php if($isDemo){ ?>
										<?php echo $dEmail; ?>
										<?php }  else{ ?>
										<?php if(isset($rides_details->user['email']))echo $rides_details->user['email']; else echo get_language_value_for_keyword('Not Available',$this->data['langCode']);  ?>
										<?php } ?>																			</div>
									</div>
								</li> 
								
								
								<li>
									<div class="form_grid_12">
										<label class="field_title"><?php if ($this->lang->line('admin_rides_mobile_number') != '') echo stripslashes($this->lang->line('admin_rides_mobile_number')); else echo 'Mobile Number'; ?></label>
										<div class="form_input">
										<?php if(isset($rides_details->user['phone']))echo $rides_details->user['phone']; else echo get_language_value_for_keyword('Not Available',$this->data['langCode']);  ?>
										
										</div>
									</div>
								</li>	
							</ul>
							
							<?php 
									if(isset($rides_details->driver['id'])){
										if($rides_details->driver['id'] != ''){
								 ?>
							<ul class="drop_locate_details drop_3 rite-contsec booking_new">
							
							<li>
							<h2>Driver Details</h2></li>
							
								<li>
									<div class="form_grid_12">
										<label class="field_title"><?php if ($this->lang->line('admin_rides_name') != '') echo stripslashes($this->lang->line('admin_rides_name')); else echo 'Name'; ?></label>
										<div class="form_input">
										<?php if(isset($rides_details->driver['name']))echo $rides_details->driver['name']; else echo get_language_value_for_keyword('Not Available',$this->data['langCode']);  ?>
										</div>
									</div>
								</li> 
													
                                <li>
									<div class="form_grid_12">
										<label class="field_title"><?php if ($this->lang->line('admin_rides_email') != '') echo stripslashes($this->lang->line('admin_rides_email')); else echo 'Email'; ?></label>
										<div class="form_input">
										<?php if($isDemo){ ?>
										<?php echo $dEmail; ?>
										<?php }  else { ?>
										<?php if(isset($rides_details->driver['email']))echo $rides_details->driver['email']; else echo get_language_value_for_keyword('Not Available',$this->data['langCode']);  ?>
										<?php } ?>
										</div>
									</div>
								</li> 
							<li>
									<div class="form_grid_12">
										<label class="field_title"><?php if ($this->lang->line('admin_rides_mobile_number') != '') echo stripslashes($this->lang->line('admin_rides_mobile_number')); else echo 'Mobile Number'; ?></label>
										<div class="form_input">
										<?php if(isset($rides_details->driver['phone']))echo $rides_details->driver['phone']; else echo get_language_value_for_keyword('Not Available',$this->data['langCode']);  ?>
										</div>
									</div>
								</li>
								<li>
									<div class="form_grid_12">
										<label class="field_title"><?php if ($this->lang->line('admin_drivers_vehicle_model') != '') echo stripslashes($this->lang->line('admin_drivers_vehicle_model')); else echo 'Vehicle Model'; ?></label>
										<div class="form_input">
										<?php if(isset($rides_details->driver['vehicle_model']))echo $rides_details->driver['vehicle_model']; else echo get_language_value_for_keyword('Not Available',$this->data['langCode']);  ?>
										</div>
									</div>
								</li>
								
								
								
								<li>
									<div class="form_grid_12">
										<label class="field_title"><?php if ($this->lang->line('admin_rides_vehicle_no') != '') echo stripslashes($this->lang->line('admin_rides_vehicle_no')); else echo 'Vehicle No'; ?></label>
										<div class="form_input">
										<?php if(isset($rides_details->driver['vehicle_no']))echo $rides_details->driver['vehicle_no']; else echo get_language_value_for_keyword('Not Available',$this->data['langCode']);  ?>
										</div>
									</div>
								</li>
								
								
							
							</ul>
							
							
								<?php 
									}
								}
								?>
							
							
							
							
							
							
							
							
						
						
						
						
							<ul class="drop_locate_details drop_2 left-contsec booking">
								<ul class="drop_1">		
								 <li>
									<h2 class="padd_limit"><?php if ($this->lang->line('admin_rides_ride_details') != '') echo stripslashes($this->lang->line('admin_rides_ride_details')); else echo 'Ride Details'; ?></h2>
								 </li>
                                <li>
									<div class="form_grid_12">
										<label class="field_title"><?php if ($this->lang->line('admin_rides_ride_id') != '') echo stripslashes($this->lang->line('admin_rides_ride_id')); else echo 'Ride Id'; ?></label>
										<div class="form_input">
										<?php if(isset($rides_details->ride_id)){ echo $rides_details->ride_id; } ?>
										</div>
									</div>
								</li> 
								<li>
									<div class="form_grid_12">
										<label class="field_title"><?php if ($this->lang->line('admin_rides_ride_type') != '') echo stripslashes($this->lang->line('admin_rides_ride_type')); else echo 'Ride Type'; ?></label>
										<div class="form_input">									
										<?php  
										if($rides_details->type == 'Now'){ echo get_language_value_for_keyword('Instant Ride',$this->data['langCode']); } else { echo get_language_value_for_keyword('Later Ride',$this->data['langCode']);}
										?>
										</div>
									</div>
								</li>
								
								<li>
									<div class="form_grid_12">
										<label class="field_title"><?php if ($this->lang->line('admin_rides_current_ride_status') != '') echo stripslashes($this->lang->line('admin_rides_current_ride_status')); else echo 'Ride Status'; ?></label>
										<div class="form_input">									
										<?php  
										if(isset($rides_details->ride_status)){
											$disp_rideStatus = get_language_value_for_keyword($rides_details->ride_status,$this->data['langCode']);
											$rideStatus = $rides_details->ride_status;
										}  else {
											$disp_rideStatus = get_language_value_for_keyword('Not Available',$this->data['langCode']); 
											$rideStatus = 'Not Available';
										}
										echo $disp_rideStatus;
										?>
										</div>
									</div>
								</li>
								<?php  
								if(isset($rides_details->ride_status) && $rides_details->ride_status=='Cancelled'){
								?>
								<li>
									<div class="form_grid_12">
										<label class="field_title"><?php if ($this->lang->line('admin_rides_cancelled_by') != '') echo stripslashes($this->lang->line('admin_rides_cancelled_by')); else echo 'Cancelled By'; ?></label>
										<div class="form_input">
										<?php  
										if(isset($rides_details->cancelled)){
											echo get_language_value_for_keyword(strtolower($rides_details->cancelled['primary']['by']),$this->data['langCode']); 
										}
										?>
										</div>
									</div>
								</li>
								<li>
									<div class="form_grid_12">
										<label class="field_title"><?php if ($this->lang->line('admin_rides_cancellation_reason') != '') echo stripslashes($this->lang->line('admin_rides_cancellation_reason')); else echo 'Cancellation Reason'; ?></label>
										<div class="form_input">	
										<?php  
											$cancel_reason = $rides_details->cancelled['primary']['text']; 
											if(isset($rides_details->cancelled['primary']['reason']) && $rides_details->cancelled['primary']['reason'] != '' )$cancel_reason = get_cancellation_reason_by_lang($rides_details->cancelled['primary']['reason'],$langCode);
											echo $cancel_reason;
										?>
										</div>
									</div>
								</li>
								<?php
								}
								?>
								
							
								<li>
									<div class="form_grid_12">
										<label class="field_title"><?php if ($this->lang->line('admin_location_and_fare_city') != '') echo stripslashes($this->lang->line('admin_location_and_fare_city')); else echo 'City'; ?></label>
										<div class="form_input">									
										<?php  
										if(isset($rides_details->location['name'])) echo $rides_details->location['name']; else echo get_language_value_for_keyword('Not Available',$this->data['langCode']); 
										?>
										</div>
									</div>
								</li>
								
								</ul>
								
							</ul>
							
							
							
							
							<ul class="drop_locate_details drop_2 rite-contsec booking_new">
							<?php 
									if($rideStatus == 'Completed' || $rideStatus == 'Finished'){
								?>
									<li>
										<h2><?php if ($this->lang->line('admin_rides_fare_summary') != '') echo stripslashes($this->lang->line('admin_rides_fare_summary')); else echo 'Fare Summary'; ?></h2>
									</li>
									
									
									
									<li>
										<div class="form_grid_12">
											<label class="field_title"><?php if ($this->lang->line('admin_rides_total_fare') != '') echo stripslashes($this->lang->line('admin_rides_total_fare')); else echo 'Total Fare'; ?></label>
											<div class="form_input">
											<?php
											$tips_amount = 0;
											$grand_fare = 0;
											if(isset($rides_details->total['tips_amount'])) {
												if($rides_details->total['tips_amount'] != '') {
													$tips_amount = $rides_details->total['tips_amount'];
												}
											}
											if(isset($rides_details->total['grand_fare'])) {
												if($rides_details->total['grand_fare'] != '') {
													$grand_fare = $rides_details->total['grand_fare'];
												}
											}
											$total_fare = $grand_fare+$tips_amount;
											echo $dcurrencySymbol.' '.number_format($total_fare,2);	
											?>
											</div>
										</div>
									</li>
									
									<?php  if(isset($rides_details->total['tips_amount'])) {} ?>
									
									<li>
										<div class="form_grid_12">
											<label class="field_title"><?php if ($this->lang->line('admin_rides_driver_tips_amount') != '') echo stripslashes($this->lang->line('admin_rides_driver_tips_amount')); else echo 'Driver Tips Amount'; ?></label>
											<div class="form_input">
											<?php
											echo $dcurrencySymbol.' '.number_format($tips_amount,2);	
											/* if(array_key_exists('tips_amount',$rides_details->total)) {
												if($rides_details->total['tips_amount'] != '') echo $dcurrencySymbol.' '.number_format($rides_details->total['tips_amount'],2); else get_language_value_for_keyword('Not Available',$this->data['langCode']); 
											} else {
												 get_language_value_for_keyword('Not Available',$this->data['langCode']); 
											} */	
											?>
											</div>
										</div>
									</li>
									
									<li>
										<div class="form_grid_12">
											<label class="field_title"><?php if ($this->lang->line('admin_rides_ride_fare') != '') echo stripslashes($this->lang->line('admin_rides_ride_fare')); else echo 'Ride Fare'; ?></label>
											<div class="form_input">
											<?php
											if(isset($rides_details->total['grand_fare'])) {
												if($rides_details->total['grand_fare'] != '') echo $dcurrencySymbol.' '.number_format($rides_details->total['grand_fare'],2); else echo get_language_value_for_keyword('Not Available',$this->data['langCode']); 
											} else {
												 echo get_language_value_for_keyword('Not Available',$this->data['langCode']); 
											}
													
											?>
											</div>
										</div>
									</li>
									
									
									<?php 
									$service_tax = 0;
									if(isset($rides_details->total['service_tax'])) $service_tax = $rides_details->total['service_tax'];
									if($service_tax != '' && $service_tax > 0){
									?>
									<li>
										<div class="form_grid_12">
											<label class="field_title"><?php if ($this->lang->line('admin_rides_service_tax') != '') echo stripslashes($this->lang->line('admin_rides_service_tax')); else echo 'Service Tax'; ?></label>
											<div class="form_input">
											<?php
												echo $dcurrencySymbol.' '.number_format($service_tax,2); 
											?>
											( <?php echo $rides_details->tax_breakup['service_tax']; ?>% )
											</div>
										</div>
									</li>
								<?php } ?>
								
								 <?php 
								$parking_charge = 0;
								if(isset($rides_details->total['parking_charge']) && $rides_details->total['parking_charge']>0) { 
								$parking_charge = $rides_details->total['parking_charge'];
								?>
									<li>
										<div class="form_grid_12">
											<label class="field_title"><?php if ($this->lang->line('dash_parking_charge') != '') echo stripslashes($this->lang->line('dash_parking_charge')); else echo 'Parking Charge'; ?></label>
											<div class="form_input">
											<?php
											if(isset($rides_details->total['parking_charge'])) {
												echo $dcurrencySymbol.' '.number_format($rides_details->total['parking_charge'],2); 
											} else {
												 echo get_language_value_for_keyword('Not Available',$this->data['langCode']); 
											}
											?>
											</div>
										</div>
									</li>
								<?php } ?>
								<?php 
								$toll_charge = 0;
								if(isset($rides_details->total['toll_charge']) && $rides_details->total['toll_charge']>0) { 
								$toll_charge = $rides_details->total['toll_charge'];
								?>
									<li>
										<div class="form_grid_12">
											<label class="field_title"><?php if ($this->lang->line('dash_toll_charge') != '') echo stripslashes($this->lang->line('dash_toll_charge')); else echo 'Toll Charge'; ?></label>
											<div class="form_input">
											<?php
											if(isset($rides_details->total['toll_charge'])) {
												echo $dcurrencySymbol.' '.number_format($rides_details->total['toll_charge'],2); 
											} else {
												 echo get_language_value_for_keyword('Not Available',$this->data['langCode']); 
											}
											?>
											</div>
										</div>
									</li>
								<?php } ?>
								
								<?php 
									if(isset($rides_details->total['coupon_discount'])) {
										$coupon_discount = $rides_details->total['coupon_discount']; 
									}else{ 
										$coupon_discount = 0; 
									}
									if($coupon_discount > 0){
										$coupon_type = $rides_details->coupon['type']; 
										$coupon_amount = $rides_details->coupon['amount']; 
									?>
									<li>
										<div class="form_grid_12">
											<label class="field_title"><?php if ($this->lang->line('admin_rides_coupon_discount') != '') echo stripslashes($this->lang->line('admin_rides_coupon_discount')); else echo 'Coupon discount'; ?></label>
											<div class="form_input">
											<?php echo $dcurrencySymbol.' '.number_format($coupon_discount,2); ?>
											( <?php if($coupon_type=="Percent") echo $coupon_amount.'%'; else echo $dcurrencySymbol.$coupon_amount; ?> )
											</div>
										</div>
									</li>
									<?php } ?>	
								
									<?php  
									if(isset($rides_details->amount_commission)) { 
										if($rides_details->amount_commission > 0){
									?>
									
									<li>
										<div class="form_grid_12">
											<label class="field_title"><?php if ($this->lang->line('admin_rides_commission_amount') != '') echo stripslashes($this->lang->line('admin_rides_commission_amount')); else echo 'Commission Amount'; ?></label>
											<div class="form_input">
											<?php											
											$amount_commission = 0;
											if(isset($rides_details->amount_commission)) {
												$amount_commission = floatval($rides_details->amount_commission);
												$amount_commission = $amount_commission - $service_tax;
											}
											if($amount_commission != '0') {
												echo $dcurrencySymbol.' '.number_format($amount_commission,2);
											} else {
												 get_language_value_for_keyword('Not Available',$this->data['langCode']); 
											}
											
											if(isset($rides_details->commission_percent)) {
												if($rides_details->commission_percent != '')  echo ' ('.$rides_details->commission_percent.'%)'; 
											} 
											
											?>
											</div>
										</div>
									</li>
									
									<?php } } ?>
									
								
									<li>
										<div class="form_grid_12">
											<label class="field_title"><?php if ($this->lang->line('admin_rides_driver_revenue') != '') echo stripslashes($this->lang->line('admin_rides_driver_revenue')); else echo 'Driver Earnings'; ?></label>
											<div class="form_input">
											<?php
												$driver_revenue = $rides_details->driver_revenue + $tips_amount;
												echo $dcurrencySymbol.' '.number_format($driver_revenue,2); 
											?>
											</div>
										</div>
									</li>
                                    <?php if(!empty($rides_details->ride_status) && $rides_details->ride_status=="Completed"){ ?>
                                    <li>
										<div class="form_grid_12">
											<label class="field_title"><?php if ($this->lang->line('admin_rides_Method') != '') echo stripslashes($this->lang->line('admin_rides_Method')); else echo 'Payment Method'; ?></label>
											<div class="form_input">
											<?php
												$pay_summary = $rides_details->pay_summary['type'];
												echo $pay_summary; 
											?>
											</div>
										</div>
									</li>
								<?php } ?>
									<?php /* 
									<li>
										<div class="form_grid_12">
											<label class="field_title"><?php if ($this->lang->line('admin_rides_total_distance') != '') echo stripslashes($this->lang->line('admin_rides_total_distance')); else echo 'Total Distance'; ?></label>
											<div class="form_input">
											<?php
											if(isset($rides_details->summary['ride_distance'])) { 
												$totRidedistance = $rides_details->summary['ride_distance'];
												echo number_format($rides_details->summary['ride_distance'],1).' '.$d_distance_unit; 
											} else {
												 get_language_value_for_keyword('Not Available',$this->data['langCode']); 
											}
											?>
											</div>
										</div>
									</li>
									
										
									<li>
										<div class="form_grid_12">
											<label class="field_title"><?php if ($this->lang->line('admin_rides_total_riding_time') != '') echo stripslashes($this->lang->line('admin_rides_total_riding_time')); else echo 'Total Riding Time'; ?></label>
											<div class="form_input">
											<?php if ($this->lang->line('rides_min') != '') $rides_min = stripslashes($this->lang->line('rides_min')); else $rides_min = 'min'; ?>
											<?php
											if(isset($rides_details->summary['ride_duration'])) {
												$totRidetime = $rides_details->summary['ride_duration'];
												echo $rides_details->summary['ride_duration'].' '.$rides_min; 
											} else {
												 get_language_value_for_keyword('Not Available',$this->data['langCode']); 
											}
											?>
											</div>
										</div>
									</li>
									
									<li>
										<div class="form_grid_12">
											<label class="field_title"><?php if ($this->lang->line('admin_rides_total_waiting_time') != '') echo stripslashes($this->lang->line('admin_rides_total_waiting_time')); else echo 'Total Waiting Time'; ?></label>
											<div class="form_input">
											<?php
											if(isset($rides_details->summary['waiting_duration'])) {
												echo $rides_details->summary['waiting_duration'].' '.$rides_min; 
											} else {
												 get_language_value_for_keyword('Not Available',$this->data['langCode']); 
											}
											?>
											</div>
										</div>
									</li> */ ?>
									
								                               
							
							</ul>
							
							<ul class="drop_locate_details drop_2 left-contsec booking_new">
							<?php if(!isset($rides_details->pool_ride)){ ?>
                                    
									<li>
										<h2><?php if ($this->lang->line('admin_rides_fare_details') != '') echo stripslashes($this->lang->line('admin_rides_fare_details')); else echo 'Fare Details'; ?></h2>
									</li>
									
									<li>
									<?php
											if(isset($rides_details->fare_breakup['min_km'])) $min_km = $rides_details->fare_breakup['min_km']; else $min_km = 0; ?>
										<div class="form_grid_12">
											<label class="field_title"><?php if ($this->lang->line('admin_rides_base_fare_for') != '') echo stripslashes($this->lang->line('admin_rides_base_fare_for')); else echo 'Base fare for'; ?> <?php echo $min_km;?>  <?php echo $d_distance_unit;?></label>
											<div class="form_input">
											<?php
											if(isset($rides_details->total['base_fare'])) {
												echo $dcurrencySymbol.' '.number_format($rides_details->total['base_fare'],2);
											} else {
												 get_language_value_for_keyword('Not Available',$this->data['langCode']); 
											}
											?>
											</div>
										</div>
									</li>
									
									<li>
										<div class="form_grid_12">
											<?php $baseDistance = 0;
												if(isset($rides_details->fare_breakup['min_km'])){
													$baseDistance = $rides_details->fare_breakup['min_km'];
												}
												$totRidedistance = $rides_details->summary['ride_distance'];
												$remainDistancetocharge = $totRidedistance - $baseDistance;
												if($remainDistancetocharge < 0){
													$remainDistancetocharge = 0;
												}
											?>
											<label class="field_title"><?php if ($this->lang->line('admin_rides_rate_for') != '') echo stripslashes($this->lang->line('admin_rides_rate_for')); else echo 'Rate for'; ?> <?php echo number_format($remainDistancetocharge,1);?> <?php echo $d_distance_unit;?></label>
											<div class="form_input">
											<?php
											if(isset($rides_details->total['distance'])) {
												echo $dcurrencySymbol.' '.number_format($rides_details->total['distance'],2); 
											} else {
												 get_language_value_for_keyword('Not Available',$this->data['langCode']); 
											}
											?>
											</div>
										</div>
									</li>
									
									<li>
										<div class="form_grid_12">
										<?php $freerideTime = 0;
										if(isset($rides_details->fare_breakup['min_time'])){
											$freerideTime = $rides_details->fare_breakup['min_time'];
										}
										?>
											<label class="field_title"><?php if ($this->lang->line('admin_rides_free_ride_time') != '') echo stripslashes($this->lang->line('admin_rides_free_ride_time')); else echo 'Free ride time'; ?> (<?php echo $freerideTime; ?> <?php if ($this->lang->line('rides_min') != '') echo stripslashes($this->lang->line('rides_min')); else echo 'min'; ?> )</label>
											<div class="form_input">
											<?php
											if(isset($rides_details->total['free_ride_time'])) {
												echo $dcurrencySymbol.' '.number_format($rides_details->total['free_ride_time'],2); 
											} else {
												 get_language_value_for_keyword('Not Available',$this->data['langCode']); 
											}
											?>
											</div>
										</div>
									</li>
									
									<li>
										<div class="form_grid_12">
											<?php $baseTime = 0;
												if(isset($rides_details->fare_breakup['min_time'])){
													$baseTime = $rides_details->fare_breakup['min_time'];
												}
												$totRidetime = $rides_details->summary['ride_duration'];
												$remainTimetocharge = $totRidetime - $baseTime;
												if($remainTimetocharge < 0){
													$remainTimetocharge = 0;
												}
											?>
											<label class="field_title"><?php if ($this->lang->line('admin_rides_rate_for') != '') echo stripslashes($this->lang->line('admin_rides_rate_for')); else echo 'Rate for'; ?> <?php echo $remainTimetocharge;?> <?php if ($this->lang->line('rides_min') != '') echo stripslashes($this->lang->line('rides_min')); else echo 'min'; ?></label>
											<div class="form_input">
											<?php
											if(isset($rides_details->total['ride_time'])) {
												echo $dcurrencySymbol.' '.number_format($rides_details->total['ride_time'],2); 
											} else {
												 get_language_value_for_keyword('Not Available',$this->data['langCode']); 
											}
											?>
											</div>
										</div>
									</li>
									
									
								
								<?php 
									if(isset($rides_details->fare_breakup['wait_per_minute'])) $wait_per_minute = $rides_details->fare_breakup['wait_per_minute'];
									if($wait_per_minute != '' && $wait_per_minute > 0){
									?>
									<li>
										<div class="form_grid_12">
											<label class="field_title"><?php if ($this->lang->line('driver_wait_time_charges') != '') echo stripslashes($this->lang->line('driver_wait_time_charges')); else echo 'Waiting time charge'; ?> ( <?php echo $dcurrencySymbol.' '.number_format($wait_per_minute,2);?> <?php if ($this->lang->line('ride_per_min') != '') echo stripslashes($this->lang->line('ride_per_min')); else echo 'per min'; ?> )</label>
											<div class="form_input">
											<?php
											if(isset($rides_details->total['wait_time'])) {
												echo $dcurrencySymbol.' '.number_format($rides_details->total['wait_time'],2); 
											} else {
												 get_language_value_for_keyword('Not Available',$this->data['langCode']); 
											}
											?>
											</div>
										</div>
									</li>
								<?php } ?>
								
									<li>
										<div class="form_grid_12">
											<label class="field_title"><?php if ($this->lang->line('admin_rides_total_fare') != '') echo stripslashes($this->lang->line('admin_rides_total_fare')); else echo 'Total Fare'; ?></label>
											<div class="form_input">
											<?php
											if(isset($rides_details->total['total_fare'])) {
												echo $dcurrencySymbol.' '.number_format($rides_details->total['total_fare'],2); 
											} else {
												 get_language_value_for_keyword('Not Available',$this->data['langCode']); 
											}
											?>
											</div>
										</div>
									</li>
									
									<?php 
									$peak_time_charge = "";
									$night_charge = "";
									$surge_fare=0;
									if(isset($rides_details->fare_breakup['peak_time_charge'])) $peak_time_charge = $rides_details->fare_breakup['peak_time_charge'];
									if(isset($rides_details->fare_breakup['night_charge'])) $night_charge = $rides_details->fare_breakup['night_charge'];
									if(($night_charge != '' && $night_charge > 0) && ($peak_time_charge != '' && $peak_time_charge >0)){
										$total_fare = $rides_details->total['total_fare'];
										$surge_value = $peak_time_charge+$night_charge;
										$surge_fare = $rides_details->total['peak_time_charge']+$rides_details->total['night_time_charge'];
									?>
									<li>
										<div class="form_grid_12">
											<label class="field_title"><?php if ($this->lang->line('driver_peak_time_and_night_charge') != '') echo stripslashes($this->lang->line('driver_peak_time_and_night_charge')); else echo 'Peak time and Night charges'; ?> ( <?php echo $peak_time_charge+$night_charge;?> <span style="font-size:8px;">x</span> )</label>
											<div class="form_input">
											<?php
											if(isset($surge_fare)) {
												echo $dcurrencySymbol.' '.number_format($surge_fare,2); 
											} else {
												 get_language_value_for_keyword('Not Available',$this->data['langCode']); 
											}
											?>
											( <?php echo $total_fare.' * '.$surge_value.' - '.$total_fare.' = '.$surge_fare; ?> )
											</div>
										</div>
									</li>
									
									<?php }else{ ?>
									
									<?php 
									if(isset($rides_details->fare_breakup['peak_time_charge'])) $peak_time_charge = $rides_details->fare_breakup['peak_time_charge'];
									if($peak_time_charge != '' && $peak_time_charge > 0){
										$total_fare = $rides_details->total['total_fare'];
										$surge_value = $peak_time_charge;
										$surge_fare = $rides_details->total['peak_time_charge'];
									?>
									<li>
										<div class="form_grid_12">
											<label class="field_title"><?php if ($this->lang->line('driver_peak_time_charge') != '') echo stripslashes($this->lang->line('driver_peak_time_charge')); else echo 'Peak time charges'; ?> ( <?php echo $peak_time_charge;?> <span style="font-size:8px;">x</span> )</label>
											<div class="form_input">
											<?php
											if(isset($rides_details->total['peak_time_charge'])) {
												echo $dcurrencySymbol.' '.number_format($rides_details->total['peak_time_charge'],2); 
											} else {
												 get_language_value_for_keyword('Not Available',$this->data['langCode']); 
											}
											?>
											( <?php echo $total_fare.' * '.$surge_value.' - '.$total_fare.' = '.$surge_fare; ?> )
											</div>
										</div>
									</li>
								<?php } ?>
								
								<?php 
									if(isset($rides_details->fare_breakup['night_charge'])) $night_charge = $rides_details->fare_breakup['night_charge'];
									if($night_charge != '' && $night_charge > 0){
										$total_fare = $rides_details->total['total_fare'];
										$surge_value = $night_charge;
										$surge_fare = $rides_details->total['night_time_charge'];
									?>
									<li>
										<div class="form_grid_12">
											<label class="field_title"><?php if ($this->lang->line('dash_night_time_charge') != '') echo stripslashes($this->lang->line('dash_night_time_charge')); else echo 'Night time charge'; ?> ( <?php echo $night_charge;?> <span style="font-size:8px;">x</span> )</label>
											<div class="form_input">
											<?php
											if(isset($rides_details->total['night_time_charge'])) {
												echo $dcurrencySymbol.' '.number_format($rides_details->total['night_time_charge'],2); 
											} else {
												 get_language_value_for_keyword('Not Available',$this->data['langCode']); 
											}
											?>
											( <?php echo $total_fare.' * '.$surge_value.' - '.$total_fare.' = '.$surge_fare; ?> )
											</div>
										</div>
									</li>
								<?php } ?>
								
									<?php } ?>
									
									
									
									
									<li>
										<div class="form_grid_12">
											<label class="field_title"><?php if ($this->lang->line('admin_rides_sub_total') != '') echo stripslashes($this->lang->line('admin_rides_sub_total')); else echo 'Sub Total'; ?></label>
											<div class="form_input">
											<?php
											if(isset($rides_details->total['total_fare'])) {
												echo $dcurrencySymbol.' '.number_format($rides_details->total['total_fare']+$surge_fare,2); 
											} else {
												 get_language_value_for_keyword('Not Available',$this->data['langCode']); 
											}
											?>
											</div>
										</div>
									</li>

								<?php 
									}
								?>
                                <?php } ?>
							
							</ul>
							
							
							
							
						
							
							
							
							
							<ul class="drop_locate_details drop_3 left-contsec booking_new">
								 <li>
									<h2 class="padd_limit"><?php if ($this->lang->line('admin_rides_booking_details') != '') echo stripslashes($this->lang->line('admin_rides_booking_details')); else echo 'Booking Details'; ?></h2>
								 </li>
								 
								 <li>
									<div class="form_grid_12">
										<label class="field_title"><?php if ($this->lang->line('admin_rides_service_type') != '') echo stripslashes($this->lang->line('admin_rides_service_type')); else echo 'Service Type'; ?></label>
										<div class="form_input">
										<?php 
										if(isset($rides_details->pool_ride) && $rides_details->pool_ride=="Yes"){
											$disp_cat = (string)$rides_details->booking_information['service_type'];
										}else{
											  if(isset($rides_details->booking_information['service_type'])) {
												$lng_cat_name =  get_category_name_by_lang($rides_details->booking_information['service_id'],$this->data['langCode']);  
												if($lng_cat_name != '') $disp_cat = $lng_cat_name; else $disp_cat = $rides_details->booking_information['service_type'];
											  } else { 
												$disp_cat = get_language_value_for_keyword('Not Available',$this->data['langCode']); 
											  } 
										}
										echo $disp_cat;
										?> 
										</div>
									</div>
								</li>
								
								<li>
									<div class="form_grid_12">
										<label class="field_title"><?php if ($this->lang->line('admin_rides_booking_date') != '') echo stripslashes($this->lang->line('admin_rides_booking_date')); else echo 'Booking Date'; ?></label>
										<div class="form_input">
										<?php
										if(isset($rides_details->booking_information['booking_date']))
										$bookDateSec = MongoEPOCH($rides_details->booking_information['booking_date']); else $bookDateSec='';
										
										if($bookDateSec != '') echo get_time_to_string('d,M-Y h:i A',$bookDateSec); else echo get_language_value_for_keyword('Not Available',$this->data['langCode']);
										?>
										</div>
									</div>
								</li>
								
								<li>
									<div class="form_grid_12">
										<label class="field_title"><?php if ($this->lang->line('admin_rides_pickup_date') != '') echo stripslashes($this->lang->line('admin_rides_pickup_date')); else echo 'Pickup Date'; ?></label>
										<div class="form_input">
										<?php
										if(isset($rides_details->booking_information['pickup_date']))
										$pickDateSec = MongoEPOCH($rides_details->booking_information['pickup_date']); else $pickDateSec='';
										
										if($pickDateSec != '') echo get_time_to_string('d,M-Y h:i A',$pickDateSec); else echo get_language_value_for_keyword('Not Available',$this->data['langCode']);
										?>
										</div>
									</div>
								</li>
								
								<li>
									<div class="form_grid_12">
										<label class="field_title"><?php if ($this->lang->line('admin_rides_estimated_pickup') != '') echo stripslashes($this->lang->line('admin_rides_estimated_pickup')); else echo 'Estimated Pickup Date'; ?></label>
										<div class="form_input">
										<?php
										if(isset($rides_details->booking_information['est_pickup_date']))
										$estpickDateSec = MongoEPOCH($rides_details->booking_information['est_pickup_date']); else $estpickDateSec='';
										
										if($estpickDateSec != '') echo get_time_to_string('d,M-Y h:i A',$estpickDateSec); else echo get_language_value_for_keyword('Not Available',$this->data['langCode']);
										?>
										</div>
									</div>
								</li>
								
								<?php 
									if($rideStatus == 'Completed' || $rideStatus == 'Finished'){
								?>
								<li>
									<div class="form_grid_12">
										<label class="field_title"><?php if ($this->lang->line('admin_rides_estimated_drop') != '') echo stripslashes($this->lang->line('admin_rides_estimated_drop')); else echo 'Drop Date'; ?></label>
										<div class="form_input">
										<?php
										if(isset($rides_details->history['end_ride']))
										$estdropDateSec = MongoEPOCH($rides_details->history['end_ride']); else $estdropDateSec='';
										
										if($estdropDateSec != '') echo get_time_to_string('d,M-Y h:i A',$estdropDateSec); else echo get_language_value_for_keyword('Not Available',$this->data['langCode']);
										?>
										</div>
									</div>
								</li>
								<?php 
									}
									?>
								
								<li>
									<div class="form_grid_12">
										<label class="field_title"><?php if ($this->lang->line('admin_rides_booking_email_id') != '') echo stripslashes($this->lang->line('admin_rides_booking_email_id')); else echo 'Booking Email Id'; ?></label>
										<div class="form_input">
										<?php if(isset($rides_details->booking_information['booking_email'])) echo $rides_details->booking_information['booking_email']; else echo get_language_value_for_keyword('Not Available',$this->data['langCode']);  ?>
										</div>
									</div>
								</li>

								<li>
									<div class="form_grid_12">
										<label class="field_title"><?php if ($this->lang->line('admin_rides_pickup_location') != '') echo stripslashes($this->lang->line('admin_rides_pickup_location')); else echo 'Pickup Location'; ?></label>
										<div class="form_input">
										<?php 
												if(isset($rides_details->booking_information['pickup']['location'])){ 
													if($rides_details->booking_information['pickup']['location'] != '') 
													echo $rides_details->booking_information['pickup']['location']; else echo get_language_value_for_keyword('Not Available',$this->data['langCode']); 
												} else {
												echo get_language_value_for_keyword('Not Available',$this->data['langCode']); 
												}
												?>
										</div>
									</div>
								</li>
								
								<li>
									<div class="form_grid_12">
										<label class="field_title"><?php if ($this->lang->line('admin_rides_drop_location') != '') echo stripslashes($this->lang->line('admin_rides_drop_location')); else echo 'Drop Location'; ?></label>
										<div class="form_input">
										<?php
												if(isset($rides_details->booking_information['drop']['location'])){ 
													if($rides_details->booking_information['drop']['location'] != '') 
													echo $rides_details->booking_information['drop']['location']; else echo get_language_value_for_keyword('Not Available',$this->data['langCode']); 
												} else {
												echo get_language_value_for_keyword('Not Available',$this->data['langCode']); 
												}
												?>
										</div>
									</div>
								</li>
								
							</ul>
							
							<ul class="last-sec-btn">
							<li class="change-pass">
									<div class="form_grid_12">
										<div class="form_input">
											<a class="tipLeft" href="<?php  if(isset($_SERVER['HTTP_REFERER'])) echo $_SERVER['HTTP_REFERER']; else echo COMPANY_NAME.'/rides/display_rides';?>" original-title="<?php if ($this->lang->line('dash_go_rides_list') != '') echo stripslashes($this->lang->line('dash_go_rides_list')); else echo 'Go to rides list'; ?>">
												<span class="badge_style b_done"><?php if ($this->lang->line('admin_rides_back') != '') echo stripslashes($this->lang->line('admin_rides_back')); else echo 'Back'; ?></span>
											</a>
										</div>
									</div>
								</li>
							</ul>
						
							</div>
							</div>
							
							<?php if($rides_details->ride_status=="Completed"){ ?>
							<div id="tab2" class="tab_sec_second">
								<ul>
								
								
							<?php 
						
            if ($rides_details->ride_status != 'Expired') {
                                    
						if(isset($rides_details->ratings)){
							if(count($rides_details->ratings) > 0){
								foreach($rides_details->ratings as $holder => $reviewsHolder) { ?>	
									<li>	
										<h2><?php if ($this->lang->line('admin_rides_rating_for') != '') echo stripslashes($this->lang->line('admin_rides_rating_for')); else echo 'Ratings for'; ?> <?php echo $holder; ?> 
													<?php 
													if(isset($reviewsHolder['avg_rating']))  $avg_rating = number_format($reviewsHolder['avg_rating'],2); else $avg_rating = 0; 
													?>
													
													<div class="star str" id="star-pos<?php echo $holder;?>"  data-star="<?php echo $avg_rating;?>" style="width: 200px;float:none;"></div>
													<span>( <?php echo $avg_rating;?> )</span>
										</h2>
									</li>
									<?php 
									foreach($reviewsHolder['ratings'] as $reviews) { 
												?>	
										
											<li>
												<div class="form_grid_12">
													<label class="field_title"><?php echo $reviews['option_title']; ?></label>
													<div class="form_input">
														<?php if(isset($reviews['rating']))  $rating = number_format($reviews['rating'],2); else $rating = 0; ?>
														<div class="star" id="star-pos<?php echo $reviews['option_id'];?>" data-star="<?php echo $rating;?>"></div>
														<span>&nbsp;</span>
														<span class="starRatings-count">( <?php echo $rating;?> )</span>
													</div>
												</div>
											</li>
												<?php 
									}
									?>
									<li>
										<div class="form_grid_12">
											<label class="field_title"><?php if ($this->lang->line('admin_rides_comments') != '') echo stripslashes($this->lang->line('admin_rides_comments')); else echo 'Comments'; ?></label>
											<div class="form_input">
												<?php 
												$comments = "N/A";
												if(array_key_exists("comments",$reviewsHolder)){
														if($reviewsHolder['comments']!=""){
															$comments = $reviewsHolder['comments'];
														}
												}
												echo $comments;
												?>
											</div>
										</div>
									</li>
									<?php
								}
							} else {
							?>
							
								<li>	
									<h2><?php if ($this->lang->line('admin_rides_no_records_found') != '') echo stripslashes($this->lang->line('admin_rides_no_records_found')); else echo 'No records found for this'; ?> <?php $reviews[0]['option_holder']; ?></h2>
								</li>
							
							<?php } 
						} else {
						?>
						
							<li>	
								<h2><?php if ($this->lang->line('admin_rides_no_submitted_ratings') != '') echo stripslashes($this->lang->line('admin_rides_no_submitted_ratings')); else echo 'Not submitted any ratings for this rides'; ?></h2>
							</li>
						
						<?php } ?>
							
						</ul>
						
						<ul class="last-sec-btn">
							<li class="change-pass">
								<div class="form_grid_12">
									<div class="form_input">
										<a href="javascript:void(0);" onclick="javascript: window.history.go(-1);" class="tipLeft" title="<?php if ($this->lang->line('dash_go_rides_list') != '') echo stripslashes($this->lang->line('dash_go_rides_list')); else echo 'Go to rides list'; ?>"><span class="badge_style b_done"><?php if ($this->lang->line('admin_rides_back') != '') echo stripslashes($this->lang->line('admin_rides_back')); else echo 'Back'; ?></span></a>
									</div>
								</div>
							</li>
						
						</ul>
						<?php } ?>
						
							</div>
							<?php } ?>
							
						</form>
					</div>
				</div>
			</div>
		</div>
		<span class="clear"></span>
</div>
</div>

</div>
<style>
.starRatings-count {
	float: right;
    margin-right: 77%;
    margin-top: -21px;
}

#tab2 h2 {
    border: 1px solid grey;
    border-radius: 8px;
	background-color: #a7a9ac;
}
.str,.star{
	width:200px !important;
}

a.export_report {
    height: 26px !important;
    font-size: 12px !important;
}
</style>

<?php 
$this->load->view(COMPANY_NAME.'/templates/footer.php');
?>