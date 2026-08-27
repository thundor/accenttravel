<?php defined('ENVIRONMENT') OR die('Invalid access'); ?>
<?php themeFunctions::debugFileLine('start'); ?>
<?php themeFunctions::loadAddons(__FILE__); ?>
<li class="nav-item">
  <a class="nav-link<?php echo isset($active) && $active ? ' active' : ''?> hasTooltip" data-toggle="tab" href="#tab_payment_gateway_pay24" role="tab" aria-controls="tab_payment_gateway_pay24">
    <strong><img src="<?php echo $this->theme_url;?>assets/images/payment-gateways/Pay24-icon.png" alt="24Pay" title="Plata prin 24Pay" style="display:inline-block; vertical-align:middle;"/></strong>
  </a>
</li>
<?php themeFunctions::debugFileLine('end'); ?>