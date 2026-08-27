<?php
defined('ENVIRONMENT') OR die('Invalid access');
/* $include_path must be defined in caller */
if(isset(Modules::$page)){
  // themeFunctions::includeAddon('forms-validation');
  themeFunctions::addIncludePath('includes/body/scripts.php', __DIR__ . '/scripts.php');
  themeFunctions::addIncludePath('modules/head/meta.php', __DIR__ . '/meta.php');
  themeFunctions::addIncludePath($include_path, __DIR__ . '/content.php');
}