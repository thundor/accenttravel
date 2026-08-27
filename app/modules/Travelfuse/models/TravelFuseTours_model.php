<?php
require_once __DIR__ . '/TravelFuseHotels_model.php';
class TravelFuseTours_model extends TravelFuseHotels_model {
	function __construct() {
		parent::__construct();
		$this->load->model('Travelfuse/TravelFuseFacilities_model');
	}
  function getById($id, $filters=array()) {
    $filters['id'] = $id;
    return $this->get($filters);
  }
  function getByCode($code, $filters=array()) {
    $filters['code'] = $code;
    return $this->get($filters);
  }
  function get($filters=array()) {
    $tours = $this->getList($filters);
    if($tours){
      return $tours[0];
    }
    return false;
  }
  function applyFilters($filters = array()) {
	  $this->db->join('tf_countries cn', "(cn.Id = t.CountryId)", 'LEFT', FALSE);
	  $this->db->join('ac_country acn', "(cn.Code = acn.iso_2)", 'LEFT', FALSE);
    if(isset($filters['status'])){
      $this->db->where_in('t.status', (array)$filters['status']);
    }
    if(isset($filters['search']) && $filters['search'] !== ''){
      $search = $filters['search'];
      $this->db->group_start();
      $this->db->or_like(array(
        't._name_ro' => $search, 
        't._name_en' => $search, 
        't.Name' => $search, 
        // 't.Code' => $search, 
      ));
      $this->db->group_end();
    }
	if(isset($filters['country']) && $filters['country'] !== ''){
      $search = $filters['country'];
      $this->db->group_start();
      $this->db->or_like(array(
        'cn._name_ro' => $search, 
        'cn._name_en' => $search, 
        'acn.name' => $search, 
        'acn.name_RO' => $search, 
        'cn.Name' => $search, 
        'cn.Code' => $search, 
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
        $this->db->where_in('t.Id', $ids);
      }
    }
    if(isset($filters['except_id'])){
      $except_ids = (array)$filters['except_id'];
      if(!empty($except_ids)){
        $this->db->where_not_in('t.Id', $except_ids);
      }
    }
  }
  function getTravelfuseOverrides($ids, $options = []) {
	  $ids = (array)$ids;
	  
	  $this->db->select("t.Id
		, COALESCE(t._name_ro, t._name_en, t.Name, '') as Name
		, COALESCE(t._stars, t.Stars, 0) as Stars
		, COALESCE(t._short_content_ro, t._short_content_en, t.ShortContent, '') as ShortContent
		" . ( empty($options['no_content']) ? ", COALESCE(t._content_ro, t._content_en, t.Content, '') as Content" : "" ) . "
		, Facilities
		, _facilities
		, (SELECT JSON_ARRAYAGG(Name) FROM `tf_facilities` f WHERE (f.regex <> '' AND t.ShortContent REGEXP f.regex)) _determined_facilities
		, MainImage
		" . ( empty($options['first_image_only']) ? ", ImageGallery" : ", IF(t.ImageGallery IS NULL, (NULL), JSON_ARRAY(JSON_EXTRACT(t.ImageGallery, '$[0]'))) AS ImageGallery" ) . "
		, _images
		, status
		");
	  $this->db->where_in('t.Id', $ids);
	  $q = $this->db->get('tf_tours t');
	  // dd($this->db);
	  return array_reduce($q->result(), function($carry, $item){
		  $carry[$item->Id] = $this->mapTravelfuse($item);
		  return $carry;
	  }, []);
  }
  function getList($filters = array()) {
    if(isset($filters['select']) && $filters['select']){
      $this->db->select($filters['select']);
    } else {
		$this->db->select("t.*, COALESCE(t._name_ro, t._name_en, t.Name) as namefinal,COALESCE(cn._name_ro, acn.name_RO, cn._name_en, acn.name, cn.Name) as country, (SELECT JSON_ARRAYAGG(Name) FROM `tf_facilities` f WHERE (f.regex <> '' AND t.ShortContent REGEXP f.regex)) _determined_facilities");
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
    $q = $this->db->get('tf_tours t', $limit, $offset);
	// print_r($this->db); die;
    if(isset($filters['return_query']) && $filters['return_query']){
      return $q;
    }
    if(isset($filters['return_row']) && $filters['return_row']){
      return $this->map($q->row());
    } 
    return array_map([$this,'map'], $q->result());
  }
  function mapTravelfuse($result) {
	  if($result){
		  $result = $this->map($result);
		  unset($result->id);
		  $cached_facilities = $this->TravelFuseFacilities_model->getCachedFacilities();
		  $goodfacilities = [];
		  foreach($result->goodfacilities as $facility){
			  $goodfacilities[strtolower($facility)] = $facility;
		  }
		  $result->Facilities = array_values(array_intersect_key(array_filter($cached_facilities['tour'] ?? []), $goodfacilities));
		  
		  $result->Stars = (int)$result->Stars;
		  unset($result->facilities);
		  unset($result->goodfacilities);
		  unset($result->_determined_facilities);
		  unset($result->_facilities);
		  unset($result->_images);
		  unset($result->cities);
		  unset($result->images);
		  
		  $result->MainImage = null;
		  $result->ImageGallery = null;

		  $tf_images = array_map(function($image){
			  return ['ExternalUrl' => $image];
		  },$result->goodimages);
		  unset($result->goodimages);
		  if($tf_images){
			  $result->MainImage = array_shift($tf_images);
			  $result->ImageGallery = ['Items' => $tf_images];
		  }
	  }
	  return $result;
  }
  
  function getTotal($filters = array()) {
    $this->db->select('COUNT(t.id) as total');
    $this->applyFilters($filters);
    $q = $this->db->get('tf_tours t');
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
	  if(isset($data['_short_content_en'])){
		  $data['_short_content_en'] = trim($data['_short_content_en']);
		  if(empty($data['_short_content_en']) || is_numeric($data['_short_content_en'])){
			  $data['_short_content_en'] = null;
		  }
	  }
	  if(isset($data['_short_content_ro'])){
		  $data['_short_content_ro'] = trim($data['_short_content_ro']);
		  if(empty($data['_short_content_ro']) || is_numeric($data['_short_content_ro'])){
			  $data['_short_content_ro'] = null;
		  }
	  }
	  if(isset($data['_content_en'])){
		  $data['_content_en'] = trim($data['_content_en']);
		  if(empty($data['_content_en']) || is_numeric($data['_content_en'])){
			  $data['_content_en'] = null;
		  }
	  }
	  if(isset($data['_content_ro'])){
		  $data['_content_ro'] = trim($data['_content_ro']);
		  if(empty($data['_content_ro']) || is_numeric($data['_content_ro'])){
			  $data['_content_ro'] = null;
		  }
	  }
	  if(isset($data['_web_address'])){
		  $data['_web_address'] = trim($data['_web_address']);
		  if(empty($data['_web_address']) || is_numeric($data['_web_address'])){
			  $data['_web_address'] = null;
		  }
	  }
	  if(isset($data['_stars'])){
		  $data['_stars'] = trim($data['_stars']);
		  if(empty($data['_stars']) || !('' . $data['_stars'] === '' . (int)$data['_stars'])){
			  $data['_stars'] = null;
		  }
	  }
	  if(isset($data['_latitude'])){
		  $data['_latitude'] = trim($data['_latitude']);
		  if(empty($data['_latitude']) || !('' . $data['_latitude'] === '' . (float)$data['_latitude'])){
			  $data['_latitude'] = null;
		  }
	  }
	  if(isset($data['_longitude'])){
		  $data['_longitude'] = trim($data['_longitude']);
		  if(empty($data['_longitude']) || !('' . $data['_longitude'] === '' . (float)$data['_longitude'])){
			  $data['_longitude'] = null;
		  }
	  }
	  if(isset($data['status'])){
		  $data['status'] = (int)$data['status'];
	  }
	  if(isset($data['_images'])){
		  if(!is_array($data['_images']) || empty($data['_images'])){
			  $data['_images'] = null;
		  } else {
			  $data['_images'] = json_encode($data['_images'], JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES);
		  }
	  }
	  if(isset($data['_facilities'])){
		  if(!is_array($data['_facilities']) || empty($data['_facilities'])){
			  $data['_facilities'] = null;
		  } else {
			  $data['_facilities'] = json_encode($data['_facilities'], JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES);
		  }
	  }
	  $d = array_intersect_key($data, array_flip(['status', '_name_en', '_name_ro', '_short_content_en', '_short_content_ro', '_content_en', '_content_ro', '_web_address', '_stars', '_latitude', '_longitude', '_images', '_facilities']));
	  if(!empty($d)){
		$this->db->update('tf_tours', $d);
	  }
      $tour_id = $data['id'];
    } else {
		// Block Adding
		return;
    }
	
	$this->logAction(__FUNCTION__ . ':AFTER', $tour_id,  null, $data);
	  
    return $tour_id;
  }
  function deleteById($id) {
	$this->logAction(__FUNCTION__ . ':BEFORE', $id);
	
    $this->db->where('id', $id);
    $this->db->set('status', -2);
    $this->db->update('tf_tours');
  }
  function trashById($id) {
	$this->logAction(__FUNCTION__ . ':BEFORE', $id);
	  
    $this->db->where('id', $id);
    $this->db->set('status', -1);
    $this->db->update('tf_tours');
  }
  function publishById($id) {
	$this->logAction(__FUNCTION__ . ':BEFORE', $id);
	  
    $this->db->where('id', $id);
    $this->db->set('status', 1);
    $this->db->update('tf_tours');
  }
  function logAction($message, $id=0, $code='', $data=null) {
	  // BLOCK LOGGING
	return false;
  }
  function unpublishById($id) {
	$this->logAction(__FUNCTION__ . ':BEFORE', $id);
	
    $this->db->where('id', $id);
    $this->db->set('status', 0);
    $this->db->update('tf_tours');
  }
}