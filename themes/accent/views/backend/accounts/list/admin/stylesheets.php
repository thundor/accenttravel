<?php defined('ENVIRONMENT') OR die('Invalid access'); ?>
<?php themeFunctions::loadAddons(__FILE__); ?>
<?php themeFunctions::debugFileLine('start'); ?>
<style type="text/css">
#ordering{
  display:table;
  width:100%;
}
#ordering > .btn {
  float:left;
  margin-right: 5px;
  margin-bottom: 5px;
}
#ordering .sortable-handle {
  cursor:move;
}
</style>
<?php themeFunctions::debugFileLine('end'); ?>