<?php
$this->load->view('site/templates/profile_header');
?> 
<section class="profile_pic_sec row">
    <div  class="profile_login_cont">
        <?php $this->load->view('site/templates/profile_sidebar'); ?>
        <div class="share_detail">
            <div class="share_det_title">
                <h2><?php if ($this->lang->line('home_shareinvite_earn') != '') echo stripslashes($this->lang->line('home_shareinvite_earn')); else echo 'INVITE AND EARN'; ?></h2>
            </div>
            <div class="share_det_icon">
                <img src="images/site/share_icon.png" />
            </div>
            <div class="share_det_amount">
				<?php if($this->config->item('welcome_amount') > 0 ) {?>
					<p><?php if ($this->lang->line('user_friend_joins_earns') != '') echo stripslashes($this->lang->line('user_friend_joins_earns')); else echo 'Friend joins, friend earns'; ?> <?php echo $dcurrencySymbol; ?><?php echo number_format($this->config->item('welcome_amount'), 2); ?></p>
				<?php } ?>
                <p>
				<?php if($this->config->item('referal_credit')=='instant') {?>
				<?php if ($this->lang->line('user_share_friend_join_earn') != '') echo stripslashes($this->lang->line('user_share_friend_join_earn')); else echo 'Friend joins, you earn'; ?>
				<?php } else {?>
					<?php if ($this->lang->line('user_share_friend_ride_you_earn') != '') echo stripslashes($this->lang->line('user_share_friend_ride_you_earn')); else echo 'Friend rides, you earn'; ?>
				<?php } ?> <?php echo $dcurrencySymbol; ?><?php echo number_format($this->config->item('referal_amount'), 2); ?>
				</p>
                <p><?php if ($this->lang->line('user_share_referral_code') != '') echo stripslashes($this->lang->line('user_share_referral_code')); else echo 'Share your referral code'; ?></p>
                <p><?php echo $rider_info->row()->unique_code; ?></p>
            </div>
            <div class="share_det_know">
                <h2><?php if ($this->lang->line('user_let_the_world_know') != '') echo stripslashes($this->lang->line('user_let_the_world_know')); else echo 'Let the world know'; ?></h2>
            </div>
            <div class="share_inner_details">
                <div class="social">
                    <span class="fb social_icon">
                    <img src="images/site/fb_share.png" />
                    
                    <a href="http://www.facebook.com/sharer.php?u=<?php echo base_url() . 'rider/signup?ref=' . base64_encode($rider_info->row()->unique_code); ?>" onclick="window.open('http://www.facebook.com/sharer.php?u=<?php echo base_url() . 'rider/signup/'.$this->data['langCode'].'/'.time().'?ref=' . base64_encode($rider_info->row()->unique_code); ?>', 'popup', 'height=500px, width=400px'); return false;">
                        <span><?php if ($this->lang->line('user_facebook') != '') echo stripslashes($this->lang->line('user_facebook')); else echo 'Facebook'; ?></span>
                    </a>
                    </span>

                    <span class="tw social_icon">
                    <img src="images/site/twiiter_share.png" />
                    
                    <a href="http://twitter.com/share?text=<?php echo $shareDesc; ?>&url=<?php echo base_url() . 'rider/signup?ref=' . base64_encode($rider_info->row()->unique_code); ?>" target=" _blank" onclick="window.open('http://twitter.com/share?text=<?php echo $shareDesc; ?>&url=<?php echo base_url() . 'rider/signup?ref=' . base64_encode($rider_info->row()->unique_code); ?>', 'popup', 'height=500px, width=400px'); return false;">
                        <span><?php if ($this->lang->line('user_twitter') != '') echo stripslashes($this->lang->line('user_twitter')); else echo 'Twitter'; ?></span>
                    </a>

                    </span>
                </div>
            </div>
        </div>
    </div>
</section>    
<?php
$this->load->view('site/templates/footer');
?> 