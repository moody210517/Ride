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
			$attributes = array('class' => 'form_container left_label', 'id' => 'addeditcategory_form','method'=>'POST','autocomplete'=>'off','enctype' => 'multipart/form-data');
			echo form_open(ADMIN_ENC_URL.'/reviews/update_language_content', $attributes)
			?>
				<div>
					<ul class="leftsec-contsec">
								<li>
                                    <h3 class="head_drive last"> <?php echo $reviewdetails->row()->option_name; ?> </h3>
                                </li>
					
							<?php 
								$langContents = array();
								if(isset($reviewdetails->row()->option_name_languages)){
									$langContents = $reviewdetails->row()->option_name_languages;
								}
								foreach($languagesList->result() as $lang){
							?>
								<li>
									<div class="form_grid_12">
										<label class="field_title"><?php echo $lang->name; ?> </label>
										<div class="form_input">
											<input name="option_name_languages[<?php echo $lang->lang_code; ?>]" id="name" value="<?php if(isset($langContents[$lang->lang_code])) echo $langContents[$lang->lang_code]; ?>" type="text"  class="large tipTop " />
										</div>
									</div>
								</li>
							<?php } ?>
				
								
									
								</ul>
								
								<ul class="admin-pass">
									<li class="change-pass">
									<div class="form_grid_12">
										<div class="form_input">
											<input type="hidden" name="category_id" id="category_id" value="<?php  echo $reviewdetails->row()->_id;  ?>"  />
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