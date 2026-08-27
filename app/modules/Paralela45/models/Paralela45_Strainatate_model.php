<?php

class Paralela45_Strainatate_model extends CI_Model {

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
      $this->session->set_userdata('paralela45/strainatate/search_data' . $session, $data);
    } else {
      $this->session->set_userdata('paralela45/strainatate/search_data' . $session, $data);
    }
  }

  function getSearchData($package_id = 0, $session = true) {
    $default_data = $this->getSearchDefaultData();
    $data = array();
    if($session){
      if($package_id){
        $data = $this->session->userdata('paralela45/strainatate/search_data' . (is_string($session) ? trim($session) : ''));
        if(!$data || ($data['package_id'] != $package_id)){
          $data = $this->session->userdata('paralela45/strainatate/search_data' . (is_string($session) ? trim($session) : ''));
        }
      } else {
        $data = $this->session->userdata('paralela45/strainatate/search_data' . (is_string($session) ? trim($session) : ''));
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
      'zone' => '',
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
    if(isset($routes['Cities'][$departure_city_code], $routes['CityLinks']['Departure'][$departure_city_code])){
      return $routes['Cities'][$departure_city_code];
    }
    return null;
  }
  function getDestinationCity(&$routes, $departure_city_code, $destination_city_code) {
    if(isset($routes['Cities'][$destination_city_code], $routes['CityLinks']['Destination'][$destination_city_code]) && in_array($destination_city_code,$routes['CityLinks']['Departure'][$departure_city_code])){
      return $routes['Cities'][$destination_city_code];
    }
    return null;
  }
  function getDestinationZone(&$routes, $departure_city_code, $destination_zone_code) {
    if(isset($routes['Zones'][$destination_zone_code], $routes['ZoneLinks']['Departure'][$destination_zone_code]) && in_array($departure_city_code,$routes['ZoneLinks']['Departure'][$destination_zone_code])){
      return $routes['Zones'][$destination_zone_code];
    }
    return null;
  }
  function getRouteDate(&$routes, &$data) {
    $departure_city_code = $data['departure_city_code'];
    $destination_city_code = $data['destination_city_code'];
    
    if(!isset($data['nights'])){
      if(!isset($data['checkout'])){
        throw new Exception('Neither checkout date nor nights specified');
      }
	  // echo '<pre>';
	  // print_r($data);
	  // die;
      $checkin_date = DateTime::createFromFormat('Y-m-d', $data['checkin']);
      $checkout_date = DateTime::createFromFormat('Y-m-d', $data['checkout']);
      $nights_date = $checkout_date->diff($checkin_date);
      $data['nights'] = intval($nights_date->format('%a'));
    }
    if(!isset($data['checkout']) || !strlen($data['checkout'])){
      $checkin_date = DateTime::createFromFormat('Y-m-d', $data['checkin']);
      $checkin_date->modify('+' . intval($data['nights']) . ' days');
      $data['checkout'] = $checkin_date->format('Y-m-d');
    }
    $city_codes = array();
    if(strlen($destination_city_code)){
      $city_codes[] = $destination_city_code;
    }
    else {
      $destination_zone_code = $data['destination_zone_code'];
      if(!isset($routes['ZoneLinks']['Destination'][$destination_zone_code])){
        throw new Exception('Route date does not support the specified zone');
      }
      $city_codes = $routes['ZoneLinks']['Destination'][$destination_zone_code];
    }
	// echo '<pre>';
	  // print_r($data);
	  // print_r($city_codes);
	  // print_r($routes['Dates'][$departure_city_code]);
	  // die;
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
  function interpretPackageNVPriceRequest($request_search_data, $results, $just_offer_id = null, $variant_id = null, $flexible_variant_id = false, $first = false) {
	  $routes = $this->Paralela45_model->getPackageNVRoutes();
	  // print_r(debug_backtrace(false)[0]['file']);
    $variant_flex = null;
    if(isset($variant_id) && $flexible_variant_id){
      $variant_flex = preg_replace('/^((?:[^\-]+)(\-\d+@))?(\d+\|)\d+(_.*)$/',($first ? '$3$4' : '$2$3$4'),$variant_id);
    }
	
	// if(!empty($_POST['csrf_f96dd936408aaf4c8821b7eb1391a5b2']) && $_POST['csrf_f96dd936408aaf4c8821b7eb1391a5b2']=='7c2fcae4005ab2e9637b9a48bf4456be'){
			// echo '<pre>';
			// print_r($routes['Dates']);
			// die;
		// }
    $return = array();
    $return['products'] = array();
    $return['offers'] = array();
    
    $today = new DateTime();
    $checkin_date = DateTime::createFromFormat('Y-m-d', $request_search_data['PeriodOfStay']['CheckIn']);
    $days_till_start = $today->diff($checkin_date);
    $days_till_start_formatted = intval($days_till_start->format('%a'));
    
    // sambata && duminica
    $is_weekend = $today->format('N') >= 6;
    // $prices = array();
    // $meal_types = array();
    // $service_types = array();
    $departure_city_code = $request_search_data['DepCityCode'];
	
	// echo '<pre>';
			// print_r($routes['Dates'][$departure_city_code]);
			// die;
    foreach($results as $package){
      if(empty($package->Offers) || empty($package->Offers->Offer)){
        continue;
      }
      $any_offer = false;
      $offer_id = $request_search_data['TourOpCode'] . '_' . $package->Product->CountryCode . '_' . $package->Product->CityCode . '_' . $package->Product->ProductCode;
      if(isset($just_offer_id) && ($just_offer_id !== $offer_id)){
        continue;
      }
      foreach($package->Offers->Offer as $package_offer){
		  if(empty($package->Product->CityCode)){
			  continue;
		  }
		  if(!empty($request_search_data['CityCode'])){
			  if($package->Product->CityCode !== $request_search_data['CityCode']){
				  continue;
			  }
		  }
		  
		  if(empty($routes['Dates'][$departure_city_code][$package->Product->CityCode])){
			  continue;
		  }
		  // echo '<pre>';
			  // print_r($package_offer);
			  // die;
        $block_offline_payments = false;
        $block_online_payments = false;
        if(isset($variant_id) && ($variant_id !== $package_offer->PackageVariantId)){
          if(!$flexible_variant_id){
            continue;
          } else {
			  // if(!empty($_GET['test'])){
				// echo $package_offer->PackageVariantId . ' ' . preg_replace('/^((?:[^\-]+)(\-\d+@))?(\d+\|)\d+(_.*)$/','$2$3$4',$package_offer->PackageVariantId) . '<br/>';
			// }
            if(!isset($variant_flex) || ($variant_flex !== preg_replace('/^((?:[^\-]+)(\-\d+@))?(\d+\|)\d+(_.*)$/',($first ? '$3$4' : '$2$3$4'),$package_offer->PackageVariantId))){
              continue;
            }
          }
        }
        // checkin azi sau maine
        if(!$block_offline_payments && ($is_weekend || $days_till_start_formatted < 2)){
          $block_offline_payments = true;
        }
        if($package_offer->Availability->Code == 'ST'){
          continue;
        }
        if((!$block_offline_payments || !$block_online_payments) && $package_offer->Availability->Code == 'ST'){
          $block_offline_payments = true;
          $block_online_payments = true;
        }
        if(!$block_online_payments && $package_offer->Availability->Code == 'OR'){
          $block_online_payments = true;
        }
        $offer = new stdClass;
        $offer->block_online_payments = $block_online_payments;
        $offer->block_offline_payments = $block_offline_payments;
        $offer->block_payments = $offer->block_online_payments && $offer->block_offline_payments;
        if($offer->block_payments && isset($just_offer_id) && isset($variant_id)){
          continue;
        }
        $offer->ProductCode = $package->Product->ProductCode;
        $offer->Availability = $package_offer->Availability->Code;
        if(property_exists($package_offer,'OfferDescription')){
          $offer->OfferDescription = $package_offer->OfferDescription;
        }
        $offer->CheckIn = $package_offer->PeriodOfStay->CheckIn;
        $offer->CheckOut = $package_offer->PeriodOfStay->CheckOut;
        $offer->Currency = $package_offer->{'@'}->CurrencyCode;
        $offer->PackageId = $package_offer->PackageId;
        $offer->PackageVariantId = $package_offer->PackageVariantId;
        $offer->Price = floatval($package_offer->Gross);
        // $prices[$offer->Price] = true;
        if(!empty($package_offer->Meals) && !empty($package_offer->Meals->Meal)){
          $offer->Meals = array();
          foreach($package_offer->Meals->Meal as $offer_meal){
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
        }
        if(!empty($package_offer->BookingRoomTypes) && !empty($package_offer->BookingRoomTypes->Room)){
          $offer->Rooms = array();
          foreach($package_offer->BookingRoomTypes->Room as $offer_room){
            $room = new stdClass;;
            $room->Code = $offer_room->Code;
            $room->GCode = $offer_room->GCode;
            $room->Quantity = $offer_room->Quantity;
            $room->Provider = $offer_room->Provider;
            $room->Name = $offer_room->_;
            $room->Price = floatval($offer_room->Gross);
            $room->ExtraBed = filter_var($offer_room->ExtraBed, FILTER_VALIDATE_BOOLEAN);
            $offer->Rooms[] = $room;
          }
        }
        if(!empty($package_offer->PriceDetails) && !empty($package_offer->PriceDetails->Services) && !empty($package_offer->PriceDetails->Services->Service)){
          $offer->Services = array();
          foreach($package_offer->PriceDetails->Services->Service as $offer_service){
            if(!isset($variant_flex) && !in_array($offer_service->Type, array('2', '5', '6', '7', '7s'))){
              continue;
            }
            $service = new stdClass;;
            $service->Code = $offer_service->Code;
            $service->Type = $offer_service->Type;
            // $service_types[$service->Type] = true;
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
        $offer->Link = site_url('paralela45/strainatate/booking/' . $offer_id);
        $return['offers'][] = $offer;
		if($first){
			break;
		}
      }
      if(!$any_offer){
        continue;
      }
      // $package->Product;
      $product = new stdClass;
      $product->Class = $package->Product->Class;
      $product->Image = $package->Product->FirstImage;
      $product->Lat = $package->Product->Latitude;
      $product->Lng = $package->Product->Longitude;
      $product->Stars = $package->Product->ProductCategory;
      $product->Name = $package->Product->ProductName;
      $product->CityCode = $package->Product->CityCode;
      $product->Link = site_url('paralela45/strainatate/hotel/' . $offer_id);
      $return['products'][$package->Product->ProductCode] = $product;
    }
	
	// if(!empty($_GET['test'])){
		// echo '<pre>';
		// print_r($return);
		// print_r($return);
		// die;
	// }
	
    return $return;
  }
  function getPackageNVPriceRequest($request_search_data) {
    $response = $this->Paralela45_model->getPackageNVPriceRequest($request_search_data);
    if (!$response) {
      throw new Exception('Paralela45 error: search results returned no response');
      return null;
    }
    if(empty($response->getPackageNVPriceResponse)){
      throw new Exception('Paralela45 error: search results returned invalid response');
      return null;
    }
    if(!empty($response->getPackageNVPriceResponse->Error)){
      throw new Exception('Paralela45 error CODE: ' . $response->getPackageNVPriceResponse->Error->ErrorId . ' ' . $response->getPackageNVPriceResponse->Error->ErrorText);
      return null;
    }
    if(empty($response->getPackageNVPriceResponse->Hotel)){
      return array();
    }
    return $response->getPackageNVPriceResponse->Hotel;
  }
  function getPackageNVPriceRequestSearchData(&$routes, &$data) {
    if(!isset($routes) || !is_array($routes)){
      throw new Exception('Invalid routes');
      return null;
    }
    if(!isset($data) || !is_array($data)){
      throw new Exception('Invalid search data');
      return null;
    }
    if(!isset($data['departure_city_code'])){
      throw new Exception('Invalid departure city code');
      return null;
    }
    $departure_city = $this->getDepartureCity($routes, $data['departure_city_code']);
    if(!$departure_city){
      throw new Exception('Departure city not found');
      return null;
    }
    $data['departure_city'] = $departure_city;
    $data['departure_country_code'] = $departure_city['CountryCode'];
    $city_search_type = '';
    if(isset($data['destination_city_code']) && strlen($data['destination_city_code'])){
      $city_search_type = 'city';
      $destination_city = $this->getDestinationCity($routes, $data['departure_city_code'], $data['destination_city_code']);
      if(!$destination_city){
        throw new Exception('Destination city not found');
        return null;
      }
      $data['destination_city'] = $destination_city;
      $data['destination_country_code'] = $destination_city['CountryCode'];
    } else {
      $city_search_type = 'zone';
      if(!isset($data['destination_zone_code']) || !strlen($data['destination_zone_code'])){
        throw new Exception('Neither destination city or zone specified');
      }
      $destination_zone = $this->getDestinationZone($routes, $data['departure_city_code'], $data['destination_zone_code']);
      if(!$destination_zone){
        throw new Exception('Destination zone not found');
        return null;
      }
      $data['destination_zone'] = $destination_zone;
      $data['destination_country_code'] = $destination_zone['CountryCode'];
    }
    $route_date = $this->getRouteDate($routes,$data);
    if(!$route_date){
		// echo '<pre>';
		// print_r($routes);
		// print_r($data);
		// die;
      throw new Exception('Route date not found');
      return null;
    }
    if(!isset($data['currency_code'])){
      $data['currency_code'] = 'EUR';
    }
    if(!isset($data['language_code'])){
      $data['language_code'] = 'RO';
    }
    if(!isset($data['offer_type'])){
      $data['offer_type'] = 'NORMAL';
    }
    if(!isset($data['days'])){
      $data['days'] = 0;
    }
    if(!isset($data['transport_type'])){
      $data['transport_type'] = 'search';
    }
    if(!isset($data['room_code_general'])){
      $data['room_code_general'] = 'DB';
    }
    $search_data = array();
    $search_data['Language'] = $data['language_code'];
    $search_data['CurrencyCode'] = $data['currency_code'];
    $search_data['DepCityCode'] = $data['departure_city_code'];
    $search_data['DepCountryCode'] = $data['departure_country_code'];
    if($city_search_type == 'city'){
      $search_data['CityCode'] = $data['destination_city_code'];
    } else {
      $search_data['Zone'] = $data['destination_zone_code'];
    }
    $search_data['CountryCode'] = $data['destination_country_code'];
    $search_data['OfferType'] = $data['offer_type'];  //OfferType: TOATE, NORMAL, EB, OFSPEC, LASTMIN
    $search_data['Transport'] = $data['transport_type'];  //Transport: search, plane, bus
    $search_data['PeriodOfStay']['CheckIn'] = $data['checkin'];
    $search_data['PeriodOfStay']['CheckOut'] = $data['checkout'];
    $search_data['Days'] = $data['days'];
    $search_data['ProductName'] = isset($data['hotel_name']) && strlen($data['hotel_name']) ? $data['hotel_name'] : null;
    $search_data['TourOpCode'] = $data['tour_op_code'];
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
  function getProductInfoRequest(&$data) {
    $search_data = array();
    $search_data['Language'] = 'RO';
    $search_data['TourOpCode'] = $data['tour_op_code'];
    $search_data['CountryCode'] = $data['destination_country_code'];
    $search_data['CityCode'] = $data['destination_city_code'];
    $search_data['ProductCode'] = $data['product_code'];
    $search_data['ProductType'] = 'hotel';
    cleanArray($search_data);
    $response = $this->Paralela45_model->getProductInfoRequest($search_data, false);
    if (!$response) {
      throw new Exception('Vacanta nu a fost gasita');
    }
    if(empty($response->getProductInfoResponse)){
		// echo '<pre>';
		// print_r($search_data);
		// die;
      throw new Exception('Vacanta invalida');
    }
    if(!empty($response->getProductInfoResponse->Error)){
      throw new Exception('Vacanta invalida: ' . $response->getProductInfoResponse->Error->ErrorId . ' ' . $response->getProductInfoResponse->Error->ErrorText);
    }
    return $response->getProductInfoResponse->Product;
  }
  function getItemFeesRequest(&$data) {
    $rooms_occupancy = array();
    $all_pax_names = array();
    $total_rooms = 0;
    $total_adults = 0;
    $total_children = 0;

    foreach($data['occupancy'] as $k=>$room_occupancy){
      $room_occupancy = (object)$room_occupancy;
      $total_rooms++;
      if(!isset($data['offer']->Rooms[$k])){
        throw new Exception('Invalid offer room index');
      }
      $rooms_occupancy_item = array(
        '@' => array(
          'Code' => $data['offer']->Rooms[$k]->Code,
          'NoAdults' => 0,
          'NoChildren' => 0,
        ),
        'PaxNames' => array(
          'PaxName' => array(),
        ),
      );
      if(!is_object($room_occupancy)){
        throw new Exception('Invalid room occupancy 1');
      }
      if(!property_exists($room_occupancy, 'adt') || $room_occupancy->adt<1 || $room_occupancy->adt>6){
        throw new Exception('Invalid room occupancy 2');
      }
      $rooms_occupancy_item['@']['NoAdults'] += $room_occupancy->adt;
      for($i = 1; $i <= $room_occupancy->adt; $i++){
        $pax_name = array(
          'PaxType' => 'adult',
          '_' => 'Adult ' . ($total_adults + $i),
        );
        $all_pax_names[] = $pax_name;
        $rooms_occupancy_item['PaxNames']['PaxName'][]= $pax_name;
      }
      $total_adults += $room_occupancy->adt;
      if(!property_exists($room_occupancy,'chd')){
        $rooms_occupancy[] = $rooms_occupancy_item;
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
        $rooms_occupancy_item['@']['NoChildren'] ++;
        $pax_name = array(
          'PaxType' => 'child',
          'ChildAge' => $child_age,
          '_' => 'Child ' . ($total_adults + $total_children),
        );
        $all_pax_names[] = $pax_name;
        $rooms_occupancy_item['PaxNames']['PaxName'][]= $pax_name;
      }
      $rooms_occupancy[] = $rooms_occupancy_item;
    }
    $search_data = array(
      '@' => array(
        'CurrencyCode' => $data['currency_code'],
      ),
      'BookingItems' => array(
        'BookingItem' => array(
          array(
            '@' => array(
              'ProductType' => 'hotel',
            ),
            'TourOpCode' => $data['tour_op_code'],
            'HotelItem' => array(
              'CountryCode' => $data['destination_country_code'],
              'CityCode' => $data['destination_city_code'],
              'ProductCode' => $data['product_code'],
              'PeriodOfStay' => array(
                'CheckIn' => $data['checkin'],
                'CheckOut' => $data['checkout'],
              ),
              'PackageId' => $data['package_id'],
              'VariantId' => $data['package_variant_id'],
              'Rooms' => array(
                'Room' => $rooms_occupancy,
              ),
            ),
          ),
        ),
      ),
    );
    cleanArray($search_data);
    $response = $this->Paralela45_model->getItemFeesRequest($search_data);
    if(empty($response)){
      return null;
      throw new Exception('getItemFeesResponse invalid response');
    }
    if(!empty($response->getItemFeesResponse)){
      if(!empty($response->getItemFeesResponse->Error)){
        throw new Exception('getItemFeesResponse error CODE: ' . $response->getItemFeesResponse->Error->ErrorId . ' ' . $response->getItemFeesResponse->Error->ErrorText);
      }
      return !empty($response->getItemFeesResponse->ItemFees) && !empty($response->getItemFeesResponse->ItemFees->ItemFee) ? $response->getItemFeesResponse->ItemFees->ItemFee : array();
    }
    return false;
  }
  function getHotelServiceTypesRequest(&$data) {
    $all_pax_names = array();
    $total_adults = 0;
    $total_children = 0;
    foreach($data['occupancy'] as $k=>$room_occupancy){
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
    $search_data = array();
    $search_data['Language'] = 'RO';
    $search_data['TourOpCode'] = $data['tour_op_code'];
    $search_data['CountryCode'] = $data['destination_country_code'];
    $search_data['CityCode'] = $data['destination_city_code'];
    $search_data['ProductCode'] = $data['product_code'];
    $search_data['VariantId'] = $data['package_variant_id'];
    cleanArray($search_data);
    
    $response = $this->Paralela45_model->getHotelServiceTypesRequest($search_data);
    $extra_services = array();
    if(!empty($response->getHotelServiceTypesResponse)){
      if(!empty($response->getHotelServiceTypesResponse->Error)){
        throw new Exception('HotelServiceTypesResponse error CODE: ' . $response->getHotelServiceTypesResponse->Error->ErrorId . ' ' . $response->getHotelServiceTypesResponse->Error->ErrorText);
      }
      // Preluare pret servicii suplimentare
      $extra_service_types = !empty($response->getHotelServiceTypesResponse->Services) && !empty($response->getHotelServiceTypesResponse->Services->Service) ? $response->getHotelServiceTypesResponse->Services->Service : array();
      
      if($extra_service_types){
        $search_data = array();
        $search_data['Language'] = 'RO';
        $search_data['TourOpCode'] = $data['tour_op_code'];
        $search_data['CountryCode'] = $data['destination_country_code'];
        $search_data['CityCode'] = $data['destination_city_code'];
        $search_data['ProductCode'] = $data['product_code'];
        $search_data['VariantId'] = $data['package_variant_id'];
        $search_data['CurrencyCode'] = 'EUR';
        $search_data['Services'] = array();
        $search_data['Services']['Service'] = array();
        $extra_services = array();
        foreach($extra_service_types as $extra_service_type){
          if(isset($data['selected_extra_services'])){
            $service_index = $extra_service_type->Type . '-' . $extra_service_type->Code . '-' . $extra_service_type->CharterId;
            if(!in_array($service_index,$data['selected_extra_services'])){
              continue;
            }
          }
          $extra_services[$extra_service_type->Type . '-' . $extra_service_type->Code] = $extra_service_type;
          if(!filter_var($extra_service_type->HasPrice, FILTER_VALIDATE_BOOLEAN)){
            continue;
          }
          $service = array();
          $service['ServiceType'] = $extra_service_type->Type;
          $service['ServiceCode'] = $extra_service_type->Code;
          $service['CharterId'] = $extra_service_type->CharterId;
          $service['PeriodOfStay'] = array(
            'CheckIn' => $data['checkin'],
            'CheckOut' => $data['checkout'],
          );
          $service['PaxNames'] = array();
          $service['PaxNames']['PaxName'] = $all_pax_names;
          
          $search_data['Services']['Service'][] = $service;
        }
        if(!empty($search_data['Services']['Service'])){
          cleanArray($search_data);
          $response = $this->Paralela45_model->getHotelServicePriceRequest($search_data);
          
          if(empty($response->getHotelServicePriceResponse)){
            throw new Exception('Invalid HotelServicePriceResponse');
          }
          if(!empty($response->getHotelServicePriceResponse->Error)){
            throw new Exception('HotelServicePriceResponse error CODE: ' . $response->getHotelServicePriceResponse->Error->ErrorId . ' ' . $response->getHotelServicePriceResponse->Error->ErrorText);
          }
          $extra_service_prices = !empty($response->getHotelServicePriceResponse->Services) && !empty($response->getHotelServicePriceResponse->Services->Service) ? $response->getHotelServicePriceResponse->Services->Service : array();
          foreach($extra_service_prices as $extra_service_price){
            if(!isset($extra_services[$extra_service_price->Type . '-' . $extra_service_price->Code])){
              throw new Exception('Missing service price');
            }
            $extra_service = $extra_services[$extra_service_price->Type . '-' . $extra_service_price->Code];
            $extra_service->price = $extra_service_price;
          }
        }
      }
    }
    return $extra_services;
  }
  public function getBookingService(&$service_info){
    $routes = $this->Paralela45_model->getPackageNVRoutes();
    $request_search_data = $this->getPackageNVPriceRequestSearchData($routes, $service_info);
	// echo '<pre>';
	// print_r($request_search_data);
	// die;
    $results =  $this->getPackageNVPriceRequest($request_search_data);
    $interpretted_results = $this->interpretPackageNVPriceRequest($request_search_data,$results,$service_info['offer_id'],$service_info['package_variant_id'],true, !empty($service_info['first']));
    if(!$interpretted_results || empty($interpretted_results['products']) || empty($interpretted_results['offers'])){
		// if(!empty($_GET['test1'])){
			// echo '<pre>';
			// print_r($request_search_data);
			// print_r($service_info);
			// print_r($results);
			// print_r($interpretted_results);
			// die;
		// }
      throw new Exception('Offer not found in results');
    }
    $service_info['offer'] = $interpretted_results['offers'][0];
    $service_info['product'] = $interpretted_results['products'][$service_info['product_code']];
    $product_info = $this->getProductInfoRequest($service_info);
    $service_info['product_info'] = $product_info;
    $response_cancellation_policies = $this->getItemFeesRequest($service_info);
    $cancellation_policies = array();
    if(!empty($response_cancellation_policies)){
      foreach($response_cancellation_policies as $response_cancellation_policy){
        if(empty($response_cancellation_policy->Fees) || empty($response_cancellation_policy->Fees->Fee)){
          throw new Exception('getItemFeesResponse invalid response');
        }
        $response_group_cancellation_policies = $response_cancellation_policy->Fees->Fee;
        $group_cancellation_policies = array();
        foreach($response_group_cancellation_policies as $response_group_cancellation_policy){
          if(empty($response_group_cancellation_policy->Gross)){
            continue;
          }
          $group_cancellation_policy = array(
            'from_date' => $response_group_cancellation_policy->FromDate,
            'to_date' => $response_group_cancellation_policy->ToDate,
            'type' => $response_group_cancellation_policy->{'@'}->Type,
            'price' => floatval($response_group_cancellation_policy->Gross->_)+0,
            'percentage' => filter_var($response_group_cancellation_policy->Gross->Procent, FILTER_VALIDATE_BOOLEAN),
          );
          $group_cancellation_policies[] = $group_cancellation_policy;
        }
        $cancellation_policies[] = $group_cancellation_policies;
      }
    }
    $service_info['cancellation_policies'] = $cancellation_policies;
    $extra_services = $this->getHotelServiceTypesRequest($service_info);
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
      if($service_info['type'] == 'strainatate'){
        $service_info['departure_city_code'] = $order_service_info['departure_city_code'];
        $service_info['checkin'] = $order_service_info['checkin'];
        $service_info['checkout'] = $order_service_info['checkout'];
        $service_info['tour_op_code'] = $order_service_info['tour_op_code'];
        $service_info['destination_city_code'] = $order_service_info['destination_city_code'];
        $service_info['product_code'] = $order_service_info['product_code'];
      }
      $service_info['occupancy'] = $order_service_info['occupancy'];
      $service_info['selected_extra_services'] = $order_service_info['selected_extra_services'];
      $service_info['service_rooms'] = $order_service_info['service_rooms'];
      if($service_info['type'] == 'strainatate'){
        $this->getBookingService($service_info);
      } elseif($service_info['type'] == 'circuit'){
        $this->load->model('Paralela45/Paralela45_Circuit_model');
        $this->Paralela45_Circuit_model->getBookingService($service_info);
      }
      
      if($service_info['price'] != $order_service_info['price']){
        throw new Exception('Pretul ofertei s-a schimbat intre timp');
      }
      $updated_services[] = $service_info;
      
      $supplement_services = array();
      $pax_ids = range(1, $service_info['total_adults'] + $service_info['total_children'], 1);
      if(is_array($service_info['extra_services'])){
        foreach($service_info['extra_services'] as $extra_service){
          $supplement_service = array(
            'Code' => $extra_service->Code,
            'Type' => $extra_service->Type,
            'CharterId' => $extra_service->CharterId,
            'PaxIds' => array(
              'PaxId' => $pax_ids,
            ),
          );
          if($service_info['type'] == 'strainatate'){
            $supplement_service['PeriodOfStay'] = array(
              'CheckIn' => $service_info['checkin'],
              'CheckOut' => $service_info['checkout'],
            );
          }
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
      if($service_info['type'] == 'strainatate'){
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
      } elseif($service_info['type'] == 'circuit'){
        $booking_item = array(
          '@' => array(
            'ProductType' => 'circuit',
          ),
          'ItemClientId' => 1,
          'TourOpCode' => $service_info['tour_op_code'],
          'CircuitItem' => array(
            'BookingAgent' => 'Consultant vanzari,Accent Travel & Events,Accent Travel & Events,vanzari@accenttravel.ro',
            'BookingClient' => trim($order->user_firstname . ' ' . $order->user_lastname),
            // 'BookingClient' => 'TEST/TEST',
            'CircuitId' => $service_info['offer_id'],
            'SearchId' => $service_info['unique_id'],
            // 'SearchId' => $service_info['package_id'],
            'DepartureCharter' => $service_info['package_variant_id'],
            'SuppServices' => array(
              'Service' => $supplement_services,
            ),
            'Rooms' => array(
              'Room' => $rooms_occupancy,
            ),
          ),
        );
      }
      
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