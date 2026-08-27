<?php defined('ENVIRONMENT') OR die('Invalid access'); ?>
<?php themeFunctions::debugFileLine('start'); ?>
<?php include 'info.php'; ?>
<?php include 'sort.php'; ?>
<?php include 'items.php'; ?>
<?php include 'pagination.php'; ?>
<?php themeFunctions::loadAddons(__FILE__); ?>
<?php themeFunctions::debugFileLine('end'); ?>