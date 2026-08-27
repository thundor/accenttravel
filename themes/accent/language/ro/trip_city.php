<?php
defined('BASEPATH') OR exit('No direct script access allowed');
themeFunctions::debugFileLine('start');
themeFunctions::loadLang('general/actions');
$L1['meta_title'] = 'Informatii oras' . lang('append_title');
$L1['page_title/html'] = '<span>Informatii</span> <strong class="text-success">oras</strong>';
$L1['city_info_section_title/html'] = 'Informatii <strong>Oras</strong>';
$L1['name_field_label/html'] = 'Nume oras';
$L1['country_field_label/html'] = 'Tara';
$L1['trip_city_field_label/html'] = 'Oras TRIP (hotel/zbor)';
$L1['aida_city_field_label/html'] = 'Oras Aida (vacanta)';
$L1['name_field_placeholder'] = 'Nume oras';
themeFunctions::debugFileLine('end');