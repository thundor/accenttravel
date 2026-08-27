<?php defined('ENVIRONMENT') OR die('Invalid access'); ?>
<?php themeFunctions::loadAddons(__FILE__); ?>
<?php themeFunctions::debugFileLine('start'); ?>
<?php themeFunctions::includeAddon('lazy-loading'); ?>
<?php themeFunctions::addIncludePath('modules/head/meta.php', __DIR__ . '/index/meta.php'); ?>
<div class="fullWidth bg-home-slider">
  <div class="container">
    <?php include 'index/search.php'; ?>
  </div>
</div>
<div id="hotelNWarnings" style="display:none;">
  <div class="container">
    <div class="row">
      <div class="col-12">
        <hr />
        <h2 class="flight-warning text-center">Alegeti destinatia, data si insotitorii, apoi dati click pe butonul de cautare</h2>
        <hr />
      </div>
    </div>
  </div>
</div>
<div id="hotelWarnings" style="display:none;">
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
<div id="hotelsResultsWrapper" class="container" style="display:none;">
  <div class="row">
    <div class="col-sm-12 col-md-3 mb-4">
      <?php include 'index/filters.php'; ?>
    </div>
    <div class="col-sm-12 col-md-9 mb-4">
      <?php include 'index/results.php'; ?>
    </div>
  </div>
</div>
<?php themeFunctions::debugFileLine('end'); ?>