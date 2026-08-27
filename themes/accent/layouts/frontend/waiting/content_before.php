<?php defined('ENVIRONMENT') OR die('Invalid access'); ?>
<?php themeFunctions::debugFileLine('start'); ?>
<?php themeFunctions::loadModule('logo/link',__FILE__ . '/pos1'); ?>
<div class="topHeader">
  <div class="container">
    <div class="row">
      <div class="col-sm-12 text-center logoTop1">
      <?php themeFunctions::loadAddons(__FILE__ . '/pos1'); ?>
      </div>
    </div>
  </div>
</div>
<?php themeFunctions::loadAddons(__FILE__); ?>
<?php themeFunctions::debugFileLine('end'); ?>