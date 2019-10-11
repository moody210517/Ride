<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
	<head>
		<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
		<meta name="viewport" content="width=device-width, initial-scale=1.0" />
		<title><?php echo $this->config->item('email_title'); ?></title>
		<link rel="stylesheet" href="<?php echo base_url(); ?>css/mobile/app-style.css" type="text/css" media="all" />
	</head>
	<body>
		<section>
			<div class="app-content">
				<div class="main">
					<div class="app-content-box">
						<h1><?php if ($this->lang->line('payment_error_page_content') != '') echo stripslashes($this->lang->line('payment_error_page_content')); else echo 'Your requested page could not be found or it is currently unavailable'; ?>.</h1>
					</div>
				</div>
			</div>
		</section>
	</body>
</html>
