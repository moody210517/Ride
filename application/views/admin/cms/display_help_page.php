<?php
$this->load->view(ADMIN_ENC_URL.'/templates/header.php');
extract($privileges);
?>
<div id="content">
		<div class="grid_container">
			<?php 
				$attributes = array('id' => 'display_form');
				echo form_open(ADMIN_ENC_URL.'/cms/change_help_page_status_global',$attributes) 
			?>
			<div class="grid_12">
				<div class="widget_wrap">
					<div class="widget_top">
						<span class="h_icon blocks_images"></span>
						<h6><?php echo $heading?></h6>
						<div style="float: right;line-height:40px;padding:0px 10px;height:39px;">
						<?php if ($allPrev == '1' || in_array('2', $cms)){?>
							<div class="btn_30_light" style="height: 29px;">
								<a href="javascript:void(0)" onclick="return checkBoxValidationAdmin('Publish','<?php echo $subAdminMail; ?>');" class="tipTop" title="<?php if ($this->lang->line('driver_select_publish_records') != '') echo stripslashes($this->lang->line('driver_select_publish_records')); else echo 'Select any checkbox and click here to publish records'; ?>"><span class="icon accept_co"></span><span class="btn_link"><?php if ($this->lang->line('admin_common_publish') != '') echo stripslashes($this->lang->line('admin_common_publish')); else echo 'Publish'; ?></span></a>
							</div>
							<div class="btn_30_light" style="height: 29px;">
								<a href="javascript:void(0)" onclick="return checkBoxValidationAdmin('Unpublish','<?php echo $subAdminMail; ?>');" class="tipTop" title="<?php if ($this->lang->line('driver_select_unpublish_records') != '') echo stripslashes($this->lang->line('driver_select_unpublish_records')); else echo 'Select any checkbox and click here to unpublish records'; ?>"><span class="icon delete_co"></span><span class="btn_link"><?php if ($this->lang->line('admin_common_unpublish') != '') echo stripslashes($this->lang->line('admin_common_unpublish')); else echo 'Unpublish'; ?></span></a>
							</div>
						<?php 
						}
						if ($allPrev == '1' || in_array('3', $cms)){
						?>
							<div class="btn_30_light" style="height: 29px;">
								<a href="javascript:void(0)" onclick="return checkBoxValidationAdmin('Delete','<?php echo $subAdminMail; ?>');" class="tipTop" title="<?php if ($this->lang->line('common_select_delete_records') != '') echo stripslashes($this->lang->line('common_select_delete_records')); else echo 'Select any checkbox and click here to delete records'; ?>"><span class="icon cross_co"></span><span class="btn_link"><?php if ($this->lang->line('admin_common_delete') != '') echo stripslashes($this->lang->line('admin_common_delete')); else echo 'Delete'; ?></span></a>
							</div>
						<?php }?>
						</div>
					</div>
					<div class="widget_content">
						<table class="display display_tbl">
						<thead>
						<tr>
							<th class="center">
								<input name="checkbox_id[]" type="checkbox" value="on" class="checkall">
							</th>
							<th class="tip_top" title="<?php if ($this->lang->line('dash_click_sort') != '') echo stripslashes($this->lang->line('dash_click_sort')); else echo 'Click to sort'; ?>">
								 <?php if ($this->lang->line('admin_cms_page_name') != '') echo stripslashes($this->lang->line('admin_cms_page_name')); else echo 'Page Name'; ?>
							</th>
							<!--<th class="tip_top" title="Click to sort">
								Page Title
							</th>-->
							<th class="tip_top" title="<?php if ($this->lang->line('dash_click_sort') != '') echo stripslashes($this->lang->line('dash_click_sort')); else echo 'Click to sort'; ?>">
								<?php if ($this->lang->line('admin_cms_page_url') != '') echo stripslashes($this->lang->line('admin_cms_page_url')); else echo 'Page Url'; ?>
							</th>
							<!--<th class="tip_top" title="Click to sort">
								Category
							</th>
							<th class="tip_top" title="Click to sort">
								Hidden Page
							</th>-->
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
						if ($cmsList->num_rows() > 0){
							foreach ($cmsList->result() as $row){
						?>
						<tr>
							<td class="center tr_select ">
								<input name="checkbox_id[]" type="checkbox" value="<?php echo $row->id;?>">
							</td>
							<td class="center">
								<?php echo $row->page_name;?>
							</td>
							<!--<td class="center">
								<?php echo $row->page_title;?>
							</td>-->
							<td class="center">
								<a href="<?php echo base_url().'pages/'.$row->seourl;?>" target="_blank"><?php echo base_url().'pages/'.$row->seourl;?></a>
							</td>
							<!--<td class="center">
								<?php echo $row->category;?>
							</td>
							<td class="center">
							<?php 
							if ($allPrev == '1' || in_array('2', $cms)){
								$mode = ($row->hidden_page == 'Yes')?'No':'Yes';
								if ($mode == 'No'){
							?>
								<a title="Click to hide this page" class="tip_top" href="javascript:confirm_mode('admin/cms/change_cms_mode/<?php echo $mode;?>/<?php echo $row->id;?>');"><span class="badge_style b_done"><?php echo $row->hidden_page;?></span></a>
							<?php
								}else {	
							?>
								<a title="Click to unhide this page" class="tip_top" href="javascript:confirm_mode('admin/cms/change_cms_mode/<?php echo $mode;?>/<?php echo $row->id;?>')"><span class="badge_style"><?php echo $row->hidden_page;?></span></a>
							<?php 
								}
							}else {
							?>
							<span class="badge_style b_done"><?php echo $row->hidden_page;?></span>
							<?php }?>
							</td>-->
							<td class="center">
							<?php 
							if ($allPrev == '1' || in_array('2', $cms)){
								$mode = ($row->status == 'Publish')?'0':'1';
								if ($mode == '0'){
							?>
								<a title="<?php if ($this->lang->line('admin_click_to_unpublish') != '') echo stripslashes($this->lang->line('admin_click_to_unpublish')); else echo 'Click to unpublish'; ?>" class="tip_top" href="javascript:confirm_status('<?php echo ADMIN_ENC_URL;?>/cms/change_help_page_status/<?php echo $mode;?>/<?php echo $row->id;?>');"><span class="badge_style b_done"><?php echo $row->status;?></span></a>
							<?php
								}else {	
							?>
								<a title="<?php if ($this->lang->line('admin_click_to_publish') != '') echo stripslashes($this->lang->line('admin_click_to_publish')); else echo 'Click to publish'; ?>" class="tip_top" href="javascript:confirm_status('<?php echo ADMIN_ENC_URL;?>/cms/change_help_page_status/<?php echo $mode;?>/<?php echo $row->id;?>')"><span class="badge_style"><?php echo $row->status;?></span></a>
							<?php 
								}
							}else {
							?>
							<span class="badge_style b_done"><?php echo $row->status;?></span>
							<?php }?>
							</td>
							<td class="center">
							<?php if ($allPrev == '1' || in_array('2', $cms)){?>
								<span><a class="action-icons c-edit" href="<?php echo ADMIN_ENC_URL;?>/cms/edit_help_page_form/<?php echo $row->id;?>" title="<?php if ($this->lang->line('admin_common_edit') != '') echo stripslashes($this->lang->line('admin_common_edit')); else echo 'Edit'; ?>"><?php if ($this->lang->line('admin_common_edit') != '') echo stripslashes($this->lang->line('admin_common_edit')); else echo 'Edit'; ?></a></span>
							<?php }?>
							<?php if ($allPrev == '1' || in_array('3', $cms)){?>	
								<span><a class="action-icons c-delete" href="javascript:confirm_delete('<?php echo ADMIN_ENC_URL;?>/cms/delete_help_subpage/<?php echo $row->id;?>')" title="<?php if ($this->lang->line('admin_common_delete') != '') echo stripslashes($this->lang->line('admin_common_delete')); else echo 'Delete'; ?>"><?php if ($this->lang->line('admin_common_delete') != '') echo stripslashes($this->lang->line('admin_common_delete')); else echo 'Delete'; ?></a></span>
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
								 <?php if ($this->lang->line('admin_cms_page_name') != '') echo stripslashes($this->lang->line('admin_cms_page_name')); else echo 'Page Name'; ?>
							</th>
							<!--<th>
								 Page Title
							</th>-->
 							<th>
								<?php if ($this->lang->line('admin_cms_page_url') != '') echo stripslashes($this->lang->line('admin_cms_page_url')); else echo 'Page Url'; ?>
							</th>
							<!--<th>
								Category
							</th>
							<th>
								Hidden Page
							</th>-->
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