<?php defined('ENVIRONMENT') OR die('Invalid access'); ?>
<?php themeFunctions::debugFileLine('start'); ?>
<?php 
$social_networks = array();
$social_networks['fb'] = array(
  'text' => 'Facebook',
  'icon' => '<i class="fa fa-facebook-official"></i>',
);
$this->social_networks_selections = &$social_networks;
?>
<?php themeFunctions::loadAddons(__FILE__); ?>
<?php themeFunctions::debugFileLine('end'); ?>