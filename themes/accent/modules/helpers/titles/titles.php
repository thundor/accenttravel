<?php defined('ENVIRONMENT') OR die('Invalid access'); ?>
<?php themeFunctions::debugFileLine('start'); ?>
<?php 
$titles = array();
$titles['mr'] = 'Dl.';
$titles['mrs'] = 'Dna.';
$titles['ms'] = 'Dra.';
// $titles['chd'] = 'Copil/Infant';
$this->titles_selections = &$titles;
?>
<?php themeFunctions::loadAddons(__FILE__); ?>
<?php themeFunctions::debugFileLine('end'); ?>