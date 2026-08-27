<?php
// ini_set('display_errors', 1);
ini_set('memory_limit', '512M');
$this->load->model('Trip/Hotels_model');
$index_ids = (array)$this->input->post('Id', null);
$index_ids = array_filter($index_ids);
$rs = [];
foreach($index_ids as $index_id){
	
	$cache_key = 'newux/trip/hotel/inspect/' . md5(json_encode($index_id));
	$response = $this->getCache($cache_key);
	if(!isset($response)){
		$container_id = 'hotel-' . session_id();
		$inspections = $this->Hotels_model->inspectSearch($container_id);
		$inspection = array_filter($inspections ? $inspections : [], function($inspection) use ($index_id) {
			return $inspection->Id == $index_id;
		});
		$inspection = array_shift($inspection);
		
		$r = $this->Hotels_model->api->apiCall('index.php/en/dynamic-package/sid/' . $index_id);
		$r->status = 2;
		
		if($r && !empty($r->code) && !empty($r->life)){
			$r->status = 1;
			$r->expiry = time() + $r->life;
			$this->setCache($cache_key, json_encode($r));
		} elseif($inspection && (($inspection->Status ?? '') != 2)) {
			$r->status = 0;
		}
		if(!$inspection || empty($inspection->Status)){
			$r->status = 0;
			// echo 'Search failed';
			// return;
		}
		$r->inspection = $inspection;
	} else {
		// echo 'from cache';
		// die;
		$r = json_decode($response);
		$r->status = 1;
	}
	$rs[$index_id] = $r;
}
// $container_id = session_id();
// $rs[] = $this->Hotels_model->api->apiCall('index.php/en/dynamic-package/inspect/' . $container_id);
echo json_encode($rs);