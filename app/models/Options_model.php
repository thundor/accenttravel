<?php

class Options_model extends CI_Model {
  public $serialize_options = array(
    -2, // 1 or 0/true or false
    -1, // implode/explode (,)
    0, // database exact value (string)
    1, // serialize/unserialize
    2, // json_encode/json_decode (default)
    3, // json_encode/json_decode (decode as array), but saved in the database as 2
  );
  function get($code,$key=null,$default_values = null, $filters=array()) {
    if(isset($filters['select']) && $filters['select']){
      $this->db->select($filters['select']);
    } else {
      $this->db->select('*');
    }
    $this->db->where('option_code', $code);
    if(is_null($key) && is_array($default_values)){
      $key = array_keys($default_values);
    }
    if(!is_null($key)){
      if(is_array($key)){
        $this->db->where_in('option_key', $key);
      } else {
        $this->db->where('option_key', $key);
      }
    } else {
      $this->db->order_by('option_key', 'asc');
    }
    
    $q = $this->db->get('ac_option');
    if(isset($filters['return_query']) && $filters['return_query']){
      return $q;
    }
    $result = $q->result();
    if(isset($filters['return_rows']) && $filters['return_rows']){
      return $result;
    }
    $num = $q->num_rows();
    
    if ($num > 0) {
      $options = array();
      foreach($result as $item){
        $serialized = (int)$item->option_serialized;
        $value = $this->interpretValue($item->option_value, $serialized);
        $options[$item->option_key] = $value;
      }
      if(!is_null($key) && !is_array($key)){
        return $this->adaptValue($options[$key],$default_values);
      }
      if(is_array($default_values)){
        foreach($default_values as $k=>$v){
          $option_value = isset($options[$k]) ? $options[$k] : null;
          $options[$k] = $this->adaptValue($option_value,$v);
        }
      }
      return $options;
    }
    return $default_values;
  }
  function adaptValue($option_value, $default_value) {
    if(is_null($default_value)){
      return $option_value;
    } elseif(is_null($option_value)){
      return $default_value;
    } elseif(is_bool($default_value)){
      return $option_value ? true : false;
    } elseif(is_numeric($default_value) || is_string($default_value)){
      if(is_numeric($option_value) || is_string($option_value) || is_bool($option_value)){
        return $option_value;
      }
      return $default_value;
    } elseif(is_array($default_value) && is_object($option_value)){
      return array_replace_recursive($default_value, (array)$option_value);
    } elseif(is_object($default_value) && is_array($option_value)){
      return (object)array_replace_recursive((array)$default_value, $option_value);
    }
    return $option_value;
  }
  function serializeValue($value, &$serialize = null) {
    if(is_null($serialize)){
      if(is_object($value) || is_array($value)){
        $serialize = 1;
      } else {
        $serialize = 0;
      }
    }
    switch($serialize){
      case -2: 
        return $value ? 1 : 0;
      case -1: 
        return implode(',',$value);
      case 1: 
        return serialize($value);
      case 2:
      case 3:
        $serialize = 2;
        return json_encode($value);
      default: 
        return $value;
    }
  }
  function interpretValue($value, $serialize = 0) {
    switch($serialize){
      case -2: 
        return $value ? true : false;
      case -1: 
        return explode(',',$value);
      case 1: 
        return unserialize($value);
      case 2: 
        return json_decode($value);
      case 3: 
        return json_decode($value,true);
      default: 
        return $value;
    }
  }
  function set($code, $options, $serialize_options = array()) {
    foreach($options as $k=>$v){
      $serialized = isset($serialize_options[$k]) ? $serialize_options[$k] : null;
      $this->setValue($code, $k, $v, $serialized);
    }
  }
  function setValue($code, $key, $value, $serialized=null) {
    $option_value = $this->serializeValue($value,$serialized);
    if(is_null($option_value)){
      $this->deleteValue($code,$key);
      return;
    }
    $this->insertValue($code, $key, $option_value, $serialized);
  }
  function deleteValue($code, $key) {
    $this->db->where('option_code', $code);
    $this->db->where('option_key', $key);
    $this->db->delete('ac_option');
  }
  function insertValue($code, $key, $value, $serialized) {
    $item = array(
      'option_code' => $code,
      'option_key' => $key,
      'option_value' => $value,
      'option_serialized' => $serialized,
    );
    $sql = $this->db->insert_string('ac_option', $item) . " ON DUPLICATE KEY UPDATE `option_value` = VALUES(`option_value`), `option_serialized` = VALUES(`option_serialized`)";
    $this->db->query($sql);
  }
  function getKeys($code) {
    $options = $this->get($code,null,null,array(
      'select' => 'option_key',
      'return_rows' => true,
    ));
    $keys = array();
    if($options){
      foreach($options as $option){
        $keys[] = $option->option_key;
      }
    }
    return $keys;
  }
}