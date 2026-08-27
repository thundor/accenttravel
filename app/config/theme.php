<?php
/* * **************
 * Theme config
 *
 */

//This is the physical path to the themes (Thanks Marcus Reinhardt & Kristories, for the Mac and Linux fix)
$config['theme']['path'] = FCPATH . 'themes' . DIRECTORY_SEPARATOR;

//This is the url to the themes path
$config['theme']['url'] = trim(config_item('base_url'), '/ ') . '/themes/';

//This is the default theme (subfolder in the themes folder)
$config['theme']['theme'] = config_item('theme_newux') ? 'newux' : (config_item('trip_24_pay') && (!defined('NO_THEME_CHANGE') || !NO_THEME_CHANGE) ? 'pay24' : 'accent');

// if(!empty($_GET['testtt'])){
	// var_dump($config['theme']['theme']); die;
// }

//This is the default layout (index: a mapping to index.php)
$config['theme']['layout'] = 'index';