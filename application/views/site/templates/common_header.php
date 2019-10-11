<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
    <head>
        <meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
		<meta name="viewport" content="width=device-width, user-scalable=no">
		<meta name="viewport" content="width=device-width, initial-scale=1.0">
       
	   <?php  if ($sideMenu == 'share_code' || $sideMenu == 'share_earnings') { ?>
            <?php /* <meta property="og:site_name" content="<?php echo $this->config->item('email_title'); ?>"/> */ ?>
            <meta property="og:type" content="website"/>
            <meta property="og:url" content="<?php echo $_SERVER['HTTP_HOST'] . $_SERVER['REQUEST_URI']; ?>"/>
			<meta property="og:title" content="<?php if ($this->lang->line('signup_with_my_code') != '') echo stripslashes($this->lang->line('signup_with_my_code')); else echo 'Signup with my code.'; ?>"/>
            <?php if($this->config->item('facebook_image')!='') {?>
            <meta property="og:image" content="<?php echo base_url() . 'images/logo/'.$this->config->item('facebook_image'); ?>"/>
            <?php } else {?>
            <meta property="og:image" content="<?php echo base_url() . 'images/logo/'.$this->config->item('logo_image'); ?>"/>
            <?php }?>
            <meta property="og:image:width" content="100" />
            <meta property="og:image:height" content="100" />
            <meta property="og:description" content="<?php echo $shareDesc; ?>"/>
        <?php } ?>
        <meta name="Title" content="<?php if ($meta_title == '') { echo $this->config->item('meta_title'); } else { echo $meta_title; } ?>" />  
        <base href="<?php echo base_url(); ?>" />
        <?php
        if ($this->config->item('google_verification')) {
            echo stripslashes($this->config->item('google_verification'));
        }
        
        if ($heading == '') { ?>    
            <title><?php echo $title; ?></title>
        <?php } else { ?>
            <title><?php echo $heading; ?></title>
        <?php } ?>

        <meta name="Title" content="<?php if ($meta_title == '') { echo $this->config->item('meta_title'); } else { echo $meta_title; } ?>" />
        <meta name="keywords" content="<?php if ($meta_keyword == '') { echo $this->config->item('meta_keyword'); } else { echo $meta_keyword; } ?>" />
        <meta name="description" content="<?php if ($meta_description == '') { echo $this->config->item('meta_description'); } else { echo $meta_description; } ?>" />
		<?php
		if (isset($meta_abstraction)){
		  if ($meta_abstraction == '') {
			  echo "<!-- " . $this->config->item('meta_abstraction') . " --><cmt>";
		  } else {
			  echo "<!-- " . $meta_abstraction . " --><cmt>";
		  }
		}
		?>
        <link rel="shortcut icon" type="image/x-icon" href="<?php echo base_url() . 'images/logo/' . $this->config->item('favicon_image'); ?>">    

	  
		<?php $checkbox_lan=get_language_array_for_keyword($this->data['langCode']);?>
		
	
		<?php
		$this->load->view('site/templates/css_files', $this->data);
		$this->load->view('site/templates/validation_script');
		$this->load->view('site/templates/script_files', $this->data);
		?>

	<script src="js/jquery.growl.js" type="text/javascript"></script>
	<link href="css/jquery.growl.css" rel="stylesheet" type="text/css" />
	<?php if ($this->session->flashdata('sErrMSG') != '') { ?>
		<div></div>
		<script type="text/javascript">
	   
		var ErrorFlsh='<?php if ($this->lang->line('admin_error') != '') echo stripslashes($this->lang->line('admin_error')); else echo 'Error'; ?>';
		var SuccessFlsh='<?php if ($this->lang->line('admin_success') != '') echo stripslashes($this->lang->line('admin_success')); else echo 'Success'; ?>';
		<?php
		$sErrMSGdecoded = base64_decode($this->session->flashdata('sErrMSG'));
		if ($this->session->flashdata('sErrMSGType') == 'message-red') {
		?>
			$.growl.error({title: ErrorFlsh, message: "<?php echo $sErrMSGdecoded; ?>"});
		<?php } ?>
		<?php if ($this->session->flashdata('sErrMSGType') == 'message-green') { ?>
			$.growl.notice({title: SuccessFlsh, message: "<?php echo $sErrMSGdecoded; ?>"});
		<?php } ?>
		<?php if ($this->session->flashdata('sErrMSGType') == 'warning') { ?>
			$.growl.warning({message: "<?php echo $sErrMSGdecoded; ?>"});
		<?php } ?>
		</script>
	<?php } ?>
	
	
	<?php 
	if(isset($billing_job)){
		if($billing_job=='Yes'){ 
	?>
		<script>
			$.ajax({
				type:'post',
				url:'<?php echo base_url(); ?>generate-billing',
				data:{},
				complete:function(){
					//console.log('success');
				}
			});
		</script>
	<?php 
		}
	} 
	?>
    
    <?php  
    $this->load->view('site/templates/datetime_lang_script');
    ?>
	
