<?php
defined('ENVIRONMENT') OR die('Invalid access');
// default pagination module
themeFunctions::includeAddon('' . basename(__DIR__) . '/4.3.2');
// themeFunctions::addIncludePath('addons/' . basename(__DIR__) . '/scripts.php', __DIR__ . '/scripts.php');