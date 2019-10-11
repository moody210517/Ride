<?php
$this->load->view('site/templates/profile_header');
?>

<section class="profile_pic_sec row">
   <div  class="profile_login_cont">

                <?php
                $this->load->view('site/templates/profile_sidebar');
                ?>
                <div class="share_detail">
                    <div>
                        <?php
                        if (isset($payOption)) {
                            if ($payOption == 'wallet recharge') {
                                ?>
                                <section>
                                    <div class="shipping_address">
                                        <div class="main">		
                                            <div class="wallet_pay_notification">
                                                <h1><?php if ($this->lang->line('wallet_recharge_success') != '') echo stripslashes($this->lang->line('wallet_recharge_success')); else echo 'Wallet Recharge Successful'; ?> <a class="close_btn" href="<?php echo base_url() . 'site/wallet-recharge/pay-cancel'; ?>"></a></h1>
                                                <div class="payment-success"><img src="<?php echo base_url(); ?>css/mobile/images/success.png" alt="<?php if ($this->lang->line('wallet_success') != '') echo stripslashes($this->lang->line('wallet_success')); else echo 'success'; ?>" title="<?php if ($this->lang->line('wallet_success') != '') echo stripslashes($this->lang->line('wallet_success')); else echo 'success'; ?>" /></div>
                                                    <?php if (isset($trans_id)) { ?>
                                                    <h3 class="payment-reference"><?php if ($this->lang->line('wallet_your_transaction_reference') != '') echo stripslashes($this->lang->line('wallet_your_transaction_reference')); else echo 'Your transaction reference id :'; ?><b> <?php echo $trans_id; ?></b></h3>
                                                <?php } ?>
                                            </div>			
                                        </div>	
                                    </div>
                                    <?php $this->output->set_header('refresh:5;url=' . base_url() . 'rider/wallet-transactions'); ?>
                                </section>
                                <?php
                            }
                        }
                        ?>
                        <?php
                        if (isset($payOption)) {
                            if ($payOption == 'ride payment') {
                                ?>
                                <div class="wallet_pay_notification">
                                    <h1><?php if ($this->lang->line('wallet_payment_success') != '') echo stripslashes($this->lang->line('wallet_payment_success')); else echo 'Your Payment Successful'; ?></h1>
                                    <div class="payment-success"><img src="<?php echo base_url(); ?>css/mobile/images/success.png" alt="<?php if ($this->lang->line('wallet_success') != '') echo stripslashes($this->lang->line('wallet_success')); else echo 'success'; ?>" title="<?php if ($this->lang->line('wallet_success') != '') echo stripslashes($this->lang->line('wallet_success')); else echo 'success'; ?>" /></div>
                                </div>			
                                <?php $this->output->set_header('refresh:5;url=' . base_url() . 'rider/my-money'); ?>
                                <?php
                            }
                        }
                        ?>

                        <?php
                        if ($payOption == '') {
                            ?>
                            <div class="wallet_pay_notification">
                                <h1><?php if ($this->lang->line('wallet_payment_success') != '') echo stripslashes($this->lang->line('wallet_payment_success')); else echo 'Your Payment Successful'; ?></h1>
                                <div class="payment-success"><img src="<?php echo base_url(); ?>css/mobile/images/success.png" alt="success" title="success" /></div>
                            </div>			

                            <?php $this->output->set_header('refresh:5;url=' . base_url() . 'rider/my-money'); ?>
                            <?php
                        }
                        ?>
                    </div>
                </div>
        </div>
</section>        

<style>
    .payment-reference{
        margin-top:8px;
    }
</style>

<?php
$this->load->view('site/templates/footer');
?> 

