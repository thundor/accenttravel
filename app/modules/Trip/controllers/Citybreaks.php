<?php
if (!defined('BASEPATH'))
  exit('No direct script access allowed');

class CityBreaks extends MX_Controller {
  function __construct() {
    $this->load->model('Trip/Citybreaks_model');
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
    $this->data = $this->Citybreaks_model->getSearchData($hotel_id, $session);
    
    $this->_getFlightIndex();
    $this->_getIndex();
    $this->_getContainer();
  }
  public function index() {
    $this->setData();
    $this->theme->view('trip/citybreaks/index', $this->data, $this);
  }
  public function search() {
    $this->setData();
    
    // echo '<pre>';
    // print_r($this->data);
    // die;
    $this->theme->set_sublayout('frontend/waiting/index');
    $this->theme->view('trip/citybreaks/search', $this->data, $this);
  }

  public function setSearch($return = false) {
    if ($this->input->is_ajax_request()) {
      $this->setData();
      
      $data = $this->Citybreaks_model->getSearchDefaultData();
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
      $departure = trim($this->input->post('departure'));
      if ($departure) {
        $data['departure'] = $departure;
      }
      $arrival = trim($this->input->post('arrival'));
      if ($arrival) {
        $data['arrival'] = $arrival;
      }
      if ($data['end_date'] < $data['start_date']) {
        $start_date = $data['start_date'];
        $data['start_date'] = $data['end_date'];
        $data['end_date'] = $start_date;
      }
      $origin_city_id = (int) ($this->input->post('origin_city_id'));
      if ($origin_city_id>=0) {
        $data['origin_city_id'] = $origin_city_id;
      }
      $origin_country_id = (int) ($this->input->post('origin_country_id'));
      if ($origin_country_id>=0) {
        $data['origin_country_id'] = $origin_country_id;
      }
      $origin_location_id = (int) ($this->input->post('origin_location_id'));
      if ($origin_location_id>=0) {
        $data['origin_location_id'] = $origin_location_id;
      }
      $origin_full_location_name = trim($this->input->post('origin_full_location_name'));
      $data['origin_full_location_name'] = $origin_full_location_name;
      $origin_location_name = trim($this->input->post('origin_location_name'));
      $data['origin_location_name'] = $origin_location_name;
      $origin_city_name = trim($this->input->post('origin_city_name'));
      $data['origin_city_name'] = $origin_city_name;
      $origin_country_name = trim($this->input->post('origin_country_name'));
      $data['origin_country_name'] = $origin_country_name;
      
      $destination_city_id = (int) ($this->input->post('destination_city_id'));
      if ($destination_city_id>=0) {
        $data['destination_city_id'] = $destination_city_id;
      }
      $destination_country_id = (int) ($this->input->post('destination_country_id'));
      if ($destination_country_id>=0) {
        $data['destination_country_id'] = $destination_country_id;
      }
      $destination_location_id = (int) ($this->input->post('destination_location_id'));
      if ($destination_location_id>=0) {
        $data['destination_location_id'] = $destination_location_id;
      }
      $destination_full_location_name = trim($this->input->post('destination_full_location_name'));
      $data['destination_full_location_name'] = $destination_full_location_name;
      $destination_location_name = trim($this->input->post('destination_location_name'));
      $data['destination_location_name'] = $destination_location_name;
      $destination_city_name = trim($this->input->post('destination_city_name'));
      $data['destination_city_name'] = $destination_city_name;
      $destination_country_name = trim($this->input->post('destination_country_name'));
      $data['destination_country_name'] = $destination_country_name;
      
      $occupancy = $this->input->post('occupancy');
      if (is_array($occupancy) && !empty($occupancy)) {
        $rooms = array();
        $expected_room_index = 0;
        foreach ($occupancy as $room_index => $occupants) {
          if ($room_index != $expected_room_index) {
            break;
          }
          if ($expected_room_index + 1 > $this->Citybreaks_model->max_rooms) {
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
          if ($adults <= 0 || $adults > $this->Citybreaks_model->max_adults_per_room) {
            break;
          }
          $room = array();
          $room['adt'] = $adults;
          $room_children_ages = array();
          $children = isset($occupants['chd']) && is_array($occupants['chd']) ? $occupants['chd'] : array();
          $ages = isset($children['age']) && is_array($children['age']) ? $children['age'] : array();
          $expected_child_index = 0;
          foreach ($ages as $child_index => $child_age) {
            if ($child_index != $expected_child_index) {
              break;
            }
            if ($expected_child_index + 1 > $this->Citybreaks_model->max_children_per_room) {
              break;
            }
            $expected_child_index++;
            if (!is_numeric($child_age)) {
              break;
            }
            if ((int) $child_age . '' !== $child_age . '') {
              break;
            }
            if ($child_age < 1 || $child_age > $this->Citybreaks_model->max_child_age) {
              break;
            }
            $child_age = (int) $child_age;
            $room_children_ages[] = $child_age;
          }
          if ($room_children_ages) {
            $room['chd'] = array(
              'age' => $room_children_ages
            );
          }
          $rooms[] = $room;
        }
        if ($rooms) {
          $data['occupancy'] = $rooms;
        }
      }
      $passengers_adult = (int) ($this->input->post('passengers_adult'));
      if ($passengers_adult>0) {
        $data['passengers_adult'] = $passengers_adult;
      }
      $passengers_senior = (int) ($this->input->post('passengers_senior'));
      if ($passengers_senior>=0) {
        $data['passengers_senior'] = $passengers_senior;
      }
      $passengers_youth = (int) ($this->input->post('passengers_youth'));
      if ($passengers_youth>=0) {
        $data['passengers_youth'] = $passengers_youth;
      }
      $passengers_child = (int) ($this->input->post('passengers_child'));
      if ($passengers_child>=0) {
        $data['passengers_child'] = $passengers_child;
      }
      $passengers_infant_lap = (int) ($this->input->post('passengers_infant_lap'));
      if ($passengers_infant_lap>=0) {
        $data['passengers_infant_lap'] = $passengers_infant_lap;
      }
      $passengers_infant_seat = (int) ($this->input->post('passengers_infant_seat'));
      if ($passengers_infant_seat>=0) {
        $data['passengers_infant_seat'] = $passengers_infant_seat;
      }
      // $data['filters'] = $this->_getFilters();
	  $data['filters'] = $default_search_data['filters'];
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
      $this->Citybreaks_model->setSearchData($this->data);
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
      $this->response = $this->Citybreaks_model->initiateSearch($this->data);
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
      $container_response = $this->response->{$container_id};
      $this->container_response = $container_response;
      $this->flight_container_response = array_pop($container_response);
      $this->hotel_container_response = array_pop($container_response);
      $this->initiateHotels();
      $this->initiateFlights();
      return $this->loadResultsSummary(false);
    }
  }
  protected function initiateHotels() {
    static $maxretries = 100;
    if ($maxretries <= 0) {
      $this->outputError('TRIP error: too many retries initiating hotels.');
    }
    if ($maxretries < 100) {
      sleep(2);
    }
    $maxretries --;
    $data = &$this->data;
    $this->data['index_id'] = $_POST['index_id'] = $this->hotel_container_response->Id;
    $index_id = $this->_getIndex();
    $this->response = $this->Citybreaks_model->inspectSearchIndex($index_id);
    if (!$this->response) {
      $this->outputError('TRIP error: hotel search index inspect returned no response');
    }
    if (empty($this->response->code)) {
      $this->addMessage('TRIP error: hotel code parameter missing in search index result, reinitating.', 'error');
      return $this->initiateHotels();
    }
    $this->data['code'] = $_POST['code'] = $this->response->code;
    $this->Citybreaks_model->setSearchData($this->data);
    return true;
  }
  protected function initiateFlights() {
    static $maxretries = 100;
    if ($maxretries <= 0) {
      $this->outputError('TRIP error: too many retries initiating flights.');
    }
    if ($maxretries < 100) {
      sleep(2);
    }
    $maxretries --;
    $data = &$this->data;
    $this->data['flight_index_id'] = $_POST['flight_index_id'] = $this->flight_container_response->Id;
    $flight_index_id = $this->_getFlightIndex();
    $this->response = $this->Citybreaks_model->inspectSearchIndex($flight_index_id);
    if (!$this->response) {
      $this->outputError('TRIP error: flight search index inspect returned no response');
    }
    if (empty($this->response->code)) {
      $this->addMessage('TRIP error: flight code parameter missing in search index result, reinitating.', 'error');
      return $this->initiateFlights();
    }
    $this->data['flight_code'] = $_POST['flight_code'] = $this->response->code;
    $this->Citybreaks_model->setSearchData($this->data);
    return true;
  }

  public function loadFilters() {
    if ($this->input->is_ajax_request()) {
      $this->setData();
      ignore_user_abort(false);
      $code = $this->_getCode();
      $this->response = $this->Citybreaks_model->loadFilters($code);
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
      if ($this->response->filters && $this->response->filters->activities) {
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
      if ($maxretries < 10) {
        sleep(2);
      }
      $maxretries--;

      $this->response = $this->Citybreaks_model->loadHotels($code, $summary ? 'true' : '');
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
        if (!$complete) {
          $this->addMessage('TRIP loading: search progress ' . $progress . '/100');
          return $this->loadResultsSummary(true);
        }
        $this->results['offers'] = $this->response->summary->offers;
      }
      $_POST['filters'] = array();
      $this->Citybreaks_model->setSearchData($this->data);
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

      $this->response = $this->Citybreaks_model->loadHotels($code, null, $page, $sort_by, $sort_order, $request_filters);
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
      
      // $results_json = APPPATH . 'modules/Trip/flights_response.json';
      // $response = json_decode(file_get_contents($results_json));

      $code = $this->_getFlightCode();
      $this->response = $this->Citybreaks_model->loadFlights($code, 1);
      /* if($response && $response->code === $code){
        $this->response = &$response;
      } else {
        $this->response = $this->Citybreaks_model->loadFlights($code, 1);
        file_put_contents($results_json, json_encode($this->response, JSON_PRETTY_PRINT));
      } */
      if (!$this->response) {
        $this->outputError('TRIP error: search results returned no response');
      }
      if(empty($this->response->code)){
        $this->data['flights_expired'] = true;
        $this->outputError('No flights found');
      }
      $this->results['all_flights'] = $this->response->_embedded->flights;
      $this->interpretFlights();
      $this->Citybreaks_model->setSearchData($this->data);
      $this->output();
    }
  }
  protected function interpretFlights() {
    $this->results['flights'] = array();
    $this->results['flight_price'] = 0;
    if($this->results['all_flights']){
      $flight = false;
      foreach($this->results['all_flights'] as $potential_flight){
        $potential_flight_ok = false;
        $potential_combinations = $potential_flight->Combinations;
        $combinations = array();
        
        $dep_index_ref = array();
        $ret_index_ref = array();
        
        foreach($potential_flight->Routes[0]->Route as $k=>$route){
          $dep_index_ref[intval($route->Ref)] = intval($k);
        }
        foreach($potential_flight->Routes[1]->Route as $k=>$route){
          $ret_index_ref[intval($route->Ref)] = intval($k);
        }
        
        $dep_routes = array();
        $ret_routes = array();
        foreach($potential_combinations as $potential_combination){
          list($comb_dep, $comb_ret) = explode('|', $potential_combination);
          $dep = intval(substr($comb_dep,1));
          $ret = intval(substr($comb_ret,1));
          $dep_route = $potential_flight->Routes[0]->Route[$dep_index_ref[$dep]];
          $last_dep_segment = end($dep_route->Segment);
          $ret_route = $potential_flight->Routes[1]->Route[$ret_index_ref[$ret]];
          $first_ret_segment = $ret_route->Segment[0];
          if($last_dep_segment->Destination->Date == $this->data['start_date'] && $first_ret_segment->Origin->Date == $this->data['end_date']){
            $dep_routes[$dep] = true;
            $ret_routes[$ret] = true;
            $combinations[] = $potential_combination;
          }
        }
        $potential_flight_ok = !empty($combinations);
        if($potential_flight_ok){
          $potential_flight->DepRoutes = $dep_routes;
          $potential_flight->RetRoutes = $ret_routes;
          $potential_flight->Combinations = $combinations;
          $flight = $potential_flight;
          break;
        }
      }
      if(!$flight){
        return;
      }
      $flight_price = $flight->Price;
      $this->results['flight_price'] = $flight_price;
      foreach($this->results['hotels'] as $k => &$hotel){
        $hotel->Price = $hotel->MinPrice;
        $hotel->FlightPrice = $flight_price;
        $hotel->MinPrice += $flight_price;
      }
      $this->results['flights'] = array(&$flight);
    }
    $flights = &$this->results['flights'];
    $min_price = null;
    $max_price = null;
    $escale = array();
    $escale_tur = array();
    $escale_retur = array();
    $companii_marketing = array();
    $classes = array();
    $cabin_types = array();
    /* 
    $this->load->library('image_lib');
    
    $config['image_library'] = 'gd2';
    $config['width'] = 25;
    $config['height'] = 25;
    $config['master_dim'] = 'height';
    
    $theme_path = $this->theme->config('path');
    $theme_name = $this->theme->config('theme');
    
    $original_filename = 'placeholder_companie';
    $original_file =  $original_filename . '.png';
    $original_filepath = $theme_path . $theme_name . '/assets/images/' . $original_file;

    $new_filename =  $original_filename . '-' . $config['width'] . 'x' . $config['height'] . '-' . $config['master_dim'];
    $new_file =  $new_filename . '.png';
    $cdn_image_path = FCPATH . 'cdn/airlines/images/';
    $overwrite = false;
    $placeholder_companie = null;
    if(!file_exists($cdn_image_path . $new_file) || $overwrite){
      if(file_exists($original_filepath)){
        $config['source_image'] = $original_filepath;
        $config['new_image'] = $cdn_image_path . $new_file;
        $this->image_lib->initialize($config);
        $this->image_lib->resize();
      }
    }
    if(file_exists($cdn_image_path . $new_file)){
      $placeholder_companie = base_url() . 'cdn/airlines/images/' . $new_file;
    } */
    $placeholder_companie = $this->theme->theme_url . 'assets/images/placeholder_companie.png';
    
    // $escale_available = array();
    foreach($flights as &$flight){
      // $escale_tur_retur = array();
      if(is_null($min_price) || $flight->PriceDetail->Amount < $min_price){
        $min_price = $flight->PriceDetail->Amount;
      }
      if(is_null($max_price) || $flight->PriceDetail->Amount > $max_price){
        $max_price = $flight->PriceDetail->Amount;
      }
      foreach($flight->Routes as $rk => &$route){
        foreach($route->Route as $srk => &$subroute){
          $subroute->escale = count($subroute->Segment) -1;
          $escale[$subroute->escale] = $subroute->escale;
          // $escale_tur_retur[$rk . $subroute->Ref] = $subroute->escale;
          if($rk == 0) {
            $escale_tur[$subroute->escale] = $subroute->escale;
          }
          if($rk == 1) {
            $escale_retur[$subroute->escale] = $subroute->escale;
          }
          foreach($subroute->Segment as $sk => $segment){
            $companie_marketing = $segment->Carrier->Marketing;
            $companii_marketing[$companie_marketing->Code] = array('code'=>$companie_marketing->Code,'name'=>$companie_marketing->_, 'img'=>$placeholder_companie);
            $classes[$segment->Flight->Class] = $segment->Flight->Class;
            $cabin_types[$segment->Flight->CabinType] = $segment->Flight->CabinType;
          }
        }
      }
      // foreach($flight->Combinations as $combination){
        // list($dep_comb,$ret_comb) = explode('|', $combination);
      // }
    }
    $companies_from_db = array();
    if($companii_marketing){
      $company_codes = array_keys($companii_marketing);
      $this->load->model('Trip/Flights_airlines_model');
      $companies_from_db = $this->Flights_airlines_model->getAirlines(array('code' => $company_codes));
    }
    foreach($companies_from_db as $k=>$company_from_db){
      if($company_from_db->image){
        /* $original_filename = $company_from_db->image;
        $original_file =  $original_filename . '.png';
        $original_filepath = $theme_path . $theme_name . '/assets/images/' . $original_file;
        
        $new_filename =  $original_filename . '-' . $config['width'] . 'x' . $config['height'] . '-' . $config['master_dim'];
        $new_file =  $new_filename . '.png';
        $cdn_image_path = FCPATH . 'cdn/airlines/images/';
        $overwrite = false;
        if(!file_exists($cdn_image_path . $new_file) || $overwrite){
          if(file_exists($original_filepath)){
            $config['source_image'] = $original_filepath;
            $config['new_image'] = $cdn_image_path . $new_file;
            $this->image_lib->initialize($config);
            $this->image_lib->resize();
          }
        }
        if(file_exists($cdn_image_path . $new_file)){
          $companii_marketing[$company_from_db->code]['img'] = base_url() . 'cdn/airlines/images/' . $new_file;
        } */
        $companii_marketing[$company_from_db->code]['img'] = $this->theme->theme_url . 'assets/images/' . $company_from_db->image;
      }
    }
    asort($escale);
    $this->results['companies'] = array_values($companii_marketing);
    $this->results['companies_indexes'] = array_combine(array_keys($companii_marketing), array_keys($this->results['companies']));
    $this->results['stops'] = array_values($escale);
    $this->results['stops_tur'] = array_values($escale_tur);
    $this->results['stops_retur'] = array_values($escale_retur);
    $this->results['min_price'] = &$min_price;
    $this->results['max_price'] = &$max_price;
    $this->results['classes'] = array_values($classes);
    $this->results['cabin_types'] = array_values($cabin_types);
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
      $hotel->ShortDesc = html_entity_decode($hotel->ShortDesc,ENT_QUOTES); 
      $hotel->Address = html_entity_decode($hotel->Address,ENT_QUOTES); 
      $hotel->OrigImage = $hotel->Image;
      $hotel->link = site_url('trip/citybreak/' . $hotel->Id . '?n=1');
      /* if(empty($hotel->Image)){
        continue;
      }
      $image = $hotel->Image;
      $original_filename = $hotel->Id . '-' . md5($image);
      $original_file = $original_filename . '.png';
      $original_filepath = $tmp_path . $original_file;
      $new_filename =  $original_filename . '-' . $config['width'] . 'x' . $config['height'] . '-' . $config['master_dim'];
      $new_file =  $new_filename . '.png';
      if(!file_exists($hotel_image_path . $new_file) || $overwrite){
        if(!file_exists($original_filepath) || $overwrite){
          file_put_contents($original_filepath, fopen($image, 'r'));
        }
        $config['source_image'] = $original_filepath;
        $config['new_image'] = $hotel_image_path . $new_file;
        $this->image_lib->initialize($config);
        $this->image_lib->resize();
      }
      
      $hotel->Image = base_url() . 'cdn/hotels/images/' . $new_file; */
    }
  }
  protected $response = null;
  protected $results = array();

  protected function output($status = 'success') {
    $response = array(
      'status' => $status,
      'response' => $this->response,
      'calls' => $this->Citybreaks_model->api->calls,
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

  protected function _getFlightIndex() {
    $session_index_id = $this->data['flight_index_id'];
    $post_index_id = $this->input->post('flight_index_id');
    if ($post_index_id && ($post_index_id != $session_index_id)) {
      $this->data['flight_index_id'] = $post_index_id;
    }
    return $this->data['flight_index_id'];
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
  protected function _getFlightCode() {
    $session_code = $this->data['flight_code'];
    $post_code = $this->input->post('flight_code');
    if ($post_code && ($post_code != $session_code)) {
      $this->data['flight_code'] = $post_code;
    }
    return $this->data['flight_code'];
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
    && is_string($post_sort_by) && in_array($post_sort_by, $this->Citybreaks_model->sort_types)) {
      $this->data['sort_by'] = $post_sort_by;
    }
    return $this->data['sort_by'];
  }

  protected function _getSortOrder() {
    $post_sort_order = $this->input->post('sort_order');
    if ($post_sort_order != $this->data['sort_order'] && is_numeric($post_sort_order) 
    && ('' . (int)$post_sort_order === '' . $post_sort_order) 
    && in_array((int)$post_sort_order, $this->Citybreaks_model->sort_orders)) {
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
    $max_stars = $this->Citybreaks_model->max_stars;
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
  public function loadMarkers() {
    if ($this->input->is_ajax_request()) {
      $this->setData();
      ignore_user_abort(false);
      $code = $this->_getCode();
      $this->response = $this->Citybreaks_model->loadMarkers($code);
      $this->output();
    }
  }
  public function loadRoomPackages() {
    if ($this->input->is_ajax_request()) {
      $this->setData();
      ignore_user_abort(false);
      $code = $this->_getCode();
      $hotel_id = $this->_getHotelId();
      $this->response = $this->Citybreaks_model->loadRoomPackages($code,$hotel_id);
      $this->Citybreaks_model->setSearchData($this->data);
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
      $this->response = $this->Citybreaks_model->loadRoomPackage($code,$hotel_id,$package_code);
      $this->output();
    }
  }
}