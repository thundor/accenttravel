<?php defined('ENVIRONMENT') OR die('Invalid access'); ?>
<?php themeFunctions::debugFileLine('start'); ?>
<div class="rezCount" style="display:none;">
  <a class="float-right mb-2 mt-2 mapInfo" href="#"><i class="fa fa-info-circle"></i> INFO <span></span></a>
  <h1 class="filterTitle pl-0"></h1>
  <p>
    <i class="fa fa-calendar"></i>
    <span class="selected_date_start"></span>
    <i class="fa fa-angle-right"></i>
    <span class="selected_date_end"></span>
    <span class="selected_date_interval"></span>
    <i class="fa fa-hotel"></i> <span class="selected_rooms"></span>
    <i class="fa fa-users"></i> <span class="selected_passengers"></span>
  </p>
</div>
<?php themeFunctions::loadAddons(__FILE__); ?>
<?php themeFunctions::debugFileLine('end'); ?>