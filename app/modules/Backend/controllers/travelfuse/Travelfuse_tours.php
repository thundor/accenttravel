<?php
if (!defined('BASEPATH'))
  exit('No direct script access allowed');

class TravelFuse_Tours extends MX_Controller {
  public function index() {
    if(!$this->user->can('backend-access', 'backend-config-access')){
      $this->redirect('backend','Acces invalid', 'error');
    }
    $this->theme->view('backend/travelfuse/tours', $this->data);
  }
  public function getlist() {
    if(!$this->user->can('backend-access', 'backend-config-access')){
      $this->outputError('Invalid access');
    }
    $filters = array();
    $simple = $this->input->post('simple');
    $type = $this->input->post('type');
    $filters['status'] = array(0,1,-1);
    
    $user_can = array();
    $user_can['access'] = $this->user->can('backend-config-access');
    $user_can['view'] = $user_can['access'];
    $user_can['edit'] = $user_can['access'] && $this->user->can('backend-config-save');
    $user_can['delete'] = $user_can['access'] && $this->user->can('backend-config-save');
    
    $search = trim('' . $this->input->post('search'));
    $filters['search'] = $search;
    $country = trim('' . $this->input->post('country'));
    $filters['country'] = $country;
    $type = trim('' . $this->input->post('type'));
    $filters['type'] = $type;
    $select = $this->input->post('select');
    $filters['select'] = $select;
    if($simple){
      $filters['return_rows'] = true;
    }
	
    $this->load->model('Travelfuse/TravelFuseTours_model');
    $this->data['total_items'] = $this->TravelFuseTours_model->getTotal($filters);
    
    $limit = (int)$this->input->post('limit');
    if($limit<0){
      $limit = 0;
    }
    $filters['limit'] = $limit;
    $ordering = trim('' . $this->input->post('ordering'));
    $filters['ordering'] = $ordering;
    
    $max_pages = $filters['limit'] ? ceil($this->data['total_items'] / $filters['limit']) : 1;
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
    $tours = $this->TravelFuseTours_model->getList($filters);
	
    if(!$simple){
	foreach($tours as $k=>$tour){
        $tour->can_view = ($tour->status>=-1) && $user_can['access'] && $user_can['view'];
        if($tour->can_view){
          $tour->view_link = base_url('backend/travelfuse/travelfuse_tours/view?id=' . $tour->id);
        }
        $tour->can_change_status = ($tour->status>=-1) && $user_can['access'] && $user_can['edit'];
        if($tour->can_change_status){
          $tour->publish_link = base_url('backend/travelfuse/travelfuse_tours/publish?id=' . $tour->id);
          $tour->unpublish_link = base_url('backend/travelfuse/travelfuse_tours/unpublish?id=' . $tour->id);
        }
        $tour->can_edit = ($tour->status>=0) && ($user_can['access'] && $user_can['edit']);
        if($tour->can_edit){
          $tour->edit_link = base_url('backend/travelfuse/travelfuse_tours/edit?id=' . $tour->id);
        }
        $tour->can_delete = 0;
	}
	}
    $this->data['tours'] = $tours;
    $this->data['page'] = $current_page;
    
    if(!$simple){
      $session_data = array();
      $session_data['page'] = $current_page;
      $session_data['ordering'] = $ordering;
      $session_data['search'] = $search;
      $session_data['limit'] = $limit;
      $this->session->set_userdata('backend/travelfuse/tours', $session_data);
    }
	
    $this->output();
  }
  public function edit() {
    if(!$this->user->can('backend-access', 'backend-config-access', 'backend-config-save')){
      $this->redirect('backend','Acces invalid', 'error');
    }
    $id = (int)$this->input->get('id');
    $this->load->model('Travelfuse/TravelFuseTours_model');
    $tour = $this->TravelFuseTours_model->getById($id);
    if(!$tour){
      $this->redirect('backend/travelfuse/travelfuse_tours','Acces invalid', 'error');
    }
    $this->data['tour'] = $tour;
    $this->theme->view('backend/travelfuse/tour', $this->data);
  }
  public function view() {
    if(!$this->user->can('backend-access', 'backend-config-access')){
      $this->redirect('backend','Acces invalid', 'error');
    }
    $id = (int)$this->input->get('id');
    $this->load->model('Travelfuse/TravelFuseTours_model');
    $tour = $this->TravelFuseTours_model->getById($id);
    if(!$tour || ($tour->status<-1)){
      $this->redirect('backend/travelfuse/travelfuse_tours','Acces invalid', 'error');
    }
    $this->data['tour'] = $tour;
    $this->theme->view('backend/travelfuse/tour', $this->data);
  }
  
  public function unpublish() {
    if(!$this->user->can('backend-access', 'backend-config-access', 'backend-config-save')){
      $this->redirect('backend','Acces invalid', 'error');
    }
    $id = (int)$this->input->get('id');
    $this->load->model('Travelfuse/TravelFuseTours_model');
    $tour = $this->TravelFuseTours_model->getById($id);
    if(!$tour || ($tour->status<-1)){
      $this->redirect('backend/travelfuse/travelfuse_tours','Acces invalid', 'error');
    }
    $this->TravelFuseTours_model->unpublishById($id);
    $this->redirect('backend/travelfuse/travelfuse_tours','Orasul a fost dezactivat', 'success');
  }
  public function publish() {
    if(!$this->user->can('backend-access', 'backend-config-access', 'backend-config-save')){
      $this->redirect('backend','Acces invalid', 'error');
    }
    $id = (int)$this->input->get('id');
    $this->load->model('Travelfuse/TravelFuseTours_model');
    $tour = $this->TravelFuseTours_model->getById($id);
    if(!$tour || ($tour->status<-1)){
      $this->redirect('backend/travelfuse/travelfuse_tours','Acces invalid', 'error');
    }
    $this->TravelFuseTours_model->publishById($id);
    $this->redirect('backend/travelfuse/travelfuse_tours','Orasul a fost activat', 'success');
  }
  public function save() {
    if(!$this->user->can('backend-access', 'backend-config-access', 'backend-config-save')){
      if ($this->input->is_ajax_request()) {
        $this->outputError('Invalid access');
      } else {
        $this->redirect('backend/travelfuse/travelfuse_tours', 'Invalid access', 'error');
      }
    }
    $id = (int)$this->input->post('id');
    $task = $this->input->post('task');
    $tour_id = $id > 0 ? $id : 0;
    if($task == 'save_as_new'){
      $tour_id = 0;
    }
    $data = array();
    $this->load->model('Travelfuse/TravelFuseTours_model');
    if($tour_id){
      $tour = $this->TravelFuseTours_model->getById($tour_id);
      if(!$tour){
        if ($this->input->is_ajax_request()) {
          $this->outputError('Invalid tour');
        } else {
          $this->redirect('backend/travelfuse/travelfuse_tours', 'Invalid tour', 'error');
        }
      }
    } else {
      $this->redirect('backend/travelfuse/travelfuse_tours', 'Adding disabled', 'error');
    }
	
    $this->load->library('form_validation');
    $should_validate = true;
    $this->form_validation->set_rules('status', 'Status', 'required|in_list[0,1]');
    $tour->_name_ro = $data['_name_ro'] = trim($this->input->post('_name_ro'));
    $tour->_name_en = $data['_name_en'] = trim($this->input->post('_name_en'));
    $tour->_short_content_ro = $data['_short_content_ro'] = trim($this->input->post('_short_content_ro'));
    $tour->_short_content_en = $data['_short_content_en'] = trim($this->input->post('_short_content_en'));
    $tour->_content_ro = $data['_content_ro'] = trim($this->input->post('_content_ro'));
    $tour->_content_en = $data['_content_en'] = trim($this->input->post('_content_en'));
    $tour->_web_address = $data['_web_address'] = trim($this->input->post('_web_address'));
    $tour->status = $data['status'] = (int)$this->input->post('status');
	
	$tour->_stars = null;
	$stars = $this->input->post('_stars');
	if(isset($stars) && '' . $stars === '' . (int)trim($stars)){
		$tour->_stars = (int)$stars;
	}
	$data['_stars'] = $tour->_stars;
	
	$tour->_latitude = null;
	$_latitude = $this->input->post('_latitude');
	if(isset($_latitude) && '' . $_latitude === '' . (float)trim($_latitude)){
		$tour->_latitude = (int)$_latitude;
	}
	$data['_latitude'] = $tour->_latitude;
	$tour->_longitude = null;
	$_longitude = $this->input->post('_longitude');
	if(isset($_longitude) && '' . $_longitude === '' . (float)trim($_longitude)){
		$tour->_longitude = (int)$_longitude;
	}
	$data['_longitude'] = $tour->_longitude;
	
	$facilities = (array)$this->input->post('_facilities');
	$tour->_facilities = [];
	foreach($facilities as $facility){
		$tour->_facilities[$facility['name']] = [];
		if(isset($facility['hide'])) $tour->_facilities[$facility['name']]['hide'] = 1;
		if(isset($facility['custom'])) $tour->_facilities[$facility['name']]['custom'] = 1;
	}
	$data['_facilities'] = $tour->_facilities;
	
	$images = (array)$this->input->post('_images');
	$tour->_images = [];
	foreach($images as $facility){
		$tour->_images[$facility['name']] = [];
		if(isset($facility['hide'])) $tour->_images[$facility['name']]['hide'] = 1;
		if(isset($facility['custom'])) $tour->_images[$facility['name']]['custom'] = 1;
	}
	$data['_images'] = $tour->_images;
	// echo '<table><tr><td>';
	// dump($this->input->post());
	// echo '</td><td>';
	// dump($tour);
	// echo '</tr></table>';
	// die;
    
    if($tour_id){
      $data['id'] = $tour_id;
    }
    if($should_validate && $this->form_validation->run() == FALSE){
      $this->data['errors'] = $this->form_validation->error_array();
      if ($this->input->is_ajax_request()) {
        $this->outputError($this->form_validation->error_string());
      }
      $this->addError($this->form_validation->error_string());
      $this->saveMessagesInSession();
      $this->data['tour'] = $tour;
      return $this->theme->view('backend/travelfuse/tour', $this->data);
    }
    
    if ($this->input->is_ajax_request()) {
      $this->addMessage('Validat cu succes');
      $this->output();
    }
    
    $is_new = !$tour_id;
    $id = $this->TravelFuseTours_model->save($data);
    $message = 'Orasul a fost actualizat';
    if($is_new){
      $message = 'Orasul a fost creat';
    }
    $redirect_url = 'backend/travelfuse/travelfuse_tours';
    switch($task){
      case 'save_and_new': $redirect_url = 'backend/travelfuse/travelfuse_tours/add'; break;
      case 'apply':
      case 'save_as_new': $redirect_url = 'backend/travelfuse/travelfuse_tours/edit?id=' . $id; break;
    }
    $this->redirect($redirect_url, $message, 'success');
  }
}