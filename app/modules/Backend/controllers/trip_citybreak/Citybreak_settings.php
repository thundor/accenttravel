<?php
if (!defined('BASEPATH'))
  exit('No direct script access allowed');

class CityBreak_Settings extends MX_Controller {
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
    $this->data = $this->Options_model->get('trip_citybreak_settings');
    $this->theme->view('backend/trip/citybreak_settings', $this->data);
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
      $this->Options_model->set('trip_citybreak_settings',$options);
    }
    $this->addMessage('Informatiile au fost salvate','success');
    $this->output();
  }
  protected function validate() {
    return true;
    $this->load->library('form_validation');
    // $keys = array('travel','storno','service');
    // foreach($keys as $key){
      // $this->form_validation->set_rules($key . '_price', 'Pret', 'numeric|greater_than_equal_to[0]');
    // }
    if ($this->form_validation->run() == FALSE) {
      $this->outputError(validation_errors());
    }
    return true;
  }
}