<?php
$this->load->view(ADMIN_ENC_URL.'/templates/header.php');
extract($privileges);
?>
<div id="content" class="disply_docum">
		<div class="grid_container">
			<?php 
				$attributes = array('id' => 'display_form');
				echo form_open(ADMIN_ENC_URL.'/documents/change_document_status_global',$attributes) 
			?>
			<div class="grid_12">
				<div class="widget_wrap">
					<div class="widget_top">
						<span class="h_icon blocks_images"></span>
						<h6><?php echo $heading?></h6>
						<div style="float: right;line-height:40px;padding:0px 10px;height:39px;">
						<?php if ($allPrev == '1' || in_array('1', $documents)){?>
							<div class="btn_30_light" style="height: 29px; text-align:left;">
								<a href="<?php echo ADMIN_ENC_URL;?>/documents/add_edit_document_form" class="tipTop" title="<?php if ($this->lang->line('common_validate_add_new_document') != '') echo stripslashes($this->lang->line('common_validate_add_new_document')); else echo 'Click here to Add New Documents'; ?>"><span class="icon add_co addnew-btn2"></span><!-- <span class="btn_link"><?php if ($this->lang->line('admin_common_add_new') != '') echo stripslashes($this->lang->line('admin_common_add_new')); else echo 'Add New'; ?></span> --></a>
							</div>
						<?php } ?>
						<?php if ($allPrev == '1' || in_array('2', $documents)){?>
							<div class="btn_30_light" style="height: 29px;">
								<a href="javascript:void(0)" onclick="return checkBoxValidationAdmin('Active','<?php echo $subAdminMail; ?>');" class="tipTop" title="<?php if ($this->lang->line('driver_select_publish_records') != '') echo stripslashes($this->lang->line('driver_select_publish_records')); else echo 'Select any checkbox and click here to publish records'; ?>"><!-- <span class="icon accept_co"></span> --><span class="btn_link act-btn"><?php if ($this->lang->line('admin_common_active') != '') echo stripslashes($this->lang->line('admin_common_active')); else echo 'Active'; ?></span></a>
							</div>
							<div class="btn_30_light" style="height: 29px;">
								<a href="javascript:void(0)" onclick="return checkBoxValidationAdmin('Inactive','<?php echo $subAdminMail; ?>');" class="tipTop" title="<?php if ($this->lang->line('driver_select_unpublish_records') != '') echo stripslashes($this->lang->line('driver_select_unpublish_records')); else echo 'Select any checkbox and click here to Unpublish records'; ?>"><!-- <span class="icon delete_co"></span> --><span class="btn_link inact-btn"><?php if ($this->lang->line('admin_common_inactive') != '') echo stripslashes($this->lang->line('admin_common_inactive')); else echo 'Inactive'; ?></span></a>
							</div>
						<?php 
						}
						if ($allPrev == '1' || in_array('3', $documents)){
						?>
							<div class="btn_30_light" style="height: 29px;">
								<a href="javascript:void(0)" onclick="return checkBoxValidationAdmin('Delete','<?php echo $subAdminMail; ?>');" class="tipTop" title="<?php if ($this->lang->line('common_select_delete_records') != '') echo stripslashes($this->lang->line('common_select_delete_records')); else echo 'Select any checkbox and click here to delete records'; ?>"><!--<span class="icon cross_co del-btn"></span>--><span class="btn_link"><?php if ($this->lang->line('admin_common_delete') != '') echo stripslashes($this->lang->line('admin_common_delete')); else echo 'Delete'; ?></span></a>
							</div>
						<?php }?>
						</div>
					</div>
					<div class="widget_content">
						<div class="documentView">
							<?php if(!empty($documentsType)){ ?>
							<?php foreach($documentsType as $types){ ?>
							<div class="category-wrap" onclick="javascript:showView('<?php echo $types['category']; ?>')" id="DocTypeDiv_<?php echo $types['category']; ?>">
								<span class="maincat">
									<strong><?php if ($this->lang->line('common_label_for') != '') echo stripslashes($this->lang->line('common_label_for')); else echo 'For'; ?> <?php echo get_language_value_for_keyword($types['category'],$this->data['langCode']); ?></strong>
								</span>
								<?php /* <div class="mainshow">
									<div title="ShowDown" onclick="javascript:showView('<?php echo $types['category']; ?>')" id="DocType_<?php echo $types['category']; ?>" class="dropdown-button"></div>
								</div> */ ?>
							</div>
		
							<div id="DocList_<?php echo $types['category']; ?>" >
								<?php if(!empty($documentList[$types['category']])){ ?>
								<?php foreach($documentList[$types['category']] as $list){ ?>
								<span class="subcat1">
									<input type="checkbox" value="<?php echo $list->_id; ?>" name="checkbox_id[]">
									<strong><?php echo $list->name; ?></strong>
								</span>
								<div class="subview1">
									<span>
										<?php if($list->status=='Active'){ ?>
										<a href="javascript:confirm_status('<?php echo ADMIN_ENC_URL;?>/documents/change_document_status/0/<?php echo $list->_id; ?>')" class="action-icons c-active" original-title="<?php if ($this->lang->line('common_click_to_inactive') != '') echo stripslashes($this->lang->line('common_click_to_inactive')); else echo 'Click to Inactive'; ?>">
											<span class="badge_style b_done">&nbsp;</span>
										</a>
										<?php }else if($list->status=='Inactive'){ ?>
										<a href="javascript:confirm_status('<?php echo ADMIN_ENC_URL;?>/documents/change_document_status/1/<?php echo $list->_id; ?>')" class="action-icons c-inactive" original-title="<?php if ($this->lang->line('common_click_to_active') != '') echo stripslashes($this->lang->line('common_click_to_active')); else echo 'Click to Active'; ?>">
											<span class="delete_co">&nbsp;</span>
										</a>
										<?php }else{ ?>
										<?php } ?>
									</span>
									<span>
										<?php if ($allPrev == '1' || in_array('2', $documents)){?>	
										
											<?php /* <span class="view_cat"><a class="action-icons c-pencil_basic_red" href="admin/documents/edit_language_document/<?php echo $list->_id;?>" title="<?php if ($this->lang->line('admin_common_lang_edit') != '') echo stripslashes($this->lang->line('admin_common_lang_edit')); else echo 'Edit Language'; ?>"><?php if ($this->lang->line('admin_common_lang_edit') != '') echo stripslashes($this->lang->line('admin_common_lang_edit')); else echo 'Edit Language'; ?></a></span> */ ?>
										
										<span class="view_cat">
											<a href="<?php echo ADMIN_ENC_URL;?>/documents/add_edit_document_form/<?php echo $list->_id; ?>" class="action-icons c-edit" original-title="<?php if ($this->lang->line('admin_common_edit') != '') echo stripslashes($this->lang->line('admin_common_edit')); else echo 'Edit'; ?>"><?php if ($this->lang->line('admin_common_edit') != '') echo stripslashes($this->lang->line('admin_common_edit')); else echo 'Edit'; ?></a>
										</span>
										<?php } ?>
										<?php if ($allPrev == '1' || in_array('3', $documents)){?>	
										<span class="view_cat">
											<a href="javascript:confirm_delete('<?php echo ADMIN_ENC_URL;?>/documents/delete_documents/<?php echo $list->_id; ?>')" class="action-icons c-delete" original-title="<?php if ($this->lang->line('admin_common_delete') != '') echo stripslashes($this->lang->line('admin_common_delete')); else echo 'Delete'; ?>"><?php if ($this->lang->line('admin_common_delete') != '') echo stripslashes($this->lang->line('admin_common_delete')); else echo 'Delete'; ?></a>
										</span>
										<?php } ?>
									</span>
								</div>
								<?php } ?>
								<?php } ?>
							</div>	
							<?php } ?>
							<?php } ?>
						</div>
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
<script>
function showView(val){
	/* $('#DocList_'+val).show();
	$('#DocType_'+val).attr('class','dropdown-up');
	$('#DocType_'+val).attr('onClick','javascript:showHide(\''+val+'\')');
	$('#DocType_'+val).attr('title','ShowUp');
	$('#DocTypeDiv_'+val).attr('onClick','javascript:showHide(\''+val+'\')'); */
}
function showHide(val){
	/* $('#DocList_'+val).hide();
	$('#DocType_'+val).attr('class','dropdown-button');
	$('#DocType_'+val).attr('onClick','javascript:showView(\''+val+'\')');
	$('#DocType_'+val).attr('title','ShowDown');
	$('#DocTypeDiv_'+val).attr('onClick','javascript:showView(\''+val+'\')'); */
}
</script>
<?php 
$this->load->view(ADMIN_ENC_URL.'/templates/footer.php');
?>