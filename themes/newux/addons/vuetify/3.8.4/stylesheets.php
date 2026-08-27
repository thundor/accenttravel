<?php defined('ENVIRONMENT') OR die('Invalid access'); ?>
<?php themeFunctions::loadAddons(__FILE__); ?>
<?php themeFunctions::debugFileLine('start'); ?>
<?php // https://cdn.jsdelivr.net/npm/vuetify@3.0.4/dist/vuetify.css ?>
<link type="text/css" rel="stylesheet" href="<?php echo $this->theme_url; ?>assets/plugins/vuetify/<?php echo basename(__DIR__)?>/dist/vuetify.min.css" />
<?php themeFunctions::debugFileLine('end'); ?>