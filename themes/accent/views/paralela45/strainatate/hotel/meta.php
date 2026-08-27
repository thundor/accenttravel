<?php defined('ENVIRONMENT') OR die('Invalid access'); ?>
<?php themeFunctions::debugFileLine('start'); ?>
<?php
$product = $this->view_data['product'];
$title = $product->ProductName . ', ' . $product->CityName . ', ' . $product->CountryName;
?>
<title><?php echo htmlspecialchars($title); ?></title>
<meta name="description" content="<?php echo htmlspecialchars($title); ?>">
<meta name="keywords" content="">
<?php themeFunctions::loadAddons(__FILE__); ?>
<?php themeFunctions::debugFileLine('end'); ?>