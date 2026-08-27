<?php defined('ENVIRONMENT') OR die('Invalid access'); ?>
<?php themeFunctions::debugFileLine('start'); ?>
<script type="text/javascript" src="<?php echo $this->theme_url; ?>assets/plugins/bootstrap/4.0.0.alpha/js/bootstrap.min.js"></script>
<script>
/*** Handle jQuery plugin naming conflict between jQuery UI and Bootstrap ***/
var bootstrapButton = $.fn.button.noConflict()
$.fn.bootstrap_button = bootstrapButton;
</script>
<?php themeFunctions::loadAddons(__FILE__); ?>
<?php themeFunctions::debugFileLine('end'); ?>