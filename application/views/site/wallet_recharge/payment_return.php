<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
    <head>
        <meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
        <?php
        if ($this->lang->line('home_cabily') != '')
            $sitename = str_replace('{SITENAME}', $this->config->item('email_title'), stripslashes($this->lang->line('home_cabily')));
        else
            $sitename = $this->config->item('email_title');
        ?>
        <title><?php echo $sitename; ?> - <?php if ($this->lang->line('wallet_payment_return') != '') echo stripslashes($this->lang->line('wallet_payment_return')); else echo 'Payment Return'; ?></title>
        <link rel="stylesheet" href="<?php echo base_url(); ?>css/mobile/app-style.css" type="text/css" media="all" />
    </head>
    <body>
        <section>
            <div class="shipping_address">
                <div class="main">		
                    <div class="app-content-box">
                        <h1><?php if ($this->lang->line('wallet_connecting_back') != '') echo stripslashes($this->lang->line('wallet_connecting_back')); else echo 'Connecting back your Application'; ?></h1>
                    </div>			
                </div>	
            </div>
        </section>
    </body>
</html>
