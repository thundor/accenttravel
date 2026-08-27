<?php
defined('ENVIRONMENT') OR die('Invalid access');
/* $include_path must be defined in caller */
if(false && !$this->_ci->session->userdata('cookie_accepted')){
  themeFunctions::addIncludePath($include_path, __DIR__ . '/home.php');
  themeFunctions::addIncludePath('includes/body/scripts.php', __DIR__ . '/scripts.php');
}