<?php
$this->load->view('site/templates/profile_header');
$findpage = $this->uri->segment(2);
?> 

<section class="profile_pic_sec row rate_card">
   <div  class="profile_login_cont">

    <?php $this->load->view('site/templates/profile_sidebar'); ?>

    <div class="share_detail">
       <div class="share_det_title">
          <h2><?php if ($this->lang->line('user_rate_card_upper') != '') echo stripslashes($this->lang->line('user_rate_card_upper')); else echo 'RATE CARD'; ?></h2>
       </div>
       <div class="profile_ac_inner_det">
        <?php if (count($locationsArr) > 0) { ?>
          <div class="inner_full select_rate">
            <div class="inner_left rate_top_cont">
                <?php if ($this->lang->line('site_user_city_upper') != '') echo stripslashes($this->lang->line('site_user_city_upper')); else echo 'CITY'; ?><br>
                <select onchange="city_rate_charge();" id="city_rate_card">
                    <option value=""><?php if ($this->lang->line('ride_select_your_city') != '') echo stripslashes($this->lang->line('ride_select_your_city')); else echo 'Select your city'; ?>...</option>
                    <?php foreach ($locationsArr as $locations) { ?>
                        <option <?php if (isset($ratecard_data['location_id'])) if ($ratecard_data['location_id'] == (string) $locations->_id) echo 'selected="selected"'; ?> value="<?php echo (string) $locations->_id; ?>"><?php echo $locations->city; ?></option>
                   <?php } ?>
                </select>
                <span id="loc_loader"></span>
            </div>
            <div class="inner_right rate_top_cont catBox" style="display:none;">
                <?php if ($this->lang->line('site_user_cab_type_upper') != '') echo stripslashes($this->lang->line('site_user_cab_type_upper')); else echo 'CAB TYPE'; ?><br>
                <select onchange="get_rate_card();" id="cat_rate_card">
                    <?php echo $categoryOptions; ?>
                </select>
                <span id="cat_loader"></span>
            </div>
          </div>
          
        <div id="rateCardBox">
   
        </div>

        <?php } else { ?>
            <div class="rate_title">
                <?php if ($this->lang->line('user_no_locations_found') != '') echo stripslashes($this->lang->line('user_no_locations_found')); else echo 'No locations found for rate card'; ?>
            </div>
        <?php } ?>
        
       </div>

    </div>


    </div>
</section>

<script>
    
    

    <?php if($this->input->get('loc') != ''){ ?>
        $('#city_rate_card').val('<?php echo $this->input->get('loc'); ?>');
        //city_rate_charge();
    <?php } ?>
    function city_rate_charge() {
        $('#cat_rate_card').val('');
        $('#city_rate_card').css('border-color','#e8e8e8');
        var category_id = $('#cat_rate_card').val();
        var location_id = $('#city_rate_card').val();
        if(location_id != ''){
            $('#rateCardBox').html('');
			$('#loc_loader').html('<img src="images/indicator.gif">');
			$.ajax({
			    type: "POST",
			    url: 'site/rider/get_rate_card_city_categories_ajax',
			    data: {'location_id':location_id},
			    dataType: 'json',
				success:function(res){
                    $('.catBox').show();
					$('#loc_loader').html('');
					$('#cat_rate_card').html(res.response);
                    $('#cat_rate_card').val(category_id);
				} 
			});
		} 
    }
    
    <?php if($this->input->get('cat') != ''){ ?>
        $('.catBox').show();
        $('#cat_rate_card').val('<?php echo $this->input->get('cat'); ?>');
        get_rate_card();
    <?php } ?>
    
    function get_rate_card(){ 
		var location_id = $('#city_rate_card').val();
        var category_id = $('#cat_rate_card').val();
		if(location_id != '' && category_id != ''){
			$('#cat_loader').html('<img src="images/indicator.gif">');
			$.ajax({
			    type: "POST",
			    url: 'site/rider/get_rate_card_ajax',
			    data: {'location_id':location_id,'category_id':category_id},
			    dataType: 'json',
				success:function(res){
					$('#cat_loader').html('');
					$('#rateCardBox').html(res.response);
				} 
			});
		} else {
            if(location_id == ''){
                $('#city_rate_card').css('border-color','red');
            }
        }
	}
    
</script>

<?php
$this->load->view('site/templates/footer');
?> 