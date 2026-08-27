<?php defined('ENVIRONMENT') OR die('Invalid access'); ?>
<?php themeFunctions::debugFileLine('start'); ?>
<?php themeFunctions::loadModule('static/menu',__FILE__); ?>
<div class="bg-primary">
  <div class="container">
    <div class="row">
      <?php themeFunctions::loadAddons(__FILE__); ?>
    </div>
  </div> 
</div>
<?php themeFunctions::debugFileLine('end'); ?>