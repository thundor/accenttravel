<?php

class Trip_Cities_Model extends CI_Model {
  function applyFilters($filters = array()) {
    if(isset($filters['status'])){
      $this->db->where_in('status', (array)$filters['status']);
    }
    if(isset($filters['search']) && $filters['search'] !== ''){
      $search = $filters['search'];
      // $this->db->group_start();
      $this->db->or_like(array(
          'name' => $search,
          'description' => $search,
      ));
      // $this->db->group_end();
    }
    if(isset($filters['id'])){
      $ids = (array)$filters['id'];
      if(!empty($ids)){
        $this->db->where_in('id', $ids);
      }
    }
    if(isset($filters['parent_id'])){
      $parent_ids = (array)$filters['parent_id'];
      if(!empty($parent_ids)){
        $this->db->where_in('parent_id', $parent_ids);
      }
    }
  }
  function getCities($filters = array()) {
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

    $q = $this->db->get('trip_cities', $limit, $offset);
    if(isset($filters['return_query']) && $filters['return_query']){
      return $q;
    }
    if(isset($filters['return_row']) && $filters['return_row']){
      return $q->row();
    }
    return $q->result();
  }
  function getCityById($id,$filters = array()) {
    $filters['id'] = $id;
    $filters['return_row'] = true;
    return $this->getCities($filters);
  }
  function getTotalCities($filters = array()) {
    if(isset($filters['select']) && $filters['select']){
      $this->db->select($filters['select']);
    } else {
      $this->db->select('COUNT(id) as total');
    }
    $this->applyFilters($filters);
    if(isset($filters['return_query']) && $filters['return_query']){
      return $q;
    }
    $q = $this->db->get('trip_cities');
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
  function saveCity($data) {
    if(isset($data['id']) && $data['id']){
      $this->db->where('id', $data['id']);
      $this->db->update('trip_cities', $data);
      return $data['id'];
    } else {
      $this->db->insert('trip_cities', $data);
      return $this->db->insert_id();
    }
  }
}