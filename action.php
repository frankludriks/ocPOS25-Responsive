<?php
// action.php


include("includes/db.php");
include("includes/functions.php");
include("includes/session.php");

if($_REQUEST['Action']=="SelectOrder" && $_REQUEST['OrderID']) {
	header("Location: order.php?OrderID=" . $_REQUEST['OrderID'] . "&PopReceipt=true");
}

if($_REQUEST['Action']=="SelectOrder" && $_REQUEST['OrderIndex'] > -1 && $_REQUEST['OrderIndex']!="xxx") {
	if(substr($_REQUEST['OrderIndex'],0,2)=="a_") {
		$_REQUEST['OrderIndex'] = str_replace("a_","",$_REQUEST['OrderIndex']);
		GetArchivedOrder($_REQUEST['OrderIndex']);
	}else{
		$_SESSION['CurrentOrderIndex'] = $_REQUEST['OrderIndex'];
	}
}

if($_REQUEST['Action']=="ArchiveOrder") {
	$_SESSION['Orders'][$_SESSION['CurrentOrderIndex']]->Archive();
	RemoveOrder();
}

if($_REQUEST['Action']=="NewOrder") {
	NewOrder($_REQUEST['CustomerID']);
}

if($_REQUEST['Action']=="ItemNewOrder") {
	ItemNewOrder($_REQUEST['CustomerID'], $_REQUEST['ProductID']);
}

if($_REQUEST['Action']=="AssignCustomer" && $_REQUEST['CustomerID']) {
	$_SESSION['Orders'][$_SESSION['CurrentOrderIndex']]->AssignCustomer($_REQUEST['CustomerID']);
}

if($_REQUEST['Action']=="RemoveCustomer") {
	$_SESSION['Orders'][$_SESSION['CurrentOrderIndex']]->CustomerID = NULL;
}

if($_REQUEST['Action']=="RemoveOrder") {
	RemoveOrder();
}


if($_REQUEST['Action']=="ReturnOrder") {
   NewOrder($_REQUEST['CustomerID']);
	$_SESSION['Orders'][$_SESSION['CurrentOrderIndex']]->ReturnOrder = 1;
}

if($_REQUEST['Action']=="ProcessOrder") {
    
	if ($_GET['payment_method']=='Cash') {
		$Total = $_SESSION['Orders'][$_SESSION['CurrentOrderIndex']]->Total;
		if(!$_SESSION['Orders'][$_SESSION['CurrentOrderIndex']]->TaxExempt) {
			$Total += ($_SESSION['Orders'][$_SESSION['CurrentOrderIndex']]->Tax);
		}
        
        // is this a partial payment (less than order total)
		if ( ( ($Total - ($_SESSION['Orders'][$_SESSION['CurrentOrderIndex']]->Cash)) >= 0.0045)   // not enough money - using 0.0045 to overcome math rounding problem
			|| (!is_numeric($_SESSION['Orders'][$_SESSION['CurrentOrderIndex']]->Cash)) ) {
            //header("Location: checkout.php?Error=Invalid%20Cash%20Amount");
            
            // are there any existing partial payments?
            $RemainingTotal = $Total;
            if (isset($_SESSION['Orders'][$_SESSION['CurrentOrderIndex']]->SplitPayments[0]['PaymentMethod'])) { 
                while(list ($key, $val) = each ($_SESSION['Orders'][$_SESSION['CurrentOrderIndex']]->SplitPayments)) {
                    $RemainingTotal -= $_SESSION['Orders'][$_SESSION['CurrentOrderIndex']]->SplitPayments[$key]['PaymentAmount'];
                }
            }

            // if partial payment is insufficient, apply new partial payment
            if ($RemainingTotal > $_SESSION['Orders'][$_SESSION['CurrentOrderIndex']]->Cash) {
                $_SESSION['Orders'][$_SESSION['CurrentOrderIndex']]->ApplySplitPayment('Cash', $_SESSION['Orders'][$_SESSION['CurrentOrderIndex']]->Cash);
                header("Location: checkout.php?split=cash&amount=" . $_SESSION['Orders'][$_SESSION['CurrentOrderIndex']]->Cash);
                exit();
            }
			
		}
		$CashChange = $_SESSION['Orders'][$_SESSION['CurrentOrderIndex']]->Cash - $Total;
		$Change = number_format($CashChange,2, '.', '');
        
	}

	if ($_GET['payment_method']=='Check') {
		$Total = $_SESSION['Orders'][$_SESSION['CurrentOrderIndex']]->Total;
		if(!$_SESSION['Orders'][$_SESSION['CurrentOrderIndex']]->TaxExempt) {
			$Total += ($_SESSION['Orders'][$_SESSION['CurrentOrderIndex']]->Tax);
		}
        
        // is this a partial payment (less than order total)
		if ( ( ($Total - ($_SESSION['Orders'][$_SESSION['CurrentOrderIndex']]->Check)) >= 0.0045)   // not enough money - using 0.0045 to overcome math rounding problem
			|| (!is_numeric($_SESSION['Orders'][$_SESSION['CurrentOrderIndex']]->Check)) ) {
				//header("Location: checkout.php?Error=Invalid%20Check%20Amount");

            // are there any existing partial payments?
            $RemainingTotal = $Total;
            if (isset($_SESSION['Orders'][$_SESSION['CurrentOrderIndex']]->SplitPayments[0]['PaymentMethod'])) { 
                while(list ($key, $val) = each ($_SESSION['Orders'][$_SESSION['CurrentOrderIndex']]->SplitPayments)) {
                    $RemainingTotal -= $_SESSION['Orders'][$_SESSION['CurrentOrderIndex']]->SplitPayments[$key]['PaymentAmount'];
                }
            }

            // if partial payment is insufficient, apply new partial payment
            if ($RemainingTotal > $_SESSION['Orders'][$_SESSION['CurrentOrderIndex']]->Check) {
                $_SESSION['Orders'][$_SESSION['CurrentOrderIndex']]->ApplySplitPayment('Check', $_SESSION['Orders'][$_SESSION['CurrentOrderIndex']]->Check);
                header("Location: checkout.php?split=check&amount=" . $_SESSION['Orders'][$_SESSION['CurrentOrderIndex']]->Check);
			exit();
		}
        }
        
		$CheckChange = $_SESSION['Orders'][$_SESSION['CurrentOrderIndex']]->Check - $Total;
		$Change = number_format($CheckChange,2, '.', '');
        
    }
	
	if ($_GET['payment_method']=='CreditCard') {
		$Total = $_SESSION['Orders'][$_SESSION['CurrentOrderIndex']]->Total;
		if(!$_SESSION['Orders'][$_SESSION['CurrentOrderIndex']]->TaxExempt) {
			$Total += ($_SESSION['Orders'][$_SESSION['CurrentOrderIndex']]->Tax);
	}
		if (!is_numeric($_GET['ccnum'])) { // check for numeric last 4 digits of cc number
				header("Location: checkout.php?Error=Invalid%20Credit%20Card%20Number");
			exit();
		}
	}
	
	if ($_GET['payment_method']=='Authorize.Net AIM') {
/* we do this stuff in the Authorize.Net module
		$Total = $_SESSION['Orders'][$_SESSION['CurrentOrderIndex']]->Total;
		if(!$_SESSION['Orders'][$_SESSION['CurrentOrderIndex']]->TaxExempt){
			$Total += ($_SESSION['Orders'][$_SESSION['CurrentOrderIndex']]->Tax);
        }   
*/
	}
	
	$_SESSION['Orders'][$_SESSION['CurrentOrderIndex']]->Process($session);
	$OrderID = $_SESSION['Orders'][$_SESSION['CurrentOrderIndex']]->OrderID;
	RemoveOrder();
	if ($_GET['payment_method']=='CreditCard') { // if credit card, they don't need any change
		header("Location: order.php?OrderID=$OrderID&PopReceipt=True");
		exit();
	}
	header("Location: order.php?OrderID=$OrderID&PopReceipt=True&Change=$Change");
	exit();

}

if($_REQUEST['Action']=="TaxExempt") {
	$_SESSION['Orders'][$_SESSION['CurrentOrderIndex']]->TaxExempt();
}


if($_REQUEST['Action']=="AddItem" && $_REQUEST['ProductQuery'] && $_REQUEST['Quantity']) {
	$Q_Product_query = ("SELECT p.products_id FROM
		" . PRODUCTS . " p, " . PRODUCTS_DESCRIPTION . " pd WHERE
		(p.products_model LIKE '%" . $_REQUEST['ProductQuery'] . "%' OR
		pd.products_name LIKE '%" . $_REQUEST['ProductQuery'] . "%') AND
		pd.products_id = p.products_id");
      
   if (ALLOW_SOLDOUT_PRODUCTS == '0') {
      $Q_Product_query .= " AND p.products_quantity > 0 ";
   }
   
   $Q_Product_query .= " GROUP BY p.products_id";
   $Q_Product = mysql_query($Q_Product_query);

	if(mysql_num_rows($Q_Product)==1) {
		$R_Product = mysql_fetch_assoc($Q_Product);
		$_SESSION['Orders'][$_SESSION['CurrentOrderIndex']]->AddItem($R_Product['products_id'], $_REQUEST['Quantity'], IN_STORE_PRICING);
	}else{
		header("Location: product_search.php?Query=" . $_REQUEST['ProductQuery'] . "");
		exit();
	}
}

if($_REQUEST['Action']=="AddItem" && $_REQUEST['ProductID'] && $_REQUEST['Quantity']) {
	$Q_Product_query = ("SELECT products_id FROM " . PRODUCTS . " WHERE products_id = '" . $_REQUEST['ProductID'] . "'");
   if (ALLOW_SOLDOUT_PRODUCTS == '0') {
      $Q_Product_query .= " AND products_quantity > 0 ";
   }
   $Q_Product = mysql_query($Q_Product_query);
   
	if(mysql_num_rows($Q_Product)==1) {
		$_SESSION['Orders'][$_SESSION['CurrentOrderIndex']]->AddItem($_REQUEST['ProductID'], $_REQUEST['Quantity'], IN_STORE_PRICING, $_REQUEST['product_stock_attributes']);
	}elseif($_REQUEST['ProductID'] > 1000000000) {
		$_SESSION['Orders'][$_SESSION['CurrentOrderIndex']]->AddItem($_REQUEST['ProductID'], $_REQUEST['Quantity'], IN_STORE_PRICING, $_REQUEST['product_stock_attributes']);
	}
}

if($_REQUEST['Action']=="RemoveItem" && $_REQUEST['ProductID'] && $_REQUEST['Quantity']) {
	$_SESSION['Orders'][$_SESSION['CurrentOrderIndex']]->RemoveItem($_REQUEST['ProductID'], $_REQUEST['Quantity']);
}

if($_REQUEST['Action']=="AddCartItem" && $_REQUEST['Quantity']) {
	$_SESSION['Orders'][$_SESSION['CurrentOrderIndex']]->AddCartItem($_REQUEST['Index'], $_REQUEST['Quantity']);
}

if($_REQUEST['Action']=="RemoveItem" && $_REQUEST['Quantity']) {
	$_SESSION['Orders'][$_SESSION['CurrentOrderIndex']]->RemoveItem($_REQUEST['Index'], $_REQUEST['Quantity']);
}

if($_REQUEST['Action']=="DeleteItem") {
	$_SESSION['Orders'][$_SESSION['CurrentOrderIndex']]->DeleteItem($_REQUEST['Index']);
}

header("Location: index.php?PHPSESSID=".session_id());

?>
