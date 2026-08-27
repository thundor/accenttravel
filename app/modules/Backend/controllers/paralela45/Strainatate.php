<?php
if (!defined('BASEPATH'))
  exit('No direct script access allowed');

class Strainatate extends MX_Controller {
  function __construct() {
    $this->load->model('Paralela45_model');
    $this->load->model('Paralela45/Paralela45_Strainatate_model');
    parent::__construct();
  }
  public function setSearch($return = false) {
    if ($this->input->is_ajax_request()) {
      if(!$this->user->can('backend-access')){
        $this->outputError('Acces restrictionat');
      }
      if(!$this->user->canAny('backend-trip-orders-access','backend-trip-orders-own-access')){
        $this->outputError('Acces restrictionat');
      }
      $hotel_id = (int)$this->input->post('hotel_id');
      $this->data = $this->Paralela45_Strainatate_model->getSearchData($hotel_id);
      
      $this->load->library('form_validation');
      $this->form_validation->set_rules('start_date', 'Checkin', 'trim|required|max_length[10]|valid_date[Y-m-d]',array(
        'valid_date' => 'Formatul datei este invalid',
      ));
      $this->form_validation->set_rules('hotel_name', 'Nuem Hotel', 'trim|max_length[255]');
      $this->form_validation->set_rules('origin', 'Oras plecare', 'trim|required|max_length[255]');
      $this->form_validation->set_rules('destination', 'Oras destinatie', 'trim|required|max_length[255]');
      $this->form_validation->set_rules('nights', 'Nopti', 'trim|required|greater_than[0]');
      
      $reference_date = false;
      $start_date = $this->input->post('start_date');
      $nights = (int)$this->input->post('nights');
      if(isset($start_date)){
        $date_reference_date = DateTime::createFromFormat('Y-m-d', trim($start_date));
        if ($date_reference_date && $date_reference_date->format('Y-m-d') == $start_date) {
          $reference_date = $date_reference_date;
          $reference_date->modify('+' . $nights . ' days');
        }
      }
      
      $occupancy = $this->input->post('occupancy');
      $room_occupancy = array();
      if(isset($occupancy) && is_array($occupancy)){
        $expected_room_index = 0;
        foreach($occupancy as $room_index => $room){
          $room_nr = $room_index+1;
          if($room_index !== $expected_room_index){
            $this->outputError('Index camera invalid la camera #' . $room_nr);
          }
          $expected_room_index++;
          if(!is_array($room)){
            $this->outputError('Informatii invalide la camera #' . $room_nr);
          }
          if(!isset($room['adt']) || !is_numeric($room['adt']) || $room['adt']<1 || $room['adt'] > 1000 || ('' . (int)$room['adt'] !== '' . $room['adt'])){
            $this->outputError('Numar invalid de adulti la camera #' . $room_nr);
          }
          $room_occupancy[$room_index] = array(
            'adt' => (int)$room['adt']
          );
          if(isset($room['chd'])){
            $room_occupancy[$room_index]['chd']=array();
            $room_occupancy[$room_index]['birth_date']=array();
            if(!isset($room['chd']['age']) || !is_array($room['chd']['age']) || empty($room['chd']['age']) || count($room['chd']['age']) > 1000){
              $this->outputError('Valori invalide pentru copii la camera #' . $room_nr);
            }
            if(!isset($room['chd']['birth_date']) || !is_array($room['chd']['birth_date']) || empty($room['chd']['birth_date']) || count($room['chd']['birth_date']) > 1000){
              $this->outputError('Valori invalide pentru date de nastere copii la camera #' . $room_nr);
            }
            
            $expected_child_age_index = 0;
            foreach($room['chd']['age'] as $child_age_index => $child_age){
              $child_index = $child_age_index + 1;
              if($child_age_index !== $expected_child_age_index){
                $this->outputError('Index invalid copil la camera #' . $room_nr . ' copil #' . $child_index);
              }
              if(false !== $reference_date){
                $birth_date = isset($room['chd']['birth_date'][$child_age_index]) ? $room['chd']['birth_date'][$child_age_index] : false;
                if($birth_date){
                  $date_birth_date = DateTime::createFromFormat('d.m.Y', trim($birth_date));
                  if (!($date_birth_date && $date_birth_date->format('d.m.Y') == $birth_date)) {
                    $this->outputError('Data nastere invalida pentru copil la camera #' . $room_nr . ' copil #' . $child_index);
                  }
                  $years = $reference_date->diff($date_birth_date)->format('%y');
                  if($years != $child_age){
                    $this->outputError('Varsta invalida la camera #' . $room_nr . ' copil #' . $child_index);
                  }
                }
              }
              $expected_child_age_index++;
              if($child_age < 0 || $child_age > 17 || ('' . (int)$child_age !== '' . $child_age)){
                $this->outputError('Varsta invalida la camera #' . $room_nr . ' copil #' . $child_index);
              }
              $room_occupancy[$room_index]['chd'][$child_age_index] = $child_age + 1;
              $room_occupancy[$room_index]['birth_date'][$child_age_index] = null;
              if(false !== $reference_date){
                $room_occupancy[$room_index]['birth_date'][$child_age_index] = $birth_date;
              }
            }
          }
        }
      } else {
        $this->outputError('Nu ati introdus camere');
      }
      
      if($this->form_validation->run() == FALSE){
        $this->data['errors'] = $this->form_validation->error_array();
        $this->outputError($this->form_validation->error_string());
      }
      
      
      $start_date = trim($this->input->post('start_date'));

      $this->data['start_date'] = trim($this->input->post('start_date'));
      $this->data['nights'] = $nights;

      $this->data['hotel_name'] = trim($this->input->post('hotel_name'));
      $this->data['origin'] = trim($this->input->post('origin'));
      $this->data['destination'] = trim($this->input->post('destination'));
      
      $this->data['occupancy'] = $room_occupancy;
      // $this->data['ignore_session'] = 1;
      // $this->data['session'] = '/backend/order';
      
      if ($return) {
        return;
      }
      $this->output();
    }
    $this->redirect('backend', 'Acces invalid', 'error');
  }
  public function loadServices() {
    if ($this->input->is_ajax_request()) {
      if(!$this->user->can('backend-access')){
        $this->outputError('Acces restrictionat');
      }
      if(!$this->user->canAny('backend-trip-orders-access','backend-trip-orders-own-access')){
        $this->outputError('Acces restrictionat');
      }
      $id = (int)$this->input->post('order_id');
      if(!$id){
        $this->outputError('Comanda noua. Va rugam adaugati un serviciu.');
      }
      $this->load->model('TripOrder_model');
      $order = $this->TripOrder_model->getOrderById($id);
      if(!$order){
        $this->outputError('Comanda invalida.');
      }
      $trip_services = array();
      if($order->provider !== 'paralela45'){
        $this->outputError('Invalid order provider');
      }
      if(isset($order->services)){
        $trip_services = unserialize($order->services);
      }
      if(empty($trip_services)){
        $this->outputError('Niciun serviciu adaugat in comanda.');
      }
      $this->data['services'] = $trip_services;
      $this->data['currency_code'] = $order->currency;
      $this->data['trip_order_id'] = $order->trip_order_id;
      $this->output();
    }
    $this->redirect('backend', 'Acces invalid', 'error');
  }
  public function bookServices() {
    if ($this->input->is_ajax_request()) {
      if(!$this->user->can('backend-access')){
        $this->outputError('Acces restrictionat');
      }
      if(!$this->user->canAny('backend-trip-orders-access','backend-trip-orders-own-access')){
        $this->outputError('Acces restrictionat');
      }
      $id = (int)$this->input->post('order_id');
      if(!$id){
        $this->outputError('Comanda noua. Va rugam adaugati un serviciu.');
      }
      $this->load->model('TripOrder_model');
      $order = $this->TripOrder_model->getOrderById($id);
      if(!$order){
        $this->outputError('Comanda invalida.');
      }
      $trip_services = array();
      if($order->provider !== 'paralela45'){
        $this->outputError('Invalid order provider');
      }
      if(isset($order->services)){
        $trip_services = unserialize($order->services);
      }
      if(empty($trip_services)){
        $this->outputError('Niciun serviciu adaugat in comanda.');
      }
      try{
        $this->Paralela45_Strainatate_model->bookService($order);
      } catch(Exception $e){
        $this->outputError($e->getMessage());
      }
      $order = $this->TripOrder_model->getOrderById($id);
      $this->data['trip_order_id'] = $order->trip_order_id;
      $this->addMessage('Rezervarea a fost creata cu succes');
      $this->output();
    }
    $this->redirect('backend', 'Acces invalid', 'error');
  }
  public function removeServices() {
    if ($this->input->is_ajax_request()) {
      if(!$this->user->can('backend-access')){
        $this->outputError('Acces restrictionat');
      }
      if(!$this->user->canAny('backend-trip-orders-access','backend-trip-orders-own-access')){
        $this->outputError('Acces restrictionat');
      }
      $id = (int)$this->input->post('order_id');
      $this->load->model('TripOrder_model');
      $order = $this->TripOrder_model->getOrderById($id);
      if(!$order){
        $this->outputError('Comanda invalida.');
      }
      if($order->provider !== 'paralela45'){
        $this->outputError('Invalid order provider');
      }
      if($order->trip_order_id){
        $this->outputError('Nu se poate elimina serviciul odata ce a fost rezervat.');
      }
      $trip_services = array();
      if(isset($order->services)){
        $trip_services = unserialize($order->services);
      }
      $service_id = (int)$this->input->post('service_id');
      if(isset($trip_services[$service_id])){
        array_splice($trip_services, $service_id);
      }
      $this->data['services'] = $trip_services;
      $data = array();
      $data['id'] = $id;
      $services = serialize($trip_services);
      $data['services'] = $services;
      if(empty($trip_services)){
        $data['total'] = 0;
        $data['amount'] = 0;
      }
      $this->TripOrder_model->saveOrder($data);
      
      $this->addMessage('Serviciul a fost eliminat', 'success');
      $this->output();
    }
    $this->redirect('backend', 'Acces invalid', 'error');
  }
  public function addService(){
    $is_ajax_request = $this->input->is_ajax_request();
    if(!$this->user->can('backend-access')){
      $this->outputError('Acces restrictionat');
    }
    if(!$this->user->canAny('backend-trip-orders-access','backend-trip-orders-own-access')){
      $this->outputError('Acces restrictionat');
    }
    
    $this->load->library('form_validation');
    $id = $this->input->post('order_id');
    $provider = 'paralela45';
    $this->form_validation->set_rules('comment', 'Comentariu', 'trim|max_length[1024]');
    $this->form_validation->set_rules('offer_id', 'ID oferta', 'trim|required|max_length[255]');
    if($id){
      $this->form_validation->set_rules('order_id', 'Comanda', 'required|valid_order_id',array(
        'required' => 'Comanda invalida',
        'valid_order_id' => 'Comanda invalida',
      ));
    }
    $this->form_validation->set_rules('origin', 'Oras plecare', 'trim|required|max_length[255]');
    $this->form_validation->set_rules('checkin', 'Checkin', 'trim|required|max_length[10]|valid_date[Y-m-d]',array(
      'valid_date' => 'Formatul datei este invalid',
    ));
    $this->form_validation->set_rules('package_variant_id', 'Cod oferta', 'trim|required|max_length[255]',array(
      'required' => 'Informatii invalide',
      'max_length' => 'Informatii invalide',
    ));
    $checkin = $this->input->post('checkin');
    $checkout = $this->input->post('checkout');
    $extra_services = (array)$this->input->post('extra_services');
    
    $reference_date = new DateTime();
    $checkout_date = DateTime::createFromFormat('Y-m-d', $checkout);
    if ($checkout_date && $checkout_date->format('Y-m-d') == $checkout) {
      $reference_date = $checkout_date;
    }
    $_18_years_ago = $checkout_date->modify('-18 years');
    $minimum_date_start = isset($checkin) && strlen(trim($checkin)) ? trim($checkin) : date('Y-m-d');
    $this->form_validation->set_rules('checkout', 'Data checkout', 'trim|required|valid_date[Y-m-d]|is_greater_than[' . $minimum_date_start . ']',array(
      'required' => 'Informatii invalide',
      'valid_date' => 'Informatii invalide',
      'is_greater_than' => 'Informatii invalide',
    ));
    $rooms = isset($_POST['room']) && is_array($_POST['room']) ? $_POST['room'] : null;
    $_POST['room'] = $rooms;
    $service_rooms = array();
    $rooms_occupancy = array();
    if(!isset($rooms)){
      $this->form_validation->set_rules('room', 'Detalii persoane', 'required', array(
        'required' => 'Completati informatiile persoanelor'
      ));
    } else {
      foreach($rooms as $room_index => $assigned_room){
        $room_number = $room_index + 1;
        $room_occupancy = array(
          'adt' => 0,
          'chd' => array(),
        );
        $service_room = array(
          'adt' => array(),
          'chd' => array(),
        );
        foreach($assigned_room['adt'] as $adult_index=>$adult_details){
          $fake_post_index = 'adults_' . $room_index . '_' . $adult_index;
          $_POST[$fake_post_index . '_' . 'birth_date'] = isset($adult_details['birth_date']) ? $adult_details['birth_date'] : null;
          $_POST[$fake_post_index . '_' . 'title'] = isset($adult_details['title']) ? $adult_details['title'] : null;
          $_POST[$fake_post_index . '_' . 'firstname'] = isset($adult_details['firstname']) ? $adult_details['firstname'] : null;
          $_POST[$fake_post_index . '_' . 'lastname'] = isset($adult_details['lastname']) ? $adult_details['lastname'] : null;
          
          $this->form_validation->set_rules($fake_post_index . '_' . 'birth_date', 'Data nastere', 'trim|required|valid_date[d.m.Y]',array(
            'required' => 'Data nastere invalida pentru adult #' . ($adult_index+1) . ' camera #' .$room_number ,
            'valid_date' => 'Data nastere invalida pentru adult #' . ($adult_index+1) . ' camera #' .$room_number ,
          ));
          $this->form_validation->set_rules($fake_post_index . '_' . 'title', 'Titlu', 'trim|required|in_list[mr,mrs,ms]',array(
            'required' => 'Titlu invalid pentru adult #' . ($adult_index+1) . ' camera #' .$room_number ,
            'in_list' => 'Titlu invalid pentru adult #' . ($adult_index+1) . ' camera #' .$room_number ,
          ));
          $this->form_validation->set_rules($fake_post_index . '_' . 'firstname', 'Prenume', 'trim|required|max_length[255]',array(
            'required' => 'Prenumele neintrodus pentru adult #' . ($adult_index+1) . ' camera #' .$room_number ,
            'max_length' => 'Prenumele introdus depaseste limita admisa pentru adult #' . ($adult_index+1) . ' camera #' .$room_number ,
          ));
          $this->form_validation->set_rules($fake_post_index . '_' . 'lastname', 'Lastname', 'trim|required|max_length[255]',array(
            'required' => 'Numele neintrodus pentru adult #' . ($adult_index+1) . ' camera #' .$room_number ,
            'max_length' => 'Numele introdus depaseste limita admisa pentru adult #' . ($adult_index+1) . ' camera #' .$room_number ,
          ));
          $service_room_adult = array();
          if(isset($adult_details['title']) && is_string($adult_details['title'])){
            $service_room_adult['title'] = $adult_details['title'];
          }
          if(isset($adult_details['firstname']) && is_string($adult_details['firstname'])){
            $service_room_adult['firstname'] = trim($adult_details['firstname']);
          }
          if(isset($adult_details['lastname']) && is_string($adult_details['lastname'])){
            $service_room_adult['lastname'] = trim($adult_details['lastname']);
          }
          if(isset($adult_details['birth_date']) && is_string($adult_details['birth_date'])){
            $birth_date = trim($adult_details['birth_date']);
            $date_birth_date = DateTime::createFromFormat('d.m.Y', $birth_date);
            if ($date_birth_date && $date_birth_date->format('d.m.Y') == $birth_date && $date_birth_date <= $_18_years_ago) {
              $service_room_adult['birth_date'] = $date_birth_date->format('Y-m-d');
            } else {
              $this->outputError('Data de nastere invalida pentru adult #' . ($adult_index+1) . ' camera #' .$room_number);
            }
          }
          $service_room['adt'][] = $service_room_adult;
          $room_occupancy['adt']++;
        }
        if(isset($assigned_room['chd'])){
          if(!is_array($assigned_room['chd'])){
            $this->outputError('Variabila de tip incorect');
          }
          foreach($assigned_room['chd'] as $child_index=>$child_details){
            $fake_post_index = 'childs_' . $room_index . '_' . $child_index;
            $_POST[$fake_post_index . '_' . 'birth_date'] = isset($child_details['birth_date']) ? $child_details['birth_date'] : null;
            $_POST[$fake_post_index . '_' . 'title'] = isset($child_details['title']) ? $child_details['title'] : null;
            $_POST[$fake_post_index . '_' . 'firstname'] = isset($child_details['firstname']) ? $child_details['firstname'] : null;
            $_POST[$fake_post_index . '_' . 'lastname'] = isset($child_details['lastname']) ? $child_details['lastname'] : null;
            $_POST[$fake_post_index . '_' . 'age'] = isset($child_details['age']) ? $child_details['age'] : null;
            
            $this->form_validation->set_rules($fake_post_index . '_' . 'birth_date', 'Data nastere', 'trim|required|valid_date[d.m.Y]',array(
              'required' => 'Data nastere invalida pentru copil #' . ($child_index+1) . ' camera #' .$room_number ,
              'valid_date' => 'Data nastere invalida pentru copil #' . ($child_index+1) . ' camera #' .$room_number ,
            ));
            $this->form_validation->set_rules($fake_post_index . '_' . 'title', 'Titlu', 'trim|required|in_list[mr,ms]',array(
              'in_list' => 'Titlu invalid pentru copil #' . ($child_index+1) . ' camera #' .$room_number ,
            ));
            $this->form_validation->set_rules($fake_post_index . '_' . 'firstname', 'Prenume', 'trim|required|max_length[255]',array(
              'required' => 'Prenumele neintrodus pentru copil #' . ($child_index+1) . ' camera #' .$room_number ,
              'max_length' => 'Prenumele introdus depaseste limita admisa pentru copil #' . ($child_index+1) . ' camera #' .$room_number ,
            ));
            $this->form_validation->set_rules($fake_post_index . '_' . 'lastname', 'Lastname', 'trim|required|max_length[255]',array(
              'required' => 'Numele neintrodus pentru copil #' . ($child_index+1) . ' camera #' .$room_number ,
              'max_length' => 'Numele introdus depaseste limita admisa pentru copil #' . ($child_index+1) . ' camera #' .$room_number ,
            ));
            $this->form_validation->set_rules($fake_post_index . '_' . 'age', 'Age', 'trim|required|is_greater_than_or_equal_to[0]|is_less_than_or_equal_to[17]',array(
              'required' => 'Varsta neintrodusa pentru copil #' . ($child_index+1) . ' camera #' .$room_number ,
              'is_greater_than_or_equal_to' => 'Varsta incompatibila pentru copil #' . ($child_index+1) . ' camera #' .$room_number ,
              'is_less_than_or_equal_to' => 'Varsta incompatibila pentru copil #' . ($child_index+1) . ' camera #' .$room_number ,
            ));
            $service_room_child = array();
            $age = abs(intval($_POST[$fake_post_index . '_' . 'age']));
            if($age > 17){
              $age = 17;
            }
            $age_years_ago = $checkout_date->modify('-' . $age . ' years');
            if(isset($child_details['birth_date']) && is_string($child_details['birth_date'])){
              $birth_date = trim($child_details['birth_date']);
              $date_birth_date = DateTime::createFromFormat('d.m.Y', $birth_date);
              if ($date_birth_date && $date_birth_date->format('d.m.Y') == $birth_date && $date_birth_date > $age_years_ago) {
                $service_room_child['birth_date'] = $date_birth_date->format('Y-m-d');
                
              } else {
                $this->outputError('Data de nastere invalida pentru copil #' . ($child_index+1) . ' camera #' .$room_number);
              }
            }
            if(isset($child_details['title']) && is_string($child_details['title'])){
              $service_room_child['title'] = $child_details['title'];
            }
            if(isset($child_details['firstname']) && is_string($child_details['firstname'])){
              $service_room_child['firstname'] = trim($child_details['firstname']);
            }
            if(isset($child_details['lastname']) && is_string($child_details['lastname'])){
              $service_room_child['lastname'] = trim($child_details['lastname']);
            }
            $room_occupancy['chd'][] = $age;
            $service_room['chd'][] = $service_room_child;
          }
        }
        $service_rooms[] = $service_room;
        $rooms_occupancy[] = $room_occupancy;
      }
    }
    if($this->form_validation->run() == FALSE){
      $this->data['errors'] = $this->form_validation->error_array();
      $this->outputError($this->form_validation->error_string());
    }
    $offer_id = trim($this->input->post('offer_id'));
    $id_arr = explode('_', $offer_id);
    $service_info = array();
    $service_info['type'] = 'strainatate';
    $service_info['offer_id'] = $offer_id;
    $service_info['package_id'] = trim($this->input->post('package_id'));
    $service_info['package_variant_id'] = trim($this->input->post('package_variant_id'));
    $service_info['departure_city_code'] = trim($this->input->post('origin'));
    $service_info['checkin'] = trim($this->input->post('checkin'));
    $service_info['checkout'] = trim($this->input->post('checkout'));
    $service_info['comment'] = trim($this->input->post('comment'));
    $service_info['tour_op_code'] = isset($id_arr[0]) && strlen(trim($id_arr[0])) ? trim($id_arr[0]) : null;
    $service_info['country_code'] = isset($id_arr[1]) && strlen(trim($id_arr[1])) ? trim($id_arr[1]) : null;
    $service_info['destination_city_code'] = isset($id_arr[2]) && strlen(trim($id_arr[2])) ? trim($id_arr[2]) : null;
    $service_info['product_code'] = isset($id_arr[3]) && strlen(trim($id_arr[3])) ? trim($id_arr[3]) : null;
    $service_info['occupancy'] = $rooms_occupancy;
    $service_info['service_rooms'] = $service_rooms;
    $service_info['selected_extra_services'] = $extra_services;
    
    $this->data['service_info'] = $service_info;
    $data = array();
    $data['id'] = null;
    $data['provider'] = $provider;
    $data['services'] = array();
    $coupon_percentage = 0;
    if ($id) {
      $order = $this->TripOrder_model->getOrderById($id);
      if(!$order){
        $this->outputError('Invalid order');
      }
      if($order->provider != $provider){
        $this->outputError('Invalid order provider');
      }
      $coupon_percentage = floatval($order->coupon_percentage);
      $data['id'] = $order->id;
      $data['provider'] = $order->provider;
      if(isset($order->services)){
        $services = unserialize($order->services);
        if(!empty($services)){
          $this->outputError('Nu puteti avea mai mult de un serviciu in comanda');
        }
        if(is_array($services)){
          $data['services'] = $services;
        }
      }
    }
    try{
      $this->Paralela45_Strainatate_model->getBookingService($service_info);
    } catch (Exception $e){
      $this->outputError($e->getMessage());
    }
    $data['services'][] = $service_info;
    
    
    if(!$id && $is_ajax_request){
      $this->addMessage('Informatiile au fost validate', 'success');
      $this->output();
    }
    $this->load->model('TripOrder_model');
    $data['services'] = serialize($data['services']);
    $data['currency'] = $service_info['currency_code'];
    $data['total'] = $service_info['price'];
    $data['amount'] = $service_info['price'] * (1 - $coupon_percentage/100);
    
    $order_id = $this->TripOrder_model->saveOrder($data);
    $data['id'] = $order_id;
    if($is_ajax_request){
      $this->addMessage('Informatiile au fost salvate', 'success');
      $this->output();
    }
    $this->redirect('backend/trip/orders/edit?id=' . $order_id,'Informatiile serviciului au fost asociate acestei comenzi noi.','success');
  }
  
  /* TODO - REMOVE THE NEXT FUNCTION */
 /*  public function getBookingRequestTest() {
    $order_id = 154;
    $this->load->model('TripOrder_model');
    $order = $this->TripOrder_model->getOrderById($order_id);
    
    $search_data = array(
      'BookingReference' => array(
        '_' => $order->trip_order_id,
        'Source' => 'api',
      ),
    );
    $response = $this->Paralela45_model->getBookingRequest($search_data);
    $services = unserialize($order->services);
    echo '<pre>';
    print_r($response);
    print_r($services);
    die;
  }
  public function bookServiceTest() {
    $order_id = 154;
    $this->load->model('TripOrder_model');
    $order = $this->TripOrder_model->getOrderById($order_id);
    $this->Paralela45_Strainatate_model->bookService($order);
    echo 'booking done';
    die;
  } */
}