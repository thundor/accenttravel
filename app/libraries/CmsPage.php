<?php
class CMSPage {
  public $page_id = 0;
  public $status = 1;
  public $created_by = null;
  public $time_created = null;
  public $modified_by = null;
  public $time_modified = null;
  
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