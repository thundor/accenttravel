<?php defined('ENVIRONMENT') OR die('Invalid access'); ?>
<?php themeFunctions::debugFileLine('start'); ?>
<script type="text/javascript" src="<?php echo $this->theme_url; ?>assets/js/custom-jquery.js?v=1.0.0"></script>
<script type="text/javascript" src="<?php echo $this->theme_url; ?>assets/js/javascript.js?v=1.0.3"></script>
<?php themeFunctions::loadAddons(__FILE__); ?>
<?php themeFunctions::debugFileLine('end'); ?>