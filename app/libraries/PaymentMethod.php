<?php
class PaymentMethod {
  public $id = '';
  public $user_id = '';
  public $card_number = '';
  public $card_name = '';
  public $card_exp_date = '';
  public $country = '';
  public $city = '';
  public $address = '';
  public $zipcode = '';
  
  public function __set($name, $value){
    if(strpos($name,'paymeth_') === 0){
      $property_name = substr($name,8);
    }
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