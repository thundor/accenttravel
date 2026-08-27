<?php

class Hotels_model extends CI_Model {

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
      $this->session->set_userdata('trip/hotel/search_data' . $session, $data);
    } else {
      $this->session->set_userdata('trip/hotels/search_data' . $session, $data);
    }
  }

  function getSearchData($hotel_id = 0, $session = true) {
    $default_data = $this->getSearchDefaultData();
    $data = array();
    if($session){
      if($hotel_id){
        $data = $this->session->userdata('trip/hotel/search_data' . (is_string($session) ? trim($session) : ''));
        if(!$data || ($data['hotel_id'] != $hotel_id)){
          $data = $this->session->userdata('trip/hotels/search_data' . (is_string($session) ? trim($session) : ''));
        }
      } else {
        $data = $this->session->userdata('trip/hotels/search_data' . (is_string($session) ? trim($session) : ''));
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
    return array_replace_recursive($default_data, $data);
  }

  function getSearchDefaultData() {
    $default_data = array(
      // 'start_date' => date('Y-m-d'),
      'start_date' => '',
      // 'end_date' => date("Y-m-d", strtotime("+1 week")),
      'end_date' => '',
      'city_id' => 0,
      'country_id' => 0,
      'add_flight' => false,
      'weekend' => false,
      'depart_city' => '',
      'city_name' => '',
      'country_name' => '',
      'occupancy' => array(
        array(
          'adt' => 2
        )
      ),
      'hotel_name' => '',
      'hotel_id' => '',
      'min_stars' => 0,
      'container_id' => session_id(),
      'index_id' => '',
      'code' => '',
      'package_code' => '',
      'page' => 1,
      'sort_by' => 'MinPrice',
      'sort_order' => 0,
      'filters' => array(
        'stars'=>array(),
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

  function initiateSearch($data) {
    $this->api->generateToken();
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
      'cityId' => isset($data['city_id']) ? $data['city_id'] : null,
      'r' => $occupancy,
      'full' => 0,
      'onRq' => 1,
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
	
	if($this->api->getAccountId()){
        $search_data['accId'] = $this->api->getAccountId();
	}

    return $this->api->apiCall('index.php/en/dynamic-package/search', array(
        '_s' => array(
          $data['container_id'] => array(
            'h' => array(
              0 => $search_data
            )
          )
        ),
    ));
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
        'type' => 'hotel',
        'date' => date('Y-m-d H:i:s'),
      )) . ' ON DUPLICATE KEY UPDATE type=VALUES(type)';
      $this->db->query($sql);
    }
    
    return $r;
  }

  function loadHotelDetails($hotel_id) {
    return $this->api->apiCall('index.php/hotels/' . $hotel_id);
  }
  function loadLegacyHotels($filters) {
    return $this->api->apiCall('index.php/hotels', $filters);
  }
  function loadHotel($hotel_id, $cached = true) {
    // return $this->api->apiCall('index.php/' . (defined('HV3') && HV3 ? 'v3' : 'v2') . '/hotels/' . $hotel_id);
	$cache_key = 'accent/trip/hotel/' . (int)$hotel_id;
	$response = null;
	if($cached){
		$cached_file = getCacheFileByFile($cache_key);
		if($cached_file && (time() - filemtime($cached_file)) < 86400){
			$cached_response = getCacheByFile($cache_key);
			if($cached_response){
				$response = json_decode($cached_response);
			}
		}
	}
	if(!$response){
		$response = $this->api->apiCall('index.php/v3/hotels/' . $hotel_id);
		
		if($response){
			setCacheByFile($cache_key, json_encode($response));
		}
	}
	
	return $response;
	// OLD
	$response = $this->api->apiCall('index.php/v3/hotels/' . $hotel_id);
	return $response;
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
    return $this->api->apiCall('index.php/' . (defined('HV3') && HV3 ? 'v3' : 'v2') . '/hotels/' . $code . '/markers');
  }
  function loadRoomPackages($code, $hotel_id) {
    return $this->api->apiCall('index.php/' . (defined('HV3') && HV3 ? 'v3' : 'v2') . '/hotels/' . $code . '/' . $hotel_id . '/package');
  }
  function loadRoomPackage($code, $hotel_id, $package_code) {
    return $this->api->apiCall('index.php/' . (defined('HV3') && HV3 ? 'v3' : 'v2') . '/hotels/' . $code . '/' . $hotel_id . '/package/' . $package_code);
  }
  function loadRoomPackageRooms($code, $hotel_id, $package_code, $rooms_combination) {
    return $this->api->apiCall('index.php/' . (defined('HV3') && HV3 ? 'v3' : 'v2') . '/hotels/' . $code . '/' . $hotel_id . '/package/' . $package_code . '/' . $rooms_combination);
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
    return $this->api->apiCall('index.php/' . (defined('HV3') && HV3 ? 'v3' : 'v2') . '/hotels', $data);
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