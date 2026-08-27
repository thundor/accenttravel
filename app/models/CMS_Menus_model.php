<?php

class CMS_Menus_model extends CI_Model {
  function getMenuById($id, $filters=array()) {
    $filters['menu_id'] = $id;
    return $this->getMenu($filters);
  }
  function getMenuBySlug($slug, $filters=array()) {
    $filters['slug'] = $slug;
    return $this->getMenu($filters);
  }
  function getMenuByRoute($route, $filters=array()) {
    $filters['route'] = $route;
    return $this->getMenu($filters);
  }
  function getMenu($filters=array()) {
    $menus = $this->getMenus($filters);
    if($menus){
      return $menus[0];
    }
    return false;
  }
  function applyFilters($filters = array()) {
    if(isset($filters['status'])){
      $this->db->where_in('status', (array)$filters['status']);
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
    
    if(isset($filters['menu_id'])){
      $ids = (array)$filters['menu_id'];
      if(!empty($ids)){
        $this->db->where_in('p.menu_id', $ids);
      }
    }
    if(isset($filters['except_id'])){
      $except_ids = (array)$filters['except_id'];
      if(!empty($except_ids)){
        $this->db->where_not_in('p.menu_id', $except_ids);
      }
    }
    if(isset($filters['join_content']) && $filters['join_content']){
      $this->db->join('ac_cms_menus_content pc', 'p.menu_id = pc.menu_id');
    }
  }
  function getMenus($filters = array()) {
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

    $q = $this->db->get('ac_cms_menus p', $limit, $offset);
    if(isset($filters['return_query']) && $filters['return_query']){
      return $q;
    }
    if(isset($filters['return_row']) && $filters['return_row']){
      return $q->row();
    } elseif(isset($filters['return_rows']) && $filters['return_rows']){
      return $q->result();
    } else {
      $this->load->library('CmsMenu');
      if(isset($filters['return_result']) && $filters['return_result']){
        return $q->row('CmsMenu');
      }
      return $q->result('CmsMenu');
    }
  }
  function getTotalMenus($filters = array()) {
    $this->db->select('COUNT(p.menu_id) as total');
    $this->applyFilters($filters);
    $q = $this->db->get('ac_cms_menus p');
    $result = $q->result();
    $num = $q->num_rows();
    if ($num > 0) {
      return $result[0]->total;
    }
    return 0;
  }
  function saveMenu($data) {
    $languages = $data['languages'];
    unset($data['languages']);
    if(isset($data['menu_id']) && $data['menu_id']){
      $this->db->where('menu_id', $data['menu_id']);
      $this->db->update('ac_cms_menus', $data);
      $menu_id = $data['menu_id'];
    } else {
      $this->db->insert('ac_cms_menus', $data);
      $menu_id = $this->db->insert_id();
    }
    foreach($languages as $language_key=>$language_data){
      $language_data['language'] = $language_key;
      $language_data['menu_id'] = $menu_id;
      $sql = $this->db->insert_string('ac_cms_menus_content', $language_data) . ' ON DUPLICATE KEY UPDATE ';
      $first_added = false;
      foreach($language_data as $key=>$value){
        if(in_array($key,array('menu_id','language'))){
          continue;
        }
        $sql .= $first_added ? ', ' : '';
        $first_added = true;
        $sql .= '`' . $key . '` = VALUES(`' . $key . '`)';
      }
      $this->db->query($sql);
      $content_id = $this->db->insert_id();
    }
    return $menu_id;
  }
  function getMenuLanguages($id) {
    $this->db->where('menu_id', $id);
    $q = $this->db->get('ac_cms_menus_content');
    $result = $q->result();
    $languages = array();
    foreach($result as $item){
      $languages[$item->language] = $item;
    }
    return $languages;
  }
  function getMenuLanguage($id, $language) {
    $this->db->where('menu_id', $id);
    $this->db->order_by('language=\'' . $language . '\'','DESC');
    $q = $this->db->get('ac_cms_menus_content');
    return $q->row();
  }
  function deleteMenuById($id) {
    $this->db->where('menu_id', $id);
    $this->db->delete('ac_cms_menus');
    $this->db->where('menu_id', $id);
    $this->db->delete('ac_cms_menus_content');
  }
}