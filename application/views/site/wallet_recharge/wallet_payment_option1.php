<?php
$this->load->view('site/templates/profile_header');
?> 

<div class="rider-signup">
    <div class="container-new">
        <section>
            <div class="col-md-12 profile_rider">
                <?php
                $this->load->view('site/templates/profile_sidebar');
                ?>
                <div class="col-md-9 profile_rider_right">
				
					
						<?php  
							$payOption = 0;
							$cardActive = ''; 
							$paypalActive = '';
							if ($authorize_net_settings['status'] == 'Enable') {
								$cardActive = 'active';
								$payOption++;
							} else if ($paypal_settings['status'] == 'Enable'){
								$paypalActive = 'active';
								$payOption++;
							}              
						?>
				
					<?php 
					if($payOption > 0){
					?>
                    <div>
                        <!-- Nav tabs -->
                         <ul class="nav nav-tabs rider_profile-tab" role="tablist">
                            <?php if ($authorize_net_settings['status'] == 'Enable') { ?>
                                <li role="presentation" class="<?php echo $cardActive; ?>"><a href="#card" aria-controls="ride" role="tab" data-toggle="tab"><?php if ($this->lang->line('wallet_card') != '') echo stripslashes($this->lang->line('wallet_card')); else echo 'Card'; ?></a></li>
                            <?php } ?>

                            <?php if ($paypal_settings['status'] == 'Enable') { ?>
                                <li role="presentation" class="<?php echo $paypalActive; ?>"><a href="#paypal" aria-controls="upcoming" role="tab" data-toggle="tab"><?php if ($this->lang->line('wallet_paypal') != '') echo stripslashes($this->lang->line('wallet_paypal')); else echo 'Paypal'; ?></a></li>
                            <?php } ?>
                        </ul>

                        <!-- Tab panes -->
                        <div class="tab-content">
                            <div role="tabpanel" class="tab-pane <?php echo $cardActive; ?>" id="card">
                                <form name="PaymentCard" id="PaymentCard" method="post" enctype="multipart/form-data" action="site/wallet_recharge/user_wal_PaymentCard" >
                                    <div class="form-group">
                                        <label><?php if ($this->lang->line('wallet_card_type') != '') echo stripslashes($this->lang->line('wallet_card_type')); else echo 'Card Type'; ?></label>
                                        <select id="cardType" name="cardType">
                                            <option value=""><?php if ($this->lang->line('wallet_select_card_type') != '') echo stripslashes($this->lang->line('wallet_select_card_type')); else echo 'Select Card Type'; ?></option>
                                            <option value="american_express"><?php if ($this->lang->line('wallet_american_express') != '') echo stripslashes($this->lang->line('wallet_american_express')); else echo 'American Express'; ?></option>
                                            <option value="visa"><?php if ($this->lang->line('wallet_visa') != '') echo stripslashes($this->lang->line('wallet_visa')); else echo 'Visa'; ?></option>
                                            <option value="master_card"><?php if ($this->lang->line('wallet_master_card') != '') echo stripslashes($this->lang->line('wallet_master_card')); else echo 'Master Card'; ?></option>
                                            <option value="discover"><?php if ($this->lang->line('wallet_discover') != '') echo stripslashes($this->lang->line('wallet_discover')); else echo 'Discover  '; ?></option>
                                        </select>
                                    </div>

                                    <div class="form-group">
                                        <label><?php if ($this->lang->line('user_credit_card_number') != '') echo stripslashes($this->lang->line('user_credit_card_number')); else echo 'Credit Card Number'; ?></label>
                                        <input type="text" class="form-control input-scroll-3" placeholder="<?php if ($this->lang->line('wallet_card_number') != '') echo stripslashes($this->lang->line('wallet_card_number')); else echo 'Card number'; ?>" id="cardNumber" name="cardNumber" maxlength="16" size="16"></input>
                                    </div>

                                    <div class="form-group">
                                        <label><?php if ($this->lang->line('user_expiration_date') != '') echo stripslashes($this->lang->line('user_expiration_date')); else echo 'Expiration Date'; ?></label>
                                        <?php $Sel = 'selected="selected"'; ?>
                                        <select id="CCExpDay" name="CCExpDay">
                                            <option value="01" <?php if (get_time_to_string('m') == '01') { echo $Sel; } ?>>01</option>
                                            <option value="02" <?php if (get_time_to_string('m') == '02') { echo $Sel; } ?>>02</option>
                                            <option value="03" <?php if (get_time_to_string('m') == '03') { echo $Sel; } ?>>03</option>
                                            <option value="04" <?php if (get_time_to_string('m') == '04') { echo $Sel; } ?>>04</option>
                                            <option value="05" <?php if (get_time_to_string('m') == '05') { echo $Sel; } ?>>05</option>
                                            <option value="06" <?php if (get_time_to_string('m') == '06') { echo $Sel; } ?>>06</option>
                                            <option value="07" <?php if (get_time_to_string('m') == '07') { echo $Sel; } ?>>07</option>
                                            <option value="08" <?php if (get_time_to_string('m') == '08') { echo $Sel; } ?>>08</option>
                                            <option value="09" <?php if (get_time_to_string('m') == '09') { echo $Sel; } ?>>09</option>
                                            <option value="10" <?php if (get_time_to_string('m') == '10') { echo $Sel; } ?>>10</option>
                                            <option value="11" <?php if (get_time_to_string('m') == '11') { echo $Sel; } ?>>11</option>
                                            <option value="12" <?php if (get_time_to_string('m') == '12') { echo $Sel; } ?>>12</option>
                                        </select>
                                        <select id="CCExpMnth" name="CCExpMnth"> 
                                            <?php for ($i = get_time_to_string('Y'); $i < (get_time_to_string('Y') + 30); $i++) { ?>
                                                <option value="<?php echo $i; ?>"><?php echo $i; ?></option>
                                            <?php } ?>
                                        </select>
                                    </div>
                                    <div class="form-group">
                                        <label><?php if ($this->lang->line('wallet_security_code') != '') echo stripslashes($this->lang->line('wallet_security_code')); else echo 'Security Code'; ?></label>
                                        <input type="password" class="form-control" placeholder="<?php if ($this->lang->line('wallet_security_code') != '') echo stripslashes($this->lang->line('wallet_security_code')); else echo 'Security Code'; ?>" id="creditCardIdentifier" name="creditCardIdentifier"></input>
                                    </div>
                                    <input type="hidden" value="<?php echo $trans_details->row()->transaction_id; ?>" name="transaction_id" />
                                    <input type="hidden" value="<?php echo $trans_details->row()->user_id; ?>" name="user_id" />
                                    <input type="hidden" value="<?php echo $trans_details->row()->total_amount; ?>" name="total_amount" />
                                    <button type="submit" class="btn btn-default pay-btn" onClick="return wallet_recharge_validatecard()"><?php if ($this->lang->line('user_recharge') != '') echo stripslashes($this->lang->line('user_recharge')); else echo 'Recharge'; ?> <?php echo $this->config->item('currency_symbol') . ' ' . number_format($trans_details->row()->total_amount, 2); ?> <?php if ($this->lang->line('wallet_using_credit_card') != '') echo stripslashes($this->lang->line('wallet_using_credit_card')); else echo 'using credit card'; ?></button>
                                    <a href="rider/wallet-recharge/pay-cancel"  class="btn btn-default pay-btn"><?php if ($this->lang->line('wallet_cancel_payment') != '') echo stripslashes($this->lang->line('wallet_cancel_payment')); else echo 'Cancel Payment'; ?></a>
                                </form>
                            </div>

                            <!---------------------------------------------------------------------------------PAYPAL-------------------------------------------------------------------------------------------------------------------------------------->


                            <div role="tabpanel" class="tab-pane <?php echo $paypalActive; ?>" id="paypal">
                                <p><?php if ($this->lang->line('wallet_your_total_charge') != '') echo stripslashes($this->lang->line('wallet_your_total_charge')); else echo 'Your total charge is'; ?> <?php echo $this->config->item('currency_symbol') . ' ' . number_format($trans_details->row()->total_amount, 2); ?>.</p>
                                <h5><?php if ($this->lang->line('wallet_instructions') != '') echo stripslashes($this->lang->line('wallet_instructions')); else echo 'Instructions:'; ?></h5>
                                <p><?php if ($this->lang->line('wallet_after_click') != '') echo stripslashes($this->lang->line('wallet_after_click')); else echo 'After clicking "Recharge" you will be redirected to PayPal to authorize the payment.'; ?></p>
                                <span><strong> <?php if ($this->lang->line('wallet_you_must_complete') != '') echo stripslashes($this->lang->line('wallet_you_must_complete')); else echo 'You must complete the process or the transaction will not occur.'; ?></strong></span>
                                <form name="PaymentForm" id="PaymentPalForm" method="post" enctype="multipart/form-data" action="site/wallet_recharge/paypal_wal_payment_process" style="margin:2%;">
                                    <input type="hidden" value="<?php echo $trans_details->row()->transaction_id; ?>" name="transaction_id" />
                                    <input type="hidden" value="<?php echo $trans_details->row()->user_id; ?>" name="user_id" />
                                    <input type="hidden" value="<?php echo $trans_details->row()->total_amount; ?>" name="total_amount" />
                                    <button type="submit" class="btn btn-default pay-btn"> <?php if ($this->lang->line('user_recharge') != '') echo stripslashes($this->lang->line('user_recharge')); else echo 'Recharge'; ?> <?php echo $this->config->item('currency_symbol') . ' ' . number_format($trans_details->row()->total_amount, 2); ?> <?php if ($this->lang->line('wallet_using_paypal') != '') echo stripslashes($this->lang->line('wallet_using_paypal')); else echo 'using PayPal'; ?> </button>
                                    <a href="rider/wallet-recharge/pay-cancel"  class="btn btn-default pay-btn"><?php if ($this->lang->line('wallet_cancel_payment') != '') echo stripslashes($this->lang->line('wallet_cancel_payment')); else echo 'Cancel Payment'; ?></a>
                                </form>
                            </div>

                        </div>
                    </div>
					<?php } else { ?>
						<h1 style="margin:3%; text-align:center;">Sorry! No active payments available.</h1>
					<?php } ?>
					
                </div>
            </div>
        </section>
    </div>
</div> 


<script>
    function payment_action(opt) {
        if (opt == 'pay_paypal') {
            $('#paypal_container').css('display', 'block');
            $('#credit_card_container').css('display', 'none');
        } else if (opt == 'pay_credit_card') {
            $('#credit_card_container').css('display', 'block');
            $('#paypal_container').css('display', 'none');
        }
    }


    function wallet_recharge_validatecard() {
        var cardNumber = document.getElementById("cardNumber").value.trim();
        var CCExpDay = document.getElementById("CCExpDay").value.trim();
        var CCExpMnth = document.getElementById("CCExpMnth").value.trim();
        var creditCardIdentifier = document.getElementById("creditCardIdentifier").value.trim();
        var cardType = document.getElementById("cardType").value.trim();

        document.getElementById("cardNumber").classList.remove("txt-error");
        document.getElementById("CCExpDay").classList.remove("txt-error");
        document.getElementById("CCExpMnth").classList.remove("txt-error");
        document.getElementById("creditCardIdentifier").classList.remove("txt-error");
        document.getElementById("cardType").classList.remove("txt-error");

        var status = 0;
        if (cardNumber == "" || isNaN(cardNumber)) {
            document.getElementById("cardNumber").classList.add("txt-error");
            status++;
        }
        if (CCExpDay == "") {
            document.getElementById("CCExpDay").classList.add("txt-error");
            status++;
        }
        if (CCExpMnth == "") {
            document.getElementById("CCExpMnth").classList.add("txt-error");
            status++;
        }
        if (creditCardIdentifier == "") {
            document.getElementById("creditCardIdentifier").classList.add("txt-error");
            status++;
        }
        if (cardType == "") {
            document.getElementById("cardType").classList.add("txt-error");
            status++;
        }
        if (status != 0) {
            return false;
        }
    }
</script>

<?php
$this->load->view('site/templates/footer');
?> 