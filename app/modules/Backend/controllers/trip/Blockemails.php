<?php
if (!defined('BASEPATH'))
  exit('No direct script access allowed');

class BlockEmails extends MX_Controller {
  public function index() {
    if(!$this->user->can('backend-access', 'backend-config-access')){
      $this->redirect('backend','Acces invalid', 'error');
    }
    $this->theme->view('backend/trip/blockemails', $this->data);
  }
  public function getlist() {
    if(!$this->user->can('backend-access', 'backend-config-access')){
      $this->outputError('Invalid access');
    }
    $filters = array();
    $simple = $this->input->post('simple');
    $type = $this->input->post('type');
    $filters['status'] = array(0,1);
    
    $user_can = array();
    $user_can['access'] = $this->user->can('backend-config-access');
    $user_can['view'] = $user_can['access'];
    $user_can['edit'] = $user_can['access'] && $this->user->can('backend-config-save');
    $user_can['delete'] = $user_can['access'] && $this->user->can('backend-config-save');
    
    $search = trim('' . $this->input->post('search'));
    $filters['search'] = $search;
    $select = $this->input->post('select');
    $filters['select'] = $select;
    if($simple){
      $filters['return_rows'] = true;
    }
    $this->load->model('TripBlockEmail_model');
    $this->data['total_items'] = $this->TripBlockEmail_model->getTotalBlockEmails($filters);
    
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
    $blockemails = $this->TripBlockEmail_model->getBlockEmails($filters);
    if(!$simple){
      foreach($blockemails as $k=>$blockemail){
        $blockemail->can_view = ($blockemail->status>=-1) && $user_can['access'] && $user_can['view'];
        if($blockemail->can_view){
          $blockemail->view_link = base_url('backend/trip/blockemails/view?id=' . $blockemail->id);
        }
        $blockemail->can_change_status = ($blockemail->status>=-1) && $user_can['access'] && $user_can['edit'];
        if($blockemail->can_change_status){
          $blockemail->publish_link = base_url('backend/trip/blockemails/publish?id=' . $blockemail->id);
          $blockemail->unpublish_link = base_url('backend/trip/blockemails/unpublish?id=' . $blockemail->id);
        }
        $blockemail->can_edit = ($blockemail->status>=0 && !$blockemail->nr_uses) && ($user_can['access'] && $user_can['edit']);
        if($blockemail->can_edit){
          $blockemail->edit_link = base_url('backend/trip/blockemails/edit?id=' . $blockemail->id);
        }
        $blockemail->can_delete = ($blockemail->status>=0 && !$blockemail->nr_uses) && ($user_can['access'] && $user_can['delete']);
        if($blockemail->can_delete){
          $blockemail->delete_link = base_url('backend/trip/blockemails/delete?id=' . $blockemail->id);
        }
      }
    }
    $this->data['blockemails'] = $blockemails;
    $this->data['page'] = $current_page;
    
    if(!$simple){
      $session_data = array();
      $session_data['page'] = $current_page;
      $session_data['ordering'] = $ordering;
      $session_data['search'] = $search;
      $session_data['limit'] = $limit;
      $this->session->set_userdata('backend/trip/blockemails', $session_data);
    }
    $this->output();
  }
  public function add() {
    if(!$this->user->can('backend-access', 'backend-config-access', 'backend-config-save')){
      $this->redirect('backend','Acces invalid', 'error');
    }
    $this->data['blockemail'] = (object)array(
      'id'=>null,
      'code'=>'',
      'percentage'=>null,
      'max_uses'=>null,
      'nr_uses'=>null,
      'status'=>1,
      'date_start'=>null,
      'date_expire'=>null,
      'created_by'=>$this->user->id,
      'time_created'=>date('Y-m-d H:i:s'),
      'modified_by'=>null,
      'time_modified'=>null,
      'hotel'=>1,
      'package'=>1,
    );
    $this->theme->view('backend/trip/blockemail', $this->data);
  }
  public function edit() {
    if(!$this->user->can('backend-access', 'backend-config-access', 'backend-config-save')){
      $this->redirect('backend','Acces invalid', 'error');
    }
    $id = (int)$this->input->get('id');
    $this->load->model('TripBlockEmail_model');
    $blockemail = $this->TripBlockEmail_model->getBlockEmailById($id);
    if(!$blockemail){
      $this->redirect('backend/trip/blockemails','Acces invalid', 'error');
    }
    $this->data['blockemail'] = $blockemail;
    $this->theme->view('backend/trip/blockemail', $this->data);
  }
  public function view() {
    if(!$this->user->can('backend-access', 'backend-config-access')){
      $this->redirect('backend','Acces invalid', 'error');
    }
    $id = (int)$this->input->get('id');
    $this->load->model('TripBlockEmail_model');
    $blockemail = $this->TripBlockEmail_model->getBlockEmailById($id);
    if(!$blockemail || ($blockemail->status<-1)){
      $this->redirect('backend/trip/blockemails','Acces invalid', 'error');
    }
    $this->data['blockemail'] = $blockemail;
    $this->theme->view('backend/trip/blockemail', $this->data);
  }
  public function delete() {
    if(!$this->user->can('backend-access', 'backend-config-access', 'backend-config-save')){
      $this->redirect('backend','Acces invalid', 'error');
    }
    $id = (int)$this->input->get('id');
    $this->load->model('TripBlockEmail_model');
    $blockemail = $this->TripBlockEmail_model->getBlockEmailById($id);
    if(!$blockemail || ($blockemail->status<0) || $blockemail->nr_uses){
      $this->redirect('backend/trip/blockemails','Acces invalid', 'error');
    }
    $this->TripBlockEmail_model->deleteBlockEmailById($id);
    $this->redirect('backend/trip/blockemails','Emailul blocat a fost sters', 'success');
  }
  public function unpublish() {
    if(!$this->user->can('backend-access', 'backend-config-access', 'backend-config-save')){
      $this->redirect('backend','Acces invalid', 'error');
    }
    $id = (int)$this->input->get('id');
    $this->load->model('TripBlockEmail_model');
    $blockemail = $this->TripBlockEmail_model->getBlockEmailById($id);
    if(!$blockemail || ($blockemail->status<0)){
      $this->redirect('backend/trip/blockemails','Acces invalid', 'error');
    }
    $this->TripBlockEmail_model->unpublishBlockEmailById($id);
    $this->redirect('backend/trip/blockemails','Emailul blocat a fost dezactivat', 'success');
  }
  public function publish() {
    if(!$this->user->can('backend-access', 'backend-config-access', 'backend-config-save')){
      $this->redirect('backend','Acces invalid', 'error');
    }
    $id = (int)$this->input->get('id');
    $this->load->model('TripBlockEmail_model');
    $blockemail = $this->TripBlockEmail_model->getBlockEmailById($id);
    if(!$blockemail || ($blockemail->status<0)){
      $this->redirect('backend/trip/blockemails','Acces invalid', 'error');
    }
    $this->TripBlockEmail_model->publishBlockEmailById($id);
    $this->redirect('backend/trip/blockemails','Emailul blocat a fost activat', 'success');
  }
  public function save() {
    if(!$this->user->can('backend-access', 'backend-config-access', 'backend-config-save')){
      if ($this->input->is_ajax_request()) {
        $this->outputError('Invalid access');
      } else {
        $this->redirect('backend/trip/blockemails', 'Invalid access', 'error');
      }
    }
    $id = (int)$this->input->post('id');
    $task = $this->input->post('task');
    $blockemail_id = $id > 0 ? $id : 0;
    if($task == 'save_as_new'){
      $blockemail_id = 0;
    }
    $data = array();
    $this->load->model('TripBlockEmail_model');
    $code = trim($this->input->post('code'));
    if($blockemail_id){
      $blockemail = $this->TripBlockEmail_model->getBlockEmailById($blockemail_id);
      if(!$blockemail || ($blockemail->status<-1) || $blockemail->nr_uses){
        if ($this->input->is_ajax_request()) {
          $this->outputError('Invalid blockemail');
        } else {
          $this->redirect('backend/trip/blockemails', 'Invalid blockemail', 'error');
        }
      }
      $check_unique_code = $blockemail->code !== $code;
      $data['modified_by'] = $this->user->id;
      $data['time_modified'] = date('Y-m-d H:i:s');
    } else {
      $check_unique_code = true;
      $blockemail = (object)array(
        'id'=>null,
        'code'=>'',
        'percentage'=>null,
        'max_uses'=>null,
        'nr_uses'=>null,
        'status'=>1,
        'date_start'=>null,
        'date_expire'=>null,
        'created_by'=>$this->user->id,
        'time_created'=>date('Y-m-d H:i:s'),
        'modified_by'=>null,
        'time_modified'=>null,
      );
      $data['created_by'] = $this->user->id;
      $data['time_created'] = date('Y-m-d H:i:s');
    }
    $this->load->library('form_validation');
    $should_validate = true;
    $this->form_validation->set_rules('code', 'Cod', 'required' . ($check_unique_code ? '|is_unique[trip_blockemail.code]' : ''));
    $this->form_validation->set_rules('status', 'Status', 'required|in_list[0,1]');
    $this->form_validation->set_rules('percentage', 'Discount', 'is_numeric|is_greater_than[0]|is_less_than_or_equal_to[100]',array(
      'is_numeric' => 'Discount invalid',
      'is_greater_than' => 'Discountul trebuie sa fie strict pozitiv',
      'is_less_than_or_equal_to' => 'Discountul trebuie sa fie mai mic sau egal cu 100',
    ));
    $this->form_validation->set_rules('max_uses', 'Numar maxim utilizari', 'validate_positive_int|is_greater_than_or_equal_to[0]',array(
      'validate_positive_int' => 'Numarul maxim de utilizari trebuie sa fie un intreg',
      'is_greater_than_or_equal_to' => 'Numar maxim utilizari trebuie sa fie pozitiv',
    ));
    $this->form_validation->set_rules('date_start', 'Data start disponibilitate', 'valid_date[Y-m-d]',array(
      'valid_date' => 'Data de start disponibilitate trebuie sa fie o data valida',
    ));
    $minimum_date_start = isset($_POST['date_start']) && strlen(trim($_POST['date_start'])) ? trim($_POST['date_start']) : date('Y-m-d');
    $this->form_validation->set_rules('date_expire', 'Data expirare', 'valid_date[Y-m-d]|is_greater_than_or_equal_to[' . $minimum_date_start . ']',array(
      'valid_date' => 'Data de expirare trebuie sa fie o data valida',
      'is_greater_than_or_equal_to' => 'Data de expirare este in trecut fata de data de start',
    ));
    $blockemail->code = $data['code'] = $code;
    $blockemail->status = $data['status'] = $this->input->post('status');
    $percentage = floatval($this->input->post('percentage'));
    $blockemail->percentage = $data['percentage'] = abs($percentage);
    $max_uses = (int)$this->input->post('max_uses');
    $blockemail->max_uses = $data['max_uses'] = $max_uses > 0 ? $max_uses : null;
    $hotel = (int)$this->input->post('hotel');
    $blockemail->hotel = $data['hotel'] = $hotel > 0 ? 1 : 0;
    $package = (int)$this->input->post('package');
    $blockemail->package = $data['package'] = $package > 0 ? 1 : 0;
    $date_start = isset($_POST['date_start']) && strlen(trim($_POST['date_start'])) ? trim($_POST['date_start']) : null; 
    $blockemail->date_start = $data['date_start'] = $date_start;
    $date_expire = isset($_POST['date_expire']) && strlen(trim($_POST['date_expire'])) ? trim($_POST['date_expire']) : null; 
    $blockemail->date_expire = $data['date_expire'] = $date_expire;
    if($blockemail_id){
      $data['id'] = $blockemail_id;
    }
    if($should_validate && $this->form_validation->run() == FALSE){
      $this->data['errors'] = $this->form_validation->error_array();
      if ($this->input->is_ajax_request()) {
        $this->outputError($this->form_validation->error_string());
      }
      $this->addError($this->form_validation->error_string());
      $this->saveMessagesInSession();
      $this->data['blockemail'] = $blockemail;
      return $this->theme->view('backend/trip/blockemail', $this->data);
    }
    
    if ($this->input->is_ajax_request()) {
      $this->addMessage('Validat cu succes');
      $this->output();
    }
    
    $is_new = !$blockemail_id;
    $id = $this->TripBlockEmail_model->saveBlockEmail($data);
    $message = 'Emailul blocat a fost actualizat';
    if($is_new){
      $message = 'Emailul blocat a fost creat';
    }
    $redirect_url = 'backend/trip/blockemails';
    switch($task){
      case 'save_and_new': $redirect_url = 'backend/trip/blockemails/add'; break;
      case 'apply':
      case 'save_as_new': $redirect_url = 'backend/trip/blockemails/edit?id=' . $id; break;
    }
    $this->redirect($redirect_url, $message, 'success');
  }
}