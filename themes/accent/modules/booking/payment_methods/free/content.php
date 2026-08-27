<?php defined('ENVIRONMENT') OR die('Invalid access'); ?>
<?php themeFunctions::debugFileLine('start'); ?>
<?php themeFunctions::loadAddons(__FILE__); ?>
<div class="tab-pane"  role="tabpanel" id="tab_payment_method_free">
  <div class="form-group">
    <div class="custom-controls-stacked d-block">
      <label class="custom-control custom-radio">
        <input form="bookingCheckout" id="payment_method_free" disabled type="radio" name="payment_method" value="free" class="custom-control-input">
        <span class="custom-control-indicator"></span>
        <span class="custom-control-description">Reducerile achita integral rezervarea curenta.</span>
      </label>
    </div>
  </div>
</div>
<?php themeFunctions::debugFileLine('end'); ?>