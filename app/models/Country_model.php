<?php

class Country_model extends CI_Model {
  function getCountryById($id, $filters=array()) {
    $filters['id'] = $id;
    return $this->getCountry($filters);
  }
  function getCountryByIso2($iso2, $filters=array()) {
    $filters['iso_2'] = $iso2;
    return $this->getCountry($filters);
  }
  function getCountryByIso3($iso3, $filters=array()) {
    $filters['iso_3'] = $iso3;
    return $this->getCountry($filters);
  }
  function getCountry($filters=array()) {
    $countries = $this->getCountries($filters);
    if($countries){
      return $countries[0];
    }
    return false;
  }
  function applyFilters($filters = array()) {
    if(isset($filters['status'])){
      $this->db->where_in('status', (array)$filters['status']);
    }
    if(isset($filters['search']) && $filters['search'] !== ''){
      $search = $filters['search'];
      $this->db->group_start();
      $this->db->or_like(array(
          'name' => $search, 
          'name_RO' => $search,
      ));
      $this->db->group_end();
    }
    if(isset($filters['trip_id'])){
      $trip_ids = (array)$filters['trip_id'];
      if(!empty($trip_ids)){
        $this->db->where_in('trip_id', $trip_ids);
      }
    }
    if(isset($filters['aida_id'])){
      $aida_ids = (array)$filters['aida_id'];
      if(!empty($aida_ids)){
        $this->db->where_in('aida_id', $aida_ids);
      }
    }
    if(isset($filters['iso_2'])){
      $iso_2s = (array)$filters['iso_2'];
      if(!empty($iso_2s)){
        $this->db->where_in('iso_2', $iso_2s);
      }
    }
    if(isset($filters['iso_3'])){
      $iso_3s = (array)$filters['iso_3'];
      if(!empty($iso_3s)){
        $this->db->where_in('iso_3', $iso_3s);
      }
    }
    if(isset($filters['status'])){
      $status = (array)$filters['status'];
      if(!empty($status)){
        $this->db->where_in('status', $status);
      }
    }
    if(isset($filters['id'])){
      $ids = (array)$filters['id'];
      if(!empty($ids)){
        $this->db->where_in('country_id', $ids);
      }
    }
  }
  function getCountries($filters = array()) {
    $fields = array('country_id', 'status', 'iso_2', 'iso_3', 'name', 'name_RO', 'currency_code', 'phone_prefix_numeric', 'phone_prefix', 'fips', 'iso_numeric', 'north', 'south', 'east', 'west', 'capital', 'continent_name', 'continent', 'population', 'area', 'gdp_value', 'gdp_unit', 'languages', 'geonameId', 'trip_id', 'aida_id', 'created_by', 'time_created', 'modified_by', 'time_modified');
    if(isset($filters['select']) && $filters['select']){
      $this->db->select($filters['select']);
    } else {
      $this->db->select($fields);
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

    $q = $this->db->get('ac_country', $limit, $offset);
    if(isset($filters['return_query']) && $filters['return_query']){
      return $q;
    }
    if(isset($filters['return_row']) && $filters['return_row']){
      return $q->row();
    } elseif(isset($filters['return_rows']) && $filters['return_rows']){
      return $q->result();
    } else {
      $this->load->library('Country');
      if(isset($filters['return_result']) && $filters['return_result']){
        return $q->row('Country');
      }
      return $q->result('Country');
    }
  }
  function getTotalCountries($filters = array()) {
    $this->db->select('COUNT(country_id) as total');
    $this->applyFilters($filters);
    $q = $this->db->get('ac_country');
    $result = $q->result();
    $num = $q->num_rows();
    if ($num > 0) {
      return $result[0]->total;
    }
    return 0;
  }
  function saveCountry($data) {
    if($data['country_id']){
      $this->db->where('country_id', $data['country_id']);
      $this->db->update('ac_country', $data);
      return $data['country_id'];
    } else {
      $this->db->insert('ac_country', $data);
      return $this->db->insert_id();
    }
  }
  function deleteCountryById($country_id, $filters = array()) {
    $filters['id'] = $country_id;
    $this->deleteCountry($filters);
  }
  function deleteCountry($filters = array()) {
    $this->applyFilters($filters);
    $this->db->delete('ac_country');
  }
}