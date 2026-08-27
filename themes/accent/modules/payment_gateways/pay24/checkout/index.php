<?php defined('ENVIRONMENT') OR die('Invalid access'); ?>
<?php themeFunctions::debugFileLine('start'); ?>
<?php themeFunctions::loadAddons(__FILE__); ?>
<?php themeFunctions::loadLang('payment_gateways/pay24'); ?>
<?php $content = isset($content) ? $content : ''; ?>
<?php $nav = isset($nav) ? $nav : ''; ?>
<?php themeFunctions::addIncludePath($include_path . $content, __DIR__ . '/content.php', $data); ?>
<?php themeFunctions::addIncludePath($include_path . $nav, __DIR__ . '/nav.php', $data); ?>
<?php themeFunctions::debugFileLine('end'); ?>