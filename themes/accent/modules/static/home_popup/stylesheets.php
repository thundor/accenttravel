<?php defined('ENVIRONMENT') OR die('Invalid access'); ?>
<?php themeFunctions::loadAddons(__FILE__); ?>
<?php themeFunctions::debugFileLine('start'); ?>
<style type="text/css">
#modal_home_popup button.close{
  position: absolute;
  right: -20px;
  top: -20px;
  border: 1px solid #000;
  border-radius: 50%;
  width: 40px;
  height: 40px;
  background: #fff;
}
#modal_home_popup .modal-dialog{
  max-height: 90%;
}
</style>
<?php themeFunctions::debugFileLine('end'); ?>