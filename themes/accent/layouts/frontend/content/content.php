<?php defined('ENVIRONMENT') OR die('Invalid access'); ?>
<?php themeFunctions::debugFileLine('start'); ?>
<div id="page-content">
  <?php echo $this->content(); ?>
</div>
<?php themeFunctions::loadAddons(__FILE__); ?>
<div class="container">
  <div class="row">
    <div class="col-12 mt-3">
      <div class="bottomBorder"></div>
    </div>
  </div>
</div>
<?php themeFunctions::debugFileLine('end'); ?>