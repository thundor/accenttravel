<?php

class TravelFuseHotels_model extends CI_Model {
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
    $hotels = $this->getList($filters);
    if($hotels){
      return $hotels[0];
    }
    return false;
  }
  function applyFilters($filters = array()) {
	  $this->db->join('tf_countries cn', "(cn.Id = h.CountryId)", 'LEFT', FALSE);
	  $this->db->join('ac_country acn', "(cn.Code = acn.iso_2)", 'LEFT', FALSE);
    if(isset($filters['status'])){
      $this->db->where_in('h.status', (array)$filters['status']);
    }
    if(isset($filters['search']) && $filters['search'] !== ''){
      $search = $filters['search'];
      $this->db->group_start();
      $this->db->or_like(array(
        'h._name_ro' => $search, 
        'h._name_en' => $search, 
        'h.Name' => $search, 
        // 'h.Code' => $search, 
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
        $this->db->where_in('h.Id', $ids);
      }
    }
    if(isset($filters['except_id'])){
      $except_ids = (array)$filters['except_id'];
      if(!empty($except_ids)){
        $this->db->where_not_in('h.Id', $except_ids);
      }
    }
  }
  function getTravelfuseOverrides($ids, $options = []) {
	  $ids = (array)$ids;
	  
	  $this->db->select("h.Id
		, COALESCE(h._name_ro, h._name_en, h.Name, '') as Name
		, COALESCE(h._stars, h.Stars, 0) as Stars
		, COALESCE(h._short_content_ro, h._short_content_en, h.ShortContent, '') as ShortContent
		" . ( empty($options['no_content']) ? ", COALESCE(h._content_ro, h._content_en, h.Content, '') as Content" : "" ) . "
		, Facilities
		, _facilities
		, (SELECT JSON_ARRAYAGG(Name) FROM `tf_facilities` f WHERE (f.regex <> '' AND h.ShortContent REGEXP f.regex)) _determined_facilities
		, MainImage
		" . ( empty($options['first_image_only']) ? ", ImageGallery" : ", IF(h.ImageGallery IS NULL, (NULL), JSON_ARRAY(JSON_EXTRACT(h.ImageGallery, '$[0]'))) AS ImageGallery" ) . "
		, _images
		, status
		");
	  $this->db->where_in('h.Id', $ids);
	  $q = $this->db->get('tf_hotels h');
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
		$this->db->select("h.*, COALESCE(h._name_ro, h._name_en, h.Name) as namefinal,COALESCE(cn._name_ro, acn.name_RO, cn._name_en, acn.name, cn.Name) as country, (SELECT JSON_ARRAYAGG(JSON_OBJECT('Id', ci.Id, 'type', ci.type, 'namefinal', COALESCE(ci._name_ro, ci._name_en, ci.Name))) FROM `tf_city_hotels` ch JOIN `tf_cities` ci ON (ch.CityCode = ci.Id AND ch.DestinationType = ci.type) WHERE ch.Id = h.Id) as cities, (SELECT JSON_ARRAYAGG(Name) FROM `tf_facilities` f WHERE (f.regex <> '' AND h.ShortContent REGEXP f.regex)) _determined_facilities");
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
    $q = $this->db->get('tf_hotels h', $limit, $offset);
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
		  $result->Facilities = array_values(array_intersect_key(array_filter($cached_facilities['hotel'] ?? []), $goodfacilities));
		  
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
  function map($result) {
	  if($result){
		$result->id = $result->Id;
		$result->ImageGallery = $result->ImageGallery ? json_decode($result->ImageGallery, true) : [];
		$result->Facilities = $result->Facilities ? json_decode($result->Facilities, true) : [];
		$result->_facilities = $result->_facilities ? json_decode($result->_facilities, true) : [];
		$result->_determined_facilities = $result->_determined_facilities ? json_decode($result->_determined_facilities, true) : [];
		$result->_images = $result->_images ? json_decode($result->_images, true) : [];
		$result->cities = !empty($result->cities) ? json_decode($result->cities, true) : [];
		$result->facilities = $result->_facilities;
		
		// $determined_facilities = $this->db->query("SELECT 
			// JSON_ARRAY_AGG(Name)
			// FROM `tf_facilities` f
			// WHERE (f.regex <> '' AND " . $this->db->escape($result->ShortContent) . " REGEXP f.regex)
		// ")->result('array');
		
		foreach($result->facilities as $facility => $facility_detail){
			if(empty($facility_detail['custom'])){
				$result->facilities[$facility] = ['type' => 'missing'];
				$result->facilities[$facility]['missing'] = 1;
			}
		}
		
		foreach($result->Facilities as $facility){
			if(!isset($result->facilities[$facility])){
				$result->facilities[$facility] = ['type' => 'original'];
			} else {
				unset($result->facilities[$facility]['missing']);
				$result->facilities[$facility]['type'] = 'original';
			}
		}
		
		foreach($result->_determined_facilities as $facility){
			if(!isset($result->facilities[$facility])){
				$result->facilities[$facility] = ['type' => 'determined'];
			} else {
				unset($result->facilities[$facility]['missing']);
				$result->facilities[$facility]['type'] = 'determined';
			}
		}
		
		$result->goodfacilities = array_keys(array_filter($result->facilities, function($detail){
			return empty($detail['hide']) && empty($detail['missing']);
		}));
		// echo '<pre>';
		// print_r($result->facilities);
		// die;
		$result->images = !empty($result->_images) ? $result->_images : [];
		foreach($result->images as $image => $image_detail){
			if(empty($image_detail['custom'])){
				$result->images[$image]['missing'] = 1;
			} elseif(!is_file(BASEPATH) . 'resources/images/hoteluri/tf/' . $image){
				$result->images[$image]['missing'] = 1;
			}
		}
		if(!empty($result->MainImage)){
			if(!isset($result->images[$result->MainImage])){
				$result->images[$result->MainImage] = [];
			} else {
				unset($result->images[$result->MainImage]['missing']);
			}
		}
		if(!empty($result->ImageGallery)){
			foreach($result->ImageGallery as $image){
				if(!empty($image)){
					if(!isset($result->images[$image])){
						$result->images[$image] = [];
					} else {
						unset($result->images[$image]['missing']);
					}
				}
			}
		}
		
		$result->goodimages = array_keys(array_filter($result->images, function($detail){
			return empty($detail['hide']) && empty($detail['missing']);
		}));
	}
	return $result;
  }
  function getTotal($filters = array()) {
    $this->db->select('COUNT(h.id) as total');
    $this->applyFilters($filters);
    $q = $this->db->get('tf_hotels h');
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
		$this->db->update('tf_hotels', $d);
	  }
      $hotel_id = $data['id'];
    } else {
		// Block Adding
		return;
    }
	
	$this->logAction(__FUNCTION__ . ':AFTER', $hotel_id,  null, $data);
	  
    return $hotel_id;
  }
  function deleteById($id) {
	$this->logAction(__FUNCTION__ . ':BEFORE', $id);
	
    $this->db->where('id', $id);
    $this->db->set('status', -2);
    $this->db->update('tf_hotels');
  }
  function trashById($id) {
	$this->logAction(__FUNCTION__ . ':BEFORE', $id);
	  
    $this->db->where('id', $id);
    $this->db->set('status', -1);
    $this->db->update('tf_hotels');
  }
  function publishById($id) {
	$this->logAction(__FUNCTION__ . ':BEFORE', $id);
	  
    $this->db->where('id', $id);
    $this->db->set('status', 1);
    $this->db->update('tf_hotels');
  }
  function logAction($message, $id=0, $code='', $data=null) {
	  // BLOCK LOGGING
	return false;
  }
  function unpublishById($id) {
	$this->logAction(__FUNCTION__ . ':BEFORE', $id);
	
    $this->db->where('id', $id);
    $this->db->set('status', 0);
    $this->db->update('tf_hotels');
  }
}