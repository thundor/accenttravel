<?php
if (!defined('BASEPATH'))
  exit('No direct script access allowed');

class Admin extends MX_Controller {
  public function index() {
    if(!$this->user->can('backend-access')){
      $this->redirect('backend','Acces restrictionat','error');
    }
    $can_access = $this->user->canAnyUnder('backend-accounts-admin-access') || $this->user->canAnyUnder('backend-accounts-admin-own-access');
    if(!$can_access){
      $this->redirect('backend','Acces restrictionat','error');
    }
    $role = trim($this->input->get('role'));
    
    $this->load->model('Permission_model');
    $roles = $this->Permission_model->roles;
    
    $this->data['role'] = '';
    if($role && in_array($role,$roles) && ($this->user->canAny('backend-accounts-admin-access-' . $role, 'backend-accounts-admin-own-access-' . $role))){
      $this->data['role'] = $role;
    }
    
    $this->theme->view('backend/accounts/list/admin', $this->data);
  }
  public function getlist() {
    if(!$this->user->can('backend-access')){
      $this->outputError('Acces restrictionat');
    }
    $can_access = $this->user->canAnyUnder('backend-accounts-admin-access') || $this->user->canAnyUnder('backend-accounts-admin-own-access');
    if(!$can_access){
      $this->outputError('Acces restrictionat');
    }
    $filters = array(
      'type' => 'admin',
      'role' => array(),
      'own_role' => array(),
      'own_created_by' => $this->user->id,
    );
    
    $user_can = array();
    $user_can['access_own'] = array();
    $user_can['view_own'] = array();
    $user_can['edit_own'] = array();
    $user_can['delete_own'] = array();
    $user_can['view'] = array();
    $user_can['edit'] = array();
    $user_can['delete'] = array();
    $user_can['access'] = array();
    
    $this->load->model('Permission_model');
    $roles = $this->Permission_model->roles;
    
    $post_role = trim($this->input->post('role'));
    $only_role = false;
    if($post_role && in_array($post_role,$roles) && ($this->user->canAny('backend-accounts-admin-access-' . $post_role, 'backend-accounts-admin-own-access-' . $post_role))){
      $only_role = $post_role;
    }
    
    foreach($roles as $role){
      $user_can['access'][$role] = $this->user->can('backend-accounts-admin-access-' . $role);
      if($user_can['access'][$role]){
        $filters['role'][] = $role;
      }
      $user_can['access_own'][$role] = $user_can['access'][$role] || $this->user->can('backend-accounts-admin-own-access-' . $role);
      if(!$user_can['access'][$role] && $user_can['access_own'][$role]){
        $filters['own_role'][] = $role;
      }
      $user_can['view_own'][$role] = $user_can['access_own'][$role] && $this->user->can('backend-accounts-admin-own-view-' . $role);
      $user_can['edit_own'][$role] = $user_can['access_own'][$role] && $this->user->can('backend-accounts-admin-own-edit-' . $role);
      $user_can['delete_own'][$role] = $user_can['access_own'][$role] && $this->user->can('backend-accounts-admin-own-delete-' . $role);
      $user_can['view'][$role] = $user_can['access'][$role] && $this->user->can('backend-accounts-admin-view-' . $role);
      $user_can['edit'][$role] = $user_can['access'][$role] && $this->user->can('backend-accounts-admin-edit-' . $role);
      $user_can['delete'][$role] = $user_can['access'][$role] && $this->user->can('backend-accounts-admin-delete-' . $role);
    }
    $search = trim('' . $this->input->post('search'));
    $filters['search'] = $search;
    $filters['except_id'] = $this->user->id;
    if($only_role){
      $filters['role'] = array_intersect($filters['role'],array($only_role));
      $filters['own_role'] = array_intersect($filters['own_role'],array($only_role));
    }
    $can_search_users = !empty($filters['role']) || !empty($filters['own_role']);
    $total_accounts = 0;
    if($can_search_users){
      $this->load->model('Account_model');
      $total_accounts = $this->Account_model->getTotalAccounts($filters);
    }
    $this->data['total_accounts'] = $total_accounts;
    $limit = (int)$this->input->post('limit');
    if($limit<0){
      $limit = 0;
    }
    $filters['limit'] = $limit;
    $ordering = trim('' . $this->input->post('ordering'));
    $filters['ordering'] = $ordering;
    
    $max_pages = $filters['limit'] ? ceil($this->data['total_accounts'] / $filters['limit']) : 1;
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
    $accounts = array();
    if($total_accounts){
      $accounts = $this->Account_model->getAccounts($filters);
    }
    foreach($accounts as $k=>$account){
      if($account->role){
        $account->can_view = ($user_can['access'][$account->role] && $user_can['view'][$account->role]) || ($account->created_by == $this->user->id && $user_can['view_own'][$account->role]);
        if($account->can_view){
          $account->view_link = site_url('backend/accounts/admin/view?id=' . $account->id);
        }
        $account->can_edit = ($user_can['access'][$account->role] && $user_can['edit'][$account->role]) || ($account->created_by == $this->user->id && $user_can['edit_own'][$account->role]);
        if($account->can_edit){
          $account->edit_link = site_url('backend/accounts/admin/edit?id=' . $account->id);
        }
        $account->can_delete = ($user_can['access'][$account->role] && $user_can['delete'][$account->role]) || ($account->created_by == $this->user->id && $user_can['delete_own'][$account->role]);
        if($account->can_delete){
          $account->delete_link = site_url('backend/accounts/admin/delete?id=' . $account->id);
        }
      }
    }
    $this->data['accounts'] = $accounts;
    $this->data['page'] = $current_page;
    
    $session_data = array();
    $session_data['page'] = $current_page;
    $session_data['ordering'] = $ordering;
    $session_data['search'] = $search;
    $session_data['limit'] = $limit;
    $this->session->set_userdata('backend/accounts/admin/list' . ($only_role?'/'.$only_role:''), $session_data);
    $this->output();
  }
  public function add() {
    if(!$this->user->can('backend-access')){
      $this->redirect('backend','Acces restrictionat','error');
    }
    $can_access = $this->user->canAnyUnder('backend-accounts-admin-access') || $this->user->canAnyUnder('backend-accounts-admin-own-access');
    if(!$can_access){
      $this->redirect('backend','Acces restrictionat','error');
    }
    if(!$this->user->canAnyUnder('backend-accounts-admin-add')){
      $this->redirect('backend/accounts/admin','Acces restrictionat','error');
    }
    $this->load->library('user');
    $user = new User;
    $user->type = 'admin';
    $role = trim($this->input->get('role'));
    
    $this->load->model('Permission_model');
    $roles = $this->Permission_model->roles;
    
    $user->role = '';
    if($role && in_array($role,$roles) && $this->user->canAny('backend-accounts-admin-access-' . $role, 'backend-accounts-admin-own-access-' . $role) && $this->user->can('backend-accounts-admin-add-' . $role)){
      $user->role = $role;
    }
    
    $this->data['user'] = $user;
    
    $this->theme->view('backend/accounts/item/admin', $this->data);
  }
  public function edit() {
    if(!$this->user->can('backend-access')){
      $this->redirect('backend','Acces restrictionat','error');
    }
    $can_access = $this->user->canAnyUnder('backend-accounts-admin-access') || $this->user->canAnyUnder('backend-accounts-admin-own-access');
    if(!$can_access){
      $this->redirect('backend','Acces restrictionat','error');
    }
    $id = (int)$this->input->get('id');
    $this->load->model('Account_model');
    $user = $this->Account_model->getAccountById($id);
    $invalid_user = !$user || ($user->type !== 'admin') || ($user->id == $this->user->id);
    if($invalid_user){
      $this->redirect('backend/accounts/admin','Utilizator invalid','error');
    }
    $can_access = $this->user->can('backend-accounts-admin-access-' . $user->role);
    $can_edit = $can_access && $this->user->can('backend-accounts-admin-edit-' . $user->role);
    if(!$can_edit){
      $can_access_own = $can_access || $this->user->can('backend-accounts-admin-own-access-' . $user->role);
      $can_edit_own = $can_access_own && $this->user->can('backend-accounts-admin-own-edit-' . $user->role);
      $can_edit = ($user->created_by == $this->user->id) && $can_edit_own;
    }
    if(!$can_edit){
      $this->redirect('backend/accounts/admin','Acces restrictionat','error');
    }
    $this->data['user'] = $user;
    $this->theme->view('backend/accounts/item/admin', $this->data);
  }
  public function view() {
    if(!$this->user->can('backend-access')){
      $this->redirect('backend','Acces restrictionat','error');
    }
    $can_access = $this->user->canAnyUnder('backend-accounts-admin-access') || $this->user->canAnyUnder('backend-accounts-admin-own-access');
    if(!$can_access){
      $this->redirect('backend','Acces restrictionat','error');
    }
    $id = (int)$this->input->get('id');
    $this->load->model('Account_model');
    $user = $this->Account_model->getAccountById($id);
    $invalid_user = !$user || ($user->type !== 'admin') || ($user->id == $this->user->id);
    if($invalid_user){
      $this->redirect('backend/accounts/admin','Utilizator invalid','error');
    }
    $can_access = $this->user->can('backend-accounts-admin-access-' . $user->role);
    $can_view = $can_access && $this->user->can('backend-accounts-admin-view-' . $user->role);
    if(!$can_view){
      $can_access_own = $can_access || $this->user->can('backend-accounts-admin-own-access-' . $user->role);
      $can_view_own = $can_access_own && $this->user->can('backend-accounts-admin-own-view-' . $user->role);
      $can_view = ($user->created_by == $this->user->id) && $can_view_own;
    }
    if(!$can_view){
      $this->redirect('backend/accounts/admin','Acces restrictionat','error');
    }
    $this->data['user'] = $user;
    $this->theme->view('backend/accounts/item/admin', $this->data);
  }
  public function delete() {
    if(!$this->user->can('backend-access')){
      $this->redirect('backend','Acces restrictionat','error');
    }
    $can_access = $this->user->canAnyUnder('backend-accounts-admin-access') || $this->user->canAnyUnder('backend-accounts-admin-own-access');
    if(!$can_access){
      $this->redirect('backend','Acces restrictionat','error');
    }
    $id = (int)$this->input->get('id');
    $this->load->model('Account_model');
    $user = $this->Account_model->getAccountById($id);
    $invalid_user = !$user || ($user->type !== 'admin') || ($user->id == $this->user->id);
    if($invalid_user){
      $this->redirect('backend/accounts/admin','Utilizator invalid','error');
    }
    $can_access = $this->user->can('backend-accounts-admin-access-' . $user->role);
    $can_delete = $can_access && $this->user->can('backend-accounts-admin-delete-' . $user->role);
    if(!$can_delete){
      $can_access_own = $can_access || $this->user->can('backend-accounts-admin-own-access-' . $user->role);
      $can_delete_own = $can_access_own && $this->user->can('backend-accounts-admin-own-delete-' . $user->role);
      $can_delete = ($user->created_by == $this->user->id) && $can_delete_own;
    }
    if(!$can_delete){
      $this->redirect('backend/accounts/admin','Acces restrictionat','error');
    }
    $this->load->model('Account_model');
    $this->Account_model->deleteAccountById($id);
    $this->redirect('backend/accounts/admin','Utilizatorul a fost sters','success');
  }
  public function save() {
    if(!$this->user->can('backend-access')){
      $this->outputError('Acces restrictionat');
    }
    $can_access = $this->user->canAnyUnder('backend-accounts-admin-access') || $this->user->canAnyUnder('backend-accounts-admin-own-access');
    if(!$can_access){
      $this->outputError('Acces restrictionat');
    }
    $id = (int)$this->input->post('id');
    $data = array();
    $data['user_id'] = 0;
    if($id){
      $this->load->model('Account_model');
      $user = $this->Account_model->getAccountById($id);
      
      $invalid_user = !$user || ($user->type !== 'admin') || ($user->id == $this->user->id);
      if($invalid_user){
        $this->outputError('Invalid user');
      }
      $can_access = $this->user->can('backend-accounts-admin-access-' . $user->role);
      $can_edit = $can_access && $this->user->can('backend-accounts-admin-edit-' . $user->role);
      if(!$can_edit){
        $can_access_own = $can_access || $this->user->can('backend-accounts-admin-own-access-' . $user->role);
        $can_edit_own = $can_access_own && $this->user->can('backend-accounts-admin-own-edit-' . $user->role);
        $can_edit = ($user->created_by == $this->user->id) && $can_edit_own;
      }
      if(!$can_edit){
        $this->outputError('Acces restrictionat. You have no edit permission.');
      }
      
      $data['user_id'] = $user->id;
    } else {
      $access = $this->user->canAnyUnder('backend-accounts-admin-add');
      if(!$access){
        $this->outputError('Acces restrictionat. You have no create permission.');
      }
      $this->load->library('user');
      $user = new User;
      $user->type = 'admin';
    }
    
    $role = trim($this->input->post('role'));
    $this->load->model('Permission_model');
    $possible_roles = array();
    $changed_role = false;
    if(!$user->id || $user->role != $role){
      $changed_role = true;
    }
    $can_access = $this->user->can('backend-accounts-admin-access-' . $role);
    if(!$can_access){
      $can_access_own = $this->user->can('backend-accounts-admin-own-access-' . $role);
      $can_access = ((!$user->id || ($user->created_by == $this->user->id)) && $can_access_own);
    }
    if(!$can_access){
      $this->outputError('Acces restrictionat. Unable to access role '. $role);
    }
    $can_add = $can_access && $this->user->can('backend-accounts-admin-add-' . $role);
    if($changed_role && !$can_add){
      $this->outputError('Acces restrictionat. You have not create permission.');
    }
    
    $this->load->library('form_validation');
    
    $this->form_validation->set_rules('role', 'rol', 'trim|required');
    
    $changed_username = false;
    $username = trim($this->input->post('username'));
    if(!$user->id || $user->username != $username){
      $changed_username = true;
    }
    $this->form_validation->set_rules('username', 'Username', 'trim|required|min_length[4]' . ($changed_username ? '|is_unique[ac_user.user_username]' : ''),array(
      'is_unique' => 'Acest username este deja utilizat in platforma.',
    ));
    
    $changed_email = false;
    $email = trim($this->input->post('email'));
    if(!$user->id || $user->email != $email){
      $changed_email = true;
    }
    $this->form_validation->set_rules('email', 'Adresa email', 'trim|required|valid_email' . ($changed_email ? '|is_unique[ac_user.user_email]' : ''));
    
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
    
    $data['user_type'] = 'admin';
    $data['user_username'] = trim($this->input->post('username'));
    $data['user_email'] = trim($this->input->post('email'));
    $data['user_status'] = (int)$this->input->post('status');
    $data['user_role'] = trim($this->input->post('role'));
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
    $this->data['edit_link'] = site_url('backend/accounts/admin/edit?id='. $id);
    if($new_user){
      $this->addMessage('Utilizatorul a fost creat','success');
    } else {
      $this->addMessage('Informatiile au fost actualizate','success');
    }
    $this->saveMessagesInSession();
    $this->output();
  }
}