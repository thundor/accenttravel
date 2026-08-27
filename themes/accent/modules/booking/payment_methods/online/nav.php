<?php defined('ENVIRONMENT') OR die('Invalid access'); ?>
<?php themeFunctions::debugFileLine('start'); ?>
<?php themeFunctions::loadAddons(__FILE__); ?>
<li class="nav-item">
    <a class="nav-link<?php echo isset($active) && $active ? ' active' : ''?>" href="#tab_payment_method_online" role="tab" data-toggle="tab"><i class="fa fa-credit-card"></i> ON-LINE</a>
</li>
<?php themeFunctions::debugFileLine('end'); ?>