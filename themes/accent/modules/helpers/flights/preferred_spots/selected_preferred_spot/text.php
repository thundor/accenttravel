<?php defined('ENVIRONMENT') OR die('Invalid access'); ?>
<?php themeFunctions::debugFileLine('start'); ?>
<?php 
require_once(dirname(__DIR__) . '/preferred_spots.php'); 
$selected_value = isset($data['selected']) ? $data['selected'] : '';
$default_value = isset($data['default']) ? $data['default'] : '';
echo isset($this->preferred_spots_selections[$selected_value]) ? $this->preferred_spots_selections[$selected_value] : $default_value; ?>
<?php themeFunctions::loadAddons(__FILE__); ?>
<?php themeFunctions::debugFileLine('end'); ?>