<?php defined('ENVIRONMENT') OR die('Invalid access'); ?>
<?php themeFunctions::debugFileLine('start'); ?>
<?php themeFunctions::loadAddons(__FILE__); ?>
<?php include 'body/content_before.php'; ?>
<?php include 'body/content.php'; ?>
<?php include 'body/content_after.php'; ?>
<?php include 'body/scripts.php'; ?>
<?php themeFunctions::debugFileLine('end'); ?>