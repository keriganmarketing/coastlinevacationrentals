<?php
// Exit if accessed directly
if ( !defined('ABSPATH')) exit;
/*
Template Name: PayPal Confirmation Page
*/

$token = isset($_GET['token']) ? $_GET['token'] : false;
if(!$token) { wp_redirect( home_url() ); exit; }

require_once ("includes/paypal/paypalfunctions.php");

if($token) {
    $details = [];
    $details = GetShippingDetails($token);
}

$textdata = getbapitextdata(); 

//Setting this manually as the shopping cart page might not be saving this correctly
if(isset($_GET['PayerID'])) {
    setcookie('payer_id', $_GET['PayerID'], "/", time()+3600);
}

get_header();

?>

<article class="full-width-page">	
<div class="row-fluid">
	<article class="span12">   	

		<?php 
            
            $error = false;
            $error_msg = false;

            
            if($token) {
                
                if($_POST) {
                    $data = $_POST;

                } else {

                    if(isset($_GET['debug'])) { 
                        echo "Token: ".$token."<br />";
                        echo "Details:<pre>"; print_r($details); echo "</pre>"; }

                    $user = explode('|', $details['PAYMENTREQUEST_0_DESC']);
                    $custom = explode('|', $details['PAYMENTREQUEST_0_CUSTOM']); //solutionID|bookingID|bookingType|syncLinkID


                    $options = array(
                        'renterEmail'   => $user[0],
                        'LastName'  => $user[1]
                    );


                    $bapi = getBAPIObj();
                    $booking = $bapi->get('booking', array($custom[1]), $options);

                    if(isset($_GET['debug'])) { echo "Booking ID:".$custom[1]."<pre>"; print_r($options); echo "</pre>"; }

                    $booking = $booking['result'][0];

                    if(isset($_GET['debug'])) { echo "Booking:<pre>"; print_r($booking); echo "</pre>"; }

                    if(!$booking) {
                        $error = sprintf($textdata['Booking %s was not found.'], $details['INVNUM']);

                        if(!isset($_GET['retry'])) { $error .= ' <a href="'.$_SERVER['REQUEST_URI'].'&retry'.'" style="color:#843534;">'.$textdata['Try again'].'</a>'; }

                        $options = json_decode(get_option('bapi_solutiondata'));
                        //echo "<pre>"; print_r($options); echo "</pre>";
                        $phone = $options->PrimaryPhone;
                        $email = $options->PrimaryEmail;

                        ob_start();
                    ?>

                        <h4><?php echo $textdata['Please contact us to complete this transaction:']; ?></h4>
                        <b><?php if($bookingID = $custom[1]) { echo $textdata['Booking ID']; ?>:</b> <?php echo $bookingID; ?><br /> <?php } ?>
                        <b><?php if($phone): echo $textdata['Phone']; ?>:</b> <?php echo $phone; ?> <br /><?php endif; ?>
                        <b><?php if($email): echo $textdata['Email']; ?>:</b> <a href="mailto:<?php echo $email; ?>"><?php echo $email; ?></a><?php endif; ?>

                    <?php

                        $error_msg .= ob_get_clean();
                    }
                    
                    
                    $data = array(
                        'booking_id'        => $booking['ID'],
                        'status'            => $booking['Status'],
                        'checkin'           => $booking['Check_In']['LocalShortDate']." ".$booking['Check_In']['LocalShortTime'],
                        'checkout'          => $booking['Check_Out']['LocalShortDate']." ".$booking['Check_Out']['LocalShortTime'],
                        'property_name'     => $booking['UnitBooked']['UnitName'],
                        'due'               => $booking['Statement']['Total2']['LocalCurrencySymbol'].$details['AMT'],
                        'total'             => $booking['Statement']['Total2']['LocalCurrencySymbol'].$booking['Statement']['Total2']['LocalSValue2']
                    );
                }

                if(isset($_GET['debug'])) { echo "Data:<pre>"; print_r($data); echo "</pre>"; }
            }
        ?>



        <?php

            // if(isset($_GET['token'])) {
            //     $token = $_GET['token'];
            // }

            // if(isset($_POST['token'])) {
            //     $token = $_POST['token'];
            // }

            


            /*==================================================================
             PayPal Express Checkout Call
             ===================================================================
            */

            if ( $_POST && isset($token) && isset($_GET['PayerID']) ) {

                if($booking['Status'] != 'X') {//Process if not canceled
                    
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


                    $resArray = ConfirmPayment ( $finalPaymentAmount );


                    if($_COOKIE['paypal_sandbox'] === true  && isset($_GET['debug'])) {
                        echo "resArray:<pre>"; print_r($resArray); echo "</pre>";
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
                        $error = $textdata['There was an error with PayPal'];
                        //Display a user friendly Error on the page using any of the following error information returned by PayPal
                        $ErrorCode = urldecode($resArray["L_ERRORCODE0"]);
                        $ErrorShortMsg = urldecode($resArray["L_SHORTMESSAGE0"]);
                        $ErrorLongMsg = urldecode($resArray["L_LONGMESSAGE0"]);
                        $ErrorSeverityCode = urldecode($resArray["L_SEVERITYCODE0"]);

                        $error_msg = '<h4>'.$textdata["Unfortunately, your payment failed. Don't worry, your booking is safe; but you will need to try your payment again."].'</h3>';

                        if($ErrorCode == 10486 || $ErrorCode == 10422) {
                            $url = $PAYPAL_URL.$token;
                            header("Location: ".$url);
                        } else {
                            $url = "/makepayment?bid=".$data['booking_id'];
                        }
                        
                        $error_msg .= '<a href="'.$url.'" class="btn btn-primary">'.$textdata['Try again'].'</a> ';
                        $error_msg .= ' <b> '.$textdata['Booking ID'].'</b>'.$data['booking_id']."<br />";

                        if(isset($_GET['debug'])) {
                            $error_msg .= "GetExpressCheckoutDetails API call failed. <br />";
                            $error_msg .= "<br />Detailed Error Message: " . $ErrorLongMsg;
                            $error_msg .= "<br />Short Error Message: " . $ErrorShortMsg;
                            $error_msg .= "<br />Error Code: " . $ErrorCode;
                            $error_msg .= "<br />Error Severity Code: " . $ErrorSeverityCode;
                            //if(isset($_GET['debug'])) { $error_msg .= "<br /><br />Session:<pre>"; print_r($_SESSION); echo "</pre>"; }
                            $error_msg .= "<br /><br />Testing: Can we retry the transaction somehow?<br />".$API_Endpoint."<br />".$API_UserName."<br />".$API_Password."<br />".$API_Signature."<br />".$SandboxFlag;
                        }
                    }
                }
            }
                    
        ?>


        <?php
            $success = $ack == "SUCCESS" || $ack == "SUCCESSWITHWARNING";
            $title = $success ? $textdata['Payment Confirmed'] : $textdata['Booking Confirmed'];
        ?>


        <?php if(!$error) { ?>
            <div class="alert alert-success" role="alert">
                <h2><?php echo $title; ?></h2>    
            </div>


            <div class="row-fluid">
                <form action="<?php echo htmlspecialchars($_SERVER['REQUEST_URI']); ?>" method="POST">
                    
                    <?php 
                        $complete = !$_POST ? 0 : 1;
                        $labels = array(
                            $textdata['Booking Complete'],
                            $textdata['Payment Complete']
                        );
                        $steps = count($labels);
                    ?>

                    <div class="span6">
                        <div style="float:left"><b><?php echo $textdata['Create Booking']; ?></b></div>
                        <div style="float:right"><b><?php echo $textdata['Complete Payment']; ?></b></div>
                        <div class="clearfix"></div>

                        <div class="progress">
                            <div class="progress-bar" role="progressbar" aria-valuenow="<?php echo 100*($complete+1/$steps); ?>" aria-valuemin="0" aria-valuemax="100" style="width: <?php echo 100*($complete+1/$steps); ?>%; background: #5cb85c;">
                                <span style="color: #fff; padding-left: .5em;">
                                    <?php echo $labels[$complete]; ?>
                                </span>
                            </div>
                        </div>

                        <?php if(!$_POST) { ?>
                        <h3><?php echo sprintf( $textdata['Next, please confirm your %s PayPal payment.'], $data['due']); ?></h3>
                        <input type="hidden" name="token" value="<?php echo $_GET['token']; ?>">
                        <label for="token" style="font-weight: bold;"><button type="submit" class="btn btn-primary btn-large"><?php echo $textdata['Confirm this payment']; ?></button></label>
                        <?php } ?>

                        <!-- content here -->
                        <?php
                            if (have_posts() && $_POST && !$error) :
                               while (have_posts()) :
                                  the_post();
                                        the_content();
                               endwhile;
                            endif;
                        ?>
                    </div>

                    <div class="table-responsive span6">
                        <table class="table table-bordered" width="100">
                            <?php if(isset($data['booking_id'])) { ?>
                            <tr>
                                <th><?php echo $textdata['Booking ID']; ?></th>
                                <td>
                                    <?php echo $id = $data['booking_id']; ?>
                                    <input type="hidden" name="booking_id" value="<?php echo $id; ?>" />
                                </td>
                            </tr>
                            <tr>
                                <th><?php echo $textdata['Property Name']; ?>:</th>
                                <td>
                                    <?php echo $property = $data['property_name']; ?>
                                    <input type="hidden" name="property_name" value="<?php echo $property; ?>" />
                                </td>
                            </tr>
                            <tr>
                                <th><?php echo $textdata['Check-In']; ?>:</th>
                                <td>
                                    <?php echo $checkin = $data['checkin']; ?>
                                    <input type="hidden" name="checkin" value="<?php echo $checkin; ?>" />
                                </td>
                            </tr>
                            <tr>
                                <th><?php echo $textdata['Check-Out']; ?>:</th>
                                <td>
                                    <?php echo $checkout = $data['checkout']; ?>
                                    <input type="hidden" name="checkout" value="<?php echo $checkout; ?>" />
                                </td>
                            </tr>

                            <tr>
                                <th><?php echo $textdata['Total']; ?>:</th>
                                <td>
                                    <?php echo $total = $data['total']; ?>
                                    <input type="hidden" name="total" value="<?php echo $total; ?>" />
                                </td>
                            </tr>
                            <?php } ?>

                            <?php if($amt) { ?>
                            <tr>
                                <th><?php echo $textdata['Amount Paid']; ?></th>
                                <td><?php echo $amt." ".$currencyCode; ?></td>
                            </tr>
                            <?php } ?>

                            <?php if($data['due']) { ?>
                            <tr>
                                <th><?php echo $textdatap['Amount Due Now']; ?></th>
                                <td><?php echo $data['due']; ?></td>
                            </tr>
                            <?php } ?>
                        </table>
                    </div>

                </form>
            </div>

        <?php } 


        if($error) { ?>

            <div class="alert alert-error" role="alert">
            <h2><?php echo $error; ?></h2>    
            </div>
            <?php if($error_msg) { echo $error_msg; } ?>

        <?php } ?>

        

        


    </article>
    <?php 
	/* this is for the booking page */
	if(function_exists('getSSL')) {
    	//getSSL();
	} ?>
</div>
</article>
<?php get_footer(); ?>