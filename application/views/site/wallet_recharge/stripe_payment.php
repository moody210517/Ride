<?php
$this->load->view('site/templates/profile_header');
?> 

<section class="favlocation profile_pic_sec row">
    <div  class="profile_login_cont">
        <?php $this->load->view('site/templates/profile_sidebar'); ?>
		<div class="share_detail">
			<div class="share_det_title">
				<h2><?php if ($this->lang->line('user_recharge') != '') echo stripslashes($this->lang->line('user_recharge')); else echo 'Recharge'; ?> <?php echo $siteTitle; ?> <?php if ($this->lang->line('user_money_ucfirst') != '') echo stripslashes($this->lang->line('user_money_ucfirst')); else  echo 'Money '; ?></h2>
			</div>

			

			<div class="profile_ac_inner_det">
				<div class="profile_ac_form favloc">
					<div class="col-md-11 pay-instructions">
                        <h2> <?php if ($this->lang->line('wallet_your_total_charge') != '') echo stripslashes($this->lang->line('wallet_your_total_charge')); else echo 'Your total charge is'; ?> <?php echo $this->config->item('currency_symbol') . ' ' . number_format($trans_details->row()->total_amount, 2); ?></h2>
                        <span><b><?php if ($this->lang->line('user_note') != '') echo stripslashes($this->lang->line('user_note')); else echo 'NOTE :'; ?></b> <?php if ($this->lang->line('wallet_your_card_information_saved') != '') echo stripslashes($this->lang->line('wallet_your_card_information_saved')); else echo 'Your card information will be saved in stripe secure gateway for your later and faster transaction.';
                            ?></span>
                        
						<?php
						$attributes = array('name' => 'wallet_recharge_form', 'id' => 'wallet_recharge_form','enctype' => 'multipart/form-data','method' => 'post');
						echo form_open('site/wallet_recharge/stripe_payment_process', $attributes);
						
						$tatalAmount = ($trans_details->row()->total_amount * 100);
						
		
						if ($this->lang->line('wallet_pay_with_your_card') != '') $pay_with = stripslashes($this->lang->line('wallet_pay_with_your_card')); else $pay_with = 'Pay With Your Card';
						$product_description = $siteTitle .' Money - Wallet Recharge';
						$newImgpathtoStripe = 'images/logo/' . $this->config->item('logo_image');
						$payment_btn_label = $pay_with;
						?>

						<input type="hidden" value="<?php echo $trans_details->row()->transaction_id; ?>" name="transaction_id" />
						<input type="hidden" value="<?php echo $trans_details->row()->user_id; ?>" name="user_id" />
						<input type="hidden" value="<?php echo $trans_details->row()->total_amount; ?>" name="total_amount" />
						<input type="hidden" value="<?php echo $rider_info->row()->email; ?>" name="email" />

						<input type="hidden" name="Stripeproduct_description" value="<?php echo $product_description; ?>"/>
						<script src="https://checkout.stripe.com/checkout.js" class="stripe-button"
								data-key="<?php echo $stripe_settings['settings']['publishable_key']; ?>"
								data-amount="<?php echo $tatalAmount; ?>" 
								data-billing-address ="false"
								data-image="<?php echo $newImgpathtoStripe; ?>"
								data-name="<?php echo $this->config->item('email_title'); ?>"data-email="<?php echo $rider_info->row()->email; ?>"
								data-label ="<?php echo $payment_btn_label; ?>"
								data-description="<?php echo $product_description; ?>">
						</script> 
						<br/>
						<img src="images/loader.gif" style="display:none;" id="payLoader">
					</div>
					
				</div>
			</div>
		</div>
	</div>
</section>	


<?php
$this->load->view('site/templates/footer');
?>