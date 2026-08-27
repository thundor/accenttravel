<?php defined('ENVIRONMENT') OR die('Invalid access'); ?>
<?php themeFunctions::debugFileLine('start'); ?>
<script type="text/javascript">
checkSearchModuleBrowserWidth = function(){
  var browserWidth = $(window).width();
  if (browserWidth < 480) {
    $(".searchMenu1 > ul").addClass('flex-column');
  } else {
    $(".searchMenu1 > ul").removeClass('flex-column');
  }
}
$(window).bind('resize', function(){
  checkSearchModuleBrowserWidth();
});
checkSearchModuleBrowserWidth();
$(function(){
  var hash = window.location.hash;
  $target_zone = $('.searchMenu1');
  hash && $('>ul.nav.nav-tabs>li>a[href="' + hash + '"]', $target_zone).tab('show');
  $('>ul.nav.nav-tabs>li>a', $target_zone).click(function (e) {
    $(this).tab('show');
    var scrollmem = $('body').scrollTop() || $('html').scrollTop();
    window.location.hash = this.hash;
    $('html,body').scrollTop(scrollmem);
  });
});
</script>
<?php themeFunctions::debugFileLine('end'); ?>
<?php themeFunctions::loadAddons(__FILE__); ?>