<?php
$this->load->view('site/templates/header');
?>


<section class="rider-register row log-base-sec">
   <div class="container login-center">
      <div class="col-lg-12 col-md-12 col-sm-12 col-xs-12 base-log">
                  
            <div class="widget_content">                    
                <?php
                $attributes = array('class' => 'form_container left_label', 'id' => 'addEditoperators_form','method'=>'POST','autocomplete'=>'off','enctype' => 'multipart/form-data');
                echo form_open('operator/signup', $attributes)
                ?>
                    
                    <div class="row">
                        <div class="col-md-2">
                        </div>
                        
                        <div class="col-md-8">
                            <ul class="operator-sec-bar">

                                <li id="locationBox">
                                    <div class="form_grid_12">
                                        <label class="field_title"><?php if ($this->lang->line('admin_notification_location') != '') echo stripslashes($this->lang->line('admin_notification_location')); else echo 'Location'; ?><span class="req">*</span></label>
                                        <div class="form_input">
                                            <select name="operator_location" id="operator_location"  class="required" style="height: 31px; width: 51%;" >
                                                <option value=""><?php if ($this->lang->line('admin_choose_operator_location') != '') echo stripslashes($this->lang->line('admin_choose_operator_location')); else echo 'Choose operator location'; ?>...</option>
                                                <?php
                                                foreach ($locationList->result() as $loclist) {
                                                ?>
                                                <option value="<?php echo (string)$loclist->_id; ?>" <?php if ($form_mode) if ($operator_details->operator_location == (string)$loclist->_id) echo 'selected="selected"' ?>>
                                                <?php echo $loclist->city; ?>
                                                </option>
                                                <?php
                                                }
                                                ?>
                                            </select>
                                        </div>
                                    </div>
                                </li>
                    


                                <li>
                                    <div class="form_grid_12">
                                        <label class="field_title"><?php if ($this->lang->line('admin_operator_name') != '') echo stripslashes($this->lang->line('admin_operator_name')); else echo 'Operator Name'; ?><span class="req">*</span></label>
                                        <div class="form_input">
                                                <input name="operator_name" id="operator_name" type="text"  class="required large tipTop"  title="<?php if ($this->lang->line('admin_enter_operator_name') != '') echo stripslashes($this->lang->line('admin_enter_operator_name')); else echo 'Please enter operator name'; ?>" value="<?php if($form_mode) echo $operator_details->operator_name;  ?>"/>
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




                                <li class="add-mob-operator">
                                    <div class="form_grid_12">
                                        <label class="field_title"><?php if ($this->lang->line('admin_drivers_mobile_number') != '') echo stripslashes($this->lang->line('admin_drivers_mobile_number')); else echo 'Mobile Number'; ?><span class="req">*</span></label>
                                        <div class="form_input">																								
                                                
                                                <?php 
                                                $dail_code = $d_country_code;
                                                if($form_mode) if(isset($operator_details->dail_code)) $dail_code = $operator_details->dail_code; 
                                                ?>
                                                
                                                <select style="min-width:90px !important;" name="dail_code" id="country_codeM"  class="required chzn-select small tipTop mCC"  title="<?php if ($this->lang->line('select_mobile_country_code') != '') echo stripslashes($this->lang->line('select_mobile_country_code')); else echo 'Please select mobile country code'; ?>">
                                                    <?php foreach ($countryList as $country) { ?>
                                                        <option style="min-width:90px !important;" value="<?php echo $country->dial_code; ?>" <?php if($country->dial_code==$dail_code){ echo "selected='selected'"; } ?>><?php echo $country->dial_code; ?></option>
                                                    <?php } ?>
                                                </select>
                                                
                                                
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






                                <li>
                                    <div class="form_grid_12">
                                            <label class="field_title"><?php if ($this->lang->line('admin_drivers_country') != '') echo stripslashes($this->lang->line('admin_drivers_country')); else echo 'Country'; ?><span class="req">*</span></label>
                                            <div class="form_input">
                                                <select name="country" id="country"  class="required" style="height: 31px; width: 51%;">
                                                <option value=""><?php if ($this->lang->line('company_please_choose_country_tooltip') != '') echo stripslashes($this->lang->line('company_please_choose_country_tooltip')); else echo 'Please choose Country'; ?></option>
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

                                <li>
                                    <div class="form_grid_12">
                                        <label class="field_title"><?php if ($this->lang->line('admin_common_status') != '') echo stripslashes($this->lang->line('admin_common_status')); else echo 'Status'; ?></label>
                                        <div class="form_input">
                                            <div class="active_inactive">
                                                <input type="checkbox"  name="status"  id="active_inactive_active" class="active_inactive" <?php if($form_mode){ if ($operator_details->status == 'Active'){echo 'checked="checked"';} } ?> />
                                            </div>
                                        </div>
                                    </div>
                                </li>

                            </ul>

                        
                                                                
                            <ul class="last-operator">
                                <li>
                                    <div class="form_grid_12">
                                        <div class="form_input">
                                            <input type="text" name="operator_id" id="operator_id" value="<?php if($form_mode){ echo (string)$operator_details->_id; } ?>"  />
                                            <button type="submit" class="btn_small btn_blue" ><span><?php if ($this->lang->line('admin_common_submit') != '') echo stripslashes($this->lang->line('admin_common_submit')); else echo 'Submit'; ?></span></button>
                                        </div>
                                    </div>
                                </li>
                            </ul>
                                                                
                                                                
                        </div>                
                    </div>

                                                
                </form>
            </div>


      </div>

    </div>

</section>


<script>
        $('#addEditoperators_form').validate();
</script>


<?php
$this->load->view('site/templates/footer');
?>


