<?php
defined('ENVIRONMENT') OR die('Invalid access');
/* $include_path must be defined in caller */
themeFunctions::addIncludePath($include_path, __DIR__ . '/form.php');
themeFunctions::addIncludePath('includes/body/scripts.php', __DIR__ . '/scripts.php');
themeFunctions::addIncludePath('includes/head/stylesheets.php', __DIR__ . '/stylesheets.php');

$this->_ci->load->model('Paralela45/Paralela45_Circuit_model');
$this->_ci->load->model('Paralela45_model');
$paralela45_circuit_search_data = $this->_ci->Paralela45_Circuit_model->getSearchData();
$this->getCircuitSearchCityResponse = $this->_ci->Paralela45_model->getCircuitSearchCity();
$paralela45_special_layout = $this->_ci->uri->segment(0) === 'paralela45';
if($paralela45_special_layout){
  $origin = $this->_ci->uri->segment(3);
  if(isset($origin)){
    $_GET['origin'] = null;
    $_GET['destination'] = null;
    
    if(isset($this->getCircuitSearchCityResponse['Aliases'][$origin])){
      $origin_city_codes = array_intersect($this->getCircuitSearchCityResponse['Aliases'][$origin], array_keys($this->getCircuitSearchCityResponse['CityLinks']['Departure']));
      if($origin_city_codes){
        $origin_city_code = array_shift($origin_city_codes);
        $_GET['origin'] = $origin_city_code;
        $destination = $this->_ci->uri->segment(4);
        if(isset($destination)){
          if(isset($this->getCircuitSearchCityResponse['Aliases'][$destination])){
            $destination_city_codes = array_intersect($this->getCircuitSearchCityResponse['Aliases'][$destination], $this->getCircuitSearchCityResponse['CityLinks']['Departure'][$origin_city_code]);
            if($destination_city_codes){
              $destination_city_code = array_shift($destination_city_codes);
              $_GET['destination'] = $destination_city_code;
            }
          }
        }
      }
    } else {
      $destination = $this->_ci->uri->segment(4);
      if(isset($destination)){
        if(isset($this->getCircuitSearchCityResponse['Aliases'][$destination])){
          $destination_city_codes = array_intersect($this->getCircuitSearchCityResponse['Aliases'][$destination], array_keys($this->getCircuitSearchCityResponse['CityLinks']['Destination']));
          if($destination_city_codes){
            $destination_city_code = array_shift($destination_city_codes);
            $_GET['destination'] = $destination_city_code;
          }
        }
      }
    }
  }
}
if($paralela45_special_layout && !empty($_GET)){
  $paralela45_circuit_search_data = $this->_ci->Paralela45_Circuit_model->getSearchDefaultData();
}
if($paralela45_special_layout){
  $origin = $this->_ci->input->get('origin');
  if(isset($origin)){
    $paralela45_circuit_search_data['origin'] = '' . $origin;
  }
  $destination = $this->_ci->input->get('destination');
  if(isset($destination)){
    $paralela45_circuit_search_data['destination'] = '' . $destination;
  }
  $country = $this->_ci->input->get('country');
  if(isset($country)){
    $paralela45_circuit_search_data['country'] = $country;
  }
  $hotel = $this->_ci->input->get('hotel');
  if(isset($hotel)){
    $paralela45_circuit_search_data['hotel_name'] = $hotel;
  }
  $hotel = $this->_ci->input->get('hotel');
  if(isset($hotel)){
    $paralela45_circuit_search_data['hotel_name'] = $hotel;
  }
  $nights = $this->_ci->input->get('nights');
  if(isset($nights)){
    $paralela45_circuit_search_data['nights'] = (int)$nights;
  }
  $occupancy = $this->_ci->input->get('o');
  if(isset($occupancy) && is_array($occupancy)){
    $paralela45_circuit_search_data['occupancy'] = $occupancy;
  }
  $start_date = $this->_ci->input->get('sdate');
  if(isset($start_date)){
    try{
      $d2 = new DateTime($start_date);
      $paralela45_circuit_search_data['start_date'] = $d2->format('Y-m-d');
    } catch(Exception $e){}
  }
}
$this->paralela45_circuit_search_data = &$paralela45_circuit_search_data;