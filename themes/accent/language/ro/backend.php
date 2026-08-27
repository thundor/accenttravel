<?php
defined('BASEPATH') OR exit('No direct script access allowed');
themeFunctions::debugFileLine('start');
themeFunctions::loadLang('common');
$L1['backend'] = 'Backend';
$L1['title'] = $L1['backend'] . lang('title_separator') . lang('meta_title');
$L1['page_title/html'] = '<span>' . lang('meta_title') . '</span> <strong class="text-success">' . $L1['backend'] . '</strong>';
$L1['append_title'] = lang('title_separator') . $L1['title'];
$L1['meta_title'] = $L1['title'];
$L1['meta_description'] = '';
$L1['meta_keywords'] = '';
themeFunctions::debugFileLine('end');