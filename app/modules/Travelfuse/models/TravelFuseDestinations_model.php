<?php

class TravelFuseDestinations_model extends CI_Model {
	function __construct() {
		parent::__construct();
		$this->load->driver('cache', array('adapter' => 'apc', 'backup' => 'file'));
	}
  function getById($id, $filters=array()) {
    $filters['id'] = $id;
    return $this->get($filters);
  }
  function getByCode($code, $filters=array()) {
    $filters['code'] = $code;
    return $this->get($filters);
  }
  static $cached_destinations;
  function clearCachedDestinations() {
	  static::$cached_destinations = null;
	  $cache_storage_path = 'travelfuse/';
	  $cache_hash = $cache_storage_path . 'destinations';
	  $this->cache->delete($cache_hash);
  }
  function getCachedDestinations() {
	  if(isset(static::$cached_destinations)){
		  return static::$cached_destinations;
	  }
	  
	  $cache_storage_path = 'travelfuse/';
	  $cache_hash = $cache_storage_path . 'destinations';
	  $cache_time = 0;
	  $response = $this->cache->get($cache_hash, $cache_time);

	  if(true || !is_array($response)){
		  $this->db->select('de.Name, de.status, COALESCE(de._name_ro, de._name_en, de.Name) as namefinal');
		  $this->db->where_in('de.status', (array)1);
		  $q = $this->db->get('tf_destinations de');
		  $response = $q->result('array');
		  
		setCacheStorage($cache_storage_path);
		$this->cache->save($cache_hash, $response, $cache_time);
	  }
	  
	  static::$cached_destinations = $response;
	  return static::$cached_destinations;
  }
  function get($filters=array()) {
    $destinations = $this->getList($filters);
    if($destinations){
      return $destinations[0];
    }
    return false;
  }
  function applyFilters($filters = array()) {
    if(isset($filters['status'])){
      $this->db->where_in('de.status', (array)$filters['status']);
    }
    if(isset($filters['search']) && $filters['search'] !== ''){
      $search = $filters['search'];
      $this->db->group_start();
      $this->db->or_like(array(
        'de._name_ro' => $search, 
        'de._name_en' => $search, 
        'de.Name' => $search, 
        // 'de.Code' => $search, 
      ));
      $this->db->group_end();
    }
    if(isset($filters['code'])){
      $codes = (array)$filters['code'];
      if(!empty($codes)){
        $this->db->where_in('cn.Code', $codes);
      }
    }
    if(isset($filters['id'])){
      $ids = (array)$filters['id'];
      if(!empty($ids)){
        $this->db->where_in('de.Id', $ids);
      }
    }
    if(isset($filters['except_id'])){
      $except_ids = (array)$filters['except_id'];
      if(!empty($except_ids)){
        $this->db->where_not_in('de.Id', $except_ids);
      }
    }
  }
  function getList($filters = array()) {
    if(isset($filters['select']) && $filters['select']){
      $this->db->select($filters['select']);
    } else {
		$this->db->select('de.*, COALESCE(de._name_ro, de._name_en, de.Name) as namefinal');
    }
    
    $this->applyFilters($filters);
    
    if(isset($filters['ordering']) && $filters['ordering']){
		$ordering = explode(' ',$filters['ordering']);
		$sort_by = 'Id';
		$sort_order = 'DESC';
		if(isset($ordering[1])){
			list($sort_by,$sort_order) = explode(' ',$filters['ordering']);
		}
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
    $q = $this->db->get('tf_destinations de', $limit, $offset);
	// print_r($this->db); die;
    if(isset($filters['return_query']) && $filters['return_query']){
      return $q;
    }
    if(isset($filters['return_row']) && $filters['return_row']){
      return $this->map($q->row());
    } 
    return array_map([$this,'map'], $q->result());
  }
  function map($result) {
	  if($result){
		$result->id = $result->Id;
		// $result->code = $result->Code;
	  }
	return $result;
  }
  function getTotal($filters = array()) {
    $this->db->select('COUNT(de.id) as total');
    $this->applyFilters($filters);
    $q = $this->db->get('tf_destinations de');
	// print_r($this->db); die;
    $result = $q->result();
    $num = $q->num_rows();
    if ($num > 0) {
      return $result[0]->total;
    }
    return 0;
  }
  function save($data) {
    if(isset($data['id']) && $data['id']){
      $this->db->where('Id', $data['id']);
	  if(isset($data['_name_en'])){
		  $data['_name_en'] = trim($data['_name_en']);
		  if(empty($data['_name_en']) || is_numeric($data['_name_en'])){
			  $data['_name_en'] = null;
		  }
	  }
	  if(isset($data['_name_ro'])){
		  $data['_name_ro'] = trim($data['_name_ro']);
		  if(empty($data['_name_ro']) || is_numeric($data['_name_ro'])){
			  $data['_name_ro'] = null;
		  }
	  }
	  if(isset($data['status'])){
		  $data['status'] = (int)$data['status'];
	  }
	  $d = array_intersect_key($data, array_flip(['status', '_name_en', '_name_ro']));
	  if(!empty($d)){
		$this->db->update('tf_destinations', $d);
	  }
      $destination_id = $data['id'];
    } else {
		// Block Adding
		return;
    }
	
	$this->logAction(__FUNCTION__ . ':AFTER', $destination_id,  null, $data);
	$this->clearCachedDestinations();
    return $destination_id;
  }
  function deleteById($id) {
	$this->logAction(__FUNCTION__ . ':BEFORE', $id);
	
    $this->db->where('id', $id);
    $this->db->set('status', -2);
    $this->db->update('tf_destinations');
	$this->clearCachedDestinations();
  }
  function trashById($id) {
	$this->logAction(__FUNCTION__ . ':BEFORE', $id);
	  
    $this->db->where('id', $id);
    $this->db->set('status', -1);
    $this->db->update('tf_destinations');
	$this->clearCachedDestinations();
  }
  function publishById($id) {
	$this->logAction(__FUNCTION__ . ':BEFORE', $id);
	  
    $this->db->where('id', $id);
    $this->db->set('status', 1);
    $this->db->update('tf_destinations');
	$this->clearCachedDestinations();
  }
  function logAction($message, $id=0, $code='', $data=null) {
	  // BLOCK LOGGING
	return false;
  }
  function unpublishById($id) {
	$this->logAction(__FUNCTION__ . ':BEFORE', $id);
	
    $this->db->where('id', $id);
    $this->db->set('status', 0);
    $this->db->update('tf_destinations');
	$this->clearCachedDestinations();
  }
}