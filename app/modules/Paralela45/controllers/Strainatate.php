<?php
if (!defined('BASEPATH'))
  exit('No direct script access allowed');

class Strainatate extends MX_Controller {
  function __construct() {
    $this->load->model('Paralela45_model');
    $this->load->model('Paralela45/Paralela45_Strainatate_model');
    parent::__construct();
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
    $this->data = $this->Paralela45_Strainatate_model->getSearchData($package_id, $session);
  }
  public function index() {
    $this->setData();
    $this->theme->view('paralela45/strainatate/index', $this->data, $this);
  }
  public function hotel() {
    $id = $this->input->get('id');
    if(!$id){
      $id = $this->uri->segment(3);
    }
    $id_arr = explode('_', $id);
    $search_data = array();
    $search_data['ProductType'] = __FUNCTION__;
    $search_data['TourOpCode'] = isset($id_arr[0]) && strlen(trim($id_arr[0])) ? trim($id_arr[0]) : null;
    $search_data['CountryCode'] = isset($id_arr[1]) && strlen(trim($id_arr[1])) ? trim($id_arr[1]) : null;
    $search_data['CityCode'] = isset($id_arr[2]) && strlen(trim($id_arr[2])) ? trim($id_arr[2]) : null;
    $search_data['ProductCode'] = isset($id_arr[3]) && strlen(trim($id_arr[3])) ? trim($id_arr[3]) : null;
    
    cleanArray($search_data);
    if(count($search_data) < 5){
      return $this->theme->view('paralela45/strainatate/hotel/404', $this->data, $this);
    }
    $this->response = $this->Paralela45_model->getProductInfoRequest($search_data);
    if (!$this->response) {
      return $this->theme->view('paralela45/strainatate/hotel/404', $this->data, $this);
      // $this->redirect('paralela45/strainatate', 'Vacanta nu a fost gasita','error');
    }
    if(empty($this->response->getProductInfoResponse)){
      return $this->theme->view('paralela45/strainatate/hotel/404', $this->data, $this);
      // $this->redirect('paralela45/strainatate', 'Vacanta invalida','error');
    }
    if(!empty($this->response->getProductInfoResponse->Error)){
      return $this->theme->view('paralela45/strainatate/hotel/404', $this->data, $this);
      // $this->redirect('paralela45/strainatate', 'Vacanta invalida: ' . $this->response->getProductInfoResponse->Error->ErrorId . ' ' . $this->response->getProductInfoResponse->Error->ErrorText,'error');
    }
    $product = $this->response->getProductInfoResponse->Product;
    $this->data['product'] = &$product;
    $this->data['hotel_code'] = $id;
    return $this->theme->view('paralela45/strainatate/hotel', $this->data, $this);
  }
  
  public function setSearch($return = false) {
    if ($this->input->is_ajax_request()) {
      $this->setData();
      
      $data = $this->Paralela45_Strainatate_model->getSearchDefaultData();
      $start_date = $this->input->post('start_date');
      if (strlen($start_date)) {
        $d = DateTime::createFromFormat('Y-m-d', $start_date);
        if($d && $d->format('Y-m-d') == $start_date){
          $data['start_date'] = $start_date;
        }
      }
      $origin = ($this->input->post('origin'));
      if (strlen($origin)) {
        $data['origin'] = $origin;
      }
      $destination = ($this->input->post('destination'));
      if (strlen($destination)) {
        $data['destination'] = $destination;
      } else {
        $zone = ($this->input->post('zone'));
        if (strlen($zone)) {
          $data['zone'] = $zone;
        }
      }
      $hotel_name = trim($this->input->post('hotel_name'));
      if (strlen($hotel_name)) {
        $data['hotel_name'] = $hotel_name;
      }
      $nights = $this->input->post('nights');
      if (isset($nights) && $nights>=0) {
        $data['nights'] = (int)$nights;
      }

      $occupancy = $this->input->post('occupancy');
      if (is_array($occupancy) && !empty($occupancy)) {
        $rooms = array();
        $expected_room_index = 0;
        foreach ($occupancy as $room_index => $occupants) {
          if ($room_index != $expected_room_index) {
            break;
          }
          if ($expected_room_index + 1 > $this->Paralela45_Strainatate_model->max_rooms) {
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
          if ($adults <= 0 || $adults > $this->Paralela45_Strainatate_model->max_adults_per_room) {
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
            if ($expected_child_index + 1 > $this->Paralela45_Strainatate_model->max_children_per_room) {
              break;
            }
            $expected_child_index++;
            if (!is_numeric($child_age)) {
              break;
            }
            if ((int) $child_age . '' !== $child_age . '') {
              break;
            }
            if ($child_age < 1 || $child_age > $this->Paralela45_Strainatate_model->max_child_age) {
              break;
            }
            $child_age = (int) $child_age;

            $room_children_ages[] = $child_age-1;
          }
          if ($room_children_ages) {
            $room['chd'] = $room_children_ages;
          }
          if(isset($occupants['birth_date']) && is_array($occupants['birth_date'])){
            $room['birth_date'] = $occupants['birth_date'];
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
      $this->Paralela45_Strainatate_model->setSearchData($this->data);
      if ($return) {
        return;
      }
      $this->output();
    }
  }
  public function loadResults($offer_id = null) {
    if (!$this->input->is_ajax_request()) {
      $this->redirect('');
    }
    $this->setData();
    $search_data = array(
      'departure_city_code' => $this->data['origin'],
      'destination_city_code' => $this->data['destination'],
      'destination_zone_code' => $this->data['zone'],
      'checkin' => $this->data['start_date'],
      'nights' => $this->data['nights'],
      'hotel_name' => $this->data['hotel_name'],
      'occupancy' => array(),
    );
    
    foreach($this->data['occupancy'] as $room_occupancy){
      if(!is_array($room_occupancy)){
        continue;
      }
      if(!isset($room_occupancy['adt'])){
        continue;
      }
      $room = array();
      $room['adt'] = intval($room_occupancy['adt']);
      if(isset($room_occupancy['chd'])){
        $room['chd'] = array();
        foreach($room_occupancy['chd'] as $child_age){
          $room['chd'][] = $child_age;
        }
      }
      $search_data['occupancy'][] = $room;
    }
    
    $this->results['results'] = array();
    $this->results['products'] = array();
    $this->results['offers'] = array();
    $routes = $this->Paralela45_model->getPackageNVRoutes();
    try{
      $request_search_data = $this->Paralela45_Strainatate_model->getPackageNVPriceRequestSearchData($routes, $search_data);
      $results =  $this->Paralela45_Strainatate_model->getPackageNVPriceRequest($request_search_data);
      // $this->results['search_data'] = $search_data;
      // $this->results['request_search_data'] = $request_search_data;
      $this->results['results'] = $results;
      $interpretted_results = $this->Paralela45_Strainatate_model->interpretPackageNVPriceRequest($request_search_data,$results, $offer_id);
    } catch(Exception $e){
      $this->outputError($e->getMessage());
    }
    if($interpretted_results){
      $this->results['products'] = $interpretted_results['products'];
      $this->results['offers'] = $interpretted_results['offers'];
    }
    $this->Paralela45_Strainatate_model->setSearchData($this->data);
    $this->output();
  }
  
  protected $response = null;
  protected $results = array();

  protected function output($status = 'success') {
    $response = array(
      'status' => $status,
      'response' => $this->response,
      'requests' => $this->Paralela45_model->getApi()->requests,
      'results' => $this->results,
      'message' => $this->message,
      'messages' => $this->messages,
      'data' => $this->data,
    );

    echo json_encode($response);
    die;
  }
  public function booking() {
	  // error_reporting(-1);
	  // ini_set('display_errors', 1);
	$this->load->model('TripCoupon_model');
	$this->session->set_userdata('trip/checkout/coupons', $this->TripCoupon_model->getValidCoupons($this->session->userdata('trip/checkout/coupons'), 'paralela45_strainatate'));
	
    $is_ajax_request = $this->input->is_ajax_request();
    $offer = $this->input->post('offer');
    if(!is_array($offer)){
      $offer = array();
    }
    if(empty($offer)){
      $this->redirect('paralela45/strainatate', 'Invalid offer details','error');
    }
    
    if($is_ajax_request){
      $offer_id = trim(isset($offer['product_code']) ? $offer['product_code'] : null);
    } else {
      $offer_id = $this->input->get('id');
      if(!$offer_id){
        $offer_id = $this->uri->segment(3);
      }
    }
    $id_arr = explode('_', $offer_id);
    $service_info = array();
    $service_info['type'] = 'strainatate';
    $service_info['offer_id'] = $offer_id;
    $service_info['first'] = !empty($offer['first']) ? 1 : null;
    $service_info['package_id'] = trim(isset($offer['package_id']) ? $offer['package_id'] : null);
    $service_info['package_variant_id'] = trim(isset($offer['package_variant_id']) ? $offer['package_variant_id'] : null);
    $service_info['departure_city_code'] = trim(isset($offer['origin']) && strlen($offer['origin']) ? $offer['origin'] : null);
    $service_info['checkin'] = trim(isset($offer['checkin']) ? $offer['checkin'] : null);
    $service_info['checkout'] = trim(isset($offer['checkout']) ? $offer['checkout'] : null);
    $service_info['tour_op_code'] = isset($id_arr[0]) && strlen(trim($id_arr[0])) ? trim($id_arr[0]) : null;
    $service_info['destination_city_code'] = isset($id_arr[2]) && strlen(trim($id_arr[2])) ? trim($id_arr[2]) : null;
    $service_info['product_code'] = isset($id_arr[3]) && strlen(trim($id_arr[3])) ? trim($id_arr[3]) : null;
    if($is_ajax_request){
      $rooms_occupancy = isset($offer['occupancy']) ? $offer['occupancy'] : array();
    } else {
      $occupancy = isset($offer['occupancy']) ? json_decode($offer['occupancy'], true) : array();
      $rooms_occupancy = array();
      if (is_array($occupancy) && !empty($occupancy)) {
        $expected_room_index = 0;
        foreach ($occupancy as $room_index => $occupants) {
          if ($room_index != $expected_room_index) {
            break;
          }
          if ($expected_room_index + 1 > $this->Paralela45_Strainatate_model->max_rooms) {
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
          if ($adults <= 0 || $adults > $this->Paralela45_Strainatate_model->max_adults_per_room) {
            break;
          }
          $room = array();
          $room['adt'] = $adults;
          $room_children_ages = array();
          $children = isset($occupants['chd']) && is_array($occupants['chd']) ? $occupants['chd'] : array();
          $expected_child_index = 0;
          foreach ($children as $child_index => $child_age) {
            if ($child_index != $expected_child_index) {
              break;
            }
            if ($expected_child_index + 1 > $this->Paralela45_Strainatate_model->max_children_per_room) {
              break;
            }
            $expected_child_index++;
            if (!is_numeric($child_age)) {
              break;
            }
            if ((int) $child_age . '' !== $child_age . '') {
              break;
            }
            if ($child_age < 1 || $child_age > $this->Paralela45_Strainatate_model->max_child_age) {
              break;
            }
            $child_age = (int) $child_age;

            $room_children_ages[] = $child_age;
          }
          if ($room_children_ages) {
            $room['chd'] = $room_children_ages;
          }
          $rooms_occupancy[] = $room;
        }
      }
    }
    $service_info['occupancy'] = $rooms_occupancy;
    try{
      $this->Paralela45_Strainatate_model->getBookingService($service_info);
      $this->data = $service_info;
    } catch(Exception $e){
      $this->redirectOrOutput('paralela45/strainatate', $e->getMessage(),'error');
    }
    if($is_ajax_request){
      $this->output();
    }
    $this->theme->view('paralela45/strainatate/booking', $this->data, $this);
  }
  public function checkout() {
	$this->load->model('TripCoupon_model');
	$this->session->set_userdata('trip/checkout/coupons', $this->TripCoupon_model->getValidCoupons($this->session->userdata('trip/checkout/coupons'), 'paralela45_strainatate'));
	
    $is_ajax_request = $this->input->is_ajax_request();
    $this->makeResponseGlobal();
    $this->load->library('form_validation');
    $response = modules :: run('Trip/checkout/Checkout/validate', 'paralela45_strainatate');
    if(!$response){
      if($is_ajax_request){
        $this->output('error');
      }
      $this->redirect('trip/checkout/failure');
    }
    if (false === $this->form_validation->run()) {
      if ($is_ajax_request) {
        $this->data['errors'] = $this->form_validation->error_array();
        $this->outputError($this->form_validation->error_string());
      }
      $this->redirect('trip/checkout/failure');
    }
    if($is_ajax_request){
      $response = modules :: run('Trip/checkout/Checkout/service', 'paralela45_strainatate', false);
      if(!$response){
        $this->output('error');
      }
      $this->addMessage('Serviciul a fost validat.');
      return $this->output();
    }

    $status = modules :: run('Trip/checkout/Checkout/service', 'paralela45_strainatate', true);
    if(false === $status){
      $this->saveMessagesInSession();
      $this->redirect('trip/checkout/failure');
    }
    if(true === $status){
      $this->redirect('trip/checkout/success');
    }
    // $this->outputError('Blocat intentionat pentru adaugare de functionalitate');
  }
  protected function redirectOrOutput($route, $message=null, $type='info') {
    $is_ajax_request = $this->input->is_ajax_request();
    if($is_ajax_request){
      if(isset($message)){
        $this->addMessage($message);
      }
      $this->output($type);
    }
    $this->redirect($route,$message,$type);
  }
  // protected function redirect($route, $message=null, $type='info') {
    // echo 'Should redirect to: ' . $route . ' ' . strtoupper($type) . ': ' . $message;
    // die;
  // }
}