<?php
defined('BASEPATH') OR exit('No direct script access allowed');
themeFunctions::debugFileLine('start');
themeFunctions::loadLang('general/actions');
$L1['meta_title_add'] = 'Adăugare șablon';
$L1['meta_title_edit'] = 'Editare șablon';
$L1['page_title/html'] = '<span>Șablon</span> <strong class="text-success">Pagini</strong>';
$L1['action_edit'] = 'Editare șablon';
$L1['action_edit/html'] = '<i class="fa fa-pencil"></i> <span>' .$L1['action_edit'] . '</span>';
$L1['action_add'] = 'Creare șablon nou';
$L1['action_add/html'] = '<i class="fa fa-plus"></i> <span>' .$L1['action_add'] . '</span>';
if(isset($_GET['id']) && $_GET['id']){
  $L1['meta_title'] = $L1['meta_title_edit'] . lang('append_title');
} else {
  $L1['meta_title'] = $L1['meta_title_add'] . lang('append_title');
}
$L1['layout_info_section_title'] = 'Informații șablon';
$L1['layout_info_section_title/html'] = '<strong>Informații</strong> șablon';
$L1['layout_content_section_title'] = 'Conținut șablon';
$L1['layout_content_section_title/html'] = '<strong>Conținut</strong> șablon';
$L1['slug_field_label'] = 'Alias șablon';
$L1['slug_field_label/html'] = '<strong>Alias</strong> șablon';
$L1['slug_field_help/html'] = 'Doar litere si cifre. Aliasul <strong>default</strong> este blocat.';
$L1['slug_field_placeholder'] = $L1['slug_field_label'];
$L1['name_field_label'] = 'Nume șablon';
$L1['name_field_label/html'] = '<strong>Nume</strong> șablon';
$L1['name_field_placeholder'] = $L1['name_field_label'];
$L1['author_field_label'] = 'Autor șablon';
$L1['author_field_label/html'] = '<strong>Autor</strong> șablon';
$L1['author_field_placeholder'] = $L1['author_field_label'];
$L1['version_field_label'] = 'Versiune șablon';
$L1['version_field_label/html'] = '<strong>Versiune</strong> șablon';
$L1['version_field_placeholder'] = $L1['version_field_label'];


themeFunctions::loadLang('general/item');
themeFunctions::debugFileLine('end');