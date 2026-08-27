<?php defined('ENVIRONMENT') OR die('Invalid access'); ?>
<?php 
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