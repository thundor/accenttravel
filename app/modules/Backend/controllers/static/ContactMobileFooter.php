<?php
if (!defined('BASEPATH'))
  exit('No direct script access allowed');

class ContactMobileFooter extends MX_Controller {
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
    $settings = $this->Options_model->get('static_contact_mobile_footer',null,array(
      'contact_phone_number'=>'',
      'contact_text'=>'',
      'whatsapp_phone_number'=>'',
      'whatsapp_text'=>'',
    ));
    if(!$settings){
      $settings = array();
    }
    $settings['statuses'] = $this->Options_model->get('static_contact_mobile_footer_status',null,array(
      'contact'=>null,
      'whatsapp'=>null,
    ));
    foreach($settings['statuses'] as $k => $v){
      $settings[$k . '_status'] = $v;
    }
    
    $this->data = $settings;
    $this->theme->view('backend/static/contact_mobile_footer', $this->data);
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
    $contact_status = $this->input->post('contact_status') ? 1 : 0;
    $whatsapp_status = $this->input->post('whatsapp_status') ? 1 : 0;
    
    $this->form_validation->set_rules('contact_status', 'Contact Status', 'in_list[0,1]');
    $this->form_validation->set_rules('whatsapp_status', 'Whatsapp Status', 'in_list[0,1]');
    $this->form_validation->set_rules('contact_phone_number', 'Contact phone number', 'trim' . ($contact_status ? '|required' : ''));
    $this->form_validation->set_rules('whatsapp_phone_number', 'Whatsapp phone number', 'trim' . ($whatsapp_status ? '|required' : ''));
    
    if ($this->form_validation->run() == FALSE) {
      $this->data['errors'] = $this->form_validation->error_array();
      $this->outputError($this->form_validation->error_string());
    }
    
    $data = array();
    $data['contact_phone_number'] = trim($this->input->post('contact_phone_number'));
    if(!strlen($data['contact_phone_number'])){
      $data['contact_phone_number'] = null;
    }
    $data['whatsapp_phone_number'] = trim($this->input->post('whatsapp_phone_number'));
    if(!strlen($data['whatsapp_phone_number'])){
      $data['whatsapp_phone_number'] = null;
    }
    $data['contact_text'] = $this->input->post('contact_text');
    if(!strlen($data['contact_text'])){
      $data['contact_text'] = null;
    }
    $data['whatsapp_text'] = $this->input->post('whatsapp_text');
    if(!strlen($data['whatsapp_text'])){
      $data['whatsapp_text'] = null;
    }
    
    $this->load->model('Options_model');
    $this->Options_model->set('static_contact_mobile_footer',$data);
    $this->Options_model->set('static_contact_mobile_footer_status',array(
      'contact' => $contact_status ? 1 : null,
      'whatsapp' => $whatsapp_status ? 1 : null,
    ));
    
    $this->addMessage('Informatiile au fost salvate','success');
    $this->output();
  }
}