<?php
defined('ENVIRONMENT') OR die('Invalid access');
themeFunctions::addIncludePath($include_path, __DIR__ . '/content.php');
themeFunctions::includeAddon('select2');
themeFunctions::includeAddon('forms-validation');
themeFunctions::addIncludePath('includes/body/scripts.php', __DIR__ . '/scripts.php');