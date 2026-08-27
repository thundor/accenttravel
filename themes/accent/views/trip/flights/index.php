<?php defined('ENVIRONMENT') OR die('Invalid access'); ?>
<?php themeFunctions::loadAddons(__FILE__); ?>
<?php themeFunctions::debugFileLine('start'); ?>
<?php themeFunctions::addIncludePath('modules/head/meta.php', __DIR__ . '/index/meta.php'); ?>
<div class="fullWidth bg-home-slider">
  <div class="container">
    <?php include 'index/search.php'; ?>
  </div>
</div>
<div id="flightWarnings" style="display:none;">
  <div class="container">
    <div class="row">
      <div class="col-12">
        <hr />
        <h2 class="flight-warning text-center">Nu au fost găsite rezultate</h2>
        <hr />
      </div>
    </div>
  </div>
</div>
<div id="flightsResultsWrapper" class="container" style="display:none;">
  <div class="row">
    <div class="col-12">
      <?php include 'index/filters.php'; ?>
      <?php include 'index/results.php'; ?>
    </div>
  </div>
</div>
<?php themeFunctions::debugFileLine('end'); ?>