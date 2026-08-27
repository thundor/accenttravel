<?php
defined('ENVIRONMENT') OR die('Invalid access');
/* $include_path must be defined in caller */
themeFunctions::addIncludePath('includes/body/scripts.php', __DIR__ . '/scripts.php');
themeFunctions::addIncludePath('includes/head/stylesheets.php', __DIR__ . '/stylesheets.php');