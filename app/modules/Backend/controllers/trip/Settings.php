<?php
if (!defined('BASEPATH'))
  exit('No direct script access allowed');

class Settings extends MX_Controller {
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
    $settings = $this->Options_model->get('trip_settings',null,array(
      'trip_app_id'=>null,
      'trip_app_secret'=>null,
      'trip_username'=>null,
      'trip_password'=>null,
      'trip_endpoint'=>null,
    ));
    if(!$settings){
      $settings = array();
    }
    $this->load->library('encryption');
    if(isset($settings['trip_app_id'])){
      $settings['trip_app_id'] = $this->encryption->decrypt($settings['trip_app_id']);
    }
    if(isset($settings['trip_app_secret'])){
      $settings['trip_app_secret'] = $this->encryption->decrypt($settings['trip_app_secret']);
    }
    if(isset($settings['trip_username'])){
      $settings['trip_username'] = $this->encryption->decrypt($settings['trip_username']);
    }
    if(isset($settings['trip_password'])){
      $settings['trip_password'] = $this->encryption->decrypt($settings['trip_password']);
    }
    if(isset($settings['trip_endpoint'])){
      $settings['trip_endpoint'] = $this->encryption->decrypt($settings['trip_endpoint']);
    }
    $this->data = $settings;
    $this->theme->view('backend/trip/settings', $this->data);
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
    $status = $this->input->post('trip_status') ? 1 : 0;
    
    $this->form_validation->set_rules('trip_status', 'Status', 'in_list[0,1]');
    $this->form_validation->set_rules('trip_app_id', 'App ID', 'trim' . ($status ? '|required' : ''));
    $this->form_validation->set_rules('trip_app_secret', 'App Secret', 'trim' . ($status ? '|required' : ''));
    $this->form_validation->set_rules('trip_endpoint', 'Endpoint URL', 'trim' . ($status ? '|required' : ''));
    
    $this->load->model('Options_model');
    $settings = $this->Options_model->get('trip_settings',null);
    if(!$settings){
      $settings = array();
    }
    $existing_username_or_password = isset($settings['trip_username']) || isset($settings['trip_password']);
    
    $trip_username = trim($this->input->post('trip_username'));
    $trip_password = trim($this->input->post('trip_password'));
    
    $username_password_required = strlen($trip_username) || $trip_password || !$existing_username_or_password;
    
    $this->form_validation->set_rules('trip_username', 'Username', 'trim' . ($username_password_required ? '|required' : ''));
    $this->form_validation->set_rules('trip_password', 'Parola', 'trim' . ($username_password_required ? '|required' : ''));
    
    if ($this->form_validation->run() == FALSE) {
      $this->data['errors'] = $this->form_validation->error_array();
      $this->outputError($this->form_validation->error_string());
    }
    
    $this->load->library('encryption');
    $data = array();
    $data['trip_app_id'] = trim($this->input->post('trip_app_id'));
    if(!strlen($data['trip_app_id'])){
      $data['trip_app_id'] = null;
    } else {
      $data['trip_app_id'] = $this->encryption->encrypt($data['trip_app_id']);
    }
    $data['trip_app_secret'] = trim($this->input->post('trip_app_secret'));
    if(!strlen($data['trip_app_secret'])){
      $data['trip_app_secret'] = null;
    } else {
      $data['trip_app_secret'] = $this->encryption->encrypt($data['trip_app_secret']);
    }
    $data['trip_username'] = trim($this->input->post('trip_username'));
    if(!strlen($data['trip_username'])){
      $data['trip_username'] = null;
    } else {
      $data['trip_username'] = $this->encryption->encrypt($data['trip_username']);
    }
    $data['trip_password'] = trim($this->input->post('trip_password'));
    if(!strlen($data['trip_password'])){
      $data['trip_password'] = null;
    } else {
      $data['trip_password'] = $this->encryption->encrypt($data['trip_password']);
    }
    $data['trip_endpoint'] = trim($this->input->post('trip_endpoint'));
    if(!strlen($data['trip_endpoint'])){
      $data['trip_endpoint'] = null;
    } else {
      $data['trip_endpoint'] = $this->encryption->encrypt($data['trip_endpoint']);
    }
    
    $this->Options_model->set('trip_settings',$data);
    
    $this->addMessage('Informatiile au fost salvate','success');
    $this->output();
  }
}