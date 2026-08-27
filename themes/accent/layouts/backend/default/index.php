<?php
defined('ENVIRONMENT') OR die('Invalid access');
themeFunctions::includeAddon('jquery');
themeFunctions::includeAddon('jquery/cookie');
themeFunctions::includeAddon('tooltip');
themeFunctions::includeAddon('bootstrap');
themeFunctions::includeAddon('jquery-ui');
themeFunctions::includeAddon('font-icons');
themeFunctions::includeAddon('custom/backend');
themeFunctions::addIncludePath('includes/body/content.php', __DIR__ . '/content.php');
themeFunctions::addIncludePath('includes/head/stylesheets.php', __DIR__ . '/stylesheets.php');
themeFunctions::addIncludePath('includes/head/scripts.php', __DIR__ . '/head_scripts.php');
themeFunctions::addIncludePath('includes/body/scripts.php', __DIR__ . '/scripts.php');
themeFunctions::loadModule('cms/page',__DIR__ . '/content.php');
themeFunctions::loadLang('backend');
$this->content();