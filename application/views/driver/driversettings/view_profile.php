<?php
$this->load->view('driver/templates/profile_header.php');

$profilePic = base_url() . USER_PROFILE_IMAGE_DEFAULT;
if (isset($driver_info->row()->image) && $driver_info->row()->image != '') {
    $profilePic = base_url() . USER_PROFILE_THUMB . $driver_info->row()->image;
}


?>

<section class="profile_pic_sec row">
   <div  class="profile_login_cont">
	   
	   <?php
		$this->load->view('driver/templates/profile_sidebar.php');
		?>
	   
	   <div class="share_detail">
		   <div class="share_det_title">
			  <h2><span><?php echo $heading;?> </span></h2>
		   </div>
		   <div class="profile_ac_inner_det">
			  <div class="inner_full editprofile driver_profile">
				 <p class="form_sub_title"><span class="left_title"><?php
                        if ($this->lang->line('driver_profile_information') != '')
                            echo stripslashes($this->lang->line('driver_profile_information'));
                        else
                            echo 'Profile Information';
                        ?> </span><span class="edit_title"><a href="driver/profile/edit_profile_form"> <?php
                        if ($this->lang->line('driver_edit') != '')
                            echo stripslashes($this->lang->line('driver_edit'));
                        else
                            echo 'EDIT';
                        ?> </a></span></p>
				 <div class="profile_info_col"> 
					<div class="driver_profile_part">
					   <div class="driver_img">
						  <img src="<?php echo $profilePic; ?>" class="driver_picture">
					   </div>
					   <div class="driver_info">
						  <h5><?php echo $driver_info->row()->driver_name; ?></h5>
						  <p><?php echo $driver_info->row()->email; ?></p>
						  <p><?php echo $driver_info->row()->dail_code.' '.$driver_info->row()->mobile_number; ?></p>
						  <p><?php if ($this->lang->line('driver_profile_view_doj') != '') echo stripslashes($this->lang->line('driver_profile_view_doj')); else echo 'DOJ'; ?> : <?php echo get_time_to_string('M d, Y',strtotime($driver_info->row()->created)); ?></p>
						  <?php if(isset($driver_info->row()->gender)){ ?>
						  <p><?php if ($this->lang->line('admin_drivers_gender') != '') echo stripslashes($this->lang->line('admin_drivers_gender')); else echo 'Gender'; ?> : <?php echo ucfirst($driver_info->row()->gender); ?></p>
						  <?php } ?>
					   </div>
					</div>
					<div class="driver_location_part">
					   <li>
						  <h4> <?php
                        if ($this->lang->line('dash_driver_location') != '')
                            echo stripslashes($this->lang->line('dash_driver_location'));
                        else
                            echo 'Driver Location';
                        ?> :</h4>
						  <p class="driver_loc_img"><?php if(isset($locationList->row()->city)) echo $locationList->row()->city; else 'N/A';?></p>
					   </li>
					   <li>
						  <h4> <?php
                        if ($this->lang->line('dash_driver_category') != '')
                            echo stripslashes($this->lang->line('dash_driver_category'));
                        else
                            echo 'Driver Category';
                        ?> :</h4>
						  <p div class="driver_category_img"><?php 	
                            $category_name = $categoryList->row()->name;
                            if(isset($categoryList->row()->name_languages[$langCode ]) && $categoryList->row()->name_languages[$langCode ] != '') $category_name = $categoryList->row()->name_languages[$langCode ];
                            echo $category_name; ?></p>
					   </li>
					</div>
				 </div>
				 <p class="form_sub_title"><span class="left_title"><?php if ($this->lang->line('dash_address_details') != '') echo stripslashes($this->lang->line('dash_address_details')); else echo 'Address Details'; ?>  </span></p>
				 <div class="dirver_address_detail">
					<div class="d_add_part driver_addr_detail">
					   <div class="d_add_part_icon dir_icon">
						  <img src="images/site/address_icon.png">
					   </div>
					   <h3><?php
                        if ($this->lang->line('driver_address') != '')
                            echo stripslashes($this->lang->line('driver_address'));
                        else
                            echo 'Address';
                        ?></h3>
					   <p><?php 
						  $addressArr = array(); if(isset($driver_info->row()->address)) $addressArr = $driver_info->row()->address; 
						unset($addressArr['county']); unset($addressArr['state']);
					    echo implode(', ',array_values($addressArr)); ?></p>
					</div>
					<div class="d_cur_passowrd driver_addr_detail">
					   <div class="d_cur_passowrd_icon dir_icon">
						  <img src="images/site/current_password_icon.png">
					   </div>
					   <h3><?php
                        if ($this->lang->line('driver_current_pasword') != '')
                            echo stripslashes($this->lang->line('driver_current_pasword'));
                        else
                            echo 'Current Pasword';
                        ?> </h3>
					   <p>*****************</p>
					    <p><a href="driver/profile/change_password_form"><?php if ($this->lang->line('driver_change') != '') echo stripslashes($this->lang->line('driver_change')); else echo 'CHANGE';
                        ?> </a></p>
					</div>
					<div class="d_license driver_addr_detail">
					   <div class="d_license_icon dir_icon">
						  <img src="images/site/license_icon.png">
					   </div>
					   <h3> <?php
                        if ($this->lang->line('driver_mobile') != '')
                            echo stripslashes($this->lang->line('driver_mobile'));
                        else
                            echo 'Mobile';
                        ?>  </h3>
					   <p><?php echo $driver_info->row()->dail_code.' '.$driver_info->row()->mobile_number; ?></p>
					   <p><a href="driver/profile/change_mobile_form"><?php if ($this->lang->line('driver_change') != '') echo stripslashes($this->lang->line('driver_change')); else echo 'CHANGE';
                        ?></a></p>
					</div>
				 </div>
				 <p class="form_sub_title"><span class="left_title"> <?php  if ($this->lang->line('driver_vehicle_info') != '') echo stripslashes($this->lang->line('driver_vehicle_info')); else echo 'Vehicle Information'; ?></span></p>
				 <div class="profile_info_col">
					<div class="driver_profile_part vehicleinformation">
					   <div class="driver_img">
							<?php 
							$image = CATEGORY_IMAGE_DEFAULT;
							if(isset($categoryList->row()->image) && $categoryList->row()->image != ""){ 
								$image=CATEGORY_IMAGE.$categoryList->row()->image;
							}
							?>
						   <img src="<?php echo $image; ?>" class="driver_picture">
					   </div>
					   <div class="driver_info vehicleinfo">
						  <h5><?php 	
                            $category_name = $categoryList->row()->name;
                            if(isset($categoryList->row()->name_languages[$langCode ]) && $categoryList->row()->name_languages[$langCode ] != '') $category_name = $categoryList->row()->name_languages[$langCode ];
                            echo $category_name; ?></h5>
						  <p><?php if(isset($vehicle_types->row()->vehicle_type)) echo $vehicle_types->row()->vehicle_type; ?></p>
						  <p><?php if(isset($brandList->row()->brand_name)) echo $brandList->row()->brand_name.', '; ?> <?php if(isset($modelList->row()->name)) echo $modelList->row()->name; ?></p> 
						  <p><?php echo $driver_info->row()->vehicle_model_year; ?> <?php
                        if ($this->lang->line('driver_model') != '')
                            echo stripslashes($this->lang->line('driver_model'));
                        else
                            echo 'Model';
                        ?> </p>
					   </div>
					</div>
					<div class="driver_location_part vehicleinformation_right">
					   <li>
						  <h4><?php
                        if ($this->lang->line('driver_vehicle_number') != '')
                            echo stripslashes($this->lang->line('driver_vehicle_number'));
                        else
                            echo 'Vehicle Number';
                        ?>:</h4>
						  <p class="vehiclenumber_icon"><?php if(isset($driver_info->row()->vehicle_number)) echo $driver_info->row()->vehicle_number; ?></p>
					   </li>
					</div>
				 </div>
				 
				<?php if(isset($driver_info->row()->documents['driver']) && !empty($driver_info->row()->documents['driver'])){ ?>
				 
				 <p class="form_sub_title"><span class="left_title"><?php
                        if ($this->lang->line('driver_driver_documents') != '')
                            echo stripslashes($this->lang->line('driver_driver_documents'));
                        else
                            echo 'Driver Documents';
                        ?>  </span></p>
				 <div class="dirver_address_detail vehicleinformation_doc">
					
					<?php foreach($driver_info->row()->documents['driver'] as $driver) { ?>
					
					<div class="d_add_part driver_addr_detail vehicleinformation_detail">
					   <div class="d_add_part_icon dir_icon ">
						  <img src="images/site/veichal_document_icon.png">
					   </div>
					   <h3><?php if(isset($driver['typeName'])) echo $driver['typeName']; ?></h3>
					   <?php if(isset($driver['expiryDate']) && $driver['expiryDate'] != ''){ ?>
					   <p><?php
                        if ($this->lang->line('driver_expired_on') != '')
                            echo stripslashes($this->lang->line('driver_expired_on'));
                        else
                            echo 'Expired on';
                        ?>  <?php echo get_time_to_string('m-d-Y',strtotime($driver['expiryDate']));?></p>
					   <?php } ?>
					   <?php if(isset($driver['fileName']) && $driver['fileName'] != ''){ ?>
					   <p><a href="drivers_documents/<?php echo  $driver['fileName'] ; ?>" target="_blank"><?php
                        if ($this->lang->line('driver_view') != '')
                            echo stripslashes($this->lang->line('driver_view'));
                        else
                            echo 'VIEW';
                        ?>  </a></p>
					   <?php } ?>
					</div>
					
					<?php } ?>
				 </div>
				 <?php } ?>
				 
				 
				 <?php if(isset($driver_info->row()->documents['vehicle']) && !empty($driver_info->row()->documents['vehicle'])){ ?>
				 
				 <p class="form_sub_title"><span class="left_title"><?php
                        if ($this->lang->line('driver_documents') != '')
                            echo stripslashes($this->lang->line('driver_documents'));
                        else
                            echo 'Documents';
                        ?> </span></p>
				 <div class="dirver_address_detail vehicleinformation_doc">
					
					<?php foreach($driver_info->row()->documents['vehicle'] as $vehicle) { ?>
					
					<div class="d_add_part driver_addr_detail vehicleinformation_detail">
					   <div class="d_add_part_icon dir_icon ">
						  <img src="images/site/veichal_document_icon.png">
					   </div>
					   <h3><?php if(isset($vehicle['typeName'])) echo $vehicle['typeName']; ?></h3>
					   <?php if(isset($vehicle['expiryDate']) && $vehicle['expiryDate'] != ''){ ?>
					   <p><?php
                        if ($this->lang->line('driver_expired_on') != '')
                            echo stripslashes($this->lang->line('driver_expired_on'));
                        else
                            echo 'Expired on';
                        ?><?php echo get_time_to_string('m-d-Y',strtotime($vehicle['expiryDate']));?></p>
					   <?php } ?>
					   <?php if(isset($vehicle['fileName']) && $vehicle['fileName'] != ''){ ?>
					   <p><a href="drivers_documents/<?php echo  $vehicle['fileName'] ; ?>" target="_blank"><?php
                        if ($this->lang->line('driver_view') != '')
                            echo stripslashes($this->lang->line('driver_view'));
                        else
                            echo 'VIEW';
                        ?> </a></p>
					   <?php } ?>
					</div>
					
					<?php } ?>
				 </div>
				 <?php } ?>
				 
			  </div>
		   </div>
		</div>
	</div>
</section>

<?php
$this->load->view('driver/templates/footer.php');
?>