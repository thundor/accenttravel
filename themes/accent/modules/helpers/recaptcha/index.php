<?php
defined('ENVIRONMENT') OR die('Invalid access');
/* $include_path must be defined in caller */
themeFunctions::includeAddon('recaptcha');
themeFunctions::addIncludePath($include_path, __DIR__ . '/content.php');