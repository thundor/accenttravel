<?php
defined('ENVIRONMENT') OR die('Invalid access');
// default pagination module
themeFunctions::includeAddon('twigjs/1.15.1');
themeFunctions::addIncludePath('includes/body/scripts.php', __DIR__ . '/scripts.php');