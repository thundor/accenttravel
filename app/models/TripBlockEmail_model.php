<?php

class TripBlockEmail_model extends CI_Model {
  function getBlockEmailById($id, $filters=array()) {
    $filters['id'] = $id;
    return $this->getBlockEmail($filters);
  }
  function getBlockEmailByCode($code, $filters=array()) {
    $filters['code'] = $code;
    return $this->getBlockEmail($filters);
  }
  function getBlockEmail($filters=array()) {
    $subscribers = $this->getBlockEmails($filters);
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
        'code' => $search, 
      ));
      $this->db->group_end();
    }
    if(isset($filters['code'])){
      $codes = (array)$filters['code'];
      if(!empty($codes)){
        $this->db->where_in('code', $codes);
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
  function getBlockEmails($filters = array()) {
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

    $q = $this->db->get('trip_blockemail', $limit, $offset);
    if(isset($filters['return_query']) && $filters['return_query']){
      return $q;
    }
    if(isset($filters['return_row']) && $filters['return_row']){
      return $q->row();
    } 
    return $q->result();
  }
  function getTotalBlockEmails($filters = array()) {
    $this->db->select('COUNT(id) as total');
    $this->applyFilters($filters);
    $q = $this->db->get('trip_blockemail');
    $result = $q->result();
    $num = $q->num_rows();
    if ($num > 0) {
      return $result[0]->total;
    }
    return 0;
  }
  function saveBlockEmail($data) {
    if(isset($data['id']) && $data['id']){
      $this->db->where('id', $data['id']);
      $this->db->update('trip_blockemail', $data);
      $subscriber_id = $data['id'];
    } else {
      $this->db->insert('trip_blockemail', $data);
      $subscriber_id = $this->db->insert_id();
    }
    return $subscriber_id;
  }
  function getValidBlockEmailHotel($blockemail_code) {
    return getValidBlockEmail($blockemail_code, 'hotel');
  }
  function getValidBlockEmailPackage($blockemail_code) {
    return getValidBlockEmail($blockemail_code, 'package');
  }
  function getValidBlockEmail($blockemail_code, $type='') {
    $this->db->select('*');
    $this->db->where('status', 1);
    $this->db->where('code', $blockemail_code);
    $this->db->where("(max_uses IS NULL OR nr_uses <= max_uses)");
    $this->db->where("(date_start IS NULL OR date_start <= '" . date('Y-m-d') . "')");
    $this->db->where("(date_expire IS NULL OR date_expire >= '" . date('Y-m-d') . "')");
    if($type){
      if(in_array($type, array('hotel', 'package'))){
        $this->db->where("`$type` = 1");
      } else {
        return false;
      }
    }
    $q = $this->db->get('trip_blockemail', 1, 0);
    if(!$q->num_rows()){
      return false;
    }
    return $q->row();
  }
  function useBlockEmail($blockemail_code) {
    $this->db->where('code', $blockemail_code);
    $this->db->set('nr_uses', 'nr_uses+1', false);
    $this->db->update('trip_blockemail');
  }
  function unUseBlockEmail($blockemail_code) {
    $this->db->where('code', $blockemail_code);
    $this->db->set('nr_uses', 'IF(nr_uses-1>=0, nr_uses-1, 0)', false);
    $this->db->update('trip_blockemail');
  }
  function deleteBlockEmailById($id) {
    $this->db->where('id', $id);
    $this->db->set('status', -2);
    $this->db->update('trip_blockemail');
  }
  function publishBlockEmailById($id) {
    $this->db->where('id', $id);
    $this->db->set('status', 1);
    $this->db->update('trip_blockemail');
  }
  function unpublishBlockEmailById($id) {
    $this->db->where('id', $id);
    $this->db->set('status', 0);
    $this->db->update('trip_blockemail');
  }
}