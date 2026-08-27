<?php
if (!defined('BASEPATH'))
  exit('No direct script access allowed');

class Homepage extends MX_Controller {
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
    $settings = $this->Options_model->get('sliders_homepage_settings');
    if(!$settings){
      $settings = array();
    }
    
    $this->data = $settings;
    $this->theme->view('backend/sliders/homepage', $this->data);
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
      'description' => array(),
      'button' => array(),
      'url' => array(),
    );
    
    $statuses = isset($post['status']) ? $post['status'] : array();
    $k = -1;
    foreach($statuses as $i => $status){
      $k++;
      $data['status'][$k] = $status = isset($post['status'][$i]) ? $post['status'][$i] : null;
      $data['title'][$k] = $title = isset($post['title'][$i]) ? $post['title'][$i] : null;
      $data['description'][$k] = $description = isset($post['description'][$i]) ? $post['description'][$i] : null;
      $data['button'][$k] = $button = isset($post['button'][$i]) ? $post['button'][$i] : null;
      $data['url'][$k] = $url = isset($post['url'][$i]) ? $post['url'][$i] : null;
      
      $fake_post_prefix = 'zone_' . $i . '_';
      $_POST[$fake_post_prefix . 'status'] = $status;
      $_POST[$fake_post_prefix . 'title'] = $title;
      $_POST[$fake_post_prefix . 'description'] = $description;
      $_POST[$fake_post_prefix . 'button'] = $button;
      $this->form_validation->set_rules($fake_post_prefix . 'status', 'Zona ' . ($k + 1) . ' Status', 'in_list[0,1]');
      $this->form_validation->set_rules($fake_post_prefix . 'title', 'Zona ' . ($k + 1) . ' Titlu', 'trim' . (0 && $status ? '|required' : '') . '|max_length[255]');
      $this->form_validation->set_rules($fake_post_prefix . 'button', 'Zona ' . ($k + 1) . ' Titlu buton', 'trim|max_length[255]');
      $this->form_validation->set_rules($fake_post_prefix . 'description', 'Zona ' . ($k + 1) . ' Descriere', 'trim|min_length[1]');
    }  
    
    if ($this->form_validation->run() == FALSE) {
      $this->addMessage($this->form_validation->error_string(),'error');
      $this->saveMessagesInSession();
      $this->data = $data;
      return $this->theme->view('backend/sliders/homepage', $this->data);
    }
    
    $this->load->model('Options_model');
    
    $this->Options_model->set('sliders_homepage_settings',$data);
    $this->redirect('backend/sliders/homepage', 'Informatiile au fost salvate', 'success');
  }
}