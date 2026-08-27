<?php

class TripDiscount_model extends CI_Model {
  function getDiscountById($id, $filters=array()) {
    $filters['id'] = $id;
    return $this->getDiscount($filters);
  }
  function getDiscount($filters=array()) {
    $subscribers = $this->getDiscounts($filters);
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
      $search = $filters['search'];
      $this->db->group_start();
      $this->db->or_like(array(
        'type_id' => $search, 
        'name' => $search, 
      ));
      $this->db->group_end();
    }
    if(isset($filters['type'])){
      $types = (array)$filters['type'];
      if(!empty($types)){
        $this->db->where_in('type', $types);
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
  function getDiscounts($filters = array()) {
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

    $q = $this->db->get('trip_discount', $limit, $offset);
    if(isset($filters['return_query']) && $filters['return_query']){
      return $q;
    }
    if(isset($filters['return_row']) && $filters['return_row']){
      return $q->row();
    } 
    return $q->result();
  }
  function getTotalDiscounts($filters = array()) {
    $this->db->select('COUNT(id) as total');
    $this->applyFilters($filters);
    $q = $this->db->get('trip_discount');
    $result = $q->result();
    $num = $q->num_rows();
    if ($num > 0) {
      return $result[0]->total;
    }
    return 0;
  }
  function saveDiscount($data) {
    if(isset($data['id']) && $data['id']){
      $this->db->where('id', $data['id']);
      $this->db->update('trip_discount', $data);
      $subscriber_id = $data['id'];
    } else {
      $this->db->insert('trip_discount', $data);
      $subscriber_id = $this->db->insert_id();
    }
    return $subscriber_id;
  }
  function getTypeDiscountsAssoc($type,$type_ids,$default=0) {
    $discounts = $this->getTypeDiscounts($type, $type_ids);
    
    $discounts_formatted = array();
    foreach($type_ids as $type_id){
      $discounts_formatted[$type_id] = $default;
    }
    foreach($discounts as $discount){
      $discounts_formatted[$discount->type_id] = $discount->percentage;
    }
    return $discounts_formatted;
  }
  function getTypeDiscounts($type,$type_ids) {
    if(!$type_ids){
      return array();
    }
    $this->db->select('type_id');
    $this->db->select('MAX(percentage) as percentage');
    $this->db->where('status', 1);
    $this->db->where('type', $type);
    $this->db->where_in('type_id', $type_ids);
    $this->db->where("(date_start IS NULL OR date_start <= '" . date('Y-m-d') . "')");
    $this->db->where("(date_expire IS NULL OR date_expire >= '" . date('Y-m-d') . "')");
    $this->db->group_by('type_id');
    $q = $this->db->get('trip_discount');
    if(!$q->num_rows()){
      return array();
    }
    return $q->result();
  }
  function getTypeDiscount($type,$type_id) {
    if(!$type_id){
      return array();
    }
    $discounts = $this->getTypeDiscounts($type, array($type_id));
    if(!$discounts){
      return false;
    }
    return floatval($discounts[0]->percentage);
  }
  function deleteDiscountById($id) {
    $this->db->where('id', $id);
    $this->db->set('status', -2);
    $this->db->update('trip_discount');
  }
  function publishDiscountById($id) {
    $this->db->where('id', $id);
    $this->db->set('status', 1);
    $this->db->update('trip_discount');
  }
  function unpublishDiscountById($id) {
    $this->db->where('id', $id);
    $this->db->set('status', 0);
    $this->db->update('trip_discount');
  }
}