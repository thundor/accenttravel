<?php defined('ENVIRONMENT') OR die('Invalid access'); ?>
<?php themeFunctions::debugFileLine('start'); ?>
<?php themeFunctions::loadLang('general/options'); ?>
<?php themeFunctions::includeAddon('forms-validation'); ?>
<?php themeFunctions::addIncludePath('includes/body/scripts.php', __DIR__ . '/discounts_general/scripts.php'); ?>
<?php themeFunctions::addIncludePath('modules/head/meta.php', __DIR__ . '/discounts_general/meta.php'); ?>
<?php themeFunctions::addIncludePath('layouts/backend/default/headbar/page_actions.php', __DIR__ . '/discounts_general/page_actions.php'); ?>
<?php themeFunctions::addIncludePath('modules/backend/headbar/page_title.php', __DIR__ . '/discounts_general/page_title.php'); ?>
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
?>
<section class="forms">
  <div class="col-12">
    <div class="row">
      <div class="col-lg-4">
        <div class="card">
          <div class="card-header d-flex align-items-center">
            <h2 class="h5 display">Setari discount general <strong>TRIP</strong></h2>
          </div>
          <div class="card-block">
            <form id="tripForm" name="tripForm" class="trip_form" action="<?php echo base_url('backend/trip/discounts/general_save'); ?>" method="POST">
              <?php if($this->_ci->config->item('csrf_protection') === TRUE){ ?>
              <input type="hidden" id="csrfToken" name="<?php echo htmlspecialchars($this->_ci->security->get_csrf_token_name()); ?>" value="<?php echo htmlspecialchars($this->_ci->security->get_csrf_hash()); ?>" />
              <?php } ?>
              <div class="form-group row">
                <label class="<?php echo $label_class; ?>">Vacante</label>
                <div class="<?php echo $value_class; ?>">
                  <div class="input-group">
                    <input type="number" min="0" max="100" step="0.01" name="trip_discount_package" placeholder="Discount Vacante" class="form-control" value="<?php echo htmlspecialchars($data['trip_discount_package']); ?>" />
                    <span class="input-group-addon">%</span>
                  </div>
                </div>
              </div>
            </form>
            <div id="result_tripForm" class="form-group" ></div>
            <p>Discounturile generale sunt invizibile utilizatorului.</p>
          </div>
        </div>
      </div>
    </div>
  </div>
</section>
<?php themeFunctions::loadAddons(__FILE__); ?>
<?php themeFunctions::debugFileLine('end'); ?>