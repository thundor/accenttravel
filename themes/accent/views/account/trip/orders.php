<?php defined('ENVIRONMENT') OR die('Invalid access'); ?>
<?php themeFunctions::loadAddons(__FILE__); ?>
<?php themeFunctions::includeAddon('pagination'); ?>
<?php themeFunctions::debugFileLine('start'); ?>
<?php themeFunctions::addIncludePath('modules/head/meta.php', __DIR__ . '/orders/meta.php'); ?>
<?php themeFunctions::addIncludePath('includes/body/scripts.php', __DIR__ . '/orders/scripts.php'); ?>
<?php include __DIR__ . '/orders/content.php'; ?>
<?php themeFunctions::debugFileLine('end'); ?>