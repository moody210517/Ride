<?php
$this->load->view('site/templates/common_header');
$this->load->view('site/templates/cms_header');
?> 
<div class="cms_base_div">
    <div class="container-new cms-container">
        <?php if ($sideMenu == 'share_code') { ?>
            <center>
                <h1 class="text-center"><?php echo $heading; ?></h1>
                <h2> <?php echo $shareDesc; ?></h2>
                <center>
                <?php } else { ?>
                    <h2><?php if ($this->lang->line('cms_cannot_fetch') != '') echo stripslashes($this->lang->line('cms_cannot_fetch')); else echo 'Cannot fetch content from server'; ?></h2>
                <?php } ?>
                </div>
                </div>
                <?php
                $this->load->view('site/templates/footer');
                ?> 		