<?php defined('ENVIRONMENT') OR die('Invalid access'); ?>
<?php themeFunctions::loadAddons(__FILE__); ?>
<?php themeFunctions::debugFileLine('start'); ?>
<?php $paralela45_special_layout = $this->_ci->uri->segment(0) === 'paralela45'; ?>
<style type="text/css">
#paralela45-strainatate-loading-screen{
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
#paralela45-strainatate-loading-screen > div{
  text-align:center;
  vertical-align:middle;
  display: table-cell;
  font-size:50px;
  line-height:50px;
}
#paralela45-strainatate-loading-screen.inactive{
  pointer-events:none;
  opacity:0;
}
.custom-spinner-wrapper{
  display: inline-block;
  background: #fff;
  padding: 23px;
  position: relative;
  pointer-events: none;
  border: 1px solid #0275d8;
}
<?php if($paralela45_special_layout){ ?>
.searchMenu1 .nav-tabs li, .searchMenuHotel, .nav-tabs li{
  text-align:left;
}

/* .select2_4-all-options{
  width:100%;
  height:20px;
} */
.select2_4-all-options > strong{
  /* position: absolute;
  bottom: 0;
  left: 0;
  right: 0;
  height: 33px;
  line-height: 25px;
  background-color: #fff;
  font-size: 100%;
  color: #000; */
  font-weight: bold;
  padding:0;
}
/* .select2_4-all-options > strong:hover{
  background-color: #34649D;
  color: #fff;
} */
<?php } ?>
</style>
<?php themeFunctions::debugFileLine('end'); ?>