<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
	<head>
		<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
		<meta name="viewport" content="width=device-width, initial-scale=1.0" />
		<title><?php echo $this->config->item('email_title'); ?> - <?php if ($this->lang->line('payment_failed') != '') echo stripslashes($this->lang->line('payment_failed')); else echo 'Payment Failed'; ?></title>
		<link rel="stylesheet" href="<?php echo base_url(); ?>css/mobile/app-style.css" type="text/css" media="all" />
	</head>
	<body>
	
	<?php 
		if(isset($payOption)){
			if($payOption == 'wallet recharge'){		
				?>
		<section>
			<div class="shipping_address">
					<div class="main">		
						<div class="app-content-box">
						<h1><?php if ($this->lang->line('payment_our_payment_failed') != '') echo stripslashes($this->lang->line('payment_our_payment_failed')); else echo 'Your Payment Failed'; ?> - <?php echo urldecode($errors); ?><a class="close_btn" href="<?php echo base_url().'v8/webview/wallet/failed'; ?>"></a></h1>
						<div class="payment-success"><img src="<?php echo base_url(); ?>css/mobile/images/failed.png" alt="failed" title="<?php if ($this->lang->line('payment_tittle_failed') != '') echo stripslashes($this->lang->line('payment_tittle_failed')); else echo 'Failed'; ?>" /></div>
					</div>			
					</div>	
			</div>
			<?php $this->output->set_header('refresh:5;url='.base_url().'v8/webview/wallet/failed'); ?>
		</section>
		<?php 
			}
		} else {
		?>
		<section>
			<div class="shipping_address">
					<div class="main">		
						<div class="app-content-box">
						<h1><?php if ($this->lang->line('payment_our_payment_failed') != '') echo stripslashes($this->lang->line('payment_our_payment_failed')); else echo 'Your Payment Failed'; ?> - <?php echo urldecode($errors); ?><a class="close_btn" href="<?php echo base_url().'v8/webview/trip/cancelled?mobileId='.$mobileId; ?>"></a></h1>
						<div class="payment-success"><img src="<?php echo base_url(); ?>css/mobile/images/failed.png" alt="failed" title="<?php if ($this->lang->line('payment_tittle_failed') != '') echo stripslashes($this->lang->line('payment_tittle_failed')); else echo 'Failed'; ?>" /></div>
					</div>			
					</div>	
			</div>
			<?php $this->output->set_header('refresh:2;url='.base_url().'v8/webview/trip/cancelled?mobileId='.$mobileId); ?>
		</section>
		
		<?php } ?>
		
	</body>
</html>
