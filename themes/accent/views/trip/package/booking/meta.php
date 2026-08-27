<?php defined('ENVIRONMENT') OR die('Invalid access'); ?>
<?php themeFunctions::debugFileLine('start'); ?>
<?php
$package_details = $this->view_data['package_details'];
$title = lang($package_details->Type) . ' ' . $package_details->Name . ', ' . $package_details->Category;
$description = isset($package_details->Description) ? $package_details->Description : '';
?>
<title>Rezervare camere Vacanta <?php echo $title; ?> | Accent Travel & Events | Agentie de turism</title>
<meta name="description" content="<?php echo htmlspecialchars($description); ?>">
<meta name="keywords" content="">
<?php themeFunctions::loadAddons(__FILE__); ?>
<?php themeFunctions::debugFileLine('end'); ?>