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
    $settings = $this->Options_model->get('general_settings',null,array(
      'contact_phone_number'=>null,
      'contact_phone_text'=>null,
      'dont_show_home_popup'=>null,
      'newux_version'=>null,
    ));
	if(is_file(FCPATH . '/newux_version.txt')){
		$settings['newux_version'] = file_get_contents(FCPATH . '/newux_version.txt');
	}
    if(!$settings){
      $settings = array();
    }
    $this->load->library('encryption');
    $this->data = $settings;
    $this->theme->view('backend/general/settings', $this->data);
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
    $should_validate = false;
    
    if ($should_validate && $this->form_validation->run() == FALSE) {
      $this->data['errors'] = $this->form_validation->error_array();
      $this->outputError($this->form_validation->error_string());
    }
    
    $this->load->library('encryption');
    $data = array();
    $data['contact_phone_number'] = trim($this->input->post('contact_phone_number'));
    if(!strlen($data['contact_phone_number'])){
      $data['contact_phone_number'] = null;
    } else {
      $data['contact_phone_number'] = $data['contact_phone_number'];
    }
    $data['newux_version'] = trim($this->input->post('newux_version'));
    if(!strlen($data['newux_version'])){
      $data['newux_version'] = null;
    } else {
      $data['newux_version'] = $data['newux_version'];
    }
	
	file_put_contents(FCPATH . '/newux_version.txt', trim((string)$data['newux_version']));
	
    $data['contact_phone_text'] = $this->input->post('contact_phone_text');
    if(!strlen($data['contact_phone_text'])){
      $data['contact_phone_text'] = null;
    } else {
      $data['contact_phone_text'] = $data['contact_phone_text'];
    }
    $data['dont_show_home_popup'] = $this->input->post('dont_show_home_popup');
    if(!strlen($data['dont_show_home_popup'])){
      $data['dont_show_home_popup'] = null;
    } else {
      $data['dont_show_home_popup'] = 1;
    }
    $this->load->model('Options_model');
    $this->Options_model->set('general_settings',$data);
    
    $this->addMessage('Informatiile au fost salvate','success');
    $this->output();
  }
}