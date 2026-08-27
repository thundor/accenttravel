<?php defined('ENVIRONMENT') OR die('Invalid access'); ?>
<?php themeFunctions::loadAddons(__FILE__); ?>
<?php themeFunctions::debugFileLine('start'); ?>
<link type="text/css" rel="stylesheet" href="<?php echo $this->theme_url; ?>assets/plugins/select2/4.0.4/css/select2_4.min.css" />
<link type="text/css" rel="stylesheet" href="<?php echo $this->theme_url; ?>assets/plugins/select2/4.0.4/css/select2_4-bootstrap.css" />
<?php themeFunctions::debugFileLine('end'); ?>