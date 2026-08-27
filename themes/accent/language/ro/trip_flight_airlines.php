<?php
defined('BASEPATH') OR exit('No direct script access allowed');
themeFunctions::debugFileLine('start');
$L1['meta_title'] = 'Companii aeriene' . lang('append_title');
$L1['page_title/html'] = '<span>Companii</span> <strong class="text-success">aeriene</strong>';
themeFunctions::loadLang('general/actions');
themeFunctions::loadLang('general/options');
$L1['action_edit'] = 'Editare companie';
$L1['action_edit/html'] = '<i class="fa fa-pencil"></i> <span>' .$L1['action_edit'] . '</span>';
$L1['airlines_list/html'] = 'Listă <strong>companii aeriene</strong>';
themeFunctions::loadLang('accounts/list');
$L1['th_code'] = 'Cod';
$L1['th_name'] = 'Nume';
$L1['th_image'] = 'Imagine';
themeFunctions::loadLang('general/list');
themeFunctions::loadLang('general/filter');
themeFunctions::loadLang('general/sort');
themeFunctions::debugFileLine('end');