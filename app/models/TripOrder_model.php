<?php

class TripOrder_model extends CI_Model {
  function getOrderById($id, $filters=array()) {
    $filters['id'] = $id;
    return $this->getOrder($filters);
  }
  function getOrdersByTripId($id, $filters=array()) {
    $filters['trip_order_id'] = $id;
    return $this->getOrders($filters);
  }
  function getOrder($filters=array()) {
    $countries = $this->getOrders($filters);
    if($countries){
      return $countries[0];
    }
    return false;
  }
  function applyFilters($filters = array()) {
    if(isset($filters['status'])){
      $this->db->where_in('status', (array)$filters['status']);
    }
    if(isset($filters['search']) && $filters['search'] !== ''){
      $search = $filters['search'];
      $this->db->like('CONCAT_WS(" ",`id`, `trip_order_id`, `user_lastname`, `user_firstname`, `user_email`, `time_created`)',$search);
      /* $this->db->group_start();
      $this->db->or_like(array(
          'name' => $search, 
          'name_RO' => $search,
      ));
      $this->db->group_end(); */
    }
    if(isset($filters['status'])){
      $status = (array)$filters['status'];
      if(!empty($status)){
        $this->db->where_in('status', $status);
      }
    }
    if(isset($filters['id'])){
      $ids = (array)$filters['id'];
      if(!empty($ids)){
        $this->db->where_in('id', $ids);
      }
    }
    if(isset($filters['coupon_code'])){
      $coupon_codes = (array)$filters['coupon_code'];
      if(!empty($coupon_codes)){
        $this->db->where_in('coupon_code', $coupon_codes);
      }
    }
    if(isset($filters['created_by'])){
      $created_bys = (array)$filters['created_by'];
      if(!empty($created_bys)){
        $this->db->where_in('created_by', $created_bys);
      }
    }
    if(isset($filters['trip_order_id'])){
      $trip_order_ids = (array)$filters['trip_order_id'];
      if(!empty($trip_order_ids)){
        $this->db->where_in('trip_order_id', $trip_order_ids);
      }
    }
    if(isset($filters['payment_gateway'])){
      $payment_gateway = (array)$filters['payment_gateway'];
      if(!empty($payment_gateway)){
        $this->db->where_in('payment_gateway', $payment_gateway);
      }
    }
  }
  function getOrders($filters = array()) {
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

    $q = $this->db->get('ac_trip_order', $limit, $offset);
    if(isset($filters['return_query']) && $filters['return_query']){
      return $q;
    }
    if(isset($filters['return_row']) && $filters['return_row']){
      return $q->row();
    } elseif(isset($filters['return_rows']) && $filters['return_rows']){
      return $q->result();
    } else {
      $this->load->library('TripOrder');
      if(isset($filters['return_result']) && $filters['return_result']){
        return $q->row('TripOrder');
      }
      return $q->result('TripOrder');
    }
  }
  function getTotalOrders($filters = array()) {
    $this->db->select('COUNT(id) as total');
    $this->applyFilters($filters);
    $q = $this->db->get('ac_trip_order');
    $result = $q->result();
    $num = $q->num_rows();
    if ($num > 0) {
      return $result[0]->total;
    }
    return 0;
  }
  function saveOrder($data) {
    if(isset($data['id']) && $data['id']){
      $this->db->where('id', $data['id']);
      $this->db->update('ac_trip_order', $data);
      return $data['id'];
    } else {
      $ip = null;
      if (!empty($_SERVER['HTTP_CLIENT_IP'])) {
        $ip = $_SERVER['HTTP_CLIENT_IP'];
      } elseif (!empty($_SERVER['HTTP_X_FORWARDED_FOR'])) {
        $ip = $_SERVER['HTTP_X_FORWARDED_FOR'];
      } elseif (!empty($_SERVER['REMOTE_ADDR'])) {
        $ip = $_SERVER['REMOTE_ADDR'];
      }
      $data['ip'] = $ip;
      $this->db->insert('ac_trip_order', $data);
      return $this->db->insert_id();
    }
  }
  function deleteOrderById($id, $filters = array()) {
    $filters['id'] = $id;
    $this->deleteOrder($filters);
  }
  function deleteOrder($filters = array()) {
    
    $orders = $this->getOrders($filters);
    $filters = array();
    $filters['id'] = array();
    foreach($orders as $order){
      if(!$order->trip_order_id){
        $filters['id'][] = $order->id;
      }
    }
    if($filters['id']){
      $this->applyFilters($filters);
      $this->db->delete('ac_trip_order');
    }
  }
  function createTripOrder() {
    $this->load->model('Trip_model');
    return $this->Trip_model->get_api()->apiCall('index.php/orders', array(), array(), true, true);
  }
  function createTripOrderFull($user_data, $services) {
    $post_data = $this->getTripClientFromOrderData($user_data);
    $post_data['services'] = $services;
    $this->load->model('Trip_model');
    return $this->Trip_model->get_api()->loginApiCall('index.php/orders', array(), $post_data, true, true);
  }
  function getTripServices($order_id) {
    $this->load->model('Trip_model');
    return $this->Trip_model->get_api()->loginApiCall('index.php/orders/' . (int)$order_id . '/services');
  }
  function setTripPaymentMethod($order_id, $payment_method) {
    $this->load->model('Trip_model');
    $post_data = array(
      'payment' => array(
        'method' => '' . $payment_method,
      ),
    );
    $get_data = array(
      'method' => 'setPaymentMethods'
    );
    return $this->Trip_model->get_api()->loginApiCall('index.php/orders/' . (int)$order_id, $get_data, $post_data, true, 'PATCH');
  }
  function setTripPaymentStatus($order_id, $payment_status, $payment_status_message = '') {
    if(!strlen($payment_status_message)){
      if($payment_status == 0){
        $payment_status_message = 'Draft';
      } elseif($payment_status == 1){
        $payment_status_message = 'Complet';
      } elseif($payment_status == 2){
        $payment_status_message = 'Anulat';
      } 
    }
    $this->load->model('Trip_model');
    $post_data = array(
      'payment' => array(
        'Status' => $payment_status,
        'StatusMessage' => '' . $payment_status_message,
      ),
    );
    $get_data = array();
	
	$SecretKey = $this->Trip_model->get_api()->getSecretKey();
	
	$get_data['requestSecret'] = sha1(json_encode($post_data) . $SecretKey);
    return $this->Trip_model->get_api()->loginApiCall('index.php/orders/' . (int)$order_id, $get_data, $post_data, true, 'PATCH');
  }
  function getTripPaymentMethods($order_id) {
    $this->load->model('Trip_model');
    return $this->Trip_model->get_api()->loginApiCall('index.php/orders/' . (int)$order_id,array(
      'method' => 'getPaymentMethods'
    ));
  }
  function bookAllTripServices($order_id) {
    $this->load->model('Trip_model');
    $get_data = array(
      'method' => 'book'
    );
    return $this->Trip_model->get_api()->loginApiCall('index.php/orders/' . (int)$order_id, $get_data);
  }
  function bookTripService($order_id, $service_id) {
    $this->load->model('Trip_model');
    return $this->Trip_model->get_api()->loginApiCall('index.php/orders/' . (int)$order_id . '/services/' . (int)$service_id . '/book');
  }
  function getOrderInvoice($order_id) {
    $this->load->model('Trip_model');
    return $this->Trip_model->get_api()->loginApiCall('index.php/tina/invoice/' . $order_id);
  }
  function getOrderDocument($order_id, $doc_id) {
    $this->load->model('Trip_model');
    return $this->Trip_model->get_api()->loginApiCall('index.php/tina/invoice/' . $order_id . '?documents=' . $doc_id, [], [], false);
  }
  function getTripService($order_id, $service_id) {
    $this->load->model('Trip_model');
    return $this->Trip_model->get_api()->loginApiCall('index.php/orders/' . (int)$order_id . '/services/' . (int)$service_id);
  }
  // see all documents on a order service
  function getDocuments($order_id, $service_id) {
    $this->load->model('Trip_model');
    return $this->Trip_model->get_api()->loginApiCall('index.php/orders/' . (int)$order_id . '/services/' . (int)$service_id . '/documents');
  }
  // download all documents on a order service as zip
  function downloadDocuments($order_id, $service_id) {
    $this->load->model('Trip_model');
    $get_data = array(
      'action' => 'download'
    );
    return $this->Trip_model->get_api()->loginApiCall('index.php/orders/' . (int)$order_id . '/services/' . (int)$service_id . '/documents', $get_data, array(), false);
  }
  // get document by id
  function getDocument($order_id, $service_id, $document_id) {
    $this->load->model('Trip_model');
    return $this->Trip_model->get_api()->loginApiCall('index.php/orders/' . (int)$order_id . '/services/' . (int)$service_id . '/documents/' . (int)$document_id);
  }
  // download document as pdf
  function downloadDocument($order_id, $service_id, $document_id) {
    $this->load->model('Trip_model');
    $get_data = array(
      'action' => 'download'
    );
    return $this->Trip_model->get_api()->loginApiCall('index.php/orders/' . (int)$order_id . '/services/' . (int)$service_id . '/documents/' . (int)$document_id, $get_data, array(), false);
  }
  function addTripService($order_id, $service_data) {
    $this->load->model('Trip_model');
    return $this->Trip_model->get_api()->loginApiCall('index.php/orders/' . (int)$order_id . '/services', array(), $service_data);
  }
  function removeTripService($order_id, $service_id) {
    $this->load->model('Trip_model');
    return $this->Trip_model->get_api()->loginApiCall('index.php/orders/' . (int)$order_id . '/services/' . (int)$service_id, array(), array(), true, 'DELETE');
  }
  function getTripClientFromOrderData($order_data) {
    $phone_prefix = '';
    $this->load->model('Country_model');
    if(isset($order_data['user_phone']) && strlen($order_data['user_phone']) && isset($order_data['user_phone_prefix']) && strlen($order_data['user_phone_prefix'])){
      $phone_prefix_country = $this->Country_model->getCountries(array(
        'iso_2' => trim($order_data['user_phone_prefix']),
        'select' => 'phone_prefix',
        'return_row' => true,
      ));
      if($phone_prefix_country){
        $phone_prefix = '+' . $phone_prefix_country->phone_prefix . ' ';
      }
    }
    $country = $this->Country_model->getCountries(array(
      'iso_2' => trim($order_data['user_country']),
      'select' => array(
        '`trip_id`',
        '`iso_2`',
        'IFNULL(`name_RO`,`name`) as name',
      ),
      'return_row' => true,
    ));
    $post_data = array(
      'owner' => array(
        'type' => $order_data['user_invoice'] == 'pj' ? 2 : 1,
        'company' =>array(
          'CompanyName' => $order_data['user_invoice'] == 'pj' ? trim($order_data['user_company_name']) : null,
          'VatNumber' => $order_data['user_invoice'] == 'pj' ? trim($order_data['user_cui']) : null,
          'TradeRegistryNumber' => $order_data['user_invoice'] == 'pj' ? trim($order_data['user_regcom']) : null,
          'Bank' => $order_data['user_invoice'] == 'pj' ? trim($order_data['user_bank']) : null,
          'BankAccount' => $order_data['user_invoice'] == 'pj' ? trim($order_data['user_iban']) : null,
        ),
        'firstname' => trim($order_data['user_firstname']),
        'lastname' => trim($order_data['user_lastname']),
        'sin' => '',
        'street' => trim($order_data['user_street']),
        'housenr' => trim($order_data['user_street_no']),
        'zipcode' => trim($order_data['user_postal_code']),
        'province' => trim($order_data['user_address']),
        'city' => trim($order_data['user_city']),
        'country' => trim($order_data['user_country']),
        'title' => trim($order_data['user_title']),
        'email' => trim($order_data['user_email']),
        'phone' => preg_replace('/\s*(\+\d+)(\s+\1){1,}/', '\1', '' . trim($phone_prefix . $order_data['user_phone'])),
      )
    );
    $this->load->model('Trip_model');
    $this->Trip_model->clean($post_data);
    return $post_data;
  }
  function saveTripClient($order_id, $order_data) {
    $post_data = $this->getTripClientFromOrderData($order_data);
    return $this->Trip_model->get_api()->loginApiCall('index.php/orders/' . (int)$order_id, array(), $post_data, true, 'PATCH');
  }
  function getTripOrder($order_id) {
    $this->load->model('Trip_model');
    return $this->Trip_model->get_api()->loginApiCall('index.php/orders/' . (int)$order_id);
  }
  function deleteTripOrder($order_id) {
    // NOT FUNCTIONAL
    $this->load->model('Trip_model');
    return $this->Trip_model->get_api()->loginApiCall('index.php/orders/' . (int)$order_id,array(),array(),true,'DELETE');
  }
}