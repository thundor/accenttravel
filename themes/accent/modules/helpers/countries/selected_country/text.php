<?php defined('ENVIRONMENT') OR die('Invalid access'); ?>
<?php themeFunctions::debugFileLine('start'); ?>
<?php 
require_once(dirname(__DIR__) . '/countries.php'); 
$selected_value = isset($data['selected']) ? $data['selected'] : '';
$default_value = isset($data['default']) ? $data['default'] : '';
$with_prefix = isset($data['with_prefix']) ? $data['with_prefix'] : false;
echo isset($this->countries_selections[$selected_value]) ? $this->countries_selections[$selected_value]->text . ($with_prefix ? ' (' . $this->countries_selections[$selected_value]->prefix . ')' : '') : $default_value; ?>
<?php themeFunctions::loadAddons(__FILE__); ?>
<?php themeFunctions::debugFileLine('end'); ?>