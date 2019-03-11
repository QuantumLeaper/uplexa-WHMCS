<?php
include("../../../init.php");
include("../../../includes/functions.php");
include("../../../includes/gatewayfunctions.php");
include("../../../includes/invoicefunctions.php");
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);


$gatewaymodule = "uplexa";
$GATEWAY = getGatewayVariables($gatewaymodule);
if(!$GATEWAY["type"]) die("Module not activated");
require_once('library.php');

$link = $GATEWAY['daemon_host'].":".$GATEWAY['daemon_port']."/json_rpc";


function uplexa_payment_id(){
    if(!isset($_COOKIE['payment_id'])) {
		$payment_id  = bin2hex(openssl_random_pseudo_bytes(8));
		setcookie('payment_id', $payment_id, time()+2700);
	} else {
		$payment_id = $_COOKIE['payment_id'];
    }
		return $payment_id;

}

$uplexa_daemon = new uPlexa_rpc($link);

$message = "Waiting for your payment.";
$_POST  = filter_input_array(INPUT_POST, FILTER_SANITIZE_STRING);
//$currency = stripslashes($_POST['currency']);
//$amount_upx = stripslashes($_POST['amount_upx']);
$currency = $_SESSION["currency"];
$amount_upx = $_SESSION["amount_upx"];
$amount = $_SESSION["amount"];
$invoice_id = $_SESSION["invoice_id"];
//$amount = stripslashes($_POST['amount']);
$payment_id = uplexa_payment_id();
//$invoice_id = stripslashes($_POST['invoice_id']);
$array_integrated_address = $uplexa_daemon->make_integrated_address($payment_id);
$address = $array_integrated_address['integrated_address'];
$uri  =  "uplexa:$address?amount=$amount_upx";

$secretKey = $GATEWAY['secretkey'];
$hash = md5($invoice_id . $payment_id . $amount_upx . $secretKey);
echo "<link href='style.css' rel='stylesheet'>";
echo  "<script src='https://code.jquery.com/jquery-3.2.1.min.js'></script>";
echo  "<script src='spin.js'></script>";


echo "<title>Invoice</title>";
echo "<head>
        <!--Import Google Icon Font-->
        <link href='https://fonts.googleapis.com/icon?family=Material+Icons' rel='stylesheet'>
        <link href='https://fonts.googleapis.com/css?family=Montserrat:400,800' rel='stylesheet'>
        <!--Let browser know website is optimized for mobile-->
            <meta name='viewport' content='width=device-width, initial-scale=1.0'/>
            </head>
            <body>
            <!-- page container  -->
            <div class='page-container'>
                <img src='uplexalogo.png' width='200' />

        <div class='progress' id='progress'></div>

			<script>
				var opts = {
					lines: 10, // The number of lines to draw
					length: 7, // The length of each line
					width: 4, // The line thickness
					radius: 10, // The radius of the inner circle
					corners: 1, // Corner roundness (0..1)
					rotate: 0, // The rotation offset
					color: '#000', // #rgb or #rrggbb
					speed: 1, // Rounds per second
					trail: 60, // Afterglow percentage
					shadow: false, // Whether to render a shadow
					hwaccel: false, // Whether to use hardware acceleration
					className: 'spinner', // The CSS class to assign to the spinner
					zIndex: 2e9, // The z-index (defaults to 2000000000)
					top: 25, // Top position relative to parent in px
					left: 0 // Left position relative to parent in px
				};
				var target = document.getElementById('progress');
				var spinner = new Spinner(opts).spin(target);
			</script>

        <div id='container'></div>
        	    <div class='alert alert-warning' id='message'>".$message."</div><br>
          <!-- uPlexa container payment box -->
            <div class='container-upx-payment'>
            <!-- header -->
            <div class='header-upx-payment'>
            <span class='upx-payment-text-header'><h2>UPLEXA PAYMENT</h2></span>
            </div>
            <!-- end header -->
            <!-- upx content box -->
            <div class='content-upx-payment'>
            <div class='upx-amount-send'>
            <span class='upx-label'>Send:</span>
            <div class='upx-amount-box'>".$amount_upx." UPX ($" . $amount . " " . $currency .") </div><div class='upx-box'>UPX</div>
            </div>
            <div class='upx-address'>
            <span class='upx-label'>To this address:</span>
            <div class='upx-address-box'>". $array_integrated_address['integrated_address']."</div>
            </div>
            <div class='upx-qr-code'>
            <span class='upx-label'>Or scan QR:</span>
            <div class='upx-qr-code-box'><img src='https://api.qrserver.com/v1/create-qr-code/? size=200x200&data=".$uri."' /></div>
            </div>
            <div class='clear'></div>
            </div>
            <!-- end content box -->
            <!-- footer upx payment -->
            <div class='footer-upx-payment'>
            <a href='https://uplexa.com' target='_blank'>Help</a> | <a href='https://uplexa.com' target='_blank'>About uPlexa</a>
            </div>
            <!-- end footer upx payment -->
            </div>
            <!-- end uPlexa container payment box -->
            </div>
            <!-- end page container  -->
            </body>
        ";


echo "<script> function verify(){

$.ajax({ url : 'verify.php',
	type : 'POST',
	data: { 'amount_upx' : '".$amount_upx."', 'payment_id' : '".$payment_id."', 'invoice_id' : '".$invoice_id."', 'amount' : '".$amount."', 'hash' : '".$hash."', 'currency' : '".$currency."'},
	success: function(msg) {
		console.log(msg);
		$('#message').text(msg);
		if(msg=='Payment has been received.') {
			//redirect to Paid invoice
            window.location.href = '/viewinvoice.php?id=$invoice_id';
		}
	},
   error: function (req, status, err) {
        $('#message').text(err);
        console.log('Something went wrong', status, err);

    }


			});
}
verify();
setInterval(function(){ verify()}, 5000);
</script>";
?>
