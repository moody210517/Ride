<?php
$this->load->view(ADMIN_ENC_URL.'/templates/header.php');
extract($privileges);
?>
<div id="content">
	<div class="grid_container">
		<?php 
			$attributes = array('id' => 'display_form');
			echo form_open(ADMIN_ENC_URL.'/promocode/change_promocode_status_global',$attributes) 
		?>
			<div class="grid_12">
				<div class="widget_wrap">
					<div class="widget_top">
						<span class="h_icon blocks_images"></span>
						<h6><?php echo $heading?></h6>
						<div style="float: right;line-height:40px;padding:0px 10px;height:39px;">
							<?php 
							if ($allPrev == '1' || in_array('2', $promocode)){
							?>
								<div class="btn_30_light" style="height: 29px;">
									<a href="javascript:void(0)" onclick="return checkBoxValidationAdmin('Active','<?php echo $subAdminMail; ?>');" class="tipTop" title="<?php if ($this->lang->line('common_select_active_records') != '') echo stripslashes($this->lang->line('common_select_active_records')); else echo 'Select any checkbox and click here to active records'; ?>">
										<!-- <span class="icon accept_co"></span> --><span class="btn_link act-btn"><?php if ($this->lang->line('admin_common_active') != '') echo stripslashes($this->lang->line('admin_common_active')); else echo 'Active'; ?></span>
									</a>
								</div>
								<div class="btn_30_light" style="height: 29px;">
									<a href="javascript:void(0)" onclick="return checkBoxValidationAdmin('Inactive','<?php echo $subAdminMail; ?>');" class="tipTop" title="<?php if ($this->lang->line('common_select_inactive_records') != '') echo stripslashes($this->lang->line('common_select_inactive_records')); else echo 'Select any checkbox and click here to inactive records'; ?>">
										<!-- <span class="icon delete_co"></span> --><span class="btn_link inact-btn"><?php if ($this->lang->line('admin_common_inactive') != '') echo stripslashes($this->lang->line('admin_common_inactive')); else echo 'Inactive'; ?></span>
									</a>
								</div>
							<?php 
							}
							if ($allPrev == '1' || in_array('3', $promocode)){
							?>
								<div class="btn_30_light" style="height: 29px;">
									<a href="javascript:void(0)" onclick="return checkBoxValidationAdmin('Delete','<?php echo $subAdminMail; ?>');" class="tipTop" title="<?php if ($this->lang->line('common_select_delete_records') != '') echo stripslashes($this->lang->line('common_select_delete_records')); else echo 'Select any checkbox and click here to delete records'; ?>">
										<!--<span class="icon cross_co del-btn"></span>--><span class="btn_link"><?php if ($this->lang->line('admin_common_delete') != '') echo stripslashes($this->lang->line('admin_common_delete')); else echo 'Delete'; ?></span>
									</a>
								</div>
							<?php 
							}
							?>
						</div>
					</div>
					<div class="widget_content">
						<table class="display display_tbl" id="promo_tbl">
							<thead>
								<tr>
									<th class="center">
										<input name="checkbox_id[]" type="checkbox" value="on" class="checkall">
									</th>
									<th class="tip_top" title="<?php if ($this->lang->line('dash_click_sort') != '') echo stripslashes($this->lang->line('dash_click_sort')); else echo 'Click to sort'; ?>">
										<?php if ($this->lang->line('admin_promocode_code') != '') echo stripslashes($this->lang->line('admin_promocode_code')); else echo 'Code'; ?>
									</th>
									<th class="tip_top" title="<?php if ($this->lang->line('dash_click_sort') != '') echo stripslashes($this->lang->line('dash_click_sort')); else echo 'Click to sort'; ?>">
										<?php if ($this->lang->line('admin_promocode_type') != '') echo stripslashes($this->lang->line('admin_promocode_type')); else echo 'Type'; ?>
									</th>
									<th class="tip_top" title="<?php if ($this->lang->line('dash_click_sort') != '') echo stripslashes($this->lang->line('dash_click_sort')); else echo 'Click to sort'; ?>">
										<?php if ($this->lang->line('admin_promocode_amount') != '') echo stripslashes($this->lang->line('admin_promocode_amount')); else echo 'Amount'; ?>
									</th>
									<th class="tip_top" title="<?php if ($this->lang->line('dash_click_sort') != '') echo stripslashes($this->lang->line('dash_click_sort')); else echo 'Click to sort'; ?>">
										<?php if ($this->lang->line('admin_promocode_max_usage') != '') echo stripslashes($this->lang->line('admin_promocode_max_usage')); else echo 'Max Usage'; ?>
									</th>
									<th class="tip_top" title="<?php if ($this->lang->line('dash_click_sort') != '') echo stripslashes($this->lang->line('dash_click_sort')); else echo 'Click to sort'; ?>">
										<?php if ($this->lang->line('admin_promocode_used') != '') echo stripslashes($this->lang->line('admin_promocode_used')); else echo 'Used'; ?>
									</th>
									<th class="tip_top" title="<?php if ($this->lang->line('dash_click_sort') != '') echo stripslashes($this->lang->line('dash_click_sort')); else echo 'Click to sort'; ?>">
										<?php if ($this->lang->line('admin_promocode_remaining') != '') echo stripslashes($this->lang->line('admin_promocode_remaining')); else echo 'Remaining'; ?>
									</th>
									<th class="tip_top" title="<?php if ($this->lang->line('dash_click_sort') != '') echo stripslashes($this->lang->line('dash_click_sort')); else echo 'Click to sort'; ?>">
										<?php if ($this->lang->line('admin_promocode_validity_from') != '') echo stripslashes($this->lang->line('admin_promocode_validity_from')); else echo 'Validity From'; ?>
									</th>
									<th class="tip_top" title="<?php if ($this->lang->line('dash_click_sort') != '') echo stripslashes($this->lang->line('dash_click_sort')); else echo 'Click to sort'; ?>">
										<?php if ($this->lang->line('admin_promocode_validity_to') != '') echo stripslashes($this->lang->line('admin_promocode_validity_to')); else echo 'Validity To'; ?>
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
								if ($promocodeList->num_rows() > 0){
									foreach ($promocodeList->result() as $row){
								?>
								<tr>
									<td class="center tr_select ">
										<input name="checkbox_id[]" type="checkbox" value="<?php echo $row->_id;?>">
									</td>
									<td class="center">
										<?php echo $row->promo_code;?>
									</td>
									<td class="center">
										<?php if ($row->code_type == 'Flat'){?>
											<span class="badge_style b_high"><?php echo get_language_value_for_keyword('Flat',$this->data['langCode']);?></span>
										<?php }elseif ($row->code_type == 'Percent'){?>
											<span class="badge_style b_away"><?php echo get_language_value_for_keyword('Percent',$this->data['langCode']);?></span>
										<?php } ?>
									</td>
									<td class="center">
										<?php 
										if ($row->code_type == 'Flat'){
											echo $row->promo_value;
										}else if ($row->code_type == 'Percent'){
											echo floatval($row->promo_value).' %';
										}
										?>
									</td>
									<td class="center">
										 <?php echo $row->usage_allowed;?>
									</td>
									<td class="center">
										 <?php echo $row->no_of_usage;?>
									</td>
									</td>
									<td class="center">
										<?php echo ($row->usage_allowed - $row->no_of_usage);?>
									</td>
									<td class="center">
										 <?php echo $row->validity['valid_from'];?>
									</td>
									<td class="center">
										<?php echo $row->validity['valid_to'];?>
									</td>
									<td class="center">
										<?php 
										$disp_status = get_language_value_for_keyword($row->status,$this->data['langCode']);
										if ($allPrev == '1' || in_array('2', $promocode)){
											if($row->status=='Active' || $row->status=='Inactive'){
												$mode = ($row->status == 'Active')?'0':'1';
											}else{
												$mode='2';
											}
											if ($mode == '0'){
										?>
											<a title="<?php if ($this->lang->line('common_click_to_inactive') != '') echo stripslashes($this->lang->line('common_click_to_inactive')); else echo 'Click to inactive'; ?>" class="tip_top" href="javascript:confirm_status('<?php echo ADMIN_ENC_URL;?>/promocode/change_promocode_status/<?php echo $mode;?>/<?php echo $row->_id;?>');">
												<span class="badge_style b_done"><?php echo $disp_status;?></span>
											</a>
										<?php
											}else if ($mode == '1'){	
										?>
											<a title="<?php if ($this->lang->line('common_click_to_active') != '') echo stripslashes($this->lang->line('common_click_to_active')); else echo 'Click to active'; ?>" class="tip_top" href="javascript:confirm_status('<?php echo ADMIN_ENC_URL;?>/promocode/change_promocode_status/<?php echo $mode;?>/<?php echo $row->_id;?>')">
												<span class="badge_style"><?php echo $disp_status;?></span>
											</a>
										<?php 
											}else{
										?>
											<span class="badge_style b_pending"><?php echo $disp_status;?></span>
										<?php 
											}
										}else {
										?>
										<span class="badge_style b_done"><?php echo $disp_status;?></span>
										<?php }?>
									</td>
									<td class="center action-icons-wrap">
										<?php if ($allPrev == '1' || in_array('2', $promocode)){?>
											<span>
												<a class="action-icons c-edit" href="<?php echo ADMIN_ENC_URL;?>/promocode/edit_promocode_form/<?php echo $row->_id;?>" title="<?php if ($this->lang->line('admin_common_edit') != '') echo stripslashes($this->lang->line('admin_common_edit')); else echo 'Edit'; ?>">
													<?php if ($this->lang->line('admin_common_edit') != '') echo stripslashes($this->lang->line('admin_common_edit')); else echo 'Edit'; ?>
												</a>
											</span>
										<?php }?>
										<?php if ($allPrev == '1' || in_array('3', $promocode)){?>	
											<span>
												<a class="action-icons c-delete" href="javascript:confirm_delete('<?php echo ADMIN_ENC_URL;?>/promocode/delete_promocode/<?php echo $row->_id;?>')" title="<?php if ($this->lang->line('admin_common_delete') != '') echo stripslashes($this->lang->line('admin_common_delete')); else echo 'Delete'; ?>">
													<?php if ($this->lang->line('admin_common_delete') != '') echo stripslashes($this->lang->line('admin_common_delete')); else echo 'Delete'; ?>
												</a>
											</span>
										<?php } ?>
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
										<?php if ($this->lang->line('admin_promocode_code') != '') echo stripslashes($this->lang->line('admin_promocode_code')); else echo 'Code'; ?>
									</th>
									<th>
										<?php if ($this->lang->line('admin_promocode_type') != '') echo stripslashes($this->lang->line('admin_promocode_type')); else echo 'Type'; ?>
									</th>
									<th>
										<?php if ($this->lang->line('admin_promocode_amount') != '') echo stripslashes($this->lang->line('admin_promocode_amount')); else echo 'Amount'; ?>
									</th>
									<th>
										<?php if ($this->lang->line('admin_promocode_max_usage') != '') echo stripslashes($this->lang->line('admin_promocode_max_usage')); else echo 'Max Usage'; ?>
									</th>
									<th>
										<?php if ($this->lang->line('admin_promocode_used') != '') echo stripslashes($this->lang->line('admin_promocode_used')); else echo 'Used'; ?>
									</th>
									<th>
										<?php if ($this->lang->line('admin_promocode_remaining') != '') echo stripslashes($this->lang->line('admin_promocode_remaining')); else echo 'Remaining'; ?>
									</th>
									<th>
										<?php if ($this->lang->line('admin_promocode_validity_from') != '') echo stripslashes($this->lang->line('admin_promocode_validity_from')); else echo 'Validity From'; ?>
									</th>
									<th>
										<?php if ($this->lang->line('admin_promocode_validity_to') != '') echo stripslashes($this->lang->line('admin_promocode_validity_to')); else echo 'Validity To'; ?>
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