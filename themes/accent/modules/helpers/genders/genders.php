<?php defined('ENVIRONMENT') OR die('Invalid access'); ?>
<?php themeFunctions::debugFileLine('start'); ?>
<?php 
$genders = array();
$genders['m'] = 'Masculin';
$genders['f'] = 'Feminin';
$this->genders_selections = &$genders;
?>
<?php themeFunctions::loadAddons(__FILE__); ?>
<?php themeFunctions::debugFileLine('end'); ?>