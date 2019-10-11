<?php
$this->load->view('site/templates/profile_header');
$profilePic = base_url() . USER_PROFILE_IMAGE_DEFAULT;
if (isset($rider_info->row()->image) && $rider_info->row()->image != '') {
    $profilePic = base_url() . USER_PROFILE_IMAGE . $rider_info->row()->image;
}
?>
<section class="profile_pic_sec row">
	<div class="profile_login_cont">
		<!-------Profile side bar ---->
		<?php
			$this->load->view('site/templates/profile_sidebar');
			?>
		<div class="share_detail cabilymoney">
			<div class="share_det_title">
				<h2><?php echo $siteTitle; ?> <?php if ($this->lang->line('user_money_ucfirst') != '') echo stripslashes($this->lang->line('user_money_ucfirst')); else echo 'Money'; ?></h2>
			</div>
			<div class="share_det_icon">
				<img src="images/site/moneyicon.png" />
			</div>
			<div class="cabily_amount">
				<p class="light"><?php if ($this->lang->line('user_cashless_hassle_free') != '') echo stripslashes($this->lang->line('user_cashless_hassle_free')); else echo 'Cashless, hassle-free rides with'; ?> <?php echo $siteTitle; ?> <?php if ($this->lang->line('user_money_ucfirst') != '') echo stripslashes($this->lang->line('user_money_ucfirst')); else echo 'Money'; ?></p>
				<h2><?php if ($this->lang->line('user_current_balance') != '') echo stripslashes($this->lang->line('user_current_balance')); else echo 'Current Balance'; ?> : <?php echo $dcurrencySymbol . number_format($wallet_balance,2); ?> </h2>
				<div class="transaction_btn"><a  href="rider/wallet-transactions" class="light"><?php if ($this->lang->line('site_user_cab_transactions') != '') echo stripslashes($this->lang->line('site_user_cab_transactions')); else echo 'Transactions'; ?></a></div>
			</div>
			<div class="share_det_know">
				<h2><?php if ($this->lang->line('user_recharge') != '') echo stripslashes($this->lang->line('user_recharge')); else echo 'Recharge'; ?> <?php echo $siteTitle; ?> <?php if ($this->lang->line('user_money_ucfirst') != '') echo stripslashes($this->lang->line('user_money_ucfirst')); else  echo 'Money '; ?></h2>
			</div>
			<div class="amount_inner_details">
				<div class="social">
					<?php
                        $wal_recharge_max_amount = $this->config->item('wal_recharge_max_amount');
                        $wal_recharge_min_amount = $this->config->item('wal_recharge_min_amount');

                        $divider = 10;

                        $addMoney1 = $wal_recharge_min_amount;
                        
                        $addMoney2 = ($wal_recharge_max_amount + $wal_recharge_min_amount) / 2 ;
                        $addMoney2 = (ceil($addMoney2));

                        $addMoney3 = $wal_recharge_max_amount;
                 
                        ?>
					<div id="checkboxes">
						<input type="radio" name="rGroup" value="1" id="r1" />
						<label class="whatever money_bucket" id="money_bucket1" data-bucket="<?php echo $addMoney1; ?>" ><?php echo $dcurrencySymbol . $addMoney1; ?></label>
						<input type="radio" name="rGroup" value="2" id="r2" />
						<label class="whatever money_bucket" id="money_bucket2" data-bucket="<?php echo $addMoney2; ?>" ><?php echo $dcurrencySymbol . $addMoney2; ?></label>
						<input type="radio" name="rGroup" value="3" id="r3" />
						<label class="whatever money_bucket" id="money_bucket3" data-bucket="<?php echo $addMoney3; ?>" ><?php echo $dcurrencySymbol . $addMoney3; ?></label>
					</div>
				</div>

                <input type="hidden" id="auto_charge_status" value="<?php echo $auto_charge; ?>" />
                <input type="hidden" id="wal_recharge_max_amount" value="<?php echo $wal_recharge_max_amount; ?>" />
                <input type="hidden" id="wal_recharge_min_amount" value="<?php echo $wal_recharge_min_amount; ?>" />

				<?php
                    $stripe_customer_id = '';
                    if (isset($rider_info->row()->stripe_customer_id)) {
                        $stripe_customer_id = $rider_info->row()->stripe_customer_id;
                    }
                    if ($auto_charge == 'Yes' && $stripe_customer_id != '') {
							$attributes = array('name' => 'wallet_recharge_form', 'id' => 'wallet_recharge_form','enctype' => 'multipart/form-data');
							echo form_open('site/wallet_recharge/stripe_payment_process', $attributes)
                        ?>
						
							<?php 
							if ($this->lang->line('user_enter_amount_between') != '') $placeholder = stripslashes($this->lang->line('user_enter_amount_between')); else $placeholder =  'Enter amount between';
							
							$placeholder = $placeholder.' '.$dcurrencySymbol . $wal_recharge_min_amount.' - '.$dcurrencySymbol . $wal_recharge_max_amount;
							
							$input_data = array(
											'name' => 'total_amount',
											'id' => 'total_amount',
											'type' => 'text',
											'class' => 'input_div amount_div',
											'placeholder' => $placeholder
							);
							echo form_input($input_data);
						
							
							$input_data = array(
											'name' => 'transaction_id',
											'type' => 'hidden'
							);
							echo form_input($input_data);
							
							$input_data = array(
											'name' => 'user_id',
											'type' => 'hidden',
											'value' => (string) $rider_info->row()->_id
							);
							echo form_input($input_data);
							
							$input_data = array(
											'name' => 'email',
											'type' => 'hidden',
											'value' => $rider_info->row()->email
							);
							echo form_input($input_data);
							?>
						
							
							<span class="Wallet_money_err error" id="Wallet_money_err"></span>
							<button type="button" class="rd_btn add_money_btn" id="payBtn" onclick="wallet_payment_amt_validate('auto');"><?php if ($this->lang->line('user_add') != '') echo stripslashes($this->lang->line('user_add')); else echo 'ADD'; ?> <?php if ($this->lang->line('user_money_from_your_card') != '') echo stripslashes($this->lang->line('user_money_from_your_card')); else echo 'MONEY FROM YOUR CARD'; ?></button>
							


					<?php
							echo form_close();
                    	} else {
							$attributes = array('name' => 'wallet_recharge_form', 'id' => 'wallet_recharge_form','enctype' => 'multipart/form-data');
							echo form_open('rider/wallet-recharge/pay-option', $attributes)
						?>
							
							<?php 
							
							if ($this->lang->line('user_enter_amount_between') != '') $placeholder = stripslashes($this->lang->line('user_enter_amount_between')); else $placeholder =  'Enter amount between';
							
							$placeholder = $placeholder.' '.$dcurrencySymbol . $wal_recharge_min_amount.' - '.$dcurrencySymbol . $wal_recharge_max_amount;
							
							$input_data = array(
											'name' => 'total_amount',
											'id' => 'total_amount',
											'type' => 'text',
											'class' => 'input_div amount_div',
											'placeholder' => $placeholder
							);
							echo form_input($input_data);
							?>
						
							<span class="Wallet_money_err error" id="Wallet_money_err"></span>
							<button type="button" class="rd_btn add_money_btn securityCheck" id="payBtn" onclick="wallet_payment_amt_validate('manual');"><?php if ($this->lang->line('user_add') != '') echo stripslashes($this->lang->line('user_add')); else echo 'ADD'; ?> <?php if ($this->lang->line('user_money') != '') echo stripslashes($this->lang->line('user_money')); else echo 'MONEY'; ?></button>
							
						<?php 
							echo form_close();
						}	
					?>

			</div>
		</div>
	</div>
</section>

<?php
	$this->load->view('site/templates/footer');
?> 