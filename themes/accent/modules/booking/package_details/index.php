<?php defined('ENVIRONMENT') OR die('Invalid access'); ?>
<?php themeFunctions::debugFileLine('start'); ?>
<?php themeFunctions::loadAddons(__FILE__); ?>
<?php themeFunctions::addIncludePath($include_path, __DIR__ . '/content.php'); ?>
<?php themeFunctions::debugFileLine('end'); ?>