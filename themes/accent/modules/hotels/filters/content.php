<?php defined('ENVIRONMENT') OR die('Invalid access'); ?>
<?php themeFunctions::debugFileLine('start'); ?>
<?php themeFunctions::loadModule('hotels/filters/price',__FILE__); ?>
<?php themeFunctions::loadModule('hotels/filters/stars',__FILE__); ?>
<?php themeFunctions::loadModule('hotels/filters/points-of-interest',__FILE__); ?>
<?php // themeFunctions::loadModule('hotels/filters/location',__FILE__); ?>
<?php themeFunctions::loadModule('hotels/filters/activities',__FILE__); ?>
<?php themeFunctions::loadModule('hotels/filters/facilities',__FILE__); ?>
<h4 class="filterTitle pl-0"><i class="fa fa-filter"></i> Filtre cautare <i class="fa fa-plus-square-o"></i></h4>
<hr />
<div class="clearfix"></div>
<div id="allFilters" class="hiddenFilt">
  <?php themeFunctions::loadAddons(__FILE__); ?>
  <div class="hotel-filters-actions">
    <?php /* <button name="applyFilters" id="applyFilters" class="btn btn-block btn-primary" type="submit">Aplica Filtre</button> */ ?>
    <button name="resetFilters" id="resetFilters" class="btn btn-block btn-warning" type="submit">Sterge Filtre</button>
  </div>
</div>
<?php
if($this->_ci->user->can('backend-access')){ ?>
<input type="text" class="form-control trip_hotel_search_link" readonly />
<?php
}
?>
<?php themeFunctions::debugFileLine('end'); ?>