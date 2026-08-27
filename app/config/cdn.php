<?php
/* * **************
 * CDN config
 *
 */

//This is the physical path to the themes (Thanks Marcus Reinhardt & Kristories, for the Mac and Linux fix)
$config['cdn']['path'] = FCPATH . 'themes' . DIRECTORY_SEPARATOR;

//This is the url to the themes path
$config['cdn']['url'] = trim(config_item('base_url'), '/ ') . '/cdn/';
$config['cdn']['ftp'] = false;
$config['cdn']['path'] = FCPATH . 'cdn' . DIRECTORY_SEPARATOR;