<?php defined('ENVIRONMENT') OR die('Invalid access'); ?>
<?php themeFunctions::debugFileLine('start'); ?>
<?php // https://cdn.jsdelivr.net/npm/vue@3.2.45/dist/vue.global.prod.js ?>
<script type="text/javascript" src="<?php echo $this->theme_url; ?>assets/plugins/vue/3.2.45/js/vue.js"></script>
<?php themeFunctions::debugFileLine('end'); ?>
<?php themeFunctions::loadAddons(__FILE__); ?>