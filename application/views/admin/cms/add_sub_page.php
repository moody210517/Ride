<?php
$this->load->view(ADMIN_ENC_URL.'/templates/header.php');
?>
<div id="content" class="add-subpage-cms">
    <div class="grid_container">
        <div class="grid_12">
            <div class="widget_wrap">
                <div class="widget_wrap tabby">
                    <div class="widget_top"> <span class="h_icon list"></span>
                        <h6><?php echo $heading; ?></h6>
                        <div id="widget_tab">
                            <ul class="subpage-ul">
                                <li><a href="#tab1" class="active_tab"><?php if ($this->lang->line('admin_cms_content') != '') echo stripslashes($this->lang->line('admin_cms_content')); else echo 'Content'; ?></a></li>
                                <li><a href="#tab2"><?php if ($this->lang->line('admin_cms_banner_details') != '') echo stripslashes($this->lang->line('admin_cms_banner_details')); else echo 'Banner Details'; ?></a></li>
								<li><a href="#tab3"><?php if ($this->lang->line('admin_cms_seo_details') != '') echo stripslashes($this->lang->line('admin_cms_seo_details')); else echo 'SEO Details'; ?></a></li>
                            </ul>
                        </div>
                    </div>
                    <div class="widget_content">
                        <?php
                        $attributes = array('class' => 'form_container left_label', 'id' => 'addcms_form','enctype' => 'multipart/form-data');
                        echo form_open(ADMIN_ENC_URL.'/cms/insertEditCms', $attributes)
                        ?>
                        <div id="tab1">
                            <ul class="inner-subpage">
                                <li>
                                    <div class="form_grid_12">
                                        <label class="field_title"><?php if ($this->lang->line('admin_cms_select_main_page') != '') echo stripslashes($this->lang->line('admin_cms_select_main_page')); else echo 'Select Main Page'; ?> <span class="req">*</span></label>
                                        <div class="form_input">
                                            <select class="chzn-select required" name="parent" style="width: 375px; display: none;" data-placeholder="<?php if ($this->lang->line('admin_cms_select_main_page') != '') echo stripslashes($this->lang->line('admin_cms_select_main_page')); else echo 'Select Main Page'; ?>">
                                                <option value=""></option>
                                                <?php foreach ($cms_details->result() as $row) { ?>
                                                    <option value="<?php echo $row->_id; ?>"><?php echo $row->page_name; ?></option>
                                                <?php } ?>
                                            </select>
                                        </div>
                                    </div>
                                </li>
                                <li>
                                    <div class="form_grid_12">
                                        <label class="field_title"><?php if ($this->lang->line('admin_cms_page_name') != '') echo stripslashes($this->lang->line('admin_cms_page_name')); else echo 'Page Name'; ?> <span class="req">*</span></label>
                                        <div class="form_input">
                                            <input name="page_name" id="page_name" type="text"  class="required large tipTop" title="<?php if ($this->lang->line('admin_pages_enter_page_name') != '') echo stripslashes($this->lang->line('admin_pages_enter_page_name')); else echo 'Please enter the page name'; ?>"/>
                                        </div>
                                    </div>
                                </li>
                                <li>
                                    <div class="form_grid_12">
                                        <label class="field_title"><?php if ($this->lang->line('admin_cms_page_title') != '') echo stripslashes($this->lang->line('admin_cms_page_title')); else echo 'Page Title'; ?></label>
                                        <div class="form_input">
                                            <input name="page_title" id="page_title" type="text"  class="large tipTop" title="<?php if ($this->lang->line('admin_pages_enter_page_title') != '') echo stripslashes($this->lang->line('admin_pages_enter_page_title')); else echo 'Please enter the page title'; ?>"/>
                                        </div>
                                    </div>
                                </li>
                                <li>
                                    <div class="form_grid_12">
                                        <label class="field_title"><?php if ($this->lang->line('admin_cms_css_script') != '') echo stripslashes($this->lang->line('admin_cms_css_script')); else echo 'Css / Script'; ?> <span class="label_intro"><?php if ($this->lang->line('admin_cms_inline_css_and_script') != '') echo stripslashes($this->lang->line('admin_cms_inline_css_and_script')); else echo 'Please write down the content css, inline css and script in this field.'; ?></span></label></label>
                                        <div class="form_input">
                                            <textarea name="css_descrip" id="css_descrip"  class="large tipTop" title="<?php if ($this->lang->line('admin_pages_enter_css_and_script') != '') echo stripslashes($this->lang->line('admin_pages_enter_css_and_script')); else echo 'Please enter the css and script'; ?>" style="width:70%;" rows="6"></textarea>
                                        </div>
                                    </div>
                                </li>
                                <li>
                                    <div class="form_grid_12">
                                        <label class="field_title"><?php if ($this->lang->line('admin_cms_description') != '') echo stripslashes($this->lang->line('admin_cms_description')); else echo 'Description'; ?></label>
                                        <div class="form_input">
                                            <textarea name="description"  class="large tipTop mceEditor" title="<?php if ($this->lang->line('admin_pages_enter_page_content') != '') echo stripslashes($this->lang->line('admin_pages_enter_page_content')); else echo 'Please enter the page content'; ?>"></textarea>
                                        </div>
                                    </div>
                                </li>
                                <?php /* 
								<li>
                                    <div class="form_grid_12">
                                        <label class="field_title">Hidden Page<span class="req">*</span></label>
                                        <div class="form_input">
                                            <div class="yes_no">
                                                <input type="checkbox"  name="hidden_page" id="yes_no_yes" class="yes_no"/>
                                            </div>
                                        </div>
                                    </div>
                                </li> 
								*/ ?>
                            </ul>
                            <ul class="last-btn-submit"><li><div class="form_grid_12">
                                        <div class="form_input">
                                            <button type="submit" class="btn_small btn_blue" ><span><?php if ($this->lang->line('admin_common_submit') != '') echo stripslashes($this->lang->line('admin_common_submit')); else echo 'Submit'; ?></span></button>
                                        </div>
                                    </div></li></ul>
                        </div>
						
						
						 <div id="tab2" class="meta-tags">
                            <ul class="inner-subpage second-inner">
                                <li class="chck-banner">
                                    <div class="form_grid_12">
                                        <label class="field_title"><?php if ($this->lang->line('admin_cms_use_banner') != '') echo stripslashes($this->lang->line('admin_cms_use_banner')); else echo 'Use Banner'; ?></label>
                                        <div class="form_input">
                                           
											<div class="yes_no1">
                                                <input type="checkbox"  name="use_banner" id="yes_no_yes" class="yes_no1"/>
                                            </div>
											
                                        </div>
                                    </div>
                                </li>
                                <li>
                                    <div class="form_grid_12">
                                        <label class="field_title"><?php if ($this->lang->line('admin_cms_banner_image') != '') echo stripslashes($this->lang->line('admin_cms_banner_image')); else echo 'Banner Image'; ?></label>
                                        <div class="form_input">
                                            <input name="banner_img" id="banner_img" type="file"  class="large tipTop" title="<?php if ($this->lang->line('admin_pages_choose_banner_image') != '') echo stripslashes($this->lang->line('admin_pages_choose_banner_image')); else echo 'Please choose the banner image'; ?>"/>
                                        </div>
                                    </div>
                                </li>                      
                            </ul>
                            <ul class="last-btn-submit"><li><div class="form_grid_12">
                                        <div class="form_input">
                                            <button type="submit" class="btn_small btn_blue" ><span><?php if ($this->lang->line('admin_common_submit') != '') echo stripslashes($this->lang->line('admin_common_submit')); else echo 'Submit'; ?></span></button>
                                        </div>
                                    </div></li></ul>
                        </div>
						
                        <div id="tab3" class="meta-tags">
                            <ul class="inner-subpage third-inner">
                                <li>
                                    <div class="form_grid_12">
                                        <label class="field_title"><?php if ($this->lang->line('admin_cms_meta_title') != '') echo stripslashes($this->lang->line('admin_cms_meta_title')); else echo 'Meta Title'; ?> <span class="label_intro"><?php if ($this->lang->line('admin_cms_title_of_your_site') != '') echo stripslashes($this->lang->line('admin_cms_title_of_your_site')); else echo 'The title of your site.This is the first line that will be displayed in a search result. Do not make the title longer than 100 caracters.'; ?>
                                            </span></label>
                                        <div class="form_input">
                                            <input name="meta_title" id="meta_title" type="text"  class="large tipTop" title="<?php if ($this->lang->line('admin_pages_page_meta_title') != '') echo stripslashes($this->lang->line('admin_pages_page_meta_title')); else echo 'Please enter the page meta title'; ?>"/>
                                        </div>
                                    </div>
                                </li>
                                <li>
                                    <div class="form_grid_12">
                                        <label class="field_title"><?php if ($this->lang->line('admin_cms_meta_tag') != '') echo stripslashes($this->lang->line('admin_cms_meta_tag')); else echo 'Meta Tag'; ?><span class="label_intro"><?php if ($this->lang->line('admin_cms_different_keyword') != '') echo stripslashes($this->lang->line('admin_cms_different_keyword')); else echo 'Describe 20 different keyword seperated by comma to describe or categorize your page.'; ?></span></label>
                                        <div class="form_input">
                                            <input name="meta_tag" id="meta_tag" type="text"  class="large tipTop" title="<?php if ($this->lang->line('admin_pages_page_meta_tag') != '') echo stripslashes($this->lang->line('admin_pages_page_meta_tag')); else echo 'Please enter the page meta tag'; ?>"/>
                                        </div>
                                    </div>
                                </li>
                                <li>
                                    <div class="form_grid_12">
                                        <label class="field_title"><?php if ($this->lang->line('admin_cms_meta_description') != '') echo stripslashes($this->lang->line('admin_cms_meta_description')); else echo 'Meta Description'; ?>
                                            <span class="label_intro"><?php if ($this->lang->line('admin_cms_short_description') != '') echo stripslashes($this->lang->line('admin_cms_short_description')); else echo '>A short description in 200 characters of this page. This appears below title tag in the search results. Add a different description for every page.'; ?></span></label>
                                        <div class="form_input">
                                            <textarea name="meta_description" id="meta_description"  class="large tipTop" title="<?php if ($this->lang->line('admin_pages_page_meta_description') != '') echo stripslashes($this->lang->line('admin_pages_page_meta_description')); else echo 'Please enter the meta description'; ?>"></textarea>
                                        </div>
                                    </div>
                                </li>
                                <li>
                                    <div class="form_grid_12">
                                        <label class="field_title"><?php if ($this->lang->line('meta_abstraction') != '') echo stripslashes($this->lang->line('meta_abstraction')); else echo 'Meta Abstraction (Optional)'; ?>
                                            <span class="label_intro"><?php if ($this->lang->line('admin_cms_important_relevant_keyword') != '') echo stripslashes($this->lang->line('admin_cms_important_relevant_keyword')); else echo 'Fill out the 3 most important and relevant keywords of the page you add meta tags to. Seperate the 3 words with a comma and a space.'; ?></span></label>
                                        <div class="form_input">
                                            <textarea name="meta_abstraction" id="meta_abstraction"  class="large tipTop" title="<?php if ($this->lang->line('admin_pages_page_meta_description') != '') echo stripslashes($this->lang->line('admin_pages_page_meta_description')); else echo 'Please enter the meta description'; ?>"></textarea>
                                        </div>
                                    </div>
                                </li>
                            </ul>
                            <ul class="last-btn-submit"><li><div class="form_grid_12">
                                        <div class="form_input">
                                            <button type="submit" class="btn_small btn_blue" ><span><?php if ($this->lang->line('admin_common_submit') != '') echo stripslashes($this->lang->line('admin_common_submit')); else echo 'Submit'; ?></span></button>
                                        </div>
                                    </div></li></ul>
                        </div>
                        <input type="hidden" name="subpage" value="subpage"/>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <span class="clear"></span> </div>
</div>
<script>
$.validator.setDefaults({ ignore: ":hidden:not(select)" });
$('#addcms_form').validate();
</script>
<?php
$this->load->view(ADMIN_ENC_URL.'/templates/footer.php');
?>
