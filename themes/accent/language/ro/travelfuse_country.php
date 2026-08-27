<?php
defined('BASEPATH') OR exit('No direct script access allowed');
themeFunctions::debugFileLine('start');
themeFunctions::loadLang('general/actions');
themeFunctions::loadLang('general/options');
themeFunctions::loadLang('travelfuse_countries');
$L1['meta_title'] = 'Tara Travelfuse ' . lang('append_title');
$L1['page_title/html'] = '<span>Informatii</span> <strong class="text-success">tara</strong>';
$L1['country_info_section_title/html'] = 'Informatii <strong>Tara</strong>';
$L1['code_field_label/html'] = 'Cod tara';
$L1['code_field_placeholder'] = 'Cod tara';
$L1['status_field_label/html'] = 'Status';
themeFunctions::debugFileLine('end');