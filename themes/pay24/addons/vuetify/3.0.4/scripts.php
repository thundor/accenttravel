<?php defined('ENVIRONMENT') OR die('Invalid access'); ?>
<?php themeFunctions::debugFileLine('start'); ?>
<?php // https://cdn.jsdelivr.net/npm/vuetify@3.0.4/dist/vuetify.prod.js ?>
<script type="text/javascript" src="<?php echo $this->theme_url; ?>assets/plugins/vuetify/3.0.4/js/vuetify.js"></script>
<?php themeFunctions::debugFileLine('end'); ?>
<?php themeFunctions::loadAddons(__FILE__); ?>