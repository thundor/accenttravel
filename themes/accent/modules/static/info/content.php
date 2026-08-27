<?php defined('ENVIRONMENT') OR die('Invalid access'); ?>
<?php themeFunctions::debugFileLine('start'); ?>
<p id="servUtile">INFO UTILE | BUSINESS TRAVEL</p>
<div id="utileVacanta">
<?php 
$this->_ci->load->model('Options_model');
$menu = $this->_ci->Options_model->get('trip_cms_menu', 'info_utile_coloana_1');
$this->_ci->load->library('FrontendMenuItem',array('key' => 'info_utile_coloana_1', 'children' => $menu),'frontend_menu_items_info_utile_coloana_1');
$this->_ci->frontend_menu_items_info_utile_coloana_1->render_style_info();

$menu = $this->_ci->Options_model->get('trip_cms_menu', 'info_utile_coloana_2');
$this->_ci->load->library('FrontendMenuItem',array('key' => 'info_utile_coloana_2', 'children' => $menu),'frontend_menu_items_info_utile_coloana_2');
$this->_ci->frontend_menu_items_info_utile_coloana_2->render_style_info();
?>
  <img src="<?php echo $this->theme_url; ?>assets/images/arrowUp.png" alt="arrowUp" />
</div>	
<?php themeFunctions::loadAddons(__FILE__); ?>
<?php themeFunctions::debugFileLine('end'); ?>