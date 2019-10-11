<?php
$this->load->view(ADMIN_ENC_URL.'/templates/header.php');
?>
<div id="content" class="base-app-top">
    <div class="grid_container">
        <div class="grid_12">
            <div class="widget_wrap">
                <div class="widget_top">
                    <span class="h_icon list"></span>
                    <h6><?php echo $heading; ?></h6>

                </div>
                <div class="widget_content">
                    <?php
                    $attributes = array('class' => 'form_container left_label', 'id' => 'addlg_form');
                    echo form_open_multipart(ADMIN_ENC_URL.'/multilanguage/add_lg_process', $attributes)
                    ?>
					
					<div class="base-appsec">

                    <ul class="leftsec-contsec">

                        <li>
                            <div class="form_grid_12">
                                <label class="field_title"><?php if ($this->lang->line('admin_multilanguage_language_name') != '') echo stripslashes($this->lang->line('admin_multilanguage_language_name')); else echo 'Language Name'; ?> <span class="req">*</span></label>
                                <div class="form_input">
                                    <input name="name" id="name" type="text"  class="required large tipTop" title="<?php if ($this->lang->line('admin_language_language_name') != '') echo stripslashes($this->lang->line('admin_language_language_name')); else echo 'Please enter the language name'; ?>"/>
                                </div>
                            </div>
                        </li>

                        <li>
                            <div class="form_grid_12">
                                <label class="field_title"><?php if ($this->lang->line('admin_multilanguage_language_code') != '') echo stripslashes($this->lang->line('admin_multilanguage_language_code')); else echo 'Language Code'; ?> <span class="req">*</span></label>
                                <div class="form_input">
                                    <input name="lang_code" id="lang_code" type="text"  class="required large tipTop alphanumeric" title="<?php if ($this->lang->line('admin_language_language_code') != '') echo stripslashes($this->lang->line('admin_language_language_code')); else echo 'Please enter the language code'; ?>"/>
                                </div>
                            </div>
                        </li>


                        <li>
                            <input type="hidden" name="status" value="Inactive"/>
                            <div class="form_grid_12">
                                <div class="form_input">
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
<script type="text/javascript">
    $('#lang_code').keyup(function(){
        str = $(this).val()
        str = str.replace(/[^a-z]/g,'')
        $(this).val(str)
    });
    
    $('#addlg_form').validate();
</script>
<?php
$this->load->view(ADMIN_ENC_URL.'/templates/footer.php');
?>