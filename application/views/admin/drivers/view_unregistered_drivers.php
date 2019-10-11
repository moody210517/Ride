<?php
$this->load->view(ADMIN_ENC_URL.'/templates/header.php');

$driver_details = $driver_details->row();
?>
<?php if ($this->lang->line('verify') != '')
		  $verify_lang=stripslashes($this->lang->line('verify'));
		else 
		$verify_lang='verify';
		
		if ($this->lang->line('verified') != '')
		  $verified_lang=stripslashes($this->lang->line('verified'));
		else 
		$verified_lang='Verified';
	

if ($this->lang->line('dash_not_available') != '') $not_available = stripslashes($this->lang->line('dash_not_available')); else $not_available = 'Not Available';
							  
?>
<div id="content" class="add-operator-sec view-drive drives_view">
	<div class="grid_container">
		<div class="grid_12">
			<div class="widget_wrap">
				<div class="widget_wrap tabby">
					<div class="widget_top"> 
						<span class="h_icon list"></span>
						<h6><?php echo $heading; ?></h6>
						<div id="widget_tab">
							<ul>
								<li><a href="#tab1" class="active_tab"><?php if ($this->lang->line('admin_drivers_driver_details') != '') echo stripslashes($this->lang->line('admin_drivers_driver_details')); else echo 'Driver Details'; ?></a></li>
							</ul>
						</div>
					</div>
					<div class="widget_content ">
						<?php 
							$attributes = array('class' => 'form_container left_label ajaxsubmit', 'id' => 'settings_form', 'enctype' => 'multipart/form-data');
							echo form_open_multipart(ADMIN_ENC_URL.'/adminlogin/admin_global_settings',$attributes);
						
						?>
							<div id="tab1">
								<ul class="operator-sec-bar">
								
									<li>
										<div class="form_grid_12">
											<label class="field_title"><?php if ($this->lang->line('admin_drivers_driver_category') != '') echo stripslashes($this->lang->line('admin_drivers_driver_category')); else echo 'Driver Category'; ?></label>
											<div class="form_input">
												<p><?php 	$category_name = $driver_category->row()->name;
												if(isset($driver_category->row()->name_languages[$langCode ]) && $driver_category->row()->name_languages[$langCode ] != '') $category_name = $driver_category->row()->name_languages[$langCode ];
												echo $category_name; ?></p>
											</div>
										</div>
									</li>
									
									<li>
										<div class="form_grid_12">
											<label class="field_title"><?php if ($this->lang->line('admin_drivers_driver_name') != '') echo stripslashes($this->lang->line('admin_drivers_driver_name')); else echo 'Driver Name'; ?></label>
											<div class="form_input">
												<p><?php echo $driver_details->driver_name;?></p>
											</div>
										</div>
									</li>
									<li>
									<div class="form_grid_12">
										<label class="field_title"><?php if ($this->lang->line('admin_drivers_email_address') != '') echo stripslashes($this->lang->line('admin_drivers_email_address')); else echo 'Email Address'; ?> </label>
										<div class="form_input">
											<p>											
											<?php if($isDemo){ ?>
											<?php echo $dEmail; ?>
											<?php }  else{ ?>
											<?php echo $driver_details->email;?>
											<?php } ?>
											</p>											
										</div>
									</div>
									</li>

									<li>
									<div class="form_grid_12">
										<label class="field_title"><?php if ($this->lang->line('admin_drivers_mobile_number') != '') echo stripslashes($this->lang->line('admin_drivers_mobile_number')); else echo 'Mobile Number'; ?></label>
										<div class="form_input">
											<p><?php if(isset($driver_details->dail_code)) echo $driver_details->dail_code; ?>
											<?php if(isset($driver_details->mobile_number)) echo $driver_details->mobile_number; ?></p>
										</div>
									</div>
									</li>
									
									<?php /*<li>
									<div class="form_grid_12">
										<label class="field_title"><?php if ($this->lang->line('driver_date_of_birth') != '') echo stripslashes($this->lang->line('driver_date_of_birth')); else echo 'Date Of Birth'; ?></label>
										<div class="form_input">
											<p><?php if(isset($driver_details->date_of_birth)) echo date('d-m-Y',strtotime($driver_details->date_of_birth)); else echo $not_available; ?></p>
										</div>
									</div>
									</li> */?>
									
									
								</ul>	
								<?php /*
		                        <ul class="operator-log-rite">
								   
                                    <li>
									<div class="form_grid_12">
										<label class="field_title"><?php if ($this->lang->line('admin_drivers_email_address') != '') echo stripslashes($this->lang->line('admin_drivers_email_address')); else echo 'Email Address'; ?> </label>
										<div class="form_input">
											<p>											
											<?php if($isDemo){ ?>
											<?php echo $dEmail; ?>
											<?php }  else{ ?>
											<?php echo $driver_details->email;?>
											<?php } ?>
											</p>											
										</div>
									</div>
									</li>

									<li>
									<div class="form_grid_12">
										<label class="field_title"><?php if ($this->lang->line('admin_drivers_mobile_number') != '') echo stripslashes($this->lang->line('admin_drivers_mobile_number')); else echo 'Mobile Number'; ?></label>
										<div class="form_input">
											<p><?php if(isset($driver_details->dail_code)) echo $driver_details->dail_code; ?>
											<?php if(isset($driver_details->mobile_number)) echo $driver_details->mobile_number; ?></p>
										</div>
									</div>
									</li>
									

								
								</ul> */?>
								
								<ul class="last-btn-submit">
									<li>
										<div class="form_grid_12">
											<div class="form_input">
												<a  href="<?php echo ADMIN_ENC_URL;?>/drivers/display_unregister_drivers_list" class="btn_small btn_blue" ><span><?php if ($this->lang->line('admin_drivers_back_to_driver_list') != '') echo stripslashes($this->lang->line('admin_drivers_back_to_driver_list')); else echo 'Back To Drivers List'; ?></span></a>
											</div>
										</div>
									</li>
								</ul>
							</div>
						
							<?php  if(isset($driver_details->role) && $driver_details->role == 'owner'){ ?>
							<?php /*<div id="tab2">
								<ul class="leftsec-contsec">
									<li>
										<h3><?php if ($this->lang->line('admin_drivers_vehicle_details') != '') echo stripslashes($this->lang->line('admin_drivers_vehicle_details')); else echo 'Vehicle Details'; ?></h3>
									</li>
									<li>
										<div class="form_grid_12">
											<label class="field_title"><?php if ($this->lang->line('admin_drivers_vehicle_type') != '') echo stripslashes($this->lang->line('admin_drivers_vehicle_type')); else echo 'Vehicle Type'; ?></label>
											<div class="form_input">
												<p><?php echo $vehicle_types->row()->vehicle_type;?></p>
											</div>
										</div>
									</li>
									<li>
										<div class="form_grid_12">
											<label class="field_title"><?php if ($this->lang->line('admin_drivers_vehicle_maker') != '') echo stripslashes($this->lang->line('admin_drivers_vehicle_maker')); else echo 'Vehicle Maker'; ?></label>
											<div class="form_input">
												<p><?php if(isset($vehicle_maker->row()->brand_name)) echo $vehicle_maker->row()->brand_name; else echo $not_available; ?></p>
											</div>
										</div>
									</li>
									<li>
										<div class="form_grid_12">
											<label class="field_title"><?php if ($this->lang->line('admin_drivers_vehicle_model') != '') echo stripslashes($this->lang->line('admin_drivers_vehicle_model')); else echo 'Vehicle Model'; ?></label>
											<div class="form_input">
												<p><?php if(isset($vehicle_model->row()->name)) echo $vehicle_model->row()->name; else echo $not_available; ?></p>
											</div>
										</div>
									</li>
									<li>
										<div class="form_grid_12">
											<label class="field_title"><?php if ($this->lang->line('admin_drivers_air_conditioned') != '') echo stripslashes($this->lang->line('admin_drivers_air_conditioned')); else echo 'Air Conditioned'; ?></label>
											<div class="form_input">
												<p><?php if(isset($driver_details->ac)){ if ($driver_details->ac == 'Yes') echo 'A/C'; else echo 'Non A/C';  }else{echo 'Non A/C';} ?></p>
											</div>
										</div>
									</li>
									<li>
										<div class="form_grid_12">
											<label class="field_title"><?php if ($this->lang->line('admin_drivers_vehicle_number') != '') echo stripslashes($this->lang->line('admin_drivers_vehicle_number')); else echo 'Vehicle Number'; ?></label>
											<div class="form_input">
												<p><?php if(isset($driver_details->vehicle_number)) echo $driver_details->vehicle_number; ?></p>
											</div>
										</div>
									</li>
								</ul>
								<ul class="last-btn-submit">
									<li>
										<div class="form_grid_12">
											<div class="form_input">
												<a  href="admin/drivers/display_drivers_list" class="btn_small btn_blue" ><span><?php if ($this->lang->line('admin_drivers_back_to_driver_list') != '') echo stripslashes($this->lang->line('admin_drivers_back_to_driver_list')); else echo 'Back To Drivers List'; ?></span></a>
											</div>
										</div>
									</li>
								</ul>
							</div> */?>
							<?php } ?>
				           
							<?php /*<div id="tab3">
								<ul class="operator-sec-bar">
									<li>
											<h3><?php if ($this->lang->line('admin_drivers_driver_documents') != '') echo stripslashes($this->lang->line('admin_drivers_driver_documents')); else echo 'Driver Documents'; ?></h3>
									</li>
									
								<?php 
								if(isset($driver_details->documents['driver'])){
									$driver_docx = $driver_details->documents['driver'];
								} else {
									$driver_docx = array();
								}
										
								if(count($driver_docx) > 0){
									foreach($driver_docx as $docx_key => $drivers_doc){  #echo '<pre>'; print_r($drivers_doc); die;
								?>
									
									<li>
								<div class="form_grid_12">
									<label class="field_title"><?php if(isset($drivers_doc['typeName'])) echo $drivers_doc['typeName']; ?> </label>
									<div class="form_input expiry_box">
										<?php 
											if(isset($drivers_doc['fileName'])){
												if($drivers_doc['fileName'] != ''){
										?>
											<a href="drivers_documents/<?php echo $drivers_doc['fileName']; ?>" target="_blank" > <?php if ($this->lang->line('admin_subadmin_view') != '') echo stripslashes($this->lang->line('admin_subadmin_view')); else echo 'View'; ?> <?php echo $drivers_doc['typeName']; ?> </a>
										<?php } else { ?>
											<a><?php echo $drivers_doc['typeName'] ?> <?php if ($this->lang->line('admin_drivers_not_available') != '') echo stripslashes($this->lang->line('admin_drivers_not_available')); else echo $not_available; ?></a>
										<?php } 
											}
										?>
										<?php 
										if(isset($drivers_doc['expiryDate'])){ 
											if($drivers_doc['expiryDate'] != ''){
										?>
											<p><b><?php if ($this->lang->line('admin_drivers_expiry_date') != '') echo stripslashes($this->lang->line('admin_drivers_expiry_date')); else echo 'Expiry Date'; ?> : </b><?php echo date('M,d Y',strtotime($drivers_doc['expiryDate'])); ?></p>
										<?php 
											}
										} ?>	
									</div>
									<?php 
										if(isset($drivers_doc['verify_status'])){
											if($drivers_doc['verify_status'] == 'Yes'){
												$docx_status = $verified_lang;
												$docx_status_org = 'verified';
												$state_color = 'background:green';
											} else {
												$docx_status = $verify_lang;
												$docx_status_org = 'Verify';
												$state_color = 'background:red';
											}
										} else {
											$docx_status = $verify_lang;
											$docx_status_org = 'Verify';
											$state_color = '';
										}										
									
									?>
									<?php 
									if ($allPrev == '1' || in_array('2', $driver)) {?>
									<a class="btn_small btn_blue" href="javascript:void(0);" id="docx_vrf_<?php echo $docx_key; ?>" onclick="verify_docx('<?php echo $docx_key; ?>','<?php echo $driver_details->_id;?>','driver','<?php echo $docx_status_org; ?>');" style="<?php echo $state_color; ?>!important;border-color:transparent!important;float:right"><?php echo $docx_status; ?></a>
									<?php } ?>
								</div>
								</li>
						<?php 
										}
									} else {
							?>
								<li>
									<div class="form_grid_12">
										<label class="field_title"></label>
										<div class="form_input">
											<p><?php if ($this->lang->line('admin_drivers_no_record_found') != '') echo stripslashes($this->lang->line('admin_drivers_no_record_found')); else echo 'No records found'; ?></p>
										</div>
									</div>
								</li>
							<?php 
								}
							?>
							</ul>
							<?php  if(isset($driver_details->role) && $driver_details->role == 'owner'){ ?>
							<ul class="operator-log-rite">
							<li>
								<h3><?php if ($this->lang->line('admin_drivers_vehicle_documents') != '') echo stripslashes($this->lang->line('admin_drivers_vehicle_documents')); else echo 'Vehicle Documents'; ?></h3>
							</li>
									
								<?php 
										if(isset($driver_details->documents['vehicle'])){
											$vehicle_docx = $driver_details->documents['vehicle'];
										} else {
											$vehicle_docx = array();
										}
										
										if(count($vehicle_docx) > 0){
											foreach($vehicle_docx as $veh_docx_key => $vehicles_doc){  #echo '<pre>'; print_r($drivers_doc); die;
								?>
									
								<li>
									<div class="form_grid_12">
										<label class="field_title"><?php if(isset($vehicles_doc['typeName'])) echo $vehicles_doc['typeName']; ?> </label>
										<div class="form_input expiry_box">
											<?php 
												if(isset($vehicles_doc['fileName'])){
													if($vehicles_doc['fileName'] != ''){
											?>
												<a href="drivers_documents/<?php echo $vehicles_doc['fileName']; ?>" target="_blank" > <?php if ($this->lang->line('admin_subadmin_view') != '') echo stripslashes($this->lang->line('admin_subadmin_view')); else echo 'View'; ?> <?php echo $vehicles_doc['typeName']; ?> </a>
											<?php } else { ?>
												<a><?php echo $vehicles_doc['typeName'] ?> <?php if ($this->lang->line('admin_drivers_not_available') != '') echo stripslashes($this->lang->line('admin_drivers_not_available')); else echo $not_available; ?></a>
											<?php } 
												}
											?>
										
										
										<?php 
										if(isset($vehicles_doc['expiryDate'])){ 
											if($vehicles_doc['expiryDate'] != ''){
										?>
											<p><b><?php if ($this->lang->line('admin_drivers_expiry_date') != '') echo stripslashes($this->lang->line('admin_drivers_expiry_date')); else echo 'Expiry Date'; ?> : </b><?php echo get_time_to_string('M,d Y',strtotime($vehicles_doc['expiryDate'])); ?></p>
										<?php 
											}
										} ?>
										</div>
										<div>
										<?php 
											if(isset($vehicles_doc['verify_status'])){
												if($vehicles_doc['verify_status'] == 'Yes'){
													$docx_status = $verified_lang;
													$docx_status_org = 'verified';
													$state_color = 'background:green';
												} else {
													$docx_status = $verify_lang;
													$docx_status_org = 'Verify';
													$state_color = 'background:red';
												}
											} else {
												$docx_status = $verify_lang;
												$docx_status_org = 'Verify';
												$state_color = '';
											}										
										
										?>
										   <?php 
											if ($allPrev == '1' || in_array('2', $driver)) {?>
											<a class="btn_small btn_blue" href="javascript:void(0);" id="docx_vrf_<?php echo $veh_docx_key; ?>" onclick="verify_docx('<?php echo $veh_docx_key; ?>','<?php echo $driver_details->_id;?>','vehicle','<?php echo $docx_status_org; ?>');" style="<?php echo $state_color; ?>!important;border-color:transparent!important;float:right"><?php echo $docx_status; ?></a>
										<?php } ?>
										</div>
									</div>
								</li>
									
									
						<?php 
										}
									} else {
							?>

							<li>
									<div class="form_grid_12">
											<label class="field_title"></label>
											<div class="form_input">
												<p><?php if ($this->lang->line('admin_drivers_no_record_found') != '') echo stripslashes($this->lang->line('admin_drivers_no_record_found')); else echo 'No records found'; ?></p>
											</div>
										</div>
									</li>
							<?php 
							}
							?>
								</ul>
							<?php } ?>
								<ul class="last-btn-submit">
									<li>
										<div class="form_grid_12">
											<div class="form_input">
												<a  href="admin/drivers/display_drivers_list" class="btn_small btn_blue" ><span><?php if ($this->lang->line('admin_drivers_back_to_driver_list') != '') echo stripslashes($this->lang->line('admin_drivers_back_to_driver_list')); else echo 'Back To Drivers List'; ?></span></a>
											</div>
										</div>
									</li>
								</ul>
							</div> */?>
							
							<?php /*<div id="tab4">
								<ul class="leftsec-contsec">
									<li>
											<h3><?php if ($this->lang->line('admin_drivers_bank_information') != '') echo stripslashes($this->lang->line('admin_drivers_bank_information')); else echo 'Banking Informations'; ?></h3>
									</li>
									<?php 
										if(isset($driver_details->banking)){
											$banking = $driver_details->banking;
										} else {
											$banking = array();
										}
										
										if(!empty($banking)){
									?>
									
									<li>
										<div class="form_grid_12">
											<label class="field_title"><?php if ($this->lang->line('admin_drivers_account_holder_name') != '') echo stripslashes($this->lang->line('admin_drivers_account_holder_name')); else echo 'Account holder name'; ?> </label>
											<div class="form_input">
												<p><?php  if(isset($banking['acc_holder_name'])) echo $banking['acc_holder_name'];  ?></p>
											</div>
										</div>
									</li>
									<li>
										<div class="form_grid_12">
											<label class="field_title"><?php if ($this->lang->line('admin_drivers_account_holder_address') != '') echo stripslashes($this->lang->line('admin_drivers_account_holder_address')); else echo 'Account holder address'; ?> </label>
											<div class="form_input">
												<p><?php if(isset($banking['acc_holder_address'])) echo $banking['acc_holder_address']; ?></p>
											</div>
										</div>
									</li>
									<li>
										<div class="form_grid_12">
											<label class="field_title"><?php if ($this->lang->line('admin_drivers_account_number') != '') echo stripslashes($this->lang->line('admin_drivers_account_number')); else echo 'Account number'; ?> </label>
											<div class="form_input">
												<p><?php if(isset($banking['acc_number'])) echo $banking['acc_number']; ?></p>
											</div>
										</div>
									</li>
									<li>
										<div class="form_grid_12">
											<label class="field_title" style="display:block;"><?php if ($this->lang->line('admin_drivers_bank_name') != '') echo stripslashes($this->lang->line('admin_drivers_bank_name')); else echo 'Bank Name'; ?> </label>
											<div class="form_input">
												<p><?php if(isset($banking['bank_name'])) echo $banking['bank_name']; ?></p>
											</div>
										</div>
									</li>
									<li>
										<div class="form_grid_12">
											<label class="field_title"><?php if ($this->lang->line('admin_drivers_branch_name') != '') echo stripslashes($this->lang->line('admin_drivers_branch_name')); else echo 'Branch Name'; ?> </label>
											<div class="form_input">
												<p><?php  if(isset($banking['branch_name'])) echo $banking['branch_name']; ?></p>
											</div>
										</div>
									</li>
									<li>
										<div class="form_grid_12">
											<label class="field_title" style="display:block;"><?php if ($this->lang->line('admin_drivers_branch_address') != '') echo stripslashes($this->lang->line('admin_drivers_branch_address')); else echo 'Branch address'; ?>  </label>
											<div class="form_input">
												<p><?php  if(isset($banking['branch_address'])) echo $banking['branch_address']; ?></p>
											</div>
										</div>
									</li>		
									<li>
										<div class="form_grid_12">
											<label class="field_title"><?php if ($this->lang->line('admin_drivers_swift') != '') echo stripslashes($this->lang->line('admin_drivers_swift')); else echo 'Swift'; ?> / <?php if ($this->lang->line('admin_ifsc_code') != '') echo stripslashes($this->lang->line('admin_ifsc_code')); else echo 'ifsc code'; ?> </label>
											<div class="form_input">
												<p>
												
												<?php 
												if(isset($banking['swift_code'])) { 
													if($banking['swift_code'] != ''){ 
														echo $banking['swift_code']; 
													} else {
														echo $not_available;
													}
												} ?>
												
												</p>
											</div>
										</div>
									</li>		
									<li>
										<div class="form_grid_12">
											<label class="field_title" style="display:block;"><?php if ($this->lang->line('admin_drivers_routing_number') != '') echo stripslashes($this->lang->line('admin_drivers_routing_number')); else echo 'Routing Number'; ?></label>
											<div class="form_input">
												<p>
												<?php 
												if(isset($banking['routing_number'])) { 
													if($banking['routing_number'] != ''){ 
														echo $banking['routing_number']; 
													} else {
														echo $not_available;
													}
												} ?>
											</div>
										</div>
									</li>								
									
									<?php 
									} else {
									?>
									<?php 
									if ($allPrev == '1' || in_array('2', $driver)) {?>
									<li>
										<div class="form_grid_12">
											<label class="field_title"></label>
											<div class="form_input">
												<p><?php if ($this->lang->line('admin_drivers_no_record_found') != '') echo stripslashes($this->lang->line('admin_drivers_no_record_found')); else echo 'No records found'; ?></p>
												<a href="admin/drivers/banking/<?php echo $driver_details->_id; ?>" class="expiry_box"><?php if ($this->lang->line('admin_drivers_connect_banking') != '') echo stripslashes($this->lang->line('admin_drivers_connect_banking')); else echo 'Connect Banking'; ?></a>
											</div>
										</div>
									</li>
									<?php } ?>
									<?php 
									}
									?>
									
								</ul>
							</div> 
							*/?>
							
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
	var Verify = '<?php echo $verify_lang; ?>';
	var docx_state = $('#docx_vrf_'+docxNo).html();
	$('#docx_vrf_'+docxNo).html('<img src="images/indicator.gif">');
	$.get('<?php echo ADMIN_ENC_URL;?>/drivers/document_verify_status_ajax?docxId='+docxNo+'&driverId='+driverNo+'&docx_state='+docx_state_org+'&docxType='+docxType, function(res){
		if(res == 'Success'){
			if(docx_state == Verify){
				$('#docx_vrf_'+docxNo).html('<?php echo $verified_lang; ?>');
				$('#docx_vrf_'+docxNo).css('background','green');
			} else {
				$('#docx_vrf_'+docxNo).html(Verify);
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
     width: 125px !important;
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
