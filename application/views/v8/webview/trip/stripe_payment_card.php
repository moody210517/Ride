<!DOCTYPE html>
<head>
	<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
	<meta name="viewport" content="width=device-width, initial-scale=1.0" />
	<title><?php echo $this->config->item('email_title'); ?> - <?php if ($this->lang->line('stripe_payment_card') != '') echo stripslashes($this->lang->line('stripe_payment_card')); else echo 'Payment Credit Card'; ?></title>
	<link rel="stylesheet" href="<?php echo base_url(); ?>plugins/cc-payments/css/style.css">
	<script type="text/javascript" src="<?php echo base_url(); ?>plugins/cc-payments/js/jquery.min.js"></script>
	<script type="text/javascript" src="<?php echo base_url(); ?>plugins/cc-payments/js/jquery.creditCardValidator.js"></script>
	<script type="text/javascript">
		function cardFormValidate(){
		    var cardValid = 0;
		      
		    //Card validation
		    $('#card_number').validateCreditCard(function(result) {
		        var cardType = (result.card_type == null)?'':result.card_type.name;
		        if(cardType == 'Visa'){
		            var backPosition = result.valid?'2px -163px, 260px -87px':'2px -163px, 260px -61px';
		        }else if(cardType == 'MasterCard'){
		            var backPosition = result.valid?'2px -247px, 260px -87px':'2px -247px, 260px -61px';
		        }else if(cardType == 'Maestro'){
		            var backPosition = result.valid?'2px -289px, 260px -87px':'2px -289px, 260px -61px';
		        }else if(cardType == 'Discover'){
		            var backPosition = result.valid?'2px -331px, 260px -87px':'2px -331px, 260px -61px';
		        }else if(cardType == 'Amex'){
		            var backPosition = result.valid?'2px -121px, 260px -87px':'2px -121px, 260px -61px';
		        }else{
		            var backPosition = result.valid?'2px -121px, 260px -87px':'2px -121px, 260px -61px';
		        }
		        $('#card_number').css("background-position", backPosition);
		        if(result.valid){
		            $("#card_type").val(cardType);
		            $("#card_number").removeClass('required');
		            cardValid = 1;
		        }else{
		            $("#card_type").val('');
		            $("#card_number").addClass('required');
		            cardValid = 0;
		        }
		    });
		      
		    //Form validation
		    var expMonth = $("#exp_month").val();
		    var expYear = $("#exp_year").val();
		    var cvc_number = $("#cvc_number").val();
		    var regName = /^[a-z ,.'-]+$/i;
		    var regMonth = /^01|02|03|04|05|06|07|08|09|10|11|12$/;
		    var regYear = /^2016|2017|2018|2019|2020|2021|2022|2023|2024|2025|2026|2027|2028|2029|2030|2031$/;
		    var regCVV = /^[0-9]{3,3}$/;
		    if (cardValid == 0) {
		        $("#card_number").addClass('required');
		        //$("#card_number").focus();
				$('#cardSubmitBtn').prop('disabled', true); 
		        return false;
		    }else if (!regMonth.test(expMonth)) {
		        $("#card_number").removeClass('required');
		        $("#exp_month").addClass('required');
		       // $("#exp_month").focus();
			   $('#cardSubmitBtn').prop('disabled', true); 
		        return false;
		    }else if (!regYear.test(expYear)) {
		        $("#card_number").removeClass('required');
		        $("#exp_month").removeClass('required');
		        $("#exp_year").addClass('required');
		        //$("#exp_year").focus();
				$('#cardSubmitBtn').prop('disabled', true); 
		        return false;
		    }else if (!regCVV.test(cvc_number)) {
				console.log(regCVV.test(cvc_number));
		        $("#card_number").removeClass('required');
		        $("#exp_month").removeClass('required');
		        $("#exp_year").removeClass('required');
		        $("#cvc_number").addClass('required');
		        //$("#cvc_number").focus();
				$('#cardSubmitBtn').prop('disabled', true); 
		        return false;
		    }else{
		        $("#card_number").removeClass('required');
		        $("#exp_month").removeClass('required');
		        $("#exp_year").removeClass('required');
		        $("#cvc_number").removeClass('required');
		        $('#cardSubmitBtn').prop('disabled', false);  
		        return true;
		    }
		}
		    
		$(document).ready(function() {		    
		    //Card form validation on input fields
		    $('#paymentForm input[type=text]').on('keyup',function(){
		        cardFormValidate();
		    });
		    $('#paymentForm input[type=password]').on('keyup',function(){
		        cardFormValidate();
		    });
		    $('#paymentForm select').on('change',function(){
		        cardFormValidate();
		    });
		    
		    //Submit card form
		    $("#cardSubmitBtn").on('click',function(){
		        if (cardFormValidate()) {
					$("#cardSubmitBtn").val('Processing....');
					$("#cardSubmitBtn").prop('disabled', true);
					$("#paymentForm").submit();
		        }
		    });
		});
	</script>
</head>
<?php 
$tatalAmount  = ($total_amount * 100);
$product_description = ucfirst($this->config->item('email_title')).' Booking Ride';
$newImgpathtoStripe = base_url().'images/logo/'.$this->config->item('logo_image');
$payment_btn_label = 'Pay By Card' ;
?>					
						
<body>
	<div class="card-payment">
		<div id="paymentSection">
			<form name="paymentForm" id="paymentForm" method="post" enctype="multipart/form-data" action="<?php echo base_url(); ?>v8/api_v7/payment/stripe_payment_process" autocomplete="off">
				<input type="hidden" value="<?php echo $mobileId;?>" name="mobileId" />
				<input type="hidden" value="<?php echo $ride_id;?>" name="transaction_id" />
				<input type="hidden" value="<?php echo $user_id;?>" name="user_id" />
				<input type="hidden" value="<?php echo $total_amount;?>" name="total_amount" />
				<h4>Payable amount: <?php echo $total_amount+$tips_amount; ?> <?php echo $pay_currency; ?></h4>
				<ul>
					<input type="hidden" name="card_type" id="card_type" value=""/>
					<li>
						<label><?php if ($this->lang->line('stripe_enter_card_number') != '') echo stripslashes($this->lang->line('stripe_enter_card_number')); else echo 'Card number'; ?></label>
						<input type="text" class="" placeholder="1234 5678 9012 3456" id="card_number" name="card_number"></input>
					</li>
					<li class="vertical">
						<ul>
							<li>
								<label>Exp month</label>
								<select id="exp_month" name="exp_month" class="">
									<option value="" hidden="hidden">MM</option>
									<option value="01">01</option>
									<option value="02">02</option>
									<option value="03">03</option>
									<option value="04">04</option>
									<option value="05">05</option>
									<option value="06">06</option>
									<option value="07">07</option>
									<option value="08">08</option>
									<option value="09">09</option>
									<option value="10">10</option>
									<option value="11">11</option>
									<option value="12">12</option>
								</select>
							</li>
							<li>
								<label>Exp year</label>
								<select id="exp_year" name="exp_year" class="">
									<option value="" hidden="hidden">YYYY</option>
									<?php for($i=get_time_to_string('Y');$i< (get_time_to_string('Y') + 30);$i++){ ?>
									<option value="<?php echo $i; ?>"><?php echo $i; ?></option>
									<?php } ?>
								</select>
							</li>
						</ul>
					</li>
					<li>
						<label>CVV</label>
						<input type="password" value="" maxlength="3" id="cvc_number" name="cvc_number">
					</li>
					<li><input type="submit" name="card_submit" id="cardSubmitBtn" value="Proceed" class="payment-btn" disabled="true" ></li>
				</ul>
			</form>
		</div>
	</div>
</body>
</html>