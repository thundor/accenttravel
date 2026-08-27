<?php defined('ENVIRONMENT') OR die('Invalid access'); ?>
<?php themeFunctions::debugFileLine('start'); ?>
<?php themeFunctions::loadLang('general/options'); ?>
<?php themeFunctions::includeAddon('forms-validation'); ?>
<?php themeFunctions::includeAddon('select2'); ?>
<?php themeFunctions::addIncludePath('includes/body/scripts.php', __DIR__ . '/popular/scripts.php'); ?>
<?php themeFunctions::addIncludePath('modules/head/meta.php', __DIR__ . '/popular/meta.php'); ?>
<?php themeFunctions::addIncludePath('layouts/backend/default/headbar/page_actions.php', __DIR__ . '/popular/page_actions.php'); ?>
<?php themeFunctions::addIncludePath('modules/backend/headbar/page_title.php', __DIR__ . '/popular/page_title.php'); ?>
<?php 
$label_size = array();
$label_size['xl'] = 3;
$label_size['md'] = 3;
$label_size['lg'] = 4;
$label_size['sm'] = 3;
$label_size[''] = 12;
$label_class = '';
$value_class = '';
foreach($label_size as $k=>$v){
  $label_class .= ' col-' . ($k ? $k . '-' : '') . $v;
  $value_class .= ' col-' . ($k ? $k . '-' : '') . ($v < 12 ? 12-$v : 12);
}
$can_write = $this->_ci->user->can('backend-offers-save');
$data = $this->view_data;
$zones = 0;
if(isset($data['status']) && is_array($data['status'])){
  $zones = count($data['status']);
}
$interval = isset($data['interval']) && is_array($data['interval']) ? $data['interval'] : array();
if(!isset($interval['departure'])){
  $interval['departure'] = '';
}
if(!isset($interval['arrival'])){
  $interval['arrival'] = '';
}
?>
<section class="forms">
  <form id="offers_popular_form" name="offers_popular_form" class="offers_settings" action="<?php echo site_url('backend/offers/popular/save'); ?>" method="POST" enctype="multipart/form-data">
    <?php if($this->_ci->config->item('csrf_protection') === TRUE){ ?>
    <input type="hidden" id="csrfToken" name="<?php echo htmlspecialchars($this->_ci->security->get_csrf_token_name()); ?>" value="<?php echo htmlspecialchars($this->_ci->security->get_csrf_hash()); ?>" />
    <?php } ?>
    <div class="col-12">
      <div class="card mb-3">
        <div class="card-header d-flex align-items-center justify-content-between">
          <h2 class="h5 display">Setari generale</h2>
        </div>
        <div class="card-block">
          <div class="row">
            <div class="col-lg-6">
              <?php $zone = 'departure'; ?>
              <div class="form-group row">
                <label for="offers_recommended_zone_<?php echo $zone; ?>_departure" class="<?php echo $label_class; ?>">Plecare (azi + <strong>X zile</strong>):</label>
                <div class="<?php echo $value_class; ?>">
                  <label class="input-group mb-0">
                    <input id="offers_recommended_zone_<?php echo $zone; ?>_departure" name="interval[<?php echo $zone ; ?>]" type="number" min="0" max="99999" step="0.01" placeholder="Zile" class="form-control pr-0 pb-0 pt-0" style="line-height:35px;" value="<?php echo htmlspecialchars($interval['departure']); ?>" />
                    <span class="input-group-addon">Zile</span>
                  </label>
                </div>
              </div>
              <h2 class="h5 display">Plecare din</h2>
              <div id="<?php echo $zone; ?>_locations" class="offer-locations" data-type="<?php echo $zone; ?>">
                <?php require 'popular/general_fields.php'; ?>
              </div>
            </div>
            <div class="col-lg-6">
              <?php $zone = 'arrival'; ?>
              <div class="form-group row">
                <label for="offers_recommended_zone_<?php echo $zone; ?>_return" class="<?php echo $label_class; ?>">Intoarcere (azi + Plecare + <strong>X zile</strong>):</label>
                <div class="<?php echo $value_class; ?>">
                  <label class="input-group mb-0">
                    <input id="offers_recommended_zone_<?php echo $zone; ?>_return" name="interval[<?php echo $zone ; ?>]" type="number" min="0" max="99999" step="0.01" placeholder="Zile" class="form-control pr-0 pb-0 pt-0" style="line-height:35px;" value="<?php echo htmlspecialchars($interval['arrival']); ?>" />
                    <span class="input-group-addon">Zile</span>
                  </label>
                </div>
              </div>
              <h2 class="h5 display">Destinatii</h2>
              <div id="<?php echo $zone; ?>_locations" class="offer-locations" data-type="<?php echo $zone; ?>">
              <?php require 'popular/general_fields.php'; ?>
              </div>
            </div>
          </div>
        </div>
      </div>
      <div class="row offers-sortable">
        <?php for($zone = 1; $zone <= $zones; $zone++){ ?>
        <div class="col-lg-4 mb-3 offers-sortable-item">
          <div class="card active-zone">
            <div class="card-header d-flex align-items-center justify-content-between">
              <h2 class="h5 display"><span class="btn btn-info move-offer"><i class="fa fa-arrows"></i></span> Zona <strong><?php echo $zone; ?></strong></h2>
              <button type="button" class="popular-remove-zone btn btn-danger"><i class="fa fa-trash"></i> Sterge zona</button>
            </div>
            <div class="card-block">
              <?php require 'popular/fields.php'; ?>
            </div>
          </div>
        </div>
        <?php } ?>
        <div class="col-lg-4 mb-3">
          <button type="button" id="popular_add_zone" class="btn btn-success"><i class="fa fa-plus"></i> Adauga zona</button>
        </div>
      </div>
    </div>
  </form>
</section>
<div id="offers_popular_form_models" style="display:none;">
  <?php $zone = 0; ?>
  <div class="col-lg-4 mb-3 offers-sortable-item">
    <div class="card">
      <div class="card-header d-flex align-items-center justify-content-between">
        <h2 class="h5 display">Zona <strong><?php echo $zone; ?></strong></h2>
        <button type="button" class="popular-remove-zone btn btn-danger"><i class="fa fa-trash"></i> Sterge zona</button>
      </div>
      <div class="card-block">
        <?php require 'popular/fields.php'; ?>
      </div>
    </div>
  </div>
</div>
<?php themeFunctions::loadAddons(__FILE__); ?>
<?php themeFunctions::debugFileLine('end'); ?>