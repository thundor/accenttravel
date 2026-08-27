<?php
defined('ENVIRONMENT') OR die('Invalid access');
// default pagination module
themeFunctions::includeAddon('pagination/twbs');
themeFunctions::addIncludePath('includes/body/scripts.php', __DIR__ . '/scripts.php');