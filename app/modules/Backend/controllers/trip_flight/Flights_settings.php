<?php
if (!defined('BASEPATH'))
  exit('No direct script access allowed');

class Flights_Settings extends MX_Controller {
  function __construct() {
    parent :: __construct();
  }
  public function index() {
    if(!$this->user->can('backend-access')){
      $this->redirect('backend','Acces restrictionat','error');
    }
    if(!$this->user->can('backend-config-access')){
      $this->redirect('backend','Acces restrictionat','error');
    }
    $this->load->model('Options_model');
    $this->data = $this->Options_model->get('trip_flights_settings');
    $this->theme->view('backend/trip/flights_settings', $this->data);
  }
  public function save() {
    if(!$this->user->can('backend-access')){
      $this->outputError('Acces restrictionat');
    }
    if(!$this->user->can('backend-config-access')){
      $this->outputError('Acces restrictionat');
    }
    if(!$this->user->can('backend-config-save')){
      $this->outputError('Acces restrictionat');
    }
    if($this->validate()){
      $this->load->model('Options_model');
      $options = $this->input->post();
      $this->Options_model->set('trip_flights_settings',$options);
      $this->output();
    }
    $this->outputError('Invalid data');
  }
  protected function validate() {
    $this->load->library('form_validation');
    $keys = array('service');
    foreach($keys as $key){
      $this->form_validation->set_rules($key . '_price', 'Pret', 'numeric|greater_than_equal_to[0]');
    }
    if ($this->form_validation->run() == FALSE) {
      $this->outputError(validation_errors());
    }
    return true;
  }
}