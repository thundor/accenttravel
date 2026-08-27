<?php
defined('BASEPATH') OR exit('No direct script access allowed');
themeFunctions::debugFileLine('start');
$L1['meta_title'] = 'CMS resurse' . lang('append_title');
$L1['page_title/html'] = '<span>CMS</span> <strong class="text-success">resurse</strong>';
themeFunctions::loadLang('general/actions');
themeFunctions::loadLang('general/options');
$L1['resources_list/html'] = 'Listă <strong>resurse</strong>';
$L1['cms_resources_permissions/html'] = '<i class="fa fa-check-square-o"></i> <span>Permisiuni în această zonă</span>';
themeFunctions::debugFileLine('end');