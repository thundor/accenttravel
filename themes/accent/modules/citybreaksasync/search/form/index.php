<?php
defined('ENVIRONMENT') OR die('Invalid access');
themeFunctions::includeAddon('tooltip');
themeFunctions::includeAddon('datepicker');
themeFunctions::includeAddon('font-icons/font-awesome');
/* $include_path must be defined in caller */
themeFunctions::addIncludePath($include_path, __DIR__ . '/form.php');
themeFunctions::addIncludePath('includes/body/scripts.php', __DIR__ . '/scripts.php');
themeFunctions::addIncludePath('includes/head/stylesheets.php', __DIR__ . '/stylesheets.php');

$this->_ci->load->model('Trip/Citybreaks_model');

$special_layout = $this->_controller=='Citybreaksasync';
$citybreak_search_data = $this->_ci->Citybreaks_model->getSearchData();
if($special_layout){
  if(!empty($_GET)){
    $citybreak_search_data = $this->_ci->Citybreaks_model->getSearchDefaultData();
  }
  $origin = $this->_ci->uri->segment(3);
  if($origin){
    $_GET['origin'] = str_replace('_', ' ',$origin);
  }
  $destination = $this->_ci->uri->segment(4);
  if($destination){
    $_GET['destination'] = str_replace('_', ' ',$destination);
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
    $citybreak_search_data['hotel_name'] = trim($hotel_name);
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
  $citybreak_search_data['filters'] = $filters;
	$start_date = $this->_ci->input->get('sdate');
  $d2 = null;
  if(isset($start_date)){
		try{
			$d2 = new DateTime($start_date);
			$citybreak_search_data['start_date'] = $d2->format('Y-m-d');
		} catch(Exception $e){}
  }
	$end_date = $this->_ci->input->get('edate');
  if(isset($end_date)){
    if(!isset($d2) || !$d2){
      $d2 = new DateTime('today midnight');
    }
		try{
			$d2->modify($end_date);
			$citybreak_search_data['end_date'] = $d2->format('Y-m-d');
		} catch(Exception $e){}
  }
	$occupancy = $this->_ci->input->get('o');
  if(isset($occupancy) && is_array($occupancy)){
    $citybreak_search_data['occupancy'] = $occupancy;
  }
  foreach(array('origin', 'destination') as $param){
    $g_param = $this->_ci->input->get($param);
    if(isset($g_param)){
      $this->_ci->load->model('Trip/Flights_model');
      $response = $this->_ci->Flights_model->loadLocations($g_param);
      if($response){
        $first_location = $response[0];
        $citybreak_search_data[$param . '_' . 'city_id'] = $first_location->CityId;
        $citybreak_search_data[$param . '_' . 'country_id'] = $first_location->CountryId;
        $citybreak_search_data[$param . '_' . 'location_id'] = isset($first_location->LocationId) ? $first_location->LocationId : 0;
        $citybreak_search_data[$param . '_' . 'location_name'] = isset($first_location->LocationName) ? $first_location->LocationName : '';
        $citybreak_search_data[$param . '_' . 'city_name'] = $first_location->CityName;
        $citybreak_search_data[$param . '_' . 'country_name'] = $first_location->CountryName;
        $citybreak_search_data[$param . '_' . 'full_location_name'] = ($first_location->LocationId ? $first_location->LocationName . ', ' : '') . $first_location->CityName;
      }
    }
  }
}
if($_GET){
  $citybreak_search_data['index_id'] = null;
}
$this->citybreak_search_data = &$citybreak_search_data;
// echo '<pre>';
// print_r($this->citybreak_search_data);
// die;