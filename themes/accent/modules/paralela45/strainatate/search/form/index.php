<?php
defined('ENVIRONMENT') OR die('Invalid access');
/* $include_path must be defined in caller */
themeFunctions::addIncludePath($include_path, __DIR__ . '/form.php');
themeFunctions::addIncludePath('includes/body/scripts.php', __DIR__ . '/scripts.php');
themeFunctions::addIncludePath('includes/head/stylesheets.php', __DIR__ . '/stylesheets.php');

$this->_ci->load->model('Paralela45/Paralela45_Strainatate_model');
$this->_ci->load->model('Paralela45_model');
$paralela45_strainatate_search_data = $this->_ci->Paralela45_Strainatate_model->getSearchData();
$this->getPackageNVRoutesResponse = $this->_ci->Paralela45_model->getPackageNVRoutes();
$this->getPackageNVRoutesRequest = $this->_ci->Paralela45_model->getPackageNVRoutesRequest();
$paralela45_special_layout = $this->_ci->uri->segment(0) === 'paralela45';
if($paralela45_special_layout){
  $origin = $this->_ci->uri->segment(3);
  if(isset($origin)){
    $_GET['origin'] = null;
    $_GET['destination'] = null;
    
    if(isset($this->getPackageNVRoutesResponse['Aliases'][$origin])){
      $origin_city_codes = array_intersect($this->getPackageNVRoutesResponse['Aliases'][$origin], array_keys($this->getPackageNVRoutesResponse['CityLinks']['Departure']));
      if($origin_city_codes){
        $origin_city_code = array_shift($origin_city_codes);
        $_GET['origin'] = $origin_city_code;
        $destination = $this->_ci->uri->segment(4);
        if(isset($destination)){
          if(isset($this->getPackageNVRoutesResponse['Aliases'][$destination])){
            $destination_city_codes = array_intersect($this->getPackageNVRoutesResponse['Aliases'][$destination], $this->getPackageNVRoutesResponse['CityLinks']['Departure'][$origin_city_code]);
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
        if(isset($this->getPackageNVRoutesResponse['Aliases'][$destination])){
          $destination_city_codes = array_intersect($this->getPackageNVRoutesResponse['Aliases'][$destination], array_keys($this->getPackageNVRoutesResponse['CityLinks']['Destination']));
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
  $paralela45_strainatate_search_data = $this->_ci->Paralela45_Strainatate_model->getSearchDefaultData();
}
if($paralela45_special_layout){
  $origin = $this->_ci->input->get('origin');
  if(isset($origin)){
    $paralela45_strainatate_search_data['origin'] = '' . $origin;
  }
  $destination = $this->_ci->input->get('destination');
  if(isset($destination)){
    $paralela45_strainatate_search_data['destination'] = '' . $destination;
  }
  $hotel = $this->_ci->input->get('hotel');
  if(isset($hotel)){
    $paralela45_strainatate_search_data['hotel_name'] = $hotel;
  }
  $nights = $this->_ci->input->get('nights');
  if(isset($nights)){
    $paralela45_strainatate_search_data['nights'] = (int)$nights;
  }
  $occupancy = $this->_ci->input->get('o');
  if(isset($occupancy) && is_array($occupancy)){
    $paralela45_strainatate_search_data['occupancy'] = $occupancy;
  }
  $start_date = $this->_ci->input->get('sdate');
  if(isset($start_date)){
    try{
      $d2 = new DateTime($start_date);
      $paralela45_strainatate_search_data['start_date'] = $d2->format('Y-m-d');
    } catch(Exception $e){}
  }
}
$this->paralela45_strainatate_search_data = &$paralela45_strainatate_search_data;