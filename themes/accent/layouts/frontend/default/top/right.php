<?php defined('ENVIRONMENT') OR die('Invalid access'); ?>
<?php themeFunctions::debugFileLine('start'); ?>
<div class="row">
  <div class="col-12 offset-md-8 col-md-4 text-right">
    <div class="float-right w-100">
      <?php include 'right/pos2.php'; ?>
    </div>
  </div>
</div>
<?php include 'right/pos1.php'; ?>
<?php themeFunctions::loadAddons(__FILE__); ?>
<?php themeFunctions::debugFileLine('end'); ?>