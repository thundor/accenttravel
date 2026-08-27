<?php defined('ENVIRONMENT') OR die('Invalid access'); ?>
<?php themeFunctions::loadAddons(__FILE__); ?>
<?php themeFunctions::debugFileLine('start'); ?>
<style type="text/css">
.selected_date_end,
.selected_date_start {
  text-transform: capitalize;
}
#modalMapH{
  position:fixed !important;
  height:523px;
}
.hotel-image{
  display: inline-block;
  height: 1px;
  width: 100%;
  padding-bottom: 50%;
  overflow: hidden;
}
.show-rest{
  padding-left: 4px;
}
.show-rest::before{
  content:'...';
  color: #999;
}
#citybreaks-loading-screen{
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
#citybreaks-loading-screen > div{
  text-align:center;
  vertical-align:middle;
  display: table-cell;
  font-size:50px;
  line-height:50px;
}
#citybreaks-loading-screen.inactive{
  pointer-events:none;
  opacity:0;
}
li.hotelshowmore.closed>a:last-child{
  display:none;
}
li.hotelshowmore:not(.closed)>a:first-child{
  display:none;
}
li.hotelshowmore.closed~li{
  display:none;
}
</style>
<?php themeFunctions::debugFileLine('end'); ?>