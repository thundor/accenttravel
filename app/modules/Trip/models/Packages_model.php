<?php

class Packages_model extends CI_Model {

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
    if(isset($data['package_id']) && $data['package_id']){
      $this->session->set_userdata('trip/package/search_data' . $session, $data);
    } else {
      $this->session->set_userdata('trip/packages/search_data' . $session, $data);
    }
  }

  function getSearchData($package_id = 0, $session = true) {
    $default_data = $this->getSearchDefaultData();
    $data = array();
    if($session){
      if($package_id){
        $data = $this->session->userdata('trip/package/search_data' . (is_string($session) ? trim($session) : ''));
        if(!$data || ($data['package_id'] != $package_id)){
          $data = $this->session->userdata('trip/packages/search_data' . (is_string($session) ? trim($session) : ''));
        }
      } else {
        $data = $this->session->userdata('trip/packages/search_data' . (is_string($session) ? trim($session) : ''));
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
      if(!isset($data['package_id']) || !$data['package_id']){
        $data['index_id'] = '';
        $data['code'] = '';
      }
      if(!isset($data['start_date']) || !$data['start_date'] || $data['start_date'] < date('Y-m-d')){
        $data['start_date'] = date('Y-m-d');
      }
      if(!isset($data['end_date']) || !$data['end_date'] || $data['end_date'] < $data['start_date']){
        $data['end_date'] = date("Y-m-d", strtotime("+1 year"));
      }
      $data['package_id'] = $package_id;
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
      'reg_id' => 0,
      'city_id' => '',
      'country_id' => 0,
      'nights' => '',
      // 'add_flight' => false,
      // 'weekend' => false,
      // 'depart_city' => '',
      // 'city_name' => '',
      // 'country_name' => '',
      'project_id' => '',
      'occupancy' => array(
        array(
          'adt' => 2
        )
      ),
      // 'package_name' => '',
      'hotel_name' => '',
      'package_id' => null,
      // 'min_stars' => 0,
      'container_id' => session_id(),
      'index_id' => '',
      'code' => '',
      // 'package_code' => '',
      'page' => 1,
      'type' => '',
      'category' => '',
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
      if(isset($room_occupancy['chd'],$room_occupancy['chd'])){
        foreach($room_occupancy['chd'] as $k=>$chdage){
          $room_occupancy['chd'][$k]--;
        }
      }
      $occupancy[] = $room_occupancy;
    }
    if(isset($data['package_id']) && !empty($data['package_id'])){
      if(isset($data['city_id'])){
        unset($data['city_id']);
      }
      if(isset($data['country_id'])){
        unset($data['country_id']);
      }
      if(isset($data['reg_id'])){
        unset($data['reg_id']);
      }
      if(isset($data['projectId'])){
        unset($data['projectId']);
      }
      if(isset($data['category'])){
        unset($data['category']);
      }
    }
    $search_data = array(
      'dIn' => $data['start_date'],
      'dOut' => $data['end_date'],
      'cityId' => isset($data['city_id']) ? (int)$data['city_id'] : 0,
      'countryId' => isset($data['country_id']) ? (int)$data['country_id'] : 0,
      'regId' => isset($data['reg_id']) ? (int)$data['reg_id'] : 0,
      'nights' => isset($data['nights']) && $data['nights']>0 ? (int)$data['nights'] : "",
      'projectId' => isset($data['project_id']) && !empty($data['project_id']) ? (array)$data['project_id'] : null,
      'packageId' => isset($data['package_id']) ? (int)$data['package_id'] : 0,
      'category' => isset($data['category']) ? "" . $data['category'] : "",
      'type' => isset($data['type']) ? "" . $data['type'] : "",
      // 'onRq' => 0,
      'r' => $occupancy,
    );
    cleanArray($search_data);
	
	if($this->api->getAccountId()){
        $search_data['accId'] = $this->api->getAccountId();
	}

    return $this->api->apiCall('index.php/en/dynamic-package/search', array(
        '_s' => array(
          $data['container_id'] => array(
            'p' => array(
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
        'type' => 'package',
        'date' => date('Y-m-d H:i:s'),
      )) . ' ON DUPLICATE KEY UPDATE type=VALUES(type)';
      $this->db->query($sql);
    }
    
    return $r;
  }

  function loadPackageDetails($package_id) {
    $this->load->driver('cache', array('adapter' => 'apc', 'backup' => 'file'));
    $cache_storage_path = 'trip/package/' . (int)$package_id . '/';
    if ($package_details = $this->cache->get($cache_storage_path . 'details')){
      return $package_details;
    }
    $package_details = $this->api->apiCall('index.php/packages/' . $package_id);
    if($package_details){
      $package_details->discount_percentage = 0;
      $package_details->full_price = null;
      if(!empty($package_details->MinPrice)){
        $package_details->full_price = $package_details->MinPrice;
        $package_details->discount_percentage = $this->getDiscountPercentage($package_id);
        $package_details->MinPrice = $this->calculateDiscountedPrice($package_details->MinPrice, $package_details->discount_percentage);
      }
    }
    if($package_details){
      setCacheStorage($cache_storage_path);
      $this->cache->save($cache_storage_path . 'details', $package_details, 86400);
    }
    return $package_details;
  }
  function loadPackageEntries($package_id, $code = '') {
    $this->load->driver('cache', array('adapter' => 'apc', 'backup' => 'file'));
    $cache_storage_path = 'trip/package/' . (int)$package_id . '/code/' . crc32($code) . '/';
    if (!($entries = $this->cache->get($cache_storage_path . 'entries'))){
      $entries = $this->api->apiCall('index.php/packages/' . $code . '/' . $package_id . '/entries');
      if($entries){
        $cache_time = strtotime('tomorrow') - time();
        if($cache_time > 0){
          setCacheStorage($cache_storage_path);
          $this->cache->save($cache_storage_path . 'entries', $entries, $cache_time);
        }
      }
    }
    return $entries;
  }
  function loadPackageDestinations($country_id = 1) {
    $cache_storage_path = 'trip/packages/destinations/';
    $this->load->driver('cache', array('adapter' => 'apc', 'backup' => 'file'));
    if ($destinations = $this->cache->get($cache_storage_path . $country_id)){
      return $destinations;
    }
    $get = array(
      'limit' => 50,
      'filter' => array(
        array(
          'name' => 'Name',
          'direction' => 'asc',
        ),
      ),
    );
    $destinations = $this->api->apiCall('index.php/package-countries/' . $country_id . '/cities', $get);
    if($destinations){
      setCacheStorage($cache_storage_path);
      $this->cache->save($cache_storage_path . $country_id, $destinations, 86400);
    }
    return $destinations;
  }
  function loadPackageProjects() {
    return $this->api->apiCall('index.php/package-projects');
  }
  function loadPackageCategories() {
    $cache_storage_path = 'trip/packages/';
    $this->load->driver('cache', array('adapter' => 'apc', 'backup' => 'file'));
    if ($categories = $this->cache->get($cache_storage_path . 'categories')){
      return $categories;
    }
    $get = array(
      'limit' => 50,
      'filter' => array(
        array(
          'name' => 'Code',
          'direction' => 'asc',
        ),
      ),
    );
    $categories = $this->api->apiCall('index.php/package-categories', $get);
    if($categories){
      setCacheStorage($cache_storage_path);
      $this->cache->save($cache_storage_path . 'categories', $categories, 86400);
    }
    return $categories;
  }
  function loadPackageEntryDetails($package_id, $code = '', $entry_id, $rate_group_id) {
    $cache_storage_path = 'trip/package/' . (int)$package_id . '/code/' . crc32($code) . '/entry/' . (int)$rate_group_id . '/' . (int)$entry_id . '/';
    
    $this->load->driver('cache', array('adapter' => 'apc', 'backup' => 'file'));
    
    if (!($entry_details = $this->cache->get($cache_storage_path . 'details' ))){
      if($entry_details = $this->api->apiCall('index.php/packages/' . $code . '/' . $package_id . '/entries/' . $entry_id . '-' . $rate_group_id)){
        $cache_time = strtotime('tomorrow') - time();
        if($cache_time > 0){
          setCacheStorage($cache_storage_path);
          $this->cache->save($cache_storage_path . 'details', $entry_details, $cache_time);
        }
      }
    }
    if($entry_details){
      foreach($entry_details->Accommodation as $room_index=>$entry_packages){
        foreach($entry_packages as $package_index=>$room_details){
          $room_details->discount_percentage = 0;
          $room_details->full_price = null;
          if(!empty($room_details->Price)){
            $room_details->full_price = $room_details->Price;
            $room_details->discount_percentage = $this->getDiscountPercentage($package_id);
            $room_details->Price = $this->calculateDiscountedPrice($room_details->Price, $room_details->discount_percentage);
          }
        }
      }
    }
    return $entry_details;
  }
  function loadPackageEntryDetailsExtra($package_id, $code = '', $entry_id, $rate_group_id) {
    $cache_storage_path = 'trip/package/' . (int)$package_id . '/code/' . crc32($code) . '/entry/' . (int)$rate_group_id . '/' . (int)$entry_id . '/';
    
    $this->load->driver('cache', array('adapter' => 'apc', 'backup' => 'file'));
    
    if (!($extra_services = $this->cache->get($cache_storage_path . 'extra_services' ))){
      if($extra_services = $this->api->apiCall('index.php/packages/' . $code . '/' . $package_id . '/entries/' . $entry_id . '-' . $rate_group_id . '/extra-services')){
        $cache_time = strtotime('tomorrow') - time();
        if($cache_time > 0){
          setCacheStorage($cache_storage_path);
          $this->cache->save($cache_storage_path . 'extra_services', $extra_services, $cache_time);
        }
      }
    }
    return $extra_services;
  }
  function checkPackageAvailability($package_id, $code = '', $entry_id, $rate_group_id, $occupations=array(), $extra_services=array()) {
    $post = array(
      'occupations' => $occupations,
      'extra-services' => $extra_services,
    );
    $package_availability = $this->api->apiCall('index.php/packages/' . $code . '/' . $package_id . '/entries/' . $entry_id . '-' . $rate_group_id . '/check-availability', array(), $post);
    if($package_availability){
      $package_availability->discount_percentage = 0;
      $package_availability->full_price = null;
      if(!empty($package_availability->Amount)){
        $package_availability->full_price = $package_availability->Amount;
        $package_availability->discount_percentage = $this->getDiscountPercentage($package_id);
        $package_availability->Amount = $this->calculateDiscountedPrice($package_availability->Amount, $package_availability->discount_percentage);
      }
    }
    return $package_availability;
  }

  function loadFilters($code) {
    return $this->loadPackages($code, null, null, null, true);
  }
  function loadCountries($get) {
    return $this->api->apiCall('index.php/package-countries',$get);
  }
  function loadCountryCities($country_id, $get=array()) {
    return $this->api->apiCall('index.php/package-countries/'. $country_id . '/cities' ,$get);
  }
  function loadCityDetails($country_id, $city_id, $get=array()) {
    return $this->api->apiCall('index.php/package-countries/'. $country_id . '/cities/' . $city_id ,$get);
  }

  /* function loadLocations($q) {
    return $this->api->apiCall('package-locations.php', array(
        'q' => $q
    ));
  } */
  /* function loadMarkers($code) {
    return $this->api->apiCall('index.php/v2/packages/' . $code . '/markers');
  }
  function loadRoomPackages($code, $package_id) {
    return $this->api->apiCall('index.php/v2/packages/' . $code . '/' . $package_id . '/package');
  }
  function loadRoomPackage($code, $package_id, $package_code) {
    return $this->api->apiCall('index.php/v2/packages/' . $code . '/' . $package_id . '/package/' . $package_code);
  }
  function loadRoomPackageRooms($code, $package_id, $package_code, $rooms_combination) {
    return $this->api->apiCall('index.php/v2/packages/' . $code . '/' . $package_id . '/package/' . $package_code . '/' . $rooms_combination);
  } */

  function loadPackages($code, $page=1, $sort_type=null, $sort_order=null, $filter=null, $limit=null) {
    $data = array(
      'page' => $page,
      'filters' => is_bool($filter) ? $filter : null,
      'sortType' => $sort_type,
      'sortOrder' => !is_null($sort_order) ? ($sort_order ? 0 : 1) : null,
      'filter' => is_array($filter) ? $filter : null,
      'code' => $code,
      'limit' => $limit,
    );
    
    $cache_hash =  crc32(json_encode($data));
    $cache_storage_path = 'trip/packages/code/' . crc32($code) . '/results/';
    $this->load->driver('cache', array('adapter' => 'apc', 'backup' => 'file'));
    if (!($response = $this->cache->get($cache_storage_path . $cache_hash ))){
      cleanArray($data);
      if($response = $this->api->apiCall('index.php/packages', $data)){
        $cache_time = strtotime('tomorrow') - time();
        if($cache_time > 0){
          setCacheStorage($cache_storage_path);
          $this->cache->save($cache_storage_path . $cache_hash, $response, $cache_time);
        }
      }
    }
    
    if($response){
      $package_ids = array();
      foreach($response->_embedded->packages as $package){
        $package_ids[] = $package->Id;
      }
      $this->loadDiscountPercentages($package_ids);
      foreach($response->_embedded->packages as $package){
        $package->discount_percentage = 0;
        $package->full_price = null;
        if(!empty($package->MinPrice)){
          $package->full_price = $package->MinPrice;
          $package->discount_percentage = $this->getDiscountPercentage($package->Id, true);
          $package->MinPrice = $this->calculateDiscountedPrice($package->MinPrice, $package->discount_percentage);
        }
      }
      $this->unloadDiscountPercentages();
    }
    return $response;
  }
  function loadPackageResults($code, $page=1,$filter=null, $limit = 10) {
    return $this->loadPackages($code, $page, null, null, $filter, $limit);
  }
  private $discount_percentages = array();
  function loadDiscountPercentages($ids=array()){
    $this->load->model('TripDiscount_model');
    $general_discount = $this->getGeneralDiscountPercentage();
    $this->discount_percentages = $this->TripDiscount_model->getTypeDiscountsAssoc('package', $ids, $general_discount);
  }
  function unloadDiscountPercentages(){
    $this->discount_percentages = array();
  }
  function getGeneralDiscountPercentage(){
    static $packages_general_discount = null;
    if(!isset($packages_general_discount)){
      $this->load->model('Options_model');
      $discount = $this->Options_model->get('trip_discounts','trip_discount_package');
      $packages_general_discount = floatval($discount);
    }
    return $packages_general_discount;
  }
  function getDiscountPercentage($id, $loaded_multiple=false){
    $id = '' . $id;
    if($loaded_multiple){
      return isset($this->discount_percentages[$id]) ? $this->discount_percentages[$id] : 0;
    }
    $this->load->model('TripDiscount_model');
    $discount = $this->TripDiscount_model->getTypeDiscount('package', $id);
    if(false === $discount){
      $general_discount = $this->getGeneralDiscountPercentage();
      return $general_discount;
    }
    return floatval($discount);
  }
  function calculateDiscountedPrice($price, $discount){
    $discounted_price = $price * (1 - $discount/100);
    $discounted_price = ceil($discounted_price * 100) / 100;
    $discounted_price = number_format($discounted_price, 2, '.', '');
    return $discounted_price;
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