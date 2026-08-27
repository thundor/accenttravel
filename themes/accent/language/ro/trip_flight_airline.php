<?php
defined('BASEPATH') OR exit('No direct script access allowed');
themeFunctions::debugFileLine('start');
themeFunctions::loadLang('general/actions');
$L1['meta_title'] = 'Editare companie aeriana' . lang('append_title');
$L1['page_title/html'] = '<span>Companie</span> <strong class="text-success">aeriana</strong>';
$L1['airline_info_section_title/html'] = 'Informatii <strong>Companie aeriana</strong>';
$L1['name_field_label/html'] = 'Nume companie';
$L1['name_field_placeholder'] = 'Nume companie';
$L1['code_field_label/html'] = 'Cod companie';
$L1['code_field_placeholder'] = 'Cod companie';
$L1['code_field_help/html'] = 'Codul companiei trebuie sa fie exact cel din TRIP, majuscule';
themeFunctions::debugFileLine('end');