<?php
defined('ENVIRONMENT') OR die('Invalid access');
themeFunctions::includeAddon('tooltip');
themeFunctions::includeAddon('datepicker');
themeFunctions::includeAddon('font-icons/font-awesome');
/* $include_path must be defined in caller */
themeFunctions::addIncludePath($include_path, __DIR__ . '/form.php');
themeFunctions::addIncludePath('includes/body/scripts.php', __DIR__ . '/../form/scripts.php');
themeFunctions::addIncludePath('includes/head/stylesheets.php', __DIR__ . '/../form/stylesheets.php');