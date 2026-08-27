<?php defined('ENVIRONMENT') OR die('Invalid access'); ?>
<?php themeFunctions::loadAddons(__FILE__); ?>
<?php themeFunctions::debugFileLine('start'); ?>
<style type="text/css">
.carousel-item > div{
  width:100%;
  padding-bottom: 50%;
  min-height:400px;
  background-position: center;
  background-size: cover;
}
.btn-primary.disabled, .btn-primary:disabled {
  background-color: #b0cae0;
  border-color: #b0cae0;
}
</style>
<?php themeFunctions::debugFileLine('end'); ?>