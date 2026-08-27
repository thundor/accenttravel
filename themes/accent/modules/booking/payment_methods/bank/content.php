<?php defined('ENVIRONMENT') OR die('Invalid access'); ?>
<?php themeFunctions::debugFileLine('start'); ?>
<?php themeFunctions::loadAddons(__FILE__); ?>
<?php 
$this->_ci->load->model('Options_model');
$text = $this->_ci->Options_model->get('payment_methods_settings','bank_text');
?>
<div class="tab-pane <?php echo isset($active) && $active ? 'active' : ''?>"  role="tabpanel" id="tab_payment_method_banca">
  <?php echo $text; ?>
  <div class="form-group">
    <div class="custom-controls-stacked d-block">
      <label class="custom-control custom-radio">
        <input form="bookingCheckout" id="payment_method_bank" type="radio" name="payment_method" value="bank" class="custom-control-input" required>
        <span class="custom-control-indicator"></span>
        <span class="custom-control-description">Doresc sa platesc prin transfer bancar</span>
      </label>
    </div>
  </div>
</div>
<?php themeFunctions::debugFileLine('end'); ?>