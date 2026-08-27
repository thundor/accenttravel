<?php defined('ENVIRONMENT') OR die('Invalid access'); ?>
<?php themeFunctions::debugFileLine('start'); ?>
<?php 
require_once(dirname(__DIR__) . '/social_networks.php'); 
$selected_values = isset($data['selected']) ? $data['selected'] : array();
$default_value = isset($data['default']) ? $data['default'] : '';
$selected_items = array();
foreach($selected_values as $selected_value){
  if(isset($this->social_networks_selections[$selected_value])){
    $social_network = $this->social_networks_selections[$selected_value];
    $icon = isset($social_network['icon']) ? $social_network['icon'] . ' ' : '';
    $text = $social_network['text'];
    $selected_items[] = $icon . $text;
  }
}
echo $selected_items ? implode(', ', $selected_items) : $default_value; ?>
<?php themeFunctions::loadAddons(__FILE__); ?>
<?php themeFunctions::debugFileLine('end'); ?>