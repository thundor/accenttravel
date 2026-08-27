<?php defined('ENVIRONMENT') OR die('Invalid access'); ?>
<?php themeFunctions::debugFileLine('start'); ?>
<style type="text/css">
  .dropdown-item.active > a {
    color:inherit;
  }
.form-sec{
	position: absolute;
    width: 1px;
    height: 1px;
    appearance: none;
    border: 0;
    background: transparent;
    pointer-events: none;
}
</style>
<?php themeFunctions::loadAddons(__FILE__); ?>
<?php themeFunctions::debugFileLine('end'); ?>