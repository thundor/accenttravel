<?php

class TripOrderCoupon_model extends CI_Model {
  function getOrderCouponById($id, $filters=array()) {
    $filters['id'] = $id;
    return $this->getCoupon($filters);
  }
  function getOrderCouponsByCode($code, $filters=array()) {
    $filters['code'] = $code;
    return $this->getOrderCoupons($filters);
  }
  function getOrderCouponsByOrderId($order_id, $filters=array()) {
    $filters['order_id'] = $order_id;
    return $this->getOrderCoupons($filters);
  }
  function getOrderCouponsByCouponId($coupon_id, $filters=array()) {
    $filters['coupon_id'] = $coupon_id;
    return $this->getOrderCoupons($filters);
  }
  function getOrderCoupon($filters=array()) {
    $order_coupons = $this->getOrderCoupons($filters);
    if($order_coupons){
      return array_shift($order_coupons);
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
        'oc.coupon_code' => $search, 
      ));
      $this->db->group_end();
    }
    if(isset($filters['code'])){
      $codes = (array)$filters['code'];
      if(!empty($codes)){
        $this->db->where_in('`oc`.`coupon_code`', $codes);
      }
    }
    if(isset($filters['order_id'])){
      $order_ids = (array)$filters['order_id'];
      if(!empty($order_ids)){
        $this->db->where_in('oc.order_id', $order_ids);
      }
    }
    if(isset($filters['coupon_id'])){
      $coupon_ids = (array)$filters['coupon_id'];
      if(!empty($coupon_ids)){
        $this->db->where_in('oc.coupon_id', $coupon_ids);
      }
    }
    if(isset($filters['type'])){
      $types = (array)$filters['type'];
      if(!empty($types)){
        $this->db->where_in('oc.type', $types);
      }
    }
    if(isset($filters['discount_type'])){
      $discount_types = (array)$filters['discount_type'];
      if(!empty($discount_types)){
        $this->db->where_in('oc.discount_type', $discount_types);
      }
    }
    if(isset($filters['id'])){
      $ids = (array)$filters['id'];
      if(!empty($ids)){
        $this->db->where_in('oc.id', $ids);
      }
    }
    if(isset($filters['except_id'])){
      $except_ids = (array)$filters['except_id'];
      if(!empty($except_ids)){
        $this->db->where_not_in('oc.id', $except_ids);
      }
    }
  }
  function getOrderCoupons($filters = array()) {
    if(isset($filters['select']) && $filters['select']){
      $this->db->select($filters['select']);
    } else {
      $this->db->select('oc.*');
	  if(!empty($filters['join_order'])){
		  $this->db->select('o.provider, o.type, o.trip_order_id');
	  }
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
	if(!empty($filters['join_order'])){
		$this->db->join('ac_trip_order o', "o.id = oc.order_id", 'INNER', FALSE);
	}
    $q = $this->db->get('trip_order_coupon oc', $limit, $offset);
    if(isset($filters['return_query']) && $filters['return_query']){
      return $q;
    }
    if(isset($filters['return_row']) && $filters['return_row']){
      return $q->row();
    }
// if(!$q)	{
	// echo '<pre>';
	// print_r($this->db);
	// die;
// }
    return $q->result();
  }
  function getTotalOrderCoupons($filters = array()) {
    $this->db->select('COUNT(id) as total');
    $this->applyFilters($filters);
    $q = $this->db->get('trip_order_coupon oc');
    $result = $q->result();
    $num = $q->num_rows();
    if ($num > 0) {
      return $result[0]->total;
    }
    return 0;
  }
  function saveOrderCoupon($data) {
    if(isset($data['id']) && $data['id']){
      $this->db->where('id', $data['id']);
      $this->db->update('trip_order_coupon', $data);
      $subscriber_id = $data['id'];
    } else {
      $this->db->insert('trip_order_coupon', $data);
      $subscriber_id = $this->db->insert_id();
    }
    return $subscriber_id;
  }
  function deleteOrderCouponById($id) {
    $this->db->where('id', $id);
    $this->db->delete('trip_order_coupon');
  }
  function deleteOrderCouponByOrderId($id) {
    $this->db->where('order_id', $id);
    $this->db->delete('trip_order_coupon');
  }
  function deleteOrderCouponByCouponId($id) {
    $this->db->where('coupon_id', $id);
    $this->db->delete('trip_order_coupon');
  }
}