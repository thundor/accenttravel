<?php defined('ENVIRONMENT') OR die('Invalid access'); ?>
<?php themeFunctions::debugFileLine('start'); ?>
<?php 
$special_assistance = array();
$special_assistance['blind'] = 'Nevazator';
$special_assistance['deaf'] = 'Deficienta auz';
$special_assistance['wheelchair'] = 'Scaun cu rotile';
$special_assistance['baby'] = 'Copil sub 2 ani';
$special_assistance['baggage'] = 'Bagaj fragil';
$special_assistance['sports'] = 'Echipament sportiv';
$this->special_assistance_selections = &$special_assistance;
?>
<?php themeFunctions::loadAddons(__FILE__); ?>
<?php themeFunctions::debugFileLine('end'); ?>