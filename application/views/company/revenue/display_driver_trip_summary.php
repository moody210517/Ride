<?php
$this->load->view(COMPANY_NAME.'/templates/header.php');

?>
<style>
    .ui-datepicker .ui-datepicker-buttonpane { background-image: none; margin: .7em 0 0 0; padding:0 .2em; border-left: 0; border-right: 0; border-bottom: 0; margin: 32px 0 0; }
</style>
<link href="css/admin_custom.css" rel="stylesheet" type="text/css" media="screen">
<div id="content">
    <div class="grid_container">

        <?php
        $attributes = array('id' => 'display_form');
        echo form_open(COMPANY_NAME.'/revenue/display_site_revenue', $attributes)
        ?>
        <div class="grid_12">
            <div class="widget_wrap">
                <div class="widget_top">
                    <span class="h_icon blocks_images"></span>
                    <h6><?php if ($this->lang->line('admin_site_earnings_revenue_summary') != '') echo stripslashes($this->lang->line('admin_site_earnings_revenue_summary')); else echo 'Revenue Summary'; ?> : <?php echo $fromdate . ' - ' . $todate; ?></h6>
                    <div style="float: right;line-height:40px;padding:0px 10px;height:39px;">
                    </div>
                </div>
                <div class="widget_content">
                    <?php if (!empty($rideList)) { ?>
                        <table class="custom-table tbl-open-close">
                            <thead>
                                <tr>
                                    <th><?php if ($this->lang->line('admin_setting_sno') != '') echo stripslashes($this->lang->line('admin_setting_sno')); else echo 'Sl No'; ?></th>
                                    <th><?php if ($this->lang->line('admin_rides_ride_id') != '') echo stripslashes($this->lang->line('admin_rides_ride_id')); else echo 'Ride Id'; ?></th>
                                    <th><?php if ($this->lang->line('admin_site_earnings_date') != '') echo stripslashes($this->lang->line('admin_site_earnings_date')); else echo 'Date'; ?></th>
                                    <th><?php if ($this->lang->line('admin_site_earnings_total_fare') != '') echo stripslashes($this->lang->line('admin_site_earnings_total_fare')); else echo 'Total Fare'; ?></th>
									<th><?php if ($this->lang->line('admin_site_earnings_tips') != '') echo stripslashes($this->lang->line('admin_site_earnings_tips')); else echo 'Tips'; ?></th>
                                    <th><?php if ($this->lang->line('admin_site_earnings_coupon_Amount') != '') echo stripslashes($this->lang->line('admin_site_earnings_coupon_Amount')); else echo 'Coupon Amount'; ?></th>
                                    <th><?php if ($this->lang->line('admin_site_earnings_amount_in_site') != '') echo stripslashes($this->lang->line('admin_site_earnings_amount_in_site')); else echo 'Amount In Site'; ?></th>
                                    <th><?php if ($this->lang->line('admin_site_earnings_amount_in_driver') != '') echo stripslashes($this->lang->line('admin_site_earnings_amount_in_driver')); else echo 'Amount In Driver'; ?></th>
                                    <th><?php if ($this->lang->line('admin_site_earnings_site_earnings') != '') echo stripslashes($this->lang->line('admin_site_earnings_site_earnings')); else echo 'Site Earnings'; ?></th>
                                    <th><?php if ($this->lang->line('admin_site_earnings_driver_earnings') != '') echo stripslashes($this->lang->line('admin_site_earnings_driver_earnings')); else echo 'Driver Earnings'; ?></th>
                                </tr>
                            </thead>
                            <?php $i = 0; ?>
                            <?php
                            $total_grand_fare = 0;
                            $total_coupon_discount = 0;
                            $total_amount_in_site = 0;
                            $total_amount_in_driver = 0;
                            $total_site_earnings = 0;
                            $total_driver_earnings = 0;
							$total_driver_tips = 0;
                            ?>							
                            <tbody>
                                <?php
                                foreach ($rideList as $ride) {
                                    $i++;
                                    ?>
                                    <?php
                                    $amount_in_site = 0;
                                    $amount_in_driver = 0;
                                    $site_earnings = 0;
                                    $driver_earnings = 0;
                                    $pay_type = '';
									$tips_amount = 0;
									
                                    if (isset($ride['pay_summary']['type'])) {
                                        $pay_type = $ride['pay_summary']['type'];
                                    }
									
									if(isset($ride['total']['tips_amount'])){
										$tips_amount = $ride['total']['tips_amount'];
									}
									if(isset($ride['amount_detail']['amount_in_site'])){
										$amount_in_site = $ride['amount_detail']['amount_in_site'];
									}
									if(isset($ride['amount_detail']['amount_in_driver'])){
										$amount_in_driver = $ride['amount_detail']['amount_in_driver'];
									}
									
                                    if ($pay_type == '') {
                                        $pay_type = 'FREE';
                                    }
									
									$driver_earnings = $ride['driver_revenue'] + $tips_amount;
									
                                    ?>
                                    <tr id="<?php echo $ride['ride_id']; ?>">
                                        <td>
                                            <?php echo $i; ?>
                                            <em data-pickup="<?php echo $ride['booking_information']['pickup']['location']; ?>" data-drop="<?php echo $ride['booking_information']['drop']['location']; ?>" data-paytype="<?php echo get_language_value_for_keyword($pay_type,$this->data['langCode']); ?>"></em>
                                        </td>
                                        <td><?php echo $ride['ride_id']; ?></td>
                                        <td><?php echo get_time_to_string("d-m-Y h:i A", MongoEPOCH($ride['booking_information']['pickup_date'])); ?></td>
                                        <td>
                                            <span class="amt-right"><?php echo number_format($ride['total']['grand_fare'], 2); ?></span>
                                            <span class="admin-currency">&nbsp;<?php #echo $dcurrencySymbol;               ?>&nbsp;</span>
                                        </td>
										<td>
											<?php
											$tipsAmt = 0;
											if(isset($ride['total']['tips_amount'])){ 
												$tipsAmt = $ride['total']['tips_amount'];
											}
											?>
                                            <span class="amt-right"><?php echo number_format($tipsAmt, 2); ?></span>
                                            <span class="admin-currency">&nbsp;<?php #echo $dcurrencySymbol;               ?>&nbsp;</span>
                                        </td>
                                        <td>
                                            <span class="amt-right"><?php echo number_format($ride['total']['coupon_discount'], 2); ?></span>
                                        </td>
                                        <td>
                                            <span class="amt-right"><?php echo number_format($amount_in_site, 2); ?></span>
                                        </td>
                                        <td>
                                            <span class="amt-right"><?php echo number_format($amount_in_driver, 2); ?></span>
                                        </td>
                                        <td>
                                            <span class="amt-right"><?php echo number_format($ride['amount_commission'], 2); ?></span>
                                        </td>
                                        <td>
                                            <span class="amt-right"><?php echo number_format($driver_earnings, 2); ?></span>
                                        </td>
                                    </tr>
                                    <?php
                                    $total_grand_fare+=$ride['total']['grand_fare'];
                                    $total_coupon_discount+=$ride['total']['coupon_discount'];
                                    $total_amount_in_site+=$amount_in_site;
                                    $total_amount_in_driver+=$amount_in_driver;
                                    $total_site_earnings+=$ride['amount_commission'];
                                    $total_driver_earnings+=$driver_earnings;
									if(isset($ride['total']['tips_amount'])){
										$total_driver_tips+=$ride['total']['tips_amount'];
									}
                                    ?>
                                <?php } ?>
                            </tbody>
                            <tfoot>
                                <tr>
                                    <th colspan='4'> <?php if ($this->lang->line('admin_site_earnings_trip_summary') != '') echo stripslashes($this->lang->line('admin_site_earnings_trip_summary')); else echo 'Trip Summary'; ?>: </th>
                                    <th>
                                        <span class="amt-right"><?php echo number_format($total_grand_fare, 2); ?></span>
                                        <span class="admin-currency">&nbsp;<?php echo $dcurrencySymbol; ?>&nbsp;</span>
                                    </th>
									<th>
                                        <span class="amt-right"><?php echo number_format($total_driver_tips, 2); ?></span>
                                        <span class="admin-currency">&nbsp;<?php echo $dcurrencySymbol; ?>&nbsp;</span>
                                    </th>
                                    <th>
                                        <span class="amt-right"><?php echo number_format($total_coupon_discount, 2); ?></span>
                                        <span class="admin-currency">&nbsp;<?php echo $dcurrencySymbol; ?>&nbsp;</span>
                                    </th>
                                    <th>
                                        <span class="amt-right"><?php echo number_format($total_amount_in_site, 2); ?></span>
                                        <span class="admin-currency">&nbsp;<?php echo $dcurrencySymbol; ?>&nbsp;</span>
                                    </th>
                                    <th>
                                        <span class="amt-right"><?php echo number_format($total_amount_in_driver, 2); ?></span>
                                        <span class="admin-currency">&nbsp;<?php echo $dcurrencySymbol; ?>&nbsp;</span>
                                    </th>
                                    <th>
                                        <span class="amt-right"><?php echo number_format($total_site_earnings, 2); ?></span>
                                        <span class="admin-currency">&nbsp;<?php echo $dcurrencySymbol; ?>&nbsp;</span>
                                    </th>
                                    <th>
                                        <span class="amt-right"><?php echo number_format($total_driver_earnings, 2); ?></span>
                                        <span class="admin-currency">&nbsp;<?php echo $dcurrencySymbol; ?>&nbsp;</span>
                                    </th>
                                </tr>
                            </tfoot>
                        </table>
						<?php if(!empty($bill_details)){ ?>
                        <table class="custom-table grid_5 right">
                            <thead>
                                <tr>
                                    <th></th>
                                    <th><?php if ($this->lang->line('admin_site_earnings_site_owner') != '') echo stripslashes($this->lang->line('admin_site_earnings_site_owner')); else echo 'Site Owner'; ?></th>
                                    <th><?php if ($this->lang->line('admin_site_earnings_driver') != '') echo stripslashes($this->lang->line('admin_site_earnings_driver')); else echo 'Driver'; ?></th>
                                </tr>
                            </thead>
                            <tbody>
                                <tr>
                                    <td><?php if ($this->lang->line('admin_site_earnings_payment_received') != '') echo stripslashes($this->lang->line('admin_site_earnings_payment_received')); else echo 'Payment Received'; ?></td>
                                    <td>
                                        <?php $payment_in_site = $total_coupon_discount + $total_amount_in_site; ?>
                                        <span class="amt-right"><?php echo number_format($payment_in_site, 2); ?></span>
                                        <span class="admin-currency">&nbsp;<?php echo $dcurrencySymbol; ?>&nbsp;</span>
                                    </td>
                                    <td>
                                        <span class="amt-right"><?php echo number_format($total_amount_in_driver, 2); ?></span>
                                        <span class="admin-currency">&nbsp;<?php echo $dcurrencySymbol; ?>&nbsp;</span>
                                    </td>
                                </tr>
                                <tr>
                                    <td><?php if ($this->lang->line('admin_site_earnings_earnings') != '') echo stripslashes($this->lang->line('admin_site_earnings_earnings')); else echo 'Earnings'; ?></td>
                                    <td>
                                        <span class="amt-right"><?php echo number_format($total_site_earnings, 2); ?></span>
                                        <span class="admin-currency">&nbsp;<?php echo $dcurrencySymbol; ?>&nbsp;</span>
                                    </td>
                                    <td>
                                        <span class="amt-right"><?php echo number_format($total_driver_earnings, 2); ?></span>
                                        <span class="admin-currency">&nbsp;<?php echo $dcurrencySymbol; ?>&nbsp;</span>
                                    </td>
                                </tr>
                                <tr>
                                    <td><?php if ($this->lang->line('admin_site_earnings_payment_due') != '') echo stripslashes($this->lang->line('admin_site_earnings_payment_due')); else echo 'Payment Due'; ?></td>
                                    <td>
                                        <?php
                                        /* $site_due = $payment_in_site - $total_site_earnings;
                                        if ($site_due < 0) {
                                            $site_due = 0;
                                        } */
                                        ?>
                                        <span class="amt-right"><?php echo number_format($bill_details['site_pay_amount'], 2); ?></span>
                                        <span class="admin-currency">&nbsp;<?php echo $dcurrencySymbol; ?>&nbsp;</span>
                                    </td>
                                    <td>

                                        <?php
										/*  $driver_due = $total_amount_in_driver - $total_driver_earnings;
                                        if ($driver_due < 0) {
                                            $driver_due = 0;
                                        } */
                                        ?>
                                        <span class="amt-right"><?php echo number_format($bill_details['driver_pay_amount'], 2); ?></span>
                                        <span class="admin-currency">&nbsp;<?php echo $dcurrencySymbol; ?>&nbsp;</span>
                                    </td>
                                </tr>
                            </tbody>
                            <tfoot>
                                <tr>
                                    <th></th>
                                    <th>
                                        <?php if (isset($bill_details['site_need_to_pay'])) { ?>
                                        <?php if ($bill_details['site_need_to_pay']=='Yes') { ?>
											<?php if ($bill_details['site_paid']=='No') { ?>
											<?php if (((isset($revenue) && is_array($revenue)) && (in_array('1', $revenue) && in_array('2', $revenue) && in_array('3', $revenue))) || $allPrev == '1') { ?>
                                            <a href="#" class="dialog-modal" data-content="payment_paid">
                                                <span class="badge_style b_done" ><?php if ($this->lang->line('admin_site_earnings_update_payment') != '') echo stripslashes($this->lang->line('admin_site_earnings_update_payment')); else echo 'Update Payment'; ?></span>
                                            </a>
											<?php }else{ ?>
											<?php if ($this->lang->line('admin_site_earnings_pending') != '') echo stripslashes($this->lang->line('admin_site_earnings_pending')); else echo 'Pending'; ?>
											<?php } ?>
											<?php } ?>
											<?php if ($bill_details['site_paid']=='Yes') { ?>
                                            <?php if ($this->lang->line('admin_site_earnings_paid') != '') echo stripslashes($this->lang->line('admin_site_earnings_paid')); else echo 'Paid'; ?>
											<?php } ?>
                                        <?php } ?>
                                        <?php } ?>
                                    </th>
                                    <th>										
                                        <?php if (isset($bill_details['driver_need_to_payment'])) { ?>
											<?php if ($bill_details['driver_need_to_payment']=='Yes') { ?>
											<?php if ($bill_details['driver_paid']=='No') { ?>
                                            <a href="#" class="dialog-modal" data-content="payment_received">
                                                <span class="badge_style b_pending" ><?php if ($this->lang->line('admin_site_earnings_update_payment') != '') echo stripslashes($this->lang->line('admin_site_earnings_update_payment')); else echo 'Update Payment'; ?></span>
                                            </a>
											<?php } ?>
											<?php if ($bill_details['driver_paid']=='Yes') { ?>
											<?php if ($this->lang->line('admin_site_earnings_paid') != '') echo stripslashes($this->lang->line('admin_site_earnings_paid')); else echo 'Paid'; ?>
											<?php } ?>
                                        <?php } ?>
                                        <?php } ?>

                                    </th>
                                </tr>
                            </tfoot>
                        </table>
						<?php } ?>

                    <?php } else { ?>
                        <h3> <?php if ($this->lang->line('admin_site_earnings_no_trip_between_this_date') != '') echo stripslashes($this->lang->line('admin_site_earnings_no_trip_between_this_date')); else echo 'No trips between this dates'; ?></h3>
                    <?php } ?>
                </div>
            </div>
        </div>
        <input type="hidden" name="statusMode" id="statusMode"/>
        <input type="hidden" name="SubAdminEmail" id="SubAdminEmail"/>
        </form>	
		<?php if (isset($bill_details['site_need_to_pay'])) { ?>
		<?php if ($bill_details['site_need_to_pay']=='Yes') { ?>
		<?php if ($bill_details['site_paid']=='No') { ?>
        <div id="payment_paid" style="display:none;">
            <h3><?php if ($this->lang->line('admin_site_earnings_payment_paid') != '') echo stripslashes($this->lang->line('admin_site_earnings_payment_paid')); else echo 'Payment Paid'; ?></h3>
            <form action="<?php echo COMPANY_NAME; ?>/revenue/transaction/paid" method="post" class="form_container left_label"  id="payment_paid_form">
				<input name="invoice_id"  id="invoice_id" value="<?php echo $bill_details['invoice_id']; ?>" type="hidden"  />
                <table>
                    <tr>
                        <td><code><?php if ($this->lang->line('admin_site_earnings_transaction_id') != '') echo stripslashes($this->lang->line('admin_site_earnings_transaction_id')); else echo 'Transaction ID'; ?></code></td>
                        <td>
                            <input id="paid_transaction_id" class="required" type="text"  name="transaction_id" >
                        </td>
                    </tr>
                    <tr>
                        <td><code><?php if ($this->lang->line('admin_site_earnings_date') != '') echo stripslashes($this->lang->line('admin_site_earnings_date')); else echo 'Date'; ?></code></td>
                        <td>
                            <input id="paid_date" class="required datepicker_sd" readonly="readonly" type="text"  name="paid_date" >
                        </td>
                    </tr>
                    <tr>
                        <td><code><?php if ($this->lang->line('admin_users_details') != '') echo stripslashes($this->lang->line('admin_users_details')); else echo 'Details'; ?></code></td>
                        <td>
                            <textarea id="paid_details" class="" type="text"  name="paid_details" ></textarea>
                        </td>
                    </tr>
                    <tr>
                        <td>
                            <button type="submit" class="btn b_done" >
                               <?php if ($this->lang->line('admin_site_earnings_update_payment') != '') echo stripslashes($this->lang->line('admin_site_earnings_update_payment')); else echo 'Update Payment'; ?> 
                            </button>
                        </td>
                    </tr>
                </table>
            </form>
        </div>
		<?php } ?>
		<?php if ($bill_details['site_paid']=='Yes') { ?>
		<?php } ?>
		<?php } ?>
		<?php } ?>
		
		<?php if (isset($bill_details['driver_need_to_payment'])) { ?>
		<?php if ($bill_details['driver_need_to_payment']=='Yes') { ?>
		<?php if ($bill_details['driver_paid']=='No') { ?>
        <div id="payment_received" style="display:none;">
            <h3><?php if ($this->lang->line('admin_site_earnings_payment_received') != '') echo stripslashes($this->lang->line('admin_site_earnings_payment_received')); else echo 'Payment Received'; ?></h3>
            <form action="<?php echo COMPANY_NAME; ?>/revenue/transaction/received" method="post" class="form_container left_label" id="payment_received_form">
				<input name="invoice_id"  id="invoice_id" value="<?php echo $bill_details['invoice_id']; ?>" type="hidden"  />
                <table>
                    <tr>
                        <td><code><?php if ($this->lang->line('admin_site_earnings_transaction_id') != '') echo stripslashes($this->lang->line('admin_site_earnings_transaction_id')); else echo 'Transaction ID'; ?></code></td>
                        <td>
                            <input id="received_transaction_id" class="required" type="text"  name="transaction_id" >
                        </td>
                    </tr>
                    <tr>
                        <td><code><?php if ($this->lang->line('admin_site_earnings_date') != '') echo stripslashes($this->lang->line('admin_site_earnings_date')); else echo 'Date'; ?></code></td>
                        <td>
                            <input id="received_date" class="required datepicker_sd" readonly="readonly" type="text"  name="received_date" >
                        </td>
                    </tr>
                    <tr>
                        <td><code><?php if ($this->lang->line('admin_users_details') != '') echo stripslashes($this->lang->line('admin_users_details')); else echo 'Details'; ?></code></td>
                        <td>
                            <textarea id="received_details" class="" type="text"  name="received_details" ></textarea>
                        </td>
                    </tr>
                    <tr>
                        <td>
                            <button type="submit" class="btn b_done" >
                                <?php if ($this->lang->line('admin_site_earnings_update_payment') != '') echo stripslashes($this->lang->line('admin_site_earnings_update_payment')); else echo 'Update Payment'; ?>
                            </button>
                        </td>
                    </tr>
                </table>
            </form>
        </div>
		<?php } ?>
		<?php if ($bill_details['driver_paid']=='Yes') { ?>
		<?php } ?>
		<?php } ?>
		<?php } ?>
    </div>
    <span class="clear"></span>
</div>

<link rel="stylesheet" type="text/css" media="all" href="plugins/timepicker/jquery-ui-timepicker-addon.css" />
<link rel="stylesheet" type="text/css" media="all" href="plugins/timepicker/jquery-ui-timepicker-addon.min.css" />
<script type="text/javascript" src="plugins/timepicker/jquery-ui-timepicker-addon.js"></script>
<script type="text/javascript" src="plugins/timepicker/jquery-ui-sliderAccess.js"></script>
<?php
$this->load->view(COMPANY_NAME.'/templates/footer.php');
?>