<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
	<head>
		<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
		<meta name="viewport" content="width=device-width, initial-scale=1.0" />
		<title><?php echo $this->config->item('email_title'); ?> - <?php if ($this->lang->line('wallet_payment_card') != '') echo stripslashes($this->lang->line('wallet_payment_card')); else echo 'Payment Credit Card'; ?></title>
		<link rel="stylesheet" href="<?php echo base_url(); ?>css/mobile/app-style.css" type="text/css" media="all" />
		<script type="text/javascript" src="<?php echo base_url();?>js/site/jquery-1.12.4.js"></script>
	</head>
	<body>
		<section>
			<div class="shipping_address">
				<div class="main">		
					<div class="app-content-box">
					
						<h1><?php if ($this->lang->line('wallet_complete_recharge') != '') echo stripslashes($this->lang->line('wallet_complete_recharge')); else echo 'Complete Recharge'; ?> <a class="close_btn" href="<?php echo base_url(); ?>v8/webview/wallet/cancel?mobileId=<?php echo $trans_details->row()->transaction_id;?>"></a></h1>
						
						<h2 style="margin-left:15px;"><?php if ($this->lang->line('wallet_your_total_charge') != '') echo stripslashes($this->lang->line('wallet_your_total_charge')); else echo 'Your total charge is'; ?><?php echo $this->config->item('currency_symbol').' '.number_format($trans_details->row()->total_amount,2); ?></h2>
						
						<span class="payNote" style="margin-left:15px;"><b><?php if ($this->lang->line('stripe_NOTE') != '') echo stripslashes($this->lang->line('stripe_NOTE')); else echo 'NOTE'; ?> : </b>  <?php if ($this->lang->line('wallet_your_card_information_saved') != '') echo stripslashes($this->lang->line('wallet_your_card_information_saved')); else echo 'Your card information will be saved in stripe secure gateway for your later and faster transaction'; ?></span>
						
						<form name="wallet_recharge_form" id="wallet_recharge_form" method="post" enctype="multipart/form-data" action="<?php echo base_url(); ?>v8/api/wallet_payment/stripe_payment_process" autocomplete="off" onsubmit="return showLoader();>
						
						<?php 
						$tatalAmount  = ($trans_details->row()->total_amount * 100);
						$product_description = ucfirst($this->config->item('email_title')).' money - Wallet Recharge ';
						$newImgpathtoStripe = base_url().'images/logo/'.$this->config->item('logo_image');
						$payment_btn_label = 'Pay With Your Card' ;
						?>
						
						<input type="hidden" value="<?php echo $trans_details->row()->transaction_id;?>" name="transaction_id" />
						<input type="hidden" value="<?php echo $trans_details->row()->user_id;?>" name="user_id" />
						<input type="hidden" value="<?php echo $trans_details->row()->total_amount;?>" name="total_amount" />
						
						<ul>
							<li><input type="text" class="input-scroll-3" placeholder="<?php if ($this->lang->line('stripe_enter_card_number') != '') echo stripslashes($this->lang->line('stripe_enter_card_number')); else echo 'Card number'; ?>" id="cardNumber" name="card_number" maxlength="16" size="16"></input></li>
							
							<li><label><?php if ($this->lang->line('wallet_expiration') != '') echo stripslashes($this->lang->line('wallet_expiration')); else echo 'Expiration'; ?></label> 
								<?php $Sel ='selected="selected"';  $Sel ='';  ?>
								<select id="CCExpDay" name="exp_month" class="input-scroll-2">
								<option value="01" <?php if(get_time_to_string('m')=='01'){ echo $Sel;} ?>>01</option>
								<option value="02" <?php if(get_time_to_string('m')=='02'){ echo $Sel;} ?>>02</option>
								<option value="03" <?php if(get_time_to_string('m')=='03'){ echo $Sel;} ?>>03</option>
								<option value="04" <?php if(get_time_to_string('m')=='04'){ echo $Sel;} ?>>04</option>
								<option value="05" <?php if(get_time_to_string('m')=='05'){ echo $Sel;} ?>>05</option>
								<option value="06" <?php if(get_time_to_string('m')=='06'){ echo $Sel;} ?>>06</option>
								<option value="07" <?php if(get_time_to_string('m')=='07'){ echo $Sel;} ?>>07</option>
								<option value="08" <?php if(get_time_to_string('m')=='08'){ echo $Sel;} ?>>08</option>
								<option value="09" <?php if(get_time_to_string('m')=='09'){ echo $Sel;} ?>>09</option>
								<option value="10" <?php if(get_time_to_string('m')=='10'){ echo $Sel;} ?>>10</option>
								<option value="11" <?php if(get_time_to_string('m')=='11'){ echo $Sel;} ?>>11</option>
								<option value="12" <?php if(get_time_to_string('m')=='12'){ echo $Sel;} ?>>12</option>
								</select>
								<select id="CCExpMnth" name="exp_year" class="input-scroll-2"> 
									<?php for($i=get_time_to_string('Y');$i< (get_time_to_string('Y') + 30);$i++){ ?>
									<option value="<?php echo $i; ?>"><?php echo $i; ?></option>
									<?php } ?>
								</select>
							</li>
							
							<li><input type="password" class="input-scroll" placeholder="<?php if ($this->lang->line('wallet_security_code') != '') echo stripslashes($this->lang->line('wallet_security_code')); else echo 'Security Code'; ?>" id="creditCardIdentifier" name="cvc_number"></input></li>
							
							<li class="last"><input type="submit" id="submit_btn" class="input-submit-btn" value="<?php if ($this->lang->line('wallet_pay_with_your_card') != '') echo stripslashes($this->lang->line('wallet_pay_with_your_card')); else echo 'Pay With Your Card'; ?>" onClick="return validatecard();"></input></li>
							
							<li class="last"><p id="loading" class="input-loading" style="display:none;"><?php if ($this->lang->line('wallet_your_card_information_saved') != '') echo stripslashes($this->lang->line('wallet_your_card_information_saved')); else echo 'This page redirect automatically, please wait'; ?>...</p></li>
							<li class="last"><img src="images/loader.gif" style="display:none;" id="payLoader"></li>
						</ul>
						</form>
					</div>
				</div>	
			</div>
		</section>
		<script type="text/javascript">
		function validatecard(){
			var cardNumber=document.getElementById("cardNumber").value.trim();
			var CCExpDay=document.getElementById("CCExpDay").value.trim();
			var CCExpMnth=document.getElementById("CCExpMnth").value.trim();
			var creditCardIdentifier=document.getElementById("creditCardIdentifier").value.trim();
			//var cardType=document.getElementById("cardType").value.trim();
			
			document.getElementById("cardNumber").classList.remove("txt-error");
			document.getElementById("CCExpDay").classList.remove("txt-error");
			document.getElementById("CCExpMnth").classList.remove("txt-error");
			document.getElementById("creditCardIdentifier").classList.remove("txt-error");
			//document.getElementById("cardType").classList.remove("txt-error");
			
			var status=0;
			if(cardNumber=="" || isNaN(cardNumber)){
				document.getElementById("cardNumber").classList.add("txt-error");
				status++;
			}
			if(CCExpDay==""){
				document.getElementById("CCExpDay").classList.add("txt-error");
				status++;
			}
			if(CCExpMnth==""){
				document.getElementById("CCExpMnth").classList.add("txt-error");
				status++;
			}
			if(CCExpMnth=="<?php echo get_time_to_string('Y') ?>" && CCExpDay<"<?php echo get_time_to_string('m') ?>"){
				document.getElementById("CCExpDay").classList.add("txt-error");
				status++;
			}
			if(creditCardIdentifier==""){
				document.getElementById("creditCardIdentifier").classList.add("txt-error");
				status++;
			}
			/* if(cardType==""){
				document.getElementById("cardType").classList.add("txt-error");
				status++;
			} */
			if(status!=0){
				return false;
			}else{
				document.getElementById("submit_btn").style.display = 'none';
				document.getElementById("loading").style.display = 'block';
			}
		}
		</script>
		
		<script>
		function showLoader(){
			$('#payLoader').css('display','block'); 
		}
	</script>
	</body>
</html>
