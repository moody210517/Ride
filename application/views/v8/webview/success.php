<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
	<head>
		<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
		<meta name="viewport" content="width=device-width, initial-scale=1.0" />
		<title><?php echo $this->config->item('email_title'); ?> - <?php if ($this->lang->line('mobile_view_payment_success') != '') echo stripslashes($this->lang->line('mobile_view_payment_success')); else echo 'Payment Successful'; ?></title>
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
						<h1><?php if ($this->lang->line('mobile_view_wallet_recharge') != '') echo stripslashes($this->lang->line('mobile_view_wallet_recharge')); else echo 'Wallet Recharge Successful'; ?> </h1>
						<div class="payment-success" style="padding: 20px 0;"><img src="<?php echo base_url(); ?>css/mobile/images/success.png" alt="success" title="<?php if ($this->lang->line('mobile_view_success') != '') echo stripslashes($this->lang->line('mobile_view_success')); else echo 'Success'; ?>" /></div>
						<?php if($trans_id != ''){ ?>
							<h3 class="payment-reference"><?php if ($this->lang->line('mobile_view_your_transaction_id') != '') echo stripslashes($this->lang->line('mobile_view_your_transaction_id')); else echo 'Your transaction reference id'; ?> : <b><?php echo $trans_id; ?></b></h3>
						<?php } ?>
					</div>			
					</div>	
			</div>
			<?php $this->output->set_header('refresh:5;url='.base_url().'v8/webview/wallet/completed'); ?>
		</section>
		<?php 
			}
		}
		?>
		<?php 
		if(isset($payOption)){
			if($payOption == 'ride payment'){		
		?>
		<section>
			<div class="shipping_address">
					<div class="main">		
						<div class="app-content-box">
						<h1><?php if ($this->lang->line('mobile_view_your_payment_sucess') != '') echo stripslashes($this->lang->line('mobile_view_your_payment_sucess')); else echo 'Your Payment Successful'; ?> <a class="close_btn" href="<?php echo base_url().'v8/webview/trip/pay-completed?mobileId='.$mobile_id; ?>"></a></h1>
						<div class="payment-success"><img src="<?php echo base_url(); ?>css/mobile/images/success.png" alt="success" title="<?php if ($this->lang->line('mobile_view_success') != '') echo stripslashes($this->lang->line('mobile_view_success')); else echo 'Success'; ?>" /></div>
					</div>			
					</div>	
			</div>
			<?php $this->output->set_header('refresh:2;url='.base_url().'v8/webview/trip/pay-completed?mobileId=' . $mobile_id); ?>
		</section>
		<?php 
			}
		}
		?>
		
		<?php 
		if($payOption == ''){		
		?>
		<section>
			<div class="shipping_address">
					<div class="main">		
						<div class="app-content-box">
						<h1><?php if ($this->lang->line('mobile_view_your_payment_sucess') != '') echo stripslashes($this->lang->line('mobile_view_your_payment_sucess')); else echo 'Your Payment Successful'; ?> <a class="close_btn" href="<?php echo base_url().'v8/webview/trip/pay-completed?mobileId='.$mobile_id; ?>"></a></h1>
						<div class="payment-success"><img src="<?php echo base_url(); ?>css/mobile/images/success.png" alt="success" title="<?php if ($this->lang->line('mobile_view_success') != '') echo stripslashes($this->lang->line('mobile_view_success')); else echo 'Success'; ?>" /></div>
					</div>			
					</div>	
			</div>
			<?php $this->output->set_header('refresh:2;url='.base_url().'v8/webview/trip/pay-completed?mobileId=' . $mobile_id); ?>
		</section>
		<?php 
		}
		?>
<style>
	.payment-reference{
		margin-bottom: 20px;
		margin-top: 0;
		text-align: center;
	}
</style>		
	</body>
</html>
