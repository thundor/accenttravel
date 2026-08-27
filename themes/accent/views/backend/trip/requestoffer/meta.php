<?php defined('ENVIRONMENT') OR die('Invalid access'); ?>
<?php themeFunctions::debugFileLine('start'); ?>
<title><?php echo lang('meta_title'); ?></title>
<meta name="description" content="<?php echo lang('meta_description'); ?>">
<meta name="keywords" content="<?php echo lang('meta_keywords'); ?>">
<?php themeFunctions::loadAddons(__FILE__); ?>
<?php themeFunctions::debugFileLine('end'); ?>