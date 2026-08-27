<?php

class TripLog_model extends CI_Model {
	static $save_log_data = [];
  function getLogById($id, $filters=array()) {
    $filters['id'] = $id;
    return $this->getLog($filters);
  }
  function getLog($filters=array()) {
    $logs = $this->getLogs($filters);
    if($logs){
      return $logs[0];
    }
    return false;
  }
  function applyFilters($filters = array()) {
    if(isset($filters['status'])){
      $this->db->where_in('status', (array)$filters['status']);
    }
    if(isset($filters['search']) && $filters['search'] !== ''){
      $search = $filters['search'];
      $this->db->like('CONCAT_WS(" ",`id`, `user_lastname`, `user_firstname`, `user_email`, `time_created`)',$search);
      /* $this->db->group_start();
      $this->db->or_like(array(
          'name' => $search, 
          'name_RO' => $search,
      ));
      $this->db->group_end(); */
    }
    /* if(isset($filters['status'])){
      $status = (array)$filters['status'];
      if(!empty($status)){
        $this->db->where_in('status', $status);
      }
    } */
    if(isset($filters['id'])){
      $ids = (array)$filters['id'];
      if(!empty($ids)){
        $this->db->where_in('id', $ids);
      }
    }
    /* if(isset($filters['trip_order_id'])){
      $trip_order_ids = (array)$filters['trip_order_id'];
      if(!empty($trip_order_ids)){
        $this->db->where_in('trip_order_id', $trip_order_ids);
      }
    } */
  }
  function getLogs($filters = array()) {
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

    $q = $this->db->get('trip_log', $limit, $offset);
    if(isset($filters['return_query']) && $filters['return_query']){
      return $q;
    }
    if(isset($filters['return_row']) && $filters['return_row']){
      return $q->row();
    } elseif(isset($filters['return_rows']) && $filters['return_rows']){
      return $q->result();
    } else {
      $this->load->library('TripLog');
      if(isset($filters['return_result']) && $filters['return_result']){
        return $q->row('TripLog');
      }
      return $q->result('TripLog');
    }
  }
  function getTotalLogs($filters = array()) {
    $this->db->select('COUNT(id) as total');
    $this->applyFilters($filters);
    $q = $this->db->get('trip_log');
    $result = $q->result();
    $num = $q->num_rows();
    if ($num > 0) {
      return $result[0]->total;
    }
    return 0;
  }
  function shutdownSaveLog() {
	  $log_data = static::$save_log_data;
	  $sid = session_id();
	  $ip = null;
      if (!empty($_SERVER['HTTP_CLIENT_IP'])) {
        $ip = $_SERVER['HTTP_CLIENT_IP'];
      } elseif (!empty($_SERVER['HTTP_X_FORWARDED_FOR'])) {
        $ip = $_SERVER['HTTP_X_FORWARDED_FOR'];
      } elseif (!empty($_SERVER['REMOTE_ADDR'])) {
        $ip = $_SERVER['REMOTE_ADDR'];
      }
	  $search_data = array_key_exists('search_data', $log_data) ? $log_data['search_data'] : $this->session->userdata('log/trip/flights/search_data');
	  $search_data_json = json_encode($search_data);
	  
	  $hash = !empty($search_data) ? md5($search_data_json) : null;
	  
	  if(isset($log_data['start_timp_raspuns_cautare'])){
		  $log_data['timp_raspuns_cautare'] = microtime(true) - $log_data['start_timp_raspuns_cautare'];
	  }
	  if(isset($log_data['start_timp_raspuns_rezultate'])){
		  $log_data['timp_raspuns_rezultate'] = microtime(true) - $log_data['start_timp_raspuns_rezultate'];
	  }
	  if(isset($log_data['start_timp_raspuns_item'])){
		  $log_data['timp_raspuns_item'] = microtime(true) - $log_data['start_timp_raspuns_item'];
	  }
	  
	  $data = [
		'app' => !empty($_GET['pay24']) ? 'pay24' : 'site',
		'type' => null,
		'hash' => $hash ?? '',
		'session' => $sid,
		'search_data' => !empty($search_data) ? $search_data_json : null,
		'date' => date('Y-m-d H:i:s'),
		'ip' => $ip,
	  ];
	  
	  // echo '<pre>';
	  // print_r($data);
	  // die;
	  
	  $multi_fields = ['error_message', 'error_call', 'checkout_data', 'flight_data', 'order_data', 'billing_data', 'date_results', 'date_details', 'date_passengers', 'date_billing', 'date_checkout', 'date_summary', 'date_pay', 'timp_raspuns_item'];
	  foreach($multi_fields as $multi_field){
		  if(isset($log_data[$multi_field])){
			  $data[$multi_field] = $log_data[$multi_field];
		  }
	  }
	  
	  $single_fields = ['device','results_count', 'url', 'order_id', 'customer_data', 'timp_raspuns_rezultate', 'timp_raspuns_cautare'];
	  
	  foreach($single_fields as $single_field){
		  if(isset($log_data[$single_field])){
			  $data[$single_field] = $log_data[$single_field];
		  }
	  }
	  
	  $this->db->select('id, ' . implode(',', $single_fields));
	  $this->db->where('session', $sid);
	  if(!isset($hash)){
		  $this->db->where('hash', '');
	  } else {
		  $this->db->group_start();
		  $this->db->or_where("`hash` = ''");
		  $this->db->or_where("`hash` = '" . $hash . "'");
		  $this->db->group_end();
		  $this->db->order_by('`hash`', 'DESC');
	  }
	  
	  $q = $this->db->get('trip_log');
	  
	  // var_dump($this->db);
	  $result = $q->result();
	  
	  if($result){
		$data['id'] = $result[0]->id;
		$this->db->where('id', $data['id']);
		foreach($single_fields as $single_field){
			if(isset($result[0]->$single_field)){
				unset($data[$single_field]);
			}
		}
		if(isset($result[1])){
			if(!isset($data['url'])){
				$data['url'] = $result[1]->url;
			}
			if(!isset($data['device'])){
				$data['device'] = $result[1]->device;
			}
			if(!isset($data['customer_data'])){
				$data['customer_data'] = $result[1]->customer_data;
			}
		}
		
		$this->db->update('trip_log', $data);
		
		if(isset($hash)){
			$this->db->where('session', $sid);
			$this->db->where('hash', '');
			$this->db->delete('trip_log');
		}
		
		// var_dump('updated');
		// var_dump($data);
		// var_dump($this->db);
	  } else {
		  $this->db->insert('trip_log', $data);
		  // var_dump('inserted');
	  }
  }
  function saveLog($data = []) {
	  static $registered_saveLog;
	  if(!$registered_saveLog){
		  register_shutdown_function(array($this,'shutdownSaveLog'));
		  $registered_saveLog = true;
	  }
	  static::$save_log_data = array_replace(static::$save_log_data, $data);
  }
  function deleteLogById($id, $filters = array()) {
    $filters['id'] = $id;
    $this->deleteLog($filters);
  }
  function deleteLog($filters = array()) {
    
    $logs = $this->getLogs($filters);
    $filters = array();
    $filters['id'] = array();
    foreach($logs as $order){
      if(!$order->trip_log_id){
        $filters['id'][] = $order->id;
      }
    }
    if($filters['id']){
      $this->applyFilters($filters);
      $this->db->delete('trip_log');
    }
  }
}