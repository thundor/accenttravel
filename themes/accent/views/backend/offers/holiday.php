<?php defined('ENVIRONMENT') OR die('Invalid access'); ?>
<?php themeFunctions::debugFileLine('start'); ?>
<?php themeFunctions::loadLang('general/options'); ?>
<?php themeFunctions::includeAddon('forms-validation'); ?>
<?php themeFunctions::includeAddon('select2'); ?>
<?php themeFunctions::addIncludePath('includes/body/scripts.php', __DIR__ . '/holiday/scripts.php'); ?>
<?php themeFunctions::addIncludePath('modules/head/meta.php', __DIR__ . '/holiday/meta.php'); ?>
<?php themeFunctions::addIncludePath('layouts/backend/default/headbar/page_actions.php', __DIR__ . '/holiday/page_actions.php'); ?>
<?php themeFunctions::addIncludePath('modules/backend/headbar/page_title.php', __DIR__ . '/holiday/page_title.php'); ?>
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
?>
<section class="forms">
  <form id="offers_holiday_form" name="offers_holiday_form" class="offers_settings" action="<?php echo site_url('backend/offers/holiday/save'); ?>" method="POST" enctype="multipart/form-data">
    <?php if($this->_ci->config->item('csrf_protection') === TRUE){ ?>
    <input type="hidden" id="csrfToken" name="<?php echo htmlspecialchars($this->_ci->security->get_csrf_token_name()); ?>" value="<?php echo htmlspecialchars($this->_ci->security->get_csrf_hash()); ?>" />
    <?php } ?>
    <div class="col-12">
      <div class="row offers-sortable">
        <?php for($zone = 1; $zone <= $zones; $zone++){ ?>
        <div class="col-lg-4 mb-3 offers-sortable-item">
          <div class="card active-zone">
            <div class="card-header d-flex align-items-center justify-content-between">
              <h2 class="h5 display"><span class="btn btn-info move-offer"><i class="fa fa-arrows"></i></span> Zona <strong><?php echo $zone; ?></strong></h2>
              <button type="button" class="holiday-remove-zone btn btn-danger"><i class="fa fa-trash"></i> Sterge zona</button>
            </div>
            <div class="card-block">
              <?php require 'holiday/fields.php'; ?>
            </div>
          </div>
        </div>
        <?php } ?>
        <div class="col-lg-4 mb-3">
          <button type="button" id="holiday_add_zone" class="btn btn-success"><i class="fa fa-plus"></i> Adauga zona</button>
        </div>
      </div>
    </div>
  </form>
</section>
<div id="offers_holiday_form_models" style="display:none;">
  <?php $zone = 0; ?>
  <div class="col-lg-4 mb-3 offers-sortable-item">
    <div class="card">
      <div class="card-header d-flex align-items-center justify-content-between">
        <h2 class="h5 display">Zona <strong><?php echo $zone; ?></strong></h2>
        <button type="button" class="holiday-remove-zone btn btn-danger"><i class="fa fa-trash"></i> Sterge zona</button>
      </div>
      <div class="card-block">
        <?php require 'holiday/fields.php'; ?>
      </div>
    </div>
  </div>
</div>
<?php themeFunctions::loadAddons(__FILE__); ?>
<?php themeFunctions::debugFileLine('end'); ?>