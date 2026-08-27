<?php

class Offer_popular_Model extends CI_Model {
  function applyFilters($filters = array()) {
    if(isset($filters['id'])){
      $ids = (array)$filters['id'];
      if(!empty($ids)){
        $this->db->where_in('id', $ids);
      }
    }
    if(isset($filters['code'])){
      $codes = (array)$filters['code'];
      if(!empty($codes)){
        $this->db->where_in('code', $codes);
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

    $q = $this->db->get('trip_offer_popular', $limit, $offset);
    if(isset($filters['return_query']) && $filters['return_query']){
      return $q;
    }
    if(isset($filters['return_row']) && $filters['return_row']){
      return $q->row();
    }
    return $q->result();
  }
  
  function addOffer($data) {
    $data['id'] = null;
    $this->db->insert('trip_offer_popular', $data);
    return $this->db->insert_id();
  }
  function updateOffer($data) {
    $this->db->where('id', $data['id']);
    $this->db->update('trip_offer_popular', $data);
  }
  function deleteOfferById($id, $filters = array()) {
    $filters['id'] = $id;
    $this->deleteOffers($filters);
  }
  function deleteOffersByCode($code, $filters = array()) {
    $filters['code'] = $code;
    $this->deleteOffers($filters);
  }
  function deleteOffers($filters = array()) {
    $this->applyFilters($filters);
    $this->db->delete('trip_offer_popular');
  }
}