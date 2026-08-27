<?php
if (!defined('BASEPATH'))
  exit('No direct script access allowed');

class Packages extends MX_Controller {
  function __construct() {
    $this->load->model('Trip/Packages_model');
    $this->load->driver('cache', array('adapter' => 'apc', 'backup' => 'file'));
    parent::__construct();
    
    if (!$cache_check = $this->cache->get('trip/packages/cache_check')){
      setCacheStorage('trip/packages/');
      clearExpiredCache('trip/packages', $this->cache);
      $this->cache->save('trip/packages/cache_check', 1, 86400);
    }
  }
  protected function setData() {
    static $data_set;
    if(!is_null($data_set)){
      return;
    }
    $data_set = true;
    $package_id = (int)$this->input->post('package_id');
    $session = true;
    $ignore_session = filter_var($this->input->post('ignore_session'), FILTER_VALIDATE_BOOLEAN);
    if($ignore_session){
      $session = false;
    } else {
      $post_session = $this->input->post('session');
      if(isset($post_session) && is_string($post_session)){
        $session = $post_session;
      }
    }
    $this->data = $this->Packages_model->getSearchData($package_id, $session);
    
    $this->_getIndex();
    $this->_getContainer();
  }
  public function index() {
    $this->setData();
    $this->theme->view('trip/packages/index', $this->data, $this);
  }
  public function search() {
    $this->setData();
    $this->theme->set_sublayout('frontend/waiting/index');
    $this->theme->view('trip/packages/search', $this->data, $this);
  }

  public function setSearch($return = false) {
    if ($this->input->is_ajax_request()) {
      $this->setData();
      
      $data = $this->Packages_model->getSearchDefaultData();
	  $default_search_data = $data;
	  
      $start_date = $this->input->post('start_date');
      $end_date = $this->input->post('end_date');

      $date_format = 'Y-m-d';
      $d = DateTime::createFromFormat($date_format, $start_date);
      if ($d && $d->format($date_format) == $start_date) {
        $data['start_date'] = $start_date;
      }
      $d = DateTime::createFromFormat($date_format, $end_date);
      if ($d && $d->format($date_format) == $end_date) {
        $data['end_date'] = $end_date;
      }
      $hotel_name = trim($this->input->post('hotel_name'));
      if ($hotel_name) {
        $data['hotel_name'] = $hotel_name;
      }

      if ($data['end_date'] < $data['start_date']) {
        $start_date = $data['start_date'];
        $data['start_date'] = $data['end_date'];
        $data['end_date'] = $start_date;
      }
      $data['package_id'] = (int) ($this->input->post('package_id'));
      $category = ($this->input->post('category'));
      if (strlen($category)) {
        $data['category'] = $category;
      }
      $project_id = ($this->input->post('project_id'));
      if (strlen($project_id)) {
        $data['project_id'] = $project_id;
      }
      $nights = $this->input->post('nights');
      if (isset($nights) && $nights>=0) {
        $data['nights'] = $nights;
      }
      $destination_id = (int) ($this->input->post('city_id'));
      if ($destination_id) {
        $data['city_id'] = $destination_id;
      }

      $occupancy = $this->input->post('occupancy');
      if (is_array($occupancy) && !empty($occupancy)) {
        $rooms = array();
        $expected_room_index = 0;
        foreach ($occupancy as $room_index => $occupants) {
          if ($room_index != $expected_room_index) {
            break;
          }
          if ($expected_room_index + 1 > $this->Packages_model->max_rooms) {
            break;
          }
          $expected_room_index ++;
          if (!is_array($occupants) || empty($occupants)) {
            break;
          }
          if (!isset($occupants['adt'])) {
            break;
          }
          $adults = $occupants['adt'];
          if (!is_numeric($adults)) {
            break;
          }
          if ((int) $adults . '' !== $adults . '') {
            break;
          }
          $adults = (int) $adults;
          if ($adults <= 0 || $adults > $this->Packages_model->max_adults_per_room) {
            break;
          }
          $room = array();
          $room['adt'] = $adults;
          $room_children_ages = array();
          $room_children_birth_dates = array();
          $children = isset($occupants['chd']) && is_array($occupants['chd']) ? $occupants['chd'] : array();
          $expected_child_index = 0;
          foreach ($children as $child_index => $child_age) {
            if ($child_index != $expected_child_index) {
              break;
            }
            if ($expected_child_index + 1 > $this->Packages_model->max_children_per_room) {
              break;
            }
            $expected_child_index++;
            if (!is_numeric($child_age)) {
              break;
            }
            if ((int) $child_age . '' !== $child_age . '') {
              break;
            }
            if ($child_age < 1 || $child_age > $this->Packages_model->max_child_age) {
              break;
            }
            $child_age = (int) $child_age;

            $room_children_ages[] = $child_age;
          }
          if ($room_children_ages) {
            $room['chd'] = $room_children_ages;
          }
          $rooms[] = $room;
        }
        if ($rooms) {
          $data['occupancy'] = $rooms;
        }
      }
      $this->data = $data;
      $ignore_session = filter_var($this->input->post('ignore_session'), FILTER_VALIDATE_BOOLEAN);
      if($ignore_session){
        $this->data['ignore_session'] = 1;
      } else {
        $post_session = $this->input->post('session');
        if(isset($post_session) && is_string($post_session)){
          $this->data['session'] = $post_session;
        }
      }
      $this->Packages_model->setSearchData($this->data);
      if ($return) {
        return;
      }
      $this->output();
    }
  }

  public function setSearchAndInitiate() {
    if ($this->input->is_ajax_request()) {
      $this->setData();
      $this->setSearch(true);
      $this->initiate();
    }
  }

  public function initiate() {
    if ($this->input->is_ajax_request()) {
      $this->setData();
      $this->data['code'] = $_POST['code'] = NULL;
      $this->data['index_id'] = $_POST['index_id'] = NULL;
      $this->data['container_id'] = session_id();
      $this->load->driver('cache', array('adapter' => 'apc', 'backup' => 'file'));
      $cache_request = array(
        $this->data['start_date'],
        $this->data['occupancy'],
        (int)$this->data['nights'],
        (int)$this->data['city_id'],
        $this->data['category'],
      );
      
      $cache_storage_path = 'trip/packages/search/';
      $cache_hash = crc32(json_encode($cache_request));
      $cache_storage_code_path = 'trip/packages/code/';
      if ($cached_code = $this->cache->get($cache_storage_path . $cache_hash)){
        if ($cached_search_data = $this->cache->get($cache_storage_code_path . crc32($cached_code) . '/search')){
          // $response = $this->Packages_model->inspectSearchIndex($cached_search_data->index_id);
          // if ($response && !empty($response->code) && $response->code === $cached_code) {
          $this->data['container_id'] = $cached_search_data->container_id;
          $this->data['index_id'] = $cached_search_data->index_id;
          $this->data['code'] = $cached_code;
          $this->data['cache_hash'] = $cache_hash;
          $this->Packages_model->setSearchData($this->data);
          $this->output();
          // } else {
            // $this->cache->delete($cache_storage_code_path . crc32($cached_code));
            // $this->cache->delete($cache_storage_path . $cache_hash);
          // }
        } else {
          $this->cache->delete($cache_storage_path . $cache_hash);
        }
      }
      
      ignore_user_abort(false);
      static $maxretries = 10;
      if ($maxretries <= 0) {
        $this->outputError('TRIP error: too many retries initiating.');
      }
      if ($maxretries < 10) {
        sleep(1);
      }
      $maxretries --;
        
      $this->response = $this->Packages_model->initiateSearch($this->data);
      if (!$this->response) {
        $this->outputTripError('TRIP error: search initiation returned no response');
      }
      if (property_exists($this->response,'Status')) {
        $this->outputError('TRIP error: response is not interpretable');
      }
      $container_id = $this->data['container_id'];
      if (empty($this->response->{$container_id})) {
        $this->addMessage('TRIP error: container not found, reinitating.', 'error');
        return $this->initiate();
      }
      $search_response = array_pop($this->response->{$container_id});
      $this->data['index_id'] = $_POST['index_id'] = $search_response->Id;
      $index_id = $this->_getIndex();
      
      $this->inspectSearch();
      $this->inspectSearchIndex();
      $this->Packages_model->setSearchData($this->data);
      
      $code = $this->data['code'];
      if(strlen($code)){
        $cache_time = strtotime('tomorrow') - time();
        if($cache_time > 0){
          $cached_search_data = new stdClass;
          $cached_search_data->container_id = $container_id;
          $cached_search_data->index_id = $index_id;
          setCacheStorage($cache_storage_code_path . crc32($code));
          $this->cache->save($cache_storage_code_path . crc32($code) . '/search', $cached_search_data, $cache_time);
          setCacheStorage($cache_storage_path);
          $this->cache->save($cache_storage_path . $cache_hash, $code, $cache_time);
        }
      }
      $this->output();
      // return $this->loadResultsSummary(false);
    }
  }

  protected function inspectSearch() {
    static $maxretries = 10;
    if ($maxretries <= 0) {
      $this->outputError('TRIP error: too many retries initiating.');
    }
    if ($maxretries < 10) {
      sleep(1);
    }
    $maxretries --;
    $container_id = $this->_getContainer();
    $this->response = $this->Packages_model->inspectSearch($container_id);
    if (!$this->response) {
      return $this->inspectSearch();
      $this->outputTripError('TRIP error: search inspect returned no response');
    }
    $search_response = array_pop($this->response);
    if (empty($search_response->Status)) {
      $this->outputError('TRIP error: the search failed');
    }
    if($search_response->Status == 2){
      $this->inspectSearch();
    }
  }
  protected function inspectSearchIndex() {
    static $maxretries = 10;
    if ($maxretries <= 0) {
      $this->outputError('TRIP error: too many retries initiating.');
    }
    if ($maxretries < 10) {
      sleep(1);
    }
    $maxretries --;
    $index_id = $this->_getIndex();
    $this->response = $this->Packages_model->inspectSearchIndex($index_id);
    if (!$this->response) {
      return $this->inspectSearchIndex();
      $this->outputTripError('TRIP error: search index inspect returned no response 2');
    }
    if (empty($this->response->code)) {
      $this->addMessage('TRIP error: code parameter missing in search index result, reinitating.', 'error');
      return $this->inspectSearch();
    }
    $this->data['code'] = $_POST['code'] = $this->response->code;
  }
  public function loadFilters() {
    if ($this->input->is_ajax_request()) {
      $this->setData();
      ignore_user_abort(false);
      $code = $this->_getCode();
      $this->response = $this->Packages_model->loadFilters($code);
      if (!$this->response) {
        $this->outputTripError('TRIP error: filters returned no response');
      }
      $this->output();
    }
  }
  public function loadResults() {
    if ($this->input->is_ajax_request()) {
      $this->setData();
      ignore_user_abort(false);
      $code = $this->_getCode();
      $page = $this->_getPage();
      $sort_by = $this->_getSortBy();
      $sort_order = $this->_getSortOrder();
      $filters = $this->_getFilters();
      $limit = 10;
      $request_filters = array();
      if(isset($filters['Name'])){
        $filter = array(
          'type' => 'like',
          'name' => 'Name',
          'term' => $filters['Name'],
        );
        $request_filters[] = $filter;
      }

      $this->response = $this->Packages_model->loadPackageResults($code, $page, $request_filters, $limit);
      if (!$this->response) {
        $this->load->driver('cache', array('adapter' => 'apc', 'backup' => 'file'));
        $cache_storage_code_path = 'trip/packages/code/';
        $this->cache->delete($cache_storage_code_path . crc32($code));
        $this->outputTripError('TRIP error: search results returned no response');
      }
      // if(empty($this->response->status)){
        // $this->data['packages_expired'] = true;
        // $this->outputError('No packages found');
      // }
      $this->results['packages'] = $this->response->_embedded->packages;
      $this->results['page'] = !empty($this->response->page) ? $this->response->page : 1;
      $this->results['page_count'] = !empty($this->response->page_count) ? $this->response->page_count : 0;
      $this->results['page_size'] = !empty($this->response->page_size) ? $this->response->page_size : 0;
      $this->results['total_items'] = !empty($this->response->total_items) ? $this->response->total_items : 0;
      
      $this->interpretPackages();
      $this->Packages_model->setSearchData($this->data);
      $this->output();
    }
  }
  protected function interpretPackages() {
    foreach($this->results['packages'] as $k => $package){
      $package->Name = html_entity_decode($package->Name,ENT_QUOTES); 
      $package->Name = trim($package->Name);
      $package->ProjectName = html_entity_decode($package->ProjectName,ENT_QUOTES); 
      $package->Category = html_entity_decode($package->Category,ENT_QUOTES); 
      $package->Description = isset($package->Description) ? html_entity_decode($package->Description,ENT_QUOTES) : ''; 
      $package->link = site_url('trip/package/' . $package->Id . '?n=1');
    }
  }
  protected $response = null;
  protected $results = array();

  protected function output($status = 'success') {
    $response = array(
      'status' => $status,
      'response' => $this->response,
      'calls' => $this->Packages_model->api->calls,
      'results' => $this->results,
      'message' => $this->message,
      'messages' => $this->messages,
      'data' => $this->data,
    );

    echo json_encode($response);
    die;
  }

  protected function _getPage() {
    $page = $this->input->post('page');
    if (!is_numeric($page) || '' . (int) $page !== '' . $page || $page < 1) {
      return 1;
    }
    return $page;
  }

  protected function _getIndex() {
    $session_index_id = $this->data['index_id'];
    $post_index_id = $this->input->post('index_id');
    if ($post_index_id && ($post_index_id != $session_index_id)) {
      $this->data['index_id'] = $post_index_id;
    }
    return $this->data['index_id'];
  }
  protected function _getContainer() {
//    $session_container_id = $this->data['container_id'];
    $post_container_id = $this->input->post('container_id');
//    if ($post_container_id && ($post_container_id != $session_container_id)) {
      $this->data['container_id'] = $post_container_id;
//    }
    return $this->data['container_id'];
  }
  protected function _getCode() {
    $session_code = $this->data['code'];
    $post_code = $this->input->post('code');
    if ($post_code && ($post_code != $session_code)) {
      $this->data['code'] = $post_code;
    }
    return $this->data['code'];
  }
  protected function _getPackageCode() {
    $session_code = $this->data['package_code'];
    $post_code = $this->input->post('package_code');
    if ($post_code && ($post_code != $session_code)) {
      $this->data['package_code'] = $post_code;
    }
    return $this->data['package_code'];
  }
  protected function _getPackageId() {
    $session_package_id = $this->data['package_id'];
    $post_package_id = $this->input->post('package_id');
    if(isset($post_package_id)){
      $this->data['package_id'] = '';
      if ($post_package_id && $post_package_id>0 && ('' . (int)$post_package_id === '' . $post_package_id)) {
        $this->data['package_id'] = (int)$post_package_id;
      }
    }
    return $this->data['package_id'];
  }

  protected function _getSortBy() {
    $post_sort_by = $this->input->post('sort_by');
    if ($post_sort_by && ($post_sort_by != $this->data['sort_by']) 
    && is_string($post_sort_by) && in_array($post_sort_by, $this->Packages_model->sort_types)) {
      $this->data['sort_by'] = $post_sort_by;
    }
    return $this->data['sort_by'];
  }

  protected function _getSortOrder() {
    $post_sort_order = $this->input->post('sort_order');
    if ($post_sort_order != $this->data['sort_order'] && is_numeric($post_sort_order) 
    && ('' . (int)$post_sort_order === '' . $post_sort_order) 
    && in_array((int)$post_sort_order, $this->Packages_model->sort_orders)) {
      $this->data['sort_order'] = (int)$post_sort_order;
    }
    return $this->data['sort_order'];
  }

  protected function _getFilters() {
    $session_filters = $this->data['filters'];
    $post_filters = $this->input->post('filters');
    $filters = $post_filters;
    if (is_null($filters)) {
      $filters = $session_filters;
    }
    if (!$filters || !is_array($filters)) {
      $filters = array();
    }
    if(!isset($filters['Name']) && isset($this->data['hotel_name']) && strlen($this->data['hotel_name'])){
      $filters['Name'] = $this->data['hotel_name'];
    }
    $filters['Name'] = isset($filters['Name']) && strlen(trim($filters['Name'])) ? trim($filters['Name']) : null;
    $this->data['filters'] = $filters;
    return $this->data['filters'];
  }
}