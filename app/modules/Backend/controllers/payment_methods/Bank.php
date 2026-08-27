<?php
if (!defined('BASEPATH'))
  exit('No direct script access allowed');

class Bank extends MX_Controller {
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
      'bank_text'=>'',
    ));
    if(!$settings){
      $settings = array();
    }
    $settings['bank_status'] = (int)$this->Options_model->get('payment_methods_status','bank',0);
    
    $this->data = $settings;
    $this->theme->view('backend/payment_methods/bank', $this->data);
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
    $status = (int) $this->input->post('bank_status');
    
    $this->form_validation->set_rules('bank_status', 'Status', 'in_list[0,1,-1]');
    $this->form_validation->set_rules('bank_text', 'Text', 'trim' . ($status ? '|required' : ''));
    
    if ($this->form_validation->run() == FALSE) {
      $this->data['errors'] = $this->form_validation->error_array();
      $this->outputError($this->form_validation->error_string());
    }
    
    $data = array();
    $data['bank_text'] = trim($this->input->post('bank_text'));
    if(!strlen($data['bank_text'])){
      $data['bank_text'] = null;
    }
    
    $this->load->model('Options_model');
    $this->Options_model->set('payment_methods_settings',$data);
    $this->Options_model->setValue('payment_methods_status','bank',(int)$status);
    
    $this->addMessage('Informatiile au fost salvate','success');
    $this->output();
  }
}