<?php
if (!defined('BASEPATH'))
  exit('No direct script access allowed');

class Requestoffer extends MX_Controller {
  public function index() {
    if(!$this->user->can('backend-access', 'backend-config-access')){
      $this->redirect('backend','Acces invalid', 'error');
    }
    $this->theme->view('backend/trip/requestoffer', $this->data);
  }
  public function getlist() {
    if(!$this->user->can('backend-access', 'backend-config-access')){
      $this->outputError('Invalid access');
    }
    $simple = $this->input->post('simple');
    if (!$this->input->is_ajax_request()) {
      $this->redirect('','Acces invalid','error');
    }
    $filters = array();
    
    $search = trim('' . $this->input->post('search'));
    $filters['search'] = $search;
    $filters['select'] = array(
      'code', 'title', 'email', 'phone', 'fullname', 'status', 'message', 'type', 'amount', 'amount_new', 'currency', 'date_expire', 'time_created', 'times_checked', 'time_last_checked'
    );
    
    $this->load->model('TripRequestoffer_model');
    $this->data['total_requests'] = $this->TripRequestoffer_model->getTotalRequests($filters);
    
    $limit = (int)$this->input->post('limit');
    if($limit<1 || $limit>100){
      $limit = 20;
    }
    $filters['limit'] = $limit;
    // $ordering = trim('' . $this->input->post('ordering'));
    $ordering = 'id DESC';
    $filters['ordering'] = $ordering;
    
    $max_pages = $filters['limit'] ? ceil($this->data['total_requests'] / $filters['limit']) : 1;
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
    $requests = array();
    $this->load->library('encryption');
    if($this->data['total_requests']){
      $requests = $this->TripRequestoffer_model->getRequests($filters);
      foreach($requests as $k=>$request){
        $request->can_delete = false;
        $get_query = http_build_query(array('s_c' => $request->code));
        $request->delete_link = base_url('requestoffer/delete?' . $get_query);
        $request->view_link = base_url('requestoffer/view?' . $get_query);
        $request->requests_link = base_url('requestoffer/?' . $get_query);
      }
    }
    $this->data['requests'] = $requests;
    $this->data['page'] = $current_page;
    
    if(!$simple){
      $session_data = array();
      $session_data['page'] = $current_page;
      $session_data['ordering'] = $ordering;
      $session_data['search'] = $search;
      $session_data['limit'] = $limit;
      $this->session->set_userdata('backend/trip/requestoffer', $session_data);
    }
    $this->output();
  }
}