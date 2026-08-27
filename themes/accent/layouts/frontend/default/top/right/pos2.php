<?php defined('ENVIRONMENT') OR die('Invalid access'); ?>
<?php themeFunctions::debugFileLine('start'); ?>
<?php themeFunctions::loadModule('static/phone',__FILE__); ?>
<?php themeFunctions::loadModule('account/quick',__FILE__); ?>
<?php //themeFunctions::loadModule('static/info',__FILE__); ?>
<?php themeFunctions::loadAddons(__FILE__); ?>
<?php themeFunctions::debugFileLine('end'); ?>