<?php
defined('ENVIRONMENT') OR die('Invalid access');
$this->_ci->load->library('Mobile_Detect');
$this->detect = new Mobile_Detect();
if (!$this->detect->isMobile() && !$this->detect->isTablet()){
  themeFunctions::addIncludePath($include_path, __DIR__ . '/home.php');
  themeFunctions::addIncludePath('includes/head/stylesheets.php', __DIR__ . '/stylesheets.php');
}