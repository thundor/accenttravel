<?php

class CityBreaks_model extends CI_Model {

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
    $this->load->model('Trip_model');
    $this->api = $this->Trip_model->get_api();
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
    if(isset($data['hotel_id']) && $data['hotel_id']){
      $this->session->set_userdata('trip/citybreak/search_data' . $session, $data);
    } else {
      $this->session->set_userdata('trip/citybreaks/search_data' . $session, $data);
    }
  }
  function getSearchData($hotel_id = 0, $session = true) {
    $default_data = $this->getSearchDefaultData();
    $data = array();
    if($session){
      if($hotel_id){
        $data = $this->session->userdata('trip/citybreak/search_data' . (is_string($session) ? trim($session) : ''));
        if(!$data || ($data['hotel_id'] != $hotel_id)){
          $data = $this->session->userdata('trip/citybreaks/search_data' . (is_string($session) ? trim($session) : ''));
        }
      } else {
        $data = $this->session->userdata('trip/citybreaks/search_data' . (is_string($session) ? trim($session) : ''));
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
    if($hotel_id){
      if(!isset($data['hotel_id']) || !$data['hotel_id']){
        $data['index_id'] = '';
        $data['code'] = '';
        $data['flight_index_id'] = '';
        $data['flight_code'] = '';
      }
      if(!isset($data['start_date']) || !$data['start_date'] || $data['start_date'] < date('Y-m-d')){
        $data['start_date'] = date('Y-m-d');
      }
      if(!isset($data['end_date']) || !$data['end_date'] || $data['end_date'] < $data['start_date']){
        $data['end_date'] = date("Y-m-d", strtotime("+1 week"));
      }
      $data['hotel_id'] = $hotel_id;
    }
    if($this->config->item('csrf_protection') === TRUE){
      $data[$this->security->get_csrf_token_name()] = $this->security->get_csrf_hash();
    }
    $data['flexible_dates'] = true;
    $data['flex_dates'] = true;
    return array_replace_recursive($default_data, $data);
  }
  
  function getSearchDefaultData() {
    $default_data = array(
      // 'start_date' => date('Y-m-d'),
      'start_date' => '',
      // 'end_date' => date("Y-m-d", strtotime("+1 week")),
      'end_date' => '',
      'departure' => '',
      'origin_city_id' => 0,
      'origin_country_id' => 0,
      'origin_location_id' => 0,
      'origin_location_name' => '',
      'origin_full_location_name' => '',
      'origin_city_name' => '',
      'origin_country_name' => '',
      'arrival' => '',
      'destination_city_id' => 0,
      'destination_country_id' => 0,
      'destination_location_id' => 0,
      'destination_full_location_name' => '',
      'destination_location_name' => '',
      'destination_city_name' => '',
      'destination_country_name' => '',
      'passengers_adult' => 1,
      'passengers_child' => 0,
      'passengers_senior' => 0,
      'passengers_youth' => 0,
      'passengers_infant_lap' => 0,
      'passengers_infant_seat' => 0,
      'occupancy' => array(
        array(
          'adt' => 1
        )
      ),
      'hotel_name' => '',
      'hotel_id' => '',
      'min_stars' => 0,
      'container_id' => session_id(),
      'index_id' => null,
      'flight_index_id' => null,
      'code' => null,
      'flight_code' => null,
      'package_code' => '',
      'page' => 1,
      'sort_by' => 'MinPrice',
      'flexible_dates' => true,
      'flex_dates' => true,
      'go_only' => false,
      'sort_order' => 0,
      'filters' => array(
        'stars'=>array(),
        'stops'=>array(),
        'facilities'=>array(),
        'activities'=>array(),
        'activity_categories'=>array(),
        'pois'=>array(),
        'min_price'=>null,
        'max_price'=>null,
      )
    );
    if($this->config->item('csrf_protection') === TRUE){
      $default_data[$this->security->get_csrf_token_name()] = $this->security->get_csrf_hash();
    }
    return $default_data;
  }

  function initiateSearch($data, $hotels=true, $flights=true) {
    if(!$hotels && !$flights){
      return;
    }
    $this->api->generateToken();
    $s = array();
    if($hotels){
      $occupancy = array();
      foreach($data['occupancy'] as $room_occupancy){
        if(isset($room_occupancy['chd'],$room_occupancy['chd']['age'])){
          foreach($room_occupancy['chd']['age'] as $k=>$chdage){
            $room_occupancy['chd']['age'][$k]--;
          }
        }
        $occupancy[] = $room_occupancy;
      }
      $search_data = array(
        'dIn' => $data['start_date'],
        'dOut' => $data['end_date'],
        'cityId' => $data['destination_city_id'],
        'r' => $occupancy,
        'full' => 0,
        'onRq' => 0,
        'hotel' => array(
        )
      );
      if (!empty($data['hotel_id'])) {
        $search_data['hotel']['id'] = $data['hotel_id'];
        $search_data['cityId'] = null;
        $data['hotel_name'] = null;
        $data['min_stars'] = null;
      }
      if (!empty($data['hotel_name'])) {
        $search_data['hotel']['name'] = $data['hotel_name'];
      }
      if(!empty($data['min_stars']) && $data['min_stars']>1){
        $search_data['hotel']['stars'] = array();
        for($star = $data['min_stars']+1; $star<=$this->max_stars; $star++){
          $search_data['hotel']['stars'][] = $star;
        }
      }
      $s['h'] = array(
        0 => $search_data
      );
    }
    if($flights){
      $search_data2 = array();
      $search_data2['type'] = 1;
      $search_data2['class'] = 'Y';
      $search_data2['adt'] = $data['passengers_adult'];
      $search_data2['chd'] = $data['passengers_child'];
      $search_data2['sen'] = $data['passengers_senior'];
      $search_data2['yth'] = $data['passengers_youth'];
      $search_data2['inf'] = $data['passengers_infant_lap'];
      $search_data2['ins'] = $data['passengers_infant_seat'];
      $search_data2['flex'] = isset($data['flex_dates']) ? $data['flex_dates'] : false;
      
      $search_data2['r'] = array();
      $search_data2['r'][0] = array();
      $search_data2['r'][0]['date'] = $data['start_date'];
      $search_data2['r'][0]['oCityId'] = $data['origin_city_id'];
      $search_data2['r'][0]['oLocId'] = isset($data['origin_location_id']) && ($data['origin_location_id'] > 0) ? $data['origin_location_id'] : null;
      $search_data2['r'][0]['dCityId'] = $data['destination_city_id'];
      $search_data2['r'][0]['dLocId'] = isset($data['destination_location_id']) && ($data['destination_location_id'] > 0) ? $data['destination_location_id'] : null;
      
      $search_data2['r'][1] = array();
      $search_data2['r'][1]['date'] = $data['end_date'];
      $search_data2['r'][1]['oCityId'] = $data['destination_city_id'];
      $search_data2['r'][1]['oLocId'] = isset($data['destination_location_id']) && ($data['destination_location_id'] > 0) ? $data['destination_location_id'] : null;
      $search_data2['r'][1]['dCityId'] = $data['origin_city_id'];
      $search_data2['r'][1]['dLocId'] = isset($data['origin_location_id']) && ($data['origin_location_id'] > 0) ? $data['origin_location_id'] : null;
      
      $s['f'] = array(
        0 => $search_data2
      );
    }
    $this->clean($s);
    return $this->api->apiCall('index.php/en/dynamic-package/search', array(
        '_s' => array(
          $data['container_id'] => $s
        ),
    ));
  }

  function inspectSearch($container_id) {
    return $this->api->apiCall('index.php/en/dynamic-package/inspect/' . $container_id);
  }

  function inspectSearchIndex($index_id) {
    return $this->api->apiCall('index.php/en/dynamic-package/sid/' . $index_id);
  }

  function loadHotelDetails($hotel_id) {
    return $this->api->apiCall('index.php/hotels/' . $hotel_id);
  }
  
  function loadHotel($hotel_id) {
    return $this->api->apiCall('index.php/v2/hotels/' . $hotel_id);
  }

  function loadFilters($code) {
    return $this->loadHotels($code, null, null, null, null, true);
  }

  function loadLocations($q) {
    return $this->api->apiCall('hotel-locations.php', array(
        'q' => $q
    ));
  }
  function loadMarkers($code) {
    return $this->api->apiCall('index.php/v2/hotels/' . $code . '/markers');
  }
  function loadRoomPackages($code, $hotel_id) {
    return $this->api->apiCall('index.php/v2/hotels/' . $code . '/' . $hotel_id . '/package');
  }
  function loadRoomPackage($code, $hotel_id, $package_code) {
    return $this->api->apiCall('index.php/v2/hotels/' . $code . '/' . $hotel_id . '/package/' . $package_code);
  }
  function loadRoomPackageRooms($code, $hotel_id, $package_code, $rooms_combination) {
    return $this->api->apiCall('index.php/v2/hotels/' . $code . '/' . $hotel_id . '/package/' . $package_code . '/' . $rooms_combination);
  }

  function loadHotels($code, $summary=null, $page=null, $sort_type=null, $sort_order=null, $filter=null, $limit=null) {
    $data = array(
      'summary' => $summary,
      'page' => $page,
      'filters' => is_bool($filter) ? $filter : null,
      'sortType' => $sort_type,
      'sortOrder' => !is_null($sort_order) ? ($sort_order ? 0 : 1) : null,
      'filter' => is_array($filter) ? $filter : null,
      'code' => $code,
      'limit' => $limit,
    );
    $this->clean($data);
    return $this->api->apiCall('index.php/v2/hotels', $data);
  }
  function loadFlights($code, $page=1) {
    $data = array(
      'code' => $code,
      'page' => $page,
    );
    $this->clean($data);
    return $this->api->apiCall('index.php/v3/flights', $data);
  }
  function loadFlightDetails($code,$itinerary_code) {
    return $this->api->apiCall('index.php/v3/flights/' . $code . '/flight/' . $itinerary_code);
  }
  function clean(&$data){
    foreach($data as $k => &$v){
      if(is_array($v)){
        $this->clean($v);
        if(empty($v)){
          unset($data[$k]);
          continue;
        }
      }
      if(!isset($data[$k])){
        unset($data[$k]);
      }
    }
  }
}