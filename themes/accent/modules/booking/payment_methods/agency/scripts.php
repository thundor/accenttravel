<?php defined('ENVIRONMENT') OR die('Invalid access'); ?>
<?php themeFunctions::debugFileLine('start'); ?>
<script type="text/javascript">
$("#hartaBalcescu").on("click", function(e){
  $('#showMapBalcescu').show("fade");
  return false;
});
$("#hartaBravu").on("click", function(e){
  $('#showMapBravu').show("fade");
  return false;
});
$("#hartaPh").on("click", function(e){
  $('#showMapPh').show("fade");
  return false;
});
var browserWidth =  $(window).width();
$("#showMapBalcescu, #showMapBravu, #showMapPh").css("width", browserWidth -(browserWidth/2));
$("#showMapBalcescu i.fa-close").on("click", function(){
  $('#showMapBalcescu').hide();
});
$("#showMapBravu i.fa-close").on("click", function(){
  $('#showMapBravu').hide();
});
$("#showMapPh i.fa-close").on("click", function(){
  $('#showMapPh').hide();
});
</script>
<?php themeFunctions::loadAddons(__FILE__); ?>
<?php themeFunctions::debugFileLine('end'); ?>