<?php

class Hotel_Activities_Model extends CI_Model {
  public $api;
  function __construct() {
    parent::__construct();
    $this->load->model('Trip_model');
    $this->api = $this->Trip_model->get_api();
  }
  /* function sync() {
    $this->dbo = $this->Trip_model->get_dbo();
    $this->dbo->select('Id, Name, CategoryId');
    $q = $this->dbo->get('activities');
    $activities = $q->result();
    // toDO
  } */
  function getActivitiesByCategory($category_id, $filters=array()) {
    $filters['category_id'] = array($category_id);
    return $this->getActivities($filters);
  }
  function getActivitiesByCategories($category_ids, $filters=array()) {
    $filters['category_id'] = $category_ids;
    return $this->getActivities($filters);
  }
  function getActivityById($activity_id, $filters=array()) {
    $filters['id'] = array($activity_id);
    return $this->getActivities($filters);
  }
  function getActivitiesById($activity_ids, $filters=array()) {
    $filters['id'] = $activity_ids;
    return $this->getActivities($filters);
  }
  function getActivities($filters = array()) {
    extract($filters);
    $this->db->select('tha.id, tha.name, tha.category_id, tha.icon');
    if(isset($category_id)){
      $this->db->where_in('tha.category_id', $category_id);
    }
    if(isset($id)){
      $this->db->where_in('tha.id', $id);
    }
    $q = $this->db->get('trip_hotel_activities tha');
    if($q->num_rows()){
      return $q->result();
    }
    return array();
  }
  function getCategoriesByActivities($activity_ids=array(),$filters = array()) {
    $filters['activity_id'] = $activity_ids;
    return $this->getCategories($filters);
  }
  function getCategoriesWithActivities($activity_ids=null) {
    $this->db->select("thac.id, thac.name, thac.icon");
    $this->db->select("GROUP_CONCAT(tha.id SEPARATOR ',') AS activity_ids");
    if(isset($activity_ids)){
      $this->db->where_in('tha.id', $activity_ids);
    }
    $this->db->join('trip_hotel_activities tha', 'tha.category_id = thac.id');
    $this->db->group_by('thac.id');
    $q = $this->db->get('trip_hotel_activity_categories thac');
    if($q->num_rows()){
      return $q->result();
    }
    return array();
  }
  function getCategory($category_id,$filters = array()) {
    $filters['id'] = $category_id;
    return $this->getCategories($filters);
  }
  function getCategories($filters = array()) {
    extract($filters);
    $this->db->select('thac.*');
    if(isset($id)){
      $this->db->where_in('thac.id', $id);
    }
    $q = $this->db->get('trip_hotel_activity_categories thac');
    if($q->num_rows()){
      return $q->result();
    }
    return array();
  }
}