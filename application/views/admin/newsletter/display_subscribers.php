<?php
$this->load->view(ADMIN_ENC_URL.'/templates/header.php');
extract($privileges);
?>

<div id="content">
  <div class="grid_container">
    <?php 
				$attributes = array('id' => 'display_form');
				echo form_open(ADMIN_ENC_URL.'/templates/change_newsletter_status_global',$attributes) 
			?>
    <div class="grid_12">
      <div class="widget_wrap">
        <div class="widget_top"> <span class="h_icon blocks_images"></span>
          <h6><?php echo $heading?></h6>
          <div style="float: right;line-height:40px;padding:0px 10px;height:39px;">
            <?php if ($allPrev == '1' || in_array('2', $templates)){?>
            <div class="lenghtMenu" style="float:left; margin-top:4px;">
            
              <select id="mail_contents" data-placeholder="<?php if ($this->lang->line('admin_notification_select_email_template') != '') echo stripslashes($this->lang->line('admin_notification_select_email_template')); else echo 'Select Email Template'; ?>" name="mail_contents" style=" width:212px; " class="chzn-select" >
              <option></option>
                <?php 
		  if ($NewsList->num_rows() > 0){
		  foreach ($NewsList->result() as $SendNews){ if($SendNews->news_id >23){?>
                <option value="<?php echo $SendNews->news_id;?>"><?php echo $SendNews->message['title']; ?></option>
                <?php }}} ?>
              </select>
            </div>
            <div class="btn_30_light" style="height: 29px;"> <a href="javascript:void(0)" onclick="return SelectValidationAdmin('SendMailAll','<?php echo $subAdminMail; ?>');" class="tipTop" title="<?php if ($this->lang->line('admin_newsletter_send_mail_button') != '') echo stripslashes($this->lang->line('admin_newsletter_send_mail_button')); else echo 'Select any template and click here to send email button'; ?>"><!-- <span class="icon email_co"></span> --><span class="btn_link"><?php if ($this->lang->line('admin_templates_send_mail_to_all_user') != '') echo stripslashes($this->lang->line('admin_templates_send_mail_to_all_user')); else echo 'Send Mail To All User'; ?></span></a> </div>
            <div class="btn_30_light" style="height: 29px;"> <a href="javascript:void(0)" onclick="return checkBoxWithSelectValidationAdmin('SendMail','<?php echo $subAdminMail; ?>');" class="tipTop" title="<?php if ($this->lang->line('admin_newsletter_checkbox_selct_send_mail_button') != '') echo stripslashes($this->lang->line('admin_newsletter_checkbox_selct_send_mail_button')); else echo 'Select any checkbox and select template click here to send email button'; ?>"><!-- <span class="icon email_co"></span> --><span class="btn_link act-btn"><?php if ($this->lang->line('admin_templates_send') != '') echo stripslashes($this->lang->line('admin_templates_send')); else echo 'Send'; ?></span></a> </div>
<?php  /*            <div class="btn_30_light" style="height: 29px;"> <a href="javascript:void(0)" onclick="return checkBoxValidationAdmin('Active','<?php echo $subAdminMail; ?>');" class="tipTop" title="Select any checkbox and click here to active records"><span class="icon accept_co"></span><span class="btn_link">Active</span></a> </div>
            <div class="btn_30_light" style="height: 29px;"> <a href="javascript:void(0)" onclick="return checkBoxValidationAdmin('Inactive');" class="tipTop" title="Select any checkbox and click here to inactive records"><span class="icon delete_co"></span><span class="btn_link">Inactive</span></a> </div> */ ?>
            <?php 
						}
						if ($allPrev == '1' || in_array('3', $templates)){
						?>
            <div class="btn_30_light" style="height: 29px;"> <a href="javascript:void(0)" onclick="return checkBoxValidationAdmin('Delete','<?php echo $subAdminMail; ?>');" class="tipTop" title="<?php if ($this->lang->line('common_select_delete_records') != '') echo stripslashes($this->lang->line('common_select_delete_records')); else echo 'Select any checkbox and click here to delete records'; ?>"><!--<span class="icon cross_co del-btn"></span>--><span class="btn_link"><?php if ($this->lang->line('admin_common_delete') != '') echo stripslashes($this->lang->line('admin_common_delete')); else echo 'Delete'; ?></span></a> </div>
            <?php }?>
          </div>
        </div>
        <div class="widget_content">
          <table class="display" id="subscriber_tbl">
            <thead>
              <tr>
                <th class="center"> <input name="checkbox_id[]" type="checkbox" value="on" class="checkall">
                </th>
                <th class="tip_top" title="<?php if ($this->lang->line('dash_click_sort') != '') echo stripslashes($this->lang->line('dash_click_sort')); else echo 'Click to sort'; ?>"> <?php if ($this->lang->line('admin_templates_email_address') != '') echo stripslashes($this->lang->line('admin_templates_email_address')); else echo 'Email Address'; ?> </th>
                <th class="tip_top" title="<?php if ($this->lang->line('dash_click_sort') != '') echo stripslashes($this->lang->line('dash_click_sort')); else echo 'Click to sort'; ?>"> <?php if ($this->lang->line('admin_templates_verification_status') != '') echo stripslashes($this->lang->line('admin_templates_verification_status')); else echo 'Verification Status'; ?> </th>
                <th> <?php if ($this->lang->line('admin_common_action') != '') echo stripslashes($this->lang->line('admin_common_action')); else echo 'Action'; ?> </th>
              </tr>
            </thead>
            <tbody>
              <?php 
						if ($subscribersList->num_rows() > 0){
							foreach ($subscribersList->result() as $row){
						?>
              <tr>
                <td class="center tr_select "><input name="checkbox_id[]" type="checkbox" value="<?php echo $row->_id;?>">
                </td>
                <td class="center">
					<?php if($isDemo){ ?>
					<?php echo $dEmail; ?>
					<?php }  else{ ?>
					<?php echo $row->subscriber_email;?> 
					<?php } ?>
				
				</td>
                <td class="center">

					<?php
					if(isset($row->verify_status)){
						if($row->verify_status==''){
							$verify_status = get_language_value_for_keyword('No',$this->data['langCode']);;
						}else{
							$verify_status = get_language_value_for_keyword($row->verify_status,$this->data['langCode']);;
						}
					}else{
						$verify_status = get_language_value_for_keyword('No',$this->data['langCode']);;
					}
					?>
				  <?php if($row->verify_status == 'Yes'){ ?>
				   <span class="badge_style b_done"><?php echo $verify_status; ?></span>
				   <?php  } else { ?>
				   <span class="badge_style"><?php echo $verify_status; ?></span>
				   <?php } ?>
				  
                </td>
                <td class="center">
                  <?php if ($allPrev == '1' || in_array('3', $templates)){?>
                  <span><a class="action-icons c-delete" href="javascript:confirm_delete('<?php echo ADMIN_ENC_URL;?>/templates/delete_subscribers/<?php echo $row->_id;?>')" title="<?php if ($this->lang->line('admin_common_delete') != '') echo stripslashes($this->lang->line('admin_common_delete')); else echo 'Delete'; ?>"><?php if ($this->lang->line('admin_common_delete') != '') echo stripslashes($this->lang->line('admin_common_delete')); else echo 'Delete'; ?></a></span>
                  <?php }?>
                </td>
              </tr>
              <?php 
							}
						}
						?>
            </tbody>
            <tfoot>
              <tr>
                <th class="center"> <input name="checkbox_id[]" type="checkbox" value="on" class="checkall">
                </th>
                <th> <?php if ($this->lang->line('admin_templates_email_address') != '') echo stripslashes($this->lang->line('admin_templates_email_address')); else echo 'Email Address'; ?> </th>
                <th> <?php if ($this->lang->line('admin_templates_verification_status') != '') echo stripslashes($this->lang->line('admin_templates_verification_status')); else echo 'Verification Status'; ?> </th>
                <th> <?php if ($this->lang->line('admin_common_action') != '') echo stripslashes($this->lang->line('admin_common_action')); else echo 'Action'; ?> </th>
              </tr>
            </tfoot>
          </table>
        </div>
      </div>
    </div>
      <input type="hidden" name="SubAdminEmail" id="SubAdminEmail" />
    <input type="hidden" name="statusMode" id="statusMode"/>
    </form>
  </div>
  <span class="clear"></span> </div>
</div>
<?php 
$this->load->view(ADMIN_ENC_URL.'/templates/footer.php');
?>
