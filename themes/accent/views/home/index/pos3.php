<?php defined('ENVIRONMENT') OR die('Invalid access'); ?>
<?php themeFunctions::debugFileLine('start'); ?>
<?php if(!empty($_GET['tud007']) || 1) themeFunctions::loadModule('sliders/heroslider',__FILE__); ?>
<?php themeFunctions::loadModule('offers/recommended',__FILE__); ?>
<?php themeFunctions::loadAddons(__FILE__); ?>
<?php themeFunctions::debugFileLine('end'); ?>