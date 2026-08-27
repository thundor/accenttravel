<?php
defined('ENVIRONMENT') OR die('Invalid access');
themeFunctions::includeAddon('tooltip');
themeFunctions::includeAddon('datepicker');
themeFunctions::includeAddon('font-icons/font-awesome');
/* $include_path must be defined in caller */
themeFunctions::addIncludePath($include_path, __DIR__ . '/form.php');
themeFunctions::addIncludePath('includes/body/scripts.php', __DIR__ . '/scripts.php');
themeFunctions::addIncludePath('includes/head/stylesheets.php', __DIR__ . '/stylesheets.php');

$this->_ci->load->model('Trip/Flights_model');
$flights_search_data = $this->_ci->Flights_model->getSearchData();
$this->flights_search_data = &$flights_search_data;

$special_layout = $this->_controller=='Flights';
$n = $this->_ci->input->get('n');
$flights_search_data = $this->_ci->Flights_model->getSearchData();
if($special_layout){
  if(!empty($_GET)){
    $flights_search_data = $this->_ci->Flights_model->getSearchDefaultData();
  }
  $origin = $this->_ci->uri->segment(3);
  if($origin){
    $_GET['origin'] = str_replace('_', ' ',$origin);
    $destination = $this->_ci->uri->segment(4);
    if($destination){
      $_GET['destination'] = str_replace('_', ' ',$destination);
    }
  }
  $filters = array();
	$start_date = $this->_ci->input->get('sdate');
  $d2 = null;
  if(isset($start_date)){
		try{
			$d2 = new DateTime($start_date);
			$flights_search_data['departure_date'] = $d2->format('Y-m-d');
			$flights_search_data['go_only'] = true;
		} catch(Exception $e){}
  }
	$end_date = $this->_ci->input->get('edate');
  if(isset($end_date)){
    if(!isset($d2) || !$d2){
      $d2 = new DateTime('today midnight');
    }
		try{
			$d2->modify($end_date);
			$flights_search_data['return_date'] = $d2->format('Y-m-d');
      $flights_search_data['go_only'] = false;
		} catch(Exception $e){}
  }
  
	$class = $this->_ci->input->get('class');
  if(isset($class) && is_numeric($class) && isset($this->_ci->Flights_model->cabin_type_map[(int)$class])){
    $flights_search_data['cabine_type'] = (int)$class;
  }
	$direct = $this->_ci->input->get('direct');
  if(isset($direct)){
    $flights_search_data['direct_only'] = filter_var($direct, FILTER_VALIDATE_BOOLEAN);
  }
	$go = $this->_ci->input->get('go');
  if(isset($go)){
    $flights_search_data['go_only'] = filter_var($go, FILTER_VALIDATE_BOOLEAN);
  }
	$flex = $this->_ci->input->get('flex');
  if(isset($flex)){
    $flights_search_data['flexible_dates'] = filter_var($flex, FILTER_VALIDATE_BOOLEAN);
  }
	$a = $this->_ci->input->get('a');
  if(isset($a) && is_numeric($a)){
    $flights_search_data['passengers_adult'] = (int)$a;
    if($flights_search_data['passengers_adult'] < 1){
      $flights_search_data['passengers_adult'] = 1;
    } elseif($flights_search_data['passengers_adult'] > 6){
      $flights_search_data['passengers_adult'] = 6;
    }
  }
	$s = $this->_ci->input->get('s');
  if(isset($s) && is_numeric($s)){
    $flights_search_data['passengers_senior'] = (int)$s;
    if($flights_search_data['passengers_senior'] < 0){
      $flights_search_data['passengers_senior'] = 0;
    } elseif($flights_search_data['passengers_senior'] > 6){
      $flights_search_data['passengers_senior'] = 6;
    }
  }
  $total_adults = $flights_search_data['passengers_adult'] + $flights_search_data['passengers_senior'];
  $c = $this->_ci->input->get('c');
  
  $flights_search_data['varste_copii'] = array();
  $flights_search_data['passengers_infant_lap'] = 0;
  $flights_search_data['passengers_infant_seat'] = 0;
  $flights_search_data['passengers_youth'] = 0;
  $flights_search_data['passengers_child'] = 0;
  if(isset($c)){
    $varste_copii = preg_replace('/[^\d,]/', '', $c);
    $varste_copii = explode(',', $varste_copii);
    foreach($varste_copii as $varsta_copil){
      $varsta_copil = (int)$varsta_copil;
      if($varsta_copil > 17){
        $varsta_copil = 17;
      }
      if(!in_array($varsta_copil,$flights_search_data['varste_copii'])){
        if($varsta_copil < 3){
          if($total_adults){
            $total_adults --;
            $flights_search_data['passengers_infant_lap']++;
          } else {
            $flights_search_data['passengers_infant_seat']++;
          }
        } else {
          $flights_search_data['passengers_child']++;
        }
        $flights_search_data['varste_copii'][] = $varsta_copil;
      }
    }
  }
  foreach(array('origin', 'destination') as $param){
    $g_param = $this->_ci->input->get($param);
    if(isset($g_param)){
      $this->_ci->load->model('Trip/Flights_model');
      $response = $this->_ci->Flights_model->loadLocations($g_param);
      if($response){
        $first_location = $response[0];
        $flights_search_data[$param . '_' . 'city_id'] = $first_location->CityId;
        $flights_search_data[$param . '_' . 'country_id'] = $first_location->CountryId;
        $flights_search_data[$param . '_' . 'location_id'] = isset($first_location->LocationId) ? $first_location->LocationId : 0;
        $flights_search_data[$param . '_' . 'location_name'] = isset($first_location->LocationName) ? $first_location->LocationName : '';
        $flights_search_data[$param . '_' . 'city_name'] = $first_location->CityName;
        $flights_search_data[$param . '_' . 'country_name'] = $first_location->CountryName;
        $flights_search_data[$param . '_' . 'full_location_name'] = ($first_location->LocationId ? $first_location->LocationName . ', ' : '') . $first_location->CityName;
      }
    }
  }
}
if($_GET){
  $flights_search_data['index_id'] = null;
}
$this->flights_search_data = &$flights_search_data;