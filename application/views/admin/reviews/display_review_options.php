<?php 
$this->load->view(ADMIN_ENC_URL.'/templates/header.php');
extract($privileges);
?>
<div id="content">
	<div class="grid_container">
		<?php 
			$attributes = array('id' => 'display_form');
			echo form_open(ADMIN_ENC_URL.'/reviews/change_reviews_status_global',$attributes) 
		?>
			<div class="grid_12">
				<div class="widget_wrap">
					<div class="widget_top">
						<span class="h_icon blocks_images"></span>
						<h6><?php echo $heading?></h6>
						<div style="float: right;line-height:40px;padding:0px 10px;height:39px;">
							<?php
                   
							if ($allPrev == '1' || in_array('1', $reviews)){
							?>
								
								<div class="btn_30_light" style="height: 29px;">
									<a href="<?php echo ADMIN_ENC_URL;?>/reviews/add_review_option_form" class="tipTop" title="<?php if ($this->lang->line('admin_review_add_new_reviews_option_user_or_driver') != '') echo stripslashes($this->lang->line('admin_review_add_new_reviews_option_user_or_driver')); else echo 'Add new review option for user or driver'; ?>">
										<span class="icon accept_co add_co addnew-btn2"></span><!-- <span class="btn_link"><?php if ($this->lang->line('admin_common_add_new_option') != '') echo stripslashes($this->lang->line('admin_common_add_new_option')); else echo 'Add New Option'; ?></span> -->
									</a>
								</div>
						 <?php } ?>
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
							
							if ($allPrev == '1' || in_array('3', $reviews)){
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
					<div class="widget_content dispaly_review_option_tbl">
						<table class="display display_tbl" id="review_tbl">
							<thead>
								<tr>
									<th class="center">
										<input name="checkbox_id[]" type="checkbox" value="on" class="checkall">
									</th>
									<th class="tip_top" title="<?php if ($this->lang->line('dash_click_sort') != '') echo stripslashes($this->lang->line('dash_click_sort')); else echo 'Click to sort'; ?>">
										<?php if ($this->lang->line('admin_review_option_id') != '') echo stripslashes($this->lang->line('admin_review_option_id')); else echo 'Option Id'; ?>
									</th>
									<th class="tip_top" title="<?php if ($this->lang->line('dash_click_sort') != '') echo stripslashes($this->lang->line('dash_click_sort')); else echo 'Click to sort'; ?>">
										<?php if ($this->lang->line('admin_review_review_option') != '') echo stripslashes($this->lang->line('admin_review_review_option')); else echo 'Review Option'; ?>
									</th>
									<th class="tip_top" title="<?php if ($this->lang->line('dash_click_sort') != '') echo stripslashes($this->lang->line('dash_click_sort')); else echo 'Click to sort'; ?>">
										<?php if ($this->lang->line('admin_review_review_option_for') != '') echo stripslashes($this->lang->line('admin_review_review_option_for')); else echo 'Review Option For'; ?>
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
								if ($reviewsList->num_rows() > 0){
									foreach ($reviewsList->result() as $row){
								?>
								<tr>
									<td class="center tr_select ">
										<input name="checkbox_id[]" type="checkbox" value="<?php echo $row->_id;?>">
									</td>
									<td class="center">
										<?php if(isset($row->option_id)) echo $row->option_id;?>
									</td>
									<td class="center">
										<?php if(isset($row->option_name)) echo $row->option_name;?>
									</td>
									<td class="center">
										<?php if(isset($row->option_holder)) echo get_language_value_for_keyword($row->option_holder,$this->data['langCode']);?>
									</td>
									<td class="center">
										<?php 
										$disp_status = get_language_value_for_keyword($row->status,$this->data['langCode']);
										if ($allPrev == '1' || in_array('2', $reviews)){
											if($row->status=='Active' || $row->status=='Inactive'){
												$mode = ($row->status == 'Active')?'0':'1';
											}else{
												$mode='2';
											}
											if ($mode == '0'){
										?>
											<a title="<?php if ($this->lang->line('common_click_to_inactive') != '') echo stripslashes($this->lang->line('common_click_to_inactive')); else echo 'Click to inactive'; ?>" class="tip_top" href="javascript:confirm_status('<?php echo ADMIN_ENC_URL;?>/reviews/change_reviews_status/<?php echo $mode;?>/<?php echo $row->_id;?>');">
												<span class="badge_style b_done"><?php echo $disp_status;?></span>
											</a>
										<?php
											}else if ($mode == '1'){	
										?>
											<a title="<?php if ($this->lang->line('common_click_to_active') != '') echo stripslashes($this->lang->line('common_click_to_active')); else echo 'Click to active'; ?>" class="tip_top" href="javascript:confirm_status('<?php echo ADMIN_ENC_URL;?>/reviews/change_reviews_status/<?php echo $mode;?>/<?php echo $row->_id;?>')">
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
										<?php if ($allPrev == '1' || in_array('2', $reviews)){?>
											<span>
												<a class="action-icons c-edit" href="<?php echo ADMIN_ENC_URL;?>/reviews/edit_review_option_form/<?php echo $row->_id;?>" title="<?php if ($this->lang->line('admin_common_edit') != '') echo stripslashes($this->lang->line('admin_common_edit')); else echo 'Edit'; ?>">
													<?php if ($this->lang->line('admin_common_edit') != '') echo stripslashes($this->lang->line('admin_common_edit')); else echo 'Edit'; ?>
												</a>
											</span>
                                         <span><a class="action-icons c-pencil_basic_red" href="<?php echo ADMIN_ENC_URL;?>/reviews/edit_language_review/<?php echo $row->_id;?>" title="<?php if ($this->lang->line('admin_common_lang_edit') != '') echo stripslashes($this->lang->line('admin_common_lang_edit')); else echo 'Edit Language'; ?>"><?php if ($this->lang->line('admin_common_lang_edit') != '') echo stripslashes($this->lang->line('admin_common_lang_edit')); else echo 'Edit Language'; ?></a></span>
										<?php }?>
										<?php if ($allPrev == '1' || in_array('3', $reviews)){?>	
											<span>
												<a class="action-icons c-delete" href="javascript:confirm_delete('<?php echo ADMIN_ENC_URL;?>/reviews/delete_reviews/<?php echo $row->_id;?>')" title="<?php if ($this->lang->line('admin_common_delete') != '') echo stripslashes($this->lang->line('admin_common_delete')); else echo 'Delete'; ?>">
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
										<?php if ($this->lang->line('admin_review_option_id') != '') echo stripslashes($this->lang->line('admin_review_option_id')); else echo 'Option Id'; ?>
									</th>
									<th>
										<?php if ($this->lang->line('admin_review_review_option') != '') echo stripslashes($this->lang->line('admin_review_review_option')); else echo 'Review Option'; ?>
									</th>
									<th>
										<?php if ($this->lang->line('admin_review_review_option_for') != '') echo stripslashes($this->lang->line('admin_review_review_option_for')); else echo 'Review Option For'; ?>
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