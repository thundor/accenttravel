<?php
defined('BASEPATH') OR exit('No direct script access allowed');
themeFunctions::debugFileLine('start');
themeFunctions::loadLang('general/actions');
$L1['meta_title_add'] = 'Creare tichet';
$L1['meta_title_edit'] = 'Editare tichet';
$L1['action_edit'] = 'Editare tichet';
$L1['action_edit/html'] = '<i class="fa fa-pencil"></i> <span>' .$L1['action_edit'] . '</span>';
$L1['action_add'] = 'Creare tichet';
$L1['action_add/html'] = '<i class="fa fa-plus"></i> <span>' .$L1['action_add'] . '</span>';
if(isset($_GET['id']) && $_GET['id']){
  $L1['page_title/html'] = '<span>Ticketing</span> <strong class="text-success">Editare</strong>';
  $L1['meta_title'] = $L1['meta_title_edit'] . lang('append_title');
} else {
  $L1['page_title/html'] = '<span>Ticketing</span> <strong class="text-success">Adaugare</strong>';
  $L1['meta_title'] = $L1['meta_title_add'] . lang('append_title');
}
themeFunctions::loadLang('general/item');
themeFunctions::debugFileLine('end');