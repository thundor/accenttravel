<?php defined('ENVIRONMENT') OR die('Invalid access'); ?>
<?php themeFunctions::debugFileLine('start'); ?>
<?php themeFunctions::loadAddons(__FILE__); ?>
<?php themeFunctions::addIncludePath('includes/body/scripts.php', __DIR__ . '/scripts.php'); ?>
<?php themeFunctions::addIncludePath('includes/head/stylesheets.php', __DIR__ . '/stylesheets.php'); ?>
<?php themeFunctions::addIncludePath($include_path, __DIR__ . '/content.php'); ?>
<?php themeFunctions::debugFileLine('end'); ?>