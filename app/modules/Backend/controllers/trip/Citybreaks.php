<?php
if (!defined('BASEPATH'))
  exit('No direct script access allowed');

class Citybreaks extends MX_Controller {
  function __construct() {
    $this->load->model('Trip/Hotels_model');
    $this->load->model('Trip/Flights_model');
    $this->load->model('Trip/Citybreaks_model');
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
      $hotel_data = $this->Hotels_model->getSearchDefaultData();
      $flight_data = $this->Flights_model->getSearchDefaultData();
      $citybreak_data = $this->Citybreaks_model->getSearchDefaultData();
      $this->data = array_replace_recursive($hotel_data, $flight_data, $citybreak_data);

      $this->load->library('form_validation');
      $this->form_validation->set_message('validate_positive_int_strict', 'Informatii invalide');
      $this->form_validation->set_message('validate_positive_int', 'Informatii invalide');
      
      $this->form_validation->set_rules('departure_date', 'Data plecare', 'trim|required|max_length[10]|valid_date[d.m.Y]',array(
        'valid_date' => 'Format invalid al datei de plecare',
      ));
      $this->form_validation->set_rules('return_date', 'Data retur', 'trim|max_length[10]|valid_date[d.m.Y]',array(
        'valid_date' => 'Format invalid al datei de retur',
      ));
      
      $this->form_validation->set_rules('search_type', 'Tip cautare', 'required|in_list[flight,hotel,package]', array(
        'in_list' => 'Submit invalid',
      ));
      
      $search_type = $this->input->post('search_type');
      if($search_type == 'hotel'){
        $start_date = $this->input->post('start_date');
        $end_date = $this->input->post('end_date');
        if(!isset($start_date) || !isset($end_date)){
          $this->outputError('Cautati un zbor intai, pentru a determina datele de Check-in/out');
        }
        $this->form_validation->set_rules('start_date', 'Checkin', 'trim|required|max_length[10]|valid_date[d.m.Y]',array(
          'valid_date' => 'Formatul datei Checkin este invalid',
        ));
        $this->form_validation->set_rules('end_date', 'Checkout', 'trim|required|max_length[10]|valid_date[d.m.Y]',array(
          'valid_date' => 'Formatul datei Checkout este invalid',
        )); 
      }
      
      $this->form_validation->set_rules('origin_full_location_name', 'Locatie plecare', 'trim|required|max_length[255]');
      $this->form_validation->set_rules('origin_location_name', 'Locatie plecare', 'trim|max_length[255]');
      $this->form_validation->set_rules('origin_location_id', 'Locatie plecare', 'validate_positive_int');
      $this->form_validation->set_rules('origin_city_name', 'Oras plecare', 'trim|required|max_length[255]');
      $this->form_validation->set_rules('origin_city_id', 'Oras plecare', 'required|validate_positive_int_strict');
      $this->form_validation->set_rules('origin_country_id', 'Tara plecare', 'required|validate_positive_int_strict');
      $this->form_validation->set_rules('origin_country_name', 'Tara plecare', 'trim|max_length[255]');
      
      $this->form_validation->set_rules('destination_full_location_name', 'Locatie sosire', 'trim|required|max_length[255]');
      $this->form_validation->set_rules('destination_location_name', 'Locatie sosire', 'trim|max_length[255]');
      $this->form_validation->set_rules('destination_location_id', 'Locatie sosire', 'validate_positive_int');
      $this->form_validation->set_rules('destination_city_name', 'Oras sosire', 'trim|required|max_length[255]');
      $this->form_validation->set_rules('destination_city_id', 'Oras sosire', 'required|validate_positive_int_strict');
      $this->form_validation->set_rules('destination_country_id', 'Tara sosire', 'required|validate_positive_int_strict');
      $this->form_validation->set_rules('destination_country_name', 'Tara sosire', 'trim|max_length[255]');
      
      $this->form_validation->set_rules('cabine_type', 'Clasa de zbor', 'required|in_list[1,2,3,4]', array(
        'in_list' => 'Alegere invalida Clasa de zbor',
      ));
      $this->form_validation->set_rules('direct_only', 'Fara escale', 'in_list[1]', array(
        'in_list' => 'Alegere invalida Fara escale',
      ));
      $this->form_validation->set_rules('flex_dates', 'Date flexibile', 'in_list[1]', array(
        'in_list' => 'Alegere invalida Date flexibile',
      ));
      
      $this->form_validation->set_rules('passengers_adult', 'Adulti', 'validate_positive_int', array(
        'validate_positive_int' => 'Numar invalid de Adulti',
      ));
      $this->form_validation->set_rules('passengers_senior', 'Seniori', 'validate_positive_int', array(
        'validate_positive_int' => 'Numar invalid de Seniori',
      ));
      $this->form_validation->set_rules('passengers_child', 'Copii', 'validate_positive_int', array(
        'validate_positive_int' => 'Numar invalid de Copii',
      ));
      $this->form_validation->set_rules('passengers_infant_lap', 'Bebelusi in brate', 'validate_positive_int', array(
        'validate_positive_int' => 'Numar invalid de Bebelusi in brate',
      ));
      $this->form_validation->set_rules('passengers_infant_seat', 'Bebelusi in scaun', 'validate_positive_int', array(
        'validate_positive_int' => 'Numar invalid de Bebelusi in scaun',
      ));
      
      $adults = $this->input->post('passengers_adult');
      $seniors = $this->input->post('passengers_senior');
      
      if((int)$adults + (int)$seniors <=0){
        $this->outputError('Introduceti cel putin un Adult/Senior');
      }
      $reference_date = false;
      $departure_date = $this->input->post('departure_date');
      if(isset($departure_date)){
        $date_reference_date = DateTime::createFromFormat('d.m.Y', trim($departure_date));
        if ($date_reference_date && $date_reference_date->format('d.m.Y') == $departure_date) {
          $reference_date = $date_reference_date;
        }
      }
      
      $occupancy = $this->input->post('occupancy');
      $persons = $this->input->post('persons');
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
            $room_occupancy[$room_index]['chd']=array(
              'age'=>array(),
              'birth_date'=>array(),
            );
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
              $room_occupancy[$room_index]['chd']['age'][$child_age_index] = $child_age + 1;
              if(false !== $reference_date){
                $room_occupancy[$room_index]['chd']['birth_date'][$child_age_index] = $birth_date;
              }
            }
          }
        }
      } else {
        $this->outputError('Nu ati introdus camere');
      }
      
      $origin_hash = $this->input->post('origin_location_id') . '-' . $this->input->post('origin_city_id');
      $destination_hash = $this->input->post('destination_location_id') . '-' . $this->input->post('destination_city_id');
      
      if($origin_hash === $destination_hash){
        $this->outputError('Locatiile Plecare - Sosire trebuie sa difere');
      }
      
      if($this->form_validation->run() == FALSE){
        $this->data['errors'] = $this->form_validation->error_array();
        $this->outputError($this->form_validation->error_string());
      }
      
      
      $departure_date = trim($this->input->post('departure_date'));
      $this->data['departure_date'] = DateTime::createFromFormat('d.m.Y', $departure_date)->format('Y-m-d');
      
      $return_date = $this->input->post('return_date');
      $this->data['return_date'] = '';
      if($return_date){
        $this->data['return_date'] = DateTime::createFromFormat('d.m.Y', trim($return_date))->format('Y-m-d');
      }
      $start_date = $this->input->post('start_date');
      $this->data['start_date'] = '';
      if($start_date){
        $this->data['start_date'] = DateTime::createFromFormat('d.m.Y', trim($start_date))->format('Y-m-d');
      }
      $end_date = $this->input->post('end_date');
      $this->data['end_date'] = '';
      if($end_date){
        $this->data['end_date'] = DateTime::createFromFormat('d.m.Y', trim($end_date))->format('Y-m-d');
      }
      
      $go_only = $this->input->post('go_only');
      $this->data['go_only'] = filter_var($go_only, FILTER_VALIDATE_BOOLEAN);
      
      $direct_only = $this->input->post('direct_only');
      $this->data['direct_only'] = filter_var($direct_only, FILTER_VALIDATE_BOOLEAN);
      
      $flex_dates = $this->input->post('flex_dates');
      $this->data['flex_dates'] = filter_var($flex_dates, FILTER_VALIDATE_BOOLEAN);
      $this->data['flexible_dates'] = filter_var($flex_dates, FILTER_VALIDATE_BOOLEAN);
      
      $this->data['origin_full_location_name'] = trim($this->input->post('origin_full_location_name'));
      $this->data['origin_location_name'] = trim($this->input->post('origin_location_name'));
      $this->data['origin_location_id'] = trim($this->input->post('origin_location_id'));
      $this->data['origin_city_name'] = trim($this->input->post('origin_city_name'));
      $this->data['origin_city_id'] = trim($this->input->post('origin_city_id'));
      $this->data['origin_country_id'] = trim($this->input->post('origin_country_id'));
      $this->data['origin_country_name'] = trim($this->input->post('origin_country_name'));
      
      $this->data['destination_full_location_name'] = trim($this->input->post('destination_full_location_name'));
      $this->data['destination_location_name'] = trim($this->input->post('destination_location_name'));
      $this->data['destination_location_id'] = trim($this->input->post('destination_location_id'));
      $this->data['destination_city_name'] = trim($this->input->post('destination_city_name'));
      $this->data['destination_city_id'] = trim($this->input->post('destination_city_id'));
      $this->data['destination_country_id'] = trim($this->input->post('destination_country_id'));
      $this->data['destination_country_name'] = trim($this->input->post('destination_country_name'));
      
      // $this->data['ignore_session'] = 1;
      $this->data['session'] = '/backend/order';
      $this->data['occupancy'] = $room_occupancy;
      $this->data['persons'] = $persons;
      
      $passengers_adult = (int) ($this->input->post('passengers_adult'));
      if ($passengers_adult>0) {
        $this->data['passengers_adult'] = $passengers_adult;
      }
      $passengers_senior = (int) ($this->input->post('passengers_senior'));
      if ($passengers_senior>=0) {
        $this->data['passengers_senior'] = $passengers_senior;
      }
      $passengers_youth = (int) ($this->input->post('passengers_youth'));
      if ($passengers_youth>=0) {
        $this->data['passengers_youth'] = $passengers_youth;
      }
      $passengers_child = (int) ($this->input->post('passengers_child'));
      if ($passengers_child>=0) {
        $this->data['passengers_child'] = $passengers_child;
      }
      $passengers_infant_lap = (int) ($this->input->post('passengers_infant_lap'));
      if ($passengers_infant_lap>=0) {
        $this->data['passengers_infant_lap'] = $passengers_infant_lap;
      }
      $passengers_infant_seat = (int) ($this->input->post('passengers_infant_seat'));
      if ($passengers_infant_seat>=0) {
        $this->data['passengers_infant_seat'] = $passengers_infant_seat;
      }
      $this->Citybreaks_model->setSearchData($this->data);
      
      $this->data['session_data'] = $this->Citybreaks_model->getSearchData(0, '/backend/order');
      
      if ($return) {
        return;
      }
      $this->output();
    }
    $this->redirect('backend', 'Acces invalid', 'error');
  }
}