<?php
defined('ENVIRONMENT') OR die('Invalid access');
/* $include_path must be defined in caller */
$data['once'] = true;
themeFunctions::addIncludePath(isset($include_path) ? $include_path : 'includes/body/scripts.php' , __DIR__ . '/scripts.php', $data);