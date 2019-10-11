<?php
$this->load->view('site/templates/profile_header');
$findpage = $this->uri->segment(2);
$rides_details = $rides_details->row(); 
$longitude = $rides_details->booking_information['pickup']['latlong']['lon'];
$latitude = $rides_details->booking_information['pickup']['latlong']['lat'];
$loc_key = 'lat_' . str_replace('.', '-', $longitude) . 'lon_' . str_replace('.', '-', $latitude);

if($d_distance_unit=="km"){
	$d_distance_unit = get_language_value_for_keyword('km',$this->data['langCode']);
}else if($d_distance_unit=="mi"){
	$d_distance_unit = get_language_value_for_keyword('mi',$this->data['langCode']);
}

$fav = 'No'; 
if(isset($favouriteList->row()->fav_location[$loc_key])) $fav = 'Yes';

$bookinTime = '';
if(isset($rides_details->booking_information['booking_date'])){
	$bookinTime = MongoEPOCH($rides_details->booking_information['booking_date']);
}

$dropinTime = '';
if(isset($rides_details->booking_information['drop_date'])){
	$dropinTime = MongoEPOCH($rides_details->booking_information['drop_date']);
}		

$pickupTime = '';
if(isset($rides_details->booking_information['pickup_date'])){
	$pickupTime = MongoEPOCH($rides_details->booking_information['pickup_date']);
}			

$location_lon = '';
if(isset($rides_details->booking_information['pickup']['latlong']['lon'])){
	$location_lon = $rides_details->booking_information['pickup']['latlong']['lon'];
}

$location_lat = '';
if(isset($rides_details->booking_information['pickup']['latlong']['lat'])){
	$location_lat = $rides_details->booking_information['pickup']['latlong']['lat'];
}

$location = '';
if(isset($rides_details->booking_information['pickup']['location'])){
	$location = $rides_details->booking_information['pickup']['location'];
}

$pickup_location = $location;

?> 


<section class="profile_pic_sec row">
   <div  class="profile_login_cont">
     	<!--------------  Load Profile Side Bar ------------------------>
		<?php    
			$this->load->view('site/templates/profile_sidebar'); 
		?>
		
      <div class="share_detail">
         <div class="share_det_title">
            <h2><span><?php if ($this->lang->line('rider_profile_my_rides') != '') echo stripslashes($this->lang->line('rider_profile_my_rides')); else echo 'My Rides'; ?></span></h2>
         </div>
         <div class="invoice_header">
            <div class="invoice_title">
               <h1><?php if ($this->lang->line('rider_ride_crn') != '') echo stripslashes($this->lang->line('rider_ride_crn')); else echo 'CRN'; ?>  #<?php echo $rides_details->ride_id;?></h1>
			   <?php 
				  if(isset($rides_details->booking_information['service_type'])) {
					$lng_cat_name =  get_category_name_by_lang($rides_details->booking_information['service_id'],$this->data['langCode']);  
					if($lng_cat_name != '') $car_type = $lng_cat_name; else echo $rides_details->booking_information['service_type'];
				  } else { 
					$car_type = get_language_value_for_keyword('Not Available',$this->data['langCode']); 
				  }
			  ?>
               <p><?php echo get_time_to_string('h:i A', $bookinTime); ?> <?php if ($this->lang->line('user_on') != '') echo stripslashes($this->lang->line('user_on')); else echo 'on'; ?> <?php echo get_time_to_string('d M, Y', $bookinTime); ?> <strong>[<?php echo $car_type; ?>]</strong></p>
            </div>
            
			
			<!-- <?php 
            $trackStatusArr = array('Confirmed','Arrived','Onride','Finished');
            if(in_array($rides_details->ride_id,$trackStatusArr)){
            ?>
            <a href="track?rideId=<?php echo $rides_details->ride_id;?>">
               <div class="back_to_list" style="float: left; margin-left: 30px;">
                  <span><?php if ($this->lang->line('rider_track_ride') != '') echo stripslashes($this->lang->line('rider_track_ride')); else echo 'Track Ride'; ?></span>
               </div>
            </a>
            <?php } ?>  -->

			            
			<a href="track?rideId=<?php echo $rides_details->ride_id;?>">
				<div class="onride_ride_count_detail">
				<div class="track" style="float: left; margin-left: 30px; margin-top:15px;color:white;text-style:bold;font-size:14px;padding:10px;">
                  <?php if ($this->lang->line('track') != '') echo stripslashes($this->lang->line('track')); else echo 'Track'; ?>
               </div>
				</div>               
            </a>
			
            <a href="rider/my-rides">
               <div class="back_to_list">
                  <i class="fa fa-long-arrow-left" aria-hidden="true"></i> <span><?php if ($this->lang->line('site_user_back_to_listing') != '') echo stripslashes($this->lang->line('site_user_back_to_listing')); else echo 'Back to My Rides'; ?></span>
               </div>
            </a>

			

         </div>
         <div class="ride_cash_detail">
            
            <?php if($fav == 'Yes'){ ?>
				<div class="hover-fav"><a href="#" data-toggle="modal" data-target="#make-unfav"><img src="images/Heart_red.png"></a></div>
			<?php } else { ?>
				<div class="hover-fav"><a href="#" data-toggle="modal" data-target="#make-fav"><img src="images/Heart_gray.png"></a></div>
			<?php } ?>
         
            <div class="ride_long_detail">
               <div class="ride_view_map" style="height:300px; overflow: hidden;">
					<?php 
                        $invoice_src = 'trip_invoice/'.$rides_details->ride_id.'_path.jpg';
                        if(file_exists($invoice_src)){  
                    ?>
                        <img src="<?php echo $invoice_src; ?>" class="invoice_map_img" />
                    <?php 
                    } else { 
                        echo $map['js']; 
                        echo $map['html'];
                    }
                    ?>
                    
               </div>
               
               
               
               <div class="view_map_info">
				<?php if($pickupTime != '' || isset($rides_details->booking_information['pickup']['location'])){ ?>
                  <li class="even">
                     <h2><?php if($pickupTime != '') echo get_time_to_string('h:i A', $pickupTime); ?></h2>
                     <p>
						<?php	if(isset($rides_details->booking_information['pickup']['location'])) echo $rides_details->booking_information['pickup']['location'];   ?>
						</p>
                  </li>
				  <?php } ?>
				  
				  <?php if($dropinTime != '' || (isset($rides_details->booking_information['drop']['location']) && $rides_details->booking_information['drop']['location']!="")){ ?>
                  <li class="add">
                     <h2><?php if($dropinTime != '') echo get_time_to_string('h:i A', $dropinTime); ?></h2>
                     <p>
						 <?php	if(isset($rides_details->booking_information['drop']['location'])) echo $rides_details->booking_information['drop']['location'];   ?>
					 </p>
                  </li>
				  <?php } ?>
               </div>
			   <?php 
				$ride_distance = '';
				$time_taken = '';
				$wait_time = '';
				if(isset( $rides_details->summary['ride_distance'])) $ride_distance = $rides_details->summary['ride_distance']; 
				if(isset( $rides_details->summary['ride_duration'])) $time_taken = $rides_details->summary['ride_duration'];
				if(isset( $rides_details->summary['waiting_duration'])) $wait_time = $rides_details->summary['waiting_duration'];
			   
			   ?>
               <div class="ride_car_cost">
                  <li class="ride_km">
                     <h5><?php if ($this->lang->line('admin_rides_distance') != '') echo stripslashes($this->lang->line('admin_rides_distance')); else echo 'Distance'; ?></h5>
                     <p><?php $ride_distance = floatval($ride_distance);  if($ride_distance != '' || $ride_distance >=0) echo $ride_distance.' '.$d_distance_unit;  else echo '--';?></p>
                  </li>
                  <li class="time_taken">
					<?php 
                    if($time_taken > 1){
                        if ($this->lang->line('rides_mins_lower') != '')$mins = stripslashes($this->lang->line('rides_mins_lower'));else $mins = 'mins'; 
                    } else {
                        if ($this->lang->line('rides_min_lower') != '')$mins = stripslashes($this->lang->line('rides_min_lower'));else $mins = 'min';
                    }
                    ?>
                     <h5><?php if ($this->lang->line('dash_user_rides_time_taken') != '') echo stripslashes($this->lang->line('dash_user_rides_time_taken')); else echo 'Time Taken'; ?></h5>
                     <p><?php $time_taken= floatval($time_taken); if($time_taken != '' || $time_taken>=0) echo $time_taken.' '.$mins;  else echo '--';?></p>
                  </li>
                  <li class="wait_time">
					<?php 
                    if($wait_time > 1){
                        if ($this->lang->line('rides_mins_lower') != '')$mins = stripslashes($this->lang->line('rides_mins_lower'));else $mins = 'mins'; 
                    } else {
                        if ($this->lang->line('rides_min_lower') != '')$mins = stripslashes($this->lang->line('rides_min_lower'));else $mins = 'min';
                    }
                    ?>
                     <h5><?php if ($this->lang->line('dash_user_rides_wait_time') != '') echo stripslashes($this->lang->line('dash_user_rides_wait_time')); else echo 'Wait Time'; ?></h5>
                     <p><?php $wait_time= floatval($wait_time); if($wait_time != '' || $wait_time>=0) echo $wait_time.' '.$mins;  else echo '--';?></p>
                  </li>
               </div>
            </div>
			
			
            
            
            <?php 
			
			$avg_rating = 0;
			if (isset($rides_details->ratings['driver']['avg_rating'])) $avg_rating = $rides_details->ratings['driver']['avg_rating'];
			$review_status = 'No';
			if (isset($rides_details->driver_review_status)) $review_status = 'Yes';
			
			?>
            <div class="rider_cost_detail">
            
                <?php if($driver_assigned){
                    $profilePic = base_url() . USER_PROFILE_IMAGE_DEFAULT;
                    if (isset($driver_details->row()->image) && $driver_details->row()->image != '') {
                        $profilePic = base_url() . USER_PROFILE_IMAGE . $driver_details->row()->image;
                    }
                ?>
            
                <div class="rider_detail">
                    <div class="rider_img" style="background: rgba(0, 0, 0, 0) url('<?php echo $profilePic; ?>') repeat scroll 0 0 / cover ;">
					</div>
					<div class="rider_name">
                     <h2><?php if ($this->lang->line('site_user_you_ride_with') != '') echo stripslashes($this->lang->line('site_user_you_ride_with')); else echo 'You ride with'; ?> <?php if(isset($driver_details->row()->driver_name)) echo $driver_details->row()->driver_name; ?></h2>
						<div  class="star_rate">
							<?php if($review_status == 'Yes'){ ?>
							<span><?php if ($this->lang->line('site_user_your_ratings') != '') echo stripslashes($this->lang->line('site_user_your_ratings')); else echo 'Your Ratings'; ?>: </span> <span> <input id="display_ratings" value="<?php echo $avg_rating; ?>" type="number" class="" ></span>
							<?php } else if($get_ratings == 'Yes'){ ?>
							   <span><a href="" data-toggle="modal" data-target="#ratings_popup" style="color:darkblue;"><b><?php if ($this->lang->line('site_user_rate_your_ride') != '') echo stripslashes($this->lang->line('site_user_rate_your_ride')); else echo 'Rate Your Ride'; ?></b></a></span>
							<?php } ?>
						</div>
					</div>
                </div>
                <?php } else { ?>
                <div class="rider_detail" style="background-color: #fafafa; color: #000;">
                    <p class="driver_nt_assig"><?php if ($this->lang->line('rider_driver_details_not_avil') != '') echo stripslashes($this->lang->line('rider_driver_details_not_avil')); else echo 'Driver details not available'; ?></p>
                </div>
                <?php } ?>
               
               
               <?php 
                if(($rides_details->ride_status == 'finished' || $rides_details->ride_status == 'Completed') && $driver_details->num_rows() == 1){
               ?>
			   <div class="fare_breakdown">
                  <h3><?php if ($this->lang->line('site_user_fare_breakdown') != '') echo stripslashes($this->lang->line('site_user_fare_breakdown')); else echo 'Fare Breakdown'; ?></h3>
                  <div class="col">
                     <li class="fare_name"><?php if ($this->lang->line('site_user_base_fare') != '') echo stripslashes($this->lang->line('site_user_base_fare')); else echo 'Base fare'; ?> <span class="line"></span></li>
                     <li class="fare_cost">
						<?php
							$base_fare = 0;
							if(isset($rides_details->pool_ride) && isset($rides_details->total['total_fare'])) {
								$base_fare = $rides_details->total['total_fare']; 
							} else if(isset($rides_details->total['base_fare'])) {
								$base_fare = $rides_details->total['base_fare']; 
							}
                            echo $dcurrencySymbol.' '.number_format($base_fare,2);
						?>
					 </li>
                  </div>
				  
				   <?php if(isset($rides_details->total['distance']) && $rides_details->total['distance'] > 0) { ?>
                  <div class="col">
                     <li class="fare_name"><?php if ($this->lang->line('site_user_distance_fare') != '') echo stripslashes($this->lang->line('site_user_distance_fare')); else echo 'Distance Fare'; ?> <span class="line"></span></li>
                     <li class="fare_cost">
						<?php echo $dcurrencySymbol.' '.number_format($rides_details->total['distance'],2);  ?>
					 </li>
                  </div>
				  <?php } ?>
				  
				  <?php if(isset($rides_details->total['ride_time']) && $rides_details->total['ride_time'] > 0) { ?>
                  <div class="col">
                     <li class="fare_name"><?php if ($this->lang->line('site_user_duration_fare') != '') echo stripslashes($this->lang->line('site_user_duration_fare')); else echo 'Duration Fare'; ?> <span class="line"></span></li>
                     <li class="fare_cost">
						<?php echo $dcurrencySymbol.' '.number_format($rides_details->total['ride_time'],2);  ?>
					 </li>
                  </div>
				  <?php } ?>
				  
				  <?php if(isset($rides_details->total['wait_time']) && $rides_details->total['wait_time'] > 0) { ?>
                  <div class="col">
                     <li class="fare_name"><?php if ($this->lang->line('site_user_waiting_time_charges') != '') echo stripslashes($this->lang->line('site_user_waiting_time_charges')); else echo 'Waiting Time Charges'; ?> <span class="line"></span></li>
                     <li class="fare_cost">
						<?php echo $dcurrencySymbol.' '.number_format($rides_details->total['wait_time'],2);  ?>
					 </li>
                  </div>
				  <?php } ?>
				  
				  <?php 
					$coupon_discount = 0; 
					if(isset($rides_details->total['coupon_discount'])) $coupon_discount = $rides_details->total['coupon_discount']; 
					if($coupon_discount > 0){
					?>
					<div class="col">
						<li class="fare_name"><?php if ($this->lang->line('dash_coupon_discount') != '') echo stripslashes($this->lang->line('dash_coupon_discount')); else echo 'Coupon discount'; ?><span class="line"></span></li>
						<li class="fare_cost"><?php echo $dcurrencySymbol.' '.number_format($coupon_discount,2); ?></li>
					</div>
				  <?php } ?>
				  
				  <?php 
					$peak_time_charge = 0; 
					if(isset($rides_details->fare_breakup['peak_time_charge'])) $peak_time_charge = $rides_details->fare_breakup['peak_time_charge']; 
					if($peak_time_charge > 0){
					?>
					<div class="col">
						<li class="fare_name"><?php if ($this->lang->line('site_user_peak_time_charge') != '') echo stripslashes($this->lang->line('site_user_peak_time_charge')); else echo 'Peak time charge'; ?> (<?php echo $peak_time_charge;?>x)<span class="line"></span></li>
						<li class="fare_cost">
							<?php
								if(isset($rides_details->total['peak_time_charge'])) {
									echo $dcurrencySymbol.' '.number_format($rides_details->total['peak_time_charge'],2); 
								} else {
									 echo  $dcurrencySymbol.' 0.00'; 
								}
							?>
						</li>
					</div>
				  <?php } ?>
				  
				  <?php 
					$night_charge = 0; 
					if(isset($rides_details->fare_breakup['night_charge'])) $night_charge = $rides_details->fare_breakup['night_charge']; 
					if($night_charge > 0){
					?>
					<div class="col">
						<li class="fare_name"><?php if ($this->lang->line('dash_night_time_charge') != '') echo stripslashes($this->lang->line('dash_night_time_charge')); else echo 'Night time charge'; ?> (<?php echo $night_charge;?>x)<span class="line"></span></li>
						<li class="fare_cost">
							<?php
								if(isset($rides_details->total['night_time_charge'])) {
									echo $dcurrencySymbol.' '.number_format($rides_details->total['night_time_charge'],2); 
								} else {
									 echo  $dcurrencySymbol.' 0.00'; 
								}
							?>
						</li>
					</div>
				  <?php } ?>
				  
				  <?php if(isset($rides_details->total['parking_charge']) && $rides_details->total['parking_charge'] > 0) { ?>
				  <div class="col">
                     <li class="fare_name"><?php if ($this->lang->line('dash_parking_charge') != '') echo stripslashes($this->lang->line('dash_parking_charge')); else echo 'Parking Charge'; ?><span class="line"></span></li>
                     <li class="fare_cost">
						<?php echo $dcurrencySymbol.' '.number_format($rides_details->total['parking_charge'],2);  ?>
					 </li>
                  </div>
				   <?php } ?>
				   
				  <?php if(isset($rides_details->total['toll_charge']) && $rides_details->total['toll_charge'] > 0) { ?>
				  <div class="col">
                     <li class="fare_name"><?php if ($this->lang->line('dash_toll_charge') != '') echo stripslashes($this->lang->line('dash_toll_charge')); else echo 'Toll Charge'; ?><span class="line"></span></li>
                     <li class="fare_cost">
						<?php echo $dcurrencySymbol.' '.number_format($rides_details->total['toll_charge'],2);  ?>
					 </li>
                  </div>
				   <?php } ?>
				  
				  <?php if(isset($rides_details->total['service_tax']) && $rides_details->total['service_tax'] != '') { ?>
				  <div class="col">
                     <li class="fare_name"><?php if ($this->lang->line('dash_service_tax') != '') echo stripslashes($this->lang->line('dash_service_tax')); else echo 'Service Tax'; ?><span class="line"></span></li>
                     <li class="fare_cost">
						<?php echo $dcurrencySymbol.' '.number_format($rides_details->total['service_tax'],2);  ?>
					 </li>
                  </div>
				   <?php } ?>
				    <?php if(isset($rides_details->total['tips_amount']) && $rides_details->total['tips_amount'] != '') { ?>
				  <div class="col">
                     <li class="fare_name"><?php if ($this->lang->line('dash_tips_tax') != '') echo stripslashes($this->lang->line('dash_tips_tax')); else echo 'Tips Amount'; ?><span class="line"></span></li>
                     <li class="fare_cost">
						<?php echo $dcurrencySymbol.' '.number_format($rides_details->total['tips_amount'],2);  ?>
					 </li>
                  </div>
				   <?php } ?>
                  <div class=" total_via">
				  <?php 
					$pau_summary = $rides_details->pay_summary['type'];
					if($rides_details->pay_summary['type']=="Cash"){
						$pau_summary = get_language_value_for_keyword('Cash',$this->data['langCode']);
					}
				  ?>
                     <li class="fare_name"><?php if ($this->lang->line('dash_total_fare') != '') echo stripslashes($this->lang->line('dash_total_fare')); else echo 'Total Fare'; ?> <?php if(isset($rides_details->pay_summary['type'])) { ?>(<?php if ($this->lang->line('site_user_via') != '') echo stripslashes($this->lang->line('site_user_via')); else echo 'Via'; ?> <?php echo str_replace('_',',',$pau_summary);?>) <?php } ?></li>
                     <li class="fare_cost">
							<?php
                                $tips_amount=0;
                                if(isset($rides_details->total['tips_amount']) && $rides_details->total['tips_amount'] != '') {
                                    $tips_amount=$rides_details->total['tips_amount'];
                                }
								if(isset($rides_details->total['grand_fare'])) {
									echo $dcurrencySymbol.' '.number_format(($rides_details->total['grand_fare']+$tips_amount),2); 
								} else {
									 echo '0'; 
								}		
							?>
					</li>
					<?php 
					if(isset($rides_details->total['wallet_usage']) && $rides_details->total['wallet_usage'] > 0) {
					?>
                     <li class="fare_name"><?php if ($this->lang->line('site_user_wallet_usage') != '') echo stripslashes($this->lang->line('site_user_wallet_usage')); else echo 'Wallet Usage'; ?></li>
                     <li class="fare_cost">
						<?php
							echo $dcurrencySymbol.' '.number_format($rides_details->total['wallet_usage'],2); 
						?>
					</li>
					<?php } ?>
                  </div>
               </div>
               <?php } ?>
			    <?php 
			if( $rides_details->ride_status == 'Cancelled' ){ ?>
            
            
               
                   
                   <div class="fare_breakdown">
                      <h3><?php if ($this->lang->line('common_cancellation_details') != '') echo stripslashes($this->lang->line('common_cancellation_details')); else echo 'Cancellation Details'; ?></h3>
                      <div class="col">
                         <li class="" style="width:100%;"><b><?php if ($this->lang->line('admin_rides_cancelled_by') != '') echo stripslashes($this->lang->line('admin_rides_cancelled_by')); else echo 'Cancelled By'; ?></b></li>
                         <li style="text-transform: capitalize;">
                            <?php  
                            if(isset($rides_details->cancelled)){
                                echo get_language_value_for_keyword(strtolower($rides_details->cancelled['primary']['by']),$this->data['langCode']); 
                            }
                            ?>				 
                         </li>
                      </div>
                      
                      <div class="col">
                         <li style="width:100%;"><b><?php if ($this->lang->line('admin_rides_cancellation_reason') != '') echo stripslashes($this->lang->line('admin_rides_cancellation_reason')); else echo 'Cancellation Reason'; ?></b></li>
                         <li style="text-transform: capitalize;">
                           <?php  
                                $cancel_reason = $rides_details->cancelled['primary']['text']; 
                                if(isset($rides_details->cancelled['primary']['reason']) && $rides_details->cancelled['primary']['reason'] != '' )$cancel_reason = get_cancellation_reason_by_lang($rides_details->cancelled['primary']['reason'],$langCode);
                                echo $cancel_reason;
                            ?>		 
                         </li>
                      </div>
                      
                   </div>
               
            
            
            <?php }  ?>
            </div>
			
			
         </div>
      </div>
   </div>
</section>

<div class="modal fade"  id="ratings_popup">
	<div class="modal-dialog" role="document">
		<div class="modal-content">
			<div class="modal-header">
				<h5 class="modal-title"><?php if ($this->lang->line('site_user_rate_ur_ride_to_increase_our_service_quality') != '') echo stripslashes($this->lang->line('site_user_rate_ur_ride_to_increase_our_service_quality')); else echo 'Rate your ride to increase our service quality'; ?></h5>
				<button type="button" class="close" data-dismiss="modal" aria-label="Close">
				  <span aria-hidden="true">&times;</span>
				</button>
			</div>
			<?php # echo'<pre>';print_r($rating_options);die;
					$formArr = array('id' => 'ratings_form','method' => 'post','enctype' => 'multipart/form-data');
					echo form_open('site/rider/submit_reviews',$formArr);
				?>
				<div class="modal-body">
				
					<?php if($get_ratings == 'Yes'){ 
					$i = 0;
					foreach($rating_options->result() as $options){ #echo'<pre>';print_r($options);die;
						?>
					<p>
                    
                         <?php 
						$langCode = $this->data['langCode'];
						if(isset($options->option_name_languages[$langCode]) && $options->option_name_languages[$langCode] != ''){
							$option_name = $options->option_name_languages[$langCode];
						}else{
							$option_name = 	$options->option_name;
						} 
						?>
                        
						<div  class="star_rate">
							<span style="width:30%;" id="opt_<?php echo $options->option_id; ?>"><?php echo $option_name; ?></span> 
							<span> 
								<input type="hidden" name="reviews[<?php echo $i; ?>][option_id]" value="<?php echo $options->option_id; ?>">
								<input type="hidden" name="reviews[<?php echo $i; ?>][option_title]" value="<?php echo $options->option_name; ?>">
								<input id="ratings-<?php echo $options->option_id; ?>" value="0" name="reviews[<?php echo $i; ?>][rating]" type="number" class="rating user_rates" min=0 max=5 step=1 data-size="xs" data-opt_id = "<?php echo $options->option_id; ?>" />
							</span>
						</div>
					</p>
					<?php $i ++; } } ?>
					<p>
						<textarea name="comments" placeholder="<?php if ($this->lang->line('rider_enter_your_comments') != '') echo stripslashes($this->lang->line('rider_enter_your_comments')); else echo 'Please enter your comments'; ?>" style="width: 100%;"></textarea>
					</p>
				</div>
				<div class="modal-footer">
					<input type="hidden" name="ride_id" value="<?php echo $rides_details->ride_id; ?>" />
					<button type="button" onclick="validate_ratings();" class="btn btn-primary"><?php if ($this->lang->line('site_user_submit_ratings') != '') echo stripslashes($this->lang->line('site_user_submit_ratings')); else echo 'Submit Ratings'; ?></button>
					<button type="button" class="btn btn-secondary" data-dismiss="modal"><?php if ($this->lang->line('rides_close') != '') echo stripslashes($this->lang->line('rides_close')); else echo 'Close'; ?></button>
				</div>
			</form>
		</div>
	</div>
</div>




<!-- Modal -->
<div class="modal fade" id="make-unfav" role="dialog" aria-labelledby="myModalLabel1">
    <div class="modal-dialog" role="document">
        <div class="modal-content favourite_container">
            <div class="modal-header">
                <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
                <h4 class="modal-title" id="myModalLabel1">  <?php if ($this->lang->line('user_are_you_confirm') != '') echo stripslashes($this->lang->line('user_are_you_confirm')); else echo 'Are you confirm'; ?>!</h4>
            </div>
            <div class="modal-body">
                <div class="col-lg-12 text-left sign_driver_text" style="min-height: 75px;">
					<span> <?php if ($this->lang->line('user_remove_fav_loc_confirm') != '') echo stripslashes($this->lang->line('user_remove_fav_loc_confirm')); else echo 'Do you want to remove this location from your favourite list'; ?>? </span>
					<span id="FavErr1" class="favErr"></span>
				</div>
				
            </div> 
            <div class="modal-footer">
                <button type="button" class="btn btn-primary" onclick="return makeLocUnFav();" id="cont-btn1"><?php if ($this->lang->line('user_yes') != '') echo stripslashes($this->lang->line('user_yes')); else echo 'Yes'; ?></button>
            </div>
        </div>
    </div>
</div>

<div class="modal fade" id="make-fav" role="dialog" aria-labelledby="myModalLabel">
    <div class="modal-dialog" role="document">
        <div class="modal-content favourite_container">
            <div class="modal-header">
                <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
                <h4 class="modal-title" id="myModalLabel"><?php if ($this->lang->line('user_add_fav_location') != '') echo stripslashes($this->lang->line('user_add_fav_location')); else echo 'Add this location into your favourite list'; ?></h4>
            </div>
            <div class="modal-body">
                <div class="col-lg-12 text-left sign_driver_text" style="min-height: 75px;">
					<input type="text" class="form-control sign_in_text required email" id="favourite_title" placeholder="<?php if ($this->lang->line('user_favourite_title') != '') echo stripslashes($this->lang->line('user_favourite_title')); else echo 'Favourite location title'; ?>" maxlength="30" />
					<span id="FavErr" class="favErr"></span>
				</div>
				
            </div> 
            <div class="modal-footer">
                <button type="button" class="btn btn-primary securityCheck" onclick="return makeLocFav();" id="cont-btn"><?php if ($this->lang->line('user_continue') != '') echo stripslashes($this->lang->line('user_continue')); else echo 'Continue'; ?></button>
            </div>
        </div>
    </div>
</div>

<input type="hidden" id="exist-fav" value="<?php echo $fav; ?>" />
<input type="hidden" id="address" value="<?php echo htmlentities($pickup_location); ?>" />
<input type="hidden" id="user_id" value="<?php echo $rides_details->user['id']; ?>" />
<input type="hidden" id="longitude" value="<?php echo $location_lon; ?>" />
<input type="hidden" id="latitude" value="<?php echo $location_lat; ?>" />
<input type="hidden" id="favKey" value="<?php echo $loc_key; ?>" />

<script>
	<?php if ($this->lang->line('user_fav_location_added') != ''){ ?>
	var favAdded = "<?php echo stripslashes($this->lang->line('user_fav_location_added')); ?>";
	<?php }else{ ?>
	var favAdded = "Location added into your favourite list";
	<?php } ?>
	<?php if ($this->lang->line('user_fav_location_removed') != ''){ ?>
	var favRemoved = "<?php echo stripslashes($this->lang->line('user_fav_location_removed')); ?>";
	<?php }else{ ?>
	var favRemoved = "Location removed from your favourite list";
	<?php } ?>
	function makeLocFav(){
		var fav_title = $('#favourite_title').val().trim();
		var address = $('#address').val();
		var user_id = $('#user_id').val();
		var longitude = $('#longitude').val();
		var latitude = $('#latitude').val();
		$('#favourite_title').css('border-color','none');
		$('#FavErr').css('display','none');
		if(fav_title != ''){
			$('#cont-btn').html('<img src="images/indicator.gif">');
			$.ajax({
			    type: "POST",
			    url: 'site/rider/add_favourite_location',
			    data: {'title':fav_title,'address':address,'user_id':user_id,'longitude':longitude,'latitude':latitude},
			    dataType: 'json',
				success:function(res){
					$('#FavErr').css('display','block');
					$('#cont-btn').html('<?php if ($this->lang->line('user_continue') != '') echo stripslashes($this->lang->line('user_continue')); else echo 'Continue'; ?>');
					if(res.status == '1'){ 
                        $(".securityCheck").attr("disabled", true);
						$('#FavErr').css('color','green');
						$('#FavErr').html(favAdded);
						location.reload();
					} else {
						$('#FavErr').css('color','red');
						$('#FavErr').html(res.message);

					}
				} 
			});
		} else {
			$('#favourite_title').css('border-color','red');	
		}
	}
	
	function makeLocUnFav(){
		var user_id = $('#user_id').val();
		var favLocKey = $('#favKey').val();
		if(favLocKey != ''){
			$('#cont-btn1').html('<img src="images/indicator.gif">');
			$('#FavErr1').css('display','none');
			$.ajax({
			    type: "POST",
			    url: 'site/rider/remove_favourite_location',
			    data: {'user_id':user_id,'location_key':favLocKey},
			    dataType: 'json',
				success:function(res){
					$('#FavErr1').css('display','block');
					$('#cont-btn1').html('<?php if ($this->lang->line('user_yes') != '') echo stripslashes($this->lang->line('user_yes')); else echo 'Yes'; ?>');
					if(res.status == '1'){ 
						$('#FavErr1').css('color','green');
						$('#FavErr').html(favRemoved);
						location.reload();
					} else {
						$('#FavErr1').css('color','red');
						$('#FavErr1').html(res.message);
					}
				} 
			});
		} else {
			alert('Please refresh this page and try again');
		}
	}
    

</script>


<style>
	.rating-md {
		font-size: 1.13em !important;
	}
    
    .driver_nt_assig {
        text-align: center;
        background: #dfdfdf;
        padding: 12px;
    }
    
    .gm-fullscreen-control {
      display: none;
    }
    
</style>

 <link rel="stylesheet" href="css/site/star-rating.css" media="all" rel="stylesheet" type="text/css"/>
 <script src="js/site/star-rating.js" type="text/javascript"></script>
  <script>
    $(document).ready(function () {
		$('#display_ratings').rating({displayOnly: true, step: 0.5});
        
        $('#favourite_title').keypress(function(){
            $('#FavErr').hide();
            $('#FavErr1').hide();
        });
        
        $('.modal').on('hidden.bs.modal', function(){ 
            $('#favourite_title').val('');
            $('#FavErr').hide();
        });
        
    });
    
   
</script>

<?php
$this->load->view('site/templates/footer');
?> 