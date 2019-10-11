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
						$attributes = array('class' => 'form_container left_label');
						echo form_open(ADMIN_ENC_URL.'/currency/display_currency_list',$attributes) 
					?> 		
	 						<ul>								
                                <li>
									<div class="form_grid_12">
										<label class="field_title"><?php if ($this->lang->line('admin_review_name') != '') echo stripslashes($this->lang->line('admin_review_name')); else echo 'Name'; ?></label>
										<div class="form_input">
										<?php if(isset($currency_details->row()->name)){ echo $currency_details->row()->name; } ?>
										</div>
									</div>
								</li>							
                                <li>
									<div class="form_grid_12">
										<label class="field_title"><?php if ($this->lang->line('admin_review_code') != '') echo stripslashes($this->lang->line('admin_review_code')); else echo 'Code'; ?> </label>
										<div class="form_input">
										<?php if(isset($currency_details->row()->code)){ echo $currency_details->row()->code; } ?>
										</div>
									</div>
								</li>							
                                <li>
									<div class="form_grid_12">
										<label class="field_title"><?php if ($this->lang->line('admin_review_symbol') != '') echo stripslashes($this->lang->line('admin_review_symbol')); else echo 'Symbol'; ?></label>
										<div class="form_input">
										<?php if(isset($currency_details->row()->symbol)){ echo $currency_details->row()->symbol; } ?>
										</div>
									</div>
								</li>
								
								
								<li>
									<div class="form_grid_12">
										<label class="field_title"><?php if ($this->lang->line('admin_common_status') != '') echo stripslashes($this->lang->line('admin_common_status')); else echo 'Status'; ?></label>
										<div class="form_input">
											<div class="active_inactive">
												<?php if(isset($currency_details->row()->status)){ echo $currency_details->row()->status; } ?>
											</div>
										</div>
									</div>
								</li>
								<li>
									<div class="form_grid_12">
										<div class="form_input">
											<a class="tipLeft" href="<?php echo ADMIN_ENC_URL;?>/currency/display_currency_list" original-title="<?php if ($this->lang->line('admin_review_go_to_currency_list') != '') echo stripslashes($this->lang->line('admin_review_go_to_currency_list')); else echo 'Go to Currency list'; ?>">
												<span class="badge_style b_done"><?php if ($this->lang->line('admin_common_back') != '') echo stripslashes($this->lang->line('admin_common_back')); else echo 'Back'; ?></span>
											</a>
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