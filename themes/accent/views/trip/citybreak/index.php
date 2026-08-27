<?php defined('ENVIRONMENT') OR die('Invalid access'); ?>
<?php themeFunctions::includeAddon('lazy-loading'); ?>
<?php 
$hotel_details = $this->view_data['hotel_details'];
$this->_ci->load->model('Trip/Citybreaks_model');
$n = $this->_ci->input->get('n');
if($n){
  $citybreak_search_data = $this->_ci->Citybreaks_model->getSearchData(0);
  $citybreak_search_data['hotel_id'] = $hotel_details->Id;
} else {
  $citybreak_search_data = $this->_ci->Citybreaks_model->getSearchData($hotel_details->Id);
}
$citybreak_search_data['flight_code'] = $this->view_data['flight_code'];
$citybreak_search_data['itinerary_code'] = $this->view_data['itinerary_code'];

$this->_ci->Citybreaks_model->setSearchData($citybreak_search_data);

$this->citybreak_search_data = &$citybreak_search_data;
// echo '<pre>';
// print_R($this->citybreak_search_data);
// die;
themeFunctions::addIncludePath('includes/body/scripts.php', __DIR__ . '/../hotel/index/facilities_scripts.php');
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