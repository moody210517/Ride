<?php
$this->load->view(ADMIN_ENC_URL.'/templates/header.php');
?>
<script type='text/javascript'>
$(function(){
	$('#map_tab').bind('click',function() {
            var w = $('#tab2').width();
            var h = $('#tab2').height();
            $('#map_canvas').css({ width: w, height: h });
			var center = map.getCenter();
           google.maps.event.trigger(map, 'resize');
		   map.setCenter(center); 
	});
});
</script>
<div id="content" class="admin-settings view_user_set">
	<div class="grid_container">
		<div class="grid_12">
			<div class="widget_wrap">
				<div class="widget_top">
					<span class="h_icon list"></span>
					<h6><?php if ($this->lang->line('admin_users_view_users') != '') echo stripslashes($this->lang->line('admin_users_view_users')); else echo 'View User'; ?></h6>
					<div id="widget_tab">
						<ul>
							<li><a href="#tab1" class="active_tab"><?php if ($this->lang->line('admin_users_details') != '') echo stripslashes($this->lang->line('admin_users_details')); else echo 'Details'; ?></a></li>
							<li><a href="#tab2" id='map_tab'><?php if ($this->lang->line('admin_location_and_fare_location_location') != '') echo stripslashes($this->lang->line('admin_location_and_fare_location_location')); else echo 'Location'; ?></a></li>
						</ul>
					</div>
				</div>
				<div class="widget_content chenge-pass-base">
					<?php 
						$attributes = array('class' => 'form_container left_label');
						echo form_open('admin',$attributes) 
					?>
					<div id="tab1">
	 					<ul class="leftsec-contsec">
	 						<li>
								<div class="form_grid_12">
									<label class="field_title"><?php if ($this->lang->line('admin_users_users_list_user_image') != '') echo stripslashes($this->lang->line('admin_users_users_list_user_image')); else echo 'User Image'; ?></label>
									<div class="form_input">
									<?php if (isset($user_details->row()->image) && $user_details->row()->image != ''){?>
										<img src="<?php echo base_url().USER_PROFILE_IMAGE.$user_details->row()->image;?>" width="100px"/>
									<?php }else {?>
										<img src="<?php echo base_url().USER_PROFILE_IMAGE_DEFAULT;?>" width="100px"/>
									<?php }?>
									</div>
								</div>
							</li>
	 						
							<li>
								<div class="form_grid_12">
									<label class="field_title"><?php if ($this->lang->line('admin_users_users_list_user_name') != '') echo stripslashes($this->lang->line('admin_users_users_list_user_name')); else echo 'User Name'; ?></label>
									<div class="form_input">
										<?php echo $user_details->row()->user_name;?>
									</div>
								</div>
							</li>
								
	 						<li>
								<div class="form_grid_12">
									<label class="field_title"><?php if ($this->lang->line('admin_subadmin_email_address') != '') echo stripslashes($this->lang->line('admin_subadmin_email_address')); else echo 'Email Address'; ?></label>
									<div class="form_input">
										
										<?php if($isDemo){ ?>
										<?php echo $dEmail; ?>
										<?php }  else{ ?>
										<?php echo $user_details->row()->email;?>
										<?php } ?>
										
									</div>
								</div>
							</li>
							
	 						<li>
								<div class="form_grid_12">
									<label class="field_title"><?php if ($this->lang->line('dash_mobile_number') != '') echo stripslashes($this->lang->line('dash_mobile_number')); else echo 'Mobile Number'; ?></label>
									<div class="form_input">
										
										<?php if($isDemo){ ?>
										<?php echo $dMobile; ?>
										<?php }  else{ ?>
										<?php echo $user_details->row()->country_code.' '.$user_details->row()->phone_number;?>
										<?php } ?>
										
									</div>
								</div>
							</li>
							
							<li>
								<div class="form_grid_12">
									<label class="field_title"><?php if ($this->lang->line('admin_users_users_list_referral_code') != '') echo stripslashes($this->lang->line('admin_users_users_list_referral_code')); else echo 'REFERRAL CODE'; ?></label>
									<div class="form_input">
										<b><?php echo $user_details->row()->unique_code;?></b>
									</div>
								</div>
							</li>
							
							<?php if(isset($user_details->row()->referral_code)){ 
								if($user_details->row()->referral_code != ''){
								?>
							<li>
								<div class="form_grid_12">
									<label class="field_title"><?php if ($this->lang->line('admin_users_users_list_referred_by') != '') echo stripslashes($this->lang->line('admin_users_users_list_referred_by')); else echo 'REFERRED BY (code)'; ?></label>
									<div class="form_input">
										<b><?php echo $user_details->row()->referral_code;?></b>
									</div>
								</div>
							</li>
							<?php }
							} ?>
								
							<li>
								<div class="form_grid_12">
									<label class="field_title"><?php if ($this->lang->line('admin_users_users_list_created_on') != '') echo stripslashes($this->lang->line('admin_users_users_list_created_on')); else echo 'Created On'; ?></label>
									<div class="form_input">
										<?php 
										if(isset($user_details->row()->created)){  											
											if($user_details->row()->created==''){  
												if ($this->lang->line('dash_not_available') != '') echo stripslashes($this->lang->line('dash_not_available')); else echo 'Not Available';
											}else{
												echo $user_details->row()->created; 
											}
										}else{ 
												if ($this->lang->line('dash_not_available') != '') echo stripslashes($this->lang->line('dash_not_available')); else echo 'Not Available';
										}
										?>
									</div>
								</div>
							</li>
								
							<li>
								<div class="form_grid_12">
									<label class="field_title"><?php if ($this->lang->line('admin_users_users_list_modified_on') != '') echo stripslashes($this->lang->line('admin_users_users_list_modified_on')); else echo 'Modified On'; ?></label>
									<div class="form_input">
										<?php 
										if(isset($user_details->row()->modified)){  											
											if($user_details->row()->modified==''){  
												if ($this->lang->line('dash_not_available') != '') echo stripslashes($this->lang->line('dash_not_available')); else echo 'Not Available';
											}else{
												echo $user_details->row()->modified; 
											}
										}else{ 
											if ($this->lang->line('dash_not_available') != '') echo stripslashes($this->lang->line('dash_not_available')); else echo 'Not Available';
										}
										?>
									</div>
								</div>
							</li>
							
							<li>
								<h2><?php if ($this->lang->line('admin_users_users_list_emergency_contact_details') != '') echo stripslashes($this->lang->line('admin_users_users_list_emergency_contact_details')); else echo 'Emergency Contact Details'; ?>
							</li>
							
							<?php if(isset($user_details->row()->emergency_contact)){
								if(count($user_details->row()->emergency_contact) > 0){
										?>
							
									<li>
										<div class="form_grid_12">
											<label class="field_title"><?php if ($this->lang->line('admin_car_types_name') != '') echo stripslashes($this->lang->line('admin_car_types_name')); else echo 'Name'; ?></label>
											<div class="form_input">
												<?php  if(isset($user_details->row()->emergency_contact['em_name'])) echo $user_details->row()->emergency_contact['em_name'];?>
											
											</div>
										</div>
									</li>
									<li>
										<div class="form_grid_12">
											<label class="field_title"><?php if ($this->lang->line('admin_users_mobile') != '') echo stripslashes($this->lang->line('admin_users_mobile')); else echo 'Mobile'; ?></label>
											<div class="form_input">
												<?php  if(isset($user_details->row()->emergency_contact['em_mobile'])) echo $user_details->row()->emergency_contact['em_mobile_code'].$user_details->row()->emergency_contact['em_mobile'];?>
												
												<span style="float:right; margin-right:50%;"> - 
													<?php  
													if(isset($user_details->row()->emergency_contact['verification']['mobile'])){
														if($user_details->row()->emergency_contact['verification']['mobile'] == 'Yes'){
															if ($this->lang->line('admin_view_user_verified_emergency') != '') echo stripslashes($this->lang->line('admin_view_user_verified_emergency')); else echo 'Verified';
														}else{
															if ($this->lang->line('admin_view_user_not_verified_emergency') != '') echo stripslashes($this->lang->line('admin_view_user_not_verified_emergency')); else echo 'Not Verified';
														}
													} 
													?>
												</span>
												
											</div>
										</div>
									</li>
									
									<li>
										<div class="form_grid_12">
											<label class="field_title"><?php if ($this->lang->line('admin_users_users_list_email') != '') echo stripslashes($this->lang->line('admin_users_users_list_email')); else echo 'Email'; ?></label>
											<div class="form_input">
												<?php  if(isset($user_details->row()->emergency_contact['em_email'])) echo $user_details->row()->emergency_contact['em_email'];?>
												<span style="float:right; margin-right:50%;"> - 
												<?php  
												if(isset($user_details->row()->emergency_contact['verification']['email'])){
													if($user_details->row()->emergency_contact['verification']['email'] == 'Yes'){
														if ($this->lang->line('admin_view_user_verified_emergency') != '') echo stripslashes($this->lang->line('admin_view_user_verified_emergency')); else echo 'Verified';
													}else{
														if ($this->lang->line('admin_view_user_not_verified_emergency') != '') echo stripslashes($this->lang->line('admin_view_user_not_verified_emergency')); else echo 'Not Verified';
													}
												}  
												?>
												</span>
											</div>
										</div>
									</li>
								
								   <?php 
								} else {	 ?>
								
							
									<li>
										<div class="form_grid_12">
											<label class="field_title"></label>
											<div class="form_input">
												<?php if ($this->lang->line('dash_not_available') != '') echo stripslashes($this->lang->line('dash_not_available')); else echo 'Not Available'; ?>
											</div>
										</div>
									</li>
							
										<?php 
								}
							
							} else { ?>
								<li>
								<div class="form_grid_12">
									<label class="field_title"></label>
									<div class="form_input">
										<?php if ($this->lang->line('admin_drivers_not_available') != '') echo stripslashes($this->lang->line('admin_drivers_not_available')); else echo 'Not Available'; ?>
									</div>
								</div>
							</li>
							
							<?php } ?>
							
							
							
						</ul>
						
						<ul class="admin-pass back_view_user">
							<li class="change-pass">
								<div class="form_grid_12">
									<div class="form_input">
										<a href="<?php echo ADMIN_ENC_URL;?>/users/display_user_list"  title="<?php if ($this->lang->line('dash_go_users_list') != '') echo stripslashes($this->lang->line('dash_go_users_list')); else echo 'Go to users list'; ?>"><span class="badge_style b_done"><?php if ($this->lang->line('admin_location_and_fare_back') != '') echo stripslashes($this->lang->line('admin_location_and_fare_back')); else echo 'Back'; ?></span></a>
									</div>
								</div>
							</li>
						</ul>
						
						
					</div>
					<div id="tab2">
						<?php if(isset($map['html']) && isset($map['js'])){ echo $map['js']; ?>
						<?php echo $map['html']; } else { ?>
							<h2><?php if ($this->lang->line('admin_users_users_location_details_not_updated') != '') echo stripslashes($this->lang->line('admin_users_users_location_details_not_updated')); else echo 'Location details are not updated.'; ?> </h2>
						<?php } ?>
					</div>
				</div>
			</div>
		</div>
		<span class="clear"></span>
	</div>
</div>
</div>
<?php 
$this->load->view(ADMIN_ENC_URL.'/templates/footer.php');
?>