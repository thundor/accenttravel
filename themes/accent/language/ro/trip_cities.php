<?php
defined('BASEPATH') OR exit('No direct script access allowed');
themeFunctions::debugFileLine('start');
$L1['meta_title'] = 'Informatii orase' . lang('append_title');
$L1['page_title/html'] = '<span>Informatii</span> <strong class="text-success">orase</strong>';
themeFunctions::loadLang('general/actions');
themeFunctions::loadLang('general/options');
$L1['action_edit'] = 'Editare oras';
$L1['action_edit/html'] = '<i class="fa fa-pencil"></i> <span>' .$L1['action_edit'] . '</span>';
$L1['cities_list/html'] = 'Listă <strong>orase</strong>';
themeFunctions::loadLang('accounts/list');
$L1['th_id'] = 'ID';
$L1['th_name'] = 'Nume';
$L1['th_country'] = 'Tara';
$L1['th_image'] = 'Imagine';
themeFunctions::loadLang('general/list');
themeFunctions::loadLang('general/filter');
themeFunctions::loadLang('general/sort');
themeFunctions::debugFileLine('end');