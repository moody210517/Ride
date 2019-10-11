<!DOCTYPE HTML>
<html>
<head>
<meta http-equiv="Content-Type" content="text/html; charset=UTF-8"/>
<meta name="viewport" content="width=device-width"/>
<base href="<?php echo base_url(); ?>">
<title><?php echo $title;?></title>
<link rel="shortcut icon" type="image/x-icon" href="<?php echo base_url();?>images/logo/<?php echo $favicon;?>">
<link href="css/reset.css" rel="stylesheet" type="text/css">
<link href="css/layout.css" rel="stylesheet" type="text/css">
<link href="css/themes.css" rel="stylesheet" type="text/css">
<link href="css/typography.css" rel="stylesheet" type="text/css">
<link href="css/styles.css" rel="stylesheet" type="text/css">
<link href="css/shCore.css" rel="stylesheet" type="text/css">
<link href="css/bootstrap.css" rel="stylesheet" type="text/css">
<link href="css/jquery.jqplot.css" rel="stylesheet" type="text/css">
<link href="css/jquery-ui-1.8.18.custom.css" rel="stylesheet" type="text/css">
<link href="css/data-table.css" rel="stylesheet" type="text/css">
<link href="css/form.css" rel="stylesheet" type="text/css">
<link href="css/ui-elements.css" rel="stylesheet" type="text/css">
<link href="css/wizard.css" rel="stylesheet" type="text/css">
<link href="css/sprite.css" rel="stylesheet" type="text/css">
<link href="css/gradient.css" rel="stylesheet" type="text/css">
<link href="css/developer.css" rel="stylesheet" type="text/css">
<link href="css/style-responsive.css" rel="stylesheet" type="text/css">
<?php
		
	$this->load->view('site/templates/validation_script');
		
?>
<script src="js/jquery-1.7.1.min.js"></script>
<script src="js/jquery-ui-1.8.18.custom.min.js"></script>
<script src="js/jquery.ui.touch-punch.js"></script>
<script src="js/chosen.jquery.js"></script>
<script src="js/uniform.jquery.js"></script>
<script src="js/bootstrap-dropdown.js"></script>
<script src="js/bootstrap-colorpicker.js"></script>
<script src="js/sticky.full.js"></script>
<script src="js/jquery.noty.js"></script>
<script src="js/selectToUISlider.jQuery.js"></script>
<script src="js/fg.menu.js"></script>
<script src="js/jquery.tagsinput.js"></script>
<script src="js/jquery.cleditor.js"></script>
<script src="js/jquery.tipsy.js"></script>
<script src="js/jquery.peity.js"></script>
<script src="js/jquery.simplemodal.js"></script>
<script src="js/jquery.jBreadCrumb.1.1.js"></script>
<script src="js/jquery.colorbox-min.js"></script>
<script src="js/jquery.idTabs.min.js"></script>
<script src="js/jquery.multiFieldExtender.min.js"></script>
<script src="js/jquery.confirm.js"></script>
<script src="js/elfinder.min.js"></script>
<script src="js/accordion.jquery.js"></script>
<script src="js/autogrow.jquery.js"></script>
<script src="js/check-all.jquery.js"></script>
<script src="js/data-table.jquery.js"></script>
<script src="js/ZeroClipboard.js"></script>
<script src="js/TableTools.min.js"></script>
<script src="js/jeditable.jquery.js"></script>
<script src="js/duallist.jquery.js"></script>
<script src="js/easing.jquery.js"></script>
<script src="js/full-calendar.jquery.js"></script>
<script src="js/input-limiter.jquery.js"></script>
<script src="js/inputmask.jquery.js"></script>
<script src="js/iphone-style-checkbox.jquery.js"></script>
<script src="js/meta-data.jquery.js"></script>
<script src="js/quicksand.jquery.js"></script>
<script src="js/raty.jquery.js"></script>
<script src="js/smart-wizard.jquery.js"></script>
<script src="js/stepy.jquery.js"></script>
<script src="js/treeview.jquery.js"></script>
<script src="js/ui-accordion.jquery.js"></script>
<script src="js/vaidation.jquery.js"></script>
<script src="js/mosaic.1.0.1.min.js"></script>
<script src="js/jquery.collapse.js"></script>
<script src="js/jquery.cookie.js"></script>
<script src="js/jquery.autocomplete.min.js"></script>
<script src="js/localdata.js"></script>
<script src="js/excanvas.min.js"></script>
<script src="js/jquery.jqplot.min.js"></script>
<script src="js/chart-plugins/jqplot.dateAxisRenderer.min.js"></script>
<script src="js/chart-plugins/jqplot.cursor.min.js"></script>
<script src="js/chart-plugins/jqplot.logAxisRenderer.min.js"></script>
<script src="js/chart-plugins/jqplot.canvasTextRenderer.min.js"></script>
<script src="js/chart-plugins/jqplot.canvasAxisTickRenderer.min.js"></script>
<script src="js/chart-plugins/jqplot.highlighter.min.js"></script>
<script src="js/chart-plugins/jqplot.pieRenderer.min.js"></script>
<script src="js/chart-plugins/jqplot.barRenderer.min.js"></script>
<script src="js/chart-plugins/jqplot.categoryAxisRenderer.min.js"></script>
<script src="js/chart-plugins/jqplot.pointLabels.min.js"></script>
<script src="js/chart-plugins/jqplot.meterGaugeRenderer.min.js"></script>
<script src="js/custom-scripts.js"></script>
<script type="text/javascript">
/*$(function(){
	$(window).resize(function(){
		$('.login_container').css({
			position:'absolute',
			left: ($(window).width() - $('.login_container').outerWidth())/2,
			top: ($(window).height() - $('.login_container').outerHeight())/2
		});
	});
	// To initially run the function:
	$(window).resize();
});*/
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

						<script src="js/jquery.growl.js" type="text/javascript"></script>
						<link href="css/jquery.growl.css" rel="stylesheet" type="text/css" />

						<?php if($this->session->flashdata('sErrMSG') != '') { ?>
						<script type="text/javascript">
								<?php 
								$sErrMSGdecoded = base64_decode($this->session->flashdata('sErrMSG'));
								$sErrMSGKeydecoded = base64_decode($this->session->flashdata('sErrMSGKey'));
								if($this->session->flashdata('sErrMSGType')=='message-red'){ ?>
								$.growl.error({ title:"<?php echo $sErrMSGKeydecoded; ?>",message: "<?php echo  $sErrMSGdecoded;  ?>" });
								<?php } ?>
								<?php if($this->session->flashdata('sErrMSGType')=='message-green'){ ?>
								$.growl.notice({ title:"<?php echo $sErrMSGKeydecoded; ?>",message: "<?php echo  $sErrMSGdecoded;  ?>"});
								<?php } ?>
								<?php if($this->session->flashdata('sErrMSGType')=='warning'){ ?>
								$.growl.warning({ message: "<?php echo  $sErrMSGdecoded;  ?>" });
								<?php } ?>
						</script>
						<?php } ?>
		
						<div class="admin-login-form">
						
							<?php $attributes = array('id' => 'reset_password'); echo form_open(OPERATOR_NAME.'/settings/reset_password',$attributes);  ?>
										<div class="login_form">										
												<input type="hidden" name="reset_id" value="<?php echo $reset_id; ?>" />
												<ul>
														<li class="login_user tipBot" title="<?php if ($this->lang->line('dash_operator_enter_new_password') != '') echo stripslashes($this->lang->line('dash_operator_enter_new_password')); else echo 'Please enter your new password'; ?>">
																<input name="new_password" class="required" type="password" placeholder="<?php if ($this->lang->line('form_validation_new_password') != '') echo stripslashes($this->lang->line('form_validation_new_password')); else echo 'New Password'; ?>" minlength="6">
														</li>
														<li class="login_pass tipTop" title="<?php if ($this->lang->line('dash_operator_confirm_new_password') != '') echo stripslashes($this->lang->line('dash_operator_confirm_new_password')); else echo 'Please confirm your password'; ?>">
																<input name="confirm_password" class="required" type="password" placeholder="<?php if ($this->lang->line('rider_profile_confirm_password_lower') != '') echo stripslashes($this->lang->line('rider_profile_confirm_password_lower')); else echo 'Confirm Password'; ?>" minlength="6">
														</li>
												</ul>
										</div><div class="clear"></div>
										<input type="submit" class="login_btn blue_lgel" value="<?php if ($this->lang->line('user_submit_upper') != '') echo stripslashes($this->lang->line('user_submit_upper')); else echo 'SUBMIT'; ?>">
								
						</div>
		   
		   
		   
	   </div>	
     </div>
 </div>
 
<script>
$(document).ready(function() {
	
	$("#reset_password").validate();
});
</script>

<style>

.login_form .error{
	background: #fff !important;
	color:#000 !important;
}
.login_form label.error{
	color: #e84c3d !important;
	background: transparent !important;
}
.admin-login-form .login_btn{
	margin:0 auto;
}
.admin-logo{
	margin: 40px auto 0px;
}
.login_form input[type="text"], .login_form input[type="password"], .forgot_pass input[type="text"]{
	background:#fff !important;
}



</style>
 
		</body>
</html>