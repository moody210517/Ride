<?php
$this->load->view(ADMIN_ENC_URL.'/templates/header.php');
extract($privileges);
?>
<div id="content">
		<div class="grid_container">
			<?php 
				$attributes = array('id' => 'display_form');
				echo form_open(ADMIN_ENC_URL.'/cancellation/change_cancellation_status_global',$attributes) 
			?>
			<div class="grid_12">
				<div class="widget_wrap">
					<div class="widget_top">
						<span class="h_icon blocks_images"></span>
						<h6><?php echo $heading?></h6>
						<div style="float: right;line-height:40px;padding:0px 10px;height:39px;">
						<?php if ($allPrev == '1' || in_array('1', $cancellation)){?>
                        
							<div class="btn_30_light" style="height: 29px; text-align:left;">
								<a href="<?php echo ADMIN_ENC_URL;?>/cancellation/add_edit_cancellation_type/<?php echo $type; ?>" class="tipTop" title="<?php if ($this->lang->line('admin_cancellation_add_new_reason') != '') echo stripslashes($this->lang->line('admin_cancellation_add_new_reason')); else echo 'Click here to Add New Reason'; ?>"><span class="icon add_co addnew-btn2"></span><!-- <span class="btn_link"><?php if ($this->lang->line('admin_common_add_new') != '') echo stripslashes($this->lang->line('admin_common_add_new')); else echo 'Add New'; ?></span> --></a>
							</div>
                            

						<?php } ?>
						<?php if ($allPrev == '1' || in_array('2', $cancellation)){?>
							<div class="btn_30_light" style="height: 29px;">
								<a href="javascript:void(0)" onclick="return checkBoxValidationAdmin('Active','<?php echo $subAdminMail; ?>');" class="tipTop" title="<?php if ($this->lang->line('common_select_active_records') != '') echo stripslashes($this->lang->line('common_select_active_records')); else echo 'Select any checkbox and click here to active records'; ?>"><!-- <span class="icon accept_co"></span> --><span class="btn_link"><?php if ($this->lang->line('admin_common_active') != '') echo stripslashes($this->lang->line('admin_common_active')); else echo 'Active'; ?></span></a>
							</div>
							<div class="btn_30_light" style="height: 29px;">
								<a href="javascript:void(0)" onclick="return checkBoxValidationAdmin('Inactive','<?php echo $subAdminMail; ?>');" class="tipTop" title="<?php if ($this->lang->line('common_select_inactive_records') != '') echo stripslashes($this->lang->line('common_select_inactive_records')); else echo 'Select any checkbox and click here to inactive records'; ?>"><!-- <span class="icon delete_co"></span> --><span class="btn_link inact-btn"><?php if ($this->lang->line('admin_common_inactive') != '') echo stripslashes($this->lang->line('admin_common_inactive')); else echo 'Inactive'; ?></span></a>
							</div>
						<?php 
						}
						if ($allPrev == '1' || in_array('3', $cancellation)){
						?>
							<div class="btn_30_light" style="height: 29px;">
								<a href="javascript:void(0)" onclick="return checkBoxValidationAdmin('Delete','<?php echo $subAdminMail; ?>');" class="tipTop" title="<?php if ($this->lang->line('driver_select_delete_records') != '') echo stripslashes($this->lang->line('driver_select_delete_records')); else echo 'Select any checkbox and click here to delete records'; ?>"><!--<span class="icon cross_co del-btn"></span>--><span class="btn_link"><?php if ($this->lang->line('admin_common_delete') != '') echo stripslashes($this->lang->line('admin_common_delete')); else echo 'Delete'; ?></span></a>
							</div>
						<?php }?>
						</div>
					</div>
					<div class="widget_content">
						<table class="display display_tbl" id="cancellation_tbl">
							<thead>
								<tr>
									<th class="center">
										<input name="checkbox_id[]" type="checkbox" value="on" class="checkall">
									</th>
									<th class="tip_top" title="<?php if ($this->lang->line('dash_click_sort') != '') echo stripslashes($this->lang->line('dash_click_sort')); else echo 'Click to sort'; ?>">
										<?php if ($this->lang->line('admin_cancellation_cancellation_id') != '') echo stripslashes($this->lang->line('admin_cancellation_cancellation_id')); else echo 'Cancellation Id'; ?> 
									</th>
									<th class="tip_top" title="<?php if ($this->lang->line('dash_click_sort') != '') echo stripslashes($this->lang->line('dash_click_sort')); else echo 'Click to sort'; ?>">
										<?php if ($this->lang->line('admin_cancellation_reason') != '') echo stripslashes($this->lang->line('admin_cancellation_reason')); else echo 'Reason'; ?>
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
								if ($cancellationTypes->num_rows() > 0){
									foreach ($cancellationTypes->result() as $row){
								?>
								<tr>
									<td class="center tr_select ">
										<input name="checkbox_id[]" type="checkbox" value="<?php echo $row->_id;?>">
									</td>
									<td class="center">
										<?php echo (string)$row->_id;?>
									</td>
									<td class="center">
										<?php $reason = $row->reason;
										if(isset($row->name_languages[$langCode]) && $row->name_languages[$langCode] != '')  $reason = $row->name_languages[$langCode];
										echo $reason;
										?>
									</td>
									<td class="center">
									<?php 
									$disp_status = get_language_value_for_keyword($row->status,$this->data['langCode']);
									if ($allPrev == '1' || in_array('2', $cancellation)){
										$mode = ($row->status == 'Active')?'0':'1';
										if ($mode == '0'){
									?>
										<a title="<?php if ($this->lang->line('common_click_to_inactive') != '') echo stripslashes($this->lang->line('common_click_to_inactive')); else echo 'Click to inactive'; ?>" class="tip_top" href="javascript:confirm_status('<?php echo ADMIN_ENC_URL;?>/cancellation/change_reason_status/<?php echo $mode;?>/<?php echo $row->_id;?>');"><span class="badge_style b_done"><?php echo $disp_status;?></span></a>
									<?php
										}else {	
									?>
										<a title="<?php if ($this->lang->line('common_click_to_active') != '') echo stripslashes($this->lang->line('common_click_to_active')); else echo 'Click to active'; ?>" class="tip_top" href="javascript:confirm_status('<?php echo ADMIN_ENC_URL;?>/cancellation/change_reason_status/<?php echo $mode;?>/<?php echo $row->_id;?>')"><span class="badge_style"><?php echo $disp_status;?></span></a>
									<?php 
										}
									}else {
									?>
									<span class="badge_style b_done"><?php echo $disp_status;?></span>
									<?php }?>
									</td>
									<td class="center action-icons-wrap">
									<?php if ($allPrev == '1' || in_array('2', $cancellation)){?>
										<span><a class="action-icons c-pencil_basic_red" href="<?php echo ADMIN_ENC_URL;?>/cancellation/edit_language_cancellation/<?php echo $row->_id.'/'.$reason_for;?>" title="<?php if ($this->lang->line('admin_common_lang_edit') != '') echo stripslashes($this->lang->line('admin_common_lang_edit')); else echo 'Edit Language'; ?>"><?php if ($this->lang->line('admin_common_lang_edit') != '') echo stripslashes($this->lang->line('admin_common_lang_edit')); else echo 'Edit Language'; ?></a></span>
										<span>
											<a class="action-icons c-edit" href="<?php echo ADMIN_ENC_URL;?>/cancellation/add_edit_cancellation_type/<?php echo $row->type;?>/<?php echo $row->_id;?>" title="<?php if ($this->lang->line('admin_common_edit') != '') echo stripslashes($this->lang->line('admin_common_edit')); else echo 'Edit'; ?>">
												<?php if ($this->lang->line('admin_common_edit') != '') echo stripslashes($this->lang->line('admin_common_edit')); else echo 'Edit'; ?>
											</a>
										</span>
									<?php }?>
									<?php if ($allPrev == '1' || in_array('3', $cancellation)){?>	
										<span>
											<a class="action-icons c-delete" href="javascript:confirm_delete('<?php echo ADMIN_ENC_URL;?>/cancellation/delete_reason/<?php echo $row->_id;?>')" title="<?php if ($this->lang->line('admin_common_delete') != '') echo stripslashes($this->lang->line('admin_common_delete')); else echo 'Delete'; ?>">
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
										 <?php if ($this->lang->line('admin_cancellation_cancellation_id') != '') echo stripslashes($this->lang->line('admin_cancellation_cancellation_id')); else echo 'Cancellation Id'; ?>
									</th>
									<th>
										<?php if ($this->lang->line('admin_cancellation_reason') != '') echo stripslashes($this->lang->line('admin_cancellation_reason')); else echo 'Reason'; ?> 
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
<?php 
$this->load->view(ADMIN_ENC_URL.'/templates/footer.php');
?>