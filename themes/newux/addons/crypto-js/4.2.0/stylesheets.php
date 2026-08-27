<?php defined('ENVIRONMENT') OR die('Invalid access'); ?>
<?php themeFunctions::loadAddons(__FILE__); ?>
<?php themeFunctions::debugFileLine('start'); ?>
<link type="text/css" rel="stylesheet" href="<?php echo $this->theme_url; ?>assets/plugins/<?php echo basename(dirname(__DIR__))?>/<?php echo basename(__DIR__)?>/<?php echo basename(dirname(__DIR__))?>.css" />
<?php themeFunctions::debugFileLine('end'); ?>