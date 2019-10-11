<?php
$this->load->view(ADMIN_ENC_URL.'/templates/header.php');
?>
<div id="content">
	<div class="grid_container">
		<div class="grid_12">
			<div class="widget_wrap">
				<div class="widget_top">
					<span class="h_icon list"></span>
					<h6><?php if ($this->lang->line('admin_templates_sms_templates') != '') echo stripslashes($this->lang->line('admin_templates_sms_templates')); else echo 'SMS Templates'; ?></h6>
                    <?php if($news_id != '') { ?>
                    <?php if ($this->lang->line('admin_common_edit') != '')  $edit=stripslashes($this->lang->line('admin_common_edit')); else  $edit='Edit'; ?>
                    <h6><?php if ($this->lang->line('admin_templates_languages_available') != '') echo stripslashes($this->lang->line('admin_templates_languages_available')); else echo 'Languages Available'; ?> : 
                        <?php
                        if(isset($template_info->row()->translated_languages)){
                            $translated_languages = $template_info->row()->translated_languages;
                        }
                        if ($language_code != '') {
                            if (isset($template_info->row()->$language_code) && !empty($template_info->row()->$language_code))
                            $lang_details = $template_info->row()->$language_code;
                            $lang_code = $language_code;
                            $open_square_bracket = "[";
                            $close_square_bracket = "]";
                        } else {
                            $lang_code = '';
                            $open_square_bracket = '';
                            $close_square_bracket = '';
                        }
						echo  '<input name="english" type="checkbox" value="en"  checked disabled readonly><a href="' . base_url() .ADMIN_ENC_URL. '/templates/add_edit_sms_templates_form/'.$news_id.'" style="color:white">English</a>';
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
                                    echo '<input name="' . $row->name . '" type="checkbox" checked value="' . $row->lang_code . '" disabled readonly><span ' . $styling . '>' . $row->name . "</span>" . '<a href="' . base_url() .ADMIN_ENC_URL.'/templates/add_edit_sms_templates_form/' . $news_id . '/' . $row->lang_code . '" style="color:red">' . $EditText . '</a>';
                                } else {
                                    echo '<input name="' . $row->name . '" type="checkbox" value="' . $row->lang_code . '" class=""><span ' . $styling . '>' . $row->name . "</span>" . '<a href="' . base_url() .ADMIN_ENC_URL. '/templates/add_edit_sms_templates_form/' . $news_id . '/' . $row->lang_code . '" style="color:red">' . $EditText . '</a>';
                                }
                            }
                        }
                        ?>
						</h6 >
                       <?php } ?>
				</div>
				<div class="widget_content">
					<?php 
						$attributes = array('class' => 'form_container left_label', 'id' => 'smsForm');
						echo form_open(ADMIN_ENC_URL.'/templates/insertEditSMStemplate',$attributes) 
					?>
	 					<ul>							
                            <li>
								<div class="form_grid_12">
									<label class="field_title"><?php if ($this->lang->line('admin_templates_template_name') != '') echo stripslashes($this->lang->line('admin_templates_template_name')); else echo 'Template Name'; ?>
									<span class="req">*</span></label>
									<div class="form_input">
                                        <?php
                                            if (!empty($lang_code)) {
                                                $name_template_name= $lang_code . $open_square_bracket . "template_name" . $close_square_bracket;
                                            } else {
                                                $name_template_name = "template_name";
                                            }
                                            
                                            if (!empty($lang_details)) {
                                                if (isset($lang_details['template_name']))
                                                    $template_name_val = $lang_details['template_name'];
                                                else
                                                    $template_name_val = '';
                                            } else {
                                                if (isset($template_info->row()->template_name))
                                                    $template_name_val = $template_info->row()->template_name;
                                                else
                                                    $template_name_val = '';
                                            }
                                        ?>
										<input type="text" name="<?php echo $name_template_name; ?>" id="template_name" class="required tipTop" title="Template Name"  value="<?php if($form_mode) echo $template_name_val; ?>"/>
									</div>
								</div>
							</li> 
							
							<li>
								<div class="form_grid_12">
									<label class="field_title"><?php if ($this->lang->line('admin_templates_template_content') != '') echo stripslashes($this->lang->line('admin_templates_template_content')); else echo 'Template Content'; ?><span class="req">*</span></label>
									<div class="form_input">
										<p style="color:red; margin-left: -12px !important; padding: 0 !important; float: left; font-size: 14px;"><b><?php if ($this->lang->line('admin_sms_template_note_title') != '') echo stripslashes($this->lang->line('admin_sms_template_note_title')); else echo 'NOTE'; ?> :</b> <?php if ($this->lang->line('admin_sms_template_note') != '') echo stripslashes($this->lang->line('admin_sms_template_note')); else echo 'Please don\'t edit the texts inside the curly braces, also don\'t remove the curly braces. Eg:{$user_name}'; ?></p>
                                        <?php
                                            if (!empty($lang_code)) {
                                                $name_description= $lang_code . $open_square_bracket . "description" . $close_square_bracket;
                                            } else {
                                                $name_description = "description";
                                            }
                                            
                                            if (!empty($lang_details)) {
                                                if (isset($lang_details['description']))
                                                    $description_val = $lang_details['description'];
                                                else
                                                    $description_val = '';
                                            } else {
                                                if (isset($template_info->row()->description))
                                                    $description_val = $template_info->row()->description;
                                                else
                                                    $description_val = '';
                                            }
                                        ?>
										<textarea name="<?php echo $name_description; ?>" id="description" class="required tipTop" title="Template Name"  ><?php if($form_mode) echo $description_val; ?></textarea>
									</div>
								</div>
							</li>
                          
							<input type="hidden" name="news_id" value="<?php if($form_mode) echo $template_info->row()->news_id; ?>">
                            <input type="hidden" name="lang_code" value="<?php if($this->uri->segment(5) != '') echo $this->uri->segment(5); ?>">
							<li>
								<div class="form_grid_12">
									<div class="form_input">
										<button type="submit" class="btn_small btn_blue" ><span><?php if ($this->lang->line('admin_common_submit') != '') echo stripslashes($this->lang->line('admin_common_submit')); else echo 'Submit'; ?></span></button>
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