<?php
$this->load->view('site/templates/profile_header');
?>

<section class="profile_pic_sec row">
    <div  class="profile_login_cont">
      
        <!-------Profile side bar ---->
        <?php
        $this->load->view('site/templates/profile_sidebar');
        ?>
        <div class="share_detail">
        <div class="col-md-12 profile_rider_right">
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
                        
						<?php $formArr = array('id' => 'PaymentCard','enctype' => 'multipart/form-data'); 
							echo form_open('site/wallet_recharge/user_wal_PaymentCard',$formArr);
						?>
                            <div class="form-group">
                                <label><?php if ($this->lang->line('wallet_card_type') != '') echo stripslashes($this->lang->line('wallet_card_type')); else echo 'Card Type'; ?></label>
                                <?php 
									
									if ($this->lang->line('wallet_select_card_type') != '') $selectCard = stripslashes($this->lang->line('wallet_select_card_type')); else $selectCard = 'Select Card Type';
									
									if ($this->lang->line('wallet_american_express') != '') $american_exp = stripslashes($this->lang->line('wallet_american_express')); else $american_exp = 'American Express';
									
									if ($this->lang->line('wallet_visa') != '') $visa = stripslashes($this->lang->line('wallet_visa')); else $visa = 'Visa';
									
									if ($this->lang->line('wallet_master_card') != '') $master = stripslashes($this->lang->line('wallet_master_card')); else $master = 'Master Card';if 
									
									($this->lang->line('wallet_discover') != '') $discover = stripslashes($this->lang->line('wallet_discover')); else $discover = 'Discover';
									
									$drop_options = array(
														'' => $selectCard,
														'american_express' => $american_exp,
														'visa' => $visa,
														'master_card' => $master,
														'discover' => $discover
									);
									
									
									$input_data = 'id="cardType"';
									
									echo form_dropdown('cardType',$drop_options,'',$input_data);
								?>
                            </div>

                            <div class="form-group">
                                <label><?php if ($this->lang->line('user_credit_card_number') != '') echo stripslashes($this->lang->line('user_credit_card_number')); else echo 'Credit Card Number'; ?></label>
								
								<?php 
									if ($this->lang->line('wallet_card_number') != '') $placeholder = stripslashes($this->lang->line('wallet_card_number')); else $placeholder = 'Card number';
									
									$input_data = array(
													'name' => 'cardNumber',
													'id' => 'cardNumber',
													'type' => 'text',
													'class' => 'form-control input-scroll-3',
													'placeholder' => $placeholder,
													'maxlength' => '16',
													'size' => '16'
									);
									echo form_input($input_data);
								?>
								
                            </div>

                            <div class="form-group">
                                <label><?php if ($this->lang->line('user_expiration_date') != '') echo stripslashes($this->lang->line('user_expiration_date')); else echo 'Expiration Date'; ?></label>
                                <?php $Sel = 'selected="selected"'; ?>
								
								<?php 
									
									
									$drop_options = array(
														'01' => '01',
														'02' => '02',
														'03' => '03',
														'04' => '04',
														'05' => '05',
														'06' => '06',
														'07' => '07',
														'08' => '08',
														'09' => '09',
														'10' => '10',
														'11' => '11',
														'12' => '12'
									);
                                    
                                    $cM = date('m'); 
                                    $cY = date('Y'); 
                                    
									
									$input_data = 'id="CCExpDay"';
									echo form_dropdown('CCExpDay',$drop_options,get_time_to_string('m'),$input_data);
									
									
									$drop_options = array();
									for ($i = get_time_to_string('Y'); $i < (get_time_to_string('Y') + 30); $i++) { 
										$drop_options[$i] = $i;
									}
									
									$input_data = 'id="CCExpMnth"';
									echo form_dropdown('CCExpMnth',$drop_options,get_time_to_string('m'),$input_data);
								?>
								
								
                            </div>
                            <div class="form-group">
                                <label><?php if ($this->lang->line('wallet_security_code') != '') echo stripslashes($this->lang->line('wallet_security_code')); else echo 'Security Code'; ?></label>
								
								<?php 
									if ($this->lang->line('wallet_security_code') != '') $placeholder = stripslashes($this->lang->line('wallet_security_code')); else $placeholder = 'Security Code';
									
									$input_data = array(
													'name' => 'creditCardIdentifier',
													'id' => 'creditCardIdentifier',
													'type' => 'password',
													'class' => 'form-control',
													'placeholder' => $placeholder
									);
									echo form_input($input_data);
								?>

								
                            </div>
							
							<?php 
								$input_data = array(
												'name' => 'transaction_id',
												'type' => 'hidden',
												'value' => $trans_details->row()->transaction_id
								);
								echo form_input($input_data);
								
								$input_data = array(
												'name' => 'user_id',
												'type' => 'hidden',
												'value' => $trans_details->row()->user_id
								);
								echo form_input($input_data);
								
								$input_data = array(
												'name' => 'total_amount',
												'type' => 'hidden',
												'value' => $trans_details->row()->total_amount
								);
								echo form_input($input_data);
							?>

							
                            <button type="submit" class="btn btn-default pay-btn securityCheck" onClick="return wallet_recharge_validatecard()"><?php if ($this->lang->line('user_recharge') != '') echo stripslashes($this->lang->line('user_recharge')); else echo 'Recharge'; ?> <?php echo $this->config->item('currency_symbol') . ' ' . number_format($trans_details->row()->total_amount, 2); ?> <?php if ($this->lang->line('wallet_using_credit_card') != '') echo stripslashes($this->lang->line('wallet_using_credit_card')); else echo 'using credit card'; ?></button>
                            <a href="rider/wallet-recharge/pay-cancel"  class="btn btn-default pay-btn"><?php if ($this->lang->line('wallet_cancel_payment') != '') echo stripslashes($this->lang->line('wallet_cancel_payment')); else echo 'Cancel Payment'; ?></a>
                        <?php echo form_close(); ?>
                    </div>

                    <!---------------------------------------------------------------------------------PAYPAL-------------------------------------------------------------------------------------------------------------------------------------->


                    <div role="tabpanel" class="tab-pane <?php echo $paypalActive; ?>" id="paypal">
                        <p><?php if ($this->lang->line('wallet_your_total_charge') != '') echo stripslashes($this->lang->line('wallet_your_total_charge')); else echo 'Your total charge is'; ?> <?php echo $this->config->item('currency_symbol') . ' ' . number_format($trans_details->row()->total_amount, 2); ?>.</p>
                        <h5><?php if ($this->lang->line('wallet_instructions') != '') echo stripslashes($this->lang->line('wallet_instructions')); else echo 'Instructions:'; ?></h5>
                        <p><?php if ($this->lang->line('wallet_after_click') != '') echo stripslashes($this->lang->line('wallet_after_click')); else echo 'After clicking "Recharge" you will be redirected to PayPal to authorize the payment.'; ?></p>
                        <span><strong> <?php if ($this->lang->line('wallet_you_must_complete') != '') echo stripslashes($this->lang->line('wallet_you_must_complete')); else echo 'You must complete the process or the transaction will not occur.'; ?></strong></span>
                        
						<?php 
							$formArr = array('id' => 'PaymentPalForm','enctype' => 'multipart/form-data','style' => 'margin:2%;'); 
							echo form_open('site/wallet_recharge/paypal_wal_payment_process',$formArr);
						?>
							
							<?php 
								$input_data = array(
												'name' => 'transaction_id',
												'type' => 'hidden',
												'value' => $trans_details->row()->transaction_id
								);
								echo form_input($input_data);
								
								$input_data = array(
												'name' => 'user_id',
												'type' => 'hidden',
												'value' => $trans_details->row()->user_id
								);
								echo form_input($input_data);
								
								$input_data = array(
												'name' => 'total_amount',
												'type' => 'hidden',
												'value' => $trans_details->row()->total_amount
								);
								echo form_input($input_data);
							?>
							
                            <button type="submit" class="btn btn-default pay-btn"> <?php if ($this->lang->line('user_recharge') != '') echo stripslashes($this->lang->line('user_recharge')); else echo 'Recharge'; ?> <?php echo $this->config->item('currency_symbol') . ' ' . number_format($trans_details->row()->total_amount, 2); ?> <?php if ($this->lang->line('wallet_using_paypal') != '') echo stripslashes($this->lang->line('wallet_using_paypal')); else echo 'using PayPal'; ?> </button>
                            <a href="rider/wallet-recharge/pay-cancel"  class="btn btn-default pay-btn"><?php if ($this->lang->line('wallet_cancel_payment') != '') echo stripslashes($this->lang->line('wallet_cancel_payment')); else echo 'Cancel Payment'; ?></a>
                        </form>
                    </div>

                </div>
            </div>
            <?php } else { ?>
                <h2 style="margin-top:10%; text-align:center;">Sorry! No active payments available.</h2>
            <?php } ?>
        </div>
        </div>
    </div>
</section>

<style>
.tab-pane.active {
    margin: 2%;
}
</style>

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

        if (Number(CCExpMnth) == Number("<?php echo $cY;?>") && Number(CCExpDay) < Number("<?php echo $cM;?>")) { 
            document.getElementById("CCExpDay").classList.add("txt-error");
            status++;
        }
        if (status != 0) {
            return false;
        }else{
			$("#PaymentCard").submit();
        	$(".securityCheck").attr("disabled", true);
        }
    }
</script>

<?php
$this->load->view('site/templates/footer');
?> 