<?php
$this->load->view(ADMIN_ENC_URL.'/templates/header.php');
?>

<div id="content">
  <div class="grid_container">
    <div class="grid_12">
      <div class="widget_wrap">
        <div class="widget_wrap tabby">
          <div class="widget_top"> <span class="h_icon list"></span>
            <h6><?php if ($this->lang->line('admin_cms_edit_page') != '') echo stripslashes($this->lang->line('admin_cms_edit_page')); else echo 'Edit Page'; ?></h6>
            <div id="widget_tab">
              <ul>
                <li><a href="#tab1" class="active_tab"><?php if ($this->lang->line('admin_cms_content') != '') echo stripslashes($this->lang->line('admin_cms_content')); else echo 'Content'; ?></a></li>
                <li><a href="#tab2"><?php if ($this->lang->line('admin_cms_seo_details') != '') echo stripslashes($this->lang->line('admin_cms_seo_details')); else echo 'SEO Details'; ?></a></li>
              </ul>
            </div>
          </div>
          <div class="widget_content">
            <?php 
				$attributes = array('class' => 'form_container left_label', 'id' => 'addcms_form');
				echo form_open(ADMIN_ENC_URL.'/cms/insertEditHelpPage',$attributes) 
			?>
            <div id="tab1">
              <ul>
              <?php if ($cms_details->row()->category == 'Sub'){?>
              	<li>
                  <div class="form_grid_12">
                    <label class="field_title"><?php if ($this->lang->line('admin_cms_select_main_page') != '') echo stripslashes($this->lang->line('admin_cms_select_main_page')); else echo 'Select Main Page'; ?> <span class="req">*</span></label>
                    <div class="form_input">
                      <select class="chzn-select required" name="help_id" style="width: 375px; display: none;" data-placeholder="Select Main Page">
                      		<option value=""></option>
                      		<?php foreach ($cms_main_details->result() as $row){?>
                      		<option <?php if ($row->id == $cms_details->row()->help_id){echo 'selected="selected"';}?> value="<?php echo $row->id;?>"><?php echo $row->title;?></option>
                      		<?php }?>
                      </select>
                    </div>
                  </div>
                </li>
               <?php }?>
                <li>
                  <div class="form_grid_12">
                    <label class="field_title"><?php if ($this->lang->line('admin_cms_page_name') != '') echo stripslashes($this->lang->line('admin_cms_page_name')); else echo 'Page Name'; ?> <span class="req">*</span></label>
                    <div class="form_input">
                      <input name="page_name" id="page_name" type="text" value="<?php echo $cms_details->row()->page_name;?>"  class="required large tipTop" title="<?php if ($this->lang->line('admin_pages_enter_page_name') != '') echo stripslashes($this->lang->line('admin_pages_enter_page_name')); else echo 'Please enter the page name'; ?>"/>
                    </div>
                  </div>
                </li>
                <li>
                  <div class="form_grid_12">
                    <label class="field_title"><?php if ($this->lang->line('admin_cms_page_title') != '') echo stripslashes($this->lang->line('admin_cms_page_title')); else echo 'Page Title'; ?></label>
                    <div class="form_input">
                      <input name="page_title" id="page_title" type="text" value="<?php echo $cms_details->row()->page_title;?>"  class="large tipTop" title="<?php if ($this->lang->line('admin_pages_enter_page_title') != '') echo stripslashes($this->lang->line('admin_pages_enter_page_title')); else echo 'Please enter the page title'; ?>"/>
                    </div>
                  </div>
                </li>
                <li>
                  <div class="form_grid_12">
                    <label class="field_title"><?php if ($this->lang->line('admin_cms_css_script') != '') echo stripslashes($this->lang->line('admin_cms_css_script')); else echo 'Css / Script'; ?> <span class="label_intro"><?php if ($this->lang->line('admin_cms_inline_css_and_script') != '') echo stripslashes($this->lang->line('admin_cms_inline_css_and_script')); else echo 'Please write down the content css, inline css and script in this field.'; ?></span></label></label>
                    <div class="form_input">
                      <textarea name="css_descrip" id="css_descrip"  class="large tipTop" title="<?php if ($this->lang->line('admin_pages_enter_css_and_script') != '') echo stripslashes($this->lang->line('admin_pages_enter_css_and_script')); else echo 'Please enter the css and script'; ?>" style="width:70%;" rows="6"><?php echo $cms_details->row()->css_descrip;?></textarea>
                    </div>
                  </div>
                </li>
                <li>
                  <div class="form_grid_12">
                    <label class="field_title"><?php if ($this->lang->line('admin_cms_description') != '') echo stripslashes($this->lang->line('admin_cms_description')); else echo 'Description'; ?></label>
                    <div class="form_input">
                      <textarea name="description"  class="large tipTop mceEditor" title="<?php if ($this->lang->line('admin_pages_enter_page_content') != '') echo stripslashes($this->lang->line('admin_pages_enter_page_content')); else echo 'Please enter the page content'; ?>"><?php echo $cms_details->row()->description;?></textarea>
                    </div>
                  </div>
                </li>
              </ul>
            <ul><li><div class="form_grid_12">
				<div class="form_input">
					<button type="submit" class="btn_small btn_blue" ><span><?php if ($this->lang->line('admin_common_submit') != '') echo stripslashes($this->lang->line('admin_common_submit')); else echo 'Submit'; ?></span></button>
				</div>
			</div></li></ul>
			</div>
            <div id="tab2">
              <ul>
                <li>
                  <div class="form_grid_12">
                    <label class="field_title"><?php if ($this->lang->line('admin_cms_meta_title') != '') echo stripslashes($this->lang->line('admin_cms_meta_title')); else echo 'Meta Title'; ?></label>
                    <div class="form_input">
                      <input name="meta_title" id="meta_title" type="text" value="<?php echo $cms_details->row()->meta_title;?>"  class="large tipTop" title="<?php if ($this->lang->line('admin_pages_page_meta_title') != '') echo stripslashes($this->lang->line('admin_pages_page_meta_title')); else echo 'Please enter the page meta title'; ?>"/>
                    </div>
                  </div>
                </li>
                <li>
                  <div class="form_grid_12">
                    <label class="field_title"><?php if ($this->lang->line('admin_cms_meta_tag') != '') echo stripslashes($this->lang->line('admin_cms_meta_tag')); else echo 'Meta Tag'; ?></label>
                    <div class="form_input">
                      <input name="meta_tag" id="meta_tag" type="text" value="<?php echo $cms_details->row()->meta_tag;?>"  class="large tipTop" title="<?php if ($this->lang->line('admin_pages_page_meta_tag') != '') echo stripslashes($this->lang->line('admin_pages_page_meta_tag')); else echo 'Please enter the page meta tag'; ?>"/>
                    </div>
                  </div>
                </li>
                <li>
                  <div class="form_grid_12">
                    <label class="field_title"><?php if ($this->lang->line('admin_cms_meta_description') != '') echo stripslashes($this->lang->line('admin_cms_meta_description')); else echo 'Meta Description'; ?></label>
                    <div class="form_input">
                      <textarea name="meta_description" id="meta_description"  class="large tipTop" title="<?php if ($this->lang->line('admin_pages_page_meta_description') != '') echo stripslashes($this->lang->line('admin_pages_page_meta_description')); else echo 'Please enter the meta description'; ?>"><?php echo $cms_details->row()->meta_description;?></textarea>
                    </div>
                  </div>
                </li>
              </ul>
             <ul><li><div class="form_grid_12">
				<div class="form_input">
					<button type="submit" class="btn_small btn_blue" ><span><?php if ($this->lang->line('admin_common_submit') != '') echo stripslashes($this->lang->line('admin_common_submit')); else echo 'Submit'; ?></span></button>
				</div>
			</div></li></ul>
			</div>
			<input type="hidden" name="cms_id" value="<?php echo $cms_details->row()->id;?>"/>
			<?php if ($cms_details->row()->category == 'Sub'){?>
				<input type="hidden" name="subpage" value="subpage"/>
			<?php }?>
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
