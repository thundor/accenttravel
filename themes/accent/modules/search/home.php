<?php defined('ENVIRONMENT') OR die('Invalid access'); ?>
<?php themeFunctions::debugFileLine('start'); ?>
<div class="searchMenu1 rounded">
  <?php include 'includes/navbar.php'; ?>
  <?php include 'includes/content.php'; ?>
</div>
<?php themeFunctions::debugFileLine('end'); ?>
<?php themeFunctions::loadAddons(__FILE__); ?>