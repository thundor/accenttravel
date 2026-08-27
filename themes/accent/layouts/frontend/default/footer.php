<?php defined('ENVIRONMENT') OR die('Invalid access'); ?>
<?php themeFunctions::loadModule('static/footer',__FILE__); ?>
<?php themeFunctions::loadModule('static/footer2',__FILE__); ?>
<?php themeFunctions::loadModule('static/scrolltop',__FILE__); ?>
<?php themeFunctions::loadModule('static/cookie',__FILE__); ?>
<?php themeFunctions::loadModule('static/tawk',__FILE__); ?>
<?php themeFunctions::loadModule('static/contact_mobile_footer',__FILE__); ?>
<?php themeFunctions::debugFileLine('start'); ?>
<?php themeFunctions::loadAddons(__FILE__); ?>
<?php themeFunctions::debugFileLine('end'); ?>