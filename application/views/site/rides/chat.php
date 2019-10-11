<?php 
if($_SERVER['HTTP_HOST']=="192.168.1.244"){
  $bosh_url= "http://192.168.1.244:5280/http-bind/";
  $domain_name='192.168.1.244';
} else{
	$host_name = $domain_name = '';
	if($_SERVER['HTTP_HOST']=="booktaxi.casperon.co"){
		$host_name = $domain_name = 'ejabberd.casperon.co';
	} else  if (is_file('xmpp-master/config.php')) {
		require_once 'xmpp-master/config.php';
		$host_name=vhost_name;
		$domain_name=vhost_name;
	}
	if(isset($_SERVER['HTTPS']) && $_SERVER['HTTPS'] == "on") {
	   $bosh_url= "https://".$host_name.":5282/http-bind/";
	} else {
	   $bosh_url= "http://".$host_name.":5280/http-bind/";
	}
}
?>
<script>
var BOSH_SERVICE = '<?php echo $bosh_url; ?>';
var connection = null;
function onConnect(status)
{
    if (status == Strophe.Status.CONNECTING) {
	console.log('Strophe is connecting.');
    } else if (status == Strophe.Status.CONNFAIL) {
	console.log('Strophe failed to connect.');
	$('#connect').get(0).value = 'connect';
    } else if (status == Strophe.Status.DISCONNECTING) {
	console.log('Strophe is disconnecting.');
    } else if (status == Strophe.Status.DISCONNECTED) {
	console.log('Strophe is disconnected.');
	$('#connect').get(0).value = 'connect';
    } else if (status == Strophe.Status.CONNECTED) {
	console.log('Strophe is connected.');
	console.log('ECHOBOT: Send a message to ' + connection.jid + 
	    ' to talk to me.');

	connection.addHandler(onMessage, null, 'message', null, null,  null); 
	connection.send($pres().tree());
    }
}

$(document).ready(function () {
connection = new Strophe.Connection(BOSH_SERVICE);
connection.connect('<?php echo $website_tracking; ?>@<?php echo $domain_name; ?>','trackpass',onConnect);
<?php if($ride_info->row()->ride_status=='Completed')  { ?>
 connection.disconnect();	
<?php } ?>
});
</script>