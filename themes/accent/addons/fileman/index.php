<?php
defined('ENVIRONMENT') OR die('Invalid access');
themeFunctions::addIncludePath('includes/body/scripts.php', __DIR__ . '/scripts.php');
$_SESSION['FILEMAN_ACTIVE'] = $this->_ci->user->can('backend-access');