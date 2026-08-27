<?php defined('ENVIRONMENT') OR die('Invalid access'); ?>
<?php themeFunctions::loadAddons(__FILE__); ?>
<?php themeFunctions::debugFileLine('start'); ?>
<?php themeFunctions::addIncludePath('modules/head/meta.php', __DIR__ . '/delete_all/meta.php'); ?>
<?php include __DIR__ . '/delete_all/content.php'; ?>
<?php themeFunctions::debugFileLine('end'); ?>