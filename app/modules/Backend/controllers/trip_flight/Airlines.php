<?php
if (!defined('BASEPATH'))
  exit('No direct script access allowed');

class Airlines extends MX_Controller {
  public function index() {
    if(!$this->user->can('backend-access')){
      redirect('backend');
    }
    if(!$this->user->can('backend-config-access')){
      redirect('backend');
    }
    $this->theme->view('backend/trip/flight/airlines', $this->data);
  }
  public function getlist() {
    if(!$this->user->can('backend-access')){
      $this->outputError('Acces invalid');
    }
    if(!$this->user->can('backend-config-access')){
      $this->outputError('Acces invalid');
    }
    $filters = array();
    
    $user_can = array();
    $user_can['access'] = $this->user->can('backend-config-access');
    $user_can['view'] = $user_can['access'];
    $user_can['edit'] = $user_can['access'] && $this->user->can('backend-config-save');
    $user_can['delete'] = false;
    
    $search = trim('' . $this->input->post('search'));
    $filters['search'] = $search;
    
    $this->load->model('Trip/Flights_airlines_model');
    $this->data['total_airlines'] = $this->Flights_airlines_model->getTotalAirlines($filters);
    
    $limit = (int)$this->input->post('limit');
    if($limit<0){
      $limit = 0;
    }
    $filters['limit'] = $limit;
    $ordering = trim('' . $this->input->post('ordering'));
    $filters['ordering'] = $ordering;
    
    $max_pages = $filters['limit'] ? ceil($this->data['total_airlines'] / $filters['limit']) : 1;
    if($max_pages < 1){
      $max_pages = 1;
    }
    $this->data['max_pages'] = $max_pages;
    
    $current_page = (int)$this->input->post('page');
    if($current_page > $max_pages){
      $current_page = $max_pages;
    }
    if($current_page < 1){
      $current_page = 1;
    }
   
    $filters['page'] = $current_page;
    $filters['select'] = '*';
    $airlines = $this->Flights_airlines_model->getAirlines($filters);
    foreach($airlines as $k=>$airline){
      if($user_can['view']){
        $airline->can_view = true;
        $airline->view_link = site_url('backend/trip_flight/airlines/view?code=' . $airline->code);
      }
      if($user_can['edit']){
        $airline->can_edit = true;
        $airline->edit_link = site_url('backend/trip_flight/airlines/edit?code=' . $airline->code);
      }
    }
    $this->data['airlines'] = $airlines;
    $this->data['page'] = $current_page;
    
    $session_data = array();
    $session_data['page'] = $current_page;
    $session_data['ordering'] = $ordering;
    $session_data['search'] = $search;
    $session_data['limit'] = $limit;
    $this->session->set_userdata('backend/trip/flight/airlines', $session_data);
    $this->output();
  }
  public function add() {
    if(!$this->user->can('backend-access')){
      redirect('backend');
    }
    if(!$this->user->can('backend-config-access', 'backend-config-save')){
      redirect('backend');
    }
    $this->data['airline'] = (object)[
		'code' => '',
		'image' => '',
		'name' => '',
	];
    $this->theme->view('backend/trip/flight/airline', $this->data);
  }
  public function edit() {
    if(!$this->user->can('backend-access')){
      redirect('backend');
    }
    if(!$this->user->can('backend-config-access', 'backend-config-save')){
      redirect('backend');
    }
    $code = strtolower(trim($this->input->get('code')));
    if(!$code){
      redirect('backend/trip_flight/airlines');
    }
    $this->load->model('Trip/Flights_airlines_model');
    $airline = $this->Flights_airlines_model->getAirlineByCode($code);
    $this->data['airline'] = $airline;
    $this->theme->view('backend/trip/flight/airline', $this->data);
  }
  
  public function view() {
    if(!$this->user->can('backend-access')){
      redirect('backend');
    }
    if(!$this->user->can('backend-config-access')){
      redirect('backend');
    }
    $code = strtolower(trim($this->input->get('code')));
    if(!$code){
      redirect('backend/trip_flight/airlines');
    }
    $this->load->model('Trip/Flights_airlines_model');
    $airline = $this->Flights_airlines_model->getAirlineByCode($code);
    $this->data['airline'] = $airline;
    $this->theme->view('backend/trip/flight/airline', $this->data);
  }
  
  /* public function delete() {
    if(!$this->user->can('backend-access')){
      redirect('backend');
    }
    if(!$this->user->can('backend-config-access', 'backend-config-save')){
      redirect('backend');
    }
    $code = strtolower(trim($this->input->get('code')));
    if(!$code){
      redirect('backend/trip_flight/airlines/add');
    }
    $this->load->model('Trip/Flights_airlines_model');
    
    $airline = $this->Flights_airlines_model->getAirlineObj($code, $this->airline_path);
    if(!$airline){
      redirect('backend/trip_flight/airlines');
    }
    $this->Flights_airlines_model->deleteAirlineBySlug($code, $this->airline_path);
    redirect('backend/trip_flight/airlines');
  } */
  public function save() {
    if(!$this->user->can('backend-access')){
      $this->redirect('','Acces invalid','error');
    }
    if(!$this->user->can('backend-config-access','backend-config-save')){
      $this->redirect('','Acces invalid','error');
    }
    $is_new = $this->input->post('is_new');
    $task = $this->input->post('task');
    $code = $this->input->post('code');
    if(!isset($code) || !strlen('' . $code)){
      $this->redirect('backend/trip_flight/airlines','Cod nespecificat','error');
    }
    $this->load->model('Trip/Flights_airlines_model');
    $airline = $this->Flights_airlines_model->getAirlineByCode($code);
    if(!$airline){
      $this->redirect('backend/trip_flight/airlines','Compania nu a fost gasita','error');
    } elseif($is_new){
      $this->redirect('backend/trip_flight/airlines','Exista deja o companie cu acel cod ' . ($airline->name . ' [' . $airline->code . ']'),'error');
	}
    $this->load->library('form_validation');
    $this->form_validation->set_rules('name', 'Nume companie', 'trim|required|max_length[255]');
    
    $data = array();
    $data['code'] = $code;
    $airline->name = $data['name'] = $this->input->post('name');
    $airline->image = $data['image'] = $this->input->post('image');
    
    $files_image = isset($_FILES['image']) ? $_FILES['image'] : array();
    $image_tmp_name = isset($files_image['tmp_name']) ? $files_image['tmp_name'] : '';
    if($files_image && strlen($image_tmp_name)){
      $_POST['image_upload'] = null;
      $message = 'Imaginea incarcata este invalida';
      if($files_image['error']){
        $message = 'Nu s-a putut incarca imaginea';
      } else {
        $image_name = isset($files_image['name']) ? $files_image['name'] : '';
        
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
            $image = getimagesize($image_tmp_name);
          }
        }
        if($image){
          $image_size = isset($image['size']) ? (int)$image['size'] : 0;
          $image_size_kb = $image_size / 1024;
          if($image_size_kb > 10 * 1024){
            $message = 'Imaginea incarcata depaseste 10 MB';
          } else {
            $file_deposit_path = $this->theme->theme_path . 'assets/images/icons/' . $safe_image_name;
            $data['image'] = 'icons/' . $safe_image_name;
            if(file_exists($file_deposit_path)){
              $_POST['image_upload'] = 'icons/' . $safe_image_name;
              // $message = 'Imaginea incarcata deja exista pe server';
            } else {
              move_uploaded_file($image_tmp_name, $file_deposit_path);
              $_POST['image_upload'] = 'icons/' . $safe_image_name;
            }
          }
        }
      }
      $this->form_validation->set_rules('image_upload', 'Imagine companie', 'required', array(
        'required' => $message
      ));
    }
    if ($this->form_validation->run() == FALSE) {
      $this->addMessage($this->form_validation->error_string(),'error');
      $this->saveMessagesInSession();
      $this->data['airline'] = $airline;
      return $this->theme->view('backend/trip/flight/airline', $this->data);
    }
    
    // echo '<pre>';
    // print_r($data);
    // print_r($images);
    // die;
	if($is_new){
		$this->Flights_airlines_model->addAirline($data);
	} else {
		$this->Flights_airlines_model->saveAirline($data);
	}
    if($task === 'apply'){
      $this->redirect('backend/trip_flight/airlines/edit?code=' . $code, 'Informatiile companiei au fost salvate', 'success');
    }
    $this->redirect('backend/trip_flight/airlines', 'Informatiile companiei au fost salvate', 'success');
  }
}