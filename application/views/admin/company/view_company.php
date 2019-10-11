<?php
$this->load->view(ADMIN_ENC_URL.'/templates/header.php'); 
?>

<div id="content" class="add-operator-sec view-opt">
	<div class="grid_container">
		<div class="grid_12">
			<div class="widget_wrap">
				<div class="widget_wrap tabby">
					<div class="widget_top"> 
						<span class="h_icon list"></span>
						<h6><?php  echo $heading; ?></h6>
						
					</div>
					<div class="widget_content">
						<?php 
							$attributes = array('class' => 'form_container left_label ajaxsubmit', 'id' => 'settings_form', 'enctype' => 'multipart/form-data');
							echo form_open_multipart(ADMIN_ENC_URL.'/adminlogin/admin_global_settings',$attributes);
						
							$company_details = $company_details->row();
						
						?>
				          <ul class="operator-sec-bar">
                             <li>
										<div class="form_grid_12">
											<label class="field_title"><?php if ($this->lang->line('admin_partner_company_name') != '') echo stripslashes($this->lang->line('admin_partner_company_name')); else echo 'Company Name'; ?></label>
											<div class="form_input">
												<p><?php if(isset($company_details->company_name)){echo $company_details->company_name;}else{echo "not available";}?></p>
											</div>
										</div>
									</li>
									
									<li>
										<div class="form_grid_12">
											<label class="field_title"><?php if ($this->lang->line('admin_partner_phone_number') != '') echo stripslashes($this->lang->line('admin_partner_phone_number')); else echo 'Phone Number'; ?></label>
											<div class="form_input">
												<p><?php  if(isset($company_details->phonenumber)){echo $company_details->phonenumber;} else{ echo "not availble";}?></p>
											</div>
										</div>
									</li>
									
									<li>
									<div class="form_grid_12">
										<label class="field_title"><?php if ($this->lang->line('admin_partner_email_address') != '') echo stripslashes($this->lang->line('admin_partner_email_address')); else echo 'Email Address'; ?> </label>
										<div class="form_input">
											<p>											
											<?php if($isDemo){ ?>
											<?php echo $dEmail; ?>
											<?php }  else{ ?>
											<?php  if(isset($company_details->email)){echo $company_details->email;} else{ echo "not available";}?>
											<?php } ?>
											</p>											
										</div>
									</div>
									</li>
                           <li>
										<div class="form_grid_12">
											<label class="field_title"><?php if ($this->lang->line('admin_partner_address') != '') echo stripslashes($this->lang->line('admin_partner_address')); else echo 'Address'; ?></label>
											<div class="form_input">
												<p><?php if(isset($company_details->locality['address'])){echo $company_details->locality['address'];} else{ echo "not available";}?></p>
											</div>
										</div>
									</li>
                           <li>
										<div class="form_grid_12">
											<label class="field_title"><?php if ($this->lang->line('admin_partner_city') != '') echo stripslashes($this->lang->line('admin_partner_city')); else echo 'City'; ?></label>
											<div class="form_input">
												<p><?php if(isset($company_details->locality['city'])){echo $company_details->locality['city'];} else{ echo "not available";}?></p>
											</div>
										</div>
									</li>
									</ul>
									<ul class="operator-log-rite">
									
									<li>
										<div class="form_grid_12">
											<label class="field_title"><?php if ($this->lang->line('admin_partner_state') != '') echo stripslashes($this->lang->line('admin_partner_state')); else echo 'State'; ?></label>
											<div class="form_input">
												<p><?php if(isset($company_details->locality['state'])){echo $company_details->locality['state'];} else{ echo "not available";}?></p>
											</div>
										</div>
									</li>
                           <li>
										<div class="form_grid_12">
											<label class="field_title"><?php if ($this->lang->line('admin_partner_country') != '') echo stripslashes($this->lang->line('admin_partner_country')); else echo 'Country'; ?></label>
											<div class="form_input">
												<p><?php if(isset($company_details->locality['country'])){echo $company_details->locality['country'];} else{ echo "not availble";}?></p>
											</div>
										</div>
									</li>
                           <li>
										<div class="form_grid_12">
											<label class="field_title"><?php if ($this->lang->line('admin_drivers_postal_code') != '') echo stripslashes($this->lang->line('admin_drivers_postal_code')); else echo 'Postal Code'; ?></label>
											<div class="form_input">
												<p><?php if(isset($company_details->locality)){echo $company_details->locality['zipcode'];} else{ echo "not available";}?></p>
											</div>
										</div>
									</li>
                           <li>
										<div class="form_grid_12">
											<label class="field_title"><?php if ($this->lang->line('admin_partner_status') != '') echo stripslashes($this->lang->line('admin_partner_status')); else echo 'Status'; ?></label>
											<div class="form_input">
												<p><?php 
												$disp_status = get_language_value_for_keyword($company_details->status,$this->data['langCode']);
												echo $disp_status;
												?></p>
											</div>
										</div>
									</li>
                               </ul>
									
								<ul class="last-btn-submit">
									<li>
										<div class="form_grid_12">
											<div class="form_input">
												<a  href="<?php echo ADMIN_ENC_URL;?>/company/display_companylist" class="btn_small btn_blue" ><span><?php if ($this->lang->line('admin_partner_back_company_list') != '') echo stripslashes($this->lang->line('admin_partner_back_company_list')); else echo 'Back to Company List'; ?></span></a>
											</div>
										</div>
									</li>
								</ul>
					</form>
					</div>
				</div>
			</div>
		</div>
	</div>
	<span class="clear"></span> 
</div>
</div>

<script>

function verify_docx(docxNo,driverNo,docxType,docx_state_org){
	var docx_state = $('#docx_vrf_'+docxNo).html();
	$('#docx_vrf_'+docxNo).html('<img src="images/indicator.gif">');
	$.get(addUrl+'/drivers/document_verify_status_ajax?docxId='+docxNo+'&driverId='+driverNo+'&docx_state='+docx_state+'&docxType='+docxType, function(res){
		if(res == 'Success'){
			if(docx_state == 'Verify'){
				$('#docx_vrf_'+docxNo).html('Verified');
				$('#docx_vrf_'+docxNo).css('background','green');
			} else {
				$('#docx_vrf_'+docxNo).html('Verify');
				$('#docx_vrf_'+docxNo).css('background','red');
			}
		} else{
			$('#docx_vrf_'+docxNo).html('Retry');
		}
	});
}

</script>


<style>

.expiry_box {
    background: none repeat scroll 0 0 gainsboro;
    border: 1px solid grey;
    border-radius: 5px;
    margin-top: 2%;
    padding: 1%;
    text-align: center;
    width: 50% !important;
}

.expiry_box_status {
    background: gainsboro none repeat scroll 0 0;
    border: 1px solid grey;
    border-radius: 5px;
    color: #fff;
    float: right;
    font-weight: bold;
     margin: -32px 0 0;
    padding: 5px 13px;
    text-align: center;
    width: 39px !important;
}
.expiry_box a{
	color: green;
}
.expiry_box p{
	margin-bottom: 0;
    margin-top: 12px;
}
</style>

<?php 
$this->load->view(ADMIN_ENC_URL.'/templates/footer.php');
?>
