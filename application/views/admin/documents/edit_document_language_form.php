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
			<div id="widget_tab">
			</div>
		</div>
		<div class="widget_content">
			<?php
			$attributes = array('class' => 'form_container left_label', 'id' => 'addeditdocument_form','method'=>'POST','autocomplete'=>'off','enctype' => 'multipart/form-data');
			echo form_open(ADMIN_ENC_URL.'/documents/update_language_content', $attributes)
			?>
				<div>
					<ul>
								<li>
                                    <h3><?php if ($this->lang->line('admin_drivers_document_name') != '') echo stripslashes($this->lang->line('admin_drivers_document_name')); else echo 'Document Name'; ?> ( <?php echo $documentdetails->row()->name; ?> )</h3>
                                </li>
					
							<?php 
								$langContents = array();
								if(isset($documentdetails->row()->name_languages)){
									$langContents = $documentdetails->row()->name_languages;
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
				
								<li>
									<div class="form_grid_12">
										<div class="form_input">
											<input type="hidden" name="document_id" id="document_id" value="<?php  echo $documentdetails->row()->_id;  ?>"  />
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
<?php 
$this->load->view(ADMIN_ENC_URL.'/templates/footer.php');
?>