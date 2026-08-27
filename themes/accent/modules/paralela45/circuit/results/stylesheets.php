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
.checkWrapper label span.filter_results_number_overlap{
  max-width:50px;
}
.hotel-cheapest-filter .checkWrapper input[type=checkbox]{
  margin-left: 0;
}
.hotel-cheapest-filter .checkWrapper label{
  width: 89.5%;
}
@media (max-width: 991px) and (min-width: 768px) {
  .hotel-cheapest-filter .checkWrapper label{
    width: 79.5%;
  }
}
.hotel-cheapest-filter .checkWrapper{
  padding: 0;
  border-bottom: 0;
  margin-bottom: 0;
}
</style>
<?php themeFunctions::debugFileLine('end'); ?>