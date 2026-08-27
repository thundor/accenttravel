<?php defined('ENVIRONMENT') OR die('Invalid access'); ?>
<?php themeFunctions::debugFileLine('start'); ?>
<h5 class="subTitleFilter"><i class="fa fa-money"></i> Tarif oferte</h5>
<div class="mb-4"> 
  <input type="text" id="amount" class="border-0 mb-1" readonly><div id="slider-range"></div>
</div>
<?php themeFunctions::loadAddons(__FILE__); ?>
<?php themeFunctions::debugFileLine('end'); ?>