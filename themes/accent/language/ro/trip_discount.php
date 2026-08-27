<?php
defined('BASEPATH') OR exit('No direct script access allowed');
themeFunctions::debugFileLine('start');
themeFunctions::loadLang('general/actions');
themeFunctions::loadLang('general/options');
$L1['meta_title'] = 'Discount' . lang('append_title');
$L1['page_title/html'] = '<span>Informatii</span> <strong class="text-success">discount</strong>';
$L1['discount_info_section_title/html'] = 'Informatii <strong>Discount</strong>';
$L1['type_package/html'] = 'Vacanta';
$L1['percentage_field_label/html'] = 'Discount';
$L1['percentage_field_placeholder'] = 'Discount';
$L1['date_start_field_label/html'] = 'Data start disponibilitate';
$L1['date_start_field_placeholder'] = 'Campul poate fi gol';
$L1['date_expire_field_label/html'] = 'Data expirare';
$L1['date_expire_field_placeholder'] = 'Campul poate fi gol (nelimitat)';
$L1['status_field_label/html'] = 'Status';
themeFunctions::debugFileLine('end');