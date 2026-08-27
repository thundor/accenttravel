<?php
defined('BASEPATH') OR exit('No direct script access allowed');
themeFunctions::debugFileLine('start');
themeFunctions::loadLang('general/actions');
themeFunctions::loadLang('general/options');
themeFunctions::loadLang('travelfuse_facilities');
$L1['meta_title'] = 'Facilitate Travelfuse ' . lang('append_title');
$L1['page_title/html'] = '<span>Informatii</span> <strong class="text-success">facilitate</strong>';
$L1['facility_info_section_title/html'] = 'Informatii <strong>Facilitate</strong>';
// $L1['country_field_label/html'] = 'Tara';
// $L1['country_field_placeholder'] = 'Tara';
$L1['type_field_label/html'] = 'Tip';
$L1['type_field_placeholder'] = 'Tip';
$L1['status_field_label/html'] = 'Status';
themeFunctions::debugFileLine('end');