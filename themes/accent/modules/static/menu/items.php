<?php defined('ENVIRONMENT') OR die('Invalid access'); ?>
<?php themeFunctions::debugFileLine('start'); ?>
<?php 
$this->_ci->load->model('Options_model');
$menu = $this->_ci->Options_model->get('trip_cms_menu', 'navigatie_principal');
$this->_ci->load->library('FrontendMenuItem',array('key' => 'navigatie_principal', 'children' => $menu, 'ul_class'=>'navbar-nav mr-auto'),'frontend_menu_items_navigatie_principal');
$this->_ci->frontend_menu_items_navigatie_principal->render_style_main();
?>
<?php themeFunctions::loadAddons(__FILE__); ?>
<?php themeFunctions::debugFileLine('end'); ?>