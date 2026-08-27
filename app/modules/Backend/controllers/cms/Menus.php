<?php
if (!defined('BASEPATH'))
  exit('No direct script access allowed');

class Menus extends MX_Controller {
  function __construct() {
    parent :: __construct();
  }
  public function index($item = 'new') {
    if(!$this->user->can('backend-access')){
      $this->redirect('backend','Acces restrictionat','error');
    }
    if(!$this->user->can('backend-config-access')){
      $this->redirect('backend','Acces restrictionat','error');
    }
    $this->load->model('Options_model');
    $this->data['items'] = $this->Options_model->getKeys('trip_cms_menu');
    if(!in_array($item, $this->data['items'])){
      $this->data['items'][] = $item;
    }
    $this->data['item'] = $item;
    $this->data['menu'] = $this->Options_model->get('trip_cms_menu', $item);
    $this->theme->view('backend/cms/menus', $this->data);
  }
  public function save() {
    if(!$this->user->can('backend-access', 'backend-config-access', 'backend-config-save')){
      if ($this->input->is_ajax_request()) {
        $this->outputError('Invalid access');
      } else {
        $this->redirect('backend/cms/menus', 'Invalid access', 'error');
      }
    }
    $this->load->model('Options_model');
    $item = '' . $this->input->post('item');
    if (!strlen(trim($item))) {
      if ($this->input->is_ajax_request()) {
        $this->outputError('Nume de meniu invalid');
      } else {
        $this->redirect('backend/cms/menus', 'Nume de meniu invalid', 'error');
      }
    }
    $structure = $this->input->post('structure');
    $structure_decoded = json_decode($structure);
    if(!$structure_decoded){
      $structure_decoded = null;
    }
    $options = array(
      $item => $structure_decoded
    );
    if ($this->input->is_ajax_request()) {
      $this->addMessage('Validat cu succes');
      $this->output();
    }
    $this->Options_model->set('trip_cms_menu',$options);
    $message = 'Meniul a fost actualizat';
    $this->redirect('backend/cms/menus/'.$item, $message, 'success');
  }
}