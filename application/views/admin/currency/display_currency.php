<?php
$this->load->view(ADMIN_ENC_URL.'/templates/header.php');
extract($privileges);
?>
<div id="content">
	<div class="grid_container">
		<?php 
			$attributes = array('id' => 'display_form');
			echo form_open(ADMIN_ENC_URL.'/currency/change_currency_status_global',$attributes) 
		?>
			<div class="grid_12">
				<div class="widget_wrap">
					<div class="widget_top">
						<span class="h_icon blocks_images"></span>
						<h6><?php echo $heading?></h6>
						<div style="float: right;line-height:40px;padding:0px 10px;height:39px;">
							<?php 
							if ($allPrev == '1' || in_array('2', $currency)){
							?>
								<div class="btn_30_light" style="height: 29px;">
									<a href="javascript:void(0)" onclick="return checkBoxValidationAdmin('Active','<?php echo $subAdminMail; ?>');" class="tipTop" title="Select any checkbox and click here to active records">
										<span class="icon accept_co"></span><span class="btn_link"><?php if ($this->lang->line('admin_common_active') != '') echo stripslashes($this->lang->line('admin_common_active')); else echo 'Active'; ?></span>
									</a>
								</div>
								<div class="btn_30_light" style="height: 29px;">
									<a href="javascript:void(0)" onclick="return checkBoxValidationAdmin('Inactive','<?php echo $subAdminMail; ?>');" class="tipTop" title="Select any checkbox and click here to inactive records">
										<span class="icon delete_co"></span><span class="btn_link"><?php if ($this->lang->line('admin_common_inactive') != '') echo stripslashes($this->lang->line('admin_common_inactive')); else echo 'Inactive'; ?></span>
									</a>
								</div>
							<?php 
							}
							if ($allPrev == '1' || in_array('3', $currency)){
							?>
								<div class="btn_30_light" style="height: 29px;">
									<a href="javascript:void(0)" onclick="return checkBoxValidationAdmin('Delete','<?php echo $subAdminMail; ?>');" class="tipTop" title="Select any checkbox and click here to delete records">
										<span class="icon cross_co"></span><span class="btn_link">Delete</span>
									</a>
								</div>
							<?php 
							}
							?>
						</div>
					</div>
					<div class="widget_content">
						<table class="display display_tbl" id="country_tbl">
							<thead>
								<tr>
									<th class="center">
										<input name="checkbox_id[]" type="checkbox" value="on" class="checkall">
									</th>
									<th class="tip_top" title="Click to sort">
										<?php if ($this->lang->line('admin_currency_name') != '') echo stripslashes($this->lang->line('admin_currency_name')); else echo 'Name'; ?>
									</th>
									<th class="tip_top" title="Click to sort">
										<?php if ($this->lang->line('admin_currency_symbol') != '') echo stripslashes($this->lang->line('admin_currency_symbol')); else echo 'Symbol'; ?>
									</th>
									<th class="tip_top" title="Click to sort">
										<?php if ($this->lang->line('admin_currency_code') != '') echo stripslashes($this->lang->line('admin_currency_code')); else echo 'Code'; ?>
									</th>
									<th class="tip_top" title="Click to sort">
										<?php if ($this->lang->line('admin_common_status') != '') echo stripslashes($this->lang->line('admin_common_status')); else echo 'Status'; ?>
									</th>
									<th>
										 <?php if ($this->lang->line('admin_common_action') != '') echo stripslashes($this->lang->line('admin_common_action')); else echo 'Action'; ?>
									</th>
								</tr>
							</thead>
							<tbody>
								<?php 
								if ($currencyList->num_rows() > 0){
									foreach ($currencyList->result() as $row){
								?>
								<tr>
									<td class="center tr_select ">
										<input name="checkbox_id[]" type="checkbox" value="<?php echo $row->_id;?>">
									</td>
									<td class="center">
										<?php echo $row->name;?>
									</td>
									<td class="center">
										<?php echo $row->symbol;?>
									</td>
									<td class="center">
										<?php echo $row->code;?>
									</td>
									<td class="center">
										<?php 
										if ($allPrev == '1' || in_array('2', $currency)){
											if($row->status=='Active' || $row->status=='Inactive'){
												$mode = ($row->status == 'Active')?'0':'1';
											}else{
												$mode='2';
											}
											if ($mode == '0'){
										?>
											<a title="Click to inactive" class="tip_top" href="javascript:confirm_status('<?php echo ADMIN_ENC_URL;?>/currency/change_currency_status/<?php echo $mode;?>/<?php echo $row->_id;?>');">
												<span class="badge_style b_done"><?php echo $row->status;?></span>
											</a>
										<?php
											}else if ($mode == '1'){	
										?>
											<a title="Click to active" class="tip_top" href="javascript:confirm_status('<?php echo ADMIN_ENC_URL;?>/currency/change_currency_status/<?php echo $mode;?>/<?php echo $row->_id;?>')">
												<span class="badge_style"><?php echo $row->status;?></span>
											</a>
										<?php 
											}else{
										?>
											<span class="badge_style b_pending"><?php echo $row->status;?></span>
										<?php 
											}
										}else {
										?>
										<span class="badge_style b_done"><?php echo $row->status;?></span>
										<?php }?>
									</td>
									<td class="center">
										<span>
											<a class="action-icons c-suspend" href="<?php echo ADMIN_ENC_URL;?>/currency/view_currency/<?php echo $row->_id;?>" title="View">
												<?php if ($this->lang->line('admin_common_view') != '') echo stripslashes($this->lang->line('admin_common_view')); else echo 'View'; ?>
											</a>
										</span>
										<?php if ($allPrev == '1' || in_array('2', $currency)){?>
											<span>
												<a class="action-icons c-edit" href="<?php echo ADMIN_ENC_URL;?>/currency/add_edit_currency/<?php echo $row->_id;?>" title="Edit">
													<?php if ($this->lang->line('admin_common_edit') != '') echo stripslashes($this->lang->line('admin_common_edit')); else echo 'Edit'; ?>
												</a>
											</span>
										<?php }?>
										<?php if ($allPrev == '1' || in_array('3', $currency)){?>	
											<span>
												<a class="action-icons c-delete" href="javascript:confirm_delete('<?php echo ADMIN_ENC_URL;?>/currency/delete_currency/<?php echo $row->_id;?>')" title="Delete">
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
										<?php if ($this->lang->line('admin_currency_name') != '') echo stripslashes($this->lang->line('admin_currency_name')); else echo 'Name'; ?>
									</th>
									<th>
										<?php if ($this->lang->line('admin_currency_symbol') != '') echo stripslashes($this->lang->line('admin_currency_symbol')); else echo 'Symbol'; ?>
									</th>
									<th>
										<?php if ($this->lang->line('admin_currency_code') != '') echo stripslashes($this->lang->line('admin_currency_code')); else echo 'Code'; ?>
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
<?php 
$this->load->view(ADMIN_ENC_URL.'/templates/footer.php');
?>