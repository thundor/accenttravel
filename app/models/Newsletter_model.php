<?php

class Newsletter_model extends CI_Model {
  function getSubscriberById($id, $filters=array()) {
    $filters['id'] = $id;
    return $this->getSubscriber($filters);
  }
  function getSubscriber($filters=array()) {
    $subscribers = $this->getSubscribers($filters);
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
        'email' => $search, 
      ));
      $this->db->group_end();
    }
    if(isset($filters['user_id'])){
      $user_ids = (array)$filters['user_id'];
      if(!empty($user_ids)){
        $this->db->where_in('ns.user_id', $user_ids);
      }
    }
    if(isset($filters['id'])){
      $ids = (array)$filters['id'];
      if(!empty($ids)){
        $this->db->where_in('ns.id', $ids);
      }
    }
    if(isset($filters['except_id'])){
      $except_ids = (array)$filters['except_id'];
      if(!empty($except_ids)){
        $this->db->where_not_in('ns.id', $except_ids);
      }
    }
  }
  function getSubscribers($filters = array()) {
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

    $q = $this->db->get('ac_newsletter ns', $limit, $offset);
    if(isset($filters['return_query']) && $filters['return_query']){
      return $q;
    }
    if(isset($filters['return_row']) && $filters['return_row']){
      return $q->row();
    } 
    return $q->result();
  }
  function getTotalSubscribers($filters = array()) {
    $this->db->select('COUNT(ns.id) as total');
    $this->applyFilters($filters);
    $q = $this->db->get('ac_newsletter ns');
    $result = $q->result();
    $num = $q->num_rows();
    if ($num > 0) {
      return $result[0]->total;
    }
    return 0;
  }
  function saveSubscriber($data) {
    if(isset($data['id']) && $data['id']){
      $this->db->where('id', $data['id']);
      $this->db->update('ac_newsletter', $data);
      $subscriber_id = $data['id'];
    } else {
      $this->db->insert('ac_newsletter', $data);
      $subscriber_id = $this->db->insert_id();
    }
    return $subscriber_id;
  }
  function deleteSubscriberById($id) {
    $this->db->where('id', $id);
    $this->db->delete('ac_newsletter');
  }
}