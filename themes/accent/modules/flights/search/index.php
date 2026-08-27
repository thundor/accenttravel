<?php
defined('ENVIRONMENT') OR die('Invalid access');
/* $include_path must be defined in caller */
themeFunctions::addIncludePath(dirname($include_path) . '/includes/navbar.php', __DIR__ . '/navbar.php');
themeFunctions::addIncludePath(dirname($include_path) . '/includes/content.php', __DIR__ . '/content.php');
themeFunctions::addIncludePath('includes/body/scripts.php', __DIR__ . '/scripts.php');