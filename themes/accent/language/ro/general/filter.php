<?php
defined('BASEPATH') OR exit('No direct script access allowed');
themeFunctions::debugFileLine('start');
$L1['filter_search'] = 'Căutare';
$L1['filter_search_placeholder'] = 'Căutare ...';
$L1['filter_search_icon/html'] = '<i class="fa fa-search"></i>';
$L1['filter_search/html'] = $L1['filter_search_icon/html'] . ' <span class="hidden-lg-down">' . $L1['filter_search'] . '</span>';

$L1['filter_adv_search'] = 'Filtrare avansata';
$L1['filter_adv_search_icon/html'] = '<i class="fa fa-filter"></i>';
$L1['filter_adv_search/html'] = $L1['filter_adv_search_icon/html'] . ' <span class="hidden-lg-down">' . $L1['filter_adv_search'] . '</span>';

$L1['filter_clear'] = 'Curăță';
$L1['filter_clear_icon/html'] = '<i class="fa fa-times"></i>';
$L1['filter_clear/html'] = $L1['filter_clear_icon/html'] . '<span class="hidden-lg-down">' . $L1['filter_clear'] . '</span>';
themeFunctions::debugFileLine('end');