<?php
if (!defined('BASEPATH'))
  exit('No direct script access allowed');

class Customer extends MX_Controller {
  public function index() {
    if(!$this->user->can('backend-access')){
      $this->redirect('backend','Acces restrictionat','error');
    }
    if(!$this->user->canAny('backend-accounts-customer-access','backend-accounts-customer-own-access')){
      $this->redirect('backend','Acces restrictionat','error');
    }
    $this->theme->view('backend/accounts/list/customer', $this->data);
  }
  public function getlist() {
    if(!$this->user->can('backend-access')){
      $this->outputError('Acces restrictionat');
    }
    if(!$this->user->canAny('backend-accounts-customer-access','backend-accounts-customer-own-access')){
      $this->outputError('Acces restrictionat');
    }
    $filters = array();
    $simple = $this->input->post('simple');
    $one = $this->input->post('one');
    
    if(!$simple){
      $filters['type'] = 'customer';
    }
    $ids = $this->input->post('id');
    if($ids){
      $filters['id'] = $ids;
    }
    $user_can = array();
    $user_can['access'] = $this->user->can('backend-accounts-customer-access');
    if(!$simple){
      $user_can['access_own'] = $user_can['access'] || $this->user->can('backend-accounts-customer-access');
      $user_can['view_own'] = $user_can['access_own'] && $this->user->can('backend-accounts-customer-own-view');
      $user_can['edit_own'] = $user_can['access_own'] && $this->user->can('backend-accounts-customer-own-edit');
      $user_can['delete_own'] = $user_can['access_own'] && $this->user->can('backend-accounts-customer-own-delete');
      $user_can['view'] = $user_can['access'] && $this->user->can('backend-accounts-customer-view');
      $user_can['edit'] = $user_can['access'] && $this->user->can('backend-accounts-customer-edit');
      $user_can['delete'] = $user_can['access'] && $this->user->can('backend-accounts-customer-delete');
    }
    $search = trim('' . $this->input->post('search'));
    $filters['search'] = $search;
    
    if(!$user_can['access']){
      $filters['created_by'] = $this->user->id;
    }
    
    $this->load->model('Account_model');
    $total_accounts = $this->Account_model->getTotalAccounts($filters);
    if(!$one){
      $this->data['total_accounts'] = $total_accounts;
    }
    
    $limit = (int)$this->input->post('limit');
    if($limit<0){
      $limit = 0;
    }
    if($one){
      $limit = 1;
    }
    if($simple){
      $select = $this->input->post('select');
      if($select){
        $filters['select'] = $select;
      }
      $filters['status'] = 1;
      if(!$limit || $limit > 200){
        $limit = 10;
      }
      if($one){
        $filters['return_row'] = true;
      } else {
        $filters['return_rows'] = true;
      }
    } elseif($one){
      $filters['return_result'] = true;
    }
    $filters['limit'] = $limit;
    $ordering = trim('' . $this->input->post('ordering'));
    $filters['ordering'] = $ordering;
    
    $max_pages = $filters['limit'] ? ceil($total_accounts / $filters['limit']) : 1;
    if($max_pages < 1){
      $max_pages = 1;
    }
    if(!$one){
      $this->data['max_pages'] = $max_pages;
    }
    
    $current_page = (int)$this->input->post('page');
    if($current_page > $max_pages){
      $current_page = $max_pages;
    }
    if($current_page < 1){
      $current_page = 1;
    }
   
    $filters['page'] = $current_page;
    if($total_accounts){
      $accounts = $this->Account_model->getAccounts($filters);
    } elseif($one){
      $accounts = false;
    } else{
      $accounts = array();
    }
    if(!$simple){
      foreach($accounts as $k=>$account){
        $account->can_view = ($user_can['access'] && $user_can['view']) || ($account->created_by == $this->user->id && $user_can['view_own']);
        if($account->can_view){
          $account->view_link = site_url('backend/accounts/customer/view?id=' . $account->id);
        }
        $account->can_edit = ($user_can['access'] && $user_can['edit']) || ($account->created_by == $this->user->id && $user_can['edit_own']);
        if($account->can_edit){
          $account->edit_link = site_url('backend/accounts/customer/edit?id=' . $account->id);
        }
        $account->can_delete = ($user_can['access'] && $user_can['delete']) || ($account->created_by == $this->user->id && $user_can['delete_own']);
        if($account->can_delete){
          $account->delete_link = site_url('backend/accounts/customer/delete?id=' . $account->id);
        }
      }
    }
    if(!$one){
      $this->data['limit'] = $limit;
      $this->data['accounts'] = $accounts;
      $this->data['page'] = $current_page;
    } else {
      $this->data['account'] = $accounts;
    }
    if(!$simple){
      $session_data = array();
      $session_data['page'] = $current_page;
      $session_data['ordering'] = $ordering;
      $session_data['search'] = $search;
      $session_data['limit'] = $limit;
      $this->session->set_userdata('backend/accounts/customer/list', $session_data);
    }
    $this->output();
  }
  public function getinfo() {
    $_POST['simple'] = true;
    $_POST['one'] = true;
    return $this->getList();
  }
  public function add() {
    if(!$this->user->can('backend-access')){
      $this->redirect('backend','Acces restrictionat','error');
    }
    if(!$this->user->canAny('backend-accounts-customer-access','backend-accounts-customer-own-access')){
      $this->redirect('backend','Acces restrictionat','error');
    }
    if(!$this->user->can('backend-accounts-customer-add')){
      $this->redirect('backend','Acces restrictionat','error');
    }
    $this->load->library('user');
    $user = new User;
    $user->type = 'customer';
    $this->data['user'] = &$user;
    $this->theme->view('backend/accounts/item/customer', $this->data);
  }
  public function edit() {
    if(!$this->user->can('backend-access')){
      $this->redirect('backend','Acces restrictionat','error');
    }
    if(!$this->user->canAny('backend-accounts-customer-access','backend-accounts-customer-own-access')){
      $this->redirect('backend','Acces restrictionat','error');
    }
    $id = (int)$this->input->get('id');
    $this->load->model('Account_model');
    $user = $this->Account_model->getAccountById($id);
    if(!$user || $user->type !== 'customer'){
      $this->redirect('backend/accounts/customer','Utilizator invalid','error');
    }
    $can_access = $this->user->can('backend-accounts-customer-access');
    $can_edit = $can_access && $this->user->can('backend-accounts-customer-edit');
    if(!$can_edit){
      $can_access_own = $can_access || $this->user->can('backend-accounts-customer-own-access');
      $can_edit_own = $can_access_own && $this->user->can('backend-accounts-customer-own-edit');
      $can_edit = ($user->created_by == $this->user->id) && $can_edit_own;
    }
    if(!$can_edit){
      $this->redirect('backend/accounts/customer','Acces restrictionat','error');
    }
    $this->data['user'] = $user;
    $this->theme->view('backend/accounts/item/customer', $this->data);
  }
  public function view() {
    if(!$this->user->can('backend-access')){
      $this->redirect('backend','Acces restrictionat','error');
    }
    if(!$this->user->canAny('backend-accounts-customer-access','backend-accounts-customer-own-access')){
      $this->redirect('backend','Acces restrictionat','error');
    }
    $id = (int)$this->input->get('id');
    $this->load->model('Account_model');
    $user = $this->Account_model->getAccountById($id);
    if(!$user || $user->type !== 'customer'){
      $this->redirect('backend/accounts/customer','Utilizator invalid','error');
    }
    $can_access = $this->user->can('backend-accounts-customer-access');
    $can_view = $can_access && $this->user->can('backend-accounts-customer-view');
    if(!$can_view){
      $can_access_own = $can_access || $this->user->can('backend-accounts-customer-own-access');
      $can_view_own = $can_access_own && $this->user->can('backend-accounts-customer-own-view');
      $can_view = ($user->created_by == $this->user->id) && $can_view_own;
    }
    if(!$can_view){
      $this->redirect('backend/accounts/customer','Acces restrictionat','error');
    }
    $this->data['user'] = $user;
    $this->theme->view('backend/accounts/item/customer', $this->data);
  }
  public function delete() {
    if(!$this->user->can('backend-access')){
      $this->redirect('backend','Acces restrictionat','error');
    }
    if(!$this->user->canAny('backend-accounts-customer-access','backend-accounts-customer-own-access')){
      $this->redirect('backend','Acces restrictionat','error');
    }
    $id = (int)$this->input->get('id');
    $this->load->model('Account_model');
    $user = $this->Account_model->getAccountById($id);
    if(!$user || $user->type !== 'customer'){
      $this->redirect('backend/accounts/customer','Utilizator invalid','error');
    }
    $can_access = $this->user->can('backend-accounts-customer-access');
    $can_delete = $can_access && $this->user->can('backend-accounts-customer-delete');
    if(!$can_delete){
      $can_access_own = $can_access || $this->user->can('backend-accounts-customer-own-access');
      $can_delete_own = $can_access_own && $this->user->can('backend-accounts-customer-own-delete');
      $can_delete = ($user->created_by == $this->user->id) && $can_delete_own;
    }
    if(!$can_delete){
      $this->redirect('backend/accounts/customer','Acces restrictionat','error');
    }
    $this->load->model('Account_model');
    $this->Account_model->deleteAccountById($id);
    $this->redirect('backend/accounts/customer','Utilizatorul a fost sters','success');
  }
  public function save() {
    if(!$this->user->can('backend-access')){
      $this->outputError('Acces restrictionat');
    }
    if(!$this->user->canAny('backend-accounts-customer-access','backend-accounts-customer-own-access')){
      $this->outputError('Acces restrictionat');
    }
    $id = (int)$this->input->post('id');
    $data = array();
    $data['user_id'] = 0;
    if($id){
      $this->load->model('Account_model');
      $user = $this->Account_model->getAccountById($id);
      $invalid_user = !$user || ($user->type !== 'customer') || ($user->id == $this->user->id);
      if($invalid_user){
        $this->outputError('Invalid user');
      }
      $can_access = $this->user->can('backend-accounts-customer-access');
      $can_edit = $can_access && $this->user->can('backend-accounts-customer-edit');
      if(!$can_edit){
        $can_access_own = $can_access || $this->user->can('backend-accounts-customer-own-access');
        $can_edit_own = $can_access_own && $this->user->can('backend-accounts-customer-own-edit');
        $can_edit = ($user->created_by == $this->user->id) && $can_edit_own;
      }
      if(!$can_edit){
        $this->outputError('Acces restrictionat');
      }
      $data['user_id'] = $user->id;
    } else {
      if(!$this->user->can('backend-accounts-customer-add')){
        $this->outputError('Acces restrictionat');
      }
      $this->load->library('user');
      $user = new User;
      $user->type = 'customer';
    }
    
    $this->load->library('form_validation');
    
    $changed_email = false;
    $email = trim($this->input->post('email'));
    if(!$user->id || $user->username != $email){
      $changed_email = true;
    }
    $this->form_validation->set_rules('email', 'Adresa email', 'trim|required|valid_email' . ($changed_email ? '|is_unique[ac_user.user_username]' : ''));
    
    $password = $this->input->post('password');
    if (!$user->id || !empty($password)) {
      $this->form_validation->set_rules('password', 'Parola', 'min_length[8]');
    }
    $this->form_validation->set_rules('status', 'Status', 'in_list[0,1]');
    
    $this->load->model('Account_model');
    
    $should_validate = true;
    $this->Account_model->applyGeneralFormValidation($this,$user,$data,$should_validate);
    
    if($should_validate && $this->form_validation->run() == FALSE){
      $this->data['errors'] = $this->form_validation->error_array();
      $this->outputError($this->form_validation->error_string());
    }
    $data['user_type'] = 'customer';
    $data['user_username'] = trim($this->input->post('email'));
    $data['user_email'] = trim($this->input->post('email'));
    $data['user_status'] = (int)$this->input->post('status');
    $data['user_role'] = null;
    $data['user_firstname'] = trim($this->input->post('firstname'));
    $data['user_lastname'] = trim($this->input->post('lastname'));
    if($this->input->post('password')){
      $data['user_password'] = sha1($this->input->post('password'));
    }
    $new_user = false;
    if(!$id){
      $new_user = true;
      $data['user_created_by'] = $this->user->id;
      $data['user_created_datetime'] = date("Y-m-d H:i:s");
    } else {
      $data['user_modified_by'] = $this->user->id;
      $data['user_modified_datetime'] = date("Y-m-d H:i:s");
    }
    $this->Account_model->applyGeneralFormSaveAdaptation($this,$user,$data);
    
    $id = $this->Account_model->saveAccount($data);
    
    
    $this->data['id'] = $id;
    $this->data['edit_link'] = site_url('backend/accounts/customer/edit?id='. $id);
    if($new_user){
      $this->addMessage('Utilizatorul a fost creat','success');
    } else {
      $this->addMessage('Informatiile au fost actualizate','success');
    }
    $this->saveMessagesInSession();
    $this->output();
  }
}