<?php
defined('ENVIRONMENT') OR die('Invalid access');
themeFunctions::includeAddon('jquery');
themeFunctions::includeAddon('tooltip');
themeFunctions::includeAddon('bootstrap');
themeFunctions::includeAddon('jquery-ui');
themeFunctions::includeAddon('font-icons');
themeFunctions::includeAddon('google-tag-manager');
themeFunctions::includeAddon('facebook');
themeFunctions::includeAddon('custom/frontend');
themeFunctions::addIncludePath('includes/body/content_before.php', __DIR__ . '/content_before.php');
themeFunctions::addIncludePath('includes/body/content.php', __DIR__ . '/content.php');
themeFunctions::addIncludePath('includes/body/content_after.php', __DIR__ . '/footer.php');
themeFunctions::addIncludePath('includes/head/stylesheets.php', __DIR__ . '/stylesheets.php');
themeFunctions::addIncludePath('includes/body/scripts.php', __DIR__ . '/scripts.php');
themeFunctions::loadModule('cms/page',__DIR__ . '/content.php');
themeFunctions::loadLang('frontend');
$this->content();