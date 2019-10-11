<?php
$this->load->view('site/templates/common_header');
$pageFinder = $this->uri->segment(2);
?> 
</head>
<body>
   <!-------------header----------->
   <section class="header wow bounceInDown">
      <div class="container-fluid">
         <div class="row">
            <div class="col-md-4 col-lg-4">
               <div  class="menu">
                  <div id="wrapper">
                     <div class="overlay"></div>
                     <!-- Sidebar -->
                     <nav class="navbar navbar-inverse navbar-fixed-top" id="sidebar-wrapper" role="navigation">
                        <ul class="nav sidebar-nav">
                           <div class="nav_menu_header">
                              <a href="<?php echo base_url(); ?>">
                                 <li class="menu_logo"><img src="images/logo/<?php echo $this->config->item('logo_image'); ?>" alt="<?php echo $this->config->item('email_title'); ?>" title="<?php echo $this->config->item('email_title'); ?>"></li>
                              </a>
                              <li class="menu_user_login"><a href="login"><i class="fa fa-sign-in" aria-hidden="true"></i> <?php if ($this->lang->line('login_signin_ucfirst') != '') echo stripslashes($this->lang->line('login_signin_ucfirst')); else echo 'Sign In'; ?></a></li>
                           </div>
                           <a href="rider/signup">
                              <div class="ride_sign_up">
                                 <?php if ($this->lang->line('driver_sign_up_to_ride') != '') echo stripslashes($this->lang->line('driver_sign_up_to_ride')); else echo 'Sign up to ride'; ?>
                              </div>
                           </a>
                           <a href="driver/signup">
                              <div class="become_to_driver">
                                 <?php if ($this->lang->line('login_become_a_driver') != '') echo stripslashes($this->lang->line('login_become_a_driver')); else echo 'become a driver'; ?>
                              </div>
                           </a>
						   
						   <?php if($header_home == 'yes'){ ?>
								<li <?php if($pageFinder == '') echo 'class="active"';?>>
									<a href="<?php echo base_url(); ?>" <?php if($pageFinder == '') echo 'class="activemenu"';?>>
										<?php if ($this->lang->line('admin_settings_home') != '') echo stripslashes($this->lang->line('admin_settings_home')); else echo 'Home'; ?>
									</a>
								</li>
							<?php } ?>
							<?php foreach($header_menu as $menu){ $url = $menu['url']; ?>
							<li <?php if($pageFinder == $url) echo 'class="active"';?>>
								<a href="pages/<?php echo $url; ?>" <?php if($pageFinder == $url) echo 'class="activemenu"';?>>
									<?php echo $menu['name'] ?>
								</a>
							</li>
							<?php } ?>
						   
                        </ul>
                     </nav>
                     <!-- /#sidebar-wrapper -->
                     <!-- Page Content -->
                     <div id="page-content-wrapper">
                        <button type="button" class="hamburger is-closed" data-toggle="offcanvas">
                        <span class="hamb-top"></span>
                        <span class="hamb-middle"></span>
                        <span class="hamb-bottom"></span>
                        </button>
                     </div>
                     <!-- /#page-content-wrapper -->
                     <div class="menu"> <?php if ($this->lang->line('admin_settings_menu') != '') echo stripslashes($this->lang->line('admin_settings_menu')); else echo 'Menu'; ?></div>
                  </div>
               </div>
            </div>
            <div class="col-md-4 col-lg-4">
               <div class="logo"><a href="<?php echo base_url();?>"><img src="images/logo/<?php echo $this->config->item('logo_image'); ?>" alt="<?php echo $this->config->item('email_title'); ?>" title="<?php echo $this->config->item('email_title'); ?>"></a></div>
            </div>
            <div class="col-md-4 col-lg-4">
               <div class="reg_col">
                  <ul>
                     <li class="login"><a href="login"><i class="fa fa-lock" aria-hidden="true"></i> <?php if ($this->lang->line('home_login') != '') echo stripslashes($this->lang->line('home_login')); else echo 'LOG IN'; ?></a> </li>
                     <li class="register"><a href="signup"><i class="fa fa-user" aria-hidden="true"></i> <?php if ($this->lang->line('login_header_register') != '') echo stripslashes($this->lang->line('login_header_register')); else echo 'REGISTER'; ?></a> </li>
                     
					<!------------ Language Section ------------>
					 <?php if ($languageList->num_rows() > 1) {  ?>
					 <li class="get_app">
                        <div class="dropdown">
                           <button class="btn btn-primary dropdown-toggle" type="button" data-toggle="dropdown"><?= $langName ?>
                           <span class="caret"></span></button>
                           <ul class="dropdown-menu">
                              <?php foreach ($languageList->result() as $lang) {    ?>
								<li><a href="language-settings?q=<?php echo $lang->lang_code; ?>"><?php echo $lang->name; ?></a></li>
							  <?php } ?>
                           </ul>
                        </div>
                     </li>
					 
					   <?php } ?>
					 
                  </ul>
               </div>
            </div>
         </div>
      </div>
   </section>
	
	
