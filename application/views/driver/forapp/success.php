<?php
$this->load->view('site/templates/common_header');
?>
<link rel="stylesheet" href="css/site/screen.css">
<link rel="stylesheet" href="css/web_view.css">


</head>
<body>
    <div class="sign_up_cat_bg">
        <div class="container-new">
            <div class="text-center head_category">
                <a class="brand" href="">
                    <?php
                    if ($this->lang->line('home_cabily') != '')
                        $sitename = str_replace('{SITENAME}', $this->config->item('email_title'), stripslashes($this->lang->line('home_cabily')));
                    else
                        $sitename = $this->config->item('email_title');
                    ?>
                </a>
               
            </div>
        </div>
    </div>
    <div class="container-new">
        <div class="category_cont col-lg-12">
            <h2 class="text-center"><?php
                                    if ($this->lang->line('your_registration_successfull') != '')
                                        echo stripslashes($this->lang->line('your_registration_successfull'));
                                    else
                                        echo 'YOUR REGISTRATION IS SUCCESSFUL';
                                    ?>!</h2>
            <div class="col-lg-12 category_base text-center">
			<div class="payment-success"><img src="<?php echo base_url(); ?>css/mobile/images/success.png" alt="success" title="<?php
                                    if ($this->lang->line('success') != '')
                                        echo stripslashes($this->lang->line('success'));
                                    else
                                        echo 'Success';
                                    ?>" /></div>
              </div>
        </div>
    </div>
    <div class="clearfix"></div>
    <div class="clearfix"></div>
    <div class="foot_catgory"></div>
</body>
</html>