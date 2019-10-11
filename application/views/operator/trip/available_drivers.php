<?php
$this->load->view(OPERATOR_NAME.'/templates/header.php');


$pickupTime = '';
if(isset($rides_details->row()->booking_information['actual_pickup_date'])) 
    $pickupTime = MongoEPOCH($rides_details->row()->booking_information['actual_pickup_date']);
    
if($distance_unit == 'km'){
                                            
                                            
    if ($this->lang->line('rides_km_lower') != '') $distance_unit = stripslashes($this->lang->line('rides_km_lower')); else $distance_unit = 'km';

}else if($distance_unit == 'mi'){

    if ($this->lang->line('rides_mi_lower') != '') $distance_unit = stripslashes($this->lang->line('rides_mi_lower')); else $distance_unit = 'mi';

}
    
?>



<?php if($pickupTime != ''){ ?>
<script>
// Set the date we're counting down to
var countDownDate = new Date("<?php echo date('M d, Y H:i:s',$pickupTime);?>").getTime();

// Update the count down every 1 second
var x = setInterval(function() {

    // Get todays date and time
    var now = new Date().getTime();
    
    // Find the distance between now an the count down date
    var distance = countDownDate - now;
    
    // Time calculations for days, hours, minutes and seconds
    var days = Math.floor(distance / (1000 * 60 * 60 * 24));
    var hours = Math.floor((distance % (1000 * 60 * 60 * 24)) / (1000 * 60 * 60));
    var minutes = Math.floor((distance % (1000 * 60 * 60)) / (1000 * 60));
    var seconds = Math.floor((distance % (1000 * 60)) / 1000);
    
    
    if(days > 0){
        document.getElementById("pickup_timer").innerHTML = '<?php if ($this->lang->line('admin_trip_pickup_time') != '') echo stripslashes($this->lang->line('admin_trip_pickup_time')); else echo 'Trip pickup time expires in'; ?> ( <?php echo date('Y-m-d H:i:s',$pickupTime);?> ) :: <span style="color:orange; text-transform:lowercase;">  ' + days + " : " + hours + " : " + minutes + " : " + seconds+'s </span>';
    } else {
        document.getElementById("pickup_timer").innerHTML = '<?php if ($this->lang->line('admin_trip_pickup_time') != '') echo stripslashes($this->lang->line('admin_trip_pickup_time')); else echo 'Trip pickup time expires in'; ?> ( <?php echo date('Y-m-d H:i:s',$pickupTime);?> ) :: <span style="color:orange;">' + hours + " : " + minutes + " : " + seconds +'</span>';
    }
    
    // If the count down is over, write some text 
    if (distance < 0) {
        clearInterval(x);
        document.getElementById("pickup_timer").innerHTML = "<span style='color:orange;'> <?php if ($this->lang->line('admin_trip_pickup_time_expired') != '') echo stripslashes($this->lang->line('admin_trip_pickup_time_expired')); else echo 'Trip pickup time expired'; ?></span>";
    }
}, 1000);
</script>
<?php } ?>

<style>
#pickup_timer {
    float: right;
    
}
</style>

<div id="content">
    <div class="grid_container">
        <?php 
            $attributes = array('id' => 'display_form');
            echo form_open(OPERATOR_NAME.'/trip/display_rides',$attributes) 
        ?>
        <div class="grid_12">
            <div class="widget_wrap">
                <div class="widget_top">
                    <span class="h_icon blocks_images"></span>
                    <h6><?php echo $heading?></h6>
                    <?php if($pickupTime != '' && $rides_details->row()->ride_status == 'Booked' && $rides_details->row()->type != 'Now'){ ?><h6 id="pickup_timer"></h6> <?php } ?>
                </div>
                <div class="widget_content">
                    
                    <table class="display display_tbl" id="avail_drivers">
                    <thead>
                    <?php 
                    if ($this->lang->line('dash_click_sort') != '') $dash_click_sort = stripslashes($this->lang->line('dash_click_sort')); else $dash_click_sort = 'Click to sort'; ?>
                    <tr>
                        <th class="center" title="<?php echo $dash_click_sort; ?>">
                            <?php if ($this->lang->line('operator_s_no') != '') echo stripslashes($this->lang->line('operator_s_no')); else echo 'S.No'; ?>
                        </th>
                        <th class="tip_top" title="<?php echo $dash_click_sort; ?>">
                             <?php if ($this->lang->line('operator_driver_id') != '') echo stripslashes($this->lang->line('operator_driver_id')); else echo 'Driver Id'; ?>
                        </th>
                        <th class="tip_top" title="<?php echo $dash_click_sort; ?>">
                             <?php if ($this->lang->line('operator_name') != '') echo stripslashes($this->lang->line('operator_name')); else echo 'Name'; ?>
                        </th>
                        <th class="tip_top" title="<?php echo $dash_click_sort; ?>">
                            <?php if ($this->lang->line('operator_ratings') != '') echo stripslashes($this->lang->line('operator_ratings')); else echo 'Ratings'; ?>
                        </th>
                        <th class="tip_top" title="<?php echo $dash_click_sort; ?>">
                            <?php if ($this->lang->line('operator_total_rides') != '') echo stripslashes($this->lang->line('operator_total_rides')); else echo 'Total Rides'; ?>
                        </th>
                        <th class="tip_top" title="<?php echo $dash_click_sort; ?>">
                            <?php if ($this->lang->line('operator_distance') != '') echo stripslashes($this->lang->line('operator_distance')); else echo 'Distance'; ?>
                        </th>
                        <th>
                             <?php if ($this->lang->line('operator_action') != '') echo stripslashes($this->lang->line('operator_action')); else echo 'Action'; ?>
                        </th>
                    </tr>
                    </thead>
                    <tbody>
                    <?php 
                    if (count($driversList) > 0){ $i = 1;
                        foreach ($driversList as $row){
                    ?>
                    <tr>
                        <td class="center tr_select ">
                            <?php echo $i;?>
                        </td>							
                        <td class="center">
                            <?php  if(isset($row['_id'])) echo $row['_id'];?>
                        </td>
                        <td class="center">
                            <?php  if(isset($row['driver_name'])) echo $row['driver_name'];?>
                        </td>							
                        <td class="center">
                            <?php  if(isset($row['avg_review'])){ echo $row['avg_review']; }else{ if ($this->lang->line('admin_not_available') != '') echo stripslashes($this->lang->line('admin_not_available')); else echo 'N/A'; } ?>
                        </td>							
                        <td class="center">
                            <?php  if(isset($row['no_of_rides'])) echo $row['no_of_rides'];?>
                        </td>					
                        <td class="center">
                            <?php if (isset($row['distance'])) echo round($row['distance'], 2); ?> <?php echo $distance_unit; ?>
                        </td>
                        
                        <td class="center">
                            <ul class="action_list">
                                <li style="width:100%;">
                                    <a class="p_car tipTop" href="<?php echo OPERATOR_NAME; ?>/trip/assign_driver/<?php echo $rides_details->row()->ride_id;?>/<?php echo $row['_id'];?>" title="<?php if ($this->lang->line('admin_rides_assign_cab') != '') echo stripslashes($this->lang->line('admin_rides_assign_cab')); else echo 'Assign Cab'; ?>">
                                        <?php if ($this->lang->line('admin_send_req') != '') echo stripslashes($this->lang->line('admin_send_req')); else echo 'Send req'; ?>
                                    </a>
                                </li>
                            </ul>
                        </td>
                    </tr>
                    <?php 
                        $i++;
                        }
                    }
                    ?>
                    </tbody>
                    <tfoot>
                    <tr>
                        <th>
                            <?php if ($this->lang->line('operator_s_no') != '') echo stripslashes($this->lang->line('operator_s_no')); else echo 'S.No'; ?>
                        </th>
                        <th>
                             <?php if ($this->lang->line('operator_driver_id') != '') echo stripslashes($this->lang->line('operator_driver_id')); else echo 'Driver Id'; ?>
                        </th>
                        <th>
                             <?php if ($this->lang->line('operator_name') != '') echo stripslashes($this->lang->line('operator_name')); else echo 'Name'; ?>
                        </th>
                        <th>
                            <?php if ($this->lang->line('operator_ratings') != '') echo stripslashes($this->lang->line('operator_ratings')); else echo 'Ratings'; ?>
                        </th>
                        <th>
                            <?php if ($this->lang->line('operator_total_rides') != '') echo stripslashes($this->lang->line('operator_total_rides')); else echo 'Total Rides'; ?>
                        </th>
                        <th>
                            <?php if ($this->lang->line('operator_distance') != '') echo stripslashes($this->lang->line('operator_distance')); else echo 'Distance'; ?>
                        </th>
                        <th>
                             <?php if ($this->lang->line('operator_action') != '') echo stripslashes($this->lang->line('operator_action')); else echo 'Action'; ?>
                        </th>
                    </tr>
                    </tfoot>
                    </table>
                    
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


<?php 
$this->load->view(OPERATOR_NAME.'/templates/footer.php');
?>