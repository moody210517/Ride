<?php
$this->load->view(ADMIN_ENC_URL.'/templates/header.php');
?>
<div id="content" class="add-subpage-cms">
    <div class="grid_container">
        <div class="grid_12">
            <div class="widget_wrap">
                <div class="widget_top">
                    <span class="h_icon list"></span>
                    <h6><?php echo $heading; ?></h6>
                    
                </div>
                <div class="widget_content">
                    <?php
                    $attributes = array('class' => 'form_container left_label', 'id' => 'commentForm');
                    echo form_open(ADMIN_ENC_URL.'/templates/insertEditEmailtemplate', $attributes)
                    ?>
                    <ul class="inner-subpage">

                        <li>
                            <div class="form_grid_12">
                                <label class="field_title"><?php if ($this->lang->line('admin_templates_template_name') != '') echo stripslashes($this->lang->line('admin_templates_template_name')); else echo 'Template Name'; ?> <span class="req">*</span></label>
                                <div class="form_input">
                                    <input name="message[title]" style=" width:295px" id="news_title" value="" type="text"  class="required tipTop" title="<?php if ($this->lang->line('admin_newsletter_template_name') != '') echo stripslashes($this->lang->line('admin_newsletter_template_name')); else echo 'Please enter the email template name'; ?>"/>
                                </div> 
                            </div>
                        </li>

                        <li>
                            <div class="form_grid_12">
                                <label class="field_title"><?php if ($this->lang->line('admin_templates_email_subject') != '') echo stripslashes($this->lang->line('admin_templates_email_subject')); else echo 'Email Subject'; ?> <span class="req">*</span></label>
                                <div class="form_input">
                                    <input name="message[subject]" style=" width:295px" id="news_subject" type="text"  class="required tipTop" title="<?php if ($this->lang->line('admin_newsletter_template_subject') != '') echo stripslashes($this->lang->line('admin_newsletter_template_subject')); else echo 'Please enter the email template subject'; ?>"/>
                                </div>
                            </div>
                        </li>
						<!--
                        <li>
                            <div class="form_grid_12">
                                <label class="field_title">Sender Name <span class="req">*</span></label>
                                <div class="form_input">
                                    <input name="sender[name]" style=" width:295px" id="sender_name" type="text"  value="<?php echo $this->data['title']; ?>" class="required tipTop" title="Please enter the sender name"/>
                                </div>
                            </div>
                        </li>

                        <li>
                            <div class="form_grid_12">
                                <label class="field_title">Sender Email Address <span class="req">*</span></label>
                                <div class="form_input">
                                    <input name="sender[email]" style=" width:295px" id="sender_email" type="text"  value="<?php echo $this->config->item('email'); ?>" class="required tipTop" title="Please enter the sender email address"/>
                                </div>
                            </div>
                        </li> -->

                        <li>
                            <div class="form_grid_12">
                                <label class="field_title"><?php if ($this->lang->line('admin_templates_email_description') != '') echo stripslashes($this->lang->line('admin_templates_email_description')); else echo 'Email Description'; ?> </label>
                                <div class="form_input">
                                    <textarea name="message[description]" style=" width:295px" class="tipTop mceEditor" title="<?php if ($this->lang->line('admin_newsletter_template_description') != '') echo stripslashes($this->lang->line('admin_newsletter_template_description')); else echo 'Please enter the email template description'; ?>" ></textarea>
                                </div>
                            </div>
                        </li>

                        <input type="hidden" name="status" id="status" />
                        <input type="hidden" name="_id" value=""/>

                        

                    </ul>
					
					<ul class="last-btn-submit">
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