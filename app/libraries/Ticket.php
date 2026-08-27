<?php
class Ticket {
  public $id;
  public $trip_order_id;
  public $user_id;
  public $type;
  public $message;
  public $created_by;
  public $time_created;
  public $updated_by;
  public $time_updated;
  public $modified_by;
  public $time_modified;
  public $last_history_id;
  public $first_history_id;
  
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