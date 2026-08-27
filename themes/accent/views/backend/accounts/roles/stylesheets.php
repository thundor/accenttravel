<?php defined('ENVIRONMENT') OR die('Invalid access'); ?>
<?php themeFunctions::loadAddons(__FILE__); ?>
<?php themeFunctions::debugFileLine('start'); ?>
<style type="text/css">
#rolesForm .list-group{
  width:100%;
}
#rolesForm .list-group.root-item > .list-group-item{
  border-left-width:0;
  padding-left:0;
  border-bottom: 0;
}
#rolesForm .list-group-item{
  border-radius:0 !important;
  border-right-width:0;
  border-top-width:0;
  padding-top:0px;
  padding-bottom:0px;
  padding-left:10px;
  padding-right:0;
}
#rolesForm .list-group-item:hover {
  background-color: rgba(0,0,0,0.05);
}
</style>
<?php themeFunctions::debugFileLine('end'); ?>