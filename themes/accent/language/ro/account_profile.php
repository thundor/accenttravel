<?php
defined('BASEPATH') OR exit('No direct script access allowed');
themeFunctions::debugFileLine('start');
themeFunctions::loadLang('account/common_fields');
themeFunctions::loadLang('general/actions');
themeFunctions::loadLang('backend/menu');
$L1['meta_title'] = 'Profilul meu' . lang('append_title');
$L1['action_save'] = 'Salvare profil';
$L1['action_save/html'] = '<i class="fa fa-save"></i> <span>' .$L1['action_save'] . '</span>';
$L1['profile_permissions/html'] = '<i class="fa fa-check-square-o"></i> <span>Permisiuni în această zonă</span>';
$L1['page_title/html'] = '<span>Profilul</span> <strong class="text-success">Meu</strong>';
themeFunctions::debugFileLine('end');