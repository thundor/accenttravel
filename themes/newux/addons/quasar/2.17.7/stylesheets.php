<?php defined('ENVIRONMENT') OR die('Invalid access'); ?>
<?php themeFunctions::loadAddons(__FILE__); ?>
<?php themeFunctions::debugFileLine('start'); ?>
<link type="text/css" rel="stylesheet" href="<?php echo $this->theme_url; ?>assets/plugins/quasar/<?php echo basename(__DIR__)?>/dist/quasar.prod.css" />
<?php themeFunctions::debugFileLine('end'); ?>