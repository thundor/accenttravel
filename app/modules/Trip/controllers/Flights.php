<?php
if (!defined('BASEPATH'))
  exit('No direct script access allowed');

class Flights extends MX_Controller {

  function __construct() {
    $this->load->model('Trip/Flights_model');
    parent::__construct();
  }
  
  public function index() {
    $this->theme->view('trip/flights/index', $this->data, $this);
  }
  public function offers() {
    $airline = $this->input->get('airline');
    if(!$airline){
      $airline = $this->uri->segment(3);
    }
    $_GET['airline'] = $airline;
    $this->data['airline'] = $airline;
    $this->data['offers'] = true;
    $this->theme->view('trip/flights/offers', $this->data, $this);
  }
  public function search() {
    $this->theme->set_sublayout('frontend/waiting/index');
    $this->theme->view('trip/flights/search', $this->data, $this);
  }
  
  public function loadLocations() {
    if ($this->input->is_ajax_request()) {
      ignore_user_abort(false);
	  $lang = $this->input->get('lang');
      $q = trim(URLify::downcode('' . $this->input->get('q'), 'en'));
      $this->response = $this->Flights_model->loadLocations($q, $lang ? $lang : 'en');
      if(!$this->response){
        $call_response = $this->Trip_model->api->call->result_decoded;
        $this->data['calls'] = &$this->Trip_model->api->calls;
        $this->data['call'] = &$this->Trip_model->api->call;
        if($call_response){
          $this->outputError('Trip Error: (Cod ' . $call_response->status . ') ' . $call_response->title . ': ' . $call_response->detail);
        } else {
          $this->outputError('Trip Error: Nu s-au putut prelua locatiile pentru cautarea ' . $q);
        }
      }
      $this->output();
    }
  }
  
  public function setSearch($return = false) {
    if ($this->input->is_ajax_request()) {
      $data = $this->Flights_model->getSearchDefaultData();
	  $default_search_data = $data;
	  
      $departure_date = $this->input->post('departure_date');
      $return_date = $this->input->post('return_date');

      $date_format = 'Y-m-d';
      $d = DateTime::createFromFormat($date_format, $departure_date);
      if ($d && $d->format($date_format) == $departure_date) {
        $data['departure_date'] = $departure_date;
      }
      $d = DateTime::createFromFormat($date_format, $return_date);
      if ($d && $d->format($date_format) == $return_date) {
        $data['return_date'] = $return_date;
      }

      $go_only = $this->input->post('go_only');
	  if(!isset($go_only)){
		  $type = $this->input->post('type');
		  if(isset($type)){
			  $go_only = !$type;
		  }
	  }
      $data['go_only'] = filter_var($go_only, FILTER_VALIDATE_BOOLEAN);
      
      if (!$data['go_only'] && $data['return_date'] < $data['departure_date']) {
        $departure_date = $data['departure_date'];
        $data['departure_date'] = $data['return_date'];
        $data['return_date'] = $departure_date;
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
      
      
      // $company_type = (int) ($this->input->post('company_type'));
      // if ($company_type>=0) {
        // $data['company_type'] = $company_type;
      // }
      
      $cabine_type = (int) ($this->input->post('cabine_type'));
      if ($cabine_type>=0) {
        $data['cabine_type'] = $cabine_type;
      }
      
      $direct_only = $this->input->post('direct_only');
      $data['direct_only'] = filter_var($direct_only, FILTER_VALIDATE_BOOLEAN);
      
      $flexible_dates = $this->input->post('flexible_dates');
      $data['flexible_dates'] = filter_var($flexible_dates, FILTER_VALIDATE_BOOLEAN);
      
      $flex_dates = $this->input->post('flex_dates');
      $data['flex_dates'] = isset($flex_dates) ? filter_var($flex_dates, FILTER_VALIDATE_BOOLEAN) : true;
      
      $passengers_adult = (int) ($this->input->post('passengers_adult'));
      if ($passengers_adult>=0) {
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
      
      $varste_copii = (array)($this->input->post('varste_copii'));
      if ($varste_copii) {
        $data['varste_copii'] = $varste_copii;
      }
	  
	  $this->session->set_userdata('log/trip/flights/search_data', array_merge($data, ['hash_date' => date('Y-m-d H:i:s')]));
      
      $ignore_session = filter_var($this->input->post('ignore_session'), FILTER_VALIDATE_BOOLEAN);
      $data['ignore_session'] = !empty($ignore_session);
      
      $session = $this->input->post('session');
      $data['session'] = trim($session);
      // echo '<pre>';
	  // print_r($data);
	  // die;
      $this->data = $data;
      if ($return) {
        return;
      }
      $this->Flights_model->setSearchData($this->data);
      $this->output();
    }
  }
  
  public function setSearchAndInitiate() {
    if ($this->input->is_ajax_request()) {
      $this->setSearch(true);
      $this->initiate();
    }
  }

  public function initiate() {
    if ($this->input->is_ajax_request()) {
		$this->load->model('TripLog_model');
		$this->TripLog_model->saveLog([
			'date_results' => date('Y-m-d H:i:s'),
			'start_timp_raspuns_cautare' => microtime(true),
		]);
		
		
      ignore_user_abort(false);
      static $maxretries = 30;
      if ($maxretries <= 0) {
        $this->outputError('TRIP error: too many retries initiating.');
      }
      if ($maxretries < 30) {
        sleep(2);
      }
      $maxretries --;
      $data = &$this->data;
      $this->response = $this->Flights_model->initiateSearch($this->data);
      if(!$this->response || (isset($this->response->Status)) && !$this->response->Status){
        $call_response = $this->Trip_model->api->call->result_decoded;
        $this->data['calls'] = &$this->Trip_model->api->calls;
        $this->data['call'] = &$this->Trip_model->api->call;
        if($call_response){
          $this->outputError('Trip Error: (Cod ' . $call_response->status . ') ' . $call_response->title . ': ' . $call_response->detail);
        } else {
          $this->outputError('Trip Error: search initiation returned no response');
        }
      }
      $container_id = $data['container_id'];
      if (empty($this->response->{$container_id})) {
        $this->addMessage('TRIP error: container not found, reinitating.', 'error');
        return $this->initiate();
      }
      
      $search_response = array_pop($this->response->{$container_id});
      $this->data['index_id'] = $_POST['index_id'] = $search_response->Id;
      $index_id = $this->_getIndex();
      $this->Flights_model->setSearchData($this->data);
      return $this->inspectSearch();
    }
  }
  
  protected function inspectSearch() {
    if ($this->input->is_ajax_request()) {
      static $maxretries = 100;
      if ($maxretries <= 0) {
        $this->outputError('TRIP error: too many retries initiating.');
      }
      if ($maxretries < 100) {
        sleep(2);
      }
      $maxretries --;
      $data = &$this->data;
      $container_id = $data['container_id'];
      $index_id = $data['index_id'];
      $this->response = $this->Flights_model->inspectSearch($container_id);
      if(!$this->response){
        $call_response = $this->Trip_model->api->call->result_decoded;
        $this->data['calls'] = &$this->Trip_model->api->calls;
        $this->data['call'] = &$this->Trip_model->api->call;
        if($call_response){
          $this->outputError('Trip Error: (Cod ' . $call_response->status . ') ' . $call_response->title . ': ' . $call_response->detail);
        } else {
          $this->outputError('Trip Error: search inspection returned no response');
        }
      }
      $status = 2;
      foreach($this->response as $k=>$search){
        if($search->Id === $index_id){
          $status = $search->Status;
          break;
        }
        continue;
      }
      if($status == 2){
        $this->addMessage('TRIP retry: search is in progress, rechecking.', 'info');
        return $this->inspectSearch();
      }
      if($status == 1){
        return $this->inspectSearchIndex();
      }
      if($status == 0){
        return $this->outputError('The search failed.');
      }
      $this->output();
    }
  }
  protected function inspectSearchIndex() {
    static $maxretries = 100;
    if ($maxretries <= 0) {
      $this->outputError('TRIP error: too many retries initiating.');
    }
    if ($maxretries < 100) {
      sleep(2);
    }
    $maxretries --;
    $data = &$this->data;
    $index_id = $data['index_id'];
    $this->response = $this->Flights_model->inspectSearchIndex($index_id);
    if(!$this->response){
      $call_response = $this->Trip_model->api->call->result_decoded;
      $this->data['calls'] = &$this->Trip_model->api->calls;
      $this->data['call'] = &$this->Trip_model->api->call;
      if($call_response){
        $this->outputError('Trip Error: (Cod ' . $call_response->status . ') ' . $call_response->title . ': ' . $call_response->detail);
      } else {
        $this->outputError('Trip Error: search index inspect returned no response');
      }
    }
    if (empty($this->response->code)) {
      $this->addMessage('TRIP error: code parameter missing in search index result, retrying.', 'error');
      return $this->inspectSearchIndex();
    }
    $this->data['code'] = $_POST['code'] = $this->response->code;
    $this->Flights_model->setSearchData($this->data);
    return $this->loadResults();
  }
  public function loadOffers() {
    if ($this->input->is_ajax_request()) {
      ignore_user_abort(false);
      
      $this->load->model('Trip/Offer_popular_model');
      $offers = $this->Offer_popular_model->getOffers();
      
      $airline = $this->input->get('airline');
      
      $this->response = new stdClass;
      $this->response->total_items = 0;
      $this->data = array();
      $this->response->_embedded = new stdClass();
      $this->response->_embedded->flights = array();
      $this->data['offer'] = 1;
      $this->data['flex_dates'] = false;
      $this->data['flexible_dates'] = false;
      $this->data['passengers_adult'] = 1;
      $this->data['passengers_senior'] = 0;
      $this->data['passengers_youth'] = 0;
      $this->data['passengers_child'] = 0;
      $this->data['passengers_infant_lap'] = 0;
      $this->data['passengers_infant_seat'] = 0;
      $filter_by_airline = false;
      if(isset($airline)){
        $airline = trim($airline);
        if(strlen($airline) > 0){
          $filter_by_airline = true;
        }
      }
      $first_found = false;
      foreach($offers as $k=>$offer){
        if($filter_by_airline && $airline != $offer->code){
          continue;
        }
        $this->response->total_items++;
        $offer_data = unserialize($offer->data);
        $flight = $offer_data->flight;
        $routes_count = count($flight->Routes);
      
        $departure_city = trim($offer_data->departure['data']['city_text']);
        $departure_segment = $flight->Routes[0]->Segment[0];
        if(strlen($departure_city)){
          $departure_segment->Origin->Airport->City = $departure_city;
        }
        $departure_date = new DateTime($departure_segment->Origin->Date . ' ' . $departure_segment->Origin->Time);
        $departure_airport = '';
        if($offer_data->departure['location_id']){
          $departure_airport = trim($offer_data->departure['data']['location_text']);
        }
        if(strlen($departure_airport)){
          $departure_segment->Origin->Airport->_ = $departure_airport;
        }
        if(!$first_found){
          $this->data['code'] = $offer->flight_code;
          $this->data['departure_date'] = $departure_date->format('Y-m-d');
          $this->data['go_only'] = $routes_count == 1;
        }
        $total_arrival_segments = count($flight->Routes[0]->Segment);
        $arrival_segment = $flight->Routes[0]->Segment[$total_arrival_segments-1];
        $arrival_city = trim($offer_data->arrival['data']['city_text']);
        if(strlen($arrival_city)){
          $arrival_segment->Destination->Airport->City = $arrival_city;
        }
        $arrival_date = new DateTime($arrival_segment->Destination->Date . ' ' . $arrival_segment->Destination->Time);
        $arrival_airport = '';
        if($offer_data->arrival['location_id']){
          $arrival_airport = trim($offer_data->arrival['data']['location_text']);
        }
        if(strlen($arrival_airport)){
          $arrival_segment->Destination->Airport->_ = $arrival_airport;
        }
        
        if(!$first_found && $filter_by_airline){
          $this->data['origin_city_name'] = $departure_segment->Origin->Airport->City;
          $this->data['origin_location_name'] = $departure_segment->Origin->Airport->_;
          $country_name = $offer_data->departure['data']['country'];
          if(strlen(trim($offer_data->departure['data']['country_text']))){
            $country_name = trim($offer_data->departure['data']['country_text']);
          }
          $this->data['origin_country_name'] = $country_name;
        }
        
        if(!$first_found && $filter_by_airline){
          $this->data['destination_city_name'] = $arrival_segment->Destination->Airport->City;
          $this->data['destination_location_name'] = $arrival_segment->Destination->Airport->_;
          $country_name = $offer_data->arrival['data']['country'];
          if(strlen(trim($offer_data->arrival['data']['country_text']))){
            $country_name = trim($offer_data->arrival['data']['country_text']);
          }
          $this->data['destination_country_name'] = $country_name;
        }
        if($routes_count > 1){
          $departure_city = trim($offer_data->arrival['data']['city_text']);
          $departure_segment = $flight->Routes[1]->Segment[0];
          if(strlen($departure_city)){
            $departure_segment->Origin->Airport->City = $departure_city;
          }
          $departure_date = new DateTime($departure_segment->Origin->Date . ' ' . $departure_segment->Origin->Time);
          $departure_airport = '';
          if($offer_data->arrival['location_id']){
            $departure_airport = trim($offer_data->arrival['data']['location_text']);
          }
          if(strlen($departure_airport)){
            $departure_segment->Origin->Airport->_ = $departure_airport;
          }
          $total_arrival_segments = count($flight->Routes[1]->Segment);
          $arrival_segment = $flight->Routes[1]->Segment[$total_arrival_segments-1];
          $arrival_city = trim($offer_data->departure['data']['city_text']);
          if(strlen($arrival_city)){
            $arrival_segment->Destination->Airport->City = $arrival_city;
          }
          $arrival_date = new DateTime($arrival_segment->Destination->Date . ' ' . $arrival_segment->Destination->Time);
          $arrival_airport = '';
          if($offer_data->departure['location_id']){
            $arrival_airport = trim($offer_data->departure['data']['location_text']);
          }
          if(strlen($arrival_airport)){
            $arrival_segment->Destination->Airport->_ = $arrival_airport;
          }
          if(!$first_found){
            $this->data['return_date'] = $departure_date->format('Y-m-d');
          }
        }
        $first_found = true;
        $routes = $flight->Routes;
        $flight->Routes = array();
        foreach($routes as $route){
          $flight_route = new stdClass;
          $flight_route->Route = array(
            $route
          );
          $flight->Routes[] = $flight_route;
        }
        $this->response->_embedded->flights[] = $flight;
      }
      $this->interpretResults();
      $this->output();
    }
  }
  public function loadResults() {
    if ($this->input->is_ajax_request()) {
		
	$this->load->model('TripLog_model');
	$this->TripLog_model->saveLog([
		'results_count' => 0,
		'start_timp_raspuns_rezultate' => microtime(true),
	]);
	
      ignore_user_abort(false);
      $results_json = APPPATH . 'modules/Trip/flights_response.json';
      // $response = json_decode(file_get_contents($results_json));
      if(!$this->data){
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
        $this->data = $this->Flights_model->getSearchData($session);
      }
      $code = $this->_getCode();
      $page = $this->_getPage();
      // if($response && $response->code === $code){
        // $this->response = &$response;
      // } else {
        // file_put_contents($results_json, json_encode($this->response, JSON_PRETTY_PRINT));
      // }
      $this->response = $this->Flights_model->loadFlights($code, $page);
      if(!$this->response){
        $call_response = $this->Trip_model->api->call->result_decoded;
        $this->data['calls'] = &$this->Trip_model->api->calls;
        $this->data['call'] = &$this->Trip_model->api->call;
        if($call_response){
          $this->outputError('Trip Error: (Cod ' . $call_response->status . ') ' . $call_response->title . ': ' . $call_response->detail);
        } else {
          $this->outputError('Trip Error: search results returned no response');
        }
      }
      if(empty($this->response->code)){
        $this->data['flights_expired'] = true;
        $this->outputError('No flights found');
      }
      $this->interpretResults();
      $this->Flights_model->setSearchData($this->data);
      $this->output();
    }
  }
  
  protected function interpretResults() {
	$this->load->model('TripLog_model');
	$this->TripLog_model->saveLog([
		'results_count' => array_reduce($this->response->_embedded->flights, function($carry, $flight){
			return $carry + count($flight->Combinations);
		},0),
	]);
    $flights = &$this->response->_embedded->flights;
    $min_price = null;
    $max_price = null;
    $escale = array();
    $escale_tur = array();
    $escale_retur = array();
    $companii_marketing = array();
    $classes = array();
    $cabin_types = array();
    
    $this->load->library('image_lib');
    
    $config['image_library'] = 'gd2';
    $config['width'] = 25;
    $config['height'] = 25;
    $config['master_dim'] = 'height';
    
    $theme_path = $this->theme->config('path');
    $theme_name = $this->theme->config('theme');
    
    /* $original_filename = 'placeholder_companie';
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
    
    foreach($flights as &$flight){
      if(is_null($min_price) || $flight->PriceDetail->Amount < $min_price){
        $min_price = $flight->PriceDetail->Amount;
      }
      if(is_null($max_price) || $flight->PriceDetail->Amount > $max_price){
        $max_price = $flight->PriceDetail->Amount;
      }
      // print_r($flight);
      // die;
      foreach($flight->Routes as $rk => &$route){
        foreach($route->Route as $srk => &$subroute){
          $subroute->escale = count($subroute->Segment) -1;
          $escale[$subroute->escale] = $subroute->escale;
          if($rk == 0) $escale_tur[$subroute->escale] = $subroute->escale;
          if($rk == 1) $escale_retur[$subroute->escale] = $subroute->escale;
          foreach($subroute->Segment as $sk => $segment){
            $companie_marketing = $segment->Carrier->Marketing;
            $companii_marketing[$companie_marketing->Code] = array('code'=>$companie_marketing->Code,'name'=>$companie_marketing->_, 'img'=>$placeholder_companie);
            $classes[$segment->Flight->Class] = $segment->Flight->Class;
            $cabin_types[$segment->Flight->CabinType] = $segment->Flight->CabinType;
          }
        }
      }
    }
    $companies_from_db = array();
    if($companii_marketing){
      $company_codes = array_keys($companii_marketing);
      $this->load->model('Trip/Flights_airlines_model');
      $companies_from_db = $this->Flights_airlines_model->getAirlines(array('code' => $company_codes));
    }
    foreach($companies_from_db as $k=>$company_from_db){
		$companii_marketing[$company_from_db->code]['e'] = 1;
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
	$companies_not_from_db = array_filter(array_values($companii_marketing), function($v){ return empty($v['e']); });
	if(!empty($companies_not_from_db)){
		foreach($companies_not_from_db as $company_not_from_db){
			try{
				$this->Flights_airlines_model->addAirline(['code' => $company_not_from_db['code'], 'name' => $company_not_from_db['name'], 'original_name' => $company_not_from_db['name']]);
			} catch(Exception $e){
				// Do nothing
			}
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
  
  protected $response = null;
  protected $results = array();

  protected function output($status = 'success') {
	$this->load->model('TripLog_model');
	$error_message = null;
	$error_call = null;
	if($status != 'success'){
		$error_message = json_encode($this->message);
		$error_call = json_encode($this->Flights_model->api->call);
	}
	$this->TripLog_model->saveLog(['error_message' => $error_message, $error_call => $error_call]);
	
    $response = array(
      'status' => $status,
      'response' => $this->response,
      'calls' => $this->Flights_model->api->calls,
      'results' => $this->results,
      'message' => $this->message,
      'messages' => $this->messages,
      'data' => $this->data,
    );

    echo json_encode($response);
    die;
  }

  protected function _getIndex() {
    $session_index_id = $this->data['index_id'];
    $post_index_id = $this->input->post('index_id');
    if ($post_index_id && ($post_index_id != $session_index_id)) {
      $this->data['index_id'] = $post_index_id;
    }
    return $this->data['index_id'];
  }
  protected function _getCode() {
    $session_code = $this->data['code'];
    $post_code = $this->input->post('code');
    if ($post_code && ($post_code != $session_code)) {
      $this->data['code'] = $post_code;
    }
    return $this->data['code'];
  }
  protected function _getPage() {
    $page = $this->input->post('page');
    if (!is_numeric($page) || '' . (int) $page !== '' . $page || $page < 1) {
      return 1;
    }
    return $page;
  }
}