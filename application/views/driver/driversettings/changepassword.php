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
         <p class="form_sub_title"><?php
                                    if ($this->lang->line('driver_password_information') != '')
                                        echo stripslashes($this->lang->line('driver_password_information'));
                                    else
                                        echo 'Password Information';
                                    ?></p>
         
		 <?php 
				$attributes = array('class' => 'form_container left_label', 'id' => 'change_password_form');
				echo form_open('driver/profile/change_password',$attributes) 
			?>
			 
			 
			 
			<div class="col">
				<li>
					<p><?php if ($this->lang->line('dash_current_password') != '')echo stripslashes($this->lang->line('dash_current_password'));else echo 'Current Password';?> </p>
						
						
					 <?php 
						if ($this->lang->line('dash_enter_current_password') != '') $placeholder = stripslashes($this->lang->line('dash_enter_current_password')); else $placeholder =  'Please enter the current password';
						$input_data = array(
										'name' => 'password',
										'type' => 'password',
										'id' => 'password',
										'class' => 'required',
										'placeholder' => $placeholder,
										'minlength' => '6'
						);
						echo form_input($input_data);
					?>
				</li>
				<li>
					<p>&nbsp;</p>
				</li>
			</div>
			
			<div class="col">
				<li>
					<p><?php if ($this->lang->line('dash_new_password') != '') echo stripslashes($this->lang->line('dash_new_password')); else echo 'New Password'; ?></p>
						
					 <?php 
						if ($this->lang->line('dash_enter_new_password') != '') $placeholder = stripslashes($this->lang->line('dash_enter_new_password')); else $placeholder =  'Please enter a new password';
						$input_data = array(
										'name' => 'new_password',
										'type' => 'password',
										'id' => 'new_password',
										'class' => 'required',
										'placeholder' => $placeholder,
										'minlength' => '6'
						);
						echo form_input($input_data);
					?>
					
				</li>
				<li>
					<p><?php if ($this->lang->line('driver_confirm_password') != '') echo stripslashes($this->lang->line('driver_confirm_password')); else echo 'Confirm Password'; ?></p>
						
					
					 <?php 
						if ($this->lang->line('dash_reenter_password_again') != '') $placeholder = stripslashes($this->lang->line('dash_reenter_password_again')); else $placeholder =  'Please re-enter your new password again';
						$input_data = array(
										'name' => 'confirm_password',
										'type' => 'password',
										'id' => 'confirm_password',
										'class' => 'required',
										'placeholder' => $placeholder,
										'minlength' => '6',
										'equalto' => '#new_password'
						);
						echo form_input($input_data);
					?>
				</li>
			</div>

			
			 <div class="col docu_sub">
				<li>
				   <input type="submit" class="btn_profile" value="<?php if ($this->lang->line('driver_save_changes') != '') echo stripslashes($this->lang->line('driver_save_changes')); else echo 'Save Changes'; ?>">
				</li>
				<li>
				   &nbsp;
				</li>
			 </div>
		 </form>
      </div>
   </div>
</section>

<script>
$('#change_password_form').validate();
</script>
<?php
$this->load->view('driver/templates/footer.php');
?>