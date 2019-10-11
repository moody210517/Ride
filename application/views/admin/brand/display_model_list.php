<?php
$this->load->view(ADMIN_ENC_URL.'/templates/header.php');
extract($privileges);
?>
<div id="content">
		<div class="grid_container">
			<?php 
				$attributes = array('id' => 'display_form');
				echo form_open(ADMIN_ENC_URL.'/brand/change_model_status_global',$attributes) 
			?>
			<div class="grid_12">
				<div class="widget_wrap">
					<div class="widget_top">
						<span class="h_icon blocks_images"></span>
						<h6><?php echo $heading?></h6>
						<div style="float: right;line-height:40px;padding:0px 10px;height:39px;">
						<?php if ($allPrev == '1' || in_array('1', $brand)){?>
                        
							<div class="btn_30_light" style="height: 29px; text-align:left;">
								<a href="<?php echo ADMIN_ENC_URL;?>/brand/add_edit_model" class="tipTop" title="<?php if ($this->lang->line('make_model_add_new_model') != '') echo stripslashes($this->lang->line('make_model_add_new_model')); else echo 'Click here to Add New Model'; ?>"><span class="icon add_co addnew-btn2"></span><!-- <span class="btn_link"><?php if ($this->lang->line('admin_car_add_new') != '') echo stripslashes($this->lang->line('admin_car_add_new')); else echo 'Add New'; ?></span> --></a>
							</div>
                            

						<?php } ?>
						<?php if ($allPrev == '1' || in_array('2', $brand)){?>
							<div class="btn_30_light" style="height: 29px;">
								<a href="javascript:void(0)" onclick="return checkBoxValidationAdmin('Active','<?php echo $subAdminMail; ?>');" class="tipTop" title="<?php if ($this->lang->line('driver_select_publish_records') != '') echo stripslashes($this->lang->line('driver_select_publish_records')); else echo 'Select any checkbox and click here to publish records'; ?>"><!-- <span class="icon accept_co"></span> --><span class="btn_link act-btn"><?php if ($this->lang->line('admin_subadmin_active') != '') echo stripslashes($this->lang->line('admin_subadmin_active')); else echo 'Active'; ?></span></a>
							</div>
							<div class="btn_30_light" style="height: 29px;">
								<a href="javascript:void(0)" onclick="return checkBoxValidationAdmin('Inactive','<?php echo $subAdminMail; ?>');" class="tipTop" title="<?php if ($this->lang->line('driver_select_unpublish_records') != '') echo stripslashes($this->lang->line('driver_select_unpublish_records')); else echo 'Select any checkbox and click here to unpublish records'; ?>"><!-- <span class="icon delete_co"></span> --><span class="btn_link inact-btn"><?php if ($this->lang->line('admin_subadmin_inactive') != '') echo stripslashes($this->lang->line('admin_subadmin_inactive')); else echo 'Inactive'; ?></span></a>
							</div>
						<?php 
						}
						if ($allPrev == '1' || in_array('3', $brand)){
						?>
							<div class="btn_30_light" style="height: 29px;">
								<a href="javascript:void(0)" onclick="return checkBoxValidationAdmin('Delete','<?php echo $subAdminMail; ?>');" class="tipTop" title="<?php if ($this->lang->line('driver_select_delete_records') != '') echo stripslashes($this->lang->line('driver_select_delete_records')); else echo 'Select any checkbox and click here to delete records'; ?>"><!--<span class="icon cross_co del-btn"></span>--><span class="btn_link"><?php if ($this->lang->line('admin_subadmin_delete') != '') echo stripslashes($this->lang->line('admin_subadmin_delete')); else echo 'Delete'; ?></span></a>
							</div>
						<?php }?>
						</div>
					</div>
					<div class="widget_content">
						<table class="display display_tbl" id="model_tbl">
							<thead>
								<tr>
									<th class="center">
										<input name="checkbox_id[]" type="checkbox" value="on" class="checkall">
									</th>
									<th class="tip_top" title="<?php if ($this->lang->line('dash_click_sort') != '') echo stripslashes($this->lang->line('dash_click_sort')); else echo 'Click to sort'; ?>">
										 <?php if ($this->lang->line('admin_make_and_model_model_list_model_name') != '') echo stripslashes($this->lang->line('admin_make_and_model_model_list_model_name')); else echo 'Model Name'; ?>
									</th>
									<th class="tip_top" title="<?php if ($this->lang->line('dash_click_sort') != '') echo stripslashes($this->lang->line('dash_click_sort')); else echo 'Click to sort'; ?>">
										 <?php if ($this->lang->line('admin_make_and_model_model_list_model_type') != '') echo stripslashes($this->lang->line('admin_make_and_model_model_list_model_type')); else echo 'Model Type'; ?>
									</th>
									<th class="tip_top" title="<?php if ($this->lang->line('dash_click_sort') != '') echo stripslashes($this->lang->line('dash_click_sort')); else echo 'Click to sort'; ?>">
										<?php if ($this->lang->line('admin_make_and_model_model_list_maker') != '') echo stripslashes($this->lang->line('admin_make_and_model_model_list_maker')); else echo 'Maker'; ?>
									</th>
									<th class="tip_top" title="<?php if ($this->lang->line('dash_click_sort') != '') echo stripslashes($this->lang->line('dash_click_sort')); else echo 'Click to sort'; ?>">
										<?php if ($this->lang->line('admin_subadmin_status') != '') echo stripslashes($this->lang->line('admin_subadmin_status')); else echo 'Status'; ?>
									</th>
									<th>
										 <?php if ($this->lang->line('admin_subadmin_action') != '') echo stripslashes($this->lang->line('admin_subadmin_action')); else echo 'Action'; ?>
									</th>
								</tr>
							</thead>
							<tbody>
								<?php 
								if ($modelList->num_rows() > 0){
									foreach ($modelList->result() as $row){
								?>
								<tr>
									<td class="center tr_select ">
										<input name="checkbox_id[]" type="checkbox" value="<?php echo $row->_id;?>">
									</td>
									<td class="center">
										<?php echo $row->name;?>
									</td>
									<td class="center">
										<?php 
										$model_type='not available'; 
										if(isset($availableVehicles[$row->type])){	
											$model_type=$availableVehicles[$row->type];
										}
										?>
									<?php echo $model_type;?>
									</td>
									<td class="center">
									<?php echo $row->brand_name;?>
									</td>
									<td class="center">
									<?php 
									$disp_status = get_language_value_for_keyword($row->status,$this->data['langCode']);
									if ($allPrev == '1' || in_array('2', $brand)){
										$mode = ($row->status == 'Active')?'0':'1';
										if ($mode == '0'){
									?>
										<a title="<?php if ($this->lang->line('admin_click_to_unpublish') != '') echo stripslashes($this->lang->line('admin_click_to_unpublish')); else echo 'Click to unpublish'; ?>" class="tip_top" href="javascript:confirm_status('<?php echo ADMIN_ENC_URL;?>/brand/change_model_status/<?php echo $mode;?>/<?php echo $row->_id;?>');"><span class="badge_style b_done"><?php echo $disp_status;?></span></a>
									<?php
										}else {	
									?>
										<a title="<?php if ($this->lang->line('admin_click_to_publish') != '') echo stripslashes($this->lang->line('admin_click_to_publish')); else echo 'Click to publish'; ?>" class="tip_top" href="javascript:confirm_status('<?php echo ADMIN_ENC_URL;?>/brand/change_model_status/<?php echo $mode;?>/<?php echo $row->_id;?>')"><span class="badge_style"><?php echo $disp_status;?></span></a>
									<?php 
										}
									}else {
									?>
									<span class="badge_style b_done"><?php echo $disp_status;?></span>
									<?php }?>
									</td>
									<td class="center action-icons-wrap">
									<?php if ($allPrev == '1' || in_array('2', $brand)){?>
										<span><a class="action-icons c-edit" href="<?php echo ADMIN_ENC_URL;?>/brand/add_edit_model/<?php echo $row->_id;?>" title="<?php if ($this->lang->line('admin_common_edit') != '') echo stripslashes($this->lang->line('admin_common_edit')); else echo 'Edit'; ?>"><?php if ($this->lang->line('admin_common_edit') != '') echo stripslashes($this->lang->line('admin_common_edit')); else echo 'Edit'; ?></a></span>
									<?php }?>
									<?php if ($allPrev == '1' || in_array('3', $brand)){?>	
										<span><a class="action-icons c-delete" href="javascript:confirm_delete('<?php echo ADMIN_ENC_URL;?>/brand/delete_model/<?php echo $row->_id;?>')" title="<?php if ($this->lang->line('admin_common_delete') != '') echo stripslashes($this->lang->line('admin_common_delete')); else echo 'Delete'; ?>"><?php if ($this->lang->line('admin_common_delete') != '') echo stripslashes($this->lang->line('admin_common_delete')); else echo 'Delete'; ?></a></span>
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
										 <?php if ($this->lang->line('admin_make_and_model_model_list_model_name') != '') echo stripslashes($this->lang->line('admin_make_and_model_model_list_model_name')); else echo 'Model Name'; ?>
									</th>
									<th>
										 <?php if ($this->lang->line('admin_make_and_model_model_list_model_type') != '') echo stripslashes($this->lang->line('admin_make_and_model_model_list_model_type')); else echo 'Model Type'; ?>
									</th>
									<th>
										<?php if ($this->lang->line('admin_make_and_model_model_list_maker') != '') echo stripslashes($this->lang->line('admin_make_and_model_model_list_maker')); else echo 'Maker'; ?>
									</th>
									<th>
										<?php if ($this->lang->line('admin_subadmin_status') != '') echo stripslashes($this->lang->line('admin_subadmin_status')); else echo 'Status'; ?>
									</th>
									<th>
										<?php if ($this->lang->line('admin_subadmin_action') != '') echo stripslashes($this->lang->line('admin_subadmin_action')); else echo 'Action'; ?> 
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
$this->load->view(ADMIN_ENC_URL.'/templates/footer.php');
?>