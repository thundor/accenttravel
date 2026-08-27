<?php defined('ENVIRONMENT') OR die('Invalid access'); ?>
<?php themeFunctions::debugFileLine('start'); ?>
<div class="price-filter">
  <h5 class="subTitleFilter"><i class="fa fa-money"></i> Tarif vacanta</h5>
  <div class="mb-5"> 
    <input type="text" id="amount" class="border-0 mb-1" readonly><div id="slider-range"></div>
  </div>
</div>
<?php themeFunctions::loadAddons(__FILE__); ?>
<?php themeFunctions::debugFileLine('end'); ?>