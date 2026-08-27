<?php defined('ENVIRONMENT') OR die('Invalid access'); ?>
<?php themeFunctions::debugFileLine('start'); ?>
<link type="text/css" rel="stylesheet" href="<?php echo $this->theme_url; ?>assets/css/default.css?v=1.0.2" />
<link type="text/css" rel="stylesheet" href="<?php echo $this->theme_url; ?>assets/css/responsive.css?v=1.0.2" />
<link type="text/css" rel="stylesheet" href="<?php echo $this->theme_url; ?>assets/css/common.css?v=1.0.1" />
<link type="text/css" rel="stylesheet" href="<?php echo $this->theme_url; ?>assets/css/custom.css?v=1.0.7" />
<?php themeFunctions::loadAddons(__FILE__); ?>
<?php themeFunctions::debugFileLine('end'); ?>