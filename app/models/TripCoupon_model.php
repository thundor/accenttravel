<?php

class TripCoupon_model extends CI_Model {
  function getCouponById($id, $filters=array()) {
    $filters['id'] = $id;
    return $this->getCoupon($filters);
  }
  function getCouponByCode($code, $filters=array()) {
    $filters['code'] = $code;
    return $this->getCoupon($filters);
  }
  function getCoupon($filters=array()) {
    $coupons = $this->getCoupons($filters);
    if($coupons){
      return $coupons[0];
    }
    return false;
  }
  /**
	* Generate a License Key.
	*
	* @param   string  $suffix Append this to generated Key.
	* @return  string
	*/
	function generateLicense() {
		$num_segments = 4;
		$segment_chars = 4;
		// Default tokens contain no "ambiguous" characters: 1,i,0,o
		$tokens = 'ABCDEFGHJKLMNPQRSTUVWXYZ23456789';
		$license_string = '';
		$separator = '';
		// Build Default License String
		for ($i = 0; $i < $num_segments; $i++) {
			$segment = '';
			for ($j = 0; $j < $segment_chars; $j++) {
				$segment .= $tokens[rand(0, strlen($tokens)-1)];
			}
			$license_string .= $segment;
			if ($i < ($num_segments - 1)) {
				$license_string .= $separator;
			}
		}
		return $license_string;
	}
  function applyFilters($filters = array()) {
    if(isset($filters['status'])){
      $this->db->where_in('c.status', (array)$filters['status']);
    }
    if(isset($filters['parent_status'])){
      $this->db->where_in('pc.status', (array)$filters['parent_status']);
    }
    if(isset($filters['search']) && $filters['search'] !== ''){
      $search = $filters['search'];
      $this->db->group_start();
      $this->db->or_like(array(
        'c.code' => $search, 
        'c.name' => $search, 
        'c.ean' => $search, 
        'c.pan' => $search, 
      ));
	  if(is_numeric($search) && empty($filters['join_child'])){
		$this->db->or_where("EXISTS(SELECT 1 FROM trip_coupon pc1 WHERE pc1.parent_id = c.id and pc1.status>-2 AND pc1.pan LIKE '" . $search . "')");
	  }
      $this->db->group_end();
    }
    if(isset($filters['code'])){
      $codes = (array)$filters['code'];
      if(!empty($codes)){
        $this->db->where_in('c.code', $codes);
      }
    }
    if(isset($filters['ean'])){
      $eans = (array)$filters['ean'];
      if(!empty($eans)){
		  if(!empty($filters['join_child'])){
			$this->db->where_in('pc.ean', $eans);
		  } else {
			$this->db->where_in('c.ean', $eans);
		  }
      }
    }
    if(isset($filters['pan'])){
      $pans = (array)$filters['pan'];
      if(!empty($pans)){
        $this->db->where_in('c.pan', $pans);
      }
    }
    if(isset($filters['type'])){
      $types = (array)$filters['type'];
      if(!empty($types)){
        $this->db->where_in('c.type', $types);
      }
    }
    if(isset($filters['discount_type'])){
      $discount_types = (array)$filters['discount_type'];
      if(!empty($discount_types)){
        $this->db->where_in('c.discount_type', $discount_types);
      }
    }
    if(isset($filters['parent_id'])){
      $parent_ids = (array)$filters['parent_id'];
      if(!empty($parent_ids)){
        $this->db->where_in('c.parent_id', $parent_ids);
      }
    }
    if(isset($filters['id'])){
      $ids = (array)$filters['id'];
      if(!empty($ids)){
        $this->db->where_in('c.id', $ids);
      }
    }
    if(isset($filters['except_id'])){
      $except_ids = (array)$filters['except_id'];
      if(!empty($except_ids)){
        $this->db->where_not_in('c.id', $except_ids);
      }
    }
    if(isset($filters['active'])){
		if(!empty($filters['join_child'])){
			if(!empty($filters['active'])){
				$this->db->where("((pc.max_uses IS NULL OR c.nr_uses < pc.max_uses) AND (pc.date_start IS NULL OR pc.date_start <= '" . date('Y-m-d') . "') AND (pc.date_expire IS NULL OR pc.date_expire >= '" . date('Y-m-d') . "'))");
			} else {
				$this->db->where("NOT ((pc.max_uses IS NULL OR c.nr_uses < pc.max_uses) AND (pc.date_start IS NULL OR pc.date_start <= '" . date('Y-m-d') . "') AND (pc.date_expire IS NULL OR pc.date_expire >= '" . date('Y-m-d') . "'))");
			}
		} else {
			if(!empty($filters['active'])){
				$this->db->where("((c.max_uses IS NULL OR c.nr_uses < c.max_uses) AND (c.date_start IS NULL OR c.date_start <= '" . date('Y-m-d') . "') AND (c.date_expire IS NULL OR c.date_expire >= '" . date('Y-m-d') . "'))");
			} else {
				$this->db->where("NOT ((c.max_uses IS NULL OR c.nr_uses < c.max_uses) AND (c.date_start IS NULL OR c.date_start <= '" . date('Y-m-d') . "') AND (c.date_expire IS NULL OR c.date_expire >= '" . date('Y-m-d') . "'))");
			}
		}
    }
  }
  function getCoupons($filters = array()) {
    if(isset($filters['select']) && $filters['select']){
      $this->db->select($filters['select']);
    } else {
		if(!empty($filters['join_child'])){
			$this->db->select('pc.*');
			$this->db->select('c.id');
			$this->db->select('c.status');
			$this->db->select('c.pan');
			$this->db->select('pc.status as parent_status');
			$this->db->select('c.parent_id');
			$this->db->select('c.code');
			$this->db->select('c.type');
			$this->db->select('c.nr_uses');
			$this->db->select('c.time_created');
			$this->db->select('c.time_modified');
			$this->db->select('c.real_updated');
		} else {
		  $this->db->select('*');
		  $this->db->select("IF(c.type <> 'group',c.nr_uses,(SELECT SUM(nr_uses) FROM trip_coupon c2 WHERE c2.parent_id=c.id)) AS nr_uses", false);
		  $this->db->select("IF(c.type <> 'group',1,(SELECT COUNT(*) FROM trip_coupon c2 WHERE c2.parent_id=c.id AND c.status=1)) AS total_coupons", false);
		}
    }
    
    $this->applyFilters($filters);
    
    if(isset($filters['ordering']) && $filters['ordering']){
      $this->db->order_by($filters['ordering']);
    }
    
    $page = isset($filters['page']) && (int)$filters['page'] > 1 ? (int)$filters['page']: 1;
    $limit = isset($filters['limit']) && (int)$filters['limit'] > 0 ? (int)$filters['limit']: null;
    $offset = 0;
    if($limit > 0){
      $offset = ($page - 1) * $limit;
    }
	if(!empty($filters['join_child'])){
		$this->db->join('trip_coupon pc', "IF(c.type='child',c.parent_id,c.id) = pc.id", 'INNER', FALSE);
	}
    $q = $this->db->get('trip_coupon c', $limit, $offset);
    if(isset($filters['return_query']) && $filters['return_query']){
      return $q;
    }
    if(isset($filters['return_row']) && $filters['return_row']){
      return $q->row();
    } 
    return $q->result();
  }
  function getTotalCoupons($filters = array()) {
    $this->db->select('COUNT(c.id) as total');
    $this->applyFilters($filters);
    $q = $this->db->get('trip_coupon c');
    $result = $q->result();
    $num = $q->num_rows();
    if ($num > 0) {
      return $result[0]->total;
    }
    return 0;
  }
  function saveCoupon($data) {
    if(isset($data['id']) && $data['id']){
      $this->db->where('id', $data['id']);
      $this->db->update('trip_coupon', $data);
      $coupon_id = $data['id'];
    } else {
      $this->db->insert('trip_coupon', $data);
      $coupon_id = $this->db->insert_id();
	  
		$up_data = array();
		if($data['type'] == 'child'){
			$up_data = array(
				'pan' => $this->generatePAN($coupon_id),
			);
		} elseif($data['type'] == 'group'){
			$up_data = array(
				'ean' => $this->generateEAN($coupon_id),
			);
		}
		if($up_data){
			$this->db->where('id', $coupon_id);
			$this->db->update('trip_coupon', $up_data);
		}
    }
	
	$this->logAction(__FUNCTION__ . ':AFTER', $coupon_id,  null, $data);
	  
    return $coupon_id;
  }
  function getValidCouponHotel($coupon_code) {
    return getValidCoupon($coupon_code, 'hotel');
  }
  function getValidCouponPackage($coupon_code) {
    return getValidCoupon($coupon_code, 'package');
  }
  function orderCouponsByAppliance_sorter($a, $b) {
	if($a['discount_type'] == 'P'){
		if($b['discount_type'] != 'P'){
			// fixed first
			return 1;
		}
	} else {
		if($b['discount_type'] == 'P'){
			// fixed first
			return -1;
		}
	}
	return 0;
  }
  function orderCouponsByAppliance($session_coupons = array()) {
	usort($session_coupons, array($this,"orderCouponsByAppliance_sorter"));
	return array_values($session_coupons);
  }
  function getValidCouponTest($coupon_code, $type='') {
    $this->db->select('pc.*');
    $this->db->select('c.code');
    $this->db->select('c.type');
    $this->db->select('c.nr_uses');
    $this->db->select('c.fsli');
    $this->db->where('pc.status', 1);
    $this->db->where('c.status', 1);
    $this->db->where('c.code', $coupon_code);
    $this->db->where_not_in('c.type', array('group'));
    // $this->db->where("(pc.max_uses IS NULL OR c.nr_uses < pc.max_uses)");
    // $this->db->where("(pc.date_start IS NULL OR pc.date_start <= '" . date('Y-m-d') . "')");
    // $this->db->where("(pc.date_expire IS NULL OR pc.date_expire >= '" . date('Y-m-d') . "')");
    if($type){
      if(in_array($type, array('hotel', 'package', 'flight', 'citybreak', 'paralela45_strainatate', 'paralela45_circuit', 'travelfuse_circuit', 'travelfuse_charter'))){
        $this->db->where("pc.$type = 1");
      } else {
        return false;
      }
    }
	$this->db->join('trip_coupon pc', "IF(c.type='child',c.parent_id,c.id) = pc.id", 'INNER', FALSE);
    $q = $this->db->get('trip_coupon c', 1, 0);
    if(!$q->num_rows()){
      return false;
    }
    return $q->row();
  }
  function getValidCoupon($coupon_code, $type='') {
    $this->db->select('pc.*');
    $this->db->select('c.code');
    $this->db->select('c.type');
    $this->db->select('c.nr_uses');
    $this->db->select('c.fsli');
    $this->db->where('pc.status', 1);
    $this->db->where('c.status', 1);
    $this->db->where('c.code', $coupon_code);
    $this->db->where_not_in('c.type', array('group'));
    $this->db->where("(pc.max_uses IS NULL OR c.nr_uses < pc.max_uses)");
    $this->db->where("(pc.date_start IS NULL OR pc.date_start <= '" . date('Y-m-d') . "')");
    $this->db->where("(pc.date_expire IS NULL OR pc.date_expire >= '" . date('Y-m-d') . "')");
    if($type){
      if(in_array($type, array('hotel', 'package', 'flight', 'citybreak', 'paralela45_strainatate', 'paralela45_circuit', 'travelfuse_circuit', 'travelfuse_charter', 'epay'))){
        $this->db->where("pc.$type = 1");
      } else {
        return false;
      }
    }
	$this->db->join('trip_coupon pc', "IF(c.type='child',c.parent_id,c.id) = pc.id", 'INNER', FALSE);
    $q = $this->db->get('trip_coupon c', 1, 0);
    if(!$q->num_rows()){
      return false;
    }
    return $q->row();
  }
  function getValidCoupons($session_coupons, $type='', $mode=0) {
	  if(empty($session_coupons) || !is_array($session_coupons)){
		  return array();
	  }
	  if(!$mode){
		  $coupon_codes = array_column($session_coupons, 'code');
	  } else {
		  $coupon_codes = $session_coupons;
	  }
		  
	  $coupons = array();
	  if($coupon_codes){
		$this->db->select('pc.*');
		$this->db->select('c.code');
		$this->db->select('c.type');
		$this->db->select('c.nr_uses');
		$this->db->select('c.fsli');
		$this->db->where('pc.status', 1);
		if(2 == $mode){
			$this->db->where('c.status', 0);
		} else {
			$this->db->where('c.status', 1);
		}
		$this->db->where_in('c.code', (array)$coupon_codes);
		$this->db->where_not_in('c.type', array('group'));
		$this->db->where("(pc.max_uses IS NULL OR c.nr_uses < pc.max_uses)");
		if(1 != $mode){
			$this->db->where("(pc.date_start IS NULL OR pc.date_start <= '" . date('Y-m-d') . "')");
			$this->db->where("(pc.date_expire IS NULL OR pc.date_expire >= '" . date('Y-m-d') . "')");
		}
		if($type){
		  if(in_array($type, array('hotel', 'package', 'flight', 'citybreak', 'paralela45_strainatate', 'paralela45_circuit', 'travelfuse_circuit', 'travelfuse_charter', 'epay'))){
			$this->db->where("pc.$type = 1");
		  } else {
			return array();
		  }
		}
		$this->db->join('trip_coupon pc', "IF(c.type='child',c.parent_id,c.id) = pc.id", 'INNER', FALSE);
		$q = $this->db->get('trip_coupon c');
		if($q->num_rows()){
		  foreach($q->result() as $coupon){
			  $coupons[] = array(
				'id' => $coupon->id,
				'fsli' => $coupon->fsli,
				'code' => $coupon->code,
				'discount' => $coupon->percentage,
				'discount_type' => $coupon->discount_type,
				'amount_ron' => $coupon->fixed_ron,
				'amount_eur' => $coupon->fixed_eur,
			);
		  }
		}
	  }
    return $coupons;
  }
  function useCoupon($coupon_code) {
	$this->logAction(__FUNCTION__ . ':BEFORE', null, $coupon_code);
	  
    $this->db->where('code', $coupon_code);
    $this->db->set('nr_uses', 'nr_uses+1', false);
    $this->db->update('trip_coupon');
	
	$coupon = $this->getCouponByCode($coupon_code);
	if($coupon && $coupon->parent_id){
		$this->db->where('id', $coupon->parent_id);
		$this->db->set('nr_uses', 'nr_uses+1', false);
		$this->db->update('trip_coupon');
	}
  }
  function unUseCoupon($coupon_code) {
	$this->logAction(__FUNCTION__ . ':BEFORE', null, $coupon_code);
	  
    $this->db->where('code', $coupon_code);
    $this->db->set('nr_uses', 'IF(nr_uses-1>=0, nr_uses-1, 0)', false);
    $this->db->update('trip_coupon');
	
	$coupon = $this->getCouponByCode($coupon_code);
	if($coupon && $coupon->parent_id){
		$this->db->where('id', $coupon->parent_id);
		$this->db->set('nr_uses', 'IF(nr_uses-1>=0, nr_uses-1, 0)', false);
		$this->db->update('trip_coupon');
	}
  }
  function deleteCouponById($id) {
	$this->logAction(__FUNCTION__ . ':BEFORE', $id);
	
    $this->db->where('id', $id);
    $this->db->set('status', -2);
    $this->db->update('trip_coupon');
  }
  function trashCouponById($id) {
	$this->logAction(__FUNCTION__ . ':BEFORE', $id);
	  
    $this->db->where('id', $id);
    $this->db->set('status', -1);
    $this->db->update('trip_coupon');
  }
  function publishCouponById($id) {
	$this->logAction(__FUNCTION__ . ':BEFORE', $id);
	  
    $this->db->where('id', $id);
    $this->db->set('status', 1);
    $this->db->update('trip_coupon');
  }
  function logAction($message, $id=0, $code='', $data=null) {
	$args = func_get_args();
	$coupon = null;
	if($id){
		$coupon = $this->getCouponById($id);
	} elseif($code){
		$coupon = $this->getCouponByCode($code);
	}
	
	if($coupon){
		$id = $coupon->id;
		$code = $coupon->code;
	}
	$response_dir_path = APPPATH.'logs/epay_manual/' . date('YmdHis') . '/';
	if(!is_dir($response_dir_path)){
	  mkdir($response_dir_path,0777,true);
	}
	$ip = '';
	if (!empty($_SERVER['HTTP_CLIENT_IP'])) {
		$ip = $_SERVER['HTTP_CLIENT_IP'];
	} elseif (!empty($_SERVER['HTTP_X_FORWARDED_FOR'])) {
		$ip = $_SERVER['HTTP_X_FORWARDED_FOR'];
	} else {
		$ip = $_SERVER['REMOTE_ADDR'];
	}
	file_put_contents($response_dir_path . 'server.json',json_encode($_SERVER, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES));
	file_put_contents($response_dir_path . 'post.json',json_encode($_POST, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES));
	file_put_contents($response_dir_path . 'headers.json',json_encode(getallheaders(), JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES));
	file_put_contents($response_dir_path . 'get.json',json_encode($_GET, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES));
	file_put_contents($response_dir_path . 'coupon.json',json_encode($coupon, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES));
	file_put_contents($response_dir_path . 'args.json',json_encode($args, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES));
	file_put_contents($response_dir_path . 'data.json',json_encode($data, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES));
	file_put_contents($response_dir_path . 'code.txt',$code);
	file_put_contents($response_dir_path . 'ip.txt',$ip);
	file_put_contents($response_dir_path . 'user_id.txt',$this->user->id);
	file_put_contents($response_dir_path . 'id.txt',$id);
	file_put_contents($response_dir_path . 'message.txt',$message);
  }
  function unpublishCouponById($id) {
	$this->logAction(__FUNCTION__ . ':BEFORE', $id);
	
    $this->db->where('id', $id);
    $this->db->set('status', 0);
    $this->db->update('trip_coupon');
  }
	function fromEAN($ean){
		if(strlen($ean) !== 13){
			throw new Exception('Invalid EAN');
		}
		$check = substr($ean, -1);
        $code = substr($ean, 0, -1);
		
		$weightflag = true;
		$sum = 0;
		// Weight for a digit in the checksum is 3, 1, 3.. starting from the last digit. 
		// loop backwards to make the loop length-agnostic. The same basic functionality 
		// will work for codes of different lengths.
		for ($i = strlen($code) - 1; $i >= 0; $i--){
			$sum += (int)$code[$i] * ($weightflag?3:1);
			$weightflag = !$weightflag;
		}
		if($check != (10 - ($sum % 10)) % 10){
			throw new Exception('Invalid EAN');
		}
		$first_char = substr($code,0,1);
		$pos = substr($code,$first_char, 1);
		$length = str_pad(substr($code,$first_char + 1, 1),'1',STR_PAD_LEFT);
		$real_code = strrev(substr($code,0,$first_char) . substr($code,$first_char + 2));
		
		return $this->numhash(substr($real_code,$pos,$length));
	}
	function generateEAN($number)
	{
		$number = $this->numhash($number);
		$number_length = strlen($number);

		$pos = rand(0,10 - $number_length);
		$rest_pos = 10 - $pos - $number_length;
		$code = '';
		for($i = 0; $i < $pos; $i++){
			$code .= rand(0,9);
		}
		$code .= $number;
		for($i = 0; $i < $rest_pos; $i++){
			$code .= rand(0,9);
		}
		$code = strrev($code);
		$first_char = substr($code,0,1);
		$code = substr($code, 0, (int)$first_char) . $pos . ($number_length % 10) . substr($code, (int)$first_char);
		
		$weightflag = true;
		$sum = 0;
		// Weight for a digit in the checksum is 3, 1, 3.. starting from the last digit. 
		// loop backwards to make the loop length-agnostic. The same basic functionality 
		// will work for codes of different lengths.
		for ($i = strlen($code) - 1; $i >= 0; $i--){
			$sum += (int)$code[$i] * ($weightflag?3:1);
			$weightflag = !$weightflag;
		}
		$code .= (10 - ($sum % 10)) % 10;
		return $code;
	}
	function fromPAN($ean){
		if(strlen($ean) !== 10){
			throw new Exception('Invalid EAN');
		}
		$number = strrev(rtrim($ean,0));
		
		return $this->numhash(4294967295 - $number);
	}
	function generatePAN($number)
	{
		$code = $this->numhash(4294967295 - $number);
		$code = str_pad($code,'10','0', STR_PAD_LEFT);
		$code = strrev($code);
		
		return $code;
	}
	function numhash($n) {
		/* MAX n=4294967295;r=0 */
		return (((0x0000FFFF & $n) << 16) + ((0xFFFF0000 & $n) >> 16));
	}
}