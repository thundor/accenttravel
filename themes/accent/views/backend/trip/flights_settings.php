<?php defined('ENVIRONMENT') OR die('Invalid access'); ?>
<?php themeFunctions::debugFileLine('start'); ?>
<?php themeFunctions::includeAddon('forms-validation'); ?>
<?php themeFunctions::addIncludePath('includes/body/scripts.php', __DIR__ . '/flights_settings/scripts.php'); ?>
<?php themeFunctions::addIncludePath('layouts/backend/default/headbar/page_actions.php', __DIR__ . '/flights_settings/page_actions.php'); ?>
<?php themeFunctions::addIncludePath('modules/backend/headbar/page_title.php', __DIR__ . '/flights_settings/page_title.php'); ?>
<?php themeFunctions::addIncludePath('modules/head/meta.php', __DIR__ . '/flights_settings/meta.php'); ?>
<?php require 'flights_settings/fields_layout.php'; ?>
<section class="forms">
  <div class="col-12">
    <div id="result_flightsSettingsForm" class="form-group" ></div>
    <div class="row">
      <div class="col-lg-6">
        <div class="card">
          <div class="card-header d-flex align-items-center">
            <h2 class="h5 display">Asigurare <strong>calatorie</strong></h2>
          </div>
          <div class="card-block">
            <form name="travel" class="flights_settings">
              <?php require 'flights_settings/price_table.php'; ?>
            </form>
          </div>
        </div>
      </div>
      <div class="col-lg-6">
        <div class="card">
          <div class="card-header d-flex align-items-center">
            <h2 class="h5 display">Asigurare <strong>storno</strong></h2>
          </div>
          <div class="card-block">
            <form name="storno" class="flights_settings">
              <?php require 'flights_settings/price_table.php'; ?>
            </form>
          </div>
        </div>
      </div>
    </div>
    <?php /*
    <div class="row">
      <div class="col-lg-4">
        <div class="card">
          <div class="card-header d-flex align-items-center">
            <h2 class="h5 display">Taxa <strong>serviciu</strong></h2>
          </div>
          <div class="card-block">
            <form name="service" class="flights_settings">
              <div class="form-group row">
                <label class="<?php echo $label_class; ?>">Pret / pasager</label>
                <div class="<?php echo $value_class; ?>">
                  <div class="input-group">
                    <input type="number" name="price" min="0.00" step=".01" placeholder="Pret / pasager" class="form-control" />
                    <span class="input-group-addon"><?php echo $this->_ci->currency_symbol; ?></span>
                  </div>
                  <small class="text-muted">Max. 2 zecimale</small>
                </div>
              </div>
            </form>
          </div>
        </div>
      </div>
    </div>
    */ ?>
  </div>
</section>
<?php themeFunctions::loadAddons(__FILE__); ?>
<?php themeFunctions::debugFileLine('end'); ?>