<?php defined('ENVIRONMENT') OR die('Invalid access'); ?>
<?php 
$package_details = $this->view_data['package_details'];
$package_availability = $this->view_data['package_availability'];
$entry_details = $this->view_data['entry_details'];
$this->package_details = &$package_details;
$this->package_availability = &$package_availability;
$this->entry_details = &$entry_details;
themeFunctions::loadLang('package');
themeFunctions::includeAddon('forms-validation');
themeFunctions::includeAddon('formatter');
themeFunctions::includeAddon('datepicker');
themeFunctions::addIncludePath('includes/body/scripts.php', __DIR__ . '/booking/scripts.php');
themeFunctions::addIncludePath('includes/head/stylesheets.php', __DIR__ . '/booking/stylesheets.php');
themeFunctions::addIncludePath('modules/head/meta.php', __DIR__ . '/booking/meta.php');

themeFunctions::loadAddons(__FILE__);
themeFunctions::debugFileLine('start'); ?>
<div class="container">
  <?php include 'booking/details.php'; ?>
</div>
<?php themeFunctions::debugFileLine('end'); ?>