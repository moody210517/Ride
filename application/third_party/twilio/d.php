<?php 
	if(isset($_GET['act'])){
		if($_GET['act'] == 'sms'){
			demo_sms();
		} 
		if($_GET['act'] == 'call'){
			demo_call();
		}
	} else {
		echo '<h2 style="text-align:center; margin-top:20%;">Specify act ( sms or call ) to complete process...</h2>';
	}
	function demo_sms(){ 
		
		/* $from = '+12563990330';
		$to = '+919566399141'; $message = 'Hello Test Exist';
		$response = $this->twilio->sms($from, $to, $message); echo '<pre>'; print_r($response);  */
	
		// Download the library and copy into the folder containing this file.
		require('Services/Twilio.php');

		$account_sid = "ACf749984f1580dcd1ed53dc7a153e2cbe"; // Your Twilio account sid
		$auth_token = "695d8c8e8694bcb0d3f05820f17a8ec9"; // Your Twilio auth token
		
		#ACa53bd0595d772a05d71e5116704dbf30  bff6fc83b46173f5cbc73af9aca7519b  +12013088710  
		
		$client = new Services_Twilio($account_sid, $auth_token); #echo '<pre>'; print_r($client); die;
		$message = $client->account->messages->sendMessage(
		  '+12563990330', // From a Twilio number in your account
		  '+919566399141', // Text any number
		  "Hello Test"
		);

		echo '<pre>'; print_r($message);
	
	}
		
	function demo_call(){

		
		// Download the library and copy into the folder containing this file.
		require('Services/Twilio.php');

		$account_sid = 'ACf749984f1580dcd1ed53dc7a153e2cbe'; 
		$auth_token = '695d8c8e8694bcb0d3f05820f17a8ec9'; 
		$client = new Services_Twilio($account_sid, $auth_token); 
		 
		$response = $client->account->calls->create('+12563990330', array('+919566399141'), 'http://twimlets.com/ACf749984f1580dcd1ed53dc7a153e2cbe/call-foewared-test', array( 
			'Method' => 'GET',  
			'FallbackMethod' => 'GET',  
			'StatusCallbackMethod' => 'GET',    
			'Record' => 'false', 
		));
		
		
		
		echo '<pre>'; print_r($response);

	}
?>

