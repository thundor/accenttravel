<?php
class User {
  public $id = 0;
  public $username = '';
  public $email = '';
  public $status = 1;
  public $type = 'guest';
  public $role = null;
  public $created_by = null;
  public $created_datetime = null;
  public $country = 'RO';
  public $pj_country = 'RO';
  public $pf_country = 'RO';
  public $contact_country = 'RO';
  public $phone_prefix = 'RO';
  public $pj_phone_prefix = 'RO';
  public $pf_phone_prefix = 'RO';
  public $contact_phone_prefix = 'RO';
  protected $_permissions;
  
  public function __set($name, $value){
    $property_name = $name; 
    if(strpos($name,'user_') === 0){
      $property_name = substr($name,5);
    }
    $this->$property_name = $value;
  }

  public function __get($name){
    $property_name = $name; 
    if(strpos($name,'user_') === 0){
      $property_name = substr($name,5);
    }
    if(property_exists($this,$property_name)){
      return $this->$property_name;
    }
    return null;
  }
  
  public function is_blocked(){
    return $this->status == 0;
  }
  public function loadPermissions($force = false){
    if(!$force && isset($this->_permissions)){
      return $this->_permissions;
    }
    $ci = &get_instance();
    $ci->load->model('Permission_model');
    if($this->type == 'webadmin'){
      $this->_permissions = array();
    } elseif($this->type == 'admin'){
      $this->_permissions = $ci->Permission_model->role_permissions[$this->role];
    } elseif($this->type == 'customer'){
      $this->_permissions = array(
        'frontend-login' => true,
        'frontend-account-profile-access' => true,
        'frontend-account-profile-save' => true,
      );
    }
  }
  public function checkPermissions($permissions, $type='all'){
    if($this->is_blocked()){
      return false;
    }
    if($this->type == 'webadmin'){
      return true;
    }
    $this->loadPermissions();
    foreach($permissions as $permission){
      if(is_array($permission)){
        $allowed = $this->checkPermissions($permission, $type);
      } elseif(is_string($permission)) {
        $allowed = isset($this->_permissions[$permission]) && $this->_permissions[$permission];
      } else {
        throw new Exception('Invalid parameter type');
      }
      if($type === 'all' && !$allowed){
        return false;
      }
      if($type === 'any' && $allowed){
        return true;
      }
    }
    if($type === 'all'){
      return true;
    }
    if($type === 'any'){
      return false;
    }
    return false;
  }
  public function can(){
    return $this->checkPermissions(func_get_args(),'all');
  }
  public function canAny(){
    return $this->checkPermissions(func_get_args(),'any');
  }
  public function checkPermissionsUnder($permission_group, $type='all', $nesting = 0){
    if($this->is_blocked()){
      return false;
    }
    if($this->type == 'webadmin'){
      return true;
    }
    $ci = &get_instance();
    $ci->load->model('Permission_model');
    $group_permissions = isset($ci->Permission_model->permissions_groups[$permission_group]) ? $ci->Permission_model->permissions_groups[$permission_group] : array();
    $permissions = array();
    foreach($group_permissions as $permission){
      if(strpos($permission,$permission_group . '-') === 0){
        $can = $this->can($permission);
        if(!$can && $nesting){
          $can = $this->checkPermissionsUnder($permission, $type, $nesting-1);
        }
        if($type == 'any' && $can){
          return true;
        }
        if($type == 'all' && !$can){
          return false;
        }
      }
    }
    if($type == 'any'){
      return false;
    }
    if($type == 'all'){
      return true;
    }
    return false;
  }
  public function canUnder($args, $type='all', $nesting=0){
    if(!is_array($args)){
      $args = (array)$args;
    }
    foreach($args as $arg){
      if(is_array($arg)){
        foreach($arg as $sub){
          $can = $this->canUnder($sub, $type, $nesting);
          if($type=='any' && $can){
            return true;
          }
          if($type=='all' && !$can){
            return false;
          }
        }
      } elseif(is_string($arg)){
        $can = $this->checkPermissionsUnder($arg,$type,$nesting);
        if($type=='any' && $can){
          return true;
        }
        if($type=='all' && !$can){
          return false;
        }
      }
    }
    if($type=='any'){
      return false;
    }
    if($type=='all'){
      return true;
    }
  }
  public function canAnyUnder(){
    return $this->canUnder(func_get_args(),'any');
  }
  public function canAllUnder(){
    return $this->canUnder(func_get_args(),'all');
  }
  
  public function getFlightSpecialAssistance(){
    if(isset($this->exploded_flight_special_assistance)){
      return $this->exploded_flight_special_assistance;
    }
    $this->exploded_flight_special_assistance = array();
    if($this->flight_special_assistance){
      $allowed_special_assistances = array(
        'blind',
        'deaf',
        'wheelchair',
        'baby',
        'baggage',
        'sports',
      );
      $special_assistances = explode(',', $this->flight_special_assistance);
      
      $this->exploded_flight_special_assistance = array_intersect($allowed_special_assistances,$special_assistances);
    }
    
    return $this->exploded_flight_special_assistance;
  }
  public function getFellows(){
    if(isset($this->unserialized_fellows)){
      return $this->unserialized_fellows;
    }
    $this->unserialized_fellows = array();
    if($this->fellows){
      $fellows = unserialize($this->fellows);
      if(!$fellows){
        $fellows = array();
      } else {
        foreach ($fellows as &$fellow){
          $date = DateTime::createFromFormat('Y-m-d', $fellow->birth_date);
          $fellow->birth_date = $date->format('d.m.Y');
        }
      }
      $this->unserialized_fellows = $fellows;
    }
    
    return $this->unserialized_fellows;
  }
  public function getFlightDepartureAirport(){
    if(isset($this->unserialized_flight_departure_airport)){
      return $this->unserialized_flight_departure_airport;
    }
    $this->unserialized_flight_departure_airport = (object)array(
      'location_id' => 0,
      'location_code' => '',
      'city_id' => 0,
      'city_code' => '',
      'country_id' => 0,
      'city_name' => '',
      'country_name' => '',
      'location_name' => '',
    );
    
    if($this->flight_departure_airport){
      $flight_departure_airport = unserialize($this->flight_departure_airport);
      if($flight_departure_airport){
        foreach ($flight_departure_airport as $k => $v){
          if(!property_exists($this->unserialized_flight_departure_airport,$k)){
            continue;
          }
          if(!(is_numeric($v) || is_string($v))){
            continue;
          }
          $this->unserialized_flight_departure_airport->$k = ''.$v;
        }
      }
    }
    
    return $this->unserialized_flight_departure_airport;
  }
  public function getBirthDate(){
    if(isset($this->formatted_birth_date)){
      return $this->formatted_birth_date;
    }
    $this->formatted_birth_date = '';
    if(strlen($this->birth_date)){
      $date = DateTime::createFromFormat('Y-m-d', $this->birth_date);
      $this->formatted_birth_date = $date->format('d.m.Y');
    }
    return $this->formatted_birth_date;
  }
  public function getSocialLoginNetworks(){
    if(isset($this->exploded_social_login_networks)){
      return $this->exploded_social_login_networks;
    }
    $this->exploded_social_login_networks = array();
    if($this->social_login){
      $ci = &get_instance();
      $ci->load->model('Options_model');
      $allowed_social_networks = $ci->Options_model->getKeys('social_networks_status');
      $special_assistances = explode(',', $this->social_login);
      
      $this->exploded_social_login_networks = array_intersect($allowed_social_networks,$special_assistances);
    }
    
    return $this->exploded_social_login_networks;
  }
  public function getFullName(){
    return $this->firstname . ' ' . $this->lastname;
  }
}