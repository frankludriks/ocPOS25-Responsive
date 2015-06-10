<?php 
// includes/functions_header.php
// error level defined in include/functions.php

function show_archived_orders_dropdown () {
    $Q_OrderList_sql = ("SELECT * FROM " . POS_ORDERS . " ORDER BY post_time DESC");
    $Q_OrderList_query = oc_query($Q_OrderList_sql, 'SQL Error. Archive orders lookup failure.');
    
    while($R_OrderList = mysql_fetch_assoc($Q_OrderList_query)) {
        $R_OrderList['customers_name'] = DEFAULT_CUSTOMER_FIRST_NAME . ' ' . DEFAULT_CUSTOMER_LAST_NAME;
        if($R_OrderList['customers_id']) {
            $Q_Customer = mysql_query("SELECT customers_firstname,customers_lastname FROM " . CUSTOMERS . " WHERE customers_id='" . $R_OrderList['customers_id'] . "'");
            if($R_Customer_header = mysql_fetch_decode_assoc($Q_Customer)) {
                $R_OrderList['customers_name'] = $R_Customer_header['customers_firstname'] . ' ' . $R_Customer_header['customers_lastname'];
            }
        }
        echo("<option value=\"a_" . $R_OrderList['pos_orders_id'] . "\">" . date("m-d H:i", $R_OrderList['post_time']) . " - " . $default_currency_symbol . number_format($R_OrderList['total'], 2, '.', '') . " - " . $R_OrderList['customers_name'] . "</option>\n");   
        $NumberOrders++;
    }
}

function show_pending_orders_dropdown() {
   $NumberOrders = 0;
   for($i=0;$i<$_SESSION['NextOrderIndex'];$i++){
   		if(is_object($_SESSION['Orders'][$i])) {
			if($_SESSION['CurrentOrderIndex'] == $i) {
				echo("<option value=\"$i\" selected>");
			}else{
				echo("<option value=\"$i\">");
			}
			$_SESSION['Orders'][$i]->PrintHeader();
			echo("</option>\n");
			$NumberOrders++;
		}
   }
}

function show_recent_orders_dropdown() {
    $Q_OrderList_sql = ("SELECT o.orders_id,date_purchased,ot.value
                                FROM " . ORDERS . " o, " . ORDERS_TOTAL . " ot
                                WHERE o.in_store_purchase = '1' AND
                                ot.orders_id = o.orders_id AND
                                ot.class = 'ot_total' ORDER BY date_purchased DESC LIMIT 25");

    $Q_OrderList_query = oc_query($Q_OrderList_sql, 'SQL Error. Recent orders lookup failure.');
    
    if (mysql_num_rows($Q_OrderList_query) > 0) {
        while($R_OrderList = mysql_fetch_assoc($Q_OrderList_query)) {
            if(!isset($R_OrderList['customers_name'])){
                //  shouldn't this have been set when the order was placed?  Why force a default customername display here?
                $R_OrderList['customers_name'] = DEFAULT_CUSTOMER_FIRST_NAME . ' ' . DEFAULT_CUSTOMER_LAST_NAME;
            }
            
            $DateExp = explode(" ",$R_OrderList['date_purchased']);
            $TimeExp = explode(":",$DateExp[1]);
            $DateExp = explode("-",$DateExp[0]);
            $DateString = "$DateExp[1]-$DateExp[2] $TimeExp[0]:$TimeExp[1]";
            $request_order_id = 0;
            
            if ( isset($_REQUEST['OrderID']) ) {
                $request_order_id = $_REQUEST['OrderID']; 
            }
            // echo $request_order_id; die();
            
            if ($R_OrderList['orders_id'] == $request_order_id) {
                echo("<option value=\"" . $R_OrderList['orders_id'] . "\" selected>" . $DateString . " - " . $default_currency_symbol . number_format($R_OrderList['value'], 2, '.', '') . "</option>\n");
            } else {
                echo("<option value=\"" . $R_OrderList['orders_id'] . "\">" . $DateString . " - " . $default_currency_symbol . number_format($R_OrderList['value'], 2, '.', '') . "</option>\n");
            }
          
        }
    }
}

?>