<?php defined('ENVIRONMENT') OR die('Invalid access'); ?>
<?php themeFunctions::debugFileLine('start'); ?>
<?php if($this->_ci->user->can('backend-config-save')){ ?>
<li class="nav-item">
  <button type="submit" form="offers_recommended_form" class="btn btn-success">
    <i class="fa fa-save"></i> Salvare
  </button>
</li>
<?php } ?>
<?php themeFunctions::loadAddons(__FILE__); ?>
<?php themeFunctions::debugFileLine('end'); ?>