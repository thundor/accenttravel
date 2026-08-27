<?php defined('ENVIRONMENT') OR die('Invalid access'); ?>
<?php themeFunctions::debugFileLine('start'); ?>
<div class="hotel-filter hotel-locations-filter">
  <h5 class="subTitleFilter"><i class="fa fa-map-o"></i> Locatie</h5>
  <div class="hotel-filters-content">
<?php /*
<div class="radioWrapper">
  <input type="radio" id="closeToCenter" name="location" />
  <label for="closeToCenter">Aproape de centru (17)</label>
</div>

<div class="radioWrapper">
  <input type="radio" id="closeToStation" name="location" />
  <label for="closeToStation">Aproape de gara (34)</label>
</div>

<div class="radioWrapper">
  <input type="radio" id="closeToAirport" name="location" />
  <label for="closeToAirport">Aproape de aeroport (29)</label>
</div>
*/ ?>
  </div>
</div>
<?php themeFunctions::loadAddons(__FILE__); ?>
<?php themeFunctions::debugFileLine('end'); ?>