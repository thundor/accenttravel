<?php defined('ENVIRONMENT') OR die('Invalid access'); ?>
<?php themeFunctions::debugFileLine('start'); ?>
<div class="rezCount">
  <?php include 'info.php'; ?>
  <div class="col-sm-12 mb-2 text-center pl-0">
    <h4 class="filterTitleT rounded"><i class="fa fa-filter"></i> Filtre cautare <i class="fa fa-plus-square-o"></i> </h4>
    <h4 class="calendarT rounded"><i class="fa fa-calendar"></i> Calendar tarife  <i class="fa fa-plus-square-o"></i>  </h4>
    <div class="clearfix"></div>
    <?php include 'content_filters.php'; ?>
    <?php include 'content_calendar.php'; ?>
    <?php themeFunctions::loadAddons(__FILE__); ?>
  </div>
</div>
<?php themeFunctions::debugFileLine('end'); ?>