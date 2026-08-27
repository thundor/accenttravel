<?php
defined('BASEPATH') OR exit('No direct script access allowed');
themeFunctions::debugFileLine('start');
themeFunctions::loadLang('general/actions');
themeFunctions::loadLang('general/options');
$L1['page_title/html'] = '<span>CMS</span> <strong class="text-success">Meniuri</strong>';
$L1['action_edit'] = 'Editare meniu';
$L1['action_edit/html'] = '<i class="fa fa-pencil"></i> <span>' .$L1['action_edit'] . '</span>';
$L1['action_add'] = 'Creare meniu nou';
$L1['action_add/html'] = '<i class="fa fa-plus"></i> <span>' .$L1['action_add'] . '</span>';
$L1['meta_title'] = 'Meniuri' . lang('append_title');
themeFunctions::loadLang('general/item');
themeFunctions::debugFileLine('end');