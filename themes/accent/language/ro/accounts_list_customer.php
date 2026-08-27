<?php
defined('BASEPATH') OR exit('No direct script access allowed');
themeFunctions::debugFileLine('start');
$L1['meta_title'] = 'Conturi clienți' . lang('append_title');
$L1['page_title/html'] = '<span>Conturi</span> <strong class="text-success">clienți</strong>';
themeFunctions::loadLang('general/actions');
themeFunctions::loadLang('general/options');
$L1['action_add'] = 'Creare cont nou';
$L1['action_add/html'] = '<i class="fa fa-plus"></i> <span>' .$L1['action_add'] . '</span>';
$L1['action_edit'] = 'Editare cont';
$L1['action_edit/html'] = '<i class="fa fa-pencil"></i> <span>' .$L1['action_edit'] . '</span>';
$L1['user_list/html'] = 'Listă <strong>clienți</strong>';
$L1['customers_permissions/html'] = '<i class="fa fa-check-square-o"></i> <span>Permisiuni în această zonă</span>';
themeFunctions::loadLang('accounts/list');
$L1['confirm_delete'] = 'Sunteți sigur că doriți ștergerea acestui client?';
themeFunctions::loadLang('general/list');
themeFunctions::loadLang('general/filter');
themeFunctions::loadLang('general/sort');
themeFunctions::debugFileLine('end');