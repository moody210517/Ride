<?php
#echo "<pre>";print_r($language_key_values);die;
$this->load->view(ADMIN_ENC_URL.'/templates/header.php');
extract($privileges);
?>
<script type="text/javascript">
    $(document).ready(function () {
        $("#language_select").change(function () {
            filenameId = $('#language_select').val();

            if (filenameId != '') {
                window.open("<?php echo ADMIN_ENC_URL;?>/multilanguage/mobile_edit_language/" + filenameId, "_self");
            }
        });
    });


</script>

<?php /* <div id = "google_translate_element"></div>
<script type = "text/javascript">
    function googleTranslateElementInit() {
        new google.translate.TranslateElement({pageLanguage: 'pl', layout: google.translate.TranslateElement.InlineLayout.SIMPLE}, 'google_translate_element');
    }
</script> */ ?>
<script type="text/javascript" src="//translate.google.com/translate_a/element.js?cb=googleTranslateElementInit"></script>
<div id="content" class="mob-edit-languages">
    <div class="grid_container">
        <div class="grid_12">
            <div class="widget_wrap">
                <div class="widget_top">
                    <span class="h_icon blocks_images"></span>
                    <h6><?php echo $heading; ?></h6>
                    <div style="float: right;line-height:40px;padding:0px 10px;height:39px;">
                        <select name="language_select" id="language_select"   style="float:left;margin: 9px 10px 0px; ">
                            <?php if ($language_list->num_rows() > 0) { ?>
							<option value=""> <?php if ($this->lang->line('admin_multilanguage_select_any_languages') != '') echo stripslashes($this->lang->line('admin_multilanguage_select_any_languages')); else echo 'Select any languages'; ?></option>
                                <?php
                                foreach ($language_list->result() as $row) {
                                    if ($row->lang_code == $selectedLang)
                                        $selected = "selected";
                                    else
                                        $selected = "";
                                    ?>
                                    <option <?php echo $selected; ?> value="<?php echo $row->lang_code; ?>"><?php echo $row->name; ?></option>
                                <?php } ?>
                            <?php } else {
                                ?>
                                <option value=""><?php if ($this->lang->line('admin_multilanguage_no_languages_found') != '') echo stripslashes($this->lang->line('admin_multilanguage_no_languages_found')); else echo 'No Languages Found!'; ?></option>
                                <?php
                            }
                            ?>	
                        </select>

                    </div>


                </div>
                <div class="widget_content">
                    <label class="error" style="font-size:18px;"><?php if ($this->lang->line('admin_multilanguage_edit_values_inside') != '') echo stripslashes($this->lang->line('admin_multilanguage_edit_values_inside')); else echo 'Note: Dont Edit The Values Inside Of Curly Braces Eg: {SITENAME}'; ?></label>
                    <p style="font-size:12px;">Eg: Join {SITENAME} today  ---  Rejoignez {SITENAME} aujourd'hui</p>
                    <?php
                    $attributes = array('class' => 'form_container left_label', 'id' => 'languageEdit');
                    echo form_open(ADMIN_ENC_URL.'/multilanguage/add_edit_mobile_language', $attributes)
                    ?>
                    <ul class="mob-lang-inner">                            

                        <?php
						if($selectedLang!=""){
                        if(!empty($language_key_values)) {
							foreach ($language_key_values as $key => $value) {
						?>

                            <li>
                                <div class="form_grid_12">
                                    <label class="field_title lang_lbl"><?php if (isset($value)) echo stripslashes($value); ?></label>
                                    <div class="form_input">
                                        <input name="language_vals[]" id="language_vals<?php echo $key; ?>" value="<?php
                                        if (!empty($language_list_db) && $language_list_db->num_rows() > 0) {
                                            if (isset($language_list_db->row()->key_values[$key])) {
                                                echo $language_list_db->row()->key_values[$key];
                                            } else {
                                                echo "";
                                            }
                                        } else {
                                            echo (stripslashes($value));
                                        }
                                        ?>"  type="text"  class="required large tipTop" title="<?php if ($this->lang->line('admin_language_language') != '') echo stripslashes($this->lang->line('admin_language_language')); else echo 'Please enter the language'; ?>"/>
                                        <input name="languageKeys[]" value="<?php echo stripslashes($key); ?>" id="smtp_host" type="hidden"  class="required large tipTop" />                                          
                                    </div>
                                </div>
                            </li>

                            <?php
							}
						}
                        ?>

						<?php  if($selectedLang!=""){ ?>
                        <li>
                            <div class="form_grid_12">
                                <div class="form_input">
                                    <input type="hidden" name="selectedLang" value="<?php echo!empty($selectedLang) ? $selectedLang : 'en'; ?>">
                                    <button type="submit" class="btn_small btn_blue"><span><?php if ($this->lang->line('admin_common_save') != '') echo stripslashes($this->lang->line('admin_common_save')); else echo 'Save'; ?></span></button>
                                </div>
                            </div>
                        </li>
						<?php } ?>
						<?php } ?>
						
						<?php if($selectedLang==""){ ?>
						<h2 class="sel-lang-first"><?php if ($this->lang->line('admin_language_choose_any') != '') echo stripslashes($this->lang->line('admin_language_choose_any')); else echo 'Please choose any language'; ?></h2>
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
    #county_chzn{
        margin-top: 5px !important;
    }
	.lang_lbl{
		text-transform: none !important;
	}

    #county_chzn,.chzn-drop,.chzn-search,.chzn-results{
        width: 200px !important;

    }
</style>

<?php
$this->load->view(ADMIN_ENC_URL.'/templates/footer.php');
?>