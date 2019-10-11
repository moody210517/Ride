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
                    <div>
                        <?php
                        if (isset($payOption)) {
                            if ($payOption == 'wallet recharge') {
                                ?>
                                <div class="wallet_pay_notification">
                                    <h1><?php if ($this->lang->line('wallet_your_payment_failed') != '') echo stripslashes($this->lang->line('wallet_your_payment_failed')); else echo 'Your Payment Failed'; if($errors != 'Error' && $errors != 'cancelled') { ?> - <span><?php echo urldecode($errors); ?></span><?php } ?></h1>
                                    <div class="payment-success"><img src="<?php echo base_url(); ?>css/mobile/images/failed.png" alt="<?php if ($this->lang->line('wallet_failed') != '') echo stripslashes($this->lang->line('wallet_failed')); else echo 'failed'; ?>" title="<?php if ($this->lang->line('wallet_failed') != '') echo stripslashes($this->lang->line('wallet_failed')); else echo 'failed'; ?>" /></div>
                                </div>
                                <?php $this->output->set_header('refresh:5;url=' . base_url() . 'rider/my-money'); ?>
                                <?php
                            }
                        } else {
                            ?>

                            <div class="wallet_pay_notification">
                               <h1><?php if ($this->lang->line('wallet_your_payment_failed') != '') echo stripslashes($this->lang->line('wallet_your_payment_failed')); else echo 'Your Payment Failed'; if($errors != 'Error' && $errors != 'cancelled') { ?> - <span><?php echo urldecode($errors); ?></span><?php } ?></h1>
                                <div class="payment-success"><img src="<?php echo base_url(); ?>css/mobile/images/failed.png" alt="<?php if ($this->lang->line('wallet_failed') != '') echo stripslashes($this->lang->line('wallet_failed')); else echo 'failed'; ?>" title="<?php if ($this->lang->line('wallet_failed') != '') echo stripslashes($this->lang->line('wallet_failed')); else echo 'failed'; ?>" /></div>
                            </div>			
                            <?php $this->output->set_header('refresh:5;url=' . base_url() . 'rider/my-money'); ?>

                        <?php } ?>

                </div>
            </div>
        </div>
</section>
<?php
$this->load->view('site/templates/footer');
?> 
