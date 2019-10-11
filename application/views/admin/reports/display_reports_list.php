<?php
$this->load->view(ADMIN_ENC_URL.'/templates/header.php');
extract($privileges);
?>
<div id="content">
		<div class="grid_container">
			<?php 
				$attributes = array('id' => 'display_form');
				echo form_open(ADMIN_ENC_URL.'/reports/change_report_status_global',$attributes) 
			?>
			<div class="grid_12">
				<div class="widget_wrap">
					<div class="widget_top">
						<span class="h_icon blocks_images"></span>
						<h6><?php echo $heading?></h6>
						
						<div style="float: right;line-height:40px;padding:0px 10px;height:39px;">
							<?php if ($allPrev == '1' || in_array('2', $reports) || in_array('1', $reports)){ ?>
							<div class="btn_30_light" style="height: 29px;">
								<a href="javascript:void(0)" onclick="return checkBoxValidationAdmin('closed','<?php echo $subAdminMail; ?>');" class="tipTop" title="<?php if ($this->lang->line('admin_reports_close_reports_bulk') != '') echo stripslashes($this->lang->line('admin_reports_close_reports_bulk')); else echo 'Select any checkbox and click here to close reports'; ?>">
									<!-- <span class="icon accept_co"></span> -->
									<span class="btn_link inact-btn"><?php if ($this->lang->line('admin_subadmin_close') != '') echo stripslashes($this->lang->line('admin_subadmin_close')); else echo 'Close'; ?></span>
								</a>
							</div>
							<?php /* <div class="btn_30_light" style="height: 29px;">
								<a href="javascript:void(0)" onclick="return checkBoxValidationAdmin('open','<?php echo $subAdminMail; ?>');" class="tipTop" title="<?php if ($this->lang->line('admin_reports_reopen_reports_bulk') != '') echo stripslashes($this->lang->line('admin_reports_reopen_reports_bulk')); else echo 'Select any checkbox and click here to reopen reports'; ?>">
									<!-- <span class="icon delete_co"></span> -->
									<span class="btn_link act-btn"><?php if ($this->lang->line('admin_subadmin_open') != '') echo stripslashes($this->lang->line('admin_subadmin_open')); else echo 'Open'; ?></span>
								</a>
							</div> */ ?>
							<?php 
							}
							?>
						</div>
						
					</div>
					<div class="widget_content">
						<table class="display display_tbl" id="report_tbl">
							<thead>
								<tr>
									<th class="center">
										<input name="checkbox_id[]" type="checkbox" value="on" class="checkall">
									</th>
									<th class="tip_top" title="<?php if ($this->lang->line('dash_click_sort') != '') echo stripslashes($this->lang->line('dash_click_sort')); else echo 'Click to sort'; ?>">
										 <?php if ($this->lang->line('admin_reports_report_id') != '') echo stripslashes($this->lang->line('admin_reports_report_id')); else echo 'Report Id'; ?>
									</th>
									<th class="tip_top" title="<?php if ($this->lang->line('dash_click_sort') != '') echo stripslashes($this->lang->line('dash_click_sort')); else echo 'Click to sort'; ?>">
										 <?php if ($this->lang->line('admin_reports_date') != '') echo stripslashes($this->lang->line('admin_reports_date')); else echo 'Date'; ?>
									</th>
									<th class="tip_top" title="<?php if ($this->lang->line('dash_click_sort') != '') echo stripslashes($this->lang->line('dash_click_sort')); else echo 'Click to sort'; ?>">
										<?php if ($this->lang->line('admin_reports_type') != '') echo stripslashes($this->lang->line('admin_reports_type')); else echo 'Type'; ?>
									</th>
									
									<th class="tip_top" title="<?php if ($this->lang->line('dash_click_sort') != '') echo stripslashes($this->lang->line('dash_click_sort')); else echo 'Click to sort'; ?>">
										<?php if ($this->lang->line('admin_reports_user_info') != '') echo stripslashes($this->lang->line('admin_reports_user_info')); else echo 'User Info'; ?>
									</th>
									<th class="tip_top" title="<?php if ($this->lang->line('dash_click_sort') != '') echo stripslashes($this->lang->line('dash_click_sort')); else echo 'Click to sort'; ?>">
										<?php if ($this->lang->line('admin_reports_subject') != '') echo stripslashes($this->lang->line('admin_reports_subject')); else echo 'Subject'; ?>
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
								if ($reportsList->num_rows() > 0){
									foreach ($reportsList->result() as $row){
								?>
								<tr>
									<td class="center tr_select ">
										<input name="checkbox_id[]" type="checkbox" value="<?php echo $row->_id;?>">
									</td>
									<td class="center">
										<?php echo '#'.$row->report_id;?>
									</td>
									<td class="center">
										<?php 
												echo get_time_to_string('Y-m-d h:i A',MongoEPOCH($row->created_date));
										?>
									</td>
									<td class="center">
								
										<?php if(isset($row->reporter_type)) echo get_language_value_for_keyword($row->reporter_type,$this->data['langCode']);?>
									
									</td>
									<td class="center">
										<?php 
												echo ucfirst($row->reporter_details['name']).'<br/>';
												echo '<p style="color:#a1a1a1;">'; echo $row->reporter_details['email'];
												if(isset($row->reporter_details['phone_number'])) echo '<br/>'.$row->reporter_details['phone_number'];
												echo '</p>';
										?>
									</td>
									<td class="center">
										<?php 
												echo character_limiter($row->subject,100);
										?>
									</td>
									<td class="center">
									<?php 
									$disp_status = get_language_value_for_keyword($row->status,$this->data['langCode']);
									
									if($row->status == 'open'){
										if ($this->lang->line('admin_subadmin_open') != '') $disp_status =  stripslashes($this->lang->line('admin_subadmin_open')); else $disp_status =  'Open'; 
									} else {
										if ($this->lang->line('admin_subadmin_close') != '') $disp_status = stripslashes($this->lang->line('admin_subadmin_close')); else $disp_status = 'Close'; 
									}
									
									if ($allPrev == '1' || in_array('2', $reports) || in_array('1', $reports)){
										$mode = ($row->status == 'closed')?'0':'1';
										if ($mode == '0'){
									?>
										<span class="badge_style b_done"><?php echo $disp_status;?></span>
									<?php
										}else {	
									?>
										<a title="<?php if ($this->lang->line('admin_reports_click_to_close') != '') echo stripslashes($this->lang->line('admin_reports_click_to_close')); else echo 'Click to close'; ?>" class="tip_top" href="javascript:confirm_status('<?php echo ADMIN_ENC_URL;?>/reports/change_report_status/<?php echo $mode;?>/<?php echo $row->_id;?>')"><span class="badge_style"><?php echo $disp_status;?></span></a>
									<?php 
										}
									}else {
									?>
									<span class="badge_style b_done"><?php echo $disp_status;?></span>
									<?php }?>
									</td>
									<td class="center action-icons-wrap">
									<span><a class="action-icons c-suspend" href="<?php echo ADMIN_ENC_URL;?>/reports/view_reports_details/<?php echo $row->_id;?>" title="<?php if ($this->lang->line('admin_common_view') != '') echo stripslashes($this->lang->line('admin_common_view')); else echo 'View'; ?>"><?php if ($this->lang->line('admin_subadmin_view') != '') echo stripslashes($this->lang->line('admin_subadmin_view')); else echo 'View'; ?></a></span>
									
										<?php /* if ($allPrev == '1' || in_array('3', $reports)){   ?>	
											<span><a class="action-icons c-delete" href="javascript:confirm_delete('admin/reports/delete_reports/<?php echo $row->_id;?>')" title="<?php if ($this->lang->line('admin_common_delete') != '') echo stripslashes($this->lang->line('admin_common_delete')); else echo 'Delete'; ?>"><?php if ($this->lang->line('admin_subadmin_delete') != '') echo stripslashes($this->lang->line('admin_subadmin_delete')); else echo 'Delete'; ?></a></span>
										<?php } */ ?>
										
									
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
										<?php if ($this->lang->line('admin_reports_report_id') != '') echo stripslashes($this->lang->line('admin_reports_report_id')); else echo 'Report Id'; ?>
									</th>
									<th>
										<?php if ($this->lang->line('admin_reports_date') != '') echo stripslashes($this->lang->line('admin_reports_date')); else echo 'Date'; ?>
									</th>
									<th>
										<?php if ($this->lang->line('admin_reports_type') != '') echo stripslashes($this->lang->line('admin_reports_type')); else echo 'Type'; ?>
									</th>
									<th>
										<?php if ($this->lang->line('admin_reports_user_info') != '') echo stripslashes($this->lang->line('admin_reports_user_info')); else echo 'User Info'; ?>
									</th>
									<th>
										<?php if ($this->lang->line('admin_reports_subject') != '') echo stripslashes($this->lang->line('admin_reports_subject')); else echo 'Subject'; ?>
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