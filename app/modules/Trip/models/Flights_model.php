<?php

class Flights_model extends CI_Model {

  public $api;
  public $cabin_type_map = array(
    1 => 'Y',
    2 => 'F',
    3 => 'C',
    4 => 'W',
  );
  public $dev = false;
  function __construct() {
    parent::__construct();
    $this->load->model('Trip_model');
    $this->api = $this->Trip_model->get_api();

    $this->load->driver('cache', array('adapter' => 'apc', 'backup' => 'file'));
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
    $this->session->set_userdata('trip/flights/search_data' . $session, $data);
  }

  function getSearchData($session=true) {
    $default_data = $this->getSearchDefaultData();
    $data = array();
    if($session){
      $data = $this->session->userdata('trip/flights/search_data' . (is_string($session) ? trim($session) : ''));
      if (!$data) {
        $data = array();
      }
      if(is_string($session)){
        $data['session'] = $session;
      }
    }
    if($this->config->item('csrf_protection') === TRUE){
      $data[$this->security->get_csrf_token_name()] = $this->security->get_csrf_hash();
    }
    return array_replace($default_data, $data);
  }

  function getSearchDefaultData() {
    $default_data = array(
      'container_id' => session_id(),
      'index_id' => '',
      'code' => '',
      'go_only' => false,
      // 'departure_date' => date('Y-m-d'),
      'departure_date' => '',
      // 'return_date' => date("Y-m-d", strtotime("+1 week")),
      'return_date' => '',
      'origin_city_id' => 0,
      'origin_country_id' => 0,
      'origin_location_id' => 0,
      'origin_location_name' => '',
      'origin_full_location_name' => '',
      'origin_city_name' => '',
      'origin_country_name' => '',
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
      'varste_copii' => array(),
      'cabine_type' => 1,
      'company_type' => 0,
      'direct_only' => false,
      'flexible_dates' => false,
      'flex_dates' => false,
    );
    if($this->config->item('csrf_protection') === TRUE){
      $default_data[$this->security->get_csrf_token_name()] = $this->security->get_csrf_hash();
    }
    return $default_data;
  }

  function initiateSearch($data) {
    $this->api->generateToken();
    $search_data = array();
    $search_data['type'] = isset($data['go_only']) ? ($data['go_only'] ? 0 : 1) : 2;
    $search_data['adt'] = isset($data['passengers_adult']) && ($data['passengers_adult']>0) ? $data['passengers_adult'] : null;
    $search_data['chd'] = isset($data['passengers_child']) && ($data['passengers_child']>0) ? $data['passengers_child'] : null;
    $search_data['sen'] = isset($data['passengers_senior']) && ($data['passengers_senior']>0) ? $data['passengers_senior'] : null;
    $search_data['yth'] = isset($data['passengers_youth']) && ($data['passengers_youth']>0) ? $data['passengers_youth'] : null;
    $search_data['inf'] = isset($data['passengers_infant_lap']) && ($data['passengers_infant_lap']>0) ? $data['passengers_infant_lap'] : null;
    $search_data['ins'] = isset($data['passengers_infant_seat']) && ($data['passengers_infant_seat']>0) ? $data['passengers_infant_seat'] : null;
    $search_data['class'] = isset($this->cabin_type_map[(int)$data['cabine_type']]) ? $this->cabin_type_map[(int)$data['cabine_type']] : null;
    $search_data['direct'] = isset($data['direct_only']) ? $data['direct_only'] : false;
    $search_data['flex'] = isset($data['flex_dates']) ? $data['flex_dates'] : false;
    $search_data['airline'] = isset($data['airlines']) ? $data['airlines'] : null;
    $search_data['code'] = isset($data['code']) ? $data['code'] : null;
    if(isset($data['type'])){
      $search_data['type'] = $data['type'];
    }
    if(isset($data['r'])){
      $search_data['r'] = $data['r'];
    } else {
      $search_data['r'] = array();
      $search_data['r'][0] = array();
      $search_data['r'][0]['date'] = $data['departure_date'];
      $search_data['r'][0]['oCityId'] = isset($data['origin_city_id']) && ($data['origin_city_id'] > 0) ? $data['origin_city_id'] : null;
      $search_data['r'][0]['oLocId'] = isset($data['origin_location_id']) && ($data['origin_location_id'] > 0) ? $data['origin_location_id'] : null;
      $search_data['r'][0]['dCityId'] = isset($data['destination_city_id']) && ($data['destination_city_id'] > 0) ? $data['destination_city_id'] : null;
      $search_data['r'][0]['dLocId'] = isset($data['destination_location_id']) && ($data['destination_location_id'] > 0) ? $data['destination_location_id'] : null;
      
      if($search_data['type'] == 1){
        $search_data['r'][1] = array();
        $search_data['r'][1]['date'] = $data['return_date'];
        $search_data['r'][1]['oCityId'] = isset($data['destination_city_id']) && ($data['destination_city_id'] > 0) ? $data['destination_city_id'] : null;
        $search_data['r'][1]['oLocId'] = isset($data['destination_location_id']) && ($data['destination_location_id'] > 0) ? $data['destination_location_id'] : null;
        $search_data['r'][1]['dCityId'] = isset($data['origin_city_id']) && ($data['origin_city_id'] > 0) ? $data['origin_city_id'] : null;
        $search_data['r'][1]['dLocId'] = isset($data['origin_location_id']) && ($data['origin_location_id'] > 0) ? $data['origin_location_id'] : null;
      }
    }
	if($this->api->getAccountId()){
        $search_data['accId'] = $this->api->getAccountId();
	}
    $this->clean($search_data);
	
	// echo '<pre>';
      // print_r($data);
      // print_r($search_data);
      // die;
    return $this->api->apiCall('index.php/en/dynamic-package/search', array(
        '_s' => array(
          $data['container_id'] => array(
            'f' => array(
              0 => $search_data
            )
          )
        ),
    ));
  }

  function validateFlight($code, $data) {
    return $this->api->apiCall('index.php/v3/flights/' . $code . '/flight', array(), $data);
  }
  function loadFlightAncillery($code, $itinerary_code, $ancillery_code, $cached = null, $cache_time = 586400) {
    $cached = isset($cached) ? !!$cached : $this->dev;
    if($cached){
      $cache_request = array(
        $code, $itinerary_code, $ancillery_code
      );
      $cache_storage_path = 'trip/flight/ancillery/';
      $cache_hash = crc32(json_encode($cache_request));
      if ($result = $this->cache->get($cache_storage_path . $cache_hash)){
        return $result;
      }
    }
    $result = $this->api->apiCall('index.php/v3/flights/' . $code . '/flight/' . $itinerary_code . '/' . implode('/', [$ancillery_code]));
    if($cached){
      setCacheStorage($cache_storage_path);
      $this->cache->save($cache_storage_path . $cache_hash, $result, $cache_time);
    }
    return $result;
  }
  function loadFlightSeats($code, $itinerary_code, $ocode, $dcode, $rindex, $req_paid_seat = 1, $cached = null, $cache_time = 86400) {
    $cached = isset($cached) ? !!$cached : $this->dev;
    if($cached){
      $cache_request = array(
        $code, $itinerary_code, $ocode, $dcode, $rindex, $req_paid_seat
      );
      $cache_storage_path = 'trip/flight/seats/';
      $cache_hash = crc32(json_encode($cache_request));
      if ($result = $this->cache->get($cache_storage_path . $cache_hash)){
        return $result;
      }
    }
    $result = $this->api->apiCall('index.php/v3/flights/' . $code . '/flight/' . $itinerary_code . '/seats/' . implode('/', [$ocode, $dcode, $rindex, $req_paid_seat]));
    if($cached){
      setCacheStorage($cache_storage_path);
      $this->cache->save($cache_storage_path . $cache_hash, $result, $cache_time);
    }
    return $result;
  }
  function loadFlightUpsell($code,$itinerary_code, $cached = null, $cache_time = 86400) {
    $cached = isset($cached) ? !!$cached : $this->dev;
    if($cached){
      $cache_request = array(
        $code,
        $itinerary_code,
      );
      $cache_storage_path = 'trip/flight/upsell/';
      $cache_hash = crc32(json_encode($cache_request));
      if ($result = $this->cache->get($cache_storage_path . $cache_hash)){
        return $result;
      }
    }
    $result = $this->api->apiCall('index.php/v3/flights/' . $code . '/flight/' . $itinerary_code . '/upsell');
    if($cached){
      setCacheStorage($cache_storage_path);
      $this->cache->save($cache_storage_path . $cache_hash, $result, $cache_time);
    }
    return $result;
  }

  function loadFlightDetails($code,$itinerary_code, $cached = null, $cache_time = 86400) {
    $cached = isset($cached) ? !!$cached : $this->dev;
    if($cached){
      $cache_request = array(
        $code,
        $itinerary_code,
      );
      $cache_storage_path = 'trip/flight/detail/';
      $cache_hash = crc32(json_encode($cache_request));
      if ($result = $this->cache->get($cache_storage_path . $cache_hash)){
        return $result;
      }
    }
    $result = $this->api->apiCall('index.php/v3/flights/' . $code . '/flight/' . $itinerary_code);
    if($cached){
      setCacheStorage($cache_storage_path);
      $this->cache->save($cache_storage_path . $cache_hash, $result, $cache_time);
    }
    return $result;
  }
  function inspectSearch($container_id) {
    return $this->api->apiCall('index.php/en/dynamic-package/inspect/' . $container_id);
  }

  function inspectSearchIndex($index_id) {
    $r = $this->api->apiCall('index.php/en/dynamic-package/sid/' . $index_id);
    
    if($r && !empty($r->code)){
      $check_first_date = date('Y-m-d H:i:s', strtotime('30 minutes ago'));
      $sql = "DELETE FROM trip_search WHERE `date` < '" . $check_first_date . "'";
      $this->db->query($sql);
      
      $sql = $this->db->insert_string('trip_search', array(
        'code' => $r->code,
        'type' => 'flight',
        'date' => date('Y-m-d H:i:s'),
      )) . ' ON DUPLICATE KEY UPDATE type=VALUES(type)';
      $this->db->query($sql);
    }
    
    return $r;
  }
  
  function loadLocations($q, $lang='en') {
    return $this->api->apiCall('flight-locations.php', array(
        'lang' => $lang,
        'q' => $q,
    ));
  }

  function loadFlights($code, $page=1) {
    $data = array(
      'code' => $code,
      'page' => $page,
    );
    $this->clean($data);
    return $this->api->apiCall('index.php/v3/flights', $data);
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