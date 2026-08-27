<?php defined('ENVIRONMENT') OR die('Invalid access'); ?>
<?php themeFunctions::debugFileLine('start'); ?>
<?php 
$preferred_spots = array();
$preferred_spots['window'] = 'Fereastra';
$preferred_spots['corridor'] = 'Culoar';
$this->preferred_spots_selections = &$preferred_spots;
?>
<?php themeFunctions::loadAddons(__FILE__); ?>
<?php themeFunctions::debugFileLine('end'); ?>