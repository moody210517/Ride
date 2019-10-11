<?php
$this->load->view(ADMIN_ENC_URL.'/templates/header.php');
?>
<div id="content" class="add-subpage-cms">
	<div class="grid_container">
		<div class="grid_12">
			<div class="widget_wrap">
				<div class="widget_top">
					<span class="h_icon list"></span>
					<h6>
					<?php echo $heading; ?>
                    </h6>
                    <?php if ($this->lang->line('admin_common_edit') != '')  $edit=stripslashes($this->lang->line('admin_common_edit')); else  $edit='Edit'; ?>
					<h6 class="lang_rite_edit"><span><?php if ($this->lang->line('admin_templates_languages_available') != '') echo stripslashes($this->lang->line('admin_templates_languages_available')); else echo 'Languages Available'; ?> :</span> 
                        <?php
                        if ($language_code != '') {
                            if (isset($template_details->row()->$language_code) && !empty($template_details->row()->$language_code))
                                $lang_details = $template_details->row()->$language_code;
                            $lang_code = $language_code;
                            $open_square_bracket = "[";
                            $close_square_bracket = "]";
                        } else {
                            $lang_code = '';
                            $open_square_bracket = '';
                            $close_square_bracket = '';
                        }
						echo  '<input name="english" type="checkbox" value="en"  checked disabled readonly><a href="' . base_url() .ADMIN_ENC_URL. '/templates/edit_email_template_form/'.$template_id.'" style="color:white">English</a>';
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
                                    echo '<input name="' . $row->name . '" type="checkbox" checked value="' . $row->lang_code . '" disabled readonly><span ' . $styling . '>' . $row->name . "</span>" . '<a href="' . base_url() .ADMIN_ENC_URL. '/templates/edit_email_template_form/' . $template_id . '/' . $row->lang_code . '" style="color:red" class="lang_edit_sec">' . $EditText . '</a>';
                                } else {
                                    echo '<input name="' . $row->name . '" type="checkbox" value="' . $row->lang_code . '" class=""><span ' . $styling . '>' . $row->name . "</span>" . '<a href="' . base_url() .ADMIN_ENC_URL. '/templates/edit_email_template_form/' . $template_id . '/' . $row->lang_code . '" style="color:red" class="lang_edit_sec">' . $EditText . '</a>';
                                }
                            }
                        }
                        ?>
						</h6 >
				</div>
				<div class="widget_content">
					<?php 
						$attributes = array('class' => 'form_container left_label', 'id' => 'commentForm');
						echo form_open(ADMIN_ENC_URL.'/templates/insertEditEmailtemplate',$attributes) 
					?>
						<ul class="inner-subpage">
							<li>
								<div class="form_grid_12">
									<label class="field_title"><?php if ($this->lang->line('admin_templates_template_name') != '') echo stripslashes($this->lang->line('admin_templates_template_name')); else echo 'Template Name'; ?> <span class="req">*</span></label>
									<div class="form_input">
                            <?php
                                            if (!empty($lang_code)) {
                                                $templatename = $lang_code . $open_square_bracket . "email_title" . $close_square_bracket;
                                            } else {
                                                $templatename = "message[title]";
                                            }
                                            
                                            if (!empty($lang_details)) {

                                                if (isset($lang_details['email_title']))
                                                    $template_name_val = $lang_details['email_title'];
                                                else
                                                    $template_name_val = '';
                                            } else {


                                                if (isset($template_details->row()->message['title']))
                                                    $template_name_val = $template_details->row()->message['title'];
                                                else
                                                    $template_name_val = '';
                                            }
                                            ?>
										<input name="<?php echo $templatename; ?>" style=" width:295px" id="news_title" value="<?php echo $template_name_val; ?>" type="text"  class="required tipTop" title="<?php if ($this->lang->line('admin_newsletter_email_template_name') != '') echo stripslashes($this->lang->line('admin_newsletter_email_template_name')); else echo 'Please enter the email template name'; ?>"/>
									</div>
								</div>
							</li>
							
							<li>
								<div class="form_grid_12">
									<label class="field_title"><?php if ($this->lang->line('admin_templates_email_subject') != '') echo stripslashes($this->lang->line('admin_templates_email_subject')); else echo 'Email Subject'; ?> <span class="req">*</span></label>
									<div class="form_input">
                              <?php
                                            if (!empty($lang_code)) {
                                                $email_subject= $lang_code . $open_square_bracket . "email_subject" . $close_square_bracket;
                                            } else {
                                                $email_subject = "message[subject]";
                                            }
                                            
                                            if (!empty($lang_details)) {

                                                if (isset($lang_details['email_subject']))
                                                    $email_subject_val = $lang_details['email_subject'];
                                                else
                                                    $email_subject_val = '';
                                            } else {


                                                if (isset($template_details->row()->message['subject']))
                                                    $email_subject_val = $template_details->row()->message['subject'];
                                                else
                                                    $email_subject_val = '';
                                            }
                                            ?>
										<input name="<?php echo $email_subject;?>" style=" width:295px" id="news_subject" value="<?php echo $email_subject_val;?>" type="text"  class="required tipTop" title="<?php if ($this->lang->line('admin_newsletter_template_subject') != '') echo stripslashes($this->lang->line('admin_newsletter_template_subject')); else echo 'Please enter the email template subject'; ?>"/>
									</div>
								</div>
							</li>
							<!--
							<li>
								<div class="form_grid_12">
									<label class="field_title">Sender Name <span class="req">*</span></label>
									<div class="form_input">
                            <?php
                                            if (!empty($lang_code)) {
                                                $sender_name= $lang_code . $open_square_bracket . "sender_name" . $close_square_bracket;
                                            } else {
                                                $sender_name = "sender[name]";
                                            }
                                            
                                            if (!empty($lang_details)) {

                                                if (isset($lang_details['sender_name']))
                                                    $sender_name_val = $lang_details['sender_name'];
                                                else
                                                    $sender_name_val = '';
                                            } else {


                                                if (isset($template_details->row()->sender['name']))
                                                    $sender_name_val = $template_details->row()->sender['name'];
                                                else
                                                    $sender_name_val = '';
                                            }
                                            ?>
										<input name="<?php echo $sender_name;?>" style=" width:295px" id="sender_name" type="text"  value="<?php echo $sender_name_val;?>" class="required tipTop" title="Please enter the sender name"/>
									</div>
								</div>
							</li>
							
                            <li>
								<div class="form_grid_12">
									<label class="field_title">Sender Email Address <span class="req">*</span></label>
									<div class="form_input">
                              <?php
                                            if (!empty($lang_code)) {
                                                $sender_email= $lang_code . $open_square_bracket . "sender_email" . $close_square_bracket;
                                            } else {
                                                $sender_email = "sender[email]";
                                            }
                                            
                                            if (!empty($lang_details)) {

                                                if (isset($lang_details['sender_email']))
                                                    $sender_email_val = $lang_details['sender_email'];
                                                else
                                                    $sender_email_val = '';
                                            } else {


                                                if (isset($template_details->row()->sender['email']))
                                                    $sender_email_val = $template_details->row()->sender['email'];
                                                else
                                                    $sender_email_val = '';
                                            }
                                            ?>
										<input name="<?php echo $sender_email; ?>" style=" width:295px" id="sender_email" type="text"  value="<?php echo $sender_email_val; ?>" class="required tipTop" title="Please enter the sender email address"/>
									</div>
								</div>
							</li> -->
							
                            <li>
								<div class="form_grid_12">
									<label class="field_title"><?php if ($this->lang->line('admin_templates_email_description') != '') echo stripslashes($this->lang->line('admin_templates_email_description')); else echo 'Email Description'; ?> </label>
									<div class="form_input">
                             <?php
                                            if (!empty($lang_code)) {
                                                $email_description= $lang_code . $open_square_bracket . "email_description" . $close_square_bracket;
                                            } else {
                                                $email_description = "message[description]";
                                            }
                                            
                                            if (!empty($lang_details)) {

                                                if (isset($lang_details['email_description']))
                                                    $email_description_val = $lang_details['email_description'];
                                                else
                                                    $email_description_val = '';
                                            } else {


                                                if (isset($template_details->row()->message['description']))
                                                    $email_description_val = $template_details->row()->message['description'];
                                                else
                                                    $email_description_val = '';
                                            }
                                            ?>
										<textarea name="<?php echo $email_description;?>" style=" width:295px;" class="tipTop mceEditor" title="<?php if ($this->lang->line('admin_newsletter_template_description') != '') echo stripslashes($this->lang->line('admin_newsletter_template_description')); else echo 'Please enter the email template description'; ?>"><?php echo $email_description_val;?></textarea>
									</div>
								</div>
							</li>
							
							<input type="hidden" name="_id" value="<?php echo $template_details->row()->_id;?>"/>
                     <input type="hidden" name="lang_code" value="<?php echo $language_code; ?>"/>
							<li>
								<div class="form_grid_12">
									<div class="form_input">
										<button type="submit" class="btn_small btn_blue" ><span><?php if ($this->lang->line('admin_common_update') != '') echo stripslashes($this->lang->line('admin_common_update')); else echo 'Update'; ?></span></button>
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