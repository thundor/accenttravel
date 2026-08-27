<?php
defined('ENVIRONMENT') OR die('Invalid access');
themeFunctions::addIncludePath('includes/addons.php', __DIR__ . '/addons.php');
themeFunctions::addIncludePath('includes/body/content_before.php', __DIR__ . '/body/content_before.php');
themeFunctions::addIncludePath('includes/body/content.php', __DIR__ . '/body/content.php');
themeFunctions::addIncludePath('includes/body/content_after.php', __DIR__ . '/body/content_after.php');
themeFunctions::addIncludePath('includes/head/meta.php', __DIR__ . '/head/meta.php');
themeFunctions::addIncludePath('includes/head/stylesheets.php', __DIR__ . '/head/stylesheets.php');
themeFunctions::addIncludePath('includes/head/scripts.php', __DIR__ . '/head/scripts.php');
themeFunctions::addIncludePath('includes/body/scripts.php', __DIR__ . '/body/scripts.php');
$this->content();