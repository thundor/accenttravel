<?php defined('ENVIRONMENT') OR die('Invalid access'); ?>
<?php themeFunctions::debugFileLine('start'); ?>
<?php
$hotel_details = $this->view_data['hotel_details'];
$title = 'Oferta City Break ' . $hotel_details->Type . ' ' . $hotel_details->Name . ', ' . $hotel_details->CityName . ', ' . $hotel_details->CountryName;
?>
<title><?php echo $title; ?></title>
<meta name="description" content="<?php echo htmlspecialchars($hotel_details->ShortDesc); ?>">
<meta name="keywords" content="">
<?php themeFunctions::loadAddons(__FILE__); ?>
<?php themeFunctions::debugFileLine('end'); ?>