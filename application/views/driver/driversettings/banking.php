<?php
$this->load->view('driver/templates/profile_header.php');
?>
	
	<section class="profile_pic_sec row">
   <div  class="profile_login_cont">
		
		  <!----------------  Load profile side bar --------------------------------->
	   <?php
		$this->load->view('driver/templates/profile_sidebar.php');
		?> 


		<div class="share_det_title">
			  <h2><?php echo $heading;?></h2>
		</div>


		
	   <!-- <div class="share_detail">
		   <div class="share_det_title">
			  <h2><?php echo $heading;?></h2>
		   </div>
		   <div class="profile_ac_inner_det">
			  <div class="inner_full editprofile banking_profile">
			  
				<?php 
					$formArr = array('id' => 'addeditdriverbank_form','enctype' => 'multipart/form-data'); 
					echo form_open('driver/profile/insertEditBanking',$formArr);
				?>
					 <p class="form_sub_title"><?php if($this->lang->line('driver_banking_information') != '') echo stripslashes($this->lang->line('driver_banking_information')); else  echo 'Banking Information'; ?></p>
					 <div class="col">
						<li>
						   <p><?php if($this->lang->line('dash_account_holder_name') != '') echo stripslashes($this->lang->line('dash_account_holder_name')); else  echo 'Account holder name'; ?>  <span class="req">*</span></p>
						   
						    <?php 
								if ($this->lang->line('dash_enter_account_holder_name') != '') $placeholder = stripslashes($this->lang->line('dash_enter_account_holder_name')); else $placeholder =  'Please enter account holder name';
								
								$acc_holder_name = '';
								if(isset($driver_info->row()->banking['acc_holder_name'])) $acc_holder_name = $driver_info->row()->banking['acc_holder_name']; 
								
								$input_data = array(
												'name' => 'acc_holder_name',
												'id' => 'acc_holder_name',
												'type' => 'text',
												'class' => 'required',
												'placeholder' => $placeholder,
												'value' => $acc_holder_name
								);
								echo form_input($input_data);
							?>
							
						</li>
						<li>
							<p><?php if($this->lang->line('dash_account_holder_address') != '') echo stripslashes($this->lang->line('dash_account_holder_address')); else  echo 'Account holder address'; ?> <span class="req">*</span></p>
							
							<?php 
								if ($this->lang->line('dash_enter_account_holder_address') != '') $placeholder = stripslashes($this->lang->line('dash_enter_account_holder_address')); else $placeholder =  'Please enter account holder address';
								
								$acc_holder_address = '';
								if(isset($driver_info->row()->banking['acc_holder_address'])) $acc_holder_address = $driver_info->row()->banking['acc_holder_address']; 
								
								$input_data = array(
												'name' => 'acc_holder_address',
												'id' => 'acc_holder_address',
												'type' => 'text',
												'class' => 'required',
												'placeholder' => $placeholder,
												'value' => $acc_holder_address
								);
								echo form_input($input_data);
							?>
						   
						   
						</li>
					 </div>
					 <div class="col">
						<li>
						   <p><?php 
						if($this->lang->line('dash_account_number') != '') echo stripslashes($this->lang->line('dash_account_number')); else  echo 'Account number';
						?> <span class="req">*</span></p>
							
							<?php 
								if ($this->lang->line('dash_enter_account_number') != '') $placeholder = stripslashes($this->lang->line('dash_enter_account_number')); else $placeholder =  'Please enter Account number';
								
								$acc_number = '';
								if(isset($driver_info->row()->banking['acc_number'])) $acc_number = $driver_info->row()->banking['acc_number']; 
								
								$input_data = array(
												'name' => 'acc_number',
												'id' => 'acc_number',
												'type' => 'text',
												'class' => 'required',
												'placeholder' => $placeholder,
												'value' => $acc_number
								);
								echo form_input($input_data);
							?>
						
						  
						</li>
						<li>
						   <p><?php 
						if($this->lang->line('dash_bank_name') != '') echo stripslashes($this->lang->line('dash_bank_name')); else  echo 'Bank Name';
						?> <span class="req">*</span></p>
						    
							<?php 
								if ($this->lang->line('dash_enter_bank_name') != '') $placeholder = stripslashes($this->lang->line('dash_enter_bank_name')); else $placeholder =  'Please enter bank name';
								
								$bank_name = '';
								if(isset($driver_info->row()->banking['bank_name'])) $bank_name = $driver_info->row()->banking['bank_name']; 
								
								$input_data = array(
												'name' => 'bank_name',
												'id' => 'bank_name',
												'type' => 'text',
												'class' => 'required',
												'placeholder' => $placeholder,
												'value' => $bank_name
								);
								echo form_input($input_data);
							?>
						   
						  
						</li>
					 </div>
					 <div class="col">
						<li>
						   <p><?php 
						if($this->lang->line('dash_branch_name') != '') echo stripslashes($this->lang->line('dash_branch_name')); else  echo 'Branch Name';
						?> <span class="req">*</span></p>
						
							<?php 
								if ($this->lang->line('dash__enter_branch_name') != '') $placeholder = stripslashes($this->lang->line('dash__enter_branch_name')); else $placeholder =  'Please enter Branch Name';
								
								$branch_name = '';
								if(isset($driver_info->row()->banking['branch_name'])) $branch_name = $driver_info->row()->banking['branch_name']; 
								
								$input_data = array(
												'name' => 'branch_name',
												'id' => 'branch_name',
												'type' => 'text',
												'class' => 'required',
												'placeholder' => $placeholder,
												'value' => $branch_name
								);
								echo form_input($input_data);
							?>
						
						</li>
						<li>
						   <p><?php 
						if($this->lang->line('dash_branch_address') != '') echo stripslashes($this->lang->line('dash_branch_address')); else  echo 'Branch address';
						?> <span class="req">*</span></p>
						
							<?php 
								if ($this->lang->line('dash_enter_branch_address') != '') $placeholder = stripslashes($this->lang->line('dash_enter_branch_address')); else $placeholder =  'Please enter Branch address';
								
								$branch_address = '';
								if(isset($driver_info->row()->banking['branch_address'])) $branch_address = $driver_info->row()->banking['branch_address']; 
								
								$input_data = array(
												'name' => 'branch_address',
												'id' => 'branch_address',
												'type' => 'text',
												'class' => 'required',
												'placeholder' => $placeholder,
												'value' => $branch_address
								);
								echo form_input($input_data);
							?>
						
						</li>
					 </div>
					 <div class="col">
						<li>
						   <p><?php if($this->lang->line('admin_drivers_swift') != '') echo stripslashes($this->lang->line('admin_drivers_swift')); else  echo 'Swift'; ?> / <?php if($this->lang->line('admin_ifsc_code') != '') echo stripslashes($this->lang->line('admin_ifsc_code')); else echo 'ifsc code'; ?>
						   </p>
						   
						   <?php 
								if ($this->lang->line('dash_enter_swift_code') != '') $placeholder = stripslashes($this->lang->line('dash_enter_swift_code')); else $placeholder =  'Please enter Swift Code';
								
								$swift_code = '';
								if(isset($driver_info->row()->banking['swift_code'])) $swift_code = $driver_info->row()->banking['swift_code']; 
								
								$input_data = array(
												'name' => 'swift_code',
												'id' => 'swift_code',
												'type' => 'text',
												'placeholder' => $placeholder,
												'value' => $swift_code
								);
								echo form_input($input_data);
							?>
							
						</li>
						<li>
						   <p><?php if($this->lang->line('dash_routing_number') != '') echo stripslashes($this->lang->line('dash_routing_number')); else  echo 'Routing Number'; ?></p>
							
							<?php 
								if ($this->lang->line('dash_enter_routing_number') != '') $placeholder = stripslashes($this->lang->line('dash_enter_routing_number')); else $placeholder =  'Please enter Routing Number';
								
								$routing_number = '';
								if(isset($driver_info->row()->banking['routing_number'])) $routing_number = $driver_info->row()->banking['routing_number']; 
								
								$input_data = array(
												'name' => 'routing_number',
												'id' => 'routing_number',
												'type' => 'text',
												'placeholder' => $placeholder,
												'value' => $routing_number
								);
								echo form_input($input_data);
							?>
						
						</li>
					 </div>
					 <div class="col">
						<li>
						   <p> &nbsp </p>
						   
						   <?php 
								$input_data = array(
												'name' => 'driver_id',
												'id' => 'driver_id',
												'type' => 'hidden',
												'placeholder' => $placeholder,
												'value' => (string)$driver_info->row()->_id
								);
								echo form_input($input_data);
							?>
						   
						   <input type="submit" value="<?php 
						if($this->lang->line('driver_save_information') != '') echo stripslashes($this->lang->line('driver_save_information')); else  echo 'Save Information';
						?>">
						</li>
					 </div>
				 </form>
			  </div>
		   </div>
	   </div> -->

	   
   </div>
</section>

<script>
	$('#addeditdriverbank_form').validate();
</script>

<?php 
$this->load->view('driver/templates/footer.php');
?>