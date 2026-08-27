<?php defined('ENVIRONMENT') OR die('Invalid access'); ?>
<?php 
$flight_details = $this->view_data['flight_details'];
$this->flight_details = &$flight_details;
// echo '<pre>';
// print_r($this->flight_details);
// die;
themeFunctions::includeAddon('forms-validation');
themeFunctions::includeAddon('formatter');
themeFunctions::includeAddon('datepicker');
themeFunctions::addIncludePath('includes/body/scripts.php', __DIR__ . '/booking/scripts.php');
themeFunctions::addIncludePath('includes/head/stylesheets.php', __DIR__ . '/booking/stylesheets.php');
themeFunctions::addIncludePath('modules/head/meta.php', __DIR__ . '/booking/meta.php');
themeFunctions::debugFileLine('start'); ?>
<div class="container">
  <?php include 'booking/details.php'; ?>
</div>
<?php themeFunctions::loadAddons(__FILE__); ?>
<?php themeFunctions::debugFileLine('end'); ?>