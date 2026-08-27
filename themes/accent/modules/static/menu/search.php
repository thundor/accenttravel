<?php defined('ENVIRONMENT') OR die('Invalid access'); ?>
<?php 
$this->_ci->load->model('Options_model');
$menu = $this->_ci->Options_model->get('trip_cms_menu', 'navigatie_principal_dreapta');
$this->_ci->load->library('FrontendMenuItem',array('key' => 'navigatie_principal_dreapta', 'children' => $menu, 'ul_class'=>'navbar-nav ml-0'),'frontend_menu_items_navigatie_principal_dreapta');
$this->_ci->frontend_menu_items_navigatie_principal_dreapta->render_style_main();
?>
<?php /* <ul class="navbar-nav mr-0">
  <li class="nav-item <?php echo $this->_ci->uri->uri_string() == 'inregistrare-concurs' ? 'active' : ''; ?>">
    <a class="nav-link" href="<?php echo site_url('inregistrare-concurs'); ?>">Inregistrare la concurs</a>
  </li>
</ul> */ ?>
<?php /* <form class="form-inline mt-2 mt-md-0">
  <input class="form-control mr-sm-2" type="text" placeholder="Vreau sa calatoresc in…">
  <button class="btn btn-outline-success my-2 my-sm-0" type="submit">Cauta</button>
  <?php themeFunctions::loadAddons(__FILE__); ?>
</form> */ ?>
<?php themeFunctions::debugFileLine('end'); ?>