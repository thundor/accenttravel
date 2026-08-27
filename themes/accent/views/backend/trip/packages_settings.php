<?php defined('ENVIRONMENT') OR die('Invalid access'); ?>
<?php themeFunctions::debugFileLine('start'); ?>
<?php themeFunctions::includeAddon('select2'); ?>
<?php themeFunctions::includeAddon('forms-validation'); ?>
<?php themeFunctions::addIncludePath('includes/body/scripts.php', __DIR__ . '/packages_settings/scripts.php'); ?>
<?php themeFunctions::addIncludePath('layouts/backend/default/headbar/page_actions.php', __DIR__ . '/packages_settings/page_actions.php'); ?>
<?php themeFunctions::addIncludePath('modules/backend/headbar/page_title.php', __DIR__ . '/packages_settings/page_title.php'); ?>
<?php themeFunctions::addIncludePath('modules/head/meta.php', __DIR__ . '/packages_settings/meta.php'); ?>
<?php 
$data = $this->view_data;

$label_size = array();
$label_size['xl'] = 2;
$label_size['lg'] = 3;
$label_size['md'] = 3;
$label_size['sm'] = 3;
$label_size[''] = 12;
$label_class = '';
$value_class = '';
$value_offset_class = '';
foreach($label_size as $k=>$v){
  $label_class .= ' col-' . ($k ? $k . '-' : '') . $v;
  $value_offset_class .= ' offset-' . ($k ? $k . '-' : '') . ($v < 12 ? 12-$v : 12);
  $value_class .= ' col-' . ($k ? $k . '-' : '') . ($v < 12 ? 12-$v : 12);
}
 ?>
<section class="forms">
  <div class="col-lg-12">
    <div class="card">
      <div class="card-header d-flex align-items-center">
        <h2 class="h5 display display">Setari <strong>Vacante</strong></h2>
      </div>
      <div class="card-block">
        <form id="packages_settings_form" name="packages_settings_form" action="<?php echo site_url('backend/trip/packages/save'); ?>" method="POST">
          <?php if($this->_ci->config->item('csrf_protection') === TRUE){ ?>
          <input type="hidden" name="<?php echo htmlspecialchars($this->_ci->security->get_csrf_token_name()); ?>" value="<?php echo htmlspecialchars($this->_ci->security->get_csrf_hash()); ?>" />
          <?php } ?>
          <div class="form-group row">
            <label for="packages_categories" class="<?php echo $label_class; ?>">Categorii</label>
            <div class="<?php echo $value_class; ?>">
              <label class="input-group mb-0">
                <input id="packages_categories" type="hidden" name="data[categories]" class="" value="<?php echo htmlspecialchars($data['categories']); ?>"/>
              </label>
            </div>
          </div>
          <div class="form-group row">
            <label for="packages_destinations" class="<?php echo $label_class; ?>">Destinatii</label>
            <div class="<?php echo $value_class; ?>">
              <label class="input-group mb-0">
                <input id="packages_destinations" type="hidden" name="data[destinations]" class="" value="<?php echo htmlspecialchars($data['destinations']); ?>"/>
              </label>
            </div>
          </div>
        </form>
      </div>
    </div>
  </div>
</section>
<?php themeFunctions::loadAddons(__FILE__); ?>
<?php themeFunctions::debugFileLine('end'); ?>