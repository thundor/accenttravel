<?php defined('ENVIRONMENT') OR die('Invalid access'); ?>
<?php themeFunctions::includeAddon('lazy-loading'); ?>
<?php 
$hotel_details = $this->view_data['hotel_details'];
$this->_ci->load->model('Trip/Hotels_model');
$n = $this->_ci->input->get('n');
if($n){
  $hotel_search_data = $this->_ci->Hotels_model->getSearchData(0);
  $hotel_search_data['hotel_id'] = $hotel_details->Id;
} else {
  $hotel_search_data = $this->_ci->Hotels_model->getSearchData($hotel_details->Id);
}

$this->_ci->Hotels_model->setSearchData($hotel_search_data);

$this->hotel_search_data = &$hotel_search_data;

themeFunctions::addIncludePath('includes/body/scripts.php', __DIR__ . '/index/facilities_scripts.php');
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