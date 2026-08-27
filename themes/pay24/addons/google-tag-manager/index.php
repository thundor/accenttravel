<?php
defined('ENVIRONMENT') OR die('Invalid access');
if(ENVIRONMENT == 'production'){
  themeFunctions::addIncludePath('includes/head/scripts.php', __DIR__ . '/head_scripts.php');
  themeFunctions::addIncludePath('includes/body/content_before.php', __DIR__ . '/body_scripts.php');
}