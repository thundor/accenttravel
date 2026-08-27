<?php defined('ENVIRONMENT') OR die('Invalid access'); ?>
<?php themeFunctions::debugFileLine('start'); ?>
<?php themeFunctions::loadLang('general/options'); ?>
<?php themeFunctions::includeAddon('forms-validation'); ?>
<?php themeFunctions::includeAddon('select2'); ?>
<?php themeFunctions::addIncludePath('includes/body/scripts.php', __DIR__ . '/weekend/scripts.php'); ?>
<?php themeFunctions::addIncludePath('modules/head/meta.php', __DIR__ . '/weekend/meta.php'); ?>
<?php themeFunctions::addIncludePath('layouts/backend/default/headbar/page_actions.php', __DIR__ . '/weekend/page_actions.php'); ?>
<?php themeFunctions::addIncludePath('modules/backend/headbar/page_title.php', __DIR__ . '/weekend/page_title.php'); ?>
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
?>
<section class="forms">
  <form id="offers_weekend_form" name="offers_weekend_form" class="offers_settings" action="<?php echo site_url('backend/offers/weekend/save'); ?>" method="POST">
    <?php if($this->_ci->config->item('csrf_protection') === TRUE){ ?>
    <input type="hidden" id="csrfToken" name="<?php echo htmlspecialchars($this->_ci->security->get_csrf_token_name()); ?>" value="<?php echo htmlspecialchars($this->_ci->security->get_csrf_hash()); ?>" />
    <?php } ?>
    <div id="offers_delete" style="display:none">
      <?php if(isset($data['delete'])){
        foreach($data['delete'] as $delete_id){ ?>
        <input type="hidden" name="zone[delete][]" value="<?php echo $delete_id; ?>" />
        <?php
        }
      }?>
    </div>
    <div class="col-12">
      <div class="row">
        <div class="col-lg-12">
          <h2 class="h5 display">Oferte (Hoteluri / Vacante)</h2>
          <div class="input-group">
            <input type="text" id="weekend_add_hotel_id" placeholder="ID hotel" class="form-control" />
            <span class="input-group-btn">
              <button type="button" id="weekend_add_hotel" class="btn btn-success"><i class="fa fa-plus"></i> Adauga</button>
            </span>
            <input type="text" id="weekend_add_package_id" placeholder="ID vacanta" class="form-control" />
            <span class="input-group-btn">
              <button type="button" id="weekend_add_package" class="btn btn-success"><i class="fa fa-plus"></i> Adauga</button>
            </span>
          </div>
          <div id="weekend_result" class="">
          </div>
          <div id="weekend_results" class="">
          </div>
        </div>
      </div>
    </div>
  </form>
</section>
<div id="offers_weekend_form_models" style="display:none;">
  <div id="zone_model" class="zone-result card mt-1">
    <div class="card-header pt-1 pb-1 pl-2 pr-2 d-flex align-items-center justify-content-between">
      <input type="hidden" name="name" class="zone-name" />
      <a class="collapsed nounderline zone-toggler" data-toggle="collapse" href="#" aria-expanded="false">
        <i class="fa fa-map-o"></i> <span class="zone-name"></span>
      </a>
      <span>
        <div class="input-group">
          <input type="text" name="text" class="form-control" title="Titlu personalizat" />
          <label class="input-group-addon mb-0" title="Activat">
            <input type="hidden"  name="enabled" value="0" />
            <input type="checkbox" value="1" name="enabled" />
          </label>
          <input type="number" min="0" step="1" max="999999" name="zone_ordering" class="form-control" placeholder="Ordine" />
          <div class="input-group-btn">
            <button type="button" class="btn btn-danger remove-card"><i class="fa fa-trash"></i> Sterge</button>
          </div>
        </div>
      </span>
    </div>
    <div class="collapse zone-content-wrapper" role="tabpanel">
      <div class="card-block pl-5 pr-1 pb-1 pt-0 zone-content">
      </div>
    </div>
  </div>
  <div id="hotel_result_model" class="hotel-result card mt-1">
    <div class="card-header pt-1 pb-1 pl-2 pr-2 d-flex align-items-center justify-content-between">
      <input type="hidden" name="hotel_name" class="hotel-name-input" />
      <input type="hidden" name="hotel_stars" class="hotel-stars-input" />
      <span>
        <a target="_BLANK" class="hotel-link nounderline" title="Pagina hotel"><i class="fa fa-external-link"></i></a>
        <span>
          <span class="type-hotel">Hotel </span>
          <span class="hotel-name"></span>
          <span class="hotel-stars"></span>
        </span>
      </span>
      <span>
        <button type="button" class="btn btn-danger remove-card"><i class="fa fa-trash"></i> Sterge</button>
      </span>
    </div>
  </div>
  <div id="package_result_model" class="package-result card mt-1">
    <div class="card-header pt-1 pb-1 pl-2 pr-2 d-flex align-items-center justify-content-between">
      <input type="hidden" name="package_name" class="package-name-input" />
      <input type="hidden" name="package_category" class="package-category-input" />
      <span>
        <a target="_BLANK" class="package-link nounderline" title="Pagina vacanta"><i class="fa fa-external-link"></i></a>
        <span>
          <span class="type-package">Vacanta </span>
          <span class="package-category"></span>
          <span class="package-name"></span>
        </span>
      </span>
      <span>
        <button type="button" class="btn btn-danger remove-card"><i class="fa fa-trash"></i> Sterge</button>
      </span>
    </div>
  </div>
</div>
<?php themeFunctions::loadAddons(__FILE__); ?>
<?php themeFunctions::debugFileLine('end'); ?>