<?php
if (!defined('BASEPATH'))
  exit('No direct script access allowed');

class Flight extends MX_Controller {
  function __construct() {
    $this->load->model('Trip/Flights_model');
    parent::__construct();
  }
  private $__details = false;
  public function index() {
    $code = $this->input->get('code');
    if(!isset($code)){
      $code = $this->input->post('code');
    }
    $itinerary_code = $this->input->get('itinerary_code');
    if(!isset($itinerary_code)){
      $itinerary_code = $this->input->post('itinerary_code');
    }
    $combinations = $this->input->get('combinations');
    if(!isset($combinations)){
      $combinations = $this->input->post('combinations');
    }
    $flight_choose = $this->input->get('flight_choose');
    if(!isset($flight_choose)){
      $flight_choose = $this->input->post('flight_choose');
    }

    $this->redirect('trip/flight/info?code=' . $code . '&itinerary_code=' . $itinerary_code . '&combinations=' . $combinations . '&flight_choose=' . json_encode($flight_choose));
  }
  public function upsell() {
    $code = $this->input->post_get('code');
    $itinerary_code = $this->input->post_get('itinerary_code');
	$combination_index = $this->input->post_get('combination_index');
	if(isset($combination_index)){
		$itinerary_code .= ":" . $combination_index;
	}
    $this->data = $this->Flights_model->loadFlightUpsell($code, $itinerary_code);
    return $this->outputTripError('');
  }
  public function details() {
	  $this->load->model('TripLog_model');
	$this->TripLog_model->saveLog([
		'date_details' => date('Y-m-d H:i:s'),
		'start_timp_raspuns_item' => microtime(true),
	]);
	
    $code = $this->input->post_get('code');
    $itinerary_code = $this->input->post_get('itinerary_code');
    $combination_index = $this->input->post_get('combination_index');
    $this->data = $this->Flights_model->loadFlightDetails($code, $itinerary_code . ':' . $combination_index);
	
    return $this->outputTripError('');
  }
  public function validate() {
	// $preferredSeats = $this->input->post_get('preferredSeats');
	$service = array(
      'serviceType' => 'flight',
      'resultCode' => $this->input->post_get('code'),
      'itineraryCode' => $this->input->post_get('itinerary_code'),
      'comments' => '',
      'amount' => $this->input->post_get('price'),
      'currency' => $this->input->post_get('currency'),
      'type' => $this->input->post_get('type'),
      'passenger' => $this->input->post_get('passenger'),
      'optionalServices' => $this->input->post_get('optionalServices'),
      'paidSeats' => $this->input->post_get('paidSeats'),
      'upsellCode' => $this->input->post_get('upsellCode'),
    );
	// echo '<pre>';
	// print_r($service);
	// die;
    // $service_data = $this->input->post_get('service_data');
    $this->data = $this->Flights_model->validateFlight($this->input->post_get('code'), $service);
    return $this->outputTripError('');
  }
  public function ancillery() {
    $code = $this->input->post_get('code');
    $itinerary_code = $this->input->post_get('itinerary_code');
    $ancillery_code = $this->input->post_get('ancillery_code');

    $this->data = $this->Flights_model->loadFlightAncillery($code, $itinerary_code, $ancillery_code);
    return $this->outputTripError('');
  }
  public function seats() {
    $code = $this->input->post_get('code');
    $itinerary_code = $this->input->post_get('itinerary_code');
    $ocode = $this->input->post_get('ocode');
    $dcode = $this->input->post_get('dcode');
    $rindex = $this->input->post_get('rindex');
    $req_paid_seat = $this->input->post_get('pseat') ? 'True' : 'False';

    $this->data = $this->Flights_model->loadFlightSeats($code, $itinerary_code, $ocode, $dcode, $rindex, $req_paid_seat);
    return $this->outputTripError('');
  }
  public function info() {
    $_POST['code'] = $this->input->get('code');
    $_POST['itinerary_code'] = $this->input->get('itinerary_code');
    $_POST['flight_choose'] = json_decode($this->input->get('flight_choose'),true);
    $_POST['combinations'] = $this->input->get('combinations');
    $this->__details = true;
    return $this->booking();
  }
  private $is_booking_backend = false;
  public function booking_backend() {
	  $this->Flights_model->dev = true;
	  $this->is_booking_backend = true;
	  // $this->theme->set_layout('frontend/content');
	  $this->theme->set_sublayout('frontend/content/index');
	  return $this->booking();
  }
  public function booking() {
	  if(!$this->is_booking_backend){
    $this->load->model('TripCoupon_model');
	$this->session->set_userdata('trip/checkout/coupons', $this->TripCoupon_model->getValidCoupons($this->session->userdata('trip/checkout/coupons'), 'flight'));
    
    $this->session->set_userdata('customer/trip/flights/order_id', null);
    $this->session->set_userdata('customer/trip/flights/trip_order_id', null);
	  }
    $code = $this->input->get('code');
    if(!isset($code)){
      $code = $this->input->post('code');
    }
    $itinerary_code = $this->input->get('itinerary_code');
    $get = true;
    if(!isset($itinerary_code)){
      $get = false;
      $itinerary_code = $this->input->post('itinerary_code');
    }
    
    $this->data['code'] = trim((string)$code);
    $this->data['flight_code'] = $this->data['code'];
    $this->data['itinerary_code'] = $itinerary_code;
    $combinations = $this->input->get('combinations');
	if(!isset($combinations)){
      $combinations = $this->input->post('combinations');
    }
    if(isset($combinations)){
      $itinerary_code = trim((string)$itinerary_code);
      $combinations = trim((string)$combinations);
      $combinations_arr = explode(',', $combinations);
	  $flight_choose = $this->input->get('flight_choose');
      if(!isset($flight_choose)){
		  $flight_choose = $this->input->post('flight_choose');
      }
      $flight_choose = (array)$flight_choose;
      $combination_arr = array();
      foreach($flight_choose as $type=>$ref){
        $combination_arr[] = $type . $ref;
      }
      $combination = implode('|',$combination_arr);
      $combination_index = array_search($combination,$combinations_arr);
      $this->data['itinerary_code'] = $itinerary_code . ':' . $combination_index;
    }
    
    $this->db->select('*');
    $this->db->where('flight_code', $code);
    $this->db->where('status', 1);
    $q = $this->db->get('trip_offer_popular', 1, 0);
    $row = $q->row();
    $check_res = true;
if(!$this->is_booking_backend){
    $check_first_date = date('Y-m-d H:i:s', strtotime('30 minutes ago'));
    if($this->Flights_model->dev){}
    elseif($row){
      if($check_first_date > $row->time_created){
        $check_res = false;
      }
    } else {
      $this->db->select('*');
      $this->db->where('code', $code);
      $q = $this->db->get('trip_search', 1, 0);
      $row = $q->row();
      if(!$row){
        $code = '';
        $check_res = false;
      } elseif($check_first_date > $row->date){
        $check_res = false;
      }
      if(!$check_res){
        $this->redirect('trip/flights');
      }
    }
}
    $response = false;
    if($check_res){
      $response = $this->Flights_model->loadFlightDetails($this->data['code'], $this->data['itinerary_code']);
      if(!$response){
		  if($this->is_booking_backend){
			  echo 'Cererea de booking a expirat.'; die;
		  }
        if($row){
          $data = unserialize($row->data);
          
          $origin_full_location_name = ($data->departure['location_id'] ? $data->departure['data']['location'] : $data->departure['data']['city']) ;
          $destination_full_location_name = ($data->arrival['location_id'] ? $data->arrival['data']['location'] : $data->arrival['data']['city']) ;
          
          $s_date = $data->flight->Routes[0]->Segment[0]->Origin->Date;
          $e_date = end(end($data->flight->Routes)->Segment)->Destination->Date;
          
          $search_data = $this->Flights_model->getSearchData();
          if($search_data['code'] !== $code){
            $this->redirect('trip/flights?n=1&origin=' . $origin_full_location_name . '&destination=' . $destination_full_location_name . '&sdate=' . $s_date . '&edate=' . $e_date . '&a=1&class=1');
          }
        }
        $this->redirect('trip/flights','Cererea de booking a expirat.','info');
      }
    } else {
      $data = unserialize($row->data);
      
      $origin_full_location_name = ($data->departure['location_id'] ? $data->departure['data']['location'] : $data->departure['data']['city']) ;
      $destination_full_location_name = ($data->arrival['location_id'] ? $data->arrival['data']['location'] : $data->arrival['data']['city']) ;
      
      $s_date = $data->flight->Routes[0]->Segment[0]->Origin->Date;
      $e_date = end(end($data->flight->Routes)->Segment)->Destination->Date;
      
      $search_data = $this->Flights_model->getSearchData();
      if($search_data['code'] !== $code){
        $this->redirect('trip/flights?n=1&origin=' . $origin_full_location_name . '&destination=' . $destination_full_location_name . '&sdate=' . $s_date . '&edate=' . $e_date . '&a=1&class=1');
      }
    }

    $this->data['flight_details'] = &$response;
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
    $flight = &$this->data['flight_details'];
    $companii_marketing = array();
    if($flight){
      foreach($flight->Routes as $rk => &$subroute){
        foreach($subroute->Segment as $sk => $segment){
          $companie_marketing = $segment->Carrier->Marketing;
          $companii_marketing[$companie_marketing->Code] = array('code'=>$companie_marketing->Code,'name'=>$companie_marketing->_, 'img'=>$placeholder_companie);
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
        if(file_exists($this->theme->theme_path . 'assets/images/' . $company_from_db->image)){
          $companii_marketing[$company_from_db->code]['img'] = $this->theme->theme_url . 'assets/images/' . $company_from_db->image;
        }
      }
    }
    if($flight){
      $flight->companies = array_values($companii_marketing);
      $flight->companies_indexes = array_combine(array_keys($companii_marketing), array_keys($flight->companies));
    }
    if($this->is_booking_backend){
      $this->theme->view('trip/flight/booking_backend', $this->data, $this);
    } elseif($this->__details){
      $this->theme->view('trip/flight/details', $this->data, $this);
    } else {
      $this->theme->view('trip/flight/booking', $this->data, $this);
    }
  }
  public function checkout() {
    $this->load->model('TripCoupon_model');
	$this->session->set_userdata('trip/checkout/coupons', $this->TripCoupon_model->getValidCoupons($this->session->userdata('trip/checkout/coupons'), 'flight'));
    
    $this->makeResponseGlobal();
    if ($this->input->is_ajax_request()) {
      $this->load->library('form_validation');
      $response = modules :: run('Trip/checkout/Checkout/validate', 'flight');
      if(!$response){
        $this->output('error');
      }
      if (false === $this->form_validation->run()) {
        $this->data['errors'] = $this->form_validation->error_array();
        $this->outputError($this->form_validation->error_string());
      }
      $response = modules :: run('Trip/checkout/Checkout/service', 'flight', false);
      if(!$response){
        $this->output('error');
      }
      $this->addMessage('Serviciul a fost validat.');
      return $this->output();
    }
    $status = modules :: run('Trip/checkout/Checkout/service', 'flight', true);
    if(false === $status){
      $this->saveMessagesInSession();
      $this->redirect('trip/checkout/failure');
    }
    if(true === $status){
      $this->redirect('trip/checkout/success');
    }
    // $this->outputError('Blocat intentionat pentru adaugare de functionalitate');
  }
}