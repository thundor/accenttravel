<?php defined('ENVIRONMENT') OR die('Invalid access'); ?>
<?php themeFunctions::debugFileLine('start'); ?>
<?php
$product = $this->view_data['product_info'];
$title = ucfirst($product->ProductType) . ' ' . $product->ProductName . ', ' . $product->CityName . ', ' . $product->CountryName;
?>
<title>Rezervare camere <?php echo $title; ?> | Accent Travel & Events | Agentie de turism</title>
<meta name="description" content="">
<meta name="keywords" content="">
<?php themeFunctions::loadAddons(__FILE__); ?>
<?php themeFunctions::debugFileLine('end'); ?>