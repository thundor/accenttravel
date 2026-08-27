<?php
defined('ENVIRONMENT') OR die('Invalid access');
$this->_ci->load->library('Mobile_Detect');
if ($this->detect->isMobile()) {
  themeFunctions::addIncludePath('includes/head/stylesheets.php', __DIR__ . '/stylesheets.php');
  themeFunctions::addIncludePath('includes/body/scripts.php', __DIR__ . '/scripts.php');
  themeFunctions::addIncludePath($include_path, __DIR__ . '/content.php');
}