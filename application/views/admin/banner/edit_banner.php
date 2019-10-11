<?php
$this->load->view(ADMIN_ENC_URL.'/templates/header.php');
?>
<div id="content" class="admin-settings banner_sect">
		<div class="grid_container">
			<div class="grid_12">
				<div class="widget_wrap">
					<div class="widget_top">
						<span class="h_icon list"></span>
						<h6><?php echo $heading;?></h6>
					</div>
					<div class="widget_content chenge-pass-base">
					<?php 
						$attributes = array('class' => 'form_container left_label', 'id' => 'addbanner_form',  'enctype' => 'multipart/form-data');
						echo form_open_multipart(ADMIN_ENC_URL.'/banner/editBanner',$attributes) 
					?>
                    
	 						<ul class="leftsec-contsec">
	 							
								<li>
								<div class="form_grid_12">
									<label class="field_title"><?php if ($this->lang->line('admin_banner_banner_name') != '') echo stripslashes($this->lang->line('admin_banner_banner_name')); else echo 'Banner Name'; ?></label>
									<div class="form_input">
										<input name="name" id="name" type="text"  class="large tipTop" value="<?php if(isset($banner_details->row()->name)) echo $banner_details->row()->name;?>" title="<?php if ($this->lang->line('admin_banner_enter_banner_name') != '') echo stripslashes($this->lang->line('admin_banner_enter_banner_name')); else echo 'Please enter the banner name'; ?>"/>
									</div>
								</div>
								</li>
								<li>
								   <div class="form_grid_12">
									  <label class="field_title"><?php if ($this->lang->line('admin_banner_banner_title') != '') echo stripslashes($this->lang->line('admin_banner_banner_title')); else echo 'Banner Title'; ?> </label>
										<div class="form_input">
										<input name="banner_title" id="banner_title" type="text"  class="large tipTop" title="<?php if ($this->lang->line('admin_banner_enter_banner_title') != '') echo stripslashes($this->lang->line('admin_banner_enter_banner_title')); else echo 'Please enter the banner title'; ?>" value="<?php if(isset($banner_details->row()->banner_title)) echo $banner_details->row()->banner_title;?>"/>
										 </div>
										 </div>
								 </li>
								<li>
								<div class="form_grid_12">
									<label class="field_title"><?php if ($this->lang->line('admin_banner_banner_image') != '') echo stripslashes($this->lang->line('admin_banner_banner_image')); else echo 'Banner Image'; ?> </label>
									<div class="form_input">
										<input name="banner_image" id="banner_image" type="file"  class="large tipTop" title="<?php if ($this->lang->line('admin_banner_upload_banner_image') != '') echo stripslashes($this->lang->line('admin_banner_upload_banner_image')); else echo 'Please upload banner image'; ?>"/>
										<div class="error" id="ErrCAtImage" style="color:red !important;"><?php if ($this->lang->line('admin_banner_image_upload_size') != '') echo stripslashes($this->lang->line('admin_banner_image_upload_size')); else echo 'Note: Image Upload Size 1346 X 660 pixel'; ?></div>
									</div> 
									<img src="" id="loadedImg" style="widows:25px; height:25px; display:none;" />
									<div class="form_input">
										<?php if(isset($banner_details->row()->image)) { ?>
										<img src="<?php echo base_url();?>images/banner/<?php echo $banner_details->row()->image;?>" width="200"/>
										<?php } ?>
									</div>
								</div>
								</li>

                                <ul>
	 							<li>
								<div class="form_grid_12">
									<label class="field_title"><?php if ($this->lang->line('admin_common_status') != '') echo stripslashes($this->lang->line('admin_common_status')); else echo 'Status'; ?></label>
									<div class="form_input">
										<div class="publish_unpublish">
											<input type="checkbox"  name="status" <?php if ($banner_details->row()->status == 'Publish'){?>checked="checked"<?php }?> id="publish_unpublish_publish" class="publish_unpublish"/>
										</div>
									</div>
								</div>
								</li>
								<li class="update_btn_last">
								<div class="form_grid_12">
									<div class="form_input">
										<button type="submit" id="sbt_btn" class="btn_small btn_blue" ><span><?php if ($this->lang->line('admin_common_update') != '') echo stripslashes($this->lang->line('admin_common_update')); else echo 'Update'; ?></span></button>
									</div>
								</div>
								</li>
							</ul>
							<input type="hidden" name="banner_id" value="<?php echo $banner_details->row()->_id;?>" />
                            
						</form>
					</div>
				</div>
			</div>
		</div>
		<span class="clear"></span>
	</div>
</div>
<script type="text/javascript">
$('#addbanner_form').validate();
</script>

<?php 
if ($this->lang->line('admin_banner_upload_banner_image_error') != ''){
	$banner_Er = stripslashes($this->lang->line('admin_banner_upload_banner_image_error'));
} else {
	$banner_Er = 'Upload Image Too Small. Please Upload Image Size should be';
}
if ($this->lang->line('admin_success') != ''){
	$banner_Success = stripslashes($this->lang->line('admin_success'));
} else {
	$banner_Success = 'Success';
}

if ($this->lang->line('admin_banner_type_err') != ''){
	$banner_type_Er = stripslashes($this->lang->line('admin_banner_type_err'));
} else {
	$banner_type_Er = 'Invalid file. Please upload only jpg|jpeg|gif|png image files lesser than or equal 2 MB';
}

?>

<script>
$(document).ready(function() {
$("#banner_image").change(function(e) {   
	    e.preventDefault();
        var formData = new FormData($(this).parents('form')[0]);
        $.ajax({
			beforeSend: function(){
				$('#sbt_btn').css("cursor","default");
                $('#sbt_btn').attr("disabled", true);
                $("#loadedImg").css("display", "block");
      	        document.getElementById("loadedImg").src='images/loader64.gif';
  			},
            url: '<?php echo ADMIN_ENC_URL;?>/banner/ajax_check_banner_image_size',
            type: 'POST',
            xhr: function() {
                var myXhr = $.ajaxSettings.xhr();
                return myXhr;
            },
            success: function (data) {
			$("#loadedImg").css("display", "none");
                if(data=='Success'){
                    $('#sbt_btn').removeAttr("disabled");
                    $('#sbt_btn').css("cursor","pointer"); 
                    $('#ErrCAtImage').html('<?php echo $banner_Success; ?>');
                    return true;
                } else if(data == 'File_Ext_Err'){
                    $('#banner_image').val('');
                    $('#ErrCAtImage').html('<?php echo $banner_type_Er; ?>');
                    return false;
                } else {
                    $('#banner_image').val('');
                    $('#ErrCAtImage').html('<?php echo $banner_Er; ?> 1346 X 660 .');
                    return false;
                }
		   },
            data: formData,
            cache: false,
            contentType: false,
            processData: false
        });
        return false;
		
		
});
});
</script>
<style>

.error {
    color: #f00;
    font-size: 12px !important;
    font-weight: normal;
    margin: 12px 12px 12px 0;
}
</style>

<?php 
$this->load->view(ADMIN_ENC_URL.'/templates/footer.php');
?>