<?php
if (!defined('BASEPATH'))
  exit('No direct script access allowed');

class Advantages extends MX_Controller {
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
    $settings = $this->Options_model->get('static_advantages_settings');
    if(!$settings){
      $settings = array();
    }
    
    $this->data = $settings;
    $this->theme->view('backend/static/advantages', $this->data);
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
      $uploaded_image = isset($images['tmp_name'][$i]) ? $images['tmp_name'][$i] : false;
      if($uploaded_image){
        $image_name = isset($images['name'][$i]) ? $images['name'][$i] : '';
        $image_ext = strrchr($image_name, ".");
        $image_extension = substr($image_ext, 1);
        $image = false;
        if($image_extension === $image_name || '.' . $image_extension === $image_name){
          $image_extension = false;
        }
        if($image_extension){
          $safe_image_name = trim(preg_replace("/[^a-zA-Z0-9\.\-\_]/", '', $image_name),'. ');
          $image_basename = basename($safe_image_name,$image_ext);
          if(strlen($image_basename) && $image_extension && in_array(strtolower($image_extension), array('jpg','png', 'gif'))){
            $image = getimagesize($uploaded_image);
          }
        }
        $_POST[$fake_post_prefix . 'image_upload'] = null;
        if(!$image){
          $this->form_validation->set_rules($fake_post_prefix . 'image_upload', 'Zona ' . ($k + 1) . ' Imagine incarcata', 'required', array(
            'required' => 'Zona ' . ($k + 1) . ' Imaginea incarcata este invalida'
          ));
        } else {
          $image_size = isset($images['size'][$i]) ? (int)$images['size'][$i] : 0;
          $image_size_kb = $image_size / 1024;
          
          if($image_size_kb > 10 * 1024){
            $this->form_validation->set_rules($fake_post_prefix . 'image_upload', 'Zona ' . ($k + 1) . ' Imagine incarcata', 'required', array(
              'required' => 'Zona ' . ($k + 1) . ' Imaginea incarcata depaseste 10 MB'
            ));
          } else {
            $file_deposit_path = $this->theme->theme_path . 'assets/images/advantages/' . $safe_image_name;
            $data['image'][$k] = $safe_image_name;
            if(file_exists($file_deposit_path)){
              // $this->form_validation->set_rules($fake_post_prefix . 'image_upload', 'Zona ' . ($k + 1) . ' Imagine incarcata', 'required', array(
                // 'required' => 'Zona ' . ($k + 1) . ' Imaginea incarcata deja exista pe server'
              // ));
            } else {
              move_uploaded_file($uploaded_image, $file_deposit_path);
              // $data['image'][$k] = $safe_image_name;
              $_POST[$fake_post_prefix . 'image'] = 'advantages/' . $safe_image_name;
            }
          }
        }
      }
    }
    
    if ($this->form_validation->run() == FALSE) {
      $this->addMessage($this->form_validation->error_string(),'error');
      $this->saveMessagesInSession();
      $this->data = $data;
      return $this->theme->view('backend/static/advantages', $this->data);
    }
    
    $this->load->model('Options_model');
    
    $this->Options_model->set('static_advantages_settings',$data);
    $this->redirect('backend/static/advantages', 'Informatiile au fost salvate', 'success');
  }
}