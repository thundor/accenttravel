<?php
if (!defined('BASEPATH'))
  exit('No direct script access allowed');

class Flights extends MX_Controller {
  function __construct() {
    $this->load->model('Trip/Flights_model');
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
      $this->data = $this->Flights_model->getSearchData('/backend/order');
      
      $this->load->library('form_validation');
      $this->form_validation->set_message('validate_positive_int_strict', 'Informatii invalide');
      $this->form_validation->set_message('validate_positive_int', 'Informatii invalide');
      
      $this->form_validation->set_rules('departure_date', 'Data plecare', 'trim|required|max_length[10]|valid_date[d.m.Y]',array(
        'valid_date' => 'Format invalid al datei de plecare',
      ));
      $this->form_validation->set_rules('return_date', 'Data retur', 'trim|max_length[10]|valid_date[d.m.Y]',array(
        'valid_date' => 'Format invalid al datei de retur',
      ));
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
      if($return_date){
        $this->data['return_date'] = DateTime::createFromFormat('d.m.Y', trim($return_date))->format('Y-m-d');
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
      
      if ($return) {
        return;
      }
      $this->output();
    }
    $this->redirect('backend', 'Acces invalid', 'error');
  }
}