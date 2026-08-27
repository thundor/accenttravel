<?php defined('ENVIRONMENT') OR die('Invalid access'); ?>
<?php themeFunctions::debugFileLine('start'); ?>
<script type="text/javascript" src="<?php echo $this->theme_url; ?>assets/plugins/vue-router/4.1.16/js/vue-router.js"></script>
<?php themeFunctions::debugFileLine('end'); ?>
<?php themeFunctions::loadAddons(__FILE__); ?>