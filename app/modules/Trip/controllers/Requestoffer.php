<?php
if (!defined('BASEPATH'))
  exit('No direct script access allowed');

class Requestoffer extends MX_Controller {
  public function custom() {
    if (!$this->input->is_ajax_request()) {
      $this->redirect('', 'Vă mulțumim! Un consultant vă va contacta în cel mai scurt timp posibil.','success');
    }
    $date_expire = date('Y-m-d');
    $this->load->library('form_validation');
    $this->form_validation->set_rules('last_name', 'Nume', 'trim|required|max_length[255]');
    $this->form_validation->set_rules('first_name', 'Nume prenume', 'trim|required|max_length[255]');
    $this->form_validation->set_rules('email', 'Adresa email', 'trim|required|max_length[255]|valid_email');
    $this->form_validation->set_rules('phone', 'Numar telefon', 'trim|required|max_length[100]');
    $this->form_validation->set_rules('destination', 'Destinatie', 'trim|required|max_length[3000]');
    $this->form_validation->set_rules('date', 'Data plecare', 'trim|required|valid_date[Y-m-d]|is_greater_than_or_equal_to[' . $date_expire . ']',array(
      'valid_date' => 'Data plecare invalida',
      'is_greater_than_or_equal_to' => 'Data plecare in trecut',
    ));
    $this->form_validation->set_rules('nights', 'Nopti', 'trim|required|max_length[255]|is_numeric|is_greater_than_or_equal_to[0]',array(
      'is_greater_than_or_equal_to' => 'Numar nopti invalid',
    ));
    $this->form_validation->set_rules('stars', 'Stele', 'trim|required|max_length[255]');
    $this->form_validation->set_rules('board', 'Masa', 'trim|required|max_length[255]');
    $this->form_validation->set_rules('transport', 'Transport', 'trim|required|max_length[255]');
    $this->form_validation->set_rules('budget', 'Buget', 'trim|required|is_numeric',array(
      'is_numeric' => 'Buget invalid',
    ));
    $this->form_validation->set_rules('preferences', 'Preferinte', 'trim|max_length[3000]');
    $this->form_validation->set_rules('agency', 'Agentie', 'trim|max_length[255]');
    
    $this->form_validation->set_rules('occupancy', 'Camere/Persoane', 'required',array(
      'required' => 'Informatii Camere/Persoane invalide',
    ));
    $occupancy = $this->input->post('room');
    
    if(!$occupancy){
      $occupancy = array();
    }
    $_POST['occupancy'] = null;
    $this->load->model('Trip/Hotels_model');
    
    if ($occupancy && (count($occupancy) <= $this->Hotels_model->max_rooms)) {
      foreach($occupancy as $occupants){
        if(!isset($occupants['adt']) || !is_numeric($occupants['adt']) || ((int)$occupants['adt'] . '' !== $occupants['adt'] . '') || ($occupants['adt'] <= 0) || ($occupants['adt'] > $this->Hotels_model->max_adults_per_room)){
          $occupancy = null; break;
        }
        if(!isset($occupants['chd']) || empty($occupants['chd'])){
          continue;
        }
        if(!is_array($occupants['chd'])){
          $occupancy = null; break;
        }
        $ages = $occupants['chd'];
        if (!$ages || (count($ages) > $this->Hotels_model->max_children_per_room)) {
          $occupancy = null; break;
        }
        $expected_child_index = 0;
        foreach ($ages as $child_index => $child_age) {
          if ($child_index != $expected_child_index) {
            $ages = null; break;
          }
          $expected_child_index++;
          if(!is_numeric($child_age) || ((int)$child_age . '' !== $child_age . '') || ($child_age < 0) || ($child_age >= $this->Hotels_model->max_child_age)){
            $ages = null; break;
          }
        }
        
        if(!$ages){
          $occupancy = null; break;
        }
      }
      $_POST['occupancy'] = $occupancy ? 1 : null;
    }
    $destination = trim($this->input->post('destination'));
    $first_name = trim($this->input->post('first_name'));
    $last_name = trim($this->input->post('last_name'));
    $nights = trim($this->input->post('nights'));
    $date = trim($this->input->post('date'));
    $stars = trim($this->input->post('stars'));
    $board = trim($this->input->post('board'));
    $facilities = trim($this->input->post('facilities'));
    $transport = trim($this->input->post('transport'));
    $budget = trim($this->input->post('budget'));
    $preferences = trim($this->input->post('preferences'));
    $agency = trim($this->input->post('agency'));
    
    
    $_POST['title'] = $this->input->post('destination');
    $_POST['amount'] = $this->input->post('budget');
    $_POST['currency'] = 'EUR';
    $_POST['fullname'] = trim($this->input->post('first_name') . ' ' . $this->input->post('last_name'));
    $title = trim($this->input->post('title'));
    
    $fullname = trim($this->input->post('fullname'));
    $email = trim($this->input->post('email'));
    $phone = trim($this->input->post('phone'));
    $amount = trim($this->input->post('amount'));
    $currency = trim($this->input->post('currency'));
    $type = 'custom';
    $hotel_id = null;
    $package_id = null;
    $flight_itinerary_code = null;
    $amount_hotel = $amount;
    $amount_package = null;
    $amount_flight = null;
    
    $_POST['message'] = "Cerere de oferta personalizata:" . "\n";
    $_POST['message'] .= "Hotelul sau Destinatia: " . $title . "\n";
    $_POST['message'] .= "Data plecare: " . $date . "\n";
    $_POST['message'] .= "Nopti: " . $nights . "\n";
    
    $_POST['message'] .= "\n";
    $_POST['message'] .= "Camere: " . $nr_camere . "\n";
    foreach($occupancy as $room_index => $occupants){
      $_POST['message'] .= "- Camera #" . ($room_index + 1) . ":";
      $_POST['message'] .= " " . $occupants['adt'] . " adulti";
      if(isset($occupants['chd'])){
        $children = count($occupants['chd']);
        $_POST['message'] .= " " . $children . " " . ($children == 1 ? 'copil' : 'copii');
        sort($occupants['chd']);
        $_POST['message'] .= " (" . implode(', ', $occupants['chd']) . " " . ($children == 1 && $occupants['chd'][0] == 1 ? 'an' : 'ani') . ")";
      }
      $_POST['message'] .= "\n";
      
    }
    $_POST['message'] .= "\n";
    $_POST['message'] .= "Stele: " . $stars . "\n";
    $_POST['message'] .= "Buget: " . $budget . "\n";
    $_POST['message'] .= "Valuta: " . $currency . "\n";
    $_POST['message'] .= "Transport: " . $transport . "\n";
    $_POST['message'] .= "Agentie: " . $agency . "\n";
    $_POST['message'] .= "Preferinte: " . $preferences . "\n";
	if($board){
		$_POST['message'] .= "Tip masa: " . $board . "\n";
	}
	if($facilities){
		$_POST['message'] .= "Facilitati: " . $facilities . "\n";
	}
    $message = trim($this->input->post('message'));
    
    $newsletter = trim($this->input->post('newsletter'));
    if($newsletter){
      if($this->user->id && ($this->user->email === $email)){
        $user_data = array();
        $user_data['user_id'] = $this->user->id;
        $user_data['newsletter'] = 1;
        $this->db->where('user_id', $user_data['user_id']);
        $this->db->update('ac_user', $user_data);
      } else {
        $this->db->where('user_email', $email);
        $q = $this->db->get('ac_user');
        $existing_user = $q->row();
        /* if($existing_user){
          $this->addMessage('Este necesar sa va conectati cu acest utilizator pentru a va abona.', 'error');
          if ($this->input->is_ajax_request()) {
            $this->output('error');
          }
          $this->redirect('');
        } */
      }
      $data = array();
      $data['email'] = $email;
      $data['user_id'] = 0;
      $data['status'] = 1;
      $data['time_created'] = date('Y-m-d H:i:s');
      
      $sql = $this->db->insert_string('ac_newsletter', $data) . " ON DUPLICATE KEY UPDATE `status` = VALUES(`status`)";
      $this->db->query($sql);
      
      $this->load->model('WhiteImage_model');
      $search = array(
        'email|' . $email . '|1'
      );
      $return_fields = 'all';
      $response = $this->WhiteImage_model->select_one($search,$return_fields);
      if($response){
        $response_decoded = json_decode($response);
        if($response_decoded){
          if($response_decoded->count && ($response_decoded->subscriber->subscribe_status=='no')){
            $emailid = $response_decoded->subscriber->emailid;
            $this->WhiteImage_model->resubscribe($emailid);
          } else {
            $data = array();
            $data['email'] = $email;
            $data['sursa'] = 'AccentTravel&Events';
            $response = $this->WhiteImage_model->save($data);
          }
        } 
      }
      Modules :: run ('Mailer/newsletter_subscribe', array(
        'to'=>$email,
      ));
    }
    
    
    $data_decoded_hash = array(
      'last_name' => $last_name,
      'first_name' => $first_name,
      'email' => $email,
      'phone' => $phone,
      'date' => $date,
      'nights' => $nights,
      'occupancy' => $occupancy,
      'stars' => $stars,
      'board' => $board,
      'transport' => $transport,
      'budget' => $budget,
      'preferences' => $preferences,
      'agency' => $agency,
    );
    
    $code_check_data = $data_decoded_hash;
    
    $code = md5(json_encode($code_check_data));
    $_POST['code'] = $code;
    
    $this->form_validation->set_rules('code', 'Cod unic', 'is_unique[trip_request_offer.code]',array(
      'is_unique' => 'Cererea a fost deja introdusa',
    ));
   
    if ($this->form_validation->run() == FALSE) {
      $this->data['errors'] = $this->form_validation->error_array();
      $this->outputError($this->form_validation->error_string());
    }
    $time_created = date('Y-m-d H:i:s');
    $status = 1;

    // $this->outputError('block');
    
    $hotel_data = null;
    $package_data = null;
    $package_data = null;
    $data = array();
    $data['title'] = $title;
    $data['fullname'] = $fullname;
    $data['email'] = $email;
    $data['phone'] = $phone;
    $data['type'] = $type;
    $data['hotel_id'] = $hotel_id;
    $data['package_id'] = $package_id;
    $data['flight_itinerary_code'] = $flight_itinerary_code;
    $data['amount'] = $amount;
    $data['amount_hotel'] = $amount_hotel;
    $data['amount_package'] = $amount_package;
    $data['amount_flight'] = $amount_flight;
    $data['currency'] = $currency;
    $data['data_hotel'] = $hotel_data ? json_encode($hotel_data) : null;
    $data['data_package'] = $package_data ? json_encode($package_data) : null;
    $data['data_flight'] = $flight_data ? json_encode($flight_data) : null;
    $data['time_created'] = $time_created;
    $data['date_expire'] = $date_expire;
    $data['code'] = $code;
    $data['hash_hotel'] = $hotel_data ? md5(json_encode($hotel_data)) : null;
    $data['hash_package'] = $package_data ? md5(json_encode($package_data)) : null;
    $data['hash_flight'] = $flight_data ? md5(json_encode($flight_data)) : null;
    $data['status'] = $status;
    $data['message'] = $message;
    
    $this->db->insert('trip_request_offer', $data);
    
    Modules :: run ('Mailer/trip_requestoffer', array('to'=>$email, 'maildata'=>$data));
    
    $this->addMessage('Cererea de oferta a fost trimisa');
    $this->output();
    exit;
  }
  public function add() {
    $date_expire = date('Y-m-d');
    $this->load->library('form_validation');
    $this->form_validation->set_rules('title', 'Titlu', 'trim|required|max_length[255]');
    $this->form_validation->set_rules('fullname', 'Nume', 'trim|required|max_length[255]');
    $this->form_validation->set_rules('email', 'Adresa email', 'trim|required|max_length[255]|valid_email');
    $this->form_validation->set_rules('phone', 'Numar telefon', 'trim|required|max_length[100]');
    $this->form_validation->set_rules('message', 'Informatii', 'trim|required|max_length[3000]');
	
	$recaptcha = $this->input->post('g-recaptcha-response');
	$_POST['captcha'] = null;
    if (!empty($recaptcha)) {
      // $_POST['captcha'] = 1;
      // if (!$this->input->is_ajax_request()) {
        $response = $this->recaptcha->verifyResponse($recaptcha);
        if (isset($response['success']) and $response['success'] === true) {
          $_POST['captcha'] = 1;
        }
      // }
    }
    $this->form_validation->set_rules('captcha', 'Captcha', 'required', array(
      'required'=>'Va rugam sa bifati verificarea',
    ));
    
    $type = trim($this->input->post('type'));
    $this->form_validation->set_rules('type', 'Tip oferta', 'trim|in_list[hotel,package,flight,citybreak]',array(
      'in_list' => 'Tip oferta invalid',
    ));
    if(in_array($type, array('hotel','citybreak', 'package'))){
      $this->form_validation->set_rules('ref_id', 'ID', 'trim|required|is_numeric|is_greater_than[0]',array(
        'is_numeric' => 'ID invalid',
        'is_greater_than' => 'ID invalid',
      ));
    }
    if(in_array($type, array('flight'))){
      $this->form_validation->set_rules('itinerary_code', 'Cod itinerariu', 'trim|required');
    }
    $this->form_validation->set_rules('amount', 'Pret', 'trim|required|is_numeric',array(
      'is_numeric' => 'Pret invalid',
    ));
    $this->form_validation->set_rules('currency', 'Valuta', 'trim|in_list[RON,EUR]',array(
      'in_list' => 'Valuta invalida',
    ));
    $this->form_validation->set_rules('data', 'Date cautare', 'trim|required',array(
      'required' => 'Informatii invalide',
    ));
    $this->form_validation->set_rules('code', 'Cod unic', 'is_unique[trip_request_offer.code]',array(
      'is_unique' => 'Cererea a fost deja introdusa',
    ));
    $data = $this->input->post('data');
    $data_decoded = json_decode($data, true);
    
    if(!$data_decoded){
      $_POST['data'] = null;
    }
    $title = trim($this->input->post('title'));
    $message = trim($this->input->post('message'));
    $fullname = trim($this->input->post('fullname'));
    $ref_id = trim($this->input->post('ref_id'));
    $itinerary_code = trim($this->input->post('itinerary_code'));
    $email = trim($this->input->post('email'));
    $phone = trim($this->input->post('phone'));
    $amount = trim($this->input->post('amount'));
    $currency = trim($this->input->post('currency'));
    $newsletter = trim($this->input->post('newsletter'));
    
    $minimum_start_date = new DateTime('2 days');
    $minimum_start_date_formatted = $minimum_start_date->format('Y-m-d');
    $hotel_id = null;
    $package_id = null;
    $flight_itinerary_code = null;
    $hotel_hash = null;
    $package_hash = null;
    $flight_hash = null;
    $hotel_data = null;
    $flight_data = null;
    $package_data = null;
    $amount_hotel = null;
    $amount_package = null;
    $amount_flight = null;
    if(in_array($type, array('hotel', 'citybreak'))){
      if($type == 'hotel'){
        $amount_hotel = $amount;
        $this->load->model('Trip/Hotels_model');
        $needed_model = $this->Hotels_model;
      } else {
        $this->form_validation->set_rules('amount_hotel', 'Pret hotel', 'trim|required|is_numeric',array(
          'is_numeric' => 'Pret invalid',
        ));
        $amount_hotel = trim($this->input->post('amount_hotel'));
        $this->load->model('Trip/Citybreaks_model');
        $needed_model = $this->Citybreaks_model;
      }
      $data_decoded['city_id'] = isset($data_decoded['destination_city_id']) ? $data_decoded['destination_city_id'] : null;
      $data_decoded['country_id'] = isset($data_decoded['destination_country_id']) ? $data_decoded['destination_country_id'] : null;
      $data_decoded['city_name'] = isset($data_decoded['destination_city_name']) ? $data_decoded['destination_city_name'] : null;
      $data_decoded['country_name'] = isset($data_decoded['destination_country_name']) ? $data_decoded['destination_country_name'] : null;
      $data_decoded_hash = array_intersect_key(
        $data_decoded,
        array_flip(array(
          'start_date',
          'end_date',
          'city_id',
          'country_id',
          'city_name',
          'country_name',
          'occupancy',
        ))
      );
      $code_check_data = array_intersect_key(
        $data_decoded_hash,
        array_flip(array(
          'start_date',
          'end_date',
          'occupancy',
        ))
      );
      $hotel_id = $ref_id;
      $code_check_data['hotel_id'] = $ref_id;
      $code_check_data['type'] = $type;
      $code_check_data['email'] = $email;
      $date_expire = $_POST['start_date'] = isset($data_decoded_hash['start_date']) ? $data_decoded_hash['start_date'] : null;
      $this->form_validation->set_rules('start_date', 'Data checkin', 'required|valid_date[Y-m-d]',array(
        'valid_date' => 'Data checkin invalida',
      ));
      $_POST['end_date'] = isset($data_decoded_hash['end_date']) ? $data_decoded_hash['end_date'] : null;
      $this->form_validation->set_rules('end_date', 'Data checkout', 'required|valid_date[Y-m-d]',array(
        'valid_date' => 'Data checkout invalida',
      ));
      $this->form_validation->set_rules('occupancy', 'Camere/Persoane', 'required',array(
        'required' => 'Informatii Camere/Persoane invalide',
      ));
      $occupancy = isset($data_decoded_hash['occupancy']) && is_array($data_decoded_hash['occupancy']) ? $data_decoded_hash['occupancy'] : null;
      $hotel_data = $data_decoded_hash;
      $_POST['occupancy'] = null;
      if ($occupancy && (count($occupancy) <= $needed_model->max_rooms)) {
        foreach($occupancy as $occupants){
          if(!isset($occupants['adt']) || !is_numeric($occupants['adt']) || ((int)$occupants['adt'] . '' !== $occupants['adt'] . '') || ($occupants['adt'] <= 0) || ($occupants['adt'] > $needed_model->max_adults_per_room)){
            $occupancy = null; break;
          }
          if(!isset($occupants['chd']) || empty($occupants['chd'])){
            continue;
          }
          if(!is_array($occupants['chd'])){
            $occupancy = null; break;
          }
          $ages = isset($occupants['chd']['age']) && is_array($occupants['chd']['age']) ? $occupants['chd']['age'] : null;
          if (!$ages || (count($ages) > $needed_model->max_children_per_room)) {
            $occupancy = null; break;
          }
          $expected_child_index = 0;
          foreach ($ages as $child_index => $child_age) {
            if ($child_index != $expected_child_index) {
              $ages = null; break;
            }
            $expected_child_index++;
            if(!is_numeric($child_age) || ((int)$child_age . '' !== $child_age . '') || ($child_age <= 0) || ($child_age > $needed_model->max_child_age)){
              $ages = null; break;
            }
          }
          if(!$ages){
            $occupancy = null; break;
          }
        }
        $_POST['occupancy'] = $occupancy ? 1 : null;
      }
      $data_expire = $_POST['start_date'];
    }
    elseif($type == 'package'){
      $amount_package = $amount;
      $this->load->model('Trip/Packages_model');
      $data_decoded_hash = array_intersect_key(
        $data_decoded,
        array_flip(array(
          'start_date',
          'nights',
          'city_id',
          'category',
          'occupancy',
        ))
      );
      $code_check_data = array_intersect_key(
        $data_decoded_hash,
        array_flip(array(
          'start_date',
          'nights',
          'occupancy',
        ))
      );
      $package_id = $ref_id;
      $code_check_data['package_id'] = $ref_id;
      $code_check_data['type'] = $type;
      $code_check_data['email'] = $email;
      $date_expire = $_POST['start_date'] = isset($data_decoded_hash['start_date']) ? $data_decoded_hash['start_date'] : null;
      $this->form_validation->set_rules('start_date', 'Data checkin', 'required|valid_date[Y-m-d]',array(
        'valid_date' => 'Data checkin invalida',
      ));
      $_POST['nights'] = isset($data_decoded_hash['nights']) && ((int)$data_decoded_hash['nights']>0) ? (int)$data_decoded_hash['nights'] : 0;
      $this->form_validation->set_rules('nights', 'Numar nopti', 'required|is_greater_than_or_equal_to[0]',array(
        'is_greater_than_or_equal_to' => 'Numar nopti invalid',
      ));
      $this->form_validation->set_rules('occupancy', 'Camere/Persoane', 'required',array(
        'required' => 'Informatii Camere/Persoane invalide',
      ));
      $occupancy = isset($data_decoded_hash['occupancy']) && is_array($data_decoded_hash['occupancy']) ? $data_decoded_hash['occupancy'] : null;
      $package_data = $data_decoded_hash;
      $_POST['occupancy'] = null;
      if ($occupancy && (count($occupancy) <= $this->Packages_model->max_rooms)) {
        foreach($occupancy as $occupants){
          if(!isset($occupants['adt']) || !is_numeric($occupants['adt']) || ((int)$occupants['adt'] . '' !== $occupants['adt'] . '') || ($occupants['adt'] <= 0) || ($occupants['adt'] > $this->Packages_model->max_adults_per_room)){
            $occupancy = null; break;
          }
          if(!isset($occupants['chd']) || empty($occupants['chd'])){
            continue;
          }
          if(!is_array($occupants['chd'])){
            $occupancy = null; break;
          }
          $ages = $occupants['chd'];
          if (!$ages || (count($ages) > $this->Packages_model->max_children_per_room)) {
            $occupancy = null; break;
          }
          $expected_child_index = 0;
          foreach ($ages as $child_index => $child_age) {
            if ($child_index != $expected_child_index) {
              $ages = null; break;
            }
            $expected_child_index++;
            if(!is_numeric($child_age) || ((int)$child_age . '' !== $child_age . '') || ($child_age <= 0) || ($child_age > $this->Packages_model->max_child_age)){
              $ages = null; break;
            }
          }
          
          if(!$ages){
            $occupancy = null; break;
          }
        }
        $_POST['occupancy'] = $occupancy ? 1 : null;
      }
      $data_expire = $_POST['start_date'];
    }
    if(in_array($type,array('flight','citybreak'))){
      if($type == 'flight'){
        $amount_flight = $amount;
        $this->load->model('Trip/Flights_model');
        $needed_model = $this->Flights_model;
      } else {
        $this->form_validation->set_rules('amount_flight', 'Pret zbor', 'trim|required|is_numeric',array(
          'is_numeric' => 'Pret invalid',
        ));
        $amount_flight = trim($this->input->post('amount_flight'));
        $this->load->model('Trip/Citybreaks_model');
        $needed_model = $this->Citybreaks_model;
      
        $data_decoded['departure_date'] = isset($data_decoded['start_date']) ? $data_decoded['start_date'] : null;
        $data_decoded['return_date'] = isset($data_decoded['end_date']) ? $data_decoded['end_date'] : null;
        
        $data_decoded['cabine_type'] = 1;
      }
      $this->load->model('Trip/Flights_model');
      $data_decoded_hash = array_intersect_key(
        $data_decoded,
        array_flip(array(
          'departure_date',
          'return_date',
          'origin_city_id',
          'origin_city_name',
          'origin_location_id',
          'origin_location_name',
          'origin_country_id',
          'origin_country_name',
          'destination_city_id',
          'destination_city_name',
          'destination_location_id',
          'destination_location_name',
          'destination_country_id',
          'destination_country_name',
          'cabine_type',
          'direct_only',
          'go_only',
          'flex_dates',
          'passengers_adult',
          'varste_copii',
          'passengers_child',
          'passengers_infant_lap',
          'passengers_infant_seat',
          'passengers_senior',
          'passengers_youth',
        ))
      );
      $code_check_data = array_intersect_key(
        $data_decoded_hash,
        array_flip(array(
          'departure_date',
          'return_date',
          'origin_city_id',
          'origin_location_id',
          'origin_country_id',
          'destination_city_id',
          'destination_location_id',
          'cabine_type',
          'direct_only',
          'go_only',
          'flex_dates',
          'passengers_adult',
          'passengers_child',
          'passengers_infant_lap',
          'passengers_infant_seat',
          'passengers_senior',
          'passengers_youth',
        ))
      );
      if($type == 'flight'){
        $flight_itinerary_code = $itinerary_code;
        $code_check_data['itinerary_code'] = $itinerary_code;
      }
      $code_check_data['type'] = $type;
      $code_check_data['email'] = $email;
      $code_check_data['message'] = trim($message);
      if($type == 'flight'){
        $date_expire = $_POST['departure_date'] = isset($data_decoded_hash['departure_date']) ? $data_decoded_hash['departure_date'] : null;
        $this->form_validation->set_rules('departure_date', 'Data plecare', 'required|valid_date[Y-m-d]',array(
          'valid_date' => 'Data plecare invalida',
        ));
        $_POST['return_date'] = isset($data_decoded_hash['return_date']) ? $data_decoded_hash['return_date'] : null;
        $this->form_validation->set_rules('return_date', 'Data retur', 'required|valid_date[Y-m-d]',array(
          'valid_date' => 'Data retur invalida',
        ));
        $data_expire = $_POST['departure_date'];
      }
      
      $_POST['passengers_adult'] = isset($data_decoded_hash['passengers_adult']) && ((int)$data_decoded_hash['passengers_adult']>0) ? (int)$data_decoded_hash['passengers_adult'] : 0;
      $_POST['passengers_senior'] = isset($data_decoded_hash['passengers_senior']) && ((int)$data_decoded_hash['passengers_senior']>0) ? (int)$data_decoded_hash['passengers_senior'] : 0;
      $_POST['passengers_youth'] = isset($data_decoded_hash['passengers_youth']) && ((int)$data_decoded_hash['passengers_youth']>0) ? (int)$data_decoded_hash['passengers_youth'] : 0;
      $_POST['passengers_child'] = isset($data_decoded_hash['passengers_child']) && ((int)$data_decoded_hash['passengers_child']>0) ? (int)$data_decoded_hash['passengers_child'] : 0;
      $_POST['passengers_infant_lap'] = isset($data_decoded_hash['passengers_infant_lap']) && ((int)$data_decoded_hash['passengers_infant_lap']>0) ? (int)$data_decoded_hash['passengers_infant_lap'] : 0;
      $_POST['passengers_infant_seat'] = isset($data_decoded_hash['passengers_infant_seat']) && ((int)$data_decoded_hash['passengers_infant_seat']>0) ? (int)$data_decoded_hash['passengers_infant_seat'] : 0;
      $_POST['children'] = $_POST['passengers_youth'] + $_POST['passengers_child'] + $_POST['passengers_infant_lap'] + $_POST['passengers_infant_seat'];
      $this->form_validation->set_rules('passengers_adult', 'Adulti', 'required|is_numeric|is_greater_than_or_equal_to[1]|is_less_than_or_equal_to[6]',array(
        'is_numeric' => 'Nr persoane invalid',
        'is_greater_than_or_equal_to' => 'Nu ati introdus adulti',
        'is_less_than_or_equal_to' => 'Prea multi adulti',
      ));
      $this->form_validation->set_rules('passengers_senior', 'Seniori', 'required|is_numeric|is_greater_than_or_equal_to[0]|is_less_than_or_equal_to[6]',array(
        'is_numeric' => 'Nr persoane invalid',
        'is_greater_than_or_equal_to' => 'Nr invalid de seniori',
        'is_less_than_or_equal_to' => 'Prea multi seniori',
      ));
      $this->form_validation->set_rules('children', 'Copii', 'required|is_numeric|is_greater_than_or_equal_to[0]|is_less_than_or_equal_to[2]',array(
        'is_numeric' => 'Nr persoane invalid',
        'is_greater_than_or_equal_to' => 'Nr invalid de copii',
        'is_less_than_or_equal_to' => 'Prea multi copii',
      ));
      
      $flight_data = $data_decoded_hash;
    }
    $code = md5(json_encode($code_check_data));
    $_POST['code'] = $code;
   
    if ($this->form_validation->run() == FALSE) {
      $this->data['errors'] = $this->form_validation->error_array();
      $this->outputError($this->form_validation->error_string());
    }
    $time_created = date('Y-m-d H:i:s');
    $status = 1;


    if($newsletter){
      if($this->user->id && ($this->user->email === $email)){
        $user_data = array();
        $user_data['user_id'] = $this->user->id;
        $user_data['newsletter'] = 1;
        $this->db->where('user_id', $user_data['user_id']);
        $this->db->update('ac_user', $user_data);
      } else {
        $this->db->where('user_email', $email);
        $q = $this->db->get('ac_user');
        $existing_user = $q->row();
        /* if($existing_user){
          $this->addMessage('Este necesar sa va conectati cu acest utilizator pentru a va abona.', 'error');
          if ($this->input->is_ajax_request()) {
            $this->output('error');
          }
          $this->redirect('');
        } */
      }
      $data = array();
      $data['email'] = $email;
      $data['user_id'] = 0;
      $data['status'] = 1;
      $data['time_created'] = date('Y-m-d H:i:s');
      
      $sql = $this->db->insert_string('ac_newsletter', $data) . " ON DUPLICATE KEY UPDATE `status` = VALUES(`status`)";
      $this->db->query($sql);
      
      $this->load->model('WhiteImage_model');
      $search = array(
        'email|' . $email . '|1'
      );
      $return_fields = 'all';
      $response = $this->WhiteImage_model->select_one($search,$return_fields);
      if($response){
        $response_decoded = json_decode($response);
        if($response_decoded){
          if($response_decoded->count && ($response_decoded->subscriber->subscribe_status=='no')){
            $emailid = $response_decoded->subscriber->emailid;
            $this->WhiteImage_model->resubscribe($emailid);
          } else {
            $data = array();
            $data['email'] = $email;
            $data['sursa'] = 'AccentTravel&Events';
            $response = $this->WhiteImage_model->save($data);
          }
        } 
      }
      Modules :: run ('Mailer/newsletter_subscribe', array(
        'to'=>$email,
      ));
    }
    // $this->outputError('block');
    
    $data = array();
    $data['title'] = $title;
    $data['fullname'] = $fullname;
    $data['email'] = $email;
    $data['phone'] = $phone;
    $data['type'] = $type;
    $data['hotel_id'] = $hotel_id;
    $data['package_id'] = $package_id;
    $data['flight_itinerary_code'] = $flight_itinerary_code;
    $data['amount'] = $amount;
    $data['amount_hotel'] = $amount_hotel;
    $data['amount_package'] = $amount_package;
    $data['amount_flight'] = $amount_flight;
    $data['currency'] = $currency;
    $data['data_hotel'] = $hotel_data ? json_encode($hotel_data) : null;
    $data['data_package'] = $package_data ? json_encode($package_data) : null;
    $data['data_flight'] = $flight_data ? json_encode($flight_data) : null;
    $data['time_created'] = $time_created;
    $data['date_expire'] = $date_expire;
    $data['code'] = $code;
    $data['hash_hotel'] = $hotel_data ? md5(json_encode($hotel_data)) : null;
    $data['hash_package'] = $package_data ? md5(json_encode($package_data)) : null;
    $data['hash_flight'] = $flight_data ? md5(json_encode($flight_data)) : null;
    $data['status'] = $status;
    $data['message'] = $message;
    $this->db->insert('trip_request_offer', $data);
    
    Modules :: run ('Mailer/trip_requestoffer', array('to'=>$email, 'maildata'=>$data));
    
    $this->addMessage('Cererea de oferta a fost trimisa');
    $this->output();
    exit;
  }
}