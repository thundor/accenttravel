<?php defined('ENVIRONMENT') OR die('Invalid access'); ?>
<?php themeFunctions::debugFileLine('start'); ?>
<?php 
require_once(dirname(__DIR__) . '/special_assistance.php'); 
$selected_values = isset($data['selected']) ? $data['selected'] : array();
$default_value = isset($data['default']) ? $data['default'] : '';
$selected_items = array();
foreach($selected_values as $selected_value){
  if(isset($this->special_assistance_selections[$selected_value])){
    $selected_items[$selected_value] = $this->special_assistance_selections[$selected_value];
  }
}
echo $selected_items ? implode(', ', $selected_items) : $default_value; ?>
<?php themeFunctions::loadAddons(__FILE__); ?>
<?php themeFunctions::debugFileLine('end'); ?>