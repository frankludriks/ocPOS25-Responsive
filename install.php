<?php
// install.php


// NOTE:  After successful installation, delete or rename this file

include("includes/db.php");
//$DATABASE = mysql_connect(DB_SERVER, DB_USER, DB_PASSWORD) or die(mysql_error());
//mysql_select_db(DB_NAME, $DATABASE) or die(mysql_error());

include("includes/functions_values.php");
    
if (file_exists("includes/lang/$lang/common.php")) {
	include("includes/lang/$lang/common.php");
}

if (file_exists("includes/lang/$lang/install.php")) {
	include("includes/lang/$lang/install.php");
}

if (file_exists("includes/custom_header.php")) {
	include("includes/custom_header.php");
}


function install() {
    global $DATABASE;
    $is_iis = false;
    $is_apache = false;
    $message = '';
    $error = 0;

    if (!preg_match("/apache/i", $_SERVER['SERVER_SOFTWARE'])) { // not running Apache, check for IIS
        if (preg_match("/iis/i", $_SERVER['SERVER_SOFTWARE'])) {
            $is_iis = true;
        } elseif (preg_match("/microsoft/i", $_SERVER['SERVER_SOFTWARE'])) {
            $is_iis = true;
        }
    } else {
        $is_apache = true;
    }

    if (!$is_apache) {
        if ($is_iis) {
            $message .= '<br /> <br />' . THIS_IS_IIS;
        } else {
            $message .= '<br /> <br />' . NOT_APACHE;
        }
    } 

    $test_table = $table_prefix . 'customers';

    if(mysql_num_rows(mysql_query("SHOW TABLES LIKE '" . $test_table . "'")) == 0) {
        $message .= DATABASE_STRUCTURE_FAILURE;
        $failed = true;
    }

    if (!isset($failed)) {


        // Add orders_products.products_prid if it doesn't exist.  This is for compatability with ZenCart.
        $prid_query = mysql_query("show columns from " . ORDERS_PRODUCTS . " ");

        $i = 0;
        $fields=array();
        while ($row = mysql_fetch_assoc($prid_query) ) {
           $fields[$i] = ($row['Field']);
           $i++;
        }
        if (!in_array("products_prid", $fields)) {  // no orders_products.products_prid column found.  Create it.
           $query_result = mysql_query("ALTER TABLE " . ORDERS_PRODUCTS . " ADD COLUMN products_prid TINYTEXT NOT NULL", $DATABASE );
           if ($query_result) { // query success
                $message .= '<span class="success">' . ADDED . '</span> products_prid ' . TO . ' ' . $table_prefix . 'orders_products ' . TABLE . '.<br />';
           } else {
                $message .= '<span class="error">' . FAILED_TO_ADD . '</span> products_prid ' . TO . ' ' . $table_prefix . 'orders_products ' . TABLE . '.<br />';
                $message .= mysql_errno($DATABASE) . ERROR . mysql_error($DATABASE) . "<br /><br />";
                $error++;
           }        
        } else {
           $message .= $table_prefix . 'orders_products.products_prid <b>' . ALREADY_EXISTS . '</b>.<br />';
        }

    // QT PRO
         if (!in_array("products_stock_attributes", $fields)) {  // no orders_products.products_stock_attributes column found.  Create it.
            $query_result = mysql_query("ALTER TABLE " . ORDERS_PRODUCTS . " ADD COLUMN products_stock_attributes varchar(255) DEFAULT NULL", $DATABASE );
            if ($query_result) { // query success
                 echo('<span class="success">' . ADDED . '</span> products_stock_attributes ' . TO . ' ' . $table_prefix . 'orders_products ' . TABLE . '.<br />');
            } else {
                 echo('<span class="error">' . FAILED_TO_ADD . '</span> products_stock_attributes ' . TO . ' ' . $table_prefix . 'orders_products ' . TABLE . '.<br />');
                 echo mysql_errno($DATABASE) . ": " . mysql_error($DATABASE) . "<br /><br />";
            }
         } else {
            echo($table_prefix . 'orders_products.products_stock_attributes <b>' . ALREADY_EXISTS . '</b>.<br />');
         }
    // END QT PRO

        // Extend orders table if not already done.
        $orders_schema_query = mysql_query("show columns from " . ORDERS . " ");

        $i = 0;
        $fields=array();
        while ($row = mysql_fetch_assoc($orders_schema_query) ) {
           $fields[$i] = ($row['Field']);
           $i++;
        }
        if (!in_array("void", $fields)) {  // no orders.void column found.  Create it.
           $query_result = mysql_query("ALTER TABLE " . ORDERS . " ADD COLUMN void TINYINT(1) DEFAULT '0' NOT NULL", $DATABASE );   
           if ($query_result) { // query success
                $message .= '<span class="success">' . ADDED . '</span> void ' . TO . ' ' . $table_prefix . 'orders ' . TABLE . '.<br />';
           } else {
                $message .= '<span class="error">' . FAILED_TO_ADD . '</span> void ' . TO . ' ' . $table_prefix . 'orders ' . TABLE . '.<br />';
                $message .= mysql_errno($DATABASE) . ERROR . mysql_error($DATABASE) . "<br /><br />";
                $error++;
           }
        } else {
           $message .=$table_prefix . 'orders.void <b>' . ALREADY_EXISTS . '</b>.<br />';
        }
        
        if (!in_array("return_exchange", $fields)) {  // no orders.return_exchange column found.  Create it.
           $query_result = mysql_query("ALTER TABLE " . ORDERS . " ADD COLUMN return_exchange INT(11) DEFAULT '0' NOT NULL", $DATABASE );   
           if ($query_result) { // query success
                $message .= '<span class="success">' . ADDED . '</span> return_exchange ' . TO . ' ' . $table_prefix . 'orders ' . TABLE . '.<br />';
           } else {
                $message .= '<span class="error">' . FAILED_TO_ADD . '</span> return_exchange ' . TO . ' ' . $table_prefix . 'orders ' . TABLE . '.<br />';
                $message .= mysql_errno($DATABASE) . ERROR . mysql_error($DATABASE) . "<br /><br />";
                $error++;
           }
        } else {
           $message .= $table_prefix . 'orders.return_exchange <b>' . ALREADY_EXISTS . '</b>.<br />';
        }

        if (!in_array("in_store_purchase", $fields)) {  // no orders.in_store_purchase column found.  Create it.
           $query_result = mysql_query("ALTER TABLE " . ORDERS . " ADD COLUMN in_store_purchase TINYINT(1) DEFAULT '0' NOT NULL", $DATABASE );   
           if ($query_result) { // query success
                $message .= '<span class="success">' . ADDED . '</span> in_store_purchase ' . TO . ' ' . $table_prefix . 'orders ' . TABLE . '.<br />';
           } else {
                $message .= '<span class="error">' . FAILED_TO_ADD . '</span> in_store_purchase ' . TO . ' ' . $table_prefix . 'orders ' . TABLE . '.<br />';
                $message .= mysql_errno($DATABASE) . ERROR . mysql_error($DATABASE) . "<br /><br />";
                $error++;
           }
        } else {
           $message .= $table_prefix . 'orders.in_store_purchase <b>' . ALREADY_EXISTS . '</b>.<br />' ;
        }

        if (!in_array("pos_username", $fields)) {  // no orders.void column found.  Create it.
           $query_result = mysql_query("ALTER TABLE " . ORDERS . " ADD COLUMN pos_username TEXT AFTER in_store_purchase ", $DATABASE );   
           if ($query_result) { // query success
                $message .= '<span class="success">' . ADDED . '</span> pos_username ' . TO . ' ' . $table_prefix . 'orders ' . TABLE . '.<br />';
           } else {
                $message .= '<span class="error">' . FAILED_TO_ADD . '</span> pos_username ' . TO . ' ' . $table_prefix . 'orders ' . TABLE . '.<br />';
                $message .= mysql_errno($DATABASE) . ERROR . mysql_error($DATABASE) . "<br /><br />";
                $error++;
           }
        } else {
           $message .= $table_prefix . 'orders.pos_username <b>' . ALREADY_EXISTS . '</b>.<br />' ;
        }

        if (!in_array("transaction_details", $fields)) {  // no orders.transaction_details column found.  Create it.
           $query_result = mysql_query("ALTER TABLE " . ORDERS . " ADD COLUMN transaction_details TEXT AFTER cc_expires ", $DATABASE );   
           if ($query_result) { // query success
                $message .= '<span class="success">' . ADDED . '</span> transaction_details ' . TO . ' ' . $table_prefix . 'orders ' . TABLE . '.<br />';
           } else {
                $message .= '<span class="error">' . FAILED_TO_ADD . '</span> transaction_details ' . TO . ' ' . $table_prefix . 'orders ' . TABLE . '.<br />';
                $message .= mysql_errno($DATABASE) . ERROR . mysql_error($DATABASE) . "<br /><br />";
                $error++;
           }
        } else {
           $message .= $table_prefix . 'orders.transaction_details <b>' . ALREADY_EXISTS . '</b>.<br />';
        }

        // Extend orders_total table if not already done.
        $orders_total_schema_query = mysql_query("show columns from " . ORDERS_TOTAL . " ");

        $i = 0;
        $fields=array();
        while ($row = mysql_fetch_assoc($orders_total_schema_query) ) {
           $fields[$i] = ($row['Field']);
           $i++;
        }

        if (!in_array("cash", $fields)) {  // no orders.void column found.  Create it.
           $query_result = mysql_query("ALTER TABLE " . ORDERS_TOTAL . " ADD COLUMN cash DECIMAL (15,2) DEFAULT '0' NOT NULL AFTER value", $DATABASE );
           if ($query_result) { // query success
                $message .= '<span class="success">' . ADDED . '</span> cash ' . TO . ' ' . $table_prefix . 'orders_total ' . TABLE . '.<br />';
           } else {
                $message .= '<span class="error">' . FAILED_TO_ADD . '</span> cash ' . TO . ' ' . $table_prefix . 'orders_total ' . TABLE . '.<br />';
                $message .= mysql_errno($DATABASE) . ERROR . mysql_error($DATABASE) . "<br /><br />";
                $error++;
           }
        } else {
           $message .= $table_prefix . 'orders_total.cash <b>' . ALREADY_EXISTS . '</b>.<br />';
        }

        if(mysql_num_rows(mysql_query("SHOW TABLES LIKE '" . $table1 . "'")) == 1) $pos_orders_exist = 1;

        // create new ocPOS tables if needed
        $pos_orders_exist = 0;
        $pos_orders_products_exist = 0;
        $pos_orders_products_attributes_exist = 0;
        $pos_users_exist = 0;
        $pos_users_active_exist = 0;

        $table1 = POS_ORDERS;
        $table2 = POS_ORDERS_PRODUCTS;
        $table3 = POS_ORDERS_PRODUCTS_ATTRIBUTES;
        $table4 = POS_USERS;
        $table5 = POS_USERS_ACTIVE;

        if(mysql_num_rows(mysql_query("SHOW TABLES LIKE '" . $table1 . "'")) == 1) $pos_orders_exist = 1;
        if(mysql_num_rows(mysql_query("SHOW TABLES LIKE '" . $table2 . "'")) == 1) $pos_orders_products_exist = 1;
        if(mysql_num_rows(mysql_query("SHOW TABLES LIKE '" . $table3 . "'")) == 1) $pos_orders_products_attributes_exist = 1;
        if(mysql_num_rows(mysql_query("SHOW TABLES LIKE '" . $table4 . "'")) == 1) $pos_users_exist = 1;
        if(mysql_num_rows(mysql_query("SHOW TABLES LIKE '" . $table5 . "'")) == 1) $pos_users_active_exist = 1;
        
        if ($pos_orders_exist == '0') {  // Table does not exist.  Create it.
           $query_result = mysql_query("CREATE TABLE " . $table1 . " (
             pos_orders_id int(11) NOT NULL auto_increment,
             customers_id int(11) default NULL,
             comments text,
             total decimal(15,4) NOT NULL default '0.00',
             tax_exempt tinyint(1) NOT NULL default '0',
             post_time int(10) default NULL,
             PRIMARY KEY  (pos_orders_id)
           ) ;");
           if ($query_result) { // query success
                $message .= '<span class="success">' . CREATED . '</span> ' . $table1 . ' ' . TABLE . '.<br />';
           } else {
                $message .= '<span class="error">' . FAILED_TO_CREATE . '</span> ' . $table1 . ' ' . TABLE . '.<br />';
                $message .= mysql_errno($DATABASE) . ERROR . mysql_error($DATABASE) . "<br /><br />";
                $error++;
           }
        } else {
           $message .= $table1 . ' <b>' . ALREADY_EXISTS . '</b>. <br />';
        }

        if ($pos_orders_products_exist == '0') {  // Table does not exist.  Create it.
           $query_result = mysql_query("CREATE TABLE " . $table2 . " (
             pos_orders_id int(11) default NULL,
             products_id int(11) default NULL,
             products_model varchar(12) default NULL,
             products_name varchar(128) default NULL,
             products_price decimal(15,4) NOT NULL default '0.00',
             tax_rate decimal(15,4) NOT NULL default '0.05',
             price_overide tinyint(1) NOT NULL default '0',
             non_inventory tinyint(1) NOT NULL default '0',
             products_quantity int(10) NOT NULL default '1',
             products_stock_attributes varchar(255) DEFAULT NULL,
             KEY pos_orders_id (pos_orders_id,products_id)
           ) ;");
           if ($query_result) { 
                $message .='<span class="success">' . CREATED . '</span> ' . $table2 . ' ' . TABLE . '.<br />';
           } else {
                $message .= '<span class="error">' . FAILED_TO_CREATE . '</span> ' . $table2 . ' ' . TABLE . '.<br />';
                $message .= mysql_errno($DATABASE) . ERROR . mysql_error($DATABASE) . "<br /><br />";
                $error++;
           }
        } else {
           $message .= $table2 . ' <b>' . ALREADY_EXISTS . '</b>. <br />';
        }

        if ($pos_orders_products_attributes_exist == '0') {  // Table does not exist.  Create it.
           $query_result = mysql_query("CREATE TABLE " . $table3 . " (
            pos_orders_id int(11) unsigned NOT NULL default '0',
            products_id int(11) unsigned NOT NULL default '0',
            products_options varchar(32) NOT NULL default '',
            products_options_values varchar(32) NOT NULL default '',
            options_values_price decimal(15,4) NOT NULL default '0.0000',
            price_prefix char(1) NOT NULL default '',
            PRIMARY KEY  (pos_orders_id,products_id,products_options),
            KEY products_idx (products_id)
           ) ;");
           if ($query_result) { 
                $message .= '<span class="success">' . CREATED . '</span> ' . $table3 . ' ' . TABLE . '.<br />';
           } else {
                $message .= '<span class="error">' . FAILED_TO_CREATE . '</span> ' . $table3 . ' ' . TABLE . '.<br />';
                $message .= mysql_errno($DATABASE) . ERROR . mysql_error($DATABASE) . "<br /><br />";
                $error++;
           }
        } else {
           $message .= $table3 . ' <b>' . ALREADY_EXISTS . '</b>. <br />';
        }
        
        
        if ($pos_users_exist == '0') {  // Table does not exist.  Create it.
           $query_result1 = mysql_query("CREATE TABLE " . $table4 . " (
                username varchar(30) primary key,
                password varchar(32),
                userid varchar(32),
                userlevel tinyint(1) unsigned not null,
                email varchar(50),
                timestamp int(11) unsigned not null
                ) ;");
            $query_result2 = mysql_query("INSERT INTO " . POS_USERS . " (username, password, userid, userlevel, email, timestamp) VALUES ('admin', '21232f297a57a5a743894a0e4a801fc3', '0', 9, 'you@yourdomain.com', unix_timestamp())");
           if ($query_result1 && $query_result2) { 
                $message .= '<span class="success">' . CREATED . '</span> ' . $table4 . ' ' . TABLE . '.<br />';
                $message .= '&nbsp;&nbsp;<b>' . ADMIN_CREATED . '</b><br />';
           } else {
                $message .= '<span class="error">' . FAILED_TO_CREATE . '</span> ' . $table4 . ' ' . TABLE . '.<br />';
                $message .= mysql_errno($DATABASE) . ERROR . mysql_error($DATABASE) . "<br /><br />";
                $error++;
           }
        } else {
           $message .= $table4 . ' <b>' . ALREADY_EXISTS . '</b>. <br />';
        }
        
        
        if ($pos_users_active_exist == '0') {  // Table does not exist.  Create it.
           $query_result = mysql_query("CREATE TABLE " . $table5 . " (
            username varchar(30) primary key,
            timestamp int(11) unsigned not null
           ) ;");
           if ($query_result) { 
                $message .= '<span class="success">' . CREATED . '</span> ' . $table5 . ' ' . TABLE . '.<br />';
           } else {
                $message .= '<span class="error">' . FAILED_TO_CREATE . '</span> ' . $table5 . ' ' . TABLE . '.<br />';
                $message .= mysql_errno($DATABASE) . ERROR . mysql_error($DATABASE) . "<br /><br />";
                $error++;
           }
        } else {
           $message .= $table5 . ' <b>' . ALREADY_EXISTS . '</b>. <br />';
        }
        
        if (!$error) {
            $message .= '<br /><br /><b><span class="success"><font size="2">' . END_SUCCESS . '</font></span>';
            $message .= '&nbsp;&nbsp;&nbsp;<a class="button" title="' . CONT . '" href="install.php?install=3" onclick="this.blur();"><span>' . CONT . '</span></a>';
        } else {
            $message .= '<br /><br /><b><span class="error"><font size="2">' . END_ERRORS . '</font></span>';
        }
        
    } // end if (!isset($failed))
    return $message;
} // end function install()


if (isset($_GET['install'])) {
    switch(($_GET['install'])) {
        case 1:
            $message = 'Server: ' . $DB_Server . '<br>' . 
                'Database: ' . $DB_Name . '<br>' . 
                'DB User: ' . $DB_Username . '<br>' . 
                'DB Pass: xxxxxxxxxxxxxxx <br>' . 
                '<br>';
            if (!is_writable('graphs/bar_compare.png')) {
                $message .= GRAPHS_NOT_WRITEABLE;
                $message .= '&nbsp;&nbsp;&nbsp;<a class="button" title="' . RETRY . '" href="install.php?install=1" onclick="this.blur();"><span>' . RETRY . '</span></a><br><br>';
            }
            $message .= EXTEND_DB; 
            $message .= '&nbsp;&nbsp;&nbsp;<a class="button" title="' . CONT . '" href="install.php?install=2" onclick="this.blur();"><span>' . CONT . '</span></a>';
            break;
        case 2: 
            $message = install();
            break;
        case 3: 
            $message = FINISHED_INSTRUCTIONS . '<br /><br /><br /><a class="button" title="' . CONT . '" href="index.php" onclick="this.blur();"><span>' . CONT . '</span></a>';
            break;
        default: 
            break;
    }
} else {
    $message = WELCOME_TEXT . '&nbsp;&nbsp;&nbsp;';
    $message .= '<a class="button" title="' . CONT . '" href="install.php?install=1" onclick="this.blur();"><span>' . CONT . '</span></a>';
}

?>

<!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 4.01 Transitional//EN">
<html>
<head>
       <title><?php echo($POSName) . ': ' . TITLE; ?></title>
       <link rel="Stylesheet" href="css/style.css">
       <script language="JavaScript" src="javascript.js" type="text/javascript"></script>
</head>
<body>
<br />

<table width="80%" border="0" cellpadding="2" cellspacing="0" align="center">
 <tr>
  <td width="100%"><br>
    <?php echo $message; ?>
  </td>
</tr></table>

<?php include("includes/footer.php"); ?>

</body>
</html>

