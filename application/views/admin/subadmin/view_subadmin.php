<?php
$this->load->view(ADMIN_ENC_URL.'/templates/header.php');
?>
<div id="content" class="add-subadmin-sec">
		<div class="grid_container">
			<div class="grid_12">
				<div class="widget_wrap">
					<div class="widget_top">
						<span class="h_icon list"></span>
						<h6><?php if ($this->lang->line('admin_subadmin_view_subadmin') != '') echo stripslashes($this->lang->line('admin_subadmin_view_subadmin')); else echo 'View Subadmin'; ?></h6>
					</div>
					<div class="widget_content">
					<?php 
						$attributes = array('class' => 'form_container left_label');
						echo form_open(ADMIN_ENC_URL,$attributes) 
					?>
	 						<ul class="inner-subadmin new-sec-listing">
	 							<li>
								<div class="form_grid_12">
									<label class="field_title"><?php if ($this->lang->line('admin_subadmin_email_address') != '') echo stripslashes($this->lang->line('admin_subadmin_email_address')); else echo 'Email Address'; ?></label>
									<div class="form_input">
										<?php echo $admin_details->row()->email;?>
									</div>
								</div>
								</li>
								<li>
								<div class="form_grid_12">
									<label class="field_title"><?php if ($this->lang->line('admin_subadmin_sub_admin_name') != '') echo stripslashes($this->lang->line('admin_subadmin_sub_admin_name')); else echo 'Sub Admin Name'; ?></label>
									<div class="form_input">
										<?php echo $admin_details->row()->admin_name;?>
									</div>
								</div>
								</li>
								<li class="sel-all-management">
								<div class="form_grid_12 manage-time-table">
									<label class="field_title"><?php if ($this->lang->line('admin_subadmin_mangement_name') != '') echo stripslashes($this->lang->line('admin_subadmin_mangement_name')); else echo 'Management Name'; ?></label>
									<table border="0" cellspacing="0" cellpadding="0" width="400">
								     	<tr>
								            <td align="center" width="15%"><?php if ($this->lang->line('admin_common_view') != '') echo stripslashes($this->lang->line('admin_common_view')); else echo 'View'; ?></td>
											
								             <td align="center" width="15%"><?php if ($this->lang->line('admin_subadmin_add') != '') echo stripslashes($this->lang->line('admin_subadmin_add')); else echo 'Add'; ?></td>
											 
								              <td align="center" width="15%"><?php if ($this->lang->line('admin_subadmin_edit') != '') echo stripslashes($this->lang->line('admin_subadmin_edit')); else echo 'Edit'; ?></td>
								              
											  <td align="center" width="15%"><?php if ($this->lang->line('admin_subadmin_delete') != '') echo stripslashes($this->lang->line('admin_subadmin_delete')); else echo 'Delete'; ?></td>
								        </tr>
								    </table>
								</div>
								<input type="hidden" value="<?php echo $admin_details->row()->_id;?>" name="subadminid"/>
								<?php  
								for($i=0;$i<sizeof($adminPrevArr); $i++) {
							  	 $subAdmin = $adminPrevArr[$i]; 
								 $disp_subAdmin = get_language_value_for_keyword($subAdmin,$this->data['langCode']);
							  	 $priv = array();
							  	 if (isset($privArr[$subAdmin])){
								  	 $priv = $privArr[$subAdmin];
							  	 }
							  	 if (!is_array($priv)){
							  	 	$priv = array();
							  	 }
							  	 ?>
								<div class="form_grid_12">
									<label class="field_title"><?php if($this->data['langCode']=="en") echo humanize($disp_subAdmin); else echo $disp_subAdmin; ?></label>
									<table border="0" cellspacing="0" cellpadding="0" width="400">
								     	<tr>
								        	<?php for($j=0;$j<4; $j++) { ?>
								        	<td align="center" width="15%">
								        		<input disabled="disabled" <?php if (in_array($j,$priv)){echo 'checked="checked"';}?> type="checkbox" name="<?php echo $subAdmin.'[]';?>" id="<?php echo $subAdmin.'[]';?>"  value="<?php echo $j;?>" />
								        	</td>
											<?php } ?>
								        </tr>
								    </table>
								</div>
								<?php } ?>
								</li>
								
							</ul>
							
							<ul class="last-btn-submit">
								<li>
								<div class="form_grid_12">
									<div class="form_input">
										<a href="<?php echo ADMIN_ENC_URL;?>/subadmin/display_sub_admin" class="tipLeft" title="<?php if ($this->lang->line('admin_back_to_subadmin') != '') echo stripslashes($this->lang->line('admin_back_to_subadmin')); else echo 'Go to subadmin list'; ?>"><span class="badge_style b_done btn-theme"><?php if ($this->lang->line('admin_common_back') != '') echo stripslashes($this->lang->line('admin_common_back')); else echo 'Back'; ?></span></a>
									</div>
								</div>
								</li>
							</ul>
							
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