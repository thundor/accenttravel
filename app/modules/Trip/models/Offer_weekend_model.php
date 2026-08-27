<?php

class Offer_weekend_Model extends CI_Model {
  function applyFilters($filters = array()) {
    if(isset($filters['status'])){
      $this->db->where_in('status', (array)$filters['status']);
    }
    if(isset($filters['search']) && $filters['search'] !== ''){
      $search = $filters['search'];
      $this->db->like(array(
          'name' => $search, 
      ));
    }
    if(isset($filters['id'])){
      $ids = (array)$filters['id'];
      if(!empty($ids)){
        $this->db->where_in('id', $ids);
      }
    }
    if(isset($filters['type_id'])){
      $type_ids = (array)$filters['type_id'];
      if(!empty($type_ids)){
        $this->db->where_in('type_id', $type_ids);
      }
    }
    if(isset($filters['type'])){
      $types = (array)$filters['type'];
      if(!empty($types)){
        $this->db->where_in('type', $types);
      }
    }
    if(isset($filters['zone'])){
      $zones = (array)$filters['zone'];
      if(!empty($zones)){
        $this->db->where_in('zone', $zones);
      }
    }
  }
  function getOffers($filters = array()) {
    if(isset($filters['select']) && $filters['select']){
      $this->db->select($filters['select']);
    } else {
      $this->db->select('*');
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

    $q = $this->db->get('trip_offer_weekend', $limit, $offset);
    if(isset($filters['return_query']) && $filters['return_query']){
      return $q;
    }
    if(isset($filters['return_row']) && $filters['return_row']){
      return $q->row();
    }
    return $q->result();
  }
  function getTotalOffers($filters = array()) {
    if(isset($filters['select']) && $filters['select']){
      $this->db->select($filters['select']);
    } else {
      $this->db->select('COUNT(id) as total');
    }
    $this->applyFilters($filters);
    if(isset($filters['return_query']) && $filters['return_query']){
      return $q;
    }
    $q = $this->db->get('trip_offer_weekend');
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
  
  function addOffer($data) {
    $data['id'] = null;
    $this->db->insert('trip_offer_weekend', $data);
    return $this->db->insert_id();
  }
  function updateOffer($data) {
    $this->db->where('id', $data['id']);
    $this->db->update('trip_offer_weekend', $data);
  }
  function deleteOfferById($id, $filters = array()) {
    $filters['id'] = $id;
    $this->deleteOffer($filters);
  }
  function deleteOfferByZone($zone, $filters = array()) {
    $filters['zone'] = $zone;
    $this->deleteOffer($filters);
  }
  function deleteOffer($filters = array()) {
    $this->applyFilters($filters);
    $this->db->delete('trip_offer_weekend');
  }
}