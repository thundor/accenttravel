<?php

class TripRequestoffer_model extends CI_Model {
  function getRequestById($id, $filters=array()) {
    $filters['id'] = $id;
    return $this->getRequest($filters);
  }
  function getRequestByCode($code, $filters=array()) {
    $filters['code'] = $code;
    return $this->getRequest($filters);
  }
  function getRequest($filters=array()) {
    $subscribers = $this->getRequests($filters);
    if($subscribers){
      return $subscribers[0];
    }
    return false;
  }
  function applyFilters($filters = array()) {
    if(isset($filters['status'])){
      $this->db->where_in('status', (array)$filters['status']);
    }
    if(isset($filters['search']) && $filters['search'] !== ''){
      $filters['join_content'] = true;
      $search = $filters['search'];
      $this->db->group_start();
      $this->db->or_like(array(
        'title' => $search, 
        'email' => $search, 
        'phone' => $search, 
      ));
      $this->db->group_end();
    }
    if(isset($filters['code'])){
      $codes = (array)$filters['code'];
      if(!empty($codes)){
        $this->db->where_in('code', $codes);
      }
    }
    if(isset($filters['hash'])){
      $hashs = (array)$filters['hash'];
      if(!empty($hashs)){
        $this->db->where_in('hash', $hashs);
      }
    }
    if(isset($filters['email'])){
      $emails = (array)$filters['email'];
      if(!empty($emails)){
        $this->db->where_in('email', $emails);
      }
    }
    if(isset($filters['id'])){
      $ids = (array)$filters['id'];
      if(!empty($ids)){
        $this->db->where_in('id', $ids);
      }
    }
    if(isset($filters['except_id'])){
      $except_ids = (array)$filters['except_id'];
      if(!empty($except_ids)){
        $this->db->where_not_in('id', $except_ids);
      }
    }
  }
  function getRequests($filters = array()) {
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
      }
      $this->db->order_by($sort_by, $sort_order);
    }
    
    $page = isset($filters['page']) && (int)$filters['page'] > 1 ? (int)$filters['page']: 1;
    $limit = isset($filters['limit']) && (int)$filters['limit'] > 0 ? (int)$filters['limit']: null;
    $offset = 0;
    if($limit > 0){
      $offset = ($page - 1) * $limit;
    }

    $q = $this->db->get('trip_request_offer', $limit, $offset);
    if(isset($filters['return_query']) && $filters['return_query']){
      return $q;
    }
    if(isset($filters['return_row']) && $filters['return_row']){
      return $q->row();
    } 
    return $q->result();
  }
  function getTotalRequests($filters = array()) {
    $this->db->select('COUNT(id) as total');
    $this->applyFilters($filters);
    $q = $this->db->get('trip_request_offer');
    $result = $q->result();
    $num = $q->num_rows();
    if ($num > 0) {
      return $result[0]->total;
    }
    return 0;
  }
  function saveRequest($data) {
    if(isset($data['id']) && $data['id']){
      $this->db->where('id', $data['id']);
      $this->db->update('trip_request_offer', $data);
      $subscriber_id = $data['id'];
    } else {
      $this->db->insert('trip_request_offer', $data);
      $subscriber_id = $this->db->insert_id();
    }
    return $subscriber_id;
  }
  function deleteRequestById($id) {
    $this->db->where('id', $id);
    $this->db->delete('trip_request_offer');
  }
}