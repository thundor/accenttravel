<?php defined('ENVIRONMENT') OR die('Invalid access'); ?>
<?php themeFunctions::debugFileLine('start'); ?>
<?php themeFunctions::loadModule('static/copyright',__FILE__ . '/footer_pos1'); ?>
<?php themeFunctions::loadModule('static/certificates',__FILE__ . '/footer_pos2'); ?>
<?php themeFunctions::loadModule('static/payment_methods',__FILE__ . '/footer_pos3'); ?>
<div class="container underFoot">
  <div class="row mb-3 mt-3">
    <div class="col-12 col-sm-12 col-md-5">
      <?php themeFunctions::loadAddons(__FILE__ . '/footer_pos1'); ?>
    </div>
    <div class="col-12 col-sm-12 col-md-3">
      <?php themeFunctions::loadAddons(__FILE__ . '/footer_pos2'); ?>
    </div>
    <div class="col-12 col-sm-12 col-md-4">
      <?php themeFunctions::loadAddons(__FILE__ . '/footer_pos3'); ?>
    </div>
  </div>  
</div>
<?php themeFunctions::debugFileLine('end'); ?>
<?php themeFunctions::loadAddons(__FILE__); ?>