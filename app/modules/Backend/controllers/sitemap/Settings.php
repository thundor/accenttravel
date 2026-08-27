<?php
if (!defined('BASEPATH'))
  exit('No direct script access allowed');

class Settings extends MX_Controller {
  public function index() {
    if(!$this->user->can('backend-access')){
      $this->redirect('backend','Acces restrictionat','error');
    }
    if(!$this->user->can('backend-config-access')){
      $this->redirect('backend','Acces restrictionat','error');
    }
    $this->load->model('Options_model');
    $settings = $this->Options_model->get('sitemap_settings',null,array(
      'hotels'=>'',
      'flights'=>'',
      'citybreaks'=>'',
      'packages'=>'',
    ));
    if(!$settings){
      $settings = array();
    }
    $this->data = $settings;
    $this->theme->view('backend/sitemap/settings', $this->data);
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
    $status = (int) $this->input->post('payu_status');
    
    $this->form_validation->set_rules('hotels', 'Alias hoteluri', 'trim|regex_match[/^[a-z0-9_\-,]+$/i]');
    $this->form_validation->set_rules('citybreaks', 'Alias citybreakuri', 'trim|regex_match[/^[a-z0-9_\-,]+$/i]');
    $this->form_validation->set_rules('flights', 'Alias zboruri', 'trim|regex_match[/^[a-z0-9_\-,]+$/i]');
    $this->form_validation->set_rules('packages', 'Alias pachete', 'trim|regex_match[/^[a-z0-9_\-,]+$/i]');
    
    if ($this->form_validation->run() == FALSE) {
      $this->data['errors'] = $this->form_validation->error_array();
      $this->outputError($this->form_validation->error_string());
    }
    
    $data = array();
    foreach(array('hotels', 'citybreaks', 'flights', 'packages') as $item){
      $data[$item] = $this->input->post($item);
      if(!isset($data[$item]) || !strlen(trim($data[$item]))){
        $data[$item] = null;
      } else {
        $data[$item] = trim($data[$item],' ,');
      }
    }
    $data['payu_secret_key'] = trim($this->input->post('payu_secret_key'));
    if(!strlen($data['payu_secret_key'])){
      $data['payu_secret_key'] = null;
    }
    
    $this->load->model('Options_model');
    $this->Options_model->set('sitemap_settings',$data);
    $this->addMessage('Informatiile au fost salvate','success');
    $this->output();
  }
}