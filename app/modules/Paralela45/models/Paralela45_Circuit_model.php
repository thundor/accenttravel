<?php

class Paralela45_Circuit_model extends CI_Model {

  public $errors = array();
  public $api;
  public $max_stars = 7;
  public $max_rooms = 3;
  public $max_adults_per_room = 6;
  public $max_children_per_room = 2;
  public $max_child_age = 18;
  public $sort_orders = array(0, 1);
  public $sort_types = array('MinPrice', 'Stars', 'Name');

  function __construct() {
    parent::__construct();
    $this->load->model('Paralela45_model');
  }

  function setSearchData($data) {
    if(isset($data['ignore_session']) && $data['ignore_session']){
      return;
    }
    $session = '';
    if(isset($data['session'])){
      $session =  is_string($data['session']) ? trim($data['session']) : '';
      unset($data['session']);
    }
    if(isset($data['package_id']) && $data['package_id']){
      $this->session->set_userdata('paralela45/circuit/search_data' . $session, $data);
    } else {
      $this->session->set_userdata('paralela45/circuit/search_data' . $session, $data);
    }
  }

  function getSearchData($package_id = 0, $session = true) {
    $default_data = $this->getSearchDefaultData();
    $data = array();
    if($session){
      if($package_id){
        $data = $this->session->userdata('paralela45/circuit/search_data' . (is_string($session) ? trim($session) : ''));
        if(!$data || ($data['package_id'] != $package_id)){
          $data = $this->session->userdata('paralela45/circuit/search_data' . (is_string($session) ? trim($session) : ''));
        }
      } else {
        $data = $this->session->userdata('paralela45/circuit/search_data' . (is_string($session) ? trim($session) : ''));
      }
      if (!$data) {
        $data = array();
      }
      if(is_string($session)){
        $data['session'] = $session;
      }
    } else {
      $data['ignore_session'] = true;
    }
    if($package_id){
      // if(!isset($data['package_id']) || !$data['package_id']){
        // $data['index_id'] = '';
        // $data['code'] = '';
      // }
      // if(!isset($data['start_date']) || !$data['start_date'] || $data['start_date'] < date('Y-m-d')){
        // $data['start_date'] = date('Y-m-d');
      // }
      // if(!isset($data['end_date']) || !$data['end_date'] || $data['end_date'] < $data['start_date']){
        // $data['end_date'] = date("Y-m-d", strtotime("+1 year"));
      // }
      $data['package_id'] = $package_id;
    }    
    if($this->config->item('csrf_protection') === TRUE){
      $data[$this->security->get_csrf_token_name()] = $this->security->get_csrf_hash();
    }
    return array_replace_recursive($default_data, $data);
  }

  function getSearchDefaultData() {
    $default_data = array(
      'origin' => '',
      'destination' => '',
      'country' => '',
      'start_date' => '',
      'hotel_name' => '',
      'nights' => '',
      'occupancy' => array(
        array(
          'adt' => 2
        )
      ),
      // 'package_id' => null,
      'page' => 1,
      'sort_by' => 'MinPrice',
      'sort_order' => 0
    );
    if($this->config->item('csrf_protection') === TRUE){
      $default_data[$this->security->get_csrf_token_name()] = $this->security->get_csrf_hash();
    }
    return $default_data;
  }
  function getDepartureCity(&$routes, $departure_city_code) {
    if(!$departure_city_code){
      return null;
    }
    if(isset($routes['Cities'][$departure_city_code], $routes['CityLinks']['Departure'][$departure_city_code])){
      return $routes['Cities'][$departure_city_code];
    }
    return null;
  }
  function getDestinationCity(&$routes, $departure_city_code, $destination_city_code) {
    if(isset($routes['Cities'][$destination_city_code], $routes['CityLinks']['Destination'][$destination_city_code]) && (!$departure_city_code || in_array($destination_city_code,$routes['CityLinks']['Departure'][$departure_city_code]))){
      return $routes['Cities'][$destination_city_code];
    }
    return null;
  }
  function getRouteDate(&$routes, &$data) {
    if(!isset($data['checkin']) || !strlen($data['checkin'])){
      return null;
    }
    if(!isset($data['departure_city_code']) || !strlen($data['departure_city_code'])){
      return null;
    }
    if(!isset($data['destination_city_code']) || !strlen($data['destination_city_code'])){
      return null;
    }
    $departure_city_code = $data['departure_city_code'];
    $destination_city_code = $data['destination_city_code'];
    
    /* if(!isset($data['nights']) && isset($data['checkout']) && strlen($data['checkout'])){
      $checkin_date = DateTime::createFromFormat('Y-m-d', $data['checkin']);
      $checkout_date = DateTime::createFromFormat('Y-m-d', $data['checkout']);
      $nights_date = $checkout_date->diff($checkin_date);
      $data['nights'] = intval($nights_date->format('%a'));
    }
    if(isset($data['nights']) && strlen($data['nights']) && (!isset($data['checkout']) || !strlen($data['checkout']))){
      $checkin_date = DateTime::createFromFormat('Y-m-d', $data['checkin']);
      $checkin_date->modify('+' . intval($data['nights']) . ' days');
      $data['checkout'] = $checkin_date->format('Y-m-d');
    } */
    $city_codes = array();
    $city_codes[] = $destination_city_code;
    foreach($city_codes as $city_code){
      if(!isset($routes['Dates'][$departure_city_code][$city_code][$data['checkin']])){
        continue;
      }
      $route_date = $routes['Dates'][$departure_city_code][$city_code][$data['checkin']];
      $allowed_nights = explode(',', $route_date['Nights']);
      if(!in_array('' . $data['nights'], $allowed_nights)){
        continue;
      }
      $data['route_date'] = $route_date;
      $data['tour_op_code'] = $route_date['TourOpCode'];
      return $route_date;
    }
    return null;
  }
  function interpretCircuitSearchRequest($routes, $request_search_data, $results, $just_offer_id = null, $variant_id = null, $flexible_variant_id = false) {
    $variant_flex = null;
    if(isset($variant_id) && $flexible_variant_id){
      $variant_flex = preg_replace('/^\d+(\_.*)$/','$1$2',$variant_id);
    }
    $return = array();
    $return['products'] = array();
    $return['offers'] = array();
    
    $today = new DateTime();
    
    // $prices = array();
    // $meal_types = array();
    // $service_types = array();
    
    foreach($results as $circuit){
      if(empty($circuit->Variants) || empty($circuit->Variants->Variant)){
        continue;
      }
      $any_offer = false;
      $offer_id = str_replace('|','_', $circuit->CircuitId);
      if(isset($just_offer_id) && ($just_offer_id !== $offer_id)){
        continue;
      }
      foreach($circuit->Variants->Variant as $circuit_offer){
        $block_offline_payments = false;
        $block_online_payments = false;
        if(isset($variant_id) && ($variant_id !== $circuit_offer->UniqueId)){
          if(!$flexible_variant_id){
            continue;
          } else {
            if(!isset($variant_flex) || ($variant_flex !== preg_replace('/^\d+(\_.*)$/','$1$2',$circuit_offer->UniqueId))){
              continue;
            }
          }
        }
        if(in_array($circuit_offer->Availability->Code, array('ST', 'OR'))){
          continue;
        }
        $checkin_date = DateTime::createFromFormat('Y-m-d H:i:s', $circuit_offer->InfoCharter->DepDate);
        $days_till_start = $today->diff($checkin_date);
        $days_till_start_formatted = intval($days_till_start->format('%a'));
        
        // sambata && duminica
        $is_weekend = $today->format('N') >= 6;
        
        // checkin azi sau maine
        if(!$block_offline_payments && ($is_weekend || $days_till_start_formatted < 2)){
          $block_offline_payments = true;
        }
        
        if((!$block_offline_payments || !$block_online_payments) && $circuit_offer->Availability->Code == 'ST'){
          $block_offline_payments = true;
          $block_online_payments = true;
        }
        if(!$block_online_payments && $circuit_offer->Availability->Code == 'OR'){
          $block_online_payments = true;
        }
        $offer = new stdClass;
        $offer->block_online_payments = $block_online_payments;
        $offer->block_offline_payments = $block_offline_payments;
        $offer->block_payments = $offer->block_online_payments && $offer->block_offline_payments;
        if($offer->block_payments && isset($just_offer_id) && isset($variant_id)){
          continue;
        }
        $offer->DepartureCharter = $circuit_offer->DepartureCharter;
        $offer->CircuitId = $circuit->CircuitId;
        $offer->Availability = $circuit_offer->Availability->Code;
        $offer->InfoCharter = $circuit_offer->InfoCharter;
        
        // if(property_exists($circuit_offer,'OfferDescription')){
          // $offer->OfferDescription = $circuit_offer->OfferDescription;
        // }
        $offer->CheckIn = $circuit_offer->InfoCharter->DepDate;
        $offer->CheckOut = $circuit_offer->InfoCharter->RetDate;
        $offer->Currency = $circuit_offer->CurrencyCode;
        
        $offer->Price = floatval($circuit_offer->Gross);
        // $prices[$offer->Price] = true;
        /* if(!empty($circuit_offer->Meals) && !empty($circuit_offer->Meals->Meal)){
          $offer->Meals = array();
          foreach($circuit_offer->Meals->Meal as $offer_meal){
            $meal = new stdClass;;
            $meal->Code = $offer_meal->Code;
            $meal->Type = intval($offer_meal->Type);
            // $meal_types[$meal->Type] = true;
            $meal->Name = $offer_meal->_;
            $meal->CheckIn = $offer_meal->CheckIn;
            $meal->CheckOut = $offer_meal->CheckOut;
            $meal->Provider = $offer_meal->Provider;
            $offer->Meals[] = $meal;
          }
        } */
        $offer->Rooms = array();
        if(!empty($circuit_offer->Rooms) && !empty($circuit_offer->Rooms->Room)){
          foreach($circuit_offer->Rooms->Room as $offer_room){
            $room = new stdClass;;
            $room->Code = $offer_room->Code;
            $room->GCode = $offer_room->GCode;
            // $room->Quantity = $offer_room->Quantity;
            // $room->Provider = $offer_room->Provider;
            $room->Name = $offer_room->_;
            // $room->Price = floatval($offer_room->Gross);
            // $room->ExtraBed = filter_var($offer_room->ExtraBed, FILTER_VALIDATE_BOOLEAN);
            $offer->Rooms[] = $room;
          }
        }
        $offer->Services = array();
        if(!empty($circuit_offer->Services) && !empty($circuit_offer->Services->Service)){
          foreach($circuit_offer->Services->Service as $offer_service){
            // if(!isset($variant_flex) && !in_array($offer_service->Type, array('2', '5', '6', '7', '7s'))){
              // continue;
            // }
            $service = new stdClass;;
            $service->Code = $offer_service->Code;
            $service->Type = $offer_service->Type;
            $service->Availability = $offer_service->Availability->Code;
            $service->Name = $offer_service->Name;
            $service->CheckIn = $offer_service->PeriodOfStay->CheckIn;
            $service->CheckOut = $offer_service->PeriodOfStay->CheckOut;
            $service->Provider = $offer_service->Provider;
            $service->Price = floatval($offer_service->Gross);
            $offer->Services[] = $service;
          }
        }
        $any_offer = true;
        $offer->OfferId = $offer_id;
        $offer->Link = site_url('paralela45/circuit/booking/' . $offer_id);
        $return['offers'][] = $offer;
      }
      if(!$any_offer){
        continue;
      }
      // $circuit->Product;
      $product = new stdClass;
      $product->TourOpCode = $circuit->TourOpCode;
      $product->SearchId = $circuit->SearchId;
      $product->CircuitId = $circuit->CircuitId;
      $product->Name = $circuit->Name;
      $product->Period = $circuit->Period;
      $product->Image = site_url('paralela45/circuit/getCircuitProductImage/' . $offer_id);
      // $product->MinPers = $circuit->MinPers;
      $product->Label = $circuit->Label;
      $product->DayDescriptions = array();
      if(!empty($circuit->DayDescriptions) && !empty($circuit->DayDescriptions->DayDescription)){
        $product->DayDescriptions = $circuit->DayDescriptions->DayDescription;
      }
      $product->Destinations = array();
      if(!empty($circuit->Destinations) && !empty($circuit->Destinations->CircuitDestination)){
        $product->Destinations = $circuit->Destinations->CircuitDestination;
      }
      
      $product->Link = site_url('paralela45/circuit/circuit/' . $offer_id);
      $return['products'][$circuit->CircuitId] = $product;
    }
    return $return;
  }
  function getCircuitSearchRequest($request_search_data) {
    $response = $this->Paralela45_model->CircuitSearchRequest($request_search_data);
    if (!$response) {
      throw new Exception('Paralela45 error: search results returned no response');
      return null;
    }
    if(empty($response->CircuitSearchResponse)){
      throw new Exception('Paralela45 error: search results returned invalid response');
      return null;
    }
    if(empty($response->CircuitSearchResponse->Circuit)){
      return array();
    }
    return $response->CircuitSearchResponse->Circuit;
  }
  function getCircuitSearchRequestSearchData(&$routes, &$data) {
    if(!isset($routes) || !is_array($routes)){
      throw new Exception('Invalid routes');
      return null;
    }
    if(!isset($data) || !is_array($data)){
      throw new Exception('Invalid search data');
      return null;
    }
    $data['departure_city'] = null;
    $data['departure_country_code'] = null;
    if(isset($data['departure_city_code'])){
      $departure_city = $this->getDepartureCity($routes, $data['departure_city_code']);
      if($departure_city){
        $data['departure_city'] = $departure_city;
        $data['departure_country_code'] = $departure_city['CountryCode'];
      }
    } else {
      $data['departure_city_code'] = null;
    }
    $destination_city = $this->getDestinationCity($routes, $data['departure_city_code'], $data['destination_city_code']);
    if($destination_city){
      $data['destination_city'] = $destination_city;
      $data['destination_country_code'] = $destination_city['CountryCode'];
    }
    if(!isset($data['destination_country_code']) || !strlen($data['destination_country_code']) || !isset($routes['Countries'][$data['destination_country_code']])){
      throw new Exception('Invalid country');
      return null;
    }
    
    $route_date = $this->getRouteDate($routes,$data);
    
    if(!isset($data['currency_code'])){
      $data['currency_code'] = 'EUR';
    }
    if(!isset($data['language_code'])){
      $data['language_code'] = 'RO';
    }
    if(!isset($data['room_code_general'])){
      $data['room_code_general'] = 'DB';
    }
    $search_data = array();
    $search_data['Language'] = $data['language_code'];
    $search_data['CurrencyCode'] = $data['currency_code'];
    $search_data['CountryCode'] = $data['destination_country_code'];
    $search_data['Year'] = 13;
    $search_data['Month'] = 13;
    if(!isset($data['occupancy']) || !is_array($data['occupancy'])){
      throw new Exception('Invalid search data');
      return null;
    }
    $data['total_rooms'] = 0;
    $data['total_adults'] = 0;
    $data['total_children'] = 0;
    $data['children_ages'] = array();
    foreach($data['occupancy'] as $room_occupancy){
      $data['total_rooms'] ++;
      $adt = (int)$room_occupancy['adt'];
      if($adt < 1 || $adt > $this->max_adults_per_room){
        $adt = 1;
      }
      $data['total_adults'] += $adt;
      $room = array(
        '@' => array(
          'Code' => $data['room_code_general'],
          'NoAdults' => $adt,
        ),
      );
      if(isset($room_occupancy['chd']) && !empty($room_occupancy['chd'])){
        $room['@']['NoChildren'] = 0;
        $room['Children']['Age'] = array();
        foreach($room_occupancy['chd'] as $child_age){
          $data['total_children'] ++;
          $child_age = (int)$child_age;
          if($child_age < 0 || $child_age >17){
            $child_age = 0;
          }
          $data['children_ages'][] = $child_age;
          $room['Children']['Age'][] = $child_age;
          $room['@']['NoChildren']++;
        }
      }
      $search_data['Rooms']['Room'][] = $room;
    }
    cleanArray($search_data);
    return $search_data;
  }
  function CircuitSearchCityRequest() {
    $response = $this->Paralela45_model->CircuitSearchCityRequest();
    if (!$response) {
      throw new Exception('CircuitSearchCityRequest returned no response');
    }
    if(empty($response->CircuitSearchCityResponse)){
      throw new Exception('CircuitSearchCityRequest returned invalid response');
    }
    $paralela45_data = array(
      'Countries' => array(),
      'Cities' => array(),
      'Aliases' => array(),
    );
    foreach($response->CircuitSearchCityResponse->Country as $response_country){
      if(empty($response_country)){
        continue;
      }
      if(empty($response_country->Cities) || empty($response_country->Cities->City)){
        continue;
      }
      if(!isset($paralela45_data['Countries'][$response_country->CountryCode])){
        $paralela45_data['Countries'][$response_country->CountryCode] = array(
          'CountryName' => $response_country->CountryName,
          'CityCodes' => array(),
        );
      }
      foreach($response_country->Cities->City as $response_destination){
        if(empty($response_destination)){
          continue;
        }
        $paralela45_data['Cities'][$response_destination->CityCode] = array(
          'CityName' => $response_destination->CityName,
          'CountryCode' => $response_country->CountryCode,
        );
        $paralela45_data['Countries'][$response_country->CountryCode]['CityCodes'][] = $response_destination->CityCode;
        $destination_city_alias = trim(preg_replace('/\W+/', '_', strtolower($response_destination->CityName)), ' _');
        $paralela45_data['Aliases'][$destination_city_alias][] = $response_destination->CityCode;
      }
    }
    return $paralela45_data;
  }
  function getProductInfoRequest(&$service_info) {
    $search_data = array();
    $offer_id = $service_info['offer_id'];
    $offer_id_arr = explode('|', $offer_id);
    $service_info['tour_op_code'] = $offer_id_arr[1];
    $service_info['circuit_id'] = $offer_id_arr[0];
    $search_data['TourOpCode'] = $service_info['tour_op_code'];
    $search_data['ProductCode'] = $offer_id;
    $search_data['ProductType'] = 'circuit';
    cleanArray($search_data);
    $response = $this->Paralela45_model->getProductInfoRequest($search_data);
    if (!$response) {
      throw new Exception('Vacanta nu a fost gasita');
    }
    if(empty($response->getProductInfoResponse)){
      throw new Exception('Vacanta invalida');
    }
    if(!empty($response->getProductInfoResponse->Error)){
      throw new Exception('Vacanta invalida: ' . $response->getProductInfoResponse->Error->ErrorId . ' ' . $response->getProductInfoResponse->Error->ErrorText);
    }
    $product_info = $response->getProductInfoResponse->Product;
    $service_info['product_info'] = $product_info;
    $service_info['destination_country_code'] = $product_info->CountryCode ? $product_info->CountryCode : (isset($service_info['destination_country_code']) ? $service_info['destination_country_code'] : '');
    $service_info['destination_city_code'] = $product_info->CityCode ? $product_info->CityCode : (isset($service_info['destination_city_code']) ? $service_info['destination_city_code'] : '');
    
    return $product_info;
  }
  function getCircuitFeesRequest(&$data) {
    $search_data = array(
      'UniqueId' => $data['unique_id'],
    );
    cleanArray($search_data);
    $response = $this->Paralela45_model->CircuitFeesRequest($search_data);
    if(empty($response)){
      return null;
      throw new Exception('CircuitFeesRequest invalid response');
    }
    if(!empty($response->CircuitFeesResponse)){
      if(!empty($response->CircuitFeesResponse->Error)){
        throw new Exception('CircuitFeesResponse error CODE: ' . $response->CircuitFeesResponse->Error->ErrorId . ' ' . $response->CircuitFeesResponse->Error->ErrorText);
      }
      return !empty($response->CircuitFeesResponse->Service) && !empty($response->CircuitFeesResponse->Service) ? $response->CircuitFeesResponse->Service : array();
    }
    return false;
  }
  function getCircuitSearchServiceRequest(&$data) {
    $all_pax_names = array();
    $total_rooms = 0;
    $total_adults = 0;
    $total_children = 0;
    foreach($data['occupancy'] as $k=>$room_occupancy){
      $total_rooms++;
      $room_occupancy = (object)$room_occupancy;
      if(!is_object($room_occupancy)){
        throw new Exception('Invalid room occupancy 1');
      }
      if(!property_exists($room_occupancy, 'adt') || $room_occupancy->adt<1 || $room_occupancy->adt>6){
        throw new Exception('Invalid room occupancy 2');
      }
      for($i = 1; $i <= $room_occupancy->adt; $i++){
        $pax_name = array(
          'PaxType' => 'adult',
          '_' => 'Adult ' . ($total_adults + $i),
        );
        $all_pax_names[] = $pax_name;
      }
      $total_adults += $room_occupancy->adt;
      if(!property_exists($room_occupancy,'chd')){
        continue;
      }
      if(!is_array($room_occupancy->chd)){
        throw new Exception('Invalid room occupancy 3');
      }

      foreach($room_occupancy->chd as $child_age){
        if(!is_numeric($child_age) || ('' . (int)$child_age !== '' . $child_age)){
          throw new Exception('Invalid room occupancy 4');
        }
        if($child_age < 1 || $child_age > 18){
          throw new Exception('Invalid room occupancy 5');
        }
        $total_children++;
        $pax_name = array(
          'PaxType' => 'child',
          'ChildAge' => $child_age,
          '_' => 'Child ' . ($total_adults + $total_children),
        );
        $all_pax_names[] = $pax_name;
      }
    }
    $data['total_rooms'] = $total_rooms;
    $data['total_adults'] = $total_adults;
    $data['total_children'] = $total_children;
    $search_data = array();
    $search_data['CircuitId'] = $data['offer_id'];
    $search_data['CircuitDep'] = $data['charter_id'];
    cleanArray($search_data);
    
    $response = $this->Paralela45_model->CircuitSearchServiceRequest($search_data);
    $extra_services = array();
    if(!empty($response->CircuitSearchServiceResponse)){
      if(!empty($response->CircuitSearchServiceResponse->Error)){
        throw new Exception('CircuitSearchServiceResponse error CODE: ' . $response->CircuitSearchServiceResponse->Error->ErrorId . ' ' . $response->CircuitSearchServiceResponse->Error->ErrorText);
      }
      // Preluare pret servicii suplimentare
      $extra_service_types = !empty($response->CircuitSearchServiceResponse->Services) && !empty($response->CircuitSearchServiceResponse->Services->Service) ? $response->CircuitSearchServiceResponse->Services->Service : array();
      
      if($extra_service_types){
        $search_data = array();
        $search_data['CircuitId'] = $data['offer_id'];
        $search_data['CircuitDep'] = $data['charter_id'];
        $search_data['SuppType'] = 'charters';
        $search_data['CurrencyCode'] = 'EUR';
        $search_data['PaxNames'] = array();
        $search_data['PaxNames']['PaxName'] = $all_pax_names;
        cleanArray($search_data);
        $extra_services = array();
        foreach($extra_service_types as $extra_service_type){
          $type = 'charters';
          if(isset($data['selected_extra_services'])){
            $service_index = $extra_service_type->Type . '-' . $extra_service_type->Code . '-' . $extra_service_type->CharterId;
            if(!in_array($service_index,$data['selected_extra_services'])){
              continue;
            }
          }
          $extra_services[$extra_service_type->Type . '-' . $extra_service_type->Code] = $extra_service_type;
          
          $search_data['Service'] = $extra_service_type->Code;
          if(!filter_var($extra_service_type->HasPrice, FILTER_VALIDATE_BOOLEAN)){
            continue;
          }
          $response = $this->Paralela45_model->CircuitSearchServicePriceRequest($search_data);
          if(empty($response->CircuitSearchServicePriceResponse)){
            throw new Exception('Invalid CircuitSearchServicePriceResponse');
          }
          if(!empty($response->CircuitSearchServicePriceResponse->Error)){
            throw new Exception('CircuitSearchServicePriceResponse error CODE: ' . $response->CircuitSearchServicePriceResponse->Error->ErrorId . ' ' . $response->CircuitSearchServicePriceResponse->Error->ErrorText);
          }
          $extra_service_prices = !empty($response->CircuitSearchServicePriceResponse->Services) && !empty($response->CircuitSearchServicePriceResponse->Services->Service) ? $response->CircuitSearchServicePriceResponse->Services->Service : array();
          
          foreach($extra_service_prices as $extra_service_price){
            $type = 'charters';
            if($extra_service_price->Type !== 'charter'){
              $type = $extra_service_price->Type;
            }
            if(!isset($extra_services[$type . '-' . $extra_service_price->Code])){
              throw new Exception('Missing service price');
            }
            $extra_service = $extra_services[$type . '-' . $extra_service_price->Code];
            $extra_service->price = $extra_service_price;
          }
        }
      }
    }
    return $extra_services;
  }
  public function getBookingService(&$service_info){
    $this->getProductInfoRequest($service_info);
    $service_info['search_id'] = $service_info['package_id'];
    $service_info['charter_id'] = $service_info['package_variant_id'];
    $routes = $this->Paralela45_model->getCircuitSearchCity();
    $request_search_data = $this->getCircuitSearchRequestSearchData($routes, $service_info);
    $results = $this->getCircuitSearchRequest($request_search_data);
    foreach($results as $circuit){
      // if($circuit->SearchId != $service_info['search_id']){
        // continue;
      // }
      if($circuit->CircuitId != $service_info['offer_id']){
        continue;
      }
      $service_info['nights'] = $circuit->Period;
      $service_info['min_pers'] = $circuit->MinPers;
      $service_info['label'] = $circuit->Label;
      foreach($circuit->Variants->Variant as $variant){
        if($variant->DepartureCharter != $service_info['charter_id']){
          continue;
        }
        $service_info['currency_code'] = $variant->CurrencyCode;
        $service_info['checkin'] = $variant->InfoCharter->DepDate;
        $service_info['checkout'] = $variant->InfoCharter->RetArrDate;
        $offer = new stdClass;
        $offer->InfoCharter = $variant->InfoCharter;
        $offer->Availability = $variant->Availability->Code;
        $offer->Rooms = $variant->Rooms->Room;
        $offer->Price = floatval($variant->Gross);
        $offer->Services = $variant->Services->Service;
        $service_info['offer'] = $offer;
      }
    }
    // echo '<pre>';
    // print_R($service_info);
    // print_R($results);
    // die;
    
    $service_info['unique_id'] = $service_info['search_id'] . '_' . $service_info['circuit_id'] . '_' . $service_info['charter_id'] . '|' . $service_info['tour_op_code'];
    $response_cancellation_policies = $this->getCircuitFeesRequest($service_info);
    
    $cancellation_policies = array();
    foreach($response_cancellation_policies as $response_group_cancellation_policy){
      $group_cancellation_policy = array(
        'from_date' => $response_group_cancellation_policy->DStart,
        'to_date' => $response_group_cancellation_policy->DStop,
        'price' => floatval($response_group_cancellation_policy->Amount)+0,
        'percentage' => false,
      );
      $cancellation_policies[] = $group_cancellation_policy;
    }
    $service_info['cancellation_policies'] = $cancellation_policies;
    $extra_services = $this->getCircuitSearchServiceRequest($service_info);
    $service_info['extra_services'] = $extra_services;
    
    $total_price = $service_info['offer']->Price;
    
    if(isset($service_info['extra_services'])){
      foreach($service_info['extra_services'] as $extra_service){
        if(isset($extra_service->price)){
          $total_price += $extra_service->price->Gross;
        }
      }
    }
    $service_info['price'] = $total_price;
  }
  public function bookService($order){
    $services = unserialize($order->services);
    $remote_order_id = 'p45'.$order->id;
    $search_data = array(
      '@' => array(
        'CurrencyCode' => 'EUR',
      ),
      'BookingName' => $remote_order_id,
      'BookingClientId' => $remote_order_id,
      
      'BookingItems' => array(
        'BookingItem' => array(),
      ),
    );
    $updated_services = array();
    foreach($services as $order_service_info){
      $service_info = array();
      $service_info['type'] = $order_service_info['type'];
      $service_info['offer_id'] = $order_service_info['offer_id'];
      $service_info['package_id'] = $order_service_info['package_id'];
      $service_info['package_variant_id'] = $order_service_info['package_variant_id'];
      $service_info['departure_city_code'] = $order_service_info['departure_city_code'];
      $service_info['checkin'] = $order_service_info['checkin'];
      $service_info['checkout'] = $order_service_info['checkout'];
      $service_info['tour_op_code'] = $order_service_info['tour_op_code'];
      $service_info['destination_city_code'] = $order_service_info['destination_city_code'];
      $service_info['product_code'] = $order_service_info['product_code'];
      $service_info['occupancy'] = $order_service_info['occupancy'];
      $service_info['selected_extra_services'] = $order_service_info['selected_extra_services'];
      $service_info['service_rooms'] = $order_service_info['service_rooms'];
      $this->getBookingService($service_info);
      $service_price = $service_info['offer']->Price;
      if(isset($service_info['extra_services'])){
        foreach($service_info['extra_services'] as $extra_service){
          if(isset($extra_service->price)){
            $service_price += $extra_service->price->Gross;
          }
        }
      }
      if($service_price != $order_service_info['price']){
        throw new Exception('Pretul ofertei s-a schimbat intre timp');
      }
      $service_info['price'] = $service_price;
      $updated_services[] = $service_info;
      
      $supplement_services = array();
      $pax_ids = range(1, $service_info['total_adults'] + $service_info['total_children'], 1);
      if(is_array($service_info['extra_services'])){
        foreach($service_info['extra_services'] as $extra_service){
          $supplement_service = array(
            'Code' => $extra_service->Code,
            'Type' => $extra_service->Type,
            'CharterId' => $extra_service->CharterId,
            'PeriodOfStay' => array(
              'CheckIn' => $service_info['checkin'],
              'CheckOut' => $service_info['checkout'],
            ),
            'PaxIds' => array(
              'PaxId' => $pax_ids,
            ),
          );
          $supplement_services[] = $supplement_service;
        }
      }
      $rooms = $service_info['offer']->Rooms;
      $rooms_occupancy = array();
      foreach($service_info['service_rooms'] as $room_key => $service_room){
        $room = $rooms[$room_key];
        $room_occupancy = array(
          '@' => array(
            'Code' => $room->Code,
            'NoAdults' => 0,
          ),
          'PaxNames' => array(
            'PaxName' => array(),
          ),
        );
        foreach($service_room['adt'] as $passenger){
          $gender = $passenger['title'] ? $passenger['title'] : 'B';
          $pax_name = array(
            'PaxType' => 'adult',
            'TGender' => $gender,
            'DOB' => $passenger['birth_date'],
            '_' => trim($passenger['firstname'] . ' ' . $passenger['lastname']),
            // '_' => 'TEST/TEST',
          );
          if(isset($passenger['country'])){
            $pax_name['NATIONALITATE'] = $passenger['country'];
          }
          $room_occupancy['PaxNames']['PaxName'][] = $pax_name;
          $room_occupancy['@']['NoAdults']++;
        }
        if(isset($service_room['chd']) && !empty($service_room['chd'])){
          $room_occupancy['@']['NoChildren']=0;
          foreach($service_room['chd'] as $passenger){
            $pax_name = array(
              'PaxType' => 'child',
              'TGender' => 'C',
              'DOB' => $passenger['birth_date'],
              'ChildAge' => $passenger['age'],
              '_' => trim($passenger['firstname'] . ' ' . $passenger['lastname']),
              // '_' => 'TEST/TEST',
            );
            if(isset($passenger['country'])){
              $pax_name['NATIONALITATE'] = $passenger['country'];
            }
            $room_occupancy['PaxNames']['PaxName'][] = $pax_name;
            $room_occupancy['@']['NoChildren']++;
          }
        }
        $rooms_occupancy[]= $room_occupancy;
      }
      
      $booking_item = array(
        '@' => array(
          'ProductType' => 'hotel',
        ),
        'ItemClientId' => 1,
        'TourOpCode' => $service_info['tour_op_code'],
        'HotelItem' => array(
          'BookingAgent' => 'Consultant vanzari,Accent Travel & Events,Accent Travel & Events,vanzari@accenttravel.ro',
          'BookingClient' => trim($order->user_firstname . ' ' . $order->user_lastname),
          // 'BookingClient' => 'TEST/TEST',
          'CountryCode' => $service_info['destination_country_code'],
          'CityCode' => $service_info['destination_city_code'],
          'ProductCode' => $service_info['product_code'],
          'Language' => $service_info['language_code'],
          'PeriodOfStay' => array(
            'CheckIn' => $service_info['checkin'],
            'CheckOut' => $service_info['checkout'],
          ),
          'PackageId' => $service_info['package_id'],
          'VariantId' => $service_info['package_variant_id'],
          'SuppServices' => array(
            'Service' => $supplement_services,
          ),
          'Rooms' => array(
            'Room' => $rooms_occupancy,
          ),
        ),
      );
      $search_data['BookingItems']['BookingItem'][]=$booking_item;
    }
    cleanArray($search_data);
    // echo '<pre>';
    // print_r($search_data);
    // die;
    $response = $this->Paralela45_model->AddBookingRequest($search_data);
    if(empty($response->AddBookingResponse)){
      throw new Exception('Invalid booking response');
    }
    if(empty($response->AddBookingResponse->BookingReferences)){
      $error_id = $response->AddBookingResponse->BookingItems->BookingItem[0]->Error->ErrorId;
      $error_text = $response->AddBookingResponse->BookingItems->BookingItem[0]->Error->ErrorText;
      throw new Exception($error_text);
    }
    foreach($response->AddBookingResponse->BookingReferences->BookingReference as $booking_reference){
      if($booking_reference->Source == 'api'){
        $remote_order_id = $booking_reference->_;
      }
    }
    $order_data = array();
    $order_data['id'] = $order->id;
    $order_data['trip_services'] = $order->services;
    $order_data['services'] = serialize($updated_services);
    $order_data['trip_order_id'] = $remote_order_id;
    $order_data['calls'] = serialize($response);
    $this->TripOrder_model->saveOrder($order_data);
  }
}