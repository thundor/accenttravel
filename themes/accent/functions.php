<?php
defined('ENVIRONMENT') OR die('Invalid access');
if(!class_exists('themeFunctions')){
	define('DEBUG_THEME', ENVIRONMENT !== 'production' || isset($_GET['test']));
	require 'requires/theme.php';
}

$themepath = __DIR__ . '/';
themeFunctions::setBasePath(__DIR__ . '/');
themeFunctions::setThemeObj($this);
if(DEBUG_THEME){
  themeFunctions::enableDebug();
}