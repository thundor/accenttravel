<?php
defined('ENVIRONMENT') OR die('Invalid access');
define('DEBUG_THEME', ENVIRONMENT !== 'production' || isset($_GET['test']));

require __DIR__ . '/../accent/requires/theme.php';

$themepath = __DIR__ . '/';
themeFunctions::setBasePath(__DIR__ . '/');
themeFunctions::setThemeObj($this);
// if(DEBUG_THEME){
  // themeFunctions::enableDebug();
// }