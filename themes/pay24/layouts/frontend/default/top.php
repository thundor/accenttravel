<?php defined('ENVIRONMENT') OR die('Invalid access'); ?>
<?php themeFunctions::debugFileLine('start'); ?>
<div class="topHeader">
  <div class="container">
    <div class="row">
      <div class="col-sm-12 col-md-2 logoTop1">
        <?php include 'top/logo.php'; ?>
      </div>
      <div class="col-sm-12 col-md-10 text-right">
        <?php include 'top/right.php'; ?>
      </div>
    </div>
  </div>
</div>
<?php themeFunctions::loadAddons(__FILE__); ?>
<?php themeFunctions::debugFileLine('end'); ?>