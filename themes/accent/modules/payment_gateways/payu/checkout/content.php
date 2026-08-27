<?php defined('ENVIRONMENT') OR die('Invalid access'); ?>
<?php themeFunctions::debugFileLine('start'); ?>
<?php 
$label_size = array();
$label_size['xl'] = 2;
$label_size['lg'] = 3;
$label_size['md'] = 3;
$label_size['sm'] = 3;
$label_size[''] = 12;
$label_class = '';
$value_class = '';
$value_offset_class = '';
$label_offset_class = '';
foreach($label_size as $k=>$v){
  $label_class .= ' col-' . ($k ? $k . '-' : '') . $v;
  $label_offset_class .= ' offset-' . ($k ? $k . '-' : '') . $v;
  $value_offset_class .= ' offset-' . ($k ? $k . '-' : '') . ($v < 12 ? 12-$v : 12);
  $value_class .= ' col-' . ($k ? $k . '-' : '') . ($v < 12 ? 12-$v : 12);
}
$this->_ci->load->model('Options_model');
$payment_methods = $this->_ci->Options_model->getKeys('payu_payment_methods');
if(!$payment_methods || !is_array($payment_methods)){
  $payment_methods = array();
}
?>
<div class="tab-pane<?php echo isset($active) && $active ? ' active' : ''?>"  role="tabpanel" id="tab_payment_gateway_payu">
  <div class="form-group row ml-0 mt-1 mb-0">
    <label for="payu_payment_method" class="<?php echo $label_class; ?> text-center pt-1">Optiuni plata</label>
    <div class="<?php echo $value_class; ?>">
      <select form="bookingCheckout" id="payu_payment_method" name="payu_payment_method" class="form-control">
        <?php foreach($payment_methods as $pmk => $payment_method){ ?>
          <option value="<?php echo htmlspecialchars($payment_method); ?>"><?php echo htmlspecialchars(lang($payment_method)); ?></option>
        <?php } ?>
      </select>
    </div>
  </div>
  <div class="form-group row ml-0 mt-1 mb-0">
    <div class="<?php echo $value_class . $label_offset_class; ?>">
      <div class="custom-controls-stacked d-block">
        <label class="custom-control custom-radio">
          <input form="bookingCheckout" id="payment_gateway_payu" type="radio" name="payment_gateway" value="payu" class="custom-control-input" <?php echo isset($active) && $active ? ' checked' : ''?>>
          <span class="custom-control-indicator"></span>
          <span class="custom-control-description">Doresc sa platesc prin PayU</span>
        </label>
      </div>
    </div>
  </div>
</div>
<?php themeFunctions::loadAddons(__FILE__); ?>
<?php themeFunctions::debugFileLine('end'); ?>