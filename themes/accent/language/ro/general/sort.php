<?php
defined('BASEPATH') OR exit('No direct script access allowed');
themeFunctions::debugFileLine('start');
$L1['ordering'] = 'Ordonare';
$L1['ordering/html'] = '<i class="fa fa-sort"></i> <span>'. $L1['ordering'] . '</span>';
$L1['sort_ascending'] = 'Ascendent';
$L1['sort_descending'] = 'Descendent';
$L1['sort_ascending_numeric/html'] = '<i class="fa fa-sort-numeric-asc"></i> <span>'. $L1['sort_ascending'] . '</span>';
$L1['sort_descending_numeric/html'] = '<i class="fa fa-sort-numeric-desc"></i> <span>'. $L1['sort_descending'] . '</span>';
$L1['ordering_placeholder'] = 'Alegeti ordonarea';
$L1['ordering_by'] = 'Ordonare după';
themeFunctions::debugFileLine('end');