<?php
if (!defined('BASEPATH'))
  exit('No direct script access allowed');

class Online extends MX_Controller {
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
    $settings = $this->Options_model->get('payment_methods_settings',null,array(
      'online_text'=>'',
    ));
    if(!$settings){
      $settings = array();
    }
    $settings['online_status'] = (int)$this->Options_model->get('payment_methods_status','online',0);
    
    $this->data = $settings;
    $this->theme->view('backend/payment_methods/online', $this->data);
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
    $status = (int) $this->input->post('online_status');
    
    $this->form_validation->set_rules('online_status', 'Status', 'in_list[0,1,-1]');
    $this->form_validation->set_rules('online_text', 'Text', 'trim' . ($status ? '|required' : ''));
    
    if ($this->form_validation->run() == FALSE) {
      $this->data['errors'] = $this->form_validation->error_array();
      $this->outputError($this->form_validation->error_string());
    }
    
    $data = array();
    $data['online_text'] = trim($this->input->post('online_text'));
    if(!strlen($data['online_text'])){
      $data['online_text'] = null;
    }
    
    $this->load->model('Options_model');
    $this->Options_model->set('payment_methods_settings',$data);
    $this->Options_model->setValue('payment_methods_status','online',(int)$status);
    
    $this->addMessage('Informatiile au fost salvate','success');
    $this->output();
  }
}