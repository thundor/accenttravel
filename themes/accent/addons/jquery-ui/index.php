<?php
defined('ENVIRONMENT') OR die('Invalid access');
themeFunctions::includeAddon('jquery');
themeFunctions::addIncludePath('includes/head/stylesheets.php', __DIR__ . '/stylesheets.php');
themeFunctions::addIncludePath('addons/jquery/scripts.php', __DIR__ . '/scripts.php');