<?php defined('ENVIRONMENT') OR die('Invalid access'); ?>
<?php themeFunctions::debugFileLine('start'); ?>
<?php themeFunctions::loadAddons(__FILE__); ?>
<?php themeFunctions::addIncludePath('includes/body/scripts.php', __DIR__ . '/scripts.php'); ?>
<?php 
$this->_ci->load->model('Options_model');
$text = $this->_ci->Options_model->get('payment_methods_settings','agency_text');
?>
<div class="tab-pane<?php echo isset($active) && $active ? ' active' : ''?>"  role="tabpanel" id="tab_payment_method_agentie">
  <?php echo $text; ?>
  <div class="form-group">
    <div class="custom-controls-stacked d-block">
      <label class="custom-control custom-radio">
        <input form="bookingCheckout" id="payment_method_agency" type="radio" name="payment_method" value="agency" class="custom-control-input" required>
        <span class="custom-control-indicator"></span>
        <span class="custom-control-description">Doresc sa platesc la agentie</span>
      </label>
    </div>
  </div>
</div>
<?php themeFunctions::debugFileLine('end'); ?>