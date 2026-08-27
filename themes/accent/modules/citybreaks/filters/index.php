<?php
defined('ENVIRONMENT') OR die('Invalid access');
/* $include_path must be defined in caller */
themeFunctions::addIncludePath($include_path, __DIR__ . '/content.php');
themeFunctions::addIncludePath('includes/body/scripts.php', __DIR__ . '/scripts.php');