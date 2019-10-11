<?php
$this->load->view(ADMIN_ENC_URL.'/templates/header.php');
?> 
<script src="js/jquery-companyedit.js"></script>
	<div id="content">
		<div class="grid_container">
			<div class="grid_12">
				<div class="widget_wrap">
					<div class="widget_top">
						<span class="h_icon list"></span>
						<h6><?php echo $heading; ?></h6>
                        <div id="widget_tab">
            			</div>
					</div>  	
					<div class="widget_content">  
						<?php
						$attributes = array('class' => 'form_container left_label form_stylee', 'id' => 'operators_profile','method'=>'POST','autocomplete'=>'off','enctype' => 'multipart/form-data');
						echo form_open(ADMIN_ENC_URL.'/fleet_partners/insertEditcompanyprofile', $attributes)
						?>
							<ul>
								<li>
									<div class="form_grid_12">
										<label class="field_title" id="lemail"><?php if ($this->lang->line('admin_partner_email_address') != '') echo stripslashes($this->lang->line('admin_partner_email_address')); else echo 'Email Address'; ?><span class="req">*</span></label>
										<div class="form_input">
											<input id="email" name="email" type="text" value="<?php if(isset($operatorsdetails->row()->email)){ echo $operatorsdetails->row()->email; }else{echo "not available";} ?>" maxlength="150" class="large"/>
										</div>
									</div>
								</li>
								
							


								<li>
									<div class="form_grid_12">
										<label class="field_title" id="lfirstname"><?php if ($this->lang->line('admin_partner_company_name') != '') echo stripslashes($this->lang->line('admin_partner_company_name')); else echo 'Company Name'; ?><span class="req">*</span></label>
										<div class="form_input">
											<input id="firstname" name="CompanyName" type="text" value="<?php if(isset($operatorsdetails->row()->company_name)){ echo $operatorsdetails->row()->company_name; }else{echo "not available";} ?>" maxlength="100" class="large"/>
										</div>
									</div>
								</li>  
								

								
								<li>
									<div class="form_grid_12">
										<label class="field_title" id="llastname"><?php if ($this->lang->line('admin_partner_phone_number') != '') echo stripslashes($this->lang->line('admin_partner_phone_number')); else echo 'Phone Number'; ?><span class="req">*</span></label>
										<div class="form_input">
											<input id="lastname" name="phonenumber" type="text" value="<?php if($operatorsdetails->row()->phonenumber){ echo $operatorsdetails->row()->phonenumber; }else{echo "not available";} ?>" maxlength="100" class="large">
										</div>
									</div>
								</li>
								

								<li>
									<div class="form_grid_12">
										<label class="field_title" id="lusername"><?php if ($this->lang->line('admin_auto_search_address') != '') echo stripslashes($this->lang->line('admin_auto_search_address')); else echo 'Auto search address'; ?><span class="req">*</span></label>
										<div class="form_input">
											 <input id="autocomplete" name="Autosearch" placeholder="Enter your address" onFocus="geolocate()" type="text" style="width: 372px;"></input>
										</div>
									</div>
								</li>
								<li>
									<div class="form_grid_12">
										<label class="field_title" id="lusername"><address></address><?php if ($this->lang->line('admin_partner_address') != '') echo stripslashes($this->lang->line('admin_partner_address')); else echo 'Address'; ?><span class="req">*</span></label>
										<div class="form_input">
											 <textarea name="address" id="address"   title="Please enter the diver's address" style="width: 372px;"> <?php if(isset($operatorsdetails->row()->address)){ echo $operatorsdetails->row()->address; }else{echo"not available";} ?></textarea>
										</div>
									</div>
								</li>
								<li>
									<div class="form_grid_12">
										<label class="field_title" id="lusername"><?php if ($this->lang->line('admin_partner_city') != '') echo stripslashes($this->lang->line('admin_partner_city')); else echo 'City'; ?><span class="req">*</span></label>
										<div class="form_input">
											  <input class="field" type="text" id="locality" value="<?php  if(isset( $operatorsdetails->row()->locality['city'])){echo $operatorsdetails->row()->locality['city'];}else{echo "not available";} ?>" name="city" style="width: 372px;"></input></td>
										</div>													<!-- if(isset( $operatorsdetails->row()->locality['city'])){ echo $operatorsdetails->row()->locality['city'];}else{ echo "not available";} -->
									</div>
								</li>
								<li>
									<div class="form_grid_12">
										<label class="field_title" id="lusername"><?php if ($this->lang->line('admin_partner_state') != '') echo stripslashes($this->lang->line('admin_partner_state')); else echo 'State'; ?><span class="req">*</span></label>
										<div class="form_input">
											<td class="slimField"><input class="field" type="text" name="state" id="administrative_area_level_1"   value="<?php if(isset($operatorsdetails->row()->locality['state'])){ echo $operatorsdetails->row()->locality['state']; }else{ echo "not available";} ?>" style="width: 372px;"></input></td>
										</div>
									</div>
								</li>
										   

              					
						
                      <li>
                            <div class="form_grid_12">
                                <label class="field_title"><?php if ($this->lang->line('admin_partner_country') != '') echo stripslashes($this->lang->line('admin_partner_country')); else echo 'Country'; ?><span class="req">*</span></label>
                                <div class="form_input">
                                    <select name="country" id="county"  class="required chzn-select" style="height: 31px; width: 51%;">
                                        <?php  foreach ($countryList->result() as $country) { ?>
                                          <option value="<?php echo $country->name; ?>" data-dialCode="<?php echo $country->dial_code; ?>" <?php if(isset($operatorsdetails->row()->locality['country'])){if ($operatorsdetails->row()->locality['country'] == $country->name) echo 'selected="selected"' ?>><?php echo $country->name;}else{echo"notes_body(server, mailbox, msg_number)";}?></option>
                                        <?php } ?>
                                    </select>
                                </div>
                            </div>
                        </li>	
                      
								<li>
									<div class="form_grid_12">
										<label class="field_title" id="lusername"><?php if ($this->lang->line('admin_partner_zipcode') != '') echo stripslashes($this->lang->line('admin_partner_zipcode')); else echo 'zip code'; ?><span class="req">*</span></label>
										<div class="form_input">
											<td class="wideField"><input class="field" type="text" name="zipcode" value="<?php if(isset($operatorsdetails->row()->locality['zipcode'])){ echo $operatorsdetails->row()->locality['zipcode']; }else{echo "not available";} ?>"id="postal_code" style="width: 375px;"></input></td>
      </tr>
										</div>
									</div>
								</li>

								<!-- <li>
									<div class="form_grid_12">
										<label class="field_title" id="lpassword">password<span class="req">*</span></label>
										<div class="form_input">
											<input id="password" name="password" type="password" maxlength="50" value="" class="large"/>
										</div>
									</div>
								</li> -->
								<!-- <li>
									<div class="form_grid_12">
										<label class="field_title" id="lpassword_confirm">Confirm Password<span class="req">*</span></label>
										<div class="form_input">
											<input id="password_confirm" name="password_confirm" type="password" maxlength="50" value="" class="large"/>
										</div>
									</div>
								</li> -->
								
								
								<li>
									<div class="form_grid_12">
										<label class="field_title"><?php if ($this->lang->line('admin_partner_status') != '') echo stripslashes($this->lang->line('admin_partner_status')); else echo 'Status'; ?> </label>
										<div class="form_input">
											<div class="active_inactive">
												<input type="checkbox"  name="status" id="active_inactive_active" class="active_inactive" <?php if($form_mode){ if ($operatorsdetails->row()->status == 'Active'){echo 'checked="checked"';} }else{echo 'checked="checked"';} ?>/>
											</div>
										</div>
									</div>
								</li>
								
								<li>
								<div class="form_grid_12">
									<div class="form_input">
										<input type="hidden" name="operators_id" id="operators_id" value="<?php if($form_mode){ echo $operatorsdetails->row()->_id; } ?>"  />
										<button type="submit" class="btn_small btn_blue" ><span><?php if ($this->lang->line('admin_partner_submit') != '') echo stripslashes($this->lang->line('admin_partner_submit')); else echo 'Submit'; ?></span></button>
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

<!-- start of googlemap fields -->

<div id="locationField">
     
    </div>

    <table id="address">
      <tr>
        <td class="slimField"><input class="field" id="street_number" hidden></input></td>
        <td class="wideField" colspan="2"><input class="field" id="route" hidden></input></td>
      </tr>
      <tr>
       
        <td class="wideField" colspan="3"><input class="field" id="locality"hidden
              ></input></td>
      </tr>
      <tr>
        
        <td class="slimField"><input class="field" id="administrative_area_level_1"hidden></input></td>
        
        <td class="wideField"><input class="field" id="postal_code"
          hidden    ></input></td>
      </tr>
      <tr>
        
        <td class="wideField" colspan="3"><input class="field"
              hidden id="country"></input></td>
      </tr>
    </table>




<!--  start google autocomplete -->
    <script>
      // This example displays an address form, using the autocomplete feature
      // of the Google Places API to help users fill in the information.

      // This example requires the Places library. Include the libraries=places
      // parameter when you first load the API. For example:
      // <script src="https://maps.googleapis.com/maps/api/js?key=YOUR_API_KEY&libraries=places">

      var placeSearch, autocomplete;
      var componentForm = {
        street_number: 'short_name',
        route: 'long_name',
        locality: 'long_name',
        administrative_area_level_1: 'long_name',
        country: 'long_name',
        postal_code: 'short_name'
      };

      function initAutocomplete() {
        // Create the autocomplete object, restricting the search to geographical
        // location types.
        autocomplete = new google.maps.places.Autocomplete(
            /** @type {!HTMLInputElement} */(document.getElementById('autocomplete')),
            {types: ['geocode']});

        // When the user selects an address from the dropdown, populate the address
        // fields in the form.
        autocomplete.addListener('place_changed', fillInAddress);
      }

      function fillInAddress() {    
        // Get the place details from the autocomplete object.
        var place = autocomplete.getPlace();
        

        for (var component in componentForm) {
          document.getElementById(component).value = '';
          document.getElementById(component).disabled = false;
        }

        // Get each component of the address from the place details
        // and fill the corresponding field on the form.
        for (var i = 0; i < place.address_components.length; i++) {
          var addressType = place.address_components[i].types[0];
          if (componentForm[addressType]) {
            var val = place.address_components[i][componentForm[addressType]];
            document.getElementById(addressType).value = val;
          }
        }
      }

      // Bias the autocomplete object to the user's geographical location,
      // as supplied by the browser's 'navigator.geolocation' object.
      function geolocate() { 
        if (navigator.geolocation) {
          navigator.geolocation.getCurrentPosition(function(position) {
            var geolocation = {
              lat: position.coords.latitude,
              lng: position.coords.longitude
            };
            var circle = new google.maps.Circle({
              center: geolocation,
              radius: position.coords.accuracy
            });
            autocomplete.setBounds(circle.getBounds());
          });
        }
      }
    </script>
    <script src="https://maps.googleapis.com/maps/api/js?key=AIzaSyBP3C0YVa_gWKQWdcryvcktUidsPxPAax0&libraries=places&callback=initAutocomplete"
        async defer></script>
<!--  end google autocomplete --> 

<?php 
$this->load->view(ADMIN_ENC_URL.'/templates/footer.php');
?>