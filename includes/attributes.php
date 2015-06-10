<?php
// Report all errors except E_NOTICE
// error level defined in include/functions.php
// error_reporting(E_ALL ^ E_NOTICE);


if (file_exists("includes/lang/$lang/includes/attributes.php")) {
	include("includes/lang/$lang/includes/attributes.php");
}

// -=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-
// ==  attributes class
// -=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-
class attributes {
  var $products_id;
  var $language_id;
  var $attribute_str;
  var $order_id;
  var $options = array();
  
  function attributes($products_id=0,$language_id=1, $attribute_str='',$order_id=0) {
    $this->products_id = $products_id;
    $this->language_id = $language_id;
    $this->attribute_str = $attribute_str;
    if (!isset($order_id) && isset($_GET['OrderID'])) $order_id = $_GET['OrderID'];
    $this->order_id = $order_id;
    $this->_load($order_id);
  }

  //  =-=-=-=-=-= Object setup functions =-=-=-=-=-=
  // load attribute data from database
  function _load($order_id=0) {
    if ($this->attribute_str != '') {
      $options = explode(",",$this->attribute_str);
      $curr_id = 0;
      foreach ($options as $opt_set) {
        list($key,$val) = explode("-",$opt_set);
        $data = mysql_fetch_assoc($this->q_get_option_info($key,$val));
        if ($data['products_options_id'] != $curr_id) {
          $curr_id = $data['products_options_id'];
          $this->options[$curr_id] = new attribute_option($data['products_options_id'],$data['products_options_name'],$data['products_id'],$data['products_options_track_stock']);
        }
        $this->options[$curr_id]->values[$data['products_options_values_id']] = new product_attribute_value(    
          $data['products_options_values_id'],
          $data['products_options_values_name'],
          $data['options_values_price'],
          $data['price_prefix']
        );        
      }
    } else {
      if ($this->products_id == 0) {
        $q = $this->q_get_all();
      } else {
        $q = $this->q_get_product_set($order_id);
      }
      if (($q != '') && (mysql_num_rows($q) > 0)) {
        $curr_id = 0;
        while ($data = mysql_fetch_assoc($q)) {
          if ($data['products_options_id'] != $curr_id) {
            $curr_id = $data['products_options_id'];
            $this->options[$curr_id] = new attribute_option($data['products_options_id'],$data['products_options_name'],$data['products_id'],$data['products_options_track_stock']);
          }
          if ($this->products_id == 0) {
            $this->options[$curr_id]->values[$data['products_options_values_id']] = new attribute_value(    
              $data['products_options_values_id'],
              $data['products_options_values_name']
            );
          } else {
            $this->options[$curr_id]->values[$data['products_options_values_id']] = new product_attribute_value(    
              $data['products_options_values_id'],
              $data['products_options_values_name'],
              $data['options_values_price'],
              $data['price_prefix']
            );        
          }
        }
      }
    }
  }
  
  // Adds a specific attribute configuration to this object's option set
  function add($opt_key, $val_key, $price, $prefix) {
    if (! isset($this->options[$opt_key])) {
      $defaults = $this->get_defaults($opt_key);
      $this->options[$opt_key] = new attribute_option($opt_key,$defaults['option_text'], $this->products_id);
    }
    $this->options[$opt_key]->store($val_key,$price,$prefix);
  }
  
  function restock() {
    print_r($item);exit();
  }
  
  // returns an array of attributes set for this product
  function add_item(){
    $results = array();   
    if ($this->attribute_str != '') {
      $option_set = explode(",",$this->attribute_str);
      $option_arr = array();
      foreach ($option_set as $opt_item) {
        list($key,$val) = explode("-",$opt_item);
        $option_arr[$key] = $val;        
      }
      foreach ($this->options as $opt_key=>$opt) {
        $results[$opt->option_text] = $opt->values[$option_arr[$opt_key]];
      }
    } else {
      foreach ($this->options as $opt_key=>$opt) {
        $results[$opt->option_text] = $opt->values[$_REQUEST["OPT_".$opt_key]];
      }
    }
    return $results;
  }
  
  // returns true if the given configuration is present
  function is_set($option_id,$value_id) {
    return ((isset($this->options[$option_id])) && $this->options[$option_id]->is_set($value_id));
  }
  
  //  =-=-=-=-=-= Object Form display functions =-=-=-=-=-=
  // The form used on the product page to configure a product for purchase
  //  -- This assumes the product attribute form of the values class is used
  function product_form($product_base_price) {
    $outstr = "\n<form name='product_options_form' action='action.php' method=POST>\n";
    $outstr .= "<input type=hidden name='product_base_price' value='".$product_base_price."'>\n";
    $outstr .= "<input type=hidden name='product_stock_attributes' value='".$_POST['product_stock_attributes']."'>\n";
    $attrib_url_string = explode('-', $_GET['attrib']);
    foreach ($this->options as $opt_key=>$opt) {
      $outstr .= "<tr><td>".$opt->option_text."</td><td><select name='OPT_".$opt_key."' onchange='recalc_display(); update_price(this,".$opt_key.")'>\n";
      $outstr .= "<option value=''>" . SELECT_OPTION . "</option>";
      foreach ($opt->values as $val_key=>$val) {
        $outstr .= "<option value='".$val_key."'";
        if ($_POST[$opt->products_options_name]==$val_key) {
          $outstr .= " SELECTED";          
        } elseif (isset($_GET['attrib']) && $attrib_url_string[1] == $val_key) {
          $outstr .= " SELECTED";          
        }
        $outstr .= ">".$val->value_text." (".$val->price_prefix." ".$default_currency_symbol.(number_format($val->value_price, 2, '.', '')).")</option>\n";
      }
      $outstr .= "</select>";
      $outstr .= "<input type=hidden name='ADJ_".$opt_key."' value='";
      if ($_POST[$opt->products_options_name]==$val_key) {
        $outstr .= $val->price_prefix.$val->value_price;
      } else {
        $outstr .= '0';
      }
      $outstr .= "'>\n";      
      $outstr .= "</td></tr>\n";
    }
    //$outstr .= "</form>\n";
    return $outstr;
  }
  
  //  Tis the form used on the product edit page to assign option values to a product
  function config_form($product_attribs) {
    global $default_currency_symbol;
    $num_items = 0;
    $last_end = "";
    foreach ($this->options as $opt_key=>$opt) {
      if ($num_items % NUM_ATTRIB_OPTIONS_PER_CONFIG_ROW == 0) {
        $outstr .= $last_end;
        $outstr .= "<tr>";
        $last_end = "</tr>\n";
      }
      $num_items++;
      $outstr .= "<td valign='top'><table><tr><th colspan=4>". $opt->option_text ."</th></tr>";
      $outstr .= "<tr><td><i>" . USE_ME . "</i></td><td><i>" . OPTION . "</i></td><td><i>" . PREFIX . "</i></td><td><i>" . PRICE . "</i></td></tr>";
      foreach ($opt->values as $val_key=>$val) {
        $active_val = false;
        $outstr .= "<tr>";
        // selection checkbox
        $outstr .= "<td><input type='checkbox' name='option_value_selected[".$opt_key."][".$val_key."]' value=1";
        if (isset($product_attribs->options[$opt_key]) && isset($product_attribs->options[$opt_key]->values[$val_key])) {
          $active_val = true;
          $outstr .= " checked";
        } 
        $outstr .= "></td>";
        // text of value
        $outstr .= "<td>".$val->value_text."</td>";
        // price prefix
        $outstr .= "<td><select name='option_value_prefix[".$opt_key."][".$val_key."]'>";
        $prefixes = array("","+","-");
        foreach ($prefixes as $pfx) {
          $outstr .= "<option value='".$pfx."'";
          if ($active_val && $pfx == $product_attribs->options[$opt_key]->values[$val_key]->price_prefix) {
            $outstr .= " SELECTED";
          }
          $outstr .= ">".$pfx."</option>";
        } 
        $outstr .= "</select>"; 
        $outstr .= "</td>";
        // price
        $outstr .= "<td>".$default_currency_symbol." <input type='text' size='4' name='option_value_price[".$opt_key."][".$val_key."]' value='";
        if ($active_val) {
          $outstr .= $product_attribs->options[$opt_key]->values[$val_key]->value_price;
        }
        $outstr .= "'></td>";
        $outstr .= "    </tr>\n";
      }
      $outstr .= "  </table></td>\n";
    }
    $outstr .= "  </tr>\n";
    return $outstr;
  }
  
  // Here's the heavy lifting for the config form, determining if and when to update the database
  function update_config_form(&$product_attributes)  {
	  if (isset($_POST['option_value_selected']) && isset($_POST['option_value_prefix']) && isset($_POST['option_value_price'])) {
      $selecteds = $_POST['option_value_selected'];
      $prefixes = $_POST['option_value_prefix'];
      $prices = $_POST['option_value_price'];
      
      foreach ($this->options as $opt_key=>$opt) {
        foreach ($opt->values as $val_key=>$val) {
          $form_select = (isset($selecteds[$opt_key]) && isset($selecteds[$opt_key][$val_key]));   
          // if value added, save to product
          // if value existed and is still selected, 
          //    update on price or prefix change
          if ($form_select) {
            if ((!$product_attributes->is_set($opt_key,$val_key)) 
                  || ($product_attributes->options[$opt_key]->values[$val_key]->has_changed($prices[$opt_key][$val_key],$prefixes[$opt_key][$val_key]))) {
               $product_attributes->add($opt_key,$val_key,$prices[$opt_key][$val_key],$prefixes[$opt_key][$val_key]);
               $product_attributes->options[$opt_key]->save($product_attributes->products_id, $val_key);
            }
          }
          // if value removed, delete from product
          elseif (!$form_select && $product_attributes->is_set($opt_key,$val_key)) {
            //print "option/value removed: $opt_key / $val_key<br>\n";            
            $product_attributes->options[$opt_key]->delete($val_key);
          } else {
            //print "option/value ignored: $opt_key / $val_key<br>\n";            
          }
        }
      }
    }    
  }
  
  //  =-=-=-=-=-= Attribute option manipulation functions =-=-=-=-=-=
  // This returns a stock configuration string based on the passed object 
  function get_stock_attribs($item) {
    $stock = array();
    foreach ($item['Attributes'] as $opt=>$val) {
      $val_id = 0;
      foreach ($val as $mkey=>$mval) {
        if ($mkey == 'value_id') {
          $val_id = $mval;
        }
      }
      $opt_key = 0;
      foreach ($this->options as $okey=>$dokey) {
        if ($opt == $dokey->option_text) {
          $opt_key = $okey;
          $stock[] = $opt_key."-".$val_id;
        }
      }
    }
    return join(",",$stock);    
  }
  
  // returns a stock configuration string based on the internal object
  function get_stock_attrib() {
    if ($this->attribute_str != '') {
      return $this->attribute_str;
    }
    $rset = array();
    foreach ($this->options as $opt_key=>$opt) {
      $rset[] = $opt_key."-".$_REQUEST["OPT_".$opt_key];
    }
    return join(",",$rset);
  }
  
  // returns a string with stock options in an HTML, UList
  // function print_order_attribs($stock_attribs) {
    // $opt_sets = explode(",",$stock_attribs);
    // $outstr = '';    
    // if ((count($opt_sets)>0) && ($opt_sets[0]!= '')) {
      // $outstr = "<ul>";          
      // foreach ($opt_sets as $oset) {
        // list($opt_key,$val_key) = explode("-",$oset);
        // $outstr .= "<li><b>".$this->options[$opt_key]->option_text."</b>: <i>".$this->options[$opt_key]->values[$val_key]->value_text."</i></li>";
      // }
      // $outstr .= "</ul>";
    // }
    // return $outstr;
  // }
  
  // returns a string with stock options in an HTML, UList
  function print_order_attribs($orders_products_id) {
    $outstr = '';    
    $sql = mysql_query("select * from " . ORDERS_PRODUCTS_ATTRIBUTES . " WHERE orders_products_id = '" . $orders_products_id . "'");
    if (mysql_num_rows($sql) > 0) {
      $outstr = "<ul>";          
      // $i=0;
      while ($results_array = mysql_fetch_array($sql)) {
        $outstr .= "<li><b>" . $results_array['products_options'] . "</b>: <i>" . $results_array['products_options_values'] . "</i></li>";
        // $i++;
      }
      $outstr .= "</ul>";
    }
    return $outstr;
  }
  
  //  =-=-=-=-=-= Attribute configuration pricing function =-=-=-=-=-=
  // Calculates the total price adjustment based on all configured options
  function get_price_adj() {
    if ($this->attribute_str == '') {
      return 0;
    } else {
      $adj = 0;
      foreach ($this->options as $opt_key=>$opt) {
        foreach ($opt->values as $val_key=>$val) {
          eval("\$adj += (".$val->price_prefix.$val->value_price.");");
        }
      }
      return $adj;
    }
  }
  
  //  =-=-=-=-=-= Attribute database manipulation functions =-=-=-=-=-=
  // Store attribute data to POS orders products attributes table
  function archive($archive_id, $post_attribs) {
    foreach ($post_attribs as $opt=>$val) {
      $vset = array();
      foreach ($val as $vkey=>$vval) {
        $vset[$vkey] = $vval;
      }
      $option_name = squeeky($opt);
      $value_name = squeeky($vset['value_text']);
  		mysql_query("INSERT INTO " . POS_ORDERS_PRODUCTS_ATTRIBUTES . " SET
  			pos_orders_id  ='" . $archive_id . "',
  			products_id='" . $this->products_id . "',
  			products_options='" . $option_name . "',
  			products_options_values='" . $value_name . "',
  			options_values_price='" . $vset['value_price'] . "',
  			price_prefix='" . $vset['price_prefix'] . "'
  		");
    }    
  }
  
  // Store attribute data to orders products attributes table
  function process($order_id, $orders_products_id, $item) {
    //echo "<textarea rows=16 cols=80>".print_r($item,true)."</textarea>";exit();
    $attribute_sets = $item->Attributes;
    if (isset($item['Attributes'])) foreach ($item['Attributes'] as $option_text=>$valueset) {
      $option_name = squeeky($option_text);
      $value_name = squeeky($valueset->value_text);
  		$insert_sql = "INSERT INTO " . ORDERS_PRODUCTS_ATTRIBUTES . " SET
  			orders_id  ='" . $order_id . "',
  			orders_products_id='" . $orders_products_id . "',
  			products_options='" . $option_name . "',
  			products_options_values='" . $value_name . "',
        options_values_price='" . $valueset->value_price . "',
        price_prefix='" . $valueset->price_prefix . "'
  		";
      $insert_result = oc_query($insert_sql, 'Order creation: inserting products');
    }    
  }
  
  // return a query suitable for retrieving all option and value combinations
  function q_get_all() {
    switch(OSC_ATTRIBUTES_MODE) {
      case 'OSC':
      case 'QTP':
        $q = mysql_query("
  SELECT * FROM ".OPTIONS_DEFINITION. " od
  LEFT JOIN ".PRODUCTS_OPTIONS. " po on po.products_options_id=od.products_options_id
  LEFT JOIN ".PRODUCTS_OPTIONS_VALUES. " pov on pov.products_options_values_id=od.products_options_values_id
        where po.language_id=".$this->language_id." and pov.language_id=".$this->language_id."
        order by od.products_options_id, pov.products_options_values_name, od.products_options_values_id");       
        return $q; 
        break;
      case 'NONE':
      default:
    }
  }
  
  // return a query suitable for retrieving all options and value combinations specific to a provided product
  function q_get_product_set($order_id = 0) {
    switch(OSC_ATTRIBUTES_MODE) {
      case 'OSC':
      case 'QTP':
        if ($order_id == 0) {
        $q = mysql_query("
  SELECT * FROM ".PRODUCTS_ATTRIBUTES. " pa
  LEFT JOIN ".PRODUCTS_OPTIONS. " po on po.products_options_id=pa.options_id
  LEFT JOIN ".PRODUCTS_OPTIONS_VALUES. " pov on pov.products_options_values_id=pa.options_values_id
  where pa.products_id=".$this->products_id." and po.language_id=".$this->language_id." and pov.language_id=".$this->language_id."
  order by pa.options_id, pov.products_options_values_name, pa.options_values_id");
        } else { // get data from orders_products intead of from products_attributes
            $q = mysql_query("
              SELECT pa.*, pov.products_options_values_id, po.*, opa.products_options_values AS products_options_values_name 
              FROM " . ORDERS_PRODUCTS_ATTRIBUTES . " opa, " . ORDERS_PRODUCTS  . " op, " . PRODUCTS_ATTRIBUTES . " pa 
               LEFT JOIN ".PRODUCTS_OPTIONS. " po on po.products_options_id=pa.options_id
               LEFT JOIN ".PRODUCTS_OPTIONS_VALUES. " pov on pov.products_options_values_id=pa.options_values_id
               WHERE op.orders_id = opa.orders_id
               AND op.orders_id = '" . $order_id . "'
               AND op.products_id=pa.products_id 
               AND pa.products_id=".$this->products_id." 
               AND po.language_id=".$this->language_id." 
               AND pov.language_id=".$this->language_id);
        }
        return $q;
        break;
      case 'NONE':
      default:
    }
  }
  
  // return a query suitable for retrieving all related values for a given attribute configuration 
  function q_get_option_info($key,$val) {
    $q = mysql_query("
  SELECT * FROM ".PRODUCTS_ATTRIBUTES. " pa
  LEFT JOIN ".PRODUCTS_OPTIONS. " po on po.products_options_id=pa.options_id
  LEFT JOIN ".PRODUCTS_OPTIONS_VALUES. " pov on pov.products_options_values_id=pa.options_values_id
  where pa.products_id=".$this->products_id." and po.products_options_id=".$key." 
  and pov.products_options_values_id=".$val." and po.language_id=".$this->language_id." 
  and pov.language_id=".$this->language_id);
    return $q;
  }
  
  // returns true if product has rows in products stock table
  function use_attrib_stock() {
    $Q_stock = mysql_query("SELECT * from ".PRODUCTS_STOCK." where products_id=".$this->products_id );       
    if (mysql_num_rows($Q_stock)>0) {
      return true;
    }
    return false;
  }
  
  // returns stock quantity for this product/attribute configuration
  // -- if no options, get standard quantity
  function get_stock_quantity() {
    $stock_attr = $this->get_stock_attrib();
    $Q_stock = mysql_query("SELECT * from ".PRODUCTS_STOCK." where products_id=".$this->products_id." and products_stock_attributes='".$stock_attr."'" );
    if (mysql_num_rows($Q_stock)>0) {
      $R_stock = mysql_fetch_assoc($Q_stock);
      return $R_stock['products_stock_quantity'];
    }
    return $this->get_std_product_quantity();
  }
  
  // returns standard quantity from products table for this product
  function get_std_product_quantity() {
    $data = mysql_fetch_assoc(mysql_query("select products_quantity from ".PRODUCTS." where products_id=".$this->products_id));
    return $data['products_quantity'];
  }
  
  // return option information for given option
  function get_defaults($option_id) {
    return mysql_fetch_assoc(mysql_query("SELECT * FROM " .PRODUCTS_OPTIONS. " WHERE products_options_id=".$option_id));
  }
  
  //  =-=-=-=-=-= Product page javascript =-=-=-=-=-=
  // Javascript functions to be included on the product page
  function product_page_js() {
    global $default_currency_symbol;
//########################################################
// #=#=#  JAVASCRIPT ALERT  --  JAVASCRIPT BELOW    #=#=#
// #=#=#--------------------------------------------#=#=#
// |||  Alert! Code below includes bare javascript.   |||
?><script type='text/javascript'>
// Options set array
var optionSet = new Array();
<?php // build options set array
     foreach ($this->options as $opt_key=>$opt) {
       print "optionSet[".$opt_key."] = '".$opt->option_text."';\n";
     }
?>
// end Options set array

// Attribute price array
var attribs = new Array();
<?php // build attributes data array 
     foreach ($this->options as $opt_key=>$opt) {
       print "attribs[".$opt_key."] = new Array();\n";
       foreach ($opt->values as $val_key=>$val) {
         print "attribs[".$opt_key."][".$val_key."] = new Array();\n";
         print "attribs[".$opt_key."][".$val_key."]['price'] = '".$val->value_price."';\n";
         print "attribs[".$opt_key."][".$val_key."]['prefix'] = '".$val->price_prefix."';\n";
       }
     }
?>
// end Attribute price array
var useAttribStock = false;
<?php
    if (is_attrib_mode('QTP')) {
      // build attribute stock array
      $Q_stock_sql = "SELECT * from " . PRODUCTS_STOCK . " where products_id = '" . $this->products_id . "'";
      $Q_stock = oc_query($Q_stock_sql, 'QTP Products Stock lookup failure');
      if (mysql_num_rows($Q_stock) > 0) {
?>
useAttribStock = true;
// Attribute stock array
var attribStock = new Array();
<?php
        while ($R_stock = mysql_fetch_assoc($Q_stock)) {
          print "attribStock['".$R_stock['products_stock_attributes']."'] = ".$R_stock['products_stock_quantity'].";\n";
        }
?>
// end Attribute stock array
<?php
      }
    }
?>

// calculates price adjustment based on current configuration
function calc_price_adj() 
{
	adj = 0;
	for (key in optionSet) {
		eval("val = parseFloat(document.product_options_form.ADJ_"+key+".value);");
		adj += val;
	}
	return adj;
}

// builds stock attribute configuration string
function build_stock_attribs() {
	cfg = '';
	for (key in optionSet) {
		eval("val = parseFloat(document.product_options_form.OPT_"+key+".value);");
		if (cfg !='') {
			cfg += ",";
		}
		cfg += key+"-"+val;
	}
	return cfg;
}

// returns stock quantity from attribStock array
function get_stock_quantity() {
	if (useAttribStock) {
		config_stock = attribStock[document.product_options_form.product_stock_attributes.value];
		if (isNaN(config_stock)) {
			return 0;
		} else {
			return config_stock;
		}
	} else {
		return <?php echo $this->get_std_product_quantity() ?>;
	}
}

// update price display as per current configuration
function update_price(form_item, opt_key) {
	val_key = form_item.value;
  price = attribs[opt_key][val_key]['price'];
  prefix = attribs[opt_key][val_key]['prefix'];
	price_adj = calc_price_adj();
	eval("document.product_options_form.ADJ_"+opt_key+".value = '"+prefix+price+"';");
	recalc_display();
	return true;
}

function check_attrform (objForm,interval,msg) {
   var returnStatus = 1;
   for (i=1;i<=interval;i++)
   {
      if (eval("(objForm.OPT_"+i+" && objForm.OPT_"+i+".selectedIndex == 0)"))
      {
   		alert(msg);
   		returnStatus = 0;
      };
   }
   if (returnStatus == 1) {
      objForm.submit();
   }
}

// general page refresh: recalculates price and quantity based on current configuration
function recalc_display() {
<?php if (count($this->options)>0) { ?>	
	document.product_options_form.product_stock_attributes.value = build_stock_attribs();
	
	new_price = parseFloat(document.product_options_form.product_base_price.value) + calc_price_adj();
	document.product_options_form.Price.value = new_price;
	document.getElementById('product_price_display').innerHTML = "<?php echo $default_currency_symbol; ?> " + new_price.toFixed(2);

	new_quant = get_stock_quantity();
	document.getElementById('product_quantity_display').innerHTML = new_quant;
	document.product_options_form.StockQuantity.value = new_quant;
<?php } ?>	
}

</script><?php
// |||     All done. We now return to plain php.      |||
// #=#=#--------------------------------------------#=#=#
// #=#=#  JAVASCRIPT ALERT  --  JAVASCRIPT ABOVE    #=#=#
//########################################################

  }
  
} // End of Attributes Class

// -=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-
// ==  options class
// -=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-
class attribute_option {
  var $option_id;
  var $option_text;
  var $products_id;
  var $track_stock;
  var $values = array();
  
  function attribute_option ($id=0,$text='',$products_id=0,$track_stock=false) {
    $this->option_id = $id;
    $this->option_text = $text;
    $this->products_id = $products_id;
    $this->track_stock = $track_stock;
  }
  
  // Store given value information as a row in the values array
  function store($value_key,$price,$prefix) {
    if (isset($this->values[$value_key])) {
      $this->values[$value_key]->value_price = $price;
      $this->values[$value_key]->price_prefix = $prefix; 
    } else {
      $defaults = $this->get_defaults($value_key);
      $this->values[$value_key] = new product_attribute_value(    
        $value_key,
        $defaults["products_options_values_name"],
        $price,
        $prefix
      );
    } 
  }
  
  // Store this option information to a table
  function save($product_id, $value_id) {
    $this->products_id = $product_id;
    $id_data = array(
      "products_id" => $product_id,
      "options_id" => $this->option_id,
      "options_values_id" => $value_id);
    $var_data = array(
      "options_values_price" => $this->values[$value_id]->value_price,
      "price_prefix" => $this->values[$value_id]->price_prefix
    ); 
    if ($this->_pa_is_set($value_id)) {
      // update value
      _data_update(PRODUCTS_ATTRIBUTES,$id_data,$var_data);
    } else {
      // insert value
      _data_insert(PRODUCTS_ATTRIBUTES,$id_data,$var_data);
    }    
  }
  
  // return true if given value id is present in values array
  function is_set($value_id) {
    return (isset($this->values[$value_id]));
  }

  // returns true if product configuration is already in products attributes table
  function _pa_is_set($value_id) {
    if ($this->products_id > 0) {
      $Q_option = mysql_query("SELECT * FROM ".PRODUCTS_ATTRIBUTES."
      WHERE products_id=".$this->products_id." 
      AND options_id=".$this->option_id." 
      AND options_values_id=".$value_id);
      if (mysql_num_rows($Q_option) > 0) {
        return true;
      } 
    }
    return false;
  }
  
  // delete this option from the products attributes table
  function delete($value_id) {
    if (isset($this->values[$value_id])) {
      $q = "DELETE FROM ".PRODUCTS_ATTRIBUTES." WHERE products_id=".$this->products_id." AND options_id=".$this->option_id." AND options_values_id=".$value_id;     
      mysql_query($q);
      unset($this->values[$value_id]);
    } else {
      exit("whacky error!");
    }
  }
  
  // return information for specific configuration
  function get_defaults($value_id) {
    return mysql_fetch_assoc(mysql_query("SELECT * FROM " .PRODUCTS_OPTIONS_VALUES. " WHERE products_options_values_id=".$value_id));
  }
  
  
}

// -=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-
// ==  values classes
// -=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-
// These classes exist in two forms, based on whether configuration 
// calculations will be necessary, or if merely the configuration itself
// needs to be stored
class product_attribute_value extends attribute_value {
  var $value_price;
  var $price_prefix;
  
  function product_attribute_value($id=0, $text='', $price=0, $prefix='') {
    parent::attribute_value($id,$text);
    $this->value_price = $price;
    $this->price_prefix = $prefix;
  }    
  
  function has_changed($price,$prefix) {
    if (($this->value_price != $price) || ($this->price_prefix != $prefix)) {
      return true;
    }
    return false;
  }
}

class attribute_value {
  var $value_id;
  var $value_text;
  
  function attribute_value($id=0, $text='') {
    $this->value_id = $id;
    $this->value_text = $text;
  }    
}

// -=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-
// ==  Utility routines
// -=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-

function is_attrib_mode($testmode) {
  return ($testmode == OSC_ATTRIBUTES_MODE);
}

function squeeky($str) {
	$str = str_replace("\'","'",$str);
	$str = str_replace("'","\'",$str);
	$str = str_replace('"','\"',$str);
	return $str;
}
  
function _data_debug($display_set,$exit=false) {
  print "<textarea rows=20 cols=80>";
  if (is_array($display_set)) {
    foreach ($display_set as $item) {
      if (is_array($item) || is_object($item)) {
        print_r($item);
      } else {
        print "==> ".$item;
      }
      print "\n";
    }
  } else {
    print_r($display_set);
  }
  print "</textarea>\n";
  if ($exit) {
    exit();
  }
}

// -=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-
// ==  Database manipulation routines
// -=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-

function _data_insert($table_name, $id_data, $datablock) {
  $q = "INSERT INTO ".$table_name." (";
  $fields = array();
  $values = array();
  foreach ($id_data as $key=>$val) {
    $fields[] = $key;
    $values[] = $val;
  }
  foreach ($datablock as $key=>$val) {
    $fields[] = $key;
    $values[] = $val;
  }
  $q .= join(", ",$fields).") VALUES ('".join("', '",$values)."')";
  mysql_query($q);
}

function _data_update($table_name, $id_data, $datablock) {
  $fldset = array();
  foreach ($datablock as $key=>$val) {
    $fldset[] = $key."='".$val."' ";
  }
  $valset = array();
  foreach ($id_data as $key=>$val) {
    $valset[] = $key."='".$val."'";
  }
  $q = "UPDATE ".$table_name." SET ";
  $q .= join(", ",$fldset);
  $q .= "WHERE ";
  $q .= join(" AND ",$valset); 
  mysql_query($q);
}


?>
