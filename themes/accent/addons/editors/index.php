<?php
defined('ENVIRONMENT') OR die('Invalid access');
// Default datepicker module
themeFunctions::includeAddon('editors/ckeditor');
themeFunctions::addIncludePath('addons/editors/ckeditor/scripts.php', __DIR__ . '/scripts.php');