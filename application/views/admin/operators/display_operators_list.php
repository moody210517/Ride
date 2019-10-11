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
		window.location.href = "<?php echo ADMIN_ENC_URL;?>/operators/display_operators_list?" + query_strings + "&export=excel";
	});
    
      $('#country').css("display","none");
      $("#country").attr("disabled", true);
      $country='';
        <?php  if(isset($_GET['country'])) {?>
        $country = "<?php echo $_GET['country']; ?>";
        <?php }?>
        
        if($country != ''){
            $('#country').css("display","inline");
            $("#country").attr("disabled", false);
        }
   
        $("#filtertype").change(function(){  
            $filter_val = $(this).val();  
            $('#filtervalue').val('');
            $('#filtervalue').css("display","inline");
            $('#country').css("display","none");
            $("#country").attr("disabled", true);
          
            if($filter_val == 'mobile_number'){ 
                $('#country').css("display","inline");
                $('#country').prop("disabled", false);
            }
        });
 });
 
 </script>

<div id="content">
		<div class="grid_container">
                <?php 
                        $attributes = array('class' => 'form_container left_label', 'id' => 'filter_form','method'=>'GET','autocomplete'=>'off','enctype' => 'multipart/form-data','style' => 'width: 100% !important;');
						echo form_open(ADMIN_ENC_URL.'/operators/display_operators_list',$attributes) 
				?>
                
                <div class="grid_12">
                    <div class="widget_wrap">
                        <div class="widget_top filter_widget">
                                <div class="btn_30_light">
                                    <div class="filterwrap">
                                        <span class="title">Filter Type</span>
                                        <select class="form-control" id="filtertype" name="type">
                                            <option value="" data-val="">
                                            <?php if ($this->lang->line('admin_operator_select_filter_type') != '') echo stripslashes($this->lang->line('admin_operator_select_filter_type')); else echo 'Select Filter Type'; ?></option>
                                            <option value="operator_name" data-val="operator_name" <?php if(isset($type)){if($type=='operator_name'){ echo 'selected="selected"'; } }?>>
                                            <?php if ($this->lang->line('admin_operator_name') != '') echo stripslashes($this->lang->line('admin_operator_name')); else echo 'Operator Name'; ?></option>
                                            <option value="email" data-val="email" <?php if(isset($type)){if($type=='email'){ echo 'selected="selected"'; } }?>><?php if ($this->lang->line('admin_operator_email') != '') echo stripslashes($this->lang->line('admin_operator_email')); else echo 'Operator Email'; ?></option>
                                            <option value="mobile_number" data-val="mobile_number" <?php if(isset($type)){if($type=='mobile_number'){ echo 'selected="selected"'; } }?>>
                                            <?php if ($this->lang->line('admin_operator_phone_number') != '') echo stripslashes($this->lang->line('admin_operator_phone_number')); else echo 'Operator PhoneNumber'; ?></option>
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
                                                        if($d_country_code==$row){
                                                        echo "<option selected value=".$row.">".$row."</option>";
                                                        }else{
                                                        echo "<option value=".$row.">".$row."</option>";
                                                        }
                                                    }
                                                     if(!empty($country)){
                                                        if($country==$row){
                                                        echo "<option selected value=".$row.">".$row."</option>";
                                                        }else{
                                                        echo "<option value=".$row.">".$row."</option>";
                                                        }
                                                    }
                                                    } ?>
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
                                            <a href="<?php echo ADMIN_ENC_URL;?>/operators/display_operators_list"class="tipTop filterbtn" style = "background-color:#e84c3d  !important;" original-title="<?php if ($this->lang->line('admin_notification_remove_filter') != '') echo stripslashes($this->lang->line('admin_notification_remove_filter')); else echo 'Remove Filter'; ?>">
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
						echo form_open(ADMIN_ENC_URL.'/operators/change_operator_status_global',$attributes) 
				?>
				<div class="grid_12">
						<div class="widget_wrap">
								<div class="widget_top">
										<span class="h_icon blocks_images"></span>
												<h6><?php echo $heading?></h6>
												
												<div style="float: right;line-height:40px;padding:0px 10px;height:39px;">
                                                    <div class="btn_30_light" style="height: 29px;">
                                                        <a href="<?php echo OPERATOR_NAME; ?>"  target="_blank" class="tipTop" title="<?php if ($this->lang->line('admin_operator_login_tooltip') != '') echo stripslashes($this->lang->line('admin_operator_login_tooltip')); else echo 'Click here to login operator panel'; ?>"><span class="btn_link " style="border-left:none!important"><?php if ($this->lang->line('admin_operator_login') != '') echo stripslashes($this->lang->line('admin_operator_login')); else echo 'Operator Login'; ?></span></a>
                                                    </div>
                                                        <?php if ($allPrev == '1' || in_array('1', $operators)){?>
                                                                <div class="btn_30_light" style="height: 29px; text-align:left;">
                                                                        <a href="<?php echo ADMIN_ENC_URL;?>/operators/add_edit_operator_form" class="tipTop" title="<?php if ($this->lang->line('admin_operators_add_new_operator') != '') echo stripslashes($this->lang->line('admin_operators_add_new_operator')); else echo 'Click here to Add New Operator'; ?>"><span class="icon add_co addnew-btn"></span><!-- <span class="btn_link"><?php if ($this->lang->line('admin_common_add_new') != '') echo stripslashes($this->lang->line('admin_common_add_new')); else echo 'Add New'; ?></span> --></a>
                                                                </div>
                                                        <?php } ?>
												
                                                        <?php if ($allPrev == '1' || in_array('2', $operators)){?>
                                                                <div class="btn_30_light" style="height: 29px;">
                                                                        <a href="javascript:void(0)" onclick="return checkBoxValidationAdmin('Active','<?php echo $subAdminMail; ?>');" class="tipTop" title="<?php if ($this->lang->line('common_select_active_records') != '') echo stripslashes($this->lang->line('common_select_active_records')); else echo 'Select any checkbox and click here to active records'; ?>">
                                                                        <!-- <span class="icon accept_co"></span> -->
                                                                        <span class="btn_link act-btn"><?php if ($this->lang->line('admin_common_active') != '') echo stripslashes($this->lang->line('admin_common_active')); else echo 'Active'; ?></span></a>
                                                                </div>
                                                                <div class="btn_30_light" style="height: 29px;">
                                                                        <a href="javascript:void(0)" onclick="return checkBoxValidationAdmin('Inactive','<?php echo $subAdminMail; ?>');" class="tipTop" title="<?php if ($this->lang->line('common_select_inactive_records') != '') echo stripslashes($this->lang->line('common_select_inactive_records')); else echo 'Select any checkbox and click here to inactive records'; ?>"><!-- <span class="icon delete_co"></span> --><span class="btn_link inact-btn"><?php if ($this->lang->line('admin_common_inactive') != '') echo stripslashes($this->lang->line('admin_common_inactive')); else echo 'Inactive'; ?></span></a>
                                                                </div>
                                                        <?php }
                                                            if ($allPrev == '1' || in_array('3', $operators)){
                                                            ?>
                                                                <div class="btn_30_light" style="height: 29px;">
                                                                    <a href="javascript:void(0)" onclick="return checkBoxValidationAdmin('Delete','<?php echo $subAdminMail; ?>');" class="tipTop" title="<?php if ($this->lang->line('common_select_delete_records') != '') echo stripslashes($this->lang->line('common_select_delete_records')); else echo 'Select any checkbox and click here to delete records'; ?>"><!--<span class="icon cross_co del-btn"></span>--> <span class="btn_link"><?php if ($this->lang->line('admin_partner_delete') != '') echo stripslashes($this->lang->line('admin_partner_delete')); else echo 'Delete'; ?></span></a>
                                                                </div>
                                                            <?php }?>
                                                            
                                                            <?php if ($allPrev == '1') { ?>
                                                                <?php if($operatorsList->num_rows() > 0){ ?>
                                                               <div class="btn_30_light" style="height: 29px;">
                                                                    <a style="color:#fff;height: 27px !important;font-size: 12px !important;" class="p_edit tipTop export_report" id="export_report"><?php if ($this->lang->line('admin_export') != '') echo stripslashes($this->lang->line('admin_export')); else echo 'Export'; ?></a>
                                                                <?php } ?>
                                                                </div>
                                                                <?php } ?>
                                                </div>
                                </div>
							<div class="widget_content">
									<table class="display display_tbl" id="operator_tbl_admin">
											<thead>
													<tr>
															<th class="center">
																	<input name="checkbox_id[]" type="checkbox" value="on" class="checkall">
															</th>
															<th class="tip_top" title="<?php if ($this->lang->line('dash_click_sort') != '') echo stripslashes($this->lang->line('dash_click_sort')); else echo 'Click to sort'; ?>">
																	<?php if ($this->lang->line('admin_operator_name') != '') echo stripslashes($this->lang->line('admin_operator_name')); else echo 'Operator Name'; ?>
															</th>
															<th class="tip_top" title="<?php if ($this->lang->line('dash_click_sort') != '') echo stripslashes($this->lang->line('dash_click_sort')); else echo 'Click to sort'; ?>">
																	<?php if ($this->lang->line('admin_operator_email') != '') echo stripslashes($this->lang->line('admin_operator_email')); else echo 'Operator Email'; ?>
															</th>
															<th class="tip_top" title="<?php if ($this->lang->line('dash_click_sort') != '') echo stripslashes($this->lang->line('dash_click_sort')); else echo 'Click to sort'; ?>">
																	<?php if ($this->lang->line('cms_phone') != '') echo stripslashes($this->lang->line('cms_phone')); else echo 'Phone Number'; ?>
															</th>
															<th class="tip_top" title="<?php if ($this->lang->line('dash_click_sort') != '') echo stripslashes($this->lang->line('dash_click_sort')); else echo 'Click to sort'; ?>">
																	<?php if ($this->lang->line('admin_driver_list_doj') != '') echo stripslashes($this->lang->line('admin_driver_list_doj')); else echo 'DOJ'; ?> 
															</th>
															<th class="tip_top" title="<?php if ($this->lang->line('dash_click_sort') != '') echo stripslashes($this->lang->line('dash_click_sort')); else echo 'Click to sort'; ?>">
																	<?php if ($this->lang->line('admin_common_status') != '') echo stripslashes($this->lang->line('admin_common_status')); else echo 'Status'; ?> 
															</th>
															<th>
																 <?php if ($this->lang->line('admin_common_action') != '') echo stripslashes($this->lang->line('admin_common_action')); else echo 'Action'; ?>
															</th>
													</tr>
											</thead>
											<tbody>
													<?php 
													if ($operatorsList->num_rows() > 0){
														foreach ($operatorsList->result() as $row){
													?>
													<tr>
															<td class="center tr_select">
																	<input name="checkbox_id[]" type="checkbox" value="<?php echo $row->_id;?>">
															</td>
															<td class="center">
																	<?php echo $row->operator_name;?>
															</td>
															<td class="center">
																	<?php echo $row->email;?>
															</td>
															<td class="center">
																	<?php echo $row->dail_code.$row->mobile_number;?>
															</td>														
															<td class="center">
																	<?php echo get_time_to_string('Y-m-d',strtotime($row->created)); ?>
															</td>
														
															<td class="center">
																	<?php 
																	$disp_status = get_language_value_for_keyword($row->status,$this->data['langCode']);
																	if ($allPrev == '1' || in_array('2', $operators)){
																			$mode = ($row->status == 'Active')?'0':'1';
																			if ($mode == '0'){
																	?>
																					<a title="<?php if ($this->lang->line('common_click_inactive') != '') echo stripslashes($this->lang->line('common_click_inactive')); else echo 'Click to inactive'; ?>" class="tip_top" href="javascript:confirm_status('<?php echo ADMIN_ENC_URL;?>/operators/change_operator_status/<?php echo $mode;?>/<?php echo $row->_id;?>');"><span class="badge_style b_done"><?php echo $disp_status;?></span></a>
																	<?php
																			}else {	
																	?>
																					<a title="<?php if ($this->lang->line('common_click_active') != '') echo stripslashes($this->lang->line('common_click_active')); else echo 'Click to active'; ?>" class="tip_top" href="javascript:confirm_status('<?php echo ADMIN_ENC_URL;?>/operators/change_operator_status/<?php echo $mode;?>/<?php echo $row->_id;?>')"><span class="badge_style"><?php echo $disp_status;?></span></a>
																	<?php 
																			}
																	}else {
																	?>
																			<span class="badge_style b_done"><?php echo $disp_status;?></span>
																	<?php }?>
															</td>
															<td class="center action-icons-wrap">
																	<span>
																			<a class="action-icons c-suspend" href="<?php echo ADMIN_ENC_URL;?>/operators/view_operator/<?php echo $row->_id; ?>" title="<?php if ($this->lang->line('admin_common_view') != '') echo stripslashes($this->lang->line('admin_common_view')); else echo 'View'; ?>">
																					<?php if ($this->lang->line('admin_common_view') != '') echo stripslashes($this->lang->line('admin_common_view')); else echo 'View'; ?>
																			</a>
																	</span>
																	
																	<?php if ($allPrev == '1' || in_array('2', $operators)){?>
																			<span><a class="action-icons c-key" href="<?php echo ADMIN_ENC_URL;?>/operators/change_password_form/<?php echo $row->_id; ?>" title="<?php if ($this->lang->line('admin_user_change_password') != '') echo stripslashes($this->lang->line('admin_user_change_password')); else echo 'Change Password'; ?>"><?php if ($this->lang->line('driver_change_password') != '') echo stripslashes($this->lang->line('driver_change_password')); else echo 'Change Password'; ?></a></span>
																	<?php }?>
																	
																	<?php if ($allPrev == '1' || in_array('2', $operators)){?>
																	<span>
																			<a class="action-icons c-edit" href="<?php echo ADMIN_ENC_URL;?>/operators/add_edit_operator_form/<?php echo $row->_id;?>" title="<?php if ($this->lang->line('admin_common_edit') != '') echo stripslashes($this->lang->line('admin_common_edit')); else echo 'Edit'; ?>">
																					<?php if ($this->lang->line('admin_common_edit') != '') echo stripslashes($this->lang->line('admin_common_edit')); else echo 'Edit'; ?>
																			</a>
																	</span>
																	<?php }?>
																	
																	<?php if ($allPrev == '1' || in_array('3', $operators)){?>	
																	<span>
																			<a class="action-icons c-delete" href="javascript:confirm_delete('<?php echo ADMIN_ENC_URL;?>/operators/delete_operator/<?php echo $row->_id;?>')" title="<?php if ($this->lang->line('admin_common_delete') != '') echo stripslashes($this->lang->line('admin_common_delete')); else echo 'Delete'; ?>">
																					<?php if ($this->lang->line('admin_common_delete') != '') echo stripslashes($this->lang->line('admin_common_delete')); else echo 'Delete'; ?>
																			</a>
																	</span>
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
																<?php if ($this->lang->line('admin_operator_name') != '') echo stripslashes($this->lang->line('admin_operator_name')); else echo 'Operator Name'; ?>
															</th>
															<th>
																<?php if ($this->lang->line('admin_operator_email') != '') echo stripslashes($this->lang->line('admin_operator_email')); else echo 'Operator Email'; ?>
															</th>
															<th>
																<?php if ($this->lang->line('cms_phone') != '') echo stripslashes($this->lang->line('cms_phone')); else echo 'Phone Number'; ?>
															</th>
															<th>
																<?php if ($this->lang->line('admin_driver_list_doj') != '') echo stripslashes($this->lang->line('admin_driver_list_doj')); else echo 'DOJ'; ?>
															</th>
															<th>
																<?php if ($this->lang->line('admin_common_status') != '') echo stripslashes($this->lang->line('admin_common_status')); else echo 'Status'; ?> 
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
    .filterwrap .title {
        color: #fff;
        padding: 5px;
        height: 20px;
    }    
    .filterwrap {
        width: 20%;
        overflow: visible;
        padding-right: 10px;
        float: left;
    }    
    #filtervalue {
        width: 100% !important;
        border-radius: 3px;
        height: 30px;
        line-height: 30px;
    }    
    .filterwrap.search-box{ 
        margin-top: 20px;
    }    
    .filter_widget .btn_30_light {
        margin: 0 !important;
        width: 100% !important;
    }    
    div#content .filter_wrap .filter_widget select {
        border-radius: 3px;
        border: none;
        height: 30px !important;
        padding: 5px !important;
        float: left;
        margin-right: 12px;
    }    
    div#content select {
        border-radius: 3px;
        height: 30px !important;
        padding-left: 10px;
        color: #444;
        line-height: 31px;
    }    
    .filter_widget select {
        width: 187px !important;
    }    
    .filterwrap.f-submint {
        margin-left: 20px;
    }
    .filterwrap.f-submint {
        width: auto;
        margin-top: 20px;
    }    
    .filterwrap.filter-loc-section {
        width: 100% !important;
        float: initial;
    }    
    .filter_widget select#country {
        width: 70px !important;
        display: inline;
        margin-top: 20px;
    }
    button.tipTop.filterbtn {
        background-color: #e84c3d !important;
        height: 30px !important;
        padding: 6px 27px !important;
        float: left;
        margin: 0;
        margin-right: 12px;
        border: none;
        border-radius: 3px;
    }    
    .export_report {
        background: #be3b0a none repeat scroll 0 0 !important;
        float: right;
        margin-right: 15px;                
        border-radius: 3px !important;
        color:#ffffff !important; 
    }

</style>
<?php 
$this->load->view(ADMIN_ENC_URL.'/templates/footer.php');
?>