<!DOCTYPE HTML>
<html>
<head>
<meta http-equiv="Content-Type" content="text/html; charset=UTF-8"/>
<meta name="viewport" content="width=device-width"/>
<base href="<?php echo base_url(); ?>">
<title><?php echo $title;?></title>
<link rel="shortcut icon" type="image/x-icon" href="<?php echo base_url();?>images/logo/<?php echo $favicon;?>">
<link href="css/reset.css" rel="stylesheet" type="text/css">
<link href="css/typography.css" rel="stylesheet" type="text/css">
<link href="css/styles.css" rel="stylesheet" type="text/css">
<link href="css/bootstrap.css" rel="stylesheet" type="text/css">
<link href="css/jquery-ui-1.8.18.custom.css" rel="stylesheet" type="text/css">
<link href="css/gradient.css" rel="stylesheet" type="text/css">
<link href="css/developer.css" rel="stylesheet" type="text/css">
<link href="css/style-responsive.css" rel="stylesheet" type="text/css">
<link href="css/developer_colors.css" rel="stylesheet" type="text/css">
<?php 
$this->load->view('site/templates/validation_script');
?>
<script src="js/jquery-1.7.1.min.js"></script>
<script src="js/jquery-ui-1.8.18.custom.min.js"></script>

<script src="js/chosen.jquery.js"></script>
<script src="js/uniform.jquery.js"></script>
<script src="js/jquery.tagsinput.js"></script>
<script src="js/jquery.cleditor.js"></script>
<script src="js/jquery.jBreadCrumb.1.1.js"></script>
<script src="js/accordion.jquery.js"></script>
<script src="js/autogrow.jquery.js"></script>
<script src="js/duallist.jquery.js"></script>
<script src="js/input-limiter.jquery.js"></script>
<script src="js/inputmask.jquery.js"></script>
<script src="js/iphone-style-checkbox.jquery.js"></script>
<script src="js/raty.jquery.js"></script>
<script src="js/stepy.jquery.js"></script>
<script src="js/vaidation.jquery.js"></script>
<script src="js/jquery.collapse.js"></script>

<script src="js/bootstrap-dropdown.js"></script>
<script src="js/bootstrap-colorpicker.js"></script>
<script src="js/jquery.tipsy.js"></script>
<script type="text/javascript">

function hideErrDiv(arg) {
    document.getElementById(arg).style.display = 'none';
}
</script>
</head>
<body id="theme-default" class="full_block">

 <div class="admin-login-wrap">
   <div class="admin-login-outer-wrap">
     <div class="admin-login-inner-wrap">
       
	   <a class="admin-logo" href="<?php echo base_url();?>">  <img src="images/logo/<?php echo $this->config->item('logo_image'); ?>" width="" alt="<?php echo $siteTitle;?>" title="<?php echo $siteTitle;?>"> </a>
		 
		 
		 <?php if (validation_errors() != ''){?>
		<div id="validationErr">
			<script>setTimeout("hideErrDiv('validationErr')", 3000);</script>
			<p><?php echo validation_errors();?></p>
		</div>
		<?php }?>
		 	<?php	/* if($this->session->flashdata('sErrMSG') != '') { ?>
                <div class="errorContainer" id="<?php echo $this->session->flashdata('sErrMSGType'); ?>">
                  <script>setTimeout("hideErrDiv('<?php echo $this->session->flashdata('sErrMSGType'); ?>')", 5000);</script>
                  <p><span> <?php echo $this->session->flashdata('sErrMSG');  ?> </span></p>
                </div>
   		 <?php } */ ?>
					 <script src="js/jquery.growl.js" type="text/javascript"></script>
					<link href="css/jquery.growl.css" rel="stylesheet" type="text/css" />
					<?php if($this->session->flashdata('sErrMSG') != '') { ?>
			<script type="text/javascript">
			var admin_error='<?php if ($this->lang->line('admin_error') != '') echo stripslashes($this->lang->line('admin_error')); else echo 'Error'; ?>';
			var Success='<?php if ($this->lang->line('admin_success') != '') echo stripslashes($this->lang->line('admin_success')); else echo 'Success'; ?>';
			  <?php 
			  $sErrMSGdecoded = base64_decode($this->session->flashdata('sErrMSG'));
			  if($this->session->flashdata('sErrMSGType')=='message-red'){ ?>
			  $.growl.error({ title:admin_error,message: "<?php echo  $sErrMSGdecoded;  ?>" });
			  <?php } ?>
			  <?php if($this->session->flashdata('sErrMSGType')=='message-green'){ ?>
			  $.growl.notice({ title:Success,message: "<?php echo  $sErrMSGdecoded;  ?>"});
			  <?php } ?>
			  <?php if($this->session->flashdata('sErrMSGType')=='warning'){ ?>
			  $.growl.warning({ message: "<?php echo  $sErrMSGdecoded;  ?>" });
			  <?php } ?>
			</script>
			<?php } ?>
		   
		   <div class="admin-login-form">
		<?php echo form_open(ADMIN_ENC_URL.'/adminlogin/admin_login');  ?>
			
				<!--<h3 class=""><?php if ($this->lang->line('admin_login_login_into_your_Account') != '') echo stripslashes($this->lang->line('admin_login_login_into_your_Account')); else echo 'Login into your Account'; ?></h3>-->
				
					<div class="admin-username" title="<?php if ($this->lang->line('admin_enter_your_username') != '') echo stripslashes($this->lang->line('admin_enter_your_username')); else echo 'Please enter your username'; ?>">
					<img src="images/adminuser.png">
					<input name="admin_name" autocomplete="off" value="" type="text" placeholder="<?php if ($this->lang->line('admin_users_users_list_user_name') != '') echo stripslashes($this->lang->line('admin_users_users_list_user_name')); else echo 'User Name'; ?>" >
					</div>
					<div class="admin-password" title="<?php if ($this->lang->line('admin_enter_your_password') != '') echo stripslashes($this->lang->line('admin_enter_your_password')); else echo 'Please enter your password'; ?>">
					<img src="images/adminlock.png">
					<input name="admin_password" type="password" value="" placeholder="<?php if ($this->lang->line('admin_subadmin_password') != '') echo stripslashes($this->lang->line('admin_subadmin_password')); else echo 'Password'; ?>">
					</div>
				
			
			
			<input class="login_btn blue_lgel" name="" value="<?php if ($this->lang->line('driver_login_ucfirst') != '') echo stripslashes($this->lang->line('driver_login_ucfirst')); else echo 'Login'; ?>" type="submit">
			<ul class="login_opt_link" style="float:left;width:100%;">
				<li style=" margin: 6px 0 0;"><a href="<?php echo ADMIN_ENC_URL;?>/adminlogin/admin_forgot_password_form" class="tipLeft"><?php if ($this->lang->line('admin_login_forgot_password') != '') echo stripslashes($this->lang->line('admin_login_forgot_password')); else echo 'Forgot Password'; ?>?</a></li>
				<li class="remember_me right tipBot" title="Select to remember your password upto one day" style="display:none">
				<input name="remember" class="rem_me" type="checkbox" value="checked">
				<?php if ($this->lang->line('admin_login_rember_me') != '') echo stripslashes($this->lang->line('admin_login_rember_me')); else echo 'Remember Me'; ?></li>
			</ul>
		</form>
		</div>
		   
		   
		   
	   </div>	
     </div>
 </div>





</body>
</html>