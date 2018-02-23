<?php

require_once ("paypalfunctions.php");
// ==================================
// PayPal Express Checkout Module
// ==================================

//'------------------------------------
//' The paymentAmount is the total value of 
//' the shopping cart, that was set 
//' earlier in a session variable 
//' by the shopping cart page
//'------------------------------------
$paymentAmount = isset($_POST['Payment_Amount']) ? $_POST['Payment_Amount'] : $_COOKIE["Payment_Amount"];

//'------------------------------------
//' The currencyCodeType and paymentType 
//' are set to the selections made on the Integration Assistant 
//'------------------------------------
$currencyCodeType = isset($_POST['currency']) ? $_POST['currency'] : "USD";
$paymentType = "Sale";

//'------------------------------------
//' The returnURL is the location where buyers return to when a
//' payment has been succesfully authorized.
//'
//' This is set to the value entered on the Integration Assistant 
//'------------------------------------
$returnURL = kigo_site_url("paypal-confirmation");

//'------------------------------------
//' The cancelURL is the location buyers are sent to when they hit the
//' cancel button during authorization of payment during the PayPal flow
//'
//' This is set to the value entered on the Integration Assistant 
//'------------------------------------
$cancelURL = kigo_site_url("paypal-cancelled");

//'------------------------------------
//' Calls the SetExpressCheckout API call
//'
//' The CallShortcutExpressCheckout function is defined in the file PayPalFunctions.php,
//' it is included at the top of this file.
//'-------------------------------------------------

$extrasObj = array(
	'notify_url'	=> 'PAYMENTREQUEST_0_NOTIFYURL',
	//'bookingID'		=> 'PAYMENTREQUEST_0_INVNUM',
	'custom_field'	=> 'PAYMENTREQUEST_0_CUSTOM',
	'no-shipping'	=> 'NOSHIPPING',
	'desc'			=> 'PAYMENTREQUEST_0_DESC'
);

$extras = [];
foreach($extrasObj as $key => $value) {
	if(isset($_POST[$key])) { $extras[$value] = $_POST[$key]; }
}

$resArray = CallShortcutExpressCheckout ($paymentAmount, $currencyCodeType, $paymentType, $returnURL, $cancelURL, $extras);
$ack = strtoupper($resArray["ACK"]);
if($ack=="SUCCESS" || $ack=="SUCCESSWITHWARNING")
{
	RedirectToPayPal ( $resArray["TOKEN"] );
} 
else  
{
	//Display a user friendly Error on the page using any of the following error information returned by PayPal
	$ErrorCode = urldecode($resArray["L_ERRORCODE0"]);
	$ErrorShortMsg = urldecode($resArray["L_SHORTMESSAGE0"]);
	$ErrorLongMsg = urldecode($resArray["L_LONGMESSAGE0"]);
	$ErrorSeverityCode = urldecode($resArray["L_SEVERITYCODE0"]);
	
	echo "<br />SetExpressCheckout API call failed.";
	echo "<br />Detailed Error Message: " . $ErrorLongMsg;
	echo "<br />Short Error Message: " . $ErrorShortMsg;
	echo "<br />Error Code: " . $ErrorCode;
	echo "<br />Error Severity Code: " . $ErrorSeverityCode;
	echo "<br /><br />Payment Amount: " . $paymentAmount;
	echo "<br />Currency Code: " . $currencyCodeType;
	echo "<br />Payment Type: " . $paymentType;
	echo "<br />Return URL: " . $returnURL;
	echo "<br />Cancel URL: " . $cancelURL;
	echo "<br />ACK: " . $ack;
	echo "<br /><br />Response<pre>"; print_r($resArray); echo "</pre>";
	echo "<br /><br />Session<pre>"; print_r($_SESSION); echo "</pre>";
	echo "<br /><br />Testing:<br />".$API_Endpoint."<br />".$API_UserName."<br />".$API_Password."<br />".$API_Signature."<br />".$SandboxFlag;
}
?>