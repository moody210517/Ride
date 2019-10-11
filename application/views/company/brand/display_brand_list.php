<?php
$this->load->view(COMPANY_NAME.'/templates/header.php');
?>
<div id="content">
		<div class="grid_container">
			<?php 
				$attributes = array('id' => 'display_form');
				echo form_open(COMPANY_NAME.'/brand/change_brand_status_global',$attributes) 
			?>
			<div class="grid_12">
				<div class="widget_wrap">
					<div class="widget_top">
						<span class="h_icon blocks_images"></span>
						<h6><?php echo $heading?></h6>
						<div style="float: right;line-height:40px;padding:0px 10px;height:39px;">
                        
							<div class="btn_30_light" style="height: 29px; text-align:left;">
								<a href="<?php echo COMPANY_NAME;?>/brand/add_brand_form" class="tipTop" title="<?php if ($this->lang->line('make_model_add_new_brand') != '') echo stripslashes($this->lang->line('make_model_add_new_brand')); else echo 'Click here to Add New Brand'; ?>"><span class="icon add_co"></span><span class="btn_link"><?php if ($this->lang->line('admin_car_add_new') != '') echo stripslashes($this->lang->line('admin_car_add_new')); else echo 'Add New'; ?></span></a>
							</div>
						
						
						
						</div>
					</div>
					<div class="widget_content">
						<table class="display display_tbl" id="brand_tbl">
							<thead>
								<tr>
									<th class="center">
										<?php if ($this->lang->line('operator_s_no') != '') echo stripslashes($this->lang->line('operator_s_no')); else echo 'S.No'; ?>
									</th>
									<th class="tip_top" title="<?php if ($this->lang->line('dash_click_sort') != '') echo stripslashes($this->lang->line('dash_click_sort')); else echo 'Click to sort'; ?>">
										 <?php if ($this->lang->line('admin_make_and_model_marker_list_brand_name') != '') echo stripslashes($this->lang->line('admin_make_and_model_marker_list_brand_name')); else echo 'Brand Name'; ?>
									</th>
									<th class="tip_top">
										<?php if ($this->lang->line('admin_make_and_model_marker_list_brand_logo') != '') echo stripslashes($this->lang->line('admin_make_and_model_marker_list_brand_logo')); else echo 'Brand Logo'; ?>
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
								<?php $i =0;
								if ($brandList->num_rows() > 0){
									foreach ($brandList->result() as $row){ $i++;
								?>
								<tr>
									<td class="center tr_select ">
										<?php echo $i; ?>
									</td>
									<td class="center">
										<?php echo $row->brand_name;?>
									</td>
									<td class="center">
										<?php 
										if(isset($row->brand_logo)){ 
											if($row->brand_logo!=""){ 
												$brand_logo=BRAND_THUMB.$row->brand_logo;
											}else{
												$brand_logo=BRAND_THUMB_DEFAULT;
											}
										}else{
											$brand_logo=BRAND_THUMB_DEFAULT;
										}
										?>
										<img src="<?php echo base_url().$brand_logo; ?>" alt="<?php echo $row->brand_name;?>" />
									</td>
									<td class="center">
									<?php 
									$disp_status = get_language_value_for_keyword($row->status,$this->data['langCode']);
										$mode = ($row->status == 'Active')?'0':'1';
										if ($mode == '0'){
									?>
										<a title="<?php if ($this->lang->line('common_click_inactive') != '') echo stripslashes($this->lang->line('common_click_inactive')); else echo 'Click to inactive'; ?>" class="tip_top" href="javascript:confirm_status('<?php echo COMPANY_NAME;?>/brand/change_brand_status/<?php echo $mode;?>/<?php echo $row->_id;?>');"><span class="badge_style b_done"><?php echo $disp_status;?></span></a>
									<?php
										}else {	
									?>
										<a title="<?php if ($this->lang->line('common_click_active') != '') echo stripslashes($this->lang->line('common_click_active')); else echo 'Click to active'; ?>" class="tip_top" href="javascript:confirm_status('<?php echo COMPANY_NAME;?>/brand/change_brand_status/<?php echo $mode;?>/<?php echo $row->_id;?>')"><span class="badge_style"><?php echo $disp_status;?></span></a>
									<?php } ?>
									</td>
									<td class="center">
										<span><a class="action-icons c-edit" href="<?php echo COMPANY_NAME;?>/brand/edit_brand_form/<?php echo $row->_id;?>" title="<?php if ($this->lang->line('admin_common_edit') != '') echo stripslashes($this->lang->line('admin_common_edit')); else echo 'Edit'; ?>"><?php if ($this->lang->line('admin_common_edit') != '') echo stripslashes($this->lang->line('admin_common_edit')); else echo 'Edit'; ?></a></span>
									
										<span><a class="action-icons c-delete" href="javascript:confirm_delete('<?php echo COMPANY_NAME;?>/brand/delete_brand/<?php echo $row->_id;?>')" title="<?php if ($this->lang->line('admin_common_delete') != '') echo stripslashes($this->lang->line('admin_common_delete')); else echo 'Delete'; ?>"><?php if ($this->lang->line('admin_common_delete') != '') echo stripslashes($this->lang->line('admin_common_delete')); else echo 'Delete'; ?></a></span>
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
										<?php if ($this->lang->line('operator_s_no') != '') echo stripslashes($this->lang->line('operator_s_no')); else echo 'S.No'; ?>
									</th>
									<th>
										 <?php if ($this->lang->line('admin_make_and_model_marker_list_brand_name') != '') echo stripslashes($this->lang->line('admin_make_and_model_marker_list_brand_name')); else echo 'Brand Name'; ?>
									</th>
									<th>
										 <?php if ($this->lang->line('admin_make_and_model_marker_list_brand_logo') != '') echo stripslashes($this->lang->line('admin_make_and_model_marker_list_brand_logo')); else echo 'Brand Logo'; ?>
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
$this->load->view(COMPANY_NAME.'/templates/footer.php');
?>