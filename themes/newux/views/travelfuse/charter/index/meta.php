<?php defined('ENVIRONMENT') OR die('Invalid access'); ?>
<?php themeFunctions::debugFileLine('start'); ?>
<?php
$hotel_details = $this->view_data['hotel_details'];
$address = json_decode(json_encode($hotel_details->Address), true);
$address_str =  implode(', ', array_filter([$address['City']['Name'] ?? '', $address['City']['County']['Name'] ?? '', $address['City']['Country']['Name'] ?? '']));
$title = $hotel_details->Name . ', ' . $address_str;
?>
<title><?php echo $title; ?></title>
<meta name="description" content="<?php echo htmlspecialchars($hotel_details->ShortContent ?? ''); ?>">
<meta name="keywords" content="">
<?php themeFunctions::loadAddons(__FILE__); ?>
<?php themeFunctions::debugFileLine('end'); ?>