<?php
$this->load->view(ADMIN_ENC_URL.'/templates/header.php');
?>
<div id="content" class="admin-settings edit-global-set globe_configure find_global">
    <div class="grid_container">
        <div class="grid_12">
            <div class="widget_wrap">
                <div class="widget_wrap tabby">
                    <div class="widget_top"> 
                        <span class="h_icon list"></span>
                        <h6><?php if ($this->lang->line('admin_settings_global_site_configuration') != '') echo stripslashes($this->lang->line('admin_settings_global_site_configuration')); else echo 'Global Site Configuration'; ?></h6>
                        <div id="widget_tab">
                            <ul>
                                <li><a href="#tab1" class="active_tab"><?php if ($this->lang->line('admin_settings_admin_settings') != '') echo stripslashes($this->lang->line('admin_settings_admin_settings')); else echo 'Admin Settings'; ?></a></li>
                                <li><a href="#tab2" ><?php if ($this->lang->line('admin_settings_social_media') != '') echo stripslashes($this->lang->line('admin_settings_social_media')); else echo 'Social Media'; ?></a></li>
                                <li><a href="#tab3"><?php if ($this->lang->line('admin_settings_google_webmaster_seo') != '') echo stripslashes($this->lang->line('admin_settings_google_webmaster_seo')); else echo 'Google Webmaster & SEO'; ?></a></li>
                             
                            </ul>
                        </div>
                    </div>
                    <div class="widget_content">
                        <?php
                        $attributes = array('class' => 'form_container left_label ajaxsubmit', 'id' => 'settings_form', 'enctype' => 'multipart/form-data');
                        echo form_open_multipart(ADMIN_ENC_URL.'/adminlogin/admin_global_settings', $attributes)
                        ?>
                        <input type="hidden" name="form_mode" value="main_settings"/>
                        <div id="tab1" class="first-section">
                            <ul class="left-contsec">
								<li>
                                    <div class="form_grid_12">
                                        <label class="field_title"><?php if ($this->lang->line('admin_settings_site_name') != '') echo stripslashes($this->lang->line('admin_settings_site_name')); else echo 'Site Name'; ?><span class="req">*</span></label>
                                        <div class="form_input">
                                            <input name="email_title" id="email_title" type="text" value="<?php echo $admin_settings->row()->email_title; ?>"  class="required large tipTop" title="<?php if ($this->lang->line('admin_setting_site_name_tooltip') != '') echo stripslashes($this->lang->line('admin_setting_site_name_tooltip')); else echo 'Please enter the site name'; ?>"/>
                                        </div>
                                    </div>
                                </li>
                                <li>
                                    <div class="form_grid_12">
                                        <label class="field_title"><?php if ($this->lang->line('admin_settings_admin_name') != '') echo stripslashes($this->lang->line('admin_settings_admin_name')); else echo 'Admin Name'; ?> <span class="req">*</span></label>
                                        <div class="form_input">
                                            <input name="admin_name" value="<?php echo $admin_settings->row()->admin_name; ?>" id="admin_name" type="text"  class="required large tipTop alphanumeric" title="<?php if ($this->lang->line('admin_subadmin_please_enter_admin_username') != '') echo stripslashes($this->lang->line('admin_subadmin_please_enter_admin_username')); else echo 'Please enter the admin username'; ?>"/>
                                        </div>
                                    </div>
                                </li>
								
								
								<li>
                                    <div class="form_grid_12">
                                        <label class="field_title"><?php if ($this->lang->line('admin_settings_email_address') != '') echo stripslashes($this->lang->line('admin_settings_email_address')); else echo 'Email Address'; ?> <span class="req">*</span></label>
                                        <div class="form_input">
                                            <input name="email" id="email" type="text" value="<?php echo $admin_settings->row()->email; ?>"  class="required large tipTop" title="<?php if ($this->lang->line('admin_subadmin_please_enter_admin_email_address') != '') echo stripslashes($this->lang->line('admin_subadmin_please_enter_admin_email_address')); else echo 'Please enter the admin email address'; ?>"/>
                                        </div>
                                    </div>
                                </li>

                                <li class="logo-img-sec">
                                    <div class="form_grid_12">
                                        <label class="field_title"><?php if ($this->lang->line('admin_settings_logo') != '') echo stripslashes($this->lang->line('admin_settings_logo')); else echo 'Logo'; ?></label>
                                        <div class="form_input">
                                            <input name="logo_image" id="logo_image" type="file"  class="large tipTop" title="<?php if ($this->lang->line('admin_setting_choose_logo_image') != '') echo stripslashes($this->lang->line('admin_setting_choose_logo_image')); else echo 'Please Choose the logo image'; ?>"/>
                                        </div>
                                        <div class="form_input logo-out">
                                            <img src="<?php echo base_url(); ?>images/logo/<?php echo $admin_settings->row()->logo_image; ?>" width="100px"/>
                                        </div>
                                    </div>
                                </li>
								
								<li class="logo-img-sec">
                                    <div class="form_grid_12">
                                        <label class="field_title"><?php if ($this->lang->line('admin_settings_favicon') != '') echo stripslashes($this->lang->line('admin_settings_favicon')); else echo 'Favicon'; ?></label>
                                        <div class="form_input">
                                            <input name="favicon_image" id="favicon_image" type="file"  class="large tipTop" title="<?php if ($this->lang->line('admin_setting_choose_favicon_image') != '') echo stripslashes($this->lang->line('admin_setting_choose_favicon_image')); else echo 'Please Choose the favicon image'; ?>"/>
                                        </div>
                                        <div class="form_input logo-out">
                                            <img src="<?php echo base_url(); ?>images/logo/<?php echo $admin_settings->row()->favicon_image; ?>" width="50px"/>
                                        </div>
                                    </div>
                                </li>
                                
							</ul>
							<ul class="rite-contsec">
                                <li>
                                    <div class="form_grid_12">
                                        <label class="field_title"><?php if ($this->lang->line('admin_settings_site_contact_email') != '') echo stripslashes($this->lang->line('admin_settings_site_contact_email')); else echo 'Site Contact Email'; ?><span class="req">*</span></label>
                                        <div class="form_input">
                                            <input name="site_contact_mail" id="site_contact_mail" value="<?php echo $admin_settings->row()->site_contact_mail; ?>" type="text"  class="large tipTop required email" title="<?php if ($this->lang->line('admin_setting_site_contact_email') != '') echo stripslashes($this->lang->line('admin_setting_site_contact_email')); else echo 'Please enter the site contact email'; ?>"/>
                                        </div>
                                    </div>
                                </li>
                                <li>
                                    <div class="form_grid_12">
                                        <label class="field_title"><?php if ($this->lang->line('admin_settings_site_contact_address') != '') echo stripslashes($this->lang->line('admin_settings_site_contact_address')); else echo 'Site Contact Address'; ?><span class="req">*</span></label>
                                        <div class="form_input">
                                            <input name="site_contact_address" id="site_contact_address" value="<?php if(isset($admin_settings->row()->site_contact_address)) echo $admin_settings->row()->site_contact_address; ?>" type="text"  class="large tipTop required" title="<?php if ($this->lang->line('admin_setting_site_contact_address') != '') echo stripslashes($this->lang->line('admin_setting_site_contact_address')); else echo 'Please enter the site contact address'; ?>"/>
                                        </div>
                                    </div>
                                </li>
                                <li>
                                    <div class="form_grid_12">
                                        <label class="field_title"><?php if ($this->lang->line('admin_settings_site_contact_number') != '') echo stripslashes($this->lang->line('admin_settings_site_contact_number')); else echo 'Site Contact Number'; ?><span class="req">*</span></label>
                                        <div class="form_input">
                                            <input name="customer_service_number" id="customer_service_number" value="<?php if(isset($admin_settings->row()->customer_service_number)) echo $admin_settings->row()->customer_service_number; ?>" type="text"  class="large tipTop required lanlinenumber" title="<?php if ($this->lang->line('admin_setting_site_contact_number') != '') echo stripslashes($this->lang->line('admin_setting_site_contact_number')); else echo 'Please enter the site contact number'; ?>"/>
                                        </div>
                                    </div>
                                </li>

                                

                                <li>
                                    <div class="form_grid_12">
                                        <label class="field_title"><?php if ($this->lang->line('admin_settings_map_center_latitude') != '') echo stripslashes($this->lang->line('admin_settings_map_center_latitude')); else echo 'Map Center Latitude'; ?> <span class="req">*</span></label>
                                        <div class="form_input">
                                            <input name="latitude" id="latitude" type="text" value="<?php if (isset($admin_settings->row()->latitude)) echo htmlentities($admin_settings->row()->latitude); ?>"  class="large number tipTop required" title="<?php if ($this->lang->line('admin_setting_enter_map_center_latitude') != '') echo stripslashes($this->lang->line('admin_setting_enter_map_center_latitude')); else echo 'Please enter the Map Center Latitude'; ?>"/>
                                        </div>
                                    </div>
                                </li>

                                <li>
                                    <div class="form_grid_12">
                                        <label class="field_title"><?php if ($this->lang->line('admin_settings_map_center_longitude') != '') echo stripslashes($this->lang->line('admin_settings_map_center_longitude')); else echo 'Map Center Longitude'; ?> <span class="req">*</span></label>
                                        <div class="form_input">
                                            <input name="longitude" id="longitude" type="text" value="<?php if (isset($admin_settings->row()->longitude)) echo htmlentities($admin_settings->row()->longitude); ?>"  class="large number tipTop required" title="<?php if ($this->lang->line('admin_setting_enter_map_center_longitude') != '') echo stripslashes($this->lang->line('admin_setting_enter_map_center_longitude')); else echo 'Please enter the Map Center Longitude'; ?>"/>
                                        </div>
                                    </div>
                                </li>
								
								<li>
                                    <div class="form_grid_12">
                                        <label class="field_title"><?php if ($this->lang->line('admin_settings_footer_content') != '') echo stripslashes($this->lang->line('admin_settings_footer_content')); else echo 'Footer Content'; ?></label>
                                        <div class="form_input">
                                            <input name="footer_content" id="footer_content" type="text" value="<?php echo htmlentities($admin_settings->row()->footer_content); ?>"  class="large tipTop" title="<?php if ($this->lang->line('admin_settings_footer_content_tooltip') != '') echo stripslashes($this->lang->line('admin_settings_footer_content_tooltip')); else echo 'Please enter the footer copyright content'; ?>"/>
                                        </div>
                                    </div>
                                </li>
								
								
								

                            </ul>
							
							<ul class="last-sec-btn">
								<li class="change-pass">
                                    <div class="form_grid_12">
                                        <div class="form_input">
                                            <button type="submit" class="btn_small btn_blue" ><span><?php if ($this->lang->line('admin_settings_submit') != '') echo stripslashes($this->lang->line('admin_settings_submit')); else echo 'Submit'; ?></span></button>
                                        </div>
                                    </div>
                                </li>
							</ul>

                            
                        </div>
                        </form>

                        <?php
                        $attributes = array('class' => 'form_container left_label', 'id' => 'settings_form','enctype' => 'multipart/form-data');
                        echo form_open(ADMIN_ENC_URL.'/adminlogin/admin_global_settings', $attributes)
                        ?>
                        <input type="hidden" name="form_mode" value="social"/>
                        <div id="tab2" class="first-section admin-sec-part">
                            <ul class="left-contsec">
                                <li>
                                    <h3 class="head_drive"><?php if ($this->lang->line('admin_settings_social_share_links') != '') echo stripslashes($this->lang->line('admin_settings_social_share_links')); else echo 'Social Share Links'; ?></h3>
                                </li>

                                <li>
                                    <div class="form_grid_12">
                                        <label class="field_title"><?php if ($this->lang->line('admin_settings_facebook_link') != '') echo stripslashes($this->lang->line('admin_settings_facebook_link')); else echo 'Facebook Link'; ?></label>
                                        <div class="form_input">
                                            <input name="facebook_link" id="facebook_link" type="text" value="<?php if (isset($admin_settings->row()->facebook_link)) echo $admin_settings->row()->facebook_link; ?>"  class="large tipTop" title="<?php if ($this->lang->line('admin_setting_enter_facebook_url') != '') echo stripslashes($this->lang->line('admin_setting_enter_facebook_url')); else echo 'Please enter the site facebook url'; ?>"/>
                                        </div>
                                    </div>
                                </li>
                                <li>
                                    <div class="form_grid_12">
                                        <label class="field_title"><?php if ($this->lang->line('admin_settings_twitter_link') != '') echo stripslashes($this->lang->line('admin_settings_twitter_link')); else echo 'Twitter Link'; ?></label>
                                        <div class="form_input">
                                            <input name="twitter_link" id="twitter_link" type="text"  value="<?php if (isset($admin_settings->row()->twitter_link)) echo $admin_settings->row()->twitter_link; ?>" class="large tipTop" title="<?php if ($this->lang->line('admin_setting_enter_twitter_url') != '') echo stripslashes($this->lang->line('admin_setting_enter_twitter_url')); else echo 'Please enter the site twitter url'; ?>"/>
                                        </div>
                                    </div>
                                </li>

                                <li>
                                    <div class="form_grid_12">
                                        <label class="field_title"><?php if ($this->lang->line('admin_settings_pinterest_link') != '') echo stripslashes($this->lang->line('admin_settings_pinterest_link')); else echo 'Pinterest Link'; ?></label>
                                        <div class="form_input">
                                            <input name="pinterest" id="pinterest" type="text"  value="<?php if (isset($admin_settings->row()->pinterest)) echo $admin_settings->row()->pinterest; ?>" class="large tipTop" title="<?php if ($this->lang->line('admin_setting_enter_pinterest_url') != '') echo stripslashes($this->lang->line('admin_setting_enter_pinterest_url')); else echo 'Please enter the site pinterest url'; ?>"/>
                                        </div>
                                    </div>
                                </li>

                                <li>
                                    <div class="form_grid_12">
                                        <label class="field_title"><?php if ($this->lang->line('admin_settings_google_Link') != '') echo stripslashes($this->lang->line('admin_settings_google_Link')); else echo 'google+ Link'; ?></label>
                                        <div class="form_input">
                                            <input name="googleplus_link" id="googleplus_link" type="text"  value="<?php if (isset($admin_settings->row()->googleplus_link)) echo $admin_settings->row()->googleplus_link; ?>" class="large tipTop" title="<?php if ($this->lang->line('admin_setting_enter_google_url') != '') echo stripslashes($this->lang->line('admin_setting_enter_google_url')); else echo 'Please enter the site google+ url'; ?>"/>
                                        </div>
                                    </div>
                                </li>
								
								
								
								<li>
                                    <h3 class="head_drive"><?php if ($this->lang->line('admin_settings_facebook_app_login_credential') != '') echo stripslashes($this->lang->line('admin_settings_facebook_app_login_credential')); else echo 'Facebook App Login Credential'; ?></h3>
                                </li>
							
								<li>
								  <div class="form_grid_12">
									<label class="field_title"><?php if ($this->lang->line('admin_settings_facebook_app_id_android') != '') echo stripslashes($this->lang->line('admin_settings_facebook_app_id_android')); else echo 'Android facebook App ID'; ?></label>
									<div class="form_input">
									  <input name="facebook_app_id_android" id="facebook_app_id_android" type="text"  value="<?php if (isset($admin_settings->row()->facebook_app_id_android)) echo $admin_settings->row()->facebook_app_id_android;?>" class="large tipTop" title="<?php if ($this->lang->line('admin_setting_enter_facebook_app_id_android') != '') echo stripslashes($this->lang->line('admin_setting_enter_facebook_app_id_android')); else echo 'Please enter the facebook app id for android'; ?>"/>
									</div>
								  </div>
								</li>
								
								<li>
								  <div class="form_grid_12">
									<label class="field_title"><?php if ($this->lang->line('admin_settings_facebook_app_id') != '') echo stripslashes($this->lang->line('admin_settings_facebook_app_id')); else echo 'Facebook App ID'; ?></label>
									<div class="form_input">
									  <input name="facebook_app_id" id="facebook_app_id" type="text"  value="<?php if (isset($admin_settings->row()->facebook_app_id)) echo $admin_settings->row()->facebook_app_id;?>" class="large tipTop" title="<?php if ($this->lang->line('admin_setting_enter_facebook_app_id') != '') echo stripslashes($this->lang->line('admin_setting_enter_facebook_app_id')); else echo 'Please enter the facebook app id'; ?>"/>
									</div>
								  </div>
								</li>
								
								</ul>

							<ul class="rite-contsec">
								
							   <li>
								  <div class="form_grid_12">
									<label class="field_title"><?php if ($this->lang->line('admin_settings_facebook_app_secret') != '') echo stripslashes($this->lang->line('admin_settings_facebook_app_secret')); else echo 'Facebook App Secret'; ?></label>
									<div class="form_input">
									  <input name="facebook_app_secret" id="facebook_app_secret" type="text"  value="<?php if (isset($admin_settings->row()->facebook_app_secret)) echo $admin_settings->row()->facebook_app_secret;?>" class="large tipTop" title="<?php if ($this->lang->line('admin_setting_enter_facebook_facebook_secret') != '') echo stripslashes($this->lang->line('admin_setting_enter_facebook_facebook_secret')); else echo 'Please enter the facebook app secret'; ?>"/>
									</div>
								  </div>
								</li>
								
								
								
								
                                 <li>
                                    <div class="form_grid_12">
                                        <label class="field_title"><?php if ($this->lang->line('site_share_image') != '') echo stripslashes($this->lang->line('site_share_image')); else echo 'Website Share Image'; ?></label>
                                        <div class="form_input">
                                            <input name="facebook_image" id="facebook_image" type="file"  class="large tipTop" title="<?php if ($this->lang->line('admin_setting_choose_image') != '') echo stripslashes($this->lang->line('admin_setting_choose__image')); else echo 'Please Choose the image'; ?>"/>
                                        </div>
                                       <?php if(isset($admin_settings->row()->facebook_image) && $admin_settings->row()->facebook_image!=='') {?>
                                        <div class="form_input">
                                            <img src="<?php echo base_url(); ?>images/logo/<?php echo $admin_settings->row()->facebook_image; ?>" width="100px"/>
                                        </div>
                                      <?php }?>
                                    </div>
                                </li>
								
								<li>
                                    <h3 class="head_drive"><?php if ($this->lang->line('admin_settings_google_app_login_credential') != '') echo stripslashes($this->lang->line('admin_settings_google_app_login_credential')); else echo 'Google+ App Login Credential'; ?></h3>
                                </li>
								
								<li>
								  <div class="form_grid_12">
									<label class="field_title"><?php if ($this->lang->line('admin_settings_google_client_id') != '') echo stripslashes($this->lang->line('admin_settings_google_client_id')); else echo 'Google Client Id'; ?></label>
									<div class="form_input">
									  <input name="google_client_id" id="google_client_id" type="text"  value="<?php if (isset($admin_settings->row()->google_client_id)) echo $admin_settings->row()->google_client_id;?>" class="large tipTop" title="<?php if ($this->lang->line('admin_setting_enter_google_client_id') != '') echo stripslashes($this->lang->line('admin_setting_enter_google_client_id')); else echo 'Please enter the google client id'; ?>"/>
									</div>
								  </div>
								</li>
								
								 <li class="check-ip">
								  <div class="form_grid_12">
									<label class="field_title"><?php if ($this->lang->line('admin_settings_google_redirect_url') != '') echo stripslashes($this->lang->line('admin_settings_google_redirect_url')); else echo 'Google Redirect Url'; ?></label>
									<div class="form_input">
									  <input name="google_redirect_url" id="google_redirect_url" type="text"  value="<?php if (isset($admin_settings->row()->google_redirect_url)) echo $admin_settings->row()->google_redirect_url;?>" class="large tipTop" title="<?php if ($this->lang->line('admin_setting_enter_redirect_url') != '') echo stripslashes($this->lang->line('admin_setting_enter_redirect_url')); else echo 'Please enter the google redirect url'; ?>"/>
									  <label class="error"><?php if ($this->lang->line('admin_settings_google_redirect_url') != '') echo stripslashes($this->lang->line('admin_settings_google_redirect_url')); else echo 'Note: For Google Redirect Url Copy This Url and Paste It.'; ?> - <?php echo base_url();?><?php if ($this->lang->line('admin_settings_google_redirect') != '') echo stripslashes($this->lang->line('admin_settings_google_redirect')); else echo 'google-redirect'; ?> </label>
									</div>
								  </div>
								</li>
								
								<li>
								  <div class="form_grid_12">
									<label class="field_title"><?php if ($this->lang->line('admin_settings_google_secret_key') != '') echo stripslashes($this->lang->line('admin_settings_google_secret_key')); else echo 'Google Secret Key'; ?></label>
									<div class="form_input">
									  <input name="google_client_secret" id="google_client_secret" type="text"  value="<?php if (isset($admin_settings->row()->google_client_secret)) echo $admin_settings->row()->google_client_secret;?>" class="large tipTop" title="<?php if ($this->lang->line('admin_setting_google_secret_key') != '') echo stripslashes($this->lang->line('admin_setting_google_secret_key')); else echo 'Please enter the google secret key'; ?>"/>
									</div>
								  </div>
								</li>
								
								<li>
								  <div class="form_grid_12">
									<label class="field_title"><?php if ($this->lang->line('admin_settings_google_developer_key') != '') echo stripslashes($this->lang->line('admin_settings_google_developer_key')); else echo 'Google Developer Key'; ?></label>
									<div class="form_input">
									  <input name="google_developer_key" id="google_developer_key" type="text"  value="<?php if (isset($admin_settings->row()->google_developer_key)) echo $admin_settings->row()->google_developer_key;?>" class="large tipTop" title="<?php if ($this->lang->line('admin_setting_google_developer_key') != '') echo stripslashes($this->lang->line('admin_setting_google_developer_key')); else echo 'Please enter the google developer key'; ?>"/>
									</div>
								  </div>
								</li>
								
                            </ul>
                            <ul>
                                <li class="change-pass">
                                    <div class="form_grid_12">
                                        <div class="form_input">
                                            <button type="submit" class="btn_small btn_blue" ><span><?php if ($this->lang->line('admin_settings_submit') != '') echo stripslashes($this->lang->line('admin_settings_submit')); else echo 'Submit'; ?></span></button>
                                        </div>
                                    </div>
                                </li>
                            </ul>
                        </div>
                        </form>


                        <?php
                        $attributes = array('class' => 'form_container left_label', 'id' => 'settings_form');
                        echo form_open(ADMIN_ENC_URL.'/adminlogin/admin_global_settings', $attributes)
                        ?>
                        <input type="hidden" name="form_mode" value="seo"/>
                        <div id="tab3" class="first-section admin-sec-part sec-third-part">
                            <ul class="left-contsec">
                                <li>
                                    <h3 class="head_drive"><?php if ($this->lang->line('admin_settings_search_engine_information') != '') echo stripslashes($this->lang->line('admin_settings_search_engine_information')); else echo 'Search Engine Information'; ?></h3>
                                </li>

                                <li>
                                    <div class="form_grid_12">
                                        <label class="field_title"<?php if ($this->lang->line('admin_settings_meta_title') != '') echo stripslashes($this->lang->line('admin_settings_meta_title')); else echo 'Meta Title'; ?></label>
                                        <div class="form_input">
                                            <input name="meta_title" id="meta_title" type="text" value="<?php echo $admin_settings->row()->seo['meta_title']; ?>"  class="large tipTop" title="<?php if ($this->lang->line('admin_setting_enter_site_meta_title') != '') echo stripslashes($this->lang->line('admin_setting_enter_site_meta_title')); else echo 'Please enter the site meta title'; ?>" placeholder="<?php if ($this->lang->line('admin_settings_meta_title') != '') echo stripslashes($this->lang->line('admin_settings_meta_title')); else echo 'Meta Title'; ?>"/>
                                        </div>
                                    </div>
                                </li>

                                <li>
                                    <div class="form_grid_12">
                                        <label class="field_title"><?php if ($this->lang->line('admin_settings_meta_keyword') != '') echo stripslashes($this->lang->line('admin_settings_meta_keyword')); else echo 'Meta Keyword'; ?></label>
                                        <div class="form_input">
                                            <input name="meta_keyword" id="meta_keyword" type="text" value="<?php echo $admin_settings->row()->seo['meta_keyword']; ?>"  class="large tipTop" title="<?php if ($this->lang->line('admin_setting_enter_site_meta_keyword') != '') echo stripslashes($this->lang->line('admin_setting_enter_site_meta_keyword')); else echo 'Please enter the site meta keyword'; ?>" placeholder="<?php if ($this->lang->line('admin_settings_meta_keyword') != '') echo stripslashes($this->lang->line('admin_settings_meta_keyword')); else echo 'Meta Keyword'; ?>"/>
                                        </div>
                                    </div>
                                </li>
							
                                <li class="meta-descrip">
                                    <div class="form_grid_12">
                                        <label class="field_title"><?php if ($this->lang->line('admin_settings_meta_description') != '') echo stripslashes($this->lang->line('admin_settings_meta_description')); else echo 'Meta Description'; ?></label>
                                        <div class="form_input">
                                            <textarea name="meta_description" class="input_grow tipTop" cols="70" rows="5"  title="<?php if ($this->lang->line('admin_setting_enter_site_meta_description') != '') echo stripslashes($this->lang->line('admin_setting_enter_site_meta_description')); else echo 'Please enter the site meta description'; ?>" placeholder="<?php if ($this->lang->line('admin_settings_meta_description') != '') echo stripslashes($this->lang->line('admin_settings_meta_description')); else echo 'Meta Description'; ?>"><?php echo $admin_settings->row()->seo['meta_description']; ?></textarea>
                                        </div>
                                    </div>
                                </li>

                                <li>
                                    <h3 class="head_drive"><?php if ($this->lang->line('admin_settings_google_webmaster_info') != '') echo stripslashes($this->lang->line('admin_settings_google_webmaster_info')); else echo 'Google Webmaster Info'; ?></h3>
                                </li>
								
								</ul>
								
							<ul class="rite-contsec">

                                <li class="meta-descrip">
                                    <div class="form_grid_12">
                                        <label class="field_title"><?php if ($this->lang->line('admin_settings_google_analytics_code') != '') echo stripslashes($this->lang->line('admin_settings_google_analytics_code')); else echo 'Google Analytics Code'; ?></label>
                                        <div class="form_input">
                                            <textarea name="google_verification_code" class="input_grow tipTop" title="<?php if ($this->lang->line('admin_setting_copy_google_analytics_code') != '') echo stripslashes($this->lang->line('admin_setting_copy_google_analytics_code')); else echo 'Copy google analytics code and paste here'; ?>" placeholder="<?php if ($this->lang->line('admin_settings_google_analytics_code') != '') echo stripslashes($this->lang->line('admin_settings_google_analytics_code')); else echo 'Google Analytics Code'; ?>" cols="70" rows="5" ><?php echo $admin_settings->row()->seo['google_verification_code']; ?></textarea>
                                            <br />
                                            <span><?php if ($this->lang->line('admin_settings_for_examples') != '') echo stripslashes($this->lang->line('admin_settings_for_examples')); else echo 'For Examples'; ?>:
                                                <pre><?php echo htmlspecialchars('<script type="text/javascript>
	var _gaq = _gaq || [];
	_gaq.push([_setAccount, UA-XXXXX-Y]);
	_gaq.push([_trackPageview]);

	(function() {
		var ga = document.createElement(script); ga.type = text/javascript; ga.async = true;
		ga.src = (https: == document.location.protocol ? https://ssl : http://www) + .google-analytics.com/ga.js;
		var s = document.getElementsByTagName(script)[0]; s.parentNode.insertBefore(ga, s);
	})();

	</script>'); ?>
                                                </pre>
                                            </span> 
                                        </div>
                                    </div>
                                </li>
                                <li>
                                    <div class="form_grid_12">
                                        <label class="field_title"><?php if ($this->lang->line('admin_settings_google_html_meta_verifcation_code') != '') echo stripslashes($this->lang->line('admin_settings_google_html_meta_verifcation_code')); else echo 'Google HTML Meta Verification Code'; ?></label>
                                        <div class="form_input">
                                            <input name="google_verification" id="google_verification" value="<?php echo str_replace('"', "'", $admin_settings->row()->seo['google_verification']); ?>" type="text"  class="large tipTop" title="<?php if ($this->lang->line('admin_setting_google_html_verfication_code') != '') echo stripslashes($this->lang->line('admin_setting_google_html_verfication_code')); else echo 'Google HTMl Verification Code.'; ?> Eg: <meta name='google-site-verification' content='XXXXX'>" placeholder="<meta name='google-site-verification' content='XXXXX'>"/>
                                            <span>
                                                <br/>
                                               <?php if ($this->lang->line('admin_settings_google_webmaster_verification_tag') != '') echo stripslashes($this->lang->line('admin_settings_google_webmaster_verification_tag')); else echo 'Google Webmaster Verification using Meta tag.'; ?>  <br /><?php if ($this->lang->line('admin_settings_for_more_reference') != '') echo stripslashes($this->lang->line('admin_settings_for_more_reference')); else echo 'For more reference'; ?>: 
                                                <a href="https://support.google.com/webmasters/answer/35638#3" target="_blank">
                                                    https://support.google.com/webmasters/answer/35638#3
                                                </a>
                                            </span>
                                        </div>
                                    </div>
                                </li>
                            </ul>
                            <ul>
                                <li class="change-pass">
                                    <div class="form_grid_12">
                                        <div class="form_input">
                                            <button type="submit" class="btn_small btn_blue" ><span><?php if ($this->lang->line('admin_settings_submit') != '') echo stripslashes($this->lang->line('admin_settings_submit')); else echo 'Submit'; ?></span></button>
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
    </div>
    <span class="clear"></span> 
</div>
</div>

<script type="text/javascript">
    $(document).ready(function () {
        if ($("#publish1").attr("checked") == "checked") {
            $("#d_mode1").attr("checked", false);
            $("#d_mode2").attr("checked", false);
            $("#d_mode3").attr("checked", true);
            $("#d_mode4").attr("checked", false);
            $("li#dev_mode").hide();
        }
        $("#publish1").click(function () {
            $("#d_mode1").attr("checked", false);
            $("#d_mode2").attr("checked", false);
            $("#d_mode3").attr("checked", false);
            $("#d_mode4").attr("checked", false);
            $("li#dev_mode").hide();
        });
    });
					
</script>

<?php
$this->load->view(ADMIN_ENC_URL.'/templates/footer.php');
?>