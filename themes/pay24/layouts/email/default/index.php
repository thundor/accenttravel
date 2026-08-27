<?php
defined('ENVIRONMENT') OR die('Invalid access');
themeFunctions::addIncludePath('includes/body/content_before.php', __DIR__ . '/header.php');
themeFunctions::addIncludePath('includes/body/content.php', __DIR__ . '/content.php');
themeFunctions::addIncludePath('includes/body/content_after.php', __DIR__ . '/footer.php');
themeFunctions::addIncludePath('includes/head/stylesheets.php', __DIR__ . '/stylesheets.php');
$this->content();