<?php
$this->load->view(ADMIN_ENC_URL.'/templates/header.php');
?>
<div id="content" class="add_promocode">
	<div class="grid_container">
		<div class="grid_12">
			<div class="widget_wrap">
				<div class="widget_top">
					<span class="h_icon list"></span>
					<h6><?php echo $heading;?></h6>
				</div>
				<div class="widget_content edit-promo-code">
					<?php 
						$attributes = array('class' => 'form_container left_label', 'id' => 'editpromo_form');
						echo form_open(ADMIN_ENC_URL.'/promocode/insertEditPromoCode',$attributes) 
					?>
						<ul class="left_promo_base">
                            <li>
								<div class="form_grid_12">
									<label class="field_title"><?php if ($this->lang->line('admin_promocode_coupon_code') != '') echo stripslashes($this->lang->line('admin_promocode_coupon_code')); else echo 'Coupon code'; ?><span class="req">*</span></label>
									<div class="form_input">
										<input name="promo_code" id="promo_code" type="text" readonly disabled class="required large tipTop" title="<?php if ($this->lang->line('admin_field_not_editable') != '') echo stripslashes($this->lang->line('admin_field_not_editable')); else echo 'This field is not editable'; ?>" value="<?php echo $promocode_details->row()->promo_code;?>" maxlength="25"/>
									</div>
								</div>
							</li>
                                
							<li>
								<div class="form_grid_12">
									<label class="field_title"><?php if ($this->lang->line('admin_promocode_usage_limit_per_coupon') != '') echo stripslashes($this->lang->line('admin_promocode_usage_limit_per_coupon')); else echo 'Usage limit per coupon'; ?><span class="req">*</span></label>
									<div class="form_input">
										<input name="usage_allowed" id="usage_allowed" type="text"  class="required number positiveNumber wholeNumber currencyT minfloatingNumber large tipTop" title="<?php if ($this->lang->line('admin_promocode_enter_coupon_usage_limit') != '') echo stripslashes($this->lang->line('admin_promocode_enter_coupon_usage_limit')); else echo 'Please enter the coupon usage limit'; ?>" value="<?php echo $promocode_details->row()->usage_allowed;?>"/>
									</div>
								</div>
							</li>
							
							<li>
								<div class="form_grid_12">
									<label class="field_title"><?php if ($this->lang->line('admin_promocode_usage_limit_per_user') != '') echo stripslashes($this->lang->line('admin_promocode_usage_limit_per_user')); else echo 'Usage limit per user'; ?> <span class="req">*</span></label>
									<div class="form_input">
										<input name="user_usage" id="user_usage" type="text"  lesserThanCoup="#usage_allowed" class="required number positiveNumber wholeNumber currencyT minfloatingNumber large tipTop" title="<?php if ($this->lang->line('admin_promocode_enter_coupon_usage_limit_per_user') != '') echo stripslashes($this->lang->line('admin_promocode_enter_coupon_usage_limit_per_user')); else echo 'Please enter the coupon usage limit per user'; ?>" value="<?php if(isset($promocode_details->row()->user_usage))echo $promocode_details->row()->user_usage;?>" />
									</div>
								</div>
							</li>
                                
                            <li>
								<div class="form_grid_12">
									<label class="field_title"><?php if ($this->lang->line('admin_promocode_coupon_valid_from') != '') echo stripslashes($this->lang->line('admin_promocode_coupon_valid_from')); else echo 'Coupon Valid From'; ?><span class="req">*</span></label>
									<div class="form_input">
										<input name="validity[valid_from]" id="datefrom" type="text"  readonly="readonly" class="required large tipTop " title="<?php if ($this->lang->line('admin_promocode_select_the_code') != '') echo stripslashes($this->lang->line('admin_promocode_select_the_code')); else echo 'Please select the date'; ?>" value="<?php echo $promocode_details->row()->validity['valid_from'];?>" data-avail="<?php echo get_time_to_string("m-d-Y",strtotime($promocode_details->row()->validity['valid_from'])); ?>"/>
									</div>
								</div>
							</li>
                        </ul>

						<ul class="rite_promo_base">
							
							<li>
								<div class="form_grid_12">
									<label class="field_title"><?php if ($this->lang->line('admin_promocode_coupon_valid_till') != '') echo stripslashes($this->lang->line('admin_promocode_coupon_valid_till')); else echo 'Coupon Valid Till'; ?><span class="req">*</span></label>
									<div class="form_input">
										<input name="validity[valid_to]" id="dateto" type="text"  readonly="readonly" class="required large tipTop " title="<?php if ($this->lang->line('admin_promocode_select_the_code') != '') echo stripslashes($this->lang->line('admin_promocode_select_the_code')); else echo 'Please select the date'; ?>" value="<?php echo $promocode_details->row()->validity['valid_to'];?>"/>
									</div>
								</div>
							</li>
							
							<li>
									<div class="form_grid_12">
										<label class="field_title"><?php if ($this->lang->line('admin_promocode_coupon_type') != '') echo stripslashes($this->lang->line('admin_promocode_coupon_type')); else echo 'Coupon Type'; ?></label>
										<div class="form_input">
											<div class="flat_percentage">
												<input type="checkbox" name="price_type" <?php if ($promocode_details->row()->code_type == 'Flat'){echo 'checked="checked"';}?> id="flat_percentage_flat" class="flat_percentage"/>
											</div>
										</div>
									</div>
							</li>
							
							<li>
								<div class="form_grid_12">
									<label class="field_title"><?php if ($this->lang->line('admin_promocode_coupon_amount') != '') echo stripslashes($this->lang->line('admin_promocode_coupon_amount')); else echo 'Coupon Amount'; ?> <span class="req">*</span></label>
									<div class="form_input">
										<input name="promo_value" id="promo_value" type="text"  class="required number positiveNumber currencyT minfloatingNumber large tipTop" title="<?php if ($this->lang->line('admin_promocode_select_price_value') != '') echo stripslashes($this->lang->line('admin_promocode_select_price_value')); else echo 'Please enter the price value'; ?>" value="<?php echo $promocode_details->row()->promo_value;?>" greaterThan="#min_amount"/>
                                        <input type="hidden" id="min_amount" value="0" />
									</div>
								</div>
							</li>
	 						
							<li>
									<div class="form_grid_12">
										<label class="field_title"><?php if ($this->lang->line('admin_common_status') != '') echo stripslashes($this->lang->line('admin_common_status')); else echo 'Status'; ?></label>
										<div class="form_input">
											<div class="active_inactive">
												<input type="checkbox" name="status" <?php if ($promocode_details->row()->status == 'Active'){echo 'checked="checked"';}?> id="active_inactive_active" class="active_inactive"/>
											</div>
										</div>
									</div>
							</li>
						</ul>
                        
						<ul class="bottom-center-submit">
							<li class="change-pass">
								<div class="form_grid_12">
									<div class="form_input">
										<button type="button" onclick="promoCheck();" class="btn_small btn_blue" ><span><?php if ($this->lang->line('admin_common_submit') != '') echo stripslashes($this->lang->line('admin_common_submit')); else echo 'Submit'; ?></span></button>
                                         <p id="promoErr" style="display:none; color:red;"><?php if ($this->lang->line('promo_amount_percent_limit') != '') echo stripslashes($this->lang->line('promo_amount_percent_limit')); else echo 'Coupon amount should not exceed 100%'; ?></p>
									</div>
								</div>
							</li>
						</ul>
						
						<input type="hidden" name="promo_id" value="<?php echo $promocode_details->row()->_id?>"/>
					</form>
				</div>
			</div>
		</div>
	</div>
	<span class="clear"></span>
</div>
</div>
<script>
function promoCheck(){
    if($('#editpromo_form').valid()){
        if($('#flat_percentage_flat').is(":checked") == false){
            var promo_value = Number($('#promo_value').val());
            if(promo_value > 100){
                $('#promoErr').show();
                $('#promo_value').css({ 'color': "red" });
                $('#promoErr').show();
            } else {
                $('#promoErr').hide();
                $('#promo_value').css('color','#000');
                $('#editpromo_form').submit();
            }
        } else {
            $('#editpromo_form').submit();
        }
    }
}


$(document).ready(function () {

    $('#promo_value').keyup(function(){
       if($('#flat_percentage_flat').is(":checked") == false){
            var promo_value = Number($('#promo_value').val());
            if(promo_value > 100){
                $('#promoErr').show();
                $('#promo_value').css({ 'color': "red" });
                $('#promoErr').show();
            } else {
                $('#promoErr').hide();
                $('#promo_value').css('color','#000');
            }
        }
    });

    $("#datefrom").datepicker({
        dateFormat: "yy-m-dd",
        minDate: 0,
        onSelect: function (date) {
            var date2 = $('#datefrom').datepicker('getDate');
            date2.setDate(date2.getDate());
            $('#dateto').datepicker('setDate', date2);
            //sets minDate to dt1 date + 1
            $('#dateto').datepicker('option', 'minDate', date2);
        }
    });
	
    $('#dateto').datepicker({
        dateFormat: "yy-m-dd",
		minDate:$('#datefrom').datepicker('getDate'),
        onClose: function () {
            var dt1 = $('#datefrom').datepicker('getDate');
            var dt2 = $('#dateto').datepicker('getDate');
            //check to prevent a user from entering a date below date of dt1
            if (dt2 <= dt1) {
                var minDate = $('#dateto').datepicker('option', 'minDate');
                $('#dateto').datepicker('setDate', minDate);
            }
        }
    });
	
	
	
});
</script>
<?php 
$this->load->view(ADMIN_ENC_URL.'/templates/footer.php');
?>