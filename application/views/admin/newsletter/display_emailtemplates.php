<?php
$this->load->view(ADMIN_ENC_URL.'/templates/header.php');
extract($privileges);
?>
<div id="content">
	<div class="grid_container">
		<?php 
			$attributes = array('id' => 'display_form');
			echo form_open(ADMIN_ENC_URL.'/templates/change_email_template_status_global',$attributes) 
		?>
			<div class="grid_12">
				<div class="widget_wrap">
					<div class="widget_top">
						<span class="h_icon blocks_images"></span>
						<h6><?php echo $heading;?></h6>
					</div>
					<div class="widget_content">
						<table class="display" id="newsletter_tbl">
							<thead>
								<tr>
									<th class="tip_top" title="<?php if ($this->lang->line('dash_click_sort') != '') echo stripslashes($this->lang->line('dash_click_sort')); else echo 'Click to sort'; ?>">
										 <?php if ($this->lang->line('admin_templates_template_id') != '') echo stripslashes($this->lang->line('admin_templates_template_id')); else echo 'Template Id'; ?>
									</th>
									<th class="tip_top" title="<?php if ($this->lang->line('dash_click_sort') != '') echo stripslashes($this->lang->line('dash_click_sort')); else echo 'Click to sort'; ?>">
										 <?php if ($this->lang->line('admin_templates_template_name') != '') echo stripslashes($this->lang->line('admin_templates_template_name')); else echo 'Template Name'; ?>
									</th>
									<th class="center tip_top" title="<?php if ($this->lang->line('dash_click_sort') != '') echo stripslashes($this->lang->line('dash_click_sort')); else echo 'Click to sort'; ?>">
									<?php if ($this->lang->line('admin_templates_email_subject') != '') echo stripslashes($this->lang->line('admin_templates_email_subject')); else echo 'Email Subject'; ?>
									</th>
									<th>
										 <?php if ($this->lang->line('admin_common_action') != '') echo stripslashes($this->lang->line('admin_common_action')); else echo 'Action'; ?>
									</th>
								</tr>
							</thead>
							<tbody>
							<?php 
							if ($templateList->num_rows() > 0){
								foreach ($templateList->result() as $row){
							?>
								<tr>
									<td class="center  tr_select">
										<?php echo $row->news_id;?>
									</td>
									<td class="center  tr_select">
										<?php echo $row->message['title'];?>
									</td>
									<td class="center tr_select ">
										<?php echo $row->message['subject'];?>
									</td>
									<td class="center action-icons-wrap">
										<?php if ($allPrev == '1' || in_array('2', $templates)){?>
										<span>
											<a class="action-icons c-edit" href="<?php echo ADMIN_ENC_URL;?>/templates/edit_email_template_form/<?php echo $row->_id;?>" title="<?php if ($this->lang->line('admin_common_edit') != '') echo stripslashes($this->lang->line('admin_common_edit')); else echo 'Edit'; ?>">
												<?php if ($this->lang->line('admin_common_edit') != '') echo stripslashes($this->lang->line('admin_common_edit')); else echo 'Edit'; ?>
											</a>
										</span>
										<?php }?>
										<span>
											<a class="action-icons c-suspend" href="<?php echo ADMIN_ENC_URL;?>/templates/view_email_template/<?php echo $row->_id;?>" title="<?php if ($this->lang->line('admin_common_view') != '') echo stripslashes($this->lang->line('admin_common_view')); else echo 'View'; ?>">
												<?php if ($this->lang->line('admin_common_view') != '') echo stripslashes($this->lang->line('admin_common_view')); else echo 'View'; ?>
											</a>
										</span>
										<?php 
										if ($allPrev == '1' || in_array('3', $templates)){
											$EmailtempId=array('1','2','3','4','5','6','7','8','9','10','11','12','13','14','15','16','17','18','19','20','21','22','23');
											if(!in_array($row->news_id,$EmailtempId)){
										?>	
										<span>
											<a class="action-icons c-delete" href="javascript:confirm_delete('<?php echo ADMIN_ENC_URL;?>/templates/delete_email_template/<?php echo $row->_id;?>')" title="<?php if ($this->lang->line('admin_common_delete') != '') echo stripslashes($this->lang->line('admin_common_delete')); else echo 'Delete'; ?>">
												
											</a>
										</span>
										<?php 
											}
										}
										?>
									</td>
								</tr>
							<?php 
								}
							}
							?>
							</tbody>
							<tfoot>
								<tr>
									<th>
										 <?php if ($this->lang->line('admin_templates_template_id') != '') echo stripslashes($this->lang->line('admin_templates_template_id')); else echo 'Template Id'; ?>
									</th>
									<th>
										  <?php if ($this->lang->line('admin_templates_template_name') != '') echo stripslashes($this->lang->line('admin_templates_template_name')); else echo 'Template Name'; ?>
									</th>
									<th class="center">
										<?php if ($this->lang->line('admin_templates_email_subject') != '') echo stripslashes($this->lang->line('admin_templates_email_subject')); else echo 'Email Subject'; ?>
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
		</form>
	</div>
	<span class="clear"></span>
</div>
</div>
<?php 
$this->load->view(ADMIN_ENC_URL.'/templates/footer.php');
?>