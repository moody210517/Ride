<?php
$this->load->view(ADMIN_ENC_URL.'/templates/header.php');
?>

	<div id="content" class="admin-settings">
		<div class="grid_container">
			<div class="grid_12">
				<div class="widget_wrap">
					<div class="widget_top">
						<span class="h_icon list"></span>
						<h6><?php echo $heading; ?></h6>
                        <div id="widget_tab">
            			</div>
					</div>
					<div class="widget_content chenge-pass-base">
						<?php
						$attributes = array('class' => 'form_container left_label form_stylee', 'id' => 'operators_change_password','method'=>'POST','autocomplete'=>'off','enctype' => 'multipart/form-data');
						echo form_open(ADMIN_ENC_URL.'/company/change_password', $attributes)
						?>
							<ul class="leftsec-contsec">
								<li>
									<div class="form_grid_12">
										<label class="field_title" id="lpassword"><?php if ($this->lang->line('admin_partner_password') != '') echo stripslashes($this->lang->line('admin_partner_password')); else echo 'Password'; ?><span class="req">*</span></label>
										<div class="form_input">
											<input id="password" name="password" type="password" maxlength="50" value="" class="large required"/>
										</div>
									</div>
								</li>
								<li>
									<div class="form_grid_12">
										<label class="field_title" id="lpassword_confirm"><?php if ($this->lang->line('admin_partner_confirm_password') != '') echo stripslashes($this->lang->line('admin_partner_confirm_password')); else echo 'confirm Password'; ?> <span class="req">*</span></label>
										<div class="form_input">
											<input id="confirm_password" name="password_confirm" type="password" maxlength="50" value="" class="large required" equalto="#password"/>
										</div>
									</div>
								</li>
								
								</ul>
							<ul class="admin-pass">
							<li class="change-pass">
								<div class="form_grid_12">
									<div class="form_input">
										<input type="hidden" name="operators_id" id="operators_id" value="<?php echo $operatorsdetails->row()->_id; ?>"  />
										<button type="submit" class="btn_small btn_blue" ><span><?php if ($this->lang->line('admin_partner_submit') != '') echo stripslashes($this->lang->line('admin_partner_submit')); else echo 'Submit'; ?></span></button>
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