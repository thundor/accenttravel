<?php defined('ENVIRONMENT') OR die('Invalid access'); ?>
<?php themeFunctions::debugFileLine('start'); ?>
<script>
$(document).ready(function(){
  setTimeout(function(){
    window.location.href="<?php echo site_url(''); ?>";
  },10000);
});
</script>
<?php themeFunctions::debugFileLine('end'); ?>
<?php themeFunctions::loadAddons(__FILE__); ?>