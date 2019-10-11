<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
    <head>
        <?php
        if ($this->lang->line('home_cabily') != '')
            $sitename = str_replace('{SITENAME}', $this->config->item('email_title'), stripslashes($this->lang->line('home_cabily')));
        else
            $sitename = $this->config->item('email_title');
        ?>
        <meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
        <title><?php echo $sitename; ?></title>
        <link rel="stylesheet" href="<?php echo base_url(); ?>css/mobile/app-style.css" type="text/css" media="all" />
    </head>
    <body>
        <section>
            <div class="app-content">
                <div class="main">
                    <div class="app-content-box">
                        <h1><?php if ($this->lang->line('wallet_your_requested_page_could_not') != '') echo stripslashes($this->lang->line('wallet_your_requested_page_could_not')); else echo 'Your requested page could not be found or it is currently unavailable.'; ?></h1>
                    </div>
                </div>
            </div>
        </section>
    </body>
</html>
