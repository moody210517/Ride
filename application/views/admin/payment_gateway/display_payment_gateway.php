<?php
$this->load->view(ADMIN_ENC_URL.'/templates/header.php');
extract($privileges);
?>
<div id="content" class="disply_payment_sec">
    <div class="grid_container">
        <?php
        $attributes = array('id' => 'display_form');
        echo form_open(ADMIN_ENC_URL.'/payment_gateway/change_payment_gateway_status_global', $attributes)
        ?>
        <div class="grid_12">
            <div class="widget_wrap">
                <div class="widget_top">
                    <span class="h_icon blocks_images"></span>
                    <h6><?php echo $heading ?></h6>
                    <div style="float: right;line-height:40px;padding:0px 10px;height:39px;">
                        <?php
                        if ($allPrev == '1' || in_array('2', $payment_gateway)) {
                            ?>
                            <div class="btn_30_light" style="height: 29px;">
                                <a href="javascript:void(0)" onclick="return checkBoxValidationAdmin('Enable', '<?php echo $subAdminMail; ?>');" class="tipTop" title="<?php if ($this->lang->line('admin_payment_gateway_enable_records') != '') echo stripslashes($this->lang->line('admin_payment_gateway_enable_records')); else echo 'Select any checkbox and click here to enable records'; ?>"><!-- <span class="icon active"></span> --><span class="btn_link act-btn"><?php if ($this->lang->line('admin_common_enable') != '') echo stripslashes($this->lang->line('admin_common_enable')); else echo 'Enable'; ?></span></a>
                            </div>
                            <div class="btn_30_light" style="height: 29px;">
                                <a href="javascript:void(0)" onclick="return checkBoxValidationAdmin('Disable', '<?php echo $subAdminMail; ?>');" class="tipTop" title="<?php if ($this->lang->line('admin_payment_gateway_disable_records') != '') echo stripslashes($this->lang->line('admin_payment_gateway_disable_records')); else echo 'Select any checkbox and click here to disable records'; ?>"><!-- <span class="icon delete_co"></span> --><span class="btn_link inact-btn"><?php if ($this->lang->line('admin_common_disable') != '') echo stripslashes($this->lang->line('admin_common_disable')); else echo 'Disable'; ?></span></a>
                            </div>
                        <?php } ?>
                    </div>
                </div>
                <div class="widget_content">
                    <table class="display display_tbl" id="gateway_tbl">
                        <thead>
                            <tr>
                                <th class="center">
                                    <input name="checkbox_id[]" type="checkbox" value="on" class="checkall">
                                </th>
                                <th class="tip_top" title="<?php if ($this->lang->line('dash_click_sort') != '') echo stripslashes($this->lang->line('dash_click_sort')); else echo 'Click to sort'; ?>">
                                    <?php if ($this->lang->line('admin_payment_gateway_gateway_name') != '') echo stripslashes($this->lang->line('admin_payment_gateway_gateway_name')); else echo 'Gateway Name'; ?>
                                </th>
                                <th class="tip_top" title="<?php if ($this->lang->line('dash_click_sort') != '') echo stripslashes($this->lang->line('dash_click_sort')); else echo 'Click to sort'; ?>">
                                    <?php if ($this->lang->line('admin_common_status') != '') echo stripslashes($this->lang->line('admin_common_status')); else echo 'Status'; ?>
                                </th>
                                <?php if ($allPrev == '1' || in_array('2', $payment_gateway)) { ?>
                                    <th>
                                        <?php if ($this->lang->line('admin_common_action') != '') echo stripslashes($this->lang->line('admin_common_action')); else echo 'Action'; ?>
                                    </th>
                                <?php } ?>
                            </tr>
                        </thead>
                        <tbody>
                            <?php
                            if ($gatewayLists->num_rows() > 0) {
                                foreach ($gatewayLists->result() as $row) {
                                    ?>
                                    <tr>
                                        <td class="center tr_select ">
                                            <input name="checkbox_id[]" type="checkbox" value="<?php echo $row->_id; ?>">
                                        </td>
                                        <td class="center">
                                            <?php echo $row->gateway_name; ?>
                                        </td>
                                        <td class="center">
                                            <?php
											$disp_status = get_language_value_for_keyword($row->status,$this->data['langCode']);
                                            if ($allPrev == '1' || in_array('2', $payment_gateway)) {
                                                $mode = ($row->status == 'Enable') ? '0' : '1';
                                                if ($mode == '0') {
                                                    ?>
                                                    <a title="<?php if ($this->lang->line('admin_payment_gateway_click_disable') != '') echo stripslashes($this->lang->line('admin_payment_gateway_click_disable')); else echo 'Click to disable'; ?>" class="tip_top" href="javascript:confirm_status('<?php echo ADMIN_ENC_URL;?>/payment_gateway/change_gateway_status/<?php echo $mode; ?>/<?php echo $row->_id; ?>');"><span class="badge_style b_done"><?php echo $disp_status; ?></span></a>
                                                    <?php
                                                } else {
                                                    ?>
                                                    <a title="<?php if ($this->lang->line('admin_payment_gateway_click_enable') != '') echo stripslashes($this->lang->line('admin_payment_gateway_click_enable')); else echo 'Click to enable'; ?>" class="tip_top" href="javascript:confirm_status('<?php echo ADMIN_ENC_URL;?>/payment_gateway/change_gateway_status/<?php echo $mode; ?>/<?php echo $row->_id; ?>')"><span class="badge_style"><?php echo $disp_status; ?></span></a>
                                                    <?php
                                                }
                                            } else {
                                                ?>
                                                <span class="badge_style b_done"><?php echo $disp_status; ?></span>
                                            <?php } ?>
                                        </td>
                                        <?php if ($allPrev == '1' || in_array('2', $payment_gateway)) { ?>
                                            <td class="center">
                                                <ul class="action_list"><li style="width:100%;"><a class="p_edit tipTop" href="<?php echo ADMIN_ENC_URL;?>/payment_gateway/edit_gateway_form/<?php echo $row->_id; ?>" title="<?php if ($this->lang->line('admin_payment_gateway_edit_details') != '') echo stripslashes($this->lang->line('admin_payment_gateway_edit_details')); else echo 'Edit Details'; ?>"><?php if ($this->lang->line('admin_payment_gateway_edit_details') != '') echo stripslashes($this->lang->line('admin_payment_gateway_edit_details')); else echo 'Edit Details'; ?></a></li></ul>
                                            </td>
                                        <?php } ?>
                                    </tr>
                                    <?php
                                }
                            }
                            ?>

                        </tbody>
                        <tfoot>
                            <tr>
                                <th class="center">
                                    <input name="checkbox_id[]" type="checkbox" value="on" class="checkall">
                                </th>
                                <th>
                                    <?php if ($this->lang->line('admin_payment_gateway_gateway_name') != '') echo stripslashes($this->lang->line('admin_payment_gateway_gateway_name')); else echo 'Gateway Name'; ?>
                                </th>
                                <th>
                                    <?php if ($this->lang->line('admin_common_status') != '') echo stripslashes($this->lang->line('admin_common_status')); else echo 'Status'; ?>
                                </th>
                                <?php if ($allPrev == '1' || in_array('2', $payment_gateway)) { ?>
                                    <th>
                                       <?php if ($this->lang->line('admin_common_action') != '') echo stripslashes($this->lang->line('admin_common_action')); else echo 'Action'; ?> 
                                    </th>
                                <?php } ?>
                            </tr>
                        </tfoot>
                    </table>


                    <br/><br/><br/>
                    <div class="widget_top">
                        <span class="h_icon blocks_images"></span>
                        <h6><?php if ($this->lang->line('admin_payment_gateway_other_payment_options') != '') echo stripslashes($this->lang->line('admin_payment_gateway_other_payment_options')); else echo 'Other Payment Options'; ?></h6>
                    </div>
                    <table class="display display_tbl double-col-only" id="payment_tbl">
                        <thead>
                            <tr>
                                <th class="tip_top" title="<?php if ($this->lang->line('dash_click_sort') != '') echo stripslashes($this->lang->line('dash_click_sort')); else echo 'Click to sort'; ?>">
                                    <?php if ($this->lang->line('admin_payment_gateway_gateway_name') != '') echo stripslashes($this->lang->line('admin_payment_gateway_gateway_name')); else echo 'Gateway Name'; ?>
                                </th>
                                <th class="tip_top" title="<?php if ($this->lang->line('dash_click_sort') != '') echo stripslashes($this->lang->line('dash_click_sort')); else echo 'Click to sort'; ?>">
                                    <?php if ($this->lang->line('admin_common_status') != '') echo stripslashes($this->lang->line('admin_common_status')); else echo 'Status'; ?>
                                </th>
                            </tr>
                        </thead>
                        <tbody>						
                            <!-- pay by cash   -->
                            <tr>
                                <td class="center">
                                    <?php if ($this->lang->line('admin_payment_gateway_pay_by_cash') != '') echo stripslashes($this->lang->line('admin_payment_gateway_pay_by_cash')); else echo 'Pay by cash'; ?>
                                </td>
                                <td class="center">
                                    <?php
                                    $disp_pay_by_cash = get_language_value_for_keyword('Disable',$this->data['langCode']);
                                    $pay_by_cash = 'Disable';
                                    if ($this->config->item('pay_by_cash') != '') {
										$pay_by_cash = $this->config->item('pay_by_cash');
										$disp_pay_by_cash = get_language_value_for_keyword($this->config->item('pay_by_cash'),$this->data['langCode']);
                                    }
                                    if ($allPrev == '1' || in_array('2', $payment_gateway)) {
                                        $mode = ($pay_by_cash == 'Enable') ? '0' : '1';
                                        if ($mode == '0') {
                                            ?>
                                            <a title="<?php if ($this->lang->line('admin_payment_gateway_click_disable') != '') echo stripslashes($this->lang->line('admin_payment_gateway_click_disable')); else echo 'Click to disable'; ?>" class="tip_top" href="javascript:confirm_status('<?php echo ADMIN_ENC_URL;?>/payment_gateway/pay_by_cash_status/<?php echo $mode; ?>');">
                                                <span class="badge_style b_done"><?php echo $disp_pay_by_cash; ?></span>
                                            </a>
                                            <?php
                                        } else {
                                            ?>
                                            <a title="<?php if ($this->lang->line('admin_payment_gateway_click_enable') != '') echo stripslashes($this->lang->line('admin_payment_gateway_click_enable')); else echo 'Click to enable'; ?>" class="tip_top" href="javascript:confirm_status('<?php echo ADMIN_ENC_URL;?>/payment_gateway/pay_by_cash_status/<?php echo $mode; ?>')">
                                                <span class="badge_style"><?php echo $disp_pay_by_cash; ?></span>
                                            </a>
                                            <?php
                                        }
                                    } else {
                                        ?>
                                        <span class="badge_style b_done"><?php echo $disp_pay_by_cash; ?></span>
                                    <?php } ?>
                                </td>
                            </tr>
                            <!-- pay by cash    -->					
                            <!-- use wallet amount   -->
                            <tr>
                                <td class="center">
                                    <?php if ($this->lang->line('admin_payment_gateway_use_wallet_amount') != '') echo stripslashes($this->lang->line('admin_payment_gateway_use_wallet_amount')); else echo 'Use Wallet Amount'; ?>
                                </td>
                                <td class="center">
                                    <?php
                                    $use_wallet_amount = 'Disable';
                                    $disp_use_wallet_amount = get_language_value_for_keyword('Disable',$this->data['langCode']);
                                    if ($this->config->item('use_wallet_amount') != '') {
                                        $use_wallet_amount = $this->config->item('use_wallet_amount');
                                        $disp_use_wallet_amount = get_language_value_for_keyword($this->config->item('use_wallet_amount'),$this->data['langCode']);
                                    }
                                    if ($allPrev == '1' || in_array('2', $payment_gateway)) {
                                        $mode = ($use_wallet_amount == 'Enable') ? '0' : '1';
                                        if ($mode == '0') {
                                            ?>
                                            <a title="<?php if ($this->lang->line('admin_payment_gateway_click_disable') != '') echo stripslashes($this->lang->line('admin_payment_gateway_click_disable')); else echo 'Click to disable'; ?>" class="tip_top" href="javascript:confirm_status('<?php echo ADMIN_ENC_URL;?>/payment_gateway/use_wallet_amount_status/<?php echo $mode; ?>');">
                                                <span class="badge_style b_done"><?php echo $disp_use_wallet_amount; ?></span>
                                            </a>
                                            <?php
                                        } else {
                                            ?>
                                            <a title="<?php if ($this->lang->line('admin_payment_gateway_click_enable') != '') echo stripslashes($this->lang->line('admin_payment_gateway_click_enable')); else echo 'Click to enable'; ?>" class="tip_top" href="javascript:confirm_status('<?php echo ADMIN_ENC_URL;?>/payment_gateway/use_wallet_amount_status/<?php echo $mode; ?>')">
                                                <span class="badge_style"><?php echo $disp_use_wallet_amount; ?></span>
                                            </a>
                                            <?php
                                        }
                                    } else {
                                        ?>
                                        <span class="badge_style b_done"><?php echo $disp_use_wallet_amount; ?></span>
                                    <?php } ?>
                                </td>
                            </tr>
                            <!-- use wallet amount   -->

                        </tbody>
                        <tfoot>
                            <tr>
                                <th>
                                    <?php if ($this->lang->line('admin_payment_gateway_gateway_name') != '') echo stripslashes($this->lang->line('admin_payment_gateway_gateway_name')); else echo 'Gateway Name'; ?>
                                </th>
                                <th>
                                    <?php if ($this->lang->line('admin_common_status') != '') echo stripslashes($this->lang->line('admin_common_status')); else echo 'Status'; ?>
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
</div>
<?php
$this->load->view(ADMIN_ENC_URL.'/templates/footer.php');
?>