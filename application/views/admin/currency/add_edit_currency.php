<?php
$this->load->view(ADMIN_ENC_URL.'/templates/header.php');
?>
<div id="content">
		<div class="grid_container">
			<div class="grid_12">
				<div class="widget_wrap">
					<div class="widget_top">
						<span class="h_icon list"></span>
						<h6><?php echo $heading; ?></h6>
					</div>
					<div class="widget_content">
					<?php 
						$attributes = array('class' => 'form_container left_label', 'id' => 'addeditcurrency_form');
						echo form_open(ADMIN_ENC_URL.'/currency/insertEditCurrency',$attributes) 
					?> 		
	 						<ul>								
                                <li>
									<div class="form_grid_12">
										<label class="field_title"><?php if ($this->lang->line('admin_currency_name') != '') echo stripslashes($this->lang->line('admin_currency_name')); else echo 'Name'; ?> <span class="req">*</span></label>
										<div class="form_input">
										<input name="name" id="name" type="text"  class="large required tipTop" title="Please enter the name" value="<?php if($form_mode){ echo $currencydetails->row()->name; } ?>"/>
										</div>
									</div>
								</li>							
                                <li>
									<div class="form_grid_12">
										<label class="field_title"><?php if ($this->lang->line('admin_currency_code') != '') echo stripslashes($this->lang->line('admin_currency_code')); else echo 'Code'; ?> <span class="req">*</span></label>
										<div class="form_input">
										<input name="code" id="code" type="text"  class="large required tipTop" title="Please enter the Code" value="<?php if($form_mode){ echo $currencydetails->row()->code; } ?>"/>
										</div>
									</div>
								</li>							
                                <li>
									<div class="form_grid_12">
										<label class="field_title"><?php if ($this->lang->line('admin_currency_symbol') != '') echo stripslashes($this->lang->line('admin_currency_symbol')); else echo 'Symbol'; ?> <span class="req">*</span></label>
										<div class="form_input">
										<input name="symbol" id="symbol" type="text"  class="large required tipTop" title="Please enter symbol" value="<?php if($form_mode){ echo $currencydetails->row()->symbol; } ?>"/>
										</div>
									</div>
								</li>
								
								
								<li>
									<div class="form_grid_12">
										<label class="field_title"><?php if ($this->lang->line('admin_common_status') != '') echo stripslashes($this->lang->line('admin_common_status')); else echo 'Status'; ?> </label>
										<div class="form_input">
											<div class="active_inactive">
												<input type="checkbox"  name="status" id="active_inactive_active" class="active_inactive" <?php if($form_mode){ if ($currencydetails->row()->status == 'Active'){echo 'checked="checked"';} }else{echo 'checked="checked"';} ?>/>
											</div>
										</div>
									</div>
								</li>
								<input type="hidden" name="currency_id" value="<?php if($form_mode){ echo $currencydetails->row()->_id; } ?>"/>
								<li>
									<div class="form_grid_12">
										<div class="form_input">
											<button type="submit" class="btn_small btn_blue" ><span><?php if ($this->lang->line('admin_common_submit') != '') echo stripslashes($this->lang->line('admin_common_submit')); else echo 'Submit'; ?></span></button>
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
<?php 
$this->load->view(ADMIN_ENC_URL.'/templates/footer.php');
?>