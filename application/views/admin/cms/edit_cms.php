<?php  #echo '<pre>'; print_r($cms_details->result()); die;
$this->load->view(ADMIN_ENC_URL.'/templates/header.php');
?>
<style>
h6.lang_rite_edit span {
    margin-top: 0px !important;
}
</style>
<div id="content" class="add-cms-sec">
    <div class="grid_container">
        <div class="grid_12">
            <div class="widget_wrap">
                <div class="widget_wrap tabby">
                    <div class="widget_top"> <span class="h_icon list"></span>
                        <h6>
                        
                         <?php if ($this->lang->line('admin_common_edit') != '')  $edit=stripslashes($this->lang->line('admin_common_edit')); else  $edit='Edit'; ?>
                        <?php if ($this->lang->line('admin_cms_edit_page') != '') echo stripslashes($this->lang->line('admin_cms_edit_page')); else echo 'Edit Page'; ?></h6>
                        <h6 class="lang_rite_edit second_wrap_rite"><span><?php if ($this->lang->line('admin_cms_languages_available') != '') echo stripslashes($this->lang->line('admin_cms_languages_available')); else echo 'Languages Available'; ?>:</span>
                        <?php
                        if ($language_code != '') {
                            if (isset($cms_details->row()->$language_code) && !empty($cms_details->row()->$language_code))
                                $lang_details = $cms_details->row()->$language_code;
                            $lang_code = $language_code;
                            $open_square_bracket = "[";
                            $close_square_bracket = "]";
                        } else {
                            $lang_code = '';
                            $open_square_bracket = '';
                            $close_square_bracket = '';
                        }
						echo  '<input name="english" type="checkbox" value="en"  checked disabled readonly><a href="' . base_url() .ADMIN_ENC_URL. '/cms/edit_cms_form/'.$cms_id.'" style="color:white">English</a>';
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
                                    echo '<input name="' . $row->name . '" type="checkbox" checked value="' . $row->lang_code . '" disabled readonly><span ' . $styling . '>' . $row->name . "</span>" . '<a href="' . base_url() .ADMIN_ENC_URL. '/cms/edit_cms_form/' . $cms_id . '/' . $row->lang_code . '" style="color:red" class="lang_edit_sec">' . $EditText . '</a>';
                                } else {
                                    echo '<input name="' . $row->name . '" type="checkbox" value="' . $row->lang_code . '" class=""><span ' . $styling . '>' . $row->name . "</span>" . '<a href="' . base_url() . ADMIN_ENC_URL.'/cms/edit_cms_form/' . $cms_id . '/' . $row->lang_code . '" style="color:red" class="lang_edit_sec">' . $EditText . '</a>';
                                }
                            }
                        }
                        ?>
						</h6>
                        <div id="widget_tab">
                            <ul>
                                <li><a href="#tab1" class="active_tab"><?php if ($this->lang->line('admin_cms_content') != '') echo stripslashes($this->lang->line('admin_cms_content')); else echo 'Content'; ?></a></li>
								<li><a href="#tab2" onclick="mkViw()"><?php if ($this->lang->line('admin_cms_banner_details') != '') echo stripslashes($this->lang->line('admin_cms_banner_details')); else echo 'Banner Details'; ?></a></li>
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
                            <ul class="second-cms">
                                <?php
                                if ($cms_details->row()->category == 'Sub') {
                                    ?>
                                    <li>
                                        <div class="form_grid_12">
                                            <label class="field_title"><?php if ($this->lang->line('admin_cms_select_main_page') != '') echo stripslashes($this->lang->line('admin_cms_select_main_page')); else echo 'Select Main Page'; ?> <span class="req">*</span></label>
                                            <div class="form_input">
                                                <select class="chzn-select required" name="parent" style="width: 375px; display: none;" data-placeholder="Select Main Page">
                                                    <option value=""></option>
                                                    <?php foreach ($cms_main_details->result() as $row) { ?>
                                                        <option <?php
                                                        if ($row->_id == $cms_details->row()->parent) {
                                                            echo 'selected="selected"';
                                                        }
                                                        ?> value="<?php echo $row->_id; ?>"><?php echo $row->page_name; ?></option>
                                                        <?php } ?>
                                                </select>
                                            </div>
                                        </div>
                                    </li>
                                <?php } ?>
                                <li>
                                    <div class="form_grid_12">
                                        <label class="field_title"><?php if ($this->lang->line('admin_cms_page_name') != '') echo stripslashes($this->lang->line('admin_cms_page_name')); else echo 'Page Name'; ?> <span class="req">*</span></label>
                                        <div class="form_input">

                                            <?php
                                            if (!empty($lang_code)) {
                                                $pagename = $lang_code . $open_square_bracket . "page_name" . $close_square_bracket;
                                            } else {
                                                $pagename = "page_name";
                                            }
                                            if (!empty($lang_details)) {

                                                if (isset($lang_details['page_name']))
                                                    $page_name_val = $lang_details['page_name'];
                                                else
                                                    $page_name_val = '';
                                            } else {


                                                if (isset($cms_details->row()->page_name))
                                                    $page_name_val = $cms_details->row()->page_name;
                                                else
                                                    $page_name_val = '';
                                            }
                                            ?>
                                            <input name="<?php echo $pagename; ?>" id="page_name" type="text" value="<?php echo $page_name_val; ?>"  class="required large tipTop" title="<?php if ($this->lang->line('admin_pages_enter_page_name') != '') echo stripslashes($this->lang->line('admin_pages_enter_page_name')); else echo 'Please enter the page name'; ?>"/>
                                        </div>
                                    </div>
                                </li>

                                <li>
                                    <div class="form_grid_12">
                                        <label class="field_title"><?php if ($this->lang->line('admin_cms_page_title') != '') echo stripslashes($this->lang->line('admin_cms_page_title')); else echo 'Page Title'; ?> <span class="req">*</span></label>
                                        <div class="form_input">
                                            <?php
                                            if (!empty($lang_code)) {
                                                $page_title = $lang_code . $open_square_bracket . "page_title" . $close_square_bracket;
                                            } else {
                                                $page_title = "page_title";
                                            }
                                            if (!empty($lang_details)) {

                                                if (isset($lang_details['page_title']))
                                                    $page_title_val = $lang_details['page_title'];
                                                else
                                                    $page_title_val = '';
                                            } else {

                                                if (isset($cms_details->row()->page_title))
                                                    $page_title_val = $cms_details->row()->page_title;
                                                else
                                                    $page_title_val = '';
                                            }
                                            ?>
                                            <input name="<?php echo $page_title; ?>" id="page_title" type="text" value="<?php echo $page_title_val; ?>"  class="required large tipTop" title="<?php if ($this->lang->line('admin_pages_enter_page_name') != '') echo stripslashes($this->lang->line('admin_pages_enter_page_name')); else echo 'Please enter the page name'; ?>"/>
                                        </div>
                                    </div>
                                </li>
                                <li>
                                    <div class="form_grid_12">
                                        <label class="field_title"><?php if ($this->lang->line('admin_cms_css_script') != '') echo stripslashes($this->lang->line('admin_cms_css_script')); else echo 'Css / Script'; ?> <span class="label_intro"><?php if ($this->lang->line('admin_cms_inline_css_and_script') != '') echo stripslashes($this->lang->line('admin_cms_inline_css_and_script')); else echo 'Please write down the content css, inline css and script in this field.'; ?></span></label></label>
                                        <div class="form_input">
                                            <?php
                                            if (!empty($lang_code)) {
                                                $css_descrip = $lang_code . $open_square_bracket . "css_descrip" . $close_square_bracket;
                                            } else {
                                                $css_descrip = "css_descrip";
                                            }
                                            if (!empty($lang_details)) {

                                                if (isset($lang_details['css_descrip']))
                                                    $css_descrip_val = $lang_details['css_descrip'];
                                                else
                                                    $css_descrip_val = '';
                                            } else {

                                                if (isset($cms_details->row()->css_descrip))
                                                    $css_descrip_val = $cms_details->row()->css_descrip;
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
                                            if (!empty($lang_code)) {
                                                $description = $lang_code . $open_square_bracket . "description" . $close_square_bracket;
                                            } else {
                                                $description = "description";
                                            }
                                            if (!empty($lang_details)) {
												
                                                if (isset($lang_details['description']))
                                                    $description_val = $lang_details['description'];
                                                else
                                                    $description_val = '';
                                            } else {

                                                if (isset($cms_details->row()->description))
                                                    $description_val = $cms_details->row()->description;
                                                else
                                                    $description_val = '';
                                            }
                                            ?>
                                            <textarea name="<?php echo $description; ?>"  class="large tipTop mceEditor" title="<?php if ($this->lang->line('admin_pages_enter_page_content') != '') echo stripslashes($this->lang->line('admin_pages_enter_page_content')); else echo 'Please enter the page content'; ?>"><?php echo $description_val; ?></textarea>
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
						
						<div id="tab2" class="meta-tags">
                            <ul class="inner-subpage second-inner">
                                <li class="chck-banner">
                                    <div class="form_grid_12">
                                        <label class="field_title"><?php if ($this->lang->line('admin_cms_use_banner') != '') echo stripslashes($this->lang->line('admin_cms_use_banner')); else echo 'Use Banner'; ?></label>
                                        <div class="form_input">
                                           <?php 
											$use_banner = ''; 
											if (!empty($lang_details)) {
												if (isset($lang_details['use_banner'])){
													if($lang_details['use_banner'] == 'Yes'){
														$use_banner = 'checked="checked"';
													}
												}
											} else {
												if (isset($cms_details->row()->use_banner)){
													if($cms_details->row()->use_banner == 'Yes'){
														$use_banner = 'checked="checked"';
													}
												}
											}
										   
										   ?>
											<div class="yes_no1">
                                                <input type="checkbox"  name="use_banner" <?php echo $use_banner; ?> id="yes_no_yes" class="yes_no1" />
                                            </div>
											
                                        </div>
                                    </div>
                                </li>
                                <li>
                                    <div class="form_grid_12">
                                        <label class="field_title"><?php if ($this->lang->line('admin_cms_banner_image') != '') echo stripslashes($this->lang->line('admin_cms_banner_image')); else echo 'Banner Image'; ?></label>
                                        <div class="form_input">
                                            <input name="banner_img" id="banner_img" type="file"  class="large tipTop" title="<?php if ($this->lang->line('admin_pages_choose_banner_image') != '') echo stripslashes($this->lang->line('admin_pages_choose_banner_image')); else echo 'Please choose the banner image'; ?>"/>
											
											<?php 
												$banerPAth = '';
												
												$langNme = '';
												if (!empty($lang_details)) {
													if (isset($lang_details['banner_img'])){
														if($lang_details['banner_img'] != ''){
															$banerPAth = 'images/banner/'.$lang_details['banner_img'];
															$langNme = $lang_details['banner_img'];
														}
													}
												} else {
													if (isset($cms_details->row()->banner_img)){
														if($cms_details->row()->banner_img != ''){
															$banerPAth = 'images/banner/'.$cms_details->row()->banner_img;
															$langNme = $cms_details->row()->banner_img;
														}
													}
													
												}
												if($banerPAth != ''){
											?>
											<br/>
											<img src="<?php echo $banerPAth; ?>" width="200"/>
											<input name="old_banner_img" type="hidden" value="<?php echo $langNme; ?>">
											<?php } 
											?>
											
											
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
                                        <span class="label_intro meta-note"><?php if ($this->lang->line('admin_cms_meta_tags_different_language') != '') echo stripslashes($this->lang->line('admin_cms_meta_tags_different_language')); else echo 'Note: Meta tags for different languages should be added.'; ?></span>
                                        <label class="field_title"><?php if ($this->lang->line('admin_cms_meta_title') != '') echo stripslashes($this->lang->line('admin_cms_meta_title')); else echo 'Meta Title'; ?>
                                            <span class="label_intro"><?php if ($this->lang->line('admin_cms_title_of_your_site') != '') echo stripslashes($this->lang->line('admin_cms_title_of_your_site')); else echo 'The title of your site.This is the first line that will be displayed in a search result. Do not make the title longer than 100 caracters.'; ?>
                                            </span></label>
                                        <div class="form_input">
                                            <?php
                                            if (!empty($lang_code)) {
                                                $meta_title = $lang_code . $open_square_bracket . "meta_title" . $close_square_bracket;
                                            } else {
                                                $meta_title = "meta_title";
                                            }
                                            if (!empty($lang_details)) {

                                                if (isset($lang_details['meta_title']))
                                                    $meta_title_val = $lang_details['meta_title'];
                                                else
                                                    $meta_title_val = '';
                                            } else {

                                                if (isset($cms_details->row()->meta_title))
                                                    $meta_title_val = $cms_details->row()->meta_title;
                                                else
                                                    $meta_title_val = '';
                                            }
                                            ?>
                                            <input name="<?php echo $meta_title; ?>" id="meta_title" type="text" value="<?php echo $meta_title_val; ?>"  class="large tipTop" title="<?php if ($this->lang->line('admin_pages_page_meta_title') != '') echo stripslashes($this->lang->line('admin_pages_page_meta_title')); else echo 'Please enter the page meta title'; ?>"/>
                                        </div>
                                    </div>
                                </li>
                                <li>
                                    <div class="form_grid_12">
                                        <label class="field_title"><?php if ($this->lang->line('admin_cms_meta_tag') != '') echo stripslashes($this->lang->line('admin_cms_meta_tag')); else echo 'Meta Tag'; ?>
                                            <span class="label_intro"><?php if ($this->lang->line('admin_cms_different_keyword') != '') echo stripslashes($this->lang->line('admin_cms_different_keyword')); else echo 'Describe 20 different keyword seperated by comma to describe or categorize your page.'; ?></span></label>
                                        <div class="form_input">
                                            <?php
                                            if (!empty($lang_code)) {
                                                $meta_tag = $lang_code . $open_square_bracket . "meta_tag" . $close_square_bracket;
                                            } else {
                                                $meta_tag = "meta_tag";
                                            }
                                            if (!empty($lang_details)) {

                                                if (isset($lang_details['meta_tag']))
                                                    $meta_tag_val = $lang_details['meta_tag'];
                                                else
                                                    $meta_tag_val = '';
                                            } else {

                                                if (isset($cms_details->row()->meta_tag))
                                                    $meta_tag_val = $cms_details->row()->meta_tag;
                                                else
                                                    $meta_tag_val = '';
                                            }
                                            ?>
                                            <input name="<?php echo $meta_tag; ?>" id="meta_tag" type="text" value="<?php echo $meta_tag_val; ?>"  class="large tipTop" title="<?php if ($this->lang->line('admin_pages_page_meta_tag') != '') echo stripslashes($this->lang->line('admin_pages_page_meta_tag')); else echo 'Please enter the page meta tag'; ?>" />
                                        </div>
                                    </div>
                                </li>
                                <li>
                                    <div class="form_grid_12">
                                        <label class="field_title"><?php if ($this->lang->line('admin_cms_meta_description') != '') echo stripslashes($this->lang->line('admin_cms_meta_description')); else echo 'Meta Description'; ?>
                                            <span class="label_intro"><?php if ($this->lang->line('admin_cms_short_description') != '') echo stripslashes($this->lang->line('admin_cms_short_description')); else echo 'A short description in 200 characters of this page. This appears below title tag in the search results. Add a different description for every page.'; ?></span></label>
                                        <div class="form_input">
                                            <?php
                                            if (!empty($lang_code)) {
                                                $meta_description = $lang_code . $open_square_bracket . "meta_description" . $close_square_bracket;
                                            } else {
                                                $meta_description = "meta_description";
                                            }
                                            if (!empty($lang_details)) {

                                                if (isset($lang_details['meta_description']))
                                                    $meta_description_val = $lang_details['meta_description'];
                                                else
                                                    $meta_description_val = "";
                                            } else {

                                                if (isset($cms_details->row()->meta_description))
                                                    $meta_description_val = $cms_details->row()->meta_description;
                                                else
                                                    $meta_description_val = '';
                                            }
                                            ?>
                                            <textarea name="<?php echo $meta_description; ?>" id="meta_description"  class="large tipTop" title="<?php if ($this->lang->line('admin_pages_page_meta_description') != '') echo stripslashes($this->lang->line('admin_pages_page_meta_description')); else echo 'Please enter the meta description'; ?>"><?php echo $meta_description_val; ?></textarea>
                                        </div>
                                    </div>
                                </li>

                                <li>
                                    <div class="form_grid_12">
                                        <label class="field_title"><?php if ($this->lang->line('meta_abstraction') != '') echo stripslashes($this->lang->line('meta_abstraction')); else echo 'Meta Abstraction (Optional)'; ?>
                                            <span class="label_intro"><?php if ($this->lang->line('admin_cms_important_relevant_keyword') != '') echo stripslashes($this->lang->line('admin_cms_important_relevant_keyword')); else echo 'Fill out the 3 most important and relevant keywords of the page you add meta tags to. Seperate the 3 words with a comma and a space.'; ?></span></label>
                                        <div class="form_input">
                                            <?php
                                            if (!empty($lang_code)) {
                                                $meta_abstraction = $lang_code . $open_square_bracket . "meta_abstraction" . $close_square_bracket;
                                            } else {
                                                $meta_abstraction = "meta_abstraction";
                                            }
                                            if (!empty($lang_details)) {

                                                if (isset($lang_details['meta_abstraction']))
                                                    $meta_abstraction_val = $lang_details['meta_abstraction'];
                                                else
                                                    $meta_abstraction_val = '';
                                            } else {

                                                if (isset($cms_details->row()->meta_abstraction))
                                                    $meta_abstraction_val = $cms_details->row()->meta_abstraction;
                                                else
                                                    $meta_abstraction_val = '';
                                            }
                                            ?>
                                            <input name="<?php echo $meta_abstraction; ?>" id="meta_abstraction" type="text" value="<?php echo $meta_abstraction_val; ?>"  class="large tipTop" title="<?php if ($this->lang->line('admin_pages_page_meta_abstraction') != '') echo stripslashes($this->lang->line('admin_pages_page_meta_abstraction')); else echo 'Please enter the meta Abstraction'; ?>" />
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
                        <input type="hidden" name="cms_id" value="<?php echo $cms_details->row()->_id; ?>"/>
                        <input type="hidden" name="lang_code" value="<?php echo $this->uri->segment(5); ?>"/>
                        <?php if ($cms_details->row()->category == 'Sub') { ?>
                            <input type="hidden" name="subpage" value="subpage"/>
                        <?php } ?>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <span class="clear"></span> </div>
</div>
<script type="application/javascript">
function mkViw(){
	//$('.yes_no1 :checkbox').iphoneStyle();
}
</script>
<script>
$.validator.setDefaults({ ignore: ":hidden:not(select)" });
$('#addcms_form').validate();
</script>
<?php
$this->load->view(ADMIN_ENC_URL.'/templates/footer.php');
?>
