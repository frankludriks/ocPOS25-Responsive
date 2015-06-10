<?php
// authorizenet_aim.php


// unless using localhost, require SSL
if ($_SERVER['SERVER_PORT']!=443 && $_SERVER['SERVER_NAME'] != 'localhost') {   
    // if using SSL port other than 443, set here, including the leading colon ( : )
    // Example:  $sslport=':444';
    $sslport=''; 
    $url = "https://". $_SERVER['SERVER_NAME'] . $sslport . $_SERVER['REQUEST_URI'];
    header("Location: $url");
}

include("includes/db.php");
include("includes/functions.php");
include("includes/session.php");

LoadLangFiles($lang);

    if (isset($_GET['bill_addr']) && isset($_GET['bill_addr'])) {
        $_SESSION['Orders'][$_SESSION['CurrentOrderIndex']]->BillingID = $_GET['bill_addr'];
        $_SESSION['Orders'][$_SESSION['CurrentOrderIndex']]->ShippingID = $_GET['ship_addr'];
    }

// Main  (from before_process)

    function sendTransactionToGateway($parameters) {
        if ($auth_net_test_mode == 1) {
            $ch = curl_init("https://test.authorize.net/gateway/transact.dll"); 
        } else {
            $ch = curl_init("https://secure.authorize.net/gateway/transact.dll"); 
        }
        
        curl_setopt($ch, CURLOPT_HEADER, 0); // set to 0 to eliminate header info from response
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1); // Returns response data instead of TRUE(1)
        curl_setopt($ch, CURLOPT_POSTFIELDS, rtrim( $parameters, "& " )); // use HTTP POST to send form data
        ### curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, FALSE); // uncomment this line if you get no gateway response. ###
        $resp = curl_exec($ch); //execute post and get results
        curl_close ($ch);

// debug gateway response
if ($authnet_debug == 1) {
    echo($resp);
    echo('<br><br>');
}
            
            $response = explode("|", $resp);
            
            return($response);
	}
    

// brought in from includes/function.php.  needs refactoring
		// Get Customer Info		
		if($_SESSION['Orders'][$_SESSION['CurrentOrderIndex']]->CustomerID == $default_customer_id) {   // only need name if default in-store customer 
			$Q_Customer = mysql_query("SELECT c.* FROM
				" . CUSTOMERS . " c WHERE
				c.customers_id = '" . $_SESSION['Orders'][$_SESSION['CurrentOrderIndex']]->CustomerID . "' LIMIT 1");
			$R_Customer = mysql_fetch_assoc($Q_Customer);
		} else {
			$Q_Customer_zonecheck = mysql_query("SELECT entry_zone_id FROM " . ADDRESS_BOOK . " 
				WHERE customers_id = '" . $_SESSION['Orders'][$_SESSION['CurrentOrderIndex']]->CustomerID . "'");
			$R_Customer_zonecheck = mysql_fetch_assoc($Q_Customer_zonecheck);
			if ($R_Customer_zonecheck['entry_zone_id']== 0) {
				$use_zone = 0;
			} else {
				$use_zone = 1;
			}
            
            $default_address_query_raw = "SELECT customers_default_address_id FROM " . CUSTOMERS. " WHERE customers_id = '" . $_SESSION['Orders'][$_SESSION['CurrentOrderIndex']]->CustomerID. "'";
            $default_address_query = mysql_query($default_address_query_raw);
            $default_address_results = mysql_fetch_array($default_address_query);
            $default_address_id = $default_address_results['customers_default_address_id'];
            
            $Q_Customer_sql = "SELECT c.customers_telephone,co.countries_name,z.zone_name FROM
					" . CUSTOMERS . " c, " . ADDRESS_BOOK . " ab, " . COUNTRIES . "  co, " . ZONES . " z WHERE
					c.customers_id = '" . $_SESSION['Orders'][$_SESSION['CurrentOrderIndex']]->CustomerID . "' AND
					ab.customers_id = c.customers_id AND
                    ab.address_book_id = '" . $default_address_id . "' AND 
					co.countries_id = ab.entry_country_id";
                    
            $Q_Customer_Billing_sql = "SELECT ab.*,c.customers_telephone,co.countries_name,z.zone_name FROM
                " . CUSTOMERS . " c, " . ADDRESS_BOOK . " ab, " . COUNTRIES . "  co, " . ZONES . " z WHERE
                c.customers_id = '" . $_SESSION['Orders'][$_SESSION['CurrentOrderIndex']]->CustomerID . "' AND
                ab.customers_id = c.customers_id AND
                ab.address_book_id = '" . $_SESSION['Orders'][$_SESSION['CurrentOrderIndex']]->BillingID . "' AND 
                co.countries_id = ab.entry_country_id";
            
			if ($use_zone == 1) { // in some countries, it is less common to use the zone field
                $Q_Customer_sql .= " AND zone_id = ab.entry_zone_id LIMIT 1";
                $Q_Customer_Billing_sql .= " AND zone_id = ab.entry_zone_id LIMIT 1";
                
			}  else {
                $Q_Customer_sql .= " LIMIT 1";
                $Q_Customer_Billing_sql .= " LIMIT 1";
            }
            
            $Q_Customer = mysql_query($Q_Customer_sql);
            $Q_Customer_Billing = mysql_query($Q_Customer_Billing_sql);            
            
			$R_Customer = mysql_fetch_assoc($Q_Customer);

			$R_Customer_Billing = mysql_fetch_assoc($Q_Customer_Billing);
			$R_Customer_Billing['entry_firstname'] = str_replace("'","\'",$R_Customer_Billing['entry_firstname']);
			$R_Customer_Billing['entry_lastname'] = str_replace("'","\'",$R_Customer_Billing['entry_lastname']);
			$R_Customer_Billing['entry_street_address'] = str_replace("'","\'",$R_Customer_Billing['entry_street_address']);
			$R_Customer_Billing['entry_city'] = str_replace("'","\'",$R_Customer_Billing['entry_city']);
            
		}
            // if no zone name found, make blank instead of Alabama
            if ($R_Customer_Billing['entry_zone_id'] == 0) $R_Customer_Billing['zone_name'] = '';  
// end import from includes/functions.php


$order_error = '';


if (isset($_POST['process']) && $_POST['process'] == '1' && $order_error=='')  {    
    if ($order_error == '') {
      $params				        = array
        (
            "x_login"				=> $auth_net_api_login_id,
            "x_version"				=> "3.1",
            "x_delim_char"			=> "|",
            "x_delim_data"			=> "TRUE",
            "x_type"			   	=> $_POST['authtype'],
            "x_method"				=> "CC",
            "x_tran_key"			=> $auth_net_transaction_key,
            "x_relay_response"	    => "TRUE",
            "x_card_num"			=> $_POST['ccnumber'],
            "x_exp_date"			=> $_POST['ccexp'],
            "x_card_code"           => $_POST['cvv'],
            "x_description"		    => '',
            "x_amount"				=> abs($_POST['charge_amount']),
            "x_first_name"			=> $_POST['firstname'],
            "x_last_name"			=> $_POST['lastname'],
            "x_address"				=> $_POST['street_address'],
            "x_city"				=> $_POST['city'],
            "x_state"				=> $_POST['state'],
            "x_zip"					=> $_POST['postcode'],
            "x_country"				=> $_POST['country']['title'],
        );

      if ($_SESSION['Orders'][$_SESSION['CurrentOrderIndex']]->ReturnOrder) {
        $Q_TransactionID = mysql_query("SELECT cc_number, cc_expires, transaction_details FROM " . ORDERS . " WHERE orders_id = '" . $_SESSION['Orders'][$_SESSION['CurrentOrderIndex']]->ReturnOrder . "'");
        if(mysql_num_rows($Q_TransactionID) == 1) {
            $R_TransactionID = mysql_fetch_array($Q_TransactionID);
            
            $trans_id_step1 = substr($R_TransactionID['transaction_details'], 15);
            $trans_id_step2 = explode(';', $trans_id_step1);
            $trans_id = $trans_id_step2[0];
            $params['x_trans_id'] = $trans_id;
            $params['x_exp_date'] = $R_TransactionID['cc_expires'];
        } else {
            $params['x_card_num'] = $R_TransactionID['cc_number'];
            $params['x_card_code'] = '';
            $params['x_exp_date'] = '';
        }
      }
      
      if ($auth_net_test_mode == 1) {
        $params['x_test_request'] = 'TRUE';
      }
      
      $post_string = '';
      reset($params);
      
      $post_string = "";
      foreach( $params as $key => $value ) $post_string .= "$key=" . urlencode( $value ) . "&";

      $post_string = substr($post_string, 0, -1);
      
      $transaction_response = sendTransactionToGateway($post_string);

if ($authnet_debug == 1) {
      echo('Request:  ' . $post_string);
      echo('<br><br><br>');
      
      if (is_array($transaction_response)) {
        echo('Response array:');
        print_r($transaction_response);
      } else {
        echo('Response string: ' . $transaction_response);
      }
      echo('<br><br><br>');
}


      $error = false;
      reset($transaction_response);
      
/* Authorize.Net return codes
//Response Code
$transaction_response[0]
The overall status of the transaction
    1 = Approved
    2 = Declined
    3 = Error
    4 = Held for Review
 
//Response Subcode
$transaction_response[1]
A code used by the payment gateway for internal transaction tracking

//Response Reason Code
$transaction_response[2] 
A code that represents more details about the result of the transaction

//Response Reason Text
$transaction_response[3] 
A brief description of the result, which corresponds with the response reason code

//Authorization Code
$transaction_response[4] 
The authorization or approval code

//AVS Response
$transaction_response[5] 
The Address Verification Service (AVS) response code
    A = Address (Street) matches, ZIP does not
    B = Address information not provided for AVS check
    E = AVS error
    G = Non-U.S. Card Issuing Bank
    N = No Match on Address (Street) or ZIP
    P = AVS not applicable for this transaction
    R = Retry – System unavailable or timed out
    S = Service not supported by issuer
    U = Address information is unavailable
    W = Nine digit ZIP matches, Address (Street) does not
    X = Address (Street) and nine digit ZIP match
    Y = Address (Street) and five digit ZIP match
    Z = Five digit ZIP matches, Address (Street) does not match

//Transaction ID
$transaction_response[6] 
    nothing returned in test mode

//Invoice Number
$transaction_response[7] 
The merchant assigned invoice number for the transaction

//Description
$transaction_response[8] 

//Amount
$transaction_response[9] 

//Method
$transaction_response[10] 

//Transaction Type
$transaction_response[11] 
The type of credit card transaction
    AUTH_CAPTURE
    AUTH_ONLY
    CAPTURE_ONLY
    CREDIT
    PRIOR_AUTH_CAPTURE
    VOID
    
//Purchase Order Number
$transaction_response[36] 

//MD5 Hash
$transaction_response[37] 

//Card Code Response
$transaction_response[38] 
The card code verification (CCV) response code
    M = Match
    N = No Match
    P = Not Processed
    S = Should have been present
    U = Issuer unable to process request

//Cardholder Authentication Verification Response
$transaction_response[39] 
The cardholder authentication verification response code
    Blank or not present  =  CAVV not validated
    0 = CAVV not validated because erroneous data was submitted
    1 = CAVV failed validation
    2 = CAVV passed validation
    3 = CAVV validation could not be performed; issuer attempt incomplete
    4 = CAVV validation could not be performed; issuer system error
    5 = Reserved for future use
    6 = Reserved for future use
    7 = CAVV attempt – failed validation – issuer available (U.S.-issued card/non-U.S acquirer)
    8 = CAVV attempt – passed validation – issuer available (U.S.-issued card/non-U.S. acquirer)
    9 = CAVV attempt – failed validation – issuer unavailable (U.S.-issued card/non-U.S. acquirer)
    A = CAVV attempt – passed validation – issuer unavailable (U.S.-issued card/non-U.S. acquirer)
    B = CAVV passed validation, information only, no liability shift
*/
      $authtype = strtoupper($_POST['authtype']);

      switch ($transaction_response[0]) {
        case 1: 
            $results = APPROVED . ' ' . TRANSACTION_ID;
            if ($transaction_response[6] == '') {
                $results .= ' ' . NO_TRANS_ID;
            } else {
                $results .= ' ' . $transaction_response[6];
            }
            break;
        case 2: 
            $results = DECLINED;
            if ($transaction_response[2] == 4) {
                $results .= ': ' . STOLEN_CARD;
            } else {
                $results .= ': ' . $transaction_response[3];
            }
            $error = true;
            break;
        case 3: 
            $results = TRANSACTION_ERROR. ': ' . $transaction_response[3];
            $error = true;
            break;
        case 4: 
            $results = HELD_FOR_REVIEW . ': ' . $transaction_response[3];
            $error = true;
            break;
        default: 
            $results = PROCESSING_ERROR;
            $error = true;
            break;
      }
      
       
      if ($error === false) { //           
          $DateTime = date("Y-m-d H:i:s");
           $_SESSION['Orders'][$_SESSION['CurrentOrderIndex']]->Comments = $results . "\r\n\r\n" . $_SESSION['Orders'][$_SESSION['CurrentOrderIndex']]->Comments;
           $_SESSION['Orders'][$_SESSION['CurrentOrderIndex']]->PaymentTransactionID = $transaction_response[6];
           $_SESSION['Orders'][$_SESSION['CurrentOrderIndex']]->cc_last4 = substr($_POST['ccnumber'],-4);
           $_SESSION['Orders'][$_SESSION['CurrentOrderIndex']]->cc_expires = substr($_POST['ccexp'],-4);
      } else {
        $order_error = $results;
        
      }
      
   }
}

if (isset($_POST['charge_amount'])) {
    $Total = $_POST['charge_amount'];
} else {
    // get order total so we know what to charge
    $Total = $_SESSION['Orders'][$_SESSION['CurrentOrderIndex']]->Total;
    if(!$_SESSION['Orders'][$_SESSION['CurrentOrderIndex']]->TaxExempt){
        $Total += ($_SESSION['Orders'][$_SESSION['CurrentOrderIndex']]->Tax);
    }
}

// 2 decimal places
$Total = number_format($Total, 2, '.', '');

// are there any existing partial payments?
$RemainingTotal = $Total;
if (isset($_SESSION['Orders'][$_SESSION['CurrentOrderIndex']]->SplitPayments[0]['PaymentMethod'])) { 
    while(list ($key, $val) = each ($_SESSION['Orders'][$_SESSION['CurrentOrderIndex']]->SplitPayments)) {
        $RemainingTotal -= $_SESSION['Orders'][$_SESSION['CurrentOrderIndex']]->SplitPayments[$key]['PaymentAmount'];
    }
}

// if transaction has been approved, process the order, otherwise, show the form.
if(isset($transaction_response[1]) && ($transaction_response[0] == 1)) { 
    $_SESSION['Orders'][$_SESSION['CurrentOrderIndex']]->PaymentMethod = 'Authorize.Net AIM';
	$Onload = "ProcessOrder();";
} else {
	$Onload = "document.cc_process_form.ccnumber.focus();";
}

      if ($_SESSION['Orders'][$_SESSION['CurrentOrderIndex']]->ReturnOrder) {
        $Q_TransactionInfo = mysql_query("SELECT cc_number, cc_expires, transaction_details FROM " . ORDERS . " WHERE orders_id = '" . $_SESSION['Orders'][$_SESSION['CurrentOrderIndex']]->ReturnOrder . "'");
        if(mysql_num_rows($Q_TransactionInfo) == 1) {
            $R_TransactionInfo = mysql_fetch_array($Q_TransactionInfo);
            
            $trans_id_step1 = substr($R_TransactionInfo['transaction_details'], 15);
            $trans_id_step2 = explode(';', $trans_id_step1);
            $trans_id = $trans_id_step2[0];
            $ccnum = substr($R_TransactionInfo['cc_number'],-4);
            $ccexp = $R_TransactionInfo['cc_expires'];
        } else {
            $ccnum = $_POST['ccnumber'];
            $ccexp = $_POST['ccexp'];
        }
        
        
      } 
 
      if ($RemainingTotal < 0) $transaction_label = RETURN_AMOUNT;
      else $transaction_label = CHARGE_AMOUNT;
 
?>

<html>
<head>
<?php if ($_SESSION['Orders'][$_SESSION['CurrentOrderIndex']]->ReturnOrder == 0) { ?>
    <script type="text/javascript" src="includes/cc_formcheck.js"></script>
<?php } ?>
<script language="javascript" type="text/javascript">
function ProcessOrder(){
    window.opener.location.href='action.php?Action=ProcessOrder&payment_method=<?php echo($_REQUEST['payment_method']); ?>';
    window.close();
}

function setCreditCardAttributes(string) {
    string = string.replace('%B', '');
    string = string.replace('%b', '');

    var arr = string.split('^');
    
    var nameArr = arr[1].split('/');
    var len = nameArr.length;
    
    var month = arr[2].substring(2, 4);
    var year = arr[2].substring(0, 2);
    this.first_name = '';
    this.last_name = '';

    nameArr = arr[1].split('/');
    
    document.cc_process_form.firstname.value = nameArr[1];
    document.cc_process_form.lastname.value = nameArr[0];
    document.cc_process_form.ccexp.value = month + year
    document.cc_process_form.ccnumber.value = arr[0];
}

function returnKey(evt)
{
	var evt  = (evt) ? evt : ((event) ? event : null);

	if (evt.keyCode == 13) { 
        setCreditCardAttributes(document.cc_process_form.ccnumber.value);
	}
}
document.onkeypress = returnKey;

</script>

<link rel="Stylesheet" href="css/style.css">
<style type="text/css">
body {margin:20; padding:20;}
</style>
<title><?php echo TITLE; ?></title>
</head>

<body onload="<?php echo($Onload); ?>">
<h3><?php echo TITLE; ?></h3>

<?php if ($order_error) { ?>
    <script language="JavaScript">
        <!-- hide from older browser
        alert("<?php echo $order_error; ?>")
        //-->
    </script> 
<?php } 

if (isset($results)) {
    echo $results;
    echo('<br><br><a class="button" title="' . RETURN_TO_FORM_BUTTON_TITLE . '" href="#" onclick="window.location.href=\'authorizenet_aim.php\'"><span>' . RETURN_TO_FORM . '</span></a>');
} else {
?>

<table><tr><td>
    <form action="<?php echo htmlentities($_SERVER['PHP_SELF']); ?>" name="cc_process_form" onsubmit="return validate_form(this);" method="post">
        <table>
            <tr><td class="form_required"><?php echo $transaction_label; ?></td><td><input type="text" name="charge_amount" value="<?php echo number_format(abs($RemainingTotal), 2, '.', ''); ?>" readonly>
            </td></tr>
            <tr><td class="form_required"><?php echo CC_NUMBER; ?></td>         <td><input type="text" name="ccnumber" value="<?php echo $ccnum; ?>"></td></tr>
            <tr><td class="form_required"><?php echo EXPIRES_MMYY; ?></td>    <td><input type="text" name="ccexp" value="<?php echo $ccexp; ?>"></td></tr>
            <tr><td><?php echo CVV_CODE; ?></td>          <td><input type="text" name="cvv" value="<?php echo $_POST['cvv']; ?>"></td></tr>
            <tr><td class="form_required"><?php echo FIRST_NAME; ?></td>        <td><input type="text" name="firstname" value="<?php echo $R_Customer_Billing['entry_firstname']; ?>"></td></tr>
            <tr><td class="form_required"><?php echo LAST_NAME; ?></td>         <td><input type="text" name="lastname" value="<?php echo $R_Customer_Billing['entry_lastname']; ?>"></td></tr>
            <tr><td><?php echo PHONE; ?></td>         <td><input type="text" name="telephone" value="<?php echo $R_Customer['customers_telephone']; ?>"></td></tr>
            <tr><td><?php echo COMPANY; ?></td>           <td><input type="text" name="company" value="<?php echo $R_Customer['entry_company']; ?>"></td></tr>
            <tr><td><?php echo STREET_ADDR; ?></td>       <td><input type="text" name="street_address" value="<?php echo $R_Customer_Billing['entry_street_address']; ?>"></td></tr>
            <tr><td><?php echo CITY; ?></td>              <td><input type="text" name="city" value="<?php echo $R_Customer_Billing['entry_city']; ?>"></td></tr>
            <tr><td><?php echo STATE; ?></td>             <td><input type="text" name="state" value="<?php echo $R_Customer_Billing['zone_name']; ?>"</td></tr>
            <tr><td><?php echo POST_CODE; ?></td>          <td><input type="text" name="postcode" value="<?php echo $R_Customer_Billing['entry_postcode']; ?>"></td></tr>
            <tr><td><?php echo COUNTRY; ?></td>           <td><input type="text" name="country" value="<?php echo $R_Customer_Billing['countries_name']; ?>"></td></tr>
            <!--
            <tr><td class="required">Transaction Type:</td>  <td align="right">Capture<input type="radio" name="authtype" value="sale" checked></td></tr>
            <tr><td>&nbsp;</td>             <td align="right">Authorize<input type="radio" name="authtype" value="auth"></td></tr>
            -->
<?php        
        // valid transaction types:
        // AUTH_CAPTURE (default), AUTH_ONLY, CAPTURE_ONLY, CREDIT, PRIOR_AUTH_CAPTURE, VOID 

        if ($RemainingTotal < 0)  {
            // total is negative -- could be a linked return order, or just an unlinked return/exchange order.
            $trans_type = 'CREDIT';
        } else {
            // this is a "normal" order, perform a sale transaction
            $trans_type = 'AUTH_CAPTURE';
        }
?>
            <input type="hidden" name="authtype" value="<?php echo $trans_type; ?>">
            <input type="hidden" name="process" value="1">
            <tr><td colspan="2" align="center"><?php echo FIELDS_REQUIRED; ?></td></tr>
            <tr><td colspan="2">&nbsp;</td></tr>
            <tr><td align="left">&nbsp;&nbsp;&nbsp;
            <!--<a class="button" title="<?php echo PROCESS_ORDER_BUTTON_TITLE; ?>" href="#" onclick="this.blur(); document.cc_process_form.submit();"><span><?php echo PROCESS_ORDER; ?></span></a>-->
            <input type="image" value="Submit" src="images/submit.jpg">
                </td>
                <td align="right"><a class="button" title="<?php echo CANCEL_BUTTON_TITLE; ?>" href="#" onclick="this.blur(); window.close();"><span><?php echo CANCEL; ?></span></a></td></tr>
        </table>
    </form>
<?php
    if ($_SESSION['Orders'][$_SESSION['CurrentOrderIndex']]->ReturnOrder) { 
?>
    <tr><td align="center"><?php echo ORIG_TRANS_ID . ':  ' . $trans_id;?></td></tr>
<?php }
?>
</td></tr></table>

</body></html>
<?php
    } // end else 
?>
