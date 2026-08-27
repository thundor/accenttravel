<?php
defined('BASEPATH') OR exit('No direct script access allowed');
themeFunctions::debugFileLine('start');
themeFunctions::loadLang('general/actions');
themeFunctions::loadLang('general/options');
$L1['meta_title_add'] = 'Adăugare pagina';
$L1['meta_title_edit'] = 'Editare pagina';
$L1['page_title/html'] = '<span>CMS</span> <strong class="text-success">Pagini</strong>';
$L1['action_edit'] = 'Editare pagina';
$L1['action_edit/html'] = '<i class="fa fa-pencil"></i> <span>' .$L1['action_edit'] . '</span>';
$L1['action_add'] = 'Creare pagina noua';
$L1['action_add/html'] = '<i class="fa fa-plus"></i> <span>' .$L1['action_add'] . '</span>';
if(isset($_GET['id']) && $_GET['id']){
  $L1['meta_title'] = $L1['meta_title_edit'] . lang('append_title');
} else {
  $L1['meta_title'] = $L1['meta_title_add'] . lang('append_title');
}
$L1['page_info_section_title'] = 'Informații pagina';
$L1['page_info_section_title/html'] = '<strong>CMS</strong> pagina';
$L1['page_content_section_title'] = 'Conținut pagina';
$L1['page_content_section_title/html'] = '<strong>Conținut</strong> pagina';
$L1['slug_field_label'] = 'Alias pagina';
$L1['slug_field_label/html'] = '<strong>Alias</strong> pagina';
$L1['slug_field_help/html'] = 'Doar litere si cifre. Aliasul <strong>default</strong> este blocat.';
$L1['slug_field_placeholder'] = $L1['slug_field_label'];
$L1['name_field_label'] = 'Nume pagina';
$L1['name_field_label/html'] = '<strong>Nume</strong> pagina';
$L1['name_field_placeholder'] = $L1['name_field_label'];
$L1['author_field_label'] = 'Autor pagina';
$L1['author_field_label/html'] = '<strong>Autor</strong> pagina';
$L1['author_field_placeholder'] = $L1['author_field_label'];
$L1['version_field_label'] = 'Versiune pagina';
$L1['version_field_label/html'] = '<strong>Versiune</strong> pagina';
$L1['version_field_placeholder'] = $L1['version_field_label'];

$L1['param_sdate'] = 'Data checkin/plecare (sdate)';
$L1['param_edate'] = 'Data checkout/sosire (edate)';
themeFunctions::loadLang('general/item');
themeFunctions::debugFileLine('end');