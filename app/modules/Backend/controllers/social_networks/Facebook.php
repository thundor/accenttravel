<?php
if (!defined('BASEPATH'))
  exit('No direct script access allowed');

class Facebook extends MX_Controller {
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
    $settings = $this->Options_model->get('social_networks_settings',null,array(
      'facebook_app_id'=>'',
      'facebook_app_secret'=>'',
    ));
    if(!$settings){
      $settings = array();
    }
    $settings['facebook_status'] = $this->Options_model->get('social_networks_status','fb',false);
    
    $this->data = $settings;
    $this->theme->view('backend/social_networks/facebook', $this->data);
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
    $status = $this->input->post('facebook_status') ? 1 : 0;
    
    $this->form_validation->set_rules('facebook_status', 'Status', 'in_list[0,1]');
    $this->form_validation->set_rules('facebook_app_id', 'App ID', 'trim' . ($status ? '|required' : ''));
    $this->form_validation->set_rules('facebook_app_secret', 'App Secret', 'trim' . ($status ? '|required' : ''));
    
    if ($this->form_validation->run() == FALSE) {
      $this->data['errors'] = $this->form_validation->error_array();
      $this->outputError($this->form_validation->error_string());
    }
    
    $data = array();
    $data['facebook_app_id'] = trim($this->input->post('facebook_app_id'));
    if(!strlen($data['facebook_app_id'])){
      $data['facebook_app_id'] = null;
    }
    $data['facebook_app_secret'] = trim($this->input->post('facebook_app_secret'));
    if(!strlen($data['facebook_app_secret'])){
      $data['facebook_app_secret'] = null;
    }
    
    $this->load->model('Options_model');
    $this->Options_model->set('social_networks_settings',$data);
    $this->Options_model->setValue('social_networks_status','fb',$status ? true : null);
    
    $this->addMessage('Informatiile au fost salvate','success');
    $this->output();
  }
}