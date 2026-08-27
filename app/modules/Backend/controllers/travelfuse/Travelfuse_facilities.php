<?php
if (!defined('BASEPATH'))
  exit('No direct script access allowed');

class TravelFuse_Facilities extends MX_Controller {
  public function index() {
    if(!$this->user->can('backend-access', 'backend-config-access')){
      $this->redirect('backend','Acces invalid', 'error');
    }
    $this->theme->view('backend/travelfuse/facilities', $this->data);
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
    // $country = trim('' . $this->input->post('country'));
    // $filters['country'] = $country;
    $type = trim('' . $this->input->post('type'));
    $filters['type'] = $type;
    $select = $this->input->post('select');
    $filters['select'] = $select;
    // $join_child = $this->input->post('join_child');
    // $filters['join_child'] = $join_child;
    if($simple){
      $filters['return_rows'] = true;
    }
	
    $this->load->model('Travelfuse/TravelFuseFacilities_model');
    $this->data['total_items'] = $this->TravelFuseFacilities_model->getTotal($filters);
    
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
    $facilities = $this->TravelFuseFacilities_model->getList($filters);
	
    if(!$simple){
	foreach($facilities as $k=>$facility){
        $facility->can_view = ($facility->status>=-1) && $user_can['access'] && $user_can['view'];
        if($facility->can_view){
          $facility->view_link = base_url('backend/travelfuse/travelfuse_facilities/view?id=' . $facility->id);
        }
        $facility->can_change_status = ($facility->status>=-1) && $user_can['access'] && $user_can['edit'];
        if($facility->can_change_status){
          $facility->publish_link = base_url('backend/travelfuse/travelfuse_facilities/publish?id=' . $facility->id);
          $facility->unpublish_link = base_url('backend/travelfuse/travelfuse_facilities/unpublish?id=' . $facility->id);
        }
        $facility->can_edit = ($facility->status>=0) && ($user_can['access'] && $user_can['edit']);
        if($facility->can_edit){
          $facility->edit_link = base_url('backend/travelfuse/travelfuse_facilities/edit?id=' . $facility->id);
        }
        $facility->can_delete = 0;
	}
	}
    $this->data['facilities'] = $facilities;
    $this->data['page'] = $current_page;
    
    if(!$simple){
      $session_data = array();
      $session_data['page'] = $current_page;
      $session_data['ordering'] = $ordering;
      $session_data['search'] = $search;
      $session_data['limit'] = $limit;
      $this->session->set_userdata('backend/travelfuse/facilities', $session_data);
    }
	
    $this->output();
  }
  public function edit() {
    if(!$this->user->can('backend-access', 'backend-config-access', 'backend-config-save')){
      $this->redirect('backend','Acces invalid', 'error');
    }
    $id = (int)$this->input->get('id');
    $this->load->model('Travelfuse/TravelFuseFacilities_model');
    $facility = $this->TravelFuseFacilities_model->getById($id);
    if(!$facility){
      $this->redirect('backend/travelfuse/travelfuse_facilities','Acces invalid', 'error');
    }
    $this->data['facility'] = $facility;
    $this->theme->view('backend/travelfuse/facility', $this->data);
  }
  public function view() {
    if(!$this->user->can('backend-access', 'backend-config-access')){
      $this->redirect('backend','Acces invalid', 'error');
    }
    $id = (int)$this->input->get('id');
    $this->load->model('Travelfuse/TravelFuseFacilities_model');
    $facility = $this->TravelFuseFacilities_model->getById($id);
    if(!$facility || ($facility->status<-1)){
      $this->redirect('backend/travelfuse/travelfuse_facilities','Acces invalid', 'error');
    }
    $this->data['facility'] = $facility;
    $this->theme->view('backend/travelfuse/facility', $this->data);
  }
  
  public function unpublish() {
    if(!$this->user->can('backend-access', 'backend-config-access', 'backend-config-save')){
      $this->redirect('backend','Acces invalid', 'error');
    }
    $id = (int)$this->input->get('id');
    $this->load->model('Travelfuse/TravelFuseFacilities_model');
    $facility = $this->TravelFuseFacilities_model->getById($id);
    if(!$facility || ($facility->status<-1)){
      $this->redirect('backend/travelfuse/travelfuse_facilities','Acces invalid', 'error');
    }
    $this->TravelFuseFacilities_model->unpublishById($id);
    $this->redirect('backend/travelfuse/travelfuse_facilities','Facilitatea a fost dezactivat', 'success');
  }
  public function publish() {
    if(!$this->user->can('backend-access', 'backend-config-access', 'backend-config-save')){
      $this->redirect('backend','Acces invalid', 'error');
    }
    $id = (int)$this->input->get('id');
    $this->load->model('Travelfuse/TravelFuseFacilities_model');
    $facility = $this->TravelFuseFacilities_model->getById($id);
    if(!$facility || ($facility->status<-1)){
      $this->redirect('backend/travelfuse/travelfuse_facilities','Acces invalid', 'error');
    }
    $this->TravelFuseFacilities_model->publishById($id);
    $this->redirect('backend/travelfuse/travelfuse_facilities','Facilitatea a fost activat', 'success');
  }
  public function save() {
    if(!$this->user->can('backend-access', 'backend-config-access', 'backend-config-save')){
      if ($this->input->is_ajax_request()) {
        $this->outputError('Invalid access');
      } else {
        $this->redirect('backend/travelfuse/travelfuse_facilities', 'Invalid access', 'error');
      }
    }
    $id = (int)$this->input->post('id');
    $task = $this->input->post('task');
    $facility_id = $id > 0 ? $id : 0;
    if($task == 'save_as_new'){
      $facility_id = 0;
    }
    $data = array();
    $this->load->model('Travelfuse/TravelFuseFacilities_model');
    if($facility_id){
      $facility = $this->TravelFuseFacilities_model->getById($facility_id);
      if(!$facility){
        if ($this->input->is_ajax_request()) {
          $this->outputError('Invalid facility');
        } else {
          $this->redirect('backend/travelfuse/travelfuse_facilities', 'Invalid facility', 'error');
        }
      }
    } else {
      $this->redirect('backend/travelfuse/travelfuse_facilities', 'Adding disabled', 'error');
    }
	
    $this->load->library('form_validation');
    $should_validate = true;
    $this->form_validation->set_rules('status', 'Status', 'required|in_list[0,1]');
    $facility->_name_ro = $data['_name_ro'] = trim($this->input->post('_name_ro'));
    $facility->_name_en = $data['_name_en'] = trim($this->input->post('_name_en'));
    $facility->status = $data['status'] = (int)$this->input->post('status');
    
    if($facility_id){
      $data['id'] = $facility_id;
    }
    if($should_validate && $this->form_validation->run() == FALSE){
      $this->data['errors'] = $this->form_validation->error_array();
      if ($this->input->is_ajax_request()) {
        $this->outputError($this->form_validation->error_string());
      }
      $this->addError($this->form_validation->error_string());
      $this->saveMessagesInSession();
      $this->data['facility'] = $facility;
      return $this->theme->view('backend/travelfuse/facility', $this->data);
    }
    
    if ($this->input->is_ajax_request()) {
      $this->addMessage('Validat cu succes');
      $this->output();
    }
    
    $is_new = !$facility_id;
    $id = $this->TravelFuseFacilities_model->save($data);
    $message = 'Facilitatea a fost actualizat';
    if($is_new){
      $message = 'Facilitatea a fost creat';
    }
    $redirect_url = 'backend/travelfuse/travelfuse_facilities';
    switch($task){
      case 'save_and_new': $redirect_url = 'backend/travelfuse/travelfuse_facilities/add'; break;
      case 'apply':
      case 'save_as_new': $redirect_url = 'backend/travelfuse/travelfuse_facilities/edit?id=' . $id; break;
    }
    $this->redirect($redirect_url, $message, 'success');
  }
}