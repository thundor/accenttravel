<?php defined('ENVIRONMENT') OR die('Invalid access'); ?>
<?php themeFunctions::debugFileLine('start'); ?>
<?php themeFunctions::loadModule('paralela45/strainatate/filters/name',__FILE__); ?>
<?php themeFunctions::loadModule('paralela45/strainatate/filters/cheapest',__FILE__); ?>
<?php themeFunctions::loadModule('paralela45/strainatate/filters/price',__FILE__); ?>
<?php themeFunctions::loadModule('paralela45/strainatate/filters/stars',__FILE__); ?>
<?php themeFunctions::loadModule('paralela45/strainatate/filters/availability',__FILE__); ?>
<?php themeFunctions::loadModule('paralela45/strainatate/filters/cities',__FILE__); ?>
<?php //themeFunctions::loadModule('paralela45/strainatate/filters/service_type',__FILE__); ?>
<?php //themeFunctions::loadModule('paralela45/strainatate/filters/meal_type',__FILE__); ?>
<?php //themeFunctions::loadModule('paralela45/strainatate/filters/room_type',__FILE__); ?>
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
<?php themeFunctions::debugFileLine('end'); ?>