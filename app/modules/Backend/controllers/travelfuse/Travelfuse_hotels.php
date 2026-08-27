<?php
if (!defined('BASEPATH'))
  exit('No direct script access allowed');

class TravelFuse_Hotels extends MX_Controller {
  public function index() {
    if(!$this->user->can('backend-access', 'backend-config-access')){
      $this->redirect('backend','Acces invalid', 'error');
    }
    $this->theme->view('backend/travelfuse/hotels', $this->data);
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
	
    $this->load->model('Travelfuse/TravelFuseHotels_model');
    $this->data['total_items'] = $this->TravelFuseHotels_model->getTotal($filters);
    
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
    $hotels = $this->TravelFuseHotels_model->getList($filters);
	
    if(!$simple){
	foreach($hotels as $k=>$hotel){
        $hotel->can_view = ($hotel->status>=-1) && $user_can['access'] && $user_can['view'];
        if($hotel->can_view){
          $hotel->view_link = base_url('backend/travelfuse/travelfuse_hotels/view?id=' . $hotel->id);
        }
        $hotel->can_change_status = ($hotel->status>=-1) && $user_can['access'] && $user_can['edit'];
        if($hotel->can_change_status){
          $hotel->publish_link = base_url('backend/travelfuse/travelfuse_hotels/publish?id=' . $hotel->id);
          $hotel->unpublish_link = base_url('backend/travelfuse/travelfuse_hotels/unpublish?id=' . $hotel->id);
        }
        $hotel->can_edit = ($hotel->status>=0) && ($user_can['access'] && $user_can['edit']);
        if($hotel->can_edit){
          $hotel->edit_link = base_url('backend/travelfuse/travelfuse_hotels/edit?id=' . $hotel->id);
        }
        $hotel->can_delete = 0;
	}
	}
    $this->data['hotels'] = $hotels;
    $this->data['page'] = $current_page;
    
    if(!$simple){
      $session_data = array();
      $session_data['page'] = $current_page;
      $session_data['ordering'] = $ordering;
      $session_data['search'] = $search;
      $session_data['limit'] = $limit;
      $this->session->set_userdata('backend/travelfuse/hotels', $session_data);
    }
	
    $this->output();
  }
  public function edit() {
    if(!$this->user->can('backend-access', 'backend-config-access', 'backend-config-save')){
      $this->redirect('backend','Acces invalid', 'error');
    }
    $id = (int)$this->input->get('id');
    $this->load->model('Travelfuse/TravelFuseHotels_model');
    $hotel = $this->TravelFuseHotels_model->getById($id);
    if(!$hotel){
      $this->redirect('backend/travelfuse/travelfuse_hotels','Acces invalid', 'error');
    }
    $this->data['hotel'] = $hotel;
    $this->theme->view('backend/travelfuse/hotel', $this->data);
  }
  public function view() {
    if(!$this->user->can('backend-access', 'backend-config-access')){
      $this->redirect('backend','Acces invalid', 'error');
    }
    $id = (int)$this->input->get('id');
    $this->load->model('Travelfuse/TravelFuseHotels_model');
    $hotel = $this->TravelFuseHotels_model->getById($id);
    if(!$hotel || ($hotel->status<-1)){
      $this->redirect('backend/travelfuse/travelfuse_hotels','Acces invalid', 'error');
    }
    $this->data['hotel'] = $hotel;
    $this->theme->view('backend/travelfuse/hotel', $this->data);
  }
  
  public function unpublish() {
    if(!$this->user->can('backend-access', 'backend-config-access', 'backend-config-save')){
      $this->redirect('backend','Acces invalid', 'error');
    }
    $id = (int)$this->input->get('id');
    $this->load->model('Travelfuse/TravelFuseHotels_model');
    $hotel = $this->TravelFuseHotels_model->getById($id);
    if(!$hotel || ($hotel->status<-1)){
      $this->redirect('backend/travelfuse/travelfuse_hotels','Acces invalid', 'error');
    }
    $this->TravelFuseHotels_model->unpublishById($id);
    $this->redirect('backend/travelfuse/travelfuse_hotels','Orasul a fost dezactivat', 'success');
  }
  public function publish() {
    if(!$this->user->can('backend-access', 'backend-config-access', 'backend-config-save')){
      $this->redirect('backend','Acces invalid', 'error');
    }
    $id = (int)$this->input->get('id');
    $this->load->model('Travelfuse/TravelFuseHotels_model');
    $hotel = $this->TravelFuseHotels_model->getById($id);
    if(!$hotel || ($hotel->status<-1)){
      $this->redirect('backend/travelfuse/travelfuse_hotels','Acces invalid', 'error');
    }
    $this->TravelFuseHotels_model->publishById($id);
    $this->redirect('backend/travelfuse/travelfuse_hotels','Orasul a fost activat', 'success');
  }
  public function save() {
    if(!$this->user->can('backend-access', 'backend-config-access', 'backend-config-save')){
      if ($this->input->is_ajax_request()) {
        $this->outputError('Invalid access');
      } else {
        $this->redirect('backend/travelfuse/travelfuse_hotels', 'Invalid access', 'error');
      }
    }
    $id = (int)$this->input->post('id');
    $task = $this->input->post('task');
    $hotel_id = $id > 0 ? $id : 0;
    if($task == 'save_as_new'){
      $hotel_id = 0;
    }
    $data = array();
    $this->load->model('Travelfuse/TravelFuseHotels_model');
    if($hotel_id){
      $hotel = $this->TravelFuseHotels_model->getById($hotel_id);
      if(!$hotel){
        if ($this->input->is_ajax_request()) {
          $this->outputError('Invalid hotel');
        } else {
          $this->redirect('backend/travelfuse/travelfuse_hotels', 'Invalid hotel', 'error');
        }
      }
    } else {
      $this->redirect('backend/travelfuse/travelfuse_hotels', 'Adding disabled', 'error');
    }
	
    $this->load->library('form_validation');
    $should_validate = true;
    $this->form_validation->set_rules('status', 'Status', 'required|in_list[0,1]');
    $hotel->_name_ro = $data['_name_ro'] = trim($this->input->post('_name_ro'));
    $hotel->_name_en = $data['_name_en'] = trim($this->input->post('_name_en'));
    $hotel->_short_content_ro = $data['_short_content_ro'] = trim($this->input->post('_short_content_ro'));
    $hotel->_short_content_en = $data['_short_content_en'] = trim($this->input->post('_short_content_en'));
    $hotel->_content_ro = $data['_content_ro'] = trim($this->input->post('_content_ro'));
    $hotel->_content_en = $data['_content_en'] = trim($this->input->post('_content_en'));
    $hotel->_web_address = $data['_web_address'] = trim($this->input->post('_web_address'));
    $hotel->status = $data['status'] = (int)$this->input->post('status');
	
	$hotel->_stars = null;
	$stars = $this->input->post('_stars');
	if(isset($stars) && '' . $stars === '' . (int)trim($stars)){
		$hotel->_stars = (int)$stars;
	}
	$data['_stars'] = $hotel->_stars;
	
	$hotel->_latitude = null;
	$_latitude = $this->input->post('_latitude');
	if(isset($_latitude) && '' . $_latitude === '' . (float)trim($_latitude)){
		$hotel->_latitude = (int)$_latitude;
	}
	$data['_latitude'] = $hotel->_latitude;
	$hotel->_longitude = null;
	$_longitude = $this->input->post('_longitude');
	if(isset($_longitude) && '' . $_longitude === '' . (float)trim($_longitude)){
		$hotel->_longitude = (int)$_longitude;
	}
	$data['_longitude'] = $hotel->_longitude;
	
	$facilities = (array)$this->input->post('_facilities');
	$hotel->_facilities = [];
	foreach($facilities as $facility){
		$hotel->_facilities[$facility['name']] = [];
		if(isset($facility['hide'])) $hotel->_facilities[$facility['name']]['hide'] = 1;
		if(isset($facility['custom'])) $hotel->_facilities[$facility['name']]['custom'] = 1;
	}
	$data['_facilities'] = $hotel->_facilities;
	
	$images = (array)$this->input->post('_images');
	$hotel->_images = [];
	foreach($images as $facility){
		$hotel->_images[$facility['name']] = [];
		if(isset($facility['hide'])) $hotel->_images[$facility['name']]['hide'] = 1;
		if(isset($facility['custom'])) $hotel->_images[$facility['name']]['custom'] = 1;
	}
	$data['_images'] = $hotel->_images;
	// echo '<table><tr><td>';
	// dump($this->input->post());
	// echo '</td><td>';
	// dump($hotel);
	// echo '</tr></table>';
	// die;
    
    if($hotel_id){
      $data['id'] = $hotel_id;
    }
    if($should_validate && $this->form_validation->run() == FALSE){
      $this->data['errors'] = $this->form_validation->error_array();
      if ($this->input->is_ajax_request()) {
        $this->outputError($this->form_validation->error_string());
      }
      $this->addError($this->form_validation->error_string());
      $this->saveMessagesInSession();
      $this->data['hotel'] = $hotel;
      return $this->theme->view('backend/travelfuse/hotel', $this->data);
    }
    
    if ($this->input->is_ajax_request()) {
      $this->addMessage('Validat cu succes');
      $this->output();
    }
    
    $is_new = !$hotel_id;
    $id = $this->TravelFuseHotels_model->save($data);
    $message = 'Orasul a fost actualizat';
    if($is_new){
      $message = 'Orasul a fost creat';
    }
    $redirect_url = 'backend/travelfuse/travelfuse_hotels';
    switch($task){
      case 'save_and_new': $redirect_url = 'backend/travelfuse/travelfuse_hotels/add'; break;
      case 'apply':
      case 'save_as_new': $redirect_url = 'backend/travelfuse/travelfuse_hotels/edit?id=' . $id; break;
    }
    $this->redirect($redirect_url, $message, 'success');
  }
}