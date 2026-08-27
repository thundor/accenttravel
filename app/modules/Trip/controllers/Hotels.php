<?php
if (!defined('BASEPATH'))
  exit('No direct script access allowed');

class Hotels extends MX_Controller {
  function __construct() {
    $this->load->model('Trip/Hotels_model');
    parent::__construct();
  }
  protected function setData() {
    static $data_set;
    if(!is_null($data_set)){
      return;
    }
    $data_set = true;
    $hotel_id = (int)$this->input->post('hotel_id');
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
    $this->data = $this->Hotels_model->getSearchData($hotel_id, $session);
    
    $this->_getIndex();
    $this->_getContainer();
  }
  public function index() {
    $this->setData();
    $this->theme->view('trip/hotels/index', $this->data, $this);
  }
  public function search() {
    $this->setData();
    $this->theme->set_sublayout('frontend/waiting/index');
    $this->theme->view('trip/hotels/search', $this->data, $this);
  }

  public function setSearch($return = false) {
    if ($this->input->is_ajax_request()) {
      $this->setData();
      
      $data = $this->Hotels_model->getSearchDefaultData();
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

      if ($data['end_date'] < $data['start_date']) {
        $start_date = $data['start_date'];
        $data['start_date'] = $data['end_date'];
        $data['end_date'] = $start_date;
      }

      $hotel_name = trim($this->input->post('hotel_name'));
      if ($hotel_name) {
        $data['hotel_name'] = $hotel_name;
      }
      $data['hotel_id'] = $this->_getHotelId();
      $destination = trim($this->input->post('city_name'));
      if ($destination) {
        $data['city_name'] = $destination;
      }
      $country_name = trim($this->input->post('country_name'));
      if ($country_name) {
        $data['country_name'] = $country_name;
      }
      $city_id = (int) ($this->input->post('city_id'));
      if ($city_id) {
        $data['city_id'] = $city_id;
      }
      $country_id = (int) ($this->input->post('country_id'));
      if ($country_id) {
        $data['country_id'] = $country_id;
      }
      $min_stars = (int) $this->input->post('min_stars');
      if ($min_stars > 0 && $min_stars <= $this->Hotels_model->max_stars) {
        $data['min_stars'] = $min_stars;
      }

      $weekend = $this->input->post('weekend');
      $data['weekend'] = filter_var($weekend, FILTER_VALIDATE_BOOLEAN);

      $add_flight = $this->input->post('add_flight');
      $data['add_flight'] = filter_var($add_flight, FILTER_VALIDATE_BOOLEAN);
      $depart_city = trim($this->input->post('depart_city'));
      if ($depart_city) {
        $data['depart_city'] = $depart_city;
      }

      $occupancy = $this->input->post('occupancy');
      if (is_array($occupancy) && !empty($occupancy)) {
        $rooms = array();
        $expected_room_index = 0;
        foreach ($occupancy as $room_index => $occupants) {
          if ($room_index != $expected_room_index) {
            break;
          }
          if ($expected_room_index + 1 > $this->Hotels_model->max_rooms) {
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
          if ($adults <= 0 || $adults > $this->Hotels_model->max_adults_per_room) {
            break;
          }
          $room = array();
          $room['adt'] = $adults;
          $room_children_ages = array();
          $room_children_birth_dates = array();
          $children = isset($occupants['chd']) && is_array($occupants['chd']) ? $occupants['chd'] : array();
          $ages = isset($children['age']) && is_array($children['age']) ? $children['age'] : array();
          $birth_dates = isset($children['birth_date']) && is_array($children['birth_date']) ? $children['birth_date'] : array();
          $expected_child_index = 0;
          foreach ($ages as $child_index => $child_age) {
            if ($child_index != $expected_child_index) {
              break;
            }
            if ($expected_child_index + 1 > $this->Hotels_model->max_children_per_room) {
              break;
            }
            $expected_child_index++;
            if (!is_numeric($child_age)) {
              break;
            }
            if ((int) $child_age . '' !== $child_age . '') {
              break;
            }
            if ($child_age < 1 || $child_age > $this->Hotels_model->max_child_age) {
              break;
            }
            $child_age = (int) $child_age;
            $birth_date = isset($birth_dates[$child_index]) ? trim($birth_dates[$child_index]) : '';
            $room_children_ages[] = $child_age;
            $room_children_birth_dates[] = $birth_date;
          }
          if ($room_children_ages) {
            $room['chd'] = array(
              'age' => $room_children_ages,
              'birth_date' => $room_children_birth_dates,
            );
          }
          $rooms[] = $room;
        }
        if ($rooms) {
          $data['occupancy'] = $rooms;
        }
      }
      // $data['filters'] = $this->_getFilters();
      $data['filters'] = $default_search_data['filters'];
	  // print_r($data);
	  // die;
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
      
      $this->Hotels_model->setSearchData($this->data);
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
      ignore_user_abort(false);
      static $maxretries = 10;
      if ($maxretries <= 0) {
        $this->outputError('TRIP error: too many retries initiating.');
      }
      if ($maxretries < 10) {
        sleep(2);
      }
      $maxretries --;
      $data = &$this->data;
      $this->response = $this->Hotels_model->initiateSearch($this->data);
      if (!$this->response) {
        $this->outputError('TRIP error: search initiation returned no response');
      }
      if (property_exists($this->response,'Status')) {
        $this->outputError('TRIP error: response is not interpretable');
      }
      $container_id = $data['container_id'];
      if (empty($this->response->{$container_id})) {
        $this->addMessage('TRIP error: container not found, reinitating.', 'error');
        return $this->initiate();
      }
      $search_response = array_pop($this->response->{$container_id});
      $this->data['index_id'] = $_POST['index_id'] = $search_response->Id;
      $index_id = $this->_getIndex();
      $this->response = $this->Hotels_model->inspectSearchIndex($index_id);
      if (!$this->response) {
        $this->outputError('TRIP error: search index inspect returned no response');
      }
      if (empty($this->response->code)) {
        $this->addMessage('TRIP error: code parameter missing in search index result, reinitating.', 'error');
        return $this->initiate();
      }
      $this->data['code'] = $_POST['code'] = $this->response->code;
      $this->Hotels_model->setSearchData($this->data);
      return $this->loadResultsSummary(false);
    }
  }

  public function loadFilters() {
    if ($this->input->is_ajax_request()) {
      $this->setData();
      ignore_user_abort(false);
      $code = $this->_getCode();
      $this->response = $this->Hotels_model->loadFilters($code);
      if (!$this->response) {
        $this->outputError('TRIP error: filters returned no response');
      }
      $this->results = $this->response->filters ? $this->response->filters : new \stdClass();
      if (!empty($this->results->facilities)) {
        $facilities_icons_json = APPPATH . 'modules/Trip/hotel_facilities_icons.json';
        $facilities_icons = json_decode(file_get_contents($facilities_icons_json), true);
        if (!$facilities_icons) {
          $facilities_icons = array();
        }
        $write_facilities = false;
        foreach ($this->results->facilities as &$facility) {
          if (!isset($facilities_icons[$facility->Id])) {
            $facilities_icons[$facility->Id] = array('i' => 'fa fa-star');
            $write_facilities = true;
            $this->addMessage('New facilities detected. The facilities icons storage file has been updated.');
          }
          $facility->Name = html_entity_decode($facility->Name,ENT_QUOTES);

          $facility_icon = $facilities_icons[$facility->Id];
          $facility->Icon = isset($facility_icon['i']) ? $facility_icon['i'] : '';
          $facility->IconSrc = isset($facility_icon['src']) ? $this->theme->theme_url . 'assets/images/' . $facility_icon['src'] : '';
        }
        if ($write_facilities) {
          file_put_contents($facilities_icons_json, json_encode($facilities_icons, JSON_PRETTY_PRINT));
        }
      }
      $locations = array();
      $activity_ids = array();
      $activity_categories = array();
      $location_names = array();
      if (!empty($this->response->filters) && !empty($this->response->filters->activities)) {
        $activity_ids_inv = array();
        foreach ($this->response->filters->activities as &$activity) {
          $activity_ids_inv[$activity->ActivityId] = true;
          if(!isset($location_names[$activity->ActivityId])){
            $location_names[$activity->ActivityId] = array();
          }
          $location_names[$activity->ActivityId][] = $activity->Name;
        }
        $activity_ids = array_keys($activity_ids_inv);
      }
      if($activity_ids){
        $this->load->model('Trip/Hotel_activities_model');
        $locations = $this->Hotel_activities_model->getActivitiesById($activity_ids);
        foreach($locations as $k => &$location){
          $location->icon = json_decode($location->icon);
        }
        $activity_categories = $this->Hotel_activities_model->getCategoriesWithActivities($activity_ids);
        foreach($activity_categories as $k => &$activity_category){
          $activity_category->icon = json_decode($activity_category->icon);
          $activity_category->activity_ids = explode(',', $activity_category->activity_ids);
        }
      }
      $this->results->locations = $locations;
      $this->results->location_names = $location_names;
      $this->results->activity_categories = $activity_categories;
      $stars = array();
      if ($this->response->filters && $this->response->filters->stars) {
        $stars = $this->response->filters->stars;
        rsort($stars);
      }
      $this->results->stars = $stars;
      $this->output();
    }
  }

  public function loadResultsSummary($summary = false) {
    if ($this->input->is_ajax_request()) {
      $this->setData();
      ignore_user_abort(false);
      $code = $this->_getCode();
      $max_retries = isset($this->data['session']) && $this->data['session'] == '/backend/order' ? 100 : 50;
      static $maxretries;
      if(is_null($maxretries)){
        $maxretries = $max_retries;
      }
        
      if ($maxretries <= 0) {
        $this->outputError('TRIP error: too many retries loading results.');
      }
      if ($maxretries < $max_retries) {
        sleep(2);
      }
      $maxretries--;

      $this->response = $this->Hotels_model->loadHotels($code, $summary ? 'true' : '');
      if (!$this->response) {
        $this->outputError('TRIP error: search summary results with summary=' . ( $summary ? 'true' : 'null') . ' returned no response');
      }
  //    if(!$summary && !property_exists($this->response, 'summary')){
  //      $this->addMessage('TRIP error: search returned no summary');
  //      return $this->loadResultsSummary(true);
  //    }
      if(property_exists($this->response, 'status')){
        $status = !empty($this->response->status) ? $this->response->status : 0;
        $message = !empty($this->response->message) ? $this->response->message : '';
        if($status == 2){
          $this->addMessage('TRIP loading: search is ' . $message);
          return $this->loadResultsSummary(true);
        } else if($status != 1){
          $this->outputError('TRIP error: search status is not manageable');
        }
      }
      if(property_exists($this->response, 'summary')){
        $progress = !empty($this->response->summary) && !empty($this->response->summary->progress) ? $this->response->summary->progress : 0;
        $complete = $progress == 100;
        $has_progress = $progress > 0;
        if (!$complete) {
          $this->addMessage('TRIP loading: search progress ' . $progress . '/100');
          return $this->loadResultsSummary(true);
        }
        $this->results['offers'] = $this->response->summary->offers;
      }
      $_POST['filters'] = array();
      $this->Hotels_model->setSearchData($this->data);
      $this->output();
      return $this->loadResults();
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

      $request_filters = array(
        'MinPrice' => array(
          'range' => array()
        ),
        'Stars' => $filters['stars'],
        'FacilitiesId' => $filters['facilities'],
        'ActivitiesId' => $filters['activities'],
        'PointOfInterestsId' => $filters['pois'],
      );
      if($filters['max_price']){
        $request_filters['MinPrice']['range'][] = $filters['min_price']-1;
        $request_filters['MinPrice']['range'][] = $filters['max_price']+1;
      }

      $this->response = $this->Hotels_model->loadHotels($code, null, $page, $sort_by, $sort_order, $request_filters);
      if (!$this->response) {
        $this->outputError('TRIP error: search results with summary=false returned no response');
      }
      if(empty($this->response->status)){
        $this->data['hotels_expired'] = true;
        $this->outputError('No hotels found');
      }
      $this->results['hotels'] = $this->response->_embedded->hotels;
      $this->results['page'] = !empty($this->response->page) ? $this->response->page : 1;
      $this->results['page_count'] = !empty($this->response->page_count) ? $this->response->page_count : 0;
      $this->results['page_size'] = !empty($this->response->page_size) ? $this->response->page_size : 0;
      $this->results['total_items'] = !empty($this->response->total_items) ? $this->response->total_items : 0;
      
      $this->interpretHotels();
      $this->Hotels_model->setSearchData($this->data);
      $this->output();
    }
  }
  protected function interpretHotels() {
    /* $this->load->library('image_lib');
    
    $config['image_library'] = 'gd2';
    
    $config['width'] = 635;
    $config['height'] = 400;
    $config['master_dim'] = 'width';
    $hotel_image_path = FCPATH . 'cdn/hotels/images/';
    
    $tmp_path = config_item('tmp_path');
    
    $theme_path = $this->theme->config('path');
    $theme_name = $this->theme->config('theme');
    $theme_url = $this->theme->theme_url;
    
    $original_filename = 'placeholder';
    $original_file = $original_filename . '.png';
    $original_filepath = $theme_path . $theme_name . '/assets/images/' . $original_file;
    
    $this->results['placeholder_image'] = $theme_url . '/assets/images/' . $original_file;
    
    $new_filename =  $original_filename . '-' . $config['width'] . 'x' . $config['height'] . '-' . $config['master_dim'];
    $new_file =  $new_filename . '.png';
    $overwrite = false;
    if(!file_exists($hotel_image_path . $new_file) || $overwrite){
      $config['source_image'] = $original_filepath;
      $config['new_image'] = $hotel_image_path . $new_file;
      $this->image_lib->initialize($config);
      $this->image_lib->resize();
    }
    if(file_exists($hotel_image_path . $new_file)){
      $this->results['placeholder_image'] = base_url() . 'cdn/hotels/images/' . $new_file;
    } */
    
    foreach($this->results['hotels'] as $k => &$hotel){
      $hotel->Name = html_entity_decode($hotel->Name,ENT_QUOTES); 
      $hotel->Name = preg_replace('/\(.*\)/','', $hotel->Name);
      $hotel->Name = trim($hotel->Name);
      $hotel->ShortDesc = str_replace('\n', "\n",html_entity_decode($hotel->ShortDesc,ENT_QUOTES)); 
      $hotel->Address = str_replace('\n', "\n",html_entity_decode($hotel->Address,ENT_QUOTES)); 
      $hotel->link = site_url('trip/hotel/' . $hotel->Id . '?n=1');
      /* $hotel->OrigImage = $hotel->Image;
      if(empty($hotel->Image)){
        continue;
      }
      $image = $hotel->Image;
      $hotel->Image = null;
      $original_filename = $hotel->Id . '-' . md5($image);
      $original_file = $original_filename . '.png';
      $original_filepath = $tmp_path . $original_file;
      $new_filename =  $original_filename . '-' . $config['width'] . 'x' . $config['height'] . '-' . $config['master_dim'];
      $new_file =  $new_filename . '.png';
      if(!file_exists($hotel_image_path . $new_file) || $overwrite){
        if(!file_exists($original_filepath) || $overwrite){
          $file =@ fopen($image, 'r');
          if($file){
            file_put_contents($original_filepath, fopen($image, 'r'));
            if(exif_imagetype($original_filepath)) {
              $config['source_image'] = $original_filepath;
              $config['new_image'] = $hotel_image_path . $new_file;
              $this->image_lib->initialize($config);
              $this->image_lib->resize();
              $hotel->Image = base_url() . 'cdn/hotels/images/' . $new_file;
            } else {
              if(file_exists($original_filepath)){
                unlink($original_filepath);
              }
            }
          }
        }
      } else {
        $hotel->Image = base_url() . 'cdn/hotels/images/' . $new_file;
      } */
    }
  }
  public function loadLocations() {
    if ($this->input->is_ajax_request()) {
      ignore_user_abort(false);
      $q = URLify::downcode('' . $this->input->get('q'), 'en');
      $this->response = $this->Hotels_model->loadLocations($q);
      if(!$this->response){
        $this->outputError('Locatile nu au putut fi preluate pentru cautarea ' . $q);
      }
      $this->output();
    }
  }
  public function loadMarkers() {
    if ($this->input->is_ajax_request()) {
      $this->setData();
      ignore_user_abort(false);
      $code = $this->_getCode();
      $this->response = $this->Hotels_model->loadMarkers($code);
      if(!$this->response){
        $this->outputError('Markerele nu au putut fi preluate');
      }
      $this->output();
    }
  }
  public function loadRoomPackages() {
    if ($this->input->is_ajax_request()) {
      $this->setData();
      ignore_user_abort(false);
      $code = $this->_getCode();
      $hotel_id = $this->_getHotelId();
      $this->response = $this->Hotels_model->loadRoomPackages($code,$hotel_id);
      if(!$this->response){
        $this->outputError('Pachetele nu au putut fi preluate');
      }
      $this->Hotels_model->setSearchData($this->data);
      $this->output();
    }
  }
  public function loadRoomPackage() {
    if ($this->input->is_ajax_request()) {
      $this->setData();
      ignore_user_abort(false);
      $code = $this->_getCode();
      $hotel_id = $this->_getHotelId();
      $package_code = $this->_getPackageCode();
      $this->response = $this->Hotels_model->loadRoomPackage($code,$hotel_id,$package_code);
      if(!$this->response){
        $this->outputError('Pachetul nu a putut fi preluat');
      }
      $this->output();
    }
  }
  protected $response = null;
  protected $results = array();

  protected function output($status = 'success') {
    $response = array(
      'status' => $status,
      'response' => $this->response,
      'calls' => $this->Hotels_model->api->calls,
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
  protected function _getHotelId() {
    $session_hotel_id = $this->data['hotel_id'];
    $post_hotel_id = $this->input->post('hotel_id');
    if(isset($post_hotel_id)){
      $this->data['hotel_id'] = '';
      if ($post_hotel_id && $post_hotel_id>0 && ('' . (int)$post_hotel_id === '' . $post_hotel_id)) {
        $this->data['hotel_id'] = (int)$post_hotel_id;
      }
    }
    return $this->data['hotel_id'];
  }

  protected function _getSortBy() {
    $post_sort_by = $this->input->post('sort_by');
    if ($post_sort_by && ($post_sort_by != $this->data['sort_by']) 
    && is_string($post_sort_by) && in_array($post_sort_by, $this->Hotels_model->sort_types)) {
      $this->data['sort_by'] = $post_sort_by;
    }
    return $this->data['sort_by'];
  }

  protected function _getSortOrder() {
    $post_sort_order = $this->input->post('sort_order');
    if ($post_sort_order != $this->data['sort_order'] && is_numeric($post_sort_order) 
    && ('' . (int)$post_sort_order === '' . $post_sort_order) 
    && in_array((int)$post_sort_order, $this->Hotels_model->sort_orders)) {
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

    $filters['min_price'] = isset($filters['min_price']) ? floatval($filters['min_price']) : 0;
    $filters['max_price'] = isset($filters['max_price']) ? floatval($filters['max_price']) : 0;

    if($filters['min_price'] > $filters['max_price']){
      $min_price = $filters['min_price'];
      $filters['min_price'] = $filters['max_price'];
      $filters['max_price'] = $min_price;
    }
    
    $filters['stars'] = isset($filters['stars']) && is_array($filters['stars']) ? $filters['stars'] : array();
    $filters['stars'] = array_unique($filters['stars']);
    $max_stars = $this->Hotels_model->max_stars;
    foreach ($filters['stars'] as $k => $star) {
      if (( '' . (int) $star !== '' . $star) || ($star < 0) || ($star > $max_stars)) {
        unset($filters['stars'][$k]);
        continue;
      }
      $filters['stars'][$k] = (int)$star;
    }
    $filters['stars'] = array_values($filters['stars']);

    $filters['facilities'] = isset($filters['facilities']) && is_array($filters['facilities']) ? $filters['facilities'] : array();
    $filters['facilities'] = array_unique($filters['facilities']);
    foreach ($filters['facilities'] as $k => $facility) {
      if (( '' . (int) $facility !== '' . $facility) || ($facility < 0)) {
        unset($filters['facilities'][$k]);
        continue;
      }
      $filters['facilities'][$k] = (int)$facility;
    }
    $filters['facilities'] = array_values($filters['facilities']);

    $filters['activities'] = isset($filters['activities']) && is_array($filters['activities']) ? $filters['activities'] : array();
    $filters['activities'] = array_unique($filters['activities']);
    foreach ($filters['activities'] as $k => $activity) {
      if (( '' . (int) $activity !== '' . $activity) || ($activity < 0)) {
        unset($filters['activities'][$k]);
        continue;
      }
      $filters['activities'][$k] = (int)$activity;
    }
    $filters['activities'] = array_values($filters['activities']);

    $filters['activity_categories'] = isset($filters['activity_categories']) && is_array($filters['activity_categories']) ? $filters['activity_categories'] : array();
    $filters['activity_categories'] = array_unique($filters['activity_categories']);
    foreach ($filters['activity_categories'] as $k => $activity) {
      if (( '' . (int) $activity !== '' . $activity) || ($activity < 0)) {
        unset($filters['activity_categories'][$k]);
        continue;
      }
      $filters['activity_categories'][$k] = (int)$activity;
    }
    $filters['activity_categories'] = array_values($filters['activity_categories']);

    $filters['pois'] = isset($filters['pois']) && is_array($filters['pois']) ? $filters['pois'] : array();
    $filters['pois'] = array_unique($filters['pois']);
    foreach ($filters['pois'] as $k => $poi) {
      if (( '' . (int) $poi !== '' . $poi) || ($poi < 0)) {
        unset($filters['pois'][$k]);
        continue;
      }
      $filters['pois'][$k] = (int)$poi;
    }
    $filters['pois'] = array_values($filters['pois']);

    $this->data['filters'] = $filters;
    return $this->data['filters'];
  }
}