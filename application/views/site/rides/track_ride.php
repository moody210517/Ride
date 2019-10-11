<?php
$this->load->view('site/templates/common_header');
$this->load->view('site/templates/cms_header');
?> 
<link rel="stylesheet" href="css/site/timeline.css">
<script src="js/site/timeline-animation.js"></script>
<script>
    jQuery(document).ready(function ($) {
        var timelineBlocks = $('.cd-timeline-block'),
                offset = 0.8;

        //hide timeline blocks which are outside the viewport
        hideBlocks(timelineBlocks, offset);

        //on scolling, show/animate timeline blocks when enter the viewport
        $(window).on('scroll', function () {
            (!window.requestAnimationFrame)
                    ? setTimeout(function () {
                        showBlocks(timelineBlocks, offset);
                    }, 100)
                    : window.requestAnimationFrame(function () {
                        showBlocks(timelineBlocks, offset);
                    });
        });

        function hideBlocks(blocks, offset) {
            blocks.each(function () {
                ($(this).offset().top > $(window).scrollTop() + $(window).height() * offset) && $(this).find('.cd-timeline-img, .cd-timeline-content').addClass('is-hidden');
            });
        }

        function showBlocks(blocks, offset) {
            blocks.each(function () {
                ($(this).offset().top <= $(window).scrollTop() + $(window).height() * offset && $(this).find('.cd-timeline-img').hasClass('is-hidden')) && $(this).find('.cd-timeline-img, .cd-timeline-content').removeClass('is-hidden').addClass('bounce-in');
            });
        }
    });
</script>
<div class="cms_base_div">
    <div class="container-new cms-container rideTran">

        <h1 class="text-center"><?php echo $heading; ?></h1>
        <div class="booking-persons">
            <div class="rider-info">
                <h2><?php
                    if ($this->lang->line('rides_rider_information') != '')
                        echo stripslashes($this->lang->line('rides_rider_information'));
                    else
                        echo 'Rider Information';
                    ?></h2>
                <?php
                if (isset($user_details->image)) {
                    if ($user_details->image != '') {
                        $profilePic = base_url() . USER_PROFILE_IMAGE . $user_details->image;
                    } else {
                        $profilePic = base_url() . USER_PROFILE_IMAGE_DEFAULT;
                    }
                } else {
                    $profilePic = base_url() . USER_PROFILE_IMAGE_DEFAULT;
                }
                ?>
                <img src="<?php echo $profilePic; ?>" width="110px;" height="110px;">
                <p><?php echo $user_details->user_name; ?><br/>
                    <?php echo $user_details->country_code . ' ' . $user_details->phone_number; ?></p>
            </div>
            <?php if (isset($driver_details['driver_id'])) { ?>
                <div class="driver-info">
                    <h2><?php
                        if ($this->lang->line('rides_driver_information') != '')
                            echo stripslashes($this->lang->line('rides_driver_information'));
                        else
                            echo 'driver Information';
                        ?></h2>
                    <?php
                    if (isset($driver_details['driver_image'])) {
                        if ($driver_details['driver_image'] != '') {
                            $profilePic = base_url() . USER_PROFILE_IMAGE . $driver_details['driver_image'];
                        } else {
                            $profilePic = base_url() . USER_PROFILE_IMAGE_DEFAULT;
                        }
                    } else {
                        $profilePic = base_url() . USER_PROFILE_IMAGE_DEFAULT;
                    }
                    ?>
                    <div class="profile_img">
                        <img src="<?php echo $profilePic; ?>" width="110px;" height="110px;">
                    </div>
                    <p><?php echo $driver_details['driver_name']; ?> <br/>
                        <?php echo $driver_details['phone_number']; ?></p>
                </div>
            <?php } ?>
        </div>
    </div>
    <p class="text-center ps">&emsp;</p>
    <style>
	#dvMap{
		width:80%;
		margin-left:10%;
	}
	</style>
	<div id="dvMap">
		<?php #echo $map['js']; ?>
		<?php #echo $map['html']; ?>
	</div>


    <div class="container-new cms-container rideTran">
        <section id="cd-timeline" class="cd-container">

            <div class="cd-timeline-block">
                <div class="cd-timeline-img">
                    <img src="img/cd-icon-location.svg" alt="<?php
                    if ($this->lang->line('rides_location') != '')
                        echo stripslashes($this->lang->line('rides_location'));
                    else
                        echo 'Location';
                    ?>">
                </div>

                <div class="cd-timeline-content">
                    <h2><?php
                        if ($this->lang->line('rides_booking_details') != '')
                            echo stripslashes($this->lang->line('rides_booking_details'));
                        else
                            echo 'Booking Details';
                        ?></h2>
                    <p> 

                        <?php if (isset($booking_information['service_type'])) { ?>
                            <b><?php
                                if ($this->lang->line('rides_cab_type') != '')
                                    echo stripslashes($this->lang->line('rides_cab_type'));
                                else
                                    echo 'Cab Type';
                                ?></b> <?php echo $booking_information['service_type']; ?> <br/>
                        <?php } ?>

                        <?php if (isset($booking_information['pickup']['location'])) { ?>
                            <b><?php
                                if ($this->lang->line('rides_pickup_location') != '')
                                    echo stripslashes($this->lang->line('rides_pickup_location'));
                                else
                                    echo 'Pickup Location';
                                ?></b> <?php echo $booking_information['pickup']['location']; ?> <?php if (isset($booking_information['est_pickup_date'])) { ?> on <?php
                                echo get_time_to_string('M d,Y h:i A', MongoEPOCH($booking_information['est_pickup_date']));
                            }
                            ?> <br/>
                            <?php } ?>

                        <?php if (isset($booking_information['drop']['location'])) { ?>
                            <b><?php
                                if ($this->lang->line('rides_drop_location') != '')
                                    echo stripslashes($this->lang->line('rides_drop_location'));
                                else
                                    echo 'Drop Location';
                                ?> </b> <?php
                            if ($booking_information['drop']['location'] != '') {
                                echo $booking_information['drop']['location'];
                            } else {

                                if ($this->lang->line('rides_na') != '')
                                    echo stripslashes($this->lang->line('rides_na'));
                                else
                                    echo 'N/A';
                            }
                            ?> <?php if (isset($booking_information['drop_date'])) { ?> on <?php
                                    echo get_time_to_string('M d,Y h:i A', MongoEPOCH($booking_information['drop_date']));
                                }
                                ?> <br/>
                        <?php } ?>


                    </p>

                    <span class="cd-date"> <?php
                        if (isset($booking_information['booking_date'])) {
                            echo get_time_to_string('M d,Y h:i A', MongoEPOCH($booking_information['booking_date']));
                        }
                        ?> </span>
                </div>
            </div>


            <?php if (isset($ride_details->history['driver_assigned'])) { ?>
                <div class="cd-timeline-block">
                    <div class="cd-timeline-img">
                        <img src="img/cd-icon-location.svg" alt="<?php
                        if ($this->lang->line('rides_location') != '')
                            echo stripslashes($this->lang->line('rides_location'));
                        else
                            echo 'Location';
                        ?>">
                    </div>

                    <div class="cd-timeline-content">
                        <h2><?php
                            if ($this->lang->line('rides_driver_assigned_at') != '')
                                echo stripslashes($this->lang->line('rides_driver_assigned_at'));
                            else
                                echo 'Driver Assigned At';
                            ?></h2>
                        <p><?php
                            if ($this->lang->line('rides_driver_has_been_assigned') != '')
                                echo stripslashes($this->lang->line('rides_driver_has_been_assigned'));
                            else
                                echo 'Driver has been assigned at ';
                            ?> <?php echo get_time_to_string('M d,Y h:i a', MongoEPOCH($ride_details->history['driver_assigned'])); ?></p>

                        <span class="cd-date"><?php echo get_time_to_string('M d,Y h:i A', MongoEPOCH($ride_details->history['driver_assigned'])); ?></span>
                    </div>
                </div>

            <?php } ?>


            <?php
            foreach ($tracking_details as $tracking) {
                ?>
                <div class="cd-timeline-block">
                    <div class="cd-timeline-img">
                        <img src="img/cd-icon-location.svg" alt="<?php
                        if ($this->lang->line('rides_location') != '')
                            echo stripslashes($this->lang->line('rides_location'));
                        else
                            echo 'Location';
                        ?>">
                    </div>
                    <div class="cd-timeline-content">
                        <h2><?php
                            if ($this->lang->line('rides_cab_location_at') != '')
                                echo stripslashes($this->lang->line('rides_cab_location_at'));
                            else
                                echo 'Cab Location At';
                            ?> <?php echo get_time_to_string('h:i a', $tracking['on_time']); ?></h2>
                        <p><?php echo $tracking['locality']; ?></p>
                        <span class="cd-date"><?php echo get_time_to_string('M d,Y  h:i A', $tracking['on_time']); ?></span>
                    </div>
                </div>
                <?php
            }
            ?>

            <?php
            if (isset($ride_details->summary) && isset($ride_details->history['end_ride'])) {
                ?>

                <div class="cd-timeline-block">
                    <div class="cd-timeline-img">
                        <img src="img/cd-icon-location.svg" alt="<?php
                        if ($this->lang->line('rides_location') != '')
                            echo stripslashes($this->lang->line('rides_location'));
                        else
                            echo 'Location';
                        ?>">
                    </div>

                    <div class="cd-timeline-content">
                        <h2><?php
                            if ($this->lang->line('rides_ride_summary') != '')
                                echo stripslashes($this->lang->line('rides_ride_summary'));
                            else
                                echo 'Ride Summary';
                            ?></h2>
                        <p>
                            <?php if (isset($ride_details->summary['ride_distance'])) { ?>
                                <b><?php
                                    if ($this->lang->line('rides_distance') != '')
                                        echo stripslashes($this->lang->line('rides_distance')).' -';
                                    else
                                        echo 'Distance -';
                                    ?></b> <?php echo $ride_details->summary['ride_distance']; ?> <?php
                                
                                    echo $d_distance_unit;
                                ?> <br/>
                                <?php } ?>

                            <?php if (isset($ride_details->summary['ride_duration'])) { ?>
                                <b><?php
                                    if ($this->lang->line('rides_ride_time') != '')
                                        echo stripslashes($this->lang->line('rides_ride_time')).' -';
                                    else
                                        echo 'Ride Time -';
                                    ?></b> <?php echo $ride_details->summary['ride_duration']; ?> <?php
                                if ($this->lang->line('rides_min') != '')
                                    echo stripslashes($this->lang->line('rides_min'));
                                else
                                    echo 'Min';
                                ?>
                            <?php } ?>

                            <?php if (isset($ride_details->summary['waiting_duration'])) { ?>
                                <b><?php
                                    if ($this->lang->line('rides_waiting_time') != '')
                                        echo stripslashes($this->lang->line('rides_waiting_time')).' -';
                                    else
                                        echo 'Waiting Time -';
                                    ?></b> <?php echo $ride_details->summary['waiting_duration']; ?>  <?php
                                if ($this->lang->line('rides_min') != '')
                                    echo stripslashes($this->lang->line('rides_min'));
                                else
                                    echo 'Min';
                                ?>
                            <?php } ?>
                        </p>
                        <span class="cd-date"><?php echo get_time_to_string('M d,Y h:i A', MongoEPOCH($ride_details->history['end_ride'])); ?></span>
                    </div>
                </div>

            <?php } ?>

            <?php
            if (isset($ride_details->pay_summary['type'])) {
                ?>

                <div class="cd-timeline-block">
                    <div class="cd-timeline-img">
                        <img src="img/cd-icon-location.svg" alt="<?php
                        if ($this->lang->line('rides_location') != '')
                            echo stripslashes($this->lang->line('rides_location'));
                        else
                            echo 'Location';
                        ?>">
                    </div>

                    <div class="cd-timeline-content">
                        <h2><?php
                            if ($this->lang->line('rides_payment') != '')
                                echo stripslashes($this->lang->line('rides_payment'));
                            else
                                echo 'Payment';
                            ?></h2>
                        <p><?php
                            if ($this->lang->line('rides_payment_done_by') != '')
                                echo stripslashes($this->lang->line('rides_payment_done_by'));
                            else
                                echo 'Payment has been done by';
                            ?> <?php echo $ride_details->pay_summary['type']; ?>
                        </p>
                        <span class="cd-date"><?php echo get_time_to_string('M d,Y h:i A', MongoEPOCH($ride_details->history['end_ride'])); ?></span>
                    </div>
                </div>

            <?php } ?>

            <?php
            if ($ride_details->ride_status == 'Cancelled' && isset($ride_details->history['cancelled_time'])) {
                if (isset($ride_details->cancelled)) {
                    ?>

                    <div class="cd-timeline-block">
                        <div class="cd-timeline-img">
                            <img src="img/cd-icon-location.svg" alt="<?php
                            if ($this->lang->line('rides_location') != '')
                                echo stripslashes($this->lang->line('rides_location'));
                            else
                                echo 'Location';
                            ?>">
                        </div>

                        <div class="cd-timeline-content">
                            <h2><?php
                                if ($this->lang->line('rides_cancelled') != '')
                                    echo stripslashes($this->lang->line('rides_cancelled'));
                                else
                                    echo 'Ride Cancelled';
                                ?></h2>
                            <p><?php
                                if ($this->lang->line('rides_ride_has_been_cancelled') != '')
                                    echo stripslashes($this->lang->line('rides_ride_has_been_cancelled'));
                                else
                                    echo 'Ride has been cancelled by';
                                ?><?php echo $ride_details->cancelled['primary']['by']; ?> <br/>
                                <b><?php
                                    if ($this->lang->line('rides_reason_for_cancelling') != '')
                                        echo stripslashes($this->lang->line('rides_reason_for_cancelling'));
                                    else
                                        echo 'Reason For Cancelling :';
                                    ?></b> <?php echo $ride_details->cancelled['primary']['text']; ?>
                            </p>
                            <span class="cd-date"><?php echo get_time_to_string('M d,Y h:i A', MongoEPOCH($ride_details->history['cancelled_time'])); ?></span>
                        </div>
                    </div>

                    <?php
                }
            }
            ?>



        </section> <!-- cd-timeline -->


    </div>
</div>
<?php
$this->load->view('site/templates/footer');
?> 		