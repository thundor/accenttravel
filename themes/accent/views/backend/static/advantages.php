<?php defined('ENVIRONMENT') OR die('Invalid access'); ?>
<?php themeFunctions::debugFileLine('start'); ?>
<?php themeFunctions::loadLang('general/options'); ?>
<?php themeFunctions::includeAddon('forms-validation'); ?>
<?php themeFunctions::addIncludePath('includes/body/scripts.php', __DIR__ . '/advantages/scripts.php'); ?>
<?php themeFunctions::addIncludePath('modules/head/meta.php', __DIR__ . '/advantages/meta.php'); ?>
<?php themeFunctions::addIncludePath('layouts/backend/default/headbar/page_actions.php', __DIR__ . '/advantages/page_actions.php'); ?>
<?php themeFunctions::addIncludePath('modules/backend/headbar/page_title.php', __DIR__ . '/advantages/page_title.php'); ?>
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
$can_write = $this->_ci->user->can('backend-config-save');
$data = $this->view_data;
$zones = 0;
if(isset($data['status']) && is_array($data['status'])){
  $zones = count($data['status']);
}
?>
<section class="forms">
  <form id="static_advantages_form" name="static_advantages_form" class="static_settings" action="<?php echo site_url('backend/static/advantages/save'); ?>" method="POST" enctype="multipart/form-data">
    <?php if($this->_ci->config->item('csrf_protection') === TRUE){ ?>
    <input type="hidden" id="csrfToken" name="<?php echo htmlspecialchars($this->_ci->security->get_csrf_token_name()); ?>" value="<?php echo htmlspecialchars($this->_ci->security->get_csrf_hash()); ?>" />
    <?php } ?>
    <div class="col-12">
      <div class="row static-sortable">
        <?php for($zone = 1; $zone <= $zones; $zone++){ ?>
        <div class="col-lg-4 mb-3 static-sortable-item">
          <div class="card active-zone">
            <div class="card-header d-flex align-items-center justify-content-between">
              <h2 class="h5 display"><span class="btn btn-info move-offer"><i class="fa fa-arrows"></i></span> Slide <strong><?php echo $zone; ?></strong></h2>
              <button type="button" class="advantages-remove-zone btn btn-danger"><i class="fa fa-trash"></i> Sterge slide</button>
            </div>
            <div class="card-block">
              <?php require 'advantages/fields.php'; ?>
            </div>
          </div>
        </div>
        <?php } ?>
        <div class="col-lg-4 mb-3">
          <button type="button" id="advantages_add_zone" class="btn btn-success"><i class="fa fa-plus"></i> Adauga slide</button>
        </div>
      </div>
    </div>
  </form>
</section>
<div id="static_advantages_form_models" style="display:none;">
  <?php $zone = 0; ?>
  <div class="col-lg-4 mb-3 static-sortable-item">
    <div class="card">
      <div class="card-header d-flex align-items-center justify-content-between">
        <h2 class="h5 display">Slide <strong><?php echo $zone; ?></strong></h2>
        <button type="button" class="advantages-remove-zone btn btn-danger"><i class="fa fa-trash"></i> Sterge slide</button>
      </div>
      <div class="card-block">
        <?php require 'advantages/fields.php'; ?>
      </div>
    </div>
  </div>
</div>
<?php themeFunctions::loadAddons(__FILE__); ?>
<?php themeFunctions::debugFileLine('end'); ?>