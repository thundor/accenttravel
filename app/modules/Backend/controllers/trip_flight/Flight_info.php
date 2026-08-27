<?php
if (!defined('BASEPATH'))
  exit('No direct script access allowed');

class Flight_Info extends MX_Controller {
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
    $settings = $this->Options_model->get('trip_flight_info',null,array(
      'status'=>0,
      // 'title'=>'',
      'description'=>'',
      // 'insurance1_title'=>'',
      'insurance1_desc'=>'',
      // 'insurance2_title'=>'',
      'insurance2_desc'=>'',
    ));
    if(!$settings){
      $settings = array();
    }
    $this->data = $settings;
    $this->theme->view('backend/trip/flight/info', $this->data);
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
    
    $this->load->library('form_validation');
    $status = (int) $this->input->post('status');
    
    $this->form_validation->set_rules('status', 'Status', 'in_list[0,1]');
    // $this->form_validation->set_rules('title', 'Titlu', 'trim' . ($status ? '|required' : ''));
    $this->form_validation->set_rules('description', 'Descriere', 'trim' . ($status ? '|required' : ''));
    // $this->form_validation->set_rules('insurance1_title', 'Titlu asigurare turist 1', 'trim' . ($status ? '|required' : ''));
    $this->form_validation->set_rules('insurance1_desc', 'Descriere asigurare turist 1', 'trim' . ($status ? '|required' : ''));
    // $this->form_validation->set_rules('insurance2_title', 'Titlu asigurare turist 2', 'trim' . ($status ? '|required' : ''));
    $this->form_validation->set_rules('insurance2_desc', 'Descriere asigurare turist 2', 'trim' . ($status ? '|required' : ''));
    
    if ($this->form_validation->run() == FALSE) {
      $this->data['errors'] = $this->form_validation->error_array();
      $this->outputError($this->form_validation->error_string());
    }
    
    $data = array();
    // $data['title'] = trim($this->input->post('title'));
    // if(!strlen($data['title'])){
      // $data['title'] = null;
    // }
    $data['description'] = trim($this->input->post('description'));
    if(!strlen($data['description'])){
      $data['description'] = null;
    }
    // $data['insurance1_title'] = trim($this->input->post('insurance1_title'));
    // if(!strlen($data['insurance1_title'])){
      // $data['insurance1_title'] = null;
    // }
    $data['insurance1_desc'] = trim($this->input->post('insurance1_desc'));
    if(!strlen($data['insurance1_desc'])){
      $data['insurance1_desc'] = null;
    }
    // $data['insurance2_title'] = trim($this->input->post('insurance2_title'));
    // if(!strlen($data['insurance2_title'])){
      // $data['insurance2_title'] = null;
    // }
    $data['insurance2_desc'] = trim($this->input->post('insurance2_desc'));
    if(!strlen($data['insurance2_desc'])){
      $data['insurance2_desc'] = null;
    }
    $data['status'] = $status ? 1 : null;
    
    $this->load->model('Options_model');
    $this->Options_model->set('trip_flight_info',$data);
    $this->addMessage('Informatiile au fost salvate','success');
    $this->output();
  }
}