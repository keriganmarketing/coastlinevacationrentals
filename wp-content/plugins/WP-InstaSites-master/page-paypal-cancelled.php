<?php
// Exit if accessed directly
if ( !defined('ABSPATH')) exit;
/*
Template Name: PayPal Confirmation Page
*/

require_once ("includes/paypal/paypalfunctions.php");
?>

<?php get_header(); ?>
<article class="full-width-page">	
<div class="row-fluid">
	<article class="span12">   	
		<?php

            if(isset($_GET['debug'])) {
                echo "<pre>"; print_r($_POST); echo "</pre>";
                echo "<pre>"; print_r($_SESSION); echo "</pre>";
            }

            if(isset($_GET['token'])) {
                $token = $_GET['token'];
            }

            /*==================================================================
             PayPal Express Checkout Call
             ===================================================================
            */

            if ( isset($token) && isset($_GET['PayerID']) ) {

                //Setting this manually as the shopping cart page might not be saving this correctly
                if(isset($_GET['PayerID'])) {
                    setcookie('payer_id', $_GET['PayerID']);
                }
                /*
                '------------------------------------
                ' The paymentAmount is the total value of 
                ' the shopping cart, that was set 
                ' earlier in a session variable 
                ' by the shopping cart page
                '------------------------------------
                */
                
                $finalPaymentAmount =  $_COOKIE["Payment_Amount"];
                    
                /*
                '------------------------------------
                ' Calls the DoExpressCheckoutPayment API call
                '
                ' The ConfirmPayment function is defined in the file paypalfunctions.php,
                ' that is included at the top of this file.
                '-------------------------------------------------
                */

                /*
                ** Check to see if the booking was canceled first to handle delayed payments
                */
                $resArray = ConfirmPayment ( $finalPaymentAmount );


                if($_COOKIE['paypal_sandbox'] === true) {
                    echo "<pre>"; print_r($resArray); echo "</pre>";
                }

                

                $ack = strtoupper($resArray["ACK"]);
                if( $ack == "SUCCESS" || $ack == "SUCCESSWITHWARNING" )
                {
                    /*
                    '********************************************************************************************************************
                    '
                    ' THE PARTNER SHOULD SAVE THE KEY TRANSACTION RELATED INFORMATION LIKE 
                    '                    transactionId & orderTime 
                    '  IN THEIR OWN  DATABASE
                    ' AND THE REST OF THE INFORMATION CAN BE USED TO UNDERSTAND THE STATUS OF THE PAYMENT 
                    '
                    '********************************************************************************************************************
                    */

                    $transactionId      = $resArray["PAYMENTINFO_0_TRANSACTIONID"]; // ' Unique transaction ID of the payment. Note:  If the PaymentAction of the request was Authorization or Order, this value is your AuthorizationID for use with the Authorization & Capture APIs. 
                    $transactionType    = $resArray["PAYMENTINFO_0_TRANSACTIONTYPE"]; //' The type of transaction Possible values: l  cart l  express-checkout 
                    $paymentType        = $resArray["PAYMENTINFO_0_PAYMENTTYPE"];  //' Indicates whether the payment is instant or delayed. Possible values: l  none l  echeck l  instant 
                    $orderTime          = $resArray["PAYMENTINFO_0_ORDERTIME"];  //' Time/date stamp of payment
                    $amt                = $resArray["PAYMENTINFO_0_AMT"];  //' The final amount charged, including any shipping and taxes from your Merchant Profile.
                    $currencyCode       = $resArray["PAYMENTINFO_0_CURRENCYCODE"];  //' A three-character currency code for one of the currencies listed in PayPay-Supported Transactional Currencies. Default: USD. 
                    $feeAmt             = $resArray["PAYMENTINFO_0_FEEAMT"];  //' PayPal fee amount charged for the transaction
                    $settleAmt          = $resArray["PAYMENTINFO_0_SETTLEAMT"];  //' Amount deposited in your PayPal account after a currency conversion.
                    $taxAmt             = $resArray["PAYMENTINFO_0_TAXAMT"];  //' Tax charged on the transaction.
                    $exchangeRate       = $resArray["PAYMENTINFO_0_EXCHANGERATE"];  //' Exchange rate if a currency conversion occurred. Relevant only if your are billing in their non-primary currency. If the customer chooses to pay with a currency other than the non-primary currency, the conversion occurs in the customer's account.
                    
                    /*
                    ' Status of the payment: 
                            'Completed: The payment has been completed, and the funds have been added successfully to your account balance.
                            'Pending: The payment is pending. See the PendingReason element for more information. 
                    */
                    
                    $paymentStatus  = $resArray["PAYMENTINFO_0_PAYMENTSTATUS"]; 

                    /*
                    'The reason the payment is pending:
                    '  none: No pending reason 
                    '  address: The payment is pending because your customer did not include a confirmed shipping address and your Payment Receiving Preferences is set such that you want to manually accept or deny each of these payments. To change your preference, go to the Preferences section of your Profile. 
                    '  echeck: The payment is pending because it was made by an eCheck that has not yet cleared. 
                    '  intl: The payment is pending because you hold a non-U.S. account and do not have a withdrawal mechanism. You must manually accept or deny this payment from your Account Overview.       
                    '  multi-currency: You do not have a balance in the currency sent, and you do not have your Payment Receiving Preferences set to automatically convert and accept this payment. You must manually accept or deny this payment. 
                    '  verify: The payment is pending because you are not yet verified. You must verify your account before you can accept this payment. 
                    '  other: The payment is pending for a reason other than those listed above. For more information, contact PayPal customer service. 
                    */
                    
                    $pendingReason  = $resArray["PAYMENTINFO_0_PENDINGREASON"];  

                    /*
                    'The reason for a reversal if TransactionType is reversal:
                    '  none: No reason code 
                    '  chargeback: A reversal has occurred on this transaction due to a chargeback by your customer. 
                    '  guarantee: A reversal has occurred on this transaction due to your customer triggering a money-back guarantee. 
                    '  buyer-complaint: A reversal has occurred on this transaction due to a complaint about the transaction from your customer. 
                    '  refund: A reversal has occurred on this transaction because you have given the customer a refund. 
                    '  other: A reversal has occurred on this transaction due to a reason not listed above. 
                    */
                    
                    $reasonCode     = $resArray["PAYMENTINFO_0_REASONCODE"];   
                }
                else  
                {
                    //Display a user friendly Error on the page using any of the following error information returned by PayPal
                    $ErrorCode = urldecode($resArray["L_ERRORCODE0"]);
                    $ErrorShortMsg = urldecode($resArray["L_SHORTMESSAGE0"]);
                    $ErrorLongMsg = urldecode($resArray["L_LONGMESSAGE0"]);
                    $ErrorSeverityCode = urldecode($resArray["L_SEVERITYCODE0"]);
                    
                    echo "GetExpressCheckoutDetails API call failed. <br />";
                    echo "<br />Detailed Error Message: " . $ErrorLongMsg;
                    echo "<br />Short Error Message: " . $ErrorShortMsg;
                    echo "<br />Error Code: " . $ErrorCode;
                    echo "<br />Error Severity Code: " . $ErrorSeverityCode;
                    echo "<br /><br />Session:<pre>"; print_r($_SESSION); echo "</pre>";
                    echo "<br /><br />Testing:<br />".$API_Endpoint."<br />".$API_UserName."<br />".$API_Password."<br />".$API_Signature."<br />".$SandboxFlag;
                }
            }    
                    
        ?>


        <h3><?php _e("We're sorry, but your payment was unsuccessful.") ?></h3>
        

        <?php
            if (have_posts()) :
               while (have_posts()) :
                  the_post();
                        the_content();
               endwhile;
            endif;
        ?>

        <hr>

        <?php 
            $options = json_decode(get_option('bapi_solutiondata'));
            //echo "<pre>"; print_r($options); echo "</pre>";
            $phone = $options->PrimaryPhone;
            $email = $options->PrimaryEmail;
        ?>

        <h4><?php _e('Please contact us to complete this transaction:'); ?></h4>
        <b><?php if($bookingID = json_decode($_COOKIE['nvpReqArray'])['PAYMENTREQUEST_0_INVNUM']) { _e('Booking ID'); ?>:</b> <?php echo $bookingID; ?><br /> <?php } ?>
        <b><?php if($phone): _e('Phone'); ?>:</b> <?php echo $phone; ?> <br /><?php endif; ?>
        <b><?php if($email): _e('Email'); ?>:</b> <a href="mailto:<?php echo $email; ?>"><?php echo $email; ?></a><?php endif; ?>
        <hr />
        <a href="<?php echo get_permalink(get_page_by_path('rentals/rentalsearch')); ?>" class="btn btn-primary"><?php _e('Search'); ?></a>



    </article>
    <?php 
	/* this is for the booking page */
	if(function_exists('getSSL')) {
    	//getSSL();
	} ?>
</div>
</article>
<?php get_footer(); ?>