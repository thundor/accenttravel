<?php
defined('ENVIRONMENT') OR die('Invalid access');
themeFunctions::includeAddon('tooltip');
themeFunctions::includeAddon('datepicker');
themeFunctions::includeAddon('font-icons/font-awesome');
/* $include_path must be defined in caller */
themeFunctions::addIncludePath($include_path, __DIR__ . '/form.php');
themeFunctions::addIncludePath('includes/body/scripts.php', __DIR__ . '/scripts.php');
themeFunctions::addIncludePath('includes/head/stylesheets.php', __DIR__ . '/stylesheets.php');

$this->_ci->load->model('Trip/Hotels_model');
$special_layout = $this->_controller=='Hotelsasync';
$hotel_search_data = $this->_ci->Hotels_model->getSearchData();
if($special_layout){
  if(!empty($_GET)){
    $hotel_search_data = $this->_ci->Hotels_model->getSearchDefaultData();
  }
  $city_name = $this->_ci->uri->segment(3);
  if($city_name){
    $_GET['city_name'] = str_replace('_', ' ',$city_name);
  }
  $filters = array();
  $a = $this->_ci->input->get('a');
  if(isset($a) && is_string($a)){
    $this->_ci->load->model('Trip/Hotel_activities_model');
    $activity_categories = explode(',', '' . $a);
    $activities = $this->_ci->Hotel_activities_model->getActivitiesByCategories($activity_categories);
    $activity_ids = array();
    foreach($activities as $activity){
      $activity_ids[] = $activity->id;
    }
    $filters['activity_categories'] = $activity_categories;
    $filters['activities'] = $activity_ids;
  }
  $hotel_name = $this->_ci->input->get('hotel');
  if(isset($hotel_name) && is_string($hotel_name)){
    $hotel_search_data['hotel_name'] = trim($hotel_name);
  }
  $s = $this->_ci->input->get('s');
  if(isset($s) && is_string($s)){
    $filters['stars'] = explode(',', '' . $s);
  }
  $p = $this->_ci->input->get('p');
  if(isset($p) && is_string($p)){
    $filters['pois'] = explode(',', '' . $p);
  }
  $f = $this->_ci->input->get('f');
  if(isset($f) && is_string($f)){
    $filters['facilities'] = explode(',', '' . $f);
  }
  $hotel_search_data['filters'] = $filters;
	$start_date = $this->_ci->input->get('sdate');
  $d2 = null;
  if(isset($start_date)){
		try{
			$d2 = new DateTime($start_date);
			$hotel_search_data['start_date'] = $d2->format('Y-m-d');
		} catch(Exception $e){}
  }
	$end_date = $this->_ci->input->get('edate');
  if(isset($end_date)){
    if(!isset($d2) || !$d2){
      $d2 = new DateTime('today midnight');
    }
		try{
			$d2->modify($end_date);
			$hotel_search_data['end_date'] = $d2->format('Y-m-d');
		} catch(Exception $e){}
  }
	$occupancy = $this->_ci->input->get('o');
  if(isset($occupancy) && is_array($occupancy)){
    $hotel_search_data['occupancy'] = $occupancy;
  }
	// $city_id = $this->_ci->input->get('city_id');
  // if(isset($city_id)){
    // $hotel_search_data['city_id'] = (int)$city_id;
  // }
	$city_name = $this->_ci->input->get('city_name');
  if(isset($city_name)){
    $response = $this->_ci->Hotels_model->loadLocations($city_name);
    if($response){
      $first_city = $response[0];
      $hotel_search_data['city_id'] = $first_city->CityId;
      $hotel_search_data['city_code'] = $first_city->CityCode;
      $hotel_search_data['city_name'] = $first_city->Name;
      $hotel_search_data['country_id'] = $first_city->CountryId;
      $hotel_search_data['country_name'] = $first_city->CountryName;
    }
  }
}
if($_GET){
  $hotel_search_data['index_id'] = null;
}
$this->hotel_search_data = &$hotel_search_data;