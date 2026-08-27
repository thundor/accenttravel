<?php
defined('ENVIRONMENT') OR die('Invalid access');
themeFunctions::includeAddon('jquery');
themeFunctions::includeAddon('tooltip/tether-io');
themeFunctions::addIncludePath('addons/jquery/scripts.php', __DIR__ . '/scripts.php');