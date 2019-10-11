<?php 
$this->load->view('site/templates/header');
?>

<section class="contact row">
   <div class="rider_login_cont " style="max-width: 900px;">
      <div class="col-lg-12 col-md-12 col-sm-12 col-xs-12 no_padding">
		
		
		<div class="col-md-6 contact_left">
			<?php 
			$formArr = array('id' => 'cms_contact_form','enctype' => 'multipart/form-data');
			echo form_open('site/cms/send_contact_mail',$formArr);
			?>
				<ul>
					<li class="col-md-12">
						<label><?php if ($this->lang->line('cms_name') != '') echo stripslashes($this->lang->line('cms_name')); else echo 'Name'; ?></label>
						
						 <?php 
						if ($this->lang->line('cms_name') != '') $placeholder = stripslashes($this->lang->line('cms_name')); else $placeholder = 'Name';
						$input_data = array(
										'name' => 'user_name',
										'type' => 'text',
										'id' => 'user_name',
										'class' => 'form-control required',
										'placeholder' => $placeholder
						);
						echo form_input($input_data);
					  ?>
						
					</li>

					<li class="col-md-12">
						<label><?php if ($this->lang->line('cms_email') != '') echo stripslashes($this->lang->line('cms_email')); else echo 'Email'; ?></label>
						
						<?php 
						 
							if ($this->lang->line('cms_email') != '') $placeholder = stripslashes($this->lang->line('cms_email')); else $placeholder = 'Email';
							 
							$input_data = array(
											'name' => 'user_email',
											'type' => 'text',
											'id' => 'user_email',
											'class' => 'form-control required email',
											'placeholder' => $placeholder
							);
							echo form_input($input_data);
						?>
						
					</li>


					<li class="col-md-12">
						<label><?php if ($this->lang->line('cms_address') != '') echo stripslashes($this->lang->line('cms_address')); else echo 'Address'; ?></label>
						
						<?php 
							
							if ($this->lang->line('cms_address') != '') $placeholder = stripslashes($this->lang->line('cms_address')); else $placeholder = 'Address';
							 
							$input_data = array(
											'name' => 'user_address',
											'type' => 'text',
											'id' => 'user_address',
											'class' => 'form-control required',
											'placeholder' => $placeholder
							);
							echo form_input($input_data);
						?>
						
					</li>

					<li class="col-md-12">
						
						
						<?php 
							if ($this->lang->line('cms_address') != '') $placeholder = stripslashes($this->lang->line('cms_address')); else $placeholder = 'Address';
							
							$input_data = array(
											'name' => 'user_address1',
											'type' => 'text',
											'id' => 'user_address1',
											'class' => 'form-control required',
											'placeholder' => $placeholder
							);
							echo form_input($input_data);
						?>
					</li>
					
					<li class="col-md-12 change">
						<label><?php if ($this->lang->line('cms_phone') != '') echo stripslashes($this->lang->line('cms_phone')); else echo 'Phone Number'; ?></label>
						
						<?php 
						$drop_options = array();
						foreach ($countryList as $country) {  
							if($country->dial_code != ''){ 
								$optkey = $country->dial_code.'" data-cCode="'.$country->cca3;
								if($d_country_cca3 == $country->cca3) $optkey.= '" selected=selected';
								$drop_options[$optkey]=$country->cca3.' ('.$country->dial_code.')';
							}
						}
						
						$input_data = 'class="country_code required chzn-select" id="country_code" style="width: 27%; "';
						
						echo form_dropdown('dail_code',$drop_options,$d_country_code,$input_data);
							if ($this->lang->line('cms_phone') != '') $placeholder = stripslashes($this->lang->line('cms_phone')); else $placeholder = 'Phone Number';
							
							$input_data = array(
											'name' => 'mobile',
											'type' => 'text',
											'id' => 'mobile',
											'class' => 'form-control required number',
											'placeholder' => $placeholder
							);
							echo form_input($input_data);
						?>
						
					</li>

					<li class="col-md-5">
						<label><?php if ($this->lang->line('cms_city') != '') echo stripslashes($this->lang->line('cms_city')); else echo 'City'; ?></label>
						
						<?php 
							if ($this->lang->line('cms_city') != '') $placeholder = stripslashes($this->lang->line('cms_city')); else $placeholder = 'City';
							
							$input_data = array(
											'name' => 'city',
											'type' => 'text',
											'id' => 'city',
											'class' => 'form-control required',
											'placeholder' => $placeholder
							);
							echo form_input($input_data);
						?>
						
					</li>



					<li class="col-md-5">
						<label><?php if ($this->lang->line('cms_state') != '') echo stripslashes($this->lang->line('cms_state')); else echo 'State'; ?></label>
						
						<?php 
							if ($this->lang->line('cms_state') != '') $placeholder = stripslashes($this->lang->line('cms_state')); else $placeholder = 'State';
							
							$input_data = array(
											'name' => 'state',
											'type' => 'text',
											'id' => 'state',
											'class' => 'form-control required',
											'placeholder' => $placeholder
							);
							echo form_input($input_data);
						?>
					</li> 

					<li class="col-md-5">
						<label><?php if ($this->lang->line('cms_zip') != '') echo stripslashes($this->lang->line('cms_zip')); else echo 'ZIP Code'; ?></label>
						
						<?php 
							if ($this->lang->line('cms_zip') != '') $placeholder = stripslashes($this->lang->line('cms_zip')); else $placeholder = 'ZIP Code';
							
							$input_data = array(
											'name' => 'zipcode',
											'type' => 'text',
											'id' => 'zipcode',
											'class' => 'form-control required',
											'placeholder' => $placeholder
							);
							echo form_input($input_data);
						?>
						
					</li>

					

					<li class="col-md-12">
						<label><?php if ($this->lang->line('cms_message') != '') echo stripslashes($this->lang->line('cms_message')); else echo 'Message'; ?></label>
						
						
						<?php 
							if ($this->lang->line('cms_message') != '') $placeholder = stripslashes($this->lang->line('cms_message')); else $placeholder = 'Message';
							
							$input_data = array(
											'name' => 'message',
											'type' => 'text',
											'id' => 'message',
											'class' => 'form-control required',
											'placeholder' => $placeholder,
											'rows' => '3'
							);
							echo form_textarea($input_data);
						?>
						
						
					</li>
					<li class="col-md-12">
						<input class="btn btn-default contact_submit_btn" id="submit_btn" type="submit" value="<?php if ($this->lang->line('cms_submit') != '') echo stripslashes($this->lang->line('cms_submit')); else echo 'Submit'; ?>">
					</li>
				</ul>
			</form>
		</div>
		
		 <div class="col-md-5 contact_right">
				<?php echo $pageDetail['description']; ?>
				<?php echo $pageDetail['css_descrip']; ?>
			</div>
        
      </div>
   </div>
</section>


 <script>
	$(document).ready(function () {
		$("#cms_contact_form").validate();
		
		$('#submit_btn').click(function(){
			if($("#cms_contact_form").valid()){ 
				$('#submit_btn').attr('disabled','disabled'); 
				$('#cms_contact_form').submit();
			}
		});
		
	});
</script>

<?php 
$this->load->view('site/templates/footer'); 
?>