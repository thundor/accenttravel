<?php defined('ENVIRONMENT') OR die('Invalid access'); ?>
<?php themeFunctions::debugFileLine('start'); ?>
<script type="text/javascript" src="<?php echo $this->theme_url; ?>assets/js/custom-jquery.js?v=1.0.4"></script>
<script type="text/javascript" src="<?php echo $this->theme_url; ?>assets/js/javascript.js?v=1.0.10"></script>
<script type="text/javascript">
var top_offset = 0;
var bottom_offset = 0;
</script>
<?php themeFunctions::loadAddons(__FILE__); ?>
<?php themeFunctions::debugFileLine('end'); ?>