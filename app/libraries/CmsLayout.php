<?php
class CMSLayout {
  public $slug = '';
  public $name = '';
  public $author = '';
  public $version = '1.0.0';
  public $created_datetime = null;
  
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