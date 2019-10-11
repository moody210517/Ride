<?php
$this->load->view(OPERATOR_NAME.'/templates/header.php');  

$ex_user_id = $this->input->get('user_id');
$customer_type = $this->input->get('customer_type');
if($customer_type == '') $customer_type = 'new';
		$pay_mode = $this->input->get('pay_mode');
if($pay_mode == '') $pay_mode = 'cash';
?> 
<!-- Script for timepicker -->	
<script type="text/javascript" src="js/timepicker/jquery.timepicker.js"></script>
<script type="text/javascript" src="js/timepicker/bootstrap-datepicker.js"></script>
<script type="text/javascript" src="js/timepicker/site.js"></script>
<script type="text/javascript" src="js/timepicker/jquery.timepicker.min.js"></script>
<!-- Script for timepicker -->	

<!-- css for timepicker -->	
<link rel="stylesheet" type="text/css" href="css/timepicker/bootstrap-datepicker.css" />
<link rel="stylesheet" type="text/css" href="css/timepicker/site.css" />
<link rel="stylesheet" type="text/css" href="css/timepicker/jquery.timepicker.css" />


<div id="content" class="admin-settings">
    <div class="grid_container">
        <div class="grid_12">
            <div class="widget_wrap">
                <div class="widget_top">
                    <span class="h_icon list"></span>
                    <h6><?php echo $heading; ?></h6>
                    <div id="widget_tab"></div>
                </div>
                <div class="widget_content chenge-pass-base">
                    <form id="book_trip" method="POST" class="form_container left_label" action="<?php echo OPERATOR_NAME; ?>/trip/book_trip">
                        <ul class="leftsec-contsec">
                            <li>
                                <div class="form_grid_12">
                                    <label class="field_title"><?php if ($this->lang->line('dash_operator_customer_type') != '') echo stripslashes($this->lang->line('dash_operator_customer_type')); else echo 'Customer Type'; ?><span class="req">*</span></label>
                                    <div class="form_input">
                                        <select id="customer_type" name="customer_type" class="large required" style="width: 55%" onclick="choose_customer_type();">
                                            <option value="new"><?php if ($this->lang->line('dash_operator_new_customer') != '') echo stripslashes($this->lang->line('dash_operator_new_customer')); else echo 'New customer'; ?></option>
                                            <option value="existing" <?php if($customer_type == 'existing') echo 'selected="selected"';?>><?php if ($this->lang->line('dash_operator_existing_customer') != '') echo stripslashes($this->lang->line('dash_operator_existing_customer')); else echo 'Existing customer'; ?></option>
                                        </select>
                                    </div>
                                </div>
                            </li>
                            
                            <li id="customers_container" style="display: <?php if($customer_type == 'new') echo 'none';?>;">
                                    <div class="form_grid_12">
                                            <label class="field_title"><?php if ($this->lang->line('dash_operator_enter_customer_name') != '') echo stripslashes($this->lang->line('dash_operator_enter_customer_name')); else echo 'Enter customer name'; ?> <span class="req">*</span></label>
                                            <div class="form_input">
                                                    <input type="text" id="user_autosearch" name="user_autosearch" class="large required" style="width: 55% !important;  height: 30px;" autocomplete="off"/>
                                                    <img src="images/indicator.gif" id="user_loader">
                                                    <div class="ui-autocomplete">
                                                            <ul id="suggesstion-box">
                                                            </ul>
                                                    </div>
                                                    <input type="hidden" name="user_id" id="user_id" />
                                            </div>
                                    </div>
                            </li>
                            
                            <li id="payment_methodContain" style="display:<?php if($client_id == '') echo 'none';?>">
                                    <div class="form_grid_12">
                                            <label class="field_title"><?php if ($this->lang->line('dash_operator_payment_method') != '') echo stripslashes($this->lang->line('dash_operator_payment_method')); else echo 'Payment Method'; ?><span class="req">*</span></label>
                                            <div class="form_input">		
                                                    <div id="dropin-container"></div>
                                                    <input type="hidden" name="nonce" id="nonce">
                                            </div>
                                    </div>
                            </li>
                            </ul>
    <ul class="admin-pass">
                            <li class="change-pass" id="continue_btn" style="display:<?php if($client_id != '') echo 'none';?>;">
                                    <div class="form_grid_12">
                                            <div class="form_input">
                                                    <button type="submit" style="display:<?php #if($ex_user_id == ''&& $customer_type == 'existing') echo 'none';?>" id="form_submit_btn" onclick="chkExistingUser();" class="btn_small btn_blue" ><span><?php if ($this->lang->line('dash_operator_continue_booking') != '') echo stripslashes($this->lang->line('dash_operator_continue_booking')); else echo 'Continue Booking'; ?></span></button>
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


 <style>
 #user_loader {
	display: none;
 }
 
#suggesstion-box{
     display:none;
}
#suggesstion-box {
    width: 100%;
     display:none;
}

#suggesstion-box li {
    background: whitesmoke none repeat scroll 0 0;
    cursor: pointer;
   padding: 8px;
   z-index: 1;
}
#suggesstion-box li:hover {
    background: #dfdfdf none repeat scroll 0 0;
}
.ui-autocomplete {
    margin-top: 0.5px;
    position: absolute;
    width: 56%;
	margin-left: 10px;
}
 </style>
 
<script>
// AJAX call for autocomplete 
$(document).ready(function(){
	$("#user_autosearch").keyup(function(){
		$('#user_autosearch').css('border','solid 1px #d8d8d8'); 
		$.ajax({
		type: "POST",
		url: "operator/trip/ajax_user_autosearch",
		data:'keyword='+$(this).val(),
		beforeSend: function(){
			$("#user_loader").css("display",'inline-block');
		},
		success: function(data){
			$("#suggesstion-box").show();
			$("#suggesstion-box").html(data);
			$("#user_autosearch").css("background","#FFF");
			$("#user_loader").css("display",'none');
		}
		});
	});
	$("body").click(function(){
		$("#suggesstion-box").hide();
		if($("#user_autosearch").val() != '' && $("#user_id").val() == ''){
			$('#user_autosearch').val('');
			$('#user_autosearch').css('border','solid 1px red'); 
		} else {
			$('#user_autosearch').css('border','solid 1px #d8d8d8'); 
		}
	});
});

	function select_user(uid){
		$('#user_id').val(uid);
		var userTxt = $('#'+uid).html(); 
		$('#user_autosearch').val(userTxt);
		$("#suggesstion-box").hide();
	}



<?php 
if($pay_mode == 'card'){
?>
	setTimeout(function () {
		$("#continue_btn").show();
	}, 4000);
<?php } ?>

	function chkExistingUser(){ 
		if($('#customer_type').val() == 'existing' && $('#user_id').val() == ''){
		    
			$('#user_autosearch').css('border','solid 1px red'); 
			$("form").submit(function(e) {
			   if($('#customer_type').val() == 'existing' && $('#user_autosearch').val()=='') {
					 alert('<?php if ($this->lang->line('admin_operator_please_customer') != '') echo stripslashes($this->lang->line('admin_operator_please_customer')); else echo 'please enter customer name'; ?>');
					e.preventDefault();
					return false;
				
			 }
			
			});
		}
	}
	
	
	function choose_customer_type(){
		$('#customer_type').change(function(){
			var user_id = $('#user_id').val();
			if($('#customer_type').val() == 'existing'){
				$('#customers_container').show(); 
				$('#payment_methodContain').hide();
				if(user_id == ''){
					//$('#form_submit_btn').hide();
				}
				$('#payment_mode').val('cash');
			} else {
				var pay_mode = $('#payment_mode').val();
				var customer_type = $('#customer_type').val();
				$('#customers_container').hide();
				//$('#form_submit_btn').show();
				
			}
		});
	}
</script>
 <style>
 #user_id_chzn {
	width:55% !important;
 }
 .chzn-drop{
	width:97.7% !important;
 }
 .chzn-search input[type="text"]{
	width:92% !important;
 }
 #customer_type > option {
    height: 25px;
} 
#payment_mode > option {
    height: 25px;
}
</style>
<?php
$this->load->view(OPERATOR_NAME . '/templates/footer.php');
?>