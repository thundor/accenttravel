<?php defined('ENVIRONMENT') OR die('Invalid access'); ?>
<?php themeFunctions::debugFileLine('start'); ?>
<script type="text/javascript">
$(".cookieInfo").delay(1000).fadeIn(500, function(){
  bottom_offset += $(".cookieInfo").outerHeight();
});
$(".cookieInfo .btn").on("click", function(e){
  bottom_offset -= $(".cookieInfo").outerHeight();
  $(".cookieInfo").fadeOut(1000);
  $.ajax({
    url: "<?php echo site_url('account/enable_cookies'); ?>",
  });
});  
</script>
<?php themeFunctions::debugFileLine('end'); ?>
<?php themeFunctions::loadAddons(__FILE__); ?>