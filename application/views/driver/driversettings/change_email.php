<?php 
$this->load->view('driver/templates/profile_header.php');
?>



<section class="profile_pic_sec row">
   <div  class="profile_login_cont">
   
		<!--------------  Load Profile Side Bar ------------------------>
		<?php    
			$this->load->view('driver/templates/profile_sidebar'); 
		?>
   
   <div class="share_detail">
   <div class="share_det_title">
      <h2><?php echo $heading; ?></span></h2>
   </div>
   <div class="profile_ac_inner_det">
      <div class="inner_full editprofile">
         <p class="form_sub_title"><?php if($this->lang->line('driver_email_information') != '') echo stripslashes($this->lang->line('driver_email_information')); else  echo 'Email Information';?></p>
         
		 <?php 
				$attributes = array('class' => 'form_container left_label', 'id' => 'change_email_form');
				echo form_open('driver/profile/change_email',$attributes) 
			?>
			 <div class="col">
				<li>
				   <p><?php if($this->lang->line('dash_current_email') != '') echo stripslashes($this->lang->line('dash_current_email')); else  echo 'Current Email';?></p>
				   
					<?php 					
						if ($this->lang->line('dash_enter_current_email') != '') $placeholder = stripslashes($this->lang->line('dash_enter_current_email')); else $placeholder = 'Please enter the current email';
						
						$input_data = array(
										'name' => 'email',
										'type' => 'email',
										'id' => 'email',
										'class' => 'required email',
										'placeholder' => $placeholder
						);
						echo form_input($input_data);
					?>
				   
				</li>
				<li>
				   <p><?php 
						if($this->lang->line('dash_new_mail') != '') echo stripslashes($this->lang->line('dash_new_mail')); else  echo 'New Email';
						?> </p>
						
					<?php 					
						if ($this->lang->line('dash_enter_new_email') != '') $placeholder = stripslashes($this->lang->line('dash_enter_new_email')); else $placeholder = 'Please enter a new email';
						
						$input_data = array(
										'name' => 'new_email',
										'type' => 'email',
										'id' => 'new_email',
										'class' => 'required email',
										'placeholder' => $placeholder
						);
						echo form_input($input_data);
					?>
					
				</li>
			 </div>
			
			 <div class="col docu_sub">
				<li>
					 <p class="error" id="errBox"></p>
				   <input type="button" class="btn_profile" value="<?php 
						if($this->lang->line('rides_save_changes') != '') echo stripslashes($this->lang->line('rides_save_changes')); else  echo 'Save changes';
						?> " onclick="check_email_exist();">
				</li>
				<li>
				   &nbsp
				</li>
				<img src="images/indicator.gif" style="display:none;" id="loader">
			 </div>
		 </form>
		 <input type="hidden" id="org_email" value="<?php echo $driver_info->row()->email; ?>" />
      </div>
   </div>
</section>

<script>
function check_email_exist(){
	if($('#change_email_form').valid()){
		var new_email = $('#new_email').val().trim();
		var old_email = $('#email').val().trim(); 
		var org_email = $('#org_email').val();
		if(org_email == old_email){
            if(new_email != old_email){
                $('#loader').show();
                $.ajax({
                    type: 'POST',
                    url: 'driver/profile/ajax_check_driver_email_exist',
                    data: {"email":new_email},
                    dataType: "json",
                    success: function (rdata) {
                        if(rdata.status=='1'){
                            $('#change_email_form').submit();
                        } else {
                            $('#errBox').html(rdata.response);
                        }
                        $('#loader').hide();
                    }
                });
            } else {
                $('#errBox').html('<?php
							if ($this->lang->line('current_and_new_email_not_same') != '')
								echo stripslashes($this->lang->line('current_and_new_email_not_same'));
							else
								echo 'Current and new email addresses should not be same';
							?>');
            }
		} else {
			$('#errBox').html('<?php
							if ($this->lang->line('dash_driver_email_incorrect') != '')
								echo stripslashes($this->lang->line('dash_driver_email_incorrect'));
							else
								echo 'Current email is incorrect';
							?>');
		}
	}
}
</script>
<?php
$this->load->view('driver/templates/footer.php');
?>