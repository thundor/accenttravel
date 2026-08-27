<?php
defined('ENVIRONMENT') OR die('Invalid access');
// default pagination module
themeFunctions::includeAddon('select2/4.0.4');
themeFunctions::addIncludePath('addons/jquery/scripts.php', __DIR__ . '/scripts.php');