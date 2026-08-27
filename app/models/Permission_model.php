<?php
class Permission_model extends CI_Model {
  public $roles = array();
  public $all_permissions = array();
  public $permissions = array();
  public $permissions_groups = array();
  public $role_permissions = array();
  function __construct() {
    parent::__construct();
    $this->roles = config_item('roles');
    foreach($this->roles as $role){
      $this->role_permissions[$role] = array();
    }
    $this->all_permissions = config_item('permissions');
    $this->parsePermissions($this->all_permissions);
  }

  function parsePermissions($permissions,$group = null) {
    foreach($permissions as $k=>$v){
      if(is_string($k) && $group){
        $this->permissions_groups[$group][] = $group . '-' . $k;
      }
      if(is_array($v)){
        $parent = ($group ? $group . '-' : '') . $k;
        $this->permissions_groups[$parent] = array();
        $this->parsePermissions($v,$parent);
      } elseif(is_string($v)){
        $permission =  $group.'-'.$v;
        $this->permissions_groups[$group][] = $permission;
        $permission_roles = $this->getPermissionRoles($permission);
        $this->permissions[$permission] = $permission_roles;
        foreach($permission_roles as $role){
          $this->role_permissions[$role][$permission] = true;
        }
      }
    }
  }
  function getPermissionRoles($permission, $filters=array()) {
    $filters['name'] = $permission;
    $roles = $this->getPermissionsRoles($filters);
    if($roles){
      return $roles[$permission];
    }
    return array();
  }
  function getRolePermissions($role, $filters=array()) {
    $filters['role'] = $role;
    $permissions = $this->getRolesPermissions($filters);
    if($permissions){
      return $permissions[$role];
    }
    return array();
  }
  function getPermissionsRoles($filters=array()) {
    $permissions = $this->getPermissions($filters);
    if($permissions){
      $return_array = array();
      foreach($permissions as $item){
        if(!isset($return_array[$item->permission_name])){
          $return_array[$item->permission_name] = array();
        }
        $return_array[$item->permission_name][] = $item->permission_role;
      }
      return $return_array;
    }
    return array();
  }
  function getPathPermissions(&$path_permissions, $path = array(), $permissions=array(),$nesting=0) {
    if(!$path){
      $path_permissions = $permissions;
      return;
    }
    $permission = '';
    while($path){
      $v = array_shift($path);
      $permission = ($permission ? $permission . '-' : '') . $v;

      if(isset($permissions[$permission])){
        $path_permissions[$permission] = array();
        $this->getPathPermissions($path_permissions[$permission],$path, $permissions[$permission], $nesting+1);
      }
    }
  }
  function getAllPermissions() {
    return $this->all_permissions;
  }
  function getRolesPermissions($filters=array()) {
    $permissions = $this->getPermissions($filters);
    if($permissions){
      $return_array = array();
      foreach($permissions as $item){
        if(!isset($return_array[$item->permission_role])){
          $return_array[$item->permission_role] = array();
        }
        $return_array[$item->permission_role][] = $item->permission_name;
      }
      return $return_array;
    }
    return array();
  }
  function applyFilters($filters = array()) {
    if(isset($filters['role'])){
      $this->db->where_in('permission_role', (array)$filters['role']);
    }
    if(isset($filters['path']) && !empty($filters['path'])){
      $this->db->like('permission_name', $filters['path'], 'after');
    }
    if(isset($filters['except_role'])){
      $this->db->where_not_in('permission_role', (array)$filters['except_role']);
    }
    if(isset($filters['name'])){
      $this->db->where_in('permission_name', (array)$filters['name']);
    }
  }
  function getPermissions($filters = array()) {
    $this->db->select('permission_role');
    $this->db->select('permission_name');
    
    $this->applyFilters($filters);
    
    $page = isset($filters['page']) ? $filters['page']: 1;
    $limit = isset($filters['limit']) ? $filters['limit']: null;
    $offset = 0;
    if($limit > 0){
      $offset = ($page - 1) * $limit;
    }

    $q = $this->db->get('ac_permission', $limit, $offset);
    $result = $q->result();
    $num = $q->num_rows();
    if ($num > 0) {
      return $result;
    }
    return array();
  }
  function getTotalPermissions($filters = array()) {
    $this->db->select('COUNT(*) as total');
    $this->applyFilters($filters);
    $q = $this->db->get('ac_permission');
    $result = $q->result();
    $num = $q->num_rows();
    if ($num > 0) {
      return $result[0]->total;
    }
    return 0;
  }
  function savePermissions($data) {
    foreach($this->roles as $role){
      if(!array_key_exists($role,$data)){
        continue;
      }
      if(!$data[$role]){
        $data[$role] = array();
      }
      $this->saveRolePermissions($role, $data[$role]);
    }
  }
  function saveRolePermissions($role, $permissions) {
    if(!in_array($role,$this->roles)){
      return;
    }

    $current_permissions = $this->getRolePermissions($role);
    $delete_permissions = array_diff($current_permissions, $permissions);
    $add_permissions = array_diff($permissions, $current_permissions);
    
    foreach($add_permissions as $add_permission){
      $data = array(
        'permission_role' => $role,
        'permission_name' =>$add_permission
      );
      $this->db->insert('ac_permission', $data);
    }
    
    foreach($delete_permissions as $delete_permission){
      $data = array(
        'permission_role' => $role,
        'permission_name' =>$delete_permission
      );
      $this->db->delete('ac_permission', $data);
    }
  }
}