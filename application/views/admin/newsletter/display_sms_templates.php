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
						<?php /*if($templateList->num_rows() < 15){ ?>
							<div style="float: right;line-height:40px;padding:0px 10px;height:39px;">           
								<div class="btn_30_light" style="height: 29px; text-align:left;">
									<a href="admin/templates/add_edit_sms_templates_form" class="tipTop" original-title="<?php if ($this->lang->line('admin_sms_templates_add_new') != '') echo stripslashes($this->lang->line('admin_sms_templates_add_new')); else echo 'Add New Template'; ?>"><span class="icon add_co"></span><span class="btn_link"><?php if ($this->lang->line('admin_sms_templates_add_new') != '') echo stripslashes($this->lang->line('admin_sms_templates_add_new')); else echo 'Add New Template'; ?></span></a>
								</div>									
							</div>
						<?php }*/ ?>
					</div>
					<div class="widget_content">
						<table class="display" id="sms_newsletter_tbl">
							<thead>
								<tr>
									<th class="tip_top" title="<?php if ($this->lang->line('dash_click_sort') != '') echo stripslashes($this->lang->line('dash_click_sort')); else echo 'Click to sort'; ?>">
										 <?php if ($this->lang->line('admin_templates_template_id') != '') echo stripslashes($this->lang->line('admin_templates_template_id')); else echo 'Template Id'; ?>
									</th>
									<th class="tip_top" title="<?php if ($this->lang->line('dash_click_sort') != '') echo stripslashes($this->lang->line('dash_click_sort')); else echo 'Click to sort'; ?>">
										 <?php if ($this->lang->line('admin_templates_template_name') != '') echo stripslashes($this->lang->line('admin_templates_template_name')); else echo 'Template Name'; ?>
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
										<?php echo $row->template_name; ?>
									</td>
									<td class="center">
										<?php if ($allPrev == '1' || in_array('2', $templates)){?>
										<span>
											<a class="action-icons c-edit" href="<?php echo ADMIN_ENC_URL;?>/templates/add_edit_sms_templates_form/<?php echo $row->news_id;?>" title="<?php if ($this->lang->line('admin_common_edit') != '') echo stripslashes($this->lang->line('admin_common_edit')); else echo 'Edit'; ?>">
												<?php if ($this->lang->line('admin_common_edit') != '') echo stripslashes($this->lang->line('admin_common_edit')); else echo 'Edit'; ?>
											</a>
										</span>
										<?php }?>
										<span>
											<a class="action-icons c-suspend" href="<?php echo ADMIN_ENC_URL;?>/templates/view_sms_template/<?php echo $row->news_id;?>" title="<?php if ($this->lang->line('admin_common_view') != '') echo stripslashes($this->lang->line('admin_common_view')); else echo 'View'; ?>">
												<?php if ($this->lang->line('admin_common_view') != '') echo stripslashes($this->lang->line('admin_common_view')); else echo 'View'; ?>
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
										 <?php if ($this->lang->line('admin_templates_template_id') != '') echo stripslashes($this->lang->line('admin_templates_template_id')); else echo 'Template Id'; ?>
									</th>
									<th>
										  <?php if ($this->lang->line('admin_templates_template_name') != '') echo stripslashes($this->lang->line('admin_templates_template_name')); else echo 'Template Name'; ?>
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