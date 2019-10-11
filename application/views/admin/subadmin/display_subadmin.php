<?php
$this->load->view(ADMIN_ENC_URL.'/templates/header.php');
?>
<div id="content">
		<div class="grid_container">
			<?php 
				$attributes = array('id' => 'display_form');
				echo form_open(ADMIN_ENC_URL.'/subadmin/change_subadmin_status_global',$attributes) 
			?>
			<div class="grid_12">
				<div class="widget_wrap">
					<div class="widget_top">
						<span class="h_icon blocks_images"></span>
						<h6><?php echo $heading?></h6>
						<div style="float: right;line-height:40px;padding:0px 10px;height:39px;">
						<?php if ($allPrev == '1' || in_array('2', $subadmin)){?>
							<div class="btn_30_light" style="height: 29px;">
								<a href="javascript:void(0)" onclick="return checkBoxValidationAdmin('Active','<?php echo $subAdminMail; ?>');" class="tipTop" title="<?php if ($this->lang->line('common_select_active_records') != '') echo stripslashes($this->lang->line('common_select_active_records')); else echo 'Select any checkbox and click here to active records'; ?>"> <!-- <span class="icon accept_co"></span> --> <span class="btn_link act-btn"><?php if ($this->lang->line('admin_subadmin_active') != '') echo stripslashes($this->lang->line('admin_subadmin_active')); else echo 'Active'; ?></span></a>
							</div>
							<div class="btn_30_light" style="height: 29px;">
								<a href="javascript:void(0)" onclick="return checkBoxValidationAdmin('Inactive','<?php echo $subAdminMail; ?>');" class="tipTop" title="<?php if ($this->lang->line('driver_select_inactive_records') != '') echo stripslashes($this->lang->line('driver_select_inactive_records')); else echo 'Select any checkbox and click here to inactive records'; ?>"><!-- <span class="icon delete_co"></span> --><span class="btn_link inact-btn"><?php if ($this->lang->line('admin_subadmin_inactive') != '') echo stripslashes($this->lang->line('admin_subadmin_inactive')); else echo 'Inactive'; ?></span></a>
							</div>
						<?php 
						}
						if ($allPrev == '1' || in_array('3', $subadmin)){
						?>
							<div class="btn_30_light" style="height: 29px;">
								<a href="javascript:void(0)" onclick="return checkBoxValidationAdmin('Delete','<?php echo $subAdminMail; ?>');" class="tipTop" title="<?php if ($this->lang->line('driver_select_delete_records') != '') echo stripslashes($this->lang->line('driver_select_delete_records')); else echo 'Select any checkbox and click here to delete records'; ?>"><!--<span class="icon cross_co del-btn"></span>--> <span class="btn_link"><?php if ($this->lang->line('admin_common_delete') != '') echo stripslashes($this->lang->line('admin_common_delete')); else echo 'Delete'; ?></span></a>
							</div>
						<?php }?>
						</div>
					</div>
					<div class="widget_content">
						<table class="display" id="subadmin_tbl">
						<thead>
						<tr>
							<th class="center">
								<input name="checkbox_id[]" type="checkbox" value="on" class="checkall">
							</th>
							<th class="tip_top" title="<?php if ($this->lang->line('dash_click_sort') != '') echo stripslashes($this->lang->line('dash_click_sort')); else echo 'Click to sort'; ?>">
							
								 <?php if ($this->lang->line('admin_subadmin_sub_admin_name') != '') echo stripslashes($this->lang->line('admin_subadmin_sub_admin_name')); else echo 'Sub Admin Name'; ?>
							</th>
							<th class="tip_top" title="<?php if ($this->lang->line('dash_click_sort') != '') echo stripslashes($this->lang->line('dash_click_sort')); else echo 'Click to sort'; ?>">
							
								 <?php if ($this->lang->line('admin_subadmin_email') != '') echo stripslashes($this->lang->line('admin_subadmin_email')); else echo 'Email'; ?>
							</th>
							<th class="tip_top" title="<?php if ($this->lang->line('dash_click_sort') != '') echo stripslashes($this->lang->line('dash_click_sort')); else echo 'Click to sort'; ?>"> 
							
								<?php if ($this->lang->line('admin_subadmin_last_login_date') != '') echo stripslashes($this->lang->line('admin_subadmin_last_login_date')); else echo 'Last Login Date'; ?>
							</th>
							<th class="tip_top" title="<?php if ($this->lang->line('dash_click_sort') != '') echo stripslashes($this->lang->line('dash_click_sort')); else echo 'Click to sort'; ?>">
							
								<?php if ($this->lang->line('admin_subadmin_last_logout_date') != '') echo stripslashes($this->lang->line('admin_subadmin_last_logout_date')); else echo 'Last Logout Date'; ?>
							</th>
							<th class="tip_top" title="<?php if ($this->lang->line('dash_click_sort') != '') echo stripslashes($this->lang->line('dash_click_sort')); else echo 'Click to sort'; ?>">
							
								<?php if ($this->lang->line('admin_subadmin_last_login_ip') != '') echo stripslashes($this->lang->line('admin_subadmin_last_login_ip')); else echo 'Last Login IP'; ?>
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
						if ($admin_users->num_rows() > 0){
							foreach ($admin_users->result() as $row){
						?>
						<tr>
							<td class="center tr_select ">
								<input name="checkbox_id[]" type="checkbox" value="<?php echo $row->_id;?>">
							</td>
							<td class="center">
								<?php if(isset($row->admin_name)) echo $row->admin_name;?>
							</td>
							<td class="center">
								<?php if(isset($row->email)) echo $row->email;?>
							</td>
							<td class="center">
								 <?php if(isset($row->last_login_date)) echo $row->last_login_date;?>
							</td>
							<td class="center">
								 <?php if(isset($row->last_logout_date))  echo $row->last_logout_date;?>
							</td>
							<td class="center">
								<?php if(isset($row->last_login_ip)) echo $row->last_login_ip;?>
							</td>
							<td class="center">
							<?php 
							$disp_status = get_language_value_for_keyword($row->status,$this->data['langCode']);
							if ($allPrev == '1' || in_array('2', $subadmin)){
								$mode = ($row->status == 'Active')?'0':'1';
								if ($mode == '0'){
							?>
								<a title="<?php if ($this->lang->line('common_click_inactive') != '') echo stripslashes($this->lang->line('common_click_inactive')); else echo 'Click to inactive'; ?>" class="tip_top" href="javascript:confirm_status('<?php echo ADMIN_ENC_URL;?>/subadmin/change_subadmin_status/<?php echo $mode;?>/<?php echo $row->_id;?>');"><span class="badge_style b_done"><?php echo $disp_status;?></span></a>
							<?php
								}else {	
							?>
								<a title="<?php if ($this->lang->line('common_click_active') != '') echo stripslashes($this->lang->line('common_click_active')); else echo 'Click to active'; ?>" class="tip_top" href="javascript:confirm_status('<?php echo ADMIN_ENC_URL;?>/subadmin/change_subadmin_status/<?php echo $mode;?>/<?php echo $row->_id;?>')"><span class="badge_style"><?php echo $disp_status;?></span></a>
							<?php 
								}
							}else {
							?>
								<span class="badge_style b_done"><?php echo $disp_status;?></span>
							<?php }?>
							</td>
							<td class="center action-icons-wrap">
							<?php if ($allPrev == '1' || in_array('2', $subadmin)){?>
								<span><a class="action-icons c-key" href="<?php echo ADMIN_ENC_URL;?>/subadmin/change_subadmin_password/<?php echo $row->_id;?>" original-title="<?php if ($this->lang->line('admin_user_change_password') != '') echo stripslashes($this->lang->line('admin_user_change_password')); else echo 'Change Password'; ?>"><?php if ($this->lang->line('admin_user_change_password') != '') echo stripslashes($this->lang->line('admin_user_change_password')); else echo 'Change Password'; ?></a></span>
								<span><a class="action-icons c-edit" href="<?php echo ADMIN_ENC_URL;?>/subadmin/edit_subadmin_form/<?php echo $row->_id;?>" title="<?php if ($this->lang->line('admin_common_edit') != '') echo stripslashes($this->lang->line('admin_common_edit')); else echo 'Edit'; ?>"><?php if ($this->lang->line('admin_common_edit') != '') echo stripslashes($this->lang->line('admin_common_edit')); else echo 'Edit'; ?></a></span>
							<?php }?>
								<span><a class="action-icons c-suspend" href="<?php echo ADMIN_ENC_URL;?>/subadmin/view_subadmin/<?php echo $row->_id;?>" title="<?php if ($this->lang->line('admin_common_view') != '') echo stripslashes($this->lang->line('admin_common_view')); else echo 'View'; ?>"><?php if ($this->lang->line('admin_common_view') != '') echo stripslashes($this->lang->line('admin_common_view')); else echo 'View'; ?></a></span>
							<?php if ($allPrev == '1' || in_array('3', $subadmin)){?>
								<span><a class="action-icons c-delete" href="javascript:confirm_delete('<?php echo ADMIN_ENC_URL;?>/subadmin/delete_subadmin/<?php echo $row->_id;?>')" title="<?php if ($this->lang->line('admin_common_delete') != '') echo stripslashes($this->lang->line('admin_common_delete')); else echo 'Delete'; ?>"><?php if ($this->lang->line('admin_common_delete') != '') echo stripslashes($this->lang->line('admin_common_delete')); else echo 'Delete'; ?></a></span>
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
								 <?php if ($this->lang->line('admin_subadmin_sub_admin_name') != '') echo stripslashes($this->lang->line('admin_subadmin_sub_admin_name')); else echo 'Sub Admin Name'; ?>
							</th>
							<th>
								 <?php if ($this->lang->line('admin_subadmin_email') != '') echo stripslashes($this->lang->line('admin_subadmin_email')); else echo 'Email'; ?>
							</th>
							<th>
								<?php if ($this->lang->line('admin_subadmin_last_login_date') != '') echo stripslashes($this->lang->line('admin_subadmin_last_login_date')); else echo 'Last Login Date'; ?>
							</th>
							<th>
								<?php if ($this->lang->line('admin_subadmin_last_logout_date') != '') echo stripslashes($this->lang->line('admin_subadmin_last_logout_date')); else echo 'Last Logout Date'; ?>
							</th>
							<th>
								<?php if ($this->lang->line('admin_subadmin_last_login_ip') != '') echo stripslashes($this->lang->line('admin_subadmin_last_login_ip')); else echo 'Last Login IP'; ?>
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