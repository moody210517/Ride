<?php
$this->load->view(ADMIN_ENC_URL.'/templates/header.php');
extract($privileges);

if (!empty($wallet_history[0]['transactions'])) {
    $wallet_txns = array_reverse($wallet_history[0]['transactions']);
} else {
    $wallet_txns = array();
}
?>
<div id="content" class="view_wallet_sec">
	<div class="grid_container">

		
		<?php 
			$attributes = array('id' => 'display_form');
			echo form_open(ADMIN_ENC_URL.'/users/change_user_status_global',$attributes) 
		?>
			<div class="grid_12">
				<div class="widget_wrap">
					<div class="widget_top">
						<span class="h_icon blocks_images"></span>
						<h6><?php echo $user_info->row()->user_name.' [ '.$user_info->row()->email.' ] '; ?> <?php if ($this->lang->line('admin_common_wallet_transaction_history') != '') echo stripslashes($this->lang->line('admin_common_wallet_transaction_history')); else echo 'Wallet transaction history'; ?> </h6>
					</div>
					
					<div class="widget_content">
					
					
						<div class="wallet_header" style="margin-left:36%; margin-top:3%;" >
							<a class="activities_s bluebox big" style="width: 35%;">
									<div class="block_label">
									   <?php if ($this->lang->line('admin_user_wallet_balance') != '') echo stripslashes($this->lang->line('admin_user_wallet_balance')); else echo 'Wallet Balance'; ?>
										<span><?php echo $dcurrencySymbol ?> <?php echo number_format($wallet_history[0]['total'],2); ?> </span>
										<span  class="add_money_btn o-modal" data-content="add_money_box">+ <?php if ($this->lang->line('admin_user_add_money') != '') echo stripslashes($this->lang->line('admin_user_add_money')); else echo 'ADD MONEY'; ?></span>
									</div>	
								</a>
				
							</div>
					
					
						<?php  $tble = 'userWalListTblCustom'; ?>
                   
					
						<table class="display" id="<?php echo $tble; ?>">
							<thead>
								<tr>
				
									<th class="tip_top" title="<?php if ($this->lang->line('dash_click_sort') != '') echo stripslashes($this->lang->line('dash_click_sort')); else echo 'Click to sort'; ?>">
										<?php if ($this->lang->line('dash_date') != '') echo stripslashes($this->lang->line('dash_date')); else echo 'Date'; ?>
									</th>
									<th class="tip_top" title="<?php if ($this->lang->line('dash_click_sort') != '') echo stripslashes($this->lang->line('dash_click_sort')); else echo 'Click to sort'; ?>">
										 <?php if ($this->lang->line('admin_user_transaction_info') != '') echo stripslashes($this->lang->line('admin_user_transaction_info')); else echo 'Transaction info'; ?>
									</th>
									<th style="width:90px !important">
										  <?php if ($this->lang->line('admin_txn_amount') != '') echo stripslashes($this->lang->line('admin_txn_amount')); else echo 'Txn Amount'; ?>
									</th>
									<th>
										<?php if ($this->lang->line('admin_user_txn_type') != '') echo stripslashes($this->lang->line('admin_user_txn_type')); else echo 'Txn Type'; ?>
									</th>
									<th>
										<?php if ($this->lang->line('admin_user_txn_mode') != '') echo stripslashes($this->lang->line('admin_user_txn_mode')); else echo 'Txn Mode'; ?>
									</th>
									<th class="tip_top" title="<?php if ($this->lang->line('dash_click_sort') != '') echo stripslashes($this->lang->line('dash_click_sort')); else echo 'Click to sort'; ?>">
										<?php if ($this->lang->line('admin_user_currenct_balance') != '') echo stripslashes($this->lang->line('admin_user_currenct_balance')); else echo 'Current Balance'; ?>
									</th>
									
								</tr>
							</thead>
							<tbody>
                     <?php
                                    if ($this->lang->line('home_cabily') != '')
                                        $sitename = str_replace('{SITENAME}', $this->config->item('email_title'), stripslashes($this->lang->line('home_cabily')));
                                    else
                                        $sitename = $this->config->item('email_title');
                                    ?>
								<?php
									
                     if($this->lang->line('user_wallet_debit_txn') != '') $user_wallet_debit_txn = stripslashes($this->lang->line('user_wallet_debit_txn')); else $user_wallet_debit_txn = 'Wallet Debit TxnId';
                  
                           if (!empty($wallet_txns)) {
                               foreach ($wallet_txns as $txns) {
                                   $txns_description = '';
                                   $txn_amount = 0;
                                   $avail_balance = 0;
                                   $txn_date = '';
                                   $trans_mode = '';
                                   if (isset($txns['credit_type']) && isset($txns['type'])) {
                                       if ($txns['credit_type'] == 'welcome') {
                                           if ($this->lang->line('user_welcome_bonus') != '') $var = stripslashes($this->lang->line('user_welcome_bonus')); else $var = 'Welcome Bonus';
                                           $txns_description = $sitename .' '. $var;
                                       } else if ($txns['credit_type'] == 'recharge') {
                                           if ($this->lang->line('user_wallet_recharge_txn') != '') $var = stripslashes($this->lang->line('user_wallet_recharge_txn')); else $var = 'Wallet Recharge TxnId : ';
                                           $txns_description = $var .' '. $txns['trans_id'];
                                       } else if ($txns['credit_type'] == 'payment') {
                                           if ($this->lang->line('user_booking_for_crn') != '') $var = stripslashes($this->lang->line('user_booking_for_crn')); else $var = 'Booking for crn:';
                                           $txns_description = $var ." ". $txns['ref_id'];
                                       } else if ($txns['credit_type'] == 'referral') {                           
                                            if ($this->lang->line('user_referral_reward') != '') $var = stripslashes($this->lang->line('user_referral_reward')); else $var = 'Referral reward:';
                                              $name = get_referer_name($txns['ref_id']);
                                              if($name==''){
                                                if ($this->lang->line('rides_na') != '') $name = stripslashes($this->lang->line('rides_na')); else $name = 'N/A';
                                              }
                                              $txns_description = $var ." ". $name;
                                       } else {
                                           $txns_description = $txns['credit_type'];
                                       }

                                       if (isset($txns['type'])) {
                                           if ($txns['type'] == 'CREDIT') {
											    if ($this->lang->line('credit_text') != '') $credit_text = stripslashes($this->lang->line('credit_text')); else $credit_text = 'CR';
                                               $trans_mode = '&#8593;'.$credit_text;
                                           } else {
											    if ($this->lang->line('debit_text') != '') $debit_text = stripslashes($this->lang->line('debit_text')); else $debit_text = 'DR';
                                               $trans_mode = '&#8595;'.$debit_text;
                                           }
                                       }

                                       $txn_date = get_time_to_string('D, d M,Y', MongoEPOCH($txns['trans_date']));
                                       $txn_amount = $txns['trans_amount'];
                                       $avail_balance = $txns['avail_amount'];
                                   }
                        
                        if(isset($txns['debit_type'])) {
                           $txn_date = get_time_to_string('D, d M,Y', MongoEPOCH($txns['trans_date']));
                           $txn_amount = $txns['trans_amount'];
                           $var="".$user_wallet_debit_txn." :";
                           $txns_description = $var ." ". $txns['ref_id'];
                           $avail_balance = $txns['avail_amount'];
						    $trans_mode = '&#8595;DR';
                        }
                        if($txn_amount > 0){
                                            ?>
								<tr>
					
									<td class="center">
										<?php 

                                 echo get_time_to_string('Y-m-d h:i A', MongoEPOCH($txns['trans_date']));
                              ?>
									</td>
									<td class="center">
										<?php echo $txns_description; ?>
									</td>
									<td class="center">
										<?php echo number_format($txn_amount,0); ?>
									</td>
									<td class="center">
									<?php 
									if(isset($txns['credit_type'])) { 
										if($txns['ref_id'] == 'admin') {
											if($txns['credit_type']=="recharge"){
												if ($this->lang->line('admin_txn_wallet_recharge_by_admin') != ''){
													$txn_type = stripslashes($this->lang->line('admin_txn_wallet_recharge_by_admin'));
												}else{
													$txn_type = "Recharged by admin";
												}
											}else{
												$txn_type = $txns['credit_type'];
											}
										}else {
											if($txns['credit_type'] == 'welcome') {
												if ($this->lang->line('admin_txn_wallet_recharge_welcome') != ''){
													$txn_type = stripslashes($this->lang->line('admin_txn_wallet_recharge_welcome'));
												}else{
													$txn_type = "Welcome Bonus";
												}
											}else if($txns['credit_type'] == 'referral') {
												if ($this->lang->line('admin_txn_wallet_recharge_referral') != ''){
													$txn_type = stripslashes($this->lang->line('admin_txn_wallet_recharge_referral'));
												}else{
													$txn_type = "Referral";
												}
											}else if($txns['credit_type'] == 'recharge') {
												if ($this->lang->line('admin_txn_wallet_recharge') != ''){
													$txn_type = stripslashes($this->lang->line('admin_txn_wallet_recharge'));
												}else{
													$txn_type = "Recharge";
												}
											}else{
												$txn_type = $txns['credit_type'];
											}
										}
									} else {
										#echo $txns['debit_type']; 
										if ($this->lang->line('admin_txn_wallet_for_payment') != ''){
												$txn_type = stripslashes($this->lang->line('admin_txn_wallet_for_payment'));
											}else{
												$txn_type = "Payment";
											}
									}  
									echo $txn_type;
									?>
									</td>
									<td class="center">
										<b><?php echo $trans_mode;  ?></b>
									</td>
									<td class="center">
									<?php echo  number_format($avail_balance,0); ?>
									</td>
								</tr>
								<?php 
									}
								}
                        }
								?>
							</tbody>
							<tfoot>
								<tr>
									<th class="tip_top" title="<?php if ($this->lang->line('dash_click_sort') != '') echo stripslashes($this->lang->line('dash_click_sort')); else echo 'Click to sort'; ?>">
										 <?php if ($this->lang->line('dash_date') != '') echo stripslashes($this->lang->line('dash_date')); else echo 'Date'; ?>
									</th>
									<th class="tip_top" title="<?php if ($this->lang->line('dash_click_sort') != '') echo stripslashes($this->lang->line('dash_click_sort')); else echo 'Click to sort'; ?>">
										  <?php if ($this->lang->line('admin_user_transaction_info') != '') echo stripslashes($this->lang->line('admin_user_transaction_info')); else echo 'Transaction info'; ?>
									</th>
									<th style="width:90px !important">
										  <?php if ($this->lang->line('admin_txn_amount') != '') echo stripslashes($this->lang->line('admin_txn_amount')); else echo 'Txn Amount'; ?>
									</th>
									<th>
										 	<?php if ($this->lang->line('admin_user_txn_type') != '') echo stripslashes($this->lang->line('admin_user_txn_type')); else echo 'Txn Type'; ?>
									</th>
									<th>
										<?php if ($this->lang->line('admin_user_txn_mode') != '') echo stripslashes($this->lang->line('admin_user_txn_mode')); else echo 'Txn Mode'; ?>
									</th>
									<th class="tip_top" title="<?php if ($this->lang->line('dash_click_sort') != '') echo stripslashes($this->lang->line('dash_click_sort')); else echo 'Click to sort'; ?>">
										<?php if ($this->lang->line('admin_user_currenct_balance') != '') echo stripslashes($this->lang->line('admin_user_currenct_balance')); else echo 'Current Balance'; ?>
									</th>
									
								</tr>
							</tfoot>
						</table>
						
						
						
					</div>
				</div>
			</div>
			<input type="hidden" name="statusMode" id="statusMode"/>
			<input type="hidden" name="SubAdminEmail" id="SubAdminEmail"/>
		</form>	
	</div>
	<span class="clear"></span>
</div>

		<div id="add_money_box" style="display:none;">
            <h3><?php if ($this->lang->line('admin_user_add_money') != '') echo stripslashes($this->lang->line('admin_user_add_money')); else echo 'Add Money'; ?> </h3>
            <?php
            $attributes = array('class' => 'form_container left_label', 'id' => 'wallet_form', 'enctype' => 'multipart/form-data', 'method' => 'post');
            echo form_open_multipart(ADMIN_ENC_URL.'/users/add_money_to_user', $attributes);
            ?>
            <ul>

                 <li>
                    <input name="user_id" type="hidden"   value="<?php echo (string)$user_info->row()->_id; ?>"/>
                    <div class="form_grid_12">
                        <div class="form_input" style="width: 100%;margin:0 auto;">
							<input name="trans_amount" id="trans_amount" minlength="1" maxlength="7" type="text"  class="required  small tipTop number positiveNumber currencyT minfloatingNumber" />
							<br/>
                            <p id="walAmtErr"></p>
							<button type="button" id="wallet_amount" class="btn_small btn_blue "><span><?php if ($this->lang->line('admin_common_submit') != '') echo stripslashes($this->lang->line('admin_common_submit')); else echo 'Submit'; ?></span></button>
                        </div>
					
                    </div>
                </li>
            </ul>
            <?php echo form_close(); ?>
        </div>
        </div>

	<script>
		 $('.o-modal').click(function (e) {
				var contentId = $(this).attr("data-content");
				$('#' + contentId).modal();
				return false;
			});
            
            
        var minimum_amount_should_be_greater_than_zero = "<?php if ($this->lang->line('minimum_amount_should_be_greater_than_zero') != '') echo stripslashes($this->lang->line('minimum_amount_should_be_greater_than_zero')); else echo 'Minimum amount should be greater than zero'; ?>";
        
        $('#wallet_amount').click(function (e) {
            $('#walAmtErr').html('');
            if($('#wallet_form').valid()){ 
                var wall_amt= Number($("#trans_amount").val());
                if(wall_amt > 0){                    
                    $("#trans_amount").prop("readonly", true);
                    $("#wallet_amount").prop('disabled', true);
                    $('#wallet_form').submit();
                } else {
                     $('#walAmtErr').html(minimum_amount_should_be_greater_than_zero);
                }
            }
        });        
            
	</script>
	
	<style>
		.btn_blue {
			margin-top:8px;
		}

		 .simplemodal-container {
			height: 180px !important;
			left: 407.5px !important;
			position: fixed;
			top: 250px !important;
			width: 501px !important;
			z-index: 1002;
		 }
		 
		.add_money_btn {
			background: #1ecc8b none repeat scroll 0 0;
			border: 1px solid #3debaa;
			border-radius: 5px;
			font-size: 14px !important;
			margin-left: 28%;
			margin-top: 23px;
			padding: 5px;
			width: 40%;
		}
	</style>
	
<?php 
$this->load->view(ADMIN_ENC_URL.'/templates/footer.php');
?>
<script>

/* jQuery('#trans_amount').keyup(function () { 
    this.value = this.value.replace(/[^0-9\.]/g,'');
});
var minimum_amount_should_be_greater_than_zero = "<?php if ($this->lang->line('minimum_amount_should_be_greater_than_zero') != '') echo stripslashes($this->lang->line('minimum_amount_should_be_greater_than_zero')); else echo 'minimum amount should be greater than zero'; ?>"

 
 
 $("#wallet_amount").click(function(event){ alert('sedf');
	var wall_amt=$("#trans_amount").val();
	if(wall_amt == 0){
		alert(minimum_amount_should_be_greater_than_zero);
		return false;
	}
});
*/

</script>