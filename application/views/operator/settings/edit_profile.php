<?php
$this->load->view(OPERATOR_NAME.'/templates/header.php');
if ($form_mode) $operator_details = $operator_details->row();
?>
<div id="content" class="admin-settings profile_set_panel edit-global-set">
    <div class="grid_container">
        <div class="grid_12">
            <div class="widget_wrap">
                <div class="widget_top">
                    <span class="h_icon list"></span>
                    <h6><?php echo $heading; ?></h6>
                    <div id="widget_tab">
                    </div>
                </div>
                <div class="widget_content chenge-pass-base">
                    <form class="form_container left_label" action="<?php echo OPERATOR_NAME; ?>/settings/update_profile" id="addEditoperators_form" method="post" enctype="multipart/form-data">
                        <ul class="left-contsec pro_editing">
                            <li id="locationBox">
                                <div class="form_grid_12">
                                    <label class="field_title"><?php if ($this->lang->line('admin_notification_location') != '') echo stripslashes($this->lang->line('admin_notification_location')); else echo 'Location'; ?>  : <?php
                                        foreach ($locationList->result() as $loclist) {
                                      ?>
                                        <?php if ($operator_details->operator_location == (string)$loclist->_id){ ?>
                                            <?php  echo $loclist->city;  ?>
                                        <?php
                                        } }
                                        ?></label>
                                    <div class="form_input">
                                         
                                        
                                    </div>
                                </div>
                            </li>
                            
                            <li>
                                <div class="form_grid_12">
                                    <label class="field_title"><?php if ($this->lang->line('admin_operator_name') != '') echo stripslashes($this->lang->line('admin_operator_name')); else echo 'Operator Name'; ?><span class="req">*</span></label>
                                    <div class="form_input">
                                            <input name="operator_name" id="operator_name" type="text"  class="required large tipTop" title="<?php if ($this->lang->line('admin_enter_operator_name') != '') echo stripslashes($this->lang->line('admin_enter_operator_name')); else echo 'Please enter operator name'; ?>" value="<?php if($form_mode) echo $operator_details->operator_name;  ?>"/>
                                    </div>
                                </div>
                            </li>
                        
                            <li>
                                <div class="form_grid_12">
                                    <label class="field_title"><?php if ($this->lang->line('admin_operator_email') != '') echo stripslashes($this->lang->line('admin_operator_email')); else echo 'Operator Email'; ?><span class="req">*</span></label>
                                    <div class="form_input">
                                            <input name="email" id="email" type="text"  class="required large tipTop email" title="<?php if ($this->lang->line('admin_enter_operator_email') != '') echo stripslashes($this->lang->line('admin_enter_operator_email')); else echo 'Please enter operator email'; ?>" value="<?php if($form_mode) if(isset($operator_details->email)) { echo $operator_details->email; } ?>"/>
                                    </div>
                                </div>
                            </li>

                            <li>
                                <div class="form_grid_12">
                                    <label class="field_title"><?php if ($this->lang->line('admin_drivers_mobile_number') != '') echo stripslashes($this->lang->line('admin_drivers_mobile_number')); else echo 'Mobile Number'; ?><span class="req">*</span></label>
                                    <div class="form_input">
                                            <input name="dail_code" placeholder="+91" id="country_code" type="text" style="width: 10% !important;"  class="required large tipTop" title="<?php if ($this->lang->line('driver_enter_mobile_country_code') != '') echo stripslashes($this->lang->line('driver_enter_mobile_country_code')); else echo 'Please enter mobile country code'; ?>" value="<?php if($form_mode) if(isset($operator_details->dail_code)) echo $operator_details->dail_code; ?>"/>
                                            <input name="mobile_number" placeholder="<?php if ($this->lang->line('admin_drivers_mobile_number') != '') echo stripslashes($this->lang->line('admin_drivers_mobile_number')); else echo 'Mobile Number'; ?>.." id="mobile_number" type="text"  class="required large tipTop phoneNumber" title="<?php if ($this->lang->line('driver_enter_mobile_number') != '') echo stripslashes($this->lang->line('driver_enter_mobile_number')); else echo 'Please enter the mobile number'; ?>" maxlength="20"  value="<?php if($form_mode) if (isset($operator_details->mobile_number)) echo $operator_details->mobile_number; ?>"/>
                                    </div>
                                </div>
                            </li>	
    
                            <li>
                                    <div class="form_grid_12">
                                            <label class="field_title"><?php if ($this->lang->line('admin_drivers_address') != '') echo stripslashes($this->lang->line('admin_drivers_address')); else echo 'Address'; ?><span class="req">*</span></label>
                                            <div class="form_input">
                                                    <textarea name="address" id="address"  class="required large tipTop" title="<?php if ($this->lang->line('admin_enter_operator_address') != '') echo stripslashes($this->lang->line('admin_enter_operator_address')); else echo 'Please enter the operator address'; ?>" style="width: 372px;"><?php if($form_mode) if (isset($operator_details->address['address'])) echo $operator_details->address['address']; ?></textarea>
                                            </div>
                                    </div>
                            </li>
</ul>
                        
                        
                        <ul class="rite-contsec pro_editing">
                            <li>
                                <div class="form_grid_12">
                                    <label class="field_title"><?php if ($this->lang->line('admin_drivers_country') != '') echo stripslashes($this->lang->line('admin_drivers_country')); else echo 'Country'; ?><span class="req">*</span></label>
                                    <div class="form_input">
                                        <select name="country" id="country"  class="required chzn-select" style="height: 31px; width: 51%;">
                                        <?php foreach ($countryList as $country) { ?>
                                                <option value="<?php echo $country->name; ?>" data-dialCode="<?php echo $country->dial_code; ?>" <?php if ($form_mode) if ($operator_details->address['country'] == $country->name) echo 'selected="selected"' ?>><?php echo $country->name; ?></option>
                                        <?php } ?>
                                        </select>
                                    </div>
                                </div>
                            </li>
                        
                            <li>
                                <div class="form_grid_12">
                                    <label class="field_title"><?php if ($this->lang->line('admin_drivers_state_province_region') != '') echo stripslashes($this->lang->line('admin_drivers_state_province_region')); else echo 'State / Province / Region'; ?><span class="req">*</span></label>
                                    <div class="form_input">
                                            <input name="state" id="state" type="text"  class="required large tipTop" title="<?php if ($this->lang->line('admin_operator_driver_state') != '') echo stripslashes($this->lang->line('admin_operator_driver_state')); else echo 'Please enter the state'; ?>" value="<?php if($form_mode) if (isset($operator_details->address['state'])) echo $operator_details->address['state']; ?>"/>
                                    </div>
                                </div>
                            </li>

                            <li>
                                <div class="form_grid_12">
                                    <label class="field_title"><?php if ($this->lang->line('admin_drivers_city') != '') echo stripslashes($this->lang->line('admin_drivers_city')); else echo 'City'; ?><span class="req">*</span></label>
                                    <div class="form_input">
                                            <input name="city" id="city" type="text"  class="required large tipTop" title="<?php if ($this->lang->line('location_enter_the_city') != '') echo stripslashes($this->lang->line('location_enter_the_city')); else echo 'Please enter the city'; ?>" value="<?php if($form_mode) if (isset($operator_details->address['city'])) echo $operator_details->address['city']; ?>"/>
                                    </div>
                                </div>
                            </li>

                            <li>
                                <div class="form_grid_12">
                                    <label class="field_title"><?php if ($this->lang->line('admin_drivers_postal_code') != '') echo stripslashes($this->lang->line('admin_drivers_postal_code')); else echo 'Postal Code'; ?><span class="req">*</span></label>
                                    <div class="form_input">
                                            <input name="postal_code" id="postal_code" type="text"  maxlength="10" class="required large tipTop" title="<?php if ($this->lang->line('location_enter_the_postalcode') != '') echo stripslashes($this->lang->line('location_enter_the_postalcode')); else echo 'Please enter the postal code'; ?>" value="<?php if($form_mode) if (isset($operator_details->address['postal_code'])) echo $operator_details->address['postal_code']; ?>"/>
                                    </div>
                                </div>
                            </li>
                            
                        </ul>
                            
                            
                        <ul class="admin-pass">
                            <li class="change-pass">
                                    <div class="form_grid_12">
                                        <div class="form_input">
                                            <input type="hidden" name="operator_id" id="operator_id" value="<?php if($form_mode){ echo (string)$operator_details->_id; } ?>"  />
                                            <button type="submit" class="btn_small btn_blue" ><span><?php if ($this->lang->line('admin_subadmin_update') != '') echo stripslashes($this->lang->line('admin_subadmin_update')); else echo 'Update'; ?></span></button>
                                        </div>
                                    </div>
                                </li>
                        </ul>											 
                    </form>
                </div>
            </div>
        </div>
    </div>
    <span class="clear"></span>
</div>
</div>
<script>
        $('#addEditoperators_form').validate();
</script>
<?php 
$this->load->view(OPERATOR_NAME.'/templates/footer.php');
?>