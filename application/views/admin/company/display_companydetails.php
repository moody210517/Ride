<?php
$this->load->view(ADMIN_ENC_URL.'/templates/header.php'); 
extract($privileges); 	
$this->load->helper('export_helper');
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
    $("#export_report").click(function(event){     
		event.preventDefault();
		var query_strings = "<?php echo $_SERVER["QUERY_STRING"]; ?>";
		window.location.href = "<?php echo ADMIN_ENC_URL;?>/company/display_companylist?" + query_strings + "&export=excel";
	});
    
   $('#country').css("display","none");
   $("#country").attr("disabled", true);
   $country='';
   $locations_id = '';
    <?php  if(isset($_GET['country'])) {?>
    $country = "<?php echo $_GET['country']; ?>";
    <?php }?>
    <?php  if(isset($type) && $type == 'location' &&  isset($_GET['locations_id'])) {?>
	$locations_id = "<?php echo $_GET['locations_id']; ?>";
    <?php }?>
    if($country != ''){
		$('#country').css("display","inline");
		$("#country").attr("disabled", false);
	}
	if($locations_id != ''){
		$('#country').css("display","none");
		$('#filtervalue').css("display","none");
		$('#locations_id').css("display","inline");
	}
    $("#filtertype").change(function(){ 
		$filter_val = $(this).val();  
        $('#filtervalue').val('');
		$('#filtervalue').css("display","inline");
        $('#country').css("display","none");
        $("#country").attr("disabled", true);
        $('#locations_id').css('display','none');
		
        if($filter_val == 'mobile_number'){
			$('#country').css("display","inline");
			$('#country').prop("disabled", false);
		}
        
		if($filter_val == 'location'){  
			$('#country').css("display","none");
			$('#country').prop("disabled", false);
			$('#filtervalue').css("display","none");
			$('#locations_id').css('display','inline');
		}
	});
    
});  
    
</script>    

<div id="content">
		<div class="grid_container">
        
                <?php 
                        $attributes = array('class' => 'form_container left_label', 'id' => 'filter_form','method'=>'GET','autocomplete'=>'off','style' => 'width: 100% !important;');
						echo form_open(ADMIN_ENC_URL.'/company/display_companylist',$attributes) 
				?>
                
                <div class="grid_12">
                    <div class="widget_wrap">
                        <div class="widget_top filter_widget">
                                <div class="btn_30_light new_compdet">
                                    <div class="filterwrap">
                                        <span class="title">Filter Type</span>
                                        <select class="form-control" id="filtertype" name="type">
                                            <option value="" data-val="">
                                            <?php if ($this->lang->line('admin_operator_select_filter_type') != '') echo stripslashes($this->lang->line('admin_operator_select_filter_type')); else echo 'Select Filter Type'; ?></option>
                                            <option value="company_name" data-val="company_name" <?php if(isset($type)){if($type=='company_name'){ echo 'selected="selected"'; } }?>>
                                            <?php if ($this->lang->line('admin_company_name') != '') echo stripslashes($this->lang->line('admin_company_name')); else echo 'Company Name'; ?></option>
                                            <option value="email" data-val="email" <?php if(isset($type)){if($type=='email'){ echo 'selected="selected"'; } }?>><?php if ($this->lang->line('admin_company_email') != '') echo stripslashes($this->lang->line('admin_company_email')); else echo 'Company Email'; ?></option>
                                            <option value="mobile_number" data-val="mobile_number" <?php if(isset($type)){if($type=='mobile_number'){ echo 'selected="selected"'; } }?>>
                                            <?php if ($this->lang->line('admin_company_phone_number') != '') echo stripslashes($this->lang->line('admin_company_phone_number')); else echo 'Company PhoneNumber'; ?></option>
                                            <option value="location" data-val="location" <?php if(isset($type)){if($type=='location'){ echo 'selected="selected"'; } }?>>
                                            <?php if ($this->lang->line('admin_company_location') != '') echo stripslashes($this->lang->line('admin_company_location')); else echo 'Company Location'; ?></option>
                                        </select>
                                    </div>
                                        
                                        <div class="filterwrap filter-loc-section" >
                                            <select name="country" id="country"  style="display:none; width:85px !important;" class=" form-control" title="<?php if ($this->lang->line('please_enter_country') != '') echo stripslashes($this->lang->line('please_enter_country')); else echo 'Please choose your country';?>" style="display:none;">
                                            <?php 
                                                $country = '';
                                                if(isset($_GET['country']) && $_GET['country']!=''){
                                                $country = $_GET['country'];
                                                }
                                                    foreach ($dialcode as $row) {
                                                       // if($country != '' && $country == $row){
                                                        if(empty($country)){
                                                        if($d_country_code==$row)
                                                        {
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
                                            <select name="locations_id" class='locationsList' id="locations_id" style="display:none; width:320px !important;">
                                                    <option value=""><?php if ($this->lang->line('admin_driver_filter_choose_loc') != '') echo stripslashes($this->lang->line('admin_driver_filter_choose_loc')); else echo 'Choose location'; ?>...</option>
                                                        <?php 
                                                            $loc_id = '';
                                                            if(isset($_GET['locations_id']) && $_GET['locations_id']!=''){
                                                                $loc_id = $_GET['locations_id'];
                                                            }
                                                            foreach($locationsList->result() as $loc){
                                                                if($loc_id != '' && $loc_id == (string)$loc->city){
                                                                    echo "<option selected value=".(string)$loc->city.">".$loc->city."</option>";
                                                                }else{
                                                                    echo "<option value=".(string)$loc->city.">".$loc->city."</option>";
                                                                }
                                                                
                                                            }
                                                        ?>
                                                </select>
                                            
                                            <div class = "filterwrap search-box">
                                                <input name="value" id="filtervalue" type="text"  class="tipTop" title="<?php if ($this->lang->line('driver_enter_keyword') != '') echo stripslashes($this->lang->line('driver_enter_keyword')); else echo 'Please enter keyword'; ?>" value="<?php if(isset($value)) echo $value; ?>" placeholder="<?php if ($this->lang->line('driver_enter_keyword') != '') echo stripslashes($this->lang->line('driver_enter_keyword')); else echo 'Please enter keyword'; ?>" /> 
                                            </div>
                                        </div>
                                        <div class="filterwrap f-submint">
                                                <button type="submit" class="tipTop filterbtn"  original-title="<?php if ($this->lang->line('driver_enter_keyword_filter') != '') echo stripslashes($this->lang->line('driver_enter_keyword_filter')); else echo 'Select filter type and enter keyword to filter'; ?>">
                                                    <span class="icon search"></span><span class="btn_link"><?php if ($this->lang->line('admin_drivers_filter') != '') echo stripslashes($this->lang->line('admin_drivers_filter')); else echo 'Filter'; ?></span>
                                                </button>
                                                
                                            <?php if(isset($filter) && $filter!=""){ ?>
                                                <a href="<?php echo ADMIN_ENC_URL;?>/company/display_companylist"class="tipTop filterbtn" style = "background-color:#e84c3d  !important;" original-title="<?php if ($this->lang->line('admin_notification_remove_filter') != '') echo stripslashes($this->lang->line('admin_notification_remove_filter')); else echo 'Remove Filter'; ?>">
                                                    <span class="icon delete_co" style ="background-color:#e84c3d  !important;"></span>
                                                </a>
                                            <?php } ?>
                                            </form>
                                        </div>
                                </div> 
                        </div>    
                    </div>
                </div>
        
			<?php 
				$attributes = array('id' => 'display_form');
				echo form_open(ADMIN_ENC_URL.'/company/change_company_status_global',$attributes) 
			?>
			<div class="grid_12">
				<div class="widget_wrap">
					<div class="widget_top">
						<span class="h_icon blocks_images"></span>
						<h6><?php echo $heading?></h6>
						
						<div style="float: right;line-height:40px;padding:0px 10px;height:39px;">
						<div class="btn_30_light" style="height: 29px;">
								<a href="<?php echo COMPANY_NAME; ?>"  target="_blank" class="tipTop" title="<?php if ($this->lang->line('admin_company_login_tooltip') != '') echo stripslashes($this->lang->line('admin_company_login_tooltip')); else echo 'Click here to login company panel'; ?>"><span class="btn_link" style="border-left:none!important"><?php if ($this->lang->line('admin_company_login') != '') echo stripslashes($this->lang->line('admin_company_login')); else echo 'Company Login'; ?></span></a>
						</div>
						<?php if ($allPrev == '1' || in_array('1', $company)){?>
							<div class="btn_30_light" style="height: 29px; text-align:left;">
									<a href="<?php echo ADMIN_ENC_URL;?>/company/add_edit_company" class="tipTop" title="<?php if ($this->lang->line('admin_operators_add_new_company') != '') echo stripslashes($this->lang->line('admin_operators_add_new_company')); else echo 'Click here to Add New Company'; ?>"><span class="icon add_co addnew-btn2"></span><!-- <span class="btn_link"><?php if ($this->lang->line('admin_common_add_new') != '') echo stripslashes($this->lang->line('admin_common_add_new')); else echo 'Add New'; ?></span> --></a>
							</div>
						<?php } ?>
						
						<?php if ($allPrev == '1' || in_array('2', $company)){?>
							<div class="btn_30_light" style="height: 29px;">
								<a href="javascript:void(0)" onclick="return checkBoxValidationAdmin('Active','<?php echo $subAdminMail; ?>');" class="tipTop" title="<?php if ($this->lang->line('common_select_active_records') != '') echo stripslashes($this->lang->line('common_select_active_records')); else echo 'Select any checkbox and click here to active records'; ?>"><!-- <span class="icon accept_co"></span> --><span class="btn_link act-btn"><?php if ($this->lang->line('admin_partner_active') != '') echo stripslashes($this->lang->line('admin_partner_active')); else echo 'Active'; ?></span></a>
							</div>
							<div class="btn_30_light" style="height: 29px;">
								<a href="javascript:void(0)" onclick="return checkBoxValidationAdmin('Inactive','<?php echo $subAdminMail; ?>');" class="tipTop" title="<?php if ($this->lang->line('common_select_inactive_records') != '') echo stripslashes($this->lang->line('common_select_inactive_records')); else echo 'Select any checkbox and click here to inactive records'; ?>"><!-- <span class="icon delete_co"></span> --><span class="btn_link inact-btn"><?php if ($this->lang->line('admin_partner_inactive') != '') echo stripslashes($this->lang->line('admin_partner_inactive')); else echo 'Inactive'; ?></span></a>
							</div>
						<?php 
						}
						if ($allPrev == '1' || in_array('3', $company)){
						?>
							<div class="btn_30_light" style="height: 29px;">
								<a href="javascript:void(0)" onclick="return checkBoxValidationAdmin('Delete','<?php echo $subAdminMail; ?>');" class="tipTop" title="<?php if ($this->lang->line('common_select_delete_records') != '') echo stripslashes($this->lang->line('common_select_delete_records')); else echo 'Select any checkbox and click here to delete records'; ?>"><!--<span class="icon cross_co del-btn"></span>--><span class="btn_link"><?php if ($this->lang->line('admin_partner_delete') != '') echo stripslashes($this->lang->line('admin_partner_delete')); else echo 'Delete'; ?></span></a>
							</div>
						<?php }?>
                        
                        <?php if ($allPrev == '1') { ?>
						<?php if($companylist->num_rows() > 0){ ?>
                            <div class="btn_30_light" style="height: 29px;">
                                <a style="color:#fff;height: 25px !important;font-size: 12px !important;" class="p_edit tipTop export_report" id="export_report"><?php if ($this->lang->line('admin_export') != '') echo stripslashes($this->lang->line('admin_export')); else echo 'Export'; ?></a>
						<?php } ?>
                            </div>
						<?php } ?>
                        
						</div>
					</div>
					<div class="widget_content">
						<table class="display display_tbl" id="company_tbl">
							<thead>
								<tr>
									<th class="center">
										<input name="checkbox_id[]" type="checkbox" value="on" class="checkall">
									</th>
									<th class="tip_top" title="<?php if ($this->lang->line('dash_click_sort') != '') echo stripslashes($this->lang->line('dash_click_sort')); else echo 'Click to sort'; ?>">
										 <?php if ($this->lang->line('admin_partner_company_name') != '') echo stripslashes($this->lang->line('admin_partner_company_name')); else echo 'Company Name'; ?>
									</th>
									<th class="tip_top" title="<?php if ($this->lang->line('dash_click_sort') != '') echo stripslashes($this->lang->line('dash_click_sort')); else echo 'Click to sort'; ?>">
										 <?php if ($this->lang->line('admin_partner_phone_number') != '') echo stripslashes($this->lang->line('admin_partner_phone_number')); else echo 'Phone Number'; ?>
									</th>
						
									<th class="tip_top" title="<?php if ($this->lang->line('dash_click_sort') != '') echo stripslashes($this->lang->line('dash_click_sort')); else echo 'Click to sort'; ?>">
										<?php if ($this->lang->line('admin_partner_city') != '') echo stripslashes($this->lang->line('admin_partner_city')); else echo 'City'; ?>
									</th>
                                   <th class="tip_top" title="<?php if ($this->lang->line('dash_click_sort') != '') echo stripslashes($this->lang->line('dash_click_sort')); else echo 'Click to sort'; ?>">
										<?php if ($this->lang->line('admin_partner_email') != '') echo stripslashes($this->lang->line('admin_partner_email')); else echo 'Email'; ?>
									</th>
                           
									<th class="tip_top" title="<?php if ($this->lang->line('dash_click_sort') != '') echo stripslashes($this->lang->line('dash_click_sort')); else echo 'Click to sort'; ?>">
										<?php if ($this->lang->line('admin_partner_status') != '') echo stripslashes($this->lang->line('admin_partner_status')); else echo 'Status'; ?> 
									</th>
									<th><?php if ($this->lang->line('admin_common_action') != '') echo stripslashes($this->lang->line('admin_common_action')); else echo 'Action'; ?>
										 
									</th>
								</tr>
							</thead>
							<tbody>
								<?php 
								if ($companylist->num_rows() > 0){  
									foreach ($companylist->result() as $row){ 
                                   
								?> 
								<tr>
									<td class="center tr_select ">
										<input name="checkbox_id[]" type="checkbox" value="<?php echo $row->_id;?>">
									</td> 
									<td class="center">
                                       <?php if(isset($row->company_name)){ echo $row->company_name; }else{ echo 'not available'; } ?>
										
									</td>
									<td class="center">
										<?php if(isset($row->phonenumber)){ echo $row->dail_code.$row->phonenumber; }else{ echo 'not available'; } ?>
									</td>
							
                           			<td class="center">
										<?php if(isset($row->locality['city'])){ echo $row->locality['city']; }else{ echo 'not available'; } ?>

									</td>
                          
									<td class="center">
										<?php if($isDemo){ ?>
										<?php echo $dEmail; ?>
										<?php }  else{ ?>
										<?php if(isset($row->email)){ echo $row->email; }else{ echo 'not available'; } ?>
										<?php } ?>
									</td>
									<td class="center">
									<?php 
									$disp_status = get_language_value_for_keyword($row->status,$this->data['langCode']);
									if ($allPrev == '1' || in_array('2', $company)){
										$mode = ($row->status == 'Active')?'0':'1';
										if ($mode == '0'){
									?>
										<a title="<?php if ($this->lang->line('common_click_inactive') != '') echo stripslashes($this->lang->line('common_click_inactive')); else echo 'Click to inactive'; ?>" class="tip_top" href="javascript:confirm_status('<?php echo ADMIN_ENC_URL;?>/company/change_company_status/<?php echo $mode;?>/<?php echo $row->_id;?>');"><span class="badge_style b_done"><?php echo $disp_status;?></span></a>
									<?php
										}else {	
									?>
										<a title="<?php if ($this->lang->line('common_click_active') != '') echo stripslashes($this->lang->line('common_click_active')); else echo 'Click to active'; ?>" class="tip_top" href="javascript:confirm_status('<?php echo ADMIN_ENC_URL;?>/company/change_company_status/<?php echo $mode;?>/<?php echo $row->_id;?>')"><span class="badge_style"><?php echo $disp_status;?></span></a>
									<?php 
										}
									}else {
									?>
									<span class="badge_style b_done"><?php echo $disp_status;?></span>
									<?php }?>
									</td>
									<td class="center action-icons-wrap">
									
										<?php if ($allPrev == '1' || in_array('0', $company)){?>
										<span><a class="action-icons c-suspend" href="<?php echo ADMIN_ENC_URL;?>/company/view_company/<?php echo $row->_id;?>" title="<?php if ($this->lang->line('admin_common_view') != '') echo stripslashes($this->lang->line('admin_common_view')); else echo 'View'; ?>"><?php if ($this->lang->line('admin_common_view') != '') echo stripslashes($this->lang->line('admin_common_view')); else echo 'View'; ?></a></span>
										<?php }?>
										<?php if ($allPrev == '1' || in_array('2', $company)){?>
										<span><a class="action-icons c-edit" href="<?php echo ADMIN_ENC_URL;?>/company/add_edit_company/<?php echo $row->_id;?>" title="<?php if ($this->lang->line('admin_common_edit') != '') echo stripslashes($this->lang->line('admin_common_edit')); else echo 'Edit'; ?>"><?php if ($this->lang->line('admin_common_edit') != '') echo stripslashes($this->lang->line('admin_common_edit')); else echo 'Edit'; ?></a></span>
										<span><a class="action-icons c-key" href="<?php echo ADMIN_ENC_URL;?>/company/change_password_form/<?php echo $row->_id;?>" title="<?php if ($this->lang->line('admin_user_change_password') != '') echo stripslashes($this->lang->line('admin_user_change_password')); else echo 'Change Password'; ?>"><?php if ($this->lang->line('admin_partner_confirm_password') != '') echo stripslashes($this->lang->line('admin_partner_confirm_password')); else echo 'confirm Password'; ?></a></span>
                                        <?php }?>
                                        <?php if ($allPrev == '1' || in_array('3', $company)){?>	
                                            <span><a class="action-icons c-delete" href="javascript:confirm_delete('<?php echo ADMIN_ENC_URL;?>/company/delete_companyprofile/<?php echo $row->_id;?>')" title="<?php if ($this->lang->line('admin_common_delete') != '') echo stripslashes($this->lang->line('admin_common_delete')); else echo 'Delete'; ?>"><?php if ($this->lang->line('admin_partner_delete') != '') echo stripslashes($this->lang->line('admin_partner_delete')); else echo 'Delete'; ?></a></span>
                                        <?php }?>
									</td>
								</tr>
								<?php 
									}
								}
								?>
							</tbody>
							<tfoot>
								<tr>
									<th class="center">
										<input name="checkbox_id[]" type="checkbox" value="on" class="checkall">
									</th>
									<th>
										 <?php if ($this->lang->line('admin_partner_company_name') != '') echo stripslashes($this->lang->line('admin_partner_company_name')); else echo 'Company Name'; ?>
									</th>
									<th>
										  <?php if ($this->lang->line('admin_partner_phone_number') != '') echo stripslashes($this->lang->line('admin_partner_phone_number')); else echo 'Phone Number'; ?>
									</th>
								
									<th>
										<?php if ($this->lang->line('admin_partner_city') != '') echo stripslashes($this->lang->line('admin_partner_city')); else echo 'City'; ?>
									</th>
									<th>
										<?php if ($this->lang->line('admin_partner_email') != '') echo stripslashes($this->lang->line('admin_partner_email')); else echo 'Email'; ?>
									</th>
                                   <th>
										<?php if ($this->lang->line('admin_partner_status') != '') echo stripslashes($this->lang->line('admin_partner_status')); else echo 'Status'; ?> 
									</th>
									<th>
										 <?php if ($this->lang->line('admin_common_action') != '') echo stripslashes($this->lang->line('admin_common_action')); else echo 'Action'; ?>
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

	<style>										
			.b_warn {
				background: orangered none repeat scroll 0 0;
				border: medium none red;
			}		     
            .filterwrap.f-submint {
                width: auto;
                margin-top: 20px;
            }
            .filterwrap.filter-loc-section {
                width: 40%;
                 margin-top: 20px;
            }
            .filterwrap.filter-type-sec select {
                width: 100% !important;
            }
            .filterwrap.f-submint {
                margin-left: 20px;
            }
            .btn_30_light.second-row {
                width: 100% !important;
                margin-top: 0 !important;
            }
            .filterwrap.filter-loc-section #locations_id {
                width: 70% !important;
            }
            .filterwrap.filter-type-sec {
                width: 15%;
            }
            .filterwrap.filter-loc-section #filtervalue {
                width: 100% !important;
            }
            .filterwrap {
                width: 40%;
                overflow: hidden;
                padding-right: 10px;
                float: left;
            }
            .btn_30_light span {
                display: inline-block;
                float: left;
                width:100%;
            }
            .filterwrap .title {
                color: #fff;
                padding: 5px;
                height: 20px;
            }
            .export_report {
                background: #be3b0a none repeat scroll 0 0 !important;
                float: right;
                margin-right: 15px;                
                border-radius: 3px !important;
                color:#ffffff !important; 
            }
            .widget_top.filter_widget {
                background: #4f5973 !important;
                height: auto !important;
                padding: 11px 8px;
            }
            .filterwrap.search-box {
                width: 100% !important;
            }
            .filter_widget select {
                width: 180px !important;
            }
            
</style>            
<?php 
$this->load->view(ADMIN_ENC_URL.'/templates/footer.php');
?>