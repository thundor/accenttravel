<?php defined('ENVIRONMENT') OR die('Invalid access'); ?>
<?php themeFunctions::debugFileLine('start'); ?>
<?php
$package_details = $this->view_data['package_details'];
$title = 'Vacanta ' . $package_details->Name . ' - ' . $package_details->ProjectName;
?>
<title><?php echo $title; ?></title>
<meta name="description" content="<?php echo htmlspecialchars($package_details->Description); ?>">
<meta name="keywords" content="">
<?php themeFunctions::loadAddons(__FILE__); ?>
<?php themeFunctions::debugFileLine('end'); ?>