<?php defined('ENVIRONMENT') OR die('Invalid access'); ?>
<?php themeFunctions::debugFileLine('start'); ?>
<?php themeFunctions::includeAddon('forms-validation'); ?>
<?php themeFunctions::addIncludePath('includes/body/scripts.php', __DIR__ . '/citybreak_settings/scripts.php'); ?>
<?php themeFunctions::addIncludePath('layouts/backend/default/headbar/page_actions.php', __DIR__ . '/citybreak_settings/page_actions.php'); ?>
<?php themeFunctions::addIncludePath('modules/backend/headbar/page_title.php', __DIR__ . '/citybreak_settings/page_title.php'); ?>
<?php themeFunctions::addIncludePath('modules/head/meta.php', __DIR__ . '/citybreak_settings/meta.php'); ?>
<section class="forms">
  <div class="col-12">
    <div id="result_citybreakSettingsForm" class="form-group" ></div>
    <div class="row">
      <div class="col-lg-6">
        <div class="card">
          <div class="card-header d-flex align-items-center">
            <h2 class="h5 display display">Locatii <strong>plecare</strong></h2>
          </div>
          <div class="card-block">
            <form name="departure_locations" class="citybreak_settings">
              <?php require 'citybreak_settings/fields.php'; ?>
            </form>
          </div>
        </div>
      </div>
      <div class="col-lg-6">
        <div class="card">
          <div class="card-header d-flex align-items-center">
            <h2 class="h5 display display">Locatii <strong>sosire</strong></h2>
          </div>
          <div class="card-block">
            <form name="arival_locations" class="citybreak_settings">
              <?php require 'citybreak_settings/fields.php'; ?>
            </form>
          </div>
        </div>
      </div>
    </div>
  </div>
</section>
<?php themeFunctions::loadAddons(__FILE__); ?>
<?php themeFunctions::debugFileLine('end'); ?>