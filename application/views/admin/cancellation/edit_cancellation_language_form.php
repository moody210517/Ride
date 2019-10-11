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
			$attributes = array('class' => 'form_container left_label', 'id' => 'addeditcategory_form','method'=>'POST','enctype' => 'multipart/form-data');
			echo form_open(ADMIN_ENC_URL.'/cancellation/update_language_content', $attributes)
			?>
				<div>
					<ul class="leftsec-contsec">
								<li>
                                    <h3 class="head_drive user_cancel_txt">
									<?php
									if($reason_for == 'user'){
										if ($this->lang->line('admin_menu_user_cancellation_reason') != '') echo stripslashes($this->lang->line('admin_menu_user_cancellation_reason')); else echo 'User Cancellation Reason';
									}else if($reason_for == 'driver'){
										if ($this->lang->line('admin_menu_driver_cancellation_reason') != '') echo stripslashes($this->lang->line('admin_menu_driver_cancellation_reason')); else echo 'Driver Cancellation Reason';
									}?>
									 ( <?php echo $cancellationDetails->row()->reason; ?> ) 
									</h3>
                                </li>
					
							<?php 
								$langContents = array();
								if(isset($cancellationDetails->row()->name_languages)){
									$langContents = $cancellationDetails->row()->name_languages;
								}
								foreach($languagesList->result() as $lang){
							?>
								<li>
									<div class="form_grid_12">
										<label class="field_title"><?php echo $lang->name; ?> </label>
										<div class="form_input">
											<input name="name_languages[<?php echo $lang->lang_code; ?>]" id="name" value="<?php if(isset($langContents[$lang->lang_code])) echo $langContents[$lang->lang_code]; ?>" type="text"  class="large tipTop " />
										</div>
									</div>
								</li>
							<?php } ?>
				
								
									
								</ul>
								
								<ul class="admin-pass">
									<li class="change-pass">
									<div class="form_grid_12">
										<div class="form_input">
											<input type="hidden" name="cancellation_id" id="cancellation_id" value="<?php  echo $cancellationDetails->row()->_id;  ?>"  />
											<button type="submit" class="btn_small btn_blue" ><span><?php if ($this->lang->line('admin_common_submit') != '') echo stripslashes($this->lang->line('admin_common_submit')); else echo 'Submit'; ?></span></button>
										</div>
									</div>
								</li>
								</ul>
								
						   </div>
						<input type="hidden" name="reason_for" value="<?php echo $reason_for;?>">
					   
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