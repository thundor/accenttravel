<?php
class TripOrder {
  public $id;
  public $status;
  public $user_id;
  public $type;
  public $created_by;
  public $time_created;
  public $modified_by;
  public $time_modified;
  public $provider = 'trip';
  public $user_invoice = 'pj';
  public $user_country = 'RO';
  public $user_phone_prefix = 'RO';
  public $user_title = 'mr';
  public $user_gender = 'm';
  
  public function __set($name, $value){
    $property_name = $name;
    $this->$property_name = $value;
  }

  public function __get($name){
    $property_name = $name; 
    if(property_exists($this,$property_name)){
      return $this->$property_name;
    }
    return null;
  }
}