<?php
$this->load->view(ADMIN_ENC_URL.'/templates/header.php');
?>


<div id="content" class="add-subpage-cms">
    <div class="grid_container">
        <div class="grid_12">
            <div class="widget_wrap">
                <div class="widget_wrap tabby">
                    <div class="widget_top"> <span class="h_icon list"></span>
                        <h6>
                         <?php if ($this->lang->line('admin_common_edit') != '')  $edit=stripslashes($this->lang->line('admin_common_edit')); else  $edit='Edit'; ?>
                        <?php if ($this->lang->line('admin_cms_add_new_main_page') != '') echo stripslashes($this->lang->line('admin_cms_add_new_main_page')); else echo 'Add New Main Page'; ?></h6>
						  <h6 class="lang_rite_edit second_wrap_rite"><span><?php if ($this->lang->line('admin_cms_languages_available') != '') echo stripslashes($this->lang->line('admin_cms_languages_available')); else echo 'Languages Available'; ?>:</span>
                        <?php
                        if ($language_code != '') {
							
                            if (isset($landing_details->row()->$language_code) && !empty($landing_details->row()->$language_code))
                            $lang_details = $landing_details->row()->$language_code;
                            $lang_code = $language_code;
                            $open_square_bracket = "[";
                            $close_square_bracket = "]";
                        } else {
                            $lang_code = '';
                            $open_square_bracket = '';
                            $close_square_bracket = '';
                        }
						echo  '<input name="english" type="checkbox" value="en"  checked disabled readonly><a href='. base_url().ADMIN_ENC_URL.'/cms/add_landing_page_form style="color:white">English</a>';
                        $lang = array();
                        foreach ($langList as $row) {
                            $styling = "style='color:#fff'";
                            $EditText = $edit;
                            if (!empty($language_code)) {
                                if ($language_code == $row->lang_code) {
                                    $styling = "style='color:yellow'";
                                    $EditText = "";
                                }
                            }

                            if ($row->lang_code != 'en') {
                                if (isset($translated_languages) && in_array($row->lang_code, $translated_languages)) {
                                    echo '<input name="' . $row->name . '" type="checkbox" checked value="' . $row->lang_code . '" disabled readonly><span ' . $styling . '>' . $row->name . "</span>" . '<a href="' . base_url() .ADMIN_ENC_URL. '/cms/add_landing_page_form/' . $row->lang_code . '" style="color:red" class="lang_edit_sec">' . $EditText . '</a>';
                                } else {
                                    echo '<input name="' . $row->name . '" type="checkbox" value="' . $row->lang_code . '" class=""><span ' . $styling . '>' . $row->name . "</span>" . '<a href="' . base_url() . ADMIN_ENC_URL.'/cms/add_landing_page_form/' . $row->lang_code . '" style="color:red" class="lang_edit_sec">' . $EditText . '</a>';
                                }
                            }
                        }
                        ?>
						</h6>
                        <div id="widget_tab">
                            <ul>
                                <li><a href="#tab1" class="active_tab"><?php if ($this->lang->line('admin_cms_content') != '') echo stripslashes($this->lang->line('admin_cms_content')); else echo 'Content'; ?></a></li>
                                
                            </ul>
                        </div>
                    </div>
                    <div class="widget_content">
                        <?php
                        $attributes = array('class' => 'form_container left_label', 'id' => 'landing_page_form');
                        echo form_open(ADMIN_ENC_URL.'/cms/add_edit_landing_page_content', $attributes)
                        ?>
                        <div id="tab1">
                            <ul class="inner-subpage">
                                								<li>
                                    <div class="form_grid_12">
                                        <label class="field_title"><?php if ($this->lang->line('admin_cms_css_script') != '') echo stripslashes($this->lang->line('admin_cms_css_script')); else echo 'Css / Script'; ?> <span class="label_intro"><?php if ($this->lang->line('admin_cms_inline_css_and_script') != '') echo stripslashes($this->lang->line('admin_cms_inline_css_and_script')); else echo 'Please write down the content css, inline css and script in this field.'; ?></span></label></label>
                                        <div class="form_input">
                                            <?php
                                            
                                            $css_descrip = "css_descrip";
                                            
											
                                            if (!empty($lang_details)) {

                                                if (isset($lang_details['css_descrip']))
                                                    $css_descrip_val = $lang_details['css_descrip'];
                                                else
                                                    $css_descrip_val = '';
                                            } else {

                                                if (isset($landing_details->row()->css_descrip))
                                                    $css_descrip_val = $landing_details->row()->css_descrip;
                                                else
                                                    $css_descrip_val = '';
                                            }
                                            ?>
                                            <textarea name="<?php echo $css_descrip; ?>" id="css_descrip"  class="large tipTop" title="<?php if ($this->lang->line('admin_pages_enter_css_and_script') != '') echo stripslashes($this->lang->line('admin_pages_enter_css_and_script')); else echo 'Please enter the css and script'; ?>" style="width:70%;" rows="6"><?php echo $css_descrip_val; ?></textarea>
                                        </div>
                                    </div>
                                </li>
                                <li>
                                    <div class="form_grid_12">
                                        <label class="field_title"><?php if ($this->lang->line('admin_cms_description') != '') echo stripslashes($this->lang->line('admin_cms_description')); else echo 'Description'; ?></label>
                                        <div class="form_input">
											<?php 
											$land_content='';
											if($language_code !='en'){
												if(isset($landing_details->row()->$language_code)){  
												$lanArr=$landing_details->row()->$language_code;
												$land_content=stripslashes($lanArr['landing_page_content']);
												}
											}else{
												if(isset($landing_details->row()->landing_page_content)){  $land_content=stripslashes($landing_details->row()->landing_page_content);}
											} ?>
                                            <textarea name="landing_page_content" id="landing_page_content"  class="large required tipTop mceEditor" title="<?php if ($this->lang->line('admin_pages_page_content') != '') echo stripslashes($this->lang->line('admin_pages_page_content')); else echo 'Please enter the page content'; ?>"><?php echo $land_content; ?></textarea>
                                        </div>
                                    </div>
                                </li>
                               
                            </ul>
                            <ul class="last-btn-submit"><li><div class="form_grid_12">
                                        <div class="form_input">
										<input type="hidden" name="lang_code" value="<?php echo $this->uri->segment(4); ?>"/>
                                            <button type="submit" class="btn_small btn_blue" ><span><?php if ($this->lang->line('admin_common_submit') != '') echo stripslashes($this->lang->line('admin_common_submit')); else echo 'Submit'; ?></span></button>
                                        </div>
                                    </div></li></ul>
                        </div>
                       
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <span class="clear"></span> </div>
</div>

<?php
$this->load->view(ADMIN_ENC_URL.'/templates/footer.php');
?>
