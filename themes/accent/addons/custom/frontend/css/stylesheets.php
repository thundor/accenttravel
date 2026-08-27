<?php defined('ENVIRONMENT') OR die('Invalid access'); ?>
<?php themeFunctions::debugFileLine('start'); ?>
<?php themeFunctions::addIncludePath(themeFunctions::relativePath(__FILE__), __DIR__ . '/../../common/css/stylesheets.php'); ?>
<link type="text/css" rel="stylesheet" href="<?php echo $this->theme_url; ?>assets/css/default.css?v=1.1.0" />
<link type="text/css" rel="stylesheet" href="<?php echo $this->theme_url; ?>assets/css/responsive.css?v=1.0.5" />
<?php themeFunctions::loadAddons(__FILE__); ?>
<link type="text/css" rel="stylesheet" href="<?php echo $this->theme_url; ?>assets/css/custom.css?v=1.0.23" />
<?php themeFunctions::debugFileLine('end'); ?>