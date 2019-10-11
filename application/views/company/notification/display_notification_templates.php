<?php
$this->load->view(COMPANY_NAME.'/templates/header.php');

?>
<div id="content">
	<div class="grid_container">
		<?php 
			$attributes = array('id' => 'display_form');
			echo form_open(COMPANY_NAME.'/notification/change_notification_newsletter_status_global',$attributes) 
		?>
			<div class="grid_12">
				<div class="widget_wrap">
					<div class="widget_top">
						<span class="h_icon blocks_images"></span>
						<h6><?php echo $heading;?></h6>
						<div style="float: right;line-height:40px;padding:0px 10px;height:39px;">
						<?php if ($allPrev == '1' || in_array('1', $vehicle)){?>
                        
							<div class="btn_30_light" style="height: 29px; text-align:left;">
								<a href="<?php echo COMPANY_NAME; ?>/notification/add_notification_template" class="tipTop" title="<?php if ($this->lang->line('admin_payment_gateway_add_new_notification') != '') echo stripslashes($this->lang->line('admin_payment_gateway_add_new_notification')); else echo 'Click here to Add New Notification'; ?>"><span class="icon add_co"></span><span class="btn_link"><?php if ($this->lang->line('admin_common_add_new') != '') echo stripslashes($this->lang->line('admin_common_add_new')); else echo 'Add New'; ?></span></a>
							</div>
                            

						<?php } ?>
						</div>
					</div>
					<div class="widget_content">
						<table class="display" id="newsletter_tbl">
							<thead>
								<tr>
									<th>
										<?php if ($this->lang->line('admin_notification_template_id') != '') echo stripslashes($this->lang->line('admin_notification_template_id')); else echo 'Template id'; ?>
									</th>
									<th>
										<?php if ($this->lang->line('admin_notification_title') != '') echo stripslashes($this->lang->line('admin_notification_title')); else echo 'Title'; ?>
									</th>
									<th class="center">
										<?php if ($this->lang->line('admin_notification_type') != '') echo stripslashes($this->lang->line('admin_notification_type')); else echo 'Type'; ?>
									</th>
									<th>
										<?php if ($this->lang->line('admin_common_action') != '') echo stripslashes($this->lang->line('admin_common_action')); else echo 'Action'; ?>
									</th>
								</tr>
							</thead>
							<tbody>
							<?php 
							if ($display_newsletters->num_rows() > 0){
								foreach ($display_newsletters->result() as $row){
							?>
								<tr>
									<td class="center  tr_select">
										<?php echo $row->news_id;?>
									</td>
									<td class="center  tr_select">
										<?php echo $row->message['title'];?>
									</td>
									<td class="center tr_select ">
										<?php echo  get_language_value_for_keyword(ucfirst($row->notification_type),$this->data['langCode']);?>
									</td>
									<td class="center">
										<?php if ($allPrev == '1' || in_array('2', $templates)){?>
										<span>
											<a class="action-icons c-edit" href="<?php echo COMPANY_NAME; ?>/notification/edit_notification_template/<?php echo $row->_id;?>" title="<?php if ($this->lang->line('admin_common_edit') != '') echo stripslashes($this->lang->line('admin_common_edit')); else echo 'Edit'; ?>">
												<?php if ($this->lang->line('admin_common_edit') != '') echo stripslashes($this->lang->line('admin_common_edit')); else echo 'Edit'; ?>
											</a>
										</span>
										<?php }?>
										<span>
											<a class="action-icons c-suspend" href="<?php echo COMPANY_NAME; ?>/notification/view_notification_template/<?php echo $row->_id;?>" title="<?php if ($this->lang->line('admin_common_view') != '') echo stripslashes($this->lang->line('admin_common_view')); else echo 'View'; ?>">
												<?php if ($this->lang->line('admin_common_view') != '') echo stripslashes($this->lang->line('admin_common_view')); else echo 'View'; ?>
											</a>
										</span>

										<span>
											<a class="action-icons c-delete" href="javascript:confirm_delete('<?php echo COMPANY_NAME; ?>/notification/delete_email_template/<?php echo $row->_id;?>')" title="<?php if ($this->lang->line('admin_common_delete') != '') echo stripslashes($this->lang->line('admin_common_delete')); else echo 'Delete'; ?>">
												<?php if ($this->lang->line('admin_common_delete') != '') echo stripslashes($this->lang->line('admin_common_delete')); else echo 'Delete'; ?>
											</a>
										</span>
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
										<?php if ($this->lang->line('admin_notification_template_id') != '') echo stripslashes($this->lang->line('admin_notification_template_id')); else echo 'Template id'; ?>
									</th>
									<th>
										<?php if ($this->lang->line('admin_notification_title') != '') echo stripslashes($this->lang->line('admin_notification_title')); else echo 'Title'; ?>
									</th>
									<th>
										<?php if ($this->lang->line('admin_notification_type') != '') echo stripslashes($this->lang->line('admin_notification_type')); else echo 'Type'; ?>
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
<?php 
$this->load->view(COMPANY_NAME.'/templates/footer.php');
?>