<?php
$this->load->view(ADMIN_ENC_URL.'/templates/header.php');
extract($privileges);
?>
<script type="text/javascript">
    $(document).ready(function () {
        $("#language_select").change(function () {
            filenameId = $('#language_select').val();
            if (filenameId != '') {
                window.open("<?php echo ADMIN_ENC_URL;?>/multilanguage/datetime_edit_language/" + filenameId, "_self");
            }
        });
    });
</script>

<?php /* ?>
 <div id = "google_translate_element"></div>
<script type = "text/javascript">
    function googleTranslateElementInit() {
        new google.translate.TranslateElement({pageLanguage: 'pl', layout: google.translate.TranslateElement.InlineLayout.SIMPLE}, 'google_translate_element');
    }
</script> 
<script type="text/javascript" src="//translate.google.com/translate_a/element.js?cb=googleTranslateElementInit"></script>
<?php */ ?>
<div id="content" class="mob-edit-languages">
    <div class="grid_container">
        <div class="grid_12">
            <div class="widget_wrap">
                <div class="widget_top">
                    <span class="h_icon blocks_images"></span>
                    <h6><?php echo $heading; ?></h6>
                    <div style="float: right;line-height:40px;padding:0px 10px;height:39px;">
                        <select name="language_select" id="language_select"   style="float:left;margin: 9px 10px 0px; ">
							<?php  if($selectedLang==""){ ?>
							 <?php } ?>
                            <?php if ($language_list->num_rows() > 0) { ?>
							<option value=""> <?php if ($this->lang->line('admin_multilanguage_select_any_languages') != '') echo stripslashes($this->lang->line('admin_multilanguage_select_any_languages')); else echo 'Select any languages'; ?></option>
                                <?php
                                foreach ($language_list->result() as $row) {
									if($row->lang_code!="en"){
                                    if ($row->lang_code == $selectedLang)
                                        $selected = "selected";
                                    else
                                        $selected = "";
                                    ?>
                                    <option <?php echo $selected; ?> value="<?php echo $row->lang_code; ?>"><?php echo $row->name; ?></option>
                                <?php } ?>
                            <?php }} else {
                                ?>
                                <option value=""><?php if ($this->lang->line('admin_multilanguage_no_languages_found') != '') echo stripslashes($this->lang->line('admin_multilanguage_no_languages_found')); else echo 'No Languages Found!'; ?></option>
                                <?php
                            }
                            ?>	
                        </select>

                    </div>


                </div>
                <div class="widget_content">
                    <?php
                    $attributes = array('class' => 'form_container left_label', 'id' => 'languageEdit');
                    echo form_open(ADMIN_ENC_URL.'/multilanguage/add_edit_datetime_language', $attributes)
                    ?>
                    <ul class="mob-lang-inner">
                        <?php
						if($selectedLang!=""){
							foreach ($language_key_values as $key => $value) {
						?>
							<li>
								<div class="form_grid_12">
									<label class="field_title lang_lbl"><?php if (isset($value)) echo stripslashes($value); ?></label>
									<div class="form_input">
										<?php
										$selKeyVal = '';
										if (!empty($language_list_sel)) {
											if (isset($language_list_sel[$key])) {
												$selKeyVal = $language_list_sel[$key];
											}
										} else {
											$selKeyVal =  (stripslashes($value));
										}
										?>
										<input name="language_vals[<?php echo $key; ?>]" id="language_vals<?php echo $key; ?>" value="<?php echo $selKeyVal; ?>"  type="text"  class="required large tipTop" title="<?php if ($this->lang->line('admin_language_language') != '') echo stripslashes($this->lang->line('admin_language_language')); else echo 'Please enter the language'; ?>"/>
										<input name="languageKeys[]" value="<?php echo stripslashes($key); ?>" id="smtp_host" type="hidden"  class="required large tipTop"/>                                          
									</div>
								</div>
							</li>

						<?php
							}
						}
						?>
						<?php
						if($selectedLang==""){
						?>
						<h2 class="sel-lang-first"><?php if ($this->lang->line('admin_language_choose_any') != '') echo stripslashes($this->lang->line('admin_language_choose_any')); else echo 'Please choose any language'; ?></h2>
						<?php
						}
                        ?>

						<?php  if($selectedLang!=""){ ?>
                        <li>
                            <div class="form_grid_12">
                                <div class="form_input">
                                    <input type="hidden" name="selectedLang" value="<?php echo!empty($selectedLang) ? $selectedLang : 'en'; ?>">
									<input type="hidden" value="validation" name="type" />
                                    <button type="submit" class="btn_small btn_blue"><span><?php if ($this->lang->line('admin_common_save') != '') echo stripslashes($this->lang->line('admin_common_save')); else echo 'Save'; ?></span></button>
                                </div>
                            </div>
                        </li>
						<?php } ?>
                    </ul>
                    </form>
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
<style>
.form_container ul li label.field_title{
	text-transform: none !important;
}
.field_title lang_lbl{
	
}
</style>

<?php
$this->load->view(ADMIN_ENC_URL.'/templates/footer.php');
?>