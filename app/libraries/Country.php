<?php
class Country {
  public $id;
  public $status;
  public $iso_2;
  public $iso_3;
  public $name;
  public $name_RO;
  public $name_EN;
  public $currency_code;
  public $phone_prefix_numeric;
  public $phone_prefix;
  public $fips;
  public $iso_numeric;
  public $north;
  public $south;
  public $east;
  public $west;
  public $capital;
  public $continent_name;
  public $continent;
  public $population;
  public $area;
  public $gdp_value;
  public $gdp_unit;
  public $languages;
  public $geonameId;
  public $trip_id;
  public $created_by;
  public $time_created;
  public $modified_by;
  public $time_modified;
  
  public function __set($name, $value){
    if(strpos($name,'country_') === 0){
      $property_name = substr($name,8);
    }
    $property_name = $name;
    if($property_name === 'name'){
      if(is_null($this->name_EN)){
        $this->name_EN = $value;
      }
    }
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