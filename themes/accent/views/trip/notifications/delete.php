<?php defined('ENVIRONMENT') OR die('Invalid access'); ?>
<?php themeFunctions::loadAddons(__FILE__); ?>
<?php themeFunctions::debugFileLine('start'); ?>
<?php themeFunctions::addIncludePath('modules/head/meta.php', __DIR__ . '/delete/meta.php'); ?>
<?php include __DIR__ . '/delete/content.php'; ?>
<?php themeFunctions::debugFileLine('end'); ?>