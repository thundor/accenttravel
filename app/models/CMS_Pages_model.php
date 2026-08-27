<?php

class CMS_Pages_model extends CI_Model {
  function getPageById($id, $filters=array()) {
    $filters['page_id'] = $id;
    return $this->getPage($filters);
  }
  function getPageBySlug($slug, $filters=array()) {
    $filters['slug'] = $slug;
    return $this->getPage($filters);
  }
  function getPageByRoute($route, $filters=array()) {
    $filters['route'] = $route;
    return $this->getPage($filters);
  }
  function getPage($filters=array()) {
    $pages = $this->getPages($filters);
    if($pages){
      return $pages[0];
    }
    return false;
  }
  function applyFilters($filters = array()) {
    if(isset($filters['status'])){
      $this->db->where_in('status', (array)$filters['status']);
    }
    if(isset($filters['blog'])){
      $this->db->where_in('blog', (array)$filters['blog']);
    }
    if(isset($filters['type'])){
      switch($filters['type']){
        case 'static':
          $this->db->where('`route` IS NULL');
          $this->db->where('`params` IS NULL');
          break;
        case 'dynamic':
          $this->db->where('`route` IS NOT NULL');
          $this->db->where('`params` IS NOT NULL');
          break;
        case 'default':
          $this->db->where('`route` IS NOT NULL');
          $this->db->where('`params` IS NULL');
          break;
      }
    }
    if(isset($filters['search']) && $filters['search'] !== ''){
      $filters['join_content'] = true;
      $search = $filters['search'];
      $this->db->group_start();
      $this->db->or_like(array(
        'title' => $search, 
        'slug' => $search, 
        'keywords' => $search, 
        'description' => $search,
      ));
      $this->db->group_end();
    }
    if(isset($filters['slug'])){
      $filters['join_content'] = true;
      $slugs = (array)$filters['slug'];
      if(!empty($slugs)){
        $this->db->where_in('slug', $slugs);
      }
    }
    if(isset($filters['route'])){
      $filters['join_content'] = true;
      $routes = (array)$filters['route'];
      if(!empty($routes)){
        $this->db->where_in('route', $routes);
      }
    }
    
    if(isset($filters['created_by'])){
      $created_by = (array)$filters['created_by'];
      if(!empty($created_by)){
        $this->db->where_in('created_by', $created_by);
      }
    }
    
    if(isset($filters['page_id'])){
      $ids = (array)$filters['page_id'];
      if(!empty($ids)){
        $this->db->where_in('p.page_id', $ids);
      }
    }
    if(isset($filters['except_id'])){
      $except_ids = (array)$filters['except_id'];
      if(!empty($except_ids)){
        $this->db->where_not_in('p.page_id', $except_ids);
      }
    }
    if(isset($filters['join_content']) && $filters['join_content']){
      $this->db->join('ac_cms_pages_content pc', 'p.page_id = pc.page_id');
	  if(isset($filters['lang'])){
		  $lang = (array)$filters['lang'];
		  if(!empty($lang)){
			$this->db->where_in('pc.language', $lang);
		  }
		}
    }
  }
  function getPages($filters = array()) {
    if(isset($filters['select']) && $filters['select']){
      $this->db->select($filters['select']);
    } else {
      $this->db->select('*');
    }
    
    $this->applyFilters($filters);
    
    if(isset($filters['ordering']) && $filters['ordering']){
      list($sort_by,$sort_order) = explode(' ',$filters['ordering']);
		if($sort_by == 'sort_order'){
			$filters['ordering'] = '`sort_order` ' . $sort_order . ',pc.`title` ' . $sort_order . ',p.`page_id` ' . $sort_order . '';
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

    $q = $this->db->get('ac_cms_pages p', $limit, $offset);
    if(isset($filters['return_query']) && $filters['return_query']){
      return $q;
    }
    if(isset($filters['return_row']) && $filters['return_row']){
      return $this->map($q->row());
    } elseif(isset($filters['return_rows']) && $filters['return_rows']){
      return array_map([$this,'map'], $q->result());
    } else {
      $this->load->library('CmsPage');
      if(isset($filters['return_result']) && $filters['return_result']){
        return $this->map($q->row('CmsPage'));
      }
      return array_map([$this,'map'], $q->result('CmsPage'));
    }
  }
  function map($result) {
	  if($result){
		  if(!empty($result->images)){
			  $result->images = json_decode($result->images, true);
		  }
		  if(empty($result->images)) {
			  $result->images = [];
		  }
	  }
	  return $result;
  }
  function getTotalPages($filters = array()) {
    $this->db->select('COUNT(p.page_id) as total');
    $this->applyFilters($filters);
    $q = $this->db->get('ac_cms_pages p');
    $result = $q->result();
    $num = $q->num_rows();
    if ($num > 0) {
      return $result[0]->total;
    }
    return 0;
  }
  function savePage($data) {
	  if(isset($data['images'])){
		  if(!is_array($data['images']) || empty($data['images'])){
			  $data['images'] = null;
		  } else {
			  $data['images'] = json_encode($data['images'], JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES);
		  }
	  }
    $languages = $data['languages'];
    unset($data['languages']);
    if(isset($data['page_id']) && $data['page_id']){
      $this->db->where('page_id', $data['page_id']);
      $this->db->update('ac_cms_pages', $data);
      $page_id = $data['page_id'];
    } else {
      $this->db->insert('ac_cms_pages', $data);
      $page_id = $this->db->insert_id();
    }
    foreach($languages as $language_key=>$language_data){
      $language_data['language'] = $language_key;
      $language_data['page_id'] = $page_id;
      $sql = $this->db->insert_string('ac_cms_pages_content', $language_data) . ' ON DUPLICATE KEY UPDATE ';
      $first_added = false;
      foreach($language_data as $key=>$value){
        if(in_array($key,array('page_id','language'))){
          continue;
        }
        $sql .= $first_added ? ', ' : '';
        $first_added = true;
        $sql .= '`' . $key . '` = VALUES(`' . $key . '`)';
      }
      $this->db->query($sql);
      $content_id = $this->db->insert_id();
    }
    return $page_id;
  }
  function getPageLanguages($id) {
    $this->db->where('page_id', $id);
    $q = $this->db->get('ac_cms_pages_content');
    $result = $q->result();
    $languages = array();
    foreach($result as $item){
      $languages[$item->language] = $item;
    }
    return $languages;
  }
  function getPageLanguage($id, $language) {
    $this->db->where('page_id', $id);
    $this->db->order_by('language=\'' . $language . '\'','DESC');
    $q = $this->db->get('ac_cms_pages_content');
    return $q->row();
  }
  function deletePageById($id) {
    $this->db->where('page_id', $id);
    $this->db->delete('ac_cms_pages');
    $this->db->where('page_id', $id);
    $this->db->delete('ac_cms_pages_content');
  }
}