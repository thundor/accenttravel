<?php defined('ENVIRONMENT') OR die('Invalid access'); ?>
<?php themeFunctions::debugFileLine('start'); ?>
<?php themeFunctions::loadAddons(__FILE__); ?>
<?php 
$this->_ci->load->model('Options_model');
$text = $this->_ci->Options_model->get('payment_methods_settings','online_text');
?>
<div class="tab-pane <?php echo isset($active) && $active ? 'active' : ''?>"  role="tabpanel" id="tab_payment_method_online">
  <?php echo $text; ?>
  <div class="form-group">
    <div class="custom-controls-stacked d-block">
      <label class="custom-control custom-radio">
        <input form="bookingCheckout" id="payment_method_online" type="radio" name="payment_method" value="online" class="custom-control-input" required <?php echo isset($active) && $active ? ' checked' : ''?>>
        <span class="custom-control-indicator"></span>
        <span class="custom-control-description">Doresc sa platesc online</span>
      </label>
    </div>
  </div>
  <?php 
  $loaded = false;
  foreach($this->available_payment_gateways as $payment_gateway){ 
    $loaded = themeFunctions::loadModule('payment_gateways/' . $payment_gateway . '/checkout',__FILE__ . '/payment_gateways', array('nav'=>'/nav', 'content'=>'/content', 'active'=>!$loaded));
  } ?>
  <div id="payment_gateways" class="collapse <?php echo isset($active) && $active ? ' show' : ''?>">
    <div class="card mb-3">
      <div class="card-header pt-1 pr-3 pl-3">
        <ul class="nav nav-tabs card-header-tabs nav-justified">
          <?php themeFunctions::loadAddons(__FILE__ . '/payment_gateways/nav'); ?>
        </ul>
      </div>
      <div class="tab-content card-block p-1">
        <?php themeFunctions::loadAddons(__FILE__ . '/payment_gateways/content'); ?>
      </div>
    </div>
  </div>
</div>
<?php themeFunctions::debugFileLine('end'); ?>