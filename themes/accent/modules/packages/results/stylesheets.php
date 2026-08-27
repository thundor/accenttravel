<?php defined('ENVIRONMENT') OR die('Invalid access'); ?>
<?php themeFunctions::loadAddons(__FILE__); ?>
<?php themeFunctions::debugFileLine('start'); ?>
<style type="text/css">
.selected_date_end,
.selected_date_start {
  text-transform: capitalize;
}
#modalMapH{
  position:fixed !important;
  height:523px;
}
.hotel-image{
  display: inline-block;
  height: 1px;
  width: 100%;
  padding-bottom: 70%;
  overflow: hidden;
  background-position: center;
  background-size: cover;
}
.show-rest{
  padding-left: 4px;
}
.show-rest::before{
  content:'...';
  color: #999;
}
.hotel-filters-content{
  max-height:300px;
  overflow-y: auto;
}
</style>
<?php themeFunctions::debugFileLine('end'); ?>