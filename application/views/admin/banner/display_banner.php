<?php
$this->load->view(ADMIN_ENC_URL.'/templates/header.php');
extract($privileges);
?>
<div id="content">
    <div class="grid_container">
        <?php
        $attributes = array('id' => 'display_form');
        echo form_open(ADMIN_ENC_URL.'/banner/change_banner_status_global', $attributes)
        ?>
        <div class="grid_12">
            <div class="widget_wrap">
                <div class="widget_top">
                    <span class="h_icon blocks_images"></span>
                    <h6><?php echo $heading ?></h6>
                    <div style="float: right;line-height:40px;padding:0px 10px;height:39px;">
                        <?php if ($allPrev == '1' || in_array('1', $banner)) { ?>

                            <div class="btn_30_light" style="height: 29px; text-align:left;">
                                <a href="<?php echo ADMIN_ENC_URL;?>/banner/add_banner_form" class="tipTop" title="<?php if ($this->lang->line('admin_banner_add_new_banner') != '') echo stripslashes($this->lang->line('admin_banner_add_new_banner')); else echo 'Click here to Add New Banner'; ?>"><span class="icon add_co addnew-btn2"></span><!-- <span class="btn_link"><?php if ($this->lang->line('admin_common_add_new') != '') echo stripslashes($this->lang->line('admin_common_add_new')); else echo 'Add New'; ?></span> --></a>
                            </div>


                        <?php } ?>
                        <?php if ($allPrev == '1' || in_array('2', $banner)) { ?>
                            <div class="btn_30_light" style="height: 29px;">
                                <a href="javascript:void(0)" onclick="return checkBoxValidationAdmin('Publish', '<?php echo $subAdminMail; ?>');" class="tipTop" title="<?php if ($this->lang->line('driver_select_publish_records') != '') echo stripslashes($this->lang->line('driver_select_publish_records')); else echo 'Select any checkbox and click here to publish records'; ?>"><!-- <span class="icon accept_co"></span> --><span class="btn_link act-btn"><?php if ($this->lang->line('admin_common_publish') != '') echo stripslashes($this->lang->line('admin_common_publish')); else echo 'Publish'; ?></span></a>
                            </div>
                            <div class="btn_30_light" style="height: 29px;">
                                <a href="javascript:void(0)" onclick="return checkBoxValidationAdmin('Unpublish', '<?php echo $subAdminMail; ?>');" class="tipTop" title="<?php if ($this->lang->line('driver_select_unpublish_records') != '') echo stripslashes($this->lang->line('driver_select_unpublish_records')); else echo 'Select any checkbox and click here to unpublish records'; ?>"><!-- <span class="icon delete_co "></span> --><span class="btn_link inact-btn"><?php if ($this->lang->line('admin_common_unpublish') != '') echo stripslashes($this->lang->line('admin_common_unpublish')); else echo 'Unpublish'; ?></span></a>
                            </div>
                            <?php
                        }
                        if ($allPrev == '1' || in_array('3', $banner)) {
                            ?>
                            <div class="btn_30_light" style="height: 29px;">
                                <a href="javascript:void(0)" onclick="return checkBoxValidationAdmin('Delete', '<?php echo $subAdminMail; ?>');" class="tipTop" title="<?php if ($this->lang->line('common_select_delete_records') != '') echo stripslashes($this->lang->line('common_select_delete_records')); else echo 'Select any checkbox and click here to delete records'; ?>"><!--<span class="icon cross_co del-btn"></span>--><span class="btn_link"><?php if ($this->lang->line('admin_common_delete') != '') echo stripslashes($this->lang->line('admin_common_delete')); else echo 'Delete'; ?></span></a>
                            </div>
                        <?php } ?>
                    </div>
                </div>
                <div class="widget_content">
                    <table class="display display_tbl" id="banner_tbl">
                        <thead>
                            <tr>
                                <th class="center">
                                    <input name="checkbox_id[]" type="checkbox" value="on" class="checkall">
                                </th>
                                <th class="tip_top" title="<?php if ($this->lang->line('dash_click_sort') != '') echo stripslashes($this->lang->line('dash_click_sort')); else echo 'Click to sort'; ?>">
                                    <?php if ($this->lang->line('admin_banner_banner_name') != '') echo stripslashes($this->lang->line('admin_banner_banner_name')); else echo 'Banner Name'; ?>
                                </th>
                                <th class="tip_top" title="<?php if ($this->lang->line('dash_click_sort') != '') echo stripslashes($this->lang->line('dash_click_sort')); else echo 'Click to sort'; ?>">
                                    <?php if ($this->lang->line('admin_banner_banner_title') != '') echo stripslashes($this->lang->line('admin_banner_banner_title')); else echo 'Banner Title'; ?>
                                </th>
                                <th>
                                    <?php if ($this->lang->line('admin_banner_banner_image') != '') echo stripslashes($this->lang->line('admin_banner_banner_image')); else echo 'Banner Image'; ?>
                                </th>
                                <th class="tip_top" title="<?php if ($this->lang->line('dash_click_sort') != '') echo stripslashes($this->lang->line('dash_click_sort')); else echo 'Click to sort'; ?>">
                                    <?php if ($this->lang->line('admin_common_status') != '') echo stripslashes($this->lang->line('admin_common_status')); else echo 'Status'; ?>
                                </th>
                                <th>
                                    <?php if ($this->lang->line('admin_common_action') != '') echo stripslashes($this->lang->line('admin_common_action')); else echo 'Action'; ?>
                                </th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php
                            if ($bannerList->num_rows() > 0) {
                                foreach ($bannerList->result() as $row) {
                                    ?>
                                    <tr>
                                        <td class="center tr_select ">
                                            <input name="checkbox_id[]" type="checkbox" value="<?php echo $row->_id; ?>">
                                        </td>
                                        <td class="center">
                                            <?php if (isset($row->name) && $row->name !='' ){ echo $row->name; }else{ echo "Not Available"; } ?>
                                        </td>
                                        <td class="center">
                                            <?php if (isset($row->banner_title)  && $row->banner_title !='' ){ echo $row->banner_title;}else{ echo "Not Available"; } ?>
                                        </td>
                                        <td class="center">
                                            <?php if (isset($row->image) && $row->image!='' ) { ?>
                                                <img src="images/banner/<?php echo $row->image; ?>" width="100"/>
                                            <?php }else{
                                             echo "Not Available";
                                             } ?>
                                        </td>
                                        <td class="center">
                                            <?php if (isset($row->status)) { ?>
                                                <?php
												$disp_status = get_language_value_for_keyword($row->status,$this->data['langCode']);
                                                if ($allPrev == '1' || in_array('2', $banner)) {
                                                    $mode = ($row->status == 'Publish') ? '0' : '1';
                                                    if ($mode == '0') {
                                                        ?>
                                                        <a title="<?php if ($this->lang->line('admin_click_to_unpublish') != '') echo stripslashes($this->lang->line('admin_click_to_unpublish')); else echo 'Click to unpublish'; ?>" class="tip_top" href="javascript:confirm_status('<?php echo ADMIN_ENC_URL;?>/banner/change_banner_status/<?php echo $mode; ?>/<?php echo $row->_id; ?>');"><span class="badge_style b_done"><?php echo $disp_status; ?></span></a>
                                                        <?php
                                                    } else {
                                                        ?>
                                                        <a title="<?php if ($this->lang->line('admin_click_to_publish') != '') echo stripslashes($this->lang->line('admin_click_to_publish')); else echo 'Click to publish'; ?>" class="tip_top" href="javascript:confirm_status('<?php echo ADMIN_ENC_URL;?>/banner/change_banner_status/<?php echo $mode; ?>/<?php echo $row->_id; ?>')"><span class="badge_style"><?php echo $disp_status; ?></span></a>
                                                        <?php
                                                    }
                                                } else {
                                                    ?>
                                                    <span class="badge_style b_done"><?php echo $disp_status; ?></span>
                                                <?php } ?>
                                            </td>
                                            </td>
                                            <td class="center action-icons-wrap">
                                                <?php if ($allPrev == '1' || in_array('2', $banner)) { ?>
                                                    <span><a class="action-icons c-edit" href="<?php echo ADMIN_ENC_URL;?>/banner/edit_banner/<?php echo $row->_id; ?>" title="<?php if ($this->lang->line('admin_common_edit') != '') echo stripslashes($this->lang->line('admin_common_edit')); else echo 'Edit'; ?>"><?php if ($this->lang->line('admin_common_edit') != '') echo stripslashes($this->lang->line('admin_common_edit')); else echo 'Edit'; ?></a></span>
                                                <?php } ?>
                                                <?php if ($allPrev == '1' || in_array('3', $banner)) { ?>	
                                                    <span><a class="action-icons c-delete" href="javascript:confirm_delete('<?php echo ADMIN_ENC_URL;?>/banner/delete_banner/<?php echo $row->_id; ?>')" title="<?php if ($this->lang->line('admin_common_delete') != '') echo stripslashes($this->lang->line('admin_common_delete')); else echo 'Delete'; ?>"><?php if ($this->lang->line('admin_common_delete') != '') echo stripslashes($this->lang->line('admin_common_delete')); else echo 'Delete'; ?></a></span>
                                                <?php
                                                }
                                            }
                                            ?>
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
                                    <?php if ($this->lang->line('admin_banner_banner_name') != '') echo stripslashes($this->lang->line('admin_banner_banner_name')); else echo 'Banner Name'; ?>
                                </th>
                                <th>
                                    <?php if ($this->lang->line('admin_banner_banner_title') != '') echo stripslashes($this->lang->line('admin_banner_banner_title')); else echo 'Banner Title'; ?>
                                </th>
                                <th>
                                     <?php if ($this->lang->line('admin_banner_banner_image') != '') echo stripslashes($this->lang->line('admin_banner_banner_image')); else echo 'Banner Image'; ?>
                                </th>
                                <th>
                                    <?php if ($this->lang->line('admin_common_status') != '') echo stripslashes($this->lang->line('admin_common_status')); else echo 'Status'; ?>
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