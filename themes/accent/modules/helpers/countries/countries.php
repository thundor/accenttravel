<?php defined('ENVIRONMENT') OR die('Invalid access'); ?>
<?php themeFunctions::debugFileLine('start'); ?>
<?php 
$this->_ci->load->model('Country_model');
$filters = array(
  'select' => array('iso_2 as code','IFNULL(`name_RO`,`name`) as text, phone_prefix as prefix, country_id'),
  'status' => 1,
  'return_rows' => 1,
  'ordering' => 'name ASC',
);
$result_countries = $this->_ci->Country_model->getCountries($filters);
$countries = array();
foreach($result_countries as $country){
  $countries[$country->code] = $country;
}
$this->countries_selections = &$countries;
?>
<?php themeFunctions::loadAddons(__FILE__); ?>
<?php themeFunctions::debugFileLine('end'); ?>