<section class="footer_top ">  
	<div class="container">
		<div class="row">
			<div class="col-md-4 col-lg-4">
				<div class="keepintouch">
					<h2><?php if ($this->lang->line('login_footer_keep_in_touch') != '') echo stripslashes($this->lang->line('login_footer_keep_in_touch')); else echo 'Keep in touch'; ?></h2>
					<div class="head_border"></div>
					<ul>
						<?php if ($this->config->item('site_contact_address') != '') { ?>
						<li class="f_address">
							<span><?php echo $this->config->item('site_contact_address');?></span>
						</li>
						<?php  } ?>
						
						<?php if ($this->config->item('site_contact_mail') != '') { ?>
						<li class="f_mail">
							<span><?php echo $this->config->item('site_contact_mail');?></span>
						</li>
						<?php  } ?>
						
						<?php if ($this->config->item('customer_service_number') != '') { ?>
						<li class="f_number">
							<span><?php echo $this->config->item('customer_service_number');?></span>
						</li>
						<?php } ?>
					</ul>
				</div>
			</div>
			<div class="col-md-4 col-lg-4">
				<div class="footer_social_part">
					<div class="logo">
						<a href="<?php echo base_url(); ?>"><img src="images/logo/<?php echo $logo; ?>"></a>
					</div>
					<div class="download_app_part">
						<ul>
							<li class="footer_gooply">
								<a <?php if($user_playstore_link != '' ){ ?>href="<?php echo $user_playstore_link; ?>" target="_blank"<?php } ?>> <img src="images/site/footer_google_play.png"> </a>
							</li>
							<li class="footer_plystr">
								<a <?php if($user_appstore_link != '' ){ ?>href="<?php echo $user_appstore_link; ?>" target="_blank" <?php } ?>> <img src="images/site/footer_play_store.png"></a>
							</li>
						</ul>
					</div>
					<div class="footer_social_link">
						<ul>							
							<?php if ($this->config->item('facebook_link') != '') { ?>
							<li class="fb"><a href="<?php echo $this->config->item('facebook_link'); ?>">
								<img src="images/site/footer_social_icon2.png"></a>
							</li>
							<?php } ?>
							
							<?php if ($this->config->item('twitter_link') != '') { ?>
							<li class="twt"><a href="<?php echo $this->config->item('twitter_link'); ?>">
								<img src="images/site/footer_social_icon3.png"></a>
							</li>
							<?php } ?>
							
							<?php if ($this->config->item('googleplus_link') != '') { ?>
							<li class="gplas"><a href="<?php echo $this->config->item('googleplus_link'); ?>">
								<img src="images/site/footer_social_icon4.png"></a>
							</li>
							<?php } ?>
							
							<?php if ($this->config->item('youtube_link') != '') { ?>
							<li class="utube"><a href="<?php echo $this->config->item('youtube_link'); ?>">
								<img src="images/site/footer_social_icon5.png"></a>
							</li>
							<?php  } ?>
							
							<?php if ($this->config->item('linkedin_link') != '') { ?>
							<li class="in"><a href="<?php echo $this->config->item('linkedin_link'); ?>">
								<img src="images/site/footer_social_icon6.png"></a>
							</li>
							<?php  } ?>
							
							<?php if ($this->config->item('instagram_link') != '') { ?>
							<li class="gram"><a href="<?php echo $this->config->item('instagram_link'); ?>">
								<img src="images/site/footer_social_icon7.png"></a>
							</li>
							<?php  } ?>							
						</ul>
					</div>
				</div>
			</div>
			<div class="col-md-4 col-lg-4">
				<div class="footer_information">
				<h2><?php if ($this->lang->line('login_footer_information') != '') echo stripslashes($this->lang->line('login_footer_information')); else echo 'INFORMATION'; ?></h2>
				<div class="head_border"></div>
					<ul>
						<?php if($footer_home == 'yes'){ ?>
						  <li><a href="<?php echo base_url(); ?>">  <?php if ($this->lang->line('admin_settings_home') != '') echo stripslashes($this->lang->line('admin_settings_home')); else echo 'Home'; ?></a></li>
						<?php } ?>
						<?php foreach($footer_menu as $menu){ $url = $menu['url']; ?>
                        <li><a href="<?php echo base_url(); ?>pages/<?php echo $url; ?>"><?php echo $menu['name'] ?></a></li>
                       <?php } ?>
					</ul>
				</div>
			</div>
		</div>
	</div>
</section>
<section class="footer_bottom">
	<div class="container">
		<div class="col-md-12 col-lg-12">
			<p><?php echo $this->config->item('footer_content'); ?></p>
		</div>
	</div>
</section>


<script>
	$(".chzn-select").chosen();
</script>

<?php echo $this->config->item('google_verification_code'); ?>
</body>
</html>