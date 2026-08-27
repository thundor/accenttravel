<?php
defined('ENVIRONMENT') OR die('Invalid access');
/* $include_path must be defined in caller */
themeFunctions::addIncludePath($include_path, __DIR__ . '/form.php');
themeFunctions::addIncludePath('includes/body/scripts.php', __DIR__ . '/scripts.php');
themeFunctions::addIncludePath('includes/head/stylesheets.php', __DIR__ . '/stylesheets.php');
$this->_ci->load->model('Paralela45/Paralela45_Strainatate_model');
$this->_ci->load->model('Paralela45_model');
$product = $this->view_data['product'];
$search = $this->view_data['search'];
$paralela45_circuit_search_data = $this->_ci->Paralela45_Circuit_model->getSearchData();
if($product->CityCode !== $paralela45_circuit_search_data['destination'] || $product->CountryCode !== $paralela45_circuit_search_data['country']){
  $paralela45_circuit_search_data = $this->_ci->Paralela45_Circuit_model->getSearchDefaultData();
}
$paralela45_circuit_search_data['destination'] = $product->CityCode;
$paralela45_circuit_search_data['country'] = $product->CountryCode;
$departure_city_codes = array_keys($search['CityLinks']['Departure']);
$paralela45_circuit_search_data['origin'] = array_shift($departure_city_codes);
$this->getCircuitSearchCityResponse = $search;
$this->paralela45_circuit_search_data = &$paralela45_circuit_search_data;