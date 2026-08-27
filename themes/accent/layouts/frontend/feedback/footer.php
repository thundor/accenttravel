<?php defined('ENVIRONMENT') OR die('Invalid access'); ?>
<?php themeFunctions::debugFileLine('start'); ?>
<?php themeFunctions::loadModule('static/copyright',__FILE__ . '/footer_pos1', array('class'=>'text-center')); ?>
<?php themeFunctions::loadModule('static/certificates',__FILE__ . '/footer_pos2', array('class'=>'float-none mt-3 mb-3')); ?>
<?php themeFunctions::loadModule('static/payment_methods',__FILE__ . '/footer_pos3', array('class'=>'float-none mt-3 mb-3')); ?>
<div class="container underFoot">
  <div class="row mb-3 mt-3">
    <div class="col-12 text-center">
      <?php themeFunctions::loadAddons(__FILE__ . '/footer_pos1'); ?>
      <?php themeFunctions::loadAddons(__FILE__ . '/footer_pos2'); ?>
      <br />
      <?php themeFunctions::loadAddons(__FILE__ . '/footer_pos3'); ?>
    </div>
  </div>  
</div>
<?php themeFunctions::loadAddons(__FILE__); ?>
<?php themeFunctions::debugFileLine('end'); ?>