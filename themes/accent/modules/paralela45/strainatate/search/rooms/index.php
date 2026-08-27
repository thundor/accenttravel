<?php
defined('ENVIRONMENT') OR die('Invalid access');
/* $include_path must be defined in caller */
themeFunctions::addIncludePath($include_path, __DIR__ . '/form.php');
themeFunctions::addIncludePath('includes/body/scripts.php', __DIR__ . '/scripts.php');
themeFunctions::addIncludePath('includes/head/stylesheets.php', __DIR__ . '/stylesheets.php');

$this->_ci->load->model('Paralela45/Paralela45_Strainatate_model');
$this->_ci->load->model('Paralela45_model');
$paralela45_strainatate_search_data = $this->_ci->Paralela45_Strainatate_model->getSearchData();
$product = $this->view_data['product'];
$paralela45_strainatate_search_data['destination'] = $product->CityCode;
$this->getPackageNVRoutesResponse = $this->_ci->Paralela45_model->getPackageNVRoutes();
$this->paralela45_strainatate_search_data = &$paralela45_strainatate_search_data;