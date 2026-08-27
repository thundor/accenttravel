<?php
if (!defined('BASEPATH'))
  exit('No direct script access allowed');

class TravelFuse_Countries extends MX_Controller {
  public function index() {
    if(!$this->user->can('backend-access', 'backend-config-access')){
      $this->redirect('backend','Acces invalid', 'error');
    }
    $this->theme->view('backend/travelfuse/countries', $this->data);
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
    $select = $this->input->post('select');
    $filters['select'] = $select;
    $join_child = $this->input->post('join_child');
    $filters['join_child'] = $join_child;
    if($simple){
      $filters['return_rows'] = true;
    }
	
    $this->load->model('Travelfuse/TravelFuseCountries_model');
    $this->data['total_items'] = $this->TravelFuseCountries_model->getTotal($filters);
    
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
    $countries = $this->TravelFuseCountries_model->getList($filters);
	
    if(!$simple){
	foreach($countries as $k=>$country){
        $country->can_view = ($country->status>=-1) && $user_can['access'] && $user_can['view'];
        if($country->can_view){
          $country->view_link = base_url('backend/travelfuse/travelfuse_countries/view?id=' . $country->id);
        }
        $country->can_change_status = ($country->status>=-1) && $user_can['access'] && $user_can['edit'];
        if($country->can_change_status){
          $country->publish_link = base_url('backend/travelfuse/travelfuse_countries/publish?id=' . $country->id);
          $country->unpublish_link = base_url('backend/travelfuse/travelfuse_countries/unpublish?id=' . $country->id);
        }
        $country->can_edit = ($country->status>=0) && ($user_can['access'] && $user_can['edit']);
        if($country->can_edit){
          $country->edit_link = base_url('backend/travelfuse/travelfuse_countries/edit?id=' . $country->id);
        }
        $country->can_delete = 0;
	}
	}
    $this->data['countries'] = $countries;
    $this->data['page'] = $current_page;
    
    if(!$simple){
      $session_data = array();
      $session_data['page'] = $current_page;
      $session_data['ordering'] = $ordering;
      $session_data['search'] = $search;
      $session_data['limit'] = $limit;
      $this->session->set_userdata('backend/travelfuse/countries', $session_data);
    }
	
    $this->output();
  }
  public function edit() {
    if(!$this->user->can('backend-access', 'backend-config-access', 'backend-config-save')){
      $this->redirect('backend','Acces invalid', 'error');
    }
    $id = (int)$this->input->get('id');
    $this->load->model('Travelfuse/TravelFuseCountries_model');
    $country = $this->TravelFuseCountries_model->getById($id);
    if(!$country){
      $this->redirect('backend/travelfuse/travelfuse_countries','Acces invalid', 'error');
    }
    $this->data['country'] = $country;
    $this->theme->view('backend/travelfuse/country', $this->data);
  }
  public function view() {
    if(!$this->user->can('backend-access', 'backend-config-access')){
      $this->redirect('backend','Acces invalid', 'error');
    }
    $id = (int)$this->input->get('id');
    $this->load->model('Travelfuse/TravelFuseCountries_model');
    $country = $this->TravelFuseCountries_model->getById($id);
    if(!$country || ($country->status<-1)){
      $this->redirect('backend/travelfuse/travelfuse_countries','Acces invalid', 'error');
    }
    $this->data['country'] = $country;
    $this->theme->view('backend/travelfuse/country', $this->data);
  }
  
  public function unpublish() {
    if(!$this->user->can('backend-access', 'backend-config-access', 'backend-config-save')){
      $this->redirect('backend','Acces invalid', 'error');
    }
    $id = (int)$this->input->get('id');
    $this->load->model('Travelfuse/TravelFuseCountries_model');
    $country = $this->TravelFuseCountries_model->getById($id);
    if(!$country || ($country->status<-1)){
      $this->redirect('backend/travelfuse/travelfuse_countries','Acces invalid', 'error');
    }
    $this->TravelFuseCountries_model->unpublishById($id);
    $this->redirect('backend/travelfuse/travelfuse_countries','Tara a fost dezactivata', 'success');
  }
  public function publish() {
    if(!$this->user->can('backend-access', 'backend-config-access', 'backend-config-save')){
      $this->redirect('backend','Acces invalid', 'error');
    }
    $id = (int)$this->input->get('id');
    $this->load->model('Travelfuse/TravelFuseCountries_model');
    $country = $this->TravelFuseCountries_model->getById($id);
    if(!$country || ($country->status<-1)){
      $this->redirect('backend/travelfuse/travelfuse_countries','Acces invalid', 'error');
    }
    $this->TravelFuseCountries_model->publishById($id);
    $this->redirect('backend/travelfuse/travelfuse_countries','Tara a fost activata', 'success');
  }
  public function save() {
    if(!$this->user->can('backend-access', 'backend-config-access', 'backend-config-save')){
      if ($this->input->is_ajax_request()) {
        $this->outputError('Invalid access');
      } else {
        $this->redirect('backend/travelfuse/travelfuse_countries', 'Invalid access', 'error');
      }
    }
    $id = (int)$this->input->post('id');
    $task = $this->input->post('task');
    $country_id = $id > 0 ? $id : 0;
    if($task == 'save_as_new'){
      $country_id = 0;
    }
    $data = array();
    $this->load->model('Travelfuse/TravelFuseCountries_model');
    if($country_id){
      $country = $this->TravelFuseCountries_model->getById($country_id);
      if(!$country){
        if ($this->input->is_ajax_request()) {
          $this->outputError('Invalid country');
        } else {
          $this->redirect('backend/travelfuse/travelfuse_countries', 'Invalid country', 'error');
        }
      }
    } else {
      $this->redirect('backend/travelfuse/travelfuse_countries', 'Adding disabled', 'error');
    }
	
    $this->load->library('form_validation');
    $should_validate = true;
    $this->form_validation->set_rules('status', 'Status', 'required|in_list[0,1]');
    $country->_name_ro = $data['_name_ro'] = trim($this->input->post('_name_ro'));
    $country->_name_en = $data['_name_en'] = trim($this->input->post('_name_en'));
    $country->status = $data['status'] = (int)$this->input->post('status');
    
    if($country_id){
      $data['id'] = $country_id;
    }
    if($should_validate && $this->form_validation->run() == FALSE){
      $this->data['errors'] = $this->form_validation->error_array();
      if ($this->input->is_ajax_request()) {
        $this->outputError($this->form_validation->error_string());
      }
      $this->addError($this->form_validation->error_string());
      $this->saveMessagesInSession();
      $this->data['country'] = $country;
      return $this->theme->view('backend/travelfuse/country', $this->data);
    }
    
    if ($this->input->is_ajax_request()) {
      $this->addMessage('Validat cu succes');
      $this->output();
    }
    
    $is_new = !$country_id;
    $id = $this->TravelFuseCountries_model->save($data);
    $message = 'Tara a fost actualizata';
    if($is_new){
      $message = 'Tara a fost creata';
    }
    $redirect_url = 'backend/travelfuse/travelfuse_countries';
    switch($task){
      case 'save_and_new': $redirect_url = 'backend/travelfuse/travelfuse_countries/add'; break;
      case 'apply':
      case 'save_as_new': $redirect_url = 'backend/travelfuse/travelfuse_countries/edit?id=' . $id; break;
    }
    $this->redirect($redirect_url, $message, 'success');
  }
}