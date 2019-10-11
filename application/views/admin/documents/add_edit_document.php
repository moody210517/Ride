<?php
$this->load->view(ADMIN_ENC_URL.'/templates/header.php');
?>
<style>
.chzn-container-single .chzn-single{
margin-left:0;
}
</style>
	<div id="content" class="base-app-top">
		<div class="grid_container">
			<div class="grid_12">
				<div class="widget_wrap">
					<div class="widget_top">
						<span class="h_icon list"></span>
						<h6><?php echo $heading; ?></h6>
                        <div id="widget_tab">
            			</div>
					</div>
					<div class="widget_content">
						<?php
						$attributes = array('class' => 'form_container left_label', 'id' => 'addEditvehicle_form','method'=>'POST','autocomplete'=>'off','enctype' => 'multipart/form-data');
						echo form_open(ADMIN_ENC_URL.'/documents/insertEditDocument', $attributes)
						?>
							<div class="base-appsec">
								<ul class="left_promo_base">
									
									<li>
										<div class="form_grid_12">
											<label class="field_title"><?php if ($this->lang->line('admin_document_document_category') != '') echo stripslashes($this->lang->line('admin_document_document_category')); else echo 'Document Category'; ?> <span class="req">*</span></label>
											<div class="form_input">
												<select class="chzn-select required" name="category"  style="width: 375px; display: none;" data-placeholder="Select Document">
													<option value="Driver" <?php if($form_mode){ if ($documentdetails->row()->category == 'Driver'){echo 'selected="selected"';} } ?>><?php if ($this->lang->line('admin_document_for_driver') != '') echo stripslashes($this->lang->line('admin_document_for_driver')); else echo 'For Driver'; ?></option>
													<option value="Vehicle" <?php if($form_mode){ if ($documentdetails->row()->category == 'Vehicle'){echo 'selected="selected"';} } ?>><?php if ($this->lang->line('admin_document_for_vehicle') != '') echo stripslashes($this->lang->line('admin_document_for_vehicle')); else echo 'For Vehicle'; ?></option>
												</select>
											</div>
										</div>
									</li>
									
									<li>
										<div class="form_grid_12">
											<label class="field_title"><?php if ($this->lang->line('admin_document_document_name') != '') echo stripslashes($this->lang->line('admin_document_document_name')); else echo 'Document Name'; ?> <span class="req">*</span></label>
											<div class="form_input">
												<input name="name" id="name" type="text"  class="large tipTop required" title="<?php if ($this->lang->line('admin_review_enter_document_name') != '') echo stripslashes($this->lang->line('admin_review_enter_document_name')); else echo 'Please enter document name'; ?>" value="<?php if($form_mode){ echo $documentdetails->row()->name; } ?>"/>
											</div>
										</div>
									</li>
                                    <li>
										<div class="form_grid_12">
											<label class="field_title"><?php if ($this->lang->line('admin_document_has_required') != '') echo stripslashes($this->lang->line('admin_document_has_required')); else echo 'Has Required'; ?></label>
											<div class="form_input">
												<div class="has_required">
													<input type="checkbox"  name="hasReq"  id="yes_no_yes" class="yes_no" <?php if($form_mode){ if ($documentdetails->row()->hasReq == 'Yes'){echo 'checked="checked"';} }else{echo 'checked="checked"';} ?> />
												</div>
											</div>
										</div>
									</li>
									
									<li>
										<div class="form_grid_12">
											<label class="field_title"><?php if ($this->lang->line('admin_document_has_expiry_date') != '') echo stripslashes($this->lang->line('admin_document_has_expiry_date')); else echo 'Has Expiry Date'; ?></label>
											<div class="form_input">
												<div class="has_expiry">
													<input type="checkbox"  name="hasExp"  id="has_Expiry" class="yes_no" <?php if($form_mode){ if ($documentdetails->row()->hasExp == 'Yes'){echo 'checked="checked"';} }else{echo 'checked="checked"';} ?> />
												</div>
											</div>
										</div>
									</li>
									
									
									
									<li>
										<div class="form_grid_12">
											<label class="field_title"><?php if ($this->lang->line('admin_common_status') != '') echo stripslashes($this->lang->line('admin_common_status')); else echo 'Status'; ?></label>
											<div class="form_input">
												<div class="active_inactive">
													<input type="checkbox"  name="status"  id="active_inactive_active" class="active_inactive" <?php if($form_mode){ if ($documentdetails->row()->status == 'Active'){echo 'checked="checked"';} }else{echo 'checked="checked"';} ?> />
												</div>
											</div>
										</div>
									</li>
									
									
									
								</ul>
								
								<ul class="last-btn-submit">
									<li>
										<div class="form_grid_12">
											<div class="form_input">
												<input type="hidden" name="document_id" id="document_id" value="<?php if($form_mode){ echo $documentdetails->row()->_id; } ?>"  />
												<button type="submit" class="btn_small btn_blue" ><span><?php if ($this->lang->line('admin_common_submit') != '') echo stripslashes($this->lang->line('admin_common_submit')); else echo 'Submit'; ?></span></button>
											</div>
										</div>
									</li>
								</ul>
								
								
						   </div>
					   
						</form>
					</div>
				</div>
			</div>
		</div>
		<span class="clear"></span>
	</div>
	</div>
<?php 
$this->load->view(ADMIN_ENC_URL.'/templates/footer.php');
?>
<?php $checkbox_lan=get_language_array_for_keyword($this->data['langCode']);?>
<script>
$(document).ready(function() {
    $('.has_expiry :checkbox').iphoneStyle({
		checkedLabel: '<?php echo $checkbox_lan['verify_status_yes_ucfirst']; ?>', 
		uncheckedLabel: '<?php echo $checkbox_lan['verify_status_no_ucfirst']; ?>' ,
		onChange: function(elem, value) {
			if($(elem)[0].checked==false){
				
			}else{
				var onchange_checkbox = ($('.has_required :checkbox')).iphoneStyle();
				if(!onchange_checkbox.is(':checked')){
					onchange_checkbox.prop('checked', true).iphoneStyle("refresh");
				}
			}
		}
	});
    $('.has_required :checkbox').iphoneStyle({
		checkedLabel: '<?php echo $checkbox_lan['verify_status_yes_ucfirst']; ?>', 
		uncheckedLabel: '<?php echo $checkbox_lan['verify_status_no_ucfirst']; ?>' ,
	});
});
</script>