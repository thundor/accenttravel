<?php
defined('BASEPATH') OR exit('No direct script access allowed');
themeFunctions::debugFileLine('start');
$L1['action_save'] = 'Salvare';
$L1['action_delete'] = 'Ștergere';
$L1['action_move'] = 'Mutare';
$L1['action_add'] = 'Adăugare';
$L1['action_edit'] = 'Editare';
$L1['action_access'] = 'Accesare';
$L1['action_apply'] = 'Salvare și se continuă editarea';
$L1['action_export'] = 'Export CSV';
$L1['action_save_and_new'] = 'Salvare și deschide formular nou';
$L1['action_save_and_back_to_list'] = 'Salvare și înapoi la listă';
$L1['action_save_as_new'] = 'Salvare ca și instanță separată';
$L1['action_back_to_list'] = 'Înapoi la listă';

$L1['action_export/html'] = '<i class="fa fa-download"></i> <span>' . $L1['action_export'] . '</span>';
$L1['action_save/html'] = '<i class="fa fa-save"></i> <span>' . $L1['action_save'] . '</span>';
$L1['action_delete/html'] = '<i class="fa fa-times"></i> <span>' . $L1['action_delete'] . '</span>';
$L1['action_move/html'] = '<i class="fa fa-move"></i> <span>' . $L1['action_move'] . '</span>';
$L1['action_add/html'] = '<i class="fa fa-plus"></i> <span>' .$L1['action_add'] . '</span>';
$L1['action_edit/html'] = '<i class="fa fa-pencil"></i> <span>' .$L1['action_edit'] . '</span>';
$L1['action_access/html'] = '<i class="fa fa-eye"></i> <span>' .$L1['action_access'] . '</span>';
$L1['action_apply/html'] = '<i class="fa fa-save"></i> <i class="fa fa-check"></i> <span>' .$L1['action_apply'] . '</span>';
$L1['action_save_and_new/html'] = '<i class="fa fa-save"></i> <i class="fa fa-plus"></i> <span>' .$L1['action_save_and_new'] . '</span>';
$L1['action_save_as_new/html'] = '<i class="fa fa-copy"></i> <i class="fa fa-save"></i> <span>' .$L1['action_save_as_new'] . '</span>';
$L1['action_save_and_back_to_list/html'] = '<i class="fa fa-save"></i> <i class="fa fa-undo"></i> <span>' .$L1['action_save_and_back_to_list'] . '</span>';
$L1['action_back_to_list/html'] = '<i class="fa fa-undo"></i> <span>' .$L1['action_back_to_list'] . '</span>';

$L1['th_actions'] = 'Acțiuni';
$L1['th_actions/html'] = $L1['th_actions'];
themeFunctions::debugFileLine('end');