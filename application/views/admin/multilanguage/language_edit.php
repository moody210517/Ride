<?php $this->load->view(ADMIN_ENC_URL.'/templates/header.php'); ?>
<script type="text/javascript">
    function change_the_another_file(fileName) {
        filenameId = $('#language_select').val();
        if (fileName != '' && filenameId != '') {
            window.open("<?php echo ADMIN_ENC_URL;?>/multilanguage/edit_language/" + fileName + "/" + filenameId, "_self");
        }
    }
</script>


  <!--<div id = "google_translate_element"></div>
<script type = "text/javascript">


    function googleTranslateElementInit() {
        new google.translate.TranslateElement({pageLanguage: 'en', layout: google.translate.TranslateElement.InlineLayout.SIMPLE}, 'google_translate_element');
    }

</script> -->
<script type="text/javascript" src="//translate.google.com/translate_a/element.js?cb=googleTranslateElementInit"></script>




<?php /* for($i=1;$i<$get_total_files;$i++) { ?>
  <option value="<?php echo $i; ?>" ><?php echo 'Page'.' '.$i; ?></option>
  <?php } */ ?>

<div id="content">
    <div class="grid_container">
        <div class="grid_12">
            <div class="widget_wrap">
                <div class="widget_top">
                    <span class="h_icon list"></span>
                    <?php
					if ($this->lang->line('admin_multilanguage_page') != '') $page =stripslashes($this->lang->line('admin_multilanguage_page')); else $page = 'Page';
					$siteFilesArr = array('1', '2', '3');
					$driverFilesArr = array('4', '5', '6');
					$webviewFilesArr = array('17');
					for ($i = 1; $i < $get_total_files; $i++) {
						/* if (in_array($current_file_no, $driverFilesArr)) {
							$fileIdentity = '(Driver) '.$page.'';
						}else if(in_array($i, $siteFilesArr)) {
							$fileIdentity = '(Site) '.$page.'';
						}else if(in_array($i, $webviewFilesArr)) {
							$fileIdentity = '(Webview) '.$page.'';
						}else{
							$fileIdentity = '(Admin) '.$page.'';
						} */
						$fileIdentity = $page;
					}
                    ?>
                    <h6><?php if ($this->lang->line('admin_multilanguage_edit_language') != '') echo stripslashes($this->lang->line('admin_multilanguage_edit_language')); else echo 'Edit Language'; ?> - <?php echo $selectedLanguage; ?> - <?php echo $fileIdentity . ' ' . $current_file_no; ?></h6>
                    <?php
					$NewOpArr = '';
					for ($i = 1; $i < $get_total_files; $i++) {
						/* if (in_array($i, $driverFilesArr)) {
							$fileIdentity = '(Driver) '.$page.'';
						}else if(in_array($i, $siteFilesArr)){
							$fileIdentity = '(Site) '.$page.'';
						}else if(in_array($i, $webviewFilesArr)){
							$fileIdentity = '(Webview) '.$page.'';
						}else {
							$fileIdentity = '(Admin) '.$page.'';
						} */
						$fileIdentity = $page;

						$NewOpArr.= '<option value="' . $i . '"';
						if ($current_file_no == $i) {
							$NewOpArr.= 'selected="selected"';
						}
						$NewOpArr.='>' . $fileIdentity . ' ' . $i . '</option>';
					}
                    ?>
                    <div style="float:right">
                        <select name="language_select" id="language_select"   style="float:left;margin: 4px 10px 0px; ">
                            <?php echo $NewOpArr; ?>		
                        </select>
                        <button type="button" class="btn_small btn_blue" style="float:right !important; margin:4px 20px 0 0;padding: 5px 12px;" onclick="javascript:change_the_another_file('<?php echo $selectedLanguage; ?>');"><span><?php if ($this->lang->line('admin_common_submit') != '') echo stripslashes($this->lang->line('admin_common_submit')); else echo 'Submit'; ?></span></button>
                    </div> 
                </div>
                <div class="widget_content chenge-pass-base">
                    <label class="error" style="font-size:18px;"><?php if ($this->lang->line('admin_multilanguage_edit_values_inside') != '') echo stripslashes($this->lang->line('admin_multilanguage_edit_values_inside')); else echo 'Note: Dont Edit The Values Inside Of Curly Braces Eg: {SITENAME}'; ?></label>
                    <p style="font-size:12px;"><?php if ($this->lang->line('admin_multilanguage_join_today') != '') echo stripslashes($this->lang->line('admin_multilanguage_join_today')); else echo 'Eg: Join {SITENAME} today'; ?>  ---  Rejoignez {SITENAME} aujourd'hui</p>
                    <?php
                    $attributes = array('class' => 'form_container left_label', 'id' => 'languageEdit');
                    echo form_open(ADMIN_ENC_URL.'/multilanguage/languageAddEditValues', $attributes)
                    ?>
                    <input type="hidden" value="<?php echo $selectedLanguage; ?>" name="selectedLanguage"/>
                    <input type="hidden" value="<?php echo $file_name_prefix; ?>" name="file_name_prefix"/>
                    <input type="hidden" value="<?php echo $current_file_no; ?>" name="current_file_no"/>


                    <ul class="leftsec-contsec">                            

                        <?php
                        # echo '<pre>'; print_r($file_key_values); echo '<pre>'; print_r($file_lang_values); 
                        $loopNumber = 0;
                        foreach ($file_key_values as $language_keys_item) {
                            if ($loopNumber != '0') {
                                ?>

                                <li>
                                    <div class="form_grid_12">
                                        <label class="field_title lang_lbl"><?php if (isset($file_lang_values[$loopNumber])) echo stripslashes($file_lang_values[$loopNumber]); ?></label>
                                        <div class="form_input">
                                            <input name="language_vals[]" id="language_vals<?php echo $loopNumber; ?>" value="<?php echo (stripslashes($this->lang->line($language_keys_item))); ?>"  type="text"  class="required large tipTop" title="<?php if ($this->lang->line('admin_language_language') != '') echo stripslashes($this->lang->line('admin_language_language')); else echo 'Please enter the language'; ?>"/>
                                            <input name="languageKeys[]" value="<?php echo stripslashes($language_keys_item); ?>" id="smtp_host" type="hidden"  class="required large tipTop"/>                                          
                                        </div>
                                    </div>
                                </li>

                                <?php
                            }
                            $loopNumber = $loopNumber + 1;
                        }
                        ?>

                        <li>
                            <div class="form_grid_12">
                                <div class="form_input bot-submit-wrap">

                                    <button type="submit" class="btn_small btn_blue"><span><?php if ($this->lang->line('admin_common_save') != '') echo stripslashes($this->lang->line('admin_common_save')); else echo 'Save'; ?></span></button>
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
<style>
    .lang_lbl{
        text-transform:none !important;
        font-size:13px !important;
    }

    .form_container .form_grid_12 {
        display: inline-block;
    }
    .left_label ul li label.field_title {
        width: 55%;
        line-height: 20px;
    }
    .left_label ul li .form_input {
        width: 35%; 
        float:left;
        margin-left:0px;
    }
    .form_grid_12 input.large {
        width: 100% !important;
        margin-top: 5px;	
    }


</style>

<?php
$this->load->view(ADMIN_ENC_URL.'/templates/footer.php');
?>
