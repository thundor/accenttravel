<?php defined('ENVIRONMENT') OR die('Invalid access'); ?>
<?php themeFunctions::debugFileLine('start'); ?>
<script type="text/javascript" src="<?php echo $this->theme_url; ?>assets/plugins/jquery-ui/js/jquery-ui.js"></script>
<script>
/*** Handle jQuery plugin naming conflict between jQuery UI and Bootstrap ***/
$.widget.bridge('uibutton', $.ui.button);
$.widget.bridge('uitooltip', $.ui.tooltip);
</script>
<?php themeFunctions::debugFileLine('end'); ?>
<?php themeFunctions::loadAddons(__FILE__); ?>