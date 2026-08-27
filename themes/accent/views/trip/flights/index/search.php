<?php defined('ENVIRONMENT') OR die('Invalid access'); ?>
<?php themeFunctions::debugFileLine('start'); ?>
<?php themeFunctions::loadModule('flights/search/form',__FILE__); ?>
<div class="searchMenuHotel">
  <div class="tab-content">
    <?php themeFunctions::loadAddons(__FILE__); ?>
  </div>
</div>
<?php themeFunctions::debugFileLine('end'); ?>