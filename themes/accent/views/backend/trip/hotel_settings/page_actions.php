<?php defined('ENVIRONMENT') OR die('Invalid access'); ?>
<?php themeFunctions::debugFileLine('start'); ?>
<li class="nav-item">
  <button type="button" onclick="submitHotelSettings();" class="btn btn-success">
    <i class="fa fa-save"></i> Salvare
  </button>
</li>
<?php themeFunctions::loadAddons(__FILE__); ?>
<?php themeFunctions::debugFileLine('end'); ?>