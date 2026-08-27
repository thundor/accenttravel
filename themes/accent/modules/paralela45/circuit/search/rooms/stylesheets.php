<?php defined('ENVIRONMENT') OR die('Invalid access'); ?>
<?php themeFunctions::loadAddons(__FILE__); ?>
<?php themeFunctions::debugFileLine('start'); ?>
<style type="text/css">
#paralela45-circuit-loading-screen{
  position:fixed;
  width:100vw;
  height:100vh;
  top:0;
  left:0;
  display:table;
  background-color: rgba(255,255,255,0.2);
  z-index: 9999;
  opacity:1;
}
#paralela45-circuit-loading-screen > div{
  text-align:center;
  vertical-align:middle;
  display: table-cell;
  font-size:50px;
  line-height:50px;
}
#paralela45-circuit-loading-screen.inactive{
  pointer-events:none;
  opacity:0;
}
</style>
<?php themeFunctions::debugFileLine('end'); ?>