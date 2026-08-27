<?php

class CMS_Layouts_model extends CI_Model {
  public $ommit_layout_names = array('deleted');
  function getLayoutObj($slug, $folder) {
    if(in_array($slug, $this->ommit_layout_names)){
      return false;
    }
    $layout = $this->getLayout($slug, $folder);
    if(!$layout){
      return false;
    }
    $this->load->library('CmsLayout');
    $object = new CmsLayout();
    foreach($layout as $k=>$v){
      $object->$k = $v;
    }
    return $object;
  }
  function getLayout($slug, $folder) {
    if(in_array($slug, $this->ommit_layout_names)){
      return false;
    }
    $layout_path = $folder . $slug . '/';
    $version_file = $layout_path . 'info.json';
    $layout_default = array(
      'version' => '1.0.0',
      'name' => $slug,
      'author' => '-',
    );
    $layout = array();
    if(file_exists($version_file)){
      $layout = json_decode(file_get_contents($version_file), true);
      if(!$layout){
        $layout = array();
      }
    }
    $layout_info = array_replace($layout_default, $layout);
    $layout_info['slug'] = $slug;
    return $layout_info;
  }
  function getLayouts($filters = array()) {
    $folder = $filters['folder'];
    $just_total = isset($filters['just_total']) && $filters['just_total'];
    $just_total_filtered = isset($filters['just_total_filtered']) && $filters['just_total_filtered'];
    if(!$folder){
      return array();
    }
    $limit = isset($filters['limit']) ? (int)$filters['limit'] : 0;
    if($limit < 0) $limit = 1;
    $page = isset($filters['page']) ? (int)$filters['page'] : 1;
    if($page < 1) $page = 1;
    $offset = 0;
    if($limit && $page>1){
      $offset = ($page-1) * $limit;
    }
    if(!$just_total){
      $layouts = array();
    }
    $search = false;
    if(isset($filters['search']) && trim($filters['search']) !== ''){
      $search = $filters['search'];
    }
    $ordering = false;
    $sort_by = false;
    $sort_order = false;
    if(isset($filters['ordering']) && $filters['ordering']){
      list($sort_by,$sort_order) = explode(' ',$filters['ordering']);
      if(!in_array($sort_by,array(
        'slug',
        'name',
        'version',
        'author',
      ))){
        $sort_by = false;
      }
      if($sort_by){
        $sort_by = strtolower($sort_by);
      }
      $sort_order = strtolower($sort_order);
      if(!in_array($sort_order,array(
        'asc',
        'desc'
      ))){
        $sort_order = false;
      }
      if($sort_order && $sort_by){
        $ordering = true;
      }
    }
    
    $total_layouts = 0;
    foreach (new DirectoryIterator($folder) as $file_info) {
      if($file_info->isDot()) continue;
      if($file_info->isFile()) continue;
      $slug = $file_info->getBasename();
      if($slug == 'deleted'){
        continue;
      }
      if(!$just_total){
        $layout_path = $file_info->getPathName();
        $layout_info = $this->getLayout($slug,dirname($layout_path) . '/');
        if($search){
          $search_in = strtolower($slug . ' ' . $layout_info['name'] . ' ' . $layout_info['version'] . ' ' . $layout_info['author']);
          if(strpos($search_in, strtolower($search)) === false){
            continue;
          }
        }
      }
      $total_layouts++;
      if(!$just_total){
        $layouts[] = $layout_info;
      }
    }
    if($just_total){
      unset($filters,$layouts);
      return $total_layouts;
    }
    
    if($ordering && $layouts && !$just_total_filtered){
      $this->array_sort_by_column($layouts, $sort_by, $sort_order == 'asc' ? SORT_ASC : SORT_DESC);
    }
    if(!$offset && (!$limit || ($limit && $limit>=$total_layouts))){
      if($just_total_filtered){
        unset($filters,$layouts);
        return $total_layouts;
      }
      return $this->layoutsArrayToClass($layouts);
    }
    
    if(!$just_total_filtered){
      $return_layouts = array();
    }
    $show_total = 0;
    foreach($layouts as $layout){
      if($offset){
        $offset--;
        continue;
      }
      $return_layouts[] = $layout;
      $show_total++;
      if($limit && ($show_total == $limit)){
        break; 
      }
    }
    if($just_total_filtered){
      unset($filters,$layouts,$return_layouts);
      return $show_total;
    }
    unset($filters,$layouts);
    return $this->layoutsArrayToClass($return_layouts);
  }
  function layoutsArrayToClass($array = array()) {
    $this->load->library('CmsLayout');
    $objects = array();
    foreach($array as $ak=>$item){
      $object = new CmsLayout();
      foreach($item as $k=>$v){
        $object->$k = $v;
      }
      $objects[] = $object;
    }
    return $objects;
  }
  function getTotalLayouts($filters = array()) {
    $layouts = $this->getLayouts($filters);
    return count($layouts);
  }
  function saveLayout($data) {
    $slug = isset($data['slug']) ? trim($data['slug']) : '';
    if($slug === ''){
      return false;
    }
    if(in_array($slug, $this->ommit_layout_names)){
      return false;
    }
    $newslug = isset($data['newslug']) ? trim($data['newslug']) : '';
    $changed_slug = $newslug !== '';
    if(in_array($newslug, $this->ommit_layout_names)){
      return false;
    }
    $path = isset($data['path']) ? trim($data['path']) : '';
    if($path === ''){
      return false;
    }
    $new = isset($data['new']) && $data['new'] ? true : false;
    if($new){
      mkdir($path . $slug, 0775);
    }
    if($changed_slug){
      rename ($path . $slug, $path . $newslug);
      $slug = $newslug;
    }
    $json_data = array(
      'name' => $data['name'],
      'author' => $data['author'],
      'version' => $data['version'],
    );
    file_put_contents($path . $slug . '/info.json', json_encode($json_data, JSON_PRETTY_PRINT));
    return $slug;
  }
  function deleteLayoutBySlug($slug, $folder) {
    if($slug == 'default'){
      return false;
    }
    if(in_array($slug, $this->ommit_layout_names)){
      return false;
    }
    $new_name = $slug . '_' . date("Y_m_d_H_i_s");
    rename ($folder . $slug, $folder . 'deleted/'.$new_name);
  }
  function array_sort_by_column(&$arr, $col, $dir = SORT_ASC) {
    $sort_col = array();
    foreach ($arr as $key=> $row) {
      $sort_col[$key] = $row[$col];
    }

    array_multisort($sort_col, $dir, $arr);
  }
}