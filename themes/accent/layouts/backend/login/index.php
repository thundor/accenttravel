<?php
defined('ENVIRONMENT') OR die('Invalid access');
themeFunctions::includeAddon('jquery');
themeFunctions::includeAddon('tooltip');
themeFunctions::includeAddon('bootstrap');
themeFunctions::includeAddon('jquery-ui');
themeFunctions::includeAddon('ladda');
themeFunctions::includeAddon('wow');
themeFunctions::includeAddon('font-icons/font-awesome');
themeFunctions::includeAddon('animate');
themeFunctions::includeAddon('icheck');
themeFunctions::addIncludePath('includes/body/content.php', __DIR__ . '/content.php');
themeFunctions::addIncludePath('includes/head/stylesheets.php', __DIR__ . '/stylesheets.php');
themeFunctions::addIncludePath('includes/body/scripts.php', __DIR__ . '/scripts.php');
themeFunctions::addIncludePath('modules/head/meta.php', __DIR__ . '/index/meta.php');
themeFunctions::loadLang('backend');
$this->content();