<?php

class Flights_Insurance_Travel_Model extends CI_Model {
  function __construct() {
    parent::__construct();
  }
  function getInsurances($filters = array()) {
    extract($filters);
    $this->db->select('tfa.code, tfa.name, tfa.image');
    if(isset($code) && $code){
      $this->db->where_in('tfa.code', $code);
    }
    $q = $this->db->get('trip_flights_insurance_travel tfit');
    if($q->num_rows()){
      return $q->result();
    }
    return array();
  }
}