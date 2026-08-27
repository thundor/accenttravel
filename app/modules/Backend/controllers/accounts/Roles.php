<?php
if (!defined('BASEPATH'))
  exit('No direct script access allowed');

class Roles extends MX_Controller {
  public function index() {
    if(!$this->user->can('backend-access')){
      $this->redirect('backend','Acces restrictionat','error');
    }
    if(!$this->user->canAnyUnder('backend-accounts-roles-access')){
      $this->redirect('backend','Acces restrictionat','error');
    }
    $this->load->model('Permission_model');
    $roles = array();
    foreach($this->Permission_model->roles as $role){
      if($this->user->can('backend-accounts-roles-access-' . $role)){
        $roles[] = $role;
      }
    }
    $this->data['showing_all_roles'] = true;
    $input_role = $this->input->get('role');
    $this->data['role'] = $input_role;
    if($input_role && in_array($input_role,$roles)){
      $roles = array($input_role);
      $this->data['showing_all_roles'] = false;
    }
    $this->data['path'] = trim('' . $this->input->get('path'));
    $this->data['roles'] = $roles;
    $all_permissions = $this->Permission_model->getAllPermissions();
    if($this->data['path']){
      $path_arr = explode('-',$this->data['path']);
      $path_permissions = array();
      $this->Permission_model->getPathPermissions($path_permissions,$path_arr,$all_permissions);
      $this->data['all_permissions'] = $path_permissions;
    } else {
      $this->data['all_permissions'] = $all_permissions;
    }
    $filters = array(
      'role' => $this->data['roles'],
      'path' => $this->data['path'],
    );
    $this->data['roles_permissions'] = $this->Permission_model->getRolesPermissions($filters);
    $this->theme->view('backend/accounts/roles', $this->data);
  }
  public function save() {
    if(!$this->user->can('backend-access')){
      $this->redirect('backend','Acces restrictionat','error');
    }
    if(!$this->user->canAnyUnder('backend-accounts-roles-access')){
      $this->redirect('backend','Acces restrictionat','error');
    }
    $this->load->model('Permission_model');
    $role_permissions = $this->input->post('permission');
    $post_path = trim($this->input->post('path'));
    $post_role = trim($this->input->post('role'));
    $save_role_permissions = array();
    if($post_role){
      $role_permissions = array(
        $post_role => $role_permissions[$post_role]
      );
    }
    foreach($role_permissions as $role => $assigned_permissions){
      $current_permissions = $this->Permission_model->getRolePermissions($role);
      $unchangeable_permissions = array();
      foreach($current_permissions as $current_permission){
        if(!$this->user->can($current_permission)){
          $unchangeable_permissions[] = $current_permission;
        }
        if($post_path && strpos($current_permission,$post_path . '-') !== 0){
          $unchangeable_permissions[] = $current_permission;
        }
      }
      $save_permissions = $unchangeable_permissions;
      foreach($assigned_permissions as $assigned_permission){
        if($this->user->can($assigned_permission)){
          $save_permissions[] = $assigned_permission;
        }
      }
      $save_role_permissions[$role] = array_unique($save_permissions);
    }
    $this->Permission_model->savePermissions($save_role_permissions);
    $query = array();
    if($post_role){
      $query['role'] = $post_role;
    }
    if($post_path){
      $query['path'] = $post_path;
    }
    
    $this->redirect('backend/accounts/roles' . ($query ? '?' . http_build_query($query) : ''),'Permisiunile au fost salvate','success');
  }
}