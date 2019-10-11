<?php
$this->load->view(ADMIN_ENC_URL.'/templates/header.php');
extract($privileges);
?>
<div id="content">
    <div class="grid_container">
        <?php
        $attributes = array('id' => 'display_form');
        echo form_open(ADMIN_ENC_URL.'/multilanguage/change_multi_language_details', $attributes)
        ?>
        <div class="grid_12">
            <div class="widget_wrap">
                <div class="widget_top">
                    <span class="h_icon blocks_images"></span>
                    <h6><?php echo $heading; ?></h6>
                    <div style="float: right;line-height:40px;padding:0px 10px;height:39px;">
                        <?php if ($allPrev == '1' || in_array('1', $multilang)) { ?>
                            <div class="btn_30_light" style="height: 29px; text-align:left;">
                                <a href="<?php echo ADMIN_ENC_URL;?>/multilanguage/add_new_lg" class="tipTop" title="<?php if ($this->lang->line('admin_language_add_new_language') != '') echo stripslashes($this->lang->line('admin_language_add_new_language')); else echo 'Click here to add new language'; ?>"><span class="icon add_co addnew-btn2"></span><!-- <span class="btn_link"><?php if ($this->lang->line('admin_common_add_new') != '') echo stripslashes($this->lang->line('admin_common_add_new')); else echo 'Add New'; ?></span> --></a>
                            </div>
                        <?php } ?>
                        <?php if ($allPrev == '1' || in_array('2', $multilang)) { ?>
                            <div class="btn_30_light" style="height: 29px;">
                                <a href="javascript:void(0)" onclick="return checkBoxValidationAdmin('Active', '<?php echo $subAdminMail; ?>');" class="tipTop" title="<?php if ($this->lang->line('common_select_active_records') != '') echo stripslashes($this->lang->line('common_select_active_records')); else echo 'Select any checkbox and click here to active records'; ?>"><!-- <span class="icon accept_co"></span> --><span class="btn_link act-btn"><?php if ($this->lang->line('admin_common_active') != '') echo stripslashes($this->lang->line('admin_common_active')); else echo 'Active'; ?></span></a>
                            </div>
                            <div class="btn_30_light" style="height: 29px;">
                                <a href="javascript:void(0)" onclick="return checkBoxValidationAdmin('Inactive', '<?php echo $subAdminMail; ?>');" class="tipTop" title="<?php if ($this->lang->line('driver_select_inactive_records') != '') echo stripslashes($this->lang->line('driver_select_inactive_records')); else echo 'Select any checkbox and click here to inactive records'; ?>"><!-- <span class="icon delete_co"></span> --><span class="btn_link inact-btn"><?php if ($this->lang->line('admin_common_inactive') != '') echo stripslashes($this->lang->line('admin_common_inactive')); else echo 'Inactive'; ?></span></a>
                            </div>
                            <?php
                        }
                        if ($allPrev == '1' || in_array('3', $multilang)) {
                            ?>
                            <div class="btn_30_light" style="height: 29px;">
                                <a href="javascript:void(0)" onclick="return checkBoxValidationAdmin('Delete', '<?php echo $subAdminMail; ?>');" class="tipTop" title="<?php if ($this->lang->line('common_select_delete_records') != '') echo stripslashes($this->lang->line('common_select_delete_records')); else echo 'Select any checkbox and click here to delete records'; ?>"><!--<span class="icon cross_co del-btn"></span>--> <span class="btn_link"><?php if ($this->lang->line('admin_common_delete') != '') echo stripslashes($this->lang->line('admin_common_delete')); else echo 'Delete'; ?></span></a>
                            </div>
                        <?php } ?>
                    </div>

                </div>
                <div class="widget_content">
                    <table class="display" id="language_tbl">
                        <thead>
                            <tr>
                                <th class="center">                          
                                    <input name="checkbox_id[]" type="checkbox" value="on" class="checkall">                               
                                </th>
                                <th class="tip_top" title="<?php if ($this->lang->line('dash_click_sort') != '') echo stripslashes($this->lang->line('dash_click_sort')); else echo 'Click to sort'; ?>">
                                    <?php if ($this->lang->line('admin_multilanguage_language_name') != '') echo stripslashes($this->lang->line('admin_multilanguage_language_name')); else echo 'Language Name'; ?>
                                </th>
                                <th class="tip_top" title="<?php if ($this->lang->line('dash_click_sort') != '') echo stripslashes($this->lang->line('dash_click_sort')); else echo 'Click to sort'; ?>">
									<?php if ($this->lang->line('admin_multilanguage_language_code') != '') echo stripslashes($this->lang->line('admin_multilanguage_language_code')); else echo 'language Code'; ?>
                                </th>
                                <th class="tip_top" title="<?php if ($this->lang->line('dash_click_sort') != '') echo stripslashes($this->lang->line('dash_click_sort')); else echo 'Click to sort'; ?>">
                                    <?php if ($this->lang->line('admin_common_status') != '') echo stripslashes($this->lang->line('admin_common_status')); else echo 'Status'; ?>
                                </th>
                                <th class="tip_top" title="<?php if ($this->lang->line('dash_click_sort') != '') echo stripslashes($this->lang->line('dash_click_sort')); else echo 'Click to sort'; ?>">
                                    <?php if ($this->lang->line('admin_multilanguage_default_language') != '') echo stripslashes($this->lang->line('admin_multilanguage_default_language')); else echo 'Default Language'; ?>
                                </th>						
                                <th>
                                    <?php if ($this->lang->line('admin_common_action') != '') echo stripslashes($this->lang->line('admin_common_action')); else echo 'Action'; ?>
                                </th>                            
                            </tr>
                        </thead>
                        <tbody>
                            <?php
                            if ($language_list->num_rows() > 0) {
                                foreach ($language_list->result() as $row) {
                                    ?>
                                    <tr>
                                        <td class="center tr_select ">
                                            <?php if ($row->lang_code != 'en') { ?>
                                                <input name="checkbox_id[]" type="checkbox" value="<?php echo $row->_id; ?>">
                                            <?php } ?>
                                        </td>							
                                        <td class="center  tr_select">
                                            <?php echo $row->name; ?>
                                        </td>
                                        <td class="center  tr_select">
                                            <?php echo $row->lang_code; ?>
                                        </td>

                                        <td class="center">
                                            <?php
											$disp_status = get_language_value_for_keyword($row->status,$this->data['langCode']);
                                            if ($allPrev == '1' || in_array('2', $multilang)) {
                                                $mode = ($row->status == 'Active') ? '0' : '1';
                                                if ($mode == '0') {
                                                    ?>
                                                    <a title="<?php if ($this->lang->line('common_click_to_inactive') != '') echo stripslashes($this->lang->line('common_click_to_inactive')); else echo 'Click to Inactive'; ?>" class="tip_top" href="javascript:confirm_status('<?php echo ADMIN_ENC_URL;?>/multilanguage/change_language_status/<?php echo $row->status; ?>/<?php echo $row->_id; ?>');"><span class="badge_style b_done"><?php echo $disp_status; ?></span></a>
                                                    <?php
                                                } else {
                                                    ?>
                                                    <a title="<?php if ($this->lang->line('common_click_to_active') != '') echo stripslashes($this->lang->line('common_click_to_active')); else echo 'Click to Active'; ?>" class="tip_top" href="javascript:confirm_status('<?php echo ADMIN_ENC_URL;?>/multilanguage/change_language_status/<?php echo $row->status; ?>/<?php echo $row->_id; ?>')"><span class="badge_style"><?php echo $disp_status; ?></span></a>
                                                    <?php
                                                }
                                            } else {
                                                ?>
                                                <span class="badge_style b_done"><?php echo $disp_status; ?></span>
                                            <?php } ?>
                                        </td>
                                        <td class="center">
                                            <?php
											$dlongStatus = get_language_value_for_keyword('No',$this->data['langCode']);
                                            $mode = '0';
                                            if (isset($row->default_language)) {
                                                $mode = ($row->default_language == 'Yes') ? '0' : '1';
												$dlongStatus = get_language_value_for_keyword($row->default_language,$this->data['langCode']);
                                            }

                                            if ($allPrev == '1' || in_array('2', $multilang)) {

                                                if ($mode == '0') {
                                                    ?>
                                                    <a title="<?php if ($this->lang->line('admin_language_change_to_no') != '') echo stripslashes($this->lang->line('admin_language_change_to_no')); else echo 'Change to No'; ?>" class="tip_top" href="javascript:confirm_status('<?php echo ADMIN_ENC_URL;?>/multilanguage/change_language_default/<?php echo $mode; ?>/<?php echo $row->_id; ?>');"><span class="badge_style b_done"><?php echo $dlongStatus; ?></span></a>
                                                    <?php
                                                } else {
                                                    ?>
                                                    <a title="<?php if ($this->lang->line('admin_language_change_to_yes') != '') echo stripslashes($this->lang->line('admin_language_change_to_yes')); else echo 'Change to Yes'; ?>" class="tip_top" href="javascript:confirm_status('<?php echo ADMIN_ENC_URL;?>/multilanguage/change_language_default/<?php echo $mode; ?>/<?php echo $row->_id; ?>')"><span class="badge_style"><?php echo $dlongStatus; ?></span></a>
                                                    <?php
                                                }
                                            } else {
                                                ?>
                                                <span class="badge_style b_done"><?php echo $dlongStatus; ?></span>
                                            <?php } ?>
                                        </td>

                <!--<td class="center">
                                        <?php
                                        if ($allPrev == '1' || in_array('2', $multilang)) {
                                            $mode = ($row->status == 'Active') ? '0' : '1';
                                            if ($mode == '0') {
                                                ?>
                                        <a title="Click to inactive" class="tip_top" href="javascript:confirm_status('admin/newsletter/change_subscribers_status/<?php echo $mode; ?>/<?php echo $row->_id; ?>');"><span class="badge_style b_done"><?php echo $row->status; ?></span></a>
                                                <?php
                                            } else {
                                                ?>
                                        <a title="Click to active" class="tip_top" href="javascript:confirm_status('admin/newsletter/change_subscribers_status/<?php echo $mode; ?>/<?php echo $row->_id; ?>')"><span class="badge_style"><?php echo $row->status; ?></span></a>
                                                <?php
                                            }
                                        } else {
                                            ?>
                        <span class="badge_style b_done"><?php echo $row->status; ?></span>
                                        <?php } ?>
                </td>-->
                                        <td class="center action-icons-wrap">
                                            <?php if ($row->lang_code != 'en') { ?>
                                                <?php if ($allPrev == '1' || in_array('2', $multilang)) { ?>
                                                    <span><a class="action-icons c-edit" href="<?php echo ADMIN_ENC_URL;?>/multilanguage/edit_language/<?php echo $row->lang_code; ?>/1" title="<?php if ($this->lang->line('admin_common_edit') != '') echo stripslashes($this->lang->line('admin_common_edit')); else echo 'Edit'; ?>"><?php if ($this->lang->line('admin_common_edit') != '') echo stripslashes($this->lang->line('admin_common_edit')); else echo 'Edit'; ?></a></span>
                                                <?php } ?>								 
                                                <?php
                                                if ($allPrev == '1' || in_array('3', $multilang)) {
                                                    $EmailtempId = array('1', '2', '3', '4', '5');
                                                    ?>	
                                                    <span><a class="action-icons c-delete" href="javascript:confirm_delete('<?php echo ADMIN_ENC_URL;?>/multilanguage/delete_language/<?php echo $row->_id; ?>')" title="<?php if ($this->lang->line('admin_common_delete') != '') echo stripslashes($this->lang->line('admin_common_delete')); else echo 'Delete'; ?>"><?php if ($this->lang->line('admin_common_delete') != '') echo stripslashes($this->lang->line('admin_common_delete')); else echo 'Delete'; ?></a></span>
                                                <?php } ?>
                                            <?php } ?>
                                        </td>
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
                                    <?php if ($this->lang->line('admin_multilanguage_language_name') != '') echo stripslashes($this->lang->line('admin_multilanguage_language_name')); else echo 'Language Name'; ?>
                                </th>
                                <th>
                                       <?php if ($this->lang->line('admin_multilanguage_language_code') != '') echo stripslashes($this->lang->line('admin_multilanguage_language_code')); else echo 'language Code'; ?><!--<input name="checkbox_id[]" type="checkbox" value="on" class="checkall">-->
                                </th>
                                <th>
                                    <?php if ($this->lang->line('admin_common_status') != '') echo stripslashes($this->lang->line('admin_common_status')); else echo 'Status'; ?>
                                </th>
                                <th>
                                    <?php if ($this->lang->line('admin_multilanguage_default_language') != '') echo stripslashes($this->lang->line('admin_multilanguage_default_language')); else echo 'Default Language'; ?>
                                </th>
                                <th>
                                    <?php if ($this->lang->line('admin_common_action') != '') echo stripslashes($this->lang->line('admin_common_action')); else echo 'Action'; ?>
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