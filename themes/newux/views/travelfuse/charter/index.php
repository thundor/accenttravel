<?php defined('ENVIRONMENT') OR die('Invalid access'); ?>
<?php themeFunctions::includeAddon('lazy-loading'); ?>
<?php 
$this->hotel_search_data = &$hotel_search_data;
themeFunctions::addIncludePath('includes/body/scripts.php', __DIR__ . '/index/scripts.php');
themeFunctions::addIncludePath('includes/head/stylesheets.php', __DIR__ . '/index/stylesheets.php');
themeFunctions::addIncludePath('modules/head/meta.php', __DIR__ . '/index/meta.php');

themeFunctions::loadAddons(__FILE__);
themeFunctions::debugFileLine('start'); ?>
<?php include 'index/details.php'; ?>
<?php themeFunctions::debugFileLine('end'); ?>