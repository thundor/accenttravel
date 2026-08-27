<?php
defined('BASEPATH') OR exit('No direct script access allowed');
themeFunctions::debugFileLine('start');
$L1['meta_title'] = 'Roluri utilizatori' . lang('append_title');
$L1['page_title/html'] = '<span>Roluri</span> <strong class="text-success">Utilizatori</strong>';

themeFunctions::loadLang('accounts/permissions');
themeFunctions::loadLang('accounts/roles');
themeFunctions::loadLang('general/actions');
$L1['action_save'] = 'Salvare Permisiuni';
$L1['roles_permissions/html'] = '<i class="fa fa-check-square-o"></i> <span>Permisiuni în această zonă</span>';
$L1['all_roles_permissions/html'] = '<i class="fa fa-check-square-o"></i> <span>Permisiuni toate rolurile</span>';
$L1['global_permissions/html'] = '<i class="fa fa-check-square-o"></i> <span>Permisiuni globale</span>';
$L1['zone_permissions'] = 'Permisiuni pentru aceasta zona';
themeFunctions::loadLang('general/options');

themeFunctions::debugFileLine('end');