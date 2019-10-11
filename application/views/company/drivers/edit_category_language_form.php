<?php
$this->load->view(COMPANY_NAME.'/templates/header.php');
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
			<form class="form_container left_label" action="<?php echo COMPANY_NAME; ?>/drivers/update_language_content" id="addeditcategory_form" method="post" enctype="multipart/form-data">
				<div>
					<ul>
								<li>
                                    <h3><?php if ($this->lang->line('admin_drivers_category_name') != '') echo stripslashes($this->lang->line('admin_drivers_category_name')); else echo 'Category Name'; ?> ( <?php echo $categorydetails->row()->name; ?> ) </h3>
                                </li>
					
							<?php 
								$langContents = array();
								if(isset($categorydetails->row()->name_languages)){
									$langContents = $categorydetails->row()->name_languages;
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
											<input type="hidden" name="category_id" id="category_id" value="<?php  echo $categorydetails->row()->_id;  ?>"  />
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
$this->load->view(COMPANY_NAME.'/templates/footer.php');
?>