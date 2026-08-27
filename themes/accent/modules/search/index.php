<?php
defined('ENVIRONMENT') OR die('Invalid access');
/* $include_path must be defined in caller */
themeFunctions::addIncludePath($include_path, __DIR__ . '/home.php');
themeFunctions::loadModule('hotelsasync/search',__FILE__);
themeFunctions::loadModule('flights/search',__FILE__);
themeFunctions::loadModule('citybreaksasync/search',__FILE__);
themeFunctions::loadModule('packages/search',__FILE__);
themeFunctions::addIncludePath('includes/body/scripts.php', __DIR__ . '/scripts.php');