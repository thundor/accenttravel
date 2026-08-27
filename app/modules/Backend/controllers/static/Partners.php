<?php
if (!defined('BASEPATH'))
  exit('No direct script access allowed');

class Partners extends MX_Controller {
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
    $settings = $this->Options_model->get('static_partners_settings');
    if(!$settings){
      $settings = array();
    }
    
    $this->data = $settings;
    $this->theme->view('backend/static/partners', $this->data);
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
    
    $post = $this->input->post('data');
    $images = isset($_FILES['image']) ? $_FILES['image'] : array();
    $data = array(
      'status' => array(),
      'title' => array(),
      'image' => array(),
      'url' => array(),
    );
    
    $statuses = isset($post['status']) ? $post['status'] : array();
    $k = -1;
    foreach($statuses as $i => $status){
      $k++;
      $data['status'][$k] = $status = isset($post['status'][$i]) ? $post['status'][$i] : null;
      $data['title'][$k] = $title = isset($post['title'][$i]) ? trim($post['title'][$i]) : null;
      $data['image'][$k] = $image = isset($post['image'][$i]) ? $post['image'][$i] : null;
      $data['url'][$k] = $url = isset($post['url'][$i]) ? $post['url'][$i] : null;
      
      $fake_post_prefix = 'zone_' . $i . '_';
      $_POST[$fake_post_prefix . 'status'] = $status;
      $_POST[$fake_post_prefix . 'title'] = $title;
      $_POST[$fake_post_prefix . 'image'] = $image;
      $this->form_validation->set_rules($fake_post_prefix . 'status', 'Zona ' . ($k + 1) . ' Status', 'in_list[0,1]');
      $this->form_validation->set_rules($fake_post_prefix . 'image', 'Zona ' . ($k + 1) . ' Imagine', 'trim' . ($status ? '|required' : '') . '');
      $this->form_validation->set_rules($fake_post_prefix . 'title', 'Zona ' . ($k + 1) . ' Titlu', 'trim|max_length[255]');
    }
    
    if ($this->form_validation->run() == FALSE) {
      $this->addMessage($this->form_validation->error_string(),'error');
      $this->saveMessagesInSession();
      $this->data = $data;
      return $this->theme->view('backend/static/partners', $this->data);
    }
    
    $this->load->model('Options_model');
    
    $this->Options_model->set('static_partners_settings',$data);
    $this->redirect('backend/static/partners', 'Informatiile au fost salvate', 'success');
  }
}