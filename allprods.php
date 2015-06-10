<?php


include("includes/db.php");
include("includes/functions.php");
include("includes/session.php");

LoadLangFiles($lang);

/* // Determine if user is logged in
$session->logged_in = $session->checkLogin();

// If nobody is logged in, require login
if(!$session->logged_in) {
    header('Location: login.php');
} */

$languages_id = get_default_lang();
if (isset($_GET['cPath'])) $current_category_id = $_GET['cPath'];

// clas and function info from osCommerce
  class objectInfo {

// class constructor
    function objectInfo($object_array) {
      reset($object_array);
      while (list($key, $value) = each($object_array)) {
        $this->$key = tep_db_prepare_input($value);
      }
    }
  }
  
  function tep_db_prepare_input($string) {
    if (is_string($string)) {
      return trim(stripslashes($string));
    } elseif (is_array($string)) {
      reset($string);
      while (list($key, $value) = each($string)) {
        $string[$key] = tep_db_prepare_input($value);
      }
      return $string;
    } else {
      return $string;
    }
  }
  
  function tep_get_category_name($category_id, $language_id=1, $fakename=0) {
    $category_query = mysql_query("select categories_name from " . CATEGORIES_DESCRIPTION . " where categories_id = '" . (int)$category_id . "' and language_id = '" . (int)$language_id . "'");
    $category = mysql_fetch_array($category_query);

    if ($fakename == 1) {
        if (substr_count($category['categories_name'], '_bundle') > 0 ) {
            $inventory_name = substr_replace($category['categories_name'], ' Inventory ', -8, -1);
        } else {
            $inventory_name = $category['categories_name'];
            
        }
        return $inventory_name;
    }
    
    return $category['categories_name'];
  }
  
////
// Count how many subcategories exist in a category
// TABLES: categories
  function tep_childs_in_category_count($categories_id) {
    $categories_count = 0;

    $categories_query = mysql_query("select categories_id from " . CATEGORIES . " where parent_id = '" . (int)$categories_id . "'");
    while ($categories = mysql_fetch_array($categories_query)) {
      $categories_count++;
      $categories_count += tep_childs_in_category_count($categories['categories_id']);
    }

    return $categories_count;
  }
  
////
// Count how many products exist in a category
// TABLES: products, products_to_categories, categories
  function tep_products_in_category_count($categories_id, $include_deactivated = false) {
    $products_count = 0;

    if ($include_deactivated) {
      $products_query = mysql_query("select count(*) as total from " . PRODUCTS . " p, " . PRODUCTS_TO_CATEGORIES . " p2c where p.products_id = p2c.products_id and p2c.categories_id = '" . (int)$categories_id . "'");
    } else {
      $products_query = mysql_query("select count(*) as total from " . PRODUCTS . " p, " . PRODUCTS_TO_CATEGORIES . " p2c where p.products_id = p2c.products_id and p.products_status = '1' and p2c.categories_id = '" . (int)$categories_id . "'");
    }

    $products = mysql_fetch_array($products_query);

    $products_count += $products['total'];

    $childs_query = mysql_query("select categories_id from " . CATEGORIES . " where parent_id = '" . (int)$categories_id . "'");
    if (mysql_num_rows($childs_query)) {
      while ($childs = mysql_fetch_array($childs_query)) {
        $products_count += tep_products_in_category_count($childs['categories_id'], $include_deactivated);
      }
    }

    return $products_count;
  }
  
////
// The HTML href link wrapper function
  function tep_href_link($page = '', $parameters = '', $connection = 'NONSSL') {
    if ($page == '') {
      die('</td></tr></table></td></tr></table><br><br><font color="#ff0000"><b>Error!</b></font><br><br><b>Unable to determine the page link!<br><br>Function used:<br><br>tep_href_link(\'' . $page . '\', \'' . $parameters . '\', \'' . $connection . '\')</b>');
    }
    // if ($connection == 'NONSSL') {
      // $link = HTTP_SERVER . DIR_WS_ADMIN;
    // } elseif ($connection == 'SSL') {
      // if (ENABLE_SSL == 'true') {
        // $link = HTTPS_SERVER . DIR_WS_ADMIN;
      // } else {
        // $link = HTTP_SERVER . DIR_WS_ADMIN;
      // }
    // } else {
      // die('</td></tr></table></td></tr></table><br><br><font color="#ff0000"><b>Error!</b></font><br><br><b>Unable to determine connection method on a link!<br><br>Known methods: NONSSL SSL<br><br>Function used:<br><br>tep_href_link(\'' . $page . '\', \'' . $parameters . '\', \'' . $connection . '\')</b>');
    // }
    $link = '';
    if ($parameters == '') {
      $link = $link . $page . '?';
    } else {
      $link = $link . $page . '?' . $parameters;
    }

    while ( (substr($link, -1) == '&') || (substr($link, -1) == '?') ) $link = substr($link, 0, -1);

    return $link;
  }
  
  function tep_get_path($current_category_id = '') {
    global $cPath_array;

    if ($current_category_id == '') {
      $cPath_new = implode('_', $cPath_array);
    } else {
      if (sizeof($cPath_array) == 0) {
        $cPath_new = $current_category_id;
      } else {
        $cPath_new = '';
        $last_category_query = mysql_query("select parent_id from " . CATEGORIES . " where categories_id = '" . (int)$cPath_array[(sizeof($cPath_array)-1)] . "'");
        $last_category = mysql_fetch_array($last_category_query);

        $current_category_query = mysql_query("select parent_id from " . CATEGORIES . " where categories_id = '" . (int)$current_category_id . "'");
        $current_category = mysql_fetch_array($current_category_query);

        if ($last_category['parent_id'] == $current_category['parent_id']) {
          for ($i = 0, $n = sizeof($cPath_array) - 1; $i < $n; $i++) {
            $cPath_new .= '_' . $cPath_array[$i];
          }
        } else {
          for ($i = 0, $n = sizeof($cPath_array); $i < $n; $i++) {
            $cPath_new .= '_' . $cPath_array[$i];
          }
        }

        $cPath_new .= '_' . $current_category_id;

        if (substr($cPath_new, 0, 1) == '_') {
          $cPath_new = substr($cPath_new, 1);
        }
      }
    }

    return 'cPath=' . $cPath_new;
  }
  
////
// The HTML image wrapper function
  function tep_image($src, $alt = '', $width = '', $height = '', $parameters = '') {
    $image = '<img src="' . tep_output_string($src) . '" border="0" alt="' . tep_output_string($alt) . '"';

    if (tep_not_null($alt)) {
      $image .= ' title=" ' . tep_output_string($alt) . ' "';
    }

    if (tep_not_null($width) && tep_not_null($height)) {
      $image .= ' width="' . tep_output_string($width) . '" height="' . tep_output_string($height) . '"';
    }

    if (tep_not_null($parameters)) $image .= ' ' . $parameters;

    $image .= '>';

    return $image;
  }
  
  function tep_not_null($value) {
    if (is_array($value)) {
      if (sizeof($value) > 0) {
        return true;
      } else {
        return false;
      }
    } else {
      if ( (is_string($value) || is_int($value)) && ($value != '') && ($value != 'NULL') && (strlen(trim($value)) > 0)) {
        return true;
      } else {
        return false;
      }
    }
  }
  
  function tep_info_image($image, $alt, $width = '', $height = '') {
    if (tep_not_null($image) && (file_exists(DIR_FS_CATALOG_IMAGES . $image)) ) {
      $image = tep_image(DIR_WS_CATALOG_IMAGES . $image, $alt, $width, $height);
    } else {
      $image = TEXT_IMAGE_NONEXISTENT;
    }

    return $image;
  }
  
////
// Output a function button in the selected language
  function tep_image_button($image, $alt = '', $params = '') {
    global $language;

    return tep_image(DIR_WS_LANGUAGES . $language . '/images/buttons/' . $image, $alt, '', '', $params);
  }

  
////
// Parse the data used in the html tags to ensure the tags will not break
  function tep_parse_input_field_data($data, $parse) {
    return strtr(trim($data), $parse);
  }

  function tep_output_string($string, $translate = false, $protected = false) {
    if ($protected == true) {
      return htmlspecialchars($string);
    } else {
      if ($translate == false) {
        return tep_parse_input_field_data($string, array('"' => '&quot;'));
      } else {
        return tep_parse_input_field_data($string, $translate);
      }
    }
  }
  
// determine whether or not to include disabled products in search results
// if (ALLOW_DISABLED_PRODUCTS == '0') {
// $products_status_filter = " and p.products_status = '1' ";
// } else {
    // $products_status_filter = '';
// }

// need to show all products, active or not.  Otherwise there is potential confusion with Tranfer Orders
$products_status_filter = '';

$categories_count = 0;
$rows = 0;

if (isset($_GET['cPath'])) {
    $parent_category_id = $_GET['cPath'];
} else {    
    if ($_SESSION['username'] == 'admin') {
        $parent_category_id = 0;
    } else {
        $sql = "SELECT categories_id FROM " . CATEGORIES_DESCRIPTION . " WHERE categories_name = '" . $_SESSION['username'] . "'";
        $query_result = oc_query($sql, 'Category ID Lookup');
        $result_array = mysql_fetch_array($query_result);
        $parent_category_id = $result_array['categories_id'];
    }
}

?>
<!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 4.01 Transitional//EN">

<html>
<head>
<meta http-equiv="Content-Type" content="text/html; charset=<?php echo CHARSET; ?>">
<title>All Products</title>
<link rel="Stylesheet" href="css/style.css">

</head>
<body marginwidth="0" marginheight="0" topmargin="0" bottommargin="0" leftmargin="0" rightmargin="0" bgcolor="#FFFFFF" onload="SetFocus();">
<?php include("includes/header.php"); ?>
<div id="spiffycalendar" class="text"></div>

<table class="tableBorder" border="0" width="760" cellpadding="2" cellspacing="1" align="center">
    <tr>
        <td class="tdBlue" align="center" width="400"><b><?php echo tep_get_category_name($parent_category_id, 1, 1); ?></b></td>
        <td class="tdBlue" align="center" width="100"><b>Model Number</b></td>
        <td class="tdBlue" align="center" width="70"><b>Size</b></td>
        <td class="tdBlue" align="center" width="70"><b>Quantity in Stock</b></td>
    </tr>
<?php    
    // $products_stock_table = GetProductStockTableName();


    $categories_query = mysql_query("select c.categories_id, cd.categories_name, c.categories_image, c.parent_id, c.sort_order, c.date_added, c.last_modified from " . CATEGORIES . " c, " . CATEGORIES_DESCRIPTION . " cd where c.parent_id = '" . (int)$parent_category_id . "' and c.categories_id = cd.categories_id and cd.language_id = '" . (int)$languages_id . "' order by c.sort_order, cd.categories_name");

    while ($categories = mysql_fetch_array($categories_query)) {
      $categories_count++;
      $rows++;

        $category_childs = array('childs_count' => tep_childs_in_category_count($categories['categories_id']));
        $category_products = array('products_count' => tep_products_in_category_count($categories['categories_id']));

        $cInfo_array = array_merge($categories, $category_childs, $category_products);
        $cInfo = new objectInfo($cInfo_array);

      if (isset($cInfo) && is_object($cInfo) && ($categories['categories_id'] == $cInfo->categories_id) ) {
        echo '    <tr onclick="document.location.href=\'' . tep_href_link('allprods.php', tep_get_path($categories['categories_id'])) . '\'">' . "\n";
      } else {
        echo '    <tr onclick="document.location.href=\'' . tep_href_link('allprods.php', 'cPath=' . $cPath . '&cID=' . $categories['categories_id']) . '\'">' . "\n";
      }
      
      if (substr_count($categories['categories_name'], '_bundle') > 0) {
        $catname = substr_replace($categories['categories_name'], ' Inventory ', -8, -1);
      } else {
        $catname = $categories['categories_name'];
      }
?>
        <td class="dataTableContent"><?php echo '<a href="' . tep_href_link('allprods.php', tep_get_path($categories['categories_id'])) . '"><img src="images/folder.gif" border="0"></a>&nbsp;<b>' . $catname . '</b>'; ?></td>
        <td align="center">&nbsp;</td>
        <td align="center">&nbsp;</td>
        <td align="center">&nbsp;</td>
    </tr>
<?php
    }

    $products_count = 0;
    $products_by_size_count = 0;
    $in_stock_count = 0;
 
    $products_query = mysql_query("select p.products_id, pd.products_name, p.products_quantity, p.products_model, p.products_image, p.products_price, p.products_date_added, p.products_last_modified, p.products_date_available, p.products_status, p.products_ordered from " . PRODUCTS . " p, " . PRODUCTS_DESCRIPTION . " pd, " . PRODUCTS_TO_CATEGORIES . " p2c where p.products_id = pd.products_id and pd.language_id = '" . (int)$languages_id . "' and p.products_id = p2c.products_id and p2c.categories_id = '" . (int)$parent_category_id . "' $products_status_filter order by pd.products_name");
    
    while ($products = mysql_fetch_array($products_query)) {
      $products_count++;
      $rows++;

?>
    <tr>
        <td class="dataTableContent"><a href="product.php?ProductID=<?php echo $products['products_id']; ?>"><?php echo $products['products_name']; ?></a></td>
<!-- show model number -->
        <td align="center"><?php echo $products['products_model']; ?></td>
        <td align="right">&nbsp;</td>
        <td align="right">&nbsp;</td>
    </tr>
<!-- show products in stock -->
<?php

      
      $products_stock_query = mysql_query("SELECT ps.products_stock_attributes, ps.products_stock_quantity, pov.products_options_values_name FROM " . PRODUCTS_STOCK . " ps,  " . PRODUCTS_OPTIONS_VALUES . " pov WHERE pov.products_options_values_id = substring(ps.products_stock_attributes,3) AND ps.products_id = '" . $products['products_id'] . "'");
      
      while ($products_stock_results = mysql_fetch_array($products_stock_query)) {
        $products_by_size_count++;
        $in_stock_count += $products_stock_results['products_stock_quantity'];
        echo '    <tr>' . "\n";
        echo '        <td colspan="2">&nbsp;</td>'. "\n";
        echo '        <td align="center">' . $products_stock_results['products_options_values_name'] . '</td>'. "\n";
        echo '        <td align="center">' . $products_stock_results['products_stock_quantity'] . '</td>'. "\n";
        echo '    </tr>';
        } 
?>
<?php
    }

    $cPath_back = '';
    if (sizeof($cPath_array) > 0) {
      for ($i=0, $n=sizeof($cPath_array)-1; $i<$n; $i++) {
        if (empty($cPath_back)) {
          $cPath_back .= $cPath_array[$i];
        } else {
          $cPath_back .= '_' . $cPath_array[$i];
        }
      }
    }

    $cPath_back = (tep_not_null($cPath_back)) ? 'cPath=' . $cPath_back . '&' : '';
?>
    <tr>
        <td class="tdBlue" colspan="4" align="right">&nbsp;</td>
    </tr>
    <tr>
        <td class="tdBlue" colspan="2" align="right">Products by Size</td>
        <td class="tdBlue" align="center"><b><?php echo $products_by_size_count; ?></b></td>
        <td class="tdBlue" align="right">&nbsp;</td>
    </tr>
    <tr>
        <td class="tdBlue" colspan="2" align="right">Total Units In Stock</td>
        <td class="tdBlue" align="right">&nbsp;</td>
        <td class="tdBlue" align="center"><b><?php echo $in_stock_count; ?></b></td>
    </tr>

  <tr>
    <td colspan="4"><table border="0" width="100%" cellspacing="0" cellpadding="2">
      <tr>
        <td class="smallText"><br><br><?php echo 'Categories&nbsp;' . $categories_count . '<br>' . 'Products&nbsp;' . $products_count; ?></td>
        <td align="right" class="smallText"><?php if (sizeof($cPath_array) > 0) echo '<a href="' . tep_href_link('allprods.php', $cPath_back . 'cID=' . $current_category_id) . '">' . tep_image_button('button_back.gif', 'Back') . '</a>&nbsp;'; ?>&nbsp;</td>
      </tr>
    </table></td>
  </tr>
</table>
<?php include("includes/footer.php"); ?>
</body>
</html>
