<?php defined('ENVIRONMENT') OR die('Invalid access'); ?>
<?php themeFunctions::debugFileLine('start'); ?>
<script type="text/javascript">
$(window).scroll(function () {
    if ($(this).scrollTop() > 450) {
      $('#back-to-top').fadeIn();
    } else {
      $('#back-to-top').fadeOut();
    }
  });
  // scroll body to 0px on click
  $('#back-to-top').on("click", function () {
    $('#back-to-top').tooltip('hide');
    $('body,html').stop().animate({scrollTop:0}, 200, 'swing', function() { 
    });
    return false;
  });
</script>
<?php themeFunctions::debugFileLine('end'); ?>
<?php themeFunctions::loadAddons(__FILE__); ?>