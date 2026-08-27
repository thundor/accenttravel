<?php defined('ENVIRONMENT') OR die('Invalid access'); ?>
<?php themeFunctions::loadAddons(__FILE__); ?>
<?php themeFunctions::debugFileLine('start'); ?>
<?php themeFunctions::addIncludePath('modules/head/meta.php', __DIR__ . '/weekend/meta.php'); ?>
<?php themeFunctions::addIncludePath('includes/body/scripts.php', __DIR__ . '/weekend/scripts.php'); ?>
<?php themeFunctions::addIncludePath('views/home/index/pos3.php', __DIR__ . '/weekend/content.php'); ?>
<?php themeFunctions::blockModule('offers/recommended', true); ?>
<?php themeFunctions::blockModule('offers/weekend', true); ?>
<?php themeFunctions::blockModule('offers/popular', true); ?>
<?php themeFunctions::blockModule('offers/holiday', true); ?>
<?php include dirname(dirname(__DIR__)) . '/home/index.php'; ?>
<?php themeFunctions::debugFileLine('end'); ?>
<?php themeFunctions::debugFileLine('end'); ?>
