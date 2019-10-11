<?php
$this->load->view(ADMIN_ENC_URL.'/templates/header.php');
extract($privileges);
?>
<script>
    $(document).ready(function () {
        $.validator.setDefaults({ ignore: ":hidden:not(select)" });
		$("#s_notification").validate();
		$("#s_email_form").validate();
		$("#sms_notification").validate();
    });
		
	function sendNotifi(){
		var tmplId = $('#sel_notification').val();
		if(tmplId != ''){
			$('.notification_cnt').css('display','block');
			var ntContent = $('#sel_notification').find(':selected').attr('data-cnt');		
			$('#notifytContent').html('<div class="form_grid_12 notification_container"><h3><?php if ($this->lang->line('notification_template_preview') != '') echo stripslashes($this->lang->line('notification_template_preview')); else echo 'Notification Template Preview'; ?></h3><p>'+ntContent+'</p></div>');
		} else {
			$('.notification_cnt').css('display','none');
			$('#notifytContent').html('');
		}
	}
	
	function sendEmail(){
		var tmplId = $('#sel_email').val();
		if(tmplId != ''){
			var ntContent = $('#sel_email').find(':selected').attr('data-cnt');
			$('.notification_cnt').css('display','block');
			$('#emailContent').html('<div class="form_grid_12 notification_container" ><h3><?php if ($this->lang->line('email_template_preview') != '') echo stripslashes($this->lang->line('email_template_preview')); else echo 'Email Template Preview'; ?></h3><p>'+ntContent+'</p></div>');
		} else {
			$('.notification_cnt').css('display','none');
			$('#emailContent').html('');
		}
	}
	
	function sendSMS(){
		var tmplId = $('#send_smsDrop').val();
		if(tmplId != ''){
			$('.notification_cnt').css('display','block');
			var ntContent = $('#send_smsDrop').find(':selected').attr('data-cnt');		
			$('#SmsContent').html('<div class="form_grid_12 notification_container"><h3><?php if ($this->lang->line('admin_message_text') != '') echo stripslashes($this->lang->line('admin_message_text')); else echo 'Message Text'; ?></h3><p>'+ntContent+'</p></div>');
		} else {
			$('.notification_cnt').css('display','none');
			$('#SmsContent').html('');
		}
	}
	
</script>
<?php
 $dialcode=array();

 foreach ($countryList as $country) {
    
     if ($country->dial_code != '') {
        
      $dialcode[]=str_replace(' ', '', $country->dial_code);  
       
       
       
     }
 }
 
   asort($dialcode);
   $dialcode=array_unique($dialcode);

   $location_id = $this->input->get('location_id');                             
?>

<script>
$(document).ready(function(){
   $vehicle_category='';
   $country='';
   <?php  if(isset($_GET['type']) && $_GET['type']!='' && isset($_GET['vehicle_category'])) {?>
	$vehicle_category = "<?php echo $_GET['vehicle_category']; ?>";
    <?php }?>
    <?php  if(isset($_GET['type']) && $_GET['type']!='' && isset($_GET['country'])) {?>
	$country = "<?php echo $_GET['country']; ?>";
    <?php }?>
    
    $location_id = '';
    <?php  if(isset($_GET['type']) && $_GET['type']!='' && isset($_GET['location_id'])) {?>
	$location_id = "<?php echo $_GET['location_id']; ?>";
    <?php }?>
    
    $type = '<?php echo $this->input->get('type'); ?>';
    
    $('.location_id').css("display","none");
    $('.vehicle_category').css("display","none");
    $('#filtervalue').css("display","inline");
    $("#country").css("display","none");
    $("#country").attr("disabled", true);
    $('.vehicle_category').attr("disabled", true);
    $('.location_id').attr("disabled", true);
    
	if($vehicle_category != '' && $type == 'vehicle_type'){
		$('.vehicle_category').css("display","inline");
		$('#filtervalue').css("display","none");
		$('.vehicle_category').attr("disabled", false);
        $('#filtervalue').css("display","none");
	}
    if($country != '' && $type == 'mobile_number'){
		$('#country').css("display","inline");
        $('#filtervalue').css("display","block");
        $("#country").attr("disabled", false);
	}
    
    if($location_id != '' && $type == 'driver_location'){
		$('.location_id').show();  
        $('.location_id').attr("disabled", false);
        $('#filtervalue').css("display","none");
	}
	$("#filtertype").change(function(){
		$filter_val = $(this).val(); 
        $('#filtervalue').val(''); 
		$('.vehicle_category').css("display","none");
        $('.location_id').css("display","none");
		$('#filtervalue').css("display","inline");
        $('#country').css("display","none");
        $("#country").attr("disabled", true);
        $(".location_id").attr("disabled", true);
        $(".vehicle_category").attr("disabled", true);
		if($filter_val == 'vehicle_type'){
			$('.vehicle_category').css("display","inline");
			$('#filtervalue').css("display","none");
            $('#country').css("display","none");
            $('.vehicle_category').attr("disabled", false);
            $("#country").attr("disabled", true);
		}
        
        if($filter_val == 'driver_location'){
			$('.location_id').css("display","inline");
            $('#filtervalue').css("display","none");
            $(".location_id").attr("disabled", false);
		}
        
        if($filter_val == 'mobile_number'){
			$('#country').css("display","inline");
			$('#country').attr("disabled", false);
            $(".vehicle_category").attr("disabled", true);
            $('.vehicle_category').css("display","none");
		}
		
	});
	
});
</script>
<div id="content">
    <div class="grid_container">
		
		<div class="grid_12">
			<div class="">
					<div class="widget_content">
						<span class="clear"></span>						
						<div class="">
							<div class=" filter_wrap">
								<div class="widget_top filter_widget">
								
									<h6><?php if ($this->lang->line('admin_notification_filter_drivers') != '') echo stripslashes($this->lang->line('admin_notification_filter_drivers')); else echo 'Filter Drivers'; ?></h6>
									<div class="btn_30_light">	
									<?php
									$attributes = array('class' => '', 'id' => 'filter_form','method'=>'GET','autocomplete'=>'off','enctype' => 'multipart/form-data');
									echo form_open(ADMIN_ENC_URL.'/notification/display_notification_driver_list', $attributes)
									?>
										<select class="form-control" id="filtertype" name="type" >
											<option value="" data-val=""><?php if ($this->lang->line('admin_notification_select_filter_type') != '') echo stripslashes($this->lang->line('admin_notification_select_filter_type')); else echo 'Select Filter Type'; ?></option>
											<option value="driver_name" data-val="driver_name" <?php if(isset($type)){if($type=='driver_name'){ echo 'selected="selected"'; } }?>><?php if ($this->lang->line('admin_notification_driver_name') != '') echo stripslashes($this->lang->line('admin_notification_driver_name')); else echo 'Driver Name'; ?></option>
											<option value="email" data-val="email" <?php if(isset($type)){if($type=='email'){ echo 'selected="selected"'; } }?>><?php if ($this->lang->line('admin_notification_driver_email') != '') echo stripslashes($this->lang->line('admin_notification_driver_email')); else echo 'Driver Email'; ?></option>
											<option value="mobile_number" data-val="mobile_number" <?php if(isset($type)){if($type=='mobile_number'){ echo 'selected="selected"'; } }?>><?php if ($this->lang->line('admin_notification_driver_phoneNumber') != '') echo stripslashes($this->lang->line('admin_notification_driver_phoneNumber')); else echo 'Driver PhoneNumber'; ?></option>
											<option value="driver_location" data-val="location" <?php if(isset($type)){if($type=='driver_location'){ echo 'selected="selected"'; } }?>><?php if ($this->lang->line('admin_notification_location') != '') echo stripslashes($this->lang->line('admin_notification_location')); else echo 'Location'; ?></option>
											<option value="vehicle_type" data-val="vehicle_type" <?php if(isset($type)){if($type=='vehicle_type'){ echo 'selected="selected"'; } }?>><?php if ($this->lang->line('admin_notification_vehicle_type') != '') echo stripslashes($this->lang->line('admin_notification_vehicle_type')); else echo 'Vehicle Type'; ?></option>
										</select>
                                        <select name="vehicle_category" class='vehicle_category' style="display:none" disabled>
										<?php 
											$veh_cat = '';
											if(isset($_GET['vehicle_category']) && $_GET['vehicle_category']!=''){
												$veh_cat = $_GET['vehicle_category'];
											}
											foreach($cabCats as $cat){
												
                                                
                                                $category_name = $cat->name;
												if(isset($cat->name_languages[$langCode ]) && $cat->name_languages[$langCode ] != '') $category_name = $cat->name_languages[$langCode ];
                                            
												if($veh_cat != '' && $veh_cat == $cat->_id){
													echo "<option selected value=".$cat->_id.">".$category_name."</option>";
												}else{
													echo "<option value=".$cat->_id.">".$category_name."</option>";
												}
												
											}
										?>
										</select>


                                        <select name="location_id" class='location_id' style="display:none">
                                            <option value=""><?php if ($this->lang->line('select_driver_loc') != '') echo stripslashes($this->lang->line('select_driver_loc')); else echo 'Select driver location'; ?></option>
										<?php 
											
											foreach($locationList->result() as $loc){
												if($location_id != '' && $location_id == (string)$loc->_id){
													echo "<option selected value=".(string)$loc->_id.">".$loc->city."</option>";
												}else{
													echo "<option value=".(string)$loc->_id.">".$loc->city."</option>";
												}
												
											}
										?>
										</select>
                                        
                                        
                                        <select name="country" id="country"  class=" form-control tipTop" title="<?php if ($this->lang->line('admin_location_and_fare_dial_code') != '') echo stripslashes($this->lang->line('admin_location_and_fare_dial_code')); else echo 'Dial Code'; ?>" style="display:none;" disabled >
                                        
                                        
                                        <?php 
                                        $country = '';
											if(isset($_GET['country']) && $_GET['country']!=''){
												$country = $_GET['country'];
											}
                                        
                                        foreach ($dialcode as $row) {
                                            //if($country != '' && $country == $row)
                                            if(empty($country)){
                                            	if($d_country_code==$row){
													echo "<option selected value=".$row.">".$row."</option>";
												}else{
													echo "<option value=".$row.">".$row."</option>";
												}
											}
											   if(!empty($country)){
                                                        if($country==$row)
                                                        {
                                                        echo "<option selected value=".$row.">".$row."</option>";
                                                        }else{
                                                        echo "<option value=".$row.">".$row."</option>";
                                                        }
                                                    }
                                        } ?>
                                       </select>
										<input name="value" id="filtervalue" type="text"  class="tipTop" title="<?php if ($this->lang->line('driver_enter_keyword') != '') echo stripslashes($this->lang->line('driver_enter_keyword')); else echo 'Please enter keyword'; ?>" value="<?php if(isset($value)) echo $value; ?>" placeholder="<?php if ($this->lang->line('driver_enter_keyword') != '') echo stripslashes($this->lang->line('driver_enter_keyword')); else echo 'Please enter keyword'; ?>" />
										<select name="dropdown_value" style="display:none"  id="filtervalue_dropdown" placeholder="Keyword" ></select>
										
										<button type="submit" class="tipTop filterbtn"  original-title="<?php if ($this->lang->line('driver_enter_keyword_filter') != '') echo stripslashes($this->lang->line('driver_enter_keyword_filter')); else echo 'Select filter type and enter keyword to filter'; ?>">
											<span class="icon search"></span><span class="btn_link"><?php if ($this->lang->line('admin_notification_filter') != '') echo stripslashes($this->lang->line('admin_notification_filter')); else echo 'Filter'; ?></span>
										</button>
										<?php if(isset($filter) && $filter!=""){ ?>
										<a href="<?php echo ADMIN_ENC_URL;?>/notification/display_notification_driver_list"class="tipTop filterbtn" original-title="<?php if ($this->lang->line('driver_enter_view_all_users') != '') echo stripslashes($this->lang->line('driver_enter_view_all_users')); else echo 'View All Users'; ?>">
											<span class="icon delete_co"></span><span class="btn_link"><?php if ($this->lang->line('admin_notification_remove_filter') != '') echo stripslashes($this->lang->line('admin_notification_remove_filter')); else echo 'Remove Filter'; ?></span>
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
        $attributes = array('id' => 'display_formss');
        echo form_open(ADMIN_ENC_URL.'/users/change_user_status_global', $attributes)
        ?>
        <div class="grid_12">
            <div class="widget_wrap">
                <div class="widget_top">
                    <span class="h_icon blocks_images"></span>
                    <h6><?php echo $heading ?></h6>
                    <div style="float: right;line-height:40px;padding:0px 10px;height:39px;">
                        <?php #if ($allPrev == '1' || in_array('2', $notification)) { ?>
						
						<div class="btn_30_light" style="height: 29px;">
                            <a class="o-modal tipTop" data-content="send_sms"  href="javascript:void(0)" original-title="<?php if ($this->lang->line('admin_notification_send_notify_sms_tooltip') != '') echo stripslashes($this->lang->line('admin_notification_send_notify_sms_tooltip')); else echo 'Select any checkbox and click here to send SMS'; ?>">
                                <!-- <span class="icon accept_co"></span> -->
                                <span  class="btn_link act-btn"><?php if ($this->lang->line('please_send_sms') != '') echo stripslashes($this->lang->line('please_send_sms')); else echo 'Send SMS'; ?></span>
                            </a>
                        </div>
						
                        <div class="btn_30_light" style="height: 29px;">
                            <a class="o-modal tipTop" data-content="send_notification"  href="javascript:void(0)" original-title="<?php if ($this->lang->line('admin_notification_send_notify_tooltip') != '') echo stripslashes($this->lang->line('admin_notification_send_notify_tooltip')); else echo 'Select any checkbox and click here to send Notification'; ?>">
                                <!-- <span class="icon accept_co"></span> -->
                                <span  class="btn_link act-btn"><?php if ($this->lang->line('admin_notification_send_notification') != '') echo stripslashes($this->lang->line('admin_notification_send_notification')); else echo 'Send Notification'; ?></span>
                            </a>

                        </div>
						<div class="btn_30_light" style="height: 29px;">
                            <a class="o-modal tipTop" data-content="send_emails"  href="javascript:void(0)" original-title="<?php if ($this->lang->line('admin_notification_send_notify_email_tooltip') != '') echo stripslashes($this->lang->line('admin_notification_send_notify_email_tooltip')); else echo 'Select any checkbox and click here to send Email'; ?>">
                                <!-- <span class="icon accept_co"></span> -->
                                <span  class="btn_link act-btn"><?php if ($this->lang->line('admin_notification_send_email') != '') echo stripslashes($this->lang->line('admin_notification_send_email')); else echo 'Send Email'; ?></span>
                            </a>

                        </div>

                    </div>
                </div>

                <div class="widget_content">
                    <?php
                    if ($paginationLink != '') {
                        echo $paginationLink;
                        $tble = 'NotificationListcustomdriver';
                    } else {
                        $tble = 'NotificationListdriver';
                    }
                    ?>

                    <table class="display" id="<?php echo $tble; ?>">
                        <thead>
                            <tr>
                                <th class="center">
                                    <input  name="checkbox_id[]" type="checkbox" value="on" class="checkallC">
                                </th>
                                <th class="tip_top" title="<?php if ($this->lang->line('dash_click_sort') != '') echo stripslashes($this->lang->line('dash_click_sort')); else echo 'Click to sort'; ?>">
                                    <?php if ($this->lang->line('admin_notification_driver_name') != '') echo stripslashes($this->lang->line('admin_notification_driver_name')); else echo 'Driver Name'; ?>
                                </th>
                                <th class="tip_top" title="<?php if ($this->lang->line('dash_click_sort') != '') echo stripslashes($this->lang->line('dash_click_sort')); else echo 'Click to sort'; ?>">
                                    <?php if ($this->lang->line('admin_notification_email') != '') echo stripslashes($this->lang->line('admin_notification_email')); else echo 'Email'; ?>
                                </th>
								<th class="tip_top" title="<?php if ($this->lang->line('dash_click_sort') != '') echo stripslashes($this->lang->line('dash_click_sort')); else echo 'Click to sort'; ?>">
                                    <?php if ($this->lang->line('admin_notification_device_type') != '') echo stripslashes($this->lang->line('admin_notification_device_type')); else echo 'Device Type'; ?>
                                </th>
                                <th class="tip_top" title="<?php if ($this->lang->line('dash_click_sort') != '') echo stripslashes($this->lang->line('dash_click_sort')); else echo 'Click to sort'; ?>">
                                    <?php if ($this->lang->line('admin_notification_ratings') != '') echo stripslashes($this->lang->line('admin_notification_ratings')); else echo 'Ratings'; ?>
                                </th>
                                <th class="tip_top" title="<?php if ($this->lang->line('dash_click_sort') != '') echo stripslashes($this->lang->line('dash_click_sort')); else echo 'Click to sort'; ?>">
                                    <?php if ($this->lang->line('admin_notification_verify_status') != '') echo stripslashes($this->lang->line('admin_notification_verify_status')); else echo 'Verify Status'; ?>
                                </th>
                                <th class="tip_top" title="<?php if ($this->lang->line('dash_click_sort') != '') echo stripslashes($this->lang->line('dash_click_sort')); else echo 'Click to sort'; ?>">
                                    <?php if ($this->lang->line('admin_common_status') != '') echo stripslashes($this->lang->line('admin_common_status')); else echo 'Status'; ?>
                                </th>

                            </tr>
                        </thead>
                        <tbody>
                            <?php
                            if ($driversList->num_rows() > 0) {
                                foreach ($driversList->result() as $row) {
                                    ?>
                                    <tr>

                                        <td class="center tr_select ">
                                            <input name="checkbox_id[]" class="user_mongo_id" type="checkbox" value="<?php echo $row->_id; ?>">
                                        </td>   
                                        <td class="center">
                                            <?php echo $row->driver_name; ?>
                                        </td>
                                        <td class="center">
                                            <?php if ($isDemo) { ?>
                                                <?php echo $dEmail; ?>
                                            <?php } else { ?>
                                                <?php echo $row->email; ?>
                                            <?php } ?>

                                        </td>
										
                                        <td class="center">
                                            <?php echo isset($row->push_notification['type']) ? get_language_value_for_keyword($row->push_notification['type'],$this->data['langCode']) : get_language_value_for_keyword('N/A',$this->data['langCode']); ?>
                                        </td>
                                        <td class="center">

                                            <?php if (isset($row->avg_review)) { ?>
                                                <a href="<?php echo ADMIN_ENC_URL;?>/reviews/view_driver_reviews/<?php echo $row->_id; ?>" title="<?php if ($this->lang->line('user_view_review_details') != '') echo stripslashes($this->lang->line('user_view_review_details')); else echo 'View review details'; ?>" class="tip_top"style="color:blue;"><?php echo $row->avg_review; ?> (<?php echo $row->total_review; ?>) </a>
                                            <?php } else { ?>
                                                <a> 0 (0) </a>
                                            <?php } ?>

                                        </td>

                                        <td class="center">
                                            <?php
                                            if (isset($row->verify_status) && strtolower($row->verify_status) == 'yes') {
                                                ?>
                                                <span class="badge_style b_done"><?php echo get_language_value_for_keyword($row->verify_status,$this->data['langCode']); ?></span>
                                                <?php
                                            } else if (isset($row->verify_status) && strtolower($row->verify_status) == 'no') {
                                                ?>
                                                <span class="badge_style b_notDone"><?php echo get_language_value_for_keyword($row->verify_status,$this->data['langCode']); ?></span>
                                                <?php
                                            } else {
                                                ?>
                                                <span class="badge_style b_notDone"><?php echo get_language_value_for_keyword('No',$this->data['langCode']); ?></span>
                                                <?php
                                            }
                                            ?>
                                        </td>
                                        <td class="center">
                                            <?php
                                            if (strtolower($row->status) == 'active') {
                                                ?>
                                                <span class="badge_style b_done"><?php echo get_language_value_for_keyword($row->status,$this->data['langCode']); ?></span>
                                                <?php
                                            } else {
                                                ?>
                                                <span class="badge_style b_notDone"><?php echo get_language_value_for_keyword($row->status,$this->data['langCode']); ?></span>
                                                <?php
                                            }
                                            ?>
                                            <?php
                                        }
                                    }
                                    ?>
                                </td>



                            </tr>

                        </tbody>
                        <tfoot>
                            <tr>
                                <th class="center">
                                    <input  name="checkbox_id[]" type="checkbox" value="on" class="checkallC">
                                </th>
                                <th>
                                    <?php if ($this->lang->line('admin_notification_driver_name') != '') echo stripslashes($this->lang->line('admin_notification_driver_name')); else echo 'Driver Name'; ?>
                                </th>
                                <th>
                                    <?php if ($this->lang->line('admin_notification_email') != '') echo stripslashes($this->lang->line('admin_notification_email')); else echo 'Email'; ?>
                                </th>
								
                                <th>
                                    <?php if ($this->lang->line('admin_notification_device_type') != '') echo stripslashes($this->lang->line('admin_notification_device_type')); else echo 'Device Type'; ?>
                                </th>
                                <th>
                                    <?php if ($this->lang->line('admin_notification_ratings') != '') echo stripslashes($this->lang->line('admin_notification_ratings')); else echo 'Ratings'; ?>
                                </th>
                                <th>
                                    <?php if ($this->lang->line('admin_notification_verify_status') != '') echo stripslashes($this->lang->line('admin_notification_verify_status')); else echo 'Verify Status'; ?>
                                </th>
                                <th>
                                    <?php if ($this->lang->line('admin_common_status') != '') echo stripslashes($this->lang->line('admin_common_status')); else echo 'Status'; ?>
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
        <div id="send_notification" style="display:none;">
            <h3><?php if ($this->lang->line('admin_notification_send_notification') != '') echo stripslashes($this->lang->line('admin_notification_send_notification')); else echo 'Send Notification'; ?></h3>
            <?php
            $attributes = array('class' => 'form_container left_label', 'id' => 's_notification', 'enctype' => 'multipart/form-data', 'method' => 'post');
            echo form_open_multipart(ADMIN_ENC_URL.'/notification/send_notification_to_device_driver', $attributes);
            ?>
            <ul>

                <li>
                    <input name="userIds" type="hidden" class="tdesc"  value=""/>
					 <input name="user_type" type="hidden"   value="driver"/>
                    <div class="form_grid_12">
                        <label class="field_title" style="width: 30%;"><?php if ($this->lang->line('admin_notification_choose_notification') != '') echo stripslashes($this->lang->line('admin_notification_choose_notification')); else echo 'Choose Notification'; ?> <span class="req">*</span></label>
                        <div class="form_input">
                          <select id="sel_notification" onchange="sendNotifi();" class="chzn-select1 required" name="notification_id"  style="width: 250px; display: none;" data-placeholder="<?php if ($this->lang->line('admin_notification_select_notification') != '') echo stripslashes($this->lang->line('admin_notification_select_notification')); else echo 'Select Notification'; ?>">
							<option value=""><?php if ($this->lang->line('admin_notification_select_notification') != '') echo stripslashes($this->lang->line('admin_notification_select_notification')); else echo 'Select Notification'; ?>....</option>
							<?php 
							
								foreach($template_details->result() as $temp_detail){
									if($temp_detail->notification_type=='notification'){
							?>
							<option value="<?php echo $temp_detail->news_id; ?>" data-cnt="<?php echo htmlentities($temp_detail->message['msg_description']) ?>"><?php echo $temp_detail->message['title'] ?></option>		
									<?php }
								}
							?>
							</select>
                        </div><button type="submit" class="btn_small btn_blue " ><span><?php if ($this->lang->line('admin_notification_send') != '') echo stripslashes($this->lang->line('admin_notification_send')); else echo 'Send'; ?></span></button>
                    </div>
                </li>
				<li class="notification_cnt" id="notifytContent">
					
				</li>
            </ul>
            <?php echo form_close(); ?>
        </div>
		<div id="send_emails" style="display:none;">
            <h3><?php if ($this->lang->line('admin_notification_send_email') != '') echo stripslashes($this->lang->line('admin_notification_send_email')); else echo 'Send Email'; ?></h3>
            <?php
            $attributes = array('class' => 'form_container left_label', 'id' => 's_email_form', 'enctype' => 'multipart/form-data', 'method' => 'post');
            echo form_open_multipart(ADMIN_ENC_URL.'/notification/send_email_to_users', $attributes);
            ?>
            <ul>

                 <li>
                    <input name="userIds" type="hidden" class="tdesc"  value=""/>
					 <input name="user_type" type="hidden"   value="driver"/>
                    <div class="form_grid_12">
                        <label class="field_title" style="width: 30%;"><?php if ($this->lang->line('admin_notification_choose_email_template') != '') echo stripslashes($this->lang->line('admin_notification_choose_email_template')); else echo 'Choose Email Template'; ?> <span class="req">*</span></label>
                        <div class="form_input">
                          <select id="sel_email" onchange="sendEmail();" class="chzn-select1 required" name="email_id"  style="width: 250px; display: none;" data-placeholder="<?php if ($this->lang->line('admin_notification_select_email_template') != '') echo stripslashes($this->lang->line('admin_notification_select_email_template')); else echo 'Select Email Template....'; ?>">
							<option value=""><?php if ($this->lang->line('admin_notification_select_email_template') != '') echo stripslashes($this->lang->line('admin_notification_select_email_template')); else echo 'Select Email Template....'; ?></option>
							<?php 
							
								foreach($template_details->result() as $temp_detail){
									if($temp_detail->notification_type=='email'){
							?>
							<option value="<?php echo $temp_detail->news_id; ?>" data-cnt="<?php echo htmlentities($temp_detail->message['mail_description']) ?>"><?php echo $temp_detail->message['title'] ?></option>		
									<?php }
								}
							?>
							</select>
                        </div>
						<button type="submit" class="btn_small btn_blue " ><span><?php if ($this->lang->line('admin_notification_send') != '') echo stripslashes($this->lang->line('admin_notification_send')); else echo 'Send'; ?></span></button>
                    </div>
                </li>
				<li class="notification_cnt" id="emailContent">
					
				</li>

            </ul>
            <?php echo form_close(); ?>
        </div>
    </div>
    <span class="clear"></span>
</div>
</div>


<div id="send_sms" style="display:none;">
	<h3><?php if ($this->lang->line('please_send_sms') != '') echo stripslashes($this->lang->line('please_send_sms')); else echo 'Send SMS'; ?></h3>
	<?php
	$attributes = array('class' => 'form_container left_label', 'id' => 'sms_notification', 'enctype' => 'multipart/form-data', 'method' => 'post');
	echo form_open_multipart(ADMIN_ENC_URL.'/notification/send_sms_to_device', $attributes);
	?>
	<ul>

		<li>
			<input name="userIds" type="hidden" class="tdesc"  value=""/>
		<input name="user_type" type="hidden"   value="driver"/>
			<div class="form_grid_12">
				<label class="field_title" style="width: 56%;"><?php if ($this->lang->line('please_choose_sms') != '') echo stripslashes($this->lang->line('please_choose_sms')); else echo 'Choose SMS'; ?><span class="req">*</span></label>
				<div class="form_input">
				<select id="send_smsDrop"  class="chzn-select1 required" onchange="sendSMS();" name="sms_id"  style="width: 320px;" >
					<option value=""><?php if ($this->lang->line('please_select_sms_template') != '') echo stripslashes($this->lang->line('please_select_sms_template')); else echo 'Select sms template'; ?>...</option>
					<?php foreach($template_details->result() as $temp_detail){
							if($temp_detail->notification_type=='sms'){
								?>
							<option value="<?php echo $temp_detail->news_id; ?>" data-cnt="<?php echo htmlentities($temp_detail->message['sms_description']) ?>"><?php echo $temp_detail->message['title'] ?></option>		
								<?php 
							}
						}
						?>
					</select>
				
				</div> <button type="submit" class="btn_small btn_blue " ><span><?php if ($this->lang->line('admin_notification_send') != '') echo stripslashes($this->lang->line('admin_notification_send')); else echo 'Send'; ?></span></button>
			</div>	
			
		</li>
		<li class="notification_cnt" id="SmsContent">
			
		</li>
	   
	</ul>
	<?php echo form_close(); ?>
</div>


<style>
.left_label ul li .form_input {
   float: left;
   width:auto;
}
.left_label ul li label.field_title {
    margin-right: 0;    
	width: 20%;
}
.mceLayout {
    min-width: 500px !important;
}
.btn_blue {
    background: #a7a9ac none repeat scroll 0 0;
    border: 1px solid #000;
    color: #fff;
    float: left;
    font-size: 12px;
    margin-right: 10px;
    margin-top: 0;
    padding: 8px 16px;
    text-shadow: 1px 1px 0 #000;
}
.notification_cnt {
    background-image: none;
    border: 1px solid gray !important;
    border-radius: 5px;
	display:none;
}
.notification_container {
    height: 193px;
    overflow: auto;
    width: 100% !important;
}
</style>

<script>


        $('.o-modal').click(function (e) {
            var contentId = $(this).attr("data-content");
            if ($(".tdesc").val() != '') {
                $('#' + contentId).modal({
				 onClose: function(dialog){
					location.reload();
					$.modal.close();
				 }
				});
            } else {
                alert("<?php if ($this->lang->line('admin_please_select_one_more_driver') != '') echo stripslashes($this->lang->line('admin_please_select_one_more_driver')); else echo 'Please select one or more driver to send notification'; ?>");
            }
            return false;
        });


    $(document).ready(function () {
		
		$(document).on('change', '.checkallC,.user_mongo_id', function() {
				var oTable = $('#<?php echo $tble; ?>').dataTable();
				var rowcollection =  oTable.$("input:checked", {"page": "all"});
				checkbox_value = [];
				rowcollection.each(function(index,elem){
					checkbox_value.push($(elem).val());
				});
				
				$(".tdesc").val(checkbox_value);
		});
		$(".checkallC").change(function(){
			$(".user_mongo_id").prop('checked', $(this).prop("checked"));
		});

        $(".media_image").change(function (e) {
            e.preventDefault();
            if (typeof (FileReader) != "undefined") {
                var image_holder = $("#image-holder");
                image_holder.empty();
                var reader = new FileReader();
                reader.onload = function (e) {

                    var res = e.target.result;
                    var ext = res.substring(11, 14);
                    extensions = ['jpg', 'jpe', 'gif', 'png', 'bmp'];
                    if ($.inArray(ext, extensions) !== -1) {
                        var image = new Image();
                        image.src = e.target.result;

                        image.onload = function () {
                            if (this.width >= 75 && this.height >= 42) {
                                $("#loadedImg").css("display", "none");
                                $("<img />", {
                                    "src": e.target.result,
                                    "id": "thumb-image",
                                    "style": "width:100px;height:100px;margin-top:20px",
                                }).appendTo(image_holder);
                                $('#ErrNotify').html('');




                            } else {
                                $('#ErrNotify').html('Upload Image Too Small. Please Upload Image Size More than or Equalto 75 X 42 .');
                            }
                        };
                    }
                    else {
                        $('#ErrNotify').html('Please Select an Image file');
                    }
                }
                image_holder.show();
                reader.readAsDataURL($(this)[0].files[0]);
            }
        });

 


    });


</script>
<?php
$this->load->view(ADMIN_ENC_URL.'/templates/footer.php');
?>