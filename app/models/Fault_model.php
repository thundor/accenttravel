<?php

class Fault_model extends CI_Model {
  function getFaultById($id, $filters=array()) {
    $filters['id'] = $id;
    return $this->getFault($filters);
  }
  function getFaultByIp($ip = '', $filters=array()) {
	  if(!$ip){
		  $ip = $this->getIp();
	  }
    $filters['ip'] = $ip;
    $fault = $this->getFault($filters);
	
	if($fault){
		if(-1 == $fault->status) return false;
		$fault->end_status = (bool)$fault->status;
		if(!$fault->end_status){
			$fault->end_status = $fault->faillogin >= 3;
		}
		if(!$fault->end_status){
			$fault->end_status = $fault->page404 >= 3;
		}
		if(!$fault->end_status){
			$fault->end_status = $fault->forbidden >= 3;
		}
	}
	return $fault;
  }
  function getFault($filters=array()) {
    $accounts = $this->getFaults($filters);
    if($accounts){
      return $accounts[0];
    }
    return false;
  }
  function applyFilters($filters = array()) {
    if(isset($filters['status'])){
      $this->db->where_in('status', (array)$filters['status']);
    }
    if(isset($filters['ip'])){
      $ips = (array)$filters['ip'];
      if(!empty($ips)){
        $this->db->where_in('ip', $ips);
      }
    }
    if(isset($filters['id'])){
      $ids = (array)$filters['id'];
      if(!empty($ids)){
        $this->db->where_in('fault_id', $ids);
      }
    }
  }
  function getFaults($filters = array()) {
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

    $q = $this->db->get('ac_fault', $limit, $offset);
    if(isset($filters['return_query']) && $filters['return_query']){
      return $q;
    }
    if(isset($filters['return_row']) && $filters['return_row']){
      return $q->row();
    } elseif(isset($filters['return_rows']) && $filters['return_rows']){
      return $q->result();
    } else {
      $this->load->library('User');
      if(isset($filters['return_result']) && $filters['return_result']){
        return $q->row('User');
      }
      return $q->result('User');
    }
  }
  function getTotalFaults($filters = array()) {
    $this->db->select('COUNT(fault_id) as total');
    $this->applyFilters($filters);
    $q = $this->db->get('ac_fault');
    $result = $q->result();
    $num = $q->num_rows();
    if ($num > 0) {
      return $result[0]->total;
    }
    return 0;
  }
  function getIp() {
	$ip = '';
	if (!empty($_SERVER['HTTP_CLIENT_IP'])) {
		$ip = $_SERVER['HTTP_CLIENT_IP'];
	} elseif (!empty($_SERVER['HTTP_X_FORWARDED_FOR'])) {
		$ip = $_SERVER['HTTP_X_FORWARDED_FOR'];
	} elseif (!empty($_SERVER['REMOTE_ADDR'])) {
		$ip = $_SERVER['REMOTE_ADDR'];
	}
	return $ip;
  }
  function saveFault($data) {
    if(isset($data['fault_id']) && $data['fault_id']){
      $this->db->where('fault_id', $data['fault_id']);
      $this->db->update('ac_fault', $data);
      $fault_id = $data['fault_id'];
    } else {
      $data['fault_id'] = null;
	  
      $this->db->insert('ac_fault', $data);
      $fault_id = $this->db->insert_id();
    }
	
    return $fault_id;
  }
  function insertFault($data = []) {
	unset($data['fault_id']);
	unset($data['date_added']);
	unset($data['date_modified']);
	
    $data['ip'] = $this->getIp();
	$updates = [];
	foreach(['page404', 'faillogin', 'forbidden'] as $k){
		if(!empty($data[$k])){
			$updates[] = "`" . $k . "` = `" . $k . "` + 1";
		} elseif(isset($data[$k])){
			$updates[] = "`" . $k . "` = VALUES(`" . $k . "`)";
		}
	}
	$url = 'http' . (isset($_SERVER['HTTPS']) ? 's' : '') . '://' . ($_SERVER['HTTP_HOST'] ?? '') . ($_SERVER['REQUEST_URI'] ?? '');
	$data['url'] = $url;
	$url = 'http' . (isset($_SERVER['HTTPS']) ? 's' : '') . '://' . ($_SERVER['HTTP_HOST'] ?? '') . ($_SERVER['REQUEST_URI'] ?? '');
	$data['referer'] = $_SERVER['HTTP_REFERER'] ?? '';
	foreach(['status', 'url', 'referer'] as $k){
		if(!empty($data[$k])){
			$updates[] = "`" . $k . "` = VALUES(`" . $k . "`)";
		}
	}
	if(empty($updates)){
		return;
	}
	$sql = $this->db->insert_string('ac_fault', $data) . " ON DUPLICATE KEY UPDATE " . implode(',', $updates);
	// echo $sql; die;
	$this->db->query($sql);
  }
  function deleteFaultById($user_id, $filters = array()) {
    $filters['id'] = $user_id;
    $this->deleteFault($filters);
  }
  function deleteFault($filters = array()) {
    $this->applyFilters($filters);
    $this->db->delete('ac_fault');
  }
}