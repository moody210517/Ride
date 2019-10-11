<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
    <?php if ($this->lang->line('home_cabily') != '') $sitename = str_replace('{SITENAME}', $this->config->item('email_title'), stripslashes($this->lang->line('home_cabily'))); else $sitename = $this->config->item('email_title'); ?>
    <head>
        <meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
        <title><?php echo $sitename; ?> - <?php if ($this->lang->line('wallet_payment_credit_card') != '') echo stripslashes($this->lang->line('wallet_payment_credit_card')); else echo 'Payment Credit Card'; ?></title>
        <link rel="stylesheet" href="<?php echo base_url(); ?>css/mobile/app-style.css" type="text/css" media="all" />
    </head>
    <body>
        <section>
            <div class="shipping_address">
                <div class="main">		
                    <div class="app-content-box">
                        <h1><?php if ($this->lang->line('wallet_enter_credit_card_infor') != '') echo stripslashes($this->lang->line('wallet_enter_credit_card_infor')); else echo 'Enter Credit Card Informations'; ?><a class="close_btn" href="<?php echo base_url(); ?>mobile/payment/Cancel?mobileId=<?php echo $mobileId; ?>"></a></h1>
                        <form name="PaymentCard" id="PaymentCard" method="post" enctype="multipart/form-data" action="<?php echo base_url(); ?>mobile/mobile_wallet_recharge/user_wal_PaymentCard">
                            <ul>
								 <li>
                                    <select id="cardType" name="cardType" class="input-scroll-4">
                                        <option value=""><?php if ($this->lang->line('wallet_select_card_type') != '') echo stripslashes($this->lang->line('wallet_select_card_type')); else echo 'Select Card Type'; ?></option>
                                        <option value="american_express"><?php if ($this->lang->line('wallet_american_express') != '') echo stripslashes($this->lang->line('wallet_american_express')); else echo 'American Express'; ?></option>
                                        <option value="visa"><?php if ($this->lang->line('wallet_visa') != '') echo stripslashes($this->lang->line('wallet_visa')); else echo 'Visa'; ?></option>
                                        <option value="master_card"><?php if ($this->lang->line('wallet_master_card') != '') echo stripslashes($this->lang->line('wallet_master_card')); else echo 'Master Card'; ?></option>
                                        <option value="discover"><?php if ($this->lang->line('wallet_discover') != '') echo stripslashes($this->lang->line('wallet_discover')); else echo 'Discover'; ?></option>
                                    </select>
                                </li>
                                <li><input type="text" class="input-scroll-3" placeholder="<?php if ($this->lang->line('wallet_card_number') != '') echo stripslashes($this->lang->line('wallet_card_number')); else echo 'Card number'; ?>" id="cardNumber" name="cardNumber" maxlength="16" size="16"></input></li>
                                <li><label><?php if ($this->lang->line('wallet_expiration') != '') echo stripslashes($this->lang->line('wallet_expiration')); else echo 'Expiration'; ?></label> 
                                    <?php $Sel = 'selected="selected"'; ?>
                                    <select id="CCExpDay" name="CCExpDay" class="input-scroll-2">
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
                                    <select id="CCExpMnth" name="CCExpMnth" class="input-scroll-2"> 
                                        <?php for ($i = get_time_to_string('Y'); $i < (get_time_to_string('Y') + 30); $i++) { ?>
                                            <option value="<?php echo $i; ?>"><?php echo $i; ?></option>
                                        <?php } ?>
                                    </select>
                                </li>
                                <li><input type="password" class="input-scroll" placeholder="<?php if ($this->lang->line('wallet_security_code') != '') echo stripslashes($this->lang->line('wallet_security_code')); else echo 'Security Code'; ?>" id="creditCardIdentifier" name="creditCardIdentifier"></input></li>
                               
                                <input type="hidden" class="input-scroll" value="<?php echo $mobileId; ?>" name="mobileId" id="mobileId"></input>
                                <li class="last"><input type="submit" class="input-submit-btn" value="<?php if ($this->lang->line('wallet_use_this_card') != '') echo stripslashes($this->lang->line('wallet_use_this_card')); else echo 'Use This Card'; ?>" onClick="return validatecard();"></input></li>
                            </ul>
                        </form>
                    </div>
                </div>	
            </div>
        </section>
        <script type="text/javascript">
            function validatecard() {
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
    </body>
</html>
