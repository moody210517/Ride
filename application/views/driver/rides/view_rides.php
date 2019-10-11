<?php 
$this->load->view('driver/templates/profile_header');
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

?> 


<section class="profile_pic_sec row">
   <div  class="profile_login_cont">
     	<!--------------  Load Profile Side Bar ------------------------>
		<?php    
			$this->load->view('driver/templates/profile_sidebar'); 
		?>
		
      <div class="share_detail">
         <div class="share_det_title">
            <h2><span><?php if ($this->lang->line('driver_view_My') != '') echo stripslashes($this->lang->line('driver_view_My')); else echo 'MY'; ?> <?php if ($this->lang->line('driver_view_Rides') != '') echo stripslashes($this->lang->line('driver_view_Rides')); else echo 'RIDES'; ?></span></h2>
         </div>
         <div class="invoice_header">
            <div class="invoice_title">
               <h1><?php if ($this->lang->line('driver_ride_invoice') != '') echo stripslashes($this->lang->line('driver_ride_invoice')); else echo 'Invoice'; ?> #<?php echo $rides_details->ride_id;?></h1>
               <p><?php echo get_time_to_string('h:i A', $bookinTime); ?> <?php if ($this->lang->line('driver_on') != '') echo stripslashes($this->lang->line('driver_on')); else echo 'on'; ?> <?php echo get_time_to_string('d M, Y', $bookinTime); ?></p>
            </div>
            
            <?php 
            $trackStatusArr = array('Confirmed','Arrived','Onride','Finished');
            if(in_array($rides_details->ride_id,$trackStatusArr)){
            ?>
            <a href="track?rideId=<?php echo $rides_details->ride_id;?>">
               <div class="back_to_list" style="float: left; margin-left: 30px;">
                  <span><?php if ($this->lang->line('rider_track_ride') != '') echo stripslashes($this->lang->line('rider_track_ride')); else echo 'Track Ride'; ?></span>
               </div>
            </a>
            <?php } ?>
            
            <a href="driver/rides/display_rides">
               <div class="back_to_list">
                  <i class="fa fa-long-arrow-left" aria-hidden="true"></i> <span><?php if ($this->lang->line('site_user_back_to_listing') != '') echo stripslashes($this->lang->line('site_user_back_to_listing')); else echo 'Back to My Rides'; ?></span>
               </div>
            </a>
         </div>
         <div class="ride_cash_detail">
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
				<?php if($dropinTime != '' || isset($rides_details->booking_information['pickup']['location'])){ ?>
                  <li class="even">
                     <h2><?php if($pickupTime != '') echo get_time_to_string('h:i A', $dropinTime); ?></h2>
                     <p>
						<?php	if(isset($rides_details->booking_information['pickup']['location'])) echo $location = $rides_details->booking_information['pickup']['location'];   ?>
						</p>
                  </li>
				  <?php } ?>
				  
				  <?php if($dropinTime != '' || isset($rides_details->booking_information['drop']['location'])){ ?>
                  <li class="add">
                     <h2><?php if($dropinTime != '') echo get_time_to_string('h:i A', $dropinTime); ?></h2>
                     <p>
						 <?php	if(isset($rides_details->booking_information['drop']['location'])) echo $location = $rides_details->booking_information['drop']['location'];   ?>
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
                  <li class="car_name">
                     <h5><?php if ($this->lang->line('driver_ride_car') != '') echo stripslashes($this->lang->line('driver_ride_car')); else echo 'Car'; ?></h5>
                     <p>
						<?php 
						  if(isset($rides_details->booking_information['service_type'])) {
							$lng_cat_name =  get_category_name_by_lang($rides_details->booking_information['service_id'],$this->data['langCode']);  
							if($lng_cat_name != '') echo $lng_cat_name; else echo $rides_details->booking_information['service_type'];
						  } else { 
							echo get_language_value_for_keyword('Not Available',$this->data['langCode']); 
						  }
					  ?>
					</p>
                  </li>
                  <li class="ride_km">
                     <h5><?php if ($this->lang->line('driver_view_distance') != '') echo stripslashes($this->lang->line('driver_view_distance')); else echo 'Distance'; ?></h5>
                     <p><?php $ride_distance = floatval($ride_distance);  if($ride_distance != '' || $ride_distance >=0) echo $ride_distance.' '.$d_distance_unit;  else echo '--';?></p>
                  </li>
                  <li class="ride_cost"> 
                     <h5><?php if ($this->lang->line('driver_view_trip_time') != '') echo stripslashes($this->lang->line('driver_view_trip_time')); else echo 'Trip Time'; ?></h5>
					
					<?php 
                    if($time_taken > 1){
                        if ($this->lang->line('rides_mins_lower') != '')$mins = stripslashes($this->lang->line('rides_mins_lower'));else $mins = 'mins'; 
                    } else {
                        if ($this->lang->line('rides_min_lower') != '')$mins = stripslashes($this->lang->line('rides_min_lower'));else $mins = 'min';
                    }
                    ?>
					 <p><?php $time_taken= floatval($time_taken); if($time_taken != '' || $time_taken>=0) echo $time_taken.' '.$mins;  else echo '--';?></p>
                  </li>
               </div>
            </div>
            
            
            <?php /* 
			if( $rides_details->ride_status == 'Cancelled' ){ ?>
            
            
                <div class="rider_cost_detail">
                   
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
                </div>
            
            
            
            <?php } */ ?>
            
			
			<?php 
			if(($rides_details->ride_status == 'finished' || $rides_details->ride_status == 'Completed') && $user_details->num_rows() == 1){
			
			$profilePic = base_url() . USER_PROFILE_IMAGE_DEFAULT;
			if (isset($user_details->row()->image) && $user_details->row()->image != '') {
				$profilePic = base_url() . USER_PROFILE_IMAGE . $user_details->row()->image;
			}
			
			$avg_rating = 0;
			if (isset($rides_details->ratings['rider']['avg_rating'])) $avg_rating = $rides_details->ratings['rider']['avg_rating'];
			$review_status = 'No';
			if (isset($rides_details->rider_review_status)) $review_status = 'Yes';
			
			?>
            <div class="rider_cost_detail">
               <div class="rider_detail">
                    <div class="rider_img" style="background: rgba(0, 0, 0, 0) url('<?php echo $profilePic; ?>') repeat scroll 0 0 / cover ;">
					</div>
					<div class="rider_name">
                     <h2><?php if ($this->lang->line('driver_you_ride_with') != '') echo stripslashes($this->lang->line('driver_you_ride_with')); else echo 'You ride with'; ?> <?php echo $user_details->row()->user_name; ?></h2>
						<div  class="star_rate">
							<?php if($review_status == 'Yes'){ ?>
							<span><?php if ($this->lang->line('driver_your_ratings') != '') echo stripslashes($this->lang->line('driver_your_ratings')); else echo 'Your Ratings'; ?> <?php echo $user_details->row()->user_name; ?>: </span> <span> <input id="display_ratings" value="<?php echo $avg_rating; ?>" type="number" class="" ></span>
							<?php } else if($get_ratings == 'Yes'){ ?>
							   <span><a href="" data-toggle="modal" data-target="#ratings_popup" style="color:darkblue;"><b><?php if ($this->lang->line('driver_rate_your_rider') != '') echo stripslashes($this->lang->line('driver_rate_your_rider')); else echo 'Rate Your Rider'; ?> <?php echo $user_details->row()->user_name; ?></b></a></span>
							<?php } ?>
						</div>
					</div>
               </div>
               <div class="fare_breakdown">
                  <h3><?php if ($this->lang->line('driver_fare_breakdown') != '') echo stripslashes($this->lang->line('driver_fare_breakdown')); else echo 'Fare Breakdown'; ?></h3>
                  <div class="col">
                     <li class="fare_name"><?php if ($this->lang->line('driver_basic_fare') != '') echo stripslashes($this->lang->line('driver_basic_fare')); else echo 'Basic Fare'; ?> <span class="line"></span></li>
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
                     <li class="fare_name"><?php if ($this->lang->line('driver_view_distance') != '') echo stripslashes($this->lang->line('driver_view_distance')); else echo 'Distance'; ?> <span class="line"><span class="line"></span></li>
                     <li class="fare_cost">
						<?php echo $dcurrencySymbol.' '.number_format($rides_details->total['distance'],2);  ?>
					 </li>
                  </div>
				  <?php } ?>
				  
				  <?php if(isset($rides_details->total['ride_time']) && $rides_details->total['ride_time'] > 0) { ?>
                  <div class="col">
                     <li class="fare_name"><?php if ($this->lang->line('driver_view_time') != '') echo stripslashes($this->lang->line('driver_view_time')); else echo 'Time'; ?> <span class="line"></span></li>
                     <li class="fare_cost">
						<?php echo $dcurrencySymbol.' '.number_format($rides_details->total['ride_time'],2);  ?>
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
						<li class="fare_name"><?php if ($this->lang->line('driver_peak_time_charge') != '') echo stripslashes($this->lang->line('driver_peak_time_charge')); else echo 'Peak time charge'; ?>(<?php echo $peak_time_charge;?>x)<span class="line"></span></li>
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
						<li class="fare_name"><?php if ($this->lang->line('dash_night_time_charge') != '') echo stripslashes($this->lang->line('dash_night_time_charge')); else echo 'Peak time Night time charge'; ?> (<?php echo $night_charge;?>x)<span class="line"></span></li>
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
				
                  <div class=" total_via">
                     <li class="fare_name">
					<?php 
					$pay_summary = $rides_details->pay_summary['type'];
					if($rides_details->pay_summary['type']=="Cash"){
						$pay_summary = get_language_value_for_keyword('Cash',$this->data['langCode']);
					}
					?>
					 <?php if ($this->lang->line('dash_total_fare') != '') echo stripslashes($this->lang->line('dash_total_fare')); else echo 'Total Fare'; ?><?php if(isset($rides_details->pay_summary['type'])) { ?>(<?php if ($this->lang->line('site_user_via') != '') echo stripslashes($this->lang->line('site_user_via')); else echo 'Via'; ?> <?php echo str_replace('_',',',$pay_summary);?>)<?php } ?>
					 </li>
                     <li class="fare_cost">
							<?php
								if(isset($rides_details->total['grand_fare'])) {
									echo $dcurrencySymbol.' '.number_format($rides_details->total['grand_fare'],2); 
								} else {
									 echo '0'; 
								}		
							?>
					</li>
					<?php 
					if(isset($rides_details->total['wallet_usage']) && $rides_details->total['wallet_usage'] > 0) {
					?>
                     <li class="fare_name"><?php if ($this->lang->line('driver_wallet_usage') != '') echo stripslashes($this->lang->line('driver_wallet_usage')); else echo 'Wallet Usage'; ?></li>
                     <li class="fare_cost">
							<?php
									echo $dcurrencySymbol.' '.number_format($rides_details->total['wallet_usage'],2); 
							?>
					</li>
					<?php } ?>
                  </div>
				  
               </div>
            </div>
			<?php } ?>
			
         </div>
      </div>
   </div>
</section>

<div class="modal fade"  id="ratings_popup">
	<div class="modal-dialog" role="document">
		<div class="modal-content">
			<div class="modal-header">
				<h5 class="modal-title"><?php if ($this->lang->line('driver_Rate_your_rider') != '') echo stripslashes($this->lang->line('driver_Rate_your_rider')); else echo 'Rate your rider to increase our service quality'; ?></h5>
				<button type="button" class="close" data-dismiss="modal" aria-label="Close">
				  <span aria-hidden="true">&times;</span>
				</button>
			</div>
			<?php 
					$formArr = array('id' => 'ratings_form','method' => 'post','enctype' => 'multipart/form-data');
					echo form_open('driver/rides/submit_reviews',$formArr);
				?>
				<div class="modal-body">
					<?php if($get_ratings == 'Yes'){ 
					$i = 0;
					foreach($rating_options->result() as $options){
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
					<button type="button" onclick="validate_ratings();" class="btn btn-primary"><?php if ($this->lang->line('driver_submit_ratings') != '') echo stripslashes($this->lang->line('driver_submit_ratings')); else echo 'Submit Ratings'; ?></button>
					<button type="button" class="btn btn-secondary" data-dismiss="modal"><?php if ($this->lang->line('driver_view_close') != '') echo stripslashes($this->lang->line('driver_view_close')); else echo 'Close'; ?></button>
				</div>
			</form>
		</div>
	</div>
</div>



<style>
	.rating-md {
		font-size: 1.13em !important;
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
    });
</script>

<?php
$this->load->view('driver/templates/footer');
?> 