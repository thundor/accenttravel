<?php
if (!defined('BASEPATH'))
  exit('No direct script access allowed');

class Subscribers extends MX_Controller {
  public function index() {
    if(!$this->user->can('backend-access', 'backend-config-access')){
      $this->redirect('backend','Acces invalid', 'error');
    }
    $this->theme->view('backend/newsletter/subscribers', $this->data);
  }
  /* public function subscribe() {
    if (!$this->input->is_ajax_request()) {
      $this->redirect('backend','Acces invalid', 'error');
    }
    if(!$this->user->can('backend-access', 'backend-config-access')){
      $this->outputError('Invalid access');
    }
    $status = $this->input->post('status');
    $email = $this->input->post('email');
    
    $this->load->model('WhiteImage_model');
    if(!$status){
      
    }
  } */
  public function getlist() {
    if(!$this->user->can('backend-access', 'backend-config-access')){
      $this->outputError('Invalid access');
    }
    $filters = array();
    $simple = $this->input->post('simple');
    $type = $this->input->post('type');
    $filters['type'] = $type;
    
    $user_can = array();
    $user_can['access'] = $this->user->can('backend-config-access');
    $user_can['view'] = $user_can['access'];
    $user_can['edit'] = $user_can['access'] && $this->user->can('backend-config-manage');
    $user_can['delete'] = $user_can['access'] && $this->user->can('backend-config-manage');
    
    $search = trim('' . $this->input->post('search'));
    $filters['search'] = $search;
    $select = $this->input->post('select');
    $filters['select'] = $select;
    if(!$user_can['access']){
      $filters['created_by'] = $this->user->id;
    }
    if($simple){
      $filters['return_rows'] = true;
    }
    $this->load->model('Newsletter_model');
    $this->data['total_items'] = $this->Newsletter_model->getTotalSubscribers($filters);
    
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
    $subscribers = $this->Newsletter_model->getSubscribers($filters);
    if(!$simple){
      /* foreach($subscribers as $k=>$subscriber){
        $subscriber->can_view = $user_can['access'] && $user_can['view'];
        if($subscriber->can_view){
          $subscriber->view_link = site_url('backend/newsletter/subscribers/view?id=' . $subscriber->id);
        }
        $subscriber->can_edit = ($user_can['access'] && $user_can['edit']);
        if($subscriber->can_edit){
          $subscriber->edit_link = site_url('backend/newsletter/subscribers/edit?id=' . $subscriber->id);
        }
        $subscriber->can_delete = ($user_can['access'] && $user_can['delete']);
        if($subscriber->can_delete){
          $subscriber->delete_link = site_url('backend/newsletter/subscribers/delete?id=' . $subscriber->id);
        }
      } */
    }
    $this->data['subscribers'] = $subscribers;
    $this->data['page'] = $current_page;
    
    if(!$simple){
      $session_data = array();
      $session_data['page'] = $current_page;
      $session_data['ordering'] = $ordering;
      $session_data['search'] = $search;
      $session_data['limit'] = $limit;
      $this->session->set_userdata('backend/newsletter/subscribers', $session_data);
    }
    $this->output();
  }
  public function export() {
    if(!$this->user->can('backend-access', 'backend-config-access')){
      $this->redirect('backend','Acces invalid', 'error');
    }
    header('Content-Type: text/csv; charset=utf-8');
    header('Content-Disposition: attachment; filename=Export abonati.csv');
    $output = fopen('php://output', 'w');
    // fputcsv($output, array('Email'));
    $this->load->model('Newsletter_model');
    $filters = array(
      'select' => 'ns.email',
      'status' => 1,
    );
    $subscribers = $this->Newsletter_model->getSubscribers($filters);
    foreach($subscribers as $subscriber){
      fputcsv($output, array($subscriber->email));
    }
    exit;
  }
  /* public function add() {
    if(!$this->user->can('backend-access', 'backend-config-access', 'backend-config-manage')){
      $this->redirect('backend','Acces invalid', 'error');
    }
    $this->data['subscriber'] = (object)array(
      'id'=>null,
      'email'=>'',
      'user_id'=>0,
      'status'=>1,
      'time_created'=>date('Y-m-d H:i:s'),
    );
    $this->theme->view('backend/newsletter/subscriber', $this->data);
  }
  public function edit() {
    if(!$this->user->can('backend-access', 'backend-config-access', 'backend-config-manage')){
      $this->redirect('backend','Acces invalid', 'error');
    }
    $id = (int)$this->input->get('id');
    $this->load->model('Newsletter_model');
    $subscriber = $this->Newsletter_model->getSubscriberById($id);
    if(!$subscriber){
      $this->redirect('backend/newsletter/subscribers','Acces invalid', 'error');
    }
    $this->data['subscriber'] = $subscriber;
    $this->theme->view('backend/newsletter/subscriber', $this->data);
  }
  public function view() {
    if(!$this->user->can('backend-access', 'backend-config-access')){
      $this->redirect('backend','Acces invalid', 'error');
    }
    $id = (int)$this->input->get('id');
    $this->load->model('Newsletter_model');
    $subscriber = $this->Newsletter_model->getSubscriberById($id);
    if(!$subscriber){
      $this->redirect('backend/newsletter/subscribers','Acces invalid', 'error');
    }
    $this->data['subscriber'] = $subscriber;
    $this->theme->view('backend/newsletter/subscriber', $this->data);
  }
  public function delete() {
    if(!$this->user->can('backend-access', 'backend-config-access', 'backend-config-manage')){
      $this->redirect('backend','Acces invalid', 'error');
    }
    $id = (int)$this->input->get('id');
    $this->load->model('Newsletter_model');
    $this->Newsletter_model->deleteSubscriberById($id);
    $this->redirect('backend/newsletter/subscribers','Abonatul a fost sters', 'success');
  }
  public function save() {
    if(!$this->user->can('backend-access', 'backend-config-access', 'backend-config-manage')){
      if ($this->input->is_ajax_request()) {
        $this->outputError('Invalid access');
      } else {
        $this->redirect('backend/newsletter/pages', 'Invalid access', 'error');
      }
    }
    $id = (int)$this->input->post('id');
    $task = $this->input->post('task');
    $subscriber_id = $id > 0 ? $id : 0;
    if($task == 'save_as_new'){
      $subscriber_id = 0;
    }
    $data = array();
    $this->load->model('Newsletter_model');
    if($subscriber_id){
      $subscriber = $this->Newsletter_model->getSubscriberById($subscriber_id);
      if(!$subscriber){
        if ($this->input->is_ajax_request()) {
          $this->outputError('Invalid subscriber');
        } else {
          $this->redirect('backend/newsletter/subscribers', 'Invalid subscriber', 'error');
        }
      }
      // $data['modified_by'] = $this->user->id;
      // $data['time_modified'] = date('Y-m-d H:i:s');
    } else {
      $subscriber = (object)array(
        'id'=>null,
        'email'=>'',
        'user_id'=>0,
        'status'=>1,
        'time_created'=>date('Y-m-d H:i:s'),
      );
      // $data['created_by'] = $this->user->id;
      $data['time_created'] = date('Y-m-d H:i:s');
    }
    $this->load->library('form_validation');
    $should_validate = true;
    $this->form_validation->set_rules('status', 'Status', 'required|in_list[0,1]');
    $subscriber->status = $data['status'] = $this->input->post('status');
    if($subscriber_id){
      $data['subscriber_id'] = $subscriber_id;
    }
    if($should_validate && $this->form_validation->run() == FALSE){
      $this->data['errors'] = $this->form_validation->error_array();
      if ($this->input->is_ajax_request()) {
        $this->outputError($this->form_validation->error_string());
      }
      $this->addError($this->form_validation->error_string());
      $this->saveMessagesInSession();
      $this->data['subscriber'] = $subscriber;
      return $this->theme->view('backend/newsletter/subscriber', $this->data);
    }
    
    if ($this->input->is_ajax_request()) {
      $this->addMessage('Validat cu succes');
      $this->output();
    }
    
    $is_new = !$page_id;
    $id = $this->Newsletter_model->saveSubscriber($data);
    $message = 'Abonatul a fost actualizat';
    if($is_new){
      $message = 'Abonatul a fost creata';
    }
    $redirect_url = 'backend/newsletter/subscribers';
    switch($task){
      case 'save_and_new': $redirect_url = 'backend/newsletter/subscribers/add'; break;
      case 'apply':
      case 'save_as_new': $redirect_url = 'backend/newsletter/subscribers/edit?id=' . $id; break;
    }
    $this->redirect($redirect_url, $message, 'success');
  } */
}