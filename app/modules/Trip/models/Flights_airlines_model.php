<?php

class Flights_Airlines_Model extends CI_Model {
  function applyFilters($filters = array()) {
    if(isset($filters['status'])){
      $this->db->where_in('status', (array)$filters['status']);
    }
    if(isset($filters['search']) && $filters['search'] !== ''){
      $search = $filters['search'];
      // $this->db->group_start();
      $this->db->or_like(array(
          'name' => $search, 
          'original_name' => $search, 
          'code' => $search, 
      ));
      // $this->db->group_end();
    }
    if(isset($filters['code'])){
      $codes = (array)$filters['code'];
      if(!empty($codes)){
        $this->db->where_in('tfa.code', $codes);
      }
    }
  }
  function getAirlines($filters = array()) {
    if(isset($filters['select']) && $filters['select']){
      $this->db->select($filters['select']);
    } else {
      $this->db->select('tfa.code, tfa.name, tfa.image');
    }
    $this->applyFilters($filters);
    
    if(isset($filters['ordering']) && $filters['ordering']){
      list($sort_by,$sort_order) = explode(' ',$filters['ordering']);
      $sort_order = strtolower($sort_order);
      $sort_by = strtolower($sort_by);
      if(!in_array($sort_order,array(
        'asc',
        'desc'
      ))){
        $sort_order = false;
      }
      if($sort_order && $sort_by){
        $this->db->order_by($sort_by, $sort_order);
      }
    }
    
    $page = isset($filters['page']) && (int)$filters['page'] > 1 ? (int)$filters['page']: 1;
    $limit = isset($filters['limit']) && (int)$filters['limit'] > 0 ? (int)$filters['limit']: null;
    $offset = 0;
    if($limit > 0){
      $offset = ($page - 1) * $limit;
    }

    $q = $this->db->get('trip_flights_airlines tfa', $limit, $offset);
    if(isset($filters['return_query']) && $filters['return_query']){
      return $q;
    }
    if(isset($filters['return_row']) && $filters['return_row']){
      return $q->row();
    }
    return $q->result();
  }
  function getAirlineByCode($code,$filters = array()) {
    $filters['code'] = $code;
    $filters['return_row'] = true;
    return $this->getAirlines($filters);
  }
  function getTotalAirlines($filters = array()) {
    if(isset($filters['select']) && $filters['select']){
      $this->db->select($filters['select']);
    } else {
      $this->db->select('COUNT(code) as total');
    }
    $this->applyFilters($filters);
    if(isset($filters['return_query']) && $filters['return_query']){
      return $q;
    }
    $q = $this->db->get('trip_flights_airlines');
    if(isset($filters['return_row']) && $filters['return_row']){
      return $q->row();
    }
    if(isset($filters['return_rows']) && $filters['return_rows']){
      return $q->result();
    }
    $row = $q->row();
    if($row){
      return $row->total;
    }
    return 0;
  }
  function addAirline($data) {
    $this->db->insert('trip_flights_airlines', $data);
  }
  function saveAirline($data) {
    $this->db->where('code', $data['code']);
    $this->db->update('trip_flights_airlines', $data);
  }
}