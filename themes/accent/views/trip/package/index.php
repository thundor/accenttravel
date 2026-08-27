<?php defined('ENVIRONMENT') OR die('Invalid access'); ?>
<?php themeFunctions::includeAddon('lazy-loading'); ?>
<?php 
$package_details = $this->view_data['package_details'];
$this->_ci->load->model('Trip/Packages_model');
$n = $this->_ci->input->get('n');
if($n){
  $package_search_data = $this->_ci->Packages_model->getSearchData(0);
  $package_search_data['package_id'] = $package_details->Id;
} else {
  $package_search_data = $this->_ci->Packages_model->getSearchData($package_details->Id);
}

$this->_ci->Packages_model->setSearchData($package_search_data);

$this->package_search_data = &$package_search_data;

themeFunctions::includeAddon('forms-validation');
themeFunctions::includeAddon('datepicker');
themeFunctions::loadLang('package');
themeFunctions::addIncludePath('includes/body/scripts.php', __DIR__ . '/index/scripts.php');
themeFunctions::addIncludePath('includes/head/stylesheets.php', __DIR__ . '/index/stylesheets.php');
themeFunctions::addIncludePath('modules/head/meta.php', __DIR__ . '/index/meta.php');

themeFunctions::loadAddons(__FILE__);
themeFunctions::debugFileLine('start'); ?>
<div class="container">
  <?php include 'index/breadcrumbs.php'; ?>
  <?php include 'index/details.php'; ?>
</div>
<?php themeFunctions::debugFileLine('end'); ?>