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

                                    
?>

<script>
$(document).ready(function(){
   $('.vehicle_category').css("display","none");
   $(".vehicle_category").attr("disabled", true);
   $('#country').css("display","none");
   $("#country").attr("disabled", true);
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
		$(".vehicle_category").attr("disabled", false);
        $("#country").attr("disabled", true);
	}
    if($country != ''){
		$('#country').css("display","inline");
		$("#country").attr("disabled", false);
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
<div id="content" class="first-disply-list">
    <div class="grid_container">
	
		<div class="grid_12">
			<div class="">
					<div class="widget_content">
						<span class="clear"></span>						
						<div class="">
							<div class=" filter_wrap">
								<div class="widget_top filter_widget">
								
									<h6><?php if ($this->lang->line('admin_drivers_driver_filter') != '') echo stripslashes($this->lang->line('admin_drivers_driver_filter')); else echo 'Drivers Filter'; ?></h6>
									<div class="btn_30_light">
									<?php
									$attributes = array('class' => 'form_container left_label', 'id' => 'filter_form','method'=>'GET','autocomplete'=>'off','enctype' => 'multipart/form-data');
									echo form_open(ADMIN_ENC_URL.'/drivers/display_unregister_drivers_list', $attributes)
									?>
										<select class="form-control" id="filtertype" name="type" >
											<option value="" data-val="">
											<?php if ($this->lang->line('admin_drivers_select_filter_type') != '') echo stripslashes($this->lang->line('admin_drivers_select_filter_type')); else echo 'Select Filter Type'; ?></option>
											<option value="driver_name" data-val="driver_name" <?php if(isset($type)){if($type=='driver_name'){ echo 'selected="selected"'; } }?>>
											<?php if ($this->lang->line('admin_drivers_driver_name') != '') echo stripslashes($this->lang->line('admin_drivers_driver_name')); else echo 'Driver Name'; ?></option>
											<option value="email" data-val="email" <?php if(isset($type)){if($type=='email'){ echo 'selected="selected"'; } }?>><?php if ($this->lang->line('admin_drivers_change_password_driver_email') != '') echo stripslashes($this->lang->line('admin_drivers_change_password_driver_email')); else echo 'Driver Email'; ?></option>
											<option value="mobile_number" data-val="mobile_number" <?php if(isset($type)){if($type=='mobile_number'){ echo 'selected="selected"'; } }?>>
											<?php if ($this->lang->line('admin_drivers_phone_number') != '') echo stripslashes($this->lang->line('admin_drivers_phone_number')); else echo 'Driver PhoneNumber'; ?></option>
											<?php /*<option value="driver_location" data-val="location" <?php if(isset($type)){if($type=='driver_location'){ echo 'selected="selected"'; } }?>><?php if ($this->lang->line('admin_location_and_fare_location_location') != '') echo stripslashes($this->lang->line('admin_location_and_fare_location_location')); else echo 'Location'; ?></option> */?>
											<?php /*<option value="vehicle_type" data-val="vehicle_type" <?php if(isset($type)){if($type=='vehicle_type'){ echo 'selected="selected"'; } }?>><?php if ($this->lang->line('admin_drivers_vehicle_type') != '') echo stripslashes($this->lang->line('admin_drivers_vehicle_type')); else echo 'Vehicle Type'; ?></option> */ ?>
										</select>
                                         <?php /*  <select name="country" id="country"  class=" form-control" title="<?php if ($this->lang->line('please_enter_country') != '') echo stripslashes($this->lang->line('please_enter_country')); else echo 'Please choose your country';?>" style="display:none;">
                                        <?php 
                                        $country = '';
											if(isset($_GET['country']) && $_GET['country']!=''){
												$country = $_GET['country'];
											}
                                        
                                        foreach ($dialcode as $row) {
                                         
                                    
                                            if($country != '' && $country == $row){
													echo "<option selected value=".$row.">".$row."</option>";
												}else{
													echo "<option value=".$row.">".$row."</option>";
												}
                                        } ?>
                                       </select> */ ?>
									<input type="hidden" name="country" id="country" value="<?php echo $d_country_code; ?>" />
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
										<input name="value" id="filtervalue" type="text"  class="tipTop" title="<?php if ($this->lang->line('driver_enter_keyword') != '') echo stripslashes($this->lang->line('driver_enter_keyword')); else echo 'Please enter keyword'; ?>" value="<?php if(isset($value)) echo $value; ?>" placeholder="<?php if ($this->lang->line('driver_enter_keyword') != '') echo stripslashes($this->lang->line('driver_enter_keyword')); else echo 'Please enter keyword'; ?>" />
										<select name="vehicle_category" class='vehicle_category' style="display:none">
										<?php 
											$veh_cat = '';
											if(isset($_GET['vehicle_category']) && $_GET['vehicle_category']!=''){
												$veh_cat = $_GET['vehicle_category'];
											}
											foreach($cabCats as $cat){
												if($veh_cat != '' && $veh_cat == $cat->name){
													echo "<option selected value=".$cat->name.">".$cat->name."</option>";
												}else{
													echo "<option value=".$cat->name.">".$cat->name."</option>";
												}
												
											}
										?>
										</select>
                                     
								
										<button type="submit" class="tipTop filterbtn"  original-title="<?php if ($this->lang->line('driver_enter_keyword_filter') != '') echo stripslashes($this->lang->line('driver_enter_keyword_filter')); else echo 'Select filter type and enter keyword to filter'; ?>">
											<span class="icon search"></span><span class="btn_link"><?php if ($this->lang->line('admin_drivers_filter') != '') echo stripslashes($this->lang->line('admin_drivers_filter')); else echo 'Filter'; ?></span>
										</button>
										<?php if(isset($filter) && $filter!=""){ ?>
										<a href="<?php echo ADMIN_ENC_URL;?>/drivers/display_unregister_drivers_list"class="tipTop filterbtn" original-title="<?php if ($this->lang->line('admin_notification_remove_filter') != '') echo stripslashes($this->lang->line('admin_notification_remove_filter')); else echo 'Remove Filter'; ?>">
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
	
        <?php
        $attributes = array('id' => 'display_form');
        echo form_open(ADMIN_ENC_URL.'/drivers/change_driver_status_global', $attributes)
        ?>
        <div class="grid_12">
            <div class="widget_wrap">
                <div class="widget_top">
                    <span class="h_icon blocks_images"></span>
                    <h6><?php echo $heading ?></h6>
                </div>
                <div class="widget_content">
                    <?php
                    if ($paginationLink != '') {
                        echo $paginationLink;
                        $tble = 'unregisteredalldriverListTbl';
                    } else {
                        $tble = 'unregistereddriverListTbl';
                    }
                    ?>
				
                    <table class="display" id="<?php echo $tble; ?>" width='100%' style="overflow:scrolll">
                        <thead>
                            <tr>
							    <th class="tip_top">
                                    <?php if ($this->lang->line('operator_s_no') != '') echo stripslashes($this->lang->line('operator_s_no')); else echo 'S.No'; ?>
                                </th>
                                <th class="tip_top" title="<?php if ($this->lang->line('dash_click_sort') != '') echo stripslashes($this->lang->line('dash_click_sort')); else echo 'Click to sort'; ?>">
                                    <?php if ($this->lang->line('unregister_driver_name') != '') echo stripslashes($this->lang->line('unregister_driver_name')); else echo 'Name'; ?>
                                </th>
                                <th class="tip_top" title="<?php if ($this->lang->line('dash_click_sort') != '') echo stripslashes($this->lang->line('dash_click_sort')); else echo 'Click to sort'; ?>">
                                     <?php if ($this->lang->line('dash_driver_email') != '') echo stripslashes($this->lang->line('dash_driver_email')); else echo 'Email'; ?>
                                </th>
                                <th class="tip_top" title="<?php if ($this->lang->line('dash_click_sort') != '') echo stripslashes($this->lang->line('dash_click_sort')); else echo 'Click to sort'; ?>">
                                    <?php if ($this->lang->line('unregister_driver_mobilenumber') != '') echo stripslashes($this->lang->line('unregister_driver_mobilenumber')); else echo 'Mobile number'; ?>
                                </th>
                                <th class="tip_top" title="<?php if ($this->lang->line('dash_click_sort') != '') echo stripslashes($this->lang->line('dash_click_sort')); else echo 'Click to sort'; ?>">
                                    <?php if ($this->lang->line('unregister_driver_status') != '') echo stripslashes($this->lang->line('unregister_driver_status')); else echo 'Status'; ?>
                                </th>
                                <th>
                                    <?php if ($this->lang->line('unregister_driver_action') != '') echo stripslashes($this->lang->line('unregister_driver_action')); else echo 'Action'; ?>
                                </th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php $i = 0;
							///$data=count($driversList);
							#echo'<pre>';print_R($driversList);die;
                            if ($driversList->num_rows() > 0) {
                                foreach ($driversList->result() as $row) {
								$i++;
                                    ?>
                                    <tr style="border-bottom: 1px solid #dddddd !important;">
									     <td class="center tr_select ">
                                            <?php echo $i; ?>
                                        </td>
                                        <td class="center">
                                            <?php echo $row->driver_name; ?>
											<?php 
												
												$cTy ='';
												if(isset($row->category)){
													$catsId = (string)$row->category; 
													if(array_key_exists($catsId,$cabCats)){
														$cTy = $cabCats[$catsId]->name;
													}
												}
												if(isset($row->role)) echo '<br/><br/><span style="color:gray;">( '.$row->role.' )</span>';
												if($cTy != '') echo '<br/><br/><span style="color:gray;">'.$cTy.'</span>'; 
											?>
                                        </td>
                                        <td class="center">
                                            <?php
                                            if (isset($row->email)) {
                                                echo $row->email;
                                            }
                                            ?>
                                        </td>
										 <td class="center">
                                            <?php
                                            if (isset($row->dail_code)) {
                                                echo $row->dail_code;
                                            }
											echo ' ';
											if(isset($row->mobile_number)){
											    echo $row->mobile_number;
											}
                                            ?>
                                        </td>
										 <td class="center">
                                            <a title="<?php if ($this->lang->line('common_click_toactive') != '') echo stripslashes($this->lang->line('common_click_to_active')); else echo 'Click to send mail to Continue signup process for drivers'; ?>" class="tip_top" href="<?php echo ADMIN_ENC_URL;?>/drivers/send_email_to_unregister_drivers/<?php if(isset($row->_id)) echo $row->_id; ?>">
                                                        <span class="badge_style"><?php echo 'Send Link'; ?></span>
                                            </a>
                                        </td>

                                        <td class="center action-icons-wrap">
                                            <span><a class="action-icons c-suspend" href="<?php echo ADMIN_ENC_URL;?>/drivers/view_unregisterdriver/<?php echo $row->_id; ?>" title="<?php if ($this->lang->line('admin_common_view') != '') echo stripslashes($this->lang->line('admin_common_view')); else echo 'View'; ?>"></a></span>
											<span><a class="action-icons c-delete" href="javascript:confirm_delete('<?php echo ADMIN_ENC_URL;?>/drivers/delete_unregiser_driver/<?php echo $row->_id; ?>')" title="<?php if ($this->lang->line('admin_common_delete') != '') echo stripslashes($this->lang->line('admin_common_delete')); else echo 'Delete'; ?>"></a></span>
                                        </td>
                                    </tr>
                                    <?php
                                }
                            }
                            ?>
                        </tbody>
                        <tfoot>
                            <tr>
							    <th class="tip_top">
                                    <?php if ($this->lang->line('operator_s_no') != '') echo stripslashes($this->lang->line('operator_s_no')); else echo 'S.No'; ?>
                                </th>
                                <th>
                                    <?php if ($this->lang->line('unregister_driver_name') != '') echo stripslashes($this->lang->line('unregister_driver_name')); else echo 'Name'; ?>
                                </th>
                                <th>
                                     <?php if ($this->lang->line('dash_driver_email') != '') echo stripslashes($this->lang->line('dash_driver_email')); else echo 'Email'; ?>
                                </th>
                                <th>
                                    <?php if ($this->lang->line('unregister_driver_mobilenumber') != '') echo stripslashes($this->lang->line('unregister_driver_mobilenumber')); else echo 'Mobile Number'; ?>
                                </th>
								<th>
                                    <?php if ($this->lang->line('unregister_driver_status') != '') echo stripslashes($this->lang->line('unregister_driver_status')); else echo 'Status'; ?>
                                </th>
                                <th>
                                    <?php if ($this->lang->line('unregister_driver_action') != '') echo stripslashes($this->lang->line('unregister_driver_action')); else echo 'Action'; ?>
                                </th>
                            </tr>
                        </tfoot>
                    </table>

                    <?php
                    if ($paginationLink != '') {
                        echo $paginationLink;
                    }
                    ?>

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
	<style>										
			.b_warn {
				background: orangered none repeat scroll 0 0;
				border: medium none red;
			}
			
			.filter_widget .btn_30_light {
				margin: -11px;
				width: 83%;
			}
	</style>
<?php
$this->load->view(ADMIN_ENC_URL.'/templates/footer.php');
?>